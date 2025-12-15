<div class="container">
    <div class="row">
        <div class="col-md-3"></div>
        <div class="col-md-7">
            <h4>แก้ไข LINE OA QR Code</h4>
            <form action="<?php echo site_url('publicize_ita_backend/edit_lineoa/' . $rsedit->lineoa_id); ?>" method="post" class="form-horizontal" enctype="multipart/form-data">
                <br>
                <div class="form-group row">
                    <div class="col-sm-2 control-label">ชื่อ</div>
                    <div class="col-sm-6">
                        <input type="text" name="lineoa_name" required class="form-control" value="<?= $rsedit->lineoa_name; ?>">
                    </div>
                </div>
                <br>
                <div class="form-group row">
                    <div class="col-sm-2 control-label">ลิงก์ (Link)</div>
                    <div class="col-sm-6">
                        <input type="url" name="lineoa_link" class="form-control"
                            value="<?= isset($rsedit->lineoa_link) ? $rsedit->lineoa_link : ''; ?>"
                            placeholder="https://lin.ee/xxxxx หรือ https://line.me/R/ti/p/@xxxxx">
                        <small class="form-text text-muted">ใส่ลิงก์ LINE Official Account (ไม่บังคับ - ถ้าไม่ใส่จะแสดงแค่รูป QR Code)</small>
                    </div>
                </div>
                <br>
                <div class="form-group row">
                    <div class="col-sm-2 control-label">ไฟล์รูป QR Code</div>
                    <div class="col-sm-6">
                        <?php if (!empty($rsedit->lineoa_img)): ?>
                            ภาพเก่า <br>
                            <img src="<?= base_url('docs/img/' . $rsedit->lineoa_img); ?>" width="220px" height="220px">
                            <br><br>
                        <?php else: ?>
                            <div class="alert alert-info">
                                ตอนนี้ใช้ QRcode ไลน์ Live
                            </div>
                        <?php endif; ?>

                        <?php if (!empty($rsedit->lineoa_img)): ?>
                            เลือกใหม่ (ถ้าต้องการเปลี่ยน)
                        <?php else: ?>
                            เลือกรูป QR Code
                        <?php endif; ?>
                        <br>
                        <input type="file" name="lineoa_img" class="form-control" accept="image/*">
                        <small class="form-text text-muted">รองรับไฟล์: JPG, JPEG, PNG, GIF (แนะนำขนาด 400x400 พิกเซล)</small>
                    </div>
                </div>
                <br>
                <div class="form-group row">
                    <div class="col-sm-2 control-label"></div>
                    <div class="col-sm-10">
                        <button type="submit" class="btn btn-success">บันทึกข้อมูล</button>
                        <a class="btn btn-danger" href="<?php echo site_url('publicize_ita_backend'); ?>">ยกเลิก</a>
                    </div>
                </div>
            </form>
        </div>
    </div>
</div>