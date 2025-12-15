<!-- Enhanced FlipBook Modal -->
<div class="modal fade flipbook-modal" id="flipBookModal" tabindex="-1" aria-labelledby="flipBookModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <!-- Enhanced Modal Header -->
            <div class="modal-header">
                <h5 class="modal-title" id="flipBookModalLabel">
                    <i class="bi bi-book-half"></i>
                    <span id="modalTitleText">รายงานประจำปี</span>
                </h5>
                <div class="header-controls">
                    <!-- Download Button -->
                    <button class="header-btn" onclick="downloadCurrentPDF()" title="ดาวน์โหลด PDF">
                        <i class="bi bi-download"></i>
                        <span class="d-none d-md-inline ms-1">ดาวน์โหลด</span>
                    </button>

                    <!-- Print Button -->
                    <button class="header-btn" onclick="printCurrentPDF()" title="พิมพ์">
                        <i class="bi bi-printer"></i>
                        <span class="d-none d-md-inline ms-1">พิมพ์</span>
                    </button>

                    <!-- Help Button -->
                    <button class="header-btn" onclick="showUsageHint()" title="คำแนะนำการใช้งาน">
                        <i class="bi bi-question-circle"></i>
                        <span class="d-none d-md-inline ms-1">ช่วยเหลือ</span>
                    </button>

                    <!-- Close Button -->
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close" title="ปิด (กด Esc)"></button>
                </div>
            </div>

            <!-- Modal Body -->
            <div class="modal-body">
                <div class="flipbook-wrapper">
                    <!-- Loading Overlay -->
                    <div class="loading-overlay" id="loadingOverlay">
                        <div class="loading-spinner"></div>
                        <p class="mt-3" style="font-size: 18px; color: #666; font-weight: 500;">กำลังโหลด PDF...</p>
                        <p class="text-muted">กรุณารอสักครู่</p>
                    </div>

                    <!-- Page Information -->
                    <div class="page-info" id="pageInfo">
                        หน้า 1 จาก 1
                    </div>

                    <!-- Left Side Controls -->
                    <div class="side-controls left">
                        <button class="side-btn" onclick="zoomOut()" title="ซูมออก (กด -)">
                            <i class="bi bi-zoom-out"></i>
                        </button>
                        <button class="side-btn" onclick="zoomIn()" title="ซูมเข้า (กด +)">
                            <i class="bi bi-zoom-in"></i>
                        </button>
                        <button class="side-btn" onclick="resetZoom()" title="ซูมเดิม">
                            <i class="bi bi-aspect-ratio"></i>
                        </button>
                    </div>

                    <!-- Right Side Controls -->
                    <div class="side-controls right">
                        <button class="side-btn" onclick="saveBookmark()" title="บันทึกหน้าปัจจุบัน">
                            <i class="bi bi-bookmark"></i>
                        </button>
                        <button class="side-btn" onclick="shareCurrentPage()" title="แชร์หน้าปัจจุบัน">
                            <i class="bi bi-share"></i>
                        </button>
                        <button class="side-btn" onclick="toggleNightMode()" title="โหมดกลางคืน">
                            <i class="bi bi-moon" id="nightModeIcon"></i>
                        </button>
                        <button class="side-btn" onclick="showPageJumper()" title="ไปที่หน้า...">
                            <i class="bi bi-skip-end"></i>
                        </button>
                    </div>

                    <!-- FlipBook Container -->
                    <div class="flipbook-container" id="flipbookContainer">
                        <div class="book" id="book">
                            <!-- Book Spine -->
                            <div class="book-spine"></div>

                            <!-- Left Page -->
                            <div class="page-container left" id="leftPage">
                                <div class="page" id="leftPageContent">
                                    <!-- PDF content will be rendered here -->
                                </div>
                                <div class="page-shadow"></div>
                            </div>

                            <!-- Right Page -->
                            <div class="page-container right" id="rightPage">
                                <div class="page" id="rightPageContent">
                                    <!-- PDF content will be rendered here -->
                                </div>
                                <div class="page-shadow"></div>
                            </div>
                        </div>
                    </div>

                    <!-- Main Controls -->
                    <div class="controls">
                        <!-- Previous Page - ใหม่ -->
                        <button class="control-btn new-prev-btn" id="newPrevBtn" title="หน้าก่อนหน้า (←)">
                            <i class="bi bi-chevron-left"></i>
                        </button>

                        <!-- First Page -->
                        <button class="control-btn" id="firstBtn" onclick="goToFirstPage()" disabled title="หน้าแรก (Home)">
                            <i class="bi bi-skip-start"></i>
                        </button>

                        <!-- Auto Play -->
                        <button class="control-btn" id="playBtn" onclick="toggleAutoPlay()" title="เล่นอัตโนมัติ (Space)">
                            <i class="bi bi-play-fill"></i>
                        </button>

                        <!-- Last Page -->
                        <button class="control-btn" id="lastBtn" onclick="goToLastPage()" disabled title="หน้าสุดท้าย (End)">
                            <i class="bi bi-skip-end"></i>
                        </button>

                        <!-- Next Page - ใหม่ -->
                        <button class="control-btn new-next-btn" id="newNextBtn" title="หน้าถัดไป (→)">
                            <i class="bi bi-chevron-right"></i>
                        </button>
                    </div>

                    <!-- Progress Bar -->
                    <div class="progress-bar" id="progressBar"></div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Page Jumper Modal -->
<div class="modal fade" id="pageJumperModal" tabindex="-1" aria-labelledby="pageJumperModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-sm">
        <div class="modal-content">
            <div class="modal-header">
                <h6 class="modal-title" id="pageJumperModalLabel">ไปที่หน้า</h6>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <div class="mb-3">
                    <label for="pageInput" class="form-label">หมายเลขหน้า (1-<span id="maxPageNumber">1</span>)</label>
                    <input type="number" class="form-control" id="pageInput" min="1" max="1" value="1">
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">ยกเลิก</button>
                <button type="button" class="btn btn-primary" onclick="jumpToPage()">ไป</button>
            </div>
        </div>
    </div>
</div>

<!-- Keyboard Shortcuts Help Modal -->
<div class="modal fade" id="helpModal" tabindex="-1" aria-labelledby="helpModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="helpModalLabel">
                    <i class="bi bi-keyboard"></i> คำแนะนำการใช้งาน
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <div class="row">
                    <div class="col-md-6">
                        <h6><i class="bi bi-mouse text-primary"></i> การใช้เมาส์</h6>
                        <ul class="list-unstyled">
                            <li><i class="bi bi-dot"></i> คลิกที่หน้าซ้าย/ขวา เพื่อเปลี่ยนหน้า</li>
                            <li><i class="bi bi-dot"></i> ใช้ปุ่มควบคุมด้านล่าง</li>
                            <li><i class="bi bi-dot"></i> ใช้ปุ่มด้านข้างสำหรับฟีเจอร์เพิ่มเติม</li>
                        </ul>

                        <h6><i class="bi bi-phone text-success"></i> บนมือถือ</h6>
                        <ul class="list-unstyled">
                            <li><i class="bi bi-dot"></i> ปัดนิ้วซ้าย/ขวา เพื่อเปลี่ยนหน้า</li>
                            <li><i class="bi bi-dot"></i> แตะที่หน้าซ้าย/ขวา</li>
                            <li><i class="bi bi-dot"></i> ใช้ปุ่มควบคุมด้านล่าง</li>
                        </ul>
                    </div>
                    <div class="col-md-6">
                        <h6><i class="bi bi-keyboard text-warning"></i> แป้นพิมพ์</h6>
                        <ul class="list-unstyled">
                            <li><kbd>←</kbd> <kbd>→</kbd> เปลี่ยนหน้า</li>
                            <li><kbd>Space</kbd> เล่นอัตโนมัติ</li>
                            <li><kbd>F</kbd> เต็มจอ</li>
                            <li><kbd>Esc</kbd> ปิด modal</li>
                            <li><kbd>+</kbd> <kbd>-</kbd> ซูมเข้า/ออก</li>
                            <li><kbd>Home</kbd> หน้าแรก</li>
                            <li><kbd>End</kbd> หน้าสุดท้าย</li>
                            <li><kbd>G</kbd> ไปที่หน้า</li>
                            <li><kbd>N</kbd> โหมดกลางคืน</li>
                            <li><kbd>S</kbd> แชร์</li>
                            <li><kbd>D</kbd> ดาวน์โหลด</li>
                            <li><kbd>P</kbd> พิมพ์</li>
                        </ul>

                        <h6><i class="bi bi-gear text-info"></i> ฟีเจอร์พิเศษ</h6>
                        <ul class="list-unstyled">
                            <li><i class="bi bi-dot"></i> บันทึกหน้าที่อ่านอยู่อัตโนมัติ</li>
                            <li><i class="bi bi-dot"></i> โหมดกลางคืนสำหรับอ่านในที่มืด</li>
                            <li><i class="bi bi-dot"></i> ซูมเข้า/ออกสำหรับอ่านรายละเอียด</li>
                            <li><i class="bi bi-dot"></i> แชร์หน้าที่กำลังอ่าน</li>
                        </ul>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-primary" data-bs-dismiss="modal">เข้าใจแล้ว</button>
            </div>
        </div>
    </div>
</div>

<!-- Toast Container -->
<div class="toast-container position-fixed top-0 end-0 p-3" style="z-index: 9999;">
    <!-- Toasts will be dynamically added here -->
</div>

<link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.7.2/font/bootstrap-icons.css" rel="stylesheet">
<!-- Required Scripts -->
<script src="https://cdnjs.cloudflare.com/ajax/libs/pdf.js/2.16.105/pdf.min.js"></script>

<style>
    .control-btn:disabled {
        pointer-events: none;
        /* อาจทำให้คลิกไม่ได้ */
    }

    .modal-content {
        max-width: 100%;
        margin: 30px auto;
        background: white;
        border-radius: 8px;
        padding: 0;
    }

    /* e-magazine start ================================================ */
    /* Magazine Grid Styles */
    .magazine-grid {
        display: grid;
        grid-template-columns: repeat(auto-fill, minmax(250px, 1fr));
        gap: 20px;
        margin-top: 20px;
    }

    .magazine-card {
        background: white;
        border-radius: 15px;
        overflow: hidden;
        box-shadow: 0 8px 25px rgba(0, 0, 0, 0.15);
        cursor: pointer;
        transition: all 0.3s ease;
        position: relative;
    }

    .magazine-card:hover {
        transform: translateY(-10px);
        box-shadow: 0 15px 35px rgba(0, 0, 0, 0.25);
    }

    .magazine-cover {
        width: 143px;
        height: 203px;
        border-radius: 8px;
        object-fit: cover;
        margin-right: 15px;
        box-shadow: 0 2px 8px rgba(0, 0, 0, 0.15);
    }

    .magazine-card:hover .magazine-cover {
        transform: scale(1.05);
    }

    .magazine-list {
        width: 100%;
        max-width: 1200px;
        margin: 0 auto;
    }

    #magazineContainer {
        display: grid;
        grid-template-columns: repeat(auto-fit, 420px);
        gap: 20px;
        width: 1420px;
        justify-content: start;
        margin-left: -50px;
    }

    .magazine-item {
        display: flex;
        align-items: stretch;
        padding: 15px;
        /* border: 1px solid #f0f0f0; */
        border-radius: 10px;
        background: white;
        /* box-shadow: 0 2px 8px rgba(0, 0, 0, 0.1); */
        transition: all 0.3s ease;
        min-height: 203px;
        width: 420px;
    }

    .magazine-item:last-child {
        border-bottom: none;
    }

    .magazine-content {
        flex: 1;
        display: flex;
        flex-direction: column;
        justify-content: space-between;
        min-height: 100%;
        /* เปลี่ยนเป็น min-height */
    }

    .magazine-title {
        font-size: 16px;
        font-weight: 600;
        color: #333;
        line-height: 1.3;
        display: -webkit-box;
        -webkit-line-clamp: 2;
        -webkit-box-orient: vertical;
        overflow: hidden;
        margin-bottom: 5px;
    }

    .magazine-date {
        font-size: 13px;
        color: #666;
        display: flex;
        align-items: center;
        margin-bottom: 5px;
    }

    .pdf-pages {
        font-size: 14px;
        color: #666;
        display: flex;
        align-items: center;
        margin-bottom: 5px;
    }

    .magazine-date i {
        margin-right: 5px;
    }

    .magazine-actions {
        margin-top: auto;
        display: flex;
        gap: 8px;
        align-self: flex-start;
        /* เพิ่มบรรทัดนี้ */
    }

    .btn-read {
        background: #17a2b8;
        color: white;
        border: none;
        padding: 8px 16px;
        border-radius: 20px;
        font-size: 13px;
        cursor: pointer;
        transition: all 0.3s ease;
        font-weight: 500;
        display: flex;
        align-items: center;
        gap: 5px;
    }

    .btn-read:hover {
        background: #138496;
        transform: translateY(-1px);
    }

    .btn-download {
        background: linear-gradient(135deg, #6c757d, #5a6268);
        color: white;
        border: none;
        padding: 10px 12px;
        border-radius: 8px;
        font-size: 13px;
        cursor: pointer;
        transition: all 0.3s ease;
    }

    .btn-download:hover {
        background: linear-gradient(135deg, #545b62, #495057);
        transform: translateY(-2px);
    }

    .magazine-badge {
        position: absolute;
        top: 15px;
        right: 15px;
        background: rgba(0, 0, 0, 0.8);
        color: white;
        padding: 5px 10px;
        border-radius: 15px;
        font-size: 11px;
        font-weight: 500;
    }

    /* Enhanced Modal Styles */
    .flipbook-modal {
        --bs-modal-width: 90vw;
        --bs-modal-max-width: none;
    }

    .flipbook-modal .modal-dialog {
        width: 90vw;
        max-width: none;
        margin: 20px auto;
        height: calc(100vh - 40px);
    }

    .flipbook-modal .modal-content {
        height: 100%;
        border-radius: 15px;
        overflow: hidden;
        border: none;
        box-shadow: 0 25px 50px rgba(0, 0, 0, 0.3);
    }

    .flipbook-modal .modal-body {
        padding: 0;
        height: calc(100% - 70px);
        position: relative;
    }

    /* Fullscreen Modal */
    .flipbook-modal.fullscreen .modal-dialog {
        width: 100vw;
        height: 100vh;
        margin: 0;
        max-width: none;
        max-height: none;
    }

    .flipbook-modal.fullscreen .modal-content {
        height: 100vh;
        border-radius: 0;
    }

    .flipbook-modal.fullscreen .modal-body {
        height: calc(100vh - 70px);
    }

    /* FlipBook Container Styles */
    .flipbook-wrapper {
        position: relative;
        width: 100%;
        height: 100%;
        background: linear-gradient(135deg, #f5f7fa 0%, #c3cfe2 100%);
        perspective: 1200px;
        overflow: hidden;
    }

    .flipbook-container {
        position: relative;
        width: 100%;
        height: 100%;
        display: flex;
        justify-content: center;
        align-items: center;
        padding: 20px;
    }

    .book {
        position: relative;
        width: 100%;
        max-width: 1200px;
        height: 85%;
        max-height: 700px;
        transform-style: preserve-3d;
        transition: transform 0.5s ease;
        filter: drop-shadow(0 10px 30px rgba(0, 0, 0, 0.3));
    }

    .page-container {
        position: absolute;
        width: 50%;
        height: 100%;
        background: white;
        border: 3px solid #e0e0e0;
        box-shadow:
            0 0 20px rgba(0, 0, 0, 0.1),
            inset 0 0 50px rgba(0, 0, 0, 0.05);
        transform-origin: right center;
        transition: transform 0.6s cubic-bezier(0.4, 0, 0.2, 1);
        overflow: hidden;
        border-radius: 8px;
    }

    .page-container.left {
        left: 0;
        transform-origin: right center;
        border-right: 1px solid #ddd;
    }

    .page-container.right {
        right: 0;
        transform-origin: left center;
        border-left: 1px solid #ddd;
    }

    .page {
        width: 100%;
        height: 100%;
        position: relative;
        overflow: hidden;
        background: linear-gradient(to bottom, #ffffff 0%, #fafafa 100%);
        display: flex;
        align-items: center;
        justify-content: center;
    }

    .page canvas {
        max-width: 100%;
        max-height: 100%;
        object-fit: contain;
        border-radius: 4px;
    }

    .page-number {
        position: absolute;
        bottom: 15px;
        right: 20px;
        background: rgba(0, 0, 0, 0.8);
        color: white;
        padding: 8px 15px;
        border-radius: 20px;
        font-size: 14px;
        font-weight: 600;
        backdrop-filter: blur(10px);
        box-shadow: 0 4px 15px rgba(0, 0, 0, 0.2);
    }

    .page-container.flipping {
        transform: rotateY(-180deg);
        z-index: 100;
    }

    /* Modal Header Enhanced */
    .modal-header {
        background: linear-gradient(135deg, #2c3e50 0%, #34495e 100%);
        color: white;
        border-bottom: none;
        padding: 15px 25px;
        position: relative;
        z-index: 1000;
    }

    .modal-header .modal-title {
        font-size: 18px;
        font-weight: 600;
        display: flex;
        align-items: center;
        gap: 10px;
    }

    .modal-header .btn-close {
        filter: invert(1) brightness(2);
        font-size: 18px;
        opacity: 0.8;
        transition: all 0.3s ease;
    }

    .modal-header .btn-close:hover {
        opacity: 1;
        transform: scale(1.1);
    }

    .header-controls {
        display: flex;
        gap: 10px;
        align-items: center;
    }

    .header-btn {
        background: rgba(255, 255, 255, 0.2);
        color: white;
        border: none;
        padding: 10px 15px;
        border-radius: 8px;
        cursor: pointer;
        font-size: 14px;
        transition: all 0.3s ease;
        backdrop-filter: blur(10px);
        font-weight: 500;
    }

    .header-btn:hover {
        background: rgba(255, 255, 255, 0.3);
        transform: translateY(-2px);
    }

    .header-btn:active {
        transform: translateY(0);
    }

    /* Controls Enhanced */
    .controls {
        position: absolute;
        bottom: 25px;
        left: 50%;
        transform: translateX(-50%);
        display: flex;
        gap: 15px;
        z-index: 1000;
        background: rgba(0, 0, 0, 0.8);
        padding: 15px 25px;
        border-radius: 50px;
        backdrop-filter: blur(15px);
        box-shadow: 0 8px 25px rgba(0, 0, 0, 0.3);
    }

    .control-btn {
        background: transparent;
        color: white;
        border: 2px solid rgba(255, 255, 255, 0.3);
        padding: 12px 18px;
        border-radius: 50%;
        cursor: pointer;
        transition: all 0.3s ease;
        font-size: 18px;
        width: 50px;
        height: 50px;
        display: flex;
        align-items: center;
        justify-content: center;
    }

    .control-btn:hover:not(:disabled) {
        background: rgba(255, 255, 255, 0.2);
        border-color: rgba(255, 255, 255, 0.6);
        transform: translateY(-3px);
        box-shadow: 0 6px 20px rgba(0, 0, 0, 0.4);
    }

    .control-btn:disabled {
        background: rgba(255, 255, 255, 0.1);
        border-color: rgba(255, 255, 255, 0.1);
        color: rgba(255, 255, 255, 0.3);
        cursor: not-allowed;
        transform: none;
        box-shadow: none;
    }

    .control-btn:active:not(:disabled) {
        transform: translateY(-1px);
    }

    /* Page Info Enhanced */
    .page-info {
        position: absolute;
        top: 20px;
        left: 50%;
        transform: translateX(-50%);
        background: rgba(0, 0, 0, 0.8);
        color: white;
        padding: 12px 25px;
        border-radius: 25px;
        font-size: 16px;
        z-index: 1000;
        font-weight: 600;
        backdrop-filter: blur(15px);
        box-shadow: 0 4px 15px rgba(0, 0, 0, 0.2);
        transition: all 0.3s ease;
    }

    /* Side Controls Enhanced */
    .side-controls {
        position: absolute;
        top: 50%;
        transform: translateY(-50%);
        z-index: 1000;
        display: flex;
        flex-direction: column;
        gap: 10px;
    }

    .side-controls.left {
        left: 20px;
    }

    .side-controls.right {
        right: 20px;
    }

    .side-btn {
        background: rgba(0, 0, 0, 0.7);
        color: white;
        border: none;
        padding: 15px 12px;
        border-radius: 12px;
        cursor: pointer;
        font-size: 18px;
        transition: all 0.3s ease;
        backdrop-filter: blur(10px);
        box-shadow: 0 4px 15px rgba(0, 0, 0, 0.2);
        width: 50px;
        height: 50px;
        display: flex;
        align-items: center;
        justify-content: center;
    }

    .side-btn:hover {
        background: rgba(0, 0, 0, 0.9);
        transform: scale(1.1);
        box-shadow: 0 6px 20px rgba(0, 0, 0, 0.3);
    }

    /* Progress Bar Enhanced */
    .progress-bar {
        position: absolute;
        bottom: 0;
        left: 0;
        height: 6px;
        background: linear-gradient(90deg, #007bff, #0056b3);
        transition: width 0.3s ease;
        z-index: 1000;
        border-radius: 0 0 15px 15px;
        box-shadow: 0 -2px 10px rgba(0, 123, 255, 0.3);
    }

    /* Loading Overlay Enhanced */
    .loading-overlay {
        position: absolute;
        top: 0;
        left: 0;
        right: 0;
        bottom: 0;
        background: rgba(255, 255, 255, 0.95);
        display: flex;
        justify-content: center;
        align-items: center;
        flex-direction: column;
        z-index: 2000;
        backdrop-filter: blur(10px);
    }

    .loading-spinner {
        width: 80px;
        height: 80px;
        border: 8px solid #f3f3f3;
        border-top: 8px solid #007bff;
        border-radius: 50%;
        animation: spin 1s linear infinite;
    }

    @keyframes spin {
        0% {
            transform: rotate(0deg);
        }

        100% {
            transform: rotate(360deg);
        }
    }

    /* Book Spine Enhanced */
    .book-spine {
        position: absolute;
        left: 50%;
        top: 5%;
        width: 6px;
        height: 90%;
        background: linear-gradient(to bottom,
                #2c3e50 0%,
                #34495e 25%,
                #2c3e50 50%,
                #34495e 75%,
                #2c3e50 100%);
        transform: translateX(-50%);
        z-index: 500;
        box-shadow:
            0 0 20px rgba(0, 0, 0, 0.4),
            inset 0 0 10px rgba(255, 255, 255, 0.1);
        border-radius: 3px;
    }

    .page-shadow {
        position: absolute;
        top: 0;
        width: 40px;
        height: 100%;
        background: linear-gradient(to right, rgba(0, 0, 0, 0.2), transparent);
        pointer-events: none;
        transition: opacity 0.3s ease;
    }

    .page-container.left .page-shadow {
        right: 0;
    }

    .page-container.right .page-shadow {
        left: 0;
        background: linear-gradient(to left, rgba(0, 0, 0, 0.2), transparent);
    }

    /* Empty State */
    .empty-state {
        text-align: center;
        padding: 60px 20px;
        color: rgba(255, 255, 255, 0.8);
        grid-column: 1 / -1;
    }

    .empty-state i {
        font-size: 4rem;
        margin-bottom: 20px;
        opacity: 0.6;
    }

    .empty-state h4 {
        color: white;
        margin-bottom: 15px;
    }

    /* Responsive Design */
    @media (max-width: 768px) {
        .flipbook-modal .modal-dialog {
            width: 100vw;
            height: 100vh;
            margin: 0;
        }

        .flipbook-modal .modal-content {
            height: 100vh;
            border-radius: 0;
        }

        .flipbook-modal .modal-body {
            height: calc(100vh - 70px);
        }

        .book {
            width: 95%;
            height: 75%;
        }

        .controls {
            bottom: 20px;
            gap: 10px;
            padding: 12px 20px;
        }

        .control-btn {
            padding: 10px 14px;
            font-size: 16px;
            width: 45px;
            height: 45px;
        }

        .side-controls {
            display: none;
        }

        .magazine-grid {
            grid-template-columns: repeat(auto-fill, minmax(200px, 1fr));
        }

        .page-info {
            font-size: 14px;
            padding: 10px 20px;
        }

        .modal-header {
            padding: 12px 20px;
        }

        .modal-header .modal-title {
            font-size: 16px;
        }

        .header-btn {
            padding: 8px 12px;
            font-size: 13px;
        }
    }

    /* e-magazine end ================================================ */
    .magazine-info {
        padding-top: 30px;
    }

    ._df_thumb {
        width: 150px !important;
        height: 200px !important;
        flex-shrink: 0;
        border-radius: 8px;
        overflow: hidden;
        margin-right: 20px;
        cursor: pointer;
        background-size: cover;
        background-position: center;
        transition: transform 0.3s ease;
    }

    ._df_thumb:hover {
        transform: scale(1.05);
    }

    /* ซ่อน DFlip UI ที่ไม่ต้องการในโหมด thumbnail */
    ._df_thumb .df-ui {
        opacity: 0;
        transition: opacity 0.3s ease;
    }

    ._df_thumb:hover .df-ui {
        opacity: 1;
    }
</style>

<script>
    // Enhanced Magazine Modal JavaScript - Part 1
    // PDF.js worker configuration
    pdfjsLib.GlobalWorkerOptions.workerSrc = 'https://cdnjs.cloudflare.com/ajax/libs/pdf.js/2.16.105/pdf.worker.min.js';

    // Global variables
    let currentPDF = null;
    let currentPageNum = 1;
    let totalPages = 0;
    let isFlipping = false;
    let zoomLevel = 1;
    let autoPlayInterval = null;
    let isAutoPlaying = false;
    let currentMagazineTitle = '';
    let currentPdfUrl = '';
    let flipBookModal = null;
    let pageJumperModal = null;
    let helpModal = null;
    let isNightMode = false;
    let isFullscreen = false;

    // ข้อมูลรายงานประจำปีจาก PHP (เหมือนโค้ดเดิม)
    const magazinesData = <?php echo json_encode($e_mags ?? []); ?>;

    // โหลดข้อมูลรายงานประจำปีเมื่อเริ่มต้น
    document.addEventListener('DOMContentLoaded', function() {
        initializeModals();
        initializeEventListeners();
        loadMagazines();
        console.log('Enhanced Magazine Modal system initialized');
    });

    // Initialize Bootstrap modals
    function initializeModals() {
        flipBookModal = new bootstrap.Modal(document.getElementById('flipBookModal'), {
            backdrop: true, // เปลี่ยนจาก 'static' เป็น true เพื่อให้คลิกนอก modal ปิดได้
            keyboard: true // เปลี่ยนจาก false เป็น true เพื่อให้กด ESC ปิดได้
        });

        pageJumperModal = new bootstrap.Modal(document.getElementById('pageJumperModal'));
        helpModal = new bootstrap.Modal(document.getElementById('helpModal'));
    }
    // Initialize event listeners
    function initializeEventListeners() {
        // Modal close event
        document.getElementById('flipBookModal').addEventListener('hidden.bs.modal', function() {
            resetModalState();
        });

        // Page jumper modal
        document.getElementById('pageInput').addEventListener('keypress', function(e) {
            if (e.key === 'Enter') {
                jumpToPage();
            }
        });

        // Touch/swipe support
        initializeTouchSupport();

        // Click navigation on pages
        document.getElementById('leftPage').addEventListener('click', function(e) {
            e.preventDefault();
            prevPage();
        });

        document.getElementById('rightPage').addEventListener('click', function(e) {
            e.preventDefault();
            nextPage();
        });

        // Prevent context menu in modal
        document.getElementById('flipBookModal').addEventListener('contextmenu', function(e) {
            e.preventDefault();
        });

        // Prevent text selection in modal
        document.getElementById('flipBookModal').addEventListener('selectstart', function(e) {
            e.preventDefault();
        });

        // Window resize handler
        window.addEventListener('resize', function() {
            if (document.getElementById('flipBookModal').classList.contains('show') && currentPDF) {
                setTimeout(() => {
                    updatePages();
                }, 300);
            }
        });
    }

    // แสดงผลรายการรายงานประจำปี (ใช้ข้อมูลจาก PHP เหมือนโค้ดเดิม)
    function loadMagazines() {
        const container = document.getElementById('magazineContainer');

        if (!magazinesData || magazinesData.length === 0) {
            container.innerHTML = `
            <div class="empty-state">
                <i class="bi bi-inbox"></i>
                <h4>ยังไม่มีรายงานประจำปี</h4>
                <p>กรุณาเพิ่มรายงานประจำปีในระบบ backend</p>
            </div>
        `;
            return;
        }

        container.innerHTML = '';

        magazinesData.forEach((magazine, index) => {
            const item = document.createElement('div');
            item.className = 'magazine-item';

            const coverUrl = magazine.cover_image ?
                `<?= base_url('docs/img/'); ?>${magazine.cover_image}` :
                `<?= base_url('docs/default_cover.png'); ?>`;
            const pdfUrl = `<?= base_url('docs/file/'); ?>${magazine.file_name}`;

            item.innerHTML = `
    <div class="_df_thumb" 
         source="${pdfUrl}"
         tags="ebook,pdf" 
         thumb="${coverUrl}"
         title="${magazine.original_name.replace(/"/g, '&quot;')}"
         onerror="this.setAttribute('thumb', '<?= base_url('docs/default_cover.png'); ?>')">
        ${magazine.original_name}
    </div>
    <div class="magazine-info">
        <!-- เพิ่มชื่อนิตยสาร -->
        <div class="magazine-title">
            ${magazine.original_name}
        </div>
        
        <div class="magazine-meta">
            <!-- วันที่ -->
            <div class="magazine-date">
                <i class="bi bi-calendar-event"></i>
                ${formatDate(magazine.uploaded_at)}
            </div>
            
            <!-- จำนวนหน้า -->
            <div class="magazine-pages" id="pages-${magazine.id}">
                <i class="bi bi-file-earmark-text"></i>
                <span class="loading-text">กำลังตรวจสอบ...</span>
            </div>
            
            <!-- ผู้เข้าชม -->
            <div class="magazine-viewers">
                <i class="bi bi-eye"></i>
                ผู้เข้าชม ${magazine.visitors || 0} ครั้ง
            </div>
        </div>
    </div>
`;

            container.appendChild(item);
            loadPDFPageCount(pdfUrl, magazine.id);
        });
    }

    // ฟังก์ชันสำหรับดึงจำนวนหน้า PDF
    async function loadPDFPageCount(pdfUrl, magazineId) {
        try {
            const pagesElement = document.getElementById(`pages-${magazineId}`);

            // โหลด PDF เพื่อดูจำนวนหน้า
            const pdf = await pdfjsLib.getDocument(pdfUrl).promise;
            const pageCount = pdf.numPages;

            // อัปเดตข้อความแสดงจำนวนหน้า
            if (pagesElement) {
                pagesElement.innerHTML = `
                <i class="bi bi-file-earmark-text"></i>
                <span>${pageCount} หน้า</span>
            `;
            }

        } catch (error) {
            console.error('Error loading PDF page count:', error);

            // แสดงข้อความเมื่อไม่สามารถโหลดได้
            const pagesElement = document.getElementById(`pages-${magazineId}`);
            if (pagesElement) {
                pagesElement.innerHTML = `
                <i class="bi bi-exclamation-triangle text-warning"></i>
                <span>ไม่สามารถโหลดได้</span>
            `;
            }
        }
    }

    // แปลงวันที่เป็นรูปแบบไทย (เหมือนโค้ดเดิม)
    function formatDate(dateString) {
        const date = new Date(dateString);
        const thaiYear = date.getFullYear() + 543;
        const months = [
            'ม.ค.', 'ก.พ.', 'มี.ค.', 'เม.ย.', 'พ.ค.', 'มิ.ย.',
            'ก.ค.', 'ส.ค.', 'ก.ย.', 'ต.ค.', 'พ.ย.', 'ธ.ค.'
        ];

        return `${date.getDate()} ${months[date.getMonth()]} ${thaiYear}`;
    }

    // เปิด FlipBook Modal
    // function openFlipBookModal(pdfUrl, title, magazineId) {
    //     console.log('Opening modal with:', {
    //         pdfUrl: pdfUrl,
    //         title: title,
    //         magazineId: magazineId
    //     });

    //     currentPdfUrl = pdfUrl;
    //     currentMagazineTitle = title;

    //     // อัปเดตชื่อใน modal header
    //     document.getElementById('modalTitleText').textContent = title;

    //     // เพิ่มยอดเข้าชมแบบง่ายๆ
    //     if (magazineId) {
    //         incrementViewCount(magazineId);
    //     }

    //     // แสดง modal
    //     flipBookModal.show();

    //     // โหลด PDF
    //     loadPDF(pdfUrl, magazineId);
    // }


    function openFlipBookModalFixed(pdfUrl, title, magazineId, savedScrollPosition = null) {
        console.log('Opening modal with:', {
            pdfUrl: pdfUrl,
            title: title,
            magazineId: magazineId
        });

        currentPdfUrl = pdfUrl;
        currentMagazineTitle = title;

        // อัปเดตชื่อใน modal header
        document.getElementById('modalTitleText').textContent = title;

        // เพิ่มยอดเข้าชม
        if (magazineId) {
            incrementViewCount(magazineId);
        }

        // เก็บตำแหน่งปัจจุบันถ้าไม่มีที่ส่งมา
        if (!savedScrollPosition) {
            savedScrollPosition = {
                x: window.scrollX,
                y: window.scrollY
            };
        }

        // เพิ่ม class ป้องกันการเลื่อน
        document.body.style.overflow = 'hidden';
        document.body.style.position = 'fixed';
        document.body.style.top = `-${savedScrollPosition.y}px`;
        document.body.style.width = '100%';

        // แสดง modal
        flipBookModal.show();

        // คืนตำแหน่งการเลื่อนเมื่อปิด modal
        const flipBookModalElement = document.getElementById('flipBookModal');

        const restoreScroll = () => {
            document.body.style.overflow = '';
            document.body.style.position = '';
            document.body.style.top = '';
            document.body.style.width = '';
            window.scrollTo(savedScrollPosition.x, savedScrollPosition.y);

            // ลบ listener หลังจากใช้แล้ว
            flipBookModalElement.removeEventListener('hidden.bs.modal', restoreScroll);
        };

        flipBookModalElement.addEventListener('hidden.bs.modal', restoreScroll);

        // โหลด PDF
        loadPDF(pdfUrl, magazineId);
    }

    // ฟังก์ชันง่ายๆ สำหรับนับยอดเข้าชม
    function incrementViewCount(magazineId) {
        if (!magazineId) return;

        console.log('Incrementing view count for magazine ID:', magazineId);

        // ส่ง request ไปที่ increment_e_mag_view
        const url = '<?= base_url("Pages/increment_e_mag_view/"); ?>' + magazineId;

        // ใช้ image tracking แบบง่ายๆ (ไม่ต้องรอ response)
        const img = new Image();
        img.onload = function() {
            console.log('View count updated successfully for magazine ID:', magazineId);
        };
        img.onerror = function() {
            console.log('View count request sent for magazine ID:', magazineId);
        };
        img.src = url + '?t=' + Date.now();
    }

    // โหลด PDF
    function loadPDF(pdfUrl, magazineId) {
        const loadingOverlay = document.getElementById('loadingOverlay');
        loadingOverlay.style.display = 'flex';

        // รีเซ็ตค่า
        resetPDFState();

        pdfjsLib.getDocument(pdfUrl).promise.then(pdf => {
            currentPDF = pdf;
            totalPages = pdf.numPages;
            currentPageNum = 1;

            // Update page jumper max value
            document.getElementById('maxPageNumber').textContent = totalPages;
            document.getElementById('pageInput').max = totalPages;

            updatePages();
            loadingOverlay.style.display = 'none';

            // โหลด bookmark
            setTimeout(() => {
                loadBookmark(magazineId);
            }, 500);

            showToast('โหลด PDF สำเร็จ', 'success');

        }).catch(error => {
            console.error('Error loading PDF:', error);
            loadingOverlay.innerHTML = `
            <div class="text-center">
                <i class="bi bi-exclamation-triangle text-danger" style="font-size: 3rem;"></i>
                <h4 class="mt-3">ไม่สามารถโหลดไฟล์ PDF ได้</h4>
                <p>กรุณาตรวจสอบไฟล์และลองอีกครั้ง</p>
                <button class="btn btn-primary" onclick="loadPDF('${pdfUrl}', ${magazineId})">
                    <i class="bi bi-arrow-clockwise"></i> ลองอีกครั้ง
                </button>
            </div>
        `;
        });
    }

    // Reset PDF state
    function resetPDFState() {
        currentPDF = null;
        currentPageNum = 1;
        totalPages = 0;
        zoomLevel = 1;
        isFlipping = false;

        // Stop auto play if running
        if (isAutoPlaying) {
            toggleAutoPlay();
        }
    }

    // Create canvas for PDF page
    // แก้ไขฟังก์ชัน createPageCanvas ให้ใช้ scale คงที่
    function createPageCanvas(pageNum) {
        return new Promise((resolve, reject) => {
            if (!currentPDF || pageNum < 1 || pageNum > totalPages) {
                resolve(null);
                return;
            }

            currentPDF.getPage(pageNum).then(page => {
                const canvas = document.createElement('canvas');
                const ctx = canvas.getContext('2d');

                // ใช้ scale คงที่สำหรับ canvas (ไม่ใช้ zoomLevel ที่นี่)
                const scale = 1.5; // scale คงที่สำหรับคุณภาพของ canvas

                const viewport = page.getViewport({
                    scale: scale
                });
                canvas.height = viewport.height;
                canvas.width = viewport.width;

                // เพิ่ม CSS เพื่อให้ canvas ปรับขนาดได้
                canvas.style.maxWidth = '100%';
                canvas.style.maxHeight = '100%';
                canvas.style.objectFit = 'contain';

                const renderContext = {
                    canvasContext: ctx,
                    viewport: viewport
                };

                page.render(renderContext).promise.then(() => {
                    resolve(canvas);
                }).catch(error => {
                    console.error('Error rendering page:', error);
                    reject(error);
                });
            }).catch(error => {
                console.error('Error getting page:', error);
                reject(error);
            });
        });
    }
    // Update pages display
    async function updatePages() {
        if (!currentPDF) {
            console.log('No PDF loaded');
            return Promise.resolve();
        }

        console.log(`Updating pages - Current page: ${currentPageNum}, Zoom: ${zoomLevel}`);

        const leftPageContainer = document.getElementById('leftPageContent');
        const rightPageContainer = document.getElementById('rightPageContent');

        // แสดง loading indicator เฉพาะเมื่อไม่มี content
        if (!leftPageContainer.querySelector('canvas') && !rightPageContainer.querySelector('canvas')) {
            leftPageContainer.innerHTML = '<div class="d-flex justify-content-center align-items-center h-100"><div class="spinner-border text-primary" role="status"></div></div>';
            rightPageContainer.innerHTML = '<div class="d-flex justify-content-center align-items-center h-100"><div class="spinner-border text-primary" role="status"></div></div>';
        }

        try {
            // Clear existing content
            leftPageContainer.innerHTML = '';
            rightPageContainer.innerHTML = '';

            // Left page (previous page)
            if (currentPageNum > 1) {
                const leftCanvas = await createPageCanvas(currentPageNum - 1);
                if (leftCanvas) {
                    leftPageContainer.appendChild(leftCanvas);

                    const leftPageNum = document.createElement('div');
                    leftPageNum.className = 'page-number';
                    leftPageNum.textContent = currentPageNum - 1;
                    leftPageContainer.appendChild(leftPageNum);
                }
            }

            // Right page (current page)
            if (currentPageNum <= totalPages) {
                const rightCanvas = await createPageCanvas(currentPageNum);
                if (rightCanvas) {
                    rightPageContainer.appendChild(rightCanvas);

                    const rightPageNum = document.createElement('div');
                    rightPageNum.className = 'page-number';
                    rightPageNum.textContent = currentPageNum;
                    rightPageContainer.appendChild(rightPageNum);
                }
            }

            // ใช้ zoom ที่มีอยู่แล้ว
            applyZoomToBook();

            updateUI();
            saveBookmark();

            console.log('Pages updated successfully');
            return Promise.resolve();

        } catch (error) {
            console.error('Error updating pages:', error);

            // แสดงข้อผิดพลาด
            leftPageContainer.innerHTML = '<div class="text-center text-danger p-3"><i class="bi bi-exclamation-triangle"></i><br>เกิดข้อผิดพลาด</div>';
            rightPageContainer.innerHTML = '<div class="text-center text-danger p-3"><i class="bi bi-exclamation-triangle"></i><br>เกิดข้อผิดพลาด</div>';

            showToast('เกิดข้อผิดพลาดในการแสดงหน้า', 'error');
            return Promise.reject(error);
        }
    }

    // Update UI elements
    function updateUI() {
        updatePageInfo();
        updateButtons();
        updateProgress();
    }

    function updatePageInfo() {
        document.getElementById('pageInfo').textContent = `หน้า ${currentPageNum} จาก ${totalPages}`;
    }

    function updateButtons() {
        // ปุ่มเก่า
        const prevBtn = document.getElementById('prevBtn');
        const nextBtn = document.getElementById('nextBtn');
        const firstBtn = document.getElementById('firstBtn');
        const lastBtn = document.getElementById('lastBtn');

        if (prevBtn) prevBtn.disabled = currentPageNum <= 1;
        if (nextBtn) nextBtn.disabled = currentPageNum >= totalPages;
        if (firstBtn) firstBtn.disabled = currentPageNum <= 1;
        if (lastBtn) lastBtn.disabled = currentPageNum >= totalPages;

        // เรียกใช้ปุ่มใหม่ด้วย
        updateNewButtons();
    }

    // ฟังก์ชันอัปเดตปุ่มใหม่
    function updateNewButtons() {
        const newPrevBtn = document.getElementById('newPrevBtn');
        const newNextBtn = document.getElementById('newNextBtn');

        if (!newPrevBtn || !newNextBtn) return;

        // Previous button
        if (currentPageNum > 1) {
            newPrevBtn.style.opacity = '1';
            newPrevBtn.style.pointerEvents = 'auto';
        } else {
            newPrevBtn.style.opacity = '0.5';
            newPrevBtn.style.pointerEvents = 'none';
        }

        // Next button  
        if (currentPageNum < totalPages) {
            newNextBtn.style.opacity = '1';
            newNextBtn.style.pointerEvents = 'auto';
        } else {
            newNextBtn.style.opacity = '0.5';
            newNextBtn.style.pointerEvents = 'none';
        }
    }

    function updateProgress() {
        const progress = totalPages > 0 ? (currentPageNum / totalPages) * 100 : 0;
        document.getElementById('progressBar').style.width = `${progress}%`;
    }

    // Magazine JavaScript Part 2 - Navigation & Controls

    // Navigation functions
    function prevPage() {
        if (currentPageNum > 1 && !isFlipping) {
            performPageFlip(() => {
                currentPageNum--;
                updatePages();
            });
        }
    }

    function nextPage() {
        if (currentPageNum < totalPages && !isFlipping) {
            performPageFlip(() => {
                currentPageNum++;
                updatePages();
            });
        }
    }

    function goToFirstPage() {
        if (currentPageNum > 1 && !isFlipping) {
            performPageFlip(() => {
                currentPageNum = 1;
                updatePages();
            });
        }
    }

    function goToLastPage() {
        if (currentPageNum < totalPages && !isFlipping) {
            performPageFlip(() => {
                currentPageNum = totalPages;
                updatePages();
            });
        }
    }

    // Perform page flip animation
    function performPageFlip(callback) {
        isFlipping = true;

        const rightPage = document.getElementById('rightPage');
        rightPage.classList.add('flipping');

        setTimeout(() => {
            callback();
            rightPage.classList.remove('flipping');
            isFlipping = false;
        }, 300);
    }

    // Auto play functionality
    function toggleAutoPlay() {
        const playBtn = document.getElementById('playBtn');
        const icon = playBtn.querySelector('i');

        if (isAutoPlaying) {
            clearInterval(autoPlayInterval);
            icon.className = 'bi bi-play-fill';
            isAutoPlaying = false;
            showToast('หยุดการเล่นอัตโนมัติ', 'info');
        } else {
            autoPlayInterval = setInterval(() => {
                if (currentPageNum < totalPages) {
                    nextPage();
                } else {
                    toggleAutoPlay(); // Stop when reaching the end
                }
            }, 3000);
            icon.className = 'bi bi-pause-fill';
            isAutoPlaying = true;
            showToast('เริ่มการเล่นอัตโนมัติ', 'info');
        }
    }

    // Zoom functions
    function zoomIn() {
        if (zoomLevel < 5) {
            const oldZoom = zoomLevel;
            zoomLevel = Math.min(zoomLevel + 0.25, 5);

            console.log(`Zooming in from ${oldZoom} to ${zoomLevel}`);

            // ขยายทั้ง book container
            applyZoomToBook();
            showToast(`ซูมเข้า ${Math.round(zoomLevel * 100)}%`, 'info');
        } else {
            showToast('ซูมเข้าสูงสุดแล้ว', 'warning');
        }
    }

    function zoomOut() {
        if (zoomLevel > 0.25) {
            const oldZoom = zoomLevel;
            zoomLevel = Math.max(zoomLevel - 0.25, 0.25);

            console.log(`Zooming out from ${oldZoom} to ${zoomLevel}`);

            // ขยายทั้ง book container
            applyZoomToBook();
            showToast(`ซูมออก ${Math.round(zoomLevel * 100)}%`, 'info');
        } else {
            showToast('ซูมออกต่ำสุดแล้ว', 'warning');
        }
    }

    function resetZoom() {
        const oldZoom = zoomLevel;
        zoomLevel = 1;

        console.log(`Resetting zoom from ${oldZoom} to ${zoomLevel}`);

        // รีเซ็ต book container
        applyZoomToBook();
        showToast('รีเซ็ตซูมเป็นขนาดปกติ', 'info');
    }

    function applyZoomToBook() {
        const book = document.getElementById('book');
        const flipbookContainer = document.getElementById('flipbookContainer');

        if (book && flipbookContainer) {
            // ใช้ CSS transform scale เพื่อขยายทั้งหน้า
            book.style.transform = `scale(${zoomLevel})`;
            book.style.transformOrigin = 'center center';

            // ปรับ overflow ของ container ตาม zoom level
            if (zoomLevel > 1) {
                flipbookContainer.style.overflow = 'auto';
                flipbookContainer.style.cursor = 'grab';

                // เพิ่มการ drag เมื่อ zoom เข้า
                enableDragWhenZoomed();
            } else {
                flipbookContainer.style.overflow = 'hidden';
                flipbookContainer.style.cursor = 'default';

                // ปิดการ drag เมื่อ zoom ปกติ
                disableDragWhenZoomed();
            }

            console.log(`Applied zoom ${zoomLevel} to book container`);
        }
    }

    // ตัวแปรสำหรับการ drag
    let isDragging = false;
    let dragStartX = 0;
    let dragStartY = 0;
    let scrollStartX = 0;
    let scrollStartY = 0;

    // เปิดใช้งานการ drag เมื่อ zoom เข้า
    function enableDragWhenZoomed() {
        const flipbookContainer = document.getElementById('flipbookContainer');

        if (flipbookContainer && !flipbookContainer.hasAttribute('data-drag-enabled')) {
            flipbookContainer.setAttribute('data-drag-enabled', 'true');

            flipbookContainer.addEventListener('mousedown', startDrag);
            flipbookContainer.addEventListener('mousemove', handleDrag);
            flipbookContainer.addEventListener('mouseup', endDrag);
            flipbookContainer.addEventListener('mouseleave', endDrag);

            // สำหรับ touch devices
            flipbookContainer.addEventListener('touchstart', startDragTouch, {
                passive: false
            });
            flipbookContainer.addEventListener('touchmove', handleDragTouch, {
                passive: false
            });
            flipbookContainer.addEventListener('touchend', endDrag);
        }
    }

    // ปิดการ drag เมื่อ zoom ปกติ
    function disableDragWhenZoomed() {
        const flipbookContainer = document.getElementById('flipbookContainer');

        if (flipbookContainer && flipbookContainer.hasAttribute('data-drag-enabled')) {
            flipbookContainer.removeAttribute('data-drag-enabled');

            flipbookContainer.removeEventListener('mousedown', startDrag);
            flipbookContainer.removeEventListener('mousemove', handleDrag);
            flipbookContainer.removeEventListener('mouseup', endDrag);
            flipbookContainer.removeEventListener('mouseleave', endDrag);

            flipbookContainer.removeEventListener('touchstart', startDragTouch);
            flipbookContainer.removeEventListener('touchmove', handleDragTouch);
            flipbookContainer.removeEventListener('touchend', endDrag);
        }
    }

    // ฟังก์ชันการ drag ด้วย mouse
    function startDrag(e) {
        // ไม่ให้ drag ถ้าคลิกที่ปุ่มควบคุม
        if (e.target.closest('.controls') || e.target.closest('.side-controls') || e.target.closest('.side-btn')) {
            return;
        }

        isDragging = true;
        dragStartX = e.clientX;
        dragStartY = e.clientY;
        scrollStartX = e.target.scrollLeft || 0;
        scrollStartY = e.target.scrollTop || 0;

        document.getElementById('flipbookContainer').style.cursor = 'grabbing';
        e.preventDefault();
    }

    function handleDrag(e) {
        if (!isDragging) return;

        const flipbookContainer = document.getElementById('flipbookContainer');
        const deltaX = dragStartX - e.clientX;
        const deltaY = dragStartY - e.clientY;

        flipbookContainer.scrollLeft = scrollStartX + deltaX;
        flipbookContainer.scrollTop = scrollStartY + deltaY;

        e.preventDefault();
    }

    function endDrag() {
        isDragging = false;
        const flipbookContainer = document.getElementById('flipbookContainer');
        if (flipbookContainer) {
            flipbookContainer.style.cursor = zoomLevel > 1 ? 'grab' : 'default';
        }
    }

    // ฟังก์ชันการ drag ด้วย touch
    function startDragTouch(e) {
        if (e.touches.length === 1) {
            const touch = e.touches[0];
            startDrag({
                clientX: touch.clientX,
                clientY: touch.clientY,
                target: e.target,
                preventDefault: () => e.preventDefault()
            });
        }
    }

    function handleDragTouch(e) {
        if (e.touches.length === 1 && isDragging) {
            const touch = e.touches[0];
            handleDrag({
                clientX: touch.clientX,
                clientY: touch.clientY,
                preventDefault: () => e.preventDefault()
            });
        }
    }

    // Fullscreen toggle
    function toggleModalFullscreen() {
        const modal = document.getElementById('flipBookModal');
        const icon = document.getElementById('fullscreenIcon');
        const text = document.getElementById('fullscreenText');

        if (isFullscreen) {
            modal.classList.remove('fullscreen');
            icon.className = 'bi bi-fullscreen';
            if (text) text.textContent = 'เต็มจอ';
            isFullscreen = false;
            showToast('ออกจากโหมดเต็มจอ', 'info');
        } else {
            modal.classList.add('fullscreen');
            icon.className = 'bi bi-fullscreen-exit';
            if (text) text.textContent = 'ออกจากเต็มจอ';
            isFullscreen = true;
            showToast('เข้าสู่โหมดเต็มจอ', 'info');
        }

        // รักษา zoom level หลังจากเปลี่ยน fullscreen
        setTimeout(() => {
            if (currentPDF) {
                applyZoomToBook();
            }
        }, 300);
    }

    // Night mode toggle
    function toggleNightMode() {
        const icon = document.getElementById('nightModeIcon');
        const flipbookWrapper = document.querySelector('.flipbook-wrapper');

        if (isNightMode) {
            icon.className = 'bi bi-moon';
            flipbookWrapper.style.filter = 'none';
            isNightMode = false;
            showToast('ปิดโหมดกลางคืน', 'info');
        } else {
            icon.className = 'bi bi-sun';
            flipbookWrapper.style.filter = 'invert(1) hue-rotate(180deg)';
            isNightMode = true;
            showToast('เปิดโหมดกลางคืน', 'info');
        }
    }

    // Page jumper functionality
    function showPageJumper() {
        document.getElementById('pageInput').value = currentPageNum;
        pageJumperModal.show();

        // Focus on input after modal is shown
        setTimeout(() => {
            document.getElementById('pageInput').focus();
            document.getElementById('pageInput').select();
        }, 300);
    }

    function jumpToPage() {
        const pageInput = document.getElementById('pageInput');
        const pageNumber = parseInt(pageInput.value);

        if (pageNumber >= 1 && pageNumber <= totalPages && pageNumber !== currentPageNum) {
            currentPageNum = pageNumber;
            updatePages();
            showToast(`ไปที่หน้า ${pageNumber}`, 'success');
        } else if (pageNumber === currentPageNum) {
            showToast('คุณอยู่ที่หน้านี้แล้ว', 'warning');
            return;
        } else {
            showToast('หมายเลขหน้าไม่ถูกต้อง', 'error');
            return;
        }

        pageJumperModal.hide();
    }

    // Download and print functions
    function downloadCurrentPDF() {
        if (currentPdfUrl && currentMagazineTitle) {
            downloadPDF(currentPdfUrl, currentMagazineTitle);
        } else {
            showToast('ไม่พบไฟล์ PDF สำหรับดาวน์โหลด', 'error');
        }
    }

    // ดาวน์โหลด PDF (เหมือนโค้ดเดิม)
    function downloadPDF(pdfUrl, title) {
        try {
            const link = document.createElement('a');
            link.href = pdfUrl;
            link.download = `${title}.pdf`;
            link.target = '_blank';
            document.body.appendChild(link);
            link.click();
            document.body.removeChild(link);
            showToast('เริ่มดาวน์โหลด', 'success');
        } catch (error) {
            console.error('Download error:', error);
            showToast('เกิดข้อผิดพลาดในการดาวน์โหลด', 'error');
        }
    }

    function printCurrentPDF() {
        if (currentPdfUrl) {
            const printWindow = window.open(currentPdfUrl, '_blank');
            printWindow.addEventListener('load', function() {
                printWindow.print();
            });
            showToast('เปิดหน้าต่างสำหรับพิมพ์', 'info');
        } else {
            showToast('ไม่พบไฟล์ PDF สำหรับพิมพ์', 'error');
        }
    }

    // Help and usage hints
    function showUsageHint() {
        helpModal.show();
    }

    // Toast notification system
    function showToast(message, type = 'info') {
        const toastContainer = document.querySelector('.toast-container');
        const toastId = 'toast-' + Date.now();

        const iconMap = {
            'success': 'bi-check-circle',
            'error': 'bi-exclamation-triangle',
            'warning': 'bi-exclamation-triangle',
            'info': 'bi-info-circle'
        };

        const bgMap = {
            'success': 'bg-success',
            'error': 'bg-danger',
            'warning': 'bg-warning text-dark',
            'info': 'bg-info'
        };

        const icon = iconMap[type] || 'bi-info-circle';
        const bgClass = bgMap[type] || 'bg-info';

        const toastHTML = `
        <div class="toast ${bgClass} text-white" id="${toastId}" role="alert" aria-live="assertive" aria-atomic="true">
            <div class="toast-body d-flex align-items-center">
                <i class="${icon} me-2"></i>
                ${message}
                <button type="button" class="btn-close btn-close-white ms-auto" data-bs-dismiss="toast" aria-label="Close"></button>
            </div>
        </div>
    `;

        toastContainer.insertAdjacentHTML('beforeend', toastHTML);

        const toastElement = document.getElementById(toastId);
        const toast = new bootstrap.Toast(toastElement, {
            delay: type === 'error' ? 5000 : 3000,
            autohide: true
        });

        toast.show();

        // Remove toast element after it's hidden
        toastElement.addEventListener('hidden.bs.toast', function() {
            toastElement.remove();
        });
    }

    // Magazine JavaScript Part 3 - Bookmark, Share & Advanced Features

    // Bookmark functionality (เหมือนโค้ดเดิม)
    function saveBookmark(magazineId = null) {
        if (currentPDF && currentMagazineTitle) {
            const bookmark = {
                page: currentPageNum,
                totalPages: totalPages,
                timestamp: new Date().toISOString(),
                zoomLevel: zoomLevel,
                title: currentMagazineTitle
            };

            const key = magazineId ? `bookmark_${magazineId}` : `bookmark_${currentMagazineTitle}`;

            try {
                localStorage.setItem(key, JSON.stringify(bookmark));
            } catch (error) {
                console.error('Error saving bookmark:', error);
            }
        }
    }

    function loadBookmark(magazineId) {
        const key = magazineId ? `bookmark_${magazineId}` : `bookmark_${currentMagazineTitle}`;

        try {
            const bookmarkData = localStorage.getItem(key);

            if (bookmarkData) {
                const bookmark = JSON.parse(bookmarkData);
                if (bookmark.page > 1 && bookmark.page <= totalPages) {
                    const confirmLoad = confirm(
                        `พบการบันทึกหน้าที่ ${bookmark.page} จาก ${bookmark.totalPages}\n` +
                        `ต้องการไปที่หน้าที่บันทึกไว้หรือไม่?`
                    );
                    if (confirmLoad) {
                        currentPageNum = bookmark.page;
                        if (bookmark.zoomLevel) {
                            zoomLevel = bookmark.zoomLevel;
                        }
                        updatePages();
                    }
                }
            }
        } catch (error) {
            console.error('Error loading bookmark:', error);
        }
    }

    // Share functionality
    function shareCurrentPage() {
        const shareData = {
            title: currentMagazineTitle,
            text: `กำลังอ่าน "${currentMagazineTitle}" หน้า ${currentPageNum} จาก ${totalPages}`,
            url: window.location.href + `#page-${currentPageNum}`
        };

        if (navigator.share) {
            navigator.share(shareData).then(() => {
                showToast('แชร์สำเร็จ', 'success');
            }).catch(error => {
                console.error('Error sharing:', error);
                fallbackShare(shareData);
            });
        } else {
            fallbackShare(shareData);
        }
    }

    function fallbackShare(shareData) {
        const shareText = `${shareData.text} - ${shareData.url}`;

        if (navigator.clipboard) {
            navigator.clipboard.writeText(shareText).then(() => {
                showToast('คัดลอกลิงก์แล้ว', 'success');
            }).catch(() => {
                showToast('ไม่สามารถคัดลอกได้ กรุณาลองอีกครั้ง', 'error');
            });
        } else {
            showToast('ไม่รองรับการแชร์ในเบราว์เซอร์นี้', 'error');
        }
    }

    // Touch and swipe support
    function initializeTouchSupport() {
        let touchStartX = 0;
        let touchEndX = 0;
        let touchStartY = 0;
        let touchEndY = 0;

        const book = document.getElementById('book');

        book.addEventListener('touchstart', function(e) {
            touchStartX = e.changedTouches[0].screenX;
            touchStartY = e.changedTouches[0].screenY;
        }, {
            passive: true
        });

        book.addEventListener('touchend', function(e) {
            touchEndX = e.changedTouches[0].screenX;
            touchEndY = e.changedTouches[0].screenY;
            handleSwipe();
        }, {
            passive: true
        });

        function handleSwipe() {
            const swipeThreshold = 50;
            const horizontalDistance = Math.abs(touchEndX - touchStartX);
            const verticalDistance = Math.abs(touchEndY - touchStartY);

            // Check if it's a horizontal swipe
            if (horizontalDistance > verticalDistance && horizontalDistance > swipeThreshold) {
                if (touchEndX < touchStartX - swipeThreshold) {
                    // Swipe left - next page
                    nextPage();
                } else if (touchEndX > touchStartX + swipeThreshold) {
                    // Swipe right - previous page
                    prevPage();
                }
            }
        }
    }

    // Reset modal state when closing
    function resetModalState() {
        // Stop auto play
        if (isAutoPlaying) {
            toggleAutoPlay();
        }

        // Reset fullscreen
        if (isFullscreen) {
            const modal = document.getElementById('flipBookModal');
            modal.classList.remove('fullscreen');
            document.getElementById('fullscreenIcon').className = 'bi bi-fullscreen';
            const fullscreenText = document.getElementById('fullscreenText');
            if (fullscreenText) fullscreenText.textContent = 'เต็มจอ';
            isFullscreen = false;
        }

        // Reset night mode
        if (isNightMode) {
            const flipbookWrapper = document.querySelector('.flipbook-wrapper');
            flipbookWrapper.style.filter = 'none';
            document.getElementById('nightModeIcon').className = 'bi bi-moon';
            isNightMode = false;
        }

        // Reset zoom
        zoomLevel = 1;
        const book = document.getElementById('book');
        if (book) {
            book.style.transform = 'scale(1)';
        }

        const flipbookContainer = document.getElementById('flipbookContainer');
        if (flipbookContainer) {
            flipbookContainer.style.overflow = 'hidden';
            flipbookContainer.style.cursor = 'default';
        }

        // ปิดการ drag
        disableDragWhenZoomed();

        // Reset variables
        resetPDFState();
        currentMagazineTitle = '';
        currentPdfUrl = '';

        // Clear pages
        document.getElementById('leftPageContent').innerHTML = '';
        document.getElementById('rightPageContent').innerHTML = '';

        // Reset progress bar
        document.getElementById('progressBar').style.width = '0%';

        // Reset page info
        document.getElementById('pageInfo').textContent = 'หน้า 1 จาก 1';

        // Reset buttons
        document.getElementById('prevBtn').disabled = true;
        document.getElementById('nextBtn').disabled = true;
        document.getElementById('firstBtn').disabled = true;
        document.getElementById('lastBtn').disabled = true;
        document.getElementById('playBtn').querySelector('i').className = 'bi bi-play-fill';

        console.log('Modal state reset with zoom');
    }

    // เพิ่มฟังก์ชันสำหรับ mouse wheel zoom ที่ดีขึ้น
    function initializeWheelZoom() {
        const flipbookContainer = document.getElementById('flipbookContainer');

        if (flipbookContainer) {
            flipbookContainer.addEventListener('wheel', function(e) {
                // ตรวจสอบว่ากด Ctrl หรือไม่
                if (e.ctrlKey) {
                    e.preventDefault();

                    if (e.deltaY < 0) {
                        // เลื่อนขึ้น = zoom in
                        zoomIn();
                    } else {
                        // เลื่อนลง = zoom out
                        zoomOut();
                    }
                }
            }, {
                passive: false
            });

            console.log('Wheel zoom initialized');
        }
    }

    // เรียกใช้ wheel zoom เมื่อ modal เปิด
    document.getElementById('flipBookModal').addEventListener('shown.bs.modal', function() {
        setTimeout(() => {
            initializeWheelZoom();
        }, 100);
    });

    // Reading statistics tracking (เหมือนโค้ดเดิม)
    function trackReadingStats() {
        if (!currentMagazineTitle) return;

        try {
            const stats = JSON.parse(localStorage.getItem('readingStats') || '{}');
            const today = new Date().toISOString().split('T')[0];

            if (!stats[today]) {
                stats[today] = {};
            }

            if (!stats[today][currentMagazineTitle]) {
                stats[today][currentMagazineTitle] = {
                    title: currentMagazineTitle,
                    pages: new Set(),
                    timeSpent: 0,
                    lastPage: currentPageNum,
                    sessions: 1
                };
            }

            const todayStats = stats[today][currentMagazineTitle];
            todayStats.pages.add(currentPageNum);
            todayStats.lastPage = currentPageNum;
            todayStats.timeSpent += 1; // seconds

            // Convert Set to Array for JSON storage
            const pagesArray = Array.from(todayStats.pages);
            stats[today][currentMagazineTitle] = {
                ...todayStats,
                pages: pagesArray
            };

            localStorage.setItem('readingStats', JSON.stringify(stats));
        } catch (error) {
            console.error('Error tracking reading stats:', error);
        }
    }

    // Keyboard shortcuts (เหมือนโค้ดเดิม)
    document.addEventListener('keydown', function(e) {
        // Only work when modal is open
        if (!document.getElementById('flipBookModal').classList.contains('show')) {
            return;
        }

        // Prevent shortcuts when typing in inputs
        if (e.target.tagName === 'INPUT' || e.target.tagName === 'TEXTAREA') {
            return;
        }

        switch (e.key) {
            case 'ArrowLeft':
                e.preventDefault();
                prevPage();
                break;
            case 'ArrowRight':
                e.preventDefault();
                nextPage();
                break;
            case ' ':
                e.preventDefault();
                toggleAutoPlay();
                break;
            case 'f':
            case 'F':
                e.preventDefault();
                toggleModalFullscreen();
                break;
            case 'Escape':
                if (isFullscreen) {
                    toggleModalFullscreen();
                } else {
                    flipBookModal.hide();
                }
                break;
            case '+':
            case '=':
                e.preventDefault();
                zoomIn();
                break;
            case '-':
                e.preventDefault();
                zoomOut();
                break;
            case '0':
                e.preventDefault();
                resetZoom();
                break;
            case 'Home':
                e.preventDefault();
                goToFirstPage();
                break;
            case 'End':
                e.preventDefault();
                goToLastPage();
                break;
            case 'g':
            case 'G':
                e.preventDefault();
                showPageJumper();
                break;
            case 'h':
            case 'H':
            case '?':
                e.preventDefault();
                showUsageHint();
                break;
            case 'n':
            case 'N':
                e.preventDefault();
                toggleNightMode();
                break;
            case 's':
            case 'S':
                e.preventDefault();
                shareCurrentPage();
                break;
            case 'd':
            case 'D':
                e.preventDefault();
                downloadCurrentPDF();
                break;
            case 'p':
            case 'P':
                e.preventDefault();
                printCurrentPDF();
                break;
        }
    });

    // Track reading time every second when modal is open
    setInterval(() => {
        if (document.getElementById('flipBookModal').classList.contains('show') && currentPDF) {
            trackReadingStats();
        }
    }, 1000);

    // Online/offline status
    window.addEventListener('online', function() {
        showToast('เชื่อมต่ออินเทอร์เน็ตแล้ว', 'success');
    });

    window.addEventListener('offline', function() {
        showToast('ไม่มีการเชื่อมต่ออินเทอร์เน็ต', 'warning');
    });

    // Visibility API for pausing when tab is not active
    document.addEventListener('visibilitychange', function() {
        if (document.hidden && isAutoPlaying) {
            // Pause auto play when tab is not visible
            toggleAutoPlay();
            showToast('หยุดการเล่นอัตโนมัติเนื่องจากเปลี่ยนแท็บ', 'info');
        }
    });

    // Initialize first time usage hint
    let isFirstTimeUser = !localStorage.getItem('magazine_modal_used');

    document.getElementById('flipBookModal').addEventListener('shown.bs.modal', function() {
        if (isFirstTimeUser) {
            setTimeout(() => {
                showUsageHint();
                localStorage.setItem('magazine_modal_used', 'true');
                isFirstTimeUser = false;
            }, 2000);
        }
    });

    console.log('Enhanced Magazine Modal JavaScript loaded successfully');
</script>

<script>
    // ตัวแปรสำหรับจัดการสถานะปุ่มใหม่
    let newButtonsInitialized = false;

    // ฟังก์ชันสำหรับเซ็ตอัพปุ่มใหม่
    function initializeNewButtons() {
        if (newButtonsInitialized) return;

        const newPrevBtn = document.getElementById('newPrevBtn');
        const newNextBtn = document.getElementById('newNextBtn');

        if (!newPrevBtn || !newNextBtn) {
            console.log('New buttons not found, retrying...');
            setTimeout(initializeNewButtons, 100);
            return;
        }

        // เพิ่ม Event Listeners แบบใหม่
        newPrevBtn.addEventListener('click', function(e) {
            e.preventDefault();
            e.stopPropagation();

            console.log('New Prev button clicked');
            console.log('Current state:', {
                currentPageNum,
                totalPages,
                isFlipping
            });

            if (currentPageNum && currentPageNum > 1 && !isFlipping) {
                newPrevPage();
            } else {
                console.log('Cannot go to previous page:', {
                    currentPageNum,
                    canGoPrev: currentPageNum > 1,
                    isFlipping
                });
            }
        });

        newNextBtn.addEventListener('click', function(e) {
            e.preventDefault();
            e.stopPropagation();

            console.log('New Next button clicked');
            console.log('Current state:', {
                currentPageNum,
                totalPages,
                isFlipping
            });

            if (currentPageNum && totalPages && currentPageNum < totalPages && !isFlipping) {
                newNextPage();
            } else {
                console.log('Cannot go to next page:', {
                    currentPageNum,
                    totalPages,
                    canGoNext: currentPageNum < totalPages,
                    isFlipping
                });
            }
        });

        newButtonsInitialized = true;
        console.log('New navigation buttons initialized successfully');

        // อัปเดตสถานะปุ่มทันที
        updateNewButtons();
    }

    // ฟังก์ชัน Previous ใหม่
    function newPrevPage() {
        console.log('newPrevPage called');

        if (!currentPageNum || currentPageNum <= 1 || isFlipping) {
            console.log('Cannot execute newPrevPage');
            return;
        }

        // ใช้ performPageFlip ถ้ามี หรือไปหน้าโดยตรง
        if (typeof performPageFlip === 'function') {
            performPageFlip(() => {
                currentPageNum--;
                if (typeof updatePages === 'function') {
                    updatePages();
                }
                updateNewButtons();
            });
        } else {
            // ไปหน้าโดยตรงถ้าไม่มี animation
            currentPageNum--;
            if (typeof updatePages === 'function') {
                updatePages();
            }
            updateNewButtons();
        }

        console.log('Moved to page:', currentPageNum);
    }

    // ฟังก์ชัน Next ใหม่
    function newNextPage() {
        console.log('newNextPage called');

        if (!currentPageNum || !totalPages || currentPageNum >= totalPages || isFlipping) {
            console.log('Cannot execute newNextPage');
            return;
        }

        // ใช้ performPageFlip ถ้ามี หรือไปหน้าโดยตรง
        if (typeof performPageFlip === 'function') {
            performPageFlip(() => {
                currentPageNum++;
                if (typeof updatePages === 'function') {
                    updatePages();
                }
                updateNewButtons();
            });
        } else {
            // ไปหน้าโดยตรงถ้าไม่มี animation
            currentPageNum++;
            if (typeof updatePages === 'function') {
                updatePages();
            }
            updateNewButtons();
        }

        console.log('Moved to page:', currentPageNum);
    }

    // ฟังก์ชันอัปเดตสถานะปุ่มใหม่
    function updateNewButtons() {
        const newPrevBtn = document.getElementById('newPrevBtn');
        const newNextBtn = document.getElementById('newNextBtn');

        if (!newPrevBtn || !newNextBtn) return;

        // อัปเดต Previous button
        const canGoPrev = currentPageNum && currentPageNum > 1;
        if (canGoPrev) {
            newPrevBtn.classList.remove('new-btn-disabled');
            newPrevBtn.style.pointerEvents = 'auto';
            newPrevBtn.style.opacity = '1';
        } else {
            newPrevBtn.classList.add('new-btn-disabled');
            newPrevBtn.style.pointerEvents = 'none';
            newPrevBtn.style.opacity = '0.5';
        }

        // อัปเดต Next button
        const canGoNext = currentPageNum && totalPages && currentPageNum < totalPages;
        if (canGoNext) {
            newNextBtn.classList.remove('new-btn-disabled');
            newNextBtn.style.pointerEvents = 'auto';
            newNextBtn.style.opacity = '1';
        } else {
            newNextBtn.classList.add('new-btn-disabled');
            newNextBtn.style.pointerEvents = 'none';
            newNextBtn.style.opacity = '0.5';
        }

        console.log('New buttons updated:', {
            currentPageNum,
            totalPages,
            canGoPrev,
            canGoNext
        });
    }

    // เพิ่ม CSS สำหรับปุ่มใหม่
    const newButtonsCSS = `
<style>
.new-btn-disabled {
    opacity: 0.5 !important;
    pointer-events: none !important;
    cursor: not-allowed !important;
}

.new-prev-btn:hover:not(.new-btn-disabled),
.new-next-btn:hover:not(.new-btn-disabled) {
    background: rgba(255, 255, 255, 0.2) !important;
    border-color: rgba(255, 255, 255, 0.6) !important;
    transform: translateY(-3px) !important;
}
</style>
`;

    // เพิ่ม CSS เข้าไปใน head
    document.head.insertAdjacentHTML('beforeend', newButtonsCSS);

    // เริ่มต้นปุ่มใหม่เมื่อ DOM พร้อม
    document.addEventListener('DOMContentLoaded', function() {
        setTimeout(initializeNewButtons, 500);
    });

    // เรียกใช้เมื่อมีการโหลด PDF ใหม่
    function onPDFLoaded() {
        setTimeout(() => {
            updateNewButtons();
        }, 100);
    }

    // เชื่อมต่อกับ updateUI function เดิม (ถ้ามี)
    const originalUpdateUI = window.updateUI;
    window.updateUI = function() {
        if (originalUpdateUI) {
            originalUpdateUI();
        }
        updateNewButtons();
    };

    console.log('New navigation buttons script loaded');
</script>

<script src="/assets/dflip/js/libs/jquery.min.js" type="text/javascript"></script>
<script src="/assets/dflip/js/dflip.min.js" type="text/javascript"></script>

<!-- Flipbook StyleSheets -->
<link href="/assets/dflip/css/dflip.min.css" rel="stylesheet" type="text/css">
<!-- themify-icons.min.css is not required in version 2.0 and above -->
<link href="/assets/dflip/css/themify-icons.min.css" rel="stylesheet" type="text/css">