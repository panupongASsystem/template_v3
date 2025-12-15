    <!-- <h5 class="border border-#f5f5f5 p-2 mb-2 font-black" style="background-color: #f5f5f5;">จัดการข้อมูลเรื่องร้องเรียน</h5> -->
    <!-- DataTales Example -->
    <div class="card shadow mb-4">
        <div class="card-header py-3">
            <h6 class="m-0 font-weight-bold text-black">จัดการข้อมูลยื่นเอกสารออนไลน์</h6>
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <?php
                $Index = 1;
                ?>
                <table id="newdataTables" class="table">
                    <thead>
                        <tr>
                            <th style="width: 5%;">ลำดับ</th>
                            <th style="width: 20%;">ไฟล์</th>
                            <th style="width: 20%;">เรื่อง</th>
                            <th style="width: 30%;">รายละเอียด</th>
                            <th style="width: 10%;">ชื่อผู้ยื่นคำร้อง</th>
                            <th style="width: 10%;">เบอร์โทรศัพท์</th>
                            <th style="width: 5%;">จัดการ</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($query  as $rs) { ?>
                            <tr role="row">
                                <td align="center"><?= $Index; ?></td>
                                <td>
                                    <a href="<?php echo base_url('docs/file/' . $rs->esv_ods_file); ?>" target="_blank"><?= $rs->esv_ods_file; ?></a>
                                </td>
                                <td class="limited-text"><?= $rs->esv_ods_topic; ?></td>
                                <td class="limited-text"><?= $rs->esv_ods_detail; ?></td>
                                <td class="limited-text"><?= $rs->esv_ods_by; ?></td>
                                <td class="limited-text"><?= $rs->esv_ods_phone; ?></td>
                                <td>
                                    <a href="#" role="button" onclick="confirmDelete(<?= $rs->esv_ods_id; ?>);"><i class="bi bi-trash fa-lg "></i></a>
                                    <script>
                                        function confirmDelete(esv_ods_id) {
                                            Swal.fire({
                                                title: 'กดเพื่อยืนยัน?',
                                                text: "คุณจะไม่สามรถกู้คืนได้อีก!",
                                                icon: 'warning',
                                                showCancelButton: true,
                                                confirmButtonColor: '#3085d6',
                                                cancelButtonColor: '#d33',
                                                confirmButtonText: 'ใช่, ต้องการลบ!',
                                                cancelButtonText: 'ยกเลิก' // เปลี่ยนข้อความปุ่ม Cancel เป็นภาษาไทย
                                            }).then((result) => {
                                                if (result.isConfirmed) {
                                                    window.location.href = "<?= site_url('Esv_ods_backend/del/'); ?>" + esv_ods_id;
                                                }
                                            });
                                        }
                                    </script>
                                </td>
                            </tr>
                        <?php
                            $Index++;
                        } ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>


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
                        <?php foreach ($esv_odss  as $esv_ods) { ?>
                            <tr role="row">
                                <td align="center"><?= $Index; ?></td>
                                <td>
                                    <?php foreach ($esv_ods->images as $image) : ?>
                                        <img src="<?= base_url('docs/img/' . $image->esv_ods_img_img); ?>" alt="esv_ods Image" width="100">
                                    <?php endforeach; ?>
                                </td>
                                <td class="limited-text"><?= $esv_ods->esv_ods_type; ?></td>
                                <td class="limited-text"><?= $esv_ods->esv_ods_head; ?></td>
                                <td class="limited-text"><?= $esv_ods->esv_ods_detail; ?></td>
                                <td><?= $esv_ods->esv_ods_lat; ?>,<br><?= $esv_ods->esv_ods_long; ?></td>
                                <td class="limited-text"><?= $esv_ods->esv_ods_by; ?></td>
                                <td class="limited-text"><?= $esv_ods->esv_ods_phone; ?></td>
                                <td><?= date('d/m/Y H:i', strtotime($esv_ods->esv_ods_datesave . '+543 years')) ?> น.</td>
                                <td>
                                    <select class="form-select esv_ods-status" name="esv_ods_status" data-esv_ods-id="<?= $esv_ods->esv_ods_id; ?>">
                                        <option value="<?= $esv_ods->esv_ods_status; ?>"><?= $esv_ods->esv_ods_status; ?></option>
                                        <option value="รับเรื่องแล้ว" style="color: black;">รับเรื่องแล้ว</option>
                                        <option value="กำลังดำเนินการ" style="color: black;">กำลังดำเนินการ</option>
                                        <option value="รอดำเนินการ" style="color: black;">รอดำเนินการ</option>
                                        <option value="แก้ไขเรียบร้อย" style="color: black;">แก้ไขเรียบร้อย</option>
                                        <option value="ยกเลิก" style="color: black;">ยกเลิก</option>
                                    </select>
                                </td>
                                <script>
                                    // รับค่า esv_ods_id และ new_status เมื่อมีการเลือกค่าใหม่
                                    const selectElement<?= $esv_ods->esv_ods_id; ?> = document.querySelector('.esv_ods-status[data-esv_ods-id="<?= $esv_ods->esv_ods_id; ?>"]');

                                    selectElement<?= $esv_ods->esv_ods_id; ?>.addEventListener('change', function() {
                                        const esv_odsId = this.getAttribute('data-esv_ods-id');
                                        const newStatus = this.value;

                                        // ส่งข้อมูลไปยังเซิร์ฟเวอร์ด้วย AJAX
                                        $.ajax({
                                            type: 'POST',
                                            url: 'esv_ods/updateesv_odsStatus',
                                            data: {
                                                esv_ods_id: esv_odsId,
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

                                    selectElement<?= $esv_ods->esv_ods_id; ?>.addEventListener('focus', function() {
                                        this.style.backgroundColor = 'white'; // เมื่อได้รับการโฟกัส (focus) ให้สีพื้นหลังเป็นสีขาว
                                    });

                                    selectElement<?= $esv_ods->esv_ods_id; ?>.addEventListener('blur', function() {
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
                                            case 'แก้ไขเรียบร้อย':
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
                                            case 'แก้ไขเรียบร้อย':
                                                return '#DBFFDD';
                                            case 'ยกเลิก':
                                                return '#FFE3E3';
                                            default:
                                                return '#FFFBDC'; // หากไม่ตรงกับสถานะที่กำหนดให้มีสีพื้นหลัง
                                        }
                                    }
                                    // กำหนดสีเริ่มต้นเมื่อหน้าเว็บโหลดเสร็จ
                                    const initialStatus<?= $esv_ods->esv_ods_id; ?> = selectElement<?= $esv_ods->esv_ods_id; ?>.value;
                                    const initialColor<?= $esv_ods->esv_ods_id; ?> = getStatusColor(initialStatus<?= $esv_ods->esv_ods_id; ?>);
                                    selectElement<?= $esv_ods->esv_ods_id; ?>.style.color = initialColor<?= $esv_ods->esv_ods_id; ?>;
                                    selectElement<?= $esv_ods->esv_ods_id; ?>.style.backgroundColor = getBackgroundColor(initialStatus<?= $esv_ods->esv_ods_id; ?>);
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