    <!-- <h5 class="border border-#f5f5f5 p-2 mb-2 font-black" style="background-color: #f5f5f5;">จัดการข้อมูลเรื่องร้องเรียน</h5> -->
    <!-- DataTales Example -->
    <div class="card shadow mb-4">
        <div class="card-header py-3">
            <h6 class="m-0 font-weight-bold text-black">จัดการข้อมูลรับฟังความคิดเห็นและข้อเสนอแนะ</h6>
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
                        <?php foreach ($suggestionss  as $suggestions) { ?>
                            <tr role="row">
                                <td><?= $suggestions->suggestions_id; ?></td>
                                <td>
                                    <?php foreach ($suggestions->images as $image) : ?>
                                        <a href="<?php echo base_url('docs/img/' . $image->suggestions_img_img); ?>" data-lightbox="image-<?php echo $suggestions->suggestions_id; ?>">
                                            <img src="<?= base_url('docs/img/' . $image->suggestions_img_img); ?>" alt="suggestions Image" width="100">
                                        </a>
                                    <?php endforeach; ?>
                                </td>
                                <td class="limited-text"><?= $suggestions->suggestions_topic; ?></td>
                                <td class="limited-text"><?= $suggestions->suggestions_detail; ?></td>
                                <td class="limited-text"><?= $suggestions->suggestions_by; ?></td>
                                <td class="limited-text"><?= $suggestions->suggestions_phone; ?></td>
                                <td>
                                    <a href="#" role="button" onclick="confirmDelete(<?= $suggestions->suggestions_id; ?>);"><i class="bi bi-trash fa-lg "></i></a>
                                    <script>
                                        function confirmDelete(suggestions_id) {
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
                                                    window.location.href = "<?= site_url('suggestions_backend/del/'); ?>" + suggestions_id;
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
                        <?php foreach ($suggestionss  as $suggestions) { ?>
                            <tr role="row">
                                <td align="center"><?= $Index; ?></td>
                                <td>
                                    <?php foreach ($suggestions->images as $image) : ?>
                                        <img src="<?= base_url('docs/img/' . $image->suggestions_img_img); ?>" alt="suggestions Image" width="100">
                                    <?php endforeach; ?>
                                </td>
                                <td class="limited-text"><?= $suggestions->suggestions_type; ?></td>
                                <td class="limited-text"><?= $suggestions->suggestions_head; ?></td>
                                <td class="limited-text"><?= $suggestions->suggestions_detail; ?></td>
                                <td><?= $suggestions->suggestions_lat; ?>,<br><?= $suggestions->suggestions_long; ?></td>
                                <td class="limited-text"><?= $suggestions->suggestions_by; ?></td>
                                <td class="limited-text"><?= $suggestions->suggestions_phone; ?></td>
                                <td><?= date('d/m/Y H:i', strtotime($suggestions->suggestions_datesave . '+543 years')) ?> น.</td>
                                <td>
                                    <select class="form-select suggestions-status" name="suggestions_status" data-suggestions-id="<?= $suggestions->suggestions_id; ?>">
                                        <option value="<?= $suggestions->suggestions_status; ?>"><?= $suggestions->suggestions_status; ?></option>
                                        <option value="รับเรื่องแล้ว" style="color: black;">รับเรื่องแล้ว</option>
                                        <option value="กำลังดำเนินการ" style="color: black;">กำลังดำเนินการ</option>
                                        <option value="รอดำเนินการ" style="color: black;">รอดำเนินการ</option>
                                        <option value="แก้ไขเรียบร้อย" style="color: black;">แก้ไขเรียบร้อย</option>
                                        <option value="ยกเลิก" style="color: black;">ยกเลิก</option>
                                    </select>
                                </td>
                                <script>
                                    // รับค่า suggestions_id และ new_status เมื่อมีการเลือกค่าใหม่
                                    const selectElement<?= $suggestions->suggestions_id; ?> = document.querySelector('.suggestions-status[data-suggestions-id="<?= $suggestions->suggestions_id; ?>"]');

                                    selectElement<?= $suggestions->suggestions_id; ?>.addEventListener('change', function() {
                                        const suggestionsId = this.getAttribute('data-suggestions-id');
                                        const newStatus = this.value;

                                        // ส่งข้อมูลไปยังเซิร์ฟเวอร์ด้วย AJAX
                                        $.ajax({
                                            type: 'POST',
                                            url: 'suggestions/updatesuggestionsStatus',
                                            data: {
                                                suggestions_id: suggestionsId,
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

                                    selectElement<?= $suggestions->suggestions_id; ?>.addEventListener('focus', function() {
                                        this.style.backgroundColor = 'white'; // เมื่อได้รับการโฟกัส (focus) ให้สีพื้นหลังเป็นสีขาว
                                    });

                                    selectElement<?= $suggestions->suggestions_id; ?>.addEventListener('blur', function() {
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
                                    const initialStatus<?= $suggestions->suggestions_id; ?> = selectElement<?= $suggestions->suggestions_id; ?>.value;
                                    const initialColor<?= $suggestions->suggestions_id; ?> = getStatusColor(initialStatus<?= $suggestions->suggestions_id; ?>);
                                    selectElement<?= $suggestions->suggestions_id; ?>.style.color = initialColor<?= $suggestions->suggestions_id; ?>;
                                    selectElement<?= $suggestions->suggestions_id; ?>.style.backgroundColor = getBackgroundColor(initialStatus<?= $suggestions->suggestions_id; ?>);
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