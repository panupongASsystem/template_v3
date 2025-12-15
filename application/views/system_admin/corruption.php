    <!-- <h5 class="border border-#f5f5f5 p-2 mb-2 font-black" style="background-color: #f5f5f5;">จัดการข้อมูลเรื่องร้องเรียน</h5> -->
    <!-- DataTales Example -->
    <div class="card shadow mb-4">
        <div class="card-header py-3">
            <h6 class="m-0 font-weight-bold text-black">จัดการข้อมูลแจ้งเรื่องร้องเรียนการทุจริตและประพฤติมิชอบ</h6>
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
                            <th style="width: 20%;">รูปภาพ</th>
                            <th style="width: 20%;">หัวข้อร้องเรียน</th>
                            <th style="width: 30%;">รายละเอียด</th>
                            <th style="width: 10%;">ผู้แจ้ง</th>
                            <th style="width: 10%;">ติดต่อ</th>
                            <th style="width: 5%;">จัดการ</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($corruptions  as $corruption) { ?>
                            <tr role="row">
                                <td><?= $corruption->corruption_id; ?></td>
                                <td>
                                    <?php foreach ($corruption->images as $image) : ?>
                                        <a href="<?php echo base_url('docs/img/' . $image->corruption_img_img); ?>" data-lightbox="image-<?php echo $corruption->corruption_id; ?>">
                                            <img src="<?= base_url('docs/img/' . $image->corruption_img_img); ?>" alt="corruption Image" width="100">
                                        </a>
                                    <?php endforeach; ?>
                                </td>
                                <td class="limited-text"><?= $corruption->corruption_topic; ?></td>
                                <td class="limited-text"><?= $corruption->corruption_detail; ?></td>
                                <td class="limited-text"><?= $corruption->corruption_by; ?></td>
                                <td class="limited-text"><?= $corruption->corruption_phone; ?></td>
                                <td>
                                    <a href="#" role="button" onclick="confirmDelete(<?= $corruption->corruption_id; ?>);"><i class="bi bi-trash fa-lg "></i></a>
                                    <script>
                                        function confirmDelete(corruption_id) {
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
                                                    window.location.href = "<?= site_url('corruption_backend/del/'); ?>" + corruption_id;
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
                        <?php foreach ($corruptions  as $corruption) { ?>
                            <tr role="row">
                                <td align="center"><?= $Index; ?></td>
                                <td>
                                    <?php foreach ($corruption->images as $image) : ?>
                                        <img src="<?= base_url('docs/img/' . $image->corruption_img_img); ?>" alt="corruption Image" width="100">
                                    <?php endforeach; ?>
                                </td>
                                <td class="limited-text"><?= $corruption->corruption_type; ?></td>
                                <td class="limited-text"><?= $corruption->corruption_head; ?></td>
                                <td class="limited-text"><?= $corruption->corruption_detail; ?></td>
                                <td><?= $corruption->corruption_lat; ?>,<br><?= $corruption->corruption_long; ?></td>
                                <td class="limited-text"><?= $corruption->corruption_by; ?></td>
                                <td class="limited-text"><?= $corruption->corruption_phone; ?></td>
                                <td><?= date('d/m/Y H:i', strtotime($corruption->corruption_datesave . '+543 years')) ?> น.</td>
                                <td>
                                    <select class="form-select corruption-status" name="corruption_status" data-corruption-id="<?= $corruption->corruption_id; ?>">
                                        <option value="<?= $corruption->corruption_status; ?>"><?= $corruption->corruption_status; ?></option>
                                        <option value="รับเรื่องแล้ว" style="color: black;">รับเรื่องแล้ว</option>
                                        <option value="กำลังดำเนินการ" style="color: black;">กำลังดำเนินการ</option>
                                        <option value="รอดำเนินการ" style="color: black;">รอดำเนินการ</option>
                                        <option value="แก้ไขเรียบร้อย" style="color: black;">แก้ไขเรียบร้อย</option>
                                        <option value="ยกเลิก" style="color: black;">ยกเลิก</option>
                                    </select>
                                </td>
                                <script>
                                    // รับค่า corruption_id และ new_status เมื่อมีการเลือกค่าใหม่
                                    const selectElement<?= $corruption->corruption_id; ?> = document.querySelector('.corruption-status[data-corruption-id="<?= $corruption->corruption_id; ?>"]');

                                    selectElement<?= $corruption->corruption_id; ?>.addEventListener('change', function() {
                                        const corruptionId = this.getAttribute('data-corruption-id');
                                        const newStatus = this.value;

                                        // ส่งข้อมูลไปยังเซิร์ฟเวอร์ด้วย AJAX
                                        $.ajax({
                                            type: 'POST',
                                            url: 'corruption/updatecorruptionStatus',
                                            data: {
                                                corruption_id: corruptionId,
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

                                    selectElement<?= $corruption->corruption_id; ?>.addEventListener('focus', function() {
                                        this.style.backgroundColor = 'white'; // เมื่อได้รับการโฟกัส (focus) ให้สีพื้นหลังเป็นสีขาว
                                    });

                                    selectElement<?= $corruption->corruption_id; ?>.addEventListener('blur', function() {
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
                                    const initialStatus<?= $corruption->corruption_id; ?> = selectElement<?= $corruption->corruption_id; ?>.value;
                                    const initialColor<?= $corruption->corruption_id; ?> = getStatusColor(initialStatus<?= $corruption->corruption_id; ?>);
                                    selectElement<?= $corruption->corruption_id; ?>.style.color = initialColor<?= $corruption->corruption_id; ?>;
                                    selectElement<?= $corruption->corruption_id; ?>.style.backgroundColor = getBackgroundColor(initialStatus<?= $corruption->corruption_id; ?>);
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