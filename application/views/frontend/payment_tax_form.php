<div class="text-center pages-head">
    <span class="font-pages-head">ชำระภาษี</span>
</div>
</div>
<img src="<?php echo base_url('docs/welcome-btm-light-other.png'); ?>">

<div class="bg-pages ">
    <div class="container-pages-news" style="position: relative; z-index: 10;">
        <!-- <div class="d-flex justify-content-end">
            <div class="form-group">
                <div class="col-sm-12">
                    <select class="form-select custom-select" id="ChangPagesComplain">
                        <option value="" disabled selected>ยื่นเอกสารออนไลน์</option>
                        <option value="suggestions">รับฟังความคิดเห็น</option>
                        <option value="complain">ร้องเรียน/ร้องทุกข์</option>
                        <option value="follow-complain">ติดตามสถานะเรื่องร้องเรียน</option>
                        <option value="corruption">แจ้งเรื่องทุจริตหน่วยงานภาครัฐ</option>
                    </select>
                </div>
            </div>
        </div> -->
        <div class="underline">

            <!-- ปุ่มตรวจสอบสถานะ -->
            <div class="d-flex justify-content-end mb-4">
                <button type="button" onclick="openStatusModal()" class="status-check-btn">
                    <i class="fas fa-search-dollar"></i> ตรวจสอบสถานะการชำระภาษี
                </button>
            </div>

            <!-- Modal สำหรับตรวจสอบสถานะ -->
            <div class="modal fade" id="statusCheckModal" tabindex="-1" style="display: none;" aria-hidden="true">
                <div class="modal-dialog modal-dialog-centered"> <!-- ลบ modal-lg เพราะเรากำหนดขนาดใน CSS แล้ว -->
                    <div class="modal-content">
                        <!-- Header -->
                        <div class="status-header">
                            <div class="header-content">
                                <div class="header-icon">
                                    <i class="fas fa-search-dollar"></i>
                                </div>
                                <h4>ตรวจสอบสถานะการชำระภาษี</h4>
                            </div>
                            <button class="close-button" data-bs-dismiss="modal">
                                <i class="fas fa-times"></i>
                            </button>
                        </div>

                        <!-- Body -->
                        <div class="status-body">
                            <!-- Search Box -->
                            <div class="search-box">
                                <div class="search-input-group">
                                    <i class="fas fa-id-card input-icon"></i>
                                    <input type="text"
                                        id="citizenSearch"
                                        placeholder="กรอกเลขประจำตัวประชาชน 13 หลัก"
                                        maxlength="13">
                                    <button onclick="searchTaxStatus()" class="search-btn">
                                        <i class="fas fa-search"></i> ค้นหา
                                    </button>
                                </div>
                            </div>

                            <!-- ส่วนแสดงข้อมูลส่วนตัว -->
                            <div id="userInfoContainer" style="display: none;" class="mb-4"></div>

                            <!-- Advanced Search Box -->
                            <div class="search-actions d-flex align-items-center gap-2 mb-3">
                                <button class="btn btn-outline-secondary btn-sm" onclick="toggleAdvancedSearch()">
                                    <i class="fas fa-filter"></i> ค้นหาแบบละเอียด
                                </button>
                                <button class="btn btn-outline-danger btn-sm" onclick="resetFilter()" style="display: none;" id="resetFilterBtn">
                                    <i class="fas fa-times"></i> ล้างการค้นหา
                                </button>
                            </div>

                            <br>

                            <div class="advanced-search mb-4" id="advancedSearch" style="display: none;">
                                <div class="card">
                                    <div class="card-body">
                                        <div class="row g-3">
                                            <div class="col-md-3">
                                                <div class="form-group">
                                                    <label>วันที่ชำระ</label>
                                                    <input type="date" class="form-control" id="filterDate">
                                                </div>
                                            </div>
                                            <div class="col-md-3">
                                                <div class="form-group">
                                                    <label>จำนวนเงิน</label>
                                                    <input type="number" class="form-control" id="filterAmount" placeholder="ระบุจำนวนเงิน">
                                                </div>
                                            </div>
                                            <div class="col-md-3">
                                                <div class="form-group">
                                                    <label>สถานะ</label>
                                                    <select class="form-control" id="filterStatus">
                                                        <option value="">ทั้งหมด</option>
                                                        <option value="pending">รอตรวจสอบ</option>
                                                        <option value="verified">ตรวจสอบแล้ว</option>
                                                        <option value="rejected">ไม่อนุมัติ</option>
                                                    </select>
                                                </div>
                                            </div>
                                            <div class="col-md-3">
                                                <div class="form-group">
                                                    <label>ปีภาษี</label>
                                                    <select class="form-control" id="filterYear">
                                                        <option value="">ทั้งหมด</option>
                                                    </select>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- Results Table -->
                            <div id="searchResults" class="result-table-container" style="display: none;">
                                <div class="table-responsive"> <!-- เพิ่ม div ครอบตาราง -->
                                    <table class="result-table">
                                        <thead>
                                            <tr>
                                                <th>วันที่ชำระ</th>
                                                <th>จำนวนเงิน</th>
                                                <th>ประเภทภาษี</th>
                                                <th>ปีภาษี</th>
                                                <th>ไฟล์สลิป</th>
                                                <th>สถานะการชำระ</th>
                                                <th>หมายเหตุจากเจ้าหน้าที่</th>
                                                <th>วันที่ตรวจสอบ</th>
                                            </tr>
                                        </thead>
                                        <tbody id="resultTableBody"></tbody>
                                    </table>
                                </div>
                                <!-- เพิ่ม Pagination -->
                                <div id="paginationContainer" class="mt-4"></div>
                            </div>

                            <!-- No Results -->
                            <div id="noResults" class="no-data" style="display: none;">
                                <div class="no-data-content">
                                    <div class="no-data-icon">
                                        <i class="fas fa-search"></i>
                                    </div>
                                    <h5>ไม่พบข้อมูลการชำระภาษี</h5>
                                    <p>กรุณาตรวจสอบเลขประจำตัวประชาชนอีกครั้ง</p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- เพิ่ม JavaScript -->
            <script>
                // เพิ่มฟังก์ชันใหม่
                function toggleAdvancedSearch() {
                    $('#advancedSearch').slideToggle();
                }

                function resetFilter() {
                    $('#filterDate, #filterAmount').val('');
                    $('#filterStatus, #filterYear').val('');
                    $('#resetFilterBtn').hide();
                    if (allData.length > 0) {
                        currentPage = 1;
                        renderTable();
                    }
                }

                // แยกฟังก์ชันการ init modal ออกมา
                function initModal() {
                    $('#statusCheckModal').on('show.bs.modal', function() {
                        clearModalData(); // ล้างข้อมูลเมื่อเริ่มเปิด modal
                        $('body').addClass('modal-open');
                    });

                    $('#statusCheckModal').on('shown.bs.modal', function() {
                        $('#citizenSearch').focus();
                    });

                    $('#statusCheckModal').on('hide.bs.modal', function() {
                        clearModalData(); // ล้างข้อมูลเมื่อเริ่มปิด modal
                    });

                    $('#statusCheckModal').on('hidden.bs.modal', function() {
                        $('body').removeClass('modal-open');
                        // ล้างข้อมูลอีกครั้งหลังปิด modal เพื่อความแน่ใจ
                        clearModalData();
                    });
                }

                // แยกฟังก์ชันการล้างข้อมูลออกมา
                function clearModalData() {
                    // ล้างค่าการค้นหา
                    $('#citizenSearch').val('');

                    // ล้าง filter ทั้งหมด
                    $('#filterDate').val('');
                    $('#filterAmount').val('');
                    $('#filterStatus').val('');
                    $('#filterYear').val('');

                    // ซ่อน advanced search และปุ่ม reset
                    $('#advancedSearch').hide();
                    $('#resetFilterBtn').hide();

                    // ล้างการแสดงผล
                    $('#searchResults').hide();
                    $('#noResults').hide();
                    $('#resultTableBody').empty();
                    $('#paginationContainer').empty().hide();
                    $('#userInfoContainer').hide().empty();

                    // รีเซ็ตตัวแปร
                    allData = [];
                    currentPage = 1;
                }



                function initYearOptions() {
                    const currentYear = new Date().getFullYear();
                    const yearSelect = $('#filterYear');
                    for (let i = 0; i < 10; i++) {
                        const year = currentYear - i + 543;
                        yearSelect.append($('<option>', {
                            value: currentYear - i,
                            text: year
                        }));
                    }
                }

                function applyFilters(data) {
                    const filterDate = $('#filterDate').val();
                    const filterAmount = $('#filterAmount').val();
                    const filterStatus = $('#filterStatus').val();
                    const filterYear = $('#filterYear').val();

                    // Show/Hide reset button
                    if (filterDate || filterAmount || filterStatus || filterYear) {
                        $('#resetFilterBtn').show();
                    } else {
                        $('#resetFilterBtn').hide();
                        return data; // Return original data if no filters
                    }

                    return data.filter(item => {
                        let passFilter = true;

                        if (filterDate) {
                            const itemDate = new Date(item.created_at).toISOString().split('T')[0];
                            passFilter = passFilter && itemDate === filterDate;
                        }

                        if (filterAmount) {
                            passFilter = passFilter && Math.abs(parseFloat(item.amount) - parseFloat(filterAmount)) < 0.01;
                        }

                        if (filterStatus) {
                            passFilter = passFilter && item.payment_status === filterStatus;
                        }

                        if (filterYear) {
                            passFilter = passFilter && parseInt(item.tax_year) === parseInt(filterYear);
                        }

                        return passFilter;
                    });
                }

                let currentPage = 1;
                const itemsPerPage = 10;
                let allData = [];

                // Event Handlers
                $(document).ready(function() {
                    if (!modalInitialized) {
                        initModal();
                        modalInitialized = true;
                    }

                    initYearOptions();

                    // Add filter change events
                    $('#filterDate, #filterAmount, #filterStatus, #filterYear').on('change', function() {
                        currentPage = 1; // Reset to first page when filter changes
                        if (allData.length > 0) {
                            renderTable();
                        }
                    });

                    $('#statusCheckModal').modal({
                        show: false
                    });

                    $('#statusCheckModal').on('hidden.bs.modal', function() {
                        $('#citizenSearch').val('');
                        $('#searchResults').hide();
                        $('#noResults').hide();
                        $('#resultTableBody').empty();
                        $('#paginationContainer').empty().hide();
                    });

                    $('#citizenSearch').on('input', function() {
                        $(this).val(formatCitizenId($(this).val()));
                    });

                    $('#citizenSearch').on('keypress', function(e) {
                        if (e.which === 13) {
                            searchTaxStatus();
                        }
                    });

                    // Modal scroll lock
                    $('#statusCheckModal').on('show.bs.modal', function() {
                        $('body').addClass('modal-open');
                    }).on('hidden.bs.modal', function() {
                        $('body').removeClass('modal-open');
                    });

                    // Auto focus input when modal opens
                    $('#statusCheckModal').on('shown.bs.modal', function() {
                        $('#citizenSearch').focus();
                    });

                    // Button hover effects
                    $('.search-btn').hover(
                        function() {
                            $(this).find('i').addClass('fa-beat-fade');
                        },
                        function() {
                            $(this).find('i').removeClass('fa-beat-fade');
                        }
                    );

                    // Prevent modal close when clicking inside
                    $('.modal-content').click(function(e) {
                        e.stopPropagation();
                    });

                    // Table responsiveness
                    $(window).on('resize', function() {
                        if ($(window).width() < 992) {
                            $('.result-table-container').addClass('table-responsive');
                        } else {
                            $('.result-table-container').removeClass('table-responsive');
                        }
                    }).trigger('resize');
                });


                function openStatusModal() {
                    clearModalData();
                    $('#statusCheckModal').modal('show');
                }

                function updateYearFilterOptions(data) {
                    const yearSelect = $('#filterYear');
                    yearSelect.empty(); // ล้าง options เดิม

                    // เพิ่ม option ทั้งหมด
                    yearSelect.append($('<option>', {
                        value: '',
                        text: 'ทั้งหมด'
                    }));

                    // สร้างปีย้อนหลัง 10 ปี
                    const currentYear = new Date().getFullYear() + 543; // แปลงเป็น พ.ศ.
                    for (let i = 0; i < 10; i++) {
                        const year = currentYear - i;
                        yearSelect.append($('<option>', {
                            value: year,
                            text: year
                        }));
                    }

                    // ถ้ามีข้อมูล ให้เพิ่มปีที่มีในข้อมูลด้วย (กรณีมีปีเก่ากว่า 10 ปี)
                    if (data && data.length > 0) {
                        const dataYears = [...new Set(data.map(item => parseInt(item.tax_year)))];
                        dataYears.forEach(year => {
                            // ตรวจสอบว่ามี option ของปีนี้แล้วหรือยัง
                            if (!yearSelect.find(`option[value="${year}"]`).length) {
                                yearSelect.append($('<option>', {
                                    value: year,
                                    text: year
                                }));
                            }
                        });
                    }

                    // เรียงลำดับปีจากมากไปน้อย
                    const options = yearSelect.find('option:not(:first)').get(); // ยกเว้น option แรก (ทั้งหมด)
                    options.sort((a, b) => b.value - a.value);
                    yearSelect.append(options);
                }

                function displayResults(data) {
                    if (data.length > 0) {
                        allData = data;
                        currentPage = 1;

                        // เก็บค่า filter ปัจจุบันไว้
                        const currentYearFilter = $('#filterYear').val();

                        // อัพเดทตัวเลือกปีภาษี
                        updateYearFilterOptions(data);

                        // ถ้ามีการ filter ปีอยู่ก่อนแล้ว ให้เซ็ตค่ากลับ
                        if (currentYearFilter) {
                            $('#filterYear').val(currentYearFilter);
                        }

                        // แสดงข้อมูลส่วนตัว
                        const userInfo = data[0];
                        const userInfoHtml = `
            <div class="user-info-section mb-4">
                <div class="user-info-card p-4 bg-white rounded-lg shadow-sm">
                    <div class="flex flex-col gap-2">
                        <div class="user-info-item">
                            <div class="flex items-center gap-2">
                                <label class="text-gray-600 min-w-[140px]">เลขประจำตัวประชาชน:</label>
                                <span class="text-gray-800 font-medium">${formatCitizenId(userInfo.citizen_id)}</span>
                            </div>
                        </div>
                        <div class="user-info-item">
                            <div class="flex items-center gap-2">
                                <label class="text-gray-600 min-w-[140px]">ชื่อ-นามสกุล:</label>
                                <span class="text-gray-800 font-medium">${userInfo.firstname} ${userInfo.lastname}</span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        `;
                        $('#userInfoContainer').html(userInfoHtml).show();

                        renderTable();
                        $('#searchResults').show();
                        $('#noResults').hide();
                    } else {
                        $('#userInfoContainer').hide();
                        showNoResults();
                    }
                }

                function renderTable() {
                    const filteredData = applyFilters(allData);
                    const start = (currentPage - 1) * itemsPerPage;
                    const end = start + itemsPerPage;
                    const pageData = filteredData.slice(start, end);

                    const tbody = $('#resultTableBody');
                    tbody.empty();

                    pageData.forEach(item => {
                        let statusBadge = getStatusBadge(item.payment_status);
                        let taxType = getTaxType(item.tax_type);

                        tbody.append(`
            <tr>
                <td>${formatDate(item.created_at)}</td>
                <td class="text-end">${formatMoney(item.amount)} บาท</td>
                <td>${taxType}</td>
                <td>${parseInt(item.tax_year) + 0}</td>
                <td class="text-center">
                    <a href="<?= base_url('docs/img/') ?>${item.slip_file}" 
                       target="_blank"
                       class="doc-link">
                        <i class="fas fa-file-image"></i> ดูสลิป
                    </a>
                </td>
                <td class="text-center">${statusBadge}</td>
                <td>${item.admin_comment || '-'}</td>
                <td>${item.verification_date ? formatDate(item.verification_date) : '-'}</td>
            </tr>
        `);
                    });

                    // แสดง pagination ถ้ามีข้อมูลมากกว่า 10 รายการ
                    const totalPages = Math.ceil(filteredData.length / itemsPerPage);
                    if (totalPages > 1) {
                        const paginationHtml = `
            <div class="pagination-container">
                <button class="pagination-btn" onclick="changePage(${currentPage - 1})" ${currentPage === 1 ? 'disabled' : ''}>
                    <i class="fas fa-angle-left"></i> หน้าก่อนหน้า
                </button>
                <span class="mx-3">หน้า ${currentPage} จาก ${totalPages}</span>
                <button class="pagination-btn" onclick="changePage(${currentPage + 1})" ${currentPage === totalPages ? 'disabled' : ''}>
                    หน้าถัดไป <i class="fas fa-angle-right"></i>
                </button>
            </div>
        `;
                        $('#paginationContainer').html(paginationHtml).show();
                    } else {
                        $('#paginationContainer').empty().hide();
                    }
                }

                function changePage(newPage) {
                    // แก้ไขตรงนี้: ใช้ filteredData แทน allData
                    const filteredData = applyFilters(allData);
                    const totalPages = Math.ceil(filteredData.length / itemsPerPage);
                    if (newPage >= 1 && newPage <= totalPages) {
                        currentPage = newPage;
                        renderTable();
                    }
                }

                function searchTaxStatus() {
                    const citizenId = $('#citizenSearch').val().trim().replace(/-/g, '');

                    if (!citizenId || citizenId.length !== 13) {
                        Swal.fire({
                            title: 'แจ้งเตือน',
                            text: 'กรุณากรอกเลขประจำตัวประชาชนให้ครบ 13 หลัก',
                            icon: 'warning'
                        });
                        return;
                    }

                    Swal.fire({
                        title: 'กำลังค้นหา...',
                        allowOutsideClick: false,
                        showConfirmButton: false,
                        didOpen: () => {
                            Swal.showLoading();
                        }
                    });

                    $.ajax({
                        url: '<?= site_url("Pages/check_tax_status") ?>',
                        type: 'POST',
                        data: {
                            citizen_id: citizenId
                        },
                        success: function(response) {
                            Swal.close();
                            try {
                                response = typeof response === 'string' ? JSON.parse(response) : response;
                                if (response.status === 'success' && response.data.length > 0) {
                                    displayResults(response.data);
                                } else {
                                    showNoResults();
                                }
                            } catch (e) {
                                showError();
                            }
                        },
                        error: function() {
                            Swal.close();
                            showError();
                        }
                    });
                }

                // Utility Functions
                function formatCitizenId(value) {
                    const v = value.replace(/[^\d]/g, '').substr(0, 13);
                    const matches = v.match(/(\d{1})(\d{4})(\d{5})(\d{2})(\d{1})/);
                    if (matches) {
                        return `${matches[1]}-${matches[2]}-${matches[3]}-${matches[4]}-${matches[5]}`;
                    }
                    return value;
                }

                function getStatusBadge(status) {
                    const badges = {
                        'pending': `<span class="status-badge status-pending">
                     <i class="fas fa-clock"></i> รอตรวจสอบ
                   </span>`,
                        'verified': `<span class="status-badge status-verified">
                      <i class="fas fa-check-circle"></i> ตรวจสอบแล้ว
                    </span>`,
                        'rejected': `<span class="status-badge status-rejected">
                      <i class="fas fa-times-circle"></i> ไม่อนุมัติ
                    </span>`
                    };
                    return badges[status] || status;
                }

                function getTaxType(type) {
                    const types = {
                        'land': 'ภาษีที่ดินและสิ่งปลูกสร้าง',
                        'signboard': 'ภาษีป้าย',
                        'local': 'ภาษีท้องถิ่น'
                    };
                    return types[type] || type;
                }

                function formatMoney(amount) {
                    return parseFloat(amount).toLocaleString('th-TH', {
                        minimumFractionDigits: 2,
                        maximumFractionDigits: 2
                    });
                }

                function formatDate(dateStr) {
                    const date = new Date(dateStr);
                    const options = {
                        year: 'numeric',
                        month: 'long',
                        day: 'numeric',
                        calendar: 'buddhist'
                    };
                    return date.toLocaleDateString('th-TH', options);
                }

                function showNoResults() {
                    $('#searchResults').hide();
                    $('#noResults').fadeIn(300);
                }

                function showError() {
                    Swal.fire({
                        title: 'เกิดข้อผิดพลาด!',
                        text: 'ไม่สามารถดึงข้อมูลได้ กรุณาลองใหม่อีกครั้ง',
                        icon: 'error',
                        confirmButtonText: 'ตกลง'
                    });
                }

                // Loading state handler
                function setLoadingState(loading) {
                    const btn = $('.search-btn');
                    if (loading) {
                        btn.prop('disabled', true)
                            .html('<i class="fas fa-spinner fa-spin"></i> กำลังค้นหา...');
                    } else {
                        btn.prop('disabled', false)
                            .html('<i class="fas fa-search"></i> ค้นหา');
                    }
                }
            </script>

            <!-- เพิ่ม CSS -->
            <style>
                .user-info-section {
                    transition: all 0.3s ease;
                }

                .user-info-card {
                    border: 1px solid #e5e7eb;
                }

                .user-info-item {
                    display: block;
                    gap: 4px;
                }

                .user-info-item label {
                    font-size: 16px;
                }

                /* ปรับขนาดคอลัมน์ตารางใหม่ */
                .result-table th:nth-child(1),
                .result-table td:nth-child(1) {
                    width: 120px;
                }

                /* วันที่ชำระ */

                .result-table th:nth-child(2),
                .result-table td:nth-child(2) {
                    width: 100px;
                }

                /* จำนวนเงิน */

                .result-table th:nth-child(3),
                .result-table td:nth-child(3) {
                    width: 180px;
                }

                /* ประเภทภาษี */

                .result-table th:nth-child(4),
                .result-table td:nth-child(4) {
                    width: 80px;
                }

                /* ปีภาษี */

                .result-table th:nth-child(5),
                .result-table td:nth-child(5) {
                    width: 100px;
                }

                /* ไฟล์สลิป */

                .result-table th:nth-child(6),
                .result-table td:nth-child(6) {
                    width: 130px;
                }

                /* สถานะ */

                .result-table th:nth-child(7),
                .result-table td:nth-child(7) {
                    width: 200px;
                }

                /* หมายเหตุ */

                .result-table th:nth-child(8),
                .result-table td:nth-child(8) {
                    width: 120px;
                }

                /* วันที่ตรวจสอบ */

                /* สไตล์สำหรับ pagination */
                .pagination-container {
                    margin-top: 1rem;
                    display: flex;
                    justify-content: flex-end;
                    padding: 1rem;
                }

                .pagination-btn {
                    padding: 0.5rem 1rem;
                    margin: 0 0.25rem;
                    border: 1px solid #e2e8f0;
                    border-radius: 0.375rem;
                    background: white;
                    color: #1a5f7a;
                    cursor: pointer;
                    transition: all 0.3s;
                }

                .pagination-btn:hover {
                    background: #f0f9ff;
                    border-color: #1a5f7a;
                }

                .pagination-btn.active {
                    background: #1a5f7a;
                    color: white;
                }

                /* Modal Styles */
                .modal-dialog {
                    max-width: 90% !important;
                    margin: 1.75rem auto;
                }

                /* ส่วนของตารางที่สามารถเลื่อนได้ */
                .status-body {
                    max-height: 60vh;
                    /* กำหนดความสูงสูงสุดเป็น 60% ของความสูงหน้าจอ */
                    overflow-y: auto;
                    /* ให้เลื่อนในแนวตั้งได้ */
                }

                .status-check-btn {
                    background: linear-gradient(135deg, #4CAF50, #45a049);
                    color: white;
                    padding: 12px 24px;
                    border: none;
                    border-radius: 8px;
                    font-weight: 500;
                    display: flex;
                    align-items: center;
                    gap: 8px;
                    transition: all 0.3s ease;
                    box-shadow: 0 4px 6px rgba(76, 175, 80, 0.2);
                }

                .status-check-btn:hover {
                    transform: translateY(-2px);
                    box-shadow: 0 6px 8px rgba(76, 175, 80, 0.3);
                }

                .status-header {
                    background: linear-gradient(120deg, #1a5f7a, #2c86aa);
                    padding: 25px 30px;
                    color: white;
                    position: relative;
                }

                .header-content {
                    display: flex;
                    align-items: center;
                    gap: 15px;
                }

                .header-icon {
                    background: rgba(255, 255, 255, 0.2);
                    width: 45px;
                    height: 45px;
                    border-radius: 12px;
                    display: flex;
                    align-items: center;
                    justify-content: center;
                    font-size: 20px;
                }

                .status-header h4 {
                    margin: 0;
                    font-size: 22px;
                    font-weight: 600;
                }

                .close-button {
                    position: absolute;
                    right: 20px;
                    top: 20px;
                    background: none;
                    border: none;
                    color: white;
                    font-size: 20px;
                    opacity: 0.8;
                    transition: 0.3s;
                    cursor: pointer;
                }

                .close-button:hover {
                    opacity: 1;
                }

                /* Search Box */
                .status-body {
                    padding: 30px;
                    background: #f8fafc;
                }

                .search-box {
                    margin-bottom: 30px;
                }

                .search-input-group {
                    background: white;
                    border-radius: 15px;
                    padding: 8px;
                    display: flex;
                    align-items: center;
                    gap: 15px;
                    box-shadow: 0 4px 12px rgba(0, 0, 0, 0.05);
                    border: 2px solid #e2e8f0;
                }

                .input-icon {
                    color: #64748b;
                    font-size: 20px;
                    margin-left: 15px;
                }

                #citizenSearch {
                    flex: 1;
                    border: none;
                    padding: 12px;
                    font-size: 16px;
                    outline: none;
                }

                .search-btn {
                    background: linear-gradient(120deg, #1a5f7a, #2c86aa);
                    color: white;
                    border: none;
                    padding: 12px 25px;
                    border-radius: 10px;
                    font-weight: 500;
                    display: flex;
                    align-items: center;
                    gap: 8px;
                    transition: 0.3s;
                    cursor: pointer;
                }

                .search-btn:hover {
                    transform: translateY(-2px);
                    box-shadow: 0 4px 12px rgba(44, 134, 170, 0.2);
                }

                /* Table Styles */
                .result-table-container {
                    overflow-x: auto;
                    /* ให้เลื่อนในแนวนอนได้ */
                }

                /* ปรับขนาดตาราง */
                .result-table {
                    min-width: 1100px;
                    /* กำหนดความกว้างขั้นต่ำของตาราง */
                }

                /* กำหนดความกว้างของแต่ละคอลัมน์ */
                .result-table th,
                .result-table td {
                    padding: 12px 16px;
                    white-space: nowrap;
                    /* ป้องกันข้อความขึ้นบรรทัดใหม่ */
                }

                .result-table th {
                    background: #f1f5f9;
                    padding: 15px 20px;
                    text-align: left;
                    font-weight: 600;
                    color: #334155;
                    font-size: 14px;
                    white-space: nowrap;
                }

                .result-table td {
                    padding: 15px 20px;
                    border-top: 1px solid #e2e8f0;
                    color: #475569;
                }

                .result-table tbody tr:hover {
                    background: #f8fafc;
                }

                /* Status Badges */
                .status-badge {
                    padding: 8px 16px;
                    border-radius: 30px;
                    font-size: 13px;
                    font-weight: 500;
                    display: inline-flex;
                    align-items: center;
                    gap: 8px;
                    box-shadow: 0 2px 4px rgba(0, 0, 0, 0.05);
                }

                .status-badge i {
                    font-size: 14px;
                }

                .status-pending {
                    background: #fff7ed;
                    color: #c2410c;
                }

                .status-verified {
                    background: #f0fdf4;
                    color: #166534;
                }

                .status-rejected {
                    background: #fef2f2;
                    color: #b91c1c;
                }

                /* No Results */
                .no-data {
                    padding: 48px 0;
                    text-align: center;
                }

                .no-data-content {
                    display: flex;
                    flex-direction: column;
                    align-items: center;
                    gap: 16px;
                }

                .no-data-icon {
                    width: 64px;
                    height: 64px;
                    background: #f1f5f9;
                    border-radius: 50%;
                    display: flex;
                    align-items: center;
                    justify-content: center;
                    font-size: 24px;
                    color: #64748b;
                }

                .no-data h5 {
                    margin: 0;
                    font-size: 18px;
                    color: #334155;
                }

                .no-data p {
                    margin: 0;
                    color: #64748b;
                }

                /* Document Link */
                .doc-link {
                    padding: 6px 12px;
                    border-radius: 8px;
                    color: #2c86aa;
                    text-decoration: none;
                    font-weight: 500;
                    transition: all 0.3s ease;
                    display: inline-flex;
                    align-items: center;
                    gap: 6px;
                }

                .doc-link:hover {
                    background: #f0f9ff;
                    color: #1a5f7a;
                }

                .doc-link i {
                    font-size: 16px;
                }

                /* Responsive */
                @media (max-width: 992px) {
                    .result-table-container {
                        overflow-x: auto;
                    }

                    .modal-dialog {
                        margin: 10px;
                    }
                }

                /* Animations */
                @keyframes slideIn {
                    from {
                        opacity: 0;
                        transform: translateY(-20px);
                    }

                    to {
                        opacity: 1;
                        transform: translateY(0);
                    }
                }

                .modal.show .modal-content {
                    animation: slideIn 0.3s ease-out;
                }

                .modal-content {
                    background: #ffffff;
                    color: #333333;
                    border-radius: 10px;
                    margin: 10% auto;
                    padding: 30px;
                    border: 1px solid #dddddd;
                    width: 100%;
                    max-width: 1350px;
                    box-shadow: 0 10px 30px rgba(0, 0, 0, 0.2);
                    position: relative;
                    animation: slideIn 0.5s, shake 0.5s ease-in-out 0.5s 1;
                    text-align: center;
                }
            </style>

            <?php echo form_open_multipart('Pages/payment_tax', ['class' => 'tax-payment-form']); ?>

            <div class="form-group">
                <label for="citizen_id">เลขประจำตัวประชาชน <span class="red-font">*</span></label>
                <input type="text" name="citizen_id" class="form-control" value="<?php echo set_value('citizen_id'); ?>">
                <?php echo form_error('citizen_id', '<div class="text-danger">', '</div>'); ?>
            </div>

            <div class="form-group">
                <label for="firstname">ชื่อ <span class="red-font">*</span></label>
                <input type="text" name="firstname" class="form-control" value="<?php echo set_value('firstname'); ?>">
                <?php echo form_error('firstname', '<div class="text-danger">', '</div>'); ?>
            </div>

            <div class="form-group">
                <label for="lastname">นามสกุล <span class="red-font">*</span></label>
                <input type="text" name="lastname" class="form-control" value="<?php echo set_value('lastname'); ?>">
                <?php echo form_error('lastname', '<div class="text-danger">', '</div>'); ?>
            </div>

            <div class="form-group">
                <label for="phone">เบอร์โทรศัพท์ <span class="red-font">*</span></label>
                <input type="tel" name="phone" class="form-control" value="<?php echo set_value('phone'); ?>">
                <?php echo form_error('phone', '<div class="text-danger">', '</div>'); ?>
            </div>

            <div class="form-group">
                <label for="email">อีเมล <span class="red-font">*</span></label>
                <input type="email" name="email" class="form-control" value="<?php echo set_value('email'); ?>">
                <?php echo form_error('email', '<div class="text-danger">', '</div>'); ?>
            </div>

            <div class="form-group">
                <label for="address">ที่อยู่ <span class="red-font">*</span></label>
                <textarea name="address" class="form-control"><?php echo set_value('address'); ?></textarea>
                <?php echo form_error('address', '<div class="text-danger">', '</div>'); ?>
            </div>

            <div class="form-group">
                <label for="tax_type">ประเภทภาษี <span class="red-font">*</span></label>
                <?php echo form_dropdown('tax_type', $tax_types, set_value('tax_type'), 'class="form-control"'); ?>
                <?php echo form_error('tax_type', '<div class="text-danger">', '</div>'); ?>
            </div>

            <div class="form-group">
                <label for="tax_year">ปีภาษี <span class="red-font">*</span></label>
                <input type="number" name="tax_year" class="form-control" value="<?php echo set_value('tax_year', date('Y') + 543); ?>">
                <?php echo form_error('tax_year', '<div class="text-danger">', '</div>'); ?>
            </div>

            <div class="form-group">
                <label for="amount">จำนวนเงิน (บาท) <span class="red-font">*</span></label>
                <input type="number" name="amount" class="form-control" step="0.01" value="<?php echo set_value('amount'); ?>">
                <?php echo form_error('amount', '<div class="text-danger">', '</div>'); ?>
            </div>

            <!-- <div class="form-group">
                <label for="payment_date">วันที่ชำระ <span class="red-font">*</span></label>
                <input type="date" name="payment_date" class="form-control" value="<?php echo set_value('payment_date', date('Y-m-d')); ?>">
                <?php echo form_error('payment_date', '<div class="text-danger">', '</div>'); ?>
            </div> -->
            <div class="payment-section">
                <?php if (isset($payment_settings) && $payment_settings): ?>
                    <div class="payment-info-container">
                        <div class="card payment-card">
                            <div class="card-header payment-header">
                                <h5 class="mb-0">
                                    <i class="fas fa-money-bill-wave me-2"></i>
                                    ข้อมูลการชำระเงิน
                                </h5>
                            </div>
                            <div class="card-body payment-body">
                                <div class="row align-items-center">
                                    <div class="col-md-6">
                                        <div class="bank-details">
                                            <h6 class="details-title">
                                                <i class="fas fa-university me-2"></i>
                                                รายละเอียดบัญชีธนาคาร
                                            </h6>
                                            <div class="detail-item">
                                                <span class="detail-label">ธนาคาร:</span>
                                                <span class="detail-value"><?php echo $payment_settings->bank_name; ?></span>
                                            </div>
                                            <div class="detail-item">
                                                <span class="detail-label">ชื่อบัญชี:</span>
                                                <span class="detail-value"><?php echo $payment_settings->account_name; ?></span>
                                            </div>
                                            <div class="detail-item">
                                                <span class="detail-label">เลขที่บัญชี:</span>
                                                <span class="detail-value"><?php echo $payment_settings->account_number; ?></span>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <?php if ($payment_settings->qr_code_image): ?>
                                            <div class="qr-code-section text-center">
                                                <h6 class="details-title">
                                                    <i class="fas fa-qrcode me-2"></i>
                                                    QR Code สำหรับชำระเงิน
                                                </h6>
                                                <div class="qr-code-container">
                                                    <img src="<?php echo base_url('docs/img/' . $payment_settings->qr_code_image); ?>"
                                                        alt="QR Code"
                                                        class="qr-code-image">
                                                </div>
                                            </div>
                                        <?php endif; ?>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                <?php endif; ?>
            </div>

            <div class="form-group">
                <label for="slip_file">แนบสลิปการโอนเงิน (ไฟล์ jpg, png ขนาดไม่เกิน 2MB) <span class="red-font">*</span></label>
                <input type="file" name="slip_file" class="form-control" accept="image/*">
                <?php if (isset($error)) echo $error; ?>
            </div>

            <div class="form-group">
                <button type="submit" class="btn btn-primary">ชำระภาษี</button>
            </div>

            <?php echo form_close(); ?>
        </div>
    </div>