<!-- หน้าฟอร์มเพิ่มอีเมล -->
<div class="container-fluid">
    <div class="main-header">
        <div>
            <h1 class="main-title">เพิ่มอีเมลรับแจ้งเตือน</h1>
            <p class="text-muted mb-0">เพิ่มที่อยู่อีเมลสำหรับรับการแจ้งเตือนความปลอดภัยของระบบ</p>
        </div>
    </div><br>

    <!-- ฟอร์มเพิ่มอีเมล -->
    <div class="card">
        <div class="card-header">
            <h5 class="card-title">
                <i class="fas fa-plus-circle me-2"></i>เพิ่มอีเมลใหม่
            </h5>
        </div>
        <div class="card-body">
            <form action="<?php echo base_url('Email_register/add'); ?>" method="post" class="needs-validation" novalidate>
                <div class="row mb-3">
                    <label for="email_name" class="col-sm-2 col-form-label">อีเมล <span class="text-danger">*</span></label>
                    <div class="col-sm-6">
                        <input type="email" class="form-control" id="email_name" name="email_name" required>
                        <div class="invalid-feedback">
                            กรุณากรอกอีเมลให้ถูกต้อง
                        </div>
                    </div>
                </div>

                <div class="row">
                    <div class="col-sm-2"></div>
                    <div class="col-sm-6">
                        <button type="submit" class="btn btn-primary">
                            <i class="fas fa-save me-1"></i>บันทึก
                        </button>
                        <a href="<?php echo base_url('Email_register'); ?>" class="btn btn-secondary ms-2">
                            <i class="fas fa-times me-1"></i>ยกเลิก
                        </a>
                    </div>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
// ตรวจสอบความถูกต้องของฟอร์ม
(function() {
    'use strict';
    window.addEventListener('load', function() {
        // เลือกทุกฟอร์มที่ต้องการตรวจสอบ
        var forms = document.getElementsByClassName('needs-validation');
        
        // วนลูปเพื่อป้องกันการส่งฟอร์มและเริ่มการตรวจสอบ
        Array.prototype.filter.call(forms, function(form) {
            form.addEventListener('submit', function(event) {
                if (form.checkValidity() === false) {
                    event.preventDefault();
                    event.stopPropagation();
                }
                form.classList.add('was-validated');
            }, false);
        });
    }, false);
})();
</script>