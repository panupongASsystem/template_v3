<a class="btn add-btn" href="<?= site_url('store_backend/adding_store'); ?>" role="button">
    <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-plus-circle" viewBox="0 0 16 16">
        <path d="M8 15A7 7 0 1 1 8 1a7 7 0 0 1 0 14zm0 1A8 8 0 1 0 8 0a8 8 0 0 0 0 16z" />
        <path d="M8 4a.5.5 0 0 1 .5.5v3h3a.5.5 0 0 1 0 1h-3v3a.5.5 0 0 1-1 0v-3h-3a.5.5 0 0 1 0-1h3v-3A.5.5 0 0 1 8 4z" />
    </svg> เพิ่มข้อมูล</a>
<a class="btn btn-light" href="<?= site_url('store_backend'); ?>" role="button">
    <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-arrow-clockwise" viewBox="0 0 16 16">
        <path fill-rule="evenodd" d="M8 3a5 5 0 1 0 4.546 2.914.5.5 0 0 1 .908-.417A6 6 0 1 1 8 2v1z" />
        <path d="M8 4.466V.534a.25.25 0 0 1 .41-.192l2.36 1.966c.12.1.12.284 0 .384L8.41 4.658A.25.25 0 0 1 8 4.466z" />
    </svg> Refresh Data</a>

<!-- <h5 class="border border-#f5f5f5 p-2 mb-2 font-black" style="background-color: #f5f5f5;">จัดการข้อมูลร้านอาหาร</h5> -->
<!-- DataTales Example -->
<div class="card shadow mb-4">
    <div class="card-header py-3">
        <h6 class="m-0 font-weight-bold text-black">จัดการข้อมูลร้านค้าและบริการ</h6>
    </div>
    <div class="card-body">
        <div class="table-responsive">
            <!-- เพิ่ม select option สำหรับเลือกการแสดงผล -->
            <div class="mb-3">
                <label for="displayOption" class="form-label">แสดงข้อมูล:</label>
                <select class="form-select" id="displayOption">
                    <option value="all">ทั้งหมด</option>
                    <option value="first">อบจ มุกดาหาร</option>
                    <option value="second">สมาชิก</option>
                </select>
            </div>

            <?php
            $Index = 1;
            ?>

            <table id="newdataTables" class="table">
                <thead>
                    <tr>
                        <th style="width: 5%;">ลำดับ</th>
                        <th style="width: 13%;">รูปภาพ</th>
                        <th style="width: 12%;">ประเภทร้าน</th>
                        <th style="width: 15%;">ชื่อ</th>
                        <th style="width: 20%;">รายละเอียด</th>
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
                                <?php if (!empty($rs->store_img)) : ?>
                                    <img src="<?php echo base_url('docs/img/' . $rs->store_img); ?>" width="120px" height="80px">
                                <?php else : ?>
                                    <img src="<?php echo base_url('docs/logo.png'); ?>" width="120px" height="80px">
                                <?php endif; ?>
                            </td>
                            <td><?= $rs->store_type; ?></td>
                            <td><?= $rs->store_name; ?></td>
                            <td class="limited-text"><?= $rs->store_detail; ?></td>
                            <td><?= $rs->store_by; ?></td>
                            <td><?= date('d/m/Y H:i', strtotime($rs->store_datesave . '+543 years')) ?> น.</td>
                            <td>
                                <label class="switch">
                                    <input class="form-check-input" type="checkbox" role="switch" id="flexSwitchCheck<?= $rs->store_id; ?>" data-store-id="<?= $rs->store_id; ?>" <?= $rs->store_status === 'show' ? 'checked' : ''; ?> onchange="updateStoreStatus<?= $rs->store_id; ?>()">
                                    <span class="slider"></span>
                                </label>
                                <script>
                                    function updateStoreStatus<?= $rs->store_id; ?>() {
                                        const storeId = <?= $rs->store_id; ?>;
                                        const newStatus = document.getElementById('flexSwitchCheck<?= $rs->store_id; ?>').checked ? 'show' : 'hide';

                                        // ส่งข้อมูลไปยังเซิร์ฟเวอร์ด้วย AJAX
                                        $.ajax({
                                            type: 'POST',
                                            url: 'store_backend/updateStoreStatus',
                                            data: {
                                                store_id: storeId,
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
                                <a href="<?= site_url('store_backend/editing_store/' . $rs->store_id); ?>"><i class="bi bi-pencil-square fa-lg "></i></a>
                                <a href="#" role="button" onclick="confirmDelete('<?= $rs->store_id; ?>');"><i class="bi bi-trash fa-lg "></i></a>
                                <script>
                                    function confirmDelete(store_id) {
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
                                                window.location.href = "<?= site_url('store_backend/del_store/'); ?>" + store_id;
                                            }
                                        });
                                    }
                                </script>
                                <a href="#" role="button" id="dropdownMenuLink" data-bs-toggle="dropdown" aria-expanded="false">
                                    <i class="bi bi-three-dots-vertical fa-lg "></i>
                                </a>
                                <ul class="dropdown-menu" aria-labelledby="dropdownMenuLink">
                                    <li><a class="dropdown-item" href="https://www.google.com/maps?q=<?= $rs->store_lat; ?>,<?= $rs->store_long; ?>" target="_blank">google map</a></li>
                                    <li><a class="dropdown-item" href="<?= site_url('store_backend/com_store/' . $rs->store_id); ?>">ความคิดเห็น</a></li>
                                </ul>
                            </td>
                        </tr>
                    <?php
                        $Index++;
                    } ?>

                    <?php
                    foreach ($quser as $rs) { ?>
                        <tr role="row">
                            <td align="center"><?= $Index; ?></td>
                            <td><img src="<?= base_url('docs/img/' . $rs->user_store_img); ?>" width="120px" height="80px"></td>
                            <td><?= $rs->user_store_type; ?></td>
                            <td><?= $rs->user_store_name; ?></td>
                            <td class="limited-text"><?= $rs->user_store_detail; ?></td>
                            <td><?= $rs->user_store_by; ?></td>
                            <td><?= date('d/m/Y H:i', strtotime($rs->user_store_datesave . '+543 years')) ?> น.</td>
                            <td>
                                <label class="switch">
                                    <input class="form-check-input" type="checkbox" role="switch" id="flexSwitchCheckUser<?= $rs->user_store_id; ?>" data-user-store-id="<?= $rs->user_store_id; ?>" <?= $rs->user_store_status === 'show' ? 'checked' : ''; ?> onchange="updateUserStoreStatus<?= $rs->user_store_id; ?>()">
                                    <span class="slider"></span>
                                </label>
                                <script>
                                    function updateUserStoreStatus<?= $rs->user_store_id; ?>() {
                                        const userstoreId = <?= $rs->user_store_id; ?>;
                                        const newStatus = document.getElementById('flexSwitchCheckUser<?= $rs->user_store_id; ?>').checked ? 'show' : 'hide';

                                        // ส่งข้อมูลไปยังเซิร์ฟเวอร์ด้วย AJAX
                                        $.ajax({
                                            type: 'POST',
                                            url: 'store/updateUserStoreStatus',
                                            data: {
                                                user_store_id: userstoreId,
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
                                <a href="<?php echo site_url('store/editing_user_store/' . $rs->user_store_id); ?>"><i class="bi bi-pencil-square fa-lg "></i></a>
                                <a href="#" role="button" onclick="confirmDeleteUser('<?= $rs->user_store_id; ?>');"><i class="bi bi-trash fa-lg "></i></a>
                                <script>
                                    function confirmDeleteUser(user_store_id) {
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
                                                window.location.href = "<?= site_url('store/del_user_store/'); ?>" + user_store_id;
                                            }
                                        });
                                    }
                                </script>
                                <a href="#" role="button" id="dropdownMenuLink" data-bs-toggle="dropdown" aria-expanded="false">
                                    <i class="bi bi-three-dots-vertical fa-lg "></i>
                                </a>
                                <ul class="dropdown-menu" aria-labelledby="dropdownMenuLink">
                                    <li><a class="dropdown-item" href="https://www.google.com/maps?q=<?= $rs->user_store_lat; ?>,<?= $rs->user_store_long; ?>" target="_blank">google map</a></li>
                                    <li><a class="dropdown-item" href="<?= site_url('store/com_store_user/' . $rs->user_store_id); ?>">ความคิดเห็น</a></li>
                                </ul>
                            </td>
                        </tr>
                    <?php
                        $Index++; // เพิ่มลำดับสำหรับ quser
                    } ?>
                </tbody>
            </table>
        </div>
    </div>
</div>