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

if (!function_exists('format_fee')) {
    function format_fee($fee) {
        return $fee > 0 ? number_format($fee, 2) . ' บาท' : 'ฟรี';
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
    border-radius: 4px;
    border: 2px solid var(--esv-gray-300);
    margin-right: 0.5rem;
}

.esv-action-buttons {
    display: flex;
    gap: 0.5rem;
}

.esv-group-badge {
    background: linear-gradient(135deg, var(--esv-primary-color), var(--esv-primary-light));
    color: white;
    padding: 0.25rem 0.75rem;
    border-radius: 12px;
    font-size: 0.75rem;
    font-weight: 600;
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

/* ===== ICON SELECT STYLES ===== */
#categoryIcon {
    background-image: url("data:image/svg+xml,%3csvg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 16 16'%3e%3cpath fill='none' stroke='%23343a40' stroke-linecap='round' stroke-linejoin='round' stroke-width='2' d='m2 5 6 6 6-6'/%3e%3c/svg%3e");
    background-repeat: no-repeat;
    background-position: right 0.75rem center;
    background-size: 16px 12px;
}

.color-preview-circle {
    width: 24px;
    height: 24px;
    border-radius: 50%;
    border: 2px solid var(--esv-gray-300);
    box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
    transition: var(--esv-transition);
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
        min-width: 800px;
    }
}
</style>

<div class="esv-manage-page">
    <div class="esv-manage-container">
        <!-- Page Header -->
        <header class="esv-manage-header">
            <h1><i class="fas fa-tags me-3"></i>จัดการหมวดหมู่เอกสาร</h1>
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
                <h5><i class="fas fa-list me-2"></i>รายการหมวดหมู่เอกสาร</h5>
                
                <div class="esv-header-buttons">
                    <?php if ($can_add ?? false): ?>
                        <button type="button" class="esv-btn esv-btn-primary" data-bs-toggle="modal" data-bs-target="#categoryModal" onclick="openAddModal()">
                            <i class="fas fa-plus"></i>เพิ่มหมวดหมู่เอกสาร
                        </button>
                    <?php endif; ?>
                    
                    <a href="<?= site_url('Esv_ods/manage_document_types') ?>" class="esv-btn esv-btn-secondary">
                        <i class="fas fa-folder-open me-1"></i>จัดการประเภทเอกสาร
                    </a>

                    <a href="<?= site_url('Esv_ods/manage_forms') ?>" class="esv-btn esv-btn-secondary">
                        <i class="fas fa-tags me-1"></i>จัดการแบบฟอร์ม
                    </a>
                </div>
            </div>
            
            <div class="esv-table-container">
                <?php if (empty($categories)): ?>
                    <div class="text-center py-5">
                        <i class="fas fa-tags fa-3x text-muted mb-3"></i>
                        <h5 class="text-muted">ไม่พบข้อมูลหมวดหมู่เอกสาร</h5>
                        <p class="text-muted">กรุณาเพิ่มหมวดหมู่เอกสารใหม่</p>
                    </div>
                <?php else: ?>
                    <table class="esv-table">
                        <thead>
                            <tr>
                                <th style="width: 6%;">ลำดับ</th>
                                <th style="width: 5%;">ไอคอน</th>
                                <th style="width: 20%;">ชื่อหมวดหมู่</th>
                                <th style="width: 12%;">ประเภทเอกสาร</th>
                                <th style="width: 15%;">แผนก</th>
                                <th style="width: 8%;">ระยะเวลา</th>
                                <th style="width: 8%;">ค่าธรรมเนียม</th>
                                <th style="width: 23%;">คำอธิบาย</th>
                                <th style="width: 8%;">สถานะ</th>
                                <th style="width: 10%;">การจัดการ</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($categories as $index => $category): ?>
                                <tr>
                                    <td class="text-center">
                                        <strong><?= $category->esv_category_order ?: ($index + 1) ?></strong>
                                    </td>
                                    <td class="text-center">
                                        <div class="esv-icon-preview" style="background-color: <?= $category->esv_category_color ?: '#8b9cc7' ?>;">
                                            <i class="<?= $category->esv_category_icon ?: 'fas fa-folder' ?>" style="color: white;"></i>
                                        </div>
                                    </td>
                                    <td>
                                        <strong><?= htmlspecialchars($category->esv_category_name) ?></strong>
                                    </td>
                                    <td>
                                        <span class="esv-group-badge">
                                            <?= htmlspecialchars($category->type_name ?: 'ไม่ระบุ') ?>
                                        </span>
                                    </td>
                                    <td>
                                        <small class="text-muted">
                                            <?= htmlspecialchars($category->department_name ?: 'ทุกแผนก') ?>
                                        </small>
                                    </td>
                                    <td class="text-center">
                                        <?php if ($category->esv_category_process_days): ?>
                                            <span class="badge bg-info"><?= $category->esv_category_process_days ?> วัน</span>
                                        <?php else: ?>
                                            <span class="text-muted">-</span>
                                        <?php endif; ?>
                                    </td>
                                    <td class="text-center">
                                        <small class="<?= $category->esv_category_fee > 0 ? 'text-warning fw-bold' : 'text-success' ?>">
                                            <?= format_fee($category->esv_category_fee) ?>
                                        </small>
                                    </td>
                                    <td>
                                        <small class="text-muted">
                                            <?= htmlspecialchars(mb_substr($category->esv_category_description ?: '-', 0, 50)) ?>
                                            <?= mb_strlen($category->esv_category_description ?: '') > 50 ? '...' : '' ?>
                                        </small>
                                    </td>
                                    <td class="text-center">
                                        <span class="badge bg-<?= get_status_badge_class($category->esv_category_status) ?>">
                                            <?= get_status_display_text($category->esv_category_status) ?>
                                        </span>
                                    </td>
                                    <td>
                                        <div class="esv-action-buttons">
                                            <?php if ($can_edit ?? false): ?>
                                                <button type="button" class="esv-btn esv-btn-success esv-btn-sm" 
                                                        onclick="editCategory(<?= htmlspecialchars(json_encode($category), ENT_QUOTES) ?>)"
                                                        title="แก้ไข">
                                                    <i class="fas fa-edit"></i>
                                                </button>
                                            <?php endif; ?>
                                            
                                            <?php if ($can_delete ?? false): ?>
                                                <button type="button" class="esv-btn esv-btn-danger esv-btn-sm" 
                                                        onclick="deleteCategory('<?= $category->esv_category_id ?>', '<?= htmlspecialchars($category->esv_category_name, ENT_QUOTES) ?>')"
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

<!-- Modal เพิ่ม/แก้ไขหมวดหมู่เอกสาร -->
<div class="modal fade" id="categoryModal" tabindex="-1">
    <div class="modal-dialog modal-xl">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="modalTitle">
                    <i class="fas fa-tags me-2"></i>เพิ่มหมวดหมู่เอกสาร
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form id="categoryForm">
                <div class="modal-body">
                    <input type="hidden" id="categoryId" name="category_id">
                    
                    <div class="row">
                        <div class="col-md-8">
                            <div class="mb-3">
                                <label class="form-label">ชื่อหมวดหมู่เอกสาร <span class="text-danger">*</span></label>
                                <input type="text" class="form-control" id="categoryName" name="category_name" required
                                       placeholder="เช่น การศึกษา, สาธารณสุข, สิ่งแวดล้อม">
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="mb-3">
                                <label class="form-label">ลำดับ</label>
                                <input type="number" class="form-control" id="categoryOrder" name="category_order" 
                                       min="0" value="0">
                            </div>
                        </div>
                    </div>
                    
                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="form-label">ประเภทเอกสาร <span class="text-danger">*</span></label>
                                <select class="form-select" id="categoryGroup" name="category_group" required>
                                    <option value="">เลือกประเภทเอกสาร</option>
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
                                <label class="form-label">แผนกที่เกี่ยวข้อง</label>
                                <select class="form-select" id="categoryDepartment" name="category_department_id">
                                    <option value="">ทุกแผนก</option>
                                    <?php if (!empty($departments)): ?>
                                        <?php foreach ($departments as $dept): ?>
                                            <option value="<?= $dept->pid ?>"><?= htmlspecialchars($dept->pname) ?></option>
                                        <?php endforeach; ?>
                                    <?php endif; ?>
                                </select>
                            </div>
                        </div>
                    </div>
                    
                    <div class="mb-3">
                        <label class="form-label">คำอธิบาย</label>
                        <textarea class="form-control" id="categoryDescription" name="category_description" rows="3"
                                  placeholder="คำอธิบายหมวดหมู่เอกสาร"></textarea>
                    </div>
                    
                    <div class="row">
                        <div class="col-md-4">
                            <div class="mb-3">
                                <label class="form-label">ไอคอน</label>
                                <select class="form-select" id="categoryIcon" name="category_icon">
                                    <option value="fas fa-folder" data-icon="fas fa-folder">📁 โฟลเดอร์</option>
                                    <option value="fas fa-file-alt" data-icon="fas fa-file-alt">📄 เอกสาร</option>
                                    <option value="fas fa-file-signature" data-icon="fas fa-file-signature">📝 ลายเซ็น</option>
                                    <option value="fas fa-file-contract" data-icon="fas fa-file-contract">📋 สัญญา</option>
                                    <option value="fas fa-certificate" data-icon="fas fa-certificate">🏆 หนังสือรับรอง</option>
                                    <option value="fas fa-user-graduate" data-icon="fas fa-user-graduate">🎓 การศึกษา</option>
                                    <option value="fas fa-heartbeat" data-icon="fas fa-heartbeat">❤️ สาธารณสุข</option>
                                    <option value="fas fa-leaf" data-icon="fas fa-leaf">🌿 สิ่งแวดล้อม</option>
                                    <option value="fas fa-building" data-icon="fas fa-building">🏢 อาคาร</option>
                                    <option value="fas fa-users" data-icon="fas fa-users">👥 ชุมชน</option>
                                    <option value="fas fa-shield-alt" data-icon="fas fa-shield-alt">🛡️ ความปลอดภัย</option>
                                    <option value="fas fa-exclamation-triangle" data-icon="fas fa-exclamation-triangle">⚠️ เร่งด่วน</option>
                                </select>
                                <small class="form-text text-muted">เลือกไอคอนที่เหมาะสมกับหมวดหมู่</small>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="mb-3">
                                <label class="form-label">สี</label>
                                <div class="d-flex align-items-center gap-2">
                                    <input type="color" class="form-control form-control-color" id="categoryColor" name="category_color" 
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
                        <div class="col-md-4">
                            <div class="mb-3">
                                <label class="form-label">สถานะ</label>
                                <select class="form-select" id="categoryStatus" name="category_status">
                                    <option value="active">ใช้งาน</option>
                                    <option value="inactive">ไม่ใช้งาน</option>
                                </select>
                            </div>
                        </div>
                    </div>
                    
                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="form-label">ระยะเวลาดำเนินการ (วัน)</label>
                                <input type="number" class="form-control" id="categoryProcessDays" name="category_process_days" 
                                       min="1" placeholder="เช่น 7, 14, 30">
                                <small class="form-text text-muted">จำนวนวันที่ใช้ในการดำเนินการ</small>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="form-label">ค่าธรรมเนียม (บาท)</label>
                                <input type="number" class="form-control" id="categoryFee" name="category_fee" 
                                       min="0" step="0.01" value="0.00" placeholder="0.00">
                                <small class="form-text text-muted">ใส่ 0 หรือเว้นว่างหากไม่มีค่าธรรมเนียม</small>
                            </div>
                        </div>
                    </div>
                    
                    <!-- Preview -->
                    <div class="mb-3">
                        <label class="form-label">ตัวอย่าง</label>
                        <div class="p-3 bg-light rounded">
                            <div class="d-flex align-items-center">
                                <div id="iconPreview" class="esv-icon-preview me-3" style="background-color: #8b9cc7;">
                                    <i class="fas fa-folder" style="color: white;"></i>
                                </div>
                                <div class="flex-grow-1">
                                    <div class="d-flex align-items-center mb-1">
                                        <strong id="namePreview" class="me-2">ชื่อหมวดหมู่เอกสาร</strong>
                                        <span id="groupPreview" class="esv-group-badge">เลือกประเภทเอกสาร</span>
                                    </div>
                                    <small class="text-muted" id="descPreview">คำอธิบาย</small>
                                    <div class="mt-1">
                                        <small id="daysPreview" class="badge bg-info me-2" style="display: none;"></small>
                                        <small id="feePreview" class="text-success">ฟรี</small>
                                    </div>
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
// *** CATEGORY MANAGEMENT ***
// ===================================================================

const EsvCategoryConfig = {
    saveUrl: '<?= site_url("Esv_ods/save_category") ?>',
    deleteUrl: '<?= site_url("Esv_ods/delete_category") ?>'
};

// ข้อมูลประเภทเอกสารสำหรับ JavaScript
const documentTypes = <?= json_encode($document_types ?? [], JSON_UNESCAPED_UNICODE) ?>;

/**
 * เปิด Modal เพิ่มหมวดหมู่เอกสาร
 */
function openAddModal() {
    document.getElementById('modalTitle').innerHTML = '<i class="fas fa-tags me-2"></i>เพิ่มหมวดหมู่เอกสาร';
    document.getElementById('categoryForm').reset();
    document.getElementById('categoryId').value = '';
    document.getElementById('categoryColor').value = '#8b9cc7';
    document.getElementById('categoryIcon').value = 'fas fa-folder';
    document.getElementById('categoryFee').value = '0.00';
    
    // Reset color presets
    updateColorPresets('#8b9cc7');
    updatePreview();
}

/**
 * แก้ไขหมวดหมู่เอกสาร
 */
function editCategory(categoryData) {
    document.getElementById('modalTitle').innerHTML = '<i class="fas fa-edit me-2"></i>แก้ไขหมวดหมู่เอกสาร';
    
    document.getElementById('categoryId').value = categoryData.esv_category_id;
    document.getElementById('categoryName').value = categoryData.esv_category_name;
    document.getElementById('categoryDescription').value = categoryData.esv_category_description || '';
    document.getElementById('categoryGroup').value = categoryData.esv_category_group || '';
    document.getElementById('categoryDepartment').value = categoryData.esv_category_department_id || '';
    document.getElementById('categoryIcon').value = categoryData.esv_category_icon || 'fas fa-folder';
    document.getElementById('categoryColor').value = categoryData.esv_category_color || '#8b9cc7';
    document.getElementById('categoryOrder').value = categoryData.esv_category_order || 0;
    document.getElementById('categoryProcessDays').value = categoryData.esv_category_process_days || '';
    document.getElementById('categoryFee').value = categoryData.esv_category_fee || '0.00';
    document.getElementById('categoryStatus').value = categoryData.esv_category_status || 'active';
    
    // Update color presets
    updateColorPresets(categoryData.esv_category_color || '#8b9cc7');
    updatePreview();
    
    const modal = new bootstrap.Modal(document.getElementById('categoryModal'));
    modal.show();
}

/**
 * ลบหมวดหมู่เอกสาร
 */
function deleteCategory(categoryId, categoryName) {
    Swal.fire({
        title: 'ยืนยันการลบ',
        text: `คุณต้องการลบหมวดหมู่เอกสาร "${categoryName}" หรือไม่?`,
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#ef4444',
        cancelButtonColor: '#6b7280',
        confirmButtonText: 'ใช่, ลบเลย',
        cancelButtonText: 'ยกเลิก',
        reverseButtons: true
    }).then((result) => {
        if (result.isConfirmed) {
            performDeleteCategory(categoryId);
        }
    });
}

/**
 * ดำเนินการลบหมวดหมู่เอกสาร
 */
function performDeleteCategory(categoryId) {
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
    formData.append('category_id', categoryId);
    
    fetch(EsvCategoryConfig.deleteUrl, {
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
    const name = document.getElementById('categoryName').value || 'ชื่อหมวดหมู่เอกสาร';
    const description = document.getElementById('categoryDescription').value || 'คำอธิบาย';
    const groupTypeId = document.getElementById('categoryGroup').value;
    const icon = document.getElementById('categoryIcon').value || 'fas fa-folder';
    const color = document.getElementById('categoryColor').value || '#8b9cc7';
    const processDays = document.getElementById('categoryProcessDays').value;
    const fee = parseFloat(document.getElementById('categoryFee').value || 0);
    
    // หาชื่อประเภทเอกสาร
    let groupName = 'เลือกประเภทเอกสาร';
    if (groupTypeId) {
        const foundType = documentTypes.find(type => type.esv_type_id == groupTypeId);
        if (foundType) {
            groupName = foundType.esv_type_name;
        }
    }
    
    document.getElementById('namePreview').textContent = name;
    document.getElementById('descPreview').textContent = description;
    document.getElementById('groupPreview').textContent = groupName;
    document.getElementById('iconPreview').style.backgroundColor = color;
    document.getElementById('iconPreview').innerHTML = `<i class="${icon}" style="color: white;"></i>`;
    
    // Update color preview circle
    document.getElementById('colorPreviewCircle').style.backgroundColor = color;
    
    // Days preview
    const daysPreview = document.getElementById('daysPreview');
    if (processDays && processDays > 0) {
        daysPreview.textContent = `${processDays} วัน`;
        daysPreview.style.display = 'inline';
    } else {
        daysPreview.style.display = 'none';
    }
    
    // Fee preview
    const feePreview = document.getElementById('feePreview');
    if (fee > 0) {
        feePreview.textContent = `${fee.toFixed(2)} บาท`;
        feePreview.className = 'text-warning fw-bold';
    } else {
        feePreview.textContent = 'ฟรี';
        feePreview.className = 'text-success';
    }
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
document.getElementById('categoryForm').addEventListener('submit', function(e) {
    e.preventDefault();
    
    // ตรวจสอบว่าเลือกประเภทเอกสารแล้วหรือไม่
    const groupSelect = document.getElementById('categoryGroup');
    if (!groupSelect.value) {
        Swal.fire({
            title: 'ข้อมูลไม่ครบถ้วน',
            text: 'กรุณาเลือกประเภทเอกสาร',
            icon: 'warning'
        });
        groupSelect.focus();
        return;
    }
    
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
    
    fetch(EsvCategoryConfig.saveUrl, {
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
    document.getElementById('categoryName').addEventListener('input', updatePreview);
    document.getElementById('categoryDescription').addEventListener('input', updatePreview);
    document.getElementById('categoryGroup').addEventListener('change', updatePreview);
    document.getElementById('categoryIcon').addEventListener('change', updatePreview);
    document.getElementById('categoryColor').addEventListener('input', function() {
        updatePreview();
        updateColorPresets(this.value);
    });
    document.getElementById('categoryProcessDays').addEventListener('input', updatePreview);
    document.getElementById('categoryFee').addEventListener('input', updatePreview);
    
    // Color preset click handlers
    document.querySelectorAll('.color-preset').forEach(preset => {
        preset.addEventListener('click', function() {
            const color = this.dataset.color;
            document.getElementById('categoryColor').value = color;
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