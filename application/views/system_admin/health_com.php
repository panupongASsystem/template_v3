<h5 class="border border-#f5f5f5 p-2 mb-2 font-black" style="background-color: #f5f5f5;">จัดการความคิดเห็น </h5>

<div class="row">
    <div class="col-6">
        <p class="path"><a class="path" href="<?php echo site_url('health_backend'); ?>">Home</a> / <a class="path" href="<?php echo site_url('health_backend'); ?>"><?= $health->health_name; ?></a> / <a class="path" href="#">ความคิดเห็น</a></p>
    </div>
    <div class="col-6">
        <div class="d-flex justify-content-end">
            <input type="text" id="searchInput" placeholder="ค้นหา...">&nbsp;
            <button id="searchButton" class="btn btn-primary">ค้นหา</button>
        </div>
    </div>
</div>

<table id="newdataTables" class="custom-table">
    <thead>
        <tr>
            <th style="width: 3%;">ลำดับ</th>
            <th style="width: 60%;">ความคิดเห็น</th>
            <th style="width: 15%;">โดย</th>
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
                <td class="limited-text"><?= $rs->health_com_msg; ?></td>
                <td><?= $rs->health_com_by; ?></td>
                <td><?= date('d/m/Y H:i', strtotime($rs->health_com_datesave . '+543 years')) ?> น.</td>
                <td><a href="javascript:void(0);" onclick="confirmDelete(<?= $rs->health_com_id; ?>);" style="margin-left: 15%;"><i class="bi bi-trash fa-lg "></i></td>
                <script>
                    function confirmDelete(health_com_id) {
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
                                fetch("<?= site_url('health_backend/del_com/'); ?>" + health_com_id)
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
            $comReplyData = $rs->com_reply_data; // ข้อมูลความคิดเห็นตอบกลับที่เกี่ยวข้อง
            if (!empty($comReplyData)) {
                $ReplyIndex = 1; // ใช้สำหรับความคิดเห็นตอบกลับ
                foreach ($comReplyData as $reply) { ?>
                    <tr class="reply-row" style="display: none;">
                        <td align="center"><?= $ReplyIndex; ?></td>
                        <td class="reply"><?= $reply->health_com_reply_msg; ?></td>
                        <td><?= $reply->health_com_reply_by; ?></td>
                        <td><?= date('d/m/Y H:i', strtotime($reply->health_com_reply_datesave . '+543 years')) ?> น.</td>
                        <td><a href="javascript:void(0);" onclick="confirmDeleteReply(<?= $reply->health_com_reply_id; ?>);" style="margin-left: 15%;"><i class="bi bi-trash fa-lg "></i></td>
                        <script>
                            function confirmDeleteReply(replyId) {
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
                                        fetch("<?= site_url('health_backend/del_com_reply/'); ?>" + replyId)
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