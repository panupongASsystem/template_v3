<div class="ml-3 mr-3">
    <a class="btn add-btn" href="<?= site_url('video_backend/adding'); ?>">
        <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-plus-circle" viewBox="0 0 16 16">
            <path d="M8 15A7 7 0 1 1 8 1a7 7 0 0 1 0 14zm0 1A8 8 0 1 0 8 0a8 8 0 0 0 0 16z" />
            <path d="M8 4a.5.5 0 0 1 .5.5v3h3a.5.5 0 0 1 0 1h-3v3a.5.5 0 0 1-1 0v-3h-3a.5.5 0 0 1 0-1h3v-3A.5.5 0 0 1 8 4z" />
        </svg> เพิ่มข้อมูล</a>
    <a class="btn btn-light" href="<?= site_url('video_backend'); ?>">
        <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-arrow-clockwise" viewBox="0 0 16 16">
            <path fill-rule="evenodd" d="M8 3a5 5 0 1 0 4.546 2.914.5.5 0 0 1 .908-.417A6 6 0 1 1 8 2v1z" />
            <path d="M8 4.466V.534a.25.25 0 0 1 .41-.192l2.36 1.966c.12.1.12.284 0 .384L8.41 4.658A.25.25 0 0 1 8 4.466z" />
        </svg> Refresh Data</a>
    <!-- DataTales Example -->
    <div class="card shadow mb-4">
        <div class="card-header py-3">
            <h6 class="m-0 font-weight-bold text-black">จัดการข้อมูลวิดีทัศน์</h6>
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
                            <th style="width: 30%;">ชื่อ</th>
                            <th style="width: 30%;">วิดีโอ</th>
                            <th style="width: 13%;">อัพโหลด</th>
                            <th style="width: 7%;">วันที่</th>
                            <th style="width: 10%;">แสดงที่หน้าหลัก</th>
                            <th style="width: 7%;">จัดการ</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php
                        $Index = 1;
                        foreach ($query as $rs) {
                        ?>
                            <tr role="row">
                                <td align="center"><?= $Index; ?></td>
                                <td><?= $rs->video_name; ?></td>
                                <td>
                                    <?php if (!empty($rs->video_link)): ?>
                                        <a href="<?= $rs->video_link; ?>" target="_blank" rel="noopener noreferrer">
                                            <?= $rs->video_link; ?>
                                        </a>
                                    <?php elseif (!empty($rs->video_video)): ?>
                                        <video width="80%" height="150" controls>
                                            <source src="<?= base_url('docs/video/' . $rs->video_video); ?>" type="video/mp4">
                                            <?= $rs->video_video; ?>
                                        </video>
                                    <?php else: ?>
                                        <span class="text-muted">ไม่มีข้อมูลวิดีโอ</span>
                                    <?php endif; ?>
                                </td>

                                <td><?= $rs->video_by; ?></td>
                                <td><?= date('d/m/Y H:i', strtotime($rs->video_datesave . '+543 years')) ?> น.</td>
                                <td style="padding-left: 40px;">
                                    <label class="switch">
                                        <input class="form-check-input" type="checkbox" role="switch"
                                            id="flexSwitchCheck<?= $rs->video_id; ?>"
                                            data-video-id="<?= $rs->video_id; ?>"
                                            <?= $rs->video_status === 'show' ? 'checked' : ''; ?>
                                            onchange="updateVideoStatus(this)">
                                        <span class="slider"></span>
                                    </label>

                                    <script>
                                        function updateVideoStatus(checkbox) {
                                            const videoId = checkbox.getAttribute('data-video-id');
                                            const newStatus = checkbox.checked ? 'show' : 'hide';

                                            // นับจำนวน checkbox ที่ถูกเลือกในปัจจุบัน
                                            const checkedCount = document.querySelectorAll('.form-check-input:checked').length;

                                            // ถ้าไม่มี checkbox ใดถูกเลือกและกำลังจะยกเลิกการเลือก
                                            if (checkedCount === 0 && newStatus === 'hide') {
                                                showAlert();
                                                checkbox.checked = true;
                                                return;
                                            }

                                            // ถ้ากำลังจะเลือกและมีการเลือกเกิน 3 อัน
                                            if (newStatus === 'show' && checkedCount > 4) {
                                                Swal.fire({
                                                    title: 'แจ้งเตือน',
                                                    text: 'สามารถแสดงวิดีโอได้สูงสุด 4 รายการเท่านั้น',
                                                    icon: 'warning',
                                                    confirmButtonText: 'ตกลง'
                                                });
                                                checkbox.checked = false;
                                                return;
                                            }

                                            // ถ้าสถานะเป็น show และจำนวนที่เลือกยังไม่เกิน 4
                                            if (newStatus === 'show' && checkedCount <= 4) {
                                                // อัพเดทสถานะของ checkbox ที่ถูกคลิก
                                                $.ajax({
                                                    type: 'POST',
                                                    url: 'video_backend/updateVideoStatus',
                                                    data: {
                                                        video_id: videoId,
                                                        new_status: newStatus
                                                    },
                                                    success: function(response) {
                                                        console.log('Updated status for ID:', videoId);
                                                    },
                                                    error: function(error) {
                                                        console.error('Error updating status for ID:', videoId, error);
                                                    }
                                                });
                                            } else if (newStatus === 'hide') {
                                                // ถ้าเป็นการ hide สามารถทำได้เลย
                                                $.ajax({
                                                    type: 'POST',
                                                    url: 'video_backend/updateVideoStatus',
                                                    data: {
                                                        video_id: videoId,
                                                        new_status: newStatus
                                                    },
                                                    success: function(response) {
                                                        console.log('Updated status for ID:', videoId);
                                                    },
                                                    error: function(error) {
                                                        console.error('Error updating status for ID:', videoId, error);
                                                    }
                                                });
                                            }
                                        }

                                        function showAlert() {
                                            Swal.fire({
                                                title: 'แจ้งเตือน',
                                                text: 'ต้องแสดงวิดีโออย่างน้อย 1 รายการ',
                                                icon: 'warning',
                                                confirmButtonText: 'ตกลง'
                                            });
                                        }
                                    </script>
                                </td>
                                <td>
                                    <a href="<?= site_url('video_backend/editing/' . $rs->video_id); ?>"><i class="bi bi-pencil-square fa-lg "></i></a>
                                    <a href="#" role="button" onclick="confirmDelete(<?= $rs->video_id; ?>);"><i class="bi bi-trash fa-lg "></i></a>
                                    <script>
                                        function confirmDelete(video_id) {
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
                                                    window.location.href = "<?= site_url('video_backend/del_video/'); ?>" + video_id;
                                                }
                                            });
                                        }
                                    </script>

                                </td>
                            </tr>
                        <?php
                            $Index++; // เพิ่มค่า Index ทีละ 1 ทุกรอบของลูป
                        }
                        ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>