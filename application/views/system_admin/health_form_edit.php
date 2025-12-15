<div class="container">
    <div class="row">
        <div class="col-md-2"></div>
        <div class="col-md-7">
            <h4>แก้ไขข้อมูลสาธารณสุข</h4>
            <form action=" <?php echo site_url('health_backend/edit/' . $rsedit->health_id); ?> " method="post" class="form-horizontal" enctype="multipart/form-data" onsubmit="return validateForm()">
                <br>
                <div class="form-group row">
                    <div class="col-sm-2 control-label">ย่อหน้ากิจกรรม</div>
                    <div class="col-sm-10">
                        <textarea name="health_name" id="health_name"><?= $rsedit->health_name; ?></textarea>
                        <script>
                            ClassicEditor
                                .create(document.querySelector('#health_name'), {
                                    toolbar: {
                                        items: [
                                            'undo', 'redo',
                                            '|', 'heading',
                                            '|', 'fontFamily', 'fontSize', 'fontColor', 'fontBackgroundColor',
                                            '|', 'bold', 'italic', 'strikethrough', 'subscript', 'superscript', 'code',
                                            '|', 'alignment',
                                            '|', 'bulletedList', 'numberedList', 'todoList',
                                            '|', 'horizontalLine',
                                            '|', 'removeFormat',
                                            '|', 'undo', 'redo'
                                        ]
                                    },
                                    shouldNotGroupWhenFull: true
                                })
                                .catch(error => {
                                    console.error(error);
                                });
                        </script>
                    </div>
                </div>
                <br>
                <div class="form-group row">
                    <div class="col-sm-2 control-label">รายละเอียด</div>
                    <div class="col-sm-10">
                        <textarea name="health_detail" id="health_detail"><?= $rsedit->health_detail; ?></textarea>
                        <script>
                            ClassicEditor
                                .create(document.querySelector('#health_detail'), {
                                    toolbar: {
                                        items: [
                                            'undo', 'redo',
                                            '|', 'heading',
                                            '|', 'fontFamily', 'fontSize', 'fontColor', 'fontBackgroundColor',
                                            '|', 'bold', 'italic', 'strikethrough', 'subscript', 'superscript', 'code',
                                            '|', 'alignment',
                                            '|', 'bulletedList', 'numberedList', 'todoList',
                                            '|', 'horizontalLine',
                                            '|', 'removeFormat',
                                            '|', 'undo', 'redo'
                                        ]
                                    },
                                    shouldNotGroupWhenFull: true
                                })
                                .catch(error => {
                                    console.error(error);
                                });
                        </script>
                    </div>
                </div>
                <br>
                <!-- <div class="form-group row">
                    <div class="col-sm-2 control-label">แหล่งที่มา</div>
                    <div class="col-sm-10">
                        <input type="text" name="health_refer" id="health_refer" class="form-control" value="<?= $rsedit->health_refer; ?>">
                    </div>
                </div>
                <br> -->
                <div class="form-group row">
                    <div class="col-sm-2 control-label">รูปภาพหน้าปก</div>
                    <div class="col-sm-6">
                        ภาพเก่า <br>
                        <?php if (!empty($rsedit->health_img)) : ?>
                            <img src="<?= base_url('docs/img/' . $rsedit->health_img); ?>" width="250px" height="210">
                        <?php else : ?>
                            <img src="<?= base_url('docs/logo.png'); ?>" width="250px" height="210">
                        <?php endif; ?>
                        <br>
                        เลือกใหม่
                        <br>
                        <input type="file" name="health_img" class="form-control" accept="image/*">
                    </div>
                </div>
                <br>
                <div class="form-group row">
                    <div class="col-sm-2 control-label">รูปภาพเพิ่มเติม</div>
                    <div class="col-sm-6">
                        รูปภาพเก่า: <br>
                        <?php foreach ($qimg as $img) { ?>
                            <img src="<?= base_url('docs/img/' . $img->health_img_img); ?>" width="140px" height="100px">&nbsp;
                        <?php } ?>
                        <br>
                        เลือกใหม่: <br>
                        <input type="file" name="health_img_img[]" class="form-control" accept="image/*" multiple>
                        <span class="black-add">สามารถอัพโหลดได้หลายไฟล์</span>
                        <br>
                        <span class="red-add">(เฉพาะไฟล์ .JPG/.JPEG/.jfif/.PNG)</span>
                    </div>
                </div>
                <br>
                <div class="form-group row">
                    <div class="col-sm-2 control-label">ไฟล์เอกสารเพิ่มเติม</div>
                    <div class="col-sm-6">
                        <?php $df = $rsedit->health_file; ?>
                        <?php if ($df != '' && empty($_POST['delete_doc_file'])) { ?>
                            <a class="btn btn-info btn-sm" href="<?php echo base_url('docs/file/' . $rsedit->health_file); ?>" target="_blank">ดูไฟล์เดิม!</a>
                            <a class="btn btn-danger btn-sm" href="#" role="button" onclick="confirmDelete(<?= $rsedit->health_id; ?>);"><svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-x-circle-fill" viewBox="0 0 16 16">
                                    <path d="M16 8A8 8 0 1 1 0 8a8 8 0 0 1 16 0zM5.354 4.646a.5.5 0 1 0-.708.708L7.293 8l-2.647 2.646a.5.5 0 0 0 .708.708L8 8.707l2.646 2.647a.5.5 0 0 0 .708-.708L8.707 8l2.647-2.646a.5.5 0 0 0-.708-.708L8 7.293 5.354 4.646z" />
                                </svg> ลบไฟล์</a>
                        <?php } ?>
                        <script>
                            function confirmDelete(health_id) {
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
                                        window.location.href = "<?= site_url('health_backend/delPDF/'); ?>" + health_id;
                                    }
                                });
                            }
                        </script>
                        <input type="file" name="health_file" class="form-control mt-1" accept="application/pdf">
                        <span class="black-add">สามารถอัพโหลดได้หลายไฟล์</span>
                        <br>
                        <span class="red-add">(เฉพาะไฟล์ PDF)</span>
                    </div>
                </div>
                <br>
                <div class="form-group row">
                    <div class="col-sm-2 control-label"></div>
                    <div class="col-sm-6">
                        <button type="submit" class="btn btn-success">บันทึกข้อมูล</button>
                        <a class="btn btn-danger" href="<?= site_url('health_backend'); ?>" role="button">ยกเลิก</a>
                    </div>
                </div>
            </form>
        </div>
    </div>
</div>