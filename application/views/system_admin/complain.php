    <!-- <h5 class="border border-#f5f5f5 p-2 mb-2 font-black" style="background-color: #f5f5f5;">จัดการข้อมูลเรื่องร้องเรียน</h5> -->
    <label class="form-label" for="complain_status">เลือกสถานะ:</label>
    <select class="form-select" name="complain_status" id="complain_status">
        <option value="">ทั้งหมด</option>
        <!-- Add options dynamically based on your complain statuses -->
        <option value="รอรับเรื่อง">รอรับเรื่อง</option>
        <option value="รับเรื่องแล้ว">รับเรื่องแล้ว</option>
        <option value="กำลังดำเนินการ">กำลังดำเนินการ</option>
        <option value="ดำเนินการเรียบร้อย">ดำเนินการเรียบร้อย</option>
        <option value="ยกเลิก">ยกเลิก</option>
        <!-- Add more options as needed -->
    </select>
    <button class="btn btn-info" onclick="applyFilters()">ค้นหา</button>

    <!-- DataTales Example -->
    <div class="card shadow mb-4">
        <div class="card-header py-3">
            <h6 class="m-0 font-weight-bold text-black">จัดการข้อมูลเรื่องร้องเรียน</h6>
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
                            <th style="width: 5%;">วันที่แจ้ง</th>
                            <th style="width: 10%;">สถานะ</th>
                            <th style="width: 15%;">รูปภาพ</th>
                            <th style="width: 20%;">หัวข้อร้องเรียน</th>
                            <th style="width: 20%;">รายละเอียด</th>
                            <th style="width: 10%;">ผู้แจ้ง</th>
                            <th style="width: 10%;">ติดต่อ</th>
                            <th style="width: 5%;">จัดการ</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($complains  as $complain) { ?>
                            <tr role="row">
                                <td><?= $complain->complain_id; ?></td>
                                <td><?= date('d/m/Y H:i', strtotime($complain->complain_datesave . '+543 years')) ?> น.</td>
                                <td>
                                    <p class="small-font" style="font-size: 15px; background-color:
                <?php if ($complain->complain_status === 'รับเรื่องแล้ว') : ?>
                    #D9EAFF;
                <?php elseif ($complain->complain_status === 'กำลังดำเนินการ') : ?>
                    #CFD7FE;
                <?php elseif ($complain->complain_status === 'รอดำเนินการ') : ?>
                    #FFECE7;
                <?php elseif ($complain->complain_status === 'ดำเนินการเรียบร้อย') : ?>
                    #DBFFDD;
                <?php elseif ($complain->complain_status === 'ยกเลิก') : ?>
                    #FFE3E3;
                <?php else : ?>
                    #FFFBDC; /* สีเริ่มต้นหากไม่ตรงกับเงื่อนไขใดๆ */
                <?php endif; ?>
                ; color:
                <?php if ($complain->complain_status === 'รับเรื่องแล้ว') : ?>
                    #4C97EE;
                <?php elseif ($complain->complain_status === 'กำลังดำเนินการ') : ?>
                    #3D5AF1;
                <?php elseif ($complain->complain_status === 'รอดำเนินการ') : ?>
                    #E05A33;
                <?php elseif ($complain->complain_status === 'ดำเนินการเรียบร้อย') : ?>
                    #00B73E;
                <?php elseif ($complain->complain_status === 'ยกเลิก') : ?>
                    #FF0202;
                <?php else : ?>
                    #FFC700; /* สีเริ่มต้นหากไม่ตรงกับเงื่อนไขใดๆ */
                <?php endif; ?>
                border: 1.3px solid
                <?php if ($complain->complain_status === 'รับเรื่องแล้ว') : ?>
                    #4C97EE;
                <?php elseif ($complain->complain_status === 'กำลังดำเนินการ') : ?>
                    #3D5AF1;
                <?php elseif ($complain->complain_status === 'รอดำเนินการ') : ?>
                    #E05A33;
                <?php elseif ($complain->complain_status === 'ดำเนินการเรียบร้อย') : ?>
                    #00B73E;
                <?php elseif ($complain->complain_status === 'ยกเลิก') : ?>
                    #FF0202;
                <?php else : ?>
                    #FFC700; /* สีเริ่มต้นหากไม่ตรงกับเงื่อนไขใดๆ */
                <?php endif; ?>
                ;
                border-radius: 20px; /* เพิ่มเส้นโค้ง */
                padding: 5px; /* เพิ่มขอบรอบตัวอักษร */
                text-align: center; /* ปรับตำแหน่งข้อความให้อยู่กลาง */
                ">
                                        <?= $complain->complain_status; ?>
                                    </p>
                                </td>
                                <td>
                                    <?php foreach ($complain->images as $image) : ?>
                                        <a href="<?php echo base_url('docs/img/' . $image->complain_img_img); ?>" data-lightbox="image-<?php echo $complain->complain_id; ?>">
                                            <img src="<?= base_url('docs/img/' . $image->complain_img_img); ?>" alt="Complain Image" width="100">
                                        </a>
                                    <?php endforeach; ?>
                                </td>
                                <td class=""><?= $complain->complain_topic; ?></td>
                                <td class=""><?= $complain->complain_detail; ?></td>
                                <td class="limited-text"><?= $complain->complain_by; ?></td>
                                <td class="limited-text"><?= $complain->complain_phone; ?></td>
                                <td><a href="<?= site_url('Complain_backend/detail/' . $complain->complain_id); ?>"><i class="bi bi-pencil-square fa-lg "></i></a></td>
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
            var complainStatus = document.getElementById('complain_status').value;

            // Redirect to the current page with filter parameters
            window.location.href = '<?= base_url('Complain_backend/index') ?>?complain_status=' + complainStatus;
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
                        <?php foreach ($complains  as $complain) { ?>
                            <tr role="row">
                                <td align="center"><?= $Index; ?></td>
                                <td>
                                    <?php foreach ($complain->images as $image) : ?>
                                        <img src="<?= base_url('docs/img/' . $image->complain_img_img); ?>" alt="Complain Image" width="100">
                                    <?php endforeach; ?>
                                </td>
                                <td class="limited-text"><?= $complain->complain_type; ?></td>
                                <td class="limited-text"><?= $complain->complain_head; ?></td>
                                <td class="limited-text"><?= $complain->complain_detail; ?></td>
                                <td><?= $complain->complain_lat; ?>,<br><?= $complain->complain_long; ?></td>
                                <td class="limited-text"><?= $complain->complain_by; ?></td>
                                <td class="limited-text"><?= $complain->complain_phone; ?></td>
                                <td><?= date('d/m/Y H:i', strtotime($complain->complain_datesave . '+543 years')) ?> น.</td>
                                <td>
                                    <select class="form-select complain-status" name="complain_status" data-complain-id="<?= $complain->complain_id; ?>">
                                        <option value="<?= $complain->complain_status; ?>"><?= $complain->complain_status; ?></option>
                                        <option value="รับเรื่องแล้ว" style="color: black;">รับเรื่องแล้ว</option>
                                        <option value="กำลังดำเนินการ" style="color: black;">กำลังดำเนินการ</option>
                                        <option value="รอดำเนินการ" style="color: black;">รอดำเนินการ</option>
                                        <option value="ดำเนินการเรียบร้อย" style="color: black;">ดำเนินการเรียบร้อย</option>
                                        <option value="ยกเลิก" style="color: black;">ยกเลิก</option>
                                    </select>
                                </td>
                                <script>
                                    // รับค่า complain_id และ new_status เมื่อมีการเลือกค่าใหม่
                                    const selectElement<?= $complain->complain_id; ?> = document.querySelector('.complain-status[data-complain-id="<?= $complain->complain_id; ?>"]');

                                    selectElement<?= $complain->complain_id; ?>.addEventListener('change', function() {
                                        const complainId = this.getAttribute('data-complain-id');
                                        const newStatus = this.value;

                                        // ส่งข้อมูลไปยังเซิร์ฟเวอร์ด้วย AJAX
                                        $.ajax({
                                            type: 'POST',
                                            url: 'complain/updateComplainStatus',
                                            data: {
                                                complain_id: complainId,
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

                                    selectElement<?= $complain->complain_id; ?>.addEventListener('focus', function() {
                                        this.style.backgroundColor = 'white'; // เมื่อได้รับการโฟกัส (focus) ให้สีพื้นหลังเป็นสีขาว
                                    });

                                    selectElement<?= $complain->complain_id; ?>.addEventListener('blur', function() {
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
                                    const initialStatus<?= $complain->complain_id; ?> = selectElement<?= $complain->complain_id; ?>.value;
                                    const initialColor<?= $complain->complain_id; ?> = getStatusColor(initialStatus<?= $complain->complain_id; ?>);
                                    selectElement<?= $complain->complain_id; ?>.style.color = initialColor<?= $complain->complain_id; ?>;
                                    selectElement<?= $complain->complain_id; ?>.style.backgroundColor = getBackgroundColor(initialStatus<?= $complain->complain_id; ?>);
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