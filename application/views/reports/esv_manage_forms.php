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

if (!function_exists('format_file_size')) {
    function format_file_size($bytes) {
        if ($bytes >= 1048576) {
            return round($bytes / 1048576, 2) . ' MB';
        } elseif ($bytes >= 1024) {
            return round($bytes / 1024, 2) . ' KB';
        }
        return $bytes . ' B';
    }
}
?>

<!-- SweetAlert2 -->
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

<style>
/* ใช้ CSS เดียวกันกับหน้าจัดการประเภทเอกสาร */
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

.esv-btn-warning {
    background: linear-gradient(135deg, var(--esv-warning-color), #fbbf24);
    color: white;
}

.esv-btn-warning:hover {
    transform: translateY(-1px);
    box-shadow: var(--esv-shadow-lg);
    color: white;
}

.esv-btn-secondary {
    background: linear-gradient(135deg, #8b9cc7, #a5b4d0);
    color: white;
    opacity: 0.8;
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

.esv-action-buttons {
    display: flex;
    gap: 0.5rem;
    flex-wrap: wrap;
}

.esv-type-badge {
    background: linear-gradient(135deg, var(--esv-primary-color), var(--esv-primary-light));
    color: white;
    padding: 0.3rem 0.8rem;
    border-radius: 14px;
    font-size: 0.75rem;
    font-weight: 600;
    display: inline-block;
    min-width: 60px;
    text-align: center;
}

.esv-category-badge {
    background: linear-gradient(135deg, var(--esv-success-color), #34d399);
    color: white;
    padding: 0.3rem 0.8rem;
    border-radius: 14px;
    font-size: 0.75rem;
    font-weight: 600;
    display: inline-block;
    min-width: 60px;
    text-align: center;
}

.esv-file-info {
    display: flex;
    align-items: center;
    gap: 0.4rem;
}

.esv-file-icon {
    width: 20px;
    height: 20px;
    border-radius: 3px;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 0.65rem;
    color: white;
    font-weight: 600;
}

.esv-file-icon.pdf { background: #dc3545; }
.esv-file-icon.doc, .esv-file-icon.docx { background: #0d6efd; }
.esv-file-icon.xls, .esv-file-icon.xlsx { background: #198754; }
.esv-file-icon.default { background: var(--esv-gray-600); }

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

/* ===== FILE MANAGEMENT STYLES ===== */
.current-file-section {
    background: var(--esv-gray-50);
    border: 2px dashed var(--esv-gray-200);
    border-radius: 12px;
    padding: 1.5rem;
    text-align: center;
    margin-top: 1rem;
}

.current-file-info {
    display: flex;
    align-items: center;
    justify-content: center;
    gap: 1rem;
    margin-bottom: 1rem;
}

.current-file-icon {
    width: 48px;
    height: 48px;
    border-radius: 8px;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 1rem;
    color: white;
    font-weight: 600;
}

.file-actions {
    display: flex;
    gap: 0.5rem;
    justify-content: center;
    flex-wrap: wrap;
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
    
    .esv-content-header .d-flex {
        justify-content: center;
        flex-direction: column;
        gap: 0.5rem;
    }
    
    .esv-table-container {
        padding: 1rem;
        overflow-x: auto;
    }
    
    .esv-table {
        min-width: 1000px;
    }
    
    .esv-action-buttons {
        flex-direction: column;
        width: 100%;
    }
    
    .file-actions {
        flex-direction: column;
    }
}
</style>

<div class="esv-manage-page">
    <div class="esv-manage-container">
        <!-- Page Header -->
        <header class="esv-manage-header">
            <h1><i class="fas fa-file me-3"></i>จัดการแบบฟอร์ม</h1>
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
                <h5><i class="fas fa-list me-2"></i>รายการแบบฟอร์ม</h5>
                <div class="d-flex gap-2 flex-wrap">
                    <?php if ($can_add ?? false): ?>
                        <button type="button" class="esv-btn esv-btn-primary" data-bs-toggle="modal" data-bs-target="#formModal" onclick="openAddModal()">
                            <i class="fas fa-plus"></i>เพิ่มแบบฟอร์ม
                        </button>
                        
                        <a href="<?= site_url('Esv_ods/manage_document_types') ?>" class="esv-btn esv-btn-secondary">
                            <i class="fas fa-tags me-1"></i>จัดการประเภทเอกสาร
                        </a>
                        
                        <a href="<?= site_url('Esv_ods/manage_categories') ?>" class="esv-btn esv-btn-secondary">
                            <i class="fas fa-folder-open me-1"></i>จัดการหมวดหมู่เอกสาร
                        </a>
                    <?php endif; ?>
                </div>
            </div>
            
            <div class="esv-table-container">
                <?php if (empty($forms)): ?>
                    <div class="text-center py-5">
                        <i class="fas fa-file fa-3x text-muted mb-3"></i>
                        <h5 class="text-muted">ไม่พบข้อมูลแบบฟอร์ม</h5>
                        <p class="text-muted">กรุณาเพิ่มแบบฟอร์มใหม่</p>
                    </div>
                <?php else: ?>
                    <table class="esv-table">
                        <thead>
                            <tr>
                                <th style="width: 5%;">ลำดับ</th>
                                <th style="width: 25%;">ชื่อแบบฟอร์ม</th>
                                <th style="width: 14%;">ประเภทเอกสาร</th>
                                <th style="width: 14%;">หมวดหมู่</th>
                                <th style="width: 9%;">ไฟล์</th>
                                <th style="width: 7%;">ขนาดไฟล์</th>
                                <th style="width: 6%;">สถานะ</th>
                                <th style="width: 9%;">วันที่อัปเดต</th>
                                <th style="width: 16%;">การจัดการ</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($forms as $index => $form): ?>
                                <tr>
                                    <td class="text-center">
                                        <strong><?= $form->form_order ?: ($index + 1) ?></strong>
                                    </td>
                                    <td>
                                        <div class="d-flex align-items-center">
                                            <div class="esv-icon-preview" style="background-color: <?= $form->type_color ?: '#8b9cc7' ?>;">
                                                <i class="<?= $form->type_icon ?: 'fas fas fa-file' ?>" style="color: white;"></i>
                                            </div>
                                            <div>
                                                <strong><?= htmlspecialchars($form->form_name) ?></strong>
                                                <?php if (!empty($form->form_description)): ?>
                                                    <br><small class="text-muted"><?= htmlspecialchars(mb_substr($form->form_description, 0, 50)) ?><?= mb_strlen($form->form_description) > 50 ? '...' : '' ?></small>
                                                <?php endif; ?>
                                            </div>
                                        </div>
                                    </td>
                                    <td>
                                        <?php if (!empty($form->type_name)): ?>
                                            <span class="esv-type-badge">
                                                <?= htmlspecialchars($form->type_name) ?>
                                            </span>
                                        <?php else: ?>
                                            <span class="text-muted">ทุกประเภท</span>
                                        <?php endif; ?>
                                    </td>
                                    <td>
                                        <?php if (!empty($form->category_name)): ?>
                                            <span class="esv-category-badge">
                                                <?= htmlspecialchars($form->category_name) ?>
                                            </span>
                                        <?php else: ?>
                                            <span class="text-muted">ทุกหมวดหมู่</span>
                                        <?php endif; ?>
                                    </td>
                                    <td>
                                        <?php if (!empty($form->form_file)): ?>
                                            <div class="esv-file-info">
                                                <?php 
                                                $file_ext = strtolower(pathinfo($form->form_file, PATHINFO_EXTENSION));
                                                $icon_class = in_array($file_ext, ['pdf']) ? 'pdf' : 
                                                             (in_array($file_ext, ['doc', 'docx']) ? 'doc' : 
                                                             (in_array($file_ext, ['xls', 'xlsx']) ? 'xls' : 'default'));
                                                ?>
                                                <div class="esv-file-icon <?= $icon_class ?>">
                                                    <?= strtoupper($file_ext) ?>
                                                </div>
                                                <small><?= pathinfo($form->form_file, PATHINFO_FILENAME) ?></small>
                                            </div>
                                        <?php else: ?>
                                            <span class="text-muted">ไม่มีไฟล์</span>
                                        <?php endif; ?>
                                    </td>
                                    <td class="text-center">
                                        <?php if (!empty($form->form_file_size)): ?>
                                            <small><?= format_file_size($form->form_file_size) ?></small>
                                        <?php else: ?>
                                            <span class="text-muted">-</span>
                                        <?php endif; ?>
                                    </td>
                                    <td class="text-center">
                                        <span class="badge bg-<?= get_status_badge_class($form->form_status) ?>">
                                            <?= get_status_display_text($form->form_status) ?>
                                        </span>
                                    </td>
                                    <td class="text-center">
                                        <small class="text-muted">
                                            <?= !empty($form->form_updated_at) ? date('d/m/Y H:i', strtotime($form->form_updated_at)) : date('d/m/Y H:i', strtotime($form->form_created_at)) ?>
                                        </small>
                                    </td>
                                    <td>
                                        <div class="esv-action-buttons">
                                            <?php if (!empty($form->form_file)): ?>
                                                <?php $file_ext = strtolower(pathinfo($form->form_file, PATHINFO_EXTENSION)); ?>
                                                <?php if ($file_ext === 'pdf'): ?>
                                                    <button type="button" class="esv-btn esv-btn-warning esv-btn-sm" 
                                                            onclick="viewFormPDF('<?= $form->form_id ?>')"
                                                            title="ดูไฟล์ PDF">
                                                        <i class="fas fa-eye"></i>
                                                    </button>
                                                <?php endif; ?>
                                                
                                                <a href="<?= site_url('Esv_ods/download_form/' . $form->form_id) ?>" 
                                                   class="esv-btn esv-btn-success esv-btn-sm" 
                                                   title="ดาวน์โหลด">
                                                    <i class="fas fa-download"></i>
                                                </a>
                                            <?php endif; ?>
                                            
                                            <?php if ($can_edit ?? false): ?>
                                                <button type="button" class="esv-btn esv-btn-primary esv-btn-sm" 
                                                        onclick="editForm(<?= htmlspecialchars(json_encode($form), ENT_QUOTES) ?>)"
                                                        title="แก้ไข">
                                                    <i class="fas fa-edit"></i>
                                                </button>
                                            <?php endif; ?>
                                            
                                            <?php if ($can_delete ?? false): ?>
                                                <button type="button" class="esv-btn esv-btn-danger esv-btn-sm" 
                                                        onclick="deleteForm('<?= $form->form_id ?>', '<?= htmlspecialchars($form->form_name, ENT_QUOTES) ?>')"
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

<!-- Modal เพิ่ม/แก้ไขแบบฟอร์ม -->
<div class="modal fade" id="formModal" tabindex="-1">
    <div class="modal-dialog modal-xl">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="modalTitle">
                    <i class="fas fa-file me-2"></i>เพิ่มแบบฟอร์ม
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form id="formForm" enctype="multipart/form-data">
                <div class="modal-body">
                    <input type="hidden" id="formId" name="form_id">
                    
                    <div class="row">
                        <div class="col-md-8">
                            <div class="mb-3">
                                <label class="form-label">ชื่อแบบฟอร์ม <span class="text-danger">*</span></label>
                                <input type="text" class="form-control" id="formName" name="form_name" required
                                       placeholder="เช่น แบบฟอร์มคำร้องขอใบรับรอง">
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="mb-3">
                                <label class="form-label">ลำดับ</label>
                                <input type="number" class="form-control" id="formOrder" name="form_order" 
                                       min="0" value="0">
                            </div>
                        </div>
                    </div>
                    
                    <div class="mb-3">
                        <label class="form-label">คำอธิบาย</label>
                        <textarea class="form-control" id="formDescription" name="form_description" rows="3"
                                  placeholder="คำอธิบายแบบฟอร์ม"></textarea>
                    </div>
                    
                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="form-label">ประเภทเอกสาร</label>
                                <select class="form-select" id="formTypeId" name="form_type_id">
                                    <option value="">ทุกประเภท</option>
                                    <?php if (!empty($document_types)): ?>
                                        <?php foreach ($document_types as $type): ?>
                                            <option value="<?= $type->esv_type_id ?>"><?= htmlspecialchars($type->esv_type_name) ?></option>
                                        <?php endforeach; ?>
                                    <?php endif; ?>
                                </select>
                                <small class="form-text text-muted">เลือกประเภทเอกสารที่เกี่ยวข้อง</small>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="form-label">หมวดหมู่เอกสาร</label>
                                <select class="form-select" id="formCategoryId" name="form_category_id">
                                    <option value="">ทุกหมวดหมู่</option>
                                    <?php if (!empty($categories)): ?>
                                        <?php foreach ($categories as $category): ?>
                                            <option value="<?= $category->esv_category_id ?>"><?= htmlspecialchars($category->esv_category_name) ?></option>
                                        <?php endforeach; ?>
                                    <?php endif; ?>
                                </select>
                                <small class="form-text text-muted">เลือกหมวดหมู่เอกสารที่เกี่ยวข้อง</small>
                            </div>
                        </div>
                    </div>
                    
                    <!-- แสดงไฟล์ปัจจุบัน (สำหรับการแก้ไข) -->
                    <div id="currentFileSection" style="display: none;" class="mb-3">
                        <label class="form-label">ไฟล์ปัจจุบัน</label>
                        <div class="current-file-section">
                            <div class="current-file-info">
                                <div id="currentFileIcon" class="current-file-icon pdf">
                                    PDF
                                </div>
                                <div>
                                    <strong id="currentFileName">ชื่อไฟล์</strong>
                                    <br><small class="text-muted" id="currentFileSize">ขนาดไฟล์</small>
                                </div>
                            </div>
                            <div class="file-actions">
                                <button type="button" class="esv-btn esv-btn-warning esv-btn-sm" 
                                        id="viewCurrentFileBtn" onclick="viewCurrentFormPDF()">
                                    <i class="fas fa-eye me-1"></i>ดูไฟล์
                                </button>
                                <a href="#" class="esv-btn esv-btn-success esv-btn-sm" 
                                   id="downloadCurrentFileBtn" target="_blank">
                                    <i class="fas fa-download me-1"></i>ดาวน์โหลด
                                </a>
                            </div>
                        </div>
                    </div>
                    
                    <div class="row">
                        <div class="col-md-8">
                            <div class="mb-3">
                                <label class="form-label">ไฟล์แบบฟอร์ม <span class="text-danger" id="fileRequired">*</span></label>
                                <input type="file" class="form-control" id="formFile" name="form_file" 
                                       accept=".pdf,.doc,.docx,.xls,.xlsx">
                                <small class="form-text text-muted">รองรับไฟล์: PDF, DOC, DOCX, XLS, XLSX (ขนาดไม่เกิน 5MB)</small>
                                <small class="form-text text-info" id="fileReplaceNote" style="display: none;">
                                    <i class="fas fa-info-circle me-1"></i>
                                    เลือกไฟล์ใหม่เพื่อแทนที่ไฟล์เดิม (ถ้าไม่เลือกจะใช้ไฟล์เดิม)
                                </small>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="mb-3">
                                <label class="form-label">สถานะ</label>
                                <select class="form-select" id="formStatus" name="form_status">
                                    <option value="active">ใช้งาน</option>
                                    <option value="inactive">ไม่ใช้งาน</option>
                                </select>
                            </div>
                        </div>
                    </div>
                    
                    <!-- Preview -->
                    <div class="mb-3">
                        <label class="form-label">ตัวอย่าง</label>
                        <div class="p-3 bg-light rounded">
                            <div class="d-flex align-items-center">
                                <div id="iconPreview" class="esv-icon-preview me-3" style="background-color: #8b9cc7;">
                                    <i class="fas fa-file" style="color: white;"></i>
                                </div>
                                <div class="flex-grow-1">
                                    <div class="d-flex align-items-center mb-1">
                                        <strong id="namePreview" class="me-2">ชื่อแบบฟอร์ม</strong>
                                        <span id="typePreview" class="esv-type-badge">ทุกประเภท</span>
                                        <span id="categoryPreview" class="esv-category-badge ms-2">ทุกหมวดหมู่</span>
                                    </div>
                                    <small class="text-muted" id="descPreview">คำอธิบาย</small>
                                </div>
                                <div class="ms-3">
                                    <div id="filePreview" class="esv-file-info">
                                        <div class="esv-file-icon default">
                                            FILE
                                        </div>
                                        <small>ไม่มีไฟล์</small>
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
// *** FORM MANAGEMENT ***
// ===================================================================

const EsvFormConfig = {
    saveUrl: '<?= site_url("Esv_ods/save_form") ?>',
    deleteUrl: '<?= site_url("Esv_ods/delete_form") ?>',
    viewFileUrl: '<?= site_url("Esv_ods/view_form_file") ?>',
    downloadUrl: '<?= site_url("Esv_ods/download_form") ?>'
};

// ข้อมูลประเภทเอกสารและหมวดหมู่สำหรับ JavaScript
const documentTypes = <?= json_encode($document_types ?? [], JSON_UNESCAPED_UNICODE) ?>;
const categories = <?= json_encode($categories ?? [], JSON_UNESCAPED_UNICODE) ?>;

// ตัวแปรเก็บข้อมูลไฟล์ปัจจุบัน
let currentEditingForm = null;

/**
 * เปิด Modal เพิ่มแบบฟอร์ม
 */
function openAddModal() {
    currentEditingForm = null;
    
    document.getElementById('modalTitle').innerHTML = '<i class="fas fa-file me-2"></i>เพิ่มแบบฟอร์ม';
    document.getElementById('formForm').reset();
    document.getElementById('formId').value = '';
    document.getElementById('formFile').required = true;
    document.getElementById('fileRequired').style.display = 'inline';
    document.getElementById('fileReplaceNote').style.display = 'none';
    document.getElementById('currentFileSection').style.display = 'none';
    
    updatePreview();
}

/**
 * แก้ไขแบบฟอร์ม
 */
function editForm(formData) {
    currentEditingForm = formData;
    
    document.getElementById('modalTitle').innerHTML = '<i class="fas fa-edit me-2"></i>แก้ไขแบบฟอร์ม';
    
    document.getElementById('formId').value = formData.form_id;
    document.getElementById('formName').value = formData.form_name;
    document.getElementById('formDescription').value = formData.form_description || '';
    document.getElementById('formTypeId').value = formData.form_type_id || '';
    document.getElementById('formCategoryId').value = formData.form_category_id || '';
    document.getElementById('formOrder').value = formData.form_order || 0;
    document.getElementById('formStatus').value = formData.form_status || 'active';
    
    // จัดการไฟล์
    document.getElementById('formFile').required = false;
    document.getElementById('fileRequired').style.display = 'none';
    document.getElementById('fileReplaceNote').style.display = 'block';
    
    if (formData.form_file) {
        // แสดงข้อมูลไฟล์ปัจจุบัน
        showCurrentFileInfo(formData);
    } else {
        document.getElementById('currentFileSection').style.display = 'none';
    }
    
    updatePreview();
    
    const modal = new bootstrap.Modal(document.getElementById('formModal'));
    modal.show();
}

/**
 * แสดงข้อมูลไฟล์ปัจจุบัน
 */
function showCurrentFileInfo(formData) {
    const currentFileSection = document.getElementById('currentFileSection');
    const currentFileName = document.getElementById('currentFileName');
    const currentFileSize = document.getElementById('currentFileSize');
    const currentFileIcon = document.getElementById('currentFileIcon');
    const viewCurrentFileBtn = document.getElementById('viewCurrentFileBtn');
    const downloadCurrentFileBtn = document.getElementById('downloadCurrentFileBtn');
    
    // ชื่อไฟล์
    currentFileName.textContent = formData.form_file_original || formData.form_file || 'ไฟล์แบบฟอร์ม';
    
    // ขนาดไฟล์
    if (formData.form_file_size) {
        currentFileSize.textContent = formatFileSize(formData.form_file_size);
    } else {
        currentFileSize.textContent = 'ไม่ทราบขนาด';
    }
    
    // ไอคอนไฟล์
    const fileExt = getFileExtension(formData.form_file || '');
    updateFileIcon(currentFileIcon, fileExt);
    
    // ปุ่มดูไฟล์ (แสดงเฉพาะ PDF)
    if (fileExt === 'pdf') {
        viewCurrentFileBtn.style.display = 'inline-flex';
        viewCurrentFileBtn.setAttribute('data-form-id', formData.form_id);
    } else {
        viewCurrentFileBtn.style.display = 'none';
    }
    
    // ปุ่มดาวน์โหลด
    downloadCurrentFileBtn.href = `${EsvFormConfig.downloadUrl}/${formData.form_id}`;
    
    currentFileSection.style.display = 'block';
}

/**
 * ดูไฟล์ PDF ปัจจุบัน
 */
function viewCurrentFormPDF() {
    if (currentEditingForm && currentEditingForm.form_id) {
        viewFormPDF(currentEditingForm.form_id);
    }
}

/**
 * ดูไฟล์ PDF (เปิดใน tab ใหม่)
 */
function viewFormPDF(formId) {
    if (!formId) {
        Swal.fire({
            title: 'เกิดข้อผิดพลาด',
            text: 'ไม่พบรหัสแบบฟอร์ม',
            icon: 'error'
        });
        return;
    }
    
    // เปิดไฟล์ PDF ใน tab ใหม่
    const pdfUrl = `${EsvFormConfig.viewFileUrl}/${formId}`;
    const newWindow = window.open(pdfUrl, '_blank');
    
    // ตรวจสอบว่าเปิด popup ได้หรือไม่
    if (!newWindow || newWindow.closed || typeof newWindow.closed == 'undefined') {
        Swal.fire({
            title: 'Popup ถูกบล็อก',
            text: 'กรุณาอนุญาต popup สำหรับเว็บไซต์นี้ หรือคลิกปุ่มด้านล่างเพื่อเปิดไฟล์',
            icon: 'warning',
            showCancelButton: true,
            confirmButtonText: 'เปิดไฟล์',
            cancelButtonText: 'ยกเลิก'
        }).then((result) => {
            if (result.isConfirmed) {
                window.location.href = pdfUrl;
            }
        });
    } else {
        // แสดงข้อความแจ้งให้ทราบว่าเปิดไฟล์แล้ว
        Swal.fire({
            title: 'เปิดไฟล์แล้ว',
            text: 'ไฟล์ PDF ได้เปิดในแท็บใหม่แล้ว',
            icon: 'info',
            timer: 2000,
            showConfirmButton: false
        });
    }
}

/**
 * ลบแบบฟอร์ม
 */
function deleteForm(formId, formName) {
    Swal.fire({
        title: 'ยืนยันการลบ',
        text: `คุณต้องการลบแบบฟอร์ม "${formName}" หรือไม่?`,
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#ef4444',
        cancelButtonColor: '#6b7280',
        confirmButtonText: 'ใช่, ลบเลย',
        cancelButtonText: 'ยกเลิก',
        reverseButtons: true
    }).then((result) => {
        if (result.isConfirmed) {
            performDeleteForm(formId);
        }
    });
}

/**
 * ดำเนินการลบแบบฟอร์ม
 */
function performDeleteForm(formId) {
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
    formData.append('form_id', formId);
    
    fetch(EsvFormConfig.deleteUrl, {
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
    const name = document.getElementById('formName').value || 'ชื่อแบบฟอร์ม';
    const description = document.getElementById('formDescription').value || 'คำอธิบาย';
    const typeId = document.getElementById('formTypeId').value;
    const categoryId = document.getElementById('formCategoryId').value;
    const fileInput = document.getElementById('formFile');
    
    // ชื่อและคำอธิบาย
    document.getElementById('namePreview').textContent = name;
    document.getElementById('descPreview').textContent = description;
    
    // ประเภทเอกสาร
    let typeName = 'ทุกประเภท';
    let typeColor = '#8b9cc7';
    let typeIcon = 'fas fa-file';
    
    if (typeId) {
        const foundType = documentTypes.find(type => type.esv_type_id == typeId);
        if (foundType) {
            typeName = foundType.esv_type_name;
            typeColor = foundType.esv_type_color || '#8b9cc7';
            typeIcon = foundType.esv_type_icon || 'fas fa-file';
        }
    }
    
    document.getElementById('typePreview').textContent = typeName;
    document.getElementById('iconPreview').style.backgroundColor = typeColor;
    document.getElementById('iconPreview').innerHTML = `<i class="${typeIcon}" style="color: white;"></i>`;
    
    // หมวดหมู่เอกสาร
    let categoryName = 'ทุกหมวดหมู่';
    if (categoryId) {
        const foundCategory = categories.find(cat => cat.esv_category_id == categoryId);
        if (foundCategory) {
            categoryName = foundCategory.esv_category_name;
        }
    }
    document.getElementById('categoryPreview').textContent = categoryName;
    
    // ไฟล์
    let fileName = 'ไม่มีไฟล์';
    let fileExt = 'FILE';
    let fileClass = 'default';
    
    if (fileInput.files && fileInput.files[0]) {
        const file = fileInput.files[0];
        fileName = file.name;
        const ext = getFileExtension(fileName);
        fileExt = ext.toUpperCase();
        fileClass = getFileIconClass(ext);
    } else if (currentEditingForm && currentEditingForm.form_file) {
        // ใช้ไฟล์เดิม
        fileName = currentEditingForm.form_file_original || currentEditingForm.form_file;
        const ext = getFileExtension(fileName);
        fileExt = ext.toUpperCase();
        fileClass = getFileIconClass(ext);
    }
    
    const filePreview = document.getElementById('filePreview');
    filePreview.innerHTML = `
        <div class="esv-file-icon ${fileClass}">
            ${fileExt}
        </div>
        <small>${fileName}</small>
    `;
}

/**
 * จัดการ Form Submit
 */
document.getElementById('formForm').addEventListener('submit', function(e) {
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
    
    fetch(EsvFormConfig.saveUrl, {
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
// *** UTILITY FUNCTIONS ***
// ===================================================================

/**
 * ดึงนามสกุลไฟล์
 */
function getFileExtension(filename) {
    return filename.split('.').pop().toLowerCase();
}

/**
 * ดึง CSS class สำหรับไอคอนไฟล์
 */
function getFileIconClass(ext) {
    if (ext === 'pdf') return 'pdf';
    if (['doc', 'docx'].includes(ext)) return 'doc';
    if (['xls', 'xlsx'].includes(ext)) return 'xls';
    return 'default';
}

/**
 * อัปเดตไอคอนไฟล์
 */
function updateFileIcon(iconElement, fileExt) {
    const iconClass = getFileIconClass(fileExt);
    iconElement.className = `current-file-icon ${iconClass}`;
    iconElement.textContent = fileExt.toUpperCase();
}

/**
 * จัดรูปแบบขนาดไฟล์
 */
function formatFileSize(bytes) {
    if (bytes >= 1048576) {
        return (bytes / 1048576).toFixed(2) + ' MB';
    } else if (bytes >= 1024) {
        return (bytes / 1024).toFixed(2) + ' KB';
    }
    return bytes + ' B';
}

// ===================================================================
// *** EVENT LISTENERS ***
// ===================================================================

document.addEventListener('DOMContentLoaded', function() {
    // ตัวอย่างแบบเรียลไทม์
    document.getElementById('formName').addEventListener('input', updatePreview);
    document.getElementById('formDescription').addEventListener('input', updatePreview);
    document.getElementById('formTypeId').addEventListener('change', updatePreview);
    document.getElementById('formCategoryId').addEventListener('change', updatePreview);
    document.getElementById('formFile').addEventListener('change', updatePreview);
    
    // อัปเดตตัวอย่างครั้งแรก
    updatePreview();
    
    // ป้องกันการส่งฟอร์มโดยไม่ตั้งใจ
    document.getElementById('formModal').addEventListener('hidden.bs.modal', function() {
        currentEditingForm = null;
    });
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