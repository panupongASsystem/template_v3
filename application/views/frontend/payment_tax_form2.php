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

            <div class="container">
                <!-- หัวข้อ -->
                <div class="text-center pages-head mb-4">
                    <span class="font-pages-head">ตรวจสอบสถานะและชำระภาษี</span>
                </div>

                <!-- ปุ่มชำระภาษี -->
                <div class="d-flex justify-content-end mb-4">
                    <button type="button" onclick="openPaymentModal()" class="payment-btn">
                        <i class="fas fa-money-bill-wave"></i> ชำระภาษี
                    </button>
                </div>

                <!-- ส่วนแสดงสถานะ -->
                <div class="search-status-section">
                    <!-- ส่วนแสดงข้อมูลผู้ใช้ -->
                    <div id="userInfoContainer" class="mb-4">
                        <div class="user-info-card p-4">
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="user-info-item">
                                        <label>เลขประจำตัวประชาชน:</label>
                                        <span><?php
                                                $citizen_id = $this->session->userdata('mp_number');
                                                echo substr($citizen_id, 0, 1) . '-' .
                                                    substr($citizen_id, 1, 4) . '-' .
                                                    substr($citizen_id, 5, 5) . '-' .
                                                    substr($citizen_id, 10, 2) . '-' .
                                                    substr($citizen_id, 12, 1);
                                                ?></span>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="user-info-item">
                                        <label>ชื่อ-นามสกุล:</label>
                                        <span><?php echo $this->session->userdata('mp_fname') . ' ' . $this->session->userdata('mp_lname'); ?></span>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- ปุ่มกรองข้อมูล -->
                    <div class="search-actions d-flex align-items-center gap-2 mb-3">
                        <button class="btn btn-outline-secondary btn-sm" onclick="toggleAdvancedSearch()">
                            <i class="fas fa-filter"></i> ตัวกรองขั้นสูง
                        </button>
                        <button class="btn btn-outline-danger btn-sm" onclick="resetFilter()"
                            style="display: none;" id="resetFilterBtn">
                            <i class="fas fa-times"></i> ล้างตัวกรอง
                        </button>
                    </div>

                    <!-- ฟอร์มกรองขั้นสูง -->
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

                    <!-- ตารางแสดงผล -->
                    <div id="searchResults" class="result-table-container">
                        <div class="table-responsive">
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
                        <!-- Pagination -->
                        <div id="paginationContainer" class="mt-4"></div>
                    </div>

                    <!-- แสดงเมื่อไม่พบข้อมูล -->
                    <div id="noResults" class="no-data" style="display: none;">
                        <div class="no-data-content">
                            <div class="no-data-icon">
                                <i class="fas fa-search"></i>
                            </div>
                            <h5>ไม่พบข้อมูลการชำระภาษี</h5>
                            <p>ยังไม่มีประวัติการชำระภาษี</p>
                        </div>
                    </div>

                    <!-- Loading State -->
                    <div id="loadingState" class="loading-overlay" style="display: none;">
                        <div class="loading-spinner"></div>
                    </div>
                </div>
            </div>

            <!-- Modal ชำระภาษี -->
            <div class="modal fade" id="paymentModal" tabindex="-1" aria-hidden="true" style="display: none;">
                <div class="modal-dialog modal-lg modal-dialog-centered">
                    <div class="modal-content bg-white rounded-xl shadow-2xl">
                        <!-- Modal Header -->
                        <div class="modal-header border-b border-gray-100 p-6">
                            <div class="header-content">
                                <div class="header-icon">
                                    <i class="fas fa-money-bill-wave"></i>
                                </div>
                                <h4>ชำระภาษี</h4>
                            </div>
                            <button type="button" class="close-button" data-bs-dismiss="modal">
                                <i class="fas fa-times"></i>
                            </button>
                        </div>

                        <!-- Modal Body -->
                        <div class="modal-body p-6">
                            <?php echo form_open_multipart('Pages/payment_tax', ['class' => 'tax-payment-form']); ?>

                            <div class="form-section">
                                <h5 class="section-title">ข้อมูลผู้ชำระภาษี</h5>

                                <div class="form-group">
                                    <label for="citizen_id">เลขประจำตัวประชาชน <span class="red-font">*</span></label>
                                    <input type="text" name="citizen_id" class="form-control" value="<?php echo set_value('citizen_id'); ?>">
                                    <?php echo form_error('citizen_id', '<div class="text-danger">', '</div>'); ?>
                                </div>

                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label for="firstname">ชื่อ <span class="red-font">*</span></label>
                                            <input type="text" name="firstname" class="form-control" value="<?php echo set_value('firstname'); ?>">
                                            <?php echo form_error('firstname', '<div class="text-danger">', '</div>'); ?>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label for="lastname">นามสกุล <span class="red-font">*</span></label>
                                            <input type="text" name="lastname" class="form-control" value="<?php echo set_value('lastname'); ?>">
                                            <?php echo form_error('lastname', '<div class="text-danger">', '</div>'); ?>
                                        </div>
                                    </div>
                                </div>

                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label for="phone">เบอร์โทรศัพท์ <span class="red-font">*</span></label>
                                            <input type="tel" name="phone" class="form-control" value="<?php echo set_value('phone'); ?>">
                                            <?php echo form_error('phone', '<div class="text-danger">', '</div>'); ?>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label for="email">อีเมล <span class="red-font">*</span></label>
                                            <input type="email" name="email" class="form-control" value="<?php echo set_value('email'); ?>">
                                            <?php echo form_error('email', '<div class="text-danger">', '</div>'); ?>
                                        </div>
                                    </div>
                                </div>

                                <div class="form-group">
                                    <label for="address">ที่อยู่ <span class="red-font">*</span></label>
                                    <textarea name="address" class="form-control"><?php echo set_value('address'); ?></textarea>
                                    <?php echo form_error('address', '<div class="text-danger">', '</div>'); ?>
                                </div>
                            </div>

                            <div class="form-section">
                                <h5 class="section-title">ข้อมูลการชำระภาษี</h5>

                                <div class="row">
                                    <div class="col-md-4">
                                        <div class="form-group">
                                            <label for="tax_type">ประเภทภาษี <span class="red-font">*</span></label>
                                            <?php echo form_dropdown('tax_type', $tax_types, set_value('tax_type'), 'class="form-control"'); ?>
                                            <?php echo form_error('tax_type', '<div class="text-danger">', '</div>'); ?>
                                        </div>
                                    </div>
                                    <div class="col-md-4">
                                        <div class="form-group">
                                            <label for="tax_year">ปีภาษี <span class="red-font">*</span></label>
                                            <input type="number" name="tax_year" class="form-control" value="<?php echo set_value('tax_year', date('Y') + 543); ?>">
                                            <?php echo form_error('tax_year', '<div class="text-danger">', '</div>'); ?>
                                        </div>
                                    </div>
                                    <div class="col-md-4">
                                        <div class="form-group">
                                            <label for="amount">จำนวนเงิน (บาท) <span class="red-font">*</span></label>
                                            <input type="number" name="amount" class="form-control" step="0.01" value="<?php echo set_value('amount'); ?>">
                                            <?php echo form_error('amount', '<div class="text-danger">', '</div>'); ?>
                                        </div>
                                    </div>
                                </div>

                                <!-- ข้อมูลบัญชีธนาคาร -->
                                <?php if (isset($payment_settings) && $payment_settings): ?>
                                    <div class="bank-info-section mt-4">
                                        <div class="row">
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
                                                    <div class="qr-code-section">
                                                        <h6 class="details-title">
                                                            <i class="fas fa-qrcode me-2"></i>
                                                            QR Code สำหรับชำระเงิน
                                                        </h6>
                                                        <div class="qr-code-container">
                                                            <img src="<?php echo base_url('docs/img/' . $payment_settings->qr_code_image); ?>"
                                                                alt="QR Code" class="qr-code-image">
                                                        </div>
                                                    </div>
                                                <?php endif; ?>
                                            </div>
                                        </div>
                                    </div>
                                <?php endif; ?>

                                <!-- อัพโหลดสลิป -->
                                <div class="form-group mt-4">
                                    <label for="slip_file">แนบสลิปการโอนเงิน <span class="red-font">*</span></label>
                                    <div class="file-upload">
                                        <input type="file" name="slip_file" class="form-control" accept="image/*">
                                        <small class="text-muted">รองรับไฟล์ JPG, PNG ขนาดไม่เกิน 2MB</small>
                                        <?php if (isset($error)) echo $error; ?>
                                    </div>
                                </div>
                            </div>

                            <div class="form-actions mt-4">
                                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">ยกเลิก</button>
                                <button type="submit" class="btn btn-primary">
                                    <i class="fas fa-check mr-2"></i> ยืนยันการชำระภาษี
                                </button>
                            </div>

                            <?php echo form_close(); ?>
                        </div>
                    </div>
                </div>
            </div>

            <script>
                $(document).ready(function() {
                    // เริ่มดึงข้อมูลทันที
                    fetchUserTaxData();

                    // Event สำหรับ filter
                    $('#filterDate, #filterAmount, #filterStatus, #filterYear').on('change', function() {
                        if (allData.length > 0) {
                            currentPage = 1;
                            renderTable();
                        }
                    });
                });

                // ฟังก์ชันดึงข้อมูล
                function fetchUserTaxData() {
                    $.ajax({
                        url: '<?= site_url("Pages/check_tax_status") ?>',
                        type: 'POST',
                        data: {
                            citizen_id: '<?= $this->session->userdata("mp_number") ?>'
                        },
                        success: function(response) {
                            try {
                                response = typeof response === 'string' ? JSON.parse(response) : response;
                                if (response.status === 'success' && response.data.length > 0) {
                                    displayResults(response.data);
                                    updateYearFilterOptions(response.data);
                                } else {
                                    showNoResults();
                                }
                            } catch (e) {
                                showError();
                            }
                        },
                        error: function() {
                            showError();
                        }
                    });
                }

                // ตัวแปรสำหรับจัดการข้อมูล
                let currentPage = 1;
                const itemsPerPage = 10;
                let allData = [];
                let modalInitialized = false;

                // Event Handlers เมื่อโหลดหน้า
                $(document).ready(function() {
                    // ซ่อน Modal ตั้งแต่แรก
                    $('#paymentModal').modal({
                        show: false,
                        backdrop: 'static',
                        keyboard: false
                    });

                    if (!modalInitialized) {
                        initModal();
                        modalInitialized = true;
                    }

                    initYearOptions();

                    // Add filter change events
                    $('#filterDate, #filterAmount, #filterStatus, #filterYear').on('change', function() {
                        if (allData.length > 0) {
                            currentPage = 1;
                            renderTable();
                        }
                    });

                    // Event สำหรับ format เลขบัตรประชาชน
                    $('#citizenSearch').on('input', function() {
                        $(this).val(formatCitizenId($(this).val()));
                    });

                    // Event สำหรับกด Enter
                    $('#citizenSearch').on('keypress', function(e) {
                        if (e.which === 13) {
                            searchTaxStatus();
                        }
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
                });

                // ฟังก์ชันสำหรับ Modal
                function initModal() {
                    $('#paymentModal').on('show.bs.modal', function() {
                        clearModalData();
                        $('body').addClass('modal-open');
                    });

                    $('#paymentModal').on('shown.bs.modal', function() {
                        $('#payment-form input:first').focus();
                    });

                    $('#paymentModal').on('hidden.bs.modal', function() {
                        clearModalData();
                        $('body').removeClass('modal-open');
                    });
                }

                function clearModalData() {
                    // ล้างฟอร์มชำระภาษี
                    $('.tax-payment-form')[0].reset();
                    $('.text-danger').empty();
                }

                // ฟังก์ชันสำหรับการค้นหา
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

                    setLoadingState(true);

                    $.ajax({
                        url: '<?= site_url("Pages/check_tax_status") ?>',
                        type: 'POST',
                        data: {
                            citizen_id: citizenId
                        },
                        success: function(response) {
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
                            showError();
                        },
                        complete: function() {
                            setLoadingState(false);
                        }
                    });
                }

                // ฟังก์ชันแสดงผลข้อมูล
                function displayResults(data) {
                    allData = data;

                    // แสดงข้อมูลส่วนตัว
                    if (data.length > 0) {
                        const userInfo = data[0];
                        const userInfoHtml = `
            <div class="user-info-section">
                <div class="user-info-card p-4">
                    <div class="row">
                        <div class="col-md-6">
                            <div class="user-info-item">
                                <label>เลขประจำตัวประชาชน:</label>
                                <span>${formatCitizenId(userInfo.citizen_id)}</span>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="user-info-item">
                                <label>ชื่อ-นามสกุล:</label>
                                <span>${userInfo.firstname} ${userInfo.lastname}</span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>`;
                        $('#userInfoContainer').html(userInfoHtml).show();
                    }

                    // แสดงตารางและ pagination
                    currentPage = 1;
                    renderTable();
                    updateYearFilterOptions(data);
                }

                function renderTable() {
                    const filteredData = applyFilters(allData);
                    const start = (currentPage - 1) * itemsPerPage;
                    const end = start + itemsPerPage;
                    const pageData = filteredData.slice(start, end);

                    const tbody = $('#resultTableBody');
                    tbody.empty();

                    pageData.forEach(item => {
                        tbody.append(`
            <tr>
                <td>${formatDate(item.created_at)}</td>
                <td class="text-end">${formatMoney(item.amount)} บาท</td>
                <td>${getTaxType(item.tax_type)}</td>
                <td>${parseInt(item.tax_year) + 543}</td>
                <td class="text-center">
                    <a href="<?= base_url('docs/img/') ?>${item.slip_file}" 
                       target="_blank"
                       class="doc-link">
                        <i class="fas fa-file-image"></i> ดูสลิป
                    </a>
                </td>
                <td class="text-center">${getStatusBadge(item.payment_status)}</td>
                <td>${item.admin_comment || '-'}</td>
                <td>${item.verification_date ? formatDate(item.verification_date) : '-'}</td>
            </tr>
        `);
                    });

                    renderPagination(filteredData.length);
                    $('#searchResults').show();
                    $('#noResults').hide();
                }

                // ฟังก์ชันช่วยเหลือ
                function formatCitizenId(value) {
                    const v = value.replace(/[^\d]/g, '').substr(0, 13);
                    const matches = v.match(/(\d{1})(\d{4})(\d{5})(\d{2})(\d{1})/);
                    return matches ? `${matches[1]}-${matches[2]}-${matches[3]}-${matches[4]}-${matches[5]}` : value;
                }

                function formatDate(dateStr) {
                    const date = new Date(dateStr);
                    return date.toLocaleDateString('th-TH', {
                        year: 'numeric',
                        month: 'long',
                        day: 'numeric',
                        calendar: 'buddhist'
                    });
                }

                function formatMoney(amount) {
                    return parseFloat(amount).toLocaleString('th-TH', {
                        minimumFractionDigits: 2,
                        maximumFractionDigits: 2
                    });
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
                    return {
                        'land': 'ภาษีที่ดินและสิ่งปลูกสร้าง',
                        'signboard': 'ภาษีป้าย',
                        'local': 'ภาษีท้องถิ่น'
                    } [type] || type;
                }

                // ฟังก์ชันจัดการ Filter
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

                function applyFilters(data) {
                    const filterDate = $('#filterDate').val();
                    const filterAmount = $('#filterAmount').val();
                    const filterStatus = $('#filterStatus').val();
                    const filterYear = $('#filterYear').val();

                    $('#resetFilterBtn').toggle(!!(filterDate || filterAmount || filterStatus || filterYear));

                    return data.filter(item => {
                        let passFilter = true;

                        if (filterDate) {
                            passFilter &= new Date(item.created_at).toISOString().split('T')[0] === filterDate;
                        }
                        if (filterAmount) {
                            passFilter &= Math.abs(parseFloat(item.amount) - parseFloat(filterAmount)) < 0.01;
                        }
                        if (filterStatus) {
                            passFilter &= item.payment_status === filterStatus;
                        }
                        if (filterYear) {
                            passFilter &= parseInt(item.tax_year) === parseInt(filterYear);
                        }

                        return passFilter;
                    });
                }

                // ฟังก์ชันแสดงผลลัพธ์และข้อผิดพลาด
                function showNoResults() {
                    $('#searchResults').hide();
                    $('#noResults').fadeIn(300);
                }

                function showError() {
                    Swal.fire({
                        title: 'เกิดข้อผิดพลาด!',
                        text: 'ไม่สามารถดึงข้อมูลได้ กรุณาลองใหม่อีกครั้ง',
                        icon: 'error'
                    });
                }

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
            
                $(document).ready(function() {
        fetchUserTaxData();
    });
            </script>
            <style>
                .font-pages-head {
        font-size: 24px;
        font-weight: 600;
        color: #1a5f7a;
    }

    .loading-overlay {
        position: absolute;
        top: 0;
        left: 0;
        right: 0;
        bottom: 0;
        background: rgba(255, 255, 255, 0.8);
        display: flex;
        justify-content: center;
        align-items: center;
        z-index: 1;
        border-radius: 12px;
    }

    .loading-spinner {
        width: 40px;
        height: 40px;
        border: 4px solid #f3f3f3;
        border-top: 4px solid #1a5f7a;
        border-radius: 50%;
        animation: spin 1s linear infinite;
    }

    @keyframes spin {
        0% { transform: rotate(0deg); }
        100% { transform: rotate(360deg); }
    }
                /* User Info Display */
                .user-info-section {
                    background: #fff;
                    padding: 1.5rem;
                    border-radius: 12px;
                    box-shadow: 0 4px 15px rgba(0, 0, 0, 0.05);
                    margin-bottom: 2rem;
                }

                .user-info-card {
                    border: 1px solid #e5e7eb;
                    padding: 1.5rem;
                    border-radius: 10px;
                    background: #f8fafc;
                }

                .user-info-item {
                    display: flex;
                    margin-bottom: 1rem;
                }

                .user-info-item label {
                    min-width: 150px;
                    font-weight: 600;
                    color: #64748b;
                }

                .user-info-item span {
                    color: #1e293b;
                }

                /* Pagination Styles */
                .pagination-container {
                    display: flex;
                    justify-content: space-between;
                    align-items: center;
                    padding: 1rem;
                    background: #f8fafc;
                    border-top: 1px solid #e2e8f0;
                    border-radius: 0 0 12px 12px;
                }

                .pagination-info {
                    color: #64748b;
                    font-size: 0.9rem;
                }

                .pagination-btn {
                    padding: 0.5rem 1rem;
                    border: 1px solid #e2e8f0;
                    border-radius: 8px;
                    background: white;
                    color: #1a5f7a;
                    font-size: 0.9rem;
                    cursor: pointer;
                    transition: all 0.2s ease;
                }

                .pagination-btn:hover:not(:disabled) {
                    background: #f0f9ff;
                    border-color: #2c86aa;
                }

                .pagination-btn:disabled {
                    opacity: 0.5;
                    cursor: not-allowed;
                }

                /* No Results State */
                .no-data {
                    padding: 3rem 0;
                    text-align: center;
                    background: #fff;
                    border-radius: 12px;
                    margin-top: 2rem;
                }

                .no-data-icon {
                    width: 80px;
                    height: 80px;
                    background: #f8fafc;
                    border-radius: 50%;
                    display: flex;
                    align-items: center;
                    justify-content: center;
                    margin: 0 auto 1.5rem;
                }

                .no-data-icon i {
                    font-size: 32px;
                    color: #94a3b8;
                }

                .no-data h5 {
                    color: #334155;
                    font-size: 1.25rem;
                    margin-bottom: 0.5rem;
                }

                .no-data p {
                    color: #64748b;
                    font-size: 1rem;
                }

                /* Document Link Styles */
                .doc-link {
                    padding: 0.5rem 1rem;
                    border-radius: 6px;
                    color: #2c86aa;
                    text-decoration: none;
                    font-weight: 500;
                    transition: all 0.2s ease;
                    display: inline-flex;
                    align-items: center;
                    gap: 0.5rem;
                }

                .doc-link:hover {
                    background: #f0f9ff;
                    color: #1a5f7a;
                }

                .doc-link i {
                    font-size: 1.1rem;
                }

                /* Advanced Search Styles */
                .search-actions {
                    margin: 1rem 0;
                }

                .btn-outline-secondary {
                    border: 1px solid #e2e8f0;
                    background: white;
                    color: #64748b;
                    padding: 0.5rem 1rem;
                    border-radius: 6px;
                    transition: all 0.2s ease;
                }

                .btn-outline-secondary:hover {
                    background: #f8fafc;
                    color: #334155;
                }

                .btn-outline-danger {
                    border: 1px solid #fecaca;
                    background: white;
                    color: #ef4444;
                    padding: 0.5rem 1rem;
                    border-radius: 6px;
                    transition: all 0.2s ease;
                }

                .btn-outline-danger:hover {
                    background: #fef2f2;
                    color: #dc2626;
                }

                /* Loading State */
                .loading-overlay {
                    position: absolute;
                    top: 0;
                    left: 0;
                    right: 0;
                    bottom: 0;
                    background: rgba(255, 255, 255, 0.8);
                    display: flex;
                    justify-content: center;
                    align-items: center;
                    z-index: 1000;
                }

                .loading-spinner {
                    width: 40px;
                    height: 40px;
                    border: 3px solid #f3f3f3;
                    border-top: 3px solid #2c86aa;
                    border-radius: 50%;
                    animation: spin 1s linear infinite;
                }

                /* Helpers */
                .text-end {
                    text-align: right;
                }

                .text-center {
                    text-align: center;
                }

                .mb-4 {
                    margin-bottom: 1.5rem;
                }

                .mt-4 {
                    margin-top: 1.5rem;
                }

                /* Responsive Adjustments */
                @media (max-width: 640px) {
                    .user-info-item {
                        flex-direction: column;
                    }

                    .user-info-item label {
                        margin-bottom: 0.25rem;
                    }

                    .pagination-container {
                        flex-direction: column;
                        gap: 1rem;
                        text-align: center;
                    }

                    .search-actions {
                        flex-wrap: wrap;
                        gap: 0.5rem;
                    }
                }

                /* Main Containers */
                .container {
                    max-width: 1200px;
                    margin: 0 auto;
                    padding: 2rem;
                }

                .search-status-section {
                    background: #fff;
                    padding: 2rem;
                    border-radius: 1rem;
                    box-shadow: 0 4px 15px rgba(0, 0, 0, 0.1);
                    margin-bottom: 2rem;
                }

                /* Header and Buttons */
                .payment-btn {
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

                .payment-btn:hover {
                    transform: translateY(-2px);
                    box-shadow: 0 6px 8px rgba(76, 175, 80, 0.3);
                }

                /* Search Box */
                .search-box {
                    margin-bottom: 2rem;
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

                /* Advanced Search */
                .advanced-search {
                    margin: 2rem 0;
                }

                .advanced-search .card {
                    border: none;
                    box-shadow: 0 4px 12px rgba(0, 0, 0, 0.05);
                    border-radius: 12px;
                }

                .advanced-search .card-body {
                    padding: 1.5rem;
                }

                /* Table Styles */
                .result-table-container {
                    background: white;
                    border-radius: 15px;
                    box-shadow: 0 4px 12px rgba(0, 0, 0, 0.05);
                    overflow: hidden;
                    margin-top: 2rem;
                }

                .result-table {
                    width: 100%;
                    border-collapse: separate;
                    border-spacing: 0;
                }

                .result-table th {
                    background: #f8fafc;
                    padding: 15px 20px;
                    font-weight: 600;
                    color: #334155;
                    font-size: 14px;
                    text-transform: uppercase;
                    letter-spacing: 0.5px;
                    border-bottom: 2px solid #e2e8f0;
                }

                .result-table td {
                    padding: 15px 20px;
                    color: #475569;
                    border-bottom: 1px solid #e2e8f0;
                    vertical-align: middle;
                }

                .result-table tbody tr:hover {
                    background-color: #f8fafc;
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

                /* Modal Styles */
                .modal {
                    display: none;
                    /* เพิ่มบรรทัดนี้ */
                    background-color: rgba(0, 0, 0, 0.5);
                }

                .modal-content {
                    border: none;
                    border-radius: 20px;
                    box-shadow: 0 25px 50px -12px rgba(0, 0, 0, 0.25);
                }

                .modal-header {
                    background: linear-gradient(120deg, #1a5f7a, #2c86aa);
                    color: white;
                    border-radius: 20px 20px 0 0;
                    border: none;
                    padding: 1.5rem;
                }

                .modal-title {
                    font-size: 1.5rem;
                    font-weight: 600;
                }

                .modal-body {
                    padding: 2rem;
                }

                /* Form Styles */
                .form-section {
                    background: #f8fafc;
                    padding: 1.5rem;
                    border-radius: 12px;
                    margin-bottom: 1.5rem;
                    border-left: 4px solid #2c86aa;
                }

                .section-title {
                    color: #1a5f7a;
                    font-size: 1.2rem;
                    font-weight: 600;
                    margin-bottom: 1rem;
                }

                .form-group {
                    margin-bottom: 1rem;
                }

                .form-control {
                    border-radius: 8px;
                    border: 1px solid #e2e8f0;
                    padding: 0.75rem 1rem;
                    transition: all 0.3s ease;
                }

                .form-control:focus {
                    border-color: #2c86aa;
                    box-shadow: 0 0 0 3px rgba(44, 134, 170, 0.1);
                }

                /* File Upload */
                .file-upload {
                    border: 2px dashed #e2e8f0;
                    padding: 2rem;
                    text-align: center;
                    border-radius: 12px;
                    background: #f8fafc;
                    transition: all 0.3s ease;
                }

                .file-upload:hover {
                    border-color: #2c86aa;
                    background: #f0f9ff;
                }

                /* Bank Info Section */
                .bank-details {
                    background: #f8fafc;
                    padding: 1.5rem;
                    border-radius: 12px;
                    border-left: 4px solid #2c86aa;
                }

                .qr-code-container {
                    max-width: 200px;
                    margin: 0 auto;
                    padding: 1rem;
                    background: white;
                    border-radius: 12px;
                    box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
                }

                .qr-code-image {
                    width: 100%;
                    height: auto;
                    border-radius: 8px;
                }

                /* Responsive Design */
                @media (max-width: 768px) {
                    .search-input-group {
                        flex-direction: column;
                        padding: 1rem;
                    }

                    .search-btn {
                        width: 100%;
                        justify-content: center;
                    }

                    .result-table {
                        display: block;
                        overflow-x: auto;
                        white-space: nowrap;
                    }

                    .form-section {
                        padding: 1rem;
                    }
                }

                /* Animations */
                @keyframes fadeIn {
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
                    animation: fadeIn 0.3s ease-out;
                }

                /* Loading States */
                .btn-loading {
                    position: relative;
                    pointer-events: none;
                    opacity: 0.8;
                }

                .btn-loading i {
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
            </style>

        </div>
    </div>