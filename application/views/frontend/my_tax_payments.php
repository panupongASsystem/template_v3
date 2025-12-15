<div class="text-center pages-head">
    <span class="font-pages-head">ชำระภาษี</span>
</div>
</div>
<img src="<?php echo base_url('docs/welcome-btm-light-other.png'); ?>">

<div class="bg-pages ">
    <div class="container-pages-news" style="position: relative; z-index: 10;">
        <!-- <div class="d-flex justify-content-end">
            <div class="form-group">
                <div class="col-sm-12">
                    <select class="form-select custom-select" id="ChangPagesComplain">
                        <option value="" disabled selected>ยื่นเอกสารออนไลน์</option>
                        <option value="suggestions">รับฟังความคิดเห็น</option>
                        <option value="complain">ร้องเรียน/ร้องทุกข์</option>
                        <option value="follow-complain">ติดตามสถานะเรื่องร้องเรียน</option>
                        <option value="corruption">แจ้งเรื่องทุจริตหน่วยงานภาครัฐ</option>
                    </select>
                </div>
            </div>
        </div> -->
        <div class="underline">

            <div class="container py-5">
                <div class="row justify-content-center">
                    <div class="col-md-12">
                        <div class="card premium-card shadow-lg border-0">
                            <div class="card-header premium-header bg-primary text-white py-3">
                                <div class="d-flex justify-content-between align-items-center">
                                    <h4 class="mb-0"><?= $title ?></h4>
                                    <button onclick="window.location.href='<?= base_url('Pages/logout') ?>'" class="btn btn-outline-light">
                                        <i class="fas fa-sign-out-alt me-2"></i>ออกจากระบบ
                                    </button>
                                </div>
                            </div>

                            <!-- Filter Section -->
                            <div class="card-body border-bottom">
                                <form action="" method="get" id="filterForm">
                                    <div class="row g-3">
                                        <div class="col-md-6 col-lg-2">
                                            <label class="form-label">ปีภาษี</label>
                                            <select name="year" class="form-select">
                                                <option value="">ทั้งหมด</option>
                                                <?php
                                                $current_year = date('Y') + 543;
                                                for ($i = 0; $i < 5; $i++) {
                                                    $year = $current_year - $i;
                                                    $selected = ($filters['year'] == $year) ? 'selected' : '';
                                                ?>
                                                    <option value="<?= $year ?>" <?= $selected ?>><?= $year ?></option>
                                                <?php } ?>
                                            </select>
                                        </div>

                                        <div class="col-md-6 col-lg-2">
                                            <label class="form-label">ประเภทภาษี</label>
                                            <select name="tax_type" class="form-select">
                                                <option value="">ทั้งหมด</option>
                                                <option value="land" <?= ($filters['tax_type'] == 'land') ? 'selected' : '' ?>>ภาษีที่ดินและสิ่งปลูกสร้าง</option>
                                                <option value="signboard" <?= ($filters['tax_type'] == 'signboard') ? 'selected' : '' ?>>ภาษีป้าย</option>
                                                <option value="local" <?= ($filters['tax_type'] == 'local') ? 'selected' : '' ?>>ภาษีท้องถิ่น</option>
                                            </select>
                                        </div>

                                        <div class="col-md-6 col-lg-2">
                                            <label class="form-label">สถานะ</label>
                                            <select name="status" class="form-select">
                                                <option value="">ทั้งหมด</option>
                                                <option value="required" <?= ($filters['status'] == 'required') ? 'selected' : '' ?>>ที่ต้องชำระ</option>
                                                <option value="arrears" <?= ($filters['status'] == 'arrears') ? 'selected' : '' ?>>ค้างชำระ</option>
                                                <option value="pending" <?= ($filters['status'] == 'pending') ? 'selected' : '' ?>>รอตรวจสอบ</option>
                                                <option value="verified" <?= ($filters['status'] == 'verified') ? 'selected' : '' ?>>ยืนยันแล้ว</option>
                                                <option value="rejected" <?= ($filters['status'] == 'rejected') ? 'selected' : '' ?>>ปฏิเสธ</option>
                                            </select>
                                        </div>

                                        <div class="col-md-6 col-lg-3">
                                            <label class="form-label">จำนวนเงิน (บาท)</label>
                                            <div class="input-group">
                                                <input type="number" name="amount_min" class="form-control" placeholder="ต่ำสุด" value="<?= $filters['amount_min'] ?>">
                                                <span class="input-group-text">ถึง</span>
                                                <input type="number" name="amount_max" class="form-control" placeholder="สูงสุด" value="<?= $filters['amount_max'] ?>">
                                            </div>
                                        </div>

                                        <div class="col-md-12 col-lg-3">
                                            <label class="form-label">ช่วงวันที่</label>
                                            <div class="input-group">
                                                <input type="date" name="date_from" class="form-control" value="<?= $filters['date_from'] ?>">
                                                <span class="input-group-text">ถึง</span>
                                                <input type="date" name="date_to" class="form-control" value="<?= $filters['date_to'] ?>">
                                            </div>
                                        </div>

                                        <div class="col-12 text-center mt-4">
                                            <button type="submit" class=" btn-primary btn btn-premium px-4">
                                                <i class="fas fa-search me-2"></i>ค้นหา
                                            </button>
                                            <a href="<?= current_url() ?>" class="btn btn-secondary px-4">
                                                <i class="fas fa-redo me-2"></i>ล้างการค้นหา
                                            </a>
                                        </div>
                                    </div>
                                </form>
                            </div>

                            <!-- User Information Card -->
                            <div class="card-body">
                                <div class="user-profile-card p-4 bg-white rounded-xl shadow-sm">
                                    <div class="d-flex justify-content-between align-items-center">
                                        <div class="user-info d-flex">
                                            <div class="profile-image me-4">
                                                <?php if ($user->mp_img): ?>
                                                    <img src="<?= base_url('docs/img/' . $user->mp_img); ?>" alt="Profile" class="rounded-circle" style="width: 80px; height: 80px; object-fit: cover;">
                                                <?php else: ?>
                                                    <div class="rounded-circle bg-light d-flex align-items-center justify-content-center" style="width: 80px; height: 80px;">
                                                        <i class="fas fa-user-circle text-primary" style="font-size: 3rem;"></i>
                                                    </div>
                                                <?php endif; ?>
                                            </div>
                                            <div>
                                                <div class="d-flex align-items-center mb-2">
                                                    <i class="fas fa-user text-primary me-2 mt-2"></i>
                                                    <h5 class="mb-0">ชื่อ-นามสกุล: <span class="text-dark"><?= $user->mp_fname . ' ' . $user->mp_lname ?></span></h5>
                                                </div>
                                                <div class="d-flex align-items-center mb-2">
                                                    <i class="fas fa-id-card text-primary me-2 mt-2"></i>
                                                    <h5 class="mb-0">เลขบัตรประชาชน: <span class="text-dark"><?= $user->mp_number ?></span></h5>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="contact-info text-end">
                                            <div class="d-flex align-items-center justify-content-center mb-2">
                                                <i class="fas fa-envelope text-primary me-2 mt-2"></i>
                                                <h5 class="mb-0">อีเมล: <span class="text-dark"><?= $user->mp_email ?></span></h5>
                                            </div>
                                            <div class="d-flex align-items-center justify-content-center mb-2">
                                                <i class="fas fa-phone text-primary me-2 mt-2"></i>
                                                <h5 class="mb-0">เบอร์โทร: <span class="text-dark"><?= $user->mp_phone ?></span></h5>
                                            </div>
                                        </div>
                                        <a href="<?= base_url('Pages/edit_profile'); ?>" class="btn btn-premium btn-primary ms-4">
                                            <i class="fas fa-user-edit me-2"></i>แก้ไขโปรไฟล์
                                        </a>
                                    </div>
                                </div>

                                <!-- Results Summary -->
                                <div class="d-flex justify-content-between align-items-center mb-1 mt-3">
                                    <div>
                                        <h5 class="mb-0">ผลการค้นหา: <?= $total_rows ?> รายการ</h5>
                                    </div>
                                    <button type="button" class="btn btn-premium btn-success" id="openTaxPaymentBtn">
                                        <i class="fas fa-plus-circle me-2"></i>ชำระภาษี
                                    </button>
                                </div>

                                <!-- Tax Payments Table -->
                                <div class="table-responsive">
                                    <table class="table premium-table table-hover table-striped">
                                        <thead class="table-primary">
                                            <tr class="text-center">
                                                <th width="5%">ลำดับ</th>
                                                <th width="15%">ประเภทภาษี</th>
                                                <th width="10%">ปีภาษี</th>
                                                <th width="15%">วันครบกำหนดชำระ</th>
                                                <th width="15%">จำนวนเงิน / วันที่ชำระ</th>
                                                <th width="10%">สถานะ</th>
                                                <th width="15%">ข้อมูลเพิ่มเติม</th> <!-- เพิ่มคอลัมน์นี้ -->
                                                <th width="15%">หมายเหตุ</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <?php if (!empty($tax_payments)) : ?>
                                                <?php foreach ($tax_payments as $index => $payment) : ?>
                                                    <tr class="text-center">
                                                        <td><?= $index + 1 ?></td>
                                                        <td class="text-center">
                                                            <?php
                                                            switch ($payment->tax_type) {
                                                                case 'land':
                                                                    echo '<span class="badge premium-badge bg-info">ภาษีที่ดินและสิ่งปลูกสร้าง</span>';
                                                                    break;
                                                                case 'signboard':
                                                                    echo '<span class="badge premium-badge bg-secondary">ภาษีป้าย</span>';
                                                                    break;
                                                                case 'local':
                                                                    echo '<span class="badge premium-badge bg-primary">ภาษีท้องถิ่น</span>';
                                                                    break;
                                                            }
                                                            ?>
                                                        </td>
                                                        <td><?= $payment->tax_year ?></td>
                                                        <td>
                                                            <?php
                                                            // ส่งทั้ง tax_type และ tax_year ไปให้ฟังก์ชัน
                                                            echo get_due_date($payment->tax_type, $payment->tax_year);
                                                            ?>
                                                        </td>
                                                        <td class="text-center">
                                                            <?= number_format($payment->total_amount, 2) ?> บาท
                                                            <?php if (!empty($payment->payment_date) && $payment->payment_date != '0000-00-00 00:00:00'): ?>
                                                                <br><small class="text-muted">
                                                                    ชำระเมื่อ <?= date('d/m/Y', strtotime($payment->payment_date)) ?>
                                                                </small>
                                                            <?php endif; ?>
                                                        </td>
                                                        <td>
                                                            <?php
                                                            switch ($payment->payment_status) {
                                                                case 'pending':
                                                                    echo '<span class="badge premium-badge bg-primary">รอตรวจสอบ</span>';
                                                                    break;
                                                                case 'verified':
                                                                    echo '<span class="badge premium-badge bg-success">ยืนยันแล้ว</span>';
                                                                    break;
                                                                case 'rejected':
                                                                    echo '<span class="badge premium-badge bg-danger">ปฏิเสธ</span>';
                                                                    break;
                                                                case 'arrears':
                                                                    echo '<span class="badge premium-badge bg-arrears">ค้างชำระ</span>';
                                                                    break;
                                                                case 'required':
                                                                    echo '<span class="badge premium-badge bg-warning text-dark">ที่ต้องชำระ</span>';
                                                                    break;
                                                            }
                                                            ?>
                                                        </td>
                                                        <td class="text-center">
                                                            <a href="javascript:void(0);"
                                                                onclick="viewInfo(<?php echo htmlspecialchars(json_encode($payment)); ?>)"
                                                                class="btn btn-info btn-sm">
                                                                <i class="fas fa-info-circle"></i> รายละเอียด
                                                            </a>
                                                        </td>
                                                        <td class="text-center">
                                                            <?php if (in_array($payment->payment_status, ['arrears', 'required'])): ?>
                                                                <a href="#" class="text-primary text-decoration-none" onclick="openPaymentModal(<?php echo htmlspecialchars(json_encode([
                                                                                                                                                    'id' => $payment->id,
                                                                                                                                                    'tax_type' => $payment->tax_type,
                                                                                                                                                    'tax_year' => $payment->tax_year,
                                                                                                                                                    'amount' => $payment->total_amount,
                                                                                                                                                    'admin_comment' => $payment->admin_comment
                                                                                                                                                ])); ?>)">
                                                                    <i class="fas fa-money-bill me-1"></i>คลิกเพื่อชำระภาษี
                                                                </a>
                                                            <?php else: ?>
                                                                <?= $payment->admin_comment ?? '-' ?>
                                                            <?php endif; ?>
                                                        </td>
                                                    </tr>
                                                <?php endforeach; ?>
                                            <?php else : ?>
                                                <tr>
                                                    <td colspan="7" class="text-center py-4">
                                                        <div class="text-muted">
                                                            <i class="fas fa-info-circle me-2"></i>ไม่พบข้อมูลการชำระภาษี
                                                        </div>
                                                    </td>
                                                </tr>
                                            <?php endif; ?>
                                        </tbody>
                                    </table>
                                </div>
                                <?php if ($total_rows > 0): ?>
                                    <div class="mt-4">
                                        <?= $pagination ?>
                                    </div>
                                <?php endif; ?>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

        </div>
    </div>
</div>

<!-- Modal แสดงรายละเอียด -->
<div class="modal fade" id="infoModal" tabindex="-1" aria-hidden="true" style="display: none;">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header bg-primary text-white">
                <h5 class="modal-title">รายละเอียด</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <div id="signboardDetails">
                    <!-- รายละเอียดป้ายจะถูกเพิ่มที่นี่โดย JavaScript -->
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">ปิด</button>
            </div>
        </div>
    </div>
</div>
<!-- ตัวอย่างสำหรับ Fancybox 3 -->
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/@fancyapps/fancybox@3.5.7/dist/jquery.fancybox.min.css" />
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/@fancyapps/fancybox@3.5.7/dist/jquery.fancybox.min.js"></script>
<!-- เพิ่ม JavaScript -->
<script>
    document.addEventListener('DOMContentLoaded', function() {
        const modalElement = document.getElementById('infoModal');
        let infoModal = new bootstrap.Modal(modalElement, {
            keyboard: true,
            backdrop: true
        });

        modalElement.querySelector('.btn-close').addEventListener('click', function() {
            infoModal.hide();
        });

        modalElement.querySelector('.modal-footer .btn-secondary').addEventListener('click', function() {
            infoModal.hide();
        });

        modalElement.addEventListener('hidden.bs.modal', function() {
            document.getElementById('signboardDetails').innerHTML = '';
        });

        window.viewInfo = function(payment) {
            document.getElementById('signboardDetails').innerHTML = `
            <div class="text-center py-4">
                <div class="spinner-border text-primary" role="status">
                    <span class="visually-hidden">กำลังโหลด...</span>
                </div>
            </div>`;
            infoModal.show();

            let content = `
            <div class="payment-details p-4">
                <div class="summary-card bg-gradient-primary text-white p-4 rounded-lg mb-4">
                    <h5 class="border-bottom pb-2 mb-3">สรุปรายการชำระ</h5>
                    <div class="d-flex justify-content-between align-items-center mb-2">
                        <span>เงินภาษีต้น:</span>
                        <span class="fw-bold">${parseFloat(payment.amount).toLocaleString()} บาท</span>
                    </div>
                    <div class="d-flex justify-content-between align-items-center mb-2">
                        <span>ค่าปรับ:</span>
                        <span class="fw-bold text-warning">${parseFloat(payment.penalty_amount || 0).toLocaleString()} บาท</span>
                    </div>
                    <div class="d-flex justify-content-between align-items-center pt-2 border-top mt-2">
                        <span>ยอดรวมทั้งหมด:</span>
                        <span class="fw-bold fs-5">${parseFloat(payment.total_amount || payment.amount).toLocaleString()} บาท</span>
                    </div>
                </div>`;

            if (payment.tax_type === 'signboard') {
                fetch(`<?php echo site_url("Pages/get_signboard_details/"); ?>${payment.id}`)
                    .then(response => {
                        if (!response.ok) {
                            throw new Error('เกิดข้อผิดพลาดในการโหลดข้อมูล');
                        }
                        return response.json();
                    })
                    .then(data => {
                        if (data.status === 'success' && data.details && data.details.length > 0) {
                            content += `
                            <div class="signboard-details mt-4">
                                <h5 class="text-primary mb-3">รายละเอียดป้าย</h5>
                                ${data.details.map((item, index) => `
                                    <div class="signboard-item bg-light p-3 rounded-lg mb-3 shadow-sm">
                                        <div class="d-flex align-items-center gap-3">
                                            <div class="signboard-image">
                                            <a href="<?php echo base_url('docs/img/tax/'); ?>${item.image}" 
                                                   data-fancybox="signboard-gallery"
                                                   data-caption="ป้ายที่ ${index + 1} - พื้นที่: ${parseFloat(item.area).toFixed(2)} ตร.ม.">
                                                    <img src="<?php echo base_url('docs/img/tax/'); ?>${item.image}" 
                                                         class="rounded-lg shadow-sm cursor-pointer" 
                                                         style="width: 100px; height: 100px; object-fit: cover;">
                                                </a>
                                            </div>
                                            <div class="signboard-info flex-grow-1">
                                                <div class="d-flex justify-content-between align-items-center mb-2">
                                                    <h6 class="mb-0">ป้ายที่ ${index + 1}</h6>
                                                    <span class="badge bg-primary">${parseFloat(item.amount).toLocaleString()} บาท</span>
                                                </div>
                                                <div class="text-muted">
                                                    พื้นที่: ${parseFloat(item.area).toFixed(2)} ตร.ม.
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                `).join('')}
                            </div>`;
                        }
                        document.getElementById('signboardDetails').innerHTML = content;
                        // Initialize Fancybox for new images
                        $('[data-fancybox="signboard-gallery"]').fancybox({
                            buttons: [
                                "zoom",
                                "slideShow",
                                "fullScreen",
                                "download",
                                "close"
                            ],
                            loop: true,
                            protect: true
                        });
                    })
                    .catch(error => {
                        document.getElementById('signboardDetails').innerHTML = `
                        <div class="alert alert-danger" role="alert">
                            <i class="fas fa-exclamation-circle me-2"></i>${error.message}
                        </div>`;
                    });
            } else {
                document.getElementById('signboardDetails').innerHTML = content;
            }
        };
    });
</script>

<!-- Modal Form -->
<div class="modal fade" id="taxPaymentModal" tabindex="-1" aria-hidden="true" style="display: none;">
    <div class="modal-dialog modal-lg modal-dialog-centered">
        <div class="modal-content">
            <form action="<?= base_url('Pages/add_tax_payment'); ?>" method="post" enctype="multipart/form-data">
                <input type="hidden" name="payment_id" value="">
                <div class="modal-header bg-success text-white">
                    <h5 class="modal-title">แบบฟอร์มชำระภาษี</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <div class="row g-3">
                        <!-- ข้อมูลส่วนตัว -->
                        <div class="col-12">
                            <div class="form-check mb-3">
                                <input class="form-check-input" type="checkbox" id="useProfileData">
                                <label class="form-check-label" for="useProfileData">
                                    ใช้ข้อมูลตามที่อยู่ที่ลงทะเบียน
                                </label>
                            </div>
                        </div>

                        <!-- จัดกลุ่มข้อมูลส่วนตัวในแถวเดียวกัน -->
                        <div class="col-md-12">
                            <label class="form-label">เลขบัตรประชาชน</label>
                            <input sty type="text" name="citizen_id" class="form-control" required maxlength="13">
                        </div>

                        <div class="row g-3 mt-1">
                            <div class="col-md-6">
                                <label class="form-label">ชื่อ</label>
                                <input type="text" name="firstname" class="form-control" required>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">นามสกุล</label>
                                <input type="text" name="lastname" class="form-control" required>
                            </div>
                        </div>

                        <!-- ข้อมูลการติดต่อในแถวเดียวกัน -->
                        <div class="row g-3 mt-1">
                            <div class="col-md-6">
                                <label class="form-label">เบอร์โทรศัพท์</label>
                                <input type="tel" name="phone" class="form-control" required>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">อีเมล</label>
                                <input type="email" name="email" class="form-control" required>
                            </div>
                        </div>

                        <div class="col-12">
                            <label class="form-label">ที่อยู่</label>
                            <textarea name="address" class="form-control" rows="3" required></textarea>
                        </div>

                        <!-- ข้อมูลภาษีในแถวเดียวกัน -->
                        <div class="row g-3 mt-1">
                            <div class="col-md-6">
                                <label class="form-label">ประเภทภาษี</label>
                                <select name="tax_type" class="form-select" required>
                                    <option value="">เลือกประเภทภาษี</option>
                                    <option value="land">ภาษีที่ดินและสิ่งปลูกสร้าง</option>
                                    <option value="signboard">ภาษีป้าย</option>
                                    <option value="local">ภาษีท้องถิ่น</option>
                                </select>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">ปีภาษี</label>
                                <select name="tax_year" class="form-select" required>
                                    <?php
                                    $current_year = date('Y') + 543;
                                    for ($i = 0; $i < 5; $i++) {
                                        $year = $current_year - $i;
                                        echo "<option value='$year'>$year</option>";
                                    }
                                    ?>
                                </select>
                            </div>
                        </div>

                        <div class="row g-3 mt-1">
                            <div class="col-md-6">
                                <label class="form-label">วันที่ชำระ</label>
                                <input type="datetime-local" name="payment_date" class="form-control" required>
                            </div>

                            <div class="col-md-6">
                                <label class="form-label">จำนวนเงิน (บาท)</label>
                                <input type="number" name="amount" class="form-control" required>
                            </div>
                        </div>

                        <!-- Payment Information Section -->
                        <div class="payment-info-section mb-4">
                            <h5 class="payment-title mb-3">ช่องทางการชำระเงิน</h5>
                            <?php if (isset($payment_settings) && $payment_settings): ?>
                                <div class="row align-items-center">
                                    <div class="col-md-6">
                                        <div class="bank-details p-3 rounded" style="background: #f8f9fa;">
                                            <p class="mb-2"><strong>ธนาคาร:</strong> <?php echo $payment_settings->bank_name; ?></p>
                                            <p class="mb-2"><strong>ชื่อบัญชี:</strong> <?php echo $payment_settings->account_name; ?></p>
                                            <p class="mb-0"><strong>เลขที่บัญชี:</strong> <?php echo $payment_settings->account_number; ?></p>
                                        </div>
                                    </div>
                                    <?php if ($payment_settings->qr_code_image): ?>
                                        <div class="col-md-6 text-center">
                                            <p class="mb-2">QR Code สำหรับชำระเงิน</p>
                                            <img src="<?php echo base_url('docs/img/tax/' . $payment_settings->qr_code_image); ?>"
                                                class="img-fluid qr-code-img"
                                                style="max-width: 200px;">
                                        </div>
                                    <?php endif; ?>
                                </div>
                            <?php endif; ?>
                        </div>

                        <div class="col-md-12">
                            <label class="form-label">หลักฐานการชำระเงิน</label>
                            <input type="file" name="slip_file" class="form-control" accept="image/*" required>
                            <div class="form-text">รองรับไฟล์ jpg, jpeg, png ขนาดไม่เกิน 2MB</div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">ยกเลิก</button>
                    <button type="submit" class="btn btn-success">ยืนยันการชำระ</button>
                </div>
            </form>
        </div>
    </div>
</div>

<style>
    .cursor-pointer {
        cursor: pointer;
    }

    .signboard-image img {
        transition: transform 0.2s ease;
    }

    .signboard-image img:hover {
        transform: scale(1.05);
    }

    /* Custom Fancybox styling */
    .fancybox-caption {
        text-align: center;
        font-size: 1rem;
        padding: 1rem;
    }

    .fancybox-button {
        background: rgba(30, 30, 30, 0.6);
    }

    .fancybox-button:hover {
        background: rgba(30, 30, 30, 0.8);
    }
    /* สไตล์สำหรับ Modal การแสดงรายละเอียด */
    .summary-card {
        background: linear-gradient(135deg, #1976d2 0%, #1565c0 100%);
        box-shadow: 0 4px 15px rgba(25, 118, 210, 0.2);
    }

    .summary-card .text-warning {
        color: #FFE082 !important;
    }

    .signboard-item {
        transition: transform 0.2s ease, box-shadow 0.2s ease;
        border-left: 4px solid #1976d2;
    }

    .signboard-item:hover {
        transform: translateY(-2px);
        box-shadow: 0 4px 10px rgba(0, 0, 0, 0.1) !important;
    }

    .signboard-item img {
        transition: transform 0.3s ease;
    }

    .signboard-item:hover img {
        transform: scale(1.05);
    }

    .badge.bg-primary {
        background: linear-gradient(135deg, #2196f3 0%, #1976d2 100%) !important;
        padding: 0.5em 1em;
        font-weight: 500;
    }

    .payment-details {
        animation: fadeIn 0.3s ease-out;
    }

    @keyframes fadeIn {
        from {
            opacity: 0;
            transform: translateY(10px);
        }

        to {
            opacity: 1;
            transform: translateY(0);
        }
    }

    .container {
        max-width: 1600px;
    }

    .btn-outline-light {
        border: 2px solid rgba(255, 255, 255, 0.5);
        transition: all 0.3s ease;
    }

    .btn-outline-light:hover {
        background: rgba(255, 255, 255, 0.1);
        transform: translateY(-2px);
    }

    .user-profile-card {
        background: linear-gradient(145deg, #ffffff 0%, #f8f9fa 100%);
        border: 1px solid #e0e6ed;
        transition: all 0.3s ease;
    }

    .user-profile-card:hover {
        transform: translateY(-2px);
        box-shadow: 0 8px 20px rgba(0, 0, 0, 0.1);
    }

    .text-primary {
        color: #1976d2 !important;
    }

    .text-dark {
        color: #2c3e50 !important;
        font-weight: 500;
    }

    .payment-title {
        color: #1976d2;
        font-weight: 600;
        padding-bottom: 0.5rem;
        border-bottom: 2px solid #e3f2fd;
    }

    .bank-details {
        border-left: 4px solid #1976d2;
    }

    .qr-code-img {
        border-radius: 10px;
        box-shadow: 0 2px 8px rgba(0, 0, 0, 0.1);
        padding: 10px;
        background: white;
    }

    .premium-card {
        border: none;
        border-radius: 20px;
        box-shadow: 0 10px 30px rgba(0, 0, 0, 0.1);
        background: linear-gradient(145deg, #ffffff 0%, #f8f9fa 100%);
        transition: transform 0.3s ease;
    }

    .premium-card:hover {
        transform: translateY(-5px);
    }

    .premium-header {
        background: linear-gradient(135deg, #1e88e5 0%, #1565c0 100%);
        border-radius: 20px 20px 0 0;
        padding: 1.5rem;
        border: none;
    }

    .premium-header h4 {
        font-weight: 600;
        letter-spacing: 0.5px;
        text-shadow: 2px 2px 4px rgba(0, 0, 0, 0.2);
    }

    .filter-section {
        background-color: rgba(248, 249, 250, 0.9);
        border-bottom: 2px solid #e9ecef;
        padding: 1.5rem;
    }

    .form-label {
        font-weight: 500;
        margin-bottom: 0.5rem;
        color: #2c3e50;
    }

    .form-control,
    .form-select {
        border-radius: 10px;
        border: 2px solid #e0e6ed;
        padding: 0.75rem 1rem;
        transition: all 0.3s ease;
    }

    .form-control:focus,
    .form-select:focus {
        border-color: #1976d2;
        box-shadow: 0 0 0 3px rgba(25, 118, 210, 0.1);
    }

    .row {
        margin-bottom: 1rem;
    }

    .row .row {
        margin-left: 0;
        margin-right: 0;
    }

    .form-control:focus,
    .form-select:focus {
        box-shadow: 0 0 0 3px rgba(30, 136, 229, 0.25);
        border-color: #1e88e5;
    }

    .btn-premium {
        border-radius: 10px;
        padding: 0.75rem 1.5rem;
        font-weight: 500;
        text-transform: uppercase;
        letter-spacing: 0.5px;
        transition: all 0.3s ease;
        box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
    }

    .btn-premium:hover {
        transform: translateY(-2px);
        box-shadow: 0 6px 8px rgba(0, 0, 0, 0.15);
    }

    .btn-premium.btn-primary {
        background: linear-gradient(135deg, #1e88e5 0%, #1565c0 100%);
        border: none;
    }

    .btn-premium.btn-success {
        background: linear-gradient(135deg, #2e7d32 0%, #1b5e20 100%);
        border: none;
    }

    .premium-table {
        margin-top: 1.5rem;
    }

    .premium-table th {
        background: linear-gradient(145deg, #f8f9fa 0%, #e9ecef 100%);
        color: #2196f3;
        font-weight: 600;
        text-transform: uppercase;
        font-size: 0.85rem;
        letter-spacing: 0.5px;
        padding: 1rem;
    }

    .premium-table td {
        padding: 1rem;
        vertical-align: middle;
    }

    .premium-badge {
        padding: 0.5rem 1rem;
        border-radius: 20px;
        font-weight: 500;
        letter-spacing: 0.5px;
        box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
    }

    .premium-badge.bg-warning {
        background: linear-gradient(135deg, #ffc107 0%, #ffb300 100%) !important;
        color: #000 !important;
    }

    .premium-badge.bg-success {
        background: linear-gradient(135deg, #4caf50 0%, #43a047 100%) !important;
    }

    .premium-badge.bg-danger {
        background: linear-gradient(135deg, #f44336 0%, #e53935 100%) !important;
    }

    .premium-badge.bg-arrears {
        background: linear-gradient(135deg, #9ca3af 0%, #6b7280 100%) !important;
        color: #fff !important;
    }

    .modal-dialog {
        max-height: 90vh;
        margin: 20px auto;
    }

    .modal-content {
        height: 80vh;
        overflow-y: auto;
        width: 700px;
        border-radius: 25px;
        border: none;
        box-shadow: 0 20px 40px rgba(0, 0, 0, 0.2);
        background: linear-gradient(145deg, #ffffff 0%, #f8f9fa 100%);
        transform: scale(0.95);
        opacity: 0;
        transition: all 0.3s ease-out;

        /* Customize scrollbar */
        &::-webkit-scrollbar {
            width: 0px;
        }

        &::-webkit-scrollbar-track {
            background: #f1f1f1;
            border-radius: 10px;
        }

        &::-webkit-scrollbar-thumb {
            background: #888;
            border-radius: 10px;

            &:hover {
                background: #555;
            }
        }
    }

    .modal.show .modal-content {
        transform: scale(1);
        opacity: 1;
    }

    .modal-header {
        border-radius: 25px 25px 0 0;
        background: linear-gradient(135deg, #1976d2 0%, #1565c0 100%);
        border: none;
        padding: 1.75rem;
        position: relative;
        overflow: hidden;
    }

    .modal-header::before {
        content: '';
        position: absolute;
        top: 0;
        left: 0;
        right: 0;
        bottom: 0;
        background: linear-gradient(45deg, rgba(255, 255, 255, 0.1) 0%, rgba(255, 255, 255, 0) 100%);
        pointer-events: none;
    }

    .modal-header .modal-title {
        font-size: 1.5rem;
        font-weight: 600;
        color: #fff;
        text-shadow: 2px 2px 4px rgba(0, 0, 0, 0.2);
        letter-spacing: 0.5px;
    }

    .modal-header .btn-close {
        background-color: rgba(255, 255, 255, 0.3);
        border-radius: 50%;
        padding: 0.75rem;
        margin: -0.75rem -0.75rem -0.75rem auto;
        transition: all 0.2s ease;
    }

    .modal-header .btn-close:hover {
        background-color: rgba(255, 255, 255, 0.5);
        transform: rotate(90deg);
    }

    .modal-body {
        padding: 2.5rem;

    }

    .modal-body .form-label {
        font-weight: 600;
        color: #2c3e50;
        margin-bottom: 0.5rem;
    }

    .modal-body .form-control,
    .modal-body .form-select {
        border-radius: 12px;
        border: 2px solid #e0e6ed;
        padding: 0.75rem 1rem;
        font-size: 1rem;
        transition: all 0.3s ease;
        background-color: #f8fafc;
    }

    .modal-body .form-control:focus,
    .modal-body .form-select:focus {
        border-color: #1976d2;
        box-shadow: 0 0 0 4px rgba(25, 118, 210, 0.1);
        background-color: #fff;
    }

    .modal-footer {
        background: #f8fafc;
        padding: 1.5rem;
        border-radius: 0 0 25px 25px;
        border-top: 1px solid #e9ecef;
    }

    .modal-backdrop.show {
        opacity: 0.7;
        backdrop-filter: blur(5px);
    }

    .form-group {
        margin-bottom: 1.5rem;
    }

    /* Animation for form groups */

    .modal-body .form-group {
        opacity: 0;
        transform: translateY(20px);
        animation: modalFormSlideIn 0.5s ease forwards;
    }

    @keyframes modalFormSlideIn {
        to {
            opacity: 1;
            transform: translateY(0);
        }
    }

    .modal-body .form-group:nth-child(1) {
        animation-delay: 0.1s;
    }

    .modal-body .form-group:nth-child(2) {
        animation-delay: 0.2s;
    }

    .modal-body .form-group:nth-child(3) {
        animation-delay: 0.3s;
    }

    .modal-body .form-group:nth-child(4) {
        animation-delay: 0.4s;
    }

    /* Custom checkbox style */
    .modal-body .form-check-input {
        width: 1.2em;
        height: 1.2em;
        border: 2px solid #1976d2;
        border-radius: 6px;
        cursor: pointer;
        transition: all 0.2s ease;
    }

    .modal-body .form-check-input:checked {
        background-color: #1976d2;
        border-color: #1976d2;
    }

    /* User Info Card */
    .user-info-card {
        background: linear-gradient(145deg, #f8f9fa 0%, #e9ecef 100%);
        border-radius: 15px;
        padding: 1.5rem;
        margin-bottom: 2rem;
    }

    .user-info-card strong {
        color: #1565c0;
    }

    /* Pagination */
    .pagination {
        gap: 0.5rem;
    }

    .page-link {
        border-radius: 8px;
        padding: 0.75rem 1rem;
        color: #1e88e5;
        border: none;
        background-color: #f8f9fa;
        transition: all 0.3s ease;
    }

    .page-link:hover {
        background-color: #e3f2fd;
        color: #1565c0;
        transform: translateY(-2px);
    }

    .page-item.active .page-link {
        background: linear-gradient(135deg, #1e88e5 0%, #1565c0 100%);
        box-shadow: 0 4px 6px rgba(30, 136, 229, 0.25);
    }

    /* Animated elements */
    @keyframes fadeIn {
        from {
            opacity: 0;
            transform: translateY(20px);
        }

        to {
            opacity: 1;
            transform: translateY(0);
        }
    }

    .premium-card {
        animation: fadeIn 0.6s ease-out;
    }

    .table tr {
        animation: fadeIn 0.3s ease-out backwards;
    }

    .table tr:nth-child(1) {
        animation-delay: 0.1s;
    }

    .table tr:nth-child(2) {
        animation-delay: 0.2s;
    }

    .table tr:nth-child(3) {
        animation-delay: 0.3s;
    }
</style>

<script>
	 // ระบบชำระภาษี หน้าบ้าน ---------------------
    $(document).ready(function() {
        const taxPaymentModal = new bootstrap.Modal($('#taxPaymentModal')[0], {
            keyboard: false,
            backdrop: 'static'
        });

        // Add animation classes to form groups
        $('#taxPaymentModal .modal-body .form-group').each(function(index) {
            $(this).css('animation-delay', `${(index + 1) * 0.1}s`);
        });

        // เปิดโมดอลจากปุ่มชำระภาษีปกติ
        $('#openTaxPaymentBtn').on('click', function() {
            taxPaymentModal.show();
            $('#useProfileData').prop('checked', true).trigger('change');
        });

        // เปิดโมดอลจากปุ่มชำระภาษีค้างชำระ
        function openPaymentModal(data) {
            taxPaymentModal.show();

            // กรอกข้อมูลภาษี
            $('input[name="payment_id"]').val(data.id);
            $('select[name="tax_type"]').val(data.tax_type);
            $('select[name="tax_year"]').val(data.tax_year);
            $('input[name="amount"]').val(data.amount);

            // กรอกข้อมูลส่วนตัว
            $('input[name="citizen_id"]').val('<?= $user->mp_number ?>');
            $('input[name="firstname"]').val('<?= $user->mp_fname ?>');
            $('input[name="lastname"]').val('<?= $user->mp_lname ?>');
            $('input[name="phone"]').val('<?= $user->mp_phone ?>');
            $('input[name="email"]').val('<?= $user->mp_email ?>');
            $('textarea[name="address"]').val('<?= $user->mp_address ?>');
        }
        window.openPaymentModal = openPaymentModal;

        function clearModalData() {
            $('select[name="tax_type"]').val('');
            $('select[name="tax_year"]').val('');
            $('input[name="amount"]').val('');
            $('input[name="citizen_id"]').val('');
            $('input[name="firstname"]').val('');
            $('input[name="lastname"]').val('');
            $('input[name="phone"]').val('');
            $('input[name="email"]').val('');
            $('textarea[name="address"]').val('');
            $('#useProfileData').prop('checked', false);
        }

        // เพิ่มการล้างข้อมูลเมื่อปิดโมดอล
        $('.btn-close, .modal-footer .btn-secondary').on('click', function() {
            taxPaymentModal.hide();
            clearModalData();
        });

        // เพิ่ม event listener สำหรับการปิดโมดอล
        $('#taxPaymentModal').on('hidden.bs.modal', function() {
            clearModalData();
        });



        // Handle profile data checkbox
        $('#useProfileData').on('change', function() {
            if ($(this).is(':checked')) {
                $('input[name="citizen_id"]').val('<?= $user_info["mp_number"] ?>');
                $('input[name="firstname"]').val('<?= $user_info["mp_fname"] ?>');
                $('input[name="lastname"]').val('<?= $user_info["mp_lname"] ?>');
                $('input[name="phone"]').val('<?= $user_info["mp_phone"] ?>');
                $('input[name="email"]').val('<?= $user_info["mp_email"] ?>');
                $('textarea[name="address"]').val('<?= $user->mp_address ?>');
            } else {
                $('input[name="citizen_id"], input[name="firstname"], input[name="lastname"], input[name="phone"], input[name="email"]').val('');
            }
        });

        // Show success message if payment was successful
        <?php if ($this->session->flashdata('payment_tax_success')) : ?>
            Swal.fire({
                title: 'สำเร็จ!',
                text: 'บันทึกข้อมูลการชำระภาษีเรียบร้อยแล้ว',
                icon: 'success'
            });
        <?php endif; ?>
    });

    $(document).ready(function() {
        // Enhance form controls with smooth animations
        $('.form-control, .form-select').on('focus', function() {
            $(this).closest('.col-md-6, .col-md-12').addClass('scale-up');
        }).on('blur', function() {
            $(this).closest('.col-md-6, .col-md-12').removeClass('scale-up');
        });

        // Add loading states to buttons
        $('.btn-premium').on('click', function() {
            let $btn = $(this);
            $btn.html('<span class="spinner-border spinner-border-sm me-2"></span>กำลังดำเนินการ...');
            setTimeout(() => {
                $btn.html($btn.data('original-text'));
            }, 1000);
        }).each(function() {
            $(this).data('original-text', $(this).html());
        });

        // Enhance table row hover effects
        $('.premium-table tbody tr').hover(
            function() {
                $(this).addClass('bg-light');
            },
            function() {
                $(this).removeClass('bg-light');
            }
        );

        // Add smooth scroll to pagination
        $('.pagination .page-link').on('click', function(e) {
            window.location.href = $(this).attr('href');
        });
        // Remove previous pagination click handler
        $('.pagination .page-link').off('click');

        // Initialize tooltips
        const tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'));
        tooltipTriggerList.map(function(tooltipTriggerEl) {
            return new bootstrap.Tooltip(tooltipTriggerEl);
        });
    });
    // ----------------------------------------
</script>