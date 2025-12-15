<!DOCTYPE html>
<html lang="th">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= $page_title ?></title>
    
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@4.6.2/dist/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Sarabun:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    
    <style>
        * {
            font-family: 'Sarabun', sans-serif;
        }
        
        body {
            background: linear-gradient(135deg, #f5f7fa 0%, #c3cfe2 100%);
            min-height: 100vh;
            padding: 30px 0;
        }
        
        .form-container {
            max-width: 1200px;
            margin: 0 auto;
        }
        
        .page-header {
            background: white;
            padding: 25px 35px;
            border-radius: 12px;
            margin-bottom: 25px;
            box-shadow: 0 4px 20px rgba(0,0,0,0.08);
            display: flex;
            justify-content: space-between;
            align-items: center;
        }
        
        .page-header h1 {
            font-size: 1.9rem;
            font-weight: 700;
            color: #2c3e50;
            margin: 0;
            display: flex;
            align-items: center;
            gap: 12px;
        }
        
        .page-header h1 i {
            color: #3498db;
        }
        
        .breadcrumb-custom {
            background: transparent;
            padding: 0;
            margin: 0;
            font-size: 0.9rem;
        }
        
        .breadcrumb-custom a {
            color: #7f8c8d;
            text-decoration: none;
            transition: color 0.3s;
        }
        
        .breadcrumb-custom a:hover {
            color: #3498db;
        }
        
        .form-card {
            background: white;
            padding: 35px;
            border-radius: 12px;
            box-shadow: 0 4px 20px rgba(0,0,0,0.08);
            margin-bottom: 25px;
            transition: all 0.3s;
        }
        
        .form-card:hover {
            box-shadow: 0 6px 30px rgba(0,0,0,0.12);
        }
        
        .section-title {
            font-size: 1.4rem;
            font-weight: 700;
            color: #2c3e50;
            margin-bottom: 25px;
            padding-bottom: 12px;
            border-bottom: 3px solid #3498db;
            display: flex;
            align-items: center;
            gap: 10px;
        }
        
        .section-title i {
            color: #3498db;
            font-size: 1.3rem;
        }
        
        .form-group label {
            font-weight: 600;
            color: #34495e;
            margin-bottom: 10px;
            font-size: 0.95rem;
        }
        
        .form-group label .required {
            color: #e74c3c;
            margin-left: 3px;
        }
        
        .form-control, select.form-control {
            border: 2px solid #ecf0f1;
            border-radius: 8px;
            padding: 12px 18px;
            font-size: 0.95rem;
            transition: all 0.3s;
            background: #fafafa;
            color: #2c3e50;
        }
        
        select.form-control {
            appearance: none;
            background-image: url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' width='12' height='12' viewBox='0 0 12 12'%3E%3Cpath fill='%233498db' d='M10.293 3.293L6 7.586 1.707 3.293A1 1 0 00.293 4.707l5 5a1 1 0 001.414 0l5-5a1 1 0 10-1.414-1.414z'/%3E%3C/svg%3E");
            background-repeat: no-repeat;
            background-position: right 15px center;
            background-size: 12px;
            padding-right: 40px;
            padding-top: 24px;
            padding-bottom: 24px;
            height: auto;
            min-height: 60px;
        }
        
        select.form-control option {
            color: #2c3e50;
            background: white;
            padding: 20px;
            font-size: 1rem;
            line-height: 2;
        }
        
        select.form-control option:disabled {
            color: #95a5a6;
            font-style: italic;
        }
        
        .form-control:focus, select.form-control:focus {
            border-color: #3498db;
            box-shadow: 0 0 0 4px rgba(52, 152, 219, 0.1);
            background: white;
            color: #2c3e50;
        }
        
        textarea.form-control {
            min-height: 120px;
            resize: vertical;
        }
        
        .input-icon {
            position: relative;
        }
        
        .input-icon i {
            position: absolute;
            left: 15px;
            top: 50%;
            transform: translateY(-50%);
            color: #95a5a6;
        }
        
        .input-icon .form-control {
            padding-left: 45px;
        }
        
        .btn-primary {
            background: linear-gradient(135deg, #3498db 0%, #2980b9 100%);
            border: none;
            padding: 12px 35px;
            border-radius: 8px;
            font-weight: 600;
            font-size: 1rem;
            transition: all 0.3s;
            box-shadow: 0 4px 15px rgba(52, 152, 219, 0.3);
        }
        
        .btn-primary:hover {
            transform: translateY(-2px);
            box-shadow: 0 6px 25px rgba(52, 152, 219, 0.4);
        }
        
        .btn-secondary {
            background: #95a5a6;
            border: none;
            padding: 12px 35px;
            border-radius: 8px;
            font-weight: 600;
            font-size: 1rem;
            transition: all 0.3s;
        }
        
        .btn-secondary:hover {
            background: #7f8c8d;
            transform: translateY(-2px);
        }
        
        .metadata-section {
            border: 2px dashed #bdc3c7;
            padding: 25px;
            border-radius: 10px;
            background: #f8f9fa;
        }
        
        .metadata-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 20px;
        }
        
        .metadata-header h6 {
            font-weight: 600;
            color: #2c3e50;
            margin: 0;
            font-size: 1.1rem;
        }
        
        .metadata-item {
            background: white;
            padding: 20px;
            border-radius: 8px;
            margin-bottom: 15px;
            border: 1px solid #e8e8e8;
            transition: all 0.3s;
        }
        
        .metadata-item:hover {
            box-shadow: 0 4px 15px rgba(0,0,0,0.08);
            border-color: #3498db;
        }
        
        .btn-add-field {
            background: linear-gradient(135deg, #27ae60 0%, #229954 100%);
            color: white;
            border: none;
            padding: 10px 25px;
            border-radius: 8px;
            font-weight: 600;
            transition: all 0.3s;
            box-shadow: 0 4px 15px rgba(39, 174, 96, 0.3);
        }
        
        .btn-add-field:hover {
            transform: translateY(-2px);
            box-shadow: 0 6px 25px rgba(39, 174, 96, 0.4);
        }
        
        .btn-remove-field {
            background: linear-gradient(135deg, #e74c3c 0%, #c0392b 100%);
            color: white;
            border: none;
            padding: 8px 15px;
            border-radius: 6px;
            transition: all 0.3s;
        }
        
        .btn-remove-field:hover {
            transform: scale(1.05);
        }
        
        .form-help {
            font-size: 0.85rem;
            color: #7f8c8d;
            margin-top: 6px;
            display: flex;
            align-items: center;
            gap: 5px;
        }
        
        .form-help i {
            color: #3498db;
        }
        
        .alert {
            border-radius: 10px;
            border: none;
            padding: 15px 20px;
            margin-bottom: 25px;
        }
        
        .alert-success {
            background: linear-gradient(135deg, #d4edda 0%, #c3e6cb 100%);
            color: #155724;
        }
        
        .alert-danger {
            background: linear-gradient(135deg, #f8d7da 0%, #f5c6cb 100%);
            color: #721c24;
        }
        
        .action-buttons {
            display: flex;
            gap: 15px;
            justify-content: flex-end;
            padding: 25px 35px;
            background: white;
            border-radius: 12px;
            box-shadow: 0 4px 20px rgba(0,0,0,0.08);
        }
        
        .badge-info {
            background: #3498db;
            padding: 5px 12px;
            border-radius: 15px;
            font-size: 0.85rem;
        }
        
        .input-group-text {
            background: #ecf0f1;
            border: 2px solid #ecf0f1;
            border-right: none;
            color: #7f8c8d;
        }
        
        .input-group .form-control {
            border-left: none;
        }
        
        .card-stat {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            padding: 20px;
            border-radius: 10px;
            margin-bottom: 20px;
        }
        
        .card-stat h3 {
            font-size: 1.1rem;
            font-weight: 600;
            margin: 0;
        }
        
        .card-stat p {
            margin: 5px 0 0 0;
            opacity: 0.9;
            font-size: 0.9rem;
        }
    </style>

    <style>
        /* Custom SweetAlert2 styles ตาม UI ของระบบ */
        .swal2-popup {
            font-family: 'Sarabun', sans-serif !important;
            border-radius: 15px !important;
        }
        
        .swal2-title {
            color: #2c3e50 !important;
            font-size: 1.8rem !important;
            font-weight: 700 !important;
        }
        
        .swal2-html-container {
            color: #34495e !important;
            font-size: 1.05rem !important;
            line-height: 1.6 !important;
        }
        
        .swal2-confirm {
            background: linear-gradient(135deg, #e74c3c 0%, #c0392b 100%) !important;
            border: none !important;
            padding: 12px 35px !important;
            border-radius: 8px !important;
            font-weight: 600 !important;
            font-size: 1rem !important;
            box-shadow: 0 4px 15px rgba(231, 76, 60, 0.3) !important;
        }
        
        .swal2-confirm:hover {
            transform: translateY(-2px) !important;
            box-shadow: 0 6px 25px rgba(231, 76, 60, 0.4) !important;
        }
        
        .swal2-cancel {
            background: #95a5a6 !important;
            border: none !important;
            padding: 12px 35px !important;
            border-radius: 8px !important;
            font-weight: 600 !important;
            font-size: 1rem !important;
        }
        
        .swal2-cancel:hover {
            background: #7f8c8d !important;
            transform: translateY(-2px) !important;
        }
        
        .swal2-icon.swal2-warning {
            border-color: #f39c12 !important;
            color: #f39c12 !important;
        }
        
        .swal2-icon.swal2-question {
            border-color: #3498db !important;
            color: #3498db !important;
        }
    </style>

</head>
<body>
    <div class="form-container">
        <!-- Header -->
        <div class="page-header">
            <div>
                <h1>
                    <i class="fas fa-<?= $mode == 'add' ? 'plus-circle' : 'edit' ?>"></i>
                    <?= $page_title ?>
                </h1>
                <nav class="breadcrumb-custom">
                    <a href="<?= base_url('data_catalog') ?>">
                        <i class="fas fa-home"></i> หน้าแรก
                    </a>
                    <span class="mx-2">/</span>
                    <a href="<?= base_url('data_catalog_manual') ?>">จัดการข้อมูล</a>
                    <span class="mx-2">/</span>
                    <span class="text-muted"><?= $mode == 'add' ? 'เพิ่มใหม่' : 'แก้ไข' ?></span>
                </nav>
            </div>
        </div>

        <!-- Alerts -->
        <?php if ($this->session->flashdata('success')): ?>
        <div class="alert alert-success alert-dismissible fade show">
            <i class="fas fa-check-circle"></i> <strong>สำเร็จ!</strong> <?= $this->session->flashdata('success') ?>
            <button type="button" class="close" data-dismiss="alert">&times;</button>
        </div>
        <?php endif; ?>
        
        <?php if ($this->session->flashdata('error')): ?>
        <div class="alert alert-danger alert-dismissible fade show">
            <i class="fas fa-exclamation-circle"></i> <strong>ข้อผิดพลาด!</strong> <?= $this->session->flashdata('error') ?>
            <button type="button" class="close" data-dismiss="alert">&times;</button>
        </div>
        <?php endif; ?>

        <form action="<?= base_url('data_catalog_manual/save') ?>" method="post" id="datasetForm">
            <?php if ($mode == 'edit'): ?>
            <input type="hidden" name="dataset_id" value="<?= $dataset->id ?>">
            <?php endif; ?>
            
            <!-- ข้อมูลพื้นฐาน -->
            <div class="form-card">
                <div class="section-title">
                    <i class="fas fa-info-circle"></i>
                    ข้อมูลพื้นฐาน
                </div>
                
                <div class="row">
                    <div class="col-md-6">
                        <div class="form-group">
                            <label>ชื่อชุดข้อมูล (ภาษาไทย) <span class="required">*</span></label>
                            <input type="text" name="dataset_name" class="form-control" 
                                   value="<?= isset($dataset) ? $dataset->dataset_name : '' ?>" 
                                   placeholder="เช่น ข้อมูลข่าวสารและประกาศ" required>
                        </div>
                    </div>
                    
                    <div class="col-md-6">
                        <div class="form-group">
                            <label>ชื่อชุดข้อมูล (ภาษาอังกฤษ)</label>
                            <input type="text" name="dataset_name_en" class="form-control" 
                                   value="<?= isset($dataset) ? $dataset->dataset_name_en : '' ?>"
                                   placeholder="เช่น News and Announcements">
                        </div>
                    </div>
                </div>
                
                <div class="form-group">
                    <label>คำอธิบาย</label>
                    <textarea name="description" class="form-control" 
                              placeholder="อธิบายรายละเอียดของชุดข้อมูลนี้..."><?= isset($dataset) ? $dataset->description : '' ?></textarea>
                    <small class="form-help">
                        <i class="fas fa-lightbulb"></i>
                        ควรอธิบายให้ชัดเจนว่าชุดข้อมูลนี้มีอะไรบ้าง และใช้ประโยชน์อย่างไร
                    </small>
                </div>
                
                <div class="row">
                    <div class="col-md-4">
                        <div class="form-group">
                            <label>หมวดหมู่ <span class="required">*</span></label>
                            <select name="category_id" class="form-control" required>
                                <option value="" disabled selected style="color: #95a5a6;">-- เลือกหมวดหมู่ --</option>
                                <?php foreach ($categories as $cat): ?>
                                <option value="<?= $cat->id ?>" 
                                        style="color: #2c3e50;"
                                        <?= (isset($dataset) && $dataset->category_id == $cat->id) ? 'selected' : '' ?>>
                                    <?= $cat->category_name ?>
                                </option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                    </div>
                    
                    <div class="col-md-4">
                        <div class="form-group">
                            <label>รูปแบบข้อมูล</label>
                            <select name="data_format" class="form-control">
                                <option value="" disabled selected style="color: #95a5a6;">-- เลือกรูปแบบ --</option>
                                <?php
                                $formats = ['Database', 'JSON', 'CSV', 'XML', 'Excel', 'PDF', 'API'];
                                foreach ($formats as $format):
                                ?>
                                <option value="<?= $format ?>" 
                                        style="color: #2c3e50;"
                                        <?= (isset($dataset) && $dataset->data_format == $format) ? 'selected' : '' ?>>
                                    <?= $format ?>
                                </option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                    </div>
                    
                    <div class="col-md-4">
                        <div class="form-group">
                            <label>ระดับการเข้าถึง</label>
                            <select name="access_level" class="form-control">
                                <option value="" disabled <?= (!isset($dataset) || empty($dataset->access_level)) ? 'selected' : '' ?> style="color: #95a5a6;">-- เลือกระดับ --</option>
                                <option value="public" style="color: #2c3e50;" <?= (isset($dataset) && $dataset->access_level == 'public') ? 'selected' : '' ?>>
                                    🌐 เปิดเผยทั่วไป
                                </option>
                                <option value="restricted" style="color: #2c3e50;" <?= (isset($dataset) && $dataset->access_level == 'restricted') ? 'selected' : '' ?>>
                                    🔒 จำกัดสิทธิ์
                                </option>
                                <option value="private" style="color: #2c3e50;" <?= (isset($dataset) && $dataset->access_level == 'private') ? 'selected' : '' ?>>
                                    🔐 ส่วนตัว
                                </option>
                            </select>
                        </div>
                    </div>
                </div>
                
                <div class="row">
                    <div class="col-md-6">
                        <div class="form-group">
                            <label>ชื่อตารางในฐานข้อมูล</label>
                            <select name="table_name" id="table_name" class="form-control">
                                <option value="" disabled selected style="color: #95a5a6;">-- เลือกตาราง --</option>
                                <?php if (!empty($tables)): ?>
                                    <?php foreach ($tables as $table): ?>
                                    <option value="<?= $table->table_name ?>" 
                                            style="color: #2c3e50;"
                                            <?= (isset($dataset) && $dataset->table_name == $table->table_name) ? 'selected' : '' ?>>
                                        <?= $table->table_name ?>
                                    </option>
                                    <?php endforeach; ?>
                                <?php endif; ?>
                            </select>
                            <small class="form-help">
                                <i class="fas fa-info-circle"></i>
                                ระบุชื่อตารางถ้าเป็นข้อมูลใน Database
                            </small>
                        </div>
                    </div>
                    
                    <div class="col-md-6">
                        <div class="form-group">
                            <label>แหล่งที่มาของข้อมูล</label>
                            <div class="input-group">
                                <div class="input-group-prepend">
                                    <span class="input-group-text"><i class="fas fa-database"></i></span>
                                </div>
                                <input type="text" name="data_source" class="form-control" 
                                       value="<?= isset($dataset) ? $dataset->data_source : '' ?>"
                                       placeholder="ระบบฐานข้อมูลหลัก">
                            </div>
                        </div>
                    </div>
                </div>
                
                <div class="form-group">
                    <label>คำสำคัญ (Keywords)</label>
                    <div class="input-group">
                        <div class="input-group-prepend">
                            <span class="input-group-text"><i class="fas fa-tags"></i></span>
                        </div>
                        <input type="text" name="keywords" class="form-control" 
                               value="<?= isset($dataset) ? $dataset->keywords : '' ?>"
                               placeholder="ข่าวสาร, ประกาศ, ประชาสัมพันธ์">
                    </div>
                    <small class="form-help">
                        <i class="fas fa-info-circle"></i>
                        ใช้สำหรับการค้นหา คั่นด้วยเครื่องหมายจุลภาค (,)
                    </small>
                </div>
            </div>

            <!-- ข้อมูลผู้รับผิดชอบ -->
            <div class="form-card">
                <div class="section-title">
                    <i class="fas fa-user-tie"></i>
                    ข้อมูลผู้รับผิดชอบ
                </div>
                
                <div class="row">
                    <div class="col-md-6">
                        <div class="form-group">
                            <label>หน่วยงานผู้รับผิดชอบ</label>
                            <select name="responsible_department" class="form-control">
                                <option value="" disabled selected style="color: #95a5a6;">-- เลือกหน่วยงาน --</option>
                                <?php if (!empty($positions)): ?>
                                    <?php foreach ($positions as $position): ?>
                                    <option value="<?= $position->position_name ?>" 
                                            style="color: #2c3e50;"
                                            <?= (isset($dataset) && $dataset->responsible_department == $position->position_name) ? 'selected' : '' ?>>
                                        <?= $position->position_name ?>
                                    </option>
                                    <?php endforeach; ?>
                                <?php endif; ?>
                            </select>
                        </div>
                    </div>
                    
                    <div class="col-md-6">
                        <div class="form-group">
                            <label>ชื่อผู้รับผิดชอบ</label>
                            <div class="input-group">
                                <div class="input-group-prepend">
                                    <span class="input-group-text"><i class="fas fa-user"></i></span>
                                </div>
                                <input type="text" name="responsible_person" class="form-control" 
                                       value="<?= isset($dataset) ? $dataset->responsible_person : '' ?>"
                                       placeholder="นายสมชาย ใจดี">
                            </div>
                        </div>
                    </div>
                </div>
                
                <div class="row">
                    <div class="col-md-6">
                        <div class="form-group">
                            <label>อีเมล</label>
                            <div class="input-group">
                                <div class="input-group-prepend">
                                    <span class="input-group-text"><i class="fas fa-envelope"></i></span>
                                </div>
                                <input type="email" name="contact_email" class="form-control" 
                                       value="<?= isset($dataset) ? $dataset->contact_email : '' ?>"
                                       placeholder="example@domain.com">
                            </div>
                        </div>
                    </div>
                    
                    <div class="col-md-6">
                        <div class="form-group">
                            <label>เบอร์โทรศัพท์</label>
                            <div class="input-group">
                                <div class="input-group-prepend">
                                    <span class="input-group-text"><i class="fas fa-phone"></i></span>
                                </div>
                                <input type="text" name="contact_phone" class="form-control" 
                                       value="<?= isset($dataset) ? $dataset->contact_phone : '' ?>"
                                       placeholder="0812345678" maxlength="10">
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- ข้อมูลเพิ่มเติม -->
            <div class="form-card">
                <div class="section-title">
                    <i class="fas fa-cog"></i>
                    ข้อมูลเพิ่มเติม
                </div>
                
                <div class="row">
                    <div class="col-md-4">
                        <div class="form-group">
                            <label>ใบอนุญาต (License)</label>
                            <input type="text" name="license" class="form-control" 
                                   value="<?= isset($dataset) ? $dataset->license : '' ?>"
                                   placeholder="CC BY 4.0">
                        </div>
                    </div>
                    
                    <div class="col-md-4">
                        <div class="form-group">
                            <label>ความถี่ในการอัปเดต</label>
                            <select name="update_frequency" class="form-control">
                                <option value="" disabled selected style="color: #95a5a6;">-- เลือก --</option>
                                <?php
                                $frequencies = ['รายวัน', 'รายสัปดาห์', 'รายเดือน', 'รายไตรมาส', 'รายปี', 'ไม่แน่นอน'];
                                foreach ($frequencies as $freq):
                                ?>
                                <option value="<?= $freq ?>" 
                                        style="color: #2c3e50;"
                                        <?= (isset($dataset) && $dataset->update_frequency == $freq) ? 'selected' : '' ?>>
                                    <?= $freq ?>
                                </option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                    </div>
                    
                <div class="col-md-4">
                    <div class="form-group">
                        <label>จำนวนข้อมูลที่ส่งออก</label>
                        <select name="record_count" class="form-control">
                            <option value="" disabled style="color: #95a5a6;">-- เลือกจำนวน --</option>
                            <option value="100" style="color: #2c3e50;" <?= (isset($dataset) && $dataset->record_count == 100) ? 'selected' : '' ?>>
                                100 รายการ
                            </option>
                            <option value="200" style="color: #2c3e50;" <?= (isset($dataset) && $dataset->record_count == 200) ? 'selected' : '' ?>>
                                200 รายการ
                            </option>
                            <option value="500" style="color: #2c3e50;" <?= (isset($dataset) && $dataset->record_count == 500) ? 'selected' : '' ?>>
                                500 รายการ
                            </option>
                            <option value="-1" style="color: #27ae60;" <?= (isset($dataset) && $dataset->record_count == -1) ? 'selected' : '' ?>>
                                🔓 ไม่จำกัด (UNLIMIT)
                            </option>
                        </select>
                        <small class="form-help">
                            <i class="fas fa-info-circle"></i>
                            กำหนดจำนวนข้อมูลสูงสุดที่จะส่งออกผ่าน API
                        </small>
                    </div>
                </div>
                </div>
                
                <div class="row">
                    <div class="col-md-4">
                        <div class="form-group">
                            <label>วันที่อัปเดตล่าสุด</label>
                            <input type="date" name="last_updated" class="form-control" 
                                   value="<?= isset($dataset) ? $dataset->last_updated : '' ?>">
                        </div>
                    </div>
                    
                    <div class="col-md-4">
                        <div class="form-group">
                            <label>URL สำหรับดาวน์โหลด</label>
                            <input type="text" name="download_url" class="form-control" 
                                   value="<?= isset($dataset) ? $dataset->download_url : '' ?>"
                                   placeholder="api_data_catalog/download/{id}">
                            <small class="form-text text-muted">
                                <i class="fas fa-info-circle"></i> ระบบจะเพิ่ม domain อัตโนมัติ
                            </small>
                        </div>
                    </div>
                    
                    <div class="col-md-4">
                        <div class="form-group">
                            <label>API Endpoint</label>
                            <input type="text" name="api_endpoint" class="form-control" 
                                   value="<?= isset($dataset) ? $dataset->api_endpoint : '' ?>"
                                   placeholder="api_data_catalog/dataset/{id}">
                            <small class="form-text text-muted">
                                <i class="fas fa-info-circle"></i> ระบบจะเพิ่ม domain อัตโนมัติ
                            </small>
                        </div>
                    </div>
                </div>
                
                <div class="form-group">
                    <label>สถานะ</label>
                    <select name="status" class="form-control">
                        <option value="1" style="color: #27ae60;" <?= (!isset($dataset) || $dataset->status == 1) ? 'selected' : '' ?>>
                            ✅ เปิดใช้งาน
                        </option>
                        <option value="0" style="color: #e74c3c;" <?= (isset($dataset) && $dataset->status == 0) ? 'selected' : '' ?>>
                            ❌ ปิดใช้งาน
                        </option>
                    </select>
                </div>
            </div>

            <!-- โครงสร้างข้อมูล -->
            <div class="form-card">
                <div class="section-title">
                    <i class="fas fa-table"></i>
                    โครงสร้างข้อมูล (Metadata)
                </div>
                
                <div class="metadata-section">
                    <div class="metadata-header">
                        <h6><i class="fas fa-list"></i> รายการฟิลด์ข้อมูล</h6>
                        <button type="button" class="btn btn-add-field" onclick="addField()">
                            <i class="fas fa-plus"></i> เพิ่มฟิลด์
                        </button>
                    </div>
                    
                    <div id="metadata-container">
                        <?php if ($mode == 'edit' && !empty($metadata)): ?>
                            <?php foreach ($metadata as $index => $meta): ?>
                            <div class="metadata-item" data-index="<?= $index ?>">
                                <div class="row align-items-center">
                                    <div class="col-md-2">
                                        <select name="field_name[]" class="form-control field-name-select" onchange="updateFieldType(this)">
                                            <option value="">-- เลือกฟิลด์ --</option>
                                            <option value="<?= $meta->field_name ?>" selected><?= $meta->field_name ?></option>
                                        </select>
                                        <input type="text" class="form-control mt-1 d-none field-name-input" 
                                               placeholder="พิมพ์ชื่อฟิลด์เอง" style="font-size: 0.9em;">
                                        <small class="text-muted" style="cursor: pointer; font-size: 0.85em;" 
                                               onclick="toggleFieldInput(this)">
                                            <i class="fas fa-edit"></i> พิมพ์เอง
                                        </small>
                                    </div>
                                    <div class="col-md-2">
                                        <input type="text" name="field_name_en[]" class="form-control" 
                                               placeholder="ชื่อแสดง (alias)" 
                                               value="<?= $meta->field_name_en ?? '' ?>"
                                               title="ชื่อที่จะแสดงใน API (ถ้าไม่ใส่จะใช้ชื่อฟิลด์จริง)">
                                        <small class="form-text text-muted" style="font-size: 0.8em;">
                                            <i class="fas fa-tag"></i> ชื่อแสดง
                                        </small>
                                    </div>
                                    <div class="col-md-2">
                                        <input type="text" name="field_type[]" class="form-control field-type-input" 
                                               placeholder="ชนิดข้อมูล" value="<?= $meta->field_type ?>" readonly>
                                    </div>
                                    <div class="col-md-5">
                                        <input type="text" name="field_description[]" class="form-control" 
                                               placeholder="คำอธิบาย" value="<?= $meta->field_description ?>">
                                    </div>
                                    <div class="col-md-1 text-center">
                                        <button type="button" class="btn btn-remove-field" onclick="removeField(this)" title="ลบฟิลด์">
                                            <i class="fas fa-times"></i>
                                        </button>
                                    </div>
                                </div>
                            </div>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </div>
                    
                    <small class="form-help d-block mt-3">
                        <i class="fas fa-lightbulb"></i>
                        เพิ่มรายละเอียดโครงสร้างของฟิลด์ต่างๆ ในชุดข้อมูลนี้ เช่น ชื่อฟิลด์: id, ชนิด: INT, คำอธิบาย: รหัสอ้างอิง
                    </small>
                </div>
            </div>

            <!-- Action Buttons -->
            <div class="action-buttons">
                <a href="<?= base_url('data_catalog_manual') ?>" class="btn btn-secondary">
                    <i class="fas fa-times"></i> ยกเลิก
                </a>
                <button type="submit" class="btn btn-primary">
                    <i class="fas fa-save"></i> บันทึกข้อมูล
                </button>
            </div>
        </form>
    </div>

    <!-- Success Modal -->
    <div class="modal fade" id="successModal" tabindex="-1" role="dialog" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered" role="document">
            <div class="modal-content" style="border-radius: 15px; border: none; box-shadow: 0 10px 40px rgba(0,0,0,0.2);">
                <div class="modal-body text-center" style="padding: 40px 30px;">
                    <div style="margin-bottom: 20px;">
                        <div style="width: 80px; height: 80px; background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); 
                                    border-radius: 50%; display: inline-flex; align-items: center; justify-content: center;
                                    box-shadow: 0 10px 25px rgba(102, 126, 234, 0.4);">
                            <i class="fas fa-check" style="font-size: 40px; color: white;"></i>
                        </div>
                    </div>
                    <h4 style="color: #2c3e50; font-weight: 600; margin-bottom: 15px;">บันทึกข้อมูลสำเร็จ!</h4>
                    <p style="color: #7f8c8d; margin-bottom: 25px;" id="successMessage">
                        ข้อมูลชุดข้อมูลถูกบันทึกเรียบร้อยแล้ว
                    </p>
                    <button type="button" class="btn btn-primary" onclick="redirectToList()" 
                            style="padding: 10px 40px; border-radius: 25px; font-weight: 500;
                                   background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); border: none;
                                   box-shadow: 0 5px 15px rgba(102, 126, 234, 0.4);">
                        <i class="fas fa-list"></i> ไปหน้ารายการ
                    </button>
                </div>
            </div>
        </div>
    </div>

    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@4.6.2/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    
    
<script>
let fieldIndex = <?= ($mode == 'edit' && !empty($metadata)) ? count($metadata) : 0 ?>;
let tableColumns = []; // เก็บข้อมูลคอลัมน์
let originalTableName = '<?= isset($dataset) ? $dataset->table_name : '' ?>'; // เก็บชื่อตารางเดิม
let currentTableName = originalTableName; // เก็บชื่อตารางปัจจุบัน
let hasExistingMetadata = <?= ($mode == 'edit' && !empty($metadata)) ? 'true' : 'false' ?>; // มี metadata เดิมหรือไม่

// เพิ่มฟิลด์ใหม่
function addField() {
    const tableName = $('#table_name').val();
    
    // ถ้าไม่ได้เลือกตาราง ให้แจ้งเตือน
    if (!tableName) {
        Swal.fire({
            icon: 'warning',
            title: 'กรุณาเลือกตาราง',
            text: 'คุณต้องเลือกตารางในฐานข้อมูลก่อนเพิ่มฟิลด์',
            confirmButtonText: 'เข้าใจแล้ว',
            customClass: {
                confirmButton: 'btn btn-primary'
            }
        });
        $('#table_name').focus();
        return;
    }
    
    // โหลดคอลัมน์จากตารางถ้ายังไม่มี
    if (tableColumns.length === 0) {
        loadTableColumns(tableName, function() {
            addFieldRow();
        });
    } else {
        addFieldRow();
    }
}

// เพิ่มแถวฟิลด์
function addFieldRow() {
    let optionsHtml = '<option value="">-- เลือกฟิลด์ --</option>';
    tableColumns.forEach(function(col) {
        optionsHtml += `<option value="${col.field_name}" data-type="${col.field_type}">${col.field_name}</option>`;
    });
    
    const html = `
        <div class="metadata-item" data-index="${fieldIndex}">
            <div class="row align-items-center">
                <div class="col-md-2">
                    <select name="field_name[]" class="form-control field-name-select" onchange="updateFieldType(this)">
                        ${optionsHtml}
                    </select>
                    <input type="text" class="form-control mt-1 d-none field-name-input" 
                           placeholder="พิมพ์ชื่อฟิลด์เอง" style="font-size: 0.9em;">
                    <small class="text-muted" style="cursor: pointer; font-size: 0.85em;" 
                           onclick="toggleFieldInput(this)">
                        <i class="fas fa-edit"></i> พิมพ์เอง
                    </small>
                </div>
                <div class="col-md-2">
                    <input type="text" name="field_name_en[]" class="form-control" 
                           placeholder="ชื่อแสดง (alias)"
                           title="ชื่อที่จะแสดงใน API (ถ้าไม่ใส่จะใช้ชื่อฟิลด์จริง)">
                    <small class="form-text text-muted" style="font-size: 0.8em;">
                        <i class="fas fa-tag"></i> ชื่อแสดง
                    </small>
                </div>
                <div class="col-md-2">
                    <input type="text" name="field_type[]" class="form-control field-type-input" 
                           placeholder="ชนิดข้อมูล" readonly>
                </div>
                <div class="col-md-5">
                    <input type="text" name="field_description[]" class="form-control" 
                           placeholder="คำอธิบาย">
                </div>
                <div class="col-md-1 text-center">
                    <button type="button" class="btn btn-remove-field" onclick="removeField(this)" title="ลบฟิลด์">
                        <i class="fas fa-times"></i>
                    </button>
                </div>
            </div>
        </div>
    `;
    
    $('#metadata-container').append(html);
    fieldIndex++;
}

// โหลดคอลัมน์จากตาราง
function loadTableColumns(tableName, callback) {
    if (!tableName) {
        return;
    }
    
    $.ajax({
        url: '<?= base_url('data_catalog_manual/get_table_columns_ajax') ?>',
        type: 'POST',
        data: { table_name: tableName },
        dataType: 'json',
        success: function(response) {
            if (response.success && response.columns) {
                tableColumns = response.columns;
                if (callback) callback();
            } else {
                Swal.fire({
                    icon: 'error',
                    title: 'ไม่สามารถโหลดโครงสร้างตาราง',
                    text: 'กรุณาลองใหม่อีกครั้ง',
                    confirmButtonText: 'ตรวจสอบ'
                });
            }
        },
        error: function() {
            Swal.fire({
                icon: 'error',
                title: 'เกิดข้อผิดพลาด',
                text: 'ไม่สามารถเชื่อมต่อกับเซิร์ฟเวอร์ได้',
                confirmButtonText: 'ปิด'
            });
        }
    });
}

// อัปเดตชนิดข้อมูลเมื่อเลือกฟิลด์
function updateFieldType(selectElement) {
    const selectedOption = $(selectElement).find('option:selected');
    const fieldType = selectedOption.data('type');
    const typeInput = $(selectElement).closest('.row').find('.field-type-input');
    
    if (fieldType) {
        typeInput.val(fieldType);
    }
}

// สลับระหว่าง select และ input
function toggleFieldInput(element) {
    const container = $(element).parent();
    const select = container.find('.field-name-select');
    const input = container.find('.field-name-input');
    const typeInput = container.closest('.row').find('.field-type-input');
    
    if (select.hasClass('d-none')) {
        // แสดง select ซ่อน input
        select.removeClass('d-none').attr('name', 'field_name[]');
        input.addClass('d-none').removeAttr('name');
        typeInput.prop('readonly', true);
        $(element).html('<i class="fas fa-edit"></i> พิมพ์เอง');
    } else {
        // แสดง input ซ่อน select
        select.addClass('d-none').removeAttr('name');
        input.removeClass('d-none').attr('name', 'field_name[]');
        typeInput.prop('readonly', false);
        $(element).html('<i class="fas fa-list"></i> เลือกจากตาราง');
    }
}

// ลบฟิลด์
function removeField(button) {
    $(button).closest('.metadata-item').fadeOut(300, function() {
        $(this).remove();
    });
}

// ตรวจสอบว่ามีการเปลี่ยนตารางหรือไม่
function checkTableChange() {
    const newTableName = $('#table_name').val();
    const metadataCount = $('#metadata-container .metadata-item').length;
    
    // ถ้าเปลี่ยนตารางและมี metadata เดิมอยู่
    if (originalTableName && newTableName && originalTableName !== newTableName && metadataCount > 0) {
        Swal.fire({
            icon: 'question',
            title: 'ต้องการเปลี่ยนตารางหรือไม่?',
            html: `
                <div style="text-align: left; padding: 10px 20px;">
                    <p style="margin-bottom: 15px; color: #34495e;">
                        <strong>ตารางเดิม:</strong> <code style="background: #ecf0f1; padding: 4px 8px; border-radius: 4px;">${originalTableName}</code>
                    </p>
                    <p style="margin-bottom: 15px; color: #34495e;">
                        <strong>ตารางใหม่:</strong> <code style="background: #d5f4e6; padding: 4px 8px; border-radius: 4px; color: #27ae60;">${newTableName}</code>
                    </p>
                    <div style="background: #fff3cd; padding: 15px; border-left: 4px solid #f39c12; border-radius: 4px; margin-top: 15px;">
                        <p style="margin: 0; color: #856404;">
                            <i class="fas fa-exclamation-triangle"></i>
                            <strong>คำเตือน:</strong> การเปลี่ยนตารางจะลบรายการฟิลด์เดิมทั้งหมด (${metadataCount} รายการ)
                        </p>
                    </div>
                </div>
            `,
            showCancelButton: true,
            confirmButtonText: '<i class="fas fa-check"></i> ยืนยันเปลี่ยนตาราง',
            cancelButtonText: '<i class="fas fa-times"></i> ยกเลิก',
            reverseButtons: true,
            customClass: {
                confirmButton: 'btn btn-primary',
                cancelButton: 'btn btn-secondary'
            }
        }).then((result) => {
            if (result.isConfirmed) {
                // ยืนยันเปลี่ยนตาราง
                currentTableName = newTableName;
                
                // ลบ metadata เดิมทั้งหมด
                $('#metadata-container').fadeOut(300, function() {
                    $(this).empty().fadeIn(300);
                });
                
                // รีเซ็ต tableColumns
                tableColumns = [];
                
                // โหลดคอลัมน์ใหม่
                loadTableColumns(newTableName, function() {
                    Swal.fire({
                        icon: 'success',
                        title: 'เปลี่ยนตารางสำเร็จ',
                        text: 'คุณสามารถเพิ่มฟิลด์ใหม่จากตารางที่เลือกได้',
                        timer: 2000,
                        showConfirmButton: false
                    });
                });
            } else {
                // ยกเลิก - คืนค่าตารางเดิม
                $('#table_name').val(originalTableName);
            }
        });
    } else {
        // ไม่มี metadata หรือไม่ได้เปลี่ยนตาราง
        currentTableName = newTableName;
        tableColumns = [];
        
        if (newTableName) {
            loadTableColumns(newTableName);
        }
    }
}

// Form validation
$('#datasetForm').on('submit', function(e) {
    e.preventDefault();
    
    const datasetName = $('input[name="dataset_name"]').val().trim();
    const categoryId = $('select[name="category_id"]').val();
    const metadataCount = $('#metadata-container .metadata-item').length;
    const tableChanged = originalTableName !== currentTableName;
    
    // Validation
    if (!datasetName) {
        Swal.fire({
            icon: 'warning',
            title: 'กรุณากรอกชื่อชุดข้อมูล',
            text: 'ชื่อชุดข้อมูลเป็นข้อมูลที่จำเป็น',
            confirmButtonText: 'เข้าใจแล้ว'
        });
        $('input[name="dataset_name"]').focus();
        return false;
    }
    
    if (!categoryId) {
        Swal.fire({
            icon: 'warning',
            title: 'กรุณาเลือกหมวดหมู่',
            text: 'หมวดหมู่เป็นข้อมูลที่จำเป็น',
            confirmButtonText: 'เข้าใจแล้ว'
        });
        $('select[name="category_id"]').focus();
        return false;
    }
    
    // ถ้ามี metadata อยู่แล้ว ให้ยืนยันก่อนบันทึก
    if (metadataCount > 0) {
        confirmAndSave(tableChanged);
    } else {
        // ไม่มี metadata ให้บันทึกเลย
        performSave(tableChanged);
    }
    
    return false;
});

// ฟังก์ชันยืนยันก่อนบันทึก
function confirmAndSave(tableChanged) {
    const metadataCount = $('#metadata-container .metadata-item').length;
    const tableName = $('#table_name').val();
    
    let warningHtml = `
        <div style="text-align: left; padding: 10px 20px;">
            <p style="margin-bottom: 15px; color: #34495e; font-size: 1.05rem;">
                คุณกำลังจะบันทึกข้อมูลชุดข้อมูลนี้
            </p>
    `;
    
    if (tableChanged) {
        warningHtml += `
            <div style="background: #ffe5e5; padding: 15px; border-left: 4px solid #e74c3c; border-radius: 4px; margin-bottom: 15px;">
                <p style="margin: 0; color: #c0392b;">
                    <i class="fas fa-exclamation-triangle"></i>
                    <strong>การเปลี่ยนตาราง:</strong> จะลบฟิลด์เดิมทั้งหมดและบันทึกฟิลด์ใหม่
                </p>
            </div>
        `;
    }
    
    warningHtml += `
            <div style="background: #e8f5e9; padding: 15px; border-left: 4px solid #27ae60; border-radius: 4px;">
                <p style="margin: 0 0 10px 0; color: #27ae60; font-weight: 600;">
                    <i class="fas fa-info-circle"></i> ข้อมูลที่จะบันทึก:
                </p>
                <ul style="margin: 0; padding-left: 20px; color: #2c3e50;">
                    <li>ตาราง: <strong>${tableName || 'ไม่ระบุ'}</strong></li>
                    <li>จำนวนฟิลด์: <strong>${metadataCount} รายการ</strong></li>
                </ul>
            </div>
        </div>
    `;
    
    Swal.fire({
        icon: 'question',
        title: 'ยืนยันการบันทึกข้อมูล',
        html: warningHtml,
        showCancelButton: true,
        confirmButtonText: '<i class="fas fa-save"></i> ยืนยันบันทึก',
        cancelButtonText: '<i class="fas fa-times"></i> ยกเลิก',
        reverseButtons: true,
        customClass: {
            confirmButton: 'btn btn-primary',
            cancelButton: 'btn btn-secondary'
        }
    }).then((result) => {
        if (result.isConfirmed) {
            performSave(tableChanged);
        }
    });
}

// ฟังก์ชันบันทึกข้อมูลจริง
function performSave(tableChanged) {
    const formData = $('#datasetForm').serialize() + '&table_changed=' + tableChanged;
    
    // แสดง loading
    const submitBtn = $('#datasetForm').find('button[type="submit"]');
    const originalText = submitBtn.html();
    submitBtn.prop('disabled', true).html('<i class="fas fa-spinner fa-spin"></i> กำลังบันทึก...');
    
    // แสดง progress
    Swal.fire({
        title: 'กำลังบันทึกข้อมูล...',
        html: '<div class="progress" style="height: 25px; margin-top: 20px;"><div class="progress-bar progress-bar-striped progress-bar-animated" role="progressbar" style="width: 100%; background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);"></div></div>',
        allowOutsideClick: false,
        showConfirmButton: false,
        didOpen: () => {
            Swal.showLoading();
        }
    });
    
    // ส่งข้อมูลผ่าน AJAX
    $.ajax({
        url: '<?= base_url('data_catalog_manual/save') ?>',
        type: 'POST',
        data: formData,
        dataType: 'json',
        success: function(response) {
            if (response.success) {
                Swal.fire({
                    icon: 'success',
                    title: 'บันทึกข้อมูลสำเร็จ!',
                    text: response.message,
                    confirmButtonText: '<i class="fas fa-list"></i> ไปหน้ารายการ',
                    allowOutsideClick: false
                }).then((result) => {
                    if (result.isConfirmed) {
                        redirectToList();
                    }
                });
                
                // Auto redirect หลัง 2 วินาที
                setTimeout(function() {
                    redirectToList();
                }, 2000);
            } else {
                Swal.fire({
                    icon: 'error',
                    title: 'เกิดข้อผิดพลาด',
                    text: response.message || 'ไม่สามารถบันทึกข้อมูลได้',
                    confirmButtonText: 'ลองใหม่อีกครั้ง'
                });
                submitBtn.prop('disabled', false).html(originalText);
            }
        },
        error: function(xhr, status, error) {
            console.error('Error:', error);
            Swal.fire({
                icon: 'error',
                title: 'เกิดข้อผิดพลาด',
                text: 'ไม่สามารถเชื่อมต่อกับเซิร์ฟเวอร์ได้',
                confirmButtonText: 'ปิด'
            });
            submitBtn.prop('disabled', false).html(originalText);
        }
    });
}

// ฟังก์ชัน redirect
function redirectToList() {
    window.location.href = '<?= base_url('data_catalog_manual') ?>';
}

// โหลดตารางเมื่อเปลี่ยนหมวดหมู่
$('select[name="category_id"]').on('change', function() {
    // ไม่ต้องโหลดตารางตามหมวดหมู่แล้ว เพราะใช้ get_all_database_tables
    // แค่แจ้งเตือนว่าเปลี่ยนหมวดหมู่แล้ว
});

// เมื่อเปลี่ยนตาราง
$('#table_name').on('change', function() {
    checkTableChange();
});

// อัปเดต dropdown ฟิลด์ที่มีอยู่แล้ว
function updateExistingFieldDropdowns() {
    $('.field-name-select').each(function() {
        const currentValue = $(this).val();
        let optionsHtml = '<option value="">-- เลือกฟิลด์ --</option>';
        
        tableColumns.forEach(function(col) {
            const selected = (currentValue === col.field_name) ? 'selected' : '';
            optionsHtml += `<option value="${col.field_name}" data-type="${col.field_type}" ${selected}>${col.field_name}</option>`;
        });
        
        $(this).html(optionsHtml);
    });
}

// Auto resize textarea
$('textarea').on('input', function() {
    this.style.height = 'auto';
    this.style.height = (this.scrollHeight) + 'px';
});

// โหลดคอลัมน์เมื่อหน้าโหลดเสร็จ (สำหรับ Edit mode)
$(document).ready(function() {
    const tableName = $('#table_name').val();
    const mode = '<?= $mode ?>';
    
    // ถ้าเป็น edit mode และมีตารางเลือกอยู่แล้ว
    if (mode === 'edit' && tableName) {
        currentTableName = tableName;
        loadTableColumns(tableName, function() {
            // อัปเดต dropdown ที่มีอยู่
            updateExistingFieldDropdowns();
        });
        
        // Auto-generate URLs ถ้ามี dataset_id
        const datasetId = $('input[name="dataset_id"]').val();
        if (datasetId) {
            autoGenerateUrls(datasetId);
        }
    }
});

// Auto-generate URLs เมื่อเลือกข้อมูลครบ
function autoGenerateUrls(datasetId = null) {
    if (datasetId) {
        const downloadUrl = 'api_data_catalog/download/' + datasetId;
        const apiEndpoint = 'api_data_catalog/dataset/' + datasetId;
        
        $('input[name="download_url"]').val(downloadUrl);
        $('input[name="api_endpoint"]').val(apiEndpoint);
        
        addUrlHelperText();
    }
}

// เพิ่ม helper text สำหรับ URLs
function addUrlHelperText() {
    const baseUrl = '<?= base_url() ?>';
    
    if (!$('.download-helper').length) {
        const downloadPath = $('input[name="download_url"]').val();
        const downloadFullUrl = downloadPath.indexOf('http') === 0 ? downloadPath : baseUrl + downloadPath;
        
        $('input[name="download_url"]').after(`
            <small class="form-text text-muted download-helper">
                <i class="fas fa-download"></i> 
                รองรับ: CSV, JSON, Excel 
                <a href="${downloadFullUrl}?format=csv" target="_blank" class="ml-2">
                    <i class="fas fa-external-link-alt"></i> ทดสอบ
                </a>
            </small>
        `);
    }
    
    if (!$('.api-helper').length) {
        const apiPath = $('input[name="api_endpoint"]').val();
        const apiFullUrl = apiPath.indexOf('http') === 0 ? apiPath : baseUrl + apiPath;
        
        $('input[name="api_endpoint"]').after(`
            <small class="form-text text-muted api-helper">
                <i class="fas fa-code"></i> 
                REST API (JSON) 
                <a href="${apiFullUrl}" target="_blank" class="ml-2">
                    <i class="fas fa-external-link-alt"></i> ทดสอบ
                </a>
            </small>
        `);
    }
}
</script>
</body>
</html>