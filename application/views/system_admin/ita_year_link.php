<a class="btn add-btn insert-vulgar-btn" data-target="#popupInsert">
    <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-plus-circle" viewBox="0 0 16 16">
        <path d="M8 15A7 7 0 1 1 8 1a7 7 0 0 1 0 14zm0 1A8 8 0 1 0 8 0a8 8 0 0 0 0 16z" />
        <path d="M8 4a.5.5 0 0 1 .5.5v3h3a.5.5 0 0 1 0 1h-3v3a.5.5 0 0 1-1 0v-3h-3a.5.5 0 0 1 0-1h3v-3A.5.5 0 0 1 8 4z" />
    </svg> เพิ่มข้อมูล</a>
<a class="btn btn-danger" href="<?= site_url('Ita_year_backend/index_topic/' . $query->ita_year_topic_ref_id); ?>" role="button">
    <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-arrow-left-circle" viewBox="0 0 16 16">
        <path fill-rule="evenodd" d="M1 8a7 7 0 1 0 14 0A7 7 0 0 0 1 8m15 0A8 8 0 1 1 0 8a8 8 0 0 1 16 0m-4.5-.5a.5.5 0 0 1 0 1H5.707l2.147 2.146a.5.5 0 0 1-.708.708l-3-3a.5.5 0 0 1 0-.708l3-3a.5.5 0 1 1 .708.708L5.707 7.5z" />
    </svg> ย้อนกลับ</a>
<a class="btn btn-light" href="<?= site_url('Ita_year_backend/index_link/' . $query->ita_year_topic_id); ?>" role="button">
    <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-arrow-clockwise" viewBox="0 0 16 16">
        <path fill-rule="evenodd" d="M8 3a5 5 0 1 0 4.546 2.914.5.5 0 0 1 .908-.417A6 6 0 1 1 8 2v1z" />
        <path d="M8 4.466V.534a.25.25 0 0 1 .41-.192l2.36 1.966c.12.1.12.284 0 .384L8.41 4.658A.25.25 0 0 1 8 4.466z" />
    </svg> Refresh Data</a>
<div id="popupInsert" class="popup">
    <div class="popup-content">
        <h4>เพิ่มข้อมูลลิงค์ ITA ประจำปี : <?= $query->ita_year_topic_name; ?></h4>
        <form action="<?php echo site_url('Ita_year_backend/add_link'); ?> " method="post" class="form-horizontal">
            <input type="hidden" name="ita_year_link_ref_id" value="<?= $query->ita_year_topic_id; ?>" class="form-control">
            <div class="form-group row">
                <div class="col-sm-2 control-label">ชื่อ </div>
                <div class="col-sm-5">
                    <input type="text" name="ita_year_link_name" class="form-control">
                </div>
            </div>
            <br>
            <div class="form-group row">
                <div class="col-sm-2 control-label">ชื่อของลิงค์เพิ่มเติม 1</div>
                <div class="col-sm-5">
                    <input type="text" name="ita_year_link_title1" class="form-control">
                </div>
            </div>
            <br>
            <div class="form-group row">
                <div class="col-sm-2 control-label">ลิงค์เพิ่มเติม 1</div>
                <div class="col-sm-5">
                    <input type="text" name="ita_year_link_link1" class="form-control">
                </div>
            </div>
            <br>
            <div class="form-group row">
                <div class="col-sm-2 control-label">ชื่อของลิงค์เพิ่มเติม 2</div>
                <div class="col-sm-5">
                    <input type="text" name="ita_year_link_title2" class="form-control">
                </div>
            </div>
            <br>
            <div class="form-group row">
                <div class="col-sm-2 control-label">ลิงค์เพิ่มเติม 2</div>
                <div class="col-sm-5">
                    <input type="text" name="ita_year_link_link2" class="form-control">
                </div>
            </div>
            <br>
            <div class="form-group row">
                <div class="col-sm-2 control-label">ชื่อของลิงค์เพิ่มเติม 3</div>
                <div class="col-sm-5">
                    <input type="text" name="ita_year_link_title3" class="form-control">
                </div>
            </div>
            <br>
            <div class="form-group row">
                <div class="col-sm-2 control-label">ลิงค์เพิ่มเติม 3</div>
                <div class="col-sm-5">
                    <input type="text" name="ita_year_link_link3" class="form-control">
                </div>
            </div>
            <br>
            <div class="form-group row">
                <div class="col-sm-2 control-label">ชื่อของลิงค์เพิ่มเติม 4</div>
                <div class="col-sm-5">
                    <input type="text" name="ita_year_link_title4" class="form-control">
                </div>
            </div>
            <br>
            <div class="form-group row">
                <div class="col-sm-2 control-label">ลิงค์เพิ่มเติม 4</div>
                <div class="col-sm-5">
                    <input type="text" name="ita_year_link_link4" class="form-control">
                </div>
            </div>
            <br>
            <div class="form-group row">
                <div class="col-sm-2 control-label">ชื่อของลิงค์เพิ่มเติม 5</div>
                <div class="col-sm-5">
                    <input type="text" name="ita_year_link_title5" class="form-control">
                </div>
            </div>
            <br>
            <div class="form-group row">
                <div class="col-sm-2 control-label">ลิงค์เพิ่มเติม 5</div>
                <div class="col-sm-5">
                    <input type="text" name="ita_year_link_link5" class="form-control">
                </div>
            </div>
            <br>
            <div class="form-group row">
                <div class="col-sm-1 control-label"></div>
                <div class="col-sm-5">
                    <button type="submit" class="btn btn-success">บันทึกข้อมูล</button>
                    <a class="btn btn-danger" href="<?= site_url('Ita_year_backend/index_link/' . $query->ita_year_topic_id); ?>" role="button">ยกเลิก</a>
                </div>
            </div>
        </form>
    </div>
</div>
<!-- DataTales Example -->
<div class="card shadow mb-4">
    <div class="card-header py-3">
        <h6 class="m-0 font-weight-bold text-black">จัดการข้อมูลลิงค์ ITA หัวข้อ : <?= $query->ita_year_topic_name; ?></h6>
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
                        <th style="width: 15%;">ชื่อ</th>
                        <th style="width: 35%;">ชื่อของลิงค์เพิ่มเติม | ลิงค์เพิ่มเติม</th>
                        <th style="width: 13%;">อัพโหลด</th>
                        <th style="width: 7%;">วันที่</th>
                        <th style="width: 17%;">จัดการ</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($query_link as $rs) { ?>
                        <tr role="row">
                            <td align="center"><?= $Index; ?></td>
                            <td><?= $rs->ita_year_link_name; ?><br></td>
                            <td>
                                <?php if (!empty($rs->ita_year_link_link1) || !empty($rs->ita_year_link_title1)) : ?>
                                    <a href="<?= $rs->ita_year_link_link1; ?>" target="_blank">
                                        <?= $rs->ita_year_link_title1; ?>
                                        <?php if (!empty($rs->ita_year_link_title1) && !empty($rs->ita_year_link_link1)) : ?> | <?php endif; ?>
                                        <?= $rs->ita_year_link_link1; ?>
                                    </a>
                                    <br>
                                <?php endif; ?>

                                <?php if (!empty($rs->ita_year_link_link2) || !empty($rs->ita_year_link_title2)) : ?>
                                    <a href="<?= $rs->ita_year_link_link2; ?>" target="_blank">
                                        <?= $rs->ita_year_link_title2; ?>
                                        <?php if (!empty($rs->ita_year_link_title2) && !empty($rs->ita_year_link_link2)) : ?> | <?php endif; ?>
                                        <?= $rs->ita_year_link_link2; ?>
                                    </a>
                                    <br>
                                <?php endif; ?>

                                <?php if (!empty($rs->ita_year_link_link3) || !empty($rs->ita_year_link_title3)) : ?>
                                    <a href="<?= $rs->ita_year_link_link3; ?>" target="_blank">
                                        <?= $rs->ita_year_link_title3; ?>
                                        <?php if (!empty($rs->ita_year_link_title3) && !empty($rs->ita_year_link_link3)) : ?> | <?php endif; ?>
                                        <?= $rs->ita_year_link_link3; ?>
                                    </a>
                                    <br>
                                <?php endif; ?>

                                <?php if (!empty($rs->ita_year_link_link4) || !empty($rs->ita_year_link_title4)) : ?>
                                    <a href="<?= $rs->ita_year_link_link4; ?>" target="_blank">
                                        <?= $rs->ita_year_link_title4; ?>
                                        <?php if (!empty($rs->ita_year_link_title4) && !empty($rs->ita_year_link_link4)) : ?> | <?php endif; ?>
                                        <?= $rs->ita_year_link_link4; ?>
                                    </a>
                                    <br>
                                <?php endif; ?>

                                <?php if (!empty($rs->ita_year_link_link5) || !empty($rs->ita_year_link_title5)) : ?>
                                    <a href="<?= $rs->ita_year_link_link5; ?>" target="_blank">
                                        <?= $rs->ita_year_link_title5; ?>
                                        <?php if (!empty($rs->ita_year_link_title5) && !empty($rs->ita_year_link_link5)) : ?> | <?php endif; ?>
                                        <?= $rs->ita_year_link_link5; ?>
                                    </a>
                                <?php endif; ?>
                            </td>
                            <td><?= $rs->ita_year_link_by; ?></td>
                            <td><?= date('d/m/Y H:i', strtotime($rs->ita_year_link_datesave . '+543 years')) ?> น.</td>
                            <td>
                                <a href="<?= site_url('ita_year_backend/editing_link/' . $rs->ita_year_link_id); ?>"><i class="bi bi-pencil-square fa-lg "></i></a>
                                <a href="#" role="button" onclick="confirmDelete('<?= $rs->ita_year_link_id; ?>');"><i class="bi bi-trash fa-lg "></i></a>
                                <script>
                                    function confirmDelete(ita_year_link_id) {
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
                                                window.location.href = "<?= site_url('ita_year_backend/del_ita_link/'); ?>" + ita_year_link_id;
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