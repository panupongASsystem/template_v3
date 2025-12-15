<!-- <a class="btn add-btn" href="<?= site_url('Q_a_backend/adding'); ?>" role="button">
    <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-plus-circle" viewBox="0 0 16 16">
        <path d="M8 15A7 7 0 1 1 8 1a7 7 0 0 1 0 14zm0 1A8 8 0 1 0 8 0a8 8 0 0 0 0 16z" />
        <path d="M8 4a.5.5 0 0 1 .5.5v3h3a.5.5 0 0 1 0 1h-3v3a.5.5 0 0 1-1 0v-3h-3a.5.5 0 0 1 0-1h3v-3A.5.5 0 0 1 8 4z" />
    </svg> เพิ่มข้อมูล</a> -->
<a class="btn btn-light" href="<?= site_url('Q_a_backend'); ?>" role="button">
    <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-arrow-clockwise" viewBox="0 0 16 16">
        <path fill-rule="evenodd" d="M8 3a5 5 0 1 0 4.546 2.914.5.5 0 0 1 .908-.417A6 6 0 1 1 8 2v1z" />
        <path d="M8 4.466V.534a.25.25 0 0 1 .41-.192l2.36 1.966c.12.1.12.284 0 .384L8.41 4.658A.25.25 0 0 1 8 4.466z" />
    </svg> Refresh Data</a>

<!-- <h5 class="border border-#f5f5f5 p-2 mb-2 font-black" style="background-color: #f5f5f5;">จัดการข้อมูลข่าวสารประจำเดือน</h5> -->
<!-- DataTales Example -->
<div class="card shadow mb-4">
    <div class="card-header py-3">
        <h6 class="m-0 font-weight-bold text-black">จัดการข้อมูลกระทู้ถาม-ตอบ</h6>
    </div>
    <div class="card-body">
        <div class="table-responsive">

            <table id="newdataTables" class="custom-table">
                <thead>
                    <tr>
                        <th style="width: 3%;">ลำดับ</th>
                        <th style="width: 30%;">ความคิดเห็น</th>
                        <th style="width: 10%;">โดย</th>
                        <th style="width: 15%;">E-mail</th>
						<th style="width: 10%;">IP Adress</th>
						<th style="width: 10%;">ประเทศ</th>
                        <th style="width: 7%;">วันที่</th>
                        <th style="width: 3%;">จัดการ</th>
                        <th style="width: 12%;">สถานะ</th>
                    </tr>
                </thead>
                <tbody>
                    <?php
                    $Index = 1; // ใช้สำหรับความคิดเห็นหลัก
                    ?>
                    <?php foreach ($rsCom as $rs) { ?>
                        <tr role="row" class="comment-row">
                            <td align="center"><?= $Index; ?></td>
                            <td class="limited-text"><?= $rs->q_a_msg; ?></td>
                            <td><?= $rs->q_a_by; ?></td>
                            <td><?= $rs->q_a_email; ?></td>
							<td><?= $rs->q_a_ip; ?></td>
							<td><?= $rs->q_a_country; ?></td>
                            <td><?= date('d/m/Y H:i', strtotime($rs->q_a_datesave . '+543 years')) ?> น.</td>
                            <td><a href="javascript:void(0);" onclick="confirmDelete(<?= $rs->q_a_id; ?>);" style="margin-left: 15%;"><i class="bi bi-trash fa-lg "></i></td>
                            <script>
                                function confirmDelete(q_a_id) {
                                    Swal.fire({
                                        title: 'กดเพื่อยืนยัน?',
                                        text: "คุณจะไม่สามารถกู้คืนได้อีก!",
                                        icon: 'warning',
                                        showCancelButton: true,
                                        confirmButtonColor: '#3085d6',
                                        cancelButtonColor: '#d33',
                                        confirmButtonText: 'ใช่, ต้องการลบ!',
                                        cancelButtonText: 'ยกเลิก' // เปลี่ยนข้อความปุ่ม Cancel เป็นภาษาไทย
                                    }).then((result) => {
                                        if (result.isConfirmed) {
                                            // ส่งคำขอ Ajax เพื่อลบข้อมูล
                                            fetch("<?= site_url('Q_a_backend/del_com/'); ?>" + q_a_id)
                                                .then(response => response.json())
                                                .then(data => {
                                                    if (data.success) {
                                                        // หากการลบสำเร็จ รีเฟรชหน้า
                                                        window.location.reload();
                                                    } else {
                                                        // หากมีข้อผิดพลาดแสดงข้อความ
                                                        Swal.fire({
                                                            icon: 'error',
                                                            title: 'เกิดข้อผิดพลาด',
                                                            text: 'ไม่สามารถลบข้อมูลได้'
                                                        });
                                                    }
                                                });
                                        }
                                    });
                                }
                            </script>
                            <td>
                                <?php if (!empty($rs->com_reply_data)) : ?>
                                    <!-- ปุ่ม "แสดงความคิดเห็นตอบกลับ" ด้านล่าง -->
                                    <button class="btn btn-info btn-sm show-reply-btn" style="font-size: 13px;">
                                        แสดงความคิดเห็นตอบกลับ</button>
                                <?php endif; ?>
                            </td>
                        </tr>
                        <!-- เริ่มต้นแสดงข้อมูลความคิดเห็นตอบกลับ -->
                        <?php
                        $com_reply_data = $rs->com_reply_data; // ข้อมูลความคิดเห็นตอบกลับที่เกี่ยวข้อง
                        if (!empty($com_reply_data)) {
                            $ReplyIndex = 1; // ใช้สำหรับความคิดเห็นตอบกลับ
                            foreach ($com_reply_data as $reply) { ?>
                                <tr class="reply-row" style="display: none;">
                                    <td align="center"><?= $ReplyIndex; ?></td>
                                    <td class="reply"><?= $reply->q_a_reply_detail; ?></td>
                                    <td><?= $reply->q_a_reply_by; ?></td>
                                    <td><?= $reply->q_a_reply_email; ?></td>
									<td><?= $reply->q_a_reply_ip; ?></td>
									<td><?= $reply->q_a_reply_country; ?></td>
                                    <td><?= date('d/m/Y H:i', strtotime($reply->q_a_reply_datesave . '+543 years')) ?> น.</td>
                                    <td><a href="javascript:void(0);" onclick="confirmDeleteReply(<?= $reply->q_a_reply_id; ?>);" style="margin-left: 15%;"><i class="bi bi-trash fa-lg "></i></td>
                                    <script>
                                        function confirmDeleteReply(q_a_reply_id) {
                                            Swal.fire({
                                                title: 'กดเพื่อยืนยัน?',
                                                text: "คุณจะไม่สามารถกู้คืนได้อีก!",
                                                icon: 'warning',
                                                showCancelButton: true,
                                                confirmButtonColor: '#3085d6',
                                                cancelButtonColor: '#d33',
                                                confirmButtonText: 'ใช่, ต้องการลบ!',
                                                cancelButtonText: 'ยกเลิก' // เปลี่ยนข้อความปุ่ม Cancel เป็นภาษาไทย
                                            }).then((result) => {
                                                if (result.isConfirmed) {
                                                    // ส่งคำขอ Ajax เพื่อลบข้อมูล
                                                    fetch("<?= site_url('Q_a_backend/del_com_reply/'); ?>" + q_a_reply_id)
                                                        .then(response => response.json())
                                                        .then(data => {
                                                            if (data.success) {
                                                                // หากการลบสำเร็จ รีเฟรชหน้า
                                                                window.location.reload();
                                                            } else {
                                                                // หากมีข้อผิดพลาดแสดงข้อความ
                                                                Swal.fire({
                                                                    icon: 'error',
                                                                    title: 'เกิดข้อผิดพลาด',
                                                                    text: 'ไม่สามารถลบข้อมูลได้'
                                                                });
                                                            }
                                                        });
                                                }
                                            });
                                        }
                                    </script>
                                </tr>
                        <?php
                                $ReplyIndex++; // เพิ่มลำดับสำหรับความคิดเห็นตอบกลับ
                            }
                        }
                        ?>
                        <!-- จบการแสดงข้อมูลความคิดเห็นตอบกลับ -->
                    <?php
                        $Index++; // เพิ่มลำดับสำหรับความคิดเห็นหลัก
                    } ?>
                </tbody>
            </table>
        </div>
    </div>
</div>