<div class="text-center pages-head">
    <span class="font-pages-head">ข่าวสาร / กิจกรรม</span>
</div>
</div>
<img src="<?php echo base_url('docs/welcome-btm-light-other.png'); ?>">

<div class="bg-pages ">
    <div class="container-pages-detail">
        <div class="text-center">
            <img src="<?php echo base_url('docs/img/' . $rsActivity->activity_img); ?>"
                style="max-width: 1035px; width: 100%; height: auto; max-height: 600px; object-fit: contain;">
        </div>
        <div class="page-content-travel mt-4">
            <span class="font-page-detail-head"><?= $rsActivity->activity_name; ?></span>
            <div class="border-gray-332"></div>
            <span class="font-page-detail-time-img"><svg style="margin-top: -3px;" xmlns="http://www.w3.org/2000/svg" width="21" height="21" fill="currentColor" class="bi bi-calendar-minus-fill" viewBox="0 0 16 16">
                    <path d="M4 .5a.5.5 0 0 0-1 0V1H2a2 2 0 0 0-2 2v1h16V3a2 2 0 0 0-2-2h-1V.5a.5.5 0 0 0-1 0V1H4V.5zM16 14V5H0v9a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2zM6 10h4a.5.5 0 0 1 0 1H6a.5.5 0 0 1 0-1z" />
                </svg>
                <?php
                // ในการใช้งาน setThaiMonth
                $date = new DateTime($rsActivity->activity_date);
                $day_th = $date->format('d');
                $month_th = setThaiMonth($date->format('F')); // เรียกใช้ setThaiMonth สำหรับชื่อเดือน
                $year_th = $date->format('Y') + 543; // เพิ่มขึ้น 543 ปี
                $formattedDate = "$day_th $month_th $year_th"; // วันที่และเดือนเป็นภาษาไทย
                echo $formattedDate;
                ?>
            </span>
            &nbsp;
            <span class="font-page-detail-time-img">
                <svg xmlns="http://www.w3.org/2000/svg" style="margin-top: -3px;" width="20" height="20" fill="currentColor" class="bi bi-clock-fill" viewBox="0 0 16 16">
                    <path d="M16 8A8 8 0 1 1 0 8a8 8 0 0 1 16 0zM8 3.5a.5.5 0 0 0-1 0V9a.5.5 0 0 0 .252.434l3.5 2a.5.5 0 0 0 .496-.868L8 8.71V3.5z" />
                </svg>
                <?php
                $date = new DateTime($rsActivity->activity_date);
                $formattedTime = $date->format('H:i'); // เวลา
                echo $formattedTime;
                ?>
                น.</span>
            <br><br>
            <span class="font-page-detail-content-img"><?= $rsActivity->activity_detail; ?></span>
            <div class="row row-cols-4 mt-4">
                <?php foreach ($rsImg as $img) { ?>
                    <div class="col mb-3">
                        <a href="<?php echo base_url('docs/img/' . $img->activity_img_img); ?>"
                            data-fancybox="gallery">
                            <img class="rounded-all"
                                src="<?php echo base_url('docs/img/' . $img->activity_img_img); ?>"
                                style="max-width: 258px; width: 100%; height: auto; max-height: 146px; object-fit: contain;">
                        </a>
                    </div>
                <?php } ?>
            </div>
        </div>
        <div>
            <svg xmlns="http://www.w3.org/2000/svg" style="color: black; margin-top: -2px;" width="24" height="15" fill="currentColor" class="bi bi-eye" viewBox="0 0 16 16">
                <path d="M16 8s-3-5.5-8-5.5S0 8 0 8s3 5.5 8 5.5S16 8 16 8M1.173 8a13 13 0 0 1 1.66-2.043C4.12 4.668 5.88 3.5 8 3.5s3.879 1.168 5.168 2.457A13 13 0 0 1 14.828 8q-.086.13-.195.288c-.335.48-.83 1.12-1.465 1.755C11.879 11.332 10.119 12.5 8 12.5s-3.879-1.168-5.168-2.457A13 13 0 0 1 1.172 8z" />
                <path d="M8 5.5a2.5 2.5 0 1 0 0 5 2.5 2.5 0 0 0 0-5M4.5 8a3.5 3.5 0 1 1 7 0 3.5 3.5 0 0 1-7 0" />
            </svg><span class="font-page-detail-view-img">จำนวนผู้เข้าชม : <?= $rsActivity->activity_view; ?></span>
        </div>
    </div>
</div><br><br><br>