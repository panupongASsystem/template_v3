<div class="ml-72 p-8">
    <div class="flex justify-between items-center mb-8">
        <div>
            <h2 class="text-2xl font-semibold text-gray-800">จัดการวันครบกำหนดชำระภาษี</h2>
            <p class="text-gray-600">กำหนดวันครบกำหนดชำระภาษีแต่ละประเภท</p>
        </div>
        <!-- <button onclick="openAddDueDateModal()"
            class="flex items-center px-4 py-2 text-sm bg-blue-500 text-white rounded-lg hover:bg-blue-600">
            <i class="fas fa-plus mr-2"></i> เพิ่มวันครบกำหนด
        </button> -->
    </div>

    <!-- Table -->
    <div class="bg-white rounded-lg shadow">
        <div class="overflow-x-auto">
            <table class="w-full">
                <thead>
                    <tr class="bg-gray-50 text-left">
                        <th class="px-6 py-3 text-gray-600">ประเภทภาษี</th>
                        <!-- <th class="px-6 py-3 text-gray-600">ปีภาษี</th> -->
                        <th class="px-6 py-3 text-gray-600">วันที่แจ้งประเมิน</th>
                        <th class="px-6 py-3 text-gray-600">วันครบกำหนดชำระ</th>
                        <th class="px-6 py-3 text-gray-600">จัดการ</th>
                    </tr>
                </thead>
                <tbody class="divide-y">
                    <?php foreach ($due_dates as $date): ?>
                        <tr class="hover:bg-gray-50">
                            <td class="px-6 py-4">
                                <?php echo $tax_types[$date->tax_type]; ?>
                            </td>
                            <!-- <td class="px-6 py-4">
                            <?php echo $date->tax_year; ?>
                        </td> -->
                            <td class="px-6 py-4">
                                <?php
                                $parts = explode('-', $date->notification_date);
                                echo $parts[0] . ' เดือน' . $thai_month_arr[$parts[1]];
                                ?>
                            </td>
                            <td class="px-6 py-4">
                                <?php
                                $parts = explode('-', $date->due_date);
                                echo $parts[0] . ' เดือน' . $thai_month_arr[$parts[1]];
                                ?>
                            </td>
                            <td class="px-6 py-4">
                                <button onclick="editDueDate(<?php echo $date->id; ?>)"
                                    class="text-blue-600 hover:text-blue-800 mr-3">
                                    <i class="fas fa-edit"></i>
                                </button>
                                <!-- <button onclick="deleteDueDate(<?php echo $date->id; ?>)"
                                    class="text-red-600 hover:text-red-800">
                                    <i class="fas fa-trash"></i>
                                </button> -->
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<!-- Modal Form -->
<div class="modal fade" id="dueDateModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="modalTitle">เพิ่มวันครบกำหนดชำระภาษี</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form action="<?php echo site_url('System_tax/add_due_date'); ?>" method="post" id="dueDateForm">
                <input type="hidden" name="id" id="due_date_id">
                <div class="modal-body">
                    <div class="mb-3">
                        <label class="form-label">ประเภทภาษี</label>
                        <select name="tax_type" id="tax_type" class="form-select" disabled>
                            <option value="">เลือกประเภทภาษี</option>
                            <?php foreach ($tax_types as $key => $value): ?>
                                <option value="<?php echo $key; ?>"><?php echo $value; ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <!-- <div class="mb-3">
                        <label class="form-label">ปีภาษี</label>
                        <select name="tax_year" class="form-select" required>
                            <?php
                            $current_year = date('Y') + 543;
                            for ($i = 0; $i < 5; $i++) {
                                $year = $current_year + $i;
                                echo "<option value='$year'>$year</option>";
                            }
                            ?>
                        </select>
                    </div> -->
                    <div class="mb-3">
                        <label class="form-label">วันที่แจ้งประเมิน (วัน-เดือน)</label>
                        <div class="flex space-x-2">
                            <select name="notification_day" id="notification_day" class="form-select" required>
                                <option value="">วันที่</option>
                                <?php for ($i = 1; $i <= 31; $i++): ?>
                                    <option value="<?php echo str_pad($i, 2, '0', STR_PAD_LEFT); ?>"><?php echo $i; ?></option>
                                <?php endfor; ?>
                            </select>
                            <select name="notification_month" id="notification_month" class="form-select" required>
                                <option value="">เดือน</option>
                                <?php
                                $months = array(
                                    '01' => 'มกราคม',
                                    '02' => 'กุมภาพันธ์',
                                    '03' => 'มีนาคม',
                                    '04' => 'เมษายน',
                                    '05' => 'พฤษภาคม',
                                    '06' => 'มิถุนายน',
                                    '07' => 'กรกฎาคม',
                                    '08' => 'สิงหาคม',
                                    '09' => 'กันยายน',
                                    '10' => 'ตุลาคม',
                                    '11' => 'พฤศจิกายน',
                                    '12' => 'ธันวาคม'
                                );
                                foreach ($months as $value => $label): ?>
                                    <option value="<?php echo $value; ?>"><?php echo $label; ?></option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                    </div>

                    <div class="mb-3">
                        <label class="form-label">วันครบกำหนดชำระ (วัน-เดือน)</label>
                        <div class="flex space-x-2">
                            <select name="due_day" id="due_day" class="form-select" required>
                                <option value="">วันที่</option>
                                <?php for ($i = 1; $i <= 31; $i++): ?>
                                    <option value="<?php echo str_pad($i, 2, '0', STR_PAD_LEFT); ?>"><?php echo $i; ?></option>
                                <?php endfor; ?>
                            </select>
                            <select name="due_month" id="due_month" class="form-select" required>
                                <option value="">เดือน</option>
                                <?php foreach ($months as $value => $label): ?>
                                    <option value="<?php echo $value; ?>"><?php echo $label; ?></option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">ยกเลิก</button>
                    <button type="submit" class="btn btn-primary">บันทึก</button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
    $('#dueDateForm').on('submit', function(e) {
        e.preventDefault();
        if (confirm('ยืนยันการบันทึกข้อมูล?')) {
            this.submit();
        }
    });

    function openAddDueDateModal() {
        $('#modalTitle').text('เพิ่มวันครบกำหนดชำระภาษี');
        $('#dueDateForm')[0].reset();
        $('#dueDateForm').attr('action', '<?php echo site_url("System_tax/add_due_date"); ?>');
        $('#dueDateModal').modal('show');
    }

    function editDueDate(id) {
    $('#modalTitle').text('แก้ไขวันครบกำหนดชำระภาษี');
    $('#dueDateForm').attr('action', '<?php echo site_url("System_tax/update_due_date"); ?>');

    $.ajax({
        url: '<?php echo site_url("System_tax/get_due_date_by_id/"); ?>' + id,
        method: 'GET',
        success: function(response) {
            try {
                var dueDate = JSON.parse(response);
                $('#due_date_id').val(dueDate.id);
                $('#tax_type').val(dueDate.tax_type);

                // แยกวันและเดือน
                var notification_parts = dueDate.notification_date.split('-');
                var due_parts = dueDate.due_date.split('-');

                // ใส่ค่าในฟอร์ม
                $('select[name="notification_day"]').val(notification_parts[0]);
                $('select[name="notification_month"]').val(notification_parts[1]);
                $('select[name="due_day"]').val(due_parts[0]);
                $('select[name="due_month"]').val(due_parts[1]);

                $('#dueDateModal').modal('show');
            } catch (e) {
                console.error('Error parsing JSON:', e);
                alert('เกิดข้อผิดพลาดในการโหลดข้อมูล');
            }
        },
        error: function(xhr, status, error) {
            console.error('AJAX Error:', error);
            alert('เกิดข้อผิดพลาดในการโหลดข้อมูล');
        }
    });
}
    function deleteDueDate(id) {
        Swal.fire({
            title: 'ยืนยันการลบ?',
            text: "คุณต้องการลบข้อมูลนี้ใช่หรือไม่",
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#d33',
            cancelButtonColor: '#3085d6',
            confirmButtonText: 'ใช่, ลบ',
            cancelButtonText: 'ยกเลิก'
        }).then((result) => {
            if (result.isConfirmed) {
                window.location.href = '<?php echo site_url("System_tax/delete_due_date/"); ?>' + id;
            }
        });
    }
</script>