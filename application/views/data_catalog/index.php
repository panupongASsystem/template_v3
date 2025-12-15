<!DOCTYPE html>
<html lang="th">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="description" content="บัญชีรายการข้อมูล - <?php echo isset($org['fname']) ? $org['fname'] : 'องค์กรปกครองส่วนท้องถิ่น'; ?>">
    <meta name="keywords" content="บัญชีรายการข้อมูล, ข้อมูลเปิด, Open Data, <?php echo isset($org['fname']) ? $org['fname'] : 'องค์กรปกครองส่วนท้องถิ่น'; ?>">
    <meta name="author" content="<?php echo isset($org['fname']) ? $org['fname'] : 'องค์กรปกครองส่วนท้องถิ่น'; ?>">
    
    <title><?php echo isset($page_title) ? $page_title : 'บัญชีรายการข้อมูล'; ?> - <?php echo isset($org['fname']) ? $org['fname'] : 'องค์กรปกครองส่วนท้องถิ่น'; ?></title>
    
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
            --shadow-xl: 0 20px 40px rgba(0, 0, 0, 0.12);
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

        .logo-container {
            position: relative;
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
            animation: pulse 2s ease-in-out infinite;
        }

        @keyframes pulse {
            0%, 100% {
                transform: scale(1);
                box-shadow: 0 4px 15px rgba(30, 64, 175, 0.3);
            }
            50% {
                transform: scale(1.05);
                box-shadow: 0 6px 25px rgba(30, 64, 175, 0.4);
            }
        }

        .logo-fallback i {
            font-size: 2.5rem;
            color: var(--white);
        }

        .header-text {
            flex: 1;
        }

        .header-title {
            font-family: 'Kanit', sans-serif;
            font-size: 1.75rem;
            font-weight: 600;
            color: var(--gray-800);
            margin: 0;
            line-height: 1.3;
        }

        .header-subtitle {
            font-size: 1rem;
            color: var(--secondary-color);
            margin: 0.25rem 0 0 0;
            font-weight: 400;
        }

        .header-org {
            font-size: 1.1rem;
            color: var(--primary-color);
            margin: 0.5rem 0 0 0;
            font-weight: 500;
        }

        /* Search Section */
        .search-section {
            background: var(--white);
            border-radius: 16px;
            padding: 2rem;
            margin: 2rem 0;
            box-shadow: var(--shadow-md);
            transition: all 0.3s ease;
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

        .search-section:hover {
            box-shadow: var(--shadow-lg);
            transform: translateY(-2px);
        }

        .search-box {
            position: relative;
        }

        .search-input {
            width: 100%;
            padding: 1rem 180px 1rem 3.5rem;
            border: 2px solid var(--gray-200);
            border-radius: 12px;
            font-size: 1.05rem;
            transition: all 0.3s ease;
            background: var(--gray-50);
        }

        .search-input:focus {
            outline: none;
            border-color: var(--primary-light);
            box-shadow: 0 0 0 4px rgba(59, 130, 246, 0.1);
            background: var(--white);
        }

        .search-icon {
            position: absolute;
            left: 1.25rem;
            top: 50%;
            transform: translateY(-50%);
            color: var(--gray-600);
            font-size: 1.25rem;
        }

        .search-button {
            position: absolute;
            right: 0.5rem;
            top: 50%;
            transform: translateY(-50%);
            background: linear-gradient(135deg, var(--primary-color) 0%, var(--primary-light) 100%);
            border: none;
            color: var(--white);
            padding: 0.75rem 2rem;
            border-radius: 10px;
            font-weight: 600;
            font-size: 1.05rem;
            cursor: pointer;
            transition: all 0.3s ease;
            box-shadow: var(--shadow);
        }

        .search-button:hover {
            transform: translateY(-50%) translateY(-2px);
            box-shadow: var(--shadow-md);
        }

        .search-button:active {
            transform: translateY(-50%) translateY(0);
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
            animation: fadeInUp 0.6s ease-out backwards;
        }

        .stat-card:nth-child(1) { animation-delay: 0.1s; }
        .stat-card:nth-child(2) { animation-delay: 0.2s; }
        .stat-card:nth-child(3) { animation-delay: 0.3s; }

        .stat-card::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            height: 4px;
            background: linear-gradient(90deg, var(--primary-color), var(--primary-light));
            transform: scaleX(0);
            transition: transform 0.3s ease;
        }

        .stat-card:hover {
            transform: translateY(-5px);
            box-shadow: var(--shadow-lg);
        }

        .stat-card:hover::before {
            transform: scaleX(1);
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
            animation: iconFloat 3s ease-in-out infinite;
        }

        @keyframes iconFloat {
            0%, 100% { transform: translateY(0px); }
            50% { transform: translateY(-8px); }
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
            line-height: 1;
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
            animation: fadeInUp 0.6s ease-out;
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
            font-size: 1.75rem;
        }

        /* Category List */
        .category-grid {
            background: var(--white);
            border-radius: 16px;
            overflow: hidden;
            box-shadow: var(--shadow-md);
            animation: fadeInUp 0.6s ease-out;
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

        .category-item::before {
            content: '';
            position: absolute;
            left: 0;
            top: 0;
            bottom: 0;
            width: 4px;
            background: linear-gradient(180deg, var(--primary-color), var(--primary-light));
            transform: scaleY(0);
            transition: transform 0.3s ease;
        }

        .category-item:hover {
            background: var(--gray-50);
            padding-left: 2.5rem;
        }

        .category-item:hover::before {
            transform: scaleY(1);
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
            transition: all 0.3s ease;
        }

        .category-item:hover .category-icon-box {
            transform: rotate(5deg) scale(1.1);
            box-shadow: var(--shadow-md);
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

        .category-count i {
            font-size: 1rem;
        }

        .category-arrow {
            color: var(--gray-300);
            font-size: 1.25rem;
            transition: all 0.3s ease;
        }

        .category-item:hover .category-arrow {
            color: var(--primary-color);
            transform: translateX(5px);
        }

        /* Tabs */
        .dataset-tabs {
            margin: 2rem 0;
        }

        .nav-tabs-custom {
            border: none;
            background: var(--white);
            padding: 0.5rem;
            border-radius: 12px;
            box-shadow: var(--shadow);
            display: flex;
            gap: 0.5rem;
            margin-bottom: 1.5rem;
        }

        .nav-link-custom {
            flex: 1;
            border: none;
            border-radius: 8px;
            padding: 1rem 1.5rem;
            color: var(--secondary-color);
            font-weight: 600;
            font-size: 1.05rem;
            transition: all 0.3s ease;
            text-align: center;
            cursor: pointer;
            background: transparent;
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 0.5rem;
        }

        .nav-link-custom:hover {
            background: var(--gray-50);
            color: var(--primary-color);
        }

        .nav-link-custom.active {
            background: linear-gradient(135deg, var(--primary-color) 0%, var(--primary-light) 100%);
            color: var(--white);
            box-shadow: var(--shadow);
        }

        /* Dataset List */
        .dataset-list {
            background: var(--white);
            border-radius: 16px;
            overflow: hidden;
            box-shadow: var(--shadow-md);
        }

        .dataset-item {
            display: flex;
            align-items: center;
            padding: 1.5rem 2rem;
            border-bottom: 1px solid var(--gray-100);
            text-decoration: none;
            color: inherit;
            transition: all 0.3s ease;
        }

        .dataset-item:last-child {
            border-bottom: none;
        }

        .dataset-item:hover {
            background: var(--gray-50);
            padding-left: 2.5rem;
        }

        .dataset-icon-box {
            width: 48px;
            height: 48px;
            border-radius: 10px;
            display: flex;
            align-items: center;
            justify-content: center;
            margin-right: 1.25rem;
            flex-shrink: 0;
            box-shadow: var(--shadow-sm);
        }

        .dataset-icon-box i {
            font-size: 1.25rem;
            color: var(--white);
        }

        .dataset-info {
            flex: 1;
        }

        .dataset-name {
            font-size: 1.05rem;
            font-weight: 600;
            color: var(--gray-800);
            margin: 0 0 0.5rem 0;
        }

        .dataset-meta {
            display: flex;
            gap: 1.5rem;
            flex-wrap: wrap;
        }

        .dataset-meta span {
            display: flex;
            align-items: center;
            gap: 0.4rem;
            font-size: 0.9rem;
            color: var(--secondary-color);
        }

        .dataset-meta i {
            font-size: 0.85rem;
        }

        .dataset-arrow {
            color: var(--gray-300);
            font-size: 1.25rem;
            transition: all 0.3s ease;
        }

        .dataset-item:hover .dataset-arrow {
            color: var(--primary-color);
            transform: translateX(5px);
        }

        /* Alert */
        .empty-state {
            background: var(--white);
            border-radius: 16px;
            padding: 3rem 2rem;
            text-align: center;
            box-shadow: var(--shadow);
            color: var(--secondary-color);
        }

        .empty-state i {
            font-size: 3rem;
            color: var(--gray-300);
            margin-bottom: 1rem;
        }

        /* Footer */
        .footer-section {
            background: var(--white);
            border-top: 1px solid var(--gray-200);
            padding: 2rem 0;
            margin-top: 4rem;
            box-shadow: 0 -4px 15px rgba(0, 0, 0, 0.05);
        }

        .footer-links {
            display: flex;
            justify-content: center;
            gap: 2rem;
            flex-wrap: wrap;
            margin-bottom: 1.5rem;
        }

        .footer-link {
            display: inline-flex;
            align-items: center;
            gap: 0.5rem;
            color: var(--secondary-color);
            text-decoration: none;
            font-weight: 500;
            transition: all 0.3s ease;
            padding: 0.5rem 1rem;
            border-radius: 8px;
        }

        .footer-link:hover {
            color: var(--primary-color);
            background: var(--gray-50);
        }

        .footer-text {
            text-align: center;
            color: var(--secondary-color);
            font-size: 0.95rem;
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

            .logo-fallback i {
                font-size: 2rem;
            }

            .header-title {
                font-size: 1.25rem;
            }

            .header-subtitle {
                font-size: 0.9rem;
            }

            .header-org {
                font-size: 1rem;
            }

            .search-input {
                padding: 0.875rem 1rem;
                padding-left: 3rem;
            }

            .search-button {
                position: static;
                transform: none;
                width: 100%;
                margin-top: 0.75rem;
            }

            .search-button:hover {
                transform: translateY(-2px);
            }

            .stats-grid {
                grid-template-columns: 1fr;
            }

            .category-item,
            .dataset-item {
                flex-wrap: wrap;
                padding: 1.25rem 1.5rem;
            }

            .category-count {
                margin: 0.75rem 0 0 0;
                width: 100%;
                justify-content: center;
            }

            .nav-tabs-custom {
                flex-direction: column;
            }

            .footer-links {
                flex-direction: column;
                gap: 0.5rem;
            }
        }

        /* Loading Animation */
        @keyframes shimmer {
            0% {
                background-position: -1000px 0;
            }
            100% {
                background-position: 1000px 0;
            }
        }

        .loading {
            animation: shimmer 2s infinite;
            background: linear-gradient(to right, #f6f7f8 0%, #edeef1 20%, #f6f7f8 40%, #f6f7f8 100%);
            background-size: 1000px 100%;
        }
		
		
		
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
                         alt="<?php echo isset($org['fname']) ? $org['fname'] : 'Logo'; ?>" 
                         class="logo-image"
                         onerror="this.style.display='none'; this.nextElementSibling.style.display='flex';">
                    <div class="logo-fallback">
                        <i class="fas fa-database"></i>
                    </div>
                </div>
                <div class="header-text">
                    <h1 class="header-title">บัญชีรายการข้อมูล</h1>
                    <p class="header-subtitle">ระบบจัดการและเผยแพร่ข้อมูลภาครัฐ</p>
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
        <!-- Search Section -->
        <div class="search-section">
            <form action="<?= base_url('data_catalog/search') ?>" method="get">
                <div class="search-box">
                    <i class="fas fa-search search-icon"></i>
                    <input type="text" 
                           name="q" 
                           class="search-input"
                           placeholder="ค้นหาชุดข้อมูล เช่น ข่าวสาร งบประมาณ แผนพัฒนา..." 
                           required>
                    <button type="submit" class="search-button">
                        <i class="fas fa-search me-2"></i>ค้นหา
                    </button>
                </div>
            </form>
        </div>

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
                <div class="stat-value">
                    <?php 
                    $active_categories = 0;
                    foreach ($categories as $cat) {
                        if ($cat->dataset_count > 0) {
                            $active_categories++;
                        }
                    }
                    echo number_format($active_categories);
                    ?>
                </div>
                <div class="stat-label">หมวดหมู่ข้อมูล</div>
            </div>
            <div class="stat-card">
                <div class="stat-icon">
                    <i class="fas fa-calendar-check"></i>
                </div>
                <div class="stat-value"><?= $last_updated ? date('d/m/Y', strtotime($last_updated)) : '-' ?></div>
                <div class="stat-label">อัปเดตล่าสุด</div>
            </div>
        </div>

        <!-- Categories Section -->
        <div class="section-header">
            <h3 class="section-title">
                <i class="fas fa-th-list"></i>
                <span>หมวดหมู่ข้อมูล</span>
            </h3>
        </div>

        <div class="category-grid">
            <?php foreach ($categories as $cat): ?>
                <?php if ($cat->dataset_count > 0): ?>
                <a href="<?= base_url('data_catalog/category/'.$cat->id) ?>" class="category-item">
                    <div class="category-icon-box" style="background: <?= $cat->color ?>;">
                        <i class="<?= $cat->icon ?>"></i>
                    </div>
                    <div class="category-content">
                        <h5 class="category-name"><?= $cat->category_name ?></h5>
                        <p class="category-description"><?= $cat->description ? word_limiter($cat->description, 15) : 'หมวดหมู่ข้อมูล' ?></p>
                    </div>
                    <div class="category-count">
                        <i class="fas fa-database"></i>
                        <span><?= number_format($cat->dataset_count) ?></span>
                    </div>
                    <div class="category-arrow">
                        <i class="fas fa-chevron-right"></i>
                    </div>
                </a>
                <?php endif; ?>
            <?php endforeach; ?>
        </div>

        <!-- Datasets Section -->
        <div class="dataset-tabs">
            <div class="nav-tabs-custom">
                <div class="nav-link-custom active" onclick="switchTab('recent', this)">
                    <i class="fas fa-clock"></i>
                    <span>อัปเดตล่าสุด</span>
                </div>
                <div class="nav-link-custom" onclick="switchTab('popular', this)">
                    <i class="fas fa-fire"></i>
                    <span>ยอดนิยม</span>
                </div>
            </div>

            <!-- Recent Tab -->
            <div class="dataset-list" id="recent-tab">
                <?php if (!empty($recent_datasets)): ?>
                    <?php foreach ($recent_datasets as $dataset): ?>
                    <a href="<?= base_url('data_catalog/dataset/'.$dataset->id) ?>" class="dataset-item">
                        <div class="dataset-icon-box" style="background: <?= $dataset->color ?>;">
                            <i class="<?= $dataset->icon ?>"></i>
                        </div>
                        <div class="dataset-info">
                            <h6 class="dataset-name"><?= $dataset->dataset_name ?></h6>
                            <div class="dataset-meta">
                                <span><i class="fas fa-folder"></i> <?= $dataset->category_name ?></span>
                                <span><i class="fas fa-calendar"></i> <?= date('d/m/Y', strtotime($dataset->updated_at)) ?></span>
                            </div>
                        </div>
                        <div class="dataset-arrow">
                            <i class="fas fa-chevron-right"></i>
                        </div>
                    </a>
                    <?php endforeach; ?>
                <?php else: ?>
                    <div class="empty-state">
                        <i class="fas fa-inbox"></i>
                        <p>ยังไม่มีข้อมูล</p>
                    </div>
                <?php endif; ?>
            </div>

            <!-- Popular Tab -->
            <div class="dataset-list" id="popular-tab" style="display: none;">
                <?php if (!empty($popular_datasets)): ?>
                    <?php foreach ($popular_datasets as $dataset): ?>
                    <a href="<?= base_url('data_catalog/dataset/'.$dataset->id) ?>" class="dataset-item">
                        <div class="dataset-icon-box" style="background: <?= $dataset->color ?>;">
                            <i class="<?= $dataset->icon ?>"></i>
                        </div>
                        <div class="dataset-info">
                            <h6 class="dataset-name"><?= $dataset->dataset_name ?></h6>
                            <div class="dataset-meta">
                                <span><i class="fas fa-folder"></i> <?= $dataset->category_name ?></span>
                                <span><i class="fas fa-eye"></i> <?= number_format($dataset->views) ?> ครั้ง</span>
                            </div>
                        </div>
                        <div class="dataset-arrow">
                            <i class="fas fa-chevron-right"></i>
                        </div>
                    </a>
                    <?php endforeach; ?>
                <?php else: ?>
                    <div class="empty-state">
                        <i class="fas fa-inbox"></i>
                        <p>ยังไม่มีข้อมูล</p>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </div>

    <!-- Footer -->
    <div class="footer-section">
        <div class="container">
            <div class="footer-links">
                <a href="<?= base_url('data_catalog/all_datasets') ?>" class="footer-link">
                    <i class="fas fa-list"></i> ข้อมูลทั้งหมด
                </a>
                <a href="<?= base_url('data_catalog/statistics') ?>" class="footer-link">
                    <i class="fas fa-chart-bar"></i> สถิติ
                </a>
                <a href="<?= base_url('data_catalog/api') ?>" class="footer-link" target="_blank">
                    <i class="fas fa-plug"></i> API
                </a>
                <a href="<?= base_url('data_catalog/export_csv') ?>" class="footer-link">
                    <i class="fas fa-download"></i> Export CSV
                </a>
            </div>
            <p class="footer-text">
                <i class="fas fa-shield-alt"></i> ระบบบัญชีรายการข้อมูล | Digital Government Standards
            </p>
        </div>
    </div>

    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    
    <script>
        function switchTab(tabName, element) {
            // Hide all tabs
            document.getElementById('recent-tab').style.display = 'none';
            document.getElementById('popular-tab').style.display = 'none';
            
            // Remove active class from all nav items
            document.querySelectorAll('.nav-link-custom').forEach(nav => {
                nav.classList.remove('active');
            });
            
            // Show selected tab and add active class
            document.getElementById(tabName + '-tab').style.display = 'block';
            element.classList.add('active');
        }

        // Smooth scroll animation
        document.querySelectorAll('a[href^="#"]').forEach(anchor => {
            anchor.addEventListener('click', function (e) {
                e.preventDefault();
                const target = document.querySelector(this.getAttribute('href'));
                if (target) {
                    target.scrollIntoView({
                        behavior: 'smooth',
                        block: 'start'
                    });
                }
            });
        });

        // Add animation on scroll
        const observerOptions = {
            threshold: 0.1,
            rootMargin: '0px 0px -50px 0px'
        };

        const observer = new IntersectionObserver(function(entries) {
            entries.forEach(entry => {
                if (entry.isIntersecting) {
                    entry.target.style.opacity = '1';
                    entry.target.style.transform = 'translateY(0)';
                }
            });
        }, observerOptions);

        // Observe elements for animation
        document.querySelectorAll('.stat-card, .category-item, .dataset-item').forEach(el => {
            observer.observe(el);
        });
    </script>
</body>
</html>