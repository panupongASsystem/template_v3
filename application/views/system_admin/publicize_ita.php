<!-- แถบบนสำหรับเลือกแท็บ -->
<ul class="nav nav-tabs" id="myTab" role="tablist">
    <li class="nav-item" role="presentation">
        <button class="nav-link active" id="ita-tab" data-bs-toggle="tab" data-bs-target="#ita-content" type="button" role="tab">
            <i class="bi bi-megaphone"></i> ประชาสัมพันธ์ EIT/IIT
        </button>
    </li>
    <li class="nav-item" role="presentation">
        <button class="nav-link" id="lineoa-tab" data-bs-toggle="tab" data-bs-target="#lineoa-content" type="button" role="tab">
            <i class="bi bi-qr-code"></i> LINE OA QR Code
        </button>
    </li>
</ul>

<!-- เนื้อหาของแต่ละแท็บ -->
<div class="tab-content" id="myTabContent">

    <!-- ==================== แท็บ Publicize ITA ==================== -->
    <div class="tab-pane fade show active" id="ita-content" role="tabpanel">
        <br>
		<a class="btn add-btn" href="<?= site_url('publicize_ita_backend/adding_publicize_ita'); ?>" role="button">
            <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-plus-circle" viewBox="0 0 16 16">
                <path d="M8 15A7 7 0 1 1 8 1a7 7 0 0 1 0 14zm0 1A8 8 0 1 0 8 0a8 8 0 0 0 0 16z" />
                <path d="M8 4a.5.5 0 0 1 .5.5v3h3a.5.5 0 0 1 0 1h-3v3a.5.5 0 0 1-1 0v-3h-3a.5.5 0 0 1 0-1h3v-3A.5.5 0 0 1 8 4z" />
            </svg> เพิ่มข้อมูล
        </a>
        <a class="btn btn-light" href="<?= site_url('publicize_ita_backend'); ?>" role="button">
            <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-arrow-clockwise" viewBox="0 0 16 16">
                <path fill-rule="evenodd" d="M8 3a5 5 0 1 0 4.546 2.914.5.5 0 0 1 .908-.417A6 6 0 1 1 8 2v1z" />
                <path d="M8 4.466V.534a.25.25 0 0 1 .41-.192l2.36 1.966c.12.1.12.284 0 .384L8.41 4.658A.25.25 0 0 1 8 4.466z" />
            </svg> Refresh Data
        </a>

        <div class="card shadow mb-4">
            <div class="card-header py-3">
                <h6 class="m-0 font-weight-bold text-black">จัดการข้อมูลประชาสัมพันธ์ EIT/IIT</h6>
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <?php $Index = 1; ?>
                    <table id="newdataTables" class="table">
                        <thead>
                            <tr>
                                <th style="width: 4%;">ลำดับ</th>
                                <th style="width: 10%;">รูปภาพ</th>
                                <th style="width: 20%;">ชื่อ</th>
                                <th style="width: 20%;">ลิงค์</th>
                                <th style="width: 10%;">อัพโหลด</th>
                                <th style="width: 10%;">วันที่</th>
                                <th style="width: 12%;">การแสดง</th>
                                <th style="width: 7%;">สถานะ</th>
                                <th style="width: 7%;">จัดการ</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($query as $rs) {
                                $CI = &get_instance();
                                $CI->load->model('publicize_ita_model');

                                $display_status = $CI->publicize_ita_model->get_display_status_text($rs);
                                $is_active = $CI->publicize_ita_model->check_display_status($rs);
                            ?>
                                <tr role="row" <?= !$is_active ? 'style="background-color: #f8f9fa; opacity: 0.7;"' : ''; ?>>
                                    <td align="center"><?= $Index; ?></td>
                                    <td><img src="<?= base_url('docs/img/' . $rs->publicize_ita_img); ?>" width="120px" height="80px"></td>
                                    <td class="limited-text"><?= $rs->publicize_ita_name; ?></td>
                                    <td class="limited-text"><?= $rs->publicize_ita_link; ?></td>
                                    <td><?= $rs->publicize_ita_by; ?></td>
                                    <td><?= date('d/m/Y H:i', strtotime($rs->publicize_ita_datesave . '+543 years')) ?> น.</td>
                                    <td>
                                        <small>
                                            <?php if ($rs->publicize_ita_display_type == 'always'): ?>
                                                <span class="badge badge-success">แสดงตลอด</span>
                                            <?php else: ?>
                                                <span class="badge badge-info">ช่วงเวลา</span><br>
                                                <small><?= $CI->publicize_ita_model->get_period_text($rs); ?></small>
                                            <?php endif; ?>
                                        </small>
                                        <br>
                                        <small>
                                            <?php
                                            $status_class = '';
                                            switch ($display_status) {
                                                case 'แสดงตลอด':
                                                case 'กำลังแสดง':
                                                    $status_class = 'badge-success';
                                                    break;
                                                case 'ยังไม่ถึงเวลาแสดง':
                                                    $status_class = 'badge-warning';
                                                    break;
                                                case 'หมดเวลาแสดงแล้ว':
                                                    $status_class = 'badge-danger';
                                                    break;
                                                default:
                                                    $status_class = 'badge-secondary';
                                            }
                                            ?>
                                            <span class="badge <?= $status_class; ?>"><?= $display_status; ?></span>
                                        </small>
                                    </td>
                                    <td>
                                        <label class="switch">
                                            <input class="form-check-input" type="checkbox" role="switch"
                                                id="flexSwitchCheck<?= $rs->publicize_ita_id; ?>"
                                                data-publicize_ita-id="<?= $rs->publicize_ita_id; ?>"
                                                <?= $rs->publicize_ita_status === 'show' ? 'checked' : ''; ?>
                                                onchange="updatepublicize_itaStatus<?= $rs->publicize_ita_id; ?>()">
                                            <span class="slider"></span>
                                        </label>
                                        <script>
                                            function updatepublicize_itaStatus<?= $rs->publicize_ita_id; ?>() {
                                                const publicize_itaId = <?= $rs->publicize_ita_id; ?>;
                                                const newStatus = document.getElementById('flexSwitchCheck<?= $rs->publicize_ita_id; ?>').checked ? 'show' : 'hide';

                                                $.ajax({
                                                    type: 'POST',
                                                    url: 'publicize_ita_backend/updatepublicize_itaStatus',
                                                    data: {
                                                        publicize_ita_id: publicize_itaId,
                                                        new_status: newStatus
                                                    },
                                                    success: function(response) {
                                                        console.log(response);
                                                    },
                                                    error: function(error) {
                                                        console.error(error);
                                                    }
                                                });
                                            }
                                        </script>
                                    </td>
                                    <td>
                                        <a href="<?= site_url('publicize_ita_backend/editing_publicize_ita/' . $rs->publicize_ita_id); ?>"><i class="bi bi-pencil-square fa-lg"></i></a>
                                        <a href="#" role="button" onclick="confirmDelete('<?= $rs->publicize_ita_id; ?>');"><i class="bi bi-trash fa-lg"></i></a>
                                        <script>
                                            function confirmDelete(publicize_ita_id) {
                                                Swal.fire({
                                                    title: 'คุณแน่ใจที่จะลบ?',
                                                    text: "คุณจะไม่สามารถกู้คืนได้อีก!",
                                                    icon: 'warning',
                                                    showCancelButton: true,
                                                    confirmButtonColor: '#3085d6',
                                                    cancelButtonColor: '#d33',
                                                    confirmButtonText: 'ใช่, ต้องการลบ!',
                                                    cancelButtonText: 'ยกเลิก'
                                                }).then((result) => {
                                                    if (result.isConfirmed) {
                                                        window.location.href = "<?= site_url('publicize_ita_backend/del_publicize_ita/'); ?>" + publicize_ita_id;
                                                    }
                                                });
                                            }
                                        </script>
                                    </td>
                                </tr>
                            <?php $Index++;
                            } ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

    <!-- ==================== แท็บ LINE OA QR Code ==================== -->
    <div class="tab-pane fade" id="lineoa-content" role="tabpanel">
        <br>
        <!-- <a class="btn add-btn" href="<?= site_url('publicize_ita_backend/adding_lineoa'); ?>" role="button">
            <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-plus-circle" viewBox="0 0 16 16">
                <path d="M8 15A7 7 0 1 1 8 1a7 7 0 0 1 0 14zm0 1A8 8 0 1 0 8 0a8 8 0 0 0 0 16z" />
                <path d="M8 4a.5.5 0 0 1 .5.5v3h3a.5.5 0 0 1 0 1h-3v3a.5.5 0 0 1-1 0v-3h-3a.5.5 0 0 1 0-1h3v-3A.5.5 0 0 1 8 4z" />
            </svg> เพิ่ม QR Code
        </a> -->
        <a class="btn btn-light" href="<?= site_url('publicize_ita_backend'); ?>" role="button">
            <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-arrow-clockwise" viewBox="0 0 16 16">
                <path fill-rule="evenodd" d="M8 3a5 5 0 1 0 4.546 2.914.5.5 0 0 1 .908-.417A6 6 0 1 1 8 2v1z" />
                <path d="M8 4.466V.534a.25.25 0 0 1 .41-.192l2.36 1.966c.12.1.12.284 0 .384L8.41 4.658A.25.25 0 0 1 8 4.466z" />
            </svg> Refresh Data
        </a>

        <div class="card shadow mb-4">
            <div class="card-header py-3">
                <h6 class="m-0 font-weight-bold text-black">จัดการ LINE OA QR Code</h6>
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <?php $Index2 = 1; ?>
                    <table id="newdataTables" class="table">
                        <thead>
                            <tr>
                                <th style="width: 5%;">ลำดับ</th>
                                <th style="width: 12%;">รูป QR Code</th>
                                <th style="width: 20%;">ชื่อ</th>
                                <th style="width: 23%;">ลิงก์</th>
                                <th style="width: 12%;">อัพโหลดโดย</th>
                                <th style="width: 13%;">วันที่</th>
                                <th style="width: 8%;">สถานะ</th>
                                <th style="width: 7%;">จัดการ</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($query_lineoa as $lineoa) { ?>
                                <tr>
                                    <td align="center"><?= $Index2; ?></td>
                                    <td class="text-center align-middle">
                                        <?php if (!empty($lineoa->lineoa_img)): ?>
                                            <img src="<?= base_url('docs/img/' . $lineoa->lineoa_img); ?>"
                                                width="100px"
                                                height="100px"
                                                alt="<?= $lineoa->lineoa_name; ?>">
                                        <?php else: ?>
                                            <span class="">QRcode ไลน์ Live</span>
                                        <?php endif; ?>
                                    </td>
                                    <td><?= $lineoa->lineoa_name; ?></td>
                                    <td class="limited-text">
                                        <?php if (!empty($lineoa->lineoa_link)): ?>
                                            <a href="<?= $lineoa->lineoa_link; ?>" target="_blank" rel="noopener noreferrer">
                                                <i class="bi bi-link-45deg"></i> <?= $lineoa->lineoa_link; ?>
                                            </a>
                                        <?php else: ?>
                                            <span class="text-muted">-</span>
                                        <?php endif; ?>
                                    </td>
                                    <td><?= $lineoa->lineoa_by; ?></td>
                                    <td><?= date('d/m/Y H:i', strtotime($lineoa->lineoa_datesave . '+543 years')) ?> น.</td>
                                    <td>
                                        <label class="switch">
                                            <input class="form-check-input" type="checkbox" role="switch"
                                                id="flexSwitchLineoa<?= $lineoa->lineoa_id; ?>"
                                                data-lineoa-id="<?= $lineoa->lineoa_id; ?>"
                                                <?= $lineoa->lineoa_status === 'show' ? 'checked' : ''; ?>
                                                onchange="updateLineoaStatus<?= $lineoa->lineoa_id; ?>()">
                                            <span class="slider"></span>
                                        </label>
                                        <script>
                                            function updateLineoaStatus<?= $lineoa->lineoa_id; ?>() {
                                                const lineoaId = <?= $lineoa->lineoa_id; ?>;
                                                const newStatus = document.getElementById('flexSwitchLineoa<?= $lineoa->lineoa_id; ?>').checked ? 'show' : 'hide';

                                                $.ajax({
                                                    type: 'POST',
                                                    url: 'publicize_ita_backend/update_lineoa_status',
                                                    data: {
                                                        lineoa_id: lineoaId,
                                                        new_status: newStatus
                                                    },
                                                    success: function(response) {
                                                        console.log(response);
                                                    },
                                                    error: function(error) {
                                                        console.error(error);
                                                    }
                                                });
                                            }
                                        </script>
                                    </td>
                                    <td>
                                        <a href="<?= site_url('publicize_ita_backend/editing_lineoa/' . $lineoa->lineoa_id); ?>"><i class="bi bi-pencil-square fa-lg"></i></a>
                                        <!-- <a href="#" role="button" onclick="confirmDeleteLineoa('<?= $lineoa->lineoa_id; ?>');"><i class="bi bi-trash fa-lg"></i></a>
                                        <script>
                                            function confirmDeleteLineoa(lineoa_id) {
                                                Swal.fire({
                                                    title: 'คุณแน่ใจที่จะลบ?',
                                                    text: "คุณจะไม่สามารถกู้คืนได้อีก!",
                                                    icon: 'warning',
                                                    showCancelButton: true,
                                                    confirmButtonColor: '#3085d6',
                                                    cancelButtonColor: '#d33',
                                                    confirmButtonText: 'ใช่, ต้องการลบ!',
                                                    cancelButtonText: 'ยกเลิก'
                                                }).then((result) => {
                                                    if (result.isConfirmed) {
                                                        window.location.href = "<?= site_url('publicize_ita_backend/del_lineoa/'); ?>" + lineoa_id;
                                                    }
                                                });
                                            }
                                        </script> -->
                                    </td>
                                </tr>
                            <?php $Index2++;
                            } ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

</div>