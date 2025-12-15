<!-- Assessment Comments Management Panel - Elegant Design -->
<div class="comments-admin-container">
    <!-- Header Spacing -->
    <div style="height: 60px;"></div>
    
    <div class="row mb-4">
        <div class="col-12">
            <div class="card shadow-elegant">
                <div class="card-header gradient-header-orange">
                    <div class="d-flex justify-content-between align-items-center">
                        <h5 class="mb-0 header-title-white">
                            <i class="fas fa-comments me-2"></i>
                            ข้อเสนอแนะและความคิดเห็นจากการประเมิน
                        </h5>
                        <div class="btn-group" role="group">
                            <a href="<?= site_url('System_reports/assessment_admin') ?>" class="btn btn-soft-light btn-sm elegant-btn">
                                <i class="fas fa-arrow-left me-1"></i>
                                กลับ
                            </a>
                            <button type="button" class="btn btn-soft-white btn-sm elegant-btn" onclick="refreshData()">
                                <i class="fas fa-sync-alt me-1"></i>
                                รีเฟรช
                            </button>
                            <a href="<?= site_url('System_reports/export_comments_csv') ?>" class="btn btn-soft-success btn-sm elegant-btn">
                                <i class="fas fa-download me-1"></i>
                                ส่งออก CSV
                            </a>
                        </div>
                    </div>
                </div>
                <div class="card-body soft-bg">
                    <!-- Statistics Row -->
                    <div class="row mb-4">
                        <div class="col-md-3 col-sm-6 mb-3">
                            <div class="stat-card soft-primary">
                                <div class="stat-card-header">
                                    <div class="stat-icon primary-icon">
                                        <i class="fas fa-comment-dots"></i>
                                    </div>
                                    <div class="stat-change positive">
                                        <i class="fas fa-chart-line"></i>
                                    </div>
                                </div>
                                <div class="stat-info">
                                    <div class="stat-label">ข้อเสนอแนะทั้งหมด</div>
                                    <div class="stat-value primary-text">
                                        <?= number_format($stats['total_comments'] ?? 0) ?> รายการ
                                    </div>
                                </div>
                            </div>
                        </div>
                        
                        <div class="col-md-3 col-sm-6 mb-3">
                            <div class="stat-card soft-success">
                                <div class="stat-card-header">
                                    <div class="stat-icon success-icon">
                                        <i class="fas fa-calendar-day"></i>
                                    </div>
                                    <div class="stat-change positive">
                                        <i class="fas fa-plus"></i>
                                    </div>
                                </div>
                                <div class="stat-info">
                                    <div class="stat-label">วันนี้</div>
                                    <div class="stat-value success-text">
                                        <?= number_format($stats['today_comments'] ?? 0) ?> รายการ
                                    </div>
                                </div>
                            </div>
                        </div>
                        
                        <div class="col-md-3 col-sm-6 mb-3">
                            <div class="stat-card soft-warning">
                                <div class="stat-card-header">
                                    <div class="stat-icon warning-icon">
                                        <i class="fas fa-calendar-week"></i>
                                    </div>
                                    <div class="stat-change positive">
                                        <i class="fas fa-arrow-up"></i>
                                    </div>
                                </div>
                                <div class="stat-info">
                                    <div class="stat-label">7 วันล่าสุด</div>
                                    <div class="stat-value warning-text">
                                        <?= number_format($stats['week_comments'] ?? 0) ?> รายการ
                                    </div>
                                </div>
                            </div>
                        </div>
                        
                        <div class="col-md-3 col-sm-6 mb-3">
                            <div class="stat-card soft-info">
                                <div class="stat-card-header">
                                    <div class="stat-icon info-icon">
                                        <i class="fas fa-calendar-alt"></i>
                                    </div>
                                    <div class="stat-change positive">
                                        <i class="fas fa-calendar"></i>
                                    </div>
                                </div>
                                <div class="stat-info">
                                    <div class="stat-label">เดือนนี้</div>
                                    <div class="stat-value info-text">
                                        <?= number_format($stats['month_comments'] ?? 0) ?> รายการ
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Filter Section -->
                    <div class="row mb-4">
                        <div class="col-12">
                            <div class="card elegant-filter-card">
                                <div class="card-header elegant-filter-header">
                                    <h6 class="mb-0 filter-title">
                                        <i class="fas fa-filter me-2"></i>
                                        ตัวกรองข้อมูล
                                    </h6>
                                </div>
                                <div class="card-body soft-filter-bg">
                                    <form method="GET" action="<?= site_url('System_reports/assessment_comments') ?>" id="filterForm">
                                        <div class="row g-3">
                                            <div class="col-md-3">
                                                <label class="form-label elegant-label">
                                                    <i class="fas fa-search me-1"></i>
                                                    ค้นหาในข้อความ
                                                </label>
                                                <input type="text" class="form-control elegant-input" name="search" 
                                                       value="<?= htmlspecialchars($filter['search'] ?? '') ?>" 
                                                       placeholder="พิมพ์คำที่ต้องการค้นหา...">
                                            </div>
                                            <div class="col-md-2">
                                                <label class="form-label elegant-label">
                                                    <i class="fas fa-calendar me-1"></i>
                                                    วันที่เริ่มต้น
                                                </label>
                                                <input type="date" class="form-control elegant-input" name="date_from" 
                                                       value="<?= $filter['date_from'] ?? '' ?>">
                                            </div>
                                            <div class="col-md-2">
                                                <label class="form-label elegant-label">
                                                    <i class="fas fa-calendar me-1"></i>
                                                    วันที่สิ้นสุด
                                                </label>
                                                <input type="date" class="form-control elegant-input" name="date_to" 
                                                       value="<?= $filter['date_to'] ?? '' ?>">
                                            </div>
                                            <div class="col-md-3">
                                                <label class="form-label elegant-label">
                                                    <i class="fas fa-folder me-1"></i>
                                                    หมวดหมู่
                                                </label>
                                                <select class="form-select elegant-select" name="category">
                                                    <option value="">ทุกหมวดหมู่</option>
                                                    <?php if (!empty($stats['by_category'])): ?>
                                                        <?php foreach ($stats['by_category'] as $cat): ?>
                                                            <option value="<?= $cat->category_name ?>" 
                                                                    <?= ($filter['category'] ?? '') === $cat->category_name ? 'selected' : '' ?>>
                                                                <?= $cat->category_name ?> (<?= $cat->count ?>)
                                                            </option>
                                                        <?php endforeach; ?>
                                                    <?php endif; ?>
                                                </select>
                                            </div>
                                            <div class="col-md-2">
                                                <label class="form-label elegant-label">&nbsp;</label>
                                                <div class="d-grid gap-2">
                                                    <button type="submit" class="btn btn-soft-primary elegant-btn">
                                                        <i class="fas fa-search me-1"></i>
                                                        ค้นหา
                                                    </button>
                                                    <a href="<?= site_url('System_reports/assessment_comments') ?>" class="btn btn-soft-secondary elegant-btn">
                                                        <i class="fas fa-times me-1"></i>
                                                        ล้าง
                                                    </a>
                                                </div>
                                            </div>
                                        </div>
                                    </form>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Top Keywords Section -->
                    <?php if (!empty($stats['top_keywords'])): ?>
                    <div class="row mb-4">
                        <div class="col-12">
                            <div class="card elegant-keywords-card">
                                <div class="card-header elegant-keywords-header">
                                    <h6 class="mb-0 keywords-title">
                                        <i class="fas fa-tags me-2"></i>
                                        คำที่พบบ่อยในข้อเสนอแนะ (Top 10)
                                    </h6>
                                </div>
                                <div class="card-body soft-keywords-bg">
                                    <div class="row">
                                        <?php foreach ($stats['top_keywords'] as $word => $count): ?>
                                            <div class="col-md-2 col-sm-4 col-6 mb-3">
                                                <div class="keyword-item elegant-keyword">
                                                    <div class="keyword-content">
                                                        <span class="keyword-text"><?= htmlspecialchars($word) ?></span>
                                                        <span class="keyword-badge"><?= $count ?></span>
                                                    </div>
                                                </div>
                                            </div>
                                        <?php endforeach; ?>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <?php endif; ?>

                    <!-- Comments List -->
                    <div class="row">
                        <div class="col-12">
                            <div class="card elegant-comments-card">
                                <div class="card-header elegant-comments-header">
                                    <div class="d-flex justify-content-between align-items-center">
                                        <h6 class="mb-0 comments-title">
                                            <i class="fas fa-list me-2"></i>
                                            รายการข้อเสนอแนะ
                                            <?php if (!empty($filter['search']) || !empty($filter['date_from']) || !empty($filter['date_to']) || !empty($filter['category'])): ?>
                                                <span class="filter-badge">
                                                    <i class="fas fa-filter"></i> มีการกรอง
                                                </span>
                                            <?php endif; ?>
                                        </h6>
                                        <div class="comments-info">
                                            <i class="fas fa-info-circle"></i>
                                            แสดง <?= count($comments) ?> รายการ
                                        </div>
                                    </div>
                                </div>
                                <div class="card-body p-0 soft-comments-bg">
                                    <?php if (!empty($comments)): ?>
                                        <div class="elegant-table-container">
                                            <table class="table elegant-table mb-0">
                                                <thead class="elegant-table-head">
                                                    <tr>
                                                        <th width="12%">วันที่-เวลา</th>
                                                        <th width="15%">หมวดหมู่</th>
                                                        <th width="20%">คำถาม</th>
                                                        <th width="45%">ข้อเสนอแนะ</th>
                                                        <th width="8%">จัดการ</th>
                                                    </tr>
                                                </thead>
                                                <tbody class="elegant-table-body">
                                                    <?php foreach ($comments as $comment): ?>
                                                        <tr class="elegant-table-row">
                                                            <td>
                                                                <div class="date-container">
                                                                    <div class="date-primary">
                                                                        <?= date('d/m/Y', strtotime($comment->completed_at)) ?>
                                                                    </div>
                                                                    <div class="time-secondary">
                                                                        <?= date('H:i น.', strtotime($comment->completed_at)) ?>
                                                                    </div>
                                                                </div>
                                                            </td>
                                                            <td>
                                                                <span class="category-badge">
                                                                    <?= htmlspecialchars($comment->category_name) ?>
                                                                </span>
                                                            </td>
                                                            <td>
                                                                <div class="question-text">
                                                                    <?= htmlspecialchars($comment->question_text) ?>
                                                                </div>
                                                            </td>
                                                            <td>
                                                                <div class="comment-text elegant-comment-content">
                                                                    <?php 
                                                                    $text = htmlspecialchars($comment->answer_text);
                                                                    if (strlen($text) > 200) {
                                                                        echo '<div class="comment-preview">' . substr($text, 0, 200) . '...</div>';
                                                                        echo '<div class="comment-full d-none">' . nl2br($text) . '</div>';
                                                                        echo '<button type="button" class="btn elegant-toggle-btn" onclick="toggleComment(this)">อ่านเพิ่มเติม</button>';
                                                                    } else {
                                                                        echo nl2br($text);
                                                                    }
                                                                    ?>
                                                                </div>
                                                            </td>
                                                            <td>
                                                                <div class="action-buttons">
                                                                    <button type="button" class="btn elegant-action-btn btn-info" 
                                                                            onclick="viewDetails(<?= $comment->response_id ?>)"
                                                                            title="ดูรายละเอียด">
                                                                        <i class="fas fa-eye"></i>
                                                                    </button>
                                                                    <button type="button" class="btn elegant-action-btn btn-success" 
                                                                            onclick="copyText('<?= addslashes($comment->answer_text) ?>')"
                                                                            title="คัดลอกข้อความ">
                                                                        <i class="fas fa-copy"></i>
                                                                    </button>
                                                                </div>
                                                            </td>
                                                        </tr>
                                                    <?php endforeach; ?>
                                                </tbody>
                                            </table>
                                        </div>
                                        
                                        <!-- Pagination -->
                                        <?php if (!empty($pagination)): ?>
                                            <div class="card-footer elegant-pagination-footer">
                                                <?= $pagination ?>
                                            </div>
                                        <?php endif; ?>
                                        
                                    <?php else: ?>
                                        <div class="empty-state">
                                            <i class="fas fa-comment-slash fa-3x text-muted mb-3"></i>
                                            <h5 class="text-muted">ไม่พบข้อเสนอแนะ</h5>
                                            <p class="text-muted">
                                                <?php if (!empty($filter['search']) || !empty($filter['date_from']) || !empty($filter['date_to']) || !empty($filter['category'])): ?>
                                                    ลองปรับเปลี่ยนเงื่อนไขการค้นหา หรือ
                                                    <a href="<?= site_url('System_reports/assessment_comments') ?>" class="btn btn-soft-primary btn-sm">
                                                        ล้างตัวกรอง
                                                    </a>
                                                <?php else: ?>
                                                    ยังไม่มีข้อเสนอแนะจากการประเมิน
                                                <?php endif; ?>
                                            </p>
                                        </div>
                                    <?php endif; ?>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Modal สำหรับดูรายละเอียด -->
<div class="modal fade" id="detailModal" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content elegant-modal">
            <div class="modal-header elegant-modal-header">
                <h5 class="modal-title modal-title-white">
                    <i class="fas fa-info-circle me-2"></i>
                    รายละเอียดการตอบแบบประเมิน
                </h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body elegant-modal-body" id="detailContent">
                <div class="text-center py-4">
                    <i class="fas fa-spinner fa-spin fa-2x text-primary"></i>
                    <p class="mt-2">กำลังโหลดข้อมูล...</p>
                </div>
            </div>
            <div class="modal-footer elegant-modal-footer">
                <button type="button" class="btn btn-soft-secondary elegant-btn" data-bs-dismiss="modal">
                    <i class="fas fa-times me-1"></i>ปิด
                </button>
            </div>
        </div>
    </div>
</div>

<!-- CSS Styles - Elegant Orange Theme -->
<style>
/* Main Container */
.comments-admin-container {
    min-height: 100vh;
    padding: 10px;
}

/* Elegant Card Shadows */
.shadow-elegant {
    box-shadow: 
        0 4px 20px rgba(251, 146, 60, 0.15),
        0 2px 6px rgba(0,0,0,0.04),
        inset 0 1px 0 rgba(255,255,255,0.9);
    border: 1px solid rgba(251, 146, 60, 0.2);
    border-radius: 16px;
    backdrop-filter: blur(10px);
}

/* Orange Gradient Header */
.gradient-header-orange {
    background: linear-gradient(135deg, 
        rgba(251, 146, 60, 0.9) 0%, 
        rgba(249, 115, 22, 0.9) 50%, 
        rgba(234, 88, 12, 0.9) 100%);
    border-bottom: 1px solid rgba(251, 146, 60, 0.2);
    border-radius: 16px 16px 0 0;
    backdrop-filter: blur(20px);
}

.header-title-white {
    color: white;
    font-weight: 600;
    text-shadow: 0 1px 3px rgba(0,0,0,0.3);
}

/* Soft Backgrounds */
.soft-bg {
    background: linear-gradient(135deg, 
        rgba(255,255,255,0.95) 0%, 
        rgba(255, 247, 237, 0.95) 100%);
}

/* Stat Cards - Same as assessment_admin */
.stat-card {
    background: linear-gradient(135deg, rgba(255,255,255,0.95) 0%, rgba(248,250,252,0.95) 100%);
    border-radius: 12px;
    padding: 20px;
    border: 1px solid rgba(255,255,255,0.3);
    box-shadow: 
        0 3px 12px rgba(0,0,0,0.06),
        inset 0 1px 0 rgba(255,255,255,0.8);
    transition: all 0.3s ease;
    backdrop-filter: blur(10px);
}

.stat-card:hover {
    transform: translateY(-3px);
    box-shadow: 
        0 8px 25px rgba(0,0,0,0.12),
        inset 0 1px 0 rgba(255,255,255,0.9);
}

.soft-success {
    background: linear-gradient(135deg, rgba(34, 197, 94, 0.08) 0%, rgba(74, 222, 128, 0.08) 100%);
    border-left: 4px solid rgba(34, 197, 94, 0.3);
}

.soft-primary {
    background: linear-gradient(135deg, rgba(59, 130, 246, 0.08) 0%, rgba(99, 102, 241, 0.08) 100%);
    border-left: 4px solid rgba(59, 130, 246, 0.3);
}

.soft-warning {
    background: linear-gradient(135deg, rgba(245, 158, 11, 0.08) 0%, rgba(251, 191, 36, 0.08) 100%);
    border-left: 4px solid rgba(245, 158, 11, 0.3);
}

.soft-info {
    background: linear-gradient(135deg, rgba(14, 165, 233, 0.08) 0%, rgba(56, 189, 248, 0.08) 100%);
    border-left: 4px solid rgba(14, 165, 233, 0.3);
}

/* Filter Card */
.elegant-filter-card {
    border-radius: 16px;
    border: 1px solid rgba(14, 165, 233, 0.2);
    box-shadow: 0 4px 20px rgba(14, 165, 233, 0.08);
}

.elegant-filter-header {
    background: linear-gradient(135deg, rgba(14, 165, 233, 0.1) 0%, rgba(56, 189, 248, 0.1) 100%);
    border-bottom: 1px solid rgba(14, 165, 233, 0.1);
    border-radius: 16px 16px 0 0;
    padding: 15px 20px;
}

.filter-title {
    color: #0284c7;
    font-weight: 600;
}

.soft-filter-bg {
    background: linear-gradient(135deg, rgba(255,255,255,0.9) 0%, rgba(240,249,255,0.9) 100%);
}

/* Form Elements */
.elegant-label {
    font-weight: 600;
    color: #374151;
    margin-bottom: 8px;
}

.elegant-input, .elegant-select {
    border: 1px solid rgba(203, 213, 225, 0.5);
    border-radius: 8px;
    padding: 10px 12px;
    background: linear-gradient(135deg, rgba(255,255,255,0.9) 0%, rgba(248,250,252,0.9) 100%);
    backdrop-filter: blur(5px);
    transition: all 0.3s ease;
}

.elegant-input:focus, .elegant-select:focus {
    border-color: rgba(59, 130, 246, 0.5);
    box-shadow: 0 0 0 3px rgba(59, 130, 246, 0.1);
    background: rgba(255,255,255,0.95);
}

/* Keywords Card */
.elegant-keywords-card {
    border-radius: 16px;
    border: 1px solid rgba(245, 158, 11, 0.2);
    box-shadow: 0 4px 20px rgba(245, 158, 11, 0.08);
}

.elegant-keywords-header {
    background: linear-gradient(135deg, rgba(245, 158, 11, 0.1) 0%, rgba(251, 191, 36, 0.1) 100%);
    border-bottom: 1px solid rgba(245, 158, 11, 0.1);
    border-radius: 16px 16px 0 0;
    padding: 15px 20px;
}

.keywords-title {
    color: #d97706;
    font-weight: 600;
}

.soft-keywords-bg {
    background: linear-gradient(135deg, rgba(255,255,255,0.9) 0%, rgba(255,251,235,0.9) 100%);
}

.elegant-keyword {
    background: linear-gradient(135deg, rgba(255,255,255,0.9) 0%, rgba(254,243,199,0.9) 100%);
    border: 1px solid rgba(245, 158, 11, 0.2);
    border-radius: 12px;
    padding: 12px 15px;
    transition: all 0.3s ease;
    backdrop-filter: blur(5px);
}

.elegant-keyword:hover {
    transform: translateY(-2px);
    border-color: rgba(245, 158, 11, 0.4);
    box-shadow: 0 4px 15px rgba(245, 158, 11, 0.2);
}

.keyword-content {
    display: flex;
    justify-content: space-between;
    align-items: center;
}

.keyword-text {
    font-weight: 600;
    color: #92400e;
}

.keyword-badge {
    background: linear-gradient(135deg, rgba(245, 158, 11, 0.9) 0%, rgba(251, 191, 36, 0.9) 100%);
    color: white;
    padding: 4px 8px;
    border-radius: 12px;
    font-size: 0.75rem;
    font-weight: 700;
    box-shadow: 0 2px 4px rgba(245, 158, 11, 0.3);
}

/* Comments Card */
.elegant-comments-card {
    border-radius: 16px;
    border: 1px solid rgba(156, 163, 175, 0.2);
    box-shadow: 0 4px 20px rgba(0,0,0,0.06);
}

.elegant-comments-header {
    background: linear-gradient(135deg, rgba(243, 244, 246, 0.9) 0%, rgba(249, 250, 251, 0.9) 100%);
    border-bottom: 1px solid rgba(209, 213, 219, 0.3);
    border-radius: 16px 16px 0 0;
    padding: 15px 20px;
}

.comments-title {
    color: #374151;
    font-weight: 600;
}

.filter-badge {
    background: linear-gradient(135deg, rgba(14, 165, 233, 0.9) 0%, rgba(56, 189, 248, 0.9) 100%);
    color: white;
    font-size: 0.75rem;
    padding: 4px 8px;
    border-radius: 12px;
    margin-left: 8px;
    box-shadow: 0 2px 4px rgba(14, 165, 233, 0.3);
}

.comments-info {
    color: #6b7280;
    font-size: 0.875rem;
}

.soft-comments-bg {
    background: linear-gradient(135deg, rgba(255,255,255,0.95) 0%, rgba(249,250,251,0.95) 100%);
}

/* Elegant Table */
.elegant-table-container {
    border-radius: 0 0 16px 16px;
    overflow: hidden;
}

.elegant-table {
    margin: 0;
    border: none;
}

.elegant-table-head {
    background: linear-gradient(135deg, rgba(249, 250, 251, 0.95) 0%, rgba(243, 244, 246, 0.95) 100%);
}

.elegant-table-head th {
    border: none;
    border-bottom: 2px solid rgba(229, 231, 235, 0.5);
    font-weight: 700;
    color: #374151;
    padding: 16px 20px;
    font-size: 0.875rem;
    text-transform: uppercase;
    letter-spacing: 0.5px;
}

.elegant-table-body {
    background: rgba(255,255,255,0.5);
}

.elegant-table-row {
    transition: all 0.3s ease;
    border: none;
}

.elegant-table-row:hover {
    background: linear-gradient(135deg, rgba(59, 130, 246, 0.05) 0%, rgba(147, 197, 253, 0.05) 100%);
    transform: translateX(3px);
}

.elegant-table-row td {
    border: none;
    border-bottom: 1px solid rgba(229, 231, 235, 0.3);
    padding: 16px 20px;
    vertical-align: middle;
}

/* Table Content Styling */
.date-container {
    text-align: left;
}

.date-primary {
    color: #2563eb;
    font-weight: 700;
    font-size: 0.9rem;
}

.time-secondary {
    color: #6b7280;
    font-size: 0.8rem;
    margin-top: 2px;
}

.category-badge {
    background: linear-gradient(135deg, rgba(59, 130, 246, 0.9) 0%, rgba(99, 102, 241, 0.9) 100%);
    color: white;
    padding: 6px 12px;
    border-radius: 20px;
    font-size: 0.75rem;
    font-weight: 600;
    box-shadow: 0 2px 4px rgba(59, 130, 246, 0.3);
}

.question-text {
    color: #6b7280;
    font-size: 0.85rem;
    line-height: 1.4;
}

.elegant-comment-content {
    line-height: 1.6;
    word-wrap: break-word;
    color: #374151;
}

.elegant-toggle-btn {
    background: none;
    border: none;
    color: #2563eb;
    font-size: 0.75rem;
    text-decoration: underline;
    padding: 0;
    margin-top: 5px;
    transition: all 0.3s ease;
}

.elegant-toggle-btn:hover {
    color: #1d4ed8;
    text-decoration: none;
}

/* Action Buttons */
.action-buttons {
    display: flex;
    gap: 5px;
}

.elegant-action-btn {
    width: 32px;
    height: 32px;
    padding: 0;
    border-radius: 8px;
    border: none;
    font-size: 0.75rem;
    transition: all 0.3s ease;
    backdrop-filter: blur(5px);
}

.elegant-action-btn.btn-info {
    background: linear-gradient(135deg, rgba(14, 165, 233, 0.9) 0%, rgba(56, 189, 248, 0.9) 100%);
    color: white;
    box-shadow: 0 2px 4px rgba(14, 165, 233, 0.3);
}

.elegant-action-btn.btn-success {
    background: linear-gradient(135deg, rgba(34, 197, 94, 0.9) 0%, rgba(74, 222, 128, 0.9) 100%);
    color: white;
    box-shadow: 0 2px 4px rgba(34, 197, 94, 0.3);
}

.elegant-action-btn:hover {
    transform: translateY(-1px);
    box-shadow: 0 4px 12px rgba(0,0,0,0.2);
}

/* Buttons */
.btn-soft-white {
    background: linear-gradient(135deg, rgba(255,255,255,0.95) 0%, rgba(248,250,252,0.95) 100%);
    border: 1px solid rgba(255,255,255,0.5);
    color: #374151;
    backdrop-filter: blur(10px);
}

.btn-soft-light {
    background: linear-gradient(135deg, rgba(255,255,255,0.9) 0%, rgba(248,250,252,0.9) 100%);
    border: 1px solid rgba(203, 213, 225, 0.3);
    color: #475569;
    box-shadow: 0 2px 8px rgba(0,0,0,0.06);
}

.btn-soft-success {
    background: linear-gradient(135deg, rgba(34, 197, 94, 0.1) 0%, rgba(74, 222, 128, 0.1) 100%);
    border: 1px solid rgba(34, 197, 94, 0.2);
    color: #059669;
    box-shadow: 0 2px 8px rgba(34, 197, 94, 0.1);
}

.btn-soft-primary {
    background: linear-gradient(135deg, rgba(59, 130, 246, 0.1) 0%, rgba(99, 102, 241, 0.1) 100%);
    border: 1px solid rgba(59, 130, 246, 0.2);
    color: #2563eb;
    box-shadow: 0 2px 8px rgba(59, 130, 246, 0.1);
}

.btn-soft-secondary {
    background: linear-gradient(135deg, rgba(107, 114, 128, 0.1) 0%, rgba(156, 163, 175, 0.1) 100%);
    border: 1px solid rgba(107, 114, 128, 0.2);
    color: #4b5563;
    box-shadow: 0 2px 8px rgba(107, 114, 128, 0.1);
}

.elegant-btn {
    border-radius: 8px;
    font-weight: 600;
    padding: 8px 16px;
    transition: all 0.3s ease;
    backdrop-filter: blur(10px);
}

.elegant-btn:hover {
    transform: translateY(-1px);
    box-shadow: 0 4px 15px rgba(0,0,0,0.1);
}

/* Modal Styling */
.elegant-modal {
    border-radius: 16px;
    border: none;
    overflow: hidden;
    box-shadow: 0 20px 60px rgba(0,0,0,0.2);
}

.elegant-modal-header {
    background: linear-gradient(135deg, rgba(14, 165, 233, 0.9) 0%, rgba(56, 189, 248, 0.9) 100%);
    border: none;
    color: white;
}

.modal-title-white {
    color: white;
    font-weight: 600;
}

.elegant-modal-body {
    background: linear-gradient(135deg, rgba(255,255,255,0.95) 0%, rgba(240,249,255,0.95) 100%);
}

.elegant-modal-footer {
    background: linear-gradient(135deg, rgba(248,250,252,0.95) 0%, rgba(240,249,255,0.95) 100%);
    border: none;
}

/* Pagination */
.elegant-pagination-footer {
    background: linear-gradient(135deg, rgba(249, 250, 251, 0.95) 0%, rgba(243, 244, 246, 0.95) 100%);
    border-top: 1px solid rgba(229, 231, 235, 0.3);
    border-radius: 0 0 16px 16px;
}

/* Empty State */
.empty-state {
    text-align: center;
    padding: 60px 20px;
    color: #6b7280;
}

/* Text Colors - Same as assessment_admin */
.success-text {
    color: #059669 !important;
    font-weight: 700;
    text-shadow: 0 1px 2px rgba(0,0,0,0.1);
}

.primary-text {
    color: #2563eb !important;
    font-weight: 700;
    text-shadow: 0 1px 2px rgba(0,0,0,0.1);
}

.warning-text {
    color: #d97706 !important;
    font-weight: 700;
    text-shadow: 0 1px 2px rgba(0,0,0,0.1);
}

.info-text {
    color: #0284c7 !important;
    font-weight: 700;
    text-shadow: 0 1px 2px rgba(0,0,0,0.1);
}

/* Icons - Same as assessment_admin */
.success-icon {
    background: linear-gradient(135deg, rgba(34, 197, 94, 0.15) 0%, rgba(74, 222, 128, 0.15) 100%);
    color: #059669;
}

.primary-icon {
    background: linear-gradient(135deg, rgba(59, 130, 246, 0.15) 0%, rgba(99, 102, 241, 0.15) 100%);
    color: #2563eb;
}

.warning-icon {
    background: linear-gradient(135deg, rgba(245, 158, 11, 0.15) 0%, rgba(251, 191, 36, 0.15) 100%);
    color: #d97706;
}

.info-icon {
    background: linear-gradient(135deg, rgba(14, 165, 233, 0.15) 0%, rgba(56, 189, 248, 0.15) 100%);
    color: #0284c7;
}

.stat-icon {
    width: 50px;
    height: 50px;
    border-radius: 12px;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 20px;
    box-shadow: 0 3px 12px rgba(0,0,0,0.1);
    backdrop-filter: blur(10px);
}

/* Responsive Design */
@media (max-width: 768px) {
    .comments-admin-container {
        padding: 10px;
    }
    
    .elegant-table-container {
        font-size: 0.85rem;
    }
    
    .elegant-table-head th,
    .elegant-table-row td {
        padding: 12px 10px;
    }
    
    .action-buttons {
        flex-direction: column;
        gap: 3px;
    }
    
    .elegant-action-btn {
        width: 28px;
        height: 28px;
    }
    
    .btn-group {
        flex-direction: column;
        width: 100%;
    }
    
    .elegant-keyword {
        margin-bottom: 0.5rem;
    }
}

/* Animation */
.stat-card, .elegant-filter-card, .elegant-keywords-card, .elegant-comments-card {
    animation: fadeInUp 0.6s ease-out;
}

@keyframes fadeInUp {
    from {
        opacity: 0;
        transform: translateY(30px);
    }
    to {
        opacity: 1;
        transform: translateY(0);
    }
}

/* Loading Animation */
.updated {
    animation: highlight-elegant 1s ease;
}

@keyframes highlight-elegant {
    0% { 
        background-color: rgba(59, 130, 246, 0.2); 
        transform: scale(1.02); 
    }
    100% { 
        background-color: transparent; 
        transform: scale(1); 
    }
}
</style>

<!-- JavaScript -->
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Initialize tooltips
    var tooltipTriggerList = [].slice.call(document.querySelectorAll('[title]'));
    var tooltipList = tooltipTriggerList.map(function (tooltipTriggerEl) {
        return new bootstrap.Tooltip(tooltipTriggerEl);
    });
});

// Toggle comment text
function toggleComment(button) {
    const container = button.closest('.comment-text');
    const preview = container.querySelector('.comment-preview');
    const full = container.querySelector('.comment-full');
    
    if (preview.classList.contains('d-none')) {
        preview.classList.remove('d-none');
        full.classList.add('d-none');
        button.textContent = 'อ่านเพิ่มเติม';
    } else {
        preview.classList.add('d-none');
        full.classList.remove('d-none');
        button.textContent = 'ย่อข้อความ';
    }
}

// Copy text to clipboard
function copyText(text) {
    navigator.clipboard.writeText(text).then(function() {
        showToast('คัดลอกข้อความเรียบร้อยแล้ว', 'success');
    }).catch(function(err) {
        console.error('Could not copy text: ', err);
        showToast('ไม่สามารถคัดลอกข้อความได้', 'error');
    });
}

// View response details
function viewDetails(responseId) {
    const modal = new bootstrap.Modal(document.getElementById('detailModal'));
    modal.show();
    
    // Load response details via AJAX
    fetch(`<?= site_url('System_reports/get_response_detail/') ?>${responseId}`, {
        method: 'GET',
        headers: { 'X-Requested-With': 'XMLHttpRequest' }
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            displayResponseDetails(data.response);
        } else {
            document.getElementById('detailContent').innerHTML = 
                '<div class="alert alert-danger">ไม่สามารถโหลดข้อมูลได้</div>';
        }
    })
    .catch(error => {
        console.error('Error:', error);
        document.getElementById('detailContent').innerHTML = 
            '<div class="alert alert-danger">เกิดข้อผิดพลาดในการโหลดข้อมูล</div>';
    });
}

// Display response details
function displayResponseDetails(response) {
    let html = `
        <div class="row mb-3 p-3 rounded" style="background: linear-gradient(135deg, rgba(59, 130, 246, 0.05) 0%, rgba(147, 197, 253, 0.05) 100%);">
            <div class="col-md-6">
                <strong><i class="fas fa-calendar me-1"></i> วันที่ส่ง:</strong> 
                <span class="text-primary">${new Date(response.completed_at).toLocaleDateString('th-TH')}</span>
            </div>
            <div class="col-md-6">
                <strong><i class="fas fa-clock me-1"></i> เวลา:</strong> 
                <span class="text-primary">${new Date(response.completed_at).toLocaleTimeString('th-TH')}</span>
            </div>
        </div>
        <hr style="border-top: 2px solid rgba(59, 130, 246, 0.1);">
        <h6 style="color: #374151; font-weight: 600;">
            <i class="fas fa-list-ul me-2"></i>คำตอบทั้งหมด:
        </h6>
    `;
    
    response.answers.forEach(answer => {
        html += `
            <div class="mb-3 p-3 rounded" style="background: linear-gradient(135deg, rgba(255,255,255,0.8) 0%, rgba(248,250,252,0.8) 100%); border: 1px solid rgba(203, 213, 225, 0.3);">
                <div style="background: linear-gradient(135deg, rgba(59, 130, 246, 0.9) 0%, rgba(99, 102, 241, 0.9) 100%); color: white; padding: 8px 12px; border-radius: 8px; font-weight: 600; margin-bottom: 12px;">
                    <i class="fas fa-folder-open me-1"></i> ${answer.category_name}
                </div>
                <div class="mb-2">
                    <strong style="color: #374151;"><i class="fas fa-question-circle me-1"></i> คำถาม:</strong> 
                    <span style="color: #6b7280;">${answer.question_text}</span>
                </div>
                <div>
                    <strong style="color: #374151;"><i class="fas fa-comment me-1"></i> คำตอบ:</strong> 
                    <div class="mt-2 p-3 rounded" style="background: linear-gradient(135deg, rgba(249, 250, 251, 0.9) 0%, rgba(243, 244, 246, 0.9) 100%); border: 1px solid rgba(229, 231, 235, 0.5); color: #374151; line-height: 1.6;">
                        ${answer.answer_text || answer.answer_value}
                    </div>
                </div>
            </div>
        `;
    });
    
    document.getElementById('detailContent').innerHTML = html;
}

// Refresh data
function refreshData() {
    showToast('กำลังรีเฟรชข้อมูล...', 'info');
    setTimeout(() => {
        location.reload();
    }, 1000);
}

// Toast notification function - Same as assessment_admin
function showToast(message, type = 'info') {
    const typeClasses = {
        'success': 'text-white',
        'error': 'text-white', 
        'info': 'text-white',
        'warning': 'text-dark'
    };
    
    const bgStyles = {
        'success': 'background: linear-gradient(135deg, rgba(34, 197, 94, 0.95) 0%, rgba(74, 222, 128, 0.95) 100%);',
        'error': 'background: linear-gradient(135deg, rgba(239, 68, 68, 0.95) 0%, rgba(248, 113, 113, 0.95) 100%);',
        'info': 'background: linear-gradient(135deg, rgba(14, 165, 233, 0.95) 0%, rgba(56, 189, 248, 0.95) 100%);',
        'warning': 'background: linear-gradient(135deg, rgba(245, 158, 11, 0.95) 0%, rgba(251, 191, 36, 0.95) 100%);'
    };
    
    const icons = {
        'success': 'fa-check-circle',
        'error': 'fa-exclamation-triangle',
        'info': 'fa-info-circle',
        'warning': 'fa-exclamation-circle'
    };
    
    const toastHtml = `
        <div class="toast align-items-center ${typeClasses[type]} border-0" 
             style="${bgStyles[type]} backdrop-filter: blur(20px); box-shadow: 0 8px 32px rgba(0,0,0,0.1);"
             role="alert" aria-live="assertive" aria-atomic="true">
            <div class="d-flex">
                <div class="toast-body" style="font-weight: 500;">
                    <i class="fas ${icons[type]} me-2"></i>
                    ${message}
                </div>
                <button type="button" class="btn-close ${type === 'warning' ? '' : 'btn-close-white'} me-2 m-auto" data-bs-dismiss="toast"></button>
            </div>
        </div>
    `;
    
    let toastContainer = document.getElementById('toastContainer');
    if (!toastContainer) {
        toastContainer = document.createElement('div');
        toastContainer.id = 'toastContainer';
        toastContainer.className = 'toast-container position-fixed top-0 end-0 p-3';
        toastContainer.style.zIndex = '9999';
        document.body.appendChild(toastContainer);
    }
    
    toastContainer.insertAdjacentHTML('beforeend', toastHtml);
    
    const toastElement = toastContainer.lastElementChild;
    const toast = new bootstrap.Toast(toastElement, { delay: 4000 });
    toast.show();
    
    toastElement.addEventListener('hidden.bs.toast', () => {
        toastElement.remove();
    });
}
</script>