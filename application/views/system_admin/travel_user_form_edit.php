<style>
    /* เพิ่มสไตล์ของแผนที่ */
    #map {
        height: 400px;
        width: 100%;
    }
</style>
<div class="container">
    <div class="row">
        <div class="col-md-2"></div>
        <div class="col-md-7">
            <h4>แก้ไขข้อมูลสถานที่สำคัญ-ท่องเที่ยว</h4>
            <form action=" <?php echo site_url('travel_backend/edit_User_Travel/' . $rsedit->user_travel_id); ?> " method="post" class="form-horizontal" enctype="multipart/form-data" onsubmit="return validateForm()">
                <br>
                <div class="form-group row">
                    <div class="col-sm-2 control-label">ชื่อสถานที่</div>
                    <div class="col-sm-6">
                        <input type="text" name="user_travel_name" required class="form-control" value="<?= $rsedit->user_travel_name; ?>">
                    </div>
                </div>
                <br>
                <div class="form-group row">
                    <div class="col-sm-2 control-label">แหล่งที่มา</div>
                    <div class="col-sm-10">
                        <input type="text" name="user_travel_refer" required class="form-control" value="<?= $rsedit->user_travel_refer; ?>">
                    </div>
                </div>
                <br>
                <div class="form-group row">
                    <div class="col-sm-2 control-label">รายละเอียด</div>
                    <div class="col-sm-8">
                        <textarea name="user_travel_detail" id="user_travel_detail"><?= $rsedit->user_travel_detail; ?></textarea>
                        <script>
                            ClassicEditor
                                .create(document.querySelector('#user_travel_detail'), {
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
                    <div class="col-sm-2 control-label">ที่ตั้ง</div>
                    <div class="col-sm-10">
                        <input type="text" name="user_travel_location" required class="form-control" value="<?= $rsedit->user_travel_location; ?>">
                    </div>
                </div>
                <br>
                <div class="form-group row">
                    <div class="col-sm-2 control-label">เวลาเปิด-ปิด</div>
                    <div class="col-sm-3">
                        <input type="time" name="user_travel_timeopen" required class="form-control" value="<?= $rsedit->user_travel_timeopen; ?>">
                    </div>
                    ถึง
                    <div class="col-sm-3">
                        <input type="time" name="user_travel_timeclose" required class="form-control" value="<?= $rsedit->user_travel_timeclose; ?>">
                    </div>
                </div>
                <br>
                <div class="form-group row">
                    <div class="col-sm-2 control-label">วันที่ทำการ</div>
                    <div class="col-sm-8">
                        <input type="text" name="user_travel_date" required class="form-control" value="<?= $rsedit->user_travel_date; ?>">
                        <p>ตัวอย่าง หยุดทุกวันพุธ หรือ เปิดวันจันทร์-วันศุกร์</p>
                    </div>
                </div>
                <br>
                <div class="form-group row">
                    <div class="col-sm-2 control-label">เบอร์ติดต่อ</div>
                    <div class="col-sm-6">
                        <input type="number" name="user_travel_phone" required class="form-control" max="9999999999" value="<?= $rsedit->user_travel_phone; ?>">
                    </div>
                </div>
                <br>
                <div class="form-group row">
                    <div class="col-sm-2 control-label">Link youtube</div>
                    <div class="col-sm-10">
                        <input type="text" name="user_travel_youtube" required class="form-control" value="<?= $rsedit->user_travel_youtube; ?>">
                    </div>
                </div>
                <br>
                <div id="map"></div>
                <br>
                <div class="form-group row">
                    <div class="col-sm-2 control-label">ละติจูด</div>
                    <div class="col-sm-6">
                        <input type="text" name="user_travel_lat" id="user_travel_lat" class="form-control" required value="<?php echo $rsedit->user_travel_lat; ?>">
                    </div>
                </div>
                <br>
                <div class="form-group row">
                    <div class="col-sm-2 control-label">ลองจิจูด</div>
                    <div class="col-sm-6">
                        <input type="text" name="user_travel_long" id="user_travel_long" class="form-control" required value="<?php echo $rsedit->user_travel_long; ?>">
                    </div>
                </div>
                <br>
                <div class="form-group row">
                    <div class="col-sm-2 control-label">รูปภาพหน้าปก</div>
                    <div class="col-sm-6">
                        ภาพเก่า <br>
                        <img src="<?= base_url('docs/img/' . $rsedit->user_travel_img); ?>" width="250px" height="210">
                        <br>
                        เลือกใหม่
                        <br>
                        <input type="file" name="user_travel_img" class="form-control" accept="image/*">
                    </div>
                </div>
                <br>
                <div class="form-group row">
                    <div class="col-sm-2 control-label">รูปภาพเพิ่มเติม(สามารถเพิ่มได้หลายรูป)</div>
                    <div class="col-sm-6">
                        รูปภาพเก่า: <br>
                        <?php foreach ($qimg as $img) { ?>
                            <img src="<?= base_url('docs/img/' . $img->user_travel_img_img); ?>" width="140px" height="100px">&nbsp;
                        <?php } ?>
                        <br>
                        เลือกใหม่: <br>
                        <input type="file" name="user_travel_img_img[]" class="form-control" accept="image/*" multiple>
                    </div>
                </div>
                <br>
                <div class="form-group row">
                    <div class="col-sm-2 control-label"></div>
                    <div class="col-sm-6">
                        <button type="submit" class="btn btn-success">บันทึกข้อมูล</button>
                        <a class="btn btn-danger" href="<?= site_url('travel_backend'); ?>" role="button">ยกเลิก</a>
                    </div>
                </div>
            </form>
        </div>
    </div>
</div>

<script src="https://maps.googleapis.com/maps/api/js?key=AIzaSyCz5M0j4ysiYbUiYOAoidfE0hbEXJIq7MI&callback=initMap" async defer></script>

<script>
    let map;
    let marker;

    function initMap() {
        map = new google.maps.Map(document.getElementById('map'), {
            center: {
                lat: parseFloat(document.getElementById('user_travel_lat').value),
                lng: parseFloat(document.getElementById('user_travel_long').value)
            },
            zoom: 15
        });

        // สร้างมาร์คเริ่มต้นตามค่าละติจูดและลองจิจูดในฟอร์ม
        marker = new google.maps.Marker({
            position: {
                lat: parseFloat(document.getElementById('user_travel_lat').value),
                lng: parseFloat(document.getElementById('user_travel_long').value)
            },
            map: map,
            draggable: true
        });

        // อัพเดทค่าละติจูดและลองจิจูดในฟอร์มเมื่อมาร์คถูกลาก
        marker.addListener('dragend', function(event) {
            document.getElementById('user_travel_lat').value = event.latLng.lat();
            document.getElementById('user_travel_long').value = event.latLng.lng();
        });

        // เมื่อคลิกที่แผนที่เพื่อเปลี่ยนตำแหน่ง
        map.addListener('click', function(event) {
            placeMarker(event.latLng);
        });
    }

    function placeMarker(location) {
        if (marker) {
            marker.setPosition(location);
        } else {
            marker = new google.maps.Marker({
                position: location,
                map: map,
                draggable: true
            });
        }

        // อัพเดทค่าละติจูดและลองจิจูดในฟอร์ม
        document.getElementById('user_travel_lat').value = location.lat();
        document.getElementById('user_travel_long').value = location.lng();
    }
</script>