<title>WiFi Map</title>
<style>
    /* เพิ่มสไตล์ของแผนที่ */
    #map {
        height: 400px;
        width: 100%;
    }
</style>

<h1>พิกัดWiFi</h1>
<!-- ส่วนแสดงแผนที่ -->
<div id="map"></div>
<br>
<a class="btn add-btn" href="<?= site_url('wifi/addingWifi'); ?>" role="button"><i class="bi bi-plus-circle"></i> เพิ่มข้อมูล</a>
<a class="btn btn-light" href="<?= site_url('wifi'); ?>" role="button"><i class="bi bi-arrow-clockwise"></i> Refresh Data</a>
<br>
<!-- DataTales Example -->
<div class="card shadow mb-4">
    <div class="card-header py-3">
        <h6 class="m-0 font-weight-bold text-black">จัดการข้อมูล WiFi</h6>
    </div>
    <div class="card-body">
        <div class="table-responsive">
            <!-- <table class="table table-bordered" id="dataTable" cellspacing="0"> -->
            <table id="newdataTables" class="table" cellspacing="0">
                <thead>
                    <tr>
                        <th tabindex="0" rowspan="1" colspan="1">ลำดับ</th>
                        <th tabindex="0" rowspan="1" colspan="1">ชื่อ</th>
                        <th tabindex="0" rowspan="1" colspan="1">ละติจูด/ลองจิจูด</th>
                        <th tabindex="0" rowspan="1" colspan="1">เวลาที่บันทึก</th>
                        <th tabindex="0" rowspan="1" colspan="1">จัดการ</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($query as $index => $rs) { ?>
                        <tr role="row">
                            <td align="center"><?= $index + 1; ?></td>
                            <td><?= $rs->wifi_name; ?></td>
                            <td><?= $rs->wifi_lat; ?>,<br><?= $rs->wifi_long; ?></td>
                            <!-- <td><?= date('d/m/Y', strtotime($rs->wifi_datesave . '+543 years')) ?></td> ไม่มีนาที -->
                            <td><?= date('d/m/Y : H:i', strtotime($rs->wifi_datesave . '+543 years')) ?></td>
                            <td>
                                <a href="<?= site_url('wifi/edit/' . $rs->wifi_id); ?>"><i class="bi bi-pencil-square fa-lg "></i></a>
                                <a href="#" role="button" onclick="confirmDelete(<?= $rs->wifi_id; ?>);"><i class="bi bi-trash fa-lg "></i></a>

                                <script>
                                     function confirmDelete(wifi_id) {
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
                                            window.location.href = "<?= site_url('wifi/del_Wifi/'); ?>" + wifi_id;
                                        }
                                    });
                                }
                                </script>
                            </td>
                        </tr>
                    <?php } ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<!-- โหลด Google Maps JavaScript API -->
<script src="https://maps.googleapis.com/maps/api/js?key=AIzaSyCz5M0j4ysiYbUiYOAoidfE0hbEXJIq7MI&callback=initMap" async defer></script>

<script>
    // เอาแมพมาแสดงผล
    let map;
    let currentMarker; // เพิ่มตัวแปรเก็บมาร์คปัจจุบัน

    function initMap() {
        map = new google.maps.Map(document.getElementById('map'), {
            center: {
                lat: 16.5463601,
                lng: 104.555
            },
            zoom: 10
        });


        // เรียกใช้งาน API เพื่อดึงข้อมูลจากตาราง tbl_Wifi
        $.get('<?php echo base_url("wifi/getWifis"); ?>', function(data) {
            const wifis = JSON.parse(data);

            // วนลูปเพื่อสร้างมาร์คบนแผนที่
            wifis.forEach(wifi => {
                const marker = new google.maps.Marker({
                    position: {
                        lat: parseFloat(wifi.wifi_lat),
                        lng: parseFloat(wifi.wifi_long)
                    },
                    map: map,
                    icon: {
                        url: '<?php echo base_url("docs/icon-wifi.png"); ?>',
                        scaledSize: new google.maps.Size(32, 32) // ปรับขนาดตามที่คุณต้องการ
                    },
                    title: wifi.wifi_name
                });
                // เพิ่มเหตุการณ์คลิกลงบน Marker เพื่อแสดง Pop-up Info Window
                marker.addListener('click', () => {
                    const infoWindow = new google.maps.InfoWindow({
                        content: `<h5>${wifi.wifi_name}</h5> `
                    });

                    // ปิดมาร์คปัจจุบัน (ถ้ามี)
                    if (currentMarker) {
                        currentMarker.infoWindow.close();
                    }

                    // เปิด Pop-up Info Window และเซ็ตเป็นมาร์คปัจจุบัน
                    infoWindow.open(map, marker);
                    currentMarker = {
                        marker: marker,
                        infoWindow: infoWindow
                    };
                });
            });
        });
    }
</script>