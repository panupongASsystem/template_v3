<!DOCTYPE html>
<html lang="th">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= $page_title ?> - จัดการข้อมูล</title>
    
    <!-- Bootstrap 5 -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Font Awesome 6 -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
    <!-- DataTables Bootstrap 5 -->
    <link rel="stylesheet" href="https://cdn.datatables.net/1.13.7/css/dataTables.bootstrap5.min.css">
    <!-- Google Fonts -->
    <link href="https://fonts.googleapis.com/css2?family=Sarabun:wght@300;400;500;600;700&family=Kanit:wght@400;500;600;700&display=swap" rel="stylesheet">
    <!-- SweetAlert2 -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.min.css">
    
    <style>
        :root {
            --primary-color: #1e40af;
            --primary-light: #3b82f6;
            --primary-dark: #1e3a8a;
            --secondary-color: #64748b;
            --success-color: #059669;
            --warning-color: #f59e0b;
            --danger-color: #dc2626;
            --white: #ffffff;
            --gray-50: #f8fafc;
            --gray-100: #f1f5f9;
            --gray-200: #e2e8f0;
            --gray-300: #cbd5e1;
            --gray-600: #475569;
            --gray-800: #1e293b;
            --shadow: 0 4px 6px rgba(0, 0, 0, 0.07);
            --shadow-md: 0 6px 15px rgba(0, 0, 0, 0.08);
            --shadow-lg: 0 10px 25px rgba(0, 0, 0, 0.1);
        }

        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
            font-family: 'Sarabun', sans-serif;
        }

        body {
            background: linear-gradient(135deg, #f8fafc 0%, #e2e8f0 100%);
            min-height: 100vh;
            padding: 2rem 0;
        }

        /* Header */
        .page-header {
            background: var(--white);
            border-radius: 16px;
            padding: 2rem;
            margin-bottom: 2rem;
            box-shadow: var(--shadow-md);
            border-left: 5px solid var(--primary-color);
            animation: slideDown 0.5s ease-out;
        }

        @keyframes slideDown {
            from {
                opacity: 0;
                transform: translateY(-30px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        .page-header h1 {
            font-family: 'Kanit', sans-serif;
            font-size: 2rem;
            font-weight: 700;
            color: var(--gray-800);
            margin: 0 0 0.5rem 0;
            display: flex;
            align-items: center;
            gap: 1rem;
        }

        .page-header h1 i {
            color: var(--primary-color);
        }

        .page-header .subtitle {
            color: var(--secondary-color);
            font-size: 1.05rem;
            margin: 0;
        }

        /* Top Actions Bar */
        .top-actions {
            display: flex;
            justify-content: space-between;
            align-items: center;
            gap: 1rem;
            margin-bottom: 2rem;
            flex-wrap: wrap;
        }

        /* User Info Card */
        .user-info-card {
            background: linear-gradient(135deg, var(--primary-color) 0%, var(--primary-light) 100%);
            padding: 1.25rem 1.75rem;
            border-radius: 12px;
            color: var(--white);
            box-shadow: var(--shadow);
            display: flex;
            align-items: center;
            gap: 1.5rem;
            flex: 1;
            animation: fadeInUp 0.6s ease-out;
        }

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

        .user-avatar {
            width: 50px;
            height: 50px;
            background: rgba(255, 255, 255, 0.2);
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 1.5rem;
        }

        .user-details strong {
            font-weight: 600;
            display: block;
            margin-bottom: 0.25rem;
        }

        .user-role {
            font-size: 0.9rem;
            opacity: 0.9;
        }

        /* Action Buttons */
        .btn-back-admin {
            display: inline-flex;
            align-items: center;
            gap: 0.75rem;
            padding: 0.875rem 1.75rem;
            background: var(--gray-600);
            color: var(--white);
            border-radius: 12px;
            text-decoration: none;
            font-weight: 600;
            box-shadow: var(--shadow);
            transition: all 0.3s ease;
        }

        .btn-back-admin:hover {
            background: var(--gray-800);
            color: var(--white);
            transform: translateY(-2px);
            box-shadow: var(--shadow-lg);
        }

        .btn-add-new {
            display: inline-flex;
            align-items: center;
            gap: 0.75rem;
            padding: 0.875rem 1.75rem;
            background: var(--success-color);
            color: var(--white);
            border-radius: 12px;
            text-decoration: none;
            font-weight: 600;
            box-shadow: var(--shadow);
            transition: all 0.3s ease;
        }

        .btn-add-new:hover {
            background: #047857;
            color: var(--white);
            transform: translateY(-2px);
            box-shadow: var(--shadow-lg);
        }

        /* Table Card */
        .table-card {
            background: var(--white);
            border-radius: 16px;
            padding: 2rem;
            box-shadow: var(--shadow-md);
            animation: fadeInUp 0.6s ease-out 0.2s both;
        }

        .table-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 1.5rem;
            padding-bottom: 1rem;
            border-bottom: 2px solid var(--gray-200);
        }

        .table-header h5 {
            font-family: 'Kanit', sans-serif;
            font-size: 1.35rem;
            font-weight: 600;
            color: var(--gray-800);
            margin: 0;
            display: flex;
            align-items: center;
            gap: 0.75rem;
        }

        /* DataTable Styling */
        .dataTables_wrapper {
            padding: 0;
        }

        table.dataTable {
            border-collapse: separate !important;
            border-spacing: 0 0.5rem;
        }

        table.dataTable thead th {
            background: var(--gray-100);
            font-weight: 600;
            color: var(--gray-800);
            border: none;
            padding: 1rem;
            font-size: 0.95rem;
        }

        table.dataTable tbody tr {
            background: var(--white);
            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.05);
            transition: all 0.3s ease;
        }

        table.dataTable tbody tr:hover {
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.1);
            transform: translateY(-2px);
        }

        table.dataTable tbody td {
            vertical-align: middle;
            padding: 1rem;
            border: none;
        }

        /* Badges */
        .badge-category {
            display: inline-flex;
            align-items: center;
            gap: 0.5rem;
            padding: 0.5rem 1rem;
            border-radius: 8px;
            font-size: 0.9rem;
            font-weight: 600;
        }

        .badge-access {
            display: inline-flex;
            align-items: center;
            gap: 0.375rem;
            padding: 0.4rem 0.875rem;
            border-radius: 6px;
            font-size: 0.85rem;
            font-weight: 600;
        }

        .badge-status {
            padding: 0.5rem 1rem;
            border-radius: 20px;
            font-size: 0.9rem;
            font-weight: 600;
        }

        .badge-active {
            background: #d1fae5;
            color: #065f46;
        }

        .badge-inactive {
            background: #fee2e2;
            color: #991b1b;
        }

        /* Action Buttons */
        .btn-actions {
            display: flex;
            gap: 0.5rem;
        }

        .btn-action {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            width: 36px;
            height: 36px;
            border: none;
            border-radius: 8px;
            cursor: pointer;
            transition: all 0.3s ease;
            font-size: 0.95rem;
        }

        .btn-edit {
            background: #fef3c7;
            color: #92400e;
        }

        .btn-edit:hover {
            background: #fcd34d;
            transform: scale(1.1);
        }

        .btn-toggle {
            background: #dbeafe;
            color: #1e40af;
        }

        .btn-toggle:hover {
            background: #93c5fd;
            transform: scale(1.1);
        }

        .btn-delete {
            background: #fee2e2;
            color: #991b1b;
        }

        .btn-delete:hover {
            background: #fca5a5;
            transform: scale(1.1);
        }

        /* Dataset Name Styling */
        .dataset-name {
            font-weight: 600;
            color: var(--gray-800);
            margin-bottom: 0.25rem;
        }

        .dataset-table {
            font-size: 0.85rem;
            color: var(--secondary-color);
            display: flex;
            align-items: center;
            gap: 0.375rem;
        }

        /* Responsive */
        @media (max-width: 768px) {
            .top-actions {
                flex-direction: column;
                align-items: stretch;
            }

            .user-info-card {
                flex-direction: column;
                text-align: center;
            }

            .btn-back-admin,
            .btn-add-new {
                width: 100%;
                justify-content: center;
            }

            .table-card {
                padding: 1rem;
                overflow-x: auto;
            }

            .btn-actions {
                flex-direction: column;
            }
        }

        /* Loading Animation */
        .loading {
            display: inline-block;
            width: 20px;
            height: 20px;
            border: 3px solid rgba(255, 255, 255, 0.3);
            border-radius: 50%;
            border-top-color: var(--white);
            animation: spin 1s ease-in-out infinite;
        }

        @keyframes spin {
            to { transform: rotate(360deg); }
        }

        /* DataTables Custom */
        .dataTables_filter input {
            border: 2px solid var(--gray-200);
            border-radius: 8px;
            padding: 0.5rem 1rem;
            margin-left: 0.5rem;
        }

        .dataTables_filter input:focus {
            outline: none;
            border-color: var(--primary-color);
        }

        .dataTables_length select {
            border: 2px solid var(--gray-200);
            border-radius: 8px;
            padding: 0.375rem 0.75rem;
            margin: 0 0.5rem;
        }

        .page-item.active .page-link {
            background-color: var(--primary-color);
            border-color: var(--primary-color);
        }

        .page-link {
            color: var(--primary-color);
        }

        .page-link:hover {
            color: var(--primary-dark);
        }
    </style>
</head>
<body>
    <div class="container-fluid">
        <!-- Page Header -->
        <div class="page-header">
            <h1>
                <i class="fas fa-database"></i>
                จัดการข้อมูล Data Catalog
            </h1>
            <p class="subtitle">เพิ่ม แก้ไข จัดการ ข้อมูลชุดข้อมูลในระบบ</p>
        </div>

        <!-- Top Actions -->
        <div class="top-actions">
            <!-- User Info -->
            <div class="user-info-card">
                <div class="user-avatar">
                    <i class="fas fa-user-circle"></i>
                </div>
                <div class="user-details">
                    <strong>
                        <i class="fas fa-user"></i> 
                        <?= $user_info->m_fname ?> <?= $user_info->m_lname ?>
                    </strong>
                    <span class="user-role">
                        <i class="fas fa-shield-alt"></i> 
                        สิทธิ์: <?= $user_info->grant_system_ref_id ?>
                    </span>
                </div>
            </div>

            <!-- Action Buttons -->
            <div class="d-flex gap-2 flex-wrap">
                <a href="<?= base_url('System_admin') ?>" class="btn-back-admin">
                    <i class="fas fa-arrow-left"></i>
                    <span>กลับสู่หน้าจัดการเว็บ</span>
                </a>
                <a href="<?= base_url('data_catalog_manual/add') ?>" class="btn-add-new">
                    <i class="fas fa-plus-circle"></i>
                    <span>เพิ่มชุดข้อมูลใหม่</span>
                </a>
            </div>
        </div>

        <!-- Table Card -->
        <div class="table-card">
            <div class="table-header">
                <h5>
                    <i class="fas fa-list"></i>
                    รายการชุดข้อมูลทั้งหมด
                </h5>
            </div>

            <!-- DataTable -->
            <div class="table-responsive">
                <table id="datasetsTable" class="table table-hover" style="width:100%">
                    <thead>
                        <tr>
                            <th width="5%">ID</th>
                            <th width="25%">ชื่อชุดข้อมูล</th>
                            <th width="15%">หมวดหมู่</th>
                            <th width="10%">รูปแบบ</th>
                            <th width="10%">การเข้าถึง</th>
                            <th width="8%">จำนวน</th>
                            <th width="10%">สถานะ</th>
                            <th width="12%">จัดการ</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (!empty($datasets)): ?>
                            <?php foreach ($datasets as $dataset): ?>
                            <tr>
                                <td><strong>#<?= $dataset->id ?></strong></td>
                                <td>
                                    <div class="dataset-name"><?= $dataset->dataset_name ?></div>
                                    <?php if (!empty($dataset->table_name)): ?>
                                    <div class="dataset-table">
                                        <i class="fas fa-table"></i>
                                        <code><?= $dataset->table_name ?></code>
                                    </div>
                                    <?php endif; ?>
                                </td>
                                <td>
                                    <span class="badge-category" style="background: <?= $dataset->color ?>20; color: <?= $dataset->color ?>; border: 1px solid <?= $dataset->color ?>40;">
                                        <i class="<?= $dataset->icon ?>"></i>
                                        <?= $dataset->category_name ?>
                                    </span>
                                </td>
                                <td>
                                    <i class="fas fa-file-code"></i> 
                                    <?= $dataset->data_format ?>
                                </td>
                                <td>
                                    <?php if ($dataset->access_level == 'public'): ?>
                                        <span class="badge-access bg-success-subtle text-success">
                                            <i class="fas fa-globe"></i> เปิดเผย
                                        </span>
                                    <?php elseif ($dataset->access_level == 'restricted'): ?>
                                        <span class="badge-access bg-warning-subtle text-warning">
                                            <i class="fas fa-lock"></i> จำกัด
                                        </span>
                                    <?php else: ?>
                                        <span class="badge-access bg-danger-subtle text-danger">
                                            <i class="fas fa-lock"></i> ส่วนตัว
                                        </span>
                                    <?php endif; ?>
                                </td>
                                <td class="text-center">
                                    <strong><?= number_format($dataset->record_count) ?></strong>
                                </td>
                                <td>
                                    <span class="badge-status <?= $dataset->status == 1 ? 'badge-active' : 'badge-inactive' ?>">
                                        <?= $dataset->status == 1 ? '✓ เปิดใช้งาน' : '✕ ปิดใช้งาน' ?>
                                    </span>
                                </td>
                                <td>
                                    <div class="btn-actions">
                                        <button onclick="window.location.href='<?= base_url('data_catalog_manual/edit/'.$dataset->id) ?>'" 
                                                class="btn-action btn-edit" 
                                                title="แก้ไข">
                                            <i class="fas fa-edit"></i>
                                        </button>
                                        <button onclick="toggleStatus(<?= $dataset->id ?>)" 
                                                class="btn-action btn-toggle" 
                                                title="เปลี่ยนสถานะ">
                                            <i class="fas fa-power-off"></i>
                                        </button>
                                        <button onclick="deleteDataset(<?= $dataset->id ?>)" 
                                                class="btn-action btn-delete" 
                                                title="ลบ">
                                            <i class="fas fa-trash"></i>
                                        </button>
                                    </div>
                                </td>
                            </tr>
                            <?php endforeach; ?>
                        <?php else: ?>
                            <tr>
                                <td colspan="8" class="text-center py-5">
                                    <i class="fas fa-inbox fa-3x text-muted mb-3"></i>
                                    <h5 class="text-muted">ยังไม่มีข้อมูล</h5>
                                    <p class="text-muted">เริ่มต้นด้วยการเพิ่มชุดข้อมูลใหม่</p>
                                </td>
                            </tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <!-- Scripts -->
    <script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://cdn.datatables.net/1.13.7/js/jquery.dataTables.min.js"></script>
    <script src="https://cdn.datatables.net/1.13.7/js/dataTables.bootstrap5.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    
    <script>
    $(document).ready(function() {
        // Initialize DataTable
        $('#datasetsTable').DataTable({
            language: {
                url: '//cdn.datatables.net/plug-ins/1.13.7/i18n/th.json',
                search: "ค้นหา:",
                lengthMenu: "แสดง _MENU_ รายการ",
                info: "แสดง _START_ ถึง _END_ จาก _TOTAL_ รายการ",
                infoEmpty: "ไม่พบข้อมูล",
                infoFiltered: "(กรองจากทั้งหมด _MAX_ รายการ)",
                zeroRecords: "ไม่พบข้อมูลที่ค้นหา",
                paginate: {
                    first: "แรก",
                    last: "สุดท้าย",
                    next: "ถัดไป",
                    previous: "ก่อนหน้า"
                }
            },
            order: [[0, 'desc']],
            pageLength: 25,
            lengthMenu: [[10, 25, 50, 100, -1], [10, 25, 50, 100, "ทั้งหมด"]],
            responsive: true,
            dom: '<"row mb-3"<"col-sm-12 col-md-6"l><"col-sm-12 col-md-6"f>>rtip'
        });
    });

    // Toggle Status Function
    function toggleStatus(datasetId) {
        Swal.fire({
            title: 'เปลี่ยนสถานะ?',
            text: 'คุณต้องการเปลี่ยนสถานะชุดข้อมูลนี้หรือไม่?',
            icon: 'question',
            showCancelButton: true,
            confirmButtonColor: '#3b82f6',
            cancelButtonColor: '#6c757d',
            confirmButtonText: '<i class="fas fa-check"></i> ใช่, เปลี่ยนเลย',
            cancelButtonText: '<i class="fas fa-times"></i> ยกเลิก',
            customClass: {
                confirmButton: 'btn btn-primary px-4',
                cancelButton: 'btn btn-secondary px-4'
            },
            buttonsStyling: false
        }).then((result) => {
            if (result.isConfirmed) {
                // Show loading
                Swal.fire({
                    title: 'กำลังดำเนินการ...',
                    html: '<div class="loading"></div>',
                    allowOutsideClick: false,
                    showConfirmButton: false
                });

                $.ajax({
                    url: '<?= base_url("data_catalog_manual/toggle_status/") ?>' + datasetId,
                    type: 'POST',
                    dataType: 'json',
                    success: function(response) {
                        if (response.success) {
                            Swal.fire({
                                icon: 'success',
                                title: 'สำเร็จ!',
                                text: response.message,
                                timer: 1500,
                                showConfirmButton: false
                            }).then(() => {
                                location.reload();
                            });
                        } else {
                            Swal.fire({
                                icon: 'error',
                                title: 'ผิดพลาด!',
                                text: response.message
                            });
                        }
                    },
                    error: function() {
                        Swal.fire({
                            icon: 'error',
                            title: 'ผิดพลาด!',
                            text: 'เกิดข้อผิดพลาดในการเชื่อมต่อ'
                        });
                    }
                });
            }
        });
    }

    // Delete Dataset Function
    function deleteDataset(datasetId) {
        Swal.fire({
            title: 'ยืนยันการลบ?',
            html: '<p>คุณต้องการลบชุดข้อมูลนี้หรือไม่?</p><p class="text-danger mb-0"><strong>⚠️ การลบจะไม่สามารถกู้คืนได้!</strong></p>',
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#dc2626',
            cancelButtonColor: '#6c757d',
            confirmButtonText: '<i class="fas fa-trash"></i> ใช่, ลบเลย!',
            cancelButtonText: '<i class="fas fa-times"></i> ยกเลิก',
            customClass: {
                confirmButton: 'btn btn-danger px-4',
                cancelButton: 'btn btn-secondary px-4'
            },
            buttonsStyling: false
        }).then((result) => {
            if (result.isConfirmed) {
                // Show loading
                Swal.fire({
                    title: 'กำลังลบข้อมูล...',
                    html: '<div class="loading"></div>',
                    allowOutsideClick: false,
                    showConfirmButton: false
                });

                $.ajax({
                    url: '<?= base_url("data_catalog_manual/delete/") ?>' + datasetId,
                    type: 'POST',
                    dataType: 'json',
                    success: function(response) {
                        if (response.success) {
                            Swal.fire({
                                icon: 'success',
                                title: 'ลบสำเร็จ!',
                                text: response.message,
                                timer: 1500,
                                showConfirmButton: false
                            }).then(() => {
                                location.reload();
                            });
                        } else {
                            Swal.fire({
                                icon: 'error',
                                title: 'ผิดพลาด!',
                                text: response.message
                            });
                        }
                    },
                    error: function() {
                        Swal.fire({
                            icon: 'error',
                            title: 'ผิดพลาด!',
                            text: 'เกิดข้อผิดพลาดในการเชื่อมต่อ'
                        });
                    }
                });
            }
        });
    }
    </script>
</body>
</html>