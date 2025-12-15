<div class="d-flex justify-content-between mb-3">
    <div>
        <a class="btn add-btn" href="<?= site_url('System_config_backend/adding'); ?>" role="button">
            <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-plus-circle" viewBox="0 0 16 16">
                <path d="M8 15A7 7 0 1 1 8 1a7 7 0 0 1 0 14zm0 1A8 8 0 1 0 8 0a8 8 0 0 0 0 16z" />
                <path d="M8 4a.5.5 0 0 1 .5.5v3h3a.5.5 0 0 1 0 1h-3v3a.5.5 0 0 1-1 0v-3h-3a.5.5 0 0 1 0-1h3v-3A.5.5 0 0 1 8 4z" />
            </svg> เพิ่มข้อมูล</a>
        <a class="btn add-btn" href="<?= site_url('System_config_backend/update_domain'); ?>" role="button">
            <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-plus-circle" viewBox="0 0 16 16">
                <path d="M8 15A7 7 0 1 1 8 1a7 7 0 0 1 0 14zm0 1A8 8 0 1 0 8 0a8 8 0 0 0 0 16z" />
                <path d="M8 4a.5.5 0 0 1 .5.5v3h3a.5.5 0 0 1 0 1h-3v3a.5.5 0 0 1-1 0v-3h-3a.5.5 0 0 1 0-1h3v-3A.5.5 0 0 1 8 4z" />
            </svg> Update Domain</a>
        <a class="btn btn-light" href="<?= site_url('System_config_backend'); ?>" role="button">
            <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-arrow-clockwise" viewBox="0 0 16 16">
                <path fill-rule="evenodd" d="M8 3a5 5 0 1 0 4.546 2.914.5.5 0 0 1 .908-.417A6 6 0 1 1 8 2v1z" />
                <path d="M8 4.466V.534a.25.25 0 0 1 .41-.192l2.36 1.966c.12.1.12.284 0 .384L8.41 4.658A.25.25 0 0 1 8 4.466z" />
            </svg> Refresh Data</a>
    </div>
    <div>
        <a class="btn btn-info" href="<?= site_url('System_config_backend/address'); ?>" role="button">
            <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-info-circle" viewBox="0 0 16 16">
                <path d="M8 15A7 7 0 1 1 8 1a7 7 0 0 1 0 14m0 1A8 8 0 1 0 8 0a8 8 0 0 0 0 16" />
                <path d="m8.93 6.588-2.29.287-.082.38.45.083c.294.07.352.176.288.469l-.738 3.468c-.194.897.105 1.319.808 1.319.545 0 1.178-.252 1.465-.598l.088-.416c-.2.176-.492.246-.686.246-.275 0-.375-.193-.304-.533zM9 4.5a1 1 0 1 1-2 0 1 1 0 0 1 2 0" />
            </svg> System Config กลุ่มที่อยู่</a>
        <a class="btn btn-info" href="<?= site_url('System_config_backend/link'); ?>" role="button">
            <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-link" viewBox="0 0 16 16">
                <path d="M6.354 5.5H4a3 3 0 0 0 0 6h3a3 3 0 0 0 2.83-4H9q-.13 0-.25.031A2 2 0 0 1 7 10.5H4a2 2 0 1 1 0-4h1.535c.218-.376.495-.714.82-1z" />
                <path d="M9 5.5a3 3 0 0 0-2.83 4h1.098A2 2 0 0 1 9 6.5h3a2 2 0 1 1 0 4h-1.535a4 4 0 0 1-.82 1H12a3 3 0 1 0 0-6z" />
            </svg> System Config ลิงค์อื่นๆ</a>
        <a class="btn btn-info" href="<?= site_url('System_config_backend/key_token'); ?>" role="button">
            <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-key" viewBox="0 0 16 16">
                <path d="M0 8a4 4 0 0 1 7.465-2H14a.5.5 0 0 1 .354.146l1.5 1.5a.5.5 0 0 1 0 .708l-1.5 1.5a.5.5 0 0 1-.708 0L13 9.207l-.646.647a.5.5 0 0 1-.708 0L11 9.207l-.646.647a.5.5 0 0 1-.708 0L9 9.207l-.646.647A.5.5 0 0 1 8 10h-.535A4 4 0 0 1 0 8m4-3a3 3 0 1 0 2.712 4.285A.5.5 0 0 1 7.163 9h.63l.853-.854a.5.5 0 0 1 .708 0l.646.647.646-.647a.5.5 0 0 1 .708 0l.646.647.646-.647a.5.5 0 0 1 .708 0l.646.647.793-.793-1-1h-6.63a.5.5 0 0 1-.451-.285A3 3 0 0 0 4 5" />
                <path d="M4 8a1 1 0 1 1-2 0 1 1 0 0 1 2 0" />
            </svg> System Config key & token</a>
    </div>
</div>


<!-- DataTales Example -->
<div class="card shadow mb-4">
    <div class="card-header py-3">
        <?php if (isset($type) && $type == 'address'): ?>
            <h6 class="m-0 font-weight-bold text-black">จัดการข้อมูล Config กลุ่มที่อยู่</h6>
        <?php elseif (isset($type) && $type == 'link'): ?>
            <h6 class="m-0 font-weight-bold text-black">จัดการข้อมูล Config ลิงค์อื่นๆ</h6>
        <?php elseif (isset($type) && $type == 'key_token'): ?>
            <h6 class="m-0 font-weight-bold text-black">จัดการข้อมูล Config key & token</h6>
        <?php else: ?>
            <h6 class="m-0 font-weight-bold text-black">จัดการข้อมูล System Config ทั้งหมด</h6>
        <?php endif; ?>
    </div>
    <div class="card-body">
        <div class="table-responsive">
            <?php
            $Index = 1;
            ?>
            <table id="newdataTables" class="table">
                <thead>
                    <tr>
                        <th style="width: 5%;">ID</th>
                        <th style="width: 15%;">Keyword</th>
                        <th style="width: 30%;">value</th>
                        <th style="width: 20%;">description</th>
                        <th style="width: 10%;">ผู้อัพเดต</th>
                        <th style="width: 7%;">วันที่</th>
                        <th style="width: 8%;">จัดการ</th>
                    </tr>
                </thead>
                <tbody>
                    <?php
                    foreach ($query as $rs) { ?>
                        <tr role="row">
                            <td><?php echo $rs->id; ?></td>
                            <td><?php echo $rs->keyword; ?></td>
                            <td class="limited-text">
                                <?php
                                $isTelesales = ((int)$rs->id === 15) || ($rs->keyword === 'telesales');
                                echo htmlspecialchars($isTelesales && !empty($rs->display_value) ? $rs->display_value : $rs->value);
                                ?>
                            </td>
                            <td><?php echo $rs->description; ?></td>
                            <td><?php echo $rs->update_by; ?></td>
                            <td><?= date('d/m/Y H:i', strtotime($rs->update_date . '+543 years')) ?> น.</td>
                            <td>
                                <a href="<?= site_url('system_config_backend/editing/' . $rs->id); ?>" class="bi bi-pencil-square fa-lg">
                                </a>
                                <a href="<?= site_url('system_config_backend/delete/' . $rs->id); ?>" class="bi bi-trash fa-lg" onclick="return confirm('ยืนยันการลบข้อมูล');">
                                </a>
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

<!-- เพิ่ม Script สำหรับ SweetAlert หากต้องการใช้ -->
<script>
    // เพิ่ม script สำหรับแสดง SweetAlert เมื่อลบข้อมูลสำเร็จ
    <?php if ($this->session->flashdata('del_success')) { ?>
        Swal.fire({
            text: 'ลบข้อมูลเรียบร้อยแล้ว',
            icon: 'success',
            confirmButtonText: 'ตกลง',
        })
    <?php } ?>
</script>