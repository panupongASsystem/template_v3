<div class="d-flex justify-content-between">
    <!-- ส่วนอื่น ๆ ที่คุณต้องการจะแสดง -->
    <?php if ($qqueue->queue_status !== 'คิวได้ถูกยกเลิก' && $qqueue->queue_status !== 'คิวได้รับการยืนยัน') : ?>
        <?php if (!empty($latest_query)) : ?>
            <div id="popupCancel" class="popup">
                <div class="popup-content">
                    <h4>อัพเดตเรื่องจองคิวติดต่อราชการออนไลน์</h4>
                    <form action="<?php echo site_url('queue_backend/statusCancel/' . $latest_query->queue_detail_id); ?> " method="post" class="form-horizontal">
                        <br>
                        <div class="form-group row">
                            <div class="col-sm-2 control-label">สถานะเรื่องจองคิวติดต่อราชการออนไลน์</div>
                            <div class="col-sm-2">
                                <input type="text" name="queue_detail_status" disabled required class="form-control text-center" value="คิวได้ถูกยกเลิก">
                            </div>
                        </div>
                        <br>
                        <div class="form-group row">
                            <div class="col-sm-2 control-label">ข้อความ</div>
                            <div class="col-sm-6">
                                <input type="text" name="queue_detail_com" required class="form-control">
                            </div>
                        </div>
                        <br>
                        <div class="form-group row">
                            <div class="col-sm-2 control-label"></div>
                            <div class="col-sm-6">
                                <input type="hidden" name="queue_detail_case_id" required class="form-control" value="<?= $latest_query->queue_detail_case_id; ?>">
                            </div>
                        </div>
                        <br>
                        <div class="form-group row">
                            <div class="col-sm-2 control-label"></div>
                            <div class="col-sm-6">
                                <button type="submit" class="btn btn-success">บันทึกข้อมูล</button>
                                <a class="btn btn-danger" href="<?= site_url('queue_backend/detail/' . $qqueue->queue_id); ?>" role="button">ยกเลิกคิว</a>
                            </div>
                        </div>
                    </form>
                    <!-- <button class="close-button btn btn-danger" data-target="#popup<?= $rlatest_querys->queue_detail_case_id; ?>"><svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" fill="currentColor" class="bi bi-x-lg" viewBox="0 0 16 16">
                                        <path d="M2.146 2.854a.5.5 0 1 1 .708-.708L8 7.293l5.146-5.147a.5.5 0 0 1 .708.708L8.707 8l5.147 5.146a.5.5 0 0 1-.708.708L8 8.707l-5.146 5.147a.5.5 0 0 1-.708-.708L7.293 8 2.146 2.854Z" />
                                    </svg></button> -->
                </div>
            </div>
        <?php endif; ?>
    <?php endif; ?>

</div>

<a class="btn btn-danger" href="<?= site_url('queue_backend'); ?>" role="button">
    <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-arrow-left-circle" viewBox="0 0 16 16">
        <path fill-rule="evenodd" d="M1 8a7 7 0 1 0 14 0A7 7 0 0 0 1 8m15 0A8 8 0 1 1 0 8a8 8 0 0 1 16 0m-4.5-.5a.5.5 0 0 1 0 1H5.707l2.147 2.146a.5.5 0 0 1-.708.708l-3-3a.5.5 0 0 1 0-.708l3-3a.5.5 0 1 1 .708.708L5.707 7.5z" />
    </svg> ย้อนกลับ</a>
<div class="card shadow mb-4">
    <div class="card-header py-3">
        <h6 class="m-0 font-weight-bold text-primary">เรื่องจองคิวติดต่อราชการออนไลน์ คิวที่ : <?= $qqueue->queue_id; ?></h6>
    </div>
    <div class="card-body">
        <div class="table-responsive">
            <table id="newdataTables" class="table">
                <thead>
                    <tr>
                        <th style="width: 5%;">ลำดับ</th>
                        <th style="width: 15%;">สถานะ</th>
                        <th style="width: 45%;">ข้อความ</th>
                        <th style="width: 15%;">จัดการโดย</th>
                        <th style="width: 7%;">วันที่</th>
                        <th style="width: 13%;">จัดการcase</th>
                    </tr>
                </thead>
                <tbody>
                    <?php
                    $Index = 1;
                    foreach ($query as $rs) { ?>
                        <tr role="row" class="data-row">
                            <td align="center"><?= $Index; ?></td>
                            <td>
                                <p class="small-font" style="font-size: 15px; background-color:
                                    <?php if ($rs->queue_detail_status === 'คิวได้รับการยืนยัน') : ?>
                                        #DBFFDD;
                                    <?php elseif ($rs->queue_detail_status === 'คิวได้ถูกยกเลิก') : ?>
                                        #FFE3E3;
                                    <?php else : ?>
                                        #FFFBDC; /* สีเริ่มต้นหากไม่ตรงกับเงื่อนไขใดๆ */
                                    <?php endif; ?>
                                ; color:
                                    <?php if ($rs->queue_detail_status === 'คิวได้รับการยืนยัน') : ?>
                                        #00B73E;
                                    <?php elseif ($rs->queue_detail_status === 'คิวได้ถูกยกเลิก') : ?>
                                        #FF0202;
                                    <?php else : ?>
                                        #FFC700; /* สีเริ่มต้นหากไม่ตรงกับเงื่อนไขใดๆ */
                                    <?php endif; ?>
                                border: 1.3px solid
                                    <?php if ($rs->queue_detail_status === 'คิวได้รับการยืนยัน') : ?>
                                        #00B73E;
                                    <?php elseif ($rs->queue_detail_status === 'คิวได้ถูกยกเลิก') : ?>
                                        #FF0202;
                                    <?php else : ?>
                                        #FFC700; /* สีเริ่มต้นหากไม่ตรงกับเงื่อนไขใดๆ */
                                    <?php endif; ?>
                                ;
                                border-radius: 20px; /* เพิ่มเส้นโค้ง */
                                padding: 5px; /* เพิ่มขอบรอบตัวอักษร */
                                text-align: center; /* ปรับตำแหน่งข้อความให้อยู่กลาง */
                                ">
                                    <?= $rs->queue_detail_status; ?>
                                </p>
                            <td><?= empty($rs->queue_detail_com) ? $queue_topic : $rs->queue_detail_com; ?></td>
                            <td><?= $rs->queue_detail_by; ?></td>
                            <td><?= date('d/m/Y H:i', strtotime($rs->queue_detail_datesave . '+543 years')) ?> น.</td>
                            <td>
                                <?php if ($qqueue->queue_status !== 'คิวได้ถูกยกเลิก' && $qqueue->queue_status !== 'คิวได้รับการยืนยัน') : ?>
                                    <a class="btn btn-success update-complain-btn" data-target="#popupStatus">อัพเดตสถานะ</a>
                                <?php endif; ?>
                            </td>
                        </tr>
                        <?php
                        $Index++; // เพิ่มค่า $Index ในทุกๆ รอบของลูป
                        ?>
                    <?php } ?>
                </tbody>
            </table>
        </div>
    </div>
</div>


<div id="popupStatus" class="popup">
    <div class="popup-content">
        <h4>อัพเดตเรื่องจองคิวติดต่อราชการออนไลน์</h4>
        <form action=" <?php echo site_url('queue_backend/updatestatus/' . $latest_query->queue_detail_id); ?> " method="post" class="form-horizontal">
            <br>
            <div class="form-group row">
                <div class="col-sm-2 control-label">สถานะเรื่องจองคิวติดต่อราชการออนไลน์</div>
                <div class="col-sm-3">
                    <select class="form-control" name="queue_detail_status" required>
                        <?php if ($latest_query->queue_detail_status === 'รอยืนยันการจอง') : ?>
                            <!-- แสดงทุกอ็อปชันสำหรับสถานะ 'รอยืนยันการจอง' -->
                            <option class="text-center" value="คิวได้รับการยืนยัน">ยืนยันคิว</option>    
                            <option class="text-center" value="คิวได้ถูกยกเลิก">ยกเลิกคิว</option>
                        <?php elseif ($latest_query->queue_detail_status === 'คิวได้รับการยืนยัน') : ?>
                            <!-- ไม่แสดง 'รับเรื่องแล้ว' และ 'รอดำเนินการ' แต่แสดง 'กำลังดำเนินการ' -->
                            <option class="text-center" value="คิวได้รับการยืนยัน">ยืนยันคิว</option>
                        <?php endif; ?>
                    </select>
                </div>
            </div>
            <br>
            <div class="form-group row">
                <div class="col-sm-2 control-label">ข้อความ</div>
                <div class="col-sm-6">
                    <input type="text" name="queue_detail_com" required class="form-control">
                </div>
            </div>
            <br>
            <div class="form-group row">
                <div class="col-sm-2 control-label"></div>
                <div class="col-sm-6">
                    <input type="hidden" name="queue_detail_case_id" required class="form-control" value="<?= $rs->queue_detail_case_id; ?>">
                </div>
            </div>
            <br>
            <div class="form-group row">
                <div class="col-sm-2 control-label"></div>
                <div class="col-sm-6">
                    <button type="submit" class="btn btn-success">บันทึกข้อมูล</button>
                    <a class="btn btn-danger" href="<?= site_url('queue_backend/detail/' . $qqueue->queue_id); ?>" role="button">ยกเลิก</a>
                </div>
            </div>
        </form>
        <!-- <button class="close-button btn btn-danger" data-target="#popup<?= $rs->queue_detail_case_id; ?>"><svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" fill="currentColor" class="bi bi-x-lg" viewBox="0 0 16 16">
                                        <path d="M2.146 2.854a.5.5 0 1 1 .708-.708L8 7.293l5.146-5.147a.5.5 0 0 1 .708.708L8.707 8l5.147 5.146a.5.5 0 0 1-.708.708L8 8.707l-5.146 5.147a.5.5 0 0 1-.708-.708L7.293 8 2.146 2.854Z" />
                                    </svg></button> -->
    </div>
</div>