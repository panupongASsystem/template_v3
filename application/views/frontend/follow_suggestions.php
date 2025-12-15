<!-- File: application/views/frontend/follow_suggestions.php -->
<div class="text-center pages-head">
    <span class="font-pages-head">ติดตามสถานะความคิดเห็น</span>
</div>
</div>
<img src="<?php echo base_url('docs/welcome-btm-light-other.png'); ?>">

<!-- Header Section -->
<div class="text-center pages-head">
    <span class="font-pages-head"
        style="font-size: 2.8rem; font-weight: 700; text-shadow: 1px 1px 3px rgba(108, 117, 125, 0.2);">
        ติดตามสถานะความคิดเห็น
    </span>
</div>

<div class="bg-pages" style="background: #ffffff; min-height: 100vh; padding: 2rem 0;">
    <div class="container" style="max-width: 1200px;">

        <!-- Flash Messages -->
        <?php if (!empty($success_message)): ?>
            <div class="alert alert-success alert-dismissible fade show" role="alert"
                style="border-radius: 15px; background: linear-gradient(135deg, #d4edda 0%, #c3e6cb 100%); border: 1px solid #b8dacc;">
                <i class="fas fa-check-circle me-2"></i><?= $success_message; ?>
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        <?php endif; ?>

        <?php if (!empty($error_message)): ?>
            <div class="alert alert-danger alert-dismissible fade show" role="alert"
                style="border-radius: 15px; background: linear-gradient(135deg, #f8d7da 0%, #f1aeb5 100%); border: 1px solid #f1aeb5;">
                <i class="fas fa-exclamation-triangle me-2"></i><?= $error_message; ?>
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        <?php endif; ?>

        <?php if (!empty($info_message)): ?>
            <div class="alert alert-info alert-dismissible fade show" role="alert"
                style="border-radius: 15px; background: linear-gradient(135deg, #d1ecf1 0%, #b8daff 100%); border: 1px solid #b8daff;">
                <i class="fas fa-info-circle me-2"></i><?= $info_message; ?>
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        <?php endif; ?>

        <!-- Search Form -->
        <div class="row justify-content-center mb-4">
            <div class="col-lg-8">
                <div class="card shadow-lg"
                    style="border: none; border-radius: 25px; background: linear-gradient(135deg, #ffffff 0%, #f8f9fa 100%); box-shadow: 0 15px 35px rgba(0, 0, 0, 0.1);">

                    <!-- Decorative top border -->
                    <div
                        style="height: 5px; background: linear-gradient(90deg, #4caf50, #81c784, #4caf50); background-size: 200% 100%; animation: gradientShift 3s ease-in-out infinite; border-radius: 25px 25px 0 0;">
                    </div>

                    <div class="card-body" style="padding: 2.5rem;">
                        <div class="text-center mb-4">
                            <div
                                style="width: 100px; height: 100px; margin: 0 auto 1.5rem; background: linear-gradient(135deg, rgba(76, 175, 80, 0.15) 0%, rgba(129, 199, 132, 0.15) 100%); border-radius: 50%; display: flex; align-items: center; justify-content: center; box-shadow: 0 8px 25px rgba(76, 175, 80, 0.3);">
                                <i class="fas fa-search"
                                    style="font-size: 3rem; color: #4caf50; text-shadow: 0 2px 8px rgba(76, 175, 80, 0.4);"></i>
                            </div>
                            <h3 style="color: #2c3e50; font-weight: 600; margin-bottom: 0.5rem;">ค้นหาข้อมูลความคิดเห็น
                            </h3>
                            <?php if ($is_logged_in && $user_type === 'public'): ?>
                                <p class="text-muted" style="font-size: 1.1rem;">ค้นหาความคิดเห็นของคุณ
                                    (เฉพาะข้อมูลในบัญชีของคุณเท่านั้น)</p>
                                <div class="alert alert-info"
                                    style="border-radius: 12px; background: linear-gradient(135deg, rgba(23, 162, 184, 0.1) 0%, rgba(23, 162, 184, 0.15) 100%); border: 1px solid rgba(23, 162, 184, 0.3); margin-top: 1rem;">
                                    <i class="fas fa-user-shield me-2"></i>
                                    <strong>สมาชิก:</strong> คุณจะเห็นเฉพาะความคิดเห็นที่ส่งผ่านบัญชีของคุณเท่านั้น
                                </div>
                            <?php else: ?>
                                <p class="text-muted" style="font-size: 1.1rem;">
                                    กรอกข้อมูลอย่างใดอย่างหนึ่งเพื่อค้นหาสถานะความคิดเห็นของคุณ</p>
                                <div class="alert alert-warning"
                                    style="border-radius: 12px; background: linear-gradient(135deg, rgba(255, 193, 7, 0.1) 0%, rgba(255, 193, 7, 0.15) 100%); border: 1px solid rgba(255, 193, 7, 0.3); margin-top: 1rem;">
                                    <i class="fas fa-info-circle me-2"></i>
                                    <strong>ผู้เยี่ยมชม:</strong>
                                    คุณจะเห็นเฉพาะความคิดเห็นที่ส่งโดยไม่ได้เข้าสู่ระบบเท่านั้น
                                </div>
                            <?php endif; ?>
                        </div>

                        <form id="searchForm" onsubmit="searchSuggestion(event)">
                            <!-- Search Type Selection -->
                            <div class="mb-4">
                                <label class="form-label"
                                    style="font-weight: 600; color: #495057; margin-bottom: 1rem;">
                                    <i class="fas fa-filter me-2" style="color: #4caf50;"></i>เลือกประเภทการค้นหา
                                </label>
                                <div class="row">
                                    <div class="col-md-4 mb-3">
                                        <div class="form-check search-option"
                                            style="background: rgba(255,255,255,0.8); border-radius: 12px; padding: 1rem; border: 2px solid transparent; transition: all 0.3s ease; cursor: pointer;"
                                            onclick="selectSearchType('ref')">
                                            <input class="form-check-input" type="radio" name="search_type"
                                                id="search_ref" value="ref" checked>
                                            <label class="form-check-label" for="search_ref"
                                                style="cursor: pointer; width: 100%;">
                                                <div class="text-center">
                                                    <i class="fas fa-hashtag"
                                                        style="font-size: 1.5rem; color: #17a2b8; margin-bottom: 0.5rem;"></i>
                                                    <div style="font-weight: 600; color: #495057;">หมายเลขอ้างอิง</div>
                                                    <small class="text-muted">เช่น S123456</small>
                                                </div>
                                            </label>
                                        </div>
                                    </div>
                                    <div class="col-md-4 mb-3">
                                        <div class="form-check search-option"
                                            style="background: rgba(255,255,255,0.8); border-radius: 12px; padding: 1rem; border: 2px solid transparent; transition: all 0.3s ease; cursor: pointer;"
                                            onclick="selectSearchType('phone')">
                                            <input class="form-check-input" type="radio" name="search_type"
                                                id="search_phone" value="phone">
                                            <label class="form-check-label" for="search_phone"
                                                style="cursor: pointer; width: 100%;">
                                                <div class="text-center">
                                                    <i class="fas fa-phone"
                                                        style="font-size: 1.5rem; color: #28a745; margin-bottom: 0.5rem;"></i>
                                                    <div style="font-weight: 600; color: #495057;">เบอร์โทรศัพท์</div>
                                                    <small class="text-muted">เช่น 0812345678</small>
                                                </div>
                                            </label>
                                        </div>
                                    </div>
                                    <div class="col-md-4 mb-3">
                                        <div class="form-check search-option"
                                            style="background: rgba(255,255,255,0.8); border-radius: 12px; padding: 1rem; border: 2px solid transparent; transition: all 0.3s ease; cursor: pointer;"
                                            onclick="selectSearchType('id_card')">
                                            <input class="form-check-input" type="radio" name="search_type"
                                                id="search_id_card" value="id_card">
                                            <label class="form-check-label" for="search_id_card"
                                                style="cursor: pointer; width: 100%;">
                                                <div class="text-center">
                                                    <i class="fas fa-id-card"
                                                        style="font-size: 1.5rem; color: #dc3545; margin-bottom: 0.5rem;"></i>
                                                    <div style="font-weight: 600; color: #495057;">เลขบัตรประชาชน</div>
                                                    <small class="text-muted">13 หลัก</small>
                                                </div>
                                            </label>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- Search Input -->
                            <div class="mb-4">
                                <label for="search_value" class="form-label" style="font-weight: 600; color: #495057;">
                                    <i class="fas fa-edit me-2" style="color: #4caf50;"></i>
                                    <span id="search_label">หมายเลขอ้างอิง</span>
                                </label>
                                <div class="input-group" style="box-shadow: 0 6px 20px rgba(76, 175, 80, 0.15);">
                                    <span class="input-group-text"
                                        style="background: linear-gradient(135deg, #4caf50 0%, #388e3c 100%); border: none; color: white; border-radius: 15px 0 0 15px;">
                                        <i id="search_icon" class="fas fa-hashtag"></i>
                                    </span>
                                    <input type="text" class="form-control" id="search_value" name="search_value"
                                        placeholder="กรอกหมายเลขอ้างอิง..."
                                        value="<?= htmlspecialchars($search_ref ?? ''); ?>" required
                                        style="border: none; border-radius: 0 15px 15px 0; padding: 1rem; font-size: 1.1rem; background: linear-gradient(135deg, #ffffff 0%, #f8f9fa 100%);">
                                </div>
                                <small id="search_help"
                                    class="form-text text-muted">กรอกหมายเลขอ้างอิงที่ได้รับหลังจากส่งความคิดเห็น</small>
                            </div>

                            <!-- Search Button -->
                            <div class="text-center">
                                <button type="submit" class="btn btn-lg search-btn" id="searchBtn" style="background: linear-gradient(135deg, #4caf50 0%, #388e3c 100%); 
                                               border: none; 
                                               color: white; 
                                               padding: 1rem 3rem; 
                                               border-radius: 15px; 
                                               font-size: 1.2rem; 
                                               font-weight: 600; 
                                               transition: all 0.3s ease; 
                                               box-shadow: 0 6px 20px rgba(76, 175, 80, 0.3);
                                               position: relative;
                                               overflow: hidden;
                                               min-width: 200px;">
                                    <span style="position: relative; z-index: 2;">
                                        <i class="fas fa-search me-2"></i>ค้นหา
                                    </span>
                                    <div style="position: absolute; top: 0; left: -100%; width: 100%; height: 100%; background: linear-gradient(90deg, transparent, rgba(255,255,255,0.2), transparent); transition: left 0.5s; z-index: 1;"
                                        class="btn-shine"></div>
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>

        <!-- Search Results -->
        <div id="searchResults" style="<?= $search_performed ? 'display: block;' : 'display: none;'; ?>">
            <?php if ($search_performed && !empty($suggestion_result)): ?>
                <!-- Single Result Display -->
                <div class="row justify-content-center">
                    <div class="col-lg-10">
                        <div class="card shadow-lg"
                            style="border: none; border-radius: 25px; background: linear-gradient(135deg, #ffffff 0%, #f8f9fa 100%); overflow: hidden;">

                            <!-- Status Header -->
                            <div class="card-header"
                                style="background: linear-gradient(135deg, rgba(76, 175, 80, 0.1) 0%, rgba(129, 199, 132, 0.1) 100%); border: none; padding: 2rem;">
                                <div class="row align-items-center">
                                    <div class="col-md-8">
                                        <h4 style="color: #2c3e50; font-weight: 700; margin-bottom: 0.5rem;">
                                            <i class="fas fa-file-alt me-2" style="color: #4caf50;"></i>
                                            ความคิดเห็น #<?= $suggestion_result['suggestions_id']; ?>
                                        </h4>
                                        <p class="mb-0" style="color: #6c757d; font-size: 1.1rem;">
                                            <i class="fas fa-calendar me-2"></i>
                                            ส่งเมื่อ:
                                            <?= $suggestion_result['date_thai'] ?? date('d/m/Y H:i', strtotime($suggestion_result['suggestions_datesave'])); ?>
                                        </p>
                                    </div>
                                    <div class="col-md-4 text-end">
                                        <span class="badge <?= $suggestion_result['status_class']; ?>"
                                            style="font-size: 1.1rem; padding: 0.7rem 1.5rem; border-radius: 12px; background-color: <?= $suggestion_result['status_color']; ?>;">
                                            <i class="<?= $suggestion_result['status_icon']; ?> me-2"></i>
                                            <?= $suggestion_result['status_display']; ?>
                                        </span>
                                    </div>
                                </div>
                            </div>

                            <!-- Content -->
                            <div class="card-body" style="padding: 2.5rem;">
                                <!-- Topic -->
                                <div class="mb-4">
                                    <h5 style="color: #495057; font-weight: 600; margin-bottom: 1rem;">
                                        <i class="fas fa-edit me-2" style="color: #4caf50;"></i>เรื่อง
                                    </h5>
                                    <p style="font-size: 1.2rem; color: #2c3e50; font-weight: 500;">
                                        <?= htmlspecialchars($suggestion_result['suggestions_topic']); ?>
                                    </p>
                                </div>

                                <!-- Details -->
                                <div class="mb-4">
                                    <h5 style="color: #495057; font-weight: 600; margin-bottom: 1rem;">
                                        <i class="fas fa-align-left me-2" style="color: #4caf50;"></i>รายละเอียด
                                    </h5>
                                    <div
                                        style="background: #f8f9fa; padding: 1.5rem; border-radius: 12px; border-left: 4px solid #4caf50;">
                                        <p style="color: #495057; line-height: 1.8; margin: 0; white-space: pre-wrap;">
                                            <?= htmlspecialchars($suggestion_result['suggestions_detail']); ?>
                                        </p>
                                    </div>
                                </div>

                                <!-- Contact Info -->
                                <div class="user-info-section">
                                    <h5>
                                        <i class="fas fa-user me-2" style="color: #4caf50;"></i>ข้อมูลผู้ส่ง
                                    </h5>

                                    <div class="row">
                                        <div class="col-md-6">
                                            <!-- ชื่อ - เซ็นเซอร์สำหรับ Guest/Public, แสดงเต็มสำหรับ Staff -->
                                            <div class="info-item">
                                                <small class="text-muted">ชื่อ:</small>
                                                <div class="info-value">
                                                    <?php if ($user_type === 'staff'): ?>
                                                        <span
                                                            class="fw-medium"><?= htmlspecialchars($suggestion_result['suggestions_by_original']); ?></span>
                                                        <i class="fas fa-eye ms-2 text-success"
                                                            title="แสดงข้อมูลเต็ม (Staff)"></i>
                                                    <?php else: ?>
                                                        <span
                                                            class="fw-medium"><?= htmlspecialchars($suggestion_result['suggestions_by_censored']); ?></span>
                                                        <i class="fas fa-eye-slash ms-2 text-muted"
                                                            title="ข้อมูลถูกเซ็นเซอร์"></i>
                                                    <?php endif; ?>
                                                </div>
                                            </div>

                                            <!-- เบอร์โทร - เซ็นเซอร์ -->
                                            <div class="info-item">
                                                <small class="text-muted">เบอร์โทรศัพท์:</small>
                                                <div class="info-value">
                                                    <?php if ($user_type === 'staff'): ?>
                                                        <span
                                                            class="fw-medium"><?= htmlspecialchars($suggestion_result['suggestions_phone_original']); ?></span>
                                                        <i class="fas fa-phone ms-2 text-success"
                                                            title="แสดงข้อมูลเต็ม (Staff)"></i>
                                                    <?php else: ?>
                                                        <span
                                                            class="fw-medium text-primary"><?= htmlspecialchars($suggestion_result['suggestions_phone_censored']); ?></span>
                                                        <i class="fas fa-user-shield ms-2 text-muted"
                                                            title="ข้อมูลถูกเซ็นเซอร์เพื่อความปลอดภัย"></i>
                                                    <?php endif; ?>
                                                </div>
                                            </div>
                                        </div>

                                        <div class="col-md-6">
                                            <!-- อีเมล - เซ็นเซอร์ -->
                                            <?php if (!empty($suggestion_result['suggestions_email_original'])): ?>
                                                <div class="info-item">
                                                    <small class="text-muted">อีเมล:</small>
                                                    <div class="info-value">
                                                        <?php if ($user_type === 'staff'): ?>
                                                            <span
                                                                class="fw-medium"><?= htmlspecialchars($suggestion_result['suggestions_email_original']); ?></span>
                                                            <i class="fas fa-envelope ms-2 text-success"
                                                                title="แสดงข้อมูลเต็ม (Staff)"></i>
                                                        <?php else: ?>
                                                            <span
                                                                class="fw-medium text-info"><?= htmlspecialchars($suggestion_result['suggestions_email_censored']); ?></span>
                                                            <i class="fas fa-user-shield ms-2 text-muted"
                                                                title="ข้อมูลถูกเซ็นเซอร์เพื่อความปลอดภัย"></i>
                                                        <?php endif; ?>
                                                    </div>
                                                </div>
                                            <?php endif; ?>

                                            <!-- เลขบัตรประชาชน - เซ็นเซอร์ -->
                                            <?php if (!empty($suggestion_result['suggestions_number_original'])): ?>
                                                <div class="info-item">
                                                    <small class="text-muted">เลขบัตรประชาชน:</small>
                                                    <div class="info-value">
                                                        <?php if ($user_type === 'staff'): ?>
                                                            <span
                                                                class="fw-medium"><?= htmlspecialchars($suggestion_result['suggestions_number_original']); ?></span>
                                                            <i class="fas fa-id-card ms-2 text-success"
                                                                title="แสดงข้อมูลเต็ม (Staff)"></i>
                                                        <?php else: ?>
                                                            <span
                                                                class="fw-medium text-warning"><?= htmlspecialchars($suggestion_result['suggestions_number_censored']); ?></span>
                                                            <i class="fas fa-user-shield ms-2 text-muted"
                                                                title="ข้อมูลถูกเซ็นเซอร์เพื่อความปลอดภัย"></i>
                                                        <?php endif; ?>
                                                    </div>
                                                </div>
                                            <?php endif; ?>
                                        </div>
                                    </div>
                                    <!-- แสดงข้อความแจ้งเตือนสำหรับผู้ใช้ทั่วไป -->
                                    <?php if ($user_type !== 'staff'): ?>
                                        <div class="privacy-notice mt-3">
                                            <div class="alert alert-info"
                                                style="border-radius: 12px; background: linear-gradient(135deg, rgba(23, 162, 184, 0.1) 0%, rgba(23, 162, 184, 0.15) 100%); border: 1px solid rgba(23, 162, 184, 0.3);">
                                                <i class="fas fa-shield-alt me-2"></i>
                                                <strong>การปกป้องข้อมูลส่วนบุคคล:</strong>
                                                ข้อมูลส่วนตัวบางส่วนถูกเซ็นเซอร์เพื่อความปลอดภัยและการปกป้องข้อมูลส่วนบุคคล
                                            </div>
                                        </div>
                                    <?php endif; ?>
                                </div>





                                <!-- Files -->
                                <?php if (!empty($suggestion_files)): ?>
                                    <div class="mb-4">
                                        <h5 style="color: #495057; font-weight: 600; margin-bottom: 1rem;">
                                            <i class="fas fa-paperclip me-2" style="color: #4caf50;"></i>ไฟล์แนบ
                                            (<?= count($suggestion_files); ?> ไฟล์)
                                        </h5>

                                    </div>
                                <?php endif; ?>

                                <!-- Reply -->
                                <?php if (!empty($suggestion_result['suggestions_reply'])): ?>
                                    <div class="mb-4">
                                        <h5 style="color: #495057; font-weight: 600; margin-bottom: 1rem;">
                                            <i class="fas fa-reply me-2" style="color: #17a2b8;"></i>การตอบกลับ
                                        </h5>
                                        <div
                                            style="background: linear-gradient(135deg, #e3f2fd 0%, #bbdefb 100%); padding: 1.5rem; border-radius: 12px; border-left: 4px solid #17a2b8;">
                                            <p
                                                style="color: #0d47a1; line-height: 1.8; margin-bottom: 1rem; white-space: pre-wrap;">
                                                <?= htmlspecialchars($suggestion_result['suggestions_reply']); ?>
                                            </p>
                                            <div class="d-flex justify-content-between align-items-center">
                                                <span style="color: #1565c0; font-weight: 600;">
                                                    <i class="fas fa-user-tie me-1"></i>
                                                    ตอบโดย:
                                                    <?= htmlspecialchars($suggestion_result['suggestions_replied_by'] ?? 'เจ้าหน้าที่'); ?>
                                                </span>
                                                <?php if (!empty($suggestion_result['suggestions_replied_at'])): ?>
                                                    <span style="color: #1976d2; font-size: 0.9rem;">
                                                        <i class="fas fa-clock me-1"></i>
                                                        <?= date('d/m/Y H:i', strtotime($suggestion_result['suggestions_replied_at'])); ?>
                                                    </span>
                                                <?php endif; ?>
                                            </div>
                                        </div>
                                    </div>
                                <?php endif; ?>

                                <!-- Timeline -->
                                <?php if (!empty($suggestion_history)): ?>
                                    <div class="mb-4">
                                        <h5 style="color: #495057; font-weight: 600; margin-bottom: 1.5rem;">
                                            <i class="fas fa-history me-2" style="color: #4caf50;"></i>ประวัติการดำเนินการ
                                        </h5>
                                        <div class="timeline">
                                            <?php foreach ($suggestion_history as $index => $history): ?>
                                                <div class="timeline-item"
                                                    style="position: relative; padding-left: 3rem; margin-bottom: 2rem;">
                                                    <div class="timeline-marker" style="position: absolute; left: 0; top: 0; width: 40px; height: 40px; 
                                                                background: linear-gradient(135deg, #4caf50 0%, #388e3c 100%); 
                                                                border-radius: 50%; display: flex; align-items: center; justify-content: center; 
                                                                box-shadow: 0 4px 15px rgba(76, 175, 80, 0.3);">
                                                        <i class="fas fa-circle" style="color: white; font-size: 0.8rem;"></i>
                                                    </div>
                                                    <?php if ($index < count($suggestion_history) - 1): ?>
                                                        <div class="timeline-line" style="position: absolute; left: 19px; top: 40px; bottom: -2rem; 
                                                                    width: 2px; background: #dee2e6;"></div>
                                                    <?php endif; ?>
                                                    <div
                                                        style="background: #f8f9fa; padding: 1.5rem; border-radius: 12px; margin-left: 1rem;">
                                                        <h6 style="color: #2c3e50; font-weight: 600; margin-bottom: 0.5rem;">
                                                            <?= htmlspecialchars($history['action_description']); ?>
                                                        </h6>
                                                        <div class="d-flex justify-content-between align-items-center">
                                                            <span style="color: #6c757d; font-size: 0.9rem;">
                                                                <i class="fas fa-user me-1"></i>
                                                                <?= htmlspecialchars($history['action_by']); ?>
                                                            </span>
                                                            <span style="color: #6c757d; font-size: 0.9rem;">
                                                                <i class="fas fa-clock me-1"></i>
                                                                <?= $history['date_thai'] ?? date('d/m/Y H:i', strtotime($history['action_date'])); ?>
                                                            </span>
                                                        </div>
                                                    </div>
                                                </div>
                                            <?php endforeach; ?>
                                        </div>
                                    </div>
                                <?php endif; ?>
                            </div>
                        </div>
                    </div>
                </div>
            <?php elseif ($search_performed): ?>
                <!-- No Results -->
                <div class="row justify-content-center">
                    <div class="col-lg-8">
                        <div class="text-center" style="padding: 3rem;">
                            <div
                                style="width: 120px; height: 120px; margin: 0 auto 2rem; background: linear-gradient(135deg, rgba(220, 53, 69, 0.15) 0%, rgba(220, 53, 69, 0.25) 100%); border-radius: 50%; display: flex; align-items: center; justify-content: center;">
                                <i class="fas fa-search-minus" style="font-size: 3.5rem; color: #dc3545;"></i>
                            </div>
                            <h4 style="color: #495057; font-weight: 600; margin-bottom: 1rem;">ไม่พบข้อมูล</h4>
                            <?php if ($is_logged_in && $user_type === 'public'): ?>
                                <p style="color: #6c757d; font-size: 1.1rem; margin-bottom: 2rem;">
                                    ไม่พบข้อมูลความคิดเห็นในบัญชีของคุณตามที่ค้นหา<br>
                                    <small>หรือความคิดเห็นนั้นอาจส่งโดยไม่ได้เข้าสู่ระบบ</small>
                                </p>
                            <?php else: ?>
                                <p style="color: #6c757d; font-size: 1.1rem; margin-bottom: 2rem;">
                                    ไม่พบข้อมูลความคิดเห็นตามที่ค้นหา กรุณาตรวจสอบข้อมูลและลองใหม่อีกครั้ง<br>
                                    <small>หรือความคิดเห็นนั้นอาจส่งผ่านบัญชีสมาชิก</small>
                                </p>
                            <?php endif; ?>
                            <button type="button" class="btn btn-outline-primary" onclick="clearSearch()"
                                style="border-radius: 12px; padding: 0.7rem 2rem;">
                                <i class="fas fa-search me-2"></i>ค้นหาใหม่
                            </button>
                        </div>
                    </div>
                </div>
            <?php endif; ?>
        </div>

        <!-- Quick Actions -->
        <div class="row justify-content-center mt-5">
            <div class="col-lg-8">
                <div class="text-center">
                    <h5 style="color: #495057; font-weight: 600; margin-bottom: 1.5rem;">ต้องการความช่วยเหลือเพิ่มเติม?
                    </h5>
                    <div class="row">
                        <div class="col-md-4 mb-3">
                            <a href="<?= site_url('Suggestions/adding_suggestions'); ?>"
                                class="btn btn-outline-success btn-lg w-100"
                                style="border-radius: 15px; padding: 1rem; border-width: 2px;">
                                <i class="fas fa-plus-circle me-2"></i>
                                <div>ส่งความคิดเห็นใหม่</div>
                            </a>
                        </div>
                        <div class="col-md-4 mb-3">
                            <a href="<?= site_url('User'); ?>" class="btn btn-outline-primary btn-lg w-100"
                                style="border-radius: 15px; padding: 1rem; border-width: 2px;">
                                <i class="fas fa-sign-in-alt me-2"></i>
                                <div>เข้าสู่ระบบ</div>
                            </a>
                        </div>
                        <div class="col-md-4 mb-3">
                            <a href="<?= site_url('Pages/contact'); ?>" class="btn btn-outline-info btn-lg w-100"
                                style="border-radius: 15px; padding: 1rem; border-width: 2px;">
                                <i class="fas fa-phone me-2"></i>
                                <div>ติดต่อเรา</div>
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>

    </div>
</div>

<!-- CSS -->
<style>
    @keyframes gradientShift {

        0%,
        100% {
            background-position: 0% 50%;
        }

        50% {
            background-position: 100% 50%;
        }
    }

    .search-option {
        position: relative;
    }

    .search-option .form-check-input {
        position: absolute !important;
        top: 10px !important;
        left: 62% !important;
        transform: translateX(-50%) scale(1.2) !important;
    }

    .search-option .form-check-label {
        margin-top: 25px !important;
    }

    .search-option:hover {
        background: rgba(76, 175, 80, 0.05) !important;
        border-color: rgba(76, 175, 80, 0.3) !important;
        transform: translateY(-2px);
        box-shadow: 0 4px 12px rgba(76, 175, 80, 0.1);
    }

    .search-option.active {
        background: rgba(76, 175, 80, 0.1) !important;
        border-color: #4caf50 !important;
        box-shadow: 0 4px 15px rgba(76, 175, 80, 0.2);
    }

    .search-btn:hover {
        transform: translateY(-2px);
        box-shadow: 0 8px 25px rgba(76, 175, 80, 0.4) !important;
        background: linear-gradient(135deg, #43a047 0%, #2e7d32 100%) !important;
    }

    .search-btn:hover .btn-shine {
        left: 100%;
    }

    .file-item:hover {
        transform: translateY(-2px);
        box-shadow: 0 4px 15px rgba(0, 0, 0, 0.1);
        border-color: #4caf50;
    }

    .timeline-item:last-child .timeline-line {
        display: none;
    }

    /* Responsive Design */
    @media (max-width: 768px) {
        .font-pages-head {
            font-size: 2rem !important;
        }

        .card-body {
            padding: 1.5rem !important;
        }

        .row .col-md-4 {
            margin-bottom: 1rem;
        }
    }
</style>

<!-- JavaScript -->
<script>
    let currentSearchType = 'ref';

    // *** เพิ่ม: Debug reCAPTCHA variables ตั้งแต่เริ่มต้น ***
    console.log('🔑 Initial reCAPTCHA check for Suggestions:');
    console.log('- RECAPTCHA_SITE_KEY:', typeof window.RECAPTCHA_SITE_KEY !== 'undefined' ? window.RECAPTCHA_SITE_KEY : 'UNDEFINED');
    console.log('- recaptchaReady:', typeof window.recaptchaReady !== 'undefined' ? window.recaptchaReady : 'UNDEFINED');
    console.log('- SKIP_RECAPTCHA_FOR_DEV:', typeof window.SKIP_RECAPTCHA_FOR_DEV !== 'undefined' ? window.SKIP_RECAPTCHA_FOR_DEV : 'UNDEFINED');
    console.log('- grecaptcha available:', typeof grecaptcha !== 'undefined');

    function selectSearchType(type) {
        currentSearchType = type;

        // Update active class
        document.querySelectorAll('.search-option').forEach(el => {
            el.classList.remove('active');
        });
        document.querySelector(`#search_${type}`).closest('.search-option').classList.add('active');

        // Update radio button
        document.querySelector(`#search_${type}`).checked = true;

        // Update labels and placeholders
        const labels = {
            'ref': {
                label: 'หมายเลขอ้างอิง',
                placeholder: 'กรอกหมายเลขอ้างอิง...',
                icon: 'fas fa-hashtag',
                help: 'กรอกหมายเลขอ้างอิงที่ได้รับหลังจากส่งความคิดเห็น'
            },
            'phone': {
                label: 'เบอร์โทรศัพท์',
                placeholder: 'กรอกเบอร์โทรศัพท์ 10 หลัก...',
                icon: 'fas fa-phone',
                help: 'กรอกเบอร์โทรศัพท์ที่ใช้ส่งความคิดเห็น (10 หลัก เริ่มต้นด้วย 0)'
            },
            'id_card': {
                label: 'เลขบัตรประชาชน',
                placeholder: 'กรอกเลขบัตรประชาชน 13 หลัก...',
                icon: 'fas fa-id-card',
                help: 'กรอกเลขบัตรประจำตัวประชาชนที่ใช้ส่งความคิดเห็น (13 หลัก)'
            }
        };

        document.getElementById('search_label').textContent = labels[type].label;
        document.getElementById('search_value').placeholder = labels[type].placeholder;
        document.getElementById('search_icon').className = labels[type].icon;
        document.getElementById('search_help').textContent = labels[type].help;

        // Clear current value
        document.getElementById('search_value').value = '';
    }

    function searchSuggestion(event) {
        event.preventDefault();

        const searchType = document.querySelector('input[name="search_type"]:checked').value;
        const searchValue = document.getElementById('search_value').value.trim();
        const searchBtn = document.getElementById('searchBtn');

        if (!searchValue) {
            Swal.fire({
                icon: 'warning',
                title: 'กรุณากรอกข้อมูล',
                text: 'กรุณากรอกข้อมูลที่ต้องการค้นหา',
                confirmButtonColor: '#4caf50'
            });
            return;
        }

        // Validation
        if (searchType === 'phone' && !/^0\d{9}$/.test(searchValue)) {
            Swal.fire({
                icon: 'error',
                title: 'รูปแบบเบอร์โทรไม่ถูกต้อง',
                text: 'กรุณากรอกเบอร์โทรศัพท์ 10 หลัก เริ่มต้นด้วย 0',
                confirmButtonColor: '#4caf50'
            });
            return;
        }

        if (searchType === 'id_card' && !/^\d{13}$/.test(searchValue)) {
            Swal.fire({
                icon: 'error',
                title: 'รูปแบบเลขบัตรประชาชนไม่ถูกต้อง',
                text: 'กรุณากรอกเลขบัตรประจำตัวประชาชน 13 หลัก (ตัวเลขเท่านั้น)',
                confirmButtonColor: '#4caf50'
            });
            return;
        }

        console.log('📝 Suggestion search submitted - Type:', searchType, 'Value:', searchValue);

        // Show loading
        const originalContent = searchBtn.innerHTML;
        searchBtn.disabled = true;
        searchBtn.innerHTML = '<i class="fas fa-spinner fa-spin me-2"></i>กำลังค้นหา...';

        // *** เพิ่ม: Debug reCAPTCHA status แบบละเอียด ***
        console.log('🔍 Checking reCAPTCHA status...');
        console.log('- RECAPTCHA_SITE_KEY:', window.RECAPTCHA_SITE_KEY);
        console.log('- recaptchaReady:', window.recaptchaReady);
        console.log('- SKIP_RECAPTCHA_FOR_DEV:', window.SKIP_RECAPTCHA_FOR_DEV);
        console.log('- grecaptcha available:', typeof grecaptcha !== 'undefined');

        // *** เพิ่ม: ตรวจสอบเงื่อนไข reCAPTCHA แบบละเอียด ***
        const hasRecaptchaKey = window.RECAPTCHA_SITE_KEY && window.RECAPTCHA_SITE_KEY !== '';
        const isRecaptchaReady = window.recaptchaReady === true;
        const isNotSkipDev = !window.SKIP_RECAPTCHA_FOR_DEV;
        const isGrecaptchaAvailable = typeof grecaptcha !== 'undefined';

        console.log('🔍 reCAPTCHA condition check:');
        console.log('- hasRecaptchaKey:', hasRecaptchaKey);
        console.log('- isRecaptchaReady:', isRecaptchaReady);
        console.log('- isNotSkipDev:', isNotSkipDev);
        console.log('- isGrecaptchaAvailable:', isGrecaptchaAvailable);

        const shouldUseRecaptcha = hasRecaptchaKey && isRecaptchaReady && isNotSkipDev && isGrecaptchaAvailable;
        console.log('🔍 Should use reCAPTCHA:', shouldUseRecaptcha);

        // ตรวจสอบว่ามี reCAPTCHA หรือไม่
        if (shouldUseRecaptcha) {
            console.log('🛡️ Executing reCAPTCHA...');

            grecaptcha.ready(function () {
                console.log('🔧 grecaptcha.ready() called');

                grecaptcha.execute(window.RECAPTCHA_SITE_KEY, {
                    action: 'suggestions_search'
                }).then(function (token) {
                    console.log('✅ reCAPTCHA token received:', token.substring(0, 50) + '...');
                    console.log('📏 Token length:', token.length);

                    performSearchWithRecaptcha(searchType, searchValue, token, searchBtn, originalContent);
                }).catch(function (error) {
                    console.error('❌ reCAPTCHA execution failed:', error);
                    console.log('🔄 Falling back to search without reCAPTCHA');
                    performSearchWithoutRecaptcha(searchType, searchValue, searchBtn, originalContent);
                });
            });
        } else {
            console.log('⚠️ reCAPTCHA not available, searching without verification');
            console.log('📋 Reasons breakdown:');
            console.log('- SITE_KEY exists:', !!window.RECAPTCHA_SITE_KEY);
            console.log('- reCAPTCHA ready:', !!window.recaptchaReady);
            console.log('- Skip dev mode:', !!window.SKIP_RECAPTCHA_FOR_DEV);
            console.log('- grecaptcha available:', typeof grecaptcha !== 'undefined');

            performSearchWithoutRecaptcha(searchType, searchValue, searchBtn, originalContent);
        }
    }

    // *** เพิ่ม: Search Function พร้อม reCAPTCHA ***
    function performSearchWithRecaptcha(searchType, searchValue, recaptchaToken, searchBtn, originalContent) {
        console.log('📤 Submitting with reCAPTCHA token...');

        // Send AJAX request with reCAPTCHA
        const formData = new FormData();
        formData.append('search_type', searchType);
        formData.append('search_value', searchValue);
        formData.append('g-recaptcha-response', recaptchaToken);
        formData.append('recaptcha_action', 'suggestions_search');
        formData.append('recaptcha_source', 'suggestions_search_form');
        formData.append('ajax_request', '1');
        formData.append('client_timestamp', new Date().toISOString());
        formData.append('user_agent_info', navigator.userAgent);
        formData.append('is_anonymous', '0');

        console.log('📦 FormData contents:');
        for (let [key, value] of formData.entries()) {
            if (key === 'g-recaptcha-response') {
                console.log('- ' + key + ':', value.substring(0, 50) + '...');
            } else {
                console.log('- ' + key + ':', value);
            }
        }

        fetch('<?= site_url('Suggestions/search_suggestion'); ?>', {
            method: 'POST',
            body: formData
        })
            .then(response => response.json())
            .then(data => {
                handleSearchResponse(data, searchValue, searchType);
            })
            .catch(error => {
                handleSearchError(error);
            })
            .finally(() => {
                restoreSearchButton(searchBtn, originalContent);
            });
    }

    // *** เพิ่ม: Search Function แบบปกติ ***
    function performSearchWithoutRecaptcha(searchType, searchValue, searchBtn, originalContent) {
        console.log('📤 Submitting without reCAPTCHA...');

        // Send AJAX request without reCAPTCHA
        const formData = new FormData();
        formData.append('search_type', searchType);
        formData.append('search_value', searchValue);
        formData.append('dev_mode', '1');

        fetch('<?= site_url('Suggestions/search_suggestion'); ?>', {
            method: 'POST',
            body: formData
        })
            .then(response => response.json())
            .then(data => {
                handleSearchResponse(data, searchValue, searchType);
            })
            .catch(error => {
                handleSearchError(error);
            })
            .finally(() => {
                restoreSearchButton(searchBtn, originalContent);
            });
    }

    // *** เพิ่ม: จัดการ Response ***
    function handleSearchResponse(data, searchValue, searchType) {
        if (data.success) {
            // Redirect to follow_suggestions with result
            window.location.href = `<?= site_url('Suggestions/follow_suggestions'); ?>?ref=${encodeURIComponent(searchValue)}&type=${searchType}`;
        } else {
            let errorMessage = data.message || 'ไม่พบข้อมูลความคิดเห็นตามที่ค้นหา';

            // จัดการ error จาก reCAPTCHA
            if (data.error_type === 'recaptcha_failed') {
                errorMessage = 'การยืนยันความปลอดภัยไม่ผ่าน กรุณาลองใหม่อีกครั้ง';
            } else if (data.error_type === 'recaptcha_missing') {
                errorMessage = 'ไม่พบข้อมูลการยืนยันความปลอดภัย';
            }

            Swal.fire({
                icon: 'error',
                title: 'ไม่พบข้อมูล',
                text: errorMessage,
                confirmButtonColor: '#4caf50'
            });
        }
    }

    // *** เพิ่ม: จัดการ Error ***
    function handleSearchError(error) {
        console.error('Search error:', error);
        Swal.fire({
            icon: 'error',
            title: 'เกิดข้อผิดพลาด',
            text: 'ไม่สามารถค้นหาข้อมูลได้ กรุณาลองใหม่อีกครั้ง',
            confirmButtonColor: '#4caf50'
        });
    }

    // *** เพิ่ม: คืนค่าปุ่มเป็นสถานะเดิม ***
    function restoreSearchButton(searchBtn, originalContent) {
        searchBtn.disabled = false;
        searchBtn.innerHTML = originalContent;
    }

    function clearSearch() {
        document.getElementById('search_value').value = '';
        document.getElementById('searchResults').style.display = 'none';
        selectSearchType('ref'); // Reset to default
    }

    // Initialize on page load
    document.addEventListener('DOMContentLoaded', function () {
        selectSearchType('ref');

        // Auto-focus on search input if ref parameter exists
        const urlParams = new URLSearchParams(window.location.search);
        const ref = urlParams.get('ref');
        if (ref) {
            document.getElementById('search_value').focus();
        }

        // *** เพิ่ม: ตรวจสอบการโหลด reCAPTCHA ***
        if (window.RECAPTCHA_SITE_KEY && !window.recaptchaReady) {
            console.log('⏳ Waiting for reCAPTCHA to load...');

            let checkInterval = setInterval(function () {
                if (window.recaptchaReady) {
                    console.log('✅ reCAPTCHA is now ready');
                    clearInterval(checkInterval);
                }
            }, 100);

            setTimeout(function () {
                if (!window.recaptchaReady) {
                    console.log('⚠️ reCAPTCHA timeout after 10 seconds');
                    clearInterval(checkInterval);
                }
            }, 10000);
        }
    });
</script>

<!-- Load required libraries -->
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>