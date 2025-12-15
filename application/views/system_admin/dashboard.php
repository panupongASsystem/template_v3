<!DOCTYPE html>
<html lang="th">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard</title>
    <script src="https://cdn.jsdelivr.net/npm/apexcharts"></script>
</head>
<body>
<!-- ========== VISITOR STATS & RECENT ACTIVITY SECTION ========== -->
<div class="row">
    <!-- Visitor Stats Card -->
    <div class="col-xl-6 col-md-6">
        <!-- สำหรับจำนวนผู้เข้าชม -->
        <div class="card shadow mb-4">
            <div class="py-3 d-flex flex-row align-items-center justify-content-between">
                <div class="d-flex align-items-center ml-4">
                    <div class="modern-icon-wrapper visitors">
                        <i class="fas fa-eye"></i>
                    </div>
                    <h5 class="m-0 ml-3 font-weight-bold text-primary-soft">
                        จำนวนผู้เข้าชม ( ทั้งหมด )
                    </h5>
                </div>
            </div>
            <div class="card-body">
                <?php foreach ($most_viewed_tables as $table) : ?>
                    <div class="visitor-progress progress mb-3">
                        <div class="progress-bar views" style="width: 100%;">
                            <div class="progress-text">
                                <span class="member-name"><?= htmlspecialchars($table->table_name); ?></span>
                                <span class="member-count"><?= number_format($table->total_views ?? 0); ?></span>
                            </div>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
        </div>

        <!-- สำหรับจำนวนกระทู้ -->
        <div class="card shadow mb-4">
            <div class="py-3 d-flex flex-row align-items-center justify-content-between">
                <div class="d-flex align-items-center ml-4">
                    <div class="modern-icon-wrapper posts">
                        <i class="fas fa-file-alt"></i>
                    </div>
                    <h5 class="m-0 ml-3 font-weight-bold text-primary-soft">
                        จำนวนกระทู้ ( ทั้งหมด )
                    </h5>
                </div>
            </div>
            <div class="card-body">
                <?php foreach ($most_post_tables as $table) : ?>
                    <div class="visitor-progress progress mb-3">
                        <div class="progress-bar posts" style="width: 100%;">
                            <div class="progress-text">
                                <span class="member-name"><?= htmlspecialchars($table->table_name); ?></span>
                                <span class="member-count"><?= number_format($table->total_posts ?? 0); ?></span>
                            </div>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
        </div>
    </div>

    <!-- Recent Activity Card -->
    <div class="col-xl-6 col-md-6 mb-4">
        <div class="card shadow">
            <div class="py-3 d-flex flex-row align-items-center justify-content-between">
                <div class="d-flex align-items-center ml-4">
                    <div class="modern-icon-wrapper activity">
                        <i class="fas fa-history"></i>
                    </div>
                    <h5 class="m-0 ml-3 font-weight-bold text-primary-soft">
                        กิจกรรมล่าสุด
                    </h5>
                </div>
            </div>
            <div class="card-body activity-scroll">
                <?php if (!empty($recent_activities)): ?>
                    <div class="timeline">
                        <?php foreach ($recent_activities as $activity): ?>
                            <div class="timeline-item mb-3">
                                <div class="d-flex align-items-start">
                                    <div class="timeline-icon mr-3">
                                        <?php 
                                        $icon_class = 'fas fa-circle text-primary';
                                        $bg_color = 'bg-primary';
                                        
                                        switch($activity->action) {
                                            case 'เพิ่ม':
                                                $icon_class = 'fas fa-plus';
                                                $bg_color = 'bg-success';
                                                break;
                                            case 'แก้ไข':
                                                $icon_class = 'fas fa-edit';
                                                $bg_color = 'bg-warning';
                                                break;
                                            case 'ลบ':
                                                $icon_class = 'fas fa-trash';
                                                $bg_color = 'bg-danger';
                                                break;
                                            case 'เข้าชม':
                                                $icon_class = 'fas fa-eye';
                                                $bg_color = 'bg-info';
                                                break;
                                            case 'ดาวน์โหลด':
                                                $icon_class = 'fas fa-download';
                                                $bg_color = 'bg-secondary';
                                                break;
                                            default:
                                                $icon_class = 'fas fa-circle';
                                                $bg_color = 'bg-primary';
                                        }
                                        ?>
                                        <span class="timeline-dot <?= $bg_color; ?> text-white rounded-circle p-2">
                                            <i class="<?= $icon_class; ?> fa-sm"></i>
                                        </span>
                                    </div>
                                    <div class="timeline-content flex-grow-1">
                                        <div class="d-flex justify-content-between align-items-start">
                                            <div>
                                                <h6 class="mb-1 font-weight-bold text-gray-800">
                                                    <?= htmlspecialchars($activity->full_name); ?>
                                                </h6>
                                                <p class="mb-1 text-sm">
                                                    <span class="badge badge-<?= 
                                                        $activity->action == 'เพิ่ม' ? 'success' : 
                                                        ($activity->action == 'แก้ไข' ? 'warning' : 
                                                        ($activity->action == 'ลบ' ? 'danger' : 
                                                        ($activity->action == 'เข้าชม' ? 'info' : 'secondary'))) 
                                                    ?> mr-2">
                                                        <?= $activity->action; ?>
                                                    </span>
                                                    <?= htmlspecialchars($activity->menu); ?>
                                                    <?php if ($activity->item_name): ?>
                                                        : <strong><?= htmlspecialchars($activity->item_name); ?></strong>
                                                    <?php endif; ?>
                                                </p>
                                                <small class="text-muted">
                                                    <i class="fas fa-clock mr-1"></i>
                                                    <?= thai_date_time($activity->created_at); ?>
                                                </small>
                                            </div>
                                            <small class="text-muted">
                                                <?= time_elapsed_string($activity->created_at); ?>
                                            </small>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    </div>
                <?php else: ?>
                    <div class="text-center py-4">
                        <i class="fas fa-history fa-3x text-gray-300 mb-3"></i>
                        <p class="text-muted">ยังไม่มีกิจกรรมในระบบ</p>
                    </div>
                <?php endif; ?>
            </div>
        </div>
        
        <!-- ========== TOP PERFORMERS SECTION ========== -->
        <div class="modern-card">
            <div class="card-glow"></div>
            <div class="card-header-modern">
                <div class="header-left">
                    <div class="title-container">
                        <div class="trophy-icon">
                            <i class="fas fa-trophy"></i>
                        </div>
                        <div class="title-text">
                            <h3>Top 10 Performers</h3>
                            <span class="subtitle">ผู้ที่ใช้งานระบบบุคลากรภายใน 10 อันดับแรก</span>
                        </div>
                    </div>
                </div>
                <div class="header-actions">
                    <div class="time-selector">
                        <button class="time-btn" onclick="selectTimeRange(this, 7)">7 วัน</button>
                        <button class="time-btn" onclick="selectTimeRange(this, 30)">30 วัน</button>
                        <button class="time-btn active" onclick="selectTimeRange(this, 0)">ทั้งหมด</button>
                    </div>
                </div>
            </div>
            
            <div class="card-body-modern">
                <?php if (!empty($top_active_users) && count($top_active_users) > 0): ?>
                    <div class="chart-wrapper">
                        <div id="topActiveUsersChart" class="ultra-chart"></div>
                    </div>
                <?php else: ?>
                    <div class="no-data-state">
                        <div class="no-data-icon">
                            <svg viewBox="0 0 24 24" fill="none">
                                <path d="M3 13V11C3 6.58 6.58 3 11 3H13C17.42 3 21 6.58 21 11V13" stroke="url(#empty-gradient)" stroke-width="2"/>
                                <path d="M8 21L16 21" stroke="url(#empty-gradient)" stroke-width="2"/>
                                <path d="M12 17V21" stroke="url(#empty-gradient)" stroke-width="2"/>
                                <defs>
                                    <linearGradient id="empty-gradient">
                                        <stop offset="0%" style="stop-color:#94A3B8"/>
                                        <stop offset="100%" style="stop-color:#CBD5E1"/>
                                    </linearGradient>
                                </defs>
                            </svg>
                        </div>
                        <h4>ไม่พบข้อมูล</h4>
                        <p>ยังไม่มีกิจกรรมของผู้ใช้ในช่วงเวลานี้</p>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </div>
</div>
    
<!-- ========== POPULAR CATEGORIES SECTION ========== -->
<div class="col-xl-12 col-md-12 mb-4">
    <div class="popular-categories-container">
        <div class="categories-header">
            <div class="header-content">
                <div class="icon-wrapper">
                    <i class="fas fa-fire"></i>
                </div>
                <div class="header-text">
                    <h2>เนื้อหายอดนิยม</h2>
                    <p>สิ่งที่ผู้คนสนใจมากที่สุดในเว็บไซต์</p>
                </div>
            </div>
        </div>

        <div class="categories-grid">
            <?php if (!empty($popular_categories)): ?>
                <?php foreach ($popular_categories as $category_key => $category): ?>
                    <div class="category-card" data-category="<?= $category_key; ?>">
                        <div class="category-header">
                            <div class="category-icon" style="background: linear-gradient(135deg, <?= $category['color']; ?>15, <?= $category['color']; ?>25);">
                                <i class="<?= $category['icon']; ?>" style="color: <?= $category['color']; ?>;"></i>
                            </div>
                            <div class="category-title">
                                <h3><?= $category['title']; ?></h3>
                                <span class="item-count"><?= $category['total_items']; ?> รายการ</span>
                            </div>
                        </div>

                        <div class="category-content">
                            <?php if (!empty($category['items'])): ?>
                                <?php foreach ($category['items'] as $index => $item): ?>
                                    <?php
                                    $name = $item['name'] ?? 'ไม่ระบุชื่อ';
                                    $views = (int)($item['views'] ?? 0);
                                    $img = $item['img'] ?? '';
                                    $src = $item['src'] ?? '';
                                    
                                    // กำหนดโฟลเดอร์รูปภาพตามประเภท
                                    $img_folder = 'docs/img/';
                                    if ($src === 'travel') $img_folder = 'docs/img/';
                                    if ($src === 'news') $img_folder = 'docs/img/';
                                    if ($src === 'activity') $img_folder = 'docs/img/';
                                    
                                    $img_url = $img ? base_url($img_folder . $img) : 'https://images.unsplash.com/photo-1506905925346-21bda4d32df4?q=80&w=300&auto=format&fit=crop';
                                    
                                    // สีของแรงค์
                                    $rank_colors = ['#FFD700', '#C0C0C0', '#CD7F32', '#4A90E2', '#50E3C2'];
                                    $rank_color = $rank_colors[$index] ?? '#6B7280';
                                    ?>
                                    
                                    <div class="popular-item" data-rank="<?= $index + 1; ?>">
                                        <div class="item-rank" style="background: <?= $rank_color; ?>;">
                                            #<?= $index + 1; ?>
                                        </div>
                                        
                                        <div class="item-image">
                                            <img src="<?= htmlspecialchars($img_url); ?>" alt="<?= htmlspecialchars($name); ?>" loading="lazy">
                                            <div class="image-overlay"></div>
                                        </div>
                                        
                                        <div class="item-content">
                                            <h4 class="item-title"><?= htmlspecialchars($name); ?></h4>
                                            <div class="item-stats">
                                                <span class="views-count">
                                                    <i class="fas fa-eye"></i>
                                                    <?= number_format($views); ?>
                                                </span>
                                                <span class="item-type"><?= ucfirst($src); ?></span>
                                            </div>
                                            
                                            <!-- Progress bar แสดงความนิยม -->
                                            <?php 
                                            $max_views = !empty($category['items']) ? max(array_column($category['items'], 'views')) : 1;
                                            $percentage = $max_views > 0 ? round(($views / $max_views) * 100) : 0;
                                            ?>
                                            <div class="popularity-meter">
                                                <div class="meter-fill" style="width: <?= $percentage; ?>%; background: <?= $category['color']; ?>;"></div>
                                            </div>
                                        </div>
                                    </div>
                                <?php endforeach; ?>
                            <?php else: ?>
                                <div class="no-content">
                                    <i class="fas fa-inbox"></i>
                                    <p>ไม่มีข้อมูลในหมวดนี้</p>
                                </div>
                            <?php endif; ?>
                        </div>
                    </div>
                <?php endforeach; ?>
            <?php else: ?>
                <div class="no-categories">
                    <div class="empty-state">
                        <i class="fas fa-search"></i>
                        <h3>ไม่พบข้อมูล</h3>
                        <p>ยังไม่มีเนื้อหายอดนิยมในระบบ</p>
                    </div>
                </div>
            <?php endif; ?>
        </div>
    </div>
</div>
    
<!-- ========== STYLES ========== -->
<style>
/* ========== MODERN ICON WRAPPER STYLES ========== */
.modern-icon-wrapper {
    width: 56px;
    height: 56px;
    border-radius: 18px;
    display: flex;
    align-items: center;
    justify-content: center;
    position: relative;
    transition: all 0.4s cubic-bezier(0.4, 0, 0.2, 1);
}

.modern-icon-wrapper i {
    font-size: 24px;
    color: white;
    position: relative;
    z-index: 1;
}

/* Animation สำหรับ icon */
.modern-icon-wrapper::before {
    content: '';
    position: absolute;
    top: 0;
    left: 0;
    right: 0;
    bottom: 0;
    border-radius: 18px;
    opacity: 0.5;
    animation: pulse-ring 2s ease-in-out infinite;
}

@keyframes pulse-ring {
    0% {
        transform: scale(1);
        opacity: 0.5;
    }
    50% {
        transform: scale(1.1);
        opacity: 0.3;
    }
    100% {
        transform: scale(1);
        opacity: 0.5;
    }
}

/* สี Gradient แต่ละประเภท */

/* จำนวนผู้เข้าชม - สีน้ำเงิน-ม่วง */
.modern-icon-wrapper.visitors {
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    box-shadow: 
        0 12px 24px rgba(102, 126, 234, 0.4),
        0 6px 12px rgba(118, 75, 162, 0.3);
}

.modern-icon-wrapper.visitors::before {
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
}

/* จำนวนกระทู้ - สีเขียว-ฟ้า */
.modern-icon-wrapper.posts {
    background: linear-gradient(135deg, #11998e 0%, #38ef7d 100%);
    box-shadow: 
        0 12px 24px rgba(17, 153, 142, 0.4),
        0 6px 12px rgba(56, 239, 125, 0.3);
}

.modern-icon-wrapper.posts::before {
    background: linear-gradient(135deg, #11998e 0%, #38ef7d 100%);
}

/* กิจกรรมล่าสุด - สีส้ม-แดง */
.modern-icon-wrapper.activity {
    background: linear-gradient(135deg, #FF6B6B 0%, #FF8E53 100%);
    box-shadow: 
        0 12px 24px rgba(255, 107, 107, 0.4),
        0 6px 12px rgba(255, 142, 83, 0.3);
}

.modern-icon-wrapper.activity::before {
    background: linear-gradient(135deg, #FF6B6B 0%, #FF8E53 100%);
}

/* Hover Effect */
.modern-icon-wrapper:hover {
    transform: translateY(-4px) scale(1.05);
}

.modern-icon-wrapper.visitors:hover {
    box-shadow: 
        0 16px 32px rgba(102, 126, 234, 0.5),
        0 8px 16px rgba(118, 75, 162, 0.4);
}

.modern-icon-wrapper.posts:hover {
    box-shadow: 
        0 16px 32px rgba(17, 153, 142, 0.5),
        0 8px 16px rgba(56, 239, 125, 0.4);
}

.modern-icon-wrapper.activity:hover {
    box-shadow: 
        0 16px 32px rgba(255, 107, 107, 0.5),
        0 8px 16px rgba(255, 142, 83, 0.4);
}

/* ========== POPULAR CATEGORIES STYLES ========== */
.popular-categories-container {
    background: linear-gradient(145deg, #ffffff 0%, #fafbfc 100%);
    border-radius: 24px;
    overflow: hidden;
    box-shadow: 
        0 20px 40px -12px rgba(0, 0, 0, 0.1),
        0 0 0 1px rgba(255, 255, 255, 0.8);
    backdrop-filter: blur(20px);
}

.categories-header {
    padding: 32px 32px 24px;
    background: linear-gradient(135deg, rgba(255,255,255,0.95) 0%, rgba(248,250,252,0.9) 100%);
    border-bottom: 1px solid rgba(226, 232, 240, 0.3);
}

.header-content {
    display: flex;
    align-items: center;
    gap: 20px;
}

.icon-wrapper {
    width: 64px;
    height: 64px;
    border-radius: 20px;
    background: linear-gradient(135deg, #FF6B6B 0%, #FF8E53 100%);
    display: flex;
    align-items: center;
    justify-content: center;
    box-shadow: 0 12px 24px rgba(255, 107, 107, 0.3);
    animation: pulse-glow 3s ease-in-out infinite;
}

.icon-wrapper i {
    font-size: 28px;
    color: white;
}

@keyframes pulse-glow {
    0%, 100% { transform: scale(1); box-shadow: 0 12px 24px rgba(255, 107, 107, 0.3); }
    50% { transform: scale(1.05); box-shadow: 0 16px 32px rgba(255, 107, 107, 0.4); }
}

.header-text h2 {
    font-size: 28px;
    font-weight: 800;
    color: var(--primary-soft) !important;
    margin: 0 0 8px 0;
    letter-spacing: -0.02em;
}

.header-text p {
    font-size: 16px;
    color: #6B7280;
    margin: 0;
    font-weight: 500;
}

.categories-grid {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(380px, 1fr));
    gap: 24px;
    padding: 32px;
}

/* Category Card */
.category-card {
    background: linear-gradient(145deg, #ffffff 0%, #fafbfc 100%);
    border-radius: 20px;
    border: 1px solid rgba(226, 232, 240, 0.4);
    overflow: hidden;
    transition: all 0.4s cubic-bezier(0.4, 0, 0.2, 1);
    position: relative;
}

.category-card::before {
    content: '';
    position: absolute;
    top: 0;
    left: 0;
    right: 0;
    bottom: 0;
    background: linear-gradient(145deg, rgba(255,255,255,0.8) 0%, rgba(248,250,252,0.6) 100%);
    opacity: 0;
    transition: opacity 0.3s ease;
    z-index: 0;
}

.category-card:hover {
    transform: translateY(-8px);
    box-shadow: 0 25px 50px rgba(0, 0, 0, 0.15);
}

.category-card:hover::before {
    opacity: 1;
}

.category-header {
    display: flex;
    align-items: center;
    gap: 16px;
    padding: 24px 24px 20px;
    border-bottom: 1px solid rgba(226, 232, 240, 0.3);
    position: relative;
    z-index: 1;
}

.category-icon {
    width: 48px;
    height: 48px;
    border-radius: 16px;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 20px;
}

.category-title h3 {
    font-size: 18px;
    font-weight: 700;
    color: #1F2937;
    margin: 0 0 4px 0;
}

.item-count {
    font-size: 13px;
    color: #6B7280;
    font-weight: 500;
}

.category-content {
    padding: 20px 24px 24px;
    position: relative;
    z-index: 1;
}

/* Popular Item */
.popular-item {
    display: grid;
    grid-template-columns: auto 80px 1fr;
    gap: 16px;
    align-items: center;
    padding: 16px;
    border-radius: 16px;
    background: rgba(255, 255, 255, 0.6);
    border: 1px solid rgba(226, 232, 240, 0.3);
    margin-bottom: 16px;
    transition: all 0.3s ease;
}

.popular-item:hover {
    background: rgba(255, 255, 255, 0.9);
    transform: translateX(8px);
    box-shadow: 0 8px 24px rgba(0, 0, 0, 0.1);
}

.popular-item:last-child {
    margin-bottom: 0;
}

.item-rank {
    width: 32px;
    height: 32px;
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 14px;
    font-weight: 700;
    color: white;
    box-shadow: 0 4px 12px rgba(0, 0, 0, 0.2);
}

.item-image {
    position: relative;
    width: 80px;
    height: 80px;
    border-radius: 12px;
    overflow: hidden;
    box-shadow: 0 4px 12px rgba(0, 0, 0, 0.1);
}

.item-image img {
    width: 100%;
    height: 100%;
    object-fit: cover;
    transition: transform 0.3s ease;
}

.item-image:hover img {
    transform: scale(1.1);
}

.image-overlay {
    position: absolute;
    top: 0;
    left: 0;
    right: 0;
    bottom: 0;
    background: linear-gradient(45deg, rgba(0,0,0,0.1) 0%, transparent 100%);
}

.item-content {
    min-width: 0;
}

.item-title {
    font-size: 16px;
    font-weight: 600;
    color: #1F2937;
    margin: 0 0 8px 0;
    line-height: 1.4;
    display: -webkit-box;
    -webkit-line-clamp: 2;
    -webkit-box-orient: vertical;
    overflow: hidden;
}

.item-stats {
    display: flex;
    align-items: center;
    gap: 12px;
    margin-bottom: 8px;
}

.views-count {
    display: flex;
    align-items: center;
    gap: 6px;
    font-size: 13px;
    color: #6B7280;
    font-weight: 500;
}

.views-count i {
    font-size: 12px;
    color: #9CA3AF;
}

.item-type {
    font-size: 11px;
    padding: 4px 8px;
    border-radius: 8px;
    background: rgba(107, 114, 128, 0.1);
    color: #6B7280;
    font-weight: 600;
    text-transform: uppercase;
    letter-spacing: 0.5px;
}

.popularity-meter {
    height: 4px;
    background: rgba(229, 231, 235, 0.6);
    border-radius: 2px;
    overflow: hidden;
}

.meter-fill {
    height: 100%;
    border-radius: 2px;
    transition: width 0.8s ease;
    background: linear-gradient(90deg, currentColor 0%, rgba(255,255,255,0.3) 100%);
}

/* No Content States */
.no-content, .no-categories {
    text-align: center;
    padding: 40px 20px;
    color: #9CA3AF;
}

.no-content i, .empty-state i {
    font-size: 48px;
    margin-bottom: 16px;
    color: #D1D5DB;
}

.empty-state h3 {
    font-size: 20px;
    font-weight: 600;
    color: #6B7280;
    margin: 0 0 8px 0;
}

.empty-state p, .no-content p {
    font-size: 14px;
    color: #9CA3AF;
    margin: 0;
}

/* ========== TOP PERFORMERS CARD STYLES ========== */
.modern-card {
    position: relative;
    background: linear-gradient(145deg, #ffffff 0%, #fafbfc 50%, #f8faff 100%);
    border-radius: 28px;
    overflow: hidden;
    box-shadow: 
        0 25px 50px -12px rgba(0, 0, 0, 0.08),
        0 0 0 1px rgba(255, 255, 255, 0.7),
        inset 0 1px 0 rgba(255, 255, 255, 0.9);
    backdrop-filter: blur(20px);
    transition: all 0.5s cubic-bezier(0.23, 1, 0.320, 1);
    margin-top: 4%;
}

.card-glow {
    position: absolute;
    top: -2px;
    left: -2px;
    right: -2px;
    bottom: -2px;
    background: linear-gradient(45deg, #A8E6CF, #7FCDCD, #5DADE2, #BB8FCE, #F1948A);
    background-size: 400% 400%;
    border-radius: 30px;
    z-index: -1;
    opacity: 0;
    animation: gradientShift 6s ease infinite;
    transition: opacity 0.3s ease;
}

.modern-card:hover {
    transform: translateY(-12px) scale(1.02);
    box-shadow: 
        0 35px 70px -15px rgba(0, 0, 0, 0.15),
        0 0 0 1px rgba(255, 255, 255, 0.8);
}

.modern-card:hover .card-glow {
    opacity: 0.6;
}

@keyframes gradientShift {
    0%, 100% { background-position: 0% 50%; }
    50% { background-position: 100% 50%; }
}

/* Header */
.card-header-modern {
    padding: 16px 16px 10px;
    display: flex;
    justify-content: space-between;
    align-items: flex-start;
    border-bottom: 1px solid rgba(226, 232, 240, 0.4);
    background: linear-gradient(135deg, rgba(255,255,255,0.9) 0%, rgba(248,250,252,0.8) 100%);
}

.header-left {
    flex: 1;
}

.title-container {
    display: flex;
    align-items: center;
    gap: 10px;
}

.trophy-icon {
    width: 56px;
    height: 56px;
    border-radius: 20px;
    background: linear-gradient(135deg, #A8E6CF 0%, #7FCDCD 100%);
    display: flex;
    align-items: center;
    justify-content: center;
    box-shadow: 
        0 12px 24px rgba(168, 230, 207, 0.4),
        0 6px 12px rgba(127, 205, 205, 0.3);
    animation: float 3s ease-in-out infinite;
}

.trophy-icon i {
    font-size: 28px;
    color: white;
}

@keyframes float {
    0%, 100% { transform: translateY(0px); }
    50% { transform: translateY(-6px); }
}

.title-text h3 {
    font-size: 24px;
    font-weight: 800;
    color: var(--primary-soft) !important;
    margin: 0 0 4px 0;
    letter-spacing: -0.02em;
    background: linear-gradient(135deg, #1F2937 0%, #4B5563 100%);
    background-clip: text;
}

.subtitle {
    font-size: 14px;
    color: #6B7280;
    font-weight: 500;
    letter-spacing: 0.01em;
}

/* Time Selector */
.time-selector {
    display: flex;
    background: rgba(243, 244, 246, 0.8);
    border-radius: 16px;
    padding: 4px;
    gap: 2px;
    backdrop-filter: blur(10px);
    border: 1px solid rgba(255, 255, 255, 0.6);
}

.time-btn {
    padding: 12px 18px;
    border: none;
    border-radius: 12px;
    font-size: 13px;
    font-weight: 600;
    cursor: pointer;
    transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
    background: transparent;
    color: #6B7280;
    position: relative;
    overflow: hidden;
    min-width: 70px;
    z-index: 1;
}

.time-btn::before {
    content: '';
    position: absolute;
    top: 0;
    left: 0;
    right: 0;
    bottom: 0;
    background: linear-gradient(135deg, #A8E6CF 0%, #7FCDCD 100%);
    opacity: 0;
    transition: opacity 0.3s ease;
    border-radius: 12px;
    z-index: -1;
}

.time-btn.active,
.time-btn:hover {
    color: white !important;
    transform: translateY(-1px);
    box-shadow: 0 8px 16px rgba(168, 230, 207, 0.4);
}

.time-btn.active::before,
.time-btn:hover::before {
    opacity: 1;
}

/* Body */
.card-body-modern {
    padding: 12px 16px 16px;
}

.chart-wrapper {
    position: relative;
}

.ultra-chart {
    transition: all 0.4s cubic-bezier(0.4, 0, 0.2, 1);
}

/* No Data State */
.no-data-state {
    text-align: center;
    padding: 80px 20px;
    color: #6B7280;
}

.no-data-icon {
    width: 120px;
    height: 120px;
    margin: 0 auto 24px;
    border-radius: 50%;
    background: linear-gradient(135deg, #F9FAFB 0%, #F3F4F6 100%);
    display: flex;
    align-items: center;
    justify-content: center;
    border: 2px solid rgba(229, 231, 235, 0.8);
}

.no-data-icon svg {
    width: 48px;
    height: 48px;
}

.no-data-state h4 {
    font-size: 20px;
    font-weight: 700;
    color: #374151;
    margin: 0 0 8px 0;
}

.no-data-state p {
    font-size: 15px;
    color: #6B7280;
    margin: 0;
    line-height: 1.5;
}

/* ========== VISITOR STATS STYLES ========== */
.visitor-progress {
    height: 40px !important;
    background-color: #f8f9fa !important;
    border-radius: 10px !important;
    overflow: hidden !important;
    box-shadow: inset 0 2px 4px rgba(0,0,0,0.1) !important;
}

/* สีสำหรับจำนวนผู้เข้าชม (สีน้ำเงิน-ม่วง) */
.visitor-progress .progress-bar.views {
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%) !important;
    height: 100% !important;
    display: flex !important;
    align-items: center !important;
    position: relative !important;
}

/* สีสำหรับจำนวนกระทู้ (สีเขียว-ฟ้า) */
.visitor-progress .progress-bar.posts {
    background: linear-gradient(135deg, #11998e 0%, #38ef7d 100%) !important;
    height: 100% !important;
    display: flex !important;
    align-items: center !important;
    position: relative !important;
}

.progress-text {
    display: flex !important;
    justify-content: space-between !important;
    align-items: center !important;
    width: 100% !important;
    padding: 0 15px !important;
    color: white !important;
    font-weight: 600 !important;
}

.member-name {
    font-size: 14px !important;
}

.member-count {
    font-size: 14px !important;
    font-weight: 700 !important;
}

/* ========== TIMELINE STYLES ========== */
.activity-scroll {
    max-height: 305px;
    overflow-y: auto;
    scrollbar-width: thin;
    scrollbar-color: #cbd5e0 #f7fafc;
}

.activity-scroll::-webkit-scrollbar {
    width: 6px;
}

.activity-scroll::-webkit-scrollbar-track {
    background: #f7fafc;
    border-radius: 3px;
}

.activity-scroll::-webkit-scrollbar-thumb {
    background: #cbd5e0;
    border-radius: 3px;
}

.activity-scroll::-webkit-scrollbar-thumb:hover {
    background: #a0aec0;
}

.timeline {
    position: relative;
}

.timeline-item {
    position: relative;
}

.timeline-dot {
    display: inline-flex;
    align-items: center;
    justify-content: center;
    width: 32px;
    height: 32px;
    min-width: 32px;
    position: relative;
    z-index: 1;
}

.timeline-content {
    padding-left: 10px;
}

.timeline-item:not(:last-child)::after {
    content: '';
    position: absolute;
    left: 15px;
    top: 40px;
    bottom: -12px;
    width: 2px;
    background-color: #e3e6f0;
    z-index: 0;
}

.badge {
    font-size: 0.75rem;
}

.text-sm {
    font-size: 0.875rem;
}

/* ========== TOOLTIP STYLES ========== */
.ultra-tooltip {
    background: white;
    border-radius: 16px;
    padding: 20px;
    box-shadow: 
        0 25px 50px -12px rgba(0, 0, 0, 0.25),
        0 0 0 1px rgba(255, 255, 255, 0.8);
    border: 1px solid rgba(229, 231, 235, 0.5);
    min-width: 160px;
    backdrop-filter: blur(20px);
}

.tooltip-header {
    padding-left: 16px;
    margin-bottom: 12px;
}

.tooltip-header strong {
    display: block;
    font-size: 15px;
    font-weight: 700;
    color: #1F2937;
    margin-bottom: 4px;
}

.user-type {
    display: inline-block;
    padding: 4px 10px;
    border-radius: 8px;
    font-size: 11px;
    font-weight: 600;
    text-transform: uppercase;
    letter-spacing: 0.5px;
}

.user-type.staff {
    background: linear-gradient(135deg, #DBEAFE 0%, #BFDBFE 100%);
    color: #1E40AF;
}

.user-type.public {
    background: linear-gradient(135deg, #D1FAE5 0%, #A7F3D0 100%);
    color: #047857;
}

.tooltip-content {
    text-align: center;
}

.activity-count {
    display: flex;
    align-items: baseline;
    justify-content: center;
    gap: 6px;
}

.count-number {
    font-size: 24px;
    font-weight: 800;
    background: linear-gradient(135deg, #A8E6CF 0%, #7FCDCD 100%);
    -webkit-background-clip: text;
    -webkit-text-fill-color: transparent;
    background-clip: text;
}

.count-label {
    font-size: 13px;
    color: #6B7280;
    font-weight: 500;
}

/* ========== RESPONSIVE STYLES ========== */
@media (max-width: 768px) {
    .modern-card {
        border-radius: 24px;
    }
    
    .card-header-modern {
        padding: 12px 12px 8px;
        flex-direction: column;
        gap: 10px;
    }
    
    .card-body-modern {
        padding: 10px 12px 14px;
    }
    
    .title-container {
        gap: 16px;
    }
    
    .trophy-icon {
        width: 48px;
        height: 48px;
    }
    
    .title-text h3 {
        font-size: 20px;
    }
    
    .time-selector {
        align-self: stretch;
    }
    
    .time-btn {
        flex: 1;
        text-align: center;
    }
    
    .modern-icon-wrapper {
        width: 48px;
        height: 48px;
        border-radius: 14px;
    }
    
    .modern-icon-wrapper i {
        font-size: 20px;
    }
    
    .categories-grid {
        grid-template-columns: 1fr;
        gap: 20px;
        padding: 24px 20px;
    }
    
    .categories-header {
        padding: 24px 20px 20px;
    }
    
    .header-content {
        gap: 16px;
    }
    
    .icon-wrapper {
        width: 56px;
        height: 56px;
    }
    
    .header-text h2 {
        font-size: 24px;
    }
    
    .popular-item {
        grid-template-columns: auto 60px 1fr;
        gap: 12px;
        padding: 12px;
    }
    
    .item-image {
        width: 60px;
        height: 60px;
    }
}

@media (max-width: 480px) {
    .modern-icon-wrapper {
        width: 44px;
        height: 44px;
    }
    
    .modern-icon-wrapper i {
        font-size: 18px;
    }
    
    .popular-item {
        grid-template-columns: 1fr;
        text-align: center;
        gap: 12px;
    }
    
    .item-image {
        justify-self: center;
        width: 80px;
        height: 80px;
    }
}
</style>

<!-- ========== JAVASCRIPT ========== -->
<script>
// ========== TOP PERFORMERS CHART CONFIGURATION ========== 
<?php if (!empty($top_active_users) && count($top_active_users) > 0): ?>

// Ultra Modern Color Schemes
const colorSchemes = {
    sunset: ['#FF6B6B', '#FF8E53', '#FF6B9D', '#C44569', '#F8B500'],
    ocean: ['#4ECDC4', '#44A08D', '#096DD9', '#1890FF', '#36CFC9'],
    galaxy: ['#A8E6CF', '#7FCDCD', '#5DADE2', '#BB8FCE', '#F1948A'],
    neon: ['#FF3C3C', '#FF9F40', '#4BC0C0', '#36A2EB', '#9966FF'],
    gradient: ['#667eea', '#764ba2', '#f093fb', '#f5576c', '#4facfe']
};

// Chart data
const userData = [<?php echo implode(',', array_map(function($user) { return (int)$user->activity_count; }, $top_active_users)); ?>];
const userNames = [
    <?php 
    $names = [];
    foreach($top_active_users as $user) {
        if (isset($user->display_name)) {
            $name = $user->display_name;
        } else {
            $name = $user->full_name ?: $user->username ?: 'ผู้ใช้ ' . $user->user_id;
            if (strlen($name) > 15) {
                $name = mb_substr($name, 0, 13, 'UTF-8') . '..';
            }
        }
        $names[] = '"' . htmlspecialchars($name) . '"';
    }
    echo implode(',', $names);
    ?>
];

const userTypes = [
    <?php 
    $types = [];
    foreach($top_active_users as $user) {
        $types[] = '"' . ($user->user_type ?? 'unknown') . '"';
    }
    echo implode(',', $types);
    ?>
];

// Dynamic color assignment
function getColorForUser(value, maxValue, index) {
    const schemes = colorSchemes.galaxy;
    return schemes[index % schemes.length];
}

const maxValue = Math.max(...userData);
const chartColors = userData.map((value, index) => getColorForUser(value, maxValue, index));

var ultraChartOptions = {
    series: [{
        name: 'กิจกรรม',
        data: userData.map((value, index) => ({
            x: userNames[index],
            y: value,
            fillColor: chartColors[index],
            strokeColor: chartColors[index]
        }))
    }],
    chart: {
        type: 'bar',
        height: 380,
        fontFamily: "'SF Pro Display', 'Inter', -apple-system, BlinkMacSystemFont, sans-serif",
        toolbar: { show: false },
        animations: {
            enabled: true,
            easing: 'easeinout',
            speed: 1500,
            animateGradually: {
                enabled: true,
                delay: 200
            }
        },
        background: 'transparent',
        dropShadow: {
            enabled: false
        }
    },
    plotOptions: {
        bar: {
            horizontal: false,
            borderRadius: 16,
            borderRadiusApplication: 'end',
            borderRadiusWhenStacked: 'last',
            columnWidth: '70%',
            dataLabels: {
                position: 'top'
            },
            distributed: true
        }
    },
    colors: chartColors,
    dataLabels: {
        enabled: true,
        offsetY: -30,
        style: {
            fontSize: '12px',
            fontWeight: 700,
            colors: ['#1F2937']
        },
        formatter: function(val) {
            return val;
        },
        background: {
            enabled: true,
            foreColor: '#ffffff',
            padding: 8,
            borderRadius: 10,
            borderWidth: 2,
            borderColor: 'transparent',
            opacity: 0.9,
            dropShadow: {
                enabled: true,
                top: 2,
                left: 2,
                blur: 8,
                opacity: 0.1
            }
        }
    },
    stroke: {
        show: true,
        width: 3,
        colors: ['transparent']
    },
    xaxis: {
        categories: userNames,
        labels: {
            style: {
                fontSize: '11px',
                fontWeight: 600,
                colors: ['#6B7280']
            },
            rotate: -20,
            maxHeight: 50
        },
        axisBorder: { show: false },
        axisTicks: { show: false },
        crosshairs: { show: false }
    },
    yaxis: {
        title: { text: '' },
        labels: {
            style: {
                fontSize: '10px',
                fontWeight: 500,
                colors: ['#9CA3AF']
            },
            formatter: function(val) {
                return Math.round(val);
            }
        },
        axisBorder: { show: false },
        axisTicks: { show: false }
    },
    grid: {
        show: true,
        borderColor: 'rgba(156, 163, 175, 0.1)',
        strokeDashArray: 3,
        position: 'back',
        xaxis: { lines: { show: false } },
        yaxis: { lines: { show: true } },
        padding: {
            top: 20,
            right: 20,
            bottom: 0,
            left: 10
        }
    },
    tooltip: {
        enabled: true,
        theme: 'light',
        style: {
            fontSize: '13px',
            fontFamily: "'SF Pro Display', sans-serif"
        },
        custom: function({series, seriesIndex, dataPointIndex, w}) {
            const value = series[seriesIndex][dataPointIndex];
            const name = userNames[dataPointIndex];
            const type = userTypes[dataPointIndex];
            const color = chartColors[dataPointIndex];
            
            return `
                <div class="ultra-tooltip">
                    <div class="tooltip-header" style="border-left: 4px solid ${color}">
                        <strong>${name}</strong>
                        <span class="user-type ${type}">${type === 'staff' ? 'เจ้าหน้าที่' : 'ประชาชน'}</span>
                    </div>
                    <div class="tooltip-content">
                        <div class="activity-count">
                            <span class="count-number">${value}</span>
                            <span class="count-label">กิจกรรม</span>
                        </div>
                    </div>
                </div>
            `;
        }
    },
    legend: { show: false },
    responsive: [{
        breakpoint: 768,
        options: {
            chart: { height: 320 },
            plotOptions: {
                bar: { columnWidth: '85%' }
            },
            dataLabels: {
                style: { fontSize: '10px' }
            }
        }
    }]
};

var ultraChart = new ApexCharts(document.querySelector("#topActiveUsersChart"), ultraChartOptions);
ultraChart.render();

<?php endif; ?>

// ========== TIME SELECTOR FUNCTION ========== 
function selectTimeRange(button, days) {
    // Update button states
    document.querySelectorAll('.time-btn').forEach(btn => btn.classList.remove('active'));
    button.classList.add('active');
    
    // Add loading state
    const chartContainer = document.querySelector('#topActiveUsersChart');
    if (chartContainer) {
        chartContainer.style.opacity = '0.6';
        chartContainer.style.transform = 'scale(0.98)';
    }
    
    // สร้าง AJAX URL
    const ajaxUrl = '<?= base_url("system_admin/get_top_active_users"); ?>?days=' + days;
    
    // Fetch new data
    fetch(ajaxUrl, {
        method: 'GET',
        headers: {
            'X-Requested-With': 'XMLHttpRequest',
            'Content-Type': 'application/json'
        }
    })
    .then(response => {
        if (!response.ok) {
            throw new Error('HTTP ' + response.status + ' - ' + response.statusText);
        }
        return response.json();
    })
    .then(data => {
        if (data.success && data.data && data.data.length > 0) {
            <?php if (!empty($top_active_users)): ?>
            
            // Prepare new data
            const newData = data.data.map(item => parseInt(item.activity_count));
            const newNames = data.data.map(item => {
                if (item.display_name) {
                    return item.display_name;
                }
                
                let name = item.full_name || item.username || 'ผู้ใช้ ' + item.user_id;
                
                if (name.includes('@')) {
                    name = name.split('@')[0];
                }
                
                if (name.length > 15) {
                    name = name.substring(0, 13) + '..';
                }
                
                return name;
            });
            
            // Generate new colors
            const newColors = newData.map((value, index) => {
                const schemes = colorSchemes.galaxy;
                return schemes[index % schemes.length];
            });
            
            // Update chart
            if (typeof ultraChart !== 'undefined') {
                ultraChart.updateOptions({
                    xaxis: { categories: newNames },
                    colors: newColors
                });
                
                ultraChart.updateSeries([{
                    name: 'กิจกรรม',
                    data: newData.map((value, index) => ({
                        x: newNames[index],
                        y: value,
                        fillColor: newColors[index]
                    }))
                }]);
            }
            
            <?php endif; ?>
        } else {
            alert('ไม่มีข้อมูลสำหรับช่วงเวลาที่เลือก');
        }
        
        // Remove loading state
        if (chartContainer) {
            chartContainer.style.opacity = '1';
            chartContainer.style.transform = 'scale(1)';
        }
    })
    .catch(error => {
        console.error('AJAX Error:', error);
        alert('เกิดข้อผิดพลาด: ' + error.message);
        
        // Remove loading state
        if (chartContainer) {
            chartContainer.style.opacity = '1';
            chartContainer.style.transform = 'scale(1)';
        }
    });
}
</script>

</body>
</html>