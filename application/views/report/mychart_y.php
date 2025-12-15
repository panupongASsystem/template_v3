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
            <h2>แสดงรายงานจำนวนเอกสารแยกตามปี</h2>
        </div>
        <div class="col-sm-5">

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
                    <?php foreach ($query as $rs) { ?>
                        <tr>
                            <td><?php echo $rs->docsave;?></td>
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
        
        dataFromPHP.forEach(item => {
            labels.push(item.docsave);
            data.push(item.dtotal);
        });
        
        // สร้างกราฟแท่ง
        var ctx = document.getElementById('myChart').getContext('2d');
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
    </script>
</body>
</html>