<div class="text-center pages-head">
    <span class="font-pages-head">ข่าวสาร / กิจกรรม</span>
</div>
</div>
<img src="<?php echo base_url('docs/welcome-btm-light-other.png'); ?>">

<div class="bg-pages ">
    <div class="container-pages">
        <div class="row">
            <?php
            $count = count($query);
            $itemsPerPage = 25; // จำนวนรายการต่อหน้า
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
                <div class="col-2 mx-4">
                    <div class="page-border-activity">
                        <?php if (!empty($rs->activity_img)) : ?>
                            <a href="<?= site_url('pages/activity_detail/' . $rs->activity_id); ?>">
                                <img class="rounded-top-left-right" src="<?php echo base_url('docs/img/' . $rs->activity_img); ?>" width="100%" height="155px">
                            </a>
                        <?php else : ?>
                            <a href="<?= site_url('pages/activity_detail/' . $rs->activity_id); ?>">
                                <img class="rounded-top-left-right" src="<?php echo base_url('docs/logo.png'); ?>" width="100%" height="155px">
                            </a>
                        <?php endif; ?>
                        <div class="page-travel-content" style=" padding: 10px; padding-top: 10px; ">
                            <div class="activity-item underline">
                                <a href="<?= site_url('pages/activity_detail/' . $rs->activity_id); ?>">
                                    <span class="font-pages-heads-img two-line-ellipsis-activity"><?= $rs->activity_name; ?></span>
                                </a>
                                <?php
                                // วันที่ของข่าว
                                $activity_date = new DateTime($rs->activity_date);

                                // วันที่ปัจจุบัน
                                $current_date = new DateTime();

                                // คำนวณหาความต่างของวัน
                                $interval = $current_date->diff($activity_date);
                                $days_difference = $interval->days;

                                // ถ้ามากกว่า 30 วัน ให้ซ่อนไว้
                                if ($days_difference <= 30) {
                                    // แสดงรูปภาพ
                                    echo '<img src="' . base_url('docs/activity-new.gif') . '" class="activity-new-img">';
                                }
                                ?>
                            </div>

                            <div class="row" style="margin-top: 60px;">
                                <div class="col-7 mt-3 underline">
                                    <a href="<?= site_url('pages/activity_detail/' . $rs->activity_id); ?>">
                                        <svg style="color: #693708;" xmlns="http://www.w3.org/2000/svg" width="10" height="10" fill="currentColor" class="bi bi-calendar-minus-fill" viewBox="0 0 16 16">
                                            <path d="M4 .5a.5.5 0 0 0-1 0V1H2a2 2 0 0 0-2 2v1h16V3a2 2 0 0 0-2-2h-1V.5a.5.5 0 0 0-1 0V1H4V.5zM16 14V5H0v9a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2zM6 10h4a.5.5 0 0 1 0 1H6a.5.5 0 0 1 0-1z" />
                                        </svg>
                                        <span class="span-time-home ">
                                            <?php
                                            // ในการใช้งาน setThaiMonth
                                            $date = new DateTime($rs->activity_date);
                                            $day_th = $date->format('d');
                                            $month_th = setThaiMonth($date->format('F')); // เรียกใช้ setThaiMonth สำหรับชื่อเดือน
                                            $year_th = $date->format('Y') + 543; // เพิ่มขึ้น 543 ปี
                                            $formattedDate = "$day_th $month_th $year_th"; // วันที่และเดือนเป็นภาษาไทย
                                            echo $formattedDate;
                                            ?>
                                        </span>
                                    </a>
                                </div>
                                <div class="col-5">
                                    <div class="font-12 underline d-flex justify-content-end mt-4">
                                        <a href="<?= site_url('pages/activity_detail/' . $rs->activity_id); ?>"><svg xmlns="http://www.w3.org/2000/svg" style="color: black; margin-top: -2px;" width="16" height="16" fill="currentColor" class="bi bi-eye" viewBox="0 0 16 16">
                                                <path d="M16 8s-3-5.5-8-5.5S0 8 0 8s3 5.5 8 5.5S16 8 16 8M1.173 8a13 13 0 0 1 1.66-2.043C4.12 4.668 5.88 3.5 8 3.5s3.879 1.168 5.168 2.457A13 13 0 0 1 14.828 8q-.086.13-.195.288c-.335.48-.83 1.12-1.465 1.755C11.879 11.332 10.119 12.5 8 12.5s-3.879-1.168-5.168-2.457A13 13 0 0 1 1.172 8z" />
                                                <path d="M8 5.5a2.5 2.5 0 1 0 0 5 2.5 2.5 0 0 0 0-5M4.5 8a3.5 3.5 0 1 1 7 0 3.5 3.5 0 0 1-7 0" />
                                            </svg>&nbsp;เปิดดู : <span><?= $rs->activity_view; ?></span>
                                        </a>
                                    </div>
                                </div>
                            </div>
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
</div><br><br><br>