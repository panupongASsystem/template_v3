<?php
// Helper function สำหรับ CSS class ของสถานะ
if (!function_exists('get_queue_status_class')) {
    function get_queue_status_class($status)
    {
        switch ($status) {
            case 'รอยืนยันการจอง':
                return 'waiting';
            case 'ยืนยันการจอง':
            case 'คิวได้รับการยืนยัน':
                return 'received';
            case 'เสร็จสิ้น':
                return 'completed';
            case 'ยกเลิก':
            case 'คิวได้ถูกยกเลิก':
                return 'cancelled';
            default:
                return 'waiting';
        }
    }
}
?>

<style>
    /* ===== Queue Report Specific Styles ===== */
    .container-fluid {
        padding: 20px;
    }

    .page-header {
        background: linear-gradient(135deg, #e0f2fe 0%, #b3e5fc 100%);
        color: white;
        padding: 2rem;
        border-radius: 15px;
        margin-bottom: 2rem;
        box-shadow: 0 4px 15px rgba(0, 0, 0, 0.08);
    }

    .page-header h1 {
        font-size: 2rem;
        font-weight: 600;
        margin: 0;
        text-shadow: 0 2px 4px rgba(0, 0, 0, 0.2);
        color: #0277bd !important;
    }

    .page-header .breadcrumb {
        background: transparent;
        padding: 0;
        margin: 10px 0 0 0;
    }

    .page-header .breadcrumb-item a {
        color: rgba(255, 255, 255, 0.8);
        text-decoration: none;
    }

    .page-header .breadcrumb-item.active {
        color: rgba(255, 255, 255, 1);
    }

    .filter-card {
        background: white;
        border-radius: 15px;
        padding: 1.5rem;
        box-shadow: 0 4px 15px rgba(0, 0, 0, 0.08);
        margin-bottom: 2rem;
    }

    .filter-grid {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
        gap: 1rem;
        margin-bottom: 1rem;
    }

    .filter-actions {
        display: flex;
        gap: 0.5rem;
        justify-content: flex-end;
        flex-wrap: wrap;
    }

    .stats-row {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
        gap: 1rem;
        margin-bottom: 2rem;
    }

    .stat-card {
        background: white;
        border-radius: 12px;
        padding: 1.5rem;
        box-shadow: 0 4px 15px rgba(0, 0, 0, 0.08);
        position: relative;
        overflow: hidden;
        transition: all 0.3s ease;
    }

    .stat-card::before {
        content: '';
        position: absolute;
        top: 0;
        left: 0;
        right: 0;
        height: 4px;
    }

    /* Queue-specific colors */
    .stat-card.total::before {
        background: linear-gradient(90deg, #42a5f5, #1e88e5);
    }

    .stat-card.waiting::before {
        background: linear-gradient(90deg, #ffb74d, #fb8c00);
    }

    .stat-card.confirmed::before {
        background: linear-gradient(90deg, #26c6da, #00acc1);
    }

    .stat-card.completed::before {
        background: linear-gradient(90deg, #66bb6a, #43a047);
    }

    .stat-card.cancelled::before {
        background: linear-gradient(90deg, #ef5350, #e53935);
    }

    .stat-card:hover {
        transform: translateY(-3px);
        box-shadow: 0 8px 25px rgba(0, 0, 0, 0.12);
    }

    .stat-header {
        display: flex;
        align-items: center;
        justify-content: between;
        margin-bottom: 1rem;
    }

    .stat-icon {
        width: 45px;
        height: 45px;
        border-radius: 10px;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 1.25rem;
        color: white;
        margin-right: 1rem;
    }

    .stat-icon.total {
        background: linear-gradient(135deg, #42a5f5, #1e88e5);
    }

    .stat-icon.waiting {
        background: linear-gradient(135deg, #ffb74d, #fb8c00);
    }

    .stat-icon.confirmed {
        background: linear-gradient(135deg, #26c6da, #00acc1);
    }

    .stat-icon.completed {
        background: linear-gradient(135deg, #66bb6a, #43a047);
    }

    .stat-icon.cancelled {
        background: linear-gradient(135deg, #ef5350, #e53935);
    }

    .stat-value {
        font-size: 2rem;
        font-weight: 700;
        color: #1e293b;
        margin-bottom: 0.25rem;
    }

    .stat-label {
        color: #64748b;
        font-size: 0.9rem;
        font-weight: 500;
    }

    .chart-card {
        background: white;
        border-radius: 15px;
        padding: 1.5rem;
        box-shadow: 0 4px 15px rgba(0, 0, 0, 0.08);
        margin-bottom: 2rem;
    }

    .chart-header {
        display: flex;
        align-items: center;
        justify-content: space-between;
        margin-bottom: 1.5rem;
        border-bottom: 1px solid #e2e8f0;
        padding-bottom: 1rem;
    }

    .chart-title {
        font-size: 1.25rem;
        font-weight: 600;
        color: #1e293b;
        margin: 0;
    }

    .simple-chart {
        padding: 1rem 0;
    }

    .chart-item {
        display: flex;
        align-items: center;
        margin-bottom: 1rem;
        padding: 0.5rem;
        border-radius: 8px;
        background: #f8fafc;
    }

    .chart-color {
        width: 20px;
        height: 20px;
        border-radius: 4px;
        margin-right: 1rem;
        flex-shrink: 0;
    }

    .chart-color.waiting {
        background: #ffb74d;
    }

    .chart-color.confirmed {
        background: #26c6da;
    }

    .chart-color.completed {
        background: #66bb6a;
    }

    .chart-color.cancelled {
        background: #ef5350;
    }

    .chart-label {
        flex: 1;
        font-weight: 500;
        color: #374151;
    }

    .chart-value {
        font-weight: 700;
        color: #1e293b;
    }

    .trend-item {
        display: flex;
        justify-content: space-between;
        padding: 0.5rem;
        border-bottom: 1px solid #f1f5f9;
    }

    .trend-item:last-child {
        border-bottom: none;
    }

    .trend-date {
        color: #64748b;
        font-size: 0.9rem;
    }

    .trend-count {
        font-weight: 600;
        color: #1e293b;
    }

    .table-card {
        background: white;
        border-radius: 15px;
        overflow: hidden;
        box-shadow: 0 4px 15px rgba(0, 0, 0, 0.08);
    }

    .table-header {
        background: #f8fafc;
        padding: 1rem 1.5rem;
        border-bottom: 1px solid #e2e8f0;
        display: flex;
        align-items: center;
        justify-content: between;
    }

    .table-title {
        font-size: 1.25rem;
        font-weight: 600;
        color: #1e293b;
        margin: 0;
    }

    .table-actions {
        display: flex;
        gap: 0.5rem;
    }

    .table-responsive {
        max-height: 1800px;
        overflow-y: auto;
    }

    .table-card .table {
        margin: 0;
    }

    .table-card .table thead th {
        background: #f8fafc;
        border: none;
        font-weight: 600;
        color: #374151;
        padding: 1rem;
        position: sticky;
        top: 0;
        z-index: 10;
    }

    .table-card .table tbody td {
        padding: 1rem;
        border-color: #f1f5f9;
        vertical-align: middle;
    }

    .status-badge {
        padding: 0.4rem 0.8rem;
        border-radius: 20px;
        font-size: 0.75rem;
        font-weight: 600;
        text-align: center;
        white-space: nowrap;
        min-width: 120px;
        display: inline-block;
    }

    .status-badge.waiting {
        background: #fff3cd;
        color: #d68910;
        border: 1px solid #ffb74d;
    }

    .status-badge.received,
    .status-badge.confirmed {
        background: #e0f7fa;
        color: #00695c;
        border: 1px solid #26c6da;
    }

    .status-badge.completed {
        background: #e8f5e8;
        color: #2e7d32;
        border: 1px solid #66bb6a;
    }

    .status-badge.cancelled {
        background: #ffebee;
        color: #c62828;
        border: 1px solid #ef5350;
    }

    .action-buttons {
        display: flex;
        gap: 0.3rem;
        flex-wrap: wrap;
        justify-content: flex-start;
    }

    .btn-action {
        padding: 0.4rem 0.8rem;
        border-radius: 6px;
        border: none;
        font-size: 0.8rem;
        font-weight: 500;
        cursor: pointer;
        transition: all 0.3s ease;
        text-decoration: none;
        display: inline-flex;
        align-items: center;
        gap: 0.4rem;
        min-width: 70px;
        justify-content: center;
        white-space: nowrap;
    }

    .btn-action.view {
        background: linear-gradient(135deg, #42a5f5, #1e88e5);
        color: white;
    }

    .btn-action.view:hover {
        background: linear-gradient(135deg, #1e88e5, #1565c0);
        transform: translateY(-1px);
        color: white;
    }

    .btn-action.update {
        background: linear-gradient(135deg, #26c6da, #00acc1);
        color: white;
    }

    .btn-action.update:hover {
        background: linear-gradient(135deg, #00acc1, #00838f);
        transform: translateY(-1px);
        color: white;
    }

    .btn-action.delete {
        background: linear-gradient(135deg, #ef5350, #e53935);
        color: white;
    }

    .btn-action.delete:hover {
        background: linear-gradient(135deg, #e53935, #d32f2f);
        transform: translateY(-1px);
        color: white;
    }

    .btn-action.disabled {
        background: #f3f4f6;
        color: #9ca3af;
        cursor: not-allowed;
        opacity: 0.6;
    }

    .btn-action.disabled:hover {
        transform: none;
        background: #f3f4f6;
        color: #9ca3af;
    }

    /* Status buttons in rows */
    .status-row {
        background: #f8fafc;
        border-left: 4px solid #42a5f5;
    }

    .status-cell {
        padding: 1rem !important;
        border-top: 1px solid #d1d5db !important;
    }

    .status-update-row {
        display: flex;
        flex-direction: column;
        gap: 1rem;
        align-items: center;
    }

    .status-label {
        font-weight: 600;
        color: #374151;
        display: flex;
        align-items: center;
        gap: 0.5rem;
        font-size: 0.95rem;
    }

    .status-label i {
        color: #42a5f5;
    }

    .status-buttons-container {
        display: flex;
        gap: 0.5rem;
        flex-wrap: wrap;
        justify-content: center;
        align-items: center;
    }

    .btn-status-row {
        padding: 0.4rem 0.8rem;
        border-radius: 6px;
        border: none;
        font-size: 0.8rem;
        font-weight: 500;
        cursor: pointer;
        transition: all 0.3s ease;
        text-decoration: none;
        display: inline-flex;
        align-items: center;
        gap: 0.4rem;
        min-width: 120px;
        justify-content: center;
        white-space: nowrap;
        text-align: center;
        height: 38px;
    }

    .btn-status-row i {
        font-size: 1rem;
    }

    .btn-status-row span {
        font-size: 0.8rem;
        line-height: 1.2;
    }

    .btn-status-row.waiting {
        background: #fff3cd;
        color: #d68910;
        border: 1px solid #ffb74d;
    }

    .btn-status-row.waiting:hover:not(:disabled) {
        background: #ffb74d;
        color: #fff;
        transform: translateY(-1px);
    }

    .btn-status-row.received {
        background: #f3e5f5;
        color: #6a1b9a;
        border: 1px solid #9c27b0;
    }

    .btn-status-row.received:hover:not(:disabled) {
        background: #9c27b0;
        color: #fff;
        transform: translateY(-1px);
    }

    .btn-status-row.confirmed {
        background: #e0f7fa;
        color: #00695c;
        border: 1px solid #26c6da;
    }

    .btn-status-row.confirmed:hover:not(:disabled) {
        background: #26c6da;
        color: #fff;
        transform: translateY(-1px);
    }

    .btn-status-row.completed {
        background: #e8f5e8;
        color: #2e7d32;
        border: 1px solid #66bb6a;
    }

    .btn-status-row.completed:hover:not(:disabled) {
        background: #66bb6a;
        color: #fff;
        transform: translateY(-1px);
    }

    .btn-status-row.cancelled {
        background: #ffebee;
        color: #c62828;
        border: 1px solid #ef5350;
    }

    .btn-status-row.cancelled:hover:not(:disabled) {
        background: #ef5350;
        color: #fff;
        transform: translateY(-1px);
    }

    .btn-status-row.current {
        background: #f3f4f6;
        color: #6b7280;
        cursor: not-allowed;
        opacity: 0.8;
        border: 1px solid #d1d5db;
    }

    .btn-status-row.current:hover {
        background: #f3f4f6;
        color: #6b7280;
        transform: none;
        box-shadow: none;
    }

    .btn-status-row.disabled {
        background: #f9fafb;
        color: #9ca3af;
        cursor: not-allowed;
        opacity: 0.6;
        border: 1px solid #e5e7eb;
    }

    .btn-status-row.disabled:hover {
        background: #f9fafb;
        color: #9ca3af;
        transform: none;
        box-shadow: none;
    }

    .btn-status-row.current::before {
        content: "✓ ";
        font-weight: bold;
    }

    /* Permission-based styles */
    .permission-denied {
        opacity: 0.5;
        cursor: not-allowed;
        pointer-events: none;
    }

    .permission-warning {
        background: #fef3c7;
        border: 1px solid #f59e0b;
        color: #92400e;
        padding: 0.5rem 1rem;
        border-radius: 8px;
        font-size: 0.875rem;
        margin-bottom: 1rem;
    }

    /* Additional Modal Styles */
    .modal-section {
        margin-bottom: 1.5rem;
        padding: 1rem;
        border: 1px solid #e2e8f0;
        border-radius: 8px;
        background: #f8fafc;
    }

    .section-title {
        margin-bottom: 1rem;
        color: #1e293b;
        font-weight: 600;
        display: flex;
        align-items: center;
        gap: 0.5rem;
    }

    .status-change-summary {
        background: white;
        padding: 1rem;
        border-radius: 6px;
        border: 1px solid #d1d5db;
    }

    .status-arrow {
        display: flex;
        align-items: center;
        justify-content: center;
        margin: 1rem 0;
        padding: 0.5rem;
        background: #f3f4f6;
        border-radius: 6px;
    }

    .current-status {
        background: #fbbf24;
        color: white;
        padding: 0.25rem 0.75rem;
        border-radius: 15px;
        font-size: 0.875rem;
        font-weight: 500;
    }

    .new-status {
        background: #10b981;
        color: white;
        padding: 0.25rem 0.75rem;
        border-radius: 15px;
        font-size: 0.875rem;
        font-weight: 500;
    }

    .character-counter {
        text-align: right;
        font-size: 0.75rem;
        color: #6b7280;
        margin-top: 0.25rem;
    }

    .character-counter.warning {
        color: #d97706;
    }

    .character-counter.danger {
        color: #dc2626;
    }

    .image-upload-container {
        border: 2px dashed #d1d5db;
        border-radius: 8px;
        padding: 2rem;
        text-align: center;
        cursor: pointer;
        transition: all 0.3s ease;
        background: #f9fafb;
    }

    .image-upload-container:hover {
        border-color: #3b82f6;
        background: #eff6ff;
    }

    .upload-icon {
        font-size: 2rem;
        color: #6b7280;
        margin-bottom: 0.5rem;
    }

    .upload-limit-info {
        font-size: 0.75rem;
        color: #6b7280;
        margin: 0;
    }

    .file-input-hidden {
        display: none;
    }

    .compression-processing {
        display: none;
        text-align: center;
        padding: 1rem;
        background: #eff6ff;
        border-radius: 6px;
        margin: 1rem 0;
    }

    .compression-processing.show {
        display: block;
    }

    .compression-processing .spinner {
        display: inline-block;
        width: 1rem;
        height: 1rem;
        border: 2px solid #e5e7eb;
        border-top: 2px solid #3b82f6;
        border-radius: 50%;
        animation: spin 1s linear infinite;
        margin-right: 0.5rem;
    }

    @keyframes spin {
        0% {
            transform: rotate(0deg);
        }

        100% {
            transform: rotate(360deg);
        }
    }

    .upload-progress {
        display: none;
        margin: 1rem 0;
    }

    .image-preview-container {
        display: flex;
        flex-wrap: wrap;
        gap: 0.5rem;
        margin: 1rem 0;
    }

    .image-preview-item {
        position: relative;
        width: 80px;
        height: 80px;
        border-radius: 6px;
        overflow: hidden;
        border: 1px solid #d1d5db;
    }

    .image-preview-item img {
        width: 100%;
        height: 100%;
        object-fit: cover;
    }

    .image-remove-btn {
        position: absolute;
        top: -5px;
        right: -5px;
        background: #dc2626;
        color: white;
        border: none;
        border-radius: 50%;
        width: 20px;
        height: 20px;
        font-size: 0.75rem;
        cursor: pointer;
        display: flex;
        align-items: center;
        justify-content: center;
    }

    .compression-badge {
        position: absolute;
        bottom: 0;
        left: 0;
        right: 0;
        background: rgba(16, 185, 129, 0.9);
        color: white;
        font-size: 0.6rem;
        padding: 2px;
        text-align: center;
    }

    .image-upload-stats {
        display: flex;
        justify-content: space-between;
        font-size: 0.75rem;
        color: #6b7280;
        margin-top: 0.5rem;
    }

    .file-preview {
        display: flex;
        flex-direction: column;
        align-items: center;
        justify-content: center;
        width: 100%;
        height: 100%;
        background: #f3f4f6;
        color: #6b7280;
        font-size: 0.7rem;
        text-align: center;
        padding: 0.25rem;
    }

    .file-preview i {
        font-size: 1.5rem;
        margin-bottom: 0.25rem;
    }

    /* Case Container Styling */
    .case-container {
        background: #ffffff;
        border: 2px solid #e2e8f0;
        border-radius: 12px;
        margin-bottom: 1.5rem;
        overflow: hidden;
        box-shadow: 0 2px 8px rgba(0, 0, 0, 0.06);
        transition: all 0.3s ease;
    }

    .case-container:hover {
        border-color: #42a5f5;
        box-shadow: 0 4px 20px rgba(66, 165, 245, 0.15);
        transform: translateY(-1px);
    }

    .case-container .table {
        margin-bottom: 0;
    }

    .case-container .table thead {
        display: none;
    }

    .case-container .table tbody tr {
        border: none;
    }

    .case-container .table tbody td {
        border: none;
        vertical-align: middle;
        padding: 1rem;
    }

    .case-data-row {
        background: #ffffff;
        border-bottom: 1px solid #f1f5f9;
    }

    .case-data-row:hover {
        background: #f8fafc;
    }

    .case-data-row td {
        border-bottom: 1px solid #f1f5f9 !important;
    }

    .case-status-row {
        background: #f8fafc;
        border-left: 4px solid #42a5f5;
        border-bottom: none;
    }

    .case-status-row td {
        border-bottom: none !important;
        border-top: none !important;
    }

    .case-header {
        background: linear-gradient(135deg, #e3f2fd 0%, #bbdefb 100%);
        padding: 0.75rem 1rem;
        border-bottom: 1px solid #e2e8f0;
        font-size: 0.875rem;
        font-weight: 600;
        color: #1565c0;
        display: flex;
        align-items: center;
        gap: 0.5rem;
    }

    .case-header i {
        color: #42a5f5;
    }

    .case-number {
        background: linear-gradient(135deg, #42a5f5, #1e88e5);
        color: white;
        padding: 0.25rem 0.75rem;
        border-radius: 20px;
        font-size: 0.75rem;
        font-weight: 700;
        margin-left: auto;
    }

    /* File attachment display with preview */
    .queue-files {
        display: flex;
        gap: 0.4rem;
        flex-wrap: wrap;
        align-items: center;
    }

    .file-item {
        position: relative;
        display: flex;
        align-items: center;
        gap: 0.3rem;
        background: #f1f5f9;
        padding: 0.3rem 0.6rem;
        border-radius: 12px;
        font-size: 0.75rem;
        color: #64748b;
        border: 1px solid #e2e8f0;
        transition: all 0.3s ease;
        cursor: pointer;
    }

    .file-item:hover {
        background: #e2e8f0;
        border-color: #42a5f5;
        transform: translateY(-1px);
        box-shadow: 0 2px 8px rgba(66, 165, 245, 0.2);
    }

    .file-item i {
        color: #42a5f5;
        font-size: 0.8rem;
    }

    .file-item.image-file {
        padding: 0;
        border-radius: 8px;
        overflow: hidden;
        width: 40px;
        height: 40px;
        background: none;
        border: 2px solid #e2e8f0;
    }

    .file-item.image-file:hover {
        border-color: #42a5f5;
        transform: scale(1.1);
    }

    .file-preview-img {
        width: 100%;
        height: 100%;
        object-fit: cover;
        border-radius: 6px;
    }

    .file-item .file-name {
        max-width: 80px;
        white-space: nowrap;
        overflow: hidden;
        text-overflow: ellipsis;
    }

    .files-more-badge {
        background: linear-gradient(135deg, #6366f1, #8b5cf6);
        color: white;
        padding: 0.2rem 0.5rem;
        border-radius: 12px;
        font-size: 0.7rem;
        font-weight: 600;
        cursor: pointer;
        transition: all 0.3s ease;
    }

    .files-more-badge:hover {
        transform: scale(1.05);
        box-shadow: 0 2px 8px rgba(99, 102, 241, 0.3);
    }

    /* Image Preview Modal */
    .image-preview-modal {
        position: fixed;
        top: 0;
        left: 0;
        width: 100%;
        height: 100%;
        background: rgba(0, 0, 0, 0.9);
        z-index: 9999;
        display: none;
        align-items: center;
        justify-content: center;
        backdrop-filter: blur(10px);
    }

    .image-preview-modal.show {
        display: flex;
    }

    .image-preview-container {
        position: relative;
        max-width: 90%;
        max-height: 90%;
        border-radius: 15px;
        overflow: hidden;
        box-shadow: 0 20px 40px rgba(0, 0, 0, 0.3);
    }

    .image-preview-modal img {
        width: 100%;
        height: 100%;
        object-fit: contain;
        border-radius: 15px;
    }

    .image-preview-close {
        position: absolute;
        top: 15px;
        right: 15px;
        background: rgba(255, 255, 255, 0.9);
        border: none;
        border-radius: 50%;
        width: 45px;
        height: 45px;
        display: flex;
        align-items: center;
        justify-content: center;
        cursor: pointer;
        font-size: 1.2rem;
        color: #333;
        transition: all 0.3s ease;
    }

    .image-preview-close:hover {
        background: white;
        transform: scale(1.1);
    }

    .image-preview-info {
        position: absolute;
        bottom: 0;
        left: 0;
        right: 0;
        background: linear-gradient(transparent, rgba(0, 0, 0, 0.7));
        color: white;
        padding: 20px;
        text-align: center;
    }

    .image-preview-filename {
        font-size: 1.1rem;
        font-weight: 600;
        margin-bottom: 5px;
    }

    .image-preview-size {
        font-size: 0.9rem;
        opacity: 0.8;
    }



    /* Alert Cards Styles */
    .alert-summary-row {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(150px, 1fr));
        gap: 1rem;
        margin-bottom: 1.5rem;
    }

    .alert-summary-card {
        background: white;
        border-radius: 10px;
        padding: 1rem;
        box-shadow: 0 2px 8px rgba(0, 0, 0, 0.08);
        cursor: pointer;
        transition: all 0.3s ease;
        display: flex;
        align-items: center;
        gap: 0.75rem;
    }

    .alert-summary-card:hover {
        transform: translateY(-2px);
        box-shadow: 0 4px 15px rgba(0, 0, 0, 0.15);
    }

    .alert-summary-card.warning {
        border-left: 4px solid #f59e0b;
    }

    .alert-summary-card.danger {
        border-left: 4px solid #ef4444;
    }

    .alert-summary-card.critical {
        border-left: 4px solid #dc2626;
    }

    .alert-icon {
        width: 40px;
        height: 40px;
        border-radius: 8px;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 1.25rem;
        color: white;
    }

    .alert-summary-card.warning .alert-icon {
        background: linear-gradient(135deg, #f59e0b, #d97706);
    }

    .alert-summary-card.danger .alert-icon {
        background: linear-gradient(135deg, #ef4444, #dc2626);
    }

    .alert-summary-card.critical .alert-icon {
        background: linear-gradient(135deg, #dc2626, #b91c1c);
    }

    .alert-content {
        flex: 1;
    }

    .alert-number {
        font-size: 1.5rem;
        font-weight: 700;
        color: #1e293b;
        margin-bottom: 0.25rem;
    }

    .alert-label {
        font-size: 0.8rem;
        color: #64748b;
        font-weight: 500;
    }

    .alerts-list {
        background: white;
        border-radius: 8px;
        overflow: hidden;
    }

    .alerts-header {
        background: #f8fafc;
        padding: 1rem;
        border-bottom: 1px solid #e2e8f0;
        display: flex;
        justify-content: space-between;
        align-items: center;
    }

    .alerts-items {
        max-height: 400px;
        overflow-y: auto;
    }

    .alert-item {
        display: flex;
        align-items: center;
        padding: 0.75rem 1rem;
        border-bottom: 1px solid #f1f5f9;
        cursor: pointer;
        transition: background-color 0.2s ease;
    }

    .alert-item:hover {
        background: #f8fafc;
    }

    .alert-item:last-child {
        border-bottom: none;
    }

    .alert-item-icon {
        width: 32px;
        height: 32px;
        border-radius: 6px;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 0.875rem;
        color: white;
        margin-right: 0.75rem;
        flex-shrink: 0;
    }

    .alert-item.warning .alert-item-icon {
        background: #f59e0b;
    }

    .alert-item.danger .alert-item-icon {
        background: #ef4444;
    }

    .alert-item.critical .alert-item-icon {
        background: #dc2626;
    }

    .alert-item-content {
        flex: 1;
        min-width: 0;
    }

    .alert-item-header {
        display: flex;
        justify-content: space-between;
        align-items: center;
        margin-bottom: 0.25rem;
    }

    .alert-case-id {
        font-weight: 600;
        color: #1e293b;
        font-size: 0.875rem;
    }

    .alert-days {
        font-size: 0.75rem;
        font-weight: 500;
        color: #ef4444;
    }

    .alert-item-title {
        font-size: 0.8rem;
        color: #374151;
        margin-bottom: 0.25rem;
        white-space: nowrap;
        overflow: hidden;
        text-overflow: ellipsis;
    }

    .alert-item-status {
        font-size: 0.7rem;
        color: #6b7280;
    }

    .alert-item-action {
        color: #9ca3af;
        font-size: 0.75rem;
        margin-left: 0.5rem;
    }

    .alert-item-empty {
        padding: 2rem;
        text-align: center;
        color: #6b7280;
    }

    .alerts-footer {
        background: #f8fafc;
        padding: 1rem;
        border-top: 1px solid #e2e8f0;
        display: flex;
        justify-content: space-between;
        gap: 0.5rem;
    }

    /* Responsive Design */
    @media (max-width: 768px) {
        .stats-row {
            grid-template-columns: repeat(2, 1fr);
        }

        .filter-grid {
            grid-template-columns: 1fr;
        }

        .filter-actions {
            justify-content: stretch;
        }

        .filter-actions .btn {
            flex: 1;
        }

        .table-header {
            flex-direction: column;
            gap: 1rem;
            align-items: stretch;
        }

        .action-buttons {
            flex-direction: column;
            gap: 0.2rem;
        }

        .btn-action {
            width: 100%;
            min-width: auto;
            padding: 0.5rem;
            font-size: 0.75rem;
        }

        .status-buttons-container {
            flex-direction: column;
            gap: 0.2rem;
        }

        .btn-status-row {
            width: 100%;
            min-width: auto;
            padding: 0.4rem;
            font-size: 0.7rem;
            justify-content: flex-start;
        }

        .table-responsive {
            font-size: 0.85rem;
        }

        /* Case container responsive */
        .case-container {
            margin-bottom: 1rem;
            font-size: 0.875rem;
        }

        .case-header {
            padding: 0.5rem 0.75rem;
            font-size: 0.8rem;
        }

        .case-number {
            font-size: 0.7rem;
            padding: 0.2rem 0.5rem;
        }

        .case-data-row td {
            padding: 0.75rem 0.5rem;
            font-size: 0.8rem;
        }

        .case-status-row .status-cell {
            padding: 0.75rem 0.5rem !important;
        }

        .status-label {
            font-size: 0.8rem;
            text-align: center;
            margin-bottom: 0.5rem;
        }

        .alert-summary-row {
            grid-template-columns: 1fr;
        }
    }
</style>



<style>
    /* ===== NEW PAGINATION UI ===== */
    .modern-pagination-wrapper {
        background: linear-gradient(135deg, #ffffff 0%, #f8fafc 100%);
        border-radius: 20px;
        padding: 2rem;
        box-shadow:
            0 10px 25px rgba(0, 0, 0, 0.08),
            0 20px 48px rgba(0, 0, 0, 0.04);
        border: 1px solid rgba(255, 255, 255, 0.6);
        backdrop-filter: blur(10px);
        margin-top: 2rem;
    }

    .pagination-header {
        display: flex;
        align-items: center;
        justify-content: space-between;
        margin-bottom: 1.5rem;
        flex-wrap: wrap;
        gap: 1rem;
    }

    .pagination-stats {
        display: flex;
        align-items: center;
        gap: 1rem;
        flex-wrap: wrap;
    }

    .stats-card {
        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        color: white;
        padding: 0.75rem 1.25rem;
        border-radius: 15px;
        font-size: 0.875rem;
        font-weight: 600;
        box-shadow: 0 4px 15px rgba(102, 126, 234, 0.3);
        display: flex;
        align-items: center;
        gap: 0.5rem;
    }

    .stats-card i {
        font-size: 1rem;
    }

    .stats-numbers {
        color: #64748b;
        font-size: 0.9rem;
        font-weight: 500;
        background: white;
        padding: 0.6rem 1rem;
        border-radius: 12px;
        border: 1px solid #e2e8f0;
        box-shadow: inset 0 1px 3px rgba(0, 0, 0, 0.05);
    }

    .stats-numbers .highlight {
        color: #1e293b;
        font-weight: 700;
    }

    .pagination-actions {
        display: flex;
        align-items: center;
        gap: 1rem;
        flex-wrap: wrap;
    }

    /* Quick Jump */
    .quick-jump {
        display: flex;
        align-items: center;
        gap: 0.5rem;
        background: white;
        padding: 0.5rem 1rem;
        border-radius: 15px;
        border: 2px solid #e2e8f0;
        box-shadow: 0 2px 8px rgba(0, 0, 0, 0.05);
        transition: all 0.3s ease;
    }

    .quick-jump:hover {
        border-color: #3b82f6;
        box-shadow: 0 4px 15px rgba(59, 130, 246, 0.15);
    }

    .quick-jump label {
        font-size: 0.8rem;
        color: #64748b;
        font-weight: 600;
        margin: 0;
        white-space: nowrap;
    }

    .quick-jump input {
        width: 60px;
        border: none;
        background: transparent;
        text-align: center;
        font-weight: 700;
        color: #1e293b;
        padding: 0.25rem;
        font-size: 0.875rem;
    }

    .quick-jump input:focus {
        outline: none;
    }

    .quick-jump button {
        background: linear-gradient(135deg, #3b82f6, #1d4ed8);
        color: white;
        border: none;
        border-radius: 8px;
        padding: 0.4rem 0.6rem;
        cursor: pointer;
        transition: all 0.3s ease;
        display: flex;
        align-items: center;
        font-size: 0.8rem;
    }

    .quick-jump button:hover {
        background: linear-gradient(135deg, #1d4ed8, #1e40af);
        transform: scale(1.05);
    }

    /* Refresh Button */
    .refresh-btn {
        background: linear-gradient(135deg, #10b981, #059669);
        color: white;
        border: none;
        padding: 0.7rem 1.2rem;
        border-radius: 12px;
        font-weight: 600;
        font-size: 0.875rem;
        cursor: pointer;
        transition: all 0.3s ease;
        display: flex;
        align-items: center;
        gap: 0.5rem;
        box-shadow: 0 4px 12px rgba(16, 185, 129, 0.3);
    }

    .refresh-btn:hover {
        background: linear-gradient(135deg, #059669, #047857);
        transform: translateY(-2px);
        box-shadow: 0 6px 20px rgba(16, 185, 129, 0.4);
    }

    /* Main Pagination */
    .pagination-main {
        display: flex;
        justify-content: center;
        align-items: center;
        gap: 0.5rem;
        padding: 1.5rem 0;
        flex-wrap: wrap;
    }

    .pagination-nav {
        display: flex;
        align-items: center;
        gap: 0.25rem;
        background: white;
        padding: 0.5rem;
        border-radius: 15px;
        box-shadow:
            0 4px 15px rgba(0, 0, 0, 0.08),
            inset 0 1px 0 rgba(255, 255, 255, 0.6);
        border: 1px solid rgba(0, 0, 0, 0.05);
    }

    .page-btn {
        min-width: 44px;
        height: 44px;
        border: none;
        background: transparent;
        color: #64748b;
        font-weight: 600;
        font-size: 0.875rem;
        border-radius: 10px;
        cursor: pointer;
        transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
        display: flex;
        align-items: center;
        justify-content: center;
        position: relative;
        overflow: hidden;
    }

    .page-btn::before {
        content: '';
        position: absolute;
        top: 0;
        left: 0;
        right: 0;
        bottom: 0;
        background: linear-gradient(135deg, #667eea, #764ba2);
        opacity: 0;
        transition: opacity 0.3s ease;
        border-radius: 10px;
    }

    .page-btn span {
        position: relative;
        z-index: 2;
    }

    .page-btn:hover:not(:disabled) {
        color: white;
        transform: translateY(-2px);
        box-shadow: 0 6px 20px rgba(102, 126, 234, 0.4);
    }

    .page-btn:hover:not(:disabled)::before {
        opacity: 1;
    }

    .page-btn.active {
        background: linear-gradient(135deg, #667eea, #764ba2);
        color: white;
        box-shadow: 0 4px 15px rgba(102, 126, 234, 0.4);
        transform: translateY(-1px);
    }

    .page-btn.active::before {
        opacity: 1;
    }

    .page-btn:disabled {
        opacity: 0.4;
        cursor: not-allowed;
        color: #94a3b8;
    }

    .page-btn:disabled:hover {
        transform: none;
        box-shadow: none;
    }

    .page-btn.nav-btn {
        min-width: 50px;
        font-weight: 700;
        background: linear-gradient(135deg, #f1f5f9, #e2e8f0);
        color: #64748b;
    }

    .page-btn.nav-btn:hover:not(:disabled) {
        background: linear-gradient(135deg, #3b82f6, #1d4ed8);
        color: white;
    }

    .page-btn.nav-btn:disabled {
        background: #f8fafc;
        color: #cbd5e1;
    }

    /* Pagination Info */
    .pagination-footer {
        display: flex;
        justify-content: space-between;
        align-items: center;
        margin-top: 1rem;
        padding-top: 1rem;
        border-top: 1px solid #e2e8f0;
        flex-wrap: wrap;
        gap: 1rem;
    }

    .page-size-selector {
        display: flex;
        align-items: center;
        gap: 0.5rem;
        font-size: 0.875rem;
        color: #64748b;
    }

    .page-size-selector select {
        background: white;
        border: 1px solid #e2e8f0;
        border-radius: 8px;
        padding: 0.4rem 0.8rem;
        font-size: 0.875rem;
        font-weight: 600;
        color: #374151;
        cursor: pointer;
    }

    .page-size-selector select:focus {
        outline: none;
        border-color: #3b82f6;
        box-shadow: 0 0 0 3px rgba(59, 130, 246, 0.1);
    }

    .pagination-summary {
        font-size: 0.875rem;
        color: #64748b;
        background: linear-gradient(135deg, #f8fafc, #f1f5f9);
        padding: 0.6rem 1.2rem;
        border-radius: 12px;
        border: 1px solid #e2e8f0;
    }

    /* Responsive Design */
    @media (max-width: 768px) {
        .modern-pagination-wrapper {
            padding: 1.5rem;
            margin: 1rem;
        }

        .pagination-header {
            flex-direction: column;
            align-items: stretch;
            gap: 1rem;
        }

        .pagination-stats {
            justify-content: center;
        }

        .pagination-actions {
            justify-content: center;
        }

        .pagination-main {
            padding: 1rem 0;
        }

        .pagination-nav {
            padding: 0.25rem;
            gap: 0.15rem;
        }

        .page-btn {
            min-width: 38px;
            height: 38px;
            font-size: 0.8rem;
        }

        .pagination-footer {
            flex-direction: column;
            text-align: center;
        }

        .stats-card {
            font-size: 0.8rem;
            padding: 0.6rem 1rem;
        }
    }

    /* Animation */
    @keyframes slideIn {
        from {
            opacity: 0;
            transform: translateY(20px);
        }

        to {
            opacity: 1;
            transform: translateY(0);
        }
    }

    .modern-pagination-wrapper {
        animation: slideIn 0.6s ease-out;
    }

    /* Loading State */
    .pagination-loading {
        opacity: 0.6;
        pointer-events: none;
    }

    .pagination-loading::after {
        content: '';
        position: absolute;
        top: 50%;
        left: 50%;
        width: 24px;
        height: 24px;
        margin: -12px 0 0 -12px;
        border: 3px solid #e2e8f0;
        border-top: 3px solid #3b82f6;
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


<div class="container-fluid">
    <!-- Page Header -->
    <div class="page-header">
        <h1><i class="fas fa-calendar-alt me-3"></i>รายงานการจองคิว</h1>

        <!-- Permission Warning -->
        <?php if (!$permissions['can_update_status']): ?>
            <div class="permission-warning mt-3">
                <i class="fas fa-exclamation-triangle me-2"></i>
                คุณมีสิทธิ์ดูข้อมูลเท่านั้น ไม่สามารถจัดการสถานะคิวได้
            </div>
        <?php endif; ?>
    </div>

    <!-- Statistics Cards -->
    <div class="stats-row">
        <div class="stat-card total">
            <div class="stat-header">
                <div class="stat-icon total">
                    <i class="fas fa-calendar-check"></i>
                </div>
            </div>
            <div class="stat-value"><?= number_format($queue_summary['total']) ?></div>
            <div class="stat-label">คิวทั้งหมด</div>
        </div>

        <div class="stat-card waiting">
            <div class="stat-header">
                <div class="stat-icon waiting">
                    <i class="fas fa-hourglass-half"></i>
                </div>
            </div>
            <div class="stat-value"><?= number_format($queue_summary['by_status']['รอยืนยันการจอง'] ?? 0) ?></div>
            <div class="stat-label">รอยืนยันการจอง</div>
        </div>

        <div class="stat-card confirmed">
            <div class="stat-header">
                <div class="stat-icon confirmed">
                    <i class="fas fa-check-circle"></i>
                </div>
            </div>
            <div class="stat-value">
                <?= number_format(($queue_summary['by_status']['ยืนยันการจอง'] ?? 0) + ($queue_summary['by_status']['คิวได้รับการยืนยัน'] ?? 0)) ?>
            </div>
            <div class="stat-label">คิวได้รับการยืนยัน</div>
        </div>

        <div class="stat-card completed">
            <div class="stat-header">
                <div class="stat-icon completed">
                    <i class="fas fa-check-double"></i>
                </div>
            </div>
            <div class="stat-value"><?= number_format($queue_summary['by_status']['เสร็จสิ้น'] ?? 0) ?></div>
            <div class="stat-label">เสร็จสิ้น</div>
        </div>

        <div class="stat-card cancelled">
            <div class="stat-header">
                <div class="stat-icon cancelled">
                    <i class="fas fa-times-circle"></i>
                </div>
            </div>
            <div class="stat-value">
                <?= number_format(($queue_summary['by_status']['ยกเลิก'] ?? 0) + ($queue_summary['by_status']['คิวได้ถูกยกเลิก'] ?? 0)) ?>
            </div>
            <div class="stat-label">ยกเลิก</div>
        </div>
    </div>

    <!-- Filter Card -->
    <div class="filter-card">
        <h5 class="mb-3"><i class="fas fa-filter me-2"></i>ตัวกรองข้อมูล</h5>
        <form method="GET" action="<?= site_url('Queue/queue_report') ?>" id="filterForm">
            <div class="filter-grid">
                <div class="form-group">
                    <label class="form-label">สถานะ:</label>
                    <select class="form-select" name="status">
                        <option value="">ทั้งหมด</option>
                        <?php foreach ($status_options as $status): ?>
                            <option value="<?= $status['queue_status'] ?>" <?= ($filters['status'] ?? '') == $status['queue_status'] ? 'selected' : '' ?>>
                                <?= $status['queue_status'] ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>

                <div class="form-group">
                    <label class="form-label">ประเภทผู้ใช้:</label>
                    <select class="form-select" name="user_type">
                        <option value="">ทั้งหมด</option>
                        <?php foreach ($user_type_options as $type): ?>
                            <option value="<?= $type['queue_user_type'] ?>" <?= ($filters['user_type'] ?? '') == $type['queue_user_type'] ? 'selected' : '' ?>>
                                <?= $type['display_name'] ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>

                <div class="form-group">
                    <label class="form-label">วันที่เริ่มต้น:</label>
                    <input type="date" class="form-control" name="date_from" value="<?= $filters['date_from'] ?? '' ?>">
                </div>

                <div class="form-group">
                    <label class="form-label">วันที่สิ้นสุด:</label>
                    <input type="date" class="form-control" name="date_to" value="<?= $filters['date_to'] ?? '' ?>">
                </div>

                <div class="form-group">
                    <label class="form-label">ค้นหา:</label>
                    <input type="text" class="form-control" name="search"
                        placeholder="ค้นหาหัวข้อ, รายละเอียด, ผู้จอง..." value="<?= $filters['search'] ?? '' ?>">
                </div>
            </div>

            <div class="filter-actions">
                <button type="submit" class="btn btn-primary">
                    <i class="fas fa-search me-1"></i>ค้นหา
                </button>
                <a href="<?= site_url('Queue/queue_report') ?>" class="btn btn-secondary">
                    <i class="fas fa-times me-1"></i>ล้างตัวกรอง
                </a>
                <a href="<?= site_url('Queue/export_excel/queue') ?>" class="btn btn-success">
                    <i class="fas fa-file-excel me-1"></i>ส่งออก Excel
                </a>
            </div>
        </form>
    </div>

    <!-- Work Performance & Alerts Row -->
    <div class="row mb-4">
        <div class="col-lg-6 col-md-12">
            <div class="chart-card">
                <div class="chart-header">
                    <h3 class="chart-title">
                        <i class="fas fa-exclamation-triangle me-2"></i>รายงาน Queue ที่ไม่มีการอัพเดท
                    </h3>
                    <div class="chart-actions">
                        <button class="btn btn-sm btn-outline-primary" onclick="refreshAlerts()">
                            <i class="fas fa-sync-alt"></i>
                        </button>
                    </div>
                </div>
                <div class="alerts-container">
                    <!-- Alert Summary Cards -->
                    <div class="alert-summary-row">
                        <?php
                        // *** แก้ไข: เกณฑ์การแบ่งกลุ่มที่ถูกต้อง ***
                        $today = new DateTime();
                        $warning_queues = [];   // 3-6 วัน
                        $danger_queues = [];    // 7-13 วัน  
                        $critical_queues = [];  // 14+ วัน
                        
                        if (!empty($pending_queues)) {
                            foreach ($pending_queues as $queue) {
                                $days_passed = isset($queue->days_old) ? intval($queue->days_old) : 0;

                                if ($days_passed === 0) {
                                    $queue_date = new DateTime($queue->queue_datesave);
                                    $diff = $today->diff($queue_date);
                                    $days_passed = $diff->days;
                                }

                                $queue_data = [
                                    'id' => $queue->queue_id,
                                    'days' => $days_passed,
                                    'topic' => $queue->queue_topic,
                                    'status' => $queue->queue_status,
                                    'date' => $queue->queue_datesave
                                ];

                                // *** แก้ไข: เปลี่ยนเงื่อนไขให้ถูกต้อง ***
                                if ($days_passed >= 14) {
                                    $critical_queues[] = $queue_data;
                                } elseif ($days_passed >= 7 && $days_passed <= 13) {
                                    $danger_queues[] = $queue_data;
                                } elseif ($days_passed >= 3 && $days_passed <= 6) {
                                    $warning_queues[] = $queue_data;
                                }
                            }
                        }
                        ?>



                        <div class="alert-summary-card warning" onclick="showCategoryDetails('warning')">
                            <div class="alert-icon">
                                <i class="fas fa-clock"></i>
                            </div>
                            <div class="alert-content">
                                <div class="alert-number"><?= count($warning_queues) ?></div>
                                <div class="alert-label">ค้าง 3-6 วัน</div>
                            </div>
                        </div>

                        <div class="alert-summary-card danger" onclick="showCategoryDetails('danger')">
                            <div class="alert-icon">
                                <i class="fas fa-exclamation-triangle"></i>
                            </div>
                            <div class="alert-content">
                                <div class="alert-number"><?= count($danger_queues) ?></div>
                                <div class="alert-label">ค้าง 7-13 วัน</div>
                            </div>
                        </div>

                        <div class="alert-summary-card critical" onclick="showCategoryDetails('critical')">
                            <div class="alert-icon">
                                <i class="fas fa-fire"></i>
                            </div>
                            <div class="alert-content">
                                <div class="alert-number"><?= count($critical_queues) ?></div>
                                <div class="alert-label">ค้าง 14+ วัน</div>
                            </div>
                        </div>
                    </div>

                    <!-- Detailed Alerts List -->
                    <div class="alerts-list">
                        <div class="alerts-header">
                            <h6 class="mb-0">Queue ที่ต้องติดตาม</h6>
                            <small class="text-muted">อัพเดทล่าสุด: <?= date('d/m/Y H:i') ?></small>
                        </div>

                        <div class="alerts-items">
                            <?php
                            // แสดง Critical Cases ก่อน (14+ วัน)
                            foreach (array_slice($critical_queues, 0, 3) as $alert):
                                ?>
                                <div class="alert-item critical" onclick="goToQueue('<?= $alert['id'] ?>')">
                                    <div class="alert-item-icon">
                                        <i class="fas fa-fire"></i>
                                    </div>
                                    <div class="alert-item-content">
                                        <div class="alert-item-header">
                                            <span class="alert-case-id">#<?= $alert['id'] ?></span>
                                            <span class="alert-days">ค้าง <?= $alert['days'] ?> วัน</span>
                                        </div>
                                        <div class="alert-item-title" title="<?= htmlspecialchars($alert['topic']) ?>">
                                            <?= htmlspecialchars(mb_substr($alert['topic'], 0, 30)) ?>
                                            <?= mb_strlen($alert['topic']) > 30 ? '...' : '' ?>
                                        </div>
                                        <div class="alert-item-status">สถานะ: <?= $alert['status'] ?></div>
                                    </div>
                                    <div class="alert-item-action">
                                        <i class="fas fa-chevron-right"></i>
                                    </div>
                                </div>
                            <?php endforeach; ?>

                            <?php
                            // แสดง Danger Cases (7-13 วัน)
                            foreach (array_slice($danger_queues, 0, 3) as $alert):
                                ?>
                                <div class="alert-item danger" onclick="goToQueue('<?= $alert['id'] ?>')">
                                    <div class="alert-item-icon">
                                        <i class="fas fa-exclamation-triangle"></i>
                                    </div>
                                    <div class="alert-item-content">
                                        <div class="alert-item-header">
                                            <span class="alert-case-id">#<?= $alert['id'] ?></span>
                                            <span class="alert-days">ค้าง <?= $alert['days'] ?> วัน</span>
                                        </div>
                                        <div class="alert-item-title" title="<?= htmlspecialchars($alert['topic']) ?>">
                                            <?= htmlspecialchars(mb_substr($alert['topic'], 0, 30)) ?>
                                            <?= mb_strlen($alert['topic']) > 30 ? '...' : '' ?>
                                        </div>
                                        <div class="alert-item-status">สถานะ: <?= $alert['status'] ?></div>
                                    </div>
                                    <div class="alert-item-action">
                                        <i class="fas fa-chevron-right"></i>
                                    </div>
                                </div>
                            <?php endforeach; ?>

                            <?php
                            // แสดง Warning Cases (3-6 วัน)
                            foreach (array_slice($warning_queues, 0, 2) as $alert):
                                ?>
                                <div class="alert-item warning" onclick="goToQueue('<?= $alert['id'] ?>')">
                                    <div class="alert-item-icon">
                                        <i class="fas fa-clock"></i>
                                    </div>
                                    <div class="alert-item-content">
                                        <div class="alert-item-header">
                                            <span class="alert-case-id">#<?= $alert['id'] ?></span>
                                            <span class="alert-days">ค้าง <?= $alert['days'] ?> วัน</span>
                                        </div>
                                        <div class="alert-item-title" title="<?= htmlspecialchars($alert['topic']) ?>">
                                            <?= htmlspecialchars(mb_substr($alert['topic'], 0, 30)) ?>
                                            <?= mb_strlen($alert['topic']) > 30 ? '...' : '' ?>
                                        </div>
                                        <div class="alert-item-status">สถานะ: <?= $alert['status'] ?></div>
                                    </div>
                                    <div class="alert-item-action">
                                        <i class="fas fa-chevron-right"></i>
                                    </div>
                                </div>
                            <?php endforeach; ?>

                            <?php
                            $total_alerts = count($critical_queues) + count($danger_queues) + count($warning_queues);
                            if ($total_alerts == 0):
                                ?>
                                <div class="alert-item-empty">
                                    <div class="text-center py-3">
                                        <i class="fas fa-check-circle text-success fa-2x mb-2"></i>
                                        <p class="text-muted mb-0">ไม่มี Queue ที่ค้างนาน</p>
                                        <small class="text-muted">ทุกคิวอยู่ในกำหนดเวลา</small>
                                    </div>
                                </div>
                            <?php endif; ?>
                        </div>

                        <div class="alerts-footer">
                            <button class="btn btn-sm btn-primary" onclick="showAllAlerts()">
                                <i class="fas fa-list me-1"></i>ดูรายการทั้งหมด (<?= $total_alerts ?>)
                            </button>

                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-lg-6 col-md-12">
            <div class="chart-card">
                <div class="chart-header">
                    <h3 class="chart-title">แนวโน้มรายวัน (15 วันล่าสุด)</h3>
                </div>
                <div class="simple-chart">
                    <?php
                    $recent_trends = array_slice($queue_trends, -15);
                    foreach ($recent_trends as $trend):
                        ?>
                        <div class="trend-item">
                            <div class="trend-date"><?= date('d/m/Y', strtotime($trend->date)) ?></div>
                            <div class="trend-count"><?= number_format($trend->count) ?> คิว</div>
                        </div>
                    <?php endforeach; ?>
                </div>
            </div>
        </div>
    </div>

    <!-- Data Table -->
    <div class="table-card">
        <div class="table-header">
            <h5 class="table-title">
                <i class="fas fa-list me-2"></i>รายการจองคิว
            </h5>
            <div class="table-actions">
                <button class="btn btn-outline-primary btn-sm" onclick="safeRefreshTable()">
                    <i class="fas fa-sync-alt me-1"></i>รีเฟรช
                </button>
            </div>
        </div>

        <div class="table-responsive">
            <!-- Cases Container -->
            <?php if (empty($queues)): ?>
                <div class="case-container">
                    <div class="text-center py-5">
                        <i class="fas fa-calendar fa-4x text-muted mb-3"></i>
                        <h5 class="text-muted">ไม่พบข้อมูลการจองคิว</h5>
                        <p class="text-muted">กรุณาลองใช้ตัวกรองอื่น หรือเพิ่มข้อมูลใหม่</p>
                    </div>
                </div>
            <?php else: ?>
                <?php foreach ($queues as $queue): ?>
                    <div class="case-container" data-queue-id="<?= $queue->queue_id ?>">
                        <!-- Case Header -->
                        <div class="case-header">
                            <i class="fas fa-calendar-alt"></i>
                            <span>การจองคิว</span>
                            <span class="case-number">#<?= $queue->queue_id ?></span>
                        </div>

                        <!-- Case Content -->
                        <table class="table mb-0">
                            <thead class="d-none">
                                <tr>
                                    <th style="width: 120px;">รหัสคิว</th>
                                    <th style="width: 120px;">วันที่จอง</th>
                                    <th style="width: 120px;">วันที่นัด</th>
                                    <th style="width: 130px;">สถานะ</th>
                                    <th style="width: 100px;">ไฟล์แนบ</th>
                                    <th style="width: 200px;">หัวข้อ</th>
                                    <th style="width: 250px;">รายละเอียด</th>
                                    <th style="width: 120px;">ผู้จอง</th>
                                    <th style="width: 100px;">เบอร์ติดต่อ</th>
                                    <th style="width: 100px;">ประเภท</th>
                                    <th style="width: 220px;">จัดการ</th>
                                </tr>
                            </thead>
                            <tbody>
                                <!-- Case Data Row -->
                                <tr class="case-data-row">
                                    <td class="fw-bold"><?= $queue->queue_id ?></td>
                                    <td>
                                        <small>
                                            <?= date('d/m/Y', strtotime($queue->queue_datesave . '+543 years')) ?><br>
                                            <?= date('H:i', strtotime($queue->queue_datesave)) ?> น.
                                        </small>
                                    </td>
                                    <td>
                                        <small>
                                            <?php if (!empty($queue->queue_date)): ?>
                                                <?= date('d/m/Y', strtotime($queue->queue_date . '+543 years')) ?><br>
                                                <?= date('H:i', strtotime($queue->queue_date)) ?> น.
                                                <?php if (!empty($queue->queue_time_slot)): ?>
                                                    <br><small class="text-muted"><?= $queue->queue_time_slot ?></small>
                                                <?php endif; ?>
                                            <?php else: ?>
                                                ไม่ระบุ
                                            <?php endif; ?>
                                        </small>
                                    </td>
                                    <td>
                                        <span class="status-badge <?= get_queue_status_class($queue->queue_status) ?>">
                                            <?= $queue->queue_status ?>
                                        </span>
                                    </td>
                                    <td>
                                        <div class="queue-files">
                                            <?php if (!empty($queue->files)): ?>
                                                <?php
                                                $imageFiles = [];
                                                $otherFiles = [];

                                                foreach ($queue->files as $file) {
                                                    $isImage = in_array(strtolower(pathinfo($file->queue_file_original_name, PATHINFO_EXTENSION)), ['jpg', 'jpeg', 'png', 'gif', 'webp']);
                                                    if ($isImage) {
                                                        $imageFiles[] = $file;
                                                    } else {
                                                        $otherFiles[] = $file;
                                                    }
                                                }

                                                $displayFiles = array_merge(array_slice($imageFiles, 0, 2), array_slice($otherFiles, 0, 1));
                                                $remainingCount = count($queue->files) - count($displayFiles);
                                                ?>

                                                <?php foreach ($displayFiles as $file): ?>
                                                    <?php
                                                    $isImage = in_array(strtolower(pathinfo($file->queue_file_original_name, PATHINFO_EXTENSION)), ['jpg', 'jpeg', 'png', 'gif', 'webp']);
                                                    $fileUrl = site_url('Queue/view_queue_image/' . $file->queue_file_name);
                                                    ?>

                                                    <?php if ($isImage): ?>
                                                        <div class="file-item image-file"
                                                            onclick="showImagePreview('<?= $fileUrl ?>', '<?= htmlspecialchars($file->queue_file_original_name, ENT_QUOTES) ?>', '<?= number_format($file->queue_file_size / 1024, 1) ?> KB')"
                                                            title="<?= htmlspecialchars($file->queue_file_original_name) ?>">
                                                            <img src="<?= $fileUrl ?>"
                                                                alt="<?= htmlspecialchars($file->queue_file_original_name) ?>"
                                                                class="file-preview-img" loading="lazy">
                                                        </div>
                                                    <?php else: ?>
                                                        <div class="file-item"
                                                            onclick="window.open('<?= site_url('Queue/download_queue_file/' . $file->queue_file_name) ?>', '_blank')"
                                                            title="<?= htmlspecialchars($file->queue_file_original_name) ?>">
                                                            <i class="fas fa-file"></i>
                                                            <span
                                                                class="file-name"><?= mb_substr($file->queue_file_original_name, 0, 8) ?><?= mb_strlen($file->queue_file_original_name) > 8 ? '...' : '' ?></span>
                                                        </div>
                                                    <?php endif; ?>
                                                <?php endforeach; ?>

                                                <?php if ($remainingCount > 0): ?>
                                                    <div class="files-more-badge" onclick="showAllFiles('<?= $queue->queue_id ?>')"
                                                        title="ดูไฟล์ทั้งหมด">
                                                        +<?= $remainingCount ?>
                                                    </div>
                                                <?php endif; ?>
                                            <?php else: ?>
                                                <span class="text-muted small">ไม่มีไฟล์</span>
                                            <?php endif; ?>
                                        </div>
                                    </td>
                                    <td>
                                        <div class="text-truncate-2" title="<?= htmlspecialchars($queue->queue_topic) ?>">
                                            <?= htmlspecialchars($queue->queue_topic) ?>
                                        </div>
                                    </td>
                                    <td>
                                        <div class="text-truncate-2" title="<?= htmlspecialchars($queue->queue_detail) ?>">
                                            <?= htmlspecialchars($queue->queue_detail) ?>
                                        </div>
                                    </td>
                                    <td><?= htmlspecialchars($queue->queue_by) ?></td>
                                    <td><?= htmlspecialchars($queue->queue_phone) ?></td>
                                    <td>
                                        <?php
                                        $user_type_badges = [
                                            'guest' => '<span class="badge bg-secondary">ผู้ใช้งานทั่วไป</span>',
                                            'public' => '<span class="badge bg-info">สมาชิก</span>',
                                            'staff' => '<span class="badge bg-warning">เจ้าหน้าที่</span>'
                                        ];
                                        echo $user_type_badges[$queue->queue_user_type] ?? '<span class="badge bg-light text-dark">ไม่ทราบ</span>';
                                        ?>
                                    </td>
                                    <td>
                                        <div class="action-buttons">
                                            <a href="<?= site_url('Queue/queue_detail/' . $queue->queue_id) ?>"
                                                class="btn-action view" title="ดูรายละเอียด">
                                                <i class="fas fa-eye"></i>ดู
                                            </a>

                                            <!-- Delete Button - เฉพาะ System Admin และ Super Admin -->
                                            <?php if ($permissions['can_delete']): ?>
                                                <button class="btn-action delete"
                                                    onclick="showDeleteModal('<?= $queue->queue_id ?>', '<?= htmlspecialchars($queue->queue_topic, ENT_QUOTES) ?>')"
                                                    title="ลบคิว">
                                                    <i class="fas fa-trash"></i>ลบ
                                                </button>
                                            <?php endif; ?>
                                        </div>
                                    </td>
                                </tr>

                                <!-- Case Status Management Row - แสดงเฉพาะเมื่อมีสิทธิ์อัพเดทสถานะ -->
                                <?php if ($permissions['can_update_status']): ?>
                                    <tr class="case-status-row">
                                        <td colspan="11" class="status-cell">
                                            <div class="status-update-row">
                                                <div class="status-label">
                                                    <i class="fas fa-sync-alt"></i>
                                                    อัพเดทสถานะคิว #<?= $queue->queue_id ?>
                                                </div>
                                                <div class="status-buttons-container">
                                                    <?php
                                                    $current_status = $queue->queue_status;

                                                    // Queue workflow rules
                                                    $workflow_rules = [
                                                        'รอยืนยันการจอง' => [
                                                            'enabled' => ['รับเรื่องพิจารณา', 'คิวได้รับการยืนยัน', 'ยกเลิก'],
                                                            'disabled' => ['รอยืนยันการจอง', 'เสร็จสิ้น']
                                                        ],
                                                        'รับเรื่องพิจารณา' => [
                                                            'enabled' => ['คิวได้รับการยืนยัน', 'ยกเลิก'],
                                                            'disabled' => ['รอยืนยันการจอง', 'รับเรื่องพิจารณา', 'เสร็จสิ้น']
                                                        ],
                                                        'คิวได้รับการยืนยัน' => [
                                                            'enabled' => ['เสร็จสิ้น', 'ยกเลิก'],
                                                            'disabled' => ['รอยืนยันการจอง', 'รับเรื่องพิจารณา', 'คิวได้รับการยืนยัน']
                                                        ],
                                                        'เสร็จสิ้น' => [
                                                            'enabled' => [],
                                                            'disabled' => ['รอยืนยันการจอง', 'รับเรื่องพิจารณา', 'คิวได้รับการยืนยัน', 'ยกเลิก', 'เสร็จสิ้น']
                                                        ],
                                                        'ยกเลิก' => [
                                                            'enabled' => [],
                                                            'disabled' => ['รอยืนยันการจอง', 'รับเรื่องพิจารณา', 'คิวได้รับการยืนยัน', 'เสร็จสิ้น', 'ยกเลิก']
                                                        ]
                                                    ];

                                                    $current_rules = $workflow_rules[$current_status] ?? $workflow_rules['รอยืนยันการจอง'];
                                                    $enabled_statuses = $current_rules['enabled'];
                                                    $disabled_statuses = $current_rules['disabled'];

                                                    $status_buttons = [
                                                        ['รอยืนยันการจอง', 'waiting', 'fas fa-hourglass-start'],
                                                        ['รับเรื่องพิจารณา', 'received', 'fas fa-file-import'],
                                                        ['คิวได้รับการยืนยัน', 'confirmed', 'fas fa-calendar-check'],
                                                        ['เสร็จสิ้น', 'completed', 'fas fa-check-double'],
                                                        ['ยกเลิก', 'cancelled', 'fas fa-times-circle']
                                                    ];

                                                    foreach ($status_buttons as $status_btn):
                                                        $status_text = $status_btn[0];
                                                        $status_class = $status_btn[1];
                                                        $status_icon = $status_btn[2];

                                                        $is_current = ($current_status === $status_text);
                                                        $is_enabled = in_array($status_text, $enabled_statuses);
                                                        $is_disabled = in_array($status_text, $disabled_statuses) || $is_current;

                                                        $button_classes = "btn-status-row {$status_class}";
                                                        $tooltip_text = '';
                                                        $onclick_code = '';

                                                        if ($is_current) {
                                                            $button_classes .= ' current';
                                                            $tooltip_text = 'สถานะปัจจุบัน';
                                                        } elseif ($is_enabled && !$is_disabled) {
                                                            $tooltip_text = 'เปลี่ยนเป็น ' . $status_text;
                                                            $queue_id_escaped = htmlspecialchars($queue->queue_id, ENT_QUOTES);
                                                            $current_status_escaped = htmlspecialchars($current_status, ENT_QUOTES);
                                                            $status_text_escaped = htmlspecialchars($status_text, ENT_QUOTES);

                                                            if ($status_text === 'รับเรื่องพิจารณา') {
                                                                $onclick_code = 'onclick="showReceiveCaseModal(\'' . $queue_id_escaped . '\', \'' . $current_status_escaped . '\', \'' . $status_text_escaped . '\')"';
                                                            } elseif ($status_text === 'คิวได้รับการยืนยัน') {
                                                                $onclick_code = 'onclick="showConfirmQueueModal(\'' . $queue_id_escaped . '\', \'' . $current_status_escaped . '\', \'' . $status_text_escaped . '\')"';
                                                            } elseif ($status_text === 'เสร็จสิ้น') {
                                                                $onclick_code = 'onclick="showCompleteModal(\'' . $queue_id_escaped . '\', \'' . $current_status_escaped . '\', \'' . $status_text_escaped . '\')"';
                                                            } elseif ($status_text === 'ยกเลิก') {
                                                                $onclick_code = 'onclick="showCancelModal(\'' . $queue_id_escaped . '\', \'' . $current_status_escaped . '\', \'' . $status_text_escaped . '\')"';
                                                            } else {
                                                                $onclick_code = 'onclick="window.queueManager.showEnhancedModal(\'' . $queue_id_escaped . '\', \'' . $current_status_escaped . '\', \'' . $status_text_escaped . '\')"';
                                                            }
                                                        } else {
                                                            $button_classes .= ' disabled';
                                                            $tooltip_text = 'ไม่สามารถใช้งานได้ในสถานะปัจจุบัน';
                                                        }
                                                        ?>
                                                        <button class="<?= $button_classes ?>" <?= $is_disabled ? 'disabled' : '' ?>
                                                            <?= $onclick_code ?> title="<?= $tooltip_text ?>">
                                                            <i class="<?= $status_icon ?>"></i>
                                                            <span><?= $status_text ?></span>
                                                        </button>
                                                    <?php endforeach; ?>
                                                </div>
                                            </div>
                                        </td>
                                    </tr>
                                <?php else: ?>
                                    <!-- แสดงข้อความเมื่อไม่มีสิทธิ์ -->
                                    <tr class="case-status-row">
                                        <td colspan="11" class="status-cell">
                                            <div class="text-center py-3">
                                                <i class="fas fa-lock text-muted fa-2x mb-2"></i>
                                                <p class="text-muted mb-0">คุณไม่มีสิทธิ์จัดการสถานะคิว</p>
                                                <small class="text-muted">ติดต่อผู้ดูแลระบบเพื่อขอสิทธิ์</small>
                                            </div>
                                        </td>
                                    </tr>
                                <?php endif; ?>
                            </tbody>
                        </table>
                    </div>
                <?php endforeach; ?>
            <?php endif; ?>
        </div>

        <!-- Pagination -->
        <!-- Modern Pagination UI -->
        <?php if ($total_rows > 0): ?>
            <div class="modern-pagination-wrapper" id="paginationWrapper">
                <!-- Header Section -->
                <div class="pagination-header">
                    <div class="pagination-stats">
                        <div class="stats-card">
                            <i class="fas fa-list"></i>
                            <span>รายการทั้งหมด</span>
                        </div>
                        <div class="stats-numbers">
                            แสดง <span class="highlight"><?= number_format(($current_page - 1) * $per_page + 1) ?></span> -
                            <span class="highlight"><?= number_format(min($current_page * $per_page, $total_rows)) ?></span>
                            จาก <span class="highlight"><?= number_format($total_rows) ?></span> รายการ
                        </div>
                    </div>

                    <div class="pagination-actions">
                        <div class="quick-jump">
                            <label>ไปหน้า:</label>
                            <input type="number" min="1" max="<?= ceil($total_rows / $per_page) ?>"
                                placeholder="<?= $current_page ?>" id="quickPageInput">
                            <button onclick="quickNavigate()">
                                <i class="fas fa-arrow-right"></i>
                            </button>
                        </div>

                        <button class="refresh-btn" onclick="refreshPage()">
                            <i class="fas fa-sync-alt"></i>
                            <span>รีเฟรช</span>
                        </button>
                    </div>
                </div>

                <!-- Main Pagination -->
                <div class="pagination-main">
                    <div class="pagination-nav">
                        <!-- Pagination buttons will be generated by JavaScript -->
                    </div>
                </div>

                <!-- Footer Section -->
                <div class="pagination-footer">
                    <div class="page-size-selector">
                        <span>แสดง:</span>
                        <select onchange="changePageSize(this.value)">
                            <option value="10" <?= ($per_page == 10) ? 'selected' : '' ?>>10 รายการ</option>
                            <option value="25" <?= ($per_page == 25) ? 'selected' : '' ?>>25 รายการ</option>
                            <option value="50" <?= ($per_page == 50) ? 'selected' : '' ?>>50 รายการ</option>
                            <option value="100" <?= ($per_page == 100) ? 'selected' : '' ?>>100 รายการ</option>
                        </select>
                        <span>ต่อหน้า</span>
                    </div>

                    <div class="pagination-summary">
                        หน้า <strong><?= $current_page ?></strong> จาก <strong><?= ceil($total_rows / $per_page) ?></strong>
                        หน้า
                    </div>
                </div>
            </div>
        <?php endif; ?>




        <!-- Enhanced Status Update Modal -->
        <div class="modal fade" id="queueStatusUpdateModal" tabindex="-1" aria-hidden="true">
            <div class="modal-dialog status-modal-enhanced">
                <div class="modal-content">
                    <div class="modal-header bg-gradient-primary text-white">
                        <h5 class="modal-title">
                            <i class="fas fa-sync-alt me-2"></i>ยืนยันการเปลี่ยนสถานะคิว
                        </h5>
                        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                    </div>
                    <div class="modal-body">
                        <form id="enhancedQueueStatusForm" enctype="multipart/form-data">
                            <input type="hidden" id="modalQueueId" name="queue_id">
                            <input type="hidden" id="modalNewQueueStatus" name="new_status">

                            <!-- Section 1: Status Change Summary -->
                            <div class="modal-section">
                                <h6 class="section-title">
                                    <i class="fas fa-info-circle"></i>
                                    ข้อมูลการเปลี่ยนสถานะ
                                </h6>
                                <div class="status-change-summary">
                                    <div class="row">
                                        <div class="col-md-6">
                                            <label class="form-label">หมายเลขคิว:</label>
                                            <div class="fw-bold text-primary" id="modalQueueIdDisplay">#</div>
                                        </div>
                                        <div class="col-md-6">
                                            <label class="form-label">วันที่จอง:</label>
                                            <div id="modalQueueDate">-</div>
                                        </div>
                                    </div>

                                    <div class="status-arrow">
                                        <span class="current-status" id="modalCurrentQueueStatus">สถานะปัจจุบัน</span>
                                        <i class="fas fa-arrow-right mx-3"></i>
                                        <span class="new-status" id="modalNewQueueStatusDisplay">สถานะใหม่</span>
                                    </div>
                                </div>
                            </div>

                            <!-- Section 2: Note/Comment -->
                            <div class="modal-section note-section">
                                <h6 class="section-title">
                                    <i class="fas fa-sticky-note"></i>
                                    หมายเหตุการดำเนินงาน
                                </h6>
                                <div class="mb-3">
                                    <label class="form-label">เพิ่มหมายเหตุ:</label>
                                    <textarea class="form-control" id="modalQueueStatusNote" name="comment" rows="4"
                                        maxlength="250"
                                        placeholder="ระบุรายละเอียดการดำเนินการ ปัญหาที่พบ หรือข้อมูลเพิ่มเติม..."></textarea>
                                    <div class="character-counter">
                                        <span id="queueNoteCharCount">0</span>/250 ตัวอักษร
                                    </div>
                                </div>
                            </div>

                            <!-- Section 3: Image Upload -->
                            <div class="modal-section">
                                <h6 class="section-title">
                                    <i class="fas fa-images"></i>
                                    รูปภาพประกอบ
                                </h6>

                                <div class="image-upload-container" id="queueImageUploadArea">
                                    <div class="upload-icon">
                                        <i class="fas fa-cloud-upload-alt"></i>
                                    </div>
                                    <p class="mb-2"><strong>คลิกเพื่อเลือกรูปภาพ</strong> หรือลากไฟล์มาวางที่นี่</p>
                                    <p class="upload-limit-info">
                                        รองรับไฟล์: JPG, PNG, GIF, PDF, DOC (ขนาดไม่เกิน 10MB ต่อไฟล์)
                                        <br>จำนวนไม่เกิน 5 ไฟล์
                                        <br><small class="text-success">💡
                                            รูปภาพจะถูกบีบอัดอัตโนมัติเพื่อประหยัดพื้นที่</small>
                                    </p>
                                    <input type="file" id="queueStatusImages" name="status_images[]" multiple
                                        accept="image/*,.pdf,.doc,.docx" class="file-input-hidden">
                                </div>

                                <div class="compression-processing" id="queueCompressionProcessing">
                                    <div class="spinner"></div>
                                    กำลังบีบอัดไฟล์...
                                </div>

                                <div class="upload-progress" id="queueUploadProgress">
                                    <div class="progress">
                                        <div class="progress-bar" id="queueProgressBar" style="width: 0%"></div>
                                    </div>
                                    <div class="text-center mt-2">
                                        <small id="queueProgressText">กำลังอัปโหลด...</small>
                                    </div>
                                </div>

                                <div class="image-preview-container" id="queueImagePreviewContainer"></div>

                                <div class="image-upload-stats">
                                    <span>ไฟล์ที่เลือก: <span id="queueImageCount">0</span>/5</span>
                                    <span>ขนาดรวม: <span id="queueTotalSize">0 KB</span></span>
                                    <span class="text-success">ประหยัด: <span id="queueTotalSavings">0 KB</span></span>
                                </div>
                            </div>
                        </form>
                    </div>

                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                            <i class="fas fa-times me-1"></i>ยกเลิก
                        </button>
                        <button type="button" class="btn btn-primary" id="confirmQueueStatusUpdate">
                            <i class="fas fa-check me-1"></i>ยืนยันการเปลี่ยนสถานะ
                        </button>
                    </div>
                </div>
            </div>
        </div>

        <!-- Modal สำหรับรับเรื่องพิจารณา -->
        <div class="modal fade" id="receiveCaseModal" tabindex="-1" aria-hidden="true">
            <div class="modal-dialog">
                <div class="modal-content">
                    <div class="modal-header bg-primary text-white">
                        <h5 class="modal-title">
                            <i class="fas fa-file-import me-2"></i>รับเรื่องพิจารณา
                        </h5>
                        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                    </div>
                    <div class="modal-body">
                        <form id="receiveCaseForm">
                            <input type="hidden" id="receiveCaseQueueId" name="queue_id">
                            <input type="hidden" id="receiveCaseCurrentStatus" name="current_status">
                            <input type="hidden" id="receiveCaseNewStatus" name="new_status" value="รับเรื่องพิจารณา">

                            <div class="alert alert-info">
                                <i class="fas fa-info-circle me-2"></i>
                                คิว <strong id="receiveCaseQueueNumber">#</strong> จะถูกเปลี่ยนสถานะเป็น
                                <strong>"รับเรื่องพิจารณา"</strong>
                            </div>

                            <div class="mb-3">
                                <label class="form-label">หมายเหตุการรับเรื่อง (ถ้ามี):</label>
                                <textarea class="form-control" id="receiveCaseComment" name="comment" rows="3"
                                    placeholder="ระบุข้อมูลเพิ่มเติมหรือคำแนะนำ..."></textarea>
                            </div>
                        </form>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">ยกเลิก</button>
                        <button type="button" class="btn btn-primary" id="receiveCaseBtn">
                            <i class="fas fa-file-import me-1"></i>รับเรื่องพิจารณา
                        </button>
                    </div>
                </div>
            </div>
        </div>

        <!-- Modal สำหรับคิวได้รับการยืนยัน -->
        <div class="modal fade" id="confirmBookingModal" tabindex="-1" aria-hidden="true">
            <div class="modal-dialog">
                <div class="modal-content">
                    <div class="modal-header bg-success text-white">
                        <h5 class="modal-title">
                            <i class="fas fa-calendar-check me-2"></i>คิวได้รับการยืนยัน
                        </h5>
                        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                    </div>
                    <div class="modal-body">
                        <form id="confirmBookingForm">
                            <input type="hidden" id="confirmQueueId" name="queue_id">
                            <input type="hidden" id="confirmCurrentStatus" name="current_status">
                            <input type="hidden" id="confirmNewStatus" name="new_status" value="คิวได้รับการยืนยัน">

                            <div class="alert alert-success">
                                <i class="fas fa-check-circle me-2"></i>
                                คิว <strong id="confirmQueueNumber">#</strong> จะถูกเปลี่ยนสถานะเป็น
                                <strong>"คิวได้รับการยืนยัน"</strong>
                            </div>

                            <div class="mb-3">
                                <label class="form-label">ข้อความแจ้งผู้จอง:</label>
                                <textarea class="form-control" id="confirmComment" name="comment" rows="3"
                                    placeholder="แจ้งรายละเอียดการนัดหมาย เช่น เวลา สถานที่ เอกสารที่ต้องเตรียม..."></textarea>
                            </div>
                        </form>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">ยกเลิก</button>
                        <button type="button" class="btn btn-success" id="confirmBookingBtn">
                            <i class="fas fa-calendar-check me-1"></i>ยืนยันคิว
                        </button>
                    </div>
                </div>
            </div>
        </div>

        <!-- Modal สำหรับเสร็จสิ้น -->
        <div class="modal fade" id="completeQueueModal" tabindex="-1" aria-hidden="true">
            <div class="modal-dialog">
                <div class="modal-content">
                    <div class="modal-header bg-success text-white">
                        <h5 class="modal-title">
                            <i class="fas fa-check-double me-2"></i>เสร็จสิ้น
                        </h5>
                        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                    </div>
                    <div class="modal-body">
                        <form id="completeQueueForm">
                            <input type="hidden" id="completeQueueId" name="queue_id">
                            <input type="hidden" id="completeCurrentStatus" name="current_status">
                            <input type="hidden" id="completeNewStatus" name="new_status" value="เสร็จสิ้น">

                            <div class="alert alert-success">
                                <i class="fas fa-check-circle me-2"></i>
                                คิว <strong id="completeQueueNumber">#</strong> จะถูกเปลี่ยนสถานะเป็น
                                <strong>"เสร็จสิ้น"</strong>
                            </div>

                            <div class="mb-3">
                                <label class="form-label">สรุปผลการดำเนินงาน:</label>
                                <textarea class="form-control" id="completeComment" name="comment" rows="3"
                                    placeholder="สรุปผลการดำเนินงาน หรือข้อมูลที่ผู้จองควรทราบ..."></textarea>
                            </div>
                        </form>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">ยกเลิก</button>
                        <button type="button" class="btn btn-success" id="completeQueueBtn">
                            <i class="fas fa-check-double me-1"></i>เสร็จสิ้น
                        </button>
                    </div>
                </div>
            </div>
        </div>

        <!-- Modal สำหรับยกเลิกคิว -->
        <div class="modal fade" id="cancelQueueModal" tabindex="-1" aria-hidden="true">
            <div class="modal-dialog">
                <div class="modal-content">
                    <div class="modal-header bg-danger text-white">
                        <h5 class="modal-title">
                            <i class="fas fa-times-circle me-2"></i>ยกเลิกคิว
                        </h5>
                        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                    </div>
                    <div class="modal-body">
                        <form id="cancelQueueForm">
                            <input type="hidden" id="cancelQueueId" name="queue_id">
                            <input type="hidden" id="cancelCurrentStatus" name="current_status">
                            <input type="hidden" id="cancelNewStatus" name="new_status" value="ยกเลิก">

                            <div class="alert alert-warning">
                                <i class="fas fa-exclamation-triangle me-2"></i>
                                คิว <strong id="cancelQueueNumber">#</strong> จะถูกยกเลิก
                                การดำเนินการนี้ไม่สามารถย้อนกลับได้
                            </div>

                            <div class="mb-3">
                                <label class="form-label">เหตุผลการยกเลิก <span class="text-danger">*</span>:</label>
                                <textarea class="form-control" id="cancelComment" name="comment" rows="3" required
                                    placeholder="ระบุเหตุผลการยกเลิก เช่น ข้อมูลไม่ครบถ้วน, ไม่อยู่ในเขตบริการ, อื่นๆ..."></textarea>
                            </div>
                        </form>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">ย้อนกลับ</button>
                        <button type="button" class="btn btn-danger" id="cancelQueueBtn">
                            <i class="fas fa-times-circle me-1"></i>ยืนยันการยกเลิก
                        </button>
                    </div>
                </div>
            </div>
        </div>

        <!-- Modal สำหรับลบคิว -->
        <div class="modal fade" id="deleteQueueModal" tabindex="-1" aria-hidden="true">
            <div class="modal-dialog">
                <div class="modal-content">
                    <div class="modal-header bg-danger text-white">
                        <h5 class="modal-title">
                            <i class="fas fa-trash me-2"></i>ลบคิว
                        </h5>
                        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                    </div>
                    <div class="modal-body">
                        <form id="deleteQueueForm">
                            <input type="hidden" id="deleteQueueId" name="queue_id">

                            <div class="alert alert-danger">
                                <i class="fas fa-exclamation-triangle me-2"></i>
                                <strong>⚠️ คำเตือน:</strong> คิว <strong id="deleteQueueNumber">#</strong>
                                จะถูกลบออกจากระบบถาวร<br>
                                หัวข้อ: <strong id="deleteQueueTopic"></strong><br>
                                <small class="text-muted">การดำเนินการนี้ไม่สามารถย้อนกลับได้
                                    และจะลบข้อมูลทั้งหมดที่เกี่ยวข้อง</small>
                            </div>

                            <div class="mb-3">
                                <label class="form-label">เหตุผลการลบ <span class="text-danger">*</span>:</label>
                                <textarea class="form-control" id="deleteQueueReason" name="delete_reason" rows="4"
                                    required
                                    placeholder="ระบุเหตุผลการลบ เช่น ข้อมูลผิดพลาด, คิวซ้ำ, ขอลบโดยผู้ใช้, อื่นๆ..."></textarea>
                                <small class="text-muted">ต้องมีอย่างน้อย 5 ตัวอักษร</small>
                            </div>

                            <div class="mb-3">
                                <div class="form-check">
                                    <input class="form-check-input" type="checkbox" id="confirmDelete" required>
                                    <label class="form-check-label text-danger" for="confirmDelete">
                                        <strong>ฉันเข้าใจและยืนยันการลบข้อมูลถาวร</strong>
                                    </label>
                                </div>
                            </div>
                        </form>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">ยกเลิก</button>
                        <button type="button" class="btn btn-danger" id="confirmDeleteBtn" disabled>
                            <i class="fas fa-trash me-1"></i>ยืนยันการลบ
                        </button>
                    </div>
                </div>
            </div>
        </div>

        <!-- Image Preview Modal -->
        <div class="image-preview-modal" id="imagePreviewModal">
            <div class="image-preview-container">
                <img src="" alt="Image Preview" id="previewImage">
                <button class="image-preview-close" onclick="closeImagePreview()">
                    <i class="fas fa-times"></i>
                </button>
                <div class="image-preview-info">
                    <div class="image-preview-filename" id="previewFilename"></div>
                    <div class="image-preview-size" id="previewFileSize"></div>
                </div>
            </div>
        </div>

        <!-- SweetAlert2 -->
        <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

        <script>


            // ===================================================================
            // *** IMAGE PREVIEW MODAL FUNCTIONS ***
            // ===================================================================

            function showImagePreview(imageUrl, fileName, fileSize) {
                const modal = document.getElementById('imagePreviewModal');
                const img = document.getElementById('previewImage');
                const filenameEl = document.getElementById('previewFilename');
                const fileSizeEl = document.getElementById('previewFileSize');

                if (modal && img && filenameEl && fileSizeEl) {
                    img.src = imageUrl;
                    filenameEl.textContent = fileName;
                    fileSizeEl.textContent = fileSize;
                    modal.classList.add('show');
                } else {
                    console.error('Image preview modal elements not found!');
                }
            }

            function closeImagePreview() {
                const modal = document.getElementById('imagePreviewModal');
                if (modal) {
                    modal.classList.remove('show');
                }
            }

            // Close modal when clicking on the background
            const modal = document.getElementById('imagePreviewModal');
            if (modal) {
                modal.addEventListener('click', function (e) {
                    if (e.target === modal) {
                        closeImagePreview();
                    }
                });
            }

            // ===================================================================
            // *** HELPER FUNCTIONS - การตรวจสอบความปลอดภัย ***
            // ===================================================================

            function safeGetElement(id, description = '') {
                const element = document.getElementById(id);
                if (!element) {
                    console.warn(`Element with ID '${id}' not found${description ? ': ' + description : ''}`);
                }
                return element;
            }

            function safeSetContent(elementId, content, property = 'textContent') {
                const element = safeGetElement(elementId);
                if (element) {
                    element[property] = content;
                    return true;
                }
                return false;
            }

            function safeSetValue(elementId, value) {
                const element = safeGetElement(elementId);
                if (element) {
                    element.value = value;
                    return true;
                }
                return false;
            }

            function safeAddEventListener(elementId, event, handler, description = '') {
                const element = safeGetElement(elementId, description);
                if (element) {
                    element.addEventListener(event, handler);
                    return true;
                }
                return false;
            }

            // ===================================================================
            // *** QUEUE MANAGER - Queue specific functionality ***
            // ===================================================================

            (function () {
                'use strict';

                window.queueManager = window.queueManager || {};

                window.queueManager = {
                    config: {
                        updateStatusUrl: '<?= site_url("Queue/update_queue_status_with_images") ?>',
                        deleteQueueUrl: '<?= site_url("Queue/delete_queue") ?>',
                        queueDetailUrl: '<?= site_url("Queue/queue_detail") ?>'
                    },

                    permissions: <?= json_encode($permissions) ?>,

                    isReady: false,

                    init: function () {
                        this.isReady = true;
                        this.bindEvents();
                        console.log('✅ Queue Manager initialized with permissions:', this.permissions);
                    },

                    bindEvents: function () {
                        var self = this;

                        // ESC key to close modals
                        $(document).on('keydown', function (e) {
                            if (e.key === 'Escape') {
                                $('.modal.show').modal('hide');
                            }
                        });

                        console.log('✅ Queue events bound successfully');
                    },

                    showEnhancedModal: function (queueId, currentStatus, newStatus, queueData) {
                        if (!this.permissions.can_update_status) {
                            this.showAlert('คุณไม่มีสิทธิ์ในการอัพเดทสถานะคิว', 'error');
                            return;
                        }

                        queueData = queueData || {};

                        if (typeof QueueStatusUpdateModal !== 'undefined' && QueueStatusUpdateModal) {
                            QueueStatusUpdateModal.show(queueId, currentStatus, newStatus, queueData);
                        } else {
                            console.warn('QueueStatusUpdateModal not available');
                            this.quickUpdateStatus(queueId, newStatus);
                        }
                    },

                    quickUpdateStatus: function (queueId, newStatus) {
                        if (!this.permissions.can_update_status) {
                            this.showAlert('คุณไม่มีสิทธิ์ในการอัพเดทสถานะคิว', 'error');
                            return;
                        }

                        if (!queueId || !newStatus) {
                            this.showAlert('ข้อมูลไม่ถูกต้อง', 'error');
                            return;
                        }

                        var self = this;
                        this.showAlert({
                            title: 'ยืนยันการเปลี่ยนสถานะ',
                            text: 'คุณต้องการเปลี่ยนสถานะคิว #' + queueId + ' เป็น "' + newStatus + '" หรือไม่?',
                            icon: 'question',
                            showCancelButton: true,
                            confirmButtonText: 'ยืนยัน',
                            cancelButtonText: 'ยกเลิก'
                        }).then(function (result) {
                            if (result.isConfirmed) {
                                self.performQuickUpdate(queueId, newStatus);
                            }
                        });
                    },

                    performQuickUpdate: function (queueId, newStatus) {
                        var self = this;

                        const formData = {
                            queue_id: queueId,
                            new_status: newStatus,
                            status_note: 'อัปเดตเป็น "' + newStatus + '" ผ่านระบบ Workflow Management'
                        };

                        this.showAlert({
                            title: 'กำลังอัปเดตสถานะ...',
                            text: 'คิว #' + queueId,
                            icon: 'info',
                            allowOutsideClick: false,
                            allowEscapeKey: false,
                            showConfirmButton: false,
                            didOpen: function () {
                                if (typeof Swal !== 'undefined') {
                                    Swal.showLoading();
                                }
                            }
                        });

                        $.ajax({
                            url: this.config.updateStatusUrl,
                            type: 'POST',
                            data: formData,
                            dataType: 'json',
                            success: function (response) {
                                if (response.success) {
                                    self.showAlert({
                                        title: 'อัปเดตสำเร็จ!',
                                        text: response.message,
                                        icon: 'success',
                                        timer: 2000,
                                        showConfirmButton: false
                                    }).then(function () {
                                        location.reload();
                                    });
                                } else {
                                    self.showAlert(response.message, 'error');
                                }
                            },
                            error: function (xhr, status, error) {
                                console.error('AJAX Error:', error);
                                self.showAlert('เกิดข้อผิดพลาดในการเชื่อมต่อ', 'error');
                            }
                        });
                    },

                    showAlert: function (message, type) {
                        if (typeof Swal !== 'undefined') {
                            if (typeof message === 'object') {
                                return Swal.fire(message);
                            } else {
                                return Swal.fire({
                                    icon: type === 'error' ? 'error' : 'success',
                                    title: type === 'error' ? 'เกิดข้อผิดพลาด' : 'สำเร็จ',
                                    text: message,
                                    timer: type === 'success' ? 2000 : null,
                                    showConfirmButton: type === 'error'
                                });
                            }
                        } else {
                            alert(message);
                            return Promise.resolve({ isConfirmed: true });
                        }
                    }
                };

                // ===================================================================
                // *** DELETE QUEUE FUNCTIONS ***
                // ===================================================================

                window.showDeleteModal = function (queueId, queueTopic) {
                    // ตรวจสอบสิทธิ์
                    if (!window.queueManager.permissions.can_delete) {
                        window.queueManager.showAlert('คุณไม่มีสิทธิ์ในการลบคิว', 'error');
                        return;
                    }

                    const deleteQueueIdElement = safeGetElement('deleteQueueId');
                    const deleteQueueNumberElement = safeGetElement('deleteQueueNumber');
                    const deleteQueueTopicElement = safeGetElement('deleteQueueTopic');
                    const deleteQueueReasonElement = safeGetElement('deleteQueueReason');
                    const confirmDeleteElement = safeGetElement('confirmDelete');
                    const modalElement = safeGetElement('deleteQueueModal');

                    if (!deleteQueueIdElement || !deleteQueueNumberElement || !deleteQueueTopicElement ||
                        !deleteQueueReasonElement || !confirmDeleteElement || !modalElement) {
                        console.error('Required delete modal elements not found');
                        return;
                    }

                    deleteQueueIdElement.value = queueId;
                    deleteQueueNumberElement.textContent = '#' + queueId;
                    deleteQueueTopicElement.textContent = queueTopic;
                    deleteQueueReasonElement.value = '';
                    confirmDeleteElement.checked = false;

                    // อัปเดตสถานะปุ่ม
                    updateDeleteButtonState();

                    try {
                        const modal = new bootstrap.Modal(modalElement);
                        modal.show();
                    } catch (error) {
                        console.error('Error showing delete modal:', error);
                    }
                };

                function updateDeleteButtonState() {
                    const reasonElement = safeGetElement('deleteQueueReason');
                    const confirmElement = safeGetElement('confirmDelete');
                    const buttonElement = safeGetElement('confirmDeleteBtn');

                    if (reasonElement && confirmElement && buttonElement) {
                        const reasonValid = reasonElement.value.trim().length >= 5;
                        const confirmed = confirmElement.checked;

                        buttonElement.disabled = !(reasonValid && confirmed);
                    }
                }

                function deleteQueue() {
                    const queueId = safeGetElement('deleteQueueId');
                    const reason = safeGetElement('deleteQueueReason');
                    const confirmCheckbox = safeGetElement('confirmDelete');

                    if (!queueId || !reason || !confirmCheckbox) {
                        console.error('Required delete form elements not found');
                        return;
                    }

                    if (reason.value.trim().length < 5) {
                        window.queueManager.showAlert('เหตุผลการลบต้องมีอย่างน้อย 5 ตัวอักษร', 'error');
                        return;
                    }

                    if (!confirmCheckbox.checked) {
                        window.queueManager.showAlert('กรุณายืนยันการลบ', 'error');
                        return;
                    }

                    const formData = {
                        queue_id: queueId.value,
                        delete_reason: reason.value.trim()
                    };

                    // แสดง loading
                    const deleteBtn = safeGetElement('confirmDeleteBtn');
                    let originalText = '';
                    if (deleteBtn) {
                        originalText = deleteBtn.innerHTML;
                        deleteBtn.disabled = true;
                        deleteBtn.innerHTML = '<i class="fas fa-spinner fa-spin me-1"></i>กำลังลบ...';
                    }

                    fetch(window.queueManager.config.deleteQueueUrl, {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/x-www-form-urlencoded',
                        },
                        body: new URLSearchParams(formData)
                    })
                        .then(response => response.json())
                        .then(data => {
                            if (data.success) {
                                window.queueManager.showAlert({
                                    title: 'ลบสำเร็จ!',
                                    text: data.message,
                                    icon: 'success',
                                    timer: 2000,
                                    showConfirmButton: false
                                }).then(() => {
                                    const modalElement = safeGetElement('deleteQueueModal');
                                    if (modalElement) {
                                        const modalInstance = bootstrap.Modal.getInstance(modalElement);
                                        if (modalInstance) {
                                            modalInstance.hide();
                                        }
                                    }
                                    location.reload();
                                });
                            } else {
                                window.queueManager.showAlert(data.message, 'error');
                            }
                        })
                        .catch(error => {
                            console.error('Delete error:', error);
                            window.queueManager.showAlert('เกิดข้อผิดพลาดในการเชื่อมต่อ', 'error');
                        })
                        .finally(() => {
                            // คืนค่าปุ่ม
                            if (deleteBtn) {
                                deleteBtn.disabled = false;
                                deleteBtn.innerHTML = originalText;
                            }
                        });
                }

                // ===================================================================
                // *** Enhanced Queue Status Update Modal ***
                // ===================================================================

                window.QueueStatusUpdateModal = {
                    maxImages: 5,
                    maxFileSize: 10 * 1024 * 1024, // 10MB
                    allowedTypes: ['image/jpeg', 'image/png', 'image/gif', 'application/pdf', 'application/msword'],
                    selectedFiles: [],

                    compressionSettings: {
                        quality: 0.8,
                        maxWidth: 1200,
                        maxHeight: 1200,
                        enabled: true
                    },

                    init: function () {
                        this.bindEvents();
                        console.log('✅ Enhanced Queue Status Update Modal initialized');
                    },

                    bindEvents: function () {
                        var self = this;

                        // Image upload area click
                        const uploadArea = safeGetElement('queueImageUploadArea');
                        if (uploadArea) {
                            uploadArea.addEventListener('click', function () {
                                const fileInput = safeGetElement('queueStatusImages');
                                if (fileInput) {
                                    fileInput.click();
                                }
                            });
                        }

                        // File input change
                        const fileInput = safeGetElement('queueStatusImages');
                        if (fileInput) {
                            fileInput.addEventListener('change', function (e) {
                                self.handleFileSelect(e.target.files);
                            });
                        }

                        // Note character counter
                        const noteInput = safeGetElement('modalQueueStatusNote');
                        if (noteInput) {
                            noteInput.addEventListener('input', function (e) {
                                self.updateCharacterCounter(e.target);
                            });
                        }

                        // Confirm button
                        const confirmBtn = safeGetElement('confirmQueueStatusUpdate');
                        if (confirmBtn) {
                            confirmBtn.addEventListener('click', function () {
                                self.submitStatusUpdate();
                            });
                        }
                    },

                    show: function (queueId, currentStatus, newStatus, queueData) {
                        queueData = queueData || {};

                        this.resetForm();

                        safeSetValue('modalQueueId', queueId);
                        safeSetValue('modalNewQueueStatus', newStatus);
                        safeSetContent('modalQueueIdDisplay', '#' + queueId);
                        safeSetContent('modalCurrentQueueStatus', currentStatus);
                        safeSetContent('modalNewQueueStatusDisplay', newStatus);

                        if (queueData.date) {
                            safeSetContent('modalQueueDate', queueData.date);
                        }

                        const modalElement = safeGetElement('queueStatusUpdateModal');
                        if (modalElement) {
                            try {
                                const modal = new bootstrap.Modal(modalElement);
                                modal.show();
                            } catch (error) {
                                console.error('Error showing enhanced modal:', error);
                            }
                        }
                    },

                    handleFileSelect: function (files) {
                        var fileArray = Array.from(files);

                        if (this.selectedFiles.length + fileArray.length > this.maxImages) {
                            this.showAlert('สามารถเลือกได้สูงสุด ' + this.maxImages + ' ไฟล์เท่านั้น', 'warning');
                            return;
                        }

                        this.showCompressionProcessing(true);

                        var self = this;
                        var processedCount = 0;

                        fileArray.forEach(function (file) {
                            if (self.validateFile(file)) {
                                if (file.type.startsWith('image/')) {
                                    self.compressAndAddFile(file, function () {
                                        processedCount++;
                                        if (processedCount === fileArray.length) {
                                            self.showCompressionProcessing(false);
                                            self.updateStats();
                                        }
                                    });
                                } else {
                                    // Non-image files, add directly
                                    self.selectedFiles.push(file);
                                    self.createFilePreview(file, false);
                                    processedCount++;
                                    if (processedCount === fileArray.length) {
                                        self.showCompressionProcessing(false);
                                        self.updateStats();
                                    }
                                }
                            } else {
                                processedCount++;
                                if (processedCount === fileArray.length) {
                                    self.showCompressionProcessing(false);
                                    self.updateStats();
                                }
                            }
                        });
                    },

                    validateFile: function (file) {
                        if (!this.allowedTypes.includes(file.type) && !file.type.startsWith('image/')) {
                            this.showAlert('ไฟล์ ' + file.name + ' ไม่รองรับ', 'error');
                            return false;
                        }

                        if (file.size > this.maxFileSize) {
                            this.showAlert('ไฟล์ ' + file.name + ' มีขนาดเกิน 10MB', 'error');
                            return false;
                        }

                        return true;
                    },

                    compressAndAddFile: function (file, callback) {
                        var self = this;

                        const canvas = document.createElement('canvas');
                        const ctx = canvas.getContext('2d');
                        const img = new Image();

                        img.onload = function () {
                            var dimensions = self.calculateNewDimensions(img.width, img.height);
                            canvas.width = dimensions.width;
                            canvas.height = dimensions.height;

                            ctx.drawImage(img, 0, 0, dimensions.width, dimensions.height);

                            canvas.toBlob(function (compressedBlob) {
                                if (compressedBlob) {
                                    const compressedFile = new File([compressedBlob], file.name, {
                                        type: file.type,
                                        lastModified: file.lastModified
                                    });

                                    compressedFile.originalSize = file.size;
                                    self.selectedFiles.push(compressedFile);
                                    self.createFilePreview(compressedFile, true);
                                } else {
                                    self.selectedFiles.push(file);
                                    self.createFilePreview(file, false);
                                }
                                callback();
                            }, file.type, self.compressionSettings.quality);
                        };

                        img.onerror = function () {
                            self.selectedFiles.push(file);
                            self.createFilePreview(file, false);
                            callback();
                        };

                        const reader = new FileReader();
                        reader.onload = function (e) {
                            img.src = e.target.result;
                        };
                        reader.readAsDataURL(file);
                    },

                    calculateNewDimensions: function (originalWidth, originalHeight) {
                        const maxWidth = this.compressionSettings.maxWidth;
                        const maxHeight = this.compressionSettings.maxHeight;

                        let width = originalWidth;
                        let height = originalHeight;

                        if (width > maxWidth) {
                            height = (height * maxWidth) / width;
                            width = maxWidth;
                        }

                        if (height > maxHeight) {
                            width = (width * maxHeight) / height;
                            height = maxHeight;
                        }

                        return { width: Math.round(width), height: Math.round(height) };
                    },

                    createFilePreview: function (file, isCompressed) {
                        const container = safeGetElement('queueImagePreviewContainer');
                        if (!container) return;

                        const fileIndex = this.selectedFiles.length - 1;

                        const previewDiv = document.createElement('div');
                        previewDiv.className = 'image-preview-item' + (isCompressed ? ' compressed' : '');
                        previewDiv.dataset.fileIndex = fileIndex;

                        if (file.type.startsWith('image/')) {
                            const img = document.createElement('img');
                            const reader = new FileReader();
                            reader.onload = function (e) {
                                img.src = e.target.result;
                                img.alt = file.name;
                            };
                            reader.readAsDataURL(file);
                            previewDiv.appendChild(img);
                        } else {
                            // Non-image file preview
                            const fileIcon = document.createElement('div');
                            fileIcon.className = 'file-preview';
                            fileIcon.innerHTML = '<i class="fas fa-file"></i><br><small>' + file.name + '</small>';
                            previewDiv.appendChild(fileIcon);
                        }

                        const removeBtn = document.createElement('button');
                        removeBtn.className = 'image-remove-btn';
                        removeBtn.innerHTML = '<i class="fas fa-times"></i>';
                        removeBtn.title = 'ลบไฟล์นี้';
                        var self = this;
                        removeBtn.addEventListener('click', function (e) {
                            e.stopPropagation();
                            self.removeFile(fileIndex);
                        });

                        if (isCompressed) {
                            const badge = document.createElement('div');
                            badge.className = 'compression-badge';
                            badge.textContent = 'บีบอัดแล้ว';
                            previewDiv.appendChild(badge);
                        }

                        previewDiv.appendChild(removeBtn);
                        container.appendChild(previewDiv);
                    },

                    removeFile: function (fileIndex) {
                        this.selectedFiles.splice(fileIndex, 1);

                        const previewItem = document.querySelector('[data-file-index="' + fileIndex + '"]');
                        if (previewItem) {
                            previewItem.remove();
                        }

                        this.updatePreviewIndexes();
                        this.updateStats();
                    },

                    updatePreviewIndexes: function () {
                        const previews = document.querySelectorAll('.image-preview-item');
                        previews.forEach(function (preview, index) {
                            preview.dataset.fileIndex = index;
                        });
                    },

                    updateStats: function () {
                        const count = this.selectedFiles.length;
                        var totalSize = 0;
                        var totalSavings = 0;

                        for (var i = 0; i < this.selectedFiles.length; i++) {
                            const file = this.selectedFiles[i];
                            totalSize += file.size;
                            if (file.originalSize) {
                                totalSavings += (file.originalSize - file.size);
                            }
                        }

                        safeSetContent('queueImageCount', count);
                        safeSetContent('queueTotalSize', this.formatFileSize(totalSize));
                        safeSetContent('queueTotalSavings', this.formatFileSize(totalSavings));
                    },

                    updateCharacterCounter: function (textarea) {
                        const current = textarea.value.length;
                        const max = 250;
                        const counter = safeGetElement('queueNoteCharCount');

                        if (counter) {
                            const counterDiv = counter.parentElement;

                            counter.textContent = current;

                            counterDiv.className = 'character-counter';
                            if (current > max * 0.9) {
                                counterDiv.classList.add('danger');
                            } else if (current > max * 0.7) {
                                counterDiv.classList.add('warning');
                            }
                        }
                    },

                    showCompressionProcessing: function (show) {
                        const element = safeGetElement('queueCompressionProcessing');
                        if (element) {
                            if (show) {
                                element.classList.add('show');
                            } else {
                                element.classList.remove('show');
                            }
                        }
                    },

                    formatFileSize: function (bytes) {
                        if (bytes === 0) return '0 KB';
                        const k = 1024;
                        const sizes = ['B', 'KB', 'MB'];
                        const i = Math.floor(Math.log(bytes) / Math.log(k));
                        return parseFloat((bytes / Math.pow(k, i)).toFixed(1)) + ' ' + sizes[i];
                    },

                    resetForm: function () {
                        const form = safeGetElement('enhancedQueueStatusForm');
                        if (form) {
                            form.reset();
                        }

                        const noteInput = safeGetElement('modalQueueStatusNote');
                        if (noteInput) {
                            noteInput.value = '';
                        }

                        this.selectedFiles = [];
                        const previewContainer = safeGetElement('queueImagePreviewContainer');
                        if (previewContainer) {
                            previewContainer.innerHTML = '';
                        }

                        safeSetContent('queueNoteCharCount', '0');
                        const charCounter = safeGetElement('queueNoteCharCount');
                        if (charCounter && charCounter.parentElement) {
                            charCounter.parentElement.className = 'character-counter';
                        }
                        this.updateStats();
                        this.showCompressionProcessing(false);
                    },

                    submitStatusUpdate: function () {
                        const queueId = safeGetElement('modalQueueId');
                        const newStatus = safeGetElement('modalNewQueueStatus');
                        const note = safeGetElement('modalQueueStatusNote');

                        if (!queueId || !newStatus || !note) {
                            console.error('Required form elements not found');
                            return;
                        }

                        const formData = new FormData();
                        formData.append('queue_id', queueId.value);
                        formData.append('new_status', newStatus.value);
                        formData.append('status_note', note.value.trim());

                        for (var i = 0; i < this.selectedFiles.length; i++) {
                            formData.append('status_images[]', this.selectedFiles[i]);
                        }

                        this.showLoading();
                        this.sendUpdateRequest(formData);
                    },

                    sendUpdateRequest: function (formData) {
                        var self = this;
                        fetch('<?= site_url("Queue/update_queue_status_with_images") ?>', {
                            method: 'POST',
                            body: formData
                        })
                            .then(function (response) { return response.json(); })
                            .then(function (data) {
                                self.hideLoading();

                                if (data.success) {
                                    self.showAlert({
                                        title: 'อัปเดตสำเร็จ!',
                                        text: data.message,
                                        icon: 'success',
                                        timer: 2000
                                    }).then(function () {
                                        const modalElement = safeGetElement('queueStatusUpdateModal');
                                        if (modalElement) {
                                            const modalInstance = bootstrap.Modal.getInstance(modalElement);
                                            if (modalInstance) {
                                                modalInstance.hide();
                                            }
                                        }
                                        location.reload();
                                    });
                                } else {
                                    self.showAlert(data.message, 'error');
                                }
                            })
                            .catch(function (error) {
                                self.hideLoading();
                                console.error('Error:', error);
                                self.showAlert('เกิดข้อผิดพลาดในการเชื่อมต่อ', 'error');
                            });
                    },

                    showLoading: function () {
                        const button = safeGetElement('confirmQueueStatusUpdate');
                        if (button) {
                            button.disabled = true;
                            button.innerHTML = '<i class="fas fa-spinner fa-spin me-1"></i>กำลังอัปเดต...';
                        }
                    },

                    hideLoading: function () {
                        const button = safeGetElement('confirmQueueStatusUpdate');
                        if (button) {
                            button.disabled = false;
                            button.innerHTML = '<i class="fas fa-check me-1"></i>ยืนยันการเปลี่ยนสถานะ';
                        }
                    },

                    showAlert: function (message, type) {
                        if (typeof Swal !== 'undefined') {
                            if (typeof message === 'object') {
                                return Swal.fire(message);
                            } else {
                                return Swal.fire({
                                    icon: type === 'error' ? 'error' : type === 'warning' ? 'warning' : 'success',
                                    title: type === 'error' ? 'เกิดข้อผิดพลาด' : type === 'warning' ? 'คำเตือน' : 'สำเร็จ',
                                    text: message,
                                    timer: type === 'success' ? 2000 : null,
                                    showConfirmButton: type !== 'success'
                                });
                            }
                        } else {
                            alert(message);
                            return Promise.resolve({ isConfirmed: true });
                        }
                    }
                };

                // ===================================================================
                // *** MODAL FUNCTIONS ***
                // ===================================================================

                // Modal สำหรับรับเรื่องพิจารณา
                window.showReceiveCaseModal = function (queueId, currentStatus, newStatus) {
                    if (!window.queueManager.permissions.can_update_status) {
                        window.queueManager.showAlert('คุณไม่มีสิทธิ์ในการอัพเดทสถานะคิว', 'error');
                        return;
                    }

                    const receiveCaseQueueIdElement = safeGetElement('receiveCaseQueueId');
                    const receiveCaseCurrentStatusElement = safeGetElement('receiveCaseCurrentStatus');
                    const receiveCaseNewStatusElement = safeGetElement('receiveCaseNewStatus');
                    const receiveCaseQueueNumberElement = safeGetElement('receiveCaseQueueNumber');
                    const receiveCaseCommentElement = safeGetElement('receiveCaseComment');
                    const modalElement = safeGetElement('receiveCaseModal');

                    if (!receiveCaseQueueIdElement || !receiveCaseCurrentStatusElement || !receiveCaseNewStatusElement ||
                        !receiveCaseQueueNumberElement || !receiveCaseCommentElement || !modalElement) {
                        console.error('Required receive case modal elements not found');
                        return;
                    }

                    receiveCaseQueueIdElement.value = queueId;
                    receiveCaseCurrentStatusElement.value = currentStatus;
                    receiveCaseNewStatusElement.value = newStatus;
                    receiveCaseQueueNumberElement.textContent = '#' + queueId;
                    receiveCaseCommentElement.value = '';

                    try {
                        const modal = new bootstrap.Modal(modalElement);
                        modal.show();
                    } catch (error) {
                        console.error('Error showing receive case modal:', error);
                    }
                };

                // Modal สำหรับคิวได้รับการยืนยัน
                window.showConfirmQueueModal = function (queueId, currentStatus, newStatus) {
                    if (!window.queueManager.permissions.can_update_status) {
                        window.queueManager.showAlert('คุณไม่มีสิทธิ์ในการอัพเดทสถานะคิว', 'error');
                        return;
                    }

                    const confirmQueueIdElement = safeGetElement('confirmQueueId');
                    const confirmCurrentStatusElement = safeGetElement('confirmCurrentStatus');
                    const confirmNewStatusElement = safeGetElement('confirmNewStatus');
                    const confirmQueueNumberElement = safeGetElement('confirmQueueNumber');
                    const confirmCommentElement = safeGetElement('confirmComment');
                    const modalElement = safeGetElement('confirmBookingModal');

                    if (!confirmQueueIdElement || !confirmCurrentStatusElement || !confirmNewStatusElement ||
                        !confirmQueueNumberElement || !confirmCommentElement || !modalElement) {
                        console.error('Required confirm queue modal elements not found');
                        return;
                    }

                    confirmQueueIdElement.value = queueId;
                    confirmCurrentStatusElement.value = currentStatus;
                    confirmNewStatusElement.value = newStatus;
                    confirmQueueNumberElement.textContent = '#' + queueId;
                    confirmCommentElement.value = '';

                    try {
                        const modal = new bootstrap.Modal(modalElement);
                        modal.show();
                    } catch (error) {
                        console.error('Error showing confirm queue modal:', error);
                    }
                };

                // Modal สำหรับเสร็จสิ้น
                window.showCompleteModal = function (queueId, currentStatus, newStatus) {
                    if (!window.queueManager.permissions.can_update_status) {
                        window.queueManager.showAlert('คุณไม่มีสิทธิ์ในการอัพเดทสถานะคิว', 'error');
                        return;
                    }

                    const completeQueueIdElement = safeGetElement('completeQueueId');
                    const completeCurrentStatusElement = safeGetElement('completeCurrentStatus');
                    const completeNewStatusElement = safeGetElement('completeNewStatus');
                    const completeQueueNumberElement = safeGetElement('completeQueueNumber');
                    const completeCommentElement = safeGetElement('completeComment');
                    const modalElement = safeGetElement('completeQueueModal');

                    if (!completeQueueIdElement || !completeCurrentStatusElement || !completeNewStatusElement ||
                        !completeQueueNumberElement || !completeCommentElement || !modalElement) {
                        console.error('Required complete modal elements not found');
                        return;
                    }

                    completeQueueIdElement.value = queueId;
                    completeCurrentStatusElement.value = currentStatus;
                    completeNewStatusElement.value = newStatus;
                    completeQueueNumberElement.textContent = '#' + queueId;
                    completeCommentElement.value = '';

                    try {
                        const modal = new bootstrap.Modal(modalElement);
                        modal.show();
                    } catch (error) {
                        console.error('Error showing complete modal:', error);
                    }
                };

                // Modal สำหรับยกเลิกคิว
                window.showCancelModal = function (queueId, currentStatus, newStatus) {
                    if (!window.queueManager.permissions.can_update_status) {
                        window.queueManager.showAlert('คุณไม่มีสิทธิ์ในการอัพเดทสถานะคิว', 'error');
                        return;
                    }

                    const cancelQueueIdElement = safeGetElement('cancelQueueId');
                    const cancelCurrentStatusElement = safeGetElement('cancelCurrentStatus');
                    const cancelNewStatusElement = safeGetElement('cancelNewStatus');
                    const cancelQueueNumberElement = safeGetElement('cancelQueueNumber');
                    const cancelCommentElement = safeGetElement('cancelComment');
                    const modalElement = safeGetElement('cancelQueueModal');

                    if (!cancelQueueIdElement || !cancelCurrentStatusElement || !cancelNewStatusElement ||
                        !cancelQueueNumberElement || !cancelCommentElement || !modalElement) {
                        console.error('Required cancel modal elements not found');
                        return;
                    }

                    cancelQueueIdElement.value = queueId;
                    cancelCurrentStatusElement.value = currentStatus;
                    cancelNewStatusElement.value = newStatus;
                    cancelQueueNumberElement.textContent = '#' + queueId;
                    cancelCommentElement.value = '';

                    try {
                        const modal = new bootstrap.Modal(modalElement);
                        modal.show();
                    } catch (error) {
                        console.error('Error showing cancel modal:', error);
                    }
                };

                // ===================================================================
                // *** UPDATE STATUS FUNCTION *** (แก้ไขแล้ว - เพิ่ม validation ทุก Modal)
                // ===================================================================

                function updateQueueStatus(formId, modalId) {
                    console.log('🔄 Updating queue status:', formId, modalId);

                    const form = safeGetElement(formId);

                    // ✅ เพิ่ม: ตรวจสอบ comment ก่อนส่ง สำหรับทุก Modal

                    // Modal: เสร็จสิ้น
                    if (formId === 'completeQueueForm') {
                        const commentField = document.getElementById('completeComment');
                        if (!commentField || commentField.value.trim() === '') {
                            Swal.fire({
                                title: 'กรุณากรอกข้อมูล',
                                text: 'กรุณากรอกสรุปผลการดำเนินงานก่อนเสร็จสิ้นคิว',
                                icon: 'warning'
                            });
                            return; // ❌ หยุดการส่งข้อมูล
                        }
                    }

                    // Modal: รับเรื่องพิจารณา
                    if (formId === 'receiveCaseForm') {
                        const commentField = document.getElementById('receiveCaseComment');
                        if (!commentField || commentField.value.trim() === '') {
                            Swal.fire({
                                title: 'กรุณากรอกข้อมูล',
                                text: 'กรุณากรอกหมายเหตุการรับเรื่องพิจารณา',
                                icon: 'warning'
                            });
                            return; // ❌ หยุดการส่งข้อมูล
                        }
                    }

                    // Modal: คิวได้รับการยืนยัน
                    if (formId === 'confirmBookingForm') {
                        const commentField = document.getElementById('confirmComment');
                        if (!commentField || commentField.value.trim() === '') {
                            Swal.fire({
                                title: 'กรุณากรอกข้อมูล',
                                text: 'กรุณากรอกข้อความแจ้งผู้จองก่อนยืนยันคิว',
                                icon: 'warning'
                            });
                            return; // ❌ หยุดการส่งข้อมูล
                        }
                    }

                    // Modal: ยกเลิกคิว (ตรวจสอบทั้งว่างและความยาว)
                    if (formId === 'cancelQueueForm') {
                        const commentField = document.getElementById('cancelComment');
                        if (commentField) {
                            const value = commentField.value.trim();
                            if (value === '') {
                                Swal.fire({
                                    title: 'กรุณากรอกข้อมูล',
                                    text: 'กรุณาระบุเหตุผลการยกเลิกคิว',
                                    icon: 'warning'
                                });
                                return; // ❌ หยุดการส่งข้อมูล
                            }
                            if (value.length < 10) {
                                Swal.fire({
                                    title: 'ข้อมูลไม่ครบถ้วน',
                                    text: 'กรุณาระบุเหตุผลอย่างน้อย 10 ตัวอักษร (ปัจจุบัน: ' + value.length + ' ตัว)',
                                    icon: 'warning'
                                });
                                return; // ❌ หยุดการส่งข้อมูล
                            }
                        }
                    }

                    // ตรวจสอบว่า form มีอยู่หรือไม่
                    if (!form) {
                        console.error('Form not found:', formId);
                        return;
                    }

                    const formData = new FormData(form);

                    // ค้นหาปุ่ม submit
                    let submitBtn = null;

                    const buttonMappings = {
                        'receiveCaseForm': 'receiveCaseBtn',
                        'confirmBookingForm': 'confirmBookingBtn',
                        'completeQueueForm': 'completeQueueBtn',
                        'cancelQueueForm': 'cancelQueueBtn'
                    };

                    const buttonId = buttonMappings[formId];
                    if (buttonId) {
                        submitBtn = document.getElementById(buttonId);
                    }

                    if (!submitBtn) {
                        submitBtn = form.querySelector('button[type="button"]');
                    }

                    if (!submitBtn) {
                        submitBtn = form.querySelector('button');
                    }

                    if (!submitBtn) {
                        console.warn('Submit button not found in form:', formId, 'continuing without button reference');
                    }

                    // แสดง loading
                    let originalText = '';
                    if (submitBtn) {
                        originalText = submitBtn.innerHTML;
                        submitBtn.disabled = true;
                        submitBtn.innerHTML = '<i class="fas fa-spinner fa-spin me-1"></i>กำลังดำเนินการ...';
                    }

                    // ส่งข้อมูล
                    fetch('<?= site_url("Queue/update_queue_status_with_images") ?>', {
                        method: 'POST',
                        body: formData
                    })
                        .then(response => {
                            console.log('Response status:', response.status);
                            return response.json();
                        })
                        .then(data => {
                            console.log('Response data:', data);
                            if (data.success) {
                                Swal.fire({
                                    title: 'อัพเดตสำเร็จ!',
                                    text: data.message,
                                    icon: 'success',
                                    timer: 2000,
                                    showConfirmButton: false
                                }).then(() => {
                                    const modalElement = safeGetElement(modalId);
                                    if (modalElement) {
                                        const modalInstance = bootstrap.Modal.getInstance(modalElement);
                                        if (modalInstance) {
                                            modalInstance.hide();
                                        } else {
                                            try {
                                                const newModal = new bootstrap.Modal(modalElement);
                                                newModal.hide();
                                            } catch (e) {
                                                console.warn('Could not close modal:', e);
                                            }
                                        }
                                    }
                                    location.reload();
                                });
                            } else {
                                Swal.fire({
                                    title: 'เกิดข้อผิดพลาด',
                                    text: data.message || 'เกิดข้อผิดพลาดที่ไม่ทราบสาเหตุ',
                                    icon: 'error'
                                });
                            }
                        })
                        .catch(error => {
                            console.error('Fetch error:', error);
                            Swal.fire({
                                title: 'เกิดข้อผิดพลาด',
                                text: 'ไม่สามารถเชื่อมต่อกับเซิร์ฟเวอร์ได้',
                                icon: 'error'
                            });
                        })
                        .finally(() => {
                            // คืนค่าปุ่ม
                            if (submitBtn) {
                                submitBtn.disabled = false;
                                submitBtn.innerHTML = originalText;
                            }
                        });
                }

                // ===================================================================
                // *** HELPER FUNCTIONS ***
                // ===================================================================

                window.refreshAlerts = function () {
                    location.reload();
                };

                window.showCategoryDetails = function (category) {
                    console.log('Showing category:', category);
                };

                window.showAllAlerts = function () {
                    // *** แก้ไข: ไปหน้า queue_alerts ในแท็บเดียวกัน ***
                    window.location.href = '<?= site_url("Queue/queue_alerts") ?>';
                };

                window.exportAlerts = function () {
                    window.location.href = '<?= site_url("Queue/export_queue_alerts") ?>';
                };

                window.goToQueue = function (queueId) {
                    window.location.href = '<?= site_url("Queue/queue_detail") ?>/' + queueId;
                };

                window.safeRefreshTable = function () {
                    location.reload();
                };

                // ===================================================================
                // *** EVENT LISTENERS ***
                // ===================================================================

                document.addEventListener('DOMContentLoaded', function () {
                    console.log('🚀 Initializing Queue Report System...');
                    console.log('🔐 User Permissions:', <?= json_encode($permissions) ?>);

                    // Initialize Queue Manager
                    if (window.queueManager) {
                        window.queueManager.init();
                    }

                    // Initialize Enhanced Modal
                    if (window.QueueStatusUpdateModal) {
                        window.QueueStatusUpdateModal.init();
                    }

                    // รับเรื่องพิจารณา button
                    const receiveCaseBtn = document.getElementById('receiveCaseBtn');
                    if (receiveCaseBtn) {
                        console.log('✅ Found receiveCaseBtn');
                        receiveCaseBtn.addEventListener('click', function (e) {
                            e.preventDefault();
                            console.log('🔄 รับเรื่องพิจารณา button clicked');
                            updateQueueStatus('receiveCaseForm', 'receiveCaseModal');
                        });
                    }

                    // ยืนยันการจอง button
                    const confirmBookingBtn = document.getElementById('confirmBookingBtn');
                    if (confirmBookingBtn) {
                        console.log('✅ Found confirmBookingBtn');
                        confirmBookingBtn.addEventListener('click', function (e) {
                            e.preventDefault();
                            console.log('🔄 ยืนยันการจอง button clicked');
                            updateQueueStatus('confirmBookingForm', 'confirmBookingModal');
                        });
                    }

                    // เสร็จสิ้น button
                    const completeQueueBtn = document.getElementById('completeQueueBtn');
                    if (completeQueueBtn) {
                        console.log('✅ Found completeQueueBtn');
                        completeQueueBtn.addEventListener('click', function (e) {
                            e.preventDefault();
                            console.log('🔄 Complete queue button clicked');
                            updateQueueStatus('completeQueueForm', 'completeQueueModal');
                        });
                    }

                    // ยกเลิกคิว button
                    const cancelQueueBtn = document.getElementById('cancelQueueBtn');
                    if (cancelQueueBtn) {
                        console.log('✅ Found cancelQueueBtn');
                        cancelQueueBtn.addEventListener('click', function (e) {
                            e.preventDefault();
                            console.log('🔄 Cancel queue button clicked');

                            const commentElement = safeGetElement('cancelComment');
                            if (!commentElement || !commentElement.value.trim()) {
                                Swal.fire({
                                    title: 'ข้อมูลไม่ครบถ้วน',
                                    text: 'กรุณาระบุเหตุผลการยกเลิก',
                                    icon: 'warning'
                                });
                                return;
                            }
                            updateQueueStatus('cancelQueueForm', 'cancelQueueModal');
                        });
                    }

                    // Delete Queue Specific Events
                    const deleteReasonInput = safeGetElement('deleteQueueReason');
                    const confirmDeleteCheckbox = safeGetElement('confirmDelete');
                    const confirmDeleteBtn = safeGetElement('confirmDeleteBtn');

                    // Update delete button state
                    if (deleteReasonInput && confirmDeleteCheckbox) {
                        deleteReasonInput.addEventListener('input', updateDeleteButtonState);
                        confirmDeleteCheckbox.addEventListener('change', updateDeleteButtonState);
                    }

                    // Delete button click
                    if (confirmDeleteBtn) {
                        confirmDeleteBtn.addEventListener('click', function (e) {
                            e.preventDefault();
                            deleteQueue();
                        });
                    }

                    console.log('✅ Queue Report System initialized successfully');
                });

                console.log("📅 Queue Report Management System loaded successfully");
            })();
        </script>



        <script>
            // ===== PAGINATION MANAGEMENT SYSTEM =====

            // Configuration from PHP
            const PaginationConfig = {
                currentPage: <?= $current_page ?? 1 ?>,
                totalPages: <?= ceil(($total_rows ?? 0) / ($per_page ?? 10)) ?>,
                totalRows: <?= $total_rows ?? 0 ?>,
                perPage: <?= $per_page ?? 10 ?>,
                baseUrl: '<?= site_url("Queue/queue_report") ?>',
                currentFilters: <?= json_encode($filters ?? []) ?>
            };

            // Quick Navigate Function
            function quickNavigate() {
                const page = parseInt(document.getElementById('quickPageInput').value);
                const maxPage = PaginationConfig.totalPages;

                if (page >= 1 && page <= maxPage) {
                    goToPage(page);
                } else {
                    alert(`กรุณาป้อนหน้าระหว่าง 1 - ${maxPage}`);
                    document.getElementById('quickPageInput').value = '';
                }
            }

            // Enhanced Refresh Function
            function refreshPage() {
                const wrapper = document.getElementById('paginationWrapper');
                wrapper.classList.add('pagination-loading');

                // รีโหลดหน้าปัจจุบัน
                setTimeout(() => {
                    window.location.reload();
                }, 500);
            }

            // Main Page Navigation Function
            function goToPage(page) {
                if (page < 1 || page > PaginationConfig.totalPages) {
                    return;
                }

                // แสดง loading state
                const wrapper = document.getElementById('paginationWrapper');
                if (wrapper) {
                    wrapper.classList.add('pagination-loading');
                }

                // สร้าง URL พร้อม filters ปัจจุบัน
                const url = buildPaginationUrl(page);

                // เปลี่ยนหน้า
                window.location.href = url;
            }

            // Build URL with current filters
            function buildPaginationUrl(page) {
                let url = PaginationConfig.baseUrl;
                const params = new URLSearchParams();

                // เพิ่ม page parameter
                if (page > 1) {
                    params.append('page', page);
                }

                // เพิ่ม filters ปัจจุบัน
                Object.keys(PaginationConfig.currentFilters).forEach(key => {
                    const value = PaginationConfig.currentFilters[key];
                    if (value && value !== '') {
                        params.append(key, value);
                    }
                });

                // รวม URL
                const queryString = params.toString();
                return queryString ? `${url}?${queryString}` : url;
            }

            // Change Page Size Function
            function changePageSize(size) {
                const wrapper = document.getElementById('paginationWrapper');
                if (wrapper) {
                    wrapper.classList.add('pagination-loading');
                }

                // สร้าง URL ใหม่พร้อมขนาดหน้าใหม่
                const params = new URLSearchParams();
                params.append('per_page', size);
                params.append('page', 1); // กลับไปหน้าแรกเมื่อเปลี่ยนขนาด

                // เพิ่ม filters ปัจจุบัน
                Object.keys(PaginationConfig.currentFilters).forEach(key => {
                    const value = PaginationConfig.currentFilters[key];
                    if (value && value !== '') {
                        params.append(key, value);
                    }
                });

                const url = `${PaginationConfig.baseUrl}?${params.toString()}`;
                window.location.href = url;
            }

            // Initialize pagination display
            function initializePagination() {
                updatePaginationDisplay(PaginationConfig.currentPage);
                updateStats(PaginationConfig.currentPage, PaginationConfig.totalPages);

                // Set max value for quick nav input
                const quickInput = document.getElementById('quickPageInput');
                if (quickInput) {
                    quickInput.max = PaginationConfig.totalPages;
                    quickInput.placeholder = PaginationConfig.currentPage.toString();
                }

                // Set current page size in selector
                const pageSizeSelector = document.querySelector('.page-size-selector select');
                if (pageSizeSelector) {
                    pageSizeSelector.value = PaginationConfig.perPage;
                }
            }

            // Update Pagination Display
            function updatePaginationDisplay(currentPage) {
                const nav = document.querySelector('.pagination-nav');
                if (!nav) return;

                const totalPages = PaginationConfig.totalPages;

                // ล้างปุ่มเก่า
                nav.innerHTML = '';

                // ปุ่มไปหน้าแรก
                nav.innerHTML += `
        <button class="page-btn nav-btn" onclick="goToPage(1)" title="หน้าแรก" ${currentPage <= 1 ? 'disabled' : ''}>
            <span><i class="fas fa-angle-double-left"></i></span>
        </button>
        <button class="page-btn nav-btn" onclick="goToPage(${currentPage - 1})" title="ก่อนหน้า" ${currentPage <= 1 ? 'disabled' : ''}>
            <span><i class="fas fa-angle-left"></i></span>
        </button>
    `;

                // คำนวณหน้าที่จะแสดง
                let startPage = Math.max(1, currentPage - 2);
                let endPage = Math.min(totalPages, currentPage + 2);

                // ปรับให้แสดงอย่างน้อย 5 หน้า (ถ้าเป็นไปได้)
                if (endPage - startPage < 4) {
                    if (startPage === 1) {
                        endPage = Math.min(totalPages, startPage + 4);
                    } else if (endPage === totalPages) {
                        startPage = Math.max(1, endPage - 4);
                    }
                }

                // แสดงหน้าแรกถ้าไม่ใช่ในช่วงที่แสดง
                if (startPage > 1) {
                    nav.innerHTML += `
            <button class="page-btn" onclick="goToPage(1)">
                <span>1</span>
            </button>
        `;
                    if (startPage > 2) {
                        nav.innerHTML += `
                <span class="page-btn" style="cursor: default; color: #94a3b8;">
                    <span>...</span>
                </span>
            `;
                    }
                }

                // ปุ่มหน้าต่างๆ
                for (let i = startPage; i <= endPage; i++) {
                    const isActive = i === currentPage ? 'active' : '';
                    nav.innerHTML += `
            <button class="page-btn ${isActive}" onclick="goToPage(${i})">
                <span>${i}</span>
            </button>
        `;
                }

                // แสดงหน้าสุดท้ายถ้าไม่ใช่ในช่วงที่แสดง
                if (endPage < totalPages) {
                    if (endPage < totalPages - 1) {
                        nav.innerHTML += `
                <span class="page-btn" style="cursor: default; color: #94a3b8;">
                    <span>...</span>
                </span>
            `;
                    }
                    nav.innerHTML += `
            <button class="page-btn" onclick="goToPage(${totalPages})">
                <span>${totalPages}</span>
            </button>
        `;
                }

                // ปุ่มไปหน้าถัดไป
                nav.innerHTML += `
        <button class="page-btn nav-btn" onclick="goToPage(${currentPage + 1})" title="ถัดไป" ${currentPage >= totalPages ? 'disabled' : ''}>
            <span><i class="fas fa-angle-right"></i></span>
        </button>
        <button class="page-btn nav-btn" onclick="goToPage(${totalPages})" title="หน้าสุดท้าย" ${currentPage >= totalPages ? 'disabled' : ''}>
            <span><i class="fas fa-angle-double-right"></i></span>
        </button>
    `;
            }

            // Update Statistics Display
            function updateStats(currentPage, totalPages) {
                const perPage = PaginationConfig.perPage;
                const totalItems = PaginationConfig.totalRows;
                const start = (currentPage - 1) * perPage + 1;
                const end = Math.min(currentPage * perPage, totalItems);

                // Update stats numbers
                const statsNumbers = document.querySelector('.stats-numbers');
                if (statsNumbers) {
                    statsNumbers.innerHTML = `
            แสดง <span class="highlight">${start.toLocaleString()}</span> - <span class="highlight">${end.toLocaleString()}</span> 
            จาก <span class="highlight">${totalItems.toLocaleString()}</span> รายการ
        `;
                }

                // Update pagination summary
                const summary = document.querySelector('.pagination-summary');
                if (summary) {
                    summary.innerHTML = `
            หน้า <strong>${currentPage}</strong> จาก <strong>${totalPages}</strong> หน้า
        `;
                }
            }

            // Keyboard Event Handler
            function handleKeyboardEvents() {
                document.addEventListener('keydown', function (e) {
                    // เฉพาะเมื่อไม่ได้พิมพ์ใน input field
                    if (document.activeElement.tagName.toLowerCase() === 'input') {
                        return;
                    }

                    switch (e.key) {
                        case 'ArrowLeft':
                            e.preventDefault();
                            if (PaginationConfig.currentPage > 1) {
                                goToPage(PaginationConfig.currentPage - 1);
                            }
                            break;
                        case 'ArrowRight':
                            e.preventDefault();
                            if (PaginationConfig.currentPage < PaginationConfig.totalPages) {
                                goToPage(PaginationConfig.currentPage + 1);
                            }
                            break;
                        case 'Home':
                            e.preventDefault();
                            goToPage(1);
                            break;
                        case 'End':
                            e.preventDefault();
                            goToPage(PaginationConfig.totalPages);
                            break;
                    }
                });
            }

            // Quick Navigate Enter Key Handler
            function handleQuickNavEnter() {
                const quickInput = document.getElementById('quickPageInput');
                if (quickInput) {
                    quickInput.addEventListener('keypress', function (e) {
                        if (e.key === 'Enter') {
                            e.preventDefault();
                            quickNavigate();
                        }
                    });
                }
            }

            // Initialize when DOM is ready
            document.addEventListener('DOMContentLoaded', function () {
                initializePagination();
                handleKeyboardEvents();
                handleQuickNavEnter();

                console.log('✅ Pagination system initialized');
                console.log('📊 Config:', PaginationConfig);
            });
        </script>