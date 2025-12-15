<div class="container-fluid">
    <div class="d-flex justify-content-between align-items-center mb-2">
        <h3 class="m-0">จัดการการแจ้งเตือนความปลอดภัยผ่าน Line OA</h3>
    </div>
    <!-- ส่วนการตั้งค่า -->
    <div class="line-container">
        <div class="line-header">
            <i class="bi bi-gear-fill"></i> ตั้งค่าการแจ้งเตือน
        </div>
        <div class="line-body">
            <div class="setting-row">
                <div class="setting-label">
                    <div class="setting-title">การแจ้งเตือนความปลอดภัยผ่าน Line OA</div>
                    <div class="setting-description">ควบคุมการส่งการแจ้งเตือนไปยังผู้ใช้ผ่าน Line OA</div>
                </div>
                <div class="setting-control d-flex align-items-center">
                    <label class="line-toggle mb-0">
                        <input type="checkbox" id="lineNotificationToggle" <?= isset($line_notification_status) && $line_notification_status == '1' ? 'checked' : ''; ?>>
                        <span class="line-slider"></span>
                    </label>
                    <span id="notificationStatus" class="toggle-status <?= isset($line_notification_status) && $line_notification_status == '1' ? 'status-active' : 'status-inactive'; ?>">
                        <?= isset($line_notification_status) && $line_notification_status == '1' ? 'เปิดใช้งาน' : 'ปิดใช้งาน'; ?>
                    </span>
                </div>
            </div>

            <div class="setting-row">
                <div class="setting-label">
                    <div class="setting-title">ทดสอบการส่งแจ้งเตือน</div>
                    <div class="setting-description">ส่งข้อความทดสอบไปยังผู้ใช้ที่ลงทะเบียนไว้</div>
                </div>
                <button id="testNotificationBtn" class="test-notification-btn">
                    <span class="btn-text"><i class="bi bi-send"></i> ทดสอบการส่ง</span>
                    <div class="loader">
                        <div class="spinner"></div>
                    </div>
                </button>
            </div>

        </div>
    </div>
</div>


<!-- หน้าแสดงรายการอีเมลและจัดการการลงทะเบียน (ปรับปรุงใหม่ แก้ไขเพิ่มเติม) -->
<div class="container-fluid">
    <div class="main-header mb-4">
        <div>
            <h3 class="main-title">จัดการอีเมลรับแจ้งเตือน</h3>
            <p class="text-muted mb-0">ลงทะเบียนและจัดการรายชื่ออีเมลที่ต้องการรับการแจ้งเตือนความปลอดภัย</p>
        </div>
        <!-- ส่วนของปุ่มในหน้า email_register.php -->
        <div class="main-actions mt-2">
            <a href="<?php echo base_url('Email_register/add'); ?>" class="btn btn-success me-2">
                <i class="fas fa-plus-circle me-1"></i>เพิ่มอีเมล
            </a>
            <a href="<?php echo base_url('Email_register'); ?>" class="btn btn-outline-primary me-2">
                <i class="fas fa-sync-alt me-1"></i>รีเฟรช
            </a>

            <button class="btn btn-outline-info me-2" id="btnTestEmail">
                <i class="fas fa-paper-plane me-1"></i>ทดสอบส่งอีเมล
            </button>
            <!-- <a href="<?php echo base_url('User_log_backend'); ?>" class="btn btn-secondary">
        <i class="fas fa-arrow-left me-1"></i>กลับ
    </a> -->
        </div>
    </div>

    <!-- แสดงข้อความแจ้งเตือน -->
    <?php if ($this->session->flashdata('save_success')) : ?>
        <div class="alert alert-success alert-dismissible fade show mb-4" role="alert">
            <i class="fas fa-check-circle me-2"></i>บันทึกข้อมูลเรียบร้อย
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    <?php endif; ?>

    <?php if ($this->session->flashdata('save_again')) : ?>
        <div class="alert alert-warning alert-dismissible fade show mb-4" role="alert">
            <i class="fas fa-exclamation-circle me-2"></i>อีเมลนี้มีในระบบแล้ว
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    <?php endif; ?>

    <?php if ($this->session->flashdata('del_success')) : ?>
        <div class="alert alert-danger alert-dismissible fade show mb-4" role="alert">
            <i class="fas fa-trash-alt me-2"></i>ลบข้อมูลเรียบร้อย
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    <?php endif; ?>

    <?php if ($this->session->flashdata('success')) : ?>
        <div class="alert alert-success alert-dismissible fade show mb-4" role="alert">
            <i class="fas fa-check-circle me-2"></i><?php echo $this->session->flashdata('success'); ?>
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    <?php endif; ?>

    <?php if ($this->session->flashdata('error')) : ?>
        <div class="alert alert-danger alert-dismissible fade show mb-4" role="alert">
            <i class="fas fa-exclamation-triangle me-2"></i><?php echo $this->session->flashdata('error'); ?>
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    <?php endif; ?>

    <!-- ตารางแสดงรายการอีเมล -->
    <div class="card shadow-sm">
        <div class="card-header bg-white py-3 d-flex justify-content-between align-items-center">
            <h5 class="card-title mb-0">
                <i class="fas fa-envelope me-2 text-primary"></i>รายการอีเมลรับแจ้งเตือน
            </h5>
            <span class="badge bg-primary rounded-pill px-3 py-2">
                <?php echo count($emails); ?> รายการ
            </span>
        </div>
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover mb-0">
                    <thead class="table-light">
                        <tr>
                            <th class="text-center text-dark" width="70px">ลำดับ</th>
                            <th class="text-dark">อีเมล</th>
                            <th class="text-dark" width="150px">ผู้บันทึก</th>
                            <th class="text-center text-dark" width="180px">สถานะ</th>
                            <th class="text-center text-dark" width="180px">จัดการ</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (!empty($emails)) : ?>
                            <?php foreach ($emails as $index => $email) : ?>
                                <tr>
                                    <td class="text-center"><?php echo $index + 1; ?></td>
                                    <td class="fw-medium"><?php echo $email->email_name; ?></td>
                                    <td><?php echo $email->email_by; ?></td>
                                    <td class="text-center">
                                        <div class="status-container">
                                            <div class="form-check form-switch">
                                                <input class="form-check-input toggle-status" type="checkbox"
                                                    data-email-id="<?php echo $email->email_id; ?>"
                                                    <?php echo ($email->email_status == '1') ? 'checked' : ''; ?>>
                                            </div>
                                            <span class="status-label ms-2">
                                                <?php echo ($email->email_status == '1') ? 'เปิดใช้งาน' : 'ปิดใช้งาน'; ?>
                                            </span>
                                        </div>
                                    </td>
                                    <td class="text-center">
                                        <div class="btn-group">
                                            <a href="<?php echo base_url('Email_register/edit/' . $email->email_id); ?>" class="btn btn-warning btn-sm">
                                                <i class="fas fa-edit"></i> แก้ไข
                                            </a>
                                            <a href="#" class="btn btn-danger btn-sm btn-delete" data-id="<?php echo $email->email_id; ?>">
                                                <i class="fas fa-trash-alt"></i> ลบ
                                            </a>
                                        </div>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        <?php else : ?>
                            <tr>
                                <td colspan="5" class="text-center py-5">
                                    <div class="empty-state">
                                        <i class="fas fa-envelope-open fa-3x text-muted mb-3"></i>
                                        <h5 class="text-muted">ไม่พบข้อมูลอีเมล</h5>
                                        <p class="text-muted mb-3">คุณยังไม่ได้ลงทะเบียนอีเมลรับแจ้งเตือน</p>
                                        <a href="<?php echo base_url('Email_register/add'); ?>" class="btn btn-primary">
                                            <i class="fas fa-plus-circle me-1"></i>เพิ่มอีเมล
                                        </a>
                                    </div>
                                </td>
                            </tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
        <?php if (!empty($emails)) : ?>
            <div class="card-footer bg-white py-3">
                <small class="text-muted">
                    <i class="fas fa-info-circle me-1"></i>
                    การแจ้งเตือนจะถูกส่งไปยังอีเมลที่มีสถานะ "เปิดใช้งาน" เท่านั้น
                </small>
            </div>
        <?php endif; ?>
    </div>
</div>

<!-- Modal สำหรับยืนยันการลบข้อมูล -->
<div class="modal fade" id="deleteModal" tabindex="-1" aria-labelledby="deleteModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="deleteModalLabel">ยืนยันการลบข้อมูล</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <div class="text-center mb-3">
                    <i class="fas fa-trash-alt fa-3x text-danger"></i>
                </div>
                <p class="text-center">คุณต้องการลบข้อมูลอีเมลนี้ใช่หรือไม่?</p>
                <p class="text-center text-muted small">การลบข้อมูลนี้ไม่สามารถย้อนกลับได้</p>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">ยกเลิก</button>
                <a href="#" id="btnConfirmDelete" class="btn btn-danger">ยืนยันการลบ</a>
            </div>
        </div>
    </div>
</div>

<!-- Modal สำหรับยืนยันการส่งอีเมลทดสอบ -->
<div class="modal fade" id="testEmailModal" tabindex="-1" aria-labelledby="testEmailModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="testEmailModalLabel">ทดสอบส่งอีเมล</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <div class="text-center mb-3">
                    <i class="fas fa-paper-plane fa-3x text-info"></i>
                </div>
                <p class="text-center">คุณต้องการทดสอบส่งอีเมลไปยังทุกอีเมลที่เปิดใช้งานใช่หรือไม่?</p>
                <p class="text-center text-muted small">อีเมลทดสอบจะถูกส่งไปยังทุกอีเมลที่มีสถานะ "เปิดใช้งาน" เท่านั้น</p>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">ยกเลิก</button>
                <a href="<?php echo base_url('Email_register/test_email'); ?>" id="btnTestEmailConfirm" class="btn btn-info">ทดสอบส่งอีเมล</a>
            </div>
        </div>
    </div>
</div>

<!-- Container สำหรับแสดงการแจ้งเตือน -->
<div class="alert-container" id="lineAlertContainer"></div>

<style>
    /* ปรับแต่งสไตล์ */
    .toggle-status {
        width: 3.5rem;
        height: 1.5rem;
        cursor: pointer;
    }

    .status-container {
        display: flex;
        align-items: center;
        justify-content: center;
    }

    .status-label {
        font-size: 0.85rem;
        min-width: 70px;
        display: inline-block;
    }

    .form-check.form-switch {
        display: flex;
        justify-content: flex-end;
        margin-right: 5px;
    }

    .empty-state {
        padding: 2rem 0;
        text-align: center;
    }

    /* แก้ไขปัญหา modal-backdrop */
    .modal-backdrop {
        z-index: 1040;
    }

    .modal {
        z-index: 1050;
    }

    .table thead th {
        color: #000 !important;
        font-weight: 600;
    }

    /* สไตล์สำหรับหน้าจัดการการแจ้งเตือน Line OA */
    .line-container {
        background-color: #fff;
        border-radius: 8px;
        box-shadow: 0 1px 3px rgba(0, 0, 0, 0.12), 0 1px 2px rgba(0, 0, 0, 0.24);
        margin-bottom: 20px;
        overflow: hidden;
    }

    .line-header {
        background-color: #06C755;
        color: white;
        padding: 15px 20px;
        font-size: 18px;
        font-weight: 500;
        display: flex;
        align-items: center;
    }

    .line-header i {
        margin-right: 10px;
    }

    .line-body {
        padding: 20px;
    }

    .setting-row {
        display: flex;
        justify-content: space-between;
        align-items: center;
        padding: 15px 0;
        border-bottom: 1px solid #f0f0f0;
    }

    .setting-row:last-child {
        border-bottom: none;
    }

    .setting-label {
        display: flex;
        flex-direction: column;
    }

    .setting-title {
        font-size: 16px;
        font-weight: 500;
        color: #333;
        margin-bottom: 5px;
    }

    .setting-description {
        font-size: 14px;
        color: #777;
    }

    .line-toggle {
        position: relative;
        display: inline-block;
        width: 60px;
        height: 30px;
    }

    .line-toggle input {
        opacity: 0;
        width: 0;
        height: 0;
    }

    .line-slider {
        position: absolute;
        cursor: pointer;
        top: 0;
        left: 0;
        right: 0;
        bottom: 0;
        background-color: #ccc;
        transition: .4s;
        border-radius: 30px;
    }

    .line-slider:before {
        position: absolute;
        content: "";
        height: 22px;
        width: 22px;
        left: 4px;
        bottom: 4px;
        background-color: white;
        transition: .4s;
        border-radius: 50%;
    }

    input:checked+.line-slider {
        background-color: #06C755;
    }

    input:focus+.line-slider {
        box-shadow: 0 0 1px #06C755;
    }

    input:checked+.line-slider:before {
        transform: translateX(30px);
    }

    .toggle-status {
        margin-left: 10px;
        font-size: 14px;
        font-weight: 500;
    }

    .status-active {
        color: #06C755;
    }

    .status-inactive {
        color: #777;
    }

    .test-btn {
        background-color: #f8f9fa;
        color: #06C755;
        border: 1px solid #06C755;
        padding: 8px 16px;
        border-radius: 4px;
        font-size: 14px;
        font-weight: 500;
        cursor: pointer;
        transition: all 0.3s;
    }

    .test-btn:hover {
        background-color: #06C755;
        color: white;
    }

    /* ส่วนรายการผู้ใช้ Line */
    .line-users-container {
        background-color: #fff;
        border-radius: 8px;
        box-shadow: 0 1px 3px rgba(0, 0, 0, 0.12), 0 1px 2px rgba(0, 0, 0, 0.24);
        margin-bottom: 20px;
        overflow: hidden;
    }

    .line-users-header {
        font-size: 18px;
        font-weight: 500;
        padding: 15px 20px;
        border-bottom: 1px solid #eee;
    }

    .user-status-toggle {
        display: flex;
        align-items: center;
    }

    .status-badge {
        display: inline-block;
        padding: 4px 8px;
        border-radius: 20px;
        font-size: 12px;
        font-weight: 500;
        margin-left: 10px;
    }

    .badge-active {
        background-color: rgba(6, 199, 85, 0.1);
        color: #06C755;
        border: 1px solid #06C755;
    }

    .badge-inactive {
        background-color: rgba(119, 119, 119, 0.1);
        color: #777;
        border: 1px solid #777;
    }

    .action-buttons {
        display: flex;
        gap: 5px;
        justify-content: center;
        /* จัดให้อยู่ตรงกลางของพื้นที่ */
    }

    .edit-btn {
        background-color: #ffc107;
        color: white;
    }

    .delete-btn {
        background-color: #dc3545;
        color: white;
    }

    /* ปรับแต่งปุ่มให้มีรูปแบบที่ดีขึ้น */
    .action-btn {
        width: 32px;
        height: 32px;
        border: none;
        border-radius: 4px;
        display: inline-flex;
        align-items: center;
        /* จัดให้ไอคอนอยู่ตรงกลางในแนวตั้ง */
        justify-content: center;
        /* จัดให้ไอคอนอยู่ตรงกลางในแนวนอน */
        cursor: pointer;
        transition: all 0.2s;
    }

    /* ปรับแต่งไอคอนให้มีขนาดที่เหมาะสม */
    .action-btn i {
        font-size: 14px;
        /* ขนาดไอคอน */
        line-height: 1;
    }

    /* เพิ่มคลาสสำหรับเซลล์ในตาราง */
    td .action-buttons {
        display: flex;
        justify-content: center;
        /* จัดให้ปุ่มอยู่ตรงกลางในเซลล์ */
    }

    .action-btn:hover {
        opacity: 0.8;
    }

    /* การแจ้งเตือน */
    .alert-container {
        position: fixed;
        top: 20px;
        right: 20px;
        max-width: 350px;
        z-index: 1000;
    }

    .line-alert {
        background-color: white;
        border-radius: 8px;
        box-shadow: 0 4px 12px rgba(0, 0, 0, 0.15);
        margin-bottom: 10px;
        overflow: hidden;
        transition: all 0.3s;
        opacity: 0;
        transform: translateX(50px);
    }

    .line-alert.show {
        opacity: 1;
        transform: translateX(0);
    }

    .alert-success {
        border-left: 4px solid #06C755;
    }

    .alert-error {
        border-left: 4px solid #dc3545;
    }

    .alert-header {
        padding: 10px 15px;
        display: flex;
        align-items: center;
        justify-content: space-between;
        border-bottom: 1px solid #f0f0f0;
    }

    .alert-title {
        font-weight: 500;
        display: flex;
        align-items: center;
    }

    .alert-title i {
        margin-right: 8px;
    }

    .alert-close {
        cursor: pointer;
        background: none;
        border: none;
        font-size: 18px;
        color: #777;
    }

    .alert-body {
        padding: 15px;
    }

    /* Modal Dialog */
    .line-modal {
        display: none;
        position: fixed;
        top: 0;
        left: 0;
        width: 100%;
        height: 100%;
        background-color: rgba(0, 0, 0, 0.5);
        z-index: 1000;
        justify-content: center;
        align-items: center;
    }

    .modal-content {
        background-color: white;
        border-radius: 8px;
        width: 90%;
        max-width: 500px;
        max-height: 90vh;
        overflow-y: auto;
        position: relative;
    }

    .modal-header {
        padding: 15px 20px;
        border-bottom: 1px solid #f0f0f0;
        display: flex;
        justify-content: space-between;
        align-items: center;
    }

    .modal-title {
        font-size: 18px;
        font-weight: 500;
        color: #333;
    }

    .modal-close {
        cursor: pointer;
        background: none;
        border: none;
        font-size: 20px;
        color: #777;
    }

    .modal-body {
        padding: 20px;
    }

    /* สไตล์สำหรับปุ่มทดสอบแจ้งเตือน */
    .test-notification-btn {
        display: inline-block;
        position: relative;
        background-color: #f8f9fa;
        color: #06C755;
        border: 1px solid #06C755;
        padding: 8px 16px;
        border-radius: 4px;
        font-size: 14px;
        font-weight: 500;
        cursor: pointer;
        transition: all 0.3s;
        overflow: hidden;
    }

    .test-notification-btn:hover {
        background-color: #06C755;
        color: white;
    }

    a.test-notification-btn:hover,
    a.test-email-btn:hover {
        text-decoration: none;
    }

    a:hover {
        text-decoration: none;
    }

    .test-notification-btn .loader {
        display: none;
        position: absolute;
        top: 0;
        left: 0;
        width: 100%;
        height: 100%;
        background-color: rgba(255, 255, 255, 0.8);
        align-items: center;
        justify-content: center;
    }

    .test-notification-btn.loading .loader {
        display: flex;
    }

    .test-notification-btn.loading .btn-text {
        visibility: hidden;
    }

    .spinner {
        width: 20px;
        height: 20px;
        border: 2px solid #06C755;
        border-radius: 50%;
        border-top-color: transparent;
        animation: spin 0.8s linear infinite;
    }

    @keyframes spin {
        to {
            transform: rotate(360deg);
        }
    }

    /* Responsive */
    @media (max-width: 768px) {
        .setting-row {
            flex-direction: column;
            align-items: flex-start;
        }

        .setting-control {
            margin-top: 10px;
            align-self: flex-end;
        }
    }

    .table thead th {
        color: #000 !important;
        font-weight: 600;
    }

    .btn-text {
        display: flex;
        align-items: center;
    }

    .btn-text i {
        margin-right: 5px;
        font-size: 14px;
        /* ปรับขนาดไอคอนให้พอดีกับข้อความ */
        line-height: 1;
    }

    a.test-notification-btn:hover,
    a.test-email-btn:hover {
        text-decoration: none;
    }

    .btn-line {
        background-color: #06C755;
        color: white;
        border: none;
        margin-right: 5px;
    }

    .btn-line:hover {
        background-color: #00B900;
        color: white;
    }

    .btn i {
        margin-right: 5px;
        font-size: 14px;
        line-height: 1;
        vertical-align: middle;
    }

    /* ปรับปุ่มให้อยู่ในแนวเดียวกัน */
    .action-buttons {
        display: flex;
        gap: 5px;
    }

    /* เพื่อให้ไอคอนกับข้อความอยู่ในระดับเดียวกัน */
    .btn {
        display: inline-flex;
        align-items: center;
    }
</style>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        // สคริปต์สำหรับการเปลี่ยนสถานะอีเมล
        const toggleStatuses = document.querySelectorAll('.toggle-status');

        toggleStatuses.forEach(toggle => {
            toggle.addEventListener('change', function() {
                const emailId = this.dataset.emailId;
                const newStatus = this.checked ? '1' : '0';
                const statusLabel = this.closest('.status-container').querySelector('.status-label');

                // อัพเดตข้อความแสดงสถานะ
                statusLabel.textContent = this.checked ? 'เปิดใช้งาน' : 'ปิดใช้งาน';

                // ส่งคำขอไปยังเซิร์ฟเวอร์เพื่ออัพเดตสถานะ
                fetch('<?php echo base_url('Email_register/update_status'); ?>', {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/x-www-form-urlencoded',
                        },
                        body: `email_id=${emailId}&new_status=${newStatus}`
                    })
                    .then(response => {
                        if (!response.ok) {
                            throw new Error('Network response was not ok');
                        }
                        return response.json();
                    })
                    .then(data => {
                        console.log('Status updated:', data);
                    })
                    .catch(error => {
                        console.error('Error updating status:', error);
                        // ย้อนกลับสถานะเดิมถ้ามีข้อผิดพลาด
                        this.checked = !this.checked;
                        statusLabel.textContent = this.checked ? 'เปิดใช้งาน' : 'ปิดใช้งาน';
                    });
            });
        });

        // สคริปต์สำหรับการลบข้อมูล
        const deleteButtons = document.querySelectorAll('.btn-delete');
        const confirmDeleteButton = document.getElementById('btnConfirmDelete');
        const deleteModal = new bootstrap.Modal(document.getElementById('deleteModal'));

        deleteButtons.forEach(button => {
            button.addEventListener('click', function(e) {
                e.preventDefault();
                const emailId = this.dataset.id;
                confirmDeleteButton.href = `<?php echo base_url('Email_register/delete/'); ?>${emailId}`;
                deleteModal.show();
            });
        });

        // สคริปต์สำหรับการทดสอบส่งอีเมล
        const testEmailButton = document.getElementById('btnTestEmail');
        const testEmailModal = new bootstrap.Modal(document.getElementById('testEmailModal'));

        testEmailButton.addEventListener('click', function() {
            testEmailModal.show();
        });

        // แก้ไขปัญหา modal-backdrop
        document.getElementById('btnTestEmailConfirm').addEventListener('click', function(e) {
            e.preventDefault();
            const href = this.getAttribute('href');
            testEmailModal.hide();

            // รอให้ modal ถูกซ่อนเสร็จแล้วจึงเปลี่ยนหน้า
            setTimeout(function() {
                window.location.href = href;
            }, 500);
        });

        // สคริปต์สำหรับการเปิด/ปิดทั้งหมด
        const toggleAllButton = document.getElementById('btnToggleAll');
        toggleAllButton.addEventListener('click', function() {
            // ตรวจสอบว่ามี toggle ที่เปิดอยู่หรือไม่
            const anyToggleOn = Array.from(toggleStatuses).some(toggle => toggle.checked);

            // ถ้ามีอย่างน้อย 1 รายการที่เปิดอยู่ ให้ปิดทั้งหมด มิฉะนั้นให้เปิดทั้งหมด
            const newStatus = anyToggleOn ? '0' : '1';

            // อัพเดตสถานะทั้งหมด
            toggleStatuses.forEach(toggle => {
                toggle.checked = newStatus === '1';
                const statusLabel = toggle.closest('.status-container').querySelector('.status-label');
                statusLabel.textContent = newStatus === '1' ? 'เปิดใช้งาน' : 'ปิดใช้งาน';
            });

            // ส่งคำขอไปยังเซิร์ฟเวอร์เพื่ออัพเดตสถานะทั้งหมด
            fetch('<?php echo base_url('Email_register/update_status_all'); ?>', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/x-www-form-urlencoded',
                    },
                    body: `new_status=${newStatus}`
                })
                .then(response => {
                    if (!response.ok) {
                        throw new Error('Network response was not ok');
                    }
                    return response.json();
                })
                .then(data => {
                    console.log('All status updated:', data);
                })
                .catch(error => {
                    console.error('Error updating all status:', error);
                    // ย้อนกลับสถานะเดิมถ้ามีข้อผิดพลาด
                    toggleStatuses.forEach(toggle => {
                        toggle.checked = !toggle.checked;
                        const statusLabel = toggle.closest('.status-container').querySelector('.status-label');
                        statusLabel.textContent = toggle.checked ? 'เปิดใช้งาน' : 'ปิดใช้งาน';
                    });
                });
        });
    });
</script>

<script>
    // ไม่ใช้ jQuery document ready เพื่อลดการซ้ำซ้อน
    document.addEventListener('DOMContentLoaded', function() {
        // สำหรับตั้งค่าการแจ้งเตือน
        const lineNotificationToggle = document.getElementById('lineNotificationToggle');
        const notificationStatus = document.getElementById('notificationStatus');
        const testNotificationBtn = document.getElementById('testNotificationBtn');

        if (lineNotificationToggle) {
            lineNotificationToggle.addEventListener('change', function() {
                const isChecked = this.checked;

                // อัพเดต UI
                notificationStatus.textContent = isChecked ? 'เปิดใช้งาน' : 'ปิดใช้งาน';
                notificationStatus.className = isChecked ?
                    'toggle-status status-active' :
                    'toggle-status status-inactive';

                // ส่ง AJAX
                lineUpdateNotificationStatus(isChecked ? '1' : '0');
            });
        }

        if (testNotificationBtn) {
            testNotificationBtn.addEventListener('click', function() {
                lineSendTestNotification();
            });
        }

        // สำหรับสถานะผู้ใช้
        const lineUserStatusToggles = document.querySelectorAll('.line-user-status');

        lineUserStatusToggles.forEach(toggle => {
            toggle.addEventListener('change', function() {
                const lineId = this.getAttribute('data-id');
                const isChecked = this.checked;

                // อัพเดต UI badge
                const statusBadge = this.closest('.user-status-toggle').querySelector('.status-badge');
                statusBadge.textContent = isChecked ? 'เปิดใช้งาน' : 'ปิดใช้งาน';
                statusBadge.className = isChecked ?
                    'status-badge badge-active' :
                    'status-badge badge-inactive';

                // ส่ง AJAX
                lineUpdateUserStatus(lineId, isChecked ? 'show' : 'hide');
            });
        });
    });

    // ฟังก์ชันสำหรับอัพเดตสถานะการแจ้งเตือน
    function lineUpdateNotificationStatus(status) {
        // ใช้ fetch API แทน jQuery AJAX
        fetch('<?= site_url("Email_register/update_notification_status"); ?>', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/x-www-form-urlencoded',
                },
                body: 'status=' + status
            })
            .then(response => {
                if (!response.ok) {
                    throw new Error('Network response was not ok');
                }
                return response.json();
            })
            .then(data => {
                if (data.status === 'success') {
                    showLineAlert('success', 'สำเร็จ!', data.message);
                } else {
                    showLineAlert('error', 'ผิดพลาด!', data.message);
                }
            })
            .catch(error => {
                console.error('Error:', error);
                showLineAlert('error', 'ผิดพลาด!', 'ไม่สามารถเชื่อมต่อกับเซิร์ฟเวอร์ได้');
            });
    }

    // ฟังก์ชันสำหรับอัพเดตสถานะผู้ใช้
    function lineUpdateUserStatus(lineId, status) {
        // ใช้ fetch API แทน jQuery AJAX
        fetch('<?= site_url("line_backend/update_line_status"); ?>', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/x-www-form-urlencoded',
                },
                body: 'line_id=' + lineId + '&new_status=' + status
            })
            .then(response => {
                if (!response.ok) {
                    throw new Error('Network response was not ok');
                }
                return response.json();
            })
            .then(data => {
                if (data.status === 'success') {
                    showLineAlert('success', 'สำเร็จ!', 'อัพเดตสถานะเรียบร้อย');
                } else {
                    showLineAlert('error', 'ผิดพลาด!', data.message || 'เกิดข้อผิดพลาดในการอัพเดตสถานะ');
                }
            })
            .catch(error => {
                console.error('Error:', error);
                showLineAlert('error', 'ผิดพลาด!', 'ไม่สามารถเชื่อมต่อกับเซิร์ฟเวอร์ได้');
            });
    }

    // ฟังก์ชันสำหรับส่งการแจ้งเตือนทดสอบ
    function lineSendTestNotification() {
        // แสดงสถานะกำลังโหลด
        const testBtn = document.getElementById('testNotificationBtn');
        testBtn.classList.add('loading');

        fetch('<?= site_url("Email_register/test_line_notification"); ?>', {
                method: 'GET'
            })
            .then(response => {
                if (!response.ok) {
                    throw new Error('Network response was not ok');
                }
                return response.json();
            })
            .then(data => {
                if (data.status === 'success') {
                    showLineAlert('success', 'สำเร็จ!', 'ส่งข้อความทดสอบเรียบร้อย');
                } else {
                    showLineAlert('error', 'ผิดพลาด!', data.message || 'เกิดข้อผิดพลาดในการส่งข้อความทดสอบ');
                }

                // ซ่อนสถานะกำลังโหลด
                testBtn.classList.remove('loading');
            })
            .catch(error => {
                console.error('Error:', error);
                showLineAlert('error', 'ผิดพลาด!', 'ไม่สามารถเชื่อมต่อกับเซิร์ฟเวอร์ได้');

                // ซ่อนสถานะกำลังโหลด
                testBtn.classList.remove('loading');
            });
    }

    // ฟังก์ชันสำหรับแสดงการแจ้งเตือน
    function showLineAlert(type, title, message) {
        const alertContainer = document.getElementById('lineAlertContainer');
        const id = 'alert-' + Date.now();
        const iconClass = type === 'success' ? 'bi-check-circle-fill text-success' : 'bi-exclamation-circle-fill text-danger';

        const alertHtml = `
            <div id="${id}" class="line-alert alert-${type}">
                <div class="alert-header">
                    <div class="alert-title">
                        <i class="bi ${iconClass}"></i> ${title}
                    </div>
                    <button onclick="closeLineAlert('${id}')" class="alert-close">&times;</button>
                </div>
                <div class="alert-body">
                    ${message}
                </div>
            </div>
        `;

        alertContainer.insertAdjacentHTML('beforeend', alertHtml);

        // แสดงด้วย animation
        setTimeout(() => {
            document.getElementById(id).classList.add('show');
        }, 10);

        // ซ่อนอัตโนมัติหลังจาก 5 วินาที
        setTimeout(() => {
            closeLineAlert(id);
        }, 5000);
    }

    // ฟังก์ชันปิดการแจ้งเตือน
    function closeLineAlert(id) {
        const alert = document.getElementById(id);
        if (alert) {
            alert.classList.remove('show');
            setTimeout(() => {
                alert.remove();
            }, 300);
        }
    }

    // ฟังก์ชันยืนยันการลบ
    function lineConfirmDelete(lineId) {
        // เปิด modal
        const modal = document.getElementById('deleteConfirmModal');
        modal.style.display = 'flex';

        // ตั้งค่า event listener สำหรับปุ่มยืนยัน
        const confirmBtn = document.getElementById('confirmDeleteBtn');

        // ลบ event listener เดิม (ถ้ามี)
        const newConfirmBtn = confirmBtn.cloneNode(true);
        confirmBtn.parentNode.replaceChild(newConfirmBtn, confirmBtn);

        // เพิ่ม event listener ใหม่
        newConfirmBtn.addEventListener('click', function() {
            window.location.href = "<?= site_url('line_backend/del/'); ?>" + lineId;
        });
    }

    // ฟังก์ชันปิด modal
    function closeLineModal(modalId) {
        const modal = document.getElementById(modalId);
        modal.style.display = 'none';
    }
</script>