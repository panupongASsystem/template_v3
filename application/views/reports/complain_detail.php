<?php
/**
 * Complain Detail View - หน้ารายละเอียดเรื่องร้องเรียน (ปรับปรุงใหม่)
 * Path: views/reports/complain_detail.php
 */

// Helper function สำหรับ status styling
if (!function_exists('get_complain_status_class')) {
    function get_complain_status_class($status) {
        switch($status) {
            case 'รอรับเรื่อง': return 'waiting';
            case 'รับเรื่องแล้ว': return 'received';
            case 'รอดำเนินการ': return 'pending';
            case 'กำลังดำเนินการ': return 'processing';
            case 'ดำเนินการเรียบร้อย': 
            case 'เสร็จสิ้น': return 'completed';
            case 'ยกเลิก': return 'cancelled';
            default: return 'waiting';
        }
    }
}

if (!function_exists('get_complain_status_icon')) {
    function get_complain_status_icon($status) {
        switch($status) {
            case 'รอรับเรื่อง': return 'fas fa-hourglass-half';
            case 'รับเรื่องแล้ว': return 'fas fa-inbox';
            case 'รอดำเนินการ': return 'fas fa-clock';
            case 'กำลังดำเนินการ': return 'fas fa-cogs';
            case 'ดำเนินการเรียบร้อย': 
            case 'เสร็จสิ้น': return 'fas fa-check-double';
            case 'ยกเลิก': return 'fas fa-times-circle';
            default: return 'fas fa-question-circle';
        }
    }
}

// Helper function สำหรับตรวจสอบประเภทไฟล์
if (!function_exists('get_complain_file_type_info')) {
    function get_complain_file_type_info($file_name) {
        $extension = strtolower(pathinfo($file_name, PATHINFO_EXTENSION));
        
        $image_types = ['jpg', 'jpeg', 'png', 'gif', 'bmp', 'webp', 'svg'];
        $pdf_types = ['pdf'];
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
if (!function_exists('format_complain_address')) {
    function format_complain_address($complain, $user_details = null) {
        $address_parts = [];
        
        // ลองเอาจาก user_details ก่อน (สำหรับ public/staff user)
        if (isset($user_details['user_info'])) {
            $user_info = $user_details['user_info'];
            
            if ($complain->complain_user_type === 'public') {
                // สำหรับ public user
                if (!empty($user_info->mp_address)) {
                    $address_parts[] = $user_info->mp_address;
                }
                if (!empty($user_info->mp_district)) {
                    $address_parts[] = 'ตำบล' . $user_info->mp_district;
                }
                if (!empty($user_info->mp_amphoe)) {
                    $address_parts[] = 'อำเภอ' . $user_info->mp_amphoe;
                }
                if (!empty($user_info->mp_province)) {
                    $address_parts[] = 'จังหวัด' . $user_info->mp_province;
                }
                if (!empty($user_info->mp_zipcode)) {
                    $address_parts[] = $user_info->mp_zipcode;
                }
            }
        }
        
        // หากไม่มีข้อมูลจาก user หรือเป็น guest ให้ใช้ข้อมูลจาก complain
        if (empty($address_parts)) {
            if (!empty($complain->complain_address)) {
                $address_parts[] = $complain->complain_address;
            }
            
            // เพิ่มข้อมูลที่อยู่แยกย่อยจาก complain table (หากมี)
            if (!empty($complain->guest_district)) {
                $address_parts[] = 'ตำบล' . $complain->guest_district;
            }
            if (!empty($complain->guest_amphoe)) {
                $address_parts[] = 'อำเภอ' . $complain->guest_amphoe;
            }
            if (!empty($complain->guest_province)) {
                $address_parts[] = 'จังหวัด' . $complain->guest_province;
            }
            if (!empty($complain->guest_zipcode)) {
                $address_parts[] = $complain->guest_zipcode;
            }
        }
        
        return !empty($address_parts) ? implode(' ', $address_parts) : 'ไม่ระบุ';
    }
}

// Helper function สำหรับเซ็นเซอร์ข้อมูลส่วนตัว
if (!function_exists('censor_personal_data')) {
    function censor_personal_data($data, $type = 'phone') {
        if (empty($data)) return 'ไม่ระบุ';
        
        if ($type === 'phone' && strlen($data) >= 6) {
            return substr($data, 0, 3) . str_repeat('*', 4) . substr($data, -3);
        } elseif ($type === 'email' && strpos($data, '@') !== false) {
            $parts = explode('@', $data);
            $username = $parts[0];
            $domain = $parts[1];
            if (strlen($username) > 3) {
                return substr($username, 0, 2) . str_repeat('*', 3) . '@' . $domain;
            }
        }
        
        return $data;
    }
}
?>

<style>
/* ===== Complain Detail Specific Styles ===== */
.page-header {
    background: linear-gradient(135deg, #fff7ed 0%, #fed7aa 100%);
    color: #ea580c;
    padding: 2rem;
    border-radius: 15px;
    margin-bottom: 2rem;
    box-shadow: 0 4px 15px rgba(0,0,0,0.08);
}

.page-header h1 {
    font-size: 2rem;
    font-weight: 600;
    margin: 0;
    color: #ea580c;
}

.page-header .breadcrumb {
    background: transparent;
    padding: 0;
    margin: 10px 0 0 0;
}

.complain-detail-container {
    background: white;
    border-radius: 15px;
    overflow: hidden;
    box-shadow: 0 4px 15px rgba(0,0,0,0.08);
    margin-bottom: 2rem;
}

.complain-detail-header {
    background: linear-gradient(135deg, #f8fafc 0%, #e2e8f0 100%);
    padding: 1.5rem;
    border-bottom: 1px solid #e2e8f0;
}

.complain-id-badge {
    background: linear-gradient(135deg, #fb923c, #ea580c);
    color: white;
    padding: 0.5rem 1rem;
    border-radius: 25px;
    font-size: 1.1rem;
    font-weight: 700;
    display: inline-block;
    margin-bottom: 1rem;
}

.complain-status-current {
    display: inline-flex;
    align-items: center;
    gap: 0.5rem;
    padding: 0.5rem 1rem;
    border-radius: 20px;
    font-weight: 600;
    font-size: 0.9rem;
    margin-left: 1rem;
}

.complain-status-current.waiting {
    background: #fff3cd;
    color: #d68910;
    border: 1px solid #ffb74d;
}

.complain-status-current.received {
    background: #f3e5f5;
    color: #6a1b9a;
    border: 1px solid #9c27b0;
}

.complain-status-current.pending {
    background: #fef3c7;
    color: #d97706;
    border: 1px solid #fbbf24;
}

.complain-status-current.processing {
    background: #e0f2fe;
    color: #0277bd;
    border: 1px solid #29b6f6;
}

.complain-status-current.completed {
    background: #e8f5e8;
    color: #2e7d32;
    border: 1px solid #66bb6a;
}

.complain-status-current.cancelled {
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

.censored-data {
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
    color: #fb923c;
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
.timeline-icon.pending { background: linear-gradient(135deg, #fbbf24, #d97706); }
.timeline-icon.processing { background: linear-gradient(135deg, #29b6f6, #0277bd); }
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

.images-grid {
    display: grid;
    grid-template-columns: repeat(auto-fill, minmax(250px, 1fr));
    gap: 1rem;
    padding: 1.5rem;
}

.image-card {
    background: #f8fafc;
    border: 1px solid #e2e8f0;
    border-radius: 8px;
    padding: 1rem;
    transition: all 0.3s ease;
    position: relative;
}

.image-card:hover {
    background: #f1f5f9;
    border-color: #fb923c;
    transform: translateY(-2px);
    box-shadow: 0 4px 12px rgba(251, 146, 60, 0.15);
}

.image-card.image-file {
    border-left: 4px solid #e74c3c;
}

.image-thumbnail {
    width: 100%;
    height: 120px;
    border-radius: 6px;
    object-fit: cover;
    margin-bottom: 0.75rem;
    cursor: pointer;
    transition: all 0.3s ease;
    border: 2px solid #e2e8f0;
}

.image-thumbnail:hover {
    border-color: #fb923c;
    transform: scale(1.02);
}

.image-info-overlay {
    margin-bottom: 0.75rem;
}

.image-info-overlay .image-name {
    font-weight: 600;
    color: #1e293b;
    font-size: 0.9rem;
    white-space: nowrap;
    overflow: hidden;
    text-overflow: ellipsis;
    margin-bottom: 0.25rem;
}

.image-info-overlay .image-meta {
    font-size: 0.8rem;
    color: #64748b;
}

.timeline-images {
    margin-top: 1rem;
    padding-top: 1rem;
    border-top: 1px solid #e2e8f0;
}

.timeline-images-header {
    font-size: 0.9rem;
    font-weight: 600;
    color: #64748b;
    margin-bottom: 0.75rem;
    display: flex;
    align-items: center;
    gap: 0.5rem;
}

.timeline-images-grid {
    display: grid;
    grid-template-columns: repeat(auto-fill, minmax(100px, 1fr));
    gap: 0.75rem;
}

.timeline-image-item {
    position: relative;
    border-radius: 8px;
    overflow: hidden;
    box-shadow: 0 2px 8px rgba(0,0,0,0.08);
    transition: all 0.3s ease;
    border: 2px solid #e2e8f0;
    background: white;
    cursor: pointer;
}

.timeline-image-item:hover {
    transform: translateY(-2px) scale(1.03);
    box-shadow: 0 4px 12px rgba(251, 146, 60, 0.15);
    border-color: #fb923c;
}

.timeline-image-item img {
    width: 100%;
    height: 100px;
    object-fit: cover;
    cursor: pointer;
    transition: transform 0.3s ease;
}

.timeline-image-item:hover img {
    transform: scale(1.05);
}

.image-actions {
    display: flex;
    gap: 0.5rem;
    margin-top: 0.75rem;
}

.image-action-btn {
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
    background: linear-gradient(135deg, #fb923c, #ea580c);
    color: white;
}

.btn-preview:hover {
    background: linear-gradient(135deg, #ea580c, #d97706);
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

/* User Type Badges */
.user-type-badge {
    display: inline-block;
    padding: 0.4rem 0.8rem;
    border-radius: 20px;
    font-size: 0.8rem;
    font-weight: 600;
    text-transform: uppercase;
    letter-spacing: 0.5px;
}

.user-type-badge.guest {
    background: #f3f4f6;
    color: #6b7280;
    border: 1px solid #d1d5db;
}

.user-type-badge.public {
    background: #dbeafe;
    color: #1d4ed8;
    border: 1px solid #93c5fd;
}

.user-type-badge.staff {
    background: #fef3c7;
    color: #d97706;
    border: 1px solid #fcd34d;
}

.user-type-badge.anonymous {
    background: #f3f4f6;
    color: #374151;
    border: 1px solid #9ca3af;
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
    
    .images-grid {
        grid-template-columns: 1fr;
        padding: 1rem;
    }
    
    .timeline-images-grid {
        grid-template-columns: repeat(auto-fill, minmax(80px, 1fr));
        gap: 0.5rem;
    }
    
    .timeline-image-item img {
        height: 80px;
    }
    
    .action-buttons {
        flex-direction: column;
    }
    
    .btn-action {
        width: 100%;
    }
    
    .image-actions {
        flex-direction: column;
    }
    
    .image-action-btn {
        width: 100%;
        justify-content: center;
    }
}
</style>

<div class="container-fluid">
    <!-- Page Header -->
    <div class="page-header">
        <h1>
            <i class="fas fa-file-alt me-3"></i>รายละเอียดแจ้งเรื่อง ร้องเรียน
        </h1>
    </div>

    <?php if (!isset($complain) || !$complain): ?>
        <!-- Empty State -->
        <div class="section-card">
            <div class="empty-state">
                <i class="fas fa-search"></i>
                <h5>ไม่พบข้อมูลแจ้งเรื่อง ร้องเรียน</h5>
                <p>รหัสเรื่องร้องเรียนที่ระบุไม่พบในระบบ</p>
                <a href="<?= site_url('System_reports/complain_report') ?>" class="btn btn-primary">
                    <i class="fas fa-arrow-left me-2"></i>กลับไปรายงานแจ้งเรื่อง ร้องเรียน
                </a>
            </div>
        </div>
    <?php else: ?>
        <!-- Complain Information -->
        <div class="complain-detail-container">
            <div class="complain-detail-header">
                <div class="d-flex align-items-center flex-wrap">
                    <span class="complain-id-badge">
                        <i class="fas fa-file-alt me-2"></i>#<?= htmlspecialchars($complain->complain_id) ?>
                    </span>
                    <span class="complain-status-current <?= get_complain_status_class($complain->complain_status) ?>">
                        <i class="<?= get_complain_status_icon($complain->complain_status) ?>"></i>
                        <?= htmlspecialchars($complain->complain_status) ?>
                    </span>
                </div>
            </div>
            
            <div class="info-grid">
                <div class="info-item">
                    <div class="info-label">หัวข้อเรื่อง</div>
                    <div class="info-value large"><?= htmlspecialchars($complain->complain_topic ?? '') ?></div>
                </div>
                
                <div class="info-item">
                    <div class="info-label">ประเภทเรื่อง</div>
                    <div class="info-value"><?= htmlspecialchars($complain->complain_type ?? 'ไม่ระบุ') ?></div>
                </div>
                
                <div class="info-item">
                    <div class="info-label">ผู้แจ้งเรื่อง</div>
                    <div class="info-value">
                        <?php 
                        if (isset($user_details['user_info'])) {
                            $user_info = $user_details['user_info'];
                            
                            if ($complain->complain_user_type === 'public') {
                                echo htmlspecialchars(($user_info->mp_prefix ?? '') . ' ' . ($user_info->mp_fname ?? '') . ' ' . ($user_info->mp_lname ?? ''));
                            } elseif ($complain->complain_user_type === 'staff') {
                                echo htmlspecialchars(($user_info->m_fname ?? '') . ' ' . ($user_info->m_lname ?? ''));
                            } else {
                                echo htmlspecialchars($complain->complain_by ?? '');
                            }
                        } else {
                            echo htmlspecialchars($complain->complain_by ?? '');
                        }
                        ?>
                    </div>
                </div>
                
                <div class="info-item">
                    <div class="info-label">เบอร์โทรศัพท์</div>
                    <div class="info-value">
                        <?php 
                        $phone = '';
                        if (isset($user_details['user_info'])) {
                            $user_info = $user_details['user_info'];
                            
                            if ($complain->complain_user_type === 'public') {
                                $phone = $user_info->mp_phone ?? $complain->complain_phone;
                            } elseif ($complain->complain_user_type === 'staff') {
                                $phone = $user_info->m_phone ?? $complain->complain_phone;
                            } else {
                                $phone = $complain->complain_phone;
                            }
                        } else {
                            $phone = $complain->complain_phone;
                        }
                        
                        if (!empty($phone) && $phone !== '0000000000'): 
                        ?>
                            <span class="censored-data"><?= censor_personal_data($phone, 'phone') ?></span>
                        <?php else: ?>
                            ไม่ระบุ
                        <?php endif; ?>
                    </div>
                </div>
                
                <div class="info-item">
                    <div class="info-label">อีเมล</div>
                    <div class="info-value">
                        <?php 
                        $email = '';
                        if (isset($user_details['user_info'])) {
                            $user_info = $user_details['user_info'];
                            
                            if ($complain->complain_user_type === 'public') {
                                $email = $user_info->mp_email ?? $complain->complain_email;
                            } elseif ($complain->complain_user_type === 'staff') {
                                $email = $user_info->m_email ?? $complain->complain_email;
                            } else {
                                $email = $complain->complain_email;
                            }
                        } else {
                            $email = $complain->complain_email;
                        }
                        
                        if (!empty($email) && $email !== 'ไม่ระบุตัวตน' && $email !== 'ไม่ระบุ'): 
                        ?>
                            <a href="mailto:<?= $email ?>" class="text-primary">
                                <?= htmlspecialchars($email) ?>
                            </a>
                        <?php else: ?>
                            ไม่ระบุ
                        <?php endif; ?>
                    </div>
                </div>
                
                <div class="info-item">
                    <div class="info-label">ที่อยู่</div>
                    <div class="info-value"><?= format_complain_address($complain, $user_details ?? null) ?></div>
                </div>
                
                <div class="info-item">
                    <div class="info-label">วันที่แจ้งเรื่อง</div>
                    <div class="info-value">
                        <?php
                        if (isset($complain->complain_datesave)) {
                            echo date('d/m/Y H:i', strtotime($complain->complain_datesave)) . ' น.';
                        } else {
                            echo 'ไม่ระบุ';
                        }
                        ?>
                    </div>
                </div>
                
                <div class="info-item">
                    <div class="info-label">ประเภทผู้ใช้</div>
                    <div class="info-value">
                        <?php 
                        $user_type_text = $user_details['user_type_display'] ?? 'ไม่ทราบ';
                        $user_type_class = '';
                        
                        switch($complain->complain_user_type ?? '') {
                            case 'public':
                                $user_type_class = 'public';
                                break;
                            case 'staff':
                                $user_type_class = 'staff';
                                break;
                            case 'guest':
                                $user_type_class = 'guest';
                                break;
                            case 'anonymous':
                                $user_type_class = 'anonymous';
                                break;
                            default:
                                $user_type_class = 'guest';
                        }
                        ?>
                        <span class="user-type-badge <?= $user_type_class ?>">
                            <?= $user_type_text ?>
                        </span>
                    </div>
                </div>
                
                <?php if (!empty($complain->complain_lat) && !empty($complain->complain_long)): ?>
                <div class="info-item">
                    <div class="info-label">พิกัดที่เกิดเหตุ</div>
                    <div class="info-value">
                        <a href="https://maps.google.com/?q=<?= $complain->complain_lat ?>,<?= $complain->complain_long ?>" 
                           target="_blank" class="text-primary">
                            <i class="fas fa-map-marked-alt me-1"></i>
                            <?= $complain->complain_lat ?>, <?= $complain->complain_long ?>
                            <i class="fas fa-external-link-alt ms-1"></i>
                        </a>
                    </div>
                </div>
                <?php endif; ?>
            </div>
            
            <?php if (!empty($complain->complain_detail)): ?>
                <div style="padding: 0 1.5rem 1.5rem;">
                    <div class="info-item">
                        <div class="info-label">รายละเอียดแจ้งเรื่อง ร้องเรียน</div>
                        <div class="info-value" style="background: #f8fafc; padding: 1rem; border-radius: 8px; border-left: 4px solid #fb923c;">
                            <?= nl2br(htmlspecialchars($complain->complain_detail)) ?>
                        </div>
                    </div>
                </div>
            <?php endif; ?>
        </div>

        <!-- Attached Images -->
        <?php if (isset($complain_images) && !empty($complain_images)): ?>
        <div class="section-card">
            <div class="section-header">
                <i class="section-icon fas fa-images"></i>
                <h3 class="section-title">รูปภาพประกอบ</h3>
                <span class="badge bg-primary ms-auto"><?= count($complain_images) ?> รูป</span>
            </div>
            
            <div class="images-grid">
                <?php foreach ($complain_images as $image): ?>
                    <div class="image-card image-file">
                        <img class="image-thumbnail" 
                             src="<?= base_url('docs/complain/' . $image->complain_img_img) ?>" 
                             alt="รูปภาพประกอบ"
                             onclick="previewImage('<?= base_url('docs/complain/' . $image->complain_img_img) ?>', 'รูปภาพประกอบ')"
                             onerror="this.style.display='none'; this.nextElementSibling.style.display='flex';">
                        
                        <div class="image-info-overlay">
                            <div class="image-name">รูปภาพประกอบ #<?= $image->complain_img_id ?></div>
                            <div class="image-meta">
                                <?= date('d/m/Y H:i', strtotime($image->complain_img_datesave ?? '')) ?>
                            </div>
                        </div>
                        
                        <div class="image-actions">
                            <button class="image-action-btn btn-download" 
                                    onclick="downloadImage('<?= base_url('docs/complain/' . $image->complain_img_img) ?>')">
                                <i class="fas fa-download"></i>
                                ดาวน์โหลด
                            </button>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
        </div>
        <?php endif; ?>

        <!-- Complain History Timeline -->
        <div class="section-card">
            <div class="section-header">
                <i class="section-icon fas fa-history"></i>
                <h3 class="section-title">ประวัติการดำเนินงาน</h3>
            </div>
            
            <div class="timeline">
                <!-- Initial Report -->
                <div class="timeline-item">
                    <div class="timeline-icon waiting">
                        <i class="fas fa-file-plus"></i>
                    </div>
                    <div class="timeline-content">
                        <div class="timeline-status">แจ้งเรื่อง ร้องเรียน</div>
                        <div class="timeline-meta">
                            <span><i class="fas fa-user me-1"></i><?= htmlspecialchars($complain->complain_by ?? '') ?></span>
                            <span><i class="fas fa-clock me-1"></i><?= date('d/m/Y H:i', strtotime($complain->complain_datesave)) ?> น.</span>
                        </div>
                        <div class="timeline-comment">
                            เรื่องร้องเรียนถูกส่งเข้าระบบ: <?= htmlspecialchars($complain->complain_topic ?? '') ?>
                        </div>
                    </div>
                </div>

                <!-- Progress Updates -->
                <?php if (isset($complain_details) && !empty($complain_details)): ?>
                    <?php foreach ($complain_details as $detail): ?>
                        <div class="timeline-item">
                            <div class="timeline-icon <?= get_complain_status_class($detail->complain_detail_status) ?>">
                                <i class="<?= get_complain_status_icon($detail->complain_detail_status) ?>"></i>
                            </div>
                            <div class="timeline-content">
                                <div class="timeline-status"><?= htmlspecialchars($detail->complain_detail_status) ?></div>
                                <div class="timeline-meta">
                                    <span><i class="fas fa-user me-1"></i><?= htmlspecialchars($detail->complain_detail_by ?? '') ?></span>
                                    <span><i class="fas fa-clock me-1"></i><?= date('d/m/Y H:i', strtotime($detail->complain_detail_datesave)) ?> น.</span>
                                </div>
                                <?php if (!empty($detail->complain_detail_com)): ?>
                                    <div class="timeline-comment">
                                        <?= nl2br(htmlspecialchars($detail->complain_detail_com)) ?>
                                    </div>
                                <?php endif; ?>
                                
                                <!-- Status Update Images -->
                                <?php if (isset($detail->status_images) && !empty($detail->status_images)): ?>
                                    <div class="timeline-images">
                                        <div class="timeline-images-header">
                                            <i class="fas fa-images"></i>
                                            รูปภาพประกอบการอัปเดต (<?= count($detail->status_images) ?> รูป)
                                        </div>
                                        <div class="timeline-images-grid">
                                            <?php foreach ($detail->status_images as $statusImage): ?>
                                                <?php 
                                                $image_path = FCPATH . 'docs/complain/status/' . $statusImage->image_filename;
                                                $image_url = base_url('docs/complain/status/' . $statusImage->image_filename);
                                                $file_exists = file_exists($image_path);
                                                ?>
                                                
                                                <div class="timeline-image-item <?= !$file_exists ? 'image-missing' : '' ?>" 
                                                     onclick="previewImage('<?= $image_url ?>', '<?= htmlspecialchars($statusImage->image_original_name ?? $statusImage->image_filename) ?>')">
                                                    
                                                    <?php if ($file_exists): ?>
                                                        <img src="<?= $image_url ?>" 
                                                             alt="รูปภาพการอัปเดตสถานะ"
                                                             onerror="this.parentNode.classList.add('image-error'); this.style.display='none';">
                                                    <?php else: ?>
                                                        <div style="height: 100px; display: flex; align-items: center; justify-content: center; background: #f3f4f6; color: #6b7280; font-size: 0.8rem;">
                                                            <i class="fas fa-image-slash me-1"></i>ไฟล์หายไป
                                                        </div>
                                                    <?php endif; ?>
                                                </div>
                                            <?php endforeach; ?>
                                        </div>
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

        <!-- Action Buttons -->
        <div class="action-buttons">
            <a href="<?= site_url('System_reports/complain') ?>" class="btn-action btn-back">
                <i class="fas fa-arrow-left"></i>
                กลับ
            </a>
            
            <a href="<?= site_url('System_reports/export_complain_detail/' . $complain->complain_id . '?type=preview') ?>" 
               class="btn-action btn-print" target="_blank">
                <i class="fas fa-file-pdf"></i>
                พิมพ์
            </a>
            
            <a href="<?= site_url('System_reports/export_complain_detail/' . $complain->complain_id . '?type=csv') ?>" 
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

<!-- SweetAlert2 -->
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

<script>
/**
 * Preview image function
 */
function previewImage(imageSrc, imageTitle = 'รูปภาพประกอบ') {
    if (!imageSrc) {
        Swal.fire({
            icon: 'error',
            title: 'เกิดข้อผิดพลาด',
            text: 'ไม่พบที่อยู่รูปภาพ'
        });
        return;
    }
    
    const modal = document.getElementById('imagePreviewModal');
    const img = document.getElementById('previewImage');
    const fileNameElement = document.getElementById('previewFileName');
    
    img.src = imageSrc;
    fileNameElement.textContent = imageTitle;
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
 * Download image function
 */
function downloadImage(imageSrc) {
    if (!imageSrc) {
        Swal.fire({
            icon: 'error',
            title: 'เกิดข้อผิดพลาด',
            text: 'ไม่พบที่อยู่รูปภาพ'
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
    const link = document.createElement('a');
    link.href = imageSrc;
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
 * Copy complain ID to clipboard
 */
function copyComplainId() {
    const complainId = '<?= $complain->complain_id ?? '' ?>';
    
    if (navigator.clipboard) {
        navigator.clipboard.writeText(complainId).then(() => {
            Swal.fire({
                icon: 'success',
                title: 'คัดลอกสำเร็จ',
                text: 'รหัสเรื่องร้องเรียน ' + complainId + ' ถูกคัดลอกแล้ว',
                timer: 2000,
                showConfirmButton: false
            });
        });
    } else {
        // Fallback for older browsers
        const textArea = document.createElement('textarea');
        textArea.value = complainId;
        document.body.appendChild(textArea);
        textArea.focus();
        textArea.select();
        try {
            document.execCommand('copy');
            Swal.fire({
                icon: 'success',
                title: 'คัดลอกสำเร็จ',
                text: 'รหัสเรื่องร้องเรียน ' + complainId + ' ถูกคัดลอกแล้ว',
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

// Add click event to complain ID for copying
document.addEventListener('DOMContentLoaded', function() {
    const complainIdBadge = document.querySelector('.complain-id-badge');
    if (complainIdBadge) {
        complainIdBadge.style.cursor = 'pointer';
        complainIdBadge.title = 'คลิกเพื่อคัดลอกรหัสเรื่องร้องเรียน';
        complainIdBadge.addEventListener('click', copyComplainId);
    }
    
    // Add hover effect to image cards
    const imageCards = document.querySelectorAll('.image-card');
    imageCards.forEach(card => {
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
            const actionsArea = card.querySelector('.image-actions');
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
        }
    });
});

// Print styles
const printStyles = `
<style media="print">
    .btn-action, .action-buttons, .image-actions {
        display: none !important;
    }
    
    .page-header {
        background: none !important;
        color: #000 !important;
        box-shadow: none !important;
    }
    
    .complain-detail-container,
    .section-card {
        box-shadow: none !important;
        border: 1px solid #ddd !important;
        page-break-inside: avoid;
    }
    
    .timeline-item {
        page-break-inside: avoid;
        margin-bottom: 1rem;
    }
    
    .image-preview-modal {
        display: none !important;
    }
    
    .image-thumbnail {
        max-height: 150px;
        page-break-inside: avoid;
    }
    
    .images-grid {
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