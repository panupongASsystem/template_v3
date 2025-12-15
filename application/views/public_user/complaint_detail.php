<?php
defined('BASEPATH') or exit('No direct script access allowed');

// ตรวจสอบ login
if (!$this->session->userdata('mp_id')) {
    redirect('User');
    return;
}
?>

<div class="bg-pages">
    <div class="container-pages-news">
        
        <!-- Breadcrumb -->
        <nav aria-label="breadcrumb" class="mb-4">
            <ol class="breadcrumb-custom">
                <li><a href="<?php echo site_url('complaints_public/status'); ?>"><i class="fas fa-list me-1"></i>รายการแจ้งเรื่อง ร้องเรียน</a></li>
                <li class="active"><i class="fas fa-eye me-1"></i>รายละเอียด</li>
            </ol>
        </nav>

        <!-- Complaint Header -->
        <div class="modern-card complain-header">
            <?php 
            $latest_status = !empty($complain_details) ? end($complain_details)['complain_detail_status'] : $complain_data['complain_status'];
            $status_color = '#FFC700';
            $status_icon = 'fas fa-clock';
            switch ($latest_status) {
                case 'รับเรื่องแล้ว': 
                    $status_color = '#81C784'; 
                    $status_icon = 'fas fa-inbox';
                    break;
                case 'กำลังดำเนินการ': 
                    $status_color = '#e55a2b'; 
                    $status_icon = 'fas fa-cogs';
                    break;
                case 'รอดำเนินการ': 
                    $status_color = '#FFC700'; 
                    $status_icon = 'fas fa-hourglass-half';
                    break;
                case 'ดำเนินการเรียบร้อย': 
                case 'ดำเนินการแก้ไขเรียบร้อย': 
                    $status_color = '#00B73E'; 
                    $status_icon = 'fas fa-check-circle';
                    break;
                case 'ยกเลิก': 
                    $status_color = '#FF0202'; 
                    $status_icon = 'fas fa-times-circle';
                    break;
            }
            ?>
            
            <!-- Status Badge -->
            <div class="status-badge-wrapper">
                <span class="status-badge" style="background-color: <?php echo $status_color; ?>;">
                    <i class="<?php echo $status_icon; ?> me-1"></i><?php echo htmlspecialchars($latest_status); ?>
                </span>
            </div>
            
            <div class="complain-header-content">
                <div class="d-flex">
                    <div class="status-icon-wrapper">
                        <div class="status-icon" style="color: <?php echo $status_color; ?>;">
                            <i class="<?php echo $status_icon; ?>"></i>
                        </div>
                    </div>
                    <div class="complain-info flex-grow-1">
                        <div class="d-flex justify-content-between align-items-start">
                            <div>
                                <h5 class="complain-number">
                                    หมายเลขแจ้งเรื่อง ร้องเรียน: <span class="number-highlight"><?php echo htmlspecialchars($complain_data['complain_id']); ?></span>
                                </h5>
                                <h4 class="complain-topic">
                                    <?php echo htmlspecialchars($complain_data['complain_topic']); ?>
                                </h4>
                                <div class="complain-badges">
                                    <span class="badge-type">
                                        <i class="fas fa-tag me-1"></i><?php echo htmlspecialchars($complain_data['complain_type']); ?>
                                    </span>
                                    <span class="badge-date">
                                        <i class="fas fa-calendar me-1"></i><?php echo date('d/m/Y H:i', strtotime($complain_data['complain_datesave'])); ?>
                                    </span>
                                </div>
                            </div>
                            <button class="copy-btn" onclick="copyComplainId('<?php echo $complain_data['complain_id']; ?>')">
                                <i class="fas fa-copy me-1"></i>คัดลอก
                            </button>
                        </div>
                    </div>
                </div>
                
                <!-- Details Section -->
                <div class="details-section">
                    <div class="details-header">
                        <h5><i class="fas fa-info-circle me-2"></i>รายละเอียดแจ้งเรื่อง ร้องเรียน</h5>
                    </div>
                    
                    <div class="details-card">
                        <!-- <div class="row">
                            <div class="col-md-6">
                                <div class="detail-item">
                                    <h6><i class="fas fa-user me-2"></i>ผู้แจ้ง</h6>
                                    <p><?php echo htmlspecialchars($complain_data['complain_by']); ?></p>
                                </div>
                                
                                <div class="detail-item">
                                    <h6><i class="fas fa-phone me-2"></i>เบอร์โทรศัพท์</h6>
                                    <p><?php echo htmlspecialchars($complain_data['complain_phone']); ?></p>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="detail-item">
                                    <h6><i class="fas fa-envelope me-2"></i>อีเมล</h6>
                                    <p><?php echo htmlspecialchars($complain_data['complain_email'] ?: 'ไม่ระบุ'); ?></p>
                                </div>
                                
                                <div class="detail-item">
                                    <h6><i class="fas fa-map-marker-alt me-2"></i>ที่อยู่</h6>
                                    <p><?php echo htmlspecialchars($complain_data['complain_address']); ?></p>
                                </div>
                            </div>
                        </div>  -->
                        
                        <div class="detail-item full-width">
                            <h6><i class="fas fa-align-left me-2"></i>รายละเอียด</h6>
                            <div class="detail-content">
                                <p><?php echo nl2br(htmlspecialchars($complain_data['complain_detail'])); ?></p>
                            </div>
                        </div>
                        
                        <!-- รูปภาพประกอบ -->
                        <?php if (!empty($complain_images)): ?>
                            <div class="detail-item full-width">
                                <h6><i class="fas fa-images me-2"></i>รูปภาพประกอบการแจ้ง</h6>
                                <div class="images-gallery">
                                    <div class="row g-3">
                                        <?php foreach ($complain_images as $index => $image): ?>
                                            <div class="col-lg-3 col-md-4 col-sm-6">
                                                <div class="image-item">
                                                    <div class="image-wrapper" 
                                                         onclick="openImageModal('<?php echo base_url('docs/complain/' . $image['complain_img_img']); ?>', '<?php echo htmlspecialchars($complain_data['complain_topic']); ?> - รูปประกอบที่ <?php echo $index + 1; ?>')">
                                                        <img src="<?php echo base_url('docs/complain/' . $image['complain_img_img']); ?>" 
                                                             alt="รูปภาพประกอบ <?php echo $index + 1; ?>"
                                                             class="complain-image"
                                                             loading="lazy"
                                                             onerror="this.style.display='none';">
                                                        <div class="image-overlay">
                                                            <i class="fas fa-search-plus"></i>
                                                            <span>คลิกเพื่อขยาย</span>
                                                        </div>
                                                    </div>
                                                    <div class="image-caption">
                                                        <small>รูปประกอบที่ <?php echo $index + 1; ?></small>
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
        <div class="modern-card timeline-card">
            <div class="timeline-header">
                <h4><i class="fas fa-history me-2"></i>ประวัติการดำเนินงาน</h4>
            </div>
            
            <div class="modern-timeline">
                <?php if (!empty($complain_details)): ?>
                    <?php foreach (array_reverse($complain_details) as $index => $detail): 
                        $date = new DateTime($detail['complain_detail_datesave']);
                        $formatted_date = $date->format('d/m/Y');
                        $time = $date->format('H:i');
                        
                        // กำหนดสีและไอคอนตามสถานะ
                        $status_color = '#FFC700';
                        $status_icon = 'fas fa-clock';
                        $status_symbol = '●'; // fallback symbol
                        switch ($detail['complain_detail_status']) {
                            case 'รับเรื่องแล้ว': 
                                $status_color = '#ff7849'; 
                                $status_icon = 'fas fa-inbox';
                                $status_symbol = '📥';
                                break;
                            case 'กำลังดำเนินการ': 
                                $status_color = '#e55a2b'; 
                                $status_icon = 'fas fa-cogs';
                                $status_symbol = '⚙️';
                                break;
                            case 'รอดำเนินการ': 
                                $status_color = '#FFC700'; 
                                $status_icon = 'fas fa-hourglass-half';
                                $status_symbol = '⏳';
                                break;
                            case 'ดำเนินการเรียบร้อย': 
                            case 'ดำเนินการแก้ไขเรียบร้อย': 
                                $status_color = '#00B73E'; 
                                $status_icon = 'fas fa-check-circle';
                                $status_symbol = '✅';
                                break;
                            case 'ยกเลิก': 
                                $status_color = '#FF0202'; 
                                $status_icon = 'fas fa-times-circle';
                                $status_symbol = '❌';
                                break;
                        }
                        
                        $is_latest = $index === 0;
                    ?>
                        <div class="timeline-item <?php echo $is_latest ? 'timeline-latest' : ''; ?>">
                            <div class="timeline-dot" style="background: <?php echo $status_color; ?>;" data-status="<?php echo htmlspecialchars($detail['complain_detail_status']); ?>">
                                <i class="<?php echo $status_icon; ?>" style="color: white !important;"></i>
                                <span class="fallback-symbol" style="display: none;"><?php echo $status_symbol; ?></span>
                            </div>
                            
                            <?php if ($index < count($complain_details) - 1): ?>
                                <div class="timeline-line"></div>
                            <?php endif; ?>
                            
                            <div class="timeline-content">
                                <div class="timeline-header-item">
                                    <h6 class="timeline-status" style="color: <?php echo $status_color; ?>;">
                                        <?php echo htmlspecialchars($detail['complain_detail_status']); ?>
                                        <?php if ($is_latest): ?>
                                            <span class="latest-badge">ล่าสุด</span>
                                        <?php endif; ?>
                                    </h6>
                                    <small class="timeline-date">
                                        <i class="fas fa-calendar me-1"></i><?php echo $formatted_date; ?>
                                        <i class="fas fa-clock ms-2 me-1"></i><?php echo $time; ?> น.
                                    </small>
                                </div>
                                
                                <?php if (!empty($detail['complain_detail_com'])): ?>
                                    <div class="timeline-comment">
                                        <i class="fas fa-comment me-2"></i>
                                        <?php echo nl2br(htmlspecialchars($detail['complain_detail_com'])); ?>
                                    </div>
                                <?php endif; ?>
                                
                                <!-- รูปภาพการอัพเดท -->
                                <?php if (!empty($detail['detail_images'])): ?>
                                    <div class="timeline-images">
                                        <h6 class="timeline-images-title">
                                            <i class="fas fa-camera me-2"></i>รูปภาพการอัพเดท
                                            <span class="image-count">(<?php echo count($detail['detail_images']); ?> รูป)</span>
                                        </h6>
                                        <div class="timeline-images-gallery">
                                            <div class="row g-2">
                                                <?php foreach ($detail['detail_images'] as $img_index => $detail_image): ?>
                                                    <div class="col-6 col-md-4 col-lg-3">
                                                        <div class="timeline-image-item">
                                                            <div class="timeline-image-wrapper" 
                                                                 onclick="openImageModal('<?php echo base_url('docs/complain/status/' . $detail_image['detail_img_img']); ?>', '<?php echo htmlspecialchars($detail['complain_detail_status']); ?> - รูปอัพเดทที่ <?php echo $img_index + 1; ?>')">
                                                                <img src="<?php echo base_url('docs/complain/status/' . $detail_image['detail_img_img']); ?>" 
                                                                     alt="รูปภาพอัพเดท <?php echo $img_index + 1; ?>"
                                                                     class="timeline-image"
                                                                     loading="lazy"
                                                                     onerror="this.style.display='none';">
                                                                <div class="timeline-image-overlay">
                                                                    <i class="fas fa-expand-alt"></i>
                                                                </div>
                                                            </div>
                                                            <div class="timeline-image-caption">
                                                                <small>รูปที่ <?php echo $img_index + 1; ?></small>
                                                            </div>
                                                        </div>
                                                    </div>
                                                <?php endforeach; ?>
                                            </div>
                                        </div>
                                    </div>
                                <?php endif; ?>
                                
                                <?php if (!empty($detail['complain_detail_by'])): ?>
                                    <small class="timeline-by">
                                        <i class="fas fa-user me-1"></i>โดย: <?php echo htmlspecialchars($detail['complain_detail_by']); ?>
                                    </small>
                                <?php endif; ?>
                            </div>
                        </div>
                    <?php endforeach; ?>
                <?php else: ?>
                    <div class="empty-timeline">
                        <div class="empty-icon">
                            <i class="fas fa-inbox"></i>
                        </div>
                        <h6>ยังไม่มีการอัพเดทสถานะ</h6>
                        <p>เรื่องร้องเรียนของคุณอยู่ในขั้นตอนการพิจารณา</p>
                    </div>
                <?php endif; ?>
            </div>
        </div>

        <!-- Action Buttons -->
        <div class="action-buttons">
            <a href="<?php echo site_url('complaints_public/status'); ?>" class="btn-back">
                <i class="fas fa-arrow-left me-2"></i>กลับสู่รายการ
            </a>
            <button onclick="window.print()" class="btn-print">
                <i class="fas fa-print me-2"></i>พิมพ์
            </button>
        </div>
    </div>
</div>

<!-- Image Modal -->
<div class="modal fade" id="imageModal" tabindex="-1">
    <div class="modal-dialog modal-xl modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="imageModalLabel">รูปภาพ</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body text-center">
                <img id="modalImage" src="" alt="รูปภาพ" class="img-fluid">
            </div>
        </div>
    </div>
</div>

<style>
/* ใช้ CSS เดียวกับหน้า complaints_status.php และเพิ่มเติม */
@import url('https://fonts.googleapis.com/css2?family=Kanit:wght@300;400;500;600;700&display=swap');

/* เพิ่ม Font Awesome CDN สำรอง */
@import url('https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css');

:root {
    --primary-orange: #ff7849;
    --secondary-orange: #e55a2b;
    --light-orange: #ffeee8;
    --success-color: #00B73E;
    --warning-color: #FFC700;
    --danger-color: #FF0202;
    --text-dark: #2c3e50;
    --text-muted: #6c757d;
    --shadow-light: 0 4px 20px rgba(255, 120, 73, 0.1);
    --shadow-medium: 0 8px 30px rgba(255, 120, 73, 0.15);
    --gradient-primary: linear-gradient(135deg, #ff7849 0%, #e55a2b 100%);
    --gradient-light: linear-gradient(135deg, #fff7f0 0%, #ffeee8 100%);
}

* {
    font-family: 'Kanit', sans-serif;
}

.bg-pages {
    background: #ffffff;
    background-image: 
        radial-gradient(circle at 20% 30%, rgba(255, 120, 73, 0.03) 0%, transparent 50%),
        radial-gradient(circle at 80% 70%, rgba(229, 90, 43, 0.03) 0%, transparent 50%);
    min-height: 100vh;
    padding: 2rem 0;
}

.container-pages-news {
    max-width: 1000px;
    margin: 0 auto;
    padding: 0 1rem;
}

/* Breadcrumb */
.breadcrumb-custom {
    background: none;
    padding: 0;
    margin-bottom: 2rem;
    display: flex;
    align-items: center;
    gap: 1rem;
    font-size: 0.95rem;
}

.breadcrumb-custom li {
    display: flex;
    align-items: center;
}

.breadcrumb-custom li:not(:last-child)::after {
    content: '›';
    margin-left: 1rem;
    color: var(--text-muted);
    font-size: 1.2rem;
}

.breadcrumb-custom a {
    color: var(--primary-orange);
    text-decoration: none;
    font-weight: 500;
}

.breadcrumb-custom a:hover {
    text-decoration: underline;
}

.breadcrumb-custom .active {
    color: var(--text-muted);
    font-weight: 500;
}

/* Card Styles */
.modern-card {
    background: rgba(255, 255, 255, 0.95);
    border-radius: 24px;
    box-shadow: var(--shadow-light);
    margin-bottom: 2rem;
    overflow: hidden;
    position: relative;
}

.modern-card::before {
    content: '';
    position: absolute;
    top: 0;
    left: 0;
    right: 0;
    height: 4px;
    background: var(--gradient-primary);
    z-index: 1;
}

/* Complain Header */
.complain-header {
    padding: 2.5rem;
    position: relative;
}

.status-badge-wrapper {
    position: absolute;
    top: 1.5rem;
    right: 1.5rem;
    z-index: 10;
}

.status-badge {
    padding: 0.6rem 1.2rem;
    border-radius: 25px;
    font-weight: 600;
    font-size: 0.9rem;
    color: white;
    box-shadow: 0 4px 15px rgba(0, 0, 0, 0.2);
}

.complain-header-content {
    margin-right: 140px;
}

.status-icon-wrapper {
    margin-right: 2rem;
    margin-top: 0.5rem;
}

.status-icon {
    width: 80px;
    height: 80px;
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    background: rgba(255, 120, 73, 0.1);
    font-size: 2rem;
    box-shadow: 0 8px 25px rgba(255, 120, 73, 0.2);
}

.complain-number {
    color: var(--text-muted);
    font-weight: 500;
    margin-bottom: 0.5rem;
}

.number-highlight {
    color: var(--primary-orange);
    font-weight: 700;
}

.complain-topic {
    color: var(--text-dark);
    font-weight: 700;
    margin-bottom: 1rem;
    font-size: 1.5rem;
}

.complain-badges {
    margin-bottom: 1rem;
}

.badge-type, .badge-date {
    display: inline-block;
    padding: 0.4rem 0.8rem;
    border-radius: 12px;
    font-size: 0.85rem;
    margin-right: 0.5rem;
    font-weight: 500;
}

.badge-type {
    background: linear-gradient(135deg, rgba(255, 120, 73, 0.15) 0%, rgba(255, 107, 53, 0.15) 100%);
    color: var(--primary-orange);
    border: 1px solid rgba(255, 120, 73, 0.3);
}

.badge-date {
    background: linear-gradient(135deg, rgba(23, 162, 184, 0.15) 0%, rgba(23, 162, 184, 0.1) 100%);
    color: #17a2b8;
    border: 1px solid rgba(23, 162, 184, 0.3);
}

.copy-btn {
    background: rgba(255, 120, 73, 0.1);
    border: 1px solid rgba(255, 120, 73, 0.3);
    color: var(--primary-orange);
    border-radius: 12px;
    padding: 0.5rem 1rem;
    font-size: 0.9rem;
    transition: all 0.3s ease;
    cursor: pointer;
}

.copy-btn:hover {
    background: var(--gradient-primary);
    border-color: var(--primary-orange);
    color: white;
    transform: scale(1.05);
}

/* Details Section */
.details-section {
    margin-top: 2rem;
    padding-top: 2rem;
    border-top: 1px solid rgba(255, 120, 73, 0.1);
}

.details-header h5 {
    color: var(--primary-orange);
    font-weight: 600;
    margin-bottom: 1.5rem;
}

.details-card {
    border-radius: 16px;
    padding: 3rem;
    border-left: 4px solid var(--primary-orange);
    width: calc(100% + 13rem);
    max-width: none;
    margin: 0 -2rem;
    position: relative;
    left: 0;
    box-sizing: border-box;
}

.detail-item {
    margin-bottom: 1.5rem;
}

.detail-item h6 {
    color: var(--primary-orange);
    font-weight: 600;
    margin-bottom: 0.5rem;
}

.detail-item p {
    margin-bottom: 0;
    padding: 0.5rem 1rem;
    background: rgba(255, 120, 73, 0.05);
    border-radius: 8px;
    border-left: 3px solid var(--primary-orange);
    color: var(--text-dark);
}

.detail-content {
    padding: 1rem;
    background: rgba(255, 120, 73, 0.05);
    border-radius: 12px;
    border-left: 4px solid var(--primary-orange);
}

.detail-content p {
    background: none;
    border: none;
    padding: 0;
    margin: 0;
}

/* Images Gallery */
.images-gallery {
    margin-top: 1rem;
}

.image-item {
    position: relative;
    overflow: hidden;
    border-radius: 12px;
    background: white;
    box-shadow: 0 4px 15px rgba(255, 120, 73, 0.1);
    transition: all 0.3s ease;
}

.image-item:hover {
    transform: translateY(-5px);
    box-shadow: 0 8px 25px rgba(255, 120, 73, 0.2);
}

.image-wrapper {
    position: relative;
    overflow: hidden;
    border-radius: 12px 12px 0 0;
    cursor: pointer;
    aspect-ratio: 16/12;
    background: #f8f9fa;
}

.complain-image {
    width: 100%;
    height: 100%;
    object-fit: cover;
    transition: all 0.3s ease;
}

.image-overlay {
    position: absolute;
    top: 0;
    left: 0;
    right: 0;
    bottom: 0;
    background: var(--gradient-primary);
    display: flex;
    flex-direction: column;
    align-items: center;
    justify-content: center;
    opacity: 0;
    transition: all 0.3s ease;
    color: white;
    font-weight: 600;
}

.image-overlay i {
    font-size: 2rem;
    margin-bottom: 0.5rem;
}

.image-wrapper:hover .image-overlay {
    opacity: 0.9;
}

.image-wrapper:hover .complain-image {
    transform: scale(1.1);
}

.image-caption {
    padding: 0.8rem;
    text-align: center;
    background: var(--gradient-light);
}

.image-caption small {
    color: var(--primary-orange);
    font-weight: 600;
}

/* Timeline - แก้ไขให้ไอคอนแสดงผลชัดเจน */
.timeline-card {
    padding: 2.5rem;
}

.timeline-header {
    margin-bottom: 2rem;
    padding-bottom: 1rem;
    border-bottom: 2px solid rgba(255, 120, 73, 0.1);
}

.timeline-header h4 {
    color: var(--text-dark);
    font-weight: 600;
    margin-bottom: 0;
}

.modern-timeline {
    position: relative;
    padding-left: 1rem;
}

.timeline-item {
    position: relative;
    padding-left: 4.5rem;
    margin-bottom: 2.5rem;
}

.timeline-item:last-child {
    margin-bottom: 0;
}

/* แก้ไขการแสดงผล Timeline Dot และไอคอน */
.timeline-dot {
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

/* ปรับการแสดงผลไอคอนให้ชัดเจน */
.timeline-dot i {
    display: block !important;
    color: white !important;
    font-size: 1.3rem !important;
    font-weight: 900 !important;
    line-height: 1 !important;
    text-shadow: 0 2px 4px rgba(0, 0, 0, 0.4);
    z-index: 2;
    position: relative;
}

/* สำรอง fallback symbol */
.timeline-dot .fallback-symbol {
    font-size: 1.5rem;
    color: white;
    font-weight: bold;
    text-shadow: 0 2px 4px rgba(0, 0, 0, 0.4);
    position: absolute;
    z-index: 1;
}

.timeline-latest .timeline-dot {
    animation: pulse 2s infinite;
    box-shadow: 0 0 0 5px rgba(255, 255, 255, 1), 0 6px 30px rgba(0, 0, 0, 0.4);
    transform: scale(1.1);
}

.timeline-line {
    position: absolute;
    left: 1.6rem;
    top: 4rem;
    bottom: -2.5rem;
    width: 4px;
    background: linear-gradient(to bottom, rgba(255, 120, 73, 0.4), rgba(255, 120, 73, 0.2), #e9ecef);
    border-radius: 2px;
}

.timeline-item:last-child .timeline-line {
    display: none;
}

.timeline-content {
    background: rgba(255, 255, 255, 0.9);
    border-radius: 18px;
    padding: 1.8rem;
    border: 1px solid rgba(255, 120, 73, 0.15);
    box-shadow: 0 6px 25px rgba(255, 120, 73, 0.08);
    position: relative;
    margin-left: 0.5rem;
}

.timeline-content::before {
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

.timeline-header-item {
    display: flex;
    justify-content: space-between;
    align-items: flex-start;
    margin-bottom: 1rem;
    flex-wrap: wrap;
    gap: 0.5rem;
}

.timeline-status {
    font-weight: 600;
    margin-bottom: 0;
    font-size: 1.1rem;
}

.latest-badge {
    background: var(--gradient-primary);
    color: white;
    border-radius: 15px;
    font-size: 0.75rem;
    padding: 0.4rem 0.8rem;
    margin-left: 0.5rem;
    font-weight: 500;
    animation: glow 2s ease-in-out infinite alternate;
}

.timeline-date {
    color: var(--text-muted);
    font-size: 0.85rem;
    font-weight: 500;
}

.timeline-comment {
    background: var(--gradient-light);
    border-radius: 12px;
    padding: 1.2rem;
    margin-bottom: 1rem;
    border-left: 4px solid var(--primary-orange);
    font-size: 0.95rem;
    line-height: 1.6;
}

/* Timeline Images - รูปภาพการอัพเดท */
.timeline-images {
    background: rgba(255, 255, 255, 0.7);
    border-radius: 16px;
    padding: 1.5rem;
    margin-bottom: 1rem;
    border: 2px dashed rgba(255, 120, 73, 0.3);
    transition: all 0.3s ease;
}

.timeline-images:hover {
    border-color: var(--primary-orange);
    box-shadow: 0 4px 15px rgba(255, 120, 73, 0.1);
}

.timeline-images-title {
    color: var(--primary-orange);
    font-weight: 600;
    margin-bottom: 1rem;
    font-size: 1rem;
    display: flex;
    align-items: center;
}

.image-count {
    background: rgba(255, 120, 73, 0.1);
    color: var(--primary-orange);
    padding: 0.2rem 0.6rem;
    border-radius: 8px;
    font-size: 0.8rem;
    margin-left: 0.5rem;
    font-weight: 500;
}

.timeline-images-gallery {
    margin-top: 1rem;
}

.timeline-image-item {
    position: relative;
    overflow: hidden;
    border-radius: 10px;
    background: white;
    box-shadow: 0 3px 12px rgba(255, 120, 73, 0.1);
    transition: all 0.3s ease;
}

.timeline-image-item:hover {
    transform: translateY(-3px);
    box-shadow: 0 6px 20px rgba(255, 120, 73, 0.2);
}

.timeline-image-wrapper {
    position: relative;
    overflow: hidden;
    border-radius: 10px 10px 0 0;
    cursor: pointer;
    aspect-ratio: 4/3;
    background: #f8f9fa;
}

.timeline-image {
    width: 100%;
    height: 100%;
    object-fit: cover;
    transition: all 0.3s ease;
}

.timeline-image-overlay {
    position: absolute;
    top: 0;
    left: 0;
    right: 0;
    bottom: 0;
    background: rgba(255, 120, 73, 0.8);
    display: flex;
    align-items: center;
    justify-content: center;
    opacity: 0;
    transition: all 0.3s ease;
    color: white;
    font-size: 1.2rem;
}

.timeline-image-wrapper:hover .timeline-image-overlay {
    opacity: 1;
}

.timeline-image-wrapper:hover .timeline-image {
    transform: scale(1.05);
}

.timeline-image-caption {
    padding: 0.6rem;
    text-align: center;
    background: rgba(255, 120, 73, 0.05);
    border-top: 1px solid rgba(255, 120, 73, 0.1);
}

.timeline-image-caption small {
    color: var(--primary-orange);
    font-weight: 600;
    font-size: 0.8rem;
}

.timeline-by {
    color: var(--text-muted);
    font-size: 0.85rem;
    font-weight: 500;
}

/* Empty Timeline */
.empty-timeline {
    text-align: center;
    padding: 3rem 2rem;
    color: var(--text-muted);
}

.empty-icon {
    font-size: 3rem;
    margin-bottom: 1rem;
    color: var(--primary-orange);
    opacity: 0.5;
}

.empty-timeline h6 {
    color: var(--text-dark);
    margin-bottom: 0.5rem;
}

/* Action Buttons */
.action-buttons {
    display: flex;
    gap: 1rem;
    justify-content: center;
    margin-bottom: 2rem;
    flex-wrap: wrap;
}

.btn-back, .btn-print {
    background: var(--gradient-primary);
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

.btn-back:hover, .btn-print:hover {
    transform: translateY(-3px);
    box-shadow: 0 15px 35px rgba(255, 120, 73, 0.4);
    color: white;
    text-decoration: none;
}

.btn-print {
    background: linear-gradient(135deg, rgba(255, 120, 73, 0.1), rgba(255, 120, 73, 0.05));
    color: var(--primary-orange);
    border: 2px solid rgba(255, 120, 73, 0.3);
}

.btn-print:hover {
    background: var(--gradient-primary);
    color: white;
}

/* Animations */
@keyframes pulse {
    0% { 
        box-shadow: 0 0 0 0 rgba(255, 120, 73, 0.7), 0 0 0 5px rgba(255, 255, 255, 1), 0 6px 30px rgba(0, 0, 0, 0.4);
        transform: scale(1.1);
    }
    70% { 
        box-shadow: 0 0 0 15px rgba(255, 120, 73, 0), 0 0 0 5px rgba(255, 255, 255, 1), 0 6px 30px rgba(0, 0, 0, 0.4);
        transform: scale(1.1);
    }
    100% { 
        box-shadow: 0 0 0 0 rgba(255, 120, 73, 0), 0 0 0 5px rgba(255, 255, 255, 1), 0 6px 30px rgba(0, 0, 0, 0.4);
        transform: scale(1.1);
    }
}

@keyframes glow {
    from { box-shadow: 0 0 8px rgba(255, 120, 73, 0.5); }
    to { box-shadow: 0 0 20px rgba(255, 120, 73, 0.8); }
}

/* การแสดงผลเมื่อ Font Awesome ไม่โหลด */
.timeline-dot.font-fallback i {
    display: none !important;
}

.timeline-dot.font-fallback .fallback-symbol {
    display: block !important;
}

/* Responsive */
@media (max-width: 768px) {
    .complain-header {
        padding: 2rem;
    }
    
    .status-badge-wrapper {
        position: static;
        margin-bottom: 1rem;
        text-align: left;
    }
    
    .complain-header-content {
        margin-right: 0;
    }
    
    .status-icon-wrapper {
        margin-right: 1rem;
        margin-bottom: 1rem;
    }
    
    .status-icon {
        width: 60px;
        height: 60px;
        font-size: 1.5rem;
    }
    
    .timeline-item {
        padding-left: 3.5rem;
    }
    
    .timeline-dot {
        width: 3rem;
        height: 3rem;
        font-size: 1.1rem;
    }
    
    .timeline-line {
        left: 1.35rem;
    }
    
    .action-buttons {
        flex-direction: column;
        align-items: center;
    }
    
    .btn-back, .btn-print {
        width: 100%;
        max-width: 300px;
        justify-content: center;
    }
    
    .timeline-header-item {
        flex-direction: column;
        align-items: flex-start;
    }
    
    .timeline-images {
        padding: 1rem;
    }
    
    .timeline-images-title {
        font-size: 0.9rem;
        flex-direction: column;
        align-items: flex-start;
        gap: 0.3rem;
    }
}

@media print {
    .action-buttons,
    .status-badge,
    .copy-btn {
        display: none !important;
    }
    
    .modern-card {
        box-shadow: none;
        border: 1px solid #ddd;
    }
    
    .timeline-dot {
        box-shadow: none;
        border: 2px solid #333;
    }
    
    .timeline-images {
        border: 1px solid #ddd;
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
function copyComplainId(complainId) {
    if (navigator.clipboard) {
        navigator.clipboard.writeText(complainId).then(() => {
            showAlert('คัดลอกหมายเลข ' + complainId + ' สำเร็จ', 'success');
        }).catch(() => {
            fallbackCopyText(complainId);
        });
    } else {
        fallbackCopyText(complainId);
    }
}

function fallbackCopyText(text) {
    const textArea = document.createElement('textarea');
    textArea.value = text;
    document.body.appendChild(textArea);
    textArea.select();
    try {
        document.execCommand('copy');
        showAlert('คัดลอกหมายเลข ' + text + ' สำเร็จ', 'success');
    } catch (err) {
        showAlert('ไม่สามารถคัดลอกได้', 'error');
    }
    document.body.removeChild(textArea);
}

// Image modal
function openImageModal(imageUrl, imageTitle) {
    const modal = document.getElementById('imageModal');
    const modalImage = document.getElementById('modalImage');
    const modalTitle = document.getElementById('imageModalLabel');
    
    if (modal && modalImage && modalTitle) {
        modalImage.src = imageUrl;
        modalTitle.textContent = imageTitle;
        
        if (typeof bootstrap !== 'undefined') {
            const imageModal = new bootstrap.Modal(modal);
            imageModal.show();
        }
    }
}

function showAlert(message, type) {
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
        alert(message);
    }
}

// ฟังก์ชันตรวจสอบและแก้ไขการแสดงผลไอคอน
function checkAndFixIcons() {
    // ตรวจสอบการโหลด Font Awesome
    const testIcon = document.createElement('i');
    testIcon.className = 'fas fa-check';
    testIcon.style.position = 'absolute';
    testIcon.style.left = '-9999px';
    testIcon.style.fontSize = '16px';
    document.body.appendChild(testIcon);
    
    // รอให้ฟอนต์โหลดเสร็จ
    setTimeout(() => {
        const computedStyle = window.getComputedStyle(testIcon);
        const fontFamily = computedStyle.fontFamily;
        const width = testIcon.offsetWidth;
        
        console.log('Font Family:', fontFamily);
        console.log('Icon Width:', width);
        
        // ตรวจสอบว่าไอคอนโหลดหรือไม่
        const isFontAwesomeLoaded = fontFamily.includes('Font Awesome') || width > 0;
        
        if (!isFontAwesomeLoaded) {
            console.warn('Font Awesome icons not loading properly, using fallback symbols');
            
            // เปลี่ยนเป็น fallback symbols
            const timelineDots = document.querySelectorAll('.timeline-dot');
            timelineDots.forEach(dot => {
                dot.classList.add('font-fallback');
                const icon = dot.querySelector('i');
                const symbol = dot.querySelector('.fallback-symbol');
                
                if (icon && symbol) {
                    icon.style.display = 'none';
                    symbol.style.display = 'block';
                }
            });
        } else {
            console.log('Font Awesome icons loaded successfully');
            
            // เพิ่มการตรวจสอบไอคอนเฉพาะที่อาจไม่แสดง
            const timelineDots = document.querySelectorAll('.timeline-dot');
            timelineDots.forEach(dot => {
                const icon = dot.querySelector('i');
                if (icon) {
                    // ตรวจสอบว่าไอคอนแสดงผลหรือไม่
                    const iconRect = icon.getBoundingClientRect();
                    if (iconRect.width === 0 || iconRect.height === 0) {
                        console.warn('Icon not displaying properly:', icon.className);
                        
                        // ใช้ fallback
                        const symbol = dot.querySelector('.fallback-symbol');
                        if (symbol) {
                            icon.style.display = 'none';
                            symbol.style.display = 'block';
                            dot.classList.add('font-fallback');
                        }
                    }
                }
            });
        }
        
        document.body.removeChild(testIcon);
    }, 500);
}

// Initialize animations และตรวจสอบไอคอน
document.addEventListener('DOMContentLoaded', function() {
    // ตรวจสอบไอคอน
    checkAndFixIcons();
    
    // การ animate การ์ด
    const cards = document.querySelectorAll('.modern-card');
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
        const timelineItems = document.querySelectorAll('.timeline-item');
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
    
    // เพิ่ม lazy loading สำหรับรูปภาพ timeline
    const timelineImages = document.querySelectorAll('.timeline-image');
    if ('IntersectionObserver' in window) {
        const imageObserver = new IntersectionObserver((entries) => {
            entries.forEach(entry => {
                if (entry.isIntersecting) {
                    const img = entry.target;
                    if (img.dataset.src) {
                        img.src = img.dataset.src;
                        img.removeAttribute('data-src');
                    }
                    imageObserver.unobserve(img);
                }
            });
        });
        
        timelineImages.forEach(img => {
            imageObserver.observe(img);
        });
    }
});

// ตรวจสอบอีกครั้งหลังจากหน้าโหลดเสร็จ
window.addEventListener('load', function() {
    setTimeout(() => {
        checkAndFixIcons();
    }, 1000);
});

// ตรวจสอบเมื่อฟอนต์โหลดเสร็จ
if ('fonts' in document) {
    document.fonts.ready.then(() => {
        console.log('All fonts loaded');
        setTimeout(() => {
            checkAndFixIcons();
        }, 200);
    });
}
</script>