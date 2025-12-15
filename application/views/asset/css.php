<style>
  body {
    margin: 0px;
    padding: 0px;
    font-family: 'Kanit', sans-serif;
    font-weight: 300;
  }
	
	/* search หลังบ้าน ตัวใหม่ start =================================================== */
  #menuList {
    list-style-type: none;
    padding: 0;
    backdrop-filter: blur(15px);
    box-shadow: 0 0 5px rgba(8, 7, 16, 0.6);
    z-index: 999;
    position: absolute;
    margin-top: -35px;
    padding: 10px 10px;
    border-radius: 24px;
    max-height: 400px;
    overflow-y: auto;
    min-width: 300px;
    background-color: rgba(255, 255, 255, 0.95);
  }

  #menuList li {
    background-color: #e2e2e2;
    width: auto;
    max-width: 100%;
    height: auto;
    min-height: 30px;
    margin-bottom: 8px;
    padding: 8px 12px;
    border-radius: 20px;
    transition: all 0.2s ease;
  }

  #menuList li a {
    text-decoration: none;
    color: #777777;
    display: block;
    font-size: 14px;
  }

  #menuList li:hover {
    background-color: #f2f2f2;
    transform: translateY(-1px);
    box-shadow: 0 2px 5px rgba(0, 0, 0, 0.1);
  }

  #menuList li a:hover {
    text-decoration: none;
    color: #555555;
  }

  .search-highlight {
    background-color: #ffeb3b;
    padding: 1px 3px;
    border-radius: 3px;
    font-weight: bold;
  }

  .menu-category {
    font-size: 12px;
    color: #999;
    margin-bottom: 2px;
  }

  .no-results {
    text-align: center;
    color: #999;
    font-style: italic;
    padding: 20px;
  }

  .color-search {
    color: blue;
  }

  .link {
    color: #777777;
    text-decoration: none;
  }

  .link:hover {
    color: #777777;
    text-decoration: none;
  }

  /* search หลังบ้าน ตัวใหม่ end =================================================== */

  <?php
  defined('BASEPATH') or exit('No direct script access allowed');

  // เพิ่มส่วนนี้ที่ด้านบนของไฟล์
  if (!isset($current_theme)) {
    $CI = &get_instance();
    $CI->load->model('Theme_model');
    $current_theme = $CI->Theme_model->get_current_theme();
  }
  ?>

  /* Sidebar gradient */
  .sidebar.bg-gradient-custom {
    background: linear-gradient(135deg, <?php echo isset($current_theme) ? $current_theme->primary_color : '#667eea'; ?> 0%, <?php echo isset($current_theme) ? $current_theme->gradient_start : '#764ba2'; ?> 100%) !important;
  }


  .btn-custom {
    background-color: <?php echo $current_theme->primary_color; ?>;
  }

  .btn-custom:hover {
    background-color: <?php echo $current_theme->primary_color; ?>;
  }

  /* Footer gradient */
  .sticky-footer {
    background: linear-gradient(135deg, <?php echo $current_theme->gradient_start; ?> 0%, <?php echo $current_theme->gradient_end; ?> 100%) !important;
  }

  /* Scroll to top button */
  .scroll-to-top {
    background: linear-gradient(135deg, <?php echo $current_theme->gradient_start; ?>, <?php echo $current_theme->gradient_end; ?>) !important;
  }
	
  .sidebar .collapse-inner .collapse-item {
    white-space: normal !important;
  }

  .white {
    color: #ffff;
  }

  .red-add {
    color: red;
    font-size: 14px;
  }

  .black-add {
    color: gray;
    font-size: 14px;
  }

  .form-group {
    display: flex;
    align-items: center;
  }

  .form-group label {
    flex: 1;
    text-align: right;
    padding-right: 10px;
  }

  .form-group input {
    flex: 2;
  }

  /* เปลี่ยนสีพื้นหลังหน้าเนื้อหา */
  #content {
    background-color: white;
  }

  /* css ของตาราง table */
  /* ปรับแต่งข้อความ "Show X entries" */
  .dataTables_length label {
    font-size: 12px;
    /* ปรับขนาดตัวอักษร */
    display: inline-flex;
    /* จัดเรียงให้อยู่ในแถวเดียวกัน */
    align-items: center;
    /* จัดให้เนื้อหาแนวกึ่งกลางตามแนวดิ่ง */
    margin: auto;
    /* ระยะห่างของข้อความและ dropdown */
  }

  /* ปรับขนาด dropdown */
  .dataTables_length select {
    font-size: 12px;
    /* ปรับขนาดตัวอักษรใน dropdown */
    height: 25px;
    /* ปรับความสูงของ dropdown */
    width: 60px;
    /* ปรับความกว้างของ dropdown */
  }

  /* เปลี่ยนสีพื้นหลังของหัวข้อ th ในตาราง */
  /* #dataTable th {
    background-color: #f5f5f5;
  } */

  /* เปลี่ยนสีพื้นหลังของ th เป็นสีแดง */
  .table th {
    background-color: #f2f2f2;
    /* สีพื้นหลังสำหรับ th */
    color: white;
    /* สีข้อความใน th เพื่อให้อ่านง่าย */
  }

  .limited-text {
    max-width: 10px;
    /* กำหนดความยาวสูงสุดของเซลล์ */
    white-space: nowrap;
    /* ไม่ยอมขึ้นบรรทัดใหม่ */
    overflow: hidden;
    /* ซ่อนข้อความที่เกินความยาว */
    text-overflow: ellipsis;
    /* แสดง ... เมื่อข้อความเกินความยาว */
  }

  /* เพิ่มเส้นขอบด้านซ้ายสุดและขวาสุดของตาราง */
  #newdataTables {
    border-left: 1px solid #a3a3a3;
    /* เส้นขอบด้านซ้ายสุด */
    border-right: 1px solid #a3a3a3;
    /* เส้นขอบด้านขวาสุด */
    border-bottom: 1px solid #a3a3a3;
    border-top: 1px solid #a3a3a3;
  }

  #newdataTables th {
    border-top: 1px solid #a3a3a3;
    /* เส้นขอบด้านซ้าย */
    border-bottom: 1px solid #a3a3a3;
    /* เส้นขอบด้านซ้าย */
  }

  /* เปลี่ยนสีตัวอักษรใน thead เป็นสีดำ */
  #newdataTables thead th {
    color: black;
  }

  /* เพิ่มเส้นขอบด้านซ้ายสุดและขวาสุดของตาราง */
  #importantday {
    border-left: 1px solid #a3a3a3;
    /* เส้นขอบด้านซ้ายสุด */
    border-right: 1px solid #a3a3a3;
    /* เส้นขอบด้านขวาสุด */
    border-bottom: 1px solid #a3a3a3;
    border-top: 1px solid #a3a3a3;
  }

  #importantday th {
    border-top: 1px solid #a3a3a3;
    /* เส้นขอบด้านซ้าย */
    border-bottom: 1px solid #a3a3a3;
    /* เส้นขอบด้านซ้าย */
  }

  /* เปลี่ยนสีตัวอักษรใน thead เป็นสีดำ */
  #importantday thead th {
    color: black;
  }

  .add-btn {
    background-color: #83d37c;
    /* เปลี่ยนสีพื้นหลังปุ่ม */
    color: black;
  }

  /* กำหนดสีเดิมเมื่อ hover */
  .add-btn:hover {
    background-color: #83d37c;
    /* เปลี่ยนสีพื้นหลังปุ่มเป็นสีเดิม */
    color: black;
  }

  /* ตัวหนังสือสีดำ */
  .font-black {
    color: black;
  }

  .file-container {
    display: flex;
    align-items: center;
    justify-content: flex-end;
  }

  .chart-container {
    width: 100%;
    height: 50%;
    margin: auto;
  }

  /* ใช้เพื่อลบเส้นข้างในตาราง */
  .custom-table {
    border-collapse: collapse;
  }

  /* ใช้เพื่อสร้างกรอบโค้งด้านนอกของตาราง */
  .custom-table {
    border-collapse: separate;
    border-spacing: 10px;
    /* ปรับระยะห่างของกรอบโค้ง */
    border-radius: 20px;
    /* ปรับขอบโค้งของกรอบโค้ง */
    overflow: hidden;
    /* ป้องกันขอบโค้งเกินขอบตาราง */
  }

  /* สำหรับเซลล์ในตาราง */
  .custom-table th,
  .custom-table td {
    padding: 8px;
    /* ปรับระยะห่างของข้อมูลภายในเซลล์ */
  }

  /* เปลี่ยนสีพื้นหลังของ th เป็นสีแดง */
  .custom-table th {
    background-color: #fff;
    /* สีพื้นหลังสำหรับ th */
    color: black;
    /* สีข้อความใน th เพื่อให้อ่านง่าย */
  }


  /* เส้นขั้นแนวนอนระหว่าง td ในตาราง */
  .custom-table td {
    border-bottom: 1px solid #ddd;
    /* เปลี่ยนสีและรูปแบบขอบของเส้นขั้นแนวนอน */
  }

  .path {
    font-size: 12px;
    color: #888;
  }

  .modal {
    display: none;
    position: fixed;
    z-index: 1;
    left: 0;
    top: 0;
    width: 100%;
    height: 100%;
    background-color: rgba(0, 0, 0, 0.7);
  }

  .modal-content {
    background-color: #fff;
    margin: 10% auto;
    padding: 20px;
    border: 1px solid #888;
    width: 80%;
    max-width: 600px;
    position: relative;
  }

  /* เริ่มต้น: ปุ่มเล็ก */
  .responsive-btn-ban {
    font-size: 12px;
    padding: 5px 10px;
    /* ปรับขนาดของปุ่มตามที่คุณต้องการ */
  }

  /* ใช้ Flexbox หรือ Grid Layout ให้ปุ่มขยายเมื่อหน้าจอกว้างขึ้น */
  @media screen and (min-width: 1500px) {
    .responsive-btn-ban {
      font-size: 13px;
      /* ปรับขนาดของปุ่มเมื่อหน้าจอกว้างขึ้น */
      padding: 10px 20px;
      /* ปรับขนาดของปุ่มเมื่อหน้าจอกว้างขึ้น */
    }
  }

  .switch {
    --button-width: 3.5em;
    --button-height: 1.5em;
    --toggle-diameter: 1.0em;
    --button-toggle-offset: calc((var(--button-height) - var(--toggle-diameter)) / 2);
    --toggle-shadow-offset: 10px;
    --toggle-wider: 3em;
    --color-grey: #cccccc;
    --color-green: #83d37c;
  }

  .slider {
    display: inline-block;
    width: var(--button-width);
    height: var(--button-height);
    background-color: var(--color-grey);
    border-radius: calc(var(--button-height) / 2);
    position: relative;
    transition: 0.3s all ease-in-out;
  }

  .slider::after {
    content: "";
    display: inline-block;
    width: var(--toggle-diameter);
    height: var(--toggle-diameter);
    background-color: #fff;
    border-radius: calc(var(--toggle-diameter) / 2);
    position: absolute;
    top: var(--button-toggle-offset);
    transform: translateX(var(--button-toggle-offset));
    box-shadow: var(--toggle-shadow-offset) 0 calc(var(--toggle-shadow-offset) * 4) rgba(0, 0, 0, 0.1);
    transition: 0.3s all ease-in-out;
  }

  .switch input[type="checkbox"]:checked+.slider {
    background-color: var(--color-green);
  }

  .switch input[type="checkbox"]:checked+.slider::after {
    transform: translateX(calc(var(--button-width) - var(--toggle-diameter) - var(--button-toggle-offset)));
    box-shadow: calc(var(--toggle-shadow-offset) * -1) 0 calc(var(--toggle-shadow-offset) * 4) rgba(0, 0, 0, 0.1);
  }

  .switch input[type="checkbox"] {
    display: none;
  }

  .switch input[type="checkbox"]:active+.slider::after {
    width: var(--toggle-wider);
  }

  .switch input[type="checkbox"]:checked:active+.slider::after {
    transform: translateX(calc(var(--button-width) - var(--toggle-wider) - var(--button-toggle-offset)));
  }

  .popup {
    display: none;
    position: fixed;
    top: 0;
    left: 0;
    width: 100%;
    height: 100%;
    background-color: rgba(0, 0, 0, 0.5);
    z-index: 1;
    overflow: auto;
  }

  .popup-content {
    background-color: #fff;
    margin: 10% auto;
    padding: 20px;
    border: 1px solid #888;
    width: 80%;
  }

  .close-button {
    float: right;
    /* ชิดขวา  */
    color: white;
    display: none;
  }

  .limit-font-one {
    /* margin-bottom: 10px; */
    overflow: hidden;
    text-overflow: ellipsis;
    display: -webkit-box;
    -webkit-line-clamp: 1;
    -webkit-box-orient: vertical;
  }

  .hide {
    display: none !important;
  }

  .card-personnel {
    border: 2px solid gray;
    width: 220px;
    height: auto;
    text-align: center;
  }

  .underline {
    text-decoration: none;
    color: inherit;
  }

  .underline:hover {
    text-decoration: none;
    color: inherit;
  }
	
  .hidden {
    display: none;
  }

  /* chart แบบประเมินความพึงพอใจ ------------------- */
  @media print {

    /* ซ่อนปุ่มพิมพ์ */
    .btn-print {
      display: none !important;
      /* ซ่อนปุ่มพิมพ์เมื่อพิมพ์ */
    }

    /* สไตล์สำหรับหน้า A4 landscape */
    @page {
      size: A4 landscape;
      margin: 10mm;
    }
  }

  /* สไตล์พื้นฐาน */
  .flex {
    display: flex;
    flex-wrap: wrap;
    /* เพื่อให้กราฟสามารถยืดหยุ่นได้ */
  }

  #chart_gender {
    width: 25%;
  }

  #chart_age {
    width: 50%;
  }

  #chart_study {
    width: 50%;
  }

  #chart_occupation {
    width: 75%;
  }

  #chart_assessment1,
  #chart_assessment2,
  #chart_assessment3 {
    width: 33%;
  }

  #chart_assessment1_message {
    width: 22%;
    text-align: center;
  }

  #chart_assessment2_message {
    width: 46.5%;
    text-align: center;
  }

  #chart_assessment3_message {
    width: 21%;
    text-align: center;
  }

  .chart-message {
    display: flex;
    align-items: center;
    justify-content: center;
    text-align: center;
  }

  .statusSquare {
    width: 20px;
    height: 20px;
    margin-right: 5px;
    border-radius: 3px;
    /* มุมโค้ง */
  }
	
	
  /* ******************** ย้ายตำแหน่งบุคลากร *********************** */
  /* ปรับแต่งหน้าตาของ card บุคลากร */
  .card-personnel {
    border: 1px solid #ddd;
    border-radius: 8px;
    text-align: center;
    background-color: #fff;
    transition: all 0.3s ease;
    position: relative;
    box-shadow: 0 2px 5px rgba(0, 0, 0, 0.1);
  }

  /* เพิ่ม style เมื่ออยู่ในโหมดลาก */
  .drag-mode .sortable-item {
    cursor: move;
  }

  .drag-mode .card-personnel {
    border: 1px dashed #007bff;
  }

  .drag-mode .card-personnel:hover {
    box-shadow: 0 5px 15px rgba(0, 123, 255, 0.3);
    transform: translateY(-5px);
  }

  /* รูปภาพ */
  .executive-img {
    border-radius: 4px;
    margin-bottom: 10px;
  }

  /* จัดการปุ่มลาก */
  .drag-handle {
    position: absolute;
    top: 10px;
    right: 10px;
    background-color: rgba(0, 0, 0, 0.7);
    color: #fff;
    padding: 5px 10px;
    border-radius: 4px;
    font-size: 12px;
    z-index: 10;
    opacity: 0;
    transition: opacity 0.3s;
    display: none;
  }

  .drag-mode .drag-handle {
    display: block;
  }

  .drag-mode .card-personnel:hover .drag-handle {
    opacity: 1;
  }

  /* สไตล์สำหรับตอนลากวาง */
  .sortable-ghost {
    opacity: 0.5;
  }

  .sortable-chosen {
    z-index: 100;
  }

  /* ปรับขนาดลิงก์ที่อยู่ใน card */
  .card-personnel a {
    display: block;
    color: #333;
    text-decoration: none;
  }

  /* ปรับขนาดตัวอักษร */
  .card-personnel span {
    display: block;
    font-size: 14px;
    line-height: 1.6;
  }

  /* ปรับแต่งโซนควบคุม */
  .drag-controls {
    margin-bottom: 20px;
    border-bottom: 1px solid #eee;
    padding-bottom: 15px;
  }

  /* ******************************************** */
	
	
	/*//////// จัดเรียงตำแหน่งรูป ไฟล์ต่างๆ //////////////*/

  .image-item {
    border: 1px solid #ddd;
    border-radius: 4px;
    overflow: hidden;
    height: 100%;
    position: relative;
    background-color: #f8f9fa;
    margin-bottom: 5px;
    transition: border-color 0.3s;
  }

  .image-checkbox {
    position: absolute;
    top: 5px;
    left: 5px;
    z-index: 10;
  }

  .image-checkbox input[type="checkbox"] {
    display: none;
  }

  .image-checkbox label {
    display: block;
    width: 20px;
    height: 20px;
    background-color: #fff;
    border: 2px solid #ddd;
    border-radius: 3px;
    cursor: pointer;
    position: relative;
  }

  .image-checkbox input[type="checkbox"]:checked+label {
    background-color: #007bff;
    border-color: #007bff;
  }

  .image-checkbox input[type="checkbox"]:checked+label:after {
    content: "✓";
    position: absolute;
    top: 0;
    left: 4px;
    color: white;
    font-size: 14px;
  }

  .image-item.selected {
    border: 2px solid #007bff;
  }

  .img-wrapper {
    width: 100%;
    padding-top: 75%;
    /* อัตราส่วน 4:3 */
    position: relative;
    overflow: hidden;
  }

  .img-wrapper img {
    position: absolute;
    top: 0;
    left: 0;
    width: 100%;
    height: 100%;
    object-fit: cover;
    background-color: #f8f9fa;
  }

  .img-name {
    position: absolute;
    bottom: 0;
    left: 0;
    right: 0;
    background-color: rgba(0, 0, 0, 0.6);
    color: white;
    padding: 2px 5px;
    font-size: 10px;
    text-align: center;
    white-space: nowrap;
    overflow: hidden;
    text-overflow: ellipsis;
  }

  /* สไตล์สำหรับตัวอย่างรูปภาพที่เลือก */
  #file-preview .preview-item {
    position: relative;
    margin-bottom: 5px;
  }

  #file-preview .preview-wrapper {
    width: 100%;
    padding-top: 75%;
    position: relative;
    overflow: hidden;
    border: 1px solid #ddd;
    border-radius: 4px;
  }

  #file-preview img {
    position: absolute;
    top: 0;
    left: 0;
    width: 100%;
    height: 100%;
    object-fit: cover;
    background-color: #f8f9fa;
  }

  #file-preview .preview-name {
    position: absolute;
    bottom: 0;
    left: 0;
    right: 0;
    background-color: rgba(0, 0, 0, 0.6);
    color: white;
    padding: 2px 5px;
    font-size: 10px;
    text-align: center;
    white-space: nowrap;
    overflow: hidden;
    text-overflow: ellipsis;
  }

  .btn i.bi,
  .btn svg {
    vertical-align: middle;
    margin-top: -3px;
  }

  .align-middle {
    vertical-align: middle;
  }

  /*//////// สิ้นสุดจัดเรียงตำแหน่งรูป ไฟล์ต่างๆ //////////////*/
	
	
/* CSS สำหรับ Progress Bar */
.visitor-progress, .member-progress {
  position: relative;
  height: 40px; /* กำหนดความสูงของ progress bar */
}

.visitor-progress .progress-bar {
  height: 100%;
  border-radius: 20px;
}

.member-progress .progress-bar {
  height: 100%;
  border-radius: 20px;
  display: flex;
  justify-content: space-between;
  align-items: center;
  padding: 0 15px;
}

.visitor-progress .member-name, .member-progress .member-name {
  font-weight: 500;
  color: white;
  text-shadow: 1px 1px 2px rgba(0,0,0,0.5);
}

.visitor-progress .member-count, .member-progress .member-count {
  font-weight: bold;
  color: white;
  text-shadow: 1px 1px 2px rgba(0,0,0,0.5);
}

/* วางข้อความทับบน progress bar (สำหรับแบบแยก element) */
.progress-text {
  position: absolute;
  top: 0;
  left: 0;
  right: 0;
  bottom: 0;
  display: flex;
  justify-content: space-between;
  align-items: center;
  padding: 0 15px;
  z-index: 2;
}

.progress-text .member-name {
  font-weight: 500;
  color: white;
  text-shadow: 1px 1px 2px rgba(0,0,0,0.5);
}

.progress-text .member-count {
  font-weight: bold;
  color: white;
  text-shadow: 1px 1px 2px rgba(0,0,0,0.5);
}
	
	  /* โครงสร้างบุคลากรใหม่ start ======================*/
  .sidebar-sub-indent {
    display: inline-block;
    width: 16px;
    margin-right: 8px;
    color: #adb5bd;
  }

  .sidebar-sub-indent::before {
    content: "└─";
    font-size: 11px;
  }
  /* โครงสร้างบุคลากรใหม่ end ======================*/
</style>