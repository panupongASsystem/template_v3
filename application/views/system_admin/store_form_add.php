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
            <h4>เพิ่มข้อมูลร้านค้าและอื่นๆ</h4>
            <form action=" <?php echo site_url('store_backend/add_store'); ?> " method="post" class="form-horizontal" enctype="multipart/form-data" onsubmit="return validateForm()">
                <br>
                <div class="form-group row">
                    <div class="col-sm-3 control-label">ประเภทของร้าน</div>
                    <div class="col-sm-4">
                        <select class="form-control" name="store_type" required>
                            <option value="">เลือกข้อมูล</option>
                            <option value="ซ่อมแซม">ซ่อมแซม/บ้าน</option>
                            <option value="แอร์">แอร์</option>
                            <option value="รถ/ยานพาหนะ">รถ/ยานพาหนะ</option>
                            <option value="ไฟฟ้า">ไฟฟ้า</option>
                            <option value="เทคโนโลยี">เทคโนโลยี</option>
                            <option value="ประปา">ประปา</option>
                            <option value="สัตว์เลี้ยง">สัตว์เลี้ยง</option>
                            <option value="เสริมสวย">เสริมสวย</option>
                            <option value="คนสวน/แม่บ้าน">คนสวน/แม่บ้าน</option>
                            <option value="คาเฟ่">คาเฟ่</option>
                            <option value="โรงแรม">โรงแรม</option>
                            <option value="อื่นๆ">อื่นๆ</option>
                        </select>
                    </div>
                </div>
                <br>
                <div class="form-group row">
                    <div class="col-sm-3 control-label">ชื่อร้าน</div>
                    <div class="col-sm-6">
                        <input type="text" name="store_name" required class="form-control">
                    </div>
                </div>
                <br>
                <div class="form-group row">
                    <div class="col-sm-3 control-label">วันที่ทำการ</div>
                    <div class="col-sm-4">
                        <select class="form-control" name="store_date" required>
                            <option value="">เลือกข้อมูล</option>
                            <option value="เปิดทุกวัน">เปิดทุกวัน</option>
                            <option value="เปิดทุกวันจันทร์ - วันศุกร์">เปิดทุกวันจันทร์ - วันศุกร์</option>
                            <option value="เปิดทุกวันจันทร์ - วันเสาร">เปิดทุกวันจันทร์ - วันเสาร</option>
                        </select>
                    </div>
                </div>
                <br>
                <div class="form-group row">
                    <div class="col-sm-3 control-label">เวลาเปิด-ปิด</div>
                    <div class="col-sm-4">
                        <input type="time" name="store_timeopen" required class="form-control">
                    </div>
                    ถึง
                    <div class="col-sm-4">
                        <input type="time" name="store_timeclose" required class="form-control">
                    </div>
                </div>
                <br>
                <div class="form-group row">
                    <div class="col-sm-3 control-label">เบอร์ติดต่อ</div>
                    <div class="col-sm-6">
                        <input type="number" name="store_phone" required class="form-control" max="9999999999">
                    </div>
                </div>
                <br>
                <div class="form-group row">
                    <div class="col-sm-3 control-label">ที่ตั้ง</div>
                    <div class="col-sm-6">
                        <input type="text" name="store_location" required class="form-control">
                    </div>
                </div>
                <br>
                <div class="form-group row">
                    <div class="col-sm-3 control-label">คำอธิบาย</div>
                    <div class="col-sm-9">
                        <textarea name="store_detail" id="store_detail"></textarea>
                        <script>
                            ClassicEditor
                                .create(document.querySelector('#store_detail'), {
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

                    <div class="mt-4 mb-4" id="map"></div>

                    <div class="form-group row">
                        <div class="col-sm-3 control-label">ละติจูด</div>
                        <div class="col-sm-6">
                            <input type="text" name="store_lat" id="store_lat" class="form-control" required>
                        </div>
                    </div>
                    <br>
                    <div class="form-group row">
                        <div class="col-sm-3 control-label">ลองจิจูด</div>
                        <div class="col-sm-6">
                            <input type="text" name="store_long" id="store_long" class="form-control" required>
                        </div>
                    </div>
                    <br>
                    <div class="form-group row">
                        <div class="col-sm-3 control-label">รูปภาพหน้าปก</div>
                        <div class="col-sm-6">
                            <input type="file" name="store_img" class="form-control" accept="image/*" required>
                        </div>
                    </div>
                    <br>
                    <div class="form-group row">
                        <div class="col-sm-3 control-label">รูปภาพเพิ่มเติม(สามารถเพิ่มได้หลายรูป)</div>
                        <div class="col-sm-6">
                            <input type="file" name="store_imgs[]" class="form-control" accept="image/*" multiple required>
                        </div>
                    </div>
                    <br>
                    <div class="form-group row">
                        <div class="col-sm-3 control-label"></div>
                        <div class="col-sm-6">
                            <button type="submit" class="btn btn-success">บันทึกข้อมูล</button>
                            <a class="btn btn-danger" href="<?= site_url('store_backend'); ?>" role="button">ยกเลิก</a>
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
        const initialLat = parseFloat(document.getElementById('store_lat').value);
        const initialLng = parseFloat(document.getElementById('store_long').value);

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
        document.getElementById('store_lat').value = location.lat();
        document.getElementById('store_long').value = location.lng();
    }
</script>