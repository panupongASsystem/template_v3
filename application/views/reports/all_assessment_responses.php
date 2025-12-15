<!-- All Assessment Responses View - สมบูรณ์แบบ สอดคล้องกับ assessment_admin.php -->
<div class="assessment-admin-container">
    <!-- Header Spacing -->
    <div style="height: 60px;"></div>

    <!-- Flash Messages -->
    <?php if ($this->session->flashdata('error_message')): ?>
        <div class="alert alert-danger alert-dismissible fade show" role="alert">
            <i class="fas fa-exclamation-triangle me-2"></i>
            <?= $this->session->flashdata('error_message') ?>
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    <?php endif; ?>

    <?php if ($this->session->flashdata('success_message')): ?>
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            <i class="fas fa-check-circle me-2"></i>
            <?= $this->session->flashdata('success_message') ?>
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    <?php endif; ?>

    <div class="row mb-4">
        <div class="col-12">
            <div class="card shadow-elegant">
                <div class="card-header gradient-header">
                    <div class="d-flex justify-content-between align-items-center">
                        <h5 class="mb-0 header-title">
                            <i class="fas fa-list me-2"></i>
                            รายการคำตอบแบบประเมินทั้งหมด
                        </h5>
                        <div class="btn-group" role="group">
                            <a href="<?= site_url('System_reports/assessment_admin') ?>"
                                class="btn btn-soft-light btn-sm elegant-btn">
                                <i class="fas fa-arrow-left me-1"></i>
                                กลับหน้ารายงาน
                            </a>
                            <button type="button" class="btn btn-soft-info btn-sm elegant-btn"
                                onclick="exportAllResponses()">
                                <i class="fas fa-download me-1"></i>
                                ส่งออกข้อมูล
                            </button>
                            <button type="button" class="btn btn-soft-success btn-sm elegant-btn" id="autoRefreshBtn"
                                onclick="toggleAutoRefresh()">
                                <i class="fas fa-play me-1"></i>
                                รีเฟรชอัตโนมัติ
                            </button>
                        </div>
                    </div>
                </div>

                <!-- Filter Section -->
                <div class="card-body">
                    <div class="row mb-4">
                        <div class="col-12">
                            <div class="card elegant-filter-card">
                                <div class="card-header elegant-filter-header">
                                    <h6 class="mb-0">
                                        <i class="fas fa-filter me-2"></i>
                                        ตัวกรองข้อมูล
                                        <?php if (!empty($filter_date_from) || !empty($filter_date_to) || !empty($filter_score)): ?>
                                            <span class="badge bg-primary ms-2">ใช้ตัวกรอง</span>
                                        <?php endif; ?>
                                    </h6>
                                </div>
                                <div class="card-body">
                                    <form method="GET"
                                        action="<?= site_url('System_reports/all_assessment_responses') ?>"
                                        class="filter-form">
                                        <div class="row">
                                            <div class="col-md-3">
                                                <label class="form-label">
                                                    <i class="fas fa-calendar-alt me-1"></i>
                                                    วันที่เริ่มต้น
                                                </label>
                                                <input type="date" class="form-control" name="date_from"
                                                    value="<?= htmlspecialchars($filter_date_from ?? '') ?>">
                                            </div>
                                            <div class="col-md-3">
                                                <label class="form-label">
                                                    <i class="fas fa-calendar-check me-1"></i>
                                                    วันที่สิ้นสุด
                                                </label>
                                                <input type="date" class="form-control" name="date_to"
                                                    value="<?= htmlspecialchars($filter_date_to ?? '') ?>">
                                            </div>
                                            <div class="col-md-3">
                                                <label class="form-label">
                                                    <i class="fas fa-star me-1"></i>
                                                    คะแนน
                                                </label>
                                                <select class="form-select" name="score">
                                                    <?php foreach ($score_options as $value => $label): ?>
                                                        <option value="<?= $value ?>" <?= ($filter_score == $value) ? 'selected' : '' ?>>
                                                            <?= $label ?>
                                                        </option>
                                                    <?php endforeach; ?>
                                                </select>
                                            </div>
                                            <div class="col-md-3 d-flex align-items-end">
                                                <button type="submit" class="btn btn-soft-primary me-2 elegant-btn">
                                                    <i class="fas fa-search me-1"></i>
                                                    กรอง
                                                </button>
                                                <a href="<?= site_url('System_reports/all_assessment_responses') ?>"
                                                    class="btn btn-soft-secondary elegant-btn">
                                                    <i class="fas fa-times me-1"></i>
                                                    ล้าง
                                                </a>
                                            </div>
                                        </div>
                                    </form>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Statistics Summary -->
                    <div class="row mb-4">
                        <div class="col-md-3">
                            <div class="stat-card soft-primary">
                                <div class="d-flex align-items-center">
                                    <div class="stat-icon primary-icon me-3">
                                        <i class="fas fa-users"></i>
                                    </div>
                                    <div class="flex-grow-1">
                                        <div class="h4 mb-0 primary-text"><?= number_format($total_responses) ?></div>
                                        <small class="text-muted">รายการทั้งหมด</small>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="stat-card soft-success">
                                <div class="d-flex align-items-center">
                                    <div class="stat-icon success-icon me-3">
                                        <i class="fas fa-star"></i>
                                    </div>
                                    <div class="flex-grow-1">
                                        <div class="h4 mb-0 success-text">
                                            <?= number_format($statistics['average_score'] ?? 0, 2) ?>/5
                                        </div>
                                        <small class="text-muted">คะแนนเฉลี่ย</small>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="stat-card soft-info">
                                <div class="d-flex align-items-center">
                                    <div class="stat-icon info-icon me-3">
                                        <i class="fas fa-file-alt"></i>
                                    </div>
                                    <div class="flex-grow-1">
                                        <div class="h4 mb-0 info-text"><?= $current_page ?>/<?= $total_pages ?></div>
                                        <small class="text-muted">หน้าปัจจุบัน</small>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="stat-card soft-warning">
                                <div class="d-flex align-items-center">
                                    <div class="stat-icon warning-icon me-3">
                                        <i class="fas fa-list"></i>
                                    </div>
                                    <div class="flex-grow-1">
                                        <div class="h4 mb-0 warning-text"><?= $limit ?></div>
                                        <small class="text-muted">รายการต่อหน้า</small>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Quick Search -->
                    <div class="row mb-4">
                        <div class="col-12">
                            <div class="input-group">
                                <span class="input-group-text">
                                    <i class="fas fa-search"></i>
                                </span>
                                <input type="text" class="form-control" id="quickSearch"
                                    placeholder="ค้นหาใน IP Address, คำตอบ, หรือข้อเสนอแนะ..."
                                    data-target=".elegant-response-item">
                                <button class="btn btn-outline-secondary" type="button" onclick="clearSearch()">
                                    <i class="fas fa-times"></i>
                                </button>
                            </div>
                            <small class="search-results text-muted mt-2"></small>
                        </div>
                    </div>

                    <!-- Responses List -->
                    <?php if ($has_data && !empty($responses)): ?>
                        <div class="row">
                            <div class="col-12">
                                <div class="card elegant-responses-card">
                                    <div class="card-header elegant-responses-header">
                                        <div class="d-flex justify-content-between align-items-center">
                                            <h6 class="mb-0">
                                                <i class="fas fa-clipboard-list me-2"></i>
                                                รายการคำตอบ (<?= number_format(($current_page - 1) * $limit + 1) ?> -
                                                <?= number_format(min($current_page * $limit, $total_responses)) ?> จาก
                                                <?= number_format($total_responses) ?> รายการ)
                                            </h6>
                                            <small class="text-muted last-refresh-time">
                                                อัปเดตล่าสุด: <?= date('H:i:s') ?>
                                            </small>
                                        </div>
                                    </div>
                                    <div class="card-body">
                                        <?php foreach ($responses as $index => $response): ?>
                                            <div class="response-item elegant-response-item mb-4 searchable-item"
                                                data-response-id="<?= $response->id ?>">
                                                <div
                                                    class="response-header d-flex justify-content-between align-items-start mb-3">
                                                    <div class="flex-grow-1">
                                                        <h6 class="response-title">
                                                            <i class="fas fa-user me-2"></i>
                                                            ผู้ตอบ #<?= $response->id ?>
                                                            <span class="badge badge-outline ms-2">
                                                                อันดับ <?= ($current_page - 1) * $limit + $index + 1 ?>
                                                            </span>
                                                        </h6>
                                                        <small class="response-meta text-muted">
                                                            <i class="fas fa-calendar me-1"></i>
                                                            <?= date('d/m/Y H:i:s', strtotime($response->completed_at)) ?>
                                                            <span class="ms-3">
                                                                <i class="fas fa-network-wired me-1"></i>
                                                                IP: <code><?= $response->ip_address ?></code>
                                                            </span>
                                                            <?php if (!empty($response->session_id)): ?>
                                                                <span class="ms-3">
                                                                    <i class="fas fa-fingerprint me-1"></i>
                                                                    Session: <code
                                                                        class="small"><?= substr($response->session_id, 0, 8) ?>...</code>
                                                                </span>
                                                            <?php endif; ?>
                                                        </small>
                                                    </div>
                                                    <div class="text-end">
                                                        <div class="mb-2">
                                                            <span class="badge elegant-score-badge">
                                                                <i class="fas fa-star me-1"></i>
                                                                คะแนนเฉลี่ย: <?= number_format($response->average_score, 2) ?>/5
                                                            </span>
                                                        </div>
                                                        <div class="btn-group btn-group-sm">
                                                            <button type="button" class="btn btn-outline-primary btn-sm"
                                                                onclick="printResponse(<?= $response->id ?>)"
                                                                title="พิมพ์รายละเอียด">
                                                                <i class="fas fa-print"></i>
                                                            </button>
                                                            <button type="button" class="btn btn-outline-secondary btn-sm"
                                                                onclick="toggleResponseDetails(<?= $response->id ?>)"
                                                                title="ขยายรายละเอียด">
                                                                <i class="fas fa-expand-alt"
                                                                    id="toggle-icon-<?= $response->id ?>"></i>
                                                            </button>
                                                        </div>
                                                    </div>
                                                </div>

                                                <div class="response-details collapsed" id="details-<?= $response->id ?>">
                                                    <?php if (!empty($response->details)): ?>
                                                        <?php
                                                        $current_category = '';
                                                        $question_count = 0;
                                                        foreach ($response->details as $detail):
                                                            if ($current_category != $detail->category_name):
                                                                if ($current_category != '')
                                                                    echo '</div>'; // ปิด category ก่อนหน้า
                                                                $current_category = $detail->category_name;
                                                                $question_count = 0;
                                                                ?>
                                                                <div class="category-section mb-3">
                                                                    <div class="category-header">
                                                                        <h6 class="category-title">
                                                                            <i class="fas fa-folder me-2"></i>
                                                                            <?= htmlspecialchars($detail->category_name) ?>
                                                                            <?php if ($detail->is_scoring): ?>
                                                                                <span class="badge bg-success ms-2">ประเมินคะแนน</span>
                                                                            <?php else: ?>
                                                                                <span class="badge bg-info ms-2">ข้อมูลทั่วไป</span>
                                                                            <?php endif; ?>
                                                                        </h6>
                                                                    </div>
                                                                <?php endif; ?>

                                                                <div class="question-item">
                                                                    <div class="question-text">
                                                                        <strong><?= $detail->question_order ?>.
                                                                            <?= htmlspecialchars($detail->question_text) ?></strong>
                                                                        <span class="question-type-badge">
                                                                            <?php if ($detail->question_type === 'radio'): ?>
                                                                                <i class="fas fa-dot-circle text-primary"
                                                                                    title="คำถามแบบเลือก"></i>
                                                                            <?php elseif ($detail->question_type === 'textarea'): ?>
                                                                                <i class="fas fa-comment-alt text-info"
                                                                                    title="คำถามแบบข้อความ"></i>
                                                                            <?php else: ?>
                                                                                <i class="fas fa-check-square text-warning"
                                                                                    title="คำถามแบบเลือกหลายข้อ"></i>
                                                                            <?php endif; ?>
                                                                        </span>
                                                                    </div>
                                                                    <div class="answer-text">
                                                                        <?php if ($detail->question_type === 'radio' && !empty($detail->answer_value)): ?>
                                                                            <?php
                                                                            // ตรวจสอบว่าเป็นคะแนน 1-5 หรือไม่
                                                                            $is_numeric_score = is_numeric($detail->answer_value) &&
                                                                                intval($detail->answer_value) >= 1 &&
                                                                                intval($detail->answer_value) <= 5 &&
                                                                                intval($detail->answer_value) == $detail->answer_value; // ตรวจสอบว่าเป็นจำนวนเต็ม
                                                                            ?>

                                                                            <?php if ($is_numeric_score): ?>
                                                                                <!-- แสดงแบบคะแนน 1-5 -->
                                                                                <span
                                                                                    class="badge answer-score-badge score-<?= $detail->answer_value ?>">
                                                                                    <i class="fas fa-star me-1"></i>
                                                                                    <?= $detail->answer_value ?>
                                                                                    <?php
                                                                                    $score_labels = [
                                                                                        '1' => 'ต้องปรับปรุงมาก',
                                                                                        '2' => 'ต้องปรับปรุง',
                                                                                        '3' => 'ปานกลาง',
                                                                                        '4' => 'ดี',
                                                                                        '5' => 'ดีมาก'
                                                                                    ];
                                                                                    echo '(' . $score_labels[$detail->answer_value] . ')';
                                                                                    ?>
                                                                                </span>
                                                                            <?php else: ?>
                                                                                <!-- แสดงแบบข้อความทั่วไป -->
                                                                                <span class="badge answer-score-badge badge-secondary">
                                                                                    <i class="fas fa-check-circle me-1"></i>
                                                                                    <?= htmlspecialchars($detail->answer_value) ?>
                                                                                </span>
                                                                            <?php endif; ?>

                                                                        <?php elseif (!empty($detail->answer_text)): ?>
                                                                            <div class="text-answer">
                                                                                <i class="fas fa-quote-left text-muted me-2"></i>
                                                                                <?= nl2br(htmlspecialchars($detail->answer_text)) ?>
                                                                                <i class="fas fa-quote-right text-muted ms-2"></i>
                                                                            </div>
                                                                        <?php else: ?>
                                                                            <span class="text-muted fst-italic">ไม่มีคำตอบ</span>
                                                                        <?php endif; ?>
                                                                    </div>
                                                                </div>
                                                            <?php endforeach; ?>
                                                            <?php if ($current_category != '')
                                                                echo '</div>'; // ปิด category สุดท้าย ?>
                                                        <?php else: ?>
                                                            <div class="text-center text-muted py-3">
                                                                <i class="fas fa-exclamation-circle me-2"></i>
                                                                ไม่พบรายละเอียดคำตอบ
                                                            </div>
                                                        <?php endif; ?>
                                                    </div>
                                                </div>
                                                <?php if ($index < count($responses) - 1): ?>
                                                    <hr class="elegant-divider">
                                                <?php endif; ?>
                                            <?php endforeach; ?>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- Pagination -->
                            <?php if ($total_pages > 1): ?>
                                <div class="row mt-4">
                                    <div class="col-12">
                                        <nav aria-label="Pagination Navigation">
                                            <ul class="pagination justify-content-center elegant-pagination">
                                                <!-- First Page -->
                                                <?php if ($current_page > 3): ?>
                                                    <li class="page-item">
                                                        <a class="page-link"
                                                            href="<?= site_url('System_reports/all_assessment_responses') ?>?page=1<?= !empty($filter_date_from) ? '&date_from=' . $filter_date_from : '' ?><?= !empty($filter_date_to) ? '&date_to=' . $filter_date_to : '' ?><?= !empty($filter_score) ? '&score=' . $filter_score : '' ?>"
                                                            title="หน้าแรก">
                                                            <i class="fas fa-angle-double-left"></i>
                                                        </a>
                                                    </li>
                                                <?php endif; ?>

                                                <!-- Previous Page -->
                                                <?php if ($current_page > 1): ?>
                                                    <li class="page-item">
                                                        <a class="page-link"
                                                            href="<?= site_url('System_reports/all_assessment_responses') ?>?page=<?= $current_page - 1 ?><?= !empty($filter_date_from) ? '&date_from=' . $filter_date_from : '' ?><?= !empty($filter_date_to) ? '&date_to=' . $filter_date_to : '' ?><?= !empty($filter_score) ? '&score=' . $filter_score : '' ?>"
                                                            title="หน้าก่อนหน้า">
                                                            <i class="fas fa-chevron-left"></i>
                                                        </a>
                                                    </li>
                                                <?php endif; ?>

                                                <!-- Page Numbers -->
                                                <?php
                                                $start_page = max(1, $current_page - 2);
                                                $end_page = min($total_pages, $current_page + 2);

                                                for ($i = $start_page; $i <= $end_page; $i++):
                                                    ?>
                                                    <li class="page-item <?= ($i == $current_page) ? 'active' : '' ?>">
                                                        <a class="page-link"
                                                            href="<?= site_url('System_reports/all_assessment_responses') ?>?page=<?= $i ?><?= !empty($filter_date_from) ? '&date_from=' . $filter_date_from : '' ?><?= !empty($filter_date_to) ? '&date_to=' . $filter_date_to : '' ?><?= !empty($filter_score) ? '&score=' . $filter_score : '' ?>">
                                                            <?= $i ?>
                                                        </a>
                                                    </li>
                                                <?php endfor; ?>

                                                <!-- Next Page -->
                                                <?php if ($current_page < $total_pages): ?>
                                                    <li class="page-item">
                                                        <a class="page-link"
                                                            href="<?= site_url('System_reports/all_assessment_responses') ?>?page=<?= $current_page + 1 ?><?= !empty($filter_date_from) ? '&date_from=' . $filter_date_from : '' ?><?= !empty($filter_date_to) ? '&date_to=' . $filter_date_to : '' ?><?= !empty($filter_score) ? '&score=' . $filter_score : '' ?>"
                                                            title="หน้าถัดไป">
                                                            <i class="fas fa-chevron-right"></i>
                                                        </a>
                                                    </li>
                                                <?php endif; ?>

                                                <!-- Last Page -->
                                                <?php if ($current_page < $total_pages - 2): ?>
                                                    <li class="page-item">
                                                        <a class="page-link"
                                                            href="<?= site_url('System_reports/all_assessment_responses') ?>?page=<?= $total_pages ?><?= !empty($filter_date_from) ? '&date_from=' . $filter_date_from : '' ?><?= !empty($filter_date_to) ? '&date_to=' . $filter_date_to : '' ?><?= !empty($filter_score) ? '&score=' . $filter_score : '' ?>"
                                                            title="หน้าสุดท้าย">
                                                            <i class="fas fa-angle-double-right"></i>
                                                        </a>
                                                    </li>
                                                <?php endif; ?>
                                            </ul>
                                        </nav>

                                        <!-- Pagination Info -->
                                        <div class="text-center mt-3">
                                            <small class="text-muted">
                                                แสดง <?= number_format(($current_page - 1) * $limit + 1) ?> -
                                                <?= number_format(min($current_page * $limit, $total_responses)) ?> จาก
                                                <?= number_format($total_responses) ?> รายการ
                                                <?php if (!empty($filter_date_from) || !empty($filter_date_to) || !empty($filter_score)): ?>
                                                    <span class="ms-2 text-primary">(ข้อมูลที่กรองแล้ว)</span>
                                                <?php endif; ?>
                                            </small>
                                        </div>
                                    </div>
                                </div>
                            <?php endif; ?>

                        <?php else: ?>
                            <!-- Empty State -->
                            <div class="row">
                                <div class="col-12">
                                    <div class="text-center py-5 empty-state">
                                        <div class="empty-icon mb-4">
                                            <i class="fas fa-inbox fa-4x text-muted"></i>
                                        </div>
                                        <h4 class="text-muted">ไม่พบข้อมูลคำตอบแบบประเมิน</h4>
                                        <p class="text-muted mb-4">
                                            <?php if (!empty($filter_date_from) || !empty($filter_date_to) || !empty($filter_score)): ?>
                                                ไม่พบข้อมูลที่ตรงกับเงื่อนไขการค้นหา กรุณาลองปรับเปลี่ยนตัวกรอง
                                            <?php else: ?>
                                                ยังไม่มีผู้ตอบแบบประเมินความพึงพอใจ เมื่อมีผู้ตอบแล้วข้อมูลจะแสดงที่นี่
                                            <?php endif; ?>
                                        </p>
                                        <div class="empty-actions">
                                            <a href="<?= site_url('assessment') ?>" target="_blank"
                                                class="btn btn-soft-primary elegant-btn">
                                                <i class="fas fa-clipboard-check me-1"></i>
                                                ไปยังแบบประเมิน
                                            </a>
                                            <?php if (!empty($filter_date_from) || !empty($filter_date_to) || !empty($filter_score)): ?>
                                                <a href="<?= site_url('System_reports/all_assessment_responses') ?>"
                                                    class="btn btn-soft-secondary elegant-btn ms-2">
                                                    <i class="fas fa-times me-1"></i>
                                                    ล้างตัวกรอง
                                                </a>
                                            <?php endif; ?>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- CSS Styles - สมบูรณ์แบบ สอดคล้องกับ assessment_admin.php -->
    <style>
        /* Main Container */
        .assessment-admin-container {
            min-height: 100vh;
            padding: 2px;
        }

        /* Elegant Card Shadows */
        .shadow-elegant {
            box-shadow:
                0 4px 20px rgba(0, 0, 0, 0.08),
                0 2px 6px rgba(0, 0, 0, 0.04),
                inset 0 1px 0 rgba(255, 255, 255, 0.9);
            border: 1px solid rgba(255, 255, 255, 0.2);
            border-radius: 16px;
            backdrop-filter: blur(10px);
        }

        /* Gradient Headers */
        .gradient-header {
            background: linear-gradient(135deg,
                    rgba(99, 102, 241, 0.1) 0%,
                    rgba(168, 85, 247, 0.1) 50%,
                    rgba(236, 72, 153, 0.1) 100%);
            border-bottom: 1px solid rgba(99, 102, 241, 0.1);
            border-radius: 16px 16px 0 0;
            backdrop-filter: blur(20px);
        }

        .header-title {
            color: #1e293b;
            font-weight: 600;
            text-shadow: 0 1px 3px rgba(0, 0, 0, 0.1);
        }

        /* Stat Cards */
        .stat-card {
            background: linear-gradient(135deg, rgba(255, 255, 255, 0.95) 0%, rgba(248, 250, 252, 0.95) 100%);
            border-radius: 12px;
            padding: 20px;
            border: 1px solid rgba(255, 255, 255, 0.3);
            box-shadow:
                0 3px 12px rgba(0, 0, 0, 0.06),
                inset 0 1px 0 rgba(255, 255, 255, 0.8);
            transition: all 0.3s ease;
            backdrop-filter: blur(10px);
        }

        .stat-card:hover {
            transform: translateY(-3px);
            box-shadow:
                0 8px 25px rgba(0, 0, 0, 0.12),
                inset 0 1px 0 rgba(255, 255, 255, 0.9);
        }

        /* Soft Backgrounds */
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

        /* Stat Icons */
        .stat-icon {
            width: 50px;
            height: 50px;
            border-radius: 12px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 20px;
            box-shadow: 0 3px 12px rgba(0, 0, 0, 0.1);
            backdrop-filter: blur(10px);
        }

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

        /* Text Colors */
        .success-text {
            color: #059669 !important;
            font-weight: 700;
            text-shadow: 0 1px 2px rgba(0, 0, 0, 0.1);
        }

        .primary-text {
            color: #2563eb !important;
            font-weight: 700;
            text-shadow: 0 1px 2px rgba(0, 0, 0, 0.1);
        }

        .warning-text {
            color: #d97706 !important;
            font-weight: 700;
            text-shadow: 0 1px 2px rgba(0, 0, 0, 0.1);
        }

        .info-text {
            color: #0284c7 !important;
            font-weight: 700;
            text-shadow: 0 1px 2px rgba(0, 0, 0, 0.1);
        }

        /* Elegant Cards */
        .elegant-filter-card,
        .elegant-responses-card {
            background: linear-gradient(135deg, rgba(255, 255, 255, 0.95) 0%, rgba(249, 250, 251, 0.95) 100%);
            border: 1px solid rgba(255, 255, 255, 0.2);
            border-radius: 12px;
            box-shadow:
                0 3px 12px rgba(0, 0, 0, 0.06),
                inset 0 1px 0 rgba(255, 255, 255, 0.8);
            backdrop-filter: blur(10px);
        }

        .elegant-filter-header,
        .elegant-responses-header {
            background: linear-gradient(135deg,
                    rgba(124, 58, 237, 0.08) 0%,
                    rgba(147, 51, 234, 0.08) 100%);
            border-bottom: 1px solid rgba(124, 58, 237, 0.1);
            border-radius: 12px 12px 0 0;
            padding: 15px 20px;
        }

        /* Response Items */
        .elegant-response-item {
            background: linear-gradient(135deg, rgba(255, 255, 255, 0.9) 0%, rgba(248, 250, 252, 0.9) 100%);
            border: 1px solid rgba(99, 102, 241, 0.1);
            border-radius: 12px;
            padding: 20px;
            backdrop-filter: blur(5px);
            transition: all 0.3s ease;
        }

        .elegant-response-item:hover {
            border-color: rgba(99, 102, 241, 0.3);
            box-shadow: 0 4px 16px rgba(0, 0, 0, 0.08);
            transform: translateY(-2px);
        }

        .response-title {
            color: #1e293b;
            font-weight: 600;
            margin-bottom: 8px;
        }

        .response-meta {
            font-size: 0.875rem;
            line-height: 1.4;
        }

        .elegant-score-badge {
            background: linear-gradient(135deg, rgba(59, 130, 246, 0.15) 0%, rgba(99, 102, 241, 0.15) 100%);
            color: #2563eb;
            border: 1px solid rgba(59, 130, 246, 0.3);
            font-weight: 600;
            padding: 6px 12px;
            border-radius: 8px;
        }

        .badge-outline {
            background: rgba(99, 102, 241, 0.1);
            color: #4338ca;
            border: 1px solid rgba(99, 102, 241, 0.2);
            font-size: 0.75rem;
        }

        /* Category and Question Styles */
        .category-section {
            border-left: 3px solid rgba(99, 102, 241, 0.3);
            padding-left: 15px;
            margin-bottom: 20px;
            background: rgba(248, 250, 252, 0.5);
            border-radius: 8px;
            padding: 15px;
        }

        /* เพิ่ม contrast สำหรับ category header background */
        .category-header {
            background: rgba(248, 250, 252, 0.8) !important;
            /* เพิ่มพื้นหลังอ่อน */
            padding: 12px 15px !important;
            border-radius: 8px !important;
            margin-bottom: 15px;
            border: 1px solid rgba(229, 231, 235, 0.5) !important;
        }

        .category-title {
            color: #1e293b !important;
            /* เปลี่ยนจากสีม่วงเป็นสีเทาเข้ม */
            font-weight: 600;
            margin-bottom: 0;
            text-shadow: 0 1px 2px rgba(0, 0, 0, 0.1);
            /* เพิ่ม shadow เพื่อความคมชัด */
        }

        /* หรือใช้การจัดสีแบบแยกตาม badge type */
        .category-section .badge.bg-info+.category-title,
        .category-section .badge.bg-info~* .category-title {
            color: #0c63e4 !important;
            /* สีฟ้าเข้มสำหรับข้อมูลทั่วไป */
        }

        .category-section .badge.bg-success+.category-title,
        .category-section .badge.bg-success~* .category-title {
            color: #0d8043 !important;
            /* สีเขียวเข้มสำหรับการประเมิน */
        }


        /* Alternative Fix: ใช้สีตามประเภท badge */
        .category-section:has(.badge.bg-info) .category-title {
            color: #0284c7 !important;
            /* สีฟ้าสำหรับข้อมูลทั่วไป */
        }

        .category-section:has(.badge.bg-success) .category-title {
            color: #059669 !important;
            /* สีเขียวสำหรับการประเมิน */
        }

        /* Fallback สำหรับ browser ที่ไม่รองรับ :has() */
        @supports not selector(:has(*)) {
            .category-title {
                color: #1e293b !important;
                /* ใช้สีเทาเข้มเป็นค่าเริ่มต้น */
            }
        }

        .question-item {
            margin-bottom: 12px;
            padding: 10px;
            background: rgba(255, 255, 255, 0.7);
            border-radius: 8px;
            border: 1px solid rgba(229, 231, 235, 0.5);
        }

        .question-text {
            margin-bottom: 8px;
            color: #374151;
            font-size: 0.95rem;
            display: flex;
            justify-content: between;
            align-items: center;
        }

        .question-type-badge {
            margin-left: auto;
        }

        .answer-text {
            margin-left: 15px;
        }

        .answer-score-badge {
            font-weight: 600;
            padding: 4px 10px;
            border-radius: 6px;
            display: inline-flex;
            align-items: center;
        }

        /* Default badge สำหรับข้อความที่ไม่ใช่คะแนน 1-5 */
        .answer-score-badge:not(.score-1):not(.score-2):not(.score-3):not(.score-4):not(.score-5) {
            background-color: rgba(108, 117, 125, 0.15) !important;
            color: #495057 !important;
            border: 1px solid rgba(108, 117, 125, 0.3) !important;
        }

        .score-5 {
            background-color: rgba(34, 197, 94, 0.15);
            color: #059669;
            border: 1px solid rgba(34, 197, 94, 0.3);
        }

        .score-4 {
            background-color: rgba(132, 204, 22, 0.15);
            color: #65a30d;
            border: 1px solid rgba(132, 204, 22, 0.3);
        }

        .score-3 {
            background-color: rgba(245, 158, 11, 0.15);
            color: #d97706;
            border: 1px solid rgba(245, 158, 11, 0.3);
        }

        .score-2 {
            background-color: rgba(251, 146, 60, 0.15);
            color: #ea580c;
            border: 1px solid rgba(251, 146, 60, 0.3);
        }

        .score-1 {
            background-color: rgba(239, 68, 68, 0.15);
            color: #dc2626;
            border: 1px solid rgba(239, 68, 68, 0.3);
        }

        .text-answer {
            background: rgba(248, 250, 252, 0.8);
            padding: 12px;
            border-radius: 8px;
            border-left: 3px solid rgba(99, 102, 241, 0.3);
            font-style: italic;
            color: #374151;
            line-height: 1.6;
        }

        /* Soft Buttons */
        .btn-soft-light {
            background: linear-gradient(135deg, rgba(255, 255, 255, 0.9) 0%, rgba(248, 250, 252, 0.9) 100%);
            border: 1px solid rgba(203, 213, 225, 0.3);
            color: #475569;
            box-shadow: 0 2px 8px rgba(0, 0, 0, 0.06);
        }

        .btn-soft-primary {
            background: linear-gradient(135deg, rgba(59, 130, 246, 0.1) 0%, rgba(99, 102, 241, 0.1) 100%);
            border: 1px solid rgba(59, 130, 246, 0.2);
            color: #2563eb;
            box-shadow: 0 2px 8px rgba(59, 130, 246, 0.1);
        }

        .btn-soft-info {
            background: linear-gradient(135deg, rgba(14, 165, 233, 0.1) 0%, rgba(56, 189, 248, 0.1) 100%);
            border: 1px solid rgba(14, 165, 233, 0.2);
            color: #0284c7;
            box-shadow: 0 2px 8px rgba(14, 165, 233, 0.1);
        }

        .btn-soft-success {
            background: linear-gradient(135deg, rgba(34, 197, 94, 0.1) 0%, rgba(74, 222, 128, 0.1) 100%);
            border: 1px solid rgba(34, 197, 94, 0.2);
            color: #059669;
            box-shadow: 0 2px 8px rgba(34, 197, 94, 0.1);
        }

        .btn-soft-secondary {
            background: linear-gradient(135deg, rgba(107, 114, 128, 0.1) 0%, rgba(156, 163, 175, 0.1) 100%);
            border: 1px solid rgba(107, 114, 128, 0.2);
            color: #6b7280;
            box-shadow: 0 2px 8px rgba(107, 114, 128, 0.1);
        }

        .elegant-btn {
            border-radius: 8px;
            font-weight: 500;
            padding: 8px 16px;
            transition: all 0.3s ease;
        }

        .elegant-btn:hover {
            transform: translateY(-1px);
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.15);
        }

        /* Pagination */
        .elegant-pagination .page-link {
            background: linear-gradient(135deg, rgba(255, 255, 255, 0.9) 0%, rgba(248, 250, 252, 0.9) 100%);
            border: 1px solid rgba(203, 213, 225, 0.3);
            color: #475569;
            margin: 0 2px;
            border-radius: 8px;
            font-weight: 500;
            transition: all 0.3s ease;
            padding: 8px 12px;
        }

        .elegant-pagination .page-link:hover {
            background: linear-gradient(135deg, rgba(59, 130, 246, 0.1) 0%, rgba(99, 102, 241, 0.1) 100%);
            border-color: rgba(59, 130, 246, 0.3);
            color: #2563eb;
            transform: translateY(-1px);
        }

        .elegant-pagination .page-item.active .page-link {
            background: linear-gradient(135deg, #3b82f6 0%, #6366f1 100%);
            border-color: #3b82f6;
            color: white;
            box-shadow: 0 3px 12px rgba(59, 130, 246, 0.3);
        }

        /* Divider */
        .elegant-divider {
            border: none;
            height: 1px;
            background: linear-gradient(to right, transparent, rgba(99, 102, 241, 0.2), transparent);
            margin: 20px 0;
        }

        /* Empty State */
        .empty-state {
            opacity: 0.8;
            padding: 40px 20px;
        }

        .empty-icon {
            animation: float 3s ease-in-out infinite;
        }

        @keyframes float {

            0%,
            100% {
                transform: translateY(0px);
            }

            50% {
                transform: translateY(-10px);
            }
        }

        .empty-actions .btn {
            margin: 5px;
        }

        /* Animation */
        .elegant-response-item,
        .stat-card,
        .elegant-filter-card,
        .elegant-responses-card {
            animation: fadeInUp 0.6s ease-out;
        }

        @keyframes fadeInUp {
            from {
                opacity: 0;
                transform: translateY(20px);
            }

            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        /* Form Controls */
        .form-control,
        .form-select {
            border: 1px solid rgba(203, 213, 225, 0.4);
            border-radius: 8px;
            backdrop-filter: blur(5px);
            transition: all 0.3s ease;
        }

        .form-control:focus,
        .form-select:focus {
            border-color: rgba(59, 130, 246, 0.5);
            box-shadow: 0 0 0 3px rgba(59, 130, 246, 0.1);
        }

        .form-label {
            font-weight: 500;
            color: #374151;
            margin-bottom: 6px;
        }

        /* Input Group */
        .input-group-text {
            background: rgba(248, 250, 252, 0.8);
            border-color: rgba(203, 213, 225, 0.4);
        }

        /* Code Style */
        code {
            background: rgba(99, 102, 241, 0.1);
            color: #4338ca;
            padding: 2px 6px;
            border-radius: 4px;
            font-size: 0.875em;
        }

        /* Badges */
        .badge.bg-success {
            background-color: rgba(34, 197, 94, 0.9) !important;
        }

        .badge.bg-info {
            background-color: rgba(14, 165, 233, 0.9) !important;
        }

        .badge.bg-primary {
            background-color: rgba(59, 130, 246, 0.9) !important;
        }

        /* Alert Styles */
        .alert {
            border: none;
            border-radius: 12px;
            backdrop-filter: blur(10px);
        }

        .alert-danger {
            background: linear-gradient(135deg, rgba(239, 68, 68, 0.1) 0%, rgba(248, 113, 113, 0.1) 100%);
            color: #dc2626;
            border-left: 4px solid #ef4444;
        }

        .alert-success {
            background: linear-gradient(135deg, rgba(34, 197, 94, 0.1) 0%, rgba(74, 222, 128, 0.1) 100%);
            color: #059669;
            border-left: 4px solid #22c55e;
        }

        /* Responsive Design */
        @media (max-width: 768px) {
            .assessment-admin-container {
                padding: 10px;
            }

            .stat-card {
                padding: 15px;
                margin-bottom: 15px;
                text-align: center;
            }

            .elegant-response-item {
                padding: 15px;
            }

            .response-header {
                flex-direction: column;
                align-items: start !important;
            }

            .elegant-score-badge {
                margin-top: 10px;
            }

            .btn-group {
                flex-direction: column;
                width: 100%;
            }

            .btn-group .btn {
                margin-bottom: 5px;
            }

            .elegant-pagination .page-link {
                padding: 6px 8px;
                font-size: 0.875rem;
            }

            .question-item {
                padding-left: 5px;
            }

            .answer-text {
                margin-left: 5px;
            }

            .category-section {
                padding: 10px;
            }

            .form-control,
            .form-select {
                margin-bottom: 10px;
            }

            .stat-icon {
                width: 40px;
                height: 40px;
                font-size: 16px;
            }

            .question-text {
                flex-direction: column;
                align-items: start;
            }

            .question-type-badge {
                margin-left: 0;
                margin-top: 5px;
            }
        }

        @media (max-width: 576px) {
            .h4 {
                font-size: 1.2rem;
            }

            .response-meta {
                font-size: 0.8rem;
            }

            .elegant-response-item {
                padding: 12px;
            }

            .question-text {
                font-size: 0.9rem;
            }

            .category-title {
                font-size: 1rem;
            }

            .btn-group-sm .btn {
                padding: 4px 8px;
                font-size: 0.8rem;
            }
        }

        /* Loading States */
        .loading {
            opacity: 0.7;
            pointer-events: none;
        }

        .loading::after {
            content: '';
            position: absolute;
            top: 50%;
            left: 50%;
            width: 20px;
            height: 20px;
            margin: -10px 0 0 -10px;
            border: 2px solid #f3f3f3;
            border-top: 2px solid #3498db;
            border-radius: 50%;
            animation: spin 1s linear infinite;
        }

        @keyframes spin {
            0% {
                transform: rotate(0deg);
            }

            100% {
                transform: rotate(360deg);
            }
        }

        /* Hide Details Initially */
        .response-details {
            overflow: hidden;
            transition: max-height 0.3s ease;
        }

        .response-details.collapsed {
            max-height: 0;
        }

        .response-details.expanded {
            max-height: 500px;
            /* หรือค่าที่เหมาะสม */
            overflow-y: auto;
        }
    </style>

    <!-- JavaScript Functions - สมบูรณ์แบบ -->
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            // Initialize tooltips
            if (typeof bootstrap !== 'undefined') {
                var tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'));
                var tooltipList = tooltipTriggerList.map(function (tooltipTriggerEl) {
                    return new bootstrap.Tooltip(tooltipTriggerEl);
                });
            }

            // เพิ่ม smooth scroll behavior
            document.documentElement.style.scrollBehavior = 'smooth';

            // Highlight filtered results
            const urlParams = new URLSearchParams(window.location.search);
            if (urlParams.get('date_from') || urlParams.get('date_to') || urlParams.get('score')) {
                showToast('แสดงผลลัพธ์ตามเงื่อนไขที่เลือก', 'success');
            }

            // Add form validation
            const form = document.querySelector('form[method="GET"]');
            if (form) {
                form.addEventListener('submit', function (e) {
                    if (!validateDateRange()) {
                        e.preventDefault();
                    }
                });
            }

            // Initialize search
            const searchInput = document.getElementById('quickSearch');
            if (searchInput) {
                searchInput.addEventListener('input', debounce(handleSearch, 300));
                updateSearchResults('', '.elegant-response-item');
            }
        });

        // Export Functions
        function exportAllResponses() {
            // สร้าง URL พร้อม filters ปัจจุบัน
            let params = new URLSearchParams();

            const dateFrom = document.querySelector('input[name="date_from"]')?.value;
            const dateTo = document.querySelector('input[name="date_to"]')?.value;
            const score = document.querySelector('select[name="score"]')?.value;

            if (dateFrom) params.append('date_from', dateFrom);
            if (dateTo) params.append('date_to', dateTo);
            if (score) params.append('score', score);

            const exportUrl = '<?= site_url("System_reports/export_assessment_report") ?>' +
                (params.toString() ? '?' + params.toString() : '');

            showToast('กำลังเตรียมข้อมูลสำหรับส่งออก...', 'info');

            // เปิดหน้าต่างใหม่สำหรับ download
            window.open(exportUrl, '_blank');
        }

        // Toast Notification Function
        function showToast(message, type = 'info') {
            const toastColors = {
                success: { bg: 'rgba(34, 197, 94, 0.1)', border: '#22c55e', text: '#059669' },
                info: { bg: 'rgba(59, 130, 246, 0.1)', border: '#3b82f6', text: '#2563eb' },
                warning: { bg: 'rgba(245, 158, 11, 0.1)', border: '#f59e0b', text: '#d97706' },
                error: { bg: 'rgba(239, 68, 68, 0.1)', border: '#ef4444', text: '#dc2626' }
            };

            const colors = toastColors[type] || toastColors.info;

            const toastHtml = `
        <div class="toast" role="alert" style="background: ${colors.bg}; border-left: 4px solid ${colors.border};">
            <div class="d-flex">
                <div class="toast-body" style="color: ${colors.text}; font-weight: 500;">
                    <i class="fas fa-${type === 'success' ? 'check-circle' : type === 'error' ? 'exclamation-triangle' : type === 'warning' ? 'exclamation-circle' : 'info-circle'} me-2"></i>
                    ${message}
                </div>
                <button type="button" class="btn-close btn-close-white me-2 m-auto" data-bs-dismiss="toast"></button>
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

            // Use Bootstrap Toast if available, otherwise use timeout
            if (typeof bootstrap !== 'undefined') {
                const toast = new bootstrap.Toast(toastElement, { delay: 4000 });
                toast.show();

                toastElement.addEventListener('hidden.bs.toast', () => {
                    toastElement.remove();
                });
            } else {
                // Fallback
                toastElement.style.display = 'block';
                setTimeout(() => {
                    toastElement.remove();
                }, 4000);
            }
        }

        // Form validation
        function validateDateRange() {
            const dateFrom = document.querySelector('input[name="date_from"]')?.value;
            const dateTo = document.querySelector('input[name="date_to"]')?.value;

            if (dateFrom && dateTo && dateFrom > dateTo) {
                showToast('วันที่เริ่มต้นต้องไม่เกินวันที่สิ้นสุด', 'warning');
                return false;
            }

            return true;
        }

        // Search Functions
        function handleSearch() {
            const searchTerm = document.getElementById('quickSearch').value.toLowerCase();
            const target = '.elegant-response-item';

            document.querySelectorAll(target).forEach(function (item) {
                const text = item.textContent.toLowerCase();
                if (text.includes(searchTerm)) {
                    item.style.display = 'block';
                } else {
                    item.style.display = 'none';
                }
            });

            updateSearchResults(searchTerm, target);
        }

        function updateSearchResults(searchTerm, target) {
            const total = document.querySelectorAll(target).length;
            const visible = document.querySelectorAll(target + ':not([style*="display: none"])').length;

            let resultsText = '';
            if (searchTerm) {
                resultsText = `แสดง ${visible} จาก ${total} รายการ`;
            } else {
                resultsText = `แสดงทั้งหมด ${total} รายการ`;
            }

            const resultsElement = document.querySelector('.search-results');
            if (resultsElement) {
                resultsElement.textContent = resultsText;
            }
        }

        function clearSearch() {
            const searchInput = document.getElementById('quickSearch');
            if (searchInput) {
                searchInput.value = '';
                handleSearch();
            }
        }

        // Response Details Toggle
        function toggleResponseDetails(responseId) {
            const details = document.getElementById(`details-${responseId}`);
            const icon = document.getElementById(`toggle-icon-${responseId}`);

            if (details && icon) {
                if (details.classList.contains('collapsed')) {
                    details.classList.remove('collapsed');
                    details.classList.add('expanded');
                    icon.className = 'fas fa-compress-alt';
                    icon.parentElement.title = 'ย่อรายละเอียด';
                } else {
                    details.classList.remove('expanded');
                    details.classList.add('collapsed');
                    icon.className = 'fas fa-expand-alt';
                    icon.parentElement.title = 'ขยายรายละเอียด';
                }
            }
        }

        // Print function for individual responses
        function printResponse(responseId) {
            const responseElement = document.querySelector(`[data-response-id="${responseId}"]`);
            if (responseElement) {
                const printContent = responseElement.cloneNode(true);

                // Remove buttons and interactive elements
                printContent.querySelectorAll('button, .btn-group').forEach(el => el.remove());

                const printWindow = window.open('', '_blank');
                printWindow.document.write(`
            <!DOCTYPE html>
            <html>
            <head>
                <title>รายละเอียดคำตอบแบบประเมิน #${responseId}</title>
                <style>
                    body { font-family: 'Sarabun', Arial, sans-serif; margin: 20px; line-height: 1.6; }
                    .header { border-bottom: 2px solid #ddd; padding-bottom: 15px; margin-bottom: 20px; }
                    .category-section { margin: 20px 0; padding: 15px; border-left: 4px solid #007bff; background: #f8f9fa; }
                    .question-item { margin: 10px 0; padding: 10px; background: white; border-radius: 5px; }
                    .answer-text { margin: 8px 0 12px 20px; padding: 8px; }
                    .text-answer { background: #f1f3f4; padding: 10px; border-left: 3px solid #007bff; font-style: italic; }
                    .badge { padding: 4px 8px; border-radius: 4px; font-size: 0.8em; }
                    .score-5 { background: #d4edda; color: #155724; }
                    .score-4 { background: #d1ecf1; color: #0c5460; }
                    .score-3 { background: #fff3cd; color: #856404; }
                    .score-2 { background: #f8d7da; color: #721c24; }
                    .score-1 { background: #f5c6cb; color: #721c24; }
                </style>
            </head>
            <body>
                <div class="header">
                    <h2>รายละเอียดคำตอบแบบประเมิน #${responseId}</h2>
                    <p>วันที่พิมพ์: ${new Date().toLocaleDateString('th-TH')} ${new Date().toLocaleTimeString('th-TH')}</p>
                </div>
                ${printContent.innerHTML}
            </body>
            </html>
        `);
                printWindow.document.close();
                printWindow.print();
            }
        }

        // Auto-refresh functionality
        let autoRefreshInterval;

        function toggleAutoRefresh() {
            const button = document.getElementById('autoRefreshBtn');

            if (autoRefreshInterval) {
                clearInterval(autoRefreshInterval);
                autoRefreshInterval = null;
                button.innerHTML = '<i class="fas fa-play me-1"></i>รีเฟรชอัตโนมัติ';
                button.className = 'btn btn-soft-success btn-sm elegant-btn';
                showToast('ปิดการรีเฟรชอัตโนมัติแล้ว', 'info');
            } else {
                autoRefreshInterval = setInterval(() => {
                    updateLastRefreshTime();
                    // Optional: reload page or update data via AJAX
                    // window.location.reload();
                }, 30000); // Refresh every 30 seconds

                button.innerHTML = '<i class="fas fa-pause me-1"></i>ปิดรีเฟรชอัตโนมัติ';
                button.className = 'btn btn-soft-warning btn-sm elegant-btn';
                showToast('เปิดการรีเฟรชอัตโนมัติทุก 30 วินาที', 'success');
            }
        }

        // Update last refresh time
        function updateLastRefreshTime() {
            const now = new Date();
            const timeString = now.toLocaleTimeString('th-TH');
            const elements = document.querySelectorAll('.last-refresh-time');
            elements.forEach(el => {
                el.textContent = `อัปเดตล่าสุด: ${timeString}`;
            });
        }

        // Loading state management
        function showLoadingState() {
            const loadingHtml = `
        <div id="loadingOverlay" class="d-flex justify-content-center align-items-center position-fixed top-0 start-0 w-100 h-100" style="background: rgba(255,255,255,0.9); z-index: 9998; backdrop-filter: blur(5px);">
            <div class="text-center">
                <div class="spinner-border text-primary mb-3" role="status" style="width: 3rem; height: 3rem;">
                    <span class="visually-hidden">กำลังโหลด...</span>
                </div>
                <div class="text-muted">กำลังโหลดข้อมูล...</div>
            </div>
        </div>
    `;

            document.body.insertAdjacentHTML('beforeend', loadingHtml);
        }

        function hideLoadingState() {
            const loadingOverlay = document.getElementById('loadingOverlay');
            if (loadingOverlay) {
                loadingOverlay.remove();
            }
        }

        // Keyboard shortcuts
        document.addEventListener('keydown', function (e) {
            // Ctrl + R: Refresh page
            if (e.ctrlKey && e.key === 'r') {
                e.preventDefault();
                window.location.reload();
            }

            // Ctrl + E: Export data
            if (e.ctrlKey && e.key === 'e') {
                e.preventDefault();
                exportAllResponses();
            }

            // Ctrl + F: Focus search
            if (e.ctrlKey && e.key === 'f') {
                e.preventDefault();
                const searchInput = document.getElementById('quickSearch');
                if (searchInput) {
                    searchInput.focus();
                }
            }

            // Escape: Clear search and filters
            if (e.key === 'Escape') {
                clearSearch();
                // Optional: Clear filters
                // window.location.href = '<?= site_url("System_reports/all_assessment_responses") ?>';
            }
        });

        // Add visual feedback for interactions
        document.querySelectorAll('.btn').forEach(btn => {
            btn.addEventListener('click', function () {
                this.style.transform = 'scale(0.98)';
                setTimeout(() => {
                    this.style.transform = '';
                }, 100);
            });
        });

        // Enhanced error handling
        window.addEventListener('error', function (e) {
            console.error('JavaScript Error:', e.error);
            if (e.error && e.error.name !== 'ChunkLoadError') {
                showToast('เกิดข้อผิดพลาดในการแสดงผล กรุณารีเฟรชหน้าเว็บ', 'error');
            }
        });

        // Global unhandled promise rejection handler
        window.addEventListener('unhandledrejection', function (e) {
            console.error('Unhandled promise rejection:', e.reason);
            showToast('เกิดข้อผิดพลาดในการประมวลผล', 'warning');
        });

        // Performance monitoring
        window.addEventListener('load', function () {
            const loadTime = performance.now();
            if (loadTime > 3000) {
                console.warn('Page load time is slow:', loadTime + 'ms');
            }

            // Log page statistics
            console.log('Page loaded successfully:', {
                loadTime: Math.round(loadTime),
                totalResponses: <?= $total_responses ?>,
                currentPage: <?= $current_page ?>,
                hasData: <?= $has_data ? 'true' : 'false' ?>
    });
});

        // Utility: Debounce function
        function debounce(func, wait) {
            let timeout;
            return function executedFunction(...args) {
                const later = () => {
                    clearTimeout(timeout);
                    func.apply(this, args);
                };
                clearTimeout(timeout);
                timeout = setTimeout(later, wait);
            };
        }

        // Utility: Format numbers
        function formatNumber(number, options = {}) {
            const defaults = { minimumFractionDigits: 0, maximumFractionDigits: 2 };
            return new Intl.NumberFormat('th-TH', { ...defaults, ...options }).format(number);
        }

        // Utility: Format dates
        function formatDate(date, options = {}) {
            const defaults = { year: 'numeric', month: 'long', day: 'numeric' };
            return new Date(date).toLocaleDateString('th-TH', { ...defaults, ...options });
        }

        // Initialize page statistics
        function initPageStatistics() {
            const stats = {
                totalResponses: <?= $total_responses ?>,
                currentPage: <?= $current_page ?>,
                    totalPages: <?= $total_pages ?>,
                        hasFilters: <?= (!empty($filter_date_from) || !empty($filter_date_to) || !empty($filter_score)) ? 'true' : 'false' ?>,
                            averageScore: <?= $statistics['average_score'] ?? 0 ?>
    };

        console.log('Page Statistics:', stats);
        return stats;
}

        // Smooth scroll to top
        function scrollToTop() {
            window.scrollTo({
                top: 0,
                behavior: 'smooth'
            });
        }

        // Add scroll to top button
        function addScrollToTopButton() {
            const scrollButton = document.createElement('button');
            scrollButton.innerHTML = '<i class="fas fa-chevron-up"></i>';
            scrollButton.className = 'btn btn-primary position-fixed';
            scrollButton.style.cssText = `
        bottom: 20px;
        right: 20px;
        z-index: 1000;
        width: 50px;
        height: 50px;
        border-radius: 50%;
        display: none;
        box-shadow: 0 4px 12px rgba(0,0,0,0.15);
    `;
            scrollButton.onclick = scrollToTop;

            document.body.appendChild(scrollButton);

            // Show/hide on scroll
            window.addEventListener('scroll', function () {
                if (window.pageYOffset > 300) {
                    scrollButton.style.display = 'block';
                } else {
                    scrollButton.style.display = 'none';
                }
            });
        }

        // Initialize scroll to top button
        addScrollToTopButton();

        // Initialize page
        initPageStatistics();

        // Export functions for global use
        window.AssessmentResponses = {
            showToast,
            formatNumber,
            formatDate,
            exportAllResponses,
            toggleAutoRefresh,
            toggleResponseDetails,
            printResponse,
            clearSearch,
            showLoadingState,
            hideLoadingState,
            scrollToTop,
            debounce
        };

        // Browser compatibility check
        if (!window.fetch) {
            showToast('เบราว์เซอร์ของคุณไม่รองรับฟีเจอร์บางอย่าง กรุณาอัปเดตเบราว์เซอร์', 'warning');
        }

        // Check if user prefers reduced motion
        if (window.matchMedia('(prefers-reduced-motion: reduce)').matches) {
            // Disable animations for users who prefer reduced motion
            const style = document.createElement('style');
            style.textContent = `
        *, *::before, *::after {
            animation-duration: 0.01ms !important;
            animation-iteration-count: 1 !important;
            transition-duration: 0.01ms !important;
        }
    `;
            document.head.appendChild(style);
        }

        // Initialize page interactions
        document.addEventListener('DOMContentLoaded', function () {
            // Highlight active navigation
            const currentPath = window.location.pathname;
            const navLinks = document.querySelectorAll('.nav-link');
            navLinks.forEach(link => {
                if (link.href && currentPath.includes(link.getAttribute('href'))) {
                    link.classList.add('active');
                }
            });

            // Initialize lazy loading for images (if any)
            if ('IntersectionObserver' in window) {
                const imageObserver = new IntersectionObserver((entries, observer) => {
                    entries.forEach(entry => {
                        if (entry.isIntersecting) {
                            const img = entry.target;
                            if (img.dataset.src) {
                                img.src = img.dataset.src;
                                img.removeAttribute('data-src');
                                imageObserver.unobserve(img);
                            }
                        }
                    });
                });

                document.querySelectorAll('img[data-src]').forEach(img => {
                    imageObserver.observe(img);
                });
            }

            // Check for updates (optional feature)
            if (document.hidden !== undefined) {
                document.addEventListener('visibilitychange', function () {
                    if (!document.hidden) {
                        updateLastRefreshTime();
                    }
                });
            }
        });

        // Analytics tracking (optional)
        function trackPageView() {
            // Google Analytics or other tracking code can be added here
            if (typeof gtag !== 'undefined') {
                gtag('config', 'GA_MEASUREMENT_ID', {
                    page_title: 'รายการคำตอบแบบประเมินทั้งหมด',
                    page_location: window.location.href
                });
            }
        }

        // Call analytics tracking
        trackPageView();

        // Service Worker registration (optional for PWA features)
        if ('serviceWorker' in navigator) {
            window.addEventListener('load', function () {
                navigator.serviceWorker.register('/sw.js')
                    .then(function (registration) {
                        console.log('SW registered: ', registration);
                    })
                    .catch(function (registrationError) {
                        console.log('SW registration failed: ', registrationError);
                    });
            });
        }

        // Final initialization
        console.log('All Assessment Responses page initialized successfully');
    </script>