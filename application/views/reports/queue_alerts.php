<style>
/* Queue Alerts Specific Styles */
.alert-priority-badge {
    padding: 0.25rem 0.5rem;
    border-radius: 12px;
    font-size: 0.75rem;
    font-weight: 600;
    text-transform: uppercase;
}

.alert-priority-badge.critical {
    background: #dc3545;
    color: white;
}

.alert-priority-badge.danger {
    background: #fd7e14;
    color: white;
}

.alert-priority-badge.warning {
    background: #ffc107;
    color: #212529;
}

.days-badge {
    background: #f8f9fa;
    color: #495057;
    padding: 0.25rem 0.5rem;
    border-radius: 8px;
    font-size: 0.8rem;
    font-weight: 600;
}

.alert-card {
    transition: all 0.3s ease;
    border-left: 4px solid transparent;
}

.alert-card.critical {
    border-left-color: #dc3545;
}

.alert-card.danger {
    border-left-color: #fd7e14;
}

.alert-card.warning {
    border-left-color: #ffc107;
}

.alert-card:hover {
    transform: translateY(-2px);
    box-shadow: 0 4px 15px rgba(0,0,0,0.1);
}

/* Stats Grid and Cards */
.stats-grid {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
    gap: 1rem;
    margin-bottom: 2rem;
}

.stat-card {
    background: white;
    border-radius: 12px;
    padding: 1.5rem;
    box-shadow: 0 2px 10px rgba(0,0,0,0.08);
    border: 1px solid #e9ecef;
    transition: all 0.3s ease;
    position: relative;
    overflow: hidden;
}

.stat-card:hover {
    transform: translateY(-3px);
    box-shadow: 0 8px 25px rgba(0,0,0,0.15);
}

.stat-card.critical {
    border-left: 4px solid #dc3545;
}

.stat-card.danger {
    border-left: 4px solid #fd7e14;
}

.stat-card.warning {
    border-left: 4px solid #ffc107;
}

.stat-icon {
    width: 50px;
    height: 50px;
    border-radius: 12px;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 1.5rem;
    margin-bottom: 1rem;
    color: white;
}

.stat-icon.total {
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
}

.stat-icon.cancelled {
    background: linear-gradient(135deg, #dc3545 0%, #c82333 100%);
}

.stat-icon.waiting {
    background: linear-gradient(135deg, #fd7e14 0%, #e55a00 100%);
}

.stat-icon.confirmed {
    background: linear-gradient(135deg, #ffc107 0%, #ffb300 100%);
}

.stat-icon.completed {
    background: linear-gradient(135deg, #28a745 0%, #20c997 100%);
}

.stat-value {
    font-size: 2.5rem;
    font-weight: bold;
    color: #2c3e50;
    margin: 0.5rem 0;
    line-height: 1;
}

.stat-label {
    font-size: 0.9rem;
    color: #6c757d;
    font-weight: 500;
    text-transform: uppercase;
    letter-spacing: 0.5px;
}

.priority-tab {
    cursor: pointer;
    transition: all 0.3s ease;
}

.priority-tab.active {
    background: #007bff;
    color: white;
}

/* Status Badge Styles */
.status-badge {
    padding: 0.25rem 0.75rem;
    border-radius: 12px;
    font-size: 0.75rem;
    font-weight: 600;
    text-transform: uppercase;
    border: 1px solid transparent;
}

.status-badge.queue-status-pending {
    background: #fff3cd;
    color: #856404;
    border-color: #ffeaa7;
}

.status-badge.queue-status-confirmed {
    background: #d1ecf1;
    color: #0c5460;
    border-color: #bee5eb;
}

.status-badge.queue-status-processing {
    background: #e2e3f1;
    color: #383d41;
    border-color: #c3c4d3;
}

.status-badge.queue-status-completed {
    background: #d4edda;
    color: #155724;
    border-color: #c3e6cb;
}

.status-badge.queue-status-cancelled {
    background: #f8d7da;
    color: #721c24;
    border-color: #f5c6cb;
}

.status-badge.queue-status-unknown {
    background: #e9ecef;
    color: #6c757d;
    border-color: #dee2e6;
}

/* Page Header Styles  42a5f5 */
.page-header {
     background: linear-gradient(135deg, #e0f2fe 0%, #b3e5fc 100%);
    color: white;
    padding: 2rem;
    border-radius: 12px;
    margin-bottom: 2rem;
    box-shadow: 0 4px 15px rgba(0,0,0,0.1);
}

.page-header h1 {
    margin: 0;
    font-size: 2rem;
    font-weight: 600;
}

.page-header p {
    margin: 0.5rem 0 0 0;
    opacity: 0.9;
}

/* Filter Card Styles */
.filter-card {
    background: white;
    border-radius: 12px;
    padding: 1.5rem;
    margin-bottom: 2rem;
    box-shadow: 0 2px 10px rgba(0,0,0,0.08);
    border: 1px solid #e9ecef;
}

.filter-grid {
    display: grid;
    grid-template-columns: repeat(5, 1fr);
    gap: 1rem;
    margin-bottom: 1.5rem;
    align-items: end;
}

@media (max-width: 1200px) {
    .filter-grid {
        grid-template-columns: repeat(3, 1fr);
    }
}

@media (max-width: 768px) {
    .filter-grid {
        grid-template-columns: repeat(2, 1fr);
    }
}

@media (max-width: 480px) {
    .filter-grid {
        grid-template-columns: 1fr;
    }
}

.filter-actions {
    display: flex;
    gap: 0.5rem;
    flex-wrap: wrap;
}

/* Table Card Styles */
.table-card {
    background: white;
    border-radius: 12px;
    box-shadow: 0 2px 10px rgba(0,0,0,0.08);
    border: 1px solid #e9ecef;
    overflow: hidden;
}

.table-header {
    background: #f8f9fa;
    padding: 1rem 1.5rem;
    border-bottom: 1px solid #e9ecef;
    display: flex;
    justify-content: space-between;
    align-items: center;
}

.table-title {
    margin: 0;
    color: #495057;
    font-weight: 600;
}

.table-actions {
    display: flex;
    gap: 0.5rem;
}

/* Pagination Styles */
.pagination-wrapper {
    padding: 1rem 1.5rem;
    background: #f8f9fa;
    border-top: 1px solid #e9ecef;
}

.pagination-container {
    display: flex;
    justify-content: space-between;
    align-items: center;
    flex-wrap: wrap;
    gap: 1rem;
}

.pagination-info {
    color: #6c757d;
    font-size: 0.9rem;
}

.pagination-info .highlight {
    font-weight: 600;
    color: #495057;
}

/* Responsive adjustments */
@media (max-width: 768px) {
    .stats-grid {
        grid-template-columns: repeat(auto-fit, minmax(150px, 1fr));
    }
    
    .filter-grid {
        grid-template-columns: 1fr;
    }
    
    .filter-actions {
        justify-content: center;
    }
    
    .table-header {
        flex-direction: column;
        gap: 1rem;
        text-align: center;
    }
    
    .pagination-container {
        flex-direction: column;
        text-align: center;
    }
}
</style>

<div class="container-fluid">
    <!-- Page Header -->
    <div class="page-header">
    <h1 style="color: #1565c0;"><i class="fas fa-exclamation-triangle me-3 text-warning"></i>คิวที่ต้องติดตาม</h1>
    <p class="text-muted">รายการคิวที่ค้างนานและต้องการความสนใจเป็นพิเศษ</p>
</div>

    <!-- Statistics Cards -->
    <div class="stats-grid">
        <div class="stat-card">
            <div class="stat-icon total">
                <i class="fas fa-list"></i>
            </div>
            <div class="stat-value"><?= number_format($stats['total']) ?></div>
            <div class="stat-label">คิวทั้งหมด</div>
        </div>

        <div class="stat-card critical">
            <div class="stat-icon cancelled">
                <i class="fas fa-fire"></i>
            </div>
            <div class="stat-value"><?= number_format($stats['critical']) ?></div>
            <div class="stat-label">วิกฤต (14+ วัน)</div>
        </div>

        <div class="stat-card danger">
            <div class="stat-icon waiting">
                <i class="fas fa-exclamation-triangle"></i>
            </div>
            <div class="stat-value"><?= number_format($stats['danger']) ?></div>
            <div class="stat-label">เร่งด่วน (7-13 วัน)</div>
        </div>

        <div class="stat-card warning">
            <div class="stat-icon confirmed">
                <i class="fas fa-clock"></i>
            </div>
            <div class="stat-value"><?= number_format($stats['warning']) ?></div>
            <div class="stat-label">ติดตาม (3-6 วัน)</div>
        </div>

        <div class="stat-card">
            <div class="stat-icon completed">
                <i class="fas fa-chart-line"></i>
            </div>
            <div class="stat-value"><?= $stats['avg_days'] ?></div>
            <div class="stat-label">เฉลี่ย (วัน)</div>
        </div>
    </div>

    <!-- Filter Section -->
    <div class="filter-card">
        <h5 class="mb-3"><i class="fas fa-filter me-2"></i>ตัวกรองข้อมูล</h5>
        <form method="GET" action="<?= site_url('Queue/queue_alerts') ?>" id="filterForm">
            <div class="filter-grid">
                <div class="form-group">
                    <label class="form-label">ระดับความสำคัญ:</label>
                    <select class="form-select form-select-sm" name="priority">
                        <?php foreach ($priority_options as $option): ?>
                            <option value="<?= $option['value'] ?>" 
                                    <?= ($filters['priority'] ?? '') == $option['value'] ? 'selected' : '' ?>>
                                <?= $option['label'] ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>

                <div class="form-group">
                    <label class="form-label">สถานะ:</label>
                    <select class="form-select form-select-sm" name="status">
                        <?php foreach ($status_options as $option): ?>
                            <option value="<?= $option['value'] ?>" 
                                    <?= ($filters['status'] ?? '') == $option['value'] ? 'selected' : '' ?>>
                                <?= $option['label'] ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>

                <div class="form-group">
                    <label class="form-label">วันขั้นต่ำ:</label>
                    <input type="number" class="form-control form-control-sm" name="days_min" 
                           value="<?= $filters['days_min'] ?? 3 ?>" min="1" max="365"
                           placeholder="วัน">
                </div>

                <div class="form-group">
                    <label class="form-label">วันสูงสุด:</label>
                    <input type="number" class="form-control form-control-sm" name="days_max" 
                           value="<?= $filters['days_max'] ?? '' ?>" min="1" max="365"
                           placeholder="วัน">
                </div>

                <div class="form-group">
                    <label class="form-label">ค้นหา:</label>
                    <input type="text" class="form-control form-control-sm" name="search" 
                           placeholder="คิว, หัวข้อ, ผู้จอง..."
                           value="<?= $filters['search'] ?? '' ?>">
                </div>
            </div>
            
            <div class="filter-actions">
                <button type="submit" class="btn btn-primary btn-sm">
                    <i class="fas fa-search me-1"></i>ค้นหา
                </button>
                <a href="<?= site_url('Queue/queue_alerts') ?>" class="btn btn-secondary btn-sm">
                    <i class="fas fa-times me-1"></i>ล้างตัวกรอง
                </a>
                <a href="<?= site_url('Queue/export_queue_alerts') ?>" class="btn btn-success btn-sm">
                    <i class="fas fa-file-excel me-1"></i>ส่งออก Excel
                </a>
                <a href="<?= site_url('Queue/queue_report') ?>" class="btn btn-outline-primary btn-sm">
                    <i class="fas fa-arrow-left me-1"></i>กลับหน้ารายงาน
                </a>
            </div>
        </form>
    </div>

    <!-- Queue Alerts List -->
    <div class="table-card">
        <div class="table-header">
            <h5 class="table-title">
                <i class="fas fa-list-alt me-2"></i>รายการคิวที่ต้องติดตาม
                <span class="badge bg-warning text-dark ms-2"><?= number_format($total_rows) ?> รายการ</span>
            </h5>
            <div class="table-actions">
                <button class="btn btn-outline-primary btn-sm" onclick="location.reload()">
                    <i class="fas fa-sync-alt me-1"></i>รีเฟรช
                </button>
            </div>
        </div>

        <?php if (empty($queue_alerts)): ?>
            <div class="text-center py-5">
                <i class="fas fa-check-circle fa-4x text-success mb-3"></i>
                <h5 class="text-success">🎉 ยอดเยี่ยม!</h5>
                <p class="text-muted">ไม่มีคิวที่ต้องติดตาม ทุกคิวอยู่ในกำหนดเวลาที่เหมาะสม</p>
                <a href="<?= site_url('Queue/queue_report') ?>" class="btn btn-primary">
                    <i class="fas fa-arrow-left me-1"></i>กลับหน้ารายงาน
                </a>
            </div>
        <?php else: ?>
            <div class="table-responsive">
                <?php foreach ($queue_alerts as $alert): ?>
                    <div class="alert-card <?= $alert->priority ?> mb-3 p-3 bg-white rounded shadow-sm">
                        <div class="row align-items-center">
                            <!-- Queue Info -->
                            <div class="col-md-6">
                                <div class="d-flex align-items-center mb-2">
                                    <h6 class="mb-0 me-3">
                                        <a href="<?= site_url('Queue/queue_detail/' . $alert->queue_id) ?>" 
                                           class="text-primary text-decoration-none">
                                            #<?= $alert->queue_id ?>
                                        </a>
                                    </h6>
                                    <span class="alert-priority-badge <?= $alert->priority ?>">
                                        <?= $alert->priority_label ?>
                                    </span>
                                    <span class="days-badge ms-2">
                                        <?= $alert->days_old ?> วัน
                                    </span>
                                </div>
                                <p class="mb-1 fw-bold"><?= htmlspecialchars($alert->queue_topic) ?></p>
                                <small class="text-muted">
                                    <?= htmlspecialchars(mb_substr($alert->queue_detail, 0, 80)) ?>
                                    <?= mb_strlen($alert->queue_detail) > 80 ? '...' : '' ?>
                                </small>
                            </div>

                            <!-- Status & User Info -->
                            <div class="col-md-3">
                                <div class="mb-2">
                                    <!-- *** แก้ไข: ใช้ property ที่เตรียมไว้แล้วใน Controller *** -->
                                    <span class="status-badge <?= $alert->status_class ?>">
                                        <?= $alert->queue_status ?>
                                    </span>
                                </div>
                                <small class="text-muted d-block">
                                    <i class="fas fa-user me-1"></i><?= htmlspecialchars($alert->queue_by) ?>
                                </small>
                                <small class="text-muted d-block">
                                    <i class="fas fa-phone me-1"></i><?= $alert->queue_phone ?>
                                </small>
                            </div>

                            <!-- Dates -->
                            <div class="col-md-2">
                                <small class="text-muted d-block">
                                    <strong>สร้างเมื่อ:</strong><br>
                                    <?= date('d/m/Y H:i', strtotime($alert->queue_datesave)) ?>
                                </small>
                                <?php if ($alert->queue_dateupdate): ?>
                                    <small class="text-muted d-block mt-1">
                                        <strong>อัพเดท:</strong><br>
                                        <?= date('d/m/Y H:i', strtotime($alert->queue_dateupdate)) ?>
                                    </small>
                                <?php endif; ?>
                            </div>

                            <!-- Actions -->
                            <div class="col-md-1 text-end">
                                <a href="<?= site_url('Queue/queue_detail/' . $alert->queue_id) ?>" 
                                   class="btn btn-sm btn-primary" title="ดูรายละเอียด">
                                    <i class="fas fa-eye"></i>
                                </a>
                            </div>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>

            <!-- Pagination -->
            <?php if ($total_rows > 0): ?>
                <div class="pagination-wrapper">
                    <div class="pagination-container">
                        <div class="pagination-info">
                            แสดง <span class="highlight"><?= number_format(($current_page - 1) * $per_page + 1) ?></span> - 
                            <span class="highlight"><?= number_format(min($current_page * $per_page, $total_rows)) ?></span> 
                            จาก <span class="highlight"><?= number_format($total_rows) ?></span> รายการ
                        </div>
                        <div class="pagination-controls">
                            <nav aria-label="Queue alerts pagination">
                                <?= $pagination ?>
                            </nav>
                        </div>
                    </div>
                </div>
            <?php endif; ?>
        <?php endif; ?>
    </div>
</div>

<script>
// Auto refresh every 5 minutes
setInterval(function() {
    if (confirm('ต้องการรีเฟรชข้อมูลหรือไม่?')) {
        location.reload();
    }
}, 300000); // 5 minutes
</script>