<?php
defined('BASEPATH') OR exit('No direct script access allowed');
?><!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Database Error - ข้อผิดพลาดจากฐานข้อมูล</title>
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
    </style>
</head>
<body>
 <h1>Database Error - ข้อผิดพลาดจากฐานข้อมูล</h1>
    <p>เกิดข้อผิดพลาดขณะพยายามเชื่อมต่อกับฐานข้อมูล กรุณาติดต่อผู้ดูแลระบบหากปัญหานี้ยังคงอยู่</p>
    <p>A database error occurred while trying to connect. Please contact the administrator if this issue persists.</p>

    <div style="text-align: left; margin: 0 auto; max-width: 600px;">
        <h2><?php echo $heading; ?></h2>
        <code><?php echo $message; ?></code>
    </div><br>
    
    <a href="javascript:void(0);" class="button" onclick="window.history.back();">ย้อนกลับ</a><br>

    <img src="/docs/aslogo.png" alt="AS SYSTEM Co.,Ltd">
    <p>Powered by AS SYSTEM Co.,Ltd</p>
</body>
</html>
