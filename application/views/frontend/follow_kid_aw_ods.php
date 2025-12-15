<div class="text-center pages-head">
    <span class="font-pages-head">ติดตามสถานะเบี้ยยังชีพเด็ก</span>
</div>
</div>
<img src="<?php echo base_url('docs/welcome-btm-light-other.png'); ?>">

<?php
// ===================================================================
// follow_kid_aw_ods.php - หน้าติดตามสถานะเบี้ยยังชีพเด็กสำหรับ Guest
// ===================================================================

// Helper function สำหรับ CSS class ของสถานะเบี้ยยังชีพเด็ก
if (!function_exists('get_kid_aw_ods_status_class')) {
    function get_kid_aw_ods_status_class($status)
    {
        switch ($status) {
            case 'submitted':
                return 'submitted';
            case 'reviewing':
                return 'reviewing';
            case 'approved':
                return 'approved';
            case 'rejected':
                return 'rejected';
            case 'completed':
                return 'completed';
            default:
                return 'submitted';
        }
    }
}

// Helper function สำหรับแสดงสถานะเป็นภาษาไทย
if (!function_exists('get_kid_aw_ods_status_display')) {
    function get_kid_aw_ods_status_display($status)
    {
        switch ($status) {
            case 'submitted':
                return 'ยื่นเรื่องแล้ว';
            case 'reviewing':
                return 'กำลังพิจารณา';
            case 'approved':
                return 'อนุมัติแล้ว';
            case 'rejected':
                return 'ไม่อนุมัติ';
            case 'completed':
                return 'เสร็จสิ้น';
            default:
                return 'ยื่นเรื่องแล้ว';
        }
    }
}

// Helper function สำหรับแสดงประเภทเบี้ยยังชีพเด็ก
if (!function_exists('get_kid_aw_ods_type_display')) {
    function get_kid_aw_ods_type_display($type)
    {
        switch ($type) {
            case 'child_support':
                return 'เบี้ยยังชีพเด็ก';
            case 'child_disabled':
                return 'เด็กพิการ';
            case 'child_orphan':
                return 'เด็กกำพร้า';
            default:
                return 'เบี้ยยังชีพเด็ก';
        }
    }
}

// ตรวจสอบข้อมูลการค้นหา
$search_performed = isset($search_performed) ? $search_performed : false;
$kid_aw_ods_info = isset($kid_aw_ods_info) ? $kid_aw_ods_info : null;
$ref_id = isset($ref_id) ? $ref_id : '';
?>

<!-- SweetAlert2 -->
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<!-- Font Awesome -->
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">

<style>
    /* ===== FOLLOW KID AW ODS PAGE STYLES ===== */
    .follow-kid-page {
        --primary-color: #f39c12;
        --primary-light: #f5b041;
        --secondary-color: #fff8e1;
        --success-color: #81c784;
        --warning-color: #ffb74d;
        --danger-color: #e57373;
        --info-color: #64b5f6;
        --purple-color: #ba68c8;
        --light-bg: #fafbfc;
        --white: #ffffff;
        --gray-50: #fafafa;
        --gray-100: #f5f5f5;
        --gray-200: #eeeeee;
        --gray-300: #e0e0e0;
        --gray-400: #bdbdbd;
        --gray-500: #9e9e9e;
        --gray-600: #757575;
        --gray-700: #616161;
        --gray-800: #424242;
        --gray-900: #212121;
        --shadow-sm: 0 1px 2px 0 rgb(0 0 0 / 0.03);
        --shadow: 0 1px 3px 0 rgb(0 0 0 / 0.06), 0 1px 2px -1px rgb(0 0 0 / 0.04);
        --shadow-md: 0 4px 6px -1px rgb(0 0 0 / 0.06), 0 2px 4px -2px rgb(0 0 0 / 0.04);
        --shadow-lg: 0 10px 15px -3px rgb(0 0 0 / 0.06), 0 4px 6px -4px rgb(0 0 0 / 0.04);
        --shadow-xl: 0 20px 25px -5px rgb(0 0 0 / 0.06), 0 8px 10px -6px rgb(0 0 0 / 0.04);
        --border-radius: 12px;
        --border-radius-lg: 16px;
        --transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
    }

    .follow-kid-page {
        font-family: 'Inter', 'Segoe UI', system-ui, -apple-system, sans-serif;
        line-height: 1.6;
        color: var(--gray-700);
        min-height: 100vh;
        padding: 2rem 0;
    }

    .follow-container {
        max-width: 800px;
        margin: 0 auto;
        padding: 0 1rem;
    }

    /* ===== HERO SECTION ===== */
    .follow-hero {
        background: linear-gradient(135deg, rgba(243, 156, 18, 0.9) 0%, rgba(245, 176, 65, 0.7) 100%);
        color: white;
        padding: 3rem 2rem;
        border-radius: var(--border-radius-lg);
        margin-bottom: 2rem;
        box-shadow: var(--shadow-lg);
        text-align: center;
        position: relative;
        overflow: hidden;
    }

    .follow-hero::before {
        content: '';
        position: absolute;
        top: -50%;
        right: -20%;
        width: 300px;
        height: 300px;
        background: radial-gradient(circle, rgba(255, 255, 255, 0.1) 0%, transparent 70%);
        border-radius: 50%;
    }

    .follow-hero h1 {
        font-size: 2.5rem;
        font-weight: 700;
        margin: 0 0 1rem 0;
        text-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
        position: relative;
        z-index: 1;
    }

    .follow-hero .subtitle {
        font-size: 1.2rem;
        opacity: 0.9;
        margin: 0 0 2rem 0;
        position: relative;
        z-index: 1;
    }

    .follow-hero-icon {
        font-size: 4rem;
        margin-bottom: 1rem;
        opacity: 0.9;
        position: relative;
        z-index: 1;
    }

    /* ===== SEARCH SECTION ===== */
    .search-section {
        background: var(--white);
        border-radius: var(--border-radius);
        box-shadow: var(--shadow-md);
        padding: 2rem;
        margin-bottom: 2rem;
        border: 1px solid var(--gray-100);
    }

    .search-title {
        font-size: 1.5rem;
        font-weight: 700;
        color: var(--gray-900);
        margin-bottom: 1.5rem;
        text-align: center;
        display: flex;
        align-items: center;
        justify-content: center;
        gap: 0.75rem;
    }

    .search-form {
        display: flex;
        flex-direction: column;
        gap: 1.5rem;
    }

    .search-input-group {
        position: relative;
    }

    .search-input {
        width: 100%;
        padding: 1rem 1.5rem;
        padding-left: 3.5rem;
        border: 2px solid var(--gray-200);
        border-radius: var(--border-radius);
        font-size: 1.1rem;
        transition: var(--transition);
        background-color: var(--white);
    }

    .search-input:focus {
        border-color: var(--primary-color);
        box-shadow: 0 0 0 3px rgba(243, 156, 18, 0.1);
        outline: none;
    }

    .search-input-icon {
        position: absolute;
        left: 1rem;
        top: 50%;
        transform: translateY(-50%);
        font-size: 1.2rem;
        color: var(--gray-400);
    }

    .search-btn {
        padding: 1rem 2rem;
        background: linear-gradient(135deg, var(--primary-color), var(--primary-light));
        color: white;
        border: none;
        border-radius: var(--border-radius);
        font-size: 1.1rem;
        font-weight: 600;
        cursor: pointer;
        transition: var(--transition);
        display: flex;
        align-items: center;
        justify-content: center;
        gap: 0.75rem;
    }

    .search-btn:hover {
        transform: translateY(-2px);
        box-shadow: var(--shadow-lg);
    }

    .search-btn:disabled {
        background: var(--gray-300);
        cursor: not-allowed;
        transform: none;
        box-shadow: none;
    }

    .search-help {
        background: var(--secondary-color);
        border: 1px solid rgba(243, 156, 18, 0.3);
        border-radius: var(--border-radius);
        padding: 1.5rem;
        margin-top: 1.5rem;
    }

    .search-help-title {
        font-weight: 600;
        color: var(--primary-color);
        margin-bottom: 1rem;
        display: flex;
        align-items: center;
        gap: 0.5rem;
    }

    .search-help-list {
        list-style: none;
        padding: 0;
        margin: 0;
        display: flex;
        flex-direction: column;
        gap: 0.75rem;
    }

    .search-help-item {
        display: flex;
        align-items: center;
        gap: 0.75rem;
        font-size: 0.95rem;
        color: var(--gray-700);
    }

    .search-help-icon {
        color: var(--primary-color);
        font-size: 0.9rem;
        width: 16px;
        text-align: center;
    }

    /* ===== RESULT SECTION ===== */
    .result-section {
        margin-top: 2rem;
    }

    .result-card {
        background: var(--white);
        border-radius: var(--border-radius);
        box-shadow: var(--shadow-md);
        border: 1px solid var(--gray-100);
        overflow: hidden;
        animation: fadeInUp 0.5s ease-out;
    }

    .result-header {
        background: linear-gradient(135deg, var(--secondary-color) 0%, #fff3c4 100%);
        padding: 1.5rem 2rem;
        border-bottom: 1px solid var(--gray-200);
        display: flex;
        align-items: center;
        justify-content: space-between;
    }

    .result-title {
        font-size: 1.25rem;
        font-weight: 700;
        color: var(--primary-color);
        margin: 0;
        display: flex;
        align-items: center;
        gap: 0.75rem;
    }

    .result-id {
        background: linear-gradient(135deg, var(--primary-color), var(--primary-light));
        color: white;
        padding: 0.5rem 1rem;
        border-radius: 20px;
        font-size: 0.9rem;
        font-weight: 600;
    }

    .result-body {
        padding: 2rem;
    }

    /* ===== INFO GRID ===== */
    .info-grid {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
        gap: 1.5rem;
        margin-bottom: 2rem;
    }

    .info-item {
        display: flex;
        flex-direction: column;
        gap: 0.5rem;
    }

    .info-label {
        font-size: 0.875rem;
        font-weight: 600;
        color: var(--gray-600);
        text-transform: uppercase;
        letter-spacing: 0.025em;
    }

    .info-value {
        font-size: 1rem;
        color: var(--gray-900);
        padding: 0.75rem 1rem;
        background: var(--gray-50);
        border-radius: 8px;
        border: 1px solid var(--gray-200);
        font-weight: 500;
    }

    .info-value.highlight {
        background: linear-gradient(135deg, var(--secondary-color), #fff3c4);
        border-color: var(--primary-light);
        color: var(--primary-color);
        font-weight: 600;
    }

    /* ===== STATUS SECTION ===== */
    .status-section {
        background: var(--gray-50);
        border: 1px solid var(--gray-200);
        border-radius: var(--border-radius);
        padding: 1.5rem;
        margin-bottom: 2rem;
    }

    .status-header {
        display: flex;
        align-items: center;
        justify-content: space-between;
        margin-bottom: 1rem;
    }

    .status-title {
        font-size: 1.1rem;
        font-weight: 700;
        color: var(--gray-900);
        display: flex;
        align-items: center;
        gap: 0.5rem;
    }

    .status-badge {
        padding: 0.75rem 1.5rem;
        border-radius: 25px;
        font-size: 0.9rem;
        font-weight: 600;
        text-align: center;
        white-space: nowrap;
        text-transform: uppercase;
        letter-spacing: 0.025em;
        display: inline-block;
    }

    .status-badge.submitted {
        background: linear-gradient(135deg, rgba(255, 248, 225, 0.9), rgba(255, 236, 179, 0.7));
        color: #e65100;
        border: 2px solid rgba(255, 152, 0, 0.4);
    }

    .status-badge.reviewing {
        background: linear-gradient(135deg, rgba(227, 242, 253, 0.9), rgba(187, 222, 251, 0.7));
        color: #0d47a1;
        border: 2px solid rgba(33, 150, 243, 0.4);
    }

    .status-badge.approved {
        background: linear-gradient(135deg, rgba(232, 245, 232, 0.9), rgba(200, 230, 201, 0.7));
        color: #1b5e20;
        border: 2px solid rgba(76, 175, 80, 0.4);
    }

    .status-badge.rejected {
        background: linear-gradient(135deg, rgba(255, 235, 238, 0.9), rgba(255, 205, 210, 0.7));
        color: #b71c1c;
        border: 2px solid rgba(244, 67, 54, 0.4);
    }

    .status-badge.completed {
        background: linear-gradient(135deg, rgba(243, 229, 245, 0.9), rgba(225, 190, 231, 0.7));
        color: #4a148c;
        border: 2px solid rgba(156, 39, 176, 0.4);
    }

    .status-description {
        margin-top: 1rem;
        padding: 1rem;
        background: var(--white);
        border-radius: 8px;
        border-left: 4px solid var(--primary-color);
        font-size: 0.95rem;
        line-height: 1.5;
    }

    /* ===== PROGRESS TIMELINE ===== */
    .progress-timeline {
        margin-top: 2rem;
    }

    .timeline-title {
        font-size: 1.1rem;
        font-weight: 700;
        color: var(--gray-900);
        margin-bottom: 1.5rem;
        display: flex;
        align-items: center;
        gap: 0.5rem;
    }

    .timeline-container {
        position: relative;
        padding-left: 2rem;
    }

    .timeline-line {
        position: absolute;
        left: 0.75rem;
        top: 0;
        bottom: 0;
        width: 2px;
        background: var(--gray-200);
    }

    .timeline-item {
        position: relative;
        margin-bottom: 1.5rem;
        background: var(--white);
        border: 1px solid var(--gray-200);
        border-radius: var(--border-radius);
        padding: 1rem 1.5rem;
        box-shadow: var(--shadow-sm);
    }

    .timeline-item::before {
        content: '';
        position: absolute;
        left: -1.75rem;
        top: 1rem;
        width: 12px;
        height: 12px;
        background: var(--white);
        border: 3px solid var(--primary-color);
        border-radius: 50%;
    }

    .timeline-item.active::before {
        background: var(--primary-color);
    }

    .timeline-item.future::before {
        border-color: var(--gray-300);
        background: var(--gray-100);
    }

    .timeline-content {
        display: flex;
        justify-content: space-between;
        align-items: flex-start;
        gap: 1rem;
    }

    .timeline-status {
        font-weight: 600;
        color: var(--gray-900);
    }

    .timeline-date {
        font-size: 0.875rem;
        color: var(--gray-600);
        white-space: nowrap;
    }

    .timeline-description {
        margin-top: 0.5rem;
        font-size: 0.9rem;
        color: var(--gray-600);
        line-height: 1.4;
    }

    /* ===== NO RESULT STATE ===== */
    .no-result {
        background: var(--white);
        border-radius: var(--border-radius);
        box-shadow: var(--shadow-md);
        padding: 3rem 2rem;
        text-align: center;
        border: 1px solid var(--gray-100);
    }

    .no-result-icon {
        font-size: 4rem;
        color: var(--gray-400);
        margin-bottom: 1.5rem;
    }

    .no-result-title {
        font-size: 1.5rem;
        font-weight: 700;
        color: var(--gray-700);
        margin-bottom: 1rem;
    }

    .no-result-message {
        font-size: 1rem;
        color: var(--gray-600);
        line-height: 1.5;
        margin-bottom: 2rem;
    }

    .no-result-suggestions {
        background: var(--secondary-color);
        border-radius: var(--border-radius);
        padding: 1.5rem;
        margin-top: 2rem;
    }

    .suggestions-title {
        font-weight: 600;
        color: var(--primary-color);
        margin-bottom: 1rem;
    }

    .suggestions-list {
        list-style: none;
        padding: 0;
        margin: 0;
        text-align: left;
    }

    .suggestions-item {
        padding: 0.5rem 0;
        color: var(--gray-700);
        display: flex;
        align-items: flex-start;
        gap: 0.75rem;
    }

    .suggestions-item::before {
        content: '•';
        color: var(--primary-color);
        font-weight: bold;
        margin-top: 0.1rem;
    }

    /* ===== RESPONSIVE DESIGN ===== */
    @media (max-width: 768px) {
        .follow-kid-page {
            padding: 1rem 0;
        }

        .follow-container {
            padding: 0 0.5rem;
        }

        .follow-hero {
            padding: 2rem 1.5rem;
            margin-bottom: 1.5rem;
        }

        .follow-hero h1 {
            font-size: 2rem;
        }

        .follow-hero .subtitle {
            font-size: 1rem;
        }

        .search-section {
            padding: 1.5rem;
        }

        .search-form {
            gap: 1rem;
        }

        .search-input {
            padding: 0.875rem 1.25rem;
            padding-left: 3rem;
            font-size: 1rem;
        }

        .search-btn {
            padding: 0.875rem 1.5rem;
            font-size: 1rem;
        }

        .info-grid {
            grid-template-columns: 1fr;
            gap: 1rem;
        }

        .result-header {
            padding: 1.25rem 1.5rem;
            flex-direction: column;
            gap: 1rem;
            text-align: center;
        }

        .result-body {
            padding: 1.5rem;
        }

        .status-header {
            flex-direction: column;
            gap: 1rem;
            text-align: center;
        }

        .timeline-content {
            flex-direction: column;
            gap: 0.5rem;
        }

        .timeline-date {
            white-space: normal;
        }
    }

    /* ===== ANIMATIONS ===== */
    @keyframes fadeInUp {
        from {
            opacity: 0;
            transform: translateY(20px);
        }

        to {
            opacity: 1;
            transform: translateY(0);
        }
    }

    @keyframes pulse {

        0%,
        100% {
            opacity: 1;
        }

        50% {
            opacity: 0.7;
        }
    }

    .loading {
        animation: pulse 2s infinite;
    }

    /* ===== LOADING STATES ===== */
    .search-btn.loading {
        pointer-events: none;
    }

    .search-btn.loading .btn-text {
        opacity: 0;
    }

    .search-btn.loading .loading-spinner {
        display: inline-block;
    }

    .loading-spinner {
        display: none;
        width: 20px;
        height: 20px;
        border: 2px solid transparent;
        border-top: 2px solid currentColor;
        border-radius: 50%;
        animation: spin 1s linear infinite;
    }

    @keyframes spin {
        0% {
            transform: rotate(0deg);
        }

        100% {
            transform: rotate(360deg);
        }
    }
</style>

<div class="follow-kid-page">
    <div class="follow-container">
        <!-- ===== HERO SECTION ===== -->
        <div class="follow-hero">
            <div class="follow-hero-icon">
                <i class="fas fa-child"></i>
            </div>
            <h1>ติดตามสถานะเบี้ยยังชีพเด็ก</h1>
            <p class="subtitle">ตรวจสอบสถานะการยื่นเรื่องเบี้ยยังชีพเด็ก / เด็กพิการ / เด็กกำพร้า</p>
        </div>

        <!-- ===== SEARCH SECTION ===== -->
        <div class="search-section">
            <h2 class="search-title">
                <i class="fas fa-search"></i>
                ค้นหาข้อมูลการยื่นเรื่อง
            </h2>

            <form class="search-form" id="searchForm" method="GET" action="<?= current_url() ?>">
                <div class="search-input-group">
                    <i class="fas fa-hashtag search-input-icon"></i>
                    <input type="text" class="search-input" name="ref" id="searchInput"
                        placeholder="กรอกหมายเลขอ้างอิง เช่น K6712345" value="<?= htmlspecialchars($ref_id) ?>"
                        required>
                </div>

                <button type="submit" class="search-btn" id="searchBtn">
                    <span class="btn-text">
                        <i class="fas fa-search"></i>
                        ค้นหา
                    </span>
                    <span class="loading-spinner"></span>
                </button>
            </form>

            <div class="search-help">
                <div class="search-help-title">
                    <i class="fas fa-info-circle"></i>
                    วิธีการค้นหา
                </div>
                <ul class="search-help-list">
                    <li class="search-help-item">
                        <i class="fas fa-check search-help-icon"></i>
                        ใช้หมายเลขอ้างอิงที่ได้รับหลังยื่นเรื่อง
                    </li>
                    <li class="search-help-item">
                        <i class="fas fa-check search-help-icon"></i>
                        หมายเลขอ้างอิงขึ้นต้นด้วย "K" ตามด้วยตัวเลข
                    </li>
                    <li class="search-help-item">
                        <i class="fas fa-check search-help-icon"></i>
                        ตัวอย่าง: K6712345, K6800001
                    </li>
                    <li class="search-help-item">
                        <i class="fas fa-exclamation-triangle search-help-icon"></i>
                        ระบบค้นหาได้เฉพาะการยื่นเรื่องแบบผู้ใช้ทั่วไป (Guest) เท่านั้น
                    </li>
                </ul>
            </div>
        </div>

        <!-- ===== RESULT SECTION ===== -->
        <?php if ($search_performed): ?>
            <div class="result-section">
                <?php if ($kid_aw_ods_info): ?>
                    <!-- มีผลลัพธ์ -->
                    <div class="result-card">
                        <div class="result-header">
                            <h3 class="result-title">
                                <i class="fas fa-file-alt"></i>
                                ข้อมูลการยื่นเรื่อง
                            </h3>
                            <div class="result-id">#<?= htmlspecialchars($kid_aw_ods_info['kid_aw_ods_id']) ?></div>
                        </div>

                        <div class="result-body">
                            <!-- ข้อมูลพื้นฐาน -->
                            <div class="info-grid">
                                <div class="info-item">
                                    <span class="info-label">หมายเลขอ้างอิง</span>
                                    <div class="info-value highlight">
                                        #<?= htmlspecialchars($kid_aw_ods_info['kid_aw_ods_id']) ?>
                                    </div>
                                </div>
                                <div class="info-item">
                                    <span class="info-label">ประเภทเบี้ยยังชีพ</span>
                                    <div class="info-value">
                                        <?= get_kid_aw_ods_type_display($kid_aw_ods_info['kid_aw_ods_type'] ?? 'child_support') ?>
                                    </div>
                                </div>
                                <div class="info-item">
                                    <span class="info-label">ชื่อผู้ยื่นเรื่อง</span>
                                    <div class="info-value">
                                        <?= htmlspecialchars($kid_aw_ods_info['kid_aw_ods_by']) ?>
                                    </div>
                                </div>
                                <div class="info-item">
                                    <span class="info-label">เบอร์โทรศัพท์</span>
                                    <div class="info-value">
                                        <?= htmlspecialchars($kid_aw_ods_info['kid_aw_ods_phone']) ?>
                                    </div>
                                </div>
                                <div class="info-item">
                                    <span class="info-label">ชื่อเด็ก</span>
                                    <div class="info-value">
                                        <?= htmlspecialchars($kid_aw_ods_info['kid_aw_ods_child_name'] ?? '') ?>
                                    </div>
                                </div>
                                <div class="info-item">
                                    <span class="info-label">วันที่ยื่นเรื่อง</span>
                                    <div class="info-value">
                                        <?php
                                        if (!empty($kid_aw_ods_info['kid_aw_ods_datesave'])) {
                                            $thai_months = [
                                                '01' => 'มกราคม',
                                                '02' => 'กุมภาพันธ์',
                                                '03' => 'มีนาคม',
                                                '04' => 'เมษายน',
                                                '05' => 'พฤษภาคม',
                                                '06' => 'มิถุนายน',
                                                '07' => 'กรกฎาคม',
                                                '08' => 'สิงหาคม',
                                                '09' => 'กันยายน',
                                                '10' => 'ตุลาคม',
                                                '11' => 'พฤศจิกายน',
                                                '12' => 'ธันวาคม'
                                            ];

                                            $date = date('j', strtotime($kid_aw_ods_info['kid_aw_ods_datesave']));
                                            $month = $thai_months[date('m', strtotime($kid_aw_ods_info['kid_aw_ods_datesave']))];
                                            $year = date('Y', strtotime($kid_aw_ods_info['kid_aw_ods_datesave'])) + 543;
                                            $time = date('H:i', strtotime($kid_aw_ods_info['kid_aw_ods_datesave']));

                                            echo $date . ' ' . $month . ' ' . $year . ' เวลา ' . $time . ' น.';
                                        }
                                        ?>
                                    </div>
                                </div>
                                <?php if (!empty($kid_aw_ods_info['kid_aw_ods_updated_at'])): ?>
                                    <div class="info-item">
                                        <span class="info-label">อัปเดตล่าสุด</span>
                                        <div class="info-value">
                                            <?php
                                            $date = date('j', strtotime($kid_aw_ods_info['kid_aw_ods_updated_at']));
                                            $month = $thai_months[date('m', strtotime($kid_aw_ods_info['kid_aw_ods_updated_at']))];
                                            $year = date('Y', strtotime($kid_aw_ods_info['kid_aw_ods_updated_at'])) + 543;
                                            $time = date('H:i', strtotime($kid_aw_ods_info['kid_aw_ods_updated_at']));

                                            echo $date . ' ' . $month . ' ' . $year . ' เวลา ' . $time . ' น.';
                                            ?>
                                        </div>
                                    </div>
                                <?php endif; ?>
                            </div>

                            <!-- สถานะปัจจุบัน -->
                            <div class="status-section">
                                <div class="status-header">
                                    <div class="status-title">
                                        <i class="fas fa-traffic-light"></i>
                                        สถานะปัจจุบัน
                                    </div>
                                    <div
                                        class="status-badge <?= get_kid_aw_ods_status_class($kid_aw_ods_info['kid_aw_ods_status']) ?>">
                                        <?= get_kid_aw_ods_status_display($kid_aw_ods_info['kid_aw_ods_status']) ?>
                                    </div>
                                </div>

                                <div class="status-description">
                                    <?php
                                    $status_descriptions = [
                                        'submitted' => 'เรื่องของท่านได้รับการยื่นเรียบร้อยแล้ว กำลังรอเจ้าหน้าที่ตรวจสอบ',
                                        'reviewing' => 'เจ้าหน้าที่กำลังพิจารณาเรื่องของท่าน กรุณารอผลการพิจารณา',
                                        'approved' => 'เรื่องของท่านได้รับการอนุมัติแล้ว กำลังดำเนินการในขั้นตอนต่อไป',
                                        'rejected' => 'เรื่องของท่านไม่ได้รับการอนุมัติ กรุณาติดต่อเจ้าหน้าที่เพื่อสอบถามรายละเอียด',
                                        'completed' => 'เรื่องของท่านดำเนินการเสร็จสิ้นแล้ว'
                                    ];

                                    echo $status_descriptions[$kid_aw_ods_info['kid_aw_ods_status']] ?? 'สถานะไม่ทราบ';
                                    ?>
                                </div>
                            </div>

                            <!-- ไทม์ไลน์ความคืบหน้า -->
                            <div class="progress-timeline">
                                <div class="timeline-title">
                                    <i class="fas fa-history"></i>
                                    ความคืบหน้า
                                </div>

                                <div class="timeline-container">
                                    <div class="timeline-line"></div>

                                    <?php
                                    $current_status = $kid_aw_ods_info['kid_aw_ods_status'];
                                    $timeline_steps = [
                                        'submitted' => [
                                            'title' => 'ยื่นเรื่องแล้ว',
                                            'description' => 'ระบบได้รับเรื่องของท่านเรียบร้อยแล้ว',
                                            'icon' => 'fas fa-file-alt'
                                        ],
                                        'reviewing' => [
                                            'title' => 'กำลังพิจารณา',
                                            'description' => 'เจ้าหน้าที่กำลังตรวจสอบและพิจารณาเรื่องของท่าน',
                                            'icon' => 'fas fa-search'
                                        ],
                                        'approved' => [
                                            'title' => 'อนุมัติแล้ว',
                                            'description' => 'เรื่องของท่านได้รับการอนุมัติ กำลังดำเนินการขั้นตอนต่อไป',
                                            'icon' => 'fas fa-check-circle'
                                        ],
                                        'completed' => [
                                            'title' => 'เสร็จสิ้น',
                                            'description' => 'ดำเนินการทุกขั้นตอนเสร็จสิ้นแล้ว',
                                            'icon' => 'fas fa-trophy'
                                        ]
                                    ];

                                    // กรณีที่ถูก reject จะข้าม approved ไป completed
                                    if ($current_status === 'rejected') {
                                        $timeline_steps['rejected'] = [
                                            'title' => 'ไม่อนุมัติ',
                                            'description' => 'เรื่องของท่านไม่ได้รับการอนุมัติ',
                                            'icon' => 'fas fa-times-circle'
                                        ];
                                        unset($timeline_steps['approved']);
                                    }

                                    $status_order = array_keys($timeline_steps);
                                    $current_index = array_search($current_status, $status_order);

                                    foreach ($timeline_steps as $step_status => $step_info):
                                        $step_index = array_search($step_status, $status_order);
                                        $item_class = '';

                                        if ($step_index < $current_index) {
                                            $item_class = 'completed';
                                        } elseif ($step_index === $current_index) {
                                            $item_class = 'active';
                                        } else {
                                            $item_class = 'future';
                                        }
                                        ?>
                                        <div class="timeline-item <?= $item_class ?>">
                                            <div class="timeline-content">
                                                <div>
                                                    <div class="timeline-status">
                                                        <i class="<?= $step_info['icon'] ?> me-2"></i>
                                                        <?= $step_info['title'] ?>
                                                    </div>
                                                    <div class="timeline-description">
                                                        <?= $step_info['description'] ?>
                                                    </div>
                                                </div>
                                                <div class="timeline-date">
                                                    <?php if ($step_index <= $current_index && !empty($kid_aw_ods_info['kid_aw_ods_datesave'])): ?>
                                                        <?php
                                                        // ใช้วันที่ยื่นเรื่องสำหรับ submitted, ส่วนอื่นใช้วันที่อัปเดต
                                                        $display_date = ($step_status === 'submitted')
                                                            ? $kid_aw_ods_info['kid_aw_ods_datesave']
                                                            : ($kid_aw_ods_info['kid_aw_ods_updated_at'] ?? $kid_aw_ods_info['kid_aw_ods_datesave']);

                                                        if (!empty($display_date)) {
                                                            $date = date('j', strtotime($display_date));
                                                            $month = $thai_months[date('m', strtotime($display_date))];
                                                            $year = date('Y', strtotime($display_date)) + 543;
                                                            echo $date . ' ' . $month . ' ' . $year;
                                                        }
                                                        ?>
                                                    <?php elseif ($step_index <= $current_index): ?>
                                                        ดำเนินการแล้ว
                                                    <?php else: ?>
                                                        รอดำเนินการ
                                                    <?php endif; ?>
                                                </div>
                                            </div>
                                        </div>
                                    <?php endforeach; ?>
                                </div>
                            </div>

                            <!-- ข้อมูลติดต่อ -->
                            <div class="status-section" style="margin-top: 2rem;">
                                <div class="status-title">
                                    <i class="fas fa-phone"></i>
                                    ข้อมูลติดต่อ
                                </div>
                                <div class="info-grid" style="margin-top: 1rem; margin-bottom: 0;">
                                    <div class="info-item">
                                        <span class="info-label">เบอร์โทรศัพท์</span>
                                        <div class="info-value">
                                            <a href="tel:<?= htmlspecialchars($kid_aw_ods_info['kid_aw_ods_phone']) ?>"
                                                style="color: var(--primary-color); text-decoration: none;">
                                                <?= htmlspecialchars($kid_aw_ods_info['kid_aw_ods_phone']) ?>
                                            </a>
                                        </div>
                                    </div>
                                    <?php if (!empty($kid_aw_ods_info['kid_aw_ods_email'])): ?>
                                        <div class="info-item">
                                            <span class="info-label">อีเมล</span>
                                            <div class="info-value">
                                                <a href="mailto:<?= htmlspecialchars($kid_aw_ods_info['kid_aw_ods_email']) ?>"
                                                    style="color: var(--primary-color); text-decoration: none;">
                                                    <?= htmlspecialchars($kid_aw_ods_info['kid_aw_ods_email']) ?>
                                                </a>
                                            </div>
                                        </div>
                                    <?php endif; ?>
                                </div>
                            </div>
                        </div>
                    </div>
                <?php else: ?>
                    <!-- ไม่พบผลลัพธ์ -->
                    <div class="no-result">
                        <div class="no-result-icon">
                            <i class="fas fa-search-minus"></i>
                        </div>
                        <h3 class="no-result-title">ไม่พบข้อมูลที่ค้นหา</h3>
                        <p class="no-result-message">
                            ไม่พบหมายเลขอ้างอิง <strong><?= htmlspecialchars($ref_id) ?></strong> ในระบบ<br>
                            หรือไม่ใช่การยื่นเรื่องแบบผู้ใช้ทั่วไป (Guest)
                        </p>

                        <div class="no-result-suggestions">
                            <div class="suggestions-title">กรุณาตรวจสอบ:</div>
                            <ul class="suggestions-list">
                                <li class="suggestions-item">หมายเลขอ้างอิงที่กรอกถูกต้องหรือไม่</li>
                                <li class="suggestions-item">หมายเลขขึ้นต้นด้วย "K" ตามด้วยตัวเลข</li>
                                <li class="suggestions-item">การยื่นเรื่องเป็นแบบผู้ใช้ทั่วไป (Guest) หรือไม่</li>
                                <li class="suggestions-item">ลองค้นหาใหม่อีกครั้งหรือติดต่อเจ้าหน้าที่</li>
                            </ul>
                        </div>
                    </div>
                <?php endif; ?>
            </div>
        <?php endif; ?>
    </div>
</div>

<!-- Bootstrap 5 JS -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>

<script>
    // ===================================================================
    // *** CONFIGURATION & VARIABLES ***
    // ===================================================================

    const FollowKidConfig = {
        baseUrl: '<?= site_url() ?>',
        currentUrl: '<?= current_url() ?>',
        debug: <?= (defined('ENVIRONMENT') && ENVIRONMENT === 'development') ? 'true' : 'false' ?>
    };

    // *** เพิ่ม: Debug reCAPTCHA variables ตั้งแต่เริ่มต้น ***
    console.log('🔑 Initial reCAPTCHA check for Follow Kid Aw Ods:');
    console.log('- RECAPTCHA_SITE_KEY:', typeof window.RECAPTCHA_SITE_KEY !== 'undefined' ? window.RECAPTCHA_SITE_KEY : 'UNDEFINED');
    console.log('- recaptchaReady:', typeof window.recaptchaReady !== 'undefined' ? window.recaptchaReady : 'UNDEFINED');
    console.log('- SKIP_RECAPTCHA_FOR_DEV:', typeof window.SKIP_RECAPTCHA_FOR_DEV !== 'undefined' ? window.SKIP_RECAPTCHA_FOR_DEV : 'UNDEFINED');
    console.log('- grecaptcha available:', typeof grecaptcha !== 'undefined');

    // ===================================================================
    // *** CORE FUNCTIONS ***
    // ===================================================================

    /**
     * จัดการ Form Submit - เพิ่ม reCAPTCHA
     */
    function handleFormSubmit() {
        const form = document.getElementById('searchForm');
        const searchBtn = document.getElementById('searchBtn');
        const searchInput = document.getElementById('searchInput');

        if (!form || !searchBtn || !searchInput) return;

        form.addEventListener('submit', function (e) {
            e.preventDefault(); // ป้องกัน default submit เสมอ

            const searchValue = searchInput.value.trim();

            if (!searchValue) {
                Swal.fire({
                    title: 'กรุณากรอกหมายเลขอ้างอิง',
                    text: 'กรุณากรอกหมายเลขอ้างอิงเพื่อค้นหาข้อมูล',
                    icon: 'warning',
                    confirmButtonText: 'ตกลง',
                    confirmButtonColor: '#f39c12'
                });

                searchInput.focus();
                return;
            }

            console.log('📝 Follow Kid Aw Ods search submitted - Ref:', searchValue);

            // แสดง loading state
            searchBtn.classList.add('loading');
            searchBtn.disabled = true;
            const originalContent = searchBtn.innerHTML;
            searchBtn.innerHTML = '<i class="fas fa-spinner fa-spin me-2"></i>กำลังค้นหา...';

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

                grecaptcha.ready(function () {
                    console.log('🔧 grecaptcha.ready() called for follow kid aw ods');

                    grecaptcha.execute(window.RECAPTCHA_SITE_KEY, {
                        action: 'follow_kid_aw_ods_search'
                    }).then(function (token) {
                        console.log('✅ reCAPTCHA token received for follow kid aw ods:', token.substring(0, 50) + '...');
                        console.log('📏 Token length:', token.length);

                        performFollowKidSearchWithRecaptcha(searchValue, token, searchBtn, originalContent);
                    }).catch(function (error) {
                        console.error('❌ reCAPTCHA execution failed for follow kid aw ods:', error);
                        console.log('🔄 Falling back to search without reCAPTCHA');
                        performFollowKidSearchWithoutRecaptcha(searchValue, searchBtn, originalContent);
                    });
                });
            } else {
                console.log('⚠️ reCAPTCHA not available, searching without verification');
                console.log('📋 Reasons breakdown:');
                console.log('- SITE_KEY exists:', !!window.RECAPTCHA_SITE_KEY);
                console.log('- reCAPTCHA ready:', !!window.recaptchaReady);
                console.log('- Skip dev mode:', !!window.SKIP_RECAPTCHA_FOR_DEV);
                console.log('- grecaptcha available:', typeof grecaptcha !== 'undefined');

                performFollowKidSearchWithoutRecaptcha(searchValue, searchBtn, originalContent);
            }
        });
    }

    // แก้ไขเงื่อนไข reCAPTCHA ให้ชัดเจนขึ้น
    function shouldUseRecaptcha() {
        const hasKey = window.RECAPTCHA_SITE_KEY && window.RECAPTCHA_SITE_KEY !== '';
        const isReady = window.recaptchaReady === true;
        const notSkipping = !window.SKIP_RECAPTCHA_FOR_DEV;
        const hasGrecaptcha = typeof grecaptcha !== 'undefined' && grecaptcha.execute;

        console.log('🔍 reCAPTCHA readiness check:', {
            hasKey, isReady, notSkipping, hasGrecaptcha
        });

        return hasKey && isReady && notSkipping && hasGrecaptcha;
    }


    // *** เพิ่ม: Search Function พร้อม reCAPTCHA ***
    function performFollowKidSearchWithRecaptcha(searchValue, recaptchaToken, searchBtn, originalContent) {
        console.log('📤 Submitting follow kid aw ods search with reCAPTCHA token...');

        try {
            // สร้าง form สำหรับ POST
            const form = document.createElement('form');
            form.method = 'POST';
            form.action = window.location.pathname;

            // เตรียม fields
            const fields = {
                'ref': searchValue,
                'g-recaptcha-response': recaptchaToken,
                'recaptcha_action': 'follow_kid_aw_ods_search',
                'recaptcha_source': 'follow_kid_aw_ods_form',
                'user_type_detected': 'guest'
            };

            // เพิ่ม CSRF token
            const csrfMeta = document.querySelector('meta[name="csrf-token"]');
            if (csrfMeta) {
                fields[csrfMeta.getAttribute('name')] = csrfMeta.getAttribute('content');
            }

            // สร้าง hidden inputs
            Object.keys(fields).forEach(key => {
                const input = document.createElement('input');
                input.type = 'hidden';
                input.name = key;
                input.value = fields[key];
                form.appendChild(input);
            });

            // Submit form
            document.body.appendChild(form);
            form.submit();

        } catch (error) {
            console.error('Follow kid aw ods search with reCAPTCHA error:', error);
            handleFollowKidSearchError(error);
            restoreFollowKidSearchButton(searchBtn, originalContent);
        }
    }

    // *** เพิ่ม: Search Function แบบปกติ ***
    function performFollowKidSearchWithoutRecaptcha(searchValue, searchBtn, originalContent) {
        console.log('📤 Submitting follow kid aw ods search without reCAPTCHA...');

        try {
            // ใช้ GET method เหมือนเดิม
            const newUrl = new URL(window.location);
            newUrl.searchParams.set('ref', searchValue);

            setTimeout(() => {
                window.location.href = newUrl.toString();
            }, 500);

        } catch (error) {
            console.error('Follow kid aw ods search without reCAPTCHA error:', error);
            handleFollowKidSearchError(error);
            restoreFollowKidSearchButton(searchBtn, originalContent);
        }
    }

    // *** เพิ่ม: จัดการ Response ***
    function handleFollowKidSearchResponse(data, searchValue) {
        // เนื่องจากใช้ GET redirect แทน AJAX 
        // ฟังก์ชันนี้จะไม่ถูกเรียกใช้ แต่เก็บไว้เผื่ออนาคต
        console.log('Search response:', data);

        // Redirect to result page
        const newUrl = new URL(window.location);
        newUrl.searchParams.set('ref', searchValue);
        window.location.href = newUrl.toString();
    }

    // *** เพิ่ม: จัดการ Error ***
    function handleFollowKidSearchError(error) {
        console.error('Follow kid aw ods search error:', error);
        Swal.fire({
            title: 'เกิดข้อผิดพลาด',
            text: 'ไม่สามารถค้นหาข้อมูลได้ กรุณาลองใหม่อีกครั้ง',
            icon: 'error',
            confirmButtonText: 'ตกลง',
            confirmButtonColor: '#f39c12'
        });
    }

    // *** เพิ่ม: คืนค่าปุ่มเป็นสถานะเดิม ***
    function restoreFollowKidSearchButton(searchBtn, originalContent) {
        if (searchBtn) {
            searchBtn.classList.remove('loading');
            searchBtn.disabled = false;
            searchBtn.innerHTML = originalContent;
        }
    }

    /**
     * จัดการ Input Enhancement
     */
    function handleInputEnhancement() {
        const searchInput = document.getElementById('searchInput');

        if (!searchInput) return;

        // Auto format เป็นตัวพิมพ์ใหญ่
        searchInput.addEventListener('input', function (e) {
            let value = e.target.value.toUpperCase();

            // ลบอักขระที่ไม่ใช่ตัวอักษรหรือตัวเลข
            value = value.replace(/[^A-Z0-9]/g, '');

            e.target.value = value;
        });

        // Enter key to submit
        searchInput.addEventListener('keypress', function (e) {
            if (e.key === 'Enter') {
                e.preventDefault();
                document.getElementById('searchForm').dispatchEvent(new Event('submit'));
            }
        });

        // Focus เมื่อโหลดหน้า
        if (!searchInput.value) {
            setTimeout(() => {
                searchInput.focus();
            }, 500);
        }
    }

    /**
     * จัดการ Auto Refresh
     */
    function handleAutoRefresh() {
        // Auto refresh ทุก 3 นาที หากมีผลลัพธ์และสถานะยังไม่เสร็จ
        const resultSection = document.querySelector('.result-section');
        const statusBadge = document.querySelector('.status-badge');

        if (resultSection && statusBadge) {
            const currentStatus = statusBadge.className.split(' ').pop();

            // Refresh หากสถานะยัง submitted หรือ reviewing
            if (currentStatus === 'submitted' || currentStatus === 'reviewing') {
                setInterval(() => {
                    refreshResults();
                }, 180000); // 3 minutes
            }
        }
    }

    /**
     * รีเฟรชผลลัพธ์
     */
    function refreshResults() {
        const searchInput = document.getElementById('searchInput');
        const currentRef = searchInput ? searchInput.value.trim() : '';

        if (!currentRef) return;

        console.log('Auto refreshing results for:', currentRef);

        // เพิ่ม loading indicator แบบเบาๆ
        const resultSection = document.querySelector('.result-section');
        if (resultSection) {
            resultSection.classList.add('loading');
        }

        // Reload หน้าพร้อม parameter เดิม
        const newUrl = new URL(window.location);
        newUrl.searchParams.set('ref', currentRef);

        setTimeout(() => {
            window.location.href = newUrl.toString();
        }, 1000);
    }

    /**
     * จัดการ URL Parameters
     */
    function handleUrlParameters() {
        const urlParams = new URLSearchParams(window.location.search);
        const refParam = urlParams.get('ref');
        const searchInput = document.getElementById('searchInput');

        if (refParam && searchInput && !searchInput.value) {
            searchInput.value = refParam.toUpperCase();
        }
    }

    /**
     * จัดการ Timeline Animation
     */
    function handleTimelineAnimation() {
        const timelineItems = document.querySelectorAll('.timeline-item');

        timelineItems.forEach((item, index) => {
            item.style.animationDelay = `${index * 0.1}s`;
            item.style.animation = 'fadeInUp 0.5s ease-out forwards';
        });
    }

    /**
     * จัดการ Contact Links
     */
    function handleContactLinks() {
        // เพิ่ม click tracking สำหรับ tel และ mailto links
        const contactLinks = document.querySelectorAll('a[href^="tel:"], a[href^="mailto:"]');

        contactLinks.forEach(link => {
            link.addEventListener('click', function (e) {
                const type = this.href.startsWith('tel:') ? 'phone' : 'email';
                const value = this.href.replace(/^(tel:|mailto:)/, '');

                console.log(`Contact clicked: ${type} - ${value}`);

                // Optional: Send analytics
                if (typeof gtag !== 'undefined') {
                    gtag('event', 'contact_click', {
                        'contact_type': type,
                        'contact_value': value
                    });
                }
            });
        });
    }

    /**
     * จัดการ Responsive Behavior
     */
    function handleResponsiveBehavior() {
        // ปรับพฤติกรรมสำหรับมือถือ
        if (window.innerWidth <= 768) {
            // ปรับ grid layout สำหรับมือถือ
            const infoGrids = document.querySelectorAll('.info-grid');
            infoGrids.forEach(grid => {
                grid.style.gridTemplateColumns = '1fr';
            });

            // ปรับ timeline สำหรับมือถือ
            const timelineItems = document.querySelectorAll('.timeline-content');
            timelineItems.forEach(content => {
                content.style.flexDirection = 'column';
                content.style.gap = '0.5rem';
            });
        }
    }

    // ===================================================================
    // *** EVENT HANDLERS & INITIALIZATION ***
    // ===================================================================

    document.addEventListener('DOMContentLoaded', function () {
        console.log('🚀 Follow Kid AW ODS Page loading...');

        try {
            // Initialize core functionality
            handleFormSubmit();
            handleInputEnhancement();
            handleUrlParameters();
            handleAutoRefresh();
            handleTimelineAnimation();
            handleContactLinks();
            handleResponsiveBehavior();

            // *** เพิ่ม: ตรวจสอบการโหลด reCAPTCHA ***
            if (window.RECAPTCHA_SITE_KEY && !window.recaptchaReady) {
                console.log('⏳ Waiting for reCAPTCHA to load for follow kid aw ods...');

                let checkInterval = setInterval(function () {
                    if (window.recaptchaReady) {
                        console.log('✅ reCAPTCHA is now ready for follow kid aw ods');
                        clearInterval(checkInterval);
                    }
                }, 100);

                setTimeout(function () {
                    if (!window.recaptchaReady) {
                        console.log('⚠️ reCAPTCHA timeout after 10 seconds for follow kid aw ods');
                        clearInterval(checkInterval);
                    }
                }, 10000);
            }

            console.log('✅ Follow Kid AW ODS Page initialized successfully');

            if (FollowKidConfig.debug) {
                console.log('🔧 Debug mode enabled');
                console.log('⚙️ Configuration:', FollowKidConfig);
            }

        } catch (error) {
            console.error('❌ Initialization error:', error);
            Swal.fire({
                title: 'เกิดข้อผิดพลาด',
                text: 'เกิดข้อผิดพลาดในการโหลดหน้า กรุณารีเฟรชหน้า',
                icon: 'error',
                confirmButtonText: 'ตกลง',
                confirmButtonColor: '#f39c12'
            });
        }
    });

    // ===================================================================
    // *** FLASH MESSAGES ***
    // ===================================================================

    // Success message
    <?php if (isset($success_message) && !empty($success_message)): ?>
        document.addEventListener('DOMContentLoaded', function () {
            Swal.fire({
                title: 'สำเร็จ!',
                text: <?= json_encode($success_message, JSON_UNESCAPED_UNICODE) ?>,
                icon: 'success',
                timer: 3000,
                showConfirmButton: false,
                confirmButtonColor: '#f39c12'
            });
        });
    <?php endif; ?>

    // Error message
    <?php if (isset($error_message) && !empty($error_message)): ?>
        document.addEventListener('DOMContentLoaded', function () {
            Swal.fire({
                title: 'เกิดข้อผิดพลาด',
                text: <?= json_encode($error_message, JSON_UNESCAPED_UNICODE) ?>,
                icon: 'error',
                confirmButtonColor: '#f39c12'
            });
        });
    <?php endif; ?>

    // Info message
    <?php if (isset($info_message) && !empty($info_message)): ?>
        document.addEventListener('DOMContentLoaded', function () {
            Swal.fire({
                title: 'ข้อมูล',
                text: <?= json_encode($info_message, JSON_UNESCAPED_UNICODE) ?>,
                icon: 'info',
                timer: 4000,
                showConfirmButton: false,
                confirmButtonColor: '#f39c12'
            });
        });
    <?php endif; ?>

    // Warning message
    <?php if (isset($warning_message) && !empty($warning_message)): ?>
        document.addEventListener('DOMContentLoaded', function () {
            Swal.fire({
                title: 'คำเตือน',
                text: <?= json_encode($warning_message, JSON_UNESCAPED_UNICODE) ?>,
                icon: 'warning',
                timer: 4000,
                showConfirmButton: false,
                confirmButtonColor: '#f39c12'
            });
        });
    <?php endif; ?>

    console.log("🔍 Follow Kid AW ODS System loaded successfully");
    console.log("🔧 Environment: <?= defined('ENVIRONMENT') ? ENVIRONMENT : 'production' ?>");
    console.log("📊 Follow Status: Ready");
</script>