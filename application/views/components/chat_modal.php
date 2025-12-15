<!-- CSRF (ถ้าเปิดใช้ใน CI3) -->
<meta name="csrf-name" content="<?= html_escape($csrf_name ?? '') ?>">
<meta name="csrf-hash" content="<?= html_escape($csrf_hash ?? '') ?>">

<style>
    .messenger-container {
        position: fixed;
        /* เปลี่ยนเป็น fixed แทน absolute */
        bottom: 30px;
        right: 10px;
        z-index: 999;
        display: flex;
        align-items: center;
    }

    .bg-messenger {
        background-image: url('<?php echo base_url("docs/chat-fb.png"); ?>');
        width: 180px;
        height: 229px;
        display: flex;
        justify-content: flex-start;
        align-items: flex-start;
        cursor: pointer;
        transition: transform 0.3s ease, background-image 0.6s ease;
        position: relative;
        z-index: 999;
    }

    .font-messenger {
        color: #000;
        text-align: center;
        font-size: 18px;
        font-style: normal;
        font-weight: 300;
        line-height: 1.4;
        border-radius: 1px;
        padding-top: 45px;
        margin-left: 35px;
    }

    .close-button-slide-messenger {
        position: absolute;
        top: 10px;
        right: 20px;
        border: none;
        cursor: pointer;
        width: 28px;
        height: 28px;
        background-image: url('<?php echo base_url("docs/close_messenger.png"); ?>');
        background-size: cover;
        background-position: center;
        transition: background-image 0.3s ease;
        z-index: 9999;
    }

    .close-button-slide-messenger:hover {
        background-image: url('<?php echo base_url("docs/close_messenger_hover.png"); ?>');
    }


    #messenger-icon {
        position: fixed;
        bottom: 70px;
        left: 10px;
        width: 50px;
        height: 50px;
        background-color: #0078FF;
        border-radius: 50%;
        box-shadow: 0 4px 8px rgba(0, 0, 0, 0.2);
        display: flex;
        justify-content: center;
        align-items: center;
        cursor: pointer;
        transition: transform 0.3s ease;
        z-index: 999;
        /* position: relative; */
        /* เพิ่มเพื่อให้ tooltip ติดกับ icon */
    }

    #messenger-icon:hover {
        transform: scale(1.1);
    }

    #messenger-icon img {
        width: 30px;
        height: 30px;
    }

    .tooltip {
        visibility: hidden;
        width: auto;
        background-color: black;
        color: #fff;
        text-align: center;
        border-radius: 6px;
        padding: 5px;
        position: absolute;
        z-index: 1;
        top: 50%;
        left: 110%;
        transform: translateY(-50%);
        opacity: 0;
        transition: opacity 0.3s;
    }

    #messenger-icon:hover .tooltip {
        visibility: visible;
        opacity: 1;
    }

    /* ---------- Offcanvas แชท: ปรับให้ชิดมุมล่างขวา ---------- */
    .chat-canvas {
        position: fixed !important;
        width: min(440px, 90vw) !important;
        height: 90vh !important;
        /* เพิ่มความสูงเป็น 90% */
        top: auto !important;
        /* ใช้ auto แทน */
        bottom: 20px !important;
        /* ชิดล่างมากขึ้น - เว้นแค่ 20px */
        right: 20px !important;
        /* เว้นระยะจากด้านขวา */
        left: auto !important;
        border-radius: 16px;
        /* เพิ่ม border radius */
        box-shadow:
            -20px 0 60px rgba(0, 0, 0, .08),
            -5px 0 25px rgba(0, 0, 0, .05),
            0 0 0 1px rgba(255, 255, 255, .1);
        border: 0;
        background: rgba(255, 255, 255, 0.95);
        backdrop-filter: blur(20px) saturate(1.8);
        -webkit-backdrop-filter: blur(20px) saturate(1.8);
        transform-origin: center right;
        transition: all .4s cubic-bezier(.25, .8, .25, 1);
        z-index: 1055 !important;
        visibility: hidden;
        opacity: 1;
        transform: translateX(100%);
        margin-right: 25px;

    }

    /* เมื่อเปิด: แสดงและเลื่อนเข้ามา */
    .chat-canvas.show {
        visibility: visible !important;
        opacity: 1 !important;
        transform: translateX(0) !important;
        margin-right: 25px;
        margin-top: 50px;
    }

    /* Override Bootstrap offcanvas default styles */
    .chat-canvas.offcanvas {
        max-width: none !important;
        border: none !important;
    }

    .chat-canvas.offcanvas-end {
        right: 0 !important;
        left: auto !important;
        transform: translateX(100%);
    }

    .chat-canvas.offcanvas-end.show {
        transform: translateX(0) !important;
    }

    /* ---------- Chat Menu Popup ---------- */
    .chat-menu-popup {
        position: fixed !important;
        width: min(320px, 90vw) !important;
        height: auto !important;
        top: 50% !important;
        right: 30px !important;
        left: auto !important;
        transform: translateY(-50%) scale(0.8) !important;
        border-radius: 24px !important;
        box-shadow:
            0 20px 60px rgba(0, 0, 0, .08),
            0 10px 30px rgba(0, 0, 0, .04),
            0 0 0 1px rgba(255, 255, 255, .2) !important;
        border: 0;
        background: rgba(255, 255, 255, 0.95) !important;
        backdrop-filter: blur(20px) saturate(1.8) !important;
        -webkit-backdrop-filter: blur(20px) saturate(1.8) !important;
        z-index: 999999 !important;
        visibility: hidden !important;
        opacity: 0 !important;
        transition: all .3s cubic-bezier(.25, .8, .25, 1) !important;
        display: block !important;
        pointer-events: none;
    }

    .chat-menu-popup.show {
        visibility: visible !important;
        opacity: 1 !important;
        transform: translateY(-50%) scale(1) !important;
        pointer-events: auto !important;
    }

    .chat-menu-popup.hide {
        transform: translateY(-50%) scale(0.8) !important;
        opacity: 0 !important;
        pointer-events: none !important;
    }

    .menu-header {
        background: linear-gradient(135deg,
                rgba(107, 114, 128, 0.95) 0%,
                rgba(75, 85, 99, 0.95) 100%);
        color: white;
        border-radius: 24px 24px 0 0;
        padding: 20px;
        position: relative;
        overflow: hidden;
        backdrop-filter: blur(20px);
        -webkit-backdrop-filter: blur(20px);
    }

    .menu-header::before {
        content: '';
        position: absolute;
        top: 0;
        left: 0;
        right: 0;
        bottom: 0;
        background:
            radial-gradient(circle at 20% 30%, rgba(255, 255, 255, .15) 0%, transparent 50%),
            radial-gradient(circle at 80% 70%, rgba(255, 255, 255, .1) 0%, transparent 50%);
        pointer-events: none;
    }

    .menu-header .btn-close {
        background: rgba(255, 255, 255, .15);
        border-radius: 50%;
        padding: 8px;
        backdrop-filter: blur(10px);
        -webkit-backdrop-filter: blur(10px);
        border: 1px solid rgba(255, 255, 255, .2);
        transition: all .2s ease;
        position: relative;
        z-index: 1;
        width: 32px;
        height: 32px;
        display: flex;
        align-items: center;
        justify-content: center;
        color: white;
        font-size: 18px;
        font-weight: bold;
    }

    .menu-header .btn-close:hover {
        background: rgba(255, 255, 255, .25);
        transform: scale(1.1);
        border-color: rgba(255, 255, 255, .3);
        color: white;
    }

    .menu-header .btn-close:before {
        content: "×";
        font-size: 20px;
        line-height: 1;
        color: white;
    }

    .menu-title {
        font-size: 1.2rem;
        font-weight: 600;
        margin: 0;
        text-shadow: 0 2px 8px rgba(0, 0, 0, .1);
        position: relative;
        z-index: 1;
    }

    .menu-subtitle {
        opacity: 0.9;
        font-size: 0.85rem;
        margin-top: 4px;
        position: relative;
        z-index: 1;
    }

    .menu-options {
        padding: 24px;
        /* background: rgba(255, 255, 255, 0.6); */
        /* backdrop-filter: blur(10px);
        -webkit-backdrop-filter: blur(10px); */
        border-radius: 16px;
    }

    .menu-option {
        display: flex;
        align-items: center;
        padding: 16px 20px;
        margin-bottom: 12px;
        border-radius: 16px;
        background: rgba(255, 255, 255, 0.8);
        border: 1px solid rgba(107, 114, 128, .1);
        cursor: pointer;
        transition: all .3s cubic-bezier(.25, .8, .25, 1);
        text-decoration: none;
        color: inherit;
        backdrop-filter: blur(10px);
        -webkit-backdrop-filter: blur(10px);
    }

    .menu-option:hover {
        border-color: rgba(107, 114, 128, .25);
        background: rgba(255, 255, 255, 0.95);
        transform: translateY(-3px);
        box-shadow:
            0 12px 32px rgba(107, 114, 128, .1),
            0 4px 12px rgba(107, 114, 128, .05);
        color: inherit;
        text-decoration: none;
    }

    .menu-option:last-child {
        margin-bottom: 0;
    }

    .menu-option-icon {
        width: 44px;
        height: 44px;
        border-radius: 14px;
        background: linear-gradient(135deg,
                rgba(107, 114, 128, 0.9),
                rgba(75, 85, 99, 0.9));
        display: flex;
        align-items: center;
        justify-content: center;
        margin-right: 16px;
        color: white;
        font-size: 20px;
        flex-shrink: 0;
        backdrop-filter: blur(10px);
        -webkit-backdrop-filter: blur(10px);
        border: 1px solid rgba(255, 255, 255, .2);
    }

    .menu-option-content h6 {
        margin: 0;
        font-size: 1rem;
        font-weight: 600;
        color: #374151;
    }

    .menu-option-content p {
        margin: 4px 0 0;
        font-size: 0.85rem;
        color: #6B7280;
    }

    /* Backdrop for menu */
    .menu-backdrop {
        position: fixed !important;
        top: 0 !important;
        left: 0 !important;
        right: 0 !important;
        bottom: 0 !important;
        background: rgba(0, 0, 0, .15) !important;
        backdrop-filter: blur(4px) !important;
        -webkit-backdrop-filter: blur(4px) !important;
        z-index: 999998 !important;
        visibility: hidden !important;
        opacity: 0 !important;
        transition: all .3s ease !important;
        display: block !important;
        pointer-events: none;
    }

    .menu-backdrop.show {
        visibility: visible !important;
        opacity: 1 !important;
        pointer-events: auto !important;
    }

    /* ---------- Header ปรับเข้ากับรูปแบบแผงลอย ---------- */
    .chat-header {
        background: linear-gradient(135deg,
                rgba(107, 114, 128, 0.95) 0%,
                rgba(75, 85, 99, 0.95) 100%);
        color: white;
        border-radius: 16px 16px 0 0;
        /* มุมโค้งด้านบน */
        padding: 20px;
        position: relative;
        overflow: hidden;
        flex-shrink: 0;
        /* ไม่ให้หด */
        backdrop-filter: blur(20px);
        -webkit-backdrop-filter: blur(20px);
    }

    .chat-header::before {
        content: '';
        position: absolute;
        top: 0;
        left: 0;
        right: 0;
        bottom: 0;
        background:
            radial-gradient(circle at 20% 30%, rgba(255, 255, 255, .15) 0%, transparent 50%),
            radial-gradient(circle at 80% 70%, rgba(255, 255, 255, .1) 0%, transparent 50%);
        pointer-events: none;
    }

    .chat-header .btn-close {
        background: rgba(255, 255, 255, .15);
        border-radius: 50%;
        padding: 8px;
        backdrop-filter: blur(10px);
        -webkit-backdrop-filter: blur(10px);
        border: 1px solid rgba(255, 255, 255, .2);
        transition: all .2s ease;
        position: relative;
        z-index: 1;
        width: 32px;
        height: 32px;
        display: flex;
        align-items: center;
        justify-content: center;
        color: white;
        font-size: 18px;
        font-weight: bold;
    }

    .chat-header .btn-close:hover {
        background: rgba(255, 255, 255, .25);
        transform: scale(1.1);
        border-color: rgba(255, 255, 255, .3);
        color: white;
    }

    .chat-header .btn-close:before {
        content: "×";
        font-size: 20px;
        line-height: 1;
        color: white;
    }

    .chat-title {
        font-size: 1.3rem;
        font-weight: 600;
        margin: 0;
        text-shadow: 0 2px 8px rgba(0, 0, 0, .1);
        position: relative;
        z-index: 1;
    }

    .chat-subtitle {
        opacity: 0.9;
        font-size: 0.9rem;
        margin-top: 4px;
        position: relative;
        z-index: 1;
    }

    /* ---------- พื้นที่แชท: เต็มหน้าจอ ไม่มี horizontal scroll ---------- */
    .chat-history {
        background: rgba(249, 250, 251, 0.6);
        backdrop-filter: blur(10px);
        -webkit-backdrop-filter: blur(10px);
        border-radius: 0;
        /* ไม่มี border radius */
        padding: 20px;
        margin: 0;
        /* ไม่มี margin */
        box-shadow: none;
        /* ลบ shadow */

        /* ใช้พื้นที่เต็มที่ */
        flex: 1 !important;
        /* ขยายเต็มพื้นที่ที่เหลือ */
        height: auto !important;
        max-height: none !important;
        min-height: 0 !important;

        /* จัดการ scroll - ปิด horizontal สมบูรณ์ */
        overflow-x: hidden !important;
        overflow-y: auto !important;

        /* จัดการขนาด container */
        width: 100%;
        max-width: 100%;
        box-sizing: border-box;

        /* Custom scrollbar สำหรับ y-axis เท่านั้น */
        scrollbar-width: thin;
        scrollbar-color: rgba(107, 114, 128, .3) transparent;
    }

    /* ซ่อน scrollbar ใน Webkit browsers */
    .chat-history::-webkit-scrollbar {
        width: 6px !important;
        /* เฉพาะ vertical scrollbar */
        height: 0px !important;
        /* ปิด horizontal scrollbar สมบูรณ์ */
    }

    .chat-history::-webkit-scrollbar-track {
        background: rgba(0, 0, 0, .03);
        border-radius: 3px;
    }

    .chat-history::-webkit-scrollbar-thumb {
        background: rgba(107, 114, 128, .3);
        border-radius: 3px;
        transition: background .3s ease;
    }

    .chat-history::-webkit-scrollbar-thumb:hover {
        background: rgba(107, 114, 128, .5);
    }

    /* บังคับปิด horizontal scrollbar */
    .chat-history::-webkit-scrollbar-corner {
        display: none !important;
        background: transparent;
    }

    .chat-history::-webkit-scrollbar:horizontal {
        display: none !important;
        height: 0px !important;
    }

    /* ---------- ฟองแชท: ปรับรูปแบบใหม่สำหรับเต็มหน้าจอ ---------- */
    .chat-bubble {
        /* ปรับขนาดสำหรับหน้าจอใหญ่ */
        max-width: min(380px, calc(100% - 60px)) !important;
        width: fit-content;
        min-width: 80px;

        padding: 16px 20px;
        /* เพิ่ม padding */
        border-radius: 20px;
        /* เพิ่ม border radius */
        box-shadow:
            0 4px 20px rgba(107, 114, 128, .08),
            0 2px 8px rgba(107, 114, 128, .04);

        /* การจัดการข้อความ */
        word-wrap: break-word !important;
        word-break: break-word !important;
        overflow-wrap: break-word !important;
        white-space: pre-wrap !important;
        hyphens: auto;

        line-height: 1.6;
        /* เพิ่ม line height */
        position: relative;
        animation: bubbleSlideIn .3s cubic-bezier(.25, .8, .25, 1);
        margin-bottom: 16px;
        /* เพิ่มระยะห่าง */

        /* ป้องกันการ overflow */
        overflow: hidden;
        box-sizing: border-box;

        /* ปรับ font size */
        font-size: 15px;

        /* เพิ่ม blur effect */
        backdrop-filter: blur(10px);
        -webkit-backdrop-filter: blur(10px);
        border: 1px solid rgba(255, 255, 255, .3);
    }

    /* แก้ไขสำหรับ responsive - มือถือ */
    @media (max-width: 576px) {
        .chat-bubble {
            max-width: min(300px, calc(100% - 20px)) !important;
            /* ลดขนาดสำหรับมือถือ */
            padding: 12px 16px;
            font-size: 14px;
            /* ปรับขนาดฟอนต์สำหรับมือถือ */
        }

        .chat-menu-popup {
            width: min(300px, 95vw) !important;
            right: 15px !important;
            border-radius: 20px !important;
        }
    }

    @keyframes bubbleSlideIn {
        from {
            opacity: 0;
            transform: translateY(20px) scale(0.95);
        }

        to {
            opacity: 1;
            transform: translateY(0) scale(1);
        }
    }

    /* แก้ไขการจัดตำแหน่งฟองแชท */
    .chat-bubble.user {
        color: #1F2937;
        background: rgba(255, 255, 255, 0.9);
        align-self: flex-end;
        border-bottom-right-radius: 6px;
        margin-left: auto;
        box-shadow:
            0 4px 20px rgba(107, 114, 128, .12),
            0 2px 8px rgba(107, 114, 128, .06);
        /* ป้องกันการขยายเกินขนาด */
        margin-right: 0;
        border: 1px solid rgba(107, 114, 128, .15);
    }

    .chat-bubble.user::before {
        content: '';
        position: absolute;
        bottom: 0;
        right: -8px;
        width: 0;
        height: 0;
        border: 8px solid transparent;
        border-left-color: rgba(255, 255, 255, 0.9);
        border-bottom: 0;
        filter: drop-shadow(2px 2px 4px rgba(107, 114, 128, .1));
    }

    .chat-bubble.ai {
        color: #1F2937;
        background: rgba(255, 255, 255, 0.95);
        align-self: flex-start;
        border-bottom-left-radius: 6px;
        border: 1px solid rgba(107, 114, 128, .1);
        box-shadow:
            0 4px 20px rgba(107, 114, 128, .08),
            0 2px 8px rgba(107, 114, 128, .04);
        /* ป้องกันการขยายเกินขนาด */
        margin-left: 0;
    }

    .chat-bubble.ai::before {
        content: '';
        position: absolute;
        bottom: 0;
        left: -8px;
        width: 0;
        height: 0;
        border: 8px solid transparent;
        border-right-color: rgba(255, 255, 255, 0.95);
        border-bottom: 0;
        filter: drop-shadow(-2px 2px 4px rgba(107, 114, 128, .1));
    }

    .chat-bubble.error {
        color: #DC2626;
        background: rgba(254, 242, 242, 0.95);
        align-self: center;
        border: 1px solid rgba(220, 38, 38, .2);
        border-radius: 16px;
        box-shadow: 0 4px 16px rgba(220, 38, 38, .08);
        text-align: center;
        font-weight: 500;
        max-width: 90% !important;
        /* ให้ error message กว้างขึ้น */
        backdrop-filter: blur(10px);
        -webkit-backdrop-filter: blur(10px);
    }

    .chat-bubble.warning {
        color: #D97706;
        background: rgba(255, 251, 235, 0.95);
        align-self: center;
        border: 1px solid rgba(217, 119, 6, .2);
        border-radius: 16px;
        box-shadow: 0 4px 16px rgba(217, 119, 6, .08);
        text-align: center;
        font-weight: 500;
        max-width: 90% !important;
        /* ให้ warning message กว้างขึ้น */
        backdrop-filter: blur(10px);
        -webkit-backdrop-filter: blur(10px);
    }

    /* แก้ไข container ของแต่ละข้อความ - เพิ่มการจัดการ scrolling */
    .chat-history>.d-flex {
        width: 100%;
        max-width: 100%;
        box-sizing: border-box;
        margin-bottom: 12px;
        /* ป้องกัน horizontal overflow */
        overflow: visible;
        /* เปลี่ยนจาก hidden เป็น visible เพื่อให้ bubble แสดงได้เต็มที่ */
        min-height: fit-content;
    }

    /* แก้ไขการจัดตำแหน่ง justify-content */
    .chat-history .justify-content-end {
        padding-left: 20px;
        /* เพิ่ม padding เพื่อให้มีพื้นที่สำหรับ text wrapping */
    }

    .chat-history .justify-content-start {
        padding-right: 20px;
        /* เพิ่ม padding เพื่อให้มีพื้นที่สำหรับ text wrapping */
    }

    .chat-history .justify-content-center {
        padding: 0 10px;
        /* เพิ่ม padding สำหรับ error/warning messages */
    }

    /* ---------- Typing indicator ปรับเข้ากับสีเว็บ ---------- */
    .chat-typing {
        background: rgba(107, 114, 128, .08);
        border-radius: 12px;
        padding: 12px 16px;
        margin: 0 16px 8px;
        backdrop-filter: blur(10px);
        -webkit-backdrop-filter: blur(10px);
        border: 1px solid rgba(107, 114, 128, .1);
        /* ป้องกันการเกินขนาด */
        max-width: calc(100% - 32px);
        box-sizing: border-box;
    }

    .typing-dot {
        width: 8px;
        height: 8px;
        border-radius: 50%;
        background: linear-gradient(45deg, rgba(107, 114, 128, .8), rgba(75, 85, 99, .8));
        display: inline-block;
        animation: typing 1.4s infinite ease-in-out;
        margin-right: 4px;
    }

    .typing-dot:nth-child(1) {
        animation-delay: 0s;
    }

    .typing-dot:nth-child(2) {
        animation-delay: 0.2s;
    }

    .typing-dot:nth-child(3) {
        animation-delay: 0.4s;
    }

    @keyframes typing {

        0%,
        60%,
        100% {
            transform: translateY(0) scale(1);
            opacity: 0.6;
        }

        30% {
            transform: translateY(-10px) scale(1.2);
            opacity: 1;
        }
    }

    /* ---------- Form input ปรับปรุง ---------- */
    .chat-form {
        padding: 16px;
        background: rgba(255, 255, 255, 0.8);
        /* backdrop-filter: blur(20px);
        -webkit-backdrop-filter: blur(20px); */
        border-radius: 0;
        border-top: 1px solid rgba(107, 114, 128, .1);
        flex-shrink: 0;
        /* ป้องกันการ overflow ของ form */
        box-sizing: border-box;
        border-radius: 16px;
    }

    .chat-input {
        border: 2px solid rgba(107, 114, 128, .2);
        border-radius: 25px;
        padding: 12px 20px;
        font-size: 1rem;
        transition: all .3s ease;
        background: rgba(255, 255, 255, 0.95);
        backdrop-filter: blur(15px);
        -webkit-backdrop-filter: blur(15px);
        /* ป้องกัน input เกินขนาด */
        width: 100%;
        max-width: 100%;
        box-sizing: border-box;
        color: #1F2937;
    }

    .chat-input:focus {
        border-color: rgba(107, 114, 128, .5);
        box-shadow: 0 0 0 3px rgba(107, 114, 128, .1);
        background: rgba(255, 255, 255, 1);
        outline: none;
    }

    .chat-input::placeholder {
        color: #9CA3AF;
    }

    .chat-submit {
        background: linear-gradient(135deg,
                rgba(107, 114, 128, 0.9),
                rgba(75, 85, 99, 0.9));
        border: none;
        border-radius: 25px;
        padding: 12px 20px;
        color: white;
        font-weight: 600;
        transition: all .3s ease;
        box-shadow:
            0 4px 16px rgba(107, 114, 128, .2),
            0 2px 8px rgba(107, 114, 128, .1);
        /* ป้องกัน button เกินขนาด */
        white-space: nowrap;
        flex-shrink: 0;
        backdrop-filter: blur(10px);
        -webkit-backdrop-filter: blur(10px);
        border: 1px solid rgba(255, 255, 255, .2);
    }

    .chat-submit:hover:not(:disabled) {
        transform: translateY(-2px);
        box-shadow:
            0 8px 25px rgba(107, 114, 128, .25),
            0 4px 12px rgba(107, 114, 128, .15);
        background: linear-gradient(135deg,
                rgba(107, 114, 128, 1),
                rgba(75, 85, 99, 1));
    }

    .chat-submit:active {
        transform: translateY(0);
    }

    .chat-submit:disabled {
        background: rgba(156, 163, 175, 0.6);
        color: rgba(107, 114, 128, 0.7);
        cursor: not-allowed;
        transform: none;
        box-shadow: none;
    }

    /* ---------- Status indicators ---------- */
    .chat-status {
        font-size: 0.75rem;
        opacity: 0.7;
        text-align: center;
        margin: 8px 0;
        color: #6B7280;
        /* ป้องกันการเกินขนาด */
        word-wrap: break-word;
        max-width: 100%;
        box-sizing: border-box;
    }

    .connection-status {
        display: inline-flex;
        align-items: center;
        gap: 6px;
        font-size: 0.8rem;
        color: rgba(255, 255, 255, .9);
        position: relative;
        z-index: 1;
    }

    .connection-status::before {
        content: '●';
        color: #10B981;
        animation: pulse 2s infinite;
    }

    @keyframes pulse {

        0%,
        100% {
            opacity: 1;
        }

        50% {
            opacity: 0.5;
        }
    }

    /* ---------- Responsive: ปรับให้เหมาะสมกับทุกขนาดหน้าจอ ---------- */
    @media (max-width: 768px) {
        .chat-canvas {
            width: 100vw !important;
            /* เต็มความกว้างหน้าจอในมือถือ */
            height: 100vh !important;
            right: 0 !important;
            left: 0 !important;
        }

        .chat-bubble {
            max-width: min(320px, calc(100% - 40px)) !important;
            padding: 14px 18px;
            font-size: 14px;
            border-radius: 18px;
        }

        .chat-history {
            padding: 16px;
        }

        .chat-form {
            padding: 16px;
        }

        .chat-input {
            font-size: 16px;
            /* ป้องกัน zoom ใน iOS */
            padding: 12px 18px;
        }

        .chat-submit {
            padding: 12px 18px;
            font-size: 14px;
        }
    }

    @media (max-width: 576px) {
        .chat-bubble {
            max-width: min(280px, calc(100% - 30px)) !important;
            padding: 12px 16px;
        }

        .chat-history {
            padding: 12px;
        }
    }

    /* ---------- แก้ไขปัญหา z-index และ visibility สำหรับเต็มหน้าจอ ---------- */
    .offcanvas-backdrop {
        display: none !important;
    }

    .chat-canvas.offcanvas-end {
        right: 0 !important;
        left: auto !important;
        top: 0 !important;
        bottom: 0 !important;
    }

    /* Force visibility when showing */
    .chat-canvas.showing,
    .chat-canvas.show {
        visibility: visible !important;
        opacity: 1 !important;
        display: flex !important;
        /* ใช้ flex layout */
        flex-direction: column !important;
        transform: translateX(0) !important;
    }

    /* จัดการ offcanvas body ให้เป็น flex */
    .chat-canvas .offcanvas-body {
        display: flex !important;
        flex-direction: column !important;
        flex: 1 !important;
        padding: 0 !important;
        overflow: hidden !important;
        /* ป้องกัน body overflow */
    }

    /* ---------- เพิ่ม CSS สำหรับการจัดการลิงก์และรูปแบบข้อความพิเศษ ---------- */
    .chat-bubble a {
        color: inherit;
        text-decoration: underline;
        word-break: break-all;
        /* ให้ลิงก์ยาวๆ แบ่งบรรทัดได้ */
        opacity: 0.8;
        transition: opacity .2s ease;
    }

    .chat-bubble a:hover {
        opacity: 1;
    }

    .chat-bubble.user a {
        color: rgba(107, 114, 128, 0.9);
    }

    .chat-bubble.ai a {
        color: rgba(107, 114, 128, 0.8);
    }

    /* สำหรับข้อความที่มีการจัดรูปแบบ HTML */
    .chat-bubble p {
        margin: 0;
        margin-bottom: 8px;
    }

    .chat-bubble p:last-child {
        margin-bottom: 0;
    }

    .chat-bubble strong {
        font-weight: 600;
        color: #374151;
    }

    .chat-bubble em {
        font-style: italic;
        color: #4B5563;
    }

    /* เพิ่ม effect สำหรับ glass morphism */
    .glass-effect {
        background: rgba(255, 255, 255, 0.25);
        backdrop-filter: blur(20px);
        -webkit-backdrop-filter: blur(20px);
        border: 1px solid rgba(255, 255, 255, 0.18);
    }

    /* Animation เพิ่มเติมสำหรับ smooth transition */
    * {
        -webkit-font-smoothing: antialiased;
        -moz-osx-font-smoothing: grayscale;
    }

    /* เพิ่ม custom properties สำหรับสีหลัก */
    :root {
        --chat-primary: rgba(107, 114, 128, 1);
        --chat-primary-light: rgba(107, 114, 128, 0.8);
        --chat-secondary: rgba(75, 85, 99, 1);
        --chat-bg: rgba(249, 250, 251, 0.6);
        --chat-white: rgba(255, 255, 255, 0.95);
        --chat-border: rgba(107, 114, 128, 0.1);
        --chat-shadow: rgba(107, 114, 128, 0.08);
    }
</style>

<!-- ใช้ messenger container แทน chat fab -->
<div class="messenger-container underline">
    <div class="bg-messenger" onclick="showChatMenu()">
        <span class="font-messenger">แชทกับเรา<br>คลิ๊กเลย</span>
    </div>
    <div class="close-button-slide-messenger" title="ปิด" onclick="closeImageSlideMid2()"></div>
</div>

<!-- Menu Backdrop -->
<div id="menuBackdrop" class="menu-backdrop" onclick="closeChatMenu()"></div>

<!-- Chat Menu Popup -->
<div id="chatMenuPopup" class="chat-menu-popup">
    <div class="menu-header">
        <div class="d-flex justify-content-between align-items-start">
            <div>
                <h5 class="menu-title">เลือกรูปแบบการสนทนา</h5>
                <p class="menu-subtitle mb-0">ผู้ช่วยญาดา(AI) ยินดีให้บริการ</p>
            </div>
            <button type="button" class="btn-close" onclick="closeChatMenu()" aria-label="ปิด"></button>
        </div>
    </div>

    <div class="menu-options">
        <!-- AI Chat Option -->
        <div class="menu-option" onclick="selectAIChat()">
            <div class="menu-option-icon">
                🤖
            </div>
            <div class="menu-option-content">
                <h6>แชทกับ AI</h6>
                <p>ปรึกษาผู้ช่วยญาดาได้ 24 ชั่วโมง</p>
            </div>
        </div>

        <!-- Staff Chat Option -->
        <a href="<?php echo get_config_value('message'); ?>" target="_blank" class="menu-option"
            onclick="selectStaffChat(this)">
            <div class="menu-option-icon">
                👨‍💼
            </div>
            <div class="menu-option-content">
                <h6>แชทกับเจ้าหน้าที่</h6>
                <p>สนทนากับเจ้าหน้าที่จริงผ่าน Facebook</p>
            </div>
        </a>
    </div>
</div>

<!-- แผง แชทแบบ Offcanvas -->
<div id="chatbotCanvas" class="offcanvas offcanvas-end chat-canvas" tabindex="-1" aria-labelledby="chatbotCanvasLabel"
    data-bs-scroll="true" data-bs-backdrop="false">

    <!-- Header ปรับปรุงใหม่ -->
    <div class="chat-header">
        <div class="d-flex justify-content-between align-items-start">
            <div>
                <h5 class="chat-title" id="chatbotCanvasLabel">แชทกับผู้ช่วยญาดา ( AI ) </h5>
                <p class="chat-subtitle mb-0">ผู้ช่วยตอบคำถามทั่วไป</p>
                <div class="connection-status mt-2">
                    <span>เชื่อมต่อแล้ว</span>
                </div>
            </div>
            <button type="button" class="btn-close" data-bs-dismiss="offcanvas" aria-label="ปิด"></button>
        </div>
    </div>

    <div class="offcanvas-body p-0 d-flex flex-column">
        <!-- ประวัติแชท -->
        <div class="chat-history flex-grow-1" data-chat-history>
            <!-- ข้อความต้อนรับจะแสดงที่นี่ -->
        </div>

        <!-- ตอนกำลังพิมพ์ -->
        <div class="chat-typing d-flex align-items-center small d-none" data-chat-loading>
            <span class="typing-dot"></span>
            <span class="typing-dot"></span>
            <span class="typing-dot"></span>
            <span class="ms-2">ผู้ช่วยญาดากำลังพิมพ์...</span>
        </div>

        <!-- ช่องพิมพ์/ปุ่มส่ง -->
        <form class="chat-form d-flex gap-2" data-chat-form>
            <input type="text" class="form-control chat-input flex-grow-1"
                placeholder="พิมพ์คำถามเกี่ยวกับการท่องเที่ยว..." autocomplete="off" data-chat-input maxlength="500">
            <button type="submit" class="btn chat-submit px-4">
                <span data-send-text>ส่ง</span>
                <span data-sending-text class="d-none">กำลังส่ง...</span>
            </button>
        </form>
    </div>
</div>

<script>
    console.log('Modern White-Grey Chat Theme with Blur Initialized');

    // ===== Menu Functions =====
    function showChatMenu() {
        hideMessengerButton();
        console.log('=== showChatMenu() called ===');

        const menuPopup = document.getElementById('chatMenuPopup');
        const backdrop = document.getElementById('menuBackdrop');

        console.log('Menu elements check:');
        console.log('- menuPopup found:', !!menuPopup, menuPopup);
        console.log('- backdrop found:', !!backdrop, backdrop);

        if (menuPopup) {
            console.log('menuPopup current styles:');
            console.log('- visibility:', getComputedStyle(menuPopup).visibility);
            console.log('- opacity:', getComputedStyle(menuPopup).opacity);
            console.log('- display:', getComputedStyle(menuPopup).display);
            console.log('- z-index:', getComputedStyle(menuPopup).zIndex);
            console.log('- position:', getComputedStyle(menuPopup).position);
        }

        if (menuPopup && backdrop) {
            console.log('Adding show classes...');

            // Remove any existing classes
            menuPopup.classList.remove('hide');
            backdrop.classList.remove('hide');

            // Force visibility first
            menuPopup.style.visibility = 'visible';
            menuPopup.style.opacity = '1';
            menuPopup.style.display = 'block';
            menuPopup.style.zIndex = '999999';
            menuPopup.style.pointerEvents = 'auto';
            menuPopup.style.transform = 'translateY(-50%) scale(1)';

            backdrop.style.visibility = 'visible';
            backdrop.style.opacity = '1';
            backdrop.style.display = 'block';
            backdrop.style.zIndex = '999998';
            backdrop.style.pointerEvents = 'auto';

            // Add classes
            backdrop.classList.add('show');
            menuPopup.classList.add('show');

            console.log('Show classes added and manual styles applied');

            // Check final state
            setTimeout(() => {
                console.log('Final menu state:');
                console.log('- visibility:', getComputedStyle(menuPopup).visibility);
                console.log('- opacity:', getComputedStyle(menuPopup).opacity);
                console.log('- display:', getComputedStyle(menuPopup).display);
                console.log('- z-index:', getComputedStyle(menuPopup).zIndex);
            }, 100);
        } else {
            console.error('Missing elements - menuPopup:', !!menuPopup, 'backdrop:', !!backdrop);
        }
    }

    function hideMessengerButton() {
        const el = document.querySelector(".messenger-container");
        if (el) el.style.display = "none";
    }

    function showMessengerButton() {
        const el = document.querySelector(".messenger-container");
        if (el) el.style.display = "block";
    }


    function closeChatMenu({
        showButton = true
    } = {}) {
        console.log('Closing chat menu');

        const menuPopup = document.getElementById('chatMenuPopup');
        const backdrop = document.getElementById('menuBackdrop');

        if (menuPopup && backdrop) {
            menuPopup.classList.add('hide');
            backdrop.classList.remove('show');

            // Remove show class after animation
            setTimeout(() => {
                menuPopup.classList.remove('show', 'hide'); // เก็บให้เรียบร้อย
                menuPopup.style.display = 'none';
                backdrop.style.display = 'none'; // ถ้าต้องการให้ปุ่มกลับมา ให้ทำตรงนี้
                if (showButton) showMessengerButton();
            }, 300);
        }
    }

    function selectAIChat() {
        console.log('AI Chat selected');

        // Close menu first
        closeChatMenu({ showButton: false });

        // Open AI chatbot after menu closes
        setTimeout(() => {
            openChatbot();
        }, 300);
    }

    function selectStaffChat(linkElement) {
        console.log('Staff Chat selected - redirecting to Facebook');

        // Close menu immediately since we're navigating away
        closeChatMenu();

        // The link will handle the navigation to Facebook
        // No additional action needed as the onclick and href will work together
    }

    function openChatbot() {
        console.log('Opening chatbot canvas');

        const chatCanvas = document.getElementById('chatbotCanvas');

        if (!chatCanvas) {
            console.error('chatbotCanvas element not found');
            return;
        }

        try {
            if (typeof bootstrap !== 'undefined' && bootstrap.Offcanvas) {
                console.log('Using Bootstrap Offcanvas');
                const bsOffcanvas = new bootstrap.Offcanvas(chatCanvas);
                bsOffcanvas.show();
            } else if (typeof $ !== 'undefined' && $.fn.offcanvas) {
                console.log('Using jQuery Offcanvas');
                $(chatCanvas).offcanvas('show');
            } else {
                console.log('Using Manual CSS Classes');
                chatCanvas.classList.add('show');
                chatCanvas.style.visibility = 'visible';

                const shownEvent = new Event('shown.bs.offcanvas');
                chatCanvas.dispatchEvent(shownEvent);
            }
        } catch (error) {
            console.error('Error opening chatbot:', error);

            // Fallback method
            chatCanvas.classList.add('show');
            chatCanvas.style.visibility = 'visible';
            chatCanvas.style.transform = 'translateX(0) scale(1)';
            chatCanvas.style.opacity = '1';

            setTimeout(() => {
                const shownEvent = new Event('shown.bs.offcanvas');
                chatCanvas.dispatchEvent(shownEvent);
            }, 100);
        }
    }

    document.addEventListener('DOMContentLoaded', function () {
        console.log('DOM Content Loaded - Modern Theme');

        // ====== อ้างอิง DOM ======
        const canvas = document.getElementById('chatbotCanvas');

        if (!canvas) {
            console.error('chatbotCanvas not found');
            return;
        }

        const historyEl = canvas.querySelector('[data-chat-history]');
        const formEl = canvas.querySelector('[data-chat-form]');
        const inputEl = canvas.querySelector('[data-chat-input]');
        const loadingEl = canvas.querySelector('[data-chat-loading]');
        const sendBtn = formEl?.querySelector('button[type="submit"]');
        const sendText = sendBtn?.querySelector('[data-send-text]');
        const sendingText = sendBtn?.querySelector('[data-sending-text]');

        console.log('DOM Elements found:', {
            canvas: !!canvas,
            historyEl: !!historyEl,
            formEl: !!formEl,
            inputEl: !!inputEl,
            loadingEl: !!loadingEl,
            sendBtn: !!sendBtn
        });

        // ====== Config ======
        const ENDPOINT = <?= json_encode(site_url('chat/gemini')) ?>;
        const MAX_HISTORY_MESSAGES = 50;
        const INACTIVITY_TIMEOUT_MS = 5 * 60 * 1000; // 5 นาที

        console.log('Config loaded:', {
            ENDPOINT,
            MAX_HISTORY_MESSAGES
        });

        // CSRF (ถ้าเปิด)
        const CSRF_NAME = <?= json_encode($this->security->get_csrf_token_name() ?? '') ?>;
        const CSRF_HASH = <?= json_encode($this->security->get_csrf_hash() ?? '') ?>;

        console.log('CSRF Info:', {
            CSRF_NAME,
            csrf_hash_length: CSRF_HASH ? CSRF_HASH.length : 0
        });

        // ====== State ======
        let inactivityTimer;
        let conversationHistory = [];
        let isProcessing = false;

        // ====== Helpers ======
        function addBubble(message, role, animated = true) {
            console.log('Adding bubble:', role, message.substring(0, 50) + '...');

            if (!historyEl) {
                console.error('historyEl not available');
                return;
            }

            const row = document.createElement('div');
            row.className = 'd-flex mb-3 ' + (role === 'user' ? 'justify-content-end' :
                role === 'error' || role === 'warning' ? 'justify-content-center' :
                    'justify-content-start');

            const bubble = document.createElement('div');
            bubble.className = 'chat-bubble ' + role;

            let processedMessage = (message || '')
                .replace(/\n/g, '<br>')
			    .replace(/  /g, '&nbsp;&nbsp;') // เพิ่มบรรทัดนี้ - แทนที่เว้นวรรค 2 ช่องด้วย &nbsp;
                .replace(/\*\*(.*?)\*\*/g, '<strong>$1</strong>')
                .replace(/\*(.*?)\*/g, '<em>$1</em>')
                .replace(/(https?:\/\/[^\s]+)/g, '<a href="$1" target="_blank" rel="noopener">$1</a>');

            bubble.innerHTML = processedMessage;

            if (!animated) {
                bubble.style.animation = 'none';
            }

            row.appendChild(bubble);
            historyEl.appendChild(row);

            historyEl.scrollTo({
                top: historyEl.scrollHeight,
                behavior: 'smooth'
            });

            if (role === 'ai') {
                const timestamp = document.createElement('div');
                timestamp.className = 'chat-status';
                timestamp.textContent = new Date().toLocaleTimeString('th-TH', {
                    hour: '2-digit',
                    minute: '2-digit'
                });
                historyEl.appendChild(timestamp);
            }
        }

        function setTyping(on) {
            console.log('Setting typing indicator:', on);

            if (loadingEl) {
                loadingEl.classList.toggle('d-none', !on);
            }
            if (sendBtn) {
                sendBtn.disabled = on;
            }

            if (sendText && sendingText) {
                if (on) {
                    sendText.classList.add('d-none');
                    sendingText.classList.remove('d-none');
                } else {
                    sendText.classList.remove('d-none');
                    sendingText.classList.add('d-none');
                }
            }
        }

        function resetInactivityTimer() {
            clearTimeout(inactivityTimer);
            inactivityTimer = setTimeout(() => {
                if (!isProcessing) {
                    console.log('Session timeout triggered');
                    showSessionTimeout();
                }
            }, INACTIVITY_TIMEOUT_MS);
        }

        function showSessionTimeout() {
            console.log('Showing session timeout');
            if (historyEl) {
                historyEl.innerHTML = '';
            }
            conversationHistory = [];

            addBubble('การสนทนาสิ้นสุดลงเนื่องจากไม่มีการใช้งานเป็นเวลานาน', 'ai', false);
            setTimeout(() => {
                addBubble('สวัสดีค่ะ! ผู้ช่วยญาดาพร้อมช่วยแนะนำการท่องเที่ยวเชิงสุขภาพในจังหวัดขอนแก่นอีกครั้งค่ะ', 'ai');
            }, 1000);
        }

        function showWelcomeMessage() {
            console.log('Loading welcome messages from server...');

            // โหลดข้อความต้อนรับจาก server
            fetch(<?= json_encode(site_url('chat/get_welcome_messages')) ?>, {
                method: 'GET',
                headers: {
                    'Content-Type': 'application/json'
                }
            })
                .then(response => response.json())
                .then(data => {
                    if (data.ok && data.messages) {
                        console.log('Welcome messages loaded:', data.messages.length);
                        data.messages.forEach((msg, index) => {
                            setTimeout(() => {
                                addBubble(msg, 'ai');
                            }, index * 1500);
                        });
                    } else {
                        // Fallback messages
                        const fallbackMessages = [
                            'สวัสดีค่ะ ผู้ช่วยญาดายินดีต้อนรับค่ะ',
                            'น้องสามารถให้คำแนะการใช้งานเว็บไซต์เบื้องต้น และคำถามทั่วไปได้ค่ะ มีอะไรให้ช่วยเหลือไหมคะ'
                        ];

                        fallbackMessages.forEach((msg, index) => {
                            setTimeout(() => {
                                addBubble(msg, 'ai');
                            }, index * 1500);
                        });
                    }
                })
                .catch(error => {
                    console.error('Error loading welcome messages:', error);
                    // Use fallback messages
                    const fallbackMessages = [
                        'สวัสดีค่ะ ผู้ช่วยญาดายินดีต้อนรับค่ะ',
                        'น้องสามารถให้คำแนะการใช้งานเว็บไซต์เบื้องต้น และคำถามทั่วไปได้ค่ะ มีอะไรให้ช่วยเหลือไหมคะ'
                    ];

                    fallbackMessages.forEach((msg, index) => {
                        setTimeout(() => {
                            addBubble(msg, 'ai');
                        }, index * 1500);
                    });
                });
        }

        canvas.addEventListener('shown.bs.offcanvas', function () {
            console.log('Canvas shown event triggered - Modern Theme');

            if (canvas.dataset.greeted !== '1' && historyEl.children.length === 0) {
                console.log('First time opening, showing welcome');
                showWelcomeMessage();
                canvas.dataset.greeted = '1';
            }

            resetInactivityTimer();

            setTimeout(() => {
                if (inputEl) {
                    inputEl.focus();
                }
            }, 800);
        });

        canvas.addEventListener('hidden.bs.offcanvas', function () {
            console.log('Canvas hidden - Modern Theme');
            clearTimeout(inactivityTimer);
            setTyping(false);
            showMessengerButton();
        });

        // ====== ส่งข้อความไป backend ======
        async function askBackend(prompt) {
            console.log('askBackend called with:', prompt.substring(0, 100) + '...');

            if (isProcessing) {
                console.log('Already processing, skipping');
                return;
            }

            isProcessing = true;
            setTyping(true);

            conversationHistory.push({
                role: "user",
                content: prompt
            });

            console.log('Conversation history updated, total:', conversationHistory.length);

            if (conversationHistory.length > MAX_HISTORY_MESSAGES) {
                conversationHistory = conversationHistory.slice(-MAX_HISTORY_MESSAGES);
                console.log('History trimmed to', MAX_HISTORY_MESSAGES);
            }

            const headers = {
                'Content-Type': 'application/json'
            };
            if (CSRF_NAME && CSRF_HASH) {
                headers[CSRF_NAME] = CSRF_HASH;
            }

            const requestBody = {
                message: prompt,
                history: conversationHistory
            };

            console.log('Sending request to:', ENDPOINT);

            try {
                const res = await fetch(ENDPOINT, {
                    method: 'POST',
                    headers,
                    body: JSON.stringify(requestBody)
                });

                console.log('Response received:', res.status, res.ok);

                const data = await res.json();
                console.log('Response data:', data);

                if (!res.ok) {
                    throw new Error(`HTTP ${res.status}: ${res.statusText} - ${data.error || 'Unknown error'}`);
                }

                if (data.ok && data.reply) {
                    console.log('Successful response, reply length:', data.reply.length);

                    setTimeout(() => {
                        addBubble(data.reply, 'ai');

                        conversationHistory.push({
                            role: "assistant",
                            content: data.reply
                        });

                        resetInactivityTimer();
                    }, 500);
                } else {
                    console.error('Invalid response structure:', data);
                    throw new Error(data.error || 'ไม่สามารถประมวลผลได้');
                }
            } catch (error) {
                console.error('Chat Error:', error.message);

                let errorMessage = 'เกิดข้อผิดพลาดในการเชื่อมต่อ';
                if (error.message.includes('HTTP 400')) {
                    errorMessage = 'ข้อมูลที่ส่งไม่ถูกต้อง กรุณาลองใหม่';
                } else if (error.message.includes('HTTP 429')) {
                    errorMessage = 'ใช้งานเกินขีดจำกัด กรุณารอสักครู่';
                } else if (error.message.includes('HTTP 404')) {
                    errorMessage = 'ไม่พบ endpoint chat/gemini กรุณาตรวจสอบ controller';
                } else if (error.message.includes('Failed to fetch')) {
                    errorMessage = 'ไม่สามารถเชื่อมต่อเซิร์ฟเวอร์ได้';
                }

                addBubble(`⚠️ ${errorMessage}`, 'error');
                conversationHistory.pop();
            } finally {
                setTyping(false);
                isProcessing = false;
            }
        }

        // ====== Events ======
        if (formEl) {
            formEl.addEventListener('submit', (e) => {
                console.log('Form submit - Modern Theme');
                e.preventDefault();

                const msg = (inputEl?.value || '').trim();

                if (!msg || isProcessing) {
                    return;
                }

                addBubble(msg, 'user');
                askBackend(msg);

                if (inputEl) {
                    inputEl.value = '';
                }
                resetInactivityTimer();
            });
        }

        if (inputEl) {
            inputEl.addEventListener('keypress', (e) => {
                if (e.key === 'Enter' && !e.shiftKey) {
                    e.preventDefault();
                    if (formEl) {
                        formEl.dispatchEvent(new Event('submit'));
                    }
                }
            });

            inputEl.addEventListener('input', resetInactivityTimer);
        }

        console.log('Modern White-Grey Chat System with Blur Initialized');
    });

    function closeImageSlideMid2() {
        document.querySelector('.messenger-container').style.display = 'none';
    }
</script>