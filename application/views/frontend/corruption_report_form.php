<div class="text-center pages-head">
    <span class="font-pages-head">ช่องทางแจ้งเรื่องร้องเรียนการทุจริตและประพฤติมิชอบ</span>
</div>
</div>
<img src="<?php echo base_url('docs/welcome-btm-light-other.png'); ?>">

<!-- Bootstrap 5 CSS -->
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
<!-- Font Awesome -->
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
<!-- Google Fonts -->
<link href="https://fonts.googleapis.com/css2?family=Sarabun:wght@300;400;500;600;700&display=swap" rel="stylesheet">
<!-- SweetAlert2 -->
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

<style>
    /* File Upload Area Improvements */
    .file-upload-area {
        border: 2px dashed #dc3545;
        border-radius: 15px;
        padding: 2rem;
        text-align: center;
        background: linear-gradient(135deg, #fff5f5 0%, #fee2e2 100%);
        transition: all 0.3s ease;
        cursor: pointer;
        position: relative;
        overflow: hidden;
    }

    .file-upload-area:hover {
        background: linear-gradient(135deg, #fee2e2 0%, #fecaca 100%);
        transform: translateY(-2px);
        box-shadow: 0 8px 25px rgba(220, 53, 69, 0.2);
    }

    .file-upload-area.dragover {
        background: linear-gradient(135deg, #fecaca 0%, #fca5a5 100%);
        border-color: #b02a37;
        transform: scale(1.02);
    }

    /* File Preview Items */
    .preview-item {
        position: relative;
        background: white;
        border-radius: 8px;
        overflow: hidden;
        box-shadow: 0 2px 8px rgba(0, 0, 0, 0.1);
        transition: all 0.3s ease;
        margin-bottom: 1rem;
        border: 1px solid #e9ecef;
    }

    .preview-item:hover {
        transform: translateY(-2px);
        box-shadow: 0 4px 15px rgba(0, 0, 0, 0.15);
        border-color: #dc3545;
    }

    .file-icon {
        display: flex;
        align-items: center;
        justify-content: center;
        width: 50px;
        height: 50px;
        background: linear-gradient(135deg, #fff5f5 0%, #fee2e2 100%);
        border-radius: 8px;
        margin-right: 1rem;
    }

    .file-actions {
        display: flex;
        align-items: center;
        gap: 0.5rem;
    }

    .remove-file {
        border-radius: 50% !important;
        width: 32px;
        height: 32px;
        display: flex;
        align-items: center;
        justify-content: center;
        padding: 0;
    }

    .remove-file:hover {
        background-color: #dc3545 !important;
        border-color: #dc3545 !important;
        color: white !important;
        transform: scale(1.1);
    }

    /* File Summary */
    .file-summary {
        background: linear-gradient(135deg, #f8f9fa 0%, #e9ecef 100%);
        border: 1px solid #dee2e6;
        border-radius: 8px;
        padding: 0.75rem 1rem;
        margin-top: 0.5rem;
    }

    /* File Management Buttons */
    .file-management-buttons {
        display: flex;
        align-items: center;
        gap: 0.5rem;
        padding: 0.5rem 0;
        border-top: 1px solid #e9ecef;
        margin-top: 0.5rem;
    }

    /* Upload Progress */
    .upload-progress {
        margin-top: 0.25rem;
    }

    .upload-progress .progress {
        height: 4px;
        background-color: #e9ecef;
        border-radius: 2px;
        overflow: hidden;
    }

    .upload-progress .progress-bar {
        background: linear-gradient(90deg, #28a745, #20c997);
        transition: width 0.3s ease;
    }

    /* Upload Guidelines */
    .upload-guidelines {
        background: linear-gradient(135deg, #f8f9fa 0%, #e9ecef 100%);
        border: 1px solid #dee2e6;
        border-radius: 10px;
        padding: 1rem;
        margin-top: 1rem;
    }

    .upload-guidelines h6 {
        border-bottom: 1px solid #dee2e6;
        padding-bottom: 0.5rem;
        margin-bottom: 1rem;
    }

    /* Responsive */
    @media (max-width: 768px) {
        .file-upload-area {
            padding: 1.5rem 1rem;
        }

        .preview-item .d-flex {
            flex-direction: column;
            align-items: flex-start !important;
        }

        .file-icon {
            margin-right: 0;
            margin-bottom: 0.5rem;
        }

        .file-actions {
            margin-top: 0.5rem;
            width: 100%;
            justify-content: flex-end;
        }

        .file-management-buttons {
            flex-wrap: wrap;
        }
    }

    /* Animation for file items */
    .preview-item {
        animation: slideInUp 0.3s ease-out;
    }

    @keyframes slideInUp {
        from {
            opacity: 0;
            transform: translateY(20px);
        }

        to {
            opacity: 1;
            transform: translateY(0);
        }
    }

    /* File type specific icons */
    .file-icon .fa-file-image {
        color: #28a745;
    }

    .file-icon .fa-file-pdf {
        color: #dc3545;
    }

    .file-icon .fa-file-word {
        color: #007bff;
    }

    .file-icon .fa-file-excel {
        color: #28a745;
    }

    .file-icon .fa-file-powerpoint {
        color: #fd7e14;
    }

    .file-icon .fa-file-alt {
        color: #6c757d;
    }

    .corruption-container {
        max-width: 1000px;
        margin: 0 auto;
        background: white;
        border-radius: 25px;
        box-shadow: 0 20px 60px rgba(0, 0, 0, 0.15);
        overflow: hidden;
        position: relative;
    }

    .corruption-header {
        background: linear-gradient(135deg, #dc3545 0%, #b02a37 100%);
        color: white;
        padding: 2rem;
        text-align: center;
        position: relative;
        overflow: hidden;
    }

    .corruption-header::before {
        content: '';
        position: absolute;
        top: 0;
        left: 0;
        right: 0;
        bottom: 0;
        background: url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 100 100'%3E%3Ccircle cx='50' cy='50' r='2' fill='rgba(255,255,255,0.1)'/%3E%3C/svg%3E") repeat;
        background-size: 30px 30px;
        animation: float 6s ease-in-out infinite;
    }

    @keyframes float {

        0%,
        100% {
            transform: translateY(0px);
        }

        50% {
            transform: translateY(-10px);
        }
    }

    .corruption-icon {
        width: 80px;
        height: 80px;
        background: rgba(255, 255, 255, 0.2);
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        margin: 0 auto 1rem;
        backdrop-filter: blur(10px);
        border: 2px solid rgba(255, 255, 255, 0.3);
    }

    .corruption-title {
        font-size: 2.2rem;
        font-weight: 700;
        margin-bottom: 0.5rem;
        text-shadow: 2px 2px 4px rgba(0, 0, 0, 0.3);
        position: relative;
        z-index: 2;
    }

    .corruption-subtitle {
        font-size: 1.1rem;
        opacity: 0.9;
        position: relative;
        z-index: 2;
    }

    .corruption-form {
        padding: 2.5rem;
    }

    .warning-notice {
        background: linear-gradient(135deg, #fff3cd 0%, #ffeaa7 100%);
        border: 2px solid #ffc107;
        border-radius: 15px;
        padding: 1.5rem;
        margin-bottom: 2rem;
        box-shadow: 0 8px 25px rgba(255, 193, 7, 0.3);
    }

    .form-section {
        background: linear-gradient(135deg, #f8f9fa 0%, #e9ecef 100%);
        border-radius: 15px;
        padding: 1.5rem;
        margin-bottom: 2rem;
        border-left: 4px solid #dc3545;
        box-shadow: 0 4px 15px rgba(0, 0, 0, 0.1);
    }

    .form-section-title {
        color: #dc3545;
        font-size: 1.3rem;
        font-weight: 600;
        margin-bottom: 1rem;
        display: flex;
        align-items: center;
    }

    .form-label {
        font-weight: 600;
        color: #495057;
        margin-bottom: 0.5rem;
        display: flex;
        align-items: center;
    }

    .form-control,
    .form-select {
        border: 2px solid #e9ecef;
        border-radius: 12px;
        padding: 0.75rem 1rem;
        font-size: 1rem;
        transition: all 0.3s ease;
        background: linear-gradient(135deg, #ffffff 0%, #f8f9fa 100%);
    }

    .form-control:focus,
    .form-select:focus {
        border-color: #dc3545;
        box-shadow: 0 0 0 0.25rem rgba(220, 53, 69, 0.25);
        background: white;
    }

    .submit-btn {
        background: linear-gradient(135deg, #dc3545 0%, #b02a37 100%);
        border: none;
        color: white;
        padding: 1rem 2rem;
        border-radius: 15px;
        font-size: 1.1rem;
        font-weight: 600;
        transition: all 0.3s ease;
        box-shadow: 0 8px 25px rgba(220, 53, 69, 0.4);
        position: relative;
        overflow: hidden;
    }

    .submit-btn:hover {
        transform: translateY(-2px);
        box-shadow: 0 12px 35px rgba(220, 53, 69, 0.5);
        background: linear-gradient(135deg, #b02a37 0%, #8b2332 100%);
    }

    .submit-btn::before {
        content: '';
        position: absolute;
        top: 0;
        left: -100%;
        width: 100%;
        height: 100%;
        background: linear-gradient(90deg, transparent, rgba(255, 255, 255, 0.2), transparent);
        transition: left 0.5s;
    }

    .submit-btn:hover::before {
        left: 100%;
    }

    .anonymous-section {
        background: linear-gradient(135deg, #e3f2fd 0%, #bbdefb 100%);
        border: 2px solid #2196f3;
        border-radius: 15px;
        padding: 1.5rem;
        margin-bottom: 2rem;
        box-shadow: 0 8px 25px rgba(33, 150, 243, 0.2);
    }

    .corruption-types {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
        gap: 1rem;
        margin-bottom: 1.5rem;
    }

    .corruption-type-card {
        background: white;
        border: 2px solid #e9ecef;
        border-radius: 12px;
        padding: 1rem;
        cursor: pointer;
        transition: all 0.3s ease;
        position: relative;
    }

    .corruption-type-card:hover {
        border-color: #dc3545;
        transform: translateY(-2px);
        box-shadow: 0 8px 25px rgba(220, 53, 69, 0.2);
    }

    .corruption-type-card.selected {
        border-color: #dc3545;
        background: linear-gradient(135deg, #fff5f5 0%, #fee2e2 100%);
    }

    .protection-notice {
        background: linear-gradient(135deg, #d4edda 0%, #c3e6cb 100%);
        border: 2px solid #28a745;
        border-radius: 15px;
        padding: 1.5rem;
        margin-bottom: 2rem;
        box-shadow: 0 8px 25px rgba(40, 167, 69, 0.2);
    }

    .guidelines {
        background: linear-gradient(135deg, #f8f9fa 0%, #e9ecef 100%);
        border-radius: 15px;
        padding: 1.5rem;
        margin-bottom: 2rem;
        border-left: 4px solid #6c757d;
    }

    .access-denied-section {
        background: linear-gradient(135deg, #f8d7da 0%, #f5c6cb 100%);
        border: 2px solid #dc3545;
        border-radius: 15px;
        padding: 2rem;
        text-align: center;
        margin-bottom: 2rem;
    }

    @media (max-width: 768px) {
        .corruption-title {
            font-size: 1.8rem;
        }

        .corruption-form {
            padding: 1.5rem;
        }

        .corruption-types {
            grid-template-columns: 1fr;
        }
    }

    .animate-fade-in {
        animation: fadeIn 0.6s ease-in;
    }

    @keyframes fadeIn {
        from {
            opacity: 0;
            transform: translateY(20px);
        }

        to {
            opacity: 1;
            transform: translateY(0);
        }
    }

    /* === MODAL LOGIN STYLES (RED THEME) === */
    body {
        font-family: 'Sarabun', -apple-system, BlinkMacSystemFont, sans-serif;
    }

    /* Modal specific styles */
    .esv-modal-content {
        z-index: 9999 !important;
        border: none;
        border-radius: 20px;
        box-shadow: 0 20px 60px rgba(220, 53, 69, 0.2), 0 8px 25px rgba(0, 0, 0, 0.08);
        background: linear-gradient(135deg, #ffffff 0%, #fff5f5 100%);
        overflow: hidden;
    }

    .esv-modal-header {
        background: linear-gradient(135deg, rgba(220, 53, 69, 0.1) 0%, rgba(176, 42, 55, 0.1) 100%);
        color: #2d3748;
        border-radius: 20px 20px 0 0;
        border-bottom: 1px solid rgba(220, 53, 69, 0.2);
        backdrop-filter: blur(10px);
    }

    .esv-modal-title {
        font-weight: 600;
        color: #dc3545;
        width: 100%;
        text-align: center;
    }

    .esv-modal-body {
        padding: 2.5rem;
        background: linear-gradient(135deg, #ffffff 0%, #fff5f5 100%);
    }

    /* Button styles */
    .esv-login-btn {
        background: linear-gradient(135deg, #dc3545 0%, #b02a37 100%);
        border: none;
        color: white;
        border-radius: 15px;
        padding: 1rem 1.5rem;
        font-weight: 600;
        box-shadow: 0 6px 20px rgba(220, 53, 69, 0.4);
        transition: all 0.3s ease;
        font-size: 1.1rem;
    }

    .esv-login-btn:hover {
        transform: translateY(-2px);
        box-shadow: 0 8px 25px rgba(220, 53, 69, 0.5) !important;
        background: linear-gradient(135deg, #b02a37 0%, #8b2332 100%) !important;
        color: white;
    }

    .esv-guest-btn {
        background: rgba(220, 53, 69, 0.08);
        border: 2px solid rgba(220, 53, 69, 0.3);
        color: #dc3545;
        border-radius: 15px;
        padding: 1rem 1.5rem;
        font-weight: 600;
        transition: all 0.3s ease;
        font-size: 1.1rem;
        backdrop-filter: blur(10px);
    }

    .esv-guest-btn:hover {
        background: rgba(220, 53, 69, 0.15) !important;
        border-color: rgba(220, 53, 69, 0.5) !important;
        color: #dc3545 !important;
        transform: translateY(-2px);
        box-shadow: 0 6px 20px rgba(220, 53, 69, 0.3) !important;
    }

    /* Icon styling */
    .esv-modal-body .fas.fa-user-shield {
        font-size: 2.5rem;
        color: #dc3545;
        text-shadow: 0 2px 8px rgba(220, 53, 69, 0.4);
    }

    /* Circle icon container */
    .esv-modal-body .mb-4>div {
        width: 80px;
        height: 80px;
        margin: 0 auto;
        background: linear-gradient(135deg, rgba(220, 53, 69, 0.15) 0%, rgba(176, 42, 55, 0.15) 100%);
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        box-shadow: 0 8px 25px rgba(220, 53, 69, 0.3);
    }

    /* Text styling */
    .esv-modal-body h5 {
        color: #2d3748;
        font-weight: 600;
        text-shadow: 0 1px 3px rgba(0, 0, 0, 0.1);
    }

    .esv-modal-body p {
        font-size: 1.05rem;
        line-height: 1.6;
        color: #6c757d;
    }

    /* Modal backdrop styling */
    .modal-backdrop {
        background-color: rgba(220, 53, 69, 0.3);
        backdrop-filter: blur(5px);
    }

    /* Animation effects */
    @keyframes modalFadeIn {
        from {
            opacity: 0;
            transform: translateY(-50px);
        }

        to {
            opacity: 1;
            transform: translateY(0);
        }
    }

    .modal.show .modal-dialog {
        animation: modalFadeIn 0.3s ease-out;
    }

    /* Responsive design */
    @media (max-width: 768px) {
        .esv-modal-body {
            padding: 1.5rem;
        }

        .esv-modal-body .mb-4>div {
            width: 60px;
            height: 60px;
        }

        .esv-modal-body .fas.fa-user-shield {
            font-size: 2rem;
        }

        .esv-login-btn,
        .esv-guest-btn {
            padding: 0.8rem 1.2rem;
            font-size: 1rem;
        }

        .esv-modal-body h5 {
            font-size: 1.1rem;
        }

        .esv-modal-body p {
            font-size: 0.95rem;
        }
    }
</style>


<style>
    /* Track Button Container */
    .track-button-container {
        background: linear-gradient(135deg, #dc3545 0%, #b02a37 100%);
        padding: 1rem 0;
        position: relative;
        z-index: 10;
    }

    /* Track Button */
    .track-btn {
        display: inline-flex;
        align-items: center;
        padding: 0.75rem 1.5rem;
        background: rgba(255, 255, 255, 0.15);
        color: white;
        text-decoration: none;
        border-radius: 25px;
        font-weight: 600;
        font-size: 0.95rem;
        border: 2px solid rgba(255, 255, 255, 0.2);
        transition: all 0.3s ease;
        backdrop-filter: blur(10px);
    }

    .track-btn:hover {
        background: rgba(255, 255, 255, 0.25);
        color: white;
        text-decoration: none;
        transform: translateY(-2px);
        box-shadow: 0 8px 20px rgba(0, 0, 0, 0.2);
    }

    .track-btn:active {
        transform: translateY(0);
    }

    /* Responsive */
    @media (max-width: 768px) {
        .track-button-container {
            padding: 0.75rem 0;
        }

        .track-btn {
            padding: 0.6rem 1.2rem;
            font-size: 0.9rem;
        }
    }
</style>



<!-- Modal สำหรับการยืนยันการยื่นเอกสารโดยไม่เข้าสู่ระบบ -->
<div class="modal fade" id="guestConfirmModal" tabindex="-1" aria-labelledby="guestConfirmModalLabel" aria-hidden="true"
    style="z-index: 9999;">
    <div class="modal-dialog modal-dialog-centered modal-lg">
        <div class="modal-content esv-modal-content">
            <div class="modal-header esv-modal-header">
                <h5 class="modal-title esv-modal-title" id="guestConfirmModalLabel">
                    <i class="fas fa-shield-alt me-2" style="color: #dc3545;"></i>ยินดีต้อนรับสู่ระบบแจ้งเรื่องทุจริต
                </h5>
            </div>
            <div class="modal-body esv-modal-body text-center">
                <div class="mb-4">
                    <div
                        style="width: 80px; height: 80px; margin: 0 auto; background: linear-gradient(135deg, rgba(220, 53, 69, 0.15) 0%, rgba(176, 42, 55, 0.15) 100%); border-radius: 50%; display: flex; align-items: center; justify-content: center; box-shadow: 0 8px 25px rgba(220, 53, 69, 0.3);">
                        <i class="fas fa-user-shield"
                            style="font-size: 2.5rem; color: #dc3545; text-shadow: 0 2px 8px rgba(220, 53, 69, 0.4);"></i>
                    </div>
                </div>
                <h5 class="mb-3" style="color: #2d3748; font-weight: 600; text-shadow: 0 1px 3px rgba(0,0,0,0.1);">
                    เริ่มต้นการแจ้งเรื่องทุจริต</h5>
                <p class="text-muted mb-4" style="font-size: 1.05rem; line-height: 1.6; color: #6c757d;">
                    เข้าสู่ระบบเพื่อติดตามสถานะเรื่องร้องเรียนและได้รับการแจ้งเตือน สะดวกรวดเร็ว ปลอดภัย
                    ไม่มีใครค้นหาเอกสารของคุณได้ หรือดำเนินการต่อโดยไม่ต้องเข้าสู่ระบบ
                    ไม่ปลอดภัยบุคคลอื่นสามารถค้นหาเอกสารได้จากหน้าติดตาม</p>

                <div class="d-grid gap-3">
                    <button type="button" class="btn btn-lg esv-login-btn" onclick="redirectToLogin()">
                        <i class="fas fa-sign-in-alt me-2"></i>เข้าสู่ระบบ
                    </button>
                    <button type="button" class="btn btn-lg esv-guest-btn" onclick="proceedAsGuest()">
                        <i class="fas fa-exclamation-triangle me-2"></i>ดำเนินการต่อโดยไม่เข้าสู่ระบบ
                    </button>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="container-fluid py-4">
    <div class="corruption-container animate-fade-in">
        <!-- Header -->


        <!-- Track Status Button - Above Header -->
        <div class="track-button-container">
            <div class="container">
                <div class="text-end">
                    <a href="<?php echo site_url('Corruption/track_status'); ?>" class="track-btn">
                        <i class="fas fa-search me-2"></i>
                        ติดตามสถานะ
                    </a>
                </div>
            </div>
        </div>


        <div class="corruption-header">
            <div class="corruption-icon">
                <i class="fas fa-shield-alt" style="font-size: 2.5rem; color: white;"></i>
            </div>
            <h1 class="corruption-title">แจ้งเรื่องร้องเรียนการทุจริตและประพฤติมิชอบ</h1>
            <p class="corruption-subtitle">ช่องทางแจ้งเรื่องร้องเรียนการทุจริตและประพฤติมิชอบ</p>
        </div>

        <div class="corruption-form">
            <!-- คำเตือนสำคัญ -->
            <div class="warning-notice">
                <div class="d-flex align-items-start">
                    <div style="margin-right: 1rem;">
                        <i class="fas fa-exclamation-triangle" style="font-size: 2rem; color: #856404;"></i>
                    </div>
                    <div>
                        <h5 style="color: #856404; font-weight: 700; margin-bottom: 0.5rem;">
                            <i class="fas fa-gavel me-2"></i>ข้อกำหนดสำคัญในการแจ้งเรื่อง
                        </h5>
                        <ul style="color: #856404; margin: 0; padding-left: 1.5rem;">
                            <li>การกล่าวหาเท็จอาจมีความผิดทางกฎหมาย</li>
                            <li>ควรมีหลักฐานสนับสนุนข้อกล่าวหา</li>
                            <li>ข้อมูลจะถูกตรวจสอบและดำเนินการตามกฎหมาย</li>
                            <li>หน่วยงานขอสงวนสิทธิ์ในการคุ้มครองผู้แจ้งเบาะแส</li>
                        </ul>
                    </div>
                </div>
            </div>

            <!-- ประเภทการทุจริต -->
            <div class="form-section">
                <h3 class="form-section-title">
                    <i class="fas fa-list-check me-2"></i>ประเภทการทุจริตที่ต้องการแจ้ง
                </h3>
                <div class="corruption-types" id="corruptionTypes">
                    <div class="corruption-type-card" data-value="embezzlement">
                        <div class="d-flex align-items-center mb-2">
                            <i class="fas fa-money-bill-wave text-danger me-2"></i>
                            <strong>การยักยอกเงิน</strong>
                        </div>
                        <small class="text-muted">การนำเงินสาธารณะไปใช้ส่วนตัว</small>
                    </div>
                    <div class="corruption-type-card" data-value="bribery">
                        <div class="d-flex align-items-center mb-2">
                            <i class="fas fa-handshake text-danger me-2"></i>
                            <strong>การรับสินบน</strong>
                        </div>
                        <small class="text-muted">การรับของหรือเงินเพื่อการปฏิบัติหน้าที่</small>
                    </div>
                    <div class="corruption-type-card" data-value="abuse_of_power">
                        <div class="d-flex align-items-center mb-2">
                            <i class="fas fa-crown text-danger me-2"></i>
                            <strong>การใช้อำนาจเกินตัว</strong>
                        </div>
                        <small class="text-muted">การใช้ตำแหน่งหน้าที่เพื่อประโยชน์ส่วนตัว</small>
                    </div>
                    <div class="corruption-type-card" data-value="conflict_of_interest">
                        <div class="d-flex align-items-center mb-2">
                            <i class="fas fa-balance-scale text-danger me-2"></i>
                            <strong>ผลประโยชน์ทับซ้อน</strong>
                        </div>
                        <small class="text-muted">การมีผลประโยชน์ทับซ้อนกับหน้าที่</small>
                    </div>
                    <div class="corruption-type-card" data-value="procurement_fraud">
                        <div class="d-flex align-items-center mb-2">
                            <i class="fas fa-file-contract text-danger me-2"></i>
                            <strong>การทุจริตในการจัดซื้อ</strong>
                        </div>
                        <small class="text-muted">การทุจริตในกระบวนการจัดหาพัสดุ</small>
                    </div>
                    <div class="corruption-type-card" data-value="other">
                        <div class="d-flex align-items-center mb-2">
                            <i class="fas fa-ellipsis-h text-danger me-2"></i>
                            <strong>อื่นๆ</strong>
                        </div>
                        <small class="text-muted">การทุจริตประเภทอื่นๆ</small>
                    </div>
                </div>
                <input type="hidden" id="selectedCorruptionType" name="corruption_type" required>
            </div>

            <form id="corruptionForm" onsubmit="return false;">
                <!-- รายละเอียดเรื่องร้องเรียน -->
                <div class="form-section">
                    <h3 class="form-section-title">
                        <i class="fas fa-file-alt me-2"></i>รายละเอียดเรื่องร้องเรียน
                    </h3>

                    <div class="mb-3">
                        <label class="form-label">
                            <i class="fas fa-heading me-2 text-danger"></i>หัวข้อเรื่องร้องเรียน <span
                                class="text-danger">*</span>
                        </label>
                        <input type="text" class="form-control" name="complaint_subject" required
                            placeholder="เช่น พบเห็นการรับสินบนของเจ้าหน้าที่">
                    </div>

                    <div class="mb-3">
                        <label class="form-label">
                            <i class="fas fa-align-left me-2 text-danger"></i>รายละเอียดเหตุการณ์ <span
                                class="text-danger">*</span>
                        </label>
                        <textarea class="form-control" name="complaint_details" rows="6" required
                            placeholder="กรุณาระบุรายละเอียดเหตุการณ์ที่พบเห็น เช่น วันที่ เวลา สถานที่ บุคคลที่เกี่ยวข้อง และการกระทำที่เป็นการทุจริต"></textarea>
                    </div>

                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label class="form-label">
                                <i class="fas fa-calendar-alt me-2 text-danger"></i>วันที่เกิดเหตุ
                            </label>
                            <input type="date" class="form-control" name="incident_date"
                                title="วันที่เกิดเหตุ (ไม่บังคับ) ต้องไม่เป็นวันที่ในอนาคต">
                            <small class="text-muted">ไม่บังคับ - หากทราบวันที่เกิดเหตุ</small>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label">
                                <i class="fas fa-clock me-2 text-danger"></i>เวลาที่เกิดเหตุ (โดยประมาณ)
                            </label>
                            <input type="time" class="form-control" name="incident_time"
                                title="เวลาที่เกิดเหตุ (ไม่บังคับ) รูปแบบ ชช:นน">
                            <small class="text-muted">ไม่บังคับ - หากทราบเวลาเกิดเหตุโดยประมาณ</small>
                        </div>
                    </div>

                    <div class="mb-3">
                        <label class="form-label">
                            <i class="fas fa-map-marker-alt me-2 text-danger"></i>สถานที่เกิดเหตุ
                        </label>
                        <textarea class="form-control" name="incident_location" rows="2"
                            placeholder="กรุณาระบุสถานที่เกิดเหตุให้ละเอียด"></textarea>
                    </div>
                </div>

                <!-- บุคคลที่เกี่ยวข้อง -->
                <div class="form-section">
                    <h3 class="form-section-title">
                        <i class="fas fa-users me-2"></i>บุคคลที่เกี่ยวข้อง
                    </h3>

                    <div class="mb-3">
                        <label class="form-label">
                            <i class="fas fa-user-tie me-2 text-danger"></i>ชื่อ-นามสกุล ผู้กระทำผิด <span
                                class="text-danger">*</span>
                        </label>
                        <input type="text" class="form-control" name="perpetrator_name" required
                            placeholder="หากไม่ทราบชื่อแท้ ให้ระบุชื่อเล่น ตำแหน่ง หรือลักษณะของบุคคล">
                    </div>

                    <div class="mb-3">
                        <label class="form-label">
                            <i class="fas fa-building me-2 text-danger"></i>หน่วยงาน/แผนก
                        </label>
                        <input type="text" class="form-control" name="perpetrator_department"
                            placeholder="หน่วยงานหรือแผนกที่ผู้กระทำผิดสังกัด">
                    </div>

                    <div class="mb-3">
                        <label class="form-label">
                            <i class="fas fa-id-badge me-2 text-danger"></i>ตำแหน่ง/บทบาท
                        </label>
                        <input type="text" class="form-control" name="perpetrator_position"
                            placeholder="ตำแหน่งหรือบทบาทของผู้กระทำผิด">
                    </div>

                    <div class="mb-3">
                        <label class="form-label">
                            <i class="fas fa-users me-2 text-danger"></i>บุคคลอื่นที่เกี่ยวข้อง
                        </label>
                        <textarea class="form-control" name="other_involved" rows="3"
                            placeholder="รายชื่อและรายละเอียดของบุคคลอื่นที่เกี่ยวข้องในเหตุการณ์ (ถ้ามี)"></textarea>
                    </div>
                </div>

                <!-- หลักฐาน -->
                <div class="form-section">
                    <h3 class="form-section-title">
                        <i class="fas fa-paperclip me-2"></i>หลักฐานประกอบ
                    </h3>

                    <div class="mb-3">
                        <label class="form-label">
                            <i class="fas fa-camera me-2 text-danger"></i>อัพโหลดรูปภาพ/เอกสารหลักฐาน
                        </label>

                        <!-- File Upload Area -->
                        <div class="file-upload-area" onclick="document.getElementById('evidenceFiles').click()">
                            <div id="uploadPlaceholder">
                                <i class="fas fa-cloud-upload-alt"
                                    style="font-size: 3rem; color: #dc3545; margin-bottom: 1rem;"></i>
                                <h5 style="color: #dc3545; margin-bottom: 0.5rem;">คลิกเพื่อเลือกไฟล์หลักฐาน</h5>
                                <p class="text-muted mb-0">หรือลากไฟล์มาวางที่นี่</p>
                                <small class="text-muted">รองรับไฟล์: รูปภาพ, PDF, เอกสาร (สูงสุด 10 ไฟล์, ไฟล์ละไม่เกิน
                                    10MB)</small>
                            </div>
                        </div>

                        <!-- Hidden File Input -->
                        <input type="file" id="evidenceFiles" multiple accept="image/*,.pdf,.doc,.docx,.xls,.xlsx"
                            style="display: none;">

                        <!-- File Management Buttons -->
                        <div class="file-management-buttons mt-2" style="display: none;" id="fileManagementButtons">
                            <button type="button" class="btn btn-sm btn-outline-secondary me-2"
                                onclick="document.getElementById('evidenceFiles').click()">
                                <i class="fas fa-plus me-1"></i>เพิ่มไฟล์
                            </button>
                            <button type="button" class="btn btn-sm btn-outline-danger" onclick="clearAllFiles()">
                                <i class="fas fa-trash me-1"></i>ลบทั้งหมด
                            </button>
                            <button type="button" class="btn btn-sm btn-outline-info" onclick="debugFiles()">
                                <i class="fas fa-info-circle me-1"></i>ดูข้อมูล
                            </button>
                        </div>

                        <!-- File Preview Area -->
                        <div id="filePreview" class="mt-3" style="display: none;"></div>
                    </div>

                    <!-- File Upload Guidelines -->
                    <div class="upload-guidelines">
                        <h6 class="text-muted mb-2">
                            <i class="fas fa-info-circle me-2"></i>คำแนะนำการอัพโหลดไฟล์
                        </h6>
                        <div class="row">
                            <div class="col-md-6">
                                <ul class="list-unstyled small text-muted">
                                    <li><i class="fas fa-check text-success me-2"></i>รูปภาพ: JPG, PNG, GIF</li>
                                    <li><i class="fas fa-check text-success me-2"></i>เอกสาร: PDF, Word, Excel</li>
                                    <li><i class="fas fa-check text-success me-2"></i>ขนาดไฟล์: ไม่เกิน 10MB</li>
                                </ul>
                            </div>
                            <div class="col-md-6">
                                <ul class="list-unstyled small text-muted">
                                    <li><i class="fas fa-check text-success me-2"></i>จำนวนไฟล์: สูงสุด 10 ไฟล์</li>
                                    <li><i class="fas fa-check text-success me-2"></i>ชื่อไฟล์: ภาษาไทยหรือภาษาอังกฤษ
                                    </li>
                                    <li><i class="fas fa-shield-alt text-primary me-2"></i>ข้อมูลปลอดภัย 100%</li>
                                </ul>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- ข้อมูลผู้แจ้งเบาะแส -->
                <div class="anonymous-section">
                    <h3 style="color: #1976d2; font-size: 1.3rem; font-weight: 600; margin-bottom: 1rem;">
                        <i class="fas fa-user-shield me-2"></i>ข้อมูลผู้แจ้งเบาะแส
                    </h3>

                    <div class="form-check mb-3">
                        <input class="form-check-input" type="checkbox" id="anonymousReport" name="is_anonymous"
                            value="1" onchange="toggleAnonymous()">
                        <label class="form-check-label" for="anonymousReport">
                            <strong><i class="fas fa-user-secret me-2"></i>แจ้งแบบไม่ระบุตัวตน</strong>
                            <br><small class="text-muted">เลือกหากต้องการปกปิดข้อมูลส่วนตัวของผู้แจ้ง</small>
                        </label>
                    </div>

                    <input type="hidden" id="hiddenAnonymous" name="anonymous_flag" value="0">

                    <!-- ข้อมูลสำหรับ Guest User -->
                    <div id="guestFormSection">
                        <div class="alert alert-warning">
                            <i class="fas fa-info-circle me-2"></i>
                            <strong>สำหรับผู้ใช้ทั่วไป</strong><br>
                            กรุณากรอกข้อมูลของท่านเพื่อการติดต่อกลับ
                        </div>

                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label class="form-label">
                                    <i class="fas fa-user me-2 text-primary"></i>ชื่อ-นามสกุล <span
                                        class="text-danger">*</span>
                                </label>
                                <input type="text" class="form-control" name="guest_reporter_name"
                                    id="guestReporterName" placeholder="ชื่อจริงของผู้แจ้งเบาะแส" data-required="true"
                                    required>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="form-label">
                                    <i class="fas fa-phone me-2 text-primary"></i>เบอร์โทรศัพท์ <span
                                        class="text-danger">*</span>
                                </label>
                                <input type="tel" class="form-control" name="guest_reporter_phone"
                                    id="guestReporterPhone" placeholder="หมายเลขโทรศัพท์ที่สามารถติดต่อได้"
                                    data-required="true" required>
                            </div>
                        </div>

                        <div class="mb-3">
                            <label class="form-label">
                                <i class="fas fa-envelope me-2 text-primary"></i>อีเมล
                            </label>
                            <input type="email" class="form-control" name="guest_reporter_email" id="guestReporterEmail"
                                placeholder="อีเมลสำหรับการติดต่อกลับ">
                        </div>

                        <div class="mb-3">
                            <label class="form-label">
                                <i class="fas fa-link me-2 text-primary"></i>ความสัมพันธ์กับเหตุการณ์ <span
                                    class="text-danger">*</span>
                            </label>
                            <select class="form-select" name="guest_reporter_relation" data-required="true" required>
                                <option value="">เลือกความสัมพันธ์</option>
                                <option value="witness">ผู้เห็นเหตุการณ์</option>
                                <option value="victim">ผู้เสียหาย</option>
                                <option value="colleague">เพื่อนร่วมงาน</option>
                                <option value="citizen">ประชาชนทั่วไป</option>
                                <option value="other">อื่นๆ</option>
                            </select>
                        </div>
                    </div>

                    <!-- สำหรับโหมดไม่ระบุตัวตน -->
                    <div id="anonymousNotice" style="display: none;">
                        <div class="alert alert-info">
                            <i class="fas fa-info-circle me-2"></i>
                            <strong>การแจ้งแบบไม่ระบุตัวตน</strong><br>
                            ข้อมูลของคุณจะถูกเก็บเป็นความลับ และจะไม่มีการเปิดเผยต่อบุคคลภายนอก

                            <!-- ความสัมพันธ์กับเหตุการณ์ สำหรับ Anonymous -->
                            <div class="mt-3">
                                <label class="form-label">
                                    <i class="fas fa-link me-2 text-primary"></i>ความสัมพันธ์กับเหตุการณ์ <span
                                        class="text-danger">*</span>
                                </label>
                                <select class="form-select" name="anonymous_reporter_relation" required>
                                    <option value="">เลือกความสัมพันธ์</option>
                                    <option value="witness">ผู้เห็นเหตุการณ์</option>
                                    <option value="victim">ผู้เสียหาย</option>
                                    <option value="colleague">เพื่อนร่วมงาน</option>
                                    <option value="citizen">ประชาชนทั่วไป</option>
                                    <option value="other">อื่นๆ</option>
                                </select>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- หลักเกณฑ์การคุ้มครอง -->
                <div class="protection-notice">
                    <h5 style="color: #155724; font-weight: 700; margin-bottom: 1rem;">
                        <i class="fas fa-shield-alt me-2"></i>การคุ้มครองผู้แจ้งเบาะแส
                    </h5>
                    <div class="row">
                        <div class="col-md-6">
                            <ul style="color: #155724; margin: 0; padding-left: 1.5rem;">
                                <li>ข้อมูลผู้แจ้งจะถูกเก็บเป็นความลับ</li>
                                <li>มีมาตรการคุ้มครองตามกฎหมาย</li>
                                <li>ไม่เปิดเผยข้อมูลแก่บุคคลที่ไม่เกี่ยวข้อง</li>
                            </ul>
                        </div>
                        <div class="col-md-6">
                            <ul style="color: #155724; margin: 0; padding-left: 1.5rem;">
                                <li>สามารถขอการคุ้มครองเพิ่มเติมได้</li>
                                <li>มีช่องทางติดตามความคืบหน้า</li>
                                <li>ได้รับการดูแลความปลอดภัย</li>
                            </ul>
                        </div>
                    </div>
                </div>

                <!-- หลักเกณฑ์การดำเนินการ -->
                <div class="guidelines">
                    <h5 style="color: #495057; font-weight: 700; margin-bottom: 1rem;">
                        <i class="fas fa-clipboard-list me-2"></i>ขั้นตอนการดำเนินการ
                    </h5>
                    <div class="row">
                        <div class="col-md-3 text-center mb-3">
                            <div
                                style="width: 60px; height: 60px; background: linear-gradient(135deg, #dc3545, #b02a37); border-radius: 50%; display: flex; align-items: center; justify-content: center; margin: 0 auto 0.5rem; color: white;">
                                <span style="font-weight: bold; font-size: 1.2rem;">1</span>
                            </div>
                            <small><strong>รับเรื่อง</strong><br>ตรวจสอบข้อมูล</small>
                        </div>
                        <div class="col-md-3 text-center mb-3">
                            <div
                                style="width: 60px; height: 60px; background: linear-gradient(135deg, #ffc107, #e0a800); border-radius: 50%; display: flex; align-items: center; justify-content: center; margin: 0 auto 0.5rem; color: white;">
                                <span style="font-weight: bold; font-size: 1.2rem;">2</span>
                            </div>
                            <small><strong>สอบสวน</strong><br>ตรวจสอบข้อเท็จจริง</small>
                        </div>
                        <div class="col-md-3 text-center mb-3">
                            <div
                                style="width: 60px; height: 60px; background: linear-gradient(135deg, #17a2b8, #138496); border-radius: 50%; display: flex; align-items: center; justify-content: center; margin: 0 auto 0.5rem; color: white;">
                                <span style="font-weight: bold; font-size: 1.2rem;">3</span>
                            </div>
                            <small><strong>ดำเนินการ</strong><br>ส่งเรื่องดำเนินการในขั้นตอนต่อไป</small>
                        </div>
                        <div class="col-md-3 text-center mb-3">
                            <div
                                style="width: 60px; height: 60px; background: linear-gradient(135deg, #28a745, #1e7e34); border-radius: 50%; display: flex; align-items: center; justify-content: center; margin: 0 auto 0.5rem; color: white;">
                                <span style="font-weight: bold; font-size: 1.2rem;">4</span>
                            </div>
                            <small><strong>รายงาน</strong><br>แจ้งผลการดำเนินการ</small>
                        </div>
                    </div>
                </div>

                <!-- ปุ่มส่ง -->
                <div class="text-center">
                    <button type="button" class="submit-btn" onclick="submitCorruptionReport()">
                        <i class="fas fa-paper-plane me-2"></i>ส่งรายงานการทุจริต
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Bootstrap JS -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>

<script>
    // === GLOBAL VARIABLES ===
    let selectedFiles = [];
    let fileIdCounter = 0;
    let selectedCorruptionType = '';

    // *** MODAL LOGIN JAVASCRIPT ***
    // ตัวแปรสำหรับ Modal
    let guestModalInstance = null;

    // ตัวแปรตรวจสอบสถานะ login (เปลี่ยนตามระบบจริง)
    const isUserLoggedIn = false; // จะได้จาก PHP หรือ session
    const userInfo = null; // ข้อมูล user จาก PHP
    let hasConfirmedAsGuest = isUserLoggedIn; // หากยัง login ไม่ต้องแสดง modal

    // ฟังก์ชันเมื่อโหลดหน้าเสร็จ
    // ===== แก้ไขใน JavaScript Section =====

    // *** แก้ไขตัวแปร Global และ Logic การตรวจสอบ ***
    document.addEventListener('DOMContentLoaded', function () {
        console.log('🚀 Document loaded, checking login status...');

        // *** รับค่าจาก PHP อย่างถูกต้อง ***
        const phpData = window.phpData || {};
        const isUserLoggedIn = phpData.isLoggedIn || <?php echo json_encode($is_logged_in ?? false); ?>;
        const userType = phpData.userType || <?php echo json_encode($user_type ?? 'guest'); ?>;
        const accessDenied = phpData.accessDenied || <?php echo json_encode($access_denied ?? false); ?>;
        const userInfo = phpData.userInfo || <?php echo json_encode($user_info ?? null); ?>;

        console.log('Login Status Data:', {
            isUserLoggedIn,
            userType,
            accessDenied,
            userInfo
        });

        // *** แก้ไขตรงนี้ - กำหนด hasConfirmedAsGuest ให้ถูกต้อง ***
        // หาก login แล้วถือว่า confirmed แล้ว
        // หากเป็น staff แต่ยัง access denied ให้ถือว่ายัง confirm
        let hasConfirmedAsGuest = isUserLoggedIn && userType !== 'staff';

        // ถ้าเป็น staff ที่ access denied ให้ไม่ต้องแสดง modal แต่ให้แสดงข้อความแทน
        if (isUserLoggedIn && userType === 'staff' && accessDenied) {
            hasConfirmedAsGuest = true; // ไม่ให้แสดง modal
            console.log('⚠️ Staff user with access denied - no modal needed');
        }

        console.log('Final hasConfirmedAsGuest:', hasConfirmedAsGuest);

        // แสดง modal เฉพาะเมื่อ:
        // 1. ยังไม่ได้ login 
        // 2. ยังไม่ได้ confirm เป็น guest
        // 3. ไม่ใช่ staff ที่ access denied
        if (!isUserLoggedIn && !hasConfirmedAsGuest) {
            console.log('⏰ Setting timer to show modal in 2 seconds...');
            setTimeout(() => {
                showModal();
            }, 2000);
        } else if (isUserLoggedIn && userType === 'staff' && accessDenied) {
            // หาก login เป็น staff แต่ access denied ให้แสดงข้อความเตือน
            console.log('⚠️ Staff user detected with access denied');
            setTimeout(() => {
                showStaffAccessDeniedMessage();
            }, 1000);
        } else {
            console.log('✅ User is logged in or access granted, modal will not show');

            // หาก login แล้วให้เติมข้อมูลในฟอร์มอัตโนมัติ
            if (isUserLoggedIn && userType === 'public' && userInfo) {
                populateUserForm(userInfo);
            }
        }

        // *** เก็บค่าไว้ใน global scope สำหรับใช้ในฟังก์ชันอื่น ***
        window.corruptionFormState = {
            isUserLoggedIn,
            userType,
            accessDenied,
            userInfo,
            hasConfirmedAsGuest
        };

        // เรียก setup functions อื่นๆ
        setupUserFormFields();
        setupCorruptionTypeSelection();
        setupFileUpload();
        setupPhoneValidation();

        // console.log('✅ All systems initialized');
    });





    function populateUserForm(userInfo) {
        try {
            // เติมข้อมูลในฟอร์ม guest
            const nameField = document.querySelector('input[name="guest_reporter_name"]');
            const phoneField = document.querySelector('input[name="guest_reporter_phone"]');
            const emailField = document.querySelector('input[name="guest_reporter_email"]');

            if (nameField && userInfo.name) {
                nameField.value = userInfo.name;
                nameField.setAttribute('readonly', 'readonly');
                nameField.style.backgroundColor = '#f8f9fa';
            }

            if (phoneField && userInfo.phone) {
                phoneField.value = userInfo.phone;
            }

            if (emailField && userInfo.email) {
                emailField.value = userInfo.email;
            }

            // แสดงข้อความแจ้งเตือนว่าเป็นสมาชิก
            showUserWelcomeMessage(userInfo);

        } catch (error) {
            console.error('Error populating user form:', error);
        }
    }




    function showUserWelcomeMessage(userInfo) {
        if (typeof Swal !== 'undefined') {
            Swal.fire({
                icon: 'success',
                title: 'ยินดีต้อนรับ',
                html: `
                <div class="text-center">
                    <p>สวัสดี <strong>${userInfo.name}</strong></p>
                    <p>ระบบได้เติมข้อมูลของท่านในฟอร์มแล้ว</p>
                    <small class="text-muted">ท่านสามารถแก้ไขข้อมูลได้ตามต้องการ</small>
                </div>
            `,
                timer: 3000,
                showConfirmButton: false,
                toast: true,
                position: 'top-end',
                background: 'linear-gradient(135deg, #d4edda 0%, #c3e6cb 100%)',
                color: '#155724'
            });
        }
    }




    function showStaffAccessDeniedMessage() {
        if (typeof Swal !== 'undefined') {
            Swal.fire({
                icon: 'warning',
                title: 'ไม่สามารถเข้าใช้งานได้',
                html: `
                <div class="text-center">
                    <p>ท่านเข้าสู่ระบบในฐานะเจ้าหน้าที่</p>
                    <p>ไม่สามารถแจ้งรายงานการทุจริตได้</p>
                    <hr>
                    <p><strong>วิธีการใช้งาน:</strong></p>
                    <ul class="text-start">
                        <li>ออกจากระบบบุคลากรภายใน</li>
                        <li>เข้าสู่ระบบด้วยบัญชีประชาชน</li>
                        <li>หรือใช้งานในฐานะผู้ไม่ระบุตัวตน</li>
                    </ul>
                </div>
            `,
                confirmButtonText: 'ออกจากระบบ',
                showCancelButton: true,
                cancelButtonText: 'ยกเลิก',
                confirmButtonColor: '#dc3545',
                cancelButtonColor: '#6c757d'
            }).then((result) => {
                if (result.isConfirmed) {
                    // นำไปหน้า logout
                    window.location.href = '/User/logout';
                }
            });
        }
    }


    // ฟังก์ชันแสดง Modal
    function showModal() {
        console.log('📱 Attempting to show modal...');

        const modalElement = document.getElementById('guestConfirmModal');
        if (!modalElement) {
            console.error('❌ Modal element not found');
            return;
        }

        //console.log('✅ Modal element found, creating Bootstrap modal...');

        // ใช้ Bootstrap Modal
        if (typeof bootstrap !== 'undefined' && bootstrap.Modal) {
            try {
                guestModalInstance = new bootstrap.Modal(modalElement, {
                    backdrop: 'static', // ไม่ให้ปิดเมื่อคลิกข้างนอก
                    keyboard: false     // ไม่ให้ปิดด้วย ESC
                });

                //  console.log('✅ Bootstrap Modal instance created');
                guestModalInstance.show();
                console.log('🎯 Modal show() called');

                // เพิ่ม event listeners สำหรับ debug
                modalElement.addEventListener('shown.bs.modal', function () {
                    // console.log('✅ Modal is now visible');
                });

                modalElement.addEventListener('hidden.bs.modal', function () {
                    console.log('📴 Modal is now hidden');
                });

            } catch (error) {
                console.error('❌ Error creating Bootstrap modal:', error);

                // Fallback method
                fallbackShowModal(modalElement);
            }
        } else {
            console.warn('⚠️ Bootstrap not available, using fallback method');
            fallbackShowModal(modalElement);
        }
    }

    // Fallback method สำหรับแสดง modal
    function fallbackShowModal(modalElement) {
        console.log('🔄 Using fallback modal display method');

        modalElement.style.display = 'block';
        modalElement.classList.add('show');
        modalElement.style.opacity = '1';
        document.body.classList.add('modal-open');

        // เพิ่ม backdrop
        const backdrop = document.createElement('div');
        backdrop.className = 'modal-backdrop fade show';
        backdrop.id = 'modal-backdrop-fallback';
        backdrop.style.zIndex = '1040';
        document.body.appendChild(backdrop);

        // console.log('✅ Fallback modal displayed');
    }

    // ฟังก์ชันซ่อน Modal
    function hideModal() {
        console.log('📱 Hiding modal...');

        const modalElement = document.getElementById('guestConfirmModal');

        if (guestModalInstance) {
            try {
                guestModalInstance.hide();
                guestModalInstance = null;
                // console.log('✅ Bootstrap modal hidden');
            } catch (error) {
                console.error('❌ Error hiding Bootstrap modal:', error);
            }
        } else if (modalElement) {
            // Fallback method
            modalElement.style.display = 'none';
            modalElement.classList.remove('show');
            modalElement.style.opacity = '0';
            document.body.classList.remove('modal-open');

            // ลบ backdrop
            const backdrop = document.getElementById('modal-backdrop-fallback');
            if (backdrop) {
                backdrop.remove();
            }

            // console.log('✅ Fallback modal hidden');
        }
    }




    function checkCurrentLoginStatus() {
        fetch('<?php echo site_url("Corruption/check_login_status"); ?>', {
            method: 'GET',
            headers: {
                'Cache-Control': 'no-cache'
            }
        })
            .then(response => response.json())
            .then(data => {
                console.log('🔄 Current Login Status Check:', data);

                // อัปเดต global state
                if (window.corruptionFormState) {
                    window.corruptionFormState.isUserLoggedIn = data.is_logged_in || false;
                    window.corruptionFormState.userType = data.user_type || 'guest';
                    window.corruptionFormState.accessDenied = data.access_denied || false;
                    window.corruptionFormState.userInfo = data.user_info || null;

                    // ถ้า login แล้วให้ถือว่า confirmed
                    if (data.is_logged_in && data.user_type !== 'staff') {
                        window.corruptionFormState.hasConfirmedAsGuest = true;
                    }
                }

                // แสดงผลลัพธ์
                if (typeof Swal !== 'undefined') {
                    Swal.fire({
                        title: 'สถานะการเข้าสู่ระบบ',
                        html: `
                    <div class="text-start">
                        <p><strong>Login:</strong> ${data.is_logged_in ? '✅ เข้าสู่ระบบแล้ว' : '❌ ยังไม่ได้เข้าสู่ระบบ'}</p>
                        <p><strong>ประเภท:</strong> ${data.user_type}</p>
                        <p><strong>การเข้าถึง:</strong> ${data.access_denied ? '❌ ถูกปฏิเสธ' : '✅ อนุญาต'}</p>
                        ${data.user_info ? `<p><strong>ชื่อ:</strong> ${data.user_info.name}</p>` : ''}
                        <p><strong>Guest Confirmed:</strong> ${window.corruptionFormState?.hasConfirmedAsGuest ? '✅ Yes' : '❌ No'}</p>
                        <hr>
                        <small class="text-muted">เวลาตรวจสอบ: ${data.timestamp}</small>
                    </div>
                `,
                        icon: data.is_logged_in ? 'success' : 'info',
                        confirmButtonText: 'ปิด'
                    });
                }
            })
            .catch(error => {
                console.error('Error checking login status:', error);
                alert('เกิดข้อผิดพลาดในการตรวจสอบสถานะ');
            });
    }

    // *** เพิ่ม function สำหรับ reset guest confirmation (สำหรับ debug) ***
    function resetGuestConfirmation() {
        if (window.corruptionFormState) {
            window.corruptionFormState.hasConfirmedAsGuest = false;
        }
        hasConfirmedAsGuest = false;
        console.log('🔄 Guest confirmation reset to false');

        if (typeof Swal !== 'undefined') {
            Swal.fire({
                icon: 'info',
                title: 'Reset Guest Confirmation',
                text: 'สถานะ Guest Confirmation ถูก reset แล้ว',
                timer: 2000,
                showConfirmButton: false
            });
        }
    }




    // ฟังก์ชันดำเนินการต่อโดยไม่เข้าสู่ระบบ
    function proceedAsGuest() {
        // console.log('👤 User chose to proceed as guest');

        // อัปเดต global state
        if (window.corruptionFormState) {
            window.corruptionFormState.hasConfirmedAsGuest = true;
        }

        // อัปเดต local variable (deprecated แต่เก็บไว้เผื่อใช้)
        hasConfirmedAsGuest = true;

        hideModal();

        // แสดงข้อความแจ้งเตือน
        if (typeof Swal !== 'undefined') {
            Swal.fire({
                icon: 'info',
                title: 'ดำเนินการต่อโดยไม่เข้าสู่ระบบ',
                text: 'คุณสามารถกรอกข้อมูลและแจ้งเรื่องทุจริตได้แล้ว',
                timer: 3000,
                showConfirmButton: false,
                toast: true,
                position: 'top-end',
                background: 'linear-gradient(135deg, #e3f2fd 0%, #bbdefb 100%)',
                color: '#1976d2'
            });
        } else {
            alert('ดำเนินการต่อโดยไม่เข้าสู่ระบบ - คุณสามารถกรอกข้อมูลและแจ้งเรื่องทุจริตได้แล้ว');
        }

        // console.log('✅ Guest confirmation updated - hasConfirmedAsGuest: true');
    }

    // ฟังก์ชันนำไปหน้า Login
    function redirectToLogin() {
        console.log('🔐 Redirecting to login page');

        hideModal();

        // บันทึก URL ปัจจุบันเพื่อกลับมาหลัง login
        const currentUrl = window.location.href;
        sessionStorage.setItem('redirect_after_login', currentUrl);

        // แสดงข้อความแจ้งเตือนก่อนไป
        if (typeof Swal !== 'undefined') {
            Swal.fire({
                icon: 'info',
                title: 'กำลังนำท่านไปหน้าเข้าสู่ระบบ',
                text: 'หลังจากเข้าสู่ระบบสำเร็จ จะกลับมายังหน้านี้โดยอัตโนมัติ',
                timer: 2000,
                showConfirmButton: false,
                timerProgressBar: true,
                background: 'linear-gradient(135deg, #f0f9ff 0%, #e0f2fe 100%)',
                color: '#2d3748'
            }).then(() => {
                // ไปหน้า login (ปรับ URL ตามระบบของคุณ)
                window.location.href = '/user?redirect=' + encodeURIComponent(currentUrl);
            });
        } else {
            // Fallback หาก SweetAlert ไม่พร้อม
            setTimeout(() => {
                window.location.href = '/user?redirect=' + encodeURIComponent(currentUrl);
            }, 1000);
        }
    }

    // ฟังก์ชันสำหรับทดสอบ modal (เรียกใน console)
    function testModal() {
        console.log('🧪 Testing modal...');
        hasConfirmedAsGuest = false;
        showModal();
    }

    // ฟังก์ชันรีเซ็ตสถานะ
    function resetGuestConfirmation() {
        hasConfirmedAsGuest = false;
        console.log('🔄 Guest confirmation status reset');
    }

    // Global functions for inline onclick handlers
    window.proceedAsGuest = proceedAsGuest;
    window.redirectToLogin = redirectToLogin;
    window.testModal = testModal;
    window.resetGuestConfirmation = resetGuestConfirmation;

    // *** MAIN APPLICATION FUNCTIONS ***

    function setupUserFormFields() {
        console.log('👤 Setting up user form fields...');

        // รับค่าจาก PHP
        const isLoggedIn = <?php echo json_encode($is_logged_in ?? false); ?>;
        const userType = <?php echo json_encode($user_type ?? 'guest'); ?>;
        const accessDenied = <?php echo json_encode($access_denied ?? false); ?>;

        const guestFormSection = document.getElementById('guestFormSection');
        const accessDeniedSection = document.querySelector('.access-denied-section');

        if (accessDenied) {
            // แสดงส่วนการปฏิเสธการเข้าถึงสำหรับ staff
            if (guestFormSection) guestFormSection.style.display = 'none';
            if (accessDeniedSection) accessDeniedSection.style.display = 'block';
        } else {
            // แสดงฟอร์มปกติ
            if (guestFormSection) {
                guestFormSection.style.display = 'block';
                // console.log('✅ Guest form section displayed');
            }
            if (accessDeniedSection) accessDeniedSection.style.display = 'none';
        }

        // เพิ่ม label แสดงสถานะ
        addUserStatusLabel(isLoggedIn, userType);
    }

    // เพิ่มฟังก์ชันแสดง label สถานะผู้ใช้
    function addUserStatusLabel(isLoggedIn, userType) {
        const formSection = document.querySelector('.anonymous-section');
        if (!formSection) return;

        // ลบ label เก่า (ถ้ามี)
        const existingLabel = formSection.querySelector('.user-status-label');
        if (existingLabel) existingLabel.remove();

        // สร้าง label ใหม่
        const statusLabel = document.createElement('div');
        statusLabel.className = 'user-status-label alert mb-3';

        if (isLoggedIn) {
            if (userType === 'public') {
                statusLabel.className += ' alert-success';
                statusLabel.innerHTML = `
                <i class="fas fa-user-check me-2"></i>
                <strong>เข้าสู่ระบบในฐานะสมาชิก</strong><br>
                <small>ข้อมูลของท่านจะถูกเติมในฟอร์มโดยอัตโนมัติ</small>
            `;
            } else if (userType === 'staff') {
                statusLabel.className += ' alert-danger';
                statusLabel.innerHTML = `
                <i class="fas fa-user-times me-2"></i>
                <strong>เข้าสู่ระบบในฐานะเจ้าหน้าที่</strong><br>
                <small>ไม่สามารถแจ้งรายงานการทุจริตได้ กรุณาออกจากระบบ</small>
            `;
            }
        } else {
            statusLabel.className += ' alert-info';
            statusLabel.innerHTML = `
            <i class="fas fa-user me-2"></i>
            <strong>ใช้งานในฐานะผู้เยี่ยมชม</strong><br>
            <small>กรุณากรอกข้อมูลของท่านในฟอร์มด้านล่าง</small>
        `;
        }

        // แทรก label ที่จุดเริ่มต้นของ form section
        formSection.insertBefore(statusLabel, formSection.firstChild);
    }

    // จัดการการเลือกประเภทการทุจริต
    function setupCorruptionTypeSelection() {
        console.log('📋 Setting up corruption type selection...');

        document.querySelectorAll('.corruption-type-card').forEach(card => {
            card.addEventListener('click', function () {
                // ลบการเลือกเดิม
                document.querySelectorAll('.corruption-type-card').forEach(c => c.classList.remove('selected'));

                // เลือกใหม่
                this.classList.add('selected');
                selectedCorruptionType = this.dataset.value;
                document.getElementById('selectedCorruptionType').value = selectedCorruptionType;

                //console.log('✅ Selected corruption type:', selectedCorruptionType);
            });
        });
    }

    // จัดการโหมดไม่ระบุตัวตน
    function toggleAnonymous() {
        const checkbox = document.getElementById('anonymousReport');
        const guestFormSection = document.getElementById('guestFormSection');
        const anonymousNotice = document.getElementById('anonymousNotice');

        console.log('🕵️ Toggle Anonymous:', checkbox.checked);

        if (checkbox.checked) {
            // โหมดไม่ระบุตัวตน - ซ่อนฟิลด์ข้อมูล
            if (guestFormSection) {
                guestFormSection.style.display = 'none';
                const requiredFields = guestFormSection.querySelectorAll('[required], [data-required="true"]');
                requiredFields.forEach(field => {
                    field.removeAttribute('required');
                    field.value = '';
                });
            }

            // แสดง anonymousNotice และเพิ่ม required
            if (anonymousNotice) {
                anonymousNotice.style.display = 'block';
                const relationField = anonymousNotice.querySelector('select[name="anonymous_reporter_relation"]');
                if (relationField) {
                    relationField.setAttribute('required', 'required');
                }
            }
        } else {
            // โหมดระบุตัวตน - แสดงฟิลด์ข้อมูล
            if (anonymousNotice) {
                anonymousNotice.style.display = 'none';
                const relationField = anonymousNotice.querySelector('select[name="anonymous_reporter_relation"]');
                if (relationField) {
                    relationField.removeAttribute('required');
                    relationField.value = '';
                }
            }

            if (guestFormSection) {
                guestFormSection.style.display = 'block';
                // เพิ่ม required กลับให้ fields ที่จำเป็น
                const nameField = guestFormSection.querySelector('input[name="guest_reporter_name"]');
                const phoneField = guestFormSection.querySelector('input[name="guest_reporter_phone"]');
                const relationField = guestFormSection.querySelector('select[name="guest_reporter_relation"]');

                if (nameField) nameField.setAttribute('required', 'required');
                if (phoneField) phoneField.setAttribute('required', 'required');
                if (relationField) relationField.setAttribute('required', 'required');
            }
        }

        // อัพเดท hidden field
        document.getElementById('hiddenAnonymous').value = checkbox.checked ? '1' : '0';
    }

    // จัดการการอัพโหลดไฟล์
    function setupFileUpload() {
        console.log('📎 Setting up file upload...');

        const evidenceFiles = document.getElementById('evidenceFiles');
        if (evidenceFiles) {
            evidenceFiles.addEventListener('change', function (e) {
                handleFileSelect(e.target.files);
            });
        }

        // Drag & Drop
        const uploadArea = document.querySelector('.file-upload-area');
        if (uploadArea) {
            uploadArea.addEventListener('dragover', function (e) {
                e.preventDefault();
                this.classList.add('dragover');
            });

            uploadArea.addEventListener('dragleave', function (e) {
                e.preventDefault();
                this.classList.remove('dragover');
            });

            uploadArea.addEventListener('drop', function (e) {
                e.preventDefault();
                this.classList.remove('dragover');
                handleFileSelect(e.dataTransfer.files);
            });
        }
    }

    function handleFileSelect(files) {
        const fileArray = Array.from(files);

        // ตรวจสอบจำนวนไฟล์ทั้งหมด
        if (selectedFiles.length + fileArray.length > 10) {
            Swal.fire({
                icon: 'warning',
                title: 'เกินจำนวนไฟล์ที่กำหนด',
                text: 'สามารถอัพโหลดได้สูงสุด 10 ไฟล์'
            });
            return;
        }

        // เพิ่มไฟล์ใหม่โดยตรวจสอบความซ้ำ
        fileArray.forEach(file => {
            // ตรวจสอบขนาดไฟล์
            if (file.size > 10 * 1024 * 1024) {
                Swal.fire({
                    icon: 'warning',
                    title: 'ไฟล์ใหญ่เกินไป',
                    text: `ไฟล์ ${file.name} มีขนาดเกิน 10MB`
                });
                return;
            }

            // ตรวจสอบความซ้ำของไฟล์
            const isDuplicate = selectedFiles.some(existingFile => {
                return existingFile.name === file.name &&
                    existingFile.size === file.size &&
                    existingFile.lastModified === file.lastModified;
            });

            if (isDuplicate) {
                return;
            }

            // เพิ่ม unique ID ให้ไฟล์
            file.uniqueId = ++fileIdCounter;
            selectedFiles.push(file);
        });

        // อัปเดตการแสดงผล
        updateFilePreview();

        // ล้างค่า input
        const fileInput = document.getElementById('evidenceFiles');
        if (fileInput) {
            fileInput.value = '';
        }
    }

    function updateFilePreview() {
        const preview = document.getElementById('filePreview');
        const placeholder = document.getElementById('uploadPlaceholder');
        const managementButtons = document.getElementById('fileManagementButtons');

        if (selectedFiles.length === 0) {
            preview.style.display = 'none';
            placeholder.style.display = 'block';
            if (managementButtons) managementButtons.style.display = 'none';
            return;
        }

        placeholder.style.display = 'none';
        preview.style.display = 'block';
        if (managementButtons) managementButtons.style.display = 'flex';

        preview.innerHTML = '';

        selectedFiles.forEach((file, index) => {
            const fileItem = document.createElement('div');
            fileItem.className = 'preview-item';
            fileItem.setAttribute('data-file-id', file.uniqueId || index);

            fileItem.innerHTML = `
            <div class="d-flex align-items-center p-3">
                <div class="file-icon">
                    <i class="fas fa-file${getFileIcon(file.type)}"></i>
                </div>
                <div class="flex-grow-1">
                    <div class="fw-bold file-name">${escapeHtml(file.name)}</div>
                    <small class="text-muted file-size">${formatFileSize(file.size)}</small>
                    <div class="file-type text-muted" style="font-size: 0.75rem;">
                        ${getFileTypeDescription(file.type)}
                    </div>
                </div>
                <div class="file-actions">
                    <button type="button" class="remove-file btn btn-sm btn-outline-danger" 
                            onclick="removeFile(${file.uniqueId || index})" 
                            title="ลบไฟล์">
                        <i class="fas fa-times"></i>
                    </button>
                </div>
            </div>
        `;

            preview.appendChild(fileItem);
        });

        updateFileSummary();
    }

    function updateFileSummary() {
        const totalSize = selectedFiles.reduce((sum, file) => sum + file.size, 0);
        const fileCount = selectedFiles.length;

        let summaryElement = document.getElementById('fileSummary');
        if (!summaryElement) {
            summaryElement = document.createElement('div');
            summaryElement.id = 'fileSummary';
            summaryElement.className = 'file-summary mt-2 p-2 bg-light rounded';

            const preview = document.getElementById('filePreview');
            if (preview && preview.parentNode) {
                preview.parentNode.insertBefore(summaryElement, preview.nextSibling);
            }
        }

        if (fileCount > 0) {
            summaryElement.style.display = 'block';
            summaryElement.innerHTML = `
            <small class="text-muted">
                <i class="fas fa-info-circle me-1"></i>
                จำนวนไฟล์: <strong>${fileCount}</strong> ไฟล์ | 
                ขนาดรวม: <strong>${formatFileSize(totalSize)}</strong> |
                เหลือได้อีก: <strong>${10 - fileCount}</strong> ไฟล์
            </small>
        `;
        } else {
            summaryElement.style.display = 'none';
        }
    }

    function clearAllFiles() {
        if (selectedFiles.length === 0) {
            showToast('ไม่มีไฟล์ที่จะลบ', 'info');
            return;
        }

        Swal.fire({
            title: 'ต้องการลบไฟล์ทั้งหมด?',
            text: `จะลบไฟล์ ${selectedFiles.length} ไฟล์`,
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#dc3545',
            cancelButtonColor: '#6c757d',
            confirmButtonText: 'ลบทั้งหมด',
            cancelButtonText: 'ยกเลิก'
        }).then((result) => {
            if (result.isConfirmed) {
                selectedFiles = [];
                fileIdCounter = 0;
                updateFilePreview();
                showToast('ลบไฟล์ทั้งหมดเรียบร้อยแล้ว', 'success');
            }
        });
    }

    function debugFiles() {
        console.log('=== Debug File Information ===');
        console.log('Total files:', selectedFiles.length);
        console.log('File counter:', fileIdCounter);

        selectedFiles.forEach((file, index) => {
            console.log(`File ${index}:`, {
                name: file.name,
                size: file.size,
                type: file.type,
                uniqueId: file.uniqueId,
                lastModified: new Date(file.lastModified).toLocaleString()
            });
        });

        return selectedFiles;
    }

    function removeFile(fileId) {
        const fileIndex = selectedFiles.findIndex(file =>
            (file.uniqueId && file.uniqueId === fileId) ||
            selectedFiles.indexOf(file) === fileId
        );

        if (fileIndex !== -1) {
            const removedFile = selectedFiles.splice(fileIndex, 1)[0];
            updateFilePreview();
            showToast(`ลบไฟล์ "${removedFile.name}" เรียบร้อยแล้ว`, 'success');
        }
    }

    function escapeHtml(text) {
        const div = document.createElement('div');
        div.textContent = text;
        return div.innerHTML;
    }

    function getFileIcon(fileType) {
        if (fileType.startsWith('image/')) return '-image';
        if (fileType.includes('pdf')) return '-pdf';
        if (fileType.includes('word') || fileType.includes('msword')) return '-word';
        if (fileType.includes('excel') || fileType.includes('sheet')) return '-excel';
        if (fileType.includes('powerpoint') || fileType.includes('presentation')) return '-powerpoint';
        if (fileType.includes('text/')) return '-alt';
        return '';
    }

    function getFileTypeDescription(fileType) {
        if (fileType.startsWith('image/')) return 'รูปภาพ';
        if (fileType.includes('pdf')) return 'เอกสาร PDF';
        if (fileType.includes('word') || fileType.includes('msword')) return 'เอกสาร Word';
        if (fileType.includes('excel') || fileType.includes('sheet')) return 'เอกสาร Excel';
        if (fileType.includes('powerpoint') || fileType.includes('presentation')) return 'เอกสาร PowerPoint';
        if (fileType.includes('text/')) return 'ไฟล์ข้อความ';
        return 'ไฟล์อื่นๆ';
    }

    function formatFileSize(bytes) {
        if (bytes === 0) return '0 Bytes';
        const k = 1024;
        const sizes = ['Bytes', 'KB', 'MB', 'GB'];
        const i = Math.floor(Math.log(bytes) / Math.log(k));
        return parseFloat((bytes / Math.pow(k, i)).toFixed(2)) + ' ' + sizes[i];
    }

    function showToast(message, type = 'info', duration = 3000) {
        const existingToast = document.querySelector('.file-toast');
        if (existingToast) {
            existingToast.remove();
        }

        const toast = document.createElement('div');
        toast.className = `file-toast alert alert-${type === 'success' ? 'success' : 'info'} alert-dismissible`;
        toast.style.cssText = `
        position: fixed;
        top: 20px;
        right: 20px;
        z-index: 9999;
        min-width: 300px;
        animation: slideInRight 0.3s ease-out;
    `;

        toast.innerHTML = `
        <i class="fas fa-${type === 'success' ? 'check-circle' : 'info-circle'} me-2"></i>
        ${message}
        <button type="button" class="btn-close" onclick="this.parentElement.remove()"></button>
    `;

        document.body.appendChild(toast);

        setTimeout(() => {
            if (toast.parentNode) {
                toast.style.animation = 'slideOutRight 0.3s ease-in';
                setTimeout(() => toast.remove(), 300);
            }
        }, duration);
    }

    // การตรวจสอบเบอร์โทรศัพท์
    function validatePhoneNumber(phone) {
        if (!phone || phone.trim() === '') {
            return { valid: false, message: 'กรุณากรอกเบอร์โทรศัพท์' };
        }

        const cleanedPhone = phone.replace(/[^0-9+]/g, '');

        if (!/^[\+]?[0-9]+$/.test(cleanedPhone)) {
            return { valid: false, message: 'เบอร์โทรศัพท์ต้องเป็นตัวเลขเท่านั้น' };
        }

        const numbersOnly = cleanedPhone.replace(/^\+/, '');

        if (numbersOnly.length < 9) {
            return { valid: false, message: 'เบอร์โทรศัพท์สั้นเกินไป (ต้องมีอย่างน้อย 9 หลัก)' };
        }

        if (numbersOnly.length > 15) {
            return { valid: false, message: 'เบอร์โทรศัพท์ยาวเกินไป (ไม่เกิน 15 หลัก)' };
        }

        // ตรวจสอบรูปแบบเบอร์โทรไทย
        if (numbersOnly.length === 10) {
            if (/^0[689][0-9]{8}$/.test(numbersOnly)) {
                return {
                    valid: true,
                    type: 'mobile',
                    cleaned: numbersOnly,
                    formatted: numbersOnly.replace(/(\d{3})(\d{3})(\d{4})/, '$1-$2-$3')
                };
            }

            if (/^0[2-7][0-9]{8}$/.test(numbersOnly)) {
                return {
                    valid: true,
                    type: 'landline',
                    cleaned: numbersOnly,
                    formatted: numbersOnly.replace(/(\d{2})(\d{3})(\d{4})/, '$1-$2-$3')
                };
            }
        }

        if (numbersOnly.length === 9 && /^0[2-7][0-9]{7}$/.test(numbersOnly)) {
            return {
                valid: true,
                type: 'landline',
                cleaned: numbersOnly,
                formatted: numbersOnly.replace(/(\d{2})(\d{3})(\d{3})/, '$1-$2-$3')
            };
        }

        if (numbersOnly.length >= 9) {
            return {
                valid: true,
                type: 'international',
                cleaned: numbersOnly,
                warning: 'รูปแบบเบอร์โทรศัพท์ไม่ใช่รูปแบบไทย'
            };
        }

        return { valid: false, message: 'รูปแบบเบอร์โทรศัพท์ไม่ถูกต้อง' };
    }

    function setupPhoneValidation() {
        const phoneInputs = [
            document.getElementById('guestReporterPhone'),
            document.querySelector('input[name="guest_reporter_phone"]')
        ].filter(input => input !== null);

        phoneInputs.forEach(input => {
            input.setAttribute('pattern', '[0-9+\\-\\s]+');
            input.setAttribute('title', 'เบอร์โทรศัพท์ (เช่น 0812345678)');

            input.addEventListener('input', function () {
                const phone = this.value.trim();
                if (phone.length > 0) {
                    const result = validatePhoneNumber(phone);
                    showPhoneValidationResult(this, result);
                } else {
                    this.classList.remove('is-valid', 'is-invalid');
                    const feedback = this.parentNode.querySelector('.invalid-feedback, .valid-feedback');
                    if (feedback) {
                        feedback.remove();
                    }
                }
            });

            input.addEventListener('keypress', function (e) {
                const allowedChars = /[0-9+\-\s]/;
                const char = String.fromCharCode(e.which);

                if (!allowedChars.test(char) && e.which !== 8 && e.which !== 0) {
                    e.preventDefault();
                }
            });
        });
    }

    function showPhoneValidationResult(inputElement, result) {
        inputElement.classList.remove('is-valid', 'is-invalid');
        const existingFeedback = inputElement.parentNode.querySelector('.invalid-feedback, .valid-feedback');
        if (existingFeedback) {
            existingFeedback.remove();
        }

        const feedback = document.createElement('div');
        feedback.className = result.valid ? 'valid-feedback' : 'invalid-feedback';
        feedback.style.display = 'block';

        if (result.valid) {
            inputElement.classList.add('is-valid');
            let message = '✓ เบอร์โทรศัพท์ถูกต้อง';
            if (result.type === 'mobile') {
                message += ' (มือถือ)';
            } else if (result.type === 'landline') {
                message += ' (โทรศัพท์บ้าน)';
            } else if (result.type === 'international') {
                message += ' (ต่างประเทศ)';
            }
            if (result.formatted) {
                message += ` - ${result.formatted}`;
            }
            feedback.textContent = message;
            feedback.style.color = '#28a745';
        } else {
            inputElement.classList.add('is-invalid');
            feedback.textContent = result.message;
            feedback.style.color = '#dc3545';
        }

        if (result.warning) {
            const warning = document.createElement('small');
            warning.className = 'text-warning';
            warning.textContent = result.warning;
            warning.style.display = 'block';
            feedback.appendChild(document.createElement('br'));
            feedback.appendChild(warning);
        }

        inputElement.parentNode.appendChild(feedback);
    }

    // ส่งรายงาน
    async function submitCorruptionReport() {
        console.log('📝 Submitting corruption report...');

        // *** ใช้ข้อมูลจาก global state แทนการ hardcode ***
        const state = window.corruptionFormState || {};
        const isLoggedIn = state.isUserLoggedIn || false;
        const userType = state.userType || 'guest';
        const accessDenied = state.accessDenied || false;
        let hasConfirmedAsGuest = state.hasConfirmedAsGuest || false;

        console.log('Submit check - State:', {
            isLoggedIn,
            userType,
            accessDenied,
            hasConfirmedAsGuest
        });

        // *** ตรวจสอบว่าเป็น staff หรือ access denied หรือไม่ ***
        if (accessDenied || userType === 'staff') {
            console.log('❌ Access denied for staff user');
            Swal.fire({
                icon: 'error',
                title: 'ไม่สามารถดำเนินการได้',
                text: 'ท่านเข้าสู่ระบบในฐานะเจ้าหน้าที่ ไม่สามารถแจ้งรายงานการทุจริตได้',
                confirmButtonText: 'ออกจากระบบ',
                confirmButtonColor: '#dc3545'
            }).then((result) => {
                if (result.isConfirmed) {
                    window.location.href = '/User/logout';
                }
            });
            return;
        }

        // *** แก้ไขเงื่อนไขการตรวจสอบ guest confirmation ***
        // ถ้า login แล้วไม่ต้องเช็ค guest confirmation
        if (!isLoggedIn && !hasConfirmedAsGuest) {
            console.log('⚠️ User has not confirmed as guest, showing modal...');
            showModal();
            return;
        }

        // *** ดำเนินการส่งฟอร์มต่อ (เหมือนเดิม) ***

        // ตรวจสอบการเลือกประเภทการทุจริต
        if (!selectedCorruptionType) {
            Swal.fire({
                icon: 'warning',
                title: 'กรุณาเลือกประเภทการทุจริต',
                text: 'กรุณาเลือกประเภทการทุจริตที่ต้องการแจ้ง'
            });
            return;
        }

        // ตรวจสอบความสัมพันธ์กับเหตุการณ์
        const isAnonymous = document.getElementById('anonymousReport').checked;
        let reporterRelationValue = '';

        if (isAnonymous) {
            const anonymousNotice = document.getElementById('anonymousNotice');
            if (anonymousNotice && anonymousNotice.style.display !== 'none') {
                const anonymousRelation = anonymousNotice.querySelector('select[name="anonymous_reporter_relation"]');
                if (anonymousRelation && anonymousRelation.value) {
                    reporterRelationValue = anonymousRelation.value;
                }
            }
        } else {
            const guestFormSection = document.getElementById('guestFormSection');
            if (guestFormSection && guestFormSection.style.display !== 'none') {
                const guestRelation = guestFormSection.querySelector('select[name="guest_reporter_relation"]');
                if (guestRelation && guestRelation.value) {
                    reporterRelationValue = guestRelation.value;
                }
            }
        }

        if (!reporterRelationValue) {
            Swal.fire({
                icon: 'warning',
                title: 'กรุณาเลือกความสัมพันธ์กับเหตุการณ์',
                text: 'ข้อมูลนี้จำเป็นสำหรับการประเมินและดำเนินการ'
            });
            return;
        }

        // ตรวจสอบเบอร์โทรศัพท์ (เฉพาะเมื่อไม่ใช่ anonymous)
        if (!isAnonymous) {
            const guestFormSection = document.getElementById('guestFormSection');
            if (guestFormSection && guestFormSection.style.display !== 'none') {
                const guestPhone = guestFormSection.querySelector('input[name="guest_reporter_phone"]');
                if (guestPhone) {
                    const phoneToCheck = guestPhone.value.trim();
                    if (phoneToCheck) {
                        const phoneValidation = validatePhoneNumber(phoneToCheck);
                        if (!phoneValidation.valid) {
                            Swal.fire({
                                icon: 'error',
                                title: 'เบอร์โทรศัพท์ไม่ถูกต้อง',
                                text: `เบอร์โทรศัพท์: ${phoneValidation.message}`
                            });
                            return;
                        }
                    }
                }
            }
        }

        // ตรวจสอบ form validation
        const form = document.getElementById('corruptionForm');

        // ลบ required จาก field ที่ซ่อนอยู่ชั่วคราว
        const hiddenRequiredFields = [];
        const allRequiredFields = form.querySelectorAll('[required]');

        allRequiredFields.forEach(field => {
            const fieldContainer = field.closest('#guestFormSection, #anonymousNotice');
            if (fieldContainer && (fieldContainer.style.display === 'none' ||
                getComputedStyle(fieldContainer).display === 'none')) {
                hiddenRequiredFields.push(field);
                field.removeAttribute('required');
            }
        });

        // ตรวจสอบ form
        const isValid = form.checkValidity();

        // คืน required กลับให้ field ที่ซ่อน
        hiddenRequiredFields.forEach(field => {
            field.setAttribute('required', 'required');
        });

        if (!isValid) {
            form.reportValidity();
            return;
        }

        // *** เพิ่ม: ตรวจสอบและสร้าง reCAPTCHA Token ก่อนแสดงการยืนยัน ***
        console.log('🔐 Checking reCAPTCHA status for corruption report...');
        const recaptchaStatus = checkRecaptchaStatus();
        let recaptchaToken = null;

        // *** สร้าง reCAPTCHA Token หากพร้อมใช้งาน ***
        if (recaptchaStatus.ready && recaptchaStatus.siteKey) {
            try {
                console.log('🔄 Generating reCAPTCHA token for corruption report...');
                recaptchaToken = await generateRecaptchaToken('corruption_report_submit');
                console.log('✅ reCAPTCHA token generated for corruption report');
            } catch (error) {
                console.error('❌ reCAPTCHA token generation failed:', error);

                // แสดงข้อผิดพลาด reCAPTCHA
                Swal.fire({
                    icon: 'error',
                    title: 'ไม่สามารถยืนยันตัวตนได้',
                    html: `
                        <div style="text-align: center;">
                            <p style="margin-bottom: 1rem; color: #721c24;">การยืนยันความปลอดภัยล้มเหลว</p>
                            <div style="background: #f8f9fa; padding: 1rem; border-radius: 8px; margin: 1rem 0; border-left: 4px solid #dc3545;">
                                <small style="color: #721c24;">
                                    <i class="fas fa-shield-alt"></i> 
                                    กรุณารีเฟรชหน้าและลองใหม่อีกครั้ง
                                </small>
                            </div>
                        </div>
                    `,
                    confirmButtonColor: '#dc3545',
                    confirmButtonText: 'รีเฟรชหน้า',
                    showCancelButton: true,
                    cancelButtonText: 'ยกเลิก',
                    cancelButtonColor: '#6c757d'
                }).then((result) => {
                    if (result.isConfirmed) {
                        window.location.reload();
                    }
                });
                return;
            }
        } else if (!recaptchaStatus.skipForDev) {
            console.warn('⚠️ reCAPTCHA not ready for corruption report - proceeding without verification');
        }

        // *** แสดงการยืนยันพร้อมข้อมูล reCAPTCHA status ***
        const recaptchaStatusText = recaptchaToken ?
            '<small style="color: #28a745;"><i class="fas fa-shield-check"></i> ระบบรักษาความปลอดภัย: เปิดใช้งาน</small>' :
            '<small style="color: #6c757d;"><i class="fas fa-shield"></i> ระบบรักษาความปลอดภัย: โหมดทดสอบ</small>';

        Swal.fire({
            title: 'ยืนยันการส่งรายงาน',
            html: `
                <div style="text-align: center;">
                    <p style="margin-bottom: 1rem;">คุณแน่ใจหรือไม่ที่จะส่งรายงานการทุจริตนี้?</p>
                    <div style="background: #f8f9fa; padding: 1rem; border-radius: 8px; margin: 1rem 0;">
                        ${recaptchaStatusText}
                    </div>
                    <div style="background: #fff3cd; padding: 1rem; border-radius: 8px; margin-top: 1rem; border-left: 4px solid #ffc107;">
                        <small style="color: #856404;">
                            <i class="fas fa-info-circle"></i> 
                            <strong>หมายเหตุ:</strong> รายงานการทุจริตจะถูกส่งไปยังหน่วยงานที่เกี่ยวข้องเพื่อดำเนินการตรวจสอบ
                        </small>
                    </div>
                </div>
            `,
            icon: 'question',
            showCancelButton: true,
            confirmButtonColor: '#dc3545',
            cancelButtonColor: '#6c757d',
            confirmButtonText: 'ส่งรายงาน',
            cancelButtonText: 'ยกเลิก',
            allowOutsideClick: false,
            allowEscapeKey: true
        }).then((result) => {
            if (result.isConfirmed) {
                // *** ส่ง reCAPTCHA token ไปยัง processSubmission ***
                processSubmission(recaptchaToken);
            }
        });
    }

    async function processSubmission(recaptchaToken = null) {
        console.log('🚀 Processing corruption report submission...');
        console.log('🔐 reCAPTCHA Token received:', recaptchaToken ? 'YES' : 'NO');

        try {
            const form = document.getElementById('corruptionForm');
            if (!form) {
                throw new Error('Corruption form not found');
            }

            // *** แสดง loading state ***
            Swal.fire({
                title: 'กำลังส่งรายงาน...',
                html: 'กรุณารอสักครู่',
                allowOutsideClick: false,
                allowEscapeKey: false,
                didOpen: () => {
                    Swal.showLoading();
                }
            });

            // *** สร้าง FormData จากฟอร์ม ***
            const formData = new FormData(form);

            // *** เพิ่ม reCAPTCHA Token และข้อมูลที่เกี่ยวข้อง ***
            if (recaptchaToken) {
                formData.append('g-recaptcha-response', recaptchaToken);
                formData.append('recaptcha_action', 'corruption_report_submit');
                formData.append('recaptcha_source', 'corruption_form');
                console.log('🔐 Added reCAPTCHA token to FormData');
            }

            // *** เพิ่มข้อมูลเพิ่มเติมสำหรับ reCAPTCHA verification ***
            formData.append('client_timestamp', Date.now());
            formData.append('user_agent_info', navigator.userAgent);

            // ตรวจสอบโหมด development
            const isDevelopment = window.SKIP_RECAPTCHA_FOR_DEV || false;
            if (isDevelopment) {
                formData.append('dev_mode', '1');
                console.log('🔧 Development mode enabled');
            }

            // *** เพิ่มข้อมูลสถานะผู้ใช้ ***
            const state = window.corruptionFormState || {};
            const userTypeDetected = state.isUserLoggedIn ? (state.userType || 'citizen') : 'guest';
            formData.append('user_type_detected', userTypeDetected);

            // *** เพิ่มข้อมูลประเภทการทุจริต ***
            if (selectedCorruptionType) {
                formData.append('corruption_type', selectedCorruptionType);
                console.log('📋 Added corruption type:', selectedCorruptionType);
            }

            // *** จัดการโหมด Anonymous ***
            const isAnonymous = document.getElementById('anonymousReport').checked;
            formData.append('is_anonymous', isAnonymous ? '1' : '0');

            if (isAnonymous) {
                formData.append('anonymous_mode', 'true');
                console.log('🕵️ Anonymous mode enabled');
            }

            // *** เพิ่มข้อมูลความสัมพันธ์ที่ถูกต้อง ***
            let reporterRelationValue = '';

            if (isAnonymous) {
                const anonymousRelation = document.querySelector('select[name="anonymous_reporter_relation"]');
                if (anonymousRelation && anonymousRelation.value) {
                    reporterRelationValue = anonymousRelation.value;
                }
            } else {
                const guestRelation = document.querySelector('select[name="guest_reporter_relation"]');
                if (guestRelation && guestRelation.value) {
                    reporterRelationValue = guestRelation.value;
                }
            }

            if (reporterRelationValue) {
                formData.append('reporter_relation', reporterRelationValue);
                console.log('👥 Added reporter relation:', reporterRelationValue);
            }

            // *** จัดการข้อมูลสำหรับโหมดไม่ระบุตัวตน ***
            if (isAnonymous) {
                // ลบข้อมูลผู้แจ้งที่อาจจะมีอยู่
                formData.delete('guest_reporter_name');
                formData.delete('guest_reporter_phone');
                formData.delete('guest_reporter_email');

                // เพิ่มข้อมูลเริ่มต้นสำหรับไม่ระบุตัวตน
                formData.append('reporter_name', 'ไม่ระบุตัวตน');
                formData.append('reporter_phone', '00000');
                console.log('🔒 Set anonymous reporter data');
            }

            // *** จัดการไฟล์หลักฐาน ***
            let fileCount = 0;

            // ใช้ selectedFiles global variable ถ้ามี
            if (typeof selectedFiles !== 'undefined' && selectedFiles && selectedFiles.length > 0) {
                selectedFiles.forEach((file, index) => {
                    if (file instanceof File) {
                        formData.append('evidence_files[]', file, file.name);
                        fileCount++;
                    }
                });
                console.log('📎 Added files from selectedFiles:', fileCount);
            } else {
                // Fallback: ดึงจาก file input ใน form
                const fileInputs = form.querySelectorAll('input[type="file"]');
                fileInputs.forEach((fileInput) => {
                    if (fileInput.files && fileInput.files.length > 0) {
                        Array.from(fileInput.files).forEach((file) => {
                            formData.append('evidence_files[]', file, file.name);
                            fileCount++;
                        });
                    }
                });
                console.log('📎 Added files from form inputs:', fileCount);
            }

            // *** Debug FormData ***
            console.log('🔍 Final FormData Contents:');
            for (let pair of formData.entries()) {
                if (pair[1] instanceof File) {
                    console.log(`📎 ${pair[0]}: ${pair[1].name} (${(pair[1].size / 1024).toFixed(2)} KB)`);
                } else if (pair[0].includes('recaptcha') || pair[0].includes('token')) {
                    console.log(`🔐 ${pair[0]}: [PROTECTED]`);
                } else {
                    console.log(`📝 ${pair[0]}: ${pair[1]}`);
                }
            }

            // *** ส่งข้อมูลไปยัง server ***
            const response = await fetch('<?php echo site_url("Corruption/submit_report"); ?>', {
                method: 'POST',
                body: formData,
                headers: {
                    'X-Requested-With': 'XMLHttpRequest'
                },
                signal: AbortSignal.timeout(45000) // 45 seconds timeout
            });

            console.log('📊 Response status:', response.status);
            console.log('📊 Response ok:', response.ok);

            if (!response.ok) {
                throw new Error(`Network response was not ok: ${response.status}`);
            }

            const responseText = await response.text();
            console.log('📦 Raw response (first 300 chars):', responseText.substring(0, 300));

            // *** จัดการ response ***
            let jsonResponse;
            try {
                // หาจุดเริ่มต้นของ JSON
                const jsonStart = responseText.indexOf('{');
                const jsonEnd = responseText.lastIndexOf('}') + 1;

                if (jsonStart !== -1 && jsonEnd > jsonStart) {
                    const jsonString = responseText.substring(jsonStart, jsonEnd);
                    jsonResponse = JSON.parse(jsonString);
                    console.log('✅ JSON parsed successfully:', jsonResponse);
                } else {
                    // ไม่มี JSON - ถือว่าสำเร็จถ้า response ok
                    if (response.ok) {
                        jsonResponse = {
                            success: true,
                            message: 'ส่งรายงานสำเร็จ',
                            report_id: 'N/A'
                        };
                    } else {
                        throw new Error('No valid JSON found in response');
                    }
                }
            } catch (jsonError) {
                console.error('❌ JSON parse error:', jsonError.message);

                // ตรวจสอบ response ว่ามีคำว่า success หรือไม่
                if (responseText.includes('success') || responseText.includes('เรียบร้อย') || response.ok) {
                    jsonResponse = {
                        success: true,
                        message: 'ส่งรายงานสำเร็จ',
                        report_id: 'AUTO-' + Date.now()
                    };
                } else {
                    throw new Error('Invalid server response: ' + jsonError.message);
                }
            }

            // *** จัดการ JSON response ***
            if (jsonResponse.success === false) {
                // จัดการ error types ต่างๆ
                const errorType = jsonResponse.error_type || jsonResponse.error_code || 'unknown';
                let errorMessage = jsonResponse.message || 'เกิดข้อผิดพลาดในการส่งรายงาน';

                // จัดการข้อผิดพลาด reCAPTCHA สำหรับ corruption report
                if (errorType === 'recaptcha_missing' || errorType === 'RECAPTCHA_MISSING') {
                    errorMessage = 'ไม่พบข้อมูลการยืนยันตัวตน กรุณารีเฟรชหน้าและลองใหม่อีกครั้ง';
                } else if (errorType === 'recaptcha_failed' || errorType === 'RECAPTCHA_FAILED') {
                    errorMessage = 'การยืนยันตัวตนไม่ผ่าน กรุณาลองใหม่อีกครั้ง';
                } else if (errorType === 'vulgar_content') {
                    errorMessage = 'พบคำไม่เหมาะสมในรายงาน กรุณาแก้ไขข้อความ';
                } else if (errorType === 'url_content') {
                    errorMessage = 'ไม่อนุญาตให้มี URL หรือลิงก์ในรายงาน';
                } else if (errorType === 'access_denied' || errorType === 'STAFF_ACCESS_DENIED') {
                    errorMessage = 'ไม่มีสิทธิ์ในการส่งรายงานการทุจริต กรุณาออกจากระบบ login บุคลากรภายใน';
                }

                console.error('❌ Server error:', errorType, errorMessage);

                Swal.fire({
                    icon: 'error',
                    title: 'ไม่สามารถส่งรายงานได้',
                    html: `
                        <div style="text-align: center;">
                            <p style="margin-bottom: 1rem; color: #721c24;">${errorMessage}</p>
                            <div style="background: #f8f9fa; padding: 1rem; border-radius: 8px; margin: 1rem 0; border-left: 4px solid #dc3545;">
                                <small style="color: #721c24;">
                                    <i class="fas fa-exclamation-triangle"></i> 
                                    กรุณาตรวจสอบข้อมูลและลองใหม่อีกครั้ง
                                </small>
                            </div>
                            ${errorType !== 'unknown' ?
                            `<div style="margin-top: 1rem;"><small style="color: #6c757d;">รหัสข้อผิดพลาด: ${errorType}</small></div>` :
                            ''
                        }
                        </div>
                    `,
                    confirmButtonColor: '#dc3545',
                    confirmButtonText: 'ลองใหม่'
                });

                return;
            }

            // *** Success Case ***
            console.log('🎉 Submission successful!');

            // ตรวจสอบประเภทผู้ใช้สำหรับ redirect
            const userState = window.corruptionFormState || {};
            const isLoggedIn = userState.isUserLoggedIn || jsonResponse.is_logged_in || false;
            const userType = userState.userType || jsonResponse.user_type || 'guest';
            const reportId = jsonResponse.report_id || jsonResponse.corruption_id || 'AUTO-' + Date.now();
            const filesUploaded = jsonResponse.files_uploaded || fileCount || 0;

            console.log('📊 Success details:', {
                isLoggedIn,
                userType,
                reportId,
                filesUploaded,
                recaptchaVerified: jsonResponse.recaptcha_verified
            });

            // *** แสดงข้อความสำเร็จ ***
            Swal.fire({
                icon: 'success',
                title: 'ส่งรายงานการทุจริตสำเร็จ!',
                html: `
                    <div style="text-align: center;">
                        <div style="margin-bottom: 1.5rem;">
                            <div style="width: 80px; height: 80px; margin: 0 auto 1rem; background: linear-gradient(135deg, #dc3545 0%, #c82333 100%); border-radius: 50%; display: flex; align-items: center; justify-content: center; box-shadow: 0 8px 25px rgba(220, 53, 69, 0.3);">
                                <i class="fas fa-shield-alt" style="font-size: 2.5rem; color: white;"></i>
                            </div>
                            <p style="font-size: 1.2rem; margin: 0; color: #2c3e50; font-weight: 600;">
                                รายงานการทุจริตถูกส่งเรียบร้อยแล้ว
                            </p>
                        </div>
                        
                        <div style="background: linear-gradient(135deg, #f8d7da 0%, #f5c6cb 100%); 
                                    padding: 1.5rem; 
                                    border-radius: 15px; 
                                    border: 2px solid #dc3545;
                                    margin: 1rem 0;
                                    box-shadow: 0 4px 15px rgba(220, 53, 69, 0.2);">
                            <div style="font-size: 1.1rem; color: #721c24; margin-bottom: 0.5rem;">
                                <i class="fas fa-clipboard-check" style="color: #dc3545; margin-right: 0.5rem;"></i>
                                <strong>หมายเลขรายงาน</strong>
                            </div>
                            <div style="font-size: 2rem; font-weight: bold; color: #721c24; margin: 0.5rem 0; font-family: 'Courier New', monospace; letter-spacing: 2px; text-shadow: 1px 1px 2px rgba(0,0,0,0.1);">
                                ${reportId}
                            </div>
                            <small style="color: #721c24; display: block; margin-bottom: 1rem;">
                                <i class="fas fa-bookmark"></i> กรุณาเก็บหมายเลขนี้ไว้สำหรับติดตามสถานะ
                            </small>
                        </div>
                        
                        ${isAnonymous ?
                        '<div style="background: linear-gradient(135deg, #f8f9fa 0%, #e9ecef 100%); padding: 0.8rem; border-radius: 8px; margin: 1rem 0; border-left: 4px solid #6c757d;"><p style="color: #6c757d; margin: 0; font-size: 0.9rem;"><i class="fas fa-user-secret"></i> รายงานแบบไม่ระบุตัวตน</p></div>' :
                        ''
                    }
                        
                        <div style="background: #d4edda; padding: 1rem; border-radius: 8px; margin: 1rem 0; border-left: 4px solid #28a745;">
                            <p style="color: #155724; margin: 0; font-size: 0.9rem;">
                                <i class="fas fa-check-circle"></i> 
                                อัพโหลดไฟล์สำเร็จ ${filesUploaded} ไฟล์
                            </p>
                        </div>
                        
                        ${jsonResponse.recaptcha_verified ?
                        '<div style="background: #d1ecf1; padding: 0.8rem; border-radius: 8px; margin: 1rem 0; border-left: 4px solid #17a2b8;"><p style="color: #0c5460; margin: 0; font-size: 0.9rem;"><i class="fas fa-shield-check"></i> ยืนยันความปลอดภัยด้วย reCAPTCHA</p></div>' :
                        ''
                    }
                        
                        <div style="background: linear-gradient(135deg, #fff3cd 0%, #ffeaa7 100%); 
                                    padding: 1rem; 
                                    border-radius: 10px; 
                                    border: 1px solid #ffc107; 
                                    margin-top: 1rem;">
                            <p style="color: #856404; margin: 0; font-size: 0.95rem;">
                                <i class="fas fa-info-circle" style="margin-right: 0.5rem;"></i>
                                <strong>รายงานจะถูกส่งไปยังหน่วยงานที่เกี่ยวข้อง</strong>
                                <br>
                                <small>เพื่อดำเนินการตรวจสอบและดำเนินการตามกระบวนการที่กำหนด</small>
                            </p>
                        </div>
                        
                        ${getRedirectMessage(isLoggedIn, userType)}
                    </div>
                `,
                confirmButtonText: 'รับทราบ',
                confirmButtonColor: '#28a745',
                width: '600px',
                allowOutsideClick: false,
                allowEscapeKey: false
            }).then(() => {
                // รีเซ็ตฟอร์ม
                if (typeof resetForm === 'function') {
                    resetForm();
                } else if (typeof resetCorruptionForm === 'function') {
                    resetCorruptionForm();
                }

                // Redirect ตามประเภทผู้ใช้
                redirectAfterSuccess(isLoggedIn, userType, reportId);
            });

        } catch (error) {
            console.error('❌ Submission error:', error);

            let errorMessage = 'ไม่สามารถส่งรายงานการทุจริตได้ กรุณาลองใหม่อีกครั้ง';
            let errorIcon = 'error';

            if (error.name === 'AbortError') {
                errorMessage = 'การส่งรายงานใช้เวลานานเกินไป กรุณาตรวจสอบการเชื่อมต่ออินเทอร์เน็ต';
                errorIcon = 'warning';
            } else if (error.message.includes('Failed to fetch')) {
                errorMessage = 'ไม่สามารถเชื่อมต่อกับเซิร์ฟเวอร์ได้ กรุณาตรวจสอบการเชื่อมต่ออินเทอร์เน็ต';
                errorIcon = 'warning';
            } else if (error.message.includes('Network response was not ok')) {
                errorMessage = 'เซิร์ฟเวอร์ตอบสนองผิดปกติ กรุณาลองใหม่อีกครั้ง';
                errorIcon = 'error';
            }

            Swal.fire({
                icon: errorIcon,
                title: 'เกิดข้อผิดพลาด',
                html: `
                    <div style="text-align: center;">
                        <p style="margin-bottom: 1rem;">${errorMessage}</p>
                        <div style="background: #f8f9fa; padding: 1rem; border-radius: 8px; margin: 1rem 0; border-left: 4px solid #dc3545;">
                            <small style="color: #721c24;">
                                <i class="fas fa-exclamation-triangle"></i> 
                                กรุณาตรวจสอบการเชื่อมต่ออินเทอร์เน็ต และลองใหม่อีกครั้ง
                            </small>
                        </div>
                    </div>
                `,
                confirmButtonText: 'ลองใหม่',
                confirmButtonColor: '#dc3545'
            });
        }
    }


    // *** ฟังก์ชันแสดงข้อความสำเร็จสำหรับ corruption report ***
    function showCorruptionSuccessMessage(responseData) {
        const reportId = responseData?.report_id || 'ไม่ระบุ';
        const isAnonymous = responseData?.is_anonymous || document.getElementById('anonymousReport').checked;

        Swal.fire({
            icon: 'success',
            title: 'ส่งรายงานการทุจริตสำเร็จ!',
            html: `
                <div style="text-align: center;">
                    <div style="margin-bottom: 1.5rem;">
                        <div style="width: 80px; height: 80px; margin: 0 auto 1rem; background: linear-gradient(135deg, #dc3545 0%, #c82333 100%); border-radius: 50%; display: flex; align-items: center; justify-content: center; box-shadow: 0 8px 25px rgba(220, 53, 69, 0.3);">
                            <i class="fas fa-shield-alt" style="font-size: 2.5rem; color: white;"></i>
                        </div>
                        <p style="font-size: 1.2rem; margin: 0; color: #2c3e50; font-weight: 600;">
                            รายงานการทุจริตถูกส่งเรียบร้อยแล้ว
                        </p>
                    </div>
                    
                    <div style="background: linear-gradient(135deg, #f8d7da 0%, #f5c6cb 100%); 
                                padding: 1.5rem; 
                                border-radius: 15px; 
                                border: 2px solid #dc3545;
                                margin: 1rem 0;
                                box-shadow: 0 4px 15px rgba(220, 53, 69, 0.2);">
                        <div style="font-size: 1.1rem; color: #721c24; margin-bottom: 0.5rem;">
                            <i class="fas fa-clipboard-check" style="color: #dc3545; margin-right: 0.5rem;"></i>
                            <strong>หมายเลขรายงาน</strong>
                        </div>
                        <div style="font-size: 2rem; font-weight: bold; color: #721c24; margin: 0.5rem 0; font-family: 'Courier New', monospace; letter-spacing: 2px; text-shadow: 1px 1px 2px rgba(0,0,0,0.1);">
                            ${reportId}
                        </div>
                        <small style="color: #721c24; display: block; margin-bottom: 1rem;">
                            <i class="fas fa-bookmark"></i> กรุณาเก็บหมายเลขนี้ไว้สำหรับติดตามสถานะ
                        </small>
                    </div>
                    
                    ${isAnonymous ?
                    '<div style="background: linear-gradient(135deg, #f8f9fa 0%, #e9ecef 100%); padding: 0.8rem; border-radius: 8px; margin: 1rem 0; border-left: 4px solid #6c757d;"><p style="color: #6c757d; margin: 0; font-size: 0.9rem;"><i class="fas fa-user-secret"></i> รายงานแบบไม่ระบุตัวตน</p></div>' :
                    ''
                }
                    
                    <div style="background: linear-gradient(135deg, #fff3cd 0%, #ffeaa7 100%); 
                                padding: 1rem; 
                                border-radius: 10px; 
                                border: 1px solid #ffc107; 
                                margin-top: 1rem;">
                        <p style="color: #856404; margin: 0; font-size: 0.95rem;">
                            <i class="fas fa-info-circle" style="margin-right: 0.5rem;"></i>
                            <strong>รายงานจะถูกส่งไปยังหน่วยงานที่เกี่ยวข้อง</strong>
                            <br>
                            <small>เพื่อดำเนินการตรวจสอบและดำเนินการตามกระบวนการที่กำหนด</small>
                        </p>
                    </div>
                </div>
            `,
            showConfirmButton: true,
            confirmButtonText: 'เรียบร้อย',
            confirmButtonColor: '#dc3545',
            allowOutsideClick: true,
            allowEscapeKey: true
        }).then(() => {
            // *** รีเซ็ตฟอร์มหรือนำทางไปหน้าที่เหมาะสม ***
            const state = window.corruptionFormState || {};
            if (state.isUserLoggedIn) {
                // User ที่ login แล้ว - ไปหน้าติดตามสถานะ
                window.location.href = '/corruption/status';
            } else {
                // Guest user - รีเซ็ตฟอร์มสำหรับรายงานใหม่
                if (typeof resetCorruptionForm === 'function') {
                    resetCorruptionForm();
                } else {
                    window.location.href = '/';
                }
            }
        });
    }

    function checkRecaptchaStatus() {
        return {
            ready: window.recaptchaReady || false,
            siteKey: window.RECAPTCHA_SITE_KEY || null,
            skipForDev: window.SKIP_RECAPTCHA_FOR_DEV || false
        };
    }

    async function generateRecaptchaToken(action = 'complain_submit') {
        if (!window.recaptchaReady || !window.RECAPTCHA_SITE_KEY) {
            console.warn('⚠️ reCAPTCHA not ready or site key missing');
            return null;
        }

        try {
            const token = await grecaptcha.execute(window.RECAPTCHA_SITE_KEY, {
                action: action
            });
            console.log('✅ reCAPTCHA token generated for action:', action);
            return token;
        } catch (error) {
            console.error('❌ Error generating reCAPTCHA token:', error);
            return null;
        }
    }

    function redirectAfterSuccess(isLoggedIn, userType, reportId, responseData = {}) {
        try {
            console.log('🚀 Redirecting user after successful submission...');
            console.log('User Info:', { isLoggedIn, userType, reportId });
            console.log('Response Data:', responseData);

            // *** เก็บข้อมูลใน sessionStorage สำหรับการใช้งานภายหลัง ***
            if (reportId) {
                sessionStorage.setItem('last_submitted_report', reportId);
                sessionStorage.setItem('submission_timestamp', Date.now().toString());

                // *** เพิ่ม: เก็บข้อมูล reCAPTCHA verification ***
                if (responseData.recaptcha_verified !== undefined) {
                    sessionStorage.setItem('last_report_recaptcha_verified', responseData.recaptcha_verified ? '1' : '0');
                }

                if (responseData.verification_method) {
                    sessionStorage.setItem('last_report_verification_method', responseData.verification_method);
                }

                // *** เก็บจำนวนไฟล์ที่อัปโหลด ***
                if (responseData.files_uploaded !== undefined) {
                    sessionStorage.setItem('last_report_files_count', responseData.files_uploaded.toString());
                }

                console.log('💾 Saved submission data to sessionStorage');
            }

            if (isLoggedIn && userType === 'public') {
                // *** สมาชิกที่ login แล้ว -> ไปหน้ารายงานของฉัน ***
                console.log('✅ Redirecting logged-in public user to my_reports');

                // แสดงข้อความพร้อมข้อมูล reCAPTCHA
                const verificationText = responseData.recaptcha_verified ?
                    ' (ยืนยันความปลอดภัยแล้ว)' : '';

                showRedirectMessage(`กำลังนำคุณไปยังหน้ารายงานของคุณ${verificationText}...`, 'success');

                setTimeout(() => {
                    window.location.href = '<?php echo site_url("Corruption/my_reports"); ?>';
                }, 1500);

            } else {
                // *** Guest หรือ Anonymous -> ไปหน้าติดตามสถานะพร้อมหมายเลข ***
                console.log('✅ Redirecting guest/anonymous user to track_status with report ID');

                const userTypeText = userType === 'guest' ? 'ผู้เยี่ยมชม' : 'ไม่ระบุตัวตน';
                const verificationText = responseData.recaptcha_verified ?
                    ' (ยืนยันความปลอดภัยแล้ว)' : '';

                showRedirectMessage(
                    `กำลังนำคุณไปยังหน้าติดตามสถานะ (${userTypeText})${verificationText}...`,
                    'info'
                );

                setTimeout(() => {
                    if (reportId) {
                        // ไปหน้าติดตามพร้อมหมายเลขรายงาน
                        window.location.href = '<?php echo site_url("Corruption/track_status?report_id="); ?>' + encodeURIComponent(reportId);
                    } else {
                        // ถ้าไม่มีหมายเลข ไปหน้าติดตามปกติ
                        window.location.href = '<?php echo site_url("Corruption/track_status"); ?>';
                    }
                }, 1500);
            }

        } catch (error) {
            console.error('❌ Error in redirectAfterSuccess:', error);
            // Fallback: ไปหน้าติดตามสถานะ
            console.log('🔄 Using fallback redirect to track_status');
            showRedirectMessage('เกิดข้อผิดพลาดเล็กน้อย กำลังนำคุณไปยังหน้าติดตามสถานะ...', 'warning');
            setTimeout(() => {
                window.location.href = '<?php echo site_url("Corruption/track_status"); ?>';
            }, 2000);
        }
    }

    // *** ฟังก์ชันแสดงข้อความระหว่าง redirect (ปรับปรุงแล้ว) ***
    function showRedirectMessage(message, type = 'info') {
        if (typeof Swal !== 'undefined') {
            // *** เพิ่ม icon และสีตาม type ***
            let iconConfig = {
                'success': { icon: 'success', color: '#28a745' },
                'info': { icon: 'info', color: '#17a2b8' },
                'warning': { icon: 'warning', color: '#ffc107' },
                'error': { icon: 'error', color: '#dc3545' }
            };

            const config = iconConfig[type] || iconConfig['info'];

            Swal.fire({
                title: message,
                timer: 1500,
                timerProgressBar: true,
                showConfirmButton: false,
                icon: config.icon,
                background: 'linear-gradient(135deg, #f8f9fa 0%, #e9ecef 100%)',
                backdrop: 'rgba(0,0,0,0.4)',
                allowOutsideClick: false,
                allowEscapeKey: false,
                customClass: {
                    popup: 'redirect-loading-popup',
                    timerProgressBar: 'redirect-progress-bar'
                },
                // *** เพิ่ม CSS สำหรับสีของ progress bar ***
                willOpen: () => {
                    const progressBar = Swal.getTimerProgressBar();
                    if (progressBar) {
                        progressBar.style.backgroundColor = config.color;
                    }
                }
            });
        } else {
            // Fallback: แสดงใน console และ alert
            console.log(`🔄 ${message}`);
            if (typeof alert !== 'undefined') {
                alert(message);
            }
        }
    }

    // *** ฟังก์ชันสร้างข้อความแจ้งเตือนตามประเภทผู้ใช้ ***
    function getRedirectMessage(isLoggedIn, userType, responseData = {}) {
        // *** สร้างข้อความ reCAPTCHA status ***
        let recaptchaStatus = '';
        if (responseData.recaptcha_verified === true) {
            recaptchaStatus = `
                <div style="margin-top: 0.5rem; padding: 0.3rem 0.6rem; background: rgba(40, 167, 69, 0.1); border-radius: 4px; border-left: 3px solid #28a745;">
                    <small style="color: #28a745; font-weight: 500;">
                        <i class="fas fa-shield-check me-1"></i>
                        ยืนยันความปลอดภัยด้วย reCAPTCHA
                    </small>
                </div>
            `;
        } else if (responseData.verification_method === 'dev_mode_skip') {
            recaptchaStatus = `
                <div style="margin-top: 0.5rem; padding: 0.3rem 0.6rem; background: rgba(255, 193, 7, 0.1); border-radius: 4px; border-left: 3px solid #ffc107;">
                    <small style="color: #856404; font-weight: 500;">
                        <i class="fas fa-tools me-1"></i>
                        โหมดทดสอบระบบ
                    </small>
                </div>
            `;
        }

        // *** สร้างข้อความจำนวนไฟล์ ***
        let filesInfo = '';
        if (responseData.files_uploaded && responseData.files_uploaded > 0) {
            filesInfo = `
                <div style="margin-top: 0.5rem; padding: 0.3rem 0.6rem; background: rgba(23, 162, 184, 0.1); border-radius: 4px; border-left: 3px solid #17a2b8;">
                    <small style="color: #0c5460; font-weight: 500;">
                        <i class="fas fa-paperclip me-1"></i>
                        อัปโหลดไฟล์แนบ ${responseData.files_uploaded} ไฟล์
                    </small>
                </div>
            `;
        }

        if (isLoggedIn && userType === 'public') {
            return `
                <div class="alert alert-primary" style="border-left: 4px solid #007bff; background: linear-gradient(135deg, #cce7ff 0%, #e6f3ff 100%);">
                    <div style="display: flex; align-items: center; margin-bottom: 0.5rem;">
                        <i class="fas fa-user-check me-2" style="color: #007bff; font-size: 1.1rem;"></i>
                        <strong style="color: #004085;">สมาชิก:</strong>
                        <span style="color: #004085; margin-left: 0.5rem;">คุณจะถูกนำไปยังหน้ารายงานของคุณ</span>
                    </div>
                    ${recaptchaStatus}
                    ${filesInfo}
                </div>
            `;
        } else {
            // *** แยกข้อความสำหรับ Guest และ Anonymous ***
            const userDescription = userType === 'guest' ? 'แขก' : 'ไม่ระบุตัวตน';
            const iconClass = userType === 'guest' ? 'fas fa-search' : 'fas fa-user-secret';

            return `
                <div class="alert alert-info" style="border-left: 4px solid #17a2b8; background: linear-gradient(135deg, #d1ecf1 0%, #e8f6f8 100%);">
                    <div style="display: flex; align-items: center; margin-bottom: 0.5rem;">
                        <i class="${iconClass} me-2" style="color: #17a2b8; font-size: 1.1rem;"></i>
                        <strong style="color: #0c5460;">${userDescription}:</strong>
                        <span style="color: #0c5460; margin-left: 0.5rem;">คุณจะถูกนำไปยังหน้าติดตามสถานะ</span>
                    </div>
                    ${recaptchaStatus}
                    ${filesInfo}
                </div>
            `;
        }
    }

    // *** ฟังก์ชันเสริม: ดึงข้อมูลการส่งรายงานล่าสุดจาก sessionStorage ***
    function getLastSubmissionData() {
        try {
            const reportId = sessionStorage.getItem('last_submitted_report');
            const timestamp = sessionStorage.getItem('submission_timestamp');
            const recaptchaVerified = sessionStorage.getItem('last_report_recaptcha_verified') === '1';
            const verificationMethod = sessionStorage.getItem('last_report_verification_method');
            const filesCount = parseInt(sessionStorage.getItem('last_report_files_count')) || 0;

            if (reportId && timestamp) {
                return {
                    reportId,
                    timestamp: parseInt(timestamp),
                    submittedAt: new Date(parseInt(timestamp)),
                    recaptchaVerified,
                    verificationMethod,
                    filesCount
                };
            }

            return null;
        } catch (error) {
            console.error('❌ Error getting last submission data:', error);
            return null;
        }
    }

    // *** ฟังก์ชันเสริม: ล้างข้อมูลการส่งรายงานใน sessionStorage ***
    function clearSubmissionData() {
        try {
            sessionStorage.removeItem('last_submitted_report');
            sessionStorage.removeItem('submission_timestamp');
            sessionStorage.removeItem('last_report_recaptcha_verified');
            sessionStorage.removeItem('last_report_verification_method');
            sessionStorage.removeItem('last_report_files_count');
            console.log('🧹 Cleared submission data from sessionStorage');
        } catch (error) {
            console.error('❌ Error clearing submission data:', error);
        }
    }

    // *** ฟังก์ชัน resetForm() ที่ครบถ้วนสมบูรณ์ ***
    function resetForm() {
        try {
            document.getElementById('corruptionForm').reset();

            // ล้างไฟล์
            selectedFiles = [];
            if (typeof fileIdCounter !== 'undefined') {
                fileIdCounter = 0;
            }
            if (typeof updateFilePreview === 'function') {
                updateFilePreview();
            }

            // ล้างประเภทการทุจริต
            selectedCorruptionType = '';
            document.querySelectorAll('.corruption-type-card').forEach(c => c.classList.remove('selected'));

            // รีเซ็ตโหมดไม่ระบุตัวตน
            document.getElementById('anonymousReport').checked = false;
            if (typeof toggleAnonymous === 'function') {
                toggleAnonymous();
            }

            // แสดงข้อความสำเร็จ
            if (typeof showToast === 'function') {
                showToast('รีเซ็ตฟอร์มเรียบร้อยแล้ว', 'success');
            } else {
                console.log('✅ รีเซ็ตฟอร์มเรียบร้อยแล้ว');
            }

        } catch (error) {
            console.error('❌ Error resetting form:', error);
            if (typeof showToast === 'function') {
                showToast('เกิดข้อผิดพลาดในการรีเซ็ตฟอร์ม', 'error');
            }
        }
    }

    // *** ฟังก์ชัน resetCorruptionForm() เป็น alias ***
    function resetCorruptionForm() {
        resetForm();
    }

    /////////////////////////////////////////////////////////////////////

    // *** ฟังก์ชันตรวจสอบสถานะผู้ใช้อย่างละเอียด ***
    function getCurrentUserStatus() {
        try {
            // ลำดับความสำคัญในการตรวจสอบ

            // 1. ตรวจสอบจาก global state ที่อัปเดตล่าสุด
            if (window.corruptionFormState) {
                const state = window.corruptionFormState;
                console.log('📊 Using global state:', state);
                return {
                    isLoggedIn: state.isUserLoggedIn || false,
                    userType: state.userType || 'guest',
                    source: 'global_state'
                };
            }

            // 2. ตรวจสอบจาก PHP variables (ถ้ามี)
            const phpData = window.phpData || {};
            if (phpData.isLoggedIn !== undefined) {
                console.log('📊 Using PHP data:', phpData);
                return {
                    isLoggedIn: phpData.isLoggedIn || false,
                    userType: phpData.userType || 'guest',
                    source: 'php_data'
                };
            }

            // 3. ตรวจสอบจาก DOM elements (session indicators)
            const hasPublicSession = document.querySelector('[data-user-type="public"]');
            const hasStaffSession = document.querySelector('[data-user-type="staff"]');

            if (hasPublicSession) {
                return { isLoggedIn: true, userType: 'public', source: 'dom_public' };
            } else if (hasStaffSession) {
                return { isLoggedIn: true, userType: 'staff', source: 'dom_staff' };
            }

            // 4. Default เป็น guest
            console.log('📊 Using default guest status');
            return { isLoggedIn: false, userType: 'guest', source: 'default' };

        } catch (error) {
            console.error('Error getting user status:', error);
            return { isLoggedIn: false, userType: 'guest', source: 'error' };
        }
    }


    // Event listener สำหรับ anonymous checkbox
    document.addEventListener('DOMContentLoaded', function () {
        document.getElementById('anonymousReport').addEventListener('change', function () {
            document.getElementById('hiddenAnonymous').value = this.checked ? '1' : '0';
        });
    });

    // เพิ่ม CSS animations
    const toastStyles = document.createElement('style');
    toastStyles.textContent = `
    @keyframes slideInRight {
        from { transform: translateX(100%); opacity: 0; }
        to { transform: translateX(0); opacity: 1; }
    }
    
    @keyframes slideOutRight {
        from { transform: translateX(0); opacity: 1; }
        to { transform: translateX(100%); opacity: 0; }
    }
    
    .file-toast {
        box-shadow: 0 4px 12px rgba(0,0,0,0.15);
        border: none;
    }
`;
    document.head.appendChild(toastStyles);

    console.log('🎯 Script loaded completely');
</script>