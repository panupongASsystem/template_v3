<!-- Storage Management Panel with Settings for System Admin -->
<div class="row mb-4">
    <div class="col-12">
        <div class="card shadow">
            <div class="card-header" style="background: linear-gradient(135deg, #93c5fd 0%, #bfdbfe 100%); color: #1e293b;">
                <div class="d-flex justify-content-between align-items-center">
                    <h5 class="mb-0">
                        <i class="fas fa-database me-2"></i>
                        จัดการข้อมูลพื้นที่จัดเก็บ
                    </h5>
                    <div class="btn-group" role="group">
                        <button type="button" class="btn btn-light btn-sm border" onclick="updateStorageData()" id="updateBtn">
                            <i class="fas fa-sync-alt me-1"></i>
                            อัปเดตข้อมูล
                        </button>
                        
                       
                        
                        
                        <button type="button" class="btn btn-outline-secondary btn-sm" onclick="exportStorageReport()">
                            <i class="fas fa-download me-1"></i>
                            รายงาน
                        </button>
                    </div>
                </div>
            </div>
            <div class="card-body">
                <!-- Enhanced Status Row with Index Theme Colors -->
                <div class="row mb-4">
                    <div class="col-md-3 col-sm-6 mb-3">
                        <div class="stat-card">
                            <div class="stat-card-header">
                                <div class="stat-icon info">
                                    <i class="fas fa-clock"></i>
                                </div>
                                <div class="stat-change positive">
                                    <i class="fas fa-arrow-up"></i>
                                </div>
                            </div>
                            <div class="stat-info">
                                <div class="stat-label">อัปเดตล่าสุด</div>
                                <div class="stat-value" id="lastUpdateTime">
                                    <?php 
                                    $last_updated = $storage_info['last_updated'] ?? null;
                                    if ($last_updated) {
                                        echo '<span class="text-success">' . date('d/m/Y H:i:s', strtotime($last_updated)) . '</span>';
                                    } else {
                                        echo '<span class="text-warning">ไม่ทราบ</span>';
                                    }
                                    ?>
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <div class="col-md-3 col-sm-6 mb-3">
                        <div class="stat-card">
                            <div class="stat-card-header">
                                <div class="stat-icon success">
                                    <i class="fas fa-server"></i>
                                </div>
                                <div class="stat-change positive">
                                    <i class="fas fa-check"></i>
                                </div>
                            </div>
                            <div class="stat-info">
                                <div class="stat-label">สถานะระบบ</div>
                                <div class="stat-value">
                                    <?php 
                                    $status = $storage_info['status'] ?? 'normal';
                                    $badge_color = $status == 'critical' ? 'danger' : ($status == 'warning' ? 'warning' : 'success');
                                    $status_text = $status == 'critical' ? 'วิกฤต' : ($status == 'warning' ? 'เตือน' : 'ปกติ');
                                    ?>
                                    <span class="status-badge <?= $status ?>">
                                        <i class="fas fa-circle me-1"></i><?= $status_text ?>
                                    </span>
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <div class="col-md-3 col-sm-6 mb-3">
                        <div class="stat-card">
                            <div class="stat-card-header">
                                <div class="stat-icon warning">
                                    <i class="fas fa-chart-line"></i>
                                </div>
                                <div class="stat-change positive">
                                    <i class="fas fa-database"></i>
                                </div>
                            </div>
                            <div class="stat-info">
                                <div class="stat-label">ขนาดรวม</div>
                                <div class="stat-value text-warning" id="totalStorageDisplay">
                                    <?= number_format($storage_info['server_storage'] ?? 100, 1) ?> GB
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <div class="col-md-3 col-sm-6 mb-3">
                        <div class="stat-card">
                            <div class="stat-card-header">
                                <div class="stat-icon primary">
                                    <i class="fas fa-history"></i>
                                </div>
                                <div class="stat-change positive">
                                    <i class="fas fa-chart-bar"></i>
                                </div>
                            </div>
                            <div class="stat-info">
                                <div class="stat-label">ประวัติข้อมูล</div>
                                <div class="stat-value text-primary">
                                    <?php
                                    // นับจำนวนบันทึกใน tbl_storage_history
                                    if ($this->db->table_exists('tbl_storage_history')) {
                                        $history_count = $this->db->count_all_results('tbl_storage_history');
                                        echo number_format($history_count) . ' บันทึก';
                                    } else {
                                        echo 'ไม่มีข้อมูล';
                                    }
                                    ?>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Progress Display with Index Theme -->
                <div class="row mb-4">
                    <div class="col-12">
                        <div class="chart-card">
                            <div class="chart-header">
                                <h3 class="chart-title">การใช้พื้นที่ทั้งหมด</h3>
                                <div class="chart-actions">
                                    <span class="btn-chart active" id="percentageDisplay">
                                        <?= number_format($storage_info['percentage_used'] ?? 0, 2) ?>%
                                    </span>
                                </div>
                            </div>
                            
                            <!-- Enhanced Progress Bar -->
                            <div class="progress mb-4" style="height: 20px; background-color: #f1f5f9;">
                                <div class="progress-bar progress-bar-striped progress-bar-animated" 
                                     id="mainProgressBar"
                                     style="background: linear-gradient(90deg, #93c5fd 0%, #60a5fa 100%); width: <?= $storage_info['percentage_used'] ?? 0 ?>%"
                                     role="progressbar" 
                                     aria-valuenow="<?= $storage_info['percentage_used'] ?? 0 ?>" 
                                     aria-valuemin="0" 
                                     aria-valuemax="100">
                                    <?= number_format($storage_info['percentage_used'] ?? 0, 1) ?>%
                                </div>
                            </div>
                            
                            <!-- Storage Details Grid -->
                            <div class="row g-3">
                                <div class="col-md-4">
                                    <div class="storage-detail-card" style="background: linear-gradient(135deg, #bbf7d0 0%, #d1fae5 100%);">
                                        <div class="storage-detail-icon">
                                            <i class="fas fa-hdd" style="color: #059669;"></i>
                                        </div>
                                        <div class="storage-detail-info">
                                            <div class="detail-value" style="color: #059669;" id="usedSpaceDisplay">
                                                <?= number_format($storage_info['server_current'] ?? 0, 3) ?> GB
                                            </div>
                                            <div class="detail-label" style="color: #065f46;">ใช้งานแล้ว</div>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="storage-detail-card" style="background: linear-gradient(135deg, #bae6fd 0%, #dbeafe 100%);">
                                        <div class="storage-detail-icon">
                                            <i class="fas fa-database" style="color: #0284c7;"></i>
                                        </div>
                                        <div class="storage-detail-info">
                                            <div class="detail-value" style="color: #0284c7;" id="freeSpaceDisplay">
                                                <?= number_format($storage_info['free_space'] ?? 0, 3) ?> GB
                                            </div>
                                            <div class="detail-label" style="color: #0c4a6e;">พื้นที่ว่าง</div>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="storage-detail-card" style="background: linear-gradient(135deg, #fde68a 0%, #fef3c7 100%);">
                                        <div class="storage-detail-icon">
                                            <i class="fas fa-server" style="color: #d97706;"></i>
                                        </div>
                                        <div class="storage-detail-info">
                                            <div class="detail-value" style="color: #d97706;" id="totalSpaceDisplay">
                                                <?= number_format($storage_info['server_storage'] ?? 100, 1) ?> GB
                                            </div>
                                            <div class="detail-label" style="color: #92400e;">ขนาดทั้งหมด</div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
				
				
				<!-- รายละเอียดระบบพื้นที่จัดเก็บ -->
<div class="row mb-4">
    <div class="col-md-6">
        <div class="card" style="border: 1px solid #bae6fd;">
            <div class="card-header" style="background: linear-gradient(135deg, #bae6fd 0%, #dbeafe 100%); color: #0c4a6e;">
                <h6 class="mb-0">
                    <i class="fas fa-server me-2"></i>
                    ข้อมูลเซิร์ฟเวอร์
                </h6>
            </div>
            <div class="card-body">
                <table class="table table-sm table-borderless">
                    <tr>
                        <td><i class="fas fa-hdd text-secondary me-2"></i>พื้นที่ทั้งหมด :</td>
                        <td><strong class="text-warning"><?= number_format($storage_info['server_storage'] ?? 100, 3) ?> GB</strong></td>
                    </tr>
                    <tr>
                        <td><i class="fas fa-chart-pie text-secondary me-2"></i>พื้นที่ใช้งาน (คำนวณจริง):</td>
                        <td><strong class="text-primary"><?= number_format($storage_info['server_current'] ?? 0, 6) ?> GB</strong></td>
                    </tr>
                    <tr>
                        <td><i class="fas fa-database text-secondary me-2"></i>พื้นที่ว่าง (คำนวณ):</td>
                        <td><strong class="text-success"><?= number_format($storage_info['free_space'] ?? 0, 6) ?> GB</strong></td>
                    </tr>
                    <tr>
                        <td><i class="fas fa-percentage text-secondary me-2"></i>เปอร์เซ็นต์การใช้งาน:</td>
                        <td><strong class="text-info"><?= number_format($storage_info['percentage_used'] ?? 0, 4) ?>%</strong></td>
                    </tr>
                </table>
            </div>
        </div>
    </div>
    
    <div class="col-md-6">
        <div class="card" style="border: 1px solid #bbf7d0;">
            <div class="card-header" style="background: linear-gradient(135deg, #bbf7d0 0%, #d1fae5 100%); color: #065f46;">
                <h6 class="mb-0">
                    <i class="fas fa-file me-2"></i>
                    สถิติไฟล์
                </h6>
            </div>
            <div class="card-body">
                <table class="table table-sm table-borderless">
                    <tr>
                        <td><i class="fas fa-files-o text-secondary me-2"></i>ไฟล์ทั้งหมด:</td>
                        <td><strong class="text-primary"><?= number_format($file_stats['total_files'] ?? 0) ?> ไฟล์</strong></td>
                    </tr>
                    <tr>
                        <td><i class="fas fa-image text-secondary me-2"></i>ไฟล์รูปภาพ:</td>
                        <td><strong class="text-success"><?= number_format($file_stats['image_files'] ?? 0) ?> ไฟล์</strong></td>
                    </tr>
                    <tr>
                        <td><i class="fas fa-file-alt text-secondary me-2"></i>ไฟล์เอกสาร:</td>
                        <td><strong class="text-warning"><?= number_format($file_stats['document_files'] ?? 0) ?> ไฟล์</strong></td>
                    </tr>
                    <tr>
                        <td><i class="fas fa-file text-secondary me-2"></i>ไฟล์อื่นๆ:</td>
                        <td><strong class="text-info"><?= number_format($file_stats['other_files'] ?? 0) ?> ไฟล์</strong></td>
                    </tr>
                </table>
            </div>
        </div>
    </div>
</div>

<!-- การตั้งค่าและการทำงานของระบบ - เฉพาะ System Admin -->
<?php if ($this->session->userdata('m_system') === 'system_admin'): ?>
<div class="row mb-4">
    <div class="col-12">
        <div class="card" style="border: 1px solid #fde68a;">
            <div class="card-header" style="background: linear-gradient(135deg, #fde68a 0%, #fef3c7 100%); color: #92400e;">
                <div class="d-flex justify-content-between align-items-center">
                    <h6 class="mb-0">
                        <i class="fas fa-cogs me-2"></i>
                        การตั้งค่าและการทำงานของระบบ (ดูได้เฉพาะ SYSTEM ADMIN)
                    </h6>
					
					
					 <!-- ปุ่มตั้งค่าสำหรับ System Admin เท่านั้น -->
                        <?php if ($this->session->userdata('m_system') === 'system_admin'): ?>
                        <button type="button" class="btn btn-warning btn-sm border" onclick="showStorageSettings()">
                            <i class="fas fa-cogs me-1"></i>
                            ตั้งค่าขนาด
                        </button>
                        <?php endif; ?>
					
					
					
                    <span class="badge bg-warning text-dark">
                        <i class="fas fa-crown me-1"></i>
                        System Admin เท่านั้น
                    </span>
                </div>
            </div>
            <div class="card-body">
                <div class="row">
                    <div class="col-md-6">
                        <h6 class="text-primary">
                            <i class="fas fa-robot me-1"></i>
                            ระบบอัปเดตอัตโนมัติ
                        </h6>
                        <ul class="list-unstyled small text-secondary">
                            <li><i class="fas fa-check text-success me-1"></i> อัปเดตเมื่อเข้าหน้า main (ถ้าข้อมูลเก่าเกิน 30 นาที)</li>
                            <li><i class="fas fa-check text-success me-1"></i> อัปเดตเมื่อเข้าหน้า storage</li>
                            <li><i class="fas fa-check text-success me-1"></i> อัปเดตเมื่อกดปุ่ม "อัปเดตข้อมูล"</li>
                            <li><i class="fas fa-info text-info me-1"></i> ข้อมูลใช้งานมาจากการคำนวณจริง</li>
                            <li><i class="fas fa-exclamation-triangle text-warning me-1"></i> <strong>ขนาดทั้งหมดอ่านจากแอดมินเท่านั้น</strong></li>
                        </ul>
                    </div>
                    <div class="col-md-6">
                        <h6 class="text-info">
                            <i class="fas fa-folder me-1"></i>
                            โฟลเดอร์ที่ตรวจสอบ
                        </h6>
                        <ul class="list-unstyled small text-secondary">
                            <li><i class="fas fa-home text-primary me-1"></i> <code class="bg-light px-1 rounded">httpdocs/</code> - <strong>ทั้งหมด</strong> (รวมทุกไฟล์และโฟลเดอร์)</li>
                            <li class="ms-3"><i class="fas fa-folder text-warning me-1"></i> ├── <code>docs/intranet/</code> - เอกสารและรูปภาพ</li>
                            <li class="ms-3"><i class="fas fa-folder text-info me-1"></i> ├── <code>docs/file/</code> - เอกสารและรูปภาพ</li>
                            <li class="ms-3"><i class="fas fa-folder text-success me-1"></i> ├── <code>docs/temp/</code> - เอกสารและรูปภาพ</li>
                            <li class="ms-3"><i class="fas fa-folder text-secondary me-1"></i> ├── <code>docs/img/</code> - เอกสารและรูปภาพ</li>
                            <li class="ms-3"><i class="fas fa-folder text-danger me-1"></i> └── <code>docs/back_office/</code> - เอกสารและรูปภาพ</li>
                            <li><i class="fas fa-database text-primary me-1"></i> <strong>ฐานข้อมูล</strong> - ขนาดทั้งหมด</li>
                        </ul>
                        <div class="alert alert-info mt-2 p-2">
                            <small>
                                <i class="fas fa-info-circle me-1"></i>
                                <strong>ที่อยู่ code ถ้ามีการเปลี่ยนแปลง:</strong> Reports_model.php  --> public function get_file_statistics()
                            </small>
                        </div>
                    </div>
                </div>
                
                <!-- Admin Tools Section -->
                <div class="row mt-4">
                    <div class="col-12">
                        <div class="alert alert-warning border-warning">
                            <div class="d-flex align-items-start">
                                <i class="fas fa-tools me-3 mt-1 text-warning"></i>
                                <div>
                                    <h6 class="alert-heading mb-2 text-warning">
                                        <i class="fas fa-user-shield me-1"></i>
                                        เครื่องมือสำหรับ System Admin
                                    </h6>
                                    <div class="row">
                                        <div class="col-md-6">
                                            <ul class="small mb-0">
                                                <li><strong>ตั้งค่าขนาดพื้นที่:</strong> แก้ไขขนาดรวมของระบบ</li>
                                                <li><strong>ดูประวัติการเปลี่ยนแปลง:</strong> ติดตามการตั้งค่าทั้งหมด</li>
                                                <li><strong>จัดการการอัปเดต:</strong> ควบคุมการทำงานของระบบ</li>
                                            </ul>
                                        </div>
                                        <div class="col-md-6">
                                            <ul class="small mb-0">
                                                <li><strong>ตรวจสอบสถานะระบบ:</strong> ดูข้อมูลละเอียดทั้งหมด</li>
                                                <li><strong>ส่งออกรายงาน:</strong> ข้อมูลครบถ้วนสำหรับวิเคราะห์</li>
                                                <li><strong>การแจ้งเตือน:</strong> ตั้งค่าเกณฑ์การเตือน</li>
                                            </ul>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<?php endif; ?>

                <!-- Info Alert with Index Theme -->
                
            </div>
        </div>
    </div>
</div>

<!-- Modal ตั้งค่าขนาดพื้นที่จัดเก็บ (System Admin เท่านั้น) -->
<?php if ($this->session->userdata('m_system') === 'system_admin'): ?>
<div class="modal fade" id="storageSettingsModal" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content border-0 shadow">
            <div class="modal-header" style="background: linear-gradient(135deg, #fbbf24 0%, #f59e0b 100%); color: #1f2937;">
                <h5 class="modal-title">
                    <i class="fas fa-cogs me-2"></i>
                    ตั้งค่าขนาดพื้นที่จัดเก็บ (System Admin)
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <!-- Current Settings -->
                <div class="row mb-4">
                    <div class="col-12">
                        <div class="alert" style="background: linear-gradient(135deg, #dbeafe 0%, #bae6fd 100%); border: 1px solid #60a5fa;">
                            <h6 class="text-primary mb-3">
                                <i class="fas fa-info-circle me-2"></i>
                                ข้อมูลปัจจุบัน
                            </h6>
                            <div class="row g-3">
                                <div class="col-md-4">
                                    <div class="text-center p-3 rounded" style="background: rgba(59, 130, 246, 0.1);">
                                        <div class="h4 text-primary mb-1" id="currentTotalSize">-</div>
                                        <small class="text-muted">ขนาดทั้งหมด (GB)</small>
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="text-center p-3 rounded" style="background: rgba(16, 185, 129, 0.1);">
                                        <div class="h4 text-success mb-1" id="currentUsedSize">-</div>
                                        <small class="text-muted">ใช้งานแล้ว (GB)</small>
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="text-center p-3 rounded" style="background: rgba(245, 158, 11, 0.1);">
                                        <div class="h4 text-warning mb-1" id="currentUsagePercent">-</div>
                                        <small class="text-muted">เปอร์เซ็นต์ใช้งาน</small>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Settings Form -->
                <div class="row">
                    <div class="col-12">
                        <form id="storageSettingsForm">
                            <div class="mb-3">
                                <label for="newStorageSize" class="form-label fw-bold">
                                    <i class="fas fa-database me-2 text-primary"></i>
                                    ขนาดพื้นที่จัดเก็บใหม่ (GB)
                                </label>
                                <div class="input-group">
                                    <input type="number" 
                                           class="form-control form-control-lg" 
                                           id="newStorageSize" 
                                           name="new_size"
                                           min="1" 
                                           max="10000" 
                                           step="0.1"
                                           placeholder="เช่น 500.5"
                                           required>
                                    <span class="input-group-text">GB</span>
                                </div>
                                <div class="form-text">
                                    <i class="fas fa-info-circle me-1 text-info"></i>
                                    ระบุขนาดพื้นที่ทั้งหมดที่ต้องการ (ตั้งแต่ 1 GB ถึง 10,000 GB)
                                </div>
                            </div>

                            <!-- Preview -->
                            <div class="mb-3" id="previewSection" style="display: none;">
                                <h6 class="text-success mb-2">
                                    <i class="fas fa-eye me-2"></i>
                                    ตัวอย่างหลังการเปลี่ยนแปลง
                                </h6>
                                <div class="p-3 rounded" style="background: linear-gradient(135deg, #d1fae5 0%, #a7f3d0 100%);">
                                    <div class="row text-center">
                                        <div class="col-4">
                                            <strong class="text-success" id="previewTotal">-</strong>
                                            <br><small>ขนาดใหม่</small>
                                        </div>
                                        <div class="col-4">
                                            <strong class="text-info" id="previewUsed">-</strong>
                                            <br><small>ใช้งาน</small>
                                        </div>
                                        <div class="col-4">
                                            <strong class="text-warning" id="previewPercent">-</strong>
                                            <br><small>เปอร์เซ็นต์</small>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- Warning -->
                            <div class="alert alert-warning">
                                <h6 class="text-warning mb-2">
                                    <i class="fas fa-exclamation-triangle me-2"></i>
                                    ข้อควรระวัง
                                </h6>
                                <ul class="small mb-0">
                                    <li>การเปลี่ยนแปลงขนาดจะมีผลทันที</li>
                                    <li>ระบบจะบันทึกประวัติการเปลี่ยนแปลงทุกครั้ง</li>
                                    <li>หากตั้งค่าขนาดน้อยกว่าพื้นที่ที่ใช้งานจริง อาจทำให้เปอร์เซ็นต์เกิน 100%</li>
                                </ul>
                            </div>
                        </form>
                    </div>
                </div>

                <!-- History -->
                <div class="row mt-4">
                    <div class="col-12">
                        <h6 class="text-secondary mb-3">
                            <i class="fas fa-history me-2"></i>
                            ประวัติการเปลี่ยนแปลง (5 ครั้งล่าสุด)
                        </h6>
                        <div class="table-responsive">
                            <table class="table table-sm">
                                <thead style="background: #f8fafc;">
                                    <tr>
                                        <th>วันที่</th>
                                        <th>ขนาดเดิม</th>
                                        <th>ขนาดใหม่</th>
                                        <th>ผู้แก้ไข</th>
                                    </tr>
                                </thead>
                                <tbody id="settingsHistoryTable">
                                    <tr>
                                        <td colspan="4" class="text-center text-muted">
                                            <i class="fas fa-spinner fa-spin me-2"></i>กำลังโหลด...
                                        </td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-light border" data-bs-dismiss="modal">
                    <i class="fas fa-times me-1"></i>ยกเลิก
                </button>
                <button type="button" class="btn btn-warning" onclick="saveStorageSettings()" id="saveSettingsBtn">
                    <i class="fas fa-save me-1"></i>บันทึกการตั้งค่า
                </button>
            </div>
        </div>
    </div>
</div>
<?php endif; ?>

<!-- Modal ยืนยันการบันทึก (System Admin) -->
<?php if ($this->session->userdata('m_system') === 'system_admin'): ?>
<div class="modal fade" id="confirmSaveModal" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0 shadow">
            <div class="modal-header" style="background: linear-gradient(135deg, #f59e0b 0%, #d97706 100%); color: white;">
                <h5 class="modal-title">
                    <i class="fas fa-exclamation-triangle me-2"></i>
                    ยืนยันการเปลี่ยนแปลง
                </h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <div class="text-center mb-4">
                    <div class="display-1 text-warning mb-3">
                        <i class="fas fa-question-circle"></i>
                    </div>
                    <h5 class="text-dark mb-3">ต้องการเปลี่ยนขนาดพื้นที่จัดเก็บหรือไม่?</h5>
                </div>
                
                <div class="row g-3">
                    <div class="col-6">
                        <div class="card" style="background: linear-gradient(135deg, #fee2e2 0%, #fecaca 100%); border: 1px solid #f87171;">
                            <div class="card-body text-center p-3">
                                <div class="small text-muted mb-1">ขนาดเดิม</div>
                                <div class="h5 text-danger mb-0" id="confirmOldSize">-</div>
                            </div>
                        </div>
                    </div>
                    <div class="col-6">
                        <div class="card" style="background: linear-gradient(135deg, #dcfce7 0%, #bbf7d0 100%); border: 1px solid #22c55e;">
                            <div class="card-body text-center p-3">
                                <div class="small text-muted mb-1">ขนาดใหม่</div>
                                <div class="h5 text-success mb-0" id="confirmNewSize">-</div>
                            </div>
                        </div>
                    </div>
                </div>
                
                <div class="alert alert-warning mt-4 mb-0">
                    <div class="d-flex align-items-start">
                        <i class="fas fa-exclamation-triangle me-3 mt-1 text-warning"></i>
                        <div>
                            <h6 class="alert-heading mb-2">ข้อควรระวัง</h6>
                            <ul class="small mb-0">
                                <li>การเปลี่ยนแปลงจะมีผลทันทีและไม่สามารถยกเลิกได้</li>
                                <li>ระบบจะบันทึกประวัติการเปลี่ยนแปลง</li>
                                <li>อาจส่งผลต่อการคำนวณเปอร์เซ็นต์การใช้งาน</li>
                            </ul>
                        </div>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-light border" data-bs-dismiss="modal">
                    <i class="fas fa-times me-1"></i>ยกเลิก
                </button>
                <button type="button" class="btn btn-warning" onclick="confirmSaveSettings()" id="confirmSaveBtn">
                    <i class="fas fa-check me-1"></i>ยืนยันการบันทึก
                </button>
            </div>
        </div>
    </div>
</div>
<?php endif; ?>






<!-- เพิ่ม Modal แจ้งเตือนพื้นที่จัดเก็บในหน้า storage -->

<!-- Storage Warning Modal -->
<div class="modal fade" id="storageWarningModal" tabindex="-1" data-bs-backdrop="static">
    <div class="modal-dialog modal-lg modal-dialog-centered">
        <div class="modal-content border-0 shadow-lg">
            <div class="modal-header" id="warningModalHeader">
                <h5 class="modal-title fw-bold" id="warningModalTitle">
                    <i class="fas fa-exclamation-triangle me-2"></i>
                    แจ้งเตือนพื้นที่จัดเก็บ
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body p-4">
                <!-- สถานะปัจจุบัน -->
                <div class="alert mb-4" id="warningAlert">
                    <div class="row align-items-center">
                        <div class="col-md-8">
                            <h6 class="mb-2">สถานะพื้นที่จัดเก็บปัจจุบัน</h6>
                            <div class="progress mb-2" style="height: 25px;">
                                <div class="progress-bar progress-bar-striped progress-bar-animated" 
                                     id="warningProgressBar"
                                     role="progressbar">
                                    <span id="warningPercentageText"></span>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-4 text-center">
                            <div class="display-6 fw-bold" id="warningPercentageDisplay"></div>
                            <small class="text-muted">ใช้งานแล้ว</small>
                        </div>
                    </div>
                </div>

                <!-- รายละเอียดการใช้งาน -->
                <div class="row mb-4">
                    <div class="col-md-4">
                        <div class="text-center p-3 rounded bg-light">
                            <div class="h5 text-primary mb-1" id="warningUsedSpace">-</div>
                            <small class="text-muted">ใช้งานแล้ว</small>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="text-center p-3 rounded bg-light">
                            <div class="h5 text-success mb-1" id="warningFreeSpace">-</div>
                            <small class="text-muted">พื้นที่ว่าง</small>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="text-center p-3 rounded bg-light">
                            <div class="h5 text-info mb-1" id="warningTotalSpace">-</div>
                            <small class="text-muted">ขนาดทั้งหมด</small>
                        </div>
                    </div>
                </div>

                <!-- คำเตือนและข้อควรระวัง -->
                <div class="card border-warning mb-4">
                    <div class="card-body">
                        <h6 class="card-title text-warning">
                            <i class="fas fa-exclamation-circle me-1"></i>
                            ข้อควรระวัง
                        </h6>
                        <ul class="mb-0" id="warningList">
                            <!-- จะถูก populate ด้วย JavaScript -->
                        </ul>
                    </div>
                </div>

                <!-- คำแนะนำการดำเนินการ -->
                <div class="card border-info">
                    <div class="card-body">
                        <h6 class="card-title text-info">
                            <i class="fas fa-lightbulb me-1"></i>
                            คำแนะนำการดำเนินการ
                        </h6>
                        <ul class="mb-0" id="actionList">
                            <!-- จะถูก populate ด้วย JavaScript -->
                        </ul>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <div class="form-check me-auto">
                    <input class="form-check-input" type="checkbox" id="dontShowAgainToday">
                    <label class="form-check-label" for="dontShowAgainToday">
                        ไม่แสดงอีกในวันนี้
                    </label>
                </div>
                <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">
                    <i class="fas fa-times me-1"></i>ปิด
                </button>
                <button type="button" class="btn btn-primary" onclick="goToStorageManagement()">
                    <i class="fas fa-cogs me-1"></i>จัดการพื้นที่
                </button>
            </div>
        </div>
    </div>
</div>

<script>
// ระบบแจ้งเตือนพื้นที่จัดเก็บ
document.addEventListener('DOMContentLoaded', function() {
    // ดึงข้อมูลปัจจุบัน
    const storageData = {
        percentage: <?= $storage_info['percentage_used'] ?? 0 ?>,
        used: <?= $storage_info['server_current'] ?? 0 ?>,
        total: <?= $storage_info['server_storage'] ?? 100 ?>,
        free: <?= $storage_info['free_space'] ?? 0 ?>,
        status: '<?= $storage_info['status'] ?? 'normal' ?>'
    };
    
    // ตรวจสอบว่าควรแสดงการแจ้งเตือนหรือไม่
    checkStorageWarning(storageData);
});

function checkStorageWarning(data) {
    const percentage = parseFloat(data.percentage);
    
    // ตรวจสอบว่าเคยแสดงในวันนี้แล้วหรือยัง
    const today = new Date().toDateString();
    const warningKey = `storage_warning_${today}`;
    
    if (localStorage.getItem(warningKey)) {
        return; // ไม่แสดงซ้ำในวันเดียวกัน
    }
    
    // กำหนดเกณฑ์การแจ้งเตือน
    let shouldWarn = false;
    let warningType = 'normal';
    
    if (percentage >= 95) {
        shouldWarn = true;
        warningType = 'critical';
    } else if (percentage >= 90) {
        shouldWarn = true;
        warningType = 'danger';
    } else if (percentage >= 80) {
        shouldWarn = true;
        warningType = 'warning';
    }
    
    if (shouldWarn) {
        showStorageWarning(data, warningType);
    }
}

function showStorageWarning(data, type) {
    const modal = document.getElementById('storageWarningModal');
    const header = document.getElementById('warningModalHeader');
    const title = document.getElementById('warningModalTitle');
    const alert = document.getElementById('warningAlert');
    const progressBar = document.getElementById('warningProgressBar');
    const percentageText = document.getElementById('warningPercentageText');
    const percentageDisplay = document.getElementById('warningPercentageDisplay');
    const usedSpace = document.getElementById('warningUsedSpace');
    const freeSpace = document.getElementById('warningFreeSpace');
    const totalSpace = document.getElementById('warningTotalSpace');
    const warningList = document.getElementById('warningList');
    const actionList = document.getElementById('actionList');
    
    // กำหนดสีและข้อความตามระดับความรุนแรง
    let headerClass, alertClass, progressClass, icon, titleText;
    let warnings, actions;
    
    switch (type) {
        case 'critical':
            headerClass = 'bg-danger text-white';
            alertClass = 'alert-danger';
            progressClass = 'bg-danger';
            icon = 'fas fa-ban';
            titleText = '🚨 พื้นที่จัดเก็บเต็มเกือบหมด!';
            warnings = [
                'พื้นที่จัดเก็บใกล้เต็ม (' + data.percentage.toFixed(1) + '%)',
                'ระบบอาจหยุดทำงานได้หากพื้นที่เต็ม 100%',
                'ไม่สามารถอัปโหลดไฟล์ใหม่ได้',
                'ติดต่อ Sales ที่ดูแลท่านทันทีเพื่อชื้อพื้นที่เพิ่มเติม'
            ];
            actions = [
                'ลบไฟล์ที่ไม่จำเป็นทันที',
                'ย้ายไฟล์เก่าออกจากระบบ',
                'บีบอัดไฟล์ขนาดใหญ่',
                'ติดต่อ Sales เพื่อขยายพื้นที่'
            ];
            break;
            
        case 'danger':
            headerClass = 'bg-warning text-dark';
            alertClass = 'alert-warning';
            progressClass = 'bg-warning';
            icon = 'fas fa-exclamation-triangle';
            titleText = '⚠️ พื้นที่จัดเก็บใกล้เต็ม!';
            warnings = [
                'พื้นที่จัดเก็บมีการใช้งาน ' + data.percentage.toFixed(1) + '%',
                'เหลือพื้นที่ว่างเพียง ' + data.free.toFixed(2) + ' GB',
                
                'ติดต่อ Sales ที่ดูแลท่านทันทีเพื่อชื้อพื้นที่เพิ่มเติม'
            ];
            actions = [
                'ตรวจสอบและลบไฟล์ที่ไม่จำเป็น',
                'ย้ายไฟล์เก่าไปยังที่เก็บข้อมูลอื่น',
                'บีบอัดไฟล์ขนาดใหญ่',
                'วางแผนการจัดการข้อมูลระยะยาว'
            ];
            break;
            
        case 'warning':
            headerClass = 'bg-info text-white';
            alertClass = 'alert-info';
            progressClass = 'bg-info';
            icon = 'fas fa-info-circle';
            titleText = '📊 พื้นที่จัดเก็บมีการใช้งานสูง';
            warnings = [
                'พื้นที่จัดเก็บมีการใช้งาน ' + data.percentage.toFixed(1) + '%',
                'ควรเริ่มติดตามและวางแผนการจัดการ',
                'ติดต่อ Sales ที่ดูแลท่านเพื่อชื้อพื้นที่เพิ่มเติม',
                'ระบบยังทำงานปกติ'
            ];
            actions = [
                'ตรวจสอบไฟล์ขนาดใหญ่และไฟล์ที่ไม่จำเป็น',
                'วางแผนการจัดการข้อมูลระยะยาว',
                'กำหนดตารางการทำความสะอาดข้อมูล',
                'ติดตามการเติบโตของข้อมูลอย่างสม่ำเสมอ'
            ];
            break;
    }
    
    // อัปเดต UI
    header.className = `modal-header ${headerClass}`;
    title.innerHTML = `<i class="${icon} me-2"></i>${titleText}`;
    alert.className = `alert ${alertClass}`;
    
    progressBar.className = `progress-bar progress-bar-striped progress-bar-animated ${progressClass}`;
    progressBar.style.width = data.percentage + '%';
    progressBar.setAttribute('aria-valuenow', data.percentage);
    
    percentageText.textContent = data.percentage.toFixed(1) + '%';
    percentageDisplay.textContent = data.percentage.toFixed(1) + '%';
    
    usedSpace.textContent = data.used.toFixed(2) + ' GB';
    freeSpace.textContent = data.free.toFixed(2) + ' GB';
    totalSpace.textContent = data.total.toFixed(1) + ' GB';
    
    // สร้างรายการคำเตือน
    warningList.innerHTML = '';
    warnings.forEach(warning => {
        const li = document.createElement('li');
        li.innerHTML = `<i class="fas fa-exclamation-circle text-warning me-2"></i>${warning}`;
        warningList.appendChild(li);
    });
    
    // สร้างรายการคำแนะนำ
    actionList.innerHTML = '';
    actions.forEach(action => {
        const li = document.createElement('li');
        li.innerHTML = `<i class="fas fa-check-circle text-success me-2"></i>${action}`;
        actionList.appendChild(li);
    });
    
    // แสดง Modal
    const bootstrapModal = new bootstrap.Modal(modal);
    bootstrapModal.show();
    
    // จัดการการปิด Modal
    modal.addEventListener('hidden.bs.modal', function() {
        const dontShowAgain = document.getElementById('dontShowAgainToday').checked;
        if (dontShowAgain) {
            const today = new Date().toDateString();
            const warningKey = `storage_warning_${today}`;
            localStorage.setItem(warningKey, 'hidden');
        }
    });
}

function goToStorageManagement() {
    // ปิด Modal
    const modal = bootstrap.Modal.getInstance(document.getElementById('storageWarningModal'));
    modal.hide();
    
    // เลื่อนไปที่ส่วนการจัดการ (ถ้ามี)
    const managementSection = document.querySelector('.storage-management, .card-header');
    if (managementSection) {
        managementSection.scrollIntoView({ behavior: 'smooth', block: 'start' });
        
        // เพิ่มเอฟเฟกต์ highlight
        managementSection.classList.add('highlight-section');
        setTimeout(() => {
            managementSection.classList.remove('highlight-section');
        }, 3000);
    }
    
    // หรือแสดง toast แจ้งให้ผู้ใช้ทราบ
    showToast('สามารถจัดการพื้นที่จัดเก็บได้ในส่วนการตั้งค่าด้านบน', 'info');
}

// ฟังก์ชันทดสอบ Modal (สำหรับ testing)
function testStorageWarning(percentage = 91) {
    const testData = {
        percentage: percentage,
        used: 45.5,
        total: 50.0,
        free: 4.5,
        status: percentage >= 90 ? 'critical' : 'warning'
    };
    
    let type = 'warning';
    if (percentage >= 95) type = 'critical';
    else if (percentage >= 90) type = 'danger';
    
    showStorageWarning(testData, type);
}

// เพิ่ม CSS สำหรับ highlight effect
const style = document.createElement('style');
style.textContent = `
.highlight-section {
    animation: highlightPulse 3s ease-in-out;
    transition: all 0.3s ease;
}

@keyframes highlightPulse {
    0%, 100% { 
        background-color: transparent; 
        transform: scale(1);
    }
    50% { 
        background-color: rgba(255, 193, 7, 0.2); 
        transform: scale(1.02);
    }
}

.progress-bar-animated {
    animation: progress-bar-stripes 1s linear infinite;
}

@keyframes progress-bar-stripes {
    0% { background-position: 1rem 0; }
    100% { background-position: 0 0; }
}
`;
document.head.appendChild(style);

// ลบการแจ้งเตือนเก่าที่หมดอายุ (ทำความสะอาด localStorage)
function cleanupOldWarnings() {
    const today = new Date().toDateString();
    for (let i = 0; i < localStorage.length; i++) {
        const key = localStorage.key(i);
        if (key && key.startsWith('storage_warning_') && !key.includes(today)) {
            localStorage.removeItem(key);
        }
    }
}

// ทำความสะอาดเมื่อโหลดหน้า
cleanupOldWarnings();
</script>

<!-- ปุ่มทดสอบสำหรับ System Admin เท่านั้น -->
<?php if ($this->session->userdata('m_system') === 'system_admin'): ?>
<div class="position-fixed bottom-0 end-0 p-3" style="z-index: 1050;">
    <div class="card shadow-lg border-0" style="width: 280px;">
        <div class="card-header bg-dark text-white py-2">
            <h6 class="mb-0">
                <i class="fas fa-user-shield me-2"></i>
                System Admin Tools
            </h6>
        </div>
        <div class="card-body p-3">
            <div class="mb-3">
                <label class="form-label small fw-bold">ทดสอบ Storage Warning:</label>
                <div class="btn-group-vertical w-100" role="group">
                    <button type="button" class="btn btn-outline-info btn-sm mb-1" onclick="testStorageWarning(85)">
                        <i class="fas fa-info-circle me-1"></i>
                        ปกติ (85%)
                    </button>
                    <button type="button" class="btn btn-outline-warning btn-sm mb-1" onclick="testStorageWarning(91)">
                        <i class="fas fa-exclamation-triangle me-1"></i>
                        อันตราย (91%)
                    </button>
                    <button type="button" class="btn btn-outline-danger btn-sm mb-1" onclick="testStorageWarning(96)">
                        <i class="fas fa-ban me-1"></i>
                        วิกฤติ (96%)
                    </button>
                </div>
            </div>
            
            <div class="mb-3">
                <label class="form-label small fw-bold">การจัดการ:</label>
                <div class="btn-group-vertical w-100" role="group">
                    <button type="button" class="btn btn-outline-secondary btn-sm mb-1" onclick="resetStorageWarnings()">
                        <i class="fas fa-undo me-1"></i>
                        รีเซ็ตการแจ้งเตือน
                    </button>
                    <button type="button" class="btn btn-outline-primary btn-sm mb-1" onclick="showWarningStatus()">
                        <i class="fas fa-eye me-1"></i>
                        ดูสถานะการแจ้งเตือน
                    </button>
                </div>
            </div>
            
            <div class="mb-2">
                <label class="form-label small fw-bold">ทดสอบ Custom %:</label>
                <div class="input-group input-group-sm">
                    <input type="number" class="form-control" id="customPercentage" 
                           placeholder="0-100" min="0" max="100" value="85">
                    <button class="btn btn-outline-success" type="button" onclick="testCustomPercentage()">
                        <i class="fas fa-play"></i>
                    </button>
                </div>
            </div>
            
            <button type="button" class="btn btn-outline-secondary btn-sm w-100" onclick="toggleAdminTools()" id="toggleBtn">
                <i class="fas fa-minus me-1"></i>
                ย่อ
            </button>
        </div>
    </div>
</div>

<script>
// ฟังก์ชันเพิ่มเติมสำหรับ System Admin
function resetStorageWarnings() {
    if (confirm('ต้องการรีเซ็ตการแจ้งเตือนทั้งหมดหรือไม่?')) {
        // ลบเฉพาะ storage warnings
        for (let i = localStorage.length - 1; i >= 0; i--) {
            const key = localStorage.key(i);
            if (key && key.startsWith('storage_warning_')) {
                localStorage.removeItem(key);
            }
        }
        showToast('รีเซ็ตการแจ้งเตือนเรียบร้อยแล้ว', 'success');
        
        // รีเฟรชหน้าหลังจาก 1 วินาที
        setTimeout(() => {
            location.reload();
        }, 1000);
    }
}

function showWarningStatus() {
    const today = new Date().toDateString();
    const warningKey = `storage_warning_${today}`;
    const hasWarning = localStorage.getItem(warningKey);
    
    let statusMsg = hasWarning ? 
        '🔕 การแจ้งเตือนถูกปิดสำหรับวันนี้' : 
        '🔔 การแจ้งเตือนเปิดอยู่';
    
    // นับจำนวน warnings ที่เก็บไว้
    let warningCount = 0;
    for (let i = 0; i < localStorage.length; i++) {
        const key = localStorage.key(i);
        if (key && key.startsWith('storage_warning_')) {
            warningCount++;
        }
    }
    
    statusMsg += `\n📊 มีการแจ้งเตือนที่บันทึกไว้: ${warningCount} รายการ`;
    
    alert(statusMsg);
}

function testCustomPercentage() {
    const percentage = parseFloat(document.getElementById('customPercentage').value);
    
    if (isNaN(percentage) || percentage < 0 || percentage > 100) {
        showToast('กรุณาใส่ค่าเปอร์เซ็นต์ระหว่าง 0-100', 'error');
        return;
    }
    
    // ลบการแจ้งเตือนชั่วคราวเพื่อให้สามารถแสดงได้
    const today = new Date().toDateString();
    const warningKey = `storage_warning_${today}`;
    localStorage.removeItem(warningKey);
    
    testStorageWarning(percentage);
    showToast(`ทดสอบการแจ้งเตือนที่ ${percentage}%`, 'info');
}

function toggleAdminTools() {
    const card = document.querySelector('.position-fixed .card');
    const cardBody = card.querySelector('.card-body');
    const toggleBtn = document.getElementById('toggleBtn');
    
    if (cardBody.style.display === 'none') {
        // แสดง
        cardBody.style.display = 'block';
        toggleBtn.innerHTML = '<i class="fas fa-minus me-1"></i>ย่อ';
        card.style.width = '280px';
    } else {
        // ซ่อน
        cardBody.style.display = 'none';
        toggleBtn.innerHTML = '<i class="fas fa-plus me-1"></i>ขยาย';
        card.style.width = 'auto';
    }
}

// เพิ่มสถานะในการทดสอบ
function testStorageWarning(percentage = 91) {
    // ลบการแจ้งเตือนชั่วคราวเพื่อให้สามารถแสดงได้
    const today = new Date().toDateString();
    const warningKey = `storage_warning_${today}`;
    localStorage.removeItem(warningKey);
    
    const testData = {
        percentage: percentage,
        used: (percentage / 100) * 50,  // คำนวณจาก 50GB total
        total: 50.0,
        free: 50 - ((percentage / 100) * 50),
        status: percentage >= 90 ? 'critical' : 'warning'
    };
    
    let type = 'warning';
    if (percentage >= 95) type = 'critical';
    else if (percentage >= 90) type = 'danger';
    
    console.log(`[Admin Test] Testing storage warning at ${percentage}% (Type: ${type})`);
    showStorageWarning(testData, type);
}

// เพิ่ม keyboard shortcut สำหรับ System Admin
document.addEventListener('keydown', function(e) {
    // Ctrl + Shift + T = ทดสอบการแจ้งเตือน
    if (e.ctrlKey && e.shiftKey && e.key === 'T') {
        e.preventDefault();
        testStorageWarning(91);
        showToast('ทดสอบการแจ้งเตือนด้วย Keyboard Shortcut', 'info');
    }
    
    // Ctrl + Shift + R = รีเซ็ตการแจ้งเตือน
    if (e.ctrlKey && e.shiftKey && e.key === 'R') {
        e.preventDefault();
        resetStorageWarnings();
    }
});

// แสดงข้อมูล debug ใน console สำหรับ System Admin
console.log('%c🛠️ System Admin Tools Loaded', 'color: #28a745; font-weight: bold; font-size: 14px;');
console.log('💾 Storage Data:', {
    percentage: <?= $storage_info['percentage_used'] ?? 0 ?>,
    used: <?= $storage_info['server_current'] ?? 0 ?>,
    total: <?= $storage_info['server_storage'] ?? 100 ?>,
    status: '<?= $storage_info['status'] ?? 'normal' ?>'
});
console.log('⌨️ Keyboard Shortcuts:');
console.log('  Ctrl + Shift + T = ทดสอบการแจ้งเตือน');
console.log('  Ctrl + Shift + R = รีเซ็ตการแจ้งเตือน');
</script>

<style>
	
	
	
	body {
    padding-top: 50px !important;
}
	
/* Styles สำหรับ Admin Tools */
.position-fixed .card {
    transition: all 0.3s ease;
    border-radius: 12px !important;
}

.position-fixed .card:hover {
    transform: translateY(-2px);
    box-shadow: 0 8px 25px rgba(0,0,0,0.15) !important;
}

.position-fixed .btn-group-vertical .btn {
    font-size: 0.75rem;
    padding: 0.25rem 0.5rem;
}

.position-fixed .input-group-sm .form-control,
.position-fixed .input-group-sm .btn {
    font-size: 0.75rem;
}

/* เพิ่ม animation สำหรับปุ่ม */
.position-fixed .btn {
    transition: all 0.2s ease;
}

.position-fixed .btn:hover {
    transform: translateY(-1px);
}

.position-fixed .btn:active {
    transform: translateY(0);
}

/* สำหรับ responsive */
@media (max-width: 768px) {
    .position-fixed .card {
        width: 250px !important;
        font-size: 0.85rem;
    }
}
</style>
<?php endif; ?>



<!-- Enhanced JavaScript with Storage Settings -->
<script>
// Current storage data (global)
let currentStorageData = {
    total: <?= $storage_info['server_storage'] ?? 100 ?>,
    used: <?= $storage_info['server_current'] ?? 0 ?>,
    percentage: <?= $storage_info['percentage_used'] ?? 0 ?>
};

// Global variable สำหรับเก็บขนาดที่จะบันทึก
let pendingSaveSize = 0;

// ฟังก์ชันอัปเดตข้อมูลพื้นที่จัดเก็บ
function updateStorageData() {
    const updateBtn = document.getElementById('updateBtn');
    const originalHtml = updateBtn.innerHTML;
    
    // แสดง loading state
    updateBtn.disabled = true;
    updateBtn.innerHTML = '<i class="fas fa-spinner fa-spin me-1"></i>กำลังอัปเดต...';
    updateBtn.classList.add('btn-warning');
    updateBtn.classList.remove('btn-light');
    
    // แสดง toast notification
    showToast('เริ่มอัปเดตข้อมูลพื้นที่จัดเก็บ...', 'info');
    
    // เรียก API อัปเดต (ใช้ URL เดิมสำหรับการอัปเดตข้อมูลการใช้งาน)
    fetch('<?= site_url("System_reports/api_update_storage") ?>', {
        method: 'POST',
        headers: {
            'X-Requested-With': 'XMLHttpRequest',
            'Content-Type': 'application/json'
        }
    })
    .then(response => {
        if (!response.ok) {
            throw new Error(`HTTP error! status: ${response.status}`);
        }
        return response.json();
    })
    .then(data => {
        if (data.success) {
            // อัปเดต UI elements
            updateStorageUI(data);
            
            // แสดงผลสำเร็จ
            showToast('อัปเดตข้อมูลสำเร็จ!', 'success');
            updateBtn.classList.add('btn-success');
            updateBtn.classList.remove('btn-warning');
            
            // รีเฟรชหน้าหลังจาก 2 วินาที
            setTimeout(() => {
                location.reload();
            }, 2000);
            
        } else {
            throw new Error(data.message || 'เกิดข้อผิดพลาดในการอัปเดตข้อมูล');
        }
    })
    .catch(error => {
        console.error('Update Storage Error:', error);
        showToast(error.message, 'error');
        updateBtn.classList.add('btn-danger');
        updateBtn.classList.remove('btn-warning');
    })
    .finally(() => {
        // คืนสถานะปุ่มหลังจาก 3 วินาที
        setTimeout(() => {
            updateBtn.disabled = false;
            updateBtn.innerHTML = originalHtml;
            updateBtn.className = 'btn btn-light btn-sm border';
        }, 3000);
    });
}

// อัปเดต UI หลังจากได้ข้อมูลใหม่
function updateStorageUI(data) {
    // อัปเดตเวลา
    const timeElement = document.getElementById('lastUpdateTime');
    if (timeElement && data.updated_at) {
        const updateTime = new Date(data.updated_at);
        timeElement.innerHTML = `<span class="text-success">${updateTime.toLocaleString('th-TH')}</span>`;
    }
    
    // อัปเดต progress bar และข้อมูล
    if (data.total_space && data.used_space) {
        const percentage = (data.used_space / data.total_space) * 100;
        
        // อัปเดต global data
        currentStorageData = {
            total: data.total_space,
            used: data.used_space,
            percentage: percentage
        };
        
        // Progress bar
        const progressBar = document.getElementById('mainProgressBar');
        if (progressBar) {
            progressBar.style.width = percentage.toFixed(2) + '%';
            progressBar.setAttribute('aria-valuenow', percentage.toFixed(2));
            progressBar.textContent = percentage.toFixed(1) + '%';
        }
        
        // แสดงเปอร์เซ็นต์
        const percentDisplay = document.getElementById('percentageDisplay');
        if (percentDisplay) {
            percentDisplay.textContent = percentage.toFixed(2) + '%';
        }
        
        // อัปเดตข้อมูลในการ์ด
        const usedDisplay = document.getElementById('usedSpaceDisplay');
        const freeDisplay = document.getElementById('freeSpaceDisplay');
        const totalDisplay = document.getElementById('totalSpaceDisplay');
        const totalStorageDisplay = document.getElementById('totalStorageDisplay');
        
        if (usedDisplay) usedDisplay.textContent = data.used_space.toFixed(3) + ' GB';
        if (freeDisplay) freeDisplay.textContent = (data.total_space - data.used_space).toFixed(3) + ' GB';
        if (totalDisplay) totalDisplay.textContent = data.total_space.toFixed(1) + ' GB';
        if (totalStorageDisplay) totalStorageDisplay.textContent = data.total_space.toFixed(1) + ' GB';
    }
}

<?php if ($this->session->userdata('m_system') === 'system_admin'): ?>
// ฟังก์ชันแสดง Modal ตั้งค่า (System Admin เท่านั้น)
function showStorageSettings() {
    // ดึงข้อมูลปัจจุบัน
    loadCurrentStorageSettings();
    
    // แสดง modal
    const modal = new bootstrap.Modal(document.getElementById('storageSettingsModal'));
    modal.show();
    
    // โหลดประวัติ
    loadSettingsHistory();
    
    // ตั้งค่า event listeners
    setupSettingsEventListeners();
}

// โหลดข้อมูลการตั้งค่าปัจจุบัน
function loadCurrentStorageSettings() {
    fetch('<?= site_url("System_reports/api_current_storage_settings") ?>', {
        method: 'GET',
        headers: { 'X-Requested-With': 'XMLHttpRequest' }
    })
    .then(response => {
        console.log('Current settings response status:', response.status);
        if (!response.ok) {
            throw new Error(`HTTP ${response.status}: ${response.statusText}`);
        }
        return response.json();
    })
    .then(data => {
        console.log('Current settings data:', data);
        if (data.success) {
            const settings = data.settings;
            
            // แสดงข้อมูลปัจจุบัน
            document.getElementById('currentTotalSize').textContent = parseFloat(settings.total_space).toFixed(1) + ' GB';
            document.getElementById('currentUsedSize').textContent = parseFloat(settings.current_usage).toFixed(3) + ' GB';
            document.getElementById('currentUsagePercent').textContent = 
                ((settings.current_usage / settings.total_space) * 100).toFixed(2) + '%';
            
            // ตั้งค่าเริ่มต้นในฟอร์ม
            document.getElementById('newStorageSize').value = settings.total_space;
            
            // อัปเดต global data
            currentStorageData = {
                total: parseFloat(settings.total_space),
                used: parseFloat(settings.current_usage),
                percentage: (settings.current_usage / settings.total_space) * 100
            };
        } else {
            showToast('ไม่สามารถโหลดข้อมูลการตั้งค่าได้: ' + data.message, 'error');
        }
    })
    .catch(error => {
        console.error('Load settings error:', error);
        showToast('เกิดข้อผิดพลาดในการโหลดข้อมูล: ' + error.message, 'error');
    });
}

// โหลดประวัติการตั้งค่า
function loadSettingsHistory() {
    fetch('<?= site_url("System_reports/api_storage_settings_history") ?>', {
        method: 'GET',
        headers: { 'X-Requested-With': 'XMLHttpRequest' }
    })
    .then(response => {
        console.log('History response status:', response.status);
        if (!response.ok) {
            throw new Error(`HTTP ${response.status}: ${response.statusText}`);
        }
        return response.json();
    })
    .then(data => {
        console.log('History data:', data);
        const tableBody = document.getElementById('settingsHistoryTable');
        
        if (data.success && data.history && data.history.length > 0) {
            tableBody.innerHTML = '';
            data.history.forEach(item => {
                const row = document.createElement('tr');
                row.innerHTML = `
                    <td>${new Date(item.updated_at).toLocaleString('th-TH')}</td>
                    <td><span class="badge bg-secondary">${parseFloat(item.old_size).toFixed(1)} GB</span></td>
                    <td><span class="badge bg-primary">${parseFloat(item.new_size).toFixed(1)} GB</span></td>
                    <td>${item.updated_by}</td>
                `;
                tableBody.appendChild(row);
            });
        } else {
            tableBody.innerHTML = '<tr><td colspan="4" class="text-center text-muted">ไม่มีประวัติ</td></tr>';
        }
    })
    .catch(error => {
        console.error('Load history error:', error);
        document.getElementById('settingsHistoryTable').innerHTML = 
            '<tr><td colspan="4" class="text-center text-muted">เกิดข้อผิดพลาดในการโหลดประวัติ</td></tr>';
    });
}

// ตั้งค่า Event Listeners สำหรับฟอร์ม
function setupSettingsEventListeners() {
    const newSizeInput = document.getElementById('newStorageSize');
    const previewSection = document.getElementById('previewSection');
    
    newSizeInput.addEventListener('input', function() {
        const newSize = parseFloat(this.value);
        
        if (newSize && newSize > 0) {
            // แสดง preview
            const newPercentage = (currentStorageData.used / newSize) * 100;
            
            document.getElementById('previewTotal').textContent = newSize.toFixed(1) + ' GB';
            document.getElementById('previewUsed').textContent = currentStorageData.used.toFixed(3) + ' GB';
            document.getElementById('previewPercent').textContent = newPercentage.toFixed(2) + '%';
            
            previewSection.style.display = 'block';
            
            // เปลี่ยนสีตามสถานะ
            const percentElement = document.getElementById('previewPercent');
            if (newPercentage >= 90) {
                percentElement.className = 'text-danger';
            } else if (newPercentage >= 70) {
                percentElement.className = 'text-warning';
            } else {
                percentElement.className = 'text-success';
            }
        } else {
            previewSection.style.display = 'none';
        }
    });
}

// ฟังก์ชันบันทึกการตั้งค่า (แก้ไขให้มี Modal ยืนยัน)
function saveStorageSettings() {
    const newSize = parseFloat(document.getElementById('newStorageSize').value);
    
    // ตรวจสอบข้อมูล
    if (!newSize || newSize <= 0 || newSize > 10000) {
        showToast('กรุณาระบุขนาดพื้นที่ที่ถูกต้อง (1-10,000 GB)', 'error');
        return;
    }
    
    // เก็บค่าที่จะบันทึก
    pendingSaveSize = newSize;
    
    // แสดงข้อมูลใน Modal ยืนยัน
    document.getElementById('confirmOldSize').textContent = currentStorageData.total.toFixed(1) + ' GB';
    document.getElementById('confirmNewSize').textContent = newSize.toFixed(1) + ' GB';
    
    // ซ่อน Modal ตั้งค่าและแสดง Modal ยืนยัน
    bootstrap.Modal.getInstance(document.getElementById('storageSettingsModal')).hide();
    
    setTimeout(() => {
        const confirmModal = new bootstrap.Modal(document.getElementById('confirmSaveModal'));
        confirmModal.show();
    }, 300);
}

// ฟังก์ชันยืนยันการบันทึก
function confirmSaveSettings() {
    const confirmBtn = document.getElementById('confirmSaveBtn');
    const originalHtml = confirmBtn.innerHTML;
    
    // แสดง loading
    confirmBtn.disabled = true;
    confirmBtn.innerHTML = '<i class="fas fa-spinner fa-spin me-1"></i>กำลังบันทึก...';
    
    showToast('กำลังบันทึกการตั้งค่า...', 'info');
    
    // Debug: แสดงข้อมูลที่จะส่ง
    console.log('Sending data:', { new_size: pendingSaveSize });
    
    // ส่งข้อมูล (แก้ไข URL ให้ถูกต้อง)
    fetch('<?= site_url("System_reports/api_admin_update_storage_size") ?>', {
        method: 'POST',
        headers: {
            'X-Requested-With': 'XMLHttpRequest',
            'Content-Type': 'application/json'
        },
        body: JSON.stringify({
            new_size: pendingSaveSize
        })
    })
    .then(response => {
        console.log('Response status:', response.status);
        console.log('Response headers:', response.headers);
        
        if (!response.ok) {
            throw new Error(`HTTP ${response.status}: ${response.statusText}`);
        }
        
        const contentType = response.headers.get('content-type');
        if (!contentType || !contentType.includes('application/json')) {
            throw new Error('Response is not JSON. Content-Type: ' + contentType);
        }
        
        return response.json();
    })
    .then(data => {
        console.log('Response data:', data);
        
        if (data.success) {
            showToast('อัปเดตขนาดพื้นที่จัดเก็บสำเร็จ!', 'success');
            
            // อัปเดตข้อมูลในหน้า
            currentStorageData.total = data.new_size;
            updateStorageDisplays();
            
            // ปิด modal ยืนยัน
            bootstrap.Modal.getInstance(document.getElementById('confirmSaveModal')).hide();
            
            // รีเฟรชหน้าหลังจาก 2 วินาที
            setTimeout(() => {
                location.reload();
            }, 2000);
            
        } else {
            throw new Error(data.message || 'เกิดข้อผิดพลาดในการบันทึก');
        }
    })
    .catch(error => {
        console.error('Save settings error:', error);
        showToast('เกิดข้อผิดพลาด: ' + error.message, 'error');
        
        // กลับไปที่ Modal ตั้งค่า
        bootstrap.Modal.getInstance(document.getElementById('confirmSaveModal')).hide();
        setTimeout(() => {
            const settingsModal = new bootstrap.Modal(document.getElementById('storageSettingsModal'));
            settingsModal.show();
        }, 300);
    })
    .finally(() => {
        // คืนสถานะปุ่ม
        setTimeout(() => {
            confirmBtn.disabled = false;
            confirmBtn.innerHTML = originalHtml;
        }, 2000);
    });
}

// อัปเดตการแสดงผลข้อมูล
function updateStorageDisplays() {
    const percentage = (currentStorageData.used / currentStorageData.total) * 100;
    
    // อัปเดตแสดงผลต่างๆ
    document.getElementById('totalStorageDisplay').textContent = currentStorageData.total.toFixed(1) + ' GB';
    document.getElementById('totalSpaceDisplay').textContent = currentStorageData.total.toFixed(1) + ' GB';
    document.getElementById('freeSpaceDisplay').textContent = (currentStorageData.total - currentStorageData.used).toFixed(3) + ' GB';
    document.getElementById('percentageDisplay').textContent = percentage.toFixed(2) + '%';
    
    // Progress bar
    const progressBar = document.getElementById('mainProgressBar');
    if (progressBar) {
        progressBar.style.width = percentage.toFixed(2) + '%';
        progressBar.setAttribute('aria-valuenow', percentage.toFixed(2));
        progressBar.textContent = percentage.toFixed(1) + '%';
    }
}

// เพิ่ม Event Listener สำหรับ Modal ยืนยัน
document.addEventListener('DOMContentLoaded', function() {
    // เมื่อปิด Modal ยืนยันโดยไม่บันทึก ให้กลับไปที่ Modal ตั้งค่า
    const confirmModal = document.getElementById('confirmSaveModal');
    if (confirmModal) {
        confirmModal.addEventListener('hidden.bs.modal', function(event) {
            // ถ้าปิดโดยไม่ได้บันทึก (ยังมีค่า pendingSaveSize)
            if (pendingSaveSize > 0 && !event.target.querySelector('#confirmSaveBtn').disabled) {
                setTimeout(() => {
                    const settingsModal = new bootstrap.Modal(document.getElementById('storageSettingsModal'));
                    settingsModal.show();
                }, 200);
            }
            pendingSaveSize = 0; // รีเซ็ตค่า
        });
    }
});
<?php endif; ?>

// แสดงข้อมูลรายละเอียดระบบ


// ส่งออกรายงาน Storage
function exportStorageReport() {
    showToast('กำลังเตรียมรายงานพื้นที่จัดเก็บ...', 'info');
    
    // เปิดหน้า preview ในหน้าต่างใหม่
    const previewUrl = '<?= site_url("System_reports/export_excel/storage") ?>';
    const previewWindow = window.open(previewUrl, '_blank', 'width=1200,height=800,scrollbars=yes,resizable=yes');
    
    if (previewWindow) {
        previewWindow.focus();
        showToast('เปิดหน้าตัวอย่างรายงานแล้ว', 'success');
    } else {
        showToast('ไม่สามารถเปิดหน้าตัวอย่างได้ กรุณาอนุญาต Pop-up', 'warning');
        // Fallback: เปิดในแท็บเดียวกัน
        window.location.href = previewUrl;
    }
}
	
	
	// ✅ เพิ่มฟังก์ชันทั่วไปสำหรับส่งออกรายงาน
function openReportPreview(reportType, params = {}) {
    showToast(`กำลังเตรียมรายงาน${reportType}...`, 'info');
    
    // สร้าง URL พร้อม parameters
    let previewUrl = `<?= site_url("System_reports/export_excel/") ?>${reportType}`;
    
    // เพิ่ม query parameters ถ้ามี
    if (Object.keys(params).length > 0) {
        const queryParams = new URLSearchParams(params).toString();
        previewUrl += '?' + queryParams;
    }
    
    // เปิดหน้าต่างใหม่
    const previewWindow = window.open(
        previewUrl, 
        '_blank', 
        'width=1200,height=800,scrollbars=yes,resizable=yes,toolbar=yes,location=yes'
    );
    
    if (previewWindow) {
        previewWindow.focus();
        showToast('เปิดหน้าตัวอย่างรายงานแล้ว', 'success');
        
        // เพิ่ม event listener เมื่อหน้าต่างถูกปิด
        const checkClosed = setInterval(() => {
            if (previewWindow.closed) {
                clearInterval(checkClosed);
                showToast('ปิดหน้าตัวอย่างรายงานแล้ว', 'info');
            }
        }, 1000);
        
    } else {
        showToast('ไม่สามารถเปิดหน้าตัวอย่างได้ กรุณาอนุญาต Pop-up', 'warning');
        
        // แสดง Modal ให้ผู้ใช้เลือก
        showPopupBlockedModal(previewUrl);
    }
}

	
	
	function showPopupBlockedModal(url) {
    const modalHtml = `
        <div class="modal fade" id="popupBlockedModal" tabindex="-1">
            <div class="modal-dialog modal-dialog-centered">
                <div class="modal-content">
                    <div class="modal-header bg-warning text-dark">
                        <h5 class="modal-title">
                            <i class="fas fa-exclamation-triangle me-2"></i>
                            Pop-up ถูกบล็อก
                        </h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                    </div>
                    <div class="modal-body">
                        <p>เบราว์เซอร์ได้บล็อกการเปิดหน้าต่างใหม่ กรุณาเลือกวิธีการดูรายงาน:</p>
                        <div class="d-grid gap-2">
                            <button class="btn btn-primary" onclick="openInCurrentTab('${url}')">
                                <i class="fas fa-external-link-alt me-2"></i>
                                เปิดในแท็บปัจจุบัน
                            </button>
                            <button class="btn btn-outline-primary" onclick="copyReportUrl('${url}')">
                                <i class="fas fa-copy me-2"></i>
                                คัดลอกลิงก์
                            </button>
                        </div>
                        <div class="mt-3">
                            <small class="text-muted">
                                <i class="fas fa-info-circle me-1"></i>
                                เพื่อความสะดวกในครั้งต่อไป กรุณาอนุญาต Pop-up สำหรับเว็บไซต์นี้
                            </small>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    `;
    
    // เพิ่ม modal เข้าไปใน DOM
    document.body.insertAdjacentHTML('beforeend', modalHtml);
    
    // แสดง modal
    const modal = new bootstrap.Modal(document.getElementById('popupBlockedModal'));
    modal.show();
    
    // ลบ modal เมื่อปิด
    document.getElementById('popupBlockedModal').addEventListener('hidden.bs.modal', function() {
        this.remove();
    });
}

	
// ✅ เปิดในแท็บปัจจุบัน
function openInCurrentTab(url) {
    const modal = bootstrap.Modal.getInstance(document.getElementById('popupBlockedModal'));
    modal.hide();
    
    setTimeout(() => {
        window.location.href = url;
    }, 300);
}

// ✅ คัดลอกลิงก์
function copyReportUrl(url) {
    navigator.clipboard.writeText(url).then(() => {
        showToast('คัดลอกลิงก์รายงานแล้ว', 'success');
        
        const modal = bootstrap.Modal.getInstance(document.getElementById('popupBlockedModal'));
        modal.hide();
    }).catch(() => {
        // Fallback สำหรับเบราว์เซอร์เก่า
        const textArea = document.createElement('textarea');
        textArea.value = url;
        document.body.appendChild(textArea);
        textArea.select();
        document.execCommand('copy');
        document.body.removeChild(textArea);
        
        showToast('คัดลอกลิงก์รายงานแล้ว', 'success');
        
        const modal = bootstrap.Modal.getInstance(document.getElementById('popupBlockedModal'));
        modal.hide();
    });
}

// ✅ ฟังก์ชันตรวจสอบการรองรับการพิมพ์
function checkPrintSupport() {
    if (typeof window.print === 'function') {
        return true;
    }
    
    showToast('เบราว์เซอร์ไม่รองรับการพิมพ์โดยตรง', 'warning');
    return false;
}

// ✅ ฟังก์ชันสำหรับดาวน์โหลด PDF (ใช้ในหน้า Preview)
function downloadReportPDF() {
    if (checkPrintSupport()) {
        // ซ่อนปุ่มควบคุมก่อนพิมพ์
        const controls = document.querySelector('.print-controls');
        if (controls) {
            controls.style.display = 'none';
        }
        
        // เปิด print dialog
        window.print();
        
        // แสดงปุ่มควบคุมกลับมา
        setTimeout(() => {
            if (controls) {
                controls.style.display = 'block';
            }
        }, 500);
    }
}

// ✅ ฟังก์ชันสำหรับพิมพ์รายงาน (ใช้ในหน้า Preview)
function printReport() {
    if (checkPrintSupport()) {
        window.print();
    }
}

// ✅ Event Listeners สำหรับ keyboard shortcuts
document.addEventListener('DOMContentLoaded', function() {
    // Keyboard shortcuts สำหรับส่งออกรายงาน
    document.addEventListener('keydown', function(e) {
        // Ctrl/Cmd + E = Export Storage Report
        if ((e.ctrlKey || e.metaKey) && e.key === 'e') {
            e.preventDefault();
            if (typeof exportStorageReport === 'function') {
                exportStorageReport();
            }
        }
        
        // Ctrl/Cmd + R = Export Complain Report (ถ้าอยู่ในหน้า complain)
        if ((e.ctrlKey || e.metaKey) && e.key === 'r' && window.location.pathname.includes('complain')) {
            e.preventDefault();
            if (typeof exportComplainReport === 'function') {
                exportComplainReport();
            }
        }
    });
});

// ✅ เพิ่มฟังก์ชันเช็คสถานะหน้าต่าง Preview
function monitorPreviewWindow(previewWindow, reportType) {
    let checkCount = 0;
    const maxChecks = 300; // ตรวจสอบสูงสุด 5 นาที (300 * 1000ms)
    
    const checkInterval = setInterval(() => {
        checkCount++;
        
        if (previewWindow.closed) {
            clearInterval(checkInterval);
            showToast(`ปิดหน้าตัวอย่างรายงาน${reportType}แล้ว`, 'info');
            return;
        }
        
        if (checkCount >= maxChecks) {
            clearInterval(checkInterval);
            return;
        }
        
        // ตรวจสอบว่าหน้าโหลดเสร็จแล้วหรือยัง
        try {
            if (previewWindow.document && previewWindow.document.readyState === 'complete') {
                // เพิ่ม event listeners ให้หน้า preview
                previewWindow.addEventListener('beforeunload', function() {
                    showToast('กำลังปิดหน้าตัวอย่างรายงาน...', 'info');
                });
            }
        } catch (e) {
            // Cross-origin error - ปกติสำหรับหน้าต่างที่โหลดจาก domain เดียวกัน
        }
    }, 1000);
    
    return checkInterval;
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
                    ${message}
                </div>
                <button type="button" class="btn-close ${type === 'warning' ? '' : 'btn-close-white'} me-2 m-auto" data-bs-dismiss="toast"></button>
            </div>
        </div>
    `;
    
    // สร้าง toast container ถ้าไม่มี
    let toastContainer = document.getElementById('toastContainer');
    if (!toastContainer) {
        toastContainer = document.createElement('div');
        toastContainer.id = 'toastContainer';
        toastContainer.className = 'toast-container position-fixed top-0 end-0 p-3';
        toastContainer.style.zIndex = '9999';
        document.body.appendChild(toastContainer);
    }
    
    // เพิ่ม toast
    toastContainer.insertAdjacentHTML('beforeend', toastHtml);
    
    // แสดง toast
    const toastElement = toastContainer.lastElementChild;
    const toast = new bootstrap.Toast(toastElement, { delay: 4000 });
    toast.show();
    
    // ลบ toast หลังจากซ่อน
    toastElement.addEventListener('hidden.bs.toast', () => {
        toastElement.remove();
    });
}

// Auto-refresh เวลาทุก 1 นาที
setInterval(function() {
    const timeElement = document.getElementById('lastUpdateTime');
    if (timeElement && timeElement.textContent !== 'ไม่ทราบ') {
        // อัปเดต relative time display
        const now = new Date();
        const updateText = timeElement.querySelector('span');
        if (updateText) {
            updateText.title = `อัปเดตเมื่อ ${updateText.textContent}`;
        }
    }
}, 60000);

// เพิ่ม animation สำหรับ progress bar เมื่อโหลดหน้า
document.addEventListener('DOMContentLoaded', function() {
    const progressBars = document.querySelectorAll('.progress-bar');
    progressBars.forEach(bar => {
        const targetWidth = bar.style.width;
        bar.style.width = '0%';
        setTimeout(() => {
            bar.style.transition = 'width 1.5s ease-in-out';
            bar.style.width = targetWidth;
        }, 300);
    });
    
    // เพิ่ม fade-in animation สำหรับ cards
    const cards = document.querySelectorAll('.stat-card');
    cards.forEach((card, index) => {
        card.style.opacity = '0';
        card.style.transform = 'translateY(20px)';
        setTimeout(() => {
            card.style.transition = 'all 0.5s ease-out';
            card.style.opacity = '1';
            card.style.transform = 'translateY(0)';
        }, 100 * index);
    });
});

// Debug function สำหรับทดสอบ API
function testAPI() {
    fetch('<?= site_url("System_reports/api_current_storage_settings") ?>', {
        method: 'GET',
        headers: { 'X-Requested-With': 'XMLHttpRequest' }
    })
    .then(response => response.json())
    .then(data => {
        console.log('API Test Result:', data);
        showToast('API ทำงานปกติ', 'success');
    })
    .catch(error => {
        console.error('API Test Error:', error);
        showToast('API ไม่ทำงาน: ' + error.message, 'error');
    });
}

// เพิ่มปุ่ม Debug ในโหมด development (ถ้าต้องการ)
<?php if (ENVIRONMENT === 'development'): ?>
console.log('Development mode: API debug available');
// เรียกใช้ testAPI() ใน console เพื่อทดสอบ
<?php endif; ?>
</script>

<!-- Custom CSS for Index Theme Storage with Settings -->
<style>
/* Override Pastel Colors with Softer Index Theme Colors */
:root {
    --primary-color: #93c5fd;
    --primary-dark: #60a5fa;
    --secondary-color: #94a3b8;
    --success-color: #6ee7b7;
    --warning-color: #fbbf24;
    --danger-color: #f87171;
    --info-color: #67e8f9;
    --light-gray: #f8fafc;
    --medium-gray: #e2e8f0;
    --dark-gray: #64748b;
    --text-primary: #475569;
    --text-secondary: #94a3b8;
    --border-radius: 12px;
    --box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.06);
    --box-shadow-lg: 0 10px 15px -3px rgba(0, 0, 0, 0.08);
    --transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
}

/* Storage Detail Cards with Index Theme */
.storage-detail-card {
    padding: 1.25rem;
    border-radius: var(--border-radius);
    display: flex;
    align-items: center;
    transition: var(--transition);
    box-shadow: var(--box-shadow);
    border: none;
}

.storage-detail-card:hover {
    transform: translateY(-2px);
    box-shadow: var(--box-shadow-lg);
}

.storage-detail-icon {
    width: 45px;
    height: 45px;
    border-radius: 50%;
    background: rgba(255, 255, 255, 0.2);
    display: flex;
    align-items: center;
    justify-content: center;
    margin-right: 1rem;
    font-size: 1.1rem;
}

.detail-value {
    font-size: 1.3rem;
    font-weight: 700;
    margin-bottom: 0.25rem;
}

.detail-label {
    font-size: 0.85rem;
    font-weight: 500;
}

/* Enhanced Progress Bar */
.progress {
    border-radius: 10px;
    background-color: var(--light-gray);
    border: 1px solid var(--medium-gray);
}

.progress-bar {
    border-radius: 10px;
    font-weight: 600;
    display: flex;
    align-items: center;
    justify-content: center;
    text-shadow: 0 1px 2px rgba(0, 0, 0, 0.2);
}

/* Status Badge with Index Theme */
.status-badge {
    padding: 0.25rem 0.75rem;
    border-radius: 20px;
    font-size: 0.75rem;
    font-weight: 600;
    text-transform: uppercase;
    letter-spacing: 0.5px;
}

.status-badge.normal {
    background: rgba(16, 185, 129, 0.1);
    color: #059669;
}

.status-badge.warning {
    background: rgba(245, 158, 11, 0.1);
    color: #d97706;
}

.status-badge.critical {
    background: rgba(239, 68, 68, 0.1);
    color: #dc2626;
}

/* Alert Override */
.alert {
    background: linear-gradient(135deg, rgba(147, 197, 253, 0.08) 0%, rgba(191, 219, 254, 0.04) 100%);
    border: 1px solid rgba(147, 197, 253, 0.2);
    color: var(--text-primary);
}

/* Code Styling */
code {
    background: rgba(148, 163, 184, 0.08);
    color: var(--text-primary);
    padding: 0.15rem 0.4rem;
    border-radius: 4px;
    font-size: 0.85rem;
    font-family: 'Monaco', 'Consolas', monospace;
}

/* Settings Modal Styling */
.modal-content {
    border-radius: var(--border-radius);
}

.form-control:focus {
    border-color: var(--primary-color);
    box-shadow: 0 0 0 0.2rem rgba(147, 197, 253, 0.25);
}

/* Modal ยืนยัน */
#confirmSaveModal .modal-content {
    border-radius: var(--border-radius);
    overflow: hidden;
}

#confirmSaveModal .display-1 {
    font-size: 4rem;
}

#confirmSaveModal .card {
    border-radius: 8px;
    transition: transform 0.2s ease;
}

#confirmSaveModal .card:hover {
    transform: translateY(-2px);
}

/* Animation สำหรับ Modal */
.modal.fade .modal-dialog {
    transition: transform 0.3s ease-out;
    transform: translate(0, -50px);
}

.modal.show .modal-dialog {
    transform: none;
}

/* Loading states */
.btn:disabled {
    opacity: 0.7;
    cursor: not-allowed;
}

/* Toast Container */
.toast-container {
    z-index: 9999;
}

/* Animation Enhancements */
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

.stat-card {
    animation: fadeInUp 0.6s ease forwards;
    opacity: 0;
}

.stat-card:nth-child(1) { animation-delay: 0.1s; }
.stat-card:nth-child(2) { animation-delay: 0.2s; }
.stat-card:nth-child(3) { animation-delay: 0.3s; }
.stat-card:nth-child(4) { animation-delay: 0.4s; }

/* Chart Card Styling */
.chart-card {
    background: white;
    border-radius: var(--border-radius);
    padding: 1.5rem;
    box-shadow: var(--box-shadow);
    border: 1px solid var(--medium-gray);
}

.chart-header {
    display: flex;
    justify-content: between;
    align-items: center;
    margin-bottom: 1rem;
}

.chart-title {
    font-size: 1.2rem;
    font-weight: 600;
    color: var(--text-primary);
    margin: 0;
}

.btn-chart {
    background: linear-gradient(135deg, var(--primary-color) 0%, var(--primary-dark) 100%);
    color: white;
    padding: 0.5rem 1rem;
    border-radius: 20px;
    font-weight: 600;
    font-size: 0.9rem;
    border: none;
    cursor: default;
}

/* Stat Card Styling */
.stat-card {
    background: white;
    border-radius: var(--border-radius);
    padding: 1.25rem;
    box-shadow: var(--box-shadow);
    border: 1px solid var(--medium-gray);
    transition: var(--transition);
    height: 100%;
}

.stat-card:hover {
    transform: translateY(-3px);
    box-shadow: var(--box-shadow-lg);
}

.stat-card-header {
    display: flex;
    justify-content: space-between;
    align-items: flex-start;
    margin-bottom: 1rem;
}

.stat-icon {
    width: 40px;
    height: 40px;
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 1.1rem;
}

.stat-icon.info {
    background: linear-gradient(135deg, var(--info-color) 0%, #a5f3fc 100%);
    color: #0891b2;
}

.stat-icon.success {
    background: linear-gradient(135deg, var(--success-color) 0%, #a7f3d0 100%);
    color: #059669;
}

.stat-icon.warning {
    background: linear-gradient(135deg, var(--warning-color) 0%, #fcd34d 100%);
    color: #d97706;
}

.stat-icon.primary {
    background: linear-gradient(135deg, var(--primary-color) 0%, #bfdbfe 100%);
    color: #2563eb;
}

.stat-change {
    width: 24px;
    height: 24px;
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 0.75rem;
}

.stat-change.positive {
    background: rgba(16, 185, 129, 0.1);
    color: #059669;
}

.stat-info {
    flex: 1;
}

.stat-label {
    font-size: 0.85rem;
    color: var(--text-secondary);
    margin-bottom: 0.5rem;
    font-weight: 500;
}

.stat-value {
    font-size: 1.1rem;
    font-weight: 700;
    color: var(--text-primary);
}

/* Debug styles (development only) */
<?php if (ENVIRONMENT === 'development'): ?>
.debug-info {
    position: fixed;
    bottom: 10px;
    right: 10px;
    background: rgba(0,0,0,0.8);
    color: white;
    padding: 10px;
    border-radius: 5px;
    font-size: 12px;
    z-index: 10000;
}
<?php endif; ?>

/* Responsive Design */
@media (max-width: 768px) {
    .storage-detail-card {
        padding: 1rem;
        margin-bottom: 0.75rem;
        flex-direction: column;
        text-align: center;
    }
    
    .storage-detail-icon {
        margin-right: 0;
        margin-bottom: 0.5rem;
    }
    
    .detail-value {
        font-size: 1.1rem;
    }
    
    .detail-label {
        font-size: 0.8rem;
    }
    
    .chart-header {
        flex-direction: column;
        gap: 1rem;
        align-items: flex-start;
    }
    
    .stat-card {
        margin-bottom: 1rem;
    }
}

@media (max-width: 576px) {
    .btn-group .btn {
        font-size: 0.8rem;
        padding: 0.375rem 0.5rem;
    }
    
    .btn-group {
        flex-wrap: wrap;
        gap: 0.25rem;
    }
    
    .modal-dialog {
        margin: 1rem;
    }
    
    .stat-card-header {
        flex-direction: column;
        gap: 0.5rem;
        align-items: center;
    }
    
    .stat-info {
        text-align: center;
    }
}

/* Accessibility improvements */
@media (prefers-reduced-motion: reduce) {
    .stat-card,
    .storage-detail-card,
    .progress-bar {
        transition: none;
        animation: none;
    }
    
    .progress-bar {
        animation: none !important;
    }
}

/* Print styles */
@media print {
    .modal,
    .toast-container,
    .btn-group {
        display: none !important;
    }
    
    .card {
        break-inside: avoid;
        border: 1px solid #ccc !important;
        box-shadow: none !important;
    }
    
    .progress-bar {
        background: #ccc !important;
        color: #000 !important;
    }
}
</style>