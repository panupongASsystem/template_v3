<!DOCTYPE html>
<html lang="th">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= $page_title ?></title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <style>
        @media print {
            .no-print { display: none !important; }
            .page-break { page-break-before: always; }
            body { font-size: 12px; }
        }
        
        .report-header {
            background: linear-gradient(135deg, #dc2626 0%, #b91c1c 100%);
            color: white;
            padding: 2rem;
            border-radius: 10px;
            margin-bottom: 2rem;
            text-align: center;
        }
        
        .summary-card {
            background: #fef2f2;
            border: 1px solid #fecaca;
            border-radius: 8px;
            padding: 1.5rem;
            margin-bottom: 2rem;
        }
        
        .case-item {
            background: #f8fafc;
            border-left: 4px solid #e5e7eb;
            padding: 1rem;
            margin-bottom: 1rem;
            border-radius: 0 8px 8px 0;
        }
        
        .case-item.critical { 
            border-left-color: #dc2626; 
            background: linear-gradient(135deg, #fef2f2 0%, #ffffff 100%);
        }
        
        .case-item.danger { 
            border-left-color: #f59e0b; 
            background: linear-gradient(135deg, #fffbeb 0%, #ffffff 100%);
        }
        
        .case-item.warning { 
            border-left-color: #10b981; 
            background: linear-gradient(135deg, #f0fdf4 0%, #ffffff 100%);
        }
        
        .section-title {
            border-bottom: 2px solid;
            padding-bottom: 0.5rem;
            margin: 2rem 0 1rem 0;
            font-weight: bold;
        }
        
        .section-title.critical { color: #dc2626; border-color: #dc2626; }
        .section-title.danger { color: #f59e0b; border-color: #f59e0b; }
        .section-title.warning { color: #10b981; border-color: #10b981; }
        
        .stats-badge {
            display: inline-block;
            padding: 0.5rem 1rem;
            border-radius: 20px;
            font-weight: bold;
            margin: 0.25rem;
        }
        
        .stats-badge.critical { background: #fee2e2; color: #dc2626; }
        .stats-badge.danger { background: #fef3c7; color: #f59e0b; }
        .stats-badge.warning { background: #d1fae5; color: #10b981; }
        
        .recommendations {
            background: #f0f9ff;
            border: 1px solid #bae6fd;
            border-radius: 8px;
            padding: 1.5rem;
            margin-top: 2rem;
        }
    </style>
</head>
<body>
    <!-- Print Controls -->
    <div class="no-print mb-3">
        <div class="container-fluid">
            <div class="d-flex justify-content-between align-items-center">
                <button class="btn btn-secondary" onclick="window.close()">
                    <i class="fas fa-times"></i> ปิด
                </button>
                <div>
                    <button class="btn btn-primary" onclick="window.print()">
                        <i class="fas fa-print"></i> พิมพ์รายงาน/PDF
                    </button>
                    
                    <button class="btn btn-info" onclick="downloadCSV()">
                        <i class="fas fa-file-excel"></i> ดาวน์โหลด CSV
                    </button>
                </div>
            </div>
        </div>
        <hr>
    </div>

    <!-- Report Content -->
    <div class="container-fluid">
        <!-- Header -->
        <div class="report-header">
            <h1><i class="fas fa-exclamation-triangle"></i> รายงาน Case ที่ไม่มีการอัพเดท</h1>
            <h3><?= $tenant_name ?></h3>
            <p class="mb-0">วันที่ออกรายงาน: <?= $export_date ?></p>
        </div>

        <!-- Summary -->
        <div class="summary-card">
            <h3 class="text-danger mb-3">
                <i class="fas fa-chart-pie"></i> สรุปภาพรวม
            </h3>
            <div class="row">
                <div class="col-md-6">
                    <h4>จำนวน Case ทั้งหมดที่ค้าง: <span class="text-danger"><?= number_format($total_alerts) ?></span> รายการ</h4>
                </div>
                <div class="col-md-6">
                    <div class="text-end">
                        <span class="stats-badge critical">
                            <i class="fas fa-fire"></i> วิกฤติ: <?= count($critical_cases) ?>
                        </span>
                        <span class="stats-badge danger">
                            <i class="fas fa-exclamation-triangle"></i> เร่งด่วน: <?= count($danger_cases) ?>
                        </span>
                        <span class="stats-badge warning">
                            <i class="fas fa-clock"></i> ติดตาม: <?= count($warning_cases) ?>
                        </span>
                    </div>
                </div>
            </div>
        </div>

        <!-- Critical Cases -->
        <?php if (!empty($critical_cases)): ?>
            <h2 class="section-title critical">
                <i class="fas fa-fire"></i> Case วิกฤติ (ค้าง 14+ วัน) - <?= count($critical_cases) ?> รายการ
            </h2>
            <?php foreach ($critical_cases as $case): ?>
                <div class="case-item critical">
                    <div class="row">
                        <div class="col-md-8">
                            <h5 class="text-danger">
                                <i class="fas fa-hashtag"></i><?= htmlspecialchars($case['id']) ?> - 
                                <?= htmlspecialchars($case['topic']) ?>
                            </h5>
                            <p class="mb-1">
                                <strong>สถานะ:</strong> <?= htmlspecialchars($case['status']) ?>
                            </p>
                            <p class="mb-0">
                                <strong>วันที่แจ้ง:</strong> <?= date('d/m/Y', strtotime($case['date'])) ?>
                            </p>
                        </div>
                        <div class="col-md-4 text-end">
                            <span class="badge bg-danger fs-6">
                                <i class="fas fa-clock"></i> ค้าง <?= $case['days'] ?> วัน
                            </span>
                        </div>
                    </div>
                </div>
            <?php endforeach; ?>
        <?php endif; ?>

        <!-- Danger Cases -->
        <?php if (!empty($danger_cases)): ?>
            <h2 class="section-title danger">
                <i class="fas fa-exclamation-triangle"></i> Case เร่งด่วน (ค้าง 7-13 วัน) - <?= count($danger_cases) ?> รายการ
            </h2>
            <?php foreach ($danger_cases as $case): ?>
                <div class="case-item danger">
                    <div class="row">
                        <div class="col-md-8">
                            <h5 class="text-warning">
                                <i class="fas fa-hashtag"></i><?= htmlspecialchars($case['id']) ?> - 
                                <?= htmlspecialchars($case['topic']) ?>
                            </h5>
                            <p class="mb-1">
                                <strong>สถานะ:</strong> <?= htmlspecialchars($case['status']) ?>
                            </p>
                            <p class="mb-0">
                                <strong>วันที่แจ้ง:</strong> <?= date('d/m/Y', strtotime($case['date'])) ?>
                            </p>
                        </div>
                        <div class="col-md-4 text-end">
                            <span class="badge bg-warning fs-6">
                                <i class="fas fa-clock"></i> ค้าง <?= $case['days'] ?> วัน
                            </span>
                        </div>
                    </div>
                </div>
            <?php endforeach; ?>
        <?php endif; ?>

        <!-- Warning Cases -->
        <?php if (!empty($warning_cases)): ?>
            <h2 class="section-title warning">
                <i class="fas fa-clock"></i> Case ติดตาม (ค้าง 3-6 วัน) - <?= count($warning_cases) ?> รายการ
            </h2>
            <?php foreach ($warning_cases as $case): ?>
                <div class="case-item warning">
                    <div class="row">
                        <div class="col-md-8">
                            <h5 class="text-success">
                                <i class="fas fa-hashtag"></i><?= htmlspecialchars($case['id']) ?> - 
                                <?= htmlspecialchars($case['topic']) ?>
                            </h5>
                            <p class="mb-1">
                                <strong>สถานะ:</strong> <?= htmlspecialchars($case['status']) ?>
                            </p>
                            <p class="mb-0">
                                <strong>วันที่แจ้ง:</strong> <?= date('d/m/Y', strtotime($case['date'])) ?>
                            </p>
                        </div>
                        <div class="col-md-4 text-end">
                            <span class="badge bg-success fs-6">
                                <i class="fas fa-clock"></i> ค้าง <?= $case['days'] ?> วัน
                            </span>
                        </div>
                    </div>
                </div>
            <?php endforeach; ?>
        <?php endif; ?>

        <!-- No Data -->
        <?php if ($total_alerts == 0): ?>
            <div class="text-center py-5">
                <i class="fas fa-check-circle text-success fa-4x mb-3"></i>
                <h3 class="text-success">ยอดเยี่ยม!</h3>
                <p class="text-muted">ไม่มี Case ที่ค้างนาน ทุกเรื่องอยู่ในกำหนดเวลา</p>
            </div>
        <?php endif; ?>

        <!-- Recommendations -->
        <?php if ($total_alerts > 0): ?>
            <div class="recommendations">
                <h3 class="text-primary mb-3">
                    <i class="fas fa-lightbulb"></i> คำแนะนำในการดำเนินงาน
                </h3>
                <div class="row">
                    <div class="col-md-6">
                        <h5><i class="fas fa-fire text-danger"></i> Case วิกฤติ (14+ วัน):</h5>
                        <p>ต้องดำเนินการทันทีเป็นลำดับแรก และรายงานผู้บริหาร</p>
                        
                        <h5><i class="fas fa-exclamation-triangle text-warning"></i> Case เร่งด่วน (7-13 วัน):</h5>
                        <p>วางแผนดำเนินการภายในสัปดาห์นี้</p>
                    </div>
                    <div class="col-md-6">
                        <h5><i class="fas fa-clock text-success"></i> Case ติดตาม (3-6 วัน):</h5>
                        <p>ติดตามให้ไม่เลื่อนไปเป็น Case เร่งด่วน</p>
                        
                        <h5><i class="fas fa-users text-info"></i> การบริหารจัดการ:</h5>
                        <p>ควรมีการประชุมทีมเพื่อจัดลำดับความสำคัญ</p>
                    </div>
                </div>
            </div>
        <?php endif; ?>

        <!-- Footer -->
        <div class="text-center mt-4 pt-3 border-top">
            <small class="text-muted">
                รายงานนี้สร้างโดยระบบอัตโนมัติ | <?= $tenant_name ?> | <?= $export_date ?>
            </small>
        </div>
    </div>

    <script>
        // ฟังก์ชันสำหรับดาวน์โหลด PDF
        function downloadPDF() {
            const form = document.createElement('form');
            form.method = 'POST';
            form.action = '<?= site_url("System_reports/export_alerts_pdf_download") ?>';
            form.target = '_blank';
            
            const dataInput = document.createElement('input');
            dataInput.type = 'hidden';
            dataInput.name = 'alert_data';
            dataInput.value = '<?= json_encode($alert_data) ?>';
            form.appendChild(dataInput);
            
            document.body.appendChild(form);
            form.submit();
            document.body.removeChild(form);
        }

        // ฟังก์ชันสำหรับดาวน์โหลด CSV
        function downloadCSV() {
            const form = document.createElement('form');
            form.method = 'POST';
            form.action = '<?= site_url("System_reports/export_alerts_excel") ?>';
            form.target = '_blank';
            
            const dataInput = document.createElement('input');
            dataInput.type = 'hidden';
            dataInput.name = 'alert_data';
            dataInput.value = '<?= json_encode($alert_data) ?>';
            form.appendChild(dataInput);
            
            document.body.appendChild(form);
            form.submit();
            document.body.removeChild(form);
        }
    </script>
</body>
</html>