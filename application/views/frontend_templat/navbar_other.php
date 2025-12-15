  <!-- // ปุ่ม scroll-to-top  -->
  <!-- ชื่อ อบต  2 จุด -->
  <a onclick="scrolltotopFunction()" id="scroll-to-top-other" title="Go to top"></a>
  <a id="scroll-to-back" title="Go to back"></a>

  <?php
    /**
     * ตรวจสอบสถานะการเข้าสู่ระบบและแสดงข้อมูลผู้ใช้
     */
    $is_logged_in = false;
    $user_info = [];
    $user_type = '';

    // ตรวจสอบผู้ใช้ประชาชน (Public User)
    if ($this->session->userdata('mp_id')) {
        $is_logged_in = true;
        $user_type = 'public';
        $user_info = [
            'id' => $this->session->userdata('mp_id'),
            'name' => trim($this->session->userdata('mp_fname') . ' ' . $this->session->userdata('mp_lname')),
            'email' => $this->session->userdata('mp_email'),
            'img' => $this->session->userdata('mp_img'),
            'login_type' => 'ประชาชน'
        ];
    }
    // ตรวจสอบเจ้าหน้าที่ (Staff User)
    elseif ($this->session->userdata('m_id')) {
        $is_logged_in = true;
        $user_type = 'staff';
        $user_info = [
            'id' => $this->session->userdata('m_id'),
            'name' => trim($this->session->userdata('m_fname') . ' ' . $this->session->userdata('m_lname')),
            'username' => $this->session->userdata('m_username'),
            'img' => $this->session->userdata('m_img'),
            'level' => $this->session->userdata('m_level'),
            'login_type' => 'เจ้าหน้าที่'
        ];
    }
    ?>

  <style>
      #navbar2 {
          background-image: url('<?php echo base_url("docs/bg-nav-stick.png"); ?>');
          height: 80px;
          width: 1920px;
          display: none;
          position: fixed;
          transition: top 0.3s ease-in-out;
          font-size: 24px;
          padding-left: 120px;
          z-index: 100;
      }

      #navbar2:hover {
          top: 0;
      }

      @media screen and (max-width: 768px) {
          #navbar2 {
              display: none;
          }
      }

      ul li {
          list-style: none;
      }

      /* ul {
        list-style-type: none;
        margin: 0;
        padding: 0;
        overflow: hidden;
        background-color: #333;
    } */

      li {
          float: left;
      }

      li a,
      .dropbtn {
          display: inline-block;
          color: white;
          text-align: center;
          padding: 20px 14px;
          text-decoration: none;

      }

      li a,
      .dropdown .dropbtn {
          position: relative;
          text-align: center;
          /* font-family: "Noto Looped Thai UI"; */
          font-size: 24px;
          font-style: normal;
          font-weight: 600;
          line-height: 1.1;
      }

      /* เส้นใต้สำหรับ li a เท่านั้น */
      .navbar3 ul>li>a:hover::after,
      .navbar3 ul>li.dropdown:hover .dropbtn::after {
          content: '';
          position: absolute;
          left: 0;
          right: 0;
          bottom: 20px;
          /* ระยะห่างของเส้นจากตัวอักษร */
          height: 2px;
          /* ความหนาของเส้น */
          /* background: linear-gradient(180deg, #F9A602 2.64%, #F5C728 78.74%, #F5D033 97.76%); */
          background-color: #FFF33B;
          /* สีของเส้น */
          transform: scaleX(1);
          transition: transform 0.3s;
      }

      ul>li>a::after,
      ul>li.dropdown .dropbtn::after {
          content: '';
          position: absolute;
          left: 0;
          right: 0;
          bottom: 10px;
          /* ระยะห่างของเส้นจากตัวอักษร */
          height: 2px;
          /* ความหนาของเส้น */
          background-color: transparent;
          transform: scaleX(0);
          transition: transform 0.3s;
      }

      li.dropdown {
          display: inline-block;
      }


      .dropdown-content {
          /* display: none;
        position: absolute;
        background-color: #f9f9f9;
        min-width: 160px;
        box-shadow: 0px 8px 16px 0px rgba(0, 0, 0, 0.2);
        z-index: 1; */

          background-image: url('<?php echo base_url("docs/bg-nav-content.png"); ?>');
          background-repeat: no-repeat;
          background-size: 100%;
          display: none;
          position: fixed;
          width: 1920px;
          height: 584px;
          z-index: 2;
          left: 50%;
          /* ย้าย dropdown ไปที่กึ่งกลางตามแนวนอน */
          top: 290px;
          /* ย้าย dropdown ไปที่กึ่งกลางตามแนวตั้ง */
          transform: translate(-50%, -50%);
          /* แก้ไขตำแหน่งให้เป็นตรงกลาง */
      }

      .dropdown-content a {
          color: black;
          padding: 12px 16px;
          text-decoration: none;
          display: block;
          text-align: left;
      }

      .dropdown-content a:hover {
          /* background-color: #f1f1f1; */
      }

      .dropdown:hover .dropdown-content {
          display: block;
      }

      @keyframes gradient-move {
          0% {
              background-position: 100% 0%;
          }

          100% {
              background-position: 0% 0%;
          }
      }

      .font-head-navbar-letf-logo1 {
          background: var(--Gold2, linear-gradient(90deg, #D9AA58 4.04%, #F2B940 27.1%, #DEAE3F 46.15%, #E0B344 52.16%, #E7C354 61.19%, #F2DE6F 70.21%, #FFFC8D 78.23%, #FFE875 82.24%, #FFD55E 88.25%, #AA7100 100.28%));
          background-size: 1000% 1000%;
          background-clip: text;
          -webkit-background-clip: text;
          -webkit-text-fill-color: transparent;
          font-size: 34px;
          font-style: normal;
          font-weight: 600;
          line-height: normal;
          z-index: 1;
          animation: gradient-move 10s linear infinite;
      }

      .font-head-navbar-letf-logo2 {
          color: #4A0D49;
          /* font-family: "Noto Looped Thai UI"; */
          font-size: 20px;
          font-style: normal;
          font-weight: 400;
          line-height: normal;
      }

      .search {
          width: 309px;
          position: absolute;
          top: -10%;
          left: 77%;
      }

      .gsc-search-button-v2 {
          /* background-color: #007bff; */
          /* ปรับสีพื้นหลังของปุ่มค้นหาเป็นสีฟ้า */
          color: #ffffff;
          /* ปรับสีของข้อความในปุ่มเป็นสีขาว */
          padding: 5px 10px;
          /* ปรับการเรียงขนาดของปุ่ม */
          border-radius: 5px;
          /* ปรับรูปร่างของปุ่มเป็นรูปแบบวงกลม */
          border: none;
          /* ลบเส้นขอบของปุ่ม */
      }

      .gsc-search-button-v2 svg {
          fill: #A4A4A4;
          /* ปรับสีของไอคอนเป็นสีขาว */
          width: 18px;
          /* ปรับขนาดของไอคอนเป็น 15px */
          height: 18px;
          /* ปรับขนาดของไอคอนเป็น 15px */
      }

      .gsc-control-cse {
          background-color: transparent;
          border: none;
      }

      .gsc-search-button-v2:hover {
          /* background-color: #0056b3; */
          /* color: ; */
          /* ปรับสีของปุ่มเมื่อเม้าส์ hover */
      }

      /* ซ่อนข้อความ "เพิ่มประสิทธิภาพโดย Google" */
      .gsc-input-box .gsc-input {
          color: transparent;
      }

      /* เพิ่ม placeholder แทนข้อความ "เพิ่มประสิทธิภาพโดย Google" */
      .gsc-input-box::before {
          content: 'ค้นหา';
          color: #000;
          /* เปลี่ยนสีข้อความ placeholder ตามต้องการ */
          position: absolute;
          top: 12px;
          /* ปรับตำแหน่งตามต้องการ */
          left: 10px;
          /* ปรับตำแหน่งตามต้องการ */
          z-index: -1;
          /* สร้างข้อความ placeholder ให้อยู่ต่ำกว่า input */
      }


      .gsc-control {
          font-family: arial, sans-serif;
          background-color: lightblue !important;
          width: 309px;
          border-radius: 3rem;
          padding: 7px 20px !important;
      }

      .gsc-input {
          padding: 0px !important;
      }

      .gsc-input-box {
          border: 1px solid #dfe1e5;
          background: #fff;
          border-radius: 2rem;
          padding: 1px 10px;
          position: relative;
      }

      #gsc-i-id1 {
          color: #000 !important;
          line-height: 1.2 !important;
          background: none !important;
          font-size: 1rem !important;
      }

      .gsc-search-button-v2 {
          padding: 0.5rem !important;
          cursor: pointer;
          border-radius: 50%;
          position: absolute;
          margin-left: -45px;
          margin-top: -15px;
      }

      ul.no-bullets {
          list-style-type: none;
          /* ลบ bullet points */
          padding: 0;
          /* ลบ padding ของ ul */
          margin: 0;
          /* ลบ margin ของ ul */
      }

      ul.no-bullets a {
          display: block;
          /* ทำให้ลิงก์เป็นบล็อก */
          padding: 5px 0;
          /* กำหนด padding ตามต้องการ */
          margin: 0;
          /* ลบ margin ของลิงก์ */
          text-decoration: none;
          /* ลบขีดเส้นใต้ของลิงก์ */
          color: inherit;
          /* ทำให้สีของลิงก์สืบทอดจากพ่อแม่ */
      }

      /* แก้ไขส่วนนี้ในไฟล์ CSS ของคุณ */
      /* เพิ่มความเฉพาะเจาะจงให้กับ selector โดยไม่รวม .nav-background */
      #navbar2 ul>li>a:hover::after,
      #navbar2 ul>li.dropdown:hover .dropbtn::after {
          content: '';
          position: absolute;
          left: 0;
          right: 0;
          bottom: 20px;
          height: 2px;
          /* background: linear-gradient(180deg, #F9A602 2.64%, #F5C728 78.74%, #F5D033 97.76%); */
          background-color: #FFF33B;
          transform: scaleX(1);
          transition: transform 0.3s;
      }

      #navbar2 ul>li>a::after,
      #navbar2 ul>li.dropdown .dropbtn::after {
          content: '';
          position: absolute;
          left: 0;
          right: 0;
          bottom: 10px;
          height: 2px;
          background-color: transparent;
          transform: scaleX(0);
          transition: transform 0.3s;
      }

      /* เพิ่ม CSS ใหม่เพื่อยกเลิก effect สำหรับ .nav-background */
      .nav-background ul>li>a:hover::after,
      .nav-background ul>li.dropdown:hover .dropbtn::after,
      .nav-background ul>li>a::after,
      .nav-background ul>li.dropdown .dropbtn::after {
          content: none;
          background: none;
          transform: none;
          transition: none;
      }

      .nav-background {
          background-image: url('<?php echo base_url("docs/bt-headers.png"); ?>');
          height: 58px;
          width: 1273px;
          position: absolute;
          z-index: 5;
          margin-top: 30px;
          margin-left: 625px;
      }

      .nav-background ul {
          display: flex;
          list-style-type: none;
          padding: 0;
          margin: 0;
          justify-content: flex-start;
      }

      .nav-background .menu-item {
          position: relative;
          display: flex;
          align-items: center;
          justify-content: center;
          height: 50px;
          padding: 0 15px;
          transition: 0.3s ease;
      }

      .nav-background .menu-item a {
          color: #4A0D49;
          text-decoration: none;
          font-weight: bold;
          z-index: 2;
          white-space: nowrap;
      }

      .nav-background .menu-item a:hover {
          color: #91008F;
      }

      .nav-background .menu-item.dropdown:hover>a {
          color: #91008F !important;
      }

      .nav-background .menu-item.dropdown:hover .dropbtn {
          color: #91008F !important;
      }

      .nav-background .menu-item.dropdown:hover .dropdown-content {
          display: block;
      }

      .nav-background .menu-item::before {
          content: '';
          position: absolute;
          top: 0;
          left: 0;
          right: 0;
          bottom: 0;
          background-size: 100% 100%;
          background-repeat: no-repeat;
          opacity: 0;
          transition: opacity 0.3s ease;
          z-index: 1;
      }

      .nav-background .menu-item:hover::before {
          opacity: 1;
      }

      /* กำหนด background-image และขนาดสำหรับแต่ละ menu item */
      .nav-background .menu-item-1 {
          width: 144px;
      }

      .nav-background .menu-item-1::before {
          background-image: url('<?php echo base_url("docs/bt-header-1-hovers.png"); ?>');
      }

      .nav-background .menu-item-2 {
          width: 132px;
      }

      .nav-background .menu-item-2::before {
          background-image: url('<?php echo base_url("docs/bt-header-2-hovers.png"); ?>');
      }

      .nav-background .menu-item-3 {
          width: 132px;
      }

      .nav-background .menu-item-3::before {
          background-image: url('<?php echo base_url("docs/bt-header-3-hovers.png"); ?>');
      }

      .nav-background .menu-item-4 {
          width: 95px;
      }

      .nav-background .menu-item-4::before {
          background-image: url('<?php echo base_url("docs/bt-header-4-hovers.png"); ?>');
      }

      .nav-background .menu-item-5 {
          width: 112px;
      }

      .nav-background .menu-item-5::before {
          background-image: url('<?php echo base_url("docs/bt-header-5-hovers.png"); ?>');
      }

      .nav-background .menu-item-6 {
          width: 172px;
      }

      .nav-background .menu-item-6::before {
          background-image: url('<?php echo base_url("docs/bt-header-6-hovers.png"); ?>');
      }

      .nav-background .menu-item-7 {
          width: 190px;
      }

      .nav-background .menu-item-7::before {
          background-image: url('<?php echo base_url("docs/bt-header-7-hovers.png"); ?>');
      }

      .nav-background .menu-item-8 {
          width: 134px;
      }

      .nav-background .menu-item-8::before {
          background-image: url('<?php echo base_url("docs/bt-header-8-hovers.png"); ?>');
      }

      .nav-background .menu-item-9 {
          width: 154px;
      }

      .nav-background .menu-item-9::before {
          background-image: url('<?php echo base_url("docs/bt-header-9-hovers.png"); ?>');
      }

      .menu-link {
          color: #fff;
          text-decoration: none;
          transition: color 0.3s ease;
          /* เพิ่มการเปลี่ยนสีแบบ smooth */
      }

      .menu-link:hover {
          color: #fff;
      }

      .menu-link.active {
          color: #fff;
      }

      .dropdown-content a {
          display: flex !important;
          align-items: flex-start !important;
          text-decoration: none;
      }

      .dropdown-content a img {
          margin-right: 0px !important;
          /* ลดเป็น 0 เพราะมี &nbsp; อยู่แล้ว */
          margin-top: 8px !important;
          flex-shrink: 0;
      }

      .dropdown-content .font-nav {
          line-height: 1.4 !important;
          text-indent: -16px !important;
          /* เพิ่มตรงนี้ - ดึงบรรทัดแรกไปซ้าย */
          padding-left: 16px !important;
          /* เพิ่มตรงนี้ - เว้นบรรทัดที่ 2 */
      }
  </style>




  <style>
      /* ====================================
   USER DROPDOWN SYSTEM - COMPLETE CSS
   ==================================== */

      /* ========== BASE DROPDOWN CONTAINER ========== */
      .user-dropdown {
          position: relative;
          display: inline-block;
          z-index: 9999;
      }

      /* ========== USER INFO BUTTON ========== */
      .user-info {
          display: flex;
          align-items: center;
          color: #184C53;
          text-decoration: none;
          padding: 8px 12px;
          border-radius: 25px;
          background: rgba(255, 255, 255, 0.9);
          backdrop-filter: blur(10px);
          border: 1px solid rgba(255, 255, 255, 0.2);
          transition: all 0.3s cubic-bezier(0.25, 0.46, 0.45, 0.94);
          cursor: pointer;
          position: relative;
      }

      .user-info:hover {
          background: rgba(255, 255, 255, 1);
          transform: translateY(-2px);
          box-shadow: 0 5px 15px rgba(0, 0, 0, 0.1);
          text-decoration: none;
          color: #184C53;
      }

      /* ========== USER AVATAR ========== */
      .user-avatar {
          width: 35px;
          height: 35px;
          border-radius: 50%;
          margin-right: 10px;
          border: 2px solid rgba(255, 255, 255, 0.3);
          object-fit: cover;
          transition: all 0.3s ease;
      }

      .user-info:hover .user-avatar {
          border-color: rgba(255, 255, 255, 0.6);
          box-shadow: 0 2px 8px rgba(0, 0, 0, 0.1);
      }

      /* ========== USER DETAILS ========== */
      .user-details {
          display: flex;
          flex-direction: column;
          margin-right: 8px;
      }

      .user-name {
          font-weight: 600;
          font-size: 14px;
          line-height: 1.2;
          max-width: 150px;
          overflow: hidden;
          text-overflow: ellipsis;
          white-space: nowrap;
      }

      .user-type {
          font-size: 11px;
          opacity: 0.8;
          line-height: 1;
      }

      /* ========== DROPDOWN ARROW ========== */
      .dropdown-arrow {
          font-size: 12px;
          transition: transform 0.3s cubic-bezier(0.25, 0.46, 0.45, 0.94);
          margin-left: 4px;
      }

      .user-dropdown.active .dropdown-arrow {
          transform: rotate(180deg);
      }

      /* ========== SECURITY BADGE ========== */
      .security-badge {
          position: absolute;
          top: -8px;
          right: -8px;
          width: 24px;
          height: 24px;
          border-radius: 50%;
          border: 3px solid white;
          display: flex;
          align-items: center;
          justify-content: center;
          z-index: 10;
          transition: all 0.3s cubic-bezier(0.25, 0.46, 0.45, 0.94);
      }

      /* Security Badge States */
      .security-badge.verified {
          background: linear-gradient(135deg, #28a745, #20c997);
          box-shadow: 0 2px 8px rgba(40, 167, 69, 0.3);
          animation: pulse-verified 2s infinite;
      }

      .security-badge.trusted {
          background: linear-gradient(135deg, #ffc107, #ffb347);
          box-shadow: 0 2px 8px rgba(255, 193, 7, 0.3);
          animation: glow-trusted 3s infinite;
      }

      .security-badge.default {
          background: linear-gradient(135deg, #6c757d, #495057);
          box-shadow: 0 2px 8px rgba(108, 117, 125, 0.3);
      }

      .security-badge i {
          font-size: 10px;
          color: white;
      }

      .user-info:hover .security-badge {
          transform: scale(1.15);
      }

      /* ========== SECURITY BADGE ANIMATIONS ========== */
      @keyframes pulse-verified {
          0% {
              transform: scale(1);
              box-shadow: 0 2px 8px rgba(40, 167, 69, 0.3);
          }

          50% {
              transform: scale(1.1);
              box-shadow: 0 4px 15px rgba(40, 167, 69, 0.5);
          }

          100% {
              transform: scale(1);
              box-shadow: 0 2px 8px rgba(40, 167, 69, 0.3);
          }
      }

      @keyframes glow-trusted {
          0% {
              box-shadow: 0 2px 8px rgba(255, 193, 7, 0.3);
          }

          50% {
              box-shadow: 0 4px 15px rgba(255, 193, 7, 0.6), 0 0 20px rgba(255, 193, 7, 0.3);
          }

          100% {
              box-shadow: 0 2px 8px rgba(255, 193, 7, 0.3);
          }
      }

      /* ========== DROPDOWN MENU CONTAINER ========== */
      .user-dropdown-menu {
          position: absolute;
          top: 100%;
          right: 0;
          background: white;
          border-radius: 15px;
          box-shadow: 0 10px 30px rgba(0, 0, 0, 0.2);
          min-width: 220px;
          z-index: 99999;
          opacity: 0;
          visibility: hidden;
          transform: translateY(-10px);
          transition: all 0.4s cubic-bezier(0.25, 0.46, 0.45, 0.94);
          border: 1px solid rgba(0, 0, 0, 0.1);
          overflow: hidden;
      }

      .user-dropdown.active .user-dropdown-menu {
          opacity: 1;
          visibility: visible;
          transform: translateY(5px);
      }

      /* ========== DROPDOWN HEADER ========== */
      .dropdown-header {
          padding: 15px;
          background: linear-gradient(135deg, #f8f9fa, #e9ecef);
          border-bottom: 1px solid #dee2e6;
      }

      .dropdown-user-info {
          display: flex;
          align-items: center;
      }

      .dropdown-avatar {
          width: 45px;
          height: 45px;
          border-radius: 50%;
          margin-right: 12px;
          border: 2px solid #fff;
          box-shadow: 0 2px 8px rgba(0, 0, 0, 0.1);
          object-fit: cover;
      }

      .dropdown-user-details h6 {
          margin: 0;
          font-size: 14px;
          font-weight: 600;
          color: #333;
      }

      .dropdown-user-details p {
          margin: 0;
          font-size: 12px;
          color: #666;
          line-height: 1.3;
      }

      /* ========== SECURITY STATUS INFO ========== */
      .security-status-info {
          display: flex;
          align-items: center;
          background: linear-gradient(135deg, #f8f9fa, #e9ecef);
          color: #333;
          padding: 10px 15px;
          margin: 10px 15px;
          border-radius: 10px;
          font-size: 11px;
          border-left: 4px solid #28a745;
          transition: all 0.3s ease;
      }

      .security-status-info.trusted {
          border-left-color: #ffc107;
          background: linear-gradient(135deg, #fff9c4, #fff3c4);
      }

      .security-status-info.default {
          border-left-color: #6c757d;
          background: linear-gradient(135deg, #f1f3f4, #e9ecef);
      }

      .security-status-info i {
          margin-right: 8px;
          font-size: 12px;
      }

      /* ========== DROPDOWN MENU ITEMS ========== */
      .dropdown-menu-items {
          padding: 12px 0;
          background: linear-gradient(135deg, #fafbfc 0%, #f8f9fb 100%);
          border-radius: 0 0 15px 15px;
          box-shadow:
              inset 0 1px 0 rgba(255, 255, 255, 0.8),
              0 1px 3px rgba(0, 0, 0, 0.05);
      }

      .dropdown-item {
          display: flex;
          align-items: center;
          padding: 14px 20px;
          color: #2c3e50;
          text-decoration: none;
          font-size: 14px;
          font-weight: 500;
          border: none;
          background: transparent;
          transition: all 0.3s cubic-bezier(0.25, 0.46, 0.45, 0.94);
          position: relative;
          margin: 2px 8px;
          border-radius: 10px;
          overflow: hidden;
          opacity: 0;
          animation: slideInItem 0.4s ease forwards;
      }

      /* ========== DROPDOWN ITEM ICONS ========== */
      .dropdown-item i {
          width: 20px;
          height: 20px;
          margin-right: 12px;
          text-align: center;
          font-size: 16px;
          display: flex;
          align-items: center;
          justify-content: center;
          transition: all 0.3s ease;
      }

      /* Icon Colors */
      .dropdown-item i.fa-cogs {
          color: #3498db;
      }

      .dropdown-item i.fa-building {
          color: #9b59b6;
      }

      .dropdown-item i.fa-user,
      .dropdown-item i.fa-user-cog {
          color: #2ecc71;
      }

      .dropdown-item i.fa-shield-alt {
          color: #e67e22;
      }

      .dropdown-item i.fa-sign-out-alt {
          color: #e74c3c;
      }

      /* ========== DROPDOWN ITEM HOVER EFFECTS ========== */
      .dropdown-item:hover {
          background: linear-gradient(135deg, rgba(52, 152, 219, 0.08), rgba(52, 152, 219, 0.12));
          color: #2980b9;
          text-decoration: none;
          transform: translateX(8px);
          box-shadow: 0 4px 12px rgba(52, 152, 219, 0.15);
      }

      .dropdown-item:hover i.fa-cogs {
          color: #2980b9;
          transform: rotate(90deg);
      }

      .dropdown-item:hover i.fa-building {
          color: #8e44ad;
          transform: scale(1.1);
      }

      .dropdown-item:hover i.fa-user,
      .dropdown-item:hover i.fa-user-cog {
          color: #27ae60;
          transform: scale(1.1);
      }

      .dropdown-item:hover i.fa-shield-alt {
          color: #d35400;
          animation: shield-pulse 0.6s ease-in-out;
      }

      /* ========== LOGOUT BUTTON SPECIAL STYLES ========== */
      .dropdown-item.logout {
          margin-top: 8px;
          border-top: 1px solid rgba(231, 76, 60, 0.1);
          background: linear-gradient(135deg, rgba(255, 255, 255, 0.8), rgba(254, 249, 249, 0.9));
          color: #c0392b;
          font-weight: 600;
          position: relative;
      }

      .dropdown-item.logout::before {
          content: '';
          position: absolute;
          left: 0;
          top: 0;
          height: 100%;
          width: 4px;
          background: linear-gradient(135deg, #e74c3c, #c0392b);
          transform: scaleY(0);
          transition: transform 0.3s ease;
          border-radius: 0 2px 2px 0;
      }

      .dropdown-item.logout:hover {
          background: linear-gradient(135deg, rgba(231, 76, 60, 0.08), rgba(231, 76, 60, 0.12));
          color: #a93226;
          box-shadow: 0 4px 12px rgba(231, 76, 60, 0.15);
      }

      .dropdown-item.logout:hover::before {
          transform: scaleY(1);
      }

      .dropdown-item.logout:hover i.fa-sign-out-alt {
          color: #a93226;
          animation: logout-bounce 0.5s ease-in-out;
      }

      /* ========== SPECIAL ANIMATIONS ========== */
      @keyframes shield-pulse {
          0% {
              transform: scale(1);
          }

          50% {
              transform: scale(1.15);
          }

          100% {
              transform: scale(1.1);
          }
      }

      @keyframes logout-bounce {
          0% {
              transform: translateX(0);
          }

          25% {
              transform: translateX(-3px);
          }

          50% {
              transform: translateX(2px);
          }

          75% {
              transform: translateX(-1px);
          }

          100% {
              transform: translateX(0);
          }
      }

      @keyframes slideInItem {
          from {
              opacity: 0;
              transform: translateX(-20px);
          }

          to {
              opacity: 1;
              transform: translateX(0);
          }
      }

      /* ========== DROPDOWN ITEM ANIMATION DELAYS ========== */
      .dropdown-menu-items .dropdown-item:nth-child(1) {
          animation-delay: 0.1s;
      }

      .dropdown-menu-items .dropdown-item:nth-child(2) {
          animation-delay: 0.15s;
      }

      .dropdown-menu-items .dropdown-item:nth-child(3) {
          animation-delay: 0.2s;
      }

      .dropdown-menu-items .dropdown-item:nth-child(4) {
          animation-delay: 0.25s;
      }

      .dropdown-menu-items .dropdown-item:nth-child(5) {
          animation-delay: 0.3s;
      }

      .dropdown-menu-items .dropdown-item:nth-child(6) {
          animation-delay: 0.35s;
      }

      /* ========== RIPPLE EFFECT ========== */
      .dropdown-item::after {
          content: '';
          position: absolute;
          top: 50%;
          left: 50%;
          width: 0;
          height: 0;
          border-radius: 50%;
          background: rgba(255, 255, 255, 0.4);
          transform: translate(-50%, -50%);
          transition: width 0.6s, height 0.6s;
      }

      .dropdown-item:active::after {
          width: 300px;
          height: 300px;
      }

      /* ========== STATUS BADGES ========== */
      .dropdown-item .status-badge {
          margin-left: auto;
          padding: 2px 8px;
          background: linear-gradient(135deg, #2ecc71, #27ae60);
          color: white;
          font-size: 10px;
          border-radius: 10px;
          font-weight: 600;
          letter-spacing: 0.5px;
          text-transform: uppercase;
      }

      .dropdown-item .status-badge.warning {
          background: linear-gradient(135deg, #f39c12, #e67e22);
      }

      .dropdown-item .status-badge.danger {
          background: linear-gradient(135deg, #e74c3c, #c0392b);
      }

      /* ========== SEPARATOR LINES ========== */
      .dropdown-item:not(:last-child):not(.logout) {
          border-bottom: 1px solid rgba(236, 240, 244, 0.6);
      }

      /* ========== FOCUS STATES FOR ACCESSIBILITY ========== */
      .dropdown-item:focus {
          outline: none;
          background: linear-gradient(135deg, rgba(52, 152, 219, 0.1), rgba(52, 152, 219, 0.15));
          box-shadow: 0 0 0 2px rgba(52, 152, 219, 0.3);
      }

      .dropdown-item.logout:focus {
          background: linear-gradient(135deg, rgba(231, 76, 60, 0.1), rgba(231, 76, 60, 0.15));
          box-shadow: 0 0 0 2px rgba(231, 76, 60, 0.3);
      }

      /* ========== RESPONSIVE DESIGN ========== */
      @media (max-width: 768px) {
          .user-name {
              max-width: 100px;
              font-size: 13px;
          }

          .user-dropdown-menu {
              right: -10px;
              min-width: 200px;
          }

          .dropdown-menu-items {
              padding: 8px 0;
          }

          .dropdown-item {
              padding: 12px 16px;
              margin: 1px 4px;
              font-size: 13px;
          }

          .dropdown-item i {
              width: 18px;
              height: 18px;
              font-size: 14px;
              margin-right: 10px;
          }

          .dropdown-item:hover {
              transform: translateX(4px);
          }

          .security-status-info {
              margin: 8px 10px;
              padding: 8px 12px;
              font-size: 10px;
          }
      }

      @media (max-width: 480px) {
          .user-dropdown-menu {
              right: -15px;
              min-width: 180px;
          }

          .dropdown-header {
              padding: 12px;
          }

          .dropdown-avatar {
              width: 40px;
              height: 40px;
          }
      }

      .user-dropdown,
      .user-info,
      .user-dropdown-menu,
      .dropdown-item {
          -webkit-font-smoothing: antialiased;
          -moz-osx-font-smoothing: grayscale;
          transform: translateZ(0);
          backface-visibility: hidden;
          will-change: transform, opacity;
      }



      /* เพิ่ม CSS เฉพาะสำหรับ navbar ด้านล่าง */
      .nav-background .user-dropdown {
          position: relative;
          display: inline-block;
          z-index: 99999;
      }

      .nav-background .user-info {
          display: flex;
          align-items: center;
          color: #4A0D49;
          text-decoration: none;
          padding: 8px 12px;
          border-radius: 25px;
          background: rgba(255, 255, 255, 0.7);
          backdrop-filter: blur(10px);
          border: 1px solid rgba(255, 255, 255, 0.2);
          transition: all 0.3s cubic-bezier(0.25, 0.46, 0.45, 0.94);
          cursor: pointer;
          position: relative;
      }

      .nav-background .user-info:hover {
          background: rgba(255, 255, 255, 0.9);
          color: #91008F;
          text-decoration: none;
      }

      .nav-background .user-dropdown-menu {
          position: absolute;
          top: 100%;
          right: 0;
          background: white;
          border-radius: 15px;
          box-shadow: 0 15px 35px rgba(0, 0, 0, 0.25);
          min-width: 220px;
          z-index: 999999;
          opacity: 0;
          visibility: hidden;
          transform: translateY(-10px);
          transition: all 0.4s cubic-bezier(0.25, 0.46, 0.45, 0.94);
      }

      .nav-background .user-dropdown.active .user-dropdown-menu {
          opacity: 1;
          visibility: visible;
          transform: translateY(5px);
      }


      .nav-background .menu-item-9 .user-info {
          width: 155px;
          height: 47px;
          border-radius: 0px 22.5px 22.5px 0px;
          overflow: hidden;
          /* ป้องกันเนื้อหาล้น */
      }

      .nav-background .menu-item-9 .user-name {
          font-size: 12px;
          max-width: 90px;
          /* จำกัดความกว้างสูงสุด */
          overflow: hidden;
          text-overflow: ellipsis;
          /* แสดง ... เมื่อข้อความยาวเกิน */
          white-space: nowrap;
          /* ไม่ให้ข้อความขึ้นบรรทัดใหม่ */
      }

      .nav-background .menu-item-9 .user-type {
          font-size: 9px;
          max-width: 90px;
          overflow: hidden;
          text-overflow: ellipsis;
          white-space: nowrap;
      }

      .nav-background .menu-item-9 .user-details {
          margin-right: 4px;
          flex: 1;
          /* ให้ยืดหยุ่นตามพื้นที่ที่เหลือ */
          min-width: 0;
          /* ป้องกันการขยายเกินขอบเขต */
      }

      .nav-background .menu-item-9 .user-avatar {
          margin-right: 6px;
          width: 30px;
          height: 30px;
          flex-shrink: 0;
          /* ไม่ให้ avatar หดตัว */
      }

      .nav-background .menu-item-9 .dropdown-arrow {
          flex-shrink: 0;
          /* ไม่ให้ลูกศรหดตัว */
      }
  </style>


  <nav class="navbar navbar2 navbar-expand-lg navbar-dark navbar-center sticky-top" id="navbar2">
      <ul>
          <li style="margin-left: 55px;"> <a href="<?php echo site_url('Home'); ?>" class="menu-link">หน้าหลัก</a></li>
          <li class="dropdown">
              <a href="javascript:void(0)" class="dropbtn menu-link">ข้อมูลทั่วไป</a>
              <?php
                $menu_type = 'general';
                include VIEWPATH . 'frontend_templat/dropdown_menu.php';
                ?>
          </li>

		  <?php
            // ดึงข้อมูลประเภทตำแหน่งจาก helper function
            $position_types = get_position_types('show'); // เฉพาะที่แสดง

            // เพิ่มแผนผังโครงสร้างรวมเข้าไปด้วย
            $all_structure_items = array_merge([
                (object)[
                    'type' => 'main',
                    'peng' => 'site_map',
                    'pname' => 'แผนผังโครงสร้างรวม',
                    'pstatus' => 'show',
                    'psub' => 0 // ไม่เป็น sub item
                ]
            ], $position_types);

            // แบ่งรายการออกเป็นคอลัมน์โดยคำนึงถึง sub items
            $structure_columns = [];
            $columns_count = 2;
            $current_column = 0;
            $items_in_current_column = 0;
            $items_per_column = ceil(count($all_structure_items) / $columns_count);

            // เริ่มต้นคอลัมน์
            $structure_columns[$current_column] = [];

            foreach ($all_structure_items as $index => $item) {
                $is_sub_item = isset($item->psub) && $item->psub == 1;

                // ตรวจสอบว่าควรขึ้นคอลัมน์ใหม่หรือไม่
                // ขึ้นคอลัมน์ใหม่ก็ต่อเมื่อ:
                // 1. จำนวนรายการในคอลัมน์ปัจจุบันครบตามที่กำหนด
                // 2. รายการปัจจุบันไม่ใช่ sub item (เพราะ sub item ต้องอยู่กับ main item)
                // 3. ยังมีคอลัมน์เหลือ
                if (
                    $items_in_current_column >= $items_per_column &&
                    !$is_sub_item &&
                    $current_column < $columns_count - 1
                ) {

                    $current_column++;
                    $structure_columns[$current_column] = [];
                    $items_in_current_column = 0;
                }

                // เพิ่มรายการลงในคอลัมน์ปัจจุบัน
                $structure_columns[$current_column][] = $item;
                $items_in_current_column++;
            }
            ?>

              <!-- แทนที่ส่วน dropdown โครงสร้างเดิมด้วยโค้ดนี้ -->
          <li class="dropdown" style="margin-left: 15px;">
                  <a href="javascript:void(0)" class="dropbtn menu-link">โครงสร้าง</a>
                  <?php
                $menu_type = 'personnel';
                include VIEWPATH . 'frontend_templat/dropdown_menu.php';
                ?>
              </li>
          <li class="dropdown" style="margin-left: 15px;">
              <a href="javascript:void(0)" class="dropbtn menu-link">บริการประชาชน</a>
              <?php
                $menu_type = 'service';
                include VIEWPATH . 'frontend_templat/dropdown_menu.php';
                ?>
          </li>
          <li class="dropdown" style="margin-left: 15px;">
              <a href="javascript:void(0)" class="dropbtn menu-link">แผนงาน</a>
              <?php
                $menu_type = 'plan';
                include VIEWPATH . 'frontend_templat/dropdown_menu.php';
                ?>
          </li>
          <li class="dropdown" style="margin-left: 15px;">
              <a href="javascript:void(0)" class="dropbtn menu-link">การดำเนินงาน</a>
              <?php
                $menu_type = 'operation';
                include VIEWPATH . 'frontend_templat/dropdown_menu.php';
                ?>
          </li>
          <li class="dropdown" style="margin-left: 15px;">
              <a href="javascript:void(0)" class="dropbtn menu-link">มาตรการภายใน</a>
              <?php
                $menu_type = 'internal';
                include VIEWPATH . 'frontend_templat/dropdown_menu.php';
                ?>
          </li>
          <li style="margin-left: 15px;"><a href="<?php echo site_url('Pages/all_web'); ?>" class="menu-link">ผังเว็บไซต์</a></li>
          <!-- <li style="margin-left: 15px;"><a href="<?php echo site_url('Home/login'); ?>" class="menu-link" target="_blank">เข้าสู่ระบบ</a></li> -->


          <li style="margin-left: 15px;">
              <?php if ($is_logged_in): ?>
                  <!-- แสดงข้อมูลผู้ใช้เมื่อเข้าสู่ระบบแล้ว -->
                  <div class="user-dropdown" id="userDropdown" style="margin-top: 5px;">
                      <div class="user-info" onclick="toggleUserDropdown()">
                          <?php
                            // กำหนด default avatar เป็น SVG data URI
                            $default_avatar_svg = 'data:image/svg+xml;base64,' . base64_encode('
<svg width="40" height="40" viewBox="0 0 40 40" fill="none" xmlns="http://www.w3.org/2000/svg">
    <circle cx="20" cy="20" r="20" fill="#E5E7EB"/>
    <circle cx="20" cy="16" r="6" fill="#9CA3AF"/>
    <path d="M8 32c0-6.627 5.373-12 12-12s12 5.373 12 12" fill="#9CA3AF"/>
</svg>');

                            $avatar_url = $default_avatar_svg; // ค่าเริ่มต้นเป็น SVG
                            if (!empty($user_info['img'])) {
                                $avatar_url = base_url('docs/img/avatar/' . $user_info['img']);
                            }
                            ?>

                          <img src="<?php echo $avatar_url; ?>"
                              alt="User Avatar"
                              class="user-avatar">

                          <div class="user-details">
                              <span class="user-name"><?php echo htmlspecialchars($user_info['name']); ?></span>
                              <span class="user-type"><?php echo $user_info['login_type']; ?></span>
                          </div>
                          <i class="fas fa-chevron-down dropdown-arrow"></i>
                      </div>

                      <!-- Dropdown Menu -->
                      <div class="user-dropdown-menu">
                          <div class="dropdown-header">
                              <div class="dropdown-user-info">
                                  <img src="<?php echo $avatar_url; ?>"
                                      alt="User Avatar"
                                      class="dropdown-avatar">
                                  <div class="dropdown-user-details">
                                      <h6><?php echo htmlspecialchars($user_info['name']); ?></h6>
                                      <p>
                                          <?php echo $user_info['login_type']; ?>
                                          <?php if ($user_type === 'public' && !empty($user_info['email'])): ?>
                                              <br><small><?php echo htmlspecialchars($user_info['email']); ?></small>
                                          <?php elseif ($user_type === 'staff' && !empty($user_info['username'])): ?>
                                              <br><small><?php echo htmlspecialchars($user_info['username']); ?></small>
                                          <?php endif; ?>
                                      </p>
                                  </div>
                              </div>
                          </div>

                          <!-- แสดงสถานะความปลอดภัย -->
                          <?php if ($this->session->userdata('2fa_verified')): ?>
                              <div class="security-status-info">
                                  <i class="fas fa-shield-alt"></i>
                                  ยืนยันตัวตน 2FA แล้ว
                              </div>
                          <?php elseif ($this->session->userdata('trusted_device')): ?>
                              <div class="security-status-info trusted">
                                  <i class="fas fa-check"></i>
                                  อุปกรณ์ที่เชื่อถือได้
                              </div>
                          <?php else: ?>
                              <div class="security-status-info default">
                                  <i class="fas fa-exclamation-triangle"></i>
                                  แนะนำให้ยืนยันตัวตนเพื่อความปลอดภัย
                              </div>
                          <?php endif; ?>

                          <div class="dropdown-menu-items">

                              <?php if ($user_type === 'public'): ?>
                                  <a href="<?php echo site_url('Pages/service_systems'); ?>" class="dropdown-item">
                                      <i class="fas fa-cogs"></i> บริการออนไลน์
                                  </a>
                                  <a href="<?php echo site_url('Auth_public_mem/profile'); ?>" class="dropdown-item">
                                      <i class="fas fa-user"></i> จัดการโปรไฟล์
                                  </a>

                                  <a href="<?php echo site_url('Auth_public_mem/profile'); ?>#security-section" class="dropdown-item">
                                      <i class="fas fa-shield-alt"></i> การรักษาความปลอดภัย
                                  </a>




                              <?php elseif ($user_type === 'staff'): ?>
                                  <a href="<?php echo site_url('User/choice'); ?>" class="dropdown-item">
                                      <i class="fas fa-building"></i> สมาร์ทออฟฟิศ
                                  </a>
                                  <a href="<?php echo site_url('System_admin/user_profile'); ?>" class="dropdown-item">
                                      <i class="fas fa-user-cog"></i> จัดการโปรไฟล์

                                  </a>
                                  <a href="<?php echo site_url('System_admin/user_profile'); ?>#security-section" class="dropdown-item">
                                      <i class="fas fa-shield-alt"></i> การรักษาความปลอดภัย
                                  </a>


                              <?php endif; ?>

                              <!-- ปุ่มออกจากระบบ -->
                              <a href="javascript:void(0);" onclick="confirmLogout()" class="dropdown-item logout">
                                  <i class="fas fa-sign-out-alt"></i> ออกจากระบบ
                              </a>
                          </div>
                      </div>
                  </div>
              <?php else: ?>
                  <!-- แสดงปุ่มเข้าสู่ระบบเมื่อยังไม่ได้เข้าสู่ระบบ -->
                  <a href="<?php echo site_url('User'); ?>" class="menu-link" target="_blank">เข้าสู่ระบบ</a>
              <?php endif; ?>
          </li>
      </ul>
  </nav>


  <script>
      document.addEventListener('DOMContentLoaded', function() {
          const dropdowns = document.querySelectorAll('.dropdown');

          dropdowns.forEach(dropdown => {
              const link = dropdown.querySelector('.menu-link');
              const dropdownContent = dropdown.querySelector('.dropdown-content');

              if (link && dropdownContent) {
                  link.addEventListener('mouseenter', function() {
                      dropdownContent.style.display = 'block';
                      link.classList.add('active');
                  });

                  dropdown.addEventListener('mouseleave', function() {
                      dropdownContent.style.display = 'none';
                      link.classList.remove('active');
                  });
              }
          });
      });

      window.onscroll = function() {
          scrollFunction();
      };

      function scrollFunction() {
          if (window.innerWidth > 768) { // ตรวจสอบว่าขนาดหน้าจอไม่ใช่ขนาดมือถือและเล็กว่า 1200px
              if (document.body.scrollTop > 10 || document.documentElement.scrollTop > 10) {
                  document.getElementById("navbar2").style.display = "block";
              } else {
                  document.getElementById("navbar2").style.display = "none";
              }
          }
      }

      // เปลี่ยนคำเป็นคำว่า ค้นหา
      window.onload = function() {
          var placeHolderText = "ค้นหา";
          var searchBox = document.querySelector("#gsc-i-id1");
          var searchButton = document.querySelector(".gsc-search-button-v2 svg title");
          searchBox.placeholder = placeHolderText;
          searchBox.title = placeHolderText;
          searchButton.innerHTHL = placeHolderText;
      }

      // ค้นหาซ่อน / แสดงผล
      function toggleSearch() {
          var searchContainer = document.getElementById('searchContainer');
          var searchImage = document.getElementById('searchImage');

          if (searchContainer.style.display === 'none') {
              searchContainer.style.display = 'block'; // แสดง div
              searchImage.style.display = 'none'; // ซ่อนรูป
          } else {
              searchContainer.style.display = 'none'; // ซ่อน div
              searchImage.style.display = 'block'; // แสดงรูป
          }
      }

      function changeImage(imageUrl) {
          document.getElementById('searchImage').src = imageUrl;
      }

      function restoreImage(imageUrl) {
          document.getElementById('searchImage').src = imageUrl;
      }
  </script>


  <div class="d-flex justify-content-start" style="position: absolute; margin: 25px;">
      <!-- Logo -->
      <div style="z-index: 101; margin-right: 20px;">
          <img class="logo-animate" style="margin-left: 10px;" src="<?php echo base_url('docs/logo-nav.png'); ?>" width="120" height="120"><br>
      </div>

      <!-- ชื่อหน่วยงาน -->
      <div style="margin-top: 10px; z-index: 5; flex: 1;">
          <?php
            // ใช้ fname สำหรับเทศบาล, abbreviation+nname สำหรับอื่นๆ
            $abbreviation = get_config_value('abbreviation');
            $is_municipal = in_array($abbreviation, ['เทศบาล', 'เทศบาลตำบล', 'เทศบาลเมือง', 'เทศบาลนคร']);
            $display_name = $is_municipal ? get_config_value('fname') : get_config_value('abbreviation') . get_config_value('nname');
            ?>
          <span class="font-head-navbar-letf-logo1" style="margin-left: 20px;"><?php echo $display_name; ?></span><br>
          <span class="font-head-navbar-letf-logo2" style="margin-left: 20px;">อ.<?php echo get_config_value('district'); ?> จ.<?php echo get_config_value('province'); ?></span>
      </div>
  </div>


  <div class="nav-background">
      <div style="position: absolute; margin-top: 0px; z-index: 100;">
          <ul style="font-size: 24px;">
              <li class="menu-item menu-item-1"><a href="<?php echo site_url('Home'); ?>">หน้าหลัก</a></li>
              <li class="menu-item menu-item-2 dropdown">
                  <a href="javascript:void(0)">ข้อมูลทั่วไป</a>
                  <?php
                    $menu_type = 'general';
                    $dropdown_style = 'style="margin-top: 80px;"'; // ใส่ style เพิ่ม
                    include VIEWPATH . 'frontend_templat/dropdown_menu.php';
                    ?>
              </li>

			  <?php
            // ดึงข้อมูลประเภทตำแหน่งจาก helper function
            $position_types = get_position_types('show'); // เฉพาะที่แสดง

            // เพิ่มแผนผังโครงสร้างรวมเข้าไปด้วย
            $all_structure_items = array_merge([
                (object)[
                    'type' => 'main',
                    'peng' => 'site_map',
                    'pname' => 'แผนผังโครงสร้างรวม',
                    'pstatus' => 'show',
                    'psub' => 0 // ไม่เป็น sub item
                ]
            ], $position_types);

            // แบ่งรายการออกเป็นคอลัมน์โดยคำนึงถึง sub items
            $structure_columns = [];
            $columns_count = 2;
            $current_column = 0;
            $items_in_current_column = 0;
            $items_per_column = ceil(count($all_structure_items) / $columns_count);

            // เริ่มต้นคอลัมน์
            $structure_columns[$current_column] = [];

            foreach ($all_structure_items as $index => $item) {
                $is_sub_item = isset($item->psub) && $item->psub == 1;

                // ตรวจสอบว่าควรขึ้นคอลัมน์ใหม่หรือไม่
                // ขึ้นคอลัมน์ใหม่ก็ต่อเมื่อ:
                // 1. จำนวนรายการในคอลัมน์ปัจจุบันครบตามที่กำหนด
                // 2. รายการปัจจุบันไม่ใช่ sub item (เพราะ sub item ต้องอยู่กับ main item)
                // 3. ยังมีคอลัมน์เหลือ
                if (
                    $items_in_current_column >= $items_per_column &&
                    !$is_sub_item &&
                    $current_column < $columns_count - 1
                ) {

                    $current_column++;
                    $structure_columns[$current_column] = [];
                    $items_in_current_column = 0;
                }

                // เพิ่มรายการลงในคอลัมน์ปัจจุบัน
                $structure_columns[$current_column][] = $item;
                $items_in_current_column++;
            }
            ?>

              <!-- แทนที่ส่วน dropdown โครงสร้างเดิมด้วยโค้ดนี้ -->
              <li class="menu-item menu-item-3 dropdown">
                  <a href="javascript:void(0)">โครงสร้าง</a>
                  <?php
                    $menu_type = 'personnel';
                    $dropdown_style = 'style="margin-top: 80px;"'; // ใส่ style เพิ่ม
                    include VIEWPATH . 'frontend_templat/dropdown_menu.php';
                    ?>
              </li>
              <li class="menu-item menu-item-4 dropdown">
                  <a href="javascript:void(0)">บริการ</a>
                  <?php
                    $menu_type = 'service';
                    $dropdown_style = 'style="margin-top: 80px;"'; // ใส่ style เพิ่ม
                    include VIEWPATH . 'frontend_templat/dropdown_menu.php';
                    ?>
              </li>
              <li class="menu-item menu-item-5 dropdown">
                  <a href="javascript:void(0)">แผนงาน</a>
                  <?php
                    $menu_type = 'plan';
                    $dropdown_style = 'style="margin-top: 80px;"'; // ใส่ style เพิ่ม
                    include VIEWPATH . 'frontend_templat/dropdown_menu.php';
                    ?>
              </li>
              <li class="menu-item menu-item-6 dropdown">
                  <a href="javascript:void(0)">การดำเนินงาน</a>
                  <?php
                    $menu_type = 'operation';
                    $dropdown_style = 'style="margin-top: 80px;"'; // ใส่ style เพิ่ม
                    include VIEWPATH . 'frontend_templat/dropdown_menu.php';
                    ?>
              </li>
              <li class="menu-item menu-item-7 dropdown">
                  <a href="javascript:void(0)">มาตรการภายใน</a>
                  <?php
                    $menu_type = 'internal';
                    $dropdown_style = 'style="margin-top: 80px;"'; // ใส่ style เพิ่ม
                    include VIEWPATH . 'frontend_templat/dropdown_menu.php';
                    ?>
              </li>
              <li class="menu-item menu-item-8"><a href="<?php echo site_url('Pages/all_web'); ?>">ผังเว็บไซต์</a></li>
              <!-- <li class="menu-item menu-item-9"><a href="<?php echo site_url('Home/login'); ?>" target="_blank">เข้าสู่ระบบ</a></li> -->

              <li class="menu-item menu-item-9">
                  <?php if ($is_logged_in): ?>
                      <!-- แสดงข้อมูลผู้ใช้เมื่อเข้าสู่ระบบแล้ว -->
                      <div class="user-dropdown" id="userDropdown">
                          <div class="user-info" onclick="toggleUserDropdown()">
                              <?php
                                // กำหนด default avatar เป็น SVG data URI
                                $default_avatar_svg = 'data:image/svg+xml;base64,' . base64_encode('
<svg width="40" height="40" viewBox="0 0 40 40" fill="none" xmlns="http://www.w3.org/2000/svg">
    <circle cx="20" cy="20" r="20" fill="#E5E7EB"/>
    <circle cx="20" cy="16" r="6" fill="#9CA3AF"/>
    <path d="M8 32c0-6.627 5.373-12 12-12s12 5.373 12 12" fill="#9CA3AF"/>
</svg>');

                                $avatar_url = $default_avatar_svg; // ค่าเริ่มต้นเป็น SVG
                                if (!empty($user_info['img'])) {
                                    $avatar_url = base_url('docs/img/avatar/' . $user_info['img']);
                                }
                                ?>

                              <img src="<?php echo $avatar_url; ?>"
                                  alt="User Avatar"
                                  class="user-avatar">

                              <div class="user-details">
                                  <span class="user-name"><?php echo htmlspecialchars($user_info['name']); ?></span>
                                  <span class="user-type"><?php echo $user_info['login_type']; ?></span>
                              </div>
                              <i class="fas fa-chevron-down dropdown-arrow"></i>
                          </div>

                          <!-- Dropdown Menu -->
                          <div class="user-dropdown-menu">
                              <div class="dropdown-header">
                                  <div class="dropdown-user-info">
                                      <img src="<?php echo $avatar_url; ?>"
                                          alt="User Avatar"
                                          class="dropdown-avatar">
                                      <div class="dropdown-user-details">
                                          <h6><?php echo htmlspecialchars($user_info['name']); ?></h6>
                                          <p>
                                              <?php echo $user_info['login_type']; ?>
                                              <?php if ($user_type === 'public' && !empty($user_info['email'])): ?>
                                                  <br><small><?php echo htmlspecialchars($user_info['email']); ?></small>
                                              <?php elseif ($user_type === 'staff' && !empty($user_info['username'])): ?>
                                                  <br><small><?php echo htmlspecialchars($user_info['username']); ?></small>
                                              <?php endif; ?>
                                          </p>
                                      </div>
                                  </div>
                              </div>

                              <!-- แสดงสถานะความปลอดภัย -->
                              <?php if ($this->session->userdata('2fa_verified')): ?>
                                  <div class="security-status-info">
                                      <i class="fas fa-shield-alt"></i>
                                      ยืนยันตัวตน 2FA แล้ว
                                  </div>
                              <?php elseif ($this->session->userdata('trusted_device')): ?>
                                  <div class="security-status-info trusted">
                                      <i class="fas fa-check"></i>
                                      อุปกรณ์ที่เชื่อถือได้
                                  </div>
                              <?php else: ?>
                                  <div class="security-status-info default">
                                      <i class="fas fa-exclamation-triangle"></i>
                                      แนะนำให้ยืนยันตัวตนเพื่อความปลอดภัย
                                  </div>
                              <?php endif; ?>

                              <div class="dropdown-menu-items">

                                  <?php if ($user_type === 'public'): ?>
                                      <a href="<?php echo site_url('Pages/service_systems'); ?>" class="dropdown-item">
                                          <i class="fas fa-cogs"></i> บริการออนไลน์
                                      </a>
                                      <a href="<?php echo site_url('Auth_public_mem/profile'); ?>" class="dropdown-item">
                                          <i class="fas fa-user"></i> จัดการโปรไฟล์
                                      </a>

                                      <a href="<?php echo site_url('Auth_public_mem/profile'); ?>#security-section" class="dropdown-item">
                                          <i class="fas fa-shield-alt"></i> การรักษาความปลอดภัย
                                      </a>




                                  <?php elseif ($user_type === 'staff'): ?>
                                      <a href="<?php echo site_url('User/choice'); ?>" class="dropdown-item">
                                          <i class="fas fa-building"></i> สมาร์ทออฟฟิศ
                                      </a>
                                      <a href="<?php echo site_url('System_admin/user_profile'); ?>" class="dropdown-item">
                                          <i class="fas fa-user-cog"></i> จัดการโปรไฟล์

                                      </a>
                                      <a href="<?php echo site_url('System_admin/user_profile'); ?>#security-section" class="dropdown-item">
                                          <i class="fas fa-shield-alt"></i> การรักษาความปลอดภัย
                                      </a>


                                  <?php endif; ?>

                                  <!-- ปุ่มออกจากระบบ -->
                                  <a href="javascript:void(0);" onclick="confirmLogout()" class="dropdown-item logout">
                                      <i class="fas fa-sign-out-alt"></i> ออกจากระบบ
                                  </a>
                              </div>
                          </div>
                      </div>
                  <?php else: ?>
                      <!-- แสดงปุ่มเข้าสู่ระบบเมื่อยังไม่ได้เข้าสู่ระบบ -->
                      <a href="<?php echo site_url('User'); ?>" target="_blank">เข้าสู่ระบบ</a>
                  <?php endif; ?>
              </li>
          </ul>
      </div>
  </div>

  <div class="welcome-other">
      <div class="wel-nav-sky">
          <img class="cloud-animation cloud-animation-5" src="<?php echo base_url('docs/cloud-header-2.png'); ?>">
          <img class="cloud-animation cloud-animation-6" src="<?php echo base_url('docs/cloud-header-1.png'); ?>">

          <div class="animation-text-orbortor-nav">
              <img src="<?php echo base_url("docs/text-orbortor-nav.png"); ?>">
          </div>

          <img class="animation-wind-R animation-wind-7" src="<?php echo base_url('docs/flower-Header Line.png'); ?>">
      </div>
      <div class="wel-nav-home"></div>
  </div>
  <!-- ชื่อ อบต จุด 2 -->
  <div class="welcome-btm-other">
      <div class="text-center" style="padding-top: 580px;">
      </div>

      <script>
          // Toggle User Dropdown
          function toggleUserDropdown() {
              // รองรับทั้ง 2 dropdown
              const dropdowns = document.querySelectorAll('.user-dropdown');

              dropdowns.forEach(dropdown => {
                  if (event.target.closest('.user-dropdown') === dropdown) {
                      dropdown.classList.toggle('active');
                      dropdown.style.zIndex = dropdown.classList.contains('active') ? '9999999' : '';
                  } else {
                      dropdown.classList.remove('active');
                      dropdown.style.zIndex = '';
                  }
              });

              // ปิด dropdown เมื่อคลิกข้างนอก
              document.addEventListener('click', function(event) {
                  if (!event.target.closest('.user-dropdown')) {
                      dropdowns.forEach(dropdown => {
                          dropdown.classList.remove('active');
                          dropdown.style.zIndex = '';
                      });
                  }
              });
          }

          // Confirm Logout
          function confirmLogout() {
              Swal.fire({
                  title: 'ออกจากระบบ?',
                  text: 'คุณต้องการออกจากระบบหรือไม่?',
                  icon: 'question',
                  showCancelButton: true,
                  confirmButtonColor: '#dc3545',
                  cancelButtonColor: '#6c757d',
                  confirmButtonText: 'ออกจากระบบ',
                  cancelButtonText: 'ยกเลิก',
                  reverseButtons: true
              }).then((result) => {
                  if (result.isConfirmed) {
                      // แสดง loading
                      Swal.fire({
                          title: 'กำลังออกจากระบบ...',
                          allowOutsideClick: false,
                          didOpen: () => {
                              Swal.showLoading();
                          }
                      });

                      // เก็บ URL ปัจจุบัน
                      const currentUrl = window.location.href;

                      // ออกจากระบบ
                      fetch('<?php echo site_url(($user_type === 'public') ? 'Auth_public_mem/logout' : 'User/logout'); ?>', {
                          method: 'GET',
                          credentials: 'same-origin',
                          cache: 'no-cache',
                          headers: {
                              'Cache-Control': 'no-cache, no-store, must-revalidate',
                              'Pragma': 'no-cache',
                              'Expires': '0'
                          }
                      }).then(response => {
                          if (response.ok) {
                              // Clear local storage/session storage หากมี
                              if (typeof(Storage) !== "undefined") {
                                  localStorage.clear();
                                  sessionStorage.clear();
                              }

                              // ตัวเลือก 1: Reload หน้าปัจจุบัน (แนะนำ)
                              window.location.reload(true);

                              // ตัวเลือก 2: ไปที่ URL เดิมแต่ force refresh
                              // window.location.href = currentUrl + (currentUrl.includes('?') ? '&' : '?') + 'refresh=' + new Date().getTime();

                              // ตัวเลือก 3: ใช้ replace แต่ไปหน้าเดิม
                              // window.location.replace(currentUrl);

                          } else {
                              throw new Error('Logout failed');
                          }
                      }).catch(error => {
                          console.error('Logout error:', error);
                          // แม้จะ error ก็ให้ reload หน้าปัจจุบัน
                          window.location.reload(true);
                      });
                  }
              });
          }

          function forceRefreshAfterLogout() {
              // Clear browser cache
              if ('caches' in window) {
                  caches.keys().then(function(names) {
                      for (let name of names) {
                          caches.delete(name);
                      }
                  });
              }

              // Force reload with no cache
              window.location.reload(true);
          }

          // Auto-hide dropdown when scrolling
          window.addEventListener('scroll', function() {
              const dropdown = document.getElementById('userDropdown');
              if (dropdown) {
                  dropdown.classList.remove('active');
              }
          });

          // Show login success message if exists
          <?php if ($this->session->flashdata('login_success')): ?>
              document.addEventListener('DOMContentLoaded', function() {
                  Swal.fire({
                      icon: 'success',
                      title: 'เข้าสู่ระบบสำเร็จ',
                      text: 'ยินดีต้อนรับ <?php echo htmlspecialchars($user_info['name']); ?>',
                      timer: 3000,
                      showConfirmButton: false,
                      position: 'top-end',
                      toast: true
                  });
              });
          <?php endif; ?>

         // Show logout success message if exists
         <?php if ($this->session->flashdata('logout_message')): ?>
              document.addEventListener('DOMContentLoaded', function() {
                  Swal.fire({
                      icon: 'success',
                      title: 'ออกจากระบบสำเร็จ',
                      text: 'ขอบคุณที่ใช้บริการ',
                      timer: 2000,
                      showConfirmButton: false,
                      position: 'top-end',
                      toast: true
                  });

                  // ลบ cookie หลังแสดงข้อความ
                  document.cookie = "logout_message=; expires=Thu, 01 Jan 1970 00:00:00 UTC; path=/;";
              });
          <?php endif; ?>



          // เพิ่มโค้ดนี้ในส่วนท้ายของ <script> ใน navbar ที่มีอยู่

          // ฟังก์ชันเช็ค session
          function quickSessionCheck() {
              fetch('<?php echo site_url("Home/check_session"); ?>', {
                      method: 'GET',
                      credentials: 'same-origin',
                      headers: {
                          'X-Requested-With': 'XMLHttpRequest'
                      }
                  })
                  .then(response => response.json())
                  .then(data => {
                      const userDropdown = document.getElementById('userDropdown');
                      if (!data.is_logged_in && userDropdown) {
                          // เปลี่ยนเป็นปุ่ม login
                          userDropdown.parentElement.innerHTML = '<a href="<?php echo site_url("User"); ?>" target="_blank" style="color: #184C53">เข้าสู่ระบบ</a>';
                          // แสดงการแจ้งเตือน
                          if (!document.getElementById('sessionExpired')) {
                              const toast = document.createElement('div');
                              toast.id = 'sessionExpired';
                              toast.style.cssText = 'position:fixed;top:20px;right:20px;background:#fff3cd;color:#856404;padding:12px 18px;border-radius:6px;box-shadow:0 4px 15px rgba(0,0,0,0.2);z-index:999999;font-size:14px;max-width:280px;';
                              toast.innerHTML = '<i class="fas fa-exclamation-triangle"></i> <strong>Session หมดอายุ</strong><br><small>กรุณาเข้าสู่ระบบใหม่</small><button onclick="this.parentElement.remove()" style="float:right;background:none;border:none;font-size:16px;cursor:pointer;margin-left:10px;">&times;</button>';
                              document.body.appendChild(toast);
                              setTimeout(() => {
                                  if (toast.parentElement) toast.remove();
                              }, 6000);
                          }
                      }
                  })
                  .catch(() => {}); // เงียบๆ ถ้า error
          }

          // เช็คทุก 1 นาที
          setInterval(quickSessionCheck, 60000);

          // เช็คเมื่อ focus กลับมา
          window.addEventListener('focus', quickSessionCheck);

          // เช็คเมื่อคลิกเมนูผู้ใช้
          document.addEventListener('click', function(e) {
              if (e.target.closest('.user-dropdown')) {
                  quickSessionCheck();
              }
          });
      </script>










      <?php
        if (!function_exists('isActiveMenu')) {
            function isActiveMenu($path)
            {
                $CI = &get_instance();
                $current_segment = $CI->uri->segment(2);

                // เพิ่มการเช็คว่ามี segment 2 หรือไม่
                if ($current_segment === null) {
                    return '';  // ถ้าไม่มี segment 2 ให้ return ค่าว่าง
                }

                // เช็คทั้ง detail และกรณีอื่นๆ
                $base_segment = preg_replace('/_detail.*|_view.*|_edit.*|_sub_file.*|_sub.*/', '', $current_segment);

                // เช็คว่า path ที่ส่งมาตรงกับส่วนแรกของ segment หรือไม่
                return ($base_segment === $path) ? 'active-submenu' : '';
            }
        }
        ?>





      <?php
        // ฟังก์ชัน setThaiMonth อยู่นอก foreach loop
        function setThaiMonth($dateString)
        {
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

            foreach ($thaiMonths as $english => $thai) {
                $dateString = str_replace($english, $thai, $dateString);
            }

            return $dateString;
        }

        function setThaiMonthAbbreviation($dateString)
        {
            $thaiMonths = [
                'January' => 'ม.ค.',
                'February' => 'ก.พ.',
                'March' => 'มี.ค.',
                'April' => 'เม.ย.',
                'May' => 'พ.ค.',
                'June' => 'มิ.ย.',
                'July' => 'ก.ค.',
                'August' => 'ส.ค.',
                'September' => 'ก.ย.',
                'October' => 'ต.ค.',
                'November' => 'พ.ย.',
                'December' => 'ธ.ค.',
            ];

            foreach ($thaiMonths as $english => $thai) {
                $dateString = str_replace($english, $thai, $dateString);
            }

            return $dateString;
        }

        function setMonthAbbreviationToLong($monthAbbreviation)
        {
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

            return isset($thaiMonths[$monthAbbreviation]) ? $thaiMonths[$monthAbbreviation] : $monthAbbreviation;
        }
        ?>