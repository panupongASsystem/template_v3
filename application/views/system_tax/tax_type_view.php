<div class="ml-72 p-8">
    <div class="flex justify-between items-center mb-8">
        <div>
            <h2 class="text-2xl font-semibold text-gray-800"><?= $title ?></h2>
            <p class="text-gray-600">จัดการข้อมูล<?= $title ?>ทั้งหมดในระบบ</p>
        </div>
        <div class="flex gap-4">
            <?php if ($tax_type == 'land'): ?>
                <a href="<?php echo site_url('System_tax/lan_tax_penalty_settings'); ?>"
                    class="flex items-center px-4 py-2 text-sm bg-blue-500 text-white rounded-lg hover:bg-blue-600">
                    <i class="fa-solid fa-gear mr-2"></i> ตั้งค่าการปรับ<?= $title ?>
                </a>
            <?php elseif ($tax_type == 'local'): ?>
                <a href="<?php echo site_url('System_tax/local_tax_penalty_settings'); ?>"
                    class="flex items-center px-4 py-2 text-sm bg-blue-500 text-white rounded-lg hover:bg-blue-600">
                    <i class="fa-solid fa-gear mr-2"></i> ตั้งค่าการปรับ<?= $title ?>
                </a>
            <?php elseif ($tax_type == 'signboard'): ?>
                <a href="<?php echo site_url('System_tax/signboard_tax_penalty_settings'); ?>"
                    class="flex items-center px-4 py-2 text-sm bg-blue-500 text-white rounded-lg hover:bg-blue-600">
                    <i class="fa-solid fa-gear mr-2"></i> ตั้งค่าการปรับ<?= $title ?>
                </a>
            <?php endif; ?>
            <?php if ($tax_type == 'signboard'): ?>
                <button onclick="openSignboardModal()"
                    class="flex items-center px-4 py-2 text-sm bg-green-500 text-white rounded-lg hover:bg-green-600">
                    <i class="fas fa-plus mr-2"></i>เพิ่มข้อมูลภาษีป้าย
                </button>
            <?php else: ?>
                <button onclick="openArrearsModal()"
                    class="flex items-center px-4 py-2 text-sm bg-green-500 text-white rounded-lg hover:bg-green-600">
                    <i class="fas fa-plus mr-2"></i>เพิ่มข้อมูล<?= $title ?>
                </button>
            <?php endif; ?>
        </div>
    </div>

    <!-- Modal สำหรับภาษีป้าย -->
    <div id="signboardModal" class="modal fade" tabindex="-1">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content bg-white rounded-xl shadow-2xl">
                <div class="modal-header border-b border-gray-100 p-6">
                    <h3 class="text-xl font-semibold text-gray-800 flex items-center">
                        <i class="fa-solid fa-file-circle-plus mr-2 text-orange-500"></i>
                        เพิ่มข้อมูลภาษีป้าย
                    </h3>
                </div>

                <div class="modal-body p-6">
                    <form id="signboardForm" action="<?php echo site_url('System_tax/add_signboard'); ?>" method="post" enctype="multipart/form-data" class="space-y-6">
                        <!-- ข้อมูลเจ้าของป้าย -->
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

                        <input type="hidden" name="tax_type" value="signboard">

                        <!-- ข้อมูลป้าย -->
                        <!-- ส่วนรายละเอียดป้าย -->
                        <div class="space-y-4">
                            <div class="flex justify-between items-center">
                                <h4 class="font-medium text-gray-700">รายการป้าย</h4>
                                <button type="button" onclick="addSignboardItem()"
                                    class="text-sm text-blue-600 hover:text-blue-800">
                                    <i class="fas fa-plus"></i> เพิ่มป้าย
                                </button>
                            </div>

                            <!-- รายการป้ายที่เพิ่มแล้ว -->
                            <div id="signboardList" class="space-y-4"></div>

                            <!-- แบบฟอร์มเพิ่มป้าย -->
                            <div class="bg-gray-50 p-4 rounded-lg space-y-4">
                                <h5 class="font-medium text-gray-700">เพิ่มรายการป้าย</h5>
                                <div class="grid grid-cols-2 gap-4">
                                    <div>
                                        <label class="block text-sm font-medium text-gray-700 mb-2">พื้นที่ (ตร.ม.)</label>
                                        <input type="number" id="newArea" step="0.01"
                                            class="form-input w-full rounded-lg border-gray-300">
                                    </div>
                                    <div>
                                        <label class="block text-sm font-medium text-gray-700 mb-2">ราคา (บาท)</label>
                                        <input type="number" id="newAmount" step="0.01"
                                            class="form-input w-full rounded-lg border-gray-300">
                                    </div>
                                </div>

                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-2">รูปภาพป้าย</label>
                                    <input type="file" id="newImage" accept="image/*"
                                        class="form-input w-full rounded-lg border-gray-300">
                                </div>

                                <div class="text-right">
                                    <button type="button" onclick="addSignboardItem()"
                                        class="px-4 py-2 bg-blue-500 text-white rounded-lg hover:bg-blue-600">
                                        <i class="fas fa-plus mr-2"></i>เพิ่มรายการ
                                    </button>
                                </div>
                            </div>

                            <input type="hidden" name="signboard_items" id="signboardItems">
                            <input type="hidden" name="amount" id="totalAmount" value="0">
                        </div>

                        <!-- ปีภาษี -->
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

                        <div class="form-group">
                            <label class="block text-sm font-medium text-gray-700 mb-2">หมายเหตุ</label>
                            <textarea name="admin_comment" rows="3"
                                class="form-textarea w-full rounded-lg border-gray-300"
                                placeholder="ระบุรายละเอียดเพิ่มเติม"></textarea>
                        </div>
                    </form>
                </div>

                <div class="modal-footer bg-gray-50 p-6 rounded-b-xl flex justify-end space-x-3">
                    <button type="button" class="cancel-btn px-4 py-2 rounded-lg text-gray-700 bg-gray-100 hover:bg-gray-200" data-bs-dismiss="modal">
                        ยกเลิก
                    </button>
                    <button type="submit" form="signboardForm"
                        class="px-4 py-2 rounded-lg text-white bg-green-500 hover:bg-green-600">
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
                                    <input type="hidden" name="tax_type" value="<?php echo $tax_type; ?>">
                                    <div class="form-control-static p-2 bg-gray-50 rounded-lg border border-gray-300">
                                        <?php
                                        switch ($tax_type) {
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
                                    </div>
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
                <h3 class="text-lg font-semibold text-gray-800">รายการชำระ<?= $title ?>ทั้งหมด</h3>
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
                        <th class="px-6 py-3 text-gray-600">วันครบกำหนดชำระ</th>
                        <!-- แก้ไขและเพิ่ม column ใหม่ -->
                        <th class="px-6 py-3 text-gray-600">ยอดภาษี</th>
                        <th class="px-6 py-3 text-gray-600">ค่าปรับ</th>
                        <th class="px-6 py-3 text-gray-600">ยอดรวม</th>
                        <th class="px-6 py-3 text-gray-600">วันที่ชำระ</th>
                        <th class="px-6 py-3 text-gray-600">สถานะ</th>
                        <th class="px-6 py-3 text-gray-600">หลักฐาน</th>
                        <th class="px-6 py-3 text-gray-600">จัดการ</th>
                        <!-- <th class="px-6 py-3 text-gray-600">จัดการโดย</th> -->
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
                            <!-- <td class="px-6 py-4">
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
                            </td> -->
                            <!-- เพิ่มคอลัมน์วันครบกำหนดชำระ -->
                            <td class="px-6 py-4 text-gray-600">
                                <?php echo get_due_date($payment->tax_type, $payment->tax_year); ?>
                            </td>
                            <td class="px-6 py-4">
                                <div class="space-y-1">
                                    <!-- ยอดภาษี -->
                                    <div class="text-gray-800">
                                        <?php echo number_format($payment->amount, 2); ?> บาท
                                    </div>
                                </div>
                            </td>
                            <td class="px-6 py-4">
                                <div class="space-y-1">
                                    <!-- ค่าปรับ -->
                                    <?php if ($payment->penalty_amount > 0): ?>
                                        <div class="text-red-600">
                                            <?php echo number_format($payment->penalty_amount, 2); ?> บาท
                                            <i class="fas fa-info-circle ml-1 cursor-help"
                                                title="คำนวณ ณ <?php echo date('d/m/Y'); ?>"></i>
                                        </div>
                                    <?php else: ?>
                                        <div class="text-gray-500">-</div>
                                    <?php endif; ?>
                                </div>
                            </td>
                            <td class="px-6 py-4">
                                <div class="space-y-1">
                                    <!-- ยอดรวม -->
                                    <?php if ($payment->total_amount > 0): ?>
                                        <div class="font-semibold text-blue-600">
                                            <?php echo number_format($payment->total_amount, 2); ?> บาท
                                        </div>
                                    <?php else: ?>
                                        <div class="text-gray-500">-</div>
                                    <?php endif; ?>
                                </div>
                            </td>
                            <td class="px-6 py-4">
                                <?php if (!empty($payment->payment_date) && $payment->payment_date != '0000-00-00 00:00:00'): ?>
                                    <div class="text-gray-600">
                                        <?php echo thai_date($payment->payment_date); ?>
                                    </div>
                                <?php else: ?>
                                    <div class="text-gray-500">-</div>
                                <?php endif; ?>
                            </td>

                            <!-- รวมจำนวนเงินและวันที่ชำระ -->
                            <!-- <td class="px-6 py-4 text-gray-600">
                                <?php echo number_format($payment->amount, 2); ?> บาท
                                <?php if (!empty($payment->payment_date) && $payment->payment_date != '0000-00-00 00:00:00'): ?>
                                    <br>
                                    <small class="text-gray-500">
                                        ชำระเมื่อ: <?php echo date('d/m/Y', strtotime($payment->payment_date)); ?>
                                    </small>
                                <?php endif; ?>
                            </td> -->
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
                                        class="text-red-600 hover:text-red-800 mr-3">
                                        <i class="fas fa-times-circle"></i>
                                    </a>
                                <?php endif; ?><br>

                                <!-- เพิ่มปุ่ม Info -->
                                <a href="javascript:void(0);"
                                    onclick="viewInfo(<?php echo htmlspecialchars(json_encode($payment)); ?>)"
                                    class="text-blue-600 hover:text-blue-800 mr-3"
                                    title="ดูข้อมูล">
                                    <i class="fas fa-info-circle"></i>
                                </a>

                                <!-- เพิ่มปุ่มแก้ไข -->
                                <?php if ($payment->payment_status != 'verified'): ?>
                                    <a href="javascript:void(0);"
                                        onclick="editPayment(<?php echo htmlspecialchars(json_encode($payment)); ?>)"
                                        class="text-yellow-600 hover:text-yellow-800"
                                        title="แก้ไข">
                                        <i class="fas fa-edit"></i>
                                    </a>
                                <?php endif; ?><br>

                                <small class="text-gray-500">
                                    โดย: <?php
                                            if ($payment->m_fname && $payment->m_lname) {
                                                echo $payment->m_fname . ' ' . $payment->m_lname;
                                            } else {
                                                echo '-';
                                            }
                                            ?>
                                </small>
                            </td>
                            <!-- <td class="px-6 py-4 text-gray-600">
                                <span class="text-gray-600">
                                    <?php
                                    if ($payment->m_fname && $payment->m_lname) {
                                        echo $payment->m_fname . ' ' . $payment->m_lname;
                                    } else {
                                        echo '-';
                                    }
                                    ?>
                                </span>
                            </td> -->
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

<!-- Modal แสดงข้อมูล -->
<div id="infoModal" class="modal fade" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content bg-white rounded-xl shadow-2xl">
            <div class="modal-header border-b border-gray-100 p-6">
                <h3 class="text-xl font-semibold text-gray-800 flex items-center">
                    <i class="fas fa-info-circle mr-2 text-blue-500"></i>
                    รายละเอียดข้อมูลภาษี
                </h3>
            </div>
            <div class="modal-body p-6">
                <div class="space-y-4" id="infoContent">
                    <!-- ข้อมูลจะถูกเพิ่มด้วย JavaScript -->
                </div>
            </div>
            <div class="modal-footer bg-gray-50 p-6 rounded-b-xl">
                <button type="button" class="px-4 py-2 bg-gray-100 text-gray-700 rounded-lg hover:bg-gray-200" data-bs-dismiss="modal">
                    ปิด
                </button>
            </div>
        </div>
    </div>
</div>

<!-- Modal แก้ไขข้อมูล -->
<div id="editModal" class="modal fade" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content bg-white rounded-xl shadow-2xl">
            <div class="modal-header border-b border-gray-100 p-6">
                <h3 class="text-xl font-semibold text-gray-800 flex items-center">
                    <i class="fas fa-edit mr-2 text-yellow-500"></i>
                    แก้ไขข้อมูลภาษี
                </h3>
            </div>
            <div class="modal-body p-6">
                <form id="editForm" action="<?php echo site_url('System_tax/update_payment'); ?>" method="post" enctype="multipart/form-data" class="space-y-6">
                    <input type="hidden" name="payment_id" id="edit_payment_id">
                    <input type="hidden" name="tax_type" id="edit_tax_type">

                    <!-- ข้อมูลผู้เสียภาษี -->
                    <div class="grid grid-cols-2 gap-4">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">เลขประจำตัวประชาชน</label>
                            <input type="text" name="citizen_id" id="edit_citizen_id" maxlength="13" required
                                class="form-input w-full rounded-lg border-gray-300">
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">จำนวนเงิน</label>
                            <input type="number" name="amount" id="edit_amount" required
                                class="form-input w-full rounded-lg border-gray-300">
                        </div>
                    </div>

                    <div class="grid grid-cols-2 gap-4">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">ชื่อ</label>
                            <input type="text" name="firstname" id="edit_firstname" required
                                class="form-input w-full rounded-lg border-gray-300">
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">นามสกุล</label>
                            <input type="text" name="lastname" id="edit_lastname" required
                                class="form-input w-full rounded-lg border-gray-300">
                        </div>
                    </div>

                    <div class="grid grid-cols-2 gap-4">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">ปีภาษี</label>
                            <select name="tax_year" id="edit_tax_year" required class="form-select w-full rounded-lg border-gray-300">
                                <?php
                                $current_year = date('Y') + 543;
                                for ($i = 0; $i < 5; $i++) {
                                    $year = $current_year - $i;
                                    echo "<option value='$year'>$year</option>";
                                }
                                ?>
                            </select>
                        </div>
                        <!-- ถ้าต้องการเพิ่มฟิลด์อื่นๆ ในแถวเดียวกัน สามารถใส่ตรงนี้ -->
                    </div>

                    <!-- ส่วนแก้ไขรายละเอียดป้าย -->
                    <div id="editSignboardDetails" class="space-y-4" style="display: none;">
                        <h4 class="font-medium text-gray-700">รายการป้าย</h4>
                        <div id="editSignboardList" class="space-y-4">
                            <!-- รายการป้ายจะถูกเพิ่มด้วย JavaScript -->
                        </div>
                        <!-- ปุ่มเพิ่มป้ายใหม่ -->
                        <button type="button" onclick="addNewSignboardItem()"
                            class="w-full px-4 py-2 text-sm text-blue-600 bg-blue-50 rounded-lg hover:bg-blue-100">
                            <i class="fas fa-plus mr-2"></i>เพิ่มป้ายใหม่
                        </button>
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">หมายเหตุ</label>
                        <textarea name="admin_comment" id="edit_admin_comment" rows="3"
                            class="form-textarea w-full rounded-lg border-gray-300"></textarea>
                    </div>
                </form>
            </div>
            <div class="modal-footer bg-gray-50 p-6 rounded-b-xl flex justify-end space-x-3">
                <button type="button" class="px-4 py-2 bg-gray-100 text-gray-700 rounded-lg hover:bg-gray-200" data-bs-dismiss="modal">
                    ยกเลิก
                </button>
                <button type="submit" form="editForm" class="px-4 py-2 bg-yellow-500 text-white rounded-lg hover:bg-yellow-600">
                    <i class="fas fa-save mr-2"></i>บันทึก
                </button>
            </div>
        </div>
    </div>
</div>

<script>
    function removeEditSignboardItem(signboardId) {
        Swal.fire({
            title: 'ยืนยันการลบ?',
            text: "คุณต้องการลบรายการป้ายนี้ใช่หรือไม่",
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#d33',
            cancelButtonColor: '#3085d6',
            confirmButtonText: 'ใช่, ลบรายการ',
            cancelButtonText: 'ยกเลิก'
        }).then((result) => {
            if (result.isConfirmed) {
                // ส่ง request ไปลบข้อมูล
                fetch(`<?php echo site_url("System_tax/delete_signboard_detail/"); ?>${signboardId}`)
                    .then(response => response.json())
                    .then(data => {
                        if (data.status === 'success') {
                            // ลบ element ออกจาก DOM
                            const item = document.querySelector(`input[value="${signboardId}"]`).closest('.bg-white');
                            item.remove();

                            // แสดง alert success
                            Swal.fire({
                                icon: 'success',
                                title: 'ลบรายการสำเร็จ',
                                showConfirmButton: false,
                                timer: 1500
                            });
                        } else {
                            Swal.fire({
                                icon: 'error',
                                title: 'เกิดข้อผิดพลาด',
                                text: 'ไม่สามารถลบรายการได้'
                            });
                        }
                    });
            }
        });
    }

    function addNewSignboardItem() {
        const container = document.getElementById('editSignboardList');
        const index = container.children.length;

        const div = document.createElement('div');
        div.className = 'bg-white p-4 rounded-lg shadow-sm border border-gray-200 mb-4';
        div.innerHTML = `
        <div class="flex justify-between items-center">
            <div class="flex-1">
                <div class="font-medium">ป้ายใหม่</div>
                <div class="grid grid-cols-2 gap-4 mt-2">
                    <div>
                        <label class="text-sm text-gray-600">พื้นที่ (ตร.ม.)</label>
                        <input type="number" name="new_signboard[${index}][area]" 
                            step="0.01" required
                            class="form-input w-full rounded-lg border-gray-300">
                    </div>
                    <div>
                        <label class="text-sm text-gray-600">ราคา (บาท)</label>
                        <input type="number" name="new_signboard[${index}][amount]" 
                            step="0.01" required
                            class="form-input w-full rounded-lg border-gray-300">
                    </div>
                </div>
                <div class="mt-2">
                    <label class="text-sm text-gray-600">รูปภาพป้าย</label>
                    <input type="file" name="new_signboard_${index}_image" 
                        class="form-input w-full" accept="image/*" required
                        onchange="previewNewImage(this, ${index})">
                    <div id="preview_new_${index}" class="mt-2">
                        <img src="" class="w-24 h-24 object-cover rounded-lg border hidden">
                    </div>
                </div>
            </div>
            <button type="button" onclick="this.parentElement.parentElement.remove()"
                    class="text-red-600 hover:text-red-800 ml-4">
                <i class="fas fa-trash"></i>
            </button>
        </div>
    `;
        container.appendChild(div);
    }

    // เพิ่มฟังก์ชันพรีวิวรูปสำหรับป้ายใหม่
    function previewNewImage(input, index) {
        if (input.files && input.files[0]) {
            const reader = new FileReader();
            reader.onload = function(e) {
                const previewDiv = document.getElementById(`preview_new_${index}`);
                const img = previewDiv.querySelector('img');
                img.src = e.target.result;
                img.classList.remove('hidden');
            }
            reader.readAsDataURL(input.files[0]);
        }
    }

    function viewInfo(payment) {
        // ถ้าเป็นภาษีป้าย ให้ดึงข้อมูลเพิ่มเติม
        if (payment.tax_type === 'signboard') {
            fetch(`<?php echo site_url("System_tax/get_signboard_details/"); ?>${payment.id}`)
                .then(response => response.json())
                .then(data => {
                    showSignboardInfo(payment, data.details);
                });
        } else {
            showNormalInfo(payment);
        }
    }

    function showSignboardInfo(payment, signboardDetails) {
        let baseContent = getBaseInfoContent(payment);
        let signboardContent = '';

        if (signboardDetails && signboardDetails.length > 0) {
            signboardContent = `
            <div class="mt-6 border-t pt-6">
                <h4 class="text-lg font-medium text-gray-800 mb-4">รายการป้าย</h4>
                <div class="space-y-4">
                    ${signboardDetails.map((item, index) => `
                        <div class="bg-white p-4 rounded-lg shadow-sm border border-gray-200">
                            <div class="flex items-center gap-4">
                                <div class="image-preview">
                                    <a href="<?php echo base_url('docs/img/'); ?>${item.image}" 
                                       class="signboard-gallery"
                                       data-fancybox="images"
                                       data-caption="ป้ายที่ ${index + 1} - พื้นที่: ${parseFloat(item.area).toFixed(2)} ตร.ม.">
                                        <img src="<?php echo base_url('docs/img/'); ?>${item.image}" 
                                             class="w-16 h-16 object-cover rounded-lg border hover:opacity-75 transition-opacity cursor-zoom-in">
                                    </a>
                                </div>
                                <div>
                                    <div class="font-medium">ป้ายที่ ${index + 1}</div>
                                    <div class="text-sm text-gray-600">พื้นที่: ${parseFloat(item.area).toFixed(2)} ตร.ม.</div>
                                    <div class="text-sm text-gray-600">ราคา: ${parseFloat(item.amount).toLocaleString()} บาท</div>
                                </div>
                            </div>
                        </div>
                    `).join('')}
                </div>
            </div>`;
        }

        document.getElementById('infoContent').innerHTML = baseContent + signboardContent;

        // Initialize Fancybox
        $('[data-fancybox="images"]').fancybox({
            buttons: [
                "zoom",
                "slideShow",
                "fullScreen",
                "download",
                "thumbs",
                "close"
            ],
            loop: true,
            protect: true,
            modal: false,
            animationEffect: "zoom",
            transitionEffect: "fade"
        });

        new bootstrap.Modal(document.getElementById('infoModal')).show();
    }

    function showNormalInfo(payment) {
        document.getElementById('infoContent').innerHTML = getBaseInfoContent(payment);
        new bootstrap.Modal(document.getElementById('infoModal')).show();
    }

    function getBaseInfoContent(payment) {
        return `
        <div class="bg-gray-50 p-4 rounded-lg">
            <div class="grid grid-cols-2 gap-4">
                <div>
                    <p class="text-sm font-medium text-gray-600">เลขประจำตัวประชาชน</p>
                    <p class="text-gray-900">${payment.citizen_id}</p>
                </div>
                <div>
                    <p class="text-sm font-medium text-gray-600">ชื่อ-นามสกุล</p>
                    <p class="text-gray-900">${payment.firstname} ${payment.lastname}</p>
                </div>
                <div>
                    <p class="text-sm font-medium text-gray-600">จำนวนเงิน</p>
                    <p class="text-gray-900">${parseFloat(payment.amount).toLocaleString()} บาท</p>
                </div>
                <div>
                    <p class="text-sm font-medium text-gray-600">สถานะ</p>
                    <p class="text-gray-900">${getStatusText(payment.payment_status)}</p>
                </div>
            </div>
            <div class="mt-4">
                <p class="text-sm font-medium text-gray-600">หมายเหตุ</p>
                <p class="text-gray-900">${payment.admin_comment || '-'}</p>
            </div>
        </div>
    `;
    }

    function editPayment(payment) {
        // Fill form with payment data
        document.getElementById('edit_payment_id').value = payment.id;
        document.getElementById('edit_tax_type').value = payment.tax_type;
        document.getElementById('edit_citizen_id').value = payment.citizen_id;
        document.getElementById('edit_firstname').value = payment.firstname;
        document.getElementById('edit_lastname').value = payment.lastname;
        document.getElementById('edit_admin_comment').value = payment.admin_comment;
        document.getElementById('edit_tax_year').value = payment.tax_year;
        
        // จัดการ input จำนวนเงิน
        const amountInput = document.getElementById('edit_amount');

        if (payment.tax_type === 'signboard') {
            // ถ้าเป็นภาษีป้าย ให้แสดงแต่แก้ไขไม่ได้
            amountInput.value = payment.amount;
            amountInput.readOnly = true;
            amountInput.classList.add('bg-gray-100');
            // เพิ่ม tooltip หรือข้อความอธิบาย
            amountInput.title = 'จำนวนเงินจะคำนวณจากรายการป้ายโดยอัตโนมัติ';

            // แสดงส่วนรายละเอียดป้าย
            document.getElementById('editSignboardDetails').style.display = 'block';
            // โหลดข้อมูลรายละเอียดป้าย
            fetch(`<?php echo site_url("System_tax/get_signboard_details/"); ?>${payment.id}`)
                .then(response => response.json())
                .then(data => {
                    if (data.status === 'success') {
                        displayEditSignboardDetails(data.details);
                    }
                });
        } else {
            // ถ้าไม่ใช่ภาษีป้าย ให้แก้ไขได้
            amountInput.value = payment.amount;
            amountInput.readOnly = false;
            amountInput.classList.remove('bg-gray-100');
            amountInput.title = '';

            // ซ่อนส่วนรายละเอียดป้าย
            document.getElementById('editSignboardDetails').style.display = 'none';
        }

        // Show modal
        new bootstrap.Modal(document.getElementById('editModal')).show();
    }

    // เพิ่มฟังก์ชันใหม่สำหรับแสดงรายละเอียดป้ายในโหมดแก้ไข
    function displayEditSignboardDetails(details) {
        const container = document.getElementById('editSignboardList');
        container.innerHTML = '';

        details.forEach((item, index) => {
            const div = document.createElement('div');
            div.className = 'bg-white p-4 rounded-lg shadow-sm border border-gray-200 mb-4';
            div.innerHTML = `
            <div class="flex justify-between items-center">
                <div class="flex items-center gap-4 w-full">
                    <div class="image-preview">
                        <a href="<?php echo base_url('docs/img/'); ?>${item.image}" 
                           class="signboard-gallery"
                           data-fancybox="edit-images"
                           data-caption="ป้ายที่ ${index + 1} - พื้นที่: ${parseFloat(item.area).toFixed(2)} ตร.ม.">
                            <img src="<?php echo base_url('docs/img/'); ?>${item.image}" 
                                 class="w-16 h-16 object-cover rounded-lg border hover:opacity-75 transition-opacity cursor-zoom-in">
                        </a>
                    </div>
                    <div class="flex-1">
                        <div class="flex justify-between">
                            <div class="font-medium">ป้ายที่ ${index + 1}</div>
                            <button type="button" onclick="removeEditSignboardItem(${item.id})"
                                    class="text-red-600 hover:text-red-800">
                                <i class="fas fa-trash"></i>
                            </button>
                        </div>
                        <div class="grid grid-cols-2 gap-4 mt-2">
                            <div>
                                <label class="text-sm text-gray-600">พื้นที่ (ตร.ม.)</label>
                                <input type="number" name="signboard[${index}][area]" 
                                    value="${item.area}" step="0.01" 
                                    class="form-input w-full rounded-lg border-gray-300">
                            </div>
                            <div>
                                <label class="text-sm text-gray-600">ราคา (บาท)</label>
                                <input type="number" name="signboard[${index}][amount]" 
                                    value="${item.amount}" step="0.01"
                                    class="form-input w-full rounded-lg border-gray-300">
                            </div>
                        </div>
                        <input type="hidden" name="signboard[${index}][id]" value="${item.id}">
                        <input type="hidden" name="signboard[${index}][image]" value="${item.image}">
                        <div class="mt-2">
                            <label class="text-sm text-gray-600">เปลี่ยนรูปภาพ (ถ้าต้องการ)</label>
                            <input type="file" name="signboard_${index}_new_image" 
                                class="form-input w-full" accept="image/*">
                        </div>
                    </div>
                </div>
            </div>`;
            container.appendChild(div);
        });

        // Initialize Fancybox for edit mode
        $('[data-fancybox="edit-images"]').fancybox({
            buttons: [
                "zoom",
                "slideShow",
                "fullScreen",
                "download",
                "thumbs",
                "close"
            ],
            loop: true,
            protect: true,
            animationEffect: "zoom",
            transitionEffect: "fade",
            clickContent: function(current, event) {
                return current.type === "image" ? "zoom" : false;
            }
        });
    }

    function getStatusText(status) {
        const statusMap = {
            'pending': 'รอตรวจสอบ',
            'verified': 'ตรวจสอบแล้ว',
            'rejected': 'ไม่อนุมัติ',
            'arrears': 'ค้างชำระ',
            'required': 'ต้องชำระ'
        };
        return statusMap[status] || status;
    }

    // Handle edit form submission
    // document.getElementById('editForm').addEventListener('submit', function(e) {
    //     e.preventDefault();
    //     const formData = new FormData(this);

    //     fetch('<?php echo site_url("System_tax/update_payment"); ?>', {
    //             method: 'POST',
    //             body: formData
    //         })
    //         .then(response => response.json())
    //         .then(data => {
    //             if (data.status === 'success') {
    //                 Swal.fire({
    //                     icon: 'success',
    //                     title: 'บันทึกข้อมูลสำเร็จ',
    //                     text: 'ข้อมูลได้รับการอัพเดตแล้ว',
    //                     showConfirmButton: false,
    //                     timer: 1500
    //                 }).then(() => {
    //                     location.reload();
    //                 });
    //             } else {
    //                 Swal.fire({
    //                     icon: 'error',
    //                     title: 'เกิดข้อผิดพลาด',
    //                     text: data.message || 'ไม่สามารถบันทึกข้อมูลได้'
    //                 });
    //             }
    //         });
    // });

    function openSignboardModal() {
        $('#signboardModal').modal('show');
    }

    let signboardItems = [];

    function addSignboardItem() {
        const area = document.getElementById('newArea').value;
        const amount = document.getElementById('newAmount').value;
        const imageInput = document.getElementById('newImage');
        const file = imageInput.files[0];

        if (!area || !amount || !file) {
            Swal.fire({
                icon: 'warning',
                title: 'กรุณากรอกข้อมูลให้ครบถ้วน',
                text: 'กรุณากรอกพื้นที่ ราคา และเลือกรูปภาพ'
            });
            return;
        }

        // สร้าง FormData สำหรับอัพโหลดรูป
        const formData = new FormData();
        formData.append('image', file);

        // อัพโหลดรูปก่อน
        $.ajax({
            url: '<?php echo site_url("System_tax/upload_signboard_image"); ?>',
            type: 'POST',
            data: formData,
            processData: false,
            contentType: false,
            success: function(response) {
                console.log('Upload response:', response); // ดูผลลัพธ์การอัพโหลด
                const result = JSON.parse(response);
                if (result.status === 'success') {
                    // เพิ่มรายการป้าย
                    signboardItems.push({
                        area: parseFloat(area),
                        amount: parseFloat(amount),
                        image: result.filename
                    });

                    console.log('Current signboardItems:', signboardItems); // ดูข้อมูลที่เก็บ

                    // อัพเดต input hidden
                    document.getElementById('signboardItems').value = JSON.stringify(signboardItems);
                    document.getElementById('totalAmount').value = signboardItems.reduce((total, item) => total + item.amount, 0);

                    // อัพเดตรายการในหน้าจอ
                    updateSignboardList();

                    // เคลียร์ฟอร์ม
                    document.getElementById('newArea').value = '';
                    document.getElementById('newAmount').value = '';
                    document.getElementById('newImage').value = '';
                }
            },
            error: function(xhr, status, error) {
                console.error('Upload error:', error); // ดูข้อผิดพลาด
                Swal.fire({
                    icon: 'error',
                    title: 'เกิดข้อผิดพลาด',
                    text: 'ไม่สามารถอัพโหลดรูปภาพได้'
                });
            }
        });
    }

    // เพิ่ม event listener สำหรับการ submit form
    document.getElementById('signboardForm').addEventListener('submit', function(e) {
        e.preventDefault();
        console.log('Form data:', {
            signboardItems: document.getElementById('signboardItems').value,
            totalAmount: document.getElementById('totalAmount').value
        });
        this.submit();
    });

    function removeSignboardItem(index) {
        signboardItems.splice(index, 1);
        updateSignboardList();
    }

    function updateSignboardList() {
        const container = document.getElementById('signboardList');
        container.innerHTML = '';

        let totalAmount = 0;

        signboardItems.forEach((item, index) => {
            const div = document.createElement('div');
            div.className = 'bg-white p-4 rounded-lg shadow-sm border border-gray-200';
            div.innerHTML = `
            <div class="flex justify-between items-center">
                <div class="flex items-center gap-4">
                    <img src="<?php echo base_url('docs/img/'); ?>${item.image}" 
                         class="w-16 h-16 object-cover rounded-lg border">
                    <div>
                        <div class="font-medium">ป้ายที่ ${index + 1}</div>
                        <div class="text-sm text-gray-600">พื้นที่: ${item.area.toFixed(2)} ตร.ม.</div>
                        <div class="text-sm text-gray-600">ราคา: ${item.amount.toLocaleString()} บาท</div>
                    </div>
                </div>
                <button type="button" onclick="removeSignboardItem(${index})"
                        class="text-red-600 hover:text-red-800 p-2">
                    <i class="fas fa-trash"></i>
                </button>
            </div>
        `;
            container.appendChild(div);

            totalAmount += item.amount;
        });

        // อัพเดตยอดรวมในฟอร์ม
        document.getElementById('totalAmount').value = totalAmount;
        // อัพเดต JSON string ของรายการทั้งหมด
        document.getElementById('signboardItems').value = JSON.stringify(signboardItems);

        // แสดงสรุปจำนวนรายการและยอดรวม
        if (signboardItems.length > 0) {
            const summaryDiv = document.createElement('div');
            summaryDiv.className = 'mt-4 p-4 bg-blue-50 rounded-lg';
            summaryDiv.innerHTML = `
            <div class="font-medium">สรุปรายการ</div>
            <div class="text-sm text-gray-600">จำนวนป้าย: ${signboardItems.length} รายการ</div>
            <div class="text-sm text-gray-600">ยอดรวมทั้งสิ้น: ${totalAmount.toLocaleString()} บาท</div>
        `;
            container.appendChild(summaryDiv);
        }
    }

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