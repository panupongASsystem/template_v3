<!-- หน้าฟอร์มแก้ไขอีเมล -->
<div class="container-fluid">
    <div class="main-header">
        <div>
            <h1 class="main-title">แก้ไขอีเมลรับแจ้งเตือน</h1>
            <p class="text-muted mb-0">แก้ไขที่อยู่อีเมลสำหรับรับการแจ้งเตือนความปลอดภัยของระบบ</p>
        </div>
    </div><br>

    <!-- ฟอร์มแก้ไขอีเมล -->
    <div class="card">
        <div class="card-header">
            <h5 class="card-title">
                <i class="fas fa-edit me-2"></i>แก้ไขอีเมล
            </h5>
        </div>
        <div class="card-body">
            <form action="<?php echo base_url('Email_register/edit/' . $email->email_id); ?>" method="post" class="needs-validation" novalidate>
                <div class="row mb-3">
                    <label for="email_name" class="col-sm-2 col-form-label">อีเมล <span class="text-danger">*</span></label>
                    <div class="col-sm-6">
                        <input type="email" class="form-control" id="email_name" name="email_name" value="<?php echo $email->email_name; ?>" required>
                        <div class="invalid-feedback">
                            กรุณากรอกอีเมลให้ถูกต้อง
                        </div>
                    </div>
                </div>

                <div class="row mb-3">
                    <label class="col-sm-2 col-form-label">สถานะ</label>
                    <div class="col-sm-6">
                        <div class="form-check form-switch">
                            <input class="form-check-input" type="checkbox" id="email_status" name="email_status" value="1" <?php echo ($email->email_status == '1') ? 'checked' : ''; ?>>
                            <label class="form-check-label" for="email_status">
                                <?php echo ($email->email_status == '1') ? 'เปิดใช้งาน' : 'ปิดใช้งาน'; ?>
                            </label>
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
    
    // อัพเดตป้ายกำกับสถานะเมื่อสลับสวิตช์
    document.getElementById('email_status').addEventListener('change', function() {
        const statusLabel = this.nextElementSibling;
        statusLabel.textContent = this.checked ? 'เปิดใช้งาน' : 'ปิดใช้งาน';
    });
})();
</script>