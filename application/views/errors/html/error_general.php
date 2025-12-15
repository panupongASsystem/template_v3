<?php
defined('BASEPATH') OR exit('No direct script access allowed');
?><!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Error - <?php echo $heading; ?></title>
    <style>
        body {
            font-family: Arial, sans-serif;
            text-align: center;
            padding: 50px;
            background-color: #f8f8f8;
        }
        h1 {
            font-size: 48px;
            margin: 0;
            color: #ff0000;
        }
        p {
            font-size: 18px;
            color: #666;
        }
        code {
            font-family: Consolas, Monaco, Courier New, Courier, monospace;
            font-size: 14px;
            background-color: #f9f9f9;
            border: 1px solid #D0D0D0;
            color: #002166;
            display: block;
            margin: 20px 0;
            padding: 12px;
        }
        .button {
            background-color: #007bff;
            color: white;
            padding: 10px 20px;
            text-decoration: none;
            border-radius: 5px;
            font-size: 18px;
            margin-top: 20px;
            display: inline-block;
        }
        .button:hover {
            background-color: #0056b3;
        }
        img {
            margin-top: 30px;
        }
        #container {
            margin: 10px;
            border: 1px solid #D0D0D0;
            box-shadow: 0 0 8px #D0D0D0;
            padding: 20px;
        }
    </style>
</head>
<body>

    <div id="container">
        <h1><?php echo $heading; ?></h1>
        <p><?php echo $message; ?></p><br><br>
        <p>กรุณาตรวจสอบรายละเอียดของข้อผิดพลาดที่เกิดขึ้น หรือกดปุ่มด้านล่างเพื่อกลับไปยังหน้าที่แล้ว</p><br><br>

         <a href="javascript:void(0);" class="button" onclick="window.history.back();">ย้อนกลับ</a><br><br>

        <img src="/docs/aslogo.png" alt="AS SYSTEM Co.,Ltd">
        <p>Powered by AS SYSTEM Co.,Ltd</p>
    </div>

</body>
</html>
