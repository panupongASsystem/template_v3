<?php
// Helper function สำหรับสถานะฟอร์มเด็ก
if (!function_exists('get_kid_form_status_class')) {
    function get_kid_form_status_class($status) {
        return $status == 1 ? 'active' : 'inactive';
    }
}

if (!function_exists('get_kid_form_status_display')) {
    function get_kid_form_status_display($status) {
        return $status == 1 ? 'เปิดใช้งาน' : 'ปิดใช้งาน';
    }
}

if (!function_exists('get_kid_form_type_display')) {
    function get_kid_form_type_display($type) {
        switch($type) {
            case 'children': return 'เด็กทั่วไป';
            case 'disabled': return 'เด็กพิการ';
            case 'authorization': return 'หนังสือมอบอำนาจ';
            case 'general': return 'ทั่วไป';
            default: return 'เด็กทั่วไป';
        }
    }
}
?>

<!-- SweetAlert2 -->
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

<style>
/* ===== KID FORMS MANAGEMENT STYLES ===== */
.kid-forms-page {
    --kid-primary-color: #28a745;
    --kid-primary-light: #5cb85c;
    --kid-secondary-color: #f0fff4;
    --kid-success-color: #81c784;
    --kid-warning-color: #ffb74d;
    --kid-danger-color: #e57373;
    --kid-info-color: #64b5f6;
    --kid-purple-color: #ba68c8;
    --kid-light-bg: #fafbfc;
    --kid-white: #ffffff;
    --kid-gray-50: #fafafa;
    --kid-gray-100: #f5f5f5;
    --kid-gray-200: #eeeeee;
    --kid-gray-300: #e0e0e0;
    --kid-gray-400: #bdbdbd;
    --kid-gray-500: #9e9e9e;
    --kid-gray-600: #757575;
    --kid-gray-700: #616161;
    --kid-gray-800: #424242;
    --kid-gray-900: #212121;
    --kid-shadow-sm: 0 1px 2px 0 rgb(0 0 0 / 0.03);
    --kid-shadow: 0 1px 3px 0 rgb(0 0 0 / 0.06), 0 1px 2px -1px rgb(0 0 0 / 0.04);
    --kid-shadow-md: 0 4px 6px -1px rgb(0 0 0 / 0.06), 0 2px 4px -2px rgb(0 0 0 / 0.04);
    --kid-shadow-lg: 0 10px 15px -3px rgb(0 0 0 / 0.06), 0 4px 6px -4px rgb(0 0 0 / 0.04);
    --kid-shadow-xl: 0 20px 25px -5px rgb(0 0 0 / 0.06), 0 8px 10px -6px rgb(0 0 0 / 0.04);
    --kid-border-radius: 12px;
    --kid-border-radius-lg: 16px;
    --kid-transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
}

.kid-forms-page {
    background: linear-gradient(135deg, #f0fff4 0%, #fcfff7 100%);
    font-family: 'Inter', 'Segoe UI', system-ui, -apple-system, sans-serif;
    line-height: 1.6;
    color: var(--kid-gray-700);
    min-height: 100vh;
}

.kid-forms-page .kid-container-fluid {
    padding: 1.5rem;
    max-width: 1400px;
    margin: 0 auto;
    min-height: calc(100vh - 140px);
}

/* ===== PAGE HEADER ===== */
.kid-forms-page .kid-page-header {
    background: linear-gradient(135deg, rgba(40, 167, 69, 0.8) 0%, rgba(92, 184, 92, 0.6) 100%);
    color: white;
    padding: 2rem;
    border-radius: var(--kid-border-radius-lg);
    margin-bottom: 2rem;
    box-shadow: var(--kid-shadow-md);
    position: relative;
    overflow: hidden;
    margin-top: 1rem;
}

.kid-forms-page .kid-page-header::before {
    content: '';
    position: absolute;
    top: -50%;
    right: -20%;
    width: 300px;
    height: 300px;
    background: radial-gradient(circle, rgba(255,255,255,0.08) 0%, transparent 70%);
    border-radius: 50%;
}

.kid-forms-page .kid-page-header h1 {
    font-size: 2rem;
    font-weight: 600;
    margin: 0;
    text-shadow: 0 1px 3px rgba(0,0,0,0.08);
    position: relative;
    z-index: 1;
    color: #ffffff !important;
}

.kid-forms-page .kid-header-actions {
    position: absolute;
    top: 1.5rem;
    right: 2rem;
    z-index: 2;
    display: flex;
    gap: 0.75rem;
}

.kid-forms-page .kid-action-btn {
    background: rgba(255, 255, 255, 0.15);
    color: white;
    border: 1px solid rgba(255, 255, 255, 0.3);
    padding: 0.75rem 1rem;
    border-radius: 8px;
    font-size: 0.875rem;
    font-weight: 500;
    display: flex;
    align-items: center;
    gap: 0.5rem;
    text-decoration: none;
    transition: var(--kid-transition);
    backdrop-filter: blur(10px);
}

.kid-forms-page .kid-action-btn:hover {
    background: rgba(255, 255, 255, 0.25);
    border-color: rgba(255, 255, 255, 0.5);
    color: white;
    transform: translateY(-1px);
    box-shadow: 0 4px 12px rgba(0, 0, 0, 0.15);
}

/* ===== FORMS STATISTICS ===== */
.kid-forms-page .kid-stats-section {
    margin-bottom: 2rem;
}

.kid-forms-page .kid-stats-grid {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
    gap: 1.5rem;
}

.kid-forms-page .kid-stat-card {
    background: var(--kid-white);
    border-radius: var(--kid-border-radius);
    padding: 1.5rem;
    box-shadow: var(--kid-shadow-md);
    position: relative;
    overflow: hidden;
    transition: var(--kid-transition);
    border: 1px solid var(--kid-gray-100);
}

.kid-forms-page .kid-stat-card:hover {
    transform: translateY(-2px);
    box-shadow: var(--kid-shadow-lg);
}

.kid-forms-page .kid-stat-card::before {
    content: '';
    position: absolute;
    top: 0;
    left: 0;
    right: 0;
    height: 3px;
}

.kid-forms-page .kid-stat-card.total::before { 
    background: linear-gradient(90deg, var(--kid-primary-color), var(--kid-primary-light)); 
}
.kid-forms-page .kid-stat-card.active::before { 
    background: linear-gradient(90deg, var(--kid-success-color), #66bb6a); 
}
.kid-forms-page .kid-stat-card.inactive::before { 
    background: linear-gradient(90deg, var(--kid-danger-color), #ef5350); 
}
.kid-forms-page .kid-stat-card.children::before { 
    background: linear-gradient(90deg, var(--kid-info-color), #42a5f5); 
}

.kid-forms-page .kid-stat-header {
    display: flex;
    align-items: center;
    justify-content: space-between;
    margin-bottom: 1rem;
}

.kid-forms-page .kid-stat-icon {
    width: 42px;
    height: 42px;
    border-radius: 10px;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 1.1rem;
    color: white;
    margin-right: 1rem;
}

.kid-forms-page .kid-stat-icon.total { 
    background: linear-gradient(135deg, rgba(40, 167, 69, 0.8), rgba(92, 184, 92, 0.8)); 
}
.kid-forms-page .kid-stat-icon.active { 
    background: linear-gradient(135deg, rgba(129, 199, 132, 0.8), rgba(102, 187, 106, 0.8)); 
}
.kid-forms-page .kid-stat-icon.inactive { 
    background: linear-gradient(135deg, rgba(229, 115, 115, 0.8), rgba(239, 83, 80, 0.8)); 
}
.kid-forms-page .kid-stat-icon.children { 
    background: linear-gradient(135deg, rgba(100, 181, 246, 0.8), rgba(66, 165, 245, 0.8)); 
}

.kid-forms-page .kid-stat-value {
    font-size: 2.2rem;
    font-weight: 700;
    color: var(--kid-gray-800);
    margin-bottom: 0.25rem;
    line-height: 1;
}

.kid-forms-page .kid-stat-label {
    color: var(--kid-gray-600);
    font-size: 0.875rem;
    font-weight: 500;
    text-transform: uppercase;
    letter-spacing: 0.025em;
}

/* ===== FORMS MANAGEMENT SECTION ===== */
.kid-forms-page .kid-forms-section {
    margin-bottom: 2rem;
}

.kid-forms-page .kid-forms-card {
    background: var(--kid-white);
    border-radius: var(--kid-border-radius);
    padding: 2rem;
    box-shadow: var(--kid-shadow-md);
    border: 1px solid var(--kid-gray-100);
}

.kid-forms-page .kid-forms-header {
    display: flex;
    justify-content: space-between;
    align-items: center;
    margin-bottom: 1.5rem;
    padding-bottom: 1rem;
    border-bottom: 1px solid var(--kid-gray-200);
}

.kid-forms-page .kid-forms-title {
    font-size: 1.25rem;
    font-weight: 700;
    color: var(--kid-gray-900);
    margin: 0;
    display: flex;
    align-items: center;
    gap: 0.5rem;
}

.kid-forms-page .kid-forms-actions {
    display: flex;
    gap: 0.75rem;
}

.kid-forms-page .kid-btn {
    padding: 0.75rem 1.5rem;
    border-radius: 8px;
    font-weight: 600;
    font-size: 0.875rem;
    transition: var(--kid-transition);
    border: none;
    display: inline-flex;
    align-items: center;
    gap: 0.5rem;
    text-decoration: none;
    cursor: pointer;
}

.kid-forms-page .kid-btn-primary {
    background: linear-gradient(135deg, var(--kid-primary-color), var(--kid-primary-light));
    color: white;
}

.kid-forms-page .kid-btn-primary:hover {
    transform: translateY(-1px);
    box-shadow: var(--kid-shadow-lg);
    color: white;
}

.kid-forms-page .kid-btn-success {
    background: linear-gradient(135deg, var(--kid-success-color), #81c784);
    color: white;
}

.kid-forms-page .kid-btn-success:hover {
    transform: translateY(-1px);
    box-shadow: var(--kid-shadow-lg);
    color: white;
}

.kid-forms-page .kid-btn-secondary {
    background: var(--kid-gray-100);
    color: var(--kid-gray-700);
}

.kid-forms-page .kid-btn-secondary:hover {
    background: var(--kid-gray-200);
    color: var(--kid-gray-800);
}

/* ===== FORM ITEM STYLES ===== */
.kid-forms-page .kid-form-item {
    background: var(--kid-gray-50);
    border: 1px solid var(--kid-gray-200);
    border-radius: 8px;
    padding: 1.25rem;
    margin-bottom: 1rem;
    transition: var(--kid-transition);
    position: relative;
}

.kid-forms-page .kid-form-item:hover {
    border-color: var(--kid-primary-color);
    box-shadow: var(--kid-shadow-md);
    transform: translateY(-1px);
}

.kid-forms-page .kid-form-header {
    display: flex;
    justify-content: space-between;
    align-items: flex-start;
    margin-bottom: 0.75rem;
}

.kid-forms-page .kid-form-title {
    font-size: 1rem;
    font-weight: 600;
    color: var(--kid-gray-900);
    margin: 0;
    flex: 1;
}

.kid-forms-page .kid-form-status {
    padding: 0.25rem 0.75rem;
    border-radius: 20px;
    font-size: 0.75rem;
    font-weight: 600;
    text-transform: uppercase;
    letter-spacing: 0.025em;
}

.kid-forms-page .kid-form-status.active {
    background: linear-gradient(135deg, #e8f5e8, #c8e6c9);
    color: #2e7d32;
}

.kid-forms-page .kid-form-status.inactive {
    background: linear-gradient(135deg, #ffebee, #ffcdd2);
    color: #c62828;
}

.kid-forms-page .kid-form-meta {
    display: flex;
    flex-wrap: wrap;
    gap: 1rem;
    margin-bottom: 1rem;
    font-size: 0.875rem;
    color: var(--kid-gray-600);
}

.kid-forms-page .kid-form-meta-item {
    display: flex;
    align-items: center;
    gap: 0.25rem;
}

.kid-forms-page .kid-form-description {
    font-size: 0.875rem;
    color: var(--kid-gray-600);
    margin-bottom: 1rem;
    line-height: 1.5;
}

.kid-forms-page .kid-form-actions {
    display: flex;
    gap: 0.5rem;
    flex-wrap: wrap;
}

.kid-forms-page .kid-form-btn {
    padding: 0.5rem 1rem;
    border-radius: 6px;
    font-size: 0.8rem;
    font-weight: 500;
    border: none;
    cursor: pointer;
    transition: var(--kid-transition);
    display: flex;
    align-items: center;
    gap: 0.5rem;
    text-decoration: none;
}

.kid-forms-page .kid-form-btn.download {
    background: linear-gradient(135deg, var(--kid-info-color), #42a5f5);
    color: white;
}

.kid-forms-page .kid-form-btn.edit {
    background: linear-gradient(135deg, var(--kid-warning-color), #ffb74d);
    color: white;
}

.kid-forms-page .kid-form-btn.delete {
    background: linear-gradient(135deg, var(--kid-danger-color), #ef5350);
    color: white;
}

.kid-forms-page .kid-form-btn.toggle {
    background: linear-gradient(135deg, var(--kid-purple-color), #ce93d8);
    color: white;
}

.kid-forms-page .kid-form-btn:hover {
    transform: translateY(-1px);
    box-shadow: var(--kid-shadow-md);
}

/* ===== EMPTY STATE ===== */
.kid-forms-page .kid-empty-state {
    text-align: center;
    padding: 4rem 2rem;
    color: var(--kid-gray-500);
}

.kid-forms-page .kid-empty-state i {
    font-size: 4rem;
    margin-bottom: 1rem;
    opacity: 0.5;
}

.kid-forms-page .kid-empty-state h5 {
    color: var(--kid-gray-600);
    margin-bottom: 0.5rem;
}

/* ===== RESPONSIVE DESIGN ===== */
@media (max-width: 768px) {
    .kid-forms-page .kid-container-fluid {
        padding: 1rem;
        min-height: calc(100vh - 120px);
    }
    
    .kid-forms-page .kid-page-header {
        padding: 1.5rem 1rem;
        margin-bottom: 1.5rem;
        margin-top: 0.5rem;
    }
    
    .kid-forms-page .kid-page-header h1 {
        font-size: 1.5rem;
    }
    
    .kid-forms-page .kid-header-actions {
        position: relative;
        top: auto;
        right: auto;
        margin-top: 1rem;
        flex-direction: column;
        align-items: stretch;
    }
    
    .kid-forms-page .kid-stats-grid {
        grid-template-columns: repeat(2, 1fr);
        gap: 1rem;
    }
    
    .kid-forms-page .kid-forms-header {
        flex-direction: column;
        gap: 1rem;
        align-items: stretch;
    }
    
    .kid-forms-page .kid-forms-actions {
        justify-content: stretch;
    }
    
    .kid-forms-page .kid-form-actions {
        flex-direction: column;
        gap: 0.25rem;
    }
    
    .kid-forms-page .kid-form-btn {
        width: 100%;
        justify-content: center;
    }
}

/* ===== MODAL STYLES ===== */
.kid-forms-page .modal-content {
    border: none;
    border-radius: var(--kid-border-radius);
    box-shadow: var(--kid-shadow-xl);
}

.kid-forms-page .modal-header {
    border-bottom: 1px solid var(--kid-gray-200);
    padding: 1.5rem 2rem;
}

.kid-forms-page .modal-title {
    font-weight: 700;
    color: var(--kid-gray-900);
}

.kid-forms-page .modal-body {
    padding: 2rem;
}

.kid-forms-page .modal-footer {
    border-top: 1px solid var(--kid-gray-200);
    padding: 1.5rem 2rem;
}

.kid-forms-page .form-control,
.kid-forms-page .form-select {
    border: 2px solid var(--kid-gray-200);
    border-radius: 8px;
    padding: 0.75rem 1rem;
    font-size: 0.875rem;
    transition: var(--kid-transition);
    background-color: var(--kid-white);
}

.kid-forms-page .form-control:focus,
.kid-forms-page .form-select:focus {
    border-color: var(--kid-primary-color);
    box-shadow: 0 0 0 3px rgba(40, 167, 69, 0.1);
    outline: none;
}

.kid-forms-page .form-label {
    font-weight: 600;
    color: var(--kid-gray-700);
    margin-bottom: 0.5rem;
    font-size: 0.875rem;
}
</style>

<div class="kid-forms-page">
    <div class="kid-container-fluid">
        <!-- ===== PAGE HEADER ===== -->
        <header class="kid-page-header">
            <h1><i class="fas fa-baby me-3"></i>จัดการฟอร์มเงินสนับสนุนเด็ก</h1>
            
            <!-- Header Actions -->
            <div class="kid-header-actions">
                <a href="<?= site_url('Kid_aw_ods/kid_aw_ods') ?>" class="kid-action-btn" title="กลับหน้าหลัก">
                    <i class="fas fa-arrow-left"></i>
                    <span>กลับ</span>
                </a>
            </div>
        </header>

        <!-- ===== STATISTICS SECTION ===== -->
        <section class="kid-stats-section">
            <div class="kid-stats-grid">
                <div class="kid-stat-card total">
                    <div class="kid-stat-header">
                        <div class="kid-stat-icon total">
                            <i class="fas fa-file-alt"></i>
                        </div>
                    </div>
                    <div class="kid-stat-value"><?= $forms_statistics['total'] ?? 0 ?></div>
                    <div class="kid-stat-label">ฟอร์มทั้งหมด</div>
                </div>

                <div class="kid-stat-card active">
                    <div class="kid-stat-header">
                        <div class="kid-stat-icon active">
                            <i class="fas fa-check-circle"></i>
                        </div>
                    </div>
                    <div class="kid-stat-value"><?= $forms_statistics['active'] ?? 0 ?></div>
                    <div class="kid-stat-label">เปิดใช้งาน</div>
                </div>

                <div class="kid-stat-card inactive">
                    <div class="kid-stat-header">
                        <div class="kid-stat-icon inactive">
                            <i class="fas fa-times-circle"></i>
                        </div>
                    </div>
                    <div class="kid-stat-value"><?= $forms_statistics['inactive'] ?? 0 ?></div>
                    <div class="kid-stat-label">ปิดใช้งาน</div>
                </div>

                <div class="kid-stat-card children">
                    <div class="kid-stat-header">
                        <div class="kid-stat-icon children">
                            <i class="fas fa-baby"></i>
                        </div>
                    </div>
                    <div class="kid-stat-value"><?= $forms_statistics['children_type'] ?? 0 ?></div>
                    <div class="kid-stat-label">ฟอร์มเด็กทั่วไป</div>
                </div>
            </div>
        </section>

        <!-- ===== FORMS MANAGEMENT SECTION ===== -->
        <section class="kid-forms-section">
            <div class="kid-forms-card">
                <div class="kid-forms-header">
                    <h5 class="kid-forms-title">
                        <i class="fas fa-list me-2"></i>รายการฟอร์ม
                    </h5>
                    <div class="kid-forms-actions">
                        <button class="kid-btn kid-btn-primary" onclick="showAddFormModal()">
                            <i class="fas fa-plus me-1"></i>เพิ่มฟอร์มใหม่
                        </button>
                        <button class="kid-btn kid-btn-secondary" onclick="refreshFormsList()">
                            <i class="fas fa-sync-alt me-1"></i>รีเฟรช
                        </button>
                    </div>
                </div>

                <!-- Forms List -->
                <div class="kid-forms-content">
                    <?php if (empty($kid_forms)): ?>
                        <div class="kid-empty-state">
                            <i class="fas fa-baby"></i>
                            <h5>ยังไม่มีฟอร์มในระบบ</h5>
                            <p>เริ่มต้นด้วยการเพิ่มฟอร์มใหม่</p>
                            <button class="kid-btn kid-btn-primary" onclick="showAddFormModal()">
                                <i class="fas fa-plus me-1"></i>เพิ่มฟอร์มแรก
                            </button>
                        </div>
                    <?php else: ?>
                        <?php foreach ($kid_forms as $form): ?>
                            <div class="kid-form-item" data-form-id="<?= $form->kid_aw_form_id ?>">
                                <div class="kid-form-header">
                                    <h6 class="kid-form-title"><?= htmlspecialchars($form->kid_aw_form_name) ?></h6>
                                    <span class="kid-form-status <?= get_kid_form_status_class($form->kid_aw_form_status) ?>">
                                        <?= get_kid_form_status_display($form->kid_aw_form_status) ?>
                                    </span>
                                </div>

                                <div class="kid-form-meta">
                                    <div class="kid-form-meta-item">
                                        <i class="fas fa-tag"></i>
                                        <span>ประเภท: <?= get_kid_form_type_display($form->kid_aw_form_type) ?></span>
                                    </div>
                                    <div class="kid-form-meta-item">
                                        <i class="fas fa-file"></i>
                                        <span>ไฟล์: <?= htmlspecialchars($form->kid_aw_form_file) ?></span>
                                    </div>
                                    <div class="kid-form-meta-item">
                                        <i class="fas fa-calendar"></i>
                                        <span>อัปเดต: <?= date('d/m/Y H:i', strtotime($form->kid_aw_form_datesave)) ?></span>
                                    </div>
                                    <div class="kid-form-meta-item">
                                        <i class="fas fa-user"></i>
                                        <span>โดย: <?= htmlspecialchars($form->kid_aw_form_by) ?></span>
                                    </div>
                                </div>

                                <?php if (!empty($form->kid_aw_form_description)): ?>
                                    <div class="kid-form-description">
                                        <?= nl2br(htmlspecialchars($form->kid_aw_form_description)) ?>
                                    </div>
                                <?php endif; ?>

                                <div class="kid-form-actions">
                                    <a href="<?= base_url('docs/file/' . $form->kid_aw_form_file) ?>" 
                                       target="_blank" 
                                       class="kid-form-btn download"
                                       title="ดาวน์โหลดฟอร์ม">
                                        <i class="fas fa-download"></i>ดาวน์โหลด
                                    </a>
                                    
                                    <button type="button" 
                                            class="kid-form-btn edit" 
                                            onclick="showEditFormModal('<?= $form->kid_aw_form_id ?>')"
                                            title="แก้ไขฟอร์ม">
                                        <i class="fas fa-edit"></i>แก้ไข
                                    </button>
                                    
                                    <button type="button" 
                                            class="kid-form-btn toggle" 
                                            onclick="toggleFormStatus('<?= $form->kid_aw_form_id ?>', '<?= $form->kid_aw_form_status ?>')"
                                            title="<?= $form->kid_aw_form_status == 1 ? 'ปิดใช้งาน' : 'เปิดใช้งาน' ?>">
                                        <i class="fas fa-power-off"></i>
                                        <?= $form->kid_aw_form_status == 1 ? 'ปิดใช้งาน' : 'เปิดใช้งาน' ?>
                                    </button>
                                    
                                    <?php if ($can_delete_forms ?? false): ?>
                                        <button type="button" 
                                                class="kid-form-btn delete" 
                                                onclick="confirmDeleteForm('<?= $form->kid_aw_form_id ?>', '<?= htmlspecialchars($form->kid_aw_form_name, ENT_QUOTES) ?>')"
                                                title="ลบฟอร์ม">
                                            <i class="fas fa-trash"></i>ลบ
                                        </button>
                                    <?php endif; ?>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </div>
            </div>
        </section>
    </div>
</div>

<!-- ===== MODALS ===== -->

<!-- Add Form Modal -->
<div class="modal fade" id="addFormModal" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">
                    <i class="fas fa-plus me-2"></i>เพิ่มฟอร์มเงินสนับสนุนเด็กใหม่
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form id="addFormForm" enctype="multipart/form-data">
                <div class="modal-body">
                    <div class="row">
                        <div class="col-md-8">
                            <div class="mb-3">
                                <label class="form-label">ชื่อฟอร์ม <span class="text-danger">*</span></label>
                                <input type="text" class="form-control" name="form_name" required
                                       placeholder="เช่น แบบฟอร์มขอรับเงินสนับสนุนเด็กแรกเกิด">
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="mb-3">
                                <label class="form-label">ประเภทฟอร์ม</label>
                                <select class="form-select" name="form_type">
                                    <option value="children">เด็กทั่วไป</option>
                                    <option value="disabled">เด็กพิการ</option>
                                    <option value="authorization">หนังสือมอบอำนาจ</option>
                                    <option value="general">ทั่วไป</option>
                                </select>
                            </div>
                        </div>
                    </div>

                    <div class="mb-3">
                        <label class="form-label">คำอธิบายฟอร์ม</label>
                        <textarea class="form-control" name="form_description" rows="3"
                                  placeholder="อธิบายรายละเอียดของฟอร์มนี้..."></textarea>
                    </div>

                    <div class="mb-3">
                        <label class="form-label">ไฟล์ฟอร์ม <span class="text-danger">*</span></label>
                        <input type="file" class="form-control" name="form_file" required
                               accept=".pdf,.doc,.docx">
                        <div class="form-text">อนุญาตไฟล์ PDF, DOC, DOCX เท่านั้น (ขนาดไม่เกิน 5MB)</div>
                    </div>

                    <div class="mb-3">
                        <div class="form-check">
                            <input class="form-check-input" type="checkbox" name="form_status" value="1" checked>
                            <label class="form-check-label">
                                เปิดใช้งานทันที
                            </label>
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

<!-- Edit Form Modal -->
<div class="modal fade" id="editFormModal" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">
                    <i class="fas fa-edit me-2"></i>แก้ไขฟอร์มเงินสนับสนุนเด็ก
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form id="editFormForm" enctype="multipart/form-data">
                <input type="hidden" name="form_id" id="editFormId">
                <div class="modal-body">
                    <div class="row">
                        <div class="col-md-8">
                            <div class="mb-3">
                                <label class="form-label">ชื่อฟอร์ม <span class="text-danger">*</span></label>
                                <input type="text" class="form-control" name="form_name" id="editFormName" required>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="mb-3">
                                <label class="form-label">ประเภทฟอร์ม</label>
                                <select class="form-select" name="form_type" id="editFormType">
                                    <option value="children">เด็กทั่วไป</option>
                                    <option value="disabled">เด็กพิการ</option>
                                    <option value="authorization">หนังสือมอบอำนาจ</option>
                                    <option value="general">ทั่วไป</option>
                                </select>
                            </div>
                        </div>
                    </div>

                    <div class="mb-3">
                        <label class="form-label">คำอธิบายฟอร์ม</label>
                        <textarea class="form-control" name="form_description" id="editFormDescription" rows="3"></textarea>
                    </div>

                    <div class="mb-3">
                        <label class="form-label">ไฟล์ฟอร์มปัจจุบัน</label>
                        <div class="alert alert-info">
                            <i class="fas fa-info-circle me-2"></i>
                            <span id="currentFileName"></span>
                        </div>
                    </div>

                    <div class="mb-3">
                        <label class="form-label">เปลี่ยนไฟล์ฟอร์ม (ถ้าต้องการ)</label>
                        <input type="file" class="form-control" name="form_file"
                               accept=".pdf,.doc,.docx">
                        <div class="form-text">ปล่อยว่างไว้หากไม่ต้องการเปลี่ยนไฟล์</div>
                    </div>

                    <div class="mb-3">
                        <div class="form-check">
                            <input class="form-check-input" type="checkbox" name="form_status" value="1" id="editFormStatus">
                            <label class="form-check-label">
                                เปิดใช้งาน
                            </label>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                        <i class="fas fa-times me-1"></i>ยกเลิก
                    </button>
                    <button type="submit" class="btn btn-primary">
                        <i class="fas fa-save me-1"></i>บันทึกการแก้ไข
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Delete Confirmation Modal -->
<div class="modal fade" id="deleteFormModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header bg-danger text-white">
                <h5 class="modal-title">
                    <i class="fas fa-exclamation-triangle me-2"></i>ยืนยันการลบ
                </h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <div class="alert alert-danger">
                    <i class="fas fa-warning me-2"></i>
                    <strong>คำเตือน:</strong> การดำเนินการนี้ไม่สามารถยกเลิกได้!
                </div>
                
                <p>คุณต้องการลบฟอร์มนี้หรือไม่?</p>
                
                <div class="bg-light p-3 rounded">
                    <strong>ชื่อฟอร์ม:</strong> <span id="deleteFormName"></span>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                    <i class="fas fa-times me-1"></i>ยกเลิก
                </button>
                <button type="button" class="btn btn-danger" id="confirmDeleteFormBtn">
                    <i class="fas fa-trash me-1"></i>ลบฟอร์ม
                </button>
            </div>
        </div>
    </div>
</div>

<!-- Bootstrap 5 JS -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>

<script>
// ===================================================================
// *** CONFIGURATION & VARIABLES ***
// ===================================================================

const KidFormsConfig = {
    baseUrl: '<?= site_url() ?>',
    addFormUrl: '<?= site_url("Kid_aw_ods/add_form") ?>',
    editFormUrl: '<?= site_url("Kid_aw_ods/edit_form") ?>',
    deleteFormUrl: '<?= site_url("Kid_aw_ods/delete_form") ?>',
    toggleFormUrl: '<?= site_url("Kid_aw_ods/toggle_form_status") ?>',
    getFormUrl: '<?= site_url("Kid_aw_ods/get_form_data") ?>',
    debug: <?= (defined('ENVIRONMENT') && ENVIRONMENT === 'development') ? 'true' : 'false' ?>
};

// ===================================================================
// *** CORE FUNCTIONS ***
// ===================================================================

/**
 * แสดง Modal เพิ่มฟอร์มใหม่
 */
function showAddFormModal() {
    const modal = new bootstrap.Modal(document.getElementById('addFormModal'));
    modal.show();
}

/**
 * แสดง Modal แก้ไขฟอร์ม
 */
function showEditFormModal(formId) {
    if (!formId) {
        showErrorAlert('ไม่พบหมายเลขฟอร์ม');
        return;
    }

    // แสดง loading
    Swal.fire({
        title: 'กำลังโหลดข้อมูล...',
        allowOutsideClick: false,
        allowEscapeKey: false,
        showConfirmButton: false,
        didOpen: () => {
            Swal.showLoading();
        }
    });

    // ดึงข้อมูลฟอร์ม
    const formData = new FormData();
    formData.append('form_id', formId);

    fetch(KidFormsConfig.getFormUrl, {
        method: 'POST',
        body: formData,
        headers: {
            'X-Requested-With': 'XMLHttpRequest'
        }
    })
    .then(response => response.json())
    .then(data => {
        Swal.close();
        
        if (data.success) {
            // เติมข้อมูลในฟอร์ม
            document.getElementById('editFormId').value = data.form.kid_aw_form_id;
            document.getElementById('editFormName').value = data.form.kid_aw_form_name;
            document.getElementById('editFormType').value = data.form.kid_aw_form_type;
            document.getElementById('editFormDescription').value = data.form.kid_aw_form_description || '';
            document.getElementById('editFormStatus').checked = data.form.kid_aw_form_status == 1;
            document.getElementById('currentFileName').textContent = data.form.kid_aw_form_file;
            
            // แสดง Modal
            const modal = new bootstrap.Modal(document.getElementById('editFormModal'));
            modal.show();
        } else {
            showErrorAlert(data.message || 'ไม่สามารถโหลดข้อมูลฟอร์มได้');
        }
    })
    .catch(error => {
        Swal.close();
        console.error('Get form error:', error);
        showErrorAlert('ไม่สามารถเชื่อมต่อกับเซิร์ฟเวอร์ได้');
    });
}

/**
 * เปลี่ยนสถานะการใช้งานฟอร์ม
 */
function toggleFormStatus(formId, currentStatus) {
    if (!formId) {
        showErrorAlert('ไม่พบหมายเลขฟอร์ม');
        return;
    }

    const newStatus = currentStatus == 1 ? 0 : 1;
    const statusText = newStatus == 1 ? 'เปิดใช้งาน' : 'ปิดใช้งาน';
    
    Swal.fire({
        title: 'ยืนยันการเปลี่ยนสถานะ',
        text: `คุณต้องการ${statusText}ฟอร์มนี้หรือไม่?`,
        icon: 'question',
        showCancelButton: true,
        confirmButtonText: 'ใช่, ' + statusText,
        cancelButtonText: 'ยกเลิก'
    }).then((result) => {
        if (result.isConfirmed) {
            performToggleFormStatus(formId, newStatus);
        }
    });
}

/**
 * ดำเนินการเปลี่ยนสถานะฟอร์ม
 */
function performToggleFormStatus(formId, newStatus) {
    // แสดง loading
    Swal.fire({
        title: 'กำลังอัปเดต...',
        allowOutsideClick: false,
        allowEscapeKey: false,
        showConfirmButton: false,
        didOpen: () => {
            Swal.showLoading();
        }
    });

    const formData = new FormData();
    formData.append('form_id', formId);
    formData.append('status', newStatus);

    fetch(KidFormsConfig.toggleFormUrl, {
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
                title: 'สำเร็จ!',
                text: data.message || 'เปลี่ยนสถานะฟอร์มเรียบร้อยแล้ว',
                icon: 'success',
                timer: 2000,
                showConfirmButton: false
            }).then(() => {
                location.reload();
            });
        } else {
            showErrorAlert(data.message || 'เกิดข้อผิดพลาดในการเปลี่ยนสถานะ');
        }
    })
    .catch(error => {
        console.error('Toggle status error:', error);
        showErrorAlert('ไม่สามารถเชื่อมต่อกับเซิร์ฟเวอร์ได้');
    });
}

/**
 * ยืนยันการลบฟอร์ม
 */
function confirmDeleteForm(formId, formName) {
    if (!formId) {
        showErrorAlert('ไม่พบหมายเลขฟอร์ม');
        return;
    }
    
    // ตั้งค่าข้อมูลใน Modal
    document.getElementById('deleteFormName').textContent = formName || 'ไม่ระบุ';
    
    // แสดง Modal
    const deleteModal = new bootstrap.Modal(document.getElementById('deleteFormModal'));
    deleteModal.show();
    
    // ตั้งค่า event handler สำหรับปุ่มยืนยัน
    const confirmBtn = document.getElementById('confirmDeleteFormBtn');
    
    // ลบ event listener เก่า
    const newConfirmBtn = confirmBtn.cloneNode(true);
    confirmBtn.parentNode.replaceChild(newConfirmBtn, confirmBtn);
    
    // เพิ่ม event listener ใหม่
    newConfirmBtn.addEventListener('click', function() {
        performDeleteForm(formId, deleteModal);
    });
}

/**
 * ดำเนินการลบฟอร์ม
 */
function performDeleteForm(formId, modal) {
    // ปิด Modal ก่อน
    if (modal) {
        modal.hide();
    }
    
    // แสดง loading
    Swal.fire({
        title: 'กำลังลบ...',
        text: 'กรุณารอสักครู่',
        allowOutsideClick: false,
        allowEscapeKey: false,
        showConfirmButton: false,
        didOpen: () => {
            Swal.showLoading();
        }
    });
    
    const formData = new FormData();
    formData.append('form_id', formId);
    
    fetch(KidFormsConfig.deleteFormUrl, {
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
                text: data.message || 'ลบฟอร์มเรียบร้อยแล้ว',
                icon: 'success',
                timer: 2000,
                showConfirmButton: false
            }).then(() => {
                location.reload();
            });
        } else {
            showErrorAlert(data.message || 'เกิดข้อผิดพลาดในการลบ');
        }
    })
    .catch(error => {
        console.error('Delete error:', error);
        showErrorAlert('ไม่สามารถเชื่อมต่อกับเซิร์ฟเวอร์ได้');
    });
}

/**
 * แสดง Error Alert
 */
function showErrorAlert(message) {
    Swal.fire({
        title: 'เกิดข้อผิดพลาด',
        text: message,
        icon: 'error',
        confirmButtonText: 'ตกลง'
    });
}

/**
 * รีเฟรชรายการฟอร์ม
 */
function refreshFormsList() {
    location.reload();
}

// ===================================================================
// *** EVENT HANDLERS ***
// ===================================================================

/**
 * จัดการ Form Submit สำหรับเพิ่มฟอร์ม
 */
function handleAddFormSubmit() {
    const form = document.getElementById('addFormForm');
    if (!form) return;
    
    form.addEventListener('submit', function(e) {
        e.preventDefault();
        
        const formData = new FormData(this);
        
        // แสดง loading
        Swal.fire({
            title: 'กำลังบันทึก...',
            text: 'กรุณารอสักครู่',
            allowOutsideClick: false,
            allowEscapeKey: false,
            showConfirmButton: false,
            didOpen: () => {
                Swal.showLoading();
            }
        });
        
        fetch(KidFormsConfig.addFormUrl, {
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
                    title: 'สำเร็จ!',
                    text: data.message || 'เพิ่มฟอร์มเรียบร้อยแล้ว',
                    icon: 'success',
                    timer: 2000,
                    showConfirmButton: false
                }).then(() => {
                    const modal = bootstrap.Modal.getInstance(document.getElementById('addFormModal'));
                    if (modal) modal.hide();
                    location.reload();
                });
            } else {
                showErrorAlert(data.message || 'เกิดข้อผิดพลาดในการเพิ่มฟอร์ม');
            }
        })
        .catch(error => {
            console.error('Add form error:', error);
            showErrorAlert('ไม่สามารถเชื่อมต่อกับเซิร์ฟเวอร์ได้');
        });
    });
}

/**
 * จัดการ Form Submit สำหรับแก้ไขฟอร์ม
 */
function handleEditFormSubmit() {
    const form = document.getElementById('editFormForm');
    if (!form) return;
    
    form.addEventListener('submit', function(e) {
        e.preventDefault();
        
        const formData = new FormData(this);
        
        // แสดง loading
        Swal.fire({
            title: 'กำลังบันทึกการแก้ไข...',
            text: 'กรุณารอสักครู่',
            allowOutsideClick: false,
            allowEscapeKey: false,
            showConfirmButton: false,
            didOpen: () => {
                Swal.showLoading();
            }
        });
        
        fetch(KidFormsConfig.editFormUrl, {
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
                    title: 'สำเร็จ!',
                    text: data.message || 'แก้ไขฟอร์มเรียบร้อยแล้ว',
                    icon: 'success',
                    timer: 2000,
                    showConfirmButton: false
                }).then(() => {
                    const modal = bootstrap.Modal.getInstance(document.getElementById('editFormModal'));
                    if (modal) modal.hide();
                    location.reload();
                });
            } else {
                showErrorAlert(data.message || 'เกิดข้อผิดพลาดในการแก้ไขฟอร์ม');
            }
        })
        .catch(error => {
            console.error('Edit form error:', error);
            showErrorAlert('ไม่สามารถเชื่อมต่อกับเซิร์ฟเวอร์ได้');
        });
    });
}

// ===================================================================
// *** DOCUMENT READY & INITIALIZATION ***
// ===================================================================

document.addEventListener('DOMContentLoaded', function() {
    console.log('🚀 Kid Forms Management System loading...');
    
    try {
        // Initialize form handlers
        handleAddFormSubmit();
        handleEditFormSubmit();
        
        //console.log('✅ Kid Forms Management System initialized successfully');
        
        if (KidFormsConfig.debug) {
            console.log('🔧 Debug mode enabled');
            console.log('⚙️ Configuration:', KidFormsConfig);
        }
        
    } catch (error) {
        console.error('❌ Kid Forms initialization error:', error);
        alert('เกิดข้อผิดพลาดในการโหลดระบบ กรุณารีเฟรชหน้า');
    }
});

// ===================================================================
// *** FLASH MESSAGES ***
// ===================================================================

// Success message
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

// Error message
<?php if (isset($error_message) && !empty($error_message)): ?>
document.addEventListener('DOMContentLoaded', function() {
    Swal.fire({
        title: 'เกิดข้อผิดพลาด',
        text: <?= json_encode($error_message, JSON_UNESCAPED_UNICODE) ?>,
        icon: 'error'
    });
});
<?php endif; ?>

//console.log("👶 Kid Forms Management System loaded successfully");
//console.log("🔧 Environment: <?= defined('ENVIRONMENT') ? ENVIRONMENT : 'production' ?>");
//console.log("📊 System Status: Ready");
</script>