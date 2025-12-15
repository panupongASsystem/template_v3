<!-- Modal เพิ่ม Slots -->
<div class="modal fade" id="addSlotsModal" tabindex="-1" role="dialog">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header bg-success text-white">
                <h5 class="modal-title">
                    <i class="fas fa-plus"></i> เพิ่มตำแหน่งใหม่
                </h5>
                <button type="button" class="close text-white" onclick="$('#addSlotsModal').modal('hide')" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <div class="alert alert-info">
                    <h6><i class="fas fa-info-circle"></i> รายละเอียด</h6>
                    <p class="mb-0">
                        ระบบจะเพิ่มตำแหน่งว่างใหม่ต่อจากตำแหน่งสุดท้าย
                        <strong>ปัจจุบันมี <?= $total_slots ?? 61 ?> ตำแหน่ง</strong>
                    </p>
                </div>

                <div class="form-group">
                    <label for="slots-count">จำนวนตำแหน่งที่ต้องการเพิ่ม:</label>
                    <select id="slots-count" class="form-control">
                        <option value="1">1 ตำแหน่ง</option>
                        <option value="11">11 ตำแหน่ง</option>
                        <option value="21">21 ตำแหน่ง</option>
                        <option value="31">31 ตำแหน่ง</option>
                        <option value="41">41 ตำแหน่ง</option>
                        <option value="51">51 ตำแหน่ง</option>
                        <option value="custom">กำหนดเอง</option>
                    </select>
                </div>

                <div class="form-group" id="custom-count-group" style="display: none;">
                    <label for="custom-slots-count">จำนวนที่ต้องการ (1-51):</label>
                    <input type="number" id="custom-slots-count" class="form-control"
                        min="1" max="51" value="1" placeholder="ระบุจำนวน">
                    <small class="form-text text-muted">สามารถเพิ่มได้สูงสุด 51 ตำแหน่งต่อครั้ง</small>
                </div>

                <div class="card bg-light">
                    <div class="card-body">
                        <h6>ตำแหน่งใหม่ที่จะสร้าง:</h6>
                        <div id="new-slots-preview">
                            จะเป็นตำแหน่งที่ <strong><?= ($total_slots ?? 61) + 1 ?></strong>
                        </div>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" onclick="$('#addSlotsModal').modal('hide')" aria-label="Close">ยกเลิก</button>
                <button type="button" class="btn btn-success" id="confirm-add-slots-btn">
                    <i class="fas fa-plus"></i> เพิ่มตำแหน่ง
                </button>
            </div>
        </div>
    </div>
</div>

<!-- Modal ยืนยันการลบข้อมูลทั้งหมด -->
<div class="modal fade" id="clearAllModal" tabindex="-1" role="dialog">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header bg-danger text-white">
                <h5 class="modal-title">
                    <i class="fas fa-exclamation-triangle"></i> ยืนยันการลบข้อมูลทั้งหมด
                </h5>
                <button type="button" class="close text-white" onclick="$('#clearAllModal').modal('hide')" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>

            </div>
            <div class="modal-body">
                <div class="alert alert-danger">
                    <h6><i class="fas fa-warning"></i> คำเตือนสำคัญ</h6>
                    <p class="mb-0">คุณกำลังจะลบข้อมูลทั้งหมดใน<?= $type->pname ?> การดำเนินการนี้จะ:</p>
                    <ul class="mt-2 mb-0">
                        <li><strong>ลบข้อมูลบุคลากรทั้งหมด</strong> (ชื่อ, ตำแหน่ง, เบอร์โทร, อีเมล)</li>
                        <li><strong>ลบไฟล์รูปภาพทั้งหมด</strong>ออกจากเซิร์ฟเวอร์</li>
                        <li><strong>ตำแหน่งทั้ง 61 ช่องจะกลับมาเป็นช่องว่าง</strong></li>
                        <li><strong>การกระทำนี้ไม่สามารถยกเลิกได้</strong></li>
                    </ul>
                </div>

                <div class="card">
                    <div class="card-header">
                        <h6 class="mb-0">ข้อมูลปัจจุบัน</h6>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-6">
                                <div class="stat-item">
                                    <span class="stat-label">จำนวนตำแหน่งที่มีข้อมูล:</span>
                                    <span class="stat-value text-danger" id="current-filled-count"><?= $filled_count ?></span>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="stat-item">
                                    <span class="stat-label">จำนวนตำแหน่งว่าง:</span>
                                    <span class="stat-value"><?= 61 - $filled_count ?></span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="mt-3">
                    <div class="form-group">
                        <label for="clear-confirmation-text">พิมพ์ <strong>ลบทั้งหมด</strong> เพื่อยืนยัน:</label>
                        <input type="text" class="form-control" id="clear-confirmation-text"
                            placeholder="ลบทั้งหมด" autocomplete="off">
                    </div>
                    <div class="form-check">
                        <input type="checkbox" class="form-check-input" id="confirm-clear-all-checkbox">
                        <label class="form-check-label" for="confirm-clear-all-checkbox">
                            <strong>ฉันเข้าใจและยืนยันที่จะลบข้อมูลทั้งหมด</strong>
                        </label>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" onclick="$('#clearAllModal').modal('hide')" aria-label="Close">ยกเลิก</button>
                <button type="button" class="btn btn-danger" id="confirm-clear-all-btn" disabled>
                    <i class="fas fa-trash"></i> ยืนยันการลบทั้งหมด
                </button>
            </div>
        </div>
    </div>
</div>

<!-- Modal สำรองข้อมูล -->
<div class="modal fade" id="restoreModal" tabindex="-1" role="dialog">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">
                    <i class="fas fa-upload text-warning"></i> กู้คืนข้อมูลจากไฟล์สำรอง
                </h5>
                <button type="button" class="close" onclick="$('#restoreModal').modal('hide')" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <form id="restoreForm" action="<?= site_url('dynamic_position_backend/restore_from_backup/' . $type->peng) ?>"
                method="post" enctype="multipart/form-data">
                <div class="modal-body">
                    <div class="alert alert-warning">
                        <h6><i class="fas fa-exclamation-triangle"></i> คำเตือน</h6>
                        <p class="mb-0">การกู้คืนข้อมูลจะเขียนทับข้อมูลที่มีอยู่ทั้งหมด กรุณาสำรองข้อมูลปัจจุบันก่อน</p>
                    </div>

                    <div class="form-group">
                        <label for="backup_file">เลือกไฟล์สำรองข้อมูล (.json):</label>
                        <input type="file" name="backup_file" id="backup_file" class="form-control-file"
                            accept=".json" required>
                        <small class="form-text text-muted">
                            รองรับเฉพาะไฟล์ JSON ที่สร้างจากระบบนี้เท่านั้น
                        </small>
                    </div>

                    <div class="form-check">
                        <input type="checkbox" class="form-check-input" id="confirm-restore-checkbox">
                        <label class="form-check-label" for="confirm-restore-checkbox">
                            ฉันได้สำรองข้อมูลปัจจุบันแล้วและยืนยันการกู้คืน
                        </label>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" onclick="$('#restoreModal').modal('hide')" aria-label="Close">
                        <i class="fas fa-times"></i> ยกเลิก
                    </button>
                    <button type="submit" class="btn btn-warning" id="restore-btn" disabled>
                        <i class="fas fa-upload"></i> กู้คืนข้อมูล
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Modal แสดงผลการตรวจสอบข้อมูล -->
<div class="modal fade" id="dataIntegrityModal" tabindex="-1" role="dialog">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">
                    <i class="fas fa-shield-alt text-info"></i> ผลการตรวจสอบความสมบูรณ์ของข้อมูล
                </h5>
                <button type="button" class="close" onclick="$('#dataIntegrityModal').modal('hide')" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <div id="integrity-results">
                    <!-- จะถูกเติมด้วย JavaScript -->
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" onclick="$('#dataIntegrityModal').modal('hide')" aria-label="Close">ปิด</button>
                <button type="button" class="btn btn-warning" id="repair-data-btn" style="display: none;">
                    <i class="fas fa-wrench"></i> ซ่อมแซมข้อมูล
                </button>
            </div>
        </div>
    </div>
</div><!-- dynamic_position_grid.php - หน้าจัดการแบบ Grid 61 ช่อง (เพิ่มระบบลบแบบเลือกรายการ) -->
<div class="container-fluid">
    <div class="d-sm-flex align-items-center justify-content-between mb-4">
        <h1 class="h3 mb-0 text-gray-800">จัดการ<?= $type->pname ?></h1>
        <div>
            <!-- <a href="<?= site_url('dynamic_position_backend') ?>" class="btn btn-secondary">
                <i class="fas fa-arrow-left"></i> กลับ
            </a> -->
            <a href="<?= site_url('dynamic_position_backend/refresh_grid/' . $type->peng) ?>"
                class="btn btn-light">
                <i class="fas fa-sync"></i> รีเฟรช
            </a>
        </div>
    </div>

    <!-- แสดงข้อความแจ้งเตือน -->
    <?php if ($this->session->flashdata('save_success')): ?>
        <div class="alert alert-success alert-dismissible fade show">
            <strong>สำเร็จ!</strong> บันทึกข้อมูลเรียบร้อยแล้ว
            <button type="button" class="close" data-dismiss="alert">&times;</button>
        </div>
    <?php endif; ?>

    <?php if ($this->session->flashdata('del_success')): ?>
        <div class="alert alert-success alert-dismissible fade show">
            <strong>สำเร็จ!</strong> ลบข้อมูลเรียบร้อยแล้ว
            <button type="button" class="close" data-dismiss="alert">&times;</button>
        </div>
    <?php endif; ?>

    <?php if ($this->session->flashdata('bulk_delete_success')): ?>
        <div class="alert alert-success alert-dismissible fade show">
            <strong>สำเร็จ!</strong> ลบข้อมูลที่เลือกเรียบร้อยแล้ว
            <button type="button" class="close" data-dismiss="alert">&times;</button>
        </div>
    <?php endif; ?>

    <div class="card-header py-3 d-flex justify-content-between align-items-center">
        <h6 class="m-0 font-weight-bold text-primary">
            จัดการข้อมูล<?= $type->pname ?>
            <span class="badge badge-info ml-2"><?= $filled_count ?>/<?= $total_slots ?? 61 ?> ตำแหน่ง</span>
        </h6>

        <!-- Control Panel -->
        <div class="d-flex align-items-center">
            <!-- Slot Management -->
            <div class="btn-group mr-3" role="group">
                <button type="button" class="btn btn-outline-success btn-sm" onclick="showAddSlotsModal()">
                    <i class="fas fa-plus"></i> เพิ่ม Slots
                </button>
                <!-- <button type="button" class="btn btn-outline-info btn-sm" onclick="refreshSlotStats()">
                    <i class="fas fa-chart-bar"></i> สถิติ
                </button> -->
            </div>

            <!-- Selection Mode Toggle -->
            <div class="custom-control custom-switch d-inline-block mr-3">
                <input type="checkbox" class="custom-control-input" id="selection-mode-switch">
                <label class="custom-control-label" for="selection-mode-switch">โหมดเลือกข้อมูล</label>
            </div>

            <!-- Drag & Drop Mode Toggle -->
            <div class="custom-control custom-switch d-inline-block mr-3">
                <input type="checkbox" class="custom-control-input" id="drag-mode-switch">
                <label class="custom-control-label" for="drag-mode-switch">โหมดจัดตำแหน่ง</label>
            </div>

            <!-- Advanced Actions Dropdown -->
            <div class="dropdown">
                <!-- <button class="btn btn-outline-secondary dropdown-toggle" type="button"
                    id="advancedActionsDropdown"
                    data-bs-toggle="dropdown"
                    aria-expanded="false">
                    <i class="fas fa-cog"></i> การจัดการเพิ่มเติม
                </button> -->
                <ul class="dropdown-menu dropdown-menu-end" aria-labelledby="advancedActionsDropdown">
                    <li>
                        <h6 class="dropdown-header">การจัดการข้อมูล</h6>
                    </li>
                    <li>
                        <button class="dropdown-item" type="button" onclick="clearAllPositions()">
                            <i class="fas fa-trash text-danger"></i> ลบข้อมูลทั้งหมด
                        </button>
                    </li>
                    <li>
                        <button class="dropdown-item" type="button" onclick="verifyDataIntegrity()">
                            <i class="fas fa-shield-alt text-info"></i> ตรวจสอบข้อมูล
                        </button>
                    </li>
                    <li>
                        <hr class="dropdown-divider">
                    </li>
                    <li>
                        <h6 class="dropdown-header">สำรองข้อมูล</h6>
                    </li>
                    <li>
                        <a class="dropdown-item" href="<?= site_url('dynamic_position_backend/backup_position_type/' . $type->peng) ?>">
                            <i class="fas fa-download text-success"></i> สำรองข้อมูล
                        </a>
                    </li>
                    <li>
                        <button class="dropdown-item" type="button" onclick="showRestoreModal()">
                            <i class="fas fa-upload text-warning"></i> กู้คืนข้อมูล
                        </button>
                    </li>
                    <li>
                        <hr class="dropdown-divider">
                    </li>
                    <li>
                        <h6 class="dropdown-header">รายงาน</h6>
                    </li>
                    <li>
                        <a class="dropdown-item" href="<?= site_url('dynamic_position_backend/export_csv/' . $type->peng) ?>">
                            <i class="fas fa-file-csv text-success"></i> ส่งออก CSV
                        </a>
                        <a class="dropdown-item" href="<?= site_url('dynamic_position_backend/export_json/' . $type->peng) ?>">
                            <i class="fas fa-file-code text-info"></i> ส่งออก JSON
                        </a>
                        <a class="dropdown-item" href="<?= site_url('dynamic_position_backend/export_html/' . $type->peng) ?>" target="_blank">
                            <i class="fas fa-print text-warning"></i> พิมพ์รายงาน
                        </a>
                        <a class="dropdown-item" href="<?= site_url('dynamic_position_backend/generate_usage_report/' . $type->peng) ?>">
                            <i class="fas fa-chart-bar text-primary"></i> รายงานสถิติ CSV
                        </a>
                    </li>
                </ul>
            </div>
        </div>
    </div>

    <div class="card-body">
        <!-- คำแนะนำการใช้งาน -->
        <div class="alert alert-info" id="normal-instruction">
            <i class="fas fa-info-circle"></i>
            <strong>วิธีใช้:</strong> คลิกช่องว่างเพื่อเพิ่มข้อมูล หรือคลิกรูปที่มีข้อมูลแล้วเพื่อแก้ไข
        </div>

        <!-- คำแนะนำสำหรับโหมดเลือก -->
        <div class="alert alert-warning" id="selection-instruction" style="display: none;">
            <i class="fas fa-check-square"></i>
            <strong>โหมดเลือกข้อมูล:</strong> เลือกรายการที่ต้องการลบ จากนั้นกดปุ่ม "ลบรายการที่เลือก"
        </div>

        <!-- Control Bar สำหรับโหมดเลือก -->
        <div class="selection-controls mb-3" style="display: none;">
            <div class="d-flex justify-content-between align-items-center bg-light p-3 rounded">
                <div>
                    <span class="selected-count">0</span> รายการที่เลือก
                    <button type="button" class="btn btn-outline-primary btn-sm ml-2" id="select-all-btn">
                        <i class="fas fa-check-double"></i> เลือกทั้งหมด
                    </button>
                    <button type="button" class="btn btn-outline-secondary btn-sm ml-1" id="clear-selection-btn">
                        <i class="fas fa-times"></i> ยกเลิกการเลือก
                    </button>
                </div>
                <div>
                    <button type="button" class="btn btn-danger" id="bulk-delete-btn" disabled>
                        <i class="fas fa-trash"></i> ลบรายการที่เลือก
                    </button>
                    <button type="button" class="btn btn-secondary ml-2" id="cancel-selection-btn">
                        <i class="fas fa-times"></i> ออกจากโหมดเลือก
                    </button>
                </div>
            </div>
        </div>

        <!-- แสดงข้อความสำหรับ drag & drop -->
        <div id="sortable-success" class="alert alert-success" style="display: none;">
            <strong>สำเร็จ!</strong> บันทึกตำแหน่งใหม่เรียบร้อยแล้ว
        </div>
        <div id="sortable-error" class="alert alert-danger" style="display: none;">
            <strong>ผิดพลาด!</strong> ไม่สามารถบันทึกตำแหน่งใหม่ได้
        </div>
        <div class="mb-3 drag-controls" style="display: none;">
            <div class="alert alert-warning">
                <i class="fas fa-hand-paper"></i> คลิกและลากเพื่อจัดตำแหน่ง จากนั้นกดปุ่ม "บันทึกตำแหน่ง"
            </div>
            <button class="btn btn-primary" id="save-positions">บันทึกตำแหน่ง</button>
            <button class="btn btn-secondary" id="cancel-sort">ยกเลิก</button>
        </div>

        <!-- แสดงตำแหน่งหลัก (Slot 1) -->
        <?php if (!empty($main_position)): ?>
            <div class="d-flex justify-content-center mb-4">
                <div class="col-md-2 mb-4 d-flex justify-content-center">
                    <?= render_position_card($main_position, $type, true) ?>
                </div>
            </div>
            <hr>
        <?php endif; ?>

        <!-- Grid Layout สำหรับตำแหน่งรอง (Slot 2-61) -->
        <div class="row" id="sortable-positions">
            <?php
            $all_slots = $all_positions ?? [];
            $max_slot = $total_slots ?? 61;

            for ($slot = 2; $slot <= $max_slot; $slot++): ?>
                <?php
                $position = null;
                foreach ($all_slots as $pos) {
                    if ($pos->position_order == $slot) {
                        $position = $pos;
                        break;
                    }
                }
                ?>
                <div class="col-lg-4 col-md-4 col-sm-4 col-4 mb-4 position-slot"
                    data-slot="<?= $slot ?>"
                    data-id="<?= $position ? $position->position_id : 0 ?>">
                    <?= render_position_card($position, $type, false, $slot) ?>
                </div>
            <?php endfor; ?>

            <!-- ปุ่มเพิ่ม slots ใหม่ -->
            <div class="col-lg-4 col-md-4 col-sm-4 col-4 mb-4">
                <div class="position-card add-more-card" onclick="showAddSlotsModal()">
                    <div class="card-placeholder">
                        <i class="fas fa-plus-circle fa-2x"></i>
                    </div>
                    <div class="card-info">
                        <strong>เพิ่มตำแหน่งใหม่</strong>
                        <div class="text-muted">คลิกเพื่อเพิ่ม slots</div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
</div>

<!-- Modal ยืนยันการลบแบบ Bulk -->
<div class="modal fade" id="bulkDeleteModal" tabindex="-1" role="dialog">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header bg-danger text-white">
                <h5 class="modal-title">
                    <i class="fas fa-exclamation-triangle"></i> ยืนยันการลบข้อมูล
                </h5>
                <button type="button" class="close text-white" onclick="$('#bulkDeleteModal').modal('hide')" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <div class="alert alert-danger">
                    <h6><i class="fas fa-warning"></i> คำเตือน</h6>
                    <p class="mb-0">คุณกำลังจะลบข้อมูลในตำแหน่งที่เลือก การดำเนินการนี้จะ:</p>
                    <ul class="mt-2 mb-0">
                        <li>ลบข้อมูลทั้งหมดในตำแหน่งที่เลือก (ชื่อ, ตำแหน่ง, เบอร์โทร, อีเมล, รูปภาพ)</li>
                        <li>ลบไฟล์รูปภาพออกจากเซิร์ฟเวอร์</li>
                        <li>ตำแหน่งจะกลับมาเป็นช่องว่าง</li>
                        <li><strong>การกระทำนี้ไม่สามารถยกเลิกได้</strong></li>
                    </ul>
                </div>

                <div class="card">
                    <div class="card-header">
                        <h6 class="mb-0">รายการที่จะลบ (<span id="delete-count">0</span> รายการ)</h6>
                    </div>
                    <div class="card-body" style="max-height: 300px; overflow-y: auto;">
                        <div id="delete-preview-list">
                            <!-- จะถูกเติมด้วย JavaScript -->
                        </div>
                    </div>
                </div>

                <div class="mt-3">
                    <div class="form-check">
                        <input type="checkbox" class="form-check-input" id="confirm-delete-checkbox">
                        <label class="form-check-label" for="confirm-delete-checkbox">
                            <strong>ฉันเข้าใจและยืนยันที่จะลบข้อมูลเหล่านี้</strong>
                        </label>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" onclick="$('#bulkDeleteModal').modal('hide')" aria-label="Close">ยกเลิก</button>
                <button type="button" class="btn btn-danger" id="confirm-bulk-delete-btn" disabled>
                    <i class="fas fa-trash"></i> ยืนยันการลบ
                </button>
            </div>
        </div>
    </div>
</div>
<!-- JavaScript สำหรับการทำงาน -->
<script>
    // เพิ่ม JavaScript สำหรับจัดการ slots
    document.addEventListener('DOMContentLoaded', function() {
        // Slots count change handler
        document.getElementById('slots-count').addEventListener('change', function() {
            const customGroup = document.getElementById('custom-count-group');
            const preview = document.getElementById('new-slots-preview');
            const currentTotal = <?= $total_slots ?? 61 ?>;

            if (this.value === 'custom') {
                customGroup.style.display = 'block';
                updateSlotsPreview(1, currentTotal);
            } else {
                customGroup.style.display = 'none';
                const count = parseInt(this.value);
                updateSlotsPreview(count, currentTotal);
            }
        });

        // Custom count input handler
        document.getElementById('custom-slots-count').addEventListener('input', function() {
            const count = Math.max(1, Math.min(20, parseInt(this.value) || 1));
            this.value = count;
            updateSlotsPreview(count, <?= $total_slots ?? 61 ?>);
        });

        // Add slots confirmation
        document.getElementById('confirm-add-slots-btn').addEventListener('click', function() {
            const slotsSelect = document.getElementById('slots-count');
            let count;

            if (slotsSelect.value === 'custom') {
                count = parseInt(document.getElementById('custom-slots-count').value) || 1;
            } else {
                count = parseInt(slotsSelect.value);
            }

            if (count < 1 || count > 20) {
                Swal.fire({
                    icon: 'warning',
                    title: 'จำนวนไม่ถูกต้อง',
                    text: 'กรุณาระบุจำนวน 1-20 ตำแหน่ง'
                });
                return;
            }

            // ยืนยันการเพิ่ม
            Swal.fire({
                title: 'ยืนยันการเพิ่มตำแหน่ง?',
                html: `
                <div class="text-left">
                    <p>จะเพิ่ม <strong>${count}</strong> ตำแหน่งใหม่</p>
                    <p>ตำแหน่งปัจจุบัน: <?= $total_slots ?? 61 ?></p>
                    <p>ตำแหน่งใหม่: ${<?= $total_slots ?? 61 ?> + 1} - ${<?= $total_slots ?? 61 ?> + count}</p>
                </div>
            `,
                icon: 'question',
                showCancelButton: true,
                confirmButtonColor: '#28a745',
                cancelButtonColor: '#6c757d',
                confirmButtonText: 'ใช่, เพิ่มเลย!',
                cancelButtonText: 'ยกเลิก'
            }).then((result) => {
                if (result.isConfirmed) {
                    addNewSlots(count);
                }
            });
        });
    });

    // Functions
    function showAddSlotsModal() {
        $('#addSlotsModal').modal('show');

        // Reset form
        document.getElementById('slots-count').value = '1';
        document.getElementById('custom-count-group').style.display = 'none';
        document.getElementById('custom-slots-count').value = '1';
        updateSlotsPreview(1, <?= $total_slots ?? 61 ?>);
    }

    function updateSlotsPreview(count, currentTotal) {
        const preview = document.getElementById('new-slots-preview');
        const start = currentTotal + 1;
        const end = currentTotal + count;

        if (count === 1) {
            preview.innerHTML = `จะเป็นตำแหน่งที่ <strong>${start}</strong>`;
        } else {
            preview.innerHTML = `จะเป็นตำแหน่งที่ <strong>${start}</strong> ถึง <strong>${end}</strong> (รวม ${count} ตำแหน่ง)`;
        }
    }

    function addNewSlots(count) {
        // แสดง loading
        Swal.fire({
            title: 'กำลังเพิ่มตำแหน่ง...',
            html: `กำลังสร้าง ${count} ตำแหน่งใหม่<br>กรุณารอสักครู่`,
            allowOutsideClick: false,
            allowEscapeKey: false,
            showConfirmButton: false,
            didOpen: () => {
                Swal.showLoading();
            }
        });

        // ส่งข้อมูลไป backend
        fetch('<?= site_url("dynamic_position_backend/add_slots/" . $type->peng) ?>', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-Requested-With': 'XMLHttpRequest'
                },
                body: JSON.stringify({
                    count: count
                })
            })
            .then(response => response.json())
            .then(data => {
                Swal.close();
                $('#addSlotsModal').modal('hide');

                if (data.success) {
                    Swal.fire({
                        icon: 'success',
                        title: 'เพิ่มตำแหน่งเรียบร้อย!',
                        html: `
                    <div class="text-left">
                        <p>เพิ่มแล้ว ${data.added_slots.length} ตำแหน่ง</p>
                        <p>ตำแหน่งใหม่: ${data.new_slots_range.start} - ${data.new_slots_range.end}</p>
                        <p>รวมทั้งหมด: ${data.total_slots} ตำแหน่ง</p>
                    </div>
                `,
                        timer: 3000,
                        showConfirmButton: false
                    }).then(() => {
                        // รีโหลดหน้า
                        window.location.reload();
                    });
                } else {
                    Swal.fire({
                        icon: 'error',
                        title: 'เกิดข้อผิดพลาด',
                        text: data.message || 'ไม่สามารถเพิ่มตำแหน่งได้'
                    });
                }
            })
            .catch(error => {
                Swal.close();
                $('#addSlotsModal').modal('hide');

                Swal.fire({
                    icon: 'error',
                    title: 'เกิดข้อผิดพลาด',
                    text: 'ไม่สามารถติดต่อเซิร์ฟเวอร์ได้'
                });
            });
    }

    function refreshSlotStats() {
        // แสดง loading
        const originalText = event.target.innerHTML;
        event.target.innerHTML = '<i class="fas fa-spinner fa-spin"></i> โหลด...';
        event.target.disabled = true;

        fetch('<?= site_url("dynamic_position_backend/get_slot_stats/" . $type->peng) ?>', {
                method: 'GET',
                headers: {
                    'X-Requested-With': 'XMLHttpRequest'
                }
            })
            .then(response => response.json())
            .then(data => {
                event.target.innerHTML = originalText;
                event.target.disabled = false;

                if (data.success) {
                    const stats = data.stats;
                    Swal.fire({
                        icon: 'info',
                        title: 'สถิติการใช้งานปัจจุบัน',
                        html: `
                    <div class="text-left">
                        <table class="table table-borderless">
                            <tr>
                                <td><strong>ตำแหน่งทั้งหมด:</strong></td>
                                <td>${stats.total_slots} ช่อง</td>
                            </tr>
                            <tr>
                                <td><strong>มีข้อมูล:</strong></td>
                                <td class="text-success">${stats.filled_slots} ช่อง</td>
                            </tr>
                            <tr>
                                <td><strong>ว่าง:</strong></td>
                                <td class="text-secondary">${stats.empty_slots} ช่อง</td>
                            </tr>
                            <tr>
                                <td><strong>อัตราการใช้งาน:</strong></td>
                                <td class="text-info">${stats.usage_percentage}%</td>
                            </tr>
                        </table>
                        <small class="text-muted">อัปเดต: ${data.updated_at}</small>
                    </div>
                `,
                        confirmButtonText: 'ปิด'
                    });
                } else {
                    Swal.fire({
                        icon: 'error',
                        title: 'ไม่สามารถโหลดสถิติได้',
                        text: data.message || 'เกิดข้อผิดพลาด'
                    });
                }
            })
            .catch(error => {
                event.target.innerHTML = originalText;
                event.target.disabled = false;

                Swal.fire({
                    icon: 'error',
                    title: 'เกิดข้อผิดพลาด',
                    text: 'ไม่สามารถติดต่อเซิร์ฟเวอร์ได้'
                });
            });
    }

    // แก้ไขเฉพาะส่วน Selection ใน dynamic_position_grid.php

    document.addEventListener('DOMContentLoaded', function() {
        let selectedSlots = new Set();
        let selectionMode = false;
        let dragMode = false;

        // Elements
        const selectionSwitch = document.getElementById('selection-mode-switch');
        const dragSwitch = document.getElementById('drag-mode-switch');
        const selectionControls = document.querySelector('.selection-controls');
        const dragControls = document.querySelector('.drag-controls');
        const normalInstruction = document.getElementById('normal-instruction');
        const selectionInstruction = document.getElementById('selection-instruction');
        const selectedCountSpan = document.querySelector('.selected-count');
        const bulkDeleteBtn = document.getElementById('bulk-delete-btn');

        // ✅ เปลี่ยนแค่ตรงนี้: ดึง position cards ทั้งหมด รวมตำแหน่งหลัก
        const positionCards = document.querySelectorAll('.position-card:not(.add-more-card)');

        // Toggle Selection Mode
        selectionSwitch.addEventListener('change', function() {
            selectionMode = this.checked;

            if (selectionMode) {
                enableSelectionMode();
                // ปิด drag mode ถ้าเปิดอยู่
                if (dragMode) {
                    dragSwitch.checked = false;
                    disableDragMode();
                }
            } else {
                disableSelectionMode();
            }
        });

        // Toggle Drag Mode
        dragSwitch.addEventListener('change', function() {
            dragMode = this.checked;

            if (dragMode) {
                enableDragMode();
                // ปิด selection mode ถ้าเปิดอยู่
                if (selectionMode) {
                    selectionSwitch.checked = false;
                    disableSelectionMode();
                }
            } else {
                disableDragMode();
            }
        });

        // Enable Selection Mode
        function enableSelectionMode() {
            selectionControls.style.display = 'block';
            normalInstruction.style.display = 'none';
            selectionInstruction.style.display = 'block';

            // ✅ เปลี่ยนแค่ตรงนี้: เพิ่ม checkbox ให้การ์ดที่มีข้อมูลทั้งหมด รวมตำแหน่งหลัก
            positionCards.forEach(function(card) {
                const hasData = card.getAttribute('data-has-data') === 'true';

                // ✅ ใหม่: หา slot จาก parent element หรือตัวการ์ดเอง
                let slot = card.getAttribute('data-slot');
                if (!slot) {
                    const parent = card.closest('.position-slot');
                    if (parent) {
                        slot = parent.getAttribute('data-slot');
                    }
                }

                if (hasData && slot) {
                    addSelectionCheckbox(card);
                    card.classList.add('selectable');
                }
            });
        }

        // Disable Selection Mode
        function disableSelectionMode() {
            selectionMode = false; // ✅ เพิ่มบรรทัดนี้

            selectionControls.style.display = 'none';
            normalInstruction.style.display = 'block';
            selectionInstruction.style.display = 'none';

            // ลบ checkbox
            document.querySelectorAll('.selection-checkbox').forEach(cb => cb.remove());
            positionCards.forEach(card => {
                card.classList.remove('selectable', 'selected');
            });

            selectedSlots.clear();
            updateSelectionUI();
        }

        // Enable Drag Mode
        function enableDragMode() {
            dragMode = true; // เพิ่มบรรทัดนี้
            dragControls.style.display = 'block';
            normalInstruction.style.display = 'none';
            const sortableContainer = document.getElementById('sortable-positions');
            sortableContainer.classList.add('sortable-mode');

            // เปิดใช้งาน sortable และเก็บ instance
            if (window.Sortable) {
                window.sortableInstance = new Sortable(sortableContainer, {
                    animation: 150,
                    ghostClass: 'sortable-ghost',
                    chosenClass: 'sortable-chosen',
                    dragClass: 'sortable-drag'
                });
            }
        }

        // Disable Drag Mode
        function disableDragMode() {
            dragMode = false; // เพิ่มบรรทัดนี้
            dragControls.style.display = 'none';
            normalInstruction.style.display = 'block';
            const sortableContainer = document.getElementById('sortable-positions');
            sortableContainer.classList.remove('sortable-mode');

            // ทำลาย Sortable instance
            if (window.sortableInstance) {
                window.sortableInstance.destroy();
                window.sortableInstance = null;
            }
        }
        // Add Selection Checkbox
        function addSelectionCheckbox(card) {
            // ✅ ปรับปรุง: หา slot อย่างชาญฉลาด
            let slot = card.getAttribute('data-slot');
            if (!slot) {
                const parent = card.closest('.position-slot');
                if (parent) {
                    slot = parent.getAttribute('data-slot');
                }
            }

            // ✅ ป้องกันการสร้าง checkbox ซ้ำ
            if (!slot || card.querySelector('.selection-checkbox')) {
                return;
            }

            const checkbox = document.createElement('div');
            checkbox.className = 'selection-checkbox';
            checkbox.innerHTML = `
            <div class="custom-control custom-checkbox">
                <input type="checkbox" class="custom-control-input slot-checkbox" 
                       id="slot-${slot}">
                <label class="custom-control-label" for="slot-${slot}"></label>
            </div>
        `;

            card.appendChild(checkbox);

            // Event listener สำหรับ checkbox
            const checkboxInput = checkbox.querySelector('input');
            checkboxInput.addEventListener('change', function() {
                if (this.checked) {
                    selectedSlots.add(slot);
                    card.classList.add('selected');
                } else {
                    selectedSlots.delete(slot);
                    card.classList.remove('selected');
                }
                updateSelectionUI();
            });
        }

        // Update Selection UI
        function updateSelectionUI() {
            const count = selectedSlots.size;
            selectedCountSpan.textContent = count;
            bulkDeleteBtn.disabled = count === 0;
        }

        // การคลิกช่องเพื่อเพิ่ม/แก้ไขข้อมูล (เฉพาะเมื่อไม่ได้อยู่ในโหมดเลือก)
        positionCards.forEach(function(card) {
            card.addEventListener('click', function(e) {
                // ถ้าคลิกที่ checkbox ให้ไม่ทำงาน
                if (e.target.closest('.selection-checkbox')) {
                    return;
                }

                // ถ้าอยู่ในโหมดเลือก ให้คลิกที่การ์ดเป็นการเลือก
                if (selectionMode) {
                    const hasData = this.getAttribute('data-has-data') === 'true';

                    // ✅ ปรับปรุง: หา slot อย่างชาญฉลาด
                    let slot = this.getAttribute('data-slot');
                    if (!slot) {
                        const parent = this.closest('.position-slot');
                        if (parent) {
                            slot = parent.getAttribute('data-slot');
                        }
                    }

                    if (hasData && slot) {
                        const checkbox = this.querySelector('.slot-checkbox');
                        if (checkbox) {
                            checkbox.checked = !checkbox.checked;
                            checkbox.dispatchEvent(new Event('change'));
                        }
                    }
                    return;
                }

                // โหมดปกติ - ไปหน้าเพิ่ม/แก้ไข
                if (!dragMode) {
                    // ✅ ปรับปรุง: หา slot อย่างชาญฉลาด
                    let slot = this.getAttribute('data-slot');
                    if (!slot) {
                        const parent = this.closest('.position-slot');
                        if (parent) {
                            slot = parent.getAttribute('data-slot');
                        }
                    }

                    const hasData = this.getAttribute('data-has-data') === 'true';
                    const positionId = this.getAttribute('data-position-id');

                    if (hasData && positionId && slot) {
                        window.location.href = '<?= site_url("dynamic_position_backend/edit_slot/" . $type->peng . "/") ?>' + slot;
                    } else if (slot) {
                        window.location.href = '<?= site_url("dynamic_position_backend/add_to_slot/" . $type->peng . "/") ?>' + slot;
                    }
                }
            });
        });

        // Select All Button
        document.getElementById('select-all-btn').addEventListener('click', function() {
            const checkboxes = document.querySelectorAll('.slot-checkbox');
            const allChecked = Array.from(checkboxes).every(cb => cb.checked);

            checkboxes.forEach(function(checkbox) {
                if (!allChecked) {
                    checkbox.checked = true;
                } else {
                    checkbox.checked = false;
                }
                checkbox.dispatchEvent(new Event('change'));
            });
        });

        // Clear Selection Button
        document.getElementById('clear-selection-btn').addEventListener('click', function() {
            document.querySelectorAll('.slot-checkbox').forEach(function(checkbox) {
                checkbox.checked = false;
                checkbox.dispatchEvent(new Event('change'));
            });
        });

        // Cancel Selection Button
        document.getElementById('cancel-selection-btn').addEventListener('click', function() {
            selectionSwitch.checked = false;
            disableSelectionMode();
        });

        // Bulk Delete Button
        bulkDeleteBtn.addEventListener('click', function() {
            if (selectedSlots.size === 0) return;

            showBulkDeleteModal();
        });

        // Show Bulk Delete Modal
        function showBulkDeleteModal() {
            const modal = document.getElementById('bulkDeleteModal');
            const deleteCountSpan = document.getElementById('delete-count');
            const previewList = document.getElementById('delete-preview-list');

            deleteCountSpan.textContent = selectedSlots.size;

            // สร้างรายการตัวอย่าง
            let previewHTML = '';
            selectedSlots.forEach(function(slot) {
                // ✅ ปรับปรุง: หาการ์ดอย่างชาญฉลาด
                let card = document.querySelector(`[data-slot="${slot}"]`);
                if (!card) {
                    // หาจาก parent element
                    const parent = document.querySelector(`.position-slot[data-slot="${slot}"]`);
                    if (parent) {
                        card = parent.querySelector('.position-card');
                    }
                }

                if (card) {
                    const nameElement = card.querySelector('.card-info strong');
                    const name = nameElement ? nameElement.textContent : 'ไม่ระบุชื่อ';

                    previewHTML += `
                <div class="d-flex align-items-center mb-2 p-2 bg-light rounded">
                    <div class="text-danger mr-2">
                        <i class="fas fa-trash"></i>
                    </div>
                    <div>
                        <strong>Slot ${slot}:</strong> ${name}
                    </div>
                </div>
            `;
                }
            });

            previewList.innerHTML = previewHTML;

            // Reset confirmation checkbox
            document.getElementById('confirm-delete-checkbox').checked = false;
            document.getElementById('confirm-bulk-delete-btn').disabled = true;

            $(modal).modal('show');
        }

        // Confirmation Checkbox
        document.getElementById('confirm-delete-checkbox').addEventListener('change', function() {
            document.getElementById('confirm-bulk-delete-btn').disabled = !this.checked;
        });

        // Confirm Bulk Delete
        document.getElementById('confirm-bulk-delete-btn').addEventListener('click', function() {
            if (selectedSlots.size === 0) return;

            // แสดง loading
            Swal.fire({
                title: 'กำลังลบข้อมูล...',
                html: `กำลังลบข้อมูล ${selectedSlots.size} รายการ<br>กรุณารอสักครู่`,
                allowOutsideClick: false,
                allowEscapeKey: false,
                showConfirmButton: false,
                didOpen: () => {
                    Swal.showLoading();
                }
            });

            // ส่งข้อมูลไปลบ
            const slotsArray = Array.from(selectedSlots);

            fetch('<?= site_url("dynamic_position_backend/bulk_clear_slots/" . $type->peng) ?>', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-Requested-With': 'XMLHttpRequest'
                    },
                    body: JSON.stringify({
                        slots: slotsArray
                    })
                })
                .then(response => response.json())
                .then(data => {
                    Swal.close();
                    $('#bulkDeleteModal').modal('hide');

                    if (data.success) {
                        Swal.fire({
                            icon: 'success',
                            title: 'ลบข้อมูลเรียบร้อย!',
                            text: `ลบข้อมูล ${data.deleted_count} รายการเรียบร้อยแล้ว`,
                            timer: 2000,
                            showConfirmButton: false
                        }).then(() => {
                            // รีโหลดหน้า
                            window.location.reload();
                        });
                    } else {
                        Swal.fire({
                            icon: 'error',
                            title: 'เกิดข้อผิดพลาด',
                            text: data.message || 'ไม่สามารถลบข้อมูลได้'
                        });
                    }
                })
                .catch(error => {
                    Swal.close();
                    $('#bulkDeleteModal').modal('hide');

                    Swal.fire({
                        icon: 'error',
                        title: 'เกิดข้อผิดพลาด',
                        text: 'ไม่สามารถติดต่อเซิร์ฟเวอร์ได้'
                    });
                });
        });

        // Drag & Drop functionality (เดิม)
        // Drag & Drop functionality - แก้ไขส่วน save-positions
        document.getElementById('save-positions')?.addEventListener('click', function() {
            const positions = [];
            const slots = document.querySelectorAll('.position-slot');

            slots.forEach(function(slot, index) {
                const positionId = slot.getAttribute('data-id');
                if (positionId && positionId !== '0') {
                    positions.push({
                        id: positionId,
                        position: index
                    });
                }
            });

            // แสดง loading
            Swal.fire({
                title: 'กำลังบันทึกตำแหน่ง...',
                html: 'กรุณารอสักครู่',
                allowOutsideClick: false,
                allowEscapeKey: false,
                showConfirmButton: false,
                didOpen: () => {
                    Swal.showLoading();
                }
            });

            fetch('<?= site_url("dynamic_position_backend/update_positions/" . $type->peng) ?>', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-Requested-With': 'XMLHttpRequest'
                    },
                    body: JSON.stringify({
                        positions: positions
                    })
                })
                .then(response => response.json())
                .then(data => {
                    Swal.close();

                    if (data.success) {
                        // ปิดโหมดจัดตำแหน่งอัตโนมัติ
                        dragSwitch.checked = false;
                        disableDragMode();

                        // แสดงข้อความสำเร็จ
                        Swal.fire({
                            icon: 'success',
                            title: 'บันทึกตำแหน่งเรียบร้อย!',
                            text: 'จัดเรียงตำแหน่งใหม่เสร็จสิ้น',
                            timer: 2000,
                            showConfirmButton: false
                        });
                    } else {
                        Swal.fire({
                            icon: 'error',
                            title: 'เกิดข้อผิดพลาด',
                            text: 'ไม่สามารถบันทึกตำแหน่งได้'
                        });
                    }
                })
                .catch(error => {
                    Swal.close();
                    Swal.fire({
                        icon: 'error',
                        title: 'เกิดข้อผิดพลาด',
                        text: 'ไม่สามารถติดต่อเซิร์ฟเวอร์ได้'
                    });
                });
        });

        // Cancel Sort Button
        document.getElementById('cancel-sort')?.addEventListener('click', function() {
            dragSwitch.checked = false;
            disableDragMode();
            location.reload();
        });
    });

    // เพิ่มฟังก์ชันเหล่านี้ใน JavaScript section ของ dynamic_position_grid.php

    // ฟังก์ชันลบข้อมูลทั้งหมด
    function clearAllPositions() {
        // แสดง Modal ยืนยันการลบข้อมูลทั้งหมด
        $('#clearAllModal').modal('show');

        // อัพเดตจำนวนตำแหน่งที่มีข้อมูลใน Modal
        document.getElementById('current-filled-count').textContent = '<?= $filled_count ?>';

        // Reset form
        document.getElementById('clear-confirmation-text').value = '';
        document.getElementById('confirm-clear-all-checkbox').checked = false;
        document.getElementById('confirm-clear-all-btn').disabled = true;
    }

    // ฟังก์ชันตรวจสอบข้อมูล
    function verifyDataIntegrity() {
        // แสดง loading
        Swal.fire({
            title: 'กำลังตรวจสอบข้อมูล...',
            html: 'กรุณารอสักครู่',
            allowOutsideClick: false,
            allowEscapeKey: false,
            showConfirmButton: false,
            didOpen: () => {
                Swal.showLoading();
            }
        });

        // เรียก API ตรวจสอบข้อมูล
        fetch('<?= site_url("dynamic_position_backend/verify_data_integrity/" . $type->peng) ?>', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-Requested-With': 'XMLHttpRequest'
                }
            })
            .then(response => response.json())
            .then(data => {
                Swal.close();

                if (data.success) {
                    // แสดงผลการตรวจสอบใน Modal
                    showDataIntegrityResults(data);
                } else {
                    Swal.fire({
                        icon: 'error',
                        title: 'เกิดข้อผิดพลาด',
                        text: data.message || 'ไม่สามารถตรวจสอบข้อมูลได้'
                    });
                }
            })
            .catch(error => {
                Swal.close();
                Swal.fire({
                    icon: 'error',
                    title: 'เกิดข้อผิดพลาด',
                    text: 'ไม่สามารถติดต่อเซิร์ฟเวอร์ได้'
                });
            });
    }

    // ฟังก์ชันแสดงผลการตรวจสอบข้อมูล
    function showDataIntegrityResults(data) {
        const resultsDiv = document.getElementById('integrity-results');
        const repairBtn = document.getElementById('repair-data-btn');

        let resultsHTML = '';

        // สถิติทั่วไป
        resultsHTML += `
        <div class="row mb-4">
            <div class="col-md-3">
                <div class="card text-center">
                    <div class="card-body">
                        <h5 class="card-title text-primary">${data.stats.total_positions}</h5>
                        <p class="card-text">ตำแหน่งทั้งหมด</p>
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="card text-center">
                    <div class="card-body">
                        <h5 class="card-title text-success">${data.stats.filled_positions}</h5>
                        <p class="card-text">มีข้อมูล</p>
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="card text-center">
                    <div class="card-body">
                        <h5 class="card-title text-warning">${data.stats.missing_files}</h5>
                        <p class="card-text">ไฟล์หาย</p>
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="card text-center">
                    <div class="card-body">
                        <h5 class="card-title text-danger">${data.stats.corrupted_data}</h5>
                        <p class="card-text">ข้อมูลเสียหาย</p>
                    </div>
                </div>
            </div>
        </div>
    `;

        // สถานะรวม
        if (data.is_healthy) {
            resultsHTML += `
            <div class="alert alert-success">
                <h5><i class="fas fa-check-circle"></i> ข้อมูลสมบูรณ์</h5>
                <p class="mb-0">ข้อมูลทั้งหมดอยู่ในสภาพที่ดี ไม่พบปัญหาใดๆ</p>
            </div>
        `;
            repairBtn.style.display = 'none';
        } else {
            resultsHTML += `
            <div class="alert alert-warning">
                <h5><i class="fas fa-exclamation-triangle"></i> พบปัญหาในข้อมูล</h5>
                <p class="mb-0">พบปัญหา ${data.issues.length} รายการ กรุณาตรวจสอบรายละเอียดด้านล่าง</p>
            </div>
        `;

            // รายการปัญหา
            if (data.issues.length > 0) {
                resultsHTML += '<h6>รายการปัญหาที่พบ:</h6><div class="list-group mb-3">';
                data.issues.forEach(issue => {
                    resultsHTML += `
                    <div class="list-group-item list-group-item-warning">
                        <i class="fas fa-exclamation-triangle text-warning mr-2"></i>
                        ${issue}
                    </div>
                `;
                });
                resultsHTML += '</div>';
            }

            repairBtn.style.display = 'inline-block';
        }

        resultsHTML += `
        <div class="text-muted small">
            ตรวจสอบเมื่อ: ${data.checked_at}
        </div>
    `;

        resultsDiv.innerHTML = resultsHTML;
        $('#dataIntegrityModal').modal('show');
    }

    // ฟังก์ชันซ่อมแซมข้อมูล
    function repairData() {
        Swal.fire({
            title: 'ยืนยันการซ่อมแซมข้อมูล?',
            text: 'ระบบจะแก้ไขปัญหาที่พบอัตโนมัติ',
            icon: 'question',
            showCancelButton: true,
            confirmButtonColor: '#ffc107',
            cancelButtonColor: '#6c757d',
            confirmButtonText: 'ใช่, ซ่อมแซม!',
            cancelButtonText: 'ยกเลิก'
        }).then((result) => {
            if (result.isConfirmed) {
                // แสดง loading
                Swal.fire({
                    title: 'กำลังซ่อมแซมข้อมูล...',
                    html: 'กรุณารอสักครู่',
                    allowOutsideClick: false,
                    allowEscapeKey: false,
                    showConfirmButton: false,
                    didOpen: () => {
                        Swal.showLoading();
                    }
                });

                // เรียก API ซ่อมแซมข้อมูล
                fetch('<?= site_url("dynamic_position_backend/repair_data/" . $type->peng) ?>', {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'X-Requested-With': 'XMLHttpRequest'
                        }
                    })
                    .then(response => response.json())
                    .then(data => {
                        Swal.close();
                        $('#dataIntegrityModal').modal('hide');

                        if (data.success) {
                            let message = 'ซ่อมแซมข้อมูลเรียบร้อยแล้ว';
                            if (data.actions && data.actions.length > 0) {
                                message += '\n\nการดำเนินการ:\n' + data.actions.join('\n');
                            }

                            Swal.fire({
                                icon: 'success',
                                title: 'ซ่อมแซมเสร็จสิ้น',
                                text: message,
                                timer: 3000,
                                showConfirmButton: false
                            }).then(() => {
                                // รีโหลดหน้า
                                window.location.reload();
                            });
                        } else {
                            Swal.fire({
                                icon: 'error',
                                title: 'การซ่อมแซมล้มเหลว',
                                text: data.message || 'ไม่สามารถซ่อมแซมข้อมูลได้'
                            });
                        }
                    })
                    .catch(error => {
                        Swal.close();
                        $('#dataIntegrityModal').modal('hide');

                        Swal.fire({
                            icon: 'error',
                            title: 'เกิดข้อผิดพลาด',
                            text: 'ไม่สามารถติดต่อเซิร์ฟเวอร์ได้'
                        });
                    });
            }
        });
    }

    // ฟังก์ชันแสดง Modal กู้คืนข้อมูล
    function showRestoreModal() {
        $('#restoreModal').modal('show');

        // Reset form
        document.getElementById('backup_file').value = '';
        document.getElementById('confirm-restore-checkbox').checked = false;
        document.getElementById('restore-btn').disabled = true;
    }

    // Event Listeners เพิ่มเติม
    document.addEventListener('DOMContentLoaded', function() {
        // Event สำหรับ Clear All Modal
        const clearConfirmationText = document.getElementById('clear-confirmation-text');
        const confirmClearAllCheckbox = document.getElementById('confirm-clear-all-checkbox');
        const confirmClearAllBtn = document.getElementById('confirm-clear-all-btn');

        function updateClearAllButton() {
            const textMatches = clearConfirmationText.value.trim() === 'ลบทั้งหมด';
            const checkboxChecked = confirmClearAllCheckbox.checked;
            confirmClearAllBtn.disabled = !(textMatches && checkboxChecked);
        }

        clearConfirmationText.addEventListener('input', updateClearAllButton);
        confirmClearAllCheckbox.addEventListener('change', updateClearAllButton);

        // ยืนยันการลบทั้งหมด
        confirmClearAllBtn.addEventListener('click', function() {
            $('#clearAllModal').modal('hide');

            // แสดง loading
            Swal.fire({
                title: 'กำลังลบข้อมูลทั้งหมด...',
                html: 'กรุณารอสักครู่ การดำเนินการนี้อาจใช้เวลาสักครู่',
                allowOutsideClick: false,
                allowEscapeKey: false,
                showConfirmButton: false,
                didOpen: () => {
                    Swal.showLoading();
                }
            });

            // เรียก API ลบข้อมูลทั้งหมด
            fetch('<?= site_url("dynamic_position_backend/clear_all_positions/" . $type->peng) ?>', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-Requested-With': 'XMLHttpRequest'
                    }
                })
                .then(response => response.json())
                .then(data => {
                    Swal.close();

                    if (data.success) {
                        Swal.fire({
                            icon: 'success',
                            title: 'ลบข้อมูลเรียบร้อย!',
                            html: `
                        <div class="text-left">
                            <p>ลบข้อมูลเรียบร้อย ${data.cleared_count} ตำแหน่ง</p>
                            <p>ลบไฟล์ทั้งหมด ${data.deleted_files_count} ไฟล์</p>
                        </div>
                    `,
                            timer: 3000,
                            showConfirmButton: false
                        }).then(() => {
                            // รีโหลดหน้า
                            window.location.reload();
                        });
                    } else {
                        Swal.fire({
                            icon: 'error',
                            title: 'เกิดข้อผิดพลาด',
                            text: data.message || 'ไม่สามารถลบข้อมูลได้'
                        });
                    }
                })
                .catch(error => {
                    Swal.close();

                    Swal.fire({
                        icon: 'error',
                        title: 'เกิดข้อผิดพลาด',
                        text: 'ไม่สามารถติดต่อเซิร์ฟเวอร์ได้'
                    });
                });
        });

        // Event สำหรับ Restore Modal
        const confirmRestoreCheckbox = document.getElementById('confirm-restore-checkbox');
        const restoreBtn = document.getElementById('restore-btn');
        const backupFileInput = document.getElementById('backup_file');

        function updateRestoreButton() {
            const fileSelected = backupFileInput.files.length > 0;
            const checkboxChecked = confirmRestoreCheckbox.checked;
            restoreBtn.disabled = !(fileSelected && checkboxChecked);
        }

        confirmRestoreCheckbox.addEventListener('change', updateRestoreButton);
        backupFileInput.addEventListener('change', updateRestoreButton);

        // Event สำหรับปุ่มซ่อมแซมข้อมูล
        document.getElementById('repair-data-btn').addEventListener('click', repairData);
    });

    // ฟังก์ชันตรวจสอบไฟล์ที่อัพโหลด
    function validateBackupFile(file) {
        if (!file) return false;

        // ตรวจสอบ extension
        const fileName = file.name.toLowerCase();
        if (!fileName.endsWith('.json')) {
            Swal.fire({
                icon: 'error',
                title: 'ประเภทไฟล์ไม่ถูกต้อง',
                text: 'กรุณาเลือกไฟล์ .json เท่านั้น'
            });
            return false;
        }

        // ตรวจสอบขนาดไฟล์ (สูงสุด 10MB)
        if (file.size > 10 * 1024 * 1024) {
            Swal.fire({
                icon: 'error',
                title: 'ไฟล์ใหญ่เกินไป',
                text: 'ขนาดไฟล์ต้องไม่เกิน 10MB'
            });
            return false;
        }

        return true;
    }
</script>

<!-- CSS สำหรับการแสดงผล -->
<style>
    .add-more-card {
        border: 2px dashed #28a745 !important;
        background: linear-gradient(135deg, #f8fff9 0%, #e8f5e8 100%) !important;
        color: #28a745 !important;
        cursor: pointer;
        transition: all 0.3s ease;
    }

    .add-more-card:hover {
        border-color: #1e7e34 !important;
        background: linear-gradient(135deg, #e8f5e8 0%, #d4edda 100%) !important;
        transform: translateY(-2px);
        box-shadow: 0 6px 12px rgba(40, 167, 69, 0.3);
    }

    .add-more-card .card-placeholder {
        color: #28a745;
    }

    .add-more-card .card-info strong {
        color: #1e7e34;
    }

    .btn-group .btn-sm {
        padding: 0.25rem 0.75rem;
        font-size: 0.875rem;
    }

    /* Modal improvements */
    .modal-content {
        border-radius: 0.5rem;
        border: none;
        box-shadow: 0 0.5rem 1rem rgba(0, 0, 0, 0.15);
    }

    .modal-header.bg-success {
        border-top-left-radius: 0.5rem;
        border-top-right-radius: 0.5rem;
    }

    .table-borderless td {
        border: none;
        padding: 0.25rem 0.5rem;
    }

    /* Loading spinner */
    .fa-spinner {
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

    /* Responsive */
    @media (max-width: 768px) {
        .btn-group {
            flex-direction: column;
            width: 100%;
        }

        .btn-group .btn {
            border-radius: 0.25rem !important;
            margin-bottom: 0.25rem;
        }

        .d-flex.align-items-center {
            flex-direction: column;
            align-items: stretch !important;
        }

        .d-flex.align-items-center>* {
            margin-bottom: 0.5rem;
        }

        .modal-dialog {
            margin: 1rem;
        }
    }

    .position-card {
        border: 2px dashed #dee2e6;
        border-radius: 8px;
        padding: 15px;
        text-align: center;
        cursor: pointer;
        transition: all 0.3s ease;
        min-height: 200px;
        display: flex;
        flex-direction: column;
        justify-content: center;
        align-items: center;
        background: #ffffff;
        position: relative;
    }

    .position-card:hover {
        border-color: #007bff;
        box-shadow: 0 4px 8px rgba(0, 123, 255, 0.2);
        transform: translateY(-2px);
    }

    .position-card.filled {
        border: 2px solid #28a745;
        background: linear-gradient(135deg, #f8f9fa 0%, #e9ecef 100%);
    }

    .position-card.filled:hover {
        border-color: #155724;
        box-shadow: 0 6px 12px rgba(40, 167, 69, 0.3);
    }

    .position-card.selectable {
        cursor: pointer;
    }

    .position-card.selectable:hover {
        border-color: #ffc107;
        box-shadow: 0 4px 8px rgba(255, 193, 7, 0.3);
    }

    .position-card.selected {
        border: 3px solid #dc3545;
        background: linear-gradient(135deg, #ffe6e6 0%, #ffcccc 100%);
        transform: scale(0.95);
    }

    .position-card.selected:hover {
        border-color: #c82333;
        box-shadow: 0 6px 12px rgba(220, 53, 69, 0.4);
    }

    .position-card .card-image {
        width: 80px;
        height: 80px;
        border-radius: 50%;
        object-fit: cover;
        margin-bottom: 10px;
        border: 3px solid #fff;
        box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
    }

    .position-card .card-placeholder {
        width: 80px;
        height: 80px;
        border-radius: 50%;
        background: #dee2e6;
        display: flex;
        align-items: center;
        justify-content: center;
        margin-bottom: 10px;
        color: #6c757d;
        font-size: 24px;
    }

    .position-card .card-info {
        font-size: 12px;
        line-height: 1.4;
        color: #495057;
    }

    .position-card .card-info strong {
        color: #212529;
        display: block;
        margin-bottom: 2px;
    }

    .position-card .slot-number {
        position: absolute;
        top: 5px;
        right: 5px;
        background: #007bff;
        color: white;
        border-radius: 50%;
        width: 24px;
        height: 24px;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 10px;
        font-weight: bold;
        z-index: 5;
    }

    .position-card.selected .slot-number {
        background: #dc3545;
    }

    .position-card.main-position {
        border: 3px solid #ffc107;
        background: linear-gradient(135deg, #fff3cd 0%, #ffeaa7 100%);
        min-height: 250px;
    }

    .position-card.main-position .card-image {
        width: 120px;
        height: 120px;
    }

    .position-card.main-position .card-placeholder {
        width: 120px;
        height: 120px;
        font-size: 36px;
    }

    /* Selection Checkbox */
    .selection-checkbox {
        position: absolute;
        top: 8px;
        left: 8px;
        z-index: 10;
        background: rgba(255, 255, 255, 0.9);
        border-radius: 4px;
        padding: 4px;
        box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
    }

    .selection-checkbox .custom-control-label::before {
        border: 2px solid #007bff;
        background-color: #fff;
    }

    .selection-checkbox .custom-control-input:checked~.custom-control-label::before {
        background-color: #007bff;
    }

    .selection-checkbox .custom-control-input:checked~.custom-control-label::after {
        color: #fff;
    }

    /* Selection Controls */
    .selection-controls {
        animation: slideDown 0.3s ease;
    }

    @keyframes slideDown {
        from {
            opacity: 0;
            transform: translateY(-10px);
        }

        to {
            opacity: 1;
            transform: translateY(0);
        }
    }

    /* Drag & Drop Styles */
    .sortable-mode .position-card {
        cursor: move;
    }

    .sortable-ghost {
        opacity: 0.5;
    }

    .sortable-chosen {
        transform: scale(1.05);
    }

    .sortable-drag {
        transform: rotate(5deg);
    }

    /* Modal Styles */
    .modal-content {
        border-radius: 0.5rem;
        border: none;
        box-shadow: 0 0.5rem 1rem rgba(0, 0, 0, 0.15);
    }

    .modal-header.bg-danger {
        border-top-left-radius: 0.5rem;
        border-top-right-radius: 0.5rem;
    }

    #delete-preview-list {
        max-height: 300px;
        overflow-y: auto;
    }

    #delete-preview-list .bg-light {
        border-left: 4px solid #dc3545;
    }

    /* Switch Styles */
    .custom-control-label::before {
        border: 2px solid #dee2e6;
    }

    .custom-control-input:checked~.custom-control-label::before {
        background-color: #007bff;
        border-color: #007bff;
    }

    /* Alert Animations */
    .alert {
        animation: fadeIn 0.3s ease;
    }

    @keyframes fadeIn {
        from {
            opacity: 0;
        }

        to {
            opacity: 1;
        }
    }

    /* Button Hover Effects */
    .btn {
        transition: all 0.2s ease;
    }

    .btn:hover {
        transform: translateY(-1px);
        box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
    }

    .btn-danger:hover {
        background-color: #c82333;
        border-color: #bd2130;
    }

    /* Loading Animation */
    .swal2-loading {
        border-width: 4px;
    }

    /* Responsive */
    @media (max-width: 768px) {
        .position-card {
            min-height: 150px;
            padding: 10px;
        }

        .position-card .card-image,
        .position-card .card-placeholder {
            width: 60px;
            height: 60px;
        }

        .position-card .card-info {
            font-size: 11px;
        }

        .selection-controls {
            flex-direction: column;
            gap: 10px;
        }

        .selection-controls .d-flex {
            flex-direction: column;
            align-items: stretch !important;
        }

        .selection-controls .d-flex>div {
            margin-bottom: 10px;
        }

        .modal-dialog {
            margin: 1rem;
        }
    }

    /* Custom Scrollbar for Preview List */
    #delete-preview-list::-webkit-scrollbar {
        width: 6px;
    }

    #delete-preview-list::-webkit-scrollbar-track {
        background: #f1f1f1;
        border-radius: 3px;
    }

    #delete-preview-list::-webkit-scrollbar-thumb {
        background: #888;
        border-radius: 3px;
    }

    #delete-preview-list::-webkit-scrollbar-thumb:hover {
        background: #555;
    }

    /* Disabled Button Style */
    .btn:disabled {
        opacity: 0.6;
        cursor: not-allowed;
        transform: none !important;
        box-shadow: none !important;
    }

    /* Success Animation */
    @keyframes pulse {
        0% {
            transform: scale(1);
        }

        50% {
            transform: scale(1.05);
        }

        100% {
            transform: scale(1);
        }
    }

    .position-card.success-animation {
        animation: pulse 0.6s ease;
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

<?php
/**
 * Helper Function สำหรับสร้างการ์ดตำแหน่ง (เดิม)
 */
function render_position_card($position, $type, $is_main = false, $slot = null)
{
    $slot_number = $position ? $position->position_order : $slot;
    $has_data = $position && !empty(array_filter($position->data, function ($value) {
        return !empty(trim($value));
    }));

    $card_class = 'position-card';
    if ($has_data) $card_class .= ' filled';
    if ($is_main) $card_class .= ' main-position';

    $position_id = $position ? $position->position_id : 0;

    ob_start();
?>
    <div class="<?= $card_class ?>"
        data-slot="<?= $slot_number ?>"
        data-has-data="<?= $has_data ? 'true' : 'false' ?>"
        data-position-id="<?= $position_id ?>">

        <div class="slot-number"><?= $slot_number ?></div>

        <?php if ($has_data): ?>
            <!-- แสดงข้อมูลที่มี -->
            <?php if (!empty($position->data['image'])): ?>
                <img src="<?= base_url('docs/img/' . $position->data['image']) ?>"
                    class="card-image" alt="รูปภาพ">
            <?php else: ?>
                <div class="card-placeholder">
                    <i class="fas fa-user"></i>
                </div>
            <?php endif; ?>

            <div class="card-info">
                <?php
                // แสดงฟิลด์ที่สำคัญ
                $important_fields = ['name', 'rank', 'position', 'phone', 'email'];
                foreach ($important_fields as $field) {
                    if (!empty($position->data[$field])) {
                        if ($field === 'name') {
                            echo '<strong>' . htmlspecialchars($position->data[$field]) . '</strong>';
                        } else {
                            echo '<div>' . htmlspecialchars($position->data[$field]) . '</div>';
                        }
                    }
                }
                ?>
            </div>
        <?php else: ?>
            <!-- แสดงช่องว่าง -->
            <div class="card-placeholder">
                <i class="fas fa-plus"></i>
            </div>
            <div class="card-info">
                <strong>ว่าง</strong>
                <div class="text-muted">คลิกเพื่อเพิ่มข้อมูล</div>
            </div>
        <?php endif; ?>
    </div>
<?php
    return ob_get_clean();
}
?>