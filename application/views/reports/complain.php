<!-- รายงานเรื่องร้องเรียน - Full CodeIgniter 3 Code with Auto Compression -->
<style>
	
	
		body {
    padding-top: 20px !important;
}
	
	
.container-fluid {
    padding: 20px;
}

.page-header {
    background: linear-gradient(135deg, #fef3c7 0%, #fde68a 100%);
    color: white;
    padding: 2rem;
    border-radius: 15px;
    margin-bottom: 2rem;
    box-shadow: 0 4px 15px rgba(0,0,0,0.08);
}

.page-header h1 {
    font-size: 2rem;
    font-weight: 600;
    margin: 0;
    text-shadow: 0 2px 4px rgba(0,0,0,0.2);
    color: #A16207 !important;
}

.page-header .breadcrumb {
    background: transparent;
    padding: 0;
    margin: 10px 0 0 0;
}

.page-header .breadcrumb-item a {
    color: rgba(255,255,255,0.8);
    text-decoration: none;
}

.page-header .breadcrumb-item.active {
    color: rgba(255,255,255,1);
}

.filter-card {
    background: white;
    border-radius: 15px;
    padding: 1.5rem;
    box-shadow: 0 4px 15px rgba(0,0,0,0.08);
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
    box-shadow: 0 4px 15px rgba(0,0,0,0.08);
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

.stat-card.total::before { background: linear-gradient(90deg, #818cf8, #a78bfa); }
.stat-card.received::before { background: linear-gradient(90deg, #06b6d4, #0891b2); }
.stat-card.pending::before { background: linear-gradient(90deg, #fbbf24, #f59e0b); }
.stat-card.processing::before { background: linear-gradient(90deg, #60a5fa, #3b82f6); }
.stat-card.completed::before { background: linear-gradient(90deg, #34d399, #10b981); }
.stat-card.cancelled::before { background: linear-gradient(90deg, #f87171, #ef4444); }

.stat-card:hover {
    transform: translateY(-3px);
    box-shadow: 0 8px 25px rgba(0,0,0,0.12);
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

.stat-icon.total { background: linear-gradient(135deg, #818cf8, #a78bfa); }
.stat-icon.received { background: linear-gradient(135deg, #06b6d4, #0891b2); }
.stat-icon.pending { background: linear-gradient(135deg, #fbbf24, #f59e0b); }
.stat-icon.processing { background: linear-gradient(135deg, #60a5fa, #3b82f6); }
.stat-icon.completed { background: linear-gradient(135deg, #34d399, #10b981); }
.stat-icon.cancelled { background: linear-gradient(135deg, #f87171, #ef4444); }

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
    box-shadow: 0 4px 15px rgba(0,0,0,0.08);
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

.chart-color.received { background: #06b6d4; }
.chart-color.pending { background: #fbbf24; }
.chart-color.processing { background: #60a5fa; }
.chart-color.completed { background: #34d399; }
.chart-color.cancelled { background: #f87171; }

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
    box-shadow: 0 4px 15px rgba(0,0,0,0.08);
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

.status-badge.received {
    background: #cffafe;
    color: #0e7490;
    border: 1px solid #06b6d4;
}

.status-badge.processing {
    background: #e0e7ff;
    color: #1e40af;
    border: 1px solid #818cf8;
}

.status-badge.pending {
    background: #fef3c7;
    color: #d97706;
    border: 1px solid #fbbf24;
}

.status-badge.completed {
    background: #d1fae5;
    color: #059669;
    border: 1px solid #34d399;
}

.status-badge.cancelled {
    background: #fee2e2;
    color: #dc2626;
    border: 1px solid #fca5a5;
}

.status-badge.waiting {
    background: #fed7aa;
    color: #ea580c;
    border: 1px solid #fb923c;
}

.complain-images {
    display: flex;
    gap: 0.5rem;
    flex-wrap: wrap;
}

.complain-image {
    width: 60px;
    height: 60px;
    object-fit: cover;
    border-radius: 8px;
    border: 2px solid #e2e8f0;
    cursor: pointer;
    transition: all 0.3s ease;
}

.complain-image:hover {
    transform: scale(1.1);
    border-color: #60a5fa;
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
    background: linear-gradient(135deg, #60a5fa, #3b82f6);
    color: white;
}

.btn-action.view:hover {
    background: linear-gradient(135deg, #3b82f6, #2563eb);
    transform: translateY(-1px);
    color: white;
}

.btn-action.update {
    background: linear-gradient(135deg, #fbbf24, #f59e0b);
    color: white;
}

.btn-action.update:hover {
    background: linear-gradient(135deg, #f59e0b, #d97706);
    transform: translateY(-1px);
    color: white;
}

.btn-action.edit {
    background: linear-gradient(135deg, #34d399, #10b981);
    color: white;
}

.btn-action.edit:hover {
    background: linear-gradient(135deg, #10b981, #059669);
    transform: translateY(-1px);
    color: white;
}

.btn-action.delete {
    background: linear-gradient(135deg, #f87171, #ef4444);
    color: white;
}

.btn-action.delete:hover {
    background: linear-gradient(135deg, #ef4444, #dc2626);
    transform: translateY(-1px);
    color: white;
}

.btn-action.disabled {
    background: linear-gradient(135deg, #9ca3af, #6b7280);
    color: white;
    cursor: not-allowed;
    opacity: 0.6;
}

.btn-action.disabled:hover {
    background: linear-gradient(135deg, #9ca3af, #6b7280);
    transform: none;
    color: white;
}

.status-select {
    min-width: 150px;
    border: 1px solid #e2e8f0;
    border-radius: 8px;
    padding: 0.5rem;
    font-size: 0.875rem;
}

.pagination-wrapper {
    padding: 1.5rem;
    background: #f8fafc;
    border-top: 1px solid #e2e8f0;
    display: flex;
    align-items: center;
    justify-content: between;
}

.pagination-info {
    color: #64748b;
    font-size: 0.9rem;
}

.modal-content {
    border: none;
    border-radius: 15px;
    box-shadow: 0 20px 50px rgba(0,0,0,0.1);
}

.modal-header {
    background: linear-gradient(135deg, #fbbf24, #f59e0b);
    color: white;
    border: none;
    border-radius: 15px 15px 0 0;
}

.text-truncate-2 {
    display: -webkit-box;
    -webkit-line-clamp: 2;
    -webkit-box-orient: vertical;
    overflow: hidden;
    text-overflow: ellipsis;
    max-height: 2.4em;
    line-height: 1.2em;
}

.btn-action[title] {
    position: relative;
}

.btn-action[title]:hover::after {
    content: attr(title);
    position: absolute;
    bottom: 100%;
    left: 50%;
    transform: translateX(-50%);
    background: #1f2937;
    color: white;
    padding: 0.5rem 0.8rem;
    border-radius: 4px;
    font-size: 0.75rem;
    white-space: nowrap;
    z-index: 1000;
    margin-bottom: 5px;
}

.btn-action[title]:hover::before {
    content: '';
    position: absolute;
    bottom: 100%;
    left: 50%;
    transform: translateX(-50%);
    border: 5px solid transparent;
    border-top-color: #1f2937;
    z-index: 1000;
}

.status-row {
    background: #f8fafc;
    border-left: 4px solid #60a5fa;
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
    color: #60a5fa;
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
    background: #fed7aa;
    color: #ea580c;
    border: 1px solid #fb923c;
}

.btn-status-row.waiting:hover:not(:disabled) {
    background: #fb923c;
    color: #ea580c;
    transform: translateY(-1px);
    box-shadow: 0 2px 8px rgba(251, 146, 60, 0.2);
}

.btn-status-row.received {
    background: #cffafe;
    color: #0e7490;
    border: 1px solid #67e8f9;
}

.btn-status-row.received:hover:not(:disabled) {
    background: #a5f3fc;
    color: #155e75;
    transform: translateY(-1px);
    box-shadow: 0 2px 8px rgba(6, 182, 212, 0.2);
}

.btn-status-row.pending {
    background: #fef3c7;
    color: #d97706;
    border: 1px solid #fde68a;
}

.btn-status-row.pending:hover:not(:disabled) {
    background: #fde68a;
    color: #b45309;
    transform: translateY(-1px);
    box-shadow: 0 2px 8px rgba(251, 191, 36, 0.2);
}

.btn-status-row.processing {
    background: #dbeafe;
    color: #1e40af;
    border: 1px solid #93c5fd;
}

.btn-status-row.processing:hover:not(:disabled) {
    background: #bfdbfe;
    color: #1e3a8a;
    transform: translateY(-1px);
    box-shadow: 0 2px 8px rgba(96, 165, 250, 0.2);
}

.btn-status-row.completed {
    background: #d1fae5;
    color: #059669;
    border: 1px solid #6ee7b7;
}

.btn-status-row.completed:hover:not(:disabled) {
    background: #a7f3d0;
    color: #047857;
    transform: translateY(-1px);
    box-shadow: 0 2px 8px rgba(52, 211, 153, 0.2);
}

.btn-status-row.cancelled {
    background: #fee2e2;
    color: #dc2626;
    border: 1px solid #fca5a5;
}

.btn-status-row.cancelled:hover:not(:disabled) {
    background: #fecaca;
    color: #b91c1c;
    transform: translateY(-1px);
    box-shadow: 0 2px 8px rgba(248, 113, 113, 0.2);
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

/* Case Container Styling */
.case-container {
    background: #ffffff;
    border: 2px solid #e2e8f0;
    border-radius: 12px;
    margin-bottom: 1.5rem;
    overflow: hidden;
    box-shadow: 0 2px 8px rgba(0,0,0,0.06);
    transition: all 0.3s ease;
}

.case-container:hover {
    border-color: #60a5fa;
    box-shadow: 0 4px 20px rgba(96, 165, 250, 0.15);
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
    border-left: 4px solid #60a5fa;
    border-bottom: none;
}

.case-status-row td {
    border-bottom: none !important;
    border-top: none !important;
}

.case-header {
    background: linear-gradient(135deg, #f8fafc 0%, #e2e8f0 100%);
    padding: 0.75rem 1rem;
    border-bottom: 1px solid #e2e8f0;
    font-size: 0.875rem;
    font-weight: 600;
    color: #475569;
    display: flex;
    align-items: center;
    gap: 0.5rem;
}

.case-header i {
    color: #60a5fa;
}

.case-number {
    background: linear-gradient(135deg, #60a5fa, #3b82f6);
    color: white;
    padding: 0.25rem 0.75rem;
    border-radius: 20px;
    font-size: 0.75rem;
    font-weight: 700;
    margin-left: auto;
}

/* Additional table styling for case containers */
.case-container .status-badge {
    font-size: 0.7rem;
    padding: 0.3rem 0.6rem;
    min-width: 100px;
}

.case-container .btn-action {
    font-size: 0.75rem;
    padding: 0.3rem 0.6rem;
    min-width: 60px;
}

.case-container .action-buttons {
    gap: 0.25rem;
}

.case-container .complain-image {
    width: 50px;
    height: 50px;
}

.case-container .text-truncate-2 {
    max-height: 2.2em;
    line-height: 1.1em;
    font-size: 0.875rem;
}

/* Cases Cards Section */
.cases-cards-section {
    background: white;
    border-radius: 15px;
    padding: 1.5rem;
    box-shadow: 0 4px 15px rgba(0,0,0,0.08);
    margin-top: 2rem;
}

.section-header {
    display: flex;
    align-items: center;
    justify-content: space-between;
    margin-bottom: 1.5rem;
    border-bottom: 1px solid #e2e8f0;
    padding-bottom: 1rem;
}

.section-title {
    font-size: 1.25rem;
    font-weight: 600;
    color: #1e293b;
    margin: 0;
}

.section-actions {
    display: flex;
    gap: 0.5rem;
}

.cases-cards-container {
    margin-top: 1rem;
}

.cases-grid {
    display: grid;
    grid-template-columns: repeat(auto-fill, minmax(400px, 1fr));
    gap: 1.5rem;
}

.case-card {
    background: white;
    border: 1px solid #e2e8f0;
    border-radius: 12px;
    padding: 1.5rem;
    box-shadow: 0 2px 8px rgba(0,0,0,0.05);
    transition: all 0.3s ease;
    position: relative;
}

.case-card:hover {
    transform: translateY(-2px);
    box-shadow: 0 8px 25px rgba(0,0,0,0.1);
    border-color: #60a5fa;
}

/* Modal Styles for Auto Compression */
.status-modal-enhanced {
    max-width: 900px !important;
}

.image-upload-container {
    border: 2px dashed #e2e8f0;
    border-radius: 8px;
    padding: 20px;
    text-align: center;
    background: #f8fafc;
    transition: all 0.3s ease;
}

.image-upload-container:hover {
    border-color: #60a5fa;
    background: #eff6ff;
}

.image-upload-container.dragover {
    border-color: #3b82f6;
    background: #dbeafe;
}

.upload-icon {
    font-size: 2rem;
    color: #9ca3af;
    margin-bottom: 10px;
}

.image-preview-container {
    display: grid;
    grid-template-columns: repeat(auto-fill, minmax(120px, 1fr));
    gap: 10px;
    margin-top: 15px;
}

.image-preview-item {
    position: relative;
    border-radius: 8px;
    overflow: hidden;
    background: #f3f4f6;
    border: 2px solid #e5e7eb;
    transition: all 0.3s ease;
}

.image-preview-item:hover {
    border-color: #60a5fa;
    transform: translateY(-2px);
    box-shadow: 0 4px 12px rgba(0,0,0,0.1);
}

.image-preview-item img {
    width: 100%;
    height: 100px;
    object-fit: cover;
    display: block;
}

.image-remove-btn {
    position: absolute;
    top: 5px;
    right: 5px;
    background: rgba(239, 68, 68, 0.9);
    color: white;
    border: none;
    border-radius: 50%;
    width: 25px;
    height: 25px;
    display: flex;
    align-items: center;
    justify-content: center;
    cursor: pointer;
    font-size: 12px;
    transition: all 0.3s ease;
}

.image-remove-btn:hover {
    background: rgba(220, 38, 38, 1);
    transform: scale(1.1);
}

.image-preview-item.compressed {
    border-color: #10b981;
    background: #f0fdf4;
}

.image-preview-item .compression-badge {
    position: absolute;
    bottom: 5px;
    left: 5px;
    background: rgba(16, 185, 129, 0.9);
    color: white;
    padding: 2px 6px;
    border-radius: 4px;
    font-size: 0.7rem;
    font-weight: 600;
}

.upload-limit-info {
    color: #6b7280;
    font-size: 0.875rem;
    margin-top: 10px;
}

.file-input-hidden {
    display: none;
}

.status-change-summary {
    background: #f8fafc;
    border: 1px solid #e2e8f0;
    border-radius: 8px;
    padding: 15px;
    margin-bottom: 20px;
}

.status-arrow {
    display: flex;
    align-items: center;
    justify-content: center;
    margin: 10px 0;
    color: #60a5fa;
    font-weight: bold;
}

.current-status, .new-status {
    display: inline-block;
    padding: 8px 16px;
    border-radius: 20px;
    font-weight: 600;
    font-size: 0.875rem;
}

.current-status {
    background: #fee2e2;
    color: #dc2626;
}

.new-status {
    background: #d1fae5;
    color: #059669;
}

.note-section {
    margin: 20px 0;
}

.character-counter {
    text-align: right;
    font-size: 0.75rem;
    color: #6b7280;
    margin-top: 5px;
}

.character-counter.warning {
    color: #f59e0b;
}

.character-counter.danger {
    color: #ef4444;
}

.modal-section {
    margin-bottom: 25px;
    padding-bottom: 20px;
    border-bottom: 1px solid #f1f5f9;
}

.modal-section:last-child {
    border-bottom: none;
    margin-bottom: 0;
    padding-bottom: 0;
}

.section-title {
    font-weight: 600;
    color: #374151;
    margin-bottom: 15px;
    display: flex;
    align-items: center;
    gap: 8px;
}

.section-title i {
    color: #60a5fa;
}

.image-upload-stats {
    display: flex;
    justify-content: space-between;
    align-items: center;
    margin-top: 10px;
    font-size: 0.875rem;
    color: #6b7280;
}

.upload-progress {
    display: none;
    margin-top: 10px;
}

.progress {
    height: 6px;
    border-radius: 3px;
    background: #f3f4f6;
    overflow: hidden;
}

.progress-bar {
    height: 100%;
    background: linear-gradient(90deg, #60a5fa, #3b82f6);
    border-radius: 3px;
    transition: width 0.3s ease;
}

/* Auto compression processing indicator */
.compression-processing {
    display: none;
    text-align: center;
    padding: 10px;
    color: #6b7280;
    font-size: 0.875rem;
    background: #f8fafc;
    border-radius: 6px;
    margin-top: 10px;
}

.compression-processing.show {
    display: block;
}

.compression-processing .spinner {
    display: inline-block;
    width: 16px;
    height: 16px;
    border: 2px solid #e5e7eb;
    border-radius: 50%;
    border-top-color: #3b82f6;
    animation: spin 1s ease-in-out infinite;
    margin-right: 8px;
}

@keyframes spin {
    to { transform: rotate(360deg); }
}

/* Responsive adjustments */
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
    
    .complain-image {
        width: 40px;
        height: 40px;
    }
    
    .status-modal-enhanced {
        max-width: 95% !important;
        margin: 10px auto;
    }
    
    .image-preview-container {
        grid-template-columns: repeat(auto-fill, minmax(100px, 1fr));
        gap: 8px;
    }
    
    .image-preview-item img {
        height: 80px;
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
    
    /* Hide some columns on mobile */
    .case-data-row td:nth-child(1), /* รหัส */
    .case-data-row td:nth-child(7), /* ผู้แจ้ง */
    .case-data-row td:nth-child(8)  /* เบอร์ติดต่อ */ {
        display: none;
    }
    
    /* Adjust remaining columns */
    .case-data-row td:nth-child(2) { /* วันที่ */
        width: auto;
        min-width: 80px;
    }
    
    .case-data-row td:nth-child(3) { /* สถานะ */
        width: auto;
        min-width: 100px;
    }
    
    .case-data-row td:nth-child(4) { /* รูปภาพ */
        width: auto;
        min-width: 60px;
    }
    
    .case-data-row td:nth-child(5), /* หัวข้อ */
    .case-data-row td:nth-child(6) { /* รายละเอียด */
        width: auto;
    }
    
    .case-data-row td:nth-child(9) { /* จัดการ */
        width: auto;
        min-width: 100px;
    }
    
    /* Alerts responsive */
    .chart-card .alert-summary-row {
        grid-template-columns: 1fr !important;
        gap: 0.5rem !important;
        margin-bottom: 1rem !important;
    }
    
    .chart-card .alert-summary-card {
        padding: 0.75rem !important;
        gap: 0.75rem !important;
    }
    
    .chart-card .alert-icon {
        font-size: 1.25rem !important;
    }
    
    .chart-card .alert-number {
        font-size: 1.25rem !important;
    }
    
    .chart-card .alert-label {
        font-size: 0.8rem !important;
    }
    
    .chart-card .alerts-list {
        padding: 0.75rem !important;
    }
    
    .chart-card .alerts-header {
        flex-direction: column !important;
        gap: 0.5rem !important;
        align-items: flex-start !important;
    }
    
    .chart-card .alert-item {
        padding: 0.5rem !important;
        gap: 0.75rem !important;
    }
    
    .chart-card .alert-item-icon {
        width: 30px !important;
        height: 30px !important;
        font-size: 0.875rem !important;
    }
    
    .chart-card .alert-case-id {
        font-size: 0.8rem !important;
    }
    
    .chart-card .alert-days {
        font-size: 0.7rem !important;
        padding: 0.15rem 0.4rem !important;
    }
    
    .chart-card .alert-item-title {
        font-size: 0.8rem !important;
    }
    
    .chart-card .alert-item-status {
        font-size: 0.7rem !important;
    }
    
    .chart-card .alerts-footer {
        flex-direction: column !important;
        gap: 0.5rem !important;
    }
    
    .chart-card .alerts-footer .btn {
        width: 100% !important;
        font-size: 0.875rem !important;
    }
}

.status-waiting { background: #fed7aa !important; color: #ea580c !important; }
.status-received { background: #cffafe !important; color: #0e7490 !important; }
.status-pending { background: #fef3c7 !important; color: #d97706 !important; }
.status-processing { background: #dbeafe !important; color: #1e40af !important; }
.status-completed { background: #d1fae5 !important; color: #059669 !important; }
.status-cancelled { background: #fee2e2 !important; color: #dc2626 !important; }

.bg-gradient-primary {
    background: linear-gradient(135deg, #60a5fa, #3b82f6) !important;
}
</style>




<style>

@keyframes spin {
    to { transform: rotate(360deg); }
}

/* ===== Work Performance & Alerts Styling ===== */
.alerts-container {
    padding: 0;
}

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
    display: flex;
    align-items: center;
    gap: 1rem;
    box-shadow: 0 2px 8px rgba(0,0,0,0.06);
    border: 1px solid #f1f5f9;
    transition: all 0.3s ease;
}

.alert-summary-card:hover {
    transform: translateY(-2px);
    box-shadow: 0 4px 15px rgba(0,0,0,0.1);
}

.alert-summary-card.warning {
    border-left: 4px solid #fbbf24;
    background: linear-gradient(135deg, #fefbf7 0%, #fff8e1 100%);
}

.alert-summary-card.danger {
    border-left: 4px solid #f87171;
    background: linear-gradient(135deg, #fef7f7 0%, #ffebee 100%);
}

.alert-summary-card.critical {
    border-left: 4px solid #ef4444;
    background: linear-gradient(135deg, #fef2f2 0%, #fce4ec 100%);
}

.alert-icon {
    width: 40px;
    height: 40px;
    border-radius: 8px;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 1.1rem;
    color: white;
    flex-shrink: 0;
}

.alert-summary-card.warning .alert-icon {
    background: linear-gradient(135deg, #fbbf24, #f59e0b);
}

.alert-summary-card.danger .alert-icon {
    background: linear-gradient(135deg, #f87171, #ef4444);
}

.alert-summary-card.critical .alert-icon {
    background: linear-gradient(135deg, #ef4444, #dc2626);
}

.alert-content {
    flex: 1;
    min-width: 0;
}

.alert-number {
    font-size: 1.5rem;
    font-weight: 700;
    color: #1e293b;
    margin-bottom: 0.25rem;
    line-height: 1;
}

.alert-label {
    color: #64748b;
    font-size: 0.875rem;
    font-weight: 500;
    line-height: 1.2;
}

.alerts-list {
    background: #f8fafc;
    border-radius: 10px;
    padding: 1rem;
    border: 1px solid #e2e8f0;
}

.alerts-header {
    display: flex;
    align-items: center;
    justify-content: space-between;
    margin-bottom: 1rem;
    padding-bottom: 0.75rem;
    border-bottom: 1px solid #e2e8f0;
}

.alerts-header h6 {
    margin: 0;
    font-size: 1rem;
    font-weight: 600;
    color: #374151;
}

.alerts-header small {
    color: #6b7280;
    font-size: 0.8rem;
}

.alerts-items {
    max-height: 400px;
    overflow-y: auto;
}

.alert-item {
    display: flex;
    align-items: center;
    gap: 1rem;
    padding: 0.75rem;
    margin-bottom: 0.5rem;
    background: white;
    border-radius: 8px;
    border: 1px solid #e5e7eb;
    cursor: pointer;
    transition: all 0.3s ease;
}

.alert-item:hover {
    border-color: #60a5fa;
    box-shadow: 0 2px 8px rgba(96, 165, 250, 0.1);
    transform: translateX(3px);
}

.alert-item:last-child {
    margin-bottom: 0;
}

.alert-item.critical {
    border-left: 4px solid #ef4444;
    background: linear-gradient(135deg, #fef2f2 0%, #ffffff 100%);
}

.alert-item.danger {
    border-left: 4px solid #f87171;
    background: linear-gradient(135deg, #fef7f7 0%, #ffffff 100%);
}

.alert-item.warning {
    border-left: 4px solid #fbbf24;
    background: linear-gradient(135deg, #fefbf7 0%, #ffffff 100%);
}

.alert-item-icon {
    width: 35px;
    height: 35px;
    border-radius: 6px;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 1rem;
    color: white;
    flex-shrink: 0;
}

.alert-item.critical .alert-item-icon {
    background: linear-gradient(135deg, #ef4444, #dc2626);
}

.alert-item.danger .alert-item-icon {
    background: linear-gradient(135deg, #f87171, #ef4444);
}

.alert-item.warning .alert-item-icon {
    background: linear-gradient(135deg, #fbbf24, #f59e0b);
}

.alert-item-content {
    flex: 1;
    min-width: 0;
}

.alert-item-header {
    display: flex;
    align-items: center;
    gap: 0.75rem;
    margin-bottom: 0.25rem;
}

.alert-case-id {
    font-weight: 700;
    color: #1e293b;
    font-size: 0.875rem;
}

.alert-days {
    background: rgba(239, 68, 68, 0.1);
    color: #dc2626;
    padding: 0.2rem 0.5rem;
    border-radius: 12px;
    font-size: 0.75rem;
    font-weight: 600;
    border: 1px solid rgba(239, 68, 68, 0.2);
}

.alert-item.warning .alert-days {
    background: rgba(251, 191, 36, 0.1);
    color: #d97706;
    border-color: rgba(251, 191, 36, 0.2);
}

.alert-item-title {
    font-size: 0.875rem;
    color: #374151;
    font-weight: 500;
    margin-bottom: 0.25rem;
    line-height: 1.3;
}

.alert-item-status {
    font-size: 0.75rem;
    color: #6b7280;
    line-height: 1.2;
}

.alert-item-action {
    color: #9ca3af;
    font-size: 0.875rem;
    transition: all 0.3s ease;
}

.alert-item:hover .alert-item-action {
    color: #60a5fa;
    transform: translateX(2px);
}

.alert-item-more {
    text-align: center;
    margin-top: 0.5rem;
}

.alert-item-more .btn {
    border-style: dashed;
    background: transparent;
    color: #6b7280;
    font-size: 0.8rem;
    padding: 0.5rem 1rem;
}

.alert-item-more .btn:hover {
    background: #f3f4f6;
    border-color: #9ca3af;
    color: #374151;
}

.alerts-footer {
    display: flex;
    gap: 0.5rem;
    margin-top: 1rem;
    padding-top: 1rem;
    border-top: 1px solid #e2e8f0;
    justify-content: center;
}

.alerts-footer .btn {
    font-size: 0.875rem;
    padding: 0.5rem 1rem;
    min-width: 120px;
}

/* Custom Scrollbar for Alerts */
.alerts-items::-webkit-scrollbar {
    width: 6px;
}

.alerts-items::-webkit-scrollbar-track {
    background: #f1f5f9;
    border-radius: 3px;
}

.alerts-items::-webkit-scrollbar-thumb {
    background: #cbd5e1;
    border-radius: 3px;
}

.alerts-items::-webkit-scrollbar-thumb:hover {
    background: #94a3b8;
}

/* Warning Cases List (for Modal) */
.warning-cases-list {
    max-height: 400px;
    overflow-y: auto;
}

.warning-case-item {
    padding: 0.75rem;
    margin-bottom: 0.5rem;
    background: #f8fafc;
    border: 1px solid #e2e8f0;
    border-radius: 6px;
    cursor: pointer;
    transition: all 0.3s ease;
}

.warning-case-item:hover {
    background: #eff6ff;
    border-color: #60a5fa;
    transform: translateX(3px);
}

.warning-case-item:last-child {
    margin-bottom: 0;
}

.case-info {
    font-size: 0.875rem;
}

.case-info strong {
    color: #1e293b;
}

.case-info small {
    color: #6b7280;
}

/* All Alerts Modal Styling */
.all-alerts-container {
    max-height: 500px;
    overflow-y: auto;
    text-align: left;
}

.alert-section {
    margin-bottom: 1.5rem;
    padding-bottom: 1rem;
    border-bottom: 1px solid #f1f5f9;
}

.alert-section:last-child {
    border-bottom: none;
    margin-bottom: 0;
}

.alert-section h6 {
    margin-bottom: 0.75rem;
    font-size: 1rem;
    font-weight: 600;
    display: flex;
    align-items: center;
    gap: 0.5rem;
}

.alert-items-list {
    display: flex;
    flex-direction: column;
    gap: 0.5rem;
}

.alert-item-summary {
    padding: 0.5rem 0.75rem;
    background: #f8fafc;
    border: 1px solid #e2e8f0;
    border-radius: 6px;
    font-size: 0.875rem;
    cursor: pointer;
    transition: all 0.3s ease;
}

.alert-item-summary:hover {
    background: #eff6ff;
    border-color: #60a5fa;
    color: #1e40af;
}

/* Responsive adjustments */


</style>


<!-- Enhanced Complain View with Permission System -->
<style>
/* เพิ่ม CSS สำหรับการแสดงสิทธิ์ */
.permission-info {
    background: linear-gradient(135deg, #f8fafc 0%, #e2e8f0 100%);
    border: 1px solid #cbd5e1;
    border-radius: 8px;
    padding: 10px 15px;
    margin-bottom: 20px;
    font-size: 0.875rem;
}

.permission-info.system-admin {
    background: linear-gradient(135deg, #fef3c7 0%, #fde68a 100%);
    border-color: #f59e0b;
    color: #92400e;
}

.permission-info.super-admin {
    background: linear-gradient(135deg, #ddd6fe 0%, #c4b5fd 100%);
    border-color: #8b5cf6;
    color: #5b21b6;
}

.permission-info.user-admin {
    background: linear-gradient(135deg, #dbeafe 0%, #bfdbfe 100%);
    border-color: #3b82f6;
    color: #1e40af;
}

.permission-info.no-access {
    background: linear-gradient(135deg, #fee2e2 0%, #fecaca 100%);
    border-color: #f87171;
    color: #dc2626;
}

.btn-action.disabled-by-permission {
    background: linear-gradient(135deg, #f3f4f6, #e5e7eb) !important;
    color: #9ca3af !important;
    cursor: not-allowed !important;
    opacity: 0.6 !important;
    pointer-events: none !important;
}

.btn-status-row.disabled-by-permission {
    background: #f9fafb !important;
    color: #9ca3af !important;
    cursor: not-allowed !important;
    opacity: 0.6 !important;
    pointer-events: none !important;
    border: 1px solid #e5e7eb !important;
}

.permission-tooltip {
    position: relative;
    display: inline-block;
}

.permission-tooltip .tooltiptext {
    visibility: hidden;
    width: 200px;
    background-color: #1f2937;
    color: #fff;
    text-align: center;
    border-radius: 6px;
    padding: 8px;
    position: absolute;
    z-index: 1;
    bottom: 125%;
    left: 50%;
    margin-left: -100px;
    font-size: 0.75rem;
    opacity: 0;
    transition: opacity 0.3s;
}

.permission-tooltip .tooltiptext::after {
    content: "";
    position: absolute;
    top: 100%;
    left: 50%;
    margin-left: -5px;
    border-width: 5px;
    border-style: solid;
    border-color: #1f2937 transparent transparent transparent;
}

.permission-tooltip:hover .tooltiptext {
    visibility: visible;
    opacity: 1;
}

.role-badge {
    display: inline-block;
    padding: 0.25rem 0.5rem;
    border-radius: 0.375rem;
    font-size: 0.75rem;
    font-weight: 600;
    text-transform: uppercase;
    letter-spacing: 0.05em;
}

.role-badge.system-admin {
    background-color: #fbbf24;
    color: #92400e;
}

.role-badge.super-admin {
    background-color: #8b5cf6;
    color: #ffffff;
}

.role-badge.user-admin {
    background-color: #3b82f6;
    color: #ffffff;
}

.role-badge.no-access {
    background-color: #ef4444;
    color: #ffffff;
}

/* เพิ่ม CSS สำหรับปุ่มล้างข้อมูล */
.btn-clear-data {
    background: linear-gradient(135deg, #dc2626, #b91c1c);
    color: white;
    border: none;
    transition: all 0.3s ease;
    font-weight: 600;
}

.btn-clear-data:hover {
    background: linear-gradient(135deg, #b91c1c, #991b1b);
    transform: translateY(-1px);
    box-shadow: 0 4px 12px rgba(220, 38, 38, 0.3);
    color: white;
}

.btn-clear-data:active {
    transform: translateY(0);
}

/* เพิ่ม CSS สำหรับปุ่มที่ถูกปิดใช้งาน */
.action-buttons .btn-action.disabled-by-permission::before {
    content: "🔒 ";
    margin-right: 0.25rem;
}

.status-buttons-container .btn-status-row.disabled-by-permission::before {
    content: "🔒 ";
    margin-right: 0.25rem;
}

/* CSS สำหรับแสดงข้อความเมื่อไม่มีสิทธิ์ */
.no-permission-overlay {
    position: relative;
    opacity: 0.5;
    pointer-events: none;
}

.no-permission-overlay::after {
    content: "ไม่มีสิทธิ์ใช้งาน";
    position: absolute;
    top: 50%;
    left: 50%;
    transform: translate(-50%, -50%);
    background: rgba(239, 68, 68, 0.9);
    color: white;
    padding: 0.5rem 1rem;
    border-radius: 0.375rem;
    font-size: 0.75rem;
    font-weight: 600;
    white-space: nowrap;
    z-index: 10;
}
	
	
	
	.stat-card.waiting::before { background: linear-gradient(90deg, #fb923c, #f97316); }
.stat-icon.waiting { background: linear-gradient(135deg, #fb923c, #f97316); }
	
	.status-badge.waiting {
    background: #fed7aa;
    color: #ea580c;
    border: 1px solid #fb923c;
}

.chart-color.waiting { background: #fb923c; }
	
	
	
	.header-actions {
    display: flex;
    align-items: center;
    gap: 0.5rem;
}

.header-actions .btn {
    border-radius: 8px;
    font-weight: 500;
    transition: all 0.3s ease;
    white-space: nowrap;
}

.header-actions .btn:hover {
    transform: translateY(-1px);
    box-shadow: 0 4px 12px rgba(0,0,0,0.1);
}

@media (max-width: 768px) {
    .page-header .d-flex {
        flex-direction: column;
        gap: 1rem;
        align-items: stretch;
    }
    
    .header-actions {
        justify-content: center;
        flex-wrap: wrap;
    }
    
    .header-actions .btn {
        flex: 1;
        min-width: 120px;
    }
}
	

	
	
	
	
	.icon-selector .btn {
    display: flex;
    align-items: center;
    justify-content: flex-start;
    text-align: left;
}

.icon-grid {
    display: grid;
    grid-template-columns: repeat(auto-fill, minmax(80px, 1fr));
    gap: 10px;
    max-height: 400px;
    overflow-y: auto;
}

.icon-option {
    display: flex;
    flex-direction: column;
    align-items: center;
    padding: 15px 10px;
    border: 2px solid #e9ecef;
    border-radius: 8px;
    cursor: pointer;
    transition: all 0.3s ease;
    background: white;
    text-decoration: none;
    color: #495057;
}

.icon-option:hover {
    border-color: #007bff;
    background: #f8f9fa;
    transform: translateY(-2px);
    box-shadow: 0 4px 8px rgba(0,0,0,0.1);
    color: #007bff;
    text-decoration: none;
}

.icon-option.selected {
    border-color: #007bff;
    background: #e7f3ff;
    color: #007bff;
}

.icon-option i {
    font-size: 24px;
    margin-bottom: 5px;
}

.icon-option span {
    font-size: 10px;
    text-align: center;
    line-height: 1.2;
}

.icon-search {
    margin-bottom: 20px;
}

.icon-search input {
    border-radius: 25px;
    padding: 10px 20px;
}

@media (max-width: 768px) {
    .icon-grid {
        grid-template-columns: repeat(auto-fill, minmax(60px, 1fr));
        gap: 8px;
    }
    
    .icon-option {
        padding: 10px 5px;
    }
    
    .icon-option i {
        font-size: 20px;
    }
    
    .icon-option span {
        font-size: 9px;
    }
}
	
	
	
</style>

<div class="container-fluid">
    <!-- Page Header -->
    <!-- Page Header -->
<div class="page-header">
    <div class="d-flex justify-content-between align-items-center">
        <div>
            <h1><i class="fas fa-chart-line me-3"></i>รายงานแจ้งเรื่อง ร้องเรียน</h1>
        </div>
        <!-- 🆕 เพิ่มปุ่มจัดการหมวดหมู่ -->
        <div class="header-actions">
            <?php if ($user_permissions['can_manage_status'] || $user_permissions['position_id'] <= 2): ?>
                <button type="button" class="btn btn-outline-primary me-2" onclick="window.categoryManager.showModal()">
                    <i class="fas fa-tags me-2"></i>จัดการหมวดหมู่
                </button>
            <?php endif; ?>
            
            <button type="button" class="btn btn-outline-secondary" onclick="safeRefreshTable()">
                <i class="fas fa-sync-alt me-2"></i>รีเฟรช
            </button>
        </div>
    </div>
</div>

    <!-- 🆕 Permission Info Banner -->
    <?php
    $permission_class = 'no-access';
    $permission_icon = 'fas fa-lock';
    $permission_message = 'ไม่มีสิทธิ์เข้าถึง';
    
    if ($user_permissions['can_view_reports']) {
        if ($user_permissions['position_id'] == 1) {
            $permission_class = 'system-admin';
            $permission_icon = 'fas fa-crown';
            $permission_message = 'System Admin - มีสิทธิ์เต็มในการดูรายงาน จัดการสถานะ และลบเรื่องร้องเรียน';
        } elseif ($user_permissions['position_id'] == 2) {
            $permission_class = 'super-admin';
            $permission_icon = 'fas fa-star';
            $permission_message = 'Super Admin - มีสิทธิ์เต็มในการดูรายงาน จัดการสถานะ และลบเรื่องร้องเรียน';
        } elseif ($user_permissions['position_id'] == 3) {
            $permission_class = 'user-admin';
            $permission_icon = 'fas fa-user-shield';
            if ($user_permissions['can_manage_status']) {
                $permission_message = 'User Admin - มีสิทธิ์ดูรายงานและจัดการสถานะเรื่องร้องเรียน (Grant ID: 105)';
            } else {
                $permission_message = 'User Admin - มีสิทธิ์ดูรายงานเท่านั้น (ไม่มี Grant ID: 105 สำหรับจัดการสถานะ)';
            }
        } else {
            $permission_class = 'user-admin';
            $permission_icon = 'fas fa-eye';
            $permission_message = 'มีสิทธิ์ดูรายงานเท่านั้น';
        }
    }
    ?>
    
    <div class="permission-info <?= $permission_class ?>">
        <div class="d-flex align-items-center justify-content-between">
            <div class="d-flex align-items-center">
                <i class="<?= $permission_icon ?> me-2"></i>
                <span><strong>สิทธิ์การใช้งาน:</strong> <?= $permission_message ?></span>
            </div>
            <div class="d-flex align-items-center gap-2">
                <span class="role-badge <?= $permission_class ?>">
                    <?= $user_permissions['user_role'] ?>
                </span>
                
                <?php if ($user_permissions['position_id'] == 1 && $user_permissions['user_role'] == 'System Admin'): // เฉพาะ System Admin เท่านั้น ?>
                    <button class="btn btn-sm btn-danger" 
                            onclick="clearAllComplainData()"
                            title="ล้างข้อมูลเรื่องร้องเรียนทั้งหมด - เฉพาะ System Admin">
                        <i class="fas fa-trash-alt me-1"></i>ล้างข้อมูล
                    </button>
                <?php endif; ?>
                
                <?php if ($user_permissions['position_id'] == 1 && $user_permissions['user_role'] == 'System Admin'): // เฉพาะ System Admin เท่านั้น ?>
                    <a href="<?= site_url('debug_permissions') ?>" 
                       class="btn btn-sm btn-outline-secondary" title="Debug สิทธิ์">
                        <i class="fas fa-bug"></i>
                    </a>
                <?php endif; ?>
				
				
            </div>
        </div>
    </div>

    <!-- Statistics Cards -->
    <div class="stats-row">
        <div class="stat-card total">
            <div class="stat-header">
                <div class="stat-icon total">
                    <i class="fas fa-clipboard-list"></i>
                </div>
            </div>
            <div class="stat-value"><?= number_format($complain_summary['total']) ?></div>
            <div class="stat-label">เรื่องร้องเรียนทั้งหมด</div>
        </div>
		
		<div class="stat-card waiting">
        <div class="stat-header">
            <div class="stat-icon waiting">
                <i class="fas fa-hourglass-start"></i>
            </div>
        </div>
        <div class="stat-value"><?= number_format($complain_summary['by_status']['รอรับเรื่อง'] ?? 0) ?></div>
        <div class="stat-label">รอรับเรื่อง</div>
    </div>
		

        <div class="stat-card received">
            <div class="stat-header">
                <div class="stat-icon received">
                    <i class="fas fa-inbox"></i>
                </div>
            </div>
            <div class="stat-value"><?= number_format($complain_summary['by_status']['รับเรื่องแล้ว'] ?? 0) ?></div>
            <div class="stat-label">รับเรื่องแล้ว</div>
        </div>

        <div class="stat-card pending">
            <div class="stat-header">
                <div class="stat-icon pending">
                    <i class="fas fa-clock"></i>
                </div>
            </div>
            <div class="stat-value"><?= number_format($complain_summary['by_status']['รอดำเนินการ'] ?? 0) ?></div>
            <div class="stat-label">รอดำเนินการ</div>
        </div>

        <div class="stat-card processing">
            <div class="stat-header">
                <div class="stat-icon processing">
                    <i class="fas fa-cogs"></i>
                </div>
            </div>
            <div class="stat-value"><?= number_format($complain_summary['by_status']['กำลังดำเนินการ'] ?? 0) ?></div>
            <div class="stat-label">กำลังดำเนินการ</div>
        </div>

        <div class="stat-card completed">
            <div class="stat-header">
                <div class="stat-icon completed">
                    <i class="fas fa-check-circle"></i>
                </div>
            </div>
            <div class="stat-value"><?= number_format($complain_summary['by_status']['ดำเนินการเรียบร้อย'] ?? 0) ?></div>
            <div class="stat-label">ดำเนินการเรียบร้อย</div>
        </div>

        <div class="stat-card cancelled">
            <div class="stat-header">
                <div class="stat-icon cancelled">
                    <i class="fas fa-times-circle"></i>
                </div>
            </div>
            <div class="stat-value"><?= number_format($complain_summary['by_status']['ยกเลิก'] ?? 0) ?></div>
            <div class="stat-label">ยกเลิก</div>
        </div>
    </div>

    <!-- Filter Card -->
    <div class="filter-card">
        <h5 class="mb-3"><i class="fas fa-filter me-2"></i>ตัวกรองข้อมูล</h5>
        <form method="GET" action="<?= site_url('System_reports/complain') ?>" id="filterForm">
            <div class="filter-grid">
                <div class="form-group">
                    <label class="form-label">สถานะ:</label>
                    <select class="form-select" name="status">
                        <option value="">ทั้งหมด</option>
                        <?php foreach ($status_options as $status): ?>
                            <option value="<?= $status['complain_status'] ?>" 
                                    <?= ($filters['status'] ?? '') == $status['complain_status'] ? 'selected' : '' ?>>
                                <?= $status['complain_status'] ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>

                <div class="form-group">
                    <label class="form-label">ประเภท:</label>
                    <select class="form-select" name="type">
                        <option value="">ทั้งหมด</option>
                        <?php foreach ($type_options as $type): ?>
                            <option value="<?= $type['complain_type'] ?>" 
                                    <?= ($filters['type'] ?? '') == $type['complain_type'] ? 'selected' : '' ?>>
                                <?= $type['complain_type'] ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>

                <div class="form-group">
                    <label class="form-label">วันที่เริ่มต้น:</label>
                    <input type="date" class="form-control" name="date_from" 
                           value="<?= $filters['date_from'] ?? '' ?>">
                </div>

                <div class="form-group">
                    <label class="form-label">วันที่สิ้นสุด:</label>
                    <input type="date" class="form-control" name="date_to" 
                           value="<?= $filters['date_to'] ?? '' ?>">
                </div>

                <div class="form-group">
                    <label class="form-label">ค้นหา:</label>
                    <input type="text" class="form-control" name="search" 
                           placeholder="ค้นหาหัวข้อ, รายละเอียด, ผู้แจ้ง..."
                           value="<?= $filters['search'] ?? '' ?>">
                </div>
            </div>
            
            <div class="filter-actions">
                <button type="submit" class="btn btn-primary">
                    <i class="fas fa-search me-1"></i>ค้นหา
                </button>
                <a href="<?= site_url('System_reports/complain') ?>" class="btn btn-secondary">
                    <i class="fas fa-times me-1"></i>ล้างตัวกรอง
                </a>
                <a href="<?= site_url('System_reports/export_excel/complain') ?>" class="btn btn-success">
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
                        <i class="fas fa-exclamation-triangle me-2"></i>รายงาน Case ที่ไม่มีการอัพเดท
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
                        // ใช้ข้อมูลจาก pending_complains แทนถ้ามี ไม่งั้นใช้ complains
                        $complains_for_alerts = isset($pending_complains) ? $pending_complains : ($complains ?? []);

                        $today = new DateTime();
                        $warning_cases = [];   // 3-6 วัน
                        $danger_cases = [];    // 7-13 วัน  
                        $critical_cases = [];  // 14+ วัน

                        if (!empty($complains_for_alerts)) {
                            foreach ($complains_for_alerts as $complain) {
                                $complain_date = new DateTime($complain->complain_datesave);
                                $diff = $today->diff($complain_date);
                                $days_passed = $diff->days;
                                
                                $case_data = [
                                    'id' => $complain->complain_id,
                                    'days' => $days_passed,
                                    'topic' => $complain->complain_topic,
                                    'status' => $complain->complain_status,
                                    'date' => $complain->complain_datesave
                                ];
                                
                                if ($days_passed >= 14) {
                                    $critical_cases[] = $case_data;
                                } elseif ($days_passed >= 7) {
                                    $danger_cases[] = $case_data;
                                } elseif ($days_passed >= 3) {
                                    $warning_cases[] = $case_data;
                                }
                            }
                        }

                        // เรียงลำดับ
                        usort($critical_cases, function($a, $b) { return $b['days'] - $a['days']; });
                        usort($danger_cases, function($a, $b) { return $b['days'] - $a['days']; });
                        usort($warning_cases, function($a, $b) { return $b['days'] - $a['days']; });
                        ?>
                        
                        <div class="alert-summary-card warning" onclick="showCategoryDetails('warning')">
                            <div class="alert-icon">
                                <i class="fas fa-clock"></i>
                            </div>
                            <div class="alert-content">
                                <div class="alert-number"><?= count($warning_cases) ?></div>
                                <div class="alert-label">ค้าง 3-6 วัน</div>
                            </div>
                        </div>
                        
                        <div class="alert-summary-card danger" onclick="showCategoryDetails('danger')">
                            <div class="alert-icon">
                                <i class="fas fa-exclamation-triangle"></i>
                            </div>
                            <div class="alert-content">
                                <div class="alert-number"><?= count($danger_cases) ?></div>
                                <div class="alert-label">ค้าง 7-13 วัน</div>
                            </div>
                        </div>
                        
                        <div class="alert-summary-card critical" onclick="showCategoryDetails('critical')">
                            <div class="alert-icon">
                                <i class="fas fa-fire"></i>
                            </div>
                            <div class="alert-content">
                                <div class="alert-number"><?= count($critical_cases) ?></div>
                                <div class="alert-label">ค้าง 14+ วัน</div>
                            </div>
                        </div>
                    </div>
                    
                    <!-- Detailed Alerts List -->
                    <div class="alerts-list">
                        <div class="alerts-header">
                            <h6 class="mb-0">Case ที่ต้องติดตาม</h6>
                            <small class="text-muted">อัพเดทล่าสุด: <?= date('d/m/Y H:i') ?></small>
                        </div>
                        
                        <div class="alerts-items">
                            <?php
                            // แสดง Critical Cases ก่อน (14+ วัน)
                            foreach (array_slice($critical_cases, 0, 3) as $alert):
                            ?>
                                <div class="alert-item critical" onclick="goToCase('<?= $alert['id'] ?>')">
                                    <div class="alert-item-icon">
                                        <i class="fas fa-fire"></i>
                                    </div>
                                    <div class="alert-item-content">
                                        <div class="alert-item-header">
                                            <span class="alert-case-id">#<?= $alert['id'] ?></span>
                                            <span class="alert-days">ค้าง <?= $alert['days'] ?> วัน</span>
                                        </div>
                                        <div class="alert-item-title" title="<?= htmlspecialchars($alert['topic'] ?? '') ?>">
                                            <?= htmlspecialchars(mb_substr($alert['topic'] ?? '', 0, 30)) ?><?= mb_strlen($alert['topic'] ?? '') > 30 ? '...' : '' ?>
                                        </div>
                                        <div class="alert-item-status">สถานะ: <?= htmlspecialchars($alert['status'] ?? '') ?></div>
                                    </div>
                                    <div class="alert-item-action">
                                        <i class="fas fa-chevron-right"></i>
                                    </div>
                                </div>
                            <?php endforeach; ?>
                            
                            <?php
                            // แสดง Danger Cases (7-13 วัน)
                            foreach (array_slice($danger_cases, 0, 3) as $alert):
                            ?>
                                <div class="alert-item danger" onclick="goToCase('<?= $alert['id'] ?>')">
                                    <div class="alert-item-icon">
                                        <i class="fas fa-exclamation-triangle"></i>
                                    </div>
                                    <div class="alert-item-content">
                                        <div class="alert-item-header">
                                            <span class="alert-case-id">#<?= $alert['id'] ?></span>
                                            <span class="alert-days">ค้าง <?= $alert['days'] ?> วัน</span>
                                        </div>
                                        <div class="alert-item-title" title="<?= htmlspecialchars($alert['topic']) ?>">
                                            <?= htmlspecialchars(mb_substr($alert['topic'], 0, 30)) ?><?= mb_strlen($alert['topic']) > 30 ? '...' : '' ?>
                                        </div>
                                        <div class="alert-item-status">สถานะ: <?= $alert['status'] ?></div>
                                    </div>
                                    <div class="alert-item-action">
                                        <i class="fas fa-chevron-right"></i>
                                    </div>
                                </div>
                            <?php endforeach; ?>
                            
                            <?php
                            // แสดง Warning Cases (3-6 วัน) แค่ 2 รายการ
                            foreach (array_slice($warning_cases, 0, 2) as $alert):
                            ?>
                                <div class="alert-item warning" onclick="goToCase('<?= $alert['id'] ?>')">
                                    <div class="alert-item-icon">
                                        <i class="fas fa-clock"></i>
                                    </div>
                                    <div class="alert-item-content">
                                        <div class="alert-item-header">
                                            <span class="alert-case-id">#<?= $alert['id'] ?></span>
                                            <span class="alert-days">ค้าง <?= $alert['days'] ?> วัน</span>
                                        </div>
                                        <div class="alert-item-title" title="<?= htmlspecialchars($alert['topic']) ?>">
                                            <?= htmlspecialchars(mb_substr($alert['topic'], 0, 30)) ?><?= mb_strlen($alert['topic']) > 30 ? '...' : '' ?>
                                        </div>
                                        <div class="alert-item-status">สถานะ: <?= $alert['status'] ?></div>
                                    </div>
                                    <div class="alert-item-action">
                                        <i class="fas fa-chevron-right"></i>
                                    </div>
                                </div>
                            <?php endforeach; ?>
                            
                            <?php
                            $total_alerts = count($critical_cases) + count($danger_cases) + count($warning_cases);
                            $shown_alerts = min(3, count($critical_cases)) + min(3, count($danger_cases)) + min(2, count($warning_cases));
                            
                            if ($total_alerts > $shown_alerts):
                            ?>
                                <div class="alert-item-more">
                                    <button class="btn btn-sm btn-outline-secondary w-100" onclick="showAllAlerts()">
                                        <i class="fas fa-plus me-1"></i>ดูอีก <?= $total_alerts - $shown_alerts ?> รายการ
                                    </button>
                                </div>
                            <?php endif; ?>
                            
                            <?php if ($total_alerts == 0): ?>
                                <div class="alert-item-empty">
                                    <div class="text-center py-3">
                                        <i class="fas fa-check-circle text-success fa-2x mb-2"></i>
                                        <p class="text-muted mb-0">ไม่มี Case ที่ค้างนาน</p>
                                        <small class="text-muted">ทุกเรื่องอยู่ในกำหนดเวลา</small>
                                    </div>
                                </div>
                            <?php endif; ?>
                        </div>
                        
                        <div class="alerts-footer">
                            <button class="btn btn-sm btn-primary" onclick="showAllAlerts()">
                                <i class="fas fa-list me-1"></i>ดูรายการทั้งหมด (<?= $total_alerts ?>)
                            </button>
                            <button class="btn btn-sm btn-success" onclick="exportAlerts()">
                                <i class="fas fa-download me-1"></i>Export
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
                    $recent_trends = array_slice($complain_trends, -15);
                    foreach($recent_trends as $trend): 
                    ?>
                        <div class="trend-item">
                            <div class="trend-date"><?= date('d/m/Y', strtotime($trend->date)) ?></div>
                            <div class="trend-count"><?= number_format($trend->count) ?> เรื่อง</div>
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
                <i class="fas fa-list me-2"></i>รายการเรื่องร้องเรียน
            </h5>
            <div class="table-actions">
                <button class="btn btn-outline-primary btn-sm" onclick="safeRefreshTable()">
                    <i class="fas fa-sync-alt me-1"></i>รีเฟรช
                </button>
            </div>
        </div>
        
        <div class="table-responsive">
            <!-- Cases Container -->
            <?php if (empty($complains)): ?>
                <div class="case-container">
                    <div class="text-center py-5">
                        <i class="fas fa-inbox fa-4x text-muted mb-3"></i>
                        <h5 class="text-muted">ไม่พบข้อมูลเรื่องร้องเรียน</h5>
                        <p class="text-muted">กรุณาลองใช้ตัวกรองอื่น หรือเพิ่มข้อมูลใหม่</p>
                    </div>
                </div>
            <?php else: ?>
                <?php foreach ($complains as $complain): ?>
                    <div class="case-container" data-case-id="<?= $complain->complain_id ?>">
                        <!-- Case Header -->
                        <div class="case-header">
                            <i class="fas fa-file-alt"></i>
                            <span>เรื่องร้องเรียน</span>
                            <span class="case-number">#<?= $complain->complain_id ?></span>
                        </div>
                        
                        <!-- Case Content -->
                        <table class="table mb-0">
                            <thead class="d-none">
                                <tr>
                                    <th style="width: 80px;">รหัส</th>
                                    <th style="width: 120px;">วันที่แจ้ง</th>
                                    <th style="width: 130px;">สถานะ</th>
                                    <th style="width: 100px;">รูปภาพ</th>
                                    <th style="width: 200px;">หัวข้อ</th>
                                    <th style="width: 250px;">รายละเอียด</th>
                                    <th style="width: 120px;">ผู้แจ้ง</th>
                                    <th style="width: 100px;">เบอร์ติดต่อ</th>
                                    <th style="width: 220px;">จัดการ</th>
                                </tr>
                            </thead>
                            <tbody>
                                <!-- Case Data Row -->
                                <tr class="case-data-row">
                                    <td class="fw-bold"><?= $complain->complain_id ?></td>
                                    <td>
                                        <small>
                                            <?= date('d/m/Y', strtotime($complain->complain_datesave . '+543 years')) ?><br>
                                            <?= date('H:i', strtotime($complain->complain_datesave)) ?> น.
                                        </small>
                                    </td>
                                    <td>
                                        <span class="status-badge <?= get_status_class($complain->complain_status) ?>">
                                            <?= $complain->complain_status ?>
                                        </span>
                                    </td>
                                    <td>
                                        <div class="complain-images">
                                            <?php if (!empty($complain->images) && is_array($complain->images)): ?>
                                                <?php foreach (array_slice($complain->images, 0, 2) as $image): ?>
                                                    <?php if (is_object($image) && isset($image->complain_img_img)): ?>
                                                        <img src="<?= base_url('docs/complain/' . $image->complain_img_img) ?>" 
                                                             alt="รูปภาพ" class="complain-image"
                                                             onclick="window.complainManager.showImageModal('<?= base_url('docs/complain/' . $image->complain_img_img) ?>')">
                                                    <?php endif; ?>
                                                <?php endforeach; ?>
                                                <?php if (count($complain->images) > 2): ?>
                                                    <span class="badge bg-secondary">+<?= count($complain->images) - 2 ?></span>
                                                <?php endif; ?>
                                            <?php else: ?>
                                                <span class="text-muted small">ไม่มีรูปภาพ</span>
                                            <?php endif; ?>
                                        </div>
                                    </td>
                                    <td>
                                        <div class="text-truncate-2" title="<?= htmlspecialchars($complain->complain_topic ?? '') ?>">
                                            <?= htmlspecialchars($complain->complain_topic ?? '') ?>
                                        </div>
                                    </td>
                                    <td>
                                        <div class="text-truncate-2" title="<?= htmlspecialchars($complain->complain_detail ?? '') ?>">
                                            <?= htmlspecialchars($complain->complain_detail ?? '') ?>
                                        </div>
                                    </td>
                                    <td><?= htmlspecialchars($complain->complain_by ?? '') ?></td>
                                    <td><?= htmlspecialchars($complain->complain_phone ?? '') ?></td>
                                    <td>
                                        <div class="action-buttons">
                                            <a href="<?= site_url('System_reports/complain_detail/' . $complain->complain_id) ?>" 
                                               class="btn-action view" title="ดูรายละเอียด">
                                                <i class="fas fa-eye"></i>ดู
                                            </a>
                                            
                                            <!-- 🆕 ปุ่มลบ - เฉพาะ System Admin และ Super Admin -->
                                            <?php if ($user_permissions['can_delete']): ?>
                                                <button class="btn-action delete" 
                                                        onclick="window.complainManager.deleteComplain(<?= $complain->complain_id ?>)"
                                                        title="ลบข้อมูล">
                                                    <i class="fas fa-trash"></i>ลบ
                                                </button>
                                            <?php else: ?>
                                                <div class="permission-tooltip">
                                                    <button class="btn-action delete disabled-by-permission" 
                                                            title="ไม่มีสิทธิ์ลบ - เฉพาะ System Admin และ Super Admin">
                                                        <i class="fas fa-trash"></i>ลบ
                                                    </button>
                                                    <span class="tooltiptext">เฉพาะ System Admin และ Super Admin เท่านั้นที่สามารถลบได้</span>
                                                </div>
                                            <?php endif; ?>
                                        </div>
                                    </td>
                                </tr>
                                
                                <!-- Case Status Management Row -->
                                <tr class="case-status-row">
                                    <td colspan="9" class="status-cell">
                                        <?php if ($user_permissions['can_manage_status']): ?>
                                            <div class="status-update-row">
                                                <div class="status-label">
                                                    <i class="fas fa-sync-alt"></i>
                                                    อัพเดทสถานะเรื่องร้องเรียน #<?= $complain->complain_id ?>
                                                </div>
                                                <div class="status-buttons-container">
                                                    <?php 
                                                    $current_status = $complain->complain_status;
                                                    
                                                    $workflow_rules = [
                                                        'รอรับเรื่อง' => [
                                                            'enabled' => ['รับเรื่องแล้ว', 'ยกเลิก'],
                                                            'disabled' => ['รอรับเรื่อง', 'รอดำเนินการ', 'กำลังดำเนินการ', 'ดำเนินการเรียบร้อย']
                                                        ],
                                                        'รับเรื่องแล้ว' => [
                                                            'enabled' => ['รอดำเนินการ', 'กำลังดำเนินการ', 'ดำเนินการเรียบร้อย', 'ยกเลิก'],
                                                            'disabled' => ['รอรับเรื่อง', 'รับเรื่องแล้ว']
                                                        ],
                                                        'รอดำเนินการ' => [
                                                            'enabled' => ['กำลังดำเนินการ', 'ดำเนินการเรียบร้อย', 'ยกเลิก'],
                                                            'disabled' => ['รอรับเรื่อง', 'รับเรื่องแล้ว', 'รอดำเนินการ']
                                                        ],
                                                        'กำลังดำเนินการ' => [
                                                            'enabled' => ['ดำเนินการเรียบร้อย', 'ยกเลิก'],
                                                            'disabled' => ['รอรับเรื่อง', 'รับเรื่องแล้ว', 'รอดำเนินการ', 'กำลังดำเนินการ']
                                                        ],
                                                        'ดำเนินการเรียบร้อย' => [
                                                            'enabled' => [],
                                                            'disabled' => ['รอรับเรื่อง', 'รับเรื่องแล้ว', 'รอดำเนินการ', 'กำลังดำเนินการ', 'ดำเนินการเรียบร้อย', 'ยกเลิก']
                                                        ],
                                                        'ยกเลิก' => [
                                                            'enabled' => [],
                                                            'disabled' => ['รอรับเรื่อง', 'รับเรื่องแล้ว', 'รอดำเนินการ', 'กำลังดำเนินการ', 'ดำเนินการเรียบร้อย', 'ยกเลิก']
                                                        ]
                                                    ];
                                                    
                                                    $current_rules = $workflow_rules[$current_status] ?? $workflow_rules['รอรับเรื่อง'];
                                                    $enabled_statuses = $current_rules['enabled'];
                                                    $disabled_statuses = $current_rules['disabled'];
                                                    
                                                    $status_buttons = [
                                                        ['รอรับเรื่อง', 'waiting', 'fas fa-hourglass-start'],
                                                        ['รับเรื่องแล้ว', 'received', 'fas fa-inbox'],
                                                        ['รอดำเนินการ', 'pending', 'fas fa-clock'],
                                                        ['กำลังดำเนินการ', 'processing', 'fas fa-cogs'],
                                                        ['ดำเนินการเรียบร้อย', 'completed', 'fas fa-check-circle'],
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
                                                            $onclick_code = 'onclick="window.complainManager.showEnhancedModal(' . $complain->complain_id . ', \'' . $current_status . '\', \'' . addslashes($status_text) . '\')"';
                                                        } else {
                                                            $button_classes .= ' disabled';
                                                            if (in_array($status_text, $disabled_statuses)) {
                                                                if (in_array($status_text, ['รอรับเรื่อง', 'รับเรื่องแล้ว', 'รอดำเนินการ', 'กำลังดำเนินการ'])) {
                                                                    $tooltip_text = 'ผ่านขั้นตอนนี้แล้ว';
                                                                } else {
                                                                    $tooltip_text = 'ไม่สามารถใช้งานได้ในสถานะปัจจุบัน';
                                                                }
                                                            }
                                                        }
                                                    ?>
                                                        <button class="<?= $button_classes ?>"
                                                                <?= $is_disabled ? 'disabled' : '' ?>
                                                                <?= $onclick_code ?>
                                                                title="<?= $tooltip_text ?>">
                                                            <i class="<?= $status_icon ?>"></i>
                                                            <span><?= $status_text ?></span>
                                                        </button>
                                                    <?php endforeach; ?>
                                                </div>
                                            </div>
                                        <?php else: ?>
                                            <div class="no-permission-overlay">
                                                <div class="status-update-row">
                                                    <div class="status-label">
                                                        <i class="fas fa-lock"></i>
                                                        ไม่มีสิทธิ์จัดการสถานะเรื่องร้องเรียน #<?= $complain->complain_id ?>
                                                    </div>
                                                    <div class="text-center">
                                                        <small class="text-muted">
                                                            <?php if ($user_permissions['position_id'] == 3): ?>
                                                                User Admin ต้องมี Grant ID: 105 เพื่อจัดการสถานะได้
                                                            <?php else: ?>
                                                                ต้องเป็น System Admin, Super Admin หรือ User Admin ที่มี Grant ID: 105
                                                            <?php endif; ?>
                                                        </small>
                                                    </div>
                                                </div>
                                            </div>
                                        <?php endif; ?>
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                <?php endforeach; ?>
            <?php endif; ?>

            <!-- Table Headers for Reference (Hidden) -->
            <table class="table d-none">
                <thead>
                    <tr>
                        <th style="width: 80px;">รหัส</th>
                        <th style="width: 120px;">วันที่แจ้ง</th>
                        <th style="width: 130px;">สถานะ</th>
                        <th style="width: 100px;">รูปภาพ</th>
                        <th style="width: 200px;">หัวข้อ</th>
                        <th style="width: 250px;">รายละเอียด</th>
                        <th style="width: 120px;">ผู้แจ้ง</th>
                        <th style="width: 100px;">เบอร์ติดต่อ</th>
                        <th style="width: 220px;">จัดการ</th>
                    </tr>
                </thead>
            </table>
        </div>

        <!-- Pagination -->
        <?php if ($total_rows > 0): ?>
            <div class="pagination-wrapper">
                <div class="pagination-info">
                    แสดง <?= number_format(($current_page - 1) * $per_page + 1) ?> - 
                    <?= number_format(min($current_page * $per_page, $total_rows)) ?> 
                    จาก <?= number_format($total_rows) ?> รายการ
                </div>
                <div>
                    <?= $pagination ?>
                </div>
            </div>
        <?php endif; ?>
    </div>
</div>

<!-- Image Modal -->
<div class="modal fade" id="imageModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">
                    <i class="fas fa-image me-2"></i>รูปภาพประกอบ
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body text-center">
                <img id="modalImage" src="" alt="รูปภาพ" class="img-fluid" style="max-height: 70vh; border-radius: 8px;">
            </div>
        </div>
    </div>
</div>




<!-- Category Management Modal -->
<div class="modal fade" id="categoryModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header bg-primary text-white">
                <h5 class="modal-title">
                    <i class="fas fa-tags me-2"></i>จัดการหมวดหมู่เรื่องร้องเรียน
                </h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <!-- Add Category Form -->
                <div class="card mb-4">
                    <div class="card-header">
                        <h6 class="mb-0">
                            <i class="fas fa-plus me-2"></i>เพิ่มหมวดหมู่ใหม่
                        </h6>
                    </div>
                    <div class="card-body">
                        <form id="categoryForm">
                            <input type="hidden" id="categoryId" name="cat_id">
                            <div class="row">
                                <div class="col-md-6">
                                    <label class="form-label">ชื่อหมวดหมู่ <span class="text-danger">*</span></label>
                                    <input type="text" class="form-control" id="categoryName" name="cat_name" required maxlength="100">
                                </div>
                                <div class="col-md-3">
    <label class="form-label">ไอคอน</label>
    <div class="icon-selector">
        <button type="button" class="btn btn-outline-secondary w-100" id="iconSelectorBtn" onclick="window.categoryManager.showIconSelector()">
            <i id="selectedIcon" class="fas fa-exclamation-circle me-2"></i>
            <span id="selectedIconText">เลือกไอคอน</span>
        </button>
        <input type="hidden" id="categoryIcon" name="cat_icon" value="fas fa-exclamation-circle">
    </div>
</div>
                                <div class="col-md-3">
                                    <label class="form-label">สี</label>
                                    <input type="color" class="form-control form-control-color" id="categoryColor" name="cat_color" value="#e55a2b">
                                </div>
                            </div>
                            <div class="row mt-3">
                                <div class="col-md-6">
                                    <label class="form-label">ลำดับการแสดง</label>
                                    <input type="number" class="form-control" id="categoryOrder" name="cat_order" value="0" min="0">
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label">สถานะ</label>
                                    <select class="form-select" id="categoryStatus" name="cat_status">
                                        <option value="1">เปิดใช้งาน</option>
                                        <option value="0">ปิดใช้งาน</option>
                                    </select>
                                </div>
                            </div>
                            <div class="mt-3">
                                <button type="submit" class="btn btn-primary" id="saveCategoryBtn">
                                    <i class="fas fa-save me-2"></i>บันทึก
                                </button>
                                <button type="button" class="btn btn-secondary" onclick="window.categoryManager.resetForm()">
                                    <i class="fas fa-times me-2"></i>ยกเลิก
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
                
                <!-- Categories List -->
                <div class="card">
                    <div class="card-header">
                        <h6 class="mb-0">
                            <i class="fas fa-list me-2"></i>รายการหมวดหมู่ที่มีอยู่
                        </h6>
                    </div>
                    <div class="card-body">
                        <div class="table-responsive">
                            <table class="table table-hover" id="categoriesTable">
                                <thead>
                                    <tr>
                                        <th width="10%">ลำดับ</th>
                                        <th width="30%">ชื่อหมวดหมู่</th>
                                        <th width="15%">ไอคอน</th>
                                        <th width="10%">สี</th>
                                        <th width="15%">สถานะ</th>
                                        <th width="20%">จัดการ</th>
                                    </tr>
                                </thead>
                                <tbody id="categoriesTableBody">
                                    <tr>
                                        <td colspan="6" class="text-center text-muted">กำลังโหลดข้อมูล...</td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">ปิด</button>
            </div>
        </div>
    </div>
</div>




<!-- Icon Selector Modal -->
<div class="modal fade" id="iconSelectorModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">
                    <i class="fas fa-icons me-2"></i>เลือกไอคอน
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <div class="row" id="iconGrid">
                    <!-- Icons will be populated by JavaScript -->
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">ปิด</button>
            </div>
        </div>
    </div>
</div>





<?php
function get_status_class($status) {
    switch($status) {
        case 'รอรับเรื่อง': return 'waiting';
        case 'รับเรื่องแล้ว': return 'received';
        case 'รอดำเนินการ': return 'pending';
        case 'กำลังดำเนินการ': return 'processing'; 
        case 'ดำเนินการเรียบร้อย': return 'completed';
        case 'ยกเลิก': return 'cancelled';
        default: return 'waiting';
    }
}
?>

<!-- Enhanced Status Update Modal with Auto Compression -->
<div class="modal fade" id="statusUpdateModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog status-modal-enhanced">
        <div class="modal-content">
            <div class="modal-header bg-gradient-primary text-white">
                <h5 class="modal-title">
                    <i class="fas fa-sync-alt me-2"></i>ยืนยันการเปลี่ยนสถานะเรื่องร้องเรียน
                </h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <form id="enhancedStatusForm" enctype="multipart/form-data">
                    <input type="hidden" id="modalComplainId" name="complain_id">
                    <input type="hidden" id="modalNewStatus" name="new_status">
                    
                    <!-- Section 1: Status Change Summary -->
                    <div class="modal-section">
                        <h6 class="section-title">
                            <i class="fas fa-info-circle"></i>
                            ข้อมูลการเปลี่ยนสถานะ
                        </h6>
                        <div class="status-change-summary">
                            <div class="row">
                                <div class="col-md-6">
                                    <label class="form-label">หมายเลขเรื่องร้องเรียน:</label>
                                    <div class="fw-bold text-primary" id="modalComplainIdDisplay">#</div>
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label">วันที่แจ้ง:</label>
                                    <div id="modalComplainDate">-</div>
                                </div>
                            </div>
                            
                            <div class="status-arrow">
                                <span class="current-status" id="modalCurrentStatus">สถานะปัจจุบัน</span>
                                <i class="fas fa-arrow-right mx-3"></i>
                                <span class="new-status" id="modalNewStatusDisplay">สถานะใหม่</span>
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
                            <textarea class="form-control" id="modalStatusNote" name="status_note" 
                                      rows="4" maxlength="500"
                                      placeholder="ระบุรายละเอียดการดำเนินการ ปัญหาที่พบ หรือข้อมูลเพิ่มเติม..."></textarea>
                            <div class="character-counter">
                                <span id="noteCharCount">0</span>/500 ตัวอักษร
                            </div>
                        </div>
                    </div>
                    
                    <!-- Section 3: Image Upload with Auto Compression -->
                    <div class="modal-section">
                        <h6 class="section-title">
                            <i class="fas fa-images"></i>
                            รูปภาพประกอบ
                        </h6>
                        
                        <div class="image-upload-container" id="imageUploadArea">
                            <div class="upload-icon">
                                <i class="fas fa-cloud-upload-alt"></i>
                            </div>
                            <p class="mb-2"><strong>คลิกเพื่อเลือกรูปภาพ</strong> หรือลากไฟล์มาวางที่นี่</p>
                            <p class="upload-limit-info">
                                รองรับไฟล์: JPG, PNG, GIF (ขนาดไม่เกิน 10MB ต่อรูป)
                                <br>จำนวนไม่เกิน 5 รูป
                                <br><small class="text-success">💡 รูปภาพจะถูกบีบอัดอัตโนมัติเพื่อประหยัดพื้นที่</small>
                            </p>
                            <input type="file" id="statusImages" name="status_images[]" 
                                   multiple accept="image/*" class="file-input-hidden">
                        </div>
                        
                        <!-- Auto compression processing indicator -->
                        <div class="compression-processing" id="compressionProcessing">
                            <div class="spinner"></div>
                            กำลังบีบอัดรูปภาพ...
                        </div>
                        
                        <div class="upload-progress" id="uploadProgress">
                            <div class="progress">
                                <div class="progress-bar" id="progressBar" style="width: 0%"></div>
                            </div>
                            <div class="text-center mt-2">
                                <small id="progressText">กำลังอัปโหลด...</small>
                            </div>
                        </div>
                        
                        <div class="image-preview-container" id="imagePreviewContainer"></div>
                        
                        <div class="image-upload-stats">
                            <span>รูปภาพที่เลือก: <span id="imageCount">0</span>/5</span>
                            <span>ขนาดรวม: <span id="totalSize">0 KB</span></span>
                            <span class="text-success">ประหยัด: <span id="totalSavings">0 KB</span></span>
                        </div>
                    </div>
                </form>
            </div>
            
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                    <i class="fas fa-times me-1"></i>ยกเลิก
                </button>
                <button type="button" class="btn btn-primary" id="confirmStatusUpdate">
                    <i class="fas fa-check me-1"></i>ยืนยันการเปลี่ยนสถานะ
                </button>
            </div>
        </div>
    </div>
</div>

<script>
// 🆕 เพิ่มการตรวจสอบสิทธิ์ใน JavaScript
(function() {
    'use strict';
    
    // ✅ ข้อมูลสิทธิ์จาก PHP
    window.userPermissions = {
        can_view_reports: <?= json_encode($user_permissions['can_view_reports']) ?>,
        can_manage_status: <?= json_encode($user_permissions['can_manage_status']) ?>,
        can_delete: <?= json_encode($user_permissions['can_delete']) ?>,
        user_role: <?= json_encode($user_permissions['user_role']) ?>,
        position_id: <?= json_encode($user_permissions['position_id']) ?>,
        reason: <?= json_encode($user_permissions['reason']) ?>
    };
    
    // console.log('🔐 User Permissions:', window.userPermissions);
    
    // ✅ ฟังก์ชันตรวจสอบสิทธิ์ก่อนดำเนินการ
    window.checkPermissionBeforeAction = function(action) {
        switch(action) {
            case 'manage_status':
                if (!window.userPermissions.can_manage_status) {
                    let message = 'ไม่มีสิทธิ์จัดการสถานะเรื่องร้องเรียน';
                    
                    if (window.userPermissions.position_id == 3) { // User Admin
                        message += '\n\nUser Admin ต้องมี Grant ID: 105 เพื่อจัดการสถานะได้';
                    } else {
                        message += '\n\nต้องเป็น System Admin, Super Admin หรือ User Admin ที่มี Grant ID: 105';
                    }
                    
                    if (typeof Swal !== 'undefined') {
                        Swal.fire({
                            title: 'ไม่มีสิทธิ์เข้าถึง',
                            text: message,
                            icon: 'warning',
                            confirmButtonText: 'ตกลง',
                            footer: '<small>สิทธิ์ปัจจุบัน: ' + window.userPermissions.user_role + '</small>'
                        });
                    } else {
                        alert(message);
                    }
                    return false;
                }
                break;
                
            case 'delete':
                if (!window.userPermissions.can_delete) {
                    let message = 'ไม่มีสิทธิ์ลบเรื่องร้องเรียน\n\nเฉพาะ System Admin และ Super Admin เท่านั้นที่สามารถลบได้';
                    
                    if (typeof Swal !== 'undefined') {
                        Swal.fire({
                            title: 'ไม่มีสิทธิ์เข้าถึง',
                            text: message,
                            icon: 'error',
                            confirmButtonText: 'ตกลง',
                            footer: '<small>สิทธิ์ปัจจุบัน: ' + window.userPermissions.user_role + '</small>'
                        });
                    } else {
                        alert(message);
                    }
                    return false;
                }
                break;
        }
        return true;
    };
    
    // ✅ ปรับปรุง ComplainManager ให้รองรับการตรวจสอบสิทธิ์
    if (typeof window.complainManager !== 'undefined') {
        // สำรองฟังก์ชันเดิม
        const originalQuickUpdateStatus = window.complainManager.quickUpdateStatus;
        const originalDeleteComplain = window.complainManager.deleteComplain;
        
        // ✅ Wrap ฟังก์ชันการจัดการสถานะ
        window.complainManager.quickUpdateStatus = function(complainId, newStatus) {
            if (!window.checkPermissionBeforeAction('manage_status')) {
                return;
            }
            return originalQuickUpdateStatus.call(this, complainId, newStatus);
        };
        
        // ✅ Wrap ฟังก์ชันการลบ
        window.complainManager.deleteComplain = function(complainId) {
            if (!window.checkPermissionBeforeAction('delete')) {
                return;
            }
            return originalDeleteComplain.call(this, complainId);
        };
        
       // console.log('✅ ComplainManager wrapped with permission checks');
    }
    
    // ✅ ป้องกันการกดปุ่มที่ไม่มีสิทธิ์
    document.addEventListener('DOMContentLoaded', function() {
        // ปิดใช้งานปุ่มที่ไม่มีสิทธิ์
        if (!window.userPermissions.can_manage_status) {
            // ปิดปุ่มจัดการสถานะ
            const statusButtons = document.querySelectorAll('.btn-status-row:not(.current):not(.disabled)');
            statusButtons.forEach(function(btn) {
                btn.classList.add('disabled-by-permission');
                btn.setAttribute('disabled', 'disabled');
                btn.title = 'ไม่มีสิทธิ์จัดการสถานะ - ' + window.userPermissions.reason;
            });
        }
        
        if (!window.userPermissions.can_delete) {
            // ปิดปุ่มลบ
            const deleteButtons = document.querySelectorAll('.btn-action.delete:not(.disabled-by-permission)');
            deleteButtons.forEach(function(btn) {
                btn.classList.add('disabled-by-permission');
                btn.setAttribute('disabled', 'disabled');
                btn.onclick = null;
                btn.title = 'ไม่มีสิทธิ์ลบ - เฉพาะ System Admin และ Super Admin';
            });
        }
        
        console.log('🔒 Permission-based UI updates applied');
    });
    
    })();

// ✅ ฟังก์ชันล้างข้อมูลเรื่องร้องเรียนทั้งหมด (เฉพาะ System Admin)
window.clearAllComplainData = function() {
    console.log('🗑️ Clear All Complain Data requested');
    
    // ตรวจสอบสิทธิ์ System Admin
    if (!window.userPermissions || 
        window.userPermissions.position_id != 1 || 
        window.userPermissions.user_role !== 'System Admin') {
        
        if (typeof Swal !== 'undefined') {
            Swal.fire({
                title: 'ไม่มีสิทธิ์เข้าถึง',
                text: 'การล้างข้อมูลทั้งหมดสามารถทำได้เฉพาะ System Admin เท่านั้น',
                icon: 'error',
                confirmButtonText: 'ตกลง',
                footer: '<small>สิทธิ์ปัจจุบัน: ' + (window.userPermissions?.user_role || 'ไม่ทราบ') + '</small>'
            });
        } else {
            alert('ไม่มีสิทธิ์เข้าถึง - เฉพาะ System Admin เท่านั้น\nสิทธิ์ปัจจุบัน: ' + (window.userPermissions?.user_role || 'ไม่ทราบ'));
        }
        return;
    }
    
    if (typeof Swal !== 'undefined') {
        // ขั้นตอนที่ 1: คำเตือนเบื้องต้น
        Swal.fire({
            title: '⚠️ คำเตือน: การล้างข้อมูลทั้งหมด',
            html: `
                <div style="text-align: left; padding: 20px;">
                    <p><strong>คุณกำลังจะลบข้อมูลเรื่องร้องเรียนทั้งหมด</strong></p>
                    <ul style="color: #dc2626;">
                        <li>ข้อมูลที่จะถูกลบ: เรื่องร้องเรียนทั้งหมด</li>
                        <li>รูปภาพประกอบทั้งหมด</li>
                        <li>ประวัติการดำเนินงาน</li>
                        <li>ไฟล์แนบต่างๆ</li>
                    </ul>
                    <p style="color: #dc2626; font-weight: bold;">
                        ⚠️ การดำเนินการนี้ไม่สามารถยกเลิกได้!
                    </p>
                </div>
            `,
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#dc2626',
            cancelButtonColor: '#6b7280',
            confirmButtonText: 'ดำเนินการต่อ',
            cancelButtonText: 'ยกเลิก',
            reverseButtons: true
        }).then((result) => {
            if (result.isConfirmed) {
                // ขั้นตอนที่ 2: ยืนยันครั้งสุดท้าย
                Swal.fire({
                    title: '🔒 ยืนยันครั้งสุดท้าย',
                    html: `
                        <div style="text-align: center; padding: 20px;">
                            <p style="font-size: 18px; color: #dc2626; font-weight: bold;">
                                พิมพ์ "DELETE ALL" เพื่อยืนยัน
                            </p>
                            <input type="text" id="confirmDeleteText" class="swal2-input" 
                                   placeholder="พิมพ์ DELETE ALL" 
                                   style="text-transform: uppercase;">
                            <p style="margin-top: 15px; color: #6b7280; font-size: 14px;">
                                การล้างข้อมูลนี้จะส่งผลต่อระบบทั้งหมด<br>
                                และไม่สามารถกู้คืนได้
                            </p>
                        </div>
                    `,
                    icon: 'error',
                    showCancelButton: true,
                    confirmButtonColor: '#dc2626',
                    cancelButtonColor: '#6b7280',
                    confirmButtonText: '🗑️ ลบทั้งหมด',
                    cancelButtonText: 'ยกเลิก',
                    reverseButtons: true,
                    preConfirm: () => {
                        const inputValue = document.getElementById('confirmDeleteText').value;
                        if (inputValue !== 'DELETE ALL') {
                            Swal.showValidationMessage('กรุณาพิมพ์ "DELETE ALL" เพื่อยืนยัน');
                            return false;
                        }
                        return true;
                    }
                }).then((finalResult) => {
                    if (finalResult.isConfirmed) {
                        performClearAllData();
                    }
                });
            }
        });
    } else {
        // Fallback สำหรับเบราว์เซอร์ที่ไม่มี SweetAlert2
        const firstConfirm = confirm('⚠️ คำเตือน: คุณต้องการลบข้อมูลเรื่องร้องเรียนทั้งหมดหรือไม่?\n\nการดำเนินการนี้ไม่สามารถยกเลิกได้!');
        if (firstConfirm) {
            const finalConfirm = prompt('พิมพ์ "DELETE ALL" เพื่อยืนยันการลบ:');
            if (finalConfirm === 'DELETE ALL') {
                performClearAllData();
            } else {
                alert('การยืนยันไม่ถูกต้อง - ยกเลิกการลบข้อมูล');
            }
        }
    }
};

// ฟังก์ชันดำเนินการล้างข้อมูลจริง
function performClearAllData() {
    console.log('🚨 Performing clear all complain data...');
    
    // แสดง loading
    if (typeof Swal !== 'undefined') {
        Swal.fire({
            title: 'กำลังล้างข้อมูล...',
            html: `
                <div style="text-align: center;">
                    <div class="spinner-border text-danger" role="status">
                        <span class="visually-hidden">Loading...</span>
                    </div>
                    <p style="margin-top: 15px; color: #6b7280;">
                        กรุณารอสักครู่ ระบบกำลังลบข้อมูลทั้งหมด
                    </p>
                    <small style="color: #dc2626;">
                        ⚠️ ห้ามปิดหน้าเว็บระหว่างดำเนินการ
                    </small>
                </div>
            `,
            allowOutsideClick: false,
            allowEscapeKey: false,
            showConfirmButton: false,
            didOpen: () => {
                Swal.showLoading();
            }
        });
    }
    
    // ส่งคำขอไปยัง Controller
    fetch('<?= site_url("System_reports/clear_all_complain_data") ?>', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-Requested-With': 'XMLHttpRequest'
        },
        body: JSON.stringify({
            confirm_action: 'DELETE_ALL_COMPLAINS',
            user_role: window.userPermissions.user_role,
            timestamp: new Date().toISOString()
        })
    })
    .then(response => response.json())
    .then(data => {
        if (typeof Swal !== 'undefined') {
            if (data.success) {
                Swal.fire({
                    title: '✅ ล้างข้อมูลสำเร็จ!',
                    html: `
                        <div style="text-align: left;">
                            <p><strong>ข้อมูลที่ถูกลบ:</strong></p>
                            <ul>
                                <li>เรื่องร้องเรียน: ${data.deleted_counts?.complains || 0} รายการ</li>
                                <li>รูปภาพ: ${data.deleted_counts?.images || 0} ไฟล์</li>
                                <li>ประวัติการดำเนินงาน: ${data.deleted_counts?.details || 0} รายการ</li>
                                <li>รูปภาพสถานะ: ${data.deleted_counts?.status_images || 0} ไฟล์</li>
                                <li>การแจ้งเตือน: ${data.deleted_counts?.notifications || 0} รายการ</li>
                                <li>สถานะการอ่านแจ้งเตือน: ${data.deleted_counts?.notification_reads || 0} รายการ</li>
                            </ul>
                            <p style="color: #059669;"><strong>ระบบพร้อมใช้งานใหม่</strong></p>
                        </div>
                    `,
                    icon: 'success',
                    confirmButtonText: 'รีเฟรชหน้า',
                    confirmButtonColor: '#059669'
                }).then(() => {
                    location.reload();
                });
            } else {
                Swal.fire({
                    title: '❌ เกิดข้อผิดพลาด',
                    text: data.message || 'ไม่สามารถล้างข้อมูลได้',
                    icon: 'error',
                    confirmButtonText: 'ตกลง',
                    confirmButtonColor: '#dc2626'
                });
            }
        } else {
            if (data.success) {
                alert('✅ ล้างข้อมูลสำเร็จ!\n\nเรื่องร้องเรียน: ' + (data.deleted_counts?.complains || 0) + ' รายการ\nการแจ้งเตือน: ' + (data.deleted_counts?.notifications || 0) + ' รายการ');
                location.reload();
            } else {
                alert('❌ เกิดข้อผิดพลาด: ' + (data.message || 'ไม่สามารถล้างข้อมูลได้'));
            }
        }
    })
    .catch(error => {
        console.error('Clear data error:', error);
        
        if (typeof Swal !== 'undefined') {
            Swal.fire({
                title: '❌ เกิดข้อผิดพลาดในการเชื่อมต่อ',
                text: 'ไม่สามารถติดต่อเซิร์ฟเวอร์ได้ กรุณาลองใหม่อีกครั้ง',
                icon: 'error',
                confirmButtonText: 'ตกลง',
                confirmButtonColor: '#dc2626'
            });
        } else {
            alert('❌ เกิดข้อผิดพลาดในการเชื่อมต่อ');
        }
    });
}

// ✅ ฟังก์ชันสำหรับแสดงข้อมูลสิทธิ์ (Debug)
window.showPermissionInfo = function() {
    if (typeof Swal !== 'undefined') {
        const permissionsHtml = `
            <div style="text-align: left;">
                <h6>ข้อมูลสิทธิ์ปัจจุบัน:</h6>
                <ul>
                    <li><strong>ตำแหน่ง:</strong> ${window.userPermissions.user_role}</li>
                    <li><strong>Position ID:</strong> ${window.userPermissions.position_id}</li>
                    <li><strong>ดูรายงาน:</strong> ${window.userPermissions.can_view_reports ? '✅ ได้' : '❌ ไม่ได้'}</li>
                    <li><strong>จัดการสถานะ:</strong> ${window.userPermissions.can_manage_status ? '✅ ได้' : '❌ ไม่ได้'}</li>
                    <li><strong>ลบข้อมูล:</strong> ${window.userPermissions.can_delete ? '✅ ได้' : '❌ ไม่ได้'}</li>
                </ul>
                <p><strong>หมายเหตุ:</strong> ${window.userPermissions.reason}</p>
            </div>
        `;
        
        Swal.fire({
            title: 'ข้อมูลสิทธิ์การใช้งาน',
            html: permissionsHtml,
            icon: 'info',
            confirmButtonText: 'ปิด'
        });
    } else {
        console.log('User Permissions:', window.userPermissions);
    }
};

console.log('🔐 Enhanced permission system loaded');
</script>





<!-- SweetAlert2 -->
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

<!-- JavaScript for Enhanced Modal with Auto Compression -->
<script>
// 🚀 Complete Complain Manager with Auto Image Compression
(function() {
    'use strict';
    
    // ✅ สร้าง object ทันทีเพื่อป้องกัน undefined
    window.complainManager = window.complainManager || {};
    
    // ✅ ฟังก์ชัน fallback สำหรับกรณี object ยังไม่พร้อม
    window.safeQuickUpdateStatus = function(complainId, newStatus) {
        console.log('🔄 safeQuickUpdateStatus called:', complainId, newStatus);
        
        if (window.complainManager && window.complainManager.quickUpdateStatus) {
            window.complainManager.quickUpdateStatus(complainId, newStatus);
        } else {
            console.warn('⚠️ complainManager not ready, initializing...');
            setTimeout(function() {
                if (window.complainManager && window.complainManager.quickUpdateStatus) {
                    window.complainManager.quickUpdateStatus(complainId, newStatus);
                } else {
                    alert('ระบบยังไม่พร้อม กรุณารีเฟรชหน้าเว็บแล้วลองใหม่');
                }
            }, 500);
        }
    };
    
    // ✅ ฟังก์ชัน helper อื่นๆ
    window.safeToggleCardsView = function() {
        if (window.complainManager && window.complainManager.toggleCardsView) {
            window.complainManager.toggleCardsView();
        } else {
            alert('ระบบยังไม่พร้อม กรุณารอสักครู่');
        }
    };
    
    window.safeRefreshTable = function() {
        if (window.complainManager && window.complainManager.refreshTable) {
            window.complainManager.refreshTable();
        } else {
            location.reload();
        }
    };
    
    // ✅ Complete Complain Manager Object
    window.complainManager = {
        // เก็บค่า PHP สำหรับใช้ใน JavaScript
        config: {
            updateStatusUrl: '<?= site_url("System_reports/update_complain_status_with_images") ?>',
            deleteComplainUrl: '<?= site_url("System_reports/delete_complain") ?>',
            complainDetailUrl: '<?= site_url("System_reports/complain_detail") ?>',
            editComplainUrl: '<?= site_url("System_reports/edit_complain") ?>'
        },
        
        // ✅ สถานะการโหลด
        isReady: false,
        isInitializing: false,
        
        // ✅ ฟังก์ชันตรวจสอบว่าพร้อมใช้งานหรือไม่
        ensureReady: function() {
            if (!this.isReady && !this.isInitializing) {
                console.warn('complainManager ยังไม่พร้อม กำลังเริ่มต้น...');
                this.init();
            }
            return this.isReady;
        },
        
        // ✅ ฟังก์ชันเริ่มต้น
        init: function() {
            if (this.isInitializing) {
                console.log('Already initializing...');
                return false;
            }
            
            this.isInitializing = true;
            
            try {
                console.log('🚀 Complain Manager - เริ่มต้นระบบ');
                
                // ตรวจสอบ jQuery
                if (typeof jQuery === 'undefined') {
                    console.warn('jQuery ยังไม่พร้อม จะลองใหม่...');
                    this.isInitializing = false;
                    var self = this;
                    setTimeout(function() { self.init(); }, 500);
                    return false;
                }
                
                // ✅ ตั้งค่าสถานะพร้อม
                this.isReady = true;
                this.isInitializing = false;
                
                this.startAutoRefresh();
                this.bindEvents();
                
                console.log('✅ Complain Manager - เริ่มต้นเสร็จสิ้น');
                return true;
                
            } catch (error) {
                console.error('❌ Error initializing complainManager:', error);
                this.isReady = false;
                this.isInitializing = false;
                return false;
            }
        },
        
        // ✅ Bind events
        bindEvents: function() {
            try {
                // ESC key to close modals
                $(document).on('keydown', function(e) {
                    if (e.key === 'Escape') {
                        $('.modal.show').modal('hide');
                    }
                });
                
                console.log('✅ Events bound successfully');
            } catch (error) {
                console.error('❌ Error binding events:', error);
            }
        },
        
        // ✅ เริ่ม auto-refresh (ทุก 2 นาที)
        startAutoRefresh: function() {
            setInterval(function() {
                // Only refresh if no modals are open
                if (!$('.modal.show').length) {
                    location.reload();
                }
            }, 120000); // 2 minutes
        },
        
        // ✅ Toggle Cards View
        toggleCardsView: function() {
            if (!this.ensureReady()) {
                var self = this;
                setTimeout(function() { self.toggleCardsView(); }, 1000);
                return;
            }
            
            const container = document.getElementById('casesCardsContainer');
            const toggleText = document.getElementById('toggleText');
            
            if (container && toggleText) {
                if (container.style.display === 'none') {
                    container.style.display = 'block';
                    toggleText.textContent = 'ซ่อนการ์ด';
                    
                    // Smooth scroll to cards section
                    setTimeout(function() {
                        container.scrollIntoView({ 
                            behavior: 'smooth', 
                            block: 'start' 
                        });
                    }, 100);
                } else {
                    container.style.display = 'none';
                    toggleText.textContent = 'แสดงการ์ด';
                }
            }
        },
        
        // ✅ อัปเดตสถานะแบบเร็ว - รองรับ workflow ใหม่
        quickUpdateStatus: function(complainId, newStatus) {
            console.log('🔄 quickUpdateStatus called:', complainId, newStatus);
            
            // ✅ เพิ่มการตรวจสอบพื้นฐาน
            if (!complainId || !newStatus) {
                console.error('❌ Invalid parameters:', {complainId: complainId, newStatus: newStatus});
                this.showAlert('ข้อมูลไม่ถูกต้อง', 'error');
                return;
            }
            
            if (!this.ensureReady()) {
                console.warn('complainManager ไม่พร้อม รอ 1 วินาทีแล้วลองใหม่...');
                var self = this;
                setTimeout(function() { self.quickUpdateStatus(complainId, newStatus); }, 1000);
                return;
            }
            
            if (typeof jQuery === 'undefined') {
                alert('กรุณารอสักครู่ ระบบกำลังโหลด...');
                return;
            }
            
            try {
                // หาสถานะปัจจุบันจากแถวที่เกี่ยวข้องกับ complainId นี้
                let currentStatus = '';
                const rows = document.querySelectorAll('tbody tr');
                
                for (let i = 0; i < rows.length; i++) {
                    const row = rows[i];
                    // หา td แรกที่มี complainId
                    const firstCell = row.querySelector('td.fw-bold');
                    if (firstCell && firstCell.textContent.trim() === complainId.toString()) {
                        // หาสถานะจากแถวนี้
                        const statusBadge = row.querySelector('.status-badge');
                        if (statusBadge) {
                            currentStatus = statusBadge.textContent.trim();
                            break;
                        }
                    }
                }
                
                console.log('Complain ID:', complainId, 'Current Status:', currentStatus, 'New Status:', newStatus);
                
                // ✅ กำหนด Workflow Rules ใหม่
                const workflowRules = {
                    'รอรับเรื่อง': {
                        enabled: ['รับเรื่องแล้ว', 'ยกเลิก'],
                        disabled: ['รอรับเรื่อง', 'รอดำเนินการ', 'กำลังดำเนินการ', 'ดำเนินการเรียบร้อย']
                    },
                    'รับเรื่องแล้ว': {
                        enabled: ['รอดำเนินการ', 'กำลังดำเนินการ', 'ดำเนินการเรียบร้อย', 'ยกเลิก'],
                        disabled: ['รอรับเรื่อง', 'รับเรื่องแล้ว']
                    },
                    'รอดำเนินการ': {
                        enabled: ['กำลังดำเนินการ', 'ดำเนินการเรียบร้อย', 'ยกเลิก'],
                        disabled: ['รอรับเรื่อง', 'รับเรื่องแล้ว', 'รอดำเนินการ']
                    },
                    'กำลังดำเนินการ': {
                        enabled: ['ดำเนินการเรียบร้อย', 'ยกเลิก'],
                        disabled: ['รอรับเรื่อง', 'รับเรื่องแล้ว', 'รอดำเนินการ', 'กำลังดำเนินการ']
                    },
                    'ดำเนินการเรียบร้อย': {
                        enabled: [],
                        disabled: ['รอรับเรื่อง', 'รับเรื่องแล้ว', 'รอดำเนินการ', 'กำลังดำเนินการ', 'ดำเนินการเรียบร้อย', 'ยกเลิก']
                    },
                    'ยกเลิก': {
                        enabled: [],
                        disabled: ['รอรับเรื่อง', 'รับเรื่องแล้ว', 'รอดำเนินการ', 'กำลังดำเนินการ', 'ดำเนินการเรียบร้อย', 'ยกเลิก']
                    }
                };
                
                // ✅ ตรวจสอบ Workflow Rules
                const currentRules = workflowRules[currentStatus] || workflowRules['รอรับเรื่อง'];
                const enabledStatuses = currentRules.enabled;
                const disabledStatuses = currentRules.disabled;
                
                // ตรวจสอบว่าสามารถเปลี่ยนเป็นสถานะใหม่ได้หรือไม่
                if (!enabledStatuses.includes(newStatus)) {
                    let errorMessage = '';
                    
                    if (currentStatus === newStatus) {
                        errorMessage = 'เรื่องนี้อยู่ในสถานะ "' + currentStatus + '" อยู่แล้ว';
                    } else if (disabledStatuses.includes(newStatus)) {
                        if (['รอรับเรื่อง', 'รับเรื่องแล้ว', 'รอดำเนินการ', 'กำลังดำเนินการ'].includes(newStatus)) {
                            errorMessage = 'ไม่สามารถย้อนกลับไปสถานะ "' + newStatus + '" ได้ เพราะผ่านขั้นตอนนี้มาแล้ว';
                        } else {
                            errorMessage = 'ไม่สามารถเปลี่ยนเป็นสถานะ "' + newStatus + '" ได้จากสถานะปัจจุบัน';
                        }
                    } else {
                        errorMessage = 'การเปลี่ยนสถานะไม่ถูกต้องตาม Workflow';
                    }
                    
                    this.showAlert({
                        title: 'ไม่สามารถดำเนินการได้',
                        text: errorMessage,
                        icon: 'warning',
                        confirmButtonText: 'ตกลง',
                        footer: '<small>กรุณาติดตาม Workflow: รอรับเรื่อง → รับเรื่องแล้ว → ดำเนินการ → เสร็จสิ้น</small>'
                    });
                    return;
                }
                
                // ✅ ข้อความยืนยันที่แตกต่างกันตามสถานะ
                let confirmText = 'คุณต้องการเปลี่ยนสถานะเป็น "' + newStatus + '" หรือไม่?';
                let confirmColor = '#60a5fa';
                
                switch(newStatus) {
                    case 'รอรับเรื่อง':
                        confirmText = 'ยืนยันเปลี่ยนสถานะเป็น "รอรับเรื่อง"?\nเรื่อง #' + complainId + ' จะกลับไปสู่สถานะเริ่มต้น';
                        confirmColor = '#fb923c';
                        break;
                    case 'รับเรื่องแล้ว':
                        confirmText = 'ยืนยันการรับเรื่องร้องเรียน #' + complainId + '?\nจะเริ่มดำเนินการแก้ไขปัญหา';
                        confirmColor = '#06b6d4';
                        break;
                    case 'รอดำเนินการ':
                        confirmText = 'ยืนยันให้เรื่อง #' + complainId + ' รออยู่ในคิวดำเนินการ?';
                        confirmColor = '#fbbf24';
                        break;
                    case 'กำลังดำเนินการ':
                        confirmText = 'ยืนยันเริ่มดำเนินการแก้ไขปัญหาเรื่อง #' + complainId + '?';
                        confirmColor = '#60a5fa';
                        break;
                    case 'ดำเนินการเรียบร้อย':
                        confirmText = 'ยืนยันการแก้ไขปัญหาเรื่อง #' + complainId + ' เสร็จสิ้นแล้ว?\n\n⚠️ หมายเหตุ: ไม่สามารถเปลี่ยนแปลงได้อีกหลังจากนี้';
                        confirmColor = '#34d399';
                        break;
                    case 'ยกเลิก':
                        confirmText = 'ยืนยันการยกเลิกเรื่องร้องเรียน #' + complainId + '?\n\n⚠️ หมายเหตุ: ไม่สามารถเปลี่ยนแปลงได้อีกหลังจากนี้';
                        confirmColor = '#f87171';
                        break;
                }
                
                // ยืนยันการเปลี่ยนสถานะ
                var self = this;
                this.showAlert({
                    title: 'ยืนยันการเปลี่ยนสถานะ',
                    text: confirmText,
                    icon: 'question',
                    showCancelButton: true,
                    confirmButtonColor: confirmColor,
                    cancelButtonColor: '#6b7280',
                    confirmButtonText: 'ยืนยัน',
                    cancelButtonText: 'ยกเลิก'
                }).then(function(result) {
                    if (result.isConfirmed) {
                        self.performQuickUpdate(complainId, newStatus);
                    }
                });
                
            } catch (error) {
                console.error('❌ Error in quickUpdateStatus:', error);
                this.showAlert('เกิดข้อผิดพลาดในการอัปเดตสถานะ: ' + error.message, 'error');
            }
        },
        
        // ✅ ดำเนินการอัปเดตสถานะ
        performQuickUpdate: function(complainId, newStatus) {
            if (!this.ensureReady()) return;
            
            const formData = {
                complain_id: complainId,
                new_status: newStatus,
                comment: 'อัปเดตเป็น "' + newStatus + '" ผ่านระบบ Workflow Management'
            };

            // แสดง loading พร้อมข้อความเฉพาะตามสถานะ
            let loadingMessage = 'กำลังอัปเดตสถานะ...';
            
            switch(newStatus) {
                case 'รอรับเรื่อง':
                    loadingMessage = 'กำลังเปลี่ยนสถานะเป็นรอรับเรื่อง...';
                    break;
                case 'รับเรื่องแล้ว':
                    loadingMessage = 'กำลังรับเรื่องและบันทึกในระบบ...';
                    break;
                case 'รอดำเนินการ':
                    loadingMessage = 'กำลังจัดเรียงลำดับในคิว...';
                    break;
                case 'กำลังดำเนินการ':
                    loadingMessage = 'กำลังเริ่มกระบวนการแก้ไข...';
                    break;
                case 'ดำเนินการเรียบร้อย':
                    loadingMessage = 'กำลังปิดเรื่องและบันทึกผล...';
                    break;
                case 'ยกเลิก':
                    loadingMessage = 'กำลังยกเลิกและปิดเรื่อง...';
                    break;
            }

            this.showAlert({
                title: loadingMessage,
                text: 'เรื่อง #' + complainId,
                icon: 'info',
                allowOutsideClick: false,
                allowEscapeKey: false,
                showConfirmButton: false,
                didOpen: function() {
                    if (typeof Swal !== 'undefined') {
                        Swal.showLoading();
                    }
                }
            });

            var self = this;
            $.ajax({
                url: this.config.updateStatusUrl,
                type: 'POST',
                data: formData,
                dataType: 'json',
                success: function(response) {
                    if (response.success) {
                        // ข้อความสำเร็จตามสถานะ
                        let successMessage = response.message;
                        let successIcon = 'success';
                        
                        switch(newStatus) {
                            case 'รอรับเรื่อง':
                                successMessage = 'เปลี่ยนสถานะเรื่อง #' + complainId + ' เป็น "รอรับเรื่อง" เรียบร้อย';
                                successIcon = 'info';
                                break;
                            case 'รับเรื่องแล้ว':
                                successMessage = 'รับเรื่อง #' + complainId + ' เรียบร้อย! พร้อมดำเนินการขั้นตอนถัดไป';
                                successIcon = 'success';
                                break;
                            case 'รอดำเนินการ':
                                successMessage = 'เรื่อง #' + complainId + ' อยู่ในคิวดำเนินการแล้ว';
                                successIcon = 'info';
                                break;
                            case 'กำลังดำเนินการ':
                                successMessage = 'เริ่มดำเนินการแก้ไขเรื่อง #' + complainId + ' แล้ว';
                                successIcon = 'success';
                                break;
                            case 'ดำเนินการเรียบร้อย':
                                successMessage = '✅ เรื่อง #' + complainId + ' ดำเนินการเสร็จสิ้นเรียบร้อย';
                                successIcon = 'success';
                                break;
                            case 'ยกเลิก':
                                successMessage = '❌ ยกเลิกเรื่อง #' + complainId + ' เรียบร้อย';
                                successIcon = 'info';
                                break;
                        }
                        
                        self.showAlert({
                            title: 'อัปเดตสำเร็จ!',
                            text: successMessage,
                            icon: successIcon,
                            timer: 2000,
                            showConfirmButton: false
                        }).then(function() {
                            location.reload();
                        });
                    } else {
                        self.showAlert(response.message, 'error');
                    }
                },
                error: function(xhr, status, error) {
                    console.error('AJAX Error:', error);
                    console.error('Response:', xhr.responseText);
                    
                    let errorMessage = 'เกิดข้อผิดพลาดในการเชื่อมต่อ';
                    
                    if (xhr.status === 404) {
                        errorMessage = 'ไม่พบหน้าที่ร้องขอ (404)';
                    } else if (xhr.status === 500) {
                        errorMessage = 'เกิดข้อผิดพลาดในเซิร์ฟเวอร์ (500)';
                    } else if (xhr.responseJSON && xhr.responseJSON.message) {
                        errorMessage = xhr.responseJSON.message;
                    }
                    
                    self.showAlert({
                        title: 'เกิดข้อผิดพลาด',
                        text: errorMessage,
                        icon: 'error',
                        footer: '<small>กรุณาลองใหม่อีกครั้ง หรือติดต่อผู้ดูแลระบบ</small>'
                    });
                }
            });
        },
        
        // ✅ รีเฟรชตาราง
        refreshTable: function() {
            // แสดง loading indicator
            if (typeof Swal !== 'undefined') {
                Swal.fire({
                    title: 'กำลังโหลดข้อมูล...',
                    allowOutsideClick: false,
                    allowEscapeKey: false,
                    showConfirmButton: false,
                    didOpen: function() {
                        Swal.showLoading();
                    }
                });
            }
            
            setTimeout(function() {
                location.reload();
            }, 500);
        },
        
        // ✅ แสดงข้อความแจ้งเตือน
        showAlert: function(message, type) {
            if (typeof Swal !== 'undefined') {
                if (typeof message === 'object') {
                    // SweetAlert2 options object
                    return Swal.fire(message);
                } else {
                    // Simple message
                    return Swal.fire({
                        icon: type === 'error' ? 'error' : 'success',
                        title: type === 'error' ? 'เกิดข้อผิดพลาด' : 'สำเร็จ',
                        text: message,
                        timer: type === 'success' ? 2000 : null,
                        showConfirmButton: type === 'error',
                        confirmButtonText: 'ตกลง'
                    });
                }
            } else {
                // Fallback to alert
                alert(message);
                return Promise.resolve({ isConfirmed: true });
            }
        },
        
        // ✅ ฟังก์ชันสำหรับแสดง Enhanced Modal
       showEnhancedModal: function(complainId, currentStatus, newStatus) {
            if (typeof StatusUpdateModal !== 'undefined') {
                // ✅ ดึงข้อมูลเพิ่มเติมจากตาราง
                const complainData = this.getComplainDataFromTable(complainId);
                StatusUpdateModal.show(complainId, currentStatus, newStatus, complainData);
            } else {
                // Fallback to old method
                console.warn('StatusUpdateModal not available, using quickUpdateStatus fallback');
                this.quickUpdateStatus(complainId, newStatus);
            }
        },
		
		// ✅ ฟังก์ชันสำหรับดึงข้อมูลเรื่องร้องเรียนจากตาราง
        getComplainDataFromTable: function(complainId) {
            const complainData = {};
            
            try {
                // หาแถวที่มี complainId นี้
                const rows = document.querySelectorAll('tbody tr.case-data-row');
                
                for (let i = 0; i < rows.length; i++) {
                    const row = rows[i];
                    const firstCell = row.querySelector('td.fw-bold');
                    
                    if (firstCell && firstCell.textContent.trim() === complainId.toString()) {
                        // ดึงข้อมูลวันที่จาก cell ที่ 2
                        const dateCell = row.querySelectorAll('td')[1];
                        if (dateCell) {
                            const dateText = dateCell.textContent.trim();
                            complainData.date = dateText;
                        }
                        
                        // ดึงข้อมูลหัวข้อจาก cell ที่ 5
                        const topicCell = row.querySelectorAll('td')[4];
                        if (topicCell) {
                            complainData.topic = topicCell.textContent.trim();
                        }
                        
                        // ดึงข้อมูลผู้แจ้งจาก cell ที่ 7
                        const reporterCell = row.querySelectorAll('td')[6];
                        if (reporterCell) {
                            complainData.reporter = reporterCell.textContent.trim();
                        }
                        
                        // ดึงข้อมูลเบอร์โทรจาก cell ที่ 8
                        const phoneCell = row.querySelectorAll('td')[7];
                        if (phoneCell) {
                            complainData.phone = phoneCell.textContent.trim();
                        }
                        
                        break;
                    }
                }
                
                console.log('📋 Complain data extracted:', complainData);
                
            } catch (error) {
                console.error('❌ Error extracting complain data:', error);
            }
            
            return complainData;
        },
        
        // ✅ ฟังก์ชันสำหรับแสดงรูปภาพ
        showImageModal: function(imageSrc) {
            if (typeof bootstrap !== 'undefined') {
                document.getElementById('modalImage').src = imageSrc;
                var imageModal = new bootstrap.Modal(document.getElementById('imageModal'));
                imageModal.show();
            } else {
                // Fallback: เปิดรูปภาพในหน้าต่างใหม่
                window.open(imageSrc, '_blank');
            }
        },
        
        // ✅ ฟังก์ชันสำหรับแก้ไขเรื่องร้องเรียน
        editComplain: function(complainId) {
            if (complainId) {
                window.location.href = this.config.editComplainUrl + '/' + complainId;
            } else {
                this.showAlert('ไม่พบหมายเลขเรื่องร้องเรียน', 'error');
            }
        },
        
        // ✅ ฟังก์ชันสำหรับลบเรื่องร้องเรียน
        deleteComplain: function(complainId) {
            if (!complainId) {
                this.showAlert('ไม่พบหมายเลขเรื่องร้องเรียน', 'error');
                return;
            }
            
            var self = this;
            this.showAlert({
                title: 'ยืนยันการลบ',
                text: 'คุณต้องการลบเรื่องร้องเรียน #' + complainId + ' หรือไม่?\n\n⚠️ ข้อมูลที่ลบแล้วจะไม่สามารถกู้คืนได้',
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#ef4444',
                cancelButtonColor: '#6b7280',
                confirmButtonText: 'ลบ',
                cancelButtonText: 'ยกเลิก'
            }).then(function(result) {
                if (result.isConfirmed) {
                    self.performDelete(complainId);
                }
            });
        },
        
        // ✅ ฟังก์ชันดำเนินการลบ
        performDelete: function(complainId) {
            var self = this;
            
            this.showAlert({
                title: 'กำลังลบข้อมูล...',
                text: 'เรื่อง #' + complainId,
                icon: 'info',
                allowOutsideClick: false,
                allowEscapeKey: false,
                showConfirmButton: false,
                didOpen: function() {
                    if (typeof Swal !== 'undefined') {
                        Swal.showLoading();
                    }
                }
            });
            
            $.ajax({
                url: this.config.deleteComplainUrl,
                type: 'POST',
                data: {
                    complain_id: complainId
                },
                dataType: 'json',
                success: function(response) {
                    if (response.success) {
                        self.showAlert({
                            title: 'ลบสำเร็จ!',
                            text: 'ลบเรื่องร้องเรียน #' + complainId + ' เรียบร้อยแล้ว',
                            icon: 'success',
                            timer: 2000,
                            showConfirmButton: false
                        }).then(function() {
                            location.reload();
                        });
                    } else {
                        self.showAlert(response.message, 'error');
                    }
                },
                error: function(xhr, status, error) {
                    console.error('Delete Error:', error);
                    self.showAlert('เกิดข้อผิดพลาดในการลบข้อมูล', 'error');
                }
            });
        }
    };
    
    // ✅ Work Performance & Alerts Functions
    window.refreshAlerts = function() {
        if (typeof Swal !== 'undefined') {
            Swal.fire({
                title: 'กำลังอัพเดทข้อมูล...',
                text: 'กรุณารอสักครู่',
                icon: 'info',
                allowOutsideClick: false,
                allowEscapeKey: false,
                showConfirmButton: false,
                timer: 1500,
                didOpen: function() {
                    Swal.showLoading();
                }
            }).then(function() {
                location.reload();
            });
        } else {
            location.reload();
        }
    };
    
    window.goToCase = function(caseId) {
        if (caseId) {
            // Highlight the case in current page or navigate to detail
            const targetCase = document.querySelector('[data-case-id="' + caseId + '"]');
            if (targetCase) {
                // Scroll to case and highlight
                targetCase.scrollIntoView({ behavior: 'smooth', block: 'center' });
                targetCase.style.border = '3px solid #f59e0b';
                targetCase.style.boxShadow = '0 0 20px rgba(245, 158, 11, 0.3)';
                
                setTimeout(function() {
                    targetCase.style.border = '2px solid #e2e8f0';
                    targetCase.style.boxShadow = '0 2px 8px rgba(0,0,0,0.06)';
                }, 3000);
            } else {
                // Navigate to detail page
                window.location.href = '<?= site_url("System_reports/complain_detail") ?>/' + caseId;
            }
        }
    };
    
    window.showAllWarnings = function() {
        if (typeof Swal !== 'undefined') {
            <?php
            // ส่งข้อมูล warning cases ให้ JavaScript
            $warning_json = json_encode($warning_cases);
            ?>
            const warningCases = <?= $warning_json ?>;
            
            if (warningCases.length === 0) {
                Swal.fire({
                    title: '<i class="fas fa-check-circle text-success"></i> ไม่มี Case ค้าง 3-6 วัน',
                    text: 'ทุกเรื่องอยู่ในกำหนดเวลา',
                    icon: 'success',
                    confirmButtonText: 'ปิด',
                    confirmButtonColor: '#10b981'
                });
                return;
            }
            
            let html = '<div class="warning-cases-list">';
            warningCases.forEach(function(case_item) {
                html += '<div class="warning-case-item" onclick="goToCase(\'' + case_item.id + '\')">';
                html += '<div class="case-info">';
                html += '<strong>#' + case_item.id + '</strong> - ' + case_item.topic;
                html += '<br><small>ค้าง ' + case_item.days + ' วัน | สถานะ: ' + case_item.status + '</small>';
                html += '</div>';
                html += '</div>';
            });
            html += '</div>';
            
            Swal.fire({
                title: '<i class="fas fa-clock text-warning"></i> Case ค้าง 3-6 วัน (' + warningCases.length + ' รายการ)',
                html: html,
                width: 600,
                confirmButtonText: 'ปิด',
                confirmButtonColor: '#60a5fa',
                customClass: {
                    htmlContainer: 'text-left'
                }
            });
        }
    };
    
    window.showAllAlerts = function() {
        if (typeof Swal !== 'undefined') {
            <?php
            // ส่งข้อมูลทุกประเภทให้ JavaScript  
            $critical_json = json_encode($critical_cases);
            $danger_json = json_encode($danger_cases);
            $warning_json = json_encode($warning_cases);
            ?>
            const criticalCases = <?= $critical_json ?>;
            const dangerCases = <?= $danger_json ?>;
            const warningCases = <?= $warning_json ?>;
            
            let html = '<div class="all-alerts-container">';
            
            // Critical Cases Section
            if (criticalCases.length > 0) {
                html += '<div class="alert-section">';
                html += '<h6 class="text-danger"><i class="fas fa-fire"></i> ค้าง 14+ วัน (วิกฤติ) - ' + criticalCases.length + ' รายการ</h6>';
                html += '<div class="alert-items-list">';
                criticalCases.forEach(function(case_item) {
                    html += '<div class="alert-item-summary" onclick="goToCase(\'' + case_item.id + '\')">';
                    html += '#' + case_item.id + ' - ' + case_item.topic + ' (ค้าง ' + case_item.days + ' วัน)';
                    html += '<br><small class="text-muted">สถานะ: ' + case_item.status + '</small>';
                    html += '</div>';
                });
                html += '</div></div>';
            }
            
            // Danger Cases Section  
            if (dangerCases.length > 0) {
                html += '<div class="alert-section">';
                html += '<h6 class="text-warning"><i class="fas fa-exclamation-triangle"></i> ค้าง 7-13 วัน (เร่งด่วน) - ' + dangerCases.length + ' รายการ</h6>';
                html += '<div class="alert-items-list">';
                dangerCases.forEach(function(case_item) {
                    html += '<div class="alert-item-summary" onclick="goToCase(\'' + case_item.id + '\')">';
                    html += '#' + case_item.id + ' - ' + case_item.topic + ' (ค้าง ' + case_item.days + ' วัน)';
                    html += '<br><small class="text-muted">สถานะ: ' + case_item.status + '</small>';
                    html += '</div>';
                });
                html += '</div></div>';
            }
            
            // Warning Cases Section
            if (warningCases.length > 0) {
                html += '<div class="alert-section">';
                html += '<h6 class="text-info"><i class="fas fa-clock"></i> ค้าง 3-6 วัน (ติดตาม) - ' + warningCases.length + ' รายการ</h6>';
                html += '<div class="alert-items-list">';
                warningCases.slice(0, 5).forEach(function(case_item) {
                    html += '<div class="alert-item-summary" onclick="goToCase(\'' + case_item.id + '\')">';
                    html += '#' + case_item.id + ' - ' + case_item.topic + ' (ค้าง ' + case_item.days + ' วัน)';
                    html += '<br><small class="text-muted">สถานะ: ' + case_item.status + '</small>';
                    html += '</div>';
                });
                if (warningCases.length > 5) {
                    html += '<div class="alert-item-summary">';
                    html += 'และอีก ' + (warningCases.length - 5) + ' รายการ...';
                    html += '</div>';
                }
                html += '</div></div>';
            }
            
            // ถ้าไม่มี alerts เลย
            if (criticalCases.length === 0 && dangerCases.length === 0 && warningCases.length === 0) {
                html += '<div class="text-center py-4">';
                html += '<i class="fas fa-check-circle text-success fa-3x mb-3"></i>';
                html += '<h5 class="text-success">ยอดเยี่ยม!</h5>';
                html += '<p class="text-muted">ไม่มี Case ที่ค้างนาน ทุกเรื่องอยู่ในกำหนดเวลา</p>';
                html += '</div>';
            }
            
            html += '</div>';
            
            const totalAlerts = criticalCases.length + dangerCases.length + warningCases.length;
            const titleText = totalAlerts > 0 ? 
                'รายการ Case ทั้งหมดที่ต้องติดตาม (' + totalAlerts + ' รายการ)' :
                'สถานะการปฏิบัติงาน';
            
            Swal.fire({
                title: '<i class="fas fa-list text-primary"></i> ' + titleText,
                html: html,
                width: 700,
                confirmButtonText: 'ปิด',
                confirmButtonColor: '#60a5fa',
                customClass: {
                    htmlContainer: 'text-left'
                }
            });
        }
    };
    
    window.showCategoryDetails = function(category) {
        <?php
        // ส่งข้อมูลแต่ละประเภทให้ JavaScript
        ?>
        const categories = {
            'warning': <?= json_encode($warning_cases) ?>,
            'danger': <?= json_encode($danger_cases) ?>,
            'critical': <?= json_encode($critical_cases) ?>
        };
        
        const categoryData = categories[category] || [];
        const categoryInfo = {
            'warning': { title: 'Case ค้าง 3-6 วัน', icon: 'clock', color: 'warning' },
            'danger': { title: 'Case ค้าง 7-13 วัน', icon: 'exclamation-triangle', color: 'warning' },
            'critical': { title: 'Case ค้าง 14+ วัน', icon: 'fire', color: 'danger' }
        };
        
        if (categoryData.length === 0) {
            Swal.fire({
                title: '<i class="fas fa-check-circle text-success"></i> ไม่มี ' + categoryInfo[category].title,
                text: 'ไม่พบ Case ในหมวดหมู่นี้',
                icon: 'success',
                confirmButtonText: 'ปิด',
                confirmButtonColor: '#10b981'
            });
            return;
        }
        
        let html = '<div class="category-details-list">';
        categoryData.forEach(function(case_item) {
            html += '<div class="category-detail-item" onclick="goToCase(\'' + case_item.id + '\')">';
            html += '<div class="case-info">';
            html += '<strong>#' + case_item.id + '</strong> - ' + case_item.topic;
            html += '<br><small>ค้าง ' + case_item.days + ' วัน | สถานะ: ' + case_item.status;
            html += '<br>วันที่แจ้ง: ' + new Date(case_item.date).toLocaleDateString('th-TH') + '</small>';
            html += '</div>';
            html += '</div>';
        });
        html += '</div>';
        
        Swal.fire({
            title: '<i class="fas fa-' + categoryInfo[category].icon + ' text-' + categoryInfo[category].color + '"></i> ' + 
                   categoryInfo[category].title + ' (' + categoryData.length + ' รายการ)',
            html: html,
            width: 600,
            confirmButtonText: 'ปิด',
            confirmButtonColor: '#60a5fa',
            customClass: {
                htmlContainer: 'text-left'
            }
        });
    };
    
    window.exportAlerts = function() {
        <?php
        // เตรียมข้อมูลสำหรับ export
        $export_data = [
            'critical' => $critical_cases,
            'danger' => $danger_cases, 
            'warning' => $warning_cases,
            'total' => count($critical_cases) + count($danger_cases) + count($warning_cases),
            'export_date' => date('Y-m-d H:i:s')
        ];
        ?>
        const exportData = <?= json_encode($export_data) ?>;
        
        if (exportData.total === 0) {
            if (typeof Swal !== 'undefined') {
                Swal.fire({
                    title: 'ไม่มีข้อมูลสำหรับส่งออก',
                    text: 'ไม่มี Case ที่ค้างนาน ทุกเรื่องอยู่ในกำหนดเวลา',
                    icon: 'info',
                    confirmButtonText: 'ตกลง',
                    confirmButtonColor: '#60a5fa'
                });
            }
            return;
        }
        
        if (typeof Swal !== 'undefined') {
            Swal.fire({
                title: 'ส่งออกรายงาน Case ค้างนาน',
                text: 'เลือกรูปแบบที่ต้องการส่งออก (' + exportData.total + ' รายการ)',
                icon: 'question',
                showCancelButton: true,
                confirmButtonText: '<i class="fas fa-file-excel"></i> Excel',
                cancelButtonText: '<i class="fas fa-file-pdf"></i> PDF',
                confirmButtonColor: '#059669',
                cancelButtonColor: '#dc2626',
                footer: '<small>รายงาน ณ วันที่ ' + new Date(exportData.export_date).toLocaleDateString('th-TH') + '</small>'
            }).then(function(result) {
                if (result.isConfirmed) {
                    // ส่งข้อมูลไป export เป็น Excel
                    const form = document.createElement('form');
                    form.method = 'POST';
                    form.action = '<?= site_url("System_reports/export_alerts_excel") ?>';
                    
                    const dataInput = document.createElement('input');
                    dataInput.type = 'hidden';
                    dataInput.name = 'alert_data';
                    dataInput.value = JSON.stringify(exportData);
                    form.appendChild(dataInput);
                    
                    document.body.appendChild(form);
                    form.submit();
                    document.body.removeChild(form);
                    
                } else if (result.dismiss === Swal.DismissReason.cancel) {
                    // ส่งข้อมูลไป export เป็น PDF
                    const form = document.createElement('form');
                    form.method = 'POST';
                    form.action = '<?= site_url("System_reports/export_alerts_pdf") ?>';
                    
                    const dataInput = document.createElement('input');
                    dataInput.type = 'hidden';
                    dataInput.name = 'alert_data';
                    dataInput.value = JSON.stringify(exportData);
                    form.appendChild(dataInput);
                    
                    document.body.appendChild(form);
                    form.submit();
                    document.body.removeChild(form);
                }
            });
        }
    };
    
    // ✅ เริ่มต้นระบบทันที
    console.log('📚 Initializing Complain Manager immediately...');
    
    // รอให้ DOM พร้อม
    function initWhenReady() {
        if (document.readyState === 'loading') {
            document.addEventListener('DOMContentLoaded', function() {
                console.log('DOM loaded, initializing complainManager...');
                window.complainManager.init();
            });
        } else {
            console.log('DOM already loaded, initializing complainManager...');
            window.complainManager.init();
        }
    }
    
    // เริ่มต้นทันที
    initWhenReady();
    
    // ✅ Retry mechanism
    let retryCount = 0;
    const maxRetries = 5;
    
    function ensureManagerReady() {
        if (!window.complainManager.isReady && retryCount < maxRetries) {
            retryCount++;
            console.log('Retry ' + retryCount + ': Ensuring complainManager is ready...');
            setTimeout(function() {
                window.complainManager.init();
                ensureManagerReady();
            }, 1000 * retryCount);
        }
    }
    
    // เริ่ม retry mechanism หลังจาก 3 วินาที
    setTimeout(ensureManagerReady, 3000);
    
    console.log("📚 Robust Complain Manager script loaded");
})();

// Enhanced Status Update Modal with Auto Compression
const StatusUpdateModal = {
    maxImages: 5,
    maxFileSize: 10 * 1024 * 1024, // 10MB
    allowedTypes: ['image/jpeg', 'image/png', 'image/gif'],
    selectedFiles: [],
    
    // Auto compression settings (fixed values)
    compressionSettings: {
        quality: 0.8,        // 80% quality
        maxWidth: 1200,      // Max width 1200px
        maxHeight: 1200,     // Max height 1200px
        enabled: true        // Always enabled
    },
    
    init: function() {
        this.bindEvents();
        console.log('✅ Enhanced Status Update Modal with Auto Compression initialized');
    },
    
    bindEvents: function() {
        var self = this;
        
        // Image upload area click
        document.getElementById('imageUploadArea').addEventListener('click', function() {
            document.getElementById('statusImages').click();
        });
        
        // File input change
        document.getElementById('statusImages').addEventListener('change', function(e) {
            self.handleFileSelect(e.target.files);
        });
        
        // Drag and drop
        const uploadArea = document.getElementById('imageUploadArea');
        uploadArea.addEventListener('dragover', function(e) {
            e.preventDefault();
            uploadArea.classList.add('dragover');
        });
        
        uploadArea.addEventListener('dragleave', function() {
            uploadArea.classList.remove('dragover');
        });
        
        uploadArea.addEventListener('drop', function(e) {
            e.preventDefault();
            uploadArea.classList.remove('dragover');
            self.handleFileSelect(e.dataTransfer.files);
        });
        
        // Note character counter
        document.getElementById('modalStatusNote').addEventListener('input', function(e) {
            self.updateCharacterCounter(e.target);
        });
        
        // Confirm button
        document.getElementById('confirmStatusUpdate').addEventListener('click', function() {
            self.submitStatusUpdate();
        });
    },
    
    show: function(complainId, currentStatus, newStatus, complainData) {
        complainData = complainData || {};
        
        // Reset form
        this.resetForm();
        
        // Set data
        document.getElementById('modalComplainId').value = complainId;
        document.getElementById('modalNewStatus').value = newStatus;
        document.getElementById('modalComplainIdDisplay').textContent = '#' + complainId;
        document.getElementById('modalCurrentStatus').textContent = currentStatus;
        document.getElementById('modalNewStatusDisplay').textContent = newStatus;
        
        // Set complain date if available
        if (complainData.date) {
            document.getElementById('modalComplainDate').textContent = complainData.date;
        }
        
        // Update status colors
        this.updateStatusColors(currentStatus, newStatus);
        
        // Show modal
        const modal = new bootstrap.Modal(document.getElementById('statusUpdateModal'));
        modal.show();
    },
    
    updateStatusColors: function(currentStatus, newStatus) {
        const currentEl = document.getElementById('modalCurrentStatus');
        const newEl = document.getElementById('modalNewStatusDisplay');
        
        // Remove existing classes
        currentEl.className = 'current-status';
        newEl.className = 'new-status';
        
        // Add status-specific classes
        const statusClasses = {
            'รอรับเรื่อง': 'status-waiting',
            'รับเรื่องแล้ว': 'status-received',
            'รอดำเนินการ': 'status-pending',
            'กำลังดำเนินการ': 'status-processing',
            'ดำเนินการเรียบร้อย': 'status-completed',
            'ยกเลิก': 'status-cancelled'
        };
        
        currentEl.classList.add(statusClasses[currentStatus] || 'status-pending');
        newEl.classList.add(statusClasses[newStatus] || 'status-pending');
    },
    
    handleFileSelect: function(files) {
        var fileArray = Array.from(files);
        
        // Check total count
        if (this.selectedFiles.length + fileArray.length > this.maxImages) {
            this.showAlert('สามารถเลือกได้สูงสุด ' + this.maxImages + ' รูปเท่านั้น', 'warning');
            return;
        }
        
        // Show compression processing
        this.showCompressionProcessing(true);
        
        // Process files with auto compression
        var self = this;
        var processedCount = 0;
        
        fileArray.forEach(function(file) {
            if (self.validateFile(file)) {
                self.compressAndAddFile(file, function() {
                    processedCount++;
                    if (processedCount === fileArray.length) {
                        self.showCompressionProcessing(false);
                        self.updateStats();
                    }
                });
            } else {
                processedCount++;
                if (processedCount === fileArray.length) {
                    self.showCompressionProcessing(false);
                    self.updateStats();
                }
            }
        });
    },
    
    validateFile: function(file) {
        // Check file type
        if (!this.allowedTypes.includes(file.type)) {
            this.showAlert('ไฟล์ ' + file.name + ' ไม่รองรับ กรุณาเลือกไฟล์ JPG, PNG หรือ GIF', 'error');
            return false;
        }
        
        // Check file size
        if (file.size > this.maxFileSize) {
            this.showAlert('ไฟล์ ' + file.name + ' มีขนาดเกิน 10MB', 'error');
            return false;
        }
        
        return true;
    },
    
    compressAndAddFile: function(file, callback) {
        var self = this;
        
        // Create canvas for compression
        const canvas = document.createElement('canvas');
        const ctx = canvas.getContext('2d');
        const img = new Image();
        
        img.onload = function() {
            // Calculate new dimensions
            var dimensions = self.calculateNewDimensions(img.width, img.height);
            var width = dimensions.width;
            var height = dimensions.height;
            
            // Set canvas size
            canvas.width = width;
            canvas.height = height;
            
            // Draw and compress image
            ctx.drawImage(img, 0, 0, width, height);
            
            // Convert to blob with compression
            canvas.toBlob(function(compressedBlob) {
                if (compressedBlob) {
                    // Create new file with compressed data
                    const compressedFile = new File([compressedBlob], file.name, {
                        type: file.type,
                        lastModified: file.lastModified
                    });
                    
                    // Store original size for stats
                    compressedFile.originalSize = file.size;
                    
                    // Add to selected files
                    self.selectedFiles.push(compressedFile);
                    self.createImagePreview(compressedFile, true);
                } else {
                    // Fallback to original file if compression fails
                    self.selectedFiles.push(file);
                    self.createImagePreview(file, false);
                }
                
                callback();
            }, file.type, self.compressionSettings.quality);
        };
        
        img.onerror = function() {
            // Fallback to original file if image loading fails
            self.selectedFiles.push(file);
            self.createImagePreview(file, false);
            callback();
        };
        
        // Load image
        const reader = new FileReader();
        reader.onload = function(e) {
            img.src = e.target.result;
        };
        reader.readAsDataURL(file);
    },
    
    calculateNewDimensions: function(originalWidth, originalHeight) {
        const maxWidth = this.compressionSettings.maxWidth;
        const maxHeight = this.compressionSettings.maxHeight;
        
        let width = originalWidth;
        let height = originalHeight;
        
        // Scale down if needed
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
    
    createImagePreview: function(file, isCompressed) {
        const container = document.getElementById('imagePreviewContainer');
        const fileIndex = this.selectedFiles.length - 1;
        
        const previewDiv = document.createElement('div');
        previewDiv.className = 'image-preview-item' + (isCompressed ? ' compressed' : '');
        previewDiv.dataset.fileIndex = fileIndex;
        
        const img = document.createElement('img');
        const removeBtn = document.createElement('button');
        
        // Create image preview
        const reader = new FileReader();
        reader.onload = function(e) {
            img.src = e.target.result;
            img.alt = file.name;
        };
        reader.readAsDataURL(file);
        
        // Create remove button
        removeBtn.className = 'image-remove-btn';
        removeBtn.innerHTML = '<i class="fas fa-times"></i>';
        removeBtn.title = 'ลบรูปนี้';
        var self = this;
        removeBtn.addEventListener('click', function(e) {
            e.stopPropagation();
            self.removeImage(fileIndex);
        });
        
        // Add compression badge if compressed
        if (isCompressed) {
            const badge = document.createElement('div');
            badge.className = 'compression-badge';
            badge.textContent = 'บีบอัดแล้ว';
            previewDiv.appendChild(badge);
        }
        
        previewDiv.appendChild(img);
        previewDiv.appendChild(removeBtn);
        container.appendChild(previewDiv);
    },
    
    removeImage: function(fileIndex) {
        // Remove from array
        this.selectedFiles.splice(fileIndex, 1);
        
        // Remove preview
        const previewItem = document.querySelector('[data-file-index="' + fileIndex + '"]');
        if (previewItem) {
            previewItem.remove();
        }
        
        // Update indexes
        this.updatePreviewIndexes();
        this.updateStats();
    },
    
    updatePreviewIndexes: function() {
        const previews = document.querySelectorAll('.image-preview-item');
        previews.forEach(function(preview, index) {
            preview.dataset.fileIndex = index;
        });
    },
    
    updateStats: function() {
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
        
        document.getElementById('imageCount').textContent = count;
        document.getElementById('totalSize').textContent = this.formatFileSize(totalSize);
        document.getElementById('totalSavings').textContent = this.formatFileSize(totalSavings);
    },
    
    updateCharacterCounter: function(textarea) {
        const current = textarea.value.length;
        const max = 500;
        const counter = document.getElementById('noteCharCount');
        const counterDiv = counter.parentElement;
        
        counter.textContent = current;
        
        // Update counter color
        counterDiv.className = 'character-counter';
        if (current > max * 0.9) {
            counterDiv.classList.add('danger');
        } else if (current > max * 0.7) {
            counterDiv.classList.add('warning');
        }
    },
    
    showCompressionProcessing: function(show) {
        const element = document.getElementById('compressionProcessing');
        if (show) {
            element.classList.add('show');
        } else {
            element.classList.remove('show');
        }
    },
    
    formatFileSize: function(bytes) {
        if (bytes === 0) return '0 KB';
        const k = 1024;
        const sizes = ['B', 'KB', 'MB'];
        const i = Math.floor(Math.log(bytes) / Math.log(k));
        return parseFloat((bytes / Math.pow(k, i)).toFixed(1)) + ' ' + sizes[i];
    },
    
    resetForm: function() {
        // Clear form
        document.getElementById('enhancedStatusForm').reset();
        document.getElementById('modalStatusNote').value = '';
        
        // Clear images
        this.selectedFiles = [];
        document.getElementById('imagePreviewContainer').innerHTML = '';
        
        // Reset counters
        document.getElementById('noteCharCount').textContent = '0';
        document.getElementById('noteCharCount').parentElement.className = 'character-counter';
        this.updateStats();
        this.showCompressionProcessing(false);
    },
    
    submitStatusUpdate: function() {
        const formData = new FormData();
        const complainId = document.getElementById('modalComplainId').value;
        const newStatus = document.getElementById('modalNewStatus').value;
        const note = document.getElementById('modalStatusNote').value.trim();
        
        // Add basic data
        formData.append('complain_id', complainId);
        formData.append('new_status', newStatus);
        formData.append('status_note', note);
        
        // Add images
        for (var i = 0; i < this.selectedFiles.length; i++) {
            formData.append('status_images[]', this.selectedFiles[i]);
        }
        
        // Show loading
        this.showLoading();
        
        // Submit via AJAX
        this.sendUpdateRequest(formData);
    },
    
    sendUpdateRequest: function(formData) {
        var self = this;
        fetch('<?= site_url("System_reports/update_complain_status_with_images") ?>', {
            method: 'POST',
            body: formData
        })
        .then(function(response) { return response.json(); })
        .then(function(data) {
            self.hideLoading();
            
            if (data.success) {
                self.showAlert({
                    title: 'อัปเดตสำเร็จ!',
                    text: data.message,
                    icon: 'success',
                    timer: 2000
                }).then(function() {
                    // Close modal and reload
                    bootstrap.Modal.getInstance(document.getElementById('statusUpdateModal')).hide();
                    location.reload();
                });
            } else {
                self.showAlert(data.message, 'error');
            }
        })
        .catch(function(error) {
            self.hideLoading();
            console.error('Error:', error);
            self.showAlert('เกิดข้อผิดพลาดในการเชื่อมต่อ', 'error');
        });
    },
    
    showLoading: function() {
        const button = document.getElementById('confirmStatusUpdate');
        button.disabled = true;
        button.innerHTML = '<i class="fas fa-spinner fa-spin me-1"></i>กำลังอัปเดต...';
    },
    
    hideLoading: function() {
        const button = document.getElementById('confirmStatusUpdate');
        button.disabled = false;
        button.innerHTML = '<i class="fas fa-check me-1"></i>ยืนยันการเปลี่ยนสถานะ';
    },
    
    showAlert: function(message, type) {
        type = type || 'info';
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

// Initialize when DOM is ready
document.addEventListener('DOMContentLoaded', function() {
    StatusUpdateModal.init();
    
    // ✅ ให้แน่ใจว่า complainManager พร้อมใช้งาน
    if (!window.complainManager.isReady) {
        window.complainManager.init();
    }
});

console.log("📚 Enhanced Complain Manager with Auto Image Compression loaded");
</script>



<script>
// ✅ เพิ่มในส่วนท้ายของ JavaScript ในหน้า System_reports/complain

// *** Handler สำหรับ Notification Click - Highlight Case ***
document.addEventListener('DOMContentLoaded', function() {
    console.log('🚀 Complain Report - Checking for notification parameters');
    
    // ตรวจสอบ URL parameters
    const urlParams = new URLSearchParams(window.location.search);
    const highlightCase = urlParams.get('highlight_case');
    const statusUpdated = urlParams.get('status_updated');
    const actionType = urlParams.get('action');
    const fromNotification = urlParams.get('from_notification');
    
    // ตรวจสอบ hash
    const hash = window.location.hash;
    
    console.log('📍 Notification parameters:', {
        highlightCase: highlightCase,
        statusUpdated: statusUpdated,
        actionType: actionType,
        fromNotification: fromNotification,
        hash: hash
    });
    
    // ถ้ามาจาก notification ให้ highlight case
    if (highlightCase || hash.includes('case-')) {
        setTimeout(() => {
            handleNotificationHighlight(highlightCase, statusUpdated, actionType);
        }, 1000); // รอให้ตาราง load เสร็จ
    }
});

// *** ฟังก์ชัน Highlight Case จาก Notification ***
function handleNotificationHighlight(complainId, statusUpdated, actionType) {
    console.log('🎯 Highlighting case from notification:', complainId);
    
    // หา case container ที่ตรงกับ complain_id
    let targetCase = null;
    
    // วิธีที่ 1: ใช้ data-case-id
    if (complainId) {
        targetCase = document.querySelector(`[data-case-id="${complainId}"]`);
    }
    
    // วิธีที่ 2: ใช้ hash (case-123)
    if (!targetCase && window.location.hash) {
        const hashId = window.location.hash.substring(1); // ลบ #
        targetCase = document.getElementById(hashId);
        
        // ถ้าไม่เจอ ลองหา case container ที่มี complainId ตรงกัน
        if (!targetCase && hashId.startsWith('case-')) {
            const caseId = hashId.replace('case-', '');
            targetCase = document.querySelector(`[data-case-id="${caseId}"]`);
        }
    }
    
    // วิธีที่ 3: ค้นหาใน table โดยดูจาก cell แรก
    if (!targetCase && complainId) {
        const rows = document.querySelectorAll('tbody tr.case-data-row');
        for (let row of rows) {
            const firstCell = row.querySelector('td.fw-bold');
            if (firstCell && firstCell.textContent.trim() === complainId.toString()) {
                targetCase = row.closest('.case-container');
                break;
            }
        }
    }
    
    if (targetCase) {
       // console.log('✅ Found target case:', targetCase);
        
        // เลื่อนไปยัง case และ highlight
        highlightCaseFromNotification(targetCase, statusUpdated, actionType);
        
        // แสดง notification message
        showNotificationMessage(complainId, statusUpdated, actionType);
        
        // อัปเดต URL โดยลบ parameters
        cleanNotificationUrl();
        
    } else {
        console.warn('❌ Case not found:', complainId);
        showCaseNotFoundMessage(complainId);
    }
}

// *** ฟังก์ชัน Highlight Case พร้อม Animation ***
function highlightCaseFromNotification(caseElement, statusUpdated, actionType) {
    console.log('🌟 Highlighting case element:', caseElement);
    
    // เลื่อนไปยัง case
    caseElement.scrollIntoView({ 
        behavior: 'smooth', 
        block: 'center',
        inline: 'nearest'
    });
    
    // เพิ่ม highlight effect
    setTimeout(() => {
        // สร้าง highlight style แบบพิเศษ
        const originalTransition = caseElement.style.transition;
        const originalBackground = caseElement.style.background;
        const originalBorder = caseElement.style.border;
        const originalTransform = caseElement.style.transform;
        const originalBoxShadow = caseElement.style.boxShadow;
        
        // กำหนดสีตาม action type
        let highlightColor = '#60a5fa'; // สีน้ำเงิน default
        let glowColor = 'rgba(96, 165, 250, 0.4)';
        
        if (statusUpdated === '1') {
            highlightColor = '#34d399'; // สีเขียว สำหรับ status update
            glowColor = 'rgba(52, 211, 153, 0.4)';
        } else if (actionType === 'new_complain') {
            highlightColor = '#fbbf24'; // สีเหลือง สำหรับเรื่องใหม่
            glowColor = 'rgba(251, 191, 36, 0.4)';
        }
        
        // Apply highlight effect
        caseElement.style.transition = 'all 0.5s ease';
        caseElement.style.background = `linear-gradient(135deg, ${highlightColor}15 0%, ${highlightColor}08 100%)`;
        caseElement.style.border = `3px solid ${highlightColor}`;
        caseElement.style.transform = 'scale(1.02)';
        caseElement.style.boxShadow = `0 8px 25px ${glowColor}, 0 0 20px ${glowColor}`;
        
        // เพิ่ม pulse animation
        let pulseCount = 0;
        const pulseInterval = setInterval(() => {
            if (pulseCount < 3) {
                caseElement.style.transform = pulseCount % 2 === 0 ? 'scale(1.03)' : 'scale(1.02)';
                pulseCount++;
            } else {
                clearInterval(pulseInterval);
            }
        }, 300);
        
        // ลบ highlight หลัง 6 วินาที
        setTimeout(() => {
            caseElement.style.transition = originalTransition;
            caseElement.style.background = originalBackground;
            caseElement.style.border = originalBorder;
            caseElement.style.transform = originalTransform;
            caseElement.style.boxShadow = originalBoxShadow;
        }, 6000);
        
    }, 500);
}

// *** ฟังก์ชันแสดงข้อความแจ้งเตือน ***
function showNotificationMessage(complainId, statusUpdated, actionType) {
    let message = '🎯 พบเรื่องร้องเรียนที่คุณต้องการแล้ว';
    let icon = 'success';
    
    if (statusUpdated === '1') {
        message = `✅ เรื่องร้องเรียน #${complainId} มีการอัปเดตสถานะแล้ว`;
        icon = 'info';
    } else if (actionType === 'new_complain') {
        message = `🔔 เรื่องร้องเรียนใหม่ #${complainId} ที่ได้รับแจ้งเตือน`;
        icon = 'info';
    }
    
    // ใช้ SweetAlert2 หรือ fallback ไปใช้ showAlert
    if (typeof Swal !== 'undefined') {
        Swal.fire({
            title: 'พบข้อมูลแล้ว!',
            text: message,
            icon: icon,
            timer: 3000,
            showConfirmButton: false,
            toast: true,
            position: 'top-end'
        });
    } else if (typeof window.complainManager !== 'undefined' && window.complainManager.showAlert) {
        window.complainManager.showAlert(message, icon);
    } else {
        // Fallback
        alert(message);
    }
}

// *** ฟังก์ชันแสดงข้อความเมื่อไม่พบ Case ***
function showCaseNotFoundMessage(complainId) {
    const message = `❌ ไม่พบเรื่องร้องเรียน #${complainId} ในหน้าปัจจุบัน\nอาจจะอยู่ในหน้าอื่น กรุณาค้นหาโดยใช้ตัวกรอง`;
    
    if (typeof Swal !== 'undefined') {
        Swal.fire({
            title: 'ไม่พบข้อมูล',
            text: message,
            icon: 'warning',
            confirmButtonText: 'ตกลง',
            footer: '<small>กรุณาใช้ตัวกรองหรือค้นหาเพื่อหาเรื่องร้องเรียนนี้</small>'
        });
    } else {
        alert(message);
    }
}

// *** ฟังก์ชันทำความสะอาด URL ***
function cleanNotificationUrl() {
    // ลบ URL parameters ที่เกี่ยวข้องกับ notification
    const url = new URL(window.location);
    const paramsToRemove = ['highlight_case', 'status_updated', 'action', 'from_notification'];
    
    paramsToRemove.forEach(param => {
        url.searchParams.delete(param);
    });
    
    // อัปเดต URL โดยไม่ reload หน้า
    const newUrl = url.pathname + (url.search || '') + url.hash;
    window.history.replaceState({}, document.title, newUrl);
    
    console.log('🧹 Cleaned notification URL parameters');
}

// *** ฟังก์ชันทดสอบ Highlight (สำหรับ Debug) ***
function testHighlightCase(complainId) {
    console.log('🧪 Testing highlight for case:', complainId);
    handleNotificationHighlight(complainId, '0', 'test');
}

// *** เพิ่มใน complainManager object ***
if (typeof window.complainManager !== 'undefined') {
    window.complainManager.highlightCase = handleNotificationHighlight;
    window.complainManager.testHighlight = testHighlightCase;
}

//console.log('✅ Complain Report - Notification highlight system loaded');
//console.log('🔧 Debug functions available:');
//console.log('- testHighlightCase("123456") - ทดสอบ highlight case');
//console.log('- handleNotificationHighlight("123456", "1", "status_updated") - ทดสอบแบบเต็ม');

</script>



<script>

// Category Manager
// Category Manager - Full Code
window.categoryManager = {
    config: {
        apiUrl: '<?= site_url("System_reports/complain_categories_api") ?>',
        saveUrl: '<?= site_url("System_reports/save_complain_category") ?>',
        deleteUrl: '<?= site_url("System_reports/delete_complain_category") ?>'
    },
    
    // รายการไอคอน 30 ตัว
    iconsList: [
        { class: 'fas fa-exclamation-circle', name: 'แจ้งเตือน' },
        { class: 'fas fa-home', name: 'บ้าน' },
        { class: 'fas fa-building', name: 'อาคาร' },
        { class: 'fas fa-car', name: 'รถยนต์' },
        { class: 'fas fa-road', name: 'ถนน' },
        { class: 'fas fa-water', name: 'น้ำ' },
        { class: 'fas fa-bolt', name: 'ไฟฟ้า' },
        { class: 'fas fa-trash', name: 'ขยะ' },
        { class: 'fas fa-leaf', name: 'สิ่งแวดล้อม' },
        { class: 'fas fa-users', name: 'ประชาชน' },
        { class: 'fas fa-shield-alt', name: 'ความปลอดภัย' },
        { class: 'fas fa-medkit', name: 'สาธารณสุข' },
        { class: 'fas fa-graduation-cap', name: 'การศึกษา' },
        { class: 'fas fa-gavel', name: 'กฎหมาย' },
        { class: 'fas fa-money-bill', name: 'การเงิน' },
        { class: 'fas fa-clipboard-list', name: 'เอกสาร' },
        { class: 'fas fa-phone', name: 'ติดต่อ' },
        { class: 'fas fa-envelope', name: 'จดหมาย' },
        { class: 'fas fa-tools', name: 'เครื่องมือ' },
        { class: 'fas fa-cog', name: 'การตั้งค่า' },
        { class: 'fas fa-bus', name: 'ขนส่ง' },
        { class: 'fas fa-hospital', name: 'โรงพยาบาล' },
        { class: 'fas fa-school', name: 'โรงเรียน' },
        { class: 'fas fa-tree', name: 'ต้นไม้' },
        { class: 'fas fa-fire', name: 'ไฟไหม้' },
        { class: 'fas fa-chart-line', name: 'สถิติ' },
        { class: 'fas fa-bullhorn', name: 'ประชาสัมพันธ์' },
        { class: 'fas fa-heart', name: 'สวัสดิการ' },
        { class: 'fas fa-landmark', name: 'ราชการ' },
        { class: 'fas fa-question-circle', name: 'อื่นๆ' }
    ],
    
    showModal: function() {
        const modal = new bootstrap.Modal(document.getElementById('categoryModal'));
        modal.show();
        this.loadCategories();
    },
    
    loadCategories: function() {
        fetch(this.config.apiUrl)
            .then(response => response.json())
            .then(data => {
                this.renderCategories(data.categories || []);
            })
            .catch(error => {
                console.error('Error loading categories:', error);
                this.showAlert('เกิดข้อผิดพลาดในการโหลดข้อมูลหมวดหมู่', 'error');
            });
    },
    
    renderCategories: function(categories) {
        const tbody = document.getElementById('categoriesTableBody');
        
        if (categories.length === 0) {
            tbody.innerHTML = '<tr><td colspan="6" class="text-center text-muted">ไม่มีหมวดหมู่</td></tr>';
            return;
        }
        
        const html = categories.map(cat => `
            <tr>
                <td>${cat.cat_order}</td>
                <td>
                    <i class="${cat.cat_icon}" style="color: ${cat.cat_color}; margin-right: 8px;"></i>
                    ${cat.cat_name}
                </td>
                <td><code>${cat.cat_icon}</code></td>
                <td>
                    <div class="d-flex align-items-center">
                        <div style="width: 20px; height: 20px; background: ${cat.cat_color}; border-radius: 3px; margin-right: 8px;"></div>
                        <small>${cat.cat_color}</small>
                    </div>
                </td>
                <td>
                    <span class="badge ${cat.cat_status == 1 ? 'bg-success' : 'bg-secondary'}">
                        ${cat.cat_status == 1 ? 'เปิดใช้งาน' : 'ปิดใช้งาน'}
                    </span>
                </td>
                <td>
                    <button class="btn btn-sm btn-outline-primary me-1" onclick="window.categoryManager.editCategory(${cat.cat_id})" title="แก้ไข">
                        <i class="fas fa-edit"></i>
                    </button>
                    <button class="btn btn-sm btn-outline-danger" onclick="window.categoryManager.deleteCategory(${cat.cat_id}, '${cat.cat_name}')" title="ลบ">
                        <i class="fas fa-trash"></i>
                    </button>
                </td>
            </tr>
        `).join('');
        
        tbody.innerHTML = html;
    },
    
    showIconSelector: function() {
        this.renderIconGrid();
        const modal = new bootstrap.Modal(document.getElementById('iconSelectorModal'));
        modal.show();
    },
    
    renderIconGrid: function() {
        const iconGrid = document.getElementById('iconGrid');
        const currentIcon = document.getElementById('categoryIcon').value;
        
        const searchHtml = `
            <div class="col-12">
                <div class="icon-search">
                    <input type="text" class="form-control" placeholder="ค้นหาไอคอน..." onkeyup="window.categoryManager.filterIcons(this.value)">
                </div>
            </div>
        `;
        
        const iconsHtml = this.iconsList.map(icon => `
            <div class="col-auto">
                <div class="icon-option ${currentIcon === icon.class ? 'selected' : ''}" 
                     onclick="window.categoryManager.selectIcon('${icon.class}', '${icon.name}')">
                    <i class="${icon.class}"></i>
                    <span>${icon.name}</span>
                </div>
            </div>
        `).join('');
        
        iconGrid.innerHTML = searchHtml + iconsHtml;
    },
    
    filterIcons: function(searchTerm) {
        const iconOptions = document.querySelectorAll('.icon-option');
        const term = searchTerm.toLowerCase();
        
        iconOptions.forEach(option => {
            const iconName = option.querySelector('span').textContent.toLowerCase();
            const iconClass = option.querySelector('i').className.toLowerCase();
            
            if (iconName.includes(term) || iconClass.includes(term)) {
                option.style.display = 'flex';
            } else {
                option.style.display = 'none';
            }
        });
    },
    
    selectIcon: function(iconClass, iconName) {
        // อัปเดต hidden input
        document.getElementById('categoryIcon').value = iconClass;
        
        // อัปเดต button display
        document.getElementById('selectedIcon').className = iconClass + ' me-2';
        document.getElementById('selectedIconText').textContent = iconName;
        
        // ปิด modal
        const modal = bootstrap.Modal.getInstance(document.getElementById('iconSelectorModal'));
        modal.hide();
        
        // อัปเดต selected state
        document.querySelectorAll('.icon-option').forEach(option => {
            option.classList.remove('selected');
        });
        
        // หา element ที่ถูกคลิกและเพิ่ม selected class
        const clickedOption = Array.from(document.querySelectorAll('.icon-option')).find(option => {
            return option.querySelector('i').className === iconClass;
        });
        
        if (clickedOption) {
            clickedOption.classList.add('selected');
        }
    },
    
    editCategory: function(catId) {
        fetch(`${this.config.apiUrl}/${catId}`)
            .then(response => response.json())
            .then(data => {
                if (data.success && data.category) {
                    const cat = data.category;
                    document.getElementById('categoryId').value = cat.cat_id;
                    document.getElementById('categoryName').value = cat.cat_name;
                    document.getElementById('categoryColor').value = cat.cat_color;
                    document.getElementById('categoryOrder').value = cat.cat_order;
                    document.getElementById('categoryStatus').value = cat.cat_status;
                    
                    // อัปเดต icon display
                    document.getElementById('categoryIcon').value = cat.cat_icon;
                    document.getElementById('selectedIcon').className = cat.cat_icon + ' me-2';
                    
                    // หา icon name จากรายการ
                    const iconData = this.iconsList.find(icon => icon.class === cat.cat_icon);
                    document.getElementById('selectedIconText').textContent = iconData ? iconData.name : 'ไอคอนที่เลือก';
                    
                    document.getElementById('saveCategoryBtn').innerHTML = '<i class="fas fa-save me-2"></i>อัปเดต';
                    
                    // เลื่อนไปที่ form
                    document.getElementById('categoryName').focus();
                }
            })
            .catch(error => {
                console.error('Error loading category:', error);
                this.showAlert('เกิดข้อผิดพลาดในการโหลดข้อมูลหมวดหมู่', 'error');
            });
    },
    
    deleteCategory: function(catId, catName) {
        if (typeof Swal !== 'undefined') {
            Swal.fire({
                title: 'ยืนยันการลบ',
                text: `คุณต้องการลบหมวดหมู่ "${catName}" หรือไม่?`,
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#dc3545',
                cancelButtonColor: '#6c757d',
                confirmButtonText: 'ลบ',
                cancelButtonText: 'ยกเลิก'
            }).then((result) => {
                if (result.isConfirmed) {
                    this.performDelete(catId);
                }
            });
        } else {
            if (confirm(`คุณต้องการลบหมวดหมู่ "${catName}" หรือไม่?`)) {
                this.performDelete(catId);
            }
        }
    },
    
    performDelete: function(catId) {
        fetch(this.config.deleteUrl, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
            },
            body: JSON.stringify({ cat_id: catId })
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                this.showAlert('ลบหมวดหมู่เรียบร้อยแล้ว', 'success');
                this.loadCategories();
            } else {
                this.showAlert(data.message || 'เกิดข้อผิดพลาดในการลบหมวดหมู่', 'error');
            }
        })
        .catch(error => {
            console.error('Error deleting category:', error);
            this.showAlert('เกิดข้อผิดพลาดในการเชื่อมต่อ', 'error');
        });
    },
    
    resetForm: function() {
        document.getElementById('categoryForm').reset();
        document.getElementById('categoryId').value = '';
        document.getElementById('categoryColor').value = '#e55a2b';
        document.getElementById('categoryOrder').value = '0';
        document.getElementById('categoryStatus').value = '1';
        
        // รีเซ็ต icon
        document.getElementById('categoryIcon').value = 'fas fa-exclamation-circle';
        document.getElementById('selectedIcon').className = 'fas fa-exclamation-circle me-2';
        document.getElementById('selectedIconText').textContent = 'แจ้งเตือน';
        
        // รีเซ็ต button text
        document.getElementById('saveCategoryBtn').innerHTML = '<i class="fas fa-save me-2"></i>บันทึก';
    },
    
    showAlert: function(message, type) {
        if (typeof Swal !== 'undefined') {
            Swal.fire({
                icon: type === 'error' ? 'error' : 'success',
                title: type === 'error' ? 'เกิดข้อผิดพลาด' : 'สำเร็จ',
                text: message,
                timer: type === 'success' ? 2000 : null,
                showConfirmButton: type === 'error',
                confirmButtonText: 'ตกลง'
            });
        } else {
            alert(message);
        }
    },
    
    // เพิ่มฟังก์ชันสำหรับ validation
    validateForm: function() {
        const categoryName = document.getElementById('categoryName').value.trim();
        
        if (!categoryName) {
            this.showAlert('กรุณากรอกชื่อหมวดหมู่', 'error');
            document.getElementById('categoryName').focus();
            return false;
        }
        
        if (categoryName.length > 100) {
            this.showAlert('ชื่อหมวดหมู่ต้องไม่เกิน 100 ตัวอักษร', 'error');
            document.getElementById('categoryName').focus();
            return false;
        }
        
        return true;
    },
    
    // เพิ่มฟังก์ชันสำหรับแสดง loading state
    setFormLoading: function(loading) {
        const btn = document.getElementById('saveCategoryBtn');
        const form = document.getElementById('categoryForm');
        
        if (loading) {
            btn.disabled = true;
            btn.innerHTML = '<i class="fas fa-spinner fa-spin me-2"></i>กำลังบันทึก...';
            form.style.opacity = '0.7';
        } else {
            btn.disabled = false;
            const isEdit = document.getElementById('categoryId').value;
            btn.innerHTML = isEdit ? '<i class="fas fa-save me-2"></i>อัปเดต' : '<i class="fas fa-save me-2"></i>บันทึก';
            form.style.opacity = '1';
        }
    }
};

// Handle form submission
document.addEventListener('DOMContentLoaded', function() {
    const categoryForm = document.getElementById('categoryForm');
    if (categoryForm) {
        categoryForm.addEventListener('submit', function(e) {
            e.preventDefault();
            
            // Validate form
            if (!window.categoryManager.validateForm()) {
                return;
            }
            
            // Set loading state
            window.categoryManager.setFormLoading(true);
            
            const formData = new FormData(this);
            const data = Object.fromEntries(formData.entries());
            
            fetch(window.categoryManager.config.saveUrl, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                },
                body: JSON.stringify(data)
            })
            .then(response => response.json())
            .then(result => {
                window.categoryManager.setFormLoading(false);
                
                if (result.success) {
                    window.categoryManager.showAlert(
                        data.cat_id ? 'อัปเดตหมวดหมู่เรียบร้อยแล้ว' : 'เพิ่มหมวดหมู่เรียบร้อยแล้ว', 
                        'success'
                    );
                    window.categoryManager.resetForm();
                    window.categoryManager.loadCategories();
                } else {
                    window.categoryManager.showAlert(result.message || 'เกิดข้อผิดพลาด', 'error');
                }
            })
            .catch(error => {
                window.categoryManager.setFormLoading(false);
                console.error('Error saving category:', error);
                window.categoryManager.showAlert('เกิดข้อผิดพลาดในการเชื่อมต่อ', 'error');
            });
        });
    }
    
    // เพิ่ม event listener สำหรับ input validation
    const categoryNameInput = document.getElementById('categoryName');
    if (categoryNameInput) {
        categoryNameInput.addEventListener('input', function() {
            const value = this.value.trim();
            const maxLength = 100;
            const remaining = maxLength - value.length;
            
            // แสดงจำนวนตัวอักษรที่เหลือ (ถ้าต้องการ)
            if (remaining < 20) {
                this.style.borderColor = remaining < 0 ? '#dc3545' : '#ffc107';
            } else {
                this.style.borderColor = '';
            }
        });
    }
});

</script>