<!-- Include Bootstrap CSS and JavaScript -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js" integrity="sha384-C6RzsynM9kWDrMNeT87bh95OGNyZPhcTNXj1NW7RuBCsyN/o0jlpcV8Qyq46cDfL" crossorigin="anonymous"></script>
<script src="https://code.jquery.com/jquery-3.7.1.min.js" integrity="sha256-/JqT3SQfawRcv/BIHPThkBvs0OEvtFFmqPF/lYI/Cxo=" crossorigin="anonymous"></script>
<!-- sweetalert 2 -->
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11.7.31/dist/sweetalert2.all.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

<!-- sb-admin-2 -->
<!-- Bootstrap core JavaScript-->
<script src="<?= base_url(); ?>vendor/jquery/jquery.min.js"></script>
<!-- <script src="<?= base_url(); ?>vendor/bootstrap/js/bootstrap.bundle.min.js"></script> -->

<!-- Core plugin JavaScript-->
<script src="<?= base_url(); ?>vendor/jquery-easing/jquery.easing.min.js"></script>

<!-- Custom scripts for all pages-->
<script src="<?= base_url('asset/'); ?>js/sb-admin-2.min.js"></script>

<!-- Page level plugins -->
<!-- <script src="<?= base_url(); ?>vendor/chart.js/Chart.min.js"></script> -->

<!-- Page level custom scripts -->
<!-- <script src="js/demo/chart-area-demo.js"></script>
  <script src="js/demo/chart-pie-demo.js"></script> -->

<!-- Page level plugins -->
<script src="<?= base_url(); ?>vendor/datatables/jquery.dataTables.min.js"></script>
<script src="<?= base_url(); ?>vendor/datatables/dataTables.bootstrap4.min.js"></script>

<!-- Page level custom scripts -->
<script src="<?= base_url('asset/'); ?>js/demo/datatables-demo.js"></script>

<!-- Bootstrap core JavaScript-->
<script src="vendor/bootstrap/js/bootstrap.bundle.min.js"></script>
<!-- รูปภาพ preview -->
<script src="<?= base_url('asset/'); ?>lightbox2/src/js/lightbox.js"></script>

<script src="https://cdn.jsdelivr.net/npm/sortablejs@1.15.0/Sortable.min.js"></script>

<script src="https://code.jquery.com/ui/1.13.2/jquery-ui.min.js"></script>

<script src="https://cdnjs.cloudflare.com/ajax/libs/fancybox/3.5.7/jquery.fancybox.min.js"></script>

<!-- sortable โครงสร้างบุคลากรใหม่ -->
<script src="https://cdnjs.cloudflare.com/ajax/libs/Sortable/1.15.0/Sortable.min.js"></script>

<!-- Flatpickr date save ใหม่ ============================================= -->
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/flatpickr/dist/flatpickr.min.css">

<script src="https://cdn.jsdelivr.net/npm/flatpickr"></script>
<script src="https://cdn.jsdelivr.net/npm/flatpickr/dist/l10n/th.js"></script>

<script>
  (function () {
    // ===== helper: ปี พ.ศ. สำหรับ altInput =====
    const toBuddhist = (date) => {
      const d = new Date(date.getTime());
      d.setFullYear(d.getFullYear() + 543);
      return d;
    };
    const formatThaiBuddhist = (date) => {
      const d = toBuddhist(date);
      const pad = (n) => (n < 10 ? '0' : '') + n;
      return `${pad(d.getDate())}/${pad(d.getMonth() + 1)}/${d.getFullYear()} ${pad(date.getHours())}:${pad(date.getMinutes())} น.`;
    };

    // ⭐ อัปเดต "หัวปี" ของ flatpickr ให้โชว์เป็น พ.ศ.
    function updateBEYearHeader(fp) {
      const yEl = fp && fp.currentYearElement;
      if (!yEl) return;

      // โชว์ พ.ศ.
      yEl.value = fp.currentYear + 543;

      // ผูกครั้งเดียว: ถ้าผู้ใช้พิมพ์ปีเป็น พ.ศ. → แปลงกลับเป็น ค.ศ. แล้วปรับปีจริง
      if (!yEl.dataset.beHandled) {
        yEl.dataset.beHandled = '1';
        yEl.addEventListener('input', () => {
          const v = parseInt(yEl.value, 10);
          if (!isNaN(v) && v > 2400) {
            fp.changeYear(v - 543);        // ตั้งปีจริงเป็น ค.ศ.
            yEl.value = fp.currentYear + 543; // กลับมาโชว์เป็น พ.ศ.
          }
        });
      }
    }

    // ===== สร้าง picker ให้ทุก input[type=datetime-local]
    function enhanceDateTimeInputs(root = document) {
      const nodes = root.querySelectorAll('input[type="datetime-local"]:not([data-fp])');

      nodes.forEach((el) => {
        const min = el.getAttribute('min');
        const max = el.getAttribute('max');
        const value = el.value;

        el.setAttribute('data-fp', '1');
        el.setAttribute('data-iso', '1');
        el.type = 'text';

        const fp = flatpickr(el, {
  locale: 'th',
  enableTime: true,
  time_24hr: true,
  minuteIncrement: 1,
  allowInput: false,
  altInput: true,
  altFormat: 'd/m/Y H:i',
  dateFormat: 'Y-m-d\\TH:i',
  
  // ⭐ เพิ่มตรงนี้เพื่อให้เวลาเป็นปัจจุบัน
  defaultDate: new Date(),
  defaultHour: new Date().getHours(),
  defaultMinute: new Date().getMinutes(),

  onReady(selectedDates, dateStr, instance) {
    if (min) instance.set('minDate', min.replace(' ', 'T'));
    if (max) instance.set('maxDate', max.replace(' ', 'T'));
    if (value) instance.setDate(value, false, 'Y-m-d\\TH:i');

            // override การแสดงผล altInput → ไทย+พ.ศ.
            const origFormat = instance.formatDate;
            instance.formatDate = function (date, format, locale) {
              if (this.config.altInput && format === this.config.altFormat) {
                const d = new Date(date.getTime());
                d.setFullYear(d.getFullYear() + 543);
                const pad = (n) => (n < 10 ? '0' : '') + n;
                return `${pad(d.getDate())}/${pad(d.getMonth() + 1)}/${d.getFullYear()} ${pad(date.getHours())}:${pad(date.getMinutes())}`;
              }
              return origFormat.call(this, date, format, locale);
            };

            // แสดงครั้งแรก
            if (instance.altInput && instance.selectedDates[0]) {
              instance.altInput.value = formatThaiBuddhist(instance.selectedDates[0]);
            }

            // ⭐ อัปเดตหัวปีเป็น พ.ศ. เมื่อเปิด/คลิกเปลี่ยนเดือน-ปี
            updateBEYearHeader(instance);
            const cal = instance.calendarContainer;
            if (cal && !cal.dataset.beHooked) {
              cal.dataset.beHooked = '1';
              cal.addEventListener('click', (e) => {
                if (
                  e.target.closest('.flatpickr-next-month') ||
                  e.target.closest('.flatpickr-prev-month')
                ) {
                  updateBEYearHeader(instance);
                }
              }, { passive: true });
            }
          },

          onOpen(selectedDates, dateStr, instance) {
            updateBEYearHeader(instance);     // ⭐
          },
          onYearChange(selectedDates, dateStr, instance) {
            updateBEYearHeader(instance);     // ⭐
          },
          onMonthChange(selectedDates, dateStr, instance) {
            updateBEYearHeader(instance);     // ⭐
          },
          onValueUpdate(selectedDates, dateStr, instance) {
            if (instance.altInput && selectedDates[0]) {
              instance.altInput.value = formatThaiBuddhist(selectedDates[0]);
            }
            updateBEYearHeader(instance);     // ⭐
          }
        });
      });
    }

    // init
    document.addEventListener('DOMContentLoaded', () => {
      enhanceDateTimeInputs();

      // รองรับ input ใหม่ที่ถูก inject
      const mo = new MutationObserver((muts) => {
        muts.forEach((m) => {
          m.addedNodes.forEach((n) => {
            if (n.nodeType === 1) {
              if (n.matches && n.matches('input[type="datetime-local"]')) {
                enhanceDateTimeInputs(n.parentNode || document);
              } else {
                enhanceDateTimeInputs(n);
              }
            }
          });
        });
      });
      mo.observe(document.body, { childList: true, subtree: true });
    });
  })();
</script>

<script>
    // ====== ทำให้ทุก input ที่ถูก flatpickr ครอบ (data-fp) ส่งค่าเป็น ค.ศ. ======
    (function() {
        // format เป็น ISO 8601 แบบ YYYY-MM-DDTHH:mm (ที่ CI3 อ่านง่าย)
        function toISO(dt) {
            const pad = n => (n < 10 ? '0' : '') + n;
            return `${dt.getFullYear()}-${pad(dt.getMonth()+1)}-${pad(dt.getDate())}T${pad(dt.getHours())}:${pad(dt.getMinutes())}`;
        }

        // เผื่อผู้ใช้พิมพ์เองในช่อง alt (ไทย) เช่น 03/10/2568 12:00 น.
        function parseThaiAlt(val) {
            // จับ d/m/yyyy hh:mm
            const m = (val || '').match(/(\d{1,2})\/(\d{1,2})\/(\d{4})\s+(\d{1,2}):(\d{2})/);
            if (!m) return null;
            let [, d, mo, y, hh, mm] = m.map(Number);
            // ถ้าเป็น พ.ศ. (>2400) ให้ลบ 543
            if (y > 2400) y -= 543;
            return new Date(y, mo - 1, d, hh, mm);
        }

        // ผูกกับทุกฟอร์ม (หากมีหลายฟอร์มในหน้า)
        document.addEventListener('submit', function(e) {
            // หา input ที่ถูก enhance ด้วย flatpickr ทั้งหมด
            document.querySelectorAll('input[data-fp]').forEach(function(el) {
                const fp = el._flatpickr;
                let dt = null;

                if (fp && fp.selectedDates && fp.selectedDates[0]) {
                    // ผู้ใช้เลือกจากปฏิทิน → ได้ Date (ค.ศ.) ตรง ๆ
                    dt = fp.selectedDates[0];
                } else if (fp && fp.altInput && fp.altInput.value) {
                    // ผู้ใช้พิมพ์เองในช่องแสดงผล (ไทย) → แปลงกลับเป็น ค.ศ.
                    dt = parseThaiAlt(fp.altInput.value);
                } else if (el.value) {
                    // กรณีสุดท้าย ถ้ามีค่าอยู่แล้วแต่เป็นไทย เช่น 03/10/2568 12:00
                    const parsed = parseThaiAlt(el.value);
                    if (parsed) dt = parsed;
                }

                if (dt instanceof Date && !isNaN(dt.getTime())) {
                    // ✅ บันทึกจริงเป็น ค.ศ. แบบ ISO
                    el.value = toISO(dt);
                }
            });
        }, true);
    })();
</script>
<!-- Flatpickr date save ใหม่ ============================================= -->


<script>
	 // search หลังบ้าน ตัวใหม่ start =====================================================
    // ฟังก์ชันสำหรับสร้างข้อมูลเมนูจาก sidebar โดยอัตโนมัติ
    function generateMenuData() {
        const menuItems = [];

        // ดึงข้อมูลจาก sidebar
        const sidebar = document.getElementById('accordionSidebar');
        if (!sidebar) return menuItems;

        // ดึงเมนูหลัก (nav-item)
        const navItems = sidebar.querySelectorAll('.nav-item');

        navItems.forEach(item => {
            const link = item.querySelector('a.nav-link');
            if (!link) return;

            const span = link.querySelector('span');
            if (!span) return;

            const menuText = span.textContent.trim();
            const href = link.getAttribute('href');

            // ถ้าเป็นเมนูที่มี href ตรงๆ (ไม่ใช่ dropdown)
            if (href && href !== 'javascript:void(0);') {
                menuItems.push({
                    text: menuText,
                    url: href,
                    category: 'เมนูหลัก'
                });
            }

            // ดึงข้อมูลจาก submenu (collapse-item)
            const collapseDiv = item.querySelector('.collapse');
            if (collapseDiv) {
                const collapseItems = collapseDiv.querySelectorAll('.collapse-item');
                collapseItems.forEach(subItem => {
                    const subText = subItem.textContent.trim();
                    const subHref = subItem.getAttribute('href');

                    if (subText && subHref) {
                        menuItems.push({
                            text: subText,
                            url: subHref,
                            category: menuText
                        });
                    }
                });
            }
        });

        return menuItems;
    }

    // ฟังก์ชันไฮไลท์คำค้นหา
    function highlightSearchTerm(text, searchTerm) {
        if (!searchTerm) return text;

        const regex = new RegExp(`(${searchTerm})`, 'gi');
        return text.replace(regex, '<span class="search-highlight">$1</span>');
    }

    // ฟังก์ชันค้นหา
    function search() {
        const searchInput = document.getElementById('searchInput');
        const menuList = document.getElementById('menuList');
        const searchTerm = searchInput.value.trim();

        // ถ้าไม่มีคำค้นหา ให้ซ่อนผลลัพธ์
        if (!searchTerm) {
            menuList.style.display = 'none';
            return;
        }

        // สร้างข้อมูลเมนู
        const menuData = generateMenuData();

        // กรองผลลัพธ์ที่ตรงกับคำค้นหา
        const filteredResults = menuData.filter(item =>
            item.text.toLowerCase().includes(searchTerm.toLowerCase())
        );

        // สร้างแสดงผลลัพธ์
        menuList.innerHTML = '';

        if (filteredResults.length === 0) {
            menuList.innerHTML = '<li class="no-results">ไม่พบผลลัพธ์ที่ตรงกับคำค้นหา</li>';
        } else {
            filteredResults.forEach(item => {
                const li = document.createElement('li');
                li.innerHTML =
                    '<a href="' + item.url + '" class="link">' +
                    '<div class="menu-category">' + item.category + '</div>' +
                    '<div>' + highlightSearchTerm(item.text, searchTerm) + '</div>' +
                    '</a>';
                menuList.appendChild(li);
            });
        }

        menuList.style.display = 'block';
    }

    // ซ่อนผลลัพธ์เมื่อคลิกที่อื่น
    document.addEventListener('click', function(e) {
        const searchBox = document.querySelector('.search-box');
        const menuList = document.getElementById('menuList');

        if (!searchBox.contains(e.target)) {
            menuList.style.display = 'none';
        }
    });

    // แสดงผลลัพธ์เมื่อโฟกัสที่ช่องค้นหา
    document.addEventListener('DOMContentLoaded', function() {
        const searchInput = document.getElementById('searchInput');

        searchInput.addEventListener('focus', function() {
            if (this.value.trim()) {
                search();
            }
        });

        // เพิ่ม event listener สำหรับการกด Enter
        searchInput.addEventListener('keypress', function(e) {
            if (e.key === 'Enter') {
                e.preventDefault();
                const menuList = document.getElementById('menuList');
                const firstLink = menuList.querySelector('a.link');
                if (firstLink) {
                    window.location.href = firstLink.getAttribute('href');
                }
            }
        });
    });
    // search หลังบ้าน ตัวใหม่ start =====================================================
	    $(document).ready(function() {
        // ดึงค่าจาก session flashdata ที่ส่งมาจาก controller
        var facebookShareLink = "<?php echo $this->session->flashdata('facebook_share_link'); ?>";

        // ตรวจสอบว่ามีค่าหรือไม่
        if (facebookShareLink) {
            Swal.fire({
                icon: 'question',
                title: "ต้องการแชร์ข้อมูล Facebook หรือไม่",
                showCancelButton: true,
                confirmButtonText: "ต้องการแชร์",
                cancelButtonText: "ไม่ต้องการ",
            }).then((result) => {
                if (result.isConfirmed) {
                    window.open(facebookShareLink, "_blank"); // ใช้ facebook share link ที่ดึงมาจาก PHP
                } else if (result.isCancel) {
                    Swal.fire("Saved!", "", "success");
                }
            });
        }
    });
	
    // เบอร์โทรใส่ขีดได้ ************************************************************************
    $(document).ready(function() {
        $('#phone-input').on('input', function() {
            // Remove all non-numeric characters
            var value = $(this).val().replace(/\D/g, '');

            // Limit to 9 or 10 digits only
            if (value.length > 10) {
                value = value.slice(0, 10);
            }

            // Add dashes to format as 099-999-999 or 099-999-9999
            if (value.length <= 10) {
                if (value.length === 9) {
                    value = value.replace(/(\d{3})(\d{3})(\d{3})/, '$1-$2-$3');
                } else if (value.length === 10) {
                    value = value.replace(/(\d{3})(\d{3})(\d{4})/, '$1-$2-$3');
                }
            }

            $(this).val(value);
        });

        $('#phone-input').on('blur', function() {
            var value = $(this).val().replace(/\D/g, '');

            if (value.length < 9 || value.length > 10) {
                $('#phone-error').text('กรุณากรอกเบอร์มือถือ 9 หรือ 10 ตัว');
            } else {
                $('#phone-error').text('');
            }
        });
    });
 // logout session *********************************************************************
    let timeout;

    function resetTimeout() {
        clearTimeout(timeout);
        timeout = setTimeout(function() {
            // แสดงกล่องโต้ตอบ SweetAlert
            Swal.fire({
                title: 'หมดเวลาใช้งาน',
                text: 'เราจะพาคุณออกจากระบบ',
                icon: 'warning',
                showConfirmButton: true,
                confirmButtonText: 'ตกลง'
            }).then((result) => {
                // รีไดเร็กต์ไปยังหน้า logout หลังจากผู้ใช้กดปุ่มตกลง
                window.location.href = '<?php echo site_url('User/logout'); ?>';
            });

            // บังคับออกจากระบบหลังจากที่แสดง SweetAlert2
            setTimeout(() => {
                window.location.href = '<?php echo site_url('User/logout'); ?>';
            }, 90000); // 3 วินาที
        }, 900000); // หน่วยมิลลิวินาที
    }

    // รีเซ็ตเวลานับถอยหลังเมื่อผู้ใช้มีการกระทำใดๆ
    window.onload = resetTimeout;
    document.onmousemove = resetTimeout;
    document.onkeypress = resetTimeout;
    // **************************************************************************************

	function validateForm() {
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

        const fileInputs = document.querySelectorAll('input[type="file"]');

        for (const input of fileInputs) {
            for (const file of input.files) {
                if (input.accept.includes('image/') && !imageTypes.includes(file.type)) {
                    Swal.fire({
                        icon: 'warning',
                        title: 'ตรวจพบปัญหา',
                        text: 'รูปภาพเพิ่มเติมจะต้องเป็นไฟล์ .JPG/.JPEG/.jfif/.PNG เท่านั้น!',
                        footer: '<a href="#">ติดต่อผู้ดูแลระบบ?</a>'
                    })
                    return false;
                }
                if (input.accept.includes('application/pdf') && file.type !== pdfType) {
                    Swal.fire({
                        icon: 'warning',
                        title: 'ตรวจพบปัญหา',
                        text: 'ไฟล์เอกสารเพิ่มเติมจะต้องเป็นไฟล์ PDF เท่านั้น',
                        footer: '<a href="#">ติดต่อผู้ดูแลระบบ?</a>'
                    })
                    return false;
                }
                if (input.accept.includes('application/msword') || input.accept.includes('application/vnd.openxmlformats-officedocument')) {
                    if (!docTypes.includes(file.type)) {
                        Swal.fire({
                            icon: 'warning',
                            title: 'ตรวจพบปัญหา',
                            text: 'ไฟล์เอกสารเพิ่มเติมจะต้องเป็นไฟล์ .doc .docx .ppt .pptx .xls .xlsx เท่านั้น',
                            footer: '<a href="#">ติดต่อผู้ดูแลระบบ?</a>'
                        })
                        return false;
                    }
                }
            }
        }

        return true;
    }

    // รูปภาพ preview *********************************************************************
    $(document).ready(function() {
        lightbox.option({
            'resizeDuration': 200,
            'wrapAround': true
        });
    });
    // **************************************************************************************
    // ค้นหา hide show  click *********************************************************************
    $(document).ready(function() {
        // เมื่อมีการคลิกที่ส่วนอื่นๆ ของเอกสาร
        $(document).on('click', function(event) {
            var target = $(event.target);
            var menuList = $('#menuList');

            // ตรวจสอบว่าคลิกที่ #menuList หรือไม่
            if (!target.closest('#menuList').length) {
                // ถ้าไม่ใช่ ให้ซ่อน #menuList
                menuList.hide();
            }
        });

        // เมื่อมีการพิมพ์ใน input
        $('#searchInput').on('input', function() {
            var inputValue = $(this).val().trim();
            var menuList = $('#menuList');
            if (inputValue === '') {
                menuList.hide();
            } else {
                menuList.show();
            }
        });
    });

    // **************************************************************************************

    // ปุ่มย้อนกลับของยกเลิก *********************************************************************
    function goBack() {
        window.history.back();
    }
    // **************************************************************************************

    //  เมนูเปิดปิดการแสดงผล navbar **************************************************************
    function toggleCollapse(collapseId) {
        var collapseElement = document.getElementById(collapseId);
        var allCollapseElements = document.querySelectorAll('.collapse');

        allCollapseElements.forEach(function(element) {
            if (element.id !== collapseId && element.classList.contains('show')) {
                element.classList.remove('show');
            }
        });

        if (collapseElement.classList.contains('show')) {
            collapseElement.classList.remove('show');
        } else {
            collapseElement.classList.add('show');
        }
    }
    // **************************************************************************************

    // Function เพื่อตรวจสอบรหัสผ่านว่าตรงกันหรือไม่
    function checkPassword(form) {
        var password1 = form.password1.value;
        var password2 = form.password2.value;

        // ถ้าช่องรหัสผ่านไม่ถูกกรอก
        if (password1 === '') {
            alert("Please enter Password");
            return false;
        }

        // ถ้าช่องยืนยันรหัสผ่านไม่ถูกกรอก
        else if (password2 === '') {
            alert("Please enter confirm password");
            return false;
        }

        // ถ้าทั้งสองช่องไม่ตรงกัน ให้แจ้งผู้ใช้ และ return false
        else if (password1 !== password2) {
            alert("Password did not match: Please try again...");
            return false;
        }

        // ถ้าทั้งสองช่องตรงกัน return true
        else {
            alert("Password Match: Welcome to Mindphp!");
            return true;
        }
    }
    // ******************************************************************


    // เปิด-ปิด รหัสผ่าน *********************************************************
    function swapPasswordType() {
        var passwordInput = document.getElementById("m_password");

        // เปลี่ยนประเภทของ Input จาก text เป็น password หรือ ngượcกัน
        passwordInput.type = (passwordInput.type === "password") ? "text" : "password";
    }

    function swapPasswordTypeConfirm() {
        var passwordInput = document.getElementById("confirm_password");

        // เปลี่ยนประเภทของ Input จาก text เป็น password หรือ ngượcกัน
        passwordInput.type = (passwordInput.type === "password") ? "text" : "password";
    }
    // ******************************************************************

    // คำหยาบ questions *************************************************
    $(document).ready(function() {
        // เมื่อคลิกปุ่ม "จัดการ"
        $(".insert-questions-btn").click(function() {
            var target = $(this).data("target");
            $(target).show();
        });

        // เมื่อคลิกปุ่ม "ปิด"
        $(".close-button").click(function() {
            var target = $(this).data("target");
            $(target).hide();
        });
    });
    // ******************************************************************

    //member จังหวัด ******************************************************
    $(document).ready(function() {
        $('#province').change(function() {
            var province_name = $(this).val();
            $.ajax({
                url: "<?php echo site_url('member_backend/get_amphurs'); ?>",
                method: "POST",
                data: {
                    province_name: province_name
                },
                dataType: 'json',
                success: function(data) {
                    $('#amphur').html('<option value="">เลือกอำเภอ</option>');
                    $('#tambol').html('<option value="">เลือกตำบล</option>'); // เพิ่มส่วนนี้

                    $.each(data, function(key, value) {
                        $('#amphur').append('<option value="' + value.tambol_aname + '">' + value.tambol_aname + '</option>');
                    });
                }
            });
        });

        $('#amphur').change(function() { // เพิ่มส่วนนี้
            var province_name = $('#province').val();
            var amphur_name = $(this).val();
            $.ajax({
                url: "<?php echo site_url('member_backend/get_tambols'); ?>",
                method: "POST",
                data: {
                    province_name: province_name,
                    amphur_name: amphur_name
                },
                dataType: 'json',
                success: function(data) {
                    $('#tambol').html('<option value="">เลือกตำบล</option>');

                    $.each(data, function(key, value) {
                        $('#tambol').append('<option value="' + value.tambol_tname + '">' + value.tambol_tname + '</option>');
                    });
                }
            });
        });
    });

    // ***************************************************

    //personnel *********************************************
    $(document).ready(function() {
        // เมื่อเลือก "แผนก" ใน dropdown "personnelGroup"
        $('#personnelGroup').change(function() {
            var selectedGroup = $(this).val();
            if (selectedGroup) {
                // ใช้ AJAX เรียกข้อมูล "ส่วนงาน" จาก Controller
                $.ajax({
                    url: '<?php echo site_url('personnel/get_departments'); ?>',
                    type: 'post',
                    data: {
                        group_name: selectedGroup
                    },
                    dataType: 'json',
                    success: function(data) {
                        // เมื่อได้ข้อมูล "ส่วนงาน" ให้เพิ่มลงใน dropdown "personnelDepartment"
                        $('#personnelDepartment').html('<option value="">เลือกส่วนงาน</option>');
                        $.each(data, function(key, value) {
                            $('#personnelDepartment').append('<option value="' + value.pgroup_dname + '">' + value.pgroup_dname + '</option>');
                        });
                    }
                });
            } else {
                // ถ้าไม่มีการเลือก "แผนก" ให้ล้าง dropdown "personnelDepartment"
                $('#personnelDepartment').html('<option value="">เลือกส่วนงาน</option>');
            }
        });
    });


    $(document).ready(function() {
        // เมื่อเลือก "แผนก" ใน dropdown "personnelGroup"
        $('#personnelGroup').change(function() {
            var selectedGroup = $(this).val();
            if (selectedGroup) {
                // ใช้ AJAX เรียกข้อมูล "ส่วนงาน" จาก Controller
                $.ajax({
                    url: '<?php echo site_url('personnelSuper/get_departments'); ?>',
                    type: 'post',
                    data: {
                        group_name: selectedGroup
                    },
                    dataType: 'json',
                    success: function(data) {
                        // เมื่อได้ข้อมูล "ส่วนงาน" ให้เพิ่มลงใน dropdown "personnelDepartment"
                        $('#personnelDepartment').html('<option value="">เลือกส่วนงาน</option>');
                        $.each(data, function(key, value) {
                            $('#personnelDepartment').append('<option value="' + value.pgroup_dname + '">' + value.pgroup_dname + '</option>');
                        });
                    }
                });
            } else {
                // ถ้าไม่มีการเลือก "แผนก" ให้ล้าง dropdown "personnelDepartment"
                $('#personnelDepartment').html('<option value="">เลือกส่วนงาน</option>');
            }
        });
    });


    $(document).ready(function() {
        // เมื่อเลือก "แผนก" ใน dropdown "personnelGroup"
        $('#personnelGroup').change(function() {
            var selectedGroup = $(this).val();
            if (selectedGroup) {
                // ใช้ AJAX เรียกข้อมูล "ส่วนงาน" จาก Controller
                $.ajax({
                    url: '<?php echo site_url('personnelApprove/get_departments'); ?>',
                    type: 'post',
                    data: {
                        group_name: selectedGroup
                    },
                    dataType: 'json',
                    success: function(data) {
                        // เมื่อได้ข้อมูล "ส่วนงาน" ให้เพิ่มลงใน dropdown "personnelDepartment"
                        $('#personnelDepartment').html('<option value="">เลือกส่วนงาน</option>');
                        $.each(data, function(key, value) {
                            $('#personnelDepartment').append('<option value="' + value.pgroup_dname + '">' + value.pgroup_dname + '</option>');
                        });
                    }
                });
            } else {
                // ถ้าไม่มีการเลือก "แผนก" ให้ล้าง dropdown "personnelDepartment"
                $('#personnelDepartment').html('<option value="">เลือกส่วนงาน</option>');
            }
        });
    });
    // ***************************************************


    function showSubmenu(submenuId) {
        var submenu = document.getElementById(submenuId);
        submenu.style.display = "block";

        // ซ่อน ul.logout
        var logoutMenu = document.querySelector(".logout");
        logoutMenu.style.display = "none";
    }

    function hideSubmenu(submenuId) {
        var submenu = document.getElementById(submenuId);
        submenu.style.display = "none";

        // แสดง ul.logout อีกครั้ง
        var logoutMenu = document.querySelector(".logout");
        logoutMenu.style.display = "block";
    }

    $(document).ready(function() {
        <?php if ($this->session->flashdata('save_success')) { ?>
            Swal.fire({
                // position: 'top-end',
                icon: 'success',
                title: 'บันทึกข้อมูลสำเร็จ',
                showConfirmButton: false,
                timer: 1500
            })
        <?php } ?>
    });

    $(document).ready(function() {
        <?php if ($this->session->flashdata('save_required')) { ?>
            Swal.fire({
                icon: 'warning',
                title: 'ตรวจพบปัญหา',
                text: 'กรุณากรอกข้อมูลที่มี * ให้ครบทุกช่อง',
                footer: '<a href="#">ติดต่อผู้ดูแลระบบ?</a>'
            })
        <?php } ?>
    });

    $(document).ready(function() {
        <?php if ($this->session->flashdata('save_error')) { ?>
            Swal.fire({
                icon: 'error',
                title: 'ตรวจพบปัญหา',
                text: 'หน่วยความจำของท่านเต็ม!',
                footer: '<a href="#">ติดต่อผู้ดูแลระบบ?</a>'
            })
        <?php } ?>
    });

    $(document).ready(function() {
        <?php if ($this->session->flashdata('save_maxsize')) { ?>
            Swal.fire({
                icon: 'error',
                title: 'ตรวจพบปัญหา',
                text: 'ขนาดรูปภาพต้องไม่เกิน 1.5MB!',
                footer: '<a href="#">ติดต่อผู้ดูแลระบบ?</a>'
            })
        <?php } ?>
    });

    $(document).ready(function() {
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

    $(document).ready(function() {
        <?php if ($this->session->flashdata('save_again')) { ?>
            Swal.fire({
                icon: 'warning',
                title: 'ตรวจพบปัญหา',
                text: 'มีข้อมูลอยู่แล้ว!',
                footer: '<a href="#">ติดต่อผู้ดูแลระบบ?</a>'
            })
        <?php } ?>
    });

    $(document).ready(function() {
        <?php if ($this->session->flashdata('password_mismatch')) { ?>
            Swal.fire({
                icon: 'warning',
                title: 'ตรวจพบปัญหา',
                text: 'รหัสผ่านไม่ตรงกัน!',
                footer: '<a href="#">ติดต่อผู้ดูแลระบบ?</a>'
            })
        <?php } ?>
    });

    // คอมเม้น comment 
    $(document).ready(function() {
        // ให้ทำงานเมื่อคลิกที่ปุ่ม "แสดงความคิดเห็นตอบกลับ"
        $('.show-reply-btn').on('click', function() {
            // ให้หาความคิดเห็นตอบกลับที่อยู่ในแถวปัจจุบัน
            var $commentRow = $(this).closest('.comment-row');
            var $replyRow = $commentRow.nextUntil('.comment-row', '.reply-row');

            // ถ้าแถวความคิดเห็นตอบกลับยังไม่แสดง
            if ($replyRow.is(':hidden')) {
                $replyRow.show(); // แสดงแถวความคิดเห็นตอบกลับ
            } else {
                $replyRow.hide(); // ซ่อนแถวความคิดเห็นตอบกลับ
            }
        });
    });

    // ปุ่มค้นหาคอมเม้น search comment
    $(document).ready(function() {
        $('#searchButton').click(function() {
            var searchTerm = $('#searchInput').val().toLowerCase();
            if (searchTerm === "") {
                // ถ้าไม่ได้ป้อนคำค้นหา ให้แสดงข้อมูลทั้งหมด
                $('tr.comment-row').show();
            } else {
                // ถ้ามีคำค้นหา ให้ซ่อนแถวที่ไม่ตรง
                $('tr.comment-row').each(function() {
                    var commentText = $(this).find('.limited-text').text().toLowerCase();
                    if (commentText.indexOf(searchTerm) === -1) {
                        $(this).hide();
                    } else {
                        $(this).show();
                    }
                });
            }
        });
    });

    //report_user ******************************************************
    function toggleTable(id) {
        // ซ่อนทุกตารางย่อยทั้งหมด
        var subTables = document.querySelectorAll('.card');
        for (var i = 0; i < subTables.length; i++) {
            subTables[i].style.display = 'none';
        }

        // แสดงตารางที่ถูกคลิก
        var tableToShow = document.getElementById(id);
        tableToShow.style.display = 'block';
    }

    function scrollToTable(id) {
        var target = document.getElementById(id);
        if (target) {
            target.style.display = 'block';
            var offsetTop = target.offsetTop;
            window.scrollTo(0, offsetTop);
        }
    }

    // ***************************************************

    // ปุ่มกดหน้าเรื่องร้องเรียน **********************************
    $(document).ready(function() {
        // ซ่อนทุกปุ่ม "จัดการ"
        $(".update-complain-btn").hide();

        // แสดงปุ่ม "จัดการ" เฉพาะในแถวล่าสุด
        $(".data-row:last .update-complain-btn").show();

        // ถ้าสถานะเป็น "ยกเลิก" หรือ "แก้ไขเรียบร้อย" ให้ซ่อนปุ่มทันที
        $(".data-row").each(function() {
            var status = $(this).find(".complain_status").text().trim(); // แสดงสถานะจากข้อมูลและลบช่องว่างด้านหลังและด้านหน้า
            if (status === 'ยกเลิก' || status === 'แก้ไขเรียบร้อย') {
                $(this).find(".manage-button").hide();
            }
        });
    });

    $(document).ready(function() {
        // เมื่อคลิกปุ่ม "จัดการ"
        $(".update-complain-btn").click(function() {
            var target = $(this).data("target");
            $(target).show();
        });

        // เมื่อคลิกปุ่ม "ปิด"
        $(".close-button").click(function() {
            var target = $(this).data("target");
            $(target).hide();
        });
    });

    $(document).ready(function() {
        // เมื่อคลิกปุ่ม "จัดการ"
        $(".cancel-complain-btn").click(function() {
            var target = $(this).data("target");
            $(target).show();
        });

        // เมื่อคลิกปุ่ม "ปิด"
        $(".close-button").click(function() {
            var target = $(this).data("target");
            $(target).hide();
        });
    });
    // ***************************************************

    // คำหยาบ vulgar **********************************
$(document).ready(function() {
   console.log("Document ready!"); // ตรวจสอบว่า ready function ทำงาน

   $(".insert-vulgar-btn").click(function() {
       var target = $(this).data("target");
       console.log("Button clicked! Target:", target); // ตรวจสอบว่า target ถูกต้องหรือไม่
       $(target).show(); // แสดง popup ที่เกี่ยวข้อง
   });

   $(".close-button").click(function() {
       var target = $(this).data("target");
       $(target).hide(); // ซ่อน popup
   });
});

    // ***************************************************


    // เพิ่มไฟล์ทีละไฟล์
    function toggleFiles() {
        var file2 = document.getElementById("doc_file2");
        var file3 = document.getElementById("doc_file3");

        if (file2.style.display === "none") {
            file2.style.display = "block";
        } else {
            file3.style.display = "block";
        }
    }
    // ***************************************************

    // ลบไฟล์ทีละไฟล์
    document.addEventListener("DOMContentLoaded", function() {
        const deleteLinks = document.querySelectorAll(".delete-file");

        deleteLinks.forEach(function(link) {
            link.addEventListener("click", function(event) {
                event.preventDefault();
                const fieldName = this.getAttribute("data-field");
                const fileInput = document.querySelector(`input[name="${fieldName}"]`);
                const hiddenInput = document.querySelector(`input[name="${fieldName}_hidden"]`);

                // Clear file input and update hidden input
                fileInput.value = "";
                hiddenInput.value = "";
            });
        });
    });
    // ***************************************************

    // ตาราง table ***************************************
    $(document).ready(function() {
        var thaiLanguage = {
            "emptyTable": "ไม่มีข้อมูลในตาราง",
            "info": "แสดง _START_ ถึง _END_ จาก _TOTAL_ แถว",
            "infoFiltered": "(กรองข้อมูล _MAX_ ทุกแถว)",
            "infoThousands": ",",
            "lengthMenu": "แสดง _MENU_ แถว",
            "loadingRecords": "กำลังโหลดข้อมูล...",
            "processing": "กำลังดำเนินการ...",
            "zeroRecords": "ไม่พบข้อมูล",
            "paginate": {
                "first": "หน้าแรก",
                "previous": "ก่อนหน้า",
                "next": "ถัดไป",
                "last": "หน้าสุดท้าย"
            },
            "aria": {
                "sortAscending": ": เปิดใช้งานการเรียงข้อมูลจากน้อยไปมาก",
                "sortDescending": ": เปิดใช้งานการเรียงข้อมูลจากมากไปน้อย"
            },
            "autoFill": {
                "cancel": "ยกเลิก",
                "fill": "กรอกทุกช่องด้วย",
                "fillHorizontal": "กรอกตามแนวนอน",
                "fillVertical": "กรอกตามแนวตั้ง"
            },
            "buttons": {
                "collection": "ชุดข้อมูล",
                "colvis": "การมองเห็นคอลัมน์",
                "colvisRestore": "เรียกคืนการมองเห็น",
                "copy": "คัดลอก",
                "copyKeys": "กดปุ่ม Ctrl หรือ Command + C เพื่อคัดลอกข้อมูลบนตารางไปยัง Clipboard ที่เครื่องของคุณ",
                "copySuccess": {
                    "_": "คัดลอกช้อมูลแล้ว จำนวน %ds แถว",
                    "1": "คัดลอกข้อมูลแล้ว จำนวน 1 แถว"
                },
                "copyTitle": "คัดลอกไปยังคลิปบอร์ด",
                "csv": "CSV",
                "excel": "Excel",
                "pageLength": {
                    "_": "แสดงข้อมูล %d แถว",
                    "-1": "แสดงข้อมูลทั้งหมด"
                },
                "pdf": "PDF",
                "print": "สั่งพิมพ์",
                "createState": "สร้างสถานะ",
                "removeAllStates": "ลบสถานะทั้งหมด",
                "removeState": "ลบสถานะ",
                "renameState": "เปลี่ยนชื่อสถานะ",
                "savedStates": "บันทึกสถานะ",
                "stateRestore": "คืนค่าสถานะ",
                "updateState": "แก้ไขสถานะ"
            },
            "infoEmpty": "แสดงทั้งหมด 0 to 0 of 0 รายการ",
            "search": "ค้นหา :",
            "thousands": ",",
            "datetime": {
                "amPm": [
                    "เที่ยงวัน",
                    "เที่ยงคืน"
                ],
                "hours": "ชั่วโมง",
                "minutes": "นาที",
                "months": {
                    "0": "มกราคม",
                    "1": "กุมภาพันธ์",
                    "10": "พฤศจิกายน",
                    "11": "ธันวาคม",
                    "2": "มีนาคม",
                    "3": "เมษายน",
                    "4": "พฤษภาคม",
                    "5": "มิถุนายน",
                    "6": "กรกฎาคม",
                    "7": "สิงหาคม",
                    "8": "กันยายน",
                    "9": "ตุลาคม"
                },
                "next": "ถัดไป",
                "seconds": "วินาที",
                "unknown": "ไม่ทราบ",
                "weekdays": [
                    "วันอาทิตย์",
                    "วันจันทร์",
                    "วันอังคาร",
                    "วันพุธ",
                    "วันพฤหัส",
                    "วันศุกร์",
                    "วันเสาร์"
                ],
                "previous": "ก่อนหน้า"
            },
            "decimal": "จุดทศนิยม",
            "editor": {
                "close": "ปิด",
                "create": {
                    "button": "สร้าง",
                    "submit": "สร้างข้อมูล",
                    "title": "สร้างข้อมูลใหม่"
                },
                "edit": {
                    "button": "แก้ไข",
                    "submit": "บันทึก",
                    "title": "แก้ไขข้อมูล"
                },
                "error": {
                    "system": "เกิดข้อผิดพลาดของระบบ (&lt;a target=\"\\\" rel=\"nofollow\" href=\"\\\"&gt;ดูข้อมูลเพิ่มเติม)."
                },
                "remove": {
                    "button": "ลบ",
                    "submit": "ลบข้อมูล",
                    "title": "ลบข้อมูล",
                    "confirm": {
                        "_": "คุณแน่ใจที่จะลบข้อมูล %d รายการนี้ หรือไม่?",
                        "1": "คุณแน่ใจที่จะลบข้อมูลรายการนี้ หรือไม่?"
                    }
                },
                "multi": {
                    "restore": "ยกเลิกการแก้ไข",
                    "title": "หลายค่า",
                    "info": "รายการที่เลือกมีค่าที่แตกต่างกันสำหรับอินพุตนี้ หากต้องการแก้ไขและตั้งค่ารายการทั้งหมดสำหรับการป้อนข้อมูลนี้เป็นค่าเดียวกัน ให้คลิกหรือแตะที่นี่ มิฉะนั้น รายการเหล่านั้นจะคงค่าแต่ละรายการไว้",
                    "noMulti": "อินพุตนี้สามารถแก้ไขทีละรายการได้ แต่ไม่สามารถแก้ไขเป็นส่วนหนึ่งของกลุ่มได้"
                }
            },
            "searchBuilder": {
                "add": "เพิ่มเงื่อนไข",
                "clearAll": "ยกเลิกทั้งหมด",
                "condition": "เงื่อนไข",
                "data": "ข้อมูล",
                "deleteTitle": "ลบเงื่อนไขการกรอง",
                "logicAnd": "และ",
                "logicOr": "หรือ",
                "button": {
                    "0": "สร้างการค้นหา",
                    "_": "ตัวสร้างการค้นหา (%d)"
                },
                "conditions": {
                    "date": {
                        "after": "ก่อน",
                        "before": "ก่อน",
                        "between": "ระหว่าง",
                        "equals": "เท่ากับ",
                        "not": "ไม่",
                        "notEmpty": "ไม่ใช่ระหว่าง"
                    },
                    "number": {
                        "between": "ระหว่าง",
                        "equals": "เท่ากับ",
                        "gt": "มากกว่า",
                        "gte": "มากกว่าเท่ากับ",
                        "lt": "น้อยกว่า",
                        "lte": "น้อยกว่าเท่ากับ",
                        "not": "ไม่",
                        "notBetween": "ไม่ใช่ระหว่าง"
                    },
                    "string": {
                        "contains": "ประกอบด้วย",
                        "endsWith": "ลงท้ายด้วย",
                        "equals": "เท่ากับ",
                        "not": "ไม่",
                        "startsWith": "เริ่มต้นด้วย",
                        "notContains": "ไม่มี",
                        "notStartsWith": "ไม่เริ่มต้นด้วย",
                        "notEndsWith": "ไม่ลงท้ายด้วย"
                    },
                    "array": {
                        "equals": "เท่ากับ",
                        "contains": "เงื้อนไข",
                        "not": "ไม่"
                    }
                },
                "title": {
                    "0": "สร้างการค้นหา",
                    "_": "ตัวสร้างการค้นหา (%d)"
                },
                "value": "ค่า"
            },
            "select": {
                "cells": {
                    "1": "เลือก 1 cell",
                    "_": "เลือก %d cells"
                },
                "columns": {
                    "1": "เลือก 1 column",
                    "_": "เลือก %d columns"
                }
            },
            "stateRestore": {
                "duplicateError": "มีข้อมูลที่ใช้ชื่อนี้แล้ว",
                "emptyError": "ชื่อต้องไม่เป็นค่าว่าง",
                "emptyStates": "ไม่มีสถานะที่บันทึกไว้",
                "removeConfirm": "คุณแน่ใจหรือไม่ว่าต้องการลบ %s",
                "removeError": "ไม่สามารถลบสถานะ"
            }
        };
        var thaiLanguage = $('#reportTableNews').DataTable(); // ระบุตารางที่ต้องการใช้งาน DataTables
        var thaiLanguage = $('#reportTableActivity').DataTable(); // ระบุตารางที่ต้องการใช้งาน DataTables
        var thaiLanguage = $('#reportTableHealth').DataTable(); // ระบุตารางที่ต้องการใช้งาน DataTables
        var thaiLanguage = $('#reportTableTravel').DataTable(); // ระบุตารางที่ต้องการใช้งาน DataTables
        var thaiLanguage = $('#reportTableFood').DataTable(); // ระบุตารางที่ต้องการใช้งาน DataTables
        var thaiLanguage = $('#reportTableOtop').DataTable(); // ระบุตารางที่ต้องการใช้งาน DataTables
        var thaiLanguage = $('#reportTableStore').DataTable(); // ระบุตารางที่ต้องการใช้งาน DataTables
        var thaiLanguage = $('#reportTableStoreUser').DataTable(); // ระบุตารางที่ต้องการใช้งาน DataTables


        $('#newdataTables').DataTable({
            responsive: true,
            language: thaiLanguage
        });
    });
    $(document).ready(function() {
        var thaiLanguage = {
            "emptyTable": "ไม่มีข้อมูลในตาราง",
            "info": "แสดง _START_ ถึง _END_ จาก _TOTAL_ แถว",
            "infoFiltered": "(กรองข้อมูล _MAX_ ทุกแถว)",
            "infoThousands": ",",
            "lengthMenu": "แสดง _MENU_ แถว",
            "loadingRecords": "กำลังโหลดข้อมูล...",
            "processing": "กำลังดำเนินการ...",
            "zeroRecords": "ไม่พบข้อมูล",
            "paginate": {
                "first": "หน้าแรก",
                "previous": "ก่อนหน้า",
                "next": "ถัดไป",
                "last": "หน้าสุดท้าย"
            },
            "aria": {
                "sortAscending": ": เปิดใช้งานการเรียงข้อมูลจากน้อยไปมาก",
                "sortDescending": ": เปิดใช้งานการเรียงข้อมูลจากมากไปน้อย"
            },
            "autoFill": {
                "cancel": "ยกเลิก",
                "fill": "กรอกทุกช่องด้วย",
                "fillHorizontal": "กรอกตามแนวนอน",
                "fillVertical": "กรอกตามแนวตั้ง"
            },
            "buttons": {
                "collection": "ชุดข้อมูล",
                "colvis": "การมองเห็นคอลัมน์",
                "colvisRestore": "เรียกคืนการมองเห็น",
                "copy": "คัดลอก",
                "copyKeys": "กดปุ่ม Ctrl หรือ Command + C เพื่อคัดลอกข้อมูลบนตารางไปยัง Clipboard ที่เครื่องของคุณ",
                "copySuccess": {
                    "_": "คัดลอกช้อมูลแล้ว จำนวน %ds แถว",
                    "1": "คัดลอกข้อมูลแล้ว จำนวน 1 แถว"
                },
                "copyTitle": "คัดลอกไปยังคลิปบอร์ด",
                "csv": "CSV",
                "excel": "Excel",
                "pageLength": {
                    "_": "แสดงข้อมูล %d แถว",
                    "-1": "แสดงข้อมูลทั้งหมด"
                },
                "pdf": "PDF",
                "print": "สั่งพิมพ์",
                "createState": "สร้างสถานะ",
                "removeAllStates": "ลบสถานะทั้งหมด",
                "removeState": "ลบสถานะ",
                "renameState": "เปลี่ยนชื่อสถานะ",
                "savedStates": "บันทึกสถานะ",
                "stateRestore": "คืนค่าสถานะ",
                "updateState": "แก้ไขสถานะ"
            },
            "infoEmpty": "แสดงทั้งหมด 0 to 0 of 0 รายการ",
            "search": "ค้นหา :",
            "thousands": ",",
            "datetime": {
                "amPm": [
                    "เที่ยงวัน",
                    "เที่ยงคืน"
                ],
                "hours": "ชั่วโมง",
                "minutes": "นาที",
                "months": {
                    "0": "มกราคม",
                    "1": "กุมภาพันธ์",
                    "10": "พฤศจิกายน",
                    "11": "ธันวาคม",
                    "2": "มีนาคม",
                    "3": "เมษายน",
                    "4": "พฤษภาคม",
                    "5": "มิถุนายน",
                    "6": "กรกฎาคม",
                    "7": "สิงหาคม",
                    "8": "กันยายน",
                    "9": "ตุลาคม"
                },
                "next": "ถัดไป",
                "seconds": "วินาที",
                "unknown": "ไม่ทราบ",
                "weekdays": [
                    "วันอาทิตย์",
                    "วันจันทร์",
                    "วันอังคาร",
                    "วันพุธ",
                    "วันพฤหัส",
                    "วันศุกร์",
                    "วันเสาร์"
                ],
                "previous": "ก่อนหน้า"
            },
            "decimal": "จุดทศนิยม",
            "editor": {
                "close": "ปิด",
                "create": {
                    "button": "สร้าง",
                    "submit": "สร้างข้อมูล",
                    "title": "สร้างข้อมูลใหม่"
                },
                "edit": {
                    "button": "แก้ไข",
                    "submit": "บันทึก",
                    "title": "แก้ไขข้อมูล"
                },
                "error": {
                    "system": "เกิดข้อผิดพลาดของระบบ (&lt;a target=\"\\\" rel=\"nofollow\" href=\"\\\"&gt;ดูข้อมูลเพิ่มเติม)."
                },
                "remove": {
                    "button": "ลบ",
                    "submit": "ลบข้อมูล",
                    "title": "ลบข้อมูล",
                    "confirm": {
                        "_": "คุณแน่ใจที่จะลบข้อมูล %d รายการนี้ หรือไม่?",
                        "1": "คุณแน่ใจที่จะลบข้อมูลรายการนี้ หรือไม่?"
                    }
                },
                "multi": {
                    "restore": "ยกเลิกการแก้ไข",
                    "title": "หลายค่า",
                    "info": "รายการที่เลือกมีค่าที่แตกต่างกันสำหรับอินพุตนี้ หากต้องการแก้ไขและตั้งค่ารายการทั้งหมดสำหรับการป้อนข้อมูลนี้เป็นค่าเดียวกัน ให้คลิกหรือแตะที่นี่ มิฉะนั้น รายการเหล่านั้นจะคงค่าแต่ละรายการไว้",
                    "noMulti": "อินพุตนี้สามารถแก้ไขทีละรายการได้ แต่ไม่สามารถแก้ไขเป็นส่วนหนึ่งของกลุ่มได้"
                }
            },
            "searchBuilder": {
                "add": "เพิ่มเงื่อนไข",
                "clearAll": "ยกเลิกทั้งหมด",
                "condition": "เงื่อนไข",
                "data": "ข้อมูล",
                "deleteTitle": "ลบเงื่อนไขการกรอง",
                "logicAnd": "และ",
                "logicOr": "หรือ",
                "button": {
                    "0": "สร้างการค้นหา",
                    "_": "ตัวสร้างการค้นหา (%d)"
                },
                "conditions": {
                    "date": {
                        "after": "ก่อน",
                        "before": "ก่อน",
                        "between": "ระหว่าง",
                        "equals": "เท่ากับ",
                        "not": "ไม่",
                        "notEmpty": "ไม่ใช่ระหว่าง"
                    },
                    "number": {
                        "between": "ระหว่าง",
                        "equals": "เท่ากับ",
                        "gt": "มากกว่า",
                        "gte": "มากกว่าเท่ากับ",
                        "lt": "น้อยกว่า",
                        "lte": "น้อยกว่าเท่ากับ",
                        "not": "ไม่",
                        "notBetween": "ไม่ใช่ระหว่าง"
                    },
                    "string": {
                        "contains": "ประกอบด้วย",
                        "endsWith": "ลงท้ายด้วย",
                        "equals": "เท่ากับ",
                        "not": "ไม่",
                        "startsWith": "เริ่มต้นด้วย",
                        "notContains": "ไม่มี",
                        "notStartsWith": "ไม่เริ่มต้นด้วย",
                        "notEndsWith": "ไม่ลงท้ายด้วย"
                    },
                    "array": {
                        "equals": "เท่ากับ",
                        "contains": "เงื้อนไข",
                        "not": "ไม่"
                    }
                },
                "title": {
                    "0": "สร้างการค้นหา",
                    "_": "ตัวสร้างการค้นหา (%d)"
                },
                "value": "ค่า"
            },
            "select": {
                "cells": {
                    "1": "เลือก 1 cell",
                    "_": "เลือก %d cells"
                },
                "columns": {
                    "1": "เลือก 1 column",
                    "_": "เลือก %d columns"
                }
            },
            "stateRestore": {
                "duplicateError": "มีข้อมูลที่ใช้ชื่อนี้แล้ว",
                "emptyError": "ชื่อต้องไม่เป็นค่าว่าง",
                "emptyStates": "ไม่มีสถานะที่บันทึกไว้",
                "removeConfirm": "คุณแน่ใจหรือไม่ว่าต้องการลบ %s",
                "removeError": "ไม่สามารถลบสถานะ"
            }
        };

        $('#importantday').DataTable({
            responsive: true,
            language: thaiLanguage,
            pageLength: 25 // กำหนดให้แสดง 25 รายการต่อหน้า
        });
    });
    // *********************************************************
    function showAlert() {
        Swal.fire({
            icon: 'warning',
            title: 'กรุณาเปิดอย่างน้อย 1 อัน',
            confirmButtonText: 'ตกลง'
        });
    }
	
	
    /**
     * SortableManager - ระบบจัดการการลากวางสำหรับหลายหน้าในเว็บไซต์
     * สามารถใช้ได้กับหลายประเภทของแบบจำลอง เช่น p_executives, p_council, etc.
     */
    class SortableManager {
        /**
         * สร้าง SortableManager instance ใหม่
         * @param {Object} config - การตั้งค่าสำหรับ instance นี้
         * @param {string} config.containerSelector - CSS selector ของ container ที่มี sortable items
         * @param {string} config.switchSelector - CSS selector ของ switch ที่ใช้เปิด/ปิดโหมดลาก
         * @param {string} config.saveButtonSelector - CSS selector ของปุ่มบันทึก
         * @param {string} config.cancelButtonSelector - CSS selector ของปุ่มยกเลิก
         * @param {string} config.dragControlsSelector - CSS selector ของคอนโทรลลากวาง
         * @param {string} config.successMessageSelector - CSS selector ของข้อความสำเร็จ
         * @param {string} config.errorMessageSelector - CSS selector ของข้อความผิดพลาด
         * @param {string} config.itemSelector - CSS selector ของรายการที่ลากวางได้
         * @param {string} config.updateUrl - URL สำหรับส่งคำขอ AJAX เพื่ออัพเดตตำแหน่ง
         * @param {string} config.modelName - ชื่อโมเดล (เช่น 'p_executives', 'p_council')
         */
        constructor(config) {
            this.config = config;
            this.container = document.querySelector(config.containerSelector);
            this.dragModeSwitch = document.querySelector(config.switchSelector);
            this.saveButton = document.querySelector(config.saveButtonSelector);
            this.cancelButton = document.querySelector(config.cancelButtonSelector);
            this.dragControls = document.querySelector(config.dragControlsSelector);
            this.successMessage = document.querySelector(config.successMessageSelector);
            this.errorMessage = document.querySelector(config.errorMessageSelector);
            this.itemSelector = config.itemSelector;
            this.updateUrl = config.updateUrl;
            this.modelName = config.modelName;

            // ตัวแปรเก็บสถานะ
            this.hasChanges = false;
            this.sortable = null;

            // ตรวจสอบว่า element ที่จำเป็นมีอยู่จริง
            if (!this.container) {
                console.error(`Container with selector ${config.containerSelector} not found`);
                return;
            }

            // ตั้งค่า event listeners
            this.setupEventListeners();
            this.disableDragMode(); // เริ่มต้นในโหมดปกติ (ไม่ลากไม่วาง)
        }

        /**
         * ตั้งค่า event listeners ทั้งหมด
         */
        setupEventListeners() {
            // Switch drag mode
            if (this.dragModeSwitch) {
                this.dragModeSwitch.addEventListener('change', () => {
                    if (this.dragModeSwitch.checked) {
                        this.enableDragMode();
                    } else {
                        this.disableDragMode();
                    }
                });
            } else {
                console.warn(`Switch with selector ${this.config.switchSelector} not found`);
            }

            // Save button
            if (this.saveButton) {
                this.saveButton.addEventListener('click', () => {
                    this.savePositions();
                });
            } else {
                console.warn(`Save button with selector ${this.config.saveButtonSelector} not found`);
            }

            // Cancel button
            if (this.cancelButton) {
                this.cancelButton.addEventListener('click', () => {
                    this.cancelChanges();
                });
            } else {
                console.warn(`Cancel button with selector ${this.config.cancelButtonSelector} not found`);
            }
        }

        /**
         * เปิดโหมดลากและวาง
         */
        enableDragMode() {
            // แสดงคอนโทรลสำหรับลากวาง
            if (this.dragControls) {
                this.dragControls.style.display = 'block';
            }

            // เพิ่มคลาสให้ body หรือ container หลัก
            document.body.classList.add('drag-mode');

            // ปิดการทำงานของลิงก์ใน sortable-item เพื่อให้สามารถลากได้
            const links = document.querySelectorAll(`${this.itemSelector} a`);
            links.forEach(link => {
                link.addEventListener('click', this.preventLinkClick);
            });

            // สร้าง Sortable instance
            if (typeof Sortable !== 'undefined') {
                this.sortable = new Sortable(this.container, {
                    animation: 150,
                    ghostClass: 'sortable-ghost',
                    chosenClass: 'sortable-chosen',
                    dragClass: 'sortable-drag',
                    handle: '.drag-handle', // ใช้ .drag-handle เป็นจุดจับลาก
                    onStart: () => {
                        // อะไรบางอย่างเมื่อเริ่มลาก
                    },
                    onEnd: () => {
                        // เมื่อลากเสร็จ บันทึกการเปลี่ยนแปลง
                        this.hasChanges = true;
                        this.updateSaveButton();
                    }
                });
            } else {
                console.error('Sortable.js is not loaded. Please include the library.');
            }
        }

        /**
         * ปิดโหมดลากและวาง
         */
        disableDragMode() {
            // ซ่อนคอนโทรลสำหรับลากวาง
            if (this.dragControls) {
                this.dragControls.style.display = 'none';
            }

            // ลบคลาสจาก body หรือ container หลัก
            document.body.classList.remove('drag-mode');

            // เปิดการทำงานของลิงก์ใน sortable-item อีกครั้ง
            const links = document.querySelectorAll(`${this.itemSelector} a`);
            links.forEach(link => {
                link.removeEventListener('click', this.preventLinkClick);
            });

            // ทำลาย Sortable instance
            if (this.sortable) {
                this.sortable.destroy();
                this.sortable = null;
            }
        }

        /**
         * ป้องกันการคลิกลิงก์ในโหมดลาก
         * @param {Event} e - เหตุการณ์คลิก
         */
        preventLinkClick(e) {
            e.preventDefault();
        }

        /**
         * อัพเดตสถานะปุ่มบันทึก
         */
        updateSaveButton() {
            if (!this.saveButton) return;

            if (this.hasChanges) {
                this.saveButton.classList.add('btn-warning');
                this.saveButton.classList.remove('btn-primary');
                this.saveButton.innerHTML = '<i class="bi bi-save"></i> บันทึกตำแหน่ง <span class="badge bg-danger text-white">มีการเปลี่ยนแปลง!</span>';

                // เพิ่มแจ้งเตือนเมื่อพยายามออกจากหน้า
                window.onbeforeunload = () => {
                    return "คุณมีการเปลี่ยนแปลงที่ยังไม่ได้บันทึก ต้องการออกจากหน้านี้จริงหรือไม่?";
                };
            } else {
                this.saveButton.classList.remove('btn-warning');
                this.saveButton.classList.add('btn-primary');
                this.saveButton.innerHTML = '<i class="bi bi-save"></i> บันทึกตำแหน่ง';

                // ยกเลิกการแจ้งเตือนเมื่อออกจากหน้า
                window.onbeforeunload = null;
            }
        }

        /**
         * บันทึกตำแหน่งไปยังเซิร์ฟเวอร์
         */
        savePositions() {
            // เก็บข้อมูลตำแหน่งใหม่
            const positions = [];
            const items = document.querySelectorAll(this.itemSelector);

            // Debug info
            console.log(`Saving positions for ${this.modelName}:`);
            items.forEach((item, index) => {
                const id = item.getAttribute('data-id');
                console.log(`Position ${index}: ID ${id}`);
            });

            items.forEach((item, index) => {
                positions.push({
                    id: item.getAttribute('data-id'),
                    position: index
                });
            });

            // ส่งข้อมูลไปยัง server ด้วย AJAX (ใช้ fetch API)
            fetch(this.updateUrl, {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-Requested-With': 'XMLHttpRequest'
                    },
                    body: JSON.stringify({
                        positions: positions,
                        model: this.modelName // ส่งชื่อโมเดลไปด้วย (ถ้าต้องการให้ controller รองรับหลาย model)
                    })
                })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        this.handleSaveSuccess();
                    } else {
                        this.handleSaveError(data.message || 'เกิดข้อผิดพลาดในการบันทึกข้อมูล');
                    }
                })
                .catch(error => {
                    console.error('Error:', error);
                    this.handleSaveError('เกิดข้อผิดพลาดในการเชื่อมต่อกับเซิร์ฟเวอร์');
                });
        }

        /**
         * จัดการเมื่อบันทึกสำเร็จ
         */
        handleSaveSuccess() {
            if (this.successMessage) {
                this.successMessage.style.display = 'block';
            }
            if (this.errorMessage) {
                this.errorMessage.style.display = 'none';
            }

            // รีเซ็ตสถานะการเปลี่ยนแปลง
            this.hasChanges = false;
            this.updateSaveButton();

            // ซ่อนข้อความหลังจาก 3 วินาที
            if (this.successMessage) {
                setTimeout(() => {
                    this.successMessage.style.display = 'none';
                }, 3000);
            }

            // รีเฟรชหน้าหลังจาก 1 วินาที
            setTimeout(() => {
                window.location.reload();
            }, 1000);
        }

        /**
         * จัดการเมื่อบันทึกผิดพลาด
         * @param {string} message - ข้อความผิดพลาด
         */
        handleSaveError(message) {
            console.error(`Save error: ${message}`);

            if (this.errorMessage) {
                this.errorMessage.style.display = 'block';
                // ถ้ามี element สำหรับข้อความผิดพลาด อาจใส่ข้อความลงไปด้วย
                if (this.errorMessage.querySelector('.error-message-text')) {
                    this.errorMessage.querySelector('.error-message-text').textContent = message;
                }
            }

            if (this.successMessage) {
                this.successMessage.style.display = 'none';
            }

            // ซ่อนข้อความหลังจาก 3 วินาที
            if (this.errorMessage) {
                setTimeout(() => {
                    this.errorMessage.style.display = 'none';
                }, 3000);
            }
        }

        /**
         * ยกเลิกการเปลี่ยนแปลงและรีเฟรชหน้า
         */
        cancelChanges() {
            // ยืนยันก่อนรีเฟรช
            if (this.hasChanges) {
                if (confirm('คุณแน่ใจหรือไม่ว่าต้องการยกเลิกการเปลี่ยนแปลงทั้งหมด?')) {
                    window.location.reload();
                }
            } else {
                window.location.reload();
            }
        }
    }

    // Export as global if in browser
    if (typeof window !== 'undefined') {
        window.SortableManager = SortableManager;
    }
	
	
		
//========================== Member logging
/**
 * member_log_charts.js - สำหรับการสร้างกราฟหลักในหน้าแสดงผลกิจกรรมผู้ใช้
 */

document.addEventListener('DOMContentLoaded', function() {
    // ตั้งค่าสีสำหรับกราฟ
    const chartColors = {
        primary: '#6C63FF',
        primaryLight: 'rgba(108, 99, 255, 0.2)',
        success: '#36B37E',
        successLight: 'rgba(54, 179, 126, 0.2)',
        danger: '#FF5C5C',
        dangerLight: 'rgba(255, 92, 92, 0.2)',
        info: '#5E6A84',
        infoLight: 'rgba(94, 106, 132, 0.2)',
        warning: '#FFAB2B',
        warningLight: 'rgba(255, 171, 43, 0.2)',
        gray: ['#F9FAFC', '#EBEEF5', '#DFE3E8', '#C4CDD5', '#919EAB', '#637381', '#454F5B', '#212B36']
    };
    
    // กำหนดค่าเริ่มต้นสำหรับ Chart.js
    Chart.defaults.font.family = "'Poppins', 'Prompt', sans-serif";
    Chart.defaults.font.size = 12;
    Chart.defaults.color = '#637381';
    Chart.defaults.plugins.tooltip.padding = 15;
    Chart.defaults.plugins.tooltip.cornerRadius = 12;
    Chart.defaults.plugins.tooltip.titleFont = {
        size: 14,
        weight: 'bold'
    };
    Chart.defaults.plugins.tooltip.bodyFont = {
        size: 13
    };
    Chart.defaults.plugins.tooltip.backgroundColor = 'rgba(255, 255, 255, 0.95)';
    Chart.defaults.plugins.tooltip.borderColor = 'rgba(222, 226, 230, 0.9)';
    Chart.defaults.plugins.tooltip.borderWidth = 1;
    Chart.defaults.plugins.tooltip.usePointStyle = true;
    Chart.defaults.plugins.tooltip.boxPadding = 6;
    Chart.defaults.plugins.tooltip.caretSize = 6;
    Chart.defaults.plugins.tooltip.caretPadding = 10;
    Chart.defaults.plugins.tooltip.displayColors = true;
    Chart.defaults.plugins.tooltip.callbacks.labelTextColor = function(context) {
        return '#212B36';
    }
    
    // สร้างกราฟวงกลมแสดงประเภทกิจกรรม
    function createActivityTypeChart() {
        const activityTypeData = {
            labels: ['เข้าสู่ระบบล้มเหลว', 'เข้าสู่ระบบ', 'ออกจากระบบ'],
            datasets: [{
                data: [
                    failed_count, // ตัวแปรที่ได้จาก PHP
                    login_count,  // ตัวแปรที่ได้จาก PHP
                    logout_count  // ตัวแปรที่ได้จาก PHP
                ],
                backgroundColor: [
                    chartColors.danger,      // สีแดง - failed
                    chartColors.success,     // สีเขียว - login
                    chartColors.info         // สีเทา - logout
                ],
                borderWidth: 0,
                hoverOffset: 20,
                borderRadius: 4
            }]
        };
        
        new Chart(document.getElementById('activityTypeChart'), {
            type: 'doughnut',
            data: activityTypeData,
            options: {
                responsive: true,
                maintainAspectRatio: true,
                cutout: '70%',
                layout: {
                    padding: 20
                },
                plugins: {
                    legend: {
                        position: 'right',
                        labels: {
                            padding: 20,
                            usePointStyle: true,
                            pointStyle: 'circle',
                            font: {
                                size: 12,
                                weight: '500'
                            }
                        }
                    },
                    tooltip: {
                        callbacks: {
                            label: function(context) {
                                const label = context.label || '';
                                const value = context.raw || 0;
                                const total = context.chart.data.datasets[0].data.reduce((a, b) => a + b, 0);
                                const percentage = Math.round((value / total) * 100);
                                return `${label}: ${value} (${percentage}%)`;
                            }
                        }
                    }
                },
                animation: {
                    animateScale: true,
                    animateRotate: true,
                    duration: 1000
                }
            }
        });
    }
    
    // สร้างกราฟแท่งแสดงจำนวนการเข้าสู่ระบบของผู้ใช้
    function createUserLoginChart() {
        new Chart(document.getElementById('userLoginChart'), {
            type: 'bar',
            data: {
                labels: userLabels, // ตัวแปรที่ได้จาก PHP
                datasets: [{
                    label: 'จำนวนครั้งเข้าสู่ระบบ',
                    data: userLoginData, // ตัวแปรที่ได้จาก PHP
                    backgroundColor: chartColors.primaryLight,
                    borderColor: chartColors.primary,
                    borderWidth: 2,
                    borderRadius: 8,
                    maxBarThickness: 50,
                    hoverBackgroundColor: chartColors.primary
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: true,
                plugins: {
                    legend: {
                        display: false
                    },
                    tooltip: {
                        callbacks: {
                            label: function(context) {
                                return `จำนวนครั้งเข้าสู่ระบบ: ${context.raw} ครั้ง`;
                            }
                        }
                    }
                },
                scales: {
                    y: {
                        beginAtZero: true,
                        ticks: {
                            precision: 0,
                            font: {
                                weight: '500'
                            },
                            padding: 10
                        },
                        grid: {
                            drawBorder: false,
                            color: 'rgba(0, 0, 0, 0.03)'
                        }
                    },
                    x: {
                        ticks: {
                            font: {
                                weight: '500'
                            },
                            padding: 10,
                            autoSkip: false,
                            maxRotation: 45,
                            minRotation: 45
                        },
                        grid: {
                            display: false,
                            drawBorder: false
                        }
                    }
                },
                animation: {
                    duration: 1000
                }
            }
        });
    }
    
    // เพิ่มเอฟเฟกต์ animation สำหรับค่าสถิติ
    function setupStatCards() {
        // เพิ่มเอฟเฟกต์ hover ให้กับการ์ดสถิติ
        const statsCards = document.querySelectorAll('.stats-card');
        statsCards.forEach(card => {
            card.addEventListener('mouseenter', function() {
                const icon = this.querySelector('.stats-icon');
                if (icon) {
                    icon.style.transform = 'scale(1.15) translateY(-5px)';
                    icon.style.transition = 'all 0.3s ease';
                }
                
                const value = this.querySelector('.stats-value');
                if (value) {
                    value.style.transform = 'scale(1.05)';
                    value.style.transition = 'all 0.3s ease';
                }
            });
            
            card.addEventListener('mouseleave', function() {
                const icon = this.querySelector('.stats-icon');
                if (icon) {
                    icon.style.transform = 'scale(1) translateY(0)';
                }
                
                const value = this.querySelector('.stats-value');
                if (value) {
                    value.style.transform = 'scale(1)';
                }
            });
        });
        
        // เพิ่ม animation เมื่อโหลดเพจ
        const animateStats = function() {
            const statsValues = document.querySelectorAll('.stats-value');
            statsValues.forEach((element, index) => {
                setTimeout(() => {
                    element.style.opacity = '1';
                    element.style.transform = 'translateY(0)';
                }, index * 100);
            });
        };
        
        // Set initial state for animation
        const statsValues = document.querySelectorAll('.stats-value');
        statsValues.forEach(element => {
            element.style.opacity = '0';
            element.style.transform = 'translateY(15px)';
            element.style.transition = 'all 0.5s ease-out';
        });
        
        // Trigger animation
        setTimeout(animateStats, 300);
    }
    
    // สร้างกราฟและตั้งค่าเอฟเฟกต์
    if (document.getElementById('activityTypeChart')) {
        createActivityTypeChart();
    }
    
    if (document.getElementById('userLoginChart')) {
        createUserLoginChart();
    }
    
    setupStatCards();
    
    // ปุ่มส่งออก Excel
    const exportBtn = document.getElementById('exportBtn');
    if (exportBtn) {
        exportBtn.addEventListener('click', function(e) {
            e.preventDefault();
            var currentUrl = window.location.search;
            window.location.href = baseUrl + 'User_log_backend/export_csv' + currentUrl;
        });
    }
});

/**
 * member_log_daily_chart.js - สำหรับแสดงกราฟเส้นการเข้าสู่ระบบรายวัน
 */

// ฟังก์ชันสำหรับแปลงเดือนเป็นภาษาไทย
function getThaiMonth(month) {
    const thaiMonths = [
        'มกราคม', 'กุมภาพันธ์', 'มีนาคม', 'เมษายน', 'พฤษภาคม', 'มิถุนายน',
        'กรกฎาคม', 'สิงหาคม', 'กันยายน', 'ตุลาคม', 'พฤศจิกายน', 'ธันวาคม'
    ];
    return thaiMonths[month];
}

// ฟังก์ชันสำหรับแปลงวันที่เป็นรูปแบบไทย
function formatThaiDate(date) {
    const day = date.getDate();
    const month = getThaiMonth(date.getMonth());
    const year = date.getFullYear() + 543; // แปลงเป็นปี พ.ศ.
    return `${day} ${month} ${year}`;
}

// กำหนด chartColors เป็น global variable เพื่อให้แน่ใจว่าสามารถเข้าถึงได้ทุกที่
window.chartColors = {
    primary: '#6C63FF',
    primaryLight: 'rgba(108, 99, 255, 0.2)',
    success: '#36B37E',
    successLight: 'rgba(54, 179, 126, 0.2)',
    danger: '#FF5C5C',
    dangerLight: 'rgba(255, 92, 92, 0.2)',
    info: '#5E6A84',
    infoLight: 'rgba(94, 106, 132, 0.2)',
    warning: '#FFAB2B',
    warningLight: 'rgba(255, 171, 43, 0.2)',
    gray: ['#F9FAFC', '#EBEEF5', '#DFE3E8', '#C4CDD5', '#919EAB', '#637381', '#454F5B', '#212B36']
};

// สร้างกราฟเส้นแสดงการเข้าสู่ระบบรายวัน
window.dailyLoginChart = null;

function createDailyLoginChart(month, year) {
    console.log(`Creating chart for month: ${month}, year: ${year}`);
    
    // สร้างข้อมูลวันที่ในเดือนที่เลือก
    const daysInMonth = new Date(year, month, 0).getDate();
    const labels = [];
    
    for (let i = 1; i <= daysInMonth; i++) {
        labels.push(i); // แสดงเฉพาะวันที่ (1-31)
    }
    
    // ใช้ URL แบบเต็มแทนการใช้ baseUrl เพื่อหลีกเลี่ยงปัญหา
    const apiUrl = `${window.baseUrl}User_log_backend/get_daily_login_data?month=${month}&year=${year}`;
    console.log(`Fetching data from: ${apiUrl}`);
    
    fetch(apiUrl)
        .then(response => {
            if (!response.ok) {
                throw new Error(`HTTP error! Status: ${response.status}`);
            }
            console.log('Response received');
            return response.json();
        })
        .then(data => {
            console.log('Data parsed successfully:', data);
            
            // สร้างข้อมูลสำหรับแสดงในกราฟ
            const successData = Array(daysInMonth).fill(0);
            const failedData = Array(daysInMonth).fill(0);
            
            // นำข้อมูลจาก API มาใส่ในอาร์เรย์ - เพิ่มการตรวจสอบข้อมูล
            if (data.success && Array.isArray(data.success)) {
                data.success.forEach(item => {
                    const day = parseInt(item.day) - 1; // ปรับเป็น index ที่เริ่มจาก 0
                    if (day >= 0 && day < daysInMonth) {
                        successData[day] = parseInt(item.count);
                    }
                });
            }
            
            if (data.failed && Array.isArray(data.failed)) {
                data.failed.forEach(item => {
                    const day = parseInt(item.day) - 1; // ปรับเป็น index ที่เริ่มจาก 0
                    if (day >= 0 && day < daysInMonth) {
                        failedData[day] = parseInt(item.count);
                    }
                });
            }
            
            // ถ้ามีกราฟอยู่แล้ว ให้ทำลายก่อนสร้างใหม่
            if (window.dailyLoginChart instanceof Chart) {
                console.log('Destroying previous chart');
                window.dailyLoginChart.destroy();
            }
            
            // สร้างกราฟใหม่
            const canvas = document.getElementById('dailyLoginChart');
            if (!canvas) {
                console.error('Canvas element not found!');
                return;
            }
            
            const ctx = canvas.getContext('2d');
            console.log('Creating new chart');
            
            window.dailyLoginChart = new Chart(ctx, {
                type: 'line',
                data: {
                    labels: labels,
                    datasets: [
                        {
                            label: 'เข้าสู่ระบบสำเร็จ',
                            data: successData,
                            borderColor: window.chartColors.success,
                            backgroundColor: window.chartColors.successLight,
                            borderWidth: 2,
                            pointRadius: 4,
                            pointBackgroundColor: window.chartColors.success,
                            pointBorderColor: '#fff',
                            pointBorderWidth: 2,
                            tension: 0.3,
                            fill: true
                        },
                        {
                            label: 'เข้าสู่ระบบล้มเหลว',
                            data: failedData,
                            borderColor: window.chartColors.danger,
                            backgroundColor: window.chartColors.dangerLight,
                            borderWidth: 2,
                            pointRadius: 4,
                            pointBackgroundColor: window.chartColors.danger,
                            pointBorderColor: '#fff',
                            pointBorderWidth: 2,
                            tension: 0.3,
                            fill: true
                        }
                    ]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    plugins: {
                        legend: {
                            position: 'top',
                            labels: {
                                font: {
                                    size: 12,
                                    weight: '500'
                                },
                                padding: 15
                            }
                        },
                        tooltip: {
                            mode: 'index',
                            intersect: false,
                            padding: 10,
                            titleFont: {
                                size: 14,
                                weight: 'bold'
                            },
                            bodyFont: {
                                size: 13
                            },
                            callbacks: {
                                title: function(context) {
                                    // แสดงวันที่เป็นรูปแบบไทย
                                    const selectedMonth = parseInt(month);
                                    const selectedYear = parseInt(year);
                                    const day = parseInt(context[0].label);
                                    const date = new Date(selectedYear, selectedMonth-1, day);
                                    return formatThaiDate(date);
                                },
                                label: function(context) {
                                    // แสดงจำนวนครั้งการเข้าสู่ระบบ
                                    let label = context.dataset.label || '';
                                    return `${label}: ${context.raw} ครั้ง`;
                                }
                            }
                        }
                    },
                    scales: {
                        x: {
                            grid: {
                                display: false
                            },
                            title: {
                                display: true,
                                text: `${getThaiMonth(month-1)} ${year+543}`,
                                font: {
                                    size: 14,
                                    weight: '500'
                                }
                            }
                        },
                        y: {
                            beginAtZero: true,
                            // กำหนดค่า suggestedMax แทนการใช้ฟังก์ชัน max
                            suggestedMax: function() {
                                // หาค่าสูงสุดจากทั้งสอง dataset
                                let maxSuccess = Math.max(...successData);
                                let maxFailed = Math.max(...failedData);
                                let maxValue = Math.max(maxSuccess, maxFailed, 5); // ขั้นต่ำคือ 5
                                
                                // ปัดค่าเป็นจำนวนเต็มที่เหมาะสม
                                if (maxValue <= 10) {
                                    return 10; // ถ้าน้อยกว่า 10 ให้แสดงสูงสุดที่ 10
                                } else if (maxValue <= 20) {
                                    return 20; // ถ้าน้อยกว่า 20 ให้แสดงสูงสุดที่ 20
                                } else if (maxValue <= 50) {
                                    return 50; // ถ้าน้อยกว่า 50 ให้แสดงสูงสุดที่ 50
                                } else if (maxValue <= 100) {
                                    return 100; // ถ้าน้อยกว่า 100 ให้แสดงสูงสุดที่ 100
                                } else {
                                    return Math.ceil(maxValue / 100) * 100; // ปัดขึ้นเป็นหลักร้อยที่ใกล้ที่สุด
                                }
                            }(),
                            ticks: {
                                precision: 0,
                                font: {
                                    weight: '500'
                                },
                                padding: 10,
                                // กำหนดระยะห่างระหว่างขีดบนแกน Y
                                stepSize: function() {
                                    let maxSuccess = Math.max(...successData);
                                    let maxFailed = Math.max(...failedData);
                                    let maxValue = Math.max(maxSuccess, maxFailed, 5);
                                    let suggestedMax;
                                    
                                    // ใช้ logic เดียวกันกับ suggestedMax
                                    if (maxValue <= 10) {
                                        suggestedMax = 10;
                                        return 2; // แบ่งเป็น 5 ขีด (0, 2, 4, 6, 8, 10)
                                    } else if (maxValue <= 20) {
                                        suggestedMax = 20;
                                        return 4; // แบ่งเป็น 5 ขีด (0, 4, 8, 12, 16, 20)
                                    } else if (maxValue <= 50) {
                                        suggestedMax = 50;
                                        return 10; // แบ่งเป็น 5 ขีด (0, 10, 20, 30, 40, 50)
                                    } else if (maxValue <= 100) {
                                        suggestedMax = 100;
                                        return 20; // แบ่งเป็น 5 ขีด (0, 20, 40, 60, 80, 100)
                                    } else {
                                        suggestedMax = Math.ceil(maxValue / 100) * 100;
                                        return suggestedMax / 5; // แบ่งเป็น 5 ขีด
                                    }
                                }()
                            },
                            // ปิดการปรับแต่งอัตโนมัติ
                            grace: '0%',
                            grid: {
                                color: 'rgba(0, 0, 0, 0.03)'
                            }
                        }
                    },
                    // เพิ่มการกำหนดค่าที่สำคัญอื่นๆ
                    elements: {
                        line: {
                            tension: 0.3 // ความโค้งของเส้น
                        },
                        point: {
                            radius: 4,
                            hoverRadius: 6,
                            borderWidth: 2
                        }
                    },
                    layout: {
                        padding: {
                            top: 10,
                            right: 10,
                            bottom: 10,
                            left: 10
                        }
                    },
                    animation: {
                        duration: 1000
                    }
                }
            });
            
            console.log('Chart created successfully');
        })
        .catch(error => {
            console.error('Error fetching login data:', error);
            
            // แสดงข้อความเมื่อไม่สามารถโหลดข้อมูลได้
            const canvas = document.getElementById('dailyLoginChart');
            if (!canvas) {
                console.error('Canvas element not found!');
                return;
            }
            
            const ctx = canvas.getContext('2d');
            
            // ล้าง canvas ก่อนวาดข้อความ
            ctx.clearRect(0, 0, canvas.width, canvas.height);
            
            ctx.font = '16px Prompt';
            ctx.textAlign = 'center';
            ctx.fillStyle = '#FF5C5C'; // สีแดง
            ctx.fillText('ไม่สามารถโหลดข้อมูลกราฟได้', canvas.width / 2, canvas.height / 2);
            ctx.fillStyle = '#637381'; // สีเทา
            ctx.font = '14px Prompt';
            ctx.fillText('โปรดลองโหลดหน้าใหม่อีกครั้ง', canvas.width / 2, canvas.height / 2 + 30);
        });
}

// ตั้งค่าตัวเลือกเดือนและปี
function setupMonthYearSelector() {
    console.log('Setting up month/year selectors');
    
    const monthSelector = document.getElementById('monthSelector');
    const yearSelector = document.getElementById('yearSelector');
    const todayBtn = document.getElementById('todayBtn');
    
    if (!monthSelector || !yearSelector || !todayBtn) {
        console.error('Month/year selectors or today button not found!');
        return;
    }
    
    // ตั้งค่าเดือนและปีปัจจุบัน
    const currentDate = new Date();
    const currentMonth = currentDate.getMonth() + 1; // เดือนใน JavaScript เริ่มจาก 0
    const currentYear = currentDate.getFullYear();
    
    console.log(`Current month: ${currentMonth}, year: ${currentYear}`);
    
    // ตั้งค่าค่าเริ่มต้นสำหรับตัวเลือก
    monthSelector.value = currentMonth;
    yearSelector.value = currentYear;
    
    // สร้างกราฟเริ่มต้น
    createDailyLoginChart(currentMonth, currentYear);
    
    // เพิ่ม event listener สำหรับการเปลี่ยนเดือนหรือปี
    monthSelector.addEventListener('change', updateChart);
    yearSelector.addEventListener('change', updateChart);
    
    // ปุ่มกลับไปยังวันนี้
    todayBtn.addEventListener('click', function() {
        monthSelector.value = currentMonth;
        yearSelector.value = currentYear;
        createDailyLoginChart(currentMonth, currentYear);
    });
    
    // ฟังก์ชันอัปเดตกราฟเมื่อเปลี่ยนเดือนหรือปี
    function updateChart() {
        const selectedMonth = parseInt(monthSelector.value);
        const selectedYear = parseInt(yearSelector.value);
        console.log(`Updating chart to month: ${selectedMonth}, year: ${selectedYear}`);
        createDailyLoginChart(selectedMonth, selectedYear);
    }
    
    console.log('Month/year selectors setup complete');
}

// เริ่มต้นเมื่อโหลดหน้า
document.addEventListener('DOMContentLoaded', function() {
    console.log('DOM content loaded');
    
    // ตรวจสอบว่า Canvas element มีอยู่จริงหรือไม่
    const canvas = document.getElementById('dailyLoginChart');
    if (!canvas) {
        console.error('Canvas element "dailyLoginChart" not found!');
        return;
    }
    
    // ตั้งค่าตัวเลือกเดือนและปี
    setupMonthYearSelector();
});
</script>