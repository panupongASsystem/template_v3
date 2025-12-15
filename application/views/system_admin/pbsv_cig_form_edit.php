<div class="container">
    <div class="row">
        <div class="col-md-2"></div>
        <div class="col-md-7">
            <h4>แก้ไขข้อมูลศูนย์ข้อมูลข่าวสารทางราชการ</h4>
            <form action=" <?php echo site_url('pbsv_cig_backend/edit/' . $rsedit->pbsv_cig_id); ?> " method="post" class="form-horizontal" enctype="multipart/form-data" onsubmit="return validateForm()">
                <br>
                <div class="form-group row">
                    <div class="col-sm-3 control-label">เรื่อง</div>
                    <div class="col-sm-9">
                        <input type="text" name="pbsv_cig_name" id="pbsv_cig_name" class="form-control" value="<?= $rsedit->pbsv_cig_name; ?>">
                    </div>
                </div>
                <br>
                <div class="form-group row">
                    <div class="col-sm-3 control-label">รายละเอียด</div>
                    <div class="col-sm-9">
                        <textarea name="pbsv_cig_detail" id="pbsv_cig_detail"><?= $rsedit->pbsv_cig_detail; ?></textarea>
                        <script>
                            ClassicEditor
                                .create(document.querySelector('#pbsv_cig_detail'), {
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
                    <div class="col-sm-3 control-label">วันที่อัพโหลด</div>
                    <div class="col-sm-5">
                        <input type="datetime-local" name="pbsv_cig_date" id="pbsv_cig_date" class="form-control" value="<?= $rsedit->pbsv_cig_date; ?>" required>
                    </div>
                </div>
                <br>
                <div class="form-group row">
                    <div class="col-sm-3 control-label">ลิงค์เพิ่มเติม</div>
                    <div class="col-sm-9">
                        <input type="text" name="pbsv_cig_link" id="pbsv_cig_link" class="form-control" value="<?= $rsedit->pbsv_cig_link; ?>">
                    </div>
                </div>
                <br>
                <div class="form-group row">
                    <div class="col-sm-3 control-label">รูปภาพหน้าปก</div>
                    <div class="col-sm-6">
                        ภาพเก่า <br>
                        <?php if (!empty($rsedit->pbsv_cig_img)) : ?>
                            <img src="<?= base_url('docs/img/' . $rsedit->pbsv_cig_img); ?>" width="250px" height="210">
                        <?php else : ?>
                            <img src="<?= base_url('docs/logo.png'); ?>" width="250px" height="210">
                        <?php endif; ?>
                        <br>
                        เลือกใหม่
                        <br>
                        <input type="file" name="pbsv_cig_img" class="form-control" accept="image/*">
                    </div>
                </div>
                <br>
                <div class="form-group row">
                    <div class="col-sm-3 control-label">รูปภาพเพิ่มเติม</div>
                    <div class="col-sm-6">
                        รูปภาพเก่า: <br>
                        <?php if (!empty($rsImg)) { ?>
                            <?php foreach ($rsImg as $img) { ?>
                                <div class="row">
                                    <div class="col-sm-5">
                                        <img src="<?= base_url('docs/img/' . $img->pbsv_cig_img_img); ?>" width="140px" height="100px">
                                        <a class="btn btn-danger btn-sm mb-2" href="#" role="button" onclick="confirmDeleteImg(<?= $img->pbsv_cig_img_id; ?>, '<?= $img->pbsv_cig_img_img; ?>');">
                                            <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-x-circle-fill" viewBox="0 0 16 16">
                                                <path d="M16 8A8 8 0 1 1 0 8a8 8 0 0 1 16 0zM5.354 4.646a.5.5 0 1 0-.708.708L7.293 8l-2.647 2.646a.5.5 0 0 0 .708.708L8 8.707l2.646 2.647a.5.5 0 0 0 .708-.708L8.707 8l2.647-2.646a.5.5 0 0 0-.708-.708L8 7.293 5.354 4.646z" />
                                            </svg> ลบไฟล์
                                        </a>
                                    </div>
                                </div>
                                <script>
                                    function confirmDeleteImg(file_id, file_name) {
                                        Swal.fire({
                                            title: 'คุณแน่ใจหรือไม่?',
                                            text: 'คุณต้องการลบไฟล์ ' + file_name + ' ใช่หรือไม่?',
                                            icon: 'warning',
                                            showCancelButton: true,
                                            confirmButtonColor: '#3085d6',
                                            cancelButtonColor: '#d33',
                                            confirmButtonText: 'ใช่, ต้องการลบ!',
                                            cancelButtonText: 'ยกเลิก'
                                        }).then((result) => {
                                            if (result.isConfirmed) {
                                                // หลังจากคลิกยืนยันให้เรียก Controller ที่ใช้ในการลบไฟล์ PDF
                                                window.location.href = "<?= site_url('pbsv_cig_backend/del_img/'); ?>" + file_id;
                                            }
                                        });
                                    }
                                </script>
                            <?php } ?>
                        <?php } ?>
                        เลือกใหม่: <br>
                        <input type="file" name="pbsv_cig_img_img[]" class="form-control" accept="image/*" multiple>
                        <span class="black-add">สามารถอัพโหลดได้หลายไฟล์</span>
                        <br>
                        <span class="red-add">(เฉพาะไฟล์ .JPG/.JPEG/.jfif/.PNG)</span>
                    </div>
                </div>
                <br>
                <div class="form-group row">
                    <div class="col-sm-3 control-label">ไฟล์เอกสารเพิ่มเติม</div>
                    <div class="col-sm-6">
                        <?php if (!empty($rsPdf)) { ?>
                            <?php foreach ($rsPdf as $pdf) { ?>
                                <a class="btn btn-primary btn-sm mb-2" href="<?= base_url('docs/file/' . $pdf->pbsv_cig_pdf_pdf); ?>" target="_blank">ดูไฟล์ <?= $pdf->pbsv_cig_pdf_pdf; ?></a>
                                <a class="btn btn-danger btn-sm mb-2" href="#" role="button" onclick="confirmDeletePdf(<?= $pdf->pbsv_cig_pdf_id; ?>, '<?= $pdf->pbsv_cig_pdf_pdf; ?>');">
                                    <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-x-circle-fill" viewBox="0 0 16 16">
                                        <path d="M16 8A8 8 0 1 1 0 8a8 8 0 0 1 16 0zM5.354 4.646a.5.5 0 1 0-.708.708L7.293 8l-2.647 2.646a.5.5 0 0 0 .708.708L8 8.707l2.646 2.647a.5.5 0 0 0 .708-.708L8.707 8l2.647-2.646a.5.5 0 0 0-.708-.708L8 7.293 5.354 4.646z" />
                                    </svg> ลบไฟล์
                                </a>
                                <br>
                            <?php } ?>
                        <?php } ?>
                        <script>
                            function confirmDeletePdf(pdf_id, pdf_name) {
                                Swal.fire({
                                    title: 'คุณแน่ใจหรือไม่?',
                                    text: 'คุณต้องการลบไฟล์ ' + pdf_name + ' ใช่หรือไม่?',
                                    icon: 'warning',
                                    showCancelButton: true,
                                    confirmButtonColor: '#3085d6',
                                    cancelButtonColor: '#d33',
                                    confirmButtonText: 'ใช่, ต้องการลบ!',
                                    cancelButtonText: 'ยกเลิก'
                                }).then((result) => {
                                    if (result.isConfirmed) {
                                        // หลังจากคลิกยืนยันให้เรียก Controller ที่ใช้ในการลบไฟล์ PDF
                                        window.location.href = "<?= site_url('pbsv_cig_backend/del_pdf/'); ?>" + pdf_id;
                                    }
                                });
                            }
                        </script>
                        <input type="file" name="pbsv_cig_pdf_pdf[]" class="form-control mt-1" accept="application/pdf" multiple>
                        <span class="black-add">สามารถอัพโหลดได้หลายไฟล์</span>
                        <br>
                        <span class="red-add">(เฉพาะไฟล์ PDF)</span>
                    </div>
                </div>
                <br>
                <div class="form-group row">
                    <div class="col-sm-3 control-label">ไฟล์เอกสารเพิ่มเติม</div>
                    <div class="col-sm-6">
                        <?php if (!empty($rsDoc)) { ?>
                            <?php foreach ($rsDoc as $doc) { ?>
                                <a class="btn btn-info btn-sm mb-2" href="<?= base_url('docs/file/' . $doc->pbsv_cig_file_doc); ?>" target="_blank">ดูไฟล์ <?= $doc->pbsv_cig_file_doc; ?></a>
                                <a class="btn btn-danger btn-sm mb-2" href="#" role="button" onclick="confirmDeleteDoc(<?= $doc->pbsv_cig_file_id; ?>, '<?= $doc->pbsv_cig_file_doc; ?>');">
                                    <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-x-circle-fill" viewBox="0 0 16 16">
                                        <path d="M16 8A8 8 0 1 1 0 8a8 8 0 0 1 16 0zM5.354 4.646a.5.5 0 1 0-.708.708L7.293 8l-2.647 2.646a.5.5 0 0 0 .708.708L8 8.707l2.646 2.647a.5.5 0 0 0 .708-.708L8.707 8l2.647-2.646a.5.5 0 0 0-.708-.708L8 7.293 5.354 4.646z" />
                                    </svg> ลบไฟล์
                                </a>
                                <br>
                            <?php } ?>
                        <?php } ?>
                        <script>
                            function confirmDeleteDoc(doc_id, doc_name) {
                                Swal.fire({
                                    title: 'คุณแน่ใจหรือไม่?',
                                    text: 'คุณต้องการลบไฟล์ ' + doc_name + ' ใช่หรือไม่?',
                                    icon: 'warning',
                                    showCancelButton: true,
                                    confirmButtonColor: '#3085d6',
                                    cancelButtonColor: '#d33',
                                    confirmButtonText: 'ใช่, ต้องการลบ!',
                                    cancelButtonText: 'ยกเลิก'
                                }).then((result) => {
                                    if (result.isConfirmed) {
                                        // หลังจากคลิกยืนยันให้เรียก Controller ที่ใช้ในการลบไฟล์ PDF
                                        window.location.href = "<?= site_url('pbsv_cig_backend/del_doc/'); ?>" + doc_id;
                                    }
                                });
                            }
                        </script>
                        <input type="file" name="pbsv_cig_file_doc[]" class="form-control mt-1" accept="application/msword,application/vnd.openxmlformats-officedocument.wordprocessingml.document,application/vnd.ms-powerpoint,application/vnd.openxmlformats-officedocument.presentationml.presentation,application/vnd.ms-excel,application/vnd.openxmlformats-officedocument.spreadsheetml.sheet" multiple>
                        <span class="black-add">สามารถอัพโหลดได้หลายไฟล์</span>
                        <br>
                        <span class="red-add">(เฉพาะไฟล์ .doc .docx .ppt .pptx .xls .xlsx)</span>
                    </div>
                </div>
                <br>
                <div class="form-group row">
                    <div class="col-sm-3 control-label"></div>
                    <div class="col-sm-6">
                        <button type="submit" class="btn btn-success">บันทึกข้อมูล</button>
                        <a class="btn btn-danger" href="<?= site_url('pbsv_cig_backend'); ?>" role="button">ยกเลิก</a>
                    </div>
                </div>
            </form>
        </div>
    </div>
</div>