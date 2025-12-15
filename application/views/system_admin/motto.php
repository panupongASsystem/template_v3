<!-- DataTales Example -->
<div class="card shadow mb-4">
    <!-- <div>
        <a class="btn add-btn" href="<?= site_url('motto_backend/adding'); ?>">
            <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-plus-circle" viewBox="0 0 16 16">
                <path d="M8 15A7 7 0 1 1 8 1a7 7 0 0 1 0 14zm0 1A8 8 0 1 0 8 0a8 8 0 0 0 0 16z" />
                <path d="M8 4a.5.5 0 0 1 .5.5v3h3a.5.5 0 0 1 0 1h-3v3a.5.5 0 0 1-1 0v-3h-3a.5.5 0 0 1 0-1h3v-3A.5.5 0 0 1 8 4z" />
            </svg> เพิ่มข้อมูล</a>
        <a class="btn btn-light" href="<?= site_url('motto_backend'); ?>">
            <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-arrow-clockwise" viewBox="0 0 16 16">
                <path fill-rule="evenodd" d="M8 3a5 5 0 1 0 4.546 2.914.5.5 0 0 1 .908-.417A6 6 0 1 1 8 2v1z" />
                <path d="M8 4.466V.534a.25.25 0 0 1 .41-.192l2.36 1.966c.12.1.12.284 0 .384L8.41 4.658A.25.25 0 0 1 8 4.466z" />
            </svg> Refresh Data</a>
    </div> -->
    <div class="card-header py-3">
        <h6 class="m-0 font-weight-bold text-black">จัดการข้อมูลคำขวัญ</h6>
    </div>
    <div class="card-body">
        <div class="table-responsive">

            <?php
            $Index = 1;
            ?>
            <table id="newdataTables" class="table">
                <thead>
                    <tr>
                        <th style="width: 5%;">ลำดับ</th>
                        <th style="width: 13%;">รูปภาพ</th>
                        <th style="width: 55%;">รายละเอียด</th>
                        <th style="width: 13%;">อัพโหลด</th>
                        <th style="width: 7%;">วันที่</th>
                        <th style="width: 7%;">จัดการ</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($query as $rs) { ?>
                        <tr role="row">
                            <td align="center"><?= $Index; ?></td>
                            <td>
                                <?php if (!empty($rs->motto_img)) : ?>
                                    <img src="<?php echo base_url('docs/img/' . $rs->motto_img); ?>" width="180px" height="120px">
                                <?php else : ?>
                                    <img src="<?php echo base_url('docs/logo.png'); ?>" width="180px" height="120px">
                                <?php endif; ?>
                            </td>
                            <td class="limited-text"><?= $rs->motto_detail; ?></td>
                            <td><?= $rs->motto_by; ?></td>
                            <td><?= date('d/m/Y H:i', strtotime($rs->motto_datesave . '+543 years')) ?> น.</td>
                            <td><a href="<?= site_url('motto_backend/editing/' . $rs->motto_id); ?>"><i class="bi bi-pencil-square fa-lg "></i></a></td>
                        </tr>
                    <?php
                        $Index++;
                    } ?>
                </tbody>
            </table>
        </div>
    </div>
</div>