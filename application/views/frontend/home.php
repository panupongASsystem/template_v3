<!-- ห้ามเอาออกเด้อ announcements -->
<script
    src="https://assystem.co.th/backend/announcements/script/<?php echo get_config_value('domain'); ?>.go.th"></script>

<?php foreach ($qPublicize_ita as $index => $rs) { ?>
    <div class="ita-popup-backdrop" id="itaPopupModal_<?php echo $index; ?>" data-popup-id="<?php echo $index; ?>">
        <div class="ita-popup-container">
            <button class="ita-popup-close-btn" aria-label="ปิด">
                <i class="fa-regular fa-circle-xmark"></i>
            </button>
            <a href="<?php echo $rs->publicize_ita_link; ?>" target="_blank" class="ita-popup-link">
                <img src="<?php echo base_url('docs/img/' . $rs->publicize_ita_img); ?>"
                    alt="ITA Image <?php echo $index + 1; ?>" class="ita-popup-image" loading="lazy">
                <div class="ita-hover-overlay">
                    <span class="ita-hover-text">คลิกเพื่อดูข้อมูลเพิ่มเติม</span>
                </div>
            </a>
            <div class="ita-navigation">
                <div class="ita-dots"></div>
            </div>
        </div>
    </div>
<?php } ?>

<!-- กล่อง popup สำหรับแสดงรายละเอียดกิจกรรม -->
<!-- ต่อท้าย body ก่อนปิด tag -->
<div id="calendarPopup" class="calendar-popup-container">
    <div class="popup-header">
        <h3 class="popup-title">รายละเอียดกิจกรรม</h3>
        <button class="popup-close">&times;</button>
    </div>
    <div class="popup-content" id="popupContent">
        <!-- เนื้อหาจะถูกเพิ่มโดย JavaScript -->
    </div>
</div>

<div class="welcome-container">
    <div class="welcome" id="welcome"></div>
    <div class="fade-container">
        <div class="fade-content active" id="div1">
            <div class="wel-g1-sky">
                <img class="cloud-animation cloud-animation-5" src="<?php echo base_url('docs/cloud-header-2.png'); ?>">
                <img class="cloud-animation cloud-animation-6" src="<?php echo base_url('docs/cloud-header-1.png'); ?>">

                <img class="rotate-animation360 local-sun" src="<?php echo base_url('docs/sun-animation1.png'); ?>">

                <div class="animation-text-orbortor-header">
                    <img src="<?php echo base_url("docs/text-orbortor-header.png"); ?>">
                </div>
            </div>
            <div class="wel-g1-bg">
                <img class="animation-wind-B animation-wind-1"
                    src="<?php echo base_url('docs/lotus-L-1-animation1.png'); ?>">
                <img class="animation-wind-B animation-wind-2"
                    src="<?php echo base_url('docs/lotus-R-1-animation1.png'); ?>">
                <img class="animation-wind-B animation-wind-3"
                    src="<?php echo base_url('docs/lotus-R-2-animation1.png'); ?>">
                <img class="animation-wind-B animation-wind-4"
                    src="<?php echo base_url('docs/lotus-R-3-animation1.png'); ?>">

                <div class="water-wrap">
                    <img class="water-image" src="<?php echo base_url("docs/water-animation1.png"); ?>"
                        alt="Water Banner">
                    <img class="water-image" src="<?php echo base_url("docs/water-animation1.png"); ?>"
                        alt="Water Banner">
                </div>
            </div>
            <div class="wel-g1-bg2">
                <div class="butterfly-body4">
                    <img class="animation-wind-butterfly-body"
                        src="<?php echo base_url('docs/butterfly-body-L.png'); ?>">
                    <img class="animation-wind-butterfly-R animation-wind-butterfly-1"
                        src="<?php echo base_url('docs/butterfly-wing-L-1.png'); ?>">
                    <img class="animation-wind-butterfly-R animation-wind-butterfly-2"
                        src="<?php echo base_url('docs/butterfly-wing-L-2.png'); ?>">
                </div>
            </div>
        </div>

        <div class="fade-content" id="div2">
            <div class="wel-g2-sky">
                <img class="cloud-animation cloud-animation-5" src="<?php echo base_url('docs/cloud-header-2.png'); ?>">
                <img class="cloud-animation cloud-animation-6" src="<?php echo base_url('docs/cloud-header-1.png'); ?>">

                <img class="animation-wind-L animation-wind-5"
                    src="<?php echo base_url('docs/flower-L-animation2.png'); ?>">
                <img class="animation-wind-R animation-wind-6"
                    src="<?php echo base_url('docs/flower-R-animation2.png'); ?>">

                <div style="position: absolute; z-index: 2; top: 307px; left: 953px;">
                    <img src="<?php echo base_url("docs/bird.gif"); ?>">
                </div>
            </div>
            <div class="wel-g2-bg"></div>
            <div class="wel-g2-bg-Frame-green">
                <div class="fadeInhead">
                    <img src="<?php echo base_url("docs/text-animation2.png"); ?>">
                </div>
                <div class="fadeInhead2 bg-line">
                    <div class="fadeInhead3">“ เศรษฐกิจก้าวหน้า การศึกษาดี มีคุณธรรม นำเทคโนโลยี มีประชาธิปไตย “</div>
                    <div class="fadeInhead4">วิสัยทัศน์เทศบาลตำบลลาดขวาง</div>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="welcome-btm">
    <?php for ($i = 1; $i <= 6; $i++): ?>
        <img class="wipwap dot-news-animation-<?php echo $i; ?>" src="<?php echo base_url('docs/light-1.png'); ?>">
    <?php endfor; ?>
    <?php for ($i = 7; $i <= 11; $i++): ?>
        <img class="wipwap dot-news-animation-<?php echo $i; ?>" src="<?php echo base_url('docs/light-2.png'); ?>">
    <?php endfor; ?>
    <div class="text-center" style="margin-top: 870px;">
        <span class="font-welcome-btm-other1"><?php echo get_config_value('fname'); ?></span><br>
        <div style="margin-top: -20px;">
            <span class="font-welcome-btm-other2">อ.<?php echo get_config_value('district'); ?>
                จ.<?php echo get_config_value('province'); ?></span>
        </div>
    </div>
</div>

<div class="position-3bg">

    <div class="bg-banner">
        <img class="cloud-animation cloud-cartoon-animation-5" src="<?php echo base_url('docs/cloud.png'); ?>">

        <div class="bg-executives">
            <!-- Left side - Show ID 1 -->
            <div class="position-relative-left">
                <?php foreach ($qBackground_personnel as $index => $rs) {
                    if ($rs->background_personnel_id == 1) { ?>
                        <div class="text-center">
                            <div class="bg-ex-img">
                                <?php
                                $images = [];
                                if (!empty($rs->background_personnel_img1))
                                    $images[] = $rs->background_personnel_img1;
                                if (!empty($rs->background_personnel_img2))
                                    $images[] = $rs->background_personnel_img2;
                                if (!empty($rs->background_personnel_img3))
                                    $images[] = $rs->background_personnel_img3;

                                foreach ($images as $imgIndex => $img) { ?>
                                    <img src="docs/img/<?= $img; ?>" class="fade-image <?= $imgIndex === 0 ? 'active' : ''; ?>"
                                        data-personnel-id="1" alt="Personnel Image">
                                <?php } ?>
                            </div>
                            <div class="bg-text-name">
                                <div class="font-link-name"><?= $rs->background_personnel_name; ?></div>
                                <div class="font-link-rank"><?= $rs->background_personnel_rank; ?></div>
                            </div>
                            <div class="bg-text-phone-number">
                                <span class="font-link-phone">สายด่วน โทร : <?= $rs->background_personnel_phone; ?></span>
                            </div>
                        </div>
                    <?php }
                } ?>
            </div>

            <!-- Right side - Show ID 2 -->
            <div class="position-relative-right">
                <?php foreach ($qBackground_personnel as $index => $rs) {
                    if ($rs->background_personnel_id == 2) { ?>
                        <div class="text-center">
                            <div class="bg-ex-img">
                                <?php
                                $images = [];
                                if (!empty($rs->background_personnel_img1))
                                    $images[] = $rs->background_personnel_img1;
                                if (!empty($rs->background_personnel_img2))
                                    $images[] = $rs->background_personnel_img2;
                                if (!empty($rs->background_personnel_img3))
                                    $images[] = $rs->background_personnel_img3;

                                foreach ($images as $imgIndex => $img) { ?>
                                    <img src="docs/img/<?= $img; ?>" class="fade-image <?= $imgIndex === 0 ? 'active' : ''; ?>"
                                        data-personnel-id="2" alt="Personnel Image">
                                <?php } ?>
                            </div>
                            <div class="bg-text-name">
                                <div class="font-link-name"><?= $rs->background_personnel_name; ?></div>
                                <div class="font-link-rank"><?= $rs->background_personnel_rank; ?></div>
                            </div>
                            <div class="bg-text-phone-number">
                                <span class="font-link-phone">สายด่วน โทร : <?= $rs->background_personnel_phone; ?></span>
                            </div>
                        </div>
                    <?php }
                } ?>
            </div>
        </div>

        <script>
            function startImageFade() {
                // หา personnel ทั้งหมดที่มีรูปภาพ
                const personnelIds = [1, 2];

                personnelIds.forEach(personnelId => {
                    const images = document.querySelectorAll(`img[data-personnel-id="${personnelId}"]`);

                    if (images.length > 1) {
                        let currentIndex = 0;

                        setInterval(() => {
                            // ซ่อนรูปปัจจุบัน
                            images[currentIndex].classList.remove('active');

                            // ไปรูปต่อไป
                            currentIndex = (currentIndex + 1) % images.length;

                            // แสดงรูปใหม่
                            images[currentIndex].classList.add('active');

                        }, 6000); // เปลี่ยนทุก 3 วินาที
                    }
                });
            }

            // เริ่มต้น fade effect เมื่อหน้าเว็บโหลดเสร็จ
            document.addEventListener('DOMContentLoaded', function () {
                startImageFade();
            });
        </script>
        <div class="crop">
            <div class="d-flex justify-content-center">
                <div class="welcome-btm-text-run">
                    <div class="row">
                        <div class="col-2" style="padding-top: 10px; padding-left: 20px;">
                            <span class="font-left-text-run">ประกาศ</span>
                        </div>
                        <div class="col-10 position-relative">
                            <div class="tab-container">
                            <?php
                            $news = $this->HotNews_model->hotnews_frontend();
                            echo '<marquee direction="left" scrollamount="5" class="text-run-style">';
                            foreach ($news as $item) {
                                echo $item->hotNews_text . ' &nbsp;&nbsp;&nbsp; ';
                            }
                            echo '</marquee>';
                            ?>
                        </div>
                        </div>
                    </div>
                </div>
            </div>
			
            <div class="weather-container">
                <div class="weather-col-left">
                    <div class="crop-weather">
                        <div class="weather-icon">
                            <img src="docs/icon-weather.png" alt="Weather">
                        </div>
                        <div class="font-text-run">
                            <?php foreach ($qWeather as $weather) { ?>
                                <marquee direction="left">
                                    <?= $weather->title; ?> <?= $weather->description; ?>
                                </marquee>
                            <?php } ?>
                        </div>
                    </div>
                </div>

                <div class="weather-col-center">
                    <div class="gcse-search"></div>
                </div>

                <div class="weather-col-right">
                    <div class="translate-container">
                        <div class="custom-language-switcher">
                            <div class="lang-buttons">
                                <button class="lang-btn active" onclick="translateToThai()" data-lang="th">TH</button>
                                <button class="lang-btn" onclick="translateToEnglish()" data-lang="en">EN</button>
                            </div>
                            <div id="google_translate_element" style="display:none;"></div>
                        </div>
                    </div>
                </div>
            </div>
			
            <div id="carouselExampleAutoplaying" class="carousel slide" data-bs-ride="carousel" style="z-index: 10; ">
                <div class="carousel-inner">
                    <?php foreach ($qBanner as $index => $img_banner) { ?>
                        <div class="carousel-item <?= ($index === 0) ? "active" : ""; ?>" data-bs-interval="5000"
                            style="--bg-image: url('docs/img/<?= $img_banner->banner_img; ?>');">
                            <a href="<?= $img_banner->banner_link; ?>" target="_blank">
                                <img src="docs/img/<?= $img_banner->banner_img; ?>" class="d-block">
                            </a>
                        </div>
                    <?php } ?>
                </div>
                <div class="carousel-indicators" style="bottom: -20px; height: 1px; width: 1px; margin-left: 0px;">
                    <?php foreach ($qBanner as $index => $img_banner) {
                        $active = ($index === 0) ? "active" : "";
                        ?>
                        <button type="button" data-bs-target="#carouselExampleAutoplaying" data-bs-slide-to="<?= $index; ?>"
                            class="<?= $active; ?>" aria-current="true" aria-label="Slide <?= ($index + 1); ?>">
                            <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor"
                                class="bi bi-diamond-fill" viewBox="0 0 16 16">
                                <path fill-rule="evenodd"
                                    d="M6.95.435c.58-.58 1.52-.58 2.1 0l6.515 6.516c.58.58.58 1.519 0 2.098L9.05 15.565c-.58.58-1.519.58-2.098 0L.435 9.05a1.48 1.48 0 0 1 0-2.098z" />
                            </svg>
                        </button>
                    <?php } ?>
                </div>
            </div>

            <div class="bg-calender">
                <div class="d-flex justify-content-center" style="padding-top: 15px; padding-right: 0px;">
                    <span class="font-banner-link">ปฏิทินกิจกรรม</span>
                </div>
                <div class="row" style="z-index: 10; cursor: pointer; margin-top: 0px;">
                    <div class="col-6">
                        <div class="calendar">
                            <div class="calendar-header">
                                <a id="prevMonth" class="prev-month-button"></a>
                                <h3 class="calendar-month-center text-center" id="monthYear"><?= date('F Y'); ?></h3>
                                <a id="nextMonth" class="next-month-button"></a>
                            </div>
                            <div class="weekdays">
                                <div class="weekday" style="color: #820000">S</div>
                                <div class="weekday" style="color: #1C455F">M</div>
                                <div class="weekday" style="color: #1C455F">T</div>
                                <div class="weekday" style="color: #1C455F">W</div>
                                <div class="weekday" style="color: #1C455F">T</div>
                                <div class="weekday" style="color: #1C455F">F</div>
                                <div class="weekday" style="color: #1C455F">S</div>
                            </div>
                            <div class="days-container">
                                <div class="days" id="days">
                                    <?php
                                    $currentDate = new DateTime();
                                    $currentMonth = $currentDate->format('m');
                                    $currentYear = $currentDate->format('Y');
                                    $daysInMonth = cal_days_in_month(CAL_GREGORIAN, $currentMonth, $currentYear);

                                    for ($i = 1; $i <= $daysInMonth; $i++) {
                                        $day = str_pad($i, 2, '0', STR_PAD_LEFT);
                                        $dateString = "$currentYear-$currentMonth-$day";

                                        $isToday = $currentDate->format('Y-m-d') === $dateString;
                                        $dayClass = $isToday ? 'day current-day' : 'day';

                                        $hasEvent = false;
                                        foreach ($events as $event) {
                                            $eventStartDate = new DateTime($event['calender_date']);
                                            $eventEndDate = new DateTime($event['calender_date_end']);
                                            if ($dateString >= $eventStartDate->format('Y-m-d') && $dateString <= $eventEndDate->format('Y-m-d')) {
                                                $hasEvent = true;
                                                break;
                                            }
                                        }

                                        $eventDot = $hasEvent ? '<span class="event-dot"></span>' : '';
                                        echo "<div class=\"$dayClass\" data-date=\"$dateString\"><span>$i</span>$eventDot</div>";
                                    }
                                    ?>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-6" style="margin-left: 0px; margin-top: 30px; padding: 0px 0px 0px 50px;">
                        <div class="calender-detail-head">
                            <span class="font-calender">รายละเอียดกิจกรรม</span>
                        </div>
                        <div class="calender-detail-content mt-2">
                            <div id="qCalender">
                                <!-- ข้อมูลกิจกรรมจะแสดงที่นี่เมื่อคลิกวันที่ -->
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="bg-button">
        <img class="cloud-animation cloud-cartoon-animation-6" src="<?php echo base_url('docs/cloud.png'); ?>">

        <div class="butterfly-body2">
            <img class="animation-wind-butterfly-body" src="<?php echo base_url('docs/butterfly-body-R.png'); ?>">
            <img class="animation-wind-butterfly-L animation-wind-butterfly-3"
                src="<?php echo base_url('docs/butterfly-wing-R-1.png'); ?>">
            <img class="animation-wind-butterfly-L animation-wind-butterfly-4"
                src="<?php echo base_url('docs/butterfly-wing-R-2.png'); ?>">
        </div>

        <div style="position: absolute; z-index: 2; margin-top: 915px; margin-left: 1321px;">
            <img src="<?php echo base_url("docs/bird.gif"); ?>">
        </div>

        <div class="crop underline">
            <div class="button-banner-container" style="padding-top: 135px; margin-left: 0px;">
                <a href="<?php echo site_url('Pages/ita_all'); ?>">
                    <div class="button-six">
                        <div class="topic-section">
                            <span class="font-banner-button-topic">ITA</span>
                        </div>
                        <div class="detail-section">
                            <span class="font-banner-button-detail">การประเมินคุณธรรม<br>และความโปร่งใส</span>
                        </div>
                    </div>
                </a>

                <a href="<?php echo site_url('Pages/lpa'); ?>">
                    <div class="button-six">
                        <div class="topic-section">
                            <span class="font-banner-button-topic">LPA</span>
                        </div>
                        <div class="detail-section">
                            <span class="font-banner-button-detail">การประเมินประสิทธิ<br>ภาพขององค์กร</span>
                        </div>
                    </div>
                </a>

                <a href="<?php echo site_url('Pages/msg_pres'); ?>">
                    <div class="button-six">
                        <div class="topic-section">
                            <span class="font-banner-button-topic">MES</span>
                        </div>
                        <div class="detail-section">
                            <span class="font-banner-button-detail">สารจากนายก</span>
                        </div>
                    </div>
                </a>

                <a href="<?php echo site_url('Pages/menu_eservice'); ?>">
                    <div class="button-six">
                        <div class="topic-section">
                            <span class="font-banner-button-topic">eSV</span>
                        </div>
                        <div class="detail-section">
                            <span class="font-banner-button-detail">e-Service</span>
                        </div>
                    </div>
                </a>
            </div>

            <div class="button-banner-container" style="margin-top: 40px; margin-left: 0px;">
                <a href="https://itas.nacc.go.th/go/iit/<?php echo get_config_value('eit_iit'); ?>" target="_blank">
                    <div class="button-six">
                        <div class="topic-section">
                            <span class="font-banner-button-topic">IIT</span>
                        </div>
                        <div class="detail-section">
                            <span class="font-banner-button-detail">แบบวัดการรับรู้ภายใน</span>
                        </div>
                    </div>
                </a>

                <a href="https://itas.nacc.go.th/go/eit/<?php echo get_config_value('eit_iit'); ?>" target="_blank">
                    <div class="button-six">
                        <div class="topic-section">
                            <span class="font-banner-button-topic">EIT</span>
                        </div>
                        <div class="detail-section">
                            <span class="font-banner-button-detail">แบบวัดการรับรู้ภายนอก</span>
                        </div>
                    </div>
                </a>

                <a href="<?php echo site_url('Pages/questions'); ?>">
                    <div class="button-six">
                        <div class="topic-section">
                            <span class="font-banner-button-topic">FAQ</span>
                        </div>
                        <div class="detail-section">
                            <span class="font-banner-button-detail">คำถามที่พบบ่อย</span>
                        </div>
                    </div>
                </a>

                <a href="<?php echo site_url('Pages/contact'); ?>">
                    <div class="button-six">
                        <div class="topic-section">
                            <span class="font-banner-button-topic">CON</span>
                        </div>
                        <div class="detail-section">
                            <span class="font-banner-button-detail">ติดต่อสอบถาม</span>
                        </div>
                    </div>
                </a>
            </div>

            <div class="container-video">
                <div class="d-flex justify-content-center" style="margin-top: 60px;">
                    <div class="bg-header-activity d-flex align-items-center">
                        <span class="font-header-home">วิดีทัศน์</span>
                    </div>
                </div>
                <div class="container-fluid" style="margin-top: 40px;">
                    <?php if (!empty($latest_video)): ?>
                        <div class="d-flex justify-content-center video-row">
                            <?php foreach ($latest_video as $video): ?>
                                <?php if (!empty($video->video_link)): ?>
                                    <?php
                                    // Check if it's a YouTube link
                                    if (preg_match("/youtu\.be\/|youtube\.com\/watch|youtube\.com\/shorts/", $video->video_link)):
                                        if (preg_match("/youtu\.be\/([a-zA-Z0-9_-]+)/", $video->video_link, $matches)) {
                                            $video_id = $matches[1];
                                        } elseif (preg_match("/youtube\.com\/watch\?v=([a-zA-Z0-9_-]+)/", $video->video_link, $matches)) {
                                            $video_id = $matches[1];
                                        } elseif (preg_match("/youtube\.com\/shorts\/([a-zA-Z0-9_-]+)/", $video->video_link, $matches)) {
                                            $video_id = $matches[1];
                                        }
                                        if (!empty($video_id)): ?>
                                            <div class="video-wrapper">
                                                <div class="video-content">
                                                    <iframe class="video-iframe" width="320" height="182"
                                                        src="https://www.youtube-nocookie.com/embed/<?= htmlspecialchars($video_id); ?>"
                                                        title="YouTube video player" frameborder="0"
                                                        allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture; web-share"
                                                        referrerpolicy="strict-origin-when-cross-origin" allowfullscreen></iframe>
                                                </div>
                                                <div class="video-details">
                                                    <div class="crop-time-video">
                                                        <svg xmlns="http://www.w3.org/2000/svg" width="12" height="13" viewBox="0 0 12 13"
                                                            fill="none">
                                                            <path
                                                                d="M0 11.7812C0 12.4541 0.575893 13 1.28571 13H10.7143C11.4241 13 12 12.4541 12 11.7812V4.875H0V11.7812ZM1.71429 6.90625C1.71429 6.68281 1.90714 6.5 2.14286 6.5H9.85714C10.0929 6.5 10.2857 6.68281 10.2857 6.90625V8.53125C10.2857 8.75469 10.0929 8.9375 9.85714 8.9375H2.14286C1.90714 8.9375 1.71429 8.75469 1.71429 8.53125V6.90625ZM10.7143 1.625H9.42857V0.40625C9.42857 0.182812 9.23571 0 9 0H8.14286C7.90714 0 7.71429 0.182812 7.71429 0.40625V1.625H4.28571V0.40625C4.28571 0.182812 4.09286 0 3.85714 0H3C2.76429 0 2.57143 0.182812 2.57143 0.40625V1.625H1.28571C0.575893 1.625 0 2.1709 0 2.84375V4.0625H12V2.84375C12 2.1709 11.4241 1.625 10.7143 1.625Z"
                                                                fill="white" />
                                                        </svg>
                                                        <span class="span-time-home-new">
                                                            <?php
                                                            $date = new DateTime($video->video_date);
                                                            $day_th = $date->format('d');
                                                            $month_th = setThaiMonth($date->format('F'));
                                                            $year_th = ($date->format('Y') + 543) % 100; // เอาเฉพาะ 2 ตัวท้าย
                                                            $formattedDate = "$day_th $month_th $year_th";
                                                            echo $formattedDate;
                                                            ?>
                                                        </span>
                                                    </div>
                                                    <span class="video-title"><?= htmlspecialchars($video->video_name); ?>
                                                        <?php
                                                        // วันที่ของข่าว
                                                        $video_date = new DateTime($video->video_date);

                                                        // วันที่ปัจจุบัน
                                                        $current_date = new DateTime();

                                                        // คำนวณหาความต่างของวัน
                                                        $interval = $current_date->diff($video_date);
                                                        $days_difference = $interval->days;

                                                        // ถ้ามากกว่า 30 วัน ให้ซ่อนไว้
                                                        if ($days_difference > 30) {
                                                            // ไม่แสดงรูปภาพ
                                                        } else {
                                                            // แสดงรูปภาพ
                                                            echo '<img src="docs/activity-new.gif">';
                                                        }
                                                        ?>
                                                    </span>
                                                </div>
                                            </div>
                                        <?php endif; ?>
                                    <?php
                                        // Check if it's a Facebook video or reel link
                                    elseif (preg_match("/facebook\.com\/(?:watch\?v=|.*\/videos\/|reel\/)([0-9]+)/", $video->video_link, $matches)):
                                        $fb_video_id = $matches[1] ?? '';
                                        if (!empty($fb_video_id)): ?>
                                            <div class="video-wrapper">
                                                <div class="video-content">
                                                    <iframe
                                                        src="https://www.facebook.com/plugins/video.php?href=https://www.facebook.com/watch?v=<?= htmlspecialchars($fb_video_id); ?>"
                                                        width="320" height="182" style="border:none;overflow:hidden;" scrolling="no"
                                                        frameborder="0" allowTransparency="true" allow="encrypted-media"
                                                        allowfullscreen></iframe>
                                                </div>
                                                <div class="video-details">
                                                    <div class="crop-time-video">
                                                        <svg xmlns="http://www.w3.org/2000/svg" width="12" height="13" viewBox="0 0 12 13"
                                                            fill="none">
                                                            <path
                                                                d="M0 11.7812C0 12.4541 0.575893 13 1.28571 13H10.7143C11.4241 13 12 12.4541 12 11.7812V4.875H0V11.7812ZM1.71429 6.90625C1.71429 6.68281 1.90714 6.5 2.14286 6.5H9.85714C10.0929 6.5 10.2857 6.68281 10.2857 6.90625V8.53125C10.2857 8.75469 10.0929 8.9375 9.85714 8.9375H2.14286C1.90714 8.9375 1.71429 8.75469 1.71429 8.53125V6.90625ZM10.7143 1.625H9.42857V0.40625C9.42857 0.182812 9.23571 0 9 0H8.14286C7.90714 0 7.71429 0.182812 7.71429 0.40625V1.625H4.28571V0.40625C4.28571 0.182812 4.09286 0 3.85714 0H3C2.76429 0 2.57143 0.182812 2.57143 0.40625V1.625H1.28571C0.575893 1.625 0 2.1709 0 2.84375V4.0625H12V2.84375C12 2.1709 11.4241 1.625 10.7143 1.625Z"
                                                                fill="white" />
                                                        </svg>
                                                        <span class="span-time-home-new">
                                                            <?php
                                                            $date = new DateTime($video->video_date);
                                                            $day_th = $date->format('d');
                                                            $month_th = setThaiMonth($date->format('F'));
                                                            $year_th = ($date->format('Y') + 543) % 100;
                                                            $formattedDate = "$day_th $month_th $year_th";
                                                            echo $formattedDate;
                                                            ?>
                                                        </span>
                                                    </div>
                                                    <span class="video-title"><?= htmlspecialchars($video->video_name); ?>
                                                        <?php
                                                        $video_date = new DateTime($video->video_date);
                                                        $current_date = new DateTime();
                                                        $interval = $current_date->diff($video_date);
                                                        $days_difference = $interval->days;

                                                        if ($days_difference > 30) {
                                                            // ไม่แสดงรูปภาพ
                                                        } else {
                                                            echo '<img src="docs/activity-new.gif">';
                                                        }
                                                        ?>
                                                    </span>
                                                </div>
                                            </div>
                                        <?php endif; ?>
                                    <?php endif; ?>
                                <?php endif; ?>
                            <?php endforeach; ?>
                        </div>
                    <?php endif; ?>
                </div>

                <div class="d-flex justify-content-center underline">
                    <a href="<?php echo site_url('pages/video'); ?>">
                        <div class="button-activity-all text-center">
                            <span class="font-all-home">ดูทั้งหมด</span>
                        </div>
                    </a>
                </div>
            </div>
        </div>
    </div>

    <div class="bg-activity" id="activity">
        <div class="water-activity"></div>

        <img class="animation-wind-L animation-wind-8" src="<?php echo base_url('docs/lotus-button-1.png'); ?>">
        <img class="animation-wind-L animation-wind-9" src="<?php echo base_url('docs/lotus-button-2.png'); ?>">

        <img class="boat-animation-R boat-animation-1" src="<?php echo base_url('docs/ship-activity.png'); ?>">

        <div class="container-fish-LR">
            <div class="fish-animation-L">
                <img class="dynamic-fish-animation" src="<?php echo base_url('docs/fish-L-2.png'); ?>" alt="Fish">
                <img class="static-fish-animation" src="<?php echo base_url('docs/fish-L-2.png'); ?>" alt="Fish">
            </div>
            <div class="fish-animation-L2">
                <img class="dynamic-fish-animation" src="<?php echo base_url('docs/fish-L-1.png'); ?>" alt="Fish">
                <img class="static-fish-animation" src="<?php echo base_url('docs/fish-L-1.png'); ?>" alt="Fish">
            </div>
            <div class="fish-animation-R">
                <img class="dynamic-fish-animation2" src="<?php echo base_url('docs/fish-R-2.png'); ?>" alt="Fish">
                <img class="static-fish-animation2" src="<?php echo base_url('docs/fish-R-2.png'); ?>" alt="Fish">
            </div>
            <div class="fish-animation-R1">
                <img class="dynamic-fish-animation2" src="<?php echo base_url('docs/fish-R-1.png'); ?>" alt="Fish">
                <img class="static-fish-animation2" src="<?php echo base_url('docs/fish-R-1.png'); ?>" alt="Fish">
            </div>
            <div class="fish-animation-R2">
                <img class="dynamic-fish-animation2" src="<?php echo base_url('docs/fish-R-2.png'); ?>" alt="Fish">
                <img class="static-fish-animation2" src="<?php echo base_url('docs/fish-R-2.png'); ?>" alt="Fish">
            </div>
        </div>

        <div class="ball-container">
            <?php
            $bubbles = ['anima_ball1.png', 'anima_ball2.png', 'anima_ball3.png'];
            for ($i = 1; $i <= 12; $i++):
                ?>
                <img class="ball-animation" src="<?php echo base_url('docs/' . $bubbles[($i - 1) % 3]); ?>" alt="Bubble">
            <?php endfor; ?>
        </div>

        <div class="crop">
            <div class="d-flex justify-content-center">
                <div class="bg-header-activity d-flex align-items-center justify-content-center">
                    <span class="font-header-home">ข่าวสาร / กิจกรรม</span>
                </div>
            </div>
            <div class="row d-flex justify-content-center"
                style="position: relative; z-index: 5 !important; margin-top: 30px;">
                <?php foreach ($qActivity as $activity) { ?>
                    <div class="card-activity col-2 mx-4" style="margin-top: 20px;">
                        <?php if (!empty($activity->activity_img)): ?>
                            <a href="<?= site_url('pages/activity_detail/' . $activity->activity_id); ?>">
                                <img src="<?php echo base_url('docs/img/' . $activity->activity_img); ?>" width="245px"
                                    height="182px" style="border-radius: 24px 24px 0 0;">
                            </a>
                        <?php else: ?>
                            <a href="<?= site_url('pages/activity_detail/' . $activity->activity_id); ?>">
                                <img src="<?php echo base_url('docs/logo.png'); ?>">
                            </a>
                        <?php endif; ?>
                        <br>
                        <div class="box-activity">
                            <div class="text-activity underline">
                                <a href="<?= site_url('pages/activity_detail/' . $activity->activity_id); ?>">
                                    <div class="activity-item">
                                        <span
                                            class="font-pages-heads-img two-line-ellipsis-activity"><?= $activity->activity_name; ?></span>
                                        <?php
                                        // วันที่ของข่าว
                                        $activity_date = new DateTime($activity->activity_date);

                                        // วันที่ปัจจุบัน
                                        $current_date = new DateTime();

                                        // คำนวณหาความต่างของวัน
                                        $interval = $current_date->diff($activity_date);
                                        $days_difference = $interval->days;

                                        // ถ้ามากกว่า 30 วัน ให้ซ่อนไว้
                                        if ($days_difference <= 30) {
                                            // แสดงรูปภาพ
                                            echo '<img src="' . base_url('docs/activity-new.gif') . '"class="activity-new-img">';
                                        }
                                        ?>
                                    </div>
                                </a>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-7 mt-3">
                                <svg style="color: #693708;" xmlns="http://www.w3.org/2000/svg" width="10" height="10"
                                    fill="currentColor" class="bi bi-calendar-minus-fill" viewBox="0 0 16 16">
                                    <path
                                        d="M4 .5a.5.5 0 0 0-1 0V1H2a2 2 0 0 0-2 2v1h16V3a2 2 0 0 0-2-2h-1V.5a.5.5 0 0 0-1 0V1H4V.5zM16 14V5H0v9a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2zM6 10h4a.5.5 0 0 1 0 1H6a.5.5 0 0 1 0-1z" />
                                </svg>
                                <span class="span-time-home ">
                                    <?php
                                    // ในการใช้งาน setThaiMonth
                                    $date = new DateTime($activity->activity_date);
                                    $day_th = $date->format('d');
                                    $month_th = setThaiMonth($date->format('F')); // เรียกใช้ setThaiMonth สำหรับชื่อเดือน
                                    $year_th = $date->format('Y') + 543; // เพิ่มขึ้น 543 ปี
                                    $formattedDate = "$day_th $month_th $year_th"; // วันที่และเดือนเป็นภาษาไทย
                                    echo $formattedDate;
                                    ?>
                                </span>
                            </div>
                            <div class="col-5">
                                <div class="font-12 underline d-flex justify-content-end mt-4">
                                    <a href="<?= site_url('pages/activity_detail/' . $activity->activity_id); ?>"><svg
                                            xmlns="http://www.w3.org/2000/svg" style="color: black; margin-top: -5px;"
                                            width="16" height="16" fill="currentColor" class="bi bi-eye"
                                            viewBox="0 0 16 16">
                                            <path
                                                d="M16 8s-3-5.5-8-5.5S0 8 0 8s3 5.5 8 5.5S16 8 16 8M1.173 8a13 13 0 0 1 1.66-2.043C4.12 4.668 5.88 3.5 8 3.5s3.879 1.168 5.168 2.457A13 13 0 0 1 14.828 8q-.086.13-.195.288c-.335.48-.83 1.12-1.465 1.755C11.879 11.332 10.119 12.5 8 12.5s-3.879-1.168-5.168-2.457A13 13 0 0 1 1.172 8z" />
                                            <path
                                                d="M8 5.5a2.5 2.5 0 1 0 0 5 2.5 2.5 0 0 0 0-5M4.5 8a3.5 3.5 0 1 1 7 0 3.5 3.5 0 0 1-7 0" />
                                        </svg>&nbsp;เปิดดู : <span><?= $activity->activity_view; ?></span>
                                    </a>
                                </div>
                            </div>
                        </div>

                    </div>
                <?php } ?>
            </div>
            <div class="d-flex justify-content-center underline" style="margin-top: 20px;">
                <a href="<?php echo site_url('pages/activity'); ?>">
                    <div class="button-activity-all text-center">
                        <span class="font-all-home">ดูทั้งหมด</span>
                    </div>
                </a>
            </div>
        </div>
    </div>
</div>

<div class="position-2bg">
    <div class="bg-public-news">
        <div class="bg-public1-2"></div>

        <img class="animation-wind-L animation-wind-10"
            src="<?php echo base_url('docs/animation-flower-public1-1.png'); ?>">
        <img class="animation-wind-R animation-wind-11"
            src="<?php echo base_url('docs/animation-flower-public1-2.png'); ?>">


        <div class="animation-container">
            <?php
            $images = [
                'animation-flower-public2-1.png',
                'animation-flower-public2-2.png',
            ];

            for ($i = 1; $i <= 14; $i++) {
                $image = $images[($i - 1) % 2];
                echo '<img class="animation-item animation' . $i . '" src="' . base_url('docs/' . $image) . '">';
            }
            ?>
        </div>

        <div class="crop">
            <div class="d-flex justify-content-center" style="padding-top: 0px;">
                <div class="bg-header-activity">
                    <span class="font-header-home ">งานประชาสัมพันธ์</span>
                </div>
            </div>
            <div id="myDIV" class="underline" style="margin-top: 40px;">
                <div class="tab-container2 d-flex justify-content-center">
                    <div class="tab-link-two" onclick="openTabTwo('tabtwo1')">
                        <div class="public-button active-public">
                            <span class="font-public-button">ข่าวประชาสัมพันธ์</span>
                        </div>
                    </div>
                    <div class="tab-link-two" onclick="openTabTwo('tabtwo2')">
                        <div class="public-button">
                            <span class="font-public-button">
                                <?php
                                $abbreviation = get_config_value('abbreviation');
                                $canon = ($abbreviation == 'อบต.') ? 'ข้อบัญญัติ' : 'เทศบัญญัติ';
                                echo $canon;
                                ?>
                            </span>
                        </div>
                    </div>
                    <div class="tab-link-two" onclick="openTabTwo('tabtwo3')">
                        <!-- <img src="docs/public_button.png" alt="Tab 3"> -->
                        <div class="public-button">
                            <span class="font-public-button">คำสั่ง</span>
                        </div>
                    </div>
                    <div class="tab-link-two" onclick="openTabTwo('tabtwo4')">
                        <!-- <img src="docs/public_button.png" alt="Tab 4"> -->
                        <div class="public-button">
                            <span class="font-public-button">ประกาศ</span>
                        </div>
                    </div>
                </div>
                <br>
                <div class="d-flex justify-content-center">
                    <div id="tabtwo1" class="tab-content-two">
                        <?php foreach ($qNews as $news) { ?>
                            <div class="content-news-detail">
                                <a href="<?php echo site_url('Pages/news_detail/' . $news->news_id); ?>">
                                    <div class="row">
                                        <div class="col-8">
                                            <span class="text-news"><img
                                                    src="docs/icon-public.png">&nbsp;&nbsp;<?= strip_tags($news->news_name); ?></span>
                                        </div>
                                        <div class="col-4">
                                            <div class="row" style="margin-left: 170px;">
                                                <div class="col-10">
                                                    <div class="d-flex justify-content-start ">
                                                        <span class="text-news-time">
                                                            <?php
                                                            // ในการใช้งาน setThaiMonth
                                                            $date = new DateTime($news->news_date);
                                                            $day_th = $date->format('d');
                                                            $month_th = setThaiMonth($date->format('F')); // เรียกใช้ setThaiMonth สำหรับชื่อเดือน
                                                            $year_th = $date->format('Y') + 543; // เพิ่มขึ้น 543 ปี
                                                            $formattedDate = "$day_th $month_th $year_th"; // วันที่และเดือนเป็นภาษาไทย
                                                            echo $formattedDate;
                                                            ?>
                                                        </span>
                                                    </div>

                                                </div>
                                                <div class="col-2" style="margin-top: -27px;">
                                                    <?php
                                                    // วันที่ของข่าว
                                                    $news_date = new DateTime($news->news_date);

                                                    // วันที่ปัจจุบัน
                                                    $current_date = new DateTime();

                                                    // คำนวณหาความต่างของวัน
                                                    $interval = $current_date->diff($news_date);
                                                    $days_difference = $interval->days;

                                                    // ถ้ามากกว่า 30 วัน ให้ซ่อนไว้
                                                    if ($days_difference > 30) {
                                                        // ไม่แสดงรูปภาพ
                                                    } else {
                                                        // แสดงรูปภาพ
                                                        echo '<img src="docs/news-new.gif" width="40" height="16">';
                                                    }
                                                    ?>

                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </a>
                            </div>
                        <?php } ?>
                        <div class="d-flex justify-content-center underline" style="margin-top: 80px;">
                            <a href="<?php echo site_url('pages/news'); ?>">
                                <div class="button-new-all text-center">
                                    <span class="font-all-home">ดูทั้งหมด</span>
                                </div>
                            </a>
                        </div>
                    </div>
                    <div id="tabtwo2" class="tab-content-two">
                        <div class="content-news-detail">
                            <a href="<?php echo site_url('Pages/canon_bgps'); ?>">
                                <span class="text-news"><img src="docs/icon-public.png">&nbsp;&nbsp;
                                    <?php
                                    $abbreviation = get_config_value('abbreviation');
                                    $canon = ($abbreviation == 'อบต.') ? 'ข้อบัญญัติ' : 'เทศบัญญัติ';
                                    echo $canon;
                                    ?>งบประมาณ
                                </span>
                            </a>
                        </div>
                        <div class="content-news-detail">
                            <a href="<?php echo site_url('Pages/canon_chh'); ?>">
                                <span class="text-news"><img src="docs/icon-public.png">&nbsp;&nbsp;
                                    <?php
                                    $abbreviation = get_config_value('abbreviation');
                                    $canon = ($abbreviation == 'อบต.') ? 'ข้อบัญญัติ' : 'เทศบัญญัติ';
                                    echo $canon;
                                    ?>การควบคุมกิจการที่เป็นอันตรายต่อสุขภาพ
                                </span>
                            </a>
                        </div>
                        <div class="content-news-detail">
                            <a href="<?php echo site_url('Pages/canon_ritw'); ?>">
                                <span class="text-news"><img src="docs/icon-public.png">&nbsp;&nbsp;
                                    <?php
                                    $abbreviation = get_config_value('abbreviation');
                                    $canon = ($abbreviation == 'อบต.') ? 'ข้อบัญญัติ' : 'เทศบัญญัติ';
                                    echo $canon;
                                    ?>การติดตั้งระบบบำบัดน้ำเสียในอาคาร
                                </span>
                            </a>
                        </div>
                        <div class="content-news-detail">
                            <a href="<?php echo site_url('Pages/canon_market'); ?>">
                                <span class="text-news"><img src="docs/icon-public.png">&nbsp;&nbsp;
                                    <?php
                                    $abbreviation = get_config_value('abbreviation');
                                    $canon = ($abbreviation == 'อบต.') ? 'ข้อบัญญัติ' : 'เทศบัญญัติ';
                                    echo $canon;
                                    ?>ตลาด
                                </span>
                            </a>
                        </div>
                        <div class="content-news-detail">
                            <a href="<?php echo site_url('Pages/canon_rmwp'); ?>">
                                <span class="text-news"><img src="docs/icon-public.png">&nbsp;&nbsp;
                                    <?php
                                    $abbreviation = get_config_value('abbreviation');
                                    $canon = ($abbreviation == 'อบต.') ? 'ข้อบัญญัติ' : 'เทศบัญญัติ';
                                    echo $canon;
                                    ?>การจัดการสิ่งปฏิกูลและมูลฝอย
                                </span>
                            </a>
                        </div>
                        <div class="content-news-detail">
                            <a href="<?php echo site_url('Pages/canon_rcsp'); ?>">
                                <span class="text-news"><img src="docs/icon-public.png">&nbsp;&nbsp;
                                    <?php
                                    $abbreviation = get_config_value('abbreviation');
                                    $canon = ($abbreviation == 'อบต.') ? 'ข้อบัญญัติ' : 'เทศบัญญัติ';
                                    echo $canon;
                                    ?>หลักเกณฑ์การคัดมูลฝอย
                                </span>
                            </a>
                        </div>
                        <div class="content-news-detail">
                            <a href="<?php echo site_url('Pages/canon_rcp'); ?>">
                                <span class="text-news"><img src="docs/icon-public.png">&nbsp;&nbsp;
                                    <?php
                                    $abbreviation = get_config_value('abbreviation');
                                    $canon = ($abbreviation == 'อบต.') ? 'ข้อบัญญัติ' : 'เทศบัญญัติ';
                                    echo $canon;
                                    ?>การควบคุมการเลี้ยงหรือปล่อยสุนัขและแมว
                                </span>
                            </a>
                        </div>
                    </div>
                    <div id="tabtwo3" class="tab-content-two">
                        <?php foreach ($qOrder as $gw) { ?>
                            <div class="content-news-detail">
                                <a href="<?php echo site_url('Pages/order_detail/' . $gw->order_id); ?>">
                                    <div class="row">
                                        <div class="col-8">
                                            <span class="text-news"><img
                                                    src="docs/icon-public.png">&nbsp;&nbsp;<?= strip_tags($gw->order_name); ?></span>
                                        </div>
                                        <div class="col-4">
                                            <div class="row" style="margin-left: 170px;">
                                                <div class="col-10">
                                                    <div class="d-flex justify-content-start ">
                                                        <span class="text-news-time">
                                                            <?php
                                                            // ในการใช้งาน setThaiMonth
                                                            $date = new DateTime($gw->order_date);
                                                            $day_th = $date->format('d');
                                                            $month_th = setThaiMonth($date->format('F')); // เรียกใช้ setThaiMonth สำหรับชื่อเดือน
                                                            $year_th = $date->format('Y') + 543; // เพิ่มขึ้น 543 ปี
                                                            $formattedDate = "$day_th $month_th $year_th"; // วันที่และเดือนเป็นภาษาไทย
                                                            echo $formattedDate;
                                                            ?>
                                                        </span>
                                                    </div>
                                                </div>
                                                <div class="col-2" style="margin-top: -27px;">
                                                    <?php
                                                    // วันที่ของข่าว
                                                    $order_date = new DateTime($gw->order_date);

                                                    // วันที่ปัจจุบัน
                                                    $current_date = new DateTime();

                                                    // คำนวณหาความต่างของวัน
                                                    $interval = $current_date->diff($order_date);
                                                    $days_difference = $interval->days;

                                                    // ถ้ามากกว่า 30 วัน ให้ซ่อนไว้
                                                    if ($days_difference > 30) {
                                                        // ไม่แสดงรูปภาพ
                                                    } else {
                                                        // แสดงรูปภาพ
                                                        echo '<img src="docs/news-new.gif" width="40" height="16">';
                                                    }
                                                    ?>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </a>
                            </div>
                        <?php } ?>
                        <div class="d-flex justify-content-center underline" style="margin-top: 80px;">
                            <a href="<?php echo site_url('pages/order'); ?>">
                                <div class="button-new-all text-center">
                                    <span class="font-all-home">ดูทั้งหมด</span>
                                </div>
                            </a>
                        </div>
                    </div>

                    <div id="tabtwo4" class="tab-content-two">
                        <?php foreach ($qAnnounce as $gw) { ?>
                            <div class="content-news2-detail">
                                <a href="<?php echo site_url('Pages/announce_detail/' . $gw->announce_id); ?>">
                                    <div class="row">
                                        <div class="col-8">
                                            <span class="text-news"><img
                                                    src="docs/icon-public.png">&nbsp;&nbsp;<?= strip_tags($gw->announce_name); ?></span>
                                        </div>
                                        <div class="col-4">
                                            <div class="row" style="margin-left: 170px;">
                                                <div class="col-10">
                                                    <div class="d-flex justify-content-start">
                                                        <span class="text-news-time">
                                                            <?php
                                                            // ในการใช้งาน setThaiMonth
                                                            $date = new DateTime($gw->announce_date);
                                                            $day_th = $date->format('d');
                                                            $month_th = setThaiMonth($date->format('F')); // เรียกใช้ setThaiMonth สำหรับชื่อเดือน
                                                            $year_th = $date->format('Y') + 543; // เพิ่มขึ้น 543 ปี
                                                            $formattedDate = "$day_th $month_th $year_th"; // วันที่และเดือนเป็นภาษาไทย
                                                            echo $formattedDate;
                                                            ?>
                                                        </span>
                                                    </div>
                                                </div>
                                                <div class="col-2" style="margin-top: -27px;">
                                                    <?php
                                                    // วันที่ของข่าว
                                                    $announce_date = new DateTime($gw->announce_date);

                                                    // วันที่ปัจจุบัน
                                                    $current_date = new DateTime();

                                                    // คำนวณหาความต่างของวัน
                                                    $interval = $current_date->diff($announce_date);
                                                    $days_difference = $interval->days;

                                                    // ถ้ามากกว่า 30 วัน ให้ซ่อนไว้
                                                    if ($days_difference > 30) {
                                                        // ไม่แสดงรูปภาพ
                                                    } else {
                                                        // แสดงรูปภาพ
                                                        echo '<img src="docs/news-new.gif" width="40" height="16">';
                                                    }
                                                    ?>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </a>
                            </div>
                        <?php } ?>
                        <div class="d-flex justify-content-center underline" style="margin-top: 80px;">
                            <a href="<?php echo site_url('pages/announce'); ?>">
                                <div class="button-new-all text-center">
                                    <span class="font-all-home">ดูทั้งหมด</span>
                                </div>
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="bg-public-news2" id="egp">
        <div class="crop">
            <div class="d-flex justify-content-center" style="padding-top: 0px; ">
                <div class="bg-header-activity">
                    <span class="font-header-home">งานจัดซื้อจัดจ้าง</span>
                </div>
            </div>
            <div id="myDIV2" class="underline" style="margin-top: 40px;">
                <div class="tab-container2 d-flex justify-content-center">
                    <div class="tab-link" onclick="openTab('tab1')">
                        <!-- <img src="docs/news_button.png" alt="Tab 1"> -->
                        <div class="new-button active-new">
                            <span class="font-new-button">ข่าวจัดซื้อจัดจ้าง</span>
                        </div>
                    </div>
                    <div class="tab-link" onclick="openTab('tab2')">
                        <!-- <img src="docs/news_button.png" alt="Tab 2"> -->
                        <div class="new-button">
                            <span class="font-new-button">จัดซื้อจัดจ้าง e-GP</span>
                        </div>
                    </div>
                    <div class="tab-link" onclick="openTab('tab3')">
                        <!-- <img src="docs/news_button.png" alt="Tab 3"> -->
                        <div class="new-button">
                            <span class="font-new-button2">ประกาศจัดซื้อจัดจ้าง</span>
                        </div>
                    </div>
                    <div class="tab-link" onclick="openTab('tab4')">
                        <!-- <img src="docs/news_button.png" alt="Tab 4"> -->
                        <div class="new-button">
                            <span class="font-new-button2">รายงานจัดซื้อจัดจ้าง</span>
                        </div>
                    </div>
                </div>
                <br>
                <div class="d-flex justify-content-center">
                    <div id="tab1" class="tab-content">
                        <div id="myDIVPm" class="underline" style="margin-top: 40px;">
                            <div class="tab-container3 d-flex justify-content-center">
                                <div class="tab-link-pm" onclick="openTabPm('tabPm1')">
                                    <div class="pm-button-L active-pm-L">
                                        <span class="font-new-pm">ประกาศรายชื่อผู้ชนะการเสนอราคา</span>
                                    </div>
                                </div>
                                <div class="tab-link-pm" onclick="openTabPm('tabPm2')">
                                    <div class="pm-button">
                                        <span class="font-new-pm">แผนการจัดซื้อจัดจ้าง</span>
                                    </div>
                                </div>
                                <div class="tab-link-pm" onclick="openTabPm('tabPm3')">
                                    <div class="pm-button">
                                        <span class="font-new-pm">ประกาศราคากลาง</span>
                                    </div>
                                </div>
                                <div class="tab-link-pm" onclick="openTabPm('tabPm4')">
                                    <div class="pm-button-R">
                                        <span class="font-new-pm">ร่างเอกสารประกวดราคา</span>
                                    </div>
                                </div>
                            </div>
                            <br>
                            <div class="d-flex justify-content-center">
                                <div id="tabPm1" class="tab-content-pm">
                                    <div class="news-dla-prov3">
                                        <?php if (!empty($procurement_egp_tbl_w0)): ?>
                                            <?php
                                            $totalDocuments = count($procurement_egp_tbl_w0);
                                            foreach ($procurement_egp_tbl_w0 as $index => $rs): ?>
                                                <div class="row mt-1 underline" style="padding-left: 20px;">
                                                    <div class="col-3 font-dla-2">
                                                        <img src="docs/icon-news.png">&nbsp;&nbsp;
                                                        <?php
                                                        if (!function_exists('formatDateThai')) {
                                                            function formatDateThai($dateStr)
                                                            {
                                                                $thaiMonths = [
                                                                    '01' => 'มกราคม',
                                                                    '02' => 'กุมภาพันธ์',
                                                                    '03' => 'มีนาคม',
                                                                    '04' => 'เมษายน',
                                                                    '05' => 'พฤษภาคม',
                                                                    '06' => 'มิถุนายน',
                                                                    '07' => 'กรกฎาคม',
                                                                    '08' => 'สิงหาคม',
                                                                    '09' => 'กันยายน',
                                                                    '10' => 'ตุลาคม',
                                                                    '11' => 'พฤศจิกายน',
                                                                    '12' => 'ธันวาคม',
                                                                ];

                                                                $date = new DateTime($dateStr);
                                                                $day = $date->format('d');
                                                                $month = $date->format('m');
                                                                $year = $date->format('Y') + 543; // แปลงปี ค.ศ. เป็น พ.ศ.
                                                
                                                                return $day . ' ' . $thaiMonths[$month] . ' ' . $year;
                                                            }
                                                        }

                                                        // ตัวอย่างการใช้งาน
                                                        if (!empty($rs['item_date'])) {
                                                            echo formatDateThai($rs['item_date']);
                                                        }
                                                        ?>
                                                    </div>
                                                    <?php
                                                    // ตรวจสอบว่าค่า $rs['item_date'] มีค่าหรือไม่
                                                    if (!empty($rs['item_date'])) {
                                                        // วันที่ของข่าว
                                                        $item_date = new DateTime($rs['item_date']);

                                                        // วันที่ปัจจุบัน
                                                        $current_date = new DateTime();

                                                        // คำนวณหาความต่างของวัน
                                                        $interval = $current_date->diff($item_date);
                                                        $days_difference = $interval->days;

                                                        if ($days_difference <= 30) {
                                                            // แสดงรูปภาพ
                                                            echo '<div class="col-1 font-dla-2" style="margin-left: -115px; margin-right: -55px;">';
                                                            echo '<div class="bt-new-dla"><span class="text-new-dla">new</span></div>';
                                                            echo '</div>';
                                                        } else {
                                                            echo '<div class="col-1 font-dla-2" style="margin-left: -230px;">';
                                                            echo '</div>';
                                                        }
                                                    }
                                                    ?>

                                                    <div class="col-8" style="margin-left: 0px;">
                                                        <a href="<?php echo $rs['item_url']; ?>" target="_blank"
                                                            rel="noopener noreferrer">
                                                            <span
                                                                class="font-dla-2 line-ellipsis-dla-prov2-pm"><?php echo $rs['item_title']; ?></span>
                                                        </a>
                                                    </div>
                                                </div>
                                                <?php if ($index < $totalDocuments - 1): ?>
                                                    <div class="dla-end-pm"></div>
                                                <?php endif; ?>
                                            <?php endforeach; ?>
                                        <?php endif; ?>
                                    </div>
                                    <div class="d-flex justify-content-center underline" style="margin-top: 80px;">
                                        <a href="<?php echo site_url('pages/procurement_tbl_w0_search'); ?>">
                                            <div class="button-new2-all text-center">
                                                <span class="font-all-home">ดูทั้งหมด</span>
                                            </div>
                                        </a>
                                    </div>
                                </div>
                                <div id="tabPm2" class="tab-content-pm">
                                    <div class="news-dla-prov3">
                                        <?php if (!empty($procurement_egp_tbl_p0)): ?>
                                            <?php
                                            $totalDocuments = count($procurement_egp_tbl_p0);
                                            foreach ($procurement_egp_tbl_p0 as $index => $rs): ?>
                                                <div class="row mt-1 underline" style="padding-left: 20px;">
                                                    <div class="col-3 font-dla-2">
                                                        <img src="docs/icon-news.png">&nbsp;&nbsp;
                                                        <?php
                                                        if (!function_exists('formatDateThai')) {
                                                            function formatDateThai($dateStr)
                                                            {
                                                                $thaiMonths = [
                                                                    '01' => 'มกราคม',
                                                                    '02' => 'กุมภาพันธ์',
                                                                    '03' => 'มีนาคม',
                                                                    '04' => 'เมษายน',
                                                                    '05' => 'พฤษภาคม',
                                                                    '06' => 'มิถุนายน',
                                                                    '07' => 'กรกฎาคม',
                                                                    '08' => 'สิงหาคม',
                                                                    '09' => 'กันยายน',
                                                                    '10' => 'ตุลาคม',
                                                                    '11' => 'พฤศจิกายน',
                                                                    '12' => 'ธันวาคม',
                                                                ];

                                                                $date = new DateTime($dateStr);
                                                                $day = $date->format('d');
                                                                $month = $date->format('m');
                                                                $year = $date->format('Y') + 543; // แปลงปี ค.ศ. เป็น พ.ศ.
                                                
                                                                return $day . ' ' . $thaiMonths[$month] . ' ' . $year;
                                                            }
                                                        }

                                                        // ตัวอย่างการใช้งาน
                                                        if (!empty($rs['item_date'])) {
                                                            echo formatDateThai($rs['item_date']);
                                                        }
                                                        ?>
                                                    </div>
                                                    <?php
                                                    // ตรวจสอบว่าค่า $rs['item_date'] มีค่าหรือไม่
                                                    if (!empty($rs['item_date'])) {
                                                        // วันที่ของข่าว
                                                        $item_date = new DateTime($rs['item_date']);

                                                        // วันที่ปัจจุบัน
                                                        $current_date = new DateTime();

                                                        // คำนวณหาความต่างของวัน
                                                        $interval = $current_date->diff($item_date);
                                                        $days_difference = $interval->days;

                                                        // ถ้ามากกว่า 30 วัน ให้ซ่อนไว้
                                                        if ($days_difference <= 30) {
                                                            // แสดงรูปภาพ
                                                            echo '<div class="col-1 font-dla-2" style="margin-left: -115px; margin-right: -55px;">';
                                                            echo '<div class="bt-new-dla"><span class="text-new-dla">new</span></div>';
                                                            echo '</div>';
                                                        } else {
                                                            echo '<div class="col-1 font-dla-2" style="margin-left: -230px;">';
                                                            echo '</div>';
                                                        }
                                                    }
                                                    ?>

                                                    <div class="col-8" style="margin-left: 0px;">
                                                        <a href="<?php echo $rs['item_url']; ?>" target="_blank"
                                                            rel="noopener noreferrer">
                                                            <span
                                                                class="font-dla-2 line-ellipsis-dla-prov2-pm"><?php echo $rs['item_title']; ?></span>
                                                        </a>
                                                    </div>
                                                </div>
                                                <?php if ($index < $totalDocuments - 1): ?>
                                                    <div class="dla-end-pm"></div>
                                                <?php endif; ?>
                                            <?php endforeach; ?>
                                        <?php endif; ?>
                                    </div>
                                    <div class="d-flex justify-content-center underline" style="margin-top: 80px;">
                                        <a href="<?php echo site_url('pages/procurement_tbl_p0_search'); ?>">
                                            <div class="button-new2-all text-center">
                                                <span class="font-all-home">ดูทั้งหมด</span>
                                            </div>
                                        </a>
                                    </div>
                                </div>
                                <div id="tabPm3" class="tab-content-pm">
                                    <div class="news-dla-prov3">
                                        <?php if (!empty($procurement_egp_tbl_15)): ?>
                                            <?php
                                            $totalDocuments = count($procurement_egp_tbl_15);
                                            foreach ($procurement_egp_tbl_15 as $index => $rs): ?>
                                                <div class="row mt-1 underline" style="padding-left: 20px;">
                                                    <div class="col-3 font-dla-2">
                                                        <img src="docs/icon-news.png">&nbsp;&nbsp;
                                                        <?php
                                                        if (!function_exists('formatDateThai')) {
                                                            function formatDateThai($dateStr)
                                                            {
                                                                $thaiMonths = [
                                                                    '01' => 'มกราคม',
                                                                    '02' => 'กุมภาพันธ์',
                                                                    '03' => 'มีนาคม',
                                                                    '04' => 'เมษายน',
                                                                    '05' => 'พฤษภาคม',
                                                                    '06' => 'มิถุนายน',
                                                                    '07' => 'กรกฎาคม',
                                                                    '08' => 'สิงหาคม',
                                                                    '09' => 'กันยายน',
                                                                    '10' => 'ตุลาคม',
                                                                    '11' => 'พฤศจิกายน',
                                                                    '12' => 'ธันวาคม',
                                                                ];

                                                                $date = new DateTime($dateStr);
                                                                $day = $date->format('d');
                                                                $month = $date->format('m');
                                                                $year = $date->format('Y') + 543; // แปลงปี ค.ศ. เป็น พ.ศ.
                                                
                                                                return $day . ' ' . $thaiMonths[$month] . ' ' . $year;
                                                            }
                                                        }

                                                        // ตัวอย่างการใช้งาน
                                                        if (!empty($rs['item_date'])) {
                                                            echo formatDateThai($rs['item_date']);
                                                        }
                                                        ?>
                                                    </div>
                                                    <?php
                                                    // ตรวจสอบว่าค่า $rs['item_date'] มีค่าหรือไม่
                                                    if (!empty($rs['item_date'])) {
                                                        // วันที่ของข่าว
                                                        $item_date = new DateTime($rs['item_date']);

                                                        // วันที่ปัจจุบัน
                                                        $current_date = new DateTime();

                                                        // คำนวณหาความต่างของวัน
                                                        $interval = $current_date->diff($item_date);
                                                        $days_difference = $interval->days;

                                                        // ถ้ามากกว่า 30 วัน ให้ซ่อนไว้
                                                        if ($days_difference <= 30) {
                                                            // แสดงรูปภาพ
                                                            echo '<div class="col-1 font-dla-2" style="margin-left: -115px; margin-right: -55px;">';
                                                            echo '<div class="bt-new-dla"><span class="text-new-dla">new</span></div>';
                                                            echo '</div>';
                                                        } else {
                                                            echo '<div class="col-1 font-dla-2" style="margin-left: -230px;">';
                                                            echo '</div>';
                                                        }
                                                    }
                                                    ?>

                                                    <div class="col-8" style="margin-left: 0px;">
                                                        <a href="<?php echo $rs['item_url']; ?>" target="_blank"
                                                            rel="noopener noreferrer">
                                                            <span
                                                                class="font-dla-2 line-ellipsis-dla-prov2-pm"><?php echo $rs['item_title']; ?></span>
                                                        </a>
                                                    </div>
                                                </div>
                                                <?php if ($index < $totalDocuments - 1): ?>
                                                    <div class="dla-end-pm"></div>
                                                <?php endif; ?>
                                            <?php endforeach; ?>
                                        <?php endif; ?>
                                    </div>
                                    <div class="d-flex justify-content-center underline" style="margin-top: 80px;">
                                        <a href="<?php echo site_url('pages/procurement_tbl_15_search'); ?>">
                                            <div class="button-new2-all text-center">
                                                <span class="font-all-home">ดูทั้งหมด</span>
                                            </div>
                                        </a>
                                    </div>
                                </div>
                                <div id="tabPm4" class="tab-content-pm">
                                    <div class="news-dla-prov3">
                                        <?php if (!empty($procurement_egp_tbl_b0)): ?>
                                            <?php
                                            $totalDocuments = count($procurement_egp_tbl_b0);
                                            foreach ($procurement_egp_tbl_b0 as $index => $rs): ?>
                                                <div class="row mt-1 underline" style="padding-left: 20px;">
                                                    <div class="col-3 font-dla-2">
                                                        <img src="docs/icon-news.png">&nbsp;&nbsp;
                                                        <?php
                                                        if (!function_exists('formatDateThai')) {
                                                            function formatDateThai($dateStr)
                                                            {
                                                                $thaiMonths = [
                                                                    '01' => 'มกราคม',
                                                                    '02' => 'กุมภาพันธ์',
                                                                    '03' => 'มีนาคม',
                                                                    '04' => 'เมษายน',
                                                                    '05' => 'พฤษภาคม',
                                                                    '06' => 'มิถุนายน',
                                                                    '07' => 'กรกฎาคม',
                                                                    '08' => 'สิงหาคม',
                                                                    '09' => 'กันยายน',
                                                                    '10' => 'ตุลาคม',
                                                                    '11' => 'พฤศจิกายน',
                                                                    '12' => 'ธันวาคม',
                                                                ];

                                                                $date = new DateTime($dateStr);
                                                                $day = $date->format('d');
                                                                $month = $date->format('m');
                                                                $year = $date->format('Y') + 543; // แปลงปี ค.ศ. เป็น พ.ศ.
                                                
                                                                return $day . ' ' . $thaiMonths[$month] . ' ' . $year;
                                                            }
                                                        }

                                                        // ตัวอย่างการใช้งาน
                                                        if (!empty($rs['item_date'])) {
                                                            echo formatDateThai($rs['item_date']);
                                                        }
                                                        ?>
                                                    </div>
                                                    <?php
                                                    // ตรวจสอบว่าค่า $rs['item_date'] มีค่าหรือไม่
                                                    if (!empty($rs['item_date'])) {
                                                        // วันที่ของข่าว
                                                        $item_date = new DateTime($rs['item_date']);

                                                        // วันที่ปัจจุบัน
                                                        $current_date = new DateTime();

                                                        // คำนวณหาความต่างของวัน
                                                        $interval = $current_date->diff($item_date);
                                                        $days_difference = $interval->days;

                                                        // ถ้ามากกว่า 30 วัน ให้ซ่อนไว้
                                                        if ($days_difference <= 30) {
                                                            // แสดงรูปภาพ
                                                            echo '<div class="col-1 font-dla-2" style="margin-left: -115px; margin-right: -55px;">';
                                                            echo '<div class="bt-new-dla"><span class="text-new-dla">new</span></div>';
                                                            echo '</div>';
                                                        } else {
                                                            echo '<div class="col-1 font-dla-2" style="margin-left: -230px;">';
                                                            echo '</div>';
                                                        }
                                                    }
                                                    ?>

                                                    <div class="col-8" style="margin-left: 0px;">
                                                        <a href="<?php echo $rs['item_url']; ?>" target="_blank"
                                                            rel="noopener noreferrer">
                                                            <span
                                                                class="font-dla-2 line-ellipsis-dla-prov2-pm"><?php echo $rs['item_title']; ?></span>
                                                        </a>
                                                    </div>
                                                </div>
                                                <?php if ($index < $totalDocuments - 1): ?>
                                                    <div class="dla-end-pm"></div>
                                                <?php endif; ?>
                                            <?php endforeach; ?>
                                        <?php endif; ?>
                                    </div>
                                    <div class="d-flex justify-content-center underline" style="margin-top: 80px;">
                                        <a href="<?php echo site_url('pages/procurement_tbl_b0_search'); ?>">
                                            <div class="button-new2-all text-center">
                                                <span class="font-all-home">ดูทั้งหมด</span>
                                            </div>
                                        </a>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div id="tab2" class="tab-content">
                        <?php foreach ($qEgp as $egp) { ?>
                            <div class="content-news2-detail">
                                <a href="https://process3.gprocurement.go.th/egp2procmainWeb/jsp/procsearch.sch?servlet=gojsp&proc_id=ShowHTMLFile&processFlows=Procure&projectId=<?= $egp->project_id; ?>&templateType=W2&temp_Announ=A&temp_itemNo=0&seqNo=1"
                                    target="_blank">
                                    <div class="row">
                                        <div class="col-8">
                                            <span class="text-news"><img src="docs/e-gp.png" width="40px"
                                                    style="margin-top: -5px;">&nbsp;&nbsp;<?= strip_tags($egp->project_name); ?></span>
                                        </div>
                                        <div class="col-4">
                                            <div class="row" style="margin-left: 170px;">
                                                <div class="col-10">
                                                    <div class="d-flex justify-content-start">
                                                        <span class="text-news-time">
                                                            <?php
                                                            // สมมติว่าค่าที่ได้รับมาจากตัวแปร $rs['doc_date'] อยู่ในรูปแบบ "10 ม.ค. 67"
                                                            $dateStr = $egp->transaction_date;

                                                            // แปลงเดือนจากชื่อไทยย่อเป็นชื่อเต็ม
                                                            $thaiMonths = [
                                                                'ม.ค.' => 'มกราคม',
                                                                'ก.พ.' => 'กุมภาพันธ์',
                                                                'มี.ค.' => 'มีนาคม',
                                                                'เม.ย.' => 'เมษายน',
                                                                'พ.ค.' => 'พฤษภาคม',
                                                                'มิ.ย.' => 'มิถุนายน',
                                                                'ก.ค.' => 'กรกฎาคม',
                                                                'ส.ค.' => 'สิงหาคม',
                                                                'ก.ย.' => 'กันยายน',
                                                                'ต.ค.' => 'ตุลาคม',
                                                                'พ.ย.' => 'พฤศจิกายน',
                                                                'ธ.ค.' => 'ธันวาคม',
                                                            ];

                                                            // แปลงเดือนใน $dateStr โดยใช้การแทนที่จาก array $thaiMonths
                                                            foreach ($thaiMonths as $shortMonth => $fullMonth) {
                                                                if (strpos($dateStr, $shortMonth) !== false) {
                                                                    $dateStr = str_replace($shortMonth, $fullMonth, $dateStr);
                                                                    break; // ออกจาก loop เมื่อเจอการแทนที่แล้ว
                                                                }
                                                            }

                                                            // แปลงปีคริสต์ศักราช (สองหลัก) เป็นปีพุทธศักราช (สี่หลัก)
                                                            preg_match('/\d{2}$/', $dateStr, $matches);
                                                            if ($matches) {
                                                                $year = $matches[0]; // ดึงตัวเลขสองหลักท้ายสุด ซึ่งคือปีในรูปแบบ 67
                                                                $fullYear = (int) $year < 50 ? '25' . $year : '25' . $year; // เพิ่ม '25' ข้างหน้าปี
                                                                $dateStr = str_replace($year, $fullYear, $dateStr); // แทนที่ปีด้วยปีที่เพิ่ม '25' ข้างหน้า
                                                            }

                                                            // แสดงผลลัพธ์
                                                            echo $dateStr; // ตัวอย่างเช่น "10 มกราคม 2567"
                                                            ?>
                                                        </span>
                                                    </div>
                                                </div>
                                                <div class="col-2" style="margin-top: -27px;">
                                                    <?php
                                                    // วันที่ของข่าว
                                                    $contract_contract_date = new DateTime($egp->contract_contract_date);

                                                    // วันที่ปัจจุบัน
                                                    $current_date = new DateTime();

                                                    // คำนวณหาความต่างของวัน
                                                    $interval = $current_date->diff($contract_contract_date);
                                                    $days_difference = $interval->days;

                                                    // ถ้ามากกว่า 30 วัน ให้ซ่อนไว้
                                                    if ($days_difference > 30) {
                                                        // ไม่แสดงรูปภาพ
                                                    } else {
                                                        // แสดงรูปภาพ
                                                        echo '<img src="docs/news-new.gif" width="40" height="16">';
                                                    }
                                                    ?>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </a>
                            </div>
                        <?php } ?>
                        <div class="d-flex justify-content-center underline" style="margin-top: 80px;">
                            <a href="<?php echo site_url('pages/egp'); ?>">
                                <div class="button-new2-all text-center">
                                    <span class="font-all-home">ดูทั้งหมด</span>
                                </div>
                            </a>
                        </div>
                    </div>
                    <div id="tab3" class="tab-content">
                        <?php foreach ($qProcurement as $pcm) { ?>
                            <div class="content-news2-detail">
                                <a href="<?php echo site_url('Pages/procurement_detail/' . $pcm->procurement_id); ?>">
                                    <div class="row">
                                        <div class="col-8">
                                            <span class="text-news"><img
                                                    src="docs/icon-public.png">&nbsp;&nbsp;<?= strip_tags($pcm->procurement_name); ?></span>
                                        </div>
                                        <div class="col-4">
                                            <div class="row" style="margin-left: 170px;">
                                                <div class="col-10">
                                                    <div class="d-flex justify-content-start">
                                                        <span class="text-news-time">
                                                            <?php
                                                            // ในการใช้งาน setThaiMonth
                                                            $date = new DateTime($pcm->procurement_date);
                                                            $day_th = $date->format('d');
                                                            $month_th = setThaiMonth($date->format('F')); // เรียกใช้ setThaiMonth สำหรับชื่อเดือน
                                                            $year_th = $date->format('Y') + 543; // เพิ่มขึ้น 543 ปี
                                                            $formattedDate = "$day_th $month_th $year_th"; // วันที่และเดือนเป็นภาษาไทย
                                                            echo $formattedDate;
                                                            ?>
                                                        </span>
                                                    </div>
                                                </div>
                                                <div class="col-2" style="margin-top: -27px;">
                                                    <?php
                                                    // วันที่ของข่าว
                                                    $procurement_date = new DateTime($pcm->procurement_date);

                                                    // วันที่ปัจจุบัน
                                                    $current_date = new DateTime();

                                                    // คำนวณหาความต่างของวัน
                                                    $interval = $current_date->diff($procurement_date);
                                                    $days_difference = $interval->days;

                                                    // ถ้ามากกว่า 30 วัน ให้ซ่อนไว้
                                                    if ($days_difference > 30) {
                                                        // ไม่แสดงรูปภาพ
                                                    } else {
                                                        // แสดงรูปภาพ
                                                        echo '<img src="docs/news-new.gif" width="40" height="16">';
                                                    }
                                                    ?>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </a>
                            </div>
                        <?php } ?>
                        <div class="d-flex justify-content-center underline" style="margin-top: 80px;">
                            <a href="<?php echo site_url('pages/procurement'); ?>">
                                <div class="button-new2-all text-center">
                                    <span class="font-all-home">ดูทั้งหมด</span>
                                </div>
                            </a>
                        </div>
                    </div>
                    <div id="tab4" class="tab-content">
                        <div id="myDIVRp" class="underline" style="margin-top: 40px;">
                            <div class="tab-container4 d-flex justify-content-center">
                                <div class="tab-link-rp" onclick="openTabRp('tabRp1')">
                                    <div class="rp-button active-rp" style="border-radius: 20px 0px 0px 0px;">
                                        <span class="font-new-pm">รายงานใช้จ่ายงบประมาณ</span>
                                    </div>
                                </div>
                                <div class="tab-link-rp" onclick="openTabRp('tabRp2')">
                                    <div class="rp-button" style="border-radius: 0px 20px 0px 0px;">
                                        <span class="font-new-pm">รายงานผลการดำเนินงาน</span>
                                    </div>
                                </div>
                            </div>
                            <br>
                            <div class="d-flex justify-content-center">
                                <div id="tabRp1" class="tab-content-rp">
                                    <div class="news-dla-prov3">
                                        <?php
                                        $totalCount = count($qP_reb);
                                        $currentIndex = 0;
                                        foreach ($qP_reb as $anou) {
                                            $currentIndex++;
                                            ?>
                                            <a href="<?php echo site_url('Pages/p_reb_detail/' . $anou->p_reb_id); ?>">
                                                <div class="row mt-1">
                                                    <div class="col-3" style="padding-left: 35px;">
                                                        <span class="text-news-time"><img
                                                                src="docs/icon-news.png">&nbsp;&nbsp;
                                                            <?php
                                                            // ในการใช้งาน setThaiMonth
                                                            $date = new DateTime($anou->p_reb_date);
                                                            $day_th = $date->format('d');
                                                            $month_th = setThaiMonth($date->format('F')); // เรียกใช้ setThaiMonth สำหรับชื่อเดือน
                                                            $year_th = $date->format('Y') + 543; // เพิ่มขึ้น 543 ปี
                                                            $formattedDate = "$day_th $month_th $year_th"; // วันที่และเดือนเป็นภาษาไทย
                                                            echo $formattedDate;
                                                            ?>
                                                        </span>
                                                    </div>
                                                    <?php
                                                    // วันที่ของข่าว
                                                    $p_reb_date = new DateTime($anou->p_reb_date);

                                                    // วันที่ปัจจุบัน
                                                    $current_date = new DateTime();

                                                    // คำนวณหาความต่างของวัน
                                                    $interval = $current_date->diff($p_reb_date);
                                                    $days_difference = $interval->days;

                                                    // ถ้ามากกว่า 30 วัน ให้ซ่อนไว้
                                                    if ($days_difference > 30) {
                                                    } else {
                                                        echo '<div class="col-1" style="margin-top: 0px; margin-left: -80px;">';
                                                        echo '<div class="bt-new-dla"><span class="text-new-dla">new</span></div>';
                                                        echo '</div>';
                                                    }
                                                    ?>
                                                    <?php
                                                    // วันที่ของข่าว
                                                    $p_reb_date = new DateTime($anou->p_reb_date);

                                                    // วันที่ปัจจุบัน
                                                    $current_date = new DateTime();

                                                    // คำนวณหาความต่างของวัน
                                                    $interval = $current_date->diff($p_reb_date);
                                                    $days_difference = $interval->days;

                                                    // ถ้ามากกว่า 30 วัน ให้ซ่อนไว้
                                                    if ($days_difference > 30) {
                                                        echo '<div class="col-9" style="margin-left: -100px; padding-top: 10px;">';
                                                        echo '<span class="text-news">&nbsp;&nbsp;' . strip_tags($anou->p_reb_name) . '</span>';
                                                        echo '</div>';
                                                    } else {
                                                        echo '<div class="col-8" style="margin-left: -60px; padding-top: 10px;">';
                                                        echo '<span class="text-news">&nbsp;&nbsp;' . strip_tags($anou->p_reb_name) . '</span>';
                                                        echo '</div>';
                                                    }
                                                    ?>

                                                </div>
                                            </a>
                                            <?php if ($currentIndex < $totalCount) { ?>
                                                <div class="dla-end-pm"></div>
                                            <?php } ?>
                                        <?php } ?>
                                    </div>
                                    <div class="d-flex justify-content-center underline" style="margin-top: 80px;">
                                        <a href="<?php echo site_url('pages/p_reb'); ?>">
                                            <div class="button-new2-all text-center">
                                                <span class="font-all-home">ดูทั้งหมด</span>
                                            </div>
                                        </a>
                                    </div>
                                </div>
                                <div id="tabRp2" class="tab-content-rp">
                                    <div class="news-dla-prov3">
                                        <?php
                                        $totalItems = count($qP_rpo);
                                        $currentIndex = 0;
                                        foreach ($qP_rpo as $anou) {
                                            $currentIndex++;
                                            ?>
                                            <a href="<?php echo site_url('Pages/p_rpo_detail/' . $anou->p_rpo_id); ?>">
                                                <div class="row mt-1">
                                                    <div class="col-3" style="padding-left: 35px;">
                                                        <span class="text-news-time"><img
                                                                src="docs/icon-news.png">&nbsp;&nbsp;
                                                            <?php
                                                            // ในการใช้งาน setThaiMonth
                                                            $date = new DateTime($anou->p_rpo_date);
                                                            $day_th = $date->format('d');
                                                            $month_th = setThaiMonth($date->format('F')); // เรียกใช้ setThaiMonth สำหรับชื่อเดือน
                                                            $year_th = $date->format('Y') + 543; // เพิ่มขึ้น 543 ปี
                                                            $formattedDate = "$day_th $month_th $year_th"; // วันที่และเดือนเป็นภาษาไทย
                                                            echo $formattedDate;
                                                            ?>
                                                        </span>
                                                    </div>
                                                    <?php
                                                    // วันที่ของข่าว
                                                    $p_rpo_date = new DateTime($anou->p_rpo_date);

                                                    // วันที่ปัจจุบัน
                                                    $current_date = new DateTime();

                                                    // คำนวณหาความต่างของวัน
                                                    $interval = $current_date->diff($p_rpo_date);
                                                    $days_difference = $interval->days;

                                                    // ถ้ามากกว่า 30 วัน ให้ซ่อนไว้
                                                    if ($days_difference > 30) {
                                                    } else {
                                                        echo '<div class="col-1" style="margin-top: 0px; margin-left: -80px;">';
                                                        echo '<div class="bt-new-dla"><span class="text-new-dla">new</span></div>';
                                                        echo '</div>';
                                                    }
                                                    ?>
                                                    <?php
                                                    // วันที่ของข่าว
                                                    $p_rpo_date = new DateTime($anou->p_rpo_date);

                                                    // วันที่ปัจจุบัน
                                                    $current_date = new DateTime();

                                                    // คำนวณหาความต่างของวัน
                                                    $interval = $current_date->diff($p_rpo_date);
                                                    $days_difference = $interval->days;

                                                    // ถ้ามากกว่า 30 วัน ให้ซ่อนไว้
                                                    if ($days_difference > 30) {
                                                        echo '<div class="col-9" style="margin-left: -100px; padding-top: 10px;">';
                                                        echo '<span class="text-news">&nbsp;&nbsp;' . strip_tags($anou->p_rpo_name) . '</span>';
                                                        echo '</div>';
                                                    } else {
                                                        echo '<div class="col-8" style="margin-left: -60px; padding-top: 10px;">';
                                                        echo '<span class="text-news">&nbsp;&nbsp;' . strip_tags($anou->p_rpo_name) . '</span>';
                                                        echo '</div>';
                                                    }
                                                    ?>
                                                </div>
                                            </a>
                                            <?php if ($currentIndex < $totalItems) { ?>
                                                <div class="dla-end-pm"></div>
                                            <?php } ?>
                                        <?php } ?>
                                    </div>
                                    <div class="d-flex justify-content-center underline" style="margin-top: 80px;">
                                        <a href="<?php echo site_url('pages/p_rpo'); ?>">
                                            <div class="button-new2-all text-center">
                                                <span class="font-all-home">ดูทั้งหมด</span>
                                            </div>
                                        </a>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="bg-news-dla">
    <img class="cloud-animation cloud-cartoon-animation-1" src="<?php echo base_url('docs/cloud 15.png'); ?>">
    <img class="cloud-animation cloud-cartoon-animation-3" src="<?php echo base_url('docs/cloud-new.png'); ?>">

    <div id="myDIV3" class="underline" style="padding-top: 1%; position: relative; z-index: 5;">
        <div class="tab-container3 d-flex justify-content-center">
            <div class="tab-link-dla" onclick="openTabDla('tabDla1')">
                <div class="dla-button active-dla">
                    <span class="font-new-button">สถ.จ.<?php echo get_config_value('province'); ?></span>
                </div>
            </div>
            <div class="tab-link-dla" onclick="openTabDla('tabDla2')">
                <div class="dla-button">
                    <span class="font-new-button">หนังสือ สถ.</span>
                </div>
            </div>
        </div>
        <br>
        <div class="d-flex justify-content-center">
            <div id="tabDla1" class="tab-content-dla">
                <div class="news-dla-prov2">
                    <?php if (!empty($prov_local_doc)): ?>
                        <?php
                        $totalDocuments = count($prov_local_doc);
                        foreach ($prov_local_doc as $index => $rs):
                            // ตรวจสอบว่ามี doc_no หรือไม่
                            $has_doc_no = !empty($rs['doc_no']);

                            // ตรวจสอบว่ามีค่าความเร่งด่วนหรือไม่
                            $has_urgency = (strpos($rs['topic'], '[ด่วนที่สุด]') !== false) ||
                                (strpos($rs['topic'], '[ด่วนมาก]') !== false) ||
                                (strpos($rs['topic'], '[ทั่วไป]') !== false);

                            // กำหนดขนาด col ของ topic
                            if (!$has_doc_no && !$has_urgency) {
                                $topic_col = 'col-9'; // ไม่มีทั้ง doc_no และความเร่งด่วน
                            } elseif (!$has_doc_no || !$has_urgency) {
                                $topic_col = 'col-7'; // ไม่มีอย่างใดอย่างหนึ่ง
                            } else {
                                $topic_col = 'col-5'; // มีครบทั้งสองอย่าง
                            }
                        ?>
                            <div class="row mt-2 underline" style="padding-left: 20px;">

                                <!-- แสดง doc_no เฉพาะตอนมีค่า -->
                                <?php if ($has_doc_no): ?>
                                    <div class="col-2">
                                        <span class="font-dla-1 line-ellipsis-dla1">
                                            <img src="docs/icon-news.png">&nbsp;&nbsp;
                                            <?php echo $rs['doc_no']; ?>
                                        </span>
                                    </div>
                                <?php endif; ?>

                                <!-- Topic - ขนาดปรับตามเงื่อนไข -->
                                <div class="<?php echo $topic_col; ?>" style="padding-left: 50px;">
                                    <?php
                                    // หา URL ที่มีค่า
                                    $url = $rs['url'] ?? $rs['url1'] ?? $rs['url01'] ?? $rs['link'] ?? '';
                                    ?>

                                    <?php if (!empty($url)): ?>
                                        <a href="<?php echo $url; ?>" target="_blank" rel="noopener noreferrer">
                                            <span class="font-dla-2 line-ellipsis-dla-prov2-new"><?php echo $rs['topic']; ?></span>
                                        </a>
                                    <?php else: ?>
                                        <span class="font-dla-2 line-ellipsis-dla-prov2-new"><?php echo $rs['topic']; ?></span>
                                    <?php endif; ?>
                                </div>

                                <!-- แสดงความเร่งด่วนเฉพาะตอนมีค่า -->
                                <?php if ($has_urgency): ?>
                                    <div class="col-2 text-center" style="padding-left: 90px;">
                                        <?php
                                        // แสดงส่วนของ "ด่วนที่สุด" หรือ "ทั่วไป" ตามที่ต้องการ
                                        if (strpos($rs['topic'], '[ด่วนที่สุด]') !== false) {
                                            echo '<span class="font-dla-2 most_urgent">ด่วนที่สุด</span>';
                                        } elseif (strpos($rs['topic'], '[ด่วนมาก]') !== false) {
                                            echo '<span class="font-dla-2 very_urgent">ด่วนมาก</span>';
                                        } elseif (strpos($rs['topic'], '[ทั่วไป]') !== false) {
                                            echo '<span class="font-dla-2 green-color">ทั่วไป</span>';
                                        }
                                        ?>
                                    </div>
                                <?php endif; ?>

                                <!-- New Badge -->
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

                                <!-- วันที่ -->
                                <div class="col-2">
                                    <span class="font-all-dla" style="padding-left: 0px;">
                                        <?php
                                        $docDate = $rs['doc_date'];
                                        $docDateParts = explode(' ', $docDate);
                                        $monthAbbreviation = $docDateParts[1];
                                        $longMonth = setMonthAbbreviationToLong($monthAbbreviation);
                                        echo $docDateParts[0] . ' ' . $longMonth . ' ' . $docDateParts[2];
                                        ?>
                                    </span>
                                </div>
                            </div>

                            <?php if ($index < $totalDocuments - 1): ?>
                                <div class="dla-end"></div>
                            <?php endif; ?>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </div>

                <div class="d-flex justify-content-end"
                    style="margin-top: 10px; margin-left: 0px; color: #707070; font-size: 13px;">
                    <span>หมายเหตุ อ้างอิงแหล่งที่มาจาก กรมส่งเสริมการปกครองส่วนท้องถิ่น
                        <?php if (!empty($province_links['POLA'])): ?>
                            <a href="<?= $province_links['POLA'] ?>" target="_blank">
                                <span style="color: #707070;">(<?= $province_links['POLA'] ?>)</span>
                            </a>
                        <?php endif; ?>
                    </span>
                </div>

                <div class="d-flex justify-content-center underline" style="margin-top: 80px;">
                    <?php if (!empty($prov_base_url)): ?>
                        <a href="<?php echo $prov_base_url; ?>" target="_blank" rel="noopener noreferrer">
                        <?php else: ?>
                            <a href="<?php echo site_url('pages/prov_local_doc'); ?>">
                            <?php endif; ?>
                            <div class="button-new2-all text-center">
                                <span class="font-all-home">ดูทั้งหมด</span>
                            </div>
                            </a>
                </div>
            </div>
            <div id="tabDla2" class="tab-content-dla">
                <div class="news-dla-prov2">
                    <?php if (!empty($rssData)): ?>
                        <?php
                        $documentsToShow = array_slice($rssData, 0, 13);
                        $totalDocuments = count($documentsToShow);
                        foreach ($documentsToShow as $index => $document): ?>
                            <div class="row mt-2 underline" style="padding-left: 20px;">
                                <div class="col-2">
                                    <span class="font-dla-1 line-ellipsis-dla1"><img src="docs/icon-news.png">&nbsp;&nbsp;
                                        <?php echo $document['doc_number']; ?> </span>
                                </div>
                                <div class="col-6" style="padding-left: 30px;">
                                    <span class="font-dla-2 line-ellipsis-dla2"><?php echo $document['topic']; ?></span>
                                </div>
                                <div class="col-1" style="padding-left: 95px;">
                                    <?php
                                    $dateStr = $document['date'];
                                    list($day, $month, $year) = explode('/', $dateStr);
                                    $year = $year - 543;
                                    $formattedDate = "$year-$month-$day";
                                    $date = DateTime::createFromFormat('Y-m-d', $formattedDate);

                                    if ($date !== false) {
                                        $currentDate = new DateTime();
                                        $interval = $currentDate->diff($date);

                                        if ($interval->days <= 7) {
                                            echo '<div class="bt-new-dla"><span class="text-new-dla">new</span></div>';
                                        }
                                    }
                                    ?>
                                </div>
                                <div class="col-3" style="padding-left: 70px;">
                                    <?php
                                    $date = DateTime::createFromFormat('d/m/Y', $document['date']);
                                    $thaiDay = $date->format('d');
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
                                    $year = $date->format('Y');
                                    ?>
                                    <span class="font-all-dla" style="padding-left: 20px;">
                                        <?php echo $thaiDay; ?>
                                        <?php echo $thaiMonth; ?>
                                        <?php echo $year; ?>
                                    </span>
                                </div>
                            </div>
                            <?php if ($index < $totalDocuments - 1): ?>
                                <div class="dla-end"></div>
                            <?php endif; ?>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </div>
                <div class="d-flex justify-content-end"
                    style="margin-top: 10px; margin-left: 0px; color: #707070; font-size: 13px; position: relative; z-index: 5;">
                    <span>หมายเหตุ อ้างอิงแหล่งที่มาจาก กรมส่งเสริมการปกครองส่วนท้องถิ่น
                        <a href="https://www.dla.go.th/" target="_blank">
                            <span style="color: #707070;">(https://www.dla.go.th/)</span>
                        </a>
                    </span>
                </div>
                <div class="d-flex justify-content-center underline" style="margin-top: 80px;">
                    <a href="<?php echo $dla_links[9] ?? '#'; ?>" target="_blank">
                        <div class="button-new2-all text-center">
                            <span class="font-all-home">ดูทั้งหมด</span>
                        </div>
                    </a>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="position-2bg-travel">
    <div class="bg-travel" id="travel">
        <div class="bg-travel-1"></div>
        <div class="fadeTopdDown" style="position: absolute; z-index: 3; top: 135px; left: 706px;">
            <img src="<?php echo base_url("docs/head-travel.png"); ?>">
        </div>

        <img class="animation-wind-L animation-wind-12" src="<?php echo base_url('docs/animation_leaf1.png'); ?>">
        <img class="animation-wind-R animation-wind-13" src="<?php echo base_url('docs/animation_leaf2s.png'); ?>">

        <img class="cloud-animation cloud-cartoon-animation-1"
            src="<?php echo base_url('docs/animation_cloud2.png'); ?>">
        <img class="cloud-animation cloud-cartoon-animation-2"
            src="<?php echo base_url('docs/animation_cloud1.png'); ?>">
        <img class="cloud-animation cloud-cartoon-animation-3"
            src="<?php echo base_url('docs/animation_cloud1.png'); ?>">
        <img class="cloud-animation cloud-cartoon-animation-4"
            src="<?php echo base_url('docs/animation_cloud2.png'); ?>">

        <div style="position: absolute; z-index: 2; top: 437px; left: 306px;">
            <img src="<?php echo base_url("docs/bird.gif"); ?>">
        </div>

        <a href="<?php echo site_url('Pages/travel_detail/1'); ?>">
            <div class="image-container pin-delay-0 text-center"
                style="position: absolute; z-index: 5; top: 675px; left: 180px;">
                <img src="<?php echo base_url('docs/pin1.png'); ?>">
                <div class="rectangle-travel" style="width: 277px; height: 65px; margin-top: 20px;">
                    <span class="font-travel">ตลาดนัดชุมชน<br>เคหะฉะเชิงเทรา</span>
                </div>
            </div>
        </a>
        <a href="<?php echo site_url('Pages/travel_detail/2'); ?>">
            <div class="image-container pin-delay-1 text-center"
                style="position: absolute; z-index: 5; top: 610px; left: 383px;">
                <img src="<?php echo base_url('docs/pin1.png'); ?>">
                <div class="rectangle-travel" style="width: 277px; height: 44px; margin-top: 20px;">
                    <span class="font-travel">ห้างFN</span>
                </div>
            </div>
        </a>
        <a href="<?php echo site_url('Pages/travel_detail/3'); ?>">
            <div class="image-container pin-delay-2 text-center"
                style="position: absolute; z-index: 5; top: 516px; left: 567px;">
                <img src="<?php echo base_url('docs/pin1.png'); ?>">
                <div class="rectangle-travel" style="width: 277px; height: 44px; margin-top: 20px;">
                    <span class="font-travel">ร้านกาแฟสตาร์บัค</span>
                </div>
            </div>
        </a>
        <a href="<?php echo site_url('Pages/travel_detail/4'); ?>">
            <div class="image-container pin-delay-3 text-center"
                style="position: absolute; z-index: 5; top: 678px; left: 715px;">
                <img src="<?php echo base_url('docs/pin1.png'); ?>">
                <div class="rectangle-travel" style="width: 277px; height: 44px; margin-top: 20px;">
                    <span class="font-travel">ตลาดดีเลิศ</span>
                </div>
            </div>
        </a>
        <a href="<?php echo site_url('Pages/travel_detail/5'); ?>">
            <div class="image-container pin-delay-4 text-center"
                style="position: absolute; z-index: 5; top: 592px; right: 656px;">
                <img src="<?php echo base_url('docs/pin1.png'); ?>">
                <div class="rectangle-travel" style="width: 277px; height: 44px; margin-top: 20px;">
                    <span class="font-travel">Bergerking</span>
                </div>
            </div>
        </a>
        <a href="<?php echo site_url('Pages/travel_detail/6'); ?>">
            <div class="image-container pin-delay-5 text-center"
                style="position: absolute; z-index: 5; top: 685px; right: 391px;">
                <img src="<?php echo base_url('docs/pin1.png'); ?>">
                <div class="rectangle-travel" style="width: 277px; height: 44px; margin-top: 20px;">
                    <span class="font-travel">วัดหัวเนิน</span>
                </div>
            </div>
        </a>
        <a href="<?php echo site_url('Pages/travel_detail/7'); ?>">
            <div class="image-container pin-delay-6 text-center"
                style="position: absolute; z-index: 5; top: 632px; right: 128px;">
                <img src="<?php echo base_url('docs/pin1.png'); ?>">
                <div class="rectangle-travel" style="width: 277px; height: 44px; margin-top: 20px;">
                    <span class="font-travel">วัดลาดขวาง</span>
                </div>
            </div>
        </a>

    </div>

    <div class="bg-travel-2">
        <img class="water-animation2" src="<?php echo base_url('docs/animation-water.png'); ?>">

        <div class="bg-travel-2-2"></div>

        <img class="boat-animation-L boat-animation-2" src="<?php echo base_url('docs/animation-ship.png'); ?>">

        <div class="container-fish-LRs">
            <div class="fish-animation-Ls">
                <img class="dynamic-fish-animation" src="<?php echo base_url('docs/animation-fish1.png'); ?>"
                    alt="Fish">
                <img class="static-fish-animation" src="<?php echo base_url('docs/animation-fish1.png'); ?>" alt="Fish">
            </div>
            <div class="fish-animation-Ls2">
                <img class="dynamic-fish-animation" src="<?php echo base_url('docs/animation-fish1.png'); ?>"
                    alt="Fish">
                <img class="static-fish-animation" src="<?php echo base_url('docs/animation-fish1.png'); ?>" alt="Fish">
            </div>
            <div class="fish-animation-Ls3">
                <img class="dynamic-fish-animation" src="<?php echo base_url('docs/animation-fish1.png'); ?>"
                    alt="Fish">
                <img class="static-fish-animation" src="<?php echo base_url('docs/animation-fish1.png'); ?>" alt="Fish">
            </div>
            <div class="fish-animation-Rs">
                <img class="dynamic-fish-animation2" src="<?php echo base_url('docs/animation-fish2.png'); ?>"
                    alt="Fish">
                <img class="static-fish-animation2" src="<?php echo base_url('docs/animation-fish2.png'); ?>"
                    alt="Fish">
            </div>
            <div class="fish-animation-Rs2">
                <img class="dynamic-fish-animation2" src="<?php echo base_url('docs/animation-fish2.png'); ?>"
                    alt="Fish">
                <img class="static-fish-animation2" src="<?php echo base_url('docs/animation-fish2.png'); ?>"
                    alt="Fish">
            </div>

        </div>
    </div>
</div>

<div class="bg-service" id="oss">
    <div class="d-flex justify-content-center" style="padding-top: 30px; position: relative; z-index: 5;">
        <span class="crop-es font-header-service text-center">One Stop e-Service</span>
    </div>
    <div class="text-center text-run-btm-eservice" style="margin-top: 26px; position: relative; z-index: 5;">
        <div class="row">
            <div class="col-1" style="margin-top: -5px;">&nbsp;&nbsp;&nbsp;<img
                    src="<?php echo base_url("docs/icon-notify-bell.png"); ?>" width="43" height="43"></div>
            <div class="col-11">
                <marquee>
                    <span class="font-header-service-line">บริการ สะดวก รวดเร็ว เมื่อแจ้งเรื่องผ่าน e-Service โดยมี Line
                        Notify แจ้งเรื่องไปถึงผู้ดูเเลโดยตรง </span>
                </marquee>
            </div>
        </div>
    </div>
    <div class="service-content underline"
        style="padding-top: 72px; margin-left: 140px; position: relative; z-index: 5;">
        <div class="row">
            <div class="col-5">
                <div class="bg-qa" style="margin-left: 25px;">
                    <div style="padding-top: 15px; padding-left: 20px;">
                        <span class="font-header-qa">กระทู้ ถาม ตอบ</span>
                    </div>
                    <?php foreach ($qQ_a as $rs) { ?>
                        <div class="bg-content-qa-list mt-2">
                            <a href="<?php echo site_url('Pages/q_a/' . '#comment-' . $rs->q_a_id); ?>">
                                <div class="row">
                                    <div class="col-9 one-line-ellipsis" style="padding-top: 7px;">
                                        <span class="font-qa-list-content ">
                                            <?= $rs->q_a_msg; ?>
                                        </span>
                                    </div>
                                    <div class="col-3 one-line-ellipsis" style="padding-top: 8px;">
                                        <span class="font-qa-list-content-name">ผู้ตั้งกระทู้ :
                                            <?= $rs->q_a_by; ?>
                                        </span>
                                    </div>
                                </div>
                            </a>
                        </div>
                    <?php } ?>
                    <div class="d-flex justify-content-end" style="padding-top: 60px; margin-right: 30px;">
                        <div class="mx-4">
                            <a href="<?php echo site_url('pages/adding_q_a'); ?>">
                                <div class="bt-qa-add text-center" style="padding: 5px">
                                    <span class="font-bt-qa">เพิ่มกระทู้</span>
                                </div>
                            </a>
                        </div>
                        <a href="<?php echo site_url('pages/q_a'); ?>">
                            <div class="bt-qa-all text-center" style="padding: 5px">
                                <span class="font-bt-qa">ดูทั้งหมด</span>
                            </div>
                        </a>
                    </div>
                </div>
                <div class="container-video-as">
                    <div class="video-service"
                        style="position: relative; z-index: 3; margin-top: 34px; margin-left: 60px;">
                        <?php if (!empty($manual_esv) && $manual_esv->manual_esv_link != ""): ?>
                            <?php
                            // Check if it's a YouTube link
                            if (preg_match("/youtu\.be\/|youtube\.com\/watch|youtube\.com\/shorts/", $manual_esv->manual_esv_link)):
                                if (preg_match("/youtu\.be\/([a-zA-Z0-9_-]+)/", $manual_esv->manual_esv_link, $matches)) {
                                    $video_id = $matches[1];
                                } elseif (preg_match("/youtube\.com\/watch\?v=([a-zA-Z0-9_-]+)/", $manual_esv->manual_esv_link, $matches)) {
                                    $video_id = $matches[1];
                                } elseif (preg_match("/youtube\.com\/shorts\/([a-zA-Z0-9_-]+)/", $manual_esv->manual_esv_link, $matches)) {
                                    $video_id = $matches[1];
                                }
                                if (!empty($video_id)): ?>
                                    <iframe class="video-iframe" width="420" height="239"
                                        src="https://www.youtube-nocookie.com/embed/<?= $video_id; ?>" title="YouTube video player"
                                        frameborder="0"
                                        allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture; web-share"
                                        referrerpolicy="strict-origin-when-cross-origin" allowfullscreen
                                        style="border-radius: 16px;"></iframe>
                                <?php endif; ?>
                                <?php
                                // Check if it's a Facebook video or reel link
                            elseif (preg_match("/facebook\.com\/(?:watch\?v=|.*\/videos\/|reel\/)([0-9]+)/", $manual_esv->manual_esv_link, $matches)):
                                $fb_video_id = $matches[1] ?? '';
                                if (!empty($fb_video_id)): ?>
                                    <iframe
                                        src="https://www.facebook.com/plugins/video.php?href=https://www.facebook.com/watch?v=<?= $fb_video_id; ?>"
                                        width="420" height="239" style="border:none;overflow:hidden;border-radius:16px;"
                                        scrolling="no" frameborder="0" allowTransparency="true" allow="encrypted-media"
                                        allowfullscreen></iframe>
                                <?php endif; ?>
                            <?php endif; ?>
                        <?php endif; ?>
                    </div>
                    <a href="<?php echo site_url('Assessment'); ?>">
                        <div class="bg-like">
                            <div class="text-center" style="padding-left: 0px; padding-top: 5px;">
                                <span class="font-like">แบบประเมินความพึงพอใจ<br>การให้บริการ</span>
                            </div>
                        </div>
                    </a>

                </div>
            </div>
            <div class="col-7">
                <div class="row">
                    <div class="col-6">
                        <div class="bg-facebook-new">
                            <iframe
                                src="https://www.facebook.com/plugins/page.php?href=https%3A%2F%2Fwww.facebook.com%2FLadkhwanglocal%2F&tabs=timeline&width=291&height=555&small_header=false&adapt_container_width=true&hide_cover=false&show_facepile=true&appId"
                                width="291" height="555" style="border-radius: 0px 0px 24px 24px;" scrolling="no"
                                frameborder="0" allowfullscreen="true"
                                allow="autoplay; clipboard-write; encrypted-media; picture-in-picture; web-share"></iframe>
                        </div>
                    </div>
                    <div class="col-6" style="padding-top: 20px;">
                        <a href="<?php echo site_url('Pages/adding_complain'); ?>">
                            <div class="button-e-service2 e-service-pad">
                                <span class="font-button-e-service">แจ้งเรื่อง ร้องเรียน</span>
                            </div>
                        </a>
						<!-- <a href="<?php echo site_url('Pages/adding_complain/hr?cat_id=13'); ?>">
   							 <div class="button-e-service2 e-service-pad">
        						<span class="font-button-e-service">แจ้งเรื่องร้องเรียน ด้านทรัพยากรส่วนบุคคล</span>
    						</div>
						</a> -->
                        <a href="<?php echo site_url('Corruption/report_form'); ?>">
                            <div class="button-e-service8 e-service-pad" style="padding-left: 15px;">
                                <span class="font-button-e-service">แจ้งเรื่องทุจริต</span>
                            </div>
                        </a>
                        <a href="<?php echo site_url('Suggestions/adding_suggestions'); ?>">
                            <div class="button-e-service3 e-service-pad">
                                <span class="font-button-e-service">รับฟังความคิดเห็น</span>
                            </div>
                        </a>
                        <a href="<?php echo site_url('Kid_aw_ods'); ?>">
                            <div class="button-e-service4 e-service-pad" style="padding-left: 30px;">
                                <span class="font-button-e-service">เด็กแรกเกิด</span>
                            </div>
                        </a>
                        <a href="<?php echo site_url('Elderly_aw_ods/adding_elderly_aw_ods'); ?>">
                            <div class="button-e-service5 e-service-pad" style="padding-left: 10px;">
                                <span class="font-button-e-service">ผู้สูงอายุ/ผู้พิการ</span>
                            </div>
                        </a>
                        <a href="<?php echo site_url('Esv_ods/forms_online'); ?>">
                            <div class="button-e-service6 e-service-pad" style="padding-left: 30px;">
                                <span class="font-button-e-service">แบบฟอร์ม</span>
                            </div>
                        </a>
                        <a href="<?php echo site_url('Esv_ods/submit_document'); ?>">
                            <div class="button-e-service7 e-service-pad">
                                <span class="font-button-e-service">ยื่นเอกสารออนไลน์</span>
                            </div>
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="bg-e-magazine">
    <div class="bg-E-book" id="ebookSection">
        <div class="text-start text-ebook">e-book<br>วารสารออนไลน์</div>
        <div class="ebook-prev-btn" id="ebookPrevBtn"></div>
        <div class="ebook-next-btn" id="ebookNextBtn"></div>
        <div class="ebook-container">
            <div class="ebook-slider" id="ebookSlider">
                <?php foreach ($qE_mag as $rs) { ?>
                    <div class="_df_thumb" source="<?php echo base_url('Home/serve_pdf/' . $rs->file_name); ?>"
                        tags="ebook,pdf" thumb="<?php echo base_url('Home/serve_image/' . $rs->cover_image); ?>"
                        title="<?php echo htmlspecialchars($rs->original_name); ?>"
                        onerror="this.setAttribute('thumb', '<?php echo base_url('assets/images/default_cover.png'); ?>')">
                        <?php echo htmlspecialchars($rs->original_name); ?>
                    </div>
                <?php } ?>
            </div>
            <script>
                document.addEventListener("DOMContentLoaded", function () {
                    // รอฟังก์ชัน close ของ dFlip
                    document.addEventListener("click", function (e) {
                        if (e.target.classList.contains("df-lightbox-close")) {
                            // scroll ไปยัง element id="ebookSection"
                            const target = document.getElementById("ebookSection");
                            if (target) {
                                target.scrollIntoView({
                                    behavior: "smooth"
                                });
                            }
                        }
                    });
                });
            </script>
        </div>
    </div>
    <div class="d-flex justify-content-center underline" style="margin-top: 0px;">
        <a href="<?php echo site_url('pages/e_mags_view'); ?>">
            <div class="button-activity-all text-center">
                <span class="font-all-home">ดูทั้งหมด</span>
            </div>
        </a>
    </div>
</div>

<div class="bg-statistics">
    <div class="crop-statistics">สถิติการเข้าชม</div>
    <div class="bg-statistics-2">
        <a href="https://webanalytics.assystem.co.th/stats/<?php echo get_config_value('analytics_key'); ?>"
            target="_blank" style="text-decoration: none;">

            <div class="statistics-buttons-container">
                <div class="btn-statistics1">
                    <div class="text-statis-number" id="online">00.00</div>
                    <div class="text-statis">ออนไลน์</div>
                </div>
                <div class="btn-statistics2">
                    <div class="text-statis-number" id="today_visitors">00.00</div>
                    <div class="text-statis">วันนี้</div>
                </div>
                <div class="btn-statistics3">
                    <div class="text-statis-number" id="weekly_visitors">00.00</div>
                    <div class="text-statis">สัปดาห์นี้</div>
                </div>
                <div class="btn-statistics4">
                    <div class="text-statis-number" id="monthly_visitors">00.00</div>
                    <div class="text-statis">เดือนนี้</div>
                </div>
                <div class="btn-statistics5">
                    <div class="text-statis-number" id="total_visitors">00.00</div>
                    <div class="text-statis">ทั้งหมด</div>
                </div>
            </div>
        </a>

        <script>
            // ฟังก์ชันแปลงตัวเลขให้แสดงแบบย่อ
            function formatNumber(num) {
                if (num >= 1000000000) {
                    return (num / 1000000000).toFixed(1) + 'b';
                }
                if (num >= 1000000) {
                    return (num / 1000000).toFixed(1) + 'm';
                }
                if (num >= 1000) {
                    return (num / 1000).toFixed(1) + 'k';
                }
                return num.toString();
            }

            var access_key = '<?php echo get_config_value('analytics_key'); ?>';
            var xhr = new XMLHttpRequest();

            xhr.onreadystatechange = function () {
                if (xhr.readyState === 4 && xhr.status === 200) {
                    var data = JSON.parse(xhr.responseText);

                    // แสดงข้อมูลทั้งหมดที่ได้จาก API
                    console.log('=== ข้อมูลทั้งหมดจาก API ===');
                    console.log('Raw Data:', data);
                    console.log('---------------------------');

                    // แสดงทุก key และ value
                    Object.keys(data).forEach(function (key) {
                        console.log(key + ':', data[key]);
                    });

                    console.log('---------------------------');

                    // อัพเดทข้อมูลแต่ละ ID ตาม class ใหม่ พร้อมแปลงเป็นรูปแบบย่อ
                    document.getElementById('today_visitors').textContent = formatNumber(data.today_visitors || data.today_pageviews || 0);
                    document.getElementById('weekly_visitors').textContent = formatNumber(data.weekly_visitors || 0);
                    document.getElementById('total_visitors').textContent = formatNumber(data.total_visitors || data.total_pageviews || 0);
                    document.getElementById('monthly_visitors').textContent = formatNumber(data.monthly_visitors || data.month_visitors || 0);
                    document.getElementById('online').textContent = formatNumber(data.online_visitors || data.online || 0);
                }
            };

            xhr.open('GET', 'https://webanalytics.assystem.co.th/api/stats_by_key?key=' + access_key, true);
            xhr.send();
        </script>
    </div>
</div>

<div class="bg-link-dla">
    <div style="position: relative; z-index: 5;">
        <div class="text-center">
            <img src="docs/dla_logo.png" alt="dla_logo"><br>
            <span class="font-link-dla">ระบบบริหารจัดการกรมส่งเสริมการปกครองท้องถิ่น</span>
        </div>

        <div class="link-dla-container d-flex justify-content-center underline" style="margin-top: 20px;">
            <a href="<?php echo $dla_links[1] ?? '#'; ?>" target="_blank">
                <div class="button-link-dla text-center" style="padding-top: 25px !important; padding-left: 30px;">
                    <span class="font-link-dla-detail">e-LAAS</span>
                </div>
            </a>
            <a href="<?php echo $dla_links[2] ?? '#'; ?>" target="_blank">
                <div class="button-link-dla text-center" style="padding-top: 25px !important; padding-left: 30px;">
                    <span class="font-link-dla-detail">e-Plan</span>
                </div>
            </a>
            <a href="<?php echo $dla_links[3] ?? '#'; ?>" target="_blank">
                <div class="button-link-dla">
                    <div class="row">
                        <div class="col-3 six-menu-left"></div>
                        <div class="col-9 text-center " style="margin-top: 10px !important; margin-left: -10px;">
                            <span class="font-link-dla-detail">ระบบข้อมูลบุคลากร <br>LHR</span>
                        </div>
                    </div>
                </div>
            </a>
            <a href="<?php echo $dla_links[4] ?? '#'; ?>" target="_blank">
                <div class="button-link-dla">
                    <div class="row">
                        <div class="col-3 six-menu-left"></div>
                        <div class="col-9 text-center " style="padding-top: 10px !important; margin-left: -20px;">
                            <span class="font-link-dla-detail">ระบบข้อมูลกลาง<br>อปท.</span>
                        </div>
                    </div>
                </div>
            </a>
        </div>

        <div class="link-dla-container d-flex justify-content-center underline">
            <a href="<?php echo $dla_links[5] ?? '#'; ?>" target="_blank">
                <div class="button-link-dla text-center" style="padding-top: 25px !important; padding-left: 30px;">
                    <span class="font-link-dla-detail">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;One Stop
                        Service</span>
                </div>
            </a>
            <a href="<?php echo $dla_links[6] ?? '#'; ?>" target="_blank">
                <div class="button-link-dla">
                    <div class="row">
                        <div class="col-3 six-menu-left"></div>
                        <div class="col-9 text-center " style="padding-top: 10px !important; margin-left: -5px;">
                            <span class="font-link-dla-detail">ฐานข้อมูลเบี้ยยังชีพ<br>Welfare</span>
                        </div>
                    </div>
                </div>
            </a>
            <a href="<?php echo $dla_links[7] ?? '#'; ?>" target="_blank">
                <div class="button-link-dla">
                    <div class="row">
                        <div class="col-3 six-menu-left"></div>
                        <div class="col-9 text-center " style="padding-top: 10px !important; margin-left: -10px;">
                            <span class="font-link-dla-detail">ระบบสารสนเทศ<br>LEC</span>
                        </div>
                    </div>
                </div>
            </a>
            <a href="<?php echo $dla_links[8] ?? '#'; ?>" target="_blank">
                <div class="button-link-dla">
                    <div class="row">
                        <div class="col-3 six-menu-left"></div>
                        <div class="col-9 text-center " style="padding-top: 10px !important; margin-left: -10px;">
                            <span class="font-link-dla-detail">ศูนย์ข้อมูลเลือกตั้ง<br>NLC</span>
                        </div>
                    </div>
                </div>
            </a>
        </div>
    </div>
</div>