<div class="text-center pages-head">
    <span class="font-pages-head">หนังสือราชการ สถ.</span>
</div>
</div>
<img src="<?php echo base_url('docs/welcome-btm-light-other.png'); ?>">

<div class="bg-pages ">
    <div class="container-pages-news">
        <div class="bg-doc-off-all">
            <div class="news-dla-prov">
                <?php if (!empty($rssData)) : ?>
                    <?php foreach (array_slice($rssData, 0, 25) as $index => $document) : ?>
                        <div class="row mt-2 underline" style="padding-left: 60px;">
                            <div class="col-2">
                                <span class="font-dla-1 line-ellipsis-dla1"><img src="<?php echo base_url("docs/icon-news.png"); ?>">&nbsp;&nbsp; <?php echo $document['doc_number']; ?> </span>
                            </div>
                            <div class="col-6">
                                <span class="font-dla-2 line-ellipsis-dla2"><?php echo $document['topic']; ?></span>
                            </div>
                            <div class="col-1">
                                <?php
                                // สมมติว่าค่าที่ได้รับมาจากตัวแปร $document['date'] อยู่ในรูปแบบ "10/06/2567"
                                $dateStr = $document['date'];

                                // แยกวันที่ เดือน และปีออกจากกัน
                                list($day, $month, $year) = explode('/', $dateStr);

                                // แปลงปีจาก พ.ศ. เป็น ค.ศ.
                                $year = $year - 543;

                                // สร้างรูปแบบวันที่ใหม่ในรูปแบบสากล (YYYY-MM-DD)
                                $formattedDate = "$year-$month-$day";

                                // สร้าง DateTime object จากวันที่ที่ถูกแปลงแล้ว
                                $date = DateTime::createFromFormat('Y-m-d', $formattedDate);

                                // ตรวจสอบว่าการแปลงวันที่สำเร็จและคำนวณความต่างของวัน
                                if ($date !== false) {
                                    // วันที่ปัจจุบัน
                                    $currentDate = new DateTime();

                                    // คำนวณความต่างระหว่างวันที่
                                    $interval = $currentDate->diff($date);

                                    // ตรวจสอบว่าความต่างของวันไม่เกิน 7 วัน
                                    if ($interval->days <= 7) {
                                        // ถ้าห่างไม่เกิน 7 วัน (ทั้งก่อนและหลังวันที่ปัจจุบัน)
                                        echo '<div class="bt-new-dla"><span class="text-new-dla">new</span></div>';
                                    }
                                }
                                ?>
                            </div>
                            <div class="col-3">
                                <?php
                                // $document['date'] คือตัวแปรที่เก็บวันที่

                                // แปลงวันที่เป็น object DateTime ด้วยรูปแบบที่ถูกต้อง
                                $date = DateTime::createFromFormat('d/m/Y', $document['date']);

                                // ดึงวันที่
                                $thaiDay = $date->format('d');

                                // ดึงเดือนเป็นตัวย่อไทย
                                $thaiMonths = [
                                    'January' => 'มกราคม',
                                    'February' => 'กุมภาพันธ์',
                                    'March' => 'มีนาคม',
                                    'April' => 'เมษายน',
                                    'May' => 'พฤษภาคม',
                                    'June' => 'มิถุนายน',
                                    'July' => 'กรกฎาคม',
                                    'August' => 'สิงหาคม',
                                    'September' => 'กันยายน',
                                    'October' => 'ตุลาคม',
                                    'November' => 'พฤศจิกายน',
                                    'December' => 'ธันวาคม',
                                ];
                                $thaiMonth = $thaiMonths[$date->format('F')];

                                // ดึงเฉพาะปี
                                $year = $date->format('Y');

                                // แสดงผลลัพธ์
                                ?>
                                <span class="font-all-dla" style="padding-left: 20px; color: #000;">
                                    <?php echo $thaiDay; ?>
                                    <?php echo $thaiMonth; ?>
                                    <?php echo $year; ?>
                                </span>
                                <?php ?>
                            </div>
                        </div>
                    <?php endforeach; ?>
                <?php endif; ?>
            </div>
        </div>
        <div class="d-flex justify-content-end" style="margin-top: 30px; color: #707070; font-size: 13px;">
            <span>หมายเหตุ อ้างอิงแหล่งที่มาจาก กรมส่งเสริมการปกครองส่วนท้องถิ่น
                <a href="https://www.dla.go.th/" target="_blank">
                    <span style="color: #707070;">(https://www.dla.go.th/)</span>
                </a>
            </span>
        </div>
        <div class="d-flex justify-content-end underline text-center" style="margin-top: 2%;">
            <a href="https://www.dla.go.th/servlet/DocumentServlet" target="_blank">
                <div class="button-activity-all">
                    <span class="font-all-home">ดูทั้งหมด</span>
                </div>
            </a>
        </div>
    </div>
</div><br><br><br>