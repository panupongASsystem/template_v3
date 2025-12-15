<a class="btn btn-light" href="<?= site_url('position_backend'); ?>" role="button">
    <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-arrow-clockwise" viewBox="0 0 16 16">
        <path fill-rule="evenodd" d="M8 3a5 5 0 1 0 4.546 2.914.5.5 0 0 1 .908-.417A6 6 0 1 1 8 2v1z" />
        <path d="M8 4.466V.534a.25.25 0 0 1 .41-.192l2.36 1.966c.12.1.12.284 0 .384L8.41 4.658A.25.25 0 0 1 8 4.466z" />
    </svg>Refresh Data</a>

<!-- DataTales Example -->
<div class="card shadow mb-4">
    <div class="card-header py-3">
        <h6 class="m-0 font-weight-bold text-black">จัดการข้อมูลโครงสร้างบุคลากร</h6>
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
                        <th style="width: 37%;">ชื่อ</th>
                        <th style="width: 15%;">ผู้แก้ไข</th>
                        <th style="width: 5%;">วันที่</th>
                        <th style="width: 5%;">สถานะ</th>
                        <th style="width: 3%;">จัดการ</th>
                    </tr>
                </thead>
                <tbody>
                    <?php
                    foreach ($query as $rs) { ?>
                        <tr role="row">
                            <td align="center"><?= $Index; ?></td>
                            <td><?php echo $rs->pname; ?> / <?php echo $rs->peng; ?></td>
                            <td><?php echo $rs->pby; ?></td>
                            <td><?= date('d/m/Y H:i', strtotime($rs->pdatesave . '+543 years')) ?> น.</td>
                            <td>
                                        <label class="switch">
                                            <input class="form-check-input" type="checkbox" role="switch" id="flexSwitchCheck<?= $rs->pid; ?>" data-structure_personnel-id="<?= $rs->pid; ?>" <?= $rs->pstatus === 'show' ? 'checked' : ''; ?> onchange="updateStructure_personnelStatus<?= $rs->pid; ?>()">
                                            <span class="slider"></span>
                                        </label>
                                        <script>
                                            function updateStructure_personnelStatus<?= $rs->pid; ?>() {
                                                const structure_personnelId = <?= $rs->pid; ?>;
                                                const newStatus = document.getElementById('flexSwitchCheck<?= $rs->pid; ?>').checked ? 'show' : 'hide';

                                                // ส่งข้อมูลไปยังเซิร์ฟเวอร์ด้วย AJAX
                                                $.ajax({
                                                    type: 'POST',
                                                    url: 'position_backend/updateStructure_personnelStatus',
                                                    data: {
                                                        pid: structure_personnelId,
                                                        new_status: newStatus
                                                    },
                                                    success: function(response) {
                                                        console.log(response);
                                                        // ทำอื่นๆตามต้องการ เช่น อัพเดตหน้าเว็บ
                                                    },
                                                    error: function(error) {
                                                        console.error(error);
                                                    }
                                                });
                                            }
                                        </script>
                                    </td>
                            <td>
                                <a href="<?= site_url('position_backend/editing/' . $rs->pid); ?>"><i class="bi bi-pencil-square fa-lg "></i></a>
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