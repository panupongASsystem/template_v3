<!DOCTYPE html>
<html lang="th">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>รายละเอียดการจองคิว #<?= $queue_data['queue_id'] ?></title>
    
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Font Awesome -->
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
    <!-- Google Fonts -->
    <link href="https://fonts.googleapis.com/css2?family=Sarabun:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    
    <style>
        body {
            font-family: 'Sarabun', Arial, sans-serif;
            background: #f8fafc;
            min-height: 100vh;
        }
        
        .preview-container {
            max-width: 900px;
            margin: 0 auto;
            padding: 20px;
        }
        
        .preview-header {
            background: linear-gradient(135deg, #0ea5e9 0%, #0284c7 100%);
            color: white;
            padding: 2rem;
            border-radius: 15px;
            margin-bottom: 2rem;
            text-align: center;
        }
        
        .preview-header h1 {
            font-size: 2rem;
            font-weight: 700;
            margin-bottom: 1rem;
        }
        
        .queue-id-display {
            background: rgba(255, 255, 255, 0.2);
            padding: 0.5rem 1rem;
            border-radius: 25px;
            font-size: 1.2rem;
            font-weight: 600;
            display: inline-block;
            margin-bottom: 1rem;
        }
        
        .export-info {
            font-size: 0.9rem;
            opacity: 0.9;
        }
        
        .action-bar {
            background: white;
            padding: 1rem;
            border-radius: 10px;
            margin-bottom: 2rem;
            box-shadow: 0 2px 10px rgba(0,0,0,0.08);
            display: flex;
            gap: 1rem;
            flex-wrap: wrap;
            justify-content: center;
        }
        
        .pdf-content {
            background: white;
            border-radius: 15px;
            box-shadow: 0 4px 20px rgba(0,0,0,0.1);
            overflow: hidden;
            margin-bottom: 2rem;
        }
        
        /* Include the PDF styles from the template */
        .header {
            text-align: center;
            margin-bottom: 30px;
            padding: 20px;
            border-bottom: 3px solid #0ea5e9;
            background: linear-gradient(135deg, #f0f9ff 0%, #e0f2fe 100%);
        }
        
        .header h1 {
            font-size: 24px;
            font-weight: 700;
            color: #0369a1;
            margin-bottom: 10px;
        }
        
        .header .queue-id {
            font-size: 18px;
            font-weight: 600;
            color: #0284c7;
            margin-bottom: 5px;
        }
        
        .header .export-info-content {
            font-size: 12px;
            color: #64748b;
            margin-top: 10px;
        }
        
        .section {
            margin-bottom: 25px;
            border: 1px solid #e2e8f0;
            border-radius: 8px;
            overflow: hidden;
        }
        
        .section-header {
            background: #f8fafc;
            padding: 12px 15px;
            border-bottom: 1px solid #e2e8f0;
            font-weight: 600;
            font-size: 16px;
            color: #1e293b;
        }
        
        .section-content {
            padding: 15px;
        }
        
        .info-grid {
            display: table;
            width: 100%;
            border-collapse: collapse;
        }
        
        .info-row {
            display: table-row;
        }
        
        .info-label {
            display: table-cell;
            padding: 8px 15px 8px 0;
            font-weight: 600;
            color: #475569;
            width: 30%;
            vertical-align: top;
            border-bottom: 1px solid #f1f5f9;
        }
        
        .info-value {
            display: table-cell;
            padding: 8px 0;
            color: #1e293b;
            vertical-align: top;
            border-bottom: 1px solid #f1f5f9;
            word-wrap: break-word;
        }
        
        .status-badge {
            display: inline-block;
            padding: 6px 12px;
            border-radius: 20px;
            font-size: 12px;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }
        
        .status-waiting {
            background: #fffef7;
            color: #d4ac0d;
            border: 1px solid #f7dc6f;
        }
        
        .status-received {
            background: #f3e5f5;
            color: #6a1b9a;
            border: 1px solid #d1c4e9;
        }
        
        .status-confirmed {
            background: #f0f9ff;
            color: #0284c7;
            border: 1px solid #bae6fd;
        }
        
        .status-processing {
            background: #f0f4ff;
            color: #1d4ed8;
            border: 1px solid #c7d2fe;
        }
        
        .status-completed {
            background: #f0fdf4;
            color: #047857;
            border: 1px solid #bbf7d0;
        }
        
        .status-cancelled {
            background: #fef2f2;
            color: #dc2626;
            border: 1px solid #fecaca;
        }
        
        .timeline {
            position: relative;
        }
        
        .timeline-item {
            margin-bottom: 20px;
            padding-left: 25px;
            position: relative;
            border-left: 2px solid #e2e8f0;
        }
        
        .timeline-item::before {
            content: '';
            position: absolute;
            left: -6px;
            top: 5px;
            width: 10px;
            height: 10px;
            border-radius: 50%;
            background: #0ea5e9;
            border: 2px solid #fff;
        }
        
        .timeline-item.completed::before {
            background: #22c55e;
        }
        
        .timeline-item.cancelled::before {
            background: #ef4444;
        }
        
        .timeline-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 8px;
            flex-wrap: wrap;
        }
        
        .timeline-status {
            font-weight: 600;
            color: #0369a1;
        }
        
        .timeline-date {
            font-size: 12px;
            color: #64748b;
            background: #f1f5f9;
            padding: 4px 8px;
            border-radius: 4px;
        }
        
        .timeline-content {
            margin-bottom: 8px;
            color: #374151;
            background: #f8fafc;
            padding: 10px;
            border-radius: 6px;
            border-left: 3px solid #bae6fd;
        }
        
        .timeline-by {
            font-size: 12px;
            color: #64748b;
            font-style: italic;
        }
        
        .files-list {
            background: #f8fafc;
            padding: 15px;
            border-radius: 6px;
            border: 1px solid #e2e8f0;
        }
        
        .file-item {
            padding: 10px;
            margin-bottom: 8px;
            background: #fff;
            border: 1px solid #e2e8f0;
            border-radius: 4px;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }
        
        .file-name {
            font-weight: 500;
            color: #1e293b;
        }
        
        .file-info {
            font-size: 12px;
            color: #64748b;
        }
        
        .no-files {
            text-align: center;
            color: #64748b;
            font-style: italic;
            padding: 20px;
        }
        
        .footer {
            margin-top: 40px;
            padding: 20px;
            border-top: 2px solid #e2e8f0;
            text-align: center;
            font-size: 12px;
            color: #64748b;
            background: #f8fafc;
        }
        
        @media print {
            .preview-header,
            .action-bar {
                display: none !important;
            }
            
            .pdf-content {
                box-shadow: none !important;
                border-radius: 0 !important;
            }
            
            body {
                background: white !important;
            }
        }
    </style>
</head>
<body>
    <!-- Preview Header (จะซ่อนเมื่อพิมพ์) -->
    <div class="preview-header">
        <h1><i class="fas fa-file-pdf me-3"></i>ตัวอย่าง การพิมพ์การจองคิว</h1>
        <div class="queue-id-display">หมายเลขคิว: #<?= $queue_data['queue_id'] ?></div>
        <div class="export-info">
            สร้างเมื่อ: <?= $export_date ?> | โดย: <?= $exported_by ?>
        </div>
    </div>

    <div class="preview-container">
        <!-- Action Bar (จะซ่อนเมื่อพิมพ์) -->
        <div class="action-bar">
            <button onclick="window.close()" class="btn btn-secondary">
                <i class="fas fa-times me-2"></i>ปิดหน้าต่าง
            </button>
            
            <button onclick="window.print()" class="btn btn-primary">
                <i class="fas fa-print me-2"></i>พิมพ์
            </button>
            
            
            
            <a href="<?= site_url('Queue/queue_detail/' . $queue_data['queue_id']) ?>" 
               class="btn btn-info" target="_blank">
                <i class="fas fa-eye me-2"></i>ดูรายละเอียด
            </a>
            
            <a href="<?= site_url('Queue/queue_report') ?>" 
               class="btn btn-outline-primary" target="_blank">
                <i class="fas fa-list me-2"></i>กลับรายงาน
            </a>
        </div>

        <!-- PDF Content -->
        <div class="pdf-content">
            <!-- Header -->
            <div class="header">
                <h1>รายละเอียดการจองคิว</h1>
                <div class="queue-id">หมายเลขคิว: #<?= $queue_data['queue_id'] ?></div>
                <div class="export-info-content">
                    ส่งออกเมื่อ: <?= $export_date ?> | โดย: <?= $exported_by ?>
                </div>
            </div>

            <!-- Queue Information -->
            <div class="section">
                <div class="section-header">
                    📅 ข้อมูลการจองคิว
                </div>
                <div class="section-content">
                    <div class="info-grid">
                        <div class="info-row">
                            <div class="info-label">หมายเลขคิว:</div>
                            <div class="info-value"><?= htmlspecialchars($queue_data['queue_id']) ?></div>
                        </div>
                        <div class="info-row">
                            <div class="info-label">หัวข้อ:</div>
                            <div class="info-value"><?= htmlspecialchars($queue_data['queue_topic']) ?></div>
                        </div>
                        <div class="info-row">
                            <div class="info-label">รายละเอียด:</div>
                            <div class="info-value"><?= nl2br(htmlspecialchars($queue_data['queue_detail'])) ?></div>
                        </div>
                        <div class="info-row">
                            <div class="info-label">สถานะปัจจุบัน:</div>
                            <div class="info-value">
                                <span class="status-badge <?= $queue_data['status_class'] ?>">
                                    <?= htmlspecialchars($queue_data['status_display']) ?>
                                </span>
                            </div>
                        </div>
                        <div class="info-row">
                            <div class="info-label">วันที่นัดหมาย:</div>
                            <div class="info-value">
                                <?php if (!empty($queue_data['date_thai'])): ?>
                                    <?= $queue_data['date_thai'] ?>
                                    <?php if (!empty($queue_data['queue_time_slot'])): ?>
                                        <br><small>ช่วงเวลา: <?= htmlspecialchars($queue_data['queue_time_slot']) ?></small>
                                    <?php endif; ?>
                                <?php else: ?>
                                    ไม่ระบุวันนัดหมาย
                                <?php endif; ?>
                            </div>
                        </div>
                        <div class="info-row">
                            <div class="info-label">วันที่สร้างคิว:</div>
                            <div class="info-value"><?= $queue_data['created_thai'] ?? 'ไม่ระบุ' ?></div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- User Information -->
            <div class="section">
                <div class="section-header">
                    👤 ข้อมูลผู้จอง
                </div>
                <div class="section-content">
                    <div class="info-grid">
                        <div class="info-row">
                            <div class="info-label">ชื่อผู้จอง:</div>
                            <div class="info-value"><?= htmlspecialchars($queue_data['queue_by']) ?></div>
                        </div>
                        <div class="info-row">
                            <div class="info-label">เบอร์ติดต่อ:</div>
                            <div class="info-value"><?= htmlspecialchars($queue_data['queue_phone']) ?></div>
                        </div>
                        <div class="info-row">
                            <div class="info-label">อีเมล:</div>
                            <div class="info-value"><?= htmlspecialchars($queue_data['queue_email'] ?: 'ไม่ระบุ') ?></div>
                        </div>
                        <?php if (!empty($queue_data['queue_address'])): ?>
                        <div class="info-row">
                            <div class="info-label">ที่อยู่:</div>
                            <div class="info-value"><?= nl2br(htmlspecialchars($queue_data['queue_address'])) ?></div>
                        </div>
                        <?php endif; ?>
                        <div class="info-row">
                            <div class="info-label">ประเภทผู้ใช้:</div>
                            <div class="info-value">
                                <?php
                                $user_type_text = 'ไม่ทราบ';
                                switch($queue_data['queue_user_type'] ?? '') {
                                    case 'public':
                                        $user_type_text = 'สมาชิกสาธารณะ';
                                        break;
                                    case 'staff':
                                        $user_type_text = 'เจ้าหน้าที่';
                                        break;
                                    case 'guest':
                                        $user_type_text = 'ผู้ใช้ทั่วไป';
                                        break;
                                }
                                echo $user_type_text;
                                ?>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Files Section -->
            <?php if (!empty($queue_files)): ?>
            <div class="section">
                <div class="section-header">
                    📎 ไฟล์แนบ (<?= count($queue_files) ?> ไฟล์)
                </div>
                <div class="section-content">
                    <div class="files-list">
                        <?php foreach ($queue_files as $file): ?>
                            <div class="file-item">
                                <div class="file-name"><?= htmlspecialchars($file->queue_file_original_name) ?></div>
                                <div class="file-info">
                                    <?= htmlspecialchars($file->queue_file_type) ?> | 
                                    <?php 
                                    $file_size = $file->queue_file_size;
                                    if ($file_size >= 1048576) {
                                        echo number_format($file_size / 1048576, 2) . ' MB';
                                    } elseif ($file_size >= 1024) {
                                        echo number_format($file_size / 1024, 2) . ' KB';
                                    } else {
                                        echo $file_size . ' bytes';
                                    }
                                    ?>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    </div>
                </div>
            </div>
            <?php else: ?>
            <div class="section">
                <div class="section-header">
                    📎 ไฟล์แนบ
                </div>
                <div class="section-content">
                    <div class="no-files">ไม่มีไฟล์แนบ</div>
                </div>
            </div>
            <?php endif; ?>

            <!-- Timeline Section -->
            <div class="section">
                <div class="section-header">
                    🕐 ประวัติการดำเนินงาน
                </div>
                <div class="section-content">
                    <div class="timeline">
                        <!-- Initial Queue Creation -->
                        <div class="timeline-item">
                            <div class="timeline-header">
                                <div class="timeline-status">สร้างคิว</div>
                                <div class="timeline-date"><?= $queue_data['created_thai'] ?? 'ไม่ระบุ' ?></div>
                            </div>
                            <div class="timeline-content">
                                คิวถูกสร้างขึ้น: <?= htmlspecialchars($queue_data['queue_topic']) ?>
                            </div>
                            <div class="timeline-by">โดย: <?= htmlspecialchars($queue_data['queue_by']) ?></div>
                        </div>

                        <!-- Progress Updates -->
                        <?php if (!empty($queue_details)): ?>
                            <?php foreach ($queue_details as $detail): ?>
                                <div class="timeline-item <?= $detail['status_class'] ?>">
                                    <div class="timeline-header">
                                        <div class="timeline-status"><?= htmlspecialchars($detail['status_display']) ?></div>
                                        <div class="timeline-date"><?= $detail['date_thai'] ?? 'ไม่ระบุ' ?></div>
                                    </div>
                                    <div class="timeline-content">
                                        <?= nl2br(htmlspecialchars($detail['queue_detail_com'])) ?>
                                    </div>
                                    <div class="timeline-by">โดย: <?= htmlspecialchars($detail['queue_detail_by']) ?></div>
                                </div>
                            <?php endforeach; ?>
                        <?php else: ?>
                            <div style="text-align: center; color: #64748b; font-style: italic; padding: 20px;">
                                ยังไม่มีการอัพเดทสถานะ
                            </div>
                        <?php endif; ?>
                    </div>
                </div>
            </div>

            <!-- Footer -->
            <div class="footer">
                <p>รายงานนี้สร้างจากระบบจองคิวอัตโนมัติ</p>
                <p>วันที่พิมพ์: <?= $export_date ?> | ผู้ส่งออก: <?= $exported_by ?></p>
            </div>
        </div>
    </div>

    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    
    <script>
        // Auto focus for better UX
        window.focus();
        
        // Keyboard shortcuts
        document.addEventListener('keydown', function(e) {
            // Ctrl/Cmd + P = Print
            if ((e.ctrlKey || e.metaKey) && e.key === 'p') {
                e.preventDefault();
                window.print();
            }
            
            // Escape = Close window
            if (e.key === 'Escape') {
                window.close();
            }
            
            // Ctrl/Cmd + S = Download
            if ((e.ctrlKey || e.metaKey) && e.key === 's') {
                e.preventDefault();
                window.location.href = '<?= site_url('Queue/download_queue_pdf/' . $queue_data['queue_id']) ?>';
            }
        });
        
        console.log('Queue PDF Preview loaded for Queue ID: <?= $queue_data['queue_id'] ?>');
    </script>
</body>
</html>