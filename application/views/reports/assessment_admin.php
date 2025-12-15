<!-- Assessment Reports and Statistics Panel - Fixed Division by Zero -->
<div class="assessment-admin-container">
    <!-- Header Spacing -->
    <div style="height: 60px;"></div>
    
    <div class="row mb-4">
        <div class="col-12">
            <div class="card shadow-elegant">
                <div class="card-header gradient-header">
                    <div class="d-flex justify-content-between align-items-center">
                        <h5 class="mb-0 header-title">
                            <i class="fas fa-chart-bar me-2"></i>
                            รายงานผลการประเมินความพึงพอใจการให้บริการ
                        </h5>
                        <div class="btn-group" role="group">
    <button type="button" class="btn btn-soft-light btn-sm" onclick="refreshAssessmentData()" id="refreshBtn">
        <i class="fas fa-sync-alt me-1"></i>
        รีเฟรชข้อมูล
    </button>
    <a href="<?= site_url('assessment') ?>" target="_blank" class="btn btn-soft-success btn-sm">
        <i class="fas fa-external-link-alt me-1"></i>
        ดูแบบประเมิน
    </a>
    <button type="button" class="btn btn-soft-info btn-sm" onclick="exportAssessmentReport()">
        <i class="fas fa-download me-1"></i>
        ส่งออกรายงาน
    </button>
    
    <?php 
    // ตรวจสอบสิทธิ์โดยตรง
    $can_manage_form = false;
    $can_clear_data = false; // เพิ่มตัวแปรสำหรับล้างข้อมูล
    $user_system = $this->session->userdata('m_system');
    $grant_user_ref_id = $this->session->userdata('grant_user_ref_id');
    
    // System Admin เท่านั้นที่ล้างข้อมูลได้
    if ($user_system === 'system_admin') {
        $can_manage_form = true;
        $can_clear_data = true;
    }
    // Super Admin จัดการฟอร์มได้แต่ล้างข้อมูลไม่ได้
    elseif ($user_system === 'super_admin') {
        $can_manage_form = true;
        $can_clear_data = false;
    }
    // User Admin ต้องมี grant 125
    elseif ($user_system === 'user_admin' && !empty($grant_user_ref_id)) {
        $grants = array_map('trim', explode(',', $grant_user_ref_id));
        $can_manage_form = in_array('125', $grants);
        $can_clear_data = false; // User Admin ล้างข้อมูลไม่ได้
    }
    ?>
    
    <?php if ($can_manage_form): ?>
        <button type="button" class="btn btn-soft-primary btn-sm" onclick="goToManagement()">
            <i class="fas fa-cogs me-1"></i>
            จัดการฟอร์ม
        </button>
    <?php else: ?>
        <button type="button" class="btn btn-outline-secondary btn-sm" disabled title="ไม่มีสิทธิ์จัดการฟอร์ม">
            <i class="fas fa-lock me-1"></i>
            จัดการฟอร์ม
        </button>
    <?php endif; ?>
    
    <!-- ✅ ปุ่มล้างข้อมูล - เห็นได้เฉพาะ System Admin -->
    <?php if ($can_clear_data): ?>
        <button type="button" class="btn btn-soft-danger btn-sm" onclick="confirmClearAssessmentData()" id="clearDataBtn">
            <i class="fas fa-trash-alt me-1"></i>
            ล้างข้อมูลการตอบ
        </button>
    <?php endif; ?>
</div>

                    </div>
                </div>
                <div class="card-body soft-bg">
                    <!-- Enhanced Status Row -->
                    <div class="row mb-4">
                        <div class="col-md-3 col-sm-6 mb-3">
                            <div class="stat-card soft-success">
                                <div class="stat-card-header">
                                    <div class="stat-icon success-icon">
                                        <i class="fas fa-users"></i>
                                    </div>
                                    <div class="stat-change positive">
                                        <i class="fas fa-arrow-up"></i>
                                    </div>
                                </div>
                                <div class="stat-info">
                                    <div class="stat-label">จำนวนผู้ตอบรวม</div>
                                    <div class="stat-value success-text" id="totalResponsesDisplay">
                                        <?= number_format($statistics['total_responses'] ?? 0) ?> คน
                                    </div>
                                </div>
                            </div>
                        </div>
                        
                        <div class="col-md-3 col-sm-6 mb-3">
                            <div class="stat-card soft-primary">
                                <div class="stat-card-header">
                                    <div class="stat-icon primary-icon">
                                        <i class="fas fa-calendar-day"></i>
                                    </div>
                                    <div class="stat-change positive">
                                        <i class="fas fa-plus"></i>
                                    </div>
                                </div>
                                <div class="stat-info">
                                    <div class="stat-label">ผู้ตอบวันนี้</div>
                                    <div class="stat-value primary-text" id="todayResponsesDisplay">
                                        <?= number_format($statistics['today_responses'] ?? 0) ?> คน
                                    </div>
                                </div>
                            </div>
                        </div>
                        
                        <div class="col-md-3 col-sm-6 mb-3">
                            <div class="stat-card soft-warning">
                                <div class="stat-card-header">
                                    <div class="stat-icon warning-icon">
                                        <i class="fas fa-star"></i>
                                    </div>
                                    <div class="stat-change positive">
                                        <i class="fas fa-chart-line"></i>
                                    </div>
                                </div>
                                <div class="stat-info">
                                    <div class="stat-label">คะแนนเฉลี่ยรวม</div>
                                    <div class="stat-value warning-text" id="averageScoreDisplay">
                                        <?= number_format($statistics['average_score'] ?? 0, 2) ?>/5.00
                                    </div>
                                </div>
                            </div>
                        </div>
                        
                        <div class="col-md-3 col-sm-6 mb-3">
                            <div class="stat-card soft-info">
                                <div class="stat-card-header">
                                    <div class="stat-icon info-icon">
                                        <i class="fas fa-clock"></i>
                                    </div>
                                    <div class="stat-change positive">
                                        <i class="fas fa-check"></i>
                                    </div>
                                </div>
                                <div class="stat-info">
                                    <div class="stat-label">อัพเดทล่าสุด</div>
                                    <div class="stat-value info-text" id="lastUpdateDisplay">
                                        <?= date('H:i น.') ?>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Overall Satisfaction Rating -->
                    <div class="row mb-4">
                        <div class="col-12">
                            <div class="chart-card elegant-card">
                                <!-- Chart Header ใหม่ - ไม่มีสีและเส้นขอบ -->
<div class="chart-header elegant-header">
    <h3 class="chart-title">ระดับความพึงพอใจโดยรวม</h3>
    <div class="chart-actions">
        <span class="satisfaction-display">
            <?php 
            $overall_score = $statistics['average_score'] ?? 0;
            $satisfaction_level = '';
            $satisfaction_color = '';
            
            if ($overall_score >= 4.5) {
                $satisfaction_level = 'ดีมาก';
                $satisfaction_color = 'success';
            } elseif ($overall_score >= 4.0) {
                $satisfaction_level = 'ดี';
                $satisfaction_color = 'primary';
            } elseif ($overall_score >= 3.5) {
                $satisfaction_level = 'ปานกลาง';
                $satisfaction_color = 'info';
            } elseif ($overall_score >= 3.0) {
                $satisfaction_level = 'พอใช้';
                $satisfaction_color = 'warning';
            } else {
                $satisfaction_level = 'ควรปรับปรุง';
                $satisfaction_color = 'danger';
            }
            ?>
            <span class="text-<?= $satisfaction_color ?> satisfaction-badge-clean">
                <i class="fas fa-star"></i> <?= $satisfaction_level ?>
            </span>
        </span>
    </div>
</div>
                                
                                <!-- Overall Progress Bar -->
                                <div class="progress elegant-progress mb-4">
                                    <div class="progress-bar bg-<?= $satisfaction_color ?> progress-bar-striped progress-bar-animated elegant-progress-bar" 
                                         style="width: <?= ($overall_score / 5) * 100 ?>%"
                                         role="progressbar">
                                        <strong><?= number_format($overall_score, 2) ?>/5.00</strong>
                                    </div>
                                </div>
                                
                                <!-- Score Breakdown - แก้ไขปัญหา Division by Zero -->
                                <div class="row text-center">
                                    <?php 
                                    $score_labels = [
                                        1 => ['label' => 'ควรปรับปรุง', 'class' => 'danger'],
                                        2 => ['label' => 'พอใช้', 'class' => 'warning'], 
                                        3 => ['label' => 'ปานกลาง', 'class' => 'info'],
                                        4 => ['label' => 'ดี', 'class' => 'primary'],
                                        5 => ['label' => 'ดีมาก', 'class' => 'success']
                                    ];
                                    
                                    // ✅ แก้ไขปัญหา Division by Zero - รองรับทุกกรณี
                                    $total_answers = 0;
                                    $safe_score_distribution = [];
                                    
                                    // ตรวจสอบและสร้างข้อมูลเริ่มต้นที่ปลอดภัย
                                    if (isset($score_distribution) && is_array($score_distribution) && !empty($score_distribution)) {
                                        $safe_score_distribution = $score_distribution;
                                        $total_answers = array_sum($safe_score_distribution);
                                    } else {
                                        // สร้างข้อมูลเริ่มต้นถ้าไม่มีข้อมูล
                                        $safe_score_distribution = [1 => 0, 2 => 0, 3 => 0, 4 => 0, 5 => 0];
                                        $total_answers = 0;
                                    }
                                    
                                    // ตรวจสอบให้แน่ใจว่า total_answers ไม่เป็นลบหรือ null
                                    if (!is_numeric($total_answers) || $total_answers < 0) {
                                        $total_answers = 0;
                                    }
                                    
                                    foreach ($score_labels as $score => $data): 
                                        // ดึงจำนวนคำตอบอย่างปลอดภัย
                                        $count = 0;
                                        if (isset($safe_score_distribution[$score])) {
                                            $count = intval($safe_score_distribution[$score]);
                                            if ($count < 0) $count = 0; // ป้องกันค่าลบ
                                        }
                                        
                                        // ✅ ป้องกัน Division by Zero อย่างสมบูรณ์
                                        $percentage = 0;
                                        if ($total_answers > 0 && is_numeric($total_answers) && $count >= 0) {
                                            $percentage = round(($count / $total_answers) * 100, 1);
                                            // ตรวจสอบผลลัพธ์
                                            if (!is_finite($percentage) || $percentage < 0) {
                                                $percentage = 0;
                                            }
                                        }
                                    ?>
                                    <div class="col">
                                        <div class="score-item elegant-score">
                                            <div class="score-number text-<?= $data['class'] ?>"><?= $score ?></div>
                                            <div class="score-label"><?= $data['label'] ?></div>
                                            <div class="score-count">
                                                <?= number_format($count) ?> คำตอบ
                                                <br><small class="text-muted soft-text">(<?= number_format($percentage, 1) ?>%)</small>
                                            </div>
                                        </div>
                                    </div>
                                    <?php endforeach; ?>
                                </div>
                                
                                <!-- คำอธิบาย -->
                                <div class="mt-3 text-center">
                                    <small class="text-muted explanation-text">
                                        <i class="fas fa-info-circle me-1"></i>
                                        แสดงจำนวนคำตอบที่เลือกคะแนนแต่ละระดับ (1 คนตอบได้หลายคำถาม)
                                    </small>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Summary Statistics -->
                    <div class="row mb-4">
                        <div class="col-md-6">
                            <div class="card elegant-summary-card border-soft-info">
                                <div class="card-body text-center soft-info-bg">
                                    <h5 class="card-title text-info elegant-title">
                                        <i class="fas fa-users"></i> ผู้ตอบแบบประเมิน
                                    </h5>
                                    <h2 class="text-primary elegant-number"><?= number_format($statistics['total_responses'] ?? 0) ?> คน</h2>
                                    <p class="text-muted mb-0 soft-description">จำนวนคนที่เข้ามาตอบแบบประเมิน</p>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="card elegant-summary-card border-soft-warning">
                                <div class="card-body text-center soft-warning-bg">
                                    <h5 class="card-title text-warning elegant-title">
                                        <i class="fas fa-list-ul"></i> คำตอบทั้งหมด
                                    </h5>
                                    <?php 
                                    // ✅ คำนวณ total_answers อย่างปลอดภัย
                                    $total_answers_display = 0;
                                    if (isset($score_distribution) && is_array($score_distribution)) {
                                        $total_answers_display = array_sum($score_distribution);
                                        if (!is_numeric($total_answers_display) || $total_answers_display < 0) {
                                            $total_answers_display = 0;
                                        }
                                    }
                                    ?>
                                    <h2 class="text-orange elegant-number"><?= number_format($total_answers_display) ?> คำตอบ</h2>
                                    <p class="text-muted mb-0 soft-description">จำนวนคำตอบรวมทุกคำถาม</p>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Category Scores Detailed Report -->
                    <div class="row mb-4">
                        <div class="col-12">
                            <div class="card elegant-category-card">
                                <div class="card-header elegant-category-header">
                                    <h6 class="mb-0 category-title">
                                        <i class="fas fa-chart-pie me-2"></i>
                                        คะแนนเฉลี่ยแต่ละหมวดการประเมิน
                                    </h6>
                                </div>
                                <div class="card-body soft-category-bg">
                                    <?php if (!empty($statistics['categories'])): ?>
                                        <?php foreach ($statistics['categories'] as $cat_id => $cat_data): ?>
                                            <div class="category-score-item elegant-category-item mb-4">
                                                <div class="d-flex justify-content-between align-items-center mb-2">
                                                    <h6 class="category-name mb-0 elegant-category-name">
                                                        <i class="fas fa-folder-open me-2 text-primary"></i>
                                                        <?= htmlspecialchars($cat_data['name'] ?? 'ไม่ระบุชื่อหมวด') ?>
                                                    </h6>
                                                    <div class="category-score">
                                                        <span class="badge elegant-badge fs-6 px-3 py-2">
                                                            <?= number_format($cat_data['avg_score'] ?? 0, 2) ?>/5.00
                                                        </span>
                                                    </div>
                                                </div>
                                                
                                                <!-- Category Progress Bar -->
                                                <div class="progress elegant-category-progress mb-2">
                                                    <?php 
                                                    // ✅ ป้องกัน Division by Zero และ null values
                                                    $cat_avg_score = isset($cat_data['avg_score']) ? floatval($cat_data['avg_score']) : 0;
                                                    
                                                    // ตรวจสอบว่าค่าเป็นตัวเลขที่ถูกต้อง
                                                    if (!is_finite($cat_avg_score) || $cat_avg_score < 0) {
                                                        $cat_avg_score = 0;
                                                    }
                                                    
                                                    $cat_percentage = 0;
                                                    if ($cat_avg_score > 0 && $cat_avg_score <= 5) {
                                                        $cat_percentage = ($cat_avg_score / 5) * 100;
                                                        
                                                        // ตรวจสอบผลลัพธ์อีกครั้ง
                                                        if (!is_finite($cat_percentage) || $cat_percentage < 0) {
                                                            $cat_percentage = 0;
                                                        } elseif ($cat_percentage > 100) {
                                                            $cat_percentage = 100;
                                                        }
                                                    }
                                                    
                                                    $cat_color = 'secondary'; // default color
                                                    if ($cat_avg_score >= 4.5) $cat_color = 'success';
                                                    elseif ($cat_avg_score >= 4.0) $cat_color = 'primary';
                                                    elseif ($cat_avg_score >= 3.5) $cat_color = 'info';
                                                    elseif ($cat_avg_score >= 3.0) $cat_color = 'warning';
                                                    elseif ($cat_avg_score > 0) $cat_color = 'danger';
                                                    ?>
                                                    <div class="progress-bar bg-<?= $cat_color ?> progress-bar-striped elegant-cat-progress" 
                                                         style="width: <?= number_format($cat_percentage, 1) ?>%"
                                                         role="progressbar">
                                                        <?= number_format($cat_percentage, 1) ?>%
                                                    </div>
                                                </div>
                                                
                                                <!-- Category Details -->
                                                <div class="row">
                                                    <div class="col-md-6">
                                                        <small class="text-muted soft-detail">
                                                            <i class="fas fa-question-circle me-1"></i>
                                                            จำนวนคำถาม: <strong><?= intval($cat_data['question_count'] ?? 0) ?></strong> ข้อ
                                                        </small>
                                                    </div>
                                                    <div class="col-md-6 text-end">
                                                        <small class="text-muted soft-detail">
                                                            <i class="fas fa-users me-1"></i>
                                                            ผู้ตอบ: <strong><?= intval($cat_data['response_count'] ?? 0) ?></strong> คน
                                                        </small>
                                                    </div>
                                                </div>
                                            </div>
                                            <?php 
                                            $category_keys = array_keys($statistics['categories']);
                                            $current_index = array_search($cat_id, $category_keys);
                                            $is_last = ($current_index === count($category_keys) - 1);
                                            if (!$is_last): 
                                            ?>
                                                <hr class="elegant-divider my-3">
                                            <?php endif; ?>
                                        <?php endforeach; ?>
                                    <?php else: ?>
                                        <div class="text-center py-5 empty-state">
                                            <i class="fas fa-chart-pie fa-3x text-muted mb-3"></i>
                                            <h5 class="text-muted">ยังไม่มีข้อมูลการประเมิน</h5>
                                            <p class="text-muted">เมื่อมีผู้ตอบแบบประเมินแล้ว คะแนนจะแสดงที่นี่</p>
                                            <a href="<?= site_url('assessment') ?>" target="_blank" class="btn btn-soft-primary">
                                                <i class="fas fa-clipboard-check me-1"></i>
                                                ไปยังแบบประเมิน
                                            </a>
                                        </div>
                                    <?php endif; ?>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Recent Responses and Trends -->
                    <div class="row mb-4">
                        <!-- Recent Responses Section - แก้ไขให้แสดงแยกเป็นรายคน -->
<div class="col-md-6">
    <div class="card bg-light">
        <div class="card-body">
            <h6 class="card-title">
                <i class="fas fa-calendar-alt text-info"></i>
                การตอบล่าสุด (10 รายการ)
                <small class="text-muted">(จากหมวดที่เปิดใช้งาน)</small>
            </h6>
            <div id="recentResponsesList">
                <?php if (!empty($recent_responses)): ?>
                    <?php foreach ($recent_responses as $index => $response): ?>
                        <div class="d-flex justify-content-between align-items-center mb-2 p-2 bg-white rounded border">
                            <div class="flex-grow-1">
                                <div class="d-flex align-items-center mb-1">
                                    <span class="badge bg-secondary me-2">#<?= $index + 1 ?></span>
                                    <small class="text-muted">
                                        <i class="fas fa-clock"></i>
                                        <?= date('d/m/Y H:i', strtotime($response->completed_at)) ?>
                                    </small>
                                </div>
                                <?php if (!empty($response->ip_address)): ?>
                                <small class="text-muted">
                                    <i class="fas fa-globe"></i>
                                    <?= $response->ip_address ?>
                                    <?php if (!empty($response->browser_fingerprint)): ?>
                                        | <i class="fas fa-fingerprint"></i> <?= substr($response->browser_fingerprint, 0, 8) ?>...
                                    <?php endif; ?>
                                </small>
                                <?php endif; ?>
                                
                                <!-- แสดงรายละเอียดแยกประเภท -->
                                <div class="mt-1">
                                    <?php if (isset($response->scoring_answers) && $response->scoring_answers > 0): ?>
                                    <small class="text-success d-block">
                                        <i class="fas fa-star"></i>
                                        คะแนนประเมิน: <?= $response->scoring_answers ?> คำตอบ
                                    </small>
                                    <?php endif; ?>
                                    
                                    <?php if (isset($response->feedback_answers) && $response->feedback_answers > 0): ?>
                                    <small class="text-info d-block">
                                        <i class="fas fa-comment"></i>
                                        ข้อเสนอแนะ: <?= $response->feedback_answers ?> คำตอบ
                                    </small>
                                    <?php endif; ?>
                                </div>
                            </div>
                            
                            <div class="text-end">
                             
                                
                                
                                <!-- แสดง Badge ประเภท -->
                                <div>
                                    <?php if (isset($response->has_scoring) && $response->has_scoring): ?>
                                    <span class="badge bg-success me-1">
                                        <i class="fas fa-star"></i> ประเมิน
                                    </span>
                                    <?php endif; ?>
                                    
                                    <?php if (isset($response->has_feedback) && $response->has_feedback): ?>
                                    <span class="badge bg-info">
                                        <i class="fas fa-comment"></i> ข้อเสนอแนะ
                                    </span>
                                    <?php endif; ?>
                                </div>
                            </div>
                        </div>
                    <?php endforeach; ?>
                    
                    <!-- ลิงก์ดูเพิ่มเติม -->
                    <div class="text-center mt-3">
                        <a href="<?= site_url('System_reports/assessment_comments') ?>" class="btn btn-outline-info btn-sm">
                            <i class="fas fa-list me-1"></i>
                            ดูข้อเสนอแนะทั้งหมด
                        </a>
                        <button type="button" class="btn btn-outline-primary btn-sm ms-2" onclick="viewAllResponses()">
                            <i class="fas fa-users me-1"></i>
                            ดูการตอบทั้งหมด
                        </button>
                    </div>
                <?php else: ?>
                    <div class="text-center text-muted py-3">
                        <i class="fas fa-inbox fa-2x mb-2"></i>
                        <p class="mb-0">ยังไม่มีการตอบแบบประเมิน</p>
                        <small>จากหมวดหมู่ที่เปิดใช้งาน</small>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </div>
</div>
                        
                        <div class="col-md-6">
                            <div class="card elegant-stats-card">
                                <div class="card-header elegant-stats-header">
                                    <h6 class="mb-0 stats-title">
                                        <i class="fas fa-chart-line me-2"></i>
                                        สถิติการตอบ 7 วันล่าสุด
                                    </h6>
                                </div>
                                <div class="card-body soft-stats-bg">
                                    <?php if (!empty($daily_stats)): ?>
                                        <?php 
                                        // ✅ ป้องกัน Division by Zero สำหรับ daily stats - Enhanced
                                        $max_count = 1; // เริ่มต้นด้วย 1 เพื่อป้องกัน division by zero
                                        $all_counts = [];
                                        
                                        // รวบรวมและตรวจสอบข้อมูล
                                        foreach ($daily_stats as $day) {
                                            $count = isset($day['count']) ? intval($day['count']) : 0;
                                            if ($count < 0) $count = 0; // ป้องกันค่าลบ
                                            $all_counts[] = $count;
                                        }
                                        
                                        // หาค่าสูงสุด
                                        if (!empty($all_counts)) {
                                            $calculated_max = max($all_counts);
                                            if (is_numeric($calculated_max) && $calculated_max > 0) {
                                                $max_count = $calculated_max;
                                            }
                                        }
                                        ?>
                                        <?php foreach ($daily_stats as $index => $day): ?>
                                            <?php
                                            // ตรวจสอบและทำความสะอาดข้อมูล
                                            $day_count = isset($day['count']) ? intval($day['count']) : 0;
                                            if ($day_count < 0) $day_count = 0;
                                            
                                            $date_thai = isset($day['date_thai']) ? htmlspecialchars($day['date_thai']) : 'ไม่ระบุ';
                                            $date_eng = isset($day['date_eng']) ? htmlspecialchars($day['date_eng']) : '';
                                            ?>
                                            <div class="daily-stat elegant-daily d-flex justify-content-between align-items-center mb-2">
                                                <div class="day-info elegant-day-info">
                                                    <span class="fw-bold day-thai"><?= $date_thai ?></span>
                                                    <?php if (!empty($date_eng)): ?>
                                                        <small class="text-muted ms-2 day-eng"><?= $date_eng ?></small>
                                                    <?php endif; ?>
                                                </div>
                                                <div class="day-count">
                                                    <span class="badge elegant-warning-badge">
                                                        <?= number_format($day_count) ?> คน
                                                    </span>
                                                </div>
                                            </div>
                                            <div class="progress elegant-daily-progress mb-3">
                                                <?php 
                                                // ✅ ป้องกัน Division by Zero อย่างสมบูรณ์
                                                $progress_width = 0;
                                                if ($max_count > 0 && $day_count >= 0 && is_numeric($max_count) && is_numeric($day_count)) {
                                                    $progress_width = ($day_count / $max_count) * 100;
                                                    
                                                    // ตรวจสอบผลลัพธ์
                                                    if (!is_finite($progress_width) || $progress_width < 0) {
                                                        $progress_width = 0;
                                                    } elseif ($progress_width > 100) {
                                                        $progress_width = 100;
                                                    }
                                                }
                                                ?>
                                                <div class="progress-bar bg-warning elegant-daily-bar" 
                                                     style="width: <?= number_format($progress_width, 1) ?>%">
                                                </div>
                                            </div>
                                        <?php endforeach; ?>
                                    <?php else: ?>
                                        <div class="text-center py-4 empty-stats">
                                            <i class="fas fa-chart-line fa-2x text-muted mb-2"></i>
                                            <p class="text-muted">ยังไม่มีข้อมูลสถิติ</p>
                                        </div>
                                    <?php endif; ?>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Comments and Feedback Section -->
                    <?php if (!empty($feedback_comments)): ?>
                    <div class="row mb-4">
                        <div class="col-12">
                            <div class="card elegant-feedback-card">
                                <div class="card-header elegant-feedback-header">
                                    <h6 class="mb-0 feedback-title">
                                        <i class="fas fa-comments me-2"></i>
                                        ข้อเสนอแนะและความคิดเห็น (5 รายการล่าสุด)
                                    </h6>
                                </div>
                                <div class="card-body soft-feedback-bg">
                                    <?php foreach ($feedback_comments as $comment): ?>
                                        <div class="comment-item elegant-comment mb-3 p-3">
                                            <div class="comment-header d-flex justify-content-between align-items-start mb-2">
                                                <span class="comment-date small soft-date">
                                                    <i class="fas fa-calendar me-1"></i>
                                                    <?= date('d/m/Y H:i', strtotime($comment->created_at)) ?>
                                                </span>
                                                <span class="badge elegant-info-badge">
                                                    <i class="fas fa-comment"></i> ข้อเสนอแนะ
                                                </span>
                                            </div>
                                            <div class="comment-text elegant-comment-text">
                                                <i class="fas fa-quote-left text-muted me-2"></i>
                                                <?= nl2br(htmlspecialchars($comment->answer_text)) ?>
                                                <i class="fas fa-quote-right text-muted ms-2"></i>
                                            </div>
                                        </div>
                                    <?php endforeach; ?>
                                    
                                    <div class="text-center mt-3">
                                        <button type="button" class="btn btn-soft-primary btn-sm" onclick="viewAllComments()">
                                            <i class="fas fa-eye me-1"></i>
                                            ดูทั้งหมด
                                        </button>
                                    </div>
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

<!-- CSS Styles - สีอ่อน หรูหรา -->
<style>
/* Main Container */
.assessment-admin-container {
    min-height: 100vh;
    padding: 2px;
}

/* Elegant Card Shadows */
.shadow-elegant {
    box-shadow: 
        0 4px 20px rgba(0,0,0,0.08),
        0 2px 6px rgba(0,0,0,0.04),
        inset 0 1px 0 rgba(255,255,255,0.9);
    border: 1px solid rgba(255,255,255,0.2);
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
    text-shadow: 0 1px 3px rgba(0,0,0,0.1);
}

/* Soft Backgrounds */
.soft-bg {
    background: linear-gradient(135deg, 
        rgba(255,255,255,0.9) 0%, 
        rgba(248,250,252,0.9) 100%);
}

/* Stat Cards */
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

/* Stat Icons */
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

/* Elegant Cards */
.elegant-card {
    background: linear-gradient(135deg, rgba(255,255,255,0.95) 0%, rgba(249,250,251,0.95) 100%);
    border: 1px solid rgba(255,255,255,0.2);
    border-radius: 16px;
    box-shadow: 
        0 4px 20px rgba(0,0,0,0.08),
        inset 0 1px 0 rgba(255,255,255,0.9);
    backdrop-filter: blur(15px);
}

.elegant-header {
    background: linear-gradient(135deg, 
        rgba(124, 58, 237, 0.1) 0%, 
        rgba(147, 51, 234, 0.1) 100%);
    border-bottom: 1px solid rgba(124, 58, 237, 0.1);
    border-radius: 16px 16px 0 0;
    padding: 20px;
}

.chart-title {
    color: #1e293b;
    font-weight: 600;
    font-size: 1.25rem;
    margin: 0;
}

/* Progress Bars */
.elegant-progress {
    height: 35px;
    background: linear-gradient(135deg, rgba(226, 232, 240, 0.5) 0%, rgba(241, 245, 249, 0.5) 100%);
    border-radius: 20px;
    border: 1px solid rgba(255,255,255,0.3);
    box-shadow: inset 0 2px 6px rgba(0,0,0,0.05);
}

.elegant-progress-bar {
    border-radius: 20px;
    font-weight: 700;
    text-shadow: 0 1px 2px rgba(0,0,0,0.2);
    display: flex;
    align-items: center;
    justify-content: center;
}

/* Score Items */
.elegant-score {
    padding: 15px;
    text-align: center;
    background: linear-gradient(135deg, rgba(255,255,255,0.9) 0%, rgba(248,250,252,0.9) 100%);
    border-radius: 12px;
    border: 1px solid rgba(255,255,255,0.3);
    box-shadow: 0 2px 8px rgba(0,0,0,0.05);
    transition: all 0.3s ease;
    backdrop-filter: blur(10px);
}

.elegant-score:hover {
    transform: translateY(-2px);
    box-shadow: 0 4px 15px rgba(0,0,0,0.1);
}

.score-number {
    font-size: 2.5rem;
    font-weight: 800;
    margin-bottom: 8px;
    text-shadow: 0 2px 4px rgba(0,0,0,0.1);
}

.score-label {
    font-size: 0.9rem;
    color: #64748b;
    font-weight: 600;
    margin-bottom: 5px;
}

.score-count {
    font-size: 0.85rem;
    color: #475569;
    font-weight: 500;
}

/* Badges */
.satisfaction-badge-clean {
    font-size: 1.1rem;
    font-weight: 600;
    padding: 8px 16px;
    border-radius: 20px;
    background: linear-gradient(135deg, rgba(255,255,255,0.9) 0%, rgba(248,250,252,0.9) 100%);
    border: 1px solid rgba(255,255,255,0.3);
    box-shadow: 0 2px 8px rgba(0,0,0,0.1);
    display: inline-flex;
    align-items: center;
    gap: 6px;
}

.elegant-badge {
    background: linear-gradient(135deg, rgba(59, 130, 246, 0.9) 0%, rgba(99, 102, 241, 0.9) 100%);
    color: white;
    border: 1px solid rgba(255,255,255,0.2);
    box-shadow: 0 2px 8px rgba(59, 130, 246, 0.3);
    font-weight: 600;
}

/* Summary Cards */
.elegant-summary-card {
    border-radius: 16px;
    overflow: hidden;
    box-shadow: 0 4px 20px rgba(0,0,0,0.08);
    transition: all 0.3s ease;
}

.elegant-summary-card:hover {
    transform: translateY(-3px);
    box-shadow: 0 8px 30px rgba(0,0,0,0.12);
}

.border-soft-info {
    border: 2px solid rgba(14, 165, 233, 0.2) !important;
}

.border-soft-warning {
    border: 2px solid rgba(245, 158, 11, 0.2) !important;
}

.soft-info-bg {
    background: linear-gradient(135deg, rgba(14, 165, 233, 0.05) 0%, rgba(56, 189, 248, 0.05) 100%);
}

.soft-warning-bg {
    background: linear-gradient(135deg, rgba(245, 158, 11, 0.05) 0%, rgba(251, 191, 36, 0.05) 100%);
}

.elegant-title {
    font-weight: 600;
    margin-bottom: 15px;
}

.elegant-number {
    font-weight: 800;
    font-size: 2.5rem;
    text-shadow: 0 2px 4px rgba(0,0,0,0.1);
}

.soft-description {
    font-size: 0.9rem;
    opacity: 0.8;
}

/* Category Cards */
.elegant-category-card {
    border-radius: 16px;
    border: 1px solid rgba(196, 181, 253, 0.2);
    box-shadow: 0 4px 20px rgba(124, 58, 237, 0.08);
}

.elegant-category-header {
    background: linear-gradient(135deg, rgba(196, 181, 253, 0.1) 0%, rgba(221, 214, 254, 0.1) 100%);
    border-bottom: 1px solid rgba(196, 181, 253, 0.1);
}

.category-title {
    color: #5b21b6;
    font-weight: 600;
}

.soft-category-bg {
    background: linear-gradient(135deg, rgba(255,255,255,0.9) 0%, rgba(250,245,255,0.9) 100%);
}

.elegant-category-item {
    padding: 20px;
    border-radius: 12px;
    background: linear-gradient(135deg, rgba(255,255,255,0.8) 0%, rgba(248,250,252,0.8) 100%);
    border: 1px solid rgba(255,255,255,0.3);
    box-shadow: 0 2px 8px rgba(0,0,0,0.04);
    transition: all 0.3s ease;
}

.elegant-category-item:hover {
    transform: translateX(5px);
    box-shadow: 0 4px 15px rgba(0,0,0,0.08);
}

.elegant-category-name {
    color: #374151;
    font-weight: 600;
}

.elegant-category-progress {
    height: 25px;
    background: linear-gradient(135deg, rgba(226, 232, 240, 0.5) 0%, rgba(241, 245, 249, 0.5) 100%);
    border-radius: 15px;
    border: 1px solid rgba(255,255,255,0.3);
}

.elegant-cat-progress {
    border-radius: 15px;
    font-weight: 700;
    font-size: 0.85rem;
    display: flex;
    align-items: center;
    justify-content: center;
}

.soft-detail {
    color: #64748b;
    opacity: 0.9;
}

.elegant-divider {
    border: none;
    height: 1px;
    background: linear-gradient(90deg, transparent 0%, rgba(148, 163, 184, 0.3) 50%, transparent 100%);
}

/* Recent & Stats Cards */
.elegant-recent-card, .elegant-stats-card {
    border-radius: 16px;
    box-shadow: 0 4px 20px rgba(0,0,0,0.06);
    border: 1px solid rgba(255,255,255,0.2);
}

.elegant-recent-header {
    background: linear-gradient(135deg, rgba(187, 247, 208, 0.3) 0%, rgba(209, 250, 229, 0.3) 100%);
    border-bottom: 1px solid rgba(34, 197, 94, 0.1);
}

.elegant-stats-header {
    background: linear-gradient(135deg, rgba(254, 215, 170, 0.3) 0%, rgba(254, 243, 199, 0.3) 100%);
    border-bottom: 1px solid rgba(245, 158, 11, 0.1);
}

.recent-title, .stats-title {
    color: #374151;
    font-weight: 600;
}

.soft-recent-bg {
    background: linear-gradient(135deg, rgba(255,255,255,0.9) 0%, rgba(240,253,244,0.9) 100%);
}

.soft-stats-bg {
    background: linear-gradient(135deg, rgba(255,255,255,0.9) 0%, rgba(255,251,235,0.9) 100%);
}

.elegant-recent-item {
    background: linear-gradient(135deg, rgba(255,255,255,0.8) 0%, rgba(248,250,252,0.8) 100%);
    border: 1px solid rgba(34, 197, 94, 0.1);
    border-radius: 12px;
    transition: all 0.3s ease;
}

.elegant-recent-item:hover {
    transform: translateX(5px);
    border-color: rgba(34, 197, 94, 0.2);
    box-shadow: 0 4px 15px rgba(34, 197, 94, 0.1);
}

.elegant-time {
    color: #059669;
    font-weight: 700;
}

.soft-details {
    color: #6b7280;
}

.elegant-success-badge {
    background: linear-gradient(135deg, rgba(34, 197, 94, 0.9) 0%, rgba(74, 222, 128, 0.9) 100%);
    color: white;
    border: none;
    box-shadow: 0 2px 8px rgba(34, 197, 94, 0.3);
}

.elegant-daily {
    padding: 10px 0;
}

.day-thai {
    color: #374151;
}

.day-eng {
    color: #9ca3af;
}

.elegant-warning-badge {
    background: linear-gradient(135deg, rgba(245, 158, 11, 0.9) 0%, rgba(251, 191, 36, 0.9) 100%);
    color: white;
    border: none;
    box-shadow: 0 2px 8px rgba(245, 158, 11, 0.3);
}

.elegant-daily-progress {
    height: 8px;
    background: linear-gradient(135deg, rgba(254, 243, 199, 0.5) 0%, rgba(255, 251, 235, 0.5) 100%);
    border-radius: 6px;
}

.elegant-daily-bar {
    border-radius: 6px;
}

/* Feedback Cards */
.elegant-feedback-card {
    border-radius: 16px;
    border: 1px solid rgba(252, 165, 165, 0.2);
    box-shadow: 0 4px 20px rgba(239, 68, 68, 0.06);
}

.elegant-feedback-header {
    background: linear-gradient(135deg, rgba(254, 215, 215, 0.3) 0%, rgba(254, 226, 226, 0.3) 100%);
    border-bottom: 1px solid rgba(239, 68, 68, 0.1);
}

.feedback-title {
    color: #991b1b;
    font-weight: 600;
}

.soft-feedback-bg {
    background: linear-gradient(135deg, rgba(255,255,255,0.9) 0%, rgba(255,245,245,0.9) 100%);
}

.elegant-comment {
    background: linear-gradient(135deg, rgba(255,255,255,0.8) 0%, rgba(254,242,242,0.8) 100%);
    border: 1px solid rgba(252, 165, 165, 0.1);
    border-radius: 12px;
    transition: all 0.3s ease;
}

.elegant-comment:hover {
    transform: translateX(5px);
    border-color: rgba(252, 165, 165, 0.2);
    box-shadow: 0 4px 15px rgba(239, 68, 68, 0.08);
}

.soft-date {
    color: #6b7280;
}

.elegant-info-badge {
    background: linear-gradient(135deg, rgba(14, 165, 233, 0.9) 0%, rgba(56, 189, 248, 0.9) 100%);
    color: white;
    border: none;
    box-shadow: 0 2px 8px rgba(14, 165, 233, 0.3);
}

.elegant-comment-text {
    color: #4b5563;
    line-height: 1.6;
    font-style: italic;
}

/* Soft Buttons */
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

.btn-soft-info {
    background: linear-gradient(135deg, rgba(14, 165, 233, 0.1) 0%, rgba(56, 189, 248, 0.1) 100%);
    border: 1px solid rgba(14, 165, 233, 0.2);
    color: #0284c7;
    box-shadow: 0 2px 8px rgba(14, 165, 233, 0.1);
}

.btn-soft-primary {
    background: linear-gradient(135deg, rgba(59, 130, 246, 0.1) 0%, rgba(99, 102, 241, 0.1) 100%);
    border: 1px solid rgba(59, 130, 246, 0.2);
    color: #2563eb;
    box-shadow: 0 2px 8px rgba(59, 130, 246, 0.1);
}

.btn-soft-warning {
    background: linear-gradient(135deg, rgba(245, 158, 11, 0.1) 0%, rgba(251, 191, 36, 0.1) 100%);
    border: 1px solid rgba(245, 158, 11, 0.2);
    color: #d97706;
    box-shadow: 0 2px 8px rgba(245, 158, 11, 0.1);
}

.btn-soft-danger {
    background: linear-gradient(135deg, rgba(239, 68, 68, 0.1) 0%, rgba(248, 113, 113, 0.1) 100%);
    border: 1px solid rgba(239, 68, 68, 0.2);
    color: #dc2626;
    box-shadow: 0 2px 8px rgba(239, 68, 68, 0.1);
}

.elegant-btn {
    border-radius: 12px;
    font-weight: 600;
    padding: 12px 20px;
    transition: all 0.3s ease;
    backdrop-filter: blur(10px);
}

.elegant-btn:hover {
    transform: translateY(-2px);
    box-shadow: 0 6px 20px rgba(0,0,0,0.1);
}

/* Text Utilities */
.soft-text {
    color: #8b9097;
}

.explanation-text {
    color: #6b7280;
    font-style: italic;
}

.text-orange {
    color: #ea580c !important;
}

/* Empty States */
.empty-state, .empty-recent, .empty-stats {
    opacity: 0.7;
}

/* Animation Enhancements */
.stat-card, .elegant-card, .elegant-summary-card, .elegant-category-item,
.elegant-recent-item, .elegant-comment {
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

/* Responsive Design */
@media (max-width: 768px) {
    .assessment-admin-container {
        padding: 10px;
    }
    
    .stat-card {
        padding: 15px;
        margin-bottom: 15px;
    }
    
    .score-number {
        font-size: 2rem;
    }
    
    .elegant-number {
        font-size: 2rem;
    }
    
    .elegant-category-item {
        padding: 15px;
    }
    
    .elegant-btn {
        padding: 10px 16px;
        font-size: 0.9rem;
    }
}
</style>


<style>
.badge.bg-secondary {
    font-size: 0.7rem;
    font-weight: 600;
}

.recent-response-item {
    transition: all 0.3s ease;
    border-left: 3px solid transparent;
}

.recent-response-item:hover {
    border-left-color: #007bff;
    box-shadow: 0 2px 8px rgba(0,123,255,0.1);
}

.response-details {
    font-size: 0.8rem;
    line-height: 1.3;
}

.text-success {
    color: #28a745 !important;
}

.text-info {
    color: #17a2b8 !important;
}

.badge.bg-primary {
    background-color: #007bff !important;
}

.badge.bg-success {
    background-color: #28a745 !important;
}

.badge.bg-info {
    background-color: #17a2b8 !important;
}
</style>


<!-- JavaScript for Assessment Reports -->
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Initialize tooltips
    var tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'));
    var tooltipList = tooltipTriggerList.map(function (tooltipTriggerEl) {
        return new bootstrap.Tooltip(tooltipTriggerEl);
    });
    
    // Auto refresh data every 5 minutes
    setInterval(refreshAssessmentData, 300000);
});

// Navigation Functions
function goToManagement() {
    // ตรวจสอบสิทธิ์ก่อนไป
    fetch('<?= site_url("System_reports/check_form_access_js") ?>')
        .then(response => response.json())
        .then(data => {
            if (data.can_access) {
                // มีสิทธิ์ - ไปหน้าจัดการฟอร์ม
                window.location.href = '<?= site_url("System_reports/assessment_form_management") ?>';
            } else {
                // ไม่มีสิทธิ์ - แสดงข้อความแจ้งเตือน
                showToast(data.message || 'ไม่มีสิทธิ์เข้าถึงระบบจัดการฟอร์ม', 'warning');
            }
        })
        .catch(error => {
            console.error('Error checking permissions:', error);
            showToast('เกิดข้อผิดพลาดในการตรวจสอบสิทธิ์', 'error');
        });
}

function confirmClearAssessmentData() {
    // ยืนยันการล้างข้อมูล
    if (confirm('⚠️ คำเตือน: การล้างข้อมูลการตอบแบบประเมินจะไม่สามารถกู้คืนได้\n\nคุณต้องการดำเนินการต่อหรือไม่?')) {
        if (confirm('กรุณายืนยันอีกครั้ง: ข้อมูลทั้งหมดจะถูกลบถาวร')) {
            clearAssessmentData();
        }
    }
}

function clearAssessmentData() {
    const btn = document.getElementById('clearDataBtn');
    const originalHtml = btn.innerHTML;
    
    btn.disabled = true;
    btn.innerHTML = '<i class="fas fa-spinner fa-spin me-1"></i>กำลังล้างข้อมูล...';
    
    fetch('<?= site_url("System_reports/clear_assessment_data") ?>', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-Requested-With': 'XMLHttpRequest'
        }
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            showToast('ล้างข้อมูลการตอบแบบประเมินเรียบร้อยแล้ว', 'success');
            // รีเฟรชหน้าหลังจากล้างข้อมูล
            setTimeout(() => {
                location.reload();
            }, 1500);
        } else {
            showToast(data.message || 'เกิดข้อผิดพลาดในการล้างข้อมูล', 'error');
        }
    })
    .catch(error => {
        console.error('Error clearing data:', error);
        showToast('เกิดข้อผิดพลาดในการเชื่อมต่อ', 'error');
    })
    .finally(() => {
        btn.disabled = false;
        btn.innerHTML = originalHtml;
    });
}

function checkAndGoToManagement() {
    goToManagement(); // ใช้ฟังก์ชันด้านบน
}

function showSettings() {
    window.location.href = '<?= site_url("System_reports/assessment_settings") ?>';
}

function viewAllComments() {
    window.open('<?= site_url("System_reports/assessment_comments") ?>', '_blank');
}

// Data Functions
function refreshAssessmentData() {
    const btn = document.getElementById('refreshBtn');
    const originalHtml = btn.innerHTML;
    
    btn.disabled = true;
    btn.innerHTML = '<i class="fas fa-spinner fa-spin me-1"></i>กำลังรีเฟรช...';
    
    // Simulate API call
    setTimeout(() => {
        showToast('รีเฟรชข้อมูลเรียบร้อยแล้ว', 'success');
        btn.disabled = false;
        btn.innerHTML = originalHtml;
        
        // Update last update time
        document.getElementById('lastUpdateDisplay').textContent = new Date().toLocaleTimeString('th-TH', {
            hour: '2-digit',
            minute: '2-digit',
            hour12: false
        }) + ' น.';
        
        // Optional: reload page for fresh data
        // location.reload();
    }, 2000);
}

function exportAssessmentReport() {
    showToast('กำลังเตรียมรายงานแบบประเมิน...', 'info');
    
    const previewUrl = '<?= site_url("System_reports/export_assessment_report") ?>';
    const previewWindow = window.open(previewUrl, '_blank', 'width=1200,height=800,scrollbars=yes,resizable=yes');
    
    if (previewWindow) {
        previewWindow.focus();
        showToast('เปิดหน้าตัวอย่างรายงานแล้ว', 'success');
    } else {
        showToast('ไม่สามารถเปิดหน้าตัวอย่างได้ กรุณาอนุญาต Pop-up', 'warning');
        window.location.href = previewUrl;
    }
}

// Toast notification function
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
<script>
function viewAllResponses() {
    // สามารถเพิ่ม modal หรือไปยังหน้าใหม่เพื่อดูการตอบทั้งหมด
    window.open('<?= site_url("System_reports/all_assessment_responses") ?>', '_blank');
}

// Function สำหรับ refresh ข้อมูลการตอบล่าสุด
function refreshRecentResponses() {
    fetch('<?= site_url("System_reports/api_get_recent_responses") ?>')
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                updateRecentResponsesList(data.responses);
                showToast('รีเฟรชข้อมูลการตอบล่าสุดเรียบร้อยแล้ว', 'success');
            }
        })
        .catch(error => {
            console.error('Error refreshing recent responses:', error);
            showToast('เกิดข้อผิดพลาดในการรีเฟรชข้อมูล', 'error');
        });
}

function updateRecentResponsesList(responses) {
    const container = document.getElementById('recentResponsesList');
    
    if (!responses || responses.length === 0) {
        container.innerHTML = `
            <div class="text-center text-muted py-3">
                <i class="fas fa-inbox fa-2x mb-2"></i>
                <p class="mb-0">ยังไม่มีการตอบแบบประเมิน</p>
                <small>จากหมวดหมู่ที่เปิดใช้งาน</small>
            </div>
        `;
        return;
    }
    
    let html = '';
    responses.forEach((response, index) => {
        const date = new Date(response.completed_at);
        const formattedDate = date.toLocaleDateString('th-TH', {
            day: '2-digit',
            month: '2-digit',
            year: 'numeric',
            hour: '2-digit',
            minute: '2-digit'
        });
        
        html += `
            <div class="d-flex justify-content-between align-items-center mb-2 p-2 bg-white rounded border recent-response-item">
                <div class="flex-grow-1">
                    <div class="d-flex align-items-center mb-1">
                        <span class="badge bg-secondary me-2">#${index + 1}</span>
                        <small class="text-muted">
                            <i class="fas fa-clock"></i>
                            ${formattedDate}
                        </small>
                    </div>
                    ${response.ip_address ? `
                    <small class="text-muted d-block">
                        <i class="fas fa-globe"></i>
                        ${response.ip_address}
                    </small>
                    ` : ''}
                    
                    <div class="mt-1 response-details">
                        ${response.scoring_answers > 0 ? `
                        <small class="text-success d-block">
                            <i class="fas fa-star"></i>
                            คะแนนประเมิน: ${response.scoring_answers} คำตอบ
                        </small>
                        ` : ''}
                        
                        ${response.feedback_answers > 0 ? `
                        <small class="text-info d-block">
                            <i class="fas fa-comment"></i>
                            ข้อเสนอแนะ: ${response.feedback_answers} คำตอบ
                        </small>
                        ` : ''}
                    </div>
                </div>
                
                <div class="text-end">
                    <span class="badge bg-primary d-block mb-1">
                        รวม ${response.answer_count} คำตอบ
                    </span>
                    
                    <div>
                        ${response.has_scoring ? `
                        <span class="badge bg-success me-1">
                            <i class="fas fa-star"></i> ประเมิน
                        </span>
                        ` : ''}
                        
                        ${response.has_feedback ? `
                        <span class="badge bg-info">
                            <i class="fas fa-comment"></i> ข้อเสนอแนะ
                        </span>
                        ` : ''}
                    </div>
                </div>
            </div>
        `;
    });
    
    // เพิ่มปุ่มดูเพิ่มเติม
    html += `
        <div class="text-center mt-3">
            <a href="<?= site_url('System_reports/assessment_comments') ?>" class="btn btn-outline-info btn-sm">
                <i class="fas fa-list me-1"></i>
                ดูข้อเสนอแนะทั้งหมด
            </a>
            <button type="button" class="btn btn-outline-primary btn-sm ms-2" onclick="viewAllResponses()">
                <i class="fas fa-users me-1"></i>
                ดูการตอบทั้งหมด
            </button>
        </div>
    `;
    
    container.innerHTML = html;
}
</script>
