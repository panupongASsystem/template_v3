<div class="container">
    <div class="row">
        <div class="col-md-3"></div>
        <div class="col-md-7">
            <h4>แก้ไขข้อมูลวิดีทัศน์</h4>
            <form action="<?php echo site_url('video_backend/edit/' . $rsedit->video_id); ?>" 
                  method="post" class="form-horizontal" enctype="multipart/form-data" onsubmit="return validateForm()">
                <br>
                <div class="form-group row">
                    <div class="col-sm-3 control-label">เรื่อง <span class="red-add">*</span></div>
                    <div class="col-sm-6">
                        <input type="text" name="video_name" required class="form-control" 
                               value="<?= $rsedit->video_name; ?>">
                    </div>
                </div>
                <br>

                <div class="form-group row">
                    <div class="col-sm-3 control-label">ลิงค์วิดีโอ</div>
                    <div class="col-sm-6">
                        <input type="url" name="video_link" id="video_link" class="form-control" 
                               value="<?= $rsedit->video_link; ?>" 
                               <?php if (!empty($rsedit->video_video)) echo 'disabled'; ?>>
                        <small class="form-text text-muted">หากใส่ลิงก์แล้ว จะไม่สามารถอัปโหลดไฟล์ได้</small>
                    </div>
                </div>
                <br>

                <div class="form-group row">
                    <div class="col-sm-3 control-label">อัปโหลดวิดีโอ</div>
                    <div class="col-sm-6">
                        <input type="file" name="video_video" id="video_video" class="form-control-file" accept="video/*"
                               <?php if (!empty($rsedit->video_link)) echo 'disabled'; ?>>

                        <?php if (!empty($rsedit->video_video)): ?>
                            <p class="mt-2">ไฟล์ปัจจุบัน:
                                <a href="<?= base_url('docs/video/' . $rsedit->video_video); ?>" target="_blank">
                                    <?= $rsedit->video_video; ?>
                                </a>
                            </p>
                            <video width="100%" height="150" controls>
                                <source src="<?= base_url('docs/video/' . $rsedit->video_video); ?>" type="video/mp4">
                            </video>

                            <!-- ปุ่มลบเฉพาะไฟล์วิดีโอ -->
                            <p class="mt-2">
                                <a href="<?= site_url('video_backend/delete_file/' . $rsedit->video_id); ?>" 
                                   class="btn btn-danger btn-sm" 
                                   onclick="return confirm('คุณแน่ใจหรือไม่ว่าต้องการลบไฟล์วิดีโอนี้?');">
                                    ลบไฟล์วิดีโอ
                                </a>
                            </p>
                        <?php endif; ?>

                        <small class="form-text text-muted">หากอัปโหลดไฟล์ใหม่ ไฟล์เก่าจะถูกลบโดยอัตโนมัติ</small>
                    </div>
                </div>
                <br>

                <div class="form-group row">
                    <div class="col-sm-3 control-label">วันที่อัพโหลด <span class="red-add">*</span></div>
                    <div class="col-sm-6">
                        <input type="datetime-local" name="video_date" id="video_date" class="form-control" 
                               value="<?= $rsedit->video_date; ?>" required>
                    </div>
                </div>
                <br>

                <div class="form-group row">
                    <div class="col-sm-3 control-label"></div>
                    <div class="col-sm-10">
                        <button type="submit" class="btn btn-success">บันทึกข้อมูล</button>
                        <a class="btn btn-danger" href="<?php echo site_url('video_backend'); ?>">ยกเลิก</a>
                    </div>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
    // ป้องกันให้เลือกได้แค่อย่างเดียว (ใช้ตอนยังไม่มีไฟล์/ลิงก์)
    document.getElementById('video_link').addEventListener('input', function() {
        if (this.value.trim() !== '') {
            document.getElementById('video_video').disabled = true;
        } else {
            document.getElementById('video_video').disabled = false;
        }
    });

    document.getElementById('video_video').addEventListener('change', function() {
        if (this.files.length > 0) {
            document.getElementById('video_link').disabled = true;
        } else {
            document.getElementById('video_link').disabled = false;
        }
    });

    // กัน user submit กรอกทั้งสองอัน
    function validateForm() {
        const link = document.getElementById('video_link').value.trim();
        const file = document.getElementById('video_video').files.length;
        if (link !== '' && file > 0) {
            alert('กรุณาเลือกได้เพียงอย่างเดียว: ลิงก์วิดีโอ หรือ ไฟล์อัปโหลด');
            return false;
        }
        return true;
    }
</script>
