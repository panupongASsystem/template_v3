<!DOCTYPE html>
<html lang="th">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo $page_title ?? 'รายงานสถิติการใช้งานเว็บไซต์'; ?></title>
    
    <!-- Bootstrap CSS -->
    <link href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap/5.3.0/css/bootstrap.min.css" rel="stylesheet">
    <!-- FontAwesome -->
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <!-- Chart.js -->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/Chart.js/3.9.1/chart.min.js"></script>
    
    <style>
        /* Print Styles */
        @media print {
            .no-print { display: none !important; }
            .print-break { page-break-before: always; }
            .print-avoid-break { page-break-inside: avoid; }
            
            body { 
                font-size: 12px; 
                line-height: 1.4;
                -webkit-print-color-adjust: exact;
                color-adjust: exact;
            }
            
            .container-fluid { padding: 0; }
            .card { border: 1px solid #dee2e6 !important; box-shadow: none !important; }
            .bg-primary { background-color: #667eea !important; }
            .text-primary { color: #667eea !important; }
            .bg-success { background-color: #06d6a0 !important; }
            .text-success { color: #06d6a0 !important; }
            .bg-info { background-color: #17a2b8 !important; }
            .text-info { color: #17a2b8 !important; }
            .bg-warning { background-color: #ffc107 !important; }
            .text-warning { color: #856404 !important; }
            
            /* Chart containers for print */
            .chart-container { 
                max-height: 300px; 
                margin: 15px 0;
            }
        }
        
        /* Screen Styles */
        @media screen {
            .print-only { display: none; }
            
            body { 
                background: #f8f9fa; 
                font-family: 'Sarabun', sans-serif;
            }
            
            .preview-container {
                max-width: 1200px;
                margin: 0 auto;
                padding: 20px;
            }
            
            .preview-paper {
                background: white;
                border-radius: 8px;
                box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
                padding: 40px;
                margin-bottom: 20px;
            }
            
            .chart-container {
                position: relative;
                height: 300px;
                margin: 20px 0;
            }
        }
        
        /* Common Styles */
        .report-header {
            text-align: center;
            margin-bottom: 30px;
            padding-bottom: 20px;
            border-bottom: 3px solid #667eea;
        }
        
        .report-title {
            font-size: 24px;
            font-weight: bold;
            color: #667eea;
            margin-bottom: 10px;
        }
        
        .report-subtitle {
            font-size: 16px;
            color: #6c757d;
            margin: 5px 0;
        }
        
        .stats-overview {
            margin: 30px 0;
        }
        
        .stats-card {
            text-align: center;
            padding: 20px;
            border-radius: 8px;
            margin-bottom: 15px;
            border: 1px solid #dee2e6;
        }
        
        .stats-icon {
            font-size: 2rem;
            margin-bottom: 10px;
        }
        
        .stats-number {
            font-size: 2rem;
            font-weight: bold;
            margin: 5px 0;
        }
        
        .stats-label {
            font-size: 0.9rem;
            color: #6c757d;
            margin: 0;
        }
        
        .section-title {
            font-size: 18px;
            font-weight: bold;
            color: #495057;
            margin: 30px 0 15px 0;
            padding-bottom: 8px;
            border-bottom: 2px solid #e9ecef;
        }
        
        .data-table {
            width: 100%;
            margin: 20px 0;
            border-collapse: collapse;
        }
        
        .data-table th,
        .data-table td {
            padding: 12px 8px;
            text-align: left;
            border-bottom: 1px solid #dee2e6;
        }
        
        .data-table th {
            background-color: #f8f9fa;
            font-weight: bold;
            color: #495057;
            border-top: 2px solid #667eea;
        }
        
        .data-table tr:hover {
            background-color: #f8f9fa;
        }
        
        .rank-badge {
            display: inline-block;
            width: 25px;
            height: 25px;
            line-height: 25px;
            text-align: center;
            border-radius: 50%;
            background: #667eea;
            color: white;
            font-weight: bold;
            font-size: 0.8rem;
        }
        
        .page-url {
            font-size: 0.85rem;
            color: #6c757d;
            font-style: italic;
            margin-top: 2px;
        }
        
        .insights-box {
            background: #f8f9fa;
            border-left: 4px solid #667eea;
            padding: 20px;
            margin: 20px 0;
            border-radius: 0 8px 8px 0;
        }
        
        .insight-item {
            margin: 10px 0;
            padding: 8px 0;
        }
        
        .recommendation-item {
            background: #e7f3ff;
            border-left: 4px solid #0066cc;
            padding: 10px 15px;
            margin: 8px 0;
            border-radius: 0 5px 5px 0;
        }
        
        .fallback-notice {
            background: #fff3cd;
            border: 1px solid #ffc107;
            border-radius: 8px;
            padding: 15px;
            margin: 20px 0;
            text-align: center;
        }
        
        .export-actions {
            position: fixed;
            top: 20px;
            right: 20px;
            z-index: 1000;
        }
        
        .btn-export {
            margin: 0 5px;
            box-shadow: 0 2px 4px rgba(0,0,0,0.1);
        }
        
        /* Responsive adjustments */
        @media (max-width: 768px) {
            .preview-container { padding: 10px; }
            .preview-paper { padding: 20px; }
            .stats-overview .col-md-3 { margin-bottom: 15px; }
            .export-actions { 
                position: relative; 
                top: auto; 
                right: auto; 
                margin-bottom: 20px;
                text-align: center;
            }
        }
    </style>
</head>
<body>
    <!-- Export Actions (แสดงเฉพาะหน้าจอ) -->
    <div class="export-actions no-print">
        <div class="btn-group" role="group">
            <button type="button" class="btn btn-primary btn-export" onclick="window.print()">
                <i class="fas fa-print me-1"></i>
                พิมพ์ / Save as PDF
            </button>
            
            <button type="button" class="btn btn-secondary btn-export" onclick="window.close()">
                <i class="fas fa-times me-1"></i>
                ปิด
            </button>
        </div>
    </div>

    <div class="preview-container">
        <div class="preview-paper">
            <!-- Report Header -->
            <div class="report-header">
                <div class="report-title">
                    <i class="fas fa-chart-line me-2"></i>
                    รายงานสรุปสถิติการใช้งานเว็บไซต์
                </div>
                <div class="report-subtitle">
                    <strong>หน่วยงาน:</strong> <?php echo htmlspecialchars($tenant_name ?? $tenant_code ?? 'ไม่ระบุ'); ?>
                </div>
                <div class="report-subtitle">
                    <strong>ช่วงเวลา:</strong> <?php echo htmlspecialchars($period_info ?? 'ไม่ระบุ'); ?>
                </div>
                <div class="report-subtitle">
                    <strong>วันที่ออกรายงาน:</strong> <?php echo $export_date ?? date('d/m/Y H:i:s'); ?>
                </div>
            </div>

            <?php if (isset($is_fallback_data) && $is_fallback_data): ?>
            <!-- Fallback Data Notice -->
            <div class="fallback-notice">
                <h5 class="text-warning mb-2">
                    <i class="fas fa-exclamation-triangle me-2"></i>
                    ข้อมูลไม่เพียงพอ
                </h5>
                <p class="mb-1">ไม่พบข้อมูลสถิติในช่วงเวลาที่เลือก อาจเป็นเพราะ:</p>
                <ul class="list-unstyled mb-0">
                    <li>• ยังไม่มีการเข้าชมเว็บไซต์ในช่วงนี้</li>
                    <li>• ระบบติดตามยังไม่ได้เปิดใช้งาน</li>
                    <li>• เลือกช่วงเวลาในอนาคต</li>
                </ul>
            </div>
            <?php endif; ?>

            <!-- Statistics Overview -->
            <div class="stats-overview">
                <h3 class="section-title">
                    <i class="fas fa-chart-bar me-2"></i>
                    สรุปสถิติ
                </h3>
                
                <div class="row">
                    <div class="col-md-3 col-6">
                        <div class="stats-card">
                            <div class="stats-icon text-primary">
                                <i class="fas fa-eye"></i>
                            </div>
                            <div class="stats-number text-primary">
                                <?php echo number_format($summary_data['overview']['total_pageviews'] ?? 0); ?>
                            </div>
                            <div class="stats-label">การเข้าชมทั้งหมด</div>
                        </div>
                    </div>
                    
                    <div class="col-md-3 col-6">
                        <div class="stats-card">
                            <div class="stats-icon text-success">
                                <i class="fas fa-users"></i>
                            </div>
                            <div class="stats-number text-success">
                                <?php echo number_format($summary_data['overview']['total_visitors'] ?? 0); ?>
                            </div>
                            <div class="stats-label">ผู้เยี่ยมชมทั้งหมด</div>
                        </div>
                    </div>
                    
                    <div class="col-md-3 col-6">
                        <div class="stats-card">
                            <div class="stats-icon text-info">
                                <i class="fas fa-chart-bar"></i>
                            </div>
                            <div class="stats-number text-info">
                                <?php echo number_format($summary_data['calculated_stats']['avg_pages_per_visitor'] ?? 0, 2); ?>
                            </div>
                            <div class="stats-label">เฉลี่ยหน้าต่อผู้เยี่ยมชม</div>
                        </div>
                    </div>
                    
                    <div class="col-md-3 col-6">
                        <div class="stats-card">
                            <div class="stats-icon text-warning">
                                <i class="fas fa-wifi"></i>
                            </div>
                            <div class="stats-number text-warning">
                                <?php echo number_format($summary_data['overview']['online_users'] ?? 0); ?>
                            </div>
                            <div class="stats-label">ผู้ใช้ออนไลน์</div>
                        </div>
                    </div>
                </div>
            </div>

            <?php if (isset($include_charts) && $include_charts && !empty($summary_data['daily_stats'])): ?>
            <!-- Daily Chart Section -->
            <div class="print-avoid-break">
                <h3 class="section-title">
                    <i class="fas fa-chart-line me-2"></i>
                    แนวโน้มการเข้าชมรายวัน
                </h3>
                <div class="chart-container">
                    <canvas id="dailyChart"></canvas>
                </div>
            </div>
            <?php endif; ?>

            <?php if (!empty($summary_data['top_domains'])): ?>
            <!-- ✅ Top Pages Section (แทน Top Domains) -->
            <div class="print-avoid-break">
                <h3 class="section-title">
                    <i class="fas fa-trophy me-2 text-warning"></i>
                    หน้าที่เข้าชมยอดนิยม
                </h3>
                
                <table class="data-table">
                    <thead>
                        <tr>
                            <th style="width: 10%;">อันดับ</th>
                            <th style="width: 50%;">ชื่อหน้า</th>
                            <th style="width: 20%;">การเข้าชม</th>
                            <th style="width: 20%;">ผู้เยี่ยมชม</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach (array_slice($summary_data['top_domains'], 0, 15) as $index => $page): ?>
                        <?php 
                            // ✅ รองรับทั้งข้อมูลแบบใหม่ (page) และแบบเก่า (domain)
                            $page_title = '';
                            $page_url = '';
                            
                            if (isset($page->page_title) && isset($page->page_url)) {
                                // ข้อมูลแบบใหม่ (page data)
                                $page_title = $page->page_title ?? 'ไม่ระบุ';
                                $page_url = $page->page_url ?? '';
                            } else {
                                // ข้อมูลแบบเก่า (domain data)
                                $page_title = $page->domain_name ?? 'ไม่ระบุ';
                                $page_url = '';
                            }
                        ?>
                        <tr>
                            <td>
                                <span class="rank-badge"><?php echo $index + 1; ?></span>
                            </td>
                            <td>
                                <div class="fw-bold">
                                    <i class="fas fa-<?php echo $page_url ? 'file-alt' : 'globe'; ?> me-2 text-primary"></i>
                                    <?php echo htmlspecialchars($page_title); ?>
                                </div>
                                <?php if ($page_url): ?>
                                <div class="page-url"><?php echo htmlspecialchars($page_url); ?></div>
                                <?php endif; ?>
                            </td>
                            <td class="fw-bold text-primary">
                                <?php echo number_format($page->total_views ?? 0); ?>
                            </td>
                            <td class="fw-bold text-success">
                                <?php echo number_format($page->unique_visitors ?? 0); ?>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
            <?php endif; ?>

            <?php if (!empty($summary_data['browser_stats'])): ?>
            <!-- Browser Stats Section -->
            <div class="print-break print-avoid-break">
                <h3 class="section-title">
                    <i class="fas fa-browser me-2 text-info"></i>
                    สถิติเบราว์เซอร์
                </h3>
                
                <table class="data-table">
                    <thead>
                        <tr>
                            <th style="width: 50%;">เบราว์เซอร์</th>
                            <th style="width: 25%;">จำนวนผู้ใช้</th>
                            <th style="width: 25%;">เปอร์เซ็นต์</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php 
                        $total_browsers = array_sum(array_column($summary_data['browser_stats'], 'count'));
                        foreach (array_slice($summary_data['browser_stats'], 0, 10) as $browser): 
                            $percentage = $total_browsers > 0 ? ($browser->count / $total_browsers) * 100 : 0;
                        ?>
                        <tr>
                            <td>
                                <i class="fab fa-<?php echo strtolower($browser->browser ?? 'globe'); ?> me-2 text-primary"></i>
                                <?php echo htmlspecialchars($browser->browser ?? 'Unknown'); ?>
                            </td>
                            <td class="fw-bold">
                                <?php echo number_format($browser->count ?? 0); ?>
                            </td>
                            <td>
                                <div class="d-flex align-items-center">
                                    <div class="progress me-2" style="width: 100px; height: 8px;">
                                        <div class="progress-bar bg-primary" style="width: <?php echo $percentage; ?>%"></div>
                                    </div>
                                    <span class="fw-bold"><?php echo number_format($percentage, 1); ?>%</span>
                                </div>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
            <?php endif; ?>

            <?php if (isset($include_recommendations) && $include_recommendations && !empty($summary_data['insights'])): ?>
            <!-- Insights Section -->
            <div class="print-break print-avoid-break">
                <h3 class="section-title">
                    <i class="fas fa-lightbulb me-2 text-warning"></i>
                    ข้อสรุปและคำแนะนำ
                </h3>
                
                <?php if (!empty($summary_data['insights']['main_summary'])): ?>
                <div class="insights-box">
                    <h5 class="text-primary mb-3">
                        <i class="fas fa-chart-pie me-2"></i>
                        ข้อสรุปหลัก
                    </h5>
                    <?php foreach ($summary_data['insights']['main_summary'] as $summary): ?>
                    <div class="insight-item">
                        <i class="fas fa-check-circle text-success me-2"></i>
                        <?php echo htmlspecialchars($summary); ?>
                    </div>
                    <?php endforeach; ?>
                </div>
                <?php endif; ?>

                <?php if (!empty($summary_data['insights']['recommendations'])): ?>
                <div class="mt-4">
                    <h5 class="text-primary mb-3">
                        <i class="fas fa-arrow-up me-2"></i>
                        คำแนะนำเพื่อการปรับปรุง
                    </h5>
                    <?php foreach ($summary_data['insights']['recommendations'] as $recommendation): ?>
                    <div class="recommendation-item">
                        <i class="fas fa-star text-warning me-2"></i>
                        <?php echo htmlspecialchars($recommendation); ?>
                    </div>
                    <?php endforeach; ?>
                </div>
                <?php endif; ?>
            </div>
            <?php endif; ?>

            <!-- Report Footer -->
            <div class="mt-5 pt-4 border-top text-center text-muted">
                <p class="mb-1">
                    <i class="fas fa-robot me-1"></i>
                    รายงานนี้สร้างโดยระบบอัตโนมัติ
                </p>
                <p class="mb-0">
                    <strong><?php echo htmlspecialchars($tenant_name ?? $tenant_code ?? 'ระบบ'); ?></strong> | 
                    <?php echo $export_date ?? date('d/m/Y H:i:s'); ?>
                </p>
            </div>
        </div>
    </div>

    <!-- JavaScript -->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap/5.3.0/js/bootstrap.bundle.min.js"></script>
    
    <script>
        // ✅ Chart Initialization
        document.addEventListener('DOMContentLoaded', function() {
            <?php if (isset($include_charts) && $include_charts && !empty($summary_data['daily_stats'])): ?>
            initializeDailyChart();
            <?php endif; ?>
        });

        <?php if (isset($include_charts) && $include_charts && !empty($summary_data['daily_stats'])): ?>
        function initializeDailyChart() {
            const ctx = document.getElementById('dailyChart');
            if (!ctx) return;

            const dailyStats = <?php echo json_encode($summary_data['daily_stats']); ?>;
            
            const labels = dailyStats.map(stat => {
                const date = new Date(stat.date);
                return date.toLocaleDateString('th-TH', { day: '2-digit', month: '2-digit' });
            });
            
            const pageviewsData = dailyStats.map(stat => parseInt(stat.pageviews) || 0);
            const visitorsData = dailyStats.map(stat => parseInt(stat.visitors) || 0);

            new Chart(ctx, {
                type: 'line',
                data: {
                    labels: labels,
                    datasets: [{
                        label: 'การเข้าชม',
                        data: pageviewsData,
                        borderColor: '#667eea',
                        backgroundColor: 'rgba(102, 126, 234, 0.1)',
                        tension: 0.4,
                        fill: true,
                        pointBackgroundColor: '#667eea',
                        pointBorderColor: '#ffffff',
                        pointBorderWidth: 2,
                        pointRadius: 4
                    }, {
                        label: 'ผู้เยี่ยมชม',
                        data: visitorsData,
                        borderColor: '#06d6a0',
                        backgroundColor: 'rgba(6, 214, 160, 0.1)',
                        tension: 0.4,
                        fill: true,
                        pointBackgroundColor: '#06d6a0',
                        pointBorderColor: '#ffffff',
                        pointBorderWidth: 2,
                        pointRadius: 4
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    plugins: {
                        legend: {
                            position: 'top',
                            labels: {
                                usePointStyle: true,
                                padding: 20
                            }
                        },
                        tooltip: {
                            backgroundColor: 'rgba(0, 0, 0, 0.8)',
                            titleColor: '#ffffff',
                            bodyColor: '#ffffff',
                            cornerRadius: 8
                        }
                    },
                    scales: {
                        x: {
                            grid: {
                                color: 'rgba(0, 0, 0, 0.05)'
                            }
                        },
                        y: {
                            beginAtZero: true,
                            grid: {
                                color: 'rgba(0, 0, 0, 0.05)'
                            }
                        }
                    },
                    interaction: {
                        intersect: false,
                        mode: 'index'
                    }
                }
            });
        }
        <?php endif; ?>

        // ✅ Export to CSV Function
        function exportToCSV() {
            const form = document.createElement('form');
            form.method = 'POST';
            form.action = '<?php echo site_url('System_reports/ajax_export_from_preview'); ?>';
            form.style.display = 'none';
            
            const reportData = {
                summary_data: <?php echo json_encode($summary_data ?? []); ?>,
                period_info: '<?php echo addslashes($period_info ?? ''); ?>',
                export_date: '<?php echo addslashes($export_date ?? date('d/m/Y H:i:s')); ?>',
                tenant_code: '<?php echo addslashes($tenant_code ?? ''); ?>',
                tenant_name: '<?php echo addslashes($tenant_name ?? ''); ?>',
                include_charts: <?php echo json_encode($include_charts ?? false); ?>,
                include_recommendations: <?php echo json_encode($include_recommendations ?? false); ?>
            };
            
            const exportTypeInput = document.createElement('input');
            exportTypeInput.type = 'hidden';
            exportTypeInput.name = 'export_type';
            exportTypeInput.value = 'csv';
            form.appendChild(exportTypeInput);
            
            const reportDataInput = document.createElement('input');
            reportDataInput.type = 'hidden';
            reportDataInput.name = 'report_data';
            reportDataInput.value = JSON.stringify(reportData);
            form.appendChild(reportDataInput);
            
            document.body.appendChild(form);
            form.submit();
            document.body.removeChild(form);
        }

        // ✅ Print optimization
        window.addEventListener('beforeprint', function() {
            // Adjust chart sizes for print
            <?php if (isset($include_charts) && $include_charts && !empty($summary_data['daily_stats'])): ?>
            const chartCanvas = document.getElementById('dailyChart');
            if (chartCanvas) {
                chartCanvas.style.maxHeight = '300px';
            }
            <?php endif; ?>
        });
    </script>
</body>
</html>