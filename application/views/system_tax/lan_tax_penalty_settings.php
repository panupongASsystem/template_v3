<div class="ml-72 p-8">
    <div class="flex justify-between items-center mb-8">
        <div>
            <h2 class="text-2xl font-semibold text-gray-800">ตั้งค่าค่าปรับภาษีที่ดินและสิ่งปลูกสร้าง</h2>
            <p class="text-gray-600">กำหนดค่าปรับและเงินเพิ่มสำหรับการชำระภาษีล่าช้าหรือไม่ถูกต้อง</p>
        </div>
    </div>

    <?php if ($this->session->flashdata('save_success')): ?>
        <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded relative mb-6" role="alert">
            <strong class="font-bold">สำเร็จ!</strong>
            <span class="block sm:inline">บันทึกการตั้งค่าเรียบร้อยแล้ว</span>
        </div>
    <?php endif; ?>

    <?php if ($this->session->flashdata('save_error')): ?>
        <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded relative mb-6" role="alert">
            <strong class="font-bold">ผิดพลาด!</strong>
            <span class="block sm:inline">เกิดข้อผิดพลาดในการบันทึกการตั้งค่า</span>
        </div>
    <?php endif; ?>

    <div class="bg-white rounded-lg shadow p-6">
        <form action="<?php echo site_url('System_tax/update_lan_tax_penalty_settings'); ?>" method="post" class="space-y-6">
            <!-- 1. ค่าปรับไม่ยื่นแบบ -->
            <!-- <div class="p-6 bg-gray-50 rounded-lg">
                <h3 class="text-lg font-semibold text-gray-800 mb-4">
                    <i class="fas fa-exclamation-circle text-red-500 mr-2"></i>
                    ค่าปรับกรณีไม่ยื่นแบบแสดงรายการ
                </h3>
                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">
                            ค่าปรับ (บาท)
                        </label>
                        <div class="relative">
                            <input type="number" name="no_filing_fine"
                                value="<?php echo $settings->no_filing_fine; ?>"
                                class="form-input w-full rounded-lg border-gray-300" required>
                            <div class="absolute inset-y-0 right-0 flex items-center px-3 text-gray-500">บาท</div>
                        </div>
                    </div>
                    <div class="flex items-center text-gray-600">
                        <i class="fas fa-info-circle mr-2"></i>
                        <span class="text-sm">ค่าปรับกรณีไม่ยื่นแบบแสดงรายการภายในกำหนด ไม่เกิน <?php echo $settings->no_filing_fine; ?> บาท</span>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">
                            จำนวนปีที่สามารถเรียกเก็บย้อนหลัง
                        </label>
                        <div class="relative">
                            <input type="number" name="backdate_years_no_filing"
                                value="<?php echo $settings->backdate_years_no_filing; ?>"
                                class="form-input w-full rounded-lg border-gray-300" required>
                            <div class="absolute inset-y-0 right-0 flex items-center px-3 text-gray-500">ปี</div>
                        </div>
                    </div>
                    <div class="flex items-center text-gray-600">
                        <i class="fas fa-info-circle mr-2"></i>
                        <span class="text-sm">สามารถเรียกเก็บย้อนหลังได้ไม่เกิน <?php echo $settings->backdate_years_no_filing; ?> ปี</span>
                    </div>
                </div>
            </div> -->

            <!-- 2. ค่าปรับยื่นแบบไม่ถูกต้อง -->
            <!-- <div class="p-6 bg-gray-50 rounded-lg">
                <h3 class="text-lg font-semibold text-gray-800 mb-4">
                    <i class="fas fa-file-alt text-yellow-500 mr-2"></i>
                    ค่าปรับกรณียื่นแบบไม่ถูกต้อง
                </h3>
                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">
                            ค่าปรับ (บาท)
                        </label>
                        <div class="relative">
                            <input type="number" name="incorrect_filing_fine"
                                value="<?php echo $settings->incorrect_filing_fine; ?>"
                                class="form-input w-full rounded-lg border-gray-300" required>
                            <div class="absolute inset-y-0 right-0 flex items-center px-3 text-gray-500">บาท</div>
                        </div>
                    </div>
                    <div class="flex items-center text-gray-600">
                        <i class="fas fa-info-circle mr-2"></i>
                        <span class="text-sm">ค่าปรับกรณียื่นแบบไม่ถูกต้อง ไม่เกิน <?php echo $settings->incorrect_filing_fine; ?> บาท</span>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">
                            จำนวนปีที่สามารถเรียกเก็บย้อนหลัง
                        </label>
                        <div class="relative">
                            <input type="number" name="backdate_years_incorrect"
                                value="<?php echo $settings->backdate_years_incorrect; ?>"
                                class="form-input w-full rounded-lg border-gray-300" required>
                            <div class="absolute inset-y-0 right-0 flex items-center px-3 text-gray-500">ปี</div>
                        </div>
                    </div>
                    <div class="flex items-center text-gray-600">
                        <i class="fas fa-info-circle mr-2"></i>
                        <span class="text-sm">สามารถเรียกเก็บย้อนหลังได้ไม่เกิน <?php echo $settings->backdate_years_incorrect; ?> ปี</span>
                    </div>
                </div>
            </div> -->

            <!-- 3. เงินเพิ่มกรณีชำระเกินกำหนด -->
            <div class="p-6 bg-gray-50 rounded-lg">
                <h3 class="text-lg font-semibold text-gray-800 mb-4">
                    <i class="fas fa-clock text-orange-500 mr-2"></i>
                    เงินเพิ่มกรณีชำระเกินกำหนด
                </h3>
                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">
                            ไม่เกิน 1 เดือน
                        </label>
                        <div class="relative">
                            <input type="number" name="late_payment_1month"
                                value="<?php echo $settings->late_payment_1month; ?>"
                                class="form-input w-full rounded-lg border-gray-300"
                                min="0" max="100" step="0.01" required>
                            <div class="absolute inset-y-0 right-0 flex items-center px-3 text-gray-500">%</div>
                        </div>
                    </div>
                    <div class="flex items-center text-gray-600">
                        <i class="fas fa-info-circle mr-2"></i>
                        <span class="text-sm">เพิ่ม 2.5% ของค่าภาษี</span>
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">
                            เกิน 1 เดือนแต่ไม่เกิน 2 เดือน
                        </label>
                        <div class="relative">
                            <input type="number" name="late_payment_2month"
                                value="<?php echo $settings->late_payment_2month; ?>"
                                class="form-input w-full rounded-lg border-gray-300"
                                min="0" max="100" step="0.01" required>
                            <div class="absolute inset-y-0 right-0 flex items-center px-3 text-gray-500">%</div>
                        </div>
                    </div>
                    <div class="flex items-center text-gray-600">
                        <i class="fas fa-info-circle mr-2"></i>
                        <span class="text-sm">เพิ่ม 5% ของค่าภาษี</span>
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">
                            เกิน 2 เดือนแต่ไม่เกิน 3 เดือน
                        </label>
                        <div class="relative">
                            <input type="number" name="late_payment_3month"
                                value="<?php echo $settings->late_payment_3month; ?>"
                                class="form-input w-full rounded-lg border-gray-300"
                                min="0" max="100" step="0.01" required>
                            <div class="absolute inset-y-0 right-0 flex items-center px-3 text-gray-500">%</div>
                        </div>
                    </div>
                    <div class="flex items-center text-gray-600">
                        <i class="fas fa-info-circle mr-2"></i>
                        <span class="text-sm">เพิ่ม 7.5% ของค่าภาษี</span>
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">
                            เกิน 3 เดือนแต่ไม่เกิน 4 เดือน
                        </label>
                        <div class="relative">
                            <input type="number" name="late_payment_4month"
                                value="<?php echo $settings->late_payment_4month; ?>"
                                class="form-input w-full rounded-lg border-gray-300"
                                min="0" max="100" step="0.01" required>
                            <div class="absolute inset-y-0 right-0 flex items-center px-3 text-gray-500">%</div>
                        </div>
                    </div>
                    <div class="flex items-center text-gray-600">
                        <i class="fas fa-info-circle mr-2"></i>
                        <span class="text-sm">เพิ่ม 10% ของค่าภาษี</span>
                    </div>
                </div>

                <div class="mt-4 p-4 bg-red-50 rounded-lg">
                    <p class="text-sm text-red-600">
                        <i class="fas fa-exclamation-triangle mr-2"></i>
                        หมายเหตุ: เกิน 4 เดือนขึ้นไปให้ยึดหรือขายทอดตลาดทรัพย์สิน โดยมิต้องขอให้ศาลสั่งหรือออกหมายยึด
                    </p>
                </div>
            </div>

            <!-- ปุ่มบันทึก -->
            <div class="flex justify-between mt-8">
                <a href="<?php echo site_url('System_tax/land_tax'); ?>"
                    class="px-6 py-2 bg-gray-500 text-white rounded-lg hover:bg-gray-600 focus:outline-none focus:ring-2 focus:ring-gray-500">
                    <i class="fas fa-arrow-left mr-2"></i>ย้อนกลับ
                </a>
                <button type="submit" class="bg-blue-600 text-white px-6 py-2 rounded-lg hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-blue-500">
                    <i class="fas fa-save mr-2"></i>บันทึกการตั้งค่า
                </button>
            </div>
        </form>
    </div>

    <!-- ตารางประวัติการแก้ไข -->
    <div class="bg-white rounded-lg shadow p-6 mt-8">
        <h3 class="text-lg font-semibold text-gray-800 mb-4">ประวัติการแก้ไขการตั้งค่า</h3>
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200">
                <thead>
                    <tr>
                        <th class="px-6 py-3 bg-gray-50 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                            วันที่แก้ไข
                        </th>
                        <th class="px-6 py-3 bg-gray-50 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                            ผู้แก้ไข
                        </th>
                        <th class="px-6 py-3 bg-gray-50 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                            รายละเอียด
                        </th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    <tr>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                            <?php echo $settings->updated_at ? date('d/m/Y H:i:s', strtotime($settings->updated_at)) : '-'; ?>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                            <?php
                            if ($settings->updated_by) {
                                $updater = $this->db->where('m_id', $settings->updated_by)->get('tbl_member')->row();
                                echo $updater ? $updater->m_fname . ' ' . $updater->m_lname : '-';
                            } else {
                                echo '-';
                            }
                            ?>
                        </td>
                        <td class="px-6 py-4 text-sm text-gray-500">
                            <ul class="list-disc list-inside">
                                <li>ค่าปรับไม่ยื่นแบบ: <?php echo $settings->no_filing_fine; ?> บาท</li>
                                <li>ค่าปรับยื่นไม่ถูกต้อง: <?php echo $settings->incorrect_filing_fine; ?> บาท</li>
                                <li>ค่าปรับชำระล่าช้า:
                                    ไม่เกิน 1 เดือน <?php echo $settings->late_payment_1month; ?>%,
                                    ไม่เกิน 2 เดือน <?php echo $settings->late_payment_2month; ?>%,
                                    ไม่เกิน 3 เดือน <?php echo $settings->late_payment_3month; ?>%,
                                    ไม่เกิน 4 เดือน <?php echo $settings->late_payment_4month; ?>%
                                </li>
                            </ul>
                        </td>
                    </tr>
                </tbody>
            </table>
        </div>
    </div>
</div>

<style>
    .form-input::-webkit-outer-spin-button,
    .form-input::-webkit-inner-spin-button {
        -webkit-appearance: none;
        margin: 0;
    }

    .form-input[type=number] {
        -moz-appearance: textfield;
    }
</style>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        // แสดง tooltip สำหรับช่วยเหลือ
        const inputs = document.querySelectorAll('input[type="number"]');
        inputs.forEach(input => {
            input.addEventListener('input', function() {
                const max = parseFloat(this.getAttribute('max'));
                if (max && parseFloat(this.value) > max) {
                    this.value = max;
                }
            });
        });
    });
</script>