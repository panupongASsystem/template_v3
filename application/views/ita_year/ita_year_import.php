<!DOCTYPE html>
<html lang="th">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>ระบบจัดการ ITA - นำเข้าข้อมูล</title>

    <!-- Bootstrap 5 CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">

    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">

    <!-- Google Fonts -->
    <link href="https://fonts.googleapis.com/css2?family=Prompt:wght@300;400;500;600;700&display=swap" rel="stylesheet">

    <!-- SweetAlert2 -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.min.css">

    <style>
        * {
            font-family: 'Prompt', sans-serif;
        }

        :root {
            --primary-color: #2563eb;
            --secondary-color: #1e40af;
            --accent-color: #3b82f6;
            --light-bg: #f8fafc;
            --border-color: #e2e8f0;
            --text-dark: #0f172a;
            --text-muted: #64748b;
        }

        body {
            background-color: var(--light-bg);
            color: var(--text-dark);
        }

        /* Main Content */
        .main-content {
            margin: 0;
            padding: 30px;
            min-height: 100vh;
        }

        /* Disabled option styling */
        select option:disabled {
            color: #999;
            font-style: italic;
            background-color: #f5f5f5;
        }
    </style>
</head>

<body>

<!-- Main Content -->
<div class="main-content">
    <div class="container-fluid mt-4">
        <div class="row">
            <div class="col-12">
                <div class="card shadow-sm">
                    <div class="card-header bg-primary text-white">
                        <h4 class="mb-0">
                            <i class="fas fa-download"></i> นำเข้าข้อมูล ITA Year จาก API
                        </h4>
                    </div>
                    <div class="card-body">
                        <?php if ($this->session->flashdata('save_success')): ?>
                            <div class="alert alert-success alert-dismissible fade show" role="alert">
                                <i class="fas fa-check-circle"></i> นำเข้าข้อมูลสำเร็จ!
                                <?php if ($this->session->flashdata('import_summary')): ?>
                                    <?php $summary = $this->session->flashdata('import_summary'); ?>
                                    <br>
                                    <small>
                                        - นำเข้าปี: <?= $summary['years'] ?> ปี<br>
                                        - นำเข้า Topic: <?= $summary['topics'] ?> รายการ<br>
                                        - นำเข้า Link: <?= $summary['links'] ?> รายการ
                                    </small>
                                <?php endif; ?>
                                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                            </div>
                        <?php endif; ?>

                        <?php if ($this->session->flashdata('error')): ?>
                            <div class="alert alert-danger alert-dismissible fade show" role="alert">
                                <i class="fas fa-exclamation-triangle"></i> <?= $this->session->flashdata('error') ?>
                                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                            </div>
                        <?php endif; ?>

                        <div class="row">
                            <!-- นำเข้าทั้งหมด -->
                            <div class="col-md-6 mb-4">
                                <div class="card border-primary h-100">
                                    <div class="card-body text-center">
                                        <i class="fas fa-database fa-3x text-primary mb-3"></i>
                                        <h5 class="card-title">นำเข้าข้อมูลทั้งหมด</h5>
                                        <p class="card-text text-muted">
                                            นำเข้าข้อมูล ITA Year ทุกปีพร้อม Topic และ Link ทั้งหมด
                                        </p>
                                        <button type="button" class="btn btn-primary btn-lg" onclick="confirmImportAll()">
                                            <i class="fas fa-download"></i> นำเข้าทั้งหมด
                                        </button>
                                    </div>
                                </div>
                            </div>

                            <!-- นำเข้าเฉพาะปี -->
                            <div class="col-md-6 mb-4">
                                <div class="card border-success h-100">
                                    <div class="card-body text-center">
                                        <i class="fas fa-calendar-alt fa-3x text-success mb-3"></i>
                                        <h5 class="card-title">นำเข้าเฉพาะปี</h5>
                                        <p class="card-text text-muted">
                                            เลือกนำเข้าข้อมูลเฉพาะปีที่ต้องการ
                                        </p>
                                        
                                        <form method="post" action="<?= site_url('ita_year_import/import_by_year') ?>" id="importByYearForm">
                                            <div class="mb-3">
                                                <select name="year" class="form-select form-select-lg" required>
                                                    <option value="">-- เลือกปี --</option>
                                                    <?php if (!empty($years)): ?>
                                                        <?php foreach ($years as $year): ?>
                                                            <?php 
                                                                // ตรวจสอบว่าปีนี้มีอยู่ใน tbl_ita_year แล้วหรือไม่
                                                                $is_exists = isset($existing_years) && 
                                                                             is_array($existing_years) && 
                                                                             in_array($year['ita_year_year'], $existing_years);
                                                            ?>
                                                            <option value="<?= $year['ita_year_year'] ?>" <?= $is_exists ? 'disabled' : '' ?>>
                                                                ปี <?= $year['ita_year_year'] ?> <?= $is_exists ? ' (มีข้อมูลอยู่แล้ว)' : '' ?>
                                                            </option>
                                                        <?php endforeach; ?>
                                                    <?php endif; ?>
                                                </select>
                                            </div>
                                            <button type="button" class="btn btn-success btn-lg" onclick="confirmImportByYear()">
                                                <i class="fas fa-download"></i> นำเข้าตามปีที่เลือก
                                            </button>
                                        </form>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="alert alert-info mt-4">
                            <i class="fas fa-info-circle"></i> <strong>หมายเหตุ:</strong>
                            <ul class="mb-0 mt-2">
                                <li>ถ้ามีข้อมูลปีเดียวกันอยู่แล้ว ระบบจะลบข้อมูลเก่าและเพิ่มข้อมูลใหม่แทน</li>
                                <li>ระบบจะเติม Base URL ให้กับ Link โดยอัตโนมัติ</li>
                                <li>Link ที่เป็น URL เต็มอยู่แล้วจะไม่ถูกเติม Base URL</li>
                            </ul>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<!-- End Main Content -->

<!-- jQuery -->
<script src="https://code.jquery.com/jquery-3.7.0.min.js"></script>

<!-- Bootstrap 5 JS -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>

<!-- SweetAlert2 -->
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

<script>
function confirmImportAll() {
    Swal.fire({
        title: 'ยืนยันการนำเข้า',
        text: 'คุณต้องการนำเข้าข้อมูลทั้งหมดใช่หรือไม่?',
        icon: 'question',
        showCancelButton: true,
        confirmButtonColor: '#0d6efd',
        cancelButtonColor: '#6c757d',
        confirmButtonText: 'ใช่, นำเข้าเลย!',
        cancelButtonText: 'ยกเลิก',
        showLoaderOnConfirm: true,
        preConfirm: () => {
            window.location.href = '<?= site_url('ita_year_import/import_all') ?>';
        }
    });
}

function confirmImportByYear() {
    const yearSelect = document.querySelector('select[name="year"]');
    if (!yearSelect.value) {
        Swal.fire({
            icon: 'warning',
            title: 'กรุณาเลือกปี',
            text: 'โปรดเลือกปีที่ต้องการนำเข้าข้อมูล'
        });
        return;
    }

    Swal.fire({
        title: 'ยืนยันการนำเข้า',
        text: `คุณต้องการนำเข้าข้อมูลปี ${yearSelect.value} ใช่หรือไม่?`,
        icon: 'question',
        showCancelButton: true,
        confirmButtonColor: '#198754',
        cancelButtonColor: '#6c757d',
        confirmButtonText: 'ใช่, นำเข้าเลย!',
        cancelButtonText: 'ยกเลิก'
    }).then((result) => {
        if (result.isConfirmed) {
            document.getElementById('importByYearForm').submit();
        }
    });
}

// Show success/error messages
<?php if($this->session->flashdata('del_success')): ?>
    Swal.fire({
        icon: 'success',
        title: 'สำเร็จ!',
        text: 'ลบข้อมูลเรียบร้อยแล้ว',
        timer: 2000,
        showConfirmButton: false
    });
<?php endif; ?>

<?php if($this->session->flashdata('add_success')): ?>
    Swal.fire({
        icon: 'success',
        title: 'สำเร็จ!',
        text: 'เพิ่มข้อมูลเรียบร้อยแล้ว',
        timer: 2000,
        showConfirmButton: false
    });
<?php endif; ?>

<?php if($this->session->flashdata('edit_success')): ?>
    Swal.fire({
        icon: 'success',
        title: 'สำเร็จ!',
        text: 'แก้ไขข้อมูลเรียบร้อยแล้ว',
        timer: 2000,
        showConfirmButton: false
    });
<?php endif; ?>
</script>

</body>
</html>
