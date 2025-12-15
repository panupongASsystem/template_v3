<!-- Include Bootstrap CSS and JavaScript -->
<!-- <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js" integrity="sha384-C6RzsynM9kWDrMNeT87bh95OGNyZPhcTNXj1NW7RuBCsyN/o0jlpcV8Qyq46cDfL" crossorigin="anonymous"></script> -->
<script src="<?= base_url('asset/'); ?>boostrap/bootstrap/dist/js/bootstrap.bundle.min.js"></script>

<script src="https://code.jquery.com/jquery-3.7.1.min.js"
    integrity="sha256-/JqT3SQfawRcv/BIHPThkBvs0OEvtFFmqPF/lYI/Cxo=" crossorigin="anonymous"></script>
<script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
<!-- awesome  -->
<script src="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/js/all.min.js"></script>
<!-- Add Swiper JS -->
<!-- <script src="https://cdn.jsdelivr.net/npm/swiper@10/swiper-bundle.min.js"></script> -->
<script src="<?= base_url('asset/'); ?>swiper/swiper/swiper-bundle.min.js"></script>

<!-- reCAPTCHA2  -->
<script src="https://www.google.com/recaptcha/api.js?hl=th"></script>

<!-- reCAPTCHA 3  หน้านี้มีเปลี่ยน 1 จุด นี่จุด 1 -->
<script src="https://www.google.com/recaptcha/api.js?render=<?php echo get_config_value('recaptcha'); ?>"></script>

<!-- chart พาย  -->
<script src="<?= base_url('asset/'); ?>rpie.js"></script>
<!-- ใช้ JavaScript ของ Swiper -->
<script src="https://cdn.jsdelivr.net/npm/swiper@11/swiper-bundle.min.js"></script>

<!-- ใช้ JavaScript ของ Slick Carousel  -->
<!-- <script type="text/javascript" src="//cdn.jsdelivr.net/npm/slick-carousel@1.8.1/slick/slick.min.js"></script> -->
<script src="<?= base_url('asset/'); ?>slick/slick-carousel/slick/slick.min.js"></script>

<!-- sweetalert 2 -->
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11.7.31/dist/sweetalert2.all.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

<script src="https://cdn.jsdelivr.net/npm/swiper@11/swiper-element-bundle.min.js"></script>

<!-- รูปภาพ preview -->
<script src="<?= base_url('asset/'); ?>lightbox2/src/js/lightbox.js"></script>
<script src="https://cdn.jsdelivr.net/npm/@fancyapps/ui@5.0/dist/fancybox/fancybox.umd.js"></script>

<!-- Including Flatpickr JS -->
<script src="https://cdn.jsdelivr.net/npm/flatpickr"></script>
<script src="https://cdn.jsdelivr.net/npm/flatpickr/dist/l10n/th.js"></script>

<!-- Google Translate -->
<script src="https://translate.google.com/translate_a/element.js?cb=googleTranslateElementInit"></script>

<!-- เพิ่ม SheetJS Library พรีวิว excel doc -->
<script src="https://cdn.jsdelivr.net/npm/xlsx@0.18.5/dist/xlsx.full.min.js"></script>


<!-- PDF.js Library - สำหรับ E-Magazine Modal -->
<script src="https://cdnjs.cloudflare.com/ajax/libs/pdf.js/2.16.105/pdf.min.js"></script>

<!-- Include Bootstrap CSS and JavaScript -->
<script src="<?= base_url('asset/'); ?>boostrap/bootstrap/dist/js/bootstrap.bundle.min.js"></script>

<!-- กล่อง popup e-magazine -->
<script src="/assets/dflip/js/libs/jquery.min.js" type="text/javascript"></script>
<script src="/assets/dflip/js/dflip.min.js" type="text/javascript"></script>

<script>
    // ป้องกันการดูโค้ด ---------------------------------------------------

    //start รถวิ่งตามจุดที่ hover ---------------------------------------------------
    function moveBusTo(position) {
        const bus = document.getElementById('busElement');

        // เพิ่ม class สำหรับหมุนล้อ
        bus.classList.add('bus-moving');

        // เคลื่อนที่ไปตำแหน่งที่กำหนด
        switch (position) {
            case 1:
                bus.classList.add('bus-position-1');
                break;
            case 2:
                bus.classList.add('bus-position-2');
                break;
            case 3:
                bus.classList.add('bus-position-3');
                break;
            case 4:
                bus.classList.add('bus-position-4');
                break;
        }

        // หยุดหมุนล้อเมื่อถึงจุดหมาย (หลังจาก 2 วินาที)
        setTimeout(function () {
            bus.classList.remove('bus-moving');
        }, 2000); // 2000ms = 2 วินาที (ตรงกับเวลา transition)
    }

    function returnBus() {
        const bus = document.getElementById('busElement');

        // เริ่มหมุนล้อเมื่อเริ่มเคลื่อนที่กลับ
        bus.classList.add('bus-moving');

        // กลับไปตำแหน่งเดิม
        bus.classList.remove('bus-position-1', 'bus-position-2', 'bus-position-3', 'bus-position-4');

        // หยุดหมุนล้อเมื่อกลับถึงตำแหน่งเดิมแล้ว (หลังจาก 2 วินาที)
        setTimeout(function () {
            bus.classList.remove('bus-moving');
        }, 2000);
    }
    //end รถวิ่งตามจุดที่ hover ---------------------------------------------------
    ////////////////////////////////////////////////////////////////////////////////
    document.addEventListener('DOMContentLoaded', function () {
        const leaves = document.querySelectorAll('.animation-item');

        leaves.forEach((leaf) => {
            const delay = Math.random() * 5; // สุ่มเวลาเริ่มต้นระหว่าง 0 ถึง 5 วินาที
            const duration = Math.random() * 10 + 10; // สุ่มความยาวระหว่าง 10 ถึง 20 วินาที
            const startX = Math.random() * 100; // สุ่มตำแหน่งเริ่มต้นทางแนวนอน

            leaf.style.left = `${startX}vw`;
            leaf.style.animationDelay = `${delay}s`;
            leaf.style.animationDuration = `${duration}s`;
        });
    });
    document.addEventListener('DOMContentLoaded', function () {
        const leaves = document.querySelectorAll('.animation-item2');

        leaves.forEach((leaf) => {
            const delay = Math.random() * 5; // สุ่มเวลาเริ่มต้นระหว่าง 0 ถึง 5 วินาที
            const duration = Math.random() * 10 + 10; // สุ่มความยาวระหว่าง 10 ถึง 20 วินาที
            const startX = Math.random() * 100; // สุ่มตำแหน่งเริ่มต้นทางแนวนอน

            leaf.style.left = `${startX}vw`;
            leaf.style.animationDelay = `${delay}s`;
            leaf.style.animationDuration = `${duration}s`;
        });
    });

    document.addEventListener('DOMContentLoaded', function () {
        const leaves = document.querySelectorAll('.baimai-animation');

        leaves.forEach((leaf) => {
            const delay = Math.random() * 5; // สุ่มเวลาเริ่มต้นระหว่าง 0 ถึง 5 วินาที
            const duration = Math.random() * 10 + 10; // สุ่มความยาวระหว่าง 10 ถึง 20 วินาที
            const startX = Math.random() * 100; // สุ่มตำแหน่งเริ่มต้นทางแนวนอน

            leaf.style.left = `${startX}vw`;
            leaf.style.animationDelay = `${delay}s`;
            leaf.style.animationDuration = `${duration}s`;
        });
    });
    // ---------------------------------------------------------------------
    // Responsive mobile -----------------------------------
    document.addEventListener('DOMContentLoaded', function () {
        function setViewportScale() {
            const viewport = document.querySelector("meta[name=viewport]");
            const width = window.innerWidth;

            if (width <= 279) {
                viewport.setAttribute("content", "width=device-width, initial-scale=0.1");
            } else if (width >= 280 && width <= 319) {
                viewport.setAttribute("content", "width=device-width, initial-scale=0.146");
            } else if (width >= 320 && width <= 359) {
                viewport.setAttribute("content", "width=device-width, initial-scale=0.167");
            } else if (width >= 360 && width <= 374) {
                viewport.setAttribute("content", "width=device-width, initial-scale=0.19");
            } else if (width >= 375 && width <= 379) {
                viewport.setAttribute("content", "width=device-width, initial-scale=0.195");
            } else if (width >= 380 && width <= 411) {
                viewport.setAttribute("content", "width=device-width, initial-scale=0.205");
            } else if (width >= 412 && width <= 419) {
                viewport.setAttribute("content", "width=device-width, initial-scale=0.215");
            } else if (width >= 420 && width <= 480) {
                viewport.setAttribute("content", "width=device-width, initial-scale=0.225");
            } else if (width >= 481 && width <= 539) {
                viewport.setAttribute("content", "width=device-width, initial-scale=0.4");
            } else if (width >= 540 && width <= 546) {
                viewport.setAttribute("content", "width=device-width, initial-scale=0.282");
            } else if (width >= 547 && width <= 640) {
                viewport.setAttribute("content", "width=device-width, initial-scale=0.29");
            } else if (width >= 641 && width <= 711) {
                viewport.setAttribute("content", "width=device-width, initial-scale=0.33");
            } else if (width >= 712 && width <= 767) {
                viewport.setAttribute("content", "width=device-width, initial-scale=0.371");
            } else if (width >= 768 && width <= 818) {
                viewport.setAttribute("content", "width=device-width, initial-scale=0.4");
            }
        }

        // เรียกใช้ฟังก์ชันเมื่อโหลดหน้าและเมื่อมีการปรับขนาดหน้าจอ
        setViewportScale();
        window.addEventListener('resize', setViewportScale);
    });
    /* ------------------------------------------------- */

    // ตรวจจับคำสั่งพิมพ์ -----------------------------------
    window.addEventListener('beforeprint', function (e) {
        e.preventDefault();
        // ยกเลิกการพิมพ์ทันที          
        // แสดง alert และรอการยืนยัน         
        if (confirm('⚙️ กรุณาตั้งค่าการพิมพ์\n\n✅ Scale: Custom 170%\n✅ เปิดใช้งาน Background graphics\n\nคุณได้ตั้งค่าเรียบร้อยแล้วหรือไม่?')) {
            window.print();
        }
    });
    /* ------------------------------------------------- */
    // preview img Fancybo start =======================================================================
    Fancybox.bind("[data-fancybox]", {
        infinite: true,
        keyboard: true,
        wheel: "slide",

        Toolbar: {
            display: {
                left: ["infobar"],
                middle: ["zoomIn", "zoomOut", "toggle1to1", "rotateCCW", "rotateCW", "flipX", "flipY"],
                right: ["slideshow", "thumbs", "close"],
            },
        },

        Thumbs: {
            autoStart: false,
        },

        Slideshow: {
            autoStart: false,
            speed: 3000,
        },

        Images: {
            zoom: true,
            protected: true,
        },

        showClass: "fancybox-zoomInUp",
        hideClass: "fancybox-zoomOutDown",

        // Thai language
        l10n: {
            CLOSE: "ปิด",
            NEXT: "ถัดไป",
            PREV: "ก่อนหน้า",
            MODAL: "คุณสามารถปิดหน้าต่างนี้ได้โดยกดปุ่ม ESC",
            ERROR: "เกิดข้อผิดพลาดในการโหลดรูปภาพ",
            IMAGE_ERROR: "ไม่พบรูปภาพ",
            TOGGLE_ZOOM: "ซูมภาพ",
            TOGGLE_THUMBS: "รูปภาพย่อ",
            TOGGLE_SLIDESHOW: "สไลด์โชว์",
            TOGGLE_FULLSCREEN: "เต็มจอ",
            DOWNLOAD: "ดาวน์โหลด"
        }
    });
    // preview img Fancybo start =======================================================================


    //  แปลภาษา Google Translate ===================================================
    function googleTranslateElementInit() {
        // ปิดชั่วคราวเพื่อ debug
        console.log('Google Translate disabled for debugging');
        return;

        // เก็บโค้ดเดิมไว้
        // new google.translate.TranslateElement({
        //     pageLanguage: 'th',
        //     includedLanguages: 'en,th',
        //     autoDisplay: false
        // }, 'google_translate_element');

        // เพิ่มการเคลียร์ค่าการแปลเมื่อโหลดครั้งแรก
        if (document.cookie.indexOf('googtrans') > -1) {
            // ลบ cookie ของ Google Translate
            document.cookie = 'googtrans=; expires=Thu, 01 Jan 1970 00:00:00 UTC; path=/;';
            document.cookie = 'googtrans=; expires=Thu, 01 Jan 1970 00:00:00 UTC; path=/; domain=.' + window.location.hostname;
            document.cookie = 'googtrans=; expires=Thu, 01 Jan 1970 00:00:00 UTC; path=/; domain=' + window.location.hostname;
        }
    }

    function translateToEnglish() {
        const waitForSelect = setInterval(() => {
            const select = document.querySelector('.goog-te-combo');
            if (select) {
                clearInterval(waitForSelect);

                select.value = 'en';
                select.dispatchEvent(new Event('change'));

                document.querySelectorAll('.lang-btn').forEach(btn => {
                    btn.classList.remove('active');
                });
                document.querySelector('[data-lang="en"]').classList.add('active');

                document.getElementById('langFlag').src = 'https://flagcdn.com/w20/gb.png';
            }
        }, 100);
    }

    function translateToThai() {
        // ลบ cookie ของ Google Translate
        document.cookie = 'googtrans=; expires=Thu, 01 Jan 1970 00:00:00 UTC; path=/;';
        document.cookie = 'googtrans=; expires=Thu, 01 Jan 1970 00:00:00 UTC; path=/; domain=.' + window.location.hostname;
        document.cookie = 'googtrans=; expires=Thu, 01 Jan 1970 00:00:00 UTC; path=/; domain=' + window.location.hostname;

        // อัพเดทปุ่มและธง
        document.querySelectorAll('.lang-btn').forEach(btn => {
            btn.classList.remove('active');
        });
        document.querySelector('[data-lang="th"]').classList.add('active');
        document.getElementById('langFlag').src = 'https://flagcdn.com/w20/th.png';

        // รีโหลดหน้าเว็บ
        location.reload();
    }

    // รันเมื่อโหลดหน้าเว็บ
    document.addEventListener('DOMContentLoaded', function () {
        // เคลียร์ URL parameter ของ Google Translate ถ้ามี
        if (window.location.href.indexOf('?googtrans=') > -1 ||
            window.location.href.indexOf('&googtrans=') > -1) {
            window.location.href = window.location.href.split('?')[0];
        }

        // ตั้งค่าปุ่มภาษาไทยเป็น active
        const thButton = document.querySelector('[data-lang="th"]');
        if (thButton) {
            thButton.classList.add('active');
        }
    });

    // เรียกใช้ฟังก์ชันเมื่อหน้าโหลดเสร็จ
    document.addEventListener('DOMContentLoaded', function () {
        googleTranslateElementInit(); // เรียกใช้ฟังก์ชันเมื่อ DOM โหลดเสร็จ
    });

    // **************************************

    //  แปลภาษา Translate **************************
    function googleTranslateElementInit() {
        new google.translate.TranslateElement({
            pageLanguage: 'th',
            includedLanguages: 'en,th',
            autoDisplay: false
        }, 'google_translate_element');

        // เพิ่มการเคลียร์ค่าการแปลเมื่อโหลดครั้งแรก
        if (document.cookie.indexOf('googtrans') > -1) {
            // ลบ cookie ของ Google Translate
            document.cookie = 'googtrans=; expires=Thu, 01 Jan 1970 00:00:00 UTC; path=/;';
            document.cookie = 'googtrans=; expires=Thu, 01 Jan 1970 00:00:00 UTC; path=/; domain=.' + window.location.hostname;
            document.cookie = 'googtrans=; expires=Thu, 01 Jan 1970 00:00:00 UTC; path=/; domain=' + window.location.hostname;
        }
    }

    function translateToEnglish() {
        const waitForSelect = setInterval(() => {
            const select = document.querySelector('.goog-te-combo');
            if (select) {
                clearInterval(waitForSelect);

                select.value = 'en';
                select.dispatchEvent(new Event('change'));

                document.querySelectorAll('.lang-btn').forEach(btn => {
                    btn.classList.remove('active');
                });
                document.querySelector('[data-lang="en"]').classList.add('active');

                // document.getElementById('langFlag').src = 'https://flagcdn.com/w20/gb.png';
            }
        }, 100);
    }

    function translateToThai() {
        // ลบ cookie ของ Google Translate
        document.cookie = 'googtrans=; expires=Thu, 01 Jan 1970 00:00:00 UTC; path=/;';
        document.cookie = 'googtrans=; expires=Thu, 01 Jan 1970 00:00:00 UTC; path=/; domain=.' + window.location.hostname;
        document.cookie = 'googtrans=; expires=Thu, 01 Jan 1970 00:00:00 UTC; path=/; domain=' + window.location.hostname;

        // อัพเดทปุ่มและธง
        document.querySelectorAll('.lang-btn').forEach(btn => {
            btn.classList.remove('active');
        });
        document.querySelector('[data-lang="th"]').classList.add('active');
        // document.getElementById('langFlag').src = 'https://flagcdn.com/w20/th.png';

        // รีโหลดหน้าเว็บ
        location.reload();
    }

    // รันเมื่อโหลดหน้าเว็บ
    document.addEventListener('DOMContentLoaded', function () {
        // เคลียร์ URL parameter ของ Google Translate ถ้ามี
        if (window.location.href.indexOf('?googtrans=') > -1 ||
            window.location.href.indexOf('&googtrans=') > -1) {
            window.location.href = window.location.href.split('?')[0];
        }

        // ตั้งค่าปุ่มภาษาไทยเป็น active
        const thButton = document.querySelector('[data-lang="th"]');
        if (thButton) {
            thButton.classList.add('active');
        }
    });

    // เรียกใช้ฟังก์ชันเมื่อหน้าโหลดเสร็จ
    document.addEventListener('DOMContentLoaded', function () {
        googleTranslateElementInit(); // เรียกใช้ฟังก์ชันเมื่อ DOM โหลดเสร็จ
    });
    //  แปลภาษา Google Translate ===================================================

    // // banner center ห้ามลบใช้อยู่ *************************************************
    // $(document).ready(function() {
    //     var $videoContent = $(".video-content");
    //     var $carouselDiv = $("#carouselExampleAutoplaying");
    //     var $carouselIndicators = $(".carousel-indicators");

    //     // ตรวจสอบว่ามีวิดีโอหรือไม่
    //     if ($videoContent.length === 0 || $.trim($videoContent.html()) === "") {
    //         // ปรับ CSS ของ carouselDiv โดยใช้ margin-left
    //         $carouselDiv.css("margin-left", "calc(50% - -10px)");

    //         // ปรับ CSS ของ carouselIndicators ให้อยู่ตรงกลาง
    //         $carouselIndicators.css({
    //             "left": "100%",
    //             "transform": "translateX(100%)"
    //         });
    //     }
    // });
    // ดักไฟล์ถูกประเภท ก่อนส่ง *********************************************************
    function validateForm(input) {
        const imageTypes = ['image/jpeg', 'image/png', 'image/jpg', 'image/jfif'];
        const pdfType = 'application/pdf';
        const docTypes = [
            'application/msword',
            'application/vnd.openxmlformats-officedocument.wordprocessingml.document',
            'application/vnd.ms-powerpoint',
            'application/vnd.openxmlformats-officedocument.presentationml.presentation',
            'application/vnd.ms-excel',
            'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet'
        ];

        const files = input.files;
        let valid = true;

        for (const file of files) {
            if (input.accept.includes('image/') && !imageTypes.includes(file.type)) {
                valid = false;
                Swal.fire({
                    icon: 'error',
                    title: 'ตรวจพบปัญหา',
                    text: 'รูปภาพเพิ่มเติมจะต้องเป็นไฟล์ .JPG/.JPEG/.jfif/.PNG เท่านั้น!',
                    footer: '<a href="#">ติดต่อผู้ดูแลระบบ?</a>'
                });
                break;
            }
            if (input.accept.includes('application/pdf') && file.type !== pdfType) {
                valid = false;
                Swal.fire({
                    icon: 'error',
                    title: 'ตรวจพบปัญหา',
                    text: 'ไฟล์เอกสารเพิ่มเติมจะต้องเป็นไฟล์ PDF เท่านั้น',
                    footer: '<a href="#">ติดต่อผู้ดูแลระบบ?</a>'
                });
                break;
            }
            if (input.accept.includes('application/msword') || input.accept.includes('application/vnd.openxmlformats-officedocument')) {
                if (!docTypes.includes(file.type)) {
                    valid = false;
                    Swal.fire({
                        icon: 'error',
                        title: 'ตรวจพบปัญหา',
                        text: 'ไฟล์เอกสารเพิ่มเติมจะต้องเป็นไฟล์ .doc .docx .ppt .pptx .xls .xlsx เท่านั้น',
                        footer: '<a href="#">ติดต่อผู้ดูแลระบบ?</a>'
                    });
                    break;
                }
            }
        }

        if (!valid) {
            input.value = ''; // ลบไฟล์ที่เลือก
            return false;
        }
        return true;
    }

    // input date thai ******************************************************* */
    document.addEventListener('DOMContentLoaded', function () {
        flatpickr("#startDate_egp", {
            dateFormat: "Y-m-d",
            disableMobile: true,
            locale: "th"
        });
        flatpickr("#endDate_egp", {
            dateFormat: "Y-m-d",
            disableMobile: true,
            locale: "th"
        });
    });
    // *************************************************************************** */
    // รีเซ็ต ข้อมูลในฟอร์ม egp ******************************************************* */
    $('#resetButton').on('click', function () {
        $('#startDate_egp').val('');
        $('#endDate_egp').val('');
        $('input[name="search"]').val('');
        $('#selectedOptionInput').val('procurement_tbl_w0_search');
        $('#searchOption').val('procurement_tbl_w0_search');
        $('#searchForm').attr('action', '<?= site_url("Pages/procurement_tbl_w0_search"); ?>');
    });
    // *************************************************************************** */
    // ตัวสลับฟังก์ชั่นใน controller pages egp ******************************************************* */
    $(document).ready(function () {
        $('#searchOption').on('change', function () {
            var selectedOption = $(this).val();
            var formAction = "<?= site_url('Pages/'); ?>" + selectedOption;

            $('#searchForm').attr('action', formAction); // เปลี่ยนค่า action ของฟอร์ม
            $('#selectedOptionInput').val(selectedOption); // บันทึกค่า searchOption ลงในฟอร์ม
        });
    });
    // $('#searchOption').on('change', function() {
    //     var formAction = $(this).val(); // รับค่า action จาก option ที่เลือก
    //     $('#searchForm').attr('action', "<?= site_url('Pages/'); ?>" + formAction);
    // });
    // *************************************************************************** */

    $(document).ready(function () {
        var $buttons = $('.nav-container a');
        var $contents = $('.content-box');
        var $contentContainer = $('#content-container');

        $buttons.on('mouseenter', function () {
            var targetContentId = $(this).data('content');
            var buttonClass = $(this).attr('class');

            // Check if the button is nav-button1, nav-button6, or nav-button7
            if (buttonClass.includes('nav-button1') || buttonClass.includes('nav-button8') || buttonClass.includes('nav-button9')) {
                // Hide the content container if it's currently visible
                $contentContainer.hide();
                $buttons.removeClass('active');
                $contents.removeClass('active');
                return; // Skip the rest of the function
            }

            if (targetContentId) {
                // Remove active class from all buttons
                $buttons.removeClass('active');

                // Add active class to the hovered button
                $(this).addClass('active');

                // Remove active class from all content boxes
                $contents.removeClass('active');

                // Add active class to the target content box
                $('#' + targetContentId).addClass('active');

                // Show the content container at the position of nav-button1
                var $button1 = $('.nav-button1');
                var offset = $button1.offset();
                $contentContainer.css('top', offset.top + $(window).scrollTop() + 'px');
                $contentContainer.show();
            }
        });

        // Hide content-container when the mouse leaves it
        $contentContainer.on('mouseleave', function () {
            $contentContainer.hide();
            $buttons.removeClass('active');
            $contents.removeClass('active');
        });

        // Hide content-container when clicking outside of it or the buttons
        $(document).on('click', function (event) {
            var $target = $(event.target);
            if (!$target.closest('.nav-container a').length && !$target.closest('#content-container').length) {
                $contentContainer.hide();
                $buttons.removeClass('active');
                $contents.removeClass('active');
            }
        });
    });

    //********** ค่าว่างเลขหน้า ******************************************************* */
    $(document).ready(function () {
        $('#pageForm').submit(function (event) {
            var pageInput = $('#pageInput').val();
            if (pageInput === "") {
                Swal.fire({
                    icon: 'error',
                    title: 'กรุณากรอกหมายเลขหน้าก่อนที่จะไปหน้า',
                    confirmButtonText: 'ตกลง'
                });
                event.preventDefault(); // Prevent form submission
            }
        });
    });
    //********** menubar ********************************************************** */
    $(document).ready(function () {
        $('#hide-button').click(function () {
            $('#wel-navbar').addClass('hide');
            $('#show-button').show();
        });

        $('#show-button').click(function () {
            $('#wel-navbar').removeClass('hide');
            $('#show-button').hide();
        });

        $(window).scroll(function () {
            if ($(this).scrollTop() === 0) {
                $('#wel-navbar').removeClass('hide');
                $('#show-button').hide();
            } else {
                $('#wel-navbar').addClass('hide');
                $('#show-button').show();
            }
        });
    });

    //*********************************************************************************** */
    // document.addEventListener("DOMContentLoaded", function() {
    //     const container = document.querySelector(".container-wel-g3-animation");
    //     const images = container.querySelectorAll("img");

    //     images.forEach(img => {
    //         img.style.position = "absolute";
    //         img.style.top = "-100px"; // เริ่มต้นอยู่นอกจอ
    //         img.style.left = Math.random() * container.offsetWidth + "px"; // สุ่มตำแหน่งแนวนอน

    //         const delay = Math.random() * 5; // สุ่ม delay
    //         const duration = 3 + Math.random() * 2; // สุ่ม duration
    //         img.style.animation = `fall ${duration}s linear ${delay}s infinite`;

    //         // เพิ่มการหมุนให้ภาพ
    //         img.style.transform = `rotate(${Math.random() * 360}deg)`;
    //     });
    // });
    //***** required ********************************************************************/
    // document.getElementById('reCAPTCHA3').addEventListener('submit', function(event) {
    //     const form = event.target;
    //     let isValid = true;
    //     form.querySelectorAll('input, textarea').forEach(function(input) {
    //         if (!input.checkValidity()) {
    //             input.reportValidity();
    //             isValid = false;
    //         }
    //     });
    //     if (!isValid) {
    //         event.preventDefault();
    //     }
    // });
    //***** ตัวหนังสือขึ้นทีละตัว ************************************************************************ */
    function wrapText(selector, delayMultiplier = 0) {
        const element = $(selector);
        const text = element.html();
        element.html('');

        let index = 0;
        for (let i = 0; i < text.length; i++) {
            if (text[i] === '<' && text.substring(i, i + 4) === '<br>') {
                const br = $('<br>');
                element.append(br);
                i += 3; // Skip <br>
            } else {
                const span = $('<span>').text(text[i]);
                span.css('animationDelay', `${(index * 0.09) + delayMultiplier}s`);
                element.append(span);
                index++;
            }
        }
        return index * 0.05; // Return the total duration
    }

    function animateText1And2() {
        const text1Duration = wrapText('#text-1');
        const text2Duration = wrapText('#text-2', text1Duration);

        const totalDuration = (text1Duration + text2Duration + 0.5) * 1000; // Include a little buffer
    }

    function animateText3And4() {
        const text3Duration = wrapText('#text-3');
        const text4Duration = wrapText('#text-4', text3Duration);

        const totalDuration = (text3Duration + text4Duration + 0.5) * 1000; // Include a little buffer
    }

    $(document).ready(function () {
        animateText1And2();
        animateText3And4();
    });
    // สลับหน้า welcome  ********************************************************************************
    document.addEventListener('DOMContentLoaded', function () {
        console.log('🎬 Starting safe slide animation...');

        let currentIndex = 0;
        const $contents = document.querySelectorAll('.fade-content');

        // ✅ ตรวจสอบว่ามี content หรือไม่
        if (!$contents || $contents.length === 0) {
            console.log('ℹ️ No fade-content elements found - slide animation disabled');
            return;
        }

        console.log(`✅ Found ${$contents.length} slide contents`);

        // ✅ ฟังก์ชันแสดง slide แบบปลอดภัย
        function showNextContent() {
            try {
                // ตรวจสอบ element ปัจจุบัน
                const currentElement = $contents[currentIndex];
                if (!currentElement) {
                    console.warn(`⚠️ Current element at index ${currentIndex} not found`);
                    return;
                }

                // ตรวจสอบว่ามี classList หรือไม่
                if (!currentElement.classList) {
                    console.warn(`⚠️ Current element has no classList`);
                    return;
                }

                // ซ่อน div ปัจจุบัน
                currentElement.classList.remove('active');

                // รอให้ transition (opacity) ทำงานเสร็จ
                setTimeout(() => {
                    // ตรวจสอบอีกครั้งก่อนเข้าถึง style
                    if (currentElement && currentElement.style) {
                        currentElement.style.display = 'none';
                    }

                    // คำนวณ index ของ div ถัดไป
                    currentIndex = (currentIndex + 1) % $contents.length;

                    // ตรวจสอบ element ถัดไป
                    const nextElement = $contents[currentIndex];
                    if (!nextElement) {
                        console.warn(`⚠️ Next element at index ${currentIndex} not found`);
                        currentIndex = 0; // รีเซ็ตเป็น 0
                        return;
                    }

                    // ตรวจสอบว่ามี style หรือไม่
                    if (!nextElement.style) {
                        console.warn(`⚠️ Next element has no style property`);
                        return;
                    }

                    // แสดง div ถัดไป
                    nextElement.style.display = 'block';

                    // ตรวจสอบว่ามี classList หรือไม่
                    if (!nextElement.classList) {
                        console.warn(`⚠️ Next element has no classList`);
                        return;
                    }

                    setTimeout(() => {
                        // ตรวจสอบอีกครั้งก่อนเพิ่ม class
                        if (nextElement && nextElement.classList) {
                            nextElement.classList.add('active');
                            console.log(`✅ Switched to slide ${currentIndex + 1}/${$contents.length}`);
                        }
                    }, 10);

                }, 1000);

            } catch (error) {
                console.error('❌ showNextContent error:', error);
                // รีเซ็ต currentIndex หากเกิด error
                currentIndex = 0;
            }
        }

        function initializeSlideShow() {
            try {
                // ตรวจสอบ element แรก
                const firstElement = $contents[0];
                if (!firstElement) {
                    console.warn('⚠️ First element not found');
                    return false;
                }

                // ซ่อนทุก element ก่อน
                $contents.forEach((element, index) => {
                    if (element && element.style) {
                        element.style.display = 'none';
                        if (element.classList) {
                            element.classList.remove('active');
                        }
                    }
                });

                // แสดง element แรก
                if (firstElement.style) {
                    firstElement.style.display = 'block';
                }

                if (firstElement.classList) {
                    firstElement.classList.add('active');
                }

                console.log('✅ Slide show initialized successfully');
                return true;

            } catch (error) {
                console.error('❌ initializeSlideShow error:', error);
                return false;
            }
        }

        // เริ่มต้น slide show
        const initialized = initializeSlideShow();

        if (initialized && $contents.length > 1) {
            // เรียกฟังก์ชัน showNextContent ทุก 15 วินาที
            const slideInterval = setInterval(() => {
                // ตรวจสอบว่า elements ยังมีอยู่หรือไม่
                if ($contents.length === 0) {
                    console.log('⚠️ No more slide contents - stopping slideshow');
                    clearInterval(slideInterval);
                    return;
                }

                showNextContent();
            }, 15000);

            console.log(`🎯 Slide show started: ${$contents.length} slides, 15s interval`);

            // เก็บ reference ไว้สำหรับหยุด slideshow
            window.stopSlideShow = function () {
                clearInterval(slideInterval);
                console.log('🛑 Slide show stopped');
            };

        } else if ($contents.length === 1) {
            console.log('ℹ️ Only 1 slide found - no animation needed');
        } else {
            console.log('❌ Slide show initialization failed');
        }
    });

    // 🛠️ ฟังก์ชันสำรองแก้ไขปัญหา
    window.fixSlideAnimation = function () {
        console.log('🔧 Fixing slide animation...');

        // หยุด interval เก่า
        if (window.stopSlideShow) {
            window.stopSlideShow();
        }

        // เริ่มใหม่
        setTimeout(() => {
            const event = new Event('DOMContentLoaded');
            document.dispatchEvent(event);
        }, 100);
    };

    // 🧪 ฟังก์ชันทดสอบ
    window.testSlideAnimation = function () {
        console.log('🧪 Testing slide animation...');

        const contents = document.querySelectorAll('.fade-content');
        console.log(`Found ${contents.length} fade-content elements:`);

        contents.forEach((element, index) => {
            console.log(`Element ${index + 1}:`, {
                exists: !!element,
                hasClassList: !!(element && element.classList),
                hasStyle: !!(element && element.style),
                isDisplayed: element && element.style ? element.style.display : 'unknown',
                hasActiveClass: element && element.classList ? element.classList.contains('active') : false
            });
        });

        return contents.length;
    };

    // 🔄 ฟังก์ชันรีเซ็ต slide animation
    window.resetSlideAnimation = function () {
        console.log('🔄 Resetting slide animation...');

        try {
            const contents = document.querySelectorAll('.fade-content');

            if (contents.length === 0) {
                console.log('ℹ️ No fade-content elements to reset');
                return;
            }

            // รีเซ็ตทุก element
            contents.forEach((element, index) => {
                if (element) {
                    if (element.style) {
                        element.style.display = index === 0 ? 'block' : 'none';
                    }
                    if (element.classList) {
                        element.classList.remove('active');
                        if (index === 0) {
                            element.classList.add('active');
                        }
                    }
                }
            });

            console.log('✅ Slide animation reset completed');

        } catch (error) {
            console.error('❌ Reset slide animation error:', error);
        }
    };

    //  **************************************************************************************************


    // $(document).ready(function() {
    //     var $container = $('.welcome-other');
    //     var duration = 20000; // 10 วินาที
    //     var pauseDuration = 3000; // 3 วินาทีสำหรับการค้างไว้
    //     var start = null;

    //     function slideBackground(timestamp) {
    //         if (!start) start = timestamp;
    //         var elapsed = timestamp - start;

    //         // คำนวณตำแหน่งใหม่ของ background
    //         var position = (elapsed / duration) * 100;

    //         // ตั้งค่าตำแหน่ง background ของ container
    //         $container.css('background-position', 'center ' + position + '%');

    //         // ดำเนินการ animation จนกระทั่งเวลาครบกำหนด
    //         if (elapsed < duration) {
    //             requestAnimationFrame(slideBackground);
    //         } else {
    //             // เมื่อถึงตำแหน่งสุดท้าย ค้างไว้ 3 วินาทีแล้วเริ่มใหม่
    //             setTimeout(function() {
    //                 start = null;
    //                 requestAnimationFrame(slideBackground);
    //             }, pauseDuration);
    //         }
    //     }

    //     requestAnimationFrame(slideBackground);
    // });



    // E-service ด้านบนสู่ด้านล่าง  ********************************************************************************
    // 🔧 แก้ไข consoleText setAttribute Error
    // แทนที่ฟังก์ชัน consoleText เก่าด้วยนี้

    function consoleText(words, id, colors) {
        try {
            // ✅ ตรวจสอบ parameters
            if (!words || !Array.isArray(words) || words.length === 0) {
                console.warn('⚠️ consoleText: Invalid words array');
                return;
            }

            if (!id || typeof id !== 'string') {
                console.warn('⚠️ consoleText: Invalid id parameter');
                return;
            }

            // ✅ ตั้งค่า default colors
            if (colors === undefined || !Array.isArray(colors)) {
                colors = ['#fff'];
            }

            // ✅ ตรวจสอบว่าพบ element หรือไม่
            var target = document.getElementById(id);
            if (!target) {
                console.warn(`⚠️ consoleText: Element with id "${id}" not found`);
                return;
            }

            // ✅ ตรวจสอบว่า element มี setAttribute หรือไม่
            if (!target.setAttribute || typeof target.setAttribute !== 'function') {
                console.warn(`⚠️ consoleText: Element "${id}" has no setAttribute method`);
                return;
            }

            console.log(`✅ consoleText: Starting animation for "${id}" with ${words.length} words`);

            var visible = true;
            var letterCount = 1;
            var index = 0;
            var waiting = false;

            // ✅ ตั้งค่าสีเริ่มต้นแบบปลอดภัย
            try {
                target.setAttribute('style', 'color:' + colors[0]);
            } catch (styleError) {
                console.warn('⚠️ consoleText: Failed to set initial style', styleError);
                // ลองใช้ style property แทน
                if (target.style) {
                    target.style.color = colors[0];
                }
            }

            function updateText() {
                try {
                    // ✅ ตรวจสอบ target อีกครั้งก่อนใช้งาน
                    if (!target) {
                        console.warn('⚠️ consoleText: Target element lost during animation');
                        return;
                    }

                    if (letterCount === 0 && waiting === false) {
                        waiting = true;

                        // ✅ ตรวจสอบก่อนเปลี่ยน innerHTML
                        if (target.innerHTML !== undefined) {
                            target.innerHTML = '';
                        }

                        window.setTimeout(function () {
                            try {
                                index = (index + 1) % words.length;

                                // ✅ ตรวจสอบก่อนเปลี่ยนสี
                                const colorIndex = index % colors.length;
                                if (target.setAttribute) {
                                    target.setAttribute('style', 'color:' + colors[colorIndex]);
                                } else if (target.style) {
                                    target.style.color = colors[colorIndex];
                                }

                                letterCount = 1;
                                waiting = false;
                                updateText();
                            } catch (innerError) {
                                console.error('⚠️ consoleText inner error:', innerError);
                            }
                        }, 1000);

                    } else if (letterCount === words[index].length + 1 && waiting === false) {
                        waiting = true;
                        window.setTimeout(function () {
                            letterCount = 0;
                            waiting = false;
                            updateText();
                        }, 2000);

                    } else if (waiting === false) {
                        // ✅ ตรวจสอบก่อนเปลี่ยน innerHTML
                        if (target.innerHTML !== undefined && words[index]) {
                            target.innerHTML = words[index].substring(0, letterCount);
                            letterCount++;
                            window.setTimeout(updateText, 120);
                        }
                    }

                } catch (updateError) {
                    console.error('❌ consoleText updateText error:', updateError);
                }
            }

            // เริ่มต้น animation
            updateText();

        } catch (error) {
            console.error('❌ consoleText error:', error);
            console.log('Parameters:', {
                words,
                id,
                colors
            });
        }
    }

    // 🛠️ ฟังก์ชันเริ่มต้น consoleText แบบปลอดภัย
    function safeConsoleText(words, id, colors, retryCount = 0) {
        const maxRetries = 5;

        // ตรวจสอบว่ามี element หรือไม่
        const target = document.getElementById(id);
        if (!target) {
            if (retryCount < maxRetries) {
                console.log(`⏳ Waiting for element "${id}" (attempt ${retryCount + 1}/${maxRetries})`);
                setTimeout(() => {
                    safeConsoleText(words, id, colors, retryCount + 1);
                }, 500);
            } else {
                console.warn(`❌ Element "${id}" not found after ${maxRetries} attempts`);
            }
            return;
        }

        // เรียกใช้ consoleText ปกติ
        consoleText(words, id, colors);
    }

    // ✅ ฟังก์ชันตรวจสอบ elements ก่อนเริ่ม animation
    function checkConsoleTextElements() {
        const commonIds = ['text', 'console-text', 'typing-text', 'animated-text'];
        const foundElements = [];

        commonIds.forEach(id => {
            const element = document.getElementById(id);
            if (element) {
                foundElements.push(id);
                console.log(`✅ Found element: #${id}`);
            } else {
                console.log(`⚠️ Missing element: #${id}`);
            }
        });

        return foundElements;
    }

    // 🔄 ฟังก์ชันแทนที่การเรียกใช้ consoleText เดิม
    function initializeConsoleText() {
        console.log('🎯 Initializing console text animations...');

        // ตรวจสอบ elements ที่มีอยู่
        const availableElements = checkConsoleTextElements();

        // รอให้ DOM โหลดเสร็จสมบูรณ์
        setTimeout(() => {
            // เรียกใช้ consoleText สำหรับ element ที่มีอยู่
            const textElement = document.getElementById('text');
            if (textElement) {
                safeConsoleText([
                    'องค์การบริหารส่วนตำบลบ้านกลาง ยินดีต้อนรับค่ะ',
                    'มีบริการยื่นเอกสารออนไลน์',
                    'และมีบริการอื่นๆ อีกมากมาย'
                ], 'text', ['#210B00', '#210B00', '#210B00']);
            } else {
                console.log('ℹ️ No #text element found - skipping console text animation');
            }
        }, 100);
    }

    // 🚀 Auto-initialize เมื่อ DOM พร้อม
    document.addEventListener('DOMContentLoaded', function () {
        // รอให้ DOM โหลดเสร็จสมบูรณ์
        setTimeout(() => {
            initializeConsoleText();
        }, 500);
    });

    // 🧪 ฟังก์ชันทดสอบ
    window.testConsoleText = function () {
        console.log('🧪 Testing console text...');

        // ตรวจสอบ elements
        const elements = checkConsoleTextElements();

        // ทดสอบสร้าง element ชั่วคราว
        if (elements.length === 0) {
            console.log('📝 Creating test element...');
            const testDiv = document.createElement('div');
            testDiv.id = 'test-console-text';
            testDiv.style.cssText = 'padding: 20px; background: #f0f0f0; margin: 10px; border-radius: 5px;';
            document.body.appendChild(testDiv);

            // ทดสอบ animation
            safeConsoleText(['Test message 1', 'Test message 2'], 'test-console-text', ['#333', '#666']);

            // ลบหลังจาก 10 วินาที
            setTimeout(() => {
                testDiv.remove();
                console.log('✅ Test element removed');
            }, 10000);
        }

        return elements;
    };

    // 🛡️ ฟังก์ชันป้องกัน error
    window.addEventListener('error', function (event) {
        if (event.message && event.message.includes("Cannot read properties of null (reading 'setAttribute')")) {
            console.warn('🛡️ setAttribute error prevented for consoleText');
            event.preventDefault();
            return false;
        }
    });

    // 🔧 Override ฟังก์ชันเดิมเพื่อป้องกัน error
    window.originalConsoleText = window.consoleText || consoleText;
    window.consoleText = consoleText;

    //   ***************************************************************************************************************

    // โหลด api สภาพอากาศตามมาทีหลัง  ********************************************************************************
    // $(document).ready(function() {
    //     // ใช้ AJAX เพื่อโหลดข้อมูลพยากรณ์อากาศหลังจากที่หน้าเว็บโหลดเสร็จแล้ว
    //     $.ajax({
    //         url: "<?php echo site_url('WeatherController/loadWeatherData'); ?>",
    //         method: 'GET',
    //         dataType: 'json',
    //         success: function(data) {
    //             if (data && data.channel && data.channel.item) {
    //                 var title = data.channel.item.title;
    //                 var description = data.channel.item.description;

    //                 // ลบแท็ก <br> ออกจาก description
    //                 var descriptionWithoutBr = description.replace(/<br\/>/g, ' ');

    //                 // อัปเดต marquee ด้วยข้อมูลที่ได้รับ
    //                 $('#weather-marquee').html(title + " " + descriptionWithoutBr);
    //             } else {
    //                 console.error('Failed to load weather data');
    //             }
    //         },
    //         error: function(jqXHR, textStatus, errorThrown) {
    //             console.error('Error fetching weather data:', textStatus, errorThrown);
    //         }
    //     });
    // });
    //   ***************************************************************************************************************

    // ไฟลอยขึ้น หน้าเพิ่มเติม  ********************************************************************************
    // ฟังก์ชั่นสุ่มเลขสำหรับการลอยขึ้น-ลง
    function getRandomIntUpDown(min, max) {
        return Math.floor(Math.random() * (max - min + 1)) + min;
    }

    // ฟังก์ชั่นใช้แอนิเมชันลอยขึ้น-ลง
    function applyRandomAnimationUpdown(element) {
        const randomLeft = getRandomIntUpDown(0, 1900);
        const randomDuration = getRandomIntUpDown(6, 10);

        element.style.left = `${randomLeft}px`;
        element.style.animation = `fadeInOutDownUp ${randomDuration}s infinite`;
    }

    // นำฟังก์ชั่นไปใช้กับองค์ประกอบที่ต้องการลอยขึ้น-ลง
    document.querySelectorAll('.dot-updown-animation-1, .dot-updown-animation-2, .dot-updown-animation-3, .dot-updown-animation-4, .dot-updown-animation-5, .dot-updown-animation-6, .dot-updown-animation-7, .dot-updown-animation-8, .dot-updown-animation-9, .dot-updown-animation-10').forEach(applyRandomAnimationUpdown);

    //   ********************************************************************************

    // scrolltotop เลื่อนไปบนสุดของจอ  ********************************************************************************
    $(document).ready(function () {
        var scrollTopButton = $("#scroll-to-top");
        var scrollTopButtonOther = $("#scroll-to-top-other");
        var scrollBackButton = $("#scroll-to-back");

        $(window).scroll(function () {
            if ($(this).scrollTop() > 20) {
                scrollTopButton.fadeIn();
                scrollTopButtonOther.fadeIn();
                scrollBackButton.fadeIn();
            } else {
                scrollTopButton.fadeOut();
                scrollTopButtonOther.fadeOut();
                scrollBackButton.fadeOut();
            }
        });

        scrollTopButton.click(function () {
            $('html, body').animate({
                scrollTop: 0
            }, 'slow');
            return false;
        });

        scrollTopButtonOther.click(function () {
            $('html, body').animate({
                scrollTop: 0
            }, 'slow');
            return false;
        });

        scrollBackButton.click(function () {
            window.history.back();
            return false;
        });
    });

    function scrolltotopFunction() {
        $('html, body').animate({
            scrollTop: 0
        }, 'slow');
    }
    //   ********************************************************************************

    // สลับหน้าแสดงผล ข้าง banner ***************************************************************************
    function showImage(imageId) {
        var images = document.getElementsByClassName("chang_tmt_budjet");
        for (var i = 0; i < images.length; i++) {
            if (images[i].id === imageId) {
                images[i].style.display = "block";
            } else {
                images[i].style.display = "none";
            }
        }
    }
    //   ********************************************************************************

    // สุ่มวิกระพริบ และแสดงผล ข่าวจัดซื้อจัดจ้าง  ********************************************************************************
    // ฟังก์ชั่นสุ่มเลขสำหรับแอนิเมชันอื่นๆ
    // ฟังก์ชันสำหรับสุ่มตัวเลขระหว่าง min และ max รวมถึงทั้ง min และ max
    function getRandomIntOther(min, max) {
        return Math.floor(Math.random() * (max - min + 1)) + min;
    }

    // ฟังก์ชันสำหรับใช้สุ่มตำแหน่งและแสดงผลการแอนิเมชัน
    function applyRandomAnimation(element, animationName, randomDuration) {
        const randomLeft = getRandomIntOther(0, 1900); // ค่า left แบบสุ่ม
        const randomDelay = getRandomIntOther(0, 5); // การหน่วงเวลาแบบสุ่ม

        // กำหนดตำแหน่งซ้าย
        element.style.left = `${randomLeft}px`;

        // กำหนดแอนิเมชันใหม่โดยใช้การหน่วงเวลาและระยะเวลาที่สุ่ม
        element.style.animation = `${animationName} ${randomDuration}s ${randomDelay}s infinite`;
    }

    // // เลือกองค์ประกอบที่ต้องการให้มีการแสดงผลแอนิเมชัน moveBall
    // document.querySelectorAll('.ball-animation').forEach(element => {
    //     const randomDuration = getRandomIntOther(10, 15); // ระยะเวลาแอนิเมชันแบบสุ่ม
    //     applyRandomAnimation(element, 'moveBall', randomDuration);
    // });


    // ฟังก์ชันสำหรับใช้สุ่มตำแหน่งและแสดงผลการแอนิเมชัน
    function applyRandomAnimation(element, animationName, randomDuration) {
        const randomLeft = getRandomIntOther(0, 1900); // ค่า left แบบสุ่ม
        const randomDelay = getRandomIntOther(0, 10); // การหน่วงเวลาแบบสุ่ม

        // กำหนดตำแหน่งซ้าย
        element.style.left = `${randomLeft}px`;

        // กำหนดแอนิเมชันใหม่โดยใช้การหน่วงเวลาและระยะเวลาที่สุ่ม
        element.style.animation = `${animationName} ${randomDuration}s ${randomDelay}s infinite`;
    }

    // เลือกองค์ประกอบที่ต้องการให้มีการแสดงผลแอนิเมชัน movefade-down-to-top
    document.querySelectorAll('.movefade-down-to-top-animation1, .movefade-down-to-top-animation2, .movefade-down-to-top-animation3, .movefade-down-to-top-animation4, .movefade-down-to-top-animation5, .movefade-down-to-top-animation6, .movefade-down-to-top-animation7, .movefade-down-to-top-animation8, .movefade-down-to-top-animation9, .movefade-down-to-top-animation10, .movefade-down-to-top-animation11, .movefade-down-to-top-animation12, .movefade-down-to-top-animation13, .movefade-down-to-top-animation14, .movefade-down-to-top-animation15, .movefade-down-to-top-animation16, .movefade-down-to-top-animation17, .movefade-down-to-top-animation18, .movefade-down-to-top-animation19, .movefade-down-to-top-animation20, .movefade-down-to-top-animation21, .movefade-down-to-top-animation22, .movefade-down-to-top-animation23, .movefade-down-to-top-animation24').forEach(element => {
        const randomDuration = getRandomIntOther(25, 40); // ระยะเวลาแอนิเมชันแบบสุ่ม
        applyRandomAnimation(element, 'movefade-down-to-top', randomDuration);
    });

    // เลือกองค์ประกอบที่ต้องการให้มีการแสดงผลแอนิเมชัน movemovefade-down-to-top
    // document.querySelectorAll('.baimai-animation1, .baimai-animation2, .baimai-animation3, .baimai-animation4, .baimai-animation5, .baimai-animation6, .baimai-animation7, .baimai-animation8').forEach(element => {
    //     const randomDuration = getRandomIntOther(30, 50); // ระยะเวลาแอนิเมชันแบบสุ่ม
    //     applyRandomAnimation(element, 'movebaimai', randomDuration);
    // });

    // เลือกองค์ประกอบที่ต้องการให้มีการแสดงผลแอนิเมชัน fadeTopInDownOut
    document.querySelectorAll('.wel-light-animation-1, .wel-light-animation-2, .wel-light-animation-3, .wel-light-animation-4, .wel-light-animation-5, .wel-light-animation-6, .wel-light-animation-7, .wel-light-animation-8, .wel-light-animation-9, .wel-light-animation-10, .wel-light-animation-11, .wel-light-animation-12, .wel-light-animation-13, .wel-light-animation-14, .wel-light-animation-15').forEach(element => {
        const randomDuration = getRandomIntOther(15, 25); // ระยะเวลาแอนิเมชันแบบสุ่ม
        applyRandomAnimation(element, 'fadeTopInDownOut', randomDuration);
    });

    // เลือกองค์ประกอบที่ต้องการให้มีการแสดงผลแอนิเมชัน fadeInOut
    document.querySelectorAll('.star-news-animation-1, .star-news-animation-2, .star-news-animation-3, .star-news-animation-4, .star-news-animation-5, .star-news-animation-6, .star-news-animation-7, .star-news-animation-8, .star-news-animation-9, .star-news-animation-10, .star-news-animation-11, .star-news-animation-12, .star-news-animation-13, .star-news-animation-14, .star-news-animation-15').forEach(element => {
        const randomDuration = getRandomIntOther(6, 12); // ระยะเวลาแอนิเมชันแบบสุ่ม
        applyRandomAnimation(element, 'fadeInOut', randomDuration);
    });

    document.querySelectorAll('.dot-updown-animation-1, .dot-updown-animation-2, .dot-updown-animation-3, .dot-updown-animation-4, .dot-updown-animation-5, .dot-updown-animation-6, .dot-updown-animation-7, .dot-updown-animation-8, .dot-updown-animation-9, .dot-updown-animation-10').forEach(element => {
        const randomDuration = getRandomIntOther(6, 12); // ระยะเวลาแอนิเมชันแบบสุ่ม
        applyRandomAnimation(element, 'fadeInOutDownUp', randomDuration);
    });
    // ฟังก์ชั่นสุ่มเลขสำหรับแอนิเมชันอื่นๆ
    // function getRandomIntOther(min, max) {
    //     return Math.floor(Math.random() * (max - min + 1)) + min;
    // }

    // // ฟังก์ชั่นใช้แอนิเมชันอื่นๆ
    // function applyRandomAnimation(element) {
    //     const randomLeft = getRandomIntOther(0, 1900);
    //     const randomDuration = getRandomIntOther(5, 10);

    //     element.style.left = `${randomLeft}px`;
    //     element.style.animation = `fadeInOut ${randomDuration}s infinite`;
    // }

    // // นำฟังก์ชั่นไปใช้กับองค์ประกอบที่ต้องการแอนิเมชันอื่นๆ
    // document.querySelectorAll('.star-news-animation-1, .star-news-animation-2, .star-news-animation-3, .star-news-animation-4, .star-news-animation-5, .star-news-animation-6, .star-news-animation-7, .star-news-animation-8, .star-news-animation-9, .star-news-animation-10, .star-news-animation-11, .star-news-animation-12, .star-news-animation-13, .star-news-animation-14, .star-news-animation-15').forEach(applyRandomAnimation);
    //   ********************************************************************************

    // สุ่มวิกระพริบ และแสดงผล ข่าวประชาสัมพันธ์  ********************************************************************************
    // ฟังก์ชันสุ่มค่าในช่วงที่กำหนด
    // ฟังก์ชันสุ่มค่าในช่วงที่กำหนด
    function getRandomInt(min, max) {
        return Math.floor(Math.random() * (max - min + 1)) + min;
    }

    // ฟังก์ชันสุ่มตำแหน่งขององค์ประกอบ
    function randomizePosition(element) {
        var maxWidth = window.innerWidth; // ขนาดความกว้างของหน้าจอปัจจุบัน
        var maxHeight = window.innerHeight; // ขนาดความสูงของหน้าจอปัจจุบัน

        var randomMarginLeft = getRandomInt(0, maxWidth - element.offsetWidth);
        var randomMarginTop = getRandomInt(0, maxHeight - element.offsetHeight);

        element.style.marginLeft = randomMarginLeft + 'px';
        element.style.marginTop = randomMarginTop + 'px';
    }

    // ฟังก์ชันสุ่มการหน่วงเวลาเริ่มต้นแอนิเมชัน
    function randomizeAnimationDelay(element) {
        var randomDelay = getRandomInt(0, 3); // สุ่มการหน่วงเวลาระหว่าง 0 ถึง 5 วินาที
        element.style.animationDelay = randomDelay + 's';
    }

    // นำฟังก์ชันไปใช้กับองค์ประกอบที่ต้องการ
    var animations = document.querySelectorAll('.wipwap');
    animations.forEach(function (animation) {
        // สุ่มการหน่วงเวลาแอนิเมชัน
        randomizeAnimationDelay(animation);

        // กำหนดค่าเริ่มต้น
        randomizePosition(animation);

        // เพิ่ม event listener เพื่อตรวจสอบการเปลี่ยนแปลงของ opacity
        animation.addEventListener('animationiteration', function () {
            // ตั้งเวลาเพื่อให้เกิดการเปลี่ยนแปลงตำแหน่งเมื่อ opacity = 0
            setTimeout(function () {
                randomizePosition(animation);
            }, 1500); // 50% ของเวลาแอนิเมชัน 3s
        });
    });
    // วิบวับคงที่
    // function getRandomInt(min, max) {
    //     return Math.floor(Math.random() * (max - min + 1)) + min;
    // }

    // function randomizeAnimationDuration() {
    //     var minSeconds = 2; // วินาทีต่ำสุดที่ต้องการ
    //     var maxSeconds = 7; // วินาทีสูงสุดที่ต้องการ
    //     var randomSeconds = getRandomInt(minSeconds, maxSeconds);
    //     return randomSeconds + 's';
    // }

    // function randomizePosition(element) {
    //     var maxWidth = 1920; // กำหนดขนาดความกว้างสูงสุด 1920px
    //     var maxHeight = 500; // กำหนดขนาดความสูงสูงสุด 1000px

    //     var randomMarginLeft = getRandomInt(0, maxWidth - element.width);
    //     var randomMarginTop = getRandomInt(0, maxHeight - element.height);

    //     element.style.marginLeft = randomMarginLeft + 'px';
    //     element.style.marginTop = randomMarginTop + 'px';
    // }

    // var animations = document.querySelectorAll('.dot-news-animation-1, .dot-news-animation-2, .dot-news-animation-3, .dot-news-animation-4, .dot-news-animation-5, .dot-news-animation-6, .dot-news-animation-7, .dot-news-animation-8, .dot-news-animation-9, .dot-news-animation-10, .dot-news-animation-11, .dot-news-animation-12, .dot-news-animation-13, .dot-news-animation-14, .dot-news-animation-15');
    // animations.forEach(function(animation) {
    //     animation.style.animationDuration = randomizeAnimationDuration();
    //     randomizePosition(animation);
    // });
    //   ********************************************************************************
    // active  ********************************************************************************
    function addClickListenerToButtons(containerId, buttonClassName, activeClassName) {
        var $header = $('#' + containerId);
        var $btns = $header.find('.' + buttonClassName);

        $btns.on('click', function () {
            $header.find('.' + buttonClassName + '.' + activeClassName).removeClass(activeClassName);
            $(this).addClass(activeClassName);
        });
    }


    // เรียกใช้ฟังก์ชันสำหรับทั้ง 2 กรณี
    addClickListenerToButtons("myDIV", "public-button", "active-public");
    addClickListenerToButtons("myDIV2", "new-button", "active-new");
    addClickListenerToButtons("myDIV3", "dla-button", "active-dla");
    addClickListenerToButtons("myDIVRp", "rp-button", "active-rp");

    function setActiveButton(containerId) {
        var $header = $('#' + containerId);
        var buttonClasses = ['pm-button', 'pm-button-L', 'pm-button-R'];
        var activeClasses = ['active-pm', 'active-pm-L', 'active-pm-R'];

        buttonClasses.forEach(function (buttonClass, index) {
            var $btns = $header.find('.' + buttonClass);
            var activeClass = activeClasses[index];

            $btns.on('click', function () {
                // ลบ active class จากปุ่มทั้งหมด
                buttonClasses.forEach(function (cls, idx) {
                    $header.find('.' + cls).removeClass(activeClasses[idx]);
                });

                // เพิ่ม active class ให้กับปุ่มที่ถูกคลิก
                $(this).addClass(activeClass);
            });
        });
    }

    // เรียกใช้ฟังก์ชันสำหรับ myDIV4
    setActiveButton("myDIVPm");

    // *****************************************************************************************

    // รูปภาพ preview *********************************************************************
    $(document).ready(function () {
        lightbox.option({
            'resizeDuration': 200,
            'wrapAround': true
        });
    });
    // **************************************************************************************
    // กดแล้วเปลี่ยนรูป navbar กลาง *********************************************************************
    $(document).ready(function () {
        function changeImage(src, element) {
            element.attr('src', src);
        }

        function restoreImage(src, element) {
            element.attr('src', src);
        }

        function handleButtonClick(event) {
            var clickedButton = $(event.currentTarget);
            var dropdownContent = clickedButton.next('.dropdown-content');

            // รีเซ็ตรูปทุก button ใน dropdown เป็นรูปปกติ
            $('.dropdown-trigger img[data-active-src]').each(function () {
                restoreImage($(this).data('non-active-src'), $(this));
            });

            // เปลี่ยนรูปของ button ที่ถูกคลิกเป็นรูป active
            clickedButton.find('img[data-active-src]').each(function () {
                changeImage($(this).data('active-src'), $(this));
            });
        }

        $('.dropdown-trigger').on('click', handleButtonClick);

        // สร้าง Event Listener สำหรับส่วนที่ไม่ใช่ button
        $(document).on('click', function (event) {
            var target = $(event.target);

            // ตรวจสอบว่าคลิกอยู่นอกเขตของ button หรือไม่
            if (!target.closest('.dropdown-container').length) {
                // คืนค่ารูปภาพเดิม
                $('.dropdown-trigger img[data-active-src]').each(function () {
                    restoreImage($(this).data('non-active-src'), $(this));
                });
            }
        });
    });
    // ปุ่มย้อนกลับของยกเลิก *********************************************************************
    function goBack() {
        window.history.back();
    }
    // **************************************************************************************
    // เมื่อ reCAPTCHA ผ่านการตรวจสอบหน้า home ************************************
    // v2
    // function enableLoginButton() {
    //     document.getElementById("loginBtn").removeAttribute("disabled");
    // }
    // v3
    // function onSubmit(token) {
    //     document.getElementById("loginBtn").removeAttribute("disabled");
    // }
    // grecaptcha.ready(function() {
    //     grecaptcha.execute('6LcfiLYpAAAAAI7_U3nkRRxKF7e8B_fwOGqi7g6x', {
    //         action: 'submit'
    //     }).then(onSubmit);
    // });
    // v3 ล่าสุด
    function onSubmit(token) {
        document.getElementById("reCAPTCHA3").submit();
    }
    // ****************************************************************************

    // ตัวเลื่อนด้านล่างสุด หน้า home ******************************************************
    // ✅ โค้ดใหม่ที่ปลอดภัย
    $(document).ready(function () {
        // ตรวจสอบว่า slick library โหลดหรือไม่
        if (typeof $.fn.slick !== 'undefined') {
            $(".slick-carousel").slick({
                slidesToShow: 4,
                slidesToScroll: 1,
                autoplay: true,
                autoplaySpeed: 5000,
                arrows: true,
                prevArrow: '<img src="docs/pre-home.png" class="slick-prev">',
                nextArrow: '<img src="docs/next-home.png" class="slick-next">',
            });
            console.log('✅ Slick Carousel loaded successfully');
        } else {
            console.warn('⚠️ Slick library not found - carousel disabled');

            // ซ่อน carousel หรือแสดงแบบธรรมดา
            $(".slick-carousel").css({
                'display': 'flex',
                'overflow-x': 'auto',
                'scroll-behavior': 'smooth'
            });
        }
    });
    // ****************************************************************************

    // กดแล้วเปลี่ยนหน้า *******************************************************

    // เพิ่ม event listener สำหรับการเลือกประเภทของการร้องเรียน
    $(document).ready(function () {
        // เพิ่ม event listener สำหรับการเลือกประเภทของการร้องเรียน
        $('#ChangPagesComplain').change(function () {
            var selectedValue = $(this).val();
            console.log('Selected Value:', selectedValue);

            // ทำการ redirect ไปยัง URL ที่ต้องการ
            if (selectedValue) {
                var controllerUrl = ''; // URL ที่ต้องการไป
                switch (selectedValue) {
                    case 'corruption':
                        controllerUrl = '<?php echo site_url('Pages/adding_corruption'); ?>';
                        break;
                    case 'suggestions':
                        controllerUrl = '<?php echo site_url('Pages/adding_suggestions'); ?>';
                        break;
                    case 'complain':
                        controllerUrl = '<?php echo site_url('Pages/adding_complain'); ?>';
                        break;
                    case 'follow-complain':
                        controllerUrl = '<?php echo site_url('Pages/follow_complain'); ?>';
                        break;
                    case 'esv_ods':
                        controllerUrl = '<?php echo site_url('Pages/adding_esv_ods'); ?>';
                        break;
                }

                console.log('Controller URL:', controllerUrl);

                if (controllerUrl) {
                    window.location.href = controllerUrl;
                }
            }
        });
    });
    // ****************************************************************************

    // แสดงรูปภาพใหญ่ *******************************************************
    $(function () {
        "use strict";

        $(".popup img").click(function () {
            var $src = $(this).attr("src");
            $(".show").fadeIn();
            $(".img-show img").attr("src", $src);
        });

        $("span, .overlay").click(function () {
            $(".show").fadeOut();
        });

    });

    // JavaScript to adjust popup position on scroll
    document.addEventListener('scroll', function () {
        var imgShow = document.querySelector('.show .img-show');
        imgShow.style.top = window.innerHeight / 2 + window.scrollY + 'px';
    });

    // ****************************************************************************




    // function setScale() {
    //     const screenWidth = window.innerWidth;
    //     const mainElement = document.querySelector('main');

    //     if (screenWidth <= 768) {
    //         mainElement.style.transform = 'scale(0.22)';
    //     } else if (screenWidth > 768 && screenWidth <= 1420) {
    //         mainElement.style.transform = 'scale(0.67)';
    //     } else if (screenWidth > 1421 && screenWidth <= 1520) {
    //         mainElement.style.transform = 'scale(0.72)';
    //     } else {
    //         mainElement.style.transform = 'scale(1)';
    //     }
    // }
    // window.addEventListener('load', setScale);
    // window.addEventListener('resize', setScale);

    // ฟังก์ชันนี้จะถูกเรียกเมื่อคลิกที่ปุ่ม "แสดงผล"
    function showContentLikeDetail() {
        var contentDetail = document.querySelector('.content-like-detail');

        if (contentDetail) {
            // กำหนดให้ถ้าซ่อนอยู่ให้แสดง และถ้าแสดงอยู่ให้ซ่อน
            contentDetail.style.display = contentDetail.style.display === 'none' ? 'block' : 'none';
            // // แสดง div ที่ถูกซ่อนไว้
            // contentDetail.style.display = 'block';
        }
    }

    // navmid กดแล้วเปลี่ยนรูปภาพ *******************************************************

    // $(document).ready(function() {
    //     // เมื่อคลิกปุ่ม dropdown
    //     $('.dropdown-trigger').click(function() {
    //         // ถ้าปุ่มที่ถูกคลิกไม่มี class 'active' ให้ทำการลบ class 'active' จากทุก dropdown-trigger
    //         if (!$(this).hasClass('active')) {
    //             $('.dropdown-trigger').removeClass('active');

    //             // เปลี่ยนรูปภาพทุก dropdown-trigger เป็นรูปปกติ
    //             $('.dropdown-trigger img').attr('src', function() {
    //                 return $(this).attr('src').replace('-hover.png', '.png');
    //             });

    //             // เปลี่ยนรูปภาพของ dropdown-trigger ที่ถูกคลิกเป็นรูป active
    //             $(this).find('img').attr('src', function() {
    //                 return $(this).attr('src').replace('.png', '-hover.png');
    //             });
    //         }
    //     });

    //     // เมื่อคลิกที่ส่วนอื่นของหน้าเว็บ
    //     $(document).click(function(event) {
    //         // ถ้าคลิกที่ส่วนที่ไม่ใช่ dropdown-trigger ให้ลบ class 'active' และเปลี่ยนรูปภาพทุก dropdown-trigger เป็นรูปปกติ
    //         if (!$(event.target).closest('.dropdown-trigger').length) {
    //             $('.dropdown-trigger').removeClass('active');
    //             $('.dropdown-trigger img').attr('src', function() {
    //                 return $(this).attr('src').replace('-hover.png', '.png');
    //             });
    //         }
    //     });
    // });
    // *****************************************************************************


    // news ข่าว tab-link *******************************************************
    $(document).ready(function () {
        // เรียกใช้ฟังก์ชัน openTab เพื่อให้ Tab 1 เป็น active ทันทีหลังจากโหลดหน้าเว็บ
        openTab('tab1');
        // เรียกใช้ฟังก์ชัน openTabTwo เพื่อให้ Tab 1 เป็น active ทันทีหลังจากโหลดหน้าเว็บ
        openTabTwo('tabtwo1');
        // เรียกใช้ฟังก์ชัน openTabDla เพื่อให้ Tab 1 เป็น active ทันทีหลังจากโหลดหน้าเว็บ
        openTabDla('tabDla1');
        // เรียกใช้ฟังก์ชัน openTabPm เพื่อให้ Tab 1 เป็น active ทันทีหลังจากโหลดหน้าเว็บ
        openTabPm('tabPm1');
        // เรียกใช้ฟังก์ชัน openTabRp เพื่อให้ Tab 1 เป็น active ทันทีหลังจากโหลดหน้าเว็บ
        openTabRp('tabRp1');
    });

    function openTab(tabId) {
        // ซ่อนทุก tab-content ทุกตัว
        $('.tab-content').hide();

        // แสดง tab-content ที่ถูกคลิก
        $('#' + tabId).show();

        // ทำการเปลี่ยนรูปภาพทุก tab-link เป็นรูปปกติ
        $('.tab-link img').each(function () {
            $(this).attr('src', $(this).attr('src').replace('-hover.png', '.png'));
        });

        // ทำการเปลี่ยนรูปภาพของ tab-link ที่ถูกคลิกเป็นรูป active
        $('.tab-link[onclick="openTab(\'' + tabId + '\')"] img').attr('src', function (_, oldSrc) {
            return oldSrc.replace('.png', '-hover.png');
        });
    }

    function openTabTwo(tabId) {
        // ซ่อนทุก tab-content-two ทุกตัว
        $('.tab-content-two').hide();

        // แสดง tab-content-two ที่ถูกคลิก
        $('#' + tabId).show();

        // ทำการเปลี่ยนรูปภาพทุก tab-link เป็นรูปปกติ
        $('.tab-link-two img').each(function () {
            $(this).attr('src', $(this).attr('src').replace('-hover.png', '.png'));
        });

        // ทำการเปลี่ยนรูปภาพของ tab-link ที่ถูกคลิกเป็นรูป active
        $('.tab-link-two[onclick="openTabTwo(\'' + tabId + '\')"] img').attr('src', function (_, oldSrc) {
            return oldSrc.replace('.png', '-hover.png');
        });
    }

    function openTabDla(tabId) {
        // ซ่อนทุก tab-content-dla ทุกตัว
        $('.tab-content-dla').hide();

        // แสดง tab-content-dla ที่ถูกคลิก
        $('#' + tabId).show();

        // ทำการเปลี่ยนรูปภาพทุก tab-link-dla เป็นรูปปกติ
        $('.tab-link-dla img').each(function () {
            $(this).attr('src', $(this).attr('src').replace('-hover.png', '.png'));
        });

        // ทำการเปลี่ยนรูปภาพของ tab-link-dla ที่ถูกคลิกเป็นรูป active
        $('.tab-link-dla[onclick="openTabDla(\'' + tabId + '\')"] img').attr('src', function (_, oldSrc) {
            return oldSrc.replace('.png', '-hover.png');
        });
    }

    function openTabPm(tabId) {
        // ซ่อนทุก tab-content-pm ทุกตัว
        $('.tab-content-pm').hide();

        // แสดง tab-content-pm ที่ถูกคลิก
        $('#' + tabId).show();

        // ทำการเปลี่ยนรูปภาพทุก tab-link-pm เป็นรูปปกติ
        $('.tab-link-pm img').each(function () {
            $(this).attr('src', $(this).attr('src').replace('-hover.png', '.png'));
        });

        // ทำการเปลี่ยนรูปภาพของ tab-link-pm ที่ถูกคลิกเป็นรูป active
        $('.tab-link-pm[onclick="openTabPm(\'' + tabId + '\')"] img').attr('src', function (_, oldSrc) {
            return oldSrc.replace('.png', '-hover.png');
        });
    }

    function openTabRp(tabId) {
        // ซ่อนทุก tab-content-rp ทุกตัว
        $('.tab-content-rp').hide();

        // แสดง tab-content-rp ที่ถูกคลิก
        $('#' + tabId).show();

        // ทำการเปลี่ยนรูปภาพทุก tab-link-rp เป็นรูปปกติ
        $('.tab-link-rp img').each(function () {
            $(this).attr('src', $(this).attr('src').replace('-hover.png', '.png'));
        });

        // ทำการเปลี่ยนรูปภาพของ tab-link-rp ที่ถูกคลิกเป็นรูป active
        $('.tab-link-rp[onclick="openTabRp(\'' + tabId + '\')"] img').attr('src', function (_, oldSrc) {
            return oldSrc.replace('.png', '-hover.png');
        });
    }

    // *****************************************************************************

    // navbar กิจกรรม / ผลงาน *******************************************************
    $(document).ready(function () {
        $('.dropdown-trigger').each(function () {
            var dropdownTrigger = $(this);
            var dropdownContent = dropdownTrigger.next(); // Assuming the dropdown is a sibling element

            dropdownTrigger.on('click', function () {
                if (dropdownContent.css('display') === 'block') {
                    dropdownContent.css('display', 'none');
                } else {
                    dropdownContent.css('display', 'block');
                }
            });

            $(document).on('click', function (e) {
                if (!dropdownContent.is(e.target) && !dropdownTrigger.is(e.target) && dropdownContent.has(e.target).length === 0 && dropdownTrigger.has(e.target).length === 0) {
                    dropdownContent.css('display', 'none');
                }
            });
        });
    });

    // *****************************************************************************

    // navbar คลิกแล้วเปลี่ยนรูปภาพ  *******************************************************
    function changeImage(src) {
        var img = event.target || event.srcElement;
        img.src = src;
    }

    function restoreImage(src) {
        var img = event.target || event.srcElement;
        img.src = src;
    }

    // ความพึงพอใจเว็บ กดไลค์ like
    $(document).ready(function () {
        $('#confirmButton').click(function () {
            // แสดงส่วนที่คุณต้องการ
            $('#submitSection').show();
            // ซ่อนปุ่ม "ยืนยัน"
            $(this).hide();
        });
    });

    // เมื่อ reCAPTCHA ผ่านการตรวจสอบ
    // document.getElementById("confirmButton").addEventListener("click", function() {
    //     grecaptcha.ready(function() {
    //         grecaptcha.execute('รหัสของคุณ', {
    //             action: 'submit'
    //         }).then(function(token) {
    //             // ถ้าต้องการส่ง token ไปยังเซิร์ฟเวอร์สำหรับการยืนยัน
    //             // คุณสามารถทำได้ที่นี่
    //             enableSubmit(); // เรียกใช้ฟังก์ชันสำหรับเปิดใช้งานปุ่ม Submit
    //         });
    //     });
    // });

    function enableSubmit() {
        document.getElementById("SubmitLike").removeAttribute("disabled");
    }

    function initializeBrmSwiper() {
        const container = document.querySelector('.myBrmSwiper');
        if (!container) return;

        const slides = container.querySelectorAll('.swiper-slide');
        const slideCount = slides.length;

        console.log(`🔍 BRM Swiper: พบ ${slideCount} slides`);

        // กำหนด config ตามจำนวน slides
        let config = {
            spaceBetween: 20,
            navigation: {
                nextEl: '.brm-button-next',
                prevEl: '.brm-button-prev',
            },
            breakpoints: {
                320: {
                    slidesPerView: Math.min(2, slideCount),
                    spaceBetween: 10
                },
                640: {
                    slidesPerView: Math.min(3, slideCount),
                    spaceBetween: 15
                },
                1024: {
                    slidesPerView: Math.min(4, slideCount),
                    spaceBetween: 20
                }
            }
        };

        // ตั้งค่า loop และ autoplay ตามจำนวน slides
        if (slideCount > 4) {
            config.slidesPerView = 4;
            config.loop = true;
            config.autoplay = {
                delay: 3000,
                disableOnInteraction: false,
            };
            config.slidesPerGroup = 1;
        } else {
            config.slidesPerView = slideCount;
            config.loop = false;
            config.autoplay = false;
        }

        try {
            const brmSwiper = new Swiper('.myBrmSwiper', config);
            console.log(`✅ BRM Swiper สร้างสำเร็จ: ${slideCount} slides, loop: ${config.loop}`);
            return brmSwiper;
        } catch (error) {
            console.error('❌ BRM Swiper Error:', error);
            return null;
        }
    }

    function initializeMySwiper() {
        const container = document.querySelector('.mySwiper');
        if (!container) return;

        const slides = container.querySelectorAll('.swiper-slide');
        const slideCount = slides.length;

        console.log(`🔍 My Swiper: พบ ${slideCount} slides`);

        let config = {
            spaceBetween: 20,
            navigation: {
                nextEl: '.custom-button-next',
                prevEl: '.custom-button-prev',
            }
        };

        // ตั้งค่าตามจำนวน slides
        if (slideCount > 4) {
            config.slidesPerView = 4;
            config.loop = true;
            config.autoplay = {
                delay: 4000,
                disableOnInteraction: false,
            };
        } else {
            config.slidesPerView = slideCount;
            config.loop = false;
            config.autoplay = false;
        }

        try {
            const swiper = new Swiper('.mySwiper', config);
            console.log(`✅ My Swiper สร้างสำเร็จ: ${slideCount} slides, loop: ${config.loop}`);
            return swiper;
        } catch (error) {
            console.error('❌ My Swiper Error:', error);
            return null;
        }
    }

    // 🚀 เรียกใช้ทั้งหมดเมื่อ DOM พร้อม
    document.addEventListener('DOMContentLoaded', function () {
        // รอให้ Swiper library โหลดเสร็จ
        if (typeof Swiper !== 'undefined') {
            initializeBrmSwiper();
            initializeActivitySlider();
            initializeMySwiper();
        } else {
            // รอ Swiper โหลด
            let retryCount = 0;
            const checkSwiper = setInterval(() => {
                retryCount++;
                if (typeof Swiper !== 'undefined') {
                    clearInterval(checkSwiper);
                    initializeBrmSwiper();
                    initializeActivitySlider();
                    initializeMySwiper();
                    console.log('🎯 ทุก Swiper เริ่มต้นสำเร็จ');
                } else if (retryCount >= 20) {
                    clearInterval(checkSwiper);
                    console.log('❌ Swiper library ไม่พบหลังจากรอ 10 วินาที');
                }
            }, 500);
        }
    });

    // 🛠️ ฟังก์ชันสำหรับรีสตาร์ท Swiper (ใช้เมื่อต้องการ)
    window.restartAllSwipers = function () {
        // ทำลาย Swiper เก่า
        document.querySelectorAll('.swiper-container').forEach(container => {
            if (container.swiper) {
                container.swiper.destroy(true, true);
            }
        });

        // เริ่มใหม่
        setTimeout(() => {
            initializeBrmSwiper();
            initializeActivitySlider();
            initializeMySwiper();
        }, 100);
    };

    // หากคุณใช้ JavaScript เพื่อกำหนดตำแหน่ง
    var customButtonPrev = document.querySelector('.custom-button-prev');
    var customButtonNext = document.querySelector('.custom-button-next');

    //ซ้ำกับ modal ที่อยู่ใน page ให้ใช้ ที่อยู่ ใน page แทน
    // $(document).ready(function() {
    //  <?php if ($this->session->flashdata('save_success')) { ?>
        //     Swal.fire({
        // position: 'top-end',
        //        icon: 'success',
        //         title: 'บันทึกข้อมูลสำเร็จ',
        //         showConfirmButton: false,
        //        timer: 1500
        //  <?php } ?>
    //   });

    $(document).ready(function () {
        <?php if ($this->session->flashdata('save_required')) { ?>
            Swal.fire({
                icon: 'warning',
                title: 'ตรวจพบปัญหา',
                text: 'กรุณากรอกข้อมูลที่มี ให้ครบทุกช่อง',
                footer: '<a href="#">ติดต่อผู้ดูแลระบบ?</a>'
            })
        <?php } ?>
    });

    $(document).ready(function () {
        <?php if ($this->session->flashdata('save_id_crad')) { ?>
            Swal.fire({
                icon: 'warning',
                title: 'ตรวจพบปัญหา',
                text: 'กรุณากรอก หมายเลขประจำตัวประชาชน',
                footer: '<a href="#">ติดต่อผู้ดูแลระบบ?</a>'
            })
        <?php } ?>
    });

    //     $(document).ready(function () {
    //         <?php if ($this->session->flashdata('save_error')) { ?>
        //             // เก็บข้อมูล debug
        //             const debugInfo = {
        //                 timestamp: new Date().toLocaleString('th-TH'),
        //                 error_type: 'save_error',
        //                 memory_limit: '<?php echo ini_get("memory_limit"); ?>',
        //                 upload_max_filesize: '<?php echo ini_get("upload_max_filesize"); ?>',
        //                 post_max_size: '<?php echo ini_get("post_max_size"); ?>',
        //                 max_execution_time: '<?php echo ini_get("max_execution_time"); ?>',
        //                 current_memory_usage: '<?php echo round(memory_get_usage(true) / 1024 / 1024, 2); ?> MB',
        //                 peak_memory_usage: '<?php echo round(memory_get_peak_usage(true) / 1024 / 1024, 2); ?> MB',
        //                 php_version: '<?php echo PHP_VERSION; ?>',
        //                 user_agent: navigator.userAgent,
        //                 screen_resolution: screen.width + 'x' + screen.height,
        //                 available_memory: navigator.deviceMemory ? navigator.deviceMemory + ' GB' : 'ไม่ทราบ',
        //                 connection_type: navigator.connection ? navigator.connection.effectiveType : 'ไม่ทราบ'
        //             };

        //             Swal.fire({
        //                 icon: 'error',
        //                 title: 'ตรวจพบปัญหา',
        //                 html: `
    //                 <div style="text-align: left;">
    //                     <p><strong>หน่วยความจำของท่านเต็ม!</strong></p>
    //                     <hr>
    //                     <small>
    //                         <strong>ข้อมูล Debug:</strong><br>
    //                         📊 Memory Limit: ${debugInfo.memory_limit}<br>
    //                         📈 Memory ที่ใช้: ${debugInfo.current_memory_usage}<br>
    //                         📊 Peak Memory: ${debugInfo.peak_memory_usage}<br>
    //                         📁 Max Upload: ${debugInfo.upload_max_filesize}<br>
    //                         📋 Max POST: ${debugInfo.post_max_size}<br>
    //                         ⏱️ Execution Time: ${debugInfo.max_execution_time}s<br>
    //                         🖥️ Device Memory: ${debugInfo.available_memory}<br>
    //                         🌐 Connection: ${debugInfo.connection_type}<br>
    //                         🕐 เวลา: ${debugInfo.timestamp}
    //                     </small>
    //                 </div>
    //             `,
        //                 width: '500px',
        //                 showCancelButton: true,
        //                 confirmButtonText: '📋 คัดลอก Debug Info',
        //                 cancelButtonText: 'ปิด',
        //                 footer: '<a href="#" onclick="console.log(\'Debug Info:\', ' + JSON.stringify(debugInfo) + ')">ดู Debug ใน Console</a>'
        //             }).then((result) => {
        //                 if (result.isConfirmed) {
        //                     // คัดลอกข้อมูล debug ไปยัง clipboard
        //                     const debugText = `
    // Debug Information - Q&A System Error
    // =====================================
    // เวลา: ${debugInfo.timestamp}
    // ประเภทข้อผิดพลาด: ${debugInfo.error_type}

    // การตั้งค่า PHP:
    // - Memory Limit: ${debugInfo.memory_limit}
    // - Upload Max Filesize: ${debugInfo.upload_max_filesize}
    // - POST Max Size: ${debugInfo.post_max_size}
    // - Max Execution Time: ${debugInfo.max_execution_time}s

    // การใช้งาน Memory:
    // - Current Usage: ${debugInfo.current_memory_usage}
    // - Peak Usage: ${debugInfo.peak_memory_usage}

    // ข้อมูลอุปกรณ์:
    // - User Agent: ${debugInfo.user_agent}
    // - Screen Resolution: ${debugInfo.screen_resolution}
    // - Device Memory: ${debugInfo.available_memory}
    // - Connection Type: ${debugInfo.connection_type}
    // - PHP Version: ${debugInfo.php_version}
    //                 `.trim();

        //                     navigator.clipboard.writeText(debugText).then(() => {
        //                         Swal.fire({
        //                             icon: 'success',
        //                             title: 'คัดลอกแล้ว!',
        //                             text: 'ข้อมูล Debug ถูกคัดลอกไปยัง Clipboard แล้ว',
        //                             timer: 2000,
        //                             showConfirmButton: false
        //                         });
        //                     }).catch(() => {
        //                         // Fallback ถ้าคัดลอกไม่ได้
        //                         const textArea = document.createElement('textarea');
        //                         textArea.value = debugText;
        //                         document.body.appendChild(textArea);
        //                         textArea.select();
        //                         document.execCommand('copy');
        //                         document.body.removeChild(textArea);

        //                         Swal.fire({
        //                             icon: 'success',
        //                             title: 'คัดลอกแล้ว!',
        //                             text: 'ข้อมูล Debug ถูกคัดลอกแล้ว (Fallback)',
        //                             timer: 2000,
        //                             showConfirmButton: false
        //                         });
        //                     });
        //                 }
        //             });

        //             // เก็บ debug info ไว้ใน console และ localStorage (ถ้าใช้ได้)
        //             console.group('🐛 Q&A System Debug Information');
        //             console.error('Save Error Occurred');
        //             console.table(debugInfo);
        //             console.groupEnd();

        //             // พยายามเก็บใน localStorage (ถ้าอยู่ในสภาพแวดล้อมที่รองรับ)
        //             try {
        //                 const existingLogs = JSON.parse(localStorage.getItem('qa_debug_logs') || '[]');
        //                 existingLogs.push(debugInfo);
        //                 // เก็บแค่ 10 log ล่าสุด
        //                 if (existingLogs.length > 10) {
        //                     existingLogs.splice(0, existingLogs.length - 10);
        //                 }
        //                 localStorage.setItem('qa_debug_logs', JSON.stringify(existingLogs));
        //             } catch (e) {
        //                 console.warn('ไม่สามารถเก็บ debug log ใน localStorage ได้:', e);
        //             }

        //         <?php } ?>
    //     });


    $(document).ready(function () {
        <?php if ($this->session->flashdata('save_maxsize')) { ?>
            Swal.fire({
                icon: 'error',
                title: 'ตรวจพบปัญหา',
                text: 'ขนาดรูปภาพต้องไม่เกิน 1.5MB!',
                footer: '<a href="#">ติดต่อผู้ดูแลระบบ?</a>'
            })
        <?php } ?>
    });

    $(document).ready(function () {
        <?php if ($this->session->flashdata('del_success')) { ?>
            Swal.fire({
                // position: 'top-end',
                icon: 'success',
                title: 'ลบข้อมูลสำเร็จ',
                showConfirmButton: false,
                timer: 1500
            })
        <?php } ?>
    });

    $(document).ready(function () {
        <?php if ($this->session->flashdata('save_again')) { ?>
            Swal.fire({
                icon: 'warning',
                title: 'ตรวจพบปัญหา',
                text: 'มีข้อมูลอยู่แล้ว!',
                footer: '<a href="#">ติดต่อผู้ดูแลระบบ?</a>'
            })
        <?php } ?>
    });

    $(document).ready(function () {
        <?php if ($this->session->flashdata('save_vulgar')) { ?>
            Swal.fire({
                icon: 'warning',
                title: 'ตรวจพบปัญหา',
                text: 'ข้อความของท่านไม่มีเหมาะสม!',
                footer: '<a href="#">ติดต่อผู้ดูแลระบบ?</a>'
            })
        <?php } ?>
    });
    $(document).ready(function () {
        <?php if ($this->session->flashdata('password_mismatch')) { ?>
            Swal.fire({
                icon: 'warning',
                title: 'ตรวจพบปัญหา',
                text: 'รหัสผ่านไม่ตรงกัน!',
                footer: '<a href="#">ติดต่อผู้ดูแลระบบ?</a>'
            })
        <?php } ?>
    });

    $(document).ready(function () {
        <?php if ($this->session->flashdata('plz_save')) { ?>
            Swal.fire({
                icon: 'warning',
                title: 'ตรวจพบปัญหา',
                text: 'กรุณาเลือกข้อมูลให้ครบทุกช่อง',
                footer: '<a href="#">ติดต่อผู้ดูแลระบบ?</a>'
            })
        <?php } ?>
    });

    /* //////////////// start  ita-popup back-drop      ///////////////////// */

    document.addEventListener('DOMContentLoaded', function () {
        // Configuration
        const config = {
            showDelay: 800,
            slideDelay: 400,
            autoSlideInterval: 6000,
            loop: true
        };

        // Get all popups
        const popups = document.querySelectorAll('.ita-popup-backdrop');
        let currentPopupIndex = 0;
        let isAnimating = false;
        let autoSlideTimer = null;
        let touchStartX = 0;
        let touchEndX = 0;

        function initPopupSystem() {
            if (popups.length === 0) return;

            // สร้าง popup portal เพื่อไม่ให้ถูกกระทบจาก CSS ของ main
            createPopupPortal();

            popups.forEach((popup, index) => {
                const closeBtn = popup.querySelector('.ita-popup-close-btn');
                const popupLink = popup.querySelector('.ita-popup-link');
                const container = popup.querySelector('.ita-popup-container');

                // Create navigation dots
                const dotsContainer = popup.querySelector('.ita-dots');
                popups.forEach((_, dotIndex) => {
                    const dot = document.createElement('div');
                    dot.className = `ita-dot ${dotIndex === 0 ? 'active' : ''}`;
                    dot.setAttribute('role', 'button');
                    dot.setAttribute('aria-label', `ไปที่สไลด์ ${dotIndex + 1}`);
                    dot.setAttribute('tabindex', '0');

                    dot.addEventListener('click', () => goToSlide(dotIndex));
                    dot.addEventListener('keydown', (e) => {
                        if (e.key === 'Enter' || e.key === ' ') {
                            e.preventDefault();
                            goToSlide(dotIndex);
                        }
                    });

                    dotsContainer.appendChild(dot);
                });

                // Close button handler
                closeBtn.addEventListener('click', (e) => {
                    e.preventDefault();
                    e.stopPropagation();
                    hideAllPopups();
                    stopAutoSlide();
                });

                // Background click handler
                popup.addEventListener('click', (e) => {
                    if (e.target.classList.contains('ita-popup-backdrop')) {
                        hideAllPopups();
                        stopAutoSlide();
                    }
                });

                // Prevent popup from closing when clicking content
                popupLink?.addEventListener('click', (e) => {
                    e.stopPropagation();
                });

                container?.addEventListener('click', (e) => {
                    e.stopPropagation();
                });

                // Touch events for mobile swipe
                setupTouchEvents(container);
            });

            // Show first popup
            setTimeout(() => {
                showPopup(popups[0]);
                startAutoSlide();
            }, config.showDelay);

            updateNavigation();
        }

        function createPopupPortal() {
            // ย้าย popup ไปที่ body เพื่อไม่ให้ถูกกระทบจาก CSS ของ main
            popups.forEach(popup => {
                if (popup.parentNode !== document.body) {
                    document.body.appendChild(popup);
                }
            });
        }

        function setupTouchEvents(container) {
            if (!container) return;

            container.addEventListener('touchstart', (e) => {
                touchStartX = e.touches[0].clientX;
                stopAutoSlide();
            }, {
                passive: true
            });

            container.addEventListener('touchend', (e) => {
                touchEndX = e.changedTouches[0].clientX;
                handleSwipe();
                startAutoSlide();
            }, {
                passive: true
            });
        }

        function handleSwipe() {
            const swipeThreshold = 50;
            const deltaX = touchStartX - touchEndX;

            if (Math.abs(deltaX) > swipeThreshold) {
                if (deltaX > 0) {
                    // Swipe left - next slide
                    const nextIndex = (currentPopupIndex + 1) % popups.length;
                    slideToIndex(nextIndex, 'left');
                } else {
                    // Swipe right - previous slide
                    const prevIndex = currentPopupIndex === 0 ? popups.length - 1 : currentPopupIndex - 1;
                    slideToIndex(prevIndex, 'right');
                }
            }
        }

        function showPopup(popup) {
            if (!popup) return;

            // Hide all other popups first
            hideAllPopups();

            popup.classList.add('show');
            popup.setAttribute('aria-hidden', 'false');

            // Focus management for accessibility
            setTimeout(() => {
                const closeBtn = popup.querySelector('.ita-popup-close-btn');
                closeBtn?.focus();
            }, 100);

            updateNavigation();
        }

        function hideAllPopups() {
            popups.forEach(popup => {
                popup.classList.remove('show');
                popup.setAttribute('aria-hidden', 'true');
            });
        }

        function startAutoSlide() {
            stopAutoSlide();
            autoSlideTimer = setInterval(() => {
                const nextIndex = (currentPopupIndex + 1) % popups.length;
                slideToIndex(nextIndex, 'left');
            }, config.autoSlideInterval);
        }

        function stopAutoSlide() {
            if (autoSlideTimer) {
                clearInterval(autoSlideTimer);
                autoSlideTimer = null;
            }
        }

        function goToSlide(index) {
            if (isAnimating || index === currentPopupIndex) return;

            stopAutoSlide();
            const direction = index > currentPopupIndex ? 'left' : 'right';
            slideToIndex(index, direction);
            startAutoSlide();
        }

        function slideToIndex(newIndex, direction) {
            if (isAnimating || newIndex < 0 || newIndex >= popups.length) return;

            isAnimating = true;

            // Hide current popup
            hideAllPopups();

            // Show new popup with animation
            const newPopup = popups[newIndex];
            showPopup(newPopup);

            const container = newPopup.querySelector('.ita-popup-container');
            if (container) {
                container.classList.add(`slide-${direction}-enter`);

                setTimeout(() => {
                    container.classList.remove(`slide-${direction}-enter`);
                    currentPopupIndex = newIndex;
                    updateNavigation();
                    isAnimating = false;
                }, config.slideDelay);
            } else {
                currentPopupIndex = newIndex;
                updateNavigation();
                isAnimating = false;
            }
        }

        function updateNavigation() {
            popups.forEach(popup => {
                const dots = popup.querySelectorAll('.ita-dot');
                dots.forEach((dot, index) => {
                    const isActive = index === currentPopupIndex;
                    dot.classList.toggle('active', isActive);
                    dot.setAttribute('aria-pressed', isActive.toString());
                });
            });
        }

        // Enhanced keyboard navigation
        document.addEventListener('keydown', (e) => {
            const visiblePopup = document.querySelector('.ita-popup-backdrop.show');
            if (!visiblePopup) return;

            switch (e.key) {
                case 'Escape':
                    hideAllPopups();
                    stopAutoSlide();
                    break;
                case 'ArrowLeft':
                    e.preventDefault();
                    const prevIndex = currentPopupIndex === 0 ? popups.length - 1 : currentPopupIndex - 1;
                    goToSlide(prevIndex);
                    break;
                case 'ArrowRight':
                    e.preventDefault();
                    const nextIndex = (currentPopupIndex + 1) % popups.length;
                    goToSlide(nextIndex);
                    break;
            }
        });

        // Handle page visibility change
        document.addEventListener('visibilitychange', () => {
            if (document.hidden) {
                stopAutoSlide();
            } else {
                const visiblePopup = document.querySelector('.ita-popup-backdrop.show');
                if (visiblePopup) {
                    startAutoSlide();
                }
            }
        });

        // Initialize the system
        initPopupSystem();
    });

    /* //////////////// end  ita-popup back-drop      ///////////////////// */

    function closeImageSlideMid() {
        document.querySelector('.image-slide-stick-mid').style.display = 'none';
    }

    /** slide service link start =========================================================================== */
    class ServiceSlider {
        constructor(options = {}) {
            // Default configuration
            this.config = {
                autoPlay: true,
                autoPlayDelay: 4000,
                enableKeyboard: true,
                enableTouch: true,
                loop: true,
                slidesToShow: 5,
                slidesToScroll: 1,
                ...options
            };

            // Elements
            this.sliderWrapper = document.getElementById('sliderWrapper');
            this.prevBtn = document.getElementById('prevBtn');
            this.nextBtn = document.getElementById('nextBtn');
            this.slides = document.querySelectorAll('.service-slider .slide-service-link');

            // State
            this.currentSlide = 0;
            this.totalSlides = this.slides.length;
            this.isAnimating = false;
            this.autoPlayTimer = null;

            // Touch/Swipe
            this.touchStartX = 0;
            this.touchEndX = 0;
            this.touchStartY = 0;
            this.touchEndY = 0;

            // Mouse Drag
            this.isDragging = false;
            this.dragStartX = 0;
            this.dragCurrentX = 0;
            this.dragThreshold = 50;

            // Initialize
            this.init();
        }

        init() {
            if (!this.sliderWrapper || this.totalSlides === 0) {
                console.error('Service Slider: Required elements not found');
                return;
            }

            this.bindEvents();
            this.updateSlider();

            if (this.config.autoPlay) {
                this.startAutoPlay();
            }

            // Preload images
            this.preloadImages();

            console.log(`✅ Service Slider initialized: ${this.totalSlides} slides, showing ${this.config.slidesToShow} at once`);
        }

        bindEvents() {
            // Navigation buttons
            if (this.prevBtn) {
                this.prevBtn.addEventListener('click', () => {
                    if (!this.isAnimating) {
                        this.previousSlide();
                    }
                });
            }

            if (this.nextBtn) {
                this.nextBtn.addEventListener('click', () => {
                    if (!this.isAnimating) {
                        this.nextSlide();
                    }
                });
            }

            // Keyboard navigation
            if (this.config.enableKeyboard) {
                document.addEventListener('keydown', (e) => {
                    if (this.isAnimating) return;

                    switch (e.key) {
                        case 'ArrowLeft':
                            this.previousSlide();
                            break;
                        case 'ArrowRight':
                            this.nextSlide();
                            break;
                        case ' ':
                            e.preventDefault();
                            this.toggleAutoPlay();
                            break;
                    }
                });
            }

            // Touch/Swipe events
            if (this.config.enableTouch && this.sliderWrapper) {
                this.sliderWrapper.addEventListener('touchstart', (e) => {
                    this.handleTouchStart(e);
                }, {
                    passive: true
                });

                this.sliderWrapper.addEventListener('touchmove', (e) => {
                    this.handleTouchMove(e);
                }, {
                    passive: true
                });

                this.sliderWrapper.addEventListener('touchend', (e) => {
                    this.handleTouchEnd(e);
                }, {
                    passive: true
                });
            }

            // Mouse Drag events
            if (this.sliderWrapper) {
                this.sliderWrapper.addEventListener('mousedown', (e) => {
                    this.handleMouseDown(e);
                });

                this.sliderWrapper.addEventListener('mousemove', (e) => {
                    this.handleMouseMove(e);
                });

                this.sliderWrapper.addEventListener('mouseup', (e) => {
                    this.handleMouseUp(e);
                });

                this.sliderWrapper.addEventListener('mouseleave', (e) => {
                    this.handleMouseUp(e);
                });

                // ป้องกันการลาก images
                this.sliderWrapper.addEventListener('dragstart', (e) => {
                    e.preventDefault();
                });
            }

            // Mouse events for auto-play control
            const sliderContainer = document.querySelector('.slider-container');
            if (sliderContainer) {
                sliderContainer.addEventListener('mouseenter', () => {
                    this.pauseAutoPlay();
                });

                sliderContainer.addEventListener('mouseleave', () => {
                    if (this.config.autoPlay) {
                        this.startAutoPlay();
                    }
                });
            }

            // Window resize
            window.addEventListener('resize', () => {
                this.updateSlider();
            });
        }

        handleTouchStart(e) {
            this.touchStartX = e.touches[0].clientX;
            this.touchStartY = e.touches[0].clientY;
        }

        handleTouchMove(e) {
            if (!this.touchStartX || !this.touchStartY) return;

            this.touchEndX = e.touches[0].clientX;
            this.touchEndY = e.touches[0].clientY;
        }

        handleTouchEnd(e) {
            if (!this.touchStartX || !this.touchEndX) return;

            const deltaX = this.touchStartX - this.touchEndX;
            const deltaY = this.touchStartY - this.touchEndY;
            const minSwipeDistance = 50;

            // Check if horizontal swipe is more significant than vertical
            if (Math.abs(deltaX) > Math.abs(deltaY) && Math.abs(deltaX) > minSwipeDistance) {
                if (deltaX > 0) {
                    // Swipe left - next slide
                    this.nextSlide();
                } else {
                    // Swipe right - previous slide
                    this.previousSlide();
                }
            }

            // Reset touch coordinates
            this.touchStartX = 0;
            this.touchEndX = 0;
            this.touchStartY = 0;
            this.touchEndY = 0;
        }

        // Mouse Drag Functions
        handleMouseDown(e) {
            this.isDragging = true;
            this.dragStartX = e.clientX;
            this.sliderWrapper.style.cursor = 'grabbing';
            this.pauseAutoPlay();

            // ป้องกันการ select text
            e.preventDefault();
        }

        handleMouseMove(e) {
            if (!this.isDragging) return;

            this.dragCurrentX = e.clientX;
            const dragDistance = this.dragCurrentX - this.dragStartX;

            // แสดง visual feedback ขณะลาก
            const currentTransform = -this.currentSlide * (100 / this.config.slidesToShow);
            const dragOffset = (dragDistance / this.sliderWrapper.offsetWidth) * 100;

            this.sliderWrapper.style.transform = `translateX(${currentTransform + dragOffset}%)`;
        }

        handleMouseUp(e) {
            if (!this.isDragging) return;

            this.isDragging = false;
            this.sliderWrapper.style.cursor = 'grab';

            const dragDistance = this.dragCurrentX - this.dragStartX;

            // ตรวจสอบทิศทางการลาก
            if (Math.abs(dragDistance) > this.dragThreshold) {
                if (dragDistance > 0) {
                    // ลากไปทางขวา - previous slide
                    this.previousSlide();
                } else {
                    // ลากไปทางซ้าย - next slide  
                    this.nextSlide();
                }
            } else {
                // ถ้าลากไม่เกิด threshold ให้กลับไปตำแหน่งเดิม
                this.updateSlider();
            }

            // Reset drag state
            this.dragStartX = 0;
            this.dragCurrentX = 0;

            this.resetAutoPlay();
        }

        updateSlider() {
            if (!this.sliderWrapper) return;

            this.isAnimating = true;
            const slideWidth = 100 / this.config.slidesToShow;
            const translateX = -this.currentSlide * slideWidth;
            this.sliderWrapper.style.transform = `translateX(${translateX}%)`;

            // Reset animation flag
            setTimeout(() => {
                this.isAnimating = false;
            }, 600);
        }

        goToSlide(index) {
            if (index === this.currentSlide || this.isAnimating) return;

            const maxSlide = this.totalSlides - this.config.slidesToShow;
            this.currentSlide = Math.min(Math.max(index, 0), maxSlide);
            this.updateSlider();
            this.resetAutoPlay();
        }

        nextSlide() {
            if (this.isAnimating) return;

            const maxSlide = this.totalSlides - this.config.slidesToShow;

            if (this.config.loop) {
                this.currentSlide = (this.currentSlide + 1) % (maxSlide + 1);
            } else {
                this.currentSlide = Math.min(this.currentSlide + 1, maxSlide);
            }

            this.updateSlider();
            this.resetAutoPlay();
        }

        previousSlide() {
            if (this.isAnimating) return;

            const maxSlide = this.totalSlides - this.config.slidesToShow;

            if (this.config.loop) {
                this.currentSlide = this.currentSlide === 0 ? maxSlide : this.currentSlide - 1;
            } else {
                this.currentSlide = Math.max(this.currentSlide - 1, 0);
            }

            this.updateSlider();
            this.resetAutoPlay();
        }

        startAutoPlay() {
            if (!this.config.autoPlay) return;

            this.pauseAutoPlay();

            this.autoPlayTimer = setInterval(() => {
                this.nextSlide();
            }, this.config.autoPlayDelay);
        }

        pauseAutoPlay() {
            if (this.autoPlayTimer) {
                clearInterval(this.autoPlayTimer);
                this.autoPlayTimer = null;
            }
        }

        resetAutoPlay() {
            if (this.config.autoPlay) {
                this.startAutoPlay();
            }
        }

        toggleAutoPlay() {
            this.config.autoPlay = !this.config.autoPlay;
            if (this.config.autoPlay) {
                this.startAutoPlay();
            } else {
                this.pauseAutoPlay();
            }
        }

        preloadImages() {
            const images = document.querySelectorAll('.service-slider .slide-service-link img');
            images.forEach(img => {
                const imageLoader = new Image();
                imageLoader.src = img.src;
            });
        }

        // Public API
        destroy() {
            this.pauseAutoPlay();
            window.removeEventListener('resize', this.updateSlider);
            document.removeEventListener('keydown', this.bindEvents);
        }

        getCurrentSlide() {
            return this.currentSlide;
        }

        getTotalSlides() {
            return this.totalSlides;
        }

        setConfig(newConfig) {
            this.config = {
                ...this.config,
                ...newConfig
            };
            if (!this.config.autoPlay) {
                this.pauseAutoPlay();
            } else {
                this.startAutoPlay();
            }
        }
    }

    // Initialize slider when DOM is loaded
    document.addEventListener('DOMContentLoaded', function () {
        // รอให้ DOM โหลดเสร็จก่อน
        setTimeout(() => {
            // Initialize with custom configuration
            const slider = new ServiceSlider({
                autoPlay: true,
                autoPlayDelay: 5000,
                enableKeyboard: true,
                enableTouch: true,
                loop: true,
                slidesToShow: 5,
                slidesToScroll: 1
            });

            // Make slider accessible globally for debugging
            window.serviceSlider = slider;
        }, 100);
    });

    // Error handling
    window.addEventListener('error', function (e) {
        console.error('Service Slider Error:', e.error);
    });

    /** slide service link end =========================================================================== */


    /** slide e-book start =========================================================================== */
    document.addEventListener('DOMContentLoaded', function () {
        // ตรวจสอบ element ก่อน
        const slider = document.getElementById('ebookSlider');
        const prevBtn = document.getElementById('ebookPrevBtn');
        const nextBtn = document.getElementById('ebookNextBtn');

        if (!slider || !prevBtn || !nextBtn) {
            console.log('E-book slider elements not found - skipping initialization');
            return;
        }

        // นับจำนวน items จาก DOM แทน PHP
        const totalItems = slider.querySelectorAll('.ebook-item').length;

        if (totalItems === 0) {
            console.log('No ebook items found');
            return;
        }

        let currentPosition = 0;
        const itemWidth = 183; // 143px + 40px gap
        const containerWidth = 1090;
        const visibleItems = Math.floor(containerWidth / itemWidth);
        const maxPosition = Math.max(0, (totalItems - visibleItems) * itemWidth);

        let autoSlideInterval;
        let isUserInteracting = false;

        console.log('E-book auto slider initialized');
        console.log('Total items:', totalItems);
        console.log('Visible items:', visibleItems);
        console.log('Max position:', maxPosition);

        function updateSlider() {
            slider.style.transform = `translateX(-${currentPosition}px)`;

            // อัพเดทสถานะปุ่ม
            if (currentPosition <= 0) {
                prevBtn.classList.add('disabled');
            } else {
                prevBtn.classList.remove('disabled');
            }

            if (currentPosition >= maxPosition) {
                nextBtn.classList.add('disabled');
            } else {
                nextBtn.classList.remove('disabled');
            }
        }

        function nextSlide() {
            if (currentPosition >= maxPosition) {
                // ถึงสุดแล้วให้กลับไปเริ่มต้น
                currentPosition = 0;
            } else {
                currentPosition += itemWidth;
            }
            updateSlider();
        }

        function prevSlide() {
            if (currentPosition <= 0) {
                // อยู่ที่เริ่มต้นแล้วให้ไปสุดท้าย
                currentPosition = maxPosition;
            } else {
                currentPosition -= itemWidth;
            }
            updateSlider();
        }

        // เริ่ม auto slide
        function startAutoSlide() {
            if (totalItems > visibleItems) { // มี items เยอะพอที่จะเลื่อนได้
                autoSlideInterval = setInterval(nextSlide, 3000); // เลื่อนทุก 3 วินาที
            }
        }

        // หยุด auto slide
        function stopAutoSlide() {
            if (autoSlideInterval) {
                clearInterval(autoSlideInterval);
            }
        }

        // ปุ่มก่อนหน้า
        prevBtn.addEventListener('click', function () {
            console.log('Previous button clicked');
            isUserInteracting = true;
            stopAutoSlide();
            prevSlide();

            // เริ่ม auto slide ใหม่หลังจาก 5 วินาที
            setTimeout(() => {
                if (isUserInteracting) {
                    isUserInteracting = false;
                    startAutoSlide();
                }
            }, 5000);
        });

        // ปุ่มถัดไป
        nextBtn.addEventListener('click', function () {
            console.log('Next button clicked');
            isUserInteracting = true;
            stopAutoSlide();
            nextSlide();

            // เริ่ม auto slide ใหม่หลังจาก 5 วินาที
            setTimeout(() => {
                if (isUserInteracting) {
                    isUserInteracting = false;
                    startAutoSlide();
                }
            }, 5000);
        });

        // หยุด auto slide เมื่อ hover
        slider.addEventListener('mouseenter', stopAutoSlide);
        slider.addEventListener('mouseleave', function () {
            if (!isUserInteracting) {
                startAutoSlide();
            }
        });

        // เริ่มต้น
        updateSlider();
        startAutoSlide();

        console.log('Auto slide started - เลื่อนทุก 3 วินาที');
    });
    /** slide e-book end =========================================================================== */

    // ==================== Month-Year Validation for CI Page ====================
    // เพิ่มโค้ดนี้ในไฟล์ frontend_asset/js

    $(document).ready(function () {
        // ตรวจสอบว่าอยู่ในหน้า CI หรือไม่
        if ($('#select_month').length && $('#select_year').length) {
            console.log('✅ CI Page: Month-Year validation initialized');

            // ฟังก์ชันแสดง modal แจ้งเตือน
            function showMonthYearWarning() {
                Swal.fire({
                    icon: 'warning',
                    title: 'แจ้งเตือน',
                    html: '<p style="font-size: 16px; margin-bottom: 10px;">ไม่สามารถเลือกข้อมูลเดือนปัจจุบันได้</p>' +
                        '<p style="font-size: 14px; color: #666;">เนื่องจากระบบคำนวณข้อมูลทั้งเดือน<br>กรุณาเลือกเดือนก่อนหน้านี้เท่านั้น</p>',
                    confirmButtonText: 'รับทราบ',
                    confirmButtonColor: '#ffc107',
                    allowOutsideClick: false
                }).then((result) => {
                    if (result.isConfirmed) {
                        // Refresh หน้าเมื่อกดปุ่มรับทราบ
                        location.reload();
                    }
                });
            }

            // Event handler สำหรับการเปลี่ยนเดือน-ปี
            $('#select_month, #select_year').on('change', function () {
                let selectedMonth = parseInt($('#select_month').val());
                let selectedYear = parseInt($('#select_year').val());

                let today = new Date();
                let currentMonth = today.getMonth() + 1;
                let currentYear = today.getFullYear() + 543;

                console.log('Selected:', selectedMonth, selectedYear);
                console.log('Current:', currentMonth, currentYear);

                // ตรวจสอบว่าเลือกเดือน-ปีที่มากกว่าหรือเท่ากับเดือนปัจจุบันหรือไม่
                if (selectedYear > currentYear || (selectedYear === currentYear && selectedMonth >= currentMonth)) {
                    console.log('⚠️ Invalid selection detected!');

                    // คำนวณเดือนก่อนหน้า
                    let previousMonth = currentMonth - 1;
                    let previousYear = currentYear;
                    if (previousMonth === 0) {
                        previousMonth = 12;
                        previousYear -= 1;
                    }

                    // รีเซ็ตค่า dropdown
                    $('#select_month').val(previousMonth);
                    $('#select_year').val(previousYear);

                    // อัพเดท title
                    if (typeof updateTableTitle === 'function') {
                        updateTableTitle();
                    }

                    // แสดง modal แจ้งเตือน
                    showMonthYearWarning();

                    return false;
                }
            });
        }
    });

    // ==================== End Month-Year Validation ====================
</script>