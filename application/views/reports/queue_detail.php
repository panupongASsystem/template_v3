<?php
/**
 * Queue Detail View - หน้ารายละเอียดการจองคิว (ปรับปรุงแล้ว)
 * Path: views/reports/queue_detail.php
 */

// Helper function สำหรับ status styling
if (!function_exists('get_queue_status_class')) {
    function get_queue_status_class($status) {
        switch($status) {
            case 'รอยืนยันการจอง': return 'waiting';
            case 'รับเรื่องพิจารณา': return 'received';
            case 'ยืนยันการจอง': 
            case 'คิวได้รับการยืนยัน': return 'confirmed';
            case 'รับเรื่องแล้ว': return 'processing';
            case 'กำลังดำเนินการ': return 'processing';
            case 'รอดำเนินการ': return 'processing';
            case 'เสร็จสิ้น': return 'completed';
            case 'ยกเลิก': 
            case 'คิวได้ถูกยกเลิก': return 'cancelled';
            default: return 'waiting';
        }
    }
}

if (!function_exists('get_queue_status_icon')) {
    function get_queue_status_icon($status) {
        switch($status) {
            case 'รอยืนยันการจอง': return 'fas fa-hourglass-half';
            case 'รับเรื่องพิจารณา': return 'fas fa-file-import';
            case 'ยืนยันการจอง': 
            case 'คิวได้รับการยืนยัน': return 'fas fa-check-circle';
            case 'รับเรื่องแล้ว': return 'fas fa-inbox';
            case 'กำลังดำเนินการ': return 'fas fa-cogs';
            case 'รอดำเนินการ': return 'fas fa-clock';
            case 'เสร็จสิ้น': return 'fas fa-check-double';
            case 'ยกเลิก': 
            case 'คิวได้ถูกยกเลิก': return 'fas fa-times-circle';
            default: return 'fas fa-question-circle';
        }
    }
}

// Helper function สำหรับเซ็นเซอร์เลขบัตรประชาชน
if (!function_exists('censor_id_number')) {
    function censor_id_number($id_number) {
        if (empty($id_number) || strlen($id_number) < 6) {
            return $id_number;
        }
        
        $length = strlen($id_number);
        if ($length == 13) {
            // เลขบัตรประชาชน 13 หลัก แสดงเฉพาะ 3 ตัวแรกและ 4 ตัวท้าย
            return substr($id_number, 0, 3) . str_repeat('*', 6) . substr($id_number, -4);
        } else {
            // กรณีอื่นๆ แสดงเฉพาะ 6 ตัวท้าย
            return str_repeat('*', $length - 6) . substr($id_number, -6);
        }
    }
}

// Helper function สำหรับตรวจสอบประเภทไฟล์
if (!function_exists('get_file_type_info')) {
    function get_file_type_info($file_name, $file_type = '') {
        $extension = strtolower(pathinfo($file_name, PATHINFO_EXTENSION));
        
        // ประเภทรูปภาพ
        $image_types = ['jpg', 'jpeg', 'png', 'gif', 'bmp', 'webp', 'svg'];
        // ประเภท PDF
        $pdf_types = ['pdf'];
        // ประเภทเอกสาร
        $document_types = ['doc', 'docx', 'xls', 'xlsx', 'ppt', 'pptx', 'txt'];
        
        if (in_array($extension, $image_types)) {
            return [
                'type' => 'image',
                'icon' => 'fas fa-image',
                'color' => '#e74c3c',
                'can_preview' => true
            ];
        } elseif (in_array($extension, $pdf_types)) {
            return [
                'type' => 'pdf',
                'icon' => 'fas fa-file-pdf',
                'color' => '#e74c3c',
                'can_preview' => true
            ];
        } elseif (in_array($extension, $document_types)) {
            return [
                'type' => 'document',
                'icon' => 'fas fa-file-alt',
                'color' => '#3498db',
                'can_preview' => false
            ];
        } else {
            return [
                'type' => 'other',
                'icon' => 'fas fa-file',
                'color' => '#95a5a6',
                'can_preview' => false
            ];
        }
    }
}

// Helper function สำหรับจัดรูปแบบที่อยู่
if (!function_exists('format_address')) {
    function format_address($queue_data) {
        $address_parts = [];
        
        // *** แก้ไข: ตรวจสอบรูปแบบข้อมูลที่หลากหลาย ***
        
        // กรณีที่ข้อมูลเป็น array
        if (is_array($queue_data)) {
            $data = $queue_data;
        } 
        // กรณีที่ข้อมูลเป็น object
        elseif (is_object($queue_data)) {
            $data = (array) $queue_data;
        } 
        else {
            return 'ไม่ระบุ';
        }
        
        // *** สำหรับ Guest User ***
        // ดูข้อมูลจาก queue_address และ guest_* fields
        if (!empty($data['queue_address'])) {
            $address_parts[] = $data['queue_address'];
        }
        
        if (!empty($data['guest_district'])) {
            $address_parts[] = 'ตำบล' . $data['guest_district'];
        } elseif (!empty($data['queue_district'])) {
            $address_parts[] = 'ตำบล' . $data['queue_district'];
        }
        
        if (!empty($data['guest_amphoe'])) {
            $address_parts[] = 'อำเภอ' . $data['guest_amphoe'];
        } elseif (!empty($data['queue_amphoe'])) {
            $address_parts[] = 'อำเภอ' . $data['queue_amphoe'];
        }
        
        if (!empty($data['guest_province'])) {
            $address_parts[] = 'จังหวัด' . $data['guest_province'];
        } elseif (!empty($data['queue_province'])) {
            $address_parts[] = 'จังหวัด' . $data['queue_province'];
        }
        
        if (!empty($data['guest_zipcode'])) {
            $address_parts[] = $data['guest_zipcode'];
        } elseif (!empty($data['queue_zipcode'])) {
            $address_parts[] = $data['queue_zipcode'];
        }
        
        // *** สำหรับ Public User - ดูจาก user address ที่ join มา ***
        if (empty($address_parts)) {
            // ลองหาจาก fields ของ public user
            $public_fields = [
                'mp_address' => '',
                'mp_district' => 'ตำบล',
                'mp_amphoe' => 'อำเภอ', 
                'mp_province' => 'จังหวัด',
                'mp_zipcode' => ''
            ];
            
            foreach ($public_fields as $field => $prefix) {
                if (!empty($data[$field])) {
                    $address_parts[] = $prefix . $data[$field];
                }
            }
        }
        
        // *** Debug: แสดงข้อมูลที่มี (เฉพาะ development) ***
        if (ENVIRONMENT === 'development' && empty($address_parts)) {
            error_log('Address Debug - Available fields: ' . implode(', ', array_keys($data)));
            error_log('Address Debug - Data: ' . json_encode($data));
        }
        
        return !empty($address_parts) ? implode(' ', $address_parts) : 'ไม่ระบุ';
    }
}
?>

<style>
/* ===== Queue Detail Specific Styles ===== */
.page-header {
    background: linear-gradient(135deg, #e0f2fe 0%, #b3e5fc 100%);
    color: #0277bd;
    padding: 2rem;
    border-radius: 15px;
    margin-bottom: 2rem;
    box-shadow: 0 4px 15px rgba(0,0,0,0.08);
}

.page-header h1 {
    font-size: 2rem;
    font-weight: 600;
    margin: 0;
    color: #0277bd;
}

.page-header .breadcrumb {
    background: transparent;
    padding: 0;
    margin: 10px 0 0 0;
}

.queue-detail-container {
    background: white;
    border-radius: 15px;
    overflow: hidden;
    box-shadow: 0 4px 15px rgba(0,0,0,0.08);
    margin-bottom: 2rem;
}

.queue-detail-header {
    background: linear-gradient(135deg, #f8fafc 0%, #e2e8f0 100%);
    padding: 1.5rem;
    border-bottom: 1px solid #e2e8f0;
}

.queue-id-badge {
    background: linear-gradient(135deg, #42a5f5, #1e88e5);
    color: white;
    padding: 0.5rem 1rem;
    border-radius: 25px;
    font-size: 1.1rem;
    font-weight: 700;
    display: inline-block;
    margin-bottom: 1rem;
}

.queue-status-current {
    display: inline-flex;
    align-items: center;
    gap: 0.5rem;
    padding: 0.5rem 1rem;
    border-radius: 20px;
    font-weight: 600;
    font-size: 0.9rem;
    margin-left: 1rem;
}

.queue-status-current.waiting {
    background: #fff3cd;
    color: #d68910;
    border: 1px solid #ffb74d;
}

.queue-status-current.received {
    background: #f3e5f5;
    color: #6a1b9a;
    border: 1px solid #9c27b0;
}

.queue-status-current.confirmed {
    background: #e0f7fa;
    color: #00695c;
    border: 1px solid #26c6da;
}

.queue-status-current.processing {
    background: #f3e5f5;
    color: #6a1b9a;
    border: 1px solid #9c27b0;
}

.queue-status-current.completed {
    background: #e8f5e8;
    color: #2e7d32;
    border: 1px solid #66bb6a;
}

.queue-status-current.cancelled {
    background: #ffebee;
    color: #c62828;
    border: 1px solid #ef5350;
}

.info-grid {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(300px, 1fr));
    gap: 1.5rem;
    padding: 1.5rem;
}

.info-item {
    display: flex;
    flex-direction: column;
    gap: 0.5rem;
}

.info-label {
    font-weight: 600;
    color: #64748b;
    font-size: 0.9rem;
    text-transform: uppercase;
    letter-spacing: 0.5px;
}

.info-value {
    color: #1e293b;
    font-size: 1rem;
    line-height: 1.5;
}

.info-value.large {
    font-size: 1.1rem;
    font-weight: 500;
}

.censored-number {
    font-family: 'Courier New', monospace;
    background: #f8fafc;
    padding: 0.25rem 0.5rem;
    border-radius: 4px;
    border: 1px solid #e2e8f0;
}

.section-card {
    background: white;
    border-radius: 15px;
    overflow: hidden;
    box-shadow: 0 4px 15px rgba(0,0,0,0.08);
    margin-bottom: 2rem;
}

.section-header {
    background: #f8fafc;
    padding: 1rem 1.5rem;
    border-bottom: 1px solid #e2e8f0;
    display: flex;
    align-items: center;
    gap: 0.75rem;
}

.section-title {
    font-size: 1.1rem;
    font-weight: 600;
    color: #1e293b;
    margin: 0;
}

.section-icon {
    color: #42a5f5;
    font-size: 1.2rem;
}

.timeline {
    padding: 1.5rem;
}

.timeline-item {
    display: flex;
    gap: 1rem;
    margin-bottom: 1.5rem;
    position: relative;
}

.timeline-item:not(:last-child)::after {
    content: '';
    position: absolute;
    left: 15px;
    top: 40px;
    bottom: -15px;
    width: 2px;
    background: #e2e8f0;
}

.timeline-icon {
    width: 32px;
    height: 32px;
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 0.875rem;
    color: white;
    flex-shrink: 0;
    position: relative;
    z-index: 1;
}

.timeline-icon.waiting { background: linear-gradient(135deg, #ffb74d, #fb8c00); }
.timeline-icon.received { background: linear-gradient(135deg, #9c27b0, #7b1fa2); }
.timeline-icon.confirmed { background: linear-gradient(135deg, #26c6da, #00acc1); }
.timeline-icon.processing { background: linear-gradient(135deg, #9c27b0, #7b1fa2); }
.timeline-icon.completed { background: linear-gradient(135deg, #66bb6a, #43a047); }
.timeline-icon.cancelled { background: linear-gradient(135deg, #ef5350, #e53935); }

.timeline-content {
    flex: 1;
    background: #f8fafc;
    padding: 1rem;
    border-radius: 8px;
    border-left: 3px solid #e2e8f0;
}

.timeline-status {
    font-weight: 600;
    color: #1e293b;
    margin-bottom: 0.5rem;
}

.timeline-meta {
    display: flex;
    justify-content: space-between;
    align-items: center;
    margin-bottom: 0.75rem;
    font-size: 0.875rem;
    color: #64748b;
}

.timeline-comment {
    color: #374151;
    line-height: 1.5;
    font-size: 0.95rem;
}

.files-grid {
    display: grid;
    grid-template-columns: repeat(auto-fill, minmax(250px, 1fr));
    gap: 1rem;
    padding: 1.5rem;
}

.file-card {
    background: #f8fafc;
    border: 1px solid #e2e8f0;
    border-radius: 8px;
    padding: 1rem;
    transition: all 0.3s ease;
    position: relative;
}

.file-card:hover {
    background: #f1f5f9;
    border-color: #42a5f5;
    transform: translateY(-2px);
    box-shadow: 0 4px 12px rgba(66, 165, 245, 0.15);
}

.file-card.image-file {
    border-left: 4px solid #e74c3c;
}

.file-card.pdf-file {
    border-left: 4px solid #e74c3c;
}

.file-card.document-file {
    border-left: 4px solid #3498db;
}

.file-card.other-file {
    border-left: 4px solid #95a5a6;
}

.file-thumbnail {
    width: 100%;
    height: 120px;
    border-radius: 6px;
    object-fit: cover;
    margin-bottom: 0.75rem;
    cursor: pointer;
    transition: all 0.3s ease;
    border: 2px solid #e2e8f0;
}

.file-thumbnail:hover {
    border-color: #42a5f5;
    transform: scale(1.02);
}

.file-info-overlay {
    margin-bottom: 0.75rem;
}

.file-info-overlay .file-name {
    font-weight: 600;
    color: #1e293b;
    font-size: 0.9rem;
    white-space: nowrap;
    overflow: hidden;
    text-overflow: ellipsis;
    margin-bottom: 0.25rem;
}

.file-info-overlay .file-meta {
    font-size: 0.8rem;
    color: #64748b;
}

.file-header {
    display: flex;
    align-items: center;
    gap: 0.75rem;
    margin-bottom: 0.75rem;
}

.file-icon {
    width: 40px;
    height: 40px;
    border-radius: 8px;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 1.25rem;
    color: white;
}

.file-info {
    flex: 1;
    min-width: 0;
}

.file-name {
    font-weight: 600;
    color: #1e293b;
    font-size: 0.9rem;
    white-space: nowrap;
    overflow: hidden;
    text-overflow: ellipsis;
    margin-bottom: 0.25rem;
}

.file-meta {
    font-size: 0.8rem;
    color: #64748b;
}

.file-actions {
    display: flex;
    gap: 0.5rem;
    margin-top: 0.75rem;
}

.file-action-btn {
    padding: 0.375rem 0.75rem;
    border: none;
    border-radius: 6px;
    font-size: 0.8rem;
    cursor: pointer;
    transition: all 0.3s ease;
    text-decoration: none;
    display: inline-flex;
    align-items: center;
    gap: 0.25rem;
}

.btn-preview {
    background: linear-gradient(135deg, #42a5f5, #1e88e5);
    color: white;
}

.btn-preview:hover {
    background: linear-gradient(135deg, #1e88e5, #1976d2);
    transform: translateY(-1px);
    color: white;
}

.btn-download {
    background: linear-gradient(135deg, #10b981, #059669);
    color: white;
}

.btn-download:hover {
    background: linear-gradient(135deg, #059669, #047857);
    transform: translateY(-1px);
    color: white;
}

.action-buttons {
    display: flex;
    gap: 0.75rem;
    margin-top: 1.5rem;
    flex-wrap: wrap;
}

.btn-action {
    padding: 0.75rem 1.5rem;
    border-radius: 8px;
    border: none;
    font-size: 0.9rem;
    font-weight: 500;
    cursor: pointer;
    transition: all 0.3s ease;
    text-decoration: none;
    display: inline-flex;
    align-items: center;
    gap: 0.5rem;
    min-width: 120px;
    justify-content: center;
}

.btn-back {
    background: #6b7280;
    color: white;
}

.btn-back:hover {
    background: #4b5563;
    transform: translateY(-1px);
    color: white;
}

.btn-export {
    background: linear-gradient(135deg, #10b981, #059669);
    color: white;
}

.btn-export:hover {
    background: linear-gradient(135deg, #059669, #047857);
    transform: translateY(-1px);
    color: white;
}

.btn-print {
    background: linear-gradient(135deg, #f59e0b, #d97706);
    color: white;
}

.btn-print:hover {
    background: linear-gradient(135deg, #d97706, #b45309);
    transform: translateY(-1px);
    color: white;
}

.empty-state {
    text-align: center;
    padding: 3rem 1.5rem;
    color: #6b7280;
}

.empty-state i {
    font-size: 3rem;
    margin-bottom: 1rem;
    color: #d1d5db;
}

/* Image Preview Modal Styles */
.image-preview-modal {
    display: none;
    position: fixed;
    z-index: 1000;
    left: 0;
    top: 0;
    width: 100%;
    height: 100%;
    background-color: rgba(0, 0, 0, 0.9);
}

.image-preview-content {
    position: relative;
    margin: auto;
    padding: 0;
    width: 90%;
    max-width: 900px;
    max-height: 90%;
    top: 50%;
    transform: translateY(-50%);
}

.image-preview-content img {
    width: 100%;
    height: auto;
    max-height: 80vh;
    object-fit: contain;
    border-radius: 8px;
}

.close-preview {
    position: absolute;
    top: 15px;
    right: 35px;
    color: #f1f1f1;
    font-size: 40px;
    font-weight: bold;
    cursor: pointer;
    z-index: 1001;
}

.close-preview:hover {
    color: #bbb;
}

.preview-info {
    background: rgba(0, 0, 0, 0.7);
    color: white;
    padding: 1rem;
    border-radius: 0 0 8px 8px;
    text-align: center;
}

/* PDF Preview Styles */
.pdf-preview-container {
    position: fixed;
    top: 0;
    left: 0;
    width: 100%;
    height: 100%;
    background: rgba(0, 0, 0, 0.9);
    z-index: 1000;
    display: none;
}

.pdf-preview-content {
    position: relative;
    width: 90%;
    height: 90%;
    margin: 5% auto;
    background: white;
    border-radius: 8px;
    overflow: hidden;
}

.pdf-preview-header {
    background: #f8fafc;
    padding: 1rem;
    border-bottom: 1px solid #e2e8f0;
    display: flex;
    justify-content: space-between;
    align-items: center;
}

.pdf-preview-body {
    height: calc(100% - 60px);
}

.pdf-preview-iframe {
    width: 100%;
    height: 100%;
    border: none;
}

/* Responsive */
@media (max-width: 768px) {
    .info-grid {
        grid-template-columns: 1fr;
        gap: 1rem;
        padding: 1rem;
    }
    
    .timeline-item {
        gap: 0.75rem;
    }
    
    .timeline-meta {
        flex-direction: column;
        align-items: flex-start;
        gap: 0.25rem;
    }
    
    .files-grid {
        grid-template-columns: 1fr;
        padding: 1rem;
    }
    
    .action-buttons {
        flex-direction: column;
    }
    
    .btn-action {
        width: 100%;
    }
    
    .file-actions {
        flex-direction: column;
    }
    
    .file-action-btn {
        width: 100%;
        justify-content: center;
    }
}
</style>

<div class="container-fluid">
    <!-- Page Header -->
    <div class="page-header">
        <h1>
            <i class="fas fa-eye me-3"></i>รายละเอียดการจองคิว
        </h1>
    </div>

    <?php if (empty($queue_data)): ?>
        <!-- Empty State -->
        <div class="section-card">
            <div class="empty-state">
                <i class="fas fa-search"></i>
                <h5>ไม่พบข้อมูลคิว</h5>
                <p>หมายเลขคิวที่ระบุไม่พบในระบบ</p>
                <a href="<?= site_url('Queue/queue_report') ?>" class="btn btn-primary">
                    <i class="fas fa-arrow-left me-2"></i>กลับไปรายงานคิว
                </a>
            </div>
        </div>
    <?php else: ?>
        <!-- Queue Information -->
        <div class="queue-detail-container">
            <div class="queue-detail-header">
                <div class="d-flex align-items-center flex-wrap">
                    <span class="queue-id-badge">
                        <i class="fas fa-ticket-alt me-2"></i>#<?= htmlspecialchars($queue_data['queue_id']) ?>
                    </span>
                    <span class="queue-status-current <?= get_queue_status_class($queue_data['queue_status']) ?>">
                        <i class="<?= get_queue_status_icon($queue_data['queue_status']) ?>"></i>
                        <?= htmlspecialchars($queue_data['queue_status']) ?>
                    </span>
                </div>
            </div>
            
            <div class="info-grid">
                <div class="info-item">
                    <div class="info-label">หัวข้อเรื่อง</div>
                    <div class="info-value large"><?= htmlspecialchars($queue_data['queue_topic']) ?></div>
                </div>
                
                <div class="info-item">
                    <div class="info-label">ผู้จองคิว</div>
                    <div class="info-value"><?= htmlspecialchars($queue_data['queue_by']) ?></div>
                </div>
                
                <div class="info-item">
                    <div class="info-label">เบอร์โทรศัพท์</div>
                    <div class="info-value"><?= htmlspecialchars($queue_data['queue_phone']) ?></div>
                </div>
                
                <div class="info-item">
                    <div class="info-label">เลขบัตรประชาชน</div>
                    <div class="info-value">
                        <span class="censored-number"><?= censor_id_number($queue_data['queue_number']) ?></span>
                    </div>
                </div>
                
                <div class="info-item">
                    <div class="info-label">อีเมล</div>
                    <div class="info-value"><?= !empty($queue_data['queue_email']) ? htmlspecialchars($queue_data['queue_email']) : 'ไม่ระบุ' ?></div>
                </div>
                
                <div class="info-item">
                    <div class="info-label">ที่อยู่</div>
                    <div class="info-value"><?= format_address($queue_data) ?></div>
                </div>
                
                <div class="info-item">
                    <div class="info-label">วันที่นัดหมาย</div>
                    <div class="info-value">
                        <?php if (!empty($queue_data['queue_date'])): ?>
                            <?= isset($queue_data['date_thai']) ? $queue_data['date_thai'] : date('d/m/Y H:i', strtotime($queue_data['queue_date'])) ?>
                        <?php else: ?>
                            ไม่ระบุ
                        <?php endif; ?>
                    </div>
                </div>
                
                <div class="info-item">
                    <div class="info-label">ช่วงเวลา</div>
                    <div class="info-value"><?= !empty($queue_data['queue_time_slot']) ? htmlspecialchars($queue_data['queue_time_slot']) : 'ไม่ระบุ' ?></div>
                </div>
                
                <div class="info-item">
                    <div class="info-label">วันที่สร้างคิว</div>
                    <div class="info-value">
                        <?= isset($queue_data['created_thai']) ? $queue_data['created_thai'] : date('d/m/Y H:i', strtotime($queue_data['queue_datesave'])) ?>
                    </div>
                </div>
                
                <div class="info-item">
                    <div class="info-label">ประเภทผู้ใช้</div>
                    <div class="info-value">
                        <?php
                        $user_type_labels = [
                            'guest' => 'ผู้ใช้ทั่วไป',
                            'public' => 'สมาชิกในระบบ',
                            'staff' => 'เจ้าหน้าที่'
                        ];
                        echo $user_type_labels[$queue_data['queue_user_type']] ?? 'ไม่ทราบ';
                        ?>
                    </div>
                </div>
            </div>
            
            <?php if (!empty($queue_data['queue_detail'])): ?>
                <div style="padding: 0 1.5rem 1.5rem;">
                    <div class="info-item">
                        <div class="info-label">รายละเอียด</div>
                        <div class="info-value" style="background: #f8fafc; padding: 1rem; border-radius: 8px; border-left: 4px solid #42a5f5;">
                            <?= nl2br(htmlspecialchars($queue_data['queue_detail'])) ?>
                        </div>
                    </div>
                </div>
            <?php endif; ?>
        </div>

        <!-- Queue History Timeline -->
        <div class="section-card">
            <div class="section-header">
                <i class="section-icon fas fa-history"></i>
                <h3 class="section-title">ประวัติการดำเนินงาน</h3>
            </div>
            
            <div class="timeline">
                <?php if (!empty($queue_details)): ?>
                    <?php foreach ($queue_details as $detail): ?>
                        <div class="timeline-item">
                            <div class="timeline-icon <?= get_queue_status_class($detail['queue_detail_status']) ?>">
                                <i class="<?= get_queue_status_icon($detail['queue_detail_status']) ?>"></i>
                            </div>
                            <div class="timeline-content">
                                <div class="timeline-status"><?= htmlspecialchars($detail['queue_detail_status']) ?></div>
                                <div class="timeline-meta">
                                    <span><i class="fas fa-user me-1"></i><?= htmlspecialchars($detail['queue_detail_by']) ?></span>
                                    <span><i class="fas fa-clock me-1"></i><?= isset($detail['date_thai']) ? $detail['date_thai'] : date('d/m/Y H:i', strtotime($detail['queue_detail_datesave'])) ?></span>
                                </div>
                                <?php if (!empty($detail['queue_detail_com'])): ?>
                                    <div class="timeline-comment">
                                        <?= nl2br(htmlspecialchars($detail['queue_detail_com'])) ?>
                                    </div>
                                <?php endif; ?>
                            </div>
                        </div>
                    <?php endforeach; ?>
                <?php else: ?>
                    <div class="empty-state">
                        <i class="fas fa-history"></i>
                        <p>ไม่พบประวัติการดำเนินงาน</p>
                    </div>
                <?php endif; ?>
            </div>
        </div>

        <!-- Attached Files -->
        <div class="section-card">
            <div class="section-header">
                <i class="section-icon fas fa-paperclip"></i>
                <h3 class="section-title">ไฟล์แนบ</h3>
                <span class="badge bg-primary ms-auto"><?= count($queue_files) ?> ไฟล์</span>
            </div>
            
            <?php if (!empty($queue_files)): ?>
                <div class="files-grid">
                    <?php foreach ($queue_files as $file): ?>
                        <?php 
                        $file_info = get_file_type_info($file->queue_file_name, $file->queue_file_type);
                        ?>
                        <div class="file-card <?= $file_info['type'] ?>-file">
                            
                            <?php if ($file_info['type'] == 'image'): ?>
                                <!-- Image Thumbnail -->
                                <img class="file-thumbnail" 
                                     src="<?= site_url('Queue/view_queue_image/' . urlencode($file->queue_file_name)) ?>" 
                                     alt="<?= htmlspecialchars($file->queue_file_original_name) ?>"
                                     onclick="previewImage('<?= htmlspecialchars($file->queue_file_name) ?>', '<?= htmlspecialchars($file->queue_file_original_name) ?>')"
                                     onerror="this.style.display='none'; this.nextElementSibling.style.display='flex';">
                                
                                <!-- Fallback if image fails to load -->
                                <div class="file-header" style="display: none;">
                                    <div class="file-icon" style="background: <?= $file_info['color'] ?>;">
                                        <i class="<?= $file_info['icon'] ?>"></i>
                                    </div>
                                    <div class="file-info">
                                        <div class="file-name" title="<?= htmlspecialchars($file->queue_file_original_name) ?>">
                                            <?= htmlspecialchars($file->queue_file_original_name) ?>
                                        </div>
                                        <div class="file-meta">
                                            <?= $file->file_size_formatted ?> • 
                                            <?= date('d/m/Y H:i', strtotime($file->queue_file_uploaded_at)) ?>
                                        </div>
                                    </div>
                                </div>
                            <?php else: ?>
                                <!-- Non-image files -->
                                <div class="file-header">
                                    <div class="file-icon" style="background: <?= $file_info['color'] ?>;">
                                        <i class="<?= $file_info['icon'] ?>"></i>
                                    </div>
                                    <div class="file-info">
                                        <div class="file-name" title="<?= htmlspecialchars($file->queue_file_original_name) ?>">
                                            <?= htmlspecialchars($file->queue_file_original_name) ?>
                                        </div>
                                        <div class="file-meta">
                                            <?= $file->file_size_formatted ?> • 
                                            <?= date('d/m/Y H:i', strtotime($file->queue_file_uploaded_at)) ?>
                                        </div>
                                    </div>
                                </div>
                            <?php endif; ?>
                            
                            <!-- File Info for Images -->
                            <?php if ($file_info['type'] == 'image'): ?>
                                <div class="file-info-overlay">
                                    <div class="file-name" title="<?= htmlspecialchars($file->queue_file_original_name) ?>">
                                        <?= htmlspecialchars($file->queue_file_original_name) ?>
                                    </div>
                                    <div class="file-meta">
                                        <?= $file->file_size_formatted ?> • 
                                        <?= date('d/m/Y H:i', strtotime($file->queue_file_uploaded_at)) ?>
                                    </div>
                                </div>
                            <?php endif; ?>
                            
                            <div class="file-actions">
                                <?php if ($file_info['can_preview'] && $file_info['type'] == 'pdf'): ?>
                                    <button class="file-action-btn btn-preview" 
                                            onclick="previewPdf('<?= htmlspecialchars($file->queue_file_name) ?>', '<?= htmlspecialchars($file->queue_file_original_name) ?>')">
                                        <i class="fas fa-eye"></i>
                                        ดู PDF
                                    </button>
                                <?php endif; ?>
                                
                                <button class="file-action-btn btn-download" 
                                        onclick="downloadFile('<?= htmlspecialchars($file->queue_file_name) ?>')">
                                    <i class="fas fa-download"></i>
                                    ดาวน์โหลด
                                </button>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>
            <?php else: ?>
                <div class="empty-state">
                    <i class="fas fa-paperclip"></i>
                    <p>ไม่มีไฟล์แนบ</p>
                </div>
            <?php endif; ?>
        </div>

        <!-- Action Buttons -->
        <div class="action-buttons">
            <a href="<?= site_url('Queue/queue_report') ?>" class="btn-action btn-back">
                <i class="fas fa-arrow-left"></i>
                กลับ
            </a>
            
            <a href="<?= site_url('Queue/export_queue_pdf/' . $queue_data['queue_id']) ?>" 
               class="btn-action btn-print" target="_blank">
                <i class="fas fa-file-pdf"></i>
                พิมพ์
            </a>
            
            <a href="<?= site_url('Queue/export_queue_excel/' . $queue_data['queue_id']) ?>" 
               class="btn-action btn-export">
                <i class="fas fa-file-excel"></i>
                ส่งออก Excel
            </a>
            
            
        </div>
    <?php endif; ?>
</div>

<!-- Image Preview Modal -->
<div id="imagePreviewModal" class="image-preview-modal">
    <span class="close-preview" onclick="closeImagePreview()">&times;</span>
    <div class="image-preview-content">
        <img id="previewImage" src="" alt="">
        <div class="preview-info">
            <h5 id="previewFileName"></h5>
        </div>
    </div>
</div>

<!-- PDF Preview Modal -->
<div id="pdfPreviewModal" class="pdf-preview-container">
    <div class="pdf-preview-content">
        <div class="pdf-preview-header">
            <h5 id="pdfFileName"></h5>
            <button onclick="closePdfPreview()" class="btn btn-secondary">
                <i class="fas fa-times"></i> ปิด
            </button>
        </div>
        <div class="pdf-preview-body">
            <iframe id="pdfPreviewFrame" class="pdf-preview-iframe" src=""></iframe>
        </div>
    </div>
</div>

<!-- SweetAlert2 -->
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

<script>
/**
 * Download file function
 */
function downloadFile(fileName) {
    if (!fileName) {
        Swal.fire({
            icon: 'error',
            title: 'เกิดข้อผิดพลาด',
            text: 'ไม่พบชื่อไฟล์'
        });
        return;
    }
    
    // Show loading
    Swal.fire({
        title: 'กำลังดาวน์โหลด...',
        text: 'กรุณารอสักครู่',
        allowOutsideClick: false,
        allowEscapeKey: false,
        showConfirmButton: false,
        didOpen: () => {
            Swal.showLoading();
        }
    });
    
    // Create download link
    const downloadUrl = '<?= site_url("Queue/download_queue_file/") ?>' + encodeURIComponent(fileName);
    
    // Create temporary link and trigger download
    const link = document.createElement('a');
    link.href = downloadUrl;
    link.download = '';
    document.body.appendChild(link);
    link.click();
    document.body.removeChild(link);
    
    // Close loading after a short delay
    setTimeout(() => {
        Swal.close();
    }, 1000);
}

/**
 * Preview image function
 */
function previewImage(fileName, originalName) {
    if (!fileName) {
        Swal.fire({
            icon: 'error',
            title: 'เกิดข้อผิดพลาด',
            text: 'ไม่พบชื่อไฟล์'
        });
        return;
    }
    
    const imageUrl = '<?= site_url("Queue/view_queue_image/") ?>' + encodeURIComponent(fileName);
    const modal = document.getElementById('imagePreviewModal');
    const img = document.getElementById('previewImage');
    const fileNameElement = document.getElementById('previewFileName');
    
    img.src = imageUrl;
    fileNameElement.textContent = originalName || fileName;
    modal.style.display = 'block';
    
    // Close on click outside
    modal.onclick = function(event) {
        if (event.target === modal) {
            closeImagePreview();
        }
    }
}

/**
 * Close image preview
 */
function closeImagePreview() {
    const modal = document.getElementById('imagePreviewModal');
    modal.style.display = 'none';
}

/**
 * Preview PDF function
 */
function previewPdf(fileName, originalName) {
    if (!fileName) {
        Swal.fire({
            icon: 'error',
            title: 'เกิดข้อผิดพลาด',
            text: 'ไม่พบชื่อไฟล์'
        });
        return;
    }
    
    const pdfUrl = '<?= site_url("Queue/view_queue_pdf/") ?>' + encodeURIComponent(fileName);
    const modal = document.getElementById('pdfPreviewModal');
    const iframe = document.getElementById('pdfPreviewFrame');
    const fileNameElement = document.getElementById('pdfFileName');
    
    iframe.src = pdfUrl;
    fileNameElement.textContent = originalName || fileName;
    modal.style.display = 'block';
}

/**
 * Close PDF preview
 */
function closePdfPreview() {
    const modal = document.getElementById('pdfPreviewModal');
    const iframe = document.getElementById('pdfPreviewFrame');
    
    modal.style.display = 'none';
    iframe.src = '';
}

/**
 * Copy queue ID to clipboard
 */
function copyQueueId() {
    const queueId = '<?= $queue_data['queue_id'] ?? '' ?>';
    
    if (navigator.clipboard) {
        navigator.clipboard.writeText(queueId).then(() => {
            Swal.fire({
                icon: 'success',
                title: 'คัดลอกสำเร็จ',
                text: 'หมายเลขคิว ' + queueId + ' ถูกคัดลอกแล้ว',
                timer: 2000,
                showConfirmButton: false
            });
        });
    } else {
        // Fallback for older browsers
        const textArea = document.createElement('textarea');
        textArea.value = queueId;
        document.body.appendChild(textArea);
        textArea.focus();
        textArea.select();
        try {
            document.execCommand('copy');
            Swal.fire({
                icon: 'success',
                title: 'คัดลอกสำเร็จ',
                text: 'หมายเลขคิว ' + queueId + ' ถูกคัดลอกแล้ว',
                timer: 2000,
                showConfirmButton: false
            });
        } catch (err) {
            Swal.fire({
                icon: 'error',
                title: 'เกิดข้อผิดพลาด',
                text: 'ไม่สามารถคัดลอกได้'
            });
        }
        document.body.removeChild(textArea);
    }
}

/**
 * Print specific section
 */
function printQueue() {
    window.print();
}

// Add click event to queue ID for copying
document.addEventListener('DOMContentLoaded', function() {
    const queueIdBadge = document.querySelector('.queue-id-badge');
    if (queueIdBadge) {
        queueIdBadge.style.cursor = 'pointer';
        queueIdBadge.title = 'คลิกเพื่อคัดลอกหมายเลขคิว';
        queueIdBadge.addEventListener('click', copyQueueId);
    }
    
    // Add hover effect to file cards
    const fileCards = document.querySelectorAll('.file-card');
    fileCards.forEach(card => {
        // Only add hover effect to non-image cards or when hovering over action buttons
        const isImageCard = card.classList.contains('image-file');
        
        if (!isImageCard) {
            card.addEventListener('mouseenter', function() {
                if (!this.style.transform.includes('translateY')) {
                    this.style.transform = 'translateY(-2px)';
                }
            });
            
            card.addEventListener('mouseleave', function() {
                this.style.transform = 'translateY(0)';
            });
        } else {
            // For image cards, only apply hover to action buttons area
            const actionsArea = card.querySelector('.file-actions');
            if (actionsArea) {
                actionsArea.addEventListener('mouseenter', function() {
                    card.style.transform = 'translateY(-2px)';
                });
                
                actionsArea.addEventListener('mouseleave', function() {
                    card.style.transform = 'translateY(0)';
                });
            }
        }
    });
    
    // Close modals with Escape key
    document.addEventListener('keydown', function(event) {
        if (event.key === 'Escape') {
            closeImagePreview();
            closePdfPreview();
        }
    });
});

// Print styles
const printStyles = `
<style media="print">
    .btn-action, .action-buttons, .file-actions {
        display: none !important;
    }
    
    .page-header {
        background: none !important;
        color: #000 !important;
        box-shadow: none !important;
    }
    
    .queue-detail-container,
    .section-card {
        box-shadow: none !important;
        border: 1px solid #ddd !important;
        page-break-inside: avoid;
    }
    
    .timeline-item {
        page-break-inside: avoid;
        margin-bottom: 1rem;
    }
    
    .image-preview-modal,
    .pdf-preview-container {
        display: none !important;
    }
    
    .file-thumbnail {
        max-height: 150px;
        page-break-inside: avoid;
    }
    
    .files-grid {
        grid-template-columns: repeat(2, 1fr) !important;
    }
    
    @page {
        margin: 1cm;
        size: A4;
    }
</style>
`;

document.head.insertAdjacentHTML('beforeend', printStyles);
</script>