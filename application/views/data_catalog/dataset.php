<!DOCTYPE html>
<html lang="th">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="description" content="<?= $dataset->dataset_name ?> - <?php echo isset($org['fname']) ? $org['fname'] : 'องค์กรปกครองส่วนท้องถิ่น'; ?>">
    <meta name="keywords" content="<?= isset($dataset->keywords) ? $dataset->keywords : 'Open Data, บัญชีรายการข้อมูล' ?>">
    
    <title><?= $dataset->dataset_name ?> - <?php echo isset($org['fname']) ? $org['fname'] : 'องค์กรปกครองส่วนท้องถิ่น'; ?></title>
    
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

        /* Main Content */
        .content-card {
            background: var(--white);
            border-radius: 16px;
            padding: 2.5rem;
            margin-bottom: 2rem;
            box-shadow: var(--shadow-md);
            transition: all 0.3s ease;
            animation: fadeInUp 0.6s ease-out;
        }

        .content-card:hover {
            box-shadow: var(--shadow-lg);
        }

        /* Dataset Header */
        .dataset-header {
            border-left: 5px solid <?= isset($dataset->color) ? $dataset->color : 'var(--primary-color)' ?>;
            padding-left: 2rem;
            margin-bottom: 2rem;
        }

        .dataset-header h1 {
            font-family: 'Kanit', sans-serif;
            font-size: 2.25rem;
            font-weight: 700;
            color: var(--gray-800);
            margin-bottom: 0.75rem;
        }

        .dataset-header .subtitle {
            color: var(--secondary-color);
            font-size: 1.1rem;
        }

        /* Category Badge */
        .category-badge {
            display: inline-flex;
            align-items: center;
            gap: 0.75rem;
            background: <?= isset($dataset->color) ? $dataset->color : 'var(--primary-color)' ?>;
            color: var(--white);
            padding: 0.875rem 1.75rem;
            border-radius: 12px;
            font-weight: 600;
            font-size: 1.1rem;
            margin-bottom: 1.5rem;
            box-shadow: var(--shadow);
            transition: all 0.3s ease;
        }

        .category-badge:hover {
            transform: translateY(-2px);
            box-shadow: var(--shadow-md);
        }

        .category-badge i {
            font-size: 1.5rem;
        }

        /* Info Badges */
        .info-badges {
            display: flex;
            flex-wrap: wrap;
            gap: 0.75rem;
            margin-bottom: 2rem;
        }

        .info-badge {
            display: inline-flex;
            align-items: center;
            gap: 0.5rem;
            padding: 0.625rem 1.25rem;
            border-radius: 25px;
            font-size: 0.95rem;
            font-weight: 500;
            border: 2px solid;
            transition: all 0.3s ease;
        }

        .info-badge:hover {
            transform: translateY(-2px);
        }

        .badge-format {
            background: #e6f7ff;
            color: #0066cc;
            border-color: #b3e0ff;
        }

        .badge-access {
            background: #f0f9ff;
            color: #0066cc;
            border-color: #d1e7ff;
        }

        .badge-frequency {
            background: #fff7e6;
            color: #ff8c00;
            border-color: #ffd699;
        }

        .badge-views {
            background: #f0f0f0;
            color: #666;
            border-color: #d9d9d9;
        }

        /* Description */
        .description-section {
            background: var(--gray-50);
            padding: 2rem;
            border-radius: 12px;
            border-left: 4px solid var(--primary-color);
            margin-bottom: 2rem;
        }

        .description-section h5 {
            font-family: 'Kanit', sans-serif;
            font-weight: 700;
            color: var(--gray-800);
            margin-bottom: 1rem;
            font-size: 1.25rem;
        }

        .description-section p {
            color: var(--gray-600);
            line-height: 1.8;
            margin: 0;
        }

        /* Info Table */
        .info-table {
            margin-bottom: 2rem;
        }

        .info-table h5 {
            font-family: 'Kanit', sans-serif;
            font-weight: 700;
            color: var(--gray-800);
            margin-bottom: 1.5rem;
            font-size: 1.25rem;
            display: flex;
            align-items: center;
            gap: 0.5rem;
        }

        .info-table table {
            width: 100%;
            border-collapse: separate;
            border-spacing: 0;
            border-radius: 12px;
            overflow: hidden;
            box-shadow: var(--shadow-sm);
        }

        .info-table th {
            background: var(--gray-50);
            padding: 1.25rem 1.5rem;
            font-weight: 600;
            color: var(--gray-800);
            width: 30%;
            border-bottom: 1px solid var(--gray-200);
        }

        .info-table td {
            padding: 1.25rem 1.5rem;
            color: var(--gray-600);
            border-bottom: 1px solid var(--gray-200);
            background: var(--white);
        }

        .info-table tr:last-child td,
        .info-table tr:last-child th {
            border-bottom: none;
        }

        .info-table td a {
            color: var(--primary-color);
            text-decoration: none;
            transition: all 0.3s ease;
        }

        .info-table td a:hover {
            color: var(--primary-dark);
        }

        .info-table code {
            background: var(--gray-100);
            padding: 0.375rem 0.75rem;
            border-radius: 6px;
            color: #d63384;
            font-size: 0.9rem;
            font-family: 'Courier New', monospace;
        }

        /* Keywords */
        .keywords-section {
            margin-bottom: 2rem;
        }

        .keywords-section h5 {
            font-family: 'Kanit', sans-serif;
            font-weight: 700;
            color: var(--gray-800);
            margin-bottom: 1rem;
            font-size: 1.25rem;
            display: flex;
            align-items: center;
            gap: 0.5rem;
        }

        .keyword-tag {
            display: inline-block;
            background: #f0f5ff;
            color: var(--primary-color);
            padding: 0.625rem 1.25rem;
            border-radius: 25px;
            margin: 0.375rem 0.5rem 0.375rem 0;
            font-weight: 500;
            border: 1px solid #d1e7ff;
            transition: all 0.3s ease;
        }

        .keyword-tag:hover {
            background: var(--primary-color);
            color: var(--white);
            transform: translateY(-2px);
        }

        /* License Alert */
        .license-alert {
            background: #fff9e6;
            border-left: 4px solid #ffc107;
            padding: 1.25rem 1.5rem;
            border-radius: 8px;
            margin-bottom: 2rem;
            color: var(--gray-800);
        }

        .license-alert strong {
            display: flex;
            align-items: center;
            gap: 0.5rem;
            margin-bottom: 0.5rem;
        }

        /* Action Buttons */
        .action-section {
            display: flex;
            flex-wrap: wrap;
            gap: 1rem;
            margin-top: 2rem;
        }

        .btn-action {
            display: inline-flex;
            align-items: center;
            gap: 0.75rem;
            background: var(--primary-light);
            color: var(--white);
            padding: 1rem 2rem;
            border-radius: 12px;
            text-decoration: none;
            font-weight: 600;
            font-size: 1.05rem;
            box-shadow: var(--shadow);
            transition: all 0.3s ease;
        }

        .btn-action:hover {
            color: var(--white);
            background: var(--primary-color);
            transform: translateY(-3px);
            box-shadow: var(--shadow-lg);
        }

        .btn-action i {
            font-size: 1.25rem;
        }

        /* Metadata Table */
        .metadata-section {
            margin-top: 2rem;
        }

        .section-title {
            font-family: 'Kanit', sans-serif;
            font-size: 1.5rem;
            font-weight: 700;
            color: var(--gray-800);
            margin-bottom: 1.5rem;
            display: flex;
            align-items: center;
            gap: 0.75rem;
        }

        .metadata-table {
            overflow-x: auto;
        }

        .metadata-table table {
            width: 100%;
            border-collapse: separate;
            border-spacing: 0;
            border-radius: 12px;
            overflow: hidden;
            box-shadow: var(--shadow-sm);
        }

        .metadata-table thead th {
            background: var(--primary-light);
            color: var(--white);
            padding: 1.25rem 1.5rem;
            font-weight: 600;
            text-align: left;
            border-bottom: none;
        }

        .metadata-table tbody td {
            padding: 1.25rem 1.5rem;
            border-bottom: 1px solid var(--gray-200);
            background: var(--white);
        }

        .metadata-table tbody tr:hover {
            background: var(--gray-50);
        }

        .metadata-table tbody tr:last-child td {
            border-bottom: none;
        }

        .field-name {
            font-weight: 600;
            color: var(--gray-800);
            font-family: 'Courier New', monospace;
        }

        .type-badge {
            display: inline-block;
            background: var(--gray-100);
            color: var(--gray-800);
            padding: 0.375rem 0.875rem;
            border-radius: 6px;
            font-size: 0.85rem;
            font-weight: 600;
            font-family: 'Courier New', monospace;
        }

        /* Related Datasets */
        .related-section {
            margin-top: 2rem;
        }

        .related-item {
            display: block;
            background: var(--gray-50);
            padding: 1.5rem;
            border-radius: 12px;
            margin-bottom: 1rem;
            text-decoration: none;
            color: inherit;
            border-left: 4px solid var(--primary-color);
            transition: all 0.3s ease;
        }

        .related-item:hover {
            background: var(--white);
            transform: translateX(8px);
            box-shadow: var(--shadow);
        }

        .related-item h6 {
            font-weight: 600;
            color: var(--gray-800);
            margin-bottom: 0.5rem;
            font-size: 1.1rem;
        }

        .related-item small {
            color: var(--secondary-color);
        }

        /* Back Button */
        .back-button {
            display: inline-flex;
            align-items: center;
            gap: 0.5rem;
            color: var(--primary-color);
            text-decoration: none;
            font-weight: 600;
            padding: 0.75rem 1.5rem;
            border-radius: 10px;
            transition: all 0.3s ease;
            margin-bottom: 1rem;
        }

        .back-button:hover {
            background: var(--gray-50);
            color: var(--primary-dark);
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

            .dataset-header h1 {
                font-size: 1.75rem;
            }

            .content-card {
                padding: 1.5rem;
            }

            .info-table th,
            .info-table td {
                padding: 1rem;
                display: block;
                width: 100%;
            }

            .info-table th {
                background: var(--primary-light);
                color: var(--white);
                border-bottom: none;
            }

            .action-section {
                flex-direction: column;
            }

            .btn-action {
                width: 100%;
                justify-content: center;
            }
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
                        <li class="breadcrumb-item"><a href="<?= base_url('data_catalog') ?>"><i class="fas fa-home"></i> หน้าแรก</a></li>
                        <li class="breadcrumb-item"><a href="<?= base_url('data_catalog/category/'.$dataset->category_id) ?>"><?= $dataset->category_name ?></a></li>
                        <li class="breadcrumb-item active"><?= $dataset->dataset_name ?></li>
                    </ol>
                </nav>
            </div>
        </div>

        <!-- Back Button -->
        <a href="<?= base_url('data_catalog/category/'.$dataset->category_id) ?>" class="back-button">
            <i class="fas fa-arrow-left"></i> กลับไปหมวดหมู่
        </a>

        <!-- Main Content -->
        <div class="content-card">
            <!-- Dataset Header -->
            <div class="dataset-header">
                <h1><?= $dataset->dataset_name ?></h1>
                <?php if (isset($dataset->subtitle) && !empty($dataset->subtitle)): ?>
                <p class="subtitle"><?= $dataset->subtitle ?></p>
                <?php endif; ?>
            </div>

            <!-- Category Badge -->
            <div class="category-badge">
                <i class="<?= $dataset->icon ?>"></i>
                <span><?= $dataset->category_name ?></span>
            </div>

            <!-- Info Badges -->
            <div class="info-badges">
                <?php if (isset($dataset->format) && !empty($dataset->format)): ?>
                <span class="info-badge badge-format">
                    <i class="fas fa-file"></i>
                    <span><?= $dataset->format ?></span>
                </span>
                <?php endif; ?>
                
                <?php if (isset($dataset->access_level) && !empty($dataset->access_level)): ?>
                <span class="info-badge badge-access">
                    <i class="fas fa-lock-open"></i>
                    <span><?= $dataset->access_level ?></span>
                </span>
                <?php endif; ?>
                
                <?php if (isset($dataset->update_frequency) && !empty($dataset->update_frequency)): ?>
                <span class="info-badge badge-frequency">
                    <i class="fas fa-sync"></i>
                    <span><?= $dataset->update_frequency ?></span>
                </span>
                <?php endif; ?>
                
                <span class="info-badge badge-views">
                    <i class="fas fa-eye"></i>
                    <span><?= number_format($dataset->views) ?> ครั้ง</span>
                </span>
            </div>

            <!-- Description -->
            <?php if (isset($dataset->description) && !empty($dataset->description)): ?>
            <div class="description-section">
                <h5><i class="fas fa-align-left"></i> คำอธิบาย</h5>
                <p><?= nl2br($dataset->description) ?></p>
            </div>
            <?php endif; ?>

            <!-- Information Table -->
            <div class="info-table">
                <h5><i class="fas fa-info-circle"></i> ข้อมูลทั่วไป</h5>
                <table>
                    <tbody>
                        <?php if (isset($dataset->table_name) && !empty($dataset->table_name)): ?>
                        <tr>
                            <th><i class="fas fa-table"></i> ชื่อตารางในฐานข้อมูล</th>
                            <td><code><?= preg_replace('/^tbl_/', '', $dataset->table_name) ?></code></td>
                        </tr>
                        <?php endif; ?>
                        
                        <?php if (isset($dataset->data_source) && !empty($dataset->data_source)): ?>
                        <tr>
                            <th><i class="fas fa-database"></i> แหล่งที่มาของข้อมูล</th>
                            <td><?= $dataset->data_source ?></td>
                        </tr>
                        <?php endif; ?>
                        
                        <?php if (isset($dataset->responsible_department) && !empty($dataset->responsible_department)): ?>
                        <tr>
                            <th><i class="fas fa-building"></i> หน่วยงานรับผิดชอบ</th>
                            <td><?= $dataset->responsible_department ?></td>
                        </tr>
                        <?php endif; ?>
                        
                        <?php if (isset($dataset->responsible_person) && !empty($dataset->responsible_person)): ?>
                        <tr>
                            <th><i class="fas fa-user"></i> ผู้รับผิดชอบข้อมูล</th>
                            <td><?= $dataset->responsible_person ?></td>
                        </tr>
                        <?php endif; ?>
                        
                        <?php if (isset($dataset->contact_email) && !empty($dataset->contact_email)): ?>
                        <tr>
                            <th><i class="fas fa-envelope"></i> อีเมลติดต่อ</th>
                            <td><a href="mailto:<?= $dataset->contact_email ?>"><?= $dataset->contact_email ?></a></td>
                        </tr>
                        <?php endif; ?>
                        
                        <?php if (isset($dataset->contact_phone) && !empty($dataset->contact_phone)): ?>
                        <tr>
                            <th><i class="fas fa-phone"></i> เบอร์โทรติดต่อ</th>
                            <td><?= $dataset->contact_phone ?></td>
                        </tr>
                        <?php endif; ?>
                        
                        <?php if (isset($dataset->record_count) && $dataset->record_count > 0): ?>
                        <tr>
                            <th><i class="fas fa-list-ol"></i> จำนวนระเบียนข้อมูล</th>
                            <td><?= number_format($dataset->record_count) ?> รายการ</td>
                        </tr>
                        <?php endif; ?>
                        
                        <?php if (isset($dataset->last_updated) && !empty($dataset->last_updated)): ?>
                        <tr>
                            <th><i class="fas fa-calendar-alt"></i> อัปเดตข้อมูลล่าสุด</th>
                            <td><?= date('d/m/Y', strtotime($dataset->last_updated)) ?></td>
                        </tr>
                        <?php endif; ?>
                        
                        <tr>
                            <th><i class="fas fa-clock"></i> วันที่สร้างรายการ</th>
                            <td><?= date('d/m/Y H:i', strtotime($dataset->created_at)) ?></td>
                        </tr>
                    </tbody>
                </table>
            </div>

            <!-- Keywords -->
            <?php if (isset($dataset->keywords) && !empty($dataset->keywords)): ?>
            <div class="keywords-section">
                <h5><i class="fas fa-tags"></i> คำสำคัญ</h5>
                <div>
                    <?php 
                    $keywords = explode(',', $dataset->keywords);
                    foreach ($keywords as $keyword): 
                    ?>
                    <span class="keyword-tag"><?= trim($keyword) ?></span>
                    <?php endforeach; ?>
                </div>
            </div>
            <?php endif; ?>

            <!-- License -->
            <?php if (isset($dataset->license) && !empty($dataset->license)): ?>
            <div class="license-alert">
                <strong><i class="fas fa-certificate"></i> ลิขสิทธิ์:</strong>
                <div><?= $dataset->license ?></div>
            </div>
            <?php endif; ?>

            <!-- Action Buttons -->
            <div class="action-section">
                <?php if (isset($dataset->download_url) && !empty($dataset->download_url)): ?>
                <?php 
                $download_url = (strpos($dataset->download_url, 'http') === 0) 
                              ? $dataset->download_url 
                              : base_url($dataset->download_url);
                ?>
                <a href="<?= base_url('data_catalog/download/'.$dataset->id) ?>" class="btn-action" target="_blank">
                    <i class="fas fa-download"></i>
                    <span>ดาวน์โหลดข้อมูล</span>
                </a>
                <?php endif; ?>
                
                <?php if (isset($dataset->api_endpoint) && !empty($dataset->api_endpoint)): ?>
                <?php 
                $api_url = (strpos($dataset->api_endpoint, 'http') === 0) 
                         ? $dataset->api_endpoint 
                         : base_url($dataset->api_endpoint);
                ?>
                <a href="<?= $api_url ?>" class="btn-action" target="_blank">
                    <i class="fas fa-plug"></i>
                    <span>API Endpoint</span>
                </a>
                <?php endif; ?>
                
                <a href="<?= base_url('data_catalog/api/'.$dataset->id) ?>" class="btn-action" target="_blank">
                    <i class="fas fa-code"></i>
                    <span>ดูข้อมูล JSON</span>
                </a>
            </div>
        </div>

        <!-- Metadata Structure -->
        <?php if (!empty($metadata)): ?>
        <div class="content-card metadata-section">
            <h4 class="section-title"><i class="fas fa-table"></i> โครงสร้างข้อมูล (Metadata)</h4>
            
            <div class="metadata-table">
                <table>
                    <thead>
                        <tr>
                            <th style="width: 25%;">ชื่อฟิลด์</th>
                            <th style="width: 15%;">ชนิดข้อมูล</th>
                            <th>คำอธิบาย</th>
                            <th style="width: 10%;">จำเป็น</th>
                            <th style="width: 15%;">ตัวอย่าง</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($metadata as $field): ?>
                        <tr>
                            <td>
                                <?php 
                                $display_name = (isset($field->field_name_en) && !empty($field->field_name_en)) 
                                              ? $field->field_name_en 
                                              : $field->field_name;
                                ?>
                                <span class="field-name"><?= $display_name ?></span>
                            </td>
                            <td>
                                <?php if (isset($field->field_type) && !empty($field->field_type)): ?>
                                <span class="type-badge"><?= $field->field_type ?></span>
                                <?php else: ?>
                                <span class="text-muted">-</span>
                                <?php endif; ?>
                            </td>
                            <td><?= isset($field->field_description) && !empty($field->field_description) ? $field->field_description : '-' ?></td>
                            <td class="text-center">
                                <?php if (isset($field->is_required) && $field->is_required == 1): ?>
                                <i class="fas fa-check-circle text-success" title="จำเป็น"></i>
                                <?php else: ?>
                                <i class="fas fa-times-circle text-muted" title="ไม่จำเป็น"></i>
                                <?php endif; ?>
                            </td>
                            <td>
                                <?php if (isset($field->example_value) && !empty($field->example_value)): ?>
                                <code><?= $field->example_value ?></code>
                                <?php else: ?>
                                <span class="text-muted">-</span>
                                <?php endif; ?>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </div>
        <?php endif; ?>

        <!-- Related Datasets -->
        <?php if (!empty($related_datasets)): ?>
        <div class="content-card related-section">
            <h4 class="section-title"><i class="fas fa-link"></i> ชุดข้อมูลที่เกี่ยวข้อง</h4>
            
            <?php foreach ($related_datasets as $related): ?>
            <a href="<?= base_url('data_catalog/dataset/'.$related->id) ?>" class="related-item">
                <h6><?= $related->dataset_name ?></h6>
                <small>
                    <i class="fas fa-folder"></i> <?= $related->category_name ?>
                    <span class="mx-2">|</span>
                    <i class="fas fa-calendar"></i> <?= date('d/m/Y', strtotime($related->updated_at)) ?>
                </small>
            </a>
            <?php endforeach; ?>
        </div>
        <?php endif; ?>
    </div>

    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    
    <script>
        // Smooth scroll
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
    </script>
</body>
</html>