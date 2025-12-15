<a class="btn add-btn insert-vulgar-btn" data-target="#popupInsert">
    <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-plus-circle" viewBox="0 0 16 16">
        <path d="M8 15A7 7 0 1 1 8 1a7 7 0 0 1 0 14zm0 1A8 8 0 1 0 8 0a8 8 0 0 0 0 16z" />
        <path d="M8 4a.5.5 0 0 1 .5.5v3h3a.5.5 0 0 1 0 1h-3v3a.5.5 0 0 1-1 0v-3h-3a.5.5 0 0 1 0-1h3v-3A.5.5 0 0 1 8 4z" />
    </svg> เพิ่มข้อมูล</a>
<a class="btn btn-light" href="<?= site_url('Odata_backend'); ?>" role="button">
    <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-arrow-clockwise" viewBox="0 0 16 16">
        <path fill-rule="evenodd" d="M8 3a5 5 0 1 0 4.546 2.914.5.5 0 0 1 .908-.417A6 6 0 1 1 8 2v1z" />
        <path d="M8 4.466V.534a.25.25 0 0 1 .41-.192l2.36 1.966c.12.1.12.284 0 .384L8.41 4.658A.25.25 0 0 1 8 4.466z" />
    </svg> Refresh Data</a>
<div id="popupInsert" class="popup">
    <div class="popup-content">
        <h4>เพิ่มข้อมูลฐานข้อมูลเปิดภาครัฐ (Open Data)</h4>
        <form action="<?php echo site_url('Odata_backend/add_odata'); ?> " method="post" class="form-horizontal">
            <div class="form-group row">
                <div class="col-sm-1 control-label">ชื่อหัวข้อหลัก</div>
                <div class="col-sm-5">
                    <input type="text" name="odata_name" required class="form-control">
                    <!-- <span class="red-add">( ใส่เฉพาะตัวเลข เช่น 2567)</span> -->
                </div>
            </div>
            <br>
            <div class="form-group row">
                <div class="col-sm-1 control-label"></div>
                <div class="col-sm-5">
                    <button type="submit" class="btn btn-success">บันทึกข้อมูล</button>
                    <a class="btn btn-danger" href="<?= site_url('Odata_backend'); ?>" role="button">ยกเลิก</a>
                </div>
            </div>
        </form>
        <!-- <button class="close-button btn btn-danger" data-target="#popup<?= $rlatest_querys->complain_detail_case_id; ?>"><svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" fill="currentColor" class="bi bi-x-lg" viewBox="0 0 16 16">
                                        <path d="M2.146 2.854a.5.5 0 1 1 .708-.708L8 7.293l5.146-5.147a.5.5 0 0 1 .708.708L8.707 8l5.147 5.146a.5.5 0 0 1-.708.708L8 8.707l-5.146 5.147a.5.5 0 0 1-.708-.708L7.293 8 2.146 2.854Z" />
                                    </svg></button> -->
    </div>
</div>
<!-- DataTales Example -->
<div class="card shadow mb-4">
    <div class="card-header py-3">
        <h6 class="m-0 font-weight-bold text-black">จัดการข้อมูลฐานข้อมูลเปิดภาครัฐ (Open Data)</h6>
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
                        <th style="width: 50%;">ชื่อ</th>
                        <th style="width: 13%;">อัพโหลด</th>
                        <th style="width: 7%;">วันที่</th>
                        <th style="width: 17%;">จัดการ</th>
                    </tr>
                </thead>
                <tbody>
                    <?php
                    foreach ($query as $rs) { ?>
                        <tr role="row">
                            <td align="center"><?= $Index; ?></td>
                            <td class="limited-text"><?= $rs->odata_name; ?></td>
                            <td><?= $rs->odata_by; ?></td>
                            <td><?= date('d/m/Y H:i', strtotime($rs->odata_datesave . '+543 years')) ?> น.</td>
                            <td>
                                <a href="<?= site_url('odata_backend/index_odata_sub/' . $rs->odata_id); ?>"><i class="bi bi-plus-square fa-lg"></i></a>
                                <a href="<?= site_url('odata_backend/editing_odata/' . $rs->odata_id); ?>"><i class="bi bi-pencil-square fa-lg "></i></a>
                                <a href="#" role="button" onclick="confirmDelete('<?= $rs->odata_id; ?>');"><i class="bi bi-trash fa-lg "></i></a>
                                <script>
                                    function confirmDelete(odata_id) {
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
                                                window.location.href = "<?= site_url('odata_backend/del_odata/'); ?>" + odata_id;
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