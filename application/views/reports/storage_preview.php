
<html lang="th">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= $report_title ?> - <?= $tenant_code ?></title>
    
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Font Awesome -->
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
    <!-- Print CSS -->
    <style>
        /* Base Styles */
        body {
            font-family: 'Sarabun', 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            font-size: 14px;
            line-height: 1.6;
            color: #333;
            background: #fff;
        }
        
        .container-fluid {
            max-width: 1200px;
            margin: 0 auto;
            padding: 20px;
        }
        
        /* Header Styles */
        .report-header {
            text-align: center;
            margin-bottom: 30px;
            padding-bottom: 20px;
            border-bottom: 3px solid #8bb6f7;
        }
        
        .report-title {
            font-size: 28px;
            font-weight: bold;
            color: #6ba3f5;
            margin-bottom: 10px;
        }
        
        .report-subtitle {
            font-size: 16px;
            color: #6c757d;
            margin: 5px 0;
        }
        
        /* Summary Cards */
        .summary-card {
            background: linear-gradient(135deg, #f8faff 0%, #f0f4ff 100%);
            border: 1px solid #e6f0ff;
            border-radius: 12px;
            padding: 20px;
            margin-bottom: 20px;
            box-shadow: 0 2px 8px rgba(107, 163, 245, 0.08);
        }
        
        .summary-item {
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 8px 0;
            border-bottom: 1px dotted #d6e5ff;
        }
        
        .summary-item:last-child {
            border-bottom: none;
        }
        
        .summary-label {
            font-weight: 500;
            color: #5a7aa0;
        }
        
        .summary-value {
            font-weight: bold;
            font-size: 16px;
        }
        
        .value-primary { color: #6ba3f5; }
        .value-success { color: #6dbf85; }
        .value-warning { color: #f5b76b; }
        .value-danger { color: #f57a7a; }
        
        /* Progress Bar */
        .usage-progress {
            margin: 20px 0;
        }
        
        .progress {
            height: 25px;
            border-radius: 12px;
            background-color: #f8faff;
            border: 1px solid #e6f0ff;
        }
        
        .progress-bar {
            border-radius: 12px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-weight: bold;
            color: white;
            text-shadow: 1px 1px 2px rgba(0,0,0,0.2);
        }
        
        /* Tables */
        .data-table {
            width: 100%;
            border-collapse: collapse;
            margin: 20px 0;
            background: white;
            border-radius: 8px;
            overflow: hidden;
            box-shadow: 0 2px 8px rgba(0,0,0,0.1);
        }
        
        .data-table th {
            background: linear-gradient(135deg, #8bb6f7 0%, #a5c7f8 100%);
            color: white;
            padding: 12px 15px;
            text-align: left;
            font-weight: 600;
            border-bottom: 2px solid #7ba8f5;
        }
        
        .data-table td {
            padding: 12px 15px;
            border-bottom: 1px solid #e6f0ff;
        }
        
        .data-table tbody tr:hover {
            background-color: #f8faff;
        }
        
        .data-table tbody tr:nth-child(even) {
            background-color: #f8faff;
        }
        
        /* Status Badges */
        .status-badge {
            padding: 4px 12px;
            border-radius: 20px;
            font-size: 12px;
            font-weight: bold;
            text-transform: uppercase;
        }
        
        .status-normal {
            background-color: #d1fae5;
            color: #065f46;
        }
        
        .status-warning {
            background-color: #fef3c7;
            color: #92400e;
        }
        
        .status-critical {
            background-color: #fee2e2;
            color: #991b1b;
        }
        
        /* Print Controls */
        .print-controls {
            position: fixed;
            top: 20px;
            right: 20px;
            z-index: 1000;
            background: white;
            padding: 15px;
            border-radius: 8px;
            box-shadow: 0 4px 12px rgba(0,0,0,0.15);
            border: 1px solid #dee2e6;
        }
        
        .btn-print {
            background: linear-gradient(135deg, #8bb6f7 0%, #a5c7f8 100%);
            border: none;
            color: white;
            padding: 10px 20px;
            border-radius: 6px;
            font-weight: 500;
            margin: 0 5px;
            cursor: pointer;
            transition: all 0.3s ease;
        }
        
        .btn-print:hover {
            background: linear-gradient(135deg, #7ba8f5 0%, #95b7f6 100%);
            transform: translateY(-1px);
            box-shadow: 0 4px 12px rgba(139, 182, 247, 0.3);
        }
        
        .btn-secondary {
            background: linear-gradient(135deg, #a8b8d0 0%, #b8c5d8 100%);
        }
        
        .btn-secondary:hover {
            background: linear-gradient(135deg, #98a8c0 0%, #a8b5c8 100%);
        }
        
        /* Charts placeholder */
        .chart-container {
            background: #f8faff;
            border: 2px dashed #d6e5ff;
            border-radius: 8px;
            padding: 40px;
            text-align: center;
            margin: 20px 0;
            color: #8bb6f7;
        }
        
        /* Print Styles */
        @media print {
            body {
                font-size: 12px;
                line-height: 1.4;
                margin: 0;
                padding: 0;
                background: white !important;
            }
            
            .container-fluid {
                max-width: none;
                margin: 0;
                padding: 15px;
            }
            
            .print-controls {
                display: none !important;
            }
            
            .summary-card,
            .data-table {
                break-inside: avoid;
                box-shadow: none;
                border: 1px solid #ccc;
            }
            
            .progress {
                background: #f0f0f0 !important;
                border: 1px solid #ccc;
            }
            
            .progress-bar {
                background: #666 !important;
                color: white !important;
            }
            
            .data-table th {
                background: #e9ecef !important;
                color: #000 !important;
            }
            
            h1, h2, h3, h4, h5, h6 {
                page-break-after: avoid;
            }
            
            .page-break {
                page-break-before: always;
            }
            
            .no-page-break {
                page-break-inside: avoid;
            }
        }
        
        /* Responsive */
        @media (max-width: 768px) {
            .container-fluid {
                padding: 10px;
            }
            
            .report-title {
                font-size: 24px;
            }
            
            .print-controls {
                position: relative;
                top: auto;
                right: auto;
                margin-bottom: 20px;
                text-align: center;
            }
            
            .data-table {
                font-size: 12px;
            }
            
            .data-table th,
            .data-table td {
                padding: 8px 10px;
            }
        }
        
        /* Animation */
        .fade-in {
            animation: fadeIn 0.6s ease-in;
        }
        
        @keyframes fadeIn {
            from { opacity: 0; transform: translateY(20px); }
            to { opacity: 1; transform: translateY(0); }
        }
    </style>
</head>
<body>
    <div class="container-fluid fade-in">
        <!-- Print Controls -->
        <div class="print-controls">
            <button type="button" class="btn-print" onclick="window.print()">
                <i class="fas fa-print me-2"></i>พิมพ์รายงาน/บันทึก PDF
            </button>
            
            <button type="button" class="btn-print btn-secondary" onclick="window.close()">
                <i class="fas fa-times me-2"></i>ปิด
            </button>
        </div>

        <!-- Header -->
        <div class="report-header">
            <div class="report-title"><?= $report_title ?></div>
            <div class="report-subtitle">หน่วยงาน: <?= $tenant_code ?></div>
            <div class="report-subtitle">โดเมน: <?= $current_domain ?></div>
            <div class="report-subtitle">วันที่ออกรายงาน: <?= $export_date ?></div>
        </div>

        <!-- สรุปผลหลัก -->
        <div class="summary-card no-page-break">
            <h3 class="mb-3">
                <i class="fas fa-chart-pie me-2 text-primary"></i>
                สรุปการใช้พื้นที่จัดเก็บข้อมูล
            </h3>
            
            <div class="row">
                <div class="col-md-6">
                    <div class="summary-item">
                        <span class="summary-label">
                            <i class="fas fa-database me-1"></i>
                            ขนาดทั้งหมด :
                        </span>
                        <span class="summary-value value-primary">
                            <?= number_format($storage_info['server_storage'] ?? 100, 2) ?> GB
                        </span>
                    </div>
                    
                    <div class="summary-item">
                        <span class="summary-label">
                            <i class="fas fa-hdd me-1"></i>
                            พื้นที่ใช้งานแล้ว:
                        </span>
                        <span class="summary-value value-warning">
                            <?= number_format($storage_info['server_current'] ?? 0, 3) ?> GB
                        </span>
                    </div>
                    
                    <div class="summary-item">
                        <span class="summary-label">
                            <i class="fas fa-chart-line me-1"></i>
                            พื้นที่ว่าง:
                        </span>
                        <span class="summary-value value-success">
                            <?= number_format($storage_info['free_space'] ?? 0, 3) ?> GB
                        </span>
                    </div>
                </div>
                
                <div class="col-md-6">
                    <div class="summary-item">
                        <span class="summary-label">
                            <i class="fas fa-percentage me-1"></i>
                            เปอร์เซ็นต์การใช้งาน:
                        </span>
                        <span class="summary-value value-<?= ($storage_info['percentage_used'] ?? 0) > 80 ? 'danger' : (($storage_info['percentage_used'] ?? 0) > 60 ? 'warning' : 'success') ?>">
                            <?= number_format($storage_info['percentage_used'] ?? 0, 2) ?>%
                        </span>
                    </div>
                    
                    <div class="summary-item">
                        <span class="summary-label">
                            <i class="fas fa-server me-1"></i>
                            สถานะระบบ:
                        </span>
                        <span class="summary-value">
                            <?php 
                            $status = $storage_info['status'] ?? 'normal';
                            $status_text = $status == 'critical' ? 'วิกฤต' : ($status == 'warning' ? 'เตือน' : 'ปกติ');
                            $status_class = $status == 'critical' ? 'status-critical' : ($status == 'warning' ? 'status-warning' : 'status-normal');
                            ?>
                            <span class="status-badge <?= $status_class ?>">
                                <i class="fas fa-circle me-1"></i><?= $status_text ?>
                            </span>
                        </span>
                    </div>
                    
                    <div class="summary-item">
                        <span class="summary-label">
                            <i class="fas fa-clock me-1"></i>
                            อัปเดตล่าสุด:
                        </span>
                        <span class="summary-value">
                            <?php 
                            $last_updated = $storage_info['last_updated'] ?? null;
                            if ($last_updated) {
                                echo date('d/m/Y H:i:s', strtotime($last_updated));
                            } else {
                                echo '<span class="text-muted">ไม่ทราบ</span>';
                            }
                            ?>
                        </span>
                    </div>
                </div>
            </div>
            
            <!-- Progress Bar -->
            <div class="usage-progress">
                <h5 class="mb-3">การใช้งานพื้นที่</h5>
                <div class="progress">
                    <?php
                    $percentage = $storage_info['percentage_used'] ?? 0;
                    $progress_class = $percentage > 80 ? 'bg-danger' : ($percentage > 60 ? 'bg-warning' : 'bg-success');
                    ?>
                    <div class="progress-bar <?= $progress_class ?>" 
                         style="width: <?= $percentage ?>%">
                        <?= number_format($percentage, 1) ?>%
                    </div>
                </div>
                <div class="d-flex justify-content-between mt-2 small text-muted">
                    <span>0%</span>
                    <span>ใช้งาน: <?= number_format($storage_info['server_current'] ?? 0, 2) ?> GB</span>
                    <span>100%</span>
                </div>
            </div>
        </div>

        <!-- สถิติไฟล์ -->
        <div class="summary-card no-page-break">
            <h3 class="mb-3">
                <i class="fas fa-files me-2 text-success"></i>
                สถิติไฟล์ในระบบ
            </h3>
            
            <div class="row">
                <div class="col-md-3 col-sm-6">
                    <div class="summary-item">
                        <span class="summary-label">
                            <i class="fas fa-file me-1"></i>
                            ไฟล์ทั้งหมด:
                        </span>
                        <span class="summary-value value-primary">
                            <?= number_format($file_stats['total_files'] ?? 0) ?> ไฟล์
                        </span>
                    </div>
                </div>
                
                <div class="col-md-3 col-sm-6">
                    <div class="summary-item">
                        <span class="summary-label">
                            <i class="fas fa-image me-1"></i>
                            ไฟล์รูปภาพ:
                        </span>
                        <span class="summary-value value-success">
                            <?= number_format($file_stats['image_files'] ?? 0) ?> ไฟล์
                        </span>
                    </div>
                </div>
                
                <div class="col-md-3 col-sm-6">
                    <div class="summary-item">
                        <span class="summary-label">
                            <i class="fas fa-file-alt me-1"></i>
                            ไฟล์เอกสาร:
                        </span>
                        <span class="summary-value value-warning">
                            <?= number_format($file_stats['document_files'] ?? 0) ?> ไฟล์
                        </span>
                    </div>
                </div>
                
                <div class="col-md-3 col-sm-6">
                    <div class="summary-item">
                        <span class="summary-label">
                            <i class="fas fa-file-code me-1"></i>
                            ไฟล์อื่นๆ:
                        </span>
                        <span class="summary-value">
                            <?= number_format($file_stats['other_files'] ?? 0) ?> ไฟล์
                        </span>
                    </div>
                </div>
            </div>
            
            <?php if (($file_stats['total_files'] ?? 0) > 0): ?>
            <div class="mt-3">
                <small class="text-muted">
                    <i class="fas fa-calculator me-1"></i>
                    ขนาดไฟล์เฉลี่ย: 
                    <?php
                    $avg_size = ($storage_info['server_current'] * 1024) / $file_stats['total_files']; // KB per file
                    if ($avg_size >= 1024) {
                        echo number_format($avg_size / 1024, 2) . ' MB/ไฟล์';
                    } else {
                        echo number_format($avg_size, 2) . ' KB/ไฟล์';
                    }
                    ?>
                </small>
            </div>
            <?php endif; ?>
        </div>

        <!-- ประวัติการใช้งาน -->
		
		<!-- 
        <?php if (!empty($storage_history) && is_array($storage_history)): ?>
        <div class="page-break"></div>
        <div class="summary-card">
            <h3 class="mb-3">
                <i class="fas fa-history me-2 text-info"></i>
                ประวัติการใช้งานพื้นที่ (10 ครั้งล่าสุด)
            </h3>
            
            <table class="data-table">
                <thead>
                    <tr>
                        <th>วันที่</th>
                        <th>การใช้งาน (GB)</th>
                        <th>เปอร์เซ็นต์</th>
                        <th>การเปลี่ยนแปลง</th>
                        <th>สถานะ</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach (array_slice($storage_history, 0, 10) as $index => $history): ?>
                    <?php if (is_object($history)): ?>
                    <tr>
                        <td>
                            <?php 
                            $date_field = isset($history->recorded_at) ? $history->recorded_at : 
                                         (isset($history->created_at) ? $history->created_at : 
                                         (isset($history->updated_at) ? $history->updated_at : date('Y-m-d H:i:s')));
                            echo date('d/m/Y H:i', strtotime($date_field));
                            ?>
                        </td>
                        <td>
                            <?php 
                            $usage = isset($history->usage_gb) ? $history->usage_gb : 
                                    (isset($history->server_current) ? $history->server_current : 0);
                            echo number_format($usage, 3);
                            ?>
                        </td>
                        <td>
                            <?php 
                            $percentage = isset($history->percentage_used) ? $history->percentage_used : 
                                         (isset($history->usage_percentage) ? $history->usage_percentage : 0);
                            echo number_format($percentage, 2);
                            ?>%
                        </td>
                        <td>
                            <?php
                            if ($index < count($storage_history) - 1 && is_object($storage_history[$index + 1])) {
                                $current_usage = isset($history->usage_gb) ? $history->usage_gb : 
                                               (isset($history->server_current) ? $history->server_current : 0);
                                $prev_obj = $storage_history[$index + 1];
                                $prev_usage = isset($prev_obj->usage_gb) ? $prev_obj->usage_gb : 
                                             (isset($prev_obj->server_current) ? $prev_obj->server_current : 0);
                                $change = $current_usage - $prev_usage;
                                
                                if ($change > 0.001) {
                                    echo '<span class="text-danger">+' . number_format($change, 3) . ' GB</span>';
                                } elseif ($change < -0.001) {
                                    echo '<span class="text-success">' . number_format($change, 3) . ' GB</span>';
                                } else {
                                    echo '<span class="text-muted">ไม่เปลี่ยนแปลง</span>';
                                }
                            } else {
                                echo '<span class="text-muted">-</span>';
                            }
                            ?>
                        </td>
                        <td>
                            <?php
                            $hist_status = isset($history->status) ? $history->status : 'normal';
                            $hist_status_text = $hist_status == 'critical' ? 'วิกฤต' : ($hist_status == 'warning' ? 'เตือน' : 'ปกติ');
                            $hist_status_class = $hist_status == 'critical' ? 'status-critical' : ($hist_status == 'warning' ? 'status-warning' : 'status-normal');
                            ?>
                            <span class="status-badge <?= $hist_status_class ?>"><?= $hist_status_text ?></span>
                        </td>
                    </tr>
                    <?php endif; ?>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
        <?php else: ?>
        <div class="summary-card">
            <h3 class="mb-3">
                <i class="fas fa-history me-2 text-info"></i>
                ประวัติการใช้งานพื้นที่
            </h3>
            <div class="text-center py-4">
                <i class="fas fa-info-circle fa-3x text-muted mb-3"></i>
                <p class="text-muted">ไม่มีข้อมูลประวัติการใช้งาน</p>
            </div>
        </div>
        <?php endif; ?>

         -->

        <!-- การใช้งานตามประเภทไฟล์ -->
		
		 <!--
        <?php if (!empty($storage_by_type) && is_array($storage_by_type)): ?>
        <div class="summary-card">
            <h3 class="mb-3">
                <i class="fas fa-chart-bar me-2 text-warning"></i>
                การใช้งานตามประเภทไฟล์
            </h3>
            
            <table class="data-table">
                <thead>
                    <tr>
                        <th>ประเภทไฟล์</th>
                        <th>จำนวนไฟล์</th>
                        <th>ขนาดรวม (MB)</th>
                        <th>เปอร์เซ็นต์</th>
                        <th>ขนาดเฉลี่ย</th>
                    </tr>
                </thead>
                <tbody>
                    <?php 
                    // คำนวณขนาดรวมอย่างปลอดภัย
                    $total_size = 0;
                    foreach ($storage_by_type as $type) {
                        if (is_object($type) && isset($type->total_size_mb)) {
                            $total_size += $type->total_size_mb;
                        }
                    }
                    
                    foreach ($storage_by_type as $type): 
                        if (!is_object($type)) continue;
                    ?>
                    <tr>
                        <td>
                            <?php 
                            $file_type = isset($type->file_type) ? $type->file_type : 'unknown';
                            $icon_class = $file_type == 'image' ? 'image' : ($file_type == 'document' ? 'file-alt' : 'file');
                            $type_name = ucfirst($file_type);
                            ?>
                            <i class="fas fa-<?= $icon_class ?> me-2"></i>
                            <?= $type_name ?>
                        </td>
                        <td><?= number_format(isset($type->file_count) ? $type->file_count : 0) ?></td>
                        <td><?= number_format(isset($type->total_size_mb) ? $type->total_size_mb : 0, 2) ?></td>
                        <td>
                            <?php 
                            $size_mb = isset($type->total_size_mb) ? $type->total_size_mb : 0;
                            echo $total_size > 0 ? number_format(($size_mb / $total_size) * 100, 1) : 0;
                            ?>%
                        </td>
                        <td>
                            <?php 
                            $file_count = isset($type->file_count) ? $type->file_count : 0;
                            $size_mb = isset($type->total_size_mb) ? $type->total_size_mb : 0;
                            echo $file_count > 0 ? number_format($size_mb / $file_count, 2) : 0;
                            ?> MB
                        </td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
        <?php else: ?>
        <div class="summary-card">
            <h3 class="mb-3">
                <i class="fas fa-chart-bar me-2 text-warning"></i>
                การใช้งานตามประเภทไฟล์
            </h3>
            <div class="text-center py-4">
                <i class="fas fa-chart-bar fa-3x text-muted mb-3"></i>
                <p class="text-muted">ไม่มีข้อมูลการใช้งานตามประเภทไฟล์</p>
            </div>
        </div>
        <?php endif; ?>


-->
        <!-- ข้อมูลเทคนิค -->
		
		 <!--
        <div class="summary-card">
            <h3 class="mb-3">
                <i class="fas fa-cogs me-2 text-secondary"></i>
                ข้อมูลเทคนิคของระบบ
            </h3>
            
            <div class="row">
                <div class="col-md-6">
                    <h5>การตั้งค่าระบบ</h5>
                    <div class="summary-item">
                        <span class="summary-label">Tenant Code:</span>
                        <span class="summary-value"><code><?= $tenant_code ?></code></span>
                    </div>
                    <div class="summary-item">
                        <span class="summary-label">โดเมนปัจจุบัน:</span>
                        <span class="summary-value"><code><?= $current_domain ?></code></span>
                    </div>
                    <div class="summary-item">
                        <span class="summary-label">ระบบฐานข้อมูล:</span>
                        <span class="summary-value">MySQL/MariaDB</span>
                    </div>
                </div>
                
                <div class="col-md-6">
                    <h5>การทำงานของระบบ</h5>
                    <div class="summary-item">
                        <span class="summary-label">อัปเดตอัตโนมัติ:</span>
                        <span class="summary-value text-success">เปิดใช้งาน</span>
                    </div>
                    <div class="summary-item">
                        <span class="summary-label">ตรวจสอบทุก:</span>
                        <span class="summary-value">30 นาที</span>
                    </div>
                    <div class="summary-item">
                        <span class="summary-label">โฟลเดอร์ตรวจสอบ:</span>
                        <span class="summary-value"><code>httpdocs/</code></span>
                    </div>
                </div>
            </div>
        </div>
        -->
        <!-- Footer -->
        <div class="text-center mt-4 py-3 border-top">
            <small class="text-muted">
                รายงานนี้สร้างโดยระบบอัตโนมัติ | 
                วันที่: <?= $export_date ?> | 
                Domain: <?= $tenant_code ?>
            </small>
        </div>
    </div>

    <!-- JavaScript -->
    <script>
        // ✅ แก้ไขปัญหา Promise Error
        window.addEventListener('unhandledrejection', function(event) {
            // ซ่อน error ที่เกิดจาก browser extension
            if (event.reason && event.reason.message && 
                event.reason.message.includes('message channel closed')) {
                event.preventDefault();
                return;
            }
        });

        // ฟังก์ชันสำหรับดาวน์โหลด PDF
        function downloadPDF() {
            try {
                // Hide print controls temporarily
                const controls = document.querySelector('.print-controls');
                if (controls) {
                    controls.style.display = 'none';
                }
                
                // Use browser's print to PDF functionality
                window.print();
                
                // Show controls again
                setTimeout(() => {
                    if (controls) {
                        controls.style.display = 'block';
                    }
                }, 100);
            } catch (error) {
                console.warn('Print function error:', error);
            }
        }
        
        // เพิ่ม animation เมื่อโหลดหน้า
        document.addEventListener('DOMContentLoaded', function() {
            try {
                // Progress bar animation
                const progressBar = document.querySelector('.progress-bar');
                if (progressBar) {
                    const targetWidth = progressBar.style.width;
                    progressBar.style.width = '0%';
                    setTimeout(() => {
                        progressBar.style.transition = 'width 1.5s ease-in-out';
                        progressBar.style.width = targetWidth;
                    }, 300);
                }
                
                // Card fade-in animation
                const cards = document.querySelectorAll('.summary-card');
                cards.forEach((card, index) => {
                    card.style.opacity = '0';
                    card.style.transform = 'translateY(20px)';
                    setTimeout(() => {
                        card.style.transition = 'all 0.5s ease-out';
                        card.style.opacity = '1';
                        card.style.transform = 'translateY(0)';
                    }, 100 * index);
                });
            } catch (error) {
                console.warn('Animation error:', error);
            }
        });
        
        // Keyboard shortcuts
        document.addEventListener('keydown', function(e) {
            try {
                if (e.ctrlKey || e.metaKey) {
                    switch(e.key) {
                        case 'p':
                            e.preventDefault();
                            window.print();
                            break;
                        case 'w':
                            e.preventDefault();
                            if (window.opener) {
                                window.close();
                            }
                            break;
                    }
                }
            } catch (error) {
                console.warn('Keyboard shortcut error:', error);
            }
        });
        
        // Auto-update timestamp
        try {
            setInterval(function() {
                const now = new Date();
                const timestamp = now.toLocaleString('th-TH');
                document.title = '<?= $report_title ?> - อัปเดต: ' + timestamp;
            }, 60000);
        } catch (error) {
            console.warn('Auto-update error:', error);
        }

        // ✅ เพิ่มการป้องกัน Extension Error
        window.addEventListener('error', function(event) {
            // ซ่อน error ที่เกิดจาก browser extension
            if (event.message && (
                event.message.includes('Extension context invalidated') ||
                event.message.includes('message channel closed') ||
                event.message.includes('Script error')
            )) {
                event.preventDefault();
                return true;
            }
        });

        // ✅ การป้องกัน Console Error
        const originalConsoleError = console.error;
        console.error = function(...args) {
            const message = args.join(' ');
            if (message.includes('message channel closed') || 
                message.includes('Extension context')) {
                return; // ไม่แสดง error นี้
            }
            originalConsoleError.apply(console, args);
        };
    </script>
</body>
</html>