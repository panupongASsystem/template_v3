    <a class="btn add-btn" href="<?php echo site_url('member_backend/adding'); ?>">
        <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-plus-circle" viewBox="0 0 16 16">
            <path d="M8 15A7 7 0 1 1 8 1a7 7 0 0 1 0 14zm0 1A8 8 0 1 0 8 0a8 8 0 0 0 0 16z" />
            <path d="M8 4a.5.5 0 0 1 .5.5v3h3a.5.5 0 0 1 0 1h-3v3a.5.5 0 0 1-1 0v-3h-3a.5.5 0 0 1 0-1h3v-3A.5.5 0 0 1 8 4z" />
        </svg> เพิ่มข้อมูลสมาชิก</a>
    <a class="btn btn-light" href="<?= site_url('member_backend'); ?>" role="button">
        <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-arrow-clockwise" viewBox="0 0 16 16">
            <path fill-rule="evenodd" d="M8 3a5 5 0 1 0 4.546 2.914.5.5 0 0 1 .908-.417A6 6 0 1 1 8 2v1z" />
            <path d="M8 4.466V.534a.25.25 0 0 1 .41-.192l2.36 1.966c.12.1.12.284 0 .384L8.41 4.658A.25.25 0 0 1 8 4.466z" />
        </svg>Refresh Data</a>

    <!-- <h5 class="border border-#f5f5f5 p-2 mb-2 font-black" style="background-color: #f5f5f5;">จัดการข้อมูลผู้ใช้งาน</h5> -->
    <!-- DataTales Example -->
    <div class="card shadow mb-4">
        <div class="card-header py-3">
            <h6 class="m-0 font-weight-bold text-black">จัดการข้อมูลผู้ใช้งาน</h6>
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
                            <th style="width: 20%;">ตำแหน่ง</th>
                            <th style="width: 30%;">ชื่อ-สกุล</th>
                            <th style="width: 15%;">E-mail</th>
                            <th style="width: 10%;">เบอร์ติดต่อ</th>
                            <!-- <th style="width: 5%;">สถานะ</th> -->
                            <th style="width: 5%;">จัดการ</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php
                        foreach ($query as $rs) { ?>

                            <?php if ($_SESSION['m_level'] == 1) : ?>
                                <!-- ไม่ทำอะไรเลยเมื่อเป็น 'Super Admin' -->
                                <tr role="row">
                                    <td align="center"><?= $Index; ?></td>
                                    <td><?php echo $rs->pname; ?></td>
                                    <td><?php echo $rs->m_fname . ' ' . $rs->m_lname; ?></td>
                                    <td><?php echo $rs->m_email; ?></td>
                                    <td><?php echo $rs->m_phone; ?></td>
                                    <!-- <td>
                                        <?php if ($rs->pname !== 'System Admin') { ?>
                                            <?php if ($rs->m_status == 1) { ?>
                                                <a class="btn btn-danger responsive-btn-ban" href="<?php echo site_url('member_backend/blockUser/' . $rs->m_id); ?>" onclick="return confirm('ยืนยันการแบนข้อมูล');"><i class="fa-solid fa-user-large-slash"></i>แบน</a>
                                            <?php } elseif ($rs->m_status == 0) { ?>
                                                <a class="btn btn-info responsive-btn-ban" href="<?php echo site_url('member_backend/unblockUser/' . $rs->m_id); ?>" onclick="return confirm('ยืนยันการแบนข้อมูล');"><i class="fa-solid fa-user-check"></i>ปลด</a>
                                            <?php } ?>
                                        <?php } ?>
                                    </td> -->
                                    <td>
                                        <?php if ($_SESSION['m_level'] == 1) : ?>
                                            <!-- ทำสิ่งที่คุณต้องการทำเมื่อไม่ใช่ 'System Admin' และไม่ใช่ 'Super Admin' -->
                                            <a href="<?php echo site_url('member_backend/edit/' . $rs->m_id); ?> "><i class="bi bi-pencil-square fa-lg "></i></a>
                                        <?php endif; ?>
                                        <?php if ($rs->pname !== 'System Admin') { ?>
                                            <a href="#" role="button" onclick="confirmDelete('<?= $rs->m_id; ?>');"><i class="bi bi-trash fa-lg "></i></a>
                                            <script>
                                                function confirmDelete(m_id) {
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
                                                            window.location.href = "<?= site_url('member_backend/del/'); ?>" + m_id;
                                                        }
                                                    });
                                                }
                                            </script>
                                        <?php } ?>
                                    </td>
                                </tr>

                            <?php else : ?>

                                <?php if ($rs->pname !== 'System Admin') { ?>
                                    <tr role="row">
                                        <td align="center"><?= $Index; ?></td>
                                        <td><?php echo $rs->pname; ?></td>
                                        <td><?php echo $rs->m_fname . ' ' . $rs->m_lname; ?></td>
                                        <td><?php echo $rs->m_email; ?></td>
                                        <td><?php echo $rs->m_phone; ?></td>
                                        <!-- <td>
                                            <?php if ($_SESSION['m_level'] == 1 || $_SESSION['m_level'] == 2) : ?>
                                                <?php if ($rs->pname !== 'Super Admin') { ?>
                                                    <?php if ($rs->m_status == 1) { ?>
                                                        <a class="btn btn-danger responsive-btn-ban" href="<?php echo site_url('member_backend/blockUser/' . $rs->m_id); ?>" onclick="return confirm('ยืนยันการแบนข้อมูล');"><i class="fa-solid fa-user-large-slash"></i>แบน</a>
                                                    <?php } elseif ($rs->m_status == 0) { ?>
                                                        <a class="btn btn-info responsive-btn-ban" href="<?php echo site_url('member_backend/unblockUser/' . $rs->m_id); ?>" onclick="return confirm('ยืนยันการแบนข้อมูล');"><i class="fa-solid fa-user-check"></i>ปลด</a>
                                                    <?php } ?>
                                                <?php } ?>
                                            <?php endif; ?>
                                        </td> -->
                                        <td>
                                            <?php if ($rs->pname !== 'Super Admin') : ?>
                                                <!-- ทำสิ่งที่คุณต้องการทำเมื่อไม่ใช่ 'System Admin' และไม่ใช่ 'Super Admin' -->
                                                <a href="<?php echo site_url('member_backend/edit/' . $rs->m_id); ?> "><i class="bi bi-pencil-square fa-lg "></i></a>
                                                <?php if ($rs->pname !== 'Super Admin') { ?>
                                                    <a href="#" role="button" onclick="confirmDelete('<?= $rs->m_id; ?>');"><i class="bi bi-trash fa-lg "></i></a>
                                                    <script>
                                                        function confirmDelete(m_id) {
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
                                                                    window.location.href = "<?= site_url('member_backend/del/'); ?>" + m_id;
                                                                }
                                                            });
                                                        }
                                                    </script>
                                                <?php } ?>
                                            <?php endif; ?>
                                        </td>
                                    </tr>
                                <?php } ?>
                            <?php endif; ?>
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
            <h6 class="m-0 font-weight-bold text-primary">จัดการข้อมูลผู้ใช้งาน</h6>
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-bordered" id="dataTable" width="100%" cellspacing="0">
                    <thead>
                        <tr>
                            <th>id</th>
                            <th>ตำแหน่ง</th>
                            <th>ชื่อ-สกุล</th> -->
    <!-- <th>pwd</th> -->
    <!-- <th>เบอร์ติดต่อ</th>
                            <th>แก้ไข</th>
                            <th>ลบ</th>
                            <th>แบนผู้ใช้งาน</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($query as $index => $rs) { ?>
                            <tr>
                                <td align="center"><?= $index + 1; ?></td>
                                <td><?php echo $rs->pname; ?></td>
                                <td><?php echo $rs->m_fname . $rs->m_name . ' ' . $rs->m_lname; ?></td>
                                <td><?php echo $rs->m_phone; ?></td> -->
    <!-- <td><a class="btn btn-info" href="<?php echo site_url('member/pwd/' . $rs->m_id); ?> ">
                                        <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-pencil-square" viewBox="0 0 16 16">
                                            <path d="M15.502 1.94a.5.5 0 0 1 0 .706L14.459 3.69l-2-2L13.502.646a.5.5 0 0 1 .707 0l1.293 1.293zm-1.75 2.456-2-2L4.939 9.21a.5.5 0 0 0-.121.196l-.805 2.414a.25.25 0 0 0 .316.316l2.414-.805a.5.5 0 0 0 .196-.12l6.813-6.814z" />
                                            <path fill-rule="evenodd" d="M1 13.5A1.5 1.5 0 0 0 2.5 15h11a1.5 1.5 0 0 0 1.5-1.5v-6a.5.5 0 0 0-1 0v6a.5.5 0 0 1-.5.5h-11a.5.5 0 0 1-.5-.5v-11a.5.5 0 0 1 .5-.5H9a.5.5 0 0 0 0-1H2.5A1.5 1.5 0 0 0 1 2.5v11z" />
                                        </svg> pwd</a></td> -->
    <!-- <td><a class="btn btn-warning" href="<?php echo site_url('member/edit/' . $rs->m_id); ?> ">
                                        <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-pencil-square" viewBox="0 0 16 16">
                                            <path d="M15.502 1.94a.5.5 0 0 1 0 .706L14.459 3.69l-2-2L13.502.646a.5.5 0 0 1 .707 0l1.293 1.293zm-1.75 2.456-2-2L4.939 9.21a.5.5 0 0 0-.121.196l-.805 2.414a.25.25 0 0 0 .316.316l2.414-.805a.5.5 0 0 0 .196-.12l6.813-6.814z" />
                                            <path fill-rule="evenodd" d="M1 13.5A1.5 1.5 0 0 0 2.5 15h11a1.5 1.5 0 0 0 1.5-1.5v-6a.5.5 0 0 0-1 0v6a.5.5 0 0 1-.5.5h-11a.5.5 0 0 1-.5-.5v-11a.5.5 0 0 1 .5-.5H9a.5.5 0 0 0 0-1H2.5A1.5 1.5 0 0 0 1 2.5v11z" />
                                        </svg> แก้ไข</a></td>
                                <td>
                                    <?php if ($rs->pname !== 'Super Admin') { ?>
                                        <a class="btn btn-danger" href="<?php echo site_url('member/del/' . $rs->m_id); ?>" onclick="return confirm('ยืนยันการลบข้อมูล');">
                                            <svg xmlns="http://www.w3.Dorg/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-trash" viewBox="0 0 16 16">
                                                <path d="M5.5 5.5A.5.5 0 0 1 6 6v6a.5.5 0 0 1-1 0V6a.5.5 0 0 1 .5-.5Zm2.5 0a.5.5 0 0 1 .5.5v6a.5.5 0 0 1-1 0V6a.5.5 0 0 1 .5-.5Zm3 .5a.5.5 0 0 0-1 0v6a.5.5 0 0 0 1 0V6Z" />
                                                <path d="M14.5 3a1 1 0 0 1-1 1H13v9a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2V4h-.5a1 1 0 0 1-1-1V2a1 1 0 0 1 1-1H6a1 1 0 0 1 1-1h2a1 1 0 0 1 1 1h3.5a1 1 0 0 1 1 1v1ZM4.118 4 4 4.059V13a1 1 0 0 0 1 1h6a1 1 0 0 0 1-1V4.059L11.882 4H4.118ZM2.5 3h11V2h-11v1Z" />
                                            </svg>ลบ
                                        </a>
                                    <?php } ?>
                                </td>
                                <td>
                                    <?php if ($rs->pname !== 'Super Admin') { ?>
                                        <?php if ($rs->m_status == 1) { ?>
                                            <a class="btn btn-secondary" href="<?php echo site_url('member/blockUser/' . $rs->m_id); ?>" onclick="return confirm('ยืนยันการแบนข้อมูล');">แบน</a>
                                        <?php } elseif ($rs->m_status == 0) { ?>
                                            <a class="btn btn-primary" href="<?php echo site_url('member/unblockUser/' . $rs->m_id); ?>" onclick="return confirm('ยืนยันการแบนข้อมูล');">ปลดแบน</a>
                                        <?php } ?> <?php } ?>
                                </td>
                            </tr>
                        <?php } ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div> -->