<div class="container">
    <div class="row">
        <div class="col-md-3"></div>
        <div class="col-md-7">
            <h4>เพิ่มข้อมูลวิดีทัศน์</h4>
            <form action="<?php echo site_url('video_backend/add'); ?>" 
                  method="post" class="form-horizontal" 
                  enctype="multipart/form-data" onsubmit="return validateForm()">
                <br>
                <div class="form-group row">
                    <div class="col-sm-3 control-label">เรื่อง <span class="red-add">*</span></div>
                    <div class="col-sm-6">
                        <input type="text" name="video_name" required class="form-control">
                    </div>
                </div>
                <br>
                <div class="form-group row">
                    <div class="col-sm-3 control-label">Link url วิดีโอ</div>
                    <div class="col-sm-6">
                        <input type="url" name="video_link" id="video_link" class="form-control">
                        <small class="form-text text-muted">หากใส่ลิงก์แล้ว จะไม่สามารถอัปโหลดไฟล์ได้</small>
                    </div>
                </div>
                <br>
                <div class="form-group row">
                    <div class="col-sm-3 control-label font-18">วิดีโอ</div>
                    <div class="col-sm-6">
                        <input type="file" name="video_video" id="video_video" class="form-control" accept="video/*">
                        <small class="form-text text-muted">หากเลือกไฟล์แล้ว จะไม่สามารถกรอกลิงก์ได้</small>
                    </div>
                </div>
                <br>
                <div class="form-group row">
                    <div class="col-sm-3 control-label">วันที่อัพโหลด <span class="red-add">*</span></div>
                    <div class="col-sm-6">
                        <input type="datetime-local" name="video_date" id="video_date" class="form-control" required>
                    </div>
                </div>
                <br>
                <div class="form-group row">
                    <div class="col-sm-3 control-label"></div>
                    <div class="col-sm-9">
                        <button type="submit" class="btn btn-success">บันทึกข้อมูล</button>
                        <a class="btn btn-danger" href="<?php echo site_url('video_backend'); ?>">ยกเลิก</a>
                    </div>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- เพิ่ม SweetAlert2 -->
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

<style>
    .swal2-popup {
        font-family: 'Sarabun', 'Prompt', sans-serif;
        border-radius: 15px;
    }
    .swal2-html-container {
        line-height: 1.8;
    }
    .warning-box {
        background: linear-gradient(135deg, #fff5e6 0%, #ffe0b2 100%);
        border-left: 5px solid #ff9800;
        padding: 20px;
        border-radius: 10px;
        margin: 15px 0;
        text-align: left;
    }
    .recommend-box {
        background: linear-gradient(135deg, #e3f2fd 0%, #bbdefb 100%);
        border-left: 5px solid #2196f3;
        padding: 20px;
        border-radius: 10px;
        margin: 15px 0;
        text-align: left;
    }
    .icon-warning {
        font-size: 24px;
        margin-right: 10px;
    }
    .icon-recommend {
        font-size: 24px;
        margin-right: 10px;
    }
</style>

<script>
    // แสดง popup เตือนก่อนเลือกไฟล์
    let isWarningShown = false;
    
    document.getElementById('video_video').addEventListener('click', function(e) {
        if (!isWarningShown) {
            e.preventDefault();
            const fileInput = this;
            
            Swal.fire({
                title: '<strong style="color: #ff6b6b; font-size: 28px;">⚠️ คำเตือนสำคัญ</strong>',
                html: `
                    <div class="warning-box">
                        <div style="display: flex; align-items: start;">
                            <span class="icon-warning">📁</span>
                            <div>
                                <strong style="font-size: 18px; color: #e65100;">การอัปโหลดไฟล์วิดีโอ</strong>
                                <p style="margin: 10px 0; color: #555;">
                                    การอัปโหลดไฟล์วิดีโอขนาดใหญ่จะทำให้พื้นที่จัดเก็บข้อมูลในเว็บไซต์เต็มเร็วขึ้น 
                                    ส่งผลให้ประสิทธิภาพของระบบลดลง
                                </p>
                            </div>
                        </div>
                    </div>
                    
                    <div class="recommend-box">
                        <div style="display: flex; align-items: start;">
                            <span class="icon-recommend">💡</span>
                            <div>
                                <strong style="font-size: 18px; color: #0277bd;">คำแนะนำ</strong>
                                <p style="margin: 10px 0; color: #555;">
                                    ควรอัปโหลดวิดีโอไปที่ <strong style="color: #c4302b;">YouTube</strong> 
                                    แล้วนำลิงก์มาใส่แทน
                                </p>
                                <p style="margin: 0; color: #0277bd; font-weight: bold;">
                                    ✓ ประหยัดพื้นที่จัดเก็บข้อมูล<br>
                                    ✓ โหลดเร็วขึ้น<br>
                                    ✓ รองรับการรับชมได้หลายอุปกรณ์
                                </p>
                            </div>
                        </div>
                    </div>
                    
                    <p style="margin-top: 20px; color: #666; font-size: 14px;">
                        คุณแน่ใจหรือไม่ว่าต้องการอัปโหลดไฟล์วิดีโอ?
                    </p>
                `,
                showCancelButton: true,
                confirmButtonText: '<i class="fa fa-check"></i> เข้าใจแล้ว ดำเนินการต่อ',
                cancelButtonText: '<i class="fa fa-times"></i> ยกเลิก',
                confirmButtonColor: '#4CAF50',
                cancelButtonColor: '#9e9e9e',
                width: '600px',
                padding: '30px',
                backdrop: `
                    rgba(0,0,0,0.4)
                    left top
                    no-repeat
                `,
                allowOutsideClick: false,
                customClass: {
                    confirmButton: 'btn btn-lg',
                    cancelButton: 'btn btn-lg'
                },
                buttonsStyling: true
            }).then((result) => {
                if (result.isConfirmed) {
                    isWarningShown = true;
                    fileInput.click();
                }
            });
        }
    });

    // ป้องกันให้เลือกได้แค่อย่างเดียว
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
    
    // กัน user submit แบบกรอกทั้ง link และ upload พร้อมกัน
    function validateForm() {
        const link = document.getElementById('video_link').value.trim();
        const file = document.getElementById('video_video').files.length;
        if (link !== '' && file > 0) {
            Swal.fire({
                icon: 'error',
                title: '<strong style="color: #d32f2f;">เกิดข้อผิดพลาด</strong>',
                html: `
                    <div style="padding: 20px; background: #ffebee; border-radius: 10px; margin: 15px 0;">
                        <p style="font-size: 16px; color: #c62828; margin: 0;">
                            ❌ กรุณาเลือกได้เพียง<strong> 1 วิธี</strong>เท่านั้น
                        </p>
                        <p style="margin: 15px 0 0 0; color: #666;">
                            • ลิงก์วิดีโอ (YouTube/Vimeo)<br>
                            • อัปโหลดไฟล์วิดีโอ
                        </p>
                    </div>
                `,
                confirmButtonText: '✓ ตรวจสอบอีกครั้ง',
                confirmButtonColor: '#f44336',
                width: '500px',
                padding: '25px'
            });
            return false;
        }
        return true;
    }
</script>