<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8">
  <!-- <meta name="viewport" content="width=device-width, initial-scale=1.0"> -->
  <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no">
  <!-- boostrap  -->
  <!-- <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-T3c6CoIi6uLrA9TneNEoa7RxnatzjcDSCmG1MXxSR1GAsXEV/Dwwykc2MPK8M2HN" crossorigin="anonymous"> -->
  <link href="<?= base_url('asset/'); ?>boostrap/bootstrap/dist/css/bootstrap.min.css" rel="stylesheet">

  <!-- w3schools -->
  <link rel="stylesheet" href="https://www.w3schools.com/w3css/4/w3.css">
  <!-- awesome  -->
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
  <!-- font  -->
  <!-- <link href='https://fonts.googleapis.com/css?family=Kanit' rel='stylesheet'> -->
  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
  <link href="https://fonts.googleapis.com/css2?family=Noto+Sans+Thai+Looped:wght@100;200;300;400;500;600;700;800;900&family=Noto+Sans+Thai:wght@100..900&display=swap" rel="stylesheet">
  <link href="https://fonts.googleapis.com/css2?family=Sriracha&display=swap" rel="stylesheet">
  <link href="https://fonts.googleapis.com/css2?family=Srisakdi:wght@400;700&display=swap" rel="stylesheet">
  <link href="https://fonts.googleapis.com/css2?family=Pattaya&display=swap" rel="stylesheet">
  <link href="https://fonts.googleapis.com/css2?family=Charmonman:wght@400;700&family=Inter:ital,opsz,wght@0,14..32,100..900;1,14..32,100..900&family=Kanit:ital,wght@0,100;0,200;0,300;0,400;0,500;0,600;0,700;0,800;0,900;1,100;1,200;1,300;1,400;1,500;1,600;1,700;1,800;1,900&family=Krub:ital,wght@0,200;0,300;0,400;0,500;0,600;0,700;1,200;1,300;1,400;1,500;1,600;1,700&family=Pattaya&family=Sriracha&display=swap" rel="stylesheet">


  <!-- google map -->
  <script src=""></script>
  <!-- ใช้ CSS ของ Swiper -->
  <!-- <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/swiper@11/swiper-bundle.min.css" /> -->
  <link href="<?= base_url('asset/'); ?>swiper/swiper/swiper-bundle.min.css" rel="stylesheet">

  <!-- <link rel="stylesheet" type="text/css" href="./style.css" /> -->

  <!-- sweetalert 2 -->
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/sweetalert2@11.7.31/dist/sweetalert2.min.css">

  <!-- Cookie Consent by https://www.cookiewow.com -->
  <!-- <script type="text/javascript" src="https://cookiecdn.com/cwc.js"></script>
  <script id="cookieWow" type="text/javascript" src="https://cookiecdn.com/configs/5juo46fhw8Z5wmLDyQMBUmiB" data-cwcid="5juo46fhw8Z5wmLDyQMBUmiB"></script> -->

  <!-- สไลด์ Slick Carousel -->
  <!-- <link rel="stylesheet" type="text/css" href="//cdn.jsdelivr.net/npm/slick-carousel@1.8.1/slick/slick.css" /> -->
  <link href="<?= base_url('asset/'); ?>slick/slick-carousel/slick/slick.css" rel="stylesheet">

  <!-- รูปภาพ preview -->
  <link href="<?= base_url('asset/'); ?>lightbox2/src/css/lightbox.css" rel="stylesheet">
   <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/@fancyapps/ui@5.0/dist/fancybox/fancybox.css" />

  <!-- Search Google -->
  <script async src="https://cse.google.com/cse.js?cx=<?php echo get_config_value('googlesearch'); ?>"></script>

  <!-- Including Flatpickr CSS -->
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/flatpickr/dist/flatpickr.min.css">

  <script src="https://code.jquery.com/jquery-3.7.1.min.js" integrity="sha256-/JqT3SQfawRcv/BIHPThkBvs0OEvtFFmqPF/lYI/Cxo=" crossorigin="anonymous"></script>

  <link rel="icon" href="<?php echo base_url("docs/logo.png"); ?>" type="image/x-icon">
  <title><?php echo get_config_value('fname'); ?></title>


  <meta name="description" content="<?php echo get_config_value('fname'); ?> - ข้อมูลข่าวสารและบริการสำหรับประชาชน. ออกแบบเว็บไซต์โดย บริษัท เอเอส ซิสเต็ม จำกัด ผู้เชี่ยวชาญด้าน ออกแบบและพัฒนาเว็บไซต์ Smart City และ Mobile Applications" />

  <meta name="keywords" content="<?php echo get_config_value('fname'); ?>, <?php echo get_config_value('abbreviation'); ?>, <?php echo get_config_value('nname'); ?>, <?php echo get_config_value('province'); ?>, ข่าวสาร, บริการประชาชน, เอเอส ซิสเต็ม, ออกแบบเว็บไซต์, สมาร์ทซิตี้, แอปพลิเคชั่นมือถือ, โมบายแอปพลิเคชั่น, แอปพลิเคชัน" />

  <meta name="author" content="บริษัท เอเอส ซิสเต็ม จำกัด - ผู้นำด้านการพัฒนา ออกแบบเว็บไซต์ Smart City และ Mobile Applications ติดต่อฝ่ายขาย: โทร - Line ID: <?php echo get_config_value('telesales'); ?> หรือ Line ID : @assystem" />

  <meta property="og:title" content="<?php echo get_config_value('fname'); ?> | ออกแบบและพัฒนาโดย บริษัท เอเอส ซิสเต็ม จำกัด">

  <meta property="og:description" content="ข้อมูลข่าวสารและบริการสำหรับประชาชน จาก <?php echo get_config_value('fname'); ?>. เว็บไซต์พัฒนาโดย บริษัท เอเอส ซิสเต็ม จำกัด ผู้เชี่ยวชาญด้าน ออกแบบและพัฒนาเว็บไซต์ Smart City และ Mobile Applications">

  <meta property="og:image" content="https://www.<?php echo get_config_value('domain'); ?>.go.th/docs/logo.png">
  <meta property="og:url" content="https://www.<?php echo get_config_value('domain'); ?>.go.th/">
  <meta property="og:type" content="website">

  <link rel="canonical" href="https://www.<?php echo get_config_value('domain'); ?>.go.th">

  <meta name="assystem" content="ติดต่อ บริษัท เอเอส ซิสเต็ม จำกัด รับบริการออกแบบและพัฒนาเว็บไซต์, ออกแบบและพัฒนาเว็บไซต์ พัฒนาระบบ Smart City และ Mobile Applications คุณภาพสูง. ติดต่อฝ่ายขาย: โทร - Line ID: 0623624491 หรือ Line ID : @assystem">
	
	<!-- Flipbook StyleSheets -->
<link href="/assets/dflip/css/dflip.min.css" rel="stylesheet" type="text/css">
<!-- themify-icons.min.css is not required in version 2.0 and above -->
<link href="/assets/dflip/css/themify-icons.min.css" rel="stylesheet" type="text/css">

  <script src="https://webanalytics.assystem.co.th/counter/show?domain=<?php echo get_config_value('domain'); ?>.go.th"></script>
	
  <?php
  $debug_log_enabled = get_config_value('debug_log_enabled') ?? '0';
  ?>

  <?php if ($debug_log_enabled == '1'): ?>
    <!-- 🎯 Debug Control - โหลดก่อนทุกอย่าง -->
    <script src="<?php echo base_url('assets/js/debug-control.js'); ?>"></script>
  <?php endif; ?>

  <link rel="stylesheet" type="text/css" href="https://cdn.jsdelivr.net/npm/slick-carousel@1.8.1/slick/slick.css" />
  <script type="text/javascript" src="https://cdn.jsdelivr.net/npm/slick-carousel@1.8.1/slick/slick.min.js"></script>

<!-- Call reCaptChar Parameter -->
<?php if (get_config_value('recaptcha')): ?>
    <script src="https://www.google.com/recaptcha/api.js?render=<?php echo get_config_value('recaptcha'); ?>"></script>
    <script>
        // ตัวแปรสำหรับ reCAPTCHA
        window.RECAPTCHA_SITE_KEY = '<?php echo get_config_value('recaptcha'); ?>';
        window.recaptchaReady = false;
        
        console.log('🔑 reCAPTCHA Site Key:', window.RECAPTCHA_SITE_KEY ? window.RECAPTCHA_SITE_KEY.substring(0, 10) + '...' : 'NOT SET');
        
        // เมื่อ reCAPTCHA พร้อมใช้งาน
        grecaptcha.ready(function() {
            window.recaptchaReady = true;
            console.log('✅ reCAPTCHA is ready');
        });
    </script>
<?php else: ?>
    <script>
        console.error('❌ reCAPTCHA Site Key not configured in database');
        window.RECAPTCHA_SITE_KEY = '';
        window.recaptchaReady = false;
        
        // สำหรับ development - ข้าม reCAPTCHA
        window.SKIP_RECAPTCHA_FOR_DEV = true;
    </script>
<?php endif; ?>	
	
	
	
	<?php 
$mourning_ribbon_enabled = get_config_value('mourning_ribbon_enabled') ?? '0';
$mourning_ribbon_image = get_config_value('mourning_ribbon_image') ?? 'docs/ribbon.png';
?>

<?php if ($mourning_ribbon_enabled == '1'): ?>
<!-- โบว์ไว้อาลัย -->
<div class="mourning-ribbon">
    <img src="<?= base_url($mourning_ribbon_image) ?>" 
         alt="โบว์ไว้อาลัย" 
         loading="lazy">
</div>
<?php endif; ?>
	

</head>

<!-- Messenger ปลั๊กอินแชท Code -->
<div id="fb-root"></div>

<!-- Your ปลั๊กอินแชท code -->
<div id="fb-customer-chat" class="fb-customerchat">
</div>

<script>
  var chatbox = document.getElementById('fb-customer-chat');
  chatbox.setAttribute("page_id", "852452498161203");
  chatbox.setAttribute("attribution", "biz_inbox");
</script>

<!-- Your SDK code -->
<script>
  window.fbAsyncInit = function() {
    FB.init({
      xfbml: true,
      version: 'v19.0'
    });
  };

  (function(d, s, id) {
    var js, fjs = d.getElementsByTagName(s)[0];
    if (d.getElementById(id)) return;
    js = d.createElement(s);
    js.id = id;
    js.src = 'https://connect.facebook.net/th_TH/sdk/xfbml.customerchat.js';
    fjs.parentNode.insertBefore(js, fjs);
  }(document, 'script', 'facebook-jssdk'));
</script>
	
	
<!-- HTML -->
<!-- LINE OA QR Code - คลิกได้ถ้ามี Link -->
<?php if (!empty($qLineoa) && $qLineoa->lineoa_status == 'show'): ?>
  <div class="lineoa-messenger-container" id="lineoa-container">
    <!-- ปุ่มปิดลอยบนขวา -->
    <button class="lineoa-close-btn" onclick="closeLineOA()">×</button>

    <!-- QR Code -->
    <div class="lineoa-qrcode">
      <?php
      // เช็คว่ามีรูปหรือไม่ ถ้าไม่มีใช้ default
      $qrImage = !empty($qLineoa->lineoa_img)
        ? base_url('docs/img/' . $qLineoa->lineoa_img)
        : base_url('docs/ScanLineOA.png');
      $altText = !empty($qLineoa->lineoa_name)
        ? htmlspecialchars($qLineoa->lineoa_name)
        : 'LINE OA QR Code';
      ?>

      <?php if (!empty($qLineoa->lineoa_link)): ?>
        <!-- มี Link - ทำให้คลิกได้ -->
        <a href="<?php echo htmlspecialchars($qLineoa->lineoa_link); ?>" target="_blank" rel="noopener noreferrer">
          <img src="<?php echo $qrImage; ?>"
            alt="<?php echo $altText; ?>"
            title="คลิกเพื่อเปิด <?php echo $altText; ?>">
        </a>
      <?php else: ?>
        <!-- ไม่มี Link - แสดงแค่รูป -->
        <img src="<?php echo $qrImage; ?>"
          alt="<?php echo $altText; ?>">
      <?php endif; ?>
    </div>
  </div>

  <script>
    // ปิด
    function closeLineOA() {
      document.getElementById('lineoa-container').classList.add('hidden');
    }
  </script>
<?php endif; ?>
	
	

<body>
  <?php 
$cookie_data = array('show_cookie_consent' => true);
$this->load->view('frontend_templat/cookie', $cookie_data); 
?>

 <?php $this->load->view('components/chat_modal'); ?>

  <nav class="wel-navbar" id="wel-navbar">
    <div class="wel-navbar-list underline">
      <a href="<?php echo base_url('Home'); ?>">
        <div class="navbar-item">
          <img src="<?php echo base_url('docs/menubar-home1.png'); ?>">
          <span class="font-text-icon-wel">หน้าหลัก</span>
        </div>
      </a>
      <a href="<?php echo base_url('Home'); ?>#activity">
        <div class="navbar-item">
          <img src="<?php echo base_url('docs/menubar-activity1.png'); ?>">
          <span class="font-text-icon-wel">กิจกรรม</span>
        </div>
      </a>
      <a href="<?php echo base_url('Home'); ?>#egp">
        <div class="navbar-item">
          <img src="<?php echo base_url('docs/menubar-egp1.png'); ?>">
          <span class="font-text-icon-wel">ข่าว e-GP</span>
        </div>
      </a>
      <a href="<?php echo base_url('Home'); ?>#oss">
        <div class="navbar-item">
          <img src="<?php echo base_url('docs/menubar-eservice1.png'); ?>">
          <span class="font-text-icon-wel">e-Service</span>
        </div>
      </a>
      <a href="https://webmail.<?php echo get_config_value('domain'); ?>.go.th/" target="_blank">
        <div class="navbar-item">
          <img src="<?php echo base_url('docs/menubar-email1.png'); ?>">
          <span class="font-text-icon-wel">e-Mail</span>
        </div>
      </a>
      <a href="<?php echo base_url('pages/contact'); ?>" target="_blank">
        <div class="navbar-item">
          <img src="<?php echo base_url('docs/menubar-contact.png'); ?>">
          <span class="font-text-icon-wel">ติดต่อเรา</span>
        </div>
      </a>
      <a href="<?php echo get_config_value('facebook'); ?>" target="_blank">
        <div class="navbar-item">
          <img src="<?php echo base_url('docs/menubar-facebook.png'); ?>">
          <span class="font-text-icon-wel">Facebook</span>
        </div>
      </a>
    </div>
  </nav>
  <button class="hide-button" id="hide-button"></button>
  <button class="show-button" id="show-button" style="display: none;"></button>



  <main>
    <div class="show">
      <div class="overlay"></div>
      <div class="img-show">
        <span>X</span>
        <img src="">
      </div>
    </div>