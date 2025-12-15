<!DOCTYPE html>
<html lang="en">

<head>
    <!-- Design by foolishdeveloper.com -->
    <link rel="icon" href="<?php echo base_url("docs/logo.png"); ?>" type="image/x-icon">
    <!-- เปลี่ยน titel หนูด้วย -->
    <title><?php echo get_config_value('fname'); ?></title>
    <!-- icon -->
    <link href='https://fonts.googleapis.com/css?family=Kanit' rel='stylesheet'>
    <link rel="preconnect" href="https://fonts.gstatic.com">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;500;600&display=swap" rel="stylesheet">
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-T3c6CoIi6uLrA9TneNEoa7RxnatzjcDSCmG1MXxSR1GAsXEV/Dwwykc2MPK8M2HN" crossorigin="anonymous">
    <!--Stylesheet-->
    <style media="screen">
        *,
        *:before,
        *:after {
            padding: 0;
            margin: 0;
            box-sizing: border-box;
        }

        body {
            margin: 0px;
            padding: 0px;
            font-family: 'Kanit', sans-serif;
            font-weight: 300;
            background-color: #080710;

        }

        .background {
            width: 430px;
            height: 520px;
            position: absolute;
            transform: translate(-50%, -50%);
            left: 50%;
            top: 50%;
        }

        .bg {
            /* The image used */
            background-image: url('<?php echo base_url("docs/bg-login.png"); ?>');
            /* width: 1920px; */
            /* height: 1000px; */
            /* Full height */
            height: 100vh;
            width: 100vw;
            /* Center the image horizontally and vertically */
            background-position: center center;
            /* Do not repeat the background image */
            background-repeat: no-repeat;
            /* Cover the entire div with the background image */
            background-size: cover;
        }

        .bg-content {
            width: 1075px;
            height: 383px;
            flex-shrink: 0;
            border-radius: 20px;
            background: rgba(255, 255, 255, 0.50);
            /* background-color: rgba(255, 255, 255, 0.13); */
            /* background-color: rgba(255, 255, 255, 0.0); */
            position: absolute;
            transform: translate(-50%, -50%);
            top: 33%;
            left: 50%;
            /* border-radius: 24px; */
            /* backdrop-filter: blur(3px);
            border: 2px solid rgba(255, 255, 255, 0.1);
            box-shadow: 0 0 40px rgba(8, 7, 16, 0.6);
            padding: 20px 10px; */
            position: absolute;
            z-index: 10;
        }

        h2 {
            color: #FFF;
            text-shadow: 2px 2px 4px rgba(0, 0, 0, 0.25);
            font-family: Kanit;
            font-style: normal;
            font-weight: 500;
            line-height: 48.167px;
            /* 185.258% */
        }

        h3 {
            color: #FFF;
            text-shadow: 2px 2px 4px rgba(0, 0, 0, 0.25);
            font-family: Kanit;
            font-style: normal;
            font-weight: 500;
            line-height: 48.167px;
            margin-top: -20px;
        }

        .logo-header {
            text-align: center;
            margin-top: -90px;
        }

        .bg-login-back-office {
            background-image: url('<?php echo base_url("docs/btn-login-back-office.png"); ?>');
            width: 312px;
            height: 196px;
        }

        .bg-login-back-office:hover {
            background-image: url('<?php echo base_url("docs/btn-login-back-office-hover.png"); ?>');
            width: 312px;
            height: 196px;
        }

        .font-back-office {
            color: #000;
            text-align: center;
            font-family: "Noto Looped Thai";
            font-size: 24px;
            font-style: normal;
            font-weight: 700;
            line-height: normal;
        }

        .bg-login-admin {
            background-image: url('<?php echo base_url("docs/btn-login-admin.png"); ?>');
            width: 312px;
            height: 196px;
        }

        .bg-login-admin:hover {
            background-image: url('<?php echo base_url("docs/btn-login-admin-hover.png"); ?>');
            width: 312px;
            height: 196px;
        }

        .font-service {
            color: #FFF;
            text-align: center;
            font-family: "Noto Looped Thai";
            font-size: 16px;
            font-style: normal;
            font-weight: 700;
            line-height: normal;
        }

        @keyframes blink {
            0% {
                opacity: 1;
            }

            50% {
                opacity: 0;
            }

            100% {
                opacity: 1;
            }
        }

        .dot-news-animation {
            position: absolute;
            animation-name: blink;
            animation-timing-function: ease-in-out;
            animation-iteration-count: infinite;
        }

        .dot-news-animation-1 {
            width: 35px;
            height: 35px;
        }

        .dot-news-animation-2 {
            width: 15px;
            height: 15px;
        }

        .dot-news-animation-3 {
            width: 25px;
            height: 25px;
        }

        .dot-news-animation-4 {
            width: 25px;
            height: 25px;
        }

        .dot-news-animation-5 {
            width: 35px;
            height: 35px;
        }

        .dot-news-animation-6 {
            width: 50px;
            height: 50px;
        }

        .dot-news-animation-7 {
            width: 20px;
            height: 20px;
        }

        .dot-news-animation-8 {
            width: 20px;
            height: 20px;
        }

        .dot-news-animation-9 {
            width: 15px;
            height: 15px;
        }

        .dot-news-animation-10 {
            width: 50px;
            height: 50px;
        }

        .dot-news-animation-11 {
            width: 15px;
            height: 15px;
        }

        .dot-news-animation-12 {
            width: 25px;
            height: 25px;
        }

        .dot-news-animation-13 {
            width: 25px;
            height: 25px;
        }

        .dot-news-animation-14 {
            width: 20px;
            height: 20px;
        }

        .dot-news-animation-15 {
            width: 20px;
            height: 20px;
        }

.slideshow-wrapper {
            display: flex;
            align-items: center;
            justify-content: center;
            position: absolute;
            width: 100%;
            top: 75%;
            /* ค่าเริ่มต้นสำหรับ desktop */
            transform: translateY(-50%);
            z-index: 2;
        }

        /* สำหรับ iPad และ tablet */
        @media screen and (max-width: 1024px) {
            .slideshow-wrapper {
                top: 65%;
            }
        }

        /* สำหรับ iPad ขนาดเล็กและมือถือขนาดใหญ่ */
        @media screen and (max-width: 768px) {
            .slideshow-wrapper {
                top: 65%;
            }
        }

        /* สำหรับมือถือทั่วไป */
        @media screen and (max-width: 480px) {
            .slideshow-wrapper {
                top: 65%;
            }
        }

        /* สำหรับมือถือขนาดเล็ก */
        @media screen and (max-width: 320px) {
            .slideshow-wrapper {
                top: 65%;
            }
        }

        .slideshow-container {
            max-width: 1480px;
            position: relative;
            z-index: 2;
            margin: 10px;
            overflow: hidden;
            display: flex;
            cursor: grab;
        }

        .slideshow-container:active {
            cursor: grabbing;
        }

        .slide-track {
            display: flex;
            transition: transform 0.5s ease;
			z-index: 9999;
        }

        .card {
            width: 226px;
            height: 350px;
            flex-shrink: 0;
            margin: 10px;
            background-color: #fff;
            border: 1px solid #ddd;
            border-radius: 8px;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
            transition: transform 0.3s ease;
            transform-origin: center center;
            z-index: 1;
        }

        .card:hover {
            transform: scale(1.05);
            z-index: 10;
        }

        .card-img-top {
            border-radius: 10px;
            background: url(<path-to-image>) lightgray 50% / cover no-repeat, #D9D9D9;
            width: 196px;
            height: 114px;
            flex-shrink: 0;
            margin: auto;
            /* padding-top: 20px; */
            margin-top: 15px;
        }

        .card-body {
            padding: 16px;
            margin-top: -5px;
        }

        .card-title {
            color: #000;
            font-family: Kanit;
            font-size: 24px;
            font-style: normal;
            font-weight: 400;
            line-height: 24px;
            text-align: center;
        }

        .card-text {
            color: #000;
            text-align: center;
            font-family: Kanit;
            font-size: 14px;
            font-style: normal;
            font-weight: 400;
            line-height: 1.5;
            /* ควบคุมความสูงของบรรทัด */
            height: 4.5em;
            /* แสดง 3 บรรทัด */
            overflow: hidden;
            text-overflow: ellipsis;
            display: -webkit-box;
            -webkit-line-clamp: 3;
            -webkit-box-orient: vertical;
        }

        .container {
            display: flex;
            justify-content: center;
            align-items: center;
            height: 100vh;
            /* ความสูงของ container */
            background-color: #f0f0f0;
            /* ตัวอย่างสีพื้นหลังของ container */
        }

        .btn {
            width: 103px;
            height: 47.955px;
            flex-shrink: 0;
            border-radius: 30px;
            background: linear-gradient(94deg, #e0e0e0 10.32%, #eaeaea 87.35%);
            display: flex;
            justify-content: center;
            align-items: center;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
            position: absolute;
            /* เพิ่ม */
            bottom: 14px;
            /* กำหนดตำแหน่งด้านล่าง */
            left: 50%;
            /* จัดกึ่งกลางแนวนอน */
            transform: translateX(-50%);
            /* จัดกึ่งกลางแนวนอน */
        }

        .btn:hover {
            background: linear-gradient(94deg, #d1d1d1 10.32%, #dcdcdc 87.35%);
            /* สีเมื่อ hover */
            box-shadow: 0 6px 12px rgba(0, 0, 0, 0.15);
            /* เงาเมื่อ hover */
        }

        .prev,
        .next {
            cursor: pointer;
            width: 55px;
            height: 76px;
            background-size: contain;
            background-repeat: no-repeat;
            transition: 0.6s ease;
            user-select: none;
            position: absolute;
            /* เปลี่ยนจาก relative เป็น absolute */
            margin-top: 80px;
            /* กำหนดให้ปุ่มอยู่ตรงกลางแนวตั้ง */
            transform: translateY(-50%);
            /* กำหนดให้ปุ่มอยู่ตรงกลางแนวตั้ง */
            z-index: 2;
        }

        .prev {
            left: 80px;
            /* กำหนดระยะห่างจากขอบซ้าย */
            background-image: url('<?php echo base_url("docs/pre-home.png"); ?>');
        }

        .prev:hover {
            background-image: url('<?php echo base_url("docs/pre-home-hover.png"); ?>');
        }

        .next {
            right: 80px;
            /* กำหนดระยะห่างจากขอบขวา */
            background-image: url('<?php echo base_url("docs/next-home.png"); ?>');
        }

        .next:hover {
            background-image: url('<?php echo base_url("docs/next-home-hover.png"); ?>');
        }

        .dot-news-animation-1 {
            animation: blink-2 4s both infinite;
            position: absolute;
            /* margin-top: 370px; */
            /* margin-left: 20px; */
            z-index: 1;
        }

        .dot-news-animation-2 {
            animation: blink-2 4s both infinite;
            position: absolute;
            /* margin-top: 720px; */
            /* margin-left: 68px; */
            z-index: 1;
        }

        .dot-news-animation-3 {
            animation: blink-2 4s both infinite;
            position: absolute;
            /* margin-top: 40px; */
            /* margin-left: 115px; */
            z-index: 1;
        }

        .dot-news-animation-4 {
            animation: blink-2 4s both infinite;
            position: absolute;
            /* margin-top: 120px; */
            /* margin-left: 300px; */
            z-index: 1;
        }

        .dot-news-animation-5 {
            animation: blink-2 4s both infinite;
            position: absolute;
            /* margin-top: 732px; */
            /* margin-left: 720px; */
            z-index: 1;
        }

        .dot-news-animation-6 {
            animation: blink-2 4s both infinite;
            position: absolute;
            /* margin-top: 423px; */
            /* margin-left: 1030px; */
            z-index: 1;
        }

        .dot-news-animation-7 {
            animation: blink-2 4s both infinite;
            position: absolute;
            /* margin-top: 305px; */
            /* margin-left: 1120px; */
            z-index: 1;
        }

        .dot-news-animation-8 {
            animation: blink-2 4s both infinite;
            position: absolute;
            /* margin-top: 717px; */
            /* margin-left: 1180px; */
            z-index: 1;
        }

        .dot-news-animation-9 {
            animation: blink-2 4s both infinite;
            position: absolute;
            /* margin-top: 745px; */
            /* margin-left: 1500px; */
        }

        .dot-news-animation-10 {
            animation: blink-2 4s both infinite;
            position: absolute;
            /* margin-top: 730px; */
            /* margin-left: 1740px; */
            z-index: 1;
        }

        .dot-news-animation-11 {
            animation: blink-2 4s both infinite;
            position: absolute;
            /* margin-top: 370px; */
            /* margin-left: 1810px; */
            z-index: 1;
        }

        .dot-news-animation-12 {
            animation: blink-2 4s both infinite;
            position: absolute;
            /* margin-top: 60px; */
            /* margin-left: 1880px; */
            z-index: 1;
        }

        .dot-news-animation-13 {
            animation: blink-2 4s both infinite;
            position: absolute;
            /* margin-top: 605px; */
            /* margin-left: 1870px; */
            z-index: 1;
        }

        .dot-news-animation-14 {
            animation: blink-2 4s both infinite;
            position: absolute;
            /* margin-top: 605px; */
            /* margin-left: 1870px; */
            z-index: 1;
        }

        .dot-news-animation-15 {
            animation: blink-2 4s both infinite;
            position: absolute;
            /* margin-top: 605px; */
            /* margin-left: 1870px; */
            z-index: 1;
        }

        /* แสงวิบวับ fade in fade out  */
        @-webkit-keyframes blink-2 {
            0% {
                opacity: 1;
            }

            50% {
                opacity: 0;
            }

            100% {
                opacity: 1;
            }
        }
    </style>
    </style>
</head>

<body>
    <div class="bg">
        <?php for ($i = 1; $i <= 15; $i++) : ?>
            <img class="wipwap dot-news-animation-<?php echo $i; ?>" src="<?php echo base_url('docs/animation-star-1.png'); ?>">
        <?php endfor; ?>
        <?php for ($i = 7; $i <= 15; $i++) : ?>
            <img class="wipwap dot-news-animation-<?php echo $i; ?>" src="<?php echo base_url('docs/animation-star-2.png'); ?>">
        <?php endfor; ?>

        <div class="bg-content">
            <div class="logo-header">
                <img src="<?php echo base_url("docs/logo.png"); ?>" width="180" height="180">
            </div>
            <div class="row">
                <div class="col-6">
                    <a href="<?php echo site_url('Login_intranet'); ?>" style="text-decoration: none;">
                        <div class="bg-login-back-office text-center" style="margin-left: 130px; padding-top: 140px;">
                            <span class="font-back-office">ระบบ Back Office</span>
                        </div>
                    </a>
                </div>
                <div class="col-6">
                    <a href="<?php echo site_url('User'); ?>" style="text-decoration: none;">
                        <div class="bg-login-admin text-center" style="margin-left: 90px; padding-top: 140px;">
                            <span class="font-back-office">ระบบแอดมิน</span>
                        </div>
                    </a>
                </div>
 <div class="text-center" style="padding-top: 45px;">
                    <span class="font-service"style="color: black;">หากมีปัญหาการใช้งานหรือต้องการติดต่อฝ่ายขาย กรุณาติดต่อผ่าน Line ที่ @assystem</span>
                </div>
            </div>
        </div>

        <div class="slideshow-wrapper">
            <div class="slideshow-container" id="slideshow-container">
                <div class="slide-track" id="slide-track">
                    <?php foreach ($api_data1 as $service) : ?>
                        <div class="card">
                            <img class="card-img-top" src="https://www.assystem.co.th/asset/img_services/<?php echo $service['service_img']; ?>" alt="Card image cap">
                            <div class="card-body">
                                <h5 class="card-title"><?php echo $service['service_title']; ?></h5>
                                <p class="card-text"><?php echo $service['service_intro']; ?></p>
                                <a href="https://www.assystem.co.th/service/detail/<?php echo $service['service_id']; ?>" target="_blank" class="btn" style="color: #080027;">เพิ่มเติม</a>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>
            </div>
            <a class="prev" onclick="plusSlides(-1)"></a>
            <a class="next" onclick="plusSlides(1)"></a>
        </div>

    </div>
</body>


<script src="https://www.google.com/recaptcha/api.js?hl=th"></script>
<!-- boostrap 5 -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.1/dist/js/bootstrap.bundle.min.js" integrity="sha384-HwwvtgBNo3bZJJLYd8oVXjrBZt8cqVSpeBNS5n7C8IVInixGAoxmnlMuBnhbgrkm" crossorigin="anonymous"></script>
<!-- เพิ่มลิงก์ไปยัง jQuery -->
<script type="text/javascript" charset="utf8" src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<!-- icon  -->
<script src="https://kit.fontawesome.com/74345a2175.js" crossorigin="anonymous"></script>
<script>
    function getRandomInt(min, max) {
        return Math.floor(Math.random() * (max - min + 1)) + min;
    }

    function randomizeAnimationDuration() {
        var minSeconds = 1; // Minimum seconds
        var maxSeconds = 6; // Maximum seconds
        var randomSeconds = getRandomInt(minSeconds, maxSeconds);
        return randomSeconds + 's';
    }

    function randomizePosition(element) {
        var maxWidth = 1920; // กำหนดขนาดความกว้างสูงสุด 1920px
        var maxHeight = 500; // กำหนดขนาดความสูงสูงสุด 1000px

        var randomMarginLeft = getRandomInt(0, maxWidth - element.width);
        var randomMarginTop = getRandomInt(0, maxHeight - element.height);

        element.style.marginLeft = randomMarginLeft + 'px';
        element.style.marginTop = randomMarginTop + 'px';
    }

    window.onload = function() {
        var animations = document.querySelectorAll('.dot-news-animation');
        animations.forEach(function(animation) {
            animation.style.animationDuration = randomizeAnimationDuration();
            randomizePosition(animation);
        });
    }

    let slideIndex = 0;
    let isDown = false;
    let startX;
    let scrollLeft;

    const slider = document.querySelector('.slideshow-container');
    const track = document.querySelector('.slide-track');
    const cards = document.querySelectorAll('.card');
    const cardWidth = cards[0].offsetWidth + 20; // รวม margin

    // Duplicate the first and last cards
    const firstCard = cards[0].cloneNode(true);
    const lastCard = cards[cards.length - 1].cloneNode(true);
    track.appendChild(firstCard);
    track.insertBefore(lastCard, track.firstChild);

    // Function to move slides
    function moveSlides() {
        let totalWidth = track.scrollWidth;
        let containerWidth = slider.offsetWidth;
        let maxIndex = Math.floor((totalWidth - containerWidth) / cardWidth);

        if (slideIndex > maxIndex) {
            slideIndex = 0; // ไปที่สไลด์แรก
        } else if (slideIndex < 0) {
            slideIndex = maxIndex; // ไปที่สไลด์สุดท้าย
        }

        track.style.transform = 'translateX(' + (-slideIndex * cardWidth) + 'px)';
    }

    // Function to move to next or previous slide
    function plusSlides(n) {
        slideIndex += n;
        if (slideIndex > cards.length - 1) {
            slideIndex = 0; // รีเซ็ตกลับไปที่สไลด์แรก
        } else if (slideIndex < 0) {
            slideIndex = cards.length - 1; // ไปที่สไลด์สุดท้าย
        }
        moveSlides();
    }

    // Automatic slide change
    setInterval(() => {
        plusSlides(1);
    }, 3000); // เปลี่ยนทุก 3 วินาที

    slider.addEventListener('mousedown', (e) => {
        isDown = true;
        slider.classList.add('active');
        startX = e.pageX - slider.offsetLeft;
        scrollLeft = slider.scrollLeft;
    });

    slider.addEventListener('mouseleave', () => {
        isDown = false;
        slider.classList.remove('active');
    });

    slider.addEventListener('mouseup', () => {
        isDown = false;
        slider.classList.remove('active');
    });

    slider.addEventListener('mousemove', (e) => {
        if (!isDown) return;
        e.preventDefault();
        const x = e.pageX - slider.offsetLeft;
        const walk = (x - startX) * 2; // ปรับค่าตามความเร็วในการเลื่อน
        slider.scrollLeft = scrollLeft - walk;
    });

    moveSlides(); // Initial move to set the correct position

    function getRandomInt(min, max) {
        return Math.floor(Math.random() * (max - min + 1)) + min;
    }

    // ฟังก์ชันสุ่มตำแหน่งขององค์ประกอบ
    function randomizePosition($element) {
        var maxWidth = $(window).width(); // ขนาดความกว้างของหน้าจอปัจจุบัน
        var maxHeight = $(window).height(); // ขนาดความสูงของหน้าจอปัจจุบัน

        var randomMarginLeft = getRandomInt(0, maxWidth - $element.width());
        var randomMarginTop = getRandomInt(0, maxHeight - $element.height());

        $element.css({
            marginLeft: randomMarginLeft + 'px',
            marginTop: randomMarginTop + 'px'
        });
    }

    // ฟังก์ชันสุ่มการหน่วงเวลาเริ่มต้นแอนิเมชัน
    function randomizeAnimationDelay($element) {
        var randomDelay = getRandomInt(0, 3); // สุ่มการหน่วงเวลาระหว่าง 0 ถึง 3 วินาที
        $element.css('animationDelay', randomDelay + 's');
    }

    // นำฟังก์ชันไปใช้กับองค์ประกอบที่ต้องการ
    $(document).ready(function() {
        var $animations = $('.wipwap');
        $animations.each(function() {
            var $this = $(this);

            // สุ่มการหน่วงเวลาแอนิเมชัน
            randomizeAnimationDelay($this);

            // กำหนดค่าเริ่มต้น
            randomizePosition($this);

            // เพิ่ม event listener เพื่อตรวจสอบการเปลี่ยนแปลงของ opacity
            $this.on('animationiteration', function() {
                // ตั้งเวลาเพื่อให้เกิดการเปลี่ยนแปลงตำแหน่งเมื่อ opacity = 0
                setTimeout(function() {
                    randomizePosition($this);
                }, 1500); // 50% ของเวลาแอนิเมชัน 3s
            });
        });
    });
</script>

</body>

</html>