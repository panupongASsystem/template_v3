<?php
// application/views/member/google_drive_user_permissions.php
?>
<style>
    /* ===== Base Layout ===== */
    .permission-page-wrapper {
        min-height: 100vh;
        background: #f8fafc;
    }

    .permission-container {
        max-width: 1600px;
        margin: 0 auto;
        padding: 2rem;
    }

    /* ===== Header Section ===== */
    .permission-header {
        background: white;
        color: #1e293b;
        padding: 2rem;
        border-radius: 1rem;
        margin-bottom: 2rem;
        box-shadow: 0 1px 3px rgba(0, 0, 0, 0.1);
        border: 1px solid #e2e8f0;
    }

    .header-back-btn {
        background: #f1f5f9;
        border: 1px solid #e2e8f0;
        color: #475569;
        padding: 0.5rem 1rem;
        border-radius: 0.5rem;
        transition: all 0.3s ease;
        font-weight: 500;
    }

    .header-back-btn:hover {
        background: #e2e8f0;
        color: #1e293b;
        transform: translateX(-4px);
    }

    .permission-header h2 {
        color: #1e293b;
        font-weight: 600;
    }

    .permission-header p {
        color: #64748b;
    }

    .header-actions .btn {
        background: white;
        border: 1px solid #e2e8f0;
        color: #475569;
        margin-left: 0.5rem;
    }

    .header-actions .btn:hover {
        background: #f8fafc;
        border-color: #cbd5e1;
    }

    /* ===== Sidebar User Card ===== */
    .user-info-card {
        background: white;
        border-radius: 1rem;
        box-shadow: 0 1px 3px rgba(0, 0, 0, 0.1);
        overflow: hidden;
        position: sticky;
        top: 2rem;
        border: 1px solid #e2e8f0;
    }

    .user-avatar-section {
        background: white;
        padding: 2rem;
        text-align: center;
        border-bottom: 1px solid #e2e8f0;
    }

    .user-avatar {
        width: 100px;
        height: 100px;
        border-radius: 50%;
        background: #667eea;
        color: white;
        font-size: 2.5rem;
        font-weight: bold;
        display: flex;
        align-items: center;
        justify-content: center;
        margin: 0 auto 1rem;
        border: 4px solid #f1f5f9;
        box-shadow: 0 4px 12px rgba(102, 126, 234, 0.15);
    }

    .user-name {
        color: #1e293b;
        font-size: 1.5rem;
        font-weight: 600;
        margin-bottom: 0.5rem;
    }

    .user-position {
        color: #64748b;
        font-size: 0.95rem;
    }

    .status-badges {
        display: flex;
        justify-content: center;
        gap: 0.5rem;
        margin-top: 1rem;
    }

    .status-badge {
        padding: 0.35rem 0.75rem;
        border-radius: 20px;
        font-size: 0.85rem;
        font-weight: 500;
        background: #f1f5f9;
        color: #475569;
        border: 1px solid #e2e8f0;
    }

    /* ===== Contact Info ===== */
    .contact-info {
        padding: 1.5rem;
    }

    .contact-item {
        display: flex;
        align-items: center;
        padding: 0.75rem 0;
        border-bottom: 1px solid #f0f0f0;
    }

    .contact-item:last-child {
        border-bottom: none;
    }

    .contact-item i {
        width: 24px;
        color: #667eea;
    }

    .contact-item span {
        font-size: 0.9rem;
        color: #64748b;
    }

    /* ===== Statistics Card ===== */
    .statistics-card {
        background: white;
        border-radius: 1rem;
        box-shadow: 0 1px 3px rgba(0, 0, 0, 0.1);
        margin-top: 1.5rem;
        border: 1px solid #e2e8f0;
    }

    .stat-item {
        display: flex;
        justify-content: space-between;
        align-items: center;
        padding: 1rem 1.5rem;
        border-bottom: 1px solid #f0f0f0;
    }

    .stat-item:last-child {
        border-bottom: none;
    }

    .stat-label {
        font-size: 0.9rem;
        color: #64748b;
        font-weight: 500;
    }

    .stat-value {
        font-size: 1.5rem;
        font-weight: 700;
        color: #1e293b;
    }

    .stat-value.text-primary {
        color: #667eea;
    }

    .stat-value.text-success {
        color: #10b981;
    }

    .stat-value.text-info {
        color: #3b82f6;
    }

    .stat-value.text-warning {
        color: #f59e0b;
    }

    /* ===== Storage Progress ===== */
    .storage-section {
        padding: 1.5rem;
        border-top: 1px solid #f0f0f0;
    }

    .storage-progress {
        margin-top: 1rem;
    }

    .progress {
        height: 10px;
        border-radius: 10px;
        background: #f1f5f9;
        overflow: hidden;
        border: 1px solid #e2e8f0;
    }

    .progress-bar {
        height: 100%;
        background: #667eea;
        transition: width 0.5s ease;
    }

    /* ===== Main Content Card ===== */
    .main-content-card {
        background: white;
        border-radius: 1rem;
        box-shadow: 0 1px 3px rgba(0, 0, 0, 0.1);
        overflow: hidden;
        border: 1px solid #e2e8f0;
    }

    /* ===== Custom Tabs ===== */
    .permission-tabs {
        display: flex;
        border-bottom: 2px solid #e2e8f0;
        background: white;
    }

    .tab-button {
        flex: 1;
        padding: 1rem 1.5rem;
        border: none;
        background: transparent;
        color: #64748b;
        font-weight: 500;
        cursor: pointer;
        transition: all 0.3s ease;
        position: relative;
    }

    .tab-button:hover {
        background: #f8fafc;
        color: #667eea;
    }

    .tab-button.active {
        color: #667eea;
        background: white;
    }

    .tab-button.active::after {
        content: '';
        position: absolute;
        bottom: -2px;
        left: 0;
        right: 0;
        height: 2px;
        background: #667eea;
    }

    .tab-button i {
        margin-right: 0.5rem;
    }

    /* ===== Tab Content ===== */
    .tab-content-wrapper {
        padding: 2rem;
        background: white;
    }

    .tab-pane {
        display: none;
    }

    .tab-pane.active {
        display: block;
        animation: fadeIn 0.3s ease;
    }

    @keyframes fadeIn {
        from {
            opacity: 0;
            transform: translateY(10px);
        }

        to {
            opacity: 1;
            transform: translateY(0);
        }
    }

    /* ===== Folder Tree Styles ===== */
    .folder-tree-container {
        max-height: 500px;
        overflow-y: auto;
        border: 1px solid #e2e8f0;
        border-radius: 0.75rem;
        padding: 1rem;
        background: white;
    }

    .folder-tree-item {
        display: flex;
        align-items: center;
        padding: 0.75rem;
        margin: 0.25rem 0;
        border-radius: 0.5rem;
        cursor: pointer;
        transition: all 0.2s ease;
        background: white;
        border: 1px solid transparent;
    }

    .folder-tree-item:hover {
        background: #f8fafc;
        border-color: #e2e8f0;
    }

    .folder-tree-item.selected {
        background: #f5f3ff;
        border-color: #667eea;
        box-shadow: 0 1px 3px rgba(102, 126, 234, 0.1);
    }

    .folder-expand-btn {
        width: 24px;
        height: 24px;
        border: none;
        background: none;
        cursor: pointer;
        color: #94a3b8;
        transition: transform 0.2s ease;
    }

    .folder-expand-btn.expanded {
        transform: rotate(90deg);
    }

    .folder-icon {
        font-size: 1.2rem;
        margin: 0 0.5rem;
    }

    .folder-icon.system {
        color: #dc2626;
    }

    .folder-icon.department {
        color: #f59e0b;
    }

    .folder-icon.shared {
        color: #7c3aed;
    }

    .folder-icon.personal {
        color: #059669;
    }

    .folder-icon.admin {
        color: #3b82f6;
    }

    .folder-children {
        margin-left: 2rem;
        border-left: 2px solid #f1f5f9;
        padding-left: 0.5rem;
    }

    /* ===== Permission Cards ===== */
    .permission-card {
        background: white;
        border: 1px solid #e2e8f0;
        border-radius: 0.75rem;
        padding: 1.5rem;
        margin-bottom: 1rem;
        transition: all 0.3s ease;
    }

    .permission-card:hover {
        box-shadow: 0 2px 8px rgba(0, 0, 0, 0.08);
        transform: translateY(-2px);
        border-color: #cbd5e1;
    }

    .permission-card-header {
        display: flex;
        justify-content: space-between;
        align-items: center;
        margin-bottom: 1rem;
    }

    .permission-card-title {
        display: flex;
        align-items: center;
        font-weight: 600;
        color: #1e293b;
    }

    .permission-card-title i {
        margin-right: 0.75rem;
        font-size: 1.2rem;
    }

    /* ===== Permission Switch ===== */
    .permission-switch {
        position: relative;
        display: inline-block;
        width: 52px;
        height: 28px;
    }

    .permission-switch input {
        opacity: 0;
        width: 0;
        height: 0;
    }

    .permission-slider {
        position: absolute;
        cursor: pointer;
        top: 0;
        left: 0;
        right: 0;
        bottom: 0;
        background-color: #e2e8f0;
        transition: .4s;
        border-radius: 28px;
    }

    .permission-slider:before {
        position: absolute;
        content: "";
        height: 22px;
        width: 22px;
        left: 3px;
        bottom: 3px;
        background-color: white;
        transition: .4s;
        border-radius: 50%;
        box-shadow: 0 2px 4px rgba(0, 0, 0, 0.15);
    }

    input:checked+.permission-slider {
        background-color: #667eea;
    }

    input:checked+.permission-slider:before {
        transform: translateX(24px);
    }

    /* ===== Select Dropdown ===== */
    .permission-select {
        width: 100%;
        padding: 0.75rem 1rem;
        border: 1px solid #e2e8f0;
        border-radius: 0.5rem;
        font-size: 0.95rem;
        color: #1e293b;
        background: white;
        transition: all 0.3s ease;
    }

    .permission-select:focus {
        outline: none;
        border-color: #667eea;
        box-shadow: 0 0 0 3px rgba(102, 126, 234, 0.1);
    }

    /* ===== Current Permissions List ===== */
    .current-permissions-list {
        max-height: 400px;
        overflow-y: auto;
    }

    .permission-item {
        display: flex;
        justify-content: space-between;
        align-items: center;
        padding: 1rem;
        background: #f8fafc;
        border-radius: 0.75rem;
        margin-bottom: 0.75rem;
        border: 1px solid #e2e8f0;
    }

    .permission-item-info {
        flex: 1;
    }

    .permission-item-folder {
        font-weight: 600;
        color: #1e293b;
        margin-bottom: 0.25rem;
    }

    .permission-item-meta {
        font-size: 0.85rem;
        color: #64748b;
    }

    .permission-item-actions {
        display: flex;
        align-items: center;
        gap: 1rem;
    }

    /* ===== Permission History ===== */
    .permission-history-list {
        max-height: 500px;
        overflow-y: auto;
    }

    .history-item {
        display: flex;
        gap: 1rem;
        padding: 1rem;
        border-left: 3px solid #e2e8f0;
        margin-bottom: 1rem;
        transition: all 0.2s ease;
        background: white;
        border-radius: 0.5rem;
        border: 1px solid #e2e8f0;
    }

    .history-item:hover {
        background: #f8fafc;
        border-left-color: #667eea;
        box-shadow: 0 1px 3px rgba(0, 0, 0, 0.05);
    }

    .history-icon {
        width: 40px;
        height: 40px;
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        flex-shrink: 0;
    }

    .history-icon.added {
        background: #dcfce7;
        color: #10b981;
    }

    .history-icon.removed {
        background: #fee2e2;
        color: #ef4444;
    }

    .history-icon.updated {
        background: #dbeafe;
        color: #3b82f6;
    }

    .history-content {
        flex: 1;
        min-width: 0;
    }

    .history-action {
        font-weight: 600;
        color: #1e293b;
        margin-bottom: 0.5rem;
        word-wrap: break-word;
    }

    .history-meta {
        font-size: 0.85rem;
        color: #64748b;
        display: flex;
        flex-wrap: wrap;
        gap: 0.5rem;
    }

    .history-meta span {
        display: inline-flex;
        align-items: center;
    }

    /* ===== Form Controls ===== */
    .form-group {
        margin-bottom: 1.5rem;
    }

    .form-label {
        display: block;
        font-weight: 500;
        color: #1e293b;
        margin-bottom: 0.5rem;
    }

    .form-control {
        width: 100%;
        padding: 0.75rem 1rem;
        border: 1px solid #e2e8f0;
        border-radius: 0.5rem;
        font-size: 0.95rem;
        transition: all 0.3s ease;
        background: white;
    }

    .form-control:focus {
        outline: none;
        border-color: #667eea;
        box-shadow: 0 0 0 3px rgba(102, 126, 234, 0.1);
    }

    textarea.form-control {
        resize: vertical;
        min-height: 100px;
    }

    /* ===== Buttons ===== */
    .btn {
        padding: 0.75rem 1.5rem;
        border: none;
        border-radius: 0.5rem;
        font-weight: 500;
        cursor: pointer;
        transition: all 0.3s ease;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        gap: 0.5rem;
    }

    .btn-primary {
        background: #667eea;
        color: white;
        border: 1px solid #667eea;
    }

    .btn-primary:hover {
        background: #5568d3;
        border-color: #5568d3;
        transform: translateY(-1px);
        box-shadow: 0 4px 12px rgba(102, 126, 234, 0.25);
    }

    .btn-success {
        background: #10b981;
        color: white;
        border: 1px solid #10b981;
    }

    .btn-success:hover {
        background: #059669;
        border-color: #059669;
        transform: translateY(-1px);
        box-shadow: 0 4px 12px rgba(16, 185, 129, 0.25);
    }

    .btn-danger {
        background: #ef4444;
        color: white;
        border: 1px solid #ef4444;
    }

    .btn-danger:hover {
        background: #dc2626;
        border-color: #dc2626;
        transform: translateY(-1px);
        box-shadow: 0 4px 12px rgba(239, 68, 68, 0.25);
    }

    .btn-light {
        background: white;
        color: #475569;
        border: 1px solid #e2e8f0;
    }

    .btn-light:hover {
        background: #f8fafc;
        border-color: #cbd5e1;
    }

    .btn-sm {
        padding: 0.5rem 1rem;
        font-size: 0.9rem;
    }

    .btn-icon {
        width: 36px;
        height: 36px;
        padding: 0;
        border-radius: 50%;
    }

    /* ===== Empty State ===== */
    .empty-state {
        text-align: center;
        padding: 3rem 1rem;
        color: #94a3b8;
    }

    .empty-state i {
        font-size: 4rem;
        margin-bottom: 1rem;
        opacity: 0.5;
    }

    .empty-state p {
        font-size: 1rem;
        margin: 0;
    }

    /* ===== Loading State ===== */
    .loading-overlay {
        position: absolute;
        top: 0;
        left: 0;
        right: 0;
        bottom: 0;
        background: rgba(255, 255, 255, 0.95);
        display: flex;
        align-items: center;
        justify-content: center;
        z-index: 100;
    }

    .spinner {
        width: 40px;
        height: 40px;
        border: 4px solid #e2e8f0;
        border-top-color: #667eea;
        border-radius: 50%;
        animation: spin 1s linear infinite;
    }

    @keyframes spin {
        to {
            transform: rotate(360deg);
        }
    }

    /* ===== Badges ===== */
    .badge {
        display: inline-flex;
        align-items: center;
        padding: 0.35rem 0.75rem;
        border-radius: 20px;
        font-size: 0.85rem;
        font-weight: 500;
    }

    .badge-success {
        background: #dcfce7;
        color: #10b981;
        border: 1px solid #bbf7d0;
    }

    .badge-warning {
        background: #fef3c7;
        color: #f59e0b;
        border: 1px solid #fde68a;
    }

    .badge-info {
        background: #dbeafe;
        color: #3b82f6;
        border: 1px solid #bfdbfe;
    }

    .badge-secondary {
        background: #f1f5f9;
        color: #64748b;
        border: 1px solid #e2e8f0;
    }

    /* ===== Scrollbar ===== */
    ::-webkit-scrollbar {
        width: 8px;
        height: 8px;
    }

    ::-webkit-scrollbar-track {
        background: #f8fafc;
        border-radius: 4px;
    }

    ::-webkit-scrollbar-thumb {
        background: #cbd5e1;
        border-radius: 4px;
    }

    ::-webkit-scrollbar-thumb:hover {
        background: #94a3b8;
    }

    /* ===== Responsive ===== */
    @media (max-width: 1024px) {
        .permission-container {
            padding: 1rem;
        }

        .user-info-card {
            position: static;
            margin-bottom: 1.5rem;
        }
    }

    @media (max-width: 768px) {
        .permission-header {
            padding: 1.5rem;
            border-radius: 0.75rem;
        }

        .header-actions {
            margin-top: 1rem;
            display: flex;
            flex-direction: column;
            gap: 0.5rem;
        }

        .header-actions .btn {
            margin-left: 0;
            width: 100%;
        }

        .permission-tabs {
            overflow-x: auto;
        }

        .tab-button {
            white-space: nowrap;
            font-size: 0.9rem;
            padding: 0.75rem 1rem;
        }

        .tab-content-wrapper {
            padding: 1rem;
        }

        .permission-item {
            flex-direction: column;
            align-items: flex-start;
            gap: 1rem;
        }

        .permission-item-actions {
            width: 100%;
            justify-content: space-between;
        }
    }

    /* ===== Action Buttons Group ===== */
    .action-buttons {
        display: flex;
        gap: 0.5rem;
        justify-content: flex-end;
        padding: 1.5rem;
        border-top: 1px solid #e2e8f0;
        background: white;
    }

    /* ===== Search Box ===== */
    .search-box {
        position: relative;
        margin-bottom: 1rem;
    }

    .search-box input {
        width: 100%;
        padding: 0.75rem 1rem 0.75rem 3rem;
        border: 1px solid #e2e8f0;
        border-radius: 0.5rem;
        font-size: 0.95rem;
        background: white;
    }

    .search-box input:focus {
        outline: none;
        border-color: #667eea;
        box-shadow: 0 0 0 3px rgba(102, 126, 234, 0.1);
    }

    .search-box i {
        position: absolute;
        left: 1rem;
        top: 50%;
        transform: translateY(-50%);
        color: #94a3b8;
    }

    /* ===== Tooltip ===== */
    .tooltip-wrapper {
        position: relative;
        display: inline-block;
    }

    .tooltip-content {
        visibility: hidden;
        background-color: #1e293b;
        color: white;
        text-align: center;
        padding: 0.5rem 1rem;
        border-radius: 6px;
        position: absolute;
        z-index: 1000;
        bottom: 125%;
        left: 50%;
        transform: translateX(-50%);
        white-space: nowrap;
        font-size: 0.85rem;
        opacity: 0;
        transition: opacity 0.3s;
    }

    .tooltip-wrapper:hover .tooltip-content {
        visibility: visible;
        opacity: 1;
    }

    /* ===== Utility Classes ===== */
    h5,
    h6 {
        font-weight: 600;
        color: #1e293b;
    }

    .d-flex {
        display: flex;
    }

    .align-items-center {
        align-items: center;
    }

    .justify-content-between {
        justify-content: space-between;
    }

    .mb-0 {
        margin-bottom: 0;
    }

    .mb-1 {
        margin-bottom: 0.25rem;
    }

    .mb-2 {
        margin-bottom: 0.5rem;
    }

    .mb-3 {
        margin-bottom: 0.75rem;
    }

    .mb-4 {
        margin-bottom: 1rem;
    }

    .mt-1 {
        margin-top: 0.25rem;
    }

    .mt-2 {
        margin-top: 0.5rem;
    }

    .mt-4 {
        margin-top: 1rem;
    }

    .ms-2 {
        margin-left: 0.5rem;
    }

    .ms-3 {
        margin-left: 0.75rem;
    }

    .ms-4 {
        margin-left: 1rem;
    }

    .me-1 {
        margin-right: 0.25rem;
    }

    .me-2 {
        margin-right: 0.5rem;
    }

    .me-3 {
        margin-right: 0.75rem;
    }

    .p-3 {
        padding: 0.75rem;
    }

    .text-muted {
        color: #64748b;
    }

    .text-center {
        text-align: center;
    }

    .text-start {
        text-align: left;
    }

    .flex-grow-1 {
        flex-grow: 1;
    }

    .opacity-75 {
        opacity: 0.75;
    }

    .row {
        display: flex;
        flex-wrap: wrap;
        margin: 0 -0.75rem;
    }

    .col-lg-3 {
        flex: 0 0 25%;
        max-width: 25%;
        padding: 0 0.75rem;
    }

    .col-lg-9 {
        flex: 0 0 75%;
        max-width: 75%;
        padding: 0 0.75rem;
    }

    @media (max-width: 1024px) {

        .col-lg-3,
        .col-lg-9 {
            flex: 0 0 100%;
            max-width: 100%;
        }
    }
</style>

<div class="permission-page-wrapper ml-72">
    <div class="permission-container">

        <!-- Header Section -->
        <div class="permission-header">
            <div class="d-flex align-items-center justify-content-between mb-3">
                <div class="d-flex align-items-center">
                    <button onclick="goBack()" class="header-back-btn">
                        <i class="fas fa-arrow-left"></i> ย้อนกลับ
                    </button>
                    <div class="ms-4">
                        <h2 class="mb-1">
                            <i class="fas fa-user-shield me-2"></i>
                            การจัดการสิทธิ์ผู้ใช้
                        </h2>
                        <p class="mb-0 opacity-75">จัดการสิทธิ์การเข้าถึง Google Drive สำหรับผู้ใช้</p>
                    </div>
                </div>

                <div class="header-actions">
                    <button class="btn" onclick="refreshData()">
                        <i class="fas fa-sync-alt"></i> รีเฟรช
                    </button>
                    <button class="btn" onclick="exportReport()">
                        <i class="fas fa-download"></i> ส่งออก
                    </button>
                </div>
            </div>
        </div>

        <div class="row">
            <!-- Left Sidebar - User Info -->
            <div class="col-lg-3">
                <!-- User Info Card -->
                <div class="user-info-card">
                    <!-- Avatar Section -->
                    <div class="user-avatar-section">
                        <div class="user-avatar">
                            <?php echo strtoupper(substr($user_info->m_fname, 0, 1)); ?>
                        </div>
                        <div class="user-name"><?php echo $user_info->m_fname . ' ' . $user_info->m_lname; ?></div>
                        <div class="user-position"><?php echo $user_info->position_name; ?></div>
                        <div class="status-badges">
                            <?php if ($user_info->google_drive_enabled): ?>
                                <span class="status-badge">
                                    <i class="fas fa-check-circle me-1"></i>เปิดใช้งาน
                                </span>
                            <?php else: ?>
                                <span class="status-badge">
                                    <i class="fas fa-times-circle me-1"></i>ปิดใช้งาน
                                </span>
                            <?php endif; ?>

                            <?php if ($user_info->google_account_verified): ?>
                                <span class="status-badge">
                                    <i class="fas fa-cloud me-1"></i>Storage
                                </span>
                            <?php endif; ?>
                        </div>
                    </div>

                    <!-- Contact Info -->
                    <div class="contact-info">
                        <div class="contact-item">
                            <i class="fas fa-envelope"></i>
                            <span><?php echo $user_info->m_email; ?></span>
                        </div>

                        <?php if ($user_info->google_email): ?>
                            <div class="contact-item">
                                <i class="fab fa-google"></i>
                                <span><?php echo $user_info->google_email; ?></span>
                            </div>
                        <?php endif; ?>

                        <div class="contact-item">
                            <i class="fas fa-calendar"></i>
                            <span>
                                <?php echo $user_info->google_connected_at ?
                                    date('d/m/Y', strtotime($user_info->google_connected_at)) :
                                    'ยังไม่เชื่อมต่อ'; ?>
                            </span>
                        </div>
                    </div>
                </div>

                <!-- Statistics Card -->
                <div class="statistics-card">
                    <div class="p-3" style="border-bottom: 1px solid #f0f0f0;">
                        <h6 class="mb-0">
                            <i class="fas fa-chart-bar text-primary me-2"></i>สถิติการใช้งาน
                        </h6>
                    </div>

                    <div class="stat-item">
                        <div class="stat-label">โฟลเดอร์ที่เข้าถึง</div>
                        <div class="stat-value text-primary" id="totalFoldersCount">0</div>
                    </div>

                    <div class="stat-item">
                        <div class="stat-label">สิทธิ์เขียน</div>
                        <div class="stat-value text-success" id="writeFoldersCount">0</div>
                    </div>

                    <div class="stat-item">
                        <div class="stat-label">ไฟล์ทั้งหมด</div>
                        <div class="stat-value text-info" id="totalFilesCount">0</div>
                    </div>

                    <div class="stat-item">
                        <div class="stat-label">การแชร์</div>
                        <div class="stat-value text-warning" id="sharedCount">0</div>
                    </div>

                    <!-- Storage Section -->
                    <div class="storage-section">
                        <div class="d-flex justify-content-between mb-2">
                            <small class="text-muted">การใช้งาน Storage</small>
                            <small class="text-muted"><span id="storagePercent">0</span>%</small>
                        </div>
                        <div class="storage-progress">
                            <div class="progress">
                                <div class="progress-bar" id="storageBar" style="width: 0%"></div>
                            </div>
                        </div>
                        <div class="text-center mt-2">
                            <small class="text-muted">
                                <span id="storageUsed">0 B</span> / <span id="storageTotal">1 GB</span>
                            </small>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Main Content Area -->
            <div class="col-lg-9">
                <div class="main-content-card">
                    <!-- Tabs Navigation -->
                    <div class="permission-tabs">
                        <button class="tab-button active" onclick="switchTab('folders')">
                            <i class="fas fa-folder"></i>
                            <span>โฟลเดอร์</span>
                        </button>
                        <button class="tab-button" onclick="switchTab('system')">
                            <i class="fas fa-cog"></i>
                            <span>ระบบ</span>
                        </button>
                        <button class="tab-button" onclick="switchTab('history')">
                            <i class="fas fa-history"></i>
                            <span>ประวัติ</span>
                        </button>
                        <button class="tab-button" onclick="switchTab('settings')">
                            <i class="fas fa-sliders-h"></i>
                            <span>ตั้งค่า</span>
                        </button>
                    </div>

                    <!-- Tab Content -->
                    <div class="tab-content-wrapper">

                        <!-- Folders Tab -->
                        <div class="tab-pane active" id="folders-tab">
                            <div class="d-flex justify-content-between align-items-center mb-4">
                                <h5 class="mb-0">
                                    <i class="fas fa-folder-open text-warning me-2"></i>
                                    สิทธิ์การเข้าถึงโฟลเดอร์
                                </h5>
                                <button class="btn btn-primary btn-sm" onclick="showAddFolderDialog()">
                                    <i class="fas fa-plus"></i> เพิ่มสิทธิ์
                                </button>
                            </div>

                            <!-- Search Box -->
                            <div class="search-box">
                                <i class="fas fa-search"></i>
                                <input type="text" id="folderSearchInput" placeholder="ค้นหาโฟลเดอร์..."
                                    onkeyup="searchFolders()">
                            </div>

                            <!-- Folder Tree -->
                            <div class="folder-tree-container" id="folderTreeContainer">
                                <div class="empty-state">
                                    <i class="fas fa-folder-open"></i>
                                    <p>กำลังโหลดข้อมูลโฟลเดอร์...</p>
                                </div>
                            </div>

                            <hr class="my-4">

                            <!-- Current Permissions -->
                            <h6 class="mb-3">
                                <i class="fas fa-key text-primary me-2"></i>
                                สิทธิ์ปัจจุบัน
                            </h6>
                            <div class="current-permissions-list" id="currentPermissionsList">
                                <div class="empty-state">
                                    <i class="fas fa-shield-alt"></i>
                                    <p>ยังไม่มีสิทธิ์ที่กำหนด</p>
                                </div>
                            </div>
                        </div>

                        <!-- System Permissions Tab -->
                        <div class="tab-pane" id="system-tab">
                            <h5 class="mb-4">
                                <i class="fas fa-cog text-primary me-2"></i>
                                การตั้งค่าสิทธิ์ระบบ
                            </h5>

                            <!-- Storage Access -->
                            <div class="permission-card">
                                <div class="permission-card-header">
                                    <div class="permission-card-title">
                                        <i class="fas fa-database text-primary"></i>
                                        การเข้าถึง Storage
                                    </div>
                                    <label class="permission-switch">
                                        <input type="checkbox" id="storageAccessToggle"
                                            onchange="toggleSystemPermission('storage_access', this.checked)" disabled>
                                        <span class="permission-slider disabled"></span>
                                    </label>
                                </div>
                                <p class="text-muted mb-0">อนุญาตให้ผู้ใช้เข้าถึง Centralized Storage
                                    (ไม่สามารถปรับเปลี่ยนได้)</p>
                            </div>

                            <!-- Can Create Folder -->
                            <div class="permission-card">
                                <div class="permission-card-header">
                                    <div class="permission-card-title">
                                        <i class="fas fa-folder-plus text-success"></i>
                                        สร้างโฟลเดอร์
                                    </div>
                                    <label class="permission-switch">
                                        <input type="checkbox" id="createFolderToggle"
                                            onchange="toggleSystemPermission('can_create_folder', this.checked)"
                                            disabled>
                                        <span class="permission-slider disabled"></span>
                                    </label>
                                </div>
                                <p class="text-muted mb-0">อนุญาตให้สร้างโฟลเดอร์ใหม่ (ไม่สามารถปรับเปลี่ยนได้)</p>
                            </div>

                            <!-- Can Share Files -->
                            <div class="permission-card">
                                <div class="permission-card-header">
                                    <div class="permission-card-title">
                                        <i class="fas fa-share-alt text-info"></i>
                                        แชร์ไฟล์
                                    </div>
                                    <label class="permission-switch">
                                        <input type="checkbox" id="shareFileToggle"
                                            onchange="toggleSystemPermission('can_share', this.checked)">
                                        <span class="permission-slider"></span>
                                    </label>
                                </div>
                                <p class="text-muted mb-0">อนุญาตให้แชร์ไฟล์กับผู้อื่น</p>
                            </div>

                            <!-- Can Delete Files -->
                            <div class="permission-card">
                                <div class="permission-card-header">
                                    <div class="permission-card-title">
                                        <i class="fas fa-trash-alt text-danger"></i>
                                        ลบไฟล์
                                    </div>
                                    <label class="permission-switch">
                                        <input type="checkbox" id="deleteFileToggle"
                                            onchange="toggleSystemPermission('can_delete', this.checked)" disabled>
                                        <span class="permission-slider disabled"></span>
                                    </label>
                                </div>
                                <p class="text-muted mb-0">อนุญาตให้ลบไฟล์และโฟลเดอร์ (ไม่สามารถปรับเปลี่ยนได้)</p>
                            </div>

                            <!-- Storage Quota -->
                            <div class="permission-card">
                                <div class="permission-card-header">
                                    <div class="permission-card-title">
                                        <i class="fas fa-hdd text-warning"></i>
                                        Storage Quota
                                    </div>
                                </div>
                                <div class="form-group mb-0">
                                    <select class="permission-select" id="storageQuotaSelect"
                                        onchange="updateStorageQuota()">
                                        <option value="536870912">512 MB</option>
                                        <option value="1073741824" selected>1 GB</option>
                                        <option value="2147483648">2 GB</option>
                                        <option value="5368709120">5 GB</option>
                                        <option value="10737418240">10 GB</option>
                                        <option value="custom">กำหนดเอง...</option>
                                    </select>
                                </div>
                            </div>

                            <!-- Inherit Position Permissions -->
                            <div class="permission-card">
                                <div class="permission-card-header">
                                    <div class="permission-card-title">
                                        <i class="fas fa-users text-purple"></i>
                                        รับสิทธิ์จากตำแหน่ง
                                    </div>
                                    <label class="permission-switch">
                                        <input type="checkbox" id="inheritPositionToggle"
                                            onchange="toggleSystemPermission('inherit_position', this.checked)"
                                            disabled>
                                        <span class="permission-slider disabled"></span>
                                    </label>
                                </div>
                                <p class="text-muted mb-0">รับสิทธิ์อัตโนมัติตามตำแหน่งงาน (ยังไม่เปิดให้ใช้งาน)</p>
                            </div>

                            <!-- Override Position Permissions -->
                            <div class="permission-card">
                                <div class="permission-card-header">
                                    <div class="permission-card-title">
                                        <i class="fas fa-user-shield text-danger"></i>
                                        แทนที่สิทธิ์ตำแหน่ง
                                    </div>
                                    <label class="permission-switch">
                                        <input type="checkbox" id="overridePositionToggle"
                                            onchange="toggleSystemPermission('override_position', this.checked)"
                                            disabled>
                                        <span class="permission-slider disabled"></span>
                                    </label>
                                </div>
                                <p class="text-muted mb-0">ใช้สิทธิ์ส่วนตัวแทนสิทธิ์จากตำแหน่ง (ยังไม่เปิดให้ใช้งาน)</p>
                            </div>

                            <!-- Save Button -->
                            <div class="action-buttons">
                                <button class="btn btn-light" onclick="resetSystemPermissions()">
                                    <i class="fas fa-undo"></i> รีเซ็ต
                                </button>
                                <button class="btn btn-primary" onclick="saveSystemPermissions()">
                                    <i class="fas fa-save"></i> บันทึกการตั้งค่า
                                </button>
                            </div>
                        </div>

                        <!-- History Tab -->
                        <div class="tab-pane" id="history-tab">
                            <h5 class="mb-4">
                                <i class="fas fa-history text-primary me-2"></i>
                                ประวัติการเปลี่ยนแปลงสิทธิ์
                            </h5>

                            <div class="permission-history-list" id="permissionHistoryList">
                                <div class="empty-state">
                                    <i class="fas fa-clock"></i>
                                    <p>ไม่มีประวัติการเปลี่ยนแปลง</p>
                                </div>
                            </div>
                        </div>


                        <!-- Settings Tab -->
                        <div class="tab-pane" id="settings-tab">
                            <h5 class="mb-4">
                                <i class="fas fa-sliders-h text-primary me-2"></i>
                                การตั้งค่าเพิ่มเติม
                            </h5>

                            <!-- Permission Notes -->
                            <div class="form-group">
                                <label class="form-label">
                                    <i class="fas fa-sticky-note me-2"></i>หมายเหตุเกี่ยวกับสิทธิ์
                                </label>
                                <textarea class="form-control" id="permissionNotes" rows="4"
                                    placeholder="เพิ่มหมายเหตุหรือคำอธิบายเกี่ยวกับการให้สิทธิ์..."></textarea>
                                <small class="form-text text-muted">
                                    หมายเหตุนี้จะถูกบันทึกไว้ใน tbl_google_drive_member_permissions
                                </small>
                            </div>

                            <!-- Auto Sync - DISABLED -->
                            <div class="permission-card" style="opacity: 0.6; background-color: #f8f9fa;">
                                <div class="permission-card-header">
                                    <div class="permission-card-title">
                                        <i class="fas fa-sync-alt text-secondary"></i>
                                        ซิงค์อัตโนมัติ
                                        <span class="badge bg-secondary ms-2">ระบบซิงค์อัตโนมัติอยู่แล้ว</span>
                                    </div>
                                    <label class="permission-switch">
                                        <input type="checkbox" id="autoSyncToggle" disabled>
                                        <span class="permission-slider" style="opacity: 0.5;"></span>
                                    </label>
                                </div>
                                <p class="text-muted mb-0">
                                    <i class="fas fa-info-circle me-1"></i>
                                    ระบบทำการซิงค์สิทธิ์กับ Google Drive อัตโนมัติอยู่แล้ว ไม่จำเป็นต้องเปิดใช้งาน
                                </p>
                            </div>

                            <!-- Notification - DISABLED -->
                            <div class="permission-card" style="opacity: 0.6; background-color: #f8f9fa;">
                                <div class="permission-card-header">
                                    <div class="permission-card-title">
                                        <i class="fas fa-bell text-secondary"></i>
                                        แจ้งเตือนการเปลี่ยนแปลง
                                        <span class="badge bg-warning text-dark ms-2">ยังไม่เปิดใช้งาน</span>
                                    </div>
                                    <label class="permission-switch">
                                        <input type="checkbox" id="notificationToggle" disabled>
                                        <span class="permission-slider" style="opacity: 0.5;"></span>
                                    </label>
                                </div>
                                <p class="text-muted mb-0">
                                    <i class="fas fa-info-circle me-1"></i>
                                    ฟีเจอร์การแจ้งเตือนจะเปิดให้ใช้งานในอนาคต
                                </p>
                            </div>

                            <hr class="my-4">

                            <!-- Danger Zone -->
                            <div class="permission-card" style="border-color: #fee2e2; background: #fef2f2;">
                                <h6 class="text-danger mb-3">
                                    <i class="fas fa-exclamation-triangle me-2"></i>
                                    โซนอันตราย
                                </h6>
                                <p class="text-muted mb-3">การดำเนินการเหล่านี้ไม่สามารถย้อนกลับได้</p>

                                <div class="d-flex gap-2 flex-wrap">
                                    <button class="btn btn-danger btn-sm" onclick="resetAllPermissions()">
                                        <i class="fas fa-redo"></i> รีเซ็ตสิทธิ์ทั้งหมด
                                    </button>
                                    <button class="btn btn-danger btn-sm" onclick="removeUserAccess()">
                                        <i class="fas fa-user-times"></i> ยกเลิกการเข้าถึงถาวร
                                    </button>
                                </div>

                                <div class="alert alert-warning mt-3 mb-0" role="alert">
                                    <small>
                                        <strong>คำเตือน:</strong>
                                        <ul class="mb-0 ps-3">
                                            <li><strong>รีเซ็ตสิทธิ์:</strong>
                                                จะคืนค่าสิทธิ์ใช้งานโฟล์เดอร์ทั้งหมดสู่ค่าเริ่มต้น
                                                (ไม่สามารถย้อนกลับได้)</li>
                                            <li><strong>ยกเลิกการเข้าถึง:</strong> จะลบข้อมูลออกจากฐานข้อมูลถาวร
                                                (ไม่สามารถย้อนกลับได้)</li>
                                        </ul>
                                    </small>
                                </div>
                            </div>

                            <!-- Save Button -->
                            <div class="action-buttons mt-4">
                                <button class="btn btn-light" onclick="cancelSettings()">
                                    <i class="fas fa-times"></i> ยกเลิก
                                </button>
                                <button class="btn btn-primary" onclick="saveSettings()">
                                    <i class="fas fa-save"></i> บันทึกการตั้งค่า
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
    // ===== Global Variables =====
    const userId = <?php echo $user_info->m_id; ?>;
    let allFolders = [];
    let currentPermissions = [];
    let permissionHistory = [];
    let systemPermissions = {};
    let hasUnsavedChanges = false;

    function refreshPermissionHistory() {
        if (userId) {  // ✅ ใช้ userId แทน currentUserId
            console.log('🔄 Refreshing permission history...');
            loadPermissionHistory();
        }
    }

    // ===== Initialize =====
    document.addEventListener('DOMContentLoaded', function () {
        loadUserPermissionData();
        setupEventListeners();
    });

    function setupEventListeners() {
        // Prevent accidental navigation when there are unsaved changes
        window.addEventListener('beforeunload', function (e) {
            if (hasUnsavedChanges) {
                e.preventDefault();
                e.returnValue = '';
            }
        });
    }

    // ===== Navigation Functions =====
    function goBack() {
        if (hasUnsavedChanges) {
            Swal.fire({
                title: 'มีการเปลี่ยนแปลงที่ยังไม่ได้บันทึก',
                text: 'คุณต้องการออกโดยไม่บันทึกหรือไม่?',
                icon: 'warning',
                showCancelButton: true,
                confirmButtonText: 'ออกโดยไม่บันทึก',
                cancelButtonText: 'ยกเลิก',
                confirmButtonColor: '#ef4444'
            }).then((result) => {
                if (result.isConfirmed) {
                    window.location.href = '<?php echo site_url('google_drive_system/setup'); ?>';
                }
            });
        } else {
            window.location.href = '<?php echo site_url('google_drive_system/setup'); ?>';
        }
    }

    function refreshData() {
        Swal.fire({
            title: 'กำลังโหลดข้อมูล...',
            allowOutsideClick: false,
            showConfirmButton: false,
            didOpen: () => {
                Swal.showLoading();
            }
        });

        loadUserPermissionData();
    }

    function exportReport() {
        window.open('<?php echo site_url('google_drive_system/export_user_report/' . $user_info->m_id); ?>', '_blank');
    }

    // ===== Tab Management =====
    function switchTab(tabName) {
        // Update tab buttons
        document.querySelectorAll('.tab-button').forEach(btn => {
            btn.classList.remove('active');
        });
        event.target.closest('.tab-button').classList.add('active');

        // Update tab panes
        document.querySelectorAll('.tab-pane').forEach(pane => {
            pane.classList.remove('active');
        });
        document.getElementById(tabName + '-tab').classList.add('active');

        // Load tab-specific data if needed
        if (tabName === 'history') {
            console.log('📍 History tab clicked - loading from API');
            loadPermissionHistory();
        }
    }

    // ===== Data Loading Functions =====

    /**
     * ✅ โหลดข้อมูลสิทธิ์ของผู้ใช้ (ปรับปรุงให้กรองข้อมูล folder_id ที่ไม่ถูกต้อง)
     */
    function loadUserPermissionData() {
        fetch('<?php echo site_url('google_drive_system/get_user_permission_data'); ?>', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/x-www-form-urlencoded',
                'X-Requested-With': 'XMLHttpRequest'
            },
            body: `user_id=${userId}`
        })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    // ✅ 1. กรองโฟลเดอร์ที่ถูกต้อง
                    const rawFolders = data.data.available_folders || [];
                    allFolders = filterValidFolders(rawFolders);

                    // ✅ 2. กรอง permissions ที่ถูกต้อง
                    const rawPermissions = data.data.current_permissions || [];
                    currentPermissions = rawPermissions.filter(p => {
                        // ต้องมี folder_id และเป็น Google Drive ID
                        return p.folder_id && isValidGoogleDriveFolderId(p.folder_id);
                    });

                    // ✅ 3. Log เพื่อ debug
                    console.log('📊 Filtered Permissions:', currentPermissions.length);
                    console.log('📊 Valid Folders:', allFolders.length);

                    // ✅ ❌ ลบบรรทัดนี้ออกเพราะมัน overwrite ค่าที่กรองแล้ว
                    // currentPermissions = data.data.current_permissions || [];

                    // Store other data
                    systemPermissions = data.data.system_permissions || {};

                    // Populate UI
                    populateFolderTree(allFolders);
                    populateCurrentPermissions(currentPermissions);
                    populateSystemPermissions(systemPermissions);
                    updateStatistics(data.data.statistics);

                    Swal.close();
                } else {
                    throw new Error(data.message || 'ไม่สามารถโหลดข้อมูลได้');
                }
            })
            .catch(error => {
                console.error('Load data error:', error);
                Swal.fire({
                    icon: 'error',
                    title: 'เกิดข้อผิดพลาด',
                    text: error.message
                });
            });
    }

    // ===== Folder Tree Functions =====
    function populateFolderTree(folders) {
        const container = document.getElementById('folderTreeContainer');

        if (!folders || folders.length === 0) {
            container.innerHTML = `
                <div class="empty-state">
                    <i class="fas fa-folder-open"></i>
                    <p>ไม่มีโฟลเดอร์ที่สามารถเพิ่มได้</p>
                </div>
            `;
            return;
        }

        // Build hierarchy
        const hierarchy = buildFolderHierarchy(folders);
        const html = renderFolderTree(hierarchy);
        container.innerHTML = html;
    }


    function buildFolderHierarchy(folders) {
        const map = {};
        const roots = [];

        // ✅ ใช้ folder_id (Google Drive ID) แทน id (Auto-increment)
        folders.forEach(folder => {
            const key = folder.folder_id || folder.id; // fallback ถ้าไม่มี folder_id
            map[key] = { ...folder, children: [] };
        });

        // Build hierarchy
        folders.forEach(folder => {
            const key = folder.folder_id || folder.id;
            const parentKey = folder.parent_folder_id; // ใช้ parent_folder_id (Google Drive ID)

            if (parentKey && map[parentKey]) {
                map[parentKey].children.push(map[key]);
            } else {
                roots.push(map[key]);
            }
        });

        return roots;
    }

    function renderFolderTree(nodes, level = 0) {
        let html = '';

        nodes.forEach(node => {
            const hasChildren = node.children && node.children.length > 0;

            // ✅ ใช้ node.folder_id แทน node.id สำหรับเปรียบเทียบ
            const folderId = node.folder_id || node.id;
            const isGranted = currentPermissions.some(p => p.folder_id == folderId);
            const indent = level * 2;

            // ✅ Debug log
            if (isGranted) {
                console.log(`✅ Folder "${node.folder_name}" has permission (${folderId})`);
            }

            html += `
            <div class="folder-tree-item ${isGranted ? 'selected' : ''}" 
                 style="padding-left: ${indent}rem;"
                 onclick="selectFolder('${escapeHtml(folderId)}', '${escapeHtml(node.folder_name)}')">
                ${hasChildren ?
                    `<button class="folder-expand-btn" onclick="event.stopPropagation(); toggleFolderExpand(this)">
                        <i class="fas fa-chevron-right"></i>
                    </button>` :
                    '<span style="width: 24px; display: inline-block;"></span>'
                }
                <i class="fas fa-folder folder-icon ${node.folder_type}"></i>
                <span class="flex-grow-1">${escapeHtml(node.folder_name)}</span>
                ${isGranted ? '<i class="fas fa-check-circle text-success"></i>' : ''}
            </div>
            ${hasChildren ? `<div class="folder-children" style="display: none;">${renderFolderTree(node.children, level + 1)}</div>` : ''}
        `;
        });

        return html;
    }

    function toggleFolderExpand(button) {
        const item = button.closest('.folder-tree-item');
        const children = item.nextElementSibling;

        if (children && children.classList.contains('folder-children')) {
            const isExpanded = children.style.display !== 'none';

            children.style.display = isExpanded ? 'none' : 'block';
            button.classList.toggle('expanded');
        }
    }

    function selectFolder(folderId, folderName) {
        // ✅ Debug log
        console.log('🔍 selectFolder called:', { folderId, folderName });
        console.log('📋 Current Permissions:', currentPermissions);

        const isGranted = currentPermissions.some(p => p.folder_id == folderId);

        if (isGranted) {
            Swal.fire({
                title: 'ผู้ใช้มีสิทธิ์อยู่แล้ว',
                text: `ผู้ใช้มีสิทธิ์เข้าถึง "${folderName}" อยู่แล้ว`,
                icon: 'info'
            });
        } else {
            showAddFolderPermissionDialog(folderId, folderName);
        }
    }

    function searchFolders() {
        const searchTerm = document.getElementById('folderSearchInput').value.toLowerCase();
        const items = document.querySelectorAll('.folder-tree-item');

        items.forEach(item => {
            const text = item.textContent.toLowerCase();
            const match = text.includes(searchTerm);

            item.style.display = match ? 'flex' : 'none';

            // Show parent if child matches
            if (match) {
                let parent = item.parentElement;
                while (parent) {
                    if (parent.classList.contains('folder-children')) {
                        parent.style.display = 'block';
                        const expandBtn = parent.previousElementSibling?.querySelector('.folder-expand-btn');
                        if (expandBtn) {
                            expandBtn.classList.add('expanded');
                        }
                    }
                    parent = parent.parentElement;
                }
            }
        });
    }

    // ===== Current Permissions Functions =====
    function populateCurrentPermissions(permissions) {
        const container = document.getElementById('currentPermissionsList');

        if (!permissions || permissions.length === 0) {
            container.innerHTML = `
            <div class="empty-state">
                <i class="fas fa-shield-alt"></i>
                <p>ยังไม่มีสิทธิ์ที่กำหนด</p>
            </div>
        `;
            return;
        }

        let html = '';
        permissions.forEach(permission => {
            // ✅ [FIX 1] ใช้ permission_type แทน access_level
            const isOwner = permission.permission_type === 'owner';

            html += `
            <div class="permission-item ${isOwner ? 'owner-permission' : ''}">
                <div class="permission-item-info">
                    <div class="permission-item-folder">
                        <i class="fas fa-folder folder-icon ${permission.folder_type} me-2"></i>
                        ${escapeHtml(permission.folder_name)}
                        ${isOwner ? '<span class="badge badge-warning ms-2"><i class="fas fa-crown"></i> เจ้าของ</span>' : ''}
                    </div>
                    <div class="permission-item-meta">
                        <!-- ✅ [FIX 2] ใช้ permission_type แทน access_level -->
                        <span class="badge badge-${getAccessLevelBadge(permission.permission_type)}">
                            ${getAccessLevelText(permission.permission_type)}
                        </span>
                        ${permission.granted_by ? `
                            <span class="ms-2">
                                <!-- ✅ [FIX 3] ใช้ granted_by_fname และ granted_by_lname -->
                                <i class="fas fa-user me-1"></i>${escapeHtml(permission.granted_by_fname)} ${escapeHtml(permission.granted_by_lname)}
                            </span>
                        ` : ''}
                        ${permission.permission_granted_at ? `
                            <span class="ms-2">
                                <!-- ✅ [FIX 4] ใช้ permission_granted_at แทน granted_at -->
                                <i class="fas fa-clock me-1"></i>${formatDate(permission.permission_granted_at)}
                            </span>
                        ` : ''}
                    </div>
                </div>
                <div class="permission-item-actions">
                    ${isOwner ? `
                        <!-- ✅ [FIX 5] แสดง Badge แทน Dropdown สำหรับ Owner -->
                        <div class="owner-badge-container">
                            <small class="text-muted d-block mt-1">
                                <i class="fas fa-info-circle"></i> ไม่สามารถเปลี่ยนแปลงได้
                            </small>
                        </div>
                    ` : `
                        <!-- ✅ [FIX 6] Dropdown ปกติสำหรับ Non-Owner -->
                        <select class="permission-select" style="width: 150px;" 
                                onchange="updateFolderPermission('${permission.permission_id}', this.value)">
                            <option value="read" ${permission.permission_type === 'read' ? 'selected' : ''}>อ่านอย่างเดียว</option>
                            <option value="write" ${permission.permission_type === 'write' ? 'selected' : ''}>อ่าน-เขียน</option>
                            <option value="admin" ${permission.permission_type === 'admin' ? 'selected' : ''}>ผู้ดูแล</option>
                        </select>
                        
                        <!-- ✅ [FIX 7] ส่ง permission_id, folder_name และ member_name ที่ถูกต้อง -->
                        <button class="btn btn-danger btn-sm btn-icon" 
                                onclick="removeFolderPermission(
                                    '${permission.permission_id}',
                                    '${escapeHtml(permission.folder_name)}',
                                    '${escapeHtml(permission.member_fname)} ${escapeHtml(permission.member_lname)}'
                                )">
                            <i class="fas fa-trash-alt"></i>
                        </button>
                    `}
                </div>
            </div>
        `;
        });

        container.innerHTML = html;
    }

    // ===== Helper Functions =====

    function getAccessLevelBadge(permissionType) {
        const badges = {
            'owner': 'warning',
            'admin': 'danger',
            'write': 'primary',
            'read': 'secondary',
            'editor': 'primary',  // alias for write
            'viewer': 'secondary' // alias for read
        };
        return badges[permissionType] || 'secondary';
    }

    function getAccessLevelText(permissionType) {
        const texts = {
            'owner': 'เจ้าของ',
            'admin': 'ผู้ดูแล',
            'write': 'อ่าน-เขียน',
            'read': 'อ่านอย่างเดียว',
            'editor': 'อ่าน-เขียน',  // alias
            'viewer': 'อ่านอย่างเดียว' // alias
        };
        return texts[permissionType] || permissionType;
    }

    // ===== Helper Functions =====

    /**
     * ✅ ตรวจสอบความถูกต้องของ Google Drive Folder ID
     * @param {string} folderId - Folder ID ที่ต้องการตรวจสอบ
     * @returns {boolean} - true ถ้าถูกต้อง, false ถ้าไม่ถูกต้อง
     */
    function isValidGoogleDriveFolderId(folderId) {
        if (!folderId || typeof folderId !== 'string') {
            return false;
        }

        // Google Drive Folder ID มีความยาวมากกว่า 20 ตัวอักษร
        if (folderId.length < 20) {
            return false;
        }

        // ประกอบด้วย a-z, A-Z, 0-9, _, - เท่านั้น
        const pattern = /^[a-zA-Z0-9_-]+$/;
        return pattern.test(folderId);
    }

    /**
     * ✅ กรองโฟลเดอร์ที่มี folder_id ถูกต้อง
     * @param {Array} folders - รายการโฟลเดอร์
     * @returns {Array} - รายการโฟลเดอร์ที่ผ่านการตรวจสอบ
     */
    function filterValidFolders(folders) {
        if (!Array.isArray(folders)) {
            return [];
        }

        return folders.filter(folder => {
            if (!folder || !folder.folder_id) {
                return false;
            }
            return isValidGoogleDriveFolderId(folder.folder_id);
        });
    }

    function escapeHtml(text) {
        if (!text) return '';
        const map = {
            '&': '&amp;',
            '<': '&lt;',
            '>': '&gt;',
            '"': '&quot;',
            "'": '&#039;'
        };
        return text.toString().replace(/[&<>"']/g, m => map[m]);
    }

    function formatDate(dateString) {
        if (!dateString) return '';

        try {
            const date = new Date(dateString);
            if (isNaN(date.getTime())) return dateString;

            const options = {
                year: 'numeric',
                month: 'short',
                day: 'numeric',
                hour: '2-digit',
                minute: '2-digit'
            };

            return date.toLocaleDateString('th-TH', options);
        } catch (e) {
            return dateString;
        }
    }


    // ===== System Permissions Functions =====
    function populateSystemPermissions(permissions) {
        if (!permissions) return;

        // Update toggles
        document.getElementById('storageAccessToggle').checked = permissions.storage_access_granted == 1;
        document.getElementById('createFolderToggle').checked = permissions.can_create_folder == 1;
        document.getElementById('shareFileToggle').checked = permissions.can_share == 1;
        document.getElementById('deleteFileToggle').checked = permissions.can_delete == 1;
        document.getElementById('inheritPositionToggle').checked = permissions.inherit_position == 1;
        document.getElementById('overridePositionToggle').checked = permissions.override_position == 1;

        // Update quota
        if (permissions.storage_quota_limit) {
            document.getElementById('storageQuotaSelect').value = permissions.storage_quota_limit;
        }

        // Update notes
        if (permissions.notes) {
            document.getElementById('permissionNotes').value = permissions.notes;
        }
    }

    function toggleSystemPermission(permissionKey, isEnabled) {
        hasUnsavedChanges = true;
        systemPermissions[permissionKey] = isEnabled ? 1 : 0;

        showToast(
            `${isEnabled ? 'เปิด' : 'ปิด'}การตั้งค่าแล้ว`,
            'info'
        );
    }

    function updateStorageQuota() {
        const select = document.getElementById('storageQuotaSelect');
        const value = select.value;

        if (value === 'custom') {
            Swal.fire({
                title: 'กำหนด Storage Quota',
                input: 'number',
                inputLabel: 'ขนาด (MB)',
                inputPlaceholder: 'ใส่ขนาดเป็น MB',
                showCancelButton: true,
                inputValidator: (value) => {
                    if (!value || value <= 0) {
                        return 'กรุณาใส่ค่าที่ถูกต้อง';
                    }
                }
            }).then((result) => {
                if (result.isConfirmed) {
                    const bytes = result.value * 1024 * 1024;
                    systemPermissions.storage_quota_limit = bytes;
                    hasUnsavedChanges = true;
                    showToast('อัปเดต Storage Quota แล้ว', 'success');
                } else {
                    select.value = systemPermissions.storage_quota_limit || '1073741824';
                }
            });
        } else {
            systemPermissions.storage_quota_limit = value;
            hasUnsavedChanges = true;
        }
    }

    function saveSystemPermissions() {
        Swal.fire({
            title: 'กำลังบันทึก...',
            allowOutsideClick: false,
            showConfirmButton: false,
            didOpen: () => {
                Swal.showLoading();
            }
        });

        // Get notes
        systemPermissions.notes = document.getElementById('permissionNotes').value;

        fetch('<?php echo site_url('google_drive_system/save_system_permissions'); ?>', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-Requested-With': 'XMLHttpRequest'
            },
            body: JSON.stringify({
                user_id: userId,
                permissions: systemPermissions
            })
        })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    hasUnsavedChanges = false;
                    Swal.fire({
                        icon: 'success',
                        title: 'บันทึกสำเร็จ',
                        text: 'บันทึกการตั้งค่าสิทธิ์ระบบเรียบร้อยแล้ว',
                        timer: 2000,
                        showConfirmButton: false
                    });
                } else {
                    throw new Error(data.message || 'ไม่สามารถบันทึกได้');
                }
            })
            .catch(error => {
                console.error('Save error:', error);
                Swal.fire({
                    icon: 'error',
                    title: 'เกิดข้อผิดพลาด',
                    text: error.message
                });
            });
    }

    function resetSystemPermissions() {
        Swal.fire({
            title: 'รีเซ็ตการตั้งค่า?',
            text: 'การตั้งค่าจะกลับเป็นค่าเดิม',
            icon: 'warning',
            showCancelButton: true,
            confirmButtonText: 'รีเซ็ต',
            cancelButtonText: 'ยกเลิก'
        }).then((result) => {
            if (result.isConfirmed) {
                populateSystemPermissions(systemPermissions);
                hasUnsavedChanges = false;
                showToast('รีเซ็ตการตั้งค่าแล้ว', 'info');
            }
        });
    }

    // ============================================
    // ===== Permission History Functions =====
    // ============================================

    /**
     * ✅ โหลดประวัติการเปลี่ยนแปลง Permission
     */
    /**
     * ✅ แก้ไข loadPermissionHistory() - แก้ Syntax Error
     */
    function loadPermissionHistory() {
        console.log('🔵 Loading permission history for user:', userId);

        const container = document.getElementById('permissionHistoryList');

        // แสดง Loading State
        container.innerHTML = `
        <div class="empty-state">
            <div class="spinner"></div>
            <p>กำลังโหลดประวัติ...</p>
        </div>
    `;

        // ✅ ใช้ URL ที่ถูกต้อง
        const apiUrl = "<?php echo site_url('google_drive_system/get_member_permission_history'); ?>";
        console.log('📡 API URL:', apiUrl);

        // เรียก API
        fetch(apiUrl, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/x-www-form-urlencoded',
                'X-Requested-With': 'XMLHttpRequest'
            },
            body: `member_id=${encodeURIComponent(userId)}&limit=20&offset=0`
        })
            .then(response => {
                console.log('📡 Response status:', response.status);
                console.log('📡 Response URL:', response.url);
                console.log('📡 Redirected:', response.redirected);

                if (!response.ok) {
                    return response.text().then(text => {
                        console.error('📡 Error response:', text.substring(0, 500));
                        throw new Error(`HTTP ${response.status}: ${response.statusText}`);
                    });
                }

                return response.json();
            })
            .then(data => {
                console.log('📦 Full response data:', data);

                if (data.success) {
                    console.log('✅ Success - History count:', data.data.history.length);
                    populatePermissionHistory(data.data.history || []);
                } else {
                    throw new Error(data.message || 'ไม่สามารถโหลดประวัติได้');
                }
            })
            .catch(error => {
                console.error('❌ Error:', error);

                container.innerHTML = `
            <div class="empty-state">
                <i class="fas fa-exclamation-triangle" style="color: #ef4444;"></i>
                <p style="color: #ef4444; margin-top: 1rem;">ไม่สามารถโหลดประวัติได้</p>
                <p style="color: #64748b; font-size: 0.85rem; margin-top: 0.5rem;">${escapeHtml(error.message)}</p>
                <button onclick="loadPermissionHistory()" class="btn btn-primary btn-sm" style="margin-top: 1rem;">
                    <i class="fas fa-sync-alt"></i> ลองอีกครั้ง
                </button>
            </div>
        `;
            });
    }


    /**
     * ✅ แสดงข้อมูลประวัติบนหน้าจอ
     */
    function populatePermissionHistory(history) {
        console.log('📝 Populating history. Items:', history.length);

        const container = document.getElementById('permissionHistoryList');

        // ตรวจสอบข้อมูล
        if (!history || history.length === 0) {
            container.innerHTML = `
            <div class="empty-state">
                <i class="fas fa-history"></i>
                <p>ไม่มีประวัติการเปลี่ยนแปลง</p>
            </div>
        `;
            return;
        }

        let html = '';

        history.forEach((item, index) => {
            console.log(`  Item ${index}:`, {
                action_type: item.action_type,
                description: item.action_description,
                by: item.by_user_name
            });

            const actionType = item.action_type || '';

            // ✅ ใช้ฟังก์ชัน Helper ที่สร้างไว้
            const iconClass = getHistoryIcon(actionType);
            const typeClass = getHistoryType(actionType);

            html += `
            <div class="history-item">
                <div class="history-icon ${typeClass}">
                    <i class="fas ${iconClass}"></i>
                </div>
                <div class="history-content">
                    <div class="history-action">
                        ${escapeHtml(item.action_description || 'ไม่มีรายละเอียด')}
                    </div>
                    <div class="history-meta">
                        <span>
                            <i class="fas fa-user me-1"></i>
                            ${escapeHtml(item.by_user_name || 'ระบบ')}
                        </span>
                        <span class="ms-2">
                            <i class="fas fa-clock me-1"></i>
                            ${formatDate(item.created_at)}
                        </span>
                        ${item.ip_address ? `
                            <span class="ms-2">
                                <i class="fas fa-map-marker-alt me-1"></i>
                                ${item.ip_address}
                            </span>
                        ` : ''}
                    </div>
                </div>
            </div>
        `;
        });

        container.innerHTML = html;
        console.log('✅ History populated successfully');
    }

    // ============================================
    // ===== History Helper Functions =====
    // ============================================

    /**
     * ✅ ดึงไอคอนตาม action type
     */
    function getHistoryIcon(actionType) {
        switch (actionType) {
            case 'grant_permission':
                return 'fa-plus-circle';
            case 'revoke_permission':
            case 'remove_folder_permission':
                return 'fa-minus-circle';
            case 'update_permission':
            case 'update_folder_permission':
                return 'fa-edit';
            case 'create_folder':
                return 'fa-folder-plus';
            case 'delete_folder':
                return 'fa-folder-minus';
            default:
                return 'fa-circle';
        }
    }

    /**
     * ✅ ดึง CSS class ตาม action type (สำหรับ history-icon)
     */
    function getHistoryType(actionType) {
        switch (actionType) {
            case 'grant_permission':
            case 'create_folder':
                return 'added';
            case 'revoke_permission':
            case 'remove_folder_permission':
            case 'delete_folder':
                return 'removed';
            case 'update_permission':
            case 'update_folder_permission':
                return 'updated';
            default:
                return 'updated';
        }
    }


    // ===== Folder Permission Management (FIXED VERSION) =====
    /**
     * ✅ แสดง Dialog สำหรับเลือกโฟลเดอร์จาก Dropdown (ปรับปรุงให้กรองและตรวจสอบ folder_id)
     */
    function showAddFolderDialog() {
        // ✅ กรองเฉพาะโฟลเดอร์ที่มี folder_id ถูกต้อง
        const validAllFolders = filterValidFolders(allFolders);

        const availableFolders = validAllFolders.filter(f =>
            !currentPermissions.some(p => p.folder_id == f.folder_id)
        );

        if (availableFolders.length === 0) {
            Swal.fire({
                icon: 'info',
                title: 'ไม่มีโฟลเดอร์ที่สามารถเพิ่มได้',
                text: 'ผู้ใช้มีสิทธิ์เข้าถึงโฟลเดอร์ทั้งหมดแล้ว'
            });
            return;
        }

        const optionsHtml = availableFolders.map(f =>
            `<option value="${escapeHtml(f.folder_id)}">${escapeHtml(f.folder_name)}</option>`
        ).join('');

        Swal.fire({
            title: 'เพิ่มสิทธิ์โฟลเดอร์',
            html: `
            <div class="text-start">
                <div class="mb-3">
                    <label class="form-label">เลือกโฟลเดอร์</label>
                    <select id="swal-folder-select" class="form-control">
                        <option value="">-- เลือกโฟลเดอร์ --</option>
                        ${optionsHtml}
                    </select>
                </div>
                <div class="mb-3">
                    <label class="form-label">ระดับสิทธิ์</label>
                    <select id="swal-access-level" class="form-control">
                        <option value="read">อ่านอย่างเดียว</option>
                        <option value="write" selected>อ่าน-เขียน</option>
                        <option value="admin">ผู้ดูแล</option>
                    </select>
                </div>
            </div>
        `,
            showCancelButton: true,
            confirmButtonText: 'เพิ่มสิทธิ์',
            cancelButtonText: 'ยกเลิก',
            preConfirm: () => {
                const folderId = document.getElementById('swal-folder-select').value;
                const accessLevel = document.getElementById('swal-access-level').value;

                if (!folderId) {
                    Swal.showValidationMessage('กรุณาเลือกโฟลเดอร์');
                    return false;
                }

                // ✅ ตรวจสอบความถูกต้องของ folder_id ก่อนส่ง
                if (!isValidGoogleDriveFolderId(folderId)) {
                    Swal.showValidationMessage('รหัสโฟลเดอร์ไม่ถูกต้อง กรุณาเลือกโฟลเดอร์อื่น');
                    return false;
                }

                return { folderId, accessLevel };
            }
        }).then((result) => {
            if (result.isConfirmed) {
                const folder = availableFolders.find(f => f.folder_id == result.value.folderId);

                // ✅ Debug: ตรวจสอบว่า folder ที่เลือกมีข้อมูลครบ
                console.log('📁 Selected Folder from Dropdown:', folder);

                addFolderPermission(result.value.folderId, folder.folder_name, result.value.accessLevel);
            }
        });
    }

    /**
     * ✅ แสดง Dialog สำหรับเพิ่มสิทธิ์โฟลเดอร์ที่เลือกจาก Folder Tree
     * @param {string} folderId - Google Drive Folder ID
     * @param {string} folderName - ชื่อโฟลเดอร์
     */
    function showAddFolderPermissionDialog(folderId, folderName) {
        // ✅ Debug: ตรวจสอบค่าที่ได้รับทันที
        console.log('=== showAddFolderPermissionDialog ===');
        console.log('Received folderId:', folderId);
        console.log('Received folderName:', folderName);
        console.log('Type of folderId:', typeof folderId);
        console.log('Length of folderId:', folderId ? folderId.length : 'N/A');

        // ✅ Validation ก่อนแสดง Dialog
        if (!folderId) {
            console.error('❌ folderId is missing or invalid:', folderId);
            Swal.fire({
                icon: 'error',
                title: 'ข้อมูลไม่ครบถ้วน',
                text: 'ไม่พบรหัสโฟลเดอร์ กรุณาลองใหม่อีกครั้ง'
            });
            return;
        }

        if (!folderName) {
            console.warn('⚠️ folderName is missing, using default');
            folderName = 'ไม่ระบุชื่อ';
        }

        Swal.fire({
            title: 'เพิ่มสิทธิ์โฟลเดอร์',
            html: `
            <div class="text-start">
                <p class="mb-3">โฟลเดอร์: <strong>${escapeHtml(folderName)}</strong></p>
                <div class="mb-3">
                    <label class="form-label">ระดับสิทธิ์</label>
                    <select id="swal-access-level" class="form-control">
                        <option value="read">อ่านอย่างเดียว</option>
                        <option value="write" selected>อ่าน-เขียน</option>
                        <option value="admin">ผู้ดูแล</option>
                    </select>
                </div>
            </div>
        `,
            showCancelButton: true,
            confirmButtonText: 'เพิ่มสิทธิ์',
            cancelButtonText: 'ยกเลิก',
            preConfirm: () => {
                return document.getElementById('swal-access-level').value;
            }
        }).then((result) => {
            if (result.isConfirmed) {
                console.log('✅ User confirmed - calling addFolderPermission');
                console.log('  folderId:', folderId);
                console.log('  folderName:', folderName);
                console.log('  accessLevel:', result.value);

                addFolderPermission(folderId, folderName, result.value);
            }
        });
    }

    /**
     * ✅ เพิ่มสิทธิ์โฟลเดอร์ (FIXED VERSION with Validation)
     * @param {string} folderId - Google Drive Folder ID
     * @param {string} folderName - ชื่อโฟลเดอร์
     * @param {string} accessLevel - ระดับสิทธิ์ (read, write, admin)
     */
    function addFolderPermission(folderId, folderName, accessLevel) {
        // ✅ Debug Log - ตรวจสอบค่าที่ได้รับทันที
        console.log('===========================================');
        console.log('📥 addFolderPermission() Called');
        console.log('===========================================');
        console.log('Parameters Received:');
        console.log('  folderId:', folderId);
        console.log('  folderName:', folderName);
        console.log('  accessLevel:', accessLevel);
        console.log('  userId:', userId);
        console.log('-------------------------------------------');
        console.log('Type Checks:');
        console.log('  typeof folderId:', typeof folderId);
        console.log('  typeof folderName:', typeof folderName);
        console.log('  typeof accessLevel:', typeof accessLevel);
        console.log('  typeof userId:', typeof userId);
        console.log('-------------------------------------------');
        console.log('Value Checks:');
        console.log('  folderId is empty?', !folderId);
        console.log('  folderName is empty?', !folderName);
        console.log('  accessLevel is empty?', !accessLevel);
        console.log('  userId is empty?', !userId);
        console.log('===========================================');

        // ✅ Validation - ตรวจสอบข้อมูลก่อนส่ง
        const validationErrors = [];

        if (!folderId) {
            validationErrors.push('folderId is missing or empty');
            console.error('❌ Validation Error: folderId is missing');
            console.error('  Received value:', folderId);
        } else {
            // ตรวจสอบว่า folderId เป็น Google Drive Folder ID จริง (ความยาวมากกว่า 10 ตัวอักษร)
            if (typeof folderId === 'string' && folderId.length < 10) {
                console.warn('⚠️ Warning: folderId seems too short for Google Drive ID:', folderId);
                console.warn('  This might be a database ID instead of Google Drive folder_id');
            }
            console.log('✅ folderId is valid');
        }

        if (!userId) {
            validationErrors.push('userId is not set');
            console.error('❌ Validation Error: userId is not set');
            console.error('  Current userId value:', userId);
        } else {
            console.log('✅ userId is valid:', userId);
        }

        if (!accessLevel) {
            validationErrors.push('accessLevel is missing');
            console.error('❌ Validation Error: accessLevel is missing');
        } else if (!['read', 'write', 'admin'].includes(accessLevel)) {
            validationErrors.push('accessLevel is invalid (must be: read, write, admin)');
            console.error('❌ Validation Error: Invalid accessLevel:', accessLevel);
        } else {
            console.log('✅ accessLevel is valid:', accessLevel);
        }

        if (!folderName) {
            console.warn('⚠️ Warning: folderName is missing, using default');
            folderName = 'ไม่ระบุชื่อ';
        }

        // ถ้ามี validation errors ให้หยุดและแสดง error
        if (validationErrors.length > 0) {
            console.error('===========================================');
            console.error('❌ VALIDATION FAILED');
            console.error('===========================================');
            validationErrors.forEach((error, index) => {
                console.error(`  ${index + 1}. ${error}`);
            });
            console.error('===========================================');

            Swal.fire({
                icon: 'error',
                title: 'ข้อมูลไม่ครบถ้วน',
                html: `
                <div class="text-start">
                    <p class="mb-2">พบข้อผิดพลาดในข้อมูล:</p>
                    <ul class="text-danger">
                        ${validationErrors.map(err => `<li>${err}</li>`).join('')}
                    </ul>
                    <p class="mt-3 text-muted small">กรุณาลองใหม่อีกครั้ง หรือติดต่อผู้ดูแลระบบ</p>
                </div>
            `
            });
            return;
        }

        console.log('✅ All validations passed');
        console.log('===========================================');

        // ✅ แสดง Loading
        Swal.fire({
            title: 'กำลังเพิ่มสิทธิ์...',
            text: `เพิ่มสิทธิ์เข้าถึง "${folderName}"`,
            allowOutsideClick: false,
            showConfirmButton: false,
            didOpen: () => {
                Swal.showLoading();
            }
        });

        // ✅ สร้าง payload
        const payload = {
            member_id: userId,
            folder_id: folderId,
            access_type: accessLevel
        };

        console.log('-------------------------------------------');
        console.log('📤 Preparing to send request');
        console.log('-------------------------------------------');
        console.log('Payload:', payload);
        console.log('Payload (JSON):', JSON.stringify(payload));
        console.log('Payload size:', JSON.stringify(payload).length, 'bytes');
        console.log('===========================================');

        // ✅ ส่ง Request
        fetch('<?php echo site_url('google_drive_system/add_folder_permission'); ?>', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-Requested-With': 'XMLHttpRequest'
            },
            body: JSON.stringify(payload)
        })
            .then(response => {
                console.log('-------------------------------------------');
                console.log('📨 Response Received');
                console.log('-------------------------------------------');
                console.log('Status:', response.status, response.statusText);
                console.log('OK?', response.ok);
                console.log('Headers:', response.headers);
                console.log('Content-Type:', response.headers.get('content-type'));
                console.log('-------------------------------------------');

                // ✅ ตรวจสอบ Response Status
                if (!response.ok) {
                    console.error('❌ HTTP Error:', response.status);

                    // พยายามอ่าน response body เพื่อ debug
                    return response.text().then(text => {
                        console.error('-------------------------------------------');
                        console.error('❌ Server Error Response (Text):');
                        console.error('-------------------------------------------');
                        console.error(text.substring(0, 500));
                        console.error('-------------------------------------------');

                        throw new Error(`Server error: ${response.status} ${response.statusText}`);
                    });
                }

                // ✅ ตรวจสอบ Content-Type
                const contentType = response.headers.get('content-type');
                if (!contentType || !contentType.includes('application/json')) {
                    console.warn('⚠️ Warning: Response is not JSON');
                    console.warn('Content-Type:', contentType);

                    return response.text().then(text => {
                        console.error('-------------------------------------------');
                        console.error('❌ Non-JSON Response:');
                        console.error('-------------------------------------------');
                        console.error(text.substring(0, 500));
                        console.error('-------------------------------------------');

                        throw new Error('Server returned non-JSON response');
                    });
                }

                // ✅ Parse JSON
                return response.json();
            })
            .then(data => {
                console.log('-------------------------------------------');
                console.log('📄 Response Data (Parsed JSON)');
                console.log('-------------------------------------------');
                console.log('Full Response:', data);
                console.log('Success?', data.success);
                console.log('Message:', data.message);
                if (data.data) {
                    console.log('Data:', data.data);
                }
                console.log('===========================================');

                // ✅ ตรวจสอบ Response
                if (data.success) {
                    console.log('✅ Permission Added Successfully');
                    console.log('  Permission ID:', data.data?.permission_id);
                    console.log('  Member:', data.data?.member_name);
                    console.log('  Folder:', data.data?.folder_name);
                    console.log('  Access Type:', data.data?.access_type);

                    Swal.fire({
                        icon: 'success',
                        title: 'เพิ่มสิทธิ์สำเร็จ',
                        text: `เพิ่มสิทธิ์เข้าถึง "${folderName}" แล้ว`,
                        timer: 2000,
                        showConfirmButton: false
                    }).then(() => {
                        // ✅ Reload ข้อมูล
                        if (typeof loadUserPermissionData === 'function') {
                            loadUserPermissionData();
                        } else {
                            console.warn('⚠️ loadUserPermissionData() not found, reloading page');
                            location.reload();
                        }
                    });
                } else {
                    throw new Error(data.message || 'ไม่สามารถเพิ่มสิทธิ์ได้');
                }
            })
            .catch(error => {
                console.error('===========================================');
                console.error('❌ ERROR CAUGHT');
                console.error('===========================================');
                console.error('Error Type:', error.name);
                console.error('Error Message:', error.message);
                console.error('Error Stack:', error.stack);
                console.error('===========================================');

                Swal.fire({
                    icon: 'error',
                    title: 'เกิดข้อผิดพลาด',
                    text: error.message,
                    footer: '<span class="text-muted small">กรุณาตรวจสอบ Console (F12) สำหรับข้อมูลเพิ่มเติม</span>'
                });
            });
    }

    // ===== Update Permission Function =====
    /**
         * ✅ อัปเดตสิทธิ์โฟลเดอร์ (แก้ไขการรับ Error Message จาก Backend)
         */
    function updateFolderPermission(permissionId, newAccessType) {
        console.log("=== updateFolderPermission ===");
        console.log("1. permissionId:", permissionId);
        console.log("2. newAccessType:", newAccessType);

        const payload = {
            member_id: userId,
            permission_id: permissionId,
            access_type: newAccessType
        };

        fetch('<?php echo site_url("google_drive_system/update_folder_permission"); ?>', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-Requested-With': 'XMLHttpRequest'
            },
            body: JSON.stringify(payload)
        })
            .then(response => {
                return response.json().then(data => {
                    if (!response.ok) {
                        // ✅ FIX: ตรวจสอบ empty string
                        let errorMessage = data.message;

                        if (!errorMessage || errorMessage.trim() === '') {
                            errorMessage = `HTTP error! status: ${response.status}`;
                        }

                        console.log('Error from server:', errorMessage);
                        throw new Error(errorMessage);
                    }
                    return data;
                });
            })
            .then(data => {
                if (data.success) {
                    showToast('อัปเดตสิทธิ์แล้ว', 'success');
                    loadUserPermissionData();
                } else {
                    throw new Error(data.message || 'ไม่สามารถอัปเดตสิทธิ์ได้');
                }
            })
            .catch(error => {
                console.log('=== ERROR DEBUG ===');
                console.log('error.message:', error.message);
                console.log('error.message length:', error.message ? error.message.length : 0);
                console.log('===================');

                let errorMessage = error.message || 'เกิดข้อผิดพลาด';

                // ✅ FIX: แปลง <br> เป็น \n สำหรับ alert()
                errorMessage = errorMessage.replace(/<br\s*\/?>/gi, '\n');

                console.error('Final error message:', errorMessage);
                alert(errorMessage);
                loadUserPermissionData();
            });
    }

    /**
     * ✅ ลบสิทธิ์โฟลเดอร์ (Confirmation)
     */
    function removeFolderPermission(permissionId, folderName, memberName) {
        Swal.fire({
            title: 'ยืนยันการลบสิทธิ์?',
            html: `ต้องการลบสิทธิ์ของ <strong>${memberName}</strong><br>จากโฟลเดอร์ <strong>"${folderName}"</strong>`,
            icon: 'warning',
            showCancelButton: true,
            confirmButtonText: 'ลบสิทธิ์',
            cancelButtonText: 'ยกเลิก',
            confirmButtonColor: '#ef4444'
        }).then((result) => {
            if (result.isConfirmed) {
                executeRemoveFolderPermission(permissionId, folderName, memberName);
            }
        });
    }

    /**
     * ✅ ลบสิทธิ์โฟลเดอร์ (Execute)
     */
    function executeRemoveFolderPermission(permissionId, folderName, memberName) {
        Swal.fire({
            title: 'กำลังลบสิทธิ์...',
            allowOutsideClick: false,
            showConfirmButton: false,
            didOpen: () => {
                Swal.showLoading();
            }
        });

        fetch('<?php echo site_url('google_drive_system/remove_folder_permission'); ?>', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-Requested-With': 'XMLHttpRequest'
            },
            body: JSON.stringify({
                permission_id: permissionId
            })
        })
            .then(response => {
                const contentType = response.headers.get('content-type');

                if (!response.ok) {
                    if (contentType && contentType.includes('text/html')) {
                        return response.text().then(html => {
                            console.error('🚨 Server returned HTML:', html.substring(0, 500));
                            throw new Error(`เซิร์ฟเวอร์เกิดข้อผิดพลาด (${response.status})`);
                        });
                    }
                }

                if (!contentType || !contentType.includes('application/json')) {
                    return response.text().then(text => {
                        console.error('🚨 Non-JSON response:', text.substring(0, 500));
                        throw new Error('เซิร์ฟเวอร์ส่งกลับข้อมูลไม่ถูกต้อง');
                    });
                }

                return response.json();
            })
            .then(data => {
                if (data.success) {
                    Swal.fire({
                        icon: 'success',
                        title: 'ลบสิทธิ์สำเร็จ',
                        html: `ลบสิทธิ์ของ <strong>${memberName}</strong><br>จากโฟลเดอร์ <strong>"${folderName}"</strong> แล้ว`,
                        timer: 2000,
                        showConfirmButton: false
                    }).then(() => {
                        if (typeof loadUserPermissionData === 'function') {
                            loadUserPermissionData();
                        } else if (typeof loadFolderPermissions === 'function') {
                            loadFolderPermissions();
                        } else {
                            location.reload();
                        }
                    });
                } else {
                    throw new Error(data.message || 'ไม่สามารถลบสิทธิ์ได้');
                }
            })
            .catch(error => {
                console.error('Remove permission error:', error);
                Swal.fire({
                    icon: 'error',
                    title: 'เกิดข้อผิดพลาด',
                    text: error.message || 'ไม่สามารถลบสิทธิ์ได้'
                });
            });
    }

    // ===== Settings Functions =====
    function saveSettings() {
        const notes = document.getElementById('permissionNotes').value;
        const autoSync = false; // ✅ ระบบซิงค์อัตโนมัติอยู่แล้ว ส่งค่าเป็น false เสมอ
        const notification = false; // ✅ ฟีเจอร์ยังไม่เปิดใช้งาน ส่งค่าเป็น false เสมอ

        Swal.fire({
            title: 'กำลังบันทึก...',
            allowOutsideClick: false,
            showConfirmButton: false,
            didOpen: () => {
                Swal.showLoading();
            }
        });

        fetch('<?php echo site_url('google_drive_system/save_user_settings'); ?>', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-Requested-With': 'XMLHttpRequest'
            },
            body: JSON.stringify({
                user_id: userId,
                notes: notes,
                auto_sync: autoSync,
                notification: notification
            })
        })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    hasUnsavedChanges = false;
                    Swal.fire({
                        icon: 'success',
                        title: 'บันทึกสำเร็จ',
                        text: data.message || 'บันทึกการตั้งค่าเรียบร้อยแล้ว',
                        timer: 2000,
                        showConfirmButton: false
                    });
                } else {
                    throw new Error(data.message || 'ไม่สามารถบันทึกได้');
                }
            })
            .catch(error => {
                console.error('Save settings error:', error);
                Swal.fire({
                    icon: 'error',
                    title: 'เกิดข้อผิดพลาด',
                    text: error.message
                });
            });
    }

    /**
     * ✅ ยกเลิกการเปลี่ยนแปลง (Cancel Settings)
     */
    function cancelSettings() {
        if (hasUnsavedChanges) {
            Swal.fire({
                title: 'ยกเลิกการเปลี่ยนแปลง?',
                text: 'การเปลี่ยนแปลงที่ยังไม่ได้บันทึกจะหายไป',
                icon: 'warning',
                showCancelButton: true,
                confirmButtonText: 'ยกเลิก',
                cancelButtonText: 'กลับไปแก้ไข',
                confirmButtonColor: '#6c757d',
                cancelButtonColor: '#0d6efd'
            }).then((result) => {
                if (result.isConfirmed) {
                    loadUserPermissionData();
                    hasUnsavedChanges = false;
                }
            });
        } else {
            Swal.fire({
                icon: 'info',
                title: 'ไม่มีการเปลี่ยนแปลง',
                text: 'ไม่มีการเปลี่ยนแปลงที่ต้องยกเลิก',
                timer: 1500,
                showConfirmButton: false
            });
        }
    }

    /**
     * ✅ รีเซ็ตสิทธิ์ทั้งหมด (Reset All Permissions)
     * 
     * หมายเหตุ: Soft delete - UPDATE is_active = 0
     */
    function resetAllPermissions() {
        Swal.fire({
            title: 'รีเซ็ตสิทธิ์ทั้งหมด?',
            html: `
                <div class="text-start">
                    <p class="text-danger mb-3">
                        <i class="fas fa-exclamation-triangle me-2"></i>
                        <strong>คำเตือน:</strong> การดำเนินการนี้จะปิดใช้งานสิทธิ์ทั้งหมด
                    </p>
                    <p class="text-muted mb-2">สิทธิ์ที่จะถูกรีเซ็ต:</p>
                    <ul class="text-muted mb-0">
                        <li>สิทธิ์เข้าถึงโฟลเดอร์ทั้งหมด</li>
                        <li>การตั้งค่าระบบ</li>
                        <li>Storage Quota</li>
                    </ul>
                    <div class="alert alert-info mt-3 mb-0">
                        <small>
                            <i class="fas fa-info-circle me-1"></i>
                            สิทธิ์ที่ถูกรีเซ็ตสามารถคืนค่าได้ในภายหลัง
                        </small>
                    </div>
                </div>
            `,
            icon: 'warning',
            showCancelButton: true,
            confirmButtonText: 'รีเซ็ตทั้งหมด',
            cancelButtonText: 'ยกเลิก',
            confirmButtonColor: '#ef4444',
            cancelButtonColor: '#6c757d'
        }).then((result) => {
            if (result.isConfirmed) {
                executeResetAllPermissions();
            }
        });
    }

    /**
     * ✅ ดำเนินการรีเซ็ตสิทธิ์ทั้งหมด
     */
    function executeResetAllPermissions() {
        Swal.fire({
            title: 'กำลังรีเซ็ต...',
            allowOutsideClick: false,
            showConfirmButton: false,
            didOpen: () => {
                Swal.showLoading();
            }
        });

        fetch('<?php echo site_url('google_drive_system/reset_user_permissions'); ?>', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/x-www-form-urlencoded',
                'X-Requested-With': 'XMLHttpRequest'
            },
            body: `user_id=${userId}`
        })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    Swal.fire({
                        icon: 'success',
                        title: 'รีเซ็ตเรียบร้อย',
                        text: data.message || 'รีเซ็ตสิทธิ์ทั้งหมดเรียบร้อยแล้ว',
                        timer: 2000,
                        showConfirmButton: false
                    }).then(() => {
                        loadUserPermissionData();
                    });
                } else {
                    throw new Error(data.message || 'ไม่สามารถรีเซ็ตได้');
                }
            })
            .catch(error => {
                console.error('Reset error:', error);
                Swal.fire({
                    icon: 'error',
                    title: 'เกิดข้อผิดพลาด',
                    text: error.message
                });
            });
    }

    /**
     * ✅ ยกเลิกการเข้าถึงถาวร (Remove User Access)
     * 
     * หมายเหตุ: Hard delete - DELETE จากฐานข้อมูล (ไม่สามารถย้อนกลับได้)
     */
    function removeUserAccess() {
        Swal.fire({
            title: 'ยกเลิกการเข้าถึงถาวร?',
            html: `
                <div class="text-start">
                    <p class="text-danger mb-3">
                        <i class="fas fa-exclamation-triangle me-2"></i>
                        <strong>คำเตือนสำคัญ:</strong> การดำเนินการนี้ไม่สามารถย้อนกลับได้!
                    </p>
                    <p class="text-muted mb-2">การดำเนินการนี้จะ:</p>
                    <ul class="text-muted mb-0">
                        <li><strong>ลบข้อมูลออกจากฐานข้อมูลถาวร</strong></li>
                        <li>ลบสิทธิ์เข้าถึงโฟลเดอร์ทั้งหมด</li>
                        <li>ลบการตั้งค่าระบบทั้งหมด</li>
                        <li>ปิด Google Drive access ในระบบ</li>
                    </ul>
                    <div class="alert alert-danger mt-3 mb-0">
                        <small>
                            <i class="fas fa-ban me-1"></i>
                            <strong>ข้อมูลที่ลบไปแล้วไม่สามารถกู้คืนได้!</strong>
                        </small>
                    </div>
                </div>
            `,
            icon: 'error',
            showCancelButton: true,
            confirmButtonText: 'ยืนยันการลบถาวร',
            cancelButtonText: 'ยกเลิก',
            confirmButtonColor: '#dc3545',
            cancelButtonColor: '#6c757d',
            focusCancel: true // เน้นที่ปุ่มยกเลิกเป็นค่าเริ่มต้น
        }).then((result) => {
            if (result.isConfirmed) {
                // ยืนยันอีกครั้ง
                Swal.fire({
                    title: 'ยืนยันอีกครั้ง',
                    text: 'คุณแน่ใจหรือไม่ว่าต้องการลบข้อมูลถาวร?',
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonText: 'ยืนยัน',
                    cancelButtonText: 'ยกเลิก',
                    confirmButtonColor: '#dc3545',
                    cancelButtonColor: '#6c757d'
                }).then((result) => {
                    if (result.isConfirmed) {
                        executeRemoveUserAccess();
                    }
                });
            }
        });
    }

    /**
     * ✅ ดำเนินการลบการเข้าถึงถาวร
     */
    function executeRemoveUserAccess() {
        Swal.fire({
            title: 'กำลังลบข้อมูล...',
            text: 'กรุณารอสักครู่...',
            allowOutsideClick: false,
            showConfirmButton: false,
            didOpen: () => {
                Swal.showLoading();
            }
        });

        fetch('<?php echo site_url('google_drive_system/remove_user_access'); ?>', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/x-www-form-urlencoded',
                'X-Requested-With': 'XMLHttpRequest'
            },
            body: `user_id=${userId}`
        })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    Swal.fire({
                        icon: 'success',
                        title: 'ดำเนินการเรียบร้อย',
                        html: `
                            <p>${data.message || 'ลบการเข้าถึงเรียบร้อยแล้ว'}</p>
                            <p class="text-muted mb-0">
                                <small>กำลังกลับไปหน้าจัดการผู้ใช้...</small>
                            </p>
                        `,
                        timer: 3000,
                        timerProgressBar: true,
                        showConfirmButton: false
                    }).then(() => {
                        // กลับไปหน้า setup
                        window.location.href = '<?php echo site_url('google_drive_system/setup'); ?>';
                    });
                } else {
                    throw new Error(data.message || 'ไม่สามารถดำเนินการได้');
                }
            })
            .catch(error => {
                console.error('Remove access error:', error);
                Swal.fire({
                    icon: 'error',
                    title: 'เกิดข้อผิดพลาด',
                    text: error.message,
                    confirmButtonText: 'ตกลง',
                    confirmButtonColor: '#0d6efd'
                });
            });
    }

    /**
     * ✅ Track การเปลี่ยนแปลงของ Notes
     */
    document.getElementById('permissionNotes').addEventListener('input', function () {
        hasUnsavedChanges = true;
    });

    // ===== Statistics Update =====
    function updateStatistics(stats) {
        if (!stats) return;

        document.getElementById('totalFoldersCount').textContent = stats.total_folders || 0;
        document.getElementById('writeFoldersCount').textContent = stats.write_folders || 0;
        document.getElementById('totalFilesCount').textContent = stats.total_files || 0;
        document.getElementById('sharedCount').textContent = stats.shared_count || 0;

        // Update storage
        const storageUsed = stats.storage_used || 0;
        const storageTotal = stats.storage_total || 1073741824; // 1GB default
        const storagePercent = Math.round((storageUsed / storageTotal) * 100);

        document.getElementById('storagePercent').textContent = storagePercent;
        document.getElementById('storageBar').style.width = storagePercent + '%';
        document.getElementById('storageUsed').textContent = formatBytes(storageUsed);
        document.getElementById('storageTotal').textContent = formatBytes(storageTotal);
    }

    // ===== Helper Functions =====
    function escapeHtml(text) {
        if (!text) return '';
        const div = document.createElement('div');
        div.textContent = text;
        return div.innerHTML;
    }

    function formatBytes(bytes, decimals = 2) {
        if (bytes === 0) return '0 B';
        const k = 1024;
        const dm = decimals < 0 ? 0 : decimals;
        const sizes = ['B', 'KB', 'MB', 'GB', 'TB'];
        const i = Math.floor(Math.log(bytes) / Math.log(k));
        return parseFloat((bytes / Math.pow(k, i)).toFixed(dm)) + ' ' + sizes[i];
    }

    function formatDate(dateString) {
        if (!dateString) return '-';
        const date = new Date(dateString);
        return date.toLocaleDateString('th-TH', {
            year: 'numeric',
            month: 'short',
            day: 'numeric',
            hour: '2-digit',
            minute: '2-digit'
        });
    }

    function getAccessLevelText(level) {
        const levels = {
            'read': 'อ่านอย่างเดียว',
            'write': 'อ่าน-เขียน',
            'admin': 'ผู้ดูแล'
        };
        return levels[level] || level;
    }

    function getAccessLevelBadge(level) {
        const badges = {
            'read': 'info',
            'write': 'success',
            'admin': 'warning'
        };
        return badges[level] || 'secondary';
    }

    function getActionType(action) {
        if (action.includes('added') || action.includes('granted')) return 'added';
        if (action.includes('removed') || action.includes('revoked')) return 'removed';
        if (action.includes('updated') || action.includes('changed')) return 'updated';
        return 'updated';
    }

    function getActionIcon(action) {
        if (action.includes('added') || action.includes('granted')) return 'fa-plus';
        if (action.includes('removed') || action.includes('revoked')) return 'fa-minus';
        if (action.includes('updated') || action.includes('changed')) return 'fa-edit';
        return 'fa-info';
    }

    function showToast(message, type = 'info') {
        const icons = {
            success: 'success',
            error: 'error',
            info: 'info',
            warning: 'warning'
        };

        Swal.fire({
            icon: icons[type] || 'info',
            title: message,
            toast: true,
            position: 'top-end',
            showConfirmButton: false,
            timer: 3000,
            timerProgressBar: true
        });
    }
</script>