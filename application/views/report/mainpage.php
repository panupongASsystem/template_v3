<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>My Chart</title>
    <!-- <script src="https://cdn.jsdelivr.net/npm/chart.js"></script> -->
    <style>


    </style>
</head>

<body>
    <div class="container">
        <div class="row">
            <div class="col-md-9">
                <h4>ฟอร์มเรียกดูเอกสารตามวัน/เดือน/ปี</h4>
            </div>
            <div class="col-sm-9">
                <form action="<?php echo site_url('report/getform'); ?>" method="post" class="form-horizontal">
                    <div class="form-group">
                        <div class="col-sm-1">
                            start
                        </div>
                        <div class="col-sm-3">
                            <input type="date" name="ds" class="form-control" required>
                        </div>
                        <br>
                        <div class="col-sm-1">
                            end
                        </div>
                        <div class="col-sm-3">
                            <input type="date" name="de" class="form-control" required>
                        </div>
                        <br>
                        <div class="col-sm-3">
                            <button type="submit" class="btn btn-info">ดูเอกสาร</button>
                        </div>
                    </div>
                </form>
            </div>
        </div>
        <br>
        <hr>
        <div class="row">
        <div class="col-md-9">
            <h4>แสดงรายงานจำนวนเอกสารแยกตามวันที่</h4>
        </div>
        <div class="col-sm-9">
            <table class="table table-bordered table-hover">
                <thead>
                    <tr>
                        <th style="width: 60%;">ว/ด/ปี</th>
                        <th style="width: 40%;">
                            <center>จำนวนเอกสาร</center>
                        </th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($date as $rs) { ?>
                        <tr>
                            <td><?php echo $rs->docsave; ?></td>
                            <td align="center"><?php echo $rs->dtotal; ?></td>
                        </tr>
                    <?php } ?>
                </tbody>
                <?php
                $total_sum = 0;
                if (!empty($date)) {
                    foreach ($date as $data) {
                        $total_sum += $data->dtotal;
                ?>
                <?php
                    }
                }
                echo 'จำนวนเอกสารทั้งหมด = ';
                echo $total_sum;
                echo ' รายการ';
                ?>
            </table>
        </div>
        <div class="col-sm-9">
            <canvas id="myChart_d"></canvas>
        </div>
        <br><br><br><br><br><br><br><br>
        <hr>
        <div class="row">
        <div class="col-md-9">
            <h4>แสดงรายงานจำนวนเอกสารแยกตามเดือน</h4>
        </div>
        <div class="col-sm-9">

            <table class="table table-bordered table-hover">
                <thead>
                    <tr>
                        <th style="width: 60%;">ด/ปี</th>
                        <th style="width: 40%;">
                            <center>จำนวนเอกสาร</center>
                        </th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($month as $rs) { ?>
                        <tr>
                            <td><?php echo $rs->docsave; ?></td>
                            <td align="center"><?php echo $rs->dtotal; ?></td>
                        </tr>
                    <?php } ?>
                </tbody>
                <?php
                $total_sum = 0;
                if (!empty($month)) {
                    foreach ($month as $data) {
                        $total_sum += $data->dtotal;
                ?>
                <?php
                    }
                }
                echo 'จำนวนเอกสารทั้งหมด = ';
                echo $total_sum;
                echo ' รายการ';
                ?>
            </table>
        </div>
        <div class="col-sm-9">
            <canvas id="myChart_m"></canvas>
        </div>
        <br><br><br><br><br><br><br><br>
        <hr>
        <div class="row">
            <div class="col-md-9">
                <h4>แสดงรายงานจำนวนเอกสารแยกตามปี</h4>
            </div>
            <div class="col-sm-9">

                <table class="table table-bordered table-hover">
                    <thead>
                        <tr>
                            <th style="width: 60%;">ปี</th>
                            <th style="width: 40%;">
                                <center>จำนวนเอกสาร</center>
                            </th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($year as $rs) { ?>
                            <tr>
                                <td><?php echo $rs->docsave; ?></td>
                                <td align="center"><?php echo $rs->dtotal; ?></td>
                            </tr>
                        <?php } ?>
                    </tbody>
                    <?php
                    $total_sum = 0;
                    if (!empty($year)) {
                        foreach ($year as $data) {
                            $total_sum += $data->dtotal;
                    ?>
                    <?php
                        }
                    }
                    echo 'จำนวนเอกสารทั้งหมด = ';
                    echo $total_sum;
                    echo ' รายการ';
                    ?>
                </table>
            </div>
            <div class="col-sm-9">
                <canvas id="myChart_y"></canvas>
            </div>
        </div>
        <br><br><br><br><br><br><br><br>
        <hr>
        <div class="row">
            <div class="col-md-9">
                <h4>แสดงรายงานจำนวนเอกสารแยกตามสิทธิ์การเข้าถึง</h4>
                <h4>จำนวนเอกสารที่มีทั้งหมดในระบบ</h4>
            </div>
            <div class="col-sm-9">

                <table class="table table-bordered table-hover">
                    <thead>
                        <tr>
                            <th style="width: 60%;">สิทธิ์</th>
                            <th style="width: 40%;">
                                <center>จำนวนเอกสาร</center>
                            </th>

                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($status as $rs) { ?>
                            <tr>
                                <td>
                                    <?php
                                    $ds = $rs->doc_status;
                                    if ($ds == 1) {
                                        echo '- อ่านได้ทุกระดับ ';
                                    } else {
                                        echo '- อ่านได้เฉพาะผู้บริหาร';
                                    }
                                    ?>
                                </td>
                                <td align="center"><?php echo $rs->dtotal; ?></td>
                            </tr>
                        <?php } ?>
                    </tbody>
                    <?php
                    $total_sum = 0;
                    if (!empty($year)) {
                        foreach ($year as $data) {
                            $total_sum += $data->dtotal;
                    ?>
                    <?php
                        }
                    }
                    echo 'จำนวนเอกสารทั้งหมด = ';
                    echo $total_sum;
                    echo ' รายการ';
                    ?>
                </table>
            </div>
            <div class="col-sm-9">
                <canvas id="myChart_l"></canvas>
            </div>
        </div>
        <br><br><br><br><br><br><br><br>
        <hr>
        <div class="row">
            <div class="col-md-9">
                <h4>แสดงรายงานจำนวนเอกสารแยกตามประเภทเอกสาร</h4>
                <h4>จำนวนเอกสารที่มีทั้งหมดในระบบ</h4>
            </div>
            <div class="col-sm-9">

                <table class="table table-bordered table-hover">
                    <thead>
                        <tr>
                            <th style="width: 60%;">ประเภท</th>
                            <th style="width: 40%;">
                                <center>จำนวนเอกสาร</center>
                            </th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($type as $rs) { ?>
                            <tr>
                                <td><?php echo $rs->dname; ?></td>
                                <td align="center"><?php echo $rs->dtotal; ?></td>
                            </tr>
                        <?php } ?>
                    </tbody>
                    <?php
                    $total_sum = 0;
                    if (!empty($year)) {
                        foreach ($year as $data) {
                            $total_sum += $data->dtotal;
                    ?>
                    <?php
                        }
                    }
                    echo 'จำนวนเอกสารทั้งหมด = ';
                    echo $total_sum;
                    echo ' รายการ';
                    ?>
                </table>
            </div>
            <div class="col-sm-9">
                <canvas id="myChart_t"></canvas>
            </div>
        </div>
    </div>


        <script>
            // รายวัน
            var dataFromPHP = <?php echo json_encode($date); ?>;

            // สร้างข้อมูลสำหรับกราฟ
            var labels = [];
            var data = [];

            dataFromPHP.forEach(item => {
                labels.push(item.docsave);
                data.push(item.dtotal);
            });

            // สร้างกราฟแท่ง
            var ctx = document.getElementById('myChart_d').getContext('2d');
            var myChart = new Chart(ctx, {
                type: 'bar',
                data: {
                    labels: labels,
                    datasets: [{
                        label: 'จำนวนเอกสาร',
                        data: data,
                        backgroundColor: 'rgba(161, 198, 247, 1)', // ['rgba(161, 198, 247, 1)', 'rgba(98, 205, 108, 1)',  'rgba(210, 105, 30, 1)']
                        borderColor: 'rgb(47, 98, 237)', // ['rgb(47, 98, 237)',  'rgb(34, 139, 34)',  'rgb(139, 69, 19)',]
                        borderWidth: 1
                    }]
                },
                options: {
                    responsive: false, // ตั้งค่า responsive เป็น false เพื่อไม่ให้เปลี่ยนขนาดกราฟ
                    scales: {
                        y: {
                            beginAtZero: true
                        }
                    }
                }
            });

            // รายเดือน
            var dataFromPHP = <?php echo json_encode($month); ?>;

            // สร้างข้อมูลสำหรับกราฟ
            var labels = [];
            var data = [];

            dataFromPHP.forEach(item => {
                labels.push(item.docsave);
                data.push(item.dtotal);
            });

            // สร้างกราฟแท่ง
            var ctx = document.getElementById('myChart_m').getContext('2d');
            var myChart = new Chart(ctx, {
                type: 'bar',
                data: {
                    labels: labels,
                    datasets: [{
                        label: 'จำนวนเอกสาร',
                        data: data,
                        backgroundColor: 'rgba(161, 198, 247, 1)', // ['rgba(161, 198, 247, 1)', 'rgba(98, 205, 108, 1)',  'rgba(210, 105, 30, 1)']
                        borderColor: 'rgb(47, 98, 237)', // ['rgb(47, 98, 237)',  'rgb(34, 139, 34)',  'rgb(139, 69, 19)',]
                        borderWidth: 1
                    }]
                },
                options: {
                    responsive: false, // ตั้งค่า responsive เป็น false เพื่อไม่ให้เปลี่ยนขนาดกราฟ
                    scales: {
                        y: {
                            beginAtZero: true
                        }
                    }
                }
            });

            // year
            var dataFromPHP = <?php echo json_encode($year); ?>;

            // สร้างข้อมูลสำหรับกราฟ
            var labels = [];
            var data = [];

            dataFromPHP.forEach(item => {
                labels.push(item.docsave);
                data.push(item.dtotal);
            });

            // สร้างกราฟแท่ง
            var ctx = document.getElementById('myChart_y').getContext('2d');
            var myChart = new Chart(ctx, {
                type: 'bar',
                data: {
                    labels: labels,
                    datasets: [{
                        label: 'จำนวนเอกสาร',
                        data: data,
                        backgroundColor: 'rgba(161, 198, 247, 1)', // ['rgba(161, 198, 247, 1)', 'rgba(98, 205, 108, 1)',  'rgba(210, 105, 30, 1)']
                        borderColor: 'rgb(47, 98, 237)', // ['rgb(47, 98, 237)',  'rgb(34, 139, 34)',  'rgb(139, 69, 19)',]
                        borderWidth: 1
                    }]
                },
                options: {
                    responsive: false, // ตั้งค่า responsive เป็น false เพื่อไม่ให้เปลี่ยนขนาดกราฟ
                    scales: {
                        y: {
                            beginAtZero: true
                        }
                    }
                }
            });

              // ดึงข้อมูลจาก PHP ที่ส่งมาจาก controller
        var dataFromPHP = <?php echo json_encode($type); ?>;
        
        // สร้างข้อมูลสำหรับกราฟ
        var labels = [];
        var data = [];
        
        dataFromPHP.forEach(item => {
            labels.push(item.dname);
            data.push(item.dtotal);
        });
        
        // สร้างกราฟแท่ง
        var ctx = document.getElementById('myChart_t').getContext('2d');
        var myChart = new Chart(ctx, {
            type: 'bar',
            data: {
                labels: labels,
                datasets: [{
                    label: 'จำนวนเอกสาร',
                    data: data,
                    backgroundColor: 'rgba(161, 198, 247, 1)', // ['rgba(161, 198, 247, 1)', 'rgba(98, 205, 108, 1)',  'rgba(210, 105, 30, 1)']
                    borderColor: 'rgb(47, 128, 237)', // ['rgb(47, 128, 237)',  'rgb(34, 139, 34)',  'rgb(139, 69, 19)',]
                    borderWidth: 1
                }]
            },
            options: {
                responsive: false, // ตั้งค่า responsive เป็น false เพื่อไม่ให้เปลี่ยนขนาดกราฟ
                scales: {
                    y: {
                        beginAtZero: true
                    }
                }
            }
        });

        // ดึงข้อมูลจาก PHP ที่ส่งมาจาก controller
        var dataFromPHP = <?php echo json_encode($status); ?>;

        // สร้างข้อมูลสำหรับกราฟ
        var labels = [];
        var data = [];
        var dataNames = [];

        dataFromPHP.forEach(item => {
            labels.push(item.doc_status);
            data.push(item.dtotal);

            // เพิ่มชื่อของข้อมูลลงใน dataNames
            var ds = item.doc_status;
            var dn = (ds == 1) ? 'อ่านได้ทุกระดับ' : 'เฉพาะผู้บริหาร';
            dataNames.push(dn);
        });

        // สร้างกราฟแท่ง
        var ctx = document.getElementById('myChart_l').getContext('2d');
        var myChart = new Chart(ctx, {
            type: 'bar',
            data: {
                labels: labels,
                datasets: [{
                    label: 'จำนวนสิทธ์',
                    data: data,
                    backgroundColor: 'rgba(161, 198, 247, 1)', // ['rgba(161, 198, 247, 1)', 'rgba(98, 205, 108, 1)',  'rgba(210, 105, 30, 1)']
                    borderColor: 'rgb(47, 128, 237)', // ['rgb(47, 128, 237)',  'rgb(34, 139, 34)',  'rgb(139, 69, 19)',]
                    borderWidth: 1
                }]
            },
            options: {
                responsive: false, // ตั้งค่า responsive เป็น false เพื่อไม่ให้เปลี่ยนขนาดกราฟ
                scales: {
                    y: {
                beginAtZero: true,
                ticks: {
                    callback: function (value, index, values) {
                        // แสดงจำนวนเอกสารในตัวแกน y เหมือนเดิม
                        return value;
                    }
                }
            },
            x: {
                // แสดงข้อมูลสิทธิ์ของแอดมินในตัวแกน x (ต่ำแหน่งของ labels ใน dataNames)
                ticks: {
                    callback: function (value, index, values) {
                        return dataNames[index];
                    }
                }
            }
        }
    }
});
        </script>
</body>

</html>