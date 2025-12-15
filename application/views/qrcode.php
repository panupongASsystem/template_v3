<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Document</title>
</head>

<body>
    <!-- แสดง QR Code ใน view -->
    <img src="<?php echo base_url($img); ?>" alt="QR Code">

    <!-- แสดงข้อมูล QR Code (ตัวอย่าง) -->
    <p><?php echo $qrCode; ?></p>

</body>

</html>