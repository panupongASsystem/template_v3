<?php
defined('BASEPATH') or exit('No direct script access allowed');
?>

<!-- Custom CSS for Theme Management + Dynamic Theme CSS -->
<style>
    /* ========== CSS สำหรับระบบธีมที่จะเปลี่ยนแปลงได้ ========== */
    .bg-gradient-custom {
        background-color: <?php echo isset($current_theme) ? $current_theme->primary_color : '#179CB1'; ?> !important;
    }

    .btn-custom {
        background-color: <?php echo isset($current_theme) ? $current_theme->primary_color : '#179CB1'; ?> !important;
    }

    .btn-custom:hover {
        background-color: <?php echo isset($current_theme) ? $current_theme->primary_color : '#179CB1'; ?> !important;
        opacity: 0.9;
    }

    /* CSS Variables สำหรับระบบธีม */
    :root {
        --primary-color: <?php echo isset($current_theme) ? $current_theme->primary_color : '#179CB1'; ?>;
        --gradient-start: <?php echo isset($current_theme) ? $current_theme->gradient_start : '#667eea'; ?>;
        --gradient-end: <?php echo isset($current_theme) ? $current_theme->gradient_end : '#764ba2'; ?>;
    }

    /* Footer gradient ที่จะเปลี่ยนตามธีม */
    .sticky-footer {
        background: linear-gradient(135deg, <?php echo isset($current_theme) ? $current_theme->gradient_start : '#667eea'; ?> 0%, <?php echo isset($current_theme) ? $current_theme->gradient_end : '#764ba2'; ?> 100%) !important;
    }

    /* Sidebar gradient */
    .sidebar.bg-gradient-custom {
        background: linear-gradient(135deg, <?php echo isset($current_theme) ? $current_theme->primary_color : '#667eea'; ?> 0%, <?php echo isset($current_theme) ? $current_theme->gradient_start : '#764ba2'; ?> 100%) !important;
    }

    /* ========== CSS สำหรับหน้าจัดการธีม ========== */
    .theme-card {
        border: 2px solid transparent;
        border-radius: 15px;
        transition: all 0.3s ease;
        cursor: pointer;
        position: relative;
        overflow: hidden;
    }

    .theme-card:hover {
        transform: translateY(-5px);
        box-shadow: 0 10px 30px rgba(0, 0, 0, 0.2);
    }

    .theme-card.active {
        border-color: #007bff;
        box-shadow: 0 0 20px rgba(0, 123, 255, 0.3);
    }

    .theme-preview {
        height: 120px;
        position: relative;
        overflow: hidden;
    }

    .theme-gradient {
        width: 100%;
        height: 100%;
    }

    .theme-name {
        position: absolute;
        bottom: 10px;
        left: 15px;
        color: white;
        font-weight: 600;
        text-shadow: 1px 1px 3px rgba(0, 0, 0, 0.5);
    }

    .check-icon {
        position: absolute;
        top: 10px;
        right: 10px;
        background: rgba(255, 255, 255, 0.9);
        border-radius: 50%;
        width: 30px;
        height: 30px;
        display: flex;
        align-items: center;
        justify-content: center;
        opacity: 0;
        transition: opacity 0.3s ease;
    }

    .theme-card.active .check-icon {
        opacity: 1;
    }

    .custom-theme-section {
        background: linear-gradient(135deg, #f8f9fa 0%, #e9ecef 100%);
        border-radius: 15px;
        padding: 25px;
        margin-top: 30px;
    }

    .color-picker-wrapper {
        position: relative;
        display: inline-block;
    }

    .color-preview {
        width: 50px;
        height: 50px;
        border-radius: 10px;
        border: 3px solid #dee2e6;
        cursor: pointer;
        transition: all 0.3s ease;
    }

    .color-preview:hover {
        transform: scale(1.1);
        border-color: #007bff;
    }

    .color-input {
        position: absolute;
        top: 0;
        left: 0;
        width: 100%;
        height: 100%;
        opacity: 0;
        cursor: pointer;
        z-index: 10;
        /* เพิ่มบรรทัดนี้ */
    }

    .preview-section {
        background: white;
        border-radius: 15px;
        padding: 20px;
        box-shadow: 0 5px 15px rgba(0, 0, 0, 0.1);
    }

    .sidebar-preview {
        background: var(--primary-color, <?php echo isset($current_theme) ? $current_theme->primary_color : '#179CB1'; ?>);
        color: white;
        padding: 15px;
        border-radius: 10px;
        margin-bottom: 15px;
    }

    .button-preview {
        background: var(--primary-color, <?php echo isset($current_theme) ? $current_theme->primary_color : '#179CB1'; ?>);
        color: white;
        border: none;
        padding: 10px 20px;
        border-radius: 5px;
        margin: 5px;
        transition: all 0.3s ease;
    }

    .button-preview:hover {
        opacity: 0.9;
        transform: translateY(-1px);
    }

    .footer-preview {
        background: var(--gradient-start, <?php echo isset($current_theme) ? $current_theme->gradient_start : '#667eea'; ?>);
        background: linear-gradient(135deg, var(--gradient-start, <?php echo isset($current_theme) ? $current_theme->gradient_start : '#667eea'; ?>) 0%, var(--gradient-end, <?php echo isset($current_theme) ? $current_theme->gradient_end : '#764ba2'; ?>) 100%);
        color: white;
        padding: 15px;
        border-radius: 0 0 10px 10px;
        text-align: center;
    }

    /* การ์ดประวัติ */
    .history-item {
        border-left: 4px solid var(--primary-color);
        padding: 15px;
        margin-bottom: 15px;
        background-color: #f8f9fa;
        border-radius: 8px;
        transition: all 0.3s ease;
    }

    .history-item:hover {
        transform: translateY(-2px);
        box-shadow: 0 4px 15px rgba(0, 0, 0, 0.1);
    }

    .color-dot {
        display: inline-block;
        width: 20px;
        height: 20px;
        border-radius: 50%;
        margin-right: 8px;
        border: 2px solid #fff;
        box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
    }

    /* Animation */
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

    .fade-in-up {
        animation: fadeInUp 0.6s ease-out;
    }

    /* Floating Preview */
    .floating-preview {
        position: fixed;
        bottom: 100px;
        right: 0px;
        width: 340px;
        max-height: 600px;
        overflow-y: auto;
        z-index: 1000;
        transform: translateY(calc(100% - -55px));
        transition: all 0.3s ease;
    }

    .floating-preview.show {
        transform: translateY(0);
    }

    .floating-handle {
        background: linear-gradient(135deg, var(--primary-color, #179CB1), var(--gradient-start, #667eea));
        color: white;
        padding: 12px 20px;
        text-align: center;
        cursor: pointer;
        border-radius: 10px 10px 0 0;
        font-weight: 500;
        display: flex;
        justify-content: space-between;
        align-items: center;
    }

    .floating-handle:hover {
        opacity: 0.9;
    }

    /* Responsive */
    @media (max-width: 768px) {
        .floating-preview {
            width: calc(100% - 40px);
            right: 20px;
            left: 20px;
        }
    }

    /* ซ่อนการ์ดตัวอย่างเดิม */
    .original-preview-column {
        display: none;
    }

    /* Profile Slots */
    .profile-slot {
        border: 2px solid #e9ecef;
        border-radius: 10px;
        padding: 15px;
        transition: all 0.3s ease;
        background: white;
    }

    .profile-slot:hover {
        border-color: #007bff;
        transform: translateY(-2px);
        box-shadow: 0 4px 10px rgba(0, 0, 0, 0.1);
    }

    .profile-preview {
        cursor: pointer;
        text-align: center;
    }

    .profile-colors {
        display: flex;
        justify-content: center;
        gap: 5px;
        margin-bottom: 10px;
    }

    .profile-color {
        width: 30px;
        height: 30px;
        border-radius: 50%;
        border: 2px solid #fff;
        box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
        background: #f8f9fa;
    }

    .profile-name {
        display: block;
        font-weight: 500;
        color: #666;
    }

    .profile-slot.has-data {
        border-color: #28a745;
    }

    .profile-slot.has-data .profile-name {
        color: #28a745;
        font-weight: 600;
    }


    .color-code-input {
        font-family: 'Courier New', monospace;
        font-size: 13px;
        text-align: center;
        border: 1px solid #ced4da;
        transition: all 0.3s ease;
    }

    .color-code-input:focus {
        border-color: #007bff;
        box-shadow: 0 0 0 0.2rem rgba(0, 123, 255, 0.25);
    }

    .color-code-input.valid {
        border-color: #28a745;
        background-color: #f8fff9;
    }

    .color-code-input.invalid {
        border-color: #dc3545;
        background-color: #fff5f5;
    }
</style>

<!-- Page Heading -->
<div class="d-sm-flex align-items-center justify-content-between mb-4">
    <div>
        <h1 class="h3 mb-0 text-gray-800">
            <i class="fas fa-palette me-2"></i>จัดการธีมระบบ
        </h1>
        <?php if (isset($current_theme) && isset($current_theme->updated_by)): ?>
            <small class="text-muted">
                อัปเดตล่าสุดโดย: User ID <?php echo $current_theme->updated_by; ?>
                เมื่อ <?php echo date('d/m/Y H:i', strtotime($current_theme->updated_at)); ?>
            </small>
        <?php endif; ?>
    </div>
    <div>
        <button class="btn btn-warning btn-sm shadow-sm mr-2" onclick="resetTheme()">
            <i class="fas fa-undo fa-sm text-white-50"></i> รีเซ็ต
        </button>
        <button class="btn btn-success btn-sm shadow-sm" onclick="saveTheme()">
            <i class="fas fa-save fa-sm text-white-50"></i> บันทึกการตั้งค่า
        </button>
    </div>
</div>

<div class="row">
    <!-- ธีมที่กำหนดไว้แล้ว -->
    <div class="col-lg-8"> <!-- เปลี่ยนจาก col-lg-8 -->
        <!-- ส่วนกำหนดธีมเอง -->
        <div class="card shadow mb-4 fade-in-up">
            <div class="card-header py-3">
                <h6 class="m-0 font-weight-bold text-primary">
                    <i class="fas fa-paint-brush me-2"></i>กำหนดธีมเอง
                </h6>
            </div>
            <div class="card-body">
                <div class="custom-theme-section">
                    <div class="row">
                        <div class="col-md-4 mb-3">
                            <label class="form-label font-weight-bold">สีหลักของระบบ</label>
                            <div class="color-picker-wrapper">
                                <div class="color-preview" id="primary-preview" style="background-color: <?php echo isset($current_theme) ? $current_theme->primary_color : '#179CB1'; ?>;"></div>
                                <input type="color" class="color-input" id="primary-color" value="<?php echo isset($current_theme) ? $current_theme->primary_color : '#179CB1'; ?>" onchange="updateCustomTheme()">
                            </div>
                            <!-- เพิ่มช่องใส่โค้ดสี -->
                            <div class="mt-2">
                                <input type="text" class="form-control form-control-sm color-code-input"
                                    id="primary-color-code"
                                    placeholder="#179CB1"
                                    value="<?php echo isset($current_theme) ? $current_theme->primary_color : '#179CB1'; ?>"
                                    onchange="updateColorFromCode('primary')"
                                    pattern="^#[0-9A-Fa-f]{6}$">
                            </div>
                            <small class="text-muted mt-1 d-block">สีของปุ่ม, sidebar และองค์ประกอบหลัก</small>
                        </div>
                        <div class="col-md-4 mb-3">
                            <label class="form-label font-weight-bold">สีเริ่มต้น Gradient</label>
                            <div class="color-picker-wrapper">
                                <div class="color-preview" id="gradient-start-preview" style="background-color: <?php echo isset($current_theme) ? $current_theme->gradient_start : '#667eea'; ?>;"></div>
                                <input type="color" class="color-input" id="gradient-start" value="<?php echo isset($current_theme) ? $current_theme->gradient_start : '#667eea'; ?>" onchange="updateCustomTheme()">
                            </div>
                            <!-- สีเริ่มต้น Gradient -->
                            <div class="mt-2">
                                <input type="text" class="form-control form-control-sm color-code-input"
                                    id="gradient-start-code"
                                    placeholder="#667eea"
                                    value="<?php echo isset($current_theme) ? $current_theme->gradient_start : '#667eea'; ?>"
                                    onchange="updateColorFromCode('gradient-start')"
                                    pattern="^#[0-9A-Fa-f]{6}$">
                            </div>
                            <small class="text-muted mt-1 d-block">สีเริ่มต้นของ footer gradient</small>
                        </div>
                        <div class="col-md-4 mb-3">
                            <label class="form-label font-weight-bold">สีสิ้นสุด Gradient</label>
                            <div class="color-picker-wrapper">
                                <div class="color-preview" id="gradient-end-preview" style="background-color: <?php echo isset($current_theme) ? $current_theme->gradient_end : '#764ba2'; ?>;"></div>
                                <input type="color" class="color-input" id="gradient-end" value="<?php echo isset($current_theme) ? $current_theme->gradient_end : '#764ba2'; ?>" onchange="updateCustomTheme()">
                            </div>
                            <!-- สีสิ้นสุด Gradient -->
                            <div class="mt-2">
                                <input type="text" class="form-control form-control-sm color-code-input"
                                    id="gradient-end-code"
                                    placeholder="#764ba2"
                                    value="<?php echo isset($current_theme) ? $current_theme->gradient_end : '#764ba2'; ?>"
                                    onchange="updateColorFromCode('gradient-end')"
                                    pattern="^#[0-9A-Fa-f]{6}$">
                            </div>
                            <small class="text-muted mt-1 d-block">สีสิ้นสุดของ footer gradient</small>
                        </div>
                    </div>
                    <div class="text-center">
                        <button class="btn btn-primary mr-2" onclick="applyCustomTheme()">
                            <i class="fas fa-eye me-2"></i>ดูตัวอย่าง
                        </button>
                        <button class="btn btn-secondary mr-2" onclick="randomTheme()">
                            <i class="fas fa-random me-2"></i>สุ่มสี
                        </button>
                        <button class="btn btn-info mr-2" onclick="exportTheme()">
                            <i class="fas fa-download me-2"></i>ส่งออก
                        </button>
                        <button class="btn btn-success" onclick="importTheme()">
                            <i class="fas fa-upload me-2"></i>นำเข้า
                        </button>
                    </div>

                    <!-- เพิ่มส่วนนี้ -->
                    <hr class="my-4">
                    <div class="row">
                        <div class="col-12">
                            <h6 class="font-weight-bold mb-3">
                                <i class="fas fa-bookmark me-2"></i>บันทึกโปรไฟล์สี
                            </h6>
                            <div class="row">
                                <div class="col-md-4 mb-3">
                                    <div class="profile-slot" id="profile-1">
                                        <div class="profile-preview" onclick="loadProfile(1)">
                                            <div class="profile-colors">
                                                <div class="profile-color" id="profile-1-primary"></div>
                                                <div class="profile-color" id="profile-1-start"></div>
                                                <div class="profile-color" id="profile-1-end"></div>
                                            </div>
                                            <small class="profile-name">Profile 1</small>
                                        </div>
                                        <button class="btn btn-sm btn-outline-success btn-block mt-2" onclick="saveProfile(1)">
                                            <i class="fas fa-save me-1"></i>บันทึก
                                        </button>
                                    </div>
                                </div>
                                <div class="col-md-4 mb-3">
                                    <div class="profile-slot" id="profile-2">
                                        <div class="profile-preview" onclick="loadProfile(2)">
                                            <div class="profile-colors">
                                                <div class="profile-color" id="profile-2-primary"></div>
                                                <div class="profile-color" id="profile-2-start"></div>
                                                <div class="profile-color" id="profile-2-end"></div>
                                            </div>
                                            <small class="profile-name">Profile 2</small>
                                        </div>
                                        <button class="btn btn-sm btn-outline-success btn-block mt-2" onclick="saveProfile(2)">
                                            <i class="fas fa-save me-1"></i>บันทึก
                                        </button>
                                    </div>
                                </div>
                                <div class="col-md-4 mb-3">
                                    <div class="profile-slot" id="profile-3">
                                        <div class="profile-preview" onclick="loadProfile(3)">
                                            <div class="profile-colors">
                                                <div class="profile-color" id="profile-3-primary"></div>
                                                <div class="profile-color" id="profile-3-start"></div>
                                                <div class="profile-color" id="profile-3-end"></div>
                                            </div>
                                            <small class="profile-name">Profile 3</small>
                                        </div>
                                        <button class="btn btn-sm btn-outline-success btn-block mt-2" onclick="saveProfile(3)">
                                            <i class="fas fa-save me-1"></i>บันทึก
                                        </button>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- ส่วนธีมที่กำหนดไว้ -->
        <div class="card shadow mb-4 fade-in-up">
            <div class="card-header py-3">
                <h6 class="m-0 font-weight-bold text-primary">
                    <i class="fas fa-swatchbook me-2"></i>ธีมที่กำหนดไว้
                </h6>
            </div>
            <div class="card-body">
                <div class="row" id="predefined-themes">
                    <!-- ธีมจะถูกสร้างด้วย JavaScript -->
                </div>
            </div>
        </div>


    </div>

    <!-- ตัวอย่างธีม -->
    <div class="col-lg-4">
        <!-- การ์ดข้อมูลธีมปัจจุบัน -->
        <div class="card shadow mb-4 fade-in-up">
            <div class="card-header py-3">
                <h6 class="m-0 font-weight-bold text-primary">
                    <i class="fas fa-info-circle me-2"></i>ข้อมูลธีมปัจจุบัน
                </h6>
            </div>
            <div class="card-body">
                <div class="row text-center">
                    <div class="col-4">
                        <div class="color-dot" style="background-color: <?php echo isset($current_theme) ? $current_theme->primary_color : '#179CB1'; ?>; margin: 0 auto;"></div>
                        <small class="d-block mt-1">สีหลัก</small>
                        <code class="small" onclick="copyColorCode(this.textContent)" style="cursor: pointer;" title="คลิกเพื่อคัดลอก"><?php echo isset($current_theme) ? $current_theme->primary_color : '#179CB1'; ?></code>
                    </div>
                    <div class="col-4">
                        <div class="color-dot" style="background-color: <?php echo isset($current_theme) ? $current_theme->gradient_start : '#667eea'; ?>; margin: 0 auto;"></div>
                        <small class="d-block mt-1">เริ่มต้น</small>
                        <code class="small" onclick="copyColorCode(this.textContent)" style="cursor: pointer;" title="คลิกเพื่อคัดลอก"><?php echo isset($current_theme) ? $current_theme->gradient_start : '#667eea'; ?></code>
                    </div>
                    <div class="col-4">
                        <div class="color-dot" style="background-color: <?php echo isset($current_theme) ? $current_theme->gradient_end : '#764ba2'; ?>; margin: 0 auto;"></div>
                        <small class="d-block mt-1">สิ้นสุด</small>
                        <code class="small" onclick="copyColorCode(this.textContent)" style="cursor: pointer;" title="คลิกเพื่อคัดลอก"><?php echo isset($current_theme) ? $current_theme->gradient_end : '#764ba2'; ?></code>
                    </div>
                </div>
                <hr>
                <div class="text-center">
                    <h6 class="mb-1"><?php echo isset($current_theme) ? $current_theme->theme_name : 'Default Theme'; ?></h6>
                    <small class="text-muted">ชื่อธีมปัจจุบัน</small>
                </div>
            </div>
        </div>

        <!-- ประวัติการเปลี่ยนธีม -->
        <div class="card shadow mb-4 fade-in-up">
            <div class="card-header py-3">
                <h6 class="m-0 font-weight-bold text-primary">
                    <i class="fas fa-history me-2"></i>ประวัติการเปลี่ยนธีม
                    <?php if (!empty($theme_history)): ?>
                        <span class="badge badge-info ml-2"><?php echo count($theme_history); ?></span>
                    <?php endif; ?>
                </h6>
            </div>
            <div class="card-body">
                <?php if (!empty($theme_history)): ?>
                    <div class="theme-history-list" style="max-height: 1250px; overflow-y: auto;">
                        <?php foreach ($theme_history as $index => $item): ?>
                            <div class="history-item mb-3" style="border-left-color: <?php echo $item->primary_color; ?>;">
                                <div class="d-flex justify-content-between align-items-start mb-2">
                                    <h6 class="mb-1 font-weight-bold"><?php echo htmlspecialchars($item->theme_name ?: 'Unknown Theme'); ?></h6>
                                    <small class="badge badge-secondary">#<?php echo $index + 1; ?></small>
                                </div>

                                <!-- แสดงสีของธีม -->
                                <div class="row mb-2">
                                    <div class="col-4 text-center">
                                        <div class="color-dot mx-auto mb-1" style="background-color: <?php echo $item->primary_color; ?>; width: 20px; height: 20px;"></div>
                                        <small class="d-block">หลัก</small>
                                        <code class="small" onclick="copyColorCode(this.textContent)" style="cursor: pointer;" title="คลิกเพื่อคัดลอก"><?php echo $item->primary_color; ?></code>
                                    </div>
                                    <div class="col-4 text-center">
                                        <div class="color-dot mx-auto mb-1" style="background-color: <?php echo $item->gradient_start ?: $item->primary_color; ?>; width: 20px; height: 20px;"></div>
                                        <small class="d-block">เริ่ม</small>
                                        <code class="small"><?php echo $item->gradient_start ?: 'N/A'; ?></code>
                                    </div>
                                    <div class="col-4 text-center">
                                        <div class="color-dot mx-auto mb-1" style="background-color: <?php echo $item->gradient_end ?: $item->primary_color; ?>; width: 20px; height: 20px;"></div>
                                        <small class="d-block">สิ้นสุด</small>
                                        <code class="small"><?php echo $item->gradient_end ?: 'N/A'; ?></code>
                                    </div>
                                </div>

                                <!-- ข้อมูลเพิ่มเติม -->
                                <div class="text-muted mb-2">
                                    <small>
                                        <i class="fas fa-user me-1"></i>User ID: <?php echo $item->updated_by ?: 'N/A'; ?>
                                        <br>
                                        <i class="fas fa-clock me-1"></i><?php echo $item->updated_at ? date('d/m/Y H:i:s', strtotime($item->updated_at)) : 'N/A'; ?>
                                    </small>
                                </div>

                                <!-- ปุ่มใช้ธีมนี้ -->
                                <div class="mt-2">
                                    <button class="btn btn-sm btn-outline-primary me-2" onclick="applyHistoryTheme('<?php echo $item->primary_color; ?>', '<?php echo $item->gradient_start; ?>', '<?php echo $item->gradient_end; ?>', '<?php echo htmlspecialchars($item->theme_name); ?>')">
                                        <i class="fas fa-paint-brush me-1"></i>ใช้ธีมนี้
                                    </button>
                                    <button class="btn btn-sm btn-outline-info" onclick="showThemePreview('<?php echo $item->primary_color; ?>', '<?php echo $item->gradient_start; ?>', '<?php echo $item->gradient_end; ?>')">
                                        <i class="fas fa-eye me-1"></i>ดูตัวอย่าง
                                    </button>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    </div>

                    <!-- ปุ่มดูประวัติทั้งหมด -->
                    <div class="text-center mt-3">
                        <button class="btn btn-info btn-sm" onclick="showAllHistory()">
                            <i class="fas fa-list me-1"></i>ดูประวัติทั้งหมด
                        </button>
                        <button class="btn btn-secondary btn-sm ml-2" onclick="exportHistory()">
                            <i class="fas fa-download me-1"></i>ส่งออกประวัติ
                        </button>
                    </div>

                <?php else: ?>
                    <!-- ไม่มีประวัติ -->
                    <div class="text-center text-muted py-4">
                        <i class="fas fa-history fa-3x mb-3" style="opacity: 0.3;"></i>
                        <h5>ยังไม่มีประวัติการเปลี่ยนธีม</h5>
                        <p class="mb-0">เมื่อคุณบันทึกธีมใหม่ ประวัติจะแสดงที่นี่</p>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </div>

    <!-- เพิ่ม Floating Preview -->
    <div id="floating-preview" class="floating-preview">
        <div class="floating-handle" onclick="toggleFloating()">
            <span><i class="fas fa-eye me-2"></i>ตัวอย่างธีม</span>
            <i class="fas fa-chevron-up" id="float-icon"></i>
        </div>
        <div class="card" style="border-radius: 0 0 10px 10px; margin: 0;">
            <div class="card-body">
                <div class="preview-section">
                    <div class="sidebar-preview">
                        <i class="fas fa-home me-2"></i>หน้าหลัก
                    </div>
                    <div class="sidebar-preview">
                        <i class="fas fa-users me-2"></i>จัดการสมาชิก
                    </div>
                    <div class="sidebar-preview">
                        <i class="fas fa-cog me-2"></i>จัดการข้อมูล
                    </div>
                    <div class="mb-3">
                        <button class="button-preview">ปุ่มหลัก</button>
                        <button class="button-preview">ปุ่มรอง</button>
                    </div>
                    <div class="footer-preview">
                        © <?php echo date('Y'); ?> สงวนลิขสิทธิ์โดย ASSYSTEM.co.th
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
    // ฟังก์ชันหาชื่อสีจากรหัส Hex
    function getColorName(hexColor) {
        const colorMap = {
            // น้ำเงิน
            '#179CB1': 'Teal Blue',
            '#1E3A8A': 'Corporate Blue',
            '#1E40AF': 'Royal Navy',
            '#0F4C75': 'Steel Blue',
            '#0078D4': 'Azure',
            '#003366': 'Deep Blue',
            '#0284C7': 'Sky Blue Executive',

            // เขียว
            '#65A30D': 'Lime Green Professional',
            '#059669': 'Forest Green',
            '#065F46': 'Emerald',
            '#166534': 'Sage Green',
            '#0D4F3C': 'Pine Green',

            // ม่วง
            '#7C3AED': 'Royal Purple',
            '#5B21B6': 'Deep Violet',
            '#3730A3': 'Indigo',
            '#6B46C1': 'Amethyst',

            // แดง
            '#B91C1C': 'Crimson',
            '#991B1B': 'Ruby Red',
            '#7F1D1D': 'Cardinal Red',

            // ส้ม
            '#D97706': 'Amber',
            '#EA580C': 'Sunset Orange',
            '#C2410C': 'Tangerine',

            // น้ำตาล
            '#78350F': 'Coffee Brown',
            '#92400E': 'Mahogany',

            // ทอง
            '#B45309': 'Golden',

            // เซียน
            '#0F766E': 'Teal',
            '#0E7490': 'Cyan',
            '#0D9488': 'Turquoise',

            // ชมพู
            '#BE185D': 'Rose',
            '#A21CAF': 'Magenta',
            '#86198F': 'Fuchsia'
        };

        return colorMap[hexColor.toUpperCase()] || 'สีกำหนดเอง';
    }

    // สร้างชื่อธีมปัจจุบันแบบไดนามิก
    function getCurrentThemeName() {
        const currentPrimary = '<?php echo isset($current_theme) ? $current_theme->primary_color : "#179CB1"; ?>';
        const colorName = getColorName(currentPrimary);
        return `ธีมปัจจุบัน (${colorName})`;
    }

    // ธีมที่กำหนดไว้แล้ว - สีหลักทางการ
    const predefinedThemes = [{
            name: getCurrentThemeName(), // จะแสดงเป็น "ธีมปัจจุบัน (Teal Blue)" เป็นต้น
            primary: '<?php echo isset($current_theme) ? $current_theme->primary_color : "#179CB1"; ?>',
            gradientStart: '<?php echo isset($current_theme) ? $current_theme->gradient_start : "#667eea"; ?>',
            gradientEnd: '<?php echo isset($current_theme) ? $current_theme->gradient_end : "#764ba2"; ?>',
            isDefault: true
        },
        {
            name: 'Blue Ocean (ค่าเริ่มต้น)',
            primary: '#179CB1',
            gradientStart: '#667eea',
            gradientEnd: '#764ba2'
        },

        // === ธีมสีน้ำเงิน (Blue Themes) ===
        {
            name: 'Corporate Blue',
            primary: '#1E3A8A',
            gradientStart: '#1E40AF',
            gradientEnd: '#3B82F6'
        }, {
            name: 'Royal Navy',
            primary: '#1E40AF',
            gradientStart: '#1D4ED8',
            gradientEnd: '#2563EB'
        }, {
            name: 'Steel Blue',
            primary: '#0F4C75',
            gradientStart: '#3282B8',
            gradientEnd: '#4FC3F7'
        }, {
            name: 'Azure Professional',
            primary: '#0078D4',
            gradientStart: '#106EBE',
            gradientEnd: '#4FC3F7'
        }, {
            name: 'Deep Blue',
            primary: '#003366',
            gradientStart: '#0066CC',
            gradientEnd: '#3399FF'
        }, {
            name: 'Sky Blue Executive',
            primary: '#0284C7',
            gradientStart: '#0EA5E9',
            gradientEnd: '#38BDF8'
        },

        // === ธีมสีเขียว (Green Themes) ===
        {
            name: 'Lime Green Professional',
            primary: '#65A30D',
            gradientStart: '#84CC16',
            gradientEnd: '#A3E635'
        }, {
            name: 'Forest Corporate',
            primary: '#059669',
            gradientStart: '#10B981',
            gradientEnd: '#34D399'
        }, {
            name: 'Emerald Professional',
            primary: '#065F46',
            gradientStart: '#047857',
            gradientEnd: '#10B981'
        }, {
            name: 'Sage Executive',
            primary: '#166534',
            gradientStart: '#15803D',
            gradientEnd: '#22C55E'
        }, {
            name: 'Pine Green',
            primary: '#0D4F3C',
            gradientStart: '#047857',
            gradientEnd: '#059669'
        },

        // === ธีมสีม่วง (Purple Themes) ===
        {
            name: 'Royal Purple',
            primary: '#7C3AED',
            gradientStart: '#8B5CF6',
            gradientEnd: '#A78BFA'
        }, {
            name: 'Deep Violet',
            primary: '#5B21B6',
            gradientStart: '#7C3AED',
            gradientEnd: '#8B5CF6'
        }, {
            name: 'Indigo Corporate',
            primary: '#3730A3',
            gradientStart: '#4338CA',
            gradientEnd: '#6366F1'
        }, {
            name: 'Amethyst',
            primary: '#6B46C1',
            gradientStart: '#7C3AED',
            gradientEnd: '#A855F7'
        },

        // === ธีมสีแดง (Red Themes) ===
        {
            name: 'Crimson Executive',
            primary: '#B91C1C',
            gradientStart: '#DC2626',
            gradientEnd: '#EF4444'
        }, {
            name: 'Ruby Professional',
            primary: '#991B1B',
            gradientStart: '#B91C1C',
            gradientEnd: '#DC2626'
        }, {
            name: 'Cardinal Red',
            primary: '#7F1D1D',
            gradientStart: '#991B1B',
            gradientEnd: '#B91C1C'
        },

        // === ธีมสีส้ม (Orange Themes) ===
        {
            name: 'Amber Corporate',
            primary: '#D97706',
            gradientStart: '#F59E0B',
            gradientEnd: '#FBBF24'
        }, {
            name: 'Sunset Professional',
            primary: '#EA580C',
            gradientStart: '#F97316',
            gradientEnd: '#FB923C'
        }, {
            name: 'Tangerine',
            primary: '#C2410C',
            gradientStart: '#EA580C',
            gradientEnd: '#F97316'
        },

        // === ธีมสีน้ำตาล (Brown Themes) ===
        {
            name: 'Coffee Executive',
            primary: '#78350F',
            gradientStart: '#92400E',
            gradientEnd: '#B45309'
        }, {
            name: 'Mahogany',
            primary: '#92400E',
            gradientStart: '#B45309',
            gradientEnd: '#D97706'
        },

        // === ธีมสีทอง (Gold Themes) ===
        {
            name: 'Golden Executive',
            primary: '#B45309',
            gradientStart: '#D97706',
            gradientEnd: '#F59E0B'
        }, {
            name: 'Bronze Professional',
            primary: '#92400E',
            gradientStart: '#B45309',
            gradientEnd: '#D97706'
        },

        // === ธีมสีเซียน (Cyan/Teal Themes) ===
        {
            name: 'Teal Corporate',
            primary: '#0F766E',
            gradientStart: '#14B8A6',
            gradientEnd: '#2DD4BF'
        }, {
            name: 'Cyan Professional',
            primary: '#0E7490',
            gradientStart: '#0891B2',
            gradientEnd: '#06B6D4'
        }, {
            name: 'Turquoise',
            primary: '#0D9488',
            gradientStart: '#14B8A6',
            gradientEnd: '#5EEAD4'
        },

        // === ธีมสีชมพู (Pink Themes) ===
        {
            name: 'Rose Executive',
            primary: '#BE185D',
            gradientStart: '#E11D48',
            gradientEnd: '#F43F5E'
        }, {
            name: 'Magenta Professional',
            primary: '#A21CAF',
            gradientStart: '#C026D3',
            gradientEnd: '#D946EF'
        }, {
            name: 'Fuchsia',
            primary: '#86198F',
            gradientStart: '#A21CAF',
            gradientEnd: '#C026D3'
        },

        // === ธีมพิเศษสำหรับองค์กร (Special Organization Themes) ===
        {
            name: 'Government Official',
            primary: '#1E3A8A',
            gradientStart: '#1E40AF',
            gradientEnd: '#DC2626'
        }, {
            name: 'Banking Professional',
            primary: '#065F46',
            gradientStart: '#047857',
            gradientEnd: '#D97706'
        }, {
            name: 'Medical Institute',
            primary: '#0E7490',
            gradientStart: '#06B6D4',
            gradientEnd: '#67E8F9'
        }, {
            name: 'Educational Premium',
            primary: '#7C3AED',
            gradientStart: '#8B5CF6',
            gradientEnd: '#3B82F6'
        }, {
            name: 'Legal Corporate',
            primary: '#1E40AF',
            gradientStart: '#3B82F6',
            gradientEnd: '#B45309'
        }, {
            name: 'Technology Modern',
            primary: '#059669',
            gradientStart: '#10B981',
            gradientEnd: '#06B6D4'
        }
    ];

    let currentTheme = predefinedThemes[0]; // ธีมเริ่มต้น

    // สร้างธีมที่กำหนดไว้
    function createPredefinedThemes() {
        const container = document.getElementById('predefined-themes');

        predefinedThemes.forEach((theme, index) => {
            const themeCard = document.createElement('div');
            themeCard.className = `col-md-6 col-lg-4 mb-3`;
            themeCard.innerHTML = `
            <div class="theme-card ${theme.isDefault ? 'active' : ''}" onclick="selectTheme(${index})" title="${theme.name}">
                <div class="theme-preview">
                    <div class="theme-gradient" style="background: linear-gradient(135deg, ${theme.gradientStart} 0%, ${theme.gradientEnd} 100%);"></div>
                    <div class="theme-name">${theme.name}</div>
                    <div class="check-icon">
                        <i class="fas fa-check text-success"></i>
                    </div>
                </div>
            </div>
        `;
            container.appendChild(themeCard);
        });
    }

    // เลือกธีม
    function selectTheme(index) {
        // ลบ active class จากทุกธีม
        document.querySelectorAll('.theme-card').forEach(card => {
            card.classList.remove('active');
        });

        // เพิ่ม active class ให้ธีมที่เลือก
        document.querySelectorAll('.theme-card')[index].classList.add('active');

        // เซ็ตธีมปัจจุบัน
        currentTheme = predefinedThemes[index];

        // อัปเดตตัวอย่าง
        updatePreview(currentTheme);

        // อัปเดต custom theme inputs
        document.getElementById('primary-color').value = currentTheme.primary;
        document.getElementById('gradient-start').value = currentTheme.gradientStart;
        document.getElementById('gradient-end').value = currentTheme.gradientEnd;
        updateCustomThemePreview();
    }

    // อัปเดตตัวอย่าง
    function updatePreview(theme) {
        const root = document.documentElement;
        root.style.setProperty('--primary-color', theme.primary);
        root.style.setProperty('--gradient-start', theme.gradientStart);
        root.style.setProperty('--gradient-end', theme.gradientEnd);
    }

    // อัปเดตธีมกำหนดเอง
    function updateCustomTheme() {
        updateCustomThemePreview();
    }

    function updateCustomThemePreview() {
        const primary = document.getElementById('primary-color').value;
        const gradientStart = document.getElementById('gradient-start').value;
        const gradientEnd = document.getElementById('gradient-end').value;

        document.getElementById('primary-preview').style.backgroundColor = primary;
        document.getElementById('gradient-start-preview').style.backgroundColor = gradientStart;
        document.getElementById('gradient-end-preview').style.backgroundColor = gradientEnd;
    }

    // ใช้ธีมกำหนดเอง
    function applyCustomTheme() {
        const customTheme = {
            name: 'Custom Theme',
            primary: document.getElementById('primary-color').value,
            gradientStart: document.getElementById('gradient-start').value,
            gradientEnd: document.getElementById('gradient-end').value
        };

        currentTheme = customTheme;
        updatePreview(customTheme);

        // ลบ active class จากธีมที่กำหนดไว้
        document.querySelectorAll('.theme-card').forEach(card => {
            card.classList.remove('active');
        });

        // แสดงข้อความแจ้งเตือน
        Swal.fire({
            icon: 'info',
            title: 'ธีมกำหนดเอง',
            text: 'กำลังใช้ธีมที่คุณกำหนดเอง กดบันทึกเพื่อให้มีผลทั้งระบบ',
            timer: 3000,
            showConfirmButton: false
        });
    }

    // สุ่มสีธีม
    function randomTheme() {
        const randomColors = [
            '#FF6B6B', '#4ECDC4', '#45B7D1', '#96CEB4', '#FFEAA7',
            '#DDA0DD', '#F4A460', '#87CEEB', '#FFB6C1', '#98FB98',
            '#F0E68C', '#FFA07A', '#20B2AA', '#9370DB', '#FF7F50'
        ];

        const randomPrimary = randomColors[Math.floor(Math.random() * randomColors.length)];
        const randomStart = randomColors[Math.floor(Math.random() * randomColors.length)];
        const randomEnd = randomColors[Math.floor(Math.random() * randomColors.length)];

        document.getElementById('primary-color').value = randomPrimary;
        document.getElementById('gradient-start').value = randomStart;
        document.getElementById('gradient-end').value = randomEnd;

        updateCustomThemePreview();
        applyCustomTheme();
    }

    // ฟังก์ชันใช้ธีมจากประวัติ
    function applyHistoryTheme(primary, gradientStart, gradientEnd, themeName) {
        // อัปเดต color picker
        document.getElementById('primary-color').value = primary;
        document.getElementById('gradient-start').value = gradientStart || primary;
        document.getElementById('gradient-end').value = gradientEnd || primary;

        // อัปเดตตัวอย่าง
        updateCustomThemePreview();

        // ตั้งค่าธีมปัจจุบัน
        currentTheme = {
            name: themeName,
            primary: primary,
            gradientStart: gradientStart || primary,
            gradientEnd: gradientEnd || primary
        };

        // ใช้ธีม
        updatePreview(currentTheme);

        // ลบ active class จากธีมที่กำหนดไว้
        document.querySelectorAll('.theme-card').forEach(card => {
            card.classList.remove('active');
        });

        // แสดงข้อความแจ้งเตือน
        Swal.fire({
            icon: 'success',
            title: 'ใช้ธีมจากประวัติ',
            text: `กำลังใช้ธีม "${themeName}" กดบันทึกเพื่อให้มีผลทั้งระบบ`,
            timer: 3000,
            showConfirmButton: false
        });
    }

    // ฟังก์ชันดูตัวอย่างธีมจากประวัติ
    function showThemePreview(primary, gradientStart, gradientEnd) {
        const previewTheme = {
            name: 'Preview',
            primary: primary,
            gradientStart: gradientStart || primary,
            gradientEnd: gradientEnd || primary
        };

        // อัปเดตตัวอย่างชั่วคราว
        updatePreview(previewTheme);


    }

    // ฟังก์ชันดูประวัติทั้งหมด (AJAX)
    function showAllHistory() {
        Swal.fire({
            title: 'ประวัติการเปลี่ยนธีมทั้งหมด',
            html: '<div class="text-center"><div class="spinner-border" role="status"></div><p>กำลังโหลด...</p></div>',
            width: '90%',
            showCloseButton: true,
            showConfirmButton: false,
            allowOutsideClick: false
        });

        $.ajax({
            url: '<?php echo site_url("Theme_backend/get_theme_history"); ?>',
            type: 'GET',
            data: {
                limit: 20
            }, // ดึง 20 รายการ
            dataType: 'json',
            success: function(response) {
                if (response.status === 'success' && response.data.length > 0) {
                    let historyHtml = '<div class="row">';

                    response.data.forEach(function(item, index) {
                        historyHtml += `
                            <div class="col-md-6 col-lg-4 mb-3">
                                <div class="card h-100">
                                    <div class="card-header text-white" style="background: linear-gradient(135deg, ${item.primary_color}, ${item.gradient_start || item.primary_color});">
                                        <h6 class="mb-0">${item.theme_name}</h6>
                                    </div>
                                    <div class="card-body">
                                        <div class="row text-center mb-2">
                                            <div class="col-4">
                                                <div class="color-dot mx-auto" style="background-color: ${item.primary_color}; width: 25px; height: 25px;"></div>
                                                <small>หลัก</small>
                                            </div>
                                            <div class="col-4">
                                                <div class="color-dot mx-auto" style="background-color: ${item.gradient_start || item.primary_color}; width: 25px; height: 25px;"></div>
                                                <small>เริ่ม</small>
                                            </div>
                                            <div class="col-4">
                                                <div class="color-dot mx-auto" style="background-color: ${item.gradient_end || item.primary_color}; width: 25px; height: 25px;"></div>
                                                <small>สิ้นสุด</small>
                                            </div>
                                        </div>
                                        <small class="text-muted">
                                            <i class="fas fa-user me-1"></i>User ID: ${item.updated_by}<br>
                                            <i class="fas fa-clock me-1"></i>${item.updated_at}
                                        </small>
                                    </div>
                                    <div class="card-footer">
                                        <button class="btn btn-sm btn-primary btn-block" onclick="applyHistoryTheme('${item.primary_color}', '${item.gradient_start}', '${item.gradient_end}', '${item.theme_name}'); Swal.close();">
                                            <i class="fas fa-check me-1"></i>ใช้ธีมนี้
                                        </button>
                                    </div>
                                </div>
                            </div>
                        `;
                    });

                    historyHtml += '</div>';

                    Swal.update({
                        html: historyHtml
                    });
                } else {
                    Swal.update({
                        html: '<div class="text-center text-muted"><i class="fas fa-info-circle fa-2x mb-2"></i><p>ไม่มีประวัติเพิ่มเติม</p></div>'
                    });
                }
            },
            error: function() {
                Swal.update({
                    html: '<div class="text-center text-danger"><i class="fas fa-exclamation-triangle fa-2x mb-2"></i><p>ไม่สามารถโหลดประวัติได้</p></div>'
                });
            }
        });
    }

    // รีเซ็ตธีม
    function resetTheme() {
        Swal.fire({
            title: 'ยืนยันการรีเซ็ต',
            text: 'คุณต้องการรีเซ็ตธีมกลับเป็นค่าเริ่มต้นหรือไม่?',
            icon: 'question',
            showCancelButton: true,
            confirmButtonColor: '#3085d6',
            cancelButtonColor: '#d33',
            confirmButtonText: '<i class="fas fa-undo me-1"></i>รีเซ็ต',
            cancelButtonText: '<i class="fas fa-times me-1"></i>ยกเลิก',
            reverseButtons: true
        }).then((result) => {
            if (result.isConfirmed) {
                // แสดง loading
                Swal.fire({
                    title: 'กำลังรีเซ็ตธีม...',
                    allowOutsideClick: false,
                    showConfirmButton: false,
                    willOpen: () => {
                        Swal.showLoading()
                    }
                });

                $.ajax({
                    url: '<?php echo site_url("Theme_backend/reset_theme"); ?>',
                    type: 'POST',
                    dataType: 'json',
                    success: function(response) {
                        if (response.status === 'success') {
                            Swal.fire({
                                icon: 'success',
                                title: 'สำเร็จ!',
                                text: response.message,
                                confirmButtonText: 'ตกลง'
                            }).then(() => {
                                window.location.reload();
                            });
                        } else {
                            Swal.fire({
                                icon: 'error',
                                title: 'เกิดข้อผิดพลาด!',
                                text: response.message,
                                confirmButtonText: 'ตกลง'
                            });
                        }
                    },
                    error: function() {
                        Swal.fire({
                            icon: 'error',
                            title: 'เกิดข้อผิดพลาด!',
                            text: 'ไม่สามารถเชื่อมต่อกับเซิร์ฟเวอร์ได้',
                            confirmButtonText: 'ตกลง'
                        });
                    }
                });
            }
        });
    }

    // บันทึกธีม
    function saveTheme() {
        // ตรวจสอบว่ามีการเปลี่ยนแปลงหรือไม่
        if (!currentTheme.name || currentTheme.name === '') {
            Swal.fire({
                icon: 'warning',
                title: 'แจ้งเตือน',
                text: 'กรุณาเลือกธีมหรือกำหนดธีมเองก่อนบันทึก',
                confirmButtonText: 'ตกลง'
            });
            return;
        }

        // แสดง Loading
        Swal.fire({
            title: 'กำลังบันทึกธีม...',
            html: `
            <div class="text-center mb-3">
                <div class="spinner-border text-primary" role="status">
                    <span class="sr-only">Loading...</span>
                </div>
            </div>
            <p class="mb-0">กำลังอัปเดตธีมทั้งระบบ</p>
        `,
            allowOutsideClick: false,
            showConfirmButton: false,
            customClass: {
                popup: 'swal2-show',
                backdrop: 'swal2-backdrop-show',
                icon: 'swal2-icon-show'
            }
        });

        // ส่งข้อมูลไปยัง backend
        $.ajax({
            url: '<?php echo site_url("Theme_backend/save_theme"); ?>',
            type: 'POST',
            data: {
                primary_color: currentTheme.primary,
                gradient_start: currentTheme.gradientStart,
                gradient_end: currentTheme.gradientEnd,
                theme_name: currentTheme.name
            },
            dataType: 'json',
            timeout: 10000, // 10 วินาที
            success: function(response) {
                if (response.status === 'success') {
                    Swal.fire({
                        icon: 'success',
                        title: 'สำเร็จ!',
                        html: `
                        <p>${response.message}</p>
                        <small class="text-muted">ธีมใหม่จะมีผลทันทีในระบบ</small>
                    `,
                        confirmButtonText: '<i class="fas fa-check me-1"></i>ตกลง',
                        allowOutsideClick: false
                    }).then((result) => {
                        if (result.isConfirmed) {
                            // รีเฟรชหน้าเพื่อให้เห็นการเปลี่ยนแปลง
                            window.location.reload();
                        }
                    });
                } else {
                    Swal.fire({
                        icon: 'error',
                        title: 'เกิดข้อผิดพลาด!',
                        text: response.message || 'ไม่สามารถบันทึกธีมได้',
                        confirmButtonText: 'ตกลง'
                    });
                }
            },
            error: function(xhr, status, error) {
                let errorMessage = 'ไม่สามารถเชื่อมต่อกับเซิร์ฟเวอร์ได้';

                if (status === 'timeout') {
                    errorMessage = 'การเชื่อมต่อหมดเวลา กรุณาลองใหม่อีกครั้ง';
                } else if (xhr.status === 404) {
                    errorMessage = 'ไม่พบหน้าที่ต้องการ กรุณาตรวจสอบการตั้งค่าระบบ';
                } else if (xhr.status === 500) {
                    errorMessage = 'เกิดข้อผิดพลาดภายในเซิร์ฟเวอร์';
                }

                Swal.fire({
                    icon: 'error',
                    title: 'เกิดข้อผิดพลาด!',
                    text: errorMessage,
                    confirmButtonText: 'ตกลง'
                });
            }
        });
    }

    // ฟังก์ชันคัดลอกรหัสสี
    function copyColorCode(color) {
        navigator.clipboard.writeText(color).then(function() {
            // แสดงการแจ้งเตือนแบบ toast
            const Toast = Swal.mixin({
                toast: true,
                position: 'top-end',
                showConfirmButton: false,
                timer: 2000,
                timerProgressBar: true
            });

            Toast.fire({
                icon: 'success',
                title: `คัดลอก ${color} แล้ว`
            });
        });
    }

    // ฟังก์ชันส่งออกธีม (Export)
    function exportTheme() {
        const themeData = {
            name: currentTheme.name,
            primary_color: currentTheme.primary,
            gradient_start: currentTheme.gradientStart,
            gradient_end: currentTheme.gradientEnd,
            exported_at: new Date().toISOString(),
            exported_by: 'User ID <?php echo $this->session->userdata("m_id"); ?>'
        };

        const dataStr = JSON.stringify(themeData, null, 2);
        const dataBlob = new Blob([dataStr], {
            type: 'application/json'
        });

        const link = document.createElement('a');
        link.href = URL.createObjectURL(dataBlob);
        link.download = `theme_${currentTheme.name.replace(/\s+/g, '_').toLowerCase()}_${new Date().getTime()}.json`;
        link.click();

        Swal.fire({
            icon: 'success',
            title: 'ส่งออกธีมสำเร็จ!',
            text: 'ไฟล์ธีมถูกดาวน์โหลดแล้ว',
            timer: 2000,
            showConfirmButton: false
        });
    }

    // ฟังก์ชันนำเข้าธีม (Import)
    function importTheme() {
        const input = document.createElement('input');
        input.type = 'file';
        input.accept = '.json';

        input.onchange = function(e) {
            const file = e.target.files[0];
            if (!file) return;

            const reader = new FileReader();
            reader.onload = function(e) {
                try {
                    const themeData = JSON.parse(e.target.result);

                    // ตรวจสอบความถูกต้องของไฟล์
                    if (!themeData.primary_color || !themeData.gradient_start || !themeData.gradient_end) {
                        throw new Error('ไฟล์ธีมไม่ถูกต้อง');
                    }

                    // นำเข้าธีม
                    const importedTheme = {
                        name: themeData.name || 'Imported Theme',
                        primary: themeData.primary_color,
                        gradientStart: themeData.gradient_start,
                        gradientEnd: themeData.gradient_end
                    };

                    currentTheme = importedTheme;
                    updatePreview(currentTheme);

                    // อัปเดต color picker
                    document.getElementById('primary-color').value = importedTheme.primary;
                    document.getElementById('gradient-start').value = importedTheme.gradientStart;
                    document.getElementById('gradient-end').value = importedTheme.gradientEnd;
                    updateCustomThemePreview();

                    // ลบ active class จากธีมที่กำหนดไว้
                    document.querySelectorAll('.theme-card').forEach(card => {
                        card.classList.remove('active');
                    });

                    Swal.fire({
                        icon: 'success',
                        title: 'นำเข้าธีมสำเร็จ!',
                        text: `ธีม "${importedTheme.name}" ถูกนำเข้าแล้ว กดบันทึกเพื่อใช้งาน`,
                        confirmButtonText: 'ตกลง'
                    });

                } catch (error) {
                    Swal.fire({
                        icon: 'error',
                        title: 'เกิดข้อผิดพลาด!',
                        text: 'ไฟล์ธีมไม่ถูกต้องหรือเสียหาย',
                        confirmButtonText: 'ตกลง'
                    });
                }
            };
            reader.readAsText(file);
        };

        input.click();
    }

    // ฟังก์ชันส่งออกประวัติ
    function exportHistory() {
        $.ajax({
            url: '<?php echo site_url("Theme_backend/get_theme_history"); ?>',
            type: 'GET',
            data: {
                limit: 0
            }, // ดึงทั้งหมด
            dataType: 'json',
            success: function(response) {
                if (response.status === 'success' && response.data.length > 0) {
                    const historyData = {
                        export_date: new Date().toISOString(),
                        total_records: response.data.length,
                        exported_by: 'User ID <?php echo $this->session->userdata("m_id"); ?>',
                        theme_history: response.data
                    };

                    const dataStr = JSON.stringify(historyData, null, 2);
                    const dataBlob = new Blob([dataStr], {
                        type: 'application/json'
                    });

                    const link = document.createElement('a');
                    link.href = URL.createObjectURL(dataBlob);
                    link.download = `theme_history_${new Date().getTime()}.json`;
                    link.click();

                    Swal.fire({
                        icon: 'success',
                        title: 'ส่งออกประวัติสำเร็จ!',
                        text: 'ไฟล์ประวัติถูกดาวน์โหลดแล้ว',
                        timer: 2000,
                        showConfirmButton: false
                    });
                } else {
                    Swal.fire({
                        icon: 'info',
                        title: 'ไม่มีประวัติให้ส่งออก',
                        text: 'ไม่พบข้อมูลประวัติการเปลี่ยนธีม',
                        confirmButtonText: 'ตกลง'
                    });
                }
            },
            error: function() {
                Swal.fire({
                    icon: 'error',
                    title: 'เกิดข้อผิดพลาด!',
                    text: 'ไม่สามารถดึงข้อมูลประวัติได้',
                    confirmButtonText: 'ตกลง'
                });
            }
        });
    }

    // ตัวแปรเก็บข้อมูลโปรไฟล์
    let colorProfiles = {
        1: null,
        2: null,
        3: null
    };

    // บันทึกโปรไฟล์
    function saveProfile(profileNumber) {
        const primary = document.getElementById('primary-color').value;
        const gradientStart = document.getElementById('gradient-start').value;
        const gradientEnd = document.getElementById('gradient-end').value;

        colorProfiles[profileNumber] = {
            primary: primary,
            gradientStart: gradientStart,
            gradientEnd: gradientEnd,
            name: `Profile ${profileNumber}`
        };

        // อัปเดตการแสดงผล
        updateProfileDisplay(profileNumber);

        // บันทึกลง localStorage
        localStorage.setItem('themeProfiles', JSON.stringify(colorProfiles));

        // แสดงข้อความสำเร็จ
        const Toast = Swal.mixin({
            toast: true,
            position: 'top-end',
            showConfirmButton: false,
            timer: 2000,
            timerProgressBar: true
        });

        Toast.fire({
            icon: 'success',
            title: `บันทึก Profile ${profileNumber} แล้ว`
        });
    }

    // โหลดโปรไฟล์
    function loadProfile(profileNumber) {
        const profile = colorProfiles[profileNumber];

        if (!profile) {
            Swal.fire({
                icon: 'warning',
                title: 'ไม่มีข้อมูล',
                text: `Profile ${profileNumber} ยังไม่มีข้อมูลสี`,
                timer: 2000,
                showConfirmButton: false
            });
            return;
        }

        // ตั้งค่าสีใน color picker
        document.getElementById('primary-color').value = profile.primary;
        document.getElementById('gradient-start').value = profile.gradientStart;
        document.getElementById('gradient-end').value = profile.gradientEnd;

        // อัปเดตตัวอย่าง
        updateCustomThemePreview();
        applyCustomTheme();

        // แสดงข้อความสำเร็จ
        const Toast = Swal.mixin({
            toast: true,
            position: 'top-end',
            showConfirmButton: false,
            timer: 2000,
            timerProgressBar: true
        });

        Toast.fire({
            icon: 'success',
            title: `โหลด Profile ${profileNumber} แล้ว`
        });
    }

    // อัปเดตการแสดงผลโปรไฟล์
    function updateProfileDisplay(profileNumber) {
        const profile = colorProfiles[profileNumber];
        const slot = document.getElementById(`profile-${profileNumber}`);

        if (profile) {
            // อัปเดตสี
            document.getElementById(`profile-${profileNumber}-primary`).style.backgroundColor = profile.primary;
            document.getElementById(`profile-${profileNumber}-start`).style.backgroundColor = profile.gradientStart;
            document.getElementById(`profile-${profileNumber}-end`).style.backgroundColor = profile.gradientEnd;

            // เพิ่ม class has-data
            slot.classList.add('has-data');
        }
    }

    // โหลดข้อมูลจาก localStorage เมื่อเริ่มต้น
    function loadProfilesFromStorage() {
        const saved = localStorage.getItem('themeProfiles');
        if (saved) {
            colorProfiles = JSON.parse(saved);

            // อัปเดตการแสดงผลทั้งหมด
            for (let i = 1; i <= 3; i++) {
                if (colorProfiles[i]) {
                    updateProfileDisplay(i);
                }
            }
        }
    }

    // เริ่มต้นระบบ
    document.addEventListener('DOMContentLoaded', function() {
        // สร้างธีมที่กำหนดไว้
        createPredefinedThemes();

        // อัปเดตตัวอย่างเริ่มต้น
        updatePreview(currentTheme);
        updateCustomThemePreview();

        // เพิ่ม tooltip สำหรับ color picker
        document.querySelector('.color-preview').addEventListener('click', function() {
            document.getElementById('primary-color').click();
        });

        // เพิ่ม keyboard shortcuts
        document.addEventListener('keydown', function(e) {
            // Ctrl+S = บันทึก
            if (e.ctrlKey && e.key === 's') {
                e.preventDefault();
                saveTheme();
            }

            // Ctrl+R = รีเซ็ต (ปิดการทำงานของ refresh ปกติ)
            if (e.ctrlKey && e.key === 'r') {
                e.preventDefault();
                resetTheme();
            }

            // Ctrl+E = ส่งออก
            if (e.ctrlKey && e.key === 'e') {
                e.preventDefault();
                exportTheme();
            }

            // Ctrl+I = นำเข้า
            if (e.ctrlKey && e.key === 'i') {
                e.preventDefault();
                importTheme();
            }
        });

        // แสดงคำแนะนำ keyboard shortcuts
        console.log('🎨 Theme Manager Shortcuts:');
        console.log('Ctrl+S = บันทึกธีม');
        console.log('Ctrl+R = รีเซ็ตธีม');
        console.log('Ctrl+E = ส่งออกธีม');
        console.log('Ctrl+I = นำเข้าธีม');

        loadProfilesFromStorage();
    });

    // ป้องกันการออกจากหน้าโดยไม่บันทึก
    window.addEventListener('beforeunload', function(e) {
        // ตรวจสอบว่ามีการเปลี่ยนแปลงที่ยังไม่ได้บันทึกหรือไม่
        const hasUnsavedChanges = document.querySelector('.theme-card.active') === null && currentTheme.name !== 'Blue Ocean (ค่าเริ่มต้น)';

        if (hasUnsavedChanges) {
            e.preventDefault();
            e.returnValue = 'คุณมีการเปลี่ยนแปลงที่ยังไม่ได้บันทึก ต้องการออกจากหน้านี้หรือไม่?';
        }
    });



    // Toggle Floating Preview
    function toggleFloating() {
        const floatingPreview = document.getElementById('floating-preview');
        const icon = document.getElementById('float-icon');

        floatingPreview.classList.toggle('show');

        if (floatingPreview.classList.contains('show')) {
            icon.className = 'fas fa-chevron-down';
        } else {
            icon.className = 'fas fa-chevron-up';
        }
    }

    // Auto show on page load
    document.addEventListener('DOMContentLoaded', function() {
        // เนื้อหาเดิม...

        // แสดง floating preview หลังจาก 2 วินาที
        setTimeout(() => {
            document.getElementById('floating-preview').classList.add('show');
        }, 2000);
    });

    // อัปเดตสีจากโค้ดที่พิมพ์
    function updateColorFromCode(colorType) {
        let codeInput, colorInput, preview;

        // กำหนด element IDs ตามประเภทสี
        if (colorType === 'primary') {
            codeInput = document.getElementById('primary-color-code');
            colorInput = document.getElementById('primary-color');
            preview = document.getElementById('primary-preview');
        } else if (colorType === 'gradient-start') {
            codeInput = document.getElementById('gradient-start-code');
            colorInput = document.getElementById('gradient-start');
            preview = document.getElementById('gradient-start-preview');
        } else if (colorType === 'gradient-end') {
            codeInput = document.getElementById('gradient-end-code');
            colorInput = document.getElementById('gradient-end');
            preview = document.getElementById('gradient-end-preview');
        }

        const colorValue = codeInput.value.trim();

        // ตรวจสอบรูปแบบสี
        if (isValidHexColor(colorValue)) {
            // อัปเดตสี
            colorInput.value = colorValue;
            preview.style.backgroundColor = colorValue;

            // แสดงสถานะ valid
            codeInput.classList.remove('invalid');
            codeInput.classList.add('valid');

            // อัปเดตตัวอย่างธีม
            updateCustomTheme();
        } else if (colorValue === '') {
            // ถ้าช่องว่าง
            codeInput.classList.remove('valid', 'invalid');
        } else {
            // ถ้าผิดรูปแบบ
            codeInput.classList.remove('valid');
            codeInput.classList.add('invalid');
        }
    }

    // ตรวจสอบรูปแบบสี HEX
    function isValidHexColor(color) {
        return /^#[0-9A-Fa-f]{6}$/.test(color);
    }

    // ปรับปรุงฟังก์ชัน updateCustomTheme() เดิม
    function updateCustomTheme() {
        updateCustomThemePreview();

        // อัปเดต code inputs ให้ sync กับ color picker
        const primaryValue = document.getElementById('primary-color').value;
        const startValue = document.getElementById('gradient-start').value;
        const endValue = document.getElementById('gradient-end').value;

        document.getElementById('primary-color-code').value = primaryValue;
        document.getElementById('gradient-start-code').value = startValue;
        document.getElementById('gradient-end-code').value = endValue;

        // เคลียร์ validation classes
        document.getElementById('primary-color-code').classList.remove('invalid');
        document.getElementById('gradient-start-code').classList.remove('invalid');
        document.getElementById('gradient-end-code').classList.remove('invalid');
    }
</script>