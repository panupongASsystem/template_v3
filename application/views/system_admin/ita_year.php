<!-- ไฟล์: ita_year.php - ใช้ปุ่มแยกสำหรับ ITA Tour -->

<!-- ✅ CSS สำหรับปุ่ม ITA Tour (ใช้ร่วมกับ Intro.js) -->
<style>
    /* ปุ่มแนะนำการใช้งานหน้า ITA - สีฟ้าอ่อน */
    .btn-ita-tour {
        display: inline-flex;
        align-items: center;
        gap: 8px;
        padding: 8px 18px;
        border: none;
        border-radius: 10px;
        font-size: 14px;
        font-weight: 600;
        cursor: pointer;
        transition: all 0.3s ease;
        box-shadow: 0 3px 12px rgba(6, 182, 212, 0.25);
        white-space: nowrap;
        background: linear-gradient(135deg, #A8D8EA 0%, #89CFF0 100%);
        color: #2C3E50;
        text-decoration: none;
        margin-left: 10px;
    }

    .btn-ita-tour:hover {
        background: linear-gradient(135deg, #89CFF0 0%, #6BB6D9 100%);
        color: #1A252F;
        box-shadow: 0 5px 16px rgba(168, 216, 234, 0.6);
        transform: translateY(-2px);
        text-decoration: none;
    }

    .btn-ita-tour:active {
        transform: translateY(0);
        box-shadow: 0 3px 12px rgba(168, 216, 234, 0.4);
    }

    .btn-ita-tour svg {
        width: 18px;
        height: 18px;
    }

    .btn-ita-tour.loading {
        pointer-events: none;
        opacity: 0.7;
    }

    .btn-ita-tour.loading svg {
        animation: spin 1s linear infinite;
    }

    @keyframes spin {
        from {
            transform: rotate(0deg);
        }

        to {
            transform: rotate(360deg);
        }
    }

    /* Badge ใหม่ */
    .tour-new-badge {
        background: #E74C3C;
        color: white;
        padding: 2px 8px;
        border-radius: 12px;
        font-size: 11px;
        margin-left: 6px;
        animation: pulse 2s ease-in-out infinite;
    }

    @keyframes pulse {

        0%,
        100% {
            opacity: 1;
        }

        50% {
            opacity: 0.7;
        }
    }

    @media (max-width: 768px) {
        .btn-ita-tour {
            padding: 6px 14px;
            font-size: 13px;
        }

        .btn-ita-tour span.btn-text {
            display: none;
        }
    }
</style>

<!-- ✅ ปุ่มเดิม (เก็บไว้) -->
<a class="btn add-btn insert-vulgar-btn" data-target="#popupInsert">
    <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-plus-circle"
        viewBox="0 0 16 16">
        <path d="M8 15A7 7 0 1 1 8 1a7 7 0 0 1 0 14zm0 1A8 8 0 1 0 8 0a8 8 0 0 0 0 16z" />
        <path
            d="M8 4a.5.5 0 0 1 .5.5v3h3a.5.5 0 0 1 0 1h-3v3a.5.5 0 0 1-1 0v-3h-3a.5.5 0 0 1 0-1h3v-3A.5.5 0 0 1 8 4z" />
    </svg> เพิ่มข้อมูล
</a>

<a class="btn btn-light" href="<?= site_url('Ita_year_backend'); ?>" role="button">
    <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-arrow-clockwise"
        viewBox="0 0 16 16">
        <path fill-rule="evenodd" d="M8 3a5 5 0 1 0 4.546 2.914.5.5 0 0 1 .908-.417A6 6 0 1 1 8 2v1z" />
        <path d="M8 4.466V.534a.25.25 0 0 1 .41-.192l2.36 1.966c.12.1.12.284 0 .384L8.41 4.658A.25.25 0 0 1 8 4.466z" />
    </svg> Refresh Data
</a>

<!-- ✅ ปุ่มใหม่สำหรับ ITA Tour -->
<a class="btn btn-ita-tour" id="itaTourBtn" href="javascript:void(0);">
    <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" viewBox="0 0 16 16">
        <path d="M8 15A7 7 0 1 1 8 1a7 7 0 0 1 0 14zm0 1A8 8 0 1 0 8 0a8 8 0 0 0 0 16z" />
        <path
            d="M5.255 5.786a.237.237 0 0 0 .241.247h.825c.138 0 .248-.113.266-.25.09-.656.54-1.134 1.342-1.134.686 0 1.314.343 1.314 1.168 0 .635-.374.927-.965 1.371-.673.489-1.206 1.06-1.168 1.987l.003.217a.25.25 0 0 0 .25.246h.811a.25.25 0 0 0 .25-.25v-.105c0-.718.273-.927 1.01-1.486.609-.463 1.244-.977 1.244-2.056 0-1.511-1.276-2.241-2.673-2.241-1.267 0-2.655.59-2.75 2.286zm1.557 5.763c0 .533.425.927 1.01.927.609 0 1.028-.394 1.028-.927 0-.552-.42-.94-1.029-.94-.584 0-1.009.388-1.009.94z" />
    </svg>
    <span class="btn-text">แนะนำการใช้งาน</span>
</a>

<!-- Popup และ Table (เก็บไว้เหมือนเดิม) -->
<div id="popupInsert" class="popup">
    <div class="popup-content">
        <h4>เพิ่มข้อมูล ITA ประจำปี</h4>
        <form action="<?php echo site_url('Ita_year_backend/add_year'); ?> " method="post" class="form-horizontal">
            <div class="form-group row">
                <div class="col-sm-1 control-label">ปี</div>
                <div class="col-sm-5">
                    <input type="text" name="ita_year_year" required class="form-control">
                    <span class="red-add">( ใส่เฉพาะตัวเลข เช่น 2567)</span>
                </div>
            </div>
            <br>
            <div class="form-group row">
                <div class="col-sm-1 control-label"></div>
                <div class="col-sm-5">
                    <button type="submit" class="btn btn-success">บันทึกข้อมูล</button>
                    <a class="btn btn-danger" href="<?= site_url('Ita_year_backend'); ?>" role="button">ยกเลิก</a>
                </div>
            </div>
        </form>
    </div>
</div>

<!-- DataTales Example -->
<div class="card shadow mb-4">
    <div class="card-header py-3">
        <h6 class="m-0 font-weight-bold text-black">จัดการข้อมูล ITA ประจำปี</h6>
    </div>
    <div class="card-body">
        <div class="table-responsive">
            <?php $Index = 1; ?>
            <table id="newdataTables" class="table">
                <thead>
                    <tr>
                        <th style="width: 3%;">ลำดับ</th>
                        <th style="width: 50%;">ชื่อ</th>
                        <th style="width: 13%;">อัพโหลด</th>
                        <th style="width: 7%;">วันที่</th>
                        <th style="width: 17%;">จัดการ</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($query as $rs) { ?>
                        <tr role="row">
                            <td align="center"><?= $Index; ?></td>
                            <td class="limited-text"><?= $rs->ita_year_year; ?></td>
                            <td><?= $rs->ita_year_by; ?></td>
                            <td><?= date('d/m/Y H:i', strtotime($rs->ita_year_datesave . '+543 years')) ?> น.</td>
                            <td>
                                <a href="<?= site_url('ita_year_backend/index_topic/' . $rs->ita_year_id); ?>"><i
                                        class="bi bi-plus-square fa-lg"></i></a>
                                <a href="<?= site_url('ita_year_backend/editing_year/' . $rs->ita_year_id); ?>"><i
                                        class="bi bi-pencil-square fa-lg "></i></a>
                                <a href="#" role="button" onclick="confirmDelete('<?= $rs->ita_year_id; ?>');"><i
                                        class="bi bi-trash fa-lg "></i></a>
                                <script>
                                    function confirmDelete(ita_year_id) {
                                        Swal.fire({
                                            title: 'กดเพื่อยืนยัน?',
                                            text: "คุณจะไม่สามรถกู้คืนได้อีก!",
                                            icon: 'warning',
                                            showCancelButton: true,
                                            confirmButtonColor: '#3085d6',
                                            cancelButtonColor: '#d33',
                                            confirmButtonText: 'ใช่, ต้องการลบ!',
                                            cancelButtonText: 'ยกเลิก'
                                        }).then((result) => {
                                            if (result.isConfirmed) {
                                                window.location.href = "<?= site_url('ita_year_backend/del_ita_year/'); ?>" + ita_year_id;
                                            }
                                        });
                                    }
                                </script>
                            </td>
                        </tr>
                        <?php $Index++;
                    } ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<!-- ✅ JavaScript สำหรับปุ่ม ITA Tour -->
<script>
    (function () {
        'use strict';

        console.log('🎯 ITA Tour Script: Initializing...');

        // ✅ ฟังก์ชันเริ่ม Tour สำหรับ ITA
        function startITATour() {
            console.log('🚀 Starting ITA Tour...');

            // เช็คว่ามี tourManager หรือไม่
            if (typeof window.tourManager === 'undefined') {
                console.error('❌ tourManager not found!');
                alert('❌ ระบบ Tour ยังไม่พร้อม\n\nกรุณารอสักครู่แล้วลองใหม่');
                return;
            }

            // เช็คว่า tourManager มี method ที่จำเป็นหรือไม่
            if (typeof window.tourManager.startTour !== 'function') {
                console.error('❌ tourManager.startTour() not found!');
                alert('❌ ระบบ Tour ไม่สมบูรณ์\n\nกรุณาแจ้งทีมพัฒนา');
                return;
            }

            // เช็คว่ามี Tour Steps สำหรับ ITA หรือไม่
            const currentPage = window.tourManager.getCurrentPage();
            console.log('📍 Current Page:', currentPage);

            if (currentPage !== 'Ita_year_backend') {
                console.warn('⚠️ Not on ITA page:', currentPage);
                alert('⚠️ หน้านี้ไม่ใช่หน้า ITA\n\nกรุณาไปที่หน้า ITA ก่อน');
                return;
            }

            // เริ่ม Tour
            try {
                window.tourManager.startTour('Ita_year_backend');
                console.log('✅ ITA Tour started successfully!');

                // ลบ badge "ใหม่" ออก
                const badge = document.querySelector('.tour-new-badge');
                if (badge) {
                    badge.remove();
                }

                // บันทึกว่าเคยดู Tour แล้ว
                if (typeof localStorage !== 'undefined') {
                    localStorage.setItem('ita_tour_viewed', 'true');
                }

            } catch (error) {
                console.error('❌ Error starting tour:', error);
                alert('❌ เกิดข้อผิดพลาด\n\n' + error.message);
            }
        }

        // รอให้ DOM พร้อม
        function initITATourButton() {
            console.log('🔧 Initializing ITA Tour Button...');

            const tourBtn = document.getElementById('itaTourBtn');

            if (!tourBtn) {
                console.warn('⚠️ ITA Tour button not found!');
                return;
            }

            // ตรวจสอบว่าเคยดู Tour หรือยัง
            const hasViewedTour = localStorage.getItem('ita_tour_viewed');

            // เพิ่ม badge "ใหม่" ถ้ายังไม่เคยดู
            if (!hasViewedTour) {
                const badge = document.createElement('span');
                badge.className = 'tour-new-badge';
                badge.textContent = 'ใหม่';
                tourBtn.appendChild(badge);
                console.log('✨ Added "New" badge');
            }

            // ลบ event เก่าออกก่อน (ถ้ามี)
            const newBtn = tourBtn.cloneNode(true);
            tourBtn.parentNode.replaceChild(newBtn, tourBtn);

            // เพิ่ม event listener
            newBtn.addEventListener('click', function (e) {
                e.preventDefault();
                e.stopPropagation();

                console.log('🖱️ ITA Tour button clicked');

                // แสดง loading state
                newBtn.classList.add('loading');
                const originalHTML = newBtn.innerHTML;
                newBtn.innerHTML = '<svg style="width:18px;height:18px;animation:spin 1s linear infinite" viewBox="0 0 16 16"><circle cx="8" cy="8" r="7" stroke="currentColor" stroke-width="2" fill="none"/></svg><span class="btn-text">กำลังโหลด...</span>';

                // เริ่ม Tour หลังจาก 300ms
                setTimeout(function () {
                    startITATour();

                    // คืน state ปกติ
                    newBtn.classList.remove('loading');
                    newBtn.innerHTML = originalHTML;

                    // ลบ badge หลัง event เสร็จ
                    const badge = newBtn.querySelector('.tour-new-badge');
                    if (badge) {
                        badge.remove();
                    }
                }, 300);

                return false;
            });

            console.log('✅ ITA Tour button initialized successfully');
        }

        // รอให้ทุกอย่างโหลดเสร็จ
        if (document.readyState === 'loading') {
            document.addEventListener('DOMContentLoaded', function () {
                // รอให้ tourManager โหลดเสร็จ
                setTimeout(initITATourButton, 1000);
            });
        } else {
            setTimeout(initITATourButton, 1000);
        }

        // ✅ Auto-start ถ้ามี ?tour=start ใน URL (เฉพาะครั้งแรก)
        const urlParams = new URLSearchParams(window.location.search);
        if (urlParams.get('tour') === 'start') {
            // ลบ parameter ออกจาก URL
            window.history.replaceState({}, '', window.location.pathname);

            // เริ่ม Tour หลังจาก 2 วินาที
            setTimeout(function () {
                console.log('🎯 Auto-starting ITA Tour from URL parameter...');
                startITATour();
            }, 2000);
        }

    })();
</script>