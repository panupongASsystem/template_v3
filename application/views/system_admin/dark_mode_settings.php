<!-- views/system_admin/dark_mode_settings.php -->
<div class="container-fluid py-4">
    <div class="row">
        <div class="col-lg-8 mx-auto">
            <!-- Header Card -->
            <div class="card border-0 shadow-sm mb-4">
                <div class="card-body p-4">
                    <div class="d-flex align-items-center mb-1">
                        <div class="icon-box bg-gradient-dark text-white rounded-circle p-3 me-3">
                            <i class="fas fa-cog fa-lg"></i>
                        </div>
                        <div>
                            <h4 class="mb-0">การตั้งค่าการแสดงผล</h4>
                            <p class="text-muted mb-0 small">จัดการโหมดมืดและโบว์ไว้อาลัยสำหรับผู้ใช้งานทั่วไป</p>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Alert Messages -->
            <?php if ($this->session->flashdata('success')): ?>
                <div class="alert alert-success alert-dismissible fade show border-0 shadow-sm mb-4" role="alert">
                    <div class="d-flex align-items-center">
                        <i class="fas fa-check-circle fa-lg me-3"></i>
                        <div><?= $this->session->flashdata('success') ?></div>
                    </div>
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
            <?php endif; ?>

            <?php if ($this->session->flashdata('error')): ?>
                <div class="alert alert-danger alert-dismissible fade show border-0 shadow-sm mb-4" role="alert">
                    <div class="d-flex align-items-center">
                        <i class="fas fa-exclamation-circle fa-lg me-3"></i>
                        <div><?= $this->session->flashdata('error') ?></div>
                    </div>
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
            <?php endif; ?>

            <!-- Main Settings Card -->
            <div class="card border-0 shadow-sm mb-4">
                <div class="card-body p-4">
                    <form method="post" id="darkModeForm">
                        
                        <!-- สถานะปัจจุบัน -->
                        <div class="status-card <?= $dark_mode_enabled == '1' ? 'status-active' : 'status-inactive' ?> p-4 rounded-3 mb-4">
                            <div class="d-flex align-items-center">
                                <div class="status-icon me-3">
                                    <i class="fas fa-circle-info fa-2x"></i>
                                </div>
                                <div class="flex-grow-1">
                                    <div class="d-flex align-items-center mb-2">
                                        <strong class="me-2">สถานะปัจจุบัน:</strong>
                                        <?php if ($dark_mode_enabled == '1'): ?>
                                            <span class="badge badge-success-custom">
                                                <i class="fas fa-check-circle me-1"></i>Dark Mode เปิดใช้งาน
                                            </span>
                                        <?php else: ?>
                                            <span class="badge badge-secondary-custom">
                                                <i class="fas fa-times-circle me-1"></i>Dark Mode ปิดใช้งาน
                                            </span>
                                        <?php endif; ?>
                                        
                                        <?php if ($mourning_ribbon_enabled == '1'): ?>
                                            <span class="badge badge-danger-custom ms-2">
                                                <i class="fas fa-ribbon me-1"></i>โบว์ไว้อาลัยเปิดใช้งาน
                                            </span>
                                        <?php endif; ?>
                                    </div>
                                    <p class="text-muted mb-0 small">
                                        <i class="fas fa-info-circle me-1"></i>
                                        Dark Mode จะแปลงเว็บไซต์เป็นโทนสีขาว-ดำ-เทา (Grayscale) และโบว์ไว้อาลัยจะแสดงที่มุมขวาบน
                                    </p>
                                </div>
                            </div>
                        </div>

                        <!-- Dark Mode Toggle Switch Section -->
                        <div class="toggle-section p-4 bg-light rounded-3 mb-4">
                            <div class="d-flex align-items-center justify-content-between">
                                <div class="flex-grow-1">
                                    <h6 class="mb-1">
                                        <i class="fas fa-moon me-2 text-primary"></i>
                                        เปิด/ปิด Dark Mode
                                    </h6>
                                    <p class="text-muted mb-0 small">
                                        เปลี่ยนสถานะการใช้งานโหมดมืดสำหรับผู้ใช้งานทั่วไป
                                    </p>
                                </div>
                                <div class="form-check form-switch form-switch-lg ms-1">
                                    <input class="form-check-input" type="checkbox" 
                                           name="dark_mode_enabled" value="1" 
                                           id="darkModeToggle"
                                           <?= $dark_mode_enabled == '1' ? 'checked' : '' ?>>
                                    <label class="form-check-label visually-hidden" for="darkModeToggle">
                                        Toggle Dark Mode
                                    </label>
                                </div>
                            </div>
                            <div class="mt-3 pt-3 border-top">
                                <div class="text-center">
                                    <span class="badge bg-dark px-3 py-2" id="toggleStatusBadge">
                                        <i class="fas <?= $dark_mode_enabled == '1' ? 'fa-moon' : 'fa-sun' ?> me-2"></i>
                                        <span id="toggleLabel">
                                            <?= $dark_mode_enabled == '1' ? 'โหมดมืดกำลังเปิดใช้งาน' : 'โหมดปกติ' ?>
                                        </span>
                                    </span>
                                </div>
                            </div>
                        </div>

                        <!-- Mourning Ribbon Toggle Switch Section -->
                        <div class="toggle-section p-4 bg-light rounded-3 mb-4">
                            <div class="d-flex align-items-center justify-content-between">
                                <div class="flex-grow-1">
                                    <h6 class="mb-1">
                                        <i class="fas fa-ribbon me-2 text-danger"></i>
                                        เปิด/ปิดโบว์ไว้อาลัย
                                    </h6>
                                    <p class="text-muted mb-0 small">
                                        แสดงโบว์ไว้อาลัยที่มุมขวาบนของเว็บไซต์
                                    </p>
                                </div>
                                <div class="form-check form-switch form-switch-lg ms-1">
                                    <input class="form-check-input" type="checkbox" 
                                           name="mourning_ribbon_enabled" value="1" 
                                           id="mourningRibbonToggle"
                                           <?= $mourning_ribbon_enabled == '1' ? 'checked' : '' ?>>
                                    <label class="form-check-label visually-hidden" for="mourningRibbonToggle">
                                        Toggle Mourning Ribbon
                                    </label>
                                </div>
                            </div>
                            <div class="mt-3 pt-3 border-top">
                                <div class="text-center">
                                    <span class="badge bg-secondary px-3 py-2" id="ribbonStatusBadge">
                                        <i class="fas <?= $mourning_ribbon_enabled == '1' ? 'fa-ribbon' : 'fa-times-circle' ?> me-2"></i>
                                        <span id="ribbonLabel">
                                            <?= $mourning_ribbon_enabled == '1' ? 'โบว์ไว้อาลัยกำลังแสดง' : 'ไม่แสดงโบว์ไว้อาลัย' ?>
                                        </span>
                                    </span>
                                </div>
                            </div>
                        </div>

                        <!-- Action Buttons -->
                        <div class="d-flex gap-2 justify-content-end">
                            <a href="<?= base_url('system_config_backend') ?>" class="btn btn-light border px-4">
                                <i class="fas fa-arrow-left me-2"></i>กลับ
                            </a>
                            <button type="button" class="btn btn-primary px-4" id="submitBtn">
                                <i class="fas fa-save me-2"></i>บันทึกการตั้งค่า
                            </button>
                        </div>
                    </form>
                </div>
            </div>

            <!-- Preview Card -->
            <div class="card border-0 shadow-sm">
                <div class="card-header bg-white border-0 pt-4 px-4">
                    <h6 class="mb-0">
                        <i class="fas fa-eye me-2 text-primary"></i>
                        ตัวอย่างการแสดงผล
                    </h6>
                    <p class="text-muted mb-0 small mt-1">ดูตัวอย่างการแสดงผลเมื่อเปิด Dark Mode และโบว์ไว้อาลัย</p>
                </div>
                <div class="card-body p-4">
                    <div class="preview-container position-relative">
                        <!-- Preview Ribbon (จะแสดงเมื่อเปิด Toggle) -->
                        <div class="preview-ribbon" id="previewRibbon" style="display: <?= $mourning_ribbon_enabled == '1' ? 'block' : 'none' ?>;">
                            <img src="<?= base_url('docs/ribbon.png') ?>" alt="โบว์ไว้อาลัย">
                        </div>
                        
                        <div class="preview-box p-4 rounded-3 bg-gradient-light" id="previewBox">
                            <div class="row">
                                <div class="col-md-8">
                                    <h5 class="text-primary mb-3">
                                        <i class="fas fa-newspaper me-2"></i>
                                        ตัวอย่างหัวข้อข่าวประชาสัมพันธ์
                                    </h5>
                                    <p class="text-muted mb-3">
                                        นี่คือตัวอย่างข้อความในโหมดมืดแบบ Grayscale 
                                        ซึ่งจะแปลงทุกสีให้เป็นโทนสีขาว-ดำ-เทาเพื่อความสบายตาในการใช้งาน
                                    </p>
                                    <div class="d-flex gap-2 flex-wrap">
                                        <button class="btn btn-sm btn-primary" type="button">
                                            <i class="fas fa-check me-1"></i>ปุ่มหลัก
                                        </button>
                                        <button class="btn btn-sm btn-success" type="button">
                                            <i class="fas fa-save me-1"></i>ปุ่มสำเร็จ
                                        </button>
                                        <button class="btn btn-sm btn-warning" type="button">
                                            <i class="fas fa-exclamation me-1"></i>ปุ่มเตือน
                                        </button>
                                    </div>
                                </div>
                                <div class="col-md-4 mt-3 mt-md-0">
                                    <div class="card border">
                                        <div class="card-body">
                                            <h6 class="text-primary">
                                                <i class="fas fa-info-circle me-2"></i>
                                                ข้อมูลเพิ่มเติม
                                            </h6>
                                            <ul class="list-unstyled mb-0 small text-muted">
                                                <li class="mb-1"><i class="fas fa-check text-success me-2"></i>ลดแสงสีฟ้า</li>
                                                <li class="mb-1"><i class="fas fa-check text-success me-2"></i>ความสบายตา</li>
                                            </ul>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="mt-3 text-center">
                        <button type="button" class="btn btn-outline-dark px-4 me-2" id="previewToggle">
                            <i class="fas fa-eye me-2"></i>ดูตัวอย่าง Dark Mode
                        </button>
                        <button type="button" class="btn btn-outline-secondary px-4" id="ribbonPreviewToggle">
                            <i class="fas fa-ribbon me-2"></i>แสดง/ซ่อนโบว์
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Modal ยืนยันการเปิด -->
<div class="modal fade" id="confirmModal" tabindex="-1" aria-labelledby="confirmModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0 shadow-lg">
            <div class="modal-header bg-gradient-warning text-dark border-0">
                <h5 class="modal-title" id="confirmModalLabel">
                    <i class="fas fa-exclamation-triangle me-2"></i>
                    ยืนยันการเปิด <span id="confirmFeatureName"></span>
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body p-4">
                <div class="alert alert-warning border-0 mb-4">
                    <div class="d-flex">
                        <i class="fas fa-info-circle fa-lg me-3 mt-1"></i>
                        <div>
                            <strong>คำเตือน:</strong>
                            <p class="mb-0 mt-1">การเปลี่ยนแปลงจะมีผลกับผู้ใช้งานทุกคน</p>
                        </div>
                    </div>
                </div>
                
                <div class="text-center mb-4">
                    <p class="mb-2">กรุณาพิมพ์ <strong class="text-danger fs-5">"yes"</strong> เพื่อยืนยัน</p>
                </div>
                
                <div class="confirmation-input-wrapper">
                    <input type="text" 
                           class="form-control form-control-lg text-center confirmation-input" 
                           id="modalConfirmation" 
                           placeholder='พิมพ์ "yes" ที่นี่' 
                           autocomplete="off">
                </div>
                
                <div class="text-center mt-3">
                    <small class="text-muted">
                        <i class="fas fa-keyboard me-1"></i>
                        พิมพ์คำว่า "yes" ด้วยตัวพิมพ์เล็กทั้งหมด
                    </small>
                </div>
            </div>
            <div class="modal-footer border-0 pt-0">
                <button type="button" class="btn btn-light border px-4" data-bs-dismiss="modal">
                    <i class="fas fa-times me-2"></i>ยกเลิก
                </button>
                <button type="button" class="btn btn-primary px-4" id="confirmSubmit" disabled>
                    <i class="fas fa-check me-2"></i>ยืนยัน
                </button>
            </div>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const darkModeToggle = document.getElementById('darkModeToggle');
    const ribbonToggle = document.getElementById('mourningRibbonToggle');
    const toggleLabel = document.getElementById('toggleLabel');
    const toggleBadge = document.getElementById('toggleStatusBadge');
    const ribbonLabel = document.getElementById('ribbonLabel');
    const ribbonBadge = document.getElementById('ribbonStatusBadge');
    
    const currentDarkMode = <?= $dark_mode_enabled == '1' ? 'true' : 'false' ?>;
    const currentRibbon = <?= $mourning_ribbon_enabled == '1' ? 'true' : 'false' ?>;
    
    const form = document.getElementById('darkModeForm');
    const submitBtn = document.getElementById('submitBtn');
    const confirmModal = new bootstrap.Modal(document.getElementById('confirmModal'));
    const modalInput = document.getElementById('modalConfirmation');
    const confirmBtn = document.getElementById('confirmSubmit');
    const confirmFeatureName = document.getElementById('confirmFeatureName');
    
    let pendingAction = null;
    
    // อัพเดต Dark Mode label
    darkModeToggle.addEventListener('change', function() {
        if (this.checked) {
            toggleLabel.textContent = 'โหมดมืดกำลังเปิดใช้งาน';
            toggleBadge.innerHTML = '<i class="fas fa-moon me-2"></i>' + toggleLabel.textContent;
        } else {
            toggleLabel.textContent = 'โหมดปกติ';
            toggleBadge.innerHTML = '<i class="fas fa-sun me-2"></i>' + toggleLabel.textContent;
        }
    });
    
    // อัพเดต Ribbon label
    ribbonToggle.addEventListener('change', function() {
        if (this.checked) {
            ribbonLabel.textContent = 'โบว์ไว้อาลัยกำลังแสดง';
            ribbonBadge.innerHTML = '<i class="fas fa-ribbon me-2"></i>' + ribbonLabel.textContent;
        } else {
            ribbonLabel.textContent = 'ไม่แสดงโบว์ไว้อาลัย';
            ribbonBadge.innerHTML = '<i class="fas fa-times-circle me-2"></i>' + ribbonLabel.textContent;
        }
    });
    
    // เมื่อกดปุ่มบันทึก
    submitBtn.addEventListener('click', function(e) {
        e.preventDefault();
        
        const isDarkModeChanging = darkModeToggle.checked !== currentDarkMode;
        const isRibbonChanging = ribbonToggle.checked !== currentRibbon;
        
        if (!isDarkModeChanging && !isRibbonChanging) {
            alert('ไม่มีการเปลี่ยนแปลงการตั้งค่า');
            return;
        }
        
        // ถ้ามีการเปิดฟีเจอร์ใดๆ ให้แสดง modal ยืนยัน
        if ((darkModeToggle.checked && !currentDarkMode) || (ribbonToggle.checked && !currentRibbon)) {
            let features = [];
            if (darkModeToggle.checked && !currentDarkMode) features.push('Dark Mode');
            if (ribbonToggle.checked && !currentRibbon) features.push('โบว์ไว้อาลัย');
            
            confirmFeatureName.textContent = features.join(' และ ');
            pendingAction = 'enable';
            confirmModal.show();
            modalInput.value = '';
            setTimeout(() => modalInput.focus(), 300);
        } else {
            // ถ้าเป็นการปิด ให้บันทึกเลย
            form.action = '<?= base_url('system_config_backend/update_display_settings') ?>';
            form.submit();
        }
    });
    
    // ตรวจสอบ input
    modalInput.addEventListener('input', function() {
        const value = this.value.toLowerCase().trim();
        confirmBtn.disabled = (value !== 'yes');
        
        if (value === 'yes') {
            this.classList.remove('is-invalid');
            this.classList.add('is-valid');
        } else if (value.length > 0) {
            this.classList.remove('is-valid');
            this.classList.add('is-invalid');
        } else {
            this.classList.remove('is-valid', 'is-invalid');
        }
    });
    
    // Enter เพื่อยืนยัน
    modalInput.addEventListener('keypress', function(e) {
        if (e.key === 'Enter' && this.value.toLowerCase().trim() === 'yes') {
            confirmBtn.click();
        }
    });
    
    // ยืนยันการเปลี่ยนแปลง
    confirmBtn.addEventListener('click', function() {
        this.disabled = true;
        this.innerHTML = '<span class="spinner-border spinner-border-sm me-2"></span>กำลังบันทึก...';
        
        const hiddenInput = document.createElement('input');
        hiddenInput.type = 'hidden';
        hiddenInput.name = 'confirmation';
        hiddenInput.value = 'yes';
        form.appendChild(hiddenInput);
        
        form.action = '<?= base_url('system_config_backend/update_display_settings') ?>';
        form.submit();
    });
    
    // Preview Dark Mode
    const previewBox = document.getElementById('previewBox');
    const previewToggle = document.getElementById('previewToggle');
    let isPreviewDark = false;
    
    previewToggle.addEventListener('click', function() {
        if (!isPreviewDark) {
            previewBox.style.filter = 'grayscale(100%)';
            this.innerHTML = '<i class="fas fa-eye-slash me-2"></i>ปิดตัวอย่าง';
            this.classList.remove('btn-outline-dark');
            this.classList.add('btn-dark');
            isPreviewDark = true;
        } else {
            previewBox.style.filter = 'none';
            this.innerHTML = '<i class="fas fa-eye me-2"></i>ดูตัวอย่าง Dark Mode';
            this.classList.remove('btn-dark');
            this.classList.add('btn-outline-dark');
            isPreviewDark = false;
        }
    });
    
    // Preview Ribbon
    const previewRibbon = document.getElementById('previewRibbon');
    const ribbonPreviewToggle = document.getElementById('ribbonPreviewToggle');
    
    ribbonPreviewToggle.addEventListener('click', function() {
        if (previewRibbon.style.display === 'none') {
            previewRibbon.style.display = 'block';
            this.innerHTML = '<i class="fas fa-eye-slash me-2"></i>ซ่อนโบว์';
        } else {
            previewRibbon.style.display = 'none';
            this.innerHTML = '<i class="fas fa-ribbon me-2"></i>แสดงโบว์';
        }
    });
});
</script>

<style>
/* Icon Box */
.icon-box {
    width: 56px;
    height: 56px;
    display: flex;
    align-items: center;
    justify-content: center;
}

/* Status Card */
.status-card {
    border: 2px solid;
    transition: all 0.3s ease;
}

.status-active {
    background: linear-gradient(135deg, #d4edda 0%, #e8f5e9 100%);
    border-color: #28a745;
}

.status-inactive {
    background: linear-gradient(135deg, #f8f9fa 0%, #e9ecef 100%);
    border-color: #dee2e6;
}

.status-icon {
    opacity: 0.7;
}

/* Badge Custom */
.badge-success-custom {
    background: linear-gradient(135deg, #28a745 0%, #20c997 100%);
    color: white;
    padding: 0.5rem 1rem;
    font-size: 0.875rem;
    font-weight: 500;
    border-radius: 50px;
}

.badge-secondary-custom {
    background: linear-gradient(135deg, #6c757d 0%, #5a6268 100%);
    color: white;
    padding: 0.5rem 1rem;
    font-size: 0.875rem;
    font-weight: 500;
    border-radius: 50px;
}

.badge-danger-custom {
    background: linear-gradient(135deg, #dc3545 0%, #c82333 100%);
    color: white;
    padding: 0.5rem 1rem;
    font-size: 0.875rem;
    font-weight: 500;
    border-radius: 50px;
}

/* Toggle Section */
.toggle-section {
    border: 1px solid #e9ecef;
    transition: all 0.3s ease;
}

.toggle-section:hover {
    box-shadow: 0 0.125rem 0.25rem rgba(0, 0, 0, 0.075);
}

.form-switch-lg .form-check-input {
    width: 4rem;
    height: 2rem;
    cursor: pointer;
}

.form-switch-lg .form-check-input:checked {
    background-color: #28a745;
    border-color: #28a745;
}

/* Preview Box */
.preview-container {
    position: relative;
}

.preview-box {
    transition: filter 0.5s ease;
    border: 2px dashed #dee2e6;
}

.bg-gradient-light {
    background: linear-gradient(135deg, #ffffff 0%, #f8f9fa 100%);
}

/* Preview Ribbon */
.preview-ribbon {
    position: absolute;
    top: 10px;
    right: 10px;
    z-index: 10;
    width: 50px;
    height: auto;
    opacity: 0.85;
    transition: all 0.3s ease;
}

.preview-ribbon img {
    width: 100%;
    height: auto;
    display: block;
    filter: drop-shadow(0 2px 4px rgba(0,0,0,0.2));
}

/* Confirmation Input */
.confirmation-input-wrapper {
    position: relative;
}

.confirmation-input {
    font-size: 1.75rem;
    letter-spacing: 0.2em;
    font-weight: 600;
    text-transform: lowercase;
    border: 3px solid #dee2e6;
    border-radius: 12px;
    transition: all 0.3s ease;
}

.confirmation-input:focus {
    border-color: #0d6efd;
    box-shadow: 0 0 0 0.25rem rgba(13, 110, 253, 0.15);
}

.confirmation-input.is-valid {
    border-color: #28a745;
    background-color: #d4edda;
    color: #155724;
}

.confirmation-input.is-invalid {
    border-color: #dc3545;
    background-color: #f8d7da;
    color: #721c24;
}

/* Modal */
.modal-content {
    border-radius: 20px;
    overflow: hidden;
}

.modal-header {
    border-radius: 20px 20px 0 0;
}

.bg-gradient-warning {
    background: linear-gradient(135deg, #ffc107 0%, #ffb300 100%);
}

/* Card Shadows */
.card {
    transition: transform 0.2s ease, box-shadow 0.2s ease;
}

.card:hover {
    transform: translateY(-2px);
    box-shadow: 0 0.5rem 1.5rem rgba(0, 0, 0, 0.1) !important;
}

/* Buttons */
.btn {
    border-radius: 8px;
    font-weight: 500;
    transition: all 0.2s ease;
}

.btn:hover {
    transform: translateY(-1px);
}

/* Animations */
@keyframes fadeIn {
    from {
        opacity: 0;
        transform: translateY(10px);
    }
    to {
        opacity: 1;
        transform: translateY(0);
    }
}

.card {
    animation: fadeIn 0.3s ease-out;
}

/* Responsive */
@media (max-width: 768px) {
    .icon-box {
        width: 48px;
        height: 48px;
    }
    
    .confirmation-input {
        font-size: 1.25rem;
    }
    
    .preview-ribbon {
        width: 40px;
        top: 5px;
        right: 5px;
    }
}
</style>