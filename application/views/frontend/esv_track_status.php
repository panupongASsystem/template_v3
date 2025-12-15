<div class="text-center pages-head">
    <span class="font-pages-head">ติดตามสถานะเอกสารออนไลน์</span>
</div>
</div>
<img src="<?php echo base_url('docs/welcome-btm-light-other.png'); ?>">

<?php
// ===================================================================
// esv_track_status.php - หน้าติดตามสถานะเอกสาร ESV สำหรับ Guest (แก้ไขแล้ว)
// ===================================================================

// Helper function สำหรับ CSS class ของสถานะเอกสาร ESV
if (!function_exists('get_esv_status_class')) {
    function get_esv_status_class($status)
    {
        switch ($status) {
            case 'pending':
                return 'pending';
            case 'processing':
                return 'processing';
            case 'completed':
                return 'completed';
            case 'rejected':
                return 'rejected';
            case 'cancelled':
                return 'cancelled';
            default:
                return 'pending';
        }
    }
}

// Helper function สำหรับแสดงสถานะเป็นภาษาไทย
if (!function_exists('get_esv_status_display')) {
    function get_esv_status_display($status)
    {
        switch ($status) {
            case 'pending':
                return 'รอดำเนินการ';
            case 'processing':
                return 'กำลังดำเนินการ';
            case 'completed':
                return 'เสร็จสิ้น';
            case 'rejected':
                return 'ไม่อนุมัติ';
            case 'cancelled':
                return 'ยกเลิก';
            default:
                return 'รอดำเนินการ';
        }
    }
}

// Helper function สำหรับแสดงประเภทผู้ใช้
if (!function_exists('get_esv_user_type_display')) {
    function get_esv_user_type_display($type)
    {
        switch ($type) {
            case 'guest':
                return 'ผู้ใช้ทั่วไป';
            case 'public':
                return 'สมาชิก';
            case 'staff':
                return 'เจ้าหน้าที่';
            default:
                return 'ผู้ใช้ทั่วไป';
        }
    }
}

// Helper function สำหรับแสดงระดับความสำคัญ
if (!function_exists('get_esv_priority_display')) {
    function get_esv_priority_display($priority)
    {
        switch ($priority) {
            case 'normal':
                return 'ปกติ';
            case 'urgent':
                return 'เร่งด่วน';
            case 'very_urgent':
                return 'เร่งด่วนมาก';
            default:
                return 'ปกติ';
        }
    }
}

// ตรวจสอบข้อมูลการค้นหา
$search_performed = isset($search_performed) ? $search_performed : false;
$esv_document_info = isset($esv_document_info) ? $esv_document_info : null;
$tracking_code = isset($tracking_code) ? $tracking_code : '';
$error_message = isset($error_message) ? $error_message : '';
?>

<!-- SweetAlert2 -->
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<!-- Font Awesome -->
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
<!-- html2pdf for PDF generation -->
<script src="https://cdnjs.cloudflare.com/ajax/libs/html2pdf.js/0.10.1/html2pdf.bundle.min.js"></script>

<style>
    /* ===== ESV TRACK PAGE STYLES ===== */
    .esv-track-page {
        --primary-color: #667eea;
        --primary-light: #764ba2;
        --secondary-color: #e8f2ff;
        --success-color: #28a745;
        --warning-color: #ffc107;
        --danger-color: #dc3545;
        --info-color: #17a2b8;
        --purple-color: #6f42c1;
        --light-bg: #f8f9fa;
        --white: #ffffff;
        --gray-50: #f9fafb;
        --gray-100: #f3f4f6;
        --gray-200: #e5e7eb;
        --gray-300: #d1d5db;
        --gray-400: #9ca3af;
        --gray-500: #6b7280;
        --gray-600: #4b5563;
        --gray-700: #374151;
        --gray-800: #1f2937;
        --gray-900: #111827;
        --shadow-sm: 0 1px 2px 0 rgb(0 0 0 / 0.05);
        --shadow: 0 1px 3px 0 rgb(0 0 0 / 0.1), 0 1px 2px -1px rgb(0 0 0 / 0.1);
        --shadow-md: 0 4px 6px -1px rgb(0 0 0 / 0.1), 0 2px 4px -2px rgb(0 0 0 / 0.1);
        --shadow-lg: 0 10px 15px -3px rgb(0 0 0 / 0.1), 0 4px 6px -4px rgb(0 0 0 / 0.1);
        --shadow-xl: 0 20px 25px -5px rgb(0 0 0 / 0.1), 0 8px 10px -6px rgb(0 0 0 / 0.1);
        --border-radius: 12px;
        --border-radius-lg: 16px;
        --transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
    }

    .esv-track-page {
        font-family: 'Inter', 'Segoe UI', system-ui, -apple-system, sans-serif;
        line-height: 1.6;
        color: var(--gray-700);
        min-height: 100vh;
        padding: 2rem 0;
    }

    .track-container {
        max-width: 900px;
        margin: 0 auto;
        padding: 0 1rem;
    }

    /* ===== HERO SECTION ===== */
    .track-hero {
        background: linear-gradient(135deg, rgba(102, 126, 234, 0.9) 0%, rgba(118, 75, 162, 0.8) 100%);
        color: white;
        padding: 3rem 2rem;
        border-radius: var(--border-radius-lg);
        margin-bottom: 2rem;
        box-shadow: var(--shadow-xl);
        text-align: center;
        position: relative;
        overflow: hidden;
    }

    .track-hero::before {
        content: '';
        position: absolute;
        top: -50%;
        right: -20%;
        width: 350px;
        height: 350px;
        background: radial-gradient(circle, rgba(255, 255, 255, 0.1) 0%, transparent 70%);
        border-radius: 50%;
    }

    .track-hero h1 {
        font-size: 2.5rem;
        font-weight: 700;
        margin: 0 0 1rem 0;
        text-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
        position: relative;
        z-index: 1;
    }

    .track-hero .subtitle {
        font-size: 1.2rem;
        opacity: 0.9;
        margin: 0 0 2rem 0;
        position: relative;
        z-index: 1;
    }

    .track-hero-icon {
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
        box-shadow: var(--shadow-lg);
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
        padding: 1.25rem 1.5rem;
        padding-left: 3.5rem;
        border: 2px solid var(--gray-200);
        border-radius: var(--border-radius);
        font-size: 1.1rem;
        transition: var(--transition);
        background-color: var(--white);
        font-weight: 500;
    }

    .search-input:focus {
        border-color: var(--primary-color);
        box-shadow: 0 0 0 3px rgba(102, 126, 234, 0.1);
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
        padding: 1.25rem 2rem;
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
        box-shadow: var(--shadow-md);
    }

    .search-btn:hover {
        transform: translateY(-2px);
        box-shadow: var(--shadow-xl);
    }

    .search-btn:disabled {
        background: var(--gray-300);
        cursor: not-allowed;
        transform: none;
        box-shadow: var(--shadow-sm);
    }

    .search-help {
        background: var(--secondary-color);
        border: 1px solid rgba(102, 126, 234, 0.3);
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
        box-shadow: var(--shadow-lg);
        border: 1px solid var(--gray-100);
        overflow: hidden;
        animation: fadeInUp 0.6s ease-out;
    }

    .result-header {
        background: linear-gradient(135deg, var(--secondary-color) 0%, #f0f4ff 100%);
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
        letter-spacing: 0.5px;
    }

    .result-body {
        padding: 2rem;
    }

    /* ===== INFO GRID ===== */
    .info-grid {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(280px, 1fr));
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
        padding: 0.875rem 1rem;
        background: var(--gray-50);
        border-radius: 8px;
        border: 1px solid var(--gray-200);
        font-weight: 500;
        transition: var(--transition);
    }

    .info-value.highlight {
        background: linear-gradient(135deg, var(--secondary-color), #f0f4ff);
        border-color: var(--primary-color);
        color: var(--primary-color);
        font-weight: 600;
    }

    .info-value:hover {
        transform: translateY(-1px);
        box-shadow: var(--shadow-sm);
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
        transition: var(--transition);
    }

    .status-badge.pending {
        background: linear-gradient(135deg, rgba(255, 193, 7, 0.15) 0%, rgba(255, 235, 59, 0.1) 100%);
        color: #f57c00;
        border: 2px solid rgba(255, 193, 7, 0.4);
    }

    .status-badge.processing {
        background: linear-gradient(135deg, rgba(23, 162, 184, 0.15) 0%, rgba(100, 181, 246, 0.1) 100%);
        color: #0277bd;
        border: 2px solid rgba(23, 162, 184, 0.4);
    }

    .status-badge.completed {
        background: linear-gradient(135deg, rgba(40, 167, 69, 0.15) 0%, rgba(129, 199, 132, 0.1) 100%);
        color: #1b5e20;
        border: 2px solid rgba(40, 167, 69, 0.4);
    }

    .status-badge.rejected {
        background: linear-gradient(135deg, rgba(220, 53, 69, 0.15) 0%, rgba(229, 115, 115, 0.1) 100%);
        color: #b71c1c;
        border: 2px solid rgba(220, 53, 69, 0.4);
    }

    .status-badge.cancelled {
        background: linear-gradient(135deg, rgba(108, 117, 125, 0.15) 0%, rgba(158, 158, 158, 0.1) 100%);
        color: #495057;
        border: 2px solid rgba(108, 117, 125, 0.4);
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

    /* ===== NO RESULT STATE ===== */
    .no-result {
        background: var(--white);
        border-radius: var(--border-radius);
        box-shadow: var(--shadow-lg);
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

    /* ===== ACTIONS SECTION ===== */
    .actions-section {
        background: var(--gray-50);
        border: 1px solid var(--gray-200);
        border-radius: var(--border-radius);
        padding: 1.5rem;
        margin-top: 2rem;
        text-align: center;
    }

    .actions-title {
        font-size: 1.1rem;
        font-weight: 700;
        color: var(--gray-900);
        margin-bottom: 1rem;
    }

    .action-buttons {
        display: flex;
        gap: 1rem;
        justify-content: center;
        flex-wrap: wrap;
    }

    .action-btn {
        padding: 0.75rem 1.5rem;
        border: none;
        border-radius: var(--border-radius);
        font-size: 0.95rem;
        font-weight: 600;
        cursor: pointer;
        transition: var(--transition);
        text-decoration: none;
        display: inline-flex;
        align-items: center;
        gap: 0.5rem;
    }

    .action-btn.primary {
        background: linear-gradient(135deg, var(--primary-color), var(--primary-light));
        color: white;
        box-shadow: var(--shadow-md);
    }

    .action-btn.secondary {
        background: var(--white);
        color: var(--gray-700);
        border: 2px solid var(--gray-300);
    }

    .action-btn:hover {
        transform: translateY(-2px);
        box-shadow: var(--shadow-lg);
    }

    /* ===== RESPONSIVE DESIGN ===== */
    @media (max-width: 768px) {
        .esv-track-page {
            padding: 1rem 0;
        }

        .track-container {
            padding: 0 0.5rem;
        }

        .track-hero {
            padding: 2rem 1.5rem;
            margin-bottom: 1.5rem;
        }

        .track-hero h1 {
            font-size: 2rem;
        }

        .track-hero .subtitle {
            font-size: 1rem;
        }

        .search-section {
            padding: 1.5rem;
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

        .action-buttons {
            flex-direction: column;
            align-items: center;
        }

        .action-btn {
            width: 100%;
            max-width: 300px;
            justify-content: center;
        }
    }

    /* ===== ANIMATIONS ===== */
    @keyframes fadeInUp {
        from {
            opacity: 0;
            transform: translateY(30px);
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

<div class="esv-track-page">
    <div class="track-container">
        <!-- ===== HERO SECTION ===== -->
        <div class="track-hero">
            <div class="track-hero-icon">
                <i class="fas fa-file-search"></i>
            </div>
            <h1>ติดตามสถานะเอกสารออนไลน์</h1>
            <p class="subtitle">ตรวจสอบสถานะการยื่นเอกสารออนไลน์ของคุณ</p>
        </div>

        <!-- ===== SEARCH SECTION ===== -->
        <div class="search-section">
            <h2 class="search-title">
                <i class="fas fa-search"></i>
                ค้นหาเอกสารของคุณ
            </h2>

            <form class="search-form" id="searchForm" method="POST" action="<?= site_url('Esv_ods/track') ?>">
                <div class="search-input-group">
                    <i class="fas fa-hashtag search-input-icon"></i>
                    <input type="text" class="search-input" name="tracking_code" id="searchInput"
                        placeholder="กรอกรหัสติดตาม เช่น ESV67001234" value="<?= htmlspecialchars($tracking_code) ?>"
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
                        ใช้รหัสติดตามที่ได้รับหลังยื่นเอกสาร
                    </li>
                    <li class="search-help-item">
                        <i class="fas fa-check search-help-icon"></i>
                        รหัสติดตามขึ้นต้นด้วย "ESV" ตามด้วยตัวเลข
                    </li>
                    <li class="search-help-item">
                        <i class="fas fa-check search-help-icon"></i>
                        ตัวอย่าง: ESV67001234, ESV67001235
                    </li>
                    <li class="search-help-item">
                        <i class="fas fa-exclamation-triangle search-help-icon"></i>
                        ระบบค้นหาได้เฉพาะเอกสารของผู้ใช้ทั่วไป (Guest) เท่านั้น
                    </li>
                    <li class="search-help-item">
                        <i class="fas fa-user search-help-icon"></i>
                        หากคุณเป็นสมาชิก กรุณาเข้าสู่ระบบเพื่อดูเอกสารของคุณ
                    </li>
                </ul>
            </div>
        </div>

        <!-- ===== RESULT SECTION ===== -->
        <?php if ($search_performed): ?>
            <div class="result-section">
                <?php if ($esv_document_info && isset($esv_document_info->esv_ods_id)): ?>
                    <!-- มีผลลัพธ์ -->
                    <div class="result-card">
                        <div class="result-header">
                            <h3 class="result-title">
                                <i class="fas fa-file-alt"></i>
                                ข้อมูลเอกสาร
                            </h3>
                            <div class="result-id"><?= htmlspecialchars($esv_document_info->esv_ods_reference_id) ?></div>
                        </div>

                        <div class="result-body">
                            <!-- ข้อมูลพื้นฐาน -->
                            <div class="info-grid">
                                <div class="info-item">
                                    <span class="info-label">รหัสติดตาม</span>
                                    <div class="info-value highlight">
                                        <?= htmlspecialchars($esv_document_info->esv_ods_reference_id) ?>
                                    </div>
                                </div>
                                <div class="info-item">
                                    <span class="info-label">เรื่อง</span>
                                    <div class="info-value">
                                        <?= htmlspecialchars($esv_document_info->esv_ods_topic) ?>
                                    </div>
                                </div>
                                <div class="info-item">
                                    <span class="info-label">ผู้ยื่นเรื่อง</span>
                                    <div class="info-value">
                                        <?= htmlspecialchars($esv_document_info->esv_ods_by) ?>
                                    </div>
                                </div>
                                <div class="info-item">
                                    <span class="info-label">เบอร์โทรศัพท์</span>
                                    <div class="info-value">
                                        <a href="tel:<?= htmlspecialchars($esv_document_info->esv_ods_phone) ?>"
                                            style="color: var(--primary-color); text-decoration: none;">
                                            <?= htmlspecialchars($esv_document_info->esv_ods_phone) ?>
                                        </a>
                                    </div>
                                </div>
                                <div class="info-item">
                                    <span class="info-label">อีเมล</span>
                                    <div class="info-value">
                                        <a href="mailto:<?= htmlspecialchars($esv_document_info->esv_ods_email) ?>"
                                            style="color: var(--primary-color); text-decoration: none;">
                                            <?= htmlspecialchars($esv_document_info->esv_ods_email) ?>
                                        </a>
                                    </div>
                                </div>
                                <div class="info-item">
                                    <span class="info-label">วันที่ยื่นเรื่อง</span>
                                    <div class="info-value">
                                        <?php
                                        if (!empty($esv_document_info->esv_ods_datesave)) {
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

                                            $date = date('j', strtotime($esv_document_info->esv_ods_datesave));
                                            $month = $thai_months[date('m', strtotime($esv_document_info->esv_ods_datesave))];
                                            $year = date('Y', strtotime($esv_document_info->esv_ods_datesave)) + 543;
                                            $time = date('H:i', strtotime($esv_document_info->esv_ods_datesave));

                                            echo $date . ' ' . $month . ' ' . $year . ' เวลา ' . $time . ' น.';
                                        }
                                        ?>
                                    </div>
                                </div>
                                <?php if (!empty($esv_document_info->esv_ods_updated_at)): ?>
                                    <div class="info-item">
                                        <span class="info-label">อัปเดตล่าสุด</span>
                                        <div class="info-value">
                                            <?php
                                            $date = date('j', strtotime($esv_document_info->esv_ods_updated_at));
                                            $month = $thai_months[date('m', strtotime($esv_document_info->esv_ods_updated_at))];
                                            $year = date('Y', strtotime($esv_document_info->esv_ods_updated_at)) + 543;
                                            $time = date('H:i', strtotime($esv_document_info->esv_ods_updated_at));

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
                                    <div class="status-badge <?= get_esv_status_class($esv_document_info->esv_ods_status) ?>">
                                        <?= get_esv_status_display($esv_document_info->esv_ods_status) ?>
                                    </div>
                                </div>

                                <div class="status-description">
                                    <?php
                                    $status_descriptions = [
                                        'pending' => 'เอกสารของท่านได้รับการยื่นเรียบร้อยแล้ว กำลังรอเจ้าหน้าที่ตรวจสอบ',
                                        'processing' => 'เจ้าหน้าที่กำลังดำเนินการตรวจสอบเอกสารของท่าน กรุณารอผลการดำเนินการ',
                                        'completed' => 'เอกสารของท่านได้รับการอนุมัติและดำเนินการเสร็จสิ้นแล้ว',
                                        'rejected' => 'เอกสารของท่านไม่ได้รับการอนุมัติ กรุณาติดต่อเจ้าหน้าที่เพื่อสอบถามรายละเอียด',
                                        'cancelled' => 'เอกสารของท่านถูกยกเลิก'
                                    ];

                                    echo $status_descriptions[$esv_document_info->esv_ods_status] ?? 'สถานะไม่ทราบ';
                                    ?>

                                    <?php if (!empty($esv_document_info->esv_ods_response)): ?>
                                        <div style="margin-top: 1rem; padding-top: 1rem; border-top: 1px solid var(--gray-200);">
                                            <strong>หมายเหตุจากเจ้าหน้าที่:</strong><br>
                                            <?= nl2br(htmlspecialchars($esv_document_info->esv_ods_response)) ?>
                                        </div>
                                    <?php endif; ?>
                                </div>
                            </div>

                            <!-- รายละเอียดเอกสาร -->
                            <div class="status-section">
                                <div class="status-title">
                                    <i class="fas fa-align-left"></i>
                                    รายละเอียดเอกสาร
                                </div>
                                <div
                                    style="margin-top: 1rem; padding: 1rem; background: var(--white); border-radius: 8px; border: 1px solid var(--gray-200);">
                                    <?= nl2br(htmlspecialchars($esv_document_info->esv_ods_detail)) ?>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- ปุ่มดำเนินการ -->
                    <div class="actions-section">
                        <div class="actions-title">
                            <i class="fas fa-tools"></i>
                            ดำเนินการเพิ่มเติม
                        </div>
                        <div class="action-buttons">
                            <button onclick="printDocument()" class="action-btn secondary">
                                <i class="fas fa-print"></i>
                                พิมพ์ใบติดตาม
                            </button>
                            <a href="<?= site_url('Esv_ods/submit_document') ?>" class="action-btn primary">
                                <i class="fas fa-plus"></i>
                                ยื่นเอกสารใหม่
                            </a>
                        </div>
                    </div>

                <?php else: ?>
                    <!-- ไม่พบผลลัพธ์ -->
                    <div class="no-result">
                        <div class="no-result-icon">
                            <i class="fas fa-search-minus"></i>
                        </div>
                        <h3 class="no-result-title">ไม่พบเอกสารที่ค้นหา</h3>
                        <p class="no-result-message">
                            ไม่พบรหัสติดตาม <strong><?= htmlspecialchars($tracking_code) ?></strong> ในระบบ<br>
                            <?php if (!empty($error_message)): ?>
                                <?= htmlspecialchars($error_message) ?>
                            <?php else: ?>
                                หรือไม่ใช่เอกสารของผู้ใช้ทั่วไป (Guest)
                            <?php endif; ?>
                        </p>

                        <div class="no-result-suggestions">
                            <div class="suggestions-title">กรุณาตรวจสอบ:</div>
                            <ul class="suggestions-list">
                                <li class="suggestions-item">รหัสติดตามที่กรอกถูกต้องหรือไม่</li>
                                <li class="suggestions-item">รหัสขึ้นต้นด้วย "ESV" ตามด้วยตัวเลข</li>
                                <li class="suggestions-item">เอกสารเป็นของผู้ใช้ทั่วไป (Guest) หรือไม่</li>
                                <li class="suggestions-item">หากเป็นสมาชิก กรุณาเข้าสู่ระบบเพื่อดูเอกสาร</li>
                                <li class="suggestions-item">ลองค้นหาใหม่อีกครั้งหรือติดต่อเจ้าหน้าที่</li>
                            </ul>
                        </div>
                    </div>
                <?php endif; ?>
            </div>
        <?php endif; ?>
    </div>
</div>

<script>
    // ===================================================================
    // *** CONFIGURATION & VARIABLES ***
    // ===================================================================

    const EsvTrackConfig = {
        baseUrl: '<?= site_url() ?>',
        currentUrl: '<?= current_url() ?>',
        debug: <?= (defined('ENVIRONMENT') && ENVIRONMENT === 'development') ? 'true' : 'false' ?>
    };

    // *** เพิ่ม: Debug reCAPTCHA variables ตั้งแต่เริ่มต้น ***
    console.log('🔑 Initial reCAPTCHA check for ESV Track:');
    console.log('- RECAPTCHA_SITE_KEY:', typeof window.RECAPTCHA_SITE_KEY !== 'undefined' ? window.RECAPTCHA_SITE_KEY : 'UNDEFINED');
    console.log('- recaptchaReady:', typeof window.recaptchaReady !== 'undefined' ? window.recaptchaReady : 'UNDEFINED');
    console.log('- SKIP_RECAPTCHA_FOR_DEV:', typeof window.SKIP_RECAPTCHA_FOR_DEV !== 'undefined' ? window.SKIP_RECAPTCHA_FOR_DEV : 'UNDEFINED');
    console.log('- grecaptcha available:', typeof grecaptcha !== 'undefined');

    // ===================================================================
    // *** CORE FUNCTIONS ***
    // ===================================================================

    /**
     * จัดการ Form Submit - เพิ่ม reCAPTCHA integration
     */
    function handleFormSubmit() {
        const form = document.getElementById('searchForm');
        const searchBtn = document.getElementById('searchBtn');
        const searchInput = document.getElementById('searchInput');

        if (!form || !searchBtn || !searchInput) return;

        form.addEventListener('submit', function (e) {
            e.preventDefault(); // ป้องกันการ submit แบบปกติ

            const searchValue = searchInput.value.trim();

            if (!searchValue) {
                Swal.fire({
                    title: 'กรุณากรอกรหัสติดตาม',
                    text: 'กรุณากรอกรหัสติดตามเพื่อค้นหาเอกสาร',
                    icon: 'warning',
                    confirmButtonText: 'ตกลง',
                    confirmButtonColor: '#667eea'
                });

                searchInput.focus();
                return;
            }

            console.log('📝 ESV Track search submitted - Code:', searchValue);

            // แสดง loading state
            searchBtn.classList.add('loading');
            searchBtn.disabled = true;

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
                    console.log('🔧 grecaptcha.ready() called for ESV track');

                    grecaptcha.execute(window.RECAPTCHA_SITE_KEY, {
                        action: 'esv_track_search'
                    }).then(function (token) {
                        console.log('✅ reCAPTCHA token received for ESV track:', token.substring(0, 50) + '...');
                        console.log('📏 Token length:', token.length);

                        performSearchWithRecaptcha(searchValue, token);
                    }).catch(function (error) {
                        console.error('❌ reCAPTCHA execution failed for ESV track:', error);
                        console.log('🔄 Falling back to search without reCAPTCHA');
                        performSearchWithoutRecaptcha(searchValue);
                    });
                });
            } else {
                console.log('⚠️ reCAPTCHA not available, searching without verification');
                console.log('📋 Reasons breakdown:');
                console.log('- SITE_KEY exists:', !!window.RECAPTCHA_SITE_KEY);
                console.log('- reCAPTCHA ready:', !!window.recaptchaReady);
                console.log('- Skip dev mode:', !!window.SKIP_RECAPTCHA_FOR_DEV);
                console.log('- grecaptcha available:', typeof grecaptcha !== 'undefined');

                performSearchWithoutRecaptcha(searchValue);
            }
        });
    }

    /**
     * ฟังก์ชันค้นหาด้วย AJAX พร้อม reCAPTCHA
     */
    function performSearchWithRecaptcha(trackingCode, recaptchaToken) {
        console.log('📤 Submitting ESV track search with reCAPTCHA token...');

        const resultSection = document.querySelector('.result-section');

        // ซ่อนผลลัพธ์เก่า
        if (resultSection) {
            resultSection.style.display = 'none';
        }

        // เตรียม request body พร้อม reCAPTCHA token
        const requestBody = new URLSearchParams({
            'tracking_code': trackingCode,
            'g-recaptcha-response': recaptchaToken,
            'recaptcha_action': 'esv_track_search',
            'recaptcha_source': 'esv_track_form',
            'user_type_detected': 'guest'
        });

        // เพิ่ม CSRF token ถ้ามี
        const csrfMeta = document.querySelector('meta[name="csrf-token"]');
        if (csrfMeta) {
            requestBody.append(csrfMeta.getAttribute('name'), csrfMeta.getAttribute('content'));
        }

        // ส่ง AJAX request
        fetch('<?= site_url("Esv_ods/search_document") ?>', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/x-www-form-urlencoded',
                'X-Requested-With': 'XMLHttpRequest'
            },
            body: requestBody.toString()
        })
            .then(response => response.json())
            .then(data => {
                handleSearchResponse(data, trackingCode);
            })
            .catch(error => {
                console.error('ESV track search with reCAPTCHA error:', error);
                handleSearchError(error);
            })
            .finally(() => {
                restoreSearchButton();
            });
    }

    /**
     * ฟังก์ชันค้นหาด้วย AJAX แบบปกติ (เดิม)
     */
    function performSearchWithoutRecaptcha(trackingCode) {
        console.log('📤 Submitting ESV track search without reCAPTCHA...');

        const resultSection = document.querySelector('.result-section');

        // ซ่อนผลลัพธ์เก่า
        if (resultSection) {
            resultSection.style.display = 'none';
        }

        // ส่ง AJAX request แบบเดิม
        fetch('<?= site_url("Esv_ods/search_document") ?>', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/x-www-form-urlencoded',
                'X-Requested-With': 'XMLHttpRequest'
            },
            body: 'tracking_code=' + encodeURIComponent(trackingCode)
        })
            .then(response => response.json())
            .then(data => {
                handleSearchResponse(data, trackingCode);
            })
            .catch(error => {
                console.error('ESV track search without reCAPTCHA error:', error);
                handleSearchError(error);
            })
            .finally(() => {
                restoreSearchButton();
            });
    }

    /**
     * จัดการ Response ของการค้นหา
     */
    function handleSearchResponse(data, trackingCode) {
        if (data.success && data.data) {
            // แสดงผลลัพธ์สำเร็จ
            displaySearchResult(data.data, trackingCode);
        } else {
            // แสดงผลลัพธ์ไม่พบ
            displayNoResult(data.message || 'ไม่พบเอกสารที่ต้องการ', trackingCode);
        }

        // อัปเดต URL โดยไม่รีเฟรชหน้า
        const newUrl = new URL(window.location);
        newUrl.searchParams.set('code', trackingCode);
        window.history.pushState({ trackingCode }, '', newUrl);
    }

    /**
     * จัดการ Error ของการค้นหา
     */
    function handleSearchError(error) {
        console.error('ESV track search error:', error);
        Swal.fire({
            title: 'เกิดข้อผิดพลาด',
            text: 'ไม่สามารถค้นหาได้ในขณะนี้ กรุณาลองใหม่อีกครั้ง',
            icon: 'error',
            confirmButtonText: 'ตกลง',
            confirmButtonColor: '#667eea'
        });
    }

    /**
     * คืนค่าปุ่มเป็นสถานะเดิม
     */
    function restoreSearchButton() {
        const searchBtn = document.getElementById('searchBtn');
        if (searchBtn) {
            searchBtn.classList.remove('loading');
            searchBtn.disabled = false;
        }
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

    /**
     * ฟังก์ชันค้นหาด้วย AJAX (เดิม - เรียกใช้ function ใหม่)
     */
    function performSearch(trackingCode) {
        // เรียกใช้ function ใหม่แทน
        const searchInput = document.getElementById('searchInput');
        const form = document.getElementById('searchForm');

        if (form && searchInput) {
            searchInput.value = trackingCode;
            form.dispatchEvent(new Event('submit'));
        }
    }

    /**
     * แสดงผลลัพธ์ที่พบ
     */
    function displaySearchResult(documentData, trackingCode) {
        const container = document.querySelector('.track-container');
        const existingResult = document.querySelector('.result-section');

        if (existingResult) {
            existingResult.remove();
        }

        const resultHtml = createResultHTML(documentData.document, trackingCode);
        container.insertAdjacentHTML('beforeend', resultHtml);

        // เพิ่ม animation
        const newResult = document.querySelector('.result-section');
        if (newResult) {
            newResult.style.opacity = '0';
            newResult.style.transform = 'translateY(30px)';
            setTimeout(() => {
                newResult.style.transition = 'all 0.6s ease';
                newResult.style.opacity = '1';
                newResult.style.transform = 'translateY(0)';
            }, 100);
        }

        // เพิ่ม functionality สำหรับปุ่มต่างๆ
        initializeResultActions();
    }

    /**
     * แสดงผลลัพธ์ไม่พบ
     */
    function displayNoResult(message, trackingCode) {
        const container = document.querySelector('.track-container');
        const existingResult = document.querySelector('.result-section');

        if (existingResult) {
            existingResult.remove();
        }

        const noResultHtml = createNoResultHTML(message, trackingCode);
        container.insertAdjacentHTML('beforeend', noResultHtml);

        // เพิ่ม animation
        const newResult = document.querySelector('.result-section');
        if (newResult) {
            newResult.style.opacity = '0';
            newResult.style.transform = 'translateY(30px)';
            setTimeout(() => {
                newResult.style.transition = 'all 0.6s ease';
                newResult.style.opacity = '1';
                newResult.style.transform = 'translateY(0)';
            }, 100);
        }
    }

    /**
     * สร้าง HTML สำหรับผลลัพธ์ที่พบ
     */
    function createResultHTML(document, trackingCode) {
        const thai_months = {
            '01': 'มกราคม', '02': 'กุมภาพันธ์', '03': 'มีนาคม', '04': 'เมษายน',
            '05': 'พฤษภาคม', '06': 'มิถุนายน', '07': 'กรกฎาคม', '08': 'สิงหาคม',
            '09': 'กันยายน', '10': 'ตุลาคม', '11': 'พฤศจิกายน', '12': 'ธันวาคม'
        };

        function formatThaiDate(dateString) {
            if (!dateString) return '';
            const date = new Date(dateString);
            const day = date.getDate();
            const month = thai_months[String(date.getMonth() + 1).padStart(2, '0')];
            const year = date.getFullYear() + 543;
            const time = date.toTimeString().substr(0, 5);
            return `${day} ${month} ${year} เวลา ${time} น.`;
        }

        const statusDescriptions = {
            'pending': 'เอกสารของท่านได้รับการยื่นเรียบร้อยแล้ว กำลังรอเจ้าหน้าที่ตรวจสอบ',
            'processing': 'เจ้าหน้าที่กำลังดำเนินการตรวจสอบเอกสารของท่าน กรุณารอผลการดำเนินการ',
            'completed': 'เอกสารของท่านได้รับการอนุมัติและดำเนินการเสร็จสิ้นแล้ว',
            'rejected': 'เอกสารของท่านไม่ได้รับการอนุมัติ กรุณาติดต่อเจ้าหน้าที่เพื่อสอบถามรายละเอียด',
            'cancelled': 'เอกสารของท่านถูกยกเลิก'
        };

        return `
        <div class="result-section">
            <div class="result-card">
                <div class="result-header">
                    <h3 class="result-title">
                        <i class="fas fa-file-alt"></i>
                        ข้อมูลเอกสาร
                    </h3>
                    <div class="result-id">${document.esv_ods_reference_id}</div>
                </div>
                
                <div class="result-body">
                    <div class="info-grid">
                        <div class="info-item">
                            <span class="info-label">รหัสติดตาม</span>
                            <div class="info-value highlight">
                                ${document.esv_ods_reference_id}
                            </div>
                        </div>
                        <div class="info-item">
                            <span class="info-label">เรื่อง</span>
                            <div class="info-value">
                                ${document.esv_ods_topic}
                            </div>
                        </div>
                        <div class="info-item">
                            <span class="info-label">ผู้ยื่นเรื่อง</span>
                            <div class="info-value">
                                ${document.esv_ods_by}
                            </div>
                        </div>
                        <div class="info-item">
                            <span class="info-label">เบอร์โทรศัพท์</span>
                            <div class="info-value">
                                <a href="tel:${document.esv_ods_phone}" 
                                   style="color: var(--primary-color); text-decoration: none;">
                                    ${document.esv_ods_phone}
                                </a>
                            </div>
                        </div>
                        <div class="info-item">
                            <span class="info-label">อีเมล</span>
                            <div class="info-value">
                                <a href="mailto:${document.esv_ods_email}" 
                                   style="color: var(--primary-color); text-decoration: none;">
                                    ${document.esv_ods_email}
                                </a>
                            </div>
                        </div>
                        <div class="info-item">
                            <span class="info-label">วันที่ยื่นเรื่อง</span>
                            <div class="info-value">
                                ${formatThaiDate(document.esv_ods_datesave)}
                            </div>
                        </div>
                    </div>

                    <div class="status-section">
                        <div class="status-header">
                            <div class="status-title">
                                <i class="fas fa-traffic-light"></i>
                                สถานะปัจจุบัน
                            </div>
                            <div class="status-badge ${getStatusClass(document.esv_ods_status)}">
                                ${getStatusDisplay(document.esv_ods_status)}
                            </div>
                        </div>
                        
                        <div class="status-description">
                            ${statusDescriptions[document.esv_ods_status] || 'สถานะไม่ทราบ'}
                            ${document.esv_ods_response ? `
                                <div style="margin-top: 1rem; padding-top: 1rem; border-top: 1px solid var(--gray-200);">
                                    <strong>หมายเหตุจากเจ้าหน้าที่:</strong><br>
                                    ${document.esv_ods_response.replace(/\n/g, '<br>')}
                                </div>
                            ` : ''}
                        </div>
                    </div>

                    <div class="status-section">
                        <div class="status-title">
                            <i class="fas fa-align-left"></i>
                            รายละเอียดเอกสาร
                        </div>
                        <div style="margin-top: 1rem; padding: 1rem; background: var(--white); border-radius: 8px; border: 1px solid var(--gray-200);">
                            ${document.esv_ods_detail.replace(/\n/g, '<br>')}
                        </div>
                    </div>
                </div>
            </div>

            <div class="actions-section">
                <div class="actions-title">
                    <i class="fas fa-tools"></i>
                    ดำเนินการเพิ่มเติม
                </div>
                <div class="action-buttons">
                    <button onclick="printDocument()" class="action-btn secondary" title="พิมพ์ใบติดตามในรูปแบบ A4">
                        <i class="fas fa-print"></i>
                        พิมพ์ใบติดตาม A4
                    </button>
                    <button onclick="downloadPDF()" class="action-btn secondary" title="ดาวน์โหลดใบติดตามเป็นไฟล์ PDF">
                        <i class="fas fa-file-pdf"></i>
                        ดาวน์โหลด PDF
                    </button>
                    <a href="<?= site_url('Esv_ods/submit_document') ?>" class="action-btn primary">
                        <i class="fas fa-plus"></i>
                        ยื่นเอกสารใหม่
                    </a>
                </div>
            </div>
        </div>
    `;
    }

    /**
     * สร้าง HTML สำหรับผลลัพธ์ไม่พบ
     */
    function createNoResultHTML(message, trackingCode) {
        return `
        <div class="result-section">
            <div class="no-result">
                <div class="no-result-icon">
                    <i class="fas fa-search-minus"></i>
                </div>
                <h3 class="no-result-title">ไม่พบเอกสารที่ค้นหา</h3>
                <p class="no-result-message">
                    ไม่พบรหัสติดตาม <strong>${trackingCode}</strong> ในระบบ<br>
                    ${message}
                </p>
                
                <div class="no-result-suggestions">
                    <div class="suggestions-title">กรุณาตรวจสอบ:</div>
                    <ul class="suggestions-list">
                        <li class="suggestions-item">รหัสติดตามที่กรอกถูกต้องหรือไม่</li>
                        <li class="suggestions-item">รหัสขึ้นต้นด้วย "ESV" ตามด้วยตัวเลข</li>
                        <li class="suggestions-item">เอกสารเป็นของผู้ใช้ทั่วไป (Guest) หรือไม่</li>
                        <li class="suggestions-item">หากเป็นสมาชิก กรุณาเข้าสู่ระบบเพื่อดูเอกสาร</li>
                        <li class="suggestions-item">ลองค้นหาใหม่อีกครั้งหรือติดต่อเจ้าหน้าที่</li>
                    </ul>
                </div>
            </div>
        </div>
    `;
    }

    /**
     * เริ่มต้น functionality สำหรับปุ่มในผลลัพธ์
     */
    function initializeResultActions() {
        // เพิ่ม copy functionality สำหรับรหัสติดตาม
        const resultId = document.querySelector('.result-id');
        if (resultId) {
            resultId.style.cursor = 'pointer';
            resultId.title = 'คลิกเพื่อคัดลอกรหัสติดตาม';

            resultId.addEventListener('click', function () {
                const text = this.textContent;

                if (navigator.clipboard) {
                    navigator.clipboard.writeText(text).then(() => {
                        Swal.fire({
                            title: 'คัดลอกแล้ว!',
                            text: `คัดลอกรหัส ${text} เรียบร้อยแล้ว`,
                            icon: 'success',
                            timer: 2000,
                            showConfirmButton: false,
                            confirmButtonColor: '#667eea'
                        });
                    });
                }
            });
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

            // Auto-suggest รูปแบบ ESV
            if (value.length > 0 && !value.startsWith('ESV')) {
                if (value.match(/^\d/)) {
                    e.target.value = 'ESV' + value;
                }
            }
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
        // Auto refresh ทุก 2 นาที หากมีผลลัพธ์และสถานะยังไม่เสร็จ
        const resultSection = document.querySelector('.result-section');
        const statusBadge = document.querySelector('.status-badge');

        if (resultSection && statusBadge) {
            const currentStatus = statusBadge.className.split(' ').pop();

            // Refresh หากสถานะยัง pending หรือ processing
            if (currentStatus === 'pending' || currentStatus === 'processing') {
                console.log('Setting up auto-refresh for status:', currentStatus);

                setInterval(() => {
                    refreshResults();
                }, 120000); // 2 minutes
            }
        }
    }

    /**
     * รีเฟรชผลลัพธ์
     */
    function refreshResults() {
        const searchInput = document.getElementById('searchInput');
        const currentCode = searchInput ? searchInput.value.trim() : '';

        if (!currentCode) return;

        console.log('Auto refreshing results for:', currentCode);

        // เพิ่ม loading indicator แบบเบาๆ
        const resultSection = document.querySelector('.result-section');
        if (resultSection) {
            resultSection.classList.add('loading');
        }

        // ส่ง AJAX request เพื่อ refresh
        fetch('<?= site_url("Esv_ods/search_document") ?>', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/x-www-form-urlencoded',
                'X-Requested-With': 'XMLHttpRequest'
            },
            body: 'tracking_code=' + encodeURIComponent(currentCode)
        })
            .then(response => response.json())
            .then(data => {
                if (data.success && data.data) {
                    // อัปเดตเฉพาะสถานะและข้อมูลที่เปลี่ยนแปลง
                    updateDocumentStatus(data.data);
                }
            })
            .catch(error => {
                console.error('Auto refresh error:', error);
            })
            .finally(() => {
                if (resultSection) {
                    resultSection.classList.remove('loading');
                }
            });
    }

    /**
     * อัปเดตสถานะเอกสาร
     */
    function updateDocumentStatus(documentData) {
        // อัปเดต status badge
        const statusBadge = document.querySelector('.status-badge');
        if (statusBadge && documentData.document) {
            const newStatus = documentData.document.esv_ods_status;
            const newStatusDisplay = getStatusDisplay(newStatus);

            statusBadge.className = `status-badge ${getStatusClass(newStatus)}`;
            statusBadge.textContent = newStatusDisplay;
        }

        // อัปเดต status description
        const statusDescription = document.querySelector('.status-description');
        if (statusDescription && documentData.document) {
            const descriptions = {
                'pending': 'เอกสารของท่านได้รับการยื่นเรียบร้อยแล้ว กำลังรอเจ้าหน้าที่ตรวจสอบ',
                'processing': 'เจ้าหน้าที่กำลังดำเนินการตรวจสอบเอกสารของท่าน กรุณารอผลการดำเนินการ',
                'completed': 'เอกสารของท่านได้รับการอนุมัติและดำเนินการเสร็จสิ้นแล้ว',
                'rejected': 'เอกสารของท่านไม่ได้รับการอนุมัติ กรุณาติดต่อเจ้าหน้าที่เพื่อสอบถามรายละเอียด',
                'cancelled': 'เอกสารของท่านถูกยกเลิก'
            };

            statusDescription.innerHTML = descriptions[documentData.document.esv_ods_status] || 'สถานะไม่ทราบ';

            // เพิ่มหมายเหตุจากเจ้าหน้าที่ถ้ามี
            if (documentData.document.esv_ods_response) {
                statusDescription.innerHTML += `
                <div style="margin-top: 1rem; padding-top: 1rem; border-top: 1px solid var(--gray-200);">
                    <strong>หมายเหตุจากเจ้าหน้าที่:</strong><br>
                    ${documentData.document.esv_ods_response.replace(/\n/g, '<br>')}
                </div>
            `;
            }
        }

        console.log('Document status updated:', documentData.document.esv_ods_status);
    }

    /**
     * Helper functions สำหรับสถานะ
     */
    function getStatusClass(status) {
        const classes = {
            'pending': 'pending',
            'processing': 'processing',
            'completed': 'completed',
            'rejected': 'rejected',
            'cancelled': 'cancelled'
        };
        return classes[status] || 'pending';
    }

    function getStatusDisplay(status) {
        const displays = {
            'pending': 'รอดำเนินการ',
            'processing': 'กำลังดำเนินการ',
            'completed': 'เสร็จสิ้น',
            'rejected': 'ไม่อนุมัติ',
            'cancelled': 'ยกเลิก'
        };
        return displays[status] || 'รอดำเนินการ';
    }

    /**
     * จัดการ URL Parameters
     */
    function handleUrlParameters() {
        const urlParams = new URLSearchParams(window.location.search);
        const codeParam = urlParams.get('code');
        const searchInput = document.getElementById('searchInput');

        if (codeParam && searchInput && !searchInput.value) {
            searchInput.value = codeParam.toUpperCase();
        }
    }

    /**
     * จัดการ Copy to Clipboard
     */
    function handleCopyFunctionality() {
        // เพิ่มปุ่ม copy สำหรับรหัสติดตาม
        const resultId = document.querySelector('.result-id');
        if (resultId) {
            resultId.style.cursor = 'pointer';
            resultId.title = 'คลิกเพื่อคัดลอกรหัสติดตาม';

            resultId.addEventListener('click', function () {
                const text = this.textContent;

                if (navigator.clipboard) {
                    navigator.clipboard.writeText(text).then(() => {
                        Swal.fire({
                            title: 'คัดลอกแล้ว!',
                            text: `คัดลอกรหัส ${text} เรียบร้อยแล้ว`,
                            icon: 'success',
                            timer: 2000,
                            showConfirmButton: false,
                            confirmButtonColor: '#667eea'
                        });
                    });
                } else {
                    // Fallback สำหรับ browser เก่า
                    const textArea = document.createElement('textarea');
                    textArea.value = text;
                    document.body.appendChild(textArea);
                    textArea.select();
                    document.execCommand('copy');
                    document.body.removeChild(textArea);

                    Swal.fire({
                        title: 'คัดลอกแล้ว!',
                        text: `คัดลอกรหัส ${text} เรียบร้อยแล้ว`,
                        icon: 'success',
                        timer: 2000,
                        showConfirmButton: false,
                        confirmButtonColor: '#667eea'
                    });
                }
            });
        }
    }

    /**
     * ฟังก์ชันดาวน์โหลดเป็น PDF
     */
    function downloadPDF() {
        const documentData = getDocumentData();

        if (!documentData) {
            Swal.fire({
                title: 'ไม่พบข้อมูล',
                text: 'ไม่พบข้อมูลเอกสารที่จะสร้าง PDF',
                icon: 'warning',
                confirmButtonColor: '#667eea'
            });
            return;
        }

        Swal.fire({
            title: 'กำลังสร้าง PDF',
            text: 'กรุณารอสักครู่...',
            icon: 'info',
            allowOutsideClick: false,
            showConfirmButton: false,
            didOpen: () => {
                Swal.showLoading();
            }
        });

        // ใช้ html2pdf library (ต้องโหลดเพิ่ม)
        if (typeof html2pdf !== 'undefined') {
            const printHTML = createPrintHTML(documentData);

            // สร้าง temporary div
            const tempDiv = document.createElement('div');
            tempDiv.innerHTML = printHTML;
            tempDiv.style.position = 'absolute';
            tempDiv.style.left = '-9999px';
            document.body.appendChild(tempDiv);

            const element = tempDiv.querySelector('.print-container');

            const opt = {
                margin: [10, 10, 10, 10],
                filename: `ใบติดตาม_${documentData.referenceId}.pdf`,
                image: { type: 'jpeg', quality: 0.98 },
                html2canvas: { scale: 2, useCORS: true },
                jsPDF: { unit: 'mm', format: 'a4', orientation: 'portrait' }
            };

            html2pdf().set(opt).from(element).save().then(() => {
                document.body.removeChild(tempDiv);
                Swal.fire({
                    title: 'สำเร็จ!',
                    text: 'ดาวน์โหลด PDF เรียบร้อยแล้ว',
                    icon: 'success',
                    timer: 2000,
                    showConfirmButton: false
                });
            }).catch(error => {
                document.body.removeChild(tempDiv);
                console.error('PDF generation error:', error);
                Swal.fire({
                    title: 'เกิดข้อผิดพลาด',
                    text: 'ไม่สามารถสร้าง PDF ได้',
                    icon: 'error',
                    confirmButtonColor: '#667eea'
                });
            });
        } else {
            // Fallback: ใช้การพิมพ์แทน
            Swal.close();
            Swal.fire({
                title: 'ไม่รองรับ PDF',
                text: 'ระบบไม่รองรับการสร้าง PDF โดยตรง กรุณาใช้ฟังก์ชันพิมพ์แทน',
                icon: 'warning',
                confirmButtonText: 'พิมพ์เอกสาร',
                confirmButtonColor: '#667eea'
            }).then((result) => {
                if (result.isConfirmed) {
                    printDocument();
                }
            });
        }
    }

    /**
     * ฟังก์ชันพิมพ์เอกสาร - แก้ไขเป็นขนาด A4
     */
    function printDocument() {
        // สร้างหน้าพิมพ์แยกต่างหาก
        const printWindow = window.open('', '_blank');

        // ดึงข้อมูลเอกสาร
        const documentData = getDocumentData();

        if (!documentData) {
            Swal.fire({
                title: 'ไม่พบข้อมูล',
                text: 'ไม่พบข้อมูลเอกสารที่จะพิมพ์',
                icon: 'warning',
                confirmButtonColor: '#667eea'
            });
            return;
        }

        // สร้าง HTML สำหรับพิมพ์
        const printHTML = createPrintHTML(documentData);

        // เขียน HTML ลงในหน้าต่างใหม่
        printWindow.document.write(printHTML);
        printWindow.document.close();

        // รอให้โหลดเสร็จแล้วพิมพ์
        printWindow.onload = function () {
            setTimeout(() => {
                printWindow.print();
                printWindow.close();
            }, 500);
        };
    }

    /**
     * ดึงข้อมูลเอกสารจากหน้า
     */
    function getDocumentData() {
        const resultCard = document.querySelector('.result-card');
        if (!resultCard) return null;

        const data = {};

        // ดึงข้อมูลพื้นฐาน
        data.referenceId = document.querySelector('.result-id')?.textContent?.trim() || '';
        data.topic = document.querySelector('.info-value')?.textContent?.trim() || '';
        data.submittedBy = '';
        data.phone = '';
        data.email = '';
        data.submitDate = '';
        data.status = '';
        data.statusDescription = '';
        data.detail = '';
        data.response = '';

        // ดึงข้อมูลจาก info-grid
        const infoItems = document.querySelectorAll('.info-item');
        infoItems.forEach(item => {
            const label = item.querySelector('.info-label')?.textContent?.trim();
            const value = item.querySelector('.info-value')?.textContent?.trim();

            switch (label) {
                case 'เรื่อง':
                    data.topic = value;
                    break;
                case 'ผู้ยื่นเรื่อง':
                    data.submittedBy = value;
                    break;
                case 'เบอร์โทรศัพท์':
                    data.phone = value;
                    break;
                case 'อีเมล':
                    data.email = value;
                    break;
                case 'วันที่ยื่นเรื่อง':
                    data.submitDate = value;
                    break;
            }
        });

        // ดึงสถานะ
        data.status = document.querySelector('.status-badge')?.textContent?.trim() || '';
        data.statusDescription = document.querySelector('.status-description')?.textContent?.trim() || '';

        // ดึงรายละเอียด
        const detailSection = document.querySelector('.status-section:last-of-type');
        if (detailSection) {
            const detailContent = detailSection.querySelector('div[style*="background: var(--white)"]');
            data.detail = detailContent?.textContent?.trim() || '';
        }

        return data;
    }

    /**
     * สร้าง HTML สำหรับพิมพ์ในขนาด A4
     */
    function createPrintHTML(data) {
        const currentDate = new Date();
        const printDate = currentDate.toLocaleDateString('th-TH', {
            year: 'numeric',
            month: 'long',
            day: 'numeric'
        });
        const printTime = currentDate.toLocaleTimeString('th-TH', {
            hour: '2-digit',
            minute: '2-digit'
        });

        return `
<!DOCTYPE html>
<html lang="th">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>ใบติดตามเอกสาร - ${data.referenceId}</title>
    <style>
        @page {
            size: A4;
            margin: 2cm;
        }
        
        * {
            box-sizing: border-box;
            margin: 0;
            padding: 0;
        }
        
        body {
            font-family: 'Sarabun', 'TH SarabunPSK', 'Loma', Arial, sans-serif;
            font-size: 14px;
            line-height: 1.6;
            color: #333;
            background: white;
        }
        
        .print-container {
            width: 100%;
            max-width: 21cm;
            margin: 0 auto;
            padding: 0;
        }
        
        .header {
            text-align: center;
            margin-bottom: 30px;
            border-bottom: 3px solid #667eea;
            padding-bottom: 20px;
        }
        
        .header h1 {
            color: #667eea;
            font-size: 28px;
            font-weight: bold;
            margin-bottom: 10px;
        }
        
        .header h2 {
            color: #555;
            font-size: 20px;
            font-weight: normal;
        }
        
        .reference-box {
            background: #f8f9ff;
            border: 2px solid #667eea;
            border-radius: 10px;
            padding: 20px;
            text-align: center;
            margin-bottom: 30px;
        }
        
        .reference-label {
            font-size: 16px;
            color: #666;
            margin-bottom: 10px;
        }
        
        .reference-id {
            font-size: 32px;
            font-weight: bold;
            color: #667eea;
            letter-spacing: 2px;
        }
        
        .info-table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 30px;
        }
        
        .info-table th,
        .info-table td {
            border: 1px solid #ddd;
            padding: 12px;
            text-align: left;
            vertical-align: top;
        }
        
        .info-table th {
            background-color: #f5f6fa;
            font-weight: bold;
            width: 30%;
            color: #333;
        }
        
        .info-table td {
            background-color: white;
        }
        
        .status-section {
            background: #f8f9ff;
            border: 2px solid #667eea;
            border-radius: 10px;
            padding: 20px;
            margin-bottom: 20px;
        }
        
        .status-header {
            display: flex;
            align-items: center;
            justify-content: space-between;
            margin-bottom: 15px;
            flex-wrap: wrap;
        }
        
        .status-title {
            font-size: 18px;
            font-weight: bold;
            color: #333;
        }
        
        .status-badge {
            background: #667eea;
            color: white;
            padding: 8px 20px;
            border-radius: 20px;
            font-weight: bold;
            font-size: 16px;
        }
        
        .status-description {
            background: white;
            border: 1px solid #ddd;
            border-radius: 5px;
            padding: 15px;
            margin-top: 15px;
        }
        
        .detail-section {
            background: #fff;
            border: 1px solid #ddd;
            border-radius: 5px;
            padding: 20px;
            margin-bottom: 20px;
        }
        
        .detail-title {
            font-size: 18px;
            font-weight: bold;
            color: #333;
            margin-bottom: 15px;
            padding-bottom: 10px;
            border-bottom: 2px solid #f0f0f0;
        }
        
        .footer {
            margin-top: 40px;
            padding-top: 20px;
            border-top: 1px solid #ddd;
            font-size: 12px;
            color: #666;
            text-align: center;
        }
        
        .print-info {
            display: flex;
            justify-content: space-between;
            margin-top: 10px;
            flex-wrap: wrap;
        }
        
        .qr-placeholder {
            width: 100px;
            height: 100px;
            border: 2px dashed #ccc;
            display: flex;
            align-items: center;
            justify-content: center;
            margin: 20px auto;
            font-size: 12px;
            color: #999;
        }
        
        @media print {
            body {
                -webkit-print-color-adjust: exact;
                print-color-adjust: exact;
            }
            
            .print-container {
                box-shadow: none;
            }
            
            @page {
                margin: 1.5cm;
            }
        }
        
        .warning-box {
            background: #fff3cd;
            border: 1px solid #ffc107;
            border-radius: 5px;
            padding: 15px;
            margin: 20px 0;
            font-size: 12px;
        }
    </style>
</head>
<body>
    <div class="print-container">
        <!-- Header -->
        <div class="header">
            <h1>ใบติดตามเอกสารออนไลน์</h1>
            <h2>Electronic Document Tracking</h2>
        </div>
        
        <!-- Reference ID Box -->
        <div class="reference-box">
            <div class="reference-label">หมายเลขอ้างอิง / Reference Number</div>
            <div class="reference-id">${data.referenceId}</div>
        </div>
        
        <!-- Document Information Table -->
        <table class="info-table">
            <tr>
                <th>เรื่อง / Subject</th>
                <td>${data.topic}</td>
            </tr>
            <tr>
                <th>ผู้ยื่นเรื่อง / Applicant</th>
                <td>${data.submittedBy}</td>
            </tr>
            <tr>
                <th>เบอร์โทรศัพท์ / Phone</th>
                <td>${data.phone}</td>
            </tr>
            <tr>
                <th>อีเมล / Email</th>
                <td>${data.email}</td>
            </tr>
            <tr>
                <th>วันที่ยื่นเรื่อง / Submit Date</th>
                <td>${data.submitDate}</td>
            </tr>
        </table>
        
        <!-- Status Section -->
        <div class="status-section">
            <div class="status-header">
                <div class="status-title">สถานะปัจจุบัน / Current Status</div>
                <div class="status-badge">${data.status}</div>
            </div>
            <div class="status-description">
                ${data.statusDescription}
            </div>
        </div>
        
        <!-- Detail Section -->
        ${data.detail ? `
        <div class="detail-section">
            <div class="detail-title">รายละเอียดเอกสาร / Document Details</div>
            <div>${data.detail.replace(/\n/g, '<br>')}</div>
        </div>
        ` : ''}
        
        <!-- QR Code -->
        <div style="text-align: center; margin: 20px 0;">
            <div id="qrcode-${data.referenceId}" style="display: inline-block; padding: 10px; border: 1px solid #ddd; border-radius: 5px;">
                <div style="width: 120px; height: 120px; background: url('https://api.qrserver.com/v1/create-qr-code/?size=120x120&data=${encodeURIComponent(window.location.origin + '/Esv_ods/track?code=' + data.referenceId)}') center center no-repeat; background-size: contain;"></div>
            </div>
            <div style="font-size: 12px; color: #666; margin-top: 10px;">
                สแกน QR Code เพื่อติดตามสถานะออนไลน์
            </div>
        </div>
        
        <!-- Warning Box -->
        <div class="warning-box">
            <strong>คำเตือน:</strong> ใบติดตามนี้เป็นเพียงการแสดงสถานะเท่านั้น ไม่ใช่เอกสารทางราชการ 
            หากต้องการเอกสารทางการ กรุณาติดต่อเจ้าหน้าที่โดยตรง
        </div>
        
        <!-- Footer -->
        <div class="footer">
            <div>ระบบติดตามเอกสารออนไลน์ | Electronic Document Tracking System</div>
            <div class="print-info">
                <span>พิมพ์เมื่อ: ${printDate} ${printTime}</span>
                <span>หน้า 1 จาก 1</span>
            </div>
        </div>
    </div>
</body>
</html>
    `;
    }

    // ===================================================================
    // *** EVENT HANDLERS & INITIALIZATION ***
    // ===================================================================

    document.addEventListener('DOMContentLoaded', function () {
        console.log('🚀 ESV Track Page loading...');

        try {
            // Initialize core functionality
            handleFormSubmit();
            handleInputEnhancement();
            handleUrlParameters();
            handleAutoRefresh();
            handleCopyFunctionality();

            // *** เพิ่ม: ตรวจสอบการโหลด reCAPTCHA ***
            if (window.RECAPTCHA_SITE_KEY && !window.recaptchaReady) {
                console.log('⏳ Waiting for reCAPTCHA to load for ESV track...');

                let checkInterval = setInterval(function () {
                    if (window.recaptchaReady) {
                        console.log('✅ reCAPTCHA is now ready for ESV track');
                        clearInterval(checkInterval);
                    }
                }, 100);

                setTimeout(function () {
                    if (!window.recaptchaReady) {
                        console.log('⚠️ reCAPTCHA timeout after 10 seconds for ESV track');
                        clearInterval(checkInterval);
                    }
                }, 10000);
            }

            console.log('✅ ESV Track Page initialized successfully');

            if (EsvTrackConfig.debug) {
                console.log('🔧 Debug mode enabled');
                console.log('⚙️ Configuration:', EsvTrackConfig);
            }

        } catch (error) {
            console.error('❌ Initialization error:', error);
            Swal.fire({
                title: 'เกิดข้อผิดพลาด',
                text: 'เกิดข้อผิดพลาดในการโหลดหน้า กรุณารีเฟรชหน้า',
                icon: 'error',
                confirmButtonText: 'ตกลง',
                confirmButtonColor: '#667eea'
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
                confirmButtonColor: '#667eea'
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
                confirmButtonColor: '#667eea'
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
                confirmButtonColor: '#667eea'
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
                confirmButtonColor: '#667eea'
            });
        });
    <?php endif; ?>

        console.log("🔍 ESV Track System loaded successfully");
    console.log("🔧 Environment: <?= defined('ENVIRONMENT') ? ENVIRONMENT : 'production' ?>");
    console.log("📊 Track Status: Ready");
</script>