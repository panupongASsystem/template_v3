<?php
// Helper functions for display
if (!function_exists('get_status_badge_class')) {
    function get_status_badge_class($status) {
        return $status === 'active' ? 'success' : 'secondary';
    }
}

if (!function_exists('get_status_display_text')) {
    function get_status_display_text($status) {
        return $status === 'active' ? 'ใช้งาน' : 'ไม่ใช้งาน';
    }
}
?>

<!-- SweetAlert2 -->
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

<style>
/* ===== ESV MANAGEMENT STYLES ===== */
.esv-manage-page {
    --esv-primary-color: #8b9cc7;
    --esv-primary-light: #a5b4d0;
    --esv-success-color: #10b981;
    --esv-warning-color: #f59e0b;
    --esv-danger-color: #ef4444;
    --esv-white: #ffffff;
    --esv-gray-50: #f9fafb;
    --esv-gray-100: #f3f4f6;
    --esv-gray-200: #e5e7eb;
    --esv-gray-600: #4b5563;
    --esv-gray-700: #374151;
    --esv-gray-800: #1f2937;
    --esv-shadow-md: 0 4px 6px -1px rgb(0 0 0 / 0.1), 0 2px 4px -2px rgb(0 0 0 / 0.1);
    --esv-shadow-lg: 0 10px 15px -3px rgb(0 0 0 / 0.1), 0 4px 6px -4px rgb(0 0 0 / 0.1);
    --esv-border-radius: 12px;
    --esv-transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
}

.esv-manage-page {
    background: linear-gradient(135deg, #f8fafc 0%, #f1f5f9 100%);
    min-height: 100vh;
    padding: 1.5rem;
}

.esv-manage-container {
    max-width: 1600px;
    margin: 0 auto;
}

/* ===== PAGE HEADER ===== */
.esv-manage-header {
    background: linear-gradient(135deg, var(--esv-primary-color) 0%, var(--esv-primary-light) 100%);
    color: white;
    padding: 2rem;
    border-radius: var(--esv-border-radius);
    margin-bottom: 2rem;
    box-shadow: var(--esv-shadow-md);
    position: relative;
    overflow: hidden;
}

.esv-manage-header::before {
    content: '';
    position: absolute;
    top: -50%;
    right: -20%;
    width: 300px;
    height: 300px;
    background: radial-gradient(circle, rgba(255,255,255,0.1) 0%, transparent 70%);
    border-radius: 50%;
}

.esv-manage-header h1 {
    font-size: 1.75rem;
    font-weight: 600;
    margin: 0;
    position: relative;
    z-index: 1;
    color: #ffffff !important;
}

.esv-manage-header .btn-back {
    position: absolute;
    top: 1.5rem;
    right: 2rem;
    background: rgba(255, 255, 255, 0.15);
    color: white;
    border: 1px solid rgba(255, 255, 255, 0.3);
    padding: 0.5rem 1rem;
    border-radius: 8px;
    text-decoration: none;
    font-size: 0.875rem;
    transition: var(--esv-transition);
    backdrop-filter: blur(10px);
    z-index: 2;
}

.esv-manage-header .btn-back:hover {
    background: rgba(255, 255, 255, 0.25);
    color: white;
    border-color: rgba(255, 255, 255, 0.5);
}

/* ===== MAIN CONTENT ===== */
.esv-manage-content {
    background: var(--esv-white);
    border-radius: var(--esv-border-radius);
    box-shadow: var(--esv-shadow-md);
    overflow: hidden;
    border: 1px solid var(--esv-gray-200);
}

.esv-content-header {
    background: var(--esv-gray-50);
    padding: 1.5rem 2rem;
    border-bottom: 1px solid var(--esv-gray-200);
    display: flex;
    justify-content: space-between;
    align-items: center;
}

.esv-header-buttons {
    display: flex;
    align-items: center;
    gap: 0.5rem;
}

.esv-content-header h5 {
    margin: 0;
    font-size: 1.25rem;
    font-weight: 600;
    color: var(--esv-gray-800);
}

.esv-btn {
    padding: 0.75rem 1.5rem;
    border-radius: 8px;
    font-weight: 600;
    font-size: 0.875rem;
    transition: var(--esv-transition);
    border: none;
    cursor: pointer;
    text-decoration: none;
    display: inline-flex;
    align-items: center;
    gap: 0.5rem;
}

.esv-btn-primary {
    background: linear-gradient(135deg, var(--esv-primary-color), var(--esv-primary-light));
    color: white;
}

.esv-btn-primary:hover {
    transform: translateY(-1px);
    box-shadow: var(--esv-shadow-lg);
    color: white;
}

.esv-btn-success {
    background: linear-gradient(135deg, var(--esv-success-color), #34d399);
    color: white;
}

.esv-btn-success:hover {
    transform: translateY(-1px);
    box-shadow: var(--esv-shadow-lg);
    color: white;
}

.esv-btn-danger {
    background: linear-gradient(135deg, var(--esv-danger-color), #f87171);
    color: white;
}

.esv-btn-danger:hover {
    transform: translateY(-1px);
    box-shadow: var(--esv-shadow-lg);
    color: white;
}

.esv-btn-secondary {
    background: linear-gradient(135deg, #8b9cc7, #a5b4d0);
    color: white;
    opacity: 0.8;
}

.esv-btn-secondary:hover {
    transform: translateY(-1px);
    box-shadow: var(--esv-shadow-lg);
    color: white;
    opacity: 1;
}

.esv-btn-sm {
    padding: 0.5rem 1rem;
    font-size: 0.8rem;
}

/* ===== TABLE STYLES ===== */
.esv-table-container {
    padding: 2rem;
}

.esv-table {
    width: 100%;
    border-collapse: collapse;
    margin: 0;
}

.esv-table th {
    background: var(--esv-gray-50);
    color: var(--esv-gray-700);
    font-weight: 600;
    padding: 1rem;
    text-align: left;
    border-bottom: 2px solid var(--esv-gray-200);
    font-size: 0.875rem;
}

.esv-table td {
    padding: 1rem;
    border-bottom: 1px solid var(--esv-gray-200);
    vertical-align: middle;
    font-size: 0.875rem;
}

.esv-table tbody tr:hover {
    background: var(--esv-gray-50);
}

.esv-icon-preview {
    display: inline-flex;
    align-items: center;
    justify-content: center;
    width: 32px;
    height: 32px;
    border-radius: 6px;
    font-size: 1rem;
    margin-right: 0.5rem;
}

.esv-color-preview {
    display: inline-block;
    width: 24px;
    height: 24px;
    border-radius: 50%;
    border: 2px solid var(--esv-gray-300);
    margin-right: 0.5rem;
    box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
}

.esv-action-buttons {
    display: flex;
    gap: 0.5rem;
}

/* ===== COLOR PRESET STYLES ===== */
.color-presets {
    margin-top: 0.5rem;
}

.color-preset {
    width: 25px;
    height: 25px;
    border-radius: 50%;
    border: 2px solid var(--esv-gray-300);
    cursor: pointer;
    transition: var(--esv-transition);
    position: relative;
    overflow: hidden;
}

.color-preset:hover {
    transform: scale(1.1);
    border-color: var(--esv-gray-600);
}

.color-preset.active {
    border-color: var(--esv-gray-800);
    box-shadow: 0 0 0 2px rgba(139, 156, 199, 0.3);
}

.color-preset::after {
    content: '';
    position: absolute;
    top: 50%;
    left: 50%;
    transform: translate(-50%, -50%);
    width: 8px;
    height: 8px;
    background: white;
    border-radius: 50%;
    opacity: 0;
    transition: var(--esv-transition);
}

.color-preset.active::after {
    opacity: 1;
}

.form-control-color {
    border-radius: 8px;
    padding: 0;
    border: 2px solid var(--esv-gray-200);
}

.form-control-color:focus {
    border-color: var(--esv-primary-color);
    box-shadow: 0 0 0 3px rgba(139, 156, 199, 0.1);
}

.color-preview-circle {
    width: 28px;
    height: 28px;
    border-radius: 50%;
    border: 2px solid var(--esv-gray-300);
    box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
    transition: var(--esv-transition);
}

/* ===== MODAL STYLES ===== */
.modal-content {
    border-radius: var(--esv-border-radius);
    border: none;
    box-shadow: var(--esv-shadow-lg);
}

.modal-header {
    background: var(--esv-gray-50);
    border-bottom: 1px solid var(--esv-gray-200);
    border-radius: var(--esv-border-radius) var(--esv-border-radius) 0 0;
}

.modal-title {
    font-weight: 600;
    color: var(--esv-gray-800);
}

.form-label {
    font-weight: 600;
    color: var(--esv-gray-700);
    margin-bottom: 0.5rem;
}

.form-control, .form-select {
    border: 2px solid var(--esv-gray-200);
    border-radius: 8px;
    padding: 0.75rem;
    transition: var(--esv-transition);
}

.form-control:focus, .form-select:focus {
    border-color: var(--esv-primary-color);
    box-shadow: 0 0 0 3px rgba(139, 156, 199, 0.1);
    outline: none;
}

/* ===== RESPONSIVE ===== */
@media (max-width: 768px) {
    .esv-manage-page {
        padding: 1rem;
    }
    
    .esv-manage-header .btn-back {
        position: relative;
        top: auto;
        right: auto;
        margin-top: 1rem;
        display: block;
        text-align: center;
    }
    
    .esv-content-header {
        flex-direction: column;
        gap: 1rem;
        align-items: stretch;
    }
    
    .esv-header-buttons {
        justify-content: center;
        flex-wrap: wrap;
    }
    
    .esv-table-container {
        padding: 1rem;
        overflow-x: auto;
    }
    
    .esv-table {
        min-width: 600px;
    }
}
</style>

<div class="esv-manage-page">
    <div class="esv-manage-container">
        <!-- Page Header -->
        <header class="esv-manage-header">
            <h1><i class="fas fa-file-alt me-3"></i>จัดการประเภทเอกสาร</h1>
            <a href="<?= site_url('Esv_ods/admin_management') ?>" class="btn-back">
                <i class="fas fa-arrow-left me-2"></i>กลับหน้าจัดการเอกสาร
            </a>
        </header>

        <!-- Flash Messages -->
        <?php if (!empty($success_message)): ?>
            <div class="alert alert-success alert-dismissible fade show" role="alert">
                <i class="fas fa-check-circle me-2"></i><?= $success_message ?>
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        <?php endif; ?>

        <?php if (!empty($error_message)): ?>
            <div class="alert alert-danger alert-dismissible fade show" role="alert">
                <i class="fas fa-exclamation-triangle me-2"></i><?= $error_message ?>
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        <?php endif; ?>

        <!-- Main Content -->
        <div class="esv-manage-content">
            <div class="esv-content-header">
                <h5><i class="fas fa-list me-2"></i>รายการประเภทเอกสาร</h5>
                
                <div class="esv-header-buttons">
                    <?php if ($can_add ?? false): ?>
                        <button type="button" class="esv-btn esv-btn-primary" data-bs-toggle="modal" data-bs-target="#documentTypeModal" onclick="openAddModal()">
                            <i class="fas fa-plus"></i>เพิ่มประเภทเอกสาร
                        </button>
                    <?php endif; ?>
                    
                    <a href="<?= site_url('Esv_ods/manage_categories') ?>" class="esv-btn esv-btn-secondary">
                        <i class="fas fa-folder-open me-1"></i>จัดการหมวดหมู่เอกสาร
                    </a>
                    
                    <a href="<?= site_url('Esv_ods/manage_forms') ?>" class="esv-btn esv-btn-secondary">
                        <i class="fas fa-tags me-1"></i>จัดการแบบฟอร์ม
                    </a>
                </div>
            </div>
            
            <div class="esv-table-container">
                <?php if (empty($document_types)): ?>
                    <div class="text-center py-5">
                        <i class="fas fa-file-alt fa-3x text-muted mb-3"></i>
                        <h5 class="text-muted">ไม่พบข้อมูลประเภทเอกสาร</h5>
                        <p class="text-muted">กรุณาเพิ่มประเภทเอกสารใหม่</p>
                    </div>
                <?php else: ?>
                    <table class="esv-table">
                        <thead>
                            <tr>
                                <th style="width: 8%;">ลำดับ</th>
                                <th style="width: 6%;">ไอคอน</th>
                                <th style="width: 25%;">ชื่อประเภท</th>
                                <th style="width: 35%;">คำอธิบาย</th>
                                <th style="width: 8%;">สี</th>
                                <th style="width: 8%;">สถานะ</th>
                                <th style="width: 10%;">การจัดการ</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($document_types as $index => $type): ?>
                                <tr>
                                    <td class="text-center">
                                        <strong><?= $type->esv_type_order ?: ($index + 1) ?></strong>
                                    </td>
                                    <td class="text-center">
                                        <div class="esv-icon-preview" style="background-color: <?= $type->esv_type_color ?: '#8b9cc7' ?>;">
                                            <i class="<?= $type->esv_type_icon ?: 'fas fa-file-alt' ?>" style="color: white;"></i>
                                        </div>
                                    </td>
                                    <td>
                                        <strong><?= htmlspecialchars($type->esv_type_name) ?></strong>
                                    </td>
                                    <td>
                                        <?= htmlspecialchars($type->esv_type_description ?: '-') ?>
                                    </td>
                                    <td class="text-center">
                                        <div class="d-flex align-items-center">
                                            <div class="esv-color-preview" style="background-color: <?= $type->esv_type_color ?: '#8b9cc7' ?>;"></div>
                                            <small class="text-muted"><?= $type->esv_type_color ?: '#8b9cc7' ?></small>
                                        </div>
                                    </td>
                                    <td class="text-center">
                                        <span class="badge bg-<?= get_status_badge_class($type->esv_type_status) ?>">
                                            <?= get_status_display_text($type->esv_type_status) ?>
                                        </span>
                                    </td>
                                    <td>
                                        <div class="esv-action-buttons">
                                            <?php if ($can_edit ?? false): ?>
                                                <button type="button" class="esv-btn esv-btn-success esv-btn-sm" 
                                                        onclick="editDocumentType(<?= htmlspecialchars(json_encode($type), ENT_QUOTES) ?>)"
                                                        title="แก้ไข">
                                                    <i class="fas fa-edit"></i>
                                                </button>
                                            <?php endif; ?>
                                            
                                            <?php if ($can_delete ?? false): ?>
                                                <button type="button" class="esv-btn esv-btn-danger esv-btn-sm" 
                                                        onclick="deleteDocumentType('<?= $type->esv_type_id ?>', '<?= htmlspecialchars($type->esv_type_name, ENT_QUOTES) ?>')"
                                                        title="ลบ">
                                                    <i class="fas fa-trash"></i>
                                                </button>
                                            <?php endif; ?>
                                        </div>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                <?php endif; ?>
            </div>
        </div>
    </div>
</div>

<!-- Modal เพิ่ม/แก้ไขประเภทเอกสาร -->
<div class="modal fade" id="documentTypeModal" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="modalTitle">
                    <i class="fas fa-file-alt me-2"></i>เพิ่มประเภทเอกสาร
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form id="documentTypeForm">
                <div class="modal-body">
                    <input type="hidden" id="typeId" name="type_id">
                    
                    <div class="row">
                        <div class="col-md-8">
                            <div class="mb-3">
                                <label class="form-label">ชื่อประเภทเอกสาร <span class="text-danger">*</span></label>
                                <input type="text" class="form-control" id="typeName" name="type_name" required
                                       placeholder="เช่น หนังสือราชการ, คำร้องทั่วไป">
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="mb-3">
                                <label class="form-label">ลำดับ</label>
                                <input type="number" class="form-control" id="typeOrder" name="type_order" 
                                       min="0" value="0">
                            </div>
                        </div>
                    </div>
                    
                    <div class="mb-3">
                        <label class="form-label">คำอธิบาย</label>
                        <textarea class="form-control" id="typeDescription" name="type_description" rows="3"
                                  placeholder="คำอธิบายประเภทเอกสาร"></textarea>
                    </div>
                    
                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="form-label">ไอคอน</label>
                                <select class="form-select" id="typeIcon" name="type_icon">
                                    <option value="fas fa-file-alt" data-icon="fas fa-file-alt">📄 เอกสารทั่วไป</option>
                                    <option value="fas fa-file-signature" data-icon="fas fa-file-signature">📝 คำร้องขอ</option>
                                    <option value="fas fa-file-contract" data-icon="fas fa-file-contract">📋 ใบสมัคร</option>
                                    <option value="fas fa-certificate" data-icon="fas fa-certificate">🏆 หนังสือรับรอง</option>
                                    <option value="fas fa-file-invoice" data-icon="fas fa-file-invoice">🧾 ใบเสร็จ</option>
                                    <option value="fas fa-clipboard-list" data-icon="fas fa-clipboard-list">📋 รายการ</option>
                                    <option value="fas fa-scroll" data-icon="fas fa-scroll">📜 ประกาศ</option>
                                    <option value="fas fa-stamp" data-icon="fas fa-stamp">🔖 อนุมัติ</option>
                                    <option value="fas fa-balance-scale" data-icon="fas fa-balance-scale">⚖️ กฎหมาย</option>
                                    <option value="fas fa-handshake" data-icon="fas fa-handshake">🤝 ข้อตกลง</option>
                                    <option value="fas fa-exclamation-triangle" data-icon="fas fa-exclamation-triangle">⚠️ เร่งด่วน</option>
                                    <option value="fas fa-clock" data-icon="fas fa-clock">⏰ จับเวลา</option>
                                </select>
                                <small class="form-text text-muted">เลือกไอคอนที่เหมาะสมกับประเภทเอกสาร</small>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="form-label">สี</label>
                                <div class="d-flex align-items-center gap-2">
                                    <input type="color" class="form-control form-control-color" id="typeColor" name="type_color" 
                                           value="#8b9cc7" style="width: 60px; height: 40px;">
                                    <div class="flex-grow-1">
                                        <div class="color-presets d-flex gap-1 flex-wrap">
                                            <button type="button" class="color-preset" data-color="#8b9cc7" style="background-color: #8b9cc7;" title="น้ำเงินเทา"></button>
                                            <button type="button" class="color-preset" data-color="#10b981" style="background-color: #10b981;" title="เขียว"></button>
                                            <button type="button" class="color-preset" data-color="#f59e0b" style="background-color: #f59e0b;" title="ส้ม"></button>
                                            <button type="button" class="color-preset" data-color="#ef4444" style="background-color: #ef4444;" title="แดง"></button>
                                            <button type="button" class="color-preset" data-color="#8b5cf6" style="background-color: #8b5cf6;" title="ม่วง"></button>
                                            <button type="button" class="color-preset" data-color="#06b6d4" style="background-color: #06b6d4;" title="ฟ้า"></button>
                                            <button type="button" class="color-preset" data-color="#84cc16" style="background-color: #84cc16;" title="เขียวสด"></button>
                                            <button type="button" class="color-preset" data-color="#f97316" style="background-color: #f97316;" title="ส้มเข้ม"></button>
                                        </div>
                                    </div>
                                </div>
                                <small class="form-text text-muted">เลือกสีหรือใช้ตัวเลือกที่กำหนดไว้</small>
                            </div>
                        </div>
                    </div>
                    
                    <div class="mb-3">
                        <label class="form-label">สถานะ</label>
                        <select class="form-select" id="typeStatus" name="type_status">
                            <option value="active">ใช้งาน</option>
                            <option value="inactive">ไม่ใช้งาน</option>
                        </select>
                    </div>
                    
                    <!-- Preview -->
                    <div class="mb-3">
                        <label class="form-label">ตัวอย่าง</label>
                        <div class="p-3 bg-light rounded">
                            <div class="d-flex align-items-center">
                                <div id="iconPreview" class="esv-icon-preview me-3" style="background-color: #8b9cc7;">
                                    <i class="fas fa-file-alt" style="color: white;"></i>
                                </div>
                                <div class="flex-grow-1">
                                    <strong id="namePreview">ชื่อประเภทเอกสาร</strong>
                                    <div><small class="text-muted" id="descPreview">คำอธิบาย</small></div>
                                </div>
                                <div class="ms-3">
                                    <div class="d-flex align-items-center">
                                        <span class="me-2 text-muted small">สี:</span>
                                        <div id="colorPreviewCircle" class="color-preview-circle" style="background-color: #8b9cc7;"></div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                        <i class="fas fa-times me-1"></i>ยกเลิก
                    </button>
                    <button type="submit" class="btn btn-primary">
                        <i class="fas fa-save me-1"></i>บันทึก
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Bootstrap 5 JS -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>

<script>
// ===================================================================
// *** DOCUMENT TYPE MANAGEMENT ***
// ===================================================================

const EsvDocTypeConfig = {
    saveUrl: '<?= site_url("Esv_ods/save_document_type") ?>',
    deleteUrl: '<?= site_url("Esv_ods/delete_document_type") ?>'
};

/**
 * เปิด Modal เพิ่มประเภทเอกสาร
 */
function openAddModal() {
    document.getElementById('modalTitle').innerHTML = '<i class="fas fa-file-alt me-2"></i>เพิ่มประเภทเอกสาร';
    document.getElementById('documentTypeForm').reset();
    document.getElementById('typeId').value = '';
    document.getElementById('typeColor').value = '#8b9cc7';
    document.getElementById('typeIcon').value = 'fas fa-file-alt';
    
    // Reset color presets
    updateColorPresets('#8b9cc7');
    updatePreview();
}

/**
 * แก้ไขประเภทเอกสาร
 */
function editDocumentType(typeData) {
    document.getElementById('modalTitle').innerHTML = '<i class="fas fa-edit me-2"></i>แก้ไขประเภทเอกสาร';
    
    document.getElementById('typeId').value = typeData.esv_type_id;
    document.getElementById('typeName').value = typeData.esv_type_name;
    document.getElementById('typeDescription').value = typeData.esv_type_description || '';
    document.getElementById('typeIcon').value = typeData.esv_type_icon || 'fas fa-file-alt';
    document.getElementById('typeColor').value = typeData.esv_type_color || '#8b9cc7';
    document.getElementById('typeOrder').value = typeData.esv_type_order || 0;
    document.getElementById('typeStatus').value = typeData.esv_type_status || 'active';
    
    // Update color presets
    updateColorPresets(typeData.esv_type_color || '#8b9cc7');
    updatePreview();
    
    const modal = new bootstrap.Modal(document.getElementById('documentTypeModal'));
    modal.show();
}

/**
 * ลบประเภทเอกสาร
 */
function deleteDocumentType(typeId, typeName) {
    Swal.fire({
        title: 'ยืนยันการลบ',
        text: `คุณต้องการลบประเภทเอกสาร "${typeName}" หรือไม่?`,
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#ef4444',
        cancelButtonColor: '#6b7280',
        confirmButtonText: 'ใช่, ลบเลย',
        cancelButtonText: 'ยกเลิก',
        reverseButtons: true
    }).then((result) => {
        if (result.isConfirmed) {
            performDeleteDocumentType(typeId);
        }
    });
}

/**
 * ดำเนินการลบประเภทเอกสาร
 */
function performDeleteDocumentType(typeId) {
    Swal.fire({
        title: 'กำลังลบ...',
        allowOutsideClick: false,
        allowEscapeKey: false,
        showConfirmButton: false,
        didOpen: () => {
            Swal.showLoading();
        }
    });
    
    const formData = new FormData();
    formData.append('type_id', typeId);
    
    fetch(EsvDocTypeConfig.deleteUrl, {
        method: 'POST',
        body: formData,
        headers: {
            'X-Requested-With': 'XMLHttpRequest'
        }
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            Swal.fire({
                title: 'ลบสำเร็จ!',
                text: data.message,
                icon: 'success',
                timer: 2000,
                showConfirmButton: false
            }).then(() => {
                location.reload();
            });
        } else {
            Swal.fire({
                title: 'เกิดข้อผิดพลาด',
                text: data.message,
                icon: 'error'
            });
        }
    })
    .catch(error => {
        console.error('Delete error:', error);
        Swal.fire({
            title: 'เกิดข้อผิดพลาด',
            text: 'ไม่สามารถเชื่อมต่อกับเซิร์ฟเวอร์ได้',
            icon: 'error'
        });
    });
}

/**
 * อัปเดตตัวอย่าง
 */
function updatePreview() {
    const name = document.getElementById('typeName').value || 'ชื่อประเภทเอกสาร';
    const description = document.getElementById('typeDescription').value || 'คำอธิบาย';
    const icon = document.getElementById('typeIcon').value || 'fas fa-file-alt';
    const color = document.getElementById('typeColor').value || '#8b9cc7';
    
    document.getElementById('namePreview').textContent = name;
    document.getElementById('descPreview').textContent = description;
    document.getElementById('iconPreview').style.backgroundColor = color;
    document.getElementById('iconPreview').innerHTML = `<i class="${icon}" style="color: white;"></i>`;
    
    // Update color preview circle
    document.getElementById('colorPreviewCircle').style.backgroundColor = color;
}

/**
 * อัปเดต Color Presets
 */
function updateColorPresets(selectedColor) {
    const presets = document.querySelectorAll('.color-preset');
    presets.forEach(preset => {
        if (preset.dataset.color === selectedColor) {
            preset.classList.add('active');
        } else {
            preset.classList.remove('active');
        }
    });
}

/**
 * จัดการ Form Submit
 */
document.getElementById('documentTypeForm').addEventListener('submit', function(e) {
    e.preventDefault();
    
    const formData = new FormData(this);
    
    Swal.fire({
        title: 'กำลังบันทึก...',
        allowOutsideClick: false,
        allowEscapeKey: false,
        showConfirmButton: false,
        didOpen: () => {
            Swal.showLoading();
        }
    });
    
    fetch(EsvDocTypeConfig.saveUrl, {
        method: 'POST',
        body: formData,
        headers: {
            'X-Requested-With': 'XMLHttpRequest'
        }
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            Swal.fire({
                title: 'บันทึกสำเร็จ!',
                text: data.message,
                icon: 'success',
                timer: 2000,
                showConfirmButton: false
            }).then(() => {
                location.reload();
            });
        } else {
            Swal.fire({
                title: 'เกิดข้อผิดพลาด',
                text: data.message,
                icon: 'error'
            });
        }
    })
    .catch(error => {
        console.error('Save error:', error);
        Swal.fire({
            title: 'เกิดข้อผิดพลาด',
            text: 'ไม่สามารถเชื่อมต่อกับเซิร์ฟเวอร์ได้',
            icon: 'error'
        });
    });
});

// ===================================================================
// *** EVENT LISTENERS ***
// ===================================================================

document.addEventListener('DOMContentLoaded', function() {
    // ตัวอย่างแบบเรียลไทม์
    document.getElementById('typeName').addEventListener('input', updatePreview);
    document.getElementById('typeDescription').addEventListener('input', updatePreview);
    document.getElementById('typeIcon').addEventListener('change', updatePreview);
    document.getElementById('typeColor').addEventListener('input', function() {
        updatePreview();
        updateColorPresets(this.value);
    });
    
    // Color preset click handlers
    document.querySelectorAll('.color-preset').forEach(preset => {
        preset.addEventListener('click', function() {
            const color = this.dataset.color;
            document.getElementById('typeColor').value = color;
            updateColorPresets(color);
            updatePreview();
        });
    });
    
    // อัปเดตตัวอย่างครั้งแรก
    updatePreview();
});

// ===================================================================
// *** FLASH MESSAGES ***
// ===================================================================

<?php if (isset($success_message) && !empty($success_message)): ?>
document.addEventListener('DOMContentLoaded', function() {
    Swal.fire({
        title: 'สำเร็จ!',
        text: <?= json_encode($success_message, JSON_UNESCAPED_UNICODE) ?>,
        icon: 'success',
        timer: 3000,
        showConfirmButton: false
    });
});
<?php endif; ?>

<?php if (isset($error_message) && !empty($error_message)): ?>
document.addEventListener('DOMContentLoaded', function() {
    Swal.fire({
        title: 'เกิดข้อผิดพลาด',
        text: <?= json_encode($error_message, JSON_UNESCAPED_UNICODE) ?>,
        icon: 'error'
    });
});
<?php endif; ?>
</script>