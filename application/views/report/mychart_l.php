<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>My Chart</title>
    <!-- <script src="https://cdn.jsdelivr.net/npm/chart.js"></script> -->
</head>

<body>



    <div class="container">
        <div class="row">
        <div class="col-md-12">
            <h2>แสดงรายงานจำนวนเอกสารแยกตามสิทธิ์การเข้าถึง</h2>
        </div>
        <div class="col-sm-5">

            <table class="table table-bordered table-hover">
                <thead>
                    <tr>
                        <th style="width: 60%;" >สิทธิ์</th>
                        <th style="width: 40%;"><center>จำนวนเอกสาร</center></th>

                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($query as $rs) { ?>
                        <tr>
                            <td>
                                <?php 
                                $ds = $rs->doc_status; 
                                if($ds==1){
                                    echo '- อ่านได้ทุกระดับ ';
                                }else{
                                    echo '- อ่านได้เฉพาะผู้บริหาร';
                                }
                                ?>
                            </td>
                            <td align="center"><?php echo $rs->dtotal; ?></td>
                        </tr>
                    <?php } ?>
                </tbody>
                <?php 
                 $total_sum=0;
                 if(!empty($query)){
                    foreach($query as $data) {
                        $total_sum+=$data->dtotal;
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
            <div class="col-sm-12">
                <canvas id="myChart"></canvas>
            </div>
        </div>
    </div>
    <script>
        // ดึงข้อมูลจาก PHP ที่ส่งมาจาก controller
        var dataFromPHP = <?php echo json_encode($query); ?>;

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
        var ctx = document.getElementById('myChart').getContext('2d');
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