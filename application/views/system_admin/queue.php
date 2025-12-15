    <!-- <h5 class="border border-#f5f5f5 p-2 mb-2 font-black" style="background-color: #f5f5f5;">จัดการข้อมูลเรื่องร้องเรียน</h5> -->
    <label class="form-label" for="queue_status">เลือกสถานะ:</label>
    <select class="form-select" name="queue_status" id="queue_status">
        <option value="">ทั้งหมด</option>
        <!-- Add options dynamically based on your queue statuses -->
        <option value="รอยืนยันการจอง">รอยืนยันการจอง</option>
        <option value="คิวได้รับการยืนยัน">คิวได้รับการยืนยัน</option>
        <option value="คิวได้ถูกยกเลิก">คิวได้ถูกยกเลิก</option>
        <!-- Add more options as needed -->
    </select>
    <button class="btn btn-info" onclick="applyFilters()">ค้นหา</button>

    <!-- DataTales Example -->
    <div class="card shadow mb-4">
        <div class="card-header py-3">
            <h6 class="m-0 font-weight-bold text-black">จัดการข้อมูลเรื่องจองคิวติดต่อราชการออนไลน์</h6>
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <?php
                $Index = 1;
                ?>
                <table id="newdataTables" class="table">
                    <thead>
                        <tr>
                            <th style="width: 5%;">เคส</th>
                            <th style="width: 15%;">สถานะ</th>
                            <th style="width: 20%;">หัวข้อจองคิว</th>
                            <th style="width: 20%;">รายละเอียด</th>
                            <th style="width: 15%;">ผู้จอง</th>
                            <th style="width: 10%;">ติดต่อ</th>
                            <th style="width: 5%;">วันขอเข้าใช้บริการ</th>
                            <th style="width: 5%;">วันที่ส่งเรื่อง</th>
                            <th style="width: 5%;">จัดการ</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($queues  as $queue) { ?>
                            <tr role="row">
                                <td><?= $queue->queue_id; ?></td>
                                <td>
                                    <p class="small-font" style="font-size: 15px; background-color:
                <?php if ($queue->queue_status === 'รับเรื่องแล้ว') : ?>
                    #D9EAFF;
                <?php elseif ($queue->queue_status === 'กำลังดำเนินการ') : ?>
                    #CFD7FE;
                <?php elseif ($queue->queue_status === 'รอดำเนินการ') : ?>
                    #FFECE7;
                <?php elseif ($queue->queue_status === 'คิวได้รับการยืนยัน') : ?>
                    #DBFFDD;
                <?php elseif ($queue->queue_status === 'คิวได้ถูกยกเลิก') : ?>
                    #FFE3E3;
                <?php else : ?>
                    #FFFBDC; /* สีเริ่มต้นหากไม่ตรงกับเงื่อนไขใดๆ */
                <?php endif; ?>
                ; color:
                <?php if ($queue->queue_status === 'รับเรื่องแล้ว') : ?>
                    #4C97EE;
                <?php elseif ($queue->queue_status === 'กำลังดำเนินการ') : ?>
                    #3D5AF1;
                <?php elseif ($queue->queue_status === 'รอดำเนินการ') : ?>
                    #E05A33;
                <?php elseif ($queue->queue_status === 'คิวได้รับการยืนยัน') : ?>
                    #00B73E;
                <?php elseif ($queue->queue_status === 'คิวได้ถูกยกเลิก') : ?>
                    #FF0202;
                <?php else : ?>
                    #FFC700; /* สีเริ่มต้นหากไม่ตรงกับเงื่อนไขใดๆ */
                <?php endif; ?>
                border: 1.3px solid
                <?php if ($queue->queue_status === 'รับเรื่องแล้ว') : ?>
                    #4C97EE;
                <?php elseif ($queue->queue_status === 'กำลังดำเนินการ') : ?>
                    #3D5AF1;
                <?php elseif ($queue->queue_status === 'รอดำเนินการ') : ?>
                    #E05A33;
                <?php elseif ($queue->queue_status === 'คิวได้รับการยืนยัน') : ?>
                    #00B73E;
                <?php elseif ($queue->queue_status === 'คิวได้ถูกยกเลิก') : ?>
                    #FF0202;
                <?php else : ?>
                    #FFC700; /* สีเริ่มต้นหากไม่ตรงกับเงื่อนไขใดๆ */
                <?php endif; ?>
                ;
                border-radius: 20px; /* เพิ่มเส้นโค้ง */
                padding: 5px; /* เพิ่มขอบรอบตัวอักษร */
                text-align: center; /* ปรับตำแหน่งข้อความให้อยู่กลาง */
                ">
                                        <?= $queue->queue_status; ?>
                                    </p>
                                </td>

                                <td class=""><?= $queue->queue_topic; ?></td>
                                <td class=""><?= $queue->queue_detail; ?></td>
                                <td class="limited-text"><?= $queue->queue_by; ?></td>
                                <td class="limited-text"><?= $queue->queue_phone; ?></td>
                                <td><?= date('d/m/Y H:i', strtotime($queue->queue_date . '+543 years')) ?> น.</td>
                                <td><?= date('d/m/Y H:i', strtotime($queue->queue_datesave . '+543 years')) ?> น.</td>
                                <td><a href="<?= site_url('queue_backend/detail/' . $queue->queue_id); ?>"><i class="bi bi-pencil-square fa-lg "></i></a></td>
                            </tr>
                        <?php
                            $Index++;
                        } ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <script>
        function applyFilters() {
            var queueStatus = document.getElementById('queue_status').value;

            // Redirect to the current page with filter parameters
            window.location.href = '<?= base_url('queue_backend/index') ?>?queue_status=' + queueStatus;
        }
    </script>
    <!-- <h5 class="border border-#f5f5f5 p-2 mb-2 font-black" style="background-color: #f5f5f5;">จัดการข้อมูลเรื่องร้องเรียน</h5>
    <div class="card shadow mb-4">
        <div class="card-header py-3">
            <h6 class="m-0 font-weight-bold text-primary">จัดการข้อมูลเรื่องร้องเรียน</h6>
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <?php
                $Index = 1;
                ?>
                <table id="newdataTables" class="table">
                    <thead>
                        <tr>
                            <th style="width: 3%;">ลำดับ</th>
                            <th style="width: 10%;">รูปภาพ</th>
                            <th style="width: 10%;">ประเภท</th>
                            <th style="width: 15%;">หัวข้อร้องเรียน</th>
                            <th style="width: 10%;">รายละเอียด</th>
                            <th style="width: 10%;">พิกัด</th>
                            <th style="width: 10%;">ผู้แจ้ง</th>
                            <th style="width: 10%;">ติดต่อ</th>
                            <th style="width: 7%;">เวลา</th>
                            <th style="width: 15%;">จัดการ</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($queues  as $queue) { ?>
                            <tr role="row">
                                <td align="center"><?= $Index; ?></td>
                                <td>
                                    <?php foreach ($queue->images as $image) : ?>
                                        <img src="<?= base_url('docs/img/' . $image->queue_img_img); ?>" alt="queue Image" width="100">
                                    <?php endforeach; ?>
                                </td>
                                <td class="limited-text"><?= $queue->queue_type; ?></td>
                                <td class="limited-text"><?= $queue->queue_head; ?></td>
                                <td class="limited-text"><?= $queue->queue_detail; ?></td>
                                <td><?= $queue->queue_lat; ?>,<br><?= $queue->queue_long; ?></td>
                                <td class="limited-text"><?= $queue->queue_by; ?></td>
                                <td class="limited-text"><?= $queue->queue_phone; ?></td>
                                <td><?= date('d/m/Y H:i', strtotime($queue->queue_datesave . '+543 years')) ?> น.</td>
                                <td>
                                    <select class="form-select queue-status" name="queue_status" data-queue-id="<?= $queue->queue_id; ?>">
                                        <option value="<?= $queue->queue_status; ?>"><?= $queue->queue_status; ?></option>
                                        <option value="รับเรื่องแล้ว" style="color: black;">รับเรื่องแล้ว</option>
                                        <option value="กำลังดำเนินการ" style="color: black;">กำลังดำเนินการ</option>
                                        <option value="รอดำเนินการ" style="color: black;">รอดำเนินการ</option>
                                        <option value="ดำเนินการเรียบร้อย" style="color: black;">ดำเนินการเรียบร้อย</option>
                                        <option value="ยกเลิก" style="color: black;">ยกเลิก</option>
                                    </select>
                                </td>
                                <script>
                                    // รับค่า queue_id และ new_status เมื่อมีการเลือกค่าใหม่
                                    const selectElement<?= $queue->queue_id; ?> = document.querySelector('.queue-status[data-queue-id="<?= $queue->queue_id; ?>"]');

                                    selectElement<?= $queue->queue_id; ?>.addEventListener('change', function() {
                                        const queueId = this.getAttribute('data-queue-id');
                                        const newStatus = this.value;

                                        // ส่งข้อมูลไปยังเซิร์ฟเวอร์ด้วย AJAX
                                        $.ajax({
                                            type: 'POST',
                                            url: 'queue/updatequeueStatus',
                                            data: {
                                                queue_id: queueId,
                                                new_status: newStatus
                                            },
                                            success: function(response) {
                                                // รีเฟรชหน้าเมื่อมีการเปลี่ยนแปลง
                                                location.reload();
                                                console.log(response);
                                                // ทำอื่นๆตามต้องการ เช่น อัพเดตหน้าเว็บ
                                            },
                                            error: function(error) {
                                                console.error(error);
                                            }
                                        });
                                    });

                                    selectElement<?= $queue->queue_id; ?>.addEventListener('focus', function() {
                                        this.style.backgroundColor = 'white'; // เมื่อได้รับการโฟกัส (focus) ให้สีพื้นหลังเป็นสีขาว
                                    });

                                    selectElement<?= $queue->queue_id; ?>.addEventListener('blur', function() {
                                        const selectedValue = this.value;
                                        const statusColor = getStatusColor(selectedValue);
                                        this.style.color = statusColor;
                                        this.style.backgroundColor = getBackgroundColor(selectedValue); // เมื่อเลือกแล้วให้ใช้สีพื้นหลังตามสถานะที่เลือก
                                    });
                                    // ฟังก์ชันสำหรับกำหนดสีตามสถานะ
                                    function getStatusColor(status) {
                                        switch (status) {
                                            case 'รับเรื่องแล้ว':
                                                return '#4C97EE';
                                            case 'กำลังดำเนินการ':
                                                return '#3D5AF1';
                                            case 'รอดำเนินการ':
                                                return '#E05A33';
                                            case 'ดำเนินการเรียบร้อย':
                                                return '#00B73E';
                                            case 'ยกเลิก':
                                                return '#FF0202';
                                            default:
                                                return '#FFC700';
                                        }
                                    }

                                    // ฟังก์ชันสำหรับกำหนดสีพื้นหลังของ <select> ตามสถานะที่เลือก
                                    function getBackgroundColor(status) {
                                        switch (status) {
                                            case 'รับเรื่องแล้ว':
                                                return '#D9EAFF';
                                            case 'กำลังดำเนินการ':
                                                return '#CFD7FE';
                                            case 'รอดำเนินการ':
                                                return '#FFECE7';
                                            case 'ดำเนินการเรียบร้อย':
                                                return '#DBFFDD';
                                            case 'ยกเลิก':
                                                return '#FFE3E3';
                                            default:
                                                return '#FFFBDC'; // หากไม่ตรงกับสถานะที่กำหนดให้มีสีพื้นหลัง
                                        }
                                    }
                                    // กำหนดสีเริ่มต้นเมื่อหน้าเว็บโหลดเสร็จ
                                    const initialStatus<?= $queue->queue_id; ?> = selectElement<?= $queue->queue_id; ?>.value;
                                    const initialColor<?= $queue->queue_id; ?> = getStatusColor(initialStatus<?= $queue->queue_id; ?>);
                                    selectElement<?= $queue->queue_id; ?>.style.color = initialColor<?= $queue->queue_id; ?>;
                                    selectElement<?= $queue->queue_id; ?>.style.backgroundColor = getBackgroundColor(initialStatus<?= $queue->queue_id; ?>);
                                </script>
                            </tr>
                        <?php
                            $Index++;
                        } ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div> -->