    <!-- Tasks Card Example -->
    <!-- <div class="col-xl-4 col-md-6 mb-4">
        <div class="card border-left-info shadow h-100 py-2">
            <div class="card-body">
                <div class="row no-gutters align-items-center">
                    <div class="col mr-2">
                        <?php
                        $percentage = ($used_space_mb / $upload_limit_mb) * 100;
                        $color = 'green'; // เริ่มต้นเป็นสีเขียน (1-69%)
                        if ($percentage >= 70 && $percentage <= 89) {
                            $color = 'orange'; // 70-89% ให้เปลี่ยนเป็นสีส้ม
                        } elseif ($percentage >= 90) {
                            $color = 'red'; // 90% ขึ้นไป ให้เปลี่ยนเป็นสีแดง
                        }
                        ?>
                        <div class="text-xs font-weight-bold text-info text-uppercase mb-1">Storage Usage
                        </div>
                        <div class="row no-gutters align-items-center">
                            <div class="col-auto">
                                <div class="h5 mb-0 mr-3 font-weight-bold text-gray-800">
                                    <p><?php echo number_format($used_space_mb, 2); ?> MB / <?php echo $upload_limit_mb; ?> MB</p>
                                </div>
                            </div>
                            <div class="col-12">
                                <div class="progress progress-sm mr-6">
                                    <div class="progress-bar" role="progressbar" style=" background-color: <?php echo $color; ?>; width: <?php echo $percentage; ?>%" aria-valuenow="50" aria-valuemin="0" aria-valuemax="100"></div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-sm-12">
                        <?php echo number_format($percentage, 2); ?>%
                    </div>
                </div>
            </div>
        </div>
    </div> -->

    <div class="ml-3 mr-3">
        <a class="btn add-btn" href="<?= site_url('travel_backend/adding_Travel'); ?>">
            <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-plus-circle" viewBox="0 0 16 16">
                <path d="M8 15A7 7 0 1 1 8 1a7 7 0 0 1 0 14zm0 1A8 8 0 1 0 8 0a8 8 0 0 0 0 16z" />
                <path d="M8 4a.5.5 0 0 1 .5.5v3h3a.5.5 0 0 1 0 1h-3v3a.5.5 0 0 1-1 0v-3h-3a.5.5 0 0 1 0-1h3v-3A.5.5 0 0 1 8 4z" />
            </svg> เพิ่มข้อมูล</a>
        <a class="btn btn-light" href="<?= site_url('travel_backend'); ?>">
            <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-arrow-clockwise" viewBox="0 0 16 16">
                <path fill-rule="evenodd" d="M8 3a5 5 0 1 0 4.546 2.914.5.5 0 0 1 .908-.417A6 6 0 1 1 8 2v1z" />
                <path d="M8 4.466V.534a.25.25 0 0 1 .41-.192l2.36 1.966c.12.1.12.284 0 .384L8.41 4.658A.25.25 0 0 1 8 4.466z" />
            </svg> Refresh Data</a>
        <!-- <h5 class="border border-#f5f5f5 p-2 mb-2 font-black" style="background-color: #f5f5f5;">จัดการข้อมูลสถานที่ท่องเที่ยว</h5> -->
        <!-- DataTales Example -->
        <div class="card shadow mb-4">
            <div class="card-header py-3">
                <h6 class="m-0 font-weight-bold text-black">จัดการข้อมูลสถานที่ท่องเที่ยว</h6>
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <!-- เพิ่ม select option สำหรับเลือกการแสดงผล -->
                    <!-- <div class="mb-3">
                        <label for="displayOption" class="form-label">แสดงข้อมูล:</label>
                        <select class="form-select" id="displayOption">
                            <option value="all">ทั้งหมด</option>
                            <option value="first">อบจ มุกดาหาร</option>
                            <option value="second">สมาชิก</option>
                        </select>
                    </div> -->

                    <?php
                    $Index = 1;
                    ?>

                    <table id="newdataTables" class="table">
                        <thead>
                            <tr>
                                <th style="width: 5%;">ลำดับ</th>
                                <th style="width: 13%;">รูปภาพ</th>
                                <th style="width: 17%;">ชื่อ</th>
                                <th style="width: 20%;">รายละเอียด</th>
                                <th style="width: 10%;">สถานที่</th>
                                <th style="width: 13%;">อัพโหลด</th>
                                <th style="width: 7%;">วันที่</th>
                                <th style="width: 5%;">สถานะ</th>
                                <th style="width: 12%;">จัดการ</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php
                            foreach ($qadmin as $rs) { ?>
                                <tr role="row">
                                    <td align="center"><?= $Index; ?></td>
                                    <td>
                                        <?php if (!empty($rs->travel_img)) : ?>
                                            <img src="<?php echo base_url('docs/img/' . $rs->travel_img); ?>" width="100%" height="20%">
                                        <?php else : ?>
                                            <img src="<?php echo base_url('docs/logo.png'); ?>" width="100%" height="20%">
                                        <?php endif; ?>
                                    </td>
                                    <td><?= $rs->travel_name; ?></td>
                                    <td><?= mb_substr($rs->travel_detail, 0, 20, 'UTF-8'); ?>...</td>
                                    <td class="limited-text"><?= $rs->travel_location; ?></td>
                                    <td><?= $rs->travel_by; ?></td>
                                    <td><?= date('d/m/Y H:i', strtotime($rs->travel_datesave . '+543 years')) ?> น.</td>
                                    <td>
                                        <label class="switch">
                                            <input class="form-check-input" type="checkbox" role="switch" id="flexSwitchCheck<?= $rs->travel_id; ?>" data-travel-id="<?= $rs->travel_id; ?>" <?= $rs->travel_status === 'show' ? 'checked' : ''; ?> onchange="updateTravelStatus<?= $rs->travel_id; ?>()">
                                            <span class="slider"></span>
                                        </label>
                                        <script>
                                            function updateTravelStatus<?= $rs->travel_id; ?>() {
                                                const travelId = <?= $rs->travel_id; ?>;
                                                const newStatus = document.getElementById('flexSwitchCheck<?= $rs->travel_id; ?>').checked ? 'show' : 'hide';

                                                // ส่งข้อมูลไปยังเซิร์ฟเวอร์ด้วย AJAX
                                                $.ajax({
                                                    type: 'POST',
                                                    url: 'travel_backend/updateTravelStatus',
                                                    data: {
                                                        travel_id: travelId,
                                                        new_status: newStatus
                                                    },
                                                    success: function(response) {
                                                        console.log(response);
                                                        // ทำอื่นๆตามต้องการ เช่น อัพเดตหน้าเว็บ
                                                    },
                                                    error: function(error) {
                                                        console.error(error);
                                                    }
                                                });
                                            }
                                        </script>
                                    </td>
                                    <td>
                                        <a href="<?= site_url('travel_backend/editing_Travel/' . $rs->travel_id); ?>"><i class="bi bi-pencil-square fa-lg "></i></a>
                                        <a href="#" role="button" onclick="confirmDelete(<?= $rs->travel_id; ?>);"><i class="bi bi-trash fa-lg "></i></a>
                                        <script>
                                            function confirmDelete(travel_id) {
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
                                                        window.location.href = "<?= site_url('travel_backend/del_Travel/'); ?>" + travel_id;
                                                    }
                                                });
                                            }
                                        </script>
                                        <!-- <a href="#" role="button" id="dropdownMenuLink" data-bs-toggle="dropdown" aria-expanded="false">
                                            <i class="bi bi-three-dots-vertical fa-lg "></i>
                                        </a>
                                        <ul class="dropdown-menu" aria-labelledby="dropdownMenuLink">
                                            <li><a class="dropdown-item" href="<?= $rs->travel_youtube; ?>" target="_blank">youtube</a></li>
                                            <li><a class="dropdown-item" href="https://www.google.com/maps?q=<?= $rs->travel_lat; ?>,<?= $rs->travel_long; ?>" target="_blank">Google Map</a></li>
                                            <li><a class="dropdown-item" href="<?= site_url('travel_backend/com/' . $rs->travel_id); ?>">ความคิดเห็น</a></li>
                                        </ul> -->
                                    </td>
                                </tr>
                            <?php
                                $Index++;
                            } ?>

                            <!-- <?php
                                    foreach ($quser as $rs) { ?>
                                <tr role="row">
                                    <td align="center"><?= $Index; ?></td>
                                    <td><img src="<?= base_url('docs/img/' . $rs->user_travel_img); ?>" width="120px" height="80px"></td>
                                    <td><?= $rs->user_travel_name; ?></td>
                                    <td class="limited-text"><?= $rs->user_travel_refer; ?></td>
                                    <td><?= mb_substr($rs->user_travel_detail, 0, 20, 'UTF-8'); ?>...</td>
                                    <td class="limited-text"><?= $rs->user_travel_location; ?></td>
                                    <td><?= $rs->user_travel_by; ?></td>
                                    <td><?= date('d/m/Y H:i', strtotime($rs->user_travel_datesave . '+543 years')) ?> น.</td>
                                    <td>
                                        <label class="switch">
                                            <input class="form-check-input" type="checkbox" role="switch" id="flexSwitchCheckUser<?= $rs->user_travel_id; ?>" data-user-travel-id="<?= $rs->user_travel_id; ?>" <?= $rs->user_travel_status === 'show' ? 'checked' : ''; ?> onchange="updateUserTravelStatus<?= $rs->user_travel_id; ?>()">
                                            <span class="slider"></span>
                                        </label>
                                        <script>
                                            function updateUserTravelStatus<?= $rs->user_travel_id; ?>() {
                                                const usertravelId = <?= $rs->user_travel_id; ?>;
                                                const newStatus = document.getElementById('flexSwitchCheckUser<?= $rs->user_travel_id; ?>').checked ? 'show' : 'hide';

                                                // ส่งข้อมูลไปยังเซิร์ฟเวอร์ด้วย AJAX
                                                $.ajax({
                                                    type: 'POST',
                                                    url: 'travel/updateUserTravelStatus',
                                                    data: {
                                                        user_travel_id: usertravelId,
                                                        new_status: newStatus
                                                    },
                                                    success: function(response) {
                                                        console.log(response);
                                                        // ทำอื่นๆตามต้องการ เช่น อัพเดตหน้าเว็บ
                                                    },
                                                    error: function(error) {
                                                        console.error(error);
                                                    }
                                                });
                                            }
                                        </script>
                                    </td>
                                    <td>
                                        <a href="<?php echo site_url('travel/editing_User_Travel/' . $rs->user_travel_id); ?>"><i class="bi bi-pencil-square fa-lg "></i></a>
                                        <a href="#" role="button" onclick="confirmDeleteUser('<?= $rs->user_travel_id; ?>');"><i class="bi bi-trash fa-lg "></i></a>
                                        <script>
                                            function confirmDeleteUser(user_travel_id) {
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
                                                        window.location.href = "<?= site_url('travel/del_User_Travel/'); ?>" + user_travel_id;
                                                    }
                                                });
                                            }
                                        </script>
                                        <a href="#" role="button" id="dropdownMenuLink" data-bs-toggle="dropdown" aria-expanded="false">
                                            <i class="bi bi-three-dots-vertical fa-lg "></i>
                                        </a>
                                        <ul class="dropdown-menu" aria-labelledby="dropdownMenuLink">
                                            <li><a class="dropdown-item" href="<?= $rs->user_travel_youtube; ?>" target="_blank">youtube</a></li>
                                            <li><a class="dropdown-item" href="https://www.google.com/maps?q=<?= $rs->user_travel_lat; ?>,<?= $rs->user_travel_long; ?>" target="_blank">google map</a></li>
                                            <li><a class="dropdown-item" href="<?= site_url('travel/com_user/' . $rs->user_travel_id); ?>">ความคิดเห็น</a></li>
                                        </ul>
                                    </td>
                                </tr>
                            <?php
                                        $Index++; // เพิ่มลำดับสำหรับ quser
                                    } ?> -->
                        </tbody>
                    </table>
                </div>
            </div>
        </div>



        <!-- <a class="btn btn-primary" href="<?= $rs->travel_youtube; ?>" target="_blank">เปิดคลิป</a>
    <a href="<?= $rs->travel_map; ?>" class="btn btn-info" target="_blank">เปิดแผนที่</a>
    <a href="#" class="btn btn-secondary btn-xs">ดูcomment</a> -->

        <!-- DataTales Example -->
        <!-- <div class="card shadow mb-4">
        <div class="card-header py-3">
            <h6 class="m-0 font-weight-bold text-primary">จัดการข้อมูลสถานที่ท่องเที่ยว</h6>
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-bordered" id="dataTable" cellspacing="0">
                    <thead>
                        <tr>
                            <th tabindex="0" rowspan="1" colspan="1" style="width: 3%;">ลำดับ</th>
                            <th tabindex="0" rowspan="1" colspan="1" style="width: 17%;">รูปภาพ</th>
                            <th tabindex="0" rowspan="1" colspan="1" style="width: 10%;">ชื่อ</th>
                            <th tabindex="0" rowspan="1" colspan="1" style="width: 10%;">อ้างอิง</th>
                            <th tabindex="0" rowspan="1" colspan="1" style="width: 10%;">รายละเอียด</th>
                            <th tabindex="0" rowspan="1" colspan="1" style="width: 10%;">สถานที่</th>
                            <th tabindex="0" rowspan="1" colspan="1" style="width: 10%;">ผู้อัพโหลด</th>
                            <th tabindex="0" rowspan="1" colspan="1" style="width: 10%;">วันที่อัพโหลด</th>
                            <th tabindex="0" rowspan="1" colspan="1" style="width: 10%;">สถานะ</th>
                            <th tabindex="0" rowspan="1" colspan="1" style="width: 10%;">จัดการ</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($query as $index => $rs) { ?>
                            <tr role="row">
                                <td align="center"><?= $index + 1; ?></td>
                                <td>รูปภาพหน้าปก : <img src="<?= base_url('docs/img/' . $rs->travel_img); ?>" width="180px" height="140px"> <br>
                                    รูปภาพเพิ่มเติม : <?php foreach (explode(',', $rs->additional_images) as $img) { ?>
                                        <img src="<?= base_url('docs/img/' . $img); ?>" width="100px" height="60px">
                                    <?php } ?>
                                </td>
                                </td>
                                <td><?= $rs->travel_name; ?></td>
                                <td><?= $rs->travel_refer; ?></td>
                                <td><?= mb_substr($rs->travel_detail, 0, 200, 'UTF-8'); ?>...</td>
                                <td><?= $rs->travel_location; ?> <br> เวลาเปิด-ปิด : <?= $rs->travel_timeopen; ?>น. ถึง <?= $rs->travel_timeclose; ?>น. <br> เปิด : <?= $rs->travel_date; ?> <br> เบอร์ติดต่อ : <?= $rs->travel_phone; ?></td>
                                <td><?= $rs->travel_by; ?></td>
                                <td><?= date('d/m/Y : H:i', strtotime($rs->travel_datesave . '+543 years')) ?></td>
                                <td>
                                    <label class="switch">
                                        <input class="form-check-input" type="checkbox" role="switch" id="flexSwitchCheck<?= $rs->travel_id; ?>" data-travel-id="<?= $rs->travel_id; ?>" <?= $rs->travel_status === 'show' ? 'checked' : ''; ?>>
                                        <span class="slider round"></span>
                                    </label>
                                    <script>
                                        // เลือกทุก Switch ด้วย class "form-check-input"
                                        const switchCheckboxes = document.querySelectorAll('.form-check-input');

                                        switchCheckboxes.forEach((checkbox) => {
                                            checkbox.addEventListener('change', function() {
                                                const travelId = this.getAttribute('data-travel-id');
                                                const newStatus = this.checked ? 'show' : 'hide';

                                                // ส่งข้อมูลไปยังเซิร์ฟเวอร์ด้วย AJAX
                                                $.ajax({
                                                    type: 'POST',
                                                    url: 'travel/updateTravelStatus', // แทนค่า '/your-controller/' ด้วย URL ของ Controller ที่คุณสร้าง
                                                    data: {
                                                        travel_id: travelId,
                                                        new_status: newStatus
                                                    },
                                                    success: function(response) {
                                                        console.log(response);
                                                        // ทำอย่างอื่นตามความต้องการ เช่น อัพเดตหน้าเว็บ
                                                    },
                                                    error: function(error) {
                                                        console.error(error);
                                                    }
                                                });
                                            });
                                        });
                                    </script>
                                </td>

                                <td>
                                    <a class="btn btn-primary" href="<?= $rs->travel_youtube; ?>" target="_blank">เปิดคลิป</a>
                                    <a href="<?= $rs->travel_map; ?>" class="btn btn-info" target="_blank">เปิดแผนที่</a>
                                    <a href="#" class="btn btn-secondary btn-xs">ดูcomment</a>
                                    <a href="<?php echo site_url('travel/edit/' . $rs->travel_id); ?>" class="btn btn-warning btn-xs">แก้ไข</a>
                                    <a class="btn btn-danger btn-xs" href="<?= site_url('travel/del_travel/' . $rs->travel_id); ?>" role="button" onclick="return confirm('ยืนยันการลบข้อมูล??');">ลบ</a>
                                </td>
                            </tr>
                        <?php } ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div> -->