<!DOCTYPE html>
<html lang="th">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= $page_title ?></title>
    
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@4.6.2/dist/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Sarabun:wght@300;400;500;600;700&display=swap" rel="stylesheet">
	<link rel="icon" href="<?php echo base_url("docs/logo.png"); ?>" type="image/x-icon">
    
    <style>
        * {
            font-family: 'Sarabun', sans-serif;
        }
        
        body {
            background: #f8f9fa;
            min-height: 100vh;
            padding: 30px 0;
        }
        
        /* Header */
        .page-header {
            background: white;
            border-left: 5px solid #0066cc;
            padding: 25px 30px;
            border-radius: 8px;
            margin-bottom: 25px;
            box-shadow: 0 2px 8px rgba(0,0,0,0.05);
        }
        
        .page-header h1 {
            font-size: 2rem;
            font-weight: 700;
            color: #1a1a1a;
            margin: 0 0 10px 0;
        }
        
        .page-header .subtitle {
            color: #666;
            font-size: 1.05rem;
            margin: 0;
        }
        
        .breadcrumb {
            background: none;
            padding: 0;
            margin: 10px 0 0 0;
        }
        
        .breadcrumb-item a {
            color: #0066cc;
            text-decoration: none;
        }
        
        /* Filter Bar */
        .filter-bar {
            background: white;
            padding: 20px 25px;
            border-radius: 8px;
            margin-bottom: 20px;
            box-shadow: 0 2px 8px rgba(0,0,0,0.05);
        }
        
        .filter-controls {
            display: flex;
            gap: 15px;
            flex-wrap: wrap;
            align-items: center;
        }
        
        .filter-group {
            flex: 1;
            min-width: 200px;
        }
        
        .filter-group label {
            display: block;
            font-weight: 600;
            color: #333;
            margin-bottom: 8px;
            font-size: 0.9rem;
        }
        
        .filter-group select {
            width: 100%;
            padding: 10px 15px;
            border: 2px solid #e0e0e0;
            border-radius: 6px;
            background: white;
            transition: all 0.3s;
        }
        
        .filter-group select:focus {
            outline: none;
            border-color: #0066cc;
            box-shadow: 0 0 0 3px rgba(0,102,204,0.1);
        }
        
        .view-toggles {
            display: flex;
            gap: 10px;
            margin-left: auto;
        }
        
        .view-btn {
            width: 40px;
            height: 40px;
            border: 2px solid #e0e0e0;
            background: white;
            border-radius: 6px;
            display: flex;
            align-items: center;
            justify-content: center;
            cursor: pointer;
            transition: all 0.3s;
            color: #666;
        }
        
        .view-btn:hover,
        .view-btn.active {
            border-color: #0066cc;
            background: #0066cc;
            color: white;
        }
        
        /* Results Info */
        .results-info {
            background: white;
            padding: 15px 25px;
            border-radius: 8px;
            margin-bottom: 20px;
            box-shadow: 0 2px 8px rgba(0,0,0,0.05);
            display: flex;
            justify-content: space-between;
            align-items: center;
        }
        
        .results-count {
            font-size: 1.1rem;
            color: #333;
        }
        
        .results-count strong {
            color: #0066cc;
            font-size: 1.3rem;
        }
        
        .export-btns {
            display: flex;
            gap: 10px;
        }
        
        .btn-export {
            background: #f0f0f0;
            color: #666;
            padding: 8px 16px;
            border: none;
            border-radius: 6px;
            font-weight: 600;
            font-size: 0.9rem;
            cursor: pointer;
            transition: all 0.3s;
            text-decoration: none;
            display: inline-flex;
            align-items: center;
            gap: 8px;
        }
        
        .btn-export:hover {
            background: #0066cc;
            color: white;
            transform: translateY(-2px);
        }
        
        /* Grid View */
        .datasets-grid {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(320px, 1fr));
            gap: 20px;
            margin-bottom: 30px;
        }
        
        .dataset-card {
            background: white;
            border-radius: 8px;
            padding: 25px;
            box-shadow: 0 2px 8px rgba(0,0,0,0.05);
            border-top: 4px solid #0066cc;
            transition: all 0.3s;
            text-decoration: none;
            color: inherit;
            display: flex;
            flex-direction: column;
        }
        
        .dataset-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 8px 20px rgba(0,102,204,0.15);
            text-decoration: none;
            color: inherit;
        }
        
        .dataset-card .icon-wrapper {
            width: 60px;
            height: 60px;
            border-radius: 10px;
            display: flex;
            align-items: center;
            justify-content: center;
            color: white;
            font-size: 26px;
            margin-bottom: 15px;
        }
        
        .dataset-card h5 {
            font-size: 1.2rem;
            font-weight: 700;
            color: #1a1a1a;
            margin-bottom: 10px;
            line-height: 1.4;
        }
        
        .dataset-card p {
            color: #666;
            font-size: 0.95rem;
            line-height: 1.6;
            margin-bottom: 15px;
            flex-grow: 1;
        }
        
        .dataset-card .meta {
            display: flex;
            gap: 15px;
            flex-wrap: wrap;
            padding-top: 15px;
            border-top: 1px solid #f0f0f0;
            font-size: 0.85rem;
            color: #666;
        }
        
        .dataset-card .meta span {
            display: flex;
            align-items: center;
            gap: 5px;
        }
        
        .dataset-card .meta i {
            color: #0066cc;
        }
        
        /* List View */
        .datasets-list {
            display: none;
        }
        
        .datasets-list.active {
            display: block;
        }
        
        .datasets-grid.active {
            display: grid;
        }
        
        .dataset-row {
            background: white;
            border-radius: 8px;
            padding: 20px 25px;
            margin-bottom: 15px;
            box-shadow: 0 2px 8px rgba(0,0,0,0.05);
            border-left: 4px solid #0066cc;
            transition: all 0.3s;
            text-decoration: none;
            color: inherit;
            display: flex;
            align-items: center;
            gap: 20px;
        }
        
        .dataset-row:hover {
            transform: translateX(8px);
            box-shadow: 0 4px 12px rgba(0,102,204,0.15);
            text-decoration: none;
            color: inherit;
        }
        
        .dataset-row .icon-wrapper {
            width: 55px;
            height: 55px;
            border-radius: 10px;
            display: flex;
            align-items: center;
            justify-content: center;
            color: white;
            font-size: 24px;
            flex-shrink: 0;
        }
        
        .dataset-row .content {
            flex-grow: 1;
        }
        
        .dataset-row h5 {
            font-size: 1.15rem;
            font-weight: 700;
            color: #1a1a1a;
            margin-bottom: 8px;
        }
        
        .dataset-row .description {
            color: #666;
            font-size: 0.9rem;
            margin-bottom: 10px;
        }
        
        .dataset-row .meta {
            display: flex;
            gap: 20px;
            flex-wrap: wrap;
            font-size: 0.85rem;
            color: #666;
        }
        
        .dataset-row .meta span {
            display: flex;
            align-items: center;
            gap: 5px;
        }
        
        .dataset-row .meta i {
            color: #0066cc;
        }
        
        .dataset-row .arrow {
            color: #ccc;
            font-size: 20px;
            flex-shrink: 0;
        }
        
        /* Badge */
        .badge-category {
            display: inline-block;
            background: #f0f5ff;
            color: #0066cc;
            padding: 4px 10px;
            border-radius: 12px;
            font-size: 0.8rem;
            font-weight: 600;
            border: 1px solid #d1e7ff;
        }
        
        /* Pagination */
        .pagination-wrapper {
            background: white;
            padding: 20px;
            border-radius: 8px;
            margin-top: 20px;
            box-shadow: 0 2px 8px rgba(0,0,0,0.05);
        }
        
        .pagination {
            margin: 0;
            justify-content: center;
        }
        
        .pagination .page-link {
            color: #0066cc;
            border: 1px solid #e0e0e0;
            margin: 0 3px;
            border-radius: 6px;
            font-weight: 500;
        }
        
        .pagination .page-item.active .page-link {
            background: #0066cc;
            border-color: #0066cc;
        }
        
        /* Empty State */
        .empty-state {
            background: white;
            padding: 60px 30px;
            border-radius: 8px;
            text-align: center;
            box-shadow: 0 2px 8px rgba(0,0,0,0.05);
        }
        
        .empty-state i {
            font-size: 80px;
            color: #ccc;
            margin-bottom: 20px;
        }
        
        .empty-state h4 {
            color: #333;
            margin-bottom: 15px;
        }
        
        .empty-state p {
            color: #666;
        }
        
        /* Responsive */
        @media (max-width: 768px) {
            .datasets-grid {
                grid-template-columns: 1fr;
            }
            
            .filter-controls {
                flex-direction: column;
            }
            
            .filter-group {
                width: 100%;
            }
            
            .view-toggles {
                margin-left: 0;
                width: 100%;
            }
            
            .results-info {
                flex-direction: column;
                gap: 15px;
            }
            
            .dataset-row {
                flex-direction: column;
                align-items: flex-start;
            }
            
            .dataset-row .arrow {
                display: none;
            }
        }
    </style>
</head>
<body>
    <div class="container">
        <!-- Header -->
        <div class="page-header">
            <h1><i class="fas fa-database"></i> ชุดข้อมูลทั้งหมด</h1>
            <p class="subtitle">รายการชุดข้อมูลทั้งหมดในระบบ</p>
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb">
                    <li class="breadcrumb-item"><a href="<?= base_url('data_catalog') ?>"><i class="fas fa-home"></i> หน้าหลัก</a></li>
                    <li class="breadcrumb-item active">ชุดข้อมูลทั้งหมด</li>
                </ol>
            </nav>
        </div>

        <!-- Filter Bar -->
        <div class="filter-bar">
            <form action="<?= base_url('data_catalog/all_datasets') ?>" method="get">
                <div class="filter-controls">
                    <div class="filter-group">
                        <label><i class="fas fa-folder"></i> หมวดหมู่</label>
                        <select name="category" onchange="this.form.submit()">
                            <option value="">ทุกหมวดหมู่</option>
                            <?php if (!empty($categories)): ?>
                                <?php foreach ($categories as $cat): ?>
                                <option value="<?= $cat->id ?>" <?= (isset($selected_category) && $selected_category == $cat->id) ? 'selected' : '' ?>>
                                    <?= $cat->category_name ?> (<?= $cat->dataset_count ?>)
                                </option>
                                <?php endforeach; ?>
                            <?php endif; ?>
                        </select>
                    </div>
                    
                    <div class="filter-group">
                        <label><i class="fas fa-sort"></i> เรียงตาม</label>
                        <select name="sort" onchange="this.form.submit()">
                            <option value="newest" <?= (isset($sort) && $sort == 'newest') ? 'selected' : '' ?>>ล่าสุด</option>
                            <option value="oldest" <?= (isset($sort) && $sort == 'oldest') ? 'selected' : '' ?>>เก่าสุด</option>
                            <option value="name_asc" <?= (isset($sort) && $sort == 'name_asc') ? 'selected' : '' ?>>ชื่อ A-Z</option>
                            <option value="name_desc" <?= (isset($sort) && $sort == 'name_desc') ? 'selected' : '' ?>>ชื่อ Z-A</option>
                            <option value="views" <?= (isset($sort) && $sort == 'views') ? 'selected' : '' ?>>ยอดนิยม</option>
                        </select>
                    </div>
                    
                    <div class="view-toggles">
                        <button type="button" class="view-btn active" id="gridViewBtn" onclick="switchView('grid')">
                            <i class="fas fa-th"></i>
                        </button>
                        <button type="button" class="view-btn" id="listViewBtn" onclick="switchView('list')">
                            <i class="fas fa-list"></i>
                        </button>
                    </div>
                </div>
            </form>
        </div>

        <!-- Results Info -->
        <div class="results-info">
            <div class="results-count">
                ทั้งหมด <strong><?= number_format($total_datasets) ?></strong> ชุดข้อมูล
            </div>
            <div class="export-btns">
                <a href="<?= base_url('data_catalog/export_csv') ?>" class="btn-export">
                    <i class="fas fa-file-csv"></i>
                    <span>Export CSV</span>
                </a>
                <a href="<?= base_url('data_catalog/export_json') ?>" class="btn-export">
                    <i class="fas fa-file-code"></i>
                    <span>Export JSON</span>
                </a>
            </div>
        </div>

        <?php if (!empty($datasets)): ?>
        <!-- Grid View -->
        <div class="datasets-grid active" id="gridView">
            <?php foreach ($datasets as $dataset): ?>
            <a href="<?= base_url('data_catalog/dataset/'.$dataset->id) ?>" class="dataset-card" style="border-color: <?= $dataset->color ?>;">
                <div class="icon-wrapper" style="background: <?= $dataset->color ?>;">
                    <i class="<?= $dataset->icon ?>"></i>
                </div>
                <h5><?= $dataset->dataset_name ?></h5>
                <?php if (isset($dataset->description) && !empty($dataset->description)): ?>
                <p><?= word_limiter($dataset->description, 20) ?></p>
                <?php else: ?>
                <p>ไม่มีคำอธิบาย</p>
                <?php endif; ?>
                <div class="meta">
                    <span class="badge-category" style="background: <?= $dataset->color ?>20; color: <?= $dataset->color ?>; border-color: <?= $dataset->color ?>40;">
                        <?= $dataset->category_name ?>
                    </span>
                    <?php if (isset($dataset->updated_at)): ?>
                    <span>
                        <i class="fas fa-calendar"></i>
                        <?= date('d/m/Y', strtotime($dataset->updated_at)) ?>
                    </span>
                    <?php endif; ?>
                    <?php if (isset($dataset->views)): ?>
                    <span>
                        <i class="fas fa-eye"></i>
                        <?= number_format($dataset->views) ?>
                    </span>
                    <?php endif; ?>
                </div>
            </a>
            <?php endforeach; ?>
        </div>

        <!-- List View -->
        <div class="datasets-list" id="listView">
            <?php foreach ($datasets as $dataset): ?>
            <a href="<?= base_url('data_catalog/dataset/'.$dataset->id) ?>" class="dataset-row" style="border-color: <?= $dataset->color ?>;">
                <div class="icon-wrapper" style="background: <?= $dataset->color ?>;">
                    <i class="<?= $dataset->icon ?>"></i>
                </div>
                <div class="content">
                    <h5><?= $dataset->dataset_name ?></h5>
                    <?php if (isset($dataset->description) && !empty($dataset->description)): ?>
                    <div class="description"><?= word_limiter($dataset->description, 30) ?></div>
                    <?php endif; ?>
                    <div class="meta">
                        <span class="badge-category" style="background: <?= $dataset->color ?>20; color: <?= $dataset->color ?>; border-color: <?= $dataset->color ?>40;">
                            <?= $dataset->category_name ?>
                        </span>
                        <?php if (isset($dataset->data_format) && !empty($dataset->data_format)): ?>
                        <span>
                            <i class="fas fa-file-code"></i>
                            <?= $dataset->data_format ?>
                        </span>
                        <?php endif; ?>
                        <?php if (isset($dataset->updated_at)): ?>
                        <span>
                            <i class="fas fa-calendar"></i>
                            <?= date('d/m/Y', strtotime($dataset->updated_at)) ?>
                        </span>
                        <?php endif; ?>
                        <?php if (isset($dataset->views)): ?>
                        <span>
                            <i class="fas fa-eye"></i>
                            <?= number_format($dataset->views) ?> ครั้ง
                        </span>
                        <?php endif; ?>
                    </div>
                </div>
                <i class="fas fa-chevron-right arrow"></i>
            </a>
            <?php endforeach; ?>
        </div>

        <!-- Pagination -->
        <?php if (!empty($pagination)): ?>
        <div class="pagination-wrapper">
            <?= $pagination ?>
        </div>
        <?php endif; ?>
        
        <?php else: ?>
        <!-- Empty State -->
        <div class="empty-state">
            <i class="fas fa-inbox"></i>
            <h4>ไม่มีข้อมูล</h4>
            <p>ยังไม่มีชุดข้อมูลในระบบ</p>
        </div>
        <?php endif; ?>
    </div>

    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@4.6.2/dist/js/bootstrap.bundle.min.js"></script>
    
    <script>
    // Switch between grid and list view
    function switchView(view) {
        const gridView = document.getElementById('gridView');
        const listView = document.getElementById('listView');
        const gridBtn = document.getElementById('gridViewBtn');
        const listBtn = document.getElementById('listViewBtn');
        
        if (view === 'grid') {
            gridView.classList.add('active');
            listView.classList.remove('active');
            gridBtn.classList.add('active');
            listBtn.classList.remove('active');
            localStorage.setItem('viewMode', 'grid');
        } else {
            gridView.classList.remove('active');
            listView.classList.add('active');
            gridBtn.classList.remove('active');
            listBtn.classList.add('active');
            localStorage.setItem('viewMode', 'list');
        }
    }
    
    // Load saved view mode
    document.addEventListener('DOMContentLoaded', function() {
        const savedView = localStorage.getItem('viewMode') || 'grid';
        switchView(savedView);
    });
    </script>
</body>
</html>
