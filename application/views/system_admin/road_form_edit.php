<div class="container">
    <div class="row">
        <div class="col-md-2"></div>
        <div class="col-md-7">
            <h4>แก้ไขข้อมูลเส้นทางแต่ละอำเภอ</h4>
            <form action=" <?php echo site_url('road_backend/edit/' . $rsedit->road_id); ?> " method="post" class="form-horizontal">
                <br>
                <div class="form-group row">
                    <div class="col-sm-2 control-label">อำเภอ</div>
                    <div class="col-sm-5">
                        <input type="text" name="road_district" required class="form-control" value="<?= $rsedit->road_district; ?>" disabled>
                    </div>
                </div>
                <br>
                <div class="form-group row">
                    <div class="col-sm-2 control-label">รหัสสายทาง</div>
                    <div class="col-sm-5">
                        <input type="text" name="road_code" required class="form-control" value="<?= $rsedit->road_code; ?>">
                    </div>
                </div>
                <br>
                <div class="form-group row">
                    <div class="col-sm-2 control-label">ชื่อสายทาง</div>
                    <div class="col-sm-10">
                        <textarea name="road_name" id="road_name"><?= $rsedit->road_name; ?></textarea>
                        <script>
                            ClassicEditor
                                .create(document.querySelector('#road_name'), {
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
                <div class="row">
                    <div class="col-md-6">
                        <div class="form-group">
                            <label for="road_distance" class="control-label">ระยะทาง(ก.ม.)</label>
                            <div class="col-sm-8">
                                <input type="number" name="road_distance" step="0.1" required class="form-control" value="<?= $rsedit->road_distance; ?>">
                            </div>
                        </div>
                    </div>

                    <div class="col-md-6">
                        <div class="form-group">
                            <label for="road_light" class="control-label">เสาไฟส่องสว่าง(ต้น)</label>
                            <div class="col-sm-8">
                                <input type="number" name="road_light" required class="form-control" value="<?= $rsedit->road_light; ?>">
                            </div>
                        </div>
                    </div>
                </div>

                <br>
                <div class="form-group row">
                    <div class="col-sm-2 control-label">ผู้สำรวจเส้นทาง</div>
                    <div class="col-sm-10">
                        <textarea name="road_explore" id="road_explore"><?= $rsedit->road_explore; ?></textarea>
                        <script>
                            ClassicEditor
                                .create(document.querySelector('#road_explore'), {
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
                    <div class="col-sm-2 control-label">เขตผู้รับผิดชอบ</div>
                    <div class="col-sm-6">
                        <input type="text" name="road_responsibility" required class="form-control" value="<?= $rsedit->road_responsibility; ?>">
                        <span>กรุณากรอกชื่อและนามสกุล</span>
                    </div>
                </div>
                <br>
                <div class="form-group row">
                    <div class="col-sm-2 control-label">เบอร์โทร สจ.พื้นที่</div>
                    <div class="col-sm-6">
                        <input type="number" name="road_phone" required class="form-control" value="<?= $rsedit->road_phone; ?>">
                    </div>
                </div>
                <br>
                <div class="row">
                    <div class="col-md-6">
                        <div class="form-group">
                            <label for="road_gravel" class="control-label">ถนนลูกรังยาว(ก.ม)</label>
                            <div class="col-sm-8">
                                <input type="number" step="0.1" name="road_gravel"required class="form-control" value="<?= $rsedit->road_gravel; ?>">
                            </div>
                        </div>
                    </div>

                    <div class="col-md-6">
                        <div class="form-group">
                            <label for="road_gravel_width" class="control-label">ผิวลูกรังกว้าง(ม.)</label>
                            <div class="col-sm-8">
                                <input type="number" name="road_gravel_width" step="0.1" required class="form-control" value="<?= $rsedit->road_gravel_width; ?>">
                            </div>
                        </div>
                    </div>
                </div>
                <br>
                <div class="form-group row">
                    <div class="col-sm-2 control-label">ถนนลูกรังชำรุด</div>
                    <div class="col-sm-4">
                        <select class="form-control" name="road_gravel_repair" required>
                            <option value="<?php echo $rsedit->road_gravel_repair; ?>">-<?php echo $rsedit->road_gravel_repair; ?>-</option>
                            <option value="" disabled>เลือกข้อมูล</option>
                            <option value="มีจุดชำรุด">-มีจุดชำรุด-</option>
                            <option value="ไม่มีจุดชำรุด">-ไม่มีจุดชำรุด-</option>
                        </select>
                    </div>
                </div>
                <br>
                <div class="row">
                    <div class="col-md-6">
                        <div class="form-group">
                            <label for="road_concrete" class="control-label">ถนนคอนกรีดยาว(ก.ม)</label>
                            <div class="col-sm-8">
                                <input type="number" name="road_concrete" step="0.1" required class="form-control" value="<?= $rsedit->road_concrete; ?>">
                            </div>
                        </div>
                    </div>

                    <div class="col-md-6">
                        <div class="form-group">
                            <label for="road_concrete_width" class="control-label">ผิวคอนกรีดกว้าง(ม.)</label>
                            <div class="col-sm-8">
                                <input type="number" name="road_concrete_width" step="0.1" required class="form-control" value="<?= $rsedit->road_concrete_width; ?>">
                            </div>
                        </div>
                    </div>
                </div>
                <br>
                <div class="form-group row">
                    <div class="col-sm-2 control-label">ถนนคอนกรีดชำรุด</div>
                    <div class="col-sm-4">
                        <select class="form-control" name="road_concrete_repair" required>
                            <option value="<?php echo $rsedit->road_concrete_repair; ?>">-<?php echo $rsedit->road_concrete_repair; ?>-</option>
                            <option value="" disabled>เลือกข้อมูล</option>
                            <option value="มีจุดชำรุด">-มีจุดชำรุด-</option>
                            <option value="ไม่มีจุดชำรุด">-ไม่มีจุดชำรุด-</option>
                        </select>
                    </div>
                </div>
                <br>
                <div class="row">
                    <div class="col-md-6">
                        <div class="form-group">
                            <label for="road_paved" class="control-label">ถนนลาดยางยาว(ก.ม)</label>
                            <div class="col-sm-8">
                                <input type="number" name="road_paved" step="0.1" required class="form-control" value="<?= $rsedit->road_paved; ?>">
                            </div>
                        </div>
                    </div>

                    <div class="col-md-6">
                        <div class="form-group">
                            <label for="road_paved_width" class="control-label">ผิวลาดยางกว้าง(ม.)</label>
                            <div class="col-sm-8">
                                <input type="number" name="road_paved_width" step="0.1" required class="form-control" value="<?= $rsedit->road_paved_width; ?>">
                            </div>
                        </div>
                    </div>
                </div>
                <br>
                <div class="form-group row">
                    <div class="col-sm-2 control-label">ถนนลาดยางชำรุด</div>
                    <div class="col-sm-4">
                        <select class="form-control" name="road_paved_repair" required>
                            <option value="<?php echo $rsedit->road_paved_repair; ?>">-<?php echo $rsedit->road_paved_repair; ?>-</option>
                            <option value="" disabled>เลือกข้อมูล</option>
                            <option value="มีจุดชำรุด">-มีจุดชำรุด-</option>
                            <option value="ไม่มีจุดชำรุด">-ไม่มีจุดชำรุด-</option>
                        </select>
                    </div>
                </div>
                <br>
                <div class="form-group row">
                    <div class="col-sm-2 control-label"></div>
                    <div class="col-sm-10">
                        <button type="submit" class="btn btn-success">บันทึกข้อมูล</button>
                        <a class="btn btn-danger" href="<?php echo site_url('road__backend'); ?>">ยกเลิก</a>
                    </div>
                </div>
            </form>
        </div>
    </div>
</div>