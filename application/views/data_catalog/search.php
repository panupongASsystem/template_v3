<!DOCTYPE html>
<html lang="th">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="description" content="<?= isset($search_query) ? 'ค้นหา: '.$search_query : 'ค้นหาข้อมูล' ?> - <?php echo isset($org['fname']) ? $org['fname'] : 'องค์กรปกครองส่วนท้องถิ่น'; ?>">
    
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

        /* Search Section */
        .search-card {
            background: var(--white);
            border-radius: 16px;
            padding: 2.5rem;
            margin-bottom: 2rem;
            box-shadow: var(--shadow-md);
            animation: fadeInUp 0.6s ease-out 0.1s both;
        }

        .search-title {
            font-family: 'Kanit', sans-serif;
            font-size: 1.5rem;
            font-weight: 600;
            color: var(--gray-800);
            margin-bottom: 2rem;
            display: flex;
            align-items: center;
            gap: 0.75rem;
        }

        .search-input-wrapper {
            position: relative;
            margin-bottom: 1.5rem;
        }

        .search-input-wrapper input {
            width: 100%;
            padding: 1.25rem 3.5rem;
            border: 2px solid var(--gray-200);
            border-radius: 12px;
            font-size: 1.1rem;
            transition: all 0.3s ease;
            background: var(--white);
        }

        .search-input-wrapper input:focus {
            outline: none;
            border-color: var(--primary-color);
            box-shadow: 0 0 0 4px rgba(30, 64, 175, 0.1);
        }

        .search-input-wrapper .search-icon {
            position: absolute;
            left: 1.25rem;
            top: 50%;
            transform: translateY(-50%);
            color: var(--secondary-color);
            font-size: 1.25rem;
        }

        .search-input-wrapper .clear-search {
            position: absolute;
            right: 1.25rem;
            top: 50%;
            transform: translateY(-50%);
            color: var(--secondary-color);
            cursor: pointer;
            font-size: 1.25rem;
            display: none;
            transition: all 0.3s ease;
        }

        .search-input-wrapper .clear-search:hover {
            color: var(--primary-color);
        }

        /* Filter Section */
        .filter-section {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
            gap: 1.5rem;
            margin-bottom: 2rem;
        }

        .filter-group label {
            display: block;
            font-weight: 600;
            color: var(--gray-800);
            margin-bottom: 0.75rem;
            font-size: 1rem;
            display: flex;
            align-items: center;
            gap: 0.5rem;
        }

        .filter-group select {
            width: 100%;
            padding: 0.875rem 1rem;
            border: 2px solid var(--gray-200);
            border-radius: 10px;
            background: var(--white);
            transition: all 0.3s ease;
            font-size: 1rem;
        }

        .filter-group select:focus {
            outline: none;
            border-color: var(--primary-color);
            box-shadow: 0 0 0 4px rgba(30, 64, 175, 0.1);
        }

        /* Buttons */
        .button-group {
            display: flex;
            gap: 1rem;
            flex-wrap: wrap;
        }

        .btn-search, .btn-reset {
            padding: 1rem 2.5rem;
            border: none;
            border-radius: 12px;
            font-weight: 600;
            font-size: 1.05rem;
            cursor: pointer;
            transition: all 0.3s ease;
            display: inline-flex;
            align-items: center;
            gap: 0.75rem;
        }

        .btn-search {
            background: var(--primary-light);
            color: var(--white);
            box-shadow: var(--shadow);
        }

        .btn-search:hover {
            background: var(--primary-color);
            transform: translateY(-3px);
            box-shadow: var(--shadow-lg);
        }

        .btn-reset {
            background: var(--gray-100);
            color: var(--gray-600);
        }

        .btn-reset:hover {
            background: var(--gray-200);
        }

        /* Results Section */
        .results-header {
            background: var(--white);
            padding: 1.5rem 2rem;
            border-radius: 12px;
            margin-bottom: 1.5rem;
            box-shadow: var(--shadow);
            display: flex;
            justify-content: space-between;
            align-items: center;
            flex-wrap: wrap;
            gap: 1rem;
            animation: fadeInUp 0.6s ease-out 0.2s both;
        }

        .results-info {
            font-size: 1.1rem;
            color: var(--gray-800);
        }

        .results-info strong {
            color: var(--primary-color);
            font-size: 1.5rem;
            font-weight: 700;
        }

        .search-term {
            color: var(--primary-color);
            font-weight: 600;
        }

        .sort-options select {
            padding: 0.75rem 1.25rem;
            border: 2px solid var(--gray-200);
            border-radius: 10px;
            background: var(--white);
            font-size: 1rem;
            cursor: pointer;
            transition: all 0.3s ease;
        }

        .sort-options select:focus {
            outline: none;
            border-color: var(--primary-color);
            box-shadow: 0 0 0 4px rgba(30, 64, 175, 0.1);
        }

        /* Result Item */
        .result-item {
            background: var(--white);
            border-radius: 12px;
            padding: 1.75rem;
            margin-bottom: 1.25rem;
            text-decoration: none;
            color: inherit;
            display: block;
            box-shadow: var(--shadow);
            transition: all 0.3s ease;
            animation: fadeInUp 0.6s ease-out both;
        }

        .result-item:hover {
            transform: translateY(-5px);
            box-shadow: var(--shadow-lg);
        }

        .result-header {
            display: flex;
            gap: 1.5rem;
            align-items: start;
        }

        .result-icon {
            width: 60px;
            height: 60px;
            border-radius: 12px;
            display: flex;
            align-items: center;
            justify-content: center;
            flex-shrink: 0;
            box-shadow: var(--shadow);
        }

        .result-icon i {
            font-size: 1.75rem;
            color: var(--white);
        }

        .result-content h5 {
            font-family: 'Kanit', sans-serif;
            font-size: 1.35rem;
            font-weight: 600;
            color: var(--gray-800);
            margin-bottom: 0.75rem;
        }

        .result-content p {
            color: var(--gray-600);
            margin-bottom: 1rem;
            line-height: 1.6;
        }

        .result-meta {
            display: flex;
            flex-wrap: wrap;
            gap: 1rem;
            align-items: center;
        }

        .category-badge {
            background: var(--gray-100);
            color: var(--gray-800);
            padding: 0.5rem 1rem;
            border-radius: 8px;
            font-weight: 600;
            font-size: 0.9rem;
            display: inline-flex;
            align-items: center;
            gap: 0.5rem;
        }

        .meta-item {
            color: var(--secondary-color);
            font-size: 0.95rem;
            display: inline-flex;
            align-items: center;
            gap: 0.375rem;
        }

        /* No Results */
        .no-results {
            background: var(--white);
            border-radius: 16px;
            padding: 4rem 2rem;
            text-align: center;
            box-shadow: var(--shadow-md);
            animation: fadeInUp 0.6s ease-out 0.3s both;
        }

        .no-results i {
            font-size: 5rem;
            color: var(--gray-300);
            margin-bottom: 1.5rem;
        }

        .no-results h4 {
            font-family: 'Kanit', sans-serif;
            font-size: 1.75rem;
            font-weight: 600;
            color: var(--gray-800);
            margin-bottom: 1rem;
        }

        .no-results p {
            color: var(--secondary-color);
            font-size: 1.1rem;
            margin-bottom: 2rem;
        }

        .suggestions {
            background: var(--gray-50);
            padding: 2rem;
            border-radius: 12px;
            text-align: left;
            max-width: 600px;
            margin: 0 auto;
        }

        .suggestions h6 {
            font-family: 'Kanit', sans-serif;
            font-weight: 600;
            color: var(--gray-800);
            margin-bottom: 1rem;
            display: flex;
            align-items: center;
            gap: 0.5rem;
        }

        .suggestions ul {
            list-style: none;
            padding: 0;
        }

        .suggestions li {
            padding: 0.5rem 0;
            color: var(--gray-600);
            padding-left: 1.5rem;
            position: relative;
        }

        .suggestions li:before {
            content: "→";
            position: absolute;
            left: 0;
            color: var(--primary-color);
            font-weight: bold;
        }

        /* Pagination */
        .pagination-wrapper {
            display: flex;
            justify-content: center;
            margin-top: 2rem;
            animation: fadeInUp 0.6s ease-out 0.4s both;
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

            .search-card {
                padding: 1.5rem;
            }

            .filter-section {
                grid-template-columns: 1fr;
            }

            .results-header {
                flex-direction: column;
                align-items: stretch;
            }

            .result-header {
                flex-direction: column;
            }

            .result-icon {
                width: 50px;
                height: 50px;
            }

            .button-group {
                flex-direction: column;
            }

            .btn-search,
            .btn-reset {
                width: 100%;
                justify-content: center;
            }
        }
		
		
		

        /* Animation delays for results */
        .result-item:nth-child(1) { animation-delay: 0.3s; }
        .result-item:nth-child(2) { animation-delay: 0.35s; }
        .result-item:nth-child(3) { animation-delay: 0.4s; }
        .result-item:nth-child(4) { animation-delay: 0.45s; }
        .result-item:nth-child(5) { animation-delay: 0.5s; }
        .result-item:nth-child(6) { animation-delay: 0.55s; }
		
		
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
                        <li class="breadcrumb-item active">ค้นหา</li>
                    </ol>
                </nav>
            </div>
        </div>

        <!-- Search Form -->
        <div class="search-card">
            <h2 class="search-title">
                <i class="fas fa-search"></i>
                ค้นหาข้อมูล
            </h2>

            <form action="<?= base_url('data_catalog/search') ?>" method="get">
                <div class="search-input-wrapper">
                    <i class="fas fa-search search-icon"></i>
                    <input type="text" 
                           name="q" 
                           id="searchInput"
                           value="<?= isset($search_query) ? htmlspecialchars($search_query) : '' ?>"
                           placeholder="ค้นหาชื่อข้อมูล, คำอธิบาย, หรือคำสำคัญ..." 
                           required>
                    <i class="fas fa-times-circle clear-search" id="clearSearch"></i>
                </div>
                
                <div class="filter-section">
                    <div class="filter-group">
                        <label><i class="fas fa-folder"></i> หมวดหมู่</label>
                        <select name="category" id="categoryFilter">
                            <option value="">ทุกหมวดหมู่</option>
                            <?php if (!empty($categories)): ?>
                                <?php foreach ($categories as $cat): ?>
                                <option value="<?= $cat->id ?>" <?= (isset($_GET['category']) && $_GET['category'] == $cat->id) ? 'selected' : '' ?>>
                                    <?= $cat->category_name ?>
                                </option>
                                <?php endforeach; ?>
                            <?php endif; ?>
                        </select>
                    </div>
                    
                    <div class="filter-group">
                        <label><i class="fas fa-file-code"></i> รูปแบบข้อมูล</label>
                        <select name="format" id="formatFilter">
                            <option value="">ทุกรูปแบบ</option>
                            <option value="Database" <?= (isset($_GET['format']) && $_GET['format'] == 'Database') ? 'selected' : '' ?>>Database</option>
                            <option value="JSON" <?= (isset($_GET['format']) && $_GET['format'] == 'JSON') ? 'selected' : '' ?>>JSON</option>
                            <option value="CSV" <?= (isset($_GET['format']) && $_GET['format'] == 'CSV') ? 'selected' : '' ?>>CSV</option>
                            <option value="XML" <?= (isset($_GET['format']) && $_GET['format'] == 'XML') ? 'selected' : '' ?>>XML</option>
                            <option value="Excel" <?= (isset($_GET['format']) && $_GET['format'] == 'Excel') ? 'selected' : '' ?>>Excel</option>
                        </select>
                    </div>
                    
                    <div class="filter-group">
                        <label><i class="fas fa-lock-open"></i> การเข้าถึง</label>
                        <select name="access" id="accessFilter">
                            <option value="">ทุกระดับ</option>
                            <option value="public" <?= (isset($_GET['access']) && $_GET['access'] == 'public') ? 'selected' : '' ?>>เปิดเผยทั่วไป</option>
                            <option value="restricted" <?= (isset($_GET['access']) && $_GET['access'] == 'restricted') ? 'selected' : '' ?>>จำกัดสิทธิ์</option>
                            <option value="private" <?= (isset($_GET['access']) && $_GET['access'] == 'private') ? 'selected' : '' ?>>ส่วนตัว</option>
                        </select>
                    </div>
                </div>
                
                <div class="button-group">
                    <button type="submit" class="btn-search">
                        <i class="fas fa-search"></i> ค้นหา
                    </button>
                    <button type="button" class="btn-reset" onclick="resetSearch()">
                        <i class="fas fa-redo"></i> ล้างค่า
                    </button>
                </div>
            </form>
        </div>

        <?php if (isset($search_query) && !empty($search_query)): ?>
        <!-- Results Header -->
        <div class="results-header">
            <div class="results-info">
                พบ <strong><?= number_format($total_results) ?></strong> รายการ 
                จากคำค้นหา: <span class="search-term">"<?= htmlspecialchars($search_query) ?>"</span>
            </div>
            <div class="sort-options">
                <select id="sortResults" onchange="sortResults(this.value)">
                    <option value="relevance">เรียงตามความเกี่ยวข้อง</option>
                    <option value="newest">ล่าสุด</option>
                    <option value="updated">อัปเดตล่าสุด</option>
                    <option value="name">ชื่อ A-Z</option>
                    <option value="popular">ยอดนิยม</option>
                </select>
            </div>
        </div>

        <!-- Results -->
        <?php if (!empty($datasets)): ?>
            <?php foreach ($datasets as $item): ?>
            <a href="<?= base_url('data_catalog/dataset/'.$item->id) ?>" class="result-item">
                <div class="result-header">
                    <div class="result-icon" style="background: <?= $item->color ?>;">
                        <i class="<?= $item->icon ?>"></i>
                    </div>
                    <div class="result-content flex-grow-1">
                        <h5><?= $item->dataset_name ?></h5>
                        <?php if (isset($item->description) && !empty($item->description)): ?>
                        <p><?= word_limiter($item->description, 30) ?></p>
                        <?php endif; ?>
                        <div class="result-meta">
                            <span class="category-badge">
                                <i class="<?= $item->icon ?>"></i> <?= $item->category_name ?>
                            </span>
                            <?php if (isset($item->data_format) && !empty($item->data_format)): ?>
                            <span class="meta-item">
                                <i class="fas fa-file-code"></i>
                                <span><?= $item->data_format ?></span>
                            </span>
                            <?php endif; ?>
                            <?php if (isset($item->updated_at)): ?>
                            <span class="meta-item">
                                <i class="fas fa-calendar"></i>
                                <span><?= date('d/m/Y', strtotime($item->updated_at)) ?></span>
                            </span>
                            <?php endif; ?>
                            <?php if (isset($item->views)): ?>
                            <span class="meta-item">
                                <i class="fas fa-eye"></i>
                                <span><?= number_format($item->views) ?> ครั้ง</span>
                            </span>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>
            </a>
            <?php endforeach; ?>
            
            <!-- Pagination -->
            <?php if (!empty($pagination)): ?>
            <div class="pagination-wrapper">
                <?= $pagination ?>
            </div>
            <?php endif; ?>
            
        <?php else: ?>
            <!-- No Results -->
            <div class="no-results">
                <i class="fas fa-search-minus"></i>
                <h4>ไม่พบข้อมูลที่ค้นหา</h4>
                <p>ไม่พบข้อมูลที่ตรงกับ "<?= htmlspecialchars($search_query) ?>"</p>
                
                <div class="suggestions">
                    <h6><i class="fas fa-lightbulb"></i> คำแนะนำ:</h6>
                    <ul>
                        <li>ตรวจสอบการสะกดคำให้ถูกต้อง</li>
                        <li>ลองใช้คำค้นหาที่กว้างขึ้น</li>
                        <li>ลองใช้คำค้นหาอื่น หรือคำสำคัญที่เกี่ยวข้อง</li>
                        <li>ลองเลือกหมวดหมู่หรือตัวกรองอื่น</li>
                    </ul>
                </div>
            </div>
        <?php endif; ?>
        <?php endif; ?>
    </div>

    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    
    <script>
    // Clear search
    const searchInput = document.getElementById('searchInput');
    const clearBtn = document.getElementById('clearSearch');
    
    searchInput.addEventListener('input', function() {
        clearBtn.style.display = this.value ? 'block' : 'none';
    });
    
    clearBtn.addEventListener('click', function() {
        searchInput.value = '';
        this.style.display = 'none';
        searchInput.focus();
    });
    
    // Show clear button if has value
    if (searchInput.value) {
        clearBtn.style.display = 'block';
    }
    
    // Reset search
    function resetSearch() {
        document.getElementById('searchInput').value = '';
        document.getElementById('categoryFilter').value = '';
        document.getElementById('formatFilter').value = '';
        document.getElementById('accessFilter').value = '';
        clearBtn.style.display = 'none';
    }
    
    // Sort results
    function sortResults(sortBy) {
        const url = new URL(window.location.href);
        url.searchParams.set('sort', sortBy);
        window.location.href = url.toString();
    }
    </script>
</body>
</html>