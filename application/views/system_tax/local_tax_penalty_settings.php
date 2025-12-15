<div class="ml-72 p-8">
    <div class="flex justify-between items-center mb-8">
        <div>
            <h2 class="text-2xl font-semibold text-gray-800">ตั้งค่าค่าปรับภาษีท้องถิ่น</h2>
            <p class="text-gray-600">กำหนดอัตราโทษและค่าปรับสำหรับภาษีท้องถิ่น</p>
        </div>
    </div>

    <?php if ($this->session->flashdata('save_success')): ?>
        <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded relative mb-6" role="alert">
            <strong class="font-bold">สำเร็จ!</strong>
            <span class="block sm:inline">บันทึกการตั้งค่าเรียบร้อยแล้ว</span>
        </div>
    <?php endif; ?>

    <div class="bg-white rounded-lg shadow p-6">
        <form action="<?php echo site_url('system_tax/update_local_tax_penalty_settings'); ?>" method="post" class="space-y-6">
            <!-- 1. ไม่ยื่นแบบภายในกำหนด -->
            <!-- <div class="p-6 bg-gray-50 rounded-lg">
                <h3 class="text-lg font-semibold text-gray-800 mb-4">
                    <i class="fas fa-exclamation-circle text-red-500 mr-2"></i>
                    กรณีไม่ยื่นแบบภายในกำหนด
                </h3>
                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">
                            เงินเพิ่ม (% ของค่าภาษีที่ประเมิน)
                        </label>
                        <div class="relative">
                            <input type="number" name="no_filing_percent"
                                value="<?php echo $settings->no_filing_percent; ?>"
                                class="form-input w-full rounded-lg border-gray-300"
                                step="0.01" min="0" max="100" required>
                            <div class="absolute inset-y-0 right-0 flex items-center px-3 text-gray-500">%</div>
                        </div>
                    </div>
                    <div class="flex items-center text-gray-600">
                        <i class="fas fa-info-circle mr-2"></i>
                        <span class="text-sm">เงินเพิ่มจะคำนวณจากค่าภาษีที่ประเมินทั้งหมด</span>
                    </div>
                </div>
            </div> -->

            <!-- 2. ยื่นรายการไม่ถูกต้อง -->
            <!-- <div class="p-6 bg-gray-50 rounded-lg">
                <h3 class="text-lg font-semibold text-gray-800 mb-4">
                    <i class="fas fa-file-alt text-yellow-500 mr-2"></i>
                    กรณียื่นรายการไม่ถูกต้อง
                </h3>
                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">
                            เงินเพิ่ม (% ของค่าภาษีที่ประเมินเพิ่ม)
                        </label>
                        <div class="relative">
                            <input type="number" name="incorrect_filing_percent"
                                value="<?php echo $settings->incorrect_filing_percent; ?>"
                                class="form-input w-full rounded-lg border-gray-300"
                                step="0.01" min="0" max="100" required>
                            <div class="absolute inset-y-0 right-0 flex items-center px-3 text-gray-500">%</div>
                        </div>
                    </div>
                    <div class="flex items-center text-gray-600">
                        <i class="fas fa-info-circle mr-2"></i>
                        <span class="text-sm">เงินเพิ่มจะคำนวณเฉพาะจากส่วนต่างของค่าภาษีที่ประเมินเพิ่ม</span>
                    </div>
                </div>
            </div> -->

            <!-- 3. ชี้เขตแจ้งเนื้อที่ไม่ถูกต้อง -->
            <!-- <div class="p-6 bg-gray-50 rounded-lg">
                <h3 class="text-lg font-semibold text-gray-800 mb-4">
                    <i class="fas fa-map-marker-alt text-blue-500 mr-2"></i>
                    กรณีชี้เขตแจ้งจำนวนเนื้อที่ดินไม่ถูกต้อง
                </h3>
                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">
                            จำนวนเท่าของค่าภาษีที่ประเมินเพิ่ม
                        </label>
                        <div class="relative">
                            <input type="number" name="incorrect_area_multiplier"
                                value="<?php echo $settings->incorrect_area_multiplier; ?>"
                                class="form-input w-full rounded-lg border-gray-300"
                                step="0.01" min="0" required>
                            <div class="absolute inset-y-0 right-0 flex items-center px-3 text-gray-500">เท่า</div>
                        </div>
                    </div>
                    <div class="flex items-center text-gray-600">
                        <i class="fas fa-info-circle mr-2"></i>
                        <span class="text-sm">เงินเพิ่มจะคำนวณโดยคูณกับค่าภาษีที่ประเมินเพิ่มจากการแจ้งเนื้อที่ไม่ถูกต้อง</span>
                    </div>
                </div>
            </div> -->

            <!-- 4. ชำระภาษีเกินกำหนด -->
            <div class="p-6 bg-gray-50 rounded-lg">
                <h3 class="text-lg font-semibold text-gray-800 mb-4">
                    <i class="fas fa-clock text-orange-500 mr-2"></i>
                    กรณีชำระภาษีเกินกำหนด (30 เมษายน)
                </h3>
                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">
                            เงินเพิ่มต่อปี (% ของค่าภาษี)
                        </label>
                        <div class="relative">
                            <input type="number" name="late_payment_yearly_percent"
                                value="<?php echo $settings->late_payment_yearly_percent; ?>"
                                class="form-input w-full rounded-lg border-gray-300"
                                step="0.01" min="0" max="100" required>
                            <div class="absolute inset-y-0 right-0 flex items-center px-3 text-gray-500">% ต่อปี</div>
                        </div>
                    </div>
                    <div class="flex items-center text-gray-600">
                        <i class="fas fa-info-circle mr-2"></i>
                        <span class="text-sm">เงินเพิ่มจะคำนวณตามระยะเวลาที่เกินกำหนด โดยคิดเป็นรายปี</span>
                    </div>
                </div>
            </div>

            <!-- ปุ่มบันทึก -->
            <div class="flex justify-between mt-8">
                <a href="<?php echo site_url('System_tax/local_tax'); ?>"
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
                                    <li>ชี้เขตผิด: <?php echo $history->incorrect_area_multiplier; ?> เท่า</li>
                                    <li>ชำระล่าช้า: <?php echo $history->late_payment_yearly_percent; ?>% ต่อปี</li>
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
    // Script สำหรับแสดงตัวอย่างการคำนวณ
    document.querySelectorAll('input[type="number"]').forEach(input => {
        input.addEventListener('change', calculateExample);
    });

    function calculateExample() {
        const baseAmount = 10000; // สมมติค่าภาษี 10,000 บาท
        const noFilingPercent = parseFloat(document.querySelector('[name="no_filing_percent"]').value);
        const incorrectFilingPercent = parseFloat(document.querySelector('[name="incorrect_filing_percent"]').value);
        const incorrectAreaMultiplier = parseFloat(document.querySelector('[name="incorrect_area_multiplier"]').value);
        const latePaymentYearlyPercent = parseFloat(document.querySelector('[name="late_payment_yearly_percent"]').value);

        // คำนวณตัวอย่าง
        const noFilingPenalty = (baseAmount * noFilingPercent / 100).toFixed(2);
        const incorrectFilingPenalty = (baseAmount * incorrectFilingPercent / 100).toFixed(2);
        const incorrectAreaPenalty = (baseAmount * incorrectAreaMultiplier).toFixed(2);
        const latePaymentPenalty = (baseAmount * latePaymentYearlyPercent / 100).toFixed(2);

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
                        <td class="px-4 py-2">${incorrectFilingPercent}% ของค่าภาษีที่เพิ่ม</td>
                    </tr>
                    <tr>
                        <td class="px-4 py-2">ชี้เขตผิด</td>
                        <td class="px-4 py-2 text-right">${parseFloat(incorrectAreaPenalty).toLocaleString()}</td>
                        <td class="px-4 py-2">${incorrectAreaMultiplier} เท่าของค่าภาษีที่เพิ่ม</td>
                    </tr>
                    <tr>
                        <td class="px-4 py-2">ชำระล่าช้า (1 ปี)</td>
                        <td class="px-4 py-2 text-right">${parseFloat(latePaymentPenalty).toLocaleString()}</td>
                        <td class="px-4 py-2">${latePaymentYearlyPercent}% ต่อปี</td>
                    </tr>
                </tbody>
            </table>
        </div>
    `;

        document.querySelector('.modal-body').innerHTML = exampleHtml;
    }

    // เรียกคำนวณครั้งแรก
    calculateExample();

    // ฟังก์ชันคำนวณค่าปรับ
    function calculatePenalty(paymentId) {
        $.ajax({
            url: site_url('system_tax/calculate_local_tax_penalties'),
            type: 'POST',
            data: {
                payment_id: paymentId
            },
            success: function(response) {
                let data = JSON.parse(response);
                if (data.status === 'success') {
                    updatePenaltyDisplay(data.data);
                    showPenaltyModal(data.data);
                } else {
                    Swal.fire({
                        icon: 'error',
                        title: 'เกิดข้อผิดพลาด',
                        text: data.message
                    });
                }
            },
            error: function() {
                Swal.fire({
                    icon: 'error',
                    title: 'เกิดข้อผิดพลาด',
                    text: 'ไม่สามารถเชื่อมต่อกับเซิร์ฟเวอร์ได้'
                });
            }
        });
    }

    // ฟังก์ชันอัพเดตการแสดงผลค่าปรับ
    function updatePenaltyDisplay(data) {
        // อัพเดตแสดงผลรวมค่าปรับ
        $('#penalty-amount').text(number_format(data.total_penalty, 2) + ' บาท');
        $('#total-amount').text(number_format(data.total_amount, 2) + ' บาท');

        // อัพเดตรายละเอียดค่าปรับแต่ละประเภท
        let penaltyList = '';
        for (let type in data.penalties) {
            let penalty = data.penalties[type];
            penaltyList += `
            <div class="flex justify-between items-center p-2 ${type === 'late_payment' ? 'bg-yellow-50' : ''}">
                <span class="text-gray-700">${penalty.description}</span>
                <span class="font-medium">${number_format(penalty.amount, 2)} บาท</span>
            </div>
        `;
        }
        $('#penalty-details').html(penaltyList);
    }

    // ฟังก์ชันแสดง Modal รายละเอียดค่าปรับ
    function showPenaltyModal(data) {
        let modalContent = `
        <div class="space-y-4">
            <div class="p-4 bg-blue-50 rounded-lg">
                <h4 class="font-medium text-blue-800 mb-2">สรุปค่าปรับ</h4>
                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <p class="text-sm text-gray-600">ค่าภาษี</p>
                        <p class="font-medium">${number_format(data.base_amount, 2)} บาท</p>
                    </div>
                    <div>
                        <p class="text-sm text-gray-600">ค่าปรับรวม</p>
                        <p class="font-medium text-red-600">${number_format(data.total_penalty, 2)} บาท</p>
                    </div>
                </div>
            </div>

            <div class="space-y-2">
                <h4 class="font-medium text-gray-800">รายละเอียดค่าปรับ</h4>
                ${Object.entries(data.penalties).map(([type, penalty]) => `
                    <div class="p-3 bg-gray-50 rounded-lg">
                        <div class="flex justify-between items-center">
                            <span class="text-gray-700">${penalty.description}</span>
                            <span class="font-medium">${number_format(penalty.amount, 2)} บาท</span>
                        </div>
                        <div class="text-sm text-gray-500 mt-1">
                            ${getPenaltyCalculationDetail(type, penalty)}
                        </div>
                    </div>
                `).join('')}
            </div>
        </div>
    `;

        // แสดง Modal
        Swal.fire({
            title: 'รายละเอียดการคำนวณค่าปรับ',
            html: modalContent,
            width: 600,
            showCancelButton: true,
            confirmButtonText: 'พิมพ์',
            cancelButtonText: 'ปิด',
            cancelButtonColor: '#718096'
        }).then((result) => {
            if (result.isConfirmed) {
                // เปิดหน้าพิมพ์
                window.open(site_url(`system_tax/print_local_tax_penalty/${data.payment_id}`), '_blank');
            }
        });
    }

    // ฟังก์ชันแสดงรายละเอียดการคำนวณ
    function getPenaltyCalculationDetail(type, penalty) {
        switch (type) {
            case 'no_filing':
                return `${number_format(penalty.base_amount, 2)} × ${penalty.rate}`;
            case 'incorrect_filing':
                return `${number_format(penalty.base_amount, 2)} × ${penalty.rate}`;
            case 'incorrect_area':
                return `${number_format(penalty.base_amount, 2)} × ${penalty.rate}`;
            case 'late_payment':
                return `${number_format(penalty.base_amount, 2)} × ${penalty.rate} × ${penalty.years_late} ปี`;
            default:
                return '';
        }
    }

    // ฟังก์ชันยกเลิกค่าปรับ
    function cancelPenalty(paymentId) {
        Swal.fire({
            title: 'ยกเลิกค่าปรับ',
            input: 'textarea',
            inputLabel: 'เหตุผลการยกเลิก',
            inputPlaceholder: 'กรุณาระบุเหตุผล...',
            showCancelButton: true,
            confirmButtonText: 'ยืนยัน',
            cancelButtonText: 'ยกเลิก',
            inputValidator: (value) => {
                if (!value) {
                    return 'กรุณาระบุเหตุผลการยกเลิก';
                }
            }
        }).then((result) => {
            if (result.isConfirmed) {
                $.ajax({
                    url: site_url('system_tax/cancel_local_tax_penalty'),
                    type: 'POST',
                    data: {
                        payment_id: paymentId,
                        reason: result.value
                    },
                    success: function(response) {
                        let data = JSON.parse(response);
                        if (data.status === 'success') {
                            Swal.fire({
                                icon: 'success',
                                title: 'ยกเลิกค่าปรับเรียบร้อย',
                                showConfirmButton: false,
                                timer: 1500
                            }).then(() => {
                                location.reload();
                            });
                        } else {
                            Swal.fire({
                                icon: 'error',
                                title: 'เกิดข้อผิดพลาด',
                                text: data.message
                            });
                        }
                    }
                });
            }
        });
    }
</script>