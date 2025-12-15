<a class="btn add-btn insert-vulgar-btn" data-target="#popupInsert">
    <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-plus-circle" viewBox="0 0 16 16">
        <path d="M8 15A7 7 0 1 1 8 1a7 7 0 0 1 0 14zm0 1A8 8 0 1 0 8 0a8 8 0 0 0 0 16z" />
        <path d="M8 4a.5.5 0 0 1 .5.5v3h3a.5.5 0 0 1 0 1h-3v3a.5.5 0 0 1-1 0v-3h-3a.5.5 0 0 1 0-1h3v-3A.5.5 0 0 1 8 4z" />
    </svg> เพิ่มหัวข้อ</a>
<div id="popupInsert" class="popup">
    <div class="popup-content">
        <h4>เพิ่มข้อมูล</h4>
        <form action="<?php echo site_url('operation_cdm_backend/add_type'); ?> " method="post" class="form-horizontal">
            <div class="form-group row">
                <div class="col-sm-2 control-label">หัวข้อ <span class="red-add">*</span></div>
                <div class="col-sm-12">
                    <input type="text" name="operation_cdm_type_name" required class="form-control">
                </div>
            </div>
            <div class="form-group row">
                <div class="col-sm-12 control-label">วันที่อัพโหลด <span class="red-add">*</span></div>
                <br>
                <div class="col-sm-3">
                    <input type="datetime-local" name="operation_cdm_type_date" class="form-control" required>
                </div>
            </div>
            <br>
            <div class="form-group row">
                <div class="col-sm-2 control-label"></div>
                <div class="col-sm-5">
                    <button type="submit" class="btn btn-success">บันทึกข้อมูล</button>
                    <a class="btn btn-danger" href="<?= site_url('operation_cdm_backend'); ?>" role="button">ยกเลิก</a>
                </div>
            </div>
        </form>
        <!-- <button class="close-button btn btn-danger" data-target="#popup<?= $rlatest_querys->complain_detail_case_id; ?>"><svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" fill="currentColor" class="bi bi-x-lg" viewBox="0 0 16 16">
                                        <path d="M2.146 2.854a.5.5 0 1 1 .708-.708L8 7.293l5.146-5.147a.5.5 0 0 1 .708.708L8.707 8l5.147 5.146a.5.5 0 0 1-.708.708L8 8.707l-5.146 5.147a.5.5 0 0 1-.708-.708L7.293 8 2.146 2.854Z" />
                                    </svg></button> -->
    </div>
</div>
<a class="btn btn-light" href="<?= site_url('operation_cdm_backend'); ?>" role="button">
    <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-arrow-clockwise" viewBox="0 0 16 16">
        <path fill-rule="evenodd" d="M8 3a5 5 0 1 0 4.546 2.914.5.5 0 0 1 .908-.417A6 6 0 1 1 8 2v1z" />
        <path d="M8 4.466V.534a.25.25 0 0 1 .41-.192l2.36 1.966c.12.1.12.284 0 .384L8.41 4.658A.25.25 0 0 1 8 4.466z" />
    </svg> Refresh Data</a>

<!-- <h5 class="border border-#f5f5f5 p-2 mb-2 font-black" style="background-color: #f5f5f5;">จัดการข้อมูลข่าวสารประจำเดือน</h5> -->
<!-- DataTales Example -->
<div class="card shadow mb-4">
    <div class="card-header py-3">
        <h6 class="m-0 font-weight-bold text-black">จัดการข้อมูลหลักเกณฑ์การบริหารและพัฒนา</h6>
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
                        <th style="width: 55%;">หัวข้อ</th>
                        <th style="width: 20%;">อัพโหลด</th>
                        <th style="width: 7%;">วันที่</th>
                        <th style="width: 15%;">จัดการ</th>
                    </tr>
                </thead>
                <tbody>
                    <?php
                    foreach ($query as $rs) { ?>
                        <tr role="row">
                            <td align="center"><?= $Index; ?></td>
                            <td class="limited-text"><?= $rs->operation_cdm_type_name; ?></td>
                            <td><?= $rs->operation_cdm_type_by; ?></td>
                            <td><?= date('d/m/Y H:i', strtotime($rs->operation_cdm_type_date . '+543 years')) ?> น.</td>

                            <td>
                                <a href="<?= site_url('operation_cdm_backend/index_detail/' . $rs->operation_cdm_type_id); ?>"><i class="bi bi-plus-square fa-lg"></i>
                                    <a href="<?= site_url('operation_cdm_backend/editing_type/' . $rs->operation_cdm_type_id); ?>"><i class="bi bi-pencil-square fa-lg "></i></a>
                                    <a href="#" role="button" onclick="confirmDelete(<?= $rs->operation_cdm_type_id; ?>);"><i class="bi bi-trash fa-lg "></i></a>

                                    <script>
                                        function confirmDelete(operation_cdm_type_id) {
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
                                                    window.location.href = "<?= site_url('operation_cdm_backend/del_operation_cdm_type/'); ?>" + operation_cdm_type_id;
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

<!-- DataTales Example -->
<!-- <div class="card shadow mb-4">
        <div class="card-header py-3">
            <h6 class="m-0 font-weight-bold text-primary">จัดการข้อมูลข่าวสารประจำเดือน</h6>
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-bordered" id="dataTable" cellspacing="0">
                    <thead>
                        <tr>
                            <th tabindex="0" rowspan="1" colspan="1">ลำดับ</th>
                            <th tabindex="0" rowspan="1" colspan="1">รูปภาพ</th>
                            <th tabindex="0" rowspan="1" colspan="1">ชื่อ</th>
                            <th tabindex="0" rowspan="1" colspan="1">รายละเอียด</th>
                            <th tabindex="0" rowspan="1" colspan="1">ผู้อัพโหลด</th>
                            <th tabindex="0" rowspan="1" colspan="1">วันที่อัพโหลด</th>
                            <th tabindex="0" rowspan="1" colspan="1">สถานะ</th>
                            <th tabindex="0" rowspan="1" colspan="1">ความคิดเห็น</th>
                            <th tabindex="0" rowspan="1" colspan="1">แก้ไข</th>
                            <th tabindex="0" rowspan="1" colspan="1">ลบ</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($query as $index => $rs) { ?>
                            <tr role="row">
                                <td align="center"><?= $index + 1; ?></td>
                                <td>รูปภาพหน้าปก : <img src="<?= base_url('docs/img/' . $rs->operation_cdm_img); ?>" width="180px" height="140px"> <br>
                                    รูปภาพเพิ่มเติม : <?php foreach (explode(',', $rs->additional_images) as $img) { ?>
                                        <img src="<?= base_url('docs/img/' . $img); ?>" width="100px" height="60px">
                                    <?php } ?>
                                </td>
                                </td>
                                <td><?= $rs->operation_cdm_name; ?></td>
                                <td><?= mb_substr($rs->operation_cdm_detail, 0, 200, 'UTF-8'); ?>...</td>
                                <td><?= $rs->operation_cdm_by; ?></td>
                                <td><?= date('d/m/Y : H:i', strtotime($rs->operation_cdm_datesave . '+543 years')) ?></td>
                                <td>
                                       <label class="switch">
                                           <input class="form-check-input" type="checkbox" role="switch" id="flexSwitchCheck<?= $rs->operation_cdm_id; ?>" data-operation_cdm-id="<?= $rs->operation_cdm_id; ?>" <?= $rs->operation_cdm_status === 'show' ? 'checked' : ''; ?>>
                                           <span class="slider round"></span>
                                       </label>
                                       <script>
                                           // เลือก Switch ตามรายการในลูปโดยใช้ข้อมูลที่เกี่ยวข้องกับแต่ละรายการ
                                           const switchCheckbox<?= $rs->operation_cdm_id; ?> = document.getElementById('flexSwitchCheck<?= $rs->operation_cdm_id; ?>');

                                           switchCheckbox<?= $rs->operation_cdm_id; ?>.addEventListener('change', function() {
                                               const operation_cdmId = this.getAttribute('data-operation_cdm-id');
                                               const operation_cdmtatus = this.checked ? 'show' : 'hide';

                                               // ส่งข้อมูลไปยังเซิร์ฟเวอร์ด้วย AJAX
                                               $.ajax({
                                                   type: 'POST',
                                                   url: 'operation_cdm/updateoperation_cdmStatus', // แทนค่า '/your-controller/' ด้วย URL ของ Controller ที่คุณสร้าง
                                                   data: {
                                                       operation_cdm_id: operation_cdmId,
                                                       new_status: operation_cdmtatus
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
                                       </script>
                                   </td>
                                <td><a href="#" class="btn btn-secondary btn-xs">ดูcomment</a></td>
                                <td><a href="<?php echo site_url('operation_cdm/edit/' . $rs->operation_cdm_id); ?>" class="btn btn-warning btn-xs">แก้ไข</a></td>
                                <td><a class="btn btn-danger btn-xs" href="<?= site_url('operation_cdm/del_operation_cdm/' . $rs->operation_cdm_id); ?>" role="button" onclick="return confirm('ยืนยันการลบข้อมูล??');">ลบ</a></td>
                            </tr>
                        <?php } ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div> -->