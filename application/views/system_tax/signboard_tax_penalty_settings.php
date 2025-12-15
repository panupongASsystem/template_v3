<div class="ml-72 p-8">
    <div class="flex justify-between items-center mb-8">
        <div>
            <h2 class="text-2xl font-semibold text-gray-800">ตั้งค่าค่าปรับภาษีป้าย</h2>
            <p class="text-gray-600">กำหนดอัตราโทษและค่าปรับสำหรับภาษีป้าย</p>
        </div>
    </div>

    <?php if ($this->session->flashdata('save_success')): ?>
        <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded relative mb-6" role="alert">
            <strong class="font-bold">สำเร็จ!</strong>
            <span class="block sm:inline">บันทึกการตั้งค่าเรียบร้อยแล้ว</span>
        </div>
    <?php endif; ?>

    <div class="bg-white rounded-lg shadow p-6">
        <form action="<?php echo site_url('system_tax/update_signboard_tax_penalty_settings'); ?>" method="post" class="space-y-6">
            <!-- 1. ค่าปรับไม่ยื่นแบบ -->
            <!-- <div class="p-6 bg-gray-50 rounded-lg">
                <h3 class="text-lg font-semibold text-gray-800 mb-4">
                    <i class="fas fa-exclamation-circle text-red-500 mr-2"></i>
                    กรณีไม่ยื่นแบบแสดงรายการภาษีป้ายภายในกำหนด
                </h3>
                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">
                            เงินเพิ่ม (% ของค่าภาษี)
                        </label>
                        <div class="relative">
                            <input type="number" name="no_filing_percent"
                                value="<?php echo $settings->no_filing_percent ?? 10; ?>"
                                class="form-input w-full rounded-lg border-gray-300"
                                step="0.01" min="0" max="100" required>
                            <div class="absolute inset-y-0 right-0 flex items-center px-3 text-gray-500">%</div>
                        </div>
                    </div>
                    <div class="flex items-center text-gray-600">
                        <i class="fas fa-info-circle mr-2"></i>
                        <span class="text-sm">ไม่ยื่นแบบภายในเดือนมีนาคมหรือหลังติดตั้งป้าย 15 วัน</span>
                    </div>
                </div>
            </div> -->

            <!-- 2. ค่าปรับยื่นแบบไม่ถูกต้อง -->
            <!-- <div class="p-6 bg-gray-50 rounded-lg">
                <h3 class="text-lg font-semibold text-gray-800 mb-4">
                    <i class="fas fa-file-alt text-yellow-500 mr-2"></i>
                    กรณียื่นแบบแสดงรายการไม่ถูกต้อง
                </h3>
                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">
                            เงินเพิ่ม (% ของค่าภาษีที่ประเมินเพิ่ม)
                        </label>
                        <div class="relative">
                            <input type="number" name="incorrect_filing_percent"
                                value="<?php echo $settings->incorrect_filing_percent ?? 10; ?>"
                                class="form-input w-full rounded-lg border-gray-300"
                                step="0.01" min="0" max="100" required>
                            <div class="absolute inset-y-0 right-0 flex items-center px-3 text-gray-500">%</div>
                        </div>
                    </div>
                    <div class="flex items-center text-gray-600">
                        <i class="fas fa-info-circle mr-2"></i>
                        <span class="text-sm">กรณียื่นแบบไม่ถูกต้องทำให้ค่าภาษีน้อยลง</span>
                    </div>
                </div>
            </div> -->

            <!-- 3. ค่าปรับชำระเกินกำหนด -->
            <div class="p-6 bg-gray-50 rounded-lg">
                <h3 class="text-lg font-semibold text-gray-800 mb-4">
                    <i class="fas fa-clock text-orange-500 mr-2"></i>
                    กรณีไม่ชำระภาษีภายใน 15 วัน
                </h3>
                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">
                            เงินเพิ่ม (% ต่อเดือน)
                        </label>
                        <div class="relative">
                            <input type="number" name="late_payment_monthly_percent"
                                value="<?php echo $settings->late_payment_monthly_percent; ?>"
                                class="form-input w-full rounded-lg border-gray-300"
                                step="0.01" min="0" max="100" required>
                            <div class="absolute inset-y-0 right-0 flex items-center px-3 text-gray-500">% ต่อเดือน</div>
                        </div>
                    </div>
                    <div class="flex items-center text-gray-600">
                        <i class="fas fa-info-circle mr-2"></i>
                        <span class="text-sm">เศษของเดือนให้นับเป็น 1 เดือน โดยนับตั้งแต่วันพ้นกำหนด 15 วัน</span>
                    </div>
                </div>
            </div>

            <!-- ปุ่มบันทึก -->
            <div class="flex justify-between mt-8">
                <a href="<?php echo site_url('System_tax/signboard_tax'); ?>"
                    class="px-6 py-2 bg-gray-500 text-white rounded-lg hover:bg-gray-600 focus:outline-none focus:ring-2 focus:ring-gray-500">
                    <i class="fas fa-arrow-left mr-2"></i>ย้อนกลับ
                </a>
                <button type="submit" class="px-6 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-blue-500">
                    <i class="fas fa-save mr-2"></i>บันทึกการตั้งค่า
                </button>
            </div>
        </form>
    </div>

    <!-- ประวัติการแก้ไข -->
    <div class="mt-8 bg-white rounded-lg shadow p-6">
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
                    <?php foreach ($settings_history as $history): ?>
                        <tr>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                <?php echo date('d/m/Y H:i:s', strtotime($history->updated_at)); ?>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                <?php echo $history->m_fname . ' ' . $history->m_lname; ?>
                            </td>
                            <td class="px-6 py-4 text-sm text-gray-500">
                                <ul class="list-disc list-inside">
                                    <li>ไม่ยื่นแบบ: <?php echo $history->no_filing_percent; ?>%</li>
                                    <li>ยื่นไม่ถูกต้อง: <?php echo $history->incorrect_filing_percent; ?>%</li>
                                    <li>ชำระล่าช้า: 2% ต่อเดือน</li>
                                </ul>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<!-- Modal แสดงตัวอย่างการคำนวณ -->
<div id="calculationModal" class="modal fade" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content p-6 bg-white rounded-xl shadow-xl">
            <div class="modal-header border-b pb-4">
                <h3 class="text-xl font-semibold text-gray-800">
                    <i class="fas fa-calculator mr-2 text-blue-500"></i>
                    ตัวอย่างการคำนวณค่าปรับ
                </h3>
                <button type="button" class="close-modal text-gray-400 hover:text-gray-600">
                    <i class="fas fa-times"></i>
                </button>
            </div>
            <div class="modal-body py-4">
                <!-- ตัวอย่างจะถูกเพิ่มด้วย JavaScript -->
            </div>
        </div>
    </div>
</div>

<script>
    document.querySelectorAll('input[type="number"]').forEach(input => {
        input.addEventListener('change', calculateExample);
    });

    function calculateExample() {
        const baseAmount = 10000; // สมมติค่าภาษี 10,000 บาท
        const noFilingPercent = parseFloat(document.querySelector('[name="no_filing_percent"]').value);
        const incorrectFilingPercent = parseFloat(document.querySelector('[name="incorrect_filing_percent"]').value);

        // คำนวณตัวอย่าง
        const noFilingPenalty = (baseAmount * noFilingPercent / 100).toFixed(2);
        const incorrectFilingPenalty = (baseAmount * incorrectFilingPercent / 100).toFixed(2);
        const latePaymentPenalty = (baseAmount * 0.02).toFixed(2); // 2% ต่อเดือน

        // แสดงผลในตารางตัวอย่าง
        const exampleHtml = `
    <div class="space-y-4">
        <p class="text-gray-600">ตัวอย่างการคำนวณจากค่าภาษี ${baseAmount.toLocaleString()} บาท</p>
        <table class="w-full">
            <thead>
                <tr class="bg-gray-50">
                    <th class="px-4 py-2 text-left">กรณี</th>
                    <th class="px-4 py-2 text-right">ค่าปรับ (บาท)</th>
                    <th class="px-4 py-2 text-left">หมายเหตุ</th>
                </tr>
            </thead>
            <tbody>
                <tr>
                    <td class="px-4 py-2">ไม่ยื่นแบบ</td>
                    <td class="px-4 py-2 text-right">${parseFloat(noFilingPenalty).toLocaleString()}</td>
                    <td class="px-4 py-2">${noFilingPercent}% ของค่าภาษี</td>
                </tr>
                <tr>
                    <td class="px-4 py-2">ยื่นไม่ถูกต้อง</td>
                    <td class="px-4 py-2 text-right">${parseFloat(incorrectFilingPenalty).toLocaleString()}</td>
                    <td class="px-4 py-2">${incorrectFilingPercent}% ของค่าภาษีที่ประเมินเพิ่ม</td>
                </tr>
                <tr>
                    <td class="px-4 py-2">ชำระล่าช้า (1 เดือน)</td>
                    <td class="px-4 py-2 text-right">${parseFloat(latePaymentPenalty).toLocaleString()}</td>
                    <td class="px-4 py-2">2% ต่อเดือน</td>
                </tr>
            </tbody>
        </table>
    </div>
    `;

        document.querySelector('.modal-body').innerHTML = exampleHtml;
    }

    // เรียกคำนวณครั้งแรก
    calculateExample();
</script>