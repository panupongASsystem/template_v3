<!-- Flash Messages -->
<?php if ($this->session->flashdata('toggle_success')): ?>
    <div class="alert alert-info alert-dismissible fade show" role="alert">
        <i class="bi bi-info-circle-fill"></i> <?= $this->session->flashdata('toggle_success'); ?>
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
    </div>
<?php endif; ?>

<?php if ($this->session->flashdata('save_success')): ?>
    <div class="alert alert-success alert-dismissible fade show" role="alert">
        <i class="bi bi-check-circle-fill"></i> บันทึกข้อมูลสำเร็จ!
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
    </div>
<?php endif; ?>

<?php if ($this->session->flashdata('del_success')): ?>
    <div class="alert alert-success alert-dismissible fade show" role="alert">
        <i class="bi bi-check-circle-fill"></i> ลบข้อมูลสำเร็จ!
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
    </div>
<?php endif; ?>

<!-- 🆕 แสดงสถานะแหล่งข้อมูลปัจจุบัน -->
<div class="card shadow mb-3" style="border-left: 4px solid <?= ($ci_data_source === 'api') ? '#3498db' : '#95a5a6'; ?>;">
    <div class="card-body" style="padding: 15px 25px;">
        <div style="display: flex; align-items: center; justify-content: space-between;">
            <div style="display: flex; align-items: center; gap: 15px;">
                <?php if ($ci_data_source === 'api'): ?>
                    <i class="bi bi-cloud-download" style="font-size: 1.8em; color: #3498db;"></i>
                    <div>
                        <strong style="color: #2c3e50; font-size: 1.1em;">แหล่งข้อมูล: ดึงจาก API อัตโนมัติ</strong>
                        <p style="margin: 5px 0 0 0; color: #7f8c8d; font-size: 0.9em;">
                            ระบบจะดึงข้อมูลจำนวนประชากรจาก API กรมการปกครองโดยอัตโนมัติ
                        </p>
                    </div>
                <?php else: ?>
                    <i class="bi bi-pencil-square" style="font-size: 1.8em; color: #95a5a6;"></i>
                    <div>
                        <strong style="color: #2c3e50; font-size: 1.1em;">แหล่งข้อมูล: กรอกข้อมูลด้วยตนเอง</strong>
                        <p style="margin: 5px 0 0 0; color: #7f8c8d; font-size: 0.9em;">
                            เจ้าหน้าที่สามารถเพิ่ม/แก้ไขข้อมูลด้วยตนเอง
                        </p>
                    </div>
                <?php endif; ?>
            </div>
            
            <!-- 🆕 ปุ่มสลับแหล่งข้อมูล -->
            <a href="#" onclick="confirmToggleDataSource(); return false;" class="btn btn-outline-primary" style="min-width: 150px;">
                <i class="bi bi-arrow-left-right"></i> 
                สลับเป็น <?= ($ci_data_source === 'api') ? 'Manual' : 'API'; ?>
            </a>
        </div>
    </div>
</div>

<!-- ปุ่มดำเนินการ -->
<?php if ($ci_data_source === 'manual'): ?>
    <!-- แสดงปุ่มเพิ่มข้อมูล เฉพาะโหมด Manual -->
    <a class="btn add-btn" href="<?= site_url('Ci_backend/adding'); ?>" role="button">
        <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-plus-circle" viewBox="0 0 16 16">
            <path d="M8 15A7 7 0 1 1 8 1a7 7 0 0 1 0 14zm0 1A8 8 0 1 0 8 0a8 8 0 0 0 0 16z" />
            <path d="M8 4a.5.5 0 0 1 .5.5v3h3a.5.5 0 0 1 0 1h-3v3a.5.5 0 0 1-1 0v-3h-3a.5.5 0 0 1 0-1h3v-3A.5.5 0 0 1 8 4z" />
        </svg> เพิ่มข้อมูล
    </a>
<?php endif; ?>

<a class="btn btn-light" href="<?= site_url('Ci_backend'); ?>" role="button">
    <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-arrow-clockwise" viewBox="0 0 16 16">
        <path fill-rule="evenodd" d="M8 3a5 5 0 1 0 4.546 2.914.5.5 0 0 1 .908-.417A6 6 0 1 1 8 2v1z" />
        <path d="M8 4.466V.534a.25.25 0 0 1 .41-.192l2.36 1.966c.12.1.12.284 0 .384L8.41 4.658A.25.25 0 0 1 8 4.466z" />
    </svg> Refresh Data
</a>

<!-- 🆕 แสดงสถานะการเชื่อมต่อ API (เฉพาะโหมด API) -->
<?php if ($ci_data_source === 'api' && isset($api_status)): ?>
    <?php if ($api_status['status'] === 'success'): ?>
        <!-- ✅ สถานะสำเร็จ -->
        <div class="alert alert-success alert-dismissible fade show mt-3" role="alert" style="display: flex; align-items: center; gap: 10px;">
            <i class="bi bi-check-circle-fill" style="font-size: 1.2em;"></i>
            <div style="flex: 1;">
                <strong>API สถิติจำนวนประชากร:</strong> <?= htmlspecialchars($api_status['message']); ?>
                <small class="ms-2 text-muted">(⚡ <?= $api_status['response_time']; ?> ms)</small>
            </div>
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    <?php elseif ($api_status['status'] === 'warning'): ?>
        <!-- ⚠️ สถานะเตือน -->
        <div class="alert alert-warning alert-dismissible fade show mt-3" role="alert" style="display: flex; align-items: center; gap: 10px;">
            <i class="bi bi-exclamation-triangle-fill" style="font-size: 1.2em;"></i>
            <div style="flex: 1;">
                <strong>API สถิติจำนวนประชากร:</strong> <?= htmlspecialchars($api_status['message']); ?>
                <a href="<?= site_url('system_config_backend/address'); ?>" class="alert-link ms-2">ตั้งค่าที่นี่</a>
            </div>
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    <?php else: ?>
        <!-- ❌ สถานะผิดพลาด -->
        <div class="alert alert-danger alert-dismissible fade show mt-3" role="alert" style="display: flex; align-items: center; gap: 10px;">
            <i class="bi bi-x-circle-fill" style="font-size: 1.2em;"></i>
            <div style="flex: 1;">
                <strong>API สถิติจำนวนประชากร:</strong> <?= htmlspecialchars($api_status['message']); ?>
                <?php if (isset($api_status['response_time'])): ?>
                    <small class="ms-2 text-muted">(⚡ <?= $api_status['response_time']; ?> ms)</small>
                <?php endif; ?>
                <br>
                <small class="text-muted">💡 คุณสามารถสลับกลับไปใช้โหมด Manual เพื่อกรอกข้อมูลด้วยตนเอง</small>
            </div>
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    <?php endif; ?>
<?php endif; ?>

<!-- DataTales Example -->
<div class="card shadow mb-4">
    <div class="card-header py-3" style="display: flex; justify-content: space-between; align-items: center;">
        <h6 class="m-0 font-weight-bold text-black">
            จัดการข้อมูลชุมชน
            <?php if ($ci_data_source === 'api'): ?>
                <span class="badge bg-primary ms-2">API Mode</span>
            <?php else: ?>
                <span class="badge bg-secondary ms-2">Manual Mode</span>
            <?php endif; ?>
        </h6>
    </div>
    <div class="card-body">
        <?php if ($ci_data_source === 'manual'): ?>
            <!-- โหมด Manual: แสดงตารางข้อมูลจาก Database -->
            <div class="table-responsive">
                <?php $Index = 1; ?>
                <table id="newdataTables" class="table">
                    <thead>
                        <tr>
                            <th style="width: 3%;">ลำดับ</th>
                            <th style="width: 25%;">ชื่อหมู่บ้าน</th>
                            <th style="width: 22%;">จำนวนประชากรทั้งหมด</th>
                            <th style="width: 17%;">จำนวนประชากรชาย</th>
                            <th style="width: 20%;">จำนวนประชากรหญิง</th>
                            <th style="width: 17%;">จำนวนครัวเรือน</th>
                            <th style="width: 13%;">อัพโหลดโดย</th>
                            <th style="width: 7%;">วันที่</th>
                            <th style="width: 3%;">จัดการ</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($query as $rs) { ?>
                            <tr role="row">
                                <td align="center"><?= $Index; ?></td>
                                <td><?= $rs->ci_name; ?></td>
                                <td><?= number_format($rs->ci_total); ?></td>
                                <td><?= number_format($rs->ci_man); ?></td>
                                <td><?= number_format($rs->ci_woman); ?></td>
                                <td><?= number_format($rs->ci_home); ?></td>
                                <td><?= $rs->ci_by; ?></td>
                                <td><?= date('d/m/Y H:i', strtotime($rs->ci_datesave . '+543 years')) ?> น.</td>
                                <td align="center">
                                    <a href="<?= site_url('Ci_backend/editing/' . $rs->ci_id); ?>">
                                        <i class="bi bi-pencil-square fa-lg"></i>
                                    </a>
                                    <a href="#" role="button" onclick="confirmDelete('<?= $rs->ci_id; ?>'); return false;">
                                        <i class="bi bi-trash fa-lg"></i>
                                    </a>
                                </td>
                            </tr>
                        <?php
                            $Index++;
                        } ?>
                    </tbody>
                </table>
            </div>
        <?php else: ?>
            <!-- โหมด API: แสดงข้อความแจ้งเตือน -->
            <div class="alert alert-info" style="margin: 20px;">
                <i class="bi bi-info-circle" style="font-size: 1.5em;"></i>
                <strong>โหมด API อัตโนมัติ</strong>
                <p style="margin: 10px 0 0 0;">
                    ข้อมูลจำนวนประชากรจะถูกดึงจาก API กรมการปกครองโดยอัตโนมัติ<br>
                    ไม่จำเป็นต้องกรอกข้อมูลด้วยตนเอง - ระบบจะแสดงข้อมูลในหน้าบ้านโดยตรง
                </p>
            </div>
            
            <?php if (!empty($query)): ?>
                <!-- แสดงข้อมูลที่มีอยู่เดิม (ถ้ามี) -->
                <div class="alert alert-warning" style="margin: 20px;">
                    <i class="bi bi-exclamation-triangle"></i>
                    <strong>ข้อมูลเก่าที่บันทึกไว้ (<?= count($query); ?> รายการ)</strong>
                    <p style="margin: 5px 0 0 0; font-size: 0.9em;">
                        ข้อมูลที่กรอกไว้ก่อนหน้านี้จะยังคงอยู่ แต่จะไม่ถูกใช้งานในโหมด API
                    </p>
                </div>
            <?php endif; ?>
        <?php endif; ?>
    </div>
</div>

<!-- JavaScript -->
<script>
    function confirmDelete(ci_id) {
        Swal.fire({
            title: 'กดเพื่อยืนยัน?',
            text: "คุณจะไม่สามารถกู้คืนได้อีก!",
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#3085d6',
            cancelButtonColor: '#d33',
            confirmButtonText: 'ใช่, ต้องการลบ!',
            cancelButtonText: 'ยกเลิก'
        }).then((result) => {
            if (result.isConfirmed) {
                window.location.href = "<?= site_url('Ci_backend/del_ci/'); ?>" + ci_id;
            }
        });
    }

    function confirmToggleDataSource() {
        const currentSource = '<?= $ci_data_source; ?>';
        const newSource = (currentSource === 'api') ? 'Manual' : 'API';
        
        let message = '';
        if (currentSource === 'manual') {
            message = 'ระบบจะดึงข้อมูลจาก API กรมการปกครองอัตโนมัติ<br>ข้อมูลที่กรอกไว้จะยังคงอยู่ แต่จะไม่ถูกใช้งาน';
        } else {
            message = 'คุณจะต้องกรอกข้อมูลด้วยตนเอง<br>ข้อมูลจาก API จะไม่ถูกแสดงในหน้าบ้าน';
        }
        
        Swal.fire({
            title: 'สลับแหล่งข้อมูลเป็น ' + newSource + '?',
            html: message,
            icon: 'question',
            showCancelButton: true,
            confirmButtonColor: '#3498db',
            cancelButtonColor: '#95a5a6',
            confirmButtonText: 'ใช่, สลับเลย!',
            cancelButtonText: 'ยกเลิก'
        }).then((result) => {
            if (result.isConfirmed) {
                window.location.href = "<?= site_url('Ci_backend/toggle_data_source'); ?>";
            }
        });
    }
</script>

<style>
    .add-btn {
        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        color: white;
        border: none;
        padding: 10px 20px;
        border-radius: 8px;
        transition: all 0.3s ease;
        box-shadow: 0 4px 15px rgba(102, 126, 234, 0.3);
    }

    .add-btn:hover {
        transform: translateY(-2px);
        box-shadow: 0 6px 20px rgba(102, 126, 234, 0.4);
        color: white;
    }
</style>