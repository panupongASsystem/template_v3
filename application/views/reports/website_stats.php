 <style>
	 
	 body {
    padding-top: 20px !important;
}
	 
	 
</style>
<!-- สรุปสถิติการใช้งานเว็บไซต์ พร้อม Filter System -->
<div class="container-fluid py-4">
    <!-- Header Section -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="card shadow-sm border-0">
                <div class="card-header" style="background: linear-gradient(135deg, #a8b5f3 0%, #b192c9 100%); color: white;">
                    <div class="d-flex justify-content-between align-items-center">
                        <h4 class="mb-0">
                            <i class="fas fa-chart-line me-2"></i>
                            สรุปสถิติการใช้งานเว็บไซต์
                        </h4>
                        <div class="d-flex align-items-center">
                           
                            <button type="button" class="btn btn-light btn-sm" onclick="refreshAllData()" id="refreshBtn">
                                <i class="fas fa-sync-alt me-1"></i>
                                รีเฟรช
                            </button>
                        </div>
                    </div>
                </div>
                
                <!-- Filter Section -->
                <div class="card-body bg-light">
                    <div class="row align-items-center">
                        <div class="col-md-12">
                            <div class="btn-group period-filter" role="group" aria-label="Period Filter">
                                <input type="radio" class="btn-check" name="periodFilter" id="today" value="today">
                                <label class="btn btn-outline-primary" for="today">
                                    <i class="fas fa-calendar-day me-1"></i>
                                    วันนี้
                                </label>

                                <input type="radio" class="btn-check" name="periodFilter" id="7days" value="7days" checked>
                                <label class="btn btn-outline-primary" for="7days">
                                    <i class="fas fa-calendar-week me-1"></i>
                                    7 วันล่าสุด
                                </label>

                                <input type="radio" class="btn-check" name="periodFilter" id="30days" value="30days">
                                <label class="btn btn-outline-primary" for="30days">
                                    <i class="fas fa-calendar-alt me-1"></i>
                                    30 วันล่าสุด
                                </label>

                                <input type="radio" class="btn-check" name="periodFilter" id="current_month" value="current_month">
                                <label class="btn btn-outline-primary" for="current_month">
                                    <i class="fas fa-calendar me-1"></i>
                                    เดือนนี้
                                </label>

                                <input type="radio" class="btn-check" name="periodFilter" id="custom" value="custom">
                                <label class="btn btn-outline-primary" for="custom" data-bs-toggle="modal" data-bs-target="#customDateModal">
                                    <i class="fas fa-calendar-plus me-1"></i>
                                    กำหนดเอง
                                </label>
                            </div>
                            
                            <!-- Period Info Display -->
                            <div class="mt-3 d-flex align-items-center">
                                <i class="fas fa-info-circle text-primary me-2"></i>
                                <span class="text-muted" id="periodText">แสดงข้อมูล 7 วันล่าสุด</span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Loading Overlay -->
    <div id="loadingOverlay" class="position-fixed top-0 start-0 w-100 h-100 d-none" style="background: rgba(0,0,0,0.7); z-index: 9999;">
        <div class="d-flex justify-content-center align-items-center h-100">
            <div class="text-center text-white">
                <div class="spinner-border mb-3" style="width: 3rem; height: 3rem;" role="status">
                    <span class="visually-hidden">กำลังโหลด...</span>
                </div>
                <h5>กำลังโหลดข้อมูล...</h5>
                <p class="mb-0">กรุณารอสักครู่</p>
            </div>
        </div>
    </div>

    <!-- Statistics Overview Cards -->
    <div class="row mb-4" id="statsCards">
        <div class="col-xl-3 col-md-6 mb-3">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-body">
                    <div class="d-flex align-items-center">
                        <div class="flex-shrink-0">
                            <div class="avatar-sm rounded-circle bg-primary bg-gradient d-flex align-items-center justify-content-center">
                                <i class="fas fa-eye text-white"></i>
                            </div>
                        </div>
                        <div class="flex-grow-1 ms-3">
                            <h6 class="text-muted mb-1">การเข้าชมทั้งหมด</h6>
                            <h4 class="mb-0 text-primary" id="totalPageviews">0</h4>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-xl-3 col-md-6 mb-3">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-body">
                    <div class="d-flex align-items-center">
                        <div class="flex-shrink-0">
                            <div class="avatar-sm rounded-circle bg-success bg-gradient d-flex align-items-center justify-content-center">
                                <i class="fas fa-users text-white"></i>
                            </div>
                        </div>
                        <div class="flex-grow-1 ms-3">
                            <h6 class="text-muted mb-1">ผู้เยี่ยมชมทั้งหมด</h6>
                            <h4 class="mb-0 text-success" id="totalVisitors">0</h4>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-xl-3 col-md-6 mb-3">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-body">
                    <div class="d-flex align-items-center">
                        <div class="flex-shrink-0">
                            <div class="avatar-sm rounded-circle bg-info bg-gradient d-flex align-items-center justify-content-center">
                                <i class="fas fa-chart-bar text-white"></i>
                            </div>
                        </div>
                        <div class="flex-grow-1 ms-3">
                            <h6 class="text-muted mb-1">เฉลี่ยหน้าต่อผู้เยี่ยมชม</h6>
                            <h4 class="mb-0 text-info" id="avgPagesPerVisitor">0.00</h4>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-xl-3 col-md-6 mb-3">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-body">
                    <div class="d-flex align-items-center">
                        <div class="flex-shrink-0">
                            <div class="avatar-sm rounded-circle bg-warning bg-gradient d-flex align-items-center justify-content-center">
                                <i class="fas fa-wifi text-white"></i>
                            </div>
                        </div>
                        <div class="flex-grow-1 ms-3">
                            <h6 class="text-muted mb-1">ผู้ใช้ออนไลน์</h6>
                            <h4 class="mb-0 text-warning" id="onlineUsers">0</h4>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Charts Section -->
    <div class="row mb-4">
        <div class="col-xl-8">
            <div class="card border-0 shadow-sm daily-chart-card">
                <div class="card-header bg-white">
                    <h5 class="card-title mb-0">
                        <i class="fas fa-chart-line text-primary me-2"></i>
                        แนวโน้มการเข้าชมรายวัน
                    </h5>
                </div>
                <div class="card-body daily-chart-body">
                    <canvas id="dailyChart" height="200"></canvas>
                </div>
            </div>
        </div>

        <div class="col-xl-4">
            <div class="card border-0 shadow-sm">
                <div class="card-header bg-white">
                    <h5 class="card-title mb-0">
                        <i class="fas fa-mobile-alt text-success me-2"></i>
                        อุปกรณ์ที่ใช้งาน
                    </h5>
                </div>
                <div class="card-body">
                    <canvas id="deviceChart" height="200"></canvas>
                </div>
            </div>
        </div>
    </div>

    <!-- Data Tables Section -->
    <div class="row">
        <div class="col-xl-6 mb-4">
            <div class="card border-0 shadow-sm">
                <div class="card-header bg-white">
                    <h5 class="card-title mb-0">
                        <i class="fas fa-trophy text-warning me-2"></i>
                        หน้าที่เข้าชมยอดนิยม
                    </h5>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-hover">
                            <thead>
                                <tr>
                                    <th>อันดับ</th>
                                    <th>หน้าเว็บ</th>
                                    <th>การเข้าชม</th>
                                </tr>
                            </thead>
                            <tbody id="topDomainsTable">
                                <tr>
                                    <td colspan="3" class="text-center text-muted py-4">
                                        <i class="fas fa-spinner fa-spin me-2"></i>กำลังโหลดข้อมูล...
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-xl-6 mb-4">
            <div class="card border-0 shadow-sm">
                <div class="card-header bg-white">
                    <h5 class="card-title mb-0">
                        <i class="fas fa-browser text-info me-2"></i>
                        เบราว์เซอร์ยอดนิยม
                    </h5>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-hover">
                            <thead>
                                <tr>
                                    <th>เบราว์เซอร์</th>
                                    <th>จำนวนผู้ใช้</th>
                                    <th>เปอร์เซ็นต์</th>
                                </tr>
                            </thead>
                            <tbody id="browserStatsTable">
                                <tr>
                                    <td colspan="3" class="text-center text-muted py-4">
                                        <i class="fas fa-spinner fa-spin me-2"></i>กำลังโหลดข้อมูล...
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Export Section -->
    <div class="row">
        <div class="col-12">
            <div class="card border-0 shadow-sm">
                <div class="card-header bg-white">
                    <h5 class="card-title mb-0">
                        <i class="fas fa-download text-success me-2"></i>
                        ส่งออกรายงาน
                    </h5>
                </div>
                <div class="card-body">
                    <div class="row align-items-center">
                        <div class="col-md-6">
                            <p class="text-muted mb-2">ส่งออกรายงานสรุปสถิติในรูปแบบต่างๆ พร้อมข้อสรุปและคำแนะนำ</p>
                            <div class="btn-group" role="group">
                                <button type="button" class="btn btn-outline-primary" onclick="exportReport('preview')">
                                    <i class="fas fa-eye me-1"></i>
                                    Preview / Print
                                </button>
                                <button type="button" class="btn btn-outline-success" onclick="exportReport('csv')">
                                    <i class="fas fa-file-csv me-1"></i>
                                    CSV
                                </button>
                            </div>
                            <div class="mt-2">
                                <small class="text-muted">
                                    <i class="fas fa-info-circle me-1"></i>
                                    Preview: แสดงรายงานในหน้าใหม่ พร้อมปุ่มพิมพ์เป็น PDF<br>
                                    CSV: ดาวน์โหลดไฟล์ข้อมูลสำหรับ Excel
                                </small>
                            </div>
                        </div>
                        <div class="col-md-6 text-md-end">
                            <div class="form-check form-check-inline">
                                <input class="form-check-input" type="checkbox" id="includeCharts" checked>
                                <label class="form-check-label" for="includeCharts">
                                    รวมกราฟและแผนภูมิ
                                </label>
                            </div>
                            <div class="form-check form-check-inline">
                                <input class="form-check-input" type="checkbox" id="includeRecommendations" checked>
                                <label class="form-check-label" for="includeRecommendations">
                                    รวมข้อสรุปและคำแนะนำ
                                </label>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Custom Date Modal -->
<div class="modal fade" id="customDateModal" tabindex="-1" aria-labelledby="customDateModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header" style="background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); color: white;">
                <h5 class="modal-title" id="customDateModalLabel">
                    <i class="fas fa-calendar-alt me-2"></i>
                    เลือกช่วงวันที่
                </h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <!-- Quick Range Buttons -->
                <div class="row mb-4">
                    <div class="col-12">
                        <h6 class="text-muted mb-3">
                            <i class="fas fa-clock me-1"></i>
                            ช่วงเวลาด่วน
                        </h6>
                        <div class="btn-group flex-wrap" role="group">
                            <button type="button" class="btn btn-outline-secondary btn-sm" onclick="setQuickRange('today')">
                                วันนี้
                            </button>
                            <button type="button" class="btn btn-outline-secondary btn-sm" onclick="setQuickRange('yesterday')">
                                เมื่อวาน
                            </button>
                            <button type="button" class="btn btn-outline-secondary btn-sm" onclick="setQuickRange('last7days')">
                                7 วันล่าสุด
                            </button>
                            <button type="button" class="btn btn-outline-secondary btn-sm" onclick="setQuickRange('last30days')">
                                30 วันล่าสุด
                            </button>
                            <button type="button" class="btn btn-outline-secondary btn-sm" onclick="setQuickRange('thisMonth')">
                                เดือนนี้
                            </button>
                            <button type="button" class="btn btn-outline-secondary btn-sm" onclick="setQuickRange('lastMonth')">
                                เดือนที่แล้ว
                            </button>
                        </div>
                    </div>
                </div>

                <!-- Custom Date Selection -->
                <div class="row">
                    <div class="col-md-6">
                        <h6 class="text-muted mb-3">
                            <i class="fas fa-calendar-day me-1"></i>
                            เลือกวันที่เอง
                        </h6>
                        <div class="mb-3">
                            <label for="modalStartDate" class="form-label">วันที่เริ่มต้น</label>
                            <input type="date" class="form-control" id="modalStartDate" max="">
                        </div>
                        <div class="mb-3">
                            <label for="modalEndDate" class="form-label">วันที่สิ้นสุด</label>
                            <input type="date" class="form-control" id="modalEndDate" max="">
                        </div>
                        
                        <!-- Date Validation -->
                        <div id="dateValidationMessage" class="alert alert-warning d-none">
                            <i class="fas fa-exclamation-triangle me-1"></i>
                            <span id="validationText"></span>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <h6 class="text-muted mb-3">
                            <i class="fas fa-info-circle me-1"></i>
                            ตัวอย่าง
                        </h6>
                        <div class="card bg-light">
                            <div class="card-body">
                                <h6 class="card-title text-primary">ช่วงวันที่ที่เลือก:</h6>
                                <p class="card-text" id="previewDateRange">
                                    <i class="fas fa-calendar me-1"></i>
                                    <span class="text-muted">ยังไม่ได้เลือกวันที่</span>
                                </p>
                                <hr>
                                <h6 class="card-title text-success">จำนวนวัน:</h6>
                                <p class="card-text" id="previewDayCount">
                                    <i class="fas fa-clock me-1"></i>
                                    <span class="text-muted">0 วัน</span>
                                </p>
                                <hr>
                                <h6 class="card-title text-info">คำแนะนำ:</h6>
                                <ul class="small text-muted">
                                    <li>ช่วงวันที่มากเกินไปอาจทำให้โหลดช้า</li>
                                    <li>แนะนำไม่เกิน 90 วัน</li>
                                    <li>วันที่สิ้นสุดต้องมาหลังวันที่เริ่มต้น</li>
                                </ul>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                    <i class="fas fa-times me-1"></i>
                    ยกเลิก
                </button>
                <button type="button" class="btn btn-primary" onclick="applyCustomDateRange()" id="applyCustomBtn" disabled>
                    <i class="fas fa-check me-1"></i>
                    ใช้งาน
                </button>
            </div>
        </div>
    </div>
</div>

<!-- Toast Container -->
<div class="toast-container position-fixed top-0 end-0 p-3" id="toastContainer"></div>

<!-- Include required CSS and JS files -->
<script src="https://cdnjs.cloudflare.com/ajax/libs/Chart.js/3.9.1/chart.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap/5.3.0/js/bootstrap.bundle.min.js"></script>

<script>
// ✅ Global variables - ปรับปรุงให้รองรับ CodeIgniter 3
let currentPeriod = '7days';
let customStartDate = null;
let customEndDate = null;
let dailyChart = null;
let deviceChart = null;
let dataLoaded = false;

// ✅ กำหนด base URLs สำหรับ CodeIgniter 3 (ปรับตาม environment ของคุณ)
const BASE_URLS = {
    ajax_filter_stats: '/index.php/System_reports/ajax_filter_stats',
    export_stats_summary: '/index.php/System_reports/export_stats_summary'
};

// ✅ Helper functions for date manipulation
function getToday() {
    return new Date().toISOString().split('T')[0];
}

function getDateBefore(days) {
    const date = new Date();
    date.setDate(date.getDate() - days);
    return date.toISOString().split('T')[0];
}

function formatDateThai(dateString) {
    const date = new Date(dateString);
    return date.toLocaleDateString('th-TH');
}

// ✅ Number formatting utility
function numberFormat(num) {
    return new Intl.NumberFormat('th-TH').format(num || 0);
}

// ✅ Show/hide loading overlay
function showLoading(show) {
    const overlay = document.getElementById('loadingOverlay');
    if (show) {
        overlay.classList.remove('d-none');
    } else {
        overlay.classList.add('d-none');
    }
}

// ✅ Show toast notification - ปรับปรุงให้ไม่ต้องใช้ jQuery
function showToast(message, type = 'info') {
    const bgClass = {
        'success': 'bg-success',
        'error': 'bg-danger',
        'warning': 'bg-warning',
        'info': 'bg-info'
    };
    
    const icon = {
        'success': 'fa-check-circle',
        'error': 'fa-exclamation-triangle',
        'warning': 'fa-exclamation-circle',
        'info': 'fa-info-circle'
    };
    
    const toastHtml = `
        <div class="toast align-items-center text-white ${bgClass[type]} border-0" role="alert">
            <div class="d-flex">
                <div class="toast-body">
                    <i class="fas ${icon[type]} me-2"></i>
                    ${message}
                </div>
                <button type="button" class="btn-close btn-close-white me-2 m-auto" data-bs-dismiss="toast"></button>
            </div>
        </div>
    `;
    
    const toastContainer = document.getElementById('toastContainer');
    toastContainer.insertAdjacentHTML('beforeend', toastHtml);
    
    const toastElement = toastContainer.lastElementChild;
    const toast = new bootstrap.Toast(toastElement, { delay: 4000 });
    toast.show();
    
    toastElement.addEventListener('hidden.bs.toast', () => {
        toastElement.remove();
    });
}

// ✅ Get period description text
function getPeriodText(period) {
    const texts = {
        'today': 'แสดงข้อมูลวันนี้',
        '7days': 'แสดงข้อมูล 7 วันล่าสุด',
        '30days': 'แสดงข้อมูล 30 วันล่าสุด',
        'current_month': 'แสดงข้อมูลเดือนปัจจุบัน'
    };
    return texts[period] || 'แสดงข้อมูลช่วงเวลาที่เลือก';
}

// ✅ Update period text display
function updatePeriodText(text) {
    document.getElementById('periodText').textContent = text;
}

// ✅ ปรับปรุงฟังก์ชันโหลดข้อมูล - แก้ไข JSON parsing error
async function loadStatsData(period, startDate = null, endDate = null) {
    showLoading(true);
    
    const params = new URLSearchParams({
        period: period
    });
    
    if (period === 'custom' && startDate && endDate) {
        params.append('start_date', startDate);
        params.append('end_date', endDate);
    }
    
    // console.log('Loading stats data:', Object.fromEntries(params));
    
    try {
        const response = await fetch(BASE_URLS.ajax_filter_stats + '?' + params.toString(), {
            method: 'GET',
            headers: {
                'Accept': 'application/json',
                'Content-Type': 'application/json',
                'X-Requested-With': 'XMLHttpRequest'
            },
            cache: 'no-cache'
        });
        
        // console.log('Response status:', response.status);
        // console.log('Response headers:', response.headers);
        
        // ✅ ตรวจสอบ Content-Type ก่อนแปลง JSON
        const contentType = response.headers.get('content-type');
        if (!contentType || !contentType.includes('application/json')) {
            throw new Error(`Expected JSON response but received: ${contentType}`);
        }
        
        // ✅ ตรวจสอบสถานะ response
        if (!response.ok) {
            throw new Error(`HTTP error! status: ${response.status}`);
        }
        
        // ✅ อ่าน response text ก่อนแปลง JSON เพื่อ debug
        const responseText = await response.text();
        // console.log('Raw response:', responseText.substring(0, 500));
        
        if (!responseText.trim()) {
            throw new Error('Empty response received');
        }
        
        // ✅ แปลง JSON
        let data;
        try {
            data = JSON.parse(responseText);
        } catch (jsonError) {
            console.error('JSON parsing error:', jsonError);
            console.error('Response text:', responseText);
            throw new Error('Invalid JSON response from server');
        }
        
        // console.log('Parsed data:', data);
        
        if (data && data.success) {
            updateStatsDisplay(data.data);
            dataLoaded = true;
            showToast('ข้อมูลได้รับการอัปเดตแล้ว (' + period + ')', 'success');
        } else {
            const errorMsg = data?.message || data?.error || 'เกิดข้อผิดพลาดในการโหลดข้อมูล';
            throw new Error(errorMsg);
        }
        
    } catch (error) {
        console.error('Load stats data error:', error);
        
        let errorMessage = 'ไม่สามารถโหลดข้อมูลได้';
        
        if (error.message.includes('Failed to fetch')) {
            errorMessage = 'ไม่สามารถเชื่อมต่อเซิร์ฟเวอร์ได้ กรุณาตรวจสอบการเชื่อมต่ออินเทอร์เน็ต';
        } else if (error.message.includes('JSON')) {
            errorMessage = 'เซิร์ฟเวอร์ส่งข้อมูลผิดรูปแบบ กรุณาตรวจสอบ Controller';
        } else if (error.message.includes('404')) {
            errorMessage = 'ไม่พบหน้าที่ร้องขอ กรุณาตรวจสอบ URL ใน Controller';
        } else if (error.message.includes('500')) {
            errorMessage = 'เกิดข้อผิดพลาดในเซิร์ฟเวอร์ กรุณาตรวจสอบ PHP Error Log';
        } else {
            errorMessage = error.message;
        }
        
        showToast(errorMessage, 'error');
    } finally {
        showLoading(false);
    }
}

// ✅ อัปเดตฟังก์ชัน updateStatsDisplay ให้เรียกใช้ฟังก์ชันเดียว
function updateStatsDisplay(data) {
    // console.log('Updating stats display with data:', data);
    
    // Update summary cards
    document.getElementById('totalPageviews').textContent = numberFormat(data.stats_summary?.total_pageviews || 0);
    document.getElementById('totalVisitors').textContent = numberFormat(data.stats_summary?.total_visitors || 0);
    document.getElementById('avgPagesPerVisitor').textContent = data.calculated_stats?.avg_pages_per_visitor || '0.00';
    document.getElementById('onlineUsers').textContent = numberFormat(data.stats_summary?.online_users || 0);
    
    // Update tables and charts
    updateTopDomainsTable(data.top_domains || []);
    updateBrowserStatsTable(data.browser_stats || []);
    updateDailyChart(data.daily_stats || []);
    updateDeviceChart(data.device_stats || []);
}

// ✅ แก้ไขฟังก์ชัน updateTopDomainsTable ให้รองรับข้อมูล page
// ✅ ในไฟล์ JavaScript (ส่วนที่อัปเดตตาราง)
function updateTopDomainsTable(pages) {
    const tbody = document.getElementById('topDomainsTable');
    tbody.innerHTML = '';
    
    if (!pages || pages.length === 0) {
        tbody.innerHTML = `
            <tr>
                <td colspan="3" class="text-center text-muted py-4">
                    <i class="fas fa-inbox me-2"></i>ไม่มีข้อมูล
                </td>
            </tr>
        `;
        return;
    }
    
    pages.forEach((page, index) => {
        // ✅ รองรับทั้งข้อมูลแบบใหม่และแบบเก่า
        let displayTitle, displayUrl;
        
        if (page.page_title !== undefined && page.page_url !== undefined) {
            // ข้อมูลแบบใหม่ (page data)
            displayTitle = page.page_title || 'ไม่ระบุ';
            displayUrl = page.page_url || '';
        } else {
            // ข้อมูลแบบเก่า (domain data)  
            displayTitle = page.domain_name || 'ไม่ระบุ';
            displayUrl = '';
        }
        
        const totalViews = parseInt(page.total_views) || 0;
        const shortUrl = displayUrl.length > 50 ? displayUrl.substring(0, 50) + '...' : displayUrl;
        
        tbody.innerHTML += `
            <tr>
                <td>
                    <span class="badge bg-warning">${index + 1}</span>
                </td>
                <td>
                    <div class="d-flex flex-column">
                        <div class="fw-bold">
                            <i class="fas fa-${displayUrl ? 'file-alt' : 'globe'} me-2 text-primary"></i>
                            ${displayTitle}
                        </div>
                        ${shortUrl ? `<small class="text-muted">${shortUrl}</small>` : ''}
                    </div>
                </td>
                <td>
                    <span class="fw-bold text-primary">${numberFormat(totalViews)}</span>
                </td>
            </tr>
        `;
    });
}

// ✅ Update browser stats table
function updateBrowserStatsTable(browsers) {
    const tbody = document.getElementById('browserStatsTable');
    tbody.innerHTML = '';
    
    if (!browsers || browsers.length === 0) {
        tbody.innerHTML = `
            <tr>
                <td colspan="3" class="text-center text-muted py-4">
                    <i class="fas fa-inbox me-2"></i>ไม่มีข้อมูล
                </td>
            </tr>
        `;
        return;
    }
    
    // 🔍 Debug: แสดงข้อมูลดิบที่ได้รับ
    //console.log('Browser data received:', browsers);
    
    // ✅ แก้ไข: ตรวจสอบโครงสร้างข้อมูลและแปลงเป็นตัวเลข
    const validBrowsers = browsers.map(browser => {
        // รองรับโครงสร้างข้อมูลหลายแบบ
        const count = parseInt(browser.count || browser.total || browser.users || 0);
        const name = browser.browser || browser.browser_name || browser.name || 'Unknown';
        
       // console.log(`Browser: ${name}, Count: ${count} (original: ${browser.count})`);
        
        return {
            browser: name,
            count: count
        };
    }).filter(browser => browser.count > 0); // กรองข้อมูลที่มีค่า > 0
    
    // 🔍 Debug: แสดงข้อมูลหลังจากปรับแล้ว
    console.log('Processed browser data:', validBrowsers);
    
    // ✅ คำนวณผลรวมใหม่
    const total = validBrowsers.reduce((sum, browser) => sum + browser.count, 0);
    console.log('Total users:', total);
    
    // ✅ ตรวจสอบว่ามีข้อมูลหรือไม่
    if (total === 0) {
        tbody.innerHTML = `
            <tr>
                <td colspan="3" class="text-center text-muted py-4">
                    <i class="fas fa-exclamation-triangle me-2"></i>ไม่มีข้อมูลผู้ใช้
                </td>
            </tr>
        `;
        return;
    }
    
    // ✅ สร้างแถวตาราง
    validBrowsers.forEach((browser, index) => {
        const percentage = ((browser.count / total) * 100).toFixed(1);
        
        // 🔍 Debug: แสดงการคำนวณแต่ละแถว
        //console.log(`${browser.browser}: ${browser.count}/${total} = ${percentage}%`);
        
        // ✅ กำหนดไอคอนเบราว์เซอร์ที่ถูกต้อง
        const browserIcons = {
            'chrome': 'chrome',
            'firefox': 'firefox',
            'safari': 'safari',
            'edge': 'edge',
            'opera': 'opera',
            'internet explorer': 'internet-explorer',
            'ie': 'internet-explorer'
        };
        
        const iconClass = browserIcons[browser.browser.toLowerCase()] || 'globe';
        
        tbody.innerHTML += `
            <tr>
                <td>
                    <i class="fab fa-${iconClass} me-2 text-primary"></i>
                    ${browser.browser}
                </td>
                <td>
                    <span class="fw-bold text-info">${numberFormat(browser.count)}</span>
                </td>
                <td>
                    <div class="d-flex align-items-center">
                        <div class="progress me-2" style="width: 60px; height: 6px;">
                            <div class="progress-bar bg-primary" style="width: ${percentage}%"></div>
                        </div>
                        <span class="text-muted small">${percentage}%</span>
                    </div>
                </td>
            </tr>
        `;
    });
    
    // 🔍 Debug: ตรวจสอบผลรวมเปอร์เซ็นต์
    const totalPercentage = validBrowsers.reduce((sum, browser) => {
        return sum + parseFloat(((browser.count / total) * 100).toFixed(1));
    }, 0);
   // console.log('Total percentage check:', totalPercentage.toFixed(1) + '%');
}
// ✅ Chart Functions
function updateDailyChart(dailyStats) {
    const ctx = document.getElementById('dailyChart').getContext('2d');
    
    if (dailyChart) {
        dailyChart.destroy();
    }
    
    if (!dailyStats || dailyStats.length === 0) {
        ctx.font = "16px Arial";
        ctx.fillStyle = "#999";
        ctx.textAlign = "center";
        ctx.fillText("ไม่มีข้อมูลสำหรับแสดงกราฟ", ctx.canvas.width/2, ctx.canvas.height/2);
        return;
    }
    
    const labels = dailyStats.map(stat => {
        const date = new Date(stat.date);
        return date.toLocaleDateString('th-TH', { day: '2-digit', month: '2-digit' });
    });
    
    const pageviewsData = dailyStats.map(stat => parseInt(stat.pageviews) || 0);
    const visitorsData = dailyStats.map(stat => parseInt(stat.visitors) || 0);
    
    dailyChart = new Chart(ctx, {
        type: 'line',
        data: {
            labels: labels,
            datasets: [{
                label: 'การเข้าชม',
                data: pageviewsData,
                borderColor: '#667eea',
                backgroundColor: 'rgba(102, 126, 234, 0.1)',
                tension: 0.4,
                fill: true,
                pointBackgroundColor: '#667eea',
                pointBorderColor: '#ffffff',
                pointBorderWidth: 2,
                pointRadius: 4
            }, {
                label: 'ผู้เยี่ยมชม',
                data: visitorsData,
                borderColor: '#06d6a0',
                backgroundColor: 'rgba(6, 214, 160, 0.1)',
                tension: 0.4,
                fill: true,
                pointBackgroundColor: '#06d6a0',
                pointBorderColor: '#ffffff',
                pointBorderWidth: 2,
                pointRadius: 4
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
                legend: {
                    position: 'top',
                    labels: {
                        usePointStyle: true,
                        padding: 20
                    }
                },
                tooltip: {
                    backgroundColor: 'rgba(0, 0, 0, 0.8)',
                    titleColor: '#ffffff',
                    bodyColor: '#ffffff',
                    cornerRadius: 8
                }
            },
            scales: {
                x: {
                    grid: {
                        color: 'rgba(0, 0, 0, 0.05)'
                    }
                },
                y: {
                    beginAtZero: true,
                    grid: {
                        color: 'rgba(0, 0, 0, 0.05)'
                    }
                }
            },
            interaction: {
                intersect: false,
                mode: 'index'
            }
        }
    });
}

function updateDeviceChart(deviceStats) {
    const ctx = document.getElementById('deviceChart').getContext('2d');
    
    if (deviceChart) {
        deviceChart.destroy();
    }
    
    if (!deviceStats || deviceStats.length === 0) {
        ctx.font = "16px Arial";
        ctx.fillStyle = "#999";
        ctx.textAlign = "center";
        ctx.fillText("ไม่มีข้อมูลสำหรับแสดงกราฟ", ctx.canvas.width/2, ctx.canvas.height/2);
        return;
    }
    
    const labels = deviceStats.map(stat => stat.device || 'Unknown');
    const data = deviceStats.map(stat => parseInt(stat.count) || 0);
    
    deviceChart = new Chart(ctx, {
        type: 'doughnut',
        data: {
            labels: labels,
            datasets: [{
                data: data,
                backgroundColor: [
                    '#667eea',
                    '#06d6a0', 
                    '#ffc107',
                    '#e74c3c',
                    '#9b59b6',
                    '#fd7e14'
                ],
                borderWidth: 3,
                borderColor: '#fff'
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
                legend: {
                    position: 'bottom',
                    labels: {
                        usePointStyle: true,
                        padding: 15
                    }
                },
                tooltip: {
                    backgroundColor: 'rgba(0, 0, 0, 0.8)',
                    titleColor: '#ffffff',
                    bodyColor: '#ffffff',
                    cornerRadius: 8
                }
            },
            cutout: '60%'
        }
    });
}

// ✅ Period Filter Functions
function initializePeriodFilters() {
    // console.log('Initializing period filters...');
    
    const periodInputs = document.querySelectorAll('input[name="periodFilter"]');
    periodInputs.forEach(input => {
        input.addEventListener('change', function() {
            const selectedPeriod = this.value;
            currentPeriod = selectedPeriod;
            
            if (selectedPeriod === 'custom') {
                updatePeriodText('กรุณาเลือกช่วงวันที่ในหน้าต่างที่เปิดขึ้น');
            } else {
                updatePeriodText(getPeriodText(selectedPeriod));
                loadStatsData(selectedPeriod);
            }
        });
    });
    
    // console.log('Period filters initialized');
}

// ✅ Refresh all data
function refreshAllData() {
    const btn = document.getElementById('refreshBtn');
    const originalHtml = btn.innerHTML;
    
    btn.disabled = true;
    btn.innerHTML = '<i class="fas fa-spinner fa-spin me-1"></i>กำลังรีเฟรช...';
    
    setTimeout(() => {
        if (currentPeriod === 'custom' && customStartDate && customEndDate) {
            loadStatsData(currentPeriod, customStartDate, customEndDate);
        } else {
            loadStatsData(currentPeriod);
        }
        btn.disabled = false;
        btn.innerHTML = originalHtml;
    }, 500);
}

// ✅ Modal Functions
function initializeModal() {
    const today = getToday();
    document.getElementById('modalStartDate').setAttribute('max', today);
    document.getElementById('modalEndDate').setAttribute('max', today);
    
    const sevenDaysAgo = getDateBefore(7);
    document.getElementById('modalStartDate').value = sevenDaysAgo;
    document.getElementById('modalEndDate').value = today;
    
    document.getElementById('modalStartDate').addEventListener('change', () => {
        validateModalDates();
        updatePreview();
    });
    
    document.getElementById('modalEndDate').addEventListener('change', () => {
        validateModalDates();
        updatePreview();
    });
    
    updatePreview();
}

function validateModalDates() {
    const startDate = document.getElementById('modalStartDate').value;
    const endDate = document.getElementById('modalEndDate').value;
    const confirmBtn = document.getElementById('applyCustomBtn');
    const validationDiv = document.getElementById('dateValidationMessage');
    
    if (!startDate || !endDate) {
        confirmBtn.disabled = true;
        return;
    }
    
    const start = new Date(startDate);
    const end = new Date(endDate);
    const today = new Date();
    
    if (start > end) {
        validationDiv.classList.remove('d-none');
        document.getElementById('validationText').textContent = 'วันที่เริ่มต้นต้องมาก่อนวันที่สิ้นสุด';
        confirmBtn.disabled = true;
        return;
    }
    
    if (end > today) {
        validationDiv.classList.remove('d-none');
        document.getElementById('validationText').textContent = 'วันที่สิ้นสุดไม่สามารถเลือกวันในอนาคตได้';
        confirmBtn.disabled = true;
        return;
    }
    
    const dayDiff = Math.ceil((end - start) / (1000 * 60 * 60 * 24));
    if (dayDiff > 90) {
        validationDiv.classList.remove('d-none');
        document.getElementById('validationText').textContent = 'ช่วงวันที่ไม่ควรเกิน 90 วัน';
        confirmBtn.disabled = false;
        return;
    }
    
    validationDiv.classList.add('d-none');
    confirmBtn.disabled = false;
}

function updatePreview() {
    const startDate = document.getElementById('modalStartDate').value;
    const endDate = document.getElementById('modalEndDate').value;
    
    if (startDate && endDate) {
        const start = new Date(startDate);
        const end = new Date(endDate);
        const dayCount = Math.ceil((end - start) / (1000 * 60 * 60 * 24)) + 1;
        
        document.getElementById('previewDateRange').innerHTML = `
            <i class="fas fa-calendar me-1"></i>
            ${formatDateThai(startDate)} ถึง ${formatDateThai(endDate)}
        `;
        
        document.getElementById('previewDayCount').innerHTML = `
            <i class="fas fa-clock me-1"></i>
            ${dayCount} วัน
        `;
    } else {
        document.getElementById('previewDateRange').innerHTML = `
            <i class="fas fa-calendar me-1"></i>
            <span class="text-muted">ยังไม่ได้เลือกวันที่</span>
        `;
        
        document.getElementById('previewDayCount').innerHTML = `
            <i class="fas fa-clock me-1"></i>
            <span class="text-muted">0 วัน</span>
        `;
    }
}

// ✅ Quick range functions
function setQuickRange(range) {
    const today = new Date();
    let startDate, endDate;
    
    switch (range) {
        case 'today':
            startDate = endDate = today;
            break;
        case 'yesterday':
            startDate = endDate = new Date(today.getTime() - 24 * 60 * 60 * 1000);
            break;
        case 'last7days':
            startDate = new Date(today.getTime() - 6 * 24 * 60 * 60 * 1000);
            endDate = today;
            break;
        case 'last30days':
            startDate = new Date(today.getTime() - 29 * 24 * 60 * 60 * 1000);
            endDate = today;
            break;
        case 'thisMonth':
            startDate = new Date(today.getFullYear(), today.getMonth(), 1);
            endDate = today;
            break;
        case 'lastMonth':
            startDate = new Date(today.getFullYear(), today.getMonth() - 1, 1);
            endDate = new Date(today.getFullYear(), today.getMonth(), 0);
            break;
    }
    
    document.getElementById('modalStartDate').value = startDate.toISOString().split('T')[0];
    document.getElementById('modalEndDate').value = endDate.toISOString().split('T')[0];
    
    validateModalDates();
    updatePreview();
}

// ✅ Apply custom date range
function applyCustomDateRange() {
    const startDate = document.getElementById('modalStartDate').value;
    const endDate = document.getElementById('modalEndDate').value;
    
    if (!startDate || !endDate) {
        showToast('กรุณาเลือกวันที่เริ่มต้นและสิ้นสุด', 'warning');
        return;
    }
    
    customStartDate = startDate;
    customEndDate = endDate;
    
    const startFormatted = formatDateThai(startDate);
    const endFormatted = formatDateThai(endDate);
    
    updatePeriodText(`แสดงข้อมูลจาก ${startFormatted} ถึง ${endFormatted}`);
    
    const modal = bootstrap.Modal.getInstance(document.getElementById('customDateModal'));
    modal.hide();
    
    loadStatsData('custom', customStartDate, customEndDate);
    
    showToast('กำลังโหลดข้อมูลช่วงวันที่ที่เลือก...', 'info');
}

// ✅ Export report function
function exportReport(type) {
    const includeCharts = document.getElementById('includeCharts').checked;
    const includeRecommendations = document.getElementById('includeRecommendations').checked;
    
    if (type === 'preview') {
        showToast('กำลังสร้างหน้าแสดงผลรายงาน...', 'info');
    } else {
        showToast('กำลังสร้างรายงาน...', 'info');
    }
    
    // สร้าง form สำหรับส่งข้อมูลไปยัง Controller
    const form = document.createElement('form');
    form.method = 'POST';
    form.action = BASE_URLS.export_stats_summary;
    form.style.display = 'none';
    form.target = type === 'preview' ? '_blank' : '_self';
    
    const fields = [
        { name: 'export_type', value: type },
        { name: 'period', value: currentPeriod },
        { name: 'include_charts', value: includeCharts },
        { name: 'include_recommendations', value: includeRecommendations }
    ];
    
    if (currentPeriod === 'custom' && customStartDate && customEndDate) {
        fields.push(
            { name: 'start_date', value: customStartDate },
            { name: 'end_date', value: customEndDate }
        );
    }
    
    fields.forEach(field => {
        const input = document.createElement('input');
        input.type = 'hidden';
        input.name = field.name;
        input.value = field.value;
        form.appendChild(input);
    });
    
    document.body.appendChild(form);
    form.submit();
    document.body.removeChild(form);
    
    if (type === 'preview') {
        setTimeout(() => {
            showToast('หน้าแสดงผลรายงานถูกเปิดในหน้าใหม่แล้ว', 'success');
        }, 1000);
    } else {
        setTimeout(() => {
            showToast(`รายงาน ${type.toUpperCase()} ถูกส่งออกเรียบร้อยแล้ว`, 'success');
        }, 2000);
    }
}

// ✅ Network Error Handling
function handleNetworkError() {
    window.addEventListener('offline', function() {
        showToast('การเชื่อมต่ออินเทอร์เน็ตขาดหาย', 'warning');
    });
    
    window.addEventListener('online', function() {
        showToast('การเชื่อมต่ออินเทอร์เน็ตกลับมาแล้ว', 'success');
        if (currentPeriod === 'custom' && customStartDate && customEndDate) {
            loadStatsData(currentPeriod, customStartDate, customEndDate);
        } else {
            loadStatsData(currentPeriod);
        }
    });
}

// ✅ Initialize when page loads
document.addEventListener('DOMContentLoaded', function() {
    // console.log('DOM ready, initializing...');
    
    handleNetworkError();
    
    // Debug information
    // console.log('Base URLs:', BASE_URLS);
    // console.log('Current period:', currentPeriod);
    
    try {
        initializePeriodFilters();
        initializeModal();
        // console.log('All components initialized successfully');
        
        // โหลดข้อมูลครั้งแรกอัตโนมัติเมื่อเข้าหน้า
        setTimeout(function() {
            // console.log('Auto-loading initial data...');
            loadStatsData(currentPeriod);
        }, 1000);
        
    } catch (error) {
        console.error('Error in initialization:', error);
        showToast('เกิดข้อผิดพลาดในการเริ่มต้นระบบ', 'error');
    }
    
    // เพิ่ม event handler สำหรับ modal custom date
    const customModal = document.getElementById('customDateModal');
    customModal.addEventListener('hidden.bs.modal', function() {
        if (!customStartDate || !customEndDate) {
            const checkedInput = document.querySelector('input[name="periodFilter"]:checked:not(#custom)');
            if (checkedInput) {
                currentPeriod = checkedInput.value;
                updatePeriodText(getPeriodText(currentPeriod));
            } else {
                // fallback to 7days
                document.getElementById('7days').checked = true;
                currentPeriod = '7days';
                updatePeriodText(getPeriodText('7days'));
            }
        }
    });
});

// ✅ Custom CSS
const customStyle = document.createElement('style');
customStyle.textContent = `
    .avatar-sm {
        width: 2.5rem;
        height: 2.5rem;
    }
    
    .period-filter .btn {
        border-radius: 0;
        font-size: 0.875rem;
        padding: 0.5rem 1rem;
    }
    
    .period-filter .btn:first-child {
        border-top-left-radius: 0.375rem;
        border-bottom-left-radius: 0.375rem;
    }
    
    .period-filter .btn:last-child {
        border-top-right-radius: 0.375rem;
        border-bottom-right-radius: 0.375rem;
    }
    
    .btn-check:checked + .btn {
        background-color: #667eea;
        border-color: #667eea;
        color: white;
    }
    
    .card {
        transition: all 0.3s ease;
    }
    
    .card:hover {
        transform: translateY(-2px);
        box-shadow: 0 4px 25px 0 rgba(0,0,0,.1);
    }
    
    .table th {
        font-weight: 600;
        font-size: 0.875rem;
        border-bottom: 2px solid #dee2e6;
    }
    
    .progress {
        border-radius: 10px;
    }
    
    .progress-bar {
        border-radius: 10px;
    }
    
    .btn-group .btn {
        transition: all 0.2s ease;
    }
    
    .btn-group .btn:hover {
        transform: translateY(-1px);
    }
    
    @media (max-width: 768px) {
        .period-filter {
            flex-wrap: wrap;
        }
        
        .period-filter .btn {
            font-size: 0.75rem;
            padding: 0.375rem 0.75rem;
        }
    }
`;
document.head.appendChild(customStyle);
</script>

<!-- ✅ เพิ่ม FontAwesome -->
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap/5.3.0/css/bootstrap.min.css">