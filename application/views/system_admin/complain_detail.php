<div class="d-flex justify-content-between">
    <!-- ส่วนอื่น ๆ ที่คุณต้องการจะแสดง -->
    <?php if ($qcomplain->complain_status !== 'ยกเลิก' && $qcomplain->complain_status !== 'ดำเนินการเรียบร้อย') : ?>
        <?php if (!empty($latest_query)) : ?>
            <a class="btn btn-danger ml-auto cancel-complain-btn" data-target="#popupCancel">ยกเลิก</a>
            <div id="popupCancel" class="popup">
                <div class="popup-content">
                    <h4>อัพเดตเรื่องร้องเรียน</h4>
                    <form action="<?php echo site_url('Complain_backend/statusCancel/' . $latest_query->complain_detail_id); ?> " method="post" class="form-horizontal">
                        <br>
                        <div class="form-group row">
                            <div class="col-sm-2 control-label">สถานะเรื่องร้องเรียน</div>
                            <div class="col-sm-2">
                                <input type="text" name="complain_detail_status" disabled required class="form-control text-center" value="ยกเลิก">
                            </div>
                        </div>
                        <br>
                        <div class="form-group row">
                            <div class="col-sm-2 control-label">ข้อความ</div>
                            <div class="col-sm-6">
                                <input type="text" name="complain_detail_com" required class="form-control">
                            </div>
                        </div>
                        <br>
                        <div class="form-group row">
                            <div class="col-sm-2 control-label"></div>
                            <div class="col-sm-6">
                                <input type="hidden" name="complain_detail_case_id" required class="form-control" value="<?= $latest_query->complain_detail_case_id; ?>">
                            </div>
                        </div>
                        <br>
                        <div class="form-group row">
                            <div class="col-sm-2 control-label"></div>
                            <div class="col-sm-6">
                                <button type="submit" class="btn btn-success">บันทึกข้อมูล</button>
                                <a class="btn btn-danger" href="<?= site_url('complain_backend/detail/' . $qcomplain->complain_id); ?>" role="button">ยกเลิก</a>
                            </div>
                        </div>
                    </form>
                    <!-- <button class="close-button btn btn-danger" data-target="#popup<?= $rlatest_querys->complain_detail_case_id; ?>"><svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" fill="currentColor" class="bi bi-x-lg" viewBox="0 0 16 16">
                                        <path d="M2.146 2.854a.5.5 0 1 1 .708-.708L8 7.293l5.146-5.147a.5.5 0 0 1 .708.708L8.707 8l5.147 5.146a.5.5 0 0 1-.708.708L8 8.707l-5.146 5.147a.5.5 0 0 1-.708-.708L7.293 8 2.146 2.854Z" />
                                    </svg></button> -->
                </div>
            </div>
        <?php endif; ?>
    <?php endif; ?>

</div>

<a class="btn btn-danger" href="<?= site_url('Complain_backend'); ?>" role="button">
    <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-arrow-left-circle" viewBox="0 0 16 16">
        <path fill-rule="evenodd" d="M1 8a7 7 0 1 0 14 0A7 7 0 0 0 1 8m15 0A8 8 0 1 1 0 8a8 8 0 0 1 16 0m-4.5-.5a.5.5 0 0 1 0 1H5.707l2.147 2.146a.5.5 0 0 1-.708.708l-3-3a.5.5 0 0 1 0-.708l3-3a.5.5 0 1 1 .708.708L5.707 7.5z" />
    </svg> ย้อนกลับ</a>
<div class="card shadow mb-4">
    <div class="card-header py-3">
        <h6 class="m-0 font-weight-bold text-primary">เรื่องร้องเรียน case ที่ : <?= $qcomplain->complain_id; ?></h6>
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
                                    <?php if ($rs->complain_detail_status === 'รับเรื่องแล้ว') : ?>
                                        #D9EAFF;
                                    <?php elseif ($rs->complain_detail_status === 'กำลังดำเนินการ') : ?>
                                        #CFD7FE;
                                    <?php elseif ($rs->complain_detail_status === 'รอดำเนินการ') : ?>
                                        #FFECE7;
                                    <?php elseif ($rs->complain_detail_status === 'ดำเนินการเรียบร้อย') : ?>
                                        #DBFFDD;
                                    <?php elseif ($rs->complain_detail_status === 'ยกเลิก') : ?>
                                        #FFE3E3;
                                    <?php else : ?>
                                        #FFFBDC; /* สีเริ่มต้นหากไม่ตรงกับเงื่อนไขใดๆ */
                                    <?php endif; ?>
                                ; color:
                                    <?php if ($rs->complain_detail_status === 'รับเรื่องแล้ว') : ?>
                                        #4C97EE;
                                    <?php elseif ($rs->complain_detail_status === 'กำลังดำเนินการ') : ?>
                                        #3D5AF1;
                                    <?php elseif ($rs->complain_detail_status === 'รอดำเนินการ') : ?>
                                        #E05A33;
                                    <?php elseif ($rs->complain_detail_status === 'ดำเนินการเรียบร้อย') : ?>
                                        #00B73E;
                                    <?php elseif ($rs->complain_detail_status === 'ยกเลิก') : ?>
                                        #FF0202;
                                    <?php else : ?>
                                        #FFC700; /* สีเริ่มต้นหากไม่ตรงกับเงื่อนไขใดๆ */
                                    <?php endif; ?>
                                border: 1.3px solid
                                    <?php if ($rs->complain_detail_status === 'รับเรื่องแล้ว') : ?>
                                        #4C97EE;
                                    <?php elseif ($rs->complain_detail_status === 'กำลังดำเนินการ') : ?>
                                        #3D5AF1;
                                    <?php elseif ($rs->complain_detail_status === 'รอดำเนินการ') : ?>
                                        #E05A33;
                                    <?php elseif ($rs->complain_detail_status === 'ดำเนินการเรียบร้อย') : ?>
                                        #00B73E;
                                    <?php elseif ($rs->complain_detail_status === 'ยกเลิก') : ?>
                                        #FF0202;
                                    <?php else : ?>
                                        #FFC700; /* สีเริ่มต้นหากไม่ตรงกับเงื่อนไขใดๆ */
                                    <?php endif; ?>
                                ;
                                border-radius: 20px; /* เพิ่มเส้นโค้ง */
                                padding: 5px; /* เพิ่มขอบรอบตัวอักษร */
                                text-align: center; /* ปรับตำแหน่งข้อความให้อยู่กลาง */
                                ">
                                    <?= $rs->complain_detail_status; ?>
                                </p>
                            <td><?= empty($rs->complain_detail_com) ? $complain_topic : $rs->complain_detail_com; ?></td>
                            <td><?= $rs->complain_detail_by; ?></td>
                            <td><?= date('d/m/Y H:i', strtotime($rs->complain_detail_datesave . '+543 years')) ?> น.</td>
                            <td>
                                <?php if ($qcomplain->complain_status !== 'ยกเลิก' && $qcomplain->complain_status !== 'ดำเนินการเรียบร้อย') : ?>
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
        <h4>อัพเดตเรื่องร้องเรียน</h4>
        <form action=" <?php echo site_url('Complain_backend/updatestatus/' . $latest_query->complain_detail_id); ?> " method="post" class="form-horizontal">
            <br>
            <div class="form-group row">
                <div class="col-sm-2 control-label">สถานะเรื่องร้องเรียน</div>
                <div class="col-sm-3">
                    <select class="form-control" name="complain_detail_status" required>
                        <?php if ($latest_query->complain_detail_status === 'รอรับเรื่อง') : ?>
                            <!-- แสดงทุกอ็อปชันสำหรับสถานะ 'รอรับเรื่อง' -->
                            <option class="text-center" value="รับเรื่องแล้ว">รับเรื่องแล้ว</option>
                            <option class="text-center" value="รอดำเนินการ">รอดำเนินการ</option>
                            <option class="text-center" value="กำลังดำเนินการ">กำลังดำเนินการ</option>
                            <option class="text-center" value="ดำเนินการเรียบร้อย">ดำเนินการเรียบร้อย</option>
                        <?php elseif ($latest_query->complain_detail_status === 'รับเรื่องแล้ว') : ?>
                            <!-- ไม่แสดง 'รับเรื่องแล้ว' แต่แสดงอื่น ๆ -->
                            <option class="text-center" value="รอดำเนินการ">รอดำเนินการ</option>
                            <option class="text-center" value="กำลังดำเนินการ">กำลังดำเนินการ</option>
                            <option class="text-center" value="ดำเนินการเรียบร้อย">ดำเนินการเรียบร้อย</option>
                        <?php elseif ($latest_query->complain_detail_status === 'รอดำเนินการ') : ?>
                            <!-- ไม่แสดง 'รับเรื่องแล้ว' และ 'รอดำเนินการ' แต่แสดง 'กำลังดำเนินการ' -->
                            <option class="text-center" value="กำลังดำเนินการ">กำลังดำเนินการ</option>
                            <option class="text-center" value="ดำเนินการเรียบร้อย">ดำเนินการเรียบร้อย</option>
                        <?php elseif ($latest_query->complain_detail_status === 'กำลังดำเนินการ') : ?>
                            <!-- ไม่แสดง 'รับเรื่องแล้ว' และ 'รอดำเนินการ' แต่แสดง 'กำลังดำเนินการ' -->
                            <option class="text-center" value="ดำเนินการเรียบร้อย">ดำเนินการเรียบร้อย</option>
                        <?php endif; ?>
                    </select>
                </div>
            </div>
            <br>
            <div class="form-group row">
                <div class="col-sm-2 control-label">ข้อความ</div>
                <div class="col-sm-6">
                    <input type="text" name="complain_detail_com" required class="form-control">
                </div>
            </div>
            <br>
            <div class="form-group row">
                <div class="col-sm-2 control-label"></div>
                <div class="col-sm-6">
                    <input type="hidden" name="complain_detail_case_id" required class="form-control" value="<?= $rs->complain_detail_case_id; ?>">
                </div>
            </div>
            <br>
            <div class="form-group row">
                <div class="col-sm-2 control-label"></div>
                <div class="col-sm-6">
                    <button type="submit" class="btn btn-success">บันทึกข้อมูล</button>
                    <a class="btn btn-danger" href="<?= site_url('complain_backend/detail/' . $qcomplain->complain_id); ?>" role="button">ยกเลิก</a>
                </div>
            </div>
        </form>
        <!-- <button class="close-button btn btn-danger" data-target="#popup<?= $rs->complain_detail_case_id; ?>"><svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" fill="currentColor" class="bi bi-x-lg" viewBox="0 0 16 16">
                                        <path d="M2.146 2.854a.5.5 0 1 1 .708-.708L8 7.293l5.146-5.147a.5.5 0 0 1 .708.708L8.707 8l5.147 5.146a.5.5 0 0 1-.708.708L8 8.707l-5.146 5.147a.5.5 0 0 1-.708-.708L7.293 8 2.146 2.854Z" />
                                    </svg></button> -->
    </div>
</div>