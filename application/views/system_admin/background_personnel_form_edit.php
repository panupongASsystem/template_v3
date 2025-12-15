<div class="container">
    <div class="row">
        <div class="col-md-3"></div>
        <div class="col-md-7">
            <h4>แก้ไขข้อมูลแบนเนอร์บุคลากร</h4>
            <form action=" <?php echo site_url('background_personnel_backend/edit_background_personnel/' . $rsedit->background_personnel_id); ?> " method="post" class="form-horizontal" enctype="multipart/form-data" onsubmit="return validateForm()">
                <br>
                <div class="form-group row">
                    <div class="col-sm-2 control-label">ชื่อ</div>
                    <div class="col-sm-6">
                        <input type="text" name="background_personnel_name" required class="form-control" value="<?= $rsedit->background_personnel_name; ?>">
                    </div>
                </div>
                <br>
                <div class="form-group row">
                    <div class="col-sm-2 control-label">ตำแหน่ง</div>
                    <div class="col-sm-6">
                        <input type="text" name="background_personnel_rank" required class="form-control" value="<?= $rsedit->background_personnel_rank; ?>">
                    </div>
                </div>
                <br>
                <div class="form-group row">
                    <div class="col-sm-2 control-label">เบอร์มือถือ</div>
                    <div class="col-sm-6">
                        <input type="text" name="background_personnel_phone" required class="form-control" value="<?= $rsedit->background_personnel_phone; ?>">
                    </div>
                </div>
                <br>

                <!-- รูปที่ 1 -->
                <div class="form-group row">
                    <div class="col-sm-2 control-label">รูปที่ 1</div>
                    <div class="col-sm-6">
                        <?php if (!empty($rsedit->background_personnel_img1)): ?>
                            <div class="mb-2">
                                <label>รูปเก่า:</label><br>
                                <img src="<?= base_url('docs/img/' . $rsedit->background_personnel_img1); ?>" width="220px" height="180">
                            </div>
                        <?php endif; ?>
                        <label>เลือกใหม่ (ถ้าต้องการเปลี่ยน):</label>
                        <input type="file" name="background_personnel_img1" class="form-control" accept="image/*">
                    </div>
                </div>
                <br>

                <!-- รูปที่ 2 -->
                <div class="form-group row">
                    <div class="col-sm-2 control-label">รูปที่ 2</div>
                    <div class="col-sm-6">
                        <?php if (!empty($rsedit->background_personnel_img2)): ?>
                            <div class="mb-2">
                                <label>รูปเก่า:</label><br>
                                <img src="<?= base_url('docs/img/' . $rsedit->background_personnel_img2); ?>" width="220px" height="180">
                            </div>
                        <?php else: ?>
                            <div class="mb-2">
                                <small class="text-muted">ยังไม่มีรูปที่ 2</small>
                            </div>
                        <?php endif; ?>
                        <label>เลือกใหม่:</label>
                        <input type="file" name="background_personnel_img2" class="form-control" accept="image/*">
                        <?php if (!empty($rsedit->background_personnel_img2)): ?>
                            <div class="mt-2">
                                <input type="checkbox" name="delete_img2" value="1"> ลบรูปที่ 2
                            </div>
                        <?php endif; ?>
                    </div>
                </div>
                <br>

                <!-- รูปที่ 3 -->
                <div class="form-group row">
                    <div class="col-sm-2 control-label">รูปที่ 3</div>
                    <div class="col-sm-6">
                        <?php if (!empty($rsedit->background_personnel_img3)): ?>
                            <div class="mb-2">
                                <label>รูปเก่า:</label><br>
                                <img src="<?= base_url('docs/img/' . $rsedit->background_personnel_img3); ?>" width="220px" height="180">
                            </div>
                        <?php else: ?>
                            <div class="mb-2">
                                <small class="text-muted">ยังไม่มีรูปที่ 3</small>
                            </div>
                        <?php endif; ?>
                        <label>เลือกใหม่:</label>
                        <input type="file" name="background_personnel_img3" class="form-control" accept="image/*">
                        <?php if (!empty($rsedit->background_personnel_img3)): ?>
                            <div class="mt-2">
                                <input type="checkbox" name="delete_img3" value="1"> ลบรูปที่ 3
                            </div>
                        <?php endif; ?>
                    </div>
                </div>
                <br>

                <div class="form-group row">
                    <div class="col-sm-2 control-label"></div>
                    <div class="col-sm-10">
                        <button type="submit" class="btn btn-success">บันทึกข้อมูล</button>
                        <a class="btn btn-danger" href="<?php echo site_url('background_personnel_backend'); ?>">ยกเลิก</a>
                    </div>
                </div>
            </form>
        </div>
    </div>
</div>