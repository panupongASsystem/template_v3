<!-- create_position_type.php - ฟอร์มสร้างประเภทตำแหน่งใหม่ -->
<div class="container">
    <div class="row">
        <div class="col-md-2"></div>
        <div class="col-md-8">
            <div class="d-flex justify-content-between align-items-center mb-4">
                <h4><i class="fas fa-plus-circle text-success"></i> สร้างประเภทตำแหน่งใหม่</h4>
                <a href="<?= site_url('dynamic_position_backend') ?>" class="btn btn-secondary">
                    <i class="fas fa-arrow-left"></i> กลับ
                </a>
            </div>

            <!-- แสดงข้อความแจ้งเตือน -->
            <?php if ($this->session->flashdata('error_message')): ?>
            <div class="alert alert-danger alert-dismissible fade show">
                <strong>ผิดพลาด!</strong> <?= $this->session->flashdata('error_message') ?>
                <button type="button" class="close" data-dismiss="alert">&times;</button>
            </div>
            <?php endif; ?>

            <div class="card shadow">
                <div class="card-body">
                    <div class="alert alert-info">
                        <h6><i class="fas fa-info-circle"></i> รายละเอียด</h6>
                        <ul class="mb-0">
                            <li>ระบบจะสร้าง <strong>61 ตำแหน่งว่าง</strong> ให้อัตโนมัติ</li>
                            <li>จะมีฟิลด์พื้นฐาน: ชื่อ, ตำแหน่ง, โทรศัพท์, อีเมล, รูปภาพ</li>
                            <li>สามารถเพิ่ม/แก้ไขฟิลด์ได้ภายหลัง</li>
                            <li>การจัดการแบบ Grid Layout เหมือนผู้บริหาร</li>
                        </ul>
                    </div>

                    <form action="<?= site_url('dynamic_position_backend/create_new_type') ?>" 
                          method="post" class="form-horizontal" onsubmit="return validateForm()">
                        
                        <div class="form-group row">
                            <div class="col-sm-3 control-label">
                                <label for="peng">ชื่อประเภท (ภาษาอังกฤษ) <span class="text-danger">*</span></label>
                            </div>
                            <div class="col-sm-9">
                                <input type="text" name="peng" id="peng" 
                                       class="form-control" required
                                       pattern="[a-z_]+" 
                                       placeholder="เช่น faculty, staff, student_assistant"
                                       value="<?= set_value('peng') ?>">
                                <small class="form-text text-muted">
                                    ใช้สำหรับระบบ ใช้ตัวอักษรเล็กและ _ เท่านั้น (ไม่มีช่องว่างและอักขระพิเศษ)
                                </small>
                            </div>
                        </div>

                        <div class="form-group row">
                            <div class="col-sm-3 control-label">
                                <label for="pname">ชื่อแสดงผล <span class="text-danger">*</span></label>
                            </div>
                            <div class="col-sm-9">
                                <input type="text" name="pname" id="pname" 
                                       class="form-control" required 
                                       placeholder="เช่น คณาจารย์, เจ้าหน้าที่, นักศึกษาช่วยงาน"
                                       value="<?= set_value('pname') ?>">
                                <small class="form-text text-muted">
                                    ชื่อที่จะแสดงในระบบและหน้าเว็บไซต์
                                </small>
                            </div>
                        </div>

                        <div class="form-group row">
                            <div class="col-sm-3 control-label">
                                <label for="pdescription">คำอธิบาย</label>
                            </div>
                            <div class="col-sm-9">
                                <textarea name="pdescription" id="pdescription" 
                                          class="form-control" rows="3" 
                                          placeholder="คำอธิบายเกี่ยวกับประเภทตำแหน่งนี้"><?= set_value('pdescription') ?></textarea>
                                <small class="form-text text-muted">
                                    อธิบายหน้าที่หรือลักษณะของตำแหน่งประเภทนี้
                                </small>
                            </div>
                        </div>

                        <div class="form-group row">
                            <div class="col-sm-3 control-label">
                                <label for="porder">ลำดับการแสดงผล</label>
                            </div>
                            <div class="col-sm-9">
                                <input type="number" name="porder" id="porder" 
                                       class="form-control" min="0" max="999" 
                                       placeholder="0" value="<?= set_value('porder', '0') ?>">
                                <small class="form-text text-muted">
                                    ตัวเลขน้อยจะแสดงก่อน (เช่น 1=ผู้บริหาร, 2=คณาจารย์, 3=เจ้าหน้าที่)
                                </small>
                            </div>
                        </div>

                        <div class="form-group row">
                            <div class="col-sm-3 control-label">
                                <label for="psub">การแสดงผลซับเมนู</label>
                            </div>
                            <div class="col-sm-9">
                                <input type="number" name="psub" id="psub" 
                                       class="form-control" min="0" max="1" 
                                       placeholder="0" value="<?= set_value('psub', '0') ?>">
                                <small class="form-text text-muted">
                                    ต้องตั้งลำดับการแสดงผลให้ถูกต้องก่อน (เช่น 0=ไม่เป็น, 1=เป็น,)
                                </small>
                            </div>
                        </div>

                        <hr>

                        <div class="form-group row">
                            <div class="col-sm-3">
                                <strong>ฟิลด์ที่จะสร้างให้อัตโนมัติ:</strong>
                            </div>
                            <div class="col-sm-9">
                                <div class="card bg-light">
                                    <div class="card-body">
                                        <div class="row">
                                            <div class="col-md-6">
                                                <div class="form-check">
                                                    <input type="checkbox" class="form-check-input" checked disabled>
                                                    <label class="form-check-label">
                                                        <strong>ชื่อ-นามสกุล</strong> (text, จำเป็น)
                                                    </label>
                                                </div>
                                                <div class="form-check">
                                                    <input type="checkbox" class="form-check-input" checked disabled>
                                                    <label class="form-check-label">
                                                        <strong>ตำแหน่ง</strong> (text, จำเป็น)
                                                    </label>
                                                </div>
                                                <div class="form-check">
                                                    <input type="checkbox" class="form-check-input" checked disabled>
                                                    <label class="form-check-label">
                                                        <strong>เบอร์โทรศัพท์</strong> (tel, ไม่จำเป็น)
                                                    </label>
                                                </div>
                                            </div>
                                            <div class="col-md-6">
                                                <div class="form-check">
                                                    <input type="checkbox" class="form-check-input" checked disabled>
                                                    <label class="form-check-label">
                                                        <strong>อีเมล</strong> (email, ไม่จำเป็น)
                                                    </label>
                                                </div>
                                                <div class="form-check">
                                                    <input type="checkbox" class="form-check-input" checked disabled>
                                                    <label class="form-check-label">
                                                        <strong>รูปภาพ</strong> (file, ไม่จำเป็น)
                                                    </label>
                                                </div>
                                            </div>
                                        </div>
                                        <small class="text-muted">
                                            <i class="fas fa-lightbulb"></i> 
                                            สามารถเพิ่มฟิลด์อื่นๆ ได้ภายหลังผ่านการจัดการฐานข้อมูล
                                        </small>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <hr>

                        <div class="form-group row">
                            <div class="col-sm-3"></div>
                            <div class="col-sm-9">
                                <button type="submit" class="btn btn-success btn-lg">
                                    <i class="fas fa-plus-circle"></i> สร้างประเภทตำแหน่งใหม่
                                </button>
                                <a href="<?= site_url('dynamic_position_backend') ?>" 
                                   class="btn btn-danger btn-lg ml-2">
                                    <i class="fas fa-times"></i> ยกเลิก
                                </a>
                            </div>
                        </div>

                        <div class="alert alert-warning">
                            <h6><i class="fas fa-exclamation-triangle"></i> หมายเหตุสำคัญ</h6>
                            <ul class="mb-0">
                                <li>หลังจากสร้างแล้ว จะมี <strong>61 ช่องว่าง</strong> พร้อมให้เพิ่มข้อมูล</li>
                                <li>ไม่สามารถแก้ไข "ชื่อประเภท (ภาษาอังกฤษ)" ได้หลังจากสร้าง</li>
                                <li>สามารถแก้ไขชื่อแสดงผลและคำอธิบายได้ภายหลัง</li>
                                <li>การลบประเภทตำแหน่งจะลบข้อมูลทั้งหมดในประเภทนั้น</li>
                            </ul>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- JavaScript สำหรับ validation -->
<script>
function validateForm() {
    const typeName = document.getElementById('peng').value.trim();
    const displayName = document.getElementById('pname').value.trim();
    
    // ตรวจสอบชื่อประเภท
    if (!typeName) {
        Swal.fire({
            icon: 'warning',
            title: 'กรุณากรอกชื่อประเภท',
            text: 'ชื่อประเภท (ภาษาอังกฤษ) จำเป็นต้องกรอก'
        });
        return false;
    }
    
    // ตรวจสอบรูปแบบชื่อประเภท
    const typeNamePattern = /^[a-z_]+$/;
    if (!typeNamePattern.test(typeName)) {
        Swal.fire({
            icon: 'error',
            title: 'รูปแบบชื่อประเภทไม่ถูกต้อง',
            text: 'ใช้ได้เฉพาะตัวอักษรเล็ก a-z และเครื่องหมาย _ เท่านั้น'
        });
        return false;
    }
    
    // ตรวจสอบชื่อแสดงผล
    if (!displayName) {
        Swal.fire({
            icon: 'warning',
            title: 'กรุณากรอกชื่อแสดงผล',
            text: 'ชื่อแสดงผลจำเป็นต้องกรอก'
        });
        return false;
    }
    
    // ยืนยันการสร้าง
    return Swal.fire({
        title: 'ยืนยันการสร้างประเภทตำแหน่งใหม่?',
        html: `
            <div class="text-left">
                <strong>ชื่อประเภท:</strong> ${typeName}<br>
                <strong>ชื่อแสดงผล:</strong> ${displayName}<br>
                <strong>จำนวนตำแหน่ง:</strong> 61 ช่อง<br><br>
                <small class="text-muted">ระบบจะสร้างตำแหน่งว่าง 61 ช่องให้อัตโนมัติ</small>
            </div>
        `,
        icon: 'question',
        showCancelButton: true,
        confirmButtonColor: '#28a745',
        cancelButtonColor: '#6c757d',
        confirmButtonText: 'ใช่, สร้างเลย!',
        cancelButtonText: 'ยกเลิก',
        allowOutsideClick: false
    }).then((result) => {
        if (result.isConfirmed) {
            // แสดง loading
            Swal.fire({
                title: 'กำลังสร้างประเภทตำแหน่ง...',
                html: 'กรุณารอสักครู่',
                allowOutsideClick: false,
                allowEscapeKey: false,
                showConfirmButton: false,
                didOpen: () => {
                    Swal.showLoading();
                }
            });
            return true;
        } else {
            return false;
        }
    });
}

// Auto-generate peng จาก display_name
// document.getElementById('pname').addEventListener('input', function() {
//     const displayName = this.value;
//     const typeName = displayName
//         .toLowerCase()
//         .replace(/[^a-z0-9\s]/g, '') // ลบอักขระพิเศษ
//         .replace(/\s+/g, '_') // เปลี่ยนช่องว่างเป็น _
//         .replace(/_{2,}/g, '_') // ลด _ ซ้อนเป็น _ เดียว
//         .replace(/^_|_$/g, ''); // ลบ _ หน้าและหลัง
    
//     document.getElementById('peng').value = typeName;
// });

// Real-time validation
document.getElementById('peng').addEventListener('input', function() {
    const value = this.value;
    const isValid = /^[a-z_]*$/.test(value);
    
    if (isValid) {
        this.classList.remove('is-invalid');
        this.classList.add('is-valid');
    } else {
        this.classList.remove('is-valid');
        this.classList.add('is-invalid');
    }
});

// Character count
document.addEventListener('DOMContentLoaded', function() {
    const inputs = document.querySelectorAll('input[type="text"], textarea');
    
    inputs.forEach(function(input) {
        input.addEventListener('input', function() {
            const maxLength = this.getAttribute('maxlength');
            if (maxLength) {
                const currentLength = this.value.length;
                const remaining = maxLength - currentLength;
                
                let small = this.parentNode.querySelector('.char-count');
                if (!small) {
                    small = document.createElement('small');
                    small.className = 'form-text char-count';
                    this.parentNode.appendChild(small);
                }
                
                small.textContent = `${currentLength}/${maxLength} ตัวอักษร`;
                
                if (remaining < 10) {
                    small.className = 'form-text char-count text-danger';
                } else if (remaining < 30) {
                    small.className = 'form-text char-count text-warning';
                } else {
                    small.className = 'form-text char-count text-muted';
                }
            }
        });
    });
});
</script>

<!-- CSS เพิ่มเติม -->
<style>
.form-control.is-valid {
    border-color: #28a745;
    box-shadow: 0 0 0 0.2rem rgba(40, 167, 69, 0.25);
}

.form-control.is-invalid {
    border-color: #dc3545;
    box-shadow: 0 0 0 0.2rem rgba(220, 53, 69, 0.25);
}

.card {
    border: none;
    box-shadow: 0 0.125rem 0.25rem rgba(0, 0, 0, 0.075) !important;
}

.form-check-input:disabled {
    opacity: 0.6;
}

.form-check-label {
    color: #495057;
}

.alert-info {
    background-color: #e7f3ff;
    border-color: #b8daff;
    color: #004085;
}

.alert-warning {
    background-color: #fff8e1;
    border-color: #ffecb5;
    color: #856404;
}

.btn-lg {
    padding: 0.75rem 1.5rem;
    font-size: 1.125rem;
}

.char-count {
    font-size: 0.75rem;
    margin-top: 0.25rem;
}

/* Loading animation */
.swal2-loading {
    border-width: 4px;
}
</style>