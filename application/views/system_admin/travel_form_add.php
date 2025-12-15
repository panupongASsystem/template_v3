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
            <h4>เพิ่มข้อมูลสถานที่สำคัญ-ท่องเที่ยว</h4>
            <form action=" <?php echo site_url('travel_backend/add_Travel'); ?> " method="post" class="form-horizontal" enctype="multipart/form-data" onsubmit="return validateForm()">
                <br>
                <!-- <div class="form-group row">
                    <div class="col-sm-2 control-label">ชื่อตำแหน่งงาน</div>
                    <div class="col-sm-6">
                        <select class="form-control" name="transport_ref_type_id" required>
                            <option value="">-เลือกข้อมูล-</option>
                            <?php foreach ($rstp as $rs) { ?>
                            <option value="<?php echo $rs->transport_type_id; ?>">-<?php echo $rs->transport_type_name; ?>-</option>
                            <?php } ?>
                        </select>
                    </div>
                </div>
                <br> -->
                <div class="form-group row">
                    <div class="col-sm-3 control-label">ชื่อสถานที่</div>
                    <div class="col-sm-6">
                        <input type="text" name="travel_name" required class="form-control">
                    </div>
                </div>
                <br>
                <div class="form-group row">
                    <div class="col-sm-3 control-label">รายละเอียด</div>
                    <div class="col-sm-9">
                        <textarea name="travel_detail" id="travel_detail"></textarea>
                        <script>
                            ClassicEditor
                                .create(document.querySelector('#travel_detail'), {
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
                    <div class="col-sm-3 control-label">ที่ตั้ง</div>
                    <div class="col-sm-9">
                        <input type="text" name="travel_location" required class="form-control">
                    </div>
                </div>
                <br>
                <div class="form-group row">
                    <div class="col-sm-3 control-label">เวลาเปิด-ปิด</div>
                    <div class="col-sm-4">
                        <input type="time" name="travel_timeopen" required class="form-control">
                    </div>
                    ถึง
                    <div class="col-sm-4">
                        <input type="time" name="travel_timeclose" required class="form-control">
                    </div>
                </div>
                <br>
                <div class="form-group row">
                    <div class="col-sm-3 control-label">วันที่ทำการ</div>
                    <div class="col-sm-4">
                        <select class="form-control" name="travel_day" required>
                            <option value="">เลือกข้อมูล</option>
                            <option value="ทุกวัน">ทุกวัน</option>
                            <option value="จันทร์-วันศุกร์">วันจันทร์-วันศุกร์</option>
                            <option value="วันเสาร์-วันอาทิตย์">วันเสาร์-วันอาทิตย์</option>
                            <option value="หยุดวันจันทร์">หยุดทุกวันจันทร์</option>
                            <option value="หยุดทุกวันอังคาร">หยุดทุกวันอังคาร</option>
                            <option value="หยุดทุกวันพุธ">หยุดทุกวันพุธ</option>
                            <option value="หยุดทุกวันพฤหัสบดี">หยุดทุกวันพฤหัสบดี</option>
                            <option value="หยุดทุกวันวันศุกร์">หยุดทุกวันวันศุกร์</option>
                            <option value="หยุดทุกวันเสาร์">หยุดทุกวันเสาร์</option>
                            <option value="หยุดทุกวันอาทิตย์">หยุดทุกวันอาทิตย์</option>
                        </select>
                    </div>
                </div>
                <br>
                <div class="form-group row">
                    <div class="col-sm-3 control-label">เบอร์ติดต่อ</div>
                    <div class="col-sm-6">
                        <input type="text" name="travel_phone" required class="form-control" pattern="\d{9,10}" title="กรุณากรอกเบอร์มือถือเป็นตัวเลข 9 หรือ 10 ตัว">
                    </div>
                </div>
                <br>
                <div class="form-group row">
                    <div class="col-sm-3 control-label">Link youtube</div>
                    <div class="col-sm-9">
                        <input type="text" name="travel_youtube" class="form-control">
                    </div>
                </div>
                <br>
                <div class="form-group row">
                    <div class="col-sm-3 control-label">แหล่งที่มา</div>
                    <div class="col-sm-9">
                        <input type="text" name="travel_refer" class="form-control">
                    </div>
                </div>
                <br>
                <div class="form-group row">
                    <div class="col-sm-3 control-label">วันที่อัพโหลด</div>
                    <div class="col-sm-5">
                        <input type="datetime-local" name="travel_date" id="travel_date" class="form-control" required>
                    </div>
                </div>
                <br>
                <div class="form-group row">
                    <div class="col-sm-3 control-label">link Google map</div>
                    <div class="col-sm-9">
                        <input type="text" name="travel_map" class="form-control">
                    </div>
                </div>
                <!-- <div id="map"></div>
                <br>
                <div class="form-group row">
                    <div class="col-sm-3 control-label">ละติจูด</div>
                    <div class="col-sm-6">
                        <input type="text" name="travel_lat" id="travel_lat" class="form-control" required>
                    </div>
                </div>
                <br>
                <div class="form-group row">
                    <div class="col-sm-3 control-label">ลองจิจูด</div>
                    <div class="col-sm-6">
                        <input type="text" name="travel_long" id="travel_long" class="form-control" required>
                    </div>
                </div> -->
                <br>
                <div class="form-group row">
                    <div class="col-sm-3 control-label">รูปภาพหน้าปก</div>
                    <div class="col-sm-6">
                        <input type="file" name="travel_img" class="form-control" accept="image/*" required>
                    </div>
                </div>
                <br>
                <div class="form-group row">
                    <div class="col-sm-3 control-label">รูปภาพเพิ่มเติม</div>
                    <div class="col-sm-6">
                        <input type="file" name="travel_imgs[]" class="form-control" accept="image/*" multiple required>
                        <span class="black-add">สามารถอัพโหลดได้หลายไฟล์</span>
                        <br>
                        <span class="red-add">(เฉพาะไฟล์ .JPG/.JPEG/.jfif/.PNG)</span>
                    </div>
                </div>
                <br>
                <div class="form-group row">
                    <div class="col-sm-3 control-label"></div>
                    <div class="col-sm-6">
                        <button type="submit" class="btn btn-success">บันทึกข้อมูล</button>
                        <a class="btn btn-danger" href="<?= site_url('travel_backend'); ?>" role="button">ยกเลิก</a>
                    </div>
                </div>
                </forFm>
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
                lat: 16.5463601,
                lng: 104.555
            },
            zoom: 10
        });

        // เมื่อคลิกที่แผนที่เพื่อเพิ่มตำแหน่ง
        map.addListener('click', function(event) {
            placeMarker(event.latLng);
        });

        // กำหนดตำแหน่งเริ่มต้น (ถ้ามี)
        const initialLat = parseFloat(document.getElementById('travel_lat').value);
        const initialLng = parseFloat(document.getElementById('travel_long').value);

        if (!isNaN(initialLat) && !isNaN(initialLng)) {
            const initialLatLng = {
                lat: initialLat,
                lng: initialLng
            };
            placeMarker(initialLatLng);
        }
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
        document.getElementById('travel_lat').value = location.lat();
        document.getElementById('travel_long').value = location.lng();
    }
</script>