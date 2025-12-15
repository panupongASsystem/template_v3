<div class="ml-72 p-8">
    <!-- Header -->
    <div class="flex justify-between items-center mb-8">
        <div>
            <h2 class="text-2xl font-semibold text-gray-800">จัดการระบบชำระภาษี</h2>
            <p class="text-gray-600">จัดการข้อมูลระบบชำระภาษีทั้งหมดในระบบ</p>
        </div>
        <div class="flex gap-4">
            <button onclick="openPaymentSettings()"
                class="flex items-center px-4 py-2 text-sm bg-blue-500 text-white rounded-lg hover:bg-blue-600">
                <i class="fas fa-qrcode mr-2"></i> ตั้งค่าการชำระเงิน
            </button>
            <!-- <button onclick="openArrearsModal()"
                class="flex items-center px-4 py-2 text-sm bg-green-500 text-white rounded-lg hover:bg-green-600">
                <i class="fa-solid fa-plus mr-2"></i> เพิ่มข้อมูลภาษีที่ต้องชำระ
            </button> -->
        </div>
    </div>
    <!-- Modal HTML Structure -->
    <div id="paymentSettingsModal" class="modal fade" tabindex="-1">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content bg-white rounded-xl shadow-2xl">
                <!-- Modal Header -->
                <div class="modal-header border-b border-gray-100 p-6">
                    <h3 class="text-xl font-semibold text-gray-800 flex items-center">
                        <i class="fas fa-cog mr-2 text-blue-500"></i>
                        ตั้งค่าการชำระเงิน
                    </h3>
                    <!-- <button type="button" class="close-modal">
                        <i class="fas fa-times text-gray-400 hover:text-gray-600 text-xl"></i>
                    </button> -->
                </div>

                <!-- Modal Body -->
                <div class="modal-body p-6">
                    <form id="paymentSettingsForm" class="space-y-6">
                        <!-- ธนาคาร -->
                        <div class="form-group">
                            <label class="block text-sm font-medium text-gray-700 mb-2">
                                <i class="fas fa-university mr-2 text-blue-500"></i>ธนาคาร
                            </label>
                            <select name="bank_name" class="form-select w-full rounded-lg border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                                <option value="">เลือกธนาคาร</option>
                                <option value="krungthai" <?php echo ($settings->bank_name == 'krungthai') ? 'selected' : ''; ?>>ธนาคารกรุงไทย</option>
                                <option value="kasikorn" <?php echo ($settings->bank_name == 'kasikorn') ? 'selected' : ''; ?>>ธนาคารกสิกรไทย</option>
                                <option value="bangkok" <?php echo ($settings->bank_name == 'bangkok') ? 'selected' : ''; ?>>ธนาคารกรุงเทพ</option>
                                <option value="scb" <?php echo ($settings->bank_name == 'scb') ? 'selected' : ''; ?>>ธนาคารไทยพาณิชย์</option>
                            </select>
                        </div>

                        <!-- ชื่อบัญชี -->
                        <div class="form-group">
                            <label class="block text-sm font-medium text-gray-700 mb-2">
                                <i class="fas fa-user mr-2 text-blue-500"></i>ชื่อบัญชี
                            </label>
                            <input type="text" name="account_name"
                                class="form-input w-full rounded-lg border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500"
                                placeholder="ระบุชื่อบัญชี"
                                value="<?php echo $settings->account_name ?? ''; ?>">
                        </div>

                        <!-- เลขที่บัญชี -->
                        <div class="form-group">
                            <label class="block text-sm font-medium text-gray-700 mb-2">
                                <i class="fas fa-credit-card mr-2 text-blue-500"></i>เลขที่บัญชี
                            </label>
                            <input type="text" name="account_number"
                                class="form-input w-full rounded-lg border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500"
                                placeholder="ระบุเลขที่บัญชี"
                                value="<?php echo $settings->account_number ?? ''; ?>">
                        </div>

                        <!-- QR Code -->
                        <div class="form-group">
                            <label class="block text-sm font-medium text-gray-700 mb-2">
                                <i class="fas fa-qrcode mr-2 text-blue-500"></i>รูป QR Code
                            </label>
                            <label for="qr_code_image" class="w-full cursor-pointer"> <!-- เพิ่ม label ครอบทั้งหมด -->
                                <div class="mt-1 flex justify-center px-6 pt-5 pb-6 border-2 border-gray-300 border-dashed rounded-lg hover:border-blue-500 transition-colors duration-200">
                                    <div class="space-y-1 text-center">
                                        <div id="qr_preview" class="mb-3">
                                            <img src="<?php echo base_url('docs/img/' . ($settings->qr_code_image ?? 'default_qr.png')); ?>"
                                                class="mx-auto h-32 w-32 object-cover rounded-lg shadow-sm">
                                        </div>
                                        <div class="flex justify-center text-sm text-gray-600">
                                            <span class="text-blue-600 hover:text-blue-500">อัพโหลดรูปภาพ</span>
                                        </div>
                                        <p class="text-xs text-gray-500">PNG, JPG, GIF ขนาดไม่เกิน 2MB</p>
                                        <input id="qr_code_image" name="qr_code_image" type="file" class="hidden" accept="image/*">
                                    </div>
                                </div>
                            </label>
                        </div>
                    </form>
                </div>
                <!-- Modal Footer -->
                <div class="modal-footer bg-gray-50 p-6 rounded-b-xl flex justify-end space-x-3">
                    <button type="button" class="cancel-btn px-4 py-2 rounded-lg text-gray-700 bg-gray-100 hover:bg-gray-200 focus:outline-none focus:ring-2 focus:ring-gray-400">
                        ยกเลิก
                    </button>
                    <button type="submit" form="paymentSettingsForm" class="save-btn px-4 py-2 rounded-lg text-white bg-blue-600 hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-blue-500">
                        <i class="fas fa-save mr-2"></i>บันทึก
                    </button>
                </div>
            </div>
        </div>
    </div>

    <!-- เพิ่ม Modal ค้างชำระ -->
    <div id="arrearsModal" class="modal fade" tabindex="-1">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content bg-white rounded-xl shadow-2xl">
                <div class="modal-header border-b border-gray-100 p-6">
                    <h3 class="text-xl font-semibold text-gray-800 flex items-center">
                        <i class="fa-solid fa-file-circle-plus mr-2 text-orange-500"></i>
                        เพิ่มข้อมูลภาษีที่ต้องชำระ
                    </h3>
                </div>

                <div class="modal-body p-6">
                    <!-- Tab Navigation -->
                    <div class="nav-tabs flex space-x-4 border-b border-gray-200 mb-4">
                        <button class="px-4 py-2 font-medium rounded-t-lg hover:bg-gray-100 focus:outline-none active"
                            data-tab="manual-input">
                            กรอกข้อมูล
                        </button>
                        <button class="px-4 py-2 font-medium rounded-t-lg hover:bg-gray-100 focus:outline-none"
                            data-tab="file-upload">
                            นำเข้าไฟล์ Excel
                        </button>
                    </div>

                    <div class="w-full">
                        <!-- Form กรอกข้อมูล -->
                        <div id="manual-input" class="tab-content w-full">
                            <form id="arrearsForm" action="<?php echo site_url('System_tax/add_arrears'); ?>" method="post" class="space-y-6">
                                <div class="form-group">
                                    <label class="block text-sm font-medium text-gray-700 mb-2">เลขประจำตัวประชาชน</label>
                                    <input type="text" name="citizen_id" maxlength="13" required
                                        class="form-input w-full rounded-lg border-gray-300" placeholder="เลขประจำตัวประชาชน 13 หลัก">
                                </div>

                                <div class="grid grid-cols-2 gap-4">
                                    <div class="form-group">
                                        <label class="block text-sm font-medium text-gray-700 mb-2">ชื่อ</label>
                                        <input type="text" name="firstname" required
                                            class="form-input w-full rounded-lg border-gray-300">
                                    </div>
                                    <div class="form-group">
                                        <label class="block text-sm font-medium text-gray-700 mb-2">นามสกุล</label>
                                        <input type="text" name="lastname" required
                                            class="form-input w-full rounded-lg border-gray-300">
                                    </div>
                                </div>

                                <div class="form-group">
                                    <label class="block text-sm font-medium text-gray-700 mb-2">ประเภทภาษี</label>
                                    <select name="tax_type" required class="form-select w-full rounded-lg border-gray-300">
                                        <option value="">เลือกประเภทภาษี</option>
                                        <option value="land">ภาษีที่ดินและสิ่งปลูกสร้าง</option>
                                        <option value="signboard">ภาษีป้าย</option>
                                        <option value="local">ภาษีท้องถิ่น</option>
                                    </select>
                                </div>

                                <div class="grid grid-cols-2 gap-4">
                                    <div class="form-group">
                                        <label class="block text-sm font-medium text-gray-700 mb-2">จำนวนเงิน</label>
                                        <input type="number" name="amount" required
                                            class="form-input w-full rounded-lg border-gray-300">
                                    </div>

                                    <div class="form-group">
                                        <label class="block text-sm font-medium text-gray-700 mb-2">ปีภาษี</label>
                                        <select name="tax_year" required class="form-select w-full rounded-lg border-gray-300">
                                            <?php
                                            $current_year = date('Y') + 543;
                                            for ($i = 0; $i < 5; $i++) {
                                                $year = $current_year - $i;
                                                echo "<option value='$year'>$year</option>";
                                            }
                                            ?>
                                        </select>
                                    </div>
                                </div>

                                <div class="form-group">
                                    <label class="block text-sm font-medium text-gray-700 mb-2">หมายเหตุ</label>
                                    <textarea name="admin_comment" rows="3"
                                        class="form-textarea w-full rounded-lg border-gray-300"
                                        placeholder="ระบุรายละเอียดเพิ่มเติม"></textarea>
                                </div>
                            </form>
                        </div>

                        <!-- Form อัพโหลดไฟล์ -->
                        <div id="file-upload" class="tab-content w-full hidden">
                            <form id="uploadForm" action="<?php echo site_url('System_tax/import_excel'); ?>" method="post" enctype="multipart/form-data" class="space-y-6">
                                <div class="form-group">
                                    <label class="block text-sm font-medium text-gray-700 mb-2">เลือกไฟล์ CSV</label>
                                    <input type="file" name="excel_file" accept=".csv" required
                                        class="block w-full text-sm text-gray-500 file:mr-4 file:py-2 file:px-4 file:rounded-md file:border-0 file:text-sm file:font-semibold file:bg-blue-50 file:text-blue-700 hover:file:bg-blue-100">
                                    <p class="text-sm text-gray-500 mt-1">รองรับไฟล์ .csv เท่านั้น (ขนาดไม่เกิน 20MB)</p>
                                </div>
                                <div class="mt-4">
                                    <a href="<?php echo base_url('docs/การเพิ่มไฟล์ข้อมูลภาษีแบบExcel.zip'); ?>" class="text-blue-600 hover:text-blue-800">
                                        <i class="fas fa-download mr-1"></i> ดาวน์โหลดไฟล์ตัวอย่าง
                                    </a>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>

                <div class="modal-footer bg-gray-50 p-6 rounded-b-xl flex justify-end space-x-3">
                    <button type="button" class="cancel-btn px-4 py-2 rounded-lg text-gray-700 bg-gray-100 hover:bg-gray-200" data-bs-dismiss="modal">
                        ยกเลิก
                    </button>
                    <button id="manualSubmitBtn" type="submit" form="arrearsForm"
                        class="px-4 py-2 rounded-lg text-white bg-green-500 hover:bg-green-600">
                        <i class="fas fa-save mr-2"></i>บันทึก
                    </button>
                    <button id="uploadSubmitBtn" type="submit" form="uploadForm"
                        class="px-4 py-2 rounded-lg text-white bg-green-500 hover:bg-green-600" style="display: none;">
                        <i class="fas fa-file-upload mr-2"></i>อัพโหลด
                    </button>
                </div>
            </div>
        </div>
    </div>

    <!-- Table -->
    <div class="bg-white rounded-lg shadow">
        <div class="p-6 border-b">
            <div class="flex justify-between items-center">
                <h3 class="text-lg font-semibold text-gray-800">รายการชำระภาษีทั้งหมด</h3>
                <form method="GET" action="<?php echo site_url('System_tax/main'); ?>" class="flex space-x-4">
                    <div class="relative">
                        <input type="text" name="search"
                            value="<?php echo $this->input->get('search'); ?>"
                            placeholder="ค้นหารายการ..."
                            class="pl-10 pr-4 py-2 border rounded-lg focus:outline-none focus:border-blue-500">
                        <button type="submit" class="absolute left-1 text-gray-400" style="margin-top: 10px; margin-left: 5px;">
                            <i class="fas fa-search"></i>
                        </button>
                    </div>
                </form>
            </div>
        </div>

        <div class="overflow-x-auto">
            <table class="w-full">
                <thead>
                    <tr class="bg-gray-50 text-left">
                        <th class="px-6 py-3 text-gray-600">ผู้ชำระภาษี</th>
                        <th class="px-6 py-3 text-gray-600">เลขประจำตัวประชาชน</th>
                        <th class="px-6 py-3 text-gray-600">ประเภทภาษี</th>
                        <th class="px-6 py-3 text-gray-600">วันครบกำหนดชำระ</th>
                        <th class="px-6 py-3 text-gray-600">จำนวนเงิน/วันที่ชำระ</th>
                        <th class="px-6 py-3 text-gray-600">สถานะ</th>
                        <th class="px-6 py-3 text-gray-600">หลักฐาน</th>
                        <th class="px-6 py-3 text-gray-600">จัดการ</th>
                        <th class="px-6 py-3 text-gray-600">หมายเหตุ</th>
                    </tr>
                </thead>
                <tbody class="divide-y">
                    <?php foreach ($payments as $payment) { ?>
                        <tr class="hover:bg-gray-50">
                            <td class="px-6 py-4">
                                <div class="text-gray-800">
                                    <?php echo $payment->firstname; ?> <?php echo $payment->lastname; ?>
                                </div>
                            </td>
                            <td class="px-6 py-4 text-gray-600">
                                <?php echo $payment->citizen_id; ?>
                            </td>
                            <td class="px-6 py-4">
                                <?php
                                switch ($payment->tax_type) {
                                    case 'land':
                                        echo 'ภาษีที่ดินและสิ่งปลูกสร้าง';
                                        break;
                                    case 'signboard':
                                        echo 'ภาษีป้าย';
                                        break;
                                    case 'local':
                                        echo 'ภาษีท้องถิ่น';
                                        break;
                                }
                                ?>
                            </td>
                            <!-- เพิ่มคอลัมน์วันครบกำหนดชำระ -->
                            <td class="px-6 py-4 text-gray-600">
                                <?php echo get_due_date($payment->tax_type, $payment->tax_year); ?>
                            </td>

                            <!-- รวมจำนวนเงินและวันที่ชำระ -->
                            <td class="px-6 py-4 text-gray-600">
                                <?php echo number_format($payment->amount, 2); ?> บาท
                                <?php if (!empty($payment->payment_date) && $payment->payment_date != '0000-00-00 00:00:00'): ?>
                                    <br>
                                    <small class="text-gray-500">
                                        ชำระเมื่อ: <?php echo date('d/m/Y', strtotime($payment->payment_date)); ?>
                                    </small>
                                <?php endif; ?>
                            </td>
                            <td class="px-6 py-4">
                                <?php
                                switch ($payment->payment_status) {
                                    case 'pending':
                                        $status_class = 'bg-blue-100 text-blue-800';
                                        $status_text = 'รอตรวจสอบ';
                                        break;
                                    case 'verified':
                                        $status_class = 'bg-green-100 text-green-800';
                                        $status_text = 'ตรวจสอบแล้ว';
                                        break;
                                    case 'rejected':
                                        $status_class = 'bg-red-100 text-red-800';
                                        $status_text = 'ไม่อนุมัติ';
                                        break;
                                    case 'arrears':
                                        $status_class = 'bg-gray-100 text-gray-800';
                                        $status_text = 'ค้างชำระ';
                                        break;
                                    case 'required':
                                        $status_class = 'bg-yellow-100 text-yellow-800';
                                        $status_text = 'ต้องชำระ';
                                        break;
                                }
                                ?>
                                <span class="px-2 py-1 <?php echo $status_class; ?> rounded-full text-sm">
                                    <?php echo $status_text; ?>
                                </span>
                            </td>
                            <td class="px-6 py-4">
                                <?php if ($payment->slip_file && $payment->slip_file != '0'): ?>
                                    <a href="<?php echo base_url('docs/img/' . $payment->slip_file); ?>" target="_blank" class="text-blue-600 hover:text-blue-800">
                                        <i class="fas fa-file-image"></i> ดูสลิป
                                    </a>
                                <?php else: ?>
                                    -
                                <?php endif; ?>
                            </td>
                            <td class="px-6 py-4">
                                <?php if ($payment->payment_status == 'pending'): ?>
                                    <a href="javascript:void(0);"
                                        onclick="confirmVerify(<?php echo $payment->id; ?>);"
                                        class="text-blue-600 hover:text-blue-800 mr-3">
                                        <i class="fas fa-check-circle"></i>
                                    </a>
                                    <a href="javascript:void(0);"
                                        onclick="confirmReject(<?php echo $payment->id; ?>);"
                                        class="text-red-600 hover:text-red-800">
                                        <i class="fas fa-times-circle"></i>
                                    </a>
                                <?php else: ?>
                                    <span class="text-gray-600">
                                        <?php
                                        if ($payment->m_fname && $payment->m_lname) {
                                            echo $payment->m_fname . ' ' . $payment->m_lname;
                                        } else {
                                            echo '-';
                                        }
                                        ?>
                                    </span>
                                <?php endif; ?>
                            </td>
                            <td class="px-6 py-4 text-gray-600">
                                <?php echo $payment->admin_comment; ?>
                            </td>
                        </tr>
                    <?php } ?>
                </tbody>
            </table>
        </div>

        <div class="p-6 border-t">
            <div class="flex justify-between items-center">
                <div class="text-gray-600">
                    แสดง <?php echo $start_row; ?>-<?php echo $end_row; ?> จาก <?php echo $total_rows; ?> รายการ
                </div>
                <?php echo $pagination; ?>
            </div>
        </div>
    </div>
</div>



<script>
    function confirmVerify(id) {
        Swal.fire({
            title: 'ยืนยันการอนุมัติ?',
            text: "คุณต้องการอนุมัติการชำระภาษีนี้ใช่หรือไม่",
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#3085d6',
            cancelButtonColor: '#d33',
            confirmButtonText: 'ใช่, อนุมัติ',
            cancelButtonText: 'ยกเลิก'
        }).then((result) => {
            if (result.isConfirmed) {
                window.location.href = '<?php echo site_url("System_tax/verify/"); ?>' + id;
            }
        });
    }

    function confirmReject(id) {
        Swal.fire({
            title: 'ระบุเหตุผลการไม่อนุมัติ',
            input: 'text',
            inputPlaceholder: 'กรุณาระบุเหตุผล...',
            showCancelButton: true,
            confirmButtonColor: '#d33',
            cancelButtonColor: '#3085d6',
            confirmButtonText: 'ไม่อนุมัติ',
            cancelButtonText: 'ยกเลิก'
        }).then((result) => {
            if (result.isConfirmed) {
                window.location.href = `<?php echo site_url("System_tax/reject/"); ?>${id}?comment=${encodeURIComponent(result.value)}`;
            }
        });
    }
</script>