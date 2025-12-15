<?php
// แทนที่เนื้อหาในไฟล์ application/views/public_user/queue_detail.php

// เพิ่มฟังก์ชัน helper ที่ด้านบนของไฟล์
function get_queue_status_symbol($status) {
    $symbol_map = [
        'รอยืนยันการจอง' => '⏳',
        'ยืนยันการจอง' => '✅',
        'คิวได้รับการยืนยัน' => '✅',
        'รับเรื่องแล้ว' => '📥',
        'กำลังดำเนินการ' => '⚙️',
        'รอดำเนินการ' => '⏳',
        'เสร็จสิ้น' => '🎉',
        'ยกเลิก' => '❌',
        'คิวได้ถูกยกเลิก' => '❌'
    ];
    return $symbol_map[$status] ?? '●';
}

// ฟังก์ชันแปลงวันที่เป็นรูปแบบไทย
function formatThaiDate($date_string, $format = 'full') {
    if (empty($date_string)) return '';
    
    try {
        $date = new DateTime($date_string);
        
        // เดือนไทย
        $thai_months = [
            1 => 'มกราคม', 2 => 'กุมภาพันธ์', 3 => 'มีนาคม', 4 => 'เมษายน',
            5 => 'พฤษภาคม', 6 => 'มิถุนายน', 7 => 'กรกฎาคม', 8 => 'สิงหาคม',
            9 => 'กันยายน', 10 => 'ตุลาคม', 11 => 'พฤศจิกายน', 12 => 'ธันวาคม'
        ];
        
        $day = $date->format('j');
        $month = $thai_months[(int)$date->format('n')];
        $year = $date->format('Y') + 543; // แปลงเป็นปีไทย
        $time = $date->format('H:i');
        
        if ($format === 'full') {
            return "{$day} {$month} {$year} เวลา {$time} น.";
        } elseif ($format === 'short') {
            return "{$day} {$month} {$year}";
        } elseif ($format === 'date_only') {
            return "{$day} {$month} {$year}";
        }
        
        return "{$day} {$month} {$year} เวลา {$time} น.";
    } catch (Exception $e) {
        return $date_string; // fallback ถ้าแปลงไม่ได้
    }
}

defined('BASEPATH') or exit('No direct script access allowed');

// ตรวจสอบ login
if (!$this->session->userdata('mp_id') && !$this->session->userdata('m_id')) {
    redirect('User');
    return;
}
?>

<div class="queue-bg-pages">
    <div class="queue-container-pages-news">
        
        <!-- Breadcrumb -->
        <nav aria-label="breadcrumb" class="mb-4">
            <ol class="queue-breadcrumb-custom">
                <li><a href="<?php echo site_url('Queue/my_queue_status'); ?>"><i class="fas fa-list me-1"></i>สถานะการจองคิวของฉัน</a></li>
                <li class="active"><i class="fas fa-eye me-1"></i>รายละเอียด</li>
            </ol>
        </nav>

        <!-- Queue Header -->
        <div class="queue-modern-card queue-header">
            <?php 
            $latest_status = !empty($queue_details) ? end($queue_details)['queue_detail_status'] : $queue_data['queue_status'];
            $status_color = $queue_data['status_color'];
            $status_icon = $queue_data['status_icon'];
            $status_display = $queue_data['status_display'];
            ?>
            
            <!-- Status Badge -->
            <div class="queue-status-badge-wrapper">
                <span class="queue-status-badge" style="background-color: <?php echo $status_color; ?>;">
                    <i class="<?php echo $status_icon; ?> me-1"></i><?php echo htmlspecialchars($status_display); ?>
                </span>
            </div>
            
            <div class="queue-header-content">
                <div class="d-flex">
                    <div class="queue-status-icon-wrapper">
                        <div class="queue-status-icon" style="color: <?php echo $status_color; ?>;">
                            <i class="<?php echo $status_icon; ?>"></i>
                        </div>
                    </div>
                    <div class="queue-info flex-grow-1">
                        <div class="d-flex justify-content-between align-items-start">
                            <div>
                                <h5 class="queue-number">
                                    หมายเลขการจองคิว: <span class="queue-number-highlight"><?php echo htmlspecialchars($queue_data['queue_id']); ?></span>
                                </h5>
                                <h4 class="queue-topic">
                                    <?php echo htmlspecialchars($queue_data['queue_topic']); ?>
                                </h4>
                                <div class="queue-badges">
                                    <span class="queue-badge-date">
                                        <i class="fas fa-calendar-plus me-1"></i>จองเมื่อ: <?php echo formatThaiDate($queue_data['created_date'] ?? $queue_data['queue_datesave'] ?? ''); ?>
                                    </span>
                                    <?php if (!empty($queue_data['queue_date'])): ?>
                                    <span class="queue-badge-appointment">
                                        <i class="fas fa-calendar-check me-1"></i>นัดหมาย: <?php echo formatThaiDate($queue_data['queue_date']); ?>
                                    </span>
                                    <?php endif; ?>
                                    <?php if (!empty($queue_data['queue_time_slot'])): ?>
                                    <span class="queue-badge-time">
                                        <i class="fas fa-clock me-1"></i>ช่วงเวลา: <?php echo htmlspecialchars($queue_data['queue_time_slot']); ?>
                                    </span>
                                    <?php endif; ?>
                                </div>
                            </div>
                            
                        </div>
                    </div>
                </div>
                
                <!-- Details Section -->
                <div class="queue-details-section">
                    <div class="queue-details-header">
                        <h5><i class="fas fa-info-circle me-2"></i>รายละเอียดการจองคิว</h5>
                    </div>
                    
                    <div class="queue-details-card">
                         <!-- <div class="row">
                            <div class="col-md-6">
                                <div class="queue-detail-item">
                                    <h6><i class="fas fa-user me-2"></i>ผู้จอง</h6>
                                    <p><?php echo htmlspecialchars($queue_data['queue_by']); ?></p>
                                </div>
                                
                                <div class="queue-detail-item">
                                    <h6><i class="fas fa-phone me-2"></i>เบอร์โทรศัพท์</h6>
                                    <p><?php echo htmlspecialchars($queue_data['queue_phone']); ?></p>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <?php if (!empty($queue_data['queue_email'])): ?>
                                <div class="queue-detail-item">
                                    <h6><i class="fas fa-envelope me-2"></i>อีเมล</h6>
                                    <p><?php echo htmlspecialchars($queue_data['queue_email']); ?></p>
                                </div>
                                <?php endif; ?>
                                
                                <?php if (!empty($queue_data['queue_address']) || !empty($queue_data['guest_district'])): ?>
                                <div class="queue-detail-item">
                                    <h6><i class="fas fa-map-marker-alt me-2"></i>ที่อยู่</h6>
                                    <p>
                                        <?php 
                                        $address_parts = array_filter([
                                            $queue_data['queue_address'] ?? '',
                                            $queue_data['guest_district'] ?? '',
                                            $queue_data['guest_amphoe'] ?? '',
                                            $queue_data['guest_province'] ?? '',
                                            $queue_data['guest_zipcode'] ?? ''
                                        ]);
                                        echo htmlspecialchars(implode(' ', $address_parts) ?: 'ไม่ระบุ');
                                        ?>
                                    </p>
                                </div>
                                <?php endif; ?>
                            </div>
                        </div>  -->
                        
                        <div class="queue-detail-item full-width">
                            <h6><i class="fas fa-align-left me-2"></i>รายละเอียด</h6>
                            <div class="queue-detail-content">
                                <p><?php echo nl2br(htmlspecialchars($queue_data['queue_detail'])); ?></p>
                            </div>
                        </div>
                        
                        <!-- ไฟล์แนบ - แก้ไขให้ใช้ direct path -->
                        <?php if (!empty($queue_files)): ?>
                            <div class="queue-detail-item full-width">
                                <h6><i class="fas fa-paperclip me-2"></i>ไฟล์แนบ</h6>
                                <div class="queue-files-gallery">
                                    <div class="row g-3">
                                        <?php foreach ($queue_files as $index => $file): ?>
                                            <div class="col-lg-3 col-md-4 col-sm-6">
                                                <div class="queue-file-item">
                                                    <?php if ($file->is_image): ?>
                                                        <!-- รูปภาพ - ใช้ direct path แทน -->
                                                        <div class="queue-image-wrapper" 
                                                             onclick="openQueueImageModal('<?php echo base_url('docs/queue_files/' . $file->queue_file_name); ?>', '<?php echo htmlspecialchars($queue_data['queue_topic']); ?> - ไฟล์แนบที่ <?php echo $index + 1; ?>')">
                                                            <img src="<?php echo base_url('docs/queue_files/' . $file->queue_file_name); ?>" 
                                                                 alt="ไฟล์แนบ <?php echo $index + 1; ?>"
                                                                 class="queue-file-image"
                                                                 loading="lazy"
                                                                 onerror="this.closest('.queue-file-item').innerHTML='<div class=\'text-center p-3\'><i class=\'fas fa-exclamation-triangle text-warning\'></i><br><small>ไฟล์ไม่พบ</small></div>';">
                                                            <div class="queue-image-overlay">
                                                                <i class="fas fa-search-plus"></i>
                                                                <span>คลิกเพื่อขยาย</span>
                                                            </div>
                                                        </div>
                                                    <?php else: ?>
                                                        <!-- ไฟล์อื่นๆ -->
                                                        <div class="queue-file-wrapper" 
                                                             onclick="window.open('<?php echo site_url('Queue/download_queue_file/' . $file->queue_file_name); ?>', '_blank')">
                                                            <div class="queue-file-icon">
                                                                <i class="fas <?php echo $file->file_icon; ?>"></i>
                                                            </div>
                                                            <div class="queue-file-overlay">
                                                                <i class="fas fa-download"></i>
                                                                <span>คลิกเพื่อดาวน์โหลด</span>
                                                            </div>
                                                        </div>
                                                    <?php endif; ?>
                                                    <div class="queue-file-caption">
                                                        <small class="queue-file-name"><?php echo htmlspecialchars($file->queue_file_original_name); ?></small>
                                                        <small class="queue-file-size"><?php echo $file->file_size_formatted; ?></small>
                                                    </div>
                                                </div>
                                            </div>
                                        <?php endforeach; ?>
                                    </div>
                                </div>
                            </div>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        </div>

        <!-- Timeline Section -->
        <div class="queue-modern-card queue-timeline-card">
            <div class="queue-timeline-header">
                <h4><i class="fas fa-history me-2"></i>ประวัติการดำเนินงาน</h4>
            </div>
            
            <div class="queue-timeline">
                <?php if (!empty($queue_details)): ?>
                    <?php foreach (array_reverse($queue_details) as $index => $detail): 
                        $is_latest = $index === 0;
                    ?>
                        <div class="queue-timeline-item <?php echo $is_latest ? 'queue-timeline-latest' : ''; ?>">
                            <div class="queue-timeline-dot" style="background: <?php echo $detail['status_color']; ?>;" data-status="<?php echo htmlspecialchars($detail['queue_detail_status']); ?>">
                                <i class="<?php echo $detail['status_icon']; ?>" style="color: white !important;"></i>
                                <span class="queue-fallback-symbol" style="display: none;"><?php echo get_queue_status_symbol($detail['queue_detail_status']); ?></span>
                            </div>
                            
                            <?php if ($index < count($queue_details) - 1): ?>
                                <div class="queue-timeline-line"></div>
                            <?php endif; ?>
                            
                            <div class="queue-timeline-content">
                                <div class="queue-timeline-header-item">
                                    <h6 class="queue-timeline-status" style="color: <?php echo $detail['status_color']; ?>;">
                                        <?php echo htmlspecialchars($detail['status_display']); ?>
                                        <?php if ($is_latest): ?>
                                            <span class="queue-latest-badge">ล่าสุด</span>
                                        <?php endif; ?>
                                    </h6>
                                    <small class="queue-timeline-date">
                                        <i class="fas fa-calendar me-1"></i><?php echo formatThaiDate($detail['formatted_date'] ?? $detail['queue_detail_date'] ?? ''); ?>
                                    </small>
                                </div>
                                
                                <?php if (!empty($detail['queue_detail_com'])): ?>
                                    <div class="queue-timeline-comment">
                                        <i class="fas fa-comment me-2"></i>
                                        <?php echo nl2br(htmlspecialchars($detail['queue_detail_com'])); ?>
                                    </div>
                                <?php endif; ?>
                                
                                <?php if (!empty($detail['queue_detail_by'])): ?>
                                    <small class="queue-timeline-by">
                                        <i class="fas fa-user me-1"></i>โดย: <?php echo htmlspecialchars($detail['queue_detail_by']); ?>
                                    </small>
                                <?php endif; ?>
                            </div>
                        </div>
                    <?php endforeach; ?>
                <?php else: ?>
                    <div class="queue-empty-timeline">
                        <div class="queue-empty-icon">
                            <i class="fas fa-clock"></i>
                        </div>
                        <h6>ยังไม่มีการอัพเดทสถานะ</h6>
                        <p>การจองคิวของคุณอยู่ในขั้นตอนการพิจารณา</p>
                    </div>
                <?php endif; ?>
            </div>
        </div>

        <!-- Action Buttons -->
        <div class="queue-action-buttons">
            <a href="<?php echo site_url('Queue/my_queue_status'); ?>" class="queue-btn-back">
                <i class="fas fa-arrow-left me-2"></i>กลับสู่รายการ
            </a>
            <button onclick="window.print()" class="queue-btn-print">
                <i class="fas fa-print me-2"></i>พิมพ์
            </button>
        </div>
    </div>
</div>

<!-- Image Modal -->
<div class="modal fade" id="queueImageModal" tabindex="-1">
    <div class="modal-dialog modal-xl modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="queueImageModalLabel">รูปภาพ</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body text-center">
                <img id="queueModalImage" src="" alt="รูปภาพ" class="img-fluid">
            </div>
        </div>
    </div>
</div>

<!-- เพิ่ม CSS และ JavaScript ในไฟล์เดียวกัน -->
<style>
/* ใช้โทนสีม่วงแทนส้ม */
@import url('https://fonts.googleapis.com/css2?family=Kanit:wght@300;400;500;600;700&display=swap');
@import url('https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css');

:root {
    --queue-primary-purple: #9c6bdb;
    --queue-secondary-purple: #8a5ac7;
    --queue-light-purple: #f3efff;
    --queue-very-light-purple: #faf8ff;
    --queue-success-color: #00B73E;
    --queue-warning-color: #FFC700;
    --queue-danger-color: #FF0202;
    --queue-info-color: #17a2b8;
    --queue-text-dark: #2c3e50;
    --queue-text-muted: #6c757d;
    --queue-shadow-light: 0 4px 20px rgba(156, 107, 219, 0.1);
    --queue-shadow-medium: 0 8px 30px rgba(156, 107, 219, 0.15);
    --queue-gradient-primary: linear-gradient(135deg, #9c6bdb 0%, #8a5ac7 100%);
    --queue-gradient-light: linear-gradient(135deg, #faf8ff 0%, #f3efff 100%);
}

* {
    font-family: 'Kanit', sans-serif;
}

.queue-bg-pages {
    background: #ffffff;
    background-image: 
        radial-gradient(circle at 20% 30%, rgba(156, 107, 219, 0.03) 0%, transparent 50%),
        radial-gradient(circle at 80% 70%, rgba(138, 90, 199, 0.03) 0%, transparent 50%);
    min-height: 100vh;
    padding: 2rem 0;
}

.queue-container-pages-news {
    max-width: 1000px;
    margin: 0 auto;
    padding: 0 1rem;
}

/* Breadcrumb */
.queue-breadcrumb-custom {
    background: none;
    padding: 0;
    margin-bottom: 2rem;
    display: flex;
    align-items: center;
    gap: 1rem;
    font-size: 0.95rem;
}

.queue-breadcrumb-custom li {
    display: flex;
    align-items: center;
}

.queue-breadcrumb-custom li:not(:last-child)::after {
    content: '›';
    margin-left: 1rem;
    color: var(--queue-text-muted);
    font-size: 1.2rem;
}

.queue-breadcrumb-custom a {
    color: var(--queue-primary-purple);
    text-decoration: none;
    font-weight: 500;
}

.queue-breadcrumb-custom a:hover {
    text-decoration: underline;
}

.queue-breadcrumb-custom .active {
    color: var(--queue-text-muted);
    font-weight: 500;
}

/* Card Styles */
.queue-modern-card {
    background: rgba(255, 255, 255, 0.95);
    border-radius: 24px;
    box-shadow: var(--queue-shadow-light);
    margin-bottom: 2rem;
    overflow: hidden;
    position: relative;
}

.queue-modern-card::before {
    content: '';
    position: absolute;
    top: 0;
    left: 0;
    right: 0;
    height: 4px;
    background: var(--queue-gradient-primary);
    z-index: 1;
}

/* Queue Header */
.queue-header {
    padding: 2.5rem;
    position: relative;
}

.queue-status-badge-wrapper {
    position: absolute;
    top: 1.5rem;
    right: 1.5rem;
    z-index: 10;
}

.queue-status-badge {
    padding: 0.6rem 1.2rem;
    border-radius: 25px;
    font-weight: 600;
    font-size: 0.9rem;
    color: white;
    box-shadow: 0 4px 15px rgba(0, 0, 0, 0.2);
}

.queue-header-content {
    margin-right: 140px;
}

.queue-status-icon-wrapper {
    margin-right: 2rem;
    margin-top: 0.5rem;
}

.queue-status-icon {
    width: 80px;
    height: 80px;
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    background: rgba(156, 107, 219, 0.1);
    font-size: 2rem;
    box-shadow: 0 8px 25px rgba(156, 107, 219, 0.2);
}

.queue-number {
    color: var(--queue-text-muted);
    font-weight: 500;
    margin-bottom: 0.5rem;
}

.queue-number-highlight {
    color: var(--queue-primary-purple);
    font-weight: 700;
}

.queue-topic {
    color: var(--queue-text-dark);
    font-weight: 700;
    margin-bottom: 1rem;
    font-size: 1.5rem;
}

.queue-badges {
    margin-bottom: 1rem;
}

.queue-badge-date, .queue-badge-appointment, .queue-badge-time {
    display: inline-block;
    padding: 0.4rem 0.8rem;
    border-radius: 12px;
    font-size: 0.85rem;
    margin-right: 0.5rem;
    margin-bottom: 0.5rem;
    font-weight: 500;
}

.queue-badge-date {
    background: linear-gradient(135deg, rgba(156, 107, 219, 0.15) 0%, rgba(156, 107, 219, 0.1) 100%);
    color: var(--queue-primary-purple);
    border: 1px solid rgba(156, 107, 219, 0.3);
}

.queue-badge-appointment {
    background: linear-gradient(135deg, rgba(23, 162, 184, 0.15) 0%, rgba(23, 162, 184, 0.1) 100%);
    color: var(--queue-info-color);
    border: 1px solid rgba(23, 162, 184, 0.3);
}

.queue-badge-time {
    background: linear-gradient(135deg, rgba(0, 183, 62, 0.15) 0%, rgba(0, 183, 62, 0.1) 100%);
    color: var(--queue-success-color);
    border: 1px solid rgba(0, 183, 62, 0.3);
}

.queue-copy-btn {
    background: rgba(156, 107, 219, 0.1);
    border: 1px solid rgba(156, 107, 219, 0.3);
    color: var(--queue-primary-purple);
    border-radius: 12px;
    padding: 0.5rem 1rem;
    font-size: 0.9rem;
    transition: all 0.3s ease;
    cursor: pointer;
}

.queue-copy-btn:hover {
    background: var(--queue-gradient-primary);
    border-color: var(--queue-primary-purple);
    color: white;
    transform: scale(1.05);
}

/* Details Section */
.queue-details-section {
    margin-top: 2rem;
    padding-top: 2rem;
    border-top: 1px solid rgba(156, 107, 219, 0.1);
}

.queue-details-header h5 {
    color: var(--queue-primary-purple);
    font-weight: 600;
    margin-bottom: 1.5rem;
}

.queue-details-card {
    border-radius: 16px;
    padding: 3rem;
    border-left: 4px solid var(--queue-primary-purple);
    width: calc(100% + 13rem);
    max-width: none;
    margin: 0 -2rem;
    position: relative;
    left: 0;
    box-sizing: border-box;
}

.queue-detail-item {
    margin-bottom: 1.5rem;
}

.queue-detail-item h6 {
    color: var(--queue-primary-purple);
    font-weight: 600;
    margin-bottom: 0.5rem;
}

.queue-detail-item p {
    margin-bottom: 0;
    padding: 0.5rem 1rem;
    background: rgba(156, 107, 219, 0.05);
    border-radius: 8px;
    border-left: 3px solid var(--queue-primary-purple);
    color: var(--queue-text-dark);
}

.queue-detail-content {
    padding: 1rem;
    background: rgba(156, 107, 219, 0.05);
    border-radius: 12px;
    border-left: 4px solid var(--queue-primary-purple);
}

.queue-detail-content p {
    background: none;
    border: none;
    padding: 0;
    margin: 0;
}

/* Files Gallery */
.queue-files-gallery {
    margin-top: 1rem;
}

.queue-file-item {
    position: relative;
    overflow: hidden;
    border-radius: 12px;
    background: white;
    box-shadow: 0 4px 15px rgba(156, 107, 219, 0.1);
    transition: all 0.3s ease;
}

.queue-file-item:hover {
    transform: translateY(-5px);
    box-shadow: 0 8px 25px rgba(156, 107, 219, 0.2);
}

.queue-image-wrapper, .queue-file-wrapper {
    position: relative;
    overflow: hidden;
    border-radius: 12px 12px 0 0;
    cursor: pointer;
    aspect-ratio: 16/12;
    background: #f8f9fa;
}

.queue-file-image {
    width: 100%;
    height: 100%;
    object-fit: cover;
    transition: all 0.3s ease;
}

.queue-file-icon {
    width: 100%;
    height: 100%;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 3rem;
    color: var(--queue-primary-purple);
    background: var(--queue-gradient-light);
}

.queue-image-overlay, .queue-file-overlay {
    position: absolute;
    top: 0;
    left: 0;
    right: 0;
    bottom: 0;
    background: var(--queue-gradient-primary);
    display: flex;
    flex-direction: column;
    align-items: center;
    justify-content: center;
    opacity: 0;
    transition: all 0.3s ease;
    color: white;
    font-weight: 600;
}

.queue-image-overlay i, .queue-file-overlay i {
    font-size: 2rem;
    margin-bottom: 0.5rem;
}

.queue-image-wrapper:hover .queue-image-overlay,
.queue-file-wrapper:hover .queue-file-overlay {
    opacity: 0.9;
}

.queue-image-wrapper:hover .queue-file-image {
    transform: scale(1.1);
}

.queue-file-caption {
    padding: 0.8rem;
    text-align: center;
    background: var(--queue-gradient-light);
    display: flex;
    flex-direction: column;
    gap: 0.2rem;
}

.queue-file-name {
    color: var(--queue-primary-purple);
    font-weight: 600;
    display: block;
}

.queue-file-size {
    color: var(--queue-text-muted);
    font-size: 0.75rem;
}

/* Timeline */
.queue-timeline-card {
    padding: 2.5rem;
}

.queue-timeline-header {
    margin-bottom: 2rem;
    padding-bottom: 1rem;
    border-bottom: 2px solid rgba(156, 107, 219, 0.1);
}

.queue-timeline-header h4 {
    color: var(--queue-text-dark);
    font-weight: 600;
    margin-bottom: 0;
}

.queue-timeline {
    position: relative;
    padding-left: 1rem;
}

.queue-timeline-item {
    position: relative;
    padding-left: 4.5rem;
    margin-bottom: 2.5rem;
}

.queue-timeline-item:last-child {
    margin-bottom: 0;
}

.queue-timeline-dot {
    position: absolute;
    left: 0;
    top: 0.5rem;
    width: 3.5rem;
    height: 3.5rem;
    border-radius: 50%;
    display: flex !important;
    align-items: center;
    justify-content: center;
    color: white !important;
    font-size: 1.3rem;
    font-weight: 700;
    box-shadow: 0 0 0 5px rgba(255, 255, 255, 1), 0 6px 25px rgba(0, 0, 0, 0.25);
    z-index: 10;
    border: 3px solid white;
    overflow: hidden;
}

.queue-timeline-dot i {
    display: block !important;
    color: white !important;
    font-size: 1.3rem !important;
    font-weight: 900 !important;
    line-height: 1 !important;
    text-shadow: 0 2px 4px rgba(0, 0, 0, 0.4);
    z-index: 2;
    position: relative;
}

.queue-fallback-symbol {
    font-size: 1.5rem;
    color: white;
    font-weight: bold;
    text-shadow: 0 2px 4px rgba(0, 0, 0, 0.4);
    position: absolute;
    z-index: 1;
}

.queue-timeline-latest .queue-timeline-dot {
    animation: queuePulse 2s infinite;
    box-shadow: 0 0 0 5px rgba(255, 255, 255, 1), 0 6px 30px rgba(0, 0, 0, 0.4);
    transform: scale(1.1);
}

.queue-timeline-line {
    position: absolute;
    left: 1.6rem;
    top: 4rem;
    bottom: -2.5rem;
    width: 4px;
    background: linear-gradient(to bottom, rgba(156, 107, 219, 0.4), rgba(156, 107, 219, 0.2), #e9ecef);
    border-radius: 2px;
}

.queue-timeline-item:last-child .queue-timeline-line {
    display: none;
}

.queue-timeline-content {
    background: rgba(255, 255, 255, 0.9);
    border-radius: 18px;
    padding: 1.8rem;
    border: 1px solid rgba(156, 107, 219, 0.15);
    box-shadow: 0 6px 25px rgba(156, 107, 219, 0.08);
    position: relative;
    margin-left: 0.5rem;
}

.queue-timeline-content::before {
    content: '';
    position: absolute;
    left: -10px;
    top: 1.2rem;
    width: 0;
    height: 0;
    border-top: 10px solid transparent;
    border-bottom: 10px solid transparent;
    border-right: 10px solid rgba(255, 255, 255, 0.9);
}

.queue-timeline-header-item {
    display: flex;
    justify-content: space-between;
    align-items: flex-start;
    margin-bottom: 1rem;
    flex-wrap: wrap;
    gap: 0.5rem;
}

.queue-timeline-status {
    font-weight: 600;
    margin-bottom: 0;
    font-size: 1.1rem;
}

.queue-latest-badge {
    background: var(--queue-gradient-primary);
    color: white;
    border-radius: 15px;
    font-size: 0.75rem;
    padding: 0.4rem 0.8rem;
    margin-left: 0.5rem;
    font-weight: 500;
    animation: queueGlow 2s ease-in-out infinite alternate;
}

.queue-timeline-date {
    color: var(--queue-text-muted);
    font-size: 0.85rem;
    font-weight: 500;
}

.queue-timeline-comment {
    background: var(--queue-gradient-light);
    border-radius: 12px;
    padding: 1.2rem;
    margin-bottom: 1rem;
    border-left: 4px solid var(--queue-primary-purple);
    font-size: 0.95rem;
    line-height: 1.6;
}

.queue-timeline-by {
    color: var(--queue-text-muted);
    font-size: 0.85rem;
    font-weight: 500;
}

/* Empty Timeline */
.queue-empty-timeline {
    text-align: center;
    padding: 3rem 2rem;
    color: var(--queue-text-muted);
}

.queue-empty-icon {
    font-size: 3rem;
    margin-bottom: 1rem;
    color: var(--queue-primary-purple);
    opacity: 0.5;
}

.queue-empty-timeline h6 {
    color: var(--queue-text-dark);
    margin-bottom: 0.5rem;
}

/* Action Buttons */
.queue-action-buttons {
    display: flex;
    gap: 1rem;
    justify-content: center;
    margin-bottom: 2rem;
    flex-wrap: wrap;
}

.queue-btn-back, .queue-btn-print {
    background: var(--queue-gradient-primary);
    color: white;
    border: none;
    border-radius: 16px;
    padding: 1rem 2rem;
    font-weight: 600;
    text-decoration: none;
    transition: all 0.3s ease;
    display: inline-flex;
    align-items: center;
    cursor: pointer;
}

.queue-btn-back:hover, .queue-btn-print:hover {
    transform: translateY(-3px);
    box-shadow: 0 15px 35px rgba(156, 107, 219, 0.4);
    color: white;
    text-decoration: none;
}

.queue-btn-print {
    background: linear-gradient(135deg, rgba(156, 107, 219, 0.1), rgba(156, 107, 219, 0.05));
    color: var(--queue-primary-purple);
    border: 2px solid rgba(156, 107, 219, 0.3);
}

.queue-btn-print:hover {
    background: var(--queue-gradient-primary);
    color: white;
}

/* Animations */
@keyframes queuePulse {
    0% { 
        box-shadow: 0 0 0 0 rgba(156, 107, 219, 0.7), 0 0 0 5px rgba(255, 255, 255, 1), 0 6px 30px rgba(0, 0, 0, 0.4);
        transform: scale(1.1);
    }
    70% { 
        box-shadow: 0 0 0 15px rgba(156, 107, 219, 0), 0 0 0 5px rgba(255, 255, 255, 1), 0 6px 30px rgba(0, 0, 0, 0.4);
        transform: scale(1.1);
    }
    100% { 
        box-shadow: 0 0 0 0 rgba(156, 107, 219, 0), 0 0 0 5px rgba(255, 255, 255, 1), 0 6px 30px rgba(0, 0, 0, 0.4);
        transform: scale(1.1);
    }
}

@keyframes queueGlow {
    from { box-shadow: 0 0 8px rgba(156, 107, 219, 0.5); }
    to { box-shadow: 0 0 20px rgba(156, 107, 219, 0.8); }
}

/* Responsive */
@media (max-width: 768px) {
    .queue-header {
        padding: 2rem;
    }
    
    .queue-status-badge-wrapper {
        position: static;
        margin-bottom: 1rem;
        text-align: left;
    }
    
    .queue-header-content {
        margin-right: 0;
    }
    
    .queue-status-icon-wrapper {
        margin-right: 1rem;
        margin-bottom: 1rem;
    }
    
    .queue-status-icon {
        width: 60px;
        height: 60px;
        font-size: 1.5rem;
    }
    
    .queue-timeline-item {
        padding-left: 3.5rem;
    }
    
    .queue-timeline-dot {
        width: 3rem;
        height: 3rem;
        font-size: 1.1rem;
    }
    
    .queue-timeline-line {
        left: 1.35rem;
    }
    
    .queue-action-buttons {
        flex-direction: column;
        align-items: center;
    }
    
    .queue-btn-back, .queue-btn-print {
        width: 100%;
        max-width: 300px;
        justify-content: center;
    }
    
    .queue-timeline-header-item {
        flex-direction: column;
        align-items: flex-start;
    }
    
    .queue-details-card {
        padding: 2rem;
        margin: 0 -1rem;
        width: calc(100% + 2rem);
    }
}

@media print {
    .queue-action-buttons,
    .queue-status-badge,
    .queue-copy-btn {
        display: none !important;
    }
    
    .queue-modern-card {
        box-shadow: none;
        border: 1px solid #ddd;
    }
    
    .queue-timeline-dot {
        box-shadow: none;
        border: 2px solid #333;
    }
}

/* เพิ่ม CSS สำหรับป้องกันปัญหาการโหลดฟอนต์ */
.fas::before {
    font-family: "Font Awesome 6 Free", "Font Awesome 5 Free", "FontAwesome", sans-serif !important;
    font-weight: 900 !important;
}
</style>

<script>
// Copy function
function copyQueueId(queueId) {
    if (navigator.clipboard) {
        navigator.clipboard.writeText(queueId).then(() => {
            showQueueAlert('คัดลอกหมายเลข ' + queueId + ' สำเร็จ', 'success');
        }).catch(() => {
            fallbackCopyQueueText(queueId);
        });
    } else {
        fallbackCopyQueueText(queueId);
    }
}

function fallbackCopyQueueText(text) {
    const textArea = document.createElement('textarea');
    textArea.value = text;
    document.body.appendChild(textArea);
    textArea.select();
    try {
        document.execCommand('copy');
        showQueueAlert('คัดลอกหมายเลข ' + text + ' สำเร็จ', 'success');
    } catch (err) {
        showQueueAlert('ไม่สามารถคัดลอกได้', 'error');
    }
    document.body.removeChild(textArea);
}

// Image modal
function openQueueImageModal(imageUrl, imageTitle) {
    const modal = document.getElementById('queueImageModal');
    const modalImage = document.getElementById('queueModalImage');
    const modalTitle = document.getElementById('queueImageModalLabel');
    
    if (modal && modalImage && modalTitle) {
        modalImage.src = imageUrl;
        modalTitle.textContent = imageTitle;
        
        if (typeof bootstrap !== 'undefined') {
            const imageModal = new bootstrap.Modal(modal);
            imageModal.show();
        } else {
            // Fallback for opening modal without Bootstrap
            modal.style.display = 'block';
            modal.style.opacity = '1';
            modal.classList.add('show');
            document.body.classList.add('modal-open');
            
            // Close modal on click outside
            modal.addEventListener('click', function(e) {
                if (e.target === modal) {
                    modal.style.display = 'none';
                    modal.classList.remove('show');
                    document.body.classList.remove('modal-open');
                }
            });
        }
    }
}

function showQueueAlert(message, type) {
    if (typeof Swal !== 'undefined') {
        Swal.fire({
            icon: type,
            title: message,
            timer: 2000,
            showConfirmButton: false,
            toast: true,
            position: 'top-end'
        });
    } else {
        // Enhanced fallback alert
        const alertDiv = document.createElement('div');
        alertDiv.style.cssText = `
            position: fixed;
            top: 20px;
            right: 20px;
            background: ${type === 'success' ? '#00B73E' : '#FF0202'};
            color: white;
            padding: 1rem 1.5rem;
            border-radius: 12px;
            box-shadow: 0 4px 15px rgba(0,0,0,0.2);
            z-index: 15000;
            font-family: 'Kanit', sans-serif;
            font-weight: 500;
            display: flex;
            align-items: center;
            gap: 0.5rem;
            animation: queueSlideInRight 0.3s ease;
        `;
        
        const icon = type === 'success' ? 'fas fa-check-circle' : 'fas fa-exclamation-circle';
        alertDiv.innerHTML = `<i class="${icon}"></i> ${message}`;
        
        document.body.appendChild(alertDiv);
        
        setTimeout(() => {
            alertDiv.style.animation = 'queueSlideOutRight 0.3s ease';
            setTimeout(() => alertDiv.remove(), 300);
        }, 3000);
    }
}

// Initialize animations และตรวจสอบไอคอน
document.addEventListener('DOMContentLoaded', function() {
    // การ animate การ์ด
    const cards = document.querySelectorAll('.queue-modern-card');
    cards.forEach((card, index) => {
        card.style.opacity = '0';
        card.style.transform = 'translateY(20px)';
        
        setTimeout(() => {
            card.style.transition = 'all 0.6s ease';
            card.style.opacity = '1';
            card.style.transform = 'translateY(0)';
        }, index * 200);
    });
    
    // เพิ่ม intersection observer สำหรับ timeline
    if ('IntersectionObserver' in window) {
        const timelineItems = document.querySelectorAll('.queue-timeline-item');
        const observer = new IntersectionObserver((entries) => {
            entries.forEach(entry => {
                if (entry.isIntersecting) {
                    entry.target.style.opacity = '1';
                    entry.target.style.transform = 'translateX(0)';
                }
            });
        }, {
            threshold: 0.3
        });
        
        timelineItems.forEach((item, index) => {
            item.style.opacity = '0';
            item.style.transform = 'translateX(-20px)';
            item.style.transition = `all 0.6s ease ${index * 0.1}s`;
            observer.observe(item);
        });
    }
});

// Add CSS animations for alerts
const queueAlertAnimationStyle = document.createElement('style');
queueAlertAnimationStyle.textContent = `
    @keyframes queueSlideInRight {
        from {
            transform: translateX(100%);
            opacity: 0;
        }
        to {
            transform: translateX(0);
            opacity: 1;
        }
    }
    
    @keyframes queueSlideOutRight {
        from {
            transform: translateX(0);
            opacity: 1;
        }
        to {
            transform: translateX(100%);
            opacity: 0;
        }
    }
`;
document.head.appendChild(queueAlertAnimationStyle);
</script>