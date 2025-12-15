<?php
// Flash Messages
if (!empty($success_message)): ?>
    <div class="alert alert-success alert-dismissible fade show" role="alert">
        <i class="fas fa-check-circle me-2"></i>
        <?= $success_message ?>
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
<?php endif;

if (!empty($error_message)): ?>
    <div class="alert alert-danger alert-dismissible fade show" role="alert">
        <i class="fas fa-exclamation-triangle me-2"></i>
        <?= $error_message ?>
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
<?php endif; ?>

<!-- Assessment Management Panel for System Admin -->
<div class="row mb-4">
    <div class="col-12">
        <div class="card shadow">
            <div class="card-header" style="background: linear-gradient(135deg, #a78bfa 0%, #c4b5fd 100%); color: #1e1b4b;">
                <div class="d-flex justify-content-between align-items-center">
                    <h5 class="mb-0">
                        <i class="fas fa-clipboard-check me-2"></i>
                        จัดการระบบแบบประเมินความพึงพอใจ
                    </h5>
                    <div class="d-flex flex-wrap gap-2 align-items-center">
    <!-- ปุ่มกลับ (แสดงเฉพาะในหน้าจัดการฟอร์ม) -->
    <?php if (isset($user_permissions) && $user_permissions['can_manage_form'] && $this->uri->segment(2) === 'assessment_form_management'): ?>
    <a href="<?= site_url('System_reports/assessment_admin') ?>" class="btn btn-outline-primary btn-sm">
        <i class="fas fa-arrow-left me-1"></i>
        กลับหน้ารายงาน
    </a>
    <span class="text-muted mx-1">|</span>
    <?php endif; ?>
    
    <!-- ปุ่มรีเฟรชข้อมูล -->
    <button type="button" class="btn btn-light btn-sm border" onclick="refreshAssessmentData()" id="refreshBtn">
        <i class="fas fa-sync-alt me-1"></i>
        รีเฟรชข้อมูล
    </button>
    
    <!-- ปุ่มดูแบบประเมิน -->
    <a href="<?= site_url('assessment') ?>" target="_blank" class="btn btn-outline-success btn-sm">
        <i class="fas fa-external-link-alt me-1"></i>
        ดูแบบประเมิน
    </a>
    
    <!-- ปุ่มตัวอย่าง -->
    <a href="<?= site_url('assessment/preview') ?>" target="_blank" class="btn btn-outline-info btn-sm">
        <i class="fas fa-eye me-1"></i>
        ตัวอย่าง
    </a>
    
    <!-- ปุ่มรายงาน -->
    <button type="button" class="btn btn-outline-secondary btn-sm" onclick="exportAssessmentReport()">
        <i class="fas fa-download me-1"></i>
        รายงาน
    </button>
    
    <!-- ปุ่มจัดการฟอร์ม (แสดงเฉพาะในหน้ารายงาน) -->
    <?php if (isset($user_permissions) && $user_permissions['can_manage_form'] && $this->uri->segment(2) === 'assessment_admin'): ?>
    <span class="text-muted mx-1">|</span>
    <a href="<?= site_url('System_reports/assessment_form_management') ?>" class="btn btn-outline-warning btn-sm">
        <i class="fas fa-cogs me-1"></i>
        จัดการฟอร์ม
    </a>
    <?php endif; ?>
</div>
					
					
                </div>
            </div>
            <div class="card-body">
                <!-- Enhanced Status Row -->
                <div class="row mb-4">
                    <div class="col-md-3 col-sm-6 mb-3">
                        <div class="stat-card">
                            <div class="stat-card-header">
                                <div class="stat-icon success">
                                    <i class="fas fa-poll"></i>
                                </div>
                                <div class="stat-change positive">
                                    <i class="fas fa-arrow-up"></i>
                                </div>
                            </div>
                            <div class="stat-info">
                                <div class="stat-label">จำนวนผู้ตอบ</div>
                                <div class="stat-value text-success" id="totalResponsesDisplay">
                                    <?= number_format($statistics['total_responses'] ?? 0) ?> คน
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <div class="col-md-3 col-sm-6 mb-3">
                        <div class="stat-card">
                            <div class="stat-card-header">
                                <div class="stat-icon primary">
                                    <i class="fas fa-calendar-day"></i>
                                </div>
                                <div class="stat-change positive">
                                    <i class="fas fa-plus"></i>
                                </div>
                            </div>
                            <div class="stat-info">
                                <div class="stat-label">วันนี้</div>
                                <div class="stat-value text-primary" id="todayResponsesDisplay">
                                    <?= number_format($statistics['today_responses'] ?? 0) ?> คน
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <div class="col-md-3 col-sm-6 mb-3">
                        <div class="stat-card">
                            <div class="stat-card-header">
                                <div class="stat-icon warning">
                                    <i class="fas fa-star"></i>
                                </div>
                                <div class="stat-change positive">
                                    <i class="fas fa-chart-line"></i>
                                </div>
                            </div>
                            <div class="stat-info">
                                <div class="stat-label">คะแนนเฉลี่ย</div>
                                <div class="stat-value text-warning" id="averageScoreDisplay">
                                    <?= number_format($statistics['average_score'] ?? 0, 2) ?>/5.00
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <div class="col-md-3 col-sm-6 mb-3">
                        <div class="stat-card">
                            <div class="stat-card-header">
                                <div class="stat-icon info">
                                    <i class="fas fa-question-circle"></i>
                                </div>
                                <div class="stat-change positive">
                                    <i class="fas fa-list"></i>
                                </div>
                            </div>
                            <div class="stat-info">
                                <div class="stat-label">จำนวนคำถาม</div>
                                <div class="stat-value text-info">
                                    <?= number_format($statistics['total_questions'] ?? 0) ?> ข้อ
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Quick Actions -->
                <div class="row mb-4">
                    <div class="col-12">
                        <div class="chart-card">
                            <div class="chart-header">
                                <h3 class="chart-title">การจัดการแบบประเมิน</h3>
                                <div class="chart-actions">
                                    <span class="btn-chart active">
                                        <i class="fas fa-cogs"></i> จัดการ
                                    </span>
                                </div>
                            </div>
                            
                            <!-- Action Buttons Grid -->
                            <div class="row g-3">
                                <div class="col-md-3 col-sm-6">
                                    <div class="action-card" onclick="manageCategories()" style="background: linear-gradient(135deg, #c7d2fe 0%, #ddd6fe 100%);">
                                        <div class="action-icon">
                                            <i class="fas fa-folder" style="color: #6366f1;"></i>
                                        </div>
                                        <div class="action-info">
                                            <div class="action-title" style="color: #4338ca;">จัดการหมวดหมู่</div>
                                            <div class="action-desc" style="color: #6366f1;">เพิ่ม แก้ไข ลบหมวดหมู่</div>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-3 col-sm-6">
                                    <div class="action-card" onclick="manageQuestions()" style="background: linear-gradient(135deg, #bbf7d0 0%, #d1fae5 100%);">
                                        <div class="action-icon">
                                            <i class="fas fa-question" style="color: #059669;"></i>
                                        </div>
                                        <div class="action-info">
                                            <div class="action-title" style="color: #065f46;">จัดการคำถาม</div>
                                            <div class="action-desc" style="color: #059669;">เพิ่ม แก้ไข ลบคำถาม</div>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-3 col-sm-6">
                                    <div class="action-card" onclick="viewReports()" style="background: linear-gradient(135deg, #fde68a 0%, #fef3c7 100%);">
                                        <div class="action-icon">
                                            <i class="fas fa-chart-bar" style="color: #d97706;"></i>
                                        </div>
                                        <div class="action-info">
                                            <div class="action-title" style="color: #92400e;">ดูรายงาน</div>
                                            <div class="action-desc" style="color: #d97706;">สถิติและวิเคราะห์</div>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-3 col-sm-6">
                                    <div class="action-card" onclick="manageSettings()" style="background: linear-gradient(135deg, #fed7d7 0%, #fbb6ce 100%);">
                                        <div class="action-icon">
                                            <i class="fas fa-cogs" style="color: #dc2626;"></i>
                                        </div>
                                        <div class="action-info">
                                            <div class="action-title" style="color: #991b1b;">ตั้งค่าระบบ</div>
                                            <div class="action-desc" style="color: #dc2626;">การตั้งค่าทั่วไป</div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Categories Management Section -->
                <div class="row mb-4" id="categoriesSection">
                    <div class="col-12">
                        <div class="card" style="border: 1px solid #c7d2fe;">
                            <div class="card-header" style="background: linear-gradient(135deg, #c7d2fe 0%, #ddd6fe 100%); color: #4338ca;">
                                <div class="d-flex justify-content-between align-items-center">
                                    <h6 class="mb-0">
                                        <i class="fas fa-folder me-2"></i>
                                        จัดการหมวดหมู่แบบประเมิน
                                    </h6>
                                    <button type="button" class="btn btn-primary btn-sm" onclick="addCategory()">
                                        <i class="fas fa-plus me-1"></i>
                                        เพิ่มหมวดหมู่
                                    </button>
                                </div>
                            </div>
                            <div class="card-body">
                                <div class="table-responsive">
                                    <table class="table table-hover" id="categoriesTable">
                                        <thead style="background: #f8fafc;">
                                            <tr>
                                                <th width="8%">ลำดับ</th>
                                                <th width="50%">ชื่อหมวดหมู่</th>
                                                <th width="15%">จำนวนคำถาม</th>
                                                <th width="12%">สถานะ</th>
                                                <th width="15%">จัดการ</th>
                                            </tr>
                                        </thead>
                                        <tbody id="categoriesTableBody">
                                            <?php if (!empty($categories)): ?>
                                                <?php foreach ($categories as $category): ?>
                                                    <tr data-category-id="<?= $category->id ?>">
                                                        <td>
                                                            <span class="badge bg-secondary"><?= $category->category_order ?></span>
                                                        </td>
                                                        <td>
                                                            <strong><?= htmlspecialchars($category->category_name) ?></strong>
                                                        </td>
                                                        <td>
                                                            <span class="badge bg-info">
                                                                <?= $category->question_count ?? 0 ?> คำถาม
                                                            </span>
                                                        </td>
                                                        <td>
                                                            <?php if ($category->is_active): ?>
                                                                <span class="badge bg-success">เปิดใช้งาน</span>
                                                            <?php else: ?>
                                                                <span class="badge bg-warning">ปิดใช้งาน</span>
                                                            <?php endif; ?>
                                                        </td>
                                                        <td>
                                                            <div class="btn-group btn-group-sm" role="group">
                                                                <button type="button" class="btn btn-outline-primary" 
                                                                        onclick="editCategory(<?= $category->id ?>)"
                                                                        title="แก้ไข">
                                                                    <i class="fas fa-edit"></i>
                                                                </button>
                                                                <button type="button" class="btn btn-outline-success" 
                                                                        onclick="manageQuestions(<?= $category->id ?>)"
                                                                        title="จัดการคำถาม">
                                                                    <i class="fas fa-question"></i>
                                                                </button>
                                                                <button type="button" class="btn btn-outline-danger" 
                                                                        onclick="deleteCategory(<?= $category->id ?>)"
                                                                        title="ลบ">
                                                                    <i class="fas fa-trash"></i>
                                                                </button>
                                                            </div>
                                                        </td>
                                                    </tr>
                                                <?php endforeach; ?>
                                            <?php else: ?>
                                                <tr>
                                                    <td colspan="5" class="text-center text-muted">
                                                        <i class="fas fa-folder-open"></i> ยังไม่มีหมวดหมู่
                                                    </td>
                                                </tr>
                                            <?php endif; ?>
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Questions Management Section -->
                <div class="row mb-4" id="questionsSection" style="display: none;">
                    <div class="col-12">
                        <div class="card" style="border: 1px solid #bbf7d0;">
                            <div class="card-header" style="background: linear-gradient(135deg, #bbf7d0 0%, #d1fae5 100%); color: #065f46;">
                                <div class="d-flex justify-content-between align-items-center">
                                    <h6 class="mb-0">
                                        <i class="fas fa-question me-2"></i>
                                        จัดการคำถาม: <span id="currentCategoryName">ทั้งหมด</span>
                                    </h6>
                                    <div class="btn-group btn-group-sm">
                                        <button type="button" class="btn btn-outline-secondary" onclick="backToCategories()">
                                            <i class="fas fa-arrow-left me-1"></i>
                                            กลับ
                                        </button>
                                        <button type="button" class="btn btn-success" onclick="addQuestion()" id="addQuestionBtn">
                                            <i class="fas fa-plus me-1"></i>
                                            เพิ่มคำถาม
                                        </button>
                                    </div>
                                </div>
                            </div>
                            <div class="card-body">
                                <div class="table-responsive">
                                    <table class="table table-hover" id="questionsTable">
                                        <thead style="background: #f8fafc;">
                                            <tr>
                                                <th width="8%">ลำดับ</th>
                                                <th width="45%">คำถาม</th>
                                                <th width="12%">ประเภท</th>
                                                <th width="10%">จำเป็น</th>
                                                <th width="10%">สถานะ</th>
                                                <th width="15%">จัดการ</th>
                                            </tr>
                                        </thead>
                                        <tbody id="questionsTableBody">
                                            <!-- จะถูกโหลดด้วย AJAX -->
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Reports Section -->
                <div class="row mb-4" id="reportsSection" style="display: none;">
                    <div class="col-12">
                        <div class="card" style="border: 1px solid #fde68a;">
                            <div class="card-header" style="background: linear-gradient(135deg, #fde68a 0%, #fef3c7 100%); color: #92400e;">
                                <div class="d-flex justify-content-between align-items-center">
                                    <h6 class="mb-0">
                                        <i class="fas fa-chart-bar me-2"></i>
                                        รายงานสถิติการประเมิน
                                    </h6>
                                    <button type="button" class="btn btn-outline-secondary btn-sm" onclick="backToMain()">
                                        <i class="fas fa-arrow-left me-1"></i>
                                        กลับ
                                    </button>
                                </div>
                            </div>
                            <div class="card-body">
                                <!-- สถิติแบบประเมิน -->
                                <div class="row mb-4">
                                    <div class="col-md-6">
                                        <div class="card bg-light">
                                            <div class="card-body">
                                                <h6 class="card-title">
                                                    <i class="fas fa-chart-pie text-warning"></i>
                                                    คะแนนเฉลี่ยแต่ละหมวด
                                                </h6>
                                                <div id="categoryScoresChart">
                                                    <?php if (!empty($statistics['categories'])): ?>
                                                        <?php foreach ($statistics['categories'] as $cat_id => $cat_data): ?>
                                                            <div class="mb-3">
                                                                <div class="d-flex justify-content-between mb-1">
                                                                    <span class="small fw-bold"><?= $cat_data['name'] ?></span>
                                                                    <span class="small text-primary"><?= number_format($cat_data['avg_score'], 2) ?>/5.00</span>
                                                                </div>
                                                                <div class="progress">
                                                                    <div class="progress-bar bg-warning" 
                                                                         style="width: <?= ($cat_data['avg_score'] / 5) * 100 ?>%">
                                                                    </div>
                                                                </div>
                                                            </div>
                                                        <?php endforeach; ?>
                                                    <?php else: ?>
                                                        <p class="text-muted text-center">ยังไม่มีข้อมูลการประเมิน</p>
                                                    <?php endif; ?>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="card bg-light">
                                            <div class="card-body">
                                                <h6 class="card-title">
                                                    <i class="fas fa-calendar-alt text-info"></i>
                                                    ข้อมูลการตอบล่าสุด
                                                </h6>
                                                <div id="recentResponsesList">
                                                    <?php if (!empty($recent_responses)): ?>
                                                        <?php foreach ($recent_responses as $response): ?>
                                                            <div class="d-flex justify-content-between align-items-center mb-2 p-2 bg-white rounded">
                                                                <div>
                                                                    <small class="text-muted">
                                                                        <i class="fas fa-clock"></i>
                                                                        <?= date('d/m/Y H:i', strtotime($response->completed_at)) ?>
                                                                    </small>
                                                                </div>
                                                                <div>
                                                                    <span class="badge bg-primary"><?= $response->answer_count ?> คำตอบ</span>
                                                                </div>
                                                            </div>
                                                        <?php endforeach; ?>
                                                    <?php else: ?>
                                                        <p class="text-muted text-center">ยังไม่มีการตอบแบบประเมิน</p>
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

                <!-- Settings Section -->
                <!-- Settings Section - ใช้งานได้จริงตามฐานข้อมูลที่มี -->
<div class="row mb-4" id="settingsSection" style="display: none;">
    <div class="col-12">
        <div class="card" style="border: 1px solid #fed7d7;">
            <div class="card-header" style="background: linear-gradient(135deg, #fed7d7 0%, #fbb6ce 100%); color: #991b1b;">
                <div class="d-flex justify-content-between align-items-center">
                    <h6 class="mb-0">
                        <i class="fas fa-cogs me-2"></i>
                        การตั้งค่าระบบแบบประเมิน
                    </h6>
                    <button type="button" class="btn btn-outline-secondary btn-sm" onclick="backToMain()">
                        <i class="fas fa-arrow-left me-1"></i>
                        กลับ
                    </button>
                </div>
            </div>
            <div class="card-body">
                <form id="settingsForm">
                    


                    <!-- การตั้งค่าการทำงานของระบบ -->
                    <div class="settings-group mb-4">
                        <h6 class="settings-group-title">
                            <i class="fas fa-cog me-2"></i>
                            การตั้งค่าการทำงานของระบบ
                        </h6>
                        <div class="settings-group-description">
                            กำหนดพฤติกรรมและการทำงานของแบบประเมิน
                        </div>
                        
                        <div class="row">
                            <div class="col-md-4">
                                <div class="mb-3">
                                    <div class="form-check form-switch">
                                        <input class="form-check-input" type="checkbox" name="show_progress_bar" 
                                               id="showProgressBar" <?= (($settings['show_progress_bar'] ?? '1') == '1') ? 'checked' : '' ?>>
                                        <label class="form-check-label fw-bold" for="showProgressBar">
                                            <i class="fas fa-tasks me-1"></i>
                                            แสดงแถบความคืบหน้า
                                            <span class="setting-help" data-bs-toggle="tooltip" title="แสดงแถบแสดงความคืบหน้าในการตอบแบบประเมิน">
                                                <i class="fas fa-question-circle text-muted"></i>
                                            </span>
                                        </label>
                                    </div>
                                    <small class="form-text text-muted">แสดงเปอร์เซ็นต์ความคืบหน้าให้ผู้ตอบเห็น</small>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="mb-3">
                                    <div class="form-check form-switch">
                                        <input class="form-check-input" type="checkbox" name="allow_multiple_submissions" 
                                               id="allowMultipleSubmissions" <?= (($settings['allow_multiple_submissions'] ?? '0') == '1') ? 'checked' : '' ?>>
                                        <label class="form-check-label fw-bold" for="allowMultipleSubmissions">
                                            <i class="fas fa-redo me-1"></i>
                                            อนุญาตส่งหลายครั้ง
                                            <span class="setting-help" data-bs-toggle="tooltip" title="อนุญาตให้ส่งแบบประเมินหลายครั้งจากเครื่องเดียวกัน">
                                                <i class="fas fa-question-circle text-muted"></i>
                                            </span>
                                        </label>
                                    </div>
                                    <small class="form-text text-muted">อนุญาตให้ตอบซ้ำจาก IP/เครื่องเดียวกันในวันเดียวกัน</small>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="mb-3">
                                    <label class="form-label fw-bold">
                                        <i class="fas fa-tachometer-alt me-1"></i>
                                        จำกัดการส่งต่อ IP/เครื่อง
                                        <span class="setting-help" data-bs-toggle="tooltip" title="จำนวนครั้งสูงสุดที่อนุญาตให้ส่งต่อ IP หรือ Browser Fingerprint ต่อวัน">
                                            <i class="fas fa-question-circle text-muted"></i>
                                        </span>
                                    </label>
                                    <input type="number" class="form-control" name="max_submissions_per_ip" 
                                           min="1" max="50" value="<?= $settings['max_submissions_per_ip'] ?? '5' ?>">
                                    <small class="form-text text-muted">
                                        หากเปิดให้ส่งหลายครั้ง จำกัดไม่เกิน <strong id="maxCountDisplay"><?= $settings['max_submissions_per_ip'] ?? '5' ?></strong> ครั้งต่อวัน
                                    </small>
                                </div>
                            </div>
                        </div>
                        
                        <div class="row">
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label class="form-label fw-bold">
                                        <i class="fas fa-shield-check me-1"></i>
                                        วิธีการป้องกันการส่งซ้ำ
                                        <span class="setting-help" data-bs-toggle="tooltip" title="เลือกวิธีตรวจสอบและป้องกันการส่งแบบประเมินซ้ำ">
                                            <i class="fas fa-question-circle text-muted"></i>
                                        </span>
                                    </label>
                                    <select class="form-select" name="duplicate_check_method">
                                        <option value="ip" <?= (($settings['duplicate_check_method'] ?? 'fingerprint') == 'ip') ? 'selected' : '' ?>>
                                            เก็บ IP Address เท่านั้น
                                        </option>
                                        <option value="fingerprint" <?= (($settings['duplicate_check_method'] ?? 'fingerprint') == 'fingerprint') ? 'selected' : '' ?>>
                                            เก็บ Browser Fingerprint + IP (แนะนำ)
                                        </option>
                                        <option value="both" <?= (($settings['duplicate_check_method'] ?? 'fingerprint') == 'both') ? 'selected' : '' ?>>
                                            เก็บทั้ง IP และ Browser Fingerprint แยกกัน
                                        </option>
                                        <option value="session" <?= (($settings['duplicate_check_method'] ?? 'fingerprint') == 'session') ? 'selected' : '' ?>>
                                            ตรวจสอบจาก Session เท่านั้น
                                        </option>
                                        <option value="disabled" <?= (($settings['duplicate_check_method'] ?? 'fingerprint') == 'disabled') ? 'selected' : '' ?>>
                                            ปิดการตรวจสอบ (อนุญาตส่งไม่จำกัด)
                                        </option>
                                    </select>
                                    <small class="form-text text-muted">
                                        <strong>Browser Fingerprint</strong> ระบุเครื่องได้แม่นยำกว่า IP Address ที่อาจซ้ำกันได้
                                    </small>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label class="form-label fw-bold">
                                        <i class="fas fa-clock me-1"></i>
                                        ช่วงเวลาตรวจสอบการส่งซ้ำ
                                        <span class="setting-help" data-bs-toggle="tooltip" title="ระยะเวลาที่จะตรวจสอบการส่งซ้ำย้อนหลัง">
                                            <i class="fas fa-question-circle text-muted"></i>
                                        </span>
                                    </label>
                                    <select class="form-select" name="check_period_hours">
                                        <option value="1" <?= (($settings['check_period_hours'] ?? '24') == '1') ? 'selected' : '' ?>>1 ชั่วโมงล่าสุด</option>
                                        <option value="6" <?= (($settings['check_period_hours'] ?? '24') == '6') ? 'selected' : '' ?>>6 ชั่วโมงล่าสุด</option>
                                        <option value="12" <?= (($settings['check_period_hours'] ?? '24') == '12') ? 'selected' : '' ?>>12 ชั่วโมงล่าสุด</option>
                                        <option value="24" <?= (($settings['check_period_hours'] ?? '24') == '24') ? 'selected' : '' ?>>1 วันล่าสุด (24 ชม.)</option>
                                        <option value="48" <?= (($settings['check_period_hours'] ?? '24') == '48') ? 'selected' : '' ?>>2 วันล่าสุด (48 ชม.)</option>
                                        <option value="72" <?= (($settings['check_period_hours'] ?? '24') == '72') ? 'selected' : '' ?>>3 วันล่าสุด (72 ชม.)</option>
                                        <option value="168" <?= (($settings['check_period_hours'] ?? '24') == '168') ? 'selected' : '' ?>>1 สัปดาห์ล่าสุด (168 ชม.)</option>
                                    </select>
                                    <small class="form-text text-muted">
                                        ระบบจะตรวจสอบการส่งซ้ำในช่วงเวลานี้ <br>
                                        <strong>แนะนำ:</strong> 1 วัน สำหรับการประเมินทั่วไป
                                    </small>
                                </div>
                            </div>
                        </div>
                    </div>



                    <!-- การตั้งค่าขั้นสูง (Settings ที่มีอยู่แล้ว) -->
                    <div class="settings-group mb-4">
                        <h6 class="settings-group-title">
                            <i class="fas fa-cogs me-2"></i>
                            การตั้งค่าขั้นสูง
                        </h6>
                        <div class="settings-group-description">
                            การตั้งค่าเพิ่มเติมจากฐานข้อมูลที่มีอยู่
                        </div>
                        
                        <div class="row">
                            <?php if (isset($settings['show_question_numbers'])): ?>
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <div class="form-check form-switch">
                                        <input class="form-check-input" type="checkbox" name="show_question_numbers" 
                                               id="showQuestionNumbers" <?= (($settings['show_question_numbers'] ?? 'on') == 'on') ? 'checked' : '' ?>>
                                        <label class="form-check-label fw-bold" for="showQuestionNumbers">
                                            <i class="fas fa-list-ol me-1"></i>
                                            แสดงหมายเลขคำถาม
                                        </label>
                                    </div>
                                    <small class="form-text text-muted">แสดงหมายเลข 1, 2, 3... หน้าคำถาม</small>
                                </div>
                            </div>
                            <?php endif; ?>
                            
                            <?php if (isset($settings['session_timeout_minutes'])): ?>
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label class="form-label fw-bold">
                                        <i class="fas fa-clock me-1"></i>
                                        Timeout Session (นาที)
                                    </label>
                                    <input type="number" class="form-control" name="session_timeout_minutes" 
                                           min="5" max="120" value="<?= $settings['session_timeout_minutes'] ?? '30' ?>">
                                    <small class="form-text text-muted">ระยะเวลาที่ session จะหมดอายุ</small>
                                </div>
                            </div>
                            <?php endif; ?>
                        </div>
                        
                        <div class="row">
                            <?php if (isset($settings['enable_analytics'])): ?>
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <div class="form-check form-switch">
                                        <input class="form-check-input" type="checkbox" name="enable_analytics" 
                                               id="enableAnalytics" <?= (($settings['enable_analytics'] ?? 'on') == 'on') ? 'checked' : '' ?>>
                                        <label class="form-check-label fw-bold" for="enableAnalytics">
                                            <i class="fas fa-chart-line me-1"></i>
                                            เปิดการวิเคราะห์ข้อมูล
                                        </label>
                                    </div>
                                    <small class="form-text text-muted">เก็บข้อมูลสำหรับการวิเคราะห์</small>
                                </div>
                            </div>
                            <?php endif; ?>
                            
                            <?php if (isset($settings['store_ip_address'])): ?>
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <div class="form-check form-switch">
                                        <input class="form-check-input" type="checkbox" name="store_ip_address" 
                                               id="storeIpAddress" <?= (($settings['store_ip_address'] ?? 'on') == 'on') ? 'checked' : '' ?>>
                                        <label class="form-check-label fw-bold" for="storeIpAddress">
                                            <i class="fas fa-map-marker-alt me-1"></i>
                                            เก็บ IP Address
                                        </label>
                                    </div>
                                    <small class="form-text text-muted">บันทึก IP Address ของผู้ตอบ</small>
                                </div>
                            </div>
                            <?php endif; ?>
                        </div>
                        

                    </div>

                    <!-- ปุ่มบันทึก -->
                    <div class="text-end">
                        <button type="button" class="btn btn-secondary me-2" onclick="resetSettings()">
                            <i class="fas fa-undo me-1"></i>
                            รีเซ็ตเป็นค่าเริ่มต้น
                        </button>
                        <button type="button" class="btn btn-primary" onclick="saveSettings()">
                            <i class="fas fa-save me-1"></i>
                            บันทึกการตั้งค่า
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<!-- เพิ่ม CSS สำหรับ Settings -->
<style>
.settings-group {
    background: #f8f9fa;
    border-radius: 8px;
    padding: 1.5rem;
    border-left: 4px solid #6366f1;
}

.settings-group-title {
    color: #374151;
    font-weight: 600;
    margin-bottom: 0.5rem;
    font-size: 1.1rem;
}

.settings-group-description {
    color: #6b7280;
    font-size: 0.9rem;
    margin-bottom: 1rem;
    font-style: italic;
}

.setting-help {
    cursor: help;
    margin-left: 0.25rem;
}

.form-label.fw-bold {
    color: #374151;
    display: flex;
    align-items: center;
}

.form-text.text-muted {
    font-size: 0.8rem;
    margin-top: 0.25rem;
}

.form-control:focus,
.form-select:focus {
    border-color: #6366f1;
    box-shadow: 0 0 0 0.2rem rgba(99, 102, 241, 0.25);
}

.form-check-input:checked {
    background-color: #6366f1;
    border-color: #6366f1;
}

.font-monospace {
    font-family: 'SFMono-Regular', 'Menlo', 'Monaco', 'Consolas', 'Liberation Mono', 'Courier New', monospace;
    font-size: 0.9rem;
}

/* Responsive adjustments */
@media (max-width: 768px) {
    .settings-group {
        padding: 1rem;
    }
    
    .form-label.fw-bold {
        font-size: 0.9rem;
    }
}
</style>

<!-- เพิ่ม JavaScript สำหรับ Settings -->
<script>
// Initialize tooltips
document.addEventListener('DOMContentLoaded', function() {
    // Initialize Bootstrap tooltips
    if (typeof bootstrap !== 'undefined' && bootstrap.Tooltip) {
        var tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'));
        var tooltipList = tooltipTriggerList.map(function (tooltipTriggerEl) {
            return new bootstrap.Tooltip(tooltipTriggerEl);
        });
    }
});

// Reset settings to default
function resetSettings() {
    if (confirm('ต้องการรีเซ็ตการตั้งค่าทั้งหมดเป็นค่าเริ่มต้นหรือไม่?')) {
        // Reset form to default values
        const form = document.getElementById('settingsForm');
        if (form) {
            // Reset checkboxes
            const showProgressBar = form.querySelector('#showProgressBar');
            if (showProgressBar) showProgressBar.checked = true;
            
            const allowMultiple = form.querySelector('#allowMultipleSubmissions');
            if (allowMultiple) allowMultiple.checked = false;
            
            // Reset other fields
            const maxSubmissions = form.querySelector('input[name="max_submissions_per_ip"]');
            if (maxSubmissions) maxSubmissions.value = '5';
            
            const checkPeriod = form.querySelector('select[name="check_period_hours"]');
            if (checkPeriod) checkPeriod.value = '24';
            
            const duplicateCheck = form.querySelector('select[name="duplicate_check_method"]');
            if (duplicateCheck) duplicateCheck.value = 'fingerprint';
            
            // Update display
            const maxCountDisplay = document.getElementById('maxCountDisplay');
            if (maxCountDisplay) maxCountDisplay.textContent = '5';
        }
        
        showToast('รีเซ็ตการตั้งค่าเรียบร้อยแล้ว', 'info');
    }
}

// Enhanced saveSettings function
function saveSettings() {
    const form = document.getElementById('settingsForm');
    if (!form) {
        showToast('ไม่พบฟอร์ม', 'error');
        return;
    }
    
    const formData = new FormData(form);
    
    // Convert checkboxes to proper values
    const checkboxes = ['show_progress_bar', 'allow_multiple_submissions', 'show_question_numbers', 'enable_analytics', 'store_ip_address'];
    
    checkboxes.forEach(checkboxName => {
        const checkbox = form.querySelector(`input[name="${checkboxName}"]`);
        if (checkbox) {
            if (checkbox.type === 'checkbox') {
                formData.set(checkboxName, checkbox.checked ? '1' : '0');
            }
        }
    });
    
    const baseUrl = document.querySelector('base')?.href || window.location.origin + '/';
    
    fetch(`${baseUrl}System_reports/api_save_settings_management`, {
        method: 'POST',
        headers: { 'X-Requested-With': 'XMLHttpRequest' },
        body: formData
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            showToast('บันทึกการตั้งค่าเรียบร้อยแล้ว', 'success');
        } else {
            showToast(data.message || 'เกิดข้อผิดพลาด', 'error');
        }
    })
    .catch(error => {
        console.error('Error:', error);
        showToast('เกิดข้อผิดพลาดในการบันทึก', 'error');
    });
}

// Update max count display when input changes
document.addEventListener('DOMContentLoaded', function() {
    const maxSubmissionsInput = document.querySelector('input[name="max_submissions_per_ip"]');
    const maxCountDisplay = document.getElementById('maxCountDisplay');
    
    if (maxSubmissionsInput && maxCountDisplay) {
        maxSubmissionsInput.addEventListener('input', function() {
            maxCountDisplay.textContent = this.value;
        });
    }
});
</script>
				
				
            </div>
        </div>
    </div>
</div>

<!-- Modal เพิ่ม/แก้ไขหมวดหมู่ -->
<div class="modal fade" id="categoryModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content border-0 shadow">
            <div class="modal-header" style="background: linear-gradient(135deg, #6366f1 0%, #8b5cf6 100%); color: white;">
                <h5 class="modal-title" id="categoryModalTitle">
                    <i class="fas fa-folder me-2"></i>
                    เพิ่มหมวดหมู่ใหม่
                </h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <form id="categoryForm">
                    <input type="hidden" id="categoryId" name="category_id">
                    
                    <!-- ชื่อหมวดหมู่ -->
                    <div class="mb-3">
                        <label for="categoryName" class="form-label fw-bold">
                            <i class="fas fa-tag me-1"></i>
                            ชื่อหมวดหมู่ <span class="text-danger">*</span>
                        </label>
                        <input type="text" class="form-control" id="categoryName" name="category_name" 
                               placeholder="เช่น การให้บริการ หรือ ข้อมูลทั่วไปของผู้ตอบ" required>
                        <small class="form-text text-muted">
                            ระบุชื่อหมวดหมู่ที่ต้องการสร้าง
                        </small>
                    </div>
                    
                    <div class="row">
                        <!-- ลำดับการแสดง -->
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="categoryOrder" class="form-label fw-bold">
                                    <i class="fas fa-sort-numeric-up me-1"></i>
                                    ลำดับการแสดง <span class="text-danger">*</span>
                                </label>
                                <input type="number" class="form-control" id="categoryOrder" name="category_order" 
                                       min="1" value="1" required>
                                <small class="form-text text-muted">
                                    ลำดับที่จะแสดงในแบบประเมิน
                                </small>
                            </div>
                        </div>
                        
                        <!-- สถานะเปิด/ปิดใช้งาน -->
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="form-label fw-bold">
                                    <i class="fas fa-toggle-on me-1"></i>
                                    สถานะการใช้งาน
                                </label>
                                <div class="form-check form-switch">
                                    <input class="form-check-input" type="checkbox" id="categoryActive" 
                                           name="is_active" checked>
                                    <label class="form-check-label" for="categoryActive">
                                        เปิดใช้งานหมวดหมู่นี้
                                    </label>
                                </div>
                                <small class="form-text text-muted">
                                    ปิด = ไม่แสดงในแบบประเมิน
                                </small>
                            </div>
                        </div>
                    </div>
                    
                    <!-- การนำไปคำนวณคะแนน (ฟิลด์ใหม่) -->
                    <div class="mb-3">
                        <div class="card border-primary" style="background-color: #f8f9ff;">
                            <div class="card-header bg-primary text-white py-2">
                                <h6 class="mb-0">
                                    <i class="fas fa-calculator me-2"></i>
                                    การนำไปคำนวณคะแนน
                                </h6>
                            </div>
                            <div class="card-body py-3">
                                <div class="form-check form-switch">
                                    <input class="form-check-input" type="checkbox" id="categoryScoring" 
                                           name="is_scoring" checked>
                                    <label class="form-check-label fw-bold" for="categoryScoring">
                                        <i class="fas fa-chart-line me-1"></i>
                                        นำไปคำนวณคะแนนในรายงาน
                                    </label>
                                </div>
                                
                                <!-- คำอธิบายแบบแยกประเภท -->
                                <div class="mt-2">
                                    <div class="row">
                                        <div class="col-md-6">
                                            <div class="scoring-option scoring-yes" id="scoringYesInfo">
                                                <h6 class="text-success">
                                                    <i class="fas fa-check-circle"></i> 
                                                    เปิด = นำไปคำนวณ
                                                </h6>
                                                <ul class="small text-success mb-0">
                                                    <li>แสดงในรายงานสถิติ</li>
                                                    <li>นำไปคำนวณคะแนนเฉลี่ย</li>
                                                    <li>เหมาะสำหรับการประเมินความพึงพอใจ</li>
                                                </ul>
                                                <div class="mt-2">
                                                    <span class="badge bg-success">เช่น: การให้บริการ, สถานที่</span>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-md-6">
                                            <div class="scoring-option scoring-no" id="scoringNoInfo" style="display: none;">
                                                <h6 class="text-warning">
                                                    <i class="fas fa-times-circle"></i> 
                                                    ปิด = ไม่นำไปคำนวณ
                                                </h6>
                                                <ul class="small text-warning mb-0">
                                                    <li>ไม่แสดงในรายงานสถิติ</li>
                                                    <li>ไม่นำไปคำนวณคะแนน</li>
                                                    <li>เหมาะสำหรับข้อมูลทั่วไป</li>
                                                </ul>
                                                <div class="mt-2">
                                                    <span class="badge bg-warning text-dark">เช่น: ข้อมูลผู้ตอบ, ข้อเสนอแนะ</span>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                
                                <!-- คำแนะนำ -->
                                <div class="alert alert-info mt-3 mb-0" role="alert">
                                    <i class="fas fa-lightbulb me-2"></i>
                                    <strong>คำแนะนำ:</strong> 
                                    หมวดที่มีคำถามประเภท Radio (เลือกคะแนน 1-5) ควรเปิดการคำนวณ 
                                    ส่วนหมวดที่เป็น Textarea (ข้อความ) หรือข้อมูลทั่วไปควรปิดการคำนวณ
                                </div>
                            </div>
                        </div>
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-light border" data-bs-dismiss="modal">
                    <i class="fas fa-times me-1"></i>ยกเลิก
                </button>
                <button type="button" class="btn btn-primary" onclick="saveCategory()">
                    <i class="fas fa-save me-1"></i>บันทึก
                </button>
            </div>
        </div>
    </div>
</div>

<!-- Modal เพิ่ม/แก้ไขคำถาม -->
<div class="modal fade" id="questionModal" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content border-0 shadow">
            <div class="modal-header" style="background: linear-gradient(135deg, #059669 0%, #10b981 100%); color: white;">
                <h5 class="modal-title" id="questionModalTitle">
                    <i class="fas fa-question me-2"></i>
                    เพิ่มคำถามใหม่
                </h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
                                        <div class="modal-body">
                <form id="questionForm">
                    <input type="hidden" id="questionId" name="question_id">
                    <input type="hidden" id="questionCategoryId" name="category_id">
                    
                    <div class="mb-3">
                        <label for="questionText" class="form-label fw-bold">
                            <i class="fas fa-edit me-1"></i>
                            คำถาม
                        </label>
                        <textarea class="form-control" id="questionText" name="question_text" 
                                  rows="3" placeholder="พิมพ์คำถามที่ต้องการ..." required></textarea>
                    </div>
                    
                    <div class="row">
                        <div class="col-md-4">
                            <div class="mb-3">
                                <label for="questionOrder" class="form-label fw-bold">
                                    <i class="fas fa-sort-numeric-up me-1"></i>
                                    ลำดับ
                                </label>
                                <input type="number" class="form-control" id="questionOrder" name="question_order" 
                                       min="1" value="1" required>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="mb-3">
                                <label for="questionType" class="form-label fw-bold">
                                    <i class="fas fa-list me-1"></i>
                                    ประเภทคำถาม
                                </label>
                                <select class="form-select" id="questionType" name="question_type" required onchange="toggleOptionsSection()">
                                    <option value="radio">เลือกตัวเลือก (Radio)</option>
                                    <option value="textarea">ข้อความยาว (Textarea)</option>
                                </select>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="mb-3">
                                <label class="form-label fw-bold">
                                    <i class="fas fa-asterisk me-1"></i>
                                    สถานะ
                                </label>
                                <div class="form-check form-switch">
                                    <input class="form-check-input" type="checkbox" id="questionRequired" 
                                           name="is_required" checked>
                                    <label class="form-check-label" for="questionRequired">
                                        คำถามจำเป็น
                                    </label>
                                </div>
                                <div class="form-check form-switch">
                                    <input class="form-check-input" type="checkbox" id="questionActive" 
                                           name="is_active" checked>
                                    <label class="form-check-label" for="questionActive">
                                        เปิดใช้งาน
                                    </label>
                                </div>
                            </div>
                        </div>
                    </div>
                </form>
                
                <!-- Options Management Section -->
                <div id="optionsSection" style="display: none;">
                    <hr>
                    <div class="d-flex justify-content-between align-items-center mb-3">
                        <h6 class="mb-0">
                            <i class="fas fa-list-ul me-2"></i>
                            จัดการตัวเลือก (Options)
                        </h6>
                        <button type="button" class="btn btn-success btn-sm" onclick="addOption()">
                            <i class="fas fa-plus me-1"></i>
                            เพิ่มตัวเลือก
                        </button>
                    </div>
                    
                    <div class="table-responsive">
                        <table class="table table-sm" id="optionsTable">
                            <thead style="background: #f8f9fa;">
                                <tr>
                                    <th width="10%">ลำดับ</th>
                                    <th width="40%">ข้อความ</th>
                                    <th width="25%">ค่า (Value)</th>
                                    <th width="10%">สถานะ</th>
                                    <th width="15%">จัดการ</th>
                                </tr>
                            </thead>
                            <tbody id="optionsTableBody">
                                <!-- จะถูกโหลดด้วย AJAX -->
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-light border" data-bs-dismiss="modal">
                    <i class="fas fa-times me-1"></i>ยกเลิก
                </button>
                <button type="button" class="btn btn-success" onclick="saveQuestion()">
                    <i class="fas fa-save me-1"></i>บันทึก
                </button>
            </div>
        </div>
    </div>
</div>

<!-- Modal เพิ่ม/แก้ไขตัวเลือก -->
<div class="modal fade" id="optionModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content border-0 shadow">
            <div class="modal-header" style="background: linear-gradient(135deg, #f59e0b 0%, #f97316 100%); color: white;">
                <h5 class="modal-title" id="optionModalTitle">
                    <i class="fas fa-list-ul me-2"></i>
                    เพิ่มตัวเลือกใหม่
                </h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <form id="optionForm">
                    <input type="hidden" id="optionId" name="option_id">
                    <input type="hidden" id="optionQuestionId" name="question_id">
                    
                    <div class="mb-3">
                        <label for="optionText" class="form-label fw-bold">
                            <i class="fas fa-edit me-1"></i>
                            ข้อความตัวเลือก
                        </label>
                        <input type="text" class="form-control" id="optionText" name="option_text" 
                               placeholder="เช่น ดีมาก (5 คะแนน)" required>
                    </div>
                    
                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="optionValue" class="form-label fw-bold">
                                    <i class="fas fa-tag me-1"></i>
                                    ค่า (Value)
                                </label>
                                <input type="text" class="form-control" id="optionValue" name="option_value" 
                                       placeholder="เช่น 5">
                                <small class="text-muted">หากไม่กรอก จะใช้ข้อความตัวเลือกแทน</small>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="optionOrder" class="form-label fw-bold">
                                    <i class="fas fa-sort-numeric-up me-1"></i>
                                    ลำดับการแสดง
                                </label>
                                <input type="number" class="form-control" id="optionOrder" name="option_order" 
                                       min="1" value="1" required>
                            </div>
                        </div>
                    </div>
                    
                    <div class="form-check form-switch">
                        <input class="form-check-input" type="checkbox" id="optionActive" 
                               name="is_active" checked>
                        <label class="form-check-label fw-bold" for="optionActive">
                            <i class="fas fa-toggle-on me-1"></i>
                            เปิดใช้งาน
                        </label>
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-light border" data-bs-dismiss="modal">
                    <i class="fas fa-times me-1"></i>ยกเลิก
                </button>
                <button type="button" class="btn btn-warning" onclick="saveOption()">
                    <i class="fas fa-save me-1"></i>บันทึก
                </button>
            </div>
        </div>
    </div>
</div>

<!-- JavaScript for Assessment Management -->
<script>
// JavaScript for Assessment Management - แก้ไขแล้ว

// Current state variables
let currentSection = 'main';
let currentCategoryId = null;
let currentQuestionId = null;

// Initialize page
document.addEventListener('DOMContentLoaded', function() {
    showSection('main');
    initializeEventListeners();
});

// Section Management
function showSection(section) {
    // Hide all sections
    const sections = ['categoriesSection', 'questionsSection', 'reportsSection', 'settingsSection'];
    sections.forEach(sectionId => {
        const element = document.getElementById(sectionId);
        if (element) {
            element.style.display = 'none';
        }
    });
    
    // Show selected section
    currentSection = section;
    
    switch(section) {
        case 'categories':
            const categoriesEl = document.getElementById('categoriesSection');
            if (categoriesEl) categoriesEl.style.display = 'block';
            break;
        case 'questions':
            const questionsEl = document.getElementById('questionsSection');
            if (questionsEl) questionsEl.style.display = 'block';
            break;
        case 'reports':
            const reportsEl = document.getElementById('reportsSection');
            if (reportsEl) reportsEl.style.display = 'block';
            break;
        case 'settings':
            const settingsEl = document.getElementById('settingsSection');
            if (settingsEl) settingsEl.style.display = 'block';
            break;
        default:
            // Main section - all hidden
            break;
    }
}

// Toggle Options Section based on question type
function toggleOptionsSection() {
    const questionType = document.getElementById('questionType');
    const optionsSection = document.getElementById('optionsSection');
    
    if (!questionType || !optionsSection) return;
    
    if (questionType.value === 'radio') {
        optionsSection.style.display = 'block';
        // Load options if editing existing question
        const questionId = document.getElementById('questionId');
        if (questionId && questionId.value) {
            loadQuestionOptions(questionId.value);
        }
    } else {
        optionsSection.style.display = 'none';
    }
}

// Action Functions
function manageCategories() {
    showSection('categories');
}

function manageQuestions(categoryId = null) {
    currentCategoryId = categoryId;
    showSection('questions');
    
    if (categoryId) {
        loadQuestions(categoryId);
    } else {
        loadAllQuestions();
    }
}

function viewReports() {
    showSection('reports');
}

function manageSettings() {
    showSection('settings');
}

function backToCategories() {
    showSection('categories');
}

function backToMain() {
    showSection('main');
}

// Category Management
function addCategory() {
    const modal = document.getElementById('categoryModal');
    const form = document.getElementById('categoryForm');
    const title = document.getElementById('categoryModalTitle');
    const categoryId = document.getElementById('categoryId');
    const categoryOrder = document.getElementById('categoryOrder');
    
    if (!modal || !form || !title || !categoryId || !categoryOrder) {
        console.error('Required elements not found for addCategory');
        return;
    }
    
    title.innerHTML = '<i class="fas fa-folder me-2"></i>เพิ่มหมวดหมู่ใหม่';
    form.reset();
    categoryId.value = '';
    
    // Get next order
    const nextOrder = document.querySelectorAll('#categoriesTableBody tr[data-category-id]').length + 1;
    categoryOrder.value = nextOrder;
    
    try {
        const bootstrapModal = new bootstrap.Modal(modal);
        bootstrapModal.show();
    } catch (error) {
        console.error('Error showing modal:', error);
        showToast('เกิดข้อผิดพลาดในการเปิด Modal', 'error');
    }
}

function editCategory(categoryId) {
    const title = document.getElementById('categoryModalTitle');
    if (title) {
        title.innerHTML = '<i class="fas fa-edit me-2"></i>แก้ไขหมวดหมู่';
    }
    
    // Load category data
    const baseUrl = document.querySelector('base')?.href || window.location.origin + '/';
    const url = `${baseUrl}System_reports/api_get_category_management/${categoryId}`;
    
    fetch(url, {
        method: 'GET',
        headers: { 'X-Requested-With': 'XMLHttpRequest' }
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            const category = data.category;
            const categoryIdEl = document.getElementById('categoryId');
            const categoryNameEl = document.getElementById('categoryName');
            const categoryOrderEl = document.getElementById('categoryOrder');
            const categoryActiveEl = document.getElementById('categoryActive');
            
            if (categoryIdEl) categoryIdEl.value = category.id;
            if (categoryNameEl) categoryNameEl.value = category.category_name;
            if (categoryOrderEl) categoryOrderEl.value = category.category_order;
            if (categoryActiveEl) categoryActiveEl.checked = category.is_active == 1;
            
            const modal = document.getElementById('categoryModal');
            if (modal) {
                try {
                    const bootstrapModal = new bootstrap.Modal(modal);
                    bootstrapModal.show();
                } catch (error) {
                    console.error('Error showing modal:', error);
                }
            }
        } else {
            showToast('ไม่สามารถโหลดข้อมูลหมวดหมู่ได้', 'error');
        }
    })
    .catch(error => {
        console.error('Error:', error);
        showToast('เกิดข้อผิดพลาด', 'error');
    });
}

function saveCategory() {
    const form = document.getElementById('categoryForm');
    if (!form) {
        showToast('ไม่พบฟอร์ม', 'error');
        return;
    }
    
    const formData = new FormData(form);
    
    // Convert checkbox to value
    const categoryActive = document.getElementById('categoryActive');
    if (categoryActive) {
        formData.set('is_active', categoryActive.checked ? '1' : '0');
    }
    
    const categoryId = document.getElementById('categoryId');
    const isEdit = categoryId && categoryId.value;
    
    const baseUrl = document.querySelector('base')?.href || window.location.origin + '/';
    const url = isEdit ? 
        `${baseUrl}System_reports/api_update_category_management` : 
        `${baseUrl}System_reports/api_add_category_management`;
    
    fetch(url, {
        method: 'POST',
        headers: { 'X-Requested-With': 'XMLHttpRequest' },
        body: formData
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            showToast(isEdit ? 'อัพเดทหมวดหมู่เรียบร้อยแล้ว' : 'เพิ่มหมวดหมู่เรียบร้อยแล้ว', 'success');
            
            const modal = document.getElementById('categoryModal');
            if (modal && window.bootstrap) {
                const modalInstance = bootstrap.Modal.getInstance(modal);
                if (modalInstance) {
                    modalInstance.hide();
                }
            }
            
            setTimeout(() => location.reload(), 1000);
        } else {
            showToast(data.message || 'เกิดข้อผิดพลาด', 'error');
        }
    })
    .catch(error => {
        console.error('Error:', error);
        showToast('เกิดข้อผิดพลาด', 'error');
    });
}

function deleteCategory(categoryId) {
    if (confirm('ต้องการลบหมวดหมู่นี้หรือไม่?\n\nหมายเหตุ: คำถามทั้งหมดในหมวดหมู่นี้จะถูกลบด้วย')) {
        const baseUrl = document.querySelector('base')?.href || window.location.origin + '/';
        
        fetch(`${baseUrl}System_reports/api_delete_category_management`, {
            method: 'POST',
            headers: { 
                'X-Requested-With': 'XMLHttpRequest',
                'Content-Type': 'application/json'
            },
            body: JSON.stringify({ category_id: categoryId })
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                showToast('ลบหมวดหมู่เรียบร้อยแล้ว', 'success');
                setTimeout(() => location.reload(), 1000);
            } else {
                showToast(data.message || 'ไม่สามารถลบหมวดหมู่ได้', 'error');
            }
        })
        .catch(error => {
            console.error('Error:', error);
            showToast('เกิดข้อผิดพลาด', 'error');
        });
    }
}

// Question Management
function loadQuestions(categoryId) {
    const baseUrl = document.querySelector('base')?.href || window.location.origin + '/';
    
    fetch(`${baseUrl}System_reports/api_get_questions_management/${categoryId}`, {
        method: 'GET',
        headers: { 'X-Requested-With': 'XMLHttpRequest' }
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            updateQuestionsTable(data.questions);
            const categoryNameEl = document.getElementById('currentCategoryName');
            if (categoryNameEl) {
                categoryNameEl.textContent = data.category_name || 'ทั้งหมด';
            }
            currentCategoryId = categoryId;
        } else {
            showToast('ไม่สามารถโหลดคำถามได้', 'error');
        }
    })
    .catch(error => {
        console.error('Error:', error);
        showToast('เกิดข้อผิดพลาด', 'error');
    });
}

function loadAllQuestions() {
    const categoryNameEl = document.getElementById('currentCategoryName');
    if (categoryNameEl) {
        categoryNameEl.textContent = 'ทั้งหมด';
    }
    currentCategoryId = null;
}

function updateQuestionsTable(questions) {
    const tbody = document.getElementById('questionsTableBody');
    if (!tbody) return;
    
    if (questions.length === 0) {
        tbody.innerHTML = '<tr><td colspan="6" class="text-center text-muted"><i class="fas fa-question-circle"></i> ยังไม่มีคำถาม</td></tr>';
        return;
    }
    
    tbody.innerHTML = questions.map(question => `
        <tr data-question-id="${question.id}">
            <td><span class="badge bg-secondary">${question.question_order}</span></td>
            <td><strong>${escapeHtml(question.question_text)}</strong></td>
            <td>
                <span class="badge ${question.question_type === 'textarea' ? 'bg-info' : 'bg-primary'}">
                    ${question.question_type === 'textarea' ? 'ข้อความยาว' : 'เลือกตัวเลือก'}
                </span>
            </td>
            <td>
                ${question.is_required ? '<span class="badge bg-danger">จำเป็น</span>' : '<span class="badge bg-secondary">ไม่จำเป็น</span>'}
            </td>
            <td>
                ${question.is_active ? '<span class="badge bg-success">เปิดใช้งาน</span>' : '<span class="badge bg-warning">ปิดใช้งาน</span>'}
            </td>
            <td>
                <div class="btn-group btn-group-sm">
                    <button type="button" class="btn btn-outline-primary" onclick="editQuestion(${question.id})" title="แก้ไข">
                        <i class="fas fa-edit"></i>
                    </button>
                    ${question.question_type === 'radio' ? 
                        `<button type="button" class="btn btn-outline-warning" onclick="manageQuestionOptions(${question.id})" title="จัดการตัวเลือก">
                            <i class="fas fa-list-ul"></i>
                        </button>` : ''
                    }
                    <button type="button" class="btn btn-outline-danger" onclick="deleteQuestion(${question.id})" title="ลบ">
                        <i class="fas fa-trash"></i>
                    </button>
                </div>
            </td>
        </tr>
    `).join('');
}

function addQuestion() {
    if (!currentCategoryId) {
        showToast('กรุณาเลือกหมวดหมู่ก่อน', 'warning');
        return;
    }
    
    const title = document.getElementById('questionModalTitle');
    const form = document.getElementById('questionForm');
    const questionId = document.getElementById('questionId');
    const questionCategoryId = document.getElementById('questionCategoryId');
    const optionsSection = document.getElementById('optionsSection');
    const optionsTableBody = document.getElementById('optionsTableBody');
    const questionOrder = document.getElementById('questionOrder');
    
    if (title) title.innerHTML = '<i class="fas fa-question me-2"></i>เพิ่มคำถามใหม่';
    if (form) form.reset();
    if (questionId) questionId.value = '';
    if (questionCategoryId) questionCategoryId.value = currentCategoryId;
    
    // Reset options section
    if (optionsSection) optionsSection.style.display = 'none';
    if (optionsTableBody) optionsTableBody.innerHTML = '';
    
    // Get next order
    const nextOrder = document.querySelectorAll('#questionsTableBody tr[data-question-id]').length + 1;
    if (questionOrder) questionOrder.value = nextOrder;
    
    const modal = document.getElementById('questionModal');
    if (modal) {
        try {
            const bootstrapModal = new bootstrap.Modal(modal);
            bootstrapModal.show();
        } catch (error) {
            console.error('Error showing modal:', error);
        }
    }
}

function editQuestion(questionId) {
    const title = document.getElementById('questionModalTitle');
    if (title) {
        title.innerHTML = '<i class="fas fa-edit me-2"></i>แก้ไขคำถาม';
    }
    
    currentQuestionId = questionId;
    
    const baseUrl = document.querySelector('base')?.href || window.location.origin + '/';
    
    // Load question data
    fetch(`${baseUrl}System_reports/api_get_question_management/${questionId}`, {
        method: 'GET',
        headers: { 'X-Requested-With': 'XMLHttpRequest' }
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            const question = data.question;
            const elements = {
                questionId: document.getElementById('questionId'),
                questionCategoryId: document.getElementById('questionCategoryId'),
                questionText: document.getElementById('questionText'),
                questionOrder: document.getElementById('questionOrder'),
                questionType: document.getElementById('questionType'),
                questionRequired: document.getElementById('questionRequired'),
                questionActive: document.getElementById('questionActive')
            };
            
            if (elements.questionId) elements.questionId.value = question.id;
            if (elements.questionCategoryId) elements.questionCategoryId.value = question.category_id;
            if (elements.questionText) elements.questionText.value = question.question_text;
            if (elements.questionOrder) elements.questionOrder.value = question.question_order;
            if (elements.questionType) elements.questionType.value = question.question_type;
            if (elements.questionRequired) elements.questionRequired.checked = question.is_required == 1;
            if (elements.questionActive) elements.questionActive.checked = question.is_active == 1;
            
            // Toggle options section based on question type
            toggleOptionsSection();
            
            const modal = document.getElementById('questionModal');
            if (modal) {
                try {
                    const bootstrapModal = new bootstrap.Modal(modal);
                    bootstrapModal.show();
                } catch (error) {
                    console.error('Error showing modal:', error);
                }
            }
        } else {
            showToast('ไม่สามารถโหลดข้อมูลคำถามได้', 'error');
        }
    })
    .catch(error => {
        console.error('Error:', error);
        showToast('เกิดข้อผิดพลาด', 'error');
    });
}

function saveQuestion() {
    const form = document.getElementById('questionForm');
    if (!form) {
        showToast('ไม่พบฟอร์ม', 'error');
        return;
    }
    
    const formData = new FormData(form);
    
    // Convert checkboxes to values
    const questionRequired = document.getElementById('questionRequired');
    const questionActive = document.getElementById('questionActive');
    
    if (questionRequired) formData.set('is_required', questionRequired.checked ? '1' : '0');
    if (questionActive) formData.set('is_active', questionActive.checked ? '1' : '0');
    
    const questionId = document.getElementById('questionId');
    const isEdit = questionId && questionId.value;
    
    const baseUrl = document.querySelector('base')?.href || window.location.origin + '/';
    const url = isEdit ? 
        `${baseUrl}System_reports/api_update_question_management` : 
        `${baseUrl}System_reports/api_add_question_management`;
    
    fetch(url, {
        method: 'POST',
        headers: { 'X-Requested-With': 'XMLHttpRequest' },
        body: formData
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            showToast(isEdit ? 'อัพเดทคำถามเรียบร้อยแล้ว' : 'เพิ่มคำถามเรียบร้อยแล้ว', 'success');
            
            const modal = document.getElementById('questionModal');
            if (modal && window.bootstrap) {
                const modalInstance = bootstrap.Modal.getInstance(modal);
                if (modalInstance) {
                    modalInstance.hide();
                }
            }
            
            // Reload questions for current category
            if (currentCategoryId) {
                loadQuestions(currentCategoryId);
            }
        } else {
            showToast(data.message || 'เกิดข้อผิดพลาด', 'error');
        }
    })
    .catch(error => {
        console.error('Error:', error);
        showToast('เกิดข้อผิดพลาด', 'error');
    });
}

function deleteQuestion(questionId) {
    if (confirm('ต้องการลบคำถามนี้หรือไม่?')) {
        const baseUrl = document.querySelector('base')?.href || window.location.origin + '/';
        
        fetch(`${baseUrl}System_reports/api_delete_question_management`, {
            method: 'POST',
            headers: { 
                'X-Requested-With': 'XMLHttpRequest',
                'Content-Type': 'application/json'
            },
            body: JSON.stringify({ question_id: questionId })
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                showToast('ลบคำถามเรียบร้อยแล้ว', 'success');
                
                // Reload questions for current category
                if (currentCategoryId) {
                    loadQuestions(currentCategoryId);
                }
            } else {
                showToast(data.message || 'ไม่สามารถลบคำถามได้', 'error');
            }
        })
        .catch(error => {
            console.error('Error:', error);
            showToast('เกิดข้อผิดพลาด', 'error');
        });
    }
}

// Options Management
function manageQuestionOptions(questionId) {
    currentQuestionId = questionId;
    editQuestion(questionId);
}

function loadQuestionOptions(questionId) {
    const baseUrl = document.querySelector('base')?.href || window.location.origin + '/';
    
    fetch(`${baseUrl}System_reports/api_get_question_options/${questionId}`, {
        method: 'GET',
        headers: { 'X-Requested-With': 'XMLHttpRequest' }
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            updateOptionsTable(data.options);
        } else {
            showToast('ไม่สามารถโหลดตัวเลือกได้', 'error');
        }
    })
    .catch(error => {
        console.error('Error:', error);
        showToast('เกิดข้อผิดพลาด', 'error');
    });
}

function updateOptionsTable(options) {
    const tbody = document.getElementById('optionsTableBody');
    if (!tbody) return;
    
    if (options.length === 0) {
        tbody.innerHTML = '<tr><td colspan="5" class="text-center text-muted">ยังไม่มีตัวเลือก</td></tr>';
        return;
    }
    
    tbody.innerHTML = options.map(option => `
        <tr data-option-id="${option.id}">
            <td><span class="badge bg-secondary">${option.option_order}</span></td>
            <td><strong>${escapeHtml(option.option_text)}</strong></td>
            <td><code>${escapeHtml(option.option_value)}</code></td>
            <td>
                ${option.is_active ? '<span class="badge bg-success">เปิด</span>' : '<span class="badge bg-warning">ปิด</span>'}
            </td>
            <td>
                <div class="btn-group btn-group-sm">
                    <button type="button" class="btn btn-outline-primary" onclick="editOption(${option.id})" title="แก้ไข">
                        <i class="fas fa-edit"></i>
                    </button>
                    <button type="button" class="btn btn-outline-danger" onclick="deleteOption(${option.id})" title="ลบ">
                        <i class="fas fa-trash"></i>
                    </button>
                </div>
            </td>
        </tr>
    `).join('');
}

function addOption() {
    if (!currentQuestionId) {
        showToast('กรุณาเลือกคำถามก่อน', 'warning');
        return;
    }
    
    const title = document.getElementById('optionModalTitle');
    const form = document.getElementById('optionForm');
    const optionId = document.getElementById('optionId');
    const optionQuestionId = document.getElementById('optionQuestionId');
    const optionOrder = document.getElementById('optionOrder');
    
    if (title) title.innerHTML = '<i class="fas fa-list-ul me-2"></i>เพิ่มตัวเลือกใหม่';
    if (form) form.reset();
    if (optionId) optionId.value = '';
    if (optionQuestionId) optionQuestionId.value = currentQuestionId;
    
    // Get next order
    const nextOrder = document.querySelectorAll('#optionsTableBody tr[data-option-id]').length + 1;
    if (optionOrder) optionOrder.value = nextOrder;
    
    const modal = document.getElementById('optionModal');
    if (modal) {
        try {
            const bootstrapModal = new bootstrap.Modal(modal);
            bootstrapModal.show();
        } catch (error) {
            console.error('Error showing modal:', error);
        }
    }
}

function editOption(optionId) {
    const title = document.getElementById('optionModalTitle');
    if (title) {
        title.innerHTML = '<i class="fas fa-edit me-2"></i>แก้ไขตัวเลือก';
    }
    
    // Find option data from table
    const optionRow = document.querySelector(`tr[data-option-id="${optionId}"]`);
    if (optionRow) {
        const cells = optionRow.querySelectorAll('td');
        const elements = {
            optionId: document.getElementById('optionId'),
            optionQuestionId: document.getElementById('optionQuestionId'),
            optionText: document.getElementById('optionText'),
            optionValue: document.getElementById('optionValue'),
            optionOrder: document.getElementById('optionOrder'),
            optionActive: document.getElementById('optionActive')
        };
        
        if (elements.optionId) elements.optionId.value = optionId;
        if (elements.optionQuestionId) elements.optionQuestionId.value = currentQuestionId;
        if (elements.optionText && cells[1]) {
            const strong = cells[1].querySelector('strong');
            if (strong) elements.optionText.value = strong.textContent;
        }
        if (elements.optionValue && cells[2]) {
            const code = cells[2].querySelector('code');
            if (code) elements.optionValue.value = code.textContent;
        }
        if (elements.optionOrder && cells[0]) {
            const badge = cells[0].querySelector('.badge');
            if (badge) elements.optionOrder.value = badge.textContent;
        }
        if (elements.optionActive && cells[3]) {
            const successBadge = cells[3].querySelector('.badge.bg-success');
            elements.optionActive.checked = !!successBadge;
        }
        
        const modal = document.getElementById('optionModal');
        if (modal) {
            try {
                const bootstrapModal = new bootstrap.Modal(modal);
                bootstrapModal.show();
            } catch (error) {
                console.error('Error showing modal:', error);
            }
        }
    }
}

function saveOption() {
    const form = document.getElementById('optionForm');
    if (!form) {
        showToast('ไม่พบฟอร์ม', 'error');
        return;
    }
    
    const formData = new FormData(form);
    
    // Convert checkbox to value
    const optionActive = document.getElementById('optionActive');
    if (optionActive) {
        formData.set('is_active', optionActive.checked ? '1' : '0');
    }
    
    const optionId = document.getElementById('optionId');
    const isEdit = optionId && optionId.value;
    
    const baseUrl = document.querySelector('base')?.href || window.location.origin + '/';
    const url = isEdit ? 
        `${baseUrl}System_reports/api_update_option` : 
        `${baseUrl}System_reports/api_add_option`;
    
    fetch(url, {
        method: 'POST',
        headers: { 'X-Requested-With': 'XMLHttpRequest' },
        body: formData
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            showToast(isEdit ? 'อัพเดทตัวเลือกเรียบร้อยแล้ว' : 'เพิ่มตัวเลือกเรียบร้อยแล้ว', 'success');
            
            const modal = document.getElementById('optionModal');
            if (modal && window.bootstrap) {
                const modalInstance = bootstrap.Modal.getInstance(modal);
                if (modalInstance) {
                    modalInstance.hide();
                }
            }
            
            // Reload options for current question
            if (currentQuestionId) {
                loadQuestionOptions(currentQuestionId);
            }
        } else {
            showToast(data.message || 'เกิดข้อผิดพลาด', 'error');
        }
    })
    .catch(error => {
        console.error('Error:', error);
        showToast('เกิดข้อผิดพลาด', 'error');
    });
}

function deleteOption(optionId) {
    if (confirm('ต้องการลบตัวเลือกนี้หรือไม่?')) {
        const baseUrl = document.querySelector('base')?.href || window.location.origin + '/';
        
        fetch(`${baseUrl}System_reports/api_delete_option`, {
            method: 'POST',
            headers: { 
                'X-Requested-With': 'XMLHttpRequest',
                'Content-Type': 'application/json'
            },
            body: JSON.stringify({ option_id: optionId })
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                showToast('ลบตัวเลือกเรียบร้อยแล้ว', 'success');
                
                // Reload options for current question
                if (currentQuestionId) {
                    loadQuestionOptions(currentQuestionId);
                }
            } else {
                showToast(data.message || 'ไม่สามารถลบตัวเลือกได้', 'error');
            }
        })
        .catch(error => {
            console.error('Error:', error);
            showToast('เกิดข้อผิดพลาด', 'error');
        });
    }
}

// Settings Management
function saveSettings() {
    const form = document.getElementById('settingsForm');
    if (!form) {
        showToast('ไม่พบฟอร์ม', 'error');
        return;
    }
    
    const formData = new FormData(form);
    
    // Convert checkboxes
    const showProgressBar = form.show_progress_bar;
    const allowMultipleSubmissions = form.allow_multiple_submissions;
    
    if (showProgressBar) formData.set('show_progress_bar', showProgressBar.checked ? '1' : '0');
    if (allowMultipleSubmissions) formData.set('allow_multiple_submissions', allowMultipleSubmissions.checked ? '1' : '0');
    
    const baseUrl = document.querySelector('base')?.href || window.location.origin + '/';
    
    fetch(`${baseUrl}System_reports/api_save_settings_management`, {
        method: 'POST',
        headers: { 'X-Requested-With': 'XMLHttpRequest' },
        body: formData
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            showToast('บันทึกการตั้งค่าเรียบร้อยแล้ว', 'success');
        } else {
            showToast(data.message || 'เกิดข้อผิดพลาด', 'error');
        }
    })
    .catch(error => {
        console.error('Error:', error);
        showToast('เกิดข้อผิดพลาด', 'error');
    });
}

// Utility Functions
function refreshAssessmentData() {
    const btn = document.getElementById('refreshBtn');
    if (!btn) return;
    
    const originalHtml = btn.innerHTML;
    
    btn.disabled = true;
    btn.innerHTML = '<i class="fas fa-spinner fa-spin me-1"></i>กำลังรีเฟรช...';
    
    setTimeout(() => {
        showToast('รีเฟรชข้อมูลเรียบร้อยแล้ว', 'success');
        btn.disabled = false;
        btn.innerHTML = originalHtml;
        location.reload();
    }, 2000);
}

function exportAssessmentReport() {
    showToast('กำลังเตรียมรายงานแบบประเมิน...', 'info');
    
    const baseUrl = document.querySelector('base')?.href || window.location.origin + '/';
    const previewUrl = `${baseUrl}System_reports/export_assessment_report`;
    const previewWindow = window.open(previewUrl, '_blank', 'width=1200,height=800,scrollbars=yes,resizable=yes');
    
    if (previewWindow) {
        previewWindow.focus();
        showToast('เปิดหน้าตัวอย่างรายงานแล้ว', 'success');
    } else {
        showToast('ไม่สามารถเปิดหน้าตัวอย่างได้ กรุณาอนุญาต Pop-up', 'warning');
        window.location.href = previewUrl;
    }
}

function initializeEventListeners() {
    // Form validation
    const categoryForm = document.getElementById('categoryForm');
    if (categoryForm) {
        categoryForm.addEventListener('submit', function(e) {
            e.preventDefault();
            saveCategory();
        });
    }
    
    const questionForm = document.getElementById('questionForm');
    if (questionForm) {
        questionForm.addEventListener('submit', function(e) {
            e.preventDefault();
            saveQuestion();
        });
    }
    
    const optionForm = document.getElementById('optionForm');
    if (optionForm) {
        optionForm.addEventListener('submit', function(e) {
            e.preventDefault();
            saveOption();
        });
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
        'success': 'background: linear-gradient(135deg, #6ee7b7 0%, #a7f3d0 100%);',
        'error': 'background: linear-gradient(135deg, #f87171 0%, #fca5a5 100%);',
        'info': 'background: linear-gradient(135deg, #67e8f9 0%, #a5f3fc 100%);',
        'warning': 'background: linear-gradient(135deg, #fbbf24 0%, #fcd34d 100%);'
    };
    
    const icons = {
        'success': 'fa-check-circle',
        'error': 'fa-exclamation-triangle',
        'info': 'fa-info-circle',
        'warning': 'fa-exclamation-circle'
    };
    
    const toastHtml = `
        <div class="toast align-items-center ${typeClasses[type]} border-0" 
             style="${bgStyles[type]} backdrop-filter: blur(10px);"
             role="alert" aria-live="assertive" aria-atomic="true">
            <div class="d-flex">
                <div class="toast-body">
                    <i class="fas ${icons[type]} me-2"></i>
                    ${escapeHtml(message)}
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
    if (window.bootstrap && bootstrap.Toast) {
        const toast = new bootstrap.Toast(toastElement, { delay: 4000 });
        toast.show();
        
        toastElement.addEventListener('hidden.bs.toast', () => {
            toastElement.remove();
        });
    } else {
        // Fallback if Bootstrap is not available
        setTimeout(() => {
            toastElement.remove();
        }, 4000);
    }
}

// Utility function to escape HTML
function escapeHtml(text) {
    const map = {
        '&': '&amp;',
        '<': '&lt;',
        '>': '&gt;',
        '"': '&quot;',
        "'": '&#039;'
    };
    return text.replace(/[&<>"']/g, function(m) { return map[m]; });
}

// Global error handler
window.addEventListener('error', function(e) {
    console.error('Global error:', e.error);
    if (e.error instanceof ReferenceError) {
        showToast('เกิดข้อผิดพลาดในระบบ กรุณารีเฟรชหน้าเว็บ', 'error');
    }
});

// Make functions available globally for onclick handlers
window.manageCategories = manageCategories;
window.manageQuestions = manageQuestions;
window.viewReports = viewReports;
window.manageSettings = manageSettings;
window.addCategory = addCategory;
window.editCategory = editCategory;
window.deleteCategory = deleteCategory;
window.addQuestion = addQuestion;
window.editQuestion = editQuestion;
window.deleteQuestion = deleteQuestion;
window.manageQuestionOptions = manageQuestionOptions;
window.addOption = addOption;
window.editOption = editOption;
window.saveOption = saveOption;
window.deleteOption = deleteOption;
window.saveCategory = saveCategory;
window.saveQuestion = saveQuestion;
window.saveSettings = saveSettings;
window.refreshAssessmentData = refreshAssessmentData;
window.exportAssessmentReport = exportAssessmentReport;
window.backToCategories = backToCategories;
window.backToMain = backToMain;
window.toggleOptionsSection = toggleOptionsSection;
</script>

<!-- CSS for Assessment Management -->
<style>
	
	
	
	body {
    padding-top: 50px !important;
}
	
	
/* Include all the CSS from the original paste.txt */
.stat-card {
    background: white;
    border-radius: 12px;
    padding: 1.5rem;
    box-shadow: 0 2px 8px rgba(0,0,0,0.06);
    border: 1px solid #e5e7eb;
    transition: all 0.3s ease;
}

.stat-card:hover {
    transform: translateY(-2px);
    box-shadow: 0 4px 15px rgba(0,0,0,0.1);
}

.stat-card-header {
    display: flex;
    justify-content: between;
    align-items: center;
    margin-bottom: 1rem;
}

.stat-icon {
    width: 40px;
    height: 40px;
    border-radius: 8px;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 1.25rem;
}

.stat-icon.success {
    background: linear-gradient(135deg, #10b981, #059669);
    color: white;
}

.stat-icon.primary {
    background: linear-gradient(135deg, #3b82f6, #2563eb);
    color: white;
}

.stat-icon.warning {
    background: linear-gradient(135deg, #f59e0b, #d97706);
    color: white;
}

.stat-icon.info {
    background: linear-gradient(135deg, #06b6d4, #0891b2);
    color: white;
}

.stat-label {
    font-size: 0.9rem;
    color: #6b7280;
    margin-bottom: 0.5rem;
}

.stat-value {
    font-size: 1.5rem;
    font-weight: 700;
}

.chart-card {
    background: white;
    border-radius: 12px;
    padding: 1.5rem;
    box-shadow: 0 2px 8px rgba(0,0,0,0.06);
    border: 1px solid #e5e7eb;
}

.chart-header {
    display: flex;
    justify-content: between;
    align-items: center;
    margin-bottom: 1.5rem;
}

.chart-title {
    font-size: 1.25rem;
    font-weight: 600;
    color: #1f2937;
    margin: 0;
}

.btn-chart {
    background: #f3f4f6;
    border: none;
    border-radius: 8px;
    padding: 0.5rem 1rem;
    font-size: 0.875rem;
    color: #6b7280;
}

.btn-chart.active {
    background: #3b82f6;
    color: white;
}

/* Action Cards */
.action-card {
    padding: 1.5rem;
    border-radius: 12px;
    display: flex;
    align-items: center;
    transition: all 0.3s ease;
    cursor: pointer;
    box-shadow: 0 2px 8px rgba(0,0,0,0.06);
    border: none;
    text-decoration: none;
}

.action-card:hover {
    transform: translateY(-4px);
    box-shadow: 0 8px 25px rgba(0,0,0,0.15);
    text-decoration: none;
}

.action-icon {
    width: 50px;
    height: 50px;
    border-radius: 50%;
    background: rgba(255, 255, 255, 0.2);
    display: flex;
    align-items: center;
    justify-content: center;
    margin-right: 1rem;
    font-size: 1.25rem;
}

.action-title {
    font-size: 1rem;
    font-weight: 700;
    margin-bottom: 0.25rem;
}

.action-desc {
    font-size: 0.85rem;
    font-weight: 500;
    opacity: 0.8;
}

/* Table Enhancements */
.table-hover tbody tr:hover {
    background-color: rgba(0,0,0,0.02);
    transition: background-color 0.2s ease;
}

.btn-group-sm .btn {
    padding: 0.25rem 0.5rem;
    font-size: 0.875rem;
}

/* Modal Enhancements */
.modal-content {
    border-radius: 12px;
    border: none;
}

.modal-header {
    border-radius: 12px 12px 0 0;
}

/* Form Enhancements */
.form-control:focus,
.form-select:focus {
    border-color: #6366f1;
    box-shadow: 0 0 0 0.2rem rgba(99, 102, 241, 0.25);
}

.form-check-input:checked {
    background-color: #6366f1;
    border-color: #6366f1;
}

/* Badge Colors */
.badge {
    font-size: 0.75rem;
    padding: 0.35em 0.65em;
}

/* Options Table Styling */
#optionsTable {
    font-size: 0.85rem;
}

#optionsTable .badge {
    font-size: 0.7rem;
}

#optionsSection {
    border-top: 2px solid #e5e7eb;
    padding-top: 1rem;
    margin-top: 1rem;
    background: #f8f9fa;
    border-radius: 8px;
    padding: 15px;
}

/* Enhanced Option Modal */
#optionModal .modal-header {
    background: linear-gradient(135deg, #f59e0b 0%, #f97316 100%);
}

/* Button Enhancements */
.btn-outline-warning {
    color: #f59e0b;
    border-color: #f59e0b;
}

.btn-outline-warning:hover {
    background-color: #f59e0b;
    border-color: #f59e0b;
    color: white;
}

/* Responsive */
@media (max-width: 768px) {
    .action-card {
        padding: 1rem;
        flex-direction: column;
        text-align: center;
    }
    
    .action-icon {
        margin-right: 0;
        margin-bottom: 0.75rem;
    }
    
    .btn-group {
        flex-direction: column;
        width: 100%;
    }
    
    .modal-dialog {
        margin: 1rem;
    }
    
    #optionsSection {
        padding: 10px;
    }
    
    .modal-lg {
        max-width: 95%;
    }
}

/* Loading states */
.btn:disabled {
    opacity: 0.65;
    cursor: not-allowed;
}

/* Animation for stat cards */
.stat-card, .action-card {
    animation: fadeInUp 0.6s ease forwards;
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

/* Enhanced Table Styling */
.table-sm th {
    font-weight: 600;
    text-transform: uppercase;
    font-size: 0.75rem;
    letter-spacing: 0.05em;
    color: #374151;
    border-bottom: 2px solid #e5e7eb;
}

.table-sm td {
    vertical-align: middle;
    padding: 0.5rem;
}

/* Code styling for option values */
code {
    background-color: #f1f5f9;
    color: #0f172a;
    padding: 0.25rem 0.5rem;
    border-radius: 4px;
    font-size: 0.8rem;
    font-weight: 500;
}

/* Enhanced badge styling */
.badge.bg-secondary {
    background-color: #6b7280 !important;
}

.badge.bg-success {
    background-color: #059669 !important;
}

.badge.bg-warning {
    background-color: #d97706 !important;
}

.badge.bg-info {
    background-color: #0891b2 !important;
}

.badge.bg-primary {
    background-color: #2563eb !important;
}

.badge.bg-danger {
    background-color: #dc2626 !important;
}

/* Hover effects for table rows */
.table-hover tbody tr:hover {
    background-color: rgba(59, 130, 246, 0.05);
}

/* Enhanced form styling */
.form-label.fw-bold {
    color: #374151;
    margin-bottom: 0.5rem;
}

.form-control, .form-select {
    border: 1px solid #d1d5db;
    border-radius: 8px;
    transition: all 0.2s ease;
}

.form-control:focus, .form-select:focus {
    border-color: #3b82f6;
    box-shadow: 0 0 0 3px rgba(59, 130, 246, 0.1);
}

/* Switch styling */
.form-check-input:focus {
    border-color: #3b82f6;
    box-shadow: 0 0 0 0.25rem rgba(59, 130, 246, 0.25);
}

.form-check-input:checked {
    background-color: #3b82f6;
    border-color: #3b82f6;
}
</style>



<script>
// Enhanced Category Management Functions

// แสดง/ซ่อนคำอธิบายตามสถานะ is_scoring
function toggleScoringInfo() {
    const scoringCheckbox = document.getElementById('categoryScoring');
    const scoringYesInfo = document.getElementById('scoringYesInfo');
    const scoringNoInfo = document.getElementById('scoringNoInfo');
    
    if (!scoringCheckbox || !scoringYesInfo || !scoringNoInfo) return;
    
    if (scoringCheckbox.checked) {
        scoringYesInfo.style.display = 'block';
        scoringNoInfo.style.display = 'none';
    } else {
        scoringYesInfo.style.display = 'none';
        scoringNoInfo.style.display = 'block';
    }
}

// Event listener สำหรับ checkbox เมื่อเปลี่ยนแปลง
document.addEventListener('DOMContentLoaded', function() {
    const scoringCheckbox = document.getElementById('categoryScoring');
    if (scoringCheckbox) {
        scoringCheckbox.addEventListener('change', toggleScoringInfo);
        
        // เรียกใช้ครั้งแรกเพื่อตั้งค่าเริ่มต้น
        toggleScoringInfo();
    }
});

// อัพเดทฟังก์ชัน editCategory เพื่อรองรับ is_scoring
function editCategoryEnhanced(categoryId) {
    const title = document.getElementById('categoryModalTitle');
    if (title) {
        title.innerHTML = '<i class="fas fa-edit me-2"></i>แก้ไขหมวดหมู่';
    }
    
    // Load category data
    const baseUrl = document.querySelector('base')?.href || window.location.origin + '/';
    const url = `${baseUrl}System_reports/api_get_category_management/${categoryId}`;
    
    fetch(url, {
        method: 'GET',
        headers: { 'X-Requested-With': 'XMLHttpRequest' }
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            const category = data.category;
            const elements = {
                categoryId: document.getElementById('categoryId'),
                categoryName: document.getElementById('categoryName'),
                categoryOrder: document.getElementById('categoryOrder'),
                categoryActive: document.getElementById('categoryActive'),
                categoryScoring: document.getElementById('categoryScoring') // ✅ เพิ่มฟิลด์ใหม่
            };
            
            if (elements.categoryId) elements.categoryId.value = category.id;
            if (elements.categoryName) elements.categoryName.value = category.category_name;
            if (elements.categoryOrder) elements.categoryOrder.value = category.category_order;
            if (elements.categoryActive) elements.categoryActive.checked = category.is_active == 1;
            
            // ✅ ตั้งค่า is_scoring
            if (elements.categoryScoring) {
                elements.categoryScoring.checked = category.is_scoring == 1;
                toggleScoringInfo(); // อัพเดทการแสดงผลคำอธิบาย
            }
            
            const modal = document.getElementById('categoryModal');
            if (modal) {
                try {
                    const bootstrapModal = new bootstrap.Modal(modal);
                    bootstrapModal.show();
                } catch (error) {
                    console.error('Error showing modal:', error);
                }
            }
        } else {
            showToast('ไม่สามารถโหลดข้อมูลหมวดหมู่ได้', 'error');
        }
    })
    .catch(error => {
        console.error('Error:', error);
        showToast('เกิดข้อผิดพลาด', 'error');
    });
}

// อัพเดทฟังก์ชัน saveCategory เพื่อรองรับ is_scoring
function saveCategoryEnhanced() {
    const form = document.getElementById('categoryForm');
    if (!form) {
        showToast('ไม่พบฟอร์ม', 'error');
        return;
    }
    
    const formData = new FormData(form);
    
    // Convert checkboxes to values
    const categoryActive = document.getElementById('categoryActive');
    const categoryScoring = document.getElementById('categoryScoring');
    
    if (categoryActive) {
        formData.set('is_active', categoryActive.checked ? '1' : '0');
    }
    
    // ✅ เพิ่มการส่งค่า is_scoring
    if (categoryScoring) {
        formData.set('is_scoring', categoryScoring.checked ? '1' : '0');
    }
    
    const categoryId = document.getElementById('categoryId');
    const isEdit = categoryId && categoryId.value;
    
    const baseUrl = document.querySelector('base')?.href || window.location.origin + '/';
    const url = isEdit ? 
        `${baseUrl}System_reports/api_update_category_management` : 
        `${baseUrl}System_reports/api_add_category_management`;
    
    fetch(url, {
        method: 'POST',
        headers: { 'X-Requested-With': 'XMLHttpRequest' },
        body: formData
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            showToast(isEdit ? 'อัพเดทหมวดหมู่เรียบร้อยแล้ว' : 'เพิ่มหมวดหมู่เรียบร้อยแล้ว', 'success');
            
            const modal = document.getElementById('categoryModal');
            if (modal && window.bootstrap) {
                const modalInstance = bootstrap.Modal.getInstance(modal);
                if (modalInstance) {
                    modalInstance.hide();
                }
            }
            
            setTimeout(() => location.reload(), 1000);
        } else {
            showToast(data.message || 'เกิดข้อผิดพลาด', 'error');
        }
    })
    .catch(error => {
        console.error('Error:', error);
        showToast('เกิดข้อผิดพลาด', 'error');
    });
}

// อัพเดทฟังก์ชัน addCategory เพื่อรีเซ็ต is_scoring
function addCategoryEnhanced() {
    const modal = document.getElementById('categoryModal');
    const form = document.getElementById('categoryForm');
    const title = document.getElementById('categoryModalTitle');
    const categoryId = document.getElementById('categoryId');
    const categoryOrder = document.getElementById('categoryOrder');
    const categoryScoring = document.getElementById('categoryScoring');
    
    if (!modal || !form || !title || !categoryId || !categoryOrder) {
        console.error('Required elements not found for addCategory');
        return;
    }
    
    title.innerHTML = '<i class="fas fa-folder me-2"></i>เพิ่มหมวดหมู่ใหม่';
    form.reset();
    categoryId.value = '';
    
    // Get next order
    const nextOrder = document.querySelectorAll('#categoriesTableBody tr[data-category-id]').length + 1;
    categoryOrder.value = nextOrder;
    
    // ✅ ตั้งค่าเริ่มต้นสำหรับ is_scoring
    if (categoryScoring) {
        categoryScoring.checked = true; // default เป็น true (นำไปคำนวณ)
        toggleScoringInfo(); // อัพเดทการแสดงผล
    }
    
    try {
        const bootstrapModal = new bootstrap.Modal(modal);
        bootstrapModal.show();
    } catch (error) {
        console.error('Error showing modal:', error);
        showToast('เกิดข้อผิดพลาดในการเปิด Modal', 'error');
    }
}

// Override ฟังก์ชันเดิม
window.editCategory = editCategoryEnhanced;
window.saveCategory = saveCategoryEnhanced;
window.addCategory = addCategoryEnhanced;
</script>

<!-- CSS สำหรับ styling เพิ่มเติม -->
<style>
/* Enhanced Category Modal Styling */
.scoring-option {
    background-color: rgba(255, 255, 255, 0.7);
    border-radius: 8px;
    padding: 1rem;
    border: 1px solid #e5e7eb;
    transition: all 0.3s ease;
}

.scoring-option h6 {
    font-weight: 600;
    margin-bottom: 0.5rem;
}

.scoring-option ul {
    list-style-type: none;
    padding-left: 0;
}

.scoring-option ul li {
    padding: 0.25rem 0;
    position: relative;
    padding-left: 1.2rem;
}

.scoring-option ul li:before {
    content: "•";
    position: absolute;
    left: 0;
    font-weight: bold;
}

.scoring-yes {
    border-left: 4px solid #10b981;
    background: linear-gradient(135deg, rgba(16, 185, 129, 0.05) 0%, rgba(16, 185, 129, 0.1) 100%);
}

.scoring-no {
    border-left: 4px solid #f59e0b;
    background: linear-gradient(135deg, rgba(245, 158, 11, 0.05) 0%, rgba(245, 158, 11, 0.1) 100%);
}

.badge {
    font-size: 0.75rem;
    padding: 0.35em 0.65em;
    border-radius: 6px;
}

.form-check-input:checked {
    background-color: #3b82f6;
    border-color: #3b82f6;
}

.form-check-input:focus {
    border-color: #3b82f6;
    box-shadow: 0 0 0 0.25rem rgba(59, 130, 246, 0.25);
}

.alert-info {
    background-color: rgba(59, 130, 246, 0.1);
    border-color: rgba(59, 130, 246, 0.2);
    color: #1e40af;
}

.card.border-primary {
    border-color: rgba(59, 130, 246, 0.3) !important;
}

.text-danger {
    color: #dc2626 !important;
}

/* Animation for info toggle */
.scoring-option {
    animation: fadeIn 0.3s ease-in-out;
}

@keyframes fadeIn {
    from {
        opacity: 0;
        transform: translateY(-10px);
    }
    to {
        opacity: 1;
        transform: translateY(0);
    }
}

/* Responsive adjustments */
@media (max-width: 768px) {
    .scoring-option {
        margin-bottom: 1rem;
    }
    
    .row .col-md-6 {
        margin-bottom: 0.5rem;
    }
}
</style>
<style>
/* สไตล์ปุ่มแบบเรียบๆ */
.btn-sm {
    padding: 0.375rem 0.75rem;
    font-size: 0.875rem;
    border-radius: 6px;
    font-weight: 400;
    transition: all 0.15s ease-in-out;
}

.btn-sm:hover {
    transform: none;
    box-shadow: 0 2px 4px rgba(0,0,0,0.1);
}

.btn-light {
    background-color: #f8f9fa;
    border-color: #dee2e6;
    color: #495057;
}

.btn-outline-primary {
    color: #0d6efd;
    border-color: #0d6efd;
}

.btn-outline-success {
    color: #198754;
    border-color: #198754;
}

.btn-outline-info {
    color: #0dcaf0;
    border-color: #0dcaf0;
}

.btn-outline-secondary {
    color: #6c757d;
    border-color: #6c757d;
}

.btn-outline-warning {
    color: #fd7e14;
    border-color: #fd7e14;
}

/* เส้นแบ่งแบบเรียบ */
.text-muted {
    color: #adb5bd !important;
}

/* Responsive สำหรับมือถือ */
@media (max-width: 768px) {
    .d-flex.gap-2 {
        gap: 0.5rem !important;
        justify-content: center;
    }
    
    .btn-sm {
        font-size: 0.8rem;
        padding: 0.25rem 0.5rem;
    }
    
    .text-muted.mx-1 {
        display: none;
    }
}

@media (max-width: 576px) {
    .d-flex.gap-2 {
        flex-direction: column;
        align-items: stretch;
        gap: 0.5rem !important;
    }
    
    .btn-sm {
        width: 100%;
        text-align: center;
    }
}
</style>