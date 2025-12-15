<div class="ml-72 p-8">
    <!-- Header -->
    <div class="flex justify-between items-center mb-8">
        <div>
            <h2 class="text-2xl font-semibold text-gray-800">ภาพรวม</h2>
            <p class="text-gray-600">ภาพรวมข้อมูลระบบชำระภาษีทั้งหมดในระบบ</p>
        </div>
        <!-- <button class="px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700">
            <a href="<?php echo site_url('System_member/add_member'); ?>">
                <i class="fa-solid fa-qrcode mr-2"></i></i>QR Code ชำระเงิน
            </a>
        </button> -->

    </div>

    <!-- Stats Cards -->
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-5 gap-4 mb-8">
        <!-- ทั้งหมด -->
        <div class="bg-white p-6 rounded-xl shadow-lg hover:shadow-xl transition-all duration-300 transform hover:-translate-y-1">
            <div class="flex items-center">
                <div class="p-3 bg-gradient-to-br from-blue-100 to-blue-50 rounded-xl">
                    <i class="fas fa-clipboard-list text-2xl text-blue-600"></i>
                </div>
                <div class="ml-4">
                    <h4 class="text-gray-500 font-medium">ทั้งหมด</h4>
                    <p class="text-2xl font-semibold text-gray-800">
                        <?php echo number_format($payment_status_all); ?>
                    </p>
                </div>
            </div>
        </div>

        <!-- กำลังรออนุมัติ -->
        <div class="bg-white p-6 rounded-xl shadow-lg hover:shadow-xl transition-all duration-300 transform hover:-translate-y-1">
            <div class="flex items-center">
                <div class="p-3 bg-gradient-to-br from-yellow-100 to-yellow-50 rounded-xl">
                    <i class="fas fa-clock text-2xl text-yellow-600"></i>
                </div>
                <div class="ml-4">
                    <h4 class="text-gray-500 font-medium">กำลังรออนุมัติ</h4>
                    <p class="text-2xl font-semibold text-gray-800">
                        <?php echo number_format($payment_status_pending); ?>
                    </p>
                </div>
            </div>
        </div>

        <!-- อนุมัติแล้ว -->
        <div class="bg-white p-6 rounded-xl shadow-lg hover:shadow-xl transition-all duration-300 transform hover:-translate-y-1">
            <div class="flex items-center">
                <div class="p-3 bg-gradient-to-br from-green-100 to-green-50 rounded-xl">
                    <i class="fas fa-check-circle text-2xl text-green-600"></i>
                </div>
                <div class="ml-4">
                    <h4 class="text-gray-500 font-medium">อนุมัติแล้ว</h4>
                    <p class="text-2xl font-semibold text-gray-800">
                        <?php echo number_format($payment_status_verified); ?>
                    </p>
                </div>
            </div>
        </div>

        <!-- ปฏิเสธ -->
        <div class="bg-white p-6 rounded-xl shadow-lg hover:shadow-xl transition-all duration-300 transform hover:-translate-y-1">
            <div class="flex items-center">
                <div class="p-3 bg-gradient-to-br from-red-100 to-red-50 rounded-xl">
                    <i class="fas fa-times-circle text-2xl text-red-600"></i>
                </div>
                <div class="ml-4">
                    <h4 class="text-gray-500 font-medium">ปฏิเสธ</h4>
                    <p class="text-2xl font-semibold text-gray-800">
                        <?php echo number_format($payment_status_rejected); ?>
                    </p>
                </div>
            </div>
        </div>

        <!-- ค้างชำระ -->
        <div class="bg-white p-6 rounded-xl shadow-lg hover:shadow-xl transition-all duration-300 transform hover:-translate-y-1">
            <div class="flex items-center">
                <div class="p-3 bg-gradient-to-br from-gray-100 to-gray-50 rounded-xl">
                    <i class="fas fa-exclamation-triangle text-2xl text-orange-600"></i>
                </div>
                <div class="ml-4">
                    <h4 class="text-gray-500 font-medium">ค้างชำระ</h4>
                    <p class="text-2xl font-semibold text-gray-800">
                        <?php echo number_format($payment_status_arrears); ?>
                    </p>
                </div>
            </div>
        </div>
    </div>

    <!-- Summary Cards -->
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-5 gap-4 mb-8">
        <!-- จำนวนเงินรวมที่ชำระทั้งหมด -->
        <div class="bg-white p-6 rounded-xl shadow-lg hover:shadow-xl transition-all duration-300 transform hover:-translate-y-1">
            <div class="flex items-center">
                <div class="p-3 bg-gradient-to-br from-green-100 to-green-50 rounded-xl">
                    <i class="fas fa-money-bill-wave text-2xl text-green-600"></i>
                </div>
                <div class="ml-4">
                    <h4 class="text-gray-500 font-medium">ยอดชำระทั้งหมด</h4>
                    <p class="text-2xl font-semibold text-gray-800">
                        <?php echo number_format($total_paid_amount, 2); ?> บาท
                    </p>
                </div>
            </div>
        </div>

        <!-- ยอดชำระปีนี้ -->
        <div class="bg-white p-6 rounded-xl shadow-lg hover:shadow-xl transition-all duration-300 transform hover:-translate-y-1">
            <div class="flex items-center">
                <div class="p-3 bg-gradient-to-br from-yellow-100 to-yellow-50 rounded-xl">
                    <i class="fas fa-calendar-check text-2xl text-yellow-600"></i>
                </div>
                <div class="ml-4">
                    <h4 class="text-gray-500 font-medium">ยอดชำระปีนี้</h4>
                    <p class="text-2xl font-semibold text-gray-800">
                        <?php echo number_format($current_year_amount, 2); ?> บาท
                    </p>
                </div>
            </div>
        </div>

        <!-- ยอดชำระเดือนนี้ -->
        <div class="bg-white p-6 rounded-xl shadow-lg hover:shadow-xl transition-all duration-300 transform hover:-translate-y-1">
            <div class="flex items-center">
                <div class="p-3 bg-gradient-to-br from-blue-100 to-blue-50 rounded-xl">
                    <i class="fas fa-calendar-alt text-2xl text-blue-600"></i>
                </div>
                <div class="ml-4">
                    <h4 class="text-gray-500 font-medium">ยอดชำระเดือนนี้</h4>
                    <p class="text-2xl font-semibold text-gray-800">
                        <?php echo number_format($current_month_amount, 2); ?> บาท
                    </p>
                </div>
            </div>
        </div>

        <!-- จำนวนผู้ชำระภาษีทั้งหมด -->
        <div class="bg-white p-6 rounded-xl shadow-lg hover:shadow-xl transition-all duration-300 transform hover:-translate-y-1">
            <div class="flex items-center">
                <div class="p-3 bg-gradient-to-br from-purple-100 to-purple-50 rounded-xl">
                    <i class="fas fa-users text-2xl text-purple-600"></i>
                </div>
                <div class="ml-4">
                    <h4 class="text-gray-500 font-medium">ผู้ชำระภาษีทั้งหมด</h4>
                    <p class="text-2xl font-semibold text-gray-800">
                        <?php echo number_format($unique_taxpayers); ?> ราย
                    </p>
                </div>
            </div>
        </div>

        <!-- จำนวนเงินรวมที่ค้างชำระ -->
        <div class="bg-white p-6 rounded-xl shadow-lg hover:shadow-xl transition-all duration-300 transform hover:-translate-y-1">
            <div class="flex items-center">
                <div class="p-3 bg-gradient-to-br from-red-100 to-red-50 rounded-xl">
                    <i class="fas fa-exclamation-circle text-2xl text-red-600"></i>
                </div>
                <div class="ml-4">
                    <h4 class="text-gray-500 font-medium">ยอดค้างชำระ</h4>
                    <p class="text-2xl font-semibold text-gray-800">
                        <?php echo number_format($total_arrears_amount, 2); ?> บาท
                    </p>
                </div>
            </div>
        </div>
    </div>


    <div class="bg-white p-6 rounded-xl shadow-lg mb-8">
        <h3 class="text-xl font-semibold mb-4">สถิติการชำระภาษีรายเดือน</h3>
        <div id="monthlyChart" style="height: 400px;"></div>
    </div>

    <!-- กราฟวงกลมแสดงสัดส่วน -->
    <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-8">
        <!-- สัดส่วนประเภทภาษี -->
        <div class="bg-white p-6 rounded-xl shadow-lg">
            <h3 class="text-xl font-semibold mb-4">สัดส่วนประเภทภาษี</h3>
            <div class="d-flex justify-content-center">
                <div id="taxTypeChart" style="height: 300px; margin-left: 25%;"></div>
            </div>
        </div>

        <!-- สัดส่วนสถานะการชำระ -->
        <div class="bg-white p-6 rounded-xl shadow-lg">
            <h3 class="text-xl font-semibold mb-4">สัดส่วนสถานะการชำระ</h3>
            <div id="statusChart" style="height: 300px; margin-left: 25%;"></div>
        </div>
    </div>

    <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-8">
        <!-- เปรียบเทียบยอดชำระภาษี -->
        <div class="bg-white p-6 rounded-xl shadow-lg">
            <h3 class="text-xl font-semibold mb-4">เปรียบเทียบยอดชำระภาษีแต่ละประเภท</h3>
            <div id="taxCompareChart"></div>
        </div>

        <!-- จำนวนผู้ค้างชำระ -->
        <div class="bg-white p-6 rounded-xl shadow-lg">
            <h3 class="text-xl font-semibold mb-4">จำนวนผู้ค้างชำระแยกตามประเภท</h3>
            <div id="arrearsChart"></div>
        </div>
    </div>



</div>