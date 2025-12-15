<div class="text-center pages-head">
    <span class="font-pages-head">ติดตามสถานะรายงานแจ้งการทุจริตและประพฤติมิชอบ</span>
</div>
</div>
<img src="<?php echo base_url('docs/welcome-btm-light-other.png'); ?>">

<?php
defined('BASEPATH') or exit('No direct script access allowed');

// ฟังก์ชันแปลงวันที่เป็นรูปแบบไทย
function convertToThaiDate($date_string)
{
    if (empty($date_string))
        return '';

    $thai_months = array(
        1 => 'มกราคม',
        2 => 'กุมภาพันธ์',
        3 => 'มีนาคม',
        4 => 'เมษายน',
        5 => 'พฤษภาคม',
        6 => 'มิถุนายน',
        7 => 'กรกฎาคม',
        8 => 'สิงหาคม',
        9 => 'กันยายน',
        10 => 'ตุลาคม',
        11 => 'พฤศจิกายน',
        12 => 'ธันวาคม'
    );

    try {
        $date = new DateTime($date_string);
        $day = $date->format('j');
        $month = (int) $date->format('n');
        $year = $date->format('Y') + 543;
        $time = $date->format('H:i');

        return $day . ' ' . $thai_months[$month] . ' ' . $year . ' เวลา ' . $time . ' น.';
    } catch (Exception $e) {
        return $date_string;
    }
}

// ฟังก์ชันแปลงสถานะ
function getStatusDisplay($status)
{
    $status_map = [
        'pending' => 'รอดำเนินการ',
        'under_review' => 'กำลังตรวจสอบ',
        'investigating' => 'กำลังสอบสวน',
        'resolved' => 'แก้ไขแล้ว',
        'dismissed' => 'ไม่อนุมัติ',
        'closed' => 'ปิดเรื่อง'
    ];
    return $status_map[$status] ?? $status;
}

// ฟังก์ชันแปลงประเภทการทุจริต
function getCorruptionTypeDisplay($type)
{
    $type_map = [
        'embezzlement' => 'การยักยอกเงิน',
        'bribery' => 'การรับสินบน',
        'abuse_of_power' => 'การใช้อำนาจหน้าที่มิชอบ',
        'conflict_of_interest' => 'ความขัดแย้งทางผลประโยชน์',
        'procurement_fraud' => 'การทุจริตในการจัดซื้อจัดจ้าง',
        'other' => 'อื่นๆ'
    ];
    return $type_map[$type] ?? $type;
}

// ข้อมูลสำหรับทดสอบ (จะมาจาก Controller)
$report_id = $report_id ?? '';
$search_performed = $search_performed ?? false;
$corruption_report_info = $corruption_report_info ?? null;
$error_message = $error_message ?? '';
?>

<style>
    @import url('https://fonts.googleapis.com/css2?family=Kanit:wght@300;400;500;600;700&display=swap');

    :root {
        --track-primary-red: #e74c3c;
        --track-secondary-red: #c0392b;
        --track-light-red: #fdf2f2;
        --track-very-light-red: #fef9f9;
        --track-success-color: #28a745;
        --track-warning-color: #ffc107;
        --track-danger-color: #dc3545;
        --track-info-color: #17a2b8;
        --track-purple-color: #6f42c1;
        --track-text-dark: #2c3e50;
        --track-text-muted: #6c757d;
        --track-border-light: rgba(231, 76, 60, 0.1);
        --track-shadow-light: 0 4px 20px rgba(231, 76, 60, 0.1);
        --track-shadow-medium: 0 8px 30px rgba(231, 76, 60, 0.15);
        --track-shadow-strong: 0 15px 40px rgba(231, 76, 60, 0.2);
        --track-gradient-primary: linear-gradient(135deg, #e74c3c 0%, #c0392b 100%);
        --track-gradient-light: linear-gradient(135deg, #fef9f9 0%, #fdf2f2 100%);
        --track-gradient-card: linear-gradient(145deg, rgba(255, 255, 255, 0.95) 0%, rgba(255, 255, 255, 0.9) 100%);
    }

    * {
        font-family: 'Kanit', sans-serif;
    }

    .track-bg-pages {
        background: #ffffff;
        background-image:
            radial-gradient(circle at 20% 30%, rgba(231, 76, 60, 0.03) 0%, transparent 50%),
            radial-gradient(circle at 80% 70%, rgba(192, 57, 43, 0.03) 0%, transparent 50%),
            linear-gradient(135deg, rgba(231, 76, 60, 0.01) 0%, transparent 100%);
        min-height: 100vh;
        padding: 2rem 0;
    }

    .track-container-pages {
        max-width: 1000px;
        margin: 0 auto;
        padding: 0 1rem;
    }

    /* Modern Page Header */
    .track-page-header {
        text-align: center;
        margin-bottom: 3rem;
        position: relative;
    }

    .track-header-decoration {
        width: 120px;
        height: 6px;
        background: var(--track-gradient-primary);
        margin: 0 auto 2rem;
        border-radius: 3px;
        position: relative;
        overflow: hidden;
    }

    .track-header-decoration::before {
        content: '';
        position: absolute;
        top: 0;
        left: -100%;
        width: 100%;
        height: 100%;
        background: linear-gradient(90deg, transparent, rgba(255, 255, 255, 0.4), transparent);
        animation: trackShimmer 2s infinite;
    }

    .track-page-title {
        font-size: 2.8rem;
        font-weight: 600;
        background: var(--track-gradient-primary);
        -webkit-background-clip: text;
        -webkit-text-fill-color: transparent;
        background-clip: text;
        margin-bottom: 1rem;
        line-height: 1.2;
    }

    .track-page-subtitle {
        font-size: 1.2rem;
        color: var(--track-text-muted);
        margin-bottom: 0;
        font-weight: 400;
    }

    /* Modern Card */
    .track-modern-card {
        background: var(--track-gradient-card);
        border-radius: 24px;
        box-shadow: var(--track-shadow-light);
        margin-bottom: 2rem;
        overflow: hidden;
        position: relative;
        backdrop-filter: blur(10px);
        border: 1px solid rgba(0, 123, 255, 0.08);
        z-index: 50;
    }

    .track-modern-card::before {
        content: '';
        position: absolute;
        top: 0;
        left: 0;
        right: 0;
        height: 4px;
        background: var(--track-gradient-primary);
        z-index: 1;
    }

    /* Search Form */
    .track-search-form {
        padding: 3rem 2.5rem;
        text-align: center;
        position: relative;
    }

    .track-search-icon-bg {
        width: 120px;
        height: 120px;
        border-radius: 50%;
        background: var(--track-gradient-light);
        display: flex;
        align-items: center;
        justify-content: center;
        margin: 0 auto 2rem;
        font-size: 3rem;
        color: var(--track-primary-red);
        box-shadow: 0 10px 30px rgba(231, 76, 60, 0.2);
        position: relative;
        overflow: hidden;
    }

    .track-search-icon-bg::before {
        content: '';
        position: absolute;
        top: -50%;
        left: -50%;
        width: 200%;
        height: 200%;
        background: linear-gradient(45deg, transparent, rgba(255, 255, 255, 0.1), transparent);
        transform: rotate(45deg);
        animation: trackIconShine 3s infinite;
    }

    .track-search-title {
        font-size: 1.8rem;
        font-weight: 600;
        color: var(--track-text-dark);
        margin-bottom: 1rem;
    }

    .track-search-description {
        color: var(--track-text-muted);
        margin-bottom: 2.5rem;
        font-size: 1.1rem;
        line-height: 1.6;
    }

    .track-input-group {
        max-width: 500px;
        margin: 0 auto;
        position: relative;
    }

    .track-input-field {
        width: 100%;
        padding: 1.2rem 1.5rem;
        font-size: 1.1rem;
        border: 2px solid var(--track-border-light);
        border-radius: 15px;
        background: rgba(255, 255, 255, 0.9);
        transition: all 0.3s ease;
        font-family: 'Kanit', sans-serif;
        font-weight: 500;
        text-align: center;
        letter-spacing: 1px;
    }

    .track-input-field:focus {
        outline: none;
        border-color: var(--track-primary-red);
        box-shadow: 0 0 0 0.25rem rgba(231, 76, 60, 0.25);
        transform: translateY(-2px);
    }

    .track-input-field::placeholder {
        color: var(--track-text-muted);
        font-weight: 400;
    }

    .track-search-btn {
        width: 100%;
        padding: 1.2rem 2rem;
        background: var(--track-gradient-primary);
        color: white;
        border: none;
        border-radius: 15px;
        font-size: 1.1rem;
        font-weight: 600;
        margin-top: 1.5rem;
        transition: all 0.3s ease;
        cursor: pointer;
        position: relative;
        overflow: hidden;
    }

    .track-search-btn::before {
        content: '';
        position: absolute;
        top: 0;
        left: -100%;
        width: 100%;
        height: 100%;
        background: linear-gradient(90deg, transparent, rgba(255, 255, 255, 0.2), transparent);
        transition: all 0.5s;
    }

    .track-search-btn:hover {
        transform: translateY(-3px);
        box-shadow: 0 8px 25px rgba(231, 76, 60, 0.4);
    }

    .track-search-btn:hover::before {
        left: 100%;
    }

    .track-search-btn:active {
        transform: translateY(-1px);
    }

    .track-search-btn.loading {
        pointer-events: none;
        opacity: 0.8;
    }

    /* Search Guidelines */
    .track-guidelines {
        padding: 2rem 2.5rem;
        background: var(--track-gradient-light);
        border-top: 1px solid var(--track-border-light);
    }

    .track-guidelines-title {
        font-size: 1.3rem;
        font-weight: 600;
        color: var(--track-text-dark);
        margin-bottom: 1.5rem;
        text-align: center;
    }

    .track-guidelines-grid {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
        gap: 1.5rem;
    }

    .track-guideline-item {
        background: rgba(255, 255, 255, 0.8);
        padding: 1.5rem;
        border-radius: 15px;
        border-left: 4px solid var(--track-primary-red);
        transition: all 0.3s ease;
    }

    .track-guideline-item:hover {
        transform: translateY(-3px);
        box-shadow: var(--track-shadow-medium);
    }

    .track-guideline-icon {
        width: 40px;
        height: 40px;
        border-radius: 50%;
        background: var(--track-gradient-light);
        display: flex;
        align-items: center;
        justify-content: center;
        color: var(--track-primary-red);
        margin-bottom: 1rem;
        font-size: 1.1rem;
    }

    .track-guideline-title {
        font-weight: 600;
        color: var(--track-text-dark);
        margin-bottom: 0.5rem;
        font-size: 1rem;
    }

    .track-guideline-text {
        color: var(--track-text-muted);
        font-size: 0.9rem;
        line-height: 1.5;
        margin: 0;
    }

    /* Result Section */
    .track-result-card {
        padding: 0;
        animation: trackSlideUp 0.6s ease;
    }

    .track-result-header {
        padding: 2rem 2.5rem 1rem;
        background: var(--track-gradient-light);
        text-align: center;
    }

    .track-result-icon {
        width: 80px;
        height: 80px;
        border-radius: 50%;
        background: var(--track-gradient-primary);
        display: flex;
        align-items: center;
        justify-content: center;
        margin: 0 auto 1.5rem;
        font-size: 2rem;
        color: white;
        box-shadow: 0 8px 25px rgba(231, 76, 60, 0.3);
    }

    .track-result-title {
        font-size: 1.5rem;
        font-weight: 600;
        color: var(--track-text-dark);
        margin-bottom: 0.5rem;
    }

    .track-result-subtitle {
        color: var(--track-text-muted);
        font-size: 1rem;
    }

    /* Report Info Display */
    .track-report-content {
        padding: 2.5rem;
    }

    .track-report-header {
        text-align: center;
        margin-bottom: 2.5rem;
        padding-bottom: 2rem;
        border-bottom: 1px solid var(--track-border-light);
    }

    .track-report-id-badge {
        background: var(--track-gradient-primary);
        color: white;
        padding: 1rem 2rem;
        border-radius: 20px;
        font-weight: 700;
        font-size: 1.3rem;
        display: inline-flex;
        align-items: center;
        margin-bottom: 1.5rem;
        box-shadow: 0 4px 15px rgba(231, 76, 60, 0.3);
    }

    .track-report-title {
        font-size: 1.6rem;
        font-weight: 600;
        color: var(--track-text-dark);
        margin-bottom: 1rem;
        line-height: 1.3;
    }

    .track-status-display {
        display: inline-flex;
        align-items: center;
        gap: 0.5rem;
        padding: 0.8rem 1.5rem;
        border-radius: 15px;
        font-weight: 600;
        font-size: 1.1rem;
        box-shadow: 0 4px 15px rgba(0, 0, 0, 0.1);
    }

    /* Status Colors */
    .track-status-pending {
        background: linear-gradient(135deg, #ffc107, #e0a800);
        color: white;
    }

    .track-status-under_review {
        background: linear-gradient(135deg, #17a2b8, #138496);
        color: white;
    }

    .track-status-investigating {
        background: linear-gradient(135deg, #007bff, #0056b3);
        color: white;
    }

    .track-status-resolved {
        background: linear-gradient(135deg, #28a745, #1e7e34);
        color: white;
    }

    .track-status-dismissed {
        background: linear-gradient(135deg, #dc3545, #c82333);
        color: white;
    }

    .track-status-closed {
        background: linear-gradient(135deg, #6c757d, #545b62);
        color: white;
    }

    /* Info Grid */
    .track-info-grid {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
        gap: 1.5rem;
        margin-bottom: 2rem;
    }

    .track-info-item {
        background: rgba(255, 255, 255, 0.7);
        padding: 1.5rem;
        border-radius: 15px;
        border-left: 4px solid var(--track-primary-red);
        transition: all 0.3s ease;
    }

    .track-info-item:hover {
        transform: translateY(-3px);
        box-shadow: var(--track-shadow-medium);
    }

    .track-info-label {
        color: var(--track-text-muted);
        font-size: 0.9rem;
        margin-bottom: 0.5rem;
        font-weight: 500;
    }

    .track-info-value {
        color: var(--track-text-dark);
        font-size: 1.1rem;
        font-weight: 600;
        line-height: 1.4;
    }

    /* Progress Timeline */
    .track-progress-section {
        margin-top: 2.5rem;
        padding-top: 2rem;
        border-top: 1px solid var(--track-border-light);
    }

    .track-progress-title {
        font-size: 1.4rem;
        font-weight: 600;
        color: var(--track-text-dark);
        margin-bottom: 2rem;
        text-align: center;
    }

    .track-timeline {
        position: relative;
        padding-left: 2rem;
        max-width: 600px;
        margin: 0 auto;
    }

    .track-timeline::before {
        content: '';
        position: absolute;
        left: 0.75rem;
        top: 0;
        bottom: 0;
        width: 2px;
        background: var(--track-gradient-primary);
    }

    .track-timeline-item {
        position: relative;
        margin-bottom: 2rem;
        background: rgba(255, 255, 255, 0.9);
        border-radius: 15px;
        padding: 1.5rem;
        margin-left: 1rem;
        border: 1px solid var(--track-border-light);
        animation: trackFadeInLeft 0.6s ease forwards;
        opacity: 0;
    }

    .track-timeline-item:nth-child(1) {
        animation-delay: 0.1s;
    }

    .track-timeline-item:nth-child(2) {
        animation-delay: 0.2s;
    }

    .track-timeline-item:nth-child(3) {
        animation-delay: 0.3s;
    }

    .track-timeline-item:nth-child(4) {
        animation-delay: 0.4s;
    }

    .track-timeline-item::before {
        content: '';
        position: absolute;
        left: -1.75rem;
        top: 1.5rem;
        width: 12px;
        height: 12px;
        border-radius: 50%;
        background: var(--track-primary-red);
        border: 3px solid white;
        box-shadow: 0 0 0 2px var(--track-primary-red);
    }

    .track-timeline-item.active::before {
        background: var(--track-success-color);
        box-shadow: 0 0 0 2px var(--track-success-color);
        animation: trackPulse 2s infinite;
    }

    .track-timeline-content h5 {
        font-weight: 600;
        color: var(--track-text-dark);
        margin-bottom: 0.5rem;
    }

    .track-timeline-content p {
        color: var(--track-text-muted);
        margin: 0;
        line-height: 1.5;
    }

    .track-timeline-date {
        color: var(--track-text-muted);
        font-size: 0.9rem;
        margin-top: 0.5rem;
    }

    /* Response Section */
    .track-response-section {
        background: var(--track-gradient-light);
        padding: 2rem;
        border-radius: 15px;
        border-left: 4px solid var(--track-success-color);
        margin-top: 2rem;
    }

    .track-response-title {
        font-weight: 600;
        color: var(--track-text-dark);
        margin-bottom: 1rem;
        font-size: 1.2rem;
    }

    .track-response-content {
        color: var(--track-text-dark);
        line-height: 1.6;
        margin-bottom: 1rem;
    }

    .track-response-date {
        color: var(--track-text-muted);
        font-size: 0.9rem;
    }

    /* Error State */
    .track-error-state {
        text-align: center;
        padding: 3rem 2rem;
        color: var(--track-text-muted);
    }

    .track-error-icon {
        width: 80px;
        height: 80px;
        border-radius: 50%;
        background: linear-gradient(135deg, rgba(220, 53, 69, 0.1), rgba(220, 53, 69, 0.05));
        display: flex;
        align-items: center;
        justify-content: center;
        margin: 0 auto 1.5rem;
        font-size: 2rem;
        color: var(--track-danger-color);
    }

    .track-error-title {
        font-size: 1.4rem;
        font-weight: 600;
        color: var(--track-text-dark);
        margin-bottom: 1rem;
    }

    .track-error-message {
        color: var(--track-text-muted);
        margin-bottom: 2rem;
        line-height: 1.6;
    }

    /* Action Buttons */
    .track-actions {
        display: flex;
        gap: 1rem;
        justify-content: center;
        flex-wrap: wrap;
        margin-top: 2rem;
    }

    .track-action-btn {
        padding: 0.8rem 1.5rem;
        border-radius: 12px;
        font-weight: 600;
        text-decoration: none;
        transition: all 0.3s ease;
        display: inline-flex;
        align-items: center;
        gap: 0.5rem;
        cursor: pointer;
        border: none;
        font-size: 0.95rem;
    }

    .track-action-btn.primary {
        background: var(--track-gradient-primary);
        color: white;
    }

    .track-action-btn.primary:hover {
        transform: translateY(-2px);
        box-shadow: 0 8px 25px rgba(0, 123, 255, 0.4);
        color: white;
        text-decoration: none;
    }

    .track-action-btn.secondary {
        background: rgba(231, 76, 60, 0.1);
        color: var(--track-primary-red);
        border: 1px solid rgba(231, 76, 60, 0.3);
    }

    .track-action-btn.secondary:hover {
        background: var(--track-gradient-primary);
        color: white;
        transform: translateY(-2px);
        text-decoration: none;
    }

    /* Animations */
    @keyframes trackShimmer {
        0% {
            left: -100%;
        }

        100% {
            left: 100%;
        }
    }

    @keyframes trackIconShine {
        0% {
            transform: translateX(-100%) translateY(-100%) rotate(45deg);
        }

        50% {
            transform: translateX(100%) translateY(100%) rotate(45deg);
        }

        100% {
            transform: translateX(-100%) translateY(-100%) rotate(45deg);
        }
    }

    @keyframes trackSlideUp {
        from {
            opacity: 0;
            transform: translateY(30px);
        }

        to {
            opacity: 1;
            transform: translateY(0);
        }
    }

    @keyframes trackFadeInLeft {
        from {
            opacity: 0;
            transform: translateX(-30px);
        }

        to {
            opacity: 1;
            transform: translateX(0);
        }
    }

    @keyframes trackPulse {

        0%,
        100% {
            transform: scale(1);
            opacity: 1;
        }

        50% {
            transform: scale(1.2);
            opacity: 0.8;
        }
    }

    /* Responsive Design */
    @media (max-width: 768px) {
        .track-page-title {
            font-size: 2.2rem;
        }

        .track-search-form {
            padding: 2rem 1.5rem;
        }

        .track-search-icon-bg {
            width: 80px;
            height: 80px;
            font-size: 2rem;
        }

        .track-search-title {
            font-size: 1.5rem;
        }

        .track-guidelines-grid {
            grid-template-columns: 1fr;
        }

        .track-info-grid {
            grid-template-columns: 1fr;
        }

        .track-timeline {
            padding-left: 1.5rem;
        }

        .track-timeline-item {
            margin-left: 0.5rem;
            padding: 1rem;
        }

        .track-actions {
            flex-direction: column;
            align-items: center;
        }

        .track-action-btn {
            width: 100%;
            justify-content: center;
            max-width: 250px;
        }
    }

    @media (max-width: 576px) {
        .track-container-pages {
            padding: 0 0.5rem;
        }

        .track-search-form {
            padding: 1.5rem 1rem;
        }

        .track-report-content {
            padding: 1.5rem;
        }

        .track-report-id-badge {
            font-size: 1.1rem;
            padding: 0.8rem 1.5rem;
        }

        .track-report-title {
            font-size: 1.3rem;
        }
    }

    /* Print Styles */
    @media print {

        .track-search-form,
        .track-guidelines,
        .track-actions {
            display: none !important;
        }

        .track-modern-card {
            box-shadow: none;
            border: 1px solid #ddd;
        }

        .track-page-title {
            color: #333 !important;
            -webkit-text-fill-color: #333 !important;
        }
    }
</style>

<div class="track-bg-pages">
    <div class="track-container-pages">

        <!-- Page Header -->
        <div class="track-page-header">
            <div class="track-header-decoration"></div>
            <h1 class="track-page-title">
                <i class="fas fa-search me-3"></i>
                ติดตามสถานะรายงานแจ้งการทุจริตฯ
            </h1>
            <p class="track-page-subtitle">ค้นหาและติดตามความคืบหน้าของรายงานการทุจริตและประพฤติมิชอบ</p>
        </div>

        <!-- Search Form -->
        <?php if (!$search_performed || !$corruption_report_info): ?>
            <div class="track-modern-card">
                <div class="track-search-form">
                    <div class="track-search-icon-bg">
                        <i class="fas fa-search"></i>
                    </div>

                    <h2 class="track-search-title">ค้นหารายงานของคุณ</h2>
                    <p class="track-search-description">
                        กรุณากรอกหมายเลขรายงานการทุจริตและประพฤติมิชอบเพื่อติดตามสถานะและความคืบหน้า
                    </p>

                    <form id="trackSearchForm" action="<?php echo site_url('Corruption/search_report'); ?>" method="POST">
                        <div class="track-input-group">
                            <input type="text" id="reportIdInput" name="report_id" class="track-input-field"
                                placeholder="กรอกหมายเลขติดตาม" value="<?php echo htmlspecialchars($report_id); ?>"
                                maxlength="20" pattern="^COR[0-9]{7}$" required>

                            <button type="submit" class="track-search-btn" id="searchBtn">
                                <i class="fas fa-search me-2"></i>
                                ค้นหารายงาน
                            </button>
                        </div>
                    </form>
                </div>

                <!-- Guidelines -->
                <div class="track-guidelines">
                    <h3 class="track-guidelines-title">
                        <i class="fas fa-info-circle me-2"></i>
                        วิธีการใช้งาน
                    </h3>

                    <div class="track-guidelines-grid">


                        <div class="track-guideline-item">
                            <div class="track-guideline-icon">
                                <i class="fas fa-shield-alt"></i>
                            </div>
                            <h4 class="track-guideline-title">ความปลอดภัย</h4>
                            <p class="track-guideline-text">
                                คุณสามารถค้นหาได้เฉพาะรายงานที่คุณเป็นผู้แจ้งเท่านั้น เพื่อความเป็นส่วนตัว
                            </p>
                        </div>

                        <div class="track-guideline-item">
                            <div class="track-guideline-icon">
                                <i class="fas fa-clock"></i>
                            </div>
                            <h4 class="track-guideline-title">อัปเดตแบบ Real-time</h4>
                            <p class="track-guideline-text">
                                ข้อมูลสถานะจะถูกอัปเดตทันทีเมื่อมีการเปลี่ยนแปลงจากหน่วยงาน
                            </p>
                        </div>

                        <div class="track-guideline-item">
                            <div class="track-guideline-icon">
                                <i class="fas fa-eye-slash"></i>
                            </div>
                            <h4 class="track-guideline-title">ไม่ระบุตัวตน</h4>
                            <p class="track-guideline-text">
                                แม้รายงานที่ไม่ระบุตัวตนก็สามารถติดตามสถานะได้ด้วยหมายเลขรายงาน
                            </p>
                        </div>
                    </div>
                </div>
            </div>
        <?php endif; ?>

        <!-- Error Message -->
        <?php if ($search_performed && !$corruption_report_info && !empty($error_message)): ?>
            <div class="track-modern-card">
                <div class="track-error-state">
                    <div class="track-error-icon">
                        <i class="fas fa-exclamation-triangle"></i>
                    </div>
                    <h3 class="track-error-title">ไม่พบรายงานที่ระบุ</h3>
                    <p class="track-error-message">
                        <?php echo htmlspecialchars($error_message); ?>
                    </p>

                    <div class="track-actions">
                        <button onclick="resetSearch()" class="track-action-btn secondary">
                            <i class="fas fa-redo me-2"></i>ค้นหาใหม่
                        </button>
                        <a href="<?php echo site_url('Corruption/report_form'); ?>" class="track-action-btn primary">
                            <i class="fas fa-plus me-2"></i>แจ้งเรื่องใหม่
                        </a>
                    </div>
                </div>
            </div>
        <?php endif; ?>

        <!-- Report Result -->
        <?php if ($search_performed && $corruption_report_info): ?>
            <div class="track-modern-card track-result-card">
                <div class="track-result-header">
                    <div class="track-result-icon">
                        <i class="fas fa-check-circle"></i>
                    </div>
                    <h2 class="track-result-title">พบรายงานของคุณ</h2>
                    <p class="track-result-subtitle">ข้อมูลและสถานะล่าสุดของรายงาน</p>
                </div>

                <div class="track-report-content">
                    <!-- Report Header -->
                    <div class="track-report-header">
                        <div class="track-report-id-badge">
                            <i class="fas fa-hashtag me-2"></i>
                            <?php echo htmlspecialchars($corruption_report_info->corruption_report_id); ?>
                        </div>

                        <h3 class="track-report-title">
                            <?php echo htmlspecialchars($corruption_report_info->complaint_subject ?? 'ไม่ระบุหัวข้อ'); ?>
                        </h3>

                        <div
                            class="track-status-display track-status-<?php echo $corruption_report_info->report_status; ?>">
                            <?php
                            $status_icons = [
                                'pending' => 'fas fa-clock',
                                'under_review' => 'fas fa-search',
                                'investigating' => 'fas fa-cogs',
                                'resolved' => 'fas fa-check-circle',
                                'dismissed' => 'fas fa-times-circle',
                                'closed' => 'fas fa-archive'
                            ];
                            $status_icon = $status_icons[$corruption_report_info->report_status] ?? 'fas fa-file-alt';
                            ?>
                            <i class="<?php echo $status_icon; ?>"></i>
                            <?php echo getStatusDisplay($corruption_report_info->report_status); ?>
                        </div>
                    </div>

                    <!-- Report Information -->
                    <div class="track-info-grid">
                        <div class="track-info-item">
                            <div class="track-info-label">ประเภทการทุจริต</div>
                            <div class="track-info-value">
                                <?php echo getCorruptionTypeDisplay($corruption_report_info->corruption_type ?? ''); ?>
                            </div>
                        </div>

                        <div class="track-info-item">
                            <div class="track-info-label">วันที่แจ้ง</div>
                            <div class="track-info-value">
                                <?php echo convertToThaiDate($corruption_report_info->created_at ?? ''); ?>
                            </div>
                        </div>

                        <div class="track-info-item">
                            <div class="track-info-label">ผู้ถูกกล่าวหา</div>
                            <div class="track-info-value">
                                <?php echo htmlspecialchars($corruption_report_info->perpetrator_name ?? 'ไม่ระบุ'); ?>
                            </div>
                        </div>

                        <div class="track-info-item">
                            <div class="track-info-label">อัปเดตล่าสุด</div>
                            <div class="track-info-value">
                                <?php
                                if (!empty($corruption_report_info->updated_at) && $corruption_report_info->updated_at != '0000-00-00 00:00:00') {
                                    echo convertToThaiDate($corruption_report_info->updated_at);
                                } else {
                                    echo convertToThaiDate($corruption_report_info->created_at);
                                }
                                ?>
                            </div>
                        </div>
                    </div>

                    <!-- Progress Timeline -->
                    <div class="track-progress-section">
                        <h4 class="track-progress-title">
                            <i class="fas fa-route me-2"></i>
                            ความคืบหน้าการดำเนินการ
                        </h4>

                        <div class="track-timeline">
                            <?php
                            $timeline_steps = [
                                ['key' => 'submitted', 'title' => 'ได้รับรายงาน', 'desc' => 'รายงานถูกส่งเข้าระบบเรียบร้อยแล้ว'],
                                ['key' => 'under_review', 'title' => 'ตรวจสอบเบื้องต้น', 'desc' => 'เจ้าหน้าที่กำลังตรวจสอบความถูกต้องของข้อมูล'],
                                ['key' => 'investigating', 'title' => 'สอบสวนข้อเท็จจริง', 'desc' => 'ดำเนินการสอบสวนและรวบรวมหลักฐาน'],
                                ['key' => 'resolved', 'title' => 'ดำเนินการเสร็จสิ้น', 'desc' => 'การดำเนินการเสร็จสมบูรณ์']
                            ];

                            $current_status = $corruption_report_info->report_status;
                            $status_order = ['pending', 'under_review', 'investigating', 'resolved', 'dismissed', 'closed'];
                            $current_index = array_search($current_status, $status_order);

                            foreach ($timeline_steps as $index => $step):
                                $is_active = ($index <= $current_index);
                                $is_current = ($step['key'] === $current_status);
                                ?>
                                <div class="track-timeline-item <?php echo $is_active ? 'active' : ''; ?>">
                                    <div class="track-timeline-content">
                                        <h5><?php echo $step['title']; ?>         <?php echo $is_current ? '(ปัจจุบัน)' : ''; ?></h5>
                                        <p><?php echo $step['desc']; ?></p>
                                        <?php if ($is_active): ?>
                                            <div class="track-timeline-date">
                                                <i class="fas fa-check-circle me-1"></i>
                                                <?php
                                                if ($is_current) {
                                                    echo 'อัปเดตล่าสุด: ' . convertToThaiDate($corruption_report_info->updated_at ?? $corruption_report_info->created_at);
                                                } else {
                                                    echo 'ดำเนินการแล้ว';
                                                }
                                                ?>
                                            </div>
                                        <?php endif; ?>
                                    </div>
                                </div>
                            <?php endforeach; ?>
                        </div>
                    </div>

                    <!-- Response from Authority -->
                    <?php if (!empty($corruption_report_info->response_message)): ?>
                        <div class="track-response-section">
                            <h4 class="track-response-title">
                                <i class="fas fa-reply me-2"></i>
                                การตอบกลับจากหน่วยงาน
                            </h4>
                            <div class="track-response-content">
                                <?php echo nl2br(htmlspecialchars($corruption_report_info->response_message)); ?>
                            </div>
                            <?php if (!empty($corruption_report_info->response_date)): ?>
                                <div class="track-response-date">
                                    <i class="fas fa-calendar-alt me-1"></i>
                                    ตอบกลับเมื่อ: <?php echo convertToThaiDate($corruption_report_info->response_date); ?>
                                </div>
                            <?php endif; ?>
                        </div>
                    <?php endif; ?>

                    <!-- Action Buttons -->
                    <div class="track-actions">
                        <button onclick="resetSearch()" class="track-action-btn secondary">
                            <i class="fas fa-search me-2"></i>ค้นหาอื่น
                        </button>

                        <button onclick="printReport()" class="track-action-btn secondary">
                            <i class="fas fa-print me-2"></i>พิมพ์ข้อมูล
                        </button>

                        <button
                            onclick="copyReportId('<?php echo htmlspecialchars($corruption_report_info->corruption_report_id, ENT_QUOTES); ?>')"
                            class="track-action-btn secondary">
                            <i class="fas fa-copy me-2"></i>คัดลอกหมายเลข
                        </button>

                        <a href="<?php echo site_url('Corruption/report_form'); ?>" class="track-action-btn primary">
                            <i class="fas fa-plus me-2"></i>แจ้งเรื่องใหม่
                        </a>
                    </div>
                </div>
            </div>
        <?php endif; ?>

    </div>
</div>

<!-- Load Scripts -->
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

<script>
    $(document).ready(function () {
        //console.log('🔍 ระบบติดตามสถานะรายงานการทุจริต - พร้อมใช้งาน');

        // *** ตัวแปรสำคัญ ***
        let isSearching = false;

        // *** เพิ่ม: Debug reCAPTCHA variables ตั้งแต่เริ่มต้น ***
        console.log('🔑 Initial reCAPTCHA check:');
        console.log('- RECAPTCHA_SITE_KEY:', typeof window.RECAPTCHA_SITE_KEY !== 'undefined' ? window.RECAPTCHA_SITE_KEY : 'UNDEFINED');
        console.log('- recaptchaReady:', typeof window.recaptchaReady !== 'undefined' ? window.recaptchaReady : 'UNDEFINED');
        console.log('- SKIP_RECAPTCHA_FOR_DEV:', typeof window.SKIP_RECAPTCHA_FOR_DEV !== 'undefined' ? window.SKIP_RECAPTCHA_FOR_DEV : 'UNDEFINED');
        console.log('- grecaptcha available:', typeof grecaptcha !== 'undefined');

        // *** Auto Focus on Input ***
        $('#reportIdInput').focus();

        // *** Form Validation ***
        $('#reportIdInput').on('input', function () {
            let value = $(this).val().toUpperCase();

            // Auto format: COR + numbers
            if (value && !value.startsWith('COR')) {
                if (/^\d/.test(value)) {
                    value = 'COR' + value;
                }
            }

            // Remove invalid characters
            value = value.replace(/[^COR0-9]/g, '');

            // Limit length
            if (value.length > 10) {
                value = value.substring(0, 10);
            }

            $(this).val(value);

            // Real-time validation
            validateInput();
        });

        // *** Input Validation Function ***
        function validateInput() {
            const input = $('#reportIdInput');
            const value = input.val();
            const searchBtn = $('#searchBtn');

            if (value.length === 0) {
                input.removeClass('is-valid is-invalid');
                searchBtn.prop('disabled', false);
                return true;
            }

            // Check format: COR + 7 digits
            const isValid = /^COR\d{7}$/.test(value);

            if (isValid) {
                input.removeClass('is-invalid').addClass('is-valid');
                searchBtn.prop('disabled', false);
                return true;
            } else {
                input.removeClass('is-valid').addClass('is-invalid');
                searchBtn.prop('disabled', value.length >= 10);
                return false;
            }
        }

        // *** Form Submit Handler - เพิ่ม reCAPTCHA ***
        $('#trackSearchForm').on('submit', function (e) {
            e.preventDefault();

            if (isSearching) return;

            const reportId = $('#reportIdInput').val().trim();

            if (!reportId) {
                showAlert('กรุณากรอกหมายเลขรายงาน', 'warning');
                $('#reportIdInput').focus();
                return;
            }

            if (!validateInput()) {
                showAlert('รูปแบบหมายเลขรายงานไม่ถูกต้อง', 'error');
                return;
            }

            console.log('📝 Form submitted - Report ID:', reportId);

            const searchBtn = $('#searchBtn');
            const originalContent = searchBtn.html();
            searchBtn.prop('disabled', true);
            searchBtn.html('<span class="btn-content"><i class="fas fa-spinner fa-spin me-2"></i>กำลังค้นหา...</span>');

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
                    console.log('🔧 grecaptcha.ready() called');

                    grecaptcha.execute(window.RECAPTCHA_SITE_KEY, {
                        action: 'corruption_track_search'
                    }).then(function (token) {
                        console.log('✅ reCAPTCHA token received:', token.substring(0, 50) + '...');
                        console.log('📏 Token length:', token.length);

                        performSearchWithRecaptcha(reportId, token, searchBtn, originalContent);
                    }).catch(function (error) {
                        console.error('❌ reCAPTCHA execution failed:', error);
                        console.log('🔄 Falling back to search without reCAPTCHA');
                        performSearchWithoutRecaptcha(reportId, searchBtn, originalContent);
                    });
                });
            } else {
                console.log('⚠️ reCAPTCHA not available, searching without verification');
                console.log('📋 Reasons breakdown:');
                console.log('- SITE_KEY exists:', !!window.RECAPTCHA_SITE_KEY);
                console.log('- reCAPTCHA ready:', !!window.recaptchaReady);
                console.log('- Skip dev mode:', !!window.SKIP_RECAPTCHA_FOR_DEV);
                console.log('- grecaptcha available:', typeof grecaptcha !== 'undefined');

                performSearchWithoutRecaptcha(reportId, searchBtn, originalContent);
            }
        });

        // *** เพิ่ม: Search Function พร้อม reCAPTCHA ***
        function performSearchWithRecaptcha(reportId, recaptchaToken, searchBtn, originalContent) {
            console.log('📤 Submitting with reCAPTCHA token...');

            isSearching = true;
            $('#reportIdInput').prop('disabled', true);

            // AJAX Search with reCAPTCHA
            $.ajax({
                url: '<?php echo site_url("Corruption/search_report"); ?>',
                type: 'POST',
                data: {
                    report_id: reportId,
                    'g-recaptcha-response': recaptchaToken,
                    recaptcha_action: 'corruption_track_search',
                    recaptcha_source: 'track_search_form',
                    ajax_request: '1',
                    client_timestamp: new Date().toISOString(),
                    user_agent_info: navigator.userAgent,
                    is_anonymous: '0'
                },
                dataType: 'json',
                timeout: 15000,
                success: function (response) {
                    handleSearchResponse(response, reportId);
                },
                error: function (xhr, status, error) {
                    handleSearchError(xhr, status, error);
                },
                complete: function () {
                    restoreSearchButton(searchBtn, originalContent);
                }
            });
        }

        // *** เพิ่ม: Search Function แบบปกติ ***
        function performSearchWithoutRecaptcha(reportId, searchBtn, originalContent) {
            console.log('📤 Submitting without reCAPTCHA...');

            isSearching = true;
            $('#reportIdInput').prop('disabled', true);

            // AJAX Search without reCAPTCHA
            $.ajax({
                url: '<?php echo site_url("Corruption/search_report"); ?>',
                type: 'POST',
                data: {
                    report_id: reportId,
                    dev_mode: '1'
                },
                dataType: 'json',
                timeout: 15000,
                success: function (response) {
                    handleSearchResponse(response, reportId);
                },
                error: function (xhr, status, error) {
                    handleSearchError(xhr, status, error);
                },
                complete: function () {
                    restoreSearchButton(searchBtn, originalContent);
                }
            });
        }

        // *** เพิ่ม: จัดการ Response ***
        function handleSearchResponse(response, reportId) {
            if (response.success && response.data) {
                // Redirect to track status page with result
                const params = new URLSearchParams();
                params.append('report_id', reportId);
                window.location.href = '<?php echo site_url("Corruption/track_status"); ?>?' + params.toString();
            } else {
                let errorMessage = response.message || 'ไม่พบรายงานที่ระบุ';

                // จัดการ error จาก reCAPTCHA
                if (response.error_type === 'recaptcha_failed') {
                    errorMessage = 'การยืนยันความปลอดภัยไม่ผ่าน กรุณาลองใหม่อีกครั้ง';
                } else if (response.error_type === 'recaptcha_missing') {
                    errorMessage = 'ไม่พบข้อมูลการยืนยันความปลอดภัย';
                }

                showAlert(errorMessage, 'error');
            }
        }

        // *** เพิ่ม: จัดการ Error ***
        function handleSearchError(xhr, status, error) {
            let errorMessage = 'เกิดข้อผิดพลาดในการค้นหา';

            if (status === 'timeout') {
                errorMessage = 'การค้นหาใช้เวลานานเกินไป กรุณาลองใหม่อีกครั้ง';
            } else if (xhr.status === 404) {
                errorMessage = 'ไม่พบรายงานที่ระบุ';
            } else if (xhr.status >= 500) {
                errorMessage = 'เกิดข้อผิดพลาดในเซิร์ฟเวอร์';
            }

            showAlert(errorMessage, 'error');
        }

        // *** เพิ่ม: คืนค่าปุ่มเป็นสถานะเดิม ***
        function restoreSearchButton(searchBtn, originalContent) {
            searchBtn.html(originalContent).removeClass('loading');
            $('#reportIdInput').prop('disabled', false);
            searchBtn.prop('disabled', false);
            isSearching = false;
        }

        // *** Search Function - เก็บไว้เป็น fallback ***
        function performSearch(reportId) {
            performSearchWithoutRecaptcha(reportId, $('#searchBtn'), $('#searchBtn').html());
        }

        // *** Reset Search Function ***
        window.resetSearch = function () {
            $('#reportIdInput').val('').removeClass('is-valid is-invalid').focus();
            $('#searchBtn').prop('disabled', false);

            // Remove URL parameters and reload
            const url = new URL(window.location);
            url.search = '';
            window.history.replaceState({}, document.title, url.toString());

            // Hide result section if exists
            $('.track-result-card, .track-error-state').parent().fadeOut(300, function () {
                $(this).remove();
            });
        };

        // *** Copy Report ID Function ***
        window.copyReportId = function (reportId) {
            if (navigator.clipboard) {
                navigator.clipboard.writeText(reportId).then(() => {
                    showAlert('คัดลอกหมายเลข ' + reportId + ' สำเร็จ', 'success');
                }).catch(() => {
                    fallbackCopy(reportId);
                });
            } else {
                fallbackCopy(reportId);
            }
        };

        // *** Print Report Function ***
        window.printReport = function () {
            // Hide search form and show only result
            $('.track-search-form, .track-guidelines, .track-actions').hide();

            window.print();

            // Show hidden elements back
            setTimeout(() => {
                $('.track-search-form, .track-guidelines, .track-actions').show();
            }, 1000);
        };

        // *** Fallback Copy Function ***
        function fallbackCopy(text) {
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

        // *** Alert Function ***
        function showAlert(message, type) {
            if (typeof Swal !== 'undefined') {
                const iconMap = {
                    'success': 'success',
                    'error': 'error',
                    'warning': 'warning',
                    'info': 'info'
                };

                Swal.fire({
                    icon: iconMap[type] || 'info',
                    title: message,
                    timer: 4000,
                    showConfirmButton: true,
                    confirmButtonText: 'ตกลง',
                    confirmButtonColor: '#e74c3c'
                });
            } else {
                alert(message);
            }
        }

        // *** Keyboard Shortcuts ***
        $(document).keydown(function (e) {
            // Enter = Search (when focused on input)
            if (e.keyCode === 13 && $('#reportIdInput').is(':focus')) {
                $('#trackSearchForm').submit();
            }

            // Escape = Clear input and focus
            if (e.keyCode === 27) {
                $('#reportIdInput').val('').removeClass('is-valid is-invalid').focus();
            }

            // Ctrl + K = Focus search (like modern apps)
            if (e.ctrlKey && e.keyCode === 75) {
                e.preventDefault();
                $('#reportIdInput').focus().select();
            }
        });

        // *** Auto-fill from URL parameter ***
        const urlParams = new URLSearchParams(window.location.search);
        const urlReportId = urlParams.get('report_id');
        if (urlReportId && !$('#reportIdInput').val()) {
            $('#reportIdInput').val(urlReportId.toUpperCase());
            validateInput();
        }

        // *** Example ID Suggestion ***
        function showExampleSuggestion() {
            if (!$('#reportIdInput').val()) {
                $('#reportIdInput').attr('placeholder', 'กรอกหมายเลขติดตาม');
            }
        }

        // *** Auto-suggest correction ***
        $('#reportIdInput').on('blur', function () {
            const value = $(this).val();

            // If user typed only numbers, suggest adding COR prefix
            if (value && /^\d{7}$/.test(value)) {
                showAlert('คุณหมายถึง "COR' + value + '" ใช่หรือไม่?', 'info');
                $(this).val('COR' + value);
                validateInput();
            }
        });

        // *** Show random example every 10 seconds ***
        setInterval(showExampleSuggestion, 10000);

        // *** Analytics Tracking (if needed) ***
        $('#trackSearchForm').on('submit', function () {
            // Track search attempt
            console.log('Search attempted:', $('#reportIdInput').val());
        });

        // *** เพิ่ม: ตรวจสอบการโหลด reCAPTCHA ***
        if (window.RECAPTCHA_SITE_KEY && !window.recaptchaReady) {
            console.log('⏳ Waiting for reCAPTCHA to load...');

            let checkInterval = setInterval(function () {
                if (window.recaptchaReady) {
                    console.log('✅ reCAPTCHA is now ready');
                    clearInterval(checkInterval);
                }
            }, 100);

            setTimeout(function () {
                if (!window.recaptchaReady) {
                    console.log('⚠️ reCAPTCHA timeout after 10 seconds');
                    clearInterval(checkInterval);
                }
            }, 10000);
        }

        // console.log('✅ ระบบติดตามสถานะพร้อมใช้งาน');
    });
</script>