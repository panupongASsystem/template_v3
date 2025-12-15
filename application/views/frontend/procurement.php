<div class="text-center pages-head">
<span class="font-pages-head">ประกาศจัดซื้อจัดจ้าง</span>
</div>
</div>
<img src="<?php echo base_url('docs/welcome-btm-light-other.png'); ?>">

<div class="bg-pages ">
    <div class="container-pages-news">
        <?php
        $count = count($query);
        $itemsPerPage = 27; // จำนวนรายการต่อหน้า
        $totalPages = ceil($count / $itemsPerPage);

        $currentPage = isset($_GET['page']) ? $_GET['page'] : 1;

        // ปรับตำแหน่งที่กำหนดค่า $numToShow
        $numToShow = 3; // จำนวนปุ่มที่ต้องการแสดง
        $half = floor($numToShow / 2);

        $startPage = max($currentPage - $half, 1);
        $endPage = min($startPage + $numToShow - 1, $totalPages);

        $startIndex = ($currentPage - 1) * $itemsPerPage;
        $endIndex = min($startIndex + $itemsPerPage - 1, $count - 1);

        for ($i = $startIndex; $i <= $endIndex; $i++) {
            $rs = $query[$i];
        ?>
            <div class="pages-select-pdf underline">
                <div class="row">
                    <div class="col-1 style-col-img">
                        <a href="<?php echo site_url('Pages/procurement_detail/' . $rs->procurement_id); ?>">
                            <img class="border-radius24" src="<?php echo base_url('docs/logo.png'); ?>" width="50px" height="50px">
                        </a>
                    </div>
                    <div class="col-9 font-pages-content">
                        <a href="<?php echo site_url('Pages/procurement_detail/' . $rs->procurement_id); ?>">
                            <span class="one-line-ellipsis"><?= $rs->procurement_name; ?></span>
                        </a>
                    </div>
                    <div class="col-2 span-time-pages-news">
                        <a href="<?php echo site_url('Pages/procurement_detail/' . $rs->procurement_id); ?>">
                            <span><svg xmlns="http://www.w3.org/2000/svg" width="12" height="16" fill="currentColor" class="bi bi-calendar-minus-fill" viewBox="0 0 16 16">
                                    <path d="M4 .5a.5.5 0 0 0-1 0V1H2a2 2 0 0 0-2 2v1h16V3a2 2 0 0 0-2-2h-1V.5a.5.5 0 0 0-1 0V1H4V.5zM16 14V5H0v9a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2zM6 10h4a.5.5 0 0 1 0 1H6a.5.5 0 0 1 0-1z" />
                                </svg>
                                <?php
                                // ในการใช้งาน setThaiMonth
                                $date = new DateTime($rs->procurement_date);
                                $day_th = $date->format('d');
                                $month_th = setThaiMonthAbbreviation($date->format('F')); // เรียกใช้ setThaiMonth สำหรับชื่อเดือน
                                $year_th = $date->format('Y') + 543; // เพิ่มขึ้น 543 ปี
                                $formattedDate = "$day_th $month_th $year_th"; // วันที่และเดือนเป็นภาษาไทย
                                echo $formattedDate;
                                ?>
                            </span>
                        </a>
                    </div>
                </div>
            </div>
        <?php } ?>
        <!-- จัดการหน้า -->
        <div class="pagination-container d-flex justify-content-end">
            <div class="pagination-pages">
                <ul class="pagination">
                    <!-- ปุ่ม "กลับไปหน้าแรก" -->
                    <?php if ($currentPage > 1) : ?>
                        <li class="page-item pagination-item">
                            <a class="" href="?page=1" aria-label="First">
                                <img src="<?php echo base_url('docs/s.pages-first.png'); ?>" class="pages-first">
                                <span aria-hidden="true"></span>
                            </a>
                        </li>
                    <?php endif; ?>

                    <!-- ปุ่ม Previous -->
                    <?php if ($currentPage > 1) : ?>
                        <li class="page-item" style="width: 55px; margin-left: -12px;">
                            <a class="" href="?page=<?php echo $currentPage - 1; ?>" aria-label="Previous">
                                <img src="<?php echo base_url('docs/s.pages-pre.png'); ?>" alt="Previous" class="pages-pre">
                                <span aria-hidden="true"></span>
                            </a>
                        </li>
                    <?php endif; ?>



                    <!-- แสดงปุ่ม "กลับไปหน้าแรก" ถ้าหน้าปัจจุบันไม่ได้ต่อเนื่องจากหน้าแรก -->
                    <?php
                    $numToShow = 3; // จำนวนปุ่มที่ต้องการแสดง
                    $half = floor($numToShow / 2);

                    // ปุ่มหน้าเริ่มต้น
                    $startPage = max($currentPage - $half, 1);

                    // ปุ่มหน้าสุดท้าย
                    $endPage = min($startPage + $numToShow - 1, $totalPages);

                    // แสดงปุ่ม "กลับไปหน้าแรก" ถ้าหน้าปัจจุบันไม่ได้ต่อเนื่องจากหน้าแรก
                    if ($startPage > 1) {
                    ?>
                        <li class="page-item pagination-item">
                            <a class="page-link" href="?page=1">1</a>
                        </li>
                        <?php if ($startPage > 2) : ?>
                            <li class="page-item pagination-item">
                                <a class="page-link" href="?page=2">2</a>
                            </li>
                            <li class="page-item pagination-item disabled">
                                <span class="page-link">...</span>
                            </li>
                        <?php endif; ?>
                    <?php
                    }

                    // แสดงปุ่มหน้า
                    for ($i = $startPage; $i <= $endPage; $i++) {
                    ?>
                        <li class="page-item pagination-item <?php echo ($i == $currentPage) ? 'active' : ''; ?>">
                            <a class="page-link" href="?page=<?php echo $i; ?>"><?php echo $i; ?></a>
                        </li>
                    <?php
                    }

                    // แสดงปุ่ม "..." ถ้าหน้าไม่ได้ต่อเนื่อง และรองสุดท้าย
                    if ($endPage < $totalPages - 1) {
                    ?>
                        <li class="page-item pagination-item disabled">
                            <span class="page-link">...</span>
                        </li>
                        <li class="page-item pagination-item">
                            <a class="page-link" href="?page=<?php echo $totalPages - 1; ?>"><?php echo $totalPages - 1; ?></a>
                        </li>
                    <?php
                    }

                    // แสดงปุ่มสุดท้าย
                    if ($endPage < $totalPages) {
                    ?>
                        <li class="page-item pagination-item <?php echo ($totalPages == $currentPage) ? 'active' : ''; ?>">
                            <a class="page-link" href="?page=<?php echo $totalPages; ?>"><?php echo $totalPages; ?></a>
                        </li>
                    <?php
                    }
                    ?>
                    <!-- ปุ่ม Next -->
                    <?php if ($currentPage < $totalPages) : ?>
                        <li class="page-item" style="width: 55px; margin-left: -10px;">
                            <a class="" href="?page=<?php echo $currentPage + 1; ?>" aria-label="Next">
                                <img src="<?php echo base_url('docs/s.pages-next.png'); ?>" alt="Next" class="pages-next">
                                <span aria-hidden="true"></span>
                            </a>
                        </li>
                    <?php endif; ?>

                    <!-- ปุ่ม "ไปหน้าสุดท้าย" -->
                    <?php if ($currentPage < $totalPages) : ?>
                        <li class="page-item pagination-item">
                            <a class="" href="?page=<?php echo $totalPages; ?>" aria-label="Last">
                                <img src="<?php echo base_url('docs/s.pages-last.png'); ?>" alt="Last" class="pages-last">
                                <span aria-hidden="true"></span>
                            </a>
                        </li>
                    <?php endif; ?>
                </ul>
            </div>
        </div>

        <!-- ฟอร์มกรอกหมายเลขหน้า -->
        <div class="pagination-jump-to-page d-flex justify-content-end">
            <form action="" method="GET" class="d-flex" id="pageForm" onsubmit="return validatePageInput();">

                <label style="font-size: 24px;">ไปหน้าที่&nbsp;&nbsp;</label>
                <input type="number" name="page" min="1" max="<?php echo $totalPages; ?>" value="<?php echo $currentPage; ?>" class="form-control" style="width: 60px; margin-right: 10px;" id="pageInput">
                <input type="image" src="<?php echo base_url('docs/s.pages-go.png'); ?>" alt="Go" class="pages-go" style="width: 40px; height: 40px;">
            </form>
        </div>
    </div>
</div>