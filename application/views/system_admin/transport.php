<!-- <h5 class="border border-#f5f5f5 p-2 mb-2 font-black" style="background-color: #f5f5f5;">จัดการบริการขนส่ง</h5> -->
<!-- DataTales Example -->
<div class="card shadow mb-4">
    <div class="card-header py-3">
        <h6 class="m-0 font-weight-bold text-black">จัดการบริการขนส่ง</h6>
    </div>
    <div class="card-body">
        <div class="table-responsive">

            <?php
            $Index = 1;
            ?>
            <table id="newdataTables" class="table">
                <thead>
                    <tr>
                        <th style="width: 8%;">ประเภท</th>
                        <th style="width: 10%;">รูปภาพ</th>
                        <th style="width: 12%;">หัวข้อด้านบน</th>
                        <th style="width: 15%;">รายละเอียดด้านบน</th>
                        <th style="width: 10%;">หัวข้อกลาง</th>
                        <th style="width: 15%;">รายละเอียดกลาง</th>
                        <th style="width: 10%;">รายละเอียดด้านล่าง</th>
                        <th style="width: 10%;">โดย</th>
                        <th style="width: 7%;">วันที่อัพเดต</th>
                        <th style="width: 3%;">จัดการ</th>
                    </tr>
                </thead>
                <tbody>
                    <?php
                    foreach ($query as $rs) { ?>
                        <tr role="row">
                            <td><?= $rs->transport_type_name; ?></td>
                            <td>
                                <?php if (!empty($rs->transport_img)) : ?>
                                    <img src="<?php echo base_url('docs/img/' . $rs->transport_img); ?>" width="180px" height="120px">
                                <?php else : ?>
                                    <img src="<?php echo base_url('docs/logo.png'); ?>" width="180px" height="120px">
                                <?php endif; ?>
                            </td>
                            <td class="limited-text"><?= $rs->transport_head; ?></td>
                            <td><?= mb_substr($rs->transport_detail, 0, 25, 'UTF-8'); ?>...</td>
                            <td class="limited-text"><?= $rs->transport_head2; ?></td>
                            <td><?= mb_substr($rs->transport_detail2, 0, 25, 'UTF-8'); ?>...</td>
                            <td class="limited-text"><?= $rs->transport_head3; ?></td>
                            <td><?= $rs->transport_by; ?></td>
                            <td><?= date('d/m/Y : H:i', strtotime($rs->transport_datesave . '+543 years')) ?></td>
                            <td>
                                <a href="<?= site_url('transport_backend/edit/' . $rs->transport_id); ?>"><i class="bi bi-pencil-square fa-lg "></i></a>
                                <!-- <a href="<?= site_url('news_backend/del_News/' . $rs->transport_id); ?>" role="button" onclick="return confirm('ยืนยันการลบข้อมูล??');"><i class="bi bi-trash fa-lg "></i></a> -->
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



<!-- <a class="btn btn-success" href="<?= site_url('transport_backend/addingtransport'); ?>" role="button"><i class="bi bi-plus-circle"></i> เพิ่มข้อมูล</a>
    <a class="btn btn-light" href="<?= site_url('transport_backend'); ?>" role="button"><i class="bi bi-arrow-clockwise"></i> Refresh Data</a> -->

<!-- DataTales Example -->
<!-- <div class="card shadow mb-4">
        <div class="card-header py-3">
            <h6 class="m-0 font-weight-bold text-primary">จัดการบริการขนส่ง</h6>
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-bordered" id="dataTable" width="100%" cellspacing="0">
                    <thead>
                        <tr> -->
<!-- <th tabindex="0" rowspan="1" colspan="1" style="width: 2%;">ลำดับ</th> -->
<!-- <th tabindex="0" rowspan="1" colspan="1" style="width: 5%;">ประเภท</th>
                            <th tabindex="0" rowspan="1" colspan="1" style="width: 15%;">รูปภาพ</th>
                            <th tabindex="0" rowspan="1" colspan="1" style="width: 10%;">หัวข้อด้านบน</th>
                            <th tabindex="0" rowspan="1" colspan="1" style="width: 15%;">รายละเอียดด้านบน</th>
                            <th tabindex="0" rowspan="1" colspan="1" style="width: 10%;">หัวข้อกลาง</th>
                            <th tabindex="0" rowspan="1" colspan="1" style="width: 15%;">รายละเอียดกลาง</th>
                            <th tabindex="0" rowspan="1" colspan="1" style="width: 15%;">หัวข้อด้านล่าง</th>
                            <th tabindex="0" rowspan="1" colspan="1" style="width: 3%;">วันที่อัพโหลด</th>
                            <th tabindex="0" rowspan="1" colspan="1" style="width: 3%;">แก้ไข</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($query as $index => $rs) { ?>
                            <tr role="row"> -->
<!-- <td align="center"><?= $index + 1; ?></td> -->
<!-- <td><?= $rs->transport_type_name; ?></td>
                                <td><img src="<?= base_url('docs/img/' . $rs->transport_img); ?>" width="220px" height="180px"></td>
                                <td><?= $rs->transport_head; ?></td>
                                <td><?= $rs->transport_detail; ?></td>
                                <td><?= $rs->transport_head2; ?></td>
                                <td><?= $rs->transport_detail2; ?></td>
                                <td><?= $rs->transport_head3; ?></td>
                                <td><?= date('d/m/Y : H:i', strtotime($rs->transport_datesave . '+543 years')) ?></td>
                                <td>
                                    <a href="<?php echo site_url('transport/edit/' . $rs->transport_id); ?>" class="btn btn-warning btn-xs">
                                        แก้ไข
                                    </a>
                                </td>
                            </tr>
                        <?php } ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div> -->