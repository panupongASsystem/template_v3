<!DOCTYPE html>
<html lang="th">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="description" content="<?= isset($category->category_name) ? $category->category_name : 'หมวดหมู่' ?> - <?php echo isset($org['fname']) ? $org['fname'] : 'องค์กรปกครองส่วนท้องถิ่น'; ?>">
    
    <title><?= $page_title ?> - <?php echo isset($org['fname']) ? $org['fname'] : 'องค์กรปกครองส่วนท้องถิ่น'; ?></title>
    
    <!-- Bootstrap 5 -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Font Awesome 6 -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
    <!-- Google Fonts -->
    <link href="https://fonts.googleapis.com/css2?family=Sarabun:wght@300;400;500;600;700&family=Kanit:wght@400;500;600;700&display=swap" rel="stylesheet">
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

        /* Header */
        .header-wrapper {
            background: var(--white);
            border-bottom: 1px solid var(--gray-200);
            box-shadow: var(--shadow-md);
            position: sticky;
            top: 0;
            z-index: 1000;
            animation: slideDown 0.5s ease-out;
        }

        @keyframes slideDown {
            from {
                transform: translateY(-100%);
                opacity: 0;
            }
            to {
                transform: translateY(0);
                opacity: 1;
            }
        }

        .header-content {
            padding: 1.5rem 0;
        }

        .logo-section {
            display: flex;
            align-items: center;
            gap: 1.5rem;
        }

        .logo-image {
            height: 110px;
            width: auto;
            max-width: 110px;
            object-fit: contain;
            filter: drop-shadow(0 4px 8px rgba(0, 0, 0, 0.1));
            transition: transform 0.3s ease;
        }

        .logo-image:hover {
            transform: scale(1.05);
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
            line-height: 1.3;
        }

        .header-text .subtitle {
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

        /* Breadcrumb */
        .breadcrumb-section {
            background: var(--white);
            padding: 1rem 0;
            margin: 1.5rem 0;
            border-radius: 12px;
            box-shadow: var(--shadow);
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

        .breadcrumb {
            background: none;
            padding: 0;
            margin: 0;
        }

        .breadcrumb-item a {
            color: var(--primary-color);
            text-decoration: none;
            transition: all 0.3s ease;
        }

        .breadcrumb-item a:hover {
            color: var(--primary-dark);
        }

        .breadcrumb-item.active {
            color: var(--secondary-color);
        }

        /* Category Header */
        .category-header {
            background: var(--white);
            border-radius: 16px;
            padding: 2.5rem;
            margin-bottom: 2rem;
            box-shadow: var(--shadow-md);
            animation: fadeInUp 0.6s ease-out 0.1s both;
            position: relative;
            overflow: hidden;
        }

        .category-header::before {
            content: '';
            position: absolute;
            top: -50%;
            right: -10%;
            width: 400px;
            height: 400px;
            background: <?= isset($category->color) ? $category->color : 'var(--primary-light)' ?>15;
            border-radius: 50%;
            z-index: 0;
        }

        .category-header-content {
            position: relative;
            z-index: 1;
            display: flex;
            align-items: start;
            gap: 2rem;
        }

        .category-icon-large {
            width: 100px;
            height: 100px;
            border-radius: 20px;
            background: <?= isset($category->color) ? $category->color : 'var(--primary-light)' ?>;
            display: flex;
            align-items: center;
            justify-content: center;
            box-shadow: var(--shadow-lg);
            flex-shrink: 0;
        }

        .category-icon-large i {
            font-size: 3rem;
            color: var(--white);
        }

        .category-info h1 {
            font-family: 'Kanit', sans-serif;
            font-size: 2.25rem;
            font-weight: 700;
            color: var(--gray-800);
            margin-bottom: 0.75rem;
        }

        .category-description {
            font-size: 1.15rem;
            color: var(--gray-600);
            margin-bottom: 1rem;
            line-height: 1.6;
        }

        .category-subtitle {
            font-size: 1rem;
            color: var(--secondary-color);
            display: flex;
            align-items: center;
            gap: 0.5rem;
        }

        /* Stats Row */
        .stats-row {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
            gap: 1.5rem;
            margin-top: 1.5rem;
        }

        .stat-card {
            background: var(--gray-50);
            padding: 1.5rem;
            border-radius: 12px;
            display: flex;
            align-items: center;
            gap: 1rem;
            box-shadow: var(--shadow-sm);
        }

        .stat-icon {
            width: 50px;
            height: 50px;
            border-radius: 12px;
            background: <?= isset($category->color) ? $category->color : 'var(--primary-light)' ?>20;
            color: <?= isset($category->color) ? $category->color : 'var(--primary-light)' ?>;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 1.5rem;
        }

        .stat-info h4 {
            font-family: 'Kanit', sans-serif;
            font-size: 1.75rem;
            font-weight: 700;
            color: var(--gray-800);
            margin: 0;
        }

        .stat-info p {
            font-size: 0.95rem;
            color: var(--secondary-color);
            margin: 0;
        }

        /* Dataset Cards */
        .datasets-grid {
            display: grid;
            gap: 1.5rem;
        }

        .dataset-card {
            background: var(--white);
            border-radius: 12px;
            padding: 2rem;
            box-shadow: var(--shadow);
            transition: all 0.3s ease;
            position: relative;
            overflow: hidden;
            animation: fadeInUp 0.6s ease-out both;
        }

        .dataset-card::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            width: 5px;
            height: 100%;
            background: <?= isset($category->color) ? $category->color : 'var(--primary-light)' ?>;
            transform: scaleY(0);
            transition: transform 0.3s ease;
        }

        .dataset-card:hover {
            transform: translateY(-5px);
            box-shadow: var(--shadow-lg);
        }

        .dataset-card:hover::before {
            transform: scaleY(1);
        }

        .dataset-header {
            display: flex;
            justify-content: space-between;
            align-items: start;
            margin-bottom: 1rem;
        }

        .dataset-title {
            font-family: 'Kanit', sans-serif;
            font-size: 1.5rem;
            font-weight: 600;
            color: var(--gray-800);
            margin-bottom: 0.5rem;
            display: flex;
            align-items: center;
            gap: 0.75rem;
        }

        .dataset-title i {
            color: <?= isset($category->color) ? $category->color : 'var(--primary-light)' ?>;
        }

        .dataset-subtitle {
            font-size: 0.95rem;
            color: var(--secondary-color);
        }

        .status-badge {
            padding: 0.5rem 1rem;
            border-radius: 8px;
            font-size: 0.9rem;
            font-weight: 600;
            white-space: nowrap;
        }

        .status-active {
            background: #d4edda;
            color: #155724;
        }

        .status-inactive {
            background: #f8d7da;
            color: #721c24;
        }

        .dataset-description {
            color: var(--gray-600);
            margin-bottom: 1.25rem;
            line-height: 1.6;
        }

        .dataset-meta {
            display: flex;
            flex-wrap: wrap;
            gap: 1.25rem;
            margin-bottom: 1.5rem;
            padding: 1rem 0;
            border-top: 1px solid var(--gray-200);
            border-bottom: 1px solid var(--gray-200);
        }

        .meta-item {
            display: flex;
            align-items: center;
            gap: 0.5rem;
            color: var(--gray-600);
            font-size: 0.95rem;
        }

        .meta-item i {
            color: var(--secondary-color);
        }

        .meta-item code {
            background: var(--gray-100);
            padding: 0.25rem 0.5rem;
            border-radius: 4px;
            font-size: 0.85rem;
        }

        /* Dataset Actions */
        .dataset-actions {
            display: flex;
            gap: 1rem;
            flex-wrap: wrap;
        }

        .btn-action {
            display: inline-flex;
            align-items: center;
            gap: 0.75rem;
            padding: 0.875rem 1.75rem;
            border-radius: 10px;
            text-decoration: none;
            font-weight: 600;
            transition: all 0.3s ease;
            font-size: 1rem;
        }

        .btn-primary {
            background: var(--primary-light);
            color: var(--white);
            box-shadow: var(--shadow);
        }

        .btn-primary:hover {
            background: var(--primary-color);
            color: var(--white);
            transform: translateY(-2px);
            box-shadow: var(--shadow-lg);
        }

        .btn-secondary {
            background: var(--gray-100);
            color: var(--gray-800);
        }

        .btn-secondary:hover {
            background: var(--gray-200);
            color: var(--gray-800);
        }

        /* Empty State */
        .empty-state {
            background: var(--white);
            border-radius: 16px;
            padding: 5rem 2rem;
            text-align: center;
            box-shadow: var(--shadow-md);
            animation: fadeInUp 0.6s ease-out 0.3s both;
        }

        .empty-state i {
            font-size: 5rem;
            color: var(--gray-300);
            margin-bottom: 1.5rem;
        }

        .empty-state h3 {
            font-family: 'Kanit', sans-serif;
            font-size: 1.75rem;
            font-weight: 600;
            color: var(--gray-800);
            margin-bottom: 1rem;
        }

        .empty-state p {
            font-size: 1.1rem;
            color: var(--secondary-color);
            margin-bottom: 2rem;
        }

        /* Pagination */
        .pagination-wrapper {
            display: flex;
            justify-content: center;
            margin-top: 2rem;
            animation: fadeInUp 0.6s ease-out 0.5s both;
        }

        .pagination {
            display: flex;
            gap: 0.5rem;
        }

        .page-link {
            padding: 0.75rem 1.25rem;
            border-radius: 10px;
            background: var(--white);
            color: var(--primary-color);
            text-decoration: none;
            border: 2px solid var(--gray-200);
            font-weight: 600;
            transition: all 0.3s ease;
        }

        .page-link:hover {
            background: var(--primary-light);
            color: var(--white);
            border-color: var(--primary-light);
        }

        .page-item.active .page-link {
            background: var(--primary-color);
            color: var(--white);
            border-color: var(--primary-color);
        }

        /* Responsive */
        @media (max-width: 768px) {
            .header-content {
                padding: 1rem 0;
            }

            .logo-image,
            .logo-fallback {
                height: 60px;
                width: 60px;
            }

            .header-text h1 {
                font-size: 1.25rem;
            }

            .category-header-content {
                flex-direction: column;
            }

            .category-icon-large {
                width: 80px;
                height: 80px;
            }

            .category-info h1 {
                font-size: 1.75rem;
            }

            .stats-row {
                grid-template-columns: 1fr;
            }

            .dataset-header {
                flex-direction: column;
                gap: 1rem;
            }

            .dataset-meta {
                flex-direction: column;
                gap: 0.75rem;
            }

            .dataset-actions {
                flex-direction: column;
            }

            .btn-action {
                width: 100%;
                justify-content: center;
            }
        }

        /* Animation delays */
        .dataset-card:nth-child(1) { animation-delay: 0.2s; }
        .dataset-card:nth-child(2) { animation-delay: 0.25s; }
        .dataset-card:nth-child(3) { animation-delay: 0.3s; }
        .dataset-card:nth-child(4) { animation-delay: 0.35s; }
        .dataset-card:nth-child(5) { animation-delay: 0.4s; }
        .dataset-card:nth-child(6) { animation-delay: 0.45s; }
		
		
		/* ปุ่มกลับหน้าหลัก - มุมบนขวา */
.btn-home {
    position: absolute;
    top: 20px;
    right: 20px;  /* เปลี่ยนจาก left เป็น right */
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

.btn-home i {
    font-size: 1.2rem;
}

/* ให้ header-wrapper หรือ container มี position relative */
.header-wrapper {
    position: relative;
}

/* หรือถ้าใช้ container */
.header-wrapper .container {
    position: relative;
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
                    <div>
                        <img src="<?php echo base_url('docs/logo.png'); ?>" 
                             alt="<?php echo isset($org['fname']) ? $org['fname'] : 'Logo'; ?>" 
                             class="logo-image"
                             onerror="this.style.display='none'; this.nextElementSibling.style.display='flex';">
                        <div class="logo-fallback">
                            <i class="fas fa-database"></i>
                        </div>
                    </div>
                    <div class="header-text">
                        <h1>บัญชีรายการข้อมูล</h1>
                        <p class="subtitle">ระบบจัดการและเผยแพร่ข้อมูลภาครัฐ</p>
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
        <!-- Breadcrumb -->
        <div class="breadcrumb-section">
            <div class="container">
                <nav aria-label="breadcrumb">
                    <ol class="breadcrumb">
                        <li class="breadcrumb-item"><a href="<?= base_url('data_catalog') ?>"><i class="fas fa-home"></i> หน้าหลัก</a></li>
                        <li class="breadcrumb-item active"><?= isset($category->category_name) ? $category->category_name : 'หมวดหมู่' ?></li>
                    </ol>
                </nav>
            </div>
        </div>

        <!-- Category Header -->
        <?php if (isset($category)): ?>
        <div class="category-header">
            <div class="category-header-content">
                <div class="category-icon-large">
                    <i class="<?= isset($category->icon) ? $category->icon : 'fas fa-folder' ?>"></i>
                </div>
                
                <div class="category-info flex-grow-1">
                    <h1><?= $category->category_name ?></h1>
                    
                    <?php if (isset($category->description) && !empty($category->description)): ?>
                    <p class="category-description"><?= $category->description ?></p>
                    <?php endif; ?>
                    
                    <?php if (isset($category->category_name_en) && !empty($category->category_name_en)): ?>
                    <p class="category-subtitle">
                        <i class="fas fa-globe"></i>
                        <span><?= $category->category_name_en ?></span>
                    </p>
                    <?php endif; ?>
                    
                    <div class="stats-row">
                        <div class="stat-card">
                            <div class="stat-icon">
                                <i class="fas fa-database"></i>
                            </div>
                            <div class="stat-info">
                                <h4><?= isset($datasets) && is_array($datasets) ? count($datasets) : 0 ?></h4>
                                <p>ชุดข้อมูล</p>
                            </div>
                        </div>
                        
                        <div class="stat-card">
                            <div class="stat-icon">
                                <i class="fas fa-chart-line"></i>
                            </div>
                            <div class="stat-info">
                                <?php 
                                $total_views = 0;
                                if (isset($datasets) && is_array($datasets)) {
                                    foreach ($datasets as $ds) {
                                        $total_views += isset($ds->views) ? $ds->views : 0;
                                    }
                                }
                                ?>
                                <h4><?= number_format($total_views) ?></h4>
                                <p>ยอดเข้าชม</p>
                            </div>
                        </div>
                        
                        <div class="stat-card">
                            <div class="stat-icon">
                                <i class="fas fa-clock"></i>
                            </div>
                            <div class="stat-info">
                                <h4><?= date('d/m/Y') ?></h4>
                                <p>อัปเดตล่าสุด</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <?php endif; ?>

        <!-- Datasets Grid -->
        <?php if (!empty($datasets) && is_array($datasets)): ?>
        <div class="datasets-grid">
            <?php foreach ($datasets as $dataset): ?>
            <div class="dataset-card">
                <div class="dataset-header">
                    <div class="flex-grow-1">
                        <h3 class="dataset-title">
                            <i class="fas fa-database"></i>
                            <?= $dataset->dataset_name ?>
                        </h3>
                        <?php if (isset($dataset->dataset_name_en) && !empty($dataset->dataset_name_en)): ?>
                        <p class="dataset-subtitle"><?= $dataset->dataset_name_en ?></p>
                        <?php endif; ?>
                    </div>
                    <?php if (isset($dataset->status)): ?>
                    <span class="status-badge <?= $dataset->status == 1 ? 'status-active' : 'status-inactive' ?>">
                        <?= $dataset->status == 1 ? '✓ เปิดใช้งาน' : '✕ ปิดใช้งาน' ?>
                    </span>
                    <?php endif; ?>
                </div>
                
                <?php if (isset($dataset->description) && !empty($dataset->description)): ?>
                <p class="dataset-description"><?= $dataset->description ?></p>
                <?php endif; ?>
                
                <div class="dataset-meta">
                    <?php if (isset($dataset->data_format) && !empty($dataset->data_format)): ?>
                    <div class="meta-item">
                        <i class="fas fa-file-code"></i>
                        <span><strong>รูปแบบ:</strong> <?= $dataset->data_format ?></span>
                    </div>
                    <?php endif; ?>
                    
                    <?php if (isset($dataset->record_count) && $dataset->record_count > 0): ?>
                    <div class="meta-item">
                        <i class="fas fa-list-ol"></i>
                        <span><strong>จำนวน:</strong> <?= number_format($dataset->record_count) ?> รายการ</span>
                    </div>
                    <?php endif; ?>
                    
                    <?php if (isset($dataset->table_name) && !empty($dataset->table_name)): ?>
                    <div class="meta-item">
                        <i class="fas fa-table"></i>
                        <span><strong>ตาราง:</strong> <code><?= preg_replace('/^tbl_/', '', $dataset->table_name) ?></code></span>
                    </div>
                    <?php endif; ?>
                    
                    <?php if (isset($dataset->updated_at)): ?>
                    <div class="meta-item">
                        <i class="fas fa-calendar"></i>
                        <span><?= date('d/m/Y', strtotime($dataset->updated_at)) ?></span>
                    </div>
                    <?php endif; ?>
                    
                    <?php if (isset($dataset->views)): ?>
                    <div class="meta-item">
                        <i class="fas fa-eye"></i>
                        <span><?= number_format($dataset->views) ?> ครั้ง</span>
                    </div>
                    <?php endif; ?>
                </div>
                
                <div class="dataset-actions">
                    <a href="<?= base_url('data_catalog/dataset/' . $dataset->id) ?>" class="btn-action btn-primary">
                        <i class="fas fa-info-circle"></i>
                        ดูรายละเอียด
                    </a>
                    
                    <?php if (isset($dataset->status) && $dataset->status == 1): ?>
                    <?php if (isset($dataset->api_endpoint) && !empty($dataset->api_endpoint)): ?>
                    <?php 
                    $api_url = (strpos($dataset->api_endpoint, 'http') === 0) 
                             ? $dataset->api_endpoint 
                             : base_url($dataset->api_endpoint);
                    ?>
                    <a href="<?= $api_url ?>" class="btn-action btn-secondary" target="_blank">
                        <i class="fas fa-plug"></i>
                        API
                    </a>
                    <?php endif; ?>
                    <?php endif; ?>
                </div>
            </div>
            <?php endforeach; ?>
        </div>
        
        <!-- Pagination -->
        <?php if (isset($pagination) && !empty($pagination)): ?>
        <div class="pagination-wrapper">
            <?= $pagination ?>
        </div>
        <?php endif; ?>
        
        <?php else: ?>
        <!-- Empty State -->
        <div class="empty-state">
            <i class="fas fa-inbox"></i>
            <h3>ยังไม่มีข้อมูลในหมวดหมู่นี้</h3>
            <p>กำลังจัดเตรียมข้อมูลในหมวดหมู่นี้ กรุณากลับมาตรวจสอบอีกครั้ง</p>
            <a href="<?= base_url('data_catalog') ?>" class="btn-action btn-primary">
                <i class="fas fa-arrow-left"></i>
                กลับหน้าหลัก
            </a>
        </div>
        <?php endif; ?>
    </div>

    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>