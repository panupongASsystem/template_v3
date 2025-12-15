<div class="text-center pages-head">
    <span class="font-pages-head">ติดตามสถานะแจ้งเรื่อง ร้องเรียน</span>
</div>
</div>
<img src="<?php echo base_url('docs/welcome-btm-light-other.png'); ?>">

<!-- หน้าติดตามสถานะเรื่องร้องเรียน - Modern Orange Theme พร้อมระบบแสดงรูปภาพ (แก้ไขแล้ว) -->

<!-- Modal Error -->
<div class="modal fade" id="errorModal" tabindex="-1" aria-labelledby="errorModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content modern-modal">
            <div class="modal-header modern-modal-header error-header">
                <h5 class="modal-title" id="errorModalLabel">
                    <i class="fas fa-exclamation-triangle me-2"></i>ไม่พบข้อมูล
                </h5>
            </div>
            <div class="modal-body text-center modern-modal-body">
                <div class="error-animation mb-4">
                    <div class="error-icon">
                        <i class="fas fa-times-circle"></i>
                    </div>
                </div>
                <h6 class="error-title mb-3">ไม่พบหมายเลขร้องเรียนที่ระบุ</h6>
                <p class="error-message" id="errorMessage">กรุณาตรวจสอบหมายเลขร้องเรียนและลองใหม่อีกครั้ง</p>
            </div>
            <div class="modal-footer modern-modal-footer">
                <button type="button" class="btn modern-btn-danger" data-bs-dismiss="modal">
                    <i class="fas fa-times me-2"></i>ปิด
                </button>
            </div>
        </div>
    </div>
</div>

<!-- PHP Helper Function -->
<?php
function hex2rgb($hex) {
    $hex = str_replace('#', '', $hex);
    if (strlen($hex) == 3) {
        $r = hexdec(substr($hex, 0, 1) . substr($hex, 0, 1));
        $g = hexdec(substr($hex, 1, 1) . substr($hex, 1, 1));
        $b = hexdec(substr($hex, 2, 1) . substr($hex, 2, 1));
    } else {
        $r = hexdec(substr($hex, 0, 2));
        $g = hexdec(substr($hex, 2, 2));
        $b = hexdec(substr($hex, 4, 2));
    }
    return "$r, $g, $b";
}
?>

<!-- Modal สำหรับขยายรูปภาพ (แก้ไขแล้ว) -->
<div class="modal fade" id="imageModal" tabindex="-1" aria-labelledby="imageModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-xl modal-dialog-centered">
        <div class="modal-content modern-modal image-modal">
            <div class="modal-header modern-modal-header">
                <h5 class="modal-title" id="imageModalLabel">
                    <i class="fas fa-image me-2"></i>รูปภาพประกอบ
                </h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body text-center p-0">
                <div class="image-container">
                    <img id="modalImage" src="" alt="รูปภาพประกอบ" class="modal-image">
                </div>
            </div>
            <div class="modal-footer modern-modal-footer">
                <button type="button" class="btn modern-btn-primary" data-bs-dismiss="modal">
                    <i class="fas fa-times me-2"></i>ปิด
                </button>
                <a id="downloadImageBtn" href="" download class="btn modern-btn-outline">
                    <i class="fas fa-download me-2"></i>ดาวน์โหลด
                </a>
            </div>
        </div>
    </div>
</div>

<div class="bg-pages">
    <div class="container-pages-news">
        
        <!-- User Info Display -->
        <?php if ($is_logged_in ?? false): ?>
            <div class="modern-alert alert-member">
                <div class="d-flex align-items-center">
                    <div class="alert-icon member-icon">
                        <i class="fas fa-user-check"></i>
                    </div>
                    <div>
                        <h6 class="alert-title">
                            <i class="fas fa-shield-alt me-2"></i>สำหรับสมาชิก
                        </h6>
                        <p class="alert-text">
                            คุณสามารถดูได้เฉพาะเรื่องร้องเรียนของคุณเองเท่านั้น
                        </p>
                    </div>
                </div>
            </div>
        <?php else: ?>
            <div class="modern-alert alert-guest">
                <div class="d-flex align-items-center">
                    <div class="alert-icon guest-icon">
                        <i class="fas fa-user-friends"></i>
                    </div>
                    <div>
                        <h6 class="alert-title">
                            <i class="fas fa-info-circle me-2"></i>สำหรับผู้ใช้ทั่วไป
                        </h6>
                        <p class="alert-text">
                            ท่านสามารถติดตามเรื่องร้องเรียนที่แจ้ง โดยไม่ต้องเข้าสู่ระบบ
                        </p>
                    </div>
                </div>
            </div>
        <?php endif; ?>

        <!-- Search Section -->
        <div class="search-card modern-card">
            <form id="followComplainForm" action="<?php echo site_url('Pages/follow_complain'); ?>" method="post" class="form-horizontal">
                <div class="search-content">
                    <div class="search-header">
                        <h4 class="search-title">
                            <i class="fas fa-search me-2"></i>ติดตามสถานะ
                        </h4>
                        <p class="search-subtitle">กรอกหมายเลขเพื่อดูสถานะการดำเนินงาน</p>
                    </div>
                    
                    <div class="search-form">
                        <?php 
// แก้ไขส่วนการกำหนดค่า input
$complain_id_value = '';

// เพิ่มการตรวจสอบ GET parameter ก่อน
if (isset($_GET['auto_search']) && !empty($_GET['auto_search'])) {
    $complain_id_value = htmlspecialchars(trim($_GET['auto_search']));
} elseif (!empty($complain_data)) {
    $complain_id_value = $complain_data['complain_id'];
} elseif (isset($_POST['complain_id'])) {
    $complain_id_value = $_POST['complain_id'];
} elseif ($this->uri->segment(3)) {
    $complain_id_value = $this->uri->segment(3);
} elseif (isset($auto_search_id)) {
    $complain_id_value = $auto_search_id;
}
?>
                        <div class="search-input-wrapper">
                            <div class="input-icon">
                                <i class="fas fa-hashtag"></i>
                            </div>
                            <input type="text" 
                                   name="complain_id" 
                                   id="complainIdInput"
                                   class="modern-input" 
                                   required 
                                   value="<?php echo htmlspecialchars($complain_id_value); ?>" 
                                   placeholder="กรอกหมายเลขร้องเรียน เช่น 67000001">
                        </div>
                        
                        <div class="input-help">
                            <i class="fas fa-info-circle me-1"></i>หมายเลขร้องเรียนประกอบด้วยตัวเลข 8 หลัก
                        </div>
                        
                        <button type="submit" 
                                id="searchBtn" 
                                class="modern-btn search-btn">
                            <span class="btn-content">
                                <i class="fas fa-search me-2"></i>ค้นหา
                            </span>
                            <div class="btn-shine"></div>
                        </button>
                    </div>
                    
                    <!-- ปุ่มแจ้งเรื่องใหม่ -->
                    <div class="quick-action">
                        <a href="<?php echo site_url('Pages/adding_complain'); ?>" class="modern-btn-outline">
                            <i class="fas fa-plus me-2"></i>แจ้งเรื่อง ร้องเรียนใหม่
                        </a>
                    </div>
                </div>
            </form>
        </div>

        <!-- Results Section -->
        <?php if (!empty($complain_data)) : ?>
            <!-- Complain Header Info -->
            <div class="modern-card complain-header" 
                 id="pages-follow-complain-detail"
                 <?php if (!empty($complain_data)) : ?>
                     style="display: block;" 
                 <?php else : ?>
                     style="display: none;" 
                 <?php endif; ?>>
                <!-- Status Badge -->
                <div class="status-badge-wrapper">
                    <?php 
                    $latest_status = !empty($complain_details) ? end($complain_details)['complain_detail_status'] : $complain_data['complain_status'];
                    $status_color = '#FFC700';
                    $status_icon = 'fas fa-clock';
                    switch ($latest_status) {
                        case 'รับเรื่องแล้ว': 
                            $status_color = '#ff7849'; 
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
                                        หมายเลขร้องเรียน: <span class="number-highlight"><?php echo htmlspecialchars($complain_data['complain_id']); ?></span>
                                    </h5>
                                    <h4 class="complain-topic">
                                        <?php echo htmlspecialchars($complain_data['complain_topic']); ?>
                                    </h4>
                                    <div class="complain-badges">
                                        <span class="badge-type">
                                            <i class="fas fa-tag me-1"></i><?php echo htmlspecialchars($complain_data['complain_type']); ?>
                                        </span>
                                        <span class="badge-user">
                                            <i class="fas fa-user me-1"></i><?php echo htmlspecialchars($complain_data['complain_user_type'] ?? 'guest'); ?>
                                        </span>
                                    </div>
                                </div>
                                <button class="copy-btn" onclick="copyComplainId('<?php echo $complain_data['complain_id']; ?>')">
                                    <i class="fas fa-copy me-1"></i>คัดลอก
                                </button>
                            </div>
                        </div>
                    </div>
                    
                    <!-- Details always visible -->
                    <div class="details-section">
                        <div class="details-header">
                            <h5><i class="fas fa-info-circle me-2"></i>รายละเอียดเพิ่มเติม</h5>
                        </div>
                        
                        <div class="details-card">
                            <div class="row">
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
                            </div>
                            
                            <div class="detail-item full-width">
                                <h6><i class="fas fa-align-left me-2"></i>รายละเอียด</h6>
                                <div class="detail-content">
                                    <p><?php echo nl2br(htmlspecialchars($complain_data['complain_detail'])); ?></p>
                                </div>
                            </div>
                            
                            <!-- แสดงรูปภาพประกอบ (แก้ไขแล้ว) -->
                            <?php if (!empty($complain_data['images'])): ?>
                                <div class="detail-item full-width">
                                    <h6><i class="fas fa-images me-2"></i>รูปภาพประกอบ</h6>
                                    <div class="images-gallery">
                                        <div class="row g-3">
                                            <?php foreach ($complain_data['images'] as $index => $image): ?>
                                                <div class="col-lg-3 col-md-4 col-sm-6">
                                                    <div class="image-item">
                                                        <div class="image-wrapper" 
                                                             data-image-url="<?php echo base_url('docs/complain/' . $image['complain_img_img']); ?>" 
                                                             data-image-title="<?php echo htmlspecialchars($complain_data['complain_topic']); ?> - รูปที่ <?php echo $index + 1; ?>">
                                                            <img src="<?php echo base_url('docs/complain/' . $image['complain_img_img']); ?>" 
                                                                 alt="รูปภาพประกอบ <?php echo $index + 1; ?>"
                                                                 class="complain-image"
                                                                 loading="lazy"
                                                                 onerror="this.onerror=null; this.src='data:image/svg+xml;base64,PHN2ZyB3aWR0aD0iMjAwIiBoZWlnaHQ9IjIwMCIgeG1sbnM9Imh0dHA6Ly93d3cudzMub3JnLzIwMDAvc3ZnIj48cmVjdCB3aWR0aD0iMTAwJSIgaGVpZ2h0PSIxMDAlIiBmaWxsPSIjZGRkIi8+PHRleHQgeD0iNTAlIiB5PSI1MCUiIGZvbnQtZmFtaWx5PSJBcmlhbCwgc2Fucy1zZXJpZiIgZm9udC1zaXplPSIxNCIgZmlsbD0iIzk5OSIgdGV4dC1hbmNob3I9Im1pZGRsZSIgZHk9Ii4zZW0iPuE4muE5ieE4reE4quE4oeE4suE4o+E4luE5guE4q+E4leE4o+E4ueE4lOE4oOE4suE4nuE5hOE4lOE5iDwvdGV4dD48L3N2Zz4='; this.style.border='2px dashed #ccc';">
                                                            <div class="image-overlay">
                                                                <i class="fas fa-search-plus"></i>
                                                                <span>คลิกเพื่อขยาย</span>
                                                            </div>
                                                        </div>
                                                        <div class="image-caption">
                                                            <small>รูปที่ <?php echo $index + 1; ?></small>
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
            <div class="modern-card timeline-card"
                 <?php if (empty($complain_data)) : ?>
                     style="display: none;" 
                 <?php endif; ?>>
                <div class="timeline-header">
                    <h4>
                        <i class="fas fa-history me-2"></i>ประวัติการดำเนินงาน
                    </h4>
                </div>
                
                <div class="modern-timeline">
                    <?php if (!empty($complain_details)): ?>
                        <?php foreach (array_reverse($complain_details) as $index => $detail): 
                            $date = new DateTime($detail['complain_detail_datesave']);
                            $day_th = $date->format('d');
                            $month_names_th = array(
                                1 => 'ม.ค.', 2 => 'ก.พ.', 3 => 'มี.ค.', 4 => 'เม.ย.',
                                5 => 'พ.ค.', 6 => 'มิ.ย.', 7 => 'ก.ค.', 8 => 'ส.ค.',
                                9 => 'ก.ย.', 10 => 'ต.ค.', 11 => 'พ.ย.', 12 => 'ธ.ค.'
                            );
                            $month_th = $month_names_th[$date->format('n')];
                            $year_th = $date->format('Y') + 543;
                            $time = $date->format('H:i');
                            $formattedDate = "$day_th $month_th $year_th";
                            
                            // กำหนดสีและไอคอนตามสถานะ
                            $status_color = '#FFC700';
                            $status_icon = 'fas fa-clock';
                            switch ($detail['complain_detail_status']) {
                                case 'รับเรื่องแล้ว': 
                                    $status_color = '#ff7849'; 
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
                            
                            $is_latest = $index === 0;
                        ?>
                            <div class="timeline-item <?php echo $is_latest ? 'timeline-latest' : ''; ?>">
                                <!-- Timeline dot -->
                                <div class="timeline-dot" style="background: <?php echo $status_color; ?>;">
                                    <i class="<?php echo $status_icon; ?>"></i>
                                </div>
                                
                                <!-- Timeline line -->
                                <?php if ($index < count($complain_details) - 1): ?>
                                    <div class="timeline-line"></div>
                                <?php endif; ?>
                                
                                <!-- Content -->
                                <div class="timeline-content">
                                    <div class="timeline-header-item">
                                        <h6 class="timeline-status" style="color: <?php echo $status_color; ?>;">
                                            <?php echo htmlspecialchars($detail['complain_detail_status']); ?>
                                            <?php if ($is_latest): ?>
                                                <span class="latest-badge">ล่าสุด</span>
                                            <?php endif; ?>
                                        </h6>
                                        <small class="timeline-date">
                                            <i class="fas fa-calendar me-1"></i><?php echo $formattedDate; ?>
                                            <i class="fas fa-clock ms-2 me-1"></i><?php echo $time; ?> น.
                                        </small>
                                    </div>
                                    
                                    <?php if (!empty($detail['complain_detail_com'])): ?>
                                        <div class="timeline-comment">
                                            <i class="fas fa-comment me-2"></i>
                                            <?php echo nl2br(htmlspecialchars($detail['complain_detail_com'])); ?>
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
            
        <?php endif; ?>
    </div>
</div>

<!-- CSS Styles -->
<style>
/* Import Fonts */
@import url('https://fonts.googleapis.com/css2?family=Kanit:wght@300;400;500;600;700&display=swap');

/* Global Variables */
:root {
    --primary-orange: #ff7849;
    --secondary-orange: #e55a2b;
    --light-orange: #ffeee8;
    --very-light-orange: #fff7f0;
    --success-color: #00B73E;
    --warning-color: #FFC700;
    --danger-color: #FF0202;
    --text-dark: #2c3e50;
    --text-muted: #6c757d;
    --border-light: rgba(255, 120, 73, 0.1);
    --shadow-light: 0 4px 20px rgba(255, 120, 73, 0.1);
    --shadow-medium: 0 8px 30px rgba(255, 120, 73, 0.15);
    --shadow-heavy: 0 15px 40px rgba(255, 120, 73, 0.2);
    --gradient-primary: linear-gradient(135deg, #ff7849 0%, #e55a2b 100%);
    --gradient-light: linear-gradient(135deg, #fff7f0 0%, #ffeee8 100%);
}

/* Base Styles */

body {
    background: #ffffff;
    background-image: 
        radial-gradient(circle at 20% 30%, rgba(255, 120, 73, 0.03) 0%, transparent 50%),
        radial-gradient(circle at 80% 70%, rgba(229, 90, 43, 0.03) 0%, transparent 50%),
        linear-gradient(135deg, rgba(255, 247, 240, 0.5) 0%, rgba(255, 255, 255, 0.8) 100%);
    min-height: 100vh;
    padding: 2rem 0;
}

/* Page Header */
.pages-head {
    position: relative;
    margin-bottom: 3rem;
}

.header-decoration {
    width: 100px;
    height: 4px;
    background: var(--gradient-primary);
    margin: 0 auto 1rem;
    border-radius: 2px;
    position: relative;
}

.header-decoration::before {
    content: '';
    position: absolute;
    top: -2px;
    left: 50%;
    transform: translateX(-50%);
    width: 20px;
    height: 8px;
    background: var(--gradient-primary);
    border-radius: 4px;
}


.header-subtitle {
    font-size: 1.2rem;
    color: var(--text-muted);
    margin-bottom: 0;
    font-weight: 400;
}

.bg-pages {
    background: transparent;
    padding: 0;
}

.container-pages-news {
    max-width: 1000px;
    margin: 0 auto;
    padding: 0 1rem;
}

/* Modern Card */
.modern-card {
    background: rgba(255, 255, 255, 0.95);
    backdrop-filter: blur(20px);
    border: 1px solid rgba(255, 120, 73, 0.1);
    border-radius: 24px;
    box-shadow: var(--shadow-light);
    transition: all 0.4s cubic-bezier(0.4, 0, 0.2, 1);
    overflow: hidden;
    position: relative;
    margin-bottom: 2rem;
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

.modern-card:hover {
    transform: translateY(-5px);
    box-shadow: var(--shadow-heavy);
}

/* Alert Styles */
.modern-alert {
    border: none;
    border-radius: 20px;
    padding: 1.5rem;
    margin-bottom: 2rem;
    position: relative;
    overflow: hidden;
    backdrop-filter: blur(10px);
}

.alert-member {
    background: linear-gradient(135deg, rgba(255, 120, 73, 0.08) 0%, rgba(229, 90, 43, 0.05) 100%);
    border-left: 4px solid var(--primary-orange);
}

.alert-guest {
    background: linear-gradient(135deg, rgba(255, 199, 0, 0.08) 0%, rgba(255, 183, 3, 0.05) 100%);
    border-left: 4px solid var(--warning-color);
}

.alert-icon {
    width: 60px;
    height: 60px;
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    margin-right: 1rem;
    position: relative;
}

.member-icon {
    background: linear-gradient(135deg, rgba(255, 120, 73, 0.2) 0%, rgba(255, 120, 73, 0.1) 100%);
    color: var(--primary-orange);
    box-shadow: 0 4px 15px rgba(255, 120, 73, 0.3);
}

.guest-icon {
    background: linear-gradient(135deg, rgba(255, 199, 0, 0.2) 0%, rgba(255, 199, 0, 0.1) 100%);
    color: #856404;
    box-shadow: 0 4px 15px rgba(255, 199, 0, 0.3);
}

.alert-icon i {
    font-size: 1.5rem;
}

.alert-title {
    color: var(--primary-orange);
    font-weight: 600;
    margin-bottom: 0.5rem;
}

.alert-guest .alert-title {
    color: #856404;
}

.alert-text {
    color: var(--text-muted);
    margin-bottom: 0;
    font-size: 0.95rem;
}

/* Search Card */
.search-card {
    margin-bottom: 3rem;
}

.search-content {
    padding: 3rem;
    text-align: center;
}

.search-header {
    margin-bottom: 3rem;
}

.search-title {
    color: var(--text-dark);
    font-weight: 600;
    font-size: 2rem;
    margin-bottom: 1rem;
}

.search-title i {
    color: var(--primary-orange);
}

.search-subtitle {
    color: var(--text-muted);
    font-size: 1.2rem;
    margin-bottom: 0;
}

.search-form {
    max-width: 600px;
    margin: 0 auto;
}

.search-input-wrapper {
    position: relative;
    margin-bottom: 1rem;
}

.input-icon {
    position: absolute;
    left: 1.5rem;
    top: 50%;
    transform: translateY(-50%);
    color: var(--primary-orange);
    font-size: 1.2rem;
    z-index: 2;
}

.modern-input {
    width: 100%;
    padding: 1.2rem 1.5rem 1.2rem 4rem;
    border: 2px solid rgba(255, 120, 73, 0.1);
    border-radius: 16px;
    font-size: 1.1rem;
    font-weight: 500;
    background: rgba(255, 255, 255, 0.8);
    backdrop-filter: blur(10px);
    transition: all 0.3s ease;
    color: var(--text-dark);
}

.modern-input:focus {
    outline: none;
    border-color: var(--primary-orange);
    background: rgba(255, 255, 255, 0.95);
    box-shadow: 0 0 0 4px rgba(255, 120, 73, 0.1);
    transform: translateY(-2px);
}

.input-help {
    color: var(--text-muted);
    font-size: 0.9rem;
    margin-bottom: 2rem;
}

.input-help i {
    color: var(--primary-orange);
}

.modern-btn {
    background: var(--gradient-primary);
    border: none;
    color: white;
    padding: 1.2rem 2.5rem;
    border-radius: 16px;
    font-weight: 600;
    font-size: 1.1rem;
    transition: all 0.3s ease;
    position: relative;
    overflow: hidden;
    min-width: 180px;
    cursor: pointer;
}

.search-btn {
    margin-bottom: 2rem;
}

.btn-content {
    position: relative;
    z-index: 2;
}

.btn-shine {
    position: absolute;
    top: 0;
    left: -100%;
    width: 100%;
    height: 100%;
    background: linear-gradient(90deg, transparent, rgba(255,255,255,0.2), transparent);
    transition: left 0.5s;
    z-index: 1;
}

.modern-btn:hover {
    transform: translateY(-3px);
    box-shadow: 0 15px 35px rgba(255, 120, 73, 0.4);
}

.modern-btn:hover .btn-shine {
    left: 100%;
}

.modern-btn:active {
    transform: translateY(-1px);
}

.modern-btn:disabled {
    opacity: 0.7;
    cursor: not-allowed;
    transform: none;
}

.quick-action {
    margin-top: 1rem;
}

.modern-btn-outline {
    background: rgba(255, 120, 73, 0.08);
    border: 2px solid rgba(255, 120, 73, 0.3);
    color: var(--primary-orange);
    border-radius: 16px;
    padding: 0.8rem 1.5rem;
    font-weight: 600;
    text-decoration: none;
    transition: all 0.3s ease;
    display: inline-block;
}

.modern-btn-outline:hover {
    background: var(--gradient-primary);
    border-color: var(--primary-orange);
    color: white;
    transform: translateY(-2px);
    box-shadow: 0 8px 25px rgba(255, 120, 73, 0.3);
}

/* Complain Header */
.complain-header {
    position: relative;
    padding: 2.5rem;
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
    white-space: nowrap;
}

.complain-header-content {
    position: relative;
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

.complain-info {
    padding-top: 0.5rem;
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
    word-wrap: break-word;
}

.complain-badges {
    margin-bottom: 1rem;
}

.badge-type, .badge-user {
    display: inline-block;
    padding: 0.4rem 0.8rem;
    border-radius: 12px;
    font-size: 0.85rem;
    margin-right: 0.5rem;
    margin-bottom: 0.5rem;
    font-weight: 500;
}

.badge-type {
    background: linear-gradient(135deg, rgba(255, 120, 73, 0.15) 0%, rgba(255, 107, 53, 0.15) 100%);
    color: var(--primary-orange);
    border: 1px solid rgba(255, 120, 73, 0.3);
}

.badge-user {
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
    white-space: nowrap;
    margin-top: 0.5rem;
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

.details-header {
    margin-bottom: 1.5rem;
}

.details-header h5 {
    color: var(--primary-orange);
    font-weight: 600;
    margin-bottom: 0;
}

.details-header i {
    color: var(--primary-orange);
}

.details-card {
    background: var(--gradient-light);
    border-radius: 16px;
    padding: 2rem;
    margin-top: 1rem;
    border-left: 4px solid var(--primary-orange);
}

.detail-item {
    margin-bottom: 1.5rem;
}

.detail-item.full-width {
    grid-column: 1 / -1;
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

/* Images Gallery (แก้ไขแล้ว) */
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
    background: linear-gradient(135deg, rgba(255, 120, 73, 0.9) 0%, rgba(229, 90, 43, 0.9) 100%);
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

.image-overlay span {
    font-size: 0.9rem;
}

.image-wrapper:hover .image-overlay {
    opacity: 1;
}

.image-wrapper:hover .complain-image {
    transform: scale(1.1);
}

.image-caption {
    padding: 0.8rem;
    text-align: center;
    background: linear-gradient(135deg, #fff7f0 0%, #ffffff 100%);
    border-radius: 0 0 12px 12px;
}

.image-caption small {
    color: var(--primary-orange);
    font-weight: 600;
}

/* Image Modal (แก้ไขแล้ว) */
.image-modal {
    background: rgba(0, 0, 0, 0.95);
    border: none;
}

.image-modal .modal-header {
    background: rgba(0, 0, 0, 0.8);
    border-bottom: 1px solid rgba(255, 255, 255, 0.1);
    color: white;
}

.image-modal .modal-footer {
    background: rgba(0, 0, 0, 0.8);
    border-top: 1px solid rgba(255, 255, 255, 0.1);
}

.image-container {
    max-height: 70vh;
    display: flex;
    align-items: center;
    justify-content: center;
    background: rgba(0, 0, 0, 0.9);
}

.modal-image {
    max-width: 100%;
    max-height: 70vh;
    object-fit: contain;
    border-radius: 8px;
    box-shadow: 0 8px 32px rgba(0, 0, 0, 0.5);
}

/* Timeline Styles */
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

.timeline-header i {
    color: var(--primary-orange);
}

.modern-timeline {
    position: relative;
}

.timeline-item {
    position: relative;
    padding-left: 4rem;
    margin-bottom: 2.5rem;
    opacity: 0;
    transform: translateX(-20px);
    animation: slideInTimeline 0.6s ease forwards;
}

.timeline-item:nth-child(2) { animation-delay: 0.1s; }
.timeline-item:nth-child(3) { animation-delay: 0.2s; }
.timeline-item:nth-child(4) { animation-delay: 0.3s; }
.timeline-item:nth-child(5) { animation-delay: 0.4s; }

.timeline-dot {
    position: absolute;
    left: 0;
    top: 0.5rem;
    width: 2.5rem;
    height: 2.5rem;
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    color: white;
    font-size: 0.9rem;
    box-shadow: 0 0 0 4px rgba(255, 255, 255, 1), 0 4px 15px rgba(0, 0, 0, 0.2);
    z-index: 2;
}

.timeline-latest .timeline-dot {
    animation: pulse 2s infinite;
}

.timeline-line {
    position: absolute;
    left: 1.15rem;
    top: 3rem;
    bottom: -2.5rem;
    width: 2px;
    background: linear-gradient(to bottom, rgba(255, 120, 73, 0.3), #e9ecef);
}

.timeline-content {
    background: rgba(255, 255, 255, 0.8);
    backdrop-filter: blur(10px);
    border-radius: 16px;
    padding: 1.5rem;
    border: 1px solid rgba(255, 120, 73, 0.1);
    box-shadow: 0 4px 15px rgba(255, 120, 73, 0.05);
}

.timeline-header-item {
    display: flex;
    justify-content: between;
    align-items: start;
    margin-bottom: 1rem;
    flex-wrap: wrap;
    gap: 1rem;
}

.timeline-status {
    font-weight: 600;
    margin-bottom: 0;
    flex-grow: 1;
}

.latest-badge {
    background: var(--gradient-primary);
    color: white;
    border-radius: 12px;
    font-size: 0.7rem;
    padding: 0.3rem 0.6rem;
    margin-left: 0.5rem;
    font-weight: 500;
}

.timeline-date {
    color: var(--text-muted);
    font-size: 0.85rem;
}

.timeline-date i {
    color: var(--primary-orange);
}

.timeline-comment {
    background: var(--gradient-light);
    border-radius: 12px;
    padding: 1rem;
    margin-bottom: 0.5rem;
    border-left: 4px solid var(--primary-orange);
    border: 1px solid rgba(255, 120, 73, 0.1);
}

.timeline-comment i {
    color: var(--primary-orange);
}

.timeline-by {
    color: var(--text-muted);
    font-size: 0.85rem;
}

.timeline-by i {
    color: var(--primary-orange);
}

/* Modal Styles */
.modern-modal {
    border: none;
    border-radius: 24px;
    box-shadow: 0 25px 50px rgba(0, 0, 0, 0.15);
    overflow: hidden;
    backdrop-filter: blur(20px);
}

.modern-modal-header {
    border-radius: 24px 24px 0 0;
    border-bottom: 1px solid rgba(255, 120, 73, 0.1);
    backdrop-filter: blur(10px);
    padding: 1.5rem;
}

.error-header {
    background: linear-gradient(135deg, rgba(220, 53, 69, 0.1) 0%, rgba(220, 53, 69, 0.05) 100%);
    color: #dc3545;
}

.modern-modal-header .modal-title {
    font-weight: 600;
    width: 100%;
    text-align: center;
}

.modern-modal-body {
    padding: 2.5rem;
    background: var(--gradient-light);
}

.error-animation {
    margin-bottom: 1.5rem;
}

.error-icon {
    width: 80px;
    height: 80px;
    margin: 0 auto;
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 2.5rem;
    background: linear-gradient(135deg, rgba(220, 53, 69, 0.15) 0%, rgba(220, 53, 69, 0.1) 100%);
    color: #dc3545;
    box-shadow: 0 8px 25px rgba(220, 53, 69, 0.3);
}

.error-title {
    color: #dc3545;
    font-weight: 600;
}

.error-message {
    color: var(--text-muted);
}

.modern-modal-footer {
    background: var(--gradient-light);
    border-top: 1px solid rgba(255, 120, 73, 0.1);
    padding: 1.5rem;
    justify-content: center;
}

.modern-btn-primary {
    background: var(--gradient-primary);
    border: none;
    color: white;
    border-radius: 16px;
    padding: 0.8rem 2rem;
    font-weight: 600;
    box-shadow: 0 6px 20px rgba(255, 120, 73, 0.4);
}

.modern-btn-danger {
    background: linear-gradient(135deg, #dc3545 0%, #c82333 100%);
    border: none;
    color: white;
    border-radius: 16px;
    padding: 0.8rem 2rem;
    font-weight: 600;
}

/* Animations */
@keyframes slideInTimeline {
    to {
        opacity: 1;
        transform: translateX(0);
    }
}

@keyframes pulse {
    0% { box-shadow: 0 0 0 0 rgba(255, 120, 73, 0.7), 0 0 0 4px rgba(255, 255, 255, 1), 0 4px 15px rgba(0, 0, 0, 0.2); }
    70% { box-shadow: 0 0 0 10px rgba(255, 120, 73, 0), 0 0 0 4px rgba(255, 255, 255, 1), 0 4px 15px rgba(0, 0, 0, 0.2); }
    100% { box-shadow: 0 0 0 0 rgba(255, 120, 73, 0), 0 0 0 4px rgba(255, 255, 255, 1), 0 4px 15px rgba(0, 0, 0, 0.2); }
}

/* Modal animations */
.modal.fade .modal-dialog {
    transform: scale(0.8) translateY(-50px);
    transition: all 0.3s ease;
}

.modal.show .modal-dialog {
    transform: scale(1) translateY(0);
}

/* Responsive Design */
@media (max-width: 768px) {
    .font-pages-head {
        font-size: 2.2rem;
    }
    
    .search-content {
        padding: 2rem;
    }
    
    .search-title {
        font-size: 1.8rem;
    }
    
    .search-subtitle {
        font-size: 1.1rem;
    }
    
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
    
    .complain-topic {
        font-size: 1.3rem;
    }
    
    .complain-info .d-flex {
        flex-direction: column;
        align-items: start !important;
    }
    
    .copy-btn {
        margin-top: 1rem;
        align-self: flex-start;
    }
    
    .timeline-item {
        padding-left: 3rem;
    }
    
    .timeline-dot {
        width: 2rem;
        height: 2rem;
        font-size: 0.8rem;
    }
    
    .timeline-line {
        left: 0.9rem;
    }
    
    .timeline-header-item {
        flex-direction: column;
        align-items: start;
        gap: 0.5rem;
    }
    
    .container-pages-news {
        padding: 0 0.5rem;
    }
    
    .modern-card {
        border-radius: 20px;
    }
    
    .details-card {
        padding: 1.5rem;
    }
    
    .timeline-card {
        padding: 2rem;
    }

    .images-gallery .col-lg-3,
    .images-gallery .col-md-4 {
        width: 50% !important;
    }
    
    .image-overlay {
        opacity: 0.7;
    }
    
    .image-overlay i {
        font-size: 1.5rem;
    }
    
    .image-overlay span {
        font-size: 0.8rem;
    }
}

@media (max-width: 576px) {
    .font-pages-head {
        font-size: 2rem;
    }
    
    .search-content {
        padding: 1.5rem;
    }
    
    .modern-input {
        padding: 1rem 1rem 1rem 3.5rem;
        font-size: 1rem;
    }
    
    .input-icon {
        left: 1rem;
    }
    
    .modern-btn {
        padding: 1rem 2rem;
        font-size: 1rem;
        min-width: 150px;
    }
    
    .complain-header {
        padding: 1.5rem;
    }
    
    .timeline-card {
        padding: 1.5rem;
    }
    
    .timeline-content {
        padding: 1rem;
    }
    
    .details-card {
        padding: 1rem;
    }

    .images-gallery .col-sm-6 {
        width: 100% !important;
    }
    
    .image-container {
        max-height: 60vh;
    }
    
    .modal-image {
        max-height: 60vh;
    }
}

/* Print Styles */
@media print {
    .modern-card::before,
    .header-decoration,
    .btn-shine,
    .modern-btn,
    .copy-btn {
        display: none !important;
    }
    
    .modern-card {
        box-shadow: none;
        border: 1px solid #ccc;
    }
    
    .timeline-dot {
        box-shadow: none;
        border: 2px solid #ccc;
    }
}
</style>

<!-- Font Awesome และ Bootstrap -->
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">

<!-- JavaScript (แก้ไขแล้ว) -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

<script>
    // ตัวแปร Global สำหรับตรวจสอบสถานะ login
const isUserLoggedIn = typeof window.isUserLoggedIn !== 'undefined' ? window.isUserLoggedIn : false;
const userInfo = typeof window.userInfo !== 'undefined' ? window.userInfo : null;

// *** เพิ่ม: Console log เริ่มต้น ***
console.log('🔧 JavaScript loaded successfully');
console.log('👤 User logged in:', isUserLoggedIn);

// *** เพิ่ม: Debug reCAPTCHA variables ตั้งแต่เริ่มต้น ***
console.log('🔑 Initial reCAPTCHA check:');
console.log('- RECAPTCHA_SITE_KEY:', typeof window.RECAPTCHA_SITE_KEY !== 'undefined' ? window.RECAPTCHA_SITE_KEY : 'UNDEFINED');
console.log('- recaptchaReady:', typeof window.recaptchaReady !== 'undefined' ? window.recaptchaReady : 'UNDEFINED');
console.log('- SKIP_RECAPTCHA_FOR_DEV:', typeof window.SKIP_RECAPTCHA_FOR_DEV !== 'undefined' ? window.SKIP_RECAPTCHA_FOR_DEV : 'UNDEFINED');
console.log('- grecaptcha available:', typeof grecaptcha !== 'undefined');

// ฟังก์ชันคัดลอกหมายเลขร้องเรียน
function copyComplainId(complainId) {
    if (navigator.clipboard) {
        navigator.clipboard.writeText(complainId).then(() => {
            Swal.fire({
                icon: 'success',
                title: 'คัดลอกสำเร็จ',
                text: `คัดลอกหมายเลขร้องเรียน ${complainId} แล้ว`,
                timer: 2000,
                showConfirmButton: false,
                toast: true,
                position: 'top-end',
                background: 'linear-gradient(135deg, #fff7f0 0%, #ffeee8 100%)',
                color: '#e55a2b'
            });
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
    textArea.focus();
    textArea.select();
    try {
        document.execCommand('copy');
        Swal.fire({
            icon: 'success',
            title: 'คัดลอกสำเร็จ',
            text: `คัดลอกหมายเลขร้องเรียน ${text} แล้ว`,
            timer: 2000,
            showConfirmButton: false,
            toast: true,
            position: 'top-end',
            background: 'linear-gradient(135deg, #fff7f0 0%, #ffeee8 100%)',
            color: '#e55a2b'
        });
    } catch (err) {
        Swal.fire({
            icon: 'error',
            title: 'ไม่สามารถคัดลอกได้',
            text: 'กรุณาคัดลอกหมายเลขด้วยตนเอง',
            confirmButtonColor: '#e55a2b'
        });
    }
    document.body.removeChild(textArea);
}

// ฟังก์ชันเปิด Modal รูปภาพ
function openImageModal(imageUrl, imageTitle) {
    try {
        if (typeof bootstrap === 'undefined') {
            window.open(imageUrl, '_blank');
            return;
        }
        
        const modal = document.getElementById('imageModal');
        const modalImage = document.getElementById('modalImage');
        const modalTitle = document.getElementById('imageModalLabel');
        const downloadBtn = document.getElementById('downloadImageBtn');
        
        if (!modal || !modalImage || !modalTitle || !downloadBtn) {
            window.open(imageUrl, '_blank');
            return;
        }
        
        modalImage.src = imageUrl;
        modalImage.alt = imageTitle;
        modalTitle.innerHTML = `<i class="fas fa-image me-2"></i>${imageTitle}`;
        downloadBtn.href = imageUrl;
        
        modalImage.style.opacity = '0.5';
        
        modalImage.onload = function() {
            this.style.opacity = '1';
        };
        
        modalImage.onerror = function() {
            this.style.opacity = '1';
            Swal.fire({
                icon: 'error',
                title: 'ไม่สามารถโหลดรูปภาพได้',
                text: 'กรุณาลองใหม่อีกครั้ง',
                confirmButtonColor: '#e55a2b'
            });
        };
        
        const imageModal = new bootstrap.Modal(modal, {
            keyboard: true,
            focus: true
        });
        
        imageModal.show();
        
    } catch (error) {
        window.open(imageUrl, '_blank');
    }
}

// เริ่มต้นระบบรูปภาพ
function initializeImageGallery() {
    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', setupImageClickHandlers);
    } else {
        setupImageClickHandlers();
    }
}

function setupImageClickHandlers() {
    document.addEventListener('click', function(event) {
        const imageWrapper = event.target.closest('.image-wrapper');
        
        if (imageWrapper) {
            event.preventDefault();
            event.stopPropagation();
            
            const imageUrl = imageWrapper.getAttribute('data-image-url');
            const imageTitle = imageWrapper.getAttribute('data-image-title');
            
            if (imageUrl && imageTitle) {
                openImageModal(imageUrl, imageTitle);
            } else {
                Swal.fire({
                    icon: 'error',
                    title: 'ข้อผิดพลาด',
                    text: 'ไม่พบข้อมูลรูปภาพ',
                    confirmButtonColor: '#e55a2b'
                });
            }
        }
    });
}

// ตั้งค่า Keyboard Navigation สำหรับ Modal
function setupImageModalKeyboardNavigation() {
    document.addEventListener('keydown', function(event) {
        const imageModal = document.getElementById('imageModal');
        const isModalOpen = imageModal && imageModal.classList.contains('show');
        
        if (!isModalOpen) return;
        
        switch (event.key) {
            case 'Escape':
                const modal = bootstrap.Modal.getInstance(imageModal);
                if (modal) modal.hide();
                break;
            case 'd':
            case 'D':
                const downloadBtn = document.getElementById('downloadImageBtn');
                if (downloadBtn && downloadBtn.href) {
                    downloadBtn.click();
                }
                break;
        }
    });
}

// จัดการ Form Submission พร้อม reCAPTCHA
function setupFormHandlers() {
    console.log('🔧 Setting up form handlers...');
    
    const form = document.getElementById('followComplainForm');
    if (form) {
        console.log('✅ Form found: followComplainForm');
        
        form.addEventListener('submit', function(e) {
            e.preventDefault();
            console.log('📝 Form submitted');
            
            const complainId = document.getElementById('complainIdInput').value.trim();
            
            if (!complainId) {
                console.log('⚠️ No complaint ID provided');
                Swal.fire({
                    icon: 'warning',
                    title: 'กรุณากรอกหมายเลขร้องเรียน',
                    text: 'โปรดระบุหมายเลขร้องเรียนที่ต้องการค้นหา',
                    confirmButtonColor: '#e55a2b'
                });
                return;
            }
            
            console.log('🔍 Searching for complaint ID:', complainId);
            
            const searchBtn = document.getElementById('searchBtn');
            const originalContent = searchBtn.innerHTML;
            searchBtn.disabled = true;
            searchBtn.innerHTML = '<span class="btn-content"><i class="fas fa-spinner fa-spin me-2"></i>กำลังค้นหา...</span>';
            
            // *** เพิ่ม: Debug reCAPTCHA status แบบละเอียด ***
            console.log('🔍 Checking reCAPTCHA status...');
            console.log('- RECAPTCHA_SITE_KEY:', window.RECAPTCHA_SITE_KEY);
            console.log('- recaptchaReady:', window.recaptchaReady);
            console.log('- SKIP_RECAPTCHA_FOR_DEV:', window.SKIP_RECAPTCHA_FOR_DEV);
            console.log('- grecaptcha available:', typeof grecaptcha !== 'undefined');
            
            // *** เพิ่ม: ตรวจสอบเงื่อนไข reCAPTCHA แบบละเอียด ***
            const hasRecaptchaKey = window.RECAPTCHA_SITE_KEY && window.RECAPTCHA_SITE_KEY !== '';
            const isRecaptchaReady = window.recaptchaReady === true;
            const isNotSkipDev = !window.SKIP_RECAPTCHA_FOR_DEV;
            const isGrecaptchaAvailable = typeof grecaptcha !== 'undefined';
            
            console.log('🔍 reCAPTCHA condition check:');
            console.log('- hasRecaptchaKey:', hasRecaptchaKey);
            console.log('- isRecaptchaReady:', isRecaptchaReady);
            console.log('- isNotSkipDev:', isNotSkipDev);
            console.log('- isGrecaptchaAvailable:', isGrecaptchaAvailable);
            
            const shouldUseRecaptcha = hasRecaptchaKey && isRecaptchaReady && isNotSkipDev && isGrecaptchaAvailable;
            console.log('🔍 Should use reCAPTCHA:', shouldUseRecaptcha);
            
            // ตรวจสอบว่ามี reCAPTCHA หรือไม่
            if (shouldUseRecaptcha) {
                console.log('🛡️ Executing reCAPTCHA...');
                
                grecaptcha.ready(function() {
                    console.log('🔧 grecaptcha.ready() called');
                    
                    grecaptcha.execute(window.RECAPTCHA_SITE_KEY, {
                        action: 'follow_complain_search'
                    }).then(function(token) {
                        console.log('✅ reCAPTCHA token received:', token.substring(0, 50) + '...');
                        console.log('📏 Token length:', token.length);
                        
                        const formData = new FormData();
                        formData.append('complain_id', complainId);
                        formData.append('g-recaptcha-response', token);
                        formData.append('recaptcha_action', 'follow_complain_search');
                        formData.append('recaptcha_source', 'follow_complain_form');
                        formData.append('user_type_detected', isUserLoggedIn ? 'member' : 'guest');
                        formData.append('ajax_request', '1');
                        formData.append('client_timestamp', new Date().toISOString());
                        formData.append('user_agent_info', navigator.userAgent);
                        formData.append('is_anonymous', '0');
                        
                        console.log('📤 Submitting with reCAPTCHA token...');
                        console.log('📦 FormData contents:');
                        for (let [key, value] of formData.entries()) {
                            if (key === 'g-recaptcha-response') {
                                console.log('- ' + key + ':', value.substring(0, 50) + '...');
                            } else {
                                console.log('- ' + key + ':', value);
                            }
                        }
                        
                        submitFormWithRecaptcha(form, formData, searchBtn, originalContent);
                    }).catch(function(error) {
                        console.error('❌ reCAPTCHA execution failed:', error);
                        console.log('🔄 Falling back to submission without reCAPTCHA');
                        submitFormWithoutRecaptcha(form, complainId, searchBtn, originalContent);
                    });
                });
            } else {
                console.log('⚠️ reCAPTCHA not available, submitting without verification');
                console.log('📋 Reasons breakdown:');
                console.log('- SITE_KEY exists:', !!window.RECAPTCHA_SITE_KEY);
                console.log('- reCAPTCHA ready:', !!window.recaptchaReady);
                console.log('- Skip dev mode:', !!window.SKIP_RECAPTCHA_FOR_DEV);
                console.log('- grecaptcha available:', typeof grecaptcha !== 'undefined');
                
                submitFormWithoutRecaptcha(form, complainId, searchBtn, originalContent);
            }
        });
        
        console.log('✅ Form handlers set up successfully');
    } else {
        console.error('❌ Form not found: followComplainForm');
    }
}

// ฟังก์ชันส่งข้อมูลพร้อม reCAPTCHA
function submitFormWithRecaptcha(form, formData, searchBtn, originalContent) {
    fetch(form.action, {
        method: 'POST',
        body: formData
    })
    .then(response => {
        if (!response.ok) {
            throw new Error('Network response was not ok');
        }
        return response.text();
    })
    .then(data => {
        try {
            const jsonResponse = JSON.parse(data);
            handleJsonResponse(jsonResponse, searchBtn, originalContent);
        } catch (e) {
            if (data.includes('<!DOCTYPE html>')) {
                const blob = new Blob([data], { type: 'text/html' });
                const url = URL.createObjectURL(blob);
                window.location.href = url;
            } else {
                window.location.reload();
            }
        }
    })
    .catch(error => {
        restoreButton(searchBtn, originalContent);
        Swal.fire({
            icon: 'error',
            title: 'เกิดข้อผิดพลาด',
            text: 'ไม่สามารถติดต่อเซิร์ฟเวอร์ได้ กรุณาลองใหม่อีกครั้ง',
            confirmButtonColor: '#e55a2b'
        });
    });
}

// ฟังก์ชันส่งข้อมูลแบบปกติ
function submitFormWithoutRecaptcha(form, complainId, searchBtn, originalContent) {
    const formData = new FormData();
    formData.append('complain_id', complainId);
    formData.append('dev_mode', '1');
    
    fetch(form.action, {
        method: 'POST',
        body: formData
    })
    .then(response => response.text())
    .then(data => {
        if (data.includes('<!DOCTYPE html>')) {
            const blob = new Blob([data], { type: 'text/html' });
            const url = URL.createObjectURL(blob);
            window.location.href = url;
        } else {
            window.location.reload();
        }
    })
    .catch(error => {
        restoreButton(searchBtn, originalContent);
        Swal.fire({
            icon: 'error',
            title: 'เกิดข้อผิดพลาด',
            text: 'ไม่สามารถติดต่อเซิร์ฟเวอร์ได้ กรุณาลองใหม่อีกครั้ง',
            confirmButtonColor: '#e55a2b'
        });
    });
}

// จัดการ JSON Response
function handleJsonResponse(jsonResponse, searchBtn, originalContent) {
    restoreButton(searchBtn, originalContent);
    
    if (jsonResponse.success) {
        Swal.fire({
            icon: 'success',
            title: 'พบข้อมูลเรียบร้อย',
            text: jsonResponse.message,
            confirmButtonColor: '#00B73E'
        }).then(() => {
            if (jsonResponse.redirect_url) {
                window.location.href = jsonResponse.redirect_url;
            } else {
                window.location.reload();
            }
        });
    } else {
        let errorMessage = jsonResponse.message || 'เกิดข้อผิดพลาดไม่ทราบสาเหตุ';
        
        if (jsonResponse.error_type === 'recaptcha_failed') {
            errorMessage = 'การยืนยันความปลอดภัยไม่ผ่าน กรุณาลองใหม่อีกครั้ง';
        } else if (jsonResponse.error_type === 'recaptcha_missing') {
            errorMessage = 'ไม่พบข้อมูลการยืนยันความปลอดภัย';
        }
        
        Swal.fire({
            icon: 'error',
            title: 'ไม่พบข้อมูล',
            text: errorMessage,
            confirmButtonColor: '#e55a2b'
        });
    }
}

// ฟังก์ชันคืนค่าปุ่มเป็นสถานะเดิม
function restoreButton(searchBtn, originalContent) {
    if (searchBtn) {
        searchBtn.disabled = false;
        searchBtn.innerHTML = originalContent;
    }
}

// จำกัดการกรอกเฉพาะตัวเลข
function setupInputValidation() {
    const input = document.getElementById('complainIdInput');
    if (input) {
        input.addEventListener('input', function(e) {
            this.value = this.value.replace(/[^0-9]/g, '');
            
            if (this.value.length > 10) {
                this.value = this.value.slice(0, 10);
            }
        });
        
        input.addEventListener('keypress', function(e) {
            if (e.key === 'Enter') {
                e.preventDefault();
                document.getElementById('followComplainForm').dispatchEvent(new Event('submit'));
            }
        });
    }
}

// เริ่มต้นหน้าเว็บ
function initializePage() {
    console.log('🚀 Initialize Page Starting...');
    
    setupFormHandlers();
    setupInputValidation();
    setupImageModalKeyboardNavigation();
    initializeImageGallery();
    
    document.documentElement.style.scrollBehavior = 'smooth';
    
    const cards = document.querySelectorAll('.modern-card');
    cards.forEach((card, index) => {
        card.style.opacity = '0';
        card.style.transform = 'translateY(20px)';
        
        setTimeout(() => {
            card.style.transition = 'all 0.6s ease';
            card.style.opacity = '1';
            card.style.transform = 'translateY(0)';
        }, index * 100);
    });
    
    const inputField = document.getElementById('complainIdInput');
    if (inputField && !inputField.value) {
        inputField.focus();
    }
    
    console.log('✅ Page initialized successfully');
}

// Error handling และ protection
window.addEventListener('error', function(event) {
    if (event.message && (
        event.message.includes('initializeActivitySlider') ||
        event.message.includes('Cannot read properties of null') ||
        event.message.includes('Cannot read properties of undefined') ||
        event.message.includes('Service Slider Error') ||
        event.message.includes('wrapText') ||
        event.message.includes('animateText')
    )) {
        event.preventDefault();
        return true;
    }
});

// ป้องกัน console error จาก missing functions
window.initializeActivitySlider = window.initializeActivitySlider || function() { 
    console.log('initializeActivitySlider function called but not implemented'); 
};

// ป้องกัน error จาก extension
window.addEventListener('unhandledrejection', function(event) {
    if (event.reason && event.reason.message && 
        event.reason.message.includes('message channel')) {
        event.preventDefault();
    }
});

// ตรวจสอบการโหลด reCAPTCHA
document.addEventListener('DOMContentLoaded', function() {
    console.log('🔧 DOM Content Loaded');
    
    if (window.RECAPTCHA_SITE_KEY && !window.recaptchaReady) {
        console.log('⏳ Waiting for reCAPTCHA to load...');
        
        let checkInterval = setInterval(function() {
            if (window.recaptchaReady) {
                console.log('✅ reCAPTCHA is now ready');
                clearInterval(checkInterval);
            }
        }, 100);
        
        setTimeout(function() {
            if (!window.recaptchaReady) {
                console.log('⚠️ reCAPTCHA timeout after 10 seconds');
                clearInterval(checkInterval);
            }
        }, 10000);
    }
    
    // Initialize page
    initializePage();
});

// เริ่มต้นเมื่อ DOM พร้อม (fallback)
if (document.readyState === 'loading') {
    // Already handled above
} else {
    initializePage();
}
</script>