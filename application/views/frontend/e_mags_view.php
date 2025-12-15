<div class="text-center pages-head">
    <span class="font-pages-head">e-book วารสารออนไลน์</span>
</div>
</div>
<img src="<?php echo base_url('docs/welcome-btm-light-other.png'); ?>">

<div class="bg-pages">
    <div class="container-pages">
        <!-- Search Form -->
        <div class="search-container-emag mb-4">
            <form method="GET" class="row g-3 align-items-center">
                <div class="col-md-8">
                    <input type="text" class="form-control" name="search"
                        placeholder="ค้นหารายงานประจำปี..."
                        value="<?= htmlspecialchars($search ?? '') ?>">
                </div>
                <div class="col-md-4">
                    <button type="submit" class="btn btn-primary w-100">
                        <i class="bi bi-search"></i> ค้นหา
                    </button>
                    <?php if (!empty($search)): ?>
                        <a href="<?= current_url() ?>" class="btn btn-secondary w-100 mt-2">
                            <i class="bi bi-arrow-clockwise"></i> แสดงทั้งหมด
                        </a>
                    <?php endif; ?>
                </div>
            </form>
        </div>

        <!-- Results Info -->
        <div class="results-info mb-3">
            <?php if (!empty($search)): ?>
                <p class="text-muted">
                    พบ <?= number_format($total_records) ?> รายการ จากการค้นหา
                    "<strong><?= htmlspecialchars($search) ?></strong>"
                </p>
            <?php else: ?>
                <p class="text-muted">
                    แสดง <?= number_format($total_records) ?> รายการทั้งหมด
                </p>
            <?php endif; ?>
        </div>

        <!-- Magazine Showcase Grid -->
        <div class="magazine-list">
            <div id="magazineContainer">
                <!-- จะถูกเติมด้วย JavaScript -->
            </div>
        </div>

        <!-- Pagination -->
        <?php if ($total_pages > 1): ?>
            <nav aria-label="Page navigation" class="mt-4">
                <ul class="pagination justify-content-center">
                    <!-- Previous Page -->
                    <?php if ($current_page > 1): ?>
                        <li class="page-item">
                            <a class="page-link" href="?page=<?= $current_page - 1 ?><?= !empty($search) ? '&search=' . urlencode($search) : '' ?>">
                                <i class="bi bi-chevron-left"></i> ก่อนหน้า
                            </a>
                        </li>
                    <?php endif; ?>

                    <!-- Page Numbers -->
                    <?php
                    $start_page = max(1, $current_page - 2);
                    $end_page = min($total_pages, $current_page + 2);

                    if ($start_page > 1): ?>
                        <li class="page-item">
                            <a class="page-link" href="?page=1<?= !empty($search) ? '&search=' . urlencode($search) : '' ?>">1</a>
                        </li>
                        <?php if ($start_page > 2): ?>
                            <li class="page-item disabled">
                                <span class="page-link">...</span>
                            </li>
                        <?php endif; ?>
                    <?php endif; ?>

                    <?php for ($i = $start_page; $i <= $end_page; $i++): ?>
                        <li class="page-item <?= $i == $current_page ? 'active' : '' ?>">
                            <a class="page-link" href="?page=<?= $i ?><?= !empty($search) ? '&search=' . urlencode($search) : '' ?>">
                                <?= $i ?>
                            </a>
                        </li>
                    <?php endfor; ?>

                    <?php if ($end_page < $total_pages): ?>
                        <?php if ($end_page < $total_pages - 1): ?>
                            <li class="page-item disabled">
                                <span class="page-link">...</span>
                            </li>
                        <?php endif; ?>
                        <li class="page-item">
                            <a class="page-link" href="?page=<?= $total_pages ?><?= !empty($search) ? '&search=' . urlencode($search) : '' ?>">
                                <?= $total_pages ?>
                            </a>
                        </li>
                    <?php endif; ?>

                    <!-- Next Page -->
                    <?php if ($current_page < $total_pages): ?>
                        <li class="page-item">
                            <a class="page-link" href="?page=<?= $current_page + 1 ?><?= !empty($search) ? '&search=' . urlencode($search) : '' ?>">
                                ถัดไป <i class="bi bi-chevron-right"></i>
                            </a>
                        </li>
                    <?php endif; ?>
                </ul>

                <!-- Page Info -->
                <div class="text-center mt-3">
                    <small class="text-muted">
                        หน้า <?= $current_page ?> จาก <?= $total_pages ?>
                        (แสดง <?= number_format(min($per_page, $total_records - (($current_page - 1) * $per_page))) ?>
                        จาก <?= number_format($total_records) ?> รายการ)
                    </small>
                </div>
            </nav>
        <?php endif; ?>
     </div>
    </div>
</div><br><br><br>

<style>
     .search-container-emag {
        background: white;
        padding: 20px;
        border-radius: 10px;
        box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
        margin-bottom: 20px;
    }

    .results-info {
        padding: 10px 15px;
        background: #f8f9fa;
        border-radius: 5px;
        border-left: 4px solid #007bff;
    }

    .pagination .page-link {
        color: #007bff;
        border: 1px solid #dee2e6;
        padding: 0.5rem 0.75rem;
    }

    .pagination .page-item.active .page-link {
        background-color: #007bff;
        border-color: #007bff;
    }

    .pagination .page-link:hover {
        background-color: #e9ecef;
        border-color: #dee2e6;
        color: #007bff;
    }
</style>