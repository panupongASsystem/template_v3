<a class="btn add-btn" href="<?= site_url('operation_meeting_backend/adding'); ?>" role="button">
    <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-plus-circle" viewBox="0 0 16 16">
        <path d="M8 15A7 7 0 1 1 8 1a7 7 0 0 1 0 14zm0 1A8 8 0 1 0 8 0a8 8 0 0 0 0 16z" />
        <path d="M8 4a.5.5 0 0 1 .5.5v3h3a.5.5 0 0 1 0 1h-3v3a.5.5 0 0 1-1 0v-3h-3a.5.5 0 0 1 0-1h3v-3A.5.5 0 0 1 8 4z" />
    </svg> เพิ่มข้อมูล</a>
<a class="btn btn-light" href="<?= site_url('operation_meeting_backend'); ?>" role="button">
    ย้อนกลับ</a>

<!-- <h5 class="border border-#f5f5f5 p-2 mb-2 font-black" style="background-color: #f5f5f5;">จัดการข้อมูลข่าวสารประจำเดือน</h5> -->
<!-- DataTales Example -->
<div class="card shadow mb-4">
    <div class="card-header py-3">
        <h6 class="m-0 font-weight-bold text-black">จัดการข้อมูลรายงานการประชุมสภา</h6>
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
                        <th style="width: 25%;">ชื่อ</th>
                        <th style="width: 30%;">รายละเอียด</th>
                        <th style="width: 10%;">ไฟล์ PDF</th>
                        <th style="width: 10%;">อัพโหลด</th>
                        <th style="width: 7%;">วันที่</th>
                        <th style="width: 5%;">สถานะ</th>
                        <th style="width: 10%;">จัดการ</th>
                    </tr>
                </thead>
                <tbody>
                    <?php
                    foreach ($query as $rs) { ?>
                        <tr role="row">
                            <td align="center"><?= $Index; ?></td>
                            <td class="limited-text"><?= $rs->operation_meeting_name; ?></td>
                            <td class="limit-one-font"><?= $rs->operation_meeting_detail; ?></td>
                            <td>
                                <?php foreach ($rs->pdf as $pdf) : ?>
                                    <a class="btn btn-info btn-sm mt-1" href="<?php echo base_url('docs/file/' . $pdf->operation_meeting_pdf_pdf); ?>" target="_blank">ดูไฟล์เดิม!</a>
                                <?php endforeach; ?>
                                <?php foreach ($rs->doc as $doc) : ?>
                                    <a class="btn btn-info btn-sm mt-1" href="<?php echo base_url('docs/file/' . $doc->operation_meeting_file_doc); ?>" target="_blank">ดูไฟล์เดิม!</a>
                                <?php endforeach; ?>
                            </td>
                            <td><?= $rs->operation_meeting_by; ?></td>
                            <td><?= date('d/m/Y H:i', strtotime($rs->operation_meeting_date . '+543 years')) ?> น.</td>
                            <td>
                                <label class="switch">
                                    <input class="form-check-input" type="checkbox" role="switch" id="flexSwitchCheck<?= $rs->operation_meeting_id; ?>" data-operation-cdm-id="<?= $rs->operation_meeting_id; ?>" <?= $rs->operation_meeting_status === 'show' ? 'checked' : ''; ?> onchange="toggleUserOperationCdmStatus<?= $rs->operation_meeting_id; ?>()">
                                    <span class="slider"></span>
                                </label>
                                <script>
                                    function toggleUserOperationCdmStatus<?= $rs->operation_meeting_id; ?>() {
                                        const operationCdmId = <?= $rs->operation_meeting_id; ?>;
                                        const checkbox = $('#flexSwitchCheck<?= $rs->operation_meeting_id; ?>');
                                        const newStatus = checkbox.prop('checked') ? 'show' : 'hide';

                                        $.ajax({
                                            type: 'POST',
                                            url: '<?= base_url("operation_meeting_backend/toggleUserOperationCdmStatus") ?>',
                                            data: {
                                                operation_meeting_id: operationCdmId,
                                                new_status: newStatus
                                            },
                                            dataType: 'json',
                                            success: function(response) {
                                                if (response.status === 'success') {
                                                    console.log(response.message);
                                                    // อัพเดต UI ตามต้องการ
                                                } else {
                                                    console.error('Failed to update status:', response.message);
                                                    // ย้อนกลับสถานะ checkbox
                                                    checkbox.prop('checked', !checkbox.prop('checked'));
                                                    alert(response.message);
                                                }
                                            },
                                            error: function(xhr, status, error) {
                                                console.error('AJAX error:', status, error);
                                                // ย้อนกลับสถานะ checkbox
                                                checkbox.prop('checked', !checkbox.prop('checked'));
                                                alert('เกิดข้อผิดพลาดในการอัพเดตสถานะ');
                                            }
                                        });
                                    }
                                </script>
                            </td>
                            <td>
                                <a href="<?= site_url('operation_meeting_backend/editing/' . $rs->operation_meeting_id); ?>"><i class="bi bi-pencil-square fa-lg "></i></a>
                                <a href="#" role="button" onclick="confirmDelete(<?= $rs->operation_meeting_id; ?>);"><i class="bi bi-trash fa-lg "></i></a>

                                <script>
                                    function confirmDelete(operation_meeting_id) {
                                        Swal.fire({
                                            title: 'กดเพื่อยืนยัน?',
                                            text: "คุณจะไม่สามารถกู้คืนได้อีก!",
                                            icon: 'warning',
                                            showCancelButton: true,
                                            confirmButtonColor: '#3085d6',
                                            cancelButtonColor: '#d33',
                                            confirmButtonText: 'ใช่, ต้องการลบ!',
                                            cancelButtonText: 'ยกเลิก'
                                        }).then((result) => {
                                            if (result.isConfirmed) {
                                                $.ajax({
                                                    url: "<?= site_url('operation_meeting_backend/del_operation_meeting/'); ?>" + operation_meeting_id,
                                                    type: 'GET',
                                                    success: function(response) {
                                                        Swal.fire(
                                                            'ลบสำเร็จ!',
                                                            'ข้อมูลได้ถูกลบเรียบร้อย.',
                                                            'success'
                                                        ).then(() => {
                                                            // รีเฟรชตารางหรือหน้าเว็บหากจำเป็น
                                                            location.reload();
                                                        });
                                                    },
                                                    error: function(xhr, status, error) {
                                                        Swal.fire(
                                                            'เกิดข้อผิดพลาด!',
                                                            'ไม่สามารถลบข้อมูลได้.',
                                                            'error'
                                                        );
                                                        console.error('AJAX error:', status, error);
                                                    }
                                                });
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
                                <td>รูปภาพหน้าปก : <img src="<?= base_url('docs/img/' . $rs->operation_meeting_img); ?>" width="180px" height="140px"> <br>
                                    รูปภาพเพิ่มเติม : <?php foreach (explode(',', $rs->additional_images) as $img) { ?>
                                        <img src="<?= base_url('docs/img/' . $img); ?>" width="100px" height="60px">
                                    <?php } ?>
                                </td>
                                </td>
                                <td><?= $rs->operation_meeting_name; ?></td>
                                <td><?= mb_substr($rs->operation_meeting_detail, 0, 200, 'UTF-8'); ?>...</td>
                                <td><?= $rs->operation_meeting_by; ?></td>
                                <td><?= date('d/m/Y : H:i', strtotime($rs->operation_meeting_datesave . '+543 years')) ?></td>
                                <td>
                                       <label class="switch">
                                           <input class="form-check-input" type="checkbox" role="switch" id="flexSwitchCheck<?= $rs->operation_meeting_id; ?>" data-operation_meeting-id="<?= $rs->operation_meeting_id; ?>" <?= $rs->operation_meeting_status === 'show' ? 'checked' : ''; ?>>
                                           <span class="slider round"></span>
                                       </label>
                                       <script>
                                           // เลือก Switch ตามรายการในลูปโดยใช้ข้อมูลที่เกี่ยวข้องกับแต่ละรายการ
                                           const switchCheckbox<?= $rs->operation_meeting_id; ?> = document.getElementById('flexSwitchCheck<?= $rs->operation_meeting_id; ?>');

                                           switchCheckbox<?= $rs->operation_meeting_id; ?>.addEventListener('change', function() {
                                               const operation_meetingId = this.getAttribute('data-operation_meeting-id');
                                               const operation_meetingtatus = this.checked ? 'show' : 'hide';

                                               // ส่งข้อมูลไปยังเซิร์ฟเวอร์ด้วย AJAX
                                               $.ajax({
                                                   type: 'POST',
                                                   url: 'operation_meeting/updateoperation_meetingStatus', // แทนค่า '/your-controller/' ด้วย URL ของ Controller ที่คุณสร้าง
                                                   data: {
                                                       operation_meeting_id: operation_meetingId,
                                                       new_status: operation_meetingtatus
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
                                <td><a href="<?php echo site_url('operation_meeting/edit/' . $rs->operation_meeting_id); ?>" class="btn btn-warning btn-xs">แก้ไข</a></td>
                                <td><a class="btn btn-danger btn-xs" href="<?= site_url('operation_meeting/del_operation_meeting/' . $rs->operation_meeting_id); ?>" role="button" onclick="return confirm('ยืนยันการลบข้อมูล??');">ลบ</a></td>
                            </tr>
                        <?php } ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div> -->