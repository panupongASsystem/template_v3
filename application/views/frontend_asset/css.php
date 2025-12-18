<!-- อยู่ที่ css view: views/frontend_asset/css.php -->

<!-- ส่วนที่ 1: Dark Mode (มีอยู่แล้ว) -->
<?php
$dark_mode_enabled = get_config_value('dark_mode_enabled') ?? '0';
?>
<?php if ($dark_mode_enabled == '1'): ?>
  <style>
    /* ========== BALANCED SOFT THEME ========== */
    /* โทนสีอ่อนพอดี สมดุล */
    html {
      filter: grayscale(100%) contrast(1.05) brightness(1.05) !important;
      background-color: #A8A8A8 !important;
    }

    /* รูปภาพ - ค่ากลาง */
    img,
    video,
    picture,
    canvas {
      filter: grayscale(30%) contrast(1.0) brightness(1.05) saturate(0.6) opacity(1) !important;
    }

    /* SVG - ค่ากลาง */
    svg {
      filter: invert(1) hue-rotate(180deg) grayscale(70%) brightness(1.1) contrast(0.9) opacity(0.95) !important;
    }

    /* รูปภาพที่เป็น Background */
    [style*="background-image"] {
      filter: grayscale(30%) contrast(0.95) brightness(1.05) saturate(0.6) opacity(1) !important;
    }

    /* Google Maps */
    iframe[src*="google.com/maps"] {
      filter: invert(1) hue-rotate(180deg) grayscale(100%) contrast(0.85) brightness(1.2) !important;
    }

    /* Scrollbar - ค่ากลาง */
    ::-webkit-scrollbar {
      background-color: #CCCCCC;
      width: 12px;
    }

    ::-webkit-scrollbar-track {
      box-shadow: inset 0 0 3px rgba(100, 100, 100, 0.08);
      border-radius: 6px;
      background-color: #D8D8D8;
    }

    ::-webkit-scrollbar-thumb {
      background-color: #ADADAD;
      border-radius: 6px;
      border: 2px solid #CCCCCC;
      box-shadow: 0 0 2px rgba(100, 100, 100, 0.12);
    }

    ::-webkit-scrollbar-thumb:hover {
      background-color: #999999;
      box-shadow: 0 0 3px rgba(100, 100, 100, 0.18);
    }

    /* Text - เงาค่ากลาง */
    body,
    p,
    span,
    div,
    a,
    h1,
    h2,
    h3,
    h4,
    h5,
    h6 {
      text-shadow: 0 0 0.3px rgba(100, 100, 100, 0.15);
      -webkit-font-smoothing: antialiased;
      -moz-osx-font-smoothing: grayscale;
    }

    /* ปุ่มและ Input */
    button,
    input,
    select,
    textarea {
      filter: contrast(0.95) brightness(1.08) !important;
      border: 1px solid rgba(140, 140, 140, 0.25) !important;
    }

    /* Card และ Container - เงาค่ากลาง */
    .card,
    .box,
    .panel,
    [class*="container"] {
      box-shadow: 0 1px 3px rgba(100, 100, 100, 0.08),
        0 0 1px rgba(180, 180, 180, 0.1) inset !important;
    }

    /* Icon */
    .icon,
    i,
    [class*="icon"] {
      filter: drop-shadow(0 0 0.3px rgba(100, 100, 100, 0.15)) contrast(0.95) brightness(1.1) !important;
    }

    /* Border ทั้งหมด */
    * {
      border-color: rgba(140, 140, 140, 0.25) !important;
    }
  </style>
<?php endif; ?>



<!-- ส่วนที่ 2: Mourning Ribbon (เพิ่มใหม่) -->
<?php
$mourning_ribbon_enabled = get_config_value('mourning_ribbon_enabled') ?? '0';
?>
<?php if ($mourning_ribbon_enabled == '1'): ?>
  <style>
    /* ========== MOURNING RIBBON (โบว์ไว้อาลัย) ========== */
    .mourning-ribbon {
      position: fixed;
      top: 80px;
      left: 80px;
      z-index: 999999;
      width: 200px;
      height: auto;
      opacity: 1.0;
      transition: all 0.3s ease;
      pointer-events: none;
    }

    .mourning-ribbon img {
      width: 100%;
      height: auto;
      display: block;
      filter: drop-shadow(0 2px 4px rgba(0, 0, 0, 0.2));
    }

    .mourning-ribbon:hover {
      opacity: 1;
      transform: scale(1.05);
    }

    /* Responsive */
    @media (max-width: 768px) {
      .mourning-ribbon {
        top: 70px;
        right: 15px;
        width: 45px;
      }
    }

    @media (max-width: 480px) {
      .mourning-ribbon {
        top: 60px;
        right: 10px;
        width: 40px;
      }
    }
  </style>
<?php endif; ?>







<style>
  /* ปุ่มควบคุมขนาดตัวอักษร */
  .font-size-controller {
    position: fixed !important;
    left: 25px !important;
    bottom: 7px !important;
    top: auto !important;
    transform: translateY(0) !important;
    z-index: 99999 !important;
    display: flex;
    flex-direction: column;
    gap: 15px;
    transition: all 0.4s cubic-bezier(0.4, 0, 0.2, 1);
  }

  .font-size-controller.hidden {
    transform: translateX(-100px) !important;
    opacity: 0;
    pointer-events: none;
  }

  .font-size-controller button {
    width: 55px;
    height: 55px;
    border-radius: 50%;
    border: none;
    background: linear-gradient(145deg, #ffffff, #e6e6e6);
    box-shadow:
      4px 4px 16px rgba(0, 0, 0, 0.18),
      -4px -4px 16px rgba(255, 255, 255, 0.9);
    color: #333;
    font-size: 20px;
    font-weight: 700;
    cursor: pointer;
    transition: all 0.4s cubic-bezier(0.4, 0, 0.2, 1);
    display: flex;
    align-items: center;
    justify-content: center;
    font-family: 'Noto Sans Thai', sans-serif;
    position: relative;
    overflow: hidden;
    backdrop-filter: blur(10px);
  }

  .font-size-controller button::before {
    content: '';
    position: absolute;
    top: -50%;
    left: -50%;
    width: 200%;
    height: 200%;
    background: linear-gradient(45deg, transparent, rgba(255, 255, 255, 0.3), transparent);
    transform: rotate(45deg);
    transition: all 0.6s ease;
  }

  .font-size-controller button:hover::before {
    left: 100%;
  }

  .font-size-controller button:hover {
    transform: translateY(-3px) scale(1.08);
    box-shadow:
      4px 4px 16px rgba(0, 0, 0, 0.25),
      -4px -4px 16px rgba(255, 255, 255, 1),
      0 0 20px rgba(66, 133, 244, 0.3);
  }

  .font-size-controller button:active {
    transform: translateY(0) scale(1);
    box-shadow:
      6px 6px 12px rgba(0, 0, 0, 0.15),
      -6px -6px 12px rgba(255, 255, 255, 0.7),
      inset 3px 3px 6px rgba(0, 0, 0, 0.1);
  }

  .font-size-controller .increase-btn {
    color: #4CAF50;
  }

  .font-size-controller .increase-btn:hover {
    color: #45a049;
    box-shadow: 4px 4px 16px rgba(0, 0, 0, 0.25), -4px -4px 16px rgba(255, 255, 255, 1), 0 0 25px rgba(76, 175, 80, 0.4);
  }

  .font-size-controller .decrease-btn {
    color: #FF5722;
  }

  .font-size-controller .decrease-btn:hover {
    color: #E64A19;
    box-shadow: 4px 4px 16px rgba(0, 0, 0, 0.25), -4px -4px 16px rgba(255, 255, 255, 1), 0 0 25px rgba(255, 87, 34, 0.4);
  }

  .font-size-controller .reset-btn {
    color: #2196F3;
    font-size: 18px;
  }

  .font-size-controller .reset-btn:hover {
    color: #1976D2;
    box-shadow: 4px 4px 16px rgba(0, 0, 0, 0.25), -4px -4px 16px rgba(255, 255, 255, 1), 0 0 25px rgba(33, 150, 243, 0.4);
  }

  .font-size-controller button .icon {
    font-size: 22px;
    line-height: 1;
    text-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
  }

  .font-size-controller .reset-btn .icon {
    font-size: 20px;
  }

  /* ปุ่มซ่อน (× ขวาบน) */
  .font-size-hide-btn {
    position: absolute !important;
    top: -20px !important;
    right: -20px !important;
    width: 28px !important;
    height: 28px !important;
    border-radius: 50% !important;
    border: none !important;
    background: linear-gradient(145deg, #ff6b6b, #ff5252) !important;
    box-shadow:
      4px 4px 16px rgba(0, 0, 0, 0.2),
      -2px -2px 6px rgba(255, 255, 255, 0.1) !important;
    color: white !important;
    font-size: 18px !important;
    font-weight: bold !important;
    cursor: pointer !important;
    transition: all 0.3s ease !important;
    display: flex !important;
    align-items: center !important;
    justify-content: center !important;
    z-index: 10 !important;
    line-height: 1 !important;
    padding: 0 !important;
    overflow: visible !important;
    outline: none !important;
  }

  .font-size-hide-btn::before {
    display: none !important;
  }

  .font-size-hide-btn:hover {
    transform: scale(1.15) rotate(90deg) !important;
    box-shadow:
      6px 6px 12px rgba(0, 0, 0, 0.25),
      -3px -3px 8px rgba(255, 255, 255, 0.1),
      0 0 15px rgba(255, 107, 107, 0.5) !important;
    background: linear-gradient(145deg, #ff5252, #ff4444) !important;
    outline: none !important;
  }

  .font-size-hide-btn:active {
    transform: scale(0.9) !important;
    outline: none !important;
  }

  .font-size-hide-btn:focus {
    outline: none !important;
  }

  /* ปุ่มแสดง (เมื่อซ่อน) */
  .font-size-toggle {
    position: fixed !important;
    left: 25px !important;
    bottom: 100px !important;
    z-index: 99999 !important;
    width: 45px;
    height: 45px;
    border-radius: 50%;
    border: none;
    background: linear-gradient(145deg, rgba(255, 255, 255, 0.75), rgba(230, 230, 230, 0.75));
    box-shadow:
      4px 4px 16px rgba(0, 0, 0, 0.15),
      -4px -4px 16px rgba(255, 255, 255, 0.9);
    color: #666;
    font-size: 18px;
    font-weight: 700;
    cursor: pointer;
    transition: all 0.4s cubic-bezier(0.4, 0, 0.2, 1);
    display: none;
    align-items: center;
    justify-content: center;
    font-family: 'Noto Sans Thai', sans-serif;
    backdrop-filter: blur(10px);
    -webkit-backdrop-filter: blur(10px);
  }

  .font-size-toggle.visible {
    display: flex;
    animation: popIn 0.3s ease-out;
  }

  @keyframes popIn {
    0% {
      transform: scale(0);
      opacity: 0;
    }

    50% {
      transform: scale(1.1);
    }

    100% {
      transform: scale(1);
      opacity: 1;
    }
  }

  .font-size-toggle:hover {
    transform: scale(1.15);
    background: linear-gradient(145deg, rgba(255, 255, 255, 0.95), rgba(240, 240, 240, 0.95));
    box-shadow:
      6px 6px 20px rgba(0, 0, 0, 0.2),
      -6px -6px 20px rgba(255, 255, 255, 1),
      0 0 15px rgba(66, 133, 244, 0.3);
    color: #333;
  }

  .font-size-toggle:active {
    transform: scale(0.95);
  }

  .font-size-notification {
    position: fixed !important;
    left: 95px !important;
    bottom: 100px !important;
    top: auto !important;
    transform: translateY(0) !important;
    background: linear-gradient(145deg, #ffffff, #f5f5f5);
    color: #333;
    padding: 14px 24px;
    border-radius: 50px;
    z-index: 99998 !important;
    font-family: 'Noto Sans Thai', sans-serif;
    font-size: 15px;
    font-weight: 600;
    box-shadow: 10px 10px 20px rgba(0, 0, 0, 0.15), -10px -10px 20px rgba(255, 255, 255, 0.9);
    opacity: 0;
    pointer-events: none;
    white-space: nowrap;
  }

  @keyframes fadeInOut {
    0% {
      opacity: 0;
      transform: translateX(-15px);
    }

    15% {
      opacity: 1;
      transform: translateX(0);
    }

    85% {
      opacity: 1;
      transform: translateX(0);
    }

    100% {
      opacity: 0;
      transform: translateX(15px);
    }
  }

  @keyframes shake {

    0%,
    100% {
      transform: translateY(-3px) scale(1.08);
    }

    10%,
    30%,
    50%,
    70%,
    90% {
      transform: translateY(-3px) scale(1.08) translateX(-5px);
    }

    20%,
    40%,
    60%,
    80% {
      transform: translateY(-3px) scale(1.08) translateX(5px);
    }
  }

  /* คลาสสำหรับขนาดตัวอักษร */
  body[data-font-scale="75"] {
    --font-scale: 0.75;
  }

  body[data-font-scale="87"] {
    --font-scale: 0.875;
  }

  body[data-font-scale="100"] {
    --font-scale: 1;
  }

  body[data-font-scale="112"] {
    --font-scale: 1.125;
  }

  body[data-font-scale="125"] {
    --font-scale: 1.25;
  }

  body[data-font-scale="137"] {
    --font-scale: 1.375;
  }

  body[data-font-scale="150"] {
    --font-scale: 1.5;
  }

  /* Responsive */
  @media (max-width: 768px) {
    .font-size-controller {
      left: 15px !important;
      bottom: 40px !important;
      gap: 12px;
    }

    .font-size-controller button {
      width: 50px;
      height: 50px;
      font-size: 18px;
    }

    .font-size-hide-btn {
      width: 26px !important;
      height: 26px !important;
      font-size: 16px !important;
    }

    .font-size-toggle {
      left: 15px !important;
      bottom: 40px !important;
      width: 42px;
      height: 42px;
      font-size: 16px;
    }

    .font-size-notification {
      left: 75px !important;
      bottom: 80px !important;
      padding: 12px 20px;
      font-size: 14px;
    }
  }
</style>


<style>
  /* Container ลอยขวาล่าง - ขนาด 192x392 (250/1.3 × 509.6/1.3) */
  .lineoa-messenger-container {
    position: fixed;
    right: 30px;
    bottom: 300px;
    width: 138.9px;
    height: 283.1px;
    background-image: url('<?php echo base_url("docs/frame_lineoa.png"); ?>');
    background-size: cover;
    background-position: center;
    background-repeat: no-repeat;
    border-radius: 12px;
    overflow: hidden;
    z-index: 9999;
  }

  /* QR Code ตรงกลาง - มุมมน */
  .lineoa-qrcode {
    position: absolute;
    top: 65%;
    left: 50%;
    transform: translate(-50%, -50%);
  }

  .lineoa-qrcode img {
    display: block;
    width: 110px;
    height: 110px;
    border-radius: 12px;
  }

  /* ปุ่มปิด - ลอยบนขวา */
  .lineoa-close-btn {
    position: absolute;
    top: 0px;
    right: 8px;
    width: 24px;
    height: 24px;
    background: rgba(0, 0, 0, 0.5);
    color: white;
    border: none;
    border-radius: 50%;
    cursor: pointer;
    font-size: 18px;
    line-height: 1;
    z-index: 10;
  }

  /* Hidden */
  .lineoa-messenger-container.hidden {
    display: none;
  }

  /* Responsive */
  @media (max-width: 768px) {
    .lineoa-messenger-container {
      width: 150px;
      height: 300px;
    }

    .lineoa-qrcode img {
      width: 90px;
      height: 90px;
    }
  }
</style>




<style>
  /* โค้ดเดิมก่อนปรับให้ลองรับกับโทรศัพท์ */
  /* body {
    font-family: 'Krub';
    padding: 0px;
    margin: 0px;
    width: 100%;
    height: auto;
    display: flex;
    align-items: center;
    justify-content: center;
  }

  main {
    margin: 0px auto 0px auto;
    padding: 0px;
    width: 1280px;
  }

  @media screen and (max-width: 1280px) {
    main {
      overflow-x: auto;
    }
  } */

  body {
    padding: 0;
    margin: 0;
    height: 1000px;
    font-family: "Noto Sans Thai Looped", sans-serif;
  }

  main {
    margin: 0 auto;
    transform-origin: top left;
    width: 1920px;
    height: 1000px;
  }

  .crop {
    margin: 0 auto;
    padding: 0;
    width: 1680px;
    height: auto;
  }

  .crop-content {
    margin-left: 699px;
    padding: 0;
    width: 1060px;
    height: auto;
    position: relative;
    z-index: 10;
  }

  .crop-content2 {
    margin: 0 auto;
    padding: 0;
    width: 1600px;
    height: auto;
  }

  .crop-content-activity {
    width: 532px;
  }

  .crop-right {
    right: 0;
  }

  /* สำหรับจอ 9.7 นิ้ว */
  @media (min-width: 819px) and (max-width: 911px) {
    main {
      transform: scale(0.423);
      /* ลดจาก 0.425 */
    }
  }

  @media (min-width: 912px) and (max-width: 1023px) {
    main {
      transform: scale(0.473);
      /* ลดจาก 0.475 */
    }
  }

  /* สำหรับจอ 10 นิ้ว */
  @media (min-width: 1024px) and (max-width: 1076px) {
    main {
      transform: scale(0.528);
      /* ลดจาก 0.53 */
    }
  }

  @media (min-width: 1077px) and (max-width: 1119px) {
    main {
      transform: scale(0.556);
      /* ลดจาก 0.558 */
    }
  }

  @media (min-width: 1120px) and (max-width: 1149px) {
    main {
      transform: scale(0.578);
      /* ลดจาก 0.58 */
    }
  }

  @media (min-width: 1150px) and (max-width: 1179px) {
    main {
      transform: scale(0.598);
      /* ลดจาก 0.60 */
    }
  }

  @media (min-width: 1180px) and (max-width: 1199px) {
    main {
      transform: scale(0.618);
      /* ลดจาก 0.62 */
    }
  }

  /* สำหรับจอ 10.2 นิ้ว */
  @media (min-width: 1200px) and (max-width: 1229px) {
    main {
      transform: scale(0.623);
      /* ลดจาก 0.625 */
    }
  }

  @media (min-width: 1230px) and (max-width: 1259px) {
    main {
      transform: scale(0.638);
      /* ลดจาก 0.64 */
    }
  }

  @media (min-width: 1260px) and (max-width: 1279px) {
    main {
      transform: scale(0.658);
      /* ลดจาก 0.66 */
    }
  }

  @media (min-width: 1280px) and (max-width: 1309px) {
    main {
      transform: scale(0.668);
      /* ลดจาก 0.67 */
    }
  }

  @media (min-width: 1310px) and (max-width: 1339px) {
    main {
      transform: scale(0.688);
      /* ลดจาก 0.69 */
    }
  }

  @media (min-width: 1340px) and (max-width: 1369px) {
    main {
      transform: scale(0.698);
      /* ลดจาก 0.70 */
    }
  }

  /* สำหรับจอ 10.5 นิ้ว */
  @media (min-width: 1370px) and (max-width: 1399px) {
    main {
      transform: scale(0.708);
      /* ลดจาก 0.71 */
    }
  }

  @media (min-width: 1400px) and (max-width: 1419px) {
    main {
      transform: scale(0.728);
      /* ลดจาก 0.73 */
    }
  }

  /* สำหรับจอ 11 นิ้ว */
  @media (min-width: 1420px) and (max-width: 1459px) {
    main {
      transform: scale(0.738);
      /* ลดจาก 0.74 */
    }
  }

  @media (min-width: 1460px) and (max-width: 1499px) {
    main {
      transform: scale(0.758);
      /* ลดจาก 0.76 */
    }
  }

  /* ส่วนนี้ลงไปปรับทีละ 19 เพิ่มสเกลที่ละ 0.015 px */
  @media (min-width: 1500px) and (max-width: 1519px) {
    main {
      transform: scale(0.783);
      /* ลดจาก 0.785 */
    }
  }

  @media (min-width: 1520px) and (max-width: 1539px) {
    main {
      transform: scale(0.793);
      /* ลดจาก 0.795 */
    }
  }

  @media (min-width: 1540px) and (max-width: 1559px) {
    main {
      transform: scale(0.803);
      /* ลดจาก 0.805 */
    }
  }

  @media (min-width: 1560px) and (max-width: 1579px) {
    main {
      transform: scale(0.813);
      /* ลดจาก 0.815 */
    }
  }

  @media (min-width: 1580px) and (max-width: 1599px) {
    main {
      transform: scale(0.823);
      /* ลดจาก 0.825 */
    }
  }

  @media (min-width: 1600px) and (max-width: 1619px) {
    main {
      transform: scale(0.833);
      /* ลดจาก 0.835 */
    }
  }

  @media (min-width: 1620px) and (max-width: 1639px) {
    main {
      transform: scale(0.843);
      /* ลดจาก 0.845 */
    }
  }

  @media (min-width: 1640px) and (max-width: 1659px) {
    main {
      transform: scale(0.853);
      /* ลดจาก 0.855 */
    }
  }

  @media (min-width: 1660px) and (max-width: 1679px) {
    main {
      transform: scale(0.863);
      /* ลดจาก 0.865 */
    }
  }

  @media (min-width: 1680px) and (max-width: 1699px) {
    main {
      transform: scale(0.873);
      /* ลดจาก 0.875 */
    }
  }

  @media (min-width: 1700px) and (max-width: 1719px) {
    main {
      transform: scale(0.883);
      /* ลดจาก 0.885 */
    }
  }

  @media (min-width: 1720px) and (max-width: 1739px) {
    main {
      transform: scale(0.893);
      /* ลดจาก 0.895 */
    }
  }

  @media (min-width: 1740px) and (max-width: 1759px) {
    main {
      transform: scale(0.903);
      /* ลดจาก 0.905 */
    }
  }

  @media (min-width: 1760px) and (max-width: 1779px) {
    main {
      transform: scale(0.913);
      /* ลดจาก 0.915 */
    }
  }

  @media (min-width: 1780px) and (max-width: 1799px) {
    main {
      transform: scale(0.923);
      /* ลดจาก 0.925 */
    }
  }

  @media (min-width: 1800px) and (max-width: 1819px) {
    main {
      transform: scale(0.933);
      /* ลดจาก 0.935 */
    }
  }

  @media (min-width: 1820px) and (max-width: 1839px) {
    main {
      transform: scale(0.943);
      /* ลดจาก 0.945 */
    }
  }

  @media (min-width: 1840px) and (max-width: 1859px) {
    main {
      transform: scale(0.953);
      /* ลดจาก 0.955 */
    }
  }

  @media (min-width: 1860px) and (max-width: 1879px) {
    main {
      transform: scale(0.963);
      /* ลดจาก 0.965 */
    }
  }

  @media (min-width: 1880px) and (max-width: 1899px) {
    main {
      transform: scale(0.973);
      /* ลดจาก 0.975 */
    }
  }

  @media (min-width: 1900px) and (max-width: 1919px) {
    main {
      transform: scale(0.983);
      /* ลดจาก 0.985 */
    }
  }

  @media (min-width: 1920) {
    main {
      transform: scale(1);
      /* ลดจาก 1 */
    }
  }

  /* print ปริ้นส์หน้ารอง ---------------------------- */
  @media print {

    /* รีเซ็ตการตั้งค่าหน้ากระดาษ */
    @page {
      size: auto;
      margin: 0mm;
    }

    /* จัดการ container หลัก */
    html,
    body {
      width: 100% !important;
      height: auto !important;
      margin: 0 !important;
      padding: 0 !important;
    }

    /* บังคับให้เนื้อหาอยู่ในหน้าเดียว */
    .bg-pages,
    .container-pages-news,
    main,
    .text-center.pages-head,
    .pages-select-pdf {
      display: inline-block !important;
      position: relative !important;
      width: 100% !important;
      margin: 0 !important;
      padding: 0 !important;
      page-break-inside: avoid !important;
      break-inside: avoid !important;
      page-break-before: avoid !important;
      page-break-after: avoid !important;
    }

    .text-center.pages-head {
      position: relative !important;
      top: 65px !important;
      /* ปรับตัวเลขตามที่ต้องการ */
    }

    /* ซ่อนส่วนที่ไม่จำเป็น */

    .navbar2,
    .navbar3,
    #scroll-to-top-other,
    #scroll-to-back,
    .btn-download,
    .print-hide,
    .pagination-container,
    .pagination-jump-to-page,
    #fb-root,
    #fb-customer-chat,
    .show,
    .overlay {
      display: none !important;
    }

    /* ปรับขนาดรูปภาพ */
    img {
      /* max-width: 100% !important;
    height: auto !important; */
    }

    .grecaptcha-badge {
      visibility: hidden;
    }

    .container-pages-detail {
      padding-left: 100px;
      z-index: 10;
      position: relative;
    }

    object {
      width: 1200px !important;
    }

    .bg-pages {
      width: 2000px !important;
      height: auto;
      margin-top: -100px !important;
    }

    footer .col-3 {
      margin-left: 1130px !important;
      margin-top: -30px !important;
    }
  }

  /* ------------------------------------------------- */



  /* color-all color สีทั้งหมด ****************************************************** */
  .white {
    color: #fff;
  }

  .gray {
    color: gray;
  }

  .red {
    color: red;
  }

  .green {
    color: green;
  }

  .color-q-a {
    color: #005930;
    leading-trim: both;
    text-edge: cap;
    font-feature-settings: 'clig' off, 'liga' off;
    font-size: 20.098px;
    font-style: normal;
    font-weight: 500;
    line-height: 13.398px;
    /* 66.667% */
  }

  /* ******************************************************************************* */
  table.gsc-search-box td {
    /* vertical-align: middle; */
    /* border: none !important; */
    /* padding: 0px !important; */
  }

  .gsc-input-box .gsc-input {
    /* color: transparent; */
    /* border: none !important; */
  }

  .nav-text-color-2 {
    background-image: linear-gradient(to top, #F9B502, #FADB8d, #FDCE34);
    -webkit-background-clip: text;
    background-clip: text;
    color: transparent;
    text-shadow: 1px 1px 4px rgba(0, 0, 0, 0.2);
  }

  /* background: linear-gradient(to bottom,
        #0F1A2F 0%,
        #2F3C69 25%,
        #AEB9CD 50%,
        #2F3C69 75%,
        #1A2541 100%
      ); */

  /* .navbar2 {
    background-image: url('<?php echo base_url("docs/s.navbar-stick2.png"); ?>');
    background-repeat: no-repeat;
    background-size: 100%;
    height: 164px;
    width: 706px;
    margin-left: 23%;
  } */

  /* .full-screen-img {
    background-image: url('<?php echo base_url("docs/chang.jpg"); ?>');
    background-repeat: no-repeat;
    background-size: 100%;
    height: 1080px;
    width: 1920px;
    margin-top: -10%;
  } */

  /* .welcome {
    background-image: url('<?php echo base_url("docs/s.welcome-slide-1.jpg"); ?>');
    width: 1920px;
    height: 767px;
    background-repeat: no-repeat;
    background-size: 100% 100%;
    position: absolute;
  } */

  .welcome-container {
    position: absolute;
    width: 1920px;
    height: 1000px;
    overflow: hidden;
    /* เพื่อให้การเคลื่อนไหวไม่เกินขอบเขต */
    /* border: 1px solid black; */
    /* เส้นขอบสำหรับการแสดงผล */
  }

  .bg-search {
    width: 100%;
    /* เปลี่ยนจาก fixed width เป็น 100% */
    height: 546px;
    background-image: url('<?php echo base_url("docs/bg-search.png"); ?>');
    background-size: cover;
    /* ให้พื้นหลังปรับขนาดตาม container */
    background-position: center;
    /* จัดตำแหน่งพื้นหลังให้อยู่ตรงกลาง */
    position: relative;
    overflow: hidden;
    text-align: center;
    justify-content: center;
    padding-top: 86px;
  }

  .font-head-navbar-letf-logo1 {
    color: #FFF;
    text-align: center;
    font-size: 36px;
    font-style: normal;
    font-weight: 600;
    line-height: normal;
  }

  .font-head-navbar-letf-logo2 {
    color: #FFF;
    text-align: center;
    font-size: 24px;
    font-style: normal;
    font-weight: 400;
    line-height: normal;
  }

  .welcome {
    width: 100%;
    height: 100%;
    background-repeat: no-repeat;
    background-size: cover;
    position: absolute;
    animation: zoomOut 9s forwards;
  }

  @keyframes zoomOut {
    0% {
      transform: scale(1.2);
      opacity: 1;
    }

    100% {
      transform: scale(1);
      opacity: 1;
    }
  }

  .welcome-btm {
    background-image: url('<?php echo base_url("docs/welcome-btm.png"); ?>');
    background-repeat: no-repeat;
    background-size: 100% 100%;
    z-index: 4;
    width: 1920px;
    height: 1000px;
    position: relative;
    overflow: hidden;
    /* margin-top: 485px; */
  }

  .font-welcome-btm {
    color: #724118;
    text-align: center;
    font-size: 48px;
    font-style: normal;
    font-weight: 600;
    line-height: normal;
    text-shadow: 0px 3px 2px rgba(114, 65, 24, 0.74);
  }

  .welcome-btm-text-run {
    background-image: url('<?php echo base_url("docs/welcome-btm-text-run.png"); ?>');
    background-repeat: no-repeat;
    background-size: 100% 100%;
    z-index: 2;
    width: 1612px;
    height: 82px;
    position: relative;
    /* margin-top: 40px; */
  }

  .font-left-text-run {
    color: #404040;
    text-align: center;
    font-size: 32px;
    font-style: normal;
    font-weight: 600;
    line-height: normal;
    padding-left: 30px;
  }

  .welcome-btm-text-run-vision {
    background-image: url('<?php echo base_url("docs/welcome-btm-text-run.png"); ?>');
    background-repeat: no-repeat;
    background-size: 100% 100%;
    z-index: 2;
    width: 776px;
    height: 45px;
    position: relative;
    margin-top: 100px;
  }

  .font-left-text-run-vision {
    color: #4A0D49;
    text-align: center;
    font-size: 40px;
    font-style: normal;
    font-weight: 700;
    line-height: normal;
  }

  .tab-container {
    /* background-image: url('<?php echo base_url("docs/s.run-text1.png"); ?>'); */
    /* background: #fff; */
    /* background-repeat: no-repeat; */
    /* background-size: 100%; */
    height: 61px;
    width: 1400px;
    /* top: -100px; */
    position: relative;
    overflow: hidden;
    margin-left: -90px;
    z-index: 1;
    margin-top: 10px;
  }

  .tab-container-vision {
    position: relative;
    height: 45px;
    overflow: hidden;
    margin-top: 0px;
  }

  .text-run-update-vision {
    position: absolute;
    white-space: nowrap;
    animation: textRunUpdate 20s linear infinite;
    color: #404040;
    font-size: 30px;
    font-style: normal;
    font-weight: 300;
    line-height: 45px;
    font-family: "Noto Sans Thai", sans-serif;
  }

  .text-run-update {
    position: absolute;
    bottom: 0;
    right: 0;
    display: flex;
    flex-direction: row;
    gap: 5%;
    text-align: right;
    direction: rtl;
    white-space: nowrap;
    z-index: 1;
    animation: textRunUpdate 35s linear infinite;
    font-size: 26px;
    color: #404040;
    font-style: normal;
    font-weight: 300;
  }

  @keyframes textRunUpdate {
    0% {
      transform: translateX(140%);
    }

    100% {
      transform: translateX(-100%);
    }
  }

  .text-run-style {
    font-size: 26px;
    color: #404040;
    font-style: normal;
    font-weight: 300;
    line-height: 51px;
  }

  /* @keyframes textRunUpdate {
    0% {
      transform: translateX(140%);
    }

    100% {
      transform: translateX(-100%);
    }
  } */

  .vision {
    background-image: url('<?php echo base_url("docs/s.bg-vision4.png"); ?>');
    background-repeat: no-repeat;
    background-size: 100%;
    height: 1000px;
    width: 1680px;
    z-index: 1;
  }

  .head-activity {
    background-image: url('<?php echo base_url("docs/s.bg-nav-mid5.png"); ?>');
    background-repeat: no-repeat;
    background-size: 100%;
    height: 80px;
    width: 1280px;
    z-index: 1;
    margin-top: 90px;
  }

  .carousel {
    margin-left: 490px;
    margin-top: 33px;
    min-height: 394px;
    /* กำหนดความสูงขั้นต่ำเท่ากับความสูงที่ต้องการ */
  }

  .carousel-inner {
    position: relative;
    width: 700px;
    min-height: 394px;
    /* กำหนดความสูงขั้นต่ำ */
    border-radius: 16px;
    margin-left: 0;
  }

  .carousel-item {
    position: relative;
    width: 100%;
    height: 394px;
    overflow: visible;
  }

  .carousel-item img {
    position: absolute;
    top: 50%;
    left: 50%;
    transform: translate(-50%, -50%);
    /* จัดให้อยู่ตรงกลางทั้งแนวนอนและแนวตั้ง */
    z-index: 1;
    width: 700px;
    height: auto;
    object-fit: contain;
    max-height: 394px;
  }

  .carousel-item::before {
    content: "";
    position: absolute;
    top: 0;
    left: 0;
    width: 100%;
    height: 100%;
    background-image: var(--bg-image, none);
    background-size: cover;
    background-position: center;
    filter: blur(8px);
    /* ปรับระดับความเบลอตามต้องการ */
    opacity: 0.7;
    /* ปรับความโปร่งใสตามต้องการ */
    z-index: 0;
    border-radius: 16px;
  }

  .carousel-indicators {
    position: absolute;
    bottom: 10px;
    left: 350px;
    /* ปรับให้ indicators อยู่กึ่งกลางของ carousel */
    transform: translateX(-50%);
    display: flex;
    justify-content: center;
    gap: 5px;
    margin: 0;
    padding: 0;
    list-style: none;
  }

  .content-banner {
    /* margin-right: 120px; */
    /* margin-left: 5%; */
    z-index: 1;
    position: relative;
    top: 20px;
  }

  .banner-cartoon {
    margin-right: 120px;
    margin-left: 15%;
    z-index: 1;
    margin-top: 115px;
    background-image: url('<?php echo base_url("docs/banner2_ Cartoon.png"); ?>');
    width: 582px;
    height: 297px;
  }

  .font-banner-cartoon {
    color: #210B00;
    text-align: center;
    font-size: 24px;
    font-style: normal;
    font-weight: 400;
    line-height: normal;
  }

  .bg-group {
    z-index: 1;
    /* top: 20px; */
    margin-top: 115px;
    background-image: url('<?php echo base_url("docs/banner_group.png"); ?>');
    width: 771px;
    height: 360px;
    background-repeat: no-repeat;
    margin-left: 30px;
  }

  .bg-executives {
    position: absolute;
    z-index: 1;
    display: flex;
    width: calc(100% - 240px);
    padding-top: 230px;
    margin-left: 120px;
    margin-right: 120px;
  }

  .position-relative-left {
    margin-right: auto;
    display: flex;
    flex-direction: column;
    align-items: center;
  }

  .position-relative-right {
    margin-left: auto;
    display: flex;
    flex-direction: column;
    align-items: center;
  }

  .bg-ex-img {
    width: 476px;
    height: 500px;
    overflow: hidden;
    text-align: center;
    position: relative;
    z-index: 1;
    display: flex;
    align-items: flex-end;
    /* เปลี่ยนจาก center เป็น flex-end */
    justify-content: center;
  }

  .fade-image {
    position: absolute;
    bottom: 0;
    /* เปลี่ยนจาก top: 50% เป็น bottom: 0 */
    left: 50%;
    transform: translateX(-50%);
    /* เปลี่ยนจาก translate(-50%, -50%) เป็น translateX(-50%) */
    max-width: 100%;
    max-height: 100%;
    width: auto;
    height: auto;
    object-fit: contain;
    opacity: 0;
    transition: opacity 1s ease-in-out;
  }

  .fade-image.active {
    opacity: 1;
  }

  .fade-image:first-child {
    position: absolute;
    bottom: 0;
    /* เปลี่ยนจาก top: 50% เป็น bottom: 0 */
    left: 50%;
    transform: translateX(-50%);
    /* เปลี่ยนจาก translate(-50%, -50%) เป็น translateX(-50%) */
  }

  .bg-text-name {
    background-image: url('<?php echo base_url("docs/bg-text-ex.png"); ?>');
    width: 476px;
    height: 151px;
    display: flex;
    justify-content: center;
    align-items: center;
    flex-direction: column;
    text-align: center;
    padding-bottom: 20px;
    margin-top: -10px;
    position: relative;
    z-index: 2;
  }

  .bg-text-phone-number {
    background-image: url('<?php echo base_url("docs/bg-text-phone.png"); ?>');
    width: 276px;
    height: 54px;
    display: flex;
    justify-content: center;
    align-items: center;
    flex-direction: column;
    text-align: center;
    padding-bottom: 10px;
    margin: 0 auto;
  }

  .font-link-name {
    color: #FFF;
    text-align: center;
    font-size: 23px;
    font-style: normal;
    font-weight: 600;
    line-height: 179.6%;
  }

  .font-link-rank {
    color: #FFF;
    text-align: center;
    font-size: 16px;
    font-style: normal;
    font-weight: 600;
    line-height: 152%;
  }

  .font-link-phone {
    color: #404040;
    text-align: center;
    font-size: 16px;
    font-style: normal;
    font-weight: 500;
    line-height: 179.6%;
    white-space: nowrap;
  }

  .bg-calender {
    background-image: url('<?php echo base_url("docs/bg-calender.png"); ?>');
    width: 749px;
    height: 384px;
    background-repeat: no-repeat;
    margin: 55px auto;
    position: relative;
    z-index: 5;
  }

  .banner-calendar {
    /* margin-right: 120px; */
    /* margin-left: 5%; */
    z-index: 1;
    /* top: 20px; */
    margin-top: 65px;
    /* background-image: url('<?php echo base_url("docs/banner_manage.png"); ?>'); */
    width: 100%;
    height: 436px;
    background-repeat: no-repeat;
  }

  .font-banner-button-topic {
    color: #404040;
    text-align: center;
    font-size: 24px;
    font-style: normal;
    font-weight: 600;
    line-height: normal;
  }

  .font-banner-button-detail {
    /* color: #404040; */
    text-align: center;
    font-size: 22px;
    font-style: normal;
    font-weight: 600;
    line-height: 1.3;
  }

  .button-six {
    display: grid;
    grid-template-columns: 95px 1fr;
    align-items: center;
    width: 387px;
    height: 97px;
    background-image: url('<?php echo base_url("docs/banner_button.png"); ?>');
    background-repeat: no-repeat;
    transition: background-image 0.6s ease;
    color: #ffffff;
    text-decoration: none;
    margin-left: 20px;
  }

  .button-six:hover {
    background-image: url('<?php echo base_url("docs/banner_button_hover.png"); ?>');
    background-repeat: no-repeat;
    transition: background-image 0.6s ease;
    color: #404040;
  }

  .topic-section {
    display: flex;
    justify-content: center;
    align-items: center;
  }

  .detail-section {
    display: flex;
    justify-content: center;
    align-items: center;
    /* margin-left: -30px; */
  }

  .button-banner-container {
    display: flex;
    flex-direction: row;
    justify-content: flex-center;
    /* สามารถใช้ center หรือ space-between เพื่อการจัดเรียงที่ต้องการ */
    position: relative;
    z-index: 5;
  }

  .font-banner-button {
    /* color: #FF960B; */
    text-align: center;
    /* font-family: "Noto Looped Thai UI"; */
    font-size: 22px;
    font-style: normal;
    font-weight: 600;
    line-height: normal;
  }

  .font-button-e-service {
    color: var(--Color-4, #854E00);
    text-align: center;
    font-size: 24px;
    font-weight: 600;
    line-height: normal;
    padding-left: 100px;
  }

  .button-e-service,
  .button-e-service2,
  .button-e-service3,
  .button-e-service4,
  .button-e-service5,
  .button-e-service6,
  .button-e-service7,
  .button-e-service8 {
    z-index: 4;
    background-repeat: no-repeat;
    width: 336px;
    height: 65px;
    transition: background-image 0.6s ease;
    position: relative;
    /* color: #fff; */
  }

  .button-e-service:hover,
  .button-e-service2:hover,
  .button-e-service3:hover,
  .button-e-service4:hover,
  .button-e-service5:hover,
  .button-e-service6:hover,
  .button-e-service7:hover,
  .button-e-service8:hover {
    transition: background-image 0.6s ease;
    /* color: #404040; */
  }

  .button-e-service {
    background-image: url('<?php echo base_url("docs/bt-eservice-queue.png"); ?>');
  }

  .button-e-service:hover {
    background-image: url('<?php echo base_url("docs/bt-eservice-queue-hover.png"); ?>');
  }

  .button-e-service2 {
    background-image: url('<?php echo base_url("docs/bt-eservice-complain.png"); ?>');
  }

  .button-e-service2:hover {
    background-image: url('<?php echo base_url("docs/bt-eservice-complain-hover.png"); ?>');
  }

  .button-e-service3 {
    background-image: url('<?php echo base_url("docs/bt-eservice-suggestions.png"); ?>');
  }

  .button-e-service3:hover {
    background-image: url('<?php echo base_url("docs/bt-eservice-suggestions-hover.png"); ?>');
  }

  .button-e-service4 {
    background-image: url('<?php echo base_url("docs/bt-eservice-kid.png"); ?>');
  }

  .button-e-service4:hover {
    background-image: url('<?php echo base_url("docs/bt-eservice-kid-hover.png"); ?>');
  }

  .button-e-service5 {
    background-image: url('<?php echo base_url("docs/bt-eservice-elderly.png"); ?>');
  }

  .button-e-service5:hover {
    background-image: url('<?php echo base_url("docs/bt-eservice-elderly-hover.png"); ?>');
  }

  .button-e-service6 {
    background-image: url('<?php echo base_url("docs/bt-eservice-form-eservice.png"); ?>');
  }

  .button-e-service6:hover {
    background-image: url('<?php echo base_url("docs/bt-eservice-form-eservice-hover.png"); ?>');
  }

  .button-e-service7 {
    background-image: url('<?php echo base_url("docs/bt-eservice-esv-ods.png"); ?>');
  }

  .button-e-service7:hover {
    background-image: url('<?php echo base_url("docs/bt-eservice-esv-ods-hover.png"); ?>');
  }

  .button-e-service8 {
    background-image: url('<?php echo base_url("docs/bt-eservice-corruption.png"); ?>');
  }

  .button-e-service8:hover {
    background-image: url('<?php echo base_url("docs/bt-eservice-corruption-hover.png"); ?>');
  }

  /* Base button styles */
  .public-button,
  .dla-button,
  .new-button {
    background-image: url('<?php echo base_url("docs/public1_button.png"); ?>');
    width: 330px;
    height: 77px;
    transition: background-image 0.6s ease;
    margin: auto;
    text-align: center;
    color: #002859;
    display: flex;
    justify-content: center;
    align-items: center;
  }

  /* Hover and active states */
  .active-public,
  .active-dla,
  .active-new,
  .public-button:hover,
  .dla-button:hover,
  .new-button:hover {
    background-image: url('<?php echo base_url("docs/public1_button_hover.png"); ?>');
    color: #964518;
  }

  .font-public-button {
    /* color: #13380E; */
    margin-left: 10px;
    font-size: 30px;
    font-weight: 600;
  }

  .font-new-button {
    /* color: #13380E; */
    margin-left: 10px;
    font-size: 26px;
    font-weight: 600;
  }

  .font-new-button2 {
    /* color: #13380E; */
    margin-left: 10px;
    font-size: 25px;
    font-weight: 600;
  }

  .button-activity-all {
    color: #FFF;
    z-index: 5;
    position: relative;
    background-image: url('<?php echo base_url("docs/btn_see_all.png"); ?>');
    background-repeat: no-repeat;
    width: 184px;
    height: 55px;
    transition: background-image 0.6s ease;
    text-align: center;
    align-items: center;
    display: flex;
    justify-content: center;
  }

  .button-activity-all:hover {
    background-image: url('<?php echo base_url("docs/btn_see_all_hover.png"); ?>');
    transition: background-image 0.6s ease;
  }

  .button-new-all {
    color: #fff;
    z-index: 5;
    position: relative;
    /* margin-top: 80px; */
    background-image: url('<?php echo base_url("docs/btn_see_all.png"); ?>');
    background-repeat: no-repeat;
    width: 184px;
    height: 55px;
    margin-top: -65px;
    transition: background-image 0.6s ease;
    text-align: center;
    align-items: center;
    display: flex;
    justify-content: center;
  }

  .button-new-all:hover {
    background-image: url('<?php echo base_url("docs/btn_see_all_hover.png"); ?>');
    transition: background-image 0.6s ease;
  }

  .button-new2-all {
    color: #FFF;
    z-index: 5;
    position: relative;
    /* margin-top: 80px; */
    background-image: url('<?php echo base_url("docs/btn_see_all.png"); ?>');
    background-repeat: no-repeat;
    width: 184px;
    height: 55px;
    margin-top: -65px;
    transition: background-image 0.6s ease;
    text-align: center;
    align-items: center;
    display: flex;
    justify-content: center;
  }

  .button-new2-all:hover {
    background-image: url('<?php echo base_url("docs/btn_see_all_hover.png"); ?>');
    transition: background-image 0.6s ease;
  }

  .frame-main {
    position: absolute;
    z-index: 2;
  }

  .water-wrap {
    display: flex;
    width: 200%;
    animation: waterMove 30s linear infinite;
    margin-top: 698px;
  }

  .water-image {
    width: 50%;
    height: auto;
  }

  @keyframes waterMove {
    0% {
      transform: translateX(-50%);
    }

    100% {
      transform: translateX(0);
    }
  }

  .card-activity {
    border-radius: 24px;
    background-color: #FDF5E1;
    height: 316px;
    width: 248px;
    border: 1px solid #EABA48;
    box-shadow: 2px 2px 4px rgba(0, 0, 0, .2);
  }

  .card-activity:hover {
    border-radius: 24px;
    background-color: #ffff;
    height: 316px;
    width: 248px;
    border: 1px solid #EABA48;
    box-shadow: 2px 2px 4px rgba(0, 0, 0, .2);
  }


  .card-activity {
    border-radius: 24px;
    background-color: #FDF5E1;
    height: 316px;
    width: 248px;
    border: 1px solid #EABA48;
    box-shadow: 2px 2px 4px rgba(0, 0, 0, .2);
  }

  .card-activity img {
    /* width: 245px;
    height: 182px;
    border-radius: 24px 24px 0 0; */
    margin-left: -11px;
  }

  .text-activity {
    color: #523003;

    font-size: 18.263px;
    font-style: normal;
    font-weight: 300;
    line-height: 26.07px;
    /* 142.75% */
    padding-top: 5px;
    /* 3 บรรทัด */
    overflow: hidden;
    text-overflow: ellipsis;
    display: -webkit-box;
    -webkit-line-clamp: 2;
    -webkit-box-orient: vertical;
  }

  .box-activity {
    height: 76px;
  }

  .dropdown-container {
    position: relative;
    display: inline-block;
    width: 1280px;
    margin-left: -18px;
    margin-top: -5px;
  }

  .dropdown-content {
    background-image: url('<?php echo base_url("docs/bg-nav-content-3.png"); ?>');
    background-repeat: no-repeat;
    background-size: 100%;
    display: none;
    position: fixed;
    width: 1920px;
    height: 600px;
    z-index: 2;
    left: 50%;
    top: 291px;
    transform: translate(-50%, -50%);
    margin-top: 60px;
  }

  .dropdown-content ul {
    display: grid;
    grid-template-columns: repeat(3, 1fr);
    /* แบ่งออกเป็น 3 columns ที่มีขนาดเท่ากัน */

  }

  .dropdown-content a {
    color: black;
    padding: 12px 16px;
    text-decoration: none;
    display: block;
  }

  .no-bullets {
    list-style: none;
    padding: 0;
    margin: 0;
  }


  .content-activity {
    margin-left: 11.5%;
    margin-top: 11%;
    margin-right: 3%;

  }

  .mar-left-17 {
    margin-left: 17%;
  }

  .mar-left-12 {
    margin-left: 12%;
  }

  .mar-left-10 {
    margin-left: 10%;
  }

  .mar-left-9 {
    margin-left: 9%;
  }

  .mar-left-8 {
    margin-left: 8%;
  }

  .mar-left-7 {
    margin-left: 7%;
  }

  .mar-left-6 {
    margin-left: 6%;
  }

  .mar-left-5 {
    margin-left: 5%;
  }

  .mar-left-4 {
    margin-left: 4%;
  }

  .mar-left-3 {
    margin-left: 3%;
  }

  .mar-top-19 {
    margin-top: 19%;
  }

  .mar-top-17 {
    margin-top: 17%;
  }

  .mar-top-130 {
    margin-top: 130px;
  }

  .underline {
    text-decoration: none;
  }

  .underline a {
    text-decoration: none;
  }

  .weather-container {
    display: flex;
    /* เพิ่มบรรทัดนี้ */
    align-items: center;
    /* padding: 5px 0; */
    position: relative;
    z-index: 4;
    margin-top: -10px;
  }

  .weather-col-left {
    flex: 0 0 70%;
    padding-left: 30px;
  }

  .weather-col-center {
    flex: 0 0 20%;
  }

  .weather-col-right {
    flex: 0 0 10%;
    display: flex;
    justify-content: flex-end;
    padding-right: 60px;
  }

  .crop-weather {
    display: flex;
    /* width: 1122px; */
    height: 48px;
    padding: 8px 0px 8px 16px;
    align-items: center;
    gap: 8px;
    flex-shrink: 0;
    border-radius: 24px;
    /* background: rgba(255, 255, 255, 0.70);
	backdrop-filter: blur(2px); */
  }

  .font-text-run {
    color: #404040;
    font-size: 22px;
    font-style: normal;
    font-weight: 500;
    line-height: normal;
  }

  .bg-news2 {
    background-image: url('<?php echo base_url("docs/bg-new.png"); ?>');
    background-repeat: no-repeat;
    background-size: 100%;
    background-position: center center;
    height: 1000px;
    width: 1680px;
    display: flex;
    align-items: center;
    justify-content: center;
  }

  .font-header-activity {
    color: #2C013B;
    text-align: center;
    /* font-family: "Noto Looped Thai UI"; */
    font-size: 36px;
    font-style: normal;
    font-weight: 600;
    line-height: normal;
    /* text-shadow: 1px 1px 2px rgba(255, 255, 255, 0.8); */
    -webkit-text-stroke: 1px #fff;


  }

  .bg-header-activity {
    background-image: url('<?php echo base_url("docs/head-activity.png"); ?>');
    background-repeat: no-repeat;
    height: 98px;
    width: 420px;
    display: flex;
    justify-content: center;
    align-items: center;
    text-align: center;
    z-index: 1;
    margin-top: 15px;
    position: relative;
    z-index: 5;
  }

  .font-header-home {
    color: #2C013B;
    text-align: center;
    font-size: 36px;
    font-style: normal;
    font-weight: 600;
    line-height: normal;
  }

  .font-header-otop {
    color: #FFF;
    text-align: center;
    -webkit-text-stroke: 1px #000;
    /* ขนาดและสีของกรอบตัวหนังสือ */
    font-size: 36px;
    font-style: normal;
    font-weight: 600;
    line-height: normal;
  }

  .font-all-home {
    color: #26140A;
    /* font-family: "Noto Looped Thai UI"; */
    font-size: 26px;
    font-style: normal;
    font-weight: 500;
    line-height: normal;
  }

  .content-news-bg {
    height: 730px;
    width: 1166px;
    border-radius: 23.878px;
    background: rgba(255, 255, 255, 0.40);
    box-shadow: 0px 0px 7.023px 0px rgba(63, 62, 47, 0.25);
    padding: 1% 4%;
    margin-left: 59px;
    margin-top: 20px;
  }

  .content-news-bg-two {
    height: 730px;
    width: 1166px;
    border-radius: 23.878px;
    background: rgba(255, 255, 255, 0.40);
    box-shadow: 0px 0px 7.023px 0px rgba(63, 62, 47, 0.25);
    padding: 1% 4%;
    margin-left: 59px;
    margin-top: 20px;
  }

  .tab-container1 {
    display: flex;
    /* margin-left: 110px; */
    z-index: 5 !important;
    position: relative;
    margin-top: -20px;
  }

  .tab-container1 .tab-link-two {
    margin: 0 0px;
    /* ปรับขนาดนี้เพื่อเพิ่มหรือลดช่องว่าง */
  }

  .tabs-left {
    display: flex;
    flex-direction: column;
    /* จัดเรียงปุ่มในแนวตั้ง */
    gap: 10px;
    /* ระยะห่างระหว่างปุ่ม */
    width: 260px;
    /* ความกว้างของเมนูด้านซ้าย */
    margin-top: -35px;
  }

  .tab-container2 {
    display: flex;
    /* margin-left: 110px; */
    z-index: 5 !important;
    position: relative;
    margin-top: -20px;
  }

  .tab-container2 .tab-link-two {
    margin: 0 0px;
    /* ปรับขนาดนี้เพื่อเพิ่มหรือลดช่องว่าง */
  }

  .tab-container2 .tab-link {
    margin: 0 0px;
    /* ปรับขนาดนี้เพื่อเพิ่มหรือลดช่องว่าง */
  }

  .tab-container3 {
    display: flex;
    margin-left: 20px;
    z-index: 5 !important;
    position: relative;
  }

  .tab-container3 .tab-link-dla {
    margin: 0 10px;
    /* ปรับขนาดนี้เพื่อเพิ่มหรือลดช่องว่าง */
  }

  .tab-container3 .tab-link-dla {
    margin: 0 10px;
    /* ปรับขนาดนี้เพื่อเพิ่มหรือลดช่องว่าง */
  }

  .tab-link {
    cursor: pointer;
    padding: 15px 15px;
    /* border: 1px solid #ccc; */
    margin-left: -30px;
  }

  .tab-link-two {
    cursor: pointer;
    padding: 15px 15px;
    /* border: 1px solid #ccc; */
    margin-left: -30px;
  }

  .tab-link-dla {
    cursor: pointer;
    padding: 15px 0px;
    /* border: 1px solid #ccc; */
    margin-left: -30px;
  }

  .tab-content {
    display: none;
    padding: 30px;
    margin-top: -50px;
    /* border: 1px solid #ccc; */
    /* width: 1505px; */
    /* margin-left: 1%; */
  }

  .tab-content-two {
    display: none;
    padding: 20px;
    margin-top: -50px;
    /* border: 1px solid #ccc; */
    /* width: 1505px; */
    /* margin-left: 1%; */
  }

  .content-news-detail {
    width: 1400px;
    height: 54px;
    z-index: 5;
    position: relative;
    padding: 20px 45px;
    background-image: url('<?php echo base_url("docs/btn-public2.png"); ?>');
    margin-top: 15px;
  }

  .content-news-detail:hover {
    background-image: url('<?php echo base_url("docs/btn-public2-hover.png"); ?>');
  }

  .crop-public {
    width: auto;
    /* หรือจะลบ width ออกไปเลยก็ได้ */
    height: 33px;
    /* หรือจะลบ height ออกไปเลยก็ได้ */
    display: inline-block;
    /* หรือใช้ display: inline-flex */
    background: #3395D7;
    border-radius: 20px;
    padding: 0px 12px;
    /* ปรับ padding ให้สวยงามขึ้น */
    margin-left: -10px;
    margin-top: -20px;
  }

  .content-news2-detail {
    width: 1400px;
    height: 54px;
    z-index: 5;
    position: relative;
    padding: 20px 45px;
    background-image: url('<?php echo base_url("docs/btn-public2.png"); ?>');
    margin-top: 15px;
  }

  .content-news2-detail:hover {
    background-image: url('<?php echo base_url("docs/btn-public2-hover.png"); ?>');
  }

  .text-news {
    max-height: 2em;
    overflow: hidden;
    text-overflow: ellipsis;
    display: -webkit-box;
    -webkit-line-clamp: 1;
    -webkit-box-orient: vertical;
    color: #000;

    font-size: 22px;
    font-style: normal;
    font-weight: 400;
    line-height: 35.114px;
    margin-top: -12px;
    /* 138.889% */
  }

  .text-news-time {
    color: #000;
    font-size: 22px;
    font-style: normal;
    font-weight: 400;
    line-height: 35.114px;
    margin-top: -12px;
    /* 138.889% */
  }

  .animation-bus {
    position: absolute;
    z-index: 3;
    top: 768px;
    left: -200px;
    width: 407px;
    height: 172px;
    background-image: url('<?php echo base_url("docs/animation_bus.png"); ?>');
    transition: left 2s ease-in-out;
    /* เพิ่ม transition สำหรับการเคลื่อนที่ */
  }

  .bus-weel {
    position: absolute;
    z-index: 2;
    top: 119px;
    left: 74px;
    transition: transform 0.3s ease;
  }

  .bus-weel2 {
    position: absolute;
    z-index: 2;
    top: 120px;
    right: 44px;
    transition: transform 0.3s ease;
  }

  .font-bus {
    color: #000;
    text-align: center;
    font-family: Sriracha;
    font-size: 14px;
    font-style: normal;
    font-weight: 400;
    line-height: 1;
  }

  .bus-text-animation {
    animation: fadeSlide 4s ease-in-out infinite;
  }

  @keyframes fadeSlide {

    0%,
    100% {
      opacity: 1;
      transform: translateY(0);
    }

    25% {
      opacity: 0;
      transform: translateY(-10px);
    }

    50% {
      opacity: 0;
      transform: translateY(10px);
    }

    75% {
      opacity: 1;
      transform: translateY(0);
    }
  }

  /* Animation ล้อหมุน */
  @keyframes wheelSpin {
    from {
      transform: rotate(0deg);
    }

    to {
      transform: rotate(360deg);
    }
  }

  /* สถานะเมื่อรถกำลังเคลื่อนที่ */
  .bus-moving .bus-weel,
  .bus-moving .bus-weel2 {
    animation: wheelSpin 0.5s linear infinite;
  }

  /* ตำแหน่งต่างๆ ของรถบัส */
  .bus-position-1 {
    left: 70px !important;
    /* ใกล้ pin แรก */
  }

  .bus-position-2 {
    left: 450px !important;
    /* ใกล้ pin ที่สอง */
  }

  .bus-position-3 {
    left: 900px !important;
    /* ใกล้ pin ที่สาม */
  }

  .bus-position-4 {
    left: 1250px !important;
    /* ใกล้ pin ที่สาม */
  }

  /* Hover effects สำหรับ pins */
  .travel-pin {
    transition: transform 0.3s ease;
  }

  .travel-pin:hover {
    transform: scale(1.2);
  }

  /* เปลี่ยนขนาดของ container เมื่อ hover */
  .pin-container:hover~.animation-bus {
    /* สามารถเพิ่ม effect อื่นๆ ได้ */
  }

  .hover-text-otop {
    position: relative;
  }

  .text-otop {
    position: absolute;
    top: 90%;
    left: 50%;
    transform: translate(-50%, -50%);
    background-color: rgba(0, 0, 0, 0.8);
    color: white;
    padding: 8px 16px;
    border-radius: 4px;
    font-size: 14px;
    opacity: 0;
    transition: opacity 0.3s ease;
    pointer-events: none;
    white-space: nowrap;
  }

  .hover-text-otop:hover .text-otop {
    opacity: 1;
  }

  .hover-text-otop:hover img {
    opacity: 0.7;
    transition: opacity 0.3s ease;
  }

  .font-travel {
    color: #964518;
    text-align: center;
    font-size: 20px;
    font-style: normal;
    font-weight: 400;
    line-height: 1;
  }

  .rectangle-travel {
    flex-shrink: 0;
    background: linear-gradient(90deg, rgba(239, 219, 155, 0.00) 0%, #EFDB9B 54.6%, rgba(239, 219, 155, 0.00) 100%);
    display: flex;
    align-items: center;
    justify-content: center;
    /* ไม่ต้องกำหนด width ตรงนี้ */
  }

  .image-container {
    position: relative;
    z-index: 1;
  }

  .image-container img {
    animation: moveUpDown 3s infinite;
    /* เรียกใช้อนิเมชั่น */
  }

  .image-container img:hover {
    animation-play-state: paused;
    /* หยุดอนิเมชั่นเมื่อเมาส์ชี้ */
  }

  @keyframes moveUpDown {

    0%,
    100% {
      transform: translateY(0);
      /* ตำแหน่งเริ่มต้นและสุดท้าย */
    }

    50% {
      transform: translateY(-20px);
      /* ตำแหน่งเมื่อขยับขึ้น */
    }
  }

  .pin-delay-0 img {
    animation-delay: 0s;
  }

  .pin-delay-1 img {
    animation-delay: 0.7s;
  }

  .pin-delay-2 img {
    animation-delay: 1.4s;
  }

  .pin-delay-3 img {
    animation-delay: 2.1s;
  }

  .pin-delay-4 img {
    animation-delay: 2.8s;
  }

  .pin-delay-5 img {
    animation-delay: 3.5s;
  }

  .pin-delay-6 img {
    animation-delay: 4.2s;
  }

  .pin-delay-7 img {
    animation-delay: 4.9s;
  }

  .slick-prev,
  .slick-next {
    position: absolute;
    top: 42%;
    transform: translateY(-50%);
    z-index: 1;
    cursor: pointer;
  }

  .slick-prev {
    left: -140px;
  }

  .slick-prev:hover {
    left: -140px;
    background-image: url('<?php echo base_url("docs/pre-home-hover.png"); ?>');
    width: 55px;
    height: 76px;
  }

  .slick-next {
    right: -50px;
  }

  .slick-next:hover {
    right: -50px;
    background-image: url('<?php echo base_url("docs/next-home-hover.png"); ?>');
    width: 55px;
    height: 76px;
  }

  .slick-carousel {
    margin: 20px 0;
  }

  /* .slick-carousel img {
    margin-right: 50px;
  } */

  .text-travel {
    color: #FFE072;
    -webkit-text-stroke: 1px black;

    font-size: 36.024px;
    font-style: normal;
    font-weight: 600;
    line-height: normal;
    margin-left: -20px;
  }

  .image-with-background {
    display: flex;
    align-items: center;
    justify-content: center;
  }

  .image-with-shadow-travel {
    border-radius: 50%;
    width: 239px;
    height: 239px;
  }

  .up-down {
    width: auto;
    /* max-width: 100%; */
    position: relative;
    animation-name: up-down;
    animation-duration: 4s;
    animation-iteration-count: infinite;
    /* ทำให้ animation เล่นตลอดไป */
    padding-top: 13%;
  }

  .bg-page-bottom {
    background-image: url('<?php echo base_url("docs/s.bg-page-btm2.jpg"); ?>');
    background-repeat: no-repeat;
    background-size: 100%;
    width: 1280px;
    height: 2450px;
    z-index: 1;
  }

  .font-e-service-32 {
    color: #693708;
    text-align: center;
    text-shadow: 0px 2.668px 6.671px rgba(0, 0, 0, 0.25);

    font-size: 32px;
    font-style: normal;
    font-weight: 500;
    line-height: normal;
  }

  .font-e-service-25 {
    color: #693708;
    text-align: center;
    text-shadow: 0px 2.668px 2.668px rgba(0, 0, 0, 0.25);

    font-size: 25.35px;
    font-style: normal;
    font-weight: 500;
    line-height: normal;
  }

  .bg-eservice {
    background-image: url('<?php echo base_url("docs/bg-eservice2.png"); ?>');
    background-repeat: no-repeat;
    background-size: 100%;
    background-position: center center;
    height: 552px;
    width: 820px;
    margin-bottom: 20%;
  }

  .card-view {
    color: #000;
    font-family: Inter;
    font-size: 12px;
    font-style: normal;
    font-weight: 300;
    line-height: 23.393px;
    /* 194.942% */
  }

  .bg-q-a {
    width: 370px;
    height: 400px;
    flex-shrink: 0;
    border-radius: 20.394px;
    background: #FFFBF1;
    box-shadow: 0px 0px 6px 0px rgba(0, 0, 0, 0.25);
    margin-top: 35px;
  }

  .font-q-a-home-head {
    color: #693708;
    text-shadow: 0px 0px 6px rgba(0, 0, 0, 0.25);

    font-size: 24px;
    font-style: normal;
    font-weight: 600;
    line-height: 46.41px;
    /* 193.374% */
  }

  .head-q-a {
    padding: 10px;
    padding-top: 15px;
  }

  .font-q-a-home-form {
    color: #693708;

    font-size: 16px;
    font-style: normal;
    font-weight: 400;
    line-height: 29.47px;
    /* 245.583% */
  }

  .content-q-a {
    padding: 15px;
    margin-top: -25px;
  }

  .input-home-q-a {
    border-radius: 14px;
    border: 1px solid #693708;
    color: var(--, #6D758F);
    leading-trim: both;
    text-edge: cap;
    font-feature-settings: 'clig' off, 'liga' off;

    font-size: 14px;
    font-style: normal;
    font-weight: 300;
    line-height: 7.975px;
    /* 78.491% */
  }

  .green-border {
    border: 1px solid green;
    border-radius: 4px;
    padding: 5px;
  }

  /* swipper link icon ************************************************** */
  .swiper {
    /* background-image: url('<?php echo base_url("docs/s.bg-link.png"); ?>'); */
    /* background-size: 100%; */
    background-position: center;
    /* background-repeat: no-repeat; */
    width: 1680px;
    height: auto;
    padding-top: 40px;
    padding-bottom: 280px;
    padding-left: 90px;
    padding-right: 0;
    /* margin-top: -200px; */
    z-index: 3;
    position: relative;
  }

  .custom-button-prev {
    position: absolute;
    left: -1px;
    top: 20%;
    transform: translateY(-50%);
    cursor: pointer;
    z-index: 5;
  }

  .custom-button-next {
    position: absolute;
    right: -1px;
    top: 20%;
    transform: translateY(-50%);
    cursor: pointer;
    z-index: 100;
  }

  .custom-button-prev:hover img {
    content: url('docs/pre-home-hover.png');
    /* เปลี่ยนเป็นรูปภาพใหม่เมื่อ hover */
  }

  .custom-button-next:hover img {
    content: url('docs/next-home-hover.png');
    /* เปลี่ยนเป็นรูปภาพใหม่เมื่อ hover */
  }

  /* เปลี่ยนสีของ "swiper-pagination" เมื่อเป็นสถานะ "active" เป็นสีเหลือง */
  /* .swiper-pagination .swiper-pagination-bullet.swiper-pagination-bullet-active {
    background-color: yellow;
  } */
  /* 
  .swiper-button-prev,
  .swiper-button-next {
    color: #FADB8D;
  } */

  /** swiper otop ******************************************************* */
  .mySwiperOtop {
    width: 100%;
    height: auto;
    margin-left: auto;
    margin-right: auto;
    position: relative;
    overflow: hidden;
    list-style: none;
    padding: 0px;
    z-index: 1;
    display: block;
  }

  swiper-container {
    width: 100%;
    height: auto;
  }

  swiper-slide {
    text-align: center;
    font-size: 18px;
    display: flex;
    justify-content: center;
    align-items: center;
  }

  swiper-slide img {
    display: block;
    width: 120px;
    height: 100px;
    object-fit: cover;
  }

  swiper-container {
    width: 100%;
    height: 300px;
    margin: 20px auto;
  }

  /* เส้นสี เส้นยาว border-line ******************************************************** */
  .border-yellow {
    border: 4px solid yellow;
    border-radius: 15px;
    padding: 5px;
  }

  .border-gray {
    border: 1px solid #D3D3D3;
    border-radius: 15px;
    margin-top: 10px;
    margin-bottom: 10px;
  }

  .border-flcp {
    width: 669.399px;
    height: 0.67px;
    background: rgba(0, 0, 0, 0.25);
    margin-top: 10px;
    margin-bottom: 10px;
    margin-left: -45px;
  }

  .border-gray-332 {
    margin-top: 25px;
    margin-bottom: 25px;
    width: 100%;
    height: 0.672px;
    background: #693708;
  }

  .border-q-a {
    width: 100%;
    height: 0.67px;
    background: #000;
  }

  /* ********************************************************************************** */
  .bg-footer {
    background-image: url('<?php echo base_url("docs/bg-link.png"); ?>');
    background-repeat: no-repeat;
    background-size: cover;
    height: 1000px;
    width: 1920px;
    margin: auto;
    position: absolute;
    z-index: 1;
    overflow: hidden;
  }

  .bg-footer-other {
    background-image: url('<?php echo base_url("docs/bg-link.png"); ?>');
    background-repeat: no-repeat;
    background-size: cover;
    height: 1000px;
    width: 1920px;
    margin: auto;
    position: absolute;
    z-index: 1;
    overflow: hidden;
  }

  .bg-link2 {
    background-image: url('<?php echo base_url("docs/bg-link2.png"); ?>');
    background-repeat: no-repeat;
    background-size: cover;
    height: 1000px;
    width: 1920px;
    margin: auto;
    position: absolute;
    z-index: 2;
    overflow: hidden;
  }

  .bg-link2-other {
    background-image: url('<?php echo base_url("docs/bg-link-other.png"); ?>');
    background-repeat: no-repeat;
    background-size: cover;
    height: 1000px;
    width: 1920px;
    margin: auto;
    position: absolute;
    z-index: 2;
    overflow: hidden;
  }

  .footer {
    background-image: url('<?php echo base_url("docs/bg-bar-footer.png"); ?>');
    background-repeat: no-repeat;
    background-size: 100%;
    height: 1000px;
    width: 1920px;
    position: absolute;
    z-index: 2;
  }

  .footer-other {
    background-image: url('<?php echo base_url("docs/bg-bar-footer.png"); ?>');
    background-repeat: no-repeat;
    background-size: 100%;
    height: 1000px;
    width: 1920px;
    position: absolute;
    z-index: 2;
    padding-top: 950px;
    padding-left: 350px;
    margin-top: -300px;
    /* background-color: #000; */
  }


  .footer-bottom {
    margin-top: 60px;
    margin-left: 60px;
    /* ระยะห่างจากข้อมูลติดต่อด้านบน */
    padding: 20px 50px;
    width: 100%;
  }

  .footer-other {
    background-image: url('<?php echo base_url("docs/bg-bar-footer.png"); ?>');
    background-repeat: no-repeat;
    background-size: 100%;
    height: 1000px;
    width: 1920px;
    position: absolute;
    z-index: 2;
    padding-top: 950px;
    padding-left: 350px;
    margin-top: -300px;
    /* background-color: #000; */
  }

  .credit {
    /* ให้ข้อความที่อยู่ข้างใน div นี้ไปอยู่ชิดล่างกลาง */
    position: absolute;
    bottom: 0;
    left: 35%;
    transform: translateX(-25%);
    text-align: center;
    font-size: 24px;
    width: 1000px;
  }

  .font-footer {
    color: #fff;
    text-align: center;

    font-size: 20px;
    font-style: normal;
    line-height: 33.366px;
    /* 178.571% */
  }


  .map-home {
    border: 6px solid white;
    border-radius: 15px;
  }

  .map-contact {
    border-radius: 22.86px;
    background: rgba(255, 255, 255, 0.50);
    box-shadow: 0px 0px 6.724px 0px rgba(0, 0, 0, 0.25);
    width: 917.108px;
    height: 638.748px;
    padding: 21.516px;
    gap: 6.724px;
    flex-shrink: 0;
  }

  .bg-pages-all-web {
    background-image: url('<?php echo base_url("docs/s.bg-other.jpg"); ?>');
    background-repeat: no-repeat;
    background-size: 100%;
    width: 1920px;
    height: 2000px;
    position: relative;
    margin-top: 230px;
  }

  .bg-pages {
    /* background-image: url('<?php echo base_url("docs/s.bg-other.jpg"); ?>');
    background-repeat: no-repeat;
    background-size: 100%; */
    background-color: #fff;
    width: 1920px;
    height: auto;
    position: relative;
    margin-top: -150px;
  }

  .bg-pages-in {
    background-color: white;
    margin-top: 40px;
    margin-bottom: 5%;
    border-radius: 22.86px;
    /* background: rgba(253, 245, 225, 0.80); */
    /* box-shadow: 0px 0px 6.724px 0px rgba(0, 0, 0, 0.25); */
    height: auto;
    width: 1920px;
  }

  .bg-pages-news {
    background-image: url('<?php echo base_url("docs/s.bg-other.jpg"); ?>');
    background-repeat: no-repeat;
    background-size: 100%;
    width: 1280px;
    height: 1750px;
    position: relative;
    margin-top: 230px;
  }

  .bg-pages-e-service {
    background-image: url('<?php echo base_url("docs/s.bg-other.jpg"); ?>');
    background-repeat: no-repeat;
    background-size: 100%;
    width: 1280px;
    height: 1800px;
    position: relative;
    margin-top: 230px;
  }




  .bg-pages-in-e-service {
    height: 1362px;
    width: 1069px;
    padding-top: 15px;
    padding-left: 80px;
  }

  .bg-pages-in-activity {
    background-color: white;
    margin-top: 40px;
    margin-bottom: 5%;
    padding-left: 4%;
    padding-right: 2%;
    border-radius: 22.86px;
    background: rgba(253, 245, 225, 0.80);
    box-shadow: 0px 0px 6.724px 0px rgba(0, 0, 0, 0.25);
    height: 1362px;
    width: 1123px;
    padding-top: 45px;
  }

  .bg-pages-in-gi {
    background-color: white;
    margin-top: 40px;
    margin-bottom: 5%;
    padding-left: 5%;
    padding-right: 5%;
    border-radius: 22.86px;
    background: rgba(253, 245, 225, 0.80);
    box-shadow: 0px 0px 6.724px 0px rgba(0, 0, 0, 0.25);
    height: 1362px;
    width: 1069px;
    padding-top: 50px;
  }

  .bg-pages-web {
    background-image: url('<?php echo base_url("docs/bg-page2.png"); ?>');
    background-repeat: no-repeat;
    background-size: 100%;
    background-position: center center;
    width: 1920px;
    height: 3373;
    /* เพิ่มบรรทัดนี้ */
    margin-top: 5%;
  }

  .bg-pages-in-web {
    background-color: white;
    margin-top: 40px;
    margin-bottom: 5%;
    padding-left: 5%;
    padding-right: 2%;
    border-radius: 22.86px;
    background: rgba(253, 245, 225, 0.80);
    box-shadow: 0px 0px 6.724px 0px rgba(0, 0, 0, 0.25);
    height: 1470px;
    width: 1069px;
    padding-top: 25px;
  }

  .bg-pages-in-e-service-add {
    background-color: white;
    margin-top: 40px;
    margin-bottom: 5%;
    border-radius: 22.86px;
    background: rgba(253, 245, 225, 0.80);
    box-shadow: 0px 0px 6.724px 0px rgba(0, 0, 0, 0.25);
    height: 1500px;
    width: 1069px;
    padding-top: 10px;
    padding-left: 80px;
  }

  .bg-pages-in-e-service-q-a-top {
    height: auto;
    width: 1069px;
    padding-top: 15px;
    padding-left: 80px;
  }

  .bg-pages-in-e-service-flcp {
    height: 1362px;
    width: 1069px;
    padding-top: 15px;
    padding-left: 190px;
  }

  .bg-pages-ita {
    background-color: white;
    margin-top: 40px;
    margin-bottom: 5%;
    border-radius: 22.86px;
    box-shadow: 0px 0px 6.724px 0px rgba(0, 0, 0, 0.25);
    height: 1362px;
    width: 1069px;
  }

  .path1-1 {
    background-image: url('<?php echo base_url("docs/s.path1-1.png"); ?>');
    background-size: 100%;
    background-repeat: no-repeat;
    width: 147px;
    height: 40px;
    z-index: 3;
    text-align: center;
    display: flex;
    justify-content: center;
    align-items: center;
  }

  .path2-1 {
    background-image: url('<?php echo base_url("docs/s.path2-1.png"); ?>');
    background-size: 100%;
    background-repeat: no-repeat;
    width: 176px;
    height: 40px;
    z-index: 2;
    margin-left: -27px;
    text-align: center;
    display: flex;
    justify-content: center;
    align-items: center;
  }

  .path1-2 {
    background-image: url('<?php echo base_url("docs/s.path1-2.png"); ?>');
    background-size: 100%;
    background-repeat: no-repeat;
    width: 154px;
    height: 40px;
    z-index: 3;
    text-align: center;
    display: flex;
    justify-content: center;
    align-items: center;
  }

  .path2-2 {
    background-image: url('<?php echo base_url("docs/s.path2-2.png"); ?>');
    background-size: 100%;
    background-repeat: no-repeat;
    width: 194px;
    height: 40px;
    z-index: 2;
    margin-left: -27px;
    text-align: center;
    display: flex;
    justify-content: center;
    align-items: center;
  }

  .path1-3 {
    background-image: url('<?php echo base_url("docs/s.path1-3.png"); ?>');
    background-size: 100%;
    background-repeat: no-repeat;
    width: 164px;
    height: 40px;
    z-index: 3;
    text-align: center;
    display: flex;
    justify-content: center;
    align-items: center;
  }

  .path2-3 {
    background-image: url('<?php echo base_url("docs/s.path2-3.png"); ?>');
    background-size: 100%;
    background-repeat: no-repeat;
    width: 214px;
    height: 40px;
    z-index: 2;
    margin-left: -27px;
    text-align: center;
    display: flex;
    justify-content: center;
    align-items: center;
  }

  .path2-4 {
    background-image: url('<?php echo base_url("docs/s.path2-4.png"); ?>');
    background-size: 100%;
    background-repeat: no-repeat;
    width: 230px;
    height: 40px;
    z-index: 2;
    margin-left: -27px;
    text-align: center;
    display: flex;
    justify-content: center;
    align-items: center;
  }

  .font-path-1 {
    color: #FFF;
    text-align: center;
    text-shadow: 0.534px 0.534px 0.534px rgba(0, 0, 0, 0.25);

    font-size: 20px;
    font-style: normal;
    font-weight: 500;
    line-height: normal;
    margin-left: -15px;
  }

  .font-path-2 {
    color: #693708;
    text-align: center;
    text-shadow: 0.534px 0.534px 0.534px rgba(0, 0, 0, 0.25);

    font-size: 20px;
    font-style: normal;
    font-weight: 500;
    line-height: normal;
    margin-left: -10px;
  }

  .page-center {
    text-align: center;
    display: flex;
    justify-content: center;
    align-items: center;
    flex-direction: column;
    /* เพิ่ม flex-direction เป็น column */
  }

  .page-center-gi {
    text-align: center;
    display: flex;
    justify-content: center;
    align-items: center;
    flex-direction: column;
    /* เพิ่ม flex-direction เป็น column */
  }

  .head-pages {
    background-image: url('<?php echo base_url("docs/s.head-pages1.png"); ?>');
    background-size: 100%;
    width: 403px;
    height: 85px;
    margin-top: 35px;
    margin-bottom: 50px;
    text-align: center;
    display: flex;
    justify-content: center;
    align-items: center;
  }

  .head-pages-two {
    background-image: url('<?php echo base_url("docs/s.head-pages2.png"); ?>');
    background-size: 100%;
    width: 555px;
    height: 85px;
    margin-top: 35px;
    margin-bottom: 50px;
    text-align: center;
    display: flex;
    justify-content: center;
    align-items: center;
  }

  .head-pages-three {
    background-image: url('<?php echo base_url("docs/s.head-pages3.png"); ?>');
    background-size: 100%;
    width: 699px;
    height: 85px;
    margin-top: 35px;
    margin-bottom: 50px;
    text-align: center;
    display: flex;
    justify-content: center;
    align-items: center;
  }

  /* .head-pages {
    background-image: url('<?php echo base_url("docs/s.head-pages.png"); ?>');
    background-size: 100%;
    width: 402px;
    height: 63px;
    margin-top: 35px;
    margin-bottom: 50px;
    text-align: center;
    display: flex;
    justify-content: center;
    align-items: center;
  } */

  .font-pages-head-long {
    color: #FFF;
    leading-trim: both;
    text-edge: cap;
    text-shadow: 0px 2.115px 2.115px rgba(0, 0, 0, 0.25);
    font-size: 26px;
    font-style: normal;
    font-weight: 600;
    line-height: 26.443px;
    /* 88.143% */
  }

  .font-pages-content-head {
    color: #000;
    leading-trim: both;
    text-edge: cap;
    font-size: 32px;
    font-style: normal;
    font-weight: 600;
    line-height: 40px;
    position: relative;
    z-index: 10;
  }

  .font-pages-content-detail {
    color: #000;
    leading-trim: both;
    text-edge: cap;
    font-size: 24px;
    font-style: normal;
    font-weight: 500;
    line-height: 33.421px;
  }

  .font-laws-head {
    color: #000;
    font-size: 22px;
    font-style: normal;
    font-weight: 500;
    line-height: 33.624px;
    /* 152.838% */
  }

  .font-laws-content {
    color: #000;
    font-size: 20px;
    font-style: normal;
    font-weight: 300;
    line-height: 33.624px;
  }

  .search {
    margin-top: 10%;
    margin-right: 5%;
  }

  .page-content-otop {
    margin: 5%;
    /* background: gray; */
  }

  .span-head {
    font-size: 20px;
    font-weight: 500;
  }

  /* fontsize-all font-all fontsize ขนาดตัวหนังสือ ******************************************************* */
  .red-font {
    color: #F00;
    font-feature-settings: 'clig' off, 'liga' off;
    font-size: 22px;
    font-style: normal;
    font-weight: 400;
    line-height: 14.238px;
  }

  .font-e-service-head {
    color: #000;
    text-align: center;
    leading-trim: both;
    text-edge: cap;
    font-feature-settings: 'clig' off, 'liga' off;
    font-size: 24px;
    font-style: normal;
    font-weight: 500;
    line-height: 34px;
  }

  .font-e-service-danger {
    color: #F33;
    leading-trim: both;
    text-edge: cap;
    font-feature-settings: 'clig' off, 'liga' off;
    font-size: 19px;
    font-style: normal;
    font-weight: 400;
    line-height: 24px;
    /* 120% */
  }

  .font-e-service-top {
    color: #000;
    text-align: center;
    leading-trim: both;
    text-edge: cap;
    font-feature-settings: 'clig' off, 'liga' off;
    font-size: 20px;
    font-style: normal;
    font-weight: 300;
    line-height: 34px;
    /* 170% */
  }

  .font-e-service-content {
    color: #000;
    leading-trim: both;
    text-edge: cap;
    font-size: 22px;
    font-style: normal;
    font-weight: 400;
    line-height: 25px;
    /* 113.636% */
  }

  .font-e-service-how {
    color: #FFF;
    leading-trim: both;
    text-edge: cap;
    font-feature-settings: 'clig' off, 'liga' off;

    font-size: 24px;
    font-style: normal;
    font-weight: 600;
    line-height: 26.796px;
    /* 111.648% */
  }

  .font-head-topic {
    color: #693708;
    leading-trim: both;
    text-edge: cap;

    font-size: 24px;
    font-style: normal;
    font-weight: 500;
    line-height: 25px;
    /* 104.167% */
    padding-left: 20px;

  }

  .font-ita-head {
    color: #000;
    font-size: 24px;
    font-style: normal;
    font-weight: 500;
    line-height: normal;
    /* padding-left: 30px; */
  }

  .font-ita-content {
    color: #000;

    font-size: 22px;
    font-style: normal;
    font-weight: 300;
    line-height: normal;
    padding-left: 50px;
  }

  .font-q-a-list {
    color: #000;
    leading-trim: both;
    text-edge: cap;
    font-feature-settings: 'clig' off, 'liga' off;

    font-size: 24px;
    font-style: normal;
    font-weight: 300;
    /* 68.75% */
    padding-top: -30px;
    margin-left: 25px;
    margin-top: 10px;
  }

  .font-q-a-chat-color {
    color: #005930;
    leading-trim: both;
    text-edge: cap;
    font-feature-settings: 'clig' off, 'liga' off;

    font-size: 20.098px;
    font-style: normal;
    font-weight: 500;
    line-height: 13.398px;
    /* 66.667% */
  }

  .font-q-a-chat-black {
    color: #000;
    leading-trim: both;
    text-edge: cap;
    font-feature-settings: 'clig' off, 'liga' off;

    font-size: 21.438px;
    font-style: normal;
    font-weight: 300;
    line-height: 24.787px;
    /* 115.625% */
  }

  .font-contact-1 {
    color: #000;
    font-feature-settings: 'clig' off, 'liga' off;

    font-size: 26.895px;
    font-style: normal;
    font-weight: 300;
    line-height: 13.447px;
    /* 50% */
  }

  .font-contact-2 {
    color: #000;
    text-align: center;
    font-feature-settings: 'clig' off, 'liga' off;

    font-size: 24.205px;
    font-style: normal;
    font-weight: 300;
    line-height: 13.447px;
    /* 55.556% */
  }

  .font-contact-map {
    color: #000;
    leading-trim: both;
    text-edge: cap;

    font-size: 26.895px;
    font-style: normal;
    font-weight: 600;
    line-height: 26.541px;
    /* 98.684% */
  }

  .font-pages-heads-img {
    color: #523003;
    font-size: 16px;
    font-style: normal;
    font-weight: 500;
    line-height: 24.863px;
    /* 155.394% */
  }

  .font-pages-details-img {
    color: #6C757D;

    font-size: 15.5px;
    font-style: normal;
    font-weight: 300;
    line-height: 17.076px;
  }

  .font-page-detail-head {
    color: #000;
    leading-trim: both;
    text-edge: cap;
    font-feature-settings: 'clig' off, 'liga' off;
    font-size: 32px;
    font-style: normal;
    font-weight: 600;
    line-height: 40px;
    /* 111.648% */
  }

  .font-page-detail-time-img {
    color: #693708;
    font-size: 20px;
    font-style: normal;
    font-weight: 400;
    line-height: 187.5%;
    /* 37.5px */
  }

  .font-page-detail-content-img {
    color: #000;
    font-size: 24px;
    font-style: normal;
    font-weight: 500;
    line-height: 33.618px;
    /* 152.811% */
  }

  .font-page-detail-view-img {
    color: #693708;

    font-size: 20px;
    font-style: normal;
    font-weight: 500;
    line-height: 33.495px;
    /* 167.473% */
  }

  .font-page-detail-view-news {
    color: #693708;
    text-align: right;
    leading-trim: both;
    text-edge: cap;
    font-size: 20px;
    font-style: normal;
    font-weight: 400;
    line-height: 33.421px;
    /* 166.667% */
  }

  .font-pages-content {
    color: #000;

    font-size: 24px;
    font-style: normal;
    font-weight: 400;
    line-height: normal;
    margin-left: -15px;
    /* padding-top: 7px; */
  }

  .font-otop-head {
    color: #000;
    font-size: 32px;
    font-style: normal;
    font-weight: 600;
    line-height: normal;
  }

  .font-otop-content {
    color: #000000;
    text-align: left;
    text-shadow: 1.334px 1.334px 1.334px rgba(0, 0, 0, 0.25);
    font-family: "Noto Looped Thai UI";
    font-size: 24px;
    font-style: normal;
    font-weight: 500;
    line-height: normal;
  }

  .font-p-name {
    color: #693708;
    text-align: center;
    leading-trim: both;
    text-edge: cap;
    font-feature-settings: 'clig' off, 'liga' off;
    font-size: 22px;
    font-style: normal;
    font-weight: 600;
    line-height: 1.4;
    /* 118.512% */
    margin-top: -10px;
  }

  .font-p-detail {
    color: #000;
    leading-trim: both;
    text-edge: cap;
    font-feature-settings: 'clig' off, 'liga' off;

    font-size: 20px;
    font-style: normal;
    font-weight: 500;
    line-height: 1.4;
    /* 153.627% */
  }

  .font-head-all-web {
    color: #FFC23B;
    text-align: center;

    font-size: 24px;
    font-style: normal;
    font-weight: 500;
    line-height: 8px;
    /* 36.364% */
  }

  .font-content-all-web {
    display: inline-block;
    color: #000;
    text-align: start;
    font-size: 20px;
    font-style: normal;
    font-weight: 300;
    line-height: normal;
    transition: all 0.3s ease;
    transform-origin: center;
  }

  .font-content-all-web:hover {
    transform: scale(1.05);
    color: red;
  }

  .font-e-service-complain {
    color: #000;
    font-feature-settings: 'clig' off, 'liga' off;

    font-size: 21.442px;
    font-style: normal;
    font-weight: 500;
    line-height: 14.238px;
    margin-bottom: 25px;
    /* 66.4% */
  }

  .font-e-service-elderly_aw {
    color: #000;
    font-feature-settings: 'clig' off, 'liga' off;
    font-size: 32px;
    font-style: normal;
    font-weight: 500;
  }

  .font-label-e-service-complain {
    color: var(--text, var(--, #6D758F));
    leading-trim: both;
    text-edge: cap;
    font-feature-settings: 'clig' off, 'liga' off;

    font-size: 18.762px;
    font-style: normal;
    font-weight: 300;
    margin-top: 20px;
    /* line-height: 14.238px; */
    /* 75.886% */
  }

  .font-label-e-service-complain2 {
    color: var(--text, var(--, #6D758F));
    leading-trim: both;
    text-edge: cap;
    font-feature-settings: 'clig' off, 'liga' off;

    font-size: 18.762px;
    font-style: normal;
    font-weight: 300;
    margin-top: 20px;
    /* line-height: 14.238px; */
    /* 75.886% */
  }


  .font-label-elderly_aw {
    border-radius: 8px;
    border: 3px solid var(--thai, #F7EBB7);
    background: #FFF;
    leading-trim: both;
    text-edge: cap;
    font-feature-settings: 'clig' off, 'liga' off;
    font-size: 18.762px;
    font-style: normal;
    font-weight: 300;
    /* 75.886% */
  }

  .font-thx-curruption {
    padding-top: 25px;
    color: #000;
    leading-trim: both;
    text-edge: cap;
    font-feature-settings: 'clig' off, 'liga' off;

    font-size: 17px;
    font-style: normal;
    font-weight: 500;
    line-height: 13.401px;
  }

  .font-flcp-sd {
    color: var(--, #6D758F);

    font-size: 21.442px;
    font-style: normal;
    font-weight: 300;
    line-height: normal;
  }

  .font-color-flcp {

    font-size: 21.442px;
    font-style: normal;
    font-weight: 500;
    line-height: normal;
  }

  .font-time-flcp {
    color: #000;

    font-size: 21.442px;
    font-style: normal;
    font-weight: 300;
    line-height: normal;
  }

  .font-12 {
    color: #693708;
    text-align: center;
    leading-trim: both;
    text-edge: cap;
    font-feature-settings: 'clig' off, 'liga' off;
    font-family: Inter;
    font-size: 12.643px;
    font-style: normal;
    font-weight: 500;
    line-height: 12.265px;
    /* 97.004% */
  }

  .font-18 {
    font-size: 18;
  }

  .font-20 {
    font-size: 20px;
  }

  .font-24 {
    font-size: 24px;
  }

  .font-24b {
    font-size: 24px;
    font-weight: bold;
  }

  .font-24 {
    font-size: 24px;
  }

  .font-26 {
    font-size: 20px;
  }

  .font-26b {
    font-size: 26px;
    font-weight: bold;
  }

  .font-28 {
    font-size: 28px;
  }

  .font-28b {
    font-size: 28px;
    font-weight: bold;
  }

  .font-30 {
    font-size: 30px;
  }

  .font-30b {
    font-size: 30px;
    font-weight: bold;
  }

  .font-32 {
    font-size: 32px;
  }

  .font-32b {
    font-size: 32px;
    font-weight: bold;
  }

  .font-34b {
    font-size: 34px;
    font-weight: bold;
  }

  .font-36 {
    font-size: 36px;
  }

  .font-36b {
    font-size: 36px;
  }

  /* **************************************************************************** */
  hr {
    border-top: 2px solid gray;
    /* เปลี่ยนสีเส้นเหนียวตามที่คุณต้องการ */
  }

  .span-time-pages-img {
    color: #693708;

    font-size: 13px;
    font-style: normal;
    font-weight: 400;
    line-height: 21.267px;
    /* 163.592% */
    margin-top: -40px;
  }

  .span-time-pages-img-detail {
    color: #693708;
    text-align: center;
    leading-trim: both;
    text-edge: cap;
    font-feature-settings: 'clig' off, 'liga' off;

    font-size: 15px;
    font-style: normal;
    font-weight: 500;
    line-height: 11.697px;
    /* 77.979% */
    margin-top: -40px;

  }

  .span-time-pages-news {
    color: #693708;

    font-size: 20px;
    font-style: normal;
    font-weight: 400;
    line-height: normal;
    margin-top: 5px;
    margin-left: 15px;
    padding-top: 7px;
  }

  .span-time2 {
    margin-left: 8px;
    font-size: 14px;
    color: gray;
  }

  .span-time-q-a {
    color: #693708;

    font-size: 16.078px;
    font-style: normal;
    font-weight: 400;
    line-height: 187.5%;
    /* 30.146px */
  }

  .span-time-home {
    color: #693708;
    font-family: Inter;
    font-size: 12px;
    font-style: normal;
    font-weight: 500;
    line-height: 22.299px;
    /* 222.222% */
  }

  /* ลิมิตการแสดงผล limit-font *************************************************** */
  .three-line-ellipsis {
    overflow: hidden;
    display: -webkit-box;
    -webkit-line-clamp: 3;
    /* จำนวนบรรทัดที่ต้องการให้แสดง */
    -webkit-box-orient: vertical;
    white-space: normal;
    line-height: 1.3;
    max-height: 2.55em;
  }

  .two-line-ellipsis {
    /* margin-bottom: 10px; */
    max-height: 2.55em;
    overflow: hidden;
    text-overflow: ellipsis;
    display: -webkit-box;
    -webkit-line-clamp: 2;
    -webkit-box-orient: vertical;
    line-height: 1.3;
  }

  .one-line-ellipsis {
    /* margin-bottom: 10px; */
    overflow: hidden;
    text-overflow: ellipsis;
    display: -webkit-box;
    -webkit-line-clamp: 1;
    -webkit-box-orient: vertical;
  }

  .activity-item {
    display: flex;
    align-items: center;
  }

  .two-line-ellipsis-activity {
    flex: 1;
    max-height: 2.55em;
    overflow: hidden;
    text-overflow: ellipsis;
    display: -webkit-box;
    -webkit-line-clamp: 2;
    -webkit-box-orient: vertical;
    line-height: 1.3;
  }

  .activity-new-img {
    margin-top: 20px;
    margin-left: 10px;
    border-radius: 0px !important;
  }

  /* **************************************************************************** */

  .col-8 {
    word-wrap: break-word;
  }

  .break-word {
    word-wrap: break-word;
  }

  .page-border-otop {
    border-radius: 16.077px;
    border: 0.335px solid var(--line, #EABA48);
    background: #FDF5E1;
    box-shadow: 0px 0px 6.699px 0px rgba(0, 0, 0, 0.25);
    padding-left: 50px;
    padding-top: 30px;
    padding-bottom: 30px;
  }

  /* ปุ่ม next page pagination ******************************************** */

  .pagination {
    display: flex;
    justify-content: center;
    align-items: center;
  }

  .pagination li {
    margin: 0 5px;
    font-size: 21px;
    font-weight: bold;
  }

  .pagination .page-item.active .page-link {
    background-color: #50B1E5;
    /* สีเขียว */
    border-color: #50B1E5;
    color: #fff;
  }

  .pagination-item {
    display: flex;
    justify-content: center;
    align-items: center;
    width: 45px;
    height: 45px;
    overflow: hidden;
    /* border-radius: 50%; */
    /* background-image: url('<?php echo base_url("docs/s.pages-next-pre.png"); ?>');
    background-size: 100% 100%; */
    /* แก้เป็น 100% 100% */
    background-repeat: no-repeat;
    /* เพิ่มบรรทัดนี้ */
    background-position: center;
    padding-left: 1px;
  }

  .pagination .page-link {
    width: 100%;
    height: 100%;
    display: flex;
    justify-content: center;
    align-items: center;
    color: #000;
    /* border-radius: 50%; */
    background-size: cover;
  }


  .pagination .page-link:hover {
    color: #F1F3F7;
    background-color: #006CA6;
  }

  /* เปลี่ยนรูปภาพเมื่อ hover */
  .pages-first:hover {
    content: url('<?php echo base_url("docs/s.pages-first-hover.png"); ?>');
  }

  .pages-pre:hover {
    content: url('<?php echo base_url("docs/s.pages-pre-hover.png"); ?>');
  }

  .pages-last:hover {
    content: url('<?php echo base_url("docs/s.pages-last-hover.png"); ?>');
  }

  .pages-next:hover {
    content: url('<?php echo base_url("docs/s.pages-next-hover.png"); ?>');
  }

  .pagination-jump-to-page {
    margin-left: -8px;
  }

  .pages-go:hover {
    content: url('<?php echo base_url("docs/b.pages-go-hover.png"); ?>');
  }

  .page-border-travel {
    background-color: #FDF5E1;
    border: 1px solid #EABA48;
    border-radius: 15px;
    margin-bottom: 30px;
    width: 248px;
    height: 316px;
    flex-shrink: 0;
    position: relative;
    z-index: 5;

  }

  .page-border-travel:hover {
    background-color: #ffff;
    border: 1px solid #EABA48;
    border-radius: 15px;
    margin-bottom: 30px;
    width: 248px;
    height: 316px;
    flex-shrink: 0;
    position: relative;
    z-index: 5;

  }

  .page-border-activity {
    background-color: #FDF5E1;
    border: 1px solid #EABA48;
    border-radius: 15px;
    margin-bottom: 30px;
    width: 248px;
    height: 316px;
    flex-shrink: 0;
    z-index: 5;
    position: relative;
  }

  .page-border-activity:hover {
    background-color: #ffff;
    border: 1px solid #EABA48;
    border-radius: 15px;
    margin-bottom: 30px;
    width: 248px;
    height: 316px;
    flex-shrink: 0;
    z-index: 5;
    position: relative;
  }

  /* รูปภาพโค้ง border-radius-img ******************************************************8* */
  .rounded-top-left-right {
    border-top-left-radius: 15px;
    border-top-right-radius: 15px;
    border-bottom-left-radius: 0;
    border-bottom-right-radius: 0;
  }

  .border-radius34 {
    width: 209.09px;
    height: 201.71px;
    flex-shrink: 0;
    border-radius: 9.413px;
    background: url(<path-to-image>), lightgray -92.394px -1.345px / 171.655% 103.333% no-repeat;
    box-shadow: 0px 2.689px 2.689px 0px rgba(0, 0, 0, 0.10);
  }

  .border-radius34 {
    border-radius: 34px;
    width: 100%;
    height: 100%;
  }

  .border-radius-travel {
    border-radius: 34px;
    margin-left: -15px;
  }

  /* ************************************************************************* */

  .margin-top-delete {
    margin-top: 20px;
  }

  .margin-top-delete-topic {
    margin-top: -10px;
  }

  .margin-top-delete-q-a {
    margin-right: 90px;
    margin-top: -10px;

  }

  .margin-top-delete-travel {
    margin-top: -5px;

  }

  .pages-select-pdf {
    border-radius: 100px;
    border: 0.67px solid var(--02, #4A0D49);
    background: #FFFCF4;
    padding: 15px;
    margin-bottom: 15px;
    width: 1460px;
    height: auto;
    flex-shrink: 0;
    position: relative;
    z-index: 10;
  }

  .pages-select-pdf:hover {
    border-radius: 100px;
    border: 0.67px solid var(--02, #4A0D49);
    background: #FFF5DB;
    padding: 15px;
    margin-bottom: 15px;
    width: 1460px;
    flex-shrink: 0;
    position: relative;
    z-index: 10;
  }

  .pages-select-e-gp {
    border-radius: 16.042px;
    border: 0.668px solid var(--02, #ECB23F);
    background: #FDF5E1;
    padding: 15px;
    margin-bottom: 15px;
    flex-shrink: 0;

  }

  .pages-select-q_a {
    border-radius: 100px;
    border: 0.67px solid var(--02, #ECB23F);
    background: #FFFCF4;
    padding: 15px;
    margin-bottom: 15px;
    width: 1460px;
    height: auto;
    flex-shrink: 0;
    position: relative;
    z-index: 10;
  }

  .pages-select-q_a:hover {
    border-radius: 100px;
    border: 0.67px solid var(--02, #ECB23F);
    background: #FFF5DB;
    padding: 15px;
    margin-bottom: 15px;
    width: 1460px;
    height: auto;
    flex-shrink: 0;
    position: relative;
    z-index: 10;
  }

  .pages-select-q_a-add {
    margin-top: 40px;
    margin-bottom: 5%;
    padding-left: 5%;
    padding-right: 5%;
    border-radius: 17.085px;
    background: rgba(253, 245, 225, 0.80);
    box-shadow: 0px 0px 6.701px 0px rgba(0, 0, 0, 0.25);
    height: 600px;
    width: 914px;
  }

  .pages-select-q-a-chat {
    margin-top: 40px;
    margin-bottom: 5%;
    padding-left: 5%;
    padding-right: 5%;
    border-radius: 17.085px;
    background: rgba(253, 245, 225, 0.80);
    box-shadow: 0px 0px 6.701px 0px rgba(0, 0, 0, 0.25);
    height: 400px;
    width: 1460px;
  }

  .pages-form-es-complain {
    margin-top: 40px;
    margin-bottom: 5%;
    padding-left: 5%;
    padding-right: 5%;
    border-radius: 17.085px;
    background: rgba(253, 245, 225, 0.80);
    box-shadow: 0px 0px 6.701px 0px rgba(0, 0, 0, 0.25);
    height: 600px;
    width: 1460px;
  }

  .pages-form-es-complain-q-a {
    margin-top: 40px;
    margin-bottom: 5%;
    padding-left: 5%;
    padding-right: 5%;
    border-radius: 17.085px;
    background: rgba(253, 245, 225, 0.80);
    box-shadow: 0px 0px 6.701px 0px rgba(0, 0, 0, 0.25);
    height: 350px;
    width: 914px;
  }

  .pages-form-es-corruption {
    margin-top: 40px;
    margin-bottom: 5%;
    padding-left: 5%;
    padding-right: 5%;
    border-radius: 17.085px;
    background: rgba(253, 245, 225, 0.80);
    box-shadow: 0px 0px 6.701px 0px rgba(0, 0, 0, 0.25);
    height: 515px;
    width: 914px;
  }

  .pages-follow-complain {
    margin-top: 40px;
    margin-bottom: 5%;
    padding-left: 5%;
    padding-right: 5%;
    border-radius: 17.085px;
    background: rgba(253, 245, 225, 0.80);
    box-shadow: 0px 0px 6.701px 0px rgba(0, 0, 0, 0.25);
    height: 250px;
    width: 1460px;
  }

  .pages-follow-complain-detail {
    margin-top: 40px;
    margin-bottom: 5%;
    padding-left: 5%;
    padding-right: 5%;
    border-radius: 17.085px;
    background: rgba(253, 245, 225, 0.80);
    box-shadow: 0px 0px 6.701px 0px rgba(0, 0, 0, 0.25);
    height: auto;
    width: 1460px;
  }

  .pages-follow-elderly-aw-detail {
    margin-top: 40px;
    margin-bottom: 5%;
    border-radius: 17.085px;
    background: rgba(253, 245, 225, 0.80);
    box-shadow: 0px 0px 6.701px 0px rgba(0, 0, 0, 0.25);
    height: auto;
    width: 1460px;
  }

  .pages-select-e-service {
    border: 1px solid #6D758F;
    border-radius: 15px;
    padding: 20px;
    margin-bottom: 30px;
  }

  .pages-form-es-complain {
    margin-top: 40px;
    margin-bottom: 5%;
    padding-left: 5%;
    padding-right: 5%;
    border-radius: 17.085px;
    background: rgba(253, 245, 225, 0.80);
    box-shadow: 0px 0px 6.701px 0px rgba(0, 0, 0, 0.25);
    height: 600px;
    width: 914px;
  }

  .detail-q-a {
    display: flex;
    width: 1460px;
    height: auto;
    padding: 21.438px;
    flex-direction: column;
    align-items: flex-start;
    gap: 13.398px;
    flex-shrink: 0;
    border-radius: 22.777px;
    background: rgba(253, 245, 225, 0.80);
    box-shadow: 0px 0px 6.699px 0px rgba(0, 0, 0, 0.25);
    position: relative;
    z-index: 10;
  }

  /* scroll bar เลื่อนซ้ายขวา เลื่อนบนล่าง ****************************************************** */
  .scrollable-container {
    margin-top: 30px;
    max-height: 700px;
    overflow-y: scroll;
    overflow-x: hidden;
    margin-bottom: 40px;
  }


  .scrollable-container-news {
    max-height: 1250px;
    overflow-y: scroll;
    overflow-x: hidden;
    margin-bottom: 40px;
  }

  .scrollable-container-e-service {
    max-height: 1250px;
    overflow-y: scroll;
    overflow-x: hidden;
    margin-bottom: 40px;
    padding-left: 2%;
  }

  .scrollable-container-500 {
    max-height: 500px;
    overflow-y: scroll;
    overflow-x: hidden;
    margin-bottom: 40px;
  }

  .scrollable-container-otop {
    max-height: 450px;
    overflow-y: scroll;
    overflow-x: hidden;
    margin-bottom: 40px;
    margin-top: 10px;
  }

  /* ปรับแต่งการเลื่อน */
  .scrollable-container-otop::-webkit-scrollbar {
    width: 10px;
    /* ความกว้างของแถบเลื่อน */
  }

  .scrollable-container-otop::-webkit-scrollbar-thumb {
    background-color: #888;
    /* สีของแถบเลื่อน */
    border-radius: 5px;
    /* ทำให้แถบเลื่อนโค้งมน */
    border: 2px solid #f9f9f9;
    /* ขอบรอบ ๆ แถบเลื่อน */
  }

  .scrollable-container-otop::-webkit-scrollbar-thumb:hover {
    background-color: #555;
    /* สีของแถบเลื่อนเมื่อวางเมาส์ */
  }

  .scrollable-container-otop::-webkit-scrollbar-track {
    background-color: #f1f1f1;
    /* สีของพื้นหลังแถบเลื่อน */
    border-radius: 5px;
    /* ทำให้พื้นหลังแถบเลื่อนโค้งมน */
  }

  /* เพิ่มการเลื่อนที่เนียน */
  .scrollable-container-otop {
    scroll-behavior: smooth;
    /* การเลื่อนที่ราบรื่น */
  }


  .scrollable-container-eGP {
    max-height: 1500px;
    overflow-y: scroll;
    overflow-x: hidden;
    margin-bottom: 40px;
    margin-top: 20px;
  }

  .scrollable-container-gi {
    max-height: 850px;
    overflow-y: scroll;
    overflow-x: hidden;
    margin-bottom: 40px;
    margin-top: 10px;
  }

  .scrollable-container-p {
    max-height: 1200px;
    overflow-y: scroll;
    overflow-x: hidden;
    margin-bottom: 40px;
    margin-top: 10px;
  }

  /* กำหนดสไตล์ scroll bar สำหรับ WebKit (Chrome, Safari) */
  /* ::-webkit-scrollbar {
    height: 5px;
    width: 5px;
  }

  ::-webkit-scrollbar-track {
    border-radius: 33.559px;
    background: #FFF;
    box-shadow: 0px 2.685px 2.685px 0px rgba(0, 0, 0, 0.25);
  }

  ::-webkit-scrollbar-thumb {
    border-radius: 33.559px;
    background: #523003;
    box-shadow: 0px 2.685px 2.685px 0px rgba(0, 0, 0, 0.25);
  }

  ::-webkit-scrollbar-thumb:hover {
    background: #888;
  } */

  /* scroll bar เลื่อนซ้ายขวา เลื่อนบนล่าง ****************************************************** */



  .content-e-service {
    margin-top: 0px;
  }

  /* ให้ทุุกอย่างที่อยู่ใน bg background มันอยู่ตรงกลาง ******************************************** */
  .bg-personnel-s {
    background-image: url('<?php echo base_url("docs/s.bg-personnel.png"); ?>');
    background-repeat: no-repeat;
    background-size: 100%;
    width: 239px;
    height: 270px;
    display: grid;
    place-items: center;
    position: relative;
    z-index: 10;
  }

  .rounded-image-s {
    width: 188px;
    height: 228px;
  }

  .bg-personnel-n {
    background-image: url('<?php echo base_url("docs/bg_nameinfo.png"); ?>');
    background-repeat: no-repeat;
    background-size: 100%;
    width: 450px;
    height: 180px;
    display: flex;
    /* เปลี่ยนเป็น flex เพื่อความยืดหยุ่นมากขึ้น */
    justify-content: center;
    align-items: center;
    position: relative;
    z-index: 10;
  }

  /* รูปบุคลากรแบบวงกลม personnel */
  /* .bg-personnel-m {
    background-image: url('<?php echo base_url("docs/bg-personnel-s.png"); ?>');
    background-repeat: no-repeat;
    background-size: 100%;
    width: 298px;
    height: 298px;
    display: grid;
    place-items: center;
  }

  .rounded-image-m {
    width: 250px;
    height: 270px;
    clip-path: ellipse(55% 50% at 50% 50%);
  } */

  .center-center {
    text-align: center;
    display: flex;
    flex-direction: column;
    align-items: center;
  }

  .show {
    z-index: 999;
    display: none;
  }

  .show .overlay {
    width: 100%;
    height: 100%;
    background: rgba(0, 0, 0, .66);
    position: fixed;
    top: 0;
    left: 0;
    z-index: 10;
  }

  .show .img-show {
    width: 1000px;
    height: 700px;
    background: #FFF;
    position: absolute;
    /* เปลี่ยนเป็น position: absolute; */
    top: 50%;
    left: 50%;
    transform: translate(-50%, -50%);
    overflow: hidden;
    z-index: 999;

  }

  @media only screen and (min-device-width: 768px) and (max-device-width: 1024px) {
    .show .img-show {
      width: 1000px;
      height: 700px;
      left: 50%;
      transform: translate(-50%, -50%);
      margin-top: 40%;
    }
  }

  .img-show img {
    width: 100%;
    height: 100%;
    position: absolute;
    top: 0;
    left: 0;
  }

  select.custom-select {
    color: #693708;

    font-size: 20.102px;
    font-style: normal;
    font-weight: 500;
    line-height: 42.294px;
    background-image: url('<?php echo base_url("docs/icon-down.png"); ?>');
  }

  select.custom-select option {
    color: black;
  }

  .input-radius {
    border-radius: 20px;
    background: #fff;
    text-align: center;
    height: 47px;
  }

  .test {
    display: flex;
    align-items: center;
    justify-content: center;
  }

  .flex-nowrap {
    display: flex;
    flex-wrap: nowrap;
  }

  .container-pages {
    padding-left: 230px;
    padding-right: 230px;
    z-index: 10;
    position: relative;
  }

  .container-pages-news {
    padding-left: 230px;
    padding-right: 230px;
    z-index: 10;
    position: relative;
  }

  .container-pages-detail {
    padding-left: 230px;
    padding-right: 230px;
    z-index: 10;
    position: relative;
  }

  .border-radius24 {
    border-radius: 100px;
    background: url(<path-to-image>), lightgray -1.724px 0px / 101.139% 100% no-repeat;
    box-shadow: 1.337px 1.337px 2.005px 0px rgba(0, 0, 0, 0.25);
    width: 50.131px;
    height: 50.439px;
    flex-shrink: 0;
  }

  .style-col-img {
    margin: auto;
  }

  .font-gi-head {
    color: #000;
    text-align: center;
    text-shadow: 0.536px 0.536px 0.536px rgba(0, 0, 0, 0.25);
    font-size: 32px;
    font-style: normal;
    font-weight: 500;
    line-height: normal;
  }

  .font-gi-content {
    color: #000;
    font-size: 24px;
    font-style: normal;
    font-weight: 300;
    line-height: 33px;
  }

  .font-gi-target {
    color: #000;

    font-size: 22px;
    font-style: normal;
    font-weight: 400;
    line-height: 40.349px;
    /* 183.406% */
  }

  .pad-left-35 {
    padding-left: 35px;
  }

  .mar-fb {
    padding-top: 55px;
    margin-left: 70px;
    border-radius: 8px;
  }

  .mar-es-intra {
    padding-top: 55px;
  }

  .mar-ita {
    padding-top: 10px;
  }

  .mar-right-10 {
    margin-right: 10px;
  }

  #SubmitLike {
    border: none;
    padding: 0;
    background: none;
    cursor: pointer;
  }

  #confirmButton {
    border: none;
    padding: 0;
    background: none;
    cursor: pointer;
  }

  #loginBtn {
    border: none;
    padding: 0;
    background: none;
    cursor: pointer;
  }

  .btn-ita-open {
    color: #693708;
    background: #FCBF6A;
    font-size: 20px;
    font-weight: 500;
    border-radius: 25px;
    width: 91px;
    height: 32px;
    display: flex;
    align-items: center;
    justify-content: center;
    box-shadow: 0px 2px 0px 2px rgba(0, 0, 0, 0.15);
  }

  .btn-ita-open:hover {
    color: #FCBF6A;
    background: #693708;
    font-size: 20px;
    font-weight: 500;
  }

  .btn-esv-download {
    color: #693708;
    background: #FCBF6A;
    font-size: 20px;
    font-weight: 500;
    border-radius: 25px;
    width: 120px;
    height: 35px;
    display: flex;
    align-items: center;
    justify-content: center;
    box-shadow: 0px 2px 0px 2px rgba(0, 0, 0, 0.15);
  }

  .btn-esv-download:hover {
    color: #FCBF6A;
    background: #693708;
    font-size: 20px;
    font-weight: 500;
  }

  .bg-ita-color {
    border-top: 1px solid #ECB23F;
    border-bottom: 1px solid #ECB23F;
    background: #FFF4D0;
    padding-bottom: 20px;
  }

  .page-travel-content {
    height: 140px;
  }

  .pagination-next-prev {
    padding-right: 50px;
  }

  .font-head-travel {
    color: #000;
    leading-trim: both;
    text-edge: cap;
    font-feature-settings: 'clig' off, 'liga' off;
    font-family: Inter;
    font-size: 32px;
    font-style: normal;
    font-weight: 600;
    line-height: 40px;
    /* 100% */
  }

  .laws_ral_content {
    padding-left: 20px;
  }

  .dot-laws::before {
    content: '\2022';
    /* รหัสของ bullet point */
    color: black;
    /* สีของ bullet point */
    display: inline-block;
    width: 1em;
    /* ขนาดของ bullet point */
    margin-right: 0.5em;
    /* ระยะห่างระหว่าง bullet point กับข้อความ */
  }

  .pl-30 {
    padding-left: 30px;
  }

  .bg-how-e-service {
    /* background-image: url('<?php echo base_url("docs/bg-how-e-service.png"); ?>');
    background-repeat: no-repeat;
    background-size: 100%; */
    background-color: #005930;
    width: 100%;
    height: 70px;
    display: flex;
    /* หรือใช้ display: grid; */
    align-items: center;
    /* หรือใช้ justify-content: center; ถ้าใช้ display: grid; */
    padding-left: 40px;
  }


  .bg-head-e-service {
    border-radius: 50px;
    background: #FFFCF1;
    box-shadow: 2px 2px 10px 0px rgba(0, 0, 0, 0.25);
    width: 418px;
    height: 70px;
    flex-shrink: 0;
    display: flex;
    align-items: center;
    margin-top: 30px;
    padding-left: 30px;

  }

  .bg-content-e-service {
    border-radius: 34px;
    background: #FFFCF1;
    width: 100%;
    height: auto;
    flex-shrink: 0;
    box-shadow: 2px 2px 10px 0px rgba(0, 0, 0, 0.25);
    margin-top: 20px;
    padding: 15px 50px;
  }

  .pl-13p {
    padding-left: 13%;
  }

  .pl-20 {
    padding-left: 20px;

  }

  /* รูปภาพ ita-popup กลางหน้าจอ - รองรับ CSS scaling แบบ Heavy Protection */
  /* Main popup styles - Protection Level สูงสุด */
  .ita-popup-backdrop {
    display: none;
    position: fixed !important;
    top: 0 !important;
    left: 0 !important;
    right: 0 !important;
    bottom: 0 !important;
    width: 100vw !important;
    height: 100vh !important;
    min-width: 100vw !important;
    min-height: 100vh !important;
    max-width: 100vw !important;
    max-height: 100vh !important;
    background-color: rgba(0, 0, 0, 0.8);
    backdrop-filter: blur(8px);
    z-index: 999999 !important;
    opacity: 0;
    transition: opacity 0.3s ease-in-out;
    /* HEAVY PROTECTION: ป้องกันการถูกกระทบจาก transform ใดๆ */
    transform: none !important;
    scale: none !important;
    rotate: none !important;
    translate: none !important;
    filter: none !important;
    /* Reset ทุกอย่างที่อาจมาจาก parent */
    margin: 0 !important;
    padding: 0 !important;
    border: none !important;
    outline: none !important;
    box-sizing: border-box !important;
    /* Isolation สูงสุด */
    isolation: isolate;
    contain: layout style paint;
    /* ป้องกันการเลื่อน */
    overflow: hidden;
    /* ยึดให้แน่นกับ viewport */
    inset: 0 !important;
  }

  .ita-popup-backdrop.show {
    display: flex !important;
    align-items: center !important;
    justify-content: center !important;
    opacity: 1;
  }

  .ita-popup-container {
    position: absolute;
    left: 50%;
    top: 50%;
    transform: translate(-50%, -50%) scale(0.9);
    max-width: min(90%, 800px);
    max-height: 90vh;
    transition: transform 0.3s ease-in-out;
    background-color: #ffffff;
    padding: 15px;
    border-radius: 12px;
    box-shadow: 0 0 20px rgba(0, 0, 0, 0.2);
    animation: itaPopupFadeIn 0.5s forwards;
    /* ป้องกันการขยับ */
    transform-origin: center center;
    will-change: transform;
  }

  .ita-popup-backdrop.show .ita-popup-container {
    /* transform: scale(1);*/
  }

  .ita-popup-close-btn {
    position: absolute;
    top: -15px;
    right: -15px;
    width: 40px;
    height: 40px;
    background-color: #ffffff;
    border: 2px solid #e9ecef;
    border-radius: 50%;
    font-size: 20px;
    color: #495057;
    cursor: pointer;
    display: flex;
    align-items: center;
    justify-content: center;
    transition: all 0.3s ease;
    box-shadow: 0 4px 15px rgba(0, 0, 0, 0.2);
    z-index: 1000001;
  }

  .ita-popup-close-btn:hover {
    background-color: #f8f9fa;
    border-color: #dee2e6;
    transform: scale(1.1);
    box-shadow: 0 6px 20px rgba(0, 0, 0, 0.3);
  }

  .ita-popup-close-btn:active {
    transform: scale(0.95);
  }

  .ita-popup-link {
    display: block;
    position: relative;
    cursor: pointer;
    width: 100%;
    height: 100%;
  }

  .ita-popup-image {
    display: block;
    width: 100%;
    height: auto;
    max-height: 80vh;
    object-fit: contain;
    box-shadow: 0 10px 30px rgba(0, 0, 0, 0.3);
    border-radius: 8px;
    transition: transform 0.3s ease;
  }

  .ita-hover-overlay {
    position: absolute;
    top: 0;
    left: 0;
    width: 100%;
    height: 100%;
    background-color: rgba(0, 0, 0, 0);
    transition: background-color 0.3s ease;
    display: flex;
    align-items: center;
    justify-content: center;
    border-radius: 8px;
  }

  .ita-hover-text {
    color: white;
    font-size: clamp(1rem, 2vw, 1.2rem);
    padding: 10px 20px;
    background-color: rgba(0, 0, 0, 0.7);
    border-radius: 5px;
    opacity: 0;
    transition: opacity 0.3s ease;
    text-align: center;
  }

  .ita-popup-link:hover .ita-hover-overlay {
    background-color: rgba(0, 0, 0, 0.1);
  }

  .ita-popup-link:hover .ita-hover-text {
    opacity: 1;
  }

  .ita-navigation {
    position: absolute;
    bottom: 20px;
    left: 0;
    right: 0;
    display: flex;
    justify-content: center;
    align-items: center;
    z-index: 1000001;
  }

  .ita-dots {
    display: flex;
    gap: 12px;
    background-color: rgba(0, 0, 0, 0.6);
    padding: 8px 16px;
    border-radius: 20px;
    backdrop-filter: blur(8px);
  }

  .ita-dot {
    width: 12px;
    height: 12px;
    border-radius: 50%;
    background-color: rgba(255, 255, 255, 0.4);
    cursor: pointer;
    transition: all 0.3s ease;
    border: 2px solid transparent;
  }

  .ita-dot:hover {
    background-color: rgba(255, 255, 255, 0.7);
    transform: scale(1.2);
  }

  .ita-dot.active {
    background-color: #007bff;
    border-color: #ffffff;
    transform: scale(1.3);
    box-shadow: 0 0 10px rgba(0, 123, 255, 0.5);
  }

  /* ลบ slide animation ออกเพื่อป้องกันการกระดิก */

  @keyframes itaPopupFadeIn {
    from {
      opacity: 0;
      transform: translate(-50%, -50%) translateY(-30px) scale(0.9);
    }

    to {
      opacity: 1;
      transform: translate(-50%, -50%) translateY(0) scale(0.9);
    }
  }

  /* Mobile responsive */
  @media (max-width: 768px) {
    .ita-popup-container {
      padding: 10px;
      max-width: min(95%, 800px);
    }

    .ita-popup-close-btn {
      top: -15px;
      right: -15px;
      width: 30px;
      height: 30px;
      font-size: 20px;
    }

    .ita-dot {
      width: 8px;
      height: 8px;
    }
  }

  /* Accessibility */
  @media (prefers-reduced-motion: reduce) {

    .ita-popup-backdrop,
    .ita-popup-container,
    .ita-popup-close-btn,
    .ita-hover-overlay,
    .ita-hover-text,
    .ita-dot,
    .ita-popup-image {
      transition: none !important;
      animation: none !important;
    }
  }

  /* Focus states */
  .ita-popup-close-btn:focus,
  .ita-dot:focus {
    outline: 3px solid #007bff;
    outline-offset: 2px;
  }

  .ita-popup-link:focus {
    outline: 3px solid #007bff;
    outline-offset: 4px;
  }

  /* ///////////////////////////////////// */

  .close-button-slide-mid {
    position: absolute;
    top: 0;
    right: 0;
    border: none;
    cursor: pointer;
  }

  /* .popup-ita {
    display: none;
    position: fixed;
    top: 50%;
    left: 50%;
    transform: translate(-50%, -50%);
    background-color: #fff;
    padding: 20px;
    box-shadow: 0px 0px 10px 0px rgba(0, 0, 0, 0.5);
    width: 864px;
    height: 354px;
    flex-shrink: 0;
    border-radius: 30px;
    border: 2px solid #F5900A;
    background: #FEFCF7;
    z-index: 5;
  }

  .popup-ita-content {
    text-align: center;
  }

  .popup-ita-content button {
    color: #693708;
    background: #FCBF6A;
    font-size: 20px;
    font-weight: 500;
    
    border-radius: 25px;
    width: 91px;
    height: 32px;
    text-align: center;
    text-decoration: none;
    display: inline-block;
    box-shadow: 0px 2px 0px 2px rgba(0, 0, 0, 0.15);
    margin-top: 20%;
  } */

  .font-ita-content-detail {
    color: #000;

    font-size: 20px;
    font-style: normal;
    padding-left: 150px;
  }

  .font-doc {
    font-size: 20px;
    text-shadow: 1px 1px #ccc;
    /* padding-left: 20px; */
    margin-left: -20px;
  }


  .bg-banner {
    background-image: url('<?php echo base_url("docs/bg-banner.png"); ?>');
    background-repeat: no-repeat;
    background-size: 100%;
    width: 1920px;
    height: 1000px;
    z-index: 1;
  }

  .six-menu {
    background-image: url('<?php echo base_url("docs/bt-header.png"); ?>');
    background-repeat: no-repeat;
    background-size: 100% 100%;
    width: 100%;
    max-width: 1457px;
    height: 58px;
    z-index: 1;
    margin: 0 auto;
    position: relative;
  }

  .six-menu ul {
    display: flex;
    list-style-type: none;
    padding: 0;
    margin: 0;
    height: 50px;
    align-items: center;
    justify-content: center;
    /* จัดให้อยู่กึ่งกลาง */
    gap: 0;
    /* ลบระยะห่างระหว่างเมนู */
  }

  .six-menu ul>div {
    position: relative;
    display: flex;
    align-items: center;
    justify-content: center;
    height: 50px;
  }

  .header-nav-link {
    color: #404040;
    text-decoration: none;
    font-size: 24px;
    font-weight: 600;
    height: 50px;
    display: flex;
    align-items: center;
    justify-content: center;
    position: relative;
    white-space: nowrap;
    padding: 0;
    width: 100%;
    /* ให้ลิงก์เต็มความกว้างของ div */
  }

  /* สไตล์สำหรับ hover effects */
  .header-nav-link::before {
    content: '';
    position: absolute;
    top: 50%;
    left: 50%;
    transform: translate(-50%, -50%);
    /* จัดให้อยู่กึ่งกลางพอดี */
    background-size: 100% 100%;
    background-repeat: no-repeat;
    opacity: 0;
    transition: opacity 0.3s ease;
    z-index: -1;
  }

  .header-nav-link:hover {
    color: #404040;
  }

  .header-nav-link:hover::before {
    opacity: 1;
  }

  /* กำหนดความกว้างของแต่ละเมนู */
  .six-menu ul>div:nth-child(1) {
    width: 208px;
  }

  .six-menu ul>div:nth-child(2) {
    width: 300px;
  }

  .six-menu ul>div:nth-child(3) {
    width: 245px;
  }

  .six-menu ul>div:nth-child(4) {
    width: 259px;
  }

  .six-menu ul>div:nth-child(5) {
    width: 259px;
  }

  .six-menu ul>div:nth-child(6) {
    width: 178px;
  }

  /* กำหนด background-image สำหรับแต่ละเมนู */
  .header-nav-link[data-hover="1"]::before {
    background-image: url('<?php echo base_url("docs/bt-header-1-hover.png"); ?>');
    width: 208px;
    height: 50px;
  }

  .header-nav-link[data-hover="2"]::before {
    background-image: url('<?php echo base_url("docs/bt-header-2-hover.png"); ?>');
    width: 300px;
    height: 50px;
  }

  .header-nav-link[data-hover="3"]::before {
    background-image: url('<?php echo base_url("docs/bt-header-3-hover.png"); ?>');
    width: 245px;
    height: 50px;
  }

  .header-nav-link[data-hover="4"]::before {
    background-image: url('<?php echo base_url("docs/bt-header-4-hover.png"); ?>');
    width: 259px;
    height: 50px;
  }

  .header-nav-link[data-hover="5"]::before {
    background-image: url('<?php echo base_url("docs/bt-header-5-hover.png"); ?>');
    width: 259px;
    height: 50px;
  }

  .header-nav-link[data-hover="6"]::before {
    background-image: url('<?php echo base_url("docs/bt-header-6-hover.png"); ?>');
    width: 178px;
    height: 50px;
  }

  .padding-topic {
    margin-top: -30px;
  }

  /* สไตล์หลัก */
  .menu-drop {
    background-image: url('<?php echo base_url("docs/menu-drop-2.png"); ?>');
    background-repeat: no-repeat;
    background-size: 100% 100%;
    width: 480px;
    height: 102px;
    display: flex;
    align-items: center;
    justify-content: center;
    padding: 0;
    color: #FFF;
    text-align: center;
    font-size: 26px;
    font-style: normal;
    font-weight: 600;
    line-height: normal;
    cursor: pointer;
    position: relative;
    z-index: 2;
  }

  /* ปรับข้อความในปุ่ม */
  .menu-drop span {
    position: relative;
    z-index: 2;
    width: 480px;
    text-align: center;
  }

  /* คอนเทนเนอร์หลัก */
  .menu-container {
    position: relative;
    margin-bottom: 20px;
    transition: all 0.3s ease-in-out;
  }

  /* เพิ่มพื้นที่ด้านล่างเมื่อ hover หรือ active */
  /* .menu-container:hover, */
  .menu-container.active {
    margin-bottom: 200px;
  }

  /* เนื้อหาที่จะแสดง */
  .menu-content {
    display: none;
    position: absolute;
    top: 80px;
    left: 0;
    width: 480px;
    background: #E3FBFF;
    opacity: 0;
    padding: 20px;
    z-index: 1;
    border-radius: 0 0 10px 10px;
    box-shadow: 0 6px 12px rgba(0, 0, 0, 0.1);
    padding-left: 43px;
    transition: all 0.3s ease;
  }

  /* แสดง menu-content เมื่อ hover หรือ active */
  /* .menu-container:hover .menu-content, */
  .menu-container.active .menu-content {
    display: block;
    opacity: 0.7;
    animation: fadeIn 0.3s ease forwards;
  }

  /* สไตล์สำหรับรายการใน menu-content */
  .menu-content div {
    padding: 8px 0;
    cursor: pointer;
  }

  .menu-content div:not(:last-child) {
    margin-bottom: 5px;
  }

  .menu-content div:hover {
    color: #F1AC16;
  }

  /* สไตล์สำหรับลิงก์ในเมนู */
  .menu-content a {
    text-decoration: none;
    color: inherit;
    display: flex;
    align-items: baseline;
    transition: color 0.2s ease;
  }

  /* เพิ่มสไตล์สำหรับจัดการจุด */
  .menu-content a::first-child {
    flex-shrink: 0;
    /* ป้องกันไม่ให้จุดหด */
    margin-right: 8px;
    /* ระยะห่างระหว่างจุดกับข้อความ */
  }

  /* จัดการข้อความให้ขึ้นบรรทัดใหม่อย่างเหมาะสม */
  .menu-content .font-nav {
    display: inline-block;
    padding-left: 10px;
    /* ระยะห่างจากจุดถึงข้อความ ปรับตามต้องการ */
    text-indent: -10px;
    /* ดึงบรรทัดแรกกลับมา */
  }

  .menu-content a:hover {
    text-decoration: none;
    color: #F1AC16;
  }

  /* สไตล์สำหรับรูปภาพในเมนู */
  .menu-content img {
    margin-right: 8px;
    width: 20px;
    height: 20px;
  }

  /* Animation สำหรับ fadeIn */
  @keyframes fadeIn {
    from {
      opacity: 0;
      transform: translateY(-10px);
    }

    to {
      opacity: 0.7;
      transform: translateY(0);
    }
  }

  .bg-E-book {
    background-image: url('<?php echo base_url("docs/bg-E-book.png"); ?>');
    width: 1408px;
    height: 332px;
    padding: 41px;
    position: relative;
    margin: 35px auto;
  }

  .text-ebook {
    color: #404040;
    text-shadow: 1px 1px 0 #fff,
      -1px -1px 0 #fff,
      1px -1px 0 #fff,
      -1px 1px 0 #fff,
      0 1px 0 #fff,
      1px 0 0 #fff,
      0 -1px 0 #fff,
      -1px 0 0 #fff,
      0px 4px 4px rgba(0, 0, 0, 0.25);
    font-size: 24px;
    font-style: normal;
    font-weight: 600;
    line-height: normal;
    position: absolute;
    z-index: 1;
  }

  .ebook-prev-btn,
  .ebook-next-btn {
    position: absolute;
    z-index: 5;
    cursor: pointer;
    width: 94px;
    height: 94px;
    margin-top: 104px;
    transition: opacity 0.3s ease;
  }

  .ebook-prev-btn {
    margin-left: 0px;
    background-image: url('<?php echo base_url("docs/bt-E-book-back.png"); ?>');
    background-size: contain;
    background-repeat: no-repeat;
    transition: all 0.3s ease;
    transform: scale(1);
  }

  .ebook-prev-btn:hover {
    background-image: url('<?php echo base_url("docs/bt-E-book-back-hover.png"); ?>');
    transform: scale(1.05);
    filter: brightness(1.1);
  }

  .ebook-next-btn {
    margin-left: 105px;
    background-image: url('<?php echo base_url("docs/bt-E-book-next.png"); ?>');
    background-size: contain;
    background-repeat: no-repeat;
    transition: all 0.3s ease;
    transform: scale(1);
  }

  .ebook-next-btn:hover {
    background-image: url('<?php echo base_url("docs/bt-E-book-next-hover.png"); ?>');
    transform: scale(1.05);
    filter: brightness(1.1);
  }

  /* เพิ่ม effect เมื่อกด */
  .ebook-prev-btn:active,
  .ebook-next-btn:active {
    transform: scale(0.95);
    transition: all 0.1s ease;
  }

  /* เพิ่ม shadow เมื่อ hover (ถ้าต้องการ) */
  .ebook-prev-btn:hover,
  .ebook-next-btn:hover {
    filter: brightness(1.1) drop-shadow(0 4px 8px rgba(0, 0, 0, 0.2));
  }

  /* เพิ่มส่วนสำหรับ slider */
  .ebook-container {
    position: absolute;
    top: 41px;
    left: 300px;
    width: 1090px;
    height: 250px;
    overflow: hidden;
    z-index: 1;
  }

  .ebook-slider {
    display: flex;
    gap: 40px;
    transition: transform 0.5s ease;
    width: fit-content;
  }

  .ebook-item {
    flex: 0 0 auto;
    width: 143px;
  }

  .ebook-prev-btn.disabled,
  .ebook-next-btn.disabled {
    opacity: 0.3 !important;
    cursor: not-allowed !important;
  }

  .bg-button {
    background-image: url('<?php echo base_url("docs/bg-banner2.png"); ?>');
    background-repeat: no-repeat;
    background-size: 100%;
    height: 1000px;
    width: 1920px;
    z-index: 1;
  }

  .top-bg-banner2 {
    background-image: url('<?php echo base_url("docs/top-bg-banner2.png"); ?>');
    background-repeat: no-repeat;
    background-size: 100%;
    height: 500px;
    width: 1920px;
    z-index: 2;
    position: absolute;
  }

  .bg-grass-banner {
    background-image: url('<?php echo base_url("docs/stone-button.png"); ?>');
    background-repeat: no-repeat;
    background-size: cover;
    height: 500px;
    width: 1920px;
    margin: auto;
    position: absolute;
    z-index: 3;
  }

  .bg-activity {
    background-image: url('<?php echo base_url("docs/bg-activity.png"); ?>');
    background-repeat: no-repeat;
    background-size: cover;
    height: 1000px;
    width: 1920px;
    margin: auto;
  }

  .water-activity {
    background-image: url('<?php echo base_url("docs/water-activity.png"); ?>');
    background-repeat: no-repeat;
    background-size: cover;
    height: 1000px;
    width: 1920px;
    margin: auto;
    position: absolute;
    z-index: 3;
  }

  .bg-video {
    background-image: url('<?php echo base_url("docs/bg-video.png"); ?>');
    background-repeat: no-repeat;
    background-size: cover;
    height: 500px;
    width: 1920px;
    margin: auto;
    /* นี้จะทำให้ element อยู่ตรงกลางตามแนวนอน */
    /* margin-top: 200px; */
  }

  .custom-line {
    width: 443px;
    height: 2px;
    background-color: #4A0D49;
    margin-top: 25px;
  }

  .activity-line {
    width: 1px;
    height: 344px;
    background: #179CB1;
  }

  .position-2bg {
    width: 1920px;
    height: 2000px;
    overflow: hidden;
    position: relative;
  }

  .position-3bg {
    width: 1920px;
    height: 3500px;
    overflow: hidden;
    position: relative;
  }

  .position-3bg2 {
    width: 1920px;
    height: 3000px;
    overflow: hidden;
    position: relative;
  }

  .position-3bg3 {
    width: 1920px;
    height: 2500px;
    overflow: hidden;
    position: relative;
  }

  .bg-public-news {
    background-image: url('<?php echo base_url("docs/bg-public1.png"); ?>');
    background-repeat: no-repeat;
    background-size: cover;
    height: 1000px;
    width: 1920px;
    margin: auto;
  }

  .bg-public1-2 {
    background-image: url('<?php echo base_url("docs/bg-public1-2.png"); ?>');
    background-repeat: no-repeat;
    background-size: cover;
    height: 1000px;
    width: 1920px;
    margin: auto;
    position: absolute;
    z-index: 2;
  }

  .animation-crocodile-L {
    position: absolute;
    z-index: 2;
    top: -87px;
    left: -284px;
  }

  .animation-crocodile-R {
    position: absolute;
    z-index: 2;
    margin-top: 333px;
    margin-left: 1316px;
  }

  .bg-public1-2 {
    background-image: url('<?php echo base_url("docs/bg-public1-2.png"); ?>');
    background-repeat: no-repeat;
    background-size: cover;
    height: 1000px;
    width: 1920px;
    margin: auto;
    position: absolute;
    z-index: 2;
  }

  .bg-public-news-other {
    background-image: url('<?php echo base_url("docs/bg-public1.png"); ?>');
    background-repeat: no-repeat;
    background-size: cover;
    height: 500px;
    width: 1920px;
    margin: auto;
    overflow: hidden;
  }

  .dot-news-animation-1 {
    animation: blink-2 4s both infinite;
    position: absolute;
    /* margin-top: 370px; */
    /* margin-left: 20px; */
    z-index: 3;
  }

  .dot-news-animation-2 {
    animation: blink-2 4s both infinite;
    position: absolute;
    /* margin-top: 720px; */
    /* margin-left: 68px; */
    z-index: 3;
  }

  .dot-news-animation-3 {
    animation: blink-2 4s both infinite;
    position: absolute;
    /* margin-top: 40px; */
    /* margin-left: 115px; */
    z-index: 3;
  }

  .dot-news-animation-4 {
    animation: blink-2 4s both infinite;
    position: absolute;
    /* margin-top: 120px; */
    /* margin-left: 300px; */
    z-index: 3;
  }

  .dot-news-animation-5 {
    animation: blink-2 4s both infinite;
    position: absolute;
    /* margin-top: 732px; */
    /* margin-left: 720px; */
    z-index: 3;
  }

  .dot-news-animation-6 {
    animation: blink-2 4s both infinite;
    position: absolute;
    /* margin-top: 423px; */
    /* margin-left: 1030px; */
    z-index: 3;
  }

  .dot-news-animation-7 {
    animation: blink-2 4s both infinite;
    position: absolute;
    /* margin-top: 305px; */
    /* margin-left: 1120px; */
    z-index: 3;
  }

  .dot-news-animation-8 {
    animation: blink-2 4s both infinite;
    position: absolute;
    /* margin-top: 717px; */
    /* margin-left: 1180px; */
    z-index: 3;
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
    z-index: 3;
  }

  .dot-news-animation-11 {
    animation: blink-2 4s both infinite;
    position: absolute;
    /* margin-top: 370px; */
    /* margin-left: 1810px; */
    z-index: 3;
  }

  .dot-news-animation-12 {
    animation: blink-2 4s both infinite;
    position: absolute;
    /* margin-top: 60px; */
    /* margin-left: 1880px; */
    z-index: 3;
  }

  .dot-news-animation-13 {
    animation: blink-2 4s both infinite;
    position: absolute;
    /* margin-top: 605px; */
    /* margin-left: 1870px; */
    z-index: 3;
  }

  .dot-news-animation-14 {
    animation: blink-2 4s both infinite;
    position: absolute;
    /* margin-top: 605px; */
    /* margin-left: 1870px; */
    z-index: 3;
  }

  .dot-news-animation-15 {
    animation: blink-2 4s both infinite;
    position: absolute;
    /* margin-top: 605px; */
    /* margin-left: 1870px; */
    z-index: 3;
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

  .bg-public-news2 {
    background-image: url('<?php echo base_url("docs/bg-public2.png"); ?>');
    background-repeat: no-repeat;
    background-size: cover;
    height: 1000px;
    width: 1920px;
    margin: auto;
    z-index: 1;
  }

  .bg-news-dla {
    background-image: url('<?php echo base_url("docs/news-dla.png"); ?>');
    background-repeat: no-repeat;
    background-size: cover;
    height: 1000px;
    width: 1920px;
    margin: auto;
    z-index: 1;
    position: relative;
    overflow: hidden;
  }

  .bg-dla {
    background-image: url('<?php echo base_url("docs/b.dla.png"); ?>');
    background-repeat: no-repeat;
    background-size: cover;
    width: 1454px;
    height: 431px;
    margin: auto;
    z-index: 2;
    position: relative;
    overflow: hidden;
  }

  .bg-provlocal {
    background-image: url('<?php echo base_url("docs/b.provlocal.png"); ?>');
    background-repeat: no-repeat;
    background-size: cover;
    width: 1454px;
    height: 431px;
    margin: auto;
    z-index: 2;
    position: relative;
  }

  .font-dla-header {
    color: #FFF;
    text-align: center;
    font-family: "Noto Looped Thai UI";
    font-size: 30px;
    font-style: normal;
    font-weight: 600;
    line-height: normal;
  }

  .font-dla-1 {
    overflow: hidden;
    color: #8E2677;
    text-overflow: ellipsis;
    font-family: "Noto Looped Thai UI";
    font-size: 24px;
    font-style: normal;
    font-weight: 600;
    line-height: 35.114px;
    /* 146.308% */
  }

  .font-dla-2 {
    overflow: hidden;
    -webkit-box-orient: vertical;
    -webkit-line-clamp: 1;
    color: #000;
    text-overflow: ellipsis;
    font-family: "Noto Looped Thai UI";
    font-size: 22px;
    font-style: normal;
    font-weight: 400;
    line-height: 35.114px;
  }

  .font-dla-2:hover {
    color: red;
    font-family: "Noto Looped Thai UI";
    font-size: 22px;
    font-style: normal;
    font-weight: 400;
    line-height: 35.114px;
  }

  .bt-new-dla {
    display: inline-block;
    /* เปลี่ยนจาก flex เป็น inline-block */
    vertical-align: middle;
    /* จัดให้อยู่กึ่งกลางแนวตั้ง */
    border-radius: 2px;
    background: var(--Red-Line, linear-gradient(0deg, #B3140E -8.23%, #D9281E 32.92%, #DF3026 39.41%, #EA3C35 52.41%, #EE413A 64.32%, #FD5B47 100.05%));
    width: 44px;
    height: 22px;
    text-align: center;
    /* จัดข้อความตรงกลาง */
    line-height: 10px;
    /* ให้ข้อความอยู่กึ่งกลางแนวตั้ง */
    margin-right: 8px;
    /* เว้นระยะห่างจากหัวข้อข่าว */
    margin-top: 5px;
    /* ลบ margin-top */
  }

  .text-new-dla {
    color: #FFF;
    font-family: "Noto Looped Thai";
    font-size: 14px;
    font-style: normal;
    font-weight: 700;
    line-height: normal;
    padding-top: 0;
    /* ลบ padding-top */
  }

  .procurement-type-badge {
    display: inline-block;
    padding: 4px 10px;
    background-color: #e3f2fd;
    color: #1976d2;
    border-radius: 4px;
    font-size: 13px;
    font-weight: 500;
    white-space: nowrap;
    /* ป้องกันข้อความตัด */
    overflow: hidden;
    text-overflow: ellipsis;
    max-width: 100%;
  }

  /* ปรับ link ให้อยู่บรรทัดเดียวกับ new */
  .font-pages-content a {
    display: inline;
    vertical-align: middle;
  }

  .bt-new-dla-other {
    border-radius: 2px;
    background: var(--Red-Line, linear-gradient(0deg, #B3140E -8.23%, #D9281E 32.92%, #DF3026 39.41%, #EA3C35 52.41%, #EE413A 64.32%, #FD5B47 100.05%));
    width: 50px;
    height: 20px;
  }

  .bt-all-dla {
    background-image: url('<?php echo base_url("docs/b.all-dla.png"); ?>');
    width: 184px;
    height: 55px;
    display: flex;
    align-items: center;
    /* จัดข้อความในแนวตั้ง */
    justify-content: center;
    /* จัดข้อความในแนวนอน */
    position: relative;
    z-index: 10;
    pointer-events: auto;
    transition: background-image 0.6s ease;
  }

  .bt-all-dla:hover {
    background-image: url('<?php echo base_url("docs/b.all-dla-hover.png"); ?>');
    width: 184px;
    height: 55px;
    position: relative;
    z-index: 10;
    pointer-events: auto;
    transition: background-image 0.6s ease;
  }

  .bt-all-dla2 {
    background-image: url('<?php echo base_url("docs/b.all-dla.png"); ?>');
    width: 184px;
    height: 55px;
    display: flex;
    align-items: center;
    /* จัดข้อความในแนวตั้ง */
    justify-content: center;
    /* จัดข้อความในแนวนอน */
    position: relative;
    z-index: 10;
    pointer-events: auto;
    transition: background-image 0.6s ease;
  }

  .bt-all-dla2:hover {
    background-image: url('<?php echo base_url("docs/b.all-dla-hover2.png"); ?>');
    width: 184px;
    height: 55px;
    position: relative;
    z-index: 10;
    pointer-events: auto;
    transition: background-image 0.6s ease;
  }

  .font-all-dla {
    color: #000;
    text-shadow: #000;
    font-family: "Noto Looped Thai UI";
    font-size: 22px;
    font-style: normal;
    font-weight: 300;
    line-height: normal;
    /* margin-top: 5px; */
    /* ปรับค่าตามต้องการ */
  }

  .container-star-news-animation {
    position: absolute;
    width: 1920px;
    height: 300px;
    overflow: hidden;
    z-index: 3 !important;
    visibility: hidden;
  }

  .star-news-animation-1,
  .star-news-animation-2,
  .star-news-animation-3,
  .star-news-animation-4,
  .star-news-animation-5,
  .star-news-animation-6,
  .star-news-animation-7,
  .star-news-animation-8,
  .star-news-animation-9,
  .star-news-animation-10,
  .star-news-animation-11,
  .star-news-animation-12,
  .star-news-animation-13,
  .star-news-animation-14,
  .star-news-animation-15 {
    position: absolute;


    /* left: 100px; */
    /* animation: fadeInOut 4s infinite; */
  }

  /* ดาวตก animation  */
  @keyframes fadeInOut {

    0% {
      top: -100px;
      opacity: 1;
      visibility: visible;
    }


    100% {
      top: 300px;
      /* ตำแหน่งที่ออกไป */
      opacity: 0;
    }
  }

  .position-2bg-travel {
    position: relative;
    overflow: hidden;
    width: 1920px;
    height: 1600px;
  }

  .bg-travel {
    background-image: url('<?php echo base_url("docs/bg-travel.png"); ?>');
    background-repeat: no-repeat;
    background-size: cover;
    height: 1000px;
    width: 1920px;
    margin: auto;
  }

  .bg-travel-1 {
    background-image: url('<?php echo base_url("docs/bg-travel-1.png"); ?>');
    background-repeat: no-repeat;
    background-size: cover;
    height: 1000px;
    width: 1920px;
    margin: auto;
    position: absolute;
    z-index: 2;
  }

  .bg-travel-2 {
    background-image: url('<?php echo base_url("docs/bg-travel-2.png"); ?>');
    background-repeat: no-repeat;
    background-size: cover;
    height: 600px;
    width: 1920px;
    margin: auto;
  }

  .bg-travel-2-2 {
    background-image: url('<?php echo base_url("docs/bg-travel-2-1.png"); ?>');
    background-repeat: no-repeat;
    background-size: cover;
    height: 600px;
    width: 1920px;
    margin: auto;
    position: absolute;
    z-index: 3;
  }

  .water-animation2 {
    animation: moveWater 20s linear infinite;
    position: absolute;
    z-index: 2;
    margin-top: 30px;
    margin-left: -1000px;
  }

  @keyframes moveWater {
    from {
      transform: translateX(0);
    }

    to {
      transform: translateX(1000px);
    }
  }

  .bg-head-travel {
    background-image: url('<?php echo base_url("docs/head-travel.png"); ?>');
    background-repeat: no-repeat;
    background-size: cover;
    height: 133px;
    width: 607px;
    margin: auto;
    position: relative;
  }

  .bg-service {
    background-image: url('<?php echo base_url("docs/bg-eservice.png"); ?>');
    background-repeat: no-repeat;
    background-size: cover;
    height: 1000px;
    width: 1920px;
    margin: auto;
    /* นี้จะทำให้ element อยู่ตรงกลางตามแนวนอน */
    /* margin-top: 200px; */
  }

  .bg-link {
    background-image: url('<?php echo base_url("docs/bg-link.png"); ?>');
    background-repeat: no-repeat;
    background-size: cover;
    height: 1000px;
    width: 1920px;
    margin: auto;
    position: absolute;
    z-index: 1;
    overflow: hidden;
  }


  @keyframes bird-animation-L {
    0% {
      left: -20%;
    }

    100% {
      left: 120%;
    }
  }

  .bird-animation-L {
    position: absolute;
    animation: bird-animation-L 5s linear infinite;
    z-index: 1;
  }

  @keyframes bird-animation-R {
    0% {
      right: -20%;
    }

    100% {
      right: 120%;
    }
  }

  .bird-animation-R {
    position: absolute;
    animation: bird-animation-R 5s linear infinite;
    z-index: 1;
  }

  .bird-animation1 {
    margin-top: 180px;
  }

  .bird-animation2 {
    margin-top: 627px;
  }

  .boat-animation-L {
    position: absolute;
    white-space: nowrap;
    animation: boat-L 40s linear infinite;
    z-index: 2;
    visibility: hidden;
    /* เพิ่มบรรทัดนี้ */
  }

  .boat-animation-R {
    position: absolute;
    white-space: nowrap;
    animation: boat-R 40s linear infinite;
    z-index: 1;
    visibility: hidden;
    /* เพิ่มบรรทัดนี้ */
  }

  .boat-animation-1 {
    margin-top: 0px;
  }

  .boat-animation-2 {
    margin-top: -129px;
  }

  @keyframes boat-L {
    0% {
      left: -70%;
      visibility: visible;
    }

    10% {
      opacity: 1;
    }

    100% {
      left: 110%;
      visibility: visible;
      /* เพิ่มบรรทัดนี้เพื่อให้แน่ใจ */
    }
  }

  @keyframes boat-R {
    0% {
      right: -70%;
      visibility: visible;
    }

    10% {
      opacity: 1;
    }

    100% {
      right: 110%;
      visibility: visible;
      /* เพิ่มบรรทัดนี้เพื่อให้แน่ใจ */
    }
  }

  @keyframes fadeinleftoutright {
    0% {
      left: -10%;
      opacity: 0;
      visibility: hidden;
    }

    10% {
      opacity: 1;
      visibility: visible;
    }


    100% {
      left: 110%;
      opacity: 0;
      visibility: hidden;
    }
  }

  .cloud-animation {
    position: absolute;
    white-space: nowrap;
    animation: fadeinleftoutright 30s linear infinite;
    z-index: 1;
    visibility: hidden;
    /* ซ่อนภาพก่อนเริ่มแอนิเมชั่น */
  }

  .cloud-animation-1 {
    margin-top: 305px;
  }

  .cloud-animation-2 {
    margin-top: 515px;
    animation-delay: 1s;
  }

  .cloud-animation-3 {
    margin-top: 237px;
    animation-delay: 10s;
  }

  .cloud-animation-4 {
    margin-top: 447px;
    animation-delay: 12s;
  }

  .cloud-animation-5 {
    margin-top: 150px;
  }

  .cloud-animation-6 {
    margin-top: 150px;
    animation-delay: 10s;
  }

  .service-cartoon {
    background-image: url('<?php echo base_url("docs/e-service-cartoon.png"); ?>');
    width: 539px;
    height: 179px;
  }

  .otop-box {
    background-image: url('<?php echo base_url("docs/otop_travel_box.png"); ?>');
    width: 334px;
    height: 74px;
  }

  .font-header-service {
    color: #111A4E;
    text-align: center;
    font-size: 36px;
    font-style: normal;
    font-weight: 600;
    line-height: normal;
  }

  .crop-es {
    width: 445px;
    height: 60px;
    flex-shrink: 0;
    border-radius: 50px;
    background: rgba(255, 255, 255, 0.40);
  }

  .font-header-service-line {
    color: #170E08;
    text-align: center;
    font-family: "Noto Looped Thai UI";
    font-size: 22px;
    font-style: normal;
    font-weight: 500;
    line-height: normal;
  }

  .bg-service-line {
    width: 1038px;
    height: 49px;
    flex-shrink: 0;
    border-radius: 50px;
    background: #FFF;
    margin: auto;
    margin-top: 20px;
  }

  .service-box {
    z-index: 1;
    /* margin-left: 7%; */
    /* margin-top: 80px; */
    background-image: url('<?php echo base_url("docs/b.bt-eservice2.png"); ?>');
    background-repeat: no-repeat;
    width: 100%;
    height: 116px;
    margin-top: 5%;
  }

  .font-service {
    color: #FFF;
    -webkit-text-stroke-width: 0.4259740114212036;
    -webkit-text-stroke-color: #000;
    font-family: Inter;
    font-size: 22px;
    font-style: normal;
    font-weight: 400;
    line-height: normal;
  }

  .font-service:hover {
    color: #fff;
    -webkit-text-stroke-width: 0.4259740114212036;
    -webkit-text-stroke-color: #000;
    font-family: Inter;
    font-size: 23px;
    font-style: normal;
    font-weight: 400;
    line-height: normal;
  }

  .font-service {
    display: block;
    /* ทำให้แน่ใจว่า span เป็น block เพื่อให้ margin-top มีผล */
    margin-top: 1px;
    /* ปรับขนาดตามที่ต้องการ */
  }

  .bg-qa {
    z-index: 1;
    background-image: url('<?php echo base_url("docs/bg-qa.png"); ?>');
    background-repeat: no-repeat;
    width: 733px;
    height: 411px;
  }

  .bg-view {
    z-index: 1;
    margin-left: 15%;
    /* margin-top: 80px; */
    background-image: url('<?php echo base_url("docs/bg-view.png"); ?>');
    background-repeat: no-repeat;
    width: 360px;
    height: 317px;
    margin-top: 5%;
  }

  .bg-like {
    z-index: 1;
    margin-left: 26px;
    margin-top: 37px;
    background-image: url('<?php echo base_url("docs/bg-like.png"); ?>');
    background-repeat: no-repeat;
    transition: background-image 0.6s ease;
    width: 257px;
    height: 239px;
  }

  .bg-like:hover {
    background-image: url('<?php echo base_url("docs/bg-like-hover.png"); ?>');
  }

  .bg-prov {
    z-index: 1;
    background-image: url('<?php echo base_url("docs/b.Anti_Corruption.png"); ?>');
    background-repeat: no-repeat;
    width: 610px;
    height: 320px;
  }

  .bg-qa-list {
    background-image: url('<?php echo base_url("docs/bg-qa.png"); ?>');
    background-repeat: no-repeat;
    background-size: 100%;
    width: 788px;
    height: 648px;
    margin: auto;
    padding: 95px 22px;
  }

  .bg-content-qa-list {
    padding: 0 16.529px;
    align-items: center;
    gap: 38.018px;
    align-self: stretch;
    border-radius: 82.647px;
    border: 1px solid #EFB2CC;
    background: #FBF2F2;
    width: 647px;
    height: 40px;
    margin-left: 60px;
    position: relative;
    /* ทำให้แน่ใจว่า service-box ใช้การจัดตำแหน่งสัมพัทธ์ */
    top: 50px;
    /* ขยับตำแหน่งลงมา 20px */
    transition: background 0.3s ease, border-color 0.3s ease;
    /* เพิ่ม transition */
  }

  .bg-content-qa-list:hover {
    background: #FFD5DA;
    /* เปลี่ยนสีพื้นหลังเมื่อ hover */
    /* background: rgba(255, 255, 230, 0.50); */
    border-color: #EFB2CC;
    /* เปลี่ยนสีขอบเมื่อ hover */
  }

  .bt-qa-all {
    background-image: url('<?php echo base_url("docs/qa-all.png"); ?>');
    width: 135px;
    height: 45px;
    transition: background-image 0.6s ease;
    box-shadow: 0 4px 6px gray;
    border-radius: 25px 25px 25px 25px;
  }

  .bt-qa-all:hover {
    background-image: url('<?php echo base_url("docs/qa-all-hover.png"); ?>');
    width: 135px;
    height: 45px;
    transition: background-image 0.6s ease;
    box-shadow: 0 6px 8px gray;
  }

  .bt-qa-add {
    background-image: url('<?php echo base_url("docs/qa-add.png"); ?>');
    width: 135px;
    height: 45px;
    transition: background-image 0.6s ease;
    box-shadow: 0 4px 6px gray;
    border-radius: 25px 25px 25px 25px;
  }

  .bt-qa-add:hover {
    background-image: url('<?php echo base_url("docs/qa-add-hover.png"); ?>');
    transition: background-image 0.6s ease;
    box-shadow: 0 6px 8px gray;
  }

  .btn-like-add {
    background-image: url('<?php echo base_url("docs/qa-add.png"); ?>');
    width: 135px;
    height: 45px;
    position: relative;
    /* เพิ่มตำแหน่งเพื่อให้ z-index มีผล */
    z-index: 10;
    /* ค่าที่สูงกว่าค่า z-index ขององค์ประกอบอื่น */
    transition: background-image 0.6s ease;
    box-shadow: 0 4px 6px gray;
    border-radius: 25px 25px 25px 25px;
  }

  .btn-like-add:hover {
    background-image: url('<?php echo base_url("docs/qa-add-hover.png"); ?>');
    transition: background-image 0.6s ease;
    box-shadow: 0 6px 8px gray;
  }

  .btn-like-add .bt {
    width: 135px;
    height: 45px;
    /* ปรับขนาดความสูงตามที่คุณต้องการ */
    display: flex;
    justify-content: center;
    /* จัดกึ่งกลางแนวนอน */
    align-items: center;
    /* จัดกึ่งกลางแนวตั้ง */
  }

  .btn-like-add .bt span {
    padding-left: 0;
    /* รีเซ็ต padding-left เพื่อให้ข้อความอยู่กลาง */
  }

  .btn-like-see {
    background-image: url('<?php echo base_url("docs/qa-all.png"); ?>');
    width: 135px;
    height: 45px;
    position: relative;
    /* เพิ่มตำแหน่งเพื่อให้ z-index มีผล */
    z-index: 10;
    /* ค่าที่สูงกว่าค่า z-index ขององค์ประกอบอื่น */
    transition: background-image 0.6s ease;
    margin-top: -55px;
    margin-left: 185px;
    box-shadow: 0 4px 6px gray;
    border-radius: 25px 25px 25px 25px;
    padding-top: 5px;
  }

  .btn-like-see:hover {
    background-image: url('<?php echo base_url("docs/qa-all-hover.png"); ?>');
    width: 135px;
    height: 45px;
    transition: background-image 0.6s ease;
    box-shadow: 0 6px 8px gray;
  }

  .btn-like-see .bt {
    width: 135px;
    height: 45px;
    /* ปรับขนาดความสูงตามที่คุณต้องการ */
    display: flex;
    justify-content: center;
    /* จัดกึ่งกลางแนวนอน */
    align-items: center;
    /* จัดกึ่งกลางแนวตั้ง */
  }

  .btn-like-add .bt span {
    padding-left: 0;
    /* รีเซ็ต padding-left เพื่อให้ข้อความอยู่กลาง */
  }

  .btn-like-see .bt span {
    padding-left: 0;
    /* รีเซ็ต padding-left เพื่อให้ข้อความอยู่กลาง */
  }

  .font-header-qa {
    color: #964518;
    font-size: 24px;
    font-style: normal;
    font-weight: 700;
    line-height: normal;
  }

  .container-video-as {
    display: flex;
    align-items: flex-start;
  }

  .font-bt-qa {
    color: #FFF;
    font-size: 20px;
    font-style: normal;
    font-weight: 700;
    line-height: normal;
  }

  .font-view {
    color: #FFF;
    text-align: center;
    text-shadow: 0px 0px 10px rgba(159, 218, 255, 0.30);

    font-size: 16px;
    font-style: normal;
    font-weight: 600;
    line-height: normal;
  }

  .content-view {
    margin-top: -20px;
  }

  .head-view {
    padding: 10px;
    padding-top: 80px;
  }

  .font-like {
    color: #000;
    text-align: center;
    font-size: 20px;
    font-style: normal;
    font-weight: 600;
    line-height: normal;
  }

  .head-like {
    padding: 10px;
    padding-top: 80px;
  }

  .content-like {
    padding-top: 45px;
    padding-left: 60px;
  }

  .border-like {
    width: 25.263px;
    height: 24.211px;
    flex-shrink: 0;
    border-radius: 34px;
    border: 1.2px solid #D1D0D0;
  }

  .form-check {
    display: flex;
    align-items: center;
    margin-bottom: 10px;
    /* เพิ่มพื้นที่ระหว่างแต่ละรายการ */
  }

  .form-check-input {
    margin-right: 10px;
    /* เพิ่มพื้นที่ระหว่าง input และ label */
  }

  .form-check-label {
    margin: 0;
    /* ลบ margin เพื่อให้ label ตรงกับ input */
  }

  .font-like-label {
    color: #000;
    font-family: "Noto Looped Thai";
    font-size: 18px;
    font-style: normal;
    font-weight: 400;
    line-height: 29.991px;
    /* 166.617% */
  }

  .form-check-input:checked {
    background-color: #DFB7B0;
    border-color: #8B4437;
  }

  .progress-bar {
    width: 234px;
    height: 10px;
    flex-shrink: 0;
    border-radius: 34px;
    background: var(--Line-Y, linear-gradient(358deg, #FFDB51 -6.6%, #FF9300 105.68%));
    box-shadow: 0px 1.2px 1.2px 0px rgba(0, 0, 0, 0.10);
    /* margin-left: -150px; */
  }

  .font-link {
    color: #111A4E;
    text-align: center;
    font-size: 24px;
    font-style: normal;
    font-weight: 500;
    line-height: 47px;
    /* 195.833% */
  }

  .font-link2 {
    color: #FFF;
    text-align: center;
    font-size: 24px;
    font-style: normal;
    font-weight: 500;
    line-height: normal;
  }

  /* .contact-info {
    display: flex;
    align-items: center;
    justify-content: center;
    gap: 20px;
    margin-top: 10px;
    flex-wrap: wrap;
  } */

  .contact-item {
    display: flex;
    align-items: center;
    gap: 8px;
  }

  .contact-item img {
    vertical-align: middle;
  }

  .font-link2 {
    display: inline-block;
    vertical-align: middle;
  }

  .link-footer {
    margin-top: 665px;
    margin-left: 180px;
  }

  .font-nav {
    color: #000;
    font-size: 23px;
  }

  .font-nav:hover {
    color: #FFF33B;
  }

  .font-banner-link {
    color: #FFF;
    text-align: center;
    /* font-family: "Noto Looped Thai UI"; */
    font-size: 24px;
    font-style: normal;
    font-weight: 600;
    line-height: normal;
    position: relative;
  }

  .background-ps {
    width: 157px;
    height: 204px;
    border-radius: 10px;
    background: url(<path-to-image>) lightgray -12.321px -1.078px / 115.686% 108.857% no-repeat;
    box-shadow: 3px 6px 4px 0px rgba(0, 0, 0, 0.25);
  }


  .font-banner-link a {
    position: relative;
    z-index: 10;
    /* Ensure links are on top */
  }

  .calendar {
    display: flex;
    flex-direction: column;
    align-items: center;
    width: 300px;
    margin: 0 auto;
    font-family: Arial, sans-serif;
    justify-content: flex-start;
    /* ขยับมาทางซ้าย */
    padding-left: 30px;
  }

  .calendar-header {
    display: flex;
    justify-content: space-between;
    align-items: center;
    /* padding: 10px; */
    box-sizing: border-box;
    /* background-image: url('<?php echo base_url("docs/calendar-header.png"); ?>'); */
    width: 300px;
    height: 44px;
    margin-top: 40px;
  }

  .calendar-month-center {
    color: #fff;
    background-image: url('<?php echo base_url("docs/calendar-header.png"); ?>');
    width: 166px;
    height: 27px;
    position: relative;
    overflow: hidden;
    display: flex;
    align-items: center;
    /* จัดตำแหน่งแนวตั้งของข้อความ */
    justify-content: center;
    /* จัดตำแหน่งแนวนอนของข้อความ */
  }

  .prev-month-button {
    display: inline-block;
    width: 23px;
    height: 23px;
    margin-left: 30px;
    background-image: url('<?php echo base_url("docs/calendar-header-button-back.png"); ?>');
    background-size: cover;
    /* หรือใช้ contain ขึ้นอยู่กับการจัดการภาพ */
    background-position: center;
    transition: background-image 0.6s ease;
  }

  .prev-month-button:hover {
    background-image: url('<?php echo base_url("docs/calendar-header-button-back-hover.png"); ?>');
    transition: background-image 0.6s ease;
  }

  .next-month-button {
    display: inline-block;
    width: 23px;
    height: 23px;
    margin-right: 30px;
    background-image: url('<?php echo base_url("docs/calendar-header-button-next.png"); ?>');
    background-size: cover;
    /* หรือใช้ contain ขึ้นอยู่กับการจัดการภาพ */
    background-position: center;
    transition: background-image 0.6s ease;

  }

  .next-month-button:hover {
    background-image: url('<?php echo base_url("docs/calendar-header-button-next-hover.png"); ?>');
    transition: background-image 0.6s ease;
  }


  .weekdays,
  .days {
    display: flex;
    flex-wrap: wrap;
    width: 100%;
    flex-shrink: 0;
    /* ป้องกันไม่ให้ส่วนวันในสัปดาห์ถูกย่อขนาดลง */

  }

  .days-container {
    height: 190px;
    /* กำหนดความสูงส่วนที่แสดงวันในเดือน */
    overflow-y: auto;
    /* เพิ่ม scroll เมื่อเนื้อหาเกินขนาด */
    width: 100%;
  }

  .day,
  .weekday {
    width: 14.28%;
    text-align: center;
    padding: 10px 0;
    box-sizing: border-box;
  }

  .days {
    display: flex;
    flex-wrap: wrap;
    width: 100%;
    padding-right: 0px;
    /* เผื่อพื้นที่สำหรับ scrollbar */
  }

  /* ใส่ scrollbar styling เพื่อให้สวยงาม */
  .days-container::-webkit-scrollbar {
    width: 5px;
    margin-left: 20px;
  }

  .days-container::-webkit-scrollbar-track {
    background: #f1f1f1;
    border-radius: 10px;
  }

  .days-container::-webkit-scrollbar-thumb {
    background: #888;
    border-radius: 10px;
  }

  .days-container::-webkit-scrollbar-thumb:hover {
    background: #555;
  }

  .day {
    /* border: 1px solid #ddd; */
    color: black;
    /* สีดำสำหรับตัวเลข */
    width: 14%;
    text-align: center;
    padding: 3px 0;
    /* ลดระยะห่างของวัน */
    box-sizing: border-box;
    position: relative;
    display: flex;
    justify-content: center;
    align-items: center;
    flex-direction: column;
  }


  .event-dot {
    width: 6px;
    height: 6px;
    background-color: red;
    /* สีของจุด */
    border-radius: 50%;
    margin-top: 4px;
  }


  /* .day:hover {
    background-color: #FFD700;
    border-radius: 50% 50%;

  } */

  .weekday {
    /* background-color: #f0f0f0; */
    font-weight: bold;
  }

  .prev-month,
  .next-month {
    color: #ccc;
    /* สีเทาสำหรับวันของเดือนก่อนหน้าและถัดไป */
  }

  .current-day {
    background-color: #FED539;
    border: 1px solid #FED539;
    border-radius: 50% 50%;
  }

  .day:nth-child(7n+1) {
    color: #820000;
  }

  .day:not(:nth-child(7n+1)) {
    color: #1C455F;
  }

  .font-calender {
    color: #404040;
    text-align: center;
    /* font-family: "Noto Looped Thai UI"; */
    font-size: 24px;
    font-style: normal;
    font-weight: 600;
    line-height: normal;
  }

  .font-calender2 {
    color: #515151;
    font-size: 16px;
    font-style: normal;
    font-weight: 600;
    line-height: normal;
    padding-left: 15px;
  }

  .detail-text {
    display: block;
    margin-left: 1em;
    /* เพิ่มระยะห่างจากเครื่องหมาย &#183; */
    word-break: break-word;
    /* หรือใช้ word-wrap: break-word; */
  }

  .selected-day {
    /* background-color: #FED539; */
    border: 1px solid #FED539;
    border-radius: 50% 50%;
  }

  .carousel-indicators button {
    background-color: #4A0D49;
    /* Change to your desired color */
    border-radius: 50%;
    width: 12px;
    height: 12px;
  }

  .carousel-indicators .active {
    background-color: #4A0D49;
    /* Change to your desired color for the active indicator */
  }

  .carousel-indicators {
    /* background-color: #000; */
    /* Change to your desired color for the active indicator */
  }


  .font-like-new {
    color: #FFF;
    /* font-family: "Noto Looped Thai"; */
    font-size: 20px;
    font-style: normal;
    font-weight: 700;
    line-height: normal;
  }

  .welcome-other {
    /* background-image: url('<?php echo base_url("docs/s.welcome-other.png"); ?>'); */
    background-repeat: no-repeat;
    background-size: 100% 100%;
    /* ขนาดเต็ม 1920px x 600px */
    width: 1920px;
    height: 700px;
    /* แสดงความสูงที่คุณต้องการ */
    overflow: hidden;
    position: absolute;
    background-position: top;
    /* เริ่มจากด้านบน */
  }

  .welcome-btm-other {
    background-image: url('<?php echo base_url("docs/welcome-btm-other.png"); ?>');
    background-repeat: no-repeat;
    background-size: 100% 100%;
    z-index: 3;
    width: 1920px;
    height: 700px;
    position: relative;
  }

  @keyframes gradient-move-font {
    0% {
      background-position: 100% 0%;
    }

    100% {
      background-position: 0% 0%;
    }
  }

  .font-welcome-btm-other1 {
    color: #FFF;
    text-align: center;
    text-shadow: 2px 3px 4px rgba(0, 0, 0, 0.25);
    font-family: Charmonman;
    font-size: 48px;
    font-style: normal;
    font-weight: 700;
    line-height: normal;
  }

  .font-welcome-btm-other2 {
    color: #FFF;
    text-align: center;
    text-shadow: 0px 2px 4px rgba(0, 0, 0, 0.25);
    font-size: 26px;
    font-style: normal;
    font-weight: 400;
    line-height: normal;
  }

  .font-pages-head {
    color: #4A0D49;
    text-align: center;
    font-size: 36px;
    font-style: normal;
    font-weight: 600;
    line-height: 33.366px;
    /* 92.682% */
  }

  .font-other-head {
    color: #000;

    font-size: 32px;
    font-style: normal;
    font-weight: bold;
  }

  .font-other-content {
    color: #000;
    font-size: 24px;
    font-style: normal;
  }

  #scroll-to-top {
    display: none;
    position: fixed;
    bottom: 20px;
    right: 20px;
    z-index: 99;
    font-size: 18px;
    border: none;
    outline: none;
    background-image: url('<?php echo base_url("docs/scroll-to-top.png"); ?>');
    background-repeat: no-repeat;
    width: 76px;
    height: 76px;
    cursor: pointer;
    padding: 15px;
    border-radius: 4px;
    transition: background-image 0.6s ease;
  }

  #scroll-to-top:hover {
    background-image: url('<?php echo base_url("docs/scroll-to-top-hover.png"); ?>');
    background-repeat: no-repeat;
    width: 76px;
    height: 76px;
    transition: background-image 0.6s ease;
  }

  #scroll-to-top-other {
    display: none;
    position: fixed;
    bottom: 20px;
    right: 20px;
    z-index: 99;
    font-size: 18px;
    border: none;
    outline: none;
    background-image: url('<?php echo base_url("docs/scroll-to-top.png"); ?>');
    background-repeat: no-repeat;
    width: 76px;
    height: 76px;
    cursor: pointer;
    padding: 15px;
    border-radius: 4px;
    transition: background-image 0.6s ease;
  }

  #scroll-to-top-other:hover {
    background-image: url('<?php echo base_url("docs/scroll-to-top-hover.png"); ?>');
    background-repeat: no-repeat;
    width: 76px;
    height: 76px;
    transition: background-image 0.6s ease;
  }

  #scroll-to-back {
    display: none;
    position: fixed;
    bottom: 20px;
    right: 110px;
    z-index: 99;
    font-size: 18px;
    border: none;
    outline: none;
    background-image: url('<?php echo base_url("docs/scroll-to-back.png"); ?>');
    background-repeat: no-repeat;
    width: 76px;
    height: 76px;
    cursor: pointer;
    padding: 15px;
    border-radius: 4px;
    transition: background-image 0.6s ease;
  }

  #scroll-to-back:hover {
    background-image: url('<?php echo base_url("docs/scroll-to-back-hover.png"); ?>');
    background-repeat: no-repeat;
    width: 76px;
    height: 76px;
    transition: background-image 0.6s ease;
  }

  .btn-download {
    position: relative;
    z-index: 10;
  }

  .btn-download:hover {
    content: url('<?php echo base_url("docs/btn-download-hover.png"); ?>');
    position: relative;
    z-index: 10;
  }

  .mt-gi {
    margin-top: 150px;
  }

  .carousel-indicators {
    display: flex;
    justify-content: center;
    align-items: center;
    gap: 1px;
    /* ลดระยะห่างระหว่างไอคอน */
    bottom: -40px;
    margin-left: 380px;
  }

  .carousel-indicators button {
    background: none;
    border: none;
    display: flex;
    align-items: center;
    justify-content: center;
    padding: 0;
    margin: 0;
  }

  .carousel-indicators button .bi-diamond-fill {
    color: #FADFFF;
    /* สีของไอคอนเพชร สามารถเปลี่ยนได้ตามต้องการ */
  }

  .carousel-indicators button.active .bi-diamond-fill {
    color: #4A0D49;
    /* สีของไอคอนเพชรเมื่อ active สามารถเปลี่ยนได้ตามต้องการ */
  }


  /* ไฟลอยขึ้นบน animation */
  @keyframes fadeInOutDownUp {
    0% {
      top: 400px;
      opacity: 2;
    }

    100% {
      top: -100px;
      opacity: 0;
    }
  }



  .dot-updown-animation-1,
  .dot-updown-animation-2,
  .dot-updown-animation-3,
  .dot-updown-animation-4,
  .dot-updown-animation-5,
  .dot-updown-animation-6,
  .dot-updown-animation-7,
  .dot-updown-animation-8,
  .dot-updown-animation-9,
  .dot-updown-animation-10 {
    position: absolute;
    z-index: 4;
  }

  .console-container {
    font-size: 24px;
    text-align: center;
    height: 171px;
    width: 360px;
    display: block;
    position: relative;
    color: white;
    top: 55px;
    bottom: 0;
    left: 90px;
    right: 0;
    margin: auto;
    font-style: normal;
    font-weight: 400;
  }

  .console-underscore {
    display: inline-block;
    position: relative;
    top: -0.14em;
    left: 10px;
  }

  .hidden {
    visibility: hidden;
  }

  .bg-doc-off-head {
    border-radius: 34px 34px 0px 0px;
    border: 1px solid var(--main, #47B5FF);
    background: var(--main, #47B5FF);
    width: 1400px;
    height: 70px;
    flex-shrink: 0;
    margin-left: 10px;
  }

  .bg-doc-off-content {
    width: 1400px;
    height: auto;
    background: var(--sky, rgba(109, 207, 246, 0.30));
    padding-bottom: 15px;
  }

  .bg-doc-off-content-white {
    width: 1400px;
    height: auto;
    background-color: #fff;
    padding-bottom: 15px;
  }

  .box-time {
    width: 75.805px;
    height: 70.148px;
    flex-shrink: 0;
    border-radius: 14.316px;
    background: #FFDBEE;
    padding-top: 10px;
  }

  .font-pr-media-day {
    color: #002D53;
    text-align: center;
    font-family: Inter;
    font-size: 25.769px;
    font-style: normal;
    font-weight: 600;
    line-height: 100%;
    /* 25.769px */
  }

  .font-pr-media-mon {
    color: #002D53;
    text-align: center;
    font-family: Inter;
    font-size: 14.316px;
    font-style: normal;
    font-weight: 600;
    line-height: 100%;
    /* 14.316px */
  }

  .font-pr-media-head {
    color: #000;
    font-family: Kanit;
    font-size: 18px;
    font-style: normal;
    font-weight: 300;
    line-height: normal;
  }

  .font-head-doc-off {
    color: #FFF;
    font-family: Kanit;
    font-size: 24px;
    font-style: normal;
    font-weight: 500;
    line-height: normal;
  }

  .font-pr-media-detail {
    color: #000;
    font-family: Kanit;
    font-size: 22px;
    font-style: normal;
    font-weight: 300;
    line-height: normal;
  }

  .most_urgent {
    color: red;
    font-weight: 400;
  }

  .very_urgent {
    color: orangered;
    font-weight: 400;
  }

  .green-color {
    color: green;
    font-weight: 400;
  }

  .line-ellipsis-dla1 {
    width: 200px;
    /* ปรับขนาดตามต้องการ */
    overflow: hidden;
    text-overflow: ellipsis;
    display: -webkit-box;
    -webkit-line-clamp: 1;
    -webkit-box-orient: vertical;
  }

  .line-ellipsis-dla2 {
    width: 100%;
    /* ปรับขนาดตามต้องการ */
    overflow: hidden;
    text-overflow: ellipsis;
    display: -webkit-box;
    -webkit-line-clamp: 1;
    -webkit-box-orient: vertical;
  }

  .line-ellipsis-dla-prov2-new {
    width: 100%;
    /* ปรับขนาดตามต้องการ */
    overflow: hidden;
    text-overflow: ellipsis;
    display: -webkit-box;
    -webkit-line-clamp: 1;
    -webkit-box-orient: vertical;
  }

  .news-dla-prov {
    padding-top: 30px;
    z-index: 4;
    position: relative;
  }

  .news-dla-prov2 {
    padding-top: 10px;
    z-index: 4;
    position: relative;
    width: 1400px;
    height: 724px;
    flex-shrink: 0;
    border-radius: 20px;
    background: rgba(255, 252, 242, 0.80);
  }

  .dla-end {
    width: 1352px;
    height: 1px;
    background: #9E7C46;
    margin: auto;
    margin-top: 10px;
  }

  .line-ellipsis-dla-prov2 {
    width: 670px;
    /* ปรับขนาดตามต้องการ */
    overflow: hidden;
    text-overflow: ellipsis;
    display: -webkit-box;
    -webkit-line-clamp: 1;
    -webkit-box-orient: vertical;
  }

  /*.line-ellipsis-dla-prov2-new {
    width: 620px;
    overflow: hidden;
    text-overflow: ellipsis;
    display: -webkit-box;
    -webkit-line-clamp: 1;
    -webkit-box-orient: vertical;
  }*/

  .ball-container {
    position: absolute;
    width: 1920px;
    height: 700px;
    overflow: hidden;
    z-index: 1;
    margin-top: 300px;
  }

  .ball-container2 {
    position: absolute;
    width: 1920px;
    height: 1000px;
    overflow: hidden;
    z-index: 1;
  }

  .ball-animation {
    position: absolute;
    animation: moveBall 20s linear infinite, fadeBall 20s linear infinite;
    z-index: 1;
    bottom: -30%;
  }

  @keyframes moveBall {
    0% {
      bottom: 0;
      opacity: 0;
    }

    10% {
      opacity: 1;
    }

    50% {
      bottom: 50%;
    }

    100% {
      bottom: 100%;
      opacity: 0;
    }
  }

  @keyframes fadeBall {

    0%,
    80%,
    100% {
      opacity: 0;
    }

    10%,
    70% {
      opacity: 1;
    }
  }

  /* ใช้ nth-child สร้างตำแหน่งและ delay อัตโนมัติ */
  <?php for ($i = 1; $i <= 21; $i++): ?>.ball-animation:nth-child(<?= $i ?>) {
    <?= ($i % 2 == 1) ? 'left: ' . (60 + ($i * 15)) . 'px;' : 'right: ' . (60 + ($i * 15)) . 'px;' ?>animation-delay: <?= ($i - 1) * 1 ?>s;
  }

  <?php endfor; ?>

  /* ฟองกลาง */
  .ball-animation:nth-child(21) {
    left: 50% !important;
    right: auto !important;
    transform: translateX(-50%);
  }

  .cloud-cartoon-animation-1 {
    margin-top: 98px;
  }

  .cloud-cartoon-animation-2 {
    margin-top: 243px;
    animation-delay: 3s;
  }

  .cloud-cartoon-animation-3 {
    margin-top: 266px;
    animation-delay: 13s;
  }

  .cloud-cartoon-animation-4 {
    margin-top: 120px;
    animation-delay: 16s;
  }

  .cloud-cartoon-animation-5 {
    margin-top: 672px;
    /* animation-delay: 9s; */
  }

  .cloud-cartoon-animation-6 {
    margin-top: 268px;
    animation-delay: 10.5s;
  }

  .cloud-cartoon-animation-7 {
    margin-top: 190px;
    animation-delay: 12.5s;
  }

  .cloud-cartoon-animation-8 {
    margin-top: 390px;
    animation-delay: 16.5s;
  }

  .cloud-cartoon-animation-9 {
    margin-top: 320px;
    animation-delay: 18s;
  }

  .cloud-cartoon-animation-10 {
    margin-top: 770px;
    animation-delay: 9.5s;
  }

  .cloud-cartoon-animation-11 {
    margin-top: 50px;
    animation-delay: 12s;
  }

  .cloud-cartoon-animation-12 {
    margin-top: 770px;
    animation-delay: 14s;
  }

  .cloud-cartoon-animation-13 {
    margin-top: 730px;
    animation-delay: 14.5s;
  }

  .cloud-cartoon-animation-14 {
    margin-top: 830px;
    animation-delay: 18s;
  }

  .cloud-cartoon-animation-15 {
    margin-top: 780px;
    animation-delay: 19s;
  }

  .cloud-cartoon-animation-16 {
    margin-top: 290px;
    animation-delay: 18.5s;
  }

  .cloud-cartoon-animation-17 {
    margin-top: 245px;
    animation-delay: 19.5s;
  }

  .pages-head {
    padding-top: 55px;
  }

  .underline-hover {
    text-decoration: underline;
    color: inherit;
  }

  .underline-hover:hover {
    color: blue;
  }

  .fade-container {
    position: relative;
    height: 500px;
    /* ปรับความสูงตามที่ต้องการ */
    width: 100%;
    /* ปรับความกว้างตามที่ต้องการ */
  }

  .fade-content {
    position: absolute;
    top: 0;
    left: 0;
    width: 100%;
    height: 100%;
    opacity: 0.6;
    /* เริ่มต้นที่ความโปร่งใส */
    transition: opacity 1s linear;
    /* ใช้ transition เพื่อทำให้การเปลี่ยนแปลงความโปร่งใสเนียนขึ้น */
    z-index: 0;
    /* ซ่อน div ที่ไม่ได้แสดงอยู่ */
    display: none;
    /* ซ่อน div ที่ไม่ได้ active */
  }

  .fade-content.active {
    opacity: 1;
    /* ความโปร่งใสเต็มที่เมื่อ div มีคลาส active */
    z-index: 1;
    /* ทำให้ div ที่ active อยู่เหนือ div อื่นๆ */
    display: block;
    /* แสดง div ที่กำลังใช้งาน */
  }

  .wel-g1-bg {
    background-image: url('<?php echo base_url("docs/bg-welcome.png"); ?>');
    position: absolute;
    width: 1920px;
    height: 1000px;
    z-index: 2;
    /* animation: zoomInAndMoveToLeftBottom 5s ease-in-out; */
  }

  .wel-g1-bg2 {
    background-image: url('<?php echo base_url("docs/lotus-top.png"); ?>');
    position: absolute;
    width: 1920px;
    height: 1000px;
    z-index: 3;
    /* animation: zoomInAndMoveToLeftBottom 5s ease-in-out; */
  }

  .wel-g1-sky {
    background-image: url('<?php echo base_url("docs/sky-g1.png"); ?>');
    position: absolute;
    background-repeat: no-repeat;
    background-size: 100% 100%;
    /* ขนาดเต็ม 1920px x 600px */
    width: 1920px;
    height: 1000px;
    /* แสดงความสูงที่คุณต้องการ */
    overflow: hidden;
    position: absolute;
    background-position: top;
    /* เริ่มจากด้านบน */
    /* margin-top: -600px; */
    /* animation: zoomOutsky 25s ease-in-out infinite; */
  }

  .wel-g2-sky {
    background-image: url('<?php echo base_url("docs/sky-g2.png"); ?>');
    position: absolute;
    background-repeat: no-repeat;
    background-size: 100% 100%;
    /* ขนาดเต็ม 1920px x 600px */
    width: 1920px;
    height: 1000px;
    /* แสดงความสูงที่คุณต้องการ */
    overflow: hidden;
    position: absolute;
    background-position: top;
    /* เริ่มจากด้านบน */
    /* margin-top: -600px; */
    /* animation: zoomOutsky 25s ease-in-out infinite; */
  }

  @keyframes zoomOutsky {
    0% {
      transform: scale(1);
    }

    100% {
      transform: scale(1.5);
    }
  }

  @keyframes CenterOutRightWel {
    0% {
      left: 35%;
      opacity: 1;
    }

    100% {
      left: 100%;
      opacity: 0;
    }
  }

  .wel-g1-animation-1 {
    position: absolute;
    white-space: nowrap;
    animation: CenterOutRightWel 17s linear;
    animation-delay: 1s;
    left: 35%;
    top: 5%;
  }

  /* ตั้งค่าการหายไปให้กับ div หลัก */
  .wel-g1-animation-1 {
    animation-fill-mode: forwards;
    /* เก็บสถานะสุดท้ายของ animation */
  }

  /* ใช้ pseudo-element ::after เพื่อทำให้ div หลักหายไปหลังแอนิเมชันจบ */
  .wel-g1-animation-1::after {
    content: '';
    animation: disappear 0s 17s forwards;
    /* ซ่อน div หลังจากแอนิเมชันหลักจบลง */
  }

  @keyframes RightOutRightWel {
    0% {
      left: 60%;
      opacity: 1;
    }

    100% {
      left: 100%;
      opacity: 0;
    }
  }

  .wel-g1-animation-2 {
    position: absolute;
    white-space: nowrap;
    animation: RightOutRightWel 12s linear;
    animation-delay: 1s;
    left: 60%;
    top: 3%;
  }

  /* Keyframes ใหม่เพื่อทำให้ div หายไป */
  @keyframes disappear {
    to {
      visibility: hidden;
      /* ซ่อน div */
    }
  }

  /* ตั้งค่าการหายไปให้กับ div หลัก */
  .wel-g1-animation-2 {
    animation-fill-mode: forwards;
    /* เก็บสถานะสุดท้ายของ animation */
  }

  /* ใช้ pseudo-element ::after เพื่อทำให้ div หลักหายไปหลังแอนิเมชันจบ */
  .wel-g1-animation-2::after {
    content: '';
    animation: disappear 0s 12s forwards;
    /* ซ่อน div หลังจากแอนิเมชันหลักจบลง */
  }

  @keyframes CenterOutleftWel {
    0% {
      left: 15%;
      opacity: 1;
    }

    100% {
      left: 0;
      opacity: 0;
    }
  }

  .wel-g1-animation-3 {
    position: absolute;
    white-space: nowrap;
    animation: CenterOutleftWel 5s linear;
    animation-delay: 1s;
    left: 15%;
    top: 3%;
  }

  /* ตั้งค่าการหายไปให้กับ div หลัก */
  .wel-g1-animation-3 {
    animation-fill-mode: forwards;
    /* เก็บสถานะสุดท้ายของ animation */
  }

  /* ใช้ pseudo-element ::after เพื่อทำให้ div หลักหายไปหลังแอนิเมชันจบ */
  .wel-g1-animation-3::after {
    content: '';
    animation: disappear 0s 5s forwards;
    /* ซ่อน div หลังจากแอนิเมชันหลักจบลง */
  }

  .wel-g2-bg {
    background-image: url('<?php echo base_url("docs/bg-animation-g2.png"); ?>');
    position: absolute;
    width: 1920px;
    height: 1000px;
    top: 0;
    z-index: 1;
    overflow: hidden;
    animation: zoomInAndMoveToLeftBottom 5s ease-in-out;
    /* เพิ่ม animation ที่นี่ */
    /* transform-origin: left; */
    /* กำหนดจุดเริ่มต้นของการซูมเป็นด้านขวา */
  }

  .wel-g2-bg-Frame-green {
    background-image: url('<?php echo base_url("docs/Frame-green.png"); ?>');
    position: absolute;
    width: 1920px;
    height: 1000px;
    top: 0;
    z-index: 2;
    overflow: hidden;
    /* กำหนดจุดเริ่มต้นของการซูมเป็นด้านขวา */
    animation: fadeInWel 5s ease-out;
  }


  @keyframes zoomInAndMoveToLeftBottom {
    0% {
      transform: scale(1.5);
    }

    100% {
      transform: scale(1);
    }
  }



  .wel-g2-bg1 {
    background-image: url('<?php echo base_url("docs/b.bg-welcome-g2.png"); ?>');
    position: absolute;
    width: 1920px;
    height: 701px;
    top: 135px;
    /* ต่อจาก wel-g2-bg */
    z-index: 2;
    /* ไม่จำเป็นต้องซ้อนทับ */
    overflow: hidden;
    /* background-color: #000; */
  }

  .wel-g2-visit {
    animation: fadeInWel 5s ease-out;
    opacity: 0;
    animation-fill-mode: forwards;
    animation-delay: 1s;
    position: absolute;
    z-index: 2;
    left: 860px;
    top: 415px;
  }

  .fadeInWel {
    animation: fadeInWel 5s ease-out forwards;
  }

  @keyframes fadeInWel {
    0% {
      opacity: 0;
      /* transform: translateY(20px); */
      /* เริ่มจากตำแหน่งที่ต่ำกว่าเล็กน้อย */
    }

    100% {
      opacity: 1;
      /* transform: translateY(0); */
      /* ตำแหน่งปกติ */
    }
  }

  .font-wel-g2-visit-head {
    background-image: url('<?php echo base_url("docs/wel-g2-box.png"); ?>');
    background-repeat: no-repeat;
    color: #724118;
    text-align: center;
    font-size: 45px;
    font-style: normal;
    font-weight: 600;
    line-height: normal;
    width: 510px;
    height: 90px;
    padding-top: 10px;
    animation: fadeInWel 5s ease-out forwards;
    /* animation-delay: 13s; */
    opacity: 0;

  }

  .font-wel-g2-visit-content {
    color: #4A0D49;
    text-align: center;
    text-shadow: 1px 1px 0 #fff,
      -1px -1px 0 #fff,
      1px -1px 0 #fff,
      -1px 1px 0 #fff,
      0 1px 0 #fff,
      1px 0 0 #fff,
      0 -1px 0 #fff,
      -1px 0 0 #fff,
      0px 4px 4px rgba(0, 0, 0, 0.25);
    font-size: 18px;
    font-style: normal;
    font-weight: 500;
    line-height: normal;
  }

  .font-wel-g2-visit-content2 {
    color: #4A0D49;
    text-align: center;
    font-family: "Noto Music", "Noto Sans Thai", Arial, sans-serif;
    font-size: 24px;
    font-style: normal;
    font-weight: 400;
    line-height: normal;
  }

  .wel-g2-animation-3 {
    position: absolute;
    animation: moveLeftRight 5s infinite alternate;
  }

  @keyframes moveLeftRight {
    0% {
      left: -100px;
      /* จุดเริ่มต้นที่ด้านซ้ายสุด */
    }

    100% {
      left: 0;
      /* จุดสิ้นสุดที่ด้านขวาสุด ปรับค่า 100px ตามต้องการเพื่อระยะห่าง */
    }
  }

  .sriracha-regular {
    font-family: "Sriracha", cursive;
    font-weight: 400;
    font-style: normal;
  }

  /* .cloud-header1 { */
  /* z-index: 1; */
  /* margin-top: -390px; */
  /* position: relative; */
  /* margin-left: -20px; */
  /* animation: floatAndFade1 15s linear infinite; */
  /* background-color: #000; */
  /* position: relative; */
  /* } */

  .cloud-header2 {
    z-index: 3;
    margin-top: 180px;
    position: relative;
    margin-left: 80px;
    animation: floatAndFade2 25s linear infinite;
  }

  @keyframes floatAndFade2 {
    0% {
      opacity: 1;
      transform: translateX(0);
    }

    90% {
      opacity: 0;
      transform: translateX(600px);
      /* ปรับระยะทางได้ตามต้องการ */
    }

    100% {
      opacity: 0;
      transform: translateX(600px);
      /* ปรับระยะทางได้ตามต้องการ */
    }
  }

  .font-wel-g2-visit-content span,
  .font-wel-g2-visit-content2 span {
    opacity: 0;
    animation: fadeIn 0.5s forwards;
  }

  @keyframes fadeIn {
    to {
      opacity: 1;
    }
  }

  .cloud-header1 {
    opacity: 0;
    position: relative;
    animation: none;
    margin-top: -390px;
    position: relative;
    /* margin-left: -20px; */
    animation: floatAndFade1 30s linear infinite;
  }

  @keyframes floatAndFade1 {
    0% {
      opacity: 1;
      left: -400px;
    }

    90% {
      opacity: 0;
    }

    100% {
      opacity: 0;
      left: 600px;
      /* ปรับระยะทางได้ตามต้องการ */
    }
  }

  .font-heder-service {
    color: #FFF;
    text-align: center;
    font-family: "Noto Looped Thai UI";
    font-size: 22px;
    font-style: normal;
    font-weight: 500;
    line-height: normal;
  }

  .text-btm {
    padding-top: 210px;
    margin-left: -1100px;
    z-index: 2;
  }

  /* .text-btm .text-center span {
    display: inline-block;
    opacity: 0;
    transform: translateX(-10px);
    Adjust the movement
    animation: fadeInLeft 0.5s forwards;
  } */

  @keyframes fadeInLeft {
    0% {
      opacity: 0;
      transform: translateX(-10px);
      /* Start position */
    }

    100% {
      opacity: 1;
      transform: translateX(0);
      /* End position */
    }
  }

  .text-btm2 {
    position: relative;
    padding-top: 100px;
    /* padding-left: 1150px; */
    z-index: 2;
  }

  /* .text-btm2 .text-center span {
    display: inline-block;
    opacity: 0;
    transform: translateX(-20px);
    animation: fadeInLeft 0.5s forwards;
    letter-spacing: 2px; */
  /* เพิ่มระยะห่างระหว่างตัวอักษร */
  /* line-height: 1.5px; */
  /* เพิ่มระยะห่างระหว่างบรรทัด */
  /* } */

  @keyframes fadeInLeft {
    0% {
      opacity: 0;
      transform: translateX(-20px);
    }

    100% {
      opacity: 1;
      transform: translateX(0);
    }
  }

  .bg-light-header {
    /* background-color: #000; */
    z-index: 2;
    position: absolute;
    margin-left: 170px;
    overflow: hidden;
    /* ป้องกันภาพหลุดออกจากขอบ */
  }

  .wel-g2-light {
    position: relative;
    animation: moveRight 15s infinite ease-in-out;
    /* ใช้ easing function เพื่อให้การเคลื่อนไหวเนียนขึ้น */
  }

  @keyframes moveRight {
    0% {
      left: 0;
    }

    50% {
      left: 100px;
      /* ปรับค่าตามที่ต้องการ */
    }

    100% {
      left: 0;
    }
  }


  .wel-g2-animation-3 {
    position: absolute;
    /* หรือ relative ขึ้นอยู่กับตำแหน่งที่ต้องการ */
    animation: moveLeftRight 5s infinite alternate;
  }

  /* Keyframes สำหรับการขยับซ้ายและขวา */
  @keyframes moveLeftRight {
    0% {
      left: -100px;
      /* จุดเริ่มต้นที่ด้านซ้ายสุด */
    }

    100% {
      left: 0;
      /* จุดสิ้นสุดที่ด้านขวาสุด ปรับค่า 100px ตามต้องการเพื่อระยะห่าง */
    }
  }

  .bg-cloud-header {
    position: relative;
    width: 100%;
    height: 200px;
    margin-top: 20px;
    /* ตั้งค่าความสูงของ header ตามที่ต้องการ */
    overflow: hidden;
    /* ป้องกันเมฆไม่ให้หลุดออกจากขอบ */
    /* background: #87CEEB; */
    /* ใส่สีพื้นหลังท้องฟ้า */
  }

  .wel-g2-cloud {
    position: absolute;
    bottom: 20px;
    /* ตั้งค่าความสูงของเมฆจากด้านล่าง */
    left: -200px;
    /* ตั้งค่าเริ่มต้นที่ตำแหน่งซ้ายสุด */
    animation: moveCloud 80s linear infinite;
    /* ตั้งค่าเวลาและรูปแบบการเคลื่อนไหว */
  }

  @keyframes moveCloud {
    0% {
      left: -200px;
      /* จุดเริ่มต้นของเมฆ */
    }

    100% {
      left: 100%;
      /* จุดสิ้นสุดของเมฆ */
    }
  }

  .bg-water-header {
    position: absolute;
    overflow: hidden;
    width: 1920px;
  }

  .wel-g2-water {
    margin-top: 180px;
    margin-left: 150px;
    position: relative;
    opacity: 1;
    animation: waterfade 10s ease-in-out infinite;
    /* animation-delay: 0s; */
    /* background-color: #000; */
  }

  @keyframes waterfade {
    0% {
      transform: scale(1);
      opacity: 1;
    }

    50% {
      transform: scale(1.2);
      opacity: 1;
    }

    100% {
      transform: scale(1);
      opacity: 1;
    }
  }

  .wel-nav-sky {
    background-image: url('<?php echo base_url("docs/bg-nav-sky.png"); ?>');
    position: absolute;
    background-repeat: no-repeat;
    background-size: 100% 700px;
    /* ขนาดเต็ม 1920px x 600px */
    width: 1920px;
    height: 700px;
    /* แสดงความสูงที่คุณต้องการ */
    overflow: hidden;
    position: absolute;
    background-position: top;
    /* เริ่มจากด้านบน */
  }

  .wel-nav-home {
    background-image: url('<?php echo base_url("docs/welcome-other.png"); ?>');
    position: absolute;
    z-index: 2;
    width: 1920px;
    height: 700px;
    background-position: top;
    background-repeat: no-repeat;
    /* animation: zoomOutsky 40s forwards; */
  }

  .wel-nav-home2 {
    background-image: url('<?php echo base_url("docs/sun-Header Line.png"); ?>');
    position: absolute;
    z-index: 3;
    width: 1920px;
    height: 700px;
    background-position: top;
    background-repeat: no-repeat;
    animation: zoomInAndMoveToLeftBottom 5s ease-in-out;
    transform-origin: left;
  }

  @keyframes expandBackground {
    0% {
      background-size: 100% 100%;
      /* ขนาดปกติ */
    }

    100% {
      background-size: 110% 110%;
      /* ขยายขึ้น 10% */
    }
  }


  .elephant-nav {
    position: absolute;
    z-index: 2;
    margin-top: 40px;
    opacity: 0;
    /* เริ่มต้นด้วยความโปร่งใส 0 */
    animation: fadeInnav 5s ease-in-out forwards;
    /* เพิ่ม animation สำหรับ fade in */
  }

  @keyframes fadeInnav {
    from {
      opacity: 0;
      /* เริ่มต้นด้วยความโปร่งใส 0 */
    }

    to {
      opacity: 1;
      /* ความโปร่งใสเต็มที่ */
    }
  }


  .wel-g2-animation-cloud-1 {
    position: absolute;
    z-index: 1;
    top: 20px;
    left: 30px;
    animation: wel-g2-anima-cloud 40s linear infinite;
  }

  .wel-g2-animation-cloud-2 {
    position: absolute;
    z-index: 1;
    top: 10px;
    left: 18%;
    animation: wel-g2-anima-cloud 35s linear infinite;
  }

  .wel-g2-animation-cloud-3 {
    position: absolute;
    z-index: 1;
    top: 10px;
    left: 55%;
    animation: wel-g2-anima-cloud 35s linear infinite;
  }

  .wel-g2-animation-cloud-4 {
    position: absolute;
    z-index: 1;
    top: 60px;
    left: 65%;
    animation: wel-g2-anima-cloud 35s linear infinite;
  }

  @keyframes wel-g2-anima-cloud {
    0% {
      opacity: 0;
      /* จุดเริ่มต้นของเมฆ */
    }

    25% {
      opacity: 1;
      /* จุดเริ่มต้นของเมฆ */
    }

    75% {
      opacity: 1;
      /* จุดเริ่มต้นของเมฆ */
    }

    100% {
      left: 100%;
      opacity: 0;
      /* จุดสิ้นสุดของเมฆ */
    }
  }

  .light-nav-haeder {
    /* background-color: #000; */
    margin-top: -100px;
    padding-left: 190px;
    /* z-index: 5; */
    position: absolute;
  }

  .border-odata {
    /* display: flex; */
    width: 1060px;
    padding: 16px;
    justify-content: space-between;
    align-items: center;
    border-radius: 16px;
    border: 1px solid #4A0D49;
    box-shadow: 1px 2px 4px 0px rgba(172, 219, 133, 0.25);
    margin-top: 10px;
  }

  .bg-btn-head-elderly-aw {
    background-image: url('<?php echo base_url("docs/head-elderly-aw.png"); ?>');
    background-repeat: no-repeat;
    width: 580px;
    height: 58px;
    position: relative;
    z-index: 4;
    font-size: 30px;
    color: #fff;
    /* padding-left: 45px; */
    text-align: center;
    padding-top: 5px;
    transition: background-image 0.6s ease;
  }

  .bg-btn-head-elderly-aw:hover {
    background-image: url('<?php echo base_url("docs/head-elderly-aw-active-hover.png"); ?>');
    background-repeat: no-repeat;
    width: 580px;
    height: 58px;
    position: relative;
    z-index: 4;
    transition: background-image 0.6s ease;
  }

  .bg-btn-head-elderly-aw-active {
    background-image: url('<?php echo base_url("docs/head-elderly-aw-active.png"); ?>');
    background-repeat: no-repeat;
    width: 580px;
    height: 58px;
    position: relative;
    z-index: 4;
    font-size: 30px;
    color: #fff;
    /* padding-left: 45px; */
    text-align: center;
    padding-top: 5px;
  }

  .font-elderly-aw-ods {
    color: #000;
    font-family: "Noto Looped Thai";
    font-size: 32px;
    font-style: normal;
    font-weight: 500;
    line-height: 30px;
  }

  .box-form-elderly-aw-ods-download {
    border-radius: 16px;
    background: #FFFFE1;
    padding: 24px;
    gap: 16px;
    margin-top: 30px;
    margin-bottom: 40px;
  }

  .font-form-elderly-aw-ods-download {
    color: #000;
    font-family: "Noto Looped Thai";
    font-size: 24px;
    font-style: normal;
    font-weight: 400;
    line-height: 33.421px;
    /* 139.254% */
  }

  .space-between {
    display: flex;
    justify-content: space-between;
    align-items: center;
  }

  .btn-download-el-aw {
    background-image: url('<?php echo base_url("docs/btn-download-el-aw.png"); ?>');
    background-repeat: no-repeat;
    width: 171px;
    height: 50px;
    color: #FFF;
    font-feature-settings: 'clig' off, 'liga' off;
    font-family: "Noto Looped Thai";
    font-size: 20px;
    font-style: normal;
    font-weight: 500;
    padding: 10px 20px;
  }

  .btn-download-el-aw:hover {
    background-image: url('<?php echo base_url("docs/btn-download-el-aw-hover.png"); ?>');
    background-repeat: no-repeat;
    width: 171px;
    height: 50px;
  }

  .font-note-elderly-aw {
    color: #FF4343;
    leading-trim: both;
    text-edge: cap;
    font-feature-settings: 'clig' off, 'liga' off;
    font-family: "Noto Looped Thai UI";
    font-size: 24px;
    font-style: normal;
    font-weight: 400;
    line-height: 32px;
    /* 133.333% */
  }

  .wel-g3-bg {
    background-image: url('<?php echo base_url("docs/b.bg-welcome-g3.png"); ?>');
    position: absolute;
    width: 1920px;
    height: 1000px;
    top: 0;
    /* ตำแหน่งบนสุดของหน้า */
    z-index: 1;
    /* ไม่จำเป็นต้องซ้อนทับ */
    overflow: hidden;
  }

  .local-sun {
    margin-top: -419px;
    margin-left: 1383px;
    z-index: 2;
    position: absolute;
  }

  .rotate-animation360 {
    animation: rotate-animation360 25s infinite linear;
  }

  @keyframes rotate-animation360 {
    0% {
      transform: rotate(0deg);
    }

    50% {
      transform: rotate(180deg);
    }

    100% {
      transform: rotate(360deg);
    }
  }

  .wel-light-nav2 {
    animation: floatUp 10s ease-in-out forwards, rotateAfterFloat 25s 10s infinite linear;
    /* แยก animation ลอยขึ้นและหมุน */
    position: absolute;
    z-index: 1;
    margin-top: -600px;
    /* เริ่มจากด้านล่าง */
    margin-left: 190px;
  }

  @keyframes floatUp {
    0% {
      transform: translateY(200px);
      /* เริ่มต้นจากด้านล่าง */
      opacity: 1;
      /* เริ่มต้นด้วยความโปร่งใสเต็มที่ */
    }

    100% {
      transform: translateY(0);
      /* ลอยขึ้นไปจุดสูงสุด */
      opacity: 1;
      /* ความโปร่งใสยังคงเต็มที่ */
    }
  }

  .local-sun-left {
    margin-top: 20px;
    padding-left: 430px;
    z-index: 1;
    position: absolute;
    animation: fadeInsun 5s ease-in-out forwards;
  }

  @keyframes fadeInsun {
    from {
      opacity: 0;
    }

    to {
      opacity: 1;
    }
  }


  .local-prakru {
    animation: fadeInAndGrowFromBottomRight 5s ease-in-out forwards;
    padding-left: 330px;
    margin-top: -330px;
    z-index: 5;
    position: absolute;
    transform-origin: bottom right;
    /* กำหนดจุดเริ่มต้นของการขยายเป็นด้านล่างขวา */
  }

  @keyframes fadeInAndGrowFromBottomRight {
    0% {
      transform: scale(0.5);
      opacity: 0;
    }

    100% {
      transform: scale(1);
      opacity: 1;
    }
  }


  .line-separator {
    width: 442px;
    height: 1px;
    flex-shrink: 0;
    /* ความหนาของเส้น */
    stroke-width: 2px;
    stroke: #4A0D49;
    /* สีของเส้น */
    /* ระยะห่างระหว่างเส้นกับข้อความและการจัดกึ่งกลาง */
  }

  .light-container {
    position: relative;
    /* เพื่อให้ลูกเล่น animation อยู่ในขอบเขตของ container นี้ */
  }

  .moving-light {
    position: absolute;
    left: 0;
    opacity: 0;
    /* เริ่มต้นด้วยความโปร่งใส */
    animation: moveLight 10s linear infinite, fadeIn 8s forwards;
    /* การกำหนด animation ทั้งการเคลื่อนไหวและการค่อยๆสว่างขึ้น */
    animation-delay: 8s;
    /* เริ่ม animation หลังจาก 8 วินาที */
  }

  @keyframes moveLight {
    0% {
      left: -50px;
    }

    100% {
      left: 500px;
    }
  }

  @keyframes fadeIn {
    0% {
      opacity: 0;
    }

    100% {
      opacity: 1;
    }
  }


  .wel-g1-visit {
    animation: fadeInWel 5s ease-out;
    opacity: 0;
    animation-fill-mode: forwards;
    animation-delay: 1s;
    position: absolute;
    z-index: 2;
    left: 1211px;
    top: 369px;
  }

  @keyframes fadeInWel {
    0% {
      opacity: 0;
      /* transform: translateY(20px); */
      /* เริ่มจากตำแหน่งที่ต่ำกว่าเล็กน้อย */
    }

    100% {
      opacity: 1;
      /* transform: translateY(0); */
      /* ตำแหน่งปกติ */
    }
  }




  .dot-g3-light-animation-1 {
    position: absolute;
    padding-top: 200px;
    padding-left: 50px;
  }

  .container-wel-g3-animation {
    position: absolute;
    width: 1920px;
    height: 500px;
    overflow: hidden;
    z-index: 3 !important;
  }

  @keyframes fall {
    0% {
      top: -300px;
      opacity: 1;
      /* เริ่มต้นอยู่นอกจอ */
    }

    100% {
      top: 500px;
      opacity: 0;
      /* ตกลงมาเต็มความสูงของ container */
    }
  }

  @keyframes blink-wel-g3 {
    0% {
      opacity: 0;
    }

    10% {
      opacity: 1;
    }

    50% {
      opacity: 0;
    }

    60% {
      opacity: 1;
    }

    80% {
      opacity: 0;
    }

    100% {
      opacity: 0;
    }
  }

  .container-wel-g3-animation img {
    animation: fall 10s linear infinite;
  }

  .wel-navbar {
    position: fixed;
    top: 46%;
    left: 0;
    transform: translateY(-50%);
    padding: 10px;
    border-radius: 0 10px 10px 0;
    transition: left 0.5s ease-in-out, opacity 0.5s ease-in-out;
    opacity: 1;
    /* เริ่มต้นด้วยการแสดง */
    background-image: url('<?php echo base_url("docs/menu-bar.png"); ?>');
    width: 108px;
    height: 577px;
    z-index: 9999;
    /* เพิ่ม z-index เพื่อให้มั่นใจว่าอยู่ข้างบนสุด */
  }

  .wel-navbar.hide {
    left: -235px;
  }

  .text-wel-menubar {
    position: absolute;
    /* ใช้ fixed เพื่อให้ไม่ขยับ */
    height: 100%;
    display: flex;
    align-items: center;
    justify-content: center;
    transform: rotate(90deg);
    /* หมุนข้อความ 90 องศา */
    transform-origin: left center;
    /* ตำแหน่งจุดหมุน */
    margin-left: 30px;
    margin-top: -240px;
  }

  .font-text-menubar-wel {
    color: #FFF;
    text-align: center;
    /* font-family: "Noto Looped Thai UI"; */
    font-size: 32px;
    font-style: normal;
    font-weight: 600;
    line-height: normal;
    white-space: nowrap;
    /* ป้องกันไม่ให้ข้อความแตกบรรทัด */
  }

  .hide-button {
    position: fixed;
    top: 77%;
    left: 0;
    transform: translateY(-50%);
    padding: 0;
    width: 28px;
    /* กำหนดขนาดของปุ่ม */
    height: 28px;
    cursor: pointer;
    border: none;
    background: none;
    /* เอาพื้นหลังออก */
    background-image: url('<?php echo base_url("docs/hidenavbar1.png"); ?>');
    /* เปลี่ยนเป็น URL รูปภาพของคุณ */
    background-size: cover;
    background-position: center;
    z-index: 10000;
    transition: background-image 0.6s ease;
    margin-left: 15px;
  }

  .hide-button:hover {
    background-image: url('<?php echo base_url("docs/hidenavbar1_over.png"); ?>');
  }

  .show-button {
    position: fixed;
    top: 77%;
    left: 0;
    transform: translateY(-50%);
    padding: 0;
    width: 28px;
    /* กำหนดขนาดของปุ่ม */
    height: 28px;
    cursor: pointer;
    border: none;
    background: none;
    /* เอาพื้นหลังออก */
    background-image: url('<?php echo base_url("docs/shownavbar1.png"); ?>');
    /* เปลี่ยนเป็น URL รูปภาพของคุณ */
    background-size: cover;
    background-position: center;
    z-index: 10000;
    transition: background-image 0.6s ease;
    margin-left: 15px;
  }

  .show-button:hover {
    background-image: url('<?php echo base_url("docs/shownavbar1_over.png"); ?>');
  }

  .wel-navbar-list {
    margin-top: 100px;
    margin-left: 15px;
    display: flex;
    flex-direction: column;
    align-items: center;
    position: absolute;

  }

  .navbar-item {
    display: flex;
    flex-direction: column;
    align-items: center;
    margin: 4px 0;
    transition: transform 0.5s ease, font-size 0.5s ease;
  }

  .navbar-item:hover {
    transform: scale(1.10);
    /* ปรับขนาดการขยาย */
  }

  .navbar-item img {
    transition: transform 0.5s ease;
  }

  .navbar-item:hover img {
    transform: scale(1.10);
    /* ปรับขนาดการขยาย */
  }

  .navbar-item:hover .font-text-icon-wel {
    color: #FFB20B;
    /* เปลี่ยนสีของข้อความเมื่อเมาส์ชี้ที่รูป */
  }

  .font-text-icon-wel {
    color: #fff;
    text-align: center;
    font-family: "Noto Looped Thai";
    font-size: 12px;
    font-style: normal;
    font-weight: 700;
    line-height: normal;
    transition: transform 0.3s ease, font-size 0.3s ease;
    padding-top: 5px;
  }

  @keyframes fadeIn {
    from {
      opacity: 0;
    }

    to {
      opacity: 1;
    }
  }

  @keyframes slideIn {
    from {
      transform: translateY(-50px);
      opacity: 0;
    }

    to {
      transform: translateY(0);
      opacity: 1;
    }
  }

  @keyframes shake {

    0%,
    100% {
      transform: translateX(0);
    }

    10%,
    30%,
    50%,
    70%,
    90% {
      transform: translateX(-10px);
    }

    20%,
    40%,
    60%,
    80% {
      transform: translateX(10px);
    }
  }

  .tab-content-dla {
    display: none;
  }

  .calender-detail-head {
    border-radius: 20px;
    border: 1px solid #4A0D49;
    background: #FFF;
    width: 268px;
    height: 43px;
    display: flex;
    justify-content: space-between;
    align-items: center;
    margin-top: 10px;
    padding-left: 35px;
  }


  /* สไตล์สำหรับ overlay */
  .calendar-overlay {
    position: fixed;
    top: 0;
    left: 0;
    width: 100%;
    height: 100%;
    z-index: 9998 !important;
    /* สูงขึ้นแต่ต่ำกว่า popup */
  }

  /* สไตล์สำหรับกล่อง popup */
  /* เพิ่ม z-index ให้สูงขึ้นมาก ๆ */
  .calendar-popup-container {
    position: fixed;
    top: 50%;
    left: 50%;
    transform: translate(-50%, -50%);
    z-index: 9999 !important;
    /* เพิ่ม z-index ให้สูงมาก */
    background-color: white;
    border-radius: 20px;
    border: 1px solid #4A0D49;
    width: 800px;
    max-width: 90%;
    overflow: auto;
    max-height: 80vh;
    /* จำกัดความสูงไม่ให้เกินขอบจอ */
  }

  /* ซ่อน popup และ overlay เมื่อโหลดหน้า */
  .calendar-popup-container,
  .calendar-overlay {
    display: none;
  }

  /* สไตล์สำหรับหัวข้อใน popup */
  .popup-header {
    /* background: linear-gradient(to right, #d8e7ff, #f0f5ff); */
    background: #FFF;

    padding: 15px 20px;
    border-bottom: 1px solid #4A0D49;
    position: relative;
  }

  .popup-title {
    color: #404040;
    font-size: 22px;
    font-weight: 600;
    text-align: center;
    margin: 0;
  }

  /* สไตล์สำหรับปุ่มปิด */
  .popup-close {
    position: absolute;
    top: 10px;
    right: 10px;
    font-size: 24px;
    z-index: 10000;
    cursor: pointer;
    background: rgba(255, 255, 255, 0.8);
    border-radius: 50%;
    width: 30px;
    height: 30px;
    display: flex;
    align-items: center;
    justify-content: center;
    box-shadow: 0 2px 5px rgba(0, 0, 0, 0.2);
  }

  .popup-close:hover {
    background-color: rgba(0, 0, 0, 0.05);
  }

  /* สไตล์สำหรับเนื้อหาใน popup */
  .popup-content {
    max-height: 60vh;
    overflow-y: auto;
    padding: 15px;
  }

  /* animation */
  @keyframes popup-appear {
    from {
      opacity: 0;
      transform: translate(-50%, -60%);
    }

    to {
      opacity: 1;
      transform: translate(-50%, -50%);
    }
  }

  .calender-detail-content {
    border-radius: 20px;
    border: 1px solid #4A0D49;
    background: #FFF;
    width: 268px;
    height: 184px;
    padding-top: 10px;
    padding-left: 0px;
  }

  #qCalender {
    max-height: 180px;
    overflow-y: auto;
    padding: 10px;
    /* เพิ่มช่องว่างภายในเพื่อให้เนื้อหาดูไม่แน่นจนเกินไป */
  }

  /* แต่ง scrollbar */
  #qCalender::-webkit-scrollbar {
    width: 12px;
    /* ความกว้างของ scrollbar */
  }

  #qCalender::-webkit-scrollbar-track {
    background: #f0f0f0;
    /* สีพื้นหลังของ track */
    border-radius: 10px;
  }

  #qCalender::-webkit-scrollbar-thumb {
    background-color: #888;
    /* สีของ thumb */
    border-radius: 10px;
    border: 3px solid #f0f0f0;
    /* สีพื้นหลังรอบ thumb */
  }

  #qCalender::-webkit-scrollbar-thumb:hover {
    background-color: #555;
    /* สีของ thumb เมื่อ hover */
  }

  .font-calender2.detail-text {
    display: inline-block;
    /* ทำให้ข้อความและจุดอยู่ในบรรทัดเดียวกัน */
  }

  .services-container {
    display: flex;
    flex-direction: row;
    justify-content: flex-start;
    /* gap: 20px; */
    /* เพิ่ม gap เพื่อจัดระยะห่างระหว่างแต่ละลิงก์ */
  }

  .service-link {
    display: flex;
    flex-direction: column;
    align-items: center;
    text-decoration: none;
    /* ลบขีดเส้นใต้ของลิงก์ */
  }

  .button-box1,
  .button-box2,
  .button-box3,
  .button-box4,
  .button-box5,
  .button-box6 {
    width: 168px;
    height: 168px;
    display: flex;
    justify-content: center;
    align-items: center;
    transition: background-image 0.6s ease;
  }

  .button-box1 {
    background-image: url('<?php echo base_url("docs/banner-button1.png"); ?>');
    background-repeat: no-repeat;
  }

  .button-box1:hover {
    background-image: url('<?php echo base_url("docs/banner-button1-hover.png"); ?>');
  }

  .button-box2 {
    background-image: url('<?php echo base_url("docs/banner-button2.png"); ?>');
    background-repeat: no-repeat;
  }

  .button-box2:hover {
    background-image: url('<?php echo base_url("docs/banner-button2-hover.png"); ?>');
  }

  .button-box3 {
    background-image: url('<?php echo base_url("docs/banner-button3.png"); ?>');
    background-repeat: no-repeat;
  }

  .button-box3:hover {
    background-image: url('<?php echo base_url("docs/banner-button3-hover.png"); ?>');
  }

  .button-box4 {
    background-image: url('<?php echo base_url("docs/banner-button4.png"); ?>');
    background-repeat: no-repeat;
  }

  .button-box4:hover {
    background-image: url('<?php echo base_url("docs/banner-button4-hover.png"); ?>');
  }

  .button-box5 {
    background-image: url('<?php echo base_url("docs/banner-button5.png"); ?>');
    background-repeat: no-repeat;
  }

  .button-box5:hover {
    background-image: url('<?php echo base_url("docs/banner-button5-hover.png"); ?>');
  }

  .button-box6 {
    background-image: url('<?php echo base_url("docs/banner-button6.png"); ?>');
    background-repeat: no-repeat;
  }

  .button-box6:hover {
    background-image: url('<?php echo base_url("docs/banner-button6-hover.png"); ?>');
  }

  .font-banner-button {
    font-size: 19px;
    color: inherit;
  }

  .text-run-btm-eservice {
    width: 1038px;
    height: 49px;
    margin: 0 auto;
    border-radius: 50px;
    background: #FFF;
    padding-top: 10px;
    margin-top: 10px;
  }

  .font-like-see {
    color: #FFF;
    /* font-family: "Noto Looped Thai"; */
    font-size: 20px;
    font-style: normal;
    font-weight: 700;
    line-height: normal;
    line-height: 1;
    /* ลด line-height เพื่อขยับข้อความขึ้น */
    padding-top: 5px;
    /* เพิ่ม padding-top เพื่อขยับข้อความขึ้น */
    padding-left: 5px;
  }

  .nav-container {
    display: flex;
    flex-direction: column;
    align-items: flex-end;
    margin-right: 180px;
    padding-top: 270px;
    position: relative;
  }

  .nav-button1,
  .nav-button7 {
    background-image: url('<?php echo base_url("docs/bt-header-1-7.png"); ?>');
    width: 260px;
    height: 50px;
    padding-top: 10px;
    transition: background-image 0.6s ease, box-shadow 0.6s ease;
    cursor: pointer;
    padding-left: 40px;
    box-shadow: 0 4px 6px gray;
    border-radius: 25px 0px 0px 25px;
  }

  .nav-button1:hover,
  .nav-button7:hover,
  .nav-button1.active,
  .nav-button7.active {
    background-image: url('<?php echo base_url("docs/bt-header-1-7-hover.png"); ?>');
    box-shadow: 0 6px 8px gray;
    /* ใส่เงาที่เพิ่มขึ้นเมื่อ hover หรือ active */
  }


  .nav-button2,
  .nav-button8 {
    background-image: url('<?php echo base_url("docs/bt-header-2-8.png"); ?>');
    width: 260px;
    height: 50px;
    padding-top: 10px;
    transition: background-image 0.6s ease;
    cursor: pointer;
    padding-left: 40px;
    box-shadow: 0 4px 6px gray;
    border-radius: 25px 0px 0px 25px;
  }

  .nav-button2:hover,
  .nav-button8:hover,
  .nav-button2.active,
  .nav-button8.active {
    background-image: url('<?php echo base_url("docs/bt-header-2-8-hover.png"); ?>');
    box-shadow: 0 6px 8px gray;
  }

  .nav-button3,
  .nav-button9 {
    background-image: url('<?php echo base_url("docs/bt-header-3-9.png"); ?>');
    width: 260px;
    height: 50px;
    padding-top: 10px;
    transition: background-image 0.6s ease;
    cursor: pointer;
    padding-left: 40px;
    box-shadow: 0 4px 6px gray;
    border-radius: 25px 0px 0px 25px;
  }

  .nav-button3:hover,
  .nav-button9:hover,
  .nav-button3.active,
  .nav-button9.active {
    background-image: url('<?php echo base_url("docs/bt-header-3-9-hover.png"); ?>');
    box-shadow: 0 6px 8px gray;
  }

  .nav-button4 {
    background-image: url('<?php echo base_url("docs/bt-header4.png"); ?>');
    width: 260px;
    height: 50px;
    padding-top: 10px;
    transition: background-image 0.6s ease;
    cursor: pointer;
    padding-left: 40px;
    box-shadow: 0 4px 6px gray;
    border-radius: 25px 0px 0px 25px;
  }

  .nav-button4:hover,
  .nav-button4.active {
    background-image: url('<?php echo base_url("docs/bt-header4-hover.png"); ?>');
    box-shadow: 0 6px 8px gray;
  }

  .nav-button5 {
    background-image: url('<?php echo base_url("docs/bt-header5.png"); ?>');
    width: 260px;
    height: 50px;
    padding-top: 10px;
    transition: background-image 0.6s ease;
    cursor: pointer;
    padding-left: 40px;
    box-shadow: 0 4px 6px gray;
    border-radius: 25px 0px 0px 25px;
  }

  .nav-button5:hover,
  .nav-button5.active {
    background-image: url('<?php echo base_url("docs/bt-header5-hover.png"); ?>');
    box-shadow: 0 6px 8px gray;
  }

  .nav-button6 {
    background-image: url('<?php echo base_url("docs/bt-header6.png"); ?>');
    width: 260px;
    height: 50px;
    padding-top: 10px;
    transition: background-image 0.6s ease;
    cursor: pointer;
    padding-left: 40px;
    box-shadow: 0 4px 6px gray;
    border-radius: 25px 0px 0px 25px;
  }

  .nav-button6:hover,
  .nav-button6.active {
    background-image: url('<?php echo base_url("docs/bt-header6-hover.png"); ?>');
    box-shadow: 0 6px 8px gray;
  }

  .content-container {
    position: absolute;
    top: 270px;
    right: 460px;
    width: 1200px;
    height: 580px;
    padding: 20px;
    background-image: url('<?php echo base_url("docs/bg-nav-content2.png"); ?>');
    border-radius: 15px;
    display: none;
  }

  .content-box {
    display: none;
  }

  .content-box.active {
    display: block;
  }

  .font-nav-btn {
    color: #2C5F25;
    text-align: center;
    font-family: "Noto Looped Thai UI";
    font-size: 24px;
    font-style: normal;
    font-weight: 600;
    line-height: normal;
  }

  .dropdown-wrapper {
    display: flex;
    justify-content: space-between;
    padding-left: 20px;
    padding-top: 100px;
  }

  .dropdown-left,
  .dropdown-center,
  .dropdown-right {
    display: flex;
    flex-direction: column;
    flex: 1;
    margin-right: 10px;
  }

  .dropdown-left a,
  .dropdown-center a,
  .dropdown-right a {
    margin-bottom: 10px;
    text-decoration: none;
  }

  .font-nav-detail {
    color: #FFF;
    font-family: Inter;
    font-size: 19px;
    font-style: normal;
    font-weight: 650;
    line-height: normal;
  }

  .font-nav-detail:hover {
    color: #FDC70C;
  }

  .btn-queue {
    background-image: url('<?php echo base_url("docs/btn-queue.png"); ?>');
    width: 140px;
    height: 50px;
    position: relative;
    /* เพิ่ม position relative เพื่อใช้กับ position absolute ภายใน */
    transition: background-image 0.6s ease;
  }

  .btn-queue:hover {
    background-image: url('<?php echo base_url("docs/btn-queue-hover.png"); ?>');
    width: 140px;
    height: 50px;
    transition: background-image 0.6s ease;
  }

  .btn-queue .btn {
    width: 100%;
    height: 100%;
    background: transparent;
    /* ทำให้ปุ่มโปร่งใส */
    border: none;
    /* เอาเส้นขอบออก */
    position: absolute;
    /* ทำให้ปุ่มครอบคลุมทั้งพื้นที่ของ .btn-queue */
    top: 0;
    left: 0;
  }

  .font-label-e-service-queue {
    color: #FFF;
    font-size: 24px;
    font-weight: 500;
  }

  .queue-container {
    border: 1px solid #ddd;
    border-radius: 0px 0px 20px 20px;
    width: 1060px;
    margin: auto;
    height: auto;
  }

  .queue-container-in {
    background: #fff;
    padding: 20px;
    border-radius: 0px 0px 0px 0px;
    border: 1px solid #D9D9D9;
    height: 80px;
  }

  .queue-header {
    display: flex;
    padding: 30px;
    align-items: flex-start;
    gap: 8px;
    align-self: stretch;
    border-radius: 20px 20px 0px 0px;
    border: 1px solid #D9D9D9;
    background: #EBFFF0;
    margin-top: 50px;
    box-sizing: border-box;
    /* Ensure padding is included in element's width and height */
  }

  .queue-content {
    background: #fff;
    padding: 20px;
    border-radius: 0 0 20px 20px;
    /* overflow-y: auto; */
    /* เพิ่มการเลื่อนถ้ามีเนื้อหาเกิน */
  }

  .status-dot {
    position: relative;
    display: inline-block;
    padding-left: 20px;
  }

  .status-dot::after {
    content: "";
    position: absolute;
    top: 30px;
    left: 30px;
    width: 2px;
    height: 80px;
    background-color: #D9D9D9;
  }

  .status-container {
    position: relative;
  }

  .status-container::after {
    content: "";
    position: absolute;
    top: 50%;
    left: 0;
    width: 2px;
    height: 100%;
    background-color: #D9D9D9;
  }

  .status-container:first-child::after {
    display: none;
  }

  .status-container-last .status-dot::after {
    display: none;
    /* ซ่อนเส้นสำหรับรายการสุดท้าย */
  }

  .status-container-last::after {
    display: none;
    /* ซ่อนเส้นสำหรับรายการสุดท้าย */
  }

  .font-queue-content {
    color: #9C9C9C;
    /* font-family: "Noto Looped Thai UI"; */
    font-size: 26px;
    font-style: normal;
    font-weight: 400;
    line-height: normal;
  }

  .font-queue-content2 {
    color: #000;
    /* font-family: "Noto Looped Thai UI"; */
    font-size: 22px;
    font-style: normal;
    font-weight: 400;
    line-height: normal;
  }

  .status-date {
    margin-top: 4px;
    margin-left: 45px;
    /* Adjust this value to move the date further to the right */
  }


  .font-label-e-service-follow-queue {
    color: #000;
    font-size: 28px;
    font-weight: 500;
  }

  .font-queue-head {
    color: #432F17;
    /* font-family: "Noto Looped Thai UI"; */
    font-size: 28px;
    font-style: normal;
    font-weight: 500;
    line-height: normal;
  }

  /* ซ่อน reCAPTCHA */
  .grecaptcha-badge {
    visibility: hidden;
  }

  .search-container {
    width: 1200px;
    height: 477px;
    flex-shrink: 0;
    border-radius: 20px;
    border: 1px solid #E11B78;
    background: url(<path-to-image>) lightgray 50% / cover no-repeat, #FFF;
    box-shadow: 3px 5px 4px 0px #FFC9A9 inset;
    margin: auto;
    padding: 30px 80px;
    margin-bottom: 40px;
  }


  .font-head-egp-buy {
    color: #000;
    -webkit-text-stroke-width: 0.5;
    -webkit-text-stroke-color: #000;
    font-family: "Noto Looped Thai UI";
    font-size: 28px;
    font-style: normal;
    font-weight: 300;
    line-height: normal;
  }

  .font-label-egp-buy {
    color: #000;
    -webkit-text-stroke-width: 0.5;
    -webkit-text-stroke-color: #000;
    font-family: "Noto Looped Thai UI";
    font-size: 22px;
    font-style: normal;
    font-weight: 300;
    line-height: normal;
  }

  /* สไตล์ dropdown */
  .custom-select-egp {
    position: relative;
    display: inline-block;
  }

  .custom-select-egp::after {
    content: "\25BC";
    /* Unicode สำหรับลูกศรลง */
    position: absolute;
    top: 50%;
    right: 10px;
    transform: translateY(-50%);
    pointer-events: none;
    font-size: 20px;
    color: #444;
  }

  .btn-clear-date-egp {
    width: 300px;
    height: 44px;
    background-image: url('<?php echo base_url("docs/clear-data-egp.png"); ?>');
    /* รูปภาพปกติ */
    background-size: cover;
    background-repeat: no-repeat;
    background-position: center;
    color: #FFF;
    text-align: center;
    font-family: "Noto Looped Thai UI";
    font-size: 18px;
    font-style: normal;
    font-weight: 400;
    line-height: normal;
    border: none;
    cursor: pointer;
    transition: background-image 0.3s ease;
    /* เพื่อให้เปลี่ยนภาพด้วยเวลา */
  }

  .btn-clear-date-egp:hover {
    background-image: url('<?php echo base_url("docs/clear-data-egp-hover.png"); ?>');
    color: #FFF;
  }


  .btn-search-egp {
    width: 160px;
    height: 40px;
    flex-shrink: 0;
    border-radius: 50px;
    border: 1px solid var(--g1, #D9AA58);
    background: var(--Linear, linear-gradient(0deg, #28523A 0%, #4CA784 100%));
    color: #FFF;
    text-align: center;
    font-family: "Noto Looped Thai UI";
    font-size: 24px;
    font-style: normal;
    font-weight: 400;
    line-height: normal;
  }

  .btn-search-egp:hover {
    color: #FFF;
    border-radius: 50px;
    border: 1px solid var(--g1, #D9AA58);
    opacity: 0.8;
    background: var(--Linear, linear-gradient(0deg, #28523A 0%, #4CA784 100%));
    box-shadow: 0px 4px 4px 0px rgba(0, 0, 0, 0.25) inset;
  }

  .rp-button {
    background-image: url('<?php echo base_url("docs/bg.pm-center.png"); ?>');
    width: 334px;
    height: 55px;
    padding-top: 10px;
    transition: background-image 0.6s ease;
    margin: auto;
    text-align: center;
    padding-top: 15px;
  }

  .active-rp,
  .rp-button:hover {
    background-image: url('<?php echo base_url("docs/bg.pm-center-hover.png"); ?>');
    width: 334px;
    height: 55px;
    transition: background-image 0.6s ease;
    padding-top: 15px;
  }

  .pm-button {
    background-image: url('<?php echo base_url("docs/bg.pm-center.png"); ?>');
    width: 334px;
    height: 55px;
    padding-top: 10px;
    transition: background-image 0.6s ease;
    margin: auto;
    text-align: center;
    padding-top: 15px;
  }

  .active-pm,
  .pm-button:hover {
    background-image: url('<?php echo base_url("docs/bg.pm-center-hover.png"); ?>');
    width: 334px;
    height: 55px;
    transition: background-image 0.6s ease;
    padding-top: 15px;
  }

  .pm-button-L {
    background-image: url('<?php echo base_url("docs/bg.pm-L.png"); ?>');
    width: 360px;
    height: 55px;
    padding-top: 10px;
    transition: background-image 0.6s ease;
    margin: auto;
    text-align: center;
    padding-top: 15px;
  }

  .active-pm-L,
  .pm-button-L:hover {
    background-image: url('<?php echo base_url("docs/bg.pm-L-hover.png"); ?>');
    width: 360px;
    height: 55px;
    transition: background-image 0.6s ease;
    padding-top: 15px;
  }

  .pm-button-R {
    background-image: url('<?php echo base_url("docs/bg.pm-R.png"); ?>');
    width: 360px;
    height: 55px;
    padding-top: 10px;
    transition: background-image 0.6s ease;
    margin: auto;
    text-align: center;
    padding-top: 15px;
  }

  .active-pm-R,
  .pm-button-R:hover {
    background-image: url('<?php echo base_url("docs/bg.pm-R-hover.png"); ?>');
    width: 360px;
    height: 55px;
    transition: background-image 0.6s ease;
    padding-top: 15px;
  }

  .tab-container3 {
    display: flex;
    margin-left: 0px;
    z-index: 5 !important;
    position: relative;
  }

  .tab-container3 .tab-link-pm {
    margin: 0 0px;
    /* ปรับขนาดนี้เพื่อเพิ่มหรือลดช่องว่าง */
  }

  .tab-link-pm {
    cursor: pointer;
    padding: 0px 2px;
    /* border: 1px solid #ccc; */
    margin-left: -30px;
  }

  .tab-container4 {
    display: flex;
    margin-left: 0px;
    z-index: 5 !important;
    position: relative;
  }

  .tab-container4 .tab-link-rp {
    margin: 0 0px;
    /* ปรับขนาดนี้เพื่อเพิ่มหรือลดช่องว่าง */
  }

  .tab-link-rp {
    cursor: pointer;
    padding: 0px 2px;
    /* border: 1px solid #ccc; */
    margin-left: -30px;
  }


  .news-dla-prov3 {
    padding-top: 10px;
    z-index: 4;
    position: relative;
    width: 1400px;
    height: 511px;
    flex-shrink: 0;
    border-radius: 0px 0px 20px 20px;
    background: rgba(255, 252, 242, 0.80);
  }

  .dla-end-pm {
    width: 1380px;
    height: 1px;
    background: #228614;
    margin: auto;
    margin-top: 10px;
  }

  .font-new-pm {
    color: #000;
    font-family: "Noto Looped Thai UI";
    font-size: 24px;
    font-style: normal;
    font-weight: 400;
    line-height: normal;
  }

  .line-ellipsis-dla-prov2-pm {
    width: 1050px;
    /* ปรับขนาดตามต้องการ */
    overflow: hidden;
    text-overflow: ellipsis;
    display: -webkit-box;
    -webkit-line-clamp: 1;
    -webkit-box-orient: vertical;
  }

  .font-head-egp-buy {
    color: #000;
    -webkit-text-stroke-width: 0.5;
    -webkit-text-stroke-color: #000;
    font-family: "Noto Looped Thai UI";
    font-size: 28px;
    font-style: normal;
    font-weight: 300;
    line-height: normal;
  }

  .font-label-egp-buy {
    color: #000;
    -webkit-text-stroke-width: 0.5;
    -webkit-text-stroke-color: #000;
    font-family: "Noto Looped Thai UI";
    font-size: 22px;
    font-style: normal;
    font-weight: 300;
    line-height: normal;
  }

  /* สไตล์ dropdown */
  .custom-select-egp {
    position: relative;
    display: inline-block;
  }

  .custom-select-egp select {
    display: inline-block;
    width: 770px;
    height: 40px;
    padding: 5px 10px;
    font-size: 18px;
    line-height: 1.3;
    color: #444;
    background-color: #fff;
    border: 1px solid #e8e8e8ee;
    border-radius: 4px;
    appearance: none;
    /* ซ่อนลูกศร dropdown พื้นฐาน */
    -webkit-appearance: none;
    -moz-appearance: none;
  }

  .custom-select-egp::after {
    content: "\25BC";
    /* Unicode สำหรับลูกศรลง */
    position: absolute;
    top: 50%;
    right: 10px;
    transform: translateY(-50%);
    pointer-events: none;
    font-size: 20px;
    color: #444;
  }

  .button-menu-e-service,
  .button-menu-e-service2,
  .button-menu-e-service3,
  .button-menu-e-service4,
  .button-menu-e-service5,
  .button-menu-e-service6,
  .button-menu-e-service7,
  .button-menu-e-service8,
  .button-menu-e-service9,
  .button-menu-e-service10,
  .button-menu-e-service11,
  .button-menu-e-service12,
  .button-menu-e-service13 {
    z-index: 4;
    width: 336px;
    height: 65px;
    background-repeat: no-repeat;
    transition: background-image 0.6s ease;
  }

  .button-menu-e-service {
    background-image: url('<?php echo base_url("docs/bt-eservice-queue.png"); ?>');
  }

  .button-menu-e-service:hover {
    background-image: url('<?php echo base_url("docs/bt-eservice-queue-hover.png"); ?>');
  }

  .button-menu-e-service2 {
    background-image: url('<?php echo base_url("docs/bt-eservice-corruption.png"); ?>');
    transition: background-image 0.3s ease;
  }

  .button-menu-e-service2:hover {
    background-image: url('<?php echo base_url("docs/bt-eservice-corruption-hover.png"); ?>');
  }

  .button-menu-e-service3 {
    background-image: url('<?php echo base_url("docs/bt-eservice-odata.png"); ?>');
  }

  .button-menu-e-service3:hover {
    background-image: url('<?php echo base_url("docs/bt-eservice-odata-hover.png"); ?>');
  }

  .button-menu-e-service4 {
    background-image: url('<?php echo base_url("docs/bt-eservice-form-eservice.png"); ?>');
  }

  .button-menu-e-service4:hover {
    background-image: url('<?php echo base_url("docs/bt-eservice-form-eservice-hover.png"); ?>');
  }

  .button-menu-e-service5 {
    background-image: url('<?php echo base_url("docs/bt-eservice-complain.png"); ?>');
  }

  .button-menu-e-service5:hover {
    background-image: url('<?php echo base_url("docs/bt-eservice-complain-hover.png"); ?>');
  }

  .button-menu-e-service6 {
    background-image: url('<?php echo base_url("docs/bt-eservice-kid.png"); ?>');
  }

  .button-menu-e-service6:hover {
    background-image: url('<?php echo base_url("docs/bt-eservice-kid-hover.png"); ?>');
  }

  .button-menu-e-service7 {
    background-image: url('<?php echo base_url("docs/bt-eservice-ebook.png"); ?>');
  }

  .button-menu-e-service7:hover {
    background-image: url('<?php echo base_url("docs/bt-eservice-ebook-hover.png"); ?>');
  }

  .button-menu-e-service8 {
    background-image: url('<?php echo base_url("docs/bt-eservice-esv-ods.png"); ?>');
    transition: background-image 0.3s ease;
  }

  .button-menu-e-service8:hover {
    background-image: url('<?php echo base_url("docs/bt-eservice-esv-ods-hover.png"); ?>');
  }

  .button-menu-e-service9 {
    background-image: url('<?php echo base_url("docs/bt-eservice-manual-esv.png"); ?>');
  }

  .button-menu-e-service9:hover {
    background-image: url('<?php echo base_url("docs/bt-eservice-manual-esv-hover.png"); ?>');
  }

  .button-menu-e-service10 {
    background-image: url('<?php echo base_url("docs/bt-eservice-suggestions.png"); ?>');
  }

  .button-menu-e-service10:hover {
    background-image: url('<?php echo base_url("docs/bt-eservice-suggestions-hover.png"); ?>');
  }

  .button-menu-e-service11 {
    background-image: url('<?php echo base_url("docs/bt-eservice-elderly.png"); ?>');
  }

  .button-menu-e-service11:hover {
    background-image: url('<?php echo base_url("docs/bt-eservice-elderly-hover.png"); ?>');
  }

  .button-menu-e-service12 {
    background-image: url('<?php echo base_url("docs/bt-eservice-questions.png"); ?>');
  }

  .button-menu-e-service12:hover {
    background-image: url('<?php echo base_url("docs/bt-eservice-questions-hover.png"); ?>');
  }

  .button-menu-e-service13 {
    background-image: url('<?php echo base_url("docs/bt-eservice-q-a.png"); ?>');
  }

  .button-menu-e-service13:hover {
    background-image: url('<?php echo base_url("docs/bt-eservice-q-a-hover.png"); ?>');
  }

  .container-video {
    position: relative;
    z-index: 5;
    padding-left: 140px;
    padding-right: 140px;
  }

  .video-content {
    width: 320px;
    height: 182px;
  }

  .video-iframe {
    border-radius: 16px;
  }

  .video-row {
    padding: 1rem 0;
    flex-wrap: nowrap;
    gap: 50px;
    justify-content: center !important;
    /* เปลี่ยนจาก flex-start เป็น center */

  }

  .video-details {
    width: 320px;
    padding: 10px 0;
  }

  .video-title {
    font-weight: bold;
    margin: 8px 0 4px 0;
    /* แสดงเนื้อหาแค่ 2 บรรทัด */
    display: -webkit-box;
    -webkit-line-clamp: 2;
    -webkit-box-orient: vertical;
    overflow: hidden;
    text-overflow: ellipsis;
    line-height: 1.5;
    height: 48px;
    /* font-size * line-height * 2 lines = 16 * 1.5 * 2 */
    color: #404040;
    font-size: 18px;
    font-style: normal;
    font-weight: 600;
    line-height: 24.863px;
    /* 138.128% */
  }

  .crop-time-video {
    width: 160px;
    height: 100%;
    flex-shrink: 0;
    border-radius: 5px;
    background: #8C21B2;
    box-shadow: 4px 4px 4px 0px rgba(0, 0, 0, 0.10);
    padding: 5px 5px 5px 15px;
    color: #fff;
  }

  .video-date {
    font-size: 14px;
    color: #fff;
    margin: 0;
  }


  .video-grid {
    display: flex;
    flex-wrap: wrap;
    gap: 20px;
    /* ช่องว่างระหว่างการ์ดวิดีโอ */
  }

  .video-card {
    width: 350px;
    height: auto;
    box-sizing: border-box;
    background: #f8f8f8;
    border-radius: 15px;
    padding: 10px;
    text-align: center;
  }

  .video-card h3 {
    font-size: 18px;
    margin-bottom: 10px;
    max-width: 350px;
    word-wrap: break-word;
    /* ตัดคำเมื่อเกินความกว้างที่กำหนด */
    overflow-wrap: break-word;
    /* เพิ่มเติมเพื่อความแน่นอน */
    margin: 0 auto;
    /* กึ่งกลางการ์ด */
  }

  .video-card iframe {
    width: 325px;
    height: 193px;
    border-radius: 15px;
  }

  .header-manual {
    width: 279px;
    height: 54px;
    background-image: url('<?php echo base_url("docs/header-menuan.png"); ?>');
    color: #964518;
    text-align: center;
    font-size: 24px;
    font-style: normal;
    font-weight: 500;
    line-height: normal;
    /* ปรับให้ข้อความอยู่กึ่งกลางแนวตั้ง */
    margin: 0 auto;
    /* ทำให้อยู่กึ่งกลางแนวนอน */
    display: block;
    /* ทำให้ margin: auto ทำงานได้ */
  }

  .e-service-pad {
    margin-top: 25px;
    padding-top: 10px;
  }

  .detail-q-a2 {
    display: flex;
    width: 100%;
    height: auto;
    padding: 21.438px;
    flex-direction: column;
    align-items: flex-start;
    gap: 13.398px;
    flex-shrink: 0;
    border-radius: 22.777px;
    background: #fff;
    box-shadow: 0px 0px 6.699px 0px rgba(0, 0, 0, 0.25);
    position: relative;
    z-index: 10;
    margin: auto;
  }


  .card-q-a {
    width: auto;
    height: auto;
    background: #f5f5f5;
    padding: 25px 45px;
    margin-bottom: 25px;
    margin-top: 15px;
    border-radius: 25px 25px;
  }

  .fr-view {
    text-align: initial;
    /* all: initial !important; */
  }

  .card-body-qa {
    -ms-flex: 1 1 auto;
    flex: 1 1 auto;
    padding: 1.25rem;
  }

  .bg-e-magazine {
    background-image: url('<?php echo base_url("docs/bg-e-magazine.png"); ?>');
    height: 500px;
    width: 1920px;
    position: relative;
    overflow: hidden;
    margin: auto;
  }

  .bg-statistics {
    background-image: url('<?php echo base_url("docs/bg-statistics.png"); ?>');
    height: 300px;
    width: 1920px;
    position: relative;
    overflow: hidden;
  }

  .crop-statistics {
    width: 285.165px;
    height: 60px;
    flex-shrink: 0;
    border-radius: 50px;
    background: #FFF;
    color: #111A4E;
    text-align: center;
    font-size: 36px;
    font-style: normal;
    font-weight: 600;
    line-height: 60px;
    /* ให้ text อยู่กลางแนวตั้ง */
    margin: 31.34px auto 20.88px auto;
    /* auto ทำให้อยู่กลางแนวนอน */
  }

  .bg-statistics-2 {
    /* background-image: url('<?php echo base_url("docs/bg-statistics-2.png"); ?>'); */
    height: 164px;
    width: 1119px;
    margin: 0 auto;
    padding: 31.8px 52.85px;
  }

  .statistics-buttons-container {
    display: flex;
    gap: 35px;
    justify-content: center;
    align-items: center;
  }

  .btn-statistics1,
  .btn-statistics2,
  .btn-statistics3,
  .btn-statistics4,
  .btn-statistics5 {
    width: 234px;
    height: 100px;
    flex-shrink: 0;
    display: inline-block;
    padding-left: 66px;
  }

  .text-statis-number {
    color: #FFF;
    text-align: center;
    font-size: 40px;
    font-style: normal;
    font-weight: 700;
    line-height: normal;
  }

  .text-statis {
    color: #FFF;
    text-align: center;
    font-size: 20px;
    font-style: normal;
    font-weight: 300;
    line-height: 0.8;
  }

  .btn-statistics1 {
    background-image: url('<?php echo base_url("docs/bg-statistics-online.png"); ?>');
  }

  .btn-statistics2 {
    background-image: url('<?php echo base_url("docs/bg-statistics-today.png"); ?>');

  }

  .btn-statistics3 {
    background-image: url('<?php echo base_url("docs/bg-statistics-thisweek.png"); ?>');

  }

  .btn-statistics4 {
    background-image: url('<?php echo base_url("docs/bg-statistics-month.png"); ?>');
  }

  .btn-statistics5 {
    background-image: url('<?php echo base_url("docs/bg-statistics-all.png"); ?>');
  }

  .bg-link-dla {
    background-image: url('<?php echo base_url("docs/bg-link-dla.png"); ?>');
    background-repeat: no-repeat;
    background-size: 100%;
    height: 500px;
    width: 1920px;
    z-index: 1;
    position: relative;
  }

  .link-dla-container {
    display: flex;
    flex-direction: row;
    justify-content: flex-start;
    /* สามารถใช้ center หรือ space-between เพื่อการจัดเรียงที่ต้องการ */
    padding: 10px;
  }

  @keyframes moveRightInfinite {
    0% {
      transform: translateX(-50%);
    }

    100% {
      transform: translateX(0);
    }
  }

  .animation-move-container {
    position: absolute;
    z-index: 1;
    width: 1920px;
    overflow: hidden;
    white-space: nowrap;
    /* margin-top: 60px; */
  }

  .animation-move-content {
    display: flex;
    width: 200%;
    /* 200% เพื่อทำให้มีสองภาพเรียงต่อกัน */
    animation: moveRightInfinite 30s linear infinite;
  }

  .animation-move-content img {
    width: 50%;
    /* ปรับขนาดภาพให้พอดีกับครึ่งหนึ่งของคอนเทนเนอร์ */
    height: 100%;
    vertical-align: top;
  }

  .news-container {
    display: flex;
    flex-wrap: wrap;
    gap: 3.75%;
    /* เพิ่มระยะห่างระหว่างคอลัมน์ */
    position: relative;
    z-index: 1;
  }

  .news-col-left {
    flex: 0 0 30%;
    /* กำหนดคอลัมน์แรกเป็น 30% */

  }

  .news-col-right {
    flex: 0 0 66.25%;
    /* กำหนดคอลัมน์ที่สองเป็น 66% */
    margin-top: 68px;
  }

  .activity-container {
    display: flex;
    flex-wrap: wrap;
    gap: 5%;
    /* เพิ่มระยะห่างระหว่างคอลัมน์ */
  }

  .activity-col-left {
    flex: 0 0 55%;
    /* กำหนดคอลัมน์แรกเป็น 30% */
  }

  .activity-col-right {
    flex: 0 0 40%;
    /* กำหนดคอลัมน์ที่สองเป็น 66% */
  }

  .bg-facebook-new {
    background-image: url('<?php echo base_url("docs/bg-facebook.png"); ?>');
    width: 340px;
    height: 668px;
    position: absolute;
    z-index: 3;
    margin-top: 20px;
    margin-left: 104px;
    padding-top: 95px;
    padding-left: 25px;
  }

  .button-e-service-container {
    display: flex;
    flex-direction: row;
    justify-content: flex-center;
    /* สามารถใช้ center หรือ space-between เพื่อการจัดเรียงที่ต้องการ */
  }

  .button-link-dla-container {
    display: flex;
    flex-direction: row;
    justify-content: flex-center;
    /* สามารถใช้ center หรือ space-between เพื่อการจัดเรียงที่ต้องการ */
  }

  .dla-pad {
    padding-left: 40px;
    padding-top: 13px;
  }


  .button-link-dla {
    z-index: 1;
    /* margin-top: 80px; */
    background-image: url('<?php echo base_url("docs/button-link-dla.png"); ?>');
    background-repeat: no-repeat;
    width: 314px;
    height: 95px;
    transition: background-image 0.6s ease;
    box-shadow: -4px 7px 6px gray;
    border-radius: 45px;
    margin-right: 50px;
    color: #231F20;
  }

  .button-link-dla:hover {
    background-image: url('<?php echo base_url("docs/button-link-dla-hover.png"); ?>');
    background-repeat: no-repeat;
    transition: background-image 0.6s ease;
    color: #fff;
  }

  .font-link-dla {
    color: #000;
    text-shadow: 1px 1px 0 #fff,
      -1px -1px 0 #fff,
      1px -1px 0 #fff,
      -1px 1px 0 #fff,
      0 1px 0 #fff,
      1px 0 0 #fff,
      0 -1px 0 #fff,
      -1px 0 0 #fff,
      2px 3px 4px rgba(0, 0, 0, 0.25);
    font-size: 32px;
    font-style: normal;
    font-weight: 600;
    line-height: normal;
  }

  .link-dla-container {
    display: flex;
    flex-direction: row;
    justify-content: flex-start;
    /* สามารถใช้ center หรือ space-between เพื่อการจัดเรียงที่ต้องการ */
    padding: 10px;
  }

  .font-link-dla-detail {
    font-size: 24px;
    font-style: normal;
    font-weight: 600;
    line-height: normal;
  }

  .crop-like {
    width: 1060px;
    height: 123px;
    flex-shrink: 0;
    border-radius: 15px;
    opacity: 0.3;
    background: #FFF;
    justify-content: center;
    align-items: center;
    display: flex;
    margin: auto;
    margin-top: 83px;
  }

  .bg-ex-header {
    width: 100%;
    /* เปลี่ยนจาก fixed width เป็น 100% */
    height: 546px;
    background-color: #000;
  }

  .activity-showcase {
    width: 960px;
    height: 546px;
    position: relative;
  }

  .activity-slider {
    width: 960px;
    height: 546px;
  }

  .activity-slide {
    width: 960px;
    height: 546px;
    position: relative;
    overflow: hidden;
  }

  .activity-media {
    width: 960px;
    height: 546px;
  }

  .activity-media img {
    width: 100%;
    height: 100%;
    object-fit: cover;
    /* เพิ่ม object-position เพื่อให้รูปอยู่ตรงกลางพอดี */
    object-position: center;
    /* ถ้าต้องการให้รูปขยายเต็มพื้นที่โดยไม่สนใจ aspect ratio */
    /* object-fit: fill; */
  }

  .activity-caption {
    position: absolute;
    bottom: 0;
    width: 960px;
    height: 99px;
    background: rgba(255, 255, 255, 0.80);
    display: flex;
    justify-content: center;
    align-items: center;
  }

  .activity-heading {
    margin: auto;
    height: 100%;
    display: flex;
    justify-content: center;
    align-items: center;
  }

  .activity-link {
    color: #000;
    text-align: center;
    font-size: 24px;
    font-style: normal;
    font-weight: 600;
    line-height: normal;
    width: 624px;
    overflow: hidden;
    text-overflow: ellipsis;
    display: -webkit-box;
    -webkit-line-clamp: 2;
    -webkit-box-orient: vertical;
  }

  .slider-next,
  .slider-prev {
    color: #fff;
    background: rgba(0, 0, 0, 0.5);
    width: 44px;
    height: 44px;
    border-radius: 50%;
  }

  .slider-next:after,
  .slider-prev:after {
    font-size: 20px;
  }

  .slider-pagination-bullet {
    width: 10px;
    height: 10px;
    background: #fff;
    opacity: 0.5;
  }

  .slider-pagination-bullet-active {
    opacity: 1;
  }

  .brm-slider-container {
    position: relative;
    width: 1060px;
    height: 123px;
    margin: 83px auto 0;
    overflow: hidden;
    /* เพิ่ม overflow: hidden */
    display: flex;
    align-items: center;
    justify-content: center;
    /* เปลี่ยนเป็น center */
  }

  .brm-slider-background {
    position: absolute;
    top: 0;
    left: 0;
    width: 100%;
    height: 100%;
    background: #FFF;
    opacity: 0.3;
    border-radius: 15px;
    z-index: 1;
  }


  .menu-content a.active {
    background-color: #f0f0f0;
    font-weight: bold;
  }

  .content-header {
    padding: 15px 0;
    margin-bottom: 20px;
    font-size: 24px;
    color: #333;
    border-bottom: 2px solid #2196F3;
  }

  .content-wrapper {
    padding: 20px;
    background: #fff;
    border-radius: 4px;
    box-shadow: 0 1px 3px rgba(0, 0, 0, 0.1);
  }

  .active-submenu {
    background-color: #F8F9FA;
    /* สีพื้นหลังเมื่อ active */
    color: #4A0D49 !important;
    /* สีตัวอักษรเมื่อ active */
    font-weight: bold;
  }

  .active-submenu .font-nav {
    color: #4A0D49 !important;
    /* สีตัวอักษรของ span เมื่อ active */
  }

  /* ถ้าต้องการให้มีเส้นด้านข้างเมื่อ active */
  .active-submenu {
    border-left: 4px solid #4A0D49;
    padding-left: 10px;
  }

  /* ถ้าต้องการให้มี transition effect */
  .menu-content a {
    transition: all 0.3s ease;
  }

  /*  Google translate ********************* */
  .goog-logo-link {
    display: none !important;
  }

  .goog-te-gadget {
    color: transparent !important;
  }

  /* ปรับสไตล์ของ Dropdown */
  .goog-te-combo {
    background-color: #fff;
    border: 1px solid #ddd;
    border-radius: 4px;
    padding: 3px;
    font-size: 16px;
    color: #333;
  }

  .skiptranslate iframe {
    display: none !important;
  }

  #google_translate_element a {
    display: none !important;
  }

  /* ปรับแต่งคอนเทนเนอร์ที่รวมธงชาติและ dropdown */
  .translate-container {
    display: flex;
    align-items: center;
    gap: 5px;
  }

  .language-flag {
    width: 30px;
    height: auto;
    margin-top: -12px;
  }

  .skiptranslate select option:first-child {
    content: 'Thai';
  }

  /* ************************************  */
  .custom-language-switcher {
    display: flex;
    align-items: center;
    padding: 4px;
    backdrop-filter: blur(4px);
    gap: 4px;

    width: 100px;
    height: 50px;
    flex-shrink: 0;
    border-radius: 25px;
    opacity: 0.8;
    background: linear-gradient(90deg, #FFF 0%, #FFF 202.33%);
    box-shadow: 0px 4px 4px 0px rgba(0, 0, 0, 0.25) inset;
  }

  .flag-icon {
    width: 24px;
    height: 16px;
    object-fit: cover;
  }

  .lang-buttons {
    display: flex;
    gap: 2px;
  }

  .lang-btn {
    border: none;
    background: transparent;
    color: #404040;
    cursor: pointer;
    transition: all 0.3s ease;

    width: 50px;
    height: 50px;
    flex-shrink: 0;
    border-radius: 25px;

    text-align: center;
    font-family: "Noto Sans Thai", "Noto Sans Thai Looped", sans-serif;
    font-size: 16px;
    font-style: normal;
    font-weight: 400;
    line-height: normal;
    margin-left: -4px;
  }


  .lang-btn.active {
    background: #404040;
    color: #fff;
  }

  .goog-te-banner-frame.skiptranslate,
  #goog-gt-tt,
  .goog-te-balloon-frame,
  div#goog-gt-,
  .goog-tooltip,
  .goog-tooltip:hover,
  .goog-text-highlight {
    display: none !important;
  }

  .goog-te-gadget {
    color: transparent !important;
  }

  .goog-te-gadget .goog-te-combo {
    margin: 0 !important;
  }

  body {
    top: 0px !important;
  }

  /* หากมี notranslate class, ให้ไม่แปล */
  .notranslate {
    translate: none !important;
  }

  /* ชำระภาษี ---------------------------------  */
  .tax-payment-form {
    max-width: 800px;
    margin: 10px auto;
    padding: 25px;
    background-color: #fff;
    border-radius: 8px;
    box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
  }

  .form-title {
    text-align: center;
    color: #2c3e50;
    margin-bottom: 30px;
    padding-bottom: 15px;
    border-bottom: 2px solid #eee;
  }

  .form-section {
    background: #f8f9fa;
    padding: 20px;
    margin-bottom: 25px;
    border-radius: 6px;
    border-left: 4px solid #007bff;
  }

  .section-title {
    color: #2c3e50;
    font-size: 1.2em;
    margin-bottom: 20px;
    font-weight: 600;
  }

  .form-group {
    margin-bottom: 20px;
  }

  .form-group label {
    display: block;
    margin-bottom: 8px;
    color: #34495e;
    font-weight: 500;
  }

  .form-control {
    width: 100%;
    padding: 10px 12px;
    border: 1px solid #dce4ec;
    border-radius: 4px;
    font-size: 15px;
    transition: border-color 0.2s ease;
  }

  .form-control:focus {
    border-color: #3498db;
    outline: none;
    box-shadow: 0 0 0 3px rgba(52, 152, 219, 0.1);
  }

  .text-danger {
    color: #e74c3c;
    font-size: 0.9em;
    margin-top: 5px;
  }

  /* .btn-primary {
    background-color: #007bff;
    color: white;
    padding: 12px 25px;
    border: none;
    border-radius: 4px;
    font-size: 16px;
    font-weight: 600;
    cursor: pointer;
    width: 100%;
    transition: background-color 0.2s ease;
  }

  .btn-primary:hover {
    background-color: #0056b3;
  } */

  .file-upload {
    border: 2px dashed #dce4ec;
    padding: 20px;
    text-align: center;
    background: #f8f9fa;
    border-radius: 4px;
  }

  .required-note {
    text-align: right;
    color: #7f8c8d;
    font-size: 0.9em;
    margin-bottom: 20px;
  }

  .font-e-service-primary {
    color: #0078FF;
    leading-trim: both;
    text-edge: cap;
    font-feature-settings: 'clig' off, 'liga' off;
    font-size: 19px;
    font-style: normal;
    font-weight: 400;
    line-height: 24px;
    /* 120% */
  }

  /* Payment Section Styles */
  .payment-section {
    margin: 30px auto;
    max-width: 800px;
  }

  .payment-info-container {
    background: linear-gradient(145deg, #ffffff, #f8f9fa);
    border-radius: 15px;
    margin-bottom: 30px;
    overflow: hidden;
  }

  .payment-card {
    border: none;
    box-shadow: 0 8px 20px rgba(0, 0, 0, 0.1);
    transition: transform 0.3s ease;
  }

  .payment-card:hover {
    transform: translateY(-5px);
  }

  .payment-header {
    background: linear-gradient(45deg, #007bff, #0056b3);
    color: white;
    padding: 20px;
    border: none;
  }

  .payment-header h5 {
    font-size: 1.25rem;
    font-weight: 600;
    margin: 0;
  }

  .payment-body {
    padding: 30px;
    background-color: white;
  }

  /* Bank Details Styles */
  .bank-details {
    padding: 20px;
    background-color: #f8f9fa;
    border-radius: 10px;
    border-left: 4px solid #007bff;
  }

  .details-title {
    color: #2c3e50;
    font-size: 1.1rem;
    font-weight: 600;
    margin-bottom: 20px;
    padding-bottom: 10px;
    border-bottom: 2px solid #eee;
  }

  .detail-item {
    margin-bottom: 15px;
    padding: 10px;
    background-color: white;
    border-radius: 5px;
    box-shadow: 0 2px 5px rgba(0, 0, 0, 0.05);
  }

  .detail-label {
    font-weight: 600;
    color: #34495e;
    margin-right: 10px;
  }

  .detail-value {
    color: #2c3e50;
  }

  /* QR Code Styles */
  .qr-code-section {
    padding: 20px;
    background-color: #f8f9fa;
    border-radius: 10px;
    border-right: 4px solid #007bff;
  }

  .qr-code-container {
    background-color: white;
    padding: 15px;
    border-radius: 10px;
    box-shadow: 0 4px 15px rgba(0, 0, 0, 0.1);
    display: inline-block;
  }

  .qr-code-image {
    max-width: 200px;
    height: auto;
    border-radius: 8px;
    transition: transform 0.3s ease;
  }

  .qr-code-image:hover {
    transform: scale(1.05);
  }

  /* Icon Styles */
  .fas {
    margin-right: 8px;
  }

  /* Responsive Adjustments */
  @media (max-width: 768px) {

    .bank-details,
    .qr-code-section {
      margin-bottom: 20px;
    }

    .payment-body {
      padding: 15px;
    }

    .qr-code-image {
      max-width: 150px;
    }
  }

  /* ----------------------------------------  */

  /* preview file docs excel ppt ------------------------------------------------ */
  /* CSS เพิ่มเติมสำหรับจำนวนดาวน์โหลด */
  .doc-file-info {
    display: flex;
    align-items: center;
    flex: 1;
  }

  .doc-file-details {
    flex: 1;
    margin-left: 0;
  }

  .doc-download-stats {
    margin-top: 4px;
  }

  .doc-download-count {
    font-size: 0.85rem;
    color: #7f8c8d;
    font-weight: normal;
    display: inline-flex;
    align-items: center;
  }

  .doc-download-count::before {
    content: "📥";
    margin-right: 6px;
    font-size: 0.8rem;
  }

  .font-doc {
    font-size: 20px;
    text-shadow: 1px 1px #ccc;
  }

  .doc-preview-container {
    background-color: #ffffff;
    border-radius: 10px;
    box-shadow: 0 4px 12px rgba(0, 0, 0, 0.05);
    padding: 20px;
    margin-bottom: 24px !important;
    transition: all 0.3s ease;
  }

  .doc-preview-container:hover {
    box-shadow: 0 6px 16px rgba(0, 0, 0, 0.1);
  }

  /* ส่วนหัวเอกสาร */
  .doc-header-wrapper {
    display: flex;
    justify-content: space-between;
    align-items: center;
    padding-bottom: 15px;
    border-bottom: 1px solid #f0f0f5;
    margin-bottom: 20px !important;
  }

  .doc-icon-image {
    width: 32px;
    height: 32px;
    margin-right: 15px;
    padding: 0;
  }

  .doc-title-link {
    font-size: 1rem;
    color: #333;
    text-decoration: none;
    font-weight: 500;
    transition: color 0.2s;
    flex: 1;
  }

  .doc-title-link:hover {
    color: #3498db;
  }

  /* ปุ่มดาวน์โหลด */
  .doc-download-button {
    background-color: #56AAFF;
    color: white;
    padding: 6px 16px;
    border-radius: 4px;
    font-size: 0.875rem;
    cursor: pointer;
    transition: all 0.2s ease;
    text-decoration: none;
    display: inline-block;
    margin-left: 10px;
  }

  .doc-download-button:hover {
    background-color: #2980b9;
    color: white;
  }

  /* การ์ดสำหรับพรีวิว */
  .doc-preview-card {
    border: none;
    border-radius: 10px;
    overflow: hidden;
    box-shadow: 0 2px 8px rgba(0, 0, 0, 0.05);
    margin-top: 20px;
  }

  .doc-preview-header {
    background: linear-gradient(135deg, #56AAFF, #5BE0F6);
    color: white;
    padding: 16px 20px;
    border: none;
  }

  .doc-preview-title {
    font-weight: 500;
    font-size: 1.1rem;
    margin: 0;
  }

  .doc-preview-body {
    padding: 0 !important;
  }

  .doc-preview-footer {
    background-color: #f8f9fa;
    border-top: 1px solid #f0f0f0;
    padding: 12px 20px;
  }

  /* Excel Container */
  .excel-viewer-container {
    background-color: #fff;
    padding: 20px !important;
  }

  .excel-sheet-tabs {
    display: flex;
    overflow-x: auto;
    margin-bottom: 20px;
    border-bottom: 1px solid #e0e0e0;
    padding-bottom: 2px;
    gap: 5px;
  }

  .excel-sheet-tab {
    padding: 8px 20px;
    cursor: pointer;
    border: 1px solid #e0e0e0;
    border-bottom: none;
    border-top-left-radius: 6px;
    border-top-right-radius: 6px;
    background-color: #f8f9fa;
    font-size: 0.875rem;
    transition: all 0.2s ease;
  }

  .excel-sheet-tab.active {
    background: linear-gradient(135deg, #4b6cb7, #3498db);
    color: white;
    border-color: #3498db;
    font-weight: 500;
  }

  .excel-sheet-tab:hover:not(.active) {
    background-color: #eef2f7;
  }

  /* Loading Spinner */
  .doc-loading-center {
    text-align: center;
    padding: 20px;
  }

  .doc-loading-spinner {
    width: 40px;
    height: 40px;
    border: 4px solid rgba(52, 152, 219, 0.1);
    border-radius: 50%;
    border-top: 4px solid #3498db;
    animation: doc-spin 1s linear infinite;
    margin: 0 auto;
  }

  @keyframes doc-spin {
    0% {
      transform: rotate(0deg);
    }

    100% {
      transform: rotate(360deg);
    }
  }

  .doc-loading-text {
    color: #7f8c8d;
    font-size: 0.9rem;
    margin-top: 15px !important;
  }

  /* Alert Boxes */
  .doc-alert {
    border-radius: 8px;
    padding: 15px 20px;
    margin-bottom: 0;
    border: none;
  }

  .doc-alert-info {
    background-color: #eef7fb;
    color: #3a87ad;
    border-left: 4px solid #3498db;
  }

  .doc-alert-danger {
    background-color: #fdf2f3;
    color: #721c24;
    border-left: 4px solid #e74c3c;
  }

  .doc-alert-icon {
    margin-right: 10px;
  }

  /* iFrame */
  .doc-preview-iframe {
    border-radius: 0;
    border: none;
    width: 100%;
    height: 600px;
  }

  /* ตารางสำหรับ Excel */
  .excel-table-container {
    position: relative;
    max-height: 600px;
    overflow-y: auto;
    border: 1px solid #f0f0f0;
    border-radius: 6px;
    box-shadow: inset 0 0 10px rgba(0, 0, 0, 0.02);
  }

  .excel-data-table {
    width: 100%;
    border-collapse: collapse;
    font-family: 'Segoe UI', Arial, sans-serif;
    font-size: 14px;
    color: #333;
  }

  .excel-data-table th {
    background-color: #f5f7fa;
    font-weight: 600;
    color: #2c3e50;
    padding: 12px 15px;
    border: 1px solid #e4e7ed;
    position: sticky;
    top: 0;
    z-index: 1;
  }

  .excel-data-table td {
    padding: 12px 15px;
    border: 1px solid #e4e7ed;
  }

  .excel-data-table tr:nth-child(even) {
    background-color: #f9f9fb;
  }

  .excel-data-table tr:hover {
    background-color: #f0f7fc;
  }

  .excel-data-table [colspan],
  .excel-data-table [rowspan] {
    text-align: center;
    vertical-align: middle;
    background-color: #f5f7fa;
  }

  /* สไตล์สำหรับแถบเลื่อน */
  .excel-table-container::-webkit-scrollbar {
    width: 8px;
    height: 8px;
  }

  .excel-table-container::-webkit-scrollbar-track {
    background: #f7f8fc;
    border-radius: 4px;
  }

  .excel-table-container::-webkit-scrollbar-thumb {
    background: #bdc3c7;
    border-radius: 4px;
  }

  .excel-table-container::-webkit-scrollbar-thumb:hover {
    background: #95a5a6;
  }

  /* ฟอนต์และคำอธิบาย */
  .doc-section-title {
    font-size: 1.1rem;
    color: #34495e;
    font-weight: 500;
    margin-bottom: 15px;
    display: block;
  }

  /* แก้ไขเพื่อความเข้ากันได้กับคลาสเดิม */
  .font-pages-content-detail {
    font-size: 1.1rem;
    color: #34495e;
    font-weight: 500;
    margin-bottom: 15px;
    display: block;
  }

  /* ------------------------------------------------ */

  .google-map-footer {
    top: 353px;
    right: 261px;
    position: absolute;
    z-index: 3;
    border-radius: 20px;
    /* ทำให้มุมโค้งมน */
    overflow: hidden;
    /* ป้องกันเนื้อหาล้นขอบโค้ง */
    box-shadow: 0 10px 30px rgba(0, 0, 0, 0.1);
    /* เงาให้ดูลอย */
    padding: 8px;
    /* ระยะห่างขอบ */
    background: white;
    /* พื้นหลังสีขาว */
    border: 1px solid rgba(0, 0, 0, 0.1);
    /* ขอบบางๆ */
  }

  .google-map-footer iframe {
    border-radius: 15px;
    /* ทำให้ iframe มีมุมโค้งมนด้วย */
    width: 310px;
    height: 310px;
    display: block;
    /* ป้องกันช่องว่างด้านล่าง */
    transition: all 0.3s ease;
    /* เอฟเฟกต์เวลา hover */
  }

  /* เอฟเฟกต์เมื่อ hover */
  .google-map-footer:hover {
    transform: translateY(-5px);
    /* ยกขึ้นเล็กน้อยเมื่อชี้ */
    box-shadow: 0 15px 35px rgba(0, 0, 0, 0.15);
    /* เพิ่มเงาเมื่อชี้ */
  }

  .animation-text-orbortor-footer {
    left: 743px;
    top: 393px;
    animation: fadeTopdDown 4s linear infinite;
    position: absolute;
    z-index: 3;
  }

  .fadeTopdDown {
    animation: fadeTopdDown 4s linear infinite;
  }

  .animation-text-orbortor-header {
    left: 536px;
    top: 218px;
    animation: fadeInWel 10s ease-out, fadeTopdDown 5s linear infinite;
    position: absolute;
    z-index: 4;
  }

  /* Animation ที่สอง - delay 5 วินาที */
  .second-animation-group {
    opacity: 0;
    /* เริ่มต้นซ่อน */
    animation: fadeInSecondGroup 8s ease-out 8s forwards;
    /* delay 5s */
  }


  @keyframes fadeInWel2 {
    0% {
      opacity: 0;
    }

    50% {
      opacity: 1;
    }

    80% {
      opacity: 1;
    }

    100% {
      opacity: 0;
    }
  }

  @keyframes fadeTopdDown {
    0% {
      transform: translateY(-10px);
    }

    50% {
      transform: translateY(0);
    }

    100% {
      transform: translateY(-10px);
    }
  }

  @keyframes fadeInSecondGroup {
    0% {
      opacity: 0;
      transform: translateY(20px);
    }

    100% {
      opacity: 1;
      transform: translateY(0);
    }
  }

  @keyframes fadeInElement {
    0% {
      opacity: 0;
      transform: translateY(10px);
    }

    100% {
      opacity: 1;
      transform: translateY(0);
    }
  }

  .animation-text-orbortor-nav {
    right: 171px;
    top: 149px;
    animation: fadeInWel 10s ease-out, fadeTopdDown 5s linear infinite;
    position: absolute;
    z-index: 4;
  }

  .fadeInhead {
    animation: fadeInhead 10s forwards;
    top: 251px;
    left: 346px;
    position: absolute;
    z-index: 3;
  }

  .fadeInhead2 {
    animation: fadeInhead 10s forwards;
    animation-delay: 1s;
    opacity: 0;
    top: 453px;
    left: 418px;
    position: absolute;
    z-index: 2;
  }

  .bg-line {
    background-image: url('<?php echo base_url("docs/Line.png"); ?>');
    width: 1096px;
    height: 153px;
  }

  .fadeInhead3 {
    animation: fadeInhead 10s forwards;
    animation-delay: 2s;
    opacity: 0;
    color: #4A0D49;
    text-align: center;
    text-shadow: 1px 1px 0 #fff,
      -1px -1px 0 #fff,
      1px -1px 0 #fff,
      -1px 1px 0 #fff,
      0 1px 0 #fff,
      1px 0 0 #fff,
      0 -1px 0 #fff,
      -1px 0 0 #fff,
      0px 4px 4px rgba(0, 0, 0, 0.25);
    font-size: 18px;
    font-style: normal;
    font-weight: 500;
    line-height: normal;
    margin-top: 25px;
  }

  .fadeInhead4 {
    animation: fadeInhead 10s forwards;
    animation-delay: 3s;
    opacity: 0;
    color: #4A0D49;
    text-align: center;
    text-shadow: 1px 1px 0 #fff,
      -1px -1px 0 #fff,
      1px -1px 0 #fff,
      -1px 1px 0 #fff,
      0 1px 0 #fff,
      1px 0 0 #fff,
      0 -1px 0 #fff,
      -1px 0 0 #fff,
      0px 4px 4px rgba(0, 0, 0, 0.25);
    font-size: 18px;
    font-style: normal;
    font-weight: 500;
    line-height: normal;
    margin-top: 40px;
  }

  @keyframes fadeInhead {
    0% {
      opacity: 0;
    }

    100% {
      opacity: 1;
    }
  }

  .text-in-fade {
    opacity: 0;
    animation: fadeInnav 5s ease-in-out forwards;
    animation-delay: 6s;
  }

  .text-in-fade2 {
    opacity: 0;
    animation: fadeInnav 5s ease-in-out forwards;
    animation-delay: 6s;
  }

  @keyframes fadeInnav {

    0%,
    100% {
      opacity: 0;
      /* เริ่มต้นด้วยความโปร่งใส 0 */
    }

    20% {
      opacity: 1;
      /* ความโปร่งใสเต็มที่ */
    }
  }

  .animation-wind-1 {
    position: absolute;
    display: inline-block;
    visibility: visible;
    z-index: 3;
    margin-top: 661px;
    margin-left: 25px;
  }

  .animation-wind-2 {
    position: absolute;
    display: inline-block;
    visibility: visible;
    z-index: 3;
    margin-top: 455px;
    margin-left: 1567px;
  }

  .animation-wind-3 {
    position: absolute;
    display: inline-block;
    visibility: visible;
    z-index: 2;
    margin-top: 573px;
    margin-left: 1466px;
  }

  .animation-wind-4 {
    position: absolute;
    display: inline-block;
    visibility: visible;
    z-index: 1;
    margin-top: 613px;
    margin-left: 1343px;
  }

  .animation-wind-5 {
    position: absolute;
    display: inline-block;
    visibility: visible;
    z-index: 2;
    margin-top: 299px;
    margin-left: -166px;
  }

  .animation-wind-6 {
    position: absolute;
    display: inline-block;
    visibility: visible;
    z-index: 2;
    margin-top: -18px;
    margin-left: 1591px;
  }

  .animation-wind-7 {
    position: absolute;
    display: inline-block;
    visibility: visible;
    z-index: 3;
    margin-top: 77px;
    margin-left: 1587px;
  }

  .animation-wind-8 {
    position: absolute;
    display: inline-block;
    visibility: visible;
    z-index: 1;
    margin-top: -332px;
    margin-left: -278px;
  }

  .animation-wind-9 {
    position: absolute;
    display: inline-block;
    visibility: visible;
    z-index: 2;
    margin-top: -153px;
    margin-left: -532px;
  }

  .animation-wind-10 {
    position: absolute;
    display: inline-block;
    visibility: visible;
    z-index: 1;
    margin-top: -154px;
    margin-left: -1200px;
  }

  .animation-wind-11 {
    position: absolute;
    display: inline-block;
    visibility: visible;
    z-index: 1;
    margin-top: -820px;
    margin-left: 658px;
  }

  .animation-wind-12 {
    position: absolute;
    display: inline-block;
    visibility: visible;
    z-index: 2;
    margin-top: 0px;
    margin-left: -209px;
  }

  .animation-wind-13 {
    position: absolute;
    display: inline-block;
    visibility: visible;
    z-index: 2;
    margin-top: 0px;
    margin-left: 1590px;
  }

  .animation-wind {
    position: absolute;
    animation: animation-wind 3s infinite alternate;
    transform-origin: top center;
  }

  .animation-wind-B {
    position: absolute;
    animation: animation-wind 3s infinite alternate;
    transform-origin: bottom center;
  }

  .animation-wind-L {
    position: absolute;
    animation: animation-wind 3s infinite alternate;
    transform-origin: left center;
  }

  .animation-wind-R {
    position: absolute;
    animation: animation-wind 3s infinite alternate;
    transform-origin: right center;
  }

  .animation-wind-L-B {
    position: absolute;
    animation: animation-wind 3s infinite alternate;
    transform-origin: left bottom;
  }

  .animation-wind-R-B {
    position: absolute;
    animation: animation-wind 3s infinite alternate;
    transform-origin: right bottom;
  }


  @keyframes animation-wind {
    0% {
      transform: rotate(-2deg);
    }


    100% {
      transform: rotate(2deg);
    }
  }


  .animation-container {
    z-index: 1;
    position: absolute;
    width: 1920px;
    height: 2000px;
    overflow: hidden;
  }

  .animation-item {
    position: absolute;
    z-index: 1;
    top: 0;
    left: 0;
    animation: fallAnimation 20s linear infinite;
    visibility: hidden;
  }

  @keyframes fallAnimation {
    0% {
      transform: translateY(0) rotate(0deg) translateX(0px);
      opacity: 0;
      visibility: visible;
    }

    10% {
      transform: translateY(200px) rotate(20deg) translateX(50px);
      opacity: 0;
    }

    20% {
      transform: translateY(400px) rotate(-15deg) translateX(30px);
      opacity: 0.5;
    }

    30% {
      transform: translateY(600px) rotate(0deg) translateX(-20px);
      opacity: 0.5;
    }

    40% {
      transform: translateY(800px) rotate(-20deg) translateX(20px);
      opacity: 0.5;
    }

    50% {
      transform: translateY(1000px) rotate(15deg) translateX(-30px);
      opacity: 0.5;
    }

    60% {
      transform: translateY(1200px) rotate(0deg) translateX(10px);
      opacity: 0.1;
    }

    70% {
      transform: translateY(1400px) rotate(-25deg) translateX(50px);
      opacity: 0.1;
    }

    80% {
      transform: translateY(1600px) rotate(20deg) translateX(-20px);
      opacity: 0.1;
    }

    90% {
      transform: translateY(1800px) rotate(-15deg) translateX(30px);
      opacity: 0;
    }

    100% {
      transform: translateY(2000px) rotate(10deg) translateX(0px);
      opacity: 0;
    }
  }


  /* Service Links Slider Complete CSS */
  .service-slider {
    position: relative;
    width: 1490px;
    margin: 20px auto 0 auto;
    padding-top: 0px;
    padding-bottom: 0px;
    padding-left: 100px;
    padding-right: 100px;
    z-index: 3;
    overflow: visible;
  }

  .slider-container {
    position: relative;
    overflow: hidden;
  }

  .slider-wrapper {
    display: flex;
    transition: transform 0.6s cubic-bezier(0.4, 0, 0.2, 1);
    cursor: grab;
    user-select: none;
  }

  .slider-wrapper:active {
    cursor: grabbing;
  }

  /* Individual Slide - แสดง 5 อัน */
  .slide-service-link {
    min-width: 20%;
    flex: 0 0 20%;
    display: flex;
    align-items: center;
    justify-content: center;
    position: relative;
    overflow: hidden;
  }

  .slide-service-link a {
    display: block;
    position: relative;
    overflow: hidden;
  }

  .slide-service-link img {
    object-fit: contain;
    transition: transform 0.4s ease;
    border-radius: 0;
  }

  .slide-service-link:hover img {
    transform: translateY(-1%);
  }

  /* Navigation Buttons - อยู่ข้างนอก slider-container */
  .custom-button-prev,
  .custom-button-next {
    position: absolute;
    top: 50%;
    transform: translateY(-50%);
    cursor: pointer;
    z-index: 5;
  }

  .custom-button-prev {
    left: -1px;
  }

  .custom-button-next {
    right: -1px;
  }

  /* Hover Effects สำหรับปุ่ม */
  .custom-button-prev:hover img {
    content: url('docs/pre-home-hover.png');
  }

  .custom-button-next:hover img {
    content: url('docs/next-home-hover.png');
  }

  /* ***************************************** */
  @keyframes flutter-r {

    0%,
    100% {
      transform: rotateY(0deg);
    }

    50% {
      transform: rotateY(40deg);
    }
  }

  @keyframes flutter-l {

    0%,
    100% {
      transform: rotateY(0deg);
    }

    50% {
      transform: rotateY(-40deg);
    }
  }

  .butterfly-body {
    position: absolute;
    z-index: 2;
    animation: butterfly-up 2s infinite ease-in-out, butterfly-fly-horizontal 10s infinite linear;
  }

  .butterfly-body2 {
    position: absolute;
    z-index: 2;
    animation: butterfly-up 2s infinite ease-in-out, butterfly-fly-horizontal2 10s infinite linear;
    top: 1000px;
    right: 250px;

  }

  .butterfly-body3 {
    position: absolute;
    z-index: 2;
    top: 520px;
    right: 250px;
  }

  .butterfly-body4 {
    position: absolute;
    z-index: 2;
    animation: butterfly-up 2s infinite ease-in-out, butterfly-fly-horizontal 10s infinite linear;
  }

  .animation-wind-butterfly-body {
    position: absolute;
    z-index: 2;
  }

  .animation-wind-butterfly-1 {
    position: absolute;
    display: inline-block;
    visibility: visible;
    z-index: 3;
    margin-top: -70px;
    margin-left: -78px;
  }

  .animation-wind-butterfly-2 {
    position: absolute;
    display: inline-block;
    visibility: visible;
    z-index: 1;
    margin-top: -70px;
    margin-left: -85px;
  }

  .animation-wind-butterfly-3 {
    position: absolute;
    display: inline-block;
    visibility: visible;
    z-index: 3;
    margin-top: -120px;
    margin-left: 50px;
  }

  .animation-wind-butterfly-4 {
    position: absolute;
    display: inline-block;
    visibility: visible;
    z-index: 1;
    margin-top: -120px;
    margin-left: 50px;
  }

  .animation-wind-butterfly-R {
    position: absolute;
    animation: flutter-r 350ms infinite ease-out;
    transform-origin: right bottom;
  }

  .animation-wind-butterfly-L {
    position: absolute;
    animation: flutter-l 350ms infinite ease-out;
    transform-origin: left bottom;
  }

  @keyframes butterfly-up {
    0% {
      transform: translateY(-50px);
    }

    50% {
      transform: translateY(0);
    }

    100% {
      transform: translateY(-50px);
    }
  }

  @keyframes butterfly-fly-horizontal {
    0% {
      margin-left: -700px;
      margin-top: 900px;
    }

    100% {
      margin-left: 120%;
      margin-top: 0px;
    }
  }

  @keyframes butterfly-fly-horizontal2 {
    0% {
      right: -700px;
      margin-top: 900px;
    }

    100% {
      right: 120%;
    }
  }

  /* ***************************************** */
  .container-fish-LR {
    width: 1920px;
    height: 1000px;
    position: absolute;
    overflow: hidden;
  }

  /* ส่วนของปลา */
  .fish-animation-L {
    position: absolute;
    display: inline-block;
    animation: fish-animation-L 30s linear infinite;
    animation-delay: 0s;
    visibility: hidden;
    z-index: 1;
    top: 437px;
  }

  .fish-animation-L2 {
    position: absolute;
    display: inline-block;
    animation: fish-animation-L 30s linear infinite;
    animation-delay: 0s;
    visibility: hidden;
    z-index: 1;
    top: 600px;
    animation-delay: 3s;
  }

  .fish-animation-L3 {
    position: absolute;
    display: inline-block;
    animation: fish-animation-L 30s linear infinite;
    animation-delay: 0s;
    visibility: hidden;
    z-index: 1;
    top: 100px;
    animation-delay: 3s;
  }

  .fish-animation-L4 {
    position: absolute;
    display: inline-block;
    animation: fish-animation-L 30s linear infinite;
    animation-delay: 0s;
    visibility: hidden;
    z-index: 1;
    top: 80px;
    animation-delay: 4s;
  }

  .fish-animation-L5 {
    position: absolute;
    display: inline-block;
    animation: fish-animation-L 30s linear infinite;
    animation-delay: 0s;
    visibility: hidden;
    z-index: 1;
    top: 0px;
    animation-delay: 4.5s;
  }

  .fish-animation-L6 {
    position: absolute;
    display: inline-block;
    animation: fish-animation-L 30s linear infinite;
    animation-delay: 0s;
    visibility: hidden;
    z-index: 1;
    top: 120px;
    animation-delay: 5.2s;
  }

  .fish-animation-L .fish-body {
    clip-path: polygon(0 0, 75% 0, 75% 100%, 0 100%);
  }

  .fish-animation-L .fish-tail {
    position: absolute;
    left: 0;
    top: 0;
    clip-path: polygon(75% 0, 100% 0, 100% 100%, 75% 100%);
    transform-origin: 75% 50%;
    animation: fishTail 1s ease-in-out infinite;
  }

  .fish-animation-R {
    position: absolute;
    display: inline-block;
    animation: fish-animation-R 30s linear infinite;
    animation-delay: 0s;
    visibility: hidden;
    z-index: 1;
    top: 600px;
  }

  .fish-animation-R1 {
    position: absolute;
    display: inline-block;
    animation: fish-animation-R 30s linear infinite;
    animation-delay: 0s;
    visibility: hidden;
    z-index: 1;
    top: 384px;
    animation-delay: 3s;
  }

  .fish-animation-R2 {
    position: absolute;
    display: inline-block;
    animation: fish-animation-R 30s linear infinite;
    animation-delay: 0s;
    visibility: hidden;
    z-index: 1;
    top: 550px;
    animation-delay: 5s;
  }

  .fish-animation-R3 {
    position: absolute;
    display: inline-block;
    animation: fish-animation-R 30s linear infinite;
    animation-delay: 0s;
    visibility: hidden;
    z-index: 1;
    top: 0px;
    animation-delay: 3s;
  }

  .fish-animation-R4 {
    position: absolute;
    display: inline-block;
    animation: fish-animation-R 30s linear infinite;
    animation-delay: 0s;
    visibility: hidden;
    z-index: 1;
    top: 100px;
    animation-delay: 3s;
  }

  .fish-animation-R5 {
    position: absolute;
    display: inline-block;
    animation: fish-animation-R 30s linear infinite;
    animation-delay: 0s;
    visibility: hidden;
    z-index: 1;
    top: 80px;
    animation-delay: 4s;
  }

  .fish-animation-R6 {
    position: absolute;
    display: inline-block;
    animation: fish-animation-R 30s linear infinite;
    animation-delay: 0s;
    visibility: hidden;
    z-index: 1;
    top: 0px;
    animation-delay: 4.5s;
  }

  .fish-animation-R7 {
    position: absolute;
    display: inline-block;
    animation: fish-animation-R 30s linear infinite;
    animation-delay: 0s;
    visibility: hidden;
    z-index: 1;
    top: 120px;
    animation-delay: 5.2s;
  }

  .fish-animation-R .fish-body {
    clip-path: polygon(25% 0, 100% 0, 100% 100%, 25% 100%);
  }

  .fish-animation-R .fish-tail {
    position: absolute;
    left: 0;
    top: 0;
    clip-path: polygon(0 0, 25% 0, 25% 100%, 0 100%);
    transform-origin: 25% 50%;
    animation: fishTail 1.2s ease-in-out infinite;
  }

  @keyframes fishTail {
    0% {
      transform: rotate(0deg);
    }

    25% {
      transform: rotate(8deg);
    }

    75% {
      transform: rotate(-8deg);
    }

    100% {
      transform: rotate(0deg);
    }
  }

  @keyframes fish-animation-L {
    0% {
      transform: translate(-200px, 200px) rotate(0deg);
      visibility: visible;
      opacity: 0.5;
    }

    12.5% {
      transform: translate(480px, 300px) rotate(10deg);
      opacity: 1;
    }

    25% {
      transform: translate(960px, 250px) rotate(-10deg);
      opacity: 1;
    }

    37.5% {
      transform: translate(1440px, 350px) rotate(20deg);
      opacity: 1;
    }

    50% {
      transform: translate(1920px, 200px) rotate(0deg);
      opacity: 0;
    }

    50.01% {
      transform: translate(1920px, 200px) scaleX(-1);
      opacity: 0;
    }

    62.5% {
      transform: translate(1440px, 300px) scaleX(-1) rotate(-20deg);
      opacity: 1;
    }

    75% {
      transform: translate(960px, 250px) scaleX(-1) rotate(10deg);
      opacity: 1;
    }

    87.5% {
      transform: translate(480px, 350px) scaleX(-1) rotate(-10deg);
      opacity: 1;
    }

    100% {
      transform: translate(0px, 200px) scaleX(-1);
      opacity: 0.5;
    }
  }

  @keyframes fish-animation-R {
    0% {
      transform: translate(1920px, 150px) rotate(0deg);
      visibility: visible;
      opacity: 0.5;
    }

    12.5% {
      transform: translate(1440px, 250px) rotate(-10deg);
      opacity: 1;
    }

    25% {
      transform: translate(960px, 200px) rotate(10deg);
      opacity: 1;
    }

    37.5% {
      transform: translate(480px, 300px) rotate(-20deg);
      opacity: 1;
    }

    50% {
      transform: translate(0px, 150px) rotate(0deg);
      opacity: 0;
    }

    50.01% {
      transform: translate(0px, 150px) scaleX(-1);
      opacity: 0;
    }

    62.5% {
      transform: translate(480px, 250px) scaleX(-1) rotate(20deg);
      opacity: 1;
    }

    75% {
      transform: translate(960px, 200px) scaleX(-1) rotate(-10deg);
      opacity: 1;
    }

    87.5% {
      transform: translate(1440px, 300px) scaleX(-1) rotate(10deg);
      opacity: 1;
    }

    100% {
      transform: translate(1920px, 150px) scaleX(-1);
      opacity: 0.5;
    }
  }

  @keyframes fish-animation-R2 {
    0% {
      transform: translate(1920px, 300px) rotate(0deg);
      visibility: visible;
      opacity: 0.5;
    }

    12.5% {
      transform: translate(1440px, 200px) rotate(-10deg);
      opacity: 1;
    }

    25% {
      transform: translate(960px, 250px) rotate(10deg);
      opacity: 1;
    }

    37.5% {
      transform: translate(480px, 150px) rotate(-20deg);
      opacity: 1;
    }

    50% {
      transform: translate(0px, 300px) rotate(0deg);
      opacity: 0;
    }

    50.01% {
      transform: translate(0px, 300px) scaleX(-1);
      opacity: 0;
    }

    62.5% {
      transform: translate(480px, 200px) scaleX(-1) rotate(20deg);
      opacity: 1;
    }

    75% {
      transform: translate(960px, 250px) scaleX(-1) rotate(-10deg);
      opacity: 1;
    }

    87.5% {
      transform: translate(1440px, 150px) scaleX(-1) rotate(10deg);
      opacity: 1;
    }

    100% {
      transform: translate(1920px, 300px) scaleX(-1);
      opacity: 0.5;
    }
  }

  .container-fish {
    width: 1920px;
    height: 150px;
    margin-top: 600px;
    position: absolute;
    z-index: 2;
  }

  .fish-animation-1 {
    position: relative;
    display: inline-block;
    animation: fish-animation-1 20s linear infinite;
    animation-delay: 0s;
    visibility: hidden;
    z-index: 1;
  }

  .fish-animation-2 {
    position: relative;
    display: inline-block;
    animation: fish-animation-2 20s linear infinite;
    animation-delay: 3s;
    visibility: hidden;
    z-index: 1;
  }

  .fish-animation-3 {
    position: relative;
    display: inline-block;
    animation: fish-animation-3 20s linear infinite;
    animation-delay: 6s;
    visibility: hidden;
    z-index: 1;
  }

  .fish-animation-4 {
    position: relative;
    display: inline-block;
    animation: fish-animation-4 20s linear infinite;
    animation-delay: 9s;
    visibility: hidden;
    z-index: 1;
  }

  .fish-animation-5 {
    position: relative;
    display: inline-block;
    animation: fish-animation-5 20s linear infinite;
    animation-delay: 12s;
    visibility: hidden;
    z-index: 1;
  }


  .static-fish-animation {
    position: absolute;
    top: 0;
    clip-path: inset(0 0 0 50%);
  }

  .dynamic-fish-animation {
    position: absolute;
    top: 0;
    left: 0;
    clip-path: inset(0 40% 0 0);
    animation: jumping-fish 1s infinite cubic-bezier(0.42, 0, 0.58, 1);
  }

  @keyframes jumping-fish {
    0% {
      transform: rotate(0deg) translateY(0);
    }

    /* 25% {
            transform: rotate(10deg) translateY(-5px);
        } */

    50% {
      transform: rotate(10deg) translateY(0);
    }

    /* 75% {
            transform: rotate(-10deg) translateY(5px);
        } */

    100% {
      transform: rotate(0deg) translateY(0);
    }
  }

  .static-fish-animation2 {
    position: absolute;
    top: 0;
    clip-path: inset(0 50% 0 0);
    /* สลับจาก 0 0 0 50% */
  }

  .dynamic-fish-animation2 {
    position: absolute;
    top: 0;
    left: 0;
    clip-path: inset(0 0 0 40%);
    /* สลับจาก 0 40% 0 0 */
    animation: jumping-fish2 1s infinite cubic-bezier(0.42, 0, 0.58, 1);
  }

  @keyframes jumping-fish2 {
    0% {
      transform: rotate(0deg) translateY(0);
    }

    50% {
      transform: rotate(-10deg) translateY(0);
      /* สลับเป็น -10deg */
    }

    100% {
      transform: rotate(0deg) translateY(0);
    }
  }


  @keyframes fish-animation-1 {
    0% {
      transform: translate(0px, 50px) rotate(0deg);
      visibility: visible;
      opacity: 0.5;
    }

    25% {
      transform: translate(480px, 150px) rotate(10deg);
      opacity: 1;
    }

    50% {
      transform: translate(960px, 100px) rotate(-10deg);
      opacity: 1;
    }

    75% {
      transform: translate(1440px, 200px) rotate(20deg);
      opacity: 1;
    }

    100% {
      transform: translate(1920px, 50px) rotate(0deg);
      opacity: 0;
    }
  }

  @keyframes fish-animation-2 {
    0% {
      transform: translate(0px, 150px) rotate(0deg);
      visibility: visible;
      opacity: 0.5;
    }

    25% {
      transform: translate(480px, 250px) rotate(-10deg);
      opacity: 1;
    }

    50% {
      transform: translate(960px, 50px) rotate(10deg);
      opacity: 1;
    }

    75% {
      transform: translate(1440px, 300px) rotate(-20deg);
      opacity: 1;
    }

    100% {
      transform: translate(1920px, 150px) rotate(0deg);
      opacity: 0;
    }
  }

  @keyframes fish-animation-3 {
    0% {
      transform: translate(0px, 100px) rotate(0deg);
      visibility: visible;
      opacity: 0.5;
    }

    25% {
      transform: translate(480px, 50px) rotate(20deg);
      opacity: 1;
    }

    50% {
      transform: translate(960px, 200px) rotate(-20deg);
      opacity: 1;
    }

    75% {
      transform: translate(1440px, 100px) rotate(30deg);
      opacity: 1;
    }

    100% {
      transform: translate(1920px, 200px) rotate(0deg);
      opacity: 0;
    }
  }

  @keyframes fish-animation-4 {
    0% {
      transform: translate(0px, 200px) rotate(0deg);
      visibility: visible;
      opacity: 0.5;
    }

    25% {
      transform: translate(480px, 100px) rotate(-20deg);
      opacity: 1;
    }

    50% {
      transform: translate(960px, 300px) rotate(20deg);
      opacity: 1;
    }

    75% {
      transform: translate(1440px, 200px) rotate(-30deg);
      opacity: 1;
    }

    100% {
      transform: translate(1920px, 250px) rotate(0deg);
      opacity: 0;
    }
  }

  @keyframes fish-animation-5 {
    0% {
      transform: translate(0px, 250px) rotate(0deg);
      visibility: visible;
      opacity: 0.5;
    }

    25% {
      transform: translate(480px, 350px) rotate(0deg);
      opacity: 1;
    }

    50% {
      transform: translate(960px, 250px) rotate(0deg);
      opacity: 1;
    }

    75% {
      transform: translate(1440px, 150px) rotate(0deg);
      opacity: 1;
    }

    100% {
      transform: translate(1920px, 250px) rotate(55deg);
      opacity: 0;
    }
  }

  /* ************************************************ */

  .container-fish-LRs {
    width: 1920px;
    height: 600px;
    position: absolute;
    margin-left: 0px;
    overflow: hidden;
  }

  .fish-animation-Ls {
    position: absolute;
    display: inline-block;
    animation: fish-animation-Ls 30s linear infinite;
    visibility: hidden;
    z-index: 1;
    top: 0px;
  }

  .fish-animation-Ls2 {
    position: absolute;
    display: inline-block;
    animation: fish-animation-Ls 30s linear infinite;
    visibility: hidden;
    z-index: 1;
    top: 150px;
    animation-delay: 2s;
  }

  .fish-animation-Ls3 {
    position: absolute;
    display: inline-block;
    animation: fish-animation-Ls 30s linear infinite;
    visibility: hidden;
    z-index: 1;
    top: 100px;
    animation-delay: 4s;
  }

  .fish-animation-Rs {
    position: absolute;
    display: inline-block;
    animation: fish-animation-Rs 30s linear infinite;
    visibility: hidden;
    z-index: 1;
    top: 50px;
  }

  .fish-animation-Rs2 {
    position: absolute;
    display: inline-block;
    animation: fish-animation-Rs 30s linear infinite;
    visibility: hidden;
    z-index: 1;
    top: 200px;
    animation-delay: 2s;
  }

  .fish-animation-Rs3 {
    position: absolute;
    display: inline-block;
    animation: fish-animation-Rs 30s linear infinite;
    visibility: hidden;
    z-index: 1;
    top: 100px;
    animation-delay: 4s;
  }

  @keyframes fish-animation-Ls {
    0% {
      transform: translate(0px, 50px) rotate(0deg);
      visibility: visible;
      opacity: 0.5;
    }

    12.5% {
      transform: translate(480px, 150px) rotate(10deg);
      opacity: 1;
    }

    25% {
      transform: translate(960px, 100px) rotate(-10deg);
      opacity: 1;
    }

    37.5% {
      transform: translate(1440px, 200px) rotate(20deg);
      opacity: 1;
    }

    50% {
      transform: translate(1920px, 50px) rotate(0deg);
      opacity: 0;
    }

    50.01% {
      transform: translate(1920px, 50px) scaleX(-1);
      opacity: 0;
    }

    62.5% {
      transform: translate(1440px, 200px) scaleX(-1) rotate(-20deg);
      opacity: 1;
    }

    75% {
      transform: translate(960px, 100px) scaleX(-1) rotate(10deg);
      opacity: 1;
    }

    87.5% {
      transform: translate(480px, 150px) scaleX(-1) rotate(-10deg);
      opacity: 1;
    }

    100% {
      transform: translate(0px, 50px) scaleX(-1);
      opacity: 0.5;
    }
  }

  @keyframes fish-animation-Rs {
    0% {
      transform: translate(1920px, 50px) rotate(0deg);
      visibility: visible;
      opacity: 0.5;
    }

    12.5% {
      transform: translate(1440px, 150px) rotate(-10deg);
      opacity: 1;
    }

    25% {
      transform: translate(960px, 100px) rotate(10deg);
      opacity: 1;
    }

    37.5% {
      transform: translate(480px, 200px) rotate(-20deg);
      opacity: 1;
    }

    50% {
      transform: translate(0px, 50px) rotate(0deg);
      opacity: 0;
    }

    50.01% {
      transform: translate(0px, 50px) scaleX(-1);
      opacity: 0;
    }

    62.5% {
      transform: translate(480px, 200px) scaleX(-1) rotate(20deg);
      opacity: 1;
    }

    75% {
      transform: translate(960px, 100px) scaleX(-1) rotate(-10deg);
      opacity: 1;
    }

    87.5% {
      transform: translate(1440px, 150px) scaleX(-1) rotate(10deg);
      opacity: 1;
    }

    100% {
      transform: translate(1920px, 50px) scaleX(-1);
      opacity: 0.5;
    }
  }

  /* ***************************************** */

  /* โครงสร้างบุคลากรใหม่ =================================== */
  .structure-dropdown-container {
    display: flex;
    gap: 50px;
    justify-content: flex-start;
    min-width: 1000px;
  }

  .structure-dropdown-column {
    flex: 1;
    min-width: 250px;
  }

  .sub-indent {
    display: inline-block;
    width: 16px;
    /* เพิ่มช่องว่างสำหรับไอคอน sub */
    margin-right: 4px;
  }

  .structure-dropdown-column a {
    display: flex;
    align-items: flex-start;
    /* หรือ align-items: center; ถ้าต้องการให้อยู่กลาง */
  }

  .sub-item-content {
    margin-left: 20px;
  }

  .sub-item-content::before {
    content: "└─ ";
    color: #999;
    margin-right: 4px;
  }

  /* ========================================================== */


  /* preview img Fancybo start =========================================== */
  .gallery-item {
    transition: transform 0.3s ease, box-shadow 0.3s ease;
    cursor: pointer;
    border-radius: 10px;
    overflow: hidden;
  }

  .gallery-item:hover {
    transform: scale(1.05);
    box-shadow: 0 8px 25px rgba(0, 0, 0, 0.2);
  }

  /* preview img Fancybo end =========================================== */
  @keyframes logoBounceIn {
    0% {
      opacity: 0;
      transform: scale(0.3);
    }

    50% {
      opacity: 1;
      transform: scale(1.1);
    }

    70% {
      transform: scale(0.9);
    }

    100% {
      transform: scale(1);
    }
  }

  .logo-animate {
    animation: logoBounceIn 0.8s ease-out forwards;
  }

  .bg-population {
    background-image: url('<?php echo base_url("docs/bg-population.png"); ?>');
    background-repeat: no-repeat;
    background-size: 100%;
    width: 1920px;
    height: 500px;
    z-index: 1;
    position: relative;
    overflow: hidden;
  }

  .text-head-ci {
    color: #FFF;
    text-shadow: 0 4px 4px rgba(0, 0, 0, 0.25);
    font-size: 40px;
    font-style: normal;
    font-weight: 700;
    line-height: normal;
    display: inline-block;
    /* margin-bottom: 29px; */
  }

  .text-head-ci2 {
    color: #FFF;
    font-size: 24px;
    font-style: normal;
    font-weight: 600;
    line-height: normal;
    display: inline-block;
    margin-bottom: 37px;
  }

  .text-head-ci3 {
    color: #FFF;
    font-size: 24px;
    font-style: normal;
    font-weight: 400;
    line-height: normal;
  }

  .population-container {
    display: flex;
    gap: 40px;
    justify-content: center;
    align-items: center;
  }

  .population-item {
    display: flex;
    flex-direction: column;
    align-items: center;
    gap: 5px;
    margin-top: 148px;
  }

  .population-label {
    color: #FFF;
    text-align: center;
    text-shadow: 0 4px 4px rgba(0, 0, 0, 0.25);
    font-size: 40px;
    font-style: normal;
    font-weight: 700;
    line-height: normal;
  }

  .population-box {
    width: 289.49px;
    height: 125.865px;
    flex-shrink: 0;
    border-radius: 20px;
    background: rgba(255, 255, 255, 0.80);
    box-shadow: 0 4px 4px 0 rgba(0, 0, 0, 0.25) inset;
    backdrop-filter: blur(2px);
    display: flex;
    justify-content: center;
    align-items: center;
    text-align: center;
  }

  .population-box2 {
    width: 327px;
    height: 126px;
    flex-shrink: 0;
    border-radius: 20px;
    background: rgba(255, 255, 255, 0.80);
    box-shadow: 0 4px 4px 0 rgba(0, 0, 0, 0.25) inset;
    backdrop-filter: blur(2px);
    display: flex;
    justify-content: center;
    align-items: center;
    text-align: center;
  }

  .population-number {
    color: #404040;
    text-align: center;
    font-size: 40px;
    font-style: normal;
    font-weight: 700;
    line-height: normal;
  }

  .text-head-ci4 {
    color: #FFF;
    text-align: center;
    text-shadow: 0 4px 4px rgba(0, 0, 0, 0.25);
    font-size: 40px;
    font-style: normal;
    font-weight: 700;
    line-height: normal;
  }
</style>