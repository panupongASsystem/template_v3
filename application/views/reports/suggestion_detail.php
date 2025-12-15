<?php
// Helper function สำหรับ CSS class ของสถานะ
if (!function_exists('get_suggestion_status_class')) {
    function get_suggestion_status_class($status) {
        switch($status) {
            case 'received': return 'received';
            case 'reviewing': return 'replied';
            case 'replied': return 'replied';
            case 'closed': return 'replied';
            default: return 'received';
        }
    }
}

// Helper function สำหรับแสดงสถานะเป็นภาษาไทย
if (!function_exists('get_suggestion_status_display')) {
    function get_suggestion_status_display($status) {
        switch($status) {
            case 'received': return 'เรื่องเสนอแนะใหม่';
            case 'reviewing': return 'รับเรื่องเสนอแนะแล้ว';
            case 'replied': return 'รับเรื่องเสนอแนะแล้ว';
            case 'closed': return 'รับเรื่องเสนอแนะแล้ว';
            default: return 'เรื่องเสนอแนะใหม่';
        }
    }
}
?>

<!-- SweetAlert2 -->
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

<style>
/* ===== Suggestion Detail Specific Styles ===== */
.container-fluid {
    padding: 20px;
}

.page-header {
    background: linear-gradient(135deg, #e8f5e8 0%, #c8e6c9 100%);
    color: white;
    padding: 2rem;
    border-radius: 15px;
    margin-bottom: 2rem;
    box-shadow: 0 4px 15px rgba(0,0,0,0.08);
}

.page-header h1 {
    font-size: 2rem;
    font-weight: 600;
    margin: 0;
    text-shadow: 0 2px 4px rgba(0,0,0,0.2);
    color: #2e7d32 !important;
}

.page-header .breadcrumb {
    background: transparent;
    padding: 0;
    margin: 10px 0 0 0;
}

.page-header .breadcrumb-item a {
    color: rgba(255,255,255,0.8);
    text-decoration: none;
}

.page-header .breadcrumb-item.active {
    color: rgba(255,255,255,1);
}

.detail-card {
    background: white;
    border-radius: 15px;
    overflow: hidden;
    box-shadow: 0 4px 15px rgba(0,0,0,0.08);
    margin-bottom: 2rem;
}

.detail-header {
    background: linear-gradient(135deg, #f8f9fa 0%, #e9ecef 100%);
    padding: 1.5rem;
    border-bottom: 1px solid #dee2e6;
    display: flex;
    align-items: center;
    justify-content: space-between;
    flex-wrap: wrap;
    gap: 1rem;
}

.detail-title {
    font-size: 1.25rem;
    font-weight: 600;
    color: #495057;
    margin: 0;
    display: flex;
    align-items: center;
    gap: 0.5rem;
}

.detail-actions {
    display: flex;
    gap: 0.5rem;
    flex-wrap: wrap;
}

.detail-content {
    padding: 2rem;
}

.info-grid {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(300px, 1fr));
    gap: 2rem;
    margin-bottom: 2rem;
}

.info-section {
    background: #f8f9fa;
    border-radius: 12px;
    padding: 1.5rem;
    border-left: 4px solid #4caf50;
}

.info-section h5 {
    color: #2e7d32;
    font-weight: 600;
    margin-bottom: 1rem;
    display: flex;
    align-items: center;
    gap: 0.5rem;
}

.info-item {
    display: flex;
    align-items: flex-start;
    gap: 0.75rem;
    margin-bottom: 0.75rem;
    padding: 0.5rem 0;
    border-bottom: 1px solid #e9ecef;
}

.info-item:last-child {
    border-bottom: none;
    margin-bottom: 0;
}

.info-label {
    font-weight: 600;
    color: #495057;
    min-width: 120px;
    flex-shrink: 0;
}

.info-value {
    color: #6c757d;
    flex: 1;
    line-height: 1.5;
}

.status-badge {
    padding: 0.5rem 1rem;
    border-radius: 25px;
    font-size: 0.875rem;
    font-weight: 600;
    text-align: center;
    white-space: nowrap;
    display: inline-flex;
    align-items: center;
    gap: 0.5rem;
}

.status-badge.received {
    background: #fff3cd;
    color: #d68910;
    border: 1px solid #ffb74d;
}

.status-badge.reviewing {
    background: #e0f7fa;
    color: #00695c;
    border: 1px solid #26c6da;
}

.status-badge.replied {
    background: #f3e5f5;
    color: #6a1b9a;
    border: 1px solid #ab47bc;
}

.status-badge.closed {
    background: #f5f5f5;
    color: #424242;
    border: 1px solid #78909c;
}

.priority-badge {
    padding: 0.3rem 0.8rem;
    border-radius: 15px;
    font-size: 0.8rem;
    font-weight: 600;
    text-align: center;
    white-space: nowrap;
}

.priority-badge.low {
    background: #e8f5e8;
    color: #2e7d32;
}

.priority-badge.normal {
    background: #f3f4f6;
    color: #374151;
}

.priority-badge.high {
    background: #fff3e0;
    color: #e65100;
}

.priority-badge.urgent {
    background: #ffebee;
    color: #c62828;
}

.type-badge {
    padding: 0.3rem 0.8rem;
    border-radius: 15px;
    font-size: 0.8rem;
    font-weight: 600;
    text-align: center;
    white-space: nowrap;
}

.type-badge.suggestion {
    background: #e3f2fd;
    color: #1565c0;
}

.type-badge.feedback {
    background: #f3e5f5;
    color: #6a1b9a;
}

.type-badge.improvement {
    background: #e8f5e8;
    color: #2e7d32;
}

.user-type-badge {
    padding: 0.3rem 0.8rem;
    border-radius: 15px;
    font-size: 0.8rem;
    font-weight: 600;
    text-align: center;
    white-space: nowrap;
}

.user-type-badge.guest {
    background: #f5f5f5;
    color: #616161;
}

.user-type-badge.public {
    background: #e8f5e8;
    color: #2e7d32;
}

.user-type-badge.staff {
    background: #e3f2fd;
    color: #1565c0;
}

.detail-text {
    background: #ffffff;
    border: 1px solid #e9ecef;
    border-radius: 8px;
    padding: 1.5rem;
    line-height: 1.6;
    color: #495057;
    white-space: pre-wrap;
}

.files-section {
    margin-top: 2rem;
}

.files-grid {
    display: grid;
    grid-template-columns: repeat(auto-fill, minmax(200px, 1fr));
    gap: 1rem;
    margin-top: 1rem;
}

.file-card {
    background: #f8f9fa;
    border: 2px solid #e9ecef;
    border-radius: 12px;
    padding: 1rem;
    text-align: center;
    transition: all 0.3s ease;
    cursor: pointer;
}

.file-card:hover {
    border-color: #4caf50;
    transform: translateY(-2px);
    box-shadow: 0 4px 15px rgba(76, 175, 80, 0.2);
}

.file-card.image-file {
    padding: 0;
    overflow: hidden;
}

.file-preview {
    width: 100%;
    height: 150px;
    object-fit: cover;
    border-radius: 10px;
}

.file-icon {
    font-size: 3rem;
    margin-bottom: 0.5rem;
    color: #6c757d;
}

.file-icon.pdf {
    color: #dc3545;
}

.file-icon.image {
    color: #28a745;
}

.file-icon.document {
    color: #007bff;
}

.file-name {
    font-size: 0.875rem;
    font-weight: 500;
    color: #495057;
    margin-bottom: 0.25rem;
    word-break: break-word;
}

.file-size {
    font-size: 0.75rem;
    color: #6c757d;
}

.history-section {
    margin-top: 2rem;
}

.history-timeline {
    position: relative;
    padding-left: 2rem;
}

.history-timeline::before {
    content: '';
    position: absolute;
    left: 8px;
    top: 0;
    bottom: 0;
    width: 2px;
    background: #e9ecef;
}

.history-item {
    position: relative;
    margin-bottom: 1.5rem;
    background: #ffffff;
    border: 1px solid #e9ecef;
    border-radius: 12px;
    padding: 1.25rem;
    margin-left: 1rem;
}

.history-item::before {
    content: '';
    position: absolute;
    left: -1.75rem;
    top: 1.25rem;
    width: 12px;
    height: 12px;
    background: #4caf50;
    border-radius: 50%;
    border: 3px solid #ffffff;
}

.history-header {
    display: flex;
    justify-content: between;
    align-items: flex-start;
    margin-bottom: 0.75rem;
    flex-wrap: wrap;
    gap: 0.5rem;
}

.history-action {
    font-weight: 600;
    color: #495057;
}

.history-date {
    font-size: 0.875rem;
    color: #6c757d;
    margin-left: auto;
}

.history-description {
    color: #6c757d;
    line-height: 1.5;
}

.reply-section {
    background: #f8f9fa;
    border: 1px solid #e9ecef;
    border-radius: 12px;
    padding: 1.5rem;
    margin-top: 1.5rem;
}

.reply-header {
    display: flex;
    align-items: center;
    gap: 0.75rem;
    margin-bottom: 1rem;
    color: #495057;
    font-weight: 600;
}

.reply-content {
    background: #ffffff;
    border: 1px solid #dee2e6;
    border-radius: 8px;
    padding: 1.25rem;
    line-height: 1.6;
    color: #495057;
    white-space: pre-wrap;
}

.reply-meta {
    margin-top: 1rem;
    padding-top: 1rem;
    border-top: 1px solid #dee2e6;
    font-size: 0.875rem;
    color: #6c757d;
}

.action-buttons {
    display: flex;
    gap: 0.75rem;
    flex-wrap: wrap;
}

.btn-action {
    padding: 0.75rem 1.5rem;
    border-radius: 8px;
    border: none;
    font-size: 0.875rem;
    font-weight: 500;
    cursor: pointer;
    transition: all 0.3s ease;
    text-decoration: none;
    display: inline-flex;
    align-items: center;
    gap: 0.5rem;
    white-space: nowrap;
}

.btn-action.primary {
    background: linear-gradient(135deg, #4caf50, #45a049);
    color: white;
}

.btn-action.primary:hover {
    background: linear-gradient(135deg, #45a049, #3d8b40);
    transform: translateY(-1px);
    color: white;
}

.btn-action.secondary {
    background: linear-gradient(135deg, #6c757d, #5a6268);
    color: white;
}

.btn-action.secondary:hover {
    background: linear-gradient(135deg, #5a6268, #495057);
    transform: translateY(-1px);
    color: white;
}

.btn-action.danger {
    background: linear-gradient(135deg, #dc3545, #c82333);
    color: white;
}

.btn-action.danger:hover {
    background: linear-gradient(135deg, #c82333, #bd2130);
    transform: translateY(-1px);
    color: white;
}

.btn-action.outline {
    background: transparent;
    color: #4caf50;
    border: 2px solid #4caf50;
}

.btn-action.outline:hover {
    background: #4caf50;
    color: white;
}

.status-update-section {
    background: #f8f9fa;
    border: 2px solid #e9ecef;
    border-radius: 15px;
    padding: 2rem;
    margin-top: 2rem;
}

.status-update-header {
    display: flex;
    align-items: center;
    gap: 0.75rem;
    margin-bottom: 1.5rem;
    color: #495057;
    font-weight: 600;
    font-size: 1.1rem;
}

.status-buttons-grid {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
    gap: 1rem;
    margin-bottom: 1.5rem;
}

.btn-status {
    padding: 1rem 1.5rem;
    border-radius: 12px;
    border: 2px solid;
    font-size: 0.875rem;
    font-weight: 600;
    cursor: pointer;
    transition: all 0.3s ease;
    text-align: center;
    display: flex;
    flex-direction: column;
    align-items: center;
    gap: 0.5rem;
    min-height: 80px;
    justify-content: center;
}

.btn-status.received {
    background: #fff3cd;
    color: #d68910;
    border-color: #ffb74d;
}

.btn-status.received:hover:not(:disabled) {
    background: #ffb74d;
    color: #fff;
    transform: translateY(-2px);
    box-shadow: 0 4px 15px rgba(255, 183, 77, 0.4);
}

.btn-status.replied {
    background: #f3e5f5;
    color: #6a1b9a;
    border-color: #ab47bc;
}

.btn-status.replied:hover:not(:disabled) {
    background: #ab47bc;
    color: #fff;
    transform: translateY(-2px);
    box-shadow: 0 4px 15px rgba(171, 71, 188, 0.4);
}

.btn-status.current {
    background: #d4edda;
    color: #155724;
    border-color: #c3e6cb;
    cursor: not-allowed;
    opacity: 0.8;
}

.btn-status.locked {
    background: #f8d7da;
    color: #721c24;
    border-color: #f5c6cb;
    cursor: not-allowed;
    opacity: 0.7;
}

.btn-status.locked:hover {
    transform: none;
    box-shadow: none;
}

.btn-status i {
    font-size: 1.5rem;
}

.no-permission-notice {
    background: #fff3cd;
    border: 1px solid #ffeaa7;
    border-radius: 8px;
    padding: 1rem;
    margin-top: 1rem;
    color: #856404;
    font-size: 0.875rem;
    display: flex;
    align-items: center;
    gap: 0.5rem;
}

/* Responsive Design */
@media (max-width: 768px) {
    .container-fluid {
        padding: 10px;
    }
    
    .detail-header {
        flex-direction: column;
        align-items: stretch;
        gap: 1rem;
    }
    
    .detail-actions {
        justify-content: stretch;
    }
    
    .detail-actions .btn-action {
        flex: 1;
        justify-content: center;
    }
    
    .info-grid {
        grid-template-columns: 1fr;
        gap: 1rem;
    }
    
    .info-item {
        flex-direction: column;
        gap: 0.25rem;
    }
    
    .info-label {
        min-width: auto;
        font-weight: 600;
    }
    
    .files-grid {
        grid-template-columns: repeat(auto-fill, minmax(150px, 1fr));
    }
    
    .history-timeline {
        padding-left: 1rem;
    }
    
    .history-item {
        margin-left: 0.5rem;
    }
    
    .history-item::before {
        left: -1.25rem;
    }
    
    .status-buttons-grid {
        grid-template-columns: 1fr;
    }
    
    .action-buttons {
        flex-direction: column;
    }
    
    .btn-action {
        justify-content: center;
    }
}
</style>

<div class="container-fluid">
    <!-- Page Header -->
    <div class="page-header">
        <h1>
            <i class="fas fa-eye me-3"></i>รายละเอียดข้อเสนอแนะ #<?= $suggestion_data['suggestions_id'] ?? 'N/A' ?>
        </h1>
        
        
    </div>

    <?php if (isset($suggestion_data) && !empty($suggestion_data)): ?>
        <!-- Main Information Card -->
        <div class="detail-card">
            <div class="detail-header">
                <div class="detail-title">
                    <i class="fas fa-lightbulb text-warning"></i>
                    ข้อมูลพื้นฐาน
                </div>
                <div class="detail-actions">
                    <a href="<?= site_url('Suggestions/suggestions_report') ?>" class="btn-action outline">
                        <i class="fas fa-arrow-left"></i>กลับ
                    </a>
                    <?php if (isset($can_handle_suggestions) && $can_handle_suggestions): ?>
                        <?php if ($suggestion_data['suggestions_status'] === 'received'): ?>
                            <button type="button" class="btn-action primary" 
                                    onclick="showReplyModalWithStatus('<?= $suggestion_data['suggestions_id'] ?>', '<?= htmlspecialchars($suggestion_data['suggestions_topic'], ENT_QUOTES) ?>', 'replied')">
                                <i class="fas fa-check"></i>รับเรื่องเสนอแนะ
                            </button>
                        <?php endif; ?>
                    <?php endif; ?>
                    <?php if (isset($can_delete_suggestions) && $can_delete_suggestions): ?>
                        <button type="button" class="btn-action danger" 
                                onclick="confirmDeleteSuggestion('<?= $suggestion_data['suggestions_id'] ?>', '<?= htmlspecialchars($suggestion_data['suggestions_topic'], ENT_QUOTES) ?>')">
                            <i class="fas fa-trash"></i>ลบ
                        </button>
                    <?php endif; ?>
                </div>
            </div>
            
            <div class="detail-content">
                <div class="info-grid">
                    <!-- ข้อมูลหลัก -->
                    <div class="info-section">
                        <h5><i class="fas fa-info-circle"></i>ข้อมูลเรื่อง</h5>
                        
                        <div class="info-item">
                            <span class="info-label">หมายเลข:</span>
                            <span class="info-value">#<?= $suggestion_data['suggestions_id'] ?></span>
                        </div>
                        
                        <div class="info-item">
                            <span class="info-label">สถานะ:</span>
                            <span class="info-value">
                                <span class="status-badge <?= get_suggestion_status_class($suggestion_data['suggestions_status']) ?>">
                                    <i class="fas fa-circle"></i>
                                    <?= get_suggestion_status_display($suggestion_data['suggestions_status']) ?>
                                </span>
                            </span>
                        </div>
                        
                        <div class="info-item">
                            <span class="info-label">ประเภท:</span>
                            <span class="info-value">
                                <span class="type-badge <?= $suggestion_data['suggestion_type'] ?? 'suggestion' ?>">
                                    <?php
                                    $type_labels = [
                                        'suggestion' => 'ข้อเสนอแนะ',
                                        'feedback' => 'ความคิดเห็น',
                                        'improvement' => 'การปรับปรุง'
                                    ];
                                    echo $type_labels[$suggestion_data['suggestion_type'] ?? 'suggestion'];
                                    ?>
                                </span>
                            </span>
                        </div>
                        
                        <div class="info-item">
                            <span class="info-label">ความสำคัญ:</span>
                            <span class="info-value">
                                <span class="priority-badge <?= $suggestion_data['suggestions_priority'] ?? 'normal' ?>">
                                    <?php
                                    $priority_labels = [
                                        'low' => 'ต่ำ',
                                        'normal' => 'ปกติ',
                                        'high' => 'สูง',
                                        'urgent' => 'เร่งด่วน'
                                    ];
                                    echo $priority_labels[$suggestion_data['suggestions_priority'] ?? 'normal'];
                                    ?>
                                </span>
                            </span>
                        </div>
                        
                        <div class="info-item">
                            <span class="info-label">วันที่ส่ง:</span>
                            <span class="info-value">
                                <?php
                                if (!empty($suggestion_data['formatted_date'])) {
                                    echo $suggestion_data['formatted_date'];
                                } elseif (!empty($suggestion_data['suggestions_datesave'])) {
                                    echo date('d/m/Y H:i', strtotime($suggestion_data['suggestions_datesave']));
                                } else {
                                    echo 'ไม่ระบุ';
                                }
                                ?>
                            </span>
                        </div>
                        
                        <?php if (!empty($suggestion_data['suggestions_updated_at'])): ?>
                            <div class="info-item">
                                <span class="info-label">อัปเดตล่าสุด:</span>
                                <span class="info-value">
                                    <?= date('d/m/Y H:i', strtotime($suggestion_data['suggestions_updated_at'])) ?>
                                    <?php if (!empty($suggestion_data['suggestions_updated_by'])): ?>
                                        <br><small class="text-muted">โดย: <?= htmlspecialchars($suggestion_data['suggestions_updated_by']) ?></small>
                                    <?php endif; ?>
                                </span>
                            </div>
                        <?php endif; ?>
                    </div>

                    <!-- ข้อมูลผู้ส่ง -->
                    <div class="info-section">
                        <h5><i class="fas fa-user"></i>ข้อมูลผู้ส่ง</h5>
                        
                        <div class="info-item">
                            <span class="info-label">ชื่อ-นามสกุล:</span>
                            <span class="info-value">
                                <?php
                                // แสดงข้อมูลตามสิทธิ์ (staff ดูได้เต็ม, อื่นๆ เซ็นเซอร์)
                                $display_name = $suggestion_data['suggestions_by'];
                                if ($user_type !== 'staff') {
                                    $display_name = $suggestion_data['suggestions_by_censored'] ?? $display_name;
                                }
                                echo htmlspecialchars($display_name);
                                ?>
                            </span>
                        </div>
                        
                        <div class="info-item">
                            <span class="info-label">เบอร์โทรศัพท์:</span>
                            <span class="info-value">
                                <?php
                                $display_phone = $suggestion_data['suggestions_phone'];
                                if ($user_type !== 'staff') {
                                    $display_phone = $suggestion_data['suggestions_phone_censored'] ?? $display_phone;
                                }
                                echo htmlspecialchars($display_phone);
                                ?>
                            </span>
                        </div>
                        
                        <?php if (!empty($suggestion_data['suggestions_email'])): ?>
                            <div class="info-item">
                                <span class="info-label">อีเมล:</span>
                                <span class="info-value">
                                    <?php
                                    $display_email = $suggestion_data['suggestions_email'];
                                    if ($user_type !== 'staff') {
                                        $display_email = $suggestion_data['suggestions_email_censored'] ?? $display_email;
                                    }
                                    echo htmlspecialchars($display_email);
                                    ?>
                                </span>
                            </div>
                        <?php endif; ?>
                        
                        <!-- เลขบัตรประชาชน (เซ็นเซอร์ตามสิทธิ์) -->
    <?php if (!empty($suggestion_data['suggestions_number'])): ?>
        <div class="info-item">
            <span class="info-label">เลขบัตรประชาชน:</span>
            <span class="info-value">
                
                 
                    <!-- Public/Guest เห็นข้อมูลเซ็นเซอร์ -->
                    <span class="censored-data"><?= htmlspecialchars($suggestion_data['suggestions_number_censored']) ?></span>
                    <small class="text-muted">(ข้อมูลปกป้อง)</small>
                
            </span>
        </div>
    <?php endif; ?>
                        
                        <div class="info-item">
                            <span class="info-label">ประเภทผู้ใช้:</span>
                            <span class="info-value">
                                <span class="user-type-badge <?= $suggestion_data['suggestions_user_type'] ?>">
                                    <?php
                                    $user_type_labels = [
                                        'guest' => 'ผู้ใช้ทั่วไป',
                                        'public' => 'สมาชิก',
                                        'staff' => 'เจ้าหน้าที่'
                                    ];
                                    echo $user_type_labels[$suggestion_data['suggestions_user_type']] ?? 'ไม่ทราบ';
                                    ?>
                                </span>
                            </span>
                        </div>
                        
                        <?php if (!empty($suggestion_data['full_address_display']) && $suggestion_data['full_address_display'] !== 'ไม่ระบุที่อยู่'): ?>
                            <div class="info-item">
                                <span class="info-label">ที่อยู่:</span>
                                <span class="info-value"><?= htmlspecialchars($suggestion_data['full_address_display']) ?></span>
                            </div>
                        <?php endif; ?>
                    </div>
                </div>

                <!-- เนื้อหาข้อเสนอแนะ -->
                <div class="mb-4">
                    <h5 class="mb-3">
                        <i class="fas fa-comment-dots text-primary me-2"></i>หัวข้อ
                    </h5>
                    <div class="detail-text">
                        <?= htmlspecialchars($suggestion_data['suggestions_topic']) ?>
                    </div>
                </div>

                <div class="mb-4">
                    <h5 class="mb-3">
                        <i class="fas fa-align-left text-info me-2"></i>รายละเอียด
                    </h5>
                    <div class="detail-text">
                        <?= nl2br(htmlspecialchars($suggestion_data['suggestions_detail'])) ?>
                    </div>
                </div>

                <!-- ไฟล์แนบ -->
                <?php if (isset($suggestion_files) && !empty($suggestion_files)): ?>
                    <div class="files-section">
                        <h5 class="mb-3">
                            <i class="fas fa-paperclip text-secondary me-2"></i>ไฟล์แนบ (<?= count($suggestion_files) ?> ไฟล์)
                        </h5>
                        <div class="files-grid">
                            <?php foreach ($suggestion_files as $file): ?>
                                <div class="file-card <?= $file->is_image ? 'image-file' : '' ?>" 
                                     onclick="<?= $file->is_image ? 
                                        "showImagePreview('" . site_url('Suggestions/view_image/' . $file->suggestions_file_name) . "', '" . htmlspecialchars($file->suggestions_file_original_name, ENT_QUOTES) . "')" :
                                        "downloadFile('" . site_url('Suggestions/download_file/' . $file->suggestions_file_name) . "', '" . htmlspecialchars($file->suggestions_file_original_name, ENT_QUOTES) . "')" 
                                     ?>">
                                    <?php if ($file->is_image): ?>
                                        <img src="<?= site_url('Suggestions/view_image/' . $file->suggestions_file_name) ?>" 
                                             alt="<?= htmlspecialchars($file->suggestions_file_original_name) ?>" 
                                             class="file-preview">
                                    <?php else: ?>
                                        <i class="<?= $file->file_icon ?> file-icon <?= strpos($file->suggestions_file_type, 'pdf') !== false ? 'pdf' : 'document' ?>"></i>
                                        <div class="file-name"><?= htmlspecialchars($file->suggestions_file_original_name) ?></div>
                                        <div class="file-size"><?= $file->file_size_formatted ?></div>
                                    <?php endif; ?>
                                </div>
                            <?php endforeach; ?>
                        </div>
                    </div>
                <?php endif; ?>

                <!-- การตอบกลับ -->
                <?php if (!empty($suggestion_data['suggestions_reply'])): ?>
                    <div class="reply-section">
                        <div class="reply-header">
                            <i class="fas fa-reply text-success"></i>
                            การตอบกลับจากเจ้าหน้าที่
                        </div>
                        <div class="reply-content">
                            <?= nl2br(htmlspecialchars($suggestion_data['suggestions_reply'])) ?>
                        </div>
                        <div class="reply-meta">
                            <?php if (!empty($suggestion_data['suggestions_replied_by'])): ?>
                                <strong>ตอบกลับโดย:</strong> <?= htmlspecialchars($suggestion_data['suggestions_replied_by']) ?>
                            <?php endif; ?>
                            <?php if (!empty($suggestion_data['suggestions_replied_at'])): ?>
                                <br><strong>วันที่ตอบกลับ:</strong> <?= date('d/m/Y H:i', strtotime($suggestion_data['suggestions_replied_at'])) ?> น.
                            <?php endif; ?>
                        </div>
                    </div>
                <?php endif; ?>

                
                
            </div>
        </div>

        <!-- ประวัติการดำเนินการ -->
        <?php if (isset($suggestion_history) && !empty($suggestion_history)): ?>
            <div class="detail-card">
                <div class="detail-header">
                    <div class="detail-title">
                        <i class="fas fa-history text-info"></i>
                        ประวัติการดำเนินการ
                    </div>
                </div>
                
                <div class="detail-content">
                    <div class="history-section">
                        <div class="history-timeline">
                            <?php foreach (array_reverse($suggestion_history) as $history): ?>
                                <div class="history-item">
                                    <div class="history-header">
                                        <div class="history-action">
                                            <?= htmlspecialchars($history['action_description']) ?>
                                        </div>
                                        <div class="history-date">
                                            <?= date('d/m/Y H:i', strtotime($history['action_date'])) ?> น.
                                        </div>
                                    </div>
                                    <div class="history-description">
                                        <strong>ดำเนินการโดย:</strong> <?= htmlspecialchars($history['action_by']) ?>
                                        <?php if (!empty($history['old_status']) && !empty($history['new_status'])): ?>
                                            <br><strong>เปลี่ยนสถานะ:</strong> 
                                            <?= get_suggestion_status_display($history['old_status']) ?> 
                                            → 
                                            <?= get_suggestion_status_display($history['new_status']) ?>
                                        <?php endif; ?>
                                    </div>
                                </div>
                            <?php endforeach; ?>
                        </div>
                    </div>
                </div>
            </div>
        <?php endif; ?>

    <?php else: ?>
        <!-- ไม่พบข้อมูล -->
        <div class="detail-card">
            <div class="detail-content text-center py-5">
                <i class="fas fa-exclamation-triangle fa-4x text-warning mb-3"></i>
                <h4 class="text-muted">ไม่พบข้อมูลข้อเสนอแนะ</h4>
                <p class="text-muted">ข้อเสนอแนะที่ระบุไม่มีอยู่ในระบบ หรือคุณไม่มีสิทธิ์เข้าถึง</p>
                <a href="<?= site_url('Suggestions/suggestions_report') ?>" class="btn-action primary">
                    <i class="fas fa-arrow-left"></i>กลับไปหน้ารายงาน
                </a>
            </div>
        </div>
    <?php endif; ?>
</div>

<!-- Reply Modal -->
<div class="modal fade" id="replyModal" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">รับเรื่องเสนอแนะ</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form id="replyForm">
                <div class="modal-body">
                    <input type="hidden" id="replySuggestionId" name="suggestion_id">
                    
                    <div class="alert alert-info">
                        <i class="fas fa-info-circle me-2"></i>
                        <strong>หัวข้อ:</strong> <span id="replyTopic"></span>
                    </div>
                    
                    <div class="mb-3">
                        <label class="form-label">ข้อความรับเรื่อง <span class="text-danger">*</span>:</label>
                        <textarea class="form-control" id="replyMessage" name="reply_message" rows="6" required
                                  placeholder="พิมพ์ข้อความรับเรื่องเสนอแนะของคุณที่นี่..."></textarea>
                    </div>
                    
                    <div class="mb-3">
                        <label class="form-label">เปลี่ยนสถานะเป็น:</label>
                        <select class="form-select" id="replyNewStatus" name="new_status">
                            <option value="replied">ตอบกลับแล้ว</option>
                        </select>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">ยกเลิก</button>
                    <button type="submit" class="btn btn-success">
                        <i class="fas fa-check me-1"></i>รับเรื่องเสนอแนะ
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Delete Confirmation Modal -->
<div class="modal fade" id="deleteConfirmModal" tabindex="-1">
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
                
                <p>คุณต้องการลบข้อเสนอแนะนี้หรือไม่?</p>
                
                <div class="bg-light p-3 rounded">
                    <strong>หมายเลข:</strong> #<span id="deleteSuggestionId"></span><br>
                    <strong>หัวข้อ:</strong> <span id="deleteSuggestionTopic"></span>
                </div>
                
                <div class="mt-3">
                    <label class="form-label">เหตุผลในการลบ (ไม่บังคับ):</label>
                    <textarea class="form-control" id="deleteReason" rows="3" 
                              placeholder="ระบุเหตุผลในการลบข้อเสนอแนะนี้..."></textarea>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                    <i class="fas fa-times me-1"></i>ยกเลิก
                </button>
                <button type="button" class="btn btn-danger" id="confirmDeleteBtn">
                    <i class="fas fa-trash me-1"></i>ลบข้อเสนอแนะ
                </button>
            </div>
        </div>
    </div>
</div>

<!-- Bootstrap 5 JS -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>

<script>
// Configuration
const SuggestionsConfig = {
    baseUrl: '<?= site_url() ?>',
    updateStatusUrl: '<?= site_url("Suggestions/update_suggestion_status") ?>',
    deleteSuggestionUrl: '<?= site_url("Suggestions/delete_suggestion") ?>',
    debug: <?= (defined('ENVIRONMENT') && ENVIRONMENT === 'development') ? 'true' : 'false' ?>
};

// รวมฟังก์ชันทั้งหมดจากหน้า suggestions_report
function showReplyModalWithStatus(suggestionId, topic, newStatus) {
    console.log('Opening reply modal:', suggestionId, topic, newStatus);
    
    const modal = document.getElementById('replyModal');
    if (!modal) {
        console.error('Reply modal not found');
        return;
    }
    
    document.getElementById('replySuggestionId').value = suggestionId;
    document.getElementById('replyTopic').textContent = topic;
    document.getElementById('replyMessage').value = '';
    document.getElementById('replyNewStatus').value = newStatus;
    
    const bootstrapModal = new bootstrap.Modal(modal);
    bootstrapModal.show();
    
    setTimeout(() => {
        document.getElementById('replyMessage').focus();
    }, 500);
}

function updateSuggestionStatus(suggestionId, newStatus) {
    Swal.fire({
        title: 'ยืนยันการเปลี่ยนสถานะ',
        text: `คุณต้องการเปลี่ยนสถานะหรือไม่?`,
        icon: 'question',
        showCancelButton: true,
        confirmButtonText: 'ยืนยัน',
        cancelButtonText: 'ยกเลิก'
    }).then((result) => {
        if (result.isConfirmed) {
            performStatusUpdate(suggestionId, newStatus);
        }
    });
}

function performStatusUpdate(suggestionId, newStatus) {
    Swal.fire({
        title: 'กำลังอัปเดต...',
        allowOutsideClick: false,
        showConfirmButton: false,
        didOpen: () => Swal.showLoading()
    });
    
    const formData = new FormData();
    formData.append('suggestion_id', suggestionId);
    formData.append('new_status', newStatus);
    
    fetch(SuggestionsConfig.updateStatusUrl, {
        method: 'POST',
        body: formData,
        headers: { 'X-Requested-With': 'XMLHttpRequest' }
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            Swal.fire({
                title: 'สำเร็จ!',
                text: data.message || 'อัปเดตสถานะเรียบร้อยแล้ว',
                icon: 'success',
                timer: 2000,
                showConfirmButton: false
            }).then(() => location.reload());
        } else {
            Swal.fire('เกิดข้อผิดพลาด', data.message || 'ไม่สามารถอัปเดตได้', 'error');
        }
    })
    .catch(error => {
        console.error('Error:', error);
        Swal.fire('เกิดข้อผิดพลาด', 'ไม่สามารถเชื่อมต่อกับเซิร์ฟเวอร์ได้', 'error');
    });
}

function confirmDeleteSuggestion(suggestionId, suggestionTopic) {
    document.getElementById('deleteSuggestionId').textContent = suggestionId;
    document.getElementById('deleteSuggestionTopic').textContent = suggestionTopic;
    document.getElementById('deleteReason').value = '';
    
    const deleteModal = new bootstrap.Modal(document.getElementById('deleteConfirmModal'));
    deleteModal.show();
    
    const confirmBtn = document.getElementById('confirmDeleteBtn');
    const newConfirmBtn = confirmBtn.cloneNode(true);
    confirmBtn.parentNode.replaceChild(newConfirmBtn, confirmBtn);
    
    newConfirmBtn.addEventListener('click', function() {
        const deleteReason = document.getElementById('deleteReason').value.trim();
        performDeleteSuggestion(suggestionId, deleteReason, deleteModal);
    });
}

function performDeleteSuggestion(suggestionId, deleteReason, modal) {
    if (modal) modal.hide();
    
    Swal.fire({
        title: 'กำลังลบ...',
        allowOutsideClick: false,
        showConfirmButton: false,
        didOpen: () => Swal.showLoading()
    });
    
    const formData = new FormData();
    formData.append('suggestion_id', suggestionId);
    if (deleteReason) formData.append('delete_reason', deleteReason);
    
    fetch(SuggestionsConfig.deleteSuggestionUrl, {
        method: 'POST',
        body: formData,
        headers: { 'X-Requested-With': 'XMLHttpRequest' }
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            Swal.fire({
                title: 'ลบสำเร็จ!',
                text: data.message || 'ลบข้อเสนอแนะเรียบร้อยแล้ว',
                icon: 'success',
                timer: 2000,
                showConfirmButton: false
            }).then(() => {
                window.location.href = '<?= site_url("Suggestions/suggestions_report") ?>';
            });
        } else {
            Swal.fire('เกิดข้อผิดพลาด', data.message || 'ไม่สามารถลบได้', 'error');
        }
    })
    .catch(error => {
        console.error('Error:', error);
        Swal.fire('เกิดข้อผิดพลาด', 'ไม่สามารถเชื่อมต่อกับเซิร์ฟเวอร์ได้', 'error');
    });
}

function showImagePreview(imageUrl, fileName) {
    const modalId = 'imagePreviewModal_' + Date.now();
    const modal = document.createElement('div');
    modal.className = 'modal fade';
    modal.id = modalId;
    modal.innerHTML = `
        <div class="modal-dialog modal-xl">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">${fileName}</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body text-center">
                    <img src="${imageUrl}" class="img-fluid" alt="${fileName}" style="max-height: 70vh;">
                </div>
                <div class="modal-footer">
                    <a href="${imageUrl}" target="_blank" class="btn btn-primary">
                        <i class="fas fa-external-link-alt me-1"></i>เปิดในแท็บใหม่
                    </a>
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">ปิด</button>
                </div>
            </div>
        </div>
    `;
    
    document.body.appendChild(modal);
    const bootstrapModal = new bootstrap.Modal(modal);
    bootstrapModal.show();
    
    modal.addEventListener('hidden.bs.modal', function() {
        document.body.removeChild(modal);
    });
}

function downloadFile(fileUrl, fileName) {
    try {
        const link = document.createElement('a');
        link.href = fileUrl;
        link.download = fileName;
        link.target = '_blank';
        document.body.appendChild(link);
        link.click();
        document.body.removeChild(link);
        
        Swal.fire({
            title: 'กำลังดาวน์โหลด',
            text: `กำลังดาวน์โหลดไฟล์ "${fileName}"`,
            icon: 'info',
            timer: 2000,
            showConfirmButton: false
        });
    } catch (error) {
        window.open(fileUrl, '_blank');
    }
}

// Form handlers
document.addEventListener('DOMContentLoaded', function() {
    const replyForm = document.getElementById('replyForm');
    if (replyForm) {
        replyForm.addEventListener('submit', function(e) {
            e.preventDefault();
            
            Swal.fire({
                title: 'กำลังรับเรื่องเสนอแนะ...',
                allowOutsideClick: false,
                showConfirmButton: false,
                didOpen: () => Swal.showLoading()
            });
            
            const formData = new FormData(this);
            
            fetch(SuggestionsConfig.updateStatusUrl, {
                method: 'POST',
                body: formData,
                headers: { 'X-Requested-With': 'XMLHttpRequest' }
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    Swal.fire({
                        title: 'รับเรื่องเสนอแนะสำเร็จ!',
                        text: data.message || 'รับเรื่องเสนอแนะเรียบร้อยแล้ว',
                        icon: 'success',
                        timer: 2000,
                        showConfirmButton: false
                    }).then(() => location.reload());
                } else {
                    Swal.fire('เกิดข้อผิดพลาด', data.message || 'ไม่สามารถรับเรื่องได้', 'error');
                }
            })
            .catch(error => {
                console.error('Error:', error);
                Swal.fire('เกิดข้อผิดพลาด', 'ไม่สามารถเชื่อมต่อกับเซิร์ฟเวอร์ได้', 'error');
            });
        });
    }
});

console.log('💡 Suggestion Detail System loaded successfully');
</script>