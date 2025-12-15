<!DOCTYPE html>
<html lang="th">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="description" content="สถิติและรายงาน - <?php echo isset($org['fname']) ? $org['fname'] : 'องค์กรปกครองส่วนท้องถิ่น'; ?>">
    
    <title><?php echo isset($page_title) ? $page_title : 'สถิติและรายงาน'; ?> - <?php echo isset($org['fname']) ? $org['fname'] : 'องค์กรปกครองส่วนท้องถิ่น'; ?></title>
    
    <!-- Bootstrap 5 -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Font Awesome 6 -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
    <!-- Google Fonts -->
    <link href="https://fonts.googleapis.com/css2?family=Sarabun:wght@300;400;500;600;700&family=Kanit:wght@400;500;600;700&display=swap" rel="stylesheet">
    <!-- Chart.js -->
    <script src="https://cdn.jsdelivr.net/npm/chart.js@3.9.1/dist/chart.min.js"></script>
    <!-- Favicon -->
    <link rel="icon" href="<?php echo base_url('docs/logo.png'); ?>" type="image/x-icon">
    
    <style>
        :root {
            --primary-color: #1e40af;
            --primary-light: #3b82f6;
            --primary-dark: #1e3a8a;
            --secondary-color: #64748b;
            --accent-color: #0891b2;
            --success-color: #059669;
            --white: #ffffff;
            --gray-50: #f8fafc;
            --gray-100: #f1f5f9;
            --gray-200: #e2e8f0;
            --gray-300: #cbd5e1;
            --gray-600: #475569;
            --gray-800: #1e293b;
            --shadow-sm: 0 1px 3px rgba(0, 0, 0, 0.05);
            --shadow: 0 4px 6px rgba(0, 0, 0, 0.07);
            --shadow-md: 0 6px 15px rgba(0, 0, 0, 0.08);
            --shadow-lg: 0 10px 25px rgba(0, 0, 0, 0.1);
        }

        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Sarabun', sans-serif;
            background: linear-gradient(135deg, #f8fafc 0%, #e2e8f0 100%);
            color: var(--gray-800);
            line-height: 1.7;
            min-height: 100vh;
        }

        /* Header Section */
        .header-wrapper {
            background: var(--white);
            border-bottom: 1px solid var(--gray-200);
            box-shadow: var(--shadow-md);
            position: sticky;
            top: 0;
            z-index: 1000;
        }

        .header-content {
            padding: 1.5rem 0;
        }

        .logo-section {
            display: flex;
            align-items: center;
            gap: 1.5rem;
        }

        .logo-container {
            position: relative;
        }

        .logo-image {
            height: 110px;
            width: auto;
            max-width: 110px;
            object-fit: contain;
            filter: drop-shadow(0 4px 8px rgba(0, 0, 0, 0.1));
        }

        .logo-fallback {
            width: 80px;
            height: 80px;
            background: linear-gradient(135deg, var(--primary-color) 0%, var(--primary-light) 100%);
            border-radius: 16px;
            display: none;
            align-items: center;
            justify-content: center;
            box-shadow: var(--shadow-md);
        }

        .logo-fallback i {
            font-size: 2.5rem;
            color: var(--white);
        }

        .header-text h1 {
            font-family: 'Kanit', sans-serif;
            font-size: 1.75rem;
            font-weight: 600;
            color: var(--gray-800);
            margin: 0;
        }

        .header-subtitle {
            font-size: 1rem;
            color: var(--secondary-color);
            margin: 0.25rem 0 0 0;
        }

        .header-org {
            font-size: 1.1rem;
            color: var(--primary-color);
            margin: 0.5rem 0 0 0;
            font-weight: 500;
        }

        /* ปุ่มกลับหน้าหลัก */
        .btn-home {
            position: absolute;
            top: 20px;
            right: 20px;
            background: white;
            color: #667eea;
            border: 2px solid #667eea;
            padding: 10px 25px;
            border-radius: 50px;
            font-weight: 600;
            text-decoration: none;
            display: inline-flex;
            align-items: center;
            gap: 8px;
            transition: all 0.3s;
            box-shadow: 0 4px 15px rgba(102, 126, 234, 0.2);
            z-index: 1000;
        }

        .btn-home:hover {
            background: #667eea;
            color: white;
            transform: translateY(-2px);
            box-shadow: 0 6px 20px rgba(102, 126, 234, 0.4);
            text-decoration: none;
        }

        .header-wrapper .container {
            position: relative;
        }

        /* Stats Section */
        .stats-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
            gap: 1.5rem;
            margin: 2rem 0;
        }

        .stat-card {
            background: var(--white);
            border-radius: 16px;
            padding: 2rem;
            box-shadow: var(--shadow);
            transition: all 0.3s ease;
            position: relative;
            overflow: hidden;
        }

        .stat-card:hover {
            transform: translateY(-5px);
            box-shadow: var(--shadow-lg);
        }

        .stat-icon {
            width: 60px;
            height: 60px;
            background: linear-gradient(135deg, var(--primary-color) 0%, var(--primary-light) 100%);
            border-radius: 14px;
            display: flex;
            align-items: center;
            justify-content: center;
            margin-bottom: 1rem;
            box-shadow: var(--shadow);
        }

        .stat-icon i {
            font-size: 1.75rem;
            color: var(--white);
        }

        .stat-value {
            font-family: 'Kanit', sans-serif;
            font-size: 2.25rem;
            font-weight: 700;
            color: var(--primary-color);
            margin: 0.5rem 0;
        }

        .stat-label {
            font-size: 1rem;
            color: var(--secondary-color);
            font-weight: 500;
        }

        /* Section Header */
        .section-header {
            background: var(--white);
            border-radius: 12px;
            padding: 1.5rem 2rem;
            margin: 2rem 0 1.5rem 0;
            box-shadow: var(--shadow);
            border-left: 5px solid var(--primary-color);
        }

        .section-title {
            font-family: 'Kanit', sans-serif;
            font-size: 1.5rem;
            font-weight: 600;
            color: var(--gray-800);
            margin: 0;
            display: flex;
            align-items: center;
            gap: 0.75rem;
        }

        .section-title i {
            color: var(--primary-color);
        }

        /* Category Grid */
        .category-grid {
            background: var(--white);
            border-radius: 16px;
            overflow: hidden;
            box-shadow: var(--shadow-md);
            margin-bottom: 2rem;
        }

        .category-item {
            display: flex;
            align-items: center;
            padding: 1.5rem 2rem;
            border-bottom: 1px solid var(--gray-100);
            text-decoration: none;
            color: inherit;
            transition: all 0.3s ease;
            position: relative;
        }

        .category-item:last-child {
            border-bottom: none;
        }

        .category-item:hover {
            background: var(--gray-50);
            padding-left: 2.5rem;
        }

        .category-icon-box {
            width: 56px;
            height: 56px;
            border-radius: 12px;
            display: flex;
            align-items: center;
            justify-content: center;
            margin-right: 1.5rem;
            flex-shrink: 0;
            box-shadow: var(--shadow);
        }

        .category-icon-box i {
            font-size: 1.5rem;
            color: var(--white);
        }

        .category-content {
            flex: 1;
        }

        .category-name {
            font-size: 1.15rem;
            font-weight: 600;
            color: var(--gray-800);
            margin: 0 0 0.25rem 0;
            font-family: 'Kanit', sans-serif;
        }

        .category-description {
            font-size: 0.95rem;
            color: var(--secondary-color);
            margin: 0;
        }

        .category-count {
            display: flex;
            align-items: center;
            gap: 0.5rem;
            font-size: 1.1rem;
            font-weight: 600;
            color: var(--primary-color);
            padding: 0.5rem 1.25rem;
            background: var(--gray-50);
            border-radius: 8px;
            margin-right: 1rem;
        }

        .category-arrow {
            color: var(--gray-300);
            font-size: 1.25rem;
        }

        .category-item:hover .category-arrow {
            color: var(--primary-color);
            transform: translateX(5px);
        }

        /* Chart Section */
        .chart-section {
            background: var(--white);
            border-radius: 16px;
            padding: 2rem;
            margin-bottom: 2rem;
            box-shadow: var(--shadow-md);
        }

        .chart-title {
            font-family: 'Kanit', sans-serif;
            font-size: 1.3rem;
            font-weight: 600;
            color: var(--gray-800);
            margin-bottom: 1.5rem;
            display: flex;
            align-items: center;
            gap: 0.75rem;
        }

        .chart-title i {
            color: var(--primary-color);
        }

        .chart-container {
            position: relative;
            height: 350px;
        }

        /* Table Section */
        .table-section {
            background: var(--white);
            border-radius: 16px;
            padding: 2rem;
            margin-bottom: 2rem;
            box-shadow: var(--shadow-md);
            overflow-x: auto;
        }

        .data-table {
            width: 100%;
            border-collapse: collapse;
        }

        .data-table thead th {
            background: var(--gray-50);
            padding: 1rem;
            font-weight: 600;
            color: var(--gray-800);
            border-bottom: 2px solid var(--gray-200);
            text-align: left;
        }

        .data-table tbody td {
            padding: 1rem;
            border-bottom: 1px solid var(--gray-100);
        }

        .data-table tbody tr:hover {
            background: var(--gray-50);
        }

        .rank-badge {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            width: 32px;
            height: 32px;
            border-radius: 50%;
            font-weight: 700;
            font-size: 0.9rem;
        }

        .rank-1 { background: #ffd700; color: #fff; }
        .rank-2 { background: #c0c0c0; color: #fff; }
        .rank-3 { background: #cd7f32; color: #fff; }
        .rank-other { background: var(--gray-100); color: var(--gray-600); }

        .progress-bar-custom {
            height: 8px;
            background: var(--gray-200);
            border-radius: 10px;
            overflow: hidden;
        }

        .progress-fill {
            height: 100%;
            background: linear-gradient(90deg, var(--primary-color), var(--primary-light));
            border-radius: 10px;
            transition: width 1s ease;
        }

        /* Info Grid */
        .info-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(300px, 1fr));
            gap: 1.5rem;
            margin-bottom: 2rem;
        }

        .info-card {
            background: var(--white);
            border-radius: 16px;
            padding: 2rem;
            box-shadow: var(--shadow);
        }

        .info-card h5 {
            font-family: 'Kanit', sans-serif;
            font-size: 1.1rem;
            font-weight: 600;
            color: var(--gray-800);
            margin-bottom: 1rem;
            display: flex;
            align-items: center;
            gap: 0.5rem;
        }

        .info-item {
            display: flex;
            justify-content: space-between;
            padding: 0.75rem 0;
            border-bottom: 1px solid var(--gray-100);
        }

        .info-item:last-child {
            border-bottom: none;
        }

        .info-label {
            color: var(--secondary-color);
        }

        .info-value {
            font-weight: 600;
            color: var(--primary-color);
        }

        /* Empty State */
        .empty-state {
            padding: 3rem 2rem;
            text-align: center;
            color: var(--secondary-color);
        }

        .empty-state i {
            font-size: 3rem;
            color: var(--gray-300);
            margin-bottom: 1rem;
        }

        /* Responsive */
        @media (max-width: 768px) {
            .header-content {
                padding: 1rem 0;
            }

            .logo-image {
                height: 60px;
            }

            .stats-grid {
                grid-template-columns: 1fr;
            }

            .category-item {
                flex-wrap: wrap;
                padding: 1.25rem 1.5rem;
            }

            .category-count {
                margin: 0.75rem 0 0 0;
                width: 100%;
                justify-content: center;
            }

            .chart-container {
                height: 250px;
            }
        }
    </style>
</head>
<body>
    <!-- Header -->
    <div class="header-wrapper">
        <div class="container">
            <!-- ปุ่มกลับหน้าหลัก -->
            <a href="<?= base_url('/home') ?>" class="btn-home">
                <i class="fas fa-home"></i>
                <span>กลับสู่หน้าหลัก</span>
            </a>
            
            <div class="header-content">
                <div class="logo-section">
                    <div class="logo-container">
                        <img src="<?php echo base_url('docs/logo.png'); ?>" 
                             alt="Logo" 
                             class="logo-image"
                             onerror="this.style.display='none'; this.nextElementSibling.style.display='flex';">
                        <div class="logo-fallback">
                            <i class="fas fa-chart-bar"></i>
                        </div>
                    </div>
                    <div class="header-text">
                        <h1>สถิติและรายงาน</h1>
                        <p class="header-subtitle">ระบบติดตามและรายงานข้อมูล</p>
                        <?php if (isset($org['fname']) && !empty($org['fname'])): ?>
                        <p class="header-org">
                            <i class="fas fa-landmark"></i> <?php echo $org['fname']; ?>
                        </p>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="container">
        <!-- Stats Section -->
        <div class="stats-grid">
            <div class="stat-card">
                <div class="stat-icon">
                    <i class="fas fa-database"></i>
                </div>
                <div class="stat-value"><?= number_format($total_datasets) ?></div>
                <div class="stat-label">ชุดข้อมูลทั้งหมด</div>
            </div>
            <div class="stat-card">
                <div class="stat-icon">
                    <i class="fas fa-layer-group"></i>
                </div>
                <div class="stat-value"><?= number_format($total_categories) ?></div>
                <div class="stat-label">หมวดหมู่ข้อมูล</div>
            </div>
            <div class="stat-card">
                <div class="stat-icon">
                    <i class="fas fa-eye"></i>
                </div>
                <div class="stat-value"><?= number_format($total_views) ?></div>
                <div class="stat-label">จำนวนการเข้าชม</div>
            </div>
            <div class="stat-card">
                <div class="stat-icon">
                    <i class="fas fa-download"></i>
                </div>
                <div class="stat-value"><?= number_format($total_downloads) ?></div>
                <div class="stat-label">จำนวนดาวน์โหลด</div>
            </div>
        </div>

        <!-- หมวดหมู่ข้อมูล -->
        <div class="section-header">
            <h3 class="section-title">
                <i class="fas fa-th-list"></i>
                <span>หมวดหมู่ข้อมูล</span>
            </h3>
        </div>

        <div class="category-grid">
            <?php if (!empty($category_stats)): ?>
                <?php foreach ($category_stats as $cat): ?>
                    <?php if ($cat->count > 0): ?>
                    <div class="category-item">
                        <div class="category-icon-box" style="background: <?= $cat->color ?>;">
                            <i class="<?= $cat->icon ?>"></i>
                        </div>
                        <div class="category-content">
                            <h5 class="category-name"><?= $cat->category_name ?></h5>
                            <p class="category-description">หมวดหมู่ข้อมูล</p>
                        </div>
                        <div class="category-count">
                            <i class="fas fa-database"></i>
                            <span><?= number_format($cat->count) ?></span>
                        </div>
                    </div>
                    <?php endif; ?>
                <?php endforeach; ?>
            <?php else: ?>
                <div class="empty-state">
                    <i class="fas fa-inbox"></i>
                    <p>ยังไม่มีข้อมูล</p>
                </div>
            <?php endif; ?>
        </div>

        <!-- Category Distribution Chart -->
        <div class="chart-section">
            <h4 class="chart-title">
                <i class="fas fa-chart-pie"></i>
                <span>สัดส่วนข้อมูลตามหมวดหมู่</span>
            </h4>
            <div class="chart-container">
                <canvas id="categoryChart"></canvas>
            </div>
        </div>

        <!-- Monthly Views Chart -->
        <div class="chart-section">
            <h4 class="chart-title">
                <i class="fas fa-chart-line"></i>
                <span>สถิติการเข้าชม (6 เดือนล่าสุด)</span>
            </h4>
            <div class="chart-container">
                <canvas id="viewsChart"></canvas>
            </div>
        </div>

        <!-- Popular Datasets Table -->
        <div class="table-section">
            <h4 class="chart-title">
                <i class="fas fa-fire"></i>
                <span>ชุดข้อมูลยอดนิยม (Top 10)</span>
            </h4>
            <table class="data-table">
                <thead>
                    <tr>
                        <th style="width: 60px;">อันดับ</th>
                        <th>ชื่อชุดข้อมูล</th>
                        <th>หมวดหมู่</th>
                        <th style="width: 120px;">จำนวนเข้าชม</th>
                        <th style="width: 150px;">สัดส่วน</th>
                    </tr>
                </thead>
                <tbody>
                    <?php 
                    $rank = 1;
                    $max_views = !empty($popular_datasets) ? $popular_datasets[0]->views : 1;
                    foreach ($popular_datasets as $dataset): 
                    $percentage = ($dataset->views / $max_views) * 100;
                    ?>
                    <tr>
                        <td>
                            <span class="rank-badge rank-<?= $rank <= 3 ? $rank : 'other' ?>">
                                <?= $rank ?>
                            </span>
                        </td>
                        <td><strong><?= $dataset->dataset_name ?></strong></td>
                        <td>
                            <span style="color: <?= $dataset->color ?>;">
                                <i class="<?= $dataset->icon ?>"></i> <?= $dataset->category_name ?>
                            </span>
                        </td>
                        <td><strong><?= number_format($dataset->views) ?></strong> ครั้ง</td>
                        <td>
                            <div class="progress-bar-custom">
                                <div class="progress-fill" style="width: <?= $percentage ?>%;"></div>
                            </div>
                        </td>
                    </tr>
                    <?php 
                    $rank++;
                    endforeach; 
                    ?>
                </tbody>
            </table>
        </div>

        <!-- Info Cards -->
        <div class="info-grid">
            <div class="info-card">
                <h5><i class="fas fa-globe"></i> ระดับการเข้าถึง</h5>
                <?php if (!empty($access_stats)): ?>
                    <?php foreach ($access_stats as $stat): ?>
                    <div class="info-item">
                        <span class="info-label">
                            <?php if ($stat->access_level == 'public'): ?>
                            <i class="fas fa-globe"></i> เปิดเผยทั่วไป
                            <?php elseif ($stat->access_level == 'restricted'): ?>
                            <i class="fas fa-lock"></i> จำกัดสิทธิ์
                            <?php else: ?>
                            <i class="fas fa-lock"></i> ส่วนตัว
                            <?php endif; ?>
                        </span>
                        <span class="info-value"><?= number_format($stat->count) ?> ชุด</span>
                    </div>
                    <?php endforeach; ?>
                <?php endif; ?>
            </div>
            
            <div class="info-card">
                <h5><i class="fas fa-file-code"></i> รูปแบบข้อมูล</h5>
                <?php if (!empty($format_stats)): ?>
                    <?php foreach ($format_stats as $stat): ?>
                    <div class="info-item">
                        <span class="info-label">
                            <i class="fas fa-file"></i> <?= $stat->data_format ?>
                        </span>
                        <span class="info-value"><?= number_format($stat->count) ?> ชุด</span>
                    </div>
                    <?php endforeach; ?>
                <?php endif; ?>
            </div>
            
            <div class="info-card">
                <h5><i class="fas fa-sync-alt"></i> ความถี่การอัปเดต</h5>
                <?php if (!empty($update_frequency_stats)): ?>
                    <?php foreach ($update_frequency_stats as $stat): ?>
                    <div class="info-item">
                        <span class="info-label">
                            <i class="fas fa-calendar"></i> <?= $stat->update_frequency ?>
                        </span>
                        <span class="info-value"><?= number_format($stat->count) ?> ชุด</span>
                    </div>
                    <?php endforeach; ?>
                <?php endif; ?>
            </div>
        </div>
    </div>

    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    
    <script>
    // Category Distribution Chart
    const categoryCtx = document.getElementById('categoryChart').getContext('2d');
    const categoryChart = new Chart(categoryCtx, {
        type: 'doughnut',
        data: {
            labels: <?= json_encode(array_column($category_stats, 'category_name')) ?>,
            datasets: [{
                data: <?= json_encode(array_column($category_stats, 'count')) ?>,
                backgroundColor: <?= json_encode(array_column($category_stats, 'color')) ?>,
                borderWidth: 3,
                borderColor: '#fff'
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
                legend: {
                    position: 'right',
                    labels: {
                        font: {
                            family: 'Sarabun',
                            size: 13
                        },
                        padding: 15
                    }
                },
                tooltip: {
                    callbacks: {
                        label: function(context) {
                            let label = context.label || '';
                            let value = context.parsed || 0;
                            let total = context.dataset.data.reduce((a, b) => a + b, 0);
                            let percentage = ((value / total) * 100).toFixed(1);
                            return label + ': ' + value + ' ชุด (' + percentage + '%)';
                        }
                    }
                }
            }
        }
    });

    // Monthly Views Chart
    const viewsCtx = document.getElementById('viewsChart').getContext('2d');
    const viewsChart = new Chart(viewsCtx, {
        type: 'line',
        data: {
            labels: <?= json_encode(array_column($monthly_views, 'month')) ?>,
            datasets: [{
                label: 'จำนวนเข้าชม',
                data: <?= json_encode(array_column($monthly_views, 'views')) ?>,
                borderColor: '#1e40af',
                backgroundColor: 'rgba(30, 64, 175, 0.1)',
                borderWidth: 3,
                fill: true,
                tension: 0.4,
                pointRadius: 5,
                pointHoverRadius: 7,
                pointBackgroundColor: '#1e40af',
                pointBorderColor: '#fff',
                pointBorderWidth: 2
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
                legend: {
                    display: false
                },
                tooltip: {
                    callbacks: {
                        label: function(context) {
                            return 'เข้าชม: ' + context.parsed.y.toLocaleString() + ' ครั้ง';
                        }
                    }
                }
            },
            scales: {
                y: {
                    beginAtZero: true,
                    ticks: {
                        font: {
                            family: 'Sarabun'
                        },
                        callback: function(value) {
                            return value.toLocaleString();
                        }
                    }
                },
                x: {
                    ticks: {
                        font: {
                            family: 'Sarabun'
                        }
                    }
                }
            }
        }
    });
    </script>
</body>
</html>