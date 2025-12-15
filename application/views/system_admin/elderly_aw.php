    <!-- Tasks Card Example -->
    <a class="btn add-btn insert-vulgar-btn" data-target="#popupInsert">
        <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-file-earmark-plus" viewBox="0 0 16 16">
            <path d="M8 6.5a.5.5 0 0 1 .5.5v1.5H10a.5.5 0 0 1 0 1H8.5V11a.5.5 0 0 1-1 0V9.5H6a.5.5 0 0 1 0-1h1.5V7a.5.5 0 0 1 .5-.5" />
            <path d="M14 4.5V14a2 2 0 0 1-2 2H4a2 2 0 0 1-2-2V2a2 2 0 0 1 2-2h5.5zm-3 0A1.5 1.5 0 0 1 9.5 3V1H4a1 1 0 0 0-1 1v12a1 1 0 0 0 1 1h8a1 1 0 0 0 1-1V4.5z" />
        </svg> เพิ่มข้อมูลไฟล์ .CSV</a>
    <div id="popupInsert" class="popup">
        <div class="popup-content">
            <h4>เพิ่มข้อมูลไฟล์ .CSV</h4>
            <form method="post" action="<?php echo base_url() ?>Elderly_aw_backend/importcsv" enctype="multipart/form-data" onsubmit="return validateForm()">
                <input type="file" name="userfile"><br><br>
                <input type="submit" name="submit" value="บันทึกข้อมูล" class="btn btn-success">
                <a class="btn btn-danger" href="<?= site_url('Elderly_aw_backend'); ?>" role="button">ยกเลิก</a>
            </form>
            <!-- <form action="<?php echo site_url('Elderly_aw_backend/add_csv_data_to_database'); ?> " method="post" class="form-horizontal" enctype="multipart/form-data" onsubmit="return validateForm()">
                <div class="form-group row">
                    <div class="col-sm-2 control-label">ข้อความ</div>
                    <div class="col-sm-5">
                        <input type="file" name="userfile" size="20" required class="form-control">
                    </div>
                </div>
                <br>
                <div class="form-group row">
                    <div class="col-sm-2 control-label"></div>
                    <div class="col-sm-5">
                        <button type="submit" class="btn btn-success">บันทึกข้อมูล</button>
                        <a class="btn btn-danger" href="<?= site_url('Elderly_aw_backend'); ?>" role="button">ยกเลิก</a>
                    </div>
                </div>
            </form> -->
        </div>
    </div>
    <!-- <a class="btn add-btn" href="#" role="button">
        <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-file-earmark-plus" viewBox="0 0 16 16">
            <path d="M8 6.5a.5.5 0 0 1 .5.5v1.5H10a.5.5 0 0 1 0 1H8.5V11a.5.5 0 0 1-1 0V9.5H6a.5.5 0 0 1 0-1h1.5V7a.5.5 0 0 1 .5-.5" />
            <path d="M14 4.5V14a2 2 0 0 1-2 2H4a2 2 0 0 1-2-2V2a2 2 0 0 1 2-2h5.5zm-3 0A1.5 1.5 0 0 1 9.5 3V1H4a1 1 0 0 0-1 1v12a1 1 0 0 0 1 1h8a1 1 0 0 0 1-1V4.5z" />
        </svg> เพิ่มข้อมูลไฟล์ .CSV</a> -->
    <a class="btn btn-info" role="button" href="<?= base_url('docs/การเพิ่มข้อมูลเบี้ยผู้สูงอายุแบบไฟล์Excel.rar'); ?>" download>
        <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-arrow-down-circle" viewBox="0 0 16 16">
            <path fill-rule="evenodd" d="M1 8a7 7 0 1 0 14 0A7 7 0 0 0 1 8m15 0A8 8 0 1 1 0 8a8 8 0 0 1 16 0M8.5 4.5a.5.5 0 0 0-1 0v5.793L5.354 8.146a.5.5 0 1 0-.708.708l3 3a.5.5 0 0 0 .708 0l3-3a.5.5 0 0 0-.708-.708L8.5 10.293z" />
        </svg> ตัวอย่างการอัพโหลดไฟล์</a>
    <a class="btn add-btn" href="<?= site_url('elderly_aw_backend/adding_elderly_aw'); ?>" role="button">
        <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-plus-circle" viewBox="0 0 16 16">
            <path d="M8 15A7 7 0 1 1 8 1a7 7 0 0 1 0 14zm0 1A8 8 0 1 0 8 0a8 8 0 0 0 0 16z" />
            <path d="M8 4a.5.5 0 0 1 .5.5v3h3a.5.5 0 0 1 0 1h-3v3a.5.5 0 0 1-1 0v-3h-3a.5.5 0 0 1 0-1h3v-3A.5.5 0 0 1 8 4z" />
        </svg> เพิ่มข้อมูลรายบุคคล</a>
    <a class="btn btn-light" href="<?= site_url('elderly_aw_backend'); ?>" role="button">
        <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-arrow-clockwise" viewBox="0 0 16 16">
            <path fill-rule="evenodd" d="M8 3a5 5 0 1 0 4.546 2.914.5.5 0 0 1 .908-.417A6 6 0 1 1 8 2v1z" />
            <path d="M8 4.466V.534a.25.25 0 0 1 .41-.192l2.36 1.966c.12.1.12.284 0 .384L8.41 4.658A.25.25 0 0 1 8 4.466z" />
        </svg> Refresh Data</a>
    <!-- DataTales Example -->
    <div class="card shadow mb-4">
        <div class="card-header py-3">
            <h6 class="m-0 font-weight-bold text-black">จัดการข้อมูลตรวจสอบเบี้ยผู้สูงอายุ</h6>
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
                            <th style="width: 12%;">เลขประจำตัวประชาชนผู้มีสิทธิ</th>
                            <th style="width: 10%;">ชื่อ - นามสกุลของผู้มีสิทธิ</th>
                            <th style="width: 10%;">เลขประจำตัวประชาชนเจ้าของบัญชี</th>
                            <th style="width: 10%;">ชื่อ - นามสกุล เจ้าของบัญชี</th>
                            <th style="width: 8%;">หน่วยงาน</th>
                            <th style="width: 5%;">ธนาคาร</th>
                            <th style="width: 7%;">ประเภทการจ่าย</th>
                            <th style="width: 14%;">เลขที่บัญชีธนาคาร/เลขบัตร</th>
                            <th style="width: 8%;">งวดเงินที่จ่าย</th>
                            <th style="width: 9%;">จำนวนเงิน</th>
                            <th style="width: 10%;">สาเหตุระงับจ่าย</th>
                            <th style="width: 10%;">อัพโหลด</th>
                            <th style="width: 7%;">วันที่</th>
                            <th style="width: 10%;">จัดการ</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php
                        foreach ($query as $rs) { ?>
                            <tr role="row">
                                <td align="center"><?= $Index; ?></td>
                                <td><?= $rs->elderly_aw_id_num_eligible; ?></td>
                                <td><?= $rs->elderly_aw_name_eligible; ?></td>
                                <td><?= $rs->elderly_aw_id_num_owner; ?></td>
                                <td><?= $rs->elderly_aw_name_owner; ?></td>
                                <td><?= $rs->elderly_aw_agency; ?></td>
                                <td><?= $rs->elderly_aw_bank; ?></td>
                                <td><?= $rs->elderly_aw_type_payment; ?></td>
                                <td><?= $rs->elderly_aw_bank_num; ?></td>
                                <td><?= $rs->elderly_aw_period_payment; ?></td>
                                <td><?= $rs->elderly_aw_money; ?></td>
                                <td><?= $rs->elderly_aw_note; ?></td>
                                <td><?= $rs->elderly_aw_by; ?></td>
                                <td><?= date('d/m/Y H:i', strtotime($rs->elderly_aw_datesave . '+543 years')) ?> น.</td>
                                <td>
                                    <a href="<?= site_url('elderly_aw_backend/editing_elderly_aw/' . $rs->elderly_aw_id); ?>"><i class="bi bi-pencil-square fa-lg "></i></a>
                                    <a href="#" role="button" onclick="confirmDelete('<?= $rs->elderly_aw_id; ?>');"><i class="bi bi-trash fa-lg "></i></a>
                                    <script>
                                        function confirmDelete(elderly_aw_id) {
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
                                                    window.location.href = "<?= site_url('elderly_aw_backend/del_elderly_aw/'); ?>" + elderly_aw_id;
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