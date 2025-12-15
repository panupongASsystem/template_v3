<div class="text-center pages-head">
    <span class="font-pages-head">สถานที่สำคัญ-ท่องเที่ยว</span>
</div>
</div>
<img src="<?php echo base_url('docs/welcome-btm-light-other.png'); ?>">

<div class="bg-pages ">
    <div class="container-pages-detail">
        <div class="text-center">
            <img class="border-radius-travel"
                src="<?php echo base_url('docs/img/' . $rsTravel->travel_img); ?>"
                style="width: 100%; max-width: 1035px; height: auto; max-height: 600px; object-fit: contain;">
            <?php if (!empty($rsTravel->travel_youtube)) : ?>
                <div class="d-flex justify-content-end" style="margin-top: -95px; padding-right: 250px; ">
                    <a href="<?= $rsTravel->travel_youtube; ?>" target="_blank">
                        <svg style="color: red;" xmlns="http://www.w3.org/2000/svg" width="100" height="100" fill="currentColor" class="bi bi-youtube" viewBox="0 0 16 16">
                            <path d="M8.051 1.999h.089c.822.003 4.987.033 6.11.335a2.01 2.01 0 0 1 1.415 1.42c.101.38.172.883.22 1.402l.01.104.022.26.008.104c.065.914.073 1.77.074 1.957v.075c-.001.194-.01 1.108-.082 2.06l-.008.105-.009.104c-.05.572-.124 1.14-.235 1.558a2.007 2.007 0 0 1-1.415 1.42c-1.16.312-5.569.334-6.18.335h-.142c-.309 0-1.587-.006-2.927-.052l-.17-.006-.087-.004-.171-.007-.171-.007c-1.11-.049-2.167-.128-2.654-.26a2.007 2.007 0 0 1-1.415-1.419c-.111-.417-.185-.986-.235-1.558L.09 9.82l-.008-.104A31.4 31.4 0 0 1 0 7.68v-.123c.002-.215.01-.958.064-1.778l.007-.103.003-.052.008-.104.022-.26.01-.104c.048-.519.119-1.023.22-1.402a2.007 2.007 0 0 1 1.415-1.42c.487-.13 1.544-.21 2.654-.26l.17-.007.172-.006.086-.003.171-.007A99.788 99.788 0 0 1 7.858 2h.193zM6.4 5.209v4.818l4.157-2.408z" />
                        </svg>
                    </a>
                </div>
            <?php endif; ?>
        </div>
        <div class="page-content-travel"><br><br>
            <span class="font-head-travel"><?= $rsTravel->travel_name; ?></span>
            <div class="border-gray-332"></div>
            <span class="font-page-detail-time-img"><svg xmlns="http://www.w3.org/2000/svg" width="13" height="14" fill="currentColor" class="bi bi-calendar-minus-fill" viewBox="0 0 16 16">
                    <path d="M4 .5a.5.5 0 0 0-1 0V1H2a2 2 0 0 0-2 2v1h16V3a2 2 0 0 0-2-2h-1V.5a.5.5 0 0 0-1 0V1H4V.5zM16 14V5H0v9a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2zM6 10h4a.5.5 0 0 1 0 1H6a.5.5 0 0 1 0-1z" />
                </svg>
                <?php
                // ในการใช้งาน setThaiMonth
                $date = new DateTime($rsTravel->travel_date);
                $day_th = $date->format('d');
                $month_th = setThaiMonth($date->format('F')); // เรียกใช้ setThaiMonth สำหรับชื่อเดือน
                $year_th = $date->format('Y') + 543; // เพิ่มขึ้น 543 ปี
                $formattedDate = "$day_th $month_th $year_th"; // วันที่และเดือนเป็นภาษาไทย
                echo $formattedDate;
                ?>
            </span>
            <span class="font-page-detail-time-img">
                <svg xmlns="http://www.w3.org/2000/svg" width="13" height="14" fill="currentColor" class="bi bi-clock-fill" viewBox="0 0 16 16">
                    <path d="M16 8A8 8 0 1 1 0 8a8 8 0 0 1 16 0zM8 3.5a.5.5 0 0 0-1 0V9a.5.5 0 0 0 .252.434l3.5 2a.5.5 0 0 0 .496-.868L8 8.71V3.5z" />
                </svg>
                <?php
                $date = new DateTime($rsTravel->travel_date);
                $formattedTime = $date->format('H:i'); // เวลา
                echo $formattedTime;
                ?>
                น.</span>
            <br><br>
            <span class="font-page-detail-content-img break-word"><?= $rsTravel->travel_detail; ?></span>
            <br>
            <span class="font-page-detail-content-img">ตำแหน่งที่ตั้ง</span> : <span class="font-page-detail-content-img underline"><?= $rsTravel->travel_location; ?> <a class="btn btn-success" target="_blank" href="<?= $rsTravel->travel_map; ?>">Google
                    Map</a></span>
            <br>
            <span class="font-page-detail-content-img">เวลาทำการ</span> : <span class="font-page-detail-content-img"><?= $rsTravel->travel_day; ?>&nbsp;<?= $rsTravel->travel_timeopen; ?>
                - <?= $rsTravel->travel_timeclose; ?> น.</span>
            <br>
            <span class="font-page-detail-content-img">โทรศัพท์</span> : <span class="font-page-detail-content-img"><?= $rsTravel->travel_phone; ?></span>
            <br>
            <span class="font-page-detail-content-img">แหล่งที่มา</span> : <span class="font-page-detail-content-img"><a href="<?= $rsTravel->travel_refer; ?>" target="_blank"><?= $rsTravel->travel_refer; ?></a></span>
            <div class="row row-cols-4 mt-5 g-4">
                <?php foreach ($rsImg as $img) { ?>
                    <div class="col mb-3">
                        <a href="<?php echo base_url('docs/img/' . $img->travel_img_img); ?>" data-fancybox="gallery">
                            <img class="rounded-all" src="<?php echo base_url('docs/img/' . $img->travel_img_img); ?>" style="max-width: 258px; width: 100%; height: auto; max-height: 146px; object-fit: contain;">
                        </a>
                    </div>
                <?php } ?>
            </div>
        </div>
        <svg xmlns="http://www.w3.org/2000/svg" style="color: black; margin-top: -2px;" width="24" height="15" fill="currentColor" class="bi bi-eye" viewBox="0 0 16 16">
            <path d="M16 8s-3-5.5-8-5.5S0 8 0 8s3 5.5 8 5.5S16 8 16 8M1.173 8a13 13 0 0 1 1.66-2.043C4.12 4.668 5.88 3.5 8 3.5s3.879 1.168 5.168 2.457A13 13 0 0 1 14.828 8q-.086.13-.195.288c-.335.48-.83 1.12-1.465 1.755C11.879 11.332 10.119 12.5 8 12.5s-3.879-1.168-5.168-2.457A13 13 0 0 1 1.172 8z" />
            <path d="M8 5.5a2.5 2.5 0 1 0 0 5 2.5 2.5 0 0 0 0-5M4.5 8a3.5 3.5 0 1 1 7 0 3.5 3.5 0 0 1-7 0" />
        </svg>
        <span class="font-page-detail-view-img">จำนวนผู้เข้าชม : <?= $rsTravel->travel_view; ?></span>
    </div><br><br>
</div>