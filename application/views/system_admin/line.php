<!-- ส่วนตั้งค่าการแจ้งเตือน Line OA แบบใหม่ -->
<style>
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

    .btn.add-btn.insert-vulgar-btn {
        background: linear-gradient(135deg, #FFD700, #FFC107);
        border-color: #FFD700;
        color: #000;
        /* ตัวอักษรสีดำ */
    }

    .popup-content {
        background-color: #fff;
        margin: 10% auto;
        padding: 20px;
        border: 1px solid #888;
        width: 30%;
        border-radius: 24px;
    }
</style>

<!-- หน้าจัดการการแจ้งเตือน Line OA -->
<div class="container2">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h4 class="m-0">ลงทะเบียน Line OA</h4>
        <div class="action-buttons">
            <a class="btn btn-line" href="<?= site_url('Line_backend/login'); ?>">
                <i class="bi bi-plus-circle"></i> ลงทะเบียนไลน์
            </a>
            <a class="btn add-btn insert-vulgar-btn" data-target="#popupInsert">
                Scan QR code เพิ่มเพื่อน
            </a>
            <a class="btn btn-light" href="<?= site_url('line_backend'); ?>">
                <i class="bi bi-arrow-clockwise"></i> รีเฟรชข้อมูล
            </a>

            <div id="popupInsert" class="popup">
                <div class="popup-content">
                    <h4>ลงทะเบียน Line OA</h4>
                    <div class="text-center mb-3">
                        <img src="docs/ScanLineOA.png" alt="Line OA QR Code" style="max-width: 200px;">
                    </div>
                    <div class="steps">
                        <ol>
                            <li>Add Line OA เป็นเพื่อน</li>
                            <li>กดปุ่มลงทะเบียนไลน์ <span style="color: #00C851;">[ปุ่มสีเขียว]</span></li>
                            <li>รอรับข้อความยืนยันการลงทะเบียนสำเร็จ</li>
                        </ol>
                    </div>
                    <div class="text-center mt-3">
                        <button class="btn btn-danger close-button" onclick="closeRegisterPopup()">ปิด</button>
                    </div>
                </div>
            </div>
            <script>
                function closeRegisterPopup() {
                    document.querySelector('.popup').style.display = 'none';
                }
            </script>
        </div>
    </div>

    <!-- ส่วนการตั้งค่า -->
    <!-- <div class="line-container">
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
			
			<div class="setting-row">
    			<div class="setting-label">
        			<div class="setting-title">จัดการอีเมลแจ้งเตือน</div>
        			<div class="setting-description">จัดการรายชื่ออีเมลสำหรับรับการแจ้งเตือน</div>
    			</div>
    			<a href="<?= site_url('Email_register'); ?>" class="test-notification-btn test-email-btn">
        			<span class="btn-text"><i class="bi bi-envelope"></i> จัดการอีเมล</span>
    			</a>
			</div>
        </div>
    </div> -->

    <!-- ตารางรายการผู้ใช้ -->
    <div class="line-users-container">
        <div class="line-users-header">
            รายการผู้ใช้ Line OA
        </div>

        <div class="card-body">
            <div class="table-responsive">
                <table id="lineUsersTable" class="table">
                    <thead>
                        <tr>
                            <th class="text-dark" style="width: 5%;">ลำดับ</th>
                            <th class="text-dark" style="width: 10%;">รูป</th>
                            <th class="text-dark" style="width: 20%;">ชื่อไลน์</th>
                            <th class="text-dark" style="width: 25%;">UID</th>
                            <th class="text-dark" style="width: 20%;">สถานะ</th>
                            <th class="text-dark" style="width: 10%;">วันที่</th>
                            <th class="text-dark" style="width: 10%;">จัดการ</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php
                        $Index = 1;
                        foreach ($query as $rs) { ?>
                            <tr>
                                <td class="text-center"><?= $Index; ?></td>
                                <td><img src="<?php echo $rs->line_picture; ?>" width="50" height="50" class="rounded-circle" alt="<?php echo $rs->line_name; ?>"></td>
                                <td><?php echo $rs->line_name; ?></td>
                                <td><small class="text-muted"><?php echo $rs->line_user_id; ?></small></td>
                                <td>
                                    <div class="user-status-toggle">
                                        <label class="line-toggle mb-0" style="transform: scale(0.8);">
                                            <input type="checkbox" class="line-user-status"
                                                data-id="<?= $rs->line_id; ?>"
                                                <?= $rs->line_status === 'show' ? 'checked' : ''; ?>>
                                            <span class="line-slider"></span>
                                        </label>
                                        <span class="status-badge <?= $rs->line_status === 'show' ? 'badge-active' : 'badge-inactive'; ?>">
                                            <?= $rs->line_status === 'show' ? 'เปิดใช้งาน' : 'ปิดใช้งาน'; ?>
                                        </span>
                                    </div>
                                </td>
                                <td><small><?= date('d/m/Y H:i', strtotime($rs->line_datesave . '+543 years')) ?> น.</small></td>
                                <td>
                                    <div class="action-buttons">
                                        <a href="<?= site_url('line_backend/editing/' . $rs->line_id); ?>" class="action-btn edit-btn">
                                            <i class="bi bi-pencil-square"></i>
                                        </a>
                                        <button class="action-btn delete-btn" onclick="lineConfirmDelete(<?= $rs->line_id; ?>);">
                                            <i class="bi bi-trash"></i>
                                        </button>
                                    </div>
                                </td>
                            </tr>
                        <?php
                            $Index++;
                        } ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<!-- Container สำหรับแสดงการแจ้งเตือน -->
<div class="alert-container" id="lineAlertContainer"></div>

<!-- Modal ยืนยันการลบ -->
<div class="line-modal" id="deleteConfirmModal">
    <div class="modal-content">
        <div class="modal-header">
            <div class="modal-title">ยืนยันการลบ</div>
            <button class="modal-close" onclick="closeLineModal('deleteConfirmModal')">&times;</button>
        </div>
        <div class="modal-body text-center">
            <div class="mb-4">
                <i class="bi bi-exclamation-triangle text-warning" style="font-size: 48px;"></i>
            </div>
            <h5 class="mb-3">ยืนยันการลบรายการนี้</h5>
            <p class="mb-4">คุณจะไม่สามารถกู้คืนข้อมูลนี้ได้อีก</p>
            <div class="d-flex justify-content-center">
                <button id="confirmDeleteBtn" class="btn btn-danger me-2">ใช่, ต้องการลบ!</button>
                <button class="btn btn-secondary" onclick="closeLineModal('deleteConfirmModal')">ยกเลิก</button>
            </div>
        </div>
    </div>
</div>

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
        fetch('<?= site_url("Line_backend/update_notification_status"); ?>', {
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

        fetch('<?= site_url("Line_backend/test_line_notification"); ?>', {
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