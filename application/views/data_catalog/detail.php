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
        
        /* Breadcrumb */
        .breadcrumb-section {
            background: white;
            padding: 15px 25px;
            border-radius: 8px;
            margin-bottom: 20px;
            box-shadow: 0 2px 8px rgba(0,0,0,0.05);
        }
        
        .breadcrumb {
            background: none;
            padding: 0;
            margin: 0;
        }
        
        .breadcrumb-item a {
            color: #0066cc;
            text-decoration: none;
        }
        
        .breadcrumb-item.active {
            color: #666;
        }
        
        /* Main Card */
        .content-card {
            background: white;
            border-radius: 8px;
            padding: 35px;
            margin-bottom: 25px;
            box-shadow: 0 2px 8px rgba(0,0,0,0.05);
        }
        
        /* Header Section */
        .dataset-header {
            border-left: 5px solid <?= isset($dataset->color) ? $dataset->color : '#0066cc' ?>;
            padding-left: 25px;
            margin-bottom: 30px;
        }
        
        .dataset-header h1 {
            font-size: 2rem;
            font-weight: 700;
            color: #1a1a1a;
            margin-bottom: 10px;
        }
        
        .dataset-header .subtitle {
            color: #666;
            font-size: 1.05rem;
        }
        
        /* Icon Badge */
        .category-badge {
            display: inline-flex;
            align-items: center;
            gap: 10px;
            background: <?= isset($dataset->color) ? $dataset->color : '#0066cc' ?>;
            color: white;
            padding: 10px 20px;
            border-radius: 8px;
            font-weight: 600;
            margin-bottom: 20px;
        }
        
        .category-badge i {
            font-size: 24px;
        }
        
        /* Info Badges */
        .info-badge {
            display: inline-flex;
            align-items: center;
            gap: 8px;
            padding: 8px 16px;
            border-radius: 20px;
            font-size: 0.9rem;
            font-weight: 500;
            margin: 5px 8px 5px 0;
            border: 2px solid;
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
            background: #f8f9fa;
            padding: 25px;
            border-radius: 8px;
            border-left: 4px solid #0066cc;
            margin-bottom: 30px;
        }
        
        .description-section h5 {
            font-weight: 700;
            color: #1a1a1a;
            margin-bottom: 15px;
        }
        
        .description-section p {
            color: #333;
            line-height: 1.8;
            margin: 0;
        }
        
        /* Info Table */
        .info-table {
            margin-bottom: 30px;
        }
        
        .info-table table {
            width: 100%;
            border-collapse: separate;
            border-spacing: 0;
        }
        
        .info-table th {
            background: #f8f9fa;
            padding: 15px 20px;
            font-weight: 600;
            color: #333;
            width: 30%;
            border-bottom: 1px solid #e0e0e0;
        }
        
        .info-table td {
            padding: 15px 20px;
            color: #666;
            border-bottom: 1px solid #e0e0e0;
        }
        
        .info-table tr:last-child td,
        .info-table tr:last-child th {
            border-bottom: none;
        }
        
        .info-table code {
            background: #f0f0f0;
            padding: 4px 8px;
            border-radius: 4px;
            color: #d63384;
            font-size: 0.9rem;
        }
        
        /* Keywords */
        .keywords-section {
            margin-bottom: 30px;
        }
        
        .keyword-tag {
            display: inline-block;
            background: #f0f5ff;
            color: #0066cc;
            padding: 8px 16px;
            border-radius: 20px;
            margin: 5px 8px 5px 0;
            font-weight: 500;
            border: 1px solid #d1e7ff;
        }
        
        /* Metadata Table */
        .metadata-section {
            margin-top: 30px;
        }
        
        .metadata-table {
            background: white;
            border-radius: 8px;
            overflow: hidden;
            border: 1px solid #e0e0e0;
        }
        
        .metadata-table table {
            width: 100%;
            margin: 0;
        }
        
        .metadata-table thead th {
            background: #0066cc;
            color: white;
            padding: 15px;
            font-weight: 600;
            text-align: left;
            border: none;
        }
        
        .metadata-table tbody td {
            padding: 15px;
            border-bottom: 1px solid #f0f0f0;
        }
        
        .metadata-table tbody tr:last-child td {
            border-bottom: none;
        }
        
        .metadata-table tbody tr:hover {
            background: #f8f9fa;
        }
        
        .field-name {
            font-family: 'Courier New', monospace;
            background: #f0f0f0;
            padding: 4px 8px;
            border-radius: 4px;
            color: #d63384;
            font-weight: 600;
        }
        
        .type-badge {
            background: #e6f7ff;
            color: #0066cc;
            padding: 4px 10px;
            border-radius: 12px;
            font-size: 0.85rem;
            font-weight: 600;
        }
        
        /* Action Buttons */
        .action-section {
            background: #f8f9fa;
            padding: 25px;
            border-radius: 8px;
            text-align: center;
            margin-bottom: 30px;
        }
        
        .btn-action {
            background: #0066cc;
            color: white;
            padding: 12px 30px;
            border-radius: 6px;
            border: none;
            font-weight: 600;
            margin: 5px;
            transition: all 0.3s;
            text-decoration: none;
            display: inline-flex;
            align-items: center;
            gap: 10px;
        }
        
        .btn-action:hover {
            background: #0052a3;
            color: white;
            transform: translateY(-2px);
            box-shadow: 0 4px 12px rgba(0,102,204,0.3);
        }
        
        .btn-action i {
            font-size: 18px;
        }
        
        /* Related Section */
        .related-section {
            margin-top: 30px;
        }
        
        .section-title {
            font-size: 1.4rem;
            font-weight: 700;
            color: #1a1a1a;
            margin-bottom: 20px;
            padding-bottom: 15px;
            border-bottom: 3px solid #0066cc;
        }
        
        .related-item {
            background: white;
            border: 1px solid #e0e0e0;
            border-left: 4px solid <?= isset($dataset->color) ? $dataset->color : '#0066cc' ?>;
            padding: 20px;
            border-radius: 8px;
            margin-bottom: 15px;
            transition: all 0.3s;
            text-decoration: none;
            display: block;
            color: inherit;
        }
        
        .related-item:hover {
            background: #f8f9fa;
            transform: translateX(8px);
            box-shadow: 0 4px 12px rgba(0,0,0,0.1);
            text-decoration: none;
            color: inherit;
        }
        
        .related-item h6 {
            font-weight: 600;
            color: #1a1a1a;
            margin-bottom: 8px;
        }
        
        .related-item small {
            color: #666;
        }
        
        /* Alert */
        .license-alert {
            background: #fff9e6;
            border-left: 4px solid #ffc107;
            padding: 20px;
            border-radius: 8px;
            margin-bottom: 30px;
        }
        
        .license-alert i {
            color: #ffc107;
            font-size: 20px;
            margin-right: 10px;
        }
        
        /* Responsive */
        @media (max-width: 768px) {
            .dataset-header h1 {
                font-size: 1.5rem;
            }
            
            .info-table th {
                width: 40%;
            }
            
            .btn-action {
                display: flex;
                width: 100%;
                justify-content: center;
                margin: 8px 0;
            }
        }
    </style>
</head>
<body>
    <div class="container">
        <!-- Breadcrumb -->
        <div class="breadcrumb-section">
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb">
                    <li class="breadcrumb-item">
                        <a href="<?= base_url('data_catalog') ?>">
                            <i class="fas fa-home"></i> หน้าหลัก
                        </a>
                    </li>
                    <li class="breadcrumb-item">
                        <a href="<?= base_url('data_catalog/category/'.$dataset->category_id) ?>">
                            <?= $dataset->category_name ?>
                        </a>
                    </li>
                    <li class="breadcrumb-item active"><?= word_limiter($dataset->dataset_name, 5) ?></li>
                </ol>
            </nav>
        </div>

        <!-- Main Content -->
        <div class="content-card">
            <!-- Header -->
            <div class="dataset-header">
                <div class="category-badge" style="background: <?= $dataset->color ?>;">
                    <i class="<?= $dataset->icon ?>"></i>
                    <span><?= $dataset->category_name ?></span>
                </div>
                
                <h1><?= $dataset->dataset_name ?></h1>
                <?php if (isset($dataset->dataset_name_en) && !empty($dataset->dataset_name_en)): ?>
                <p class="subtitle"><?= $dataset->dataset_name_en ?></p>
                <?php endif; ?>
            </div>

            <!-- Badges -->
            <div class="mb-4">
                <?php if (isset($dataset->data_format) && !empty($dataset->data_format)): ?>
                <span class="info-badge badge-format">
                    <i class="fas fa-file-code"></i>
                    <span><?= $dataset->data_format ?></span>
                </span>
                <?php endif; ?>
                
                <?php if (isset($dataset->access_level)): ?>
                <span class="info-badge badge-access">
                    <i class="fas fa-<?= $dataset->access_level == 'public' ? 'globe' : 'lock' ?>"></i>
                    <span><?= $dataset->access_level == 'public' ? 'เปิดเผยทั่วไป' : ($dataset->access_level == 'restricted' ? 'จำกัดสิทธิ์' : 'ส่วนตัว') ?></span>
                </span>
                <?php endif; ?>
                
                <?php if (isset($dataset->update_frequency) && !empty($dataset->update_frequency)): ?>
                <span class="info-badge badge-frequency">
                    <i class="fas fa-sync-alt"></i>
                    <span><?= $dataset->update_frequency ?></span>
                </span>
                <?php endif; ?>
                
                <?php if (isset($dataset->views)): ?>
                <span class="info-badge badge-views">
                    <i class="fas fa-eye"></i>
                    <span><?= number_format($dataset->views) ?> ครั้ง</span>
                </span>
                <?php endif; ?>
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
                <h5 class="mb-3"><i class="fas fa-info-circle"></i> ข้อมูลทั่วไป</h5>
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
                <h5 class="mb-3"><i class="fas fa-tags"></i> คำสำคัญ</h5>
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
                <strong><i class="fas fa-certificate"></i> ลิขสิทธิ์:</strong> <?= $dataset->license ?>
            </div>
            <?php endif; ?>

            <!-- Action Buttons -->
            <div class="action-section">
                <?php if (isset($dataset->download_url) && !empty($dataset->download_url)): ?>
                <a href="<?= $dataset->download_url ?>" class="btn-action" target="_blank">
                    <i class="fas fa-download"></i>
                    <span>ดาวน์โหลดข้อมูล</span>
                </a>
                <?php endif; ?>
                
                <?php if (isset($dataset->api_endpoint) && !empty($dataset->api_endpoint)): ?>
                <a href="<?= $dataset->api_endpoint ?>" class="btn-action" target="_blank">
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
                                // แสดงชื่อแสดง (field_name_en) ถ้ามี ไม่งั้นใช้ชื่อจริง
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
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@4.6.2/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>