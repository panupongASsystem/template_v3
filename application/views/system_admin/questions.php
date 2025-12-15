<a class="btn add-btn insert-questions-btn" data-target="#popupInsert">
    <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-plus-circle" viewBox="0 0 16 16">
        <path d="M8 15A7 7 0 1 1 8 1a7 7 0 0 1 0 14zm0 1A8 8 0 1 0 8 0a8 8 0 0 0 0 16z" />
        <path d="M8 4a.5.5 0 0 1 .5.5v3h3a.5.5 0 0 1 0 1h-3v3a.5.5 0 0 1-1 0v-3h-3a.5.5 0 0 1 0-1h3v-3A.5.5 0 0 1 8 4z" />
    </svg> เพิ่มข้อมูล</a>
<div id="popupInsert" class="popup">
    <div class="popup-content">
        <h4>เพิ่มข้อมูลคำถามที่พบบ่อย</h4>
        <form action="<?php echo site_url('Questions_backend/add'); ?> " method="post" class="form-horizontal">
            <div class="form-group row">
                <div class="col-sm-2 control-label">คำถาม</div>
                <div class="col-sm-5">
                    <input type="text" name="questions_ask" required class="form-control">
                </div>
            </div>
            <br>
            <div class="form-group row">
                <div class="col-sm-2 control-label">คำตอบ</div>
                <div class="col-sm-5">
                    <input type="text" name="questions_reply" required class="form-control">
                </div>
            </div>
            <br>
            <div class="form-group row">
                <div class="col-sm-2 control-label"></div>
                <div class="col-sm-5">
                    <button type="submit" class="btn btn-success">บันทึกข้อมูล</button>
                    <a class="btn btn-danger" href="<?= site_url('Questions_backend'); ?>" role="button">ยกเลิก</a>
                </div>
            </div>
        </form>
    </div>
</div>
<a class="btn btn-light" href="<?= site_url('Questions_backend'); ?>" role="button">
    <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-arrow-clockwise" viewBox="0 0 16 16">
        <path fill-rule="evenodd" d="M8 3a5 5 0 1 0 4.546 2.914.5.5 0 0 1 .908-.417A6 6 0 1 1 8 2v1z" />
        <path d="M8 4.466V.534a.25.25 0 0 1 .41-.192l2.36 1.966c.12.1.12.284 0 .384L8.41 4.658A.25.25 0 0 1 8 4.466z" />
    </svg>Refresh Data</a>

<!-- DataTales Example -->
<div class="card shadow mb-4">
    <div class="card-header py-3">
        <h6 class="m-0 font-weight-bold text-black">จัดการข้อมูลคำถามที่พบบ่อย</h6>
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
                        <th style="width: 35%;">คำถาม</th>
                        <th style="width: 35%;">คำตอบ</th>
                        <th style="width: 10%;">วันที่</th>
                        <th style="width: 7%;">วันที่</th>
                        <th style="width: 10%;">จัดการ</th>
                    </tr>
                </thead>
                <tbody>
                    <?php
                    foreach ($query as $rs) { ?>
                        <tr role="row">
                            <td align="center"><?= $Index; ?></td>
                            <td><?php echo $rs->questions_ask; ?></td>
                            <td><?php echo $rs->questions_reply; ?></td>
                            <td><?php echo $rs->questions_by; ?></td>
                            <td><?= date('d/m/Y H:i', strtotime($rs->questions_datesave . '+543 years')) ?> น.</td>
                            <td>
                                <a href="<?= site_url('Questions_backend/editing/' . $rs->questions_id); ?>"><i class="bi bi-pencil-square fa-lg "></i></a>
                                <a href="#" role="button" onclick="confirmDelete(<?= $rs->questions_id; ?>);"><i class="bi bi-trash fa-lg "></i></a>
                                <script>
                                    function confirmDelete(questions_id) {
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
                                                window.location.href = "<?= site_url('Questions_backend/del/'); ?>" + questions_id;
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