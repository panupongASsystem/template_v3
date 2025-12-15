<div class="container-fluid">
    <div class="d-sm-flex align-items-center justify-content-between mb-4">
        <h1 class="h3 mb-0 text-gray-800">บันทึกการใช้งานระบบทั้งหมด</h1>
        <div>
            <a href="<?= site_url('logs_controller/statistics'); ?>" class="btn btn-info btn-sm">
                <i class="fas fa-chart-bar"></i> ดูสถิติ
            </a>
            <a href="<?= site_url('logs_controller/export_csv?' . http_build_query($filters)); ?>" class="btn btn-success btn-sm">
                <i class="fas fa-download"></i> ส่งออก CSV
            </a>
        </div>
    </div>

    <!-- ฟอร์มกรองข้อมูล -->
    <div class="card mb-4">
        <div class="card-header">
            <h6 class="m-0 font-weight-bold text-primary">
                <i class="fas fa-filter"></i> ตัวกรองข้อมูล
            </h6>
        </div>
        <div class="card-body">
            <form method="GET" action="<?= site_url('logs_controller/index'); ?>" id="filterForm">
                <div class="row">
                    <div class="col-md-2">
                        <label for="menu">เมนู</label>
                        <select name="menu" class="form-control form-control-sm">
                            <option value="">ทั้งหมด</option>
                            <?php if (isset($available_menus) && !empty($available_menus)): ?>
                                <?php foreach ($available_menus as $menu_item): ?>
                                    <option value="<?= $menu_item->menu; ?>" <?= ($filters['menu'] == $menu_item->menu) ? 'selected' : ''; ?>>
                                        <?= $menu_item->menu; ?>
                                    </option>
                                <?php endforeach; ?>
                            <?php else: ?>
                                <!-- Fallback ถ้าไม่มีข้อมูลใน logs -->
                                <option value="ข่าวประชาสัมพันธ์" <?= ($filters['menu'] == 'ข่าวประชาสัมพันธ์') ? 'selected' : ''; ?>>ข่าวประชาสัมพันธ์</option>
                                <option value="จัดการผู้ใช้" <?= ($filters['menu'] == 'จัดการผู้ใช้') ? 'selected' : ''; ?>>จัดการผู้ใช้</option>
                                <option value="จัดการสินค้า" <?= ($filters['menu'] == 'จัดการสินค้า') ? 'selected' : ''; ?>>จัดการสินค้า</option>
                                <option value="จัดการไฟล์" <?= ($filters['menu'] == 'จัดการไฟล์') ? 'selected' : ''; ?>>จัดการไฟล์</option>
                                <option value="ระบบจัดการ" <?= ($filters['menu'] == 'ระบบจัดการ') ? 'selected' : ''; ?>>ระบบจัดการ</option>
                                <option value="รายงาน" <?= ($filters['menu'] == 'รายงาน') ? 'selected' : ''; ?>>รายงาน</option>
                                <option value="การเงิน" <?= ($filters['menu'] == 'การเงิน') ? 'selected' : ''; ?>>การเงิน</option>
                            <?php endif; ?>
                        </select>
                    </div>
                    <div class="col-md-2">
                        <label for="action">การดำเนินการ</label>
                        <select name="action" class="form-control form-control-sm">
                            <option value="">ทั้งหมด</option>
                            <option value="เพิ่ม" <?= ($filters['action'] == 'เพิ่ม') ? 'selected' : ''; ?>>เพิ่ม</option>
                            <option value="แก้ไข" <?= ($filters['action'] == 'แก้ไข') ? 'selected' : ''; ?>>แก้ไข</option>
                            <option value="ลบ" <?= ($filters['action'] == 'ลบ') ? 'selected' : ''; ?>>ลบ</option>
                        </select>
                    </div>
                    <div class="col-md-2">
                        <label for="user_id">ผู้ใช้</label>
                        <select name="user_id" class="form-control form-control-sm">
                            <option value="">ทุกคน</option>
                            <?php if (isset($users) && !empty($users)): ?>
                                <?php foreach ($users as $user): ?>
                                    <option value="<?= $user->m_id; ?>" <?= ($filters['user_id'] == $user->m_id) ? 'selected' : ''; ?>>
                                        <?= $user->m_username; ?> (<?= $user->m_fname; ?>)
                                    </option>
                                <?php endforeach; ?>
                            <?php endif; ?>
                        </select>
                    </div>
                    <div class="col-md-2">
                        <label for="date_from">วันที่เริ่มต้น</label>
                        <input type="date" name="date_from" class="form-control form-control-sm" value="<?= $filters['date_from']; ?>">
                    </div>
                    <div class="col-md-2">
                        <label for="date_to">วันที่สิ้นสุด</label>
                        <input type="date" name="date_to" class="form-control form-control-sm" value="<?= $filters['date_to']; ?>">
                    </div>
                    <div class="col-md-2">
                        <label>&nbsp;</label><br>
                        <button type="submit" class="btn btn-primary btn-sm">
                            <i class="fas fa-search"></i> ค้นหา
                        </button>
                        <a href="<?= site_url('logs_controller'); ?>" class="btn btn-secondary btn-sm">
                            <i class="fas fa-undo"></i> รีเซ็ต
                        </a>
                    </div>
                </div>

                <!-- Quick Filter Buttons -->
                <div class="row mt-3">
                    <div class="col-md-12">
                        <small class="text-muted">ช่วงเวลา:</small>
                        <button type="button" class="btn btn-outline-primary btn-sm ml-1" onclick="setDateRange('today')">วันนี้</button>
                        <button type="button" class="btn btn-outline-primary btn-sm" onclick="setDateRange('yesterday')">เมื่อวาน</button>
                        <button type="button" class="btn btn-outline-primary btn-sm" onclick="setDateRange('week')">7 วันที่ผ่านมา</button>
                        <button type="button" class="btn btn-outline-primary btn-sm" onclick="setDateRange('month')">30 วันที่ผ่านมา</button>
                    </div>
                </div>
            </form>
        </div>
    </div>

    <!-- ตารางแสดงข้อมูล logs -->
    <div class="card shadow mb-4">
        <div class="card-header py-3 d-flex justify-content-between align-items-center">
            <h6 class="m-0 font-weight-bold text-primary">
                <i class="fas fa-list"></i> บันทึกการใช้งาน
                <?php if (!empty($logs)): ?>
                    <span class="badge badge-primary"><?= count($logs); ?> รายการ</span>
                <?php endif; ?>
            </h6>

            <!-- Clean Old Logs Button -->
            <?php if ($_SESSION['m_level'] == 1) : ?>
                <button type="button" class="btn btn-warning btn-sm" onclick="showCleanModal()">
                    <i class="fas fa-trash-alt"></i> ลบข้อมูลเก่า
                </button>
            <?php endif; ?>
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-bordered table-hover" id="logsTable">
                    <thead class="thead-light">
                        <tr>
                            <th style="width: 3%;">#</th>
                            <th style="width: 12%;">วันที่-เวลา</th>
                            <th style="width: 12%;">ผู้ใช้</th>
                            <th style="width: 8%;">การดำเนินการ</th>
                            <th style="width: 10%;">เมนู</th>
                            <th style="width: 25%;">รายละเอียด</th>
                            <th style="width: 10%;">IP Address</th>
                            <th style="width: 15%;">User Agent</th>
                            <th style="width: 5%;">ข้อมูลเพิ่มเติม</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (!empty($logs)): ?>
                            <?php $index = 1; ?>
                            <?php foreach ($logs as $log): ?>
                                <tr>
                                    <td><?= $index++; ?></td>
                                    <td>
                                        <small>
                                            <?= date('d/m/Y', strtotime($log->created_at . '+543 years')); ?><br>
                                            <strong><?= date('H:i:s', strtotime($log->created_at)); ?></strong>
                                        </small>
                                    </td>
                                    <td>
                                        <strong class="text-primary"><?= $log->username; ?></strong><br>
                                        <small class="text-muted"><?= $log->full_name; ?></small>
                                    </td>
                                    <td>
                                        <?php
                                        $badge_class = '';
                                        $icon = '';
                                        switch ($log->action) {
											case 'เพิ่มหัวข้อ':
                                                $badge_class = 'badge-success';
                                                $icon = 'fas fa-plus';
                                                break;
                                            case 'แก้ไขหัวข้อ':
                                                $badge_class = 'badge-warning';
                                                $icon = 'fas fa-edit';
                                                break;
                                            case 'เพิ่ม':
                                                $badge_class = 'badge-success';
                                                $icon = 'fas fa-plus';
                                                break;
                                            case 'แก้ไข':
                                                $badge_class = 'badge-warning';
                                                $icon = 'fas fa-edit';
                                                break;
                                            case 'ลบ':
                                                $badge_class = 'badge-danger';
                                                $icon = 'fas fa-trash';
                                                break;
                                            case 'เข้าชม':
                                                $badge_class = 'badge-info';
                                                $icon = 'fas fa-eye';
                                                break;
                                            case 'ดาวน์โหลด':
                                                $badge_class = 'badge-primary';
                                                $icon = 'fas fa-download';
                                                break;
                                            default:
                                                $badge_class = 'badge-secondary';
                                                $icon = 'fas fa-question';
                                        }
                                        ?>
                                        <span class="badge <?= $badge_class; ?>">
                                            <i class="<?= $icon; ?>"></i> <?= $log->action; ?>
                                        </span>
                                    </td>
                                    <td>
                                        <span class="badge badge-outline-dark"><?= $log->menu; ?></span>
                                    </td>
                                    <td class="limited-text" title="<?= htmlspecialchars($log->item_name); ?>">
                                        <?= $log->item_name; ?>
                                        <?php if ($log->item_id): ?>
                                            <br><small class="text-muted">ID: <?= $log->item_id; ?></small>
                                        <?php endif; ?>
                                    </td>
                                    <td>
                                        <small class="text-monospace"><?= $log->ip_address; ?></small>
                                    </td>
                                    <td>
                                        <small class="limited-text" title="<?= htmlspecialchars($log->user_agent); ?>">
                                            <?= substr($log->user_agent, 0, 30); ?>
                                            <?= strlen($log->user_agent) > 30 ? '...' : ''; ?>
                                        </small>
                                    </td>
                                    <td class="text-center">
                                        <?php if ($log->additional_data): ?>
                                            <button type="button" class="btn btn-sm btn-outline-info"
                                                onclick="showAdditionalData('<?= htmlspecialchars($log->additional_data); ?>', '<?= $log->log_id; ?>')">
                                                <i class="fas fa-eye"></i>
                                            </button>
                                        <?php else: ?>
                                            <span class="text-muted">-</span>
                                        <?php endif; ?>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        <?php else: ?>
                            <tr>
                                <td colspan="9" class="text-center text-muted">
                                    <i class="fas fa-inbox fa-2x mb-2"></i><br>
                                    ไม่พบข้อมูล logs ตามเงื่อนไขที่กำหนด
                                </td>
                            </tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>

            <!-- Pagination -->
            <?php if (!empty($pagination)): ?>
                <div class="row mt-3">
                    <div class="col-md-12 d-flex justify-content-center">
                        <?= $pagination; ?>
                    </div>
                </div>
            <?php endif; ?>
        </div>
    </div>
</div>

<!-- Modal สำหรับแสดงข้อมูลเพิ่มเติม -->
<div class="modal fade" id="additionalDataModal" tabindex="-1" role="dialog">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">
                    <i class="fas fa-info-circle"></i> ข้อมูลเพิ่มเติม - Log ID: <span id="logId"></span>
                </h5>
                <button type="button" class="close" onclick="$('#additionalDataModal').modal('hide')" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <pre id="additionalDataContent" class="bg-light p-3 rounded"></pre>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" onclick="$('#additionalDataModal').modal('hide')" aria-label="Close">
                    <i class="fas fa-times"></i> ปิด
                </button>
            </div>
        </div>
    </div>
</div>

<!-- Modal สำหรับลบข้อมูลเก่า -->
<div class="modal fade" id="cleanLogsModal" tabindex="-1" role="dialog">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">
                    <i class="fas fa-trash-alt text-warning"></i> ลบข้อมูล Logs เก่า
                </h5>
                <button type="button" class="close" onclick="$('#cleanLogsModal').modal('hide')" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <form action="<?= site_url('logs_controller/clean_old_logs'); ?>" method="POST">
                <div class="modal-body">
                    <div class="alert alert-warning">
                        <i class="fas fa-exclamation-triangle"></i>
                        <strong>คำเตือน:</strong> การดำเนินการนี้จะลบข้อมูล logs เก่าถาวร และไม่สามารถกู้คืนได้
                    </div>

                    <div class="form-group">
                        <label for="days">ลบข้อมูลที่เก่าเกิน (วัน):</label>
                        <select name="days" class="form-control" required>
                            <option value="30">30 วัน</option>
                            <option value="60">60 วัน</option>
                            <option value="90" selected>90 วัน</option>
                            <option value="180">180 วัน</option>
                            <option value="365">1 ปี</option>
                        </select>
                        <small class="form-text text-muted">
                            ข้อมูลที่เก่าเกินจำนวนวันที่เลือกจะถูกลบทิ้ง
                        </small>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" onclick="$('#cleanLogsModal').modal('hide')" aria-label="Close">
                        <i class="fas fa-times"></i> ยกเลิก
                    </button>
                    <button type="submit" class="btn btn-warning">
                        <i class="fas fa-trash-alt"></i> ลบข้อมูลเก่า
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
    // แสดงข้อมูลเพิ่มเติม
    function showAdditionalData(data, logId) {
        try {
            const jsonData = JSON.parse(data);
            document.getElementById('additionalDataContent').textContent = JSON.stringify(jsonData, null, 2);
        } catch (e) {
            document.getElementById('additionalDataContent').textContent = data;
        }
        document.getElementById('logId').textContent = logId;
        $('#additionalDataModal').modal('show');
    }

    // แสดง modal ลบข้อมูลเก่า
    function showCleanModal() {
        $('#cleanLogsModal').modal('show');
    }

    // ตั้งค่าช่วงวันที่
    function setDateRange(range) {
        const today = new Date();
        const dateFrom = document.querySelector('input[name="date_from"]');
        const dateTo = document.querySelector('input[name="date_to"]');

        let fromDate, toDate;

        switch (range) {
            case 'today':
                fromDate = toDate = formatDate(today);
                break;
            case 'yesterday':
                const yesterday = new Date(today);
                yesterday.setDate(today.getDate() - 1);
                fromDate = toDate = formatDate(yesterday);
                break;
            case 'week':
                const weekAgo = new Date(today);
                weekAgo.setDate(today.getDate() - 7);
                fromDate = formatDate(weekAgo);
                toDate = formatDate(today);
                break;
            case 'month':
                const monthAgo = new Date(today);
                monthAgo.setDate(today.getDate() - 30);
                fromDate = formatDate(monthAgo);
                toDate = formatDate(today);
                break;
        }

        dateFrom.value = fromDate;
        dateTo.value = toDate;
    }

    // Format date สำหรับ input[type="date"]
    function formatDate(date) {
        return date.getFullYear() + '-' +
            String(date.getMonth() + 1).padStart(2, '0') + '-' +
            String(date.getDate()).padStart(2, '0');
    }

    // DataTable initialization
    $(document).ready(function() {
        $('#logsTable').DataTable({
            "order": [
                [1, "desc"]
            ], // เรียงตามวันที่ล่าสุด
            "pageLength": 25,
            "lengthMenu": [
                [25, 50, 100, -1],
                [25, 50, 100, "ทั้งหมด"]
            ],
            "language": {
                "lengthMenu": "แสดง _MENU_ รายการ",
                "zeroRecords": "ไม่พบข้อมูล",
                "info": "แสดงหน้า _PAGE_ จาก _PAGES_ (ทั้งหมด _TOTAL_ รายการ)",
                "infoEmpty": "ไม่มีข้อมูล",
                "infoFiltered": "(กรองจากทั้งหมด _MAX_ รายการ)",
                "search": "ค้นหาในตาราง:",
                "paginate": {
                    "first": "หน้าแรก",
                    "last": "หน้าสุดท้าย",
                    "next": "ถัดไป",
                    "previous": "ก่อนหน้า"
                },
                "emptyTable": "ไม่มีข้อมูลในตาราง",
                "loadingRecords": "กำลังโหลด...",
                "processing": "กำลังประมวลผล..."
            },
            "columnDefs": [{
                    "orderable": false,
                    "targets": [0, 8]
                }, // ห้ามเรียงคอลัมน์ # และข้อมูลเพิ่มเติม
                {
                    "searchable": false,
                    "targets": [0, 8]
                } // ห้ามค้นหาในคอลัมน์เหล่านี้
            ]
        });

        // Auto-submit form เมื่อเปลี่ยน filter
        $('select[name="menu"], select[name="action"], select[name="user_id"]').change(function() {
            // Uncomment บรรทัดนี้หากต้องการ auto-submit
            // $('#filterForm').submit();
        });
    });

    // แจ้งเตือนเมื่อบันทึกสำเร็จ
    <?php if ($this->session->flashdata('clean_success')): ?>
        Swal.fire({
            title: 'สำเร็จ!',
            text: '<?= $this->session->flashdata('clean_success'); ?>',
            icon: 'success',
            confirmButtonText: 'ตกลง'
        });
    <?php endif; ?>
</script>

<style>
    .limited-text {
        max-width: 200px;
        overflow: hidden;
        text-overflow: ellipsis;
        white-space: nowrap;
    }

    .badge {
        font-size: 0.75em;
    }

    .table td {
        vertical-align: middle;
    }

    .table th {
        background-color: #f8f9fc;
        border-color: #e3e6f0;
    }

    .table-hover tbody tr:hover {
        background-color: rgba(0, 123, 255, 0.1);
    }

    #additionalDataContent {
        background-color: #f8f9fa;
        border: 1px solid #dee2e6;
        border-radius: 4px;
        padding: 15px;
        max-height: 400px;
        overflow-y: auto;
        font-family: 'Courier New', monospace;
        font-size: 0.9em;
    }

    .text-monospace {
        font-family: 'Courier New', monospace;
    }

    .badge-outline-dark {
        color: #343a40;
        border: 1px solid #343a40;
        background-color: transparent;
    }

    /* Responsive adjustments */
    @media (max-width: 768px) {
        .table-responsive {
            font-size: 0.8em;
        }

        .limited-text {
            max-width: 150px;
        }
    }

    /* Loading spinner */
    .table-loading {
        position: relative;
    }

    .table-loading::after {
        content: "";
        position: absolute;
        top: 50%;
        left: 50%;
        width: 20px;
        height: 20px;
        margin: -10px 0 0 -10px;
        border: 2px solid #f3f3f3;
        border-top: 2px solid #007bff;
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

    /* แก้ไขปัญหา Modal backdrop ทึบ */
    .modal-backdrop {
        z-index: 1040 !important;
    }

    .modal {
        z-index: 1050 !important;
    }

    .modal-dialog {
        z-index: 1060 !important;
    }

    /* แก้ไขการแสดงผล Modal */
    .modal.fade .modal-dialog {
        transform: translate(0, -50px);
        transition: transform 0.3s ease-out;
    }

    .modal.show .modal-dialog {
        transform: none;
    }

    /* แก้ไข overlay ที่อาจชนกัน */
    .swal2-container {
        z-index: 1070 !important;
    }

    /* ป้องกัน Modal ซ้อนกัน */
    body.modal-open {
        overflow: hidden;
    }

    /* แก้ไขการ scroll */
    .modal-open .modal {
        overflow-x: hidden;
        overflow-y: auto;
    }
</style>