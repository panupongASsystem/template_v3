<!-- CSS -->
<style>
    body {
        background: #f8f9fa;
    }

    .form-container {
        max-width: 800px;
        margin: 40px auto;
        background: white;
        border-radius: 15px;
        padding: 40px;
        box-shadow: 0 2px 10px rgba(0, 0, 0, 0.05);
        border: 1px solid #e9ecef;
    }

    .form-header {
        text-align: center;
        margin-bottom: 40px;
        padding-bottom: 20px;
        border-bottom: 2px solid #e9ecef;
    }

    .form-header h1 {
        font-size: 2em;
        font-weight: 600;
        color: #2c3e50;
        margin-bottom: 10px;
    }

    .form-header p {
        color: #7f8c8d;
        font-size: 1em;
    }

    .form-group {
        margin-bottom: 25px;
    }

    .form-group label {
        display: block;
        font-weight: 500;
        color: #2c3e50;
        margin-bottom: 8px;
        font-size: 1em;
    }

    .form-group label .required {
        color: #e74c3c;
        margin-left: 3px;
    }

    .form-control {
        width: 100%;
        padding: 12px 15px;
        border: 2px solid #e9ecef;
        border-radius: 8px;
        font-size: 1em;
        transition: all 0.3s ease;
    }

    .form-control:focus {
        outline: none;
        border-color: #95a5a6;
        box-shadow: 0 0 0 3px rgba(149, 165, 166, 0.1);
    }

    .file-upload-wrapper {
        position: relative;
        overflow: hidden;
        display: inline-block;
        width: 100%;
    }

    .file-upload-input {
        position: absolute;
        left: -9999px;
    }

    .file-upload-label {
        display: flex;
        align-items: center;
        justify-content: center;
        padding: 40px 20px;
        border: 2px dashed #bdc3c7;
        border-radius: 8px;
        cursor: pointer;
        transition: all 0.3s ease;
        background: #f8f9fa;
    }

    .file-upload-label:hover {
        border-color: #95a5a6;
        background: #ecf0f1;
    }

    .file-upload-label i {
        font-size: 3em;
        color: #95a5a6;
        margin-bottom: 10px;
    }

    .file-upload-text {
        text-align: center;
    }

    .file-upload-text p {
        margin: 0;
        color: #7f8c8d;
    }

    .file-upload-text .main-text {
        font-size: 1.1em;
        font-weight: 500;
        color: #2c3e50;
        margin-bottom: 5px;
    }

    .file-name-display {
        margin-top: 10px;
        padding: 10px;
        background: #e8f5e9;
        border-radius: 8px;
        color: #2e7d32;
        display: none;
    }

    .file-name-display i {
        margin-right: 5px;
    }

    .help-text {
        font-size: 0.85em;
        color: #95a5a6;
        margin-top: 5px;
    }

    .form-actions {
        display: flex;
        gap: 15px;
        justify-content: center;
        margin-top: 40px;
        padding-top: 30px;
        border-top: 2px solid #e9ecef;
    }

    .btn {
        padding: 12px 30px;
        border: none;
        border-radius: 8px;
        font-size: 1em;
        font-weight: 500;
        cursor: pointer;
        transition: all 0.3s ease;
        display: flex;
        align-items: center;
        gap: 8px;
        text-decoration: none;
    }

    .btn-primary {
        background: #2c3e50;
        color: white;
    }

    .btn-primary:hover {
        background: #34495e;
        transform: translateY(-2px);
        box-shadow: 0 4px 12px rgba(44, 62, 80, 0.3);
    }

    .btn-secondary {
        background: #ecf0f1;
        color: #2c3e50;
    }

    .btn-secondary:hover {
        background: #bdc3c7;
        transform: translateY(-2px);
    }

    .alert {
        padding: 15px 20px;
        border-radius: 8px;
        margin-bottom: 20px;
        display: flex;
        align-items: center;
        gap: 10px;
    }

    .alert-danger {
        background: #ffebee;
        color: #c62828;
        border: 1px solid #ef9a9a;
    }

    .alert i {
        font-size: 1.2em;
    }
</style>

<div class="container">
    <div class="form-container">
        <!-- Header -->
        <div class="form-header">
            <h1>➕ เพิ่มคู่มือใหม่</h1>
            <p>กรอกข้อมูลและอัปโหลดไฟล์ PDF คู่มือการใช้งาน</p>
        </div>

        <!-- Error Messages -->
        <?php if (validation_errors()): ?>
            <div class="alert alert-danger">
                <i class="bi bi-exclamation-circle"></i>
                <div><?= validation_errors(); ?></div>
            </div>
        <?php endif; ?>

        <?php if ($this->session->flashdata('error')): ?>
            <div class="alert alert-danger">
                <i class="bi bi-exclamation-circle"></i>
                <div><?= $this->session->flashdata('error'); ?></div>
            </div>
        <?php endif; ?>

        <!-- Form -->
        <?= form_open_multipart('manual_admin_backend/insert_manual_admin'); ?>
            
            <!-- ชื่อคู่มือ -->
            <div class="form-group">
                <label for="manual_admin_name">
                    ชื่อคู่มือ
                    <span class="required">*</span>
                </label>
                <input type="text" 
                       class="form-control" 
                       id="manual_admin_name" 
                       name="manual_admin_name" 
                       placeholder="กรุณาระบุชื่อคู่มือ เช่น คู่มือการใช้งานระบบสำหรับแอดมิน"
                       value="<?= set_value('manual_admin_name'); ?>"
                       required>
                <small class="help-text">ระบุชื่อคู่มือที่ชัดเจนและเข้าใจง่าย</small>
            </div>

            <!-- ไฟล์ PDF -->
            <div class="form-group">
                <label>
                    ไฟล์ PDF
                    <span class="required">*</span>
                </label>
                <div class="file-upload-wrapper">
                    <input type="file" 
                           class="file-upload-input" 
                           id="manual_admin_pdf" 
                           name="manual_admin_pdf" 
                           accept=".pdf"
                           required>
                    <label for="manual_admin_pdf" class="file-upload-label">
                        <div class="file-upload-text">
                            <i class="bi bi-cloud-upload"></i>
                            <p class="main-text">คลิกเพื่อเลือกไฟล์ หรือ ลากไฟล์มาวางที่นี่</p>
                            <p>รองรับไฟล์ PDF เท่านั้น (สูงสุด 20 MB)</p>
                        </div>
                    </label>
                </div>
                <div class="file-name-display" id="fileNameDisplay">
                    <i class="bi bi-file-pdf"></i>
                    <span id="fileName"></span>
                </div>
                <small class="help-text">ไฟล์ต้องเป็น PDF และมีขนาดไม่เกิน 20 MB</small>
            </div>

            <!-- Buttons -->
            <div class="form-actions">
                <button type="submit" class="btn btn-primary">
                    <i class="bi bi-check-circle"></i>
                    บันทึก
                </button>
                <a href="<?= site_url('manual_admin_backend'); ?>" class="btn btn-secondary">
                    <i class="bi bi-x-circle"></i>
                    ยกเลิก
                </a>
            </div>

        <?= form_close(); ?>
    </div>
</div>

<!-- JavaScript -->
<script>
$(document).ready(function() {
    // File Upload Display
    $('#manual_admin_pdf').on('change', function() {
        var fileName = $(this).val().split('\\').pop();
        if (fileName) {
            $('#fileName').text(fileName);
            $('#fileNameDisplay').slideDown(300);
            $('.file-upload-label').css('border-color', '#4caf50');
        } else {
            $('#fileNameDisplay').slideUp(300);
            $('.file-upload-label').css('border-color', '#bdc3c7');
        }
    });

    // Drag and Drop
    $('.file-upload-label').on('dragover', function(e) {
        e.preventDefault();
        $(this).css({
            'border-color': '#4caf50',
            'background': '#e8f5e9'
        });
    });

    $('.file-upload-label').on('dragleave', function(e) {
        e.preventDefault();
        $(this).css({
            'border-color': '#bdc3c7',
            'background': '#f8f9fa'
        });
    });

    $('.file-upload-label').on('drop', function(e) {
        e.preventDefault();
        var files = e.originalEvent.dataTransfer.files;
        $('#manual_admin_pdf')[0].files = files;
        $(this).css({
            'border-color': '#bdc3c7',
            'background': '#f8f9fa'
        });
        $('#manual_admin_pdf').trigger('change');
    });

    // Form Validation
    $('form').on('submit', function(e) {
        var fileName = $('#manual_admin_pdf').val();
        var manualName = $('#manual_admin_name').val().trim();

        if (!manualName) {
            e.preventDefault();
            Swal.fire({
                icon: 'error',
                title: 'กรุณากรอกชื่อคู่มือ',
                text: 'ชื่อคู่มือเป็นข้อมูลที่จำเป็น',
                confirmButtonColor: '#e74c3c'
            });
            return false;
        }

        if (!fileName) {
            e.preventDefault();
            Swal.fire({
                icon: 'error',
                title: 'กรุณาเลือกไฟล์ PDF',
                text: 'ต้องอัปโหลดไฟล์ PDF คู่มือ',
                confirmButtonColor: '#e74c3c'
            });
            return false;
        }

        // Check file extension
        if (!fileName.toLowerCase().endsWith('.pdf')) {
            e.preventDefault();
            Swal.fire({
                icon: 'error',
                title: 'ไฟล์ไม่ถูกต้อง',
                text: 'กรุณาเลือกไฟล์ PDF เท่านั้น',
                confirmButtonColor: '#e74c3c'
            });
            return false;
        }

        // Show loading
        Swal.fire({
            title: 'กำลังบันทึก...',
            html: 'กรุณารอสักครู่',
            allowOutsideClick: false,
            showConfirmButton: false,
            didOpen: () => {
                Swal.showLoading();
            }
        });
    });
});
</script>