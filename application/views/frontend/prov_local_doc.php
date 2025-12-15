<div class="text-center pages-head">
    <span class="font-pages-head">หนังสือราชการ สถ.จ.<?php echo get_config_value('province'); ?></span>
</div>
</div>
<img src="<?php echo base_url('docs/welcome-btm-light-other.png'); ?>">

<div class="bg-pages ">
    <div class="container-pages-news">
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
            $topic = $rs['topic'];
            if (strpos($topic, '[ด่วนที่สุด]') !== false) {
                $topic = str_replace('[ด่วนที่สุด]', '<span class="most_urgent">[ด่วนที่สุด]</span>', $topic);
            } elseif (strpos($topic, '[ด่วนมาก]') !== false) {
                $topic = str_replace('[ด่วนมาก]', '<span class="very_urgent">[ด่วนมาก]</span>', $topic);
            } elseif (strpos($topic, '[ทั่วไป]') !== false) {
                $topic = str_replace('[ทั่วไป]', '<span class="green">[ทั่วไป]</span>', $topic);
            }
        ?>
            <div class="pages-select-dla">
                <div class="row">
                    <div class="col-2 span-time-pages-news">
                        <span><svg xmlns="http://www.w3.org/2000/svg" width="12" height="16" fill="currentColor" class="bi bi-calendar-minus-fill" viewBox="0 0 16 16">
                                <path d="M4 .5a.5.5 0 0 0-1 0V1H2a2 2 0 0 0-2 2v1h16V3a2 2 0 0 0-2-2h-1V.5a.5.5 0 0 0-1 0V1H4V.5zM16 14V5H0v9a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2zM6 10h4a.5.5 0 0 1 0 1H6a.5.5 0 0 1 0-1z" />
                            </svg>
                            <?php echo $rs['doc_date']; ?>
                        </span>
                    </div>
                    <div class="col-1" style="padding-left: 30px;">
                        <?php
                        // สมมติว่าค่าที่ได้รับมาจากตัวแปร $rs['doc_date'] อยู่ในรูปแบบ "10 มิถุนายน 2567" หรือ "10 มิ.ย. 2567"
                        $dateStr = $rs['doc_date'];

                        // กำหนดอาเรย์ของเดือนภาษาไทยทั้งแบบเต็มและแบบย่อ
                        $thaiMonths = [
                            // เดือนแบบเต็ม
                            'มกราคม' => '01',
                            'กุมภาพันธ์' => '02',
                            'มีนาคม' => '03',
                            'เมษายน' => '04',
                            'พฤษภาคม' => '05',
                            'มิถุนายน' => '06',
                            'กรกฎาคม' => '07',
                            'สิงหาคม' => '08',
                            'กันยายน' => '09',
                            'ตุลาคม' => '10',
                            'พฤศจิกายน' => '11',
                            'ธันวาคม' => '12',
                            // เดือนแบบย่อ
                            'ม.ค.' => '01',
                            'ก.พ.' => '02',
                            'มี.ค.' => '03',
                            'เม.ย.' => '04',
                            'พ.ค.' => '05',
                            'มิ.ย.' => '06',
                            'ก.ค.' => '07',
                            'ส.ค.' => '08',
                            'ก.ย.' => '09',
                            'ต.ค.' => '10',
                            'พ.ย.' => '11',
                            'ธ.ค.' => '12'
                        ];

                        // แยกวันที่ เดือน และ ปี ออกจากสตริง
                        $parts = explode(' ', $dateStr);
                        if (count($parts) !== 3) {
                            echo "รูปแบบวันที่ไม่ถูกต้อง";
                        } else {
                            $day = $parts[0];
                            $monthThai = $parts[1];
                            $yearThai = $parts[2];

                            // ตรวจสอบว่าเดือนภาษาไทยมีอยู่ในอาเรย์ของเดือนหรือไม่
                            if (isset($thaiMonths[$monthThai])) {
                                $month = $thaiMonths[$monthThai];

                                // แปลงปีจาก พ.ศ. เป็น ค.ศ.
                                $year = $yearThai - 543;

                                // สร้างรูปแบบวันที่ใหม่ในรูปแบบสากล (YYYY-MM-DD)
                                $formattedDate = "$year-$month-$day";

                                // สร้าง DateTime object จากวันที่ที่ถูกแปลงแล้ว
                                $date = DateTime::createFromFormat('Y-m-d', $formattedDate);

                                // ตรวจสอบว่าการแปลงวันที่สำเร็จ
                                if ($date !== false) {
                                    // วันที่ปัจจุบัน
                                    $currentDate = new DateTime();

                                    // คำนวณความต่างระหว่างวันที่
                                    $interval = $currentDate->diff($date);

                                    // ตรวจสอบว่าความต่างของวันไม่เกิน 7 วัน
                                    if ($interval->days <= 7) {
                                        // แสดง New tag
                                        echo '<div class="bt-new-dla"><span class="text-new-dla">new</span></div>';
                                    }
                                } else {
                                    echo "การแปลงวันที่ไม่สำเร็จ";
                                }
                            } else {
                                echo "เดือนที่ระบุไม่ถูกต้อง";
                            }
                        }
                        ?>
                    </div>
                    <div class="col-9 font-pages-content ">
                        <?php if (!empty($rs['doc_no'])): ?>
                            <span class="font-dla-1"><?php echo $rs['doc_no']; ?></span>&nbsp;&nbsp;<span class=""><?= $topic; ?></span><br>
                        <?php else: ?>
                            <span class=""><?= $topic; ?></span><br>
                        <?php endif; ?>
                        <?php
                        // ตรวจสอบว่ามี url เฉยๆ หรือไม่
                        if (!empty($rs['url']) && strpos($rs['url'], 'N/A') === false && $rs['url'] !== NULL) {
                            // ถ้ามี url เฉยๆ และไม่มีคำว่า N/A แสดงผลเอกสารแนบที่ 1
                            echo '<a class="underline-hover" href="' . htmlspecialchars($rs['url'], ENT_QUOTES, 'UTF-8') . '" target="_blank">เอกสารแนบที่ 1</a><br>';
                        } else {
                            // ลูปแสดงผล URL โดยตรวจสอบทั้ง url1, url2 และ url01, url02 แบบรวมกัน
                            $documentCount = 0;
                            for ($j = 1; $j <= 20; $j++) {
                                $found = false;

                                // ตรวจสอบ url1, url2, ... ก่อน
                                $urlKey1 = 'url' . $j;
                                if (isset($rs[$urlKey1]) && !empty($rs[$urlKey1]) && strpos($rs[$urlKey1], 'N/A') === false && $rs[$urlKey1] !== NULL) {
                                    $documentCount++;
                                    echo '<a href="' . htmlspecialchars($rs[$urlKey1], ENT_QUOTES, 'UTF-8') . '" target="_blank" class="underline-hover">เอกสารแนบที่ ' . $documentCount . '</a><br>';
                                    $found = true;
                                }

                                // ถ้าไม่เจอ url1, url2 ให้ตรวจสอบ url01, url02
                                if (!$found) {
                                    $urlKey2 = 'url' . sprintf('%02d', $j); // url01, url02, etc.
                                    if (isset($rs[$urlKey2]) && !empty($rs[$urlKey2]) && strpos($rs[$urlKey2], 'N/A') === false && $rs[$urlKey2] !== NULL) {
                                        $documentCount++;
                                        echo '<a href="' . htmlspecialchars($rs[$urlKey2], ENT_QUOTES, 'UTF-8') . '" target="_blank" class="underline-hover">เอกสารแนบที่ ' . $documentCount . '</a><br>';
                                    }
                                }
                            }
                        }
                        ?>
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