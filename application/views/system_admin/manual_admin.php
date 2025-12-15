<!-- Flash Messages -->
<?php if ($this->session->flashdata('success')): ?>
    <div class="alert alert-success alert-dismissible fade show animate__animated animate__fadeInDown" role="alert">
        <i class="bi bi-check-circle-fill"></i> <?= $this->session->flashdata('success'); ?>
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
<?php endif; ?>

<?php if ($this->session->flashdata('error')): ?>
    <div class="alert alert-danger alert-dismissible fade show animate__animated animate__fadeInDown" role="alert">
        <i class="bi bi-x-circle-fill"></i> <?= $this->session->flashdata('error'); ?>
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
<?php endif; ?>

<!-- CSS & Animations -->
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/animate.css/4.1.1/animate.min.css"/>
<style>
    body {
        background: #f8f9fa;
        font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
    }

    .container {
        max-width: 1400px;
    }

    /* Header Section */
    .manual-header {
        background: white;
        border-radius: 15px;
        padding: 40px;
        margin-bottom: 40px;
        box-shadow: 0 2px 10px rgba(0, 0, 0, 0.05);
        text-align: center;
        border: 1px solid #e9ecef;
    }

    .manual-header h1 {
        font-size: 2.5em;
        font-weight: 600;
        color: #2c3e50;
        margin-bottom: 10px;
    }

    .manual-header p {
        color: #7f8c8d;
        font-size: 1.1em;
        margin: 0;
    }

    /* Search Box */
    .search-container {
        background: white;
        border-radius: 50px;
        padding: 15px 30px;
        box-shadow: 0 2px 10px rgba(0, 0, 0, 0.05);
        margin-bottom: 30px;
        display: flex;
        align-items: center;
        gap: 15px;
        border: 1px solid #e9ecef;
        transition: all 0.3s ease;
    }

    .search-container:focus-within {
        border-color: #95a5a6;
        box-shadow: 0 4px 15px rgba(0, 0, 0, 0.1);
    }

    .search-container input {
        border: none;
        outline: none;
        flex: 1;
        font-size: 1em;
    }

    .search-container i {
        color: #95a5a6;
        font-size: 1.2em;
    }

    /* Category Tabs */
    .category-tabs {
        display: flex;
        gap: 15px;
        margin-bottom: 30px;
        flex-wrap: wrap;
    }

    .category-tab {
        background: white;
        border: 2px solid #e9ecef;
        border-radius: 50px;
        padding: 12px 25px;
        font-size: 1em;
        font-weight: 500;
        color: #7f8c8d;
        cursor: pointer;
        transition: all 0.3s ease;
    }

    .category-tab:hover {
        border-color: #95a5a6;
        color: #2c3e50;
    }

    .category-tab.active {
        background: #2c3e50;
        border-color: #2c3e50;
        color: white;
    }

    /* Manual Section */
    .manual-section {
        margin-bottom: 50px;
    }

    .section-title {
        font-size: 1.6em;
        font-weight: 600;
        color: #2c3e50;
        margin-bottom: 25px;
        padding-left: 15px;
        border-left: 4px solid #95a5a6;
    }

    /* 🆕 Manual Grid - 3 คอลัมน์ */
    .manual-grid {
        display: grid;
        grid-template-columns: repeat(3, 1fr);
        gap: 25px;
    }

    /* Manual Card */
    .manual-card {
        background: white;
        border-radius: 15px;
        padding: 25px;
        border: 1px solid #e9ecef;
        transition: all 0.3s ease;
    }

    .manual-card:hover {
        box-shadow: 0 5px 20px rgba(0, 0, 0, 0.08);
        transform: translateY(-5px);
        border-color: #bdc3c7;
    }

    /* 🆕 Manual Icon - ไม่มีไล่สี */
    .manual-icon {
        width: 70px;
        height: 70px;
        border-radius: 12px;
        background: #ecf0f1;
        display: flex;
        align-items: center;
        justify-content: center;
        margin-bottom: 20px;
        transition: all 0.3s ease;
    }

    .manual-card:hover .manual-icon {
        background: #d5dbdb;
        transform: scale(1.05);
    }

    .manual-icon i {
        font-size: 2em;
        color: #7f8c8d;
    }

    /* 🆕 Icon สำหรับ LINE OA */
    .manual-icon.line {
        background: #e8f5e9;
    }

    .manual-icon.line i {
        color: #4caf50;
    }

    .manual-card:hover .manual-icon.line {
        background: #c8e6c9;
    }

    .manual-title {
        font-size: 1.2em;
        font-weight: 600;
        color: #2c3e50;
        margin-bottom: 15px;
        min-height: 3em;
        line-height: 1.5;
    }

    .manual-meta {
        display: flex;
        flex-direction: column;
        gap: 8px;
        margin-bottom: 20px;
    }

    .meta-item {
        display: flex;
        align-items: center;
        gap: 8px;
        font-size: 0.9em;
        color: #7f8c8d;
    }

    .meta-item i {
        font-size: 1em;
        width: 16px;
    }

    /* Buttons - ไม่มีไล่สี */
    .manual-actions {
        display: grid;
        grid-template-columns: 1fr 1fr;
        gap: 10px;
        padding-top: 15px;
        border-top: 1px solid #ecf0f1;
    }

    .btn-modern {
        padding: 10px 15px;
        border: none;
        border-radius: 8px;
        font-weight: 500;
        font-size: 0.9em;
        cursor: pointer;
        transition: all 0.3s ease;
        display: flex;
        align-items: center;
        justify-content: center;
        gap: 6px;
        text-decoration: none;
    }

    .btn-view {
        background: #e3f2fd;
        color: #1976d2;
    }

    .btn-view:hover {
        background: #bbdefb;
        color: #1565c0;
        transform: translateY(-2px);
    }

    .btn-download {
        background: #e8f5e9;
        color: #388e3c;
    }

    .btn-download:hover {
        background: #c8e6c9;
        color: #2e7d32;
        transform: translateY(-2px);
    }

    .btn-edit {
        background: #fff3e0;
        color: #f57c00;
    }

    .btn-edit:hover {
        background: #ffe0b2;
        color: #ef6c00;
        transform: translateY(-2px);
    }

    .btn-delete {
        background: #ffebee;
        color: #d32f2f;
    }

    .btn-delete:hover {
        background: #ffcdd2;
        color: #c62828;
        transform: translateY(-2px);
    }

    /* Floating Add Button */
    .btn-add-manual {
        position: fixed;
        bottom: 30px;
        right: 30px;
        width: 60px;
        height: 60px;
        border-radius: 50%;
        background: #2c3e50;
        color: white;
        border: none;
        font-size: 1.6em;
        cursor: pointer;
        box-shadow: 0 4px 12px rgba(0, 0, 0, 0.15);
        transition: all 0.3s ease;
        z-index: 1000;
        display: flex;
        align-items: center;
        justify-content: center;
        text-decoration: none;
    }

    .btn-add-manual:hover {
        background: #34495e;
        transform: scale(1.1);
        box-shadow: 0 6px 20px rgba(0, 0, 0, 0.2);
        color: white;
    }

    /* Empty State */
    .empty-state {
        text-align: center;
        padding: 60px 20px;
        background: white;
        border-radius: 15px;
        border: 1px solid #e9ecef;
        grid-column: 1 / -1;
    }

    .empty-state i {
        font-size: 4em;
        color: #ecf0f1;
        margin-bottom: 20px;
    }

    .empty-state h3 {
        color: #7f8c8d;
        font-size: 1.3em;
        margin-bottom: 10px;
    }

    .empty-state p {
        color: #95a5a6;
    }

    /* Responsive */
    @media (max-width: 1200px) {
        .manual-grid {
            grid-template-columns: repeat(2, 1fr);
        }
    }

    @media (max-width: 768px) {
        .manual-header h1 {
            font-size: 2em;
        }

        .manual-grid {
            grid-template-columns: 1fr;
        }

        .category-tabs {
            flex-direction: column;
        }

        .category-tab {
            width: 100%;
        }
    }
</style>

<div class="container mt-4 mb-5">
    <!-- Header -->
    <div class="manual-header animate__animated animate__fadeInDown">
        <h1>📚 คู่มือการใช้งาน</h1>
        <p>ศูนย์รวมเอกสารคู่มือการใช้งานระบบทั้งหมด</p>
    </div>

    <!-- Search Box -->
    <div class="search-container animate__animated animate__fadeInUp">
        <i class="bi bi-search"></i>
        <input type="text" id="searchManual" placeholder="ค้นหาคู่มือ...">
    </div>

    <!-- Category Tabs -->
    <div class="category-tabs animate__animated animate__fadeInUp" style="animation-delay: 0.1s;">
        <button class="category-tab active" data-category="all">
            <i class="bi bi-grid"></i> ทั้งหมด
        </button>
        <button class="category-tab" data-category="admin">
            <i class="bi bi-person-gear"></i> คู่มือแอดมิน
        </button>
        <button class="category-tab" data-category="line">
            <i class="bi bi-line"></i> คู่มือ LINE OA
        </button>
    </div>

    <!-- Section: คู่มือทั้งหมด 
     ✅ bi-chat-dots - ไอคอนแชท (แนะนำ)
        bi-chat-square-dots - แชทสี่เหลี่ยม
        bi-chat-left-dots - แชทซ้าย
        bi-messenger - แชท messenger
 -->
    <div class="manual-section category-section" data-section="all">
        <h2 class="section-title animate__animated animate__fadeInLeft">
            📚 คู่มือการใช้งานทั้งหมด
        </h2>
        
        <div class="manual-grid">
            <!-- คู่มือแอดมิน -->
            <?php if (!empty($manuals)): ?>
                <?php foreach ($manuals as $row): ?>
                    <div class="manual-card animate__animated animate__fadeInUp" data-category="admin">
                        <!-- 🆕 Icon ใหม่ที่แสดงได้แน่นอน -->
                        <div class="manual-icon">
                            <i class="bi bi-person"></i>
                        </div>
                        
                        <h3 class="manual-title"><?= htmlspecialchars($row->manual_admin_name); ?></h3>
                        
                        <div class="manual-meta">
                            <div class="meta-item">
                                <i class="bi bi-download"></i>
                                <span><?= number_format($row->manual_admin_download); ?> ครั้ง</span>
                            </div>
                            <?php if (!empty($row->manual_admin_by)): ?>
                                <div class="meta-item">
                                    <i class="bi bi-person"></i>
                                    <span><?= htmlspecialchars($row->manual_admin_by); ?></span>
                                </div>
                            <?php endif; ?>
                            <?php if (!empty($row->manual_admin_datesave)): ?>
                                <div class="meta-item">
                                    <i class="bi bi-calendar"></i>
                                    <span><?= date('d/m/Y', strtotime($row->manual_admin_datesave)); ?></span>
                                </div>
                            <?php endif; ?>
                        </div>

                        <?php if ($row->manual_admin_pdf): ?>
                            <div class="manual-actions">
                                <a href="<?= base_url('docs/file/' . $row->manual_admin_pdf); ?>" 
                                   target="_blank" 
                                   class="btn-modern btn-view"
                                   title="ดู PDF">
                                    <i class="bi bi-eye"></i> ดู
                                </a>
                                <a href="<?= site_url('manual_admin_backend/download/' . $row->manual_admin_id); ?>" 
                                   class="btn-modern btn-download"
                                   title="ดาวน์โหลด">
                                    <i class="bi bi-download"></i> ดาวน์โหลด
                                </a>
                                <a href="<?= site_url('manual_admin_backend/edit/' . $row->manual_admin_id); ?>" 
                                   class="btn-modern btn-edit"
                                   title="แก้ไข">
                                    <i class="bi bi-pencil"></i> แก้ไข
                                </a>
                                <button onclick="confirmDelete(<?= $row->manual_admin_id; ?>);" 
                                        class="btn-modern btn-delete"
                                        title="ลบ">
                                    <i class="bi bi-trash"></i> ลบ
                                </button>
                            </div>
                        <?php else: ?>
                            <div class="alert alert-warning mb-0">
                                <i class="bi bi-exclamation-triangle"></i> ยังไม่มีไฟล์อัปโหลด
                            </div>
                        <?php endif; ?>
                    </div>
                <?php endforeach; ?>
            <?php endif; ?>

            <!-- คู่มือ LINE OA -->
            <?php if ($has_line_manual): ?>
                <div class="manual-card animate__animated animate__fadeInUp" data-category="line">
                    <!-- 🆕 Icon LINE ใหม่ -->
                    <div class="manual-icon line">
                        <i class="bi bi-chat-dots"></i>
                    </div>
                    
                    <h3 class="manual-title">คู่มือการใช้งานแชท LINE Official Account</h3>
                    
                    <div class="manual-meta">
                        <div class="meta-item">
                            <i class="bi bi-file-pdf"></i>
                            <span>PDF Document</span>
                        </div>
                        <div class="meta-item">
                            <i class="bi bi-download"></i>
                            <span>ไม่จำกัดจำนวน</span>
                        </div>
                    </div>

                    <div class="manual-actions">
                        <a href="<?= base_url($line_manual_path); ?>" 
                           target="_blank" 
                           class="btn-modern btn-view"
                           title="ดู PDF">
                            <i class="bi bi-eye"></i> ดู
                        </a>
                        <a href="<?= site_url('manual_admin_backend/download_line_manual'); ?>" 
                           class="btn-modern btn-download"
                           title="ดาวน์โหลด">
                            <i class="bi bi-download"></i> ดาวน์โหลด
                        </a>
                    </div>
                </div>
            <?php endif; ?>

            <!-- Empty State ถ้าไม่มีทั้งหมด -->
            <?php if (empty($manuals) && !$has_line_manual): ?>
                <div class="empty-state">
                    <i class="bi bi-inbox"></i>
                    <h3>ยังไม่มีคู่มือ</h3>
                    <p>เริ่มต้นเพิ่มคู่มือแรกของคุณเลย</p>
                </div>
            <?php endif; ?>
        </div>
    </div>
</div>

<!-- Floating Add Button -->
<a href="<?= site_url('manual_admin_backend/create'); ?>" class="btn-add-manual" title="เพิ่มคู่มือใหม่">
    <i class="bi bi-plus-lg"></i>
</a>

<!-- JavaScript -->
<script>
$(document).ready(function() {
    // Search Functionality
    $('#searchManual').on('keyup', function() {
        var value = $(this).val().toLowerCase();
        $('.manual-card').filter(function() {
            $(this).toggle($(this).text().toLowerCase().indexOf(value) > -1);
        });
    });

    // Category Filter
    $('.category-tab').on('click', function() {
        $('.category-tab').removeClass('active');
        $(this).addClass('active');
        
        var category = $(this).data('category');
        
        if (category === 'all') {
            $('.manual-card').fadeIn(300);
        } else {
            $('.manual-card').hide();
            $('.manual-card[data-category="' + category + '"]').fadeIn(300);
        }
    });

    // Auto-hide flash messages
    setTimeout(function() {
        $('.alert').fadeOut('slow');
    }, 5000);
});

// Confirm Delete
function confirmDelete(id) {
    Swal.fire({
        title: 'ยืนยันการลบ?',
        text: "คุณจะไม่สามารถกู้คืนข้อมูลนี้ได้อีก!",
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#e74c3c',
        cancelButtonColor: '#95a5a6',
        confirmButtonText: 'ใช่, ลบเลย!',
        cancelButtonText: 'ยกเลิก',
        reverseButtons: true
    }).then((result) => {
        if (result.isConfirmed) {
            window.location.href = "<?= site_url('manual_admin_backend/delete/'); ?>" + id;
        }
    });
}
</script>