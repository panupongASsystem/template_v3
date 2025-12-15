<!DOCTYPE html>
<html lang="th">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= isset($page_title) ? $page_title : 'รายงานแจ้งเรื่อง ร้องเรียน' ?> - Preview</title>
    
    <!-- Bootstrap 5 -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
    
    <style>
        /* Print Styles สำหรับการพิมพ์ขนาด A4 */
        @page {
            size: A4;
            margin: 15mm;
        }
        
        @media print {
            /* ซ่อนส่วนที่ไม่ต้องการในการพิมพ์ */
            .no-print { 
                display: none !important; 
            }
            .print-only { 
                display: block !important; 
            }
            
            /* Reset สำหรับการพิมพ์ */
            * {
                -webkit-print-color-adjust: exact !important;
                color-adjust: exact !important;
                print-color-adjust: exact !important;
            }
            
            body { 
                margin: 0; 
                padding: 0;
                background: white !important;
                font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
                font-size: 12pt;
                line-height: 1.4;
                color: #000 !important;
            }
            
            .container-fluid { 
                max-width: none; 
                margin: 0; 
                padding: 0;
                width: 100%;
                box-shadow: none !important;
            }
            
            /* Header สำหรับการพิมพ์ */
            .preview-header {
                background: #f8f9fa !important;
                color: #000 !important;
                padding: 15mm 0 10mm 0;
                text-align: center;
                border-bottom: 2px solid #dee2e6;
                margin-bottom: 5mm;
                page-break-inside: avoid;
            }
            
            .preview-header h1 {
                font-size: 18pt;
                font-weight: bold;
                margin: 0 0 5mm 0;
                color: #000 !important;
            }
            
            .preview-header p {
                font-size: 12pt;
                margin: 0;
                color: #666 !important;
            }
            
            /* Content สำหรับการพิมพ์ */
            .preview-content {
                padding: 0;
                margin: 0;
            }
            
            /* Sections */
            .section {
                margin: 5mm 0;
                padding: 3mm;
                background: #f8f9fa !important;
                border-left: 3pt solid #6c757d !important;
                border-radius: 0;
                page-break-inside: avoid;
                box-shadow: none !important;
            }
            
            .section-title {
                color: #000 !important;
                font-weight: bold;
                font-size: 14pt;
                margin-bottom: 3mm;
                border-bottom: 1pt solid #dee2e6;
                padding-bottom: 2mm;
            }
            
            .section-title i {
                margin-right: 2mm;
            }
            
            /* Info rows */
            .info-row {
                display: flex;
                margin: 2mm 0;
                padding: 1mm 0;
                border-bottom: 0.5pt dotted #ccc;
                page-break-inside: avoid;
            }
            
            .info-label {
                font-weight: bold;
                color: #000 !important;
                min-width: 40mm;
                flex-shrink: 0;
                font-size: 11pt;
            }
            
            .info-value {
                color: #000 !important;
                flex: 1;
                font-size: 11pt;
                word-wrap: break-word;
            }
            
            /* Status badge */
            .status-badge {
                display: inline-block;
                padding: 1mm 3mm;
                border-radius: 3mm;
                background: #e9ecef !important;
                color: #000 !important;
                font-size: 10pt;
                font-weight: bold;
                border: 1pt solid #6c757d;
            }
            
            /* รายละเอียดเรื่องร้องเรียน */
            .bg-white {
                background: white !important;
                padding: 3mm;
                border: 1pt solid #dee2e6;
                border-radius: 2mm;
                margin-top: 2mm;
            }
            
            /* Timeline Styles สำหรับการพิมพ์ */
            .timeline-container {
                margin: 3mm 0;
            }
            
            .timeline-item {
                margin: 3mm 0;
                padding: 3mm;
                background: white !important;
                border: 1pt solid #dee2e6;
                border-radius: 2mm;
                page-break-inside: avoid;
                position: relative;
                padding-left: 8mm;
            }
            
            .timeline-item::before {
                content: '●';
                position: absolute;
                left: 2mm;
                top: 3mm;
                color: #6c757d;
                font-size: 8pt;
            }
            
            .timeline-header {
                display: flex;
                justify-content: space-between;
                align-items: center;
                margin-bottom: 2mm;
                padding-bottom: 1mm;
                border-bottom: 0.5pt solid #dee2e6;
                flex-wrap: wrap;
                gap: 2mm;
            }
            
            .timeline-status {
                display: inline-block;
                padding: 1mm 3mm;
                background: #e9ecef !important;
                color: #000 !important;
                border: 1pt solid #6c757d;
                border-radius: 3mm;
                font-size: 10pt;
                font-weight: bold;
            }
            
            .timeline-date {
                color: #666 !important;
                font-size: 10pt;
                font-weight: normal;
            }
            
            .timeline-content {
                color: #000 !important;
                line-height: 1.4;
                margin-bottom: 2mm;
                font-size: 11pt;
                word-wrap: break-word;
            }
            
            .timeline-by {
                color: #666 !important;
                font-size: 10pt;
                font-style: italic;
                text-align: right;
            }
            
            .text-muted {
                color: #666 !important;
                font-style: italic;
            }
            
            /* Images Section สำหรับการพิมพ์ */
            .images-container {
                margin: 3mm 0;
            }
            
            .image-item {
                margin: 5mm 0;
                page-break-inside: avoid;
                background: white !important;
                border: 1pt solid #dee2e6;
                border-radius: 2mm;
                padding: 3mm;
            }
            
            .image-header {
                font-size: 11pt;
                font-weight: bold;
                color: #000 !important;
                margin-bottom: 3mm;
                padding-bottom: 2mm;
                border-bottom: 0.5pt solid #dee2e6;
            }
            
            .image-display {
                text-align: center;
                margin: 3mm 0;
            }
            
            .complain-image {
                max-width: 100% !important;
                max-height: 120mm !important; /* จำกัดความสูงสำหรับ A4 */
                height: auto !important;
                width: auto !important;
                border: 1pt solid #ccc !important;
                border-radius: 2mm;
                display: block !important;
                margin: 0 auto;
                -webkit-print-color-adjust: exact !important;
                color-adjust: exact !important;
                print-color-adjust: exact !important;
            }
            
            .image-error {
                text-align: center !important;
                padding: 10mm !important;
                background: #f8f9fa !important;
                border: 1pt dashed #ccc !important;
                border-radius: 2mm;
                color: #666 !important;
                font-size: 10pt;
            }
            
            .image-error i {
                font-size: 20pt;
                margin-bottom: 3mm;
                color: #ccc !important;
            }
            
            .image-error p {
                margin: 2mm 0;
                font-weight: bold;
            }
            
            .image-error small {
                font-size: 9pt;
                color: #999 !important;
            }
            
            /* Page break หลังรูปภาพ */
            .page-break-after-image {
                page-break-after: always;
                height: 0;
                margin: 0;
                padding: 0;
            }
            
            /* Metadata */
            .metadata {
                background: #f8f9fa !important;
                padding: 3mm;
                border: 1pt solid #dee2e6;
                border-radius: 2mm;
                margin: 5mm 0;
                font-size: 10pt;
                color: #666 !important;
                page-break-inside: avoid;
            }
            
            /* Row layout */
            .row {
                display: flex;
                margin: 0;
            }
            
            .col-md-6 {
                flex: 1;
                padding: 0 2mm;
            }
            
            .text-end {
                text-align: right;
            }
            
            /* Page breaks */
            .page-break {
                page-break-before: always;
            }
            
            /* Footer สำหรับการพิมพ์ */
            .print-footer {
                position: fixed;
                bottom: 0;
                left: 0;
                right: 0;
                height: 10mm;
                text-align: center;
                font-size: 9pt;
                color: #666;
                border-top: 0.5pt solid #ccc;
                padding-top: 2mm;
                background: white;
            }
            
            /* หมายเลขหน้า */
            @page {
                @bottom-center {
                    content: "หน้า " counter(page) " จาก " counter(pages);
                    font-size: 9pt;
                    color: #666;
                }
            }
        }
        
        /* Screen Styles */
        @media screen {
            .preview-container {
                background: white;
                min-height: 100vh;
                box-shadow: 0 0 20px rgba(0,0,0,0.1);
            }
            
            .preview-header {
                background: linear-gradient(135deg, #e2e8f0 0%, #cbd5e1 100%);
                color: #475569;
                padding: 2rem;
                text-align: center;
            }
            
            .preview-content {
                padding: 2rem;
            }
            
            .section {
                margin: 2rem 0;
                padding: 1.5rem;
                background: #f8f9fa;
                border-left: 4px solid #94a3b8;
                border-radius: 8px;
            }
            
            .section-title {
                color: #64748b;
                font-weight: bold;
                font-size: 1.2rem;
                margin-bottom: 1rem;
            }
            
            .info-row {
                display: flex;
                margin: 0.8rem 0;
                padding: 0.5rem 0;
                border-bottom: 1px dotted #dee2e6;
            }
            
            .info-label {
                font-weight: bold;
                color: #495057;
                min-width: 150px;
                flex-shrink: 0;
            }
            
            .info-value {
                color: #212529;
                flex: 1;
            }
            
            .status-badge {
                display: inline-block;
                padding: 0.5rem 1rem;
                border-radius: 25px;
                background: #94a3b8;
                color: white;
                font-size: 0.9rem;
                font-weight: bold;
            }
            
            .action-buttons {
                position: fixed;
                top: 20px;
                right: 20px;
                z-index: 1000;
                display: flex;
                gap: 10px;
            }
            
            .btn-action {
                border-radius: 50px;
                padding: 12px 20px;
                font-weight: 600;
                text-decoration: none;
                display: inline-flex;
                align-items: center;
                gap: 8px;
                box-shadow: 0 4px 15px rgba(0,0,0,0.2);
                transition: all 0.3s ease;
            }
            
            .btn-action:hover {
                transform: translateY(-2px);
                box-shadow: 0 6px 20px rgba(0,0,0,0.3);
            }
            
            .export-section {
                background: linear-gradient(135deg, #e3f2fd, #bbdefb);
                border: 2px solid #2196f3;
                border-radius: 15px;
                padding: 2rem;
                margin: 2rem 0;
            }
            
            .export-buttons {
                display: flex;
                gap: 1rem;
                flex-wrap: wrap;
                justify-content: center;
                margin-top: 1rem;
            }
            
            .btn-export {
                padding: 1rem 2rem;
                border: none;
                border-radius: 50px;
                font-weight: bold;
                cursor: pointer;
                transition: all 0.3s ease;
                display: inline-flex;
                align-items: center;
                gap: 0.5rem;
                text-decoration: none;
            }
            
            .btn-export.pdf {
                background: linear-gradient(135deg, #fecaca, #ef4444);
                color: #991b1b;
            }
            
            .btn-export.csv {
                background: linear-gradient(135deg, #a7f3d0, #10b981);
                color: #065f46;
            }
            
            .btn-export:hover {
                transform: translateY(-3px);
                box-shadow: 0 8px 25px rgba(0,0,0,0.3);
            }
            
            .btn-export.pdf:hover {
                background: linear-gradient(135deg, #ef4444, #dc2626);
                color: white;
            }
            
            .btn-export.csv:hover {
                background: linear-gradient(135deg, #10b981, #059669);
                color: white;
            }
            
            .metadata {
                background: #f8f9fa;
                padding: 1rem;
                border-radius: 8px;
                margin: 1rem 0;
                font-size: 0.9rem;
                color: #6c757d;
            }
            
            /* Timeline Styles สำหรับหน้าจอ */
            .timeline-container {
                margin: 1rem 0;
            }
            
            .timeline-item {
                margin: 1rem 0;
                padding: 1.5rem;
                background: white;
                border: 1px solid #dee2e6;
                border-radius: 8px;
                position: relative;
                margin-left: 20px;
                box-shadow: 0 2px 4px rgba(0,0,0,0.1);
            }
            
            .timeline-item::before {
                content: '●';
                position: absolute;
                left: -25px;
                top: 1.5rem;
                color: #94a3b8;
                font-size: 12px;
                background: white;
                padding: 2px;
            }
            
            .timeline-header {
                display: flex;
                justify-content: space-between;
                align-items: center;
                margin-bottom: 0.8rem;
                padding-bottom: 0.5rem;
                border-bottom: 1px solid #dee2e6;
                flex-wrap: wrap;
                gap: 0.5rem;
            }
            
            .timeline-status {
                display: inline-block;
                padding: 0.3rem 0.8rem;
                background: #e9ecef;
                color: #495057;
                border-radius: 15px;
                font-size: 0.85rem;
                font-weight: bold;
            }
            
            .timeline-date {
                color: #6c757d;
                font-size: 0.9rem;
                font-weight: normal;
            }
            
            .timeline-content {
                color: #212529;
                line-height: 1.5;
                margin-bottom: 0.8rem;
                font-size: 1rem;
            }
            
            .timeline-by {
                color: #6c757d;
                font-size: 0.9rem;
                font-style: italic;
                text-align: right;
            }
            
            .text-muted {
                color: #6c757d !important;
                font-style: italic;
            }
            
            /* Images Section สำหรับหน้าจอ */
            .images-container {
                margin: 1rem 0;
            }
            
            .image-item {
                margin: 1.5rem 0;
                background: white;
                border: 1px solid #dee2e6;
                border-radius: 8px;
                padding: 1.5rem;
                box-shadow: 0 2px 4px rgba(0,0,0,0.1);
            }
            
            .image-header {
                font-size: 1rem;
                font-weight: bold;
                color: #495057;
                margin-bottom: 1rem;
                padding-bottom: 0.5rem;
                border-bottom: 1px solid #dee2e6;
            }
            
            .image-display {
                text-align: center;
                margin: 1rem 0;
            }
            
            .complain-image {
                max-width: 100%;
                max-height: 400px;
                height: auto;
                width: auto;
                border: 1px solid #ccc;
                border-radius: 8px;
                display: block;
                margin: 0 auto;
                cursor: pointer;
                transition: transform 0.3s ease;
            }
            
            .complain-image:hover {
                transform: scale(1.02);
                box-shadow: 0 4px 8px rgba(0,0,0,0.2);
            }
            
            .image-error {
                text-align: center;
                padding: 2rem;
                background: #f8f9fa;
                border: 2px dashed #ccc;
                border-radius: 8px;
                color: #6c757d;
                font-size: 0.95rem;
            }
            
            .image-error i {
                font-size: 2rem;
                margin-bottom: 1rem;
                color: #ccc;
                display: block;
            }
            
            .image-error p {
                margin: 0.5rem 0;
                font-weight: bold;
            }
            
            .image-error small {
                font-size: 0.85rem;
                color: #999;
            }
            
            /* ซ่อน page break ในหน้าจอ */
            .page-break-after-image {
                display: none;
            }
        }
    </style>
</head>
<body>
    <!-- Action Buttons -->
    <div class="action-buttons no-print">
        <button onclick="window.print()" class="btn btn-info btn-action">
            <i class="fas fa-print"></i> พิมพ์ / PDF
        </button>
        <button onclick="window.close()" class="btn btn-secondary btn-action">
            <i class="fas fa-times"></i> ปิด
        </button>
    </div>

    <div class="container-fluid preview-container">
        <!-- Header -->
        <div class="preview-header">
            <h1><i class="fas fa-file-alt me-2"></i><?= isset($page_title) ? $page_title : 'รายงานแจ้งเรื่อง ร้องเรียน' ?></h1>
            <p class="mb-0">
                <?= isset($tenant_name) ? $tenant_name : 'ระบบจัดการแจ้งเรื่อง ร้องเรียน' ?> | 
                <?= isset($export_date) ? $export_date : date('d/m/Y H:i:s') ?>
            </p>
        </div>

        <!-- Content -->
        <div class="preview-content">
            <?php if (isset($complain_data) && $complain_data): ?>
            
            <!-- Basic Information -->
            <div class="section">
                <div class="section-title">
                    <i class="fas fa-info-circle me-2"></i>ข้อมูลเบื้องต้น
                </div>
                
                <div class="info-row">
                    <div class="info-label">หมายเลขเรื่อง:</div>
                    <div class="info-value"><strong>#<?= isset($complain_data['complain_id']) ? htmlspecialchars($complain_data['complain_id']) : 'N/A' ?></strong></div>
                </div>
                
                <div class="info-row">
                    <div class="info-label">หัวข้อ:</div>
                    <div class="info-value"><?= isset($complain_data['complain_topic']) ? htmlspecialchars($complain_data['complain_topic']) : 'ไม่ระบุ' ?></div>
                </div>
                
                <div class="info-row">
                    <div class="info-label">สถานะ:</div>
                    <div class="info-value">
                        <span class="status-badge"><?= isset($complain_data['complain_status']) ? htmlspecialchars($complain_data['complain_status']) : 'ไม่ระบุ' ?></span>
                    </div>
                </div>
                
                <div class="info-row">
                    <div class="info-label">ผู้แจ้ง:</div>
                    <div class="info-value"><?= isset($complain_data['complain_by']) ? htmlspecialchars($complain_data['complain_by']) : 'ไม่ระบุ' ?></div>
                </div>
                
                <div class="info-row">
                    <div class="info-label">เบอร์ติดต่อ:</div>
                    <div class="info-value"><?= isset($complain_data['complain_phone']) ? htmlspecialchars($complain_data['complain_phone']) : 'ไม่ระบุ' ?></div>
                </div>
                
                <div class="info-row">
                    <div class="info-label">วันที่แจ้ง:</div>
                    <div class="info-value">
                        <?php 
                        if (isset($complain_data['complain_datesave']) && $complain_data['complain_datesave']) {
                            echo date('d/m/Y H:i', strtotime($complain_data['complain_datesave'])) . ' น.';
                        } else {
                            echo 'ไม่ระบุ';
                        }
                        ?>
                    </div>
                </div>
            </div>

            <!-- Details -->
            <div class="section">
                <div class="section-title">
                    <i class="fas fa-file-text me-2"></i>รายละเอียดแจ้งเรื่อง ร้องเรียน
                </div>
                <div class="bg-white p-3 rounded">
                    <?= isset($complain_data['complain_detail']) ? nl2br(htmlspecialchars($complain_data['complain_detail'])) : 'ไม่มีรายละเอียด' ?>
                </div>
            </div>

            <!-- Timeline/History -->
            <div class="section">
                <div class="section-title">
                    <i class="fas fa-history me-2"></i>ประวัติการดำเนินงาน
                </div>
                
                <div class="timeline-container">
                    <!-- รายการเริ่มต้น -->
                    <div class="timeline-item">
                        <div class="timeline-header">
                            <span class="timeline-status">รอรับเรื่อง</span>
                            <span class="timeline-date">
                                <?php
                                if (isset($complain_data['complain_datesave']) && $complain_data['complain_datesave']) {
                                    echo date('d/m/Y H:i', strtotime($complain_data['complain_datesave'])) . ' น.';
                                } else {
                                    echo 'ไม่ระบุ';
                                }
                                ?>
                            </span>
                        </div>
                        <div class="timeline-content">
                            เรื่องร้องเรียนถูกส่งเข้าระบบ: <?= isset($complain_data['complain_topic']) ? htmlspecialchars($complain_data['complain_topic']) : 'ไม่ระบุ' ?>
                        </div>
                        <div class="timeline-by">โดย: <?= isset($complain_data['complain_by']) ? htmlspecialchars($complain_data['complain_by']) : 'ไม่ระบุ' ?></div>
                    </div>

                    <!-- ประวัติการอัพเดทสถานะ (ถ้ามี) -->
                    <?php if (isset($complain_details) && !empty($complain_details)): ?>
                        <?php foreach ($complain_details as $detail): ?>
                            <div class="timeline-item">
                                <div class="timeline-header">
                                    <span class="timeline-status"><?= htmlspecialchars($detail->complain_detail_status ?? '') ?></span>
                                    <span class="timeline-date">
                                        <?php
                                        if (isset($detail->complain_detail_datesave)) {
                                            echo date('d/m/Y H:i', strtotime($detail->complain_detail_datesave)) . ' น.';
                                        } else {
                                            echo 'ไม่ระบุ';
                                        }
                                        ?>
                                    </span>
                                </div>
                                <div class="timeline-content">
                                    <?= nl2br(htmlspecialchars($detail->complain_detail_com ?? '')) ?>
                                </div>
                                <div class="timeline-by">โดย: <?= htmlspecialchars($detail->complain_detail_by ?? '') ?></div>
                            </div>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <div class="timeline-item">
                            <div class="timeline-content text-muted">
                                ยังไม่มีการอัพเดทสถานะเพิ่มเติม
                            </div>
                        </div>
                    <?php endif; ?>
                </div>
            </div>

            <!-- Images Section (ถ้ามีรูปภาพ) -->
            <?php if (isset($complain_images) && !empty($complain_images)): ?>
            <div class="section">
                <div class="section-title">
                    <i class="fas fa-images me-2"></i>รูปภาพประกอบ (<?= count($complain_images) ?> รูป)
                </div>
                
                <div class="images-container">
                    <?php foreach ($complain_images as $index => $image): ?>
                        <div class="image-item">
                            <div class="image-header">
                                <strong>รูปที่ <?= $index + 1 ?>:</strong> 
                                <?= htmlspecialchars($image->complain_img_img ?? '') ?>
                            </div>
                            
                            <div class="image-display">
                                <img src="<?= base_url('docs/complain/' . ($image->complain_img_img ?? '')) ?>" 
                                     alt="รูปภาพประกอบ รูปที่ <?= $index + 1 ?>"
                                     class="complain-image"
                                     onerror="this.style.display='none'; this.nextElementSibling.style.display='block';">
                                
                                <div class="image-error" style="display: none;">
                                    <i class="fas fa-image"></i>
                                    <p>ไม่สามารถแสดงรูปภาพได้</p>
                                    <small>ไฟล์: <?= htmlspecialchars($image->complain_img_img ?? '') ?></small>
                                </div>
                            </div>
                        </div>
                        
                        <!-- Page break หลังรูปภาพ (เฉพาะการพิมพ์) -->
                        <?php if ($index < count($complain_images) - 1): ?>
                            <div class="page-break-after-image"></div>
                        <?php endif; ?>
                        
                    <?php endforeach; ?>
                </div>
            </div>
            <?php endif; ?>

            <!-- Export Section -->
            <div class="export-section no-print">
                <div class="text-center">
                    <h4><i class="fas fa-download me-2"></i>ส่งออกรายงาน</h4>
                    <p class="text-muted">เลือกรูปแบบไฟล์ที่ต้องการส่งออก</p>
                    
                    <div class="export-buttons">
                        
                        
                        <button class="btn-export csv" onclick="exportFromPreview('csv')">
                            <i class="fas fa-file-csv"></i>
                            ส่งออก CSV
                        </button>
                    </div>
                </div>
            </div>

            <!-- Metadata -->
            <div class="metadata no-print">
                <div class="row">
                    <div class="col-md-6">
                        <strong>ข้อมูลการส่งออก:</strong><br>
                        รหัสเทนแนนท์: <?= isset($tenant_code) ? $tenant_code : 'system' ?><br>
                        วันที่สร้าง: <?= isset($export_date) ? $export_date : date('d/m/Y H:i:s') ?>
                    </div>
                    <div class="col-md-6 text-end">
                        <strong>ชื่อไฟล์:</strong><br>
                        <?= isset($filename) ? $filename : 'complain_report_' . date('YmdHis') ?>
                    </div>
                </div>
            </div>

            <?php else: ?>
            <div class="alert alert-danger">
                <i class="fas fa-exclamation-triangle me-2"></i>
                ไม่พบข้อมูลเรื่องร้องเรียน
            </div>
            <?php endif; ?>
        </div>
    </div>

    <!-- Scripts -->
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

    <script>
    function exportFromPreview(exportType) {
        if (!exportType) {
            console.error('Export type is required');
            return;
        }

        const complainData = <?= isset($complain_data) ? json_encode($complain_data) : 'null' ?>;
        
        if (!complainData) {
            Swal.fire('ข้อผิดพลาด', 'ไม่พบข้อมูลเรื่องร้องเรียน', 'error');
            return;
        }

        Swal.fire({
            title: 'กำลังส่งออกไฟล์...',
            text: 'กรุณารอสักครู่',
            icon: 'info',
            allowOutsideClick: false,
            allowEscapeKey: false,
            showConfirmButton: false,
            didOpen: () => {
                Swal.showLoading();
            }
        });

        $.ajax({
            url: '<?= site_url("System_reports/ajax_export_complain_from_preview") ?>',
            type: 'POST',
            data: {
                export_type: exportType,
                complain_data: JSON.stringify(complainData)
            },
            xhrFields: {
                responseType: 'blob'
            },
            success: function(data, status, xhr) {
                // Get filename from content-disposition header
                var disposition = xhr.getResponseHeader('Content-Disposition');
                var filename = 'complain_report.' + (exportType === 'pdf' ? 'pdf' : 'csv');
                
                if (disposition && disposition.indexOf('attachment') !== -1) {
                    var filenameRegex = /filename[^;=\n]*=((['"]).*?\2|[^;\n]*)/;
                    var matches = filenameRegex.exec(disposition);
                    if (matches != null && matches[1]) {
                        filename = matches[1].replace(/['"]/g, '');
                    }
                }

                // Create download link
                var blob = new Blob([data], { 
                    type: exportType === 'pdf' ? 'application/pdf' : 'text/csv'
                });
                var link = document.createElement('a');
                link.href = window.URL.createObjectURL(blob);
                link.download = filename;
                link.click();

                Swal.fire({
                    title: 'ส่งออกสำเร็จ!',
                    text: 'ไฟล์ได้ถูกบันทึกแล้ว',
                    icon: 'success',
                    timer: 2000,
                    showConfirmButton: false
                });
            },
            error: function(xhr, status, error) {
                console.error('Export error:', error);
                Swal.fire({
                    title: 'เกิดข้อผิดพลาด',
                    text: 'ไม่สามารถส่งออกไฟล์ได้',
                    icon: 'error'
                });
            }
        });
    }

    // เพิ่มฟังก์ชันสำหรับการคลิกขยายรูปภาพ
    function openImageModal(imageSrc, imageTitle) {
        // สร้าง modal สำหรับแสดงรูปภาพขยาย
        const modal = document.createElement('div');
        modal.className = 'image-modal';
        modal.style.cssText = `
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: rgba(0,0,0,0.8);
            display: flex;
            justify-content: center;
            align-items: center;
            z-index: 9999;
            cursor: pointer;
        `;
        
        const img = document.createElement('img');
        img.src = imageSrc;
        img.alt = imageTitle;
        img.style.cssText = `
            max-width: 90%;
            max-height: 90%;
            border-radius: 8px;
            box-shadow: 0 4px 20px rgba(0,0,0,0.5);
        `;
        
        const closeBtn = document.createElement('button');
        closeBtn.innerHTML = '&times;';
        closeBtn.style.cssText = `
            position: absolute;
            top: 20px;
            right: 30px;
            background: white;
            border: none;
            font-size: 2rem;
            width: 40px;
            height: 40px;
            border-radius: 50%;
            cursor: pointer;
            display: flex;
            align-items: center;
            justify-content: center;
        `;
        
        modal.appendChild(img);
        modal.appendChild(closeBtn);
        document.body.appendChild(modal);
        
        // ปิด modal เมื่อคลิก
        modal.addEventListener('click', function() {
            document.body.removeChild(modal);
        });
        
        closeBtn.addEventListener('click', function(e) {
            e.stopPropagation();
            document.body.removeChild(modal);
        });
    }

    // Initialize page
    document.addEventListener('DOMContentLoaded', function() {
        console.log('Preview page loaded successfully');
        
        // เพิ่ม event listener สำหรับรูปภาพ
        const images = document.querySelectorAll('.complain-image');
        images.forEach(function(img) {
            img.addEventListener('click', function() {
                const src = this.src;
                const alt = this.alt;
                openImageModal(src, alt);
            });
        });
    });
    </script>
</body>
</html>