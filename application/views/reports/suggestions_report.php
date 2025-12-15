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
	
	body {
    padding-top: 10px !important;
}
	
/* ===== Suggestions Report Specific Styles ===== */
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

.filter-card {
    background: white;
    border-radius: 15px;
    padding: 1.5rem;
    box-shadow: 0 4px 15px rgba(0,0,0,0.08);
    margin-bottom: 2rem;
}

.filter-grid {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
    gap: 1rem;
    margin-bottom: 1rem;
}

.filter-actions {
    display: flex;
    gap: 0.5rem;
    justify-content: flex-end;
    flex-wrap: wrap;
}

.stats-row {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
    gap: 1rem;
    margin-bottom: 2rem;
}

.stat-card {
    background: white;
    border-radius: 12px;
    padding: 1.5rem;
    box-shadow: 0 4px 15px rgba(0,0,0,0.08);
    position: relative;
    overflow: hidden;
    transition: all 0.3s ease;
}

.stat-card::before {
    content: '';
    position: absolute;
    top: 0;
    left: 0;
    right: 0;
    height: 4px;
}

/* Suggestion-specific colors */
.stat-card.total::before { background: linear-gradient(90deg, #66bb6a, #43a047); }
.stat-card.received::before { background: linear-gradient(90deg, #ffb74d, #fb8c00); }
.stat-card.reviewing::before { background: linear-gradient(90deg, #26c6da, #00acc1); }
.stat-card.replied::before { background: linear-gradient(90deg, #ab47bc, #8e24aa); }
.stat-card.closed::before { background: linear-gradient(90deg, #78909c, #546e7a); }

.stat-card:hover {
    transform: translateY(-3px);
    box-shadow: 0 8px 25px rgba(0,0,0,0.12);
}

.stat-header {
    display: flex;
    align-items: center;
    justify-content: space-between;
    margin-bottom: 1rem;
}

.stat-icon {
    width: 45px;
    height: 45px;
    border-radius: 10px;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 1.25rem;
    color: white;
    margin-right: 1rem;
}

.stat-icon.total { background: linear-gradient(135deg, #66bb6a, #43a047); }
.stat-icon.received { background: linear-gradient(135deg, #ffb74d, #fb8c00); }
.stat-icon.reviewing { background: linear-gradient(135deg, #26c6da, #00acc1); }
.stat-icon.replied { background: linear-gradient(135deg, #ab47bc, #8e24aa); }
.stat-icon.closed { background: linear-gradient(135deg, #78909c, #546e7a); }

.stat-value {
    font-size: 2rem;
    font-weight: 700;
    color: #1e293b;
    margin-bottom: 0.25rem;
}

.stat-label {
    color: #64748b;
    font-size: 0.9rem;
    font-weight: 500;
}

.chart-card {
    background: white;
    border-radius: 15px;
    padding: 1.5rem;
    box-shadow: 0 4px 15px rgba(0,0,0,0.08);
    margin-bottom: 2rem;
}

.chart-header {
    display: flex;
    align-items: center;
    justify-content: space-between;
    margin-bottom: 1.5rem;
    border-bottom: 1px solid #e2e8f0;
    padding-bottom: 1rem;
}

.chart-title {
    font-size: 1.25rem;
    font-weight: 600;
    color: #1e293b;
    margin: 0;
}

.table-card {
    background: white;
    border-radius: 15px;
    overflow: hidden;
    box-shadow: 0 4px 15px rgba(0,0,0,0.08);
}

.table-header {
    background: #f8fafc;
    padding: 1rem 1.5rem;
    border-bottom: 1px solid #e2e8f0;
    display: flex;
    align-items: center;
    justify-content: space-between;
}

.table-title {
    font-size: 1.25rem;
    font-weight: 600;
    color: #1e293b;
    margin: 0;
}

.table-actions {
    display: flex;
    gap: 0.5rem;
}

.table-responsive {
    max-height: 1800px;
    overflow-y: auto;
}

.table-card .table {
    margin: 0;
}

.table-card .table thead th {
    background: #f8fafc;
    border: none;
    font-weight: 600;
    color: #374151;
    padding: 1rem;
    position: sticky;
    top: 0;
    z-index: 10;
}

.table-card .table tbody td {
    padding: 1rem;
    border-color: #f1f5f9;
    vertical-align: middle;
}

.status-badge {
    padding: 0.4rem 0.8rem;
    border-radius: 20px;
    font-size: 0.75rem;
    font-weight: 600;
    text-align: center;
    white-space: nowrap;
    min-width: 120px;
    display: inline-block;
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
    padding: 0.2rem 0.6rem;
    border-radius: 12px;
    font-size: 0.7rem;
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
    padding: 0.2rem 0.6rem;
    border-radius: 12px;
    font-size: 0.7rem;
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
    padding: 0.2rem 0.6rem;
    border-radius: 12px;
    font-size: 0.7rem;
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

.action-buttons {
    display: flex;
    gap: 0.3rem;
    flex-wrap: wrap;
    justify-content: flex-start;
}

.btn-action {
    padding: 0.4rem 0.8rem;
    border-radius: 6px;
    border: none;
    font-size: 0.8rem;
    font-weight: 500;
    cursor: pointer;
    transition: all 0.3s ease;
    text-decoration: none;
    display: inline-flex;
    align-items: center;
    gap: 0.4rem;
    min-width: 70px;
    justify-content: center;
    white-space: nowrap;
}

.btn-action.view {
    background: linear-gradient(135deg, #42a5f5, #1e88e5);
    color: white;
}

.btn-action.view:hover {
    background: linear-gradient(135deg, #1e88e5, #1565c0);
    transform: translateY(-1px);
    color: white;
}

.btn-action.update {
    background: linear-gradient(135deg, #66bb6a, #43a047);
    color: white;
}

.btn-action.update:hover {
    background: linear-gradient(135deg, #43a047, #2e7d32);
    transform: translateY(-1px);
    color: white;
}

.btn-action.reply {
    background: linear-gradient(135deg, #ab47bc, #8e24aa);
    color: white;
}

.btn-action.reply:hover {
    background: linear-gradient(135deg, #8e24aa, #7b1fa2);
    transform: translateY(-1px);
    color: white;
}

/* เพิ่มสไตล์สำหรับปุ่มลบ */
.btn-action.delete {
    background: linear-gradient(135deg, #ef5350, #e53935);
    color: white;
}

.btn-action.delete:hover {
    background: linear-gradient(135deg, #e53935, #d32f2f);
    transform: translateY(-1px);
    color: white;
}

.btn-action.disabled {
    background: #f3f4f6;
    color: #9ca3af;
    cursor: not-allowed;
    opacity: 0.6;
}

.btn-action.disabled:hover {
    transform: none;
    background: #f3f4f6;
    color: #9ca3af;
}

/* Suggestion Container Styling */
.suggestion-container {
    background: #ffffff;
    border: 2px solid #c8e6c9;
    border-radius: 12px;
    margin-bottom: 1.5rem;
    overflow: hidden;
    box-shadow: 0 2px 8px rgba(76, 175, 80, 0.1);
    transition: all 0.3s ease;
}

.suggestion-container:hover {
    border-color: #81c784;
    box-shadow: 0 4px 20px rgba(76, 175, 80, 0.2);
    transform: translateY(-1px);
}

.suggestion-header {
    background: linear-gradient(135deg, #e8f5e8 0%, #c8e6c9 100%);
    padding: 0.75rem 1rem;
    border-bottom: 1px solid #a5d6a7;
    font-size: 0.875rem;
    font-weight: 600;
    color: #2e7d32;
    display: flex;
    align-items: center;
    gap: 0.5rem;
}

.suggestion-header i {
    color: #4caf50;
}

.suggestion-number {
    background: linear-gradient(135deg, #4caf50, #388e3c);
    color: white;
    padding: 0.25rem 0.75rem;
    border-radius: 20px;
    font-size: 0.75rem;
    font-weight: 700;
    margin-left: auto;
}

.suggestion-data-row {
    background: #ffffff;
    border-bottom: 1px solid #e8f5e8;
}

.suggestion-data-row:hover {
    background: #f1f8e9;
}

.suggestion-data-row td {
    border-bottom: 1px solid #e8f5e8 !important;
}

.suggestion-status-row {
    background: #f1f8e9;
    border-left: 4px solid #4caf50;
    border-bottom: none;
}

.suggestion-status-row td {
    border-bottom: none !important;
    border-top: none !important;
}

.status-cell {
    padding: 1rem !important;
    border-top: 1px solid #d1d5db !important;
}

.status-update-row {
    display: flex;
    flex-direction: column;
    gap: 1rem;
    align-items: center;
}

.status-label {
    font-weight: 600;
    color: #374151;
    display: flex;
    align-items: center;
    gap: 0.5rem;
    font-size: 0.95rem;
}

.status-label i {
    color: #4caf50;
}

.status-buttons-container {
    display: flex;
    gap: 0.5rem;
    flex-wrap: wrap;
    justify-content: center;
    align-items: center;
}

.btn-status-row {
    padding: 0.4rem 0.8rem;
    border-radius: 6px;
    border: none;
    font-size: 0.8rem;
    font-weight: 500;
    cursor: pointer;
    transition: all 0.3s ease;
    text-decoration: none;
    display: inline-flex;
    align-items: center;
    gap: 0.4rem;
    min-width: 120px;
    justify-content: center;
    white-space: nowrap;
    text-align: center;
    height: 38px;
}

.btn-status-row.received {
    background: #fff3cd;
    color: #d68910;
    border: 1px solid #ffb74d;
}

.btn-status-row.received:hover:not(:disabled) {
    background: #ffb74d;
    color: #fff;
    transform: translateY(-1px);
}

.btn-status-row.reviewing {
    background: #e0f7fa;
    color: #00695c;
    border: 1px solid #26c6da;
}

.btn-status-row.reviewing:hover:not(:disabled) {
    background: #26c6da;
    color: #fff;
    transform: translateY(-1px);
}

.btn-status-row.replied {
    background: #f3e5f5;
    color: #6a1b9a;
    border: 1px solid #ab47bc;
}

.btn-status-row.replied:hover:not(:disabled) {
    background: #ab47bc;
    color: #fff;
    transform: translateY(-1px);
}

.btn-status-row.closed {
    background: #f5f5f5;
    color: #424242;
    border: 1px solid #78909c;
}

.btn-status-row.closed:hover:not(:disabled) {
    background: #78909c;
    color: #fff;
    transform: translateY(-1px);
}

.btn-status-row.current {
    background: #f3f4f6;
    color: #6b7280;
    cursor: not-allowed;
    opacity: 0.8;
    border: 1px solid #d1d5db;
}

.btn-status-row.current:hover {
    background: #f3f4f6;
    color: #6b7280;
    transform: none;
    box-shadow: none;
}

.btn-status-row.current::before {
    content: "✓ ";
    font-weight: bold;
}

/* สไตล์สำหรับปุ่มที่ไม่มีสิทธิ์ */
.btn-status-row.current[disabled] {
    background: #fef2f2;
    color: #991b1b;
    border: 1px solid #fecaca;
    opacity: 0.7;
    cursor: not-allowed;
}

.btn-status-row.current[disabled]:hover {
    background: #fef2f2;
    color: #991b1b;
    transform: none;
    box-shadow: none;
}

.btn-status-row.current[disabled]::before {
    content: "🔒 ";
    font-weight: bold;
}

.address-display {
    max-width: 200px;
    word-wrap: break-word;
    line-height: 1.3;
}

.personal-info {
    display: flex;
    flex-direction: column;
    gap: 0.2rem;
}

.personal-info-item {
    font-size: 0.85rem;
    color: #64748b;
}

.files-display {
    display: flex;
    gap: 0.4rem;
    flex-wrap: wrap;
    align-items: center;
}

.file-item {
    position: relative;
    display: flex;
    align-items: center;
    gap: 0.3rem;
    background: #f1f5f9;
    padding: 0.3rem 0.6rem;
    border-radius: 12px;
    font-size: 0.75rem;
    color: #64748b;
    border: 1px solid #e2e8f0;
    transition: all 0.3s ease;
    cursor: pointer;
}

.file-item:hover {
    background: #e2e8f0;
    border-color: #66bb6a;
    transform: translateY(-1px);
    box-shadow: 0 2px 8px rgba(102, 187, 106, 0.2);
}

.file-item i {
    color: #66bb6a;
    font-size: 0.8rem;
}

.file-item.image-file {
    padding: 0;
    border-radius: 8px;
    overflow: hidden;
    width: 40px;
    height: 40px;
    background: none;
    border: 2px solid #e2e8f0;
}

.file-item.file-pdf {
    background: #fff5f5;
    border-color: #fed7d7;
}

.file-item.file-pdf:hover {
    background: #fed7d7;
    border-color: #dc3545;
}

.file-item.file-document {
    background: #f0f8ff;
    border-color: #bee5eb;
}

.file-item.file-document:hover {
    background: #bee5eb;
    border-color: #007bff;
}

.file-preview-img {
    width: 100%;
    height: 100%;
    object-fit: cover;
    border-radius: 6px;
}

.files-more-badge {
    background: linear-gradient(135deg, #6366f1, #8b5cf6);
    color: white;
    padding: 0.2rem 0.5rem;
    border-radius: 12px;
    font-size: 0.7rem;
    font-weight: 600;
    cursor: pointer;
    transition: all 0.3s ease;
}

.files-more-badge:hover {
    transform: scale(1.05);
    box-shadow: 0 2px 8px rgba(99, 102, 241, 0.3);
}

/* Responsive Design */
@media (max-width: 768px) {
    .stats-row {
        grid-template-columns: repeat(2, 1fr);
    }
    
    .filter-grid {
        grid-template-columns: 1fr;
    }
    
    .filter-actions {
        justify-content: stretch;
    }
    
    .filter-actions .btn {
        flex: 1;
    }
    
    .table-header {
        flex-direction: column;
        gap: 1rem;
        align-items: stretch;
    }
    
    .action-buttons {
        flex-direction: column;
        gap: 0.2rem;
    }
    
    .btn-action {
        width: 100%;
        min-width: auto;
        padding: 0.5rem;
        font-size: 0.75rem;
    }
    
    .status-buttons-container {
        flex-direction: column;
        gap: 0.2rem;
    }
    
    .btn-status-row {
        width: 100%;
        min-width: auto;
        padding: 0.4rem;
        font-size: 0.7rem;
        justify-content: flex-start;
    }
    
    .suggestion-container {
        margin-bottom: 1rem;
        font-size: 0.875rem;
    }
    
    .suggestion-header {
        padding: 0.5rem 0.75rem;
        font-size: 0.8rem;
    }
    
    .suggestion-number {
        font-size: 0.7rem;
        padding: 0.2rem 0.5rem;
    }
    
    .suggestion-data-row td {
        padding: 0.75rem 0.5rem;
        font-size: 0.8rem;
    }
    
    .suggestion-status-row .status-cell {
        padding: 0.75rem 0.5rem !important;
    }
    
    .status-label {
        font-size: 0.8rem;
        text-align: center;
        margin-bottom: 0.5rem;
    }
}
</style>

<div class="container-fluid">
    <!-- Page Header -->
    <div class="page-header">
        <h1><i class="fas fa-lightbulb me-3"></i>รายงานความคิดเห็นและข้อเสนอแนะ</h1>
        
        <!-- Breadcrumb
        <?php if (isset($breadcrumb) && !empty($breadcrumb)): ?>
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb">
                    <?php foreach ($breadcrumb as $index => $item): ?>
                        <?php if ($index === count($breadcrumb) - 1): ?>
                            <li class="breadcrumb-item active" aria-current="page"><?= $item['title'] ?></li>
                        <?php else: ?>
                            <li class="breadcrumb-item">
                                <a href="<?= $item['url'] ?>"><?= $item['title'] ?></a>
                            </li>
                        <?php endif; ?>
                    <?php endforeach; ?>
                </ol>
            </nav>
        <?php endif; ?>  -->
    </div>

    <!-- Statistics Cards -->
    <div class="stats-row">
        <div class="stat-card total">
            <div class="stat-header">
                <div class="stat-icon total">
                    <i class="fas fa-comments"></i>
                </div>
            </div>
            <div class="stat-value"><?= number_format($suggestion_summary['total'] ?? 0) ?></div>
            <div class="stat-label">ทั้งหมด</div>
        </div>

        <div class="stat-card received">
            <div class="stat-header">
                <div class="stat-icon received">
                    <i class="fas fa-inbox"></i>
                </div>
            </div>
            <div class="stat-value"><?= number_format($suggestion_summary['by_status']['received'] ?? 0) ?></div>
            <div class="stat-label">เรื่องเสนอแนะใหม่</div>
        </div>

        <div class="stat-card replied">
            <div class="stat-header">
                <div class="stat-icon replied">
                    <i class="fas fa-check-circle"></i>
                </div>
            </div>
            <div class="stat-value"><?= number_format($suggestion_summary['by_status']['replied'] ?? 0) ?></div>
            <div class="stat-label">รับเรื่องเสนอแนะแล้ว</div>
        </div>
    </div>

    <!-- Filter Card -->
    <div class="filter-card">
        <h5 class="mb-3"><i class="fas fa-filter me-2"></i>ตัวกรองข้อมูล</h5>
        <form method="GET" action="<?= site_url('Suggestions/suggestions_report') ?>" id="filterForm">
            <div class="filter-grid">
                <div class="form-group">
                    <label class="form-label">สถานะ:</label>
                    <select class="form-select" name="status">
                        <option value="">ทั้งหมด</option>
                        <?php if (isset($status_options)): ?>
                            <?php foreach ($status_options as $status): ?>
                                <option value="<?= $status['value'] ?>" 
                                        <?= ($filters['status'] ?? '') == $status['value'] ? 'selected' : '' ?>>
                                    <?= $status['label'] ?>
                                </option>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </select>
                </div>

                <div class="form-group">
                    <label class="form-label">ประเภท:</label>
                    <select class="form-select" name="type">
                        <option value="">ทั้งหมด</option>
                        <?php if (isset($type_options)): ?>
                            <?php foreach ($type_options as $type): ?>
                                <option value="<?= $type['value'] ?>" 
                                        <?= ($filters['type'] ?? '') == $type['value'] ? 'selected' : '' ?>>
                                    <?= $type['label'] ?>
                                </option>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </select>
                </div>

                <div class="form-group">
                    <label class="form-label">ความสำคัญ:</label>
                    <select class="form-select" name="priority">
                        <option value="">ทั้งหมด</option>
                        <?php if (isset($priority_options)): ?>
                            <?php foreach ($priority_options as $priority): ?>
                                <option value="<?= $priority['value'] ?>" 
                                        <?= ($filters['priority'] ?? '') == $priority['value'] ? 'selected' : '' ?>>
                                    <?= $priority['label'] ?>
                                </option>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </select>
                </div>

                <div class="form-group">
                    <label class="form-label">ประเภทผู้ใช้:</label>
                    <select class="form-select" name="user_type">
                        <option value="">ทั้งหมด</option>
                        <?php if (isset($user_type_options)): ?>
                            <?php foreach ($user_type_options as $user_type): ?>
                                <option value="<?= $user_type['value'] ?>" 
                                        <?= ($filters['user_type'] ?? '') == $user_type['value'] ? 'selected' : '' ?>>
                                    <?= $user_type['label'] ?>
                                </option>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </select>
                </div>

                <div class="form-group">
                    <label class="form-label">วันที่เริ่มต้น:</label>
                    <input type="date" class="form-control" name="date_from" 
                           value="<?= $filters['date_from'] ?? '' ?>">
                </div>

                <div class="form-group">
                    <label class="form-label">วันที่สิ้นสุด:</label>
                    <input type="date" class="form-control" name="date_to" 
                           value="<?= $filters['date_to'] ?? '' ?>">
                </div>

                <div class="form-group">
                    <label class="form-label">ค้นหา:</label>
                    <input type="text" class="form-control" name="search" 
                           placeholder="ค้นหาหัวข้อ, รายละเอียด, ผู้ส่ง..."
                           value="<?= $filters['search'] ?? '' ?>">
                </div>
            </div>
            
            <div class="filter-actions">
                <button type="submit" class="btn btn-primary">
                    <i class="fas fa-search me-1"></i>ค้นหา
                </button>
                <a href="<?= site_url('Suggestions/suggestions_report') ?>" class="btn btn-secondary">
                    <i class="fas fa-times me-1"></i>ล้างตัวกรอง
                </a>
                <a href="<?= site_url('Suggestions/export_excel') ?>" class="btn btn-success">
                    <i class="fas fa-file-excel me-1"></i>ส่งออก Excel
                </a>
            </div>
        </form>
    </div>

    <!-- Recent Suggestions & Analytics -->
    <div class="row mb-4">
        <div class="col-lg-6 col-md-12">
            <div class="chart-card">
                <div class="chart-header">
                    <h3 class="chart-title">
                        <i class="fas fa-star me-2"></i>ความคิดเห็นล่าสุด
                    </h3>
                </div>
                <div class="recent-suggestions">
                    <?php if (isset($recent_suggestions) && !empty($recent_suggestions)): ?>
                        <?php foreach (array_slice($recent_suggestions, 0, 5) as $recent): ?>
                            <div class="suggestion-item mb-3 p-3 border rounded">
                                <div class="d-flex justify-content-between align-items-start mb-2">
                                    <h6 class="mb-1">
                                        <a href="<?= site_url('Suggestions/suggestion_detail/' . $recent->suggestions_id) ?>" 
                                           class="text-decoration-none">
                                            #<?= $recent->suggestions_id ?> - <?= htmlspecialchars(mb_substr($recent->suggestions_topic, 0, 30)) ?>
                                            <?= mb_strlen($recent->suggestions_topic) > 30 ? '...' : '' ?>
                                        </a>
                                    </h6>
                                    <span class="status-badge <?= get_suggestion_status_class($recent->suggestions_status) ?>">
                                        <?= get_suggestion_status_display($recent->suggestions_status) ?>
                                    </span>
                                </div>
                                <small class="text-muted">
                                    โดย: <?= htmlspecialchars($recent->suggestions_by) ?> 
                                    | <?php 
                                        // แปลงวันที่เป็นภาษาไทย
                                        $thai_months = [
                                            '01' => 'ม.ค.', '02' => 'ก.พ.', '03' => 'มี.ค.', '04' => 'เม.ย.',
                                            '05' => 'พ.ค.', '06' => 'มิ.ย.', '07' => 'ก.ค.', '08' => 'ส.ค.',
                                            '09' => 'ก.ย.', '10' => 'ต.ค.', '11' => 'พ.ย.', '12' => 'ธ.ค.'
                                        ];
                                        
                                        $date = date('j', strtotime($recent->suggestions_datesave));
                                        $month = $thai_months[date('m', strtotime($recent->suggestions_datesave))];
                                        $year = date('Y', strtotime($recent->suggestions_datesave)) + 543;
                                        $time = date('H:i', strtotime($recent->suggestions_datesave));
                                        
                                        echo $date . ' ' . $month . ' ' . $year . ' ' . $time;
                                    ?>
                                </small>
                            </div>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <div class="text-center py-4">
                            <i class="fas fa-inbox fa-3x text-muted mb-3"></i>
                            <p class="text-muted">ยังไม่มีข้อเสนอแนะ</p>
                        </div>
                    <?php endif; ?>
                </div>
            </div>
        </div>

        <div class="col-lg-6 col-md-12">
            <div class="chart-card">
                <div class="chart-header">
                    <h3 class="chart-title">
                        <i class="fas fa-chart-pie me-2"></i>สถิติตามประเภท
                    </h3>
                </div>
                <div class="type-stats">
                    <?php 
                    $type_labels = [
                        'suggestion' => 'ข้อเสนอแนะ',
                        'feedback' => 'ความคิดเห็น', 
                        'improvement' => 'การปรับปรุง'
                    ];
                    $type_colors = [
                        'suggestion' => '#1565c0',
                        'feedback' => '#6a1b9a',
                        'improvement' => '#2e7d32'
                    ];
                    ?>
                    <?php if (isset($suggestion_summary['by_type'])): ?>
                        <?php foreach ($suggestion_summary['by_type'] as $type => $count): ?>
                            <div class="d-flex justify-content-between align-items-center mb-3 p-2 rounded" 
                                 style="background-color: <?= $type_colors[$type] ?? '#f5f5f5' ?>20;">
                                <div class="d-flex align-items-center">
                                    <div class="me-3" style="width: 20px; height: 20px; border-radius: 4px; background-color: <?= $type_colors[$type] ?? '#9e9e9e' ?>;"></div>
                                    <span class="fw-medium"><?= $type_labels[$type] ?? $type ?></span>
                                </div>
                                <span class="fw-bold"><?= number_format($count) ?></span>
                            </div>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>

    <!-- Data Table -->
    <div class="table-card">
        <div class="table-header">
            <h5 class="table-title">
                <i class="fas fa-list me-2"></i>รายการความคิดเห็นและข้อเสนอแนะ
            </h5>
            <div class="table-actions">
                <button class="btn btn-outline-primary btn-sm" onclick="refreshTable()">
                    <i class="fas fa-sync-alt me-1"></i>รีเฟรช
                </button>
            </div>
        </div>
        
        <div class="table-responsive">
            <?php if (empty($suggestions)): ?>
                <div class="suggestion-container">
                    <div class="text-center py-5">
                        <i class="fas fa-lightbulb fa-4x text-muted mb-3"></i>
                        <h5 class="text-muted">ไม่พบข้อมูลความคิดเห็น</h5>
                        <p class="text-muted">กรุณาลองใช้ตัวกรองอื่น หรือเพิ่มข้อมูลใหม่</p>
                    </div>
                </div>
            <?php else: ?>
                <?php foreach ($suggestions as $suggestion): ?>
                    <div class="suggestion-container" data-suggestion-id="<?= $suggestion->suggestions_id ?>">
                        <!-- Suggestion Header -->
                        <div class="suggestion-header">
                            <i class="fas fa-lightbulb"></i>
                            <span>ความคิดเห็นและข้อเสนอแนะ</span>
                            <span class="suggestion-number">#<?= $suggestion->suggestions_id ?></span>
                        </div>
                        
                        <!-- Suggestion Content -->
                        <table class="table mb-0">
                            <thead class="d-none">
                                <tr>
                                    <th style="width: 120px;">หมายเลข</th>
                                    <th style="width: 120px;">วันที่ส่ง</th>
                                    <th style="width: 130px;">สถานะ</th>
                                    <th style="width: 100px;">ความสำคัญ</th>
                                    <th style="width: 100px;">ประเภท</th>
                                    <th style="width: 100px;">ไฟล์แนบ</th>
                                    <th style="width: 200px;">หัวข้อ</th>
                                    <th style="width: 250px;">รายละเอียด</th>
                                    <th style="width: 150px;">ผู้ส่ง</th>
                                    <th style="width: 120px;">ประเภทผู้ใช้</th>
                                    <th style="width: 220px;">จัดการ</th>
                                </tr>
                            </thead>
                            <tbody>
                                <!-- Suggestion Data Row -->
                                <tr class="suggestion-data-row">
                                    <td class="fw-bold"><?= $suggestion->suggestions_id ?></td>
                                    <td>
                                        <small>
                                            <?php 
                                            // แปลงวันที่เป็นภาษาไทย
                                            $thai_months = [
                                                '01' => 'ม.ค.', '02' => 'ก.พ.', '03' => 'มี.ค.', '04' => 'เม.ย.',
                                                '05' => 'พ.ค.', '06' => 'มิ.ย.', '07' => 'ก.ค.', '08' => 'ส.ค.',
                                                '09' => 'ก.ย.', '10' => 'ต.ค.', '11' => 'พ.ย.', '12' => 'ธ.ค.'
                                            ];
                                            
                                            $date = date('j', strtotime($suggestion->suggestions_datesave));
                                            $month = $thai_months[date('m', strtotime($suggestion->suggestions_datesave))];
                                            $year = date('Y', strtotime($suggestion->suggestions_datesave)) + 543;
                                            $time = date('H:i', strtotime($suggestion->suggestions_datesave));
                                            ?>
                                            <?= $date ?> <?= $month ?> <?= $year ?><br>
                                            <?= $time ?> น.
                                        </small>
                                    </td>
                                    <td>
                                        <span class="status-badge <?= get_suggestion_status_class($suggestion->suggestions_status) ?>">
                                            <?= get_suggestion_status_display($suggestion->suggestions_status) ?>
                                        </span>
                                    </td>
                                    <td>
                                        <span class="priority-badge <?= $suggestion->suggestions_priority ?? 'normal' ?>">
                                            <?php
                                            $priority_labels = [
                                                'low' => 'ต่ำ',
                                                'normal' => 'ปกติ', 
                                                'high' => 'สูง',
                                                'urgent' => 'เร่งด่วน'
                                            ];
                                            echo $priority_labels[$suggestion->suggestions_priority ?? 'normal'];
                                            ?>
                                        </span>
                                    </td>
                                    <td>
                                        <span class="type-badge <?= $suggestion->suggestion_type ?? 'suggestion' ?>">
                                            <?php
                                            $type_labels = [
                                                'suggestion' => 'ข้อเสนอแนะ',
                                                'feedback' => 'ความคิดเห็น',
                                                'improvement' => 'การปรับปรุง'
                                            ];
                                            echo $type_labels[$suggestion->suggestion_type ?? 'suggestion'];
                                            ?>
                                        </span>
                                    </td>
                                    <td>
                                        <div class="files-display">
                                            <?php if (!empty($suggestion->files)): ?>
                                                <?php 
                                                $imageFiles = [];
                                                $otherFiles = [];
                                                
                                                foreach ($suggestion->files as $file) {
                                                    $isImage = in_array(strtolower(pathinfo($file->suggestions_file_original_name, PATHINFO_EXTENSION)), ['jpg', 'jpeg', 'png', 'gif', 'webp']);
                                                    if ($isImage) {
                                                        $imageFiles[] = $file;
                                                    } else {
                                                        $otherFiles[] = $file;
                                                    }
                                                }
                                                
                                                $displayFiles = array_merge(array_slice($imageFiles, 0, 2), array_slice($otherFiles, 0, 1));
                                                $remainingCount = count($suggestion->files) - count($displayFiles);
                                                ?>
                                                
                                                <?php foreach ($displayFiles as $file): ?>
                                                    <?php 
                                                    $fileExtension = strtolower(pathinfo($file->suggestions_file_original_name, PATHINFO_EXTENSION));
                                                    $isImage = in_array($fileExtension, ['jpg', 'jpeg', 'png', 'gif', 'webp']);
                                                    $isPdf = ($fileExtension === 'pdf');
                                                    $isDocument = in_array($fileExtension, ['doc', 'docx', 'xls', 'xlsx', 'ppt', 'pptx']);
                                                    
                                                    // กำหนด URL ตามประเภทไฟล์
                                                    if ($isImage) {
                                                        $fileUrl = site_url('Suggestions/view_image/' . $file->suggestions_file_name);
                                                    } else {
                                                        $fileUrl = site_url('Suggestions/download_file/' . $file->suggestions_file_name);
                                                    }
                                                    
                                                    // กำหนดไอคอนตามประเภทไฟล์
                                                    if ($isPdf) {
                                                        $fileIcon = 'fas fa-file-pdf';
                                                        $fileColor = '#dc3545'; // สีแดงสำหรับ PDF
                                                    } elseif ($isDocument) {
                                                        $fileIcon = 'fas fa-file-word';
                                                        $fileColor = '#007bff'; // สีน้ำเงินสำหรับเอกสาร
                                                    } elseif (in_array($fileExtension, ['zip', 'rar', '7z'])) {
                                                        $fileIcon = 'fas fa-file-archive';
                                                        $fileColor = '#6f42c1'; // สีม่วงสำหรับไฟล์บีบอัด
                                                    } else {
                                                        $fileIcon = 'fas fa-file';
                                                        $fileColor = '#6c757d'; // สีเทาสำหรับไฟล์ทั่วไป
                                                    }
                                                    ?>
                                                    
                                                    <?php if ($isImage): ?>
                                                        <div class="file-item image-file" 
                                                             onclick="showImagePreview('<?= $fileUrl ?>', '<?= htmlspecialchars($file->suggestions_file_original_name, ENT_QUOTES) ?>')"
                                                             title="<?= htmlspecialchars($file->suggestions_file_original_name) ?>">
                                                            <img src="<?= $fileUrl ?>" 
                                                                 alt="<?= htmlspecialchars($file->suggestions_file_original_name) ?>" 
                                                                 class="file-preview-img"
                                                                 loading="lazy">
                                                        </div>
                                                    <?php elseif ($isPdf): ?>
                                                        <div class="file-item file-pdf" 
                                                             onclick="openPdfFile('<?= $fileUrl ?>', '<?= htmlspecialchars($file->suggestions_file_original_name, ENT_QUOTES) ?>')"
                                                             title="<?= htmlspecialchars($file->suggestions_file_original_name) ?>">
                                                            <i class="<?= $fileIcon ?>" style="color: <?= $fileColor ?>;"></i>
                                                            <span class="file-name"><?= mb_substr($file->suggestions_file_original_name, 0, 8) ?><?= mb_strlen($file->suggestions_file_original_name) > 8 ? '...' : '' ?></span>
                                                        </div>
                                                    <?php else: ?>
                                                        <div class="file-item" 
                                                             onclick="downloadFile('<?= $fileUrl ?>', '<?= htmlspecialchars($file->suggestions_file_original_name, ENT_QUOTES) ?>')"
                                                             title="<?= htmlspecialchars($file->suggestions_file_original_name) ?>">
                                                            <i class="<?= $fileIcon ?>" style="color: <?= $fileColor ?>;"></i>
                                                            <span class="file-name"><?= mb_substr($file->suggestions_file_original_name, 0, 8) ?><?= mb_strlen($file->suggestions_file_original_name) > 8 ? '...' : '' ?></span>
                                                        </div>
                                                    <?php endif; ?>
                                                <?php endforeach; ?>
                                                
                                                <?php if ($remainingCount > 0): ?>
                                                    <div class="files-more-badge" 
                                                         onclick="showAllFiles('<?= $suggestion->suggestions_id ?>')"
                                                         title="ดูไฟล์ทั้งหมด">
                                                        +<?= $remainingCount ?>
                                                    </div>
                                                <?php endif; ?>
                                            <?php else: ?>
                                                <span class="text-muted small">ไม่มีไฟล์</span>
                                            <?php endif; ?>
                                        </div>
                                    </td>
                                    <td>
                                        <div class="text-truncate-2" title="<?= htmlspecialchars($suggestion->suggestions_topic) ?>">
                                            <?= htmlspecialchars($suggestion->suggestions_topic) ?>
                                        </div>
                                    </td>
                                    <td>
                                        <div class="text-truncate-3" title="<?= htmlspecialchars($suggestion->suggestions_detail) ?>">
                                            <?= htmlspecialchars(mb_substr($suggestion->suggestions_detail, 0, 100)) ?>
                                            <?= mb_strlen($suggestion->suggestions_detail) > 100 ? '...' : '' ?>
                                        </div>
                                    </td>
                                    <td>
                                        <div class="personal-info">
                                            <div class="personal-info-item">
                                                <strong><?= htmlspecialchars($suggestion->suggestions_by) ?></strong>
                                            </div>
                                            <div class="personal-info-item">
                                                <i class="fas fa-phone fa-xs"></i> <?= htmlspecialchars($suggestion->suggestions_phone) ?>
                                            </div>
                                            <?php if (!empty($suggestion->suggestions_email)): ?>
                                                <div class="personal-info-item">
                                                    <i class="fas fa-envelope fa-xs"></i> <?= htmlspecialchars($suggestion->suggestions_email) ?>
                                                </div>
                                            <?php endif; ?>
                                            <?php if (!empty($suggestion->suggestions_number)): ?>
                                                <div class="personal-info-item">
                                                    <i class="fas fa-id-card fa-xs"></i> 
                                                    <?= htmlspecialchars(substr($suggestion->suggestions_number, 0, 3) . '-****-****-**-' . substr($suggestion->suggestions_number, -2)) ?>
                                                </div>
                                            <?php endif; ?>
											
											
                                            <?php 
// ตรวจสอบว่ามีข้อมูลที่อยู่จาก full_address_details หรือไม่
$address_display = '';

if (isset($suggestion->full_address_details) && $suggestion->full_address_details['has_address']) {
    // ใช้ข้อมูลจาก full_address_details ที่ประมวลผลแล้ว
    $address_info = $suggestion->full_address_details;
    
    if (!empty($address_info['formatted_address'])) {
        $address_display = $address_info['formatted_address'];
    } elseif (!empty($address_info['full_address'])) {
        $address_display = $address_info['full_address'];
    }
} elseif (isset($suggestion->display_address) && !empty($suggestion->display_address['full'])) {
    // ใช้ข้อมูลจาก display_address
    $address_display = $suggestion->display_address['full'];
} else {
    // Fallback: สร้างที่อยู่แบบง่าย (ไม่ซ้ำกัน)
    $address_parts = [];
    
    // เพิ่มที่อยู่เพิ่มเติม (ถ้ามี)
    if (!empty($suggestion->suggestions_address)) {
        $address_parts[] = $suggestion->suggestions_address;
    }
    
    // สร้าง location string แยกต่างหาก
    $location_parts = [];
    if (!empty($suggestion->guest_district)) {
        $location_parts[] = 'ต.' . $suggestion->guest_district;
    }
    if (!empty($suggestion->guest_amphoe)) {
        $location_parts[] = 'อ.' . $suggestion->guest_amphoe;
    }
    if (!empty($suggestion->guest_province)) {
        $location_parts[] = 'จ.' . $suggestion->guest_province;
    }
    if (!empty($suggestion->guest_zipcode)) {
        $location_parts[] = $suggestion->guest_zipcode;
    }
    
    // รวมที่อยู่และ location
    if (!empty($location_parts)) {
        $address_parts[] = implode(' ', $location_parts);
    }
    
    $address_display = implode(' ', $address_parts);
}

// แสดงผลถ้ามีข้อมูลที่อยู่
if (!empty($address_display) && $address_display !== 'ไม่ระบุที่อยู่'): ?>
    <div class="personal-info-item address-display">
        <i class="fas fa-map-marker-alt fa-xs"></i>
        <?= htmlspecialchars(trim($address_display)) ?>
    </div>
<?php endif; ?>
											
											
                                        </div>
                                    </td>
                                    <td>
                                        <span class="user-type-badge <?= $suggestion->suggestions_user_type ?>">
                                            <?php
                                            $user_type_labels = [
                                                'guest' => 'ผู้ใช้ทั่วไป',
                                                'public' => 'สมาชิก',
                                                'staff' => 'เจ้าหน้าที่'
                                            ];
                                            echo $user_type_labels[$suggestion->suggestions_user_type] ?? 'ไม่ทราบ';
                                            ?>
                                        </span>
                                    </td>
                                    <td>
                                        <div class="action-buttons">
                                            <a href="<?= site_url('Suggestions/suggestion_detail/' . $suggestion->suggestions_id) ?>" 
                                               class="btn-action view" title="ดูรายละเอียด">
                                                <i class="fas fa-eye"></i>ดู
                                            </a>
                                            
                                            <?php 
                                            // เพิ่มปุ่มลบสำหรับ system_admin และ super_admin เท่านั้น
                                            $current_user_system = $user_info->m_system ?? '';
                                            if (in_array($current_user_system, ['system_admin', 'super_admin'])): ?>
                                                <button type="button" 
                                                        class="btn-action delete" 
                                                        onclick="confirmDeleteSuggestion('<?= $suggestion->suggestions_id ?>', '<?= htmlspecialchars($suggestion->suggestions_topic, ENT_QUOTES) ?>')"
                                                        title="ลบข้อเสนอแนะ">
                                                    <i class="fas fa-trash"></i>ลบ
                                                </button>
                                            <?php endif; ?>
                                        </div>
                                    </td>
                                </tr>
                                
                                <!-- Suggestion Status Management Row -->
                                <tr class="suggestion-status-row">
                                    <td colspan="11" class="status-cell">
                                        <div class="status-update-row">
                                            <div class="status-label">
                                                <i class="fas fa-sync-alt"></i>
                                                อัปเดตสถานะ #<?= $suggestion->suggestions_id ?>
                                            </div>
                                            <div class="status-buttons-container">
                                                <?php 
                                                $current_status = $suggestion->suggestions_status;
                                                
                                                // ตรวจสอบสิทธิ์การรับเรื่องเสนอแนะ
                                                $can_handle = $can_handle_suggestions ?? false;
                                                
                                                // แสดงเฉพาะปุ่มที่จำเป็น
                                                $status_buttons = [];
                                                
                                                if ($current_status === 'received') {
                                                    // ถ้าเป็น received ให้แสดงปุ่ม รับเรื่องเสนอแนะแล้ว
                                                    $status_buttons[] = ['replied', 'replied', 'fas fa-check-circle', 'รับเรื่องเสนอแนะแล้ว', $can_handle];
                                                } else {
                                                    // ถ้าเป็นสถานะอื่นๆ ให้แสดงสถานะปัจจุบัน
                                                    $status_buttons[] = [$current_status, 'replied', 'fas fa-check-circle', 'รับเรื่องเสนอแนะแล้ว', false];
                                                }
                                                
                                                foreach ($status_buttons as $status_btn): 
                                                    $status_text = $status_btn[0];
                                                    $status_class = $status_btn[1];
                                                    $status_icon = $status_btn[2];
                                                    $status_display = $status_btn[3];
                                                    $is_clickable = $status_btn[4];
                                                    
                                                    $button_classes = "btn-status-row {$status_class}";
                                                    $tooltip_text = '';
                                                    $onclick_code = '';
                                                    
                                                    if (!$is_clickable) {
                                                        $button_classes .= ' current';
                                                        if ($current_status === 'received' && !$can_handle) {
                                                            $tooltip_text = 'คุณไม่มีสิทธิ์รับเรื่องเสนอแนะ (เฉพาะ System Admin, Super Admin และ User Admin ที่มีสิทธิ์ 108)';
                                                        } else {
                                                            $tooltip_text = 'สถานะปัจจุบัน';
                                                        }
                                                    } else {
                                                        $tooltip_text = 'คลิกเพื่อ ' . $status_display;
                                                        $suggestion_id_js = intval($suggestion->suggestions_id);
                                                        $topic_js = htmlspecialchars($suggestion->suggestions_topic, ENT_QUOTES);
                                                        
                                                        $onclick_code = "onclick=\"showReplyModalWithStatus('{$suggestion_id_js}', '{$topic_js}', 'replied')\"";
                                                    }
                                                ?>
                                                    <button class="<?= $button_classes ?>"
                                                            <?= !$is_clickable ? 'disabled' : '' ?>
                                                            <?= $onclick_code ?>
                                                            title="<?= $tooltip_text ?>">
                                                        <i class="<?= $status_icon ?>"></i>
                                                        <span><?= $status_display ?></span>
                                                        <?php if ($current_status === 'received' && !$can_handle): ?>
                                                            <i class="fas fa-lock ms-1" style="font-size: 0.7em; opacity: 0.7;"></i>
                                                        <?php endif; ?>
                                                    </button>
                                                <?php endforeach; ?>
                                            </div>
                                        </div>
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                <?php endforeach; ?>
            <?php endif; ?>
        </div>

        <!-- Pagination -->
        <?php if (isset($pagination) && !empty($pagination)): ?>
            <div class="d-flex justify-content-between align-items-center p-3 border-top">
                <div class="text-muted small">
                    แสดง <?= number_format((($current_page ?? 1) - 1) * ($per_page ?? 20) + 1) ?> - 
                    <?= number_format(min(($current_page ?? 1) * ($per_page ?? 20), $total_rows ?? 0)) ?> 
                    จาก <?= number_format($total_rows ?? 0) ?> รายการ
                </div>
                <div>
                    <?= $pagination ?? '' ?>
                </div>
            </div>
        <?php endif; ?>
    </div>
</div>

<!-- Status Update Modal -->
<div class="modal fade" id="statusUpdateModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">อัปเดตสถานะ</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form id="statusUpdateForm">
                <div class="modal-body">
                    <input type="hidden" id="statusSuggestionId" name="suggestion_id">
                    
                    <div class="mb-3">
                        <label class="form-label">สถานะใหม่:</label>
                        <select class="form-select" id="statusNewStatus" name="new_status" required>
                            <option value="received">ได้รับแล้ว</option>
                            <option value="reviewing">กำลังพิจารณา</option>
                            <option value="replied">ตอบกลับแล้ว</option>
                            <option value="closed">ปิดเรื่อง</option>
                        </select>
                    </div>
                    
                    <div class="mb-3">
                        <label class="form-label">ความสำคัญ:</label>
                        <select class="form-select" id="statusNewPriority" name="new_priority">
                            <option value="low">ต่ำ</option>
                            <option value="normal">ปกติ</option>
                            <option value="high">สูง</option>
                            <option value="urgent">เร่งด่วน</option>
                        </select>
                    </div>
                    
                    <div class="mb-3">
                        <label class="form-label">ข้อความตอบกลับ (ถ้ามี):</label>
                        <textarea class="form-control" id="statusReplyMessage" name="reply_message" rows="4"
                                  placeholder="ข้อความตอบกลับสำหรับผู้ส่งข้อเสนอแนะ..."></textarea>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">ยกเลิก</button>
                    <button type="submit" class="btn btn-primary">
                        <i class="fas fa-save me-1"></i>บันทึก
                    </button>
                </div>
            </form>
        </div>
    </div>
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
                        <label class="form-label" id="replyMessageLabel">ข้อความรับเรื่อง <span class="text-danger">*</span>:</label>
                        <textarea class="form-control" id="replyMessage" name="reply_message" rows="6" required
                                  placeholder="พิมพ์ข้อความรับเรื่องเสนอแนะของคุณที่นี่..."></textarea>
                    </div>
                    
                    <div class="mb-3" id="statusSelectGroup">
                        <label class="form-label">เปลี่ยนสถานะเป็น:</label>
                        <select class="form-select" id="replyNewStatus" name="new_status">
                            <option value="replied">ตอบกลับแล้ว</option>
                            <option value="closed">ปิดเรื่อง</option>
                        </select>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">ยกเลิก</button>
                    <button type="submit" class="btn btn-primary" id="replySubmitBtn">
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
// ===================================================================
// *** Configuration และ Variables ***
// ===================================================================

// Configuration สำหรับระบบ
const SuggestionsConfig = {
    baseUrl: '<?= site_url() ?>',
    updateStatusUrl: '<?= site_url("Suggestions/update_suggestion_status") ?>',
    deleteSuggestionUrl: '<?= site_url("Suggestions/delete_suggestion") ?>',
    debug: <?= (defined('ENVIRONMENT') && ENVIRONMENT === 'development') ? 'true' : 'false' ?>
};

// แปลงสถานะเป็นภาษาไทย
const statusDisplayMap = {
    'received': 'เรื่องเสนอแนะใหม่',
    'reviewing': 'รับเรื่องเสนอแนะแล้ว', 
    'replied': 'รับเรื่องเสนอแนะแล้ว',
    'closed': 'รับเรื่องเสนอแนะแล้ว'
};

// ===================================================================
// *** Core Functions ***
// ===================================================================

/**
 * อัปเดตสถานะข้อเสนอแนะ
 */
function updateSuggestionStatus(suggestionId, newStatus) {
    console.log('updateSuggestionStatus called:', suggestionId, newStatus);
    
    if (!suggestionId || !newStatus) {
        console.error('Invalid parameters');
        showErrorAlert('ข้อมูลไม่ถูกต้อง');
        return;
    }
    
    const statusDisplay = statusDisplayMap[newStatus] || newStatus;
    
    Swal.fire({
        title: 'ยืนยันการเปลี่ยนสถานะ',
        text: `คุณต้องการเปลี่ยนสถานะเป็น "${statusDisplay}" หรือไม่?`,
        icon: 'question',
        showCancelButton: true,
        confirmButtonText: 'ยืนยัน',
        cancelButtonText: 'ยกเลิก',
        confirmButtonColor: '#28a745',
        cancelButtonColor: '#6c757d'
    }).then((result) => {
        if (result.isConfirmed) {
            performStatusUpdate(suggestionId, newStatus);
        }
    });
}

/**
 * ดำเนินการอัปเดตสถานะ
 */
function performStatusUpdate(suggestionId, newStatus) {
    // แสดง loading
    Swal.fire({
        title: 'กำลังอัปเดต...',
        text: 'กรุณารอสักครู่',
        allowOutsideClick: false,
        allowEscapeKey: false,
        showConfirmButton: false,
        didOpen: () => {
            Swal.showLoading();
        }
    });
    
    const formData = new FormData();
    formData.append('suggestion_id', suggestionId);
    formData.append('new_status', newStatus);
    
    fetch(SuggestionsConfig.updateStatusUrl, {
        method: 'POST',
        body: formData,
        headers: {
            'X-Requested-With': 'XMLHttpRequest'
        }
    })
    .then(response => {
        if (!response.ok) {
            throw new Error(`HTTP error! status: ${response.status}`);
        }
        return response.json();
    })
    .then(data => {
        console.log('Status update response:', data);
        
        if (data.success) {
            Swal.fire({
                title: 'สำเร็จ!',
                text: data.message || 'อัปเดตสถานะเรียบร้อยแล้ว',
                icon: 'success',
                timer: 2000,
                showConfirmButton: false
            }).then(() => {
                location.reload();
            });
        } else {
            showErrorAlert(data.message || 'เกิดข้อผิดพลาดในการอัปเดต');
        }
    })
    .catch(error => {
        console.error('Status update error:', error);
        showErrorAlert('ไม่สามารถเชื่อมต่อกับเซิร์ฟเวอร์ได้');
    });
}

/**
 * ยืนยันการลบข้อเสนอแนะ
 */
function confirmDeleteSuggestion(suggestionId, suggestionTopic) {
    console.log('confirmDeleteSuggestion called:', suggestionId, suggestionTopic);
    
    if (!suggestionId) {
        showErrorAlert('ไม่พบหมายเลขข้อเสนอแนะ');
        return;
    }
    
    // ตั้งค่าข้อมูลใน Modal
    document.getElementById('deleteSuggestionId').textContent = suggestionId;
    document.getElementById('deleteSuggestionTopic').textContent = suggestionTopic || 'ไม่ระบุหัวข้อ';
    document.getElementById('deleteReason').value = '';
    
    // แสดง Modal
    const deleteModal = new bootstrap.Modal(document.getElementById('deleteConfirmModal'));
    deleteModal.show();
    
    // ตั้งค่า event handler สำหรับปุ่มยืนยัน
    const confirmBtn = document.getElementById('confirmDeleteBtn');
    
    // ลบ event listener เก่า (ถ้ามี)
    const newConfirmBtn = confirmBtn.cloneNode(true);
    confirmBtn.parentNode.replaceChild(newConfirmBtn, confirmBtn);
    
    // เพิ่ม event listener ใหม่
    newConfirmBtn.addEventListener('click', function() {
        const deleteReason = document.getElementById('deleteReason').value.trim();
        performDeleteSuggestion(suggestionId, deleteReason, deleteModal);
    });
}

/**
 * ดำเนินการลบข้อเสนอแนะ
 */
function performDeleteSuggestion(suggestionId, deleteReason, modal) {
    console.log('performDeleteSuggestion called:', suggestionId, deleteReason);
    
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
    formData.append('suggestion_id', suggestionId);
    if (deleteReason) {
        formData.append('delete_reason', deleteReason);
    }
    
    fetch(SuggestionsConfig.deleteSuggestionUrl, {
        method: 'POST',
        body: formData,
        headers: {
            'X-Requested-With': 'XMLHttpRequest'
        }
    })
    .then(response => {
        if (!response.ok) {
            throw new Error(`HTTP error! status: ${response.status}`);
        }
        return response.json();
    })
    .then(data => {
        console.log('Delete response:', data);
        
        if (data.success) {
            Swal.fire({
                title: 'ลบสำเร็จ!',
                text: data.message || 'ลบข้อเสนอแนะเรียบร้อยแล้ว',
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
 * รีเฟรชตาราง
 */
function refreshTable() {
    console.log('Refreshing table...');
    
    const refreshBtn = document.querySelector('button[onclick="refreshTable()"]');
    if (refreshBtn) {
        refreshBtn.innerHTML = '<i class="fas fa-spinner fa-spin me-1"></i>กำลังโหลด...';
        refreshBtn.disabled = true;
        
        setTimeout(() => {
            location.reload();
        }, 500);
    } else {
        location.reload();
    }
}

/**
 * แสดง Modal สำหรับอัปเดตสถานะ
 */
function showUpdateStatusModal(suggestionId, currentStatus) {
    console.log('Opening update status modal:', suggestionId, currentStatus);
    
    const modal = document.getElementById('statusUpdateModal');
    if (!modal) {
        console.error('Status update modal not found');
        return;
    }
    
    // Set form values
    document.getElementById('statusSuggestionId').value = suggestionId;
    document.getElementById('statusNewStatus').value = currentStatus;
    
    // Show modal
    const bootstrapModal = new bootstrap.Modal(modal);
    bootstrapModal.show();
}

/**
 * แสดง Modal สำหรับตอบกลับพร้อมอัปเดตสถานะ
 */
function showReplyModalWithStatus(suggestionId, topic, newStatus) {
    console.log('Opening reply modal with status update:', suggestionId, topic, newStatus);
    
    const modal = document.getElementById('replyModal');
    if (!modal) {
        console.error('Reply modal not found');
        return;
    }
    
    // Set form values
    document.getElementById('replySuggestionId').value = suggestionId;
    document.getElementById('replyTopic').textContent = topic;
    document.getElementById('replyMessage').value = '';
    document.getElementById('replyNewStatus').value = newStatus;
    
    // อัปเดต Modal title ตามสถานะ
    const modalTitle = document.querySelector('#replyModal .modal-title');
    modalTitle.textContent = 'รับเรื่องเสนอแนะ';
    
    // อัปเดต label ของ textarea
    const messageLabel = document.getElementById('replyMessageLabel');
    messageLabel.innerHTML = 'ข้อความรับเรื่อง <span class="text-danger">*</span>:';
    document.getElementById('replyMessage').placeholder = 'พิมพ์ข้อความรับเรื่องเสนอแนะของคุณที่นี่...';
    
    // อัปเดตปุ่ม submit
    const submitBtn = document.getElementById('replySubmitBtn');
    submitBtn.innerHTML = '<i class="fas fa-check me-1"></i>รับเรื่องเสนอแนะ';
    submitBtn.className = 'btn btn-success';
    
    // ซ่อน dropdown สำหรับเปลี่ยนสถานะ (เพราะเราจะใช้ค่าที่กำหนดไว้แล้ว)
    const statusSelectGroup = document.getElementById('statusSelectGroup');
    if (statusSelectGroup) {
        statusSelectGroup.style.display = 'none';
    }
    
    // Show modal
    const bootstrapModal = new bootstrap.Modal(modal);
    bootstrapModal.show();
    
    // Focus on message textarea
    setTimeout(() => {
        document.getElementById('replyMessage').focus();
    }, 500);
}

/**
 * แสดงตัวอย่างรูปภาพ
 */
function showImagePreview(imageUrl, fileName) {
    console.log('Opening image preview:', imageUrl, fileName);
    
    // สร้าง modal สำหรับแสดงรูป
    const modalId = 'imagePreviewModal_' + Date.now();
    const modal = document.createElement('div');
    modal.className = 'modal fade';
    modal.id = modalId;
    modal.tabIndex = -1;
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
    
    // ลบ modal หลังจากปิด
    modal.addEventListener('hidden.bs.modal', function() {
        document.body.removeChild(modal);
    });
}

/**
 * เปิดไฟล์ PDF
 */
function openPdfFile(fileUrl, fileName) {
    console.log('Opening PDF file:', fileUrl, fileName);
    
    Swal.fire({
        title: 'เปิดไฟล์ PDF',
        text: `ต้องการเปิดไฟล์ "${fileName}" หรือไม่?`,
        icon: 'question',
        showCancelButton: true,
        confirmButtonText: 'เปิดในแท็บใหม่',
        cancelButtonText: 'ยกเลิก',
        showDenyButton: true,
        denyButtonText: 'ดาวน์โหลด',
        confirmButtonColor: '#dc3545',
        denyButtonColor: '#28a745',
        cancelButtonColor: '#6c757d'
    }).then((result) => {
        if (result.isConfirmed) {
            // เปิดในแท็บใหม่
            window.open(fileUrl, '_blank');
        } else if (result.isDenied) {
            // ดาวน์โหลดไฟล์
            downloadFile(fileUrl, fileName);
        }
    });
}

/**
 * ดาวน์โหลดไฟล์
 */
function downloadFile(fileUrl, fileName) {
    console.log('Downloading file:', fileUrl, fileName);
    
    try {
        // สร้าง link element สำหรับดาวน์โหลด
        const link = document.createElement('a');
        link.href = fileUrl;
        link.download = fileName;
        link.target = '_blank';
        
        // เพิ่ม link ลงใน DOM ชั่วคราว
        document.body.appendChild(link);
        
        // คลิกเพื่อเริ่มดาวน์โหลด
        link.click();
        
        // ลบ link ออกจาก DOM
        document.body.removeChild(link);
        
        // แสดงข้อความแจ้งเตือน
        Swal.fire({
            title: 'กำลังดาวน์โหลด',
            text: `กำลังดาวน์โหลดไฟล์ "${fileName}"`,
            icon: 'info',
            timer: 2000,
            showConfirmButton: false
        });
        
    } catch (error) {
        console.error('Download error:', error);
        
        // หากเกิดข้อผิดพลาด ให้เปิดในแท็บใหม่แทน
        Swal.fire({
            title: 'ไม่สามารถดาวน์โหลดได้',
            text: 'จะเปิดไฟล์ในแท็บใหม่แทน',
            icon: 'warning',
            timer: 2000,
            showConfirmButton: false
        }).then(() => {
            window.open(fileUrl, '_blank');
        });
    }
}

// ===================================================================
// *** Event Handlers ***
// ===================================================================

/**
 * จัดการ Form Submit สำหรับอัปเดตสถานะ
 */
function handleStatusUpdateForm() {
    const form = document.getElementById('statusUpdateForm');
    if (!form) return;
    
    form.addEventListener('submit', function(e) {
        e.preventDefault();
        
        const formData = new FormData(this);
        console.log('Submitting status update form');
        
        // แสดง loading
        Swal.fire({
            title: 'กำลังอัปเดต...',
            text: 'กรุณารอสักครู่',
            allowOutsideClick: false,
            allowEscapeKey: false,
            showConfirmButton: false,
            didOpen: () => {
                Swal.showLoading();
            }
        });
        
        fetch(SuggestionsConfig.updateStatusUrl, {
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
                    title: 'อัปเดตสำเร็จ!',
                    text: data.message || 'อัปเดตสถานะเรียบร้อยแล้ว',
                    icon: 'success',
                    timer: 2000,
                    showConfirmButton: false
                }).then(() => {
                    // ปิด Modal
                    const modal = bootstrap.Modal.getInstance(document.getElementById('statusUpdateModal'));
                    if (modal) modal.hide();
                    location.reload();
                });
            } else {
                showErrorAlert(data.message || 'เกิดข้อผิดพลาดในการอัปเดต');
            }
        })
        .catch(error => {
            console.error('Form submit error:', error);
            showErrorAlert('ไม่สามารถเชื่อมต่อกับเซิร์ฟเวอร์ได้');
        });
    });
}

/**
 * จัดการ Form Submit สำหรับตอบกลับ
 */
function handleReplyForm() {
    const form = document.getElementById('replyForm');
    if (!form) return;
    
    form.addEventListener('submit', function(e) {
        e.preventDefault();
        
        const formData = new FormData(this);
        console.log('Submitting reply form');
        
        // แสดง loading
        Swal.fire({
            title: 'กำลังรับเรื่องเสนอแนะ...',
            text: 'กรุณารอสักครู่',
            allowOutsideClick: false,
            allowEscapeKey: false,
            showConfirmButton: false,
            didOpen: () => {
                Swal.showLoading();
            }
        });
        
        fetch(SuggestionsConfig.updateStatusUrl, {
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
                    title: 'รับเรื่องเสนอแนะสำเร็จ!',
                    text: data.message || 'รับเรื่องเสนอแนะเรียบร้อยแล้ว',
                    icon: 'success',
                    timer: 2000,
                    showConfirmButton: false
                }).then(() => {
                    // ปิด Modal
                    const modal = bootstrap.Modal.getInstance(document.getElementById('replyModal'));
                    if (modal) modal.hide();
                    location.reload();
                });
            } else {
                showErrorAlert(data.message || 'เกิดข้อผิดพลาดในการรับเรื่องเสนอแนะ');
            }
        })
        .catch(error => {
            console.error('Reply form error:', error);
            showErrorAlert('ไม่สามารถเชื่อมต่อกับเซิร์ฟเวอร์ได้');
        });
    });
}

/**
 * จัดการ Search Enhancement
 */
function handleSearchEnhancement() {
    const searchInput = document.querySelector('input[name="search"]');
    if (!searchInput) return;
    
    // Enter key to submit
    searchInput.addEventListener('keypress', function(e) {
        if (e.key === 'Enter') {
            document.getElementById('filterForm').submit();
        }
    });
}

// ===================================================================
// *** Document Ready และ Initialization ***
// ===================================================================

document.addEventListener('DOMContentLoaded', function() {
    console.log('🚀 Suggestions Report System loading...');
    
    try {
        // Initialize core functionality
        handleStatusUpdateForm();
        handleReplyForm();
        handleSearchEnhancement();
        
        console.log('✅ Suggestions Report System initialized successfully');
        
        if (SuggestionsConfig.debug) {
            console.log('🔧 Debug mode enabled');
            console.log('⚙️ Configuration:', SuggestionsConfig);
        }
        
    } catch (error) {
        console.error('❌ Initialization error:', error);
        alert('เกิดข้อผิดพลาดในการโหลดระบบ กรุณารีเฟรชหน้า');
    }
});

// ===================================================================
// *** Flash Messages ***
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

// Info message
<?php if (isset($info_message) && !empty($info_message)): ?>
document.addEventListener('DOMContentLoaded', function() {
    Swal.fire({
        title: 'ข้อมูล',
        text: <?= json_encode($info_message, JSON_UNESCAPED_UNICODE) ?>,
        icon: 'info',
        timer: 4000,
        showConfirmButton: false
    });
});
<?php endif; ?>

console.log("💡 Suggestions Management System loaded successfully");
console.log("🔧 Environment: <?= defined('ENVIRONMENT') ? ENVIRONMENT : 'production' ?>");
console.log("📊 System Status: Ready");
</script>