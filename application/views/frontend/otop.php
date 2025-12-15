<div class="text-center pages-head">
    <span class="font-pages-head">ผลิตภัณฑ์ชุมชน</span>
</div>
</div>
<img src="<?php echo base_url('docs/welcome-btm-light-other.png'); ?>">

<div class="bg-pages ">
    <div class="container-pages-news" style="z-index: 10; position: relative;">
        <div class="pages-content break-word">
            <?php
            $count = count($otops); // เปลี่ยน $qOtop เป็น $otops
            $itemsPerPage = 5; // จำนวนรายการต่อหน้า
            $totalPages = ceil($count / $itemsPerPage);

            $currentPage = isset($_GET['page']) ? $_GET['page'] : 1;

            $startIndex = ($currentPage - 1) * $itemsPerPage;
            $endIndex = min($startIndex + $itemsPerPage - 1, $count - 1);

            for ($i = $startIndex; $i <= $endIndex; $i++) {
                $otop = $otops[$i];
            ?>
                <div class=" row page-border-otop">
                    <div class="col-8">
                        <div class="scrollable-container-otop">
                            <span class="font-otop-head"><?= $otop->otop_name; ?></span>
                            <div class="border-gray-332"></div>
                            <div style="padding-bottom: 10px;">
                                <span class="span-time-pages-news">
                                    <svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" fill="currentColor" class="bi bi-calendar-minus-fill" viewBox="0 0 16 16">
                                        <path d="M4 .5a.5.5 0 0 0-1 0V1H2a2 2 0 0 0-2 2v1h16V3a2 2 0 0 0-2-2h-1V.5a.5.5 0 0 0-1 0V1H4V.5zM16 14V5H0v9a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2zM6 10h4a.5.5 0 0 1 0 1H6a.5.5 0 0 1 0-1z" />
                                    </svg>
                                    <?php
                                    $date = new DateTime($otop->otop_date);
                                    $day_th = $date->format('d');
                                    $month_th = setThaiMonth($date->format('F')); // เรียกใช้ setThaiMonth สำหรับชื่อเดือน
                                    $year_th = $date->format('Y') + 543; // เพิ่มขึ้น 543 ปี
                                    $formattedDate = "$day_th $month_th $year_th"; // วันที่และเดือนเป็นภาษาไทย
                                    echo $formattedDate;
                                    ?>
                                </span>
                                <span class="span-time-pages-news">
                                    <svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" fill="currentColor" class="bi bi-clock-fill" viewBox="0 0 16 16">
                                        <path d="M16 8A8 8 0 1 1 0 8a8 8 0 0 1 16 0zM8 3.5a.5.5 0 0 0-1 0V9a.5.5 0 0 0 .252.434l3.5 2a.5.5 0 0 0 .496-.868L8 8.71V3.5z" />
                                    </svg>
                                    <?php
                                    $date = new DateTime($otop->otop_date);
                                    $formattedTime = $date->format('H:i'); // เวลา
                                    echo $formattedTime;
                                    ?>
                                    น.
                                </span>
                            </div>
                            <div style="padding-bottom: 10px;">
                                <span class="font-otop-content"><?= strip_tags($otop->otop_detail); ?></span>
                            </div>
                            <div class="font-otop-content">
                                <span>ประเภทสินค้า : <?= $otop->otop_type; ?></span><br>
                                <span>ขนาด : <?= $otop->otop_size; ?> เซนติเมตร</span><br>
                                <span>น้ำหนัก : <?= $otop->otop_weight; ?> กิโลกรัม</span><br>
                                <span>ราคา : <?= $otop->otop_price; ?> บาท</span><br>
                                <span>ที่อยู่ : <?= $otop->otop_location; ?></span><br>
                                <span>เบอร์ติดต่อ : <?= $otop->otop_phone; ?></span><br>
                                <span>Facebook : <?= $otop->otop_fb; ?></span><br>
                            </div>
                        </div>
                    </div>
                    <div class="col-4 ">
                        <div class="text-center">
                            <?php if (!empty($otop->otop_img)) : ?>
                                <img src="<?php echo base_url('docs/img/' . $otop->otop_img); ?>" width="auto" style="max-width: 100%;" height="310px">
                            <?php endif; ?>
                        </div>
                        <br>
                        <!-- <?php foreach ($otop->images as $image) : ?>
                                    <a href="<?php echo base_url('docs/img/' . $image->otop_img_img); ?>" data-lightbox="image-<?php echo $otop->otop_id; ?>">
                                        <img src="<?= base_url('docs/img/' . $image->otop_img_img); ?>" alt="otop Image" width="100">
                                    </a>
                                <?php endforeach; ?> -->
                        <swiper-container class="mySwiperOtop"
                            slides-per-view="3"
                            centered-slides="true"
                            space-between="30"
                            pagination="true"
                            pagination-type="fraction"
                            navigation="true">
                            <?php foreach ($otop->images as $image) : ?>
                                <swiper-slide>
                                    <a href="<?php echo base_url('docs/img/' . $image->otop_img_img); ?>"
                                        data-fancybox="gallery-otop-<?php echo $otop->otop_id; ?>"
                                        data-caption="<?php echo $image->otop_img_img; ?>">
                                        <img src="<?= base_url('docs/img/' . $image->otop_img_img); ?>"
                                            alt="otop Image"
                                            width="100">
                                    </a>
                                </swiper-slide>
                            <?php endforeach; ?>
                        </swiper-container>
                    </div>
                </div>
                <div style="margin-top: 50px;"></div>
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