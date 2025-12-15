<?php
defined('BASEPATH') OR exit('No direct script access allowed');
?><!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Error - An uncaught Exception was encountered</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            text-align: center;
            padding: 50px;
            background-color: #f8f8f8;
        }
        h1 {
            font-size: 48px;
            color: #ff0000;
            margin: 0;
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

    <h1>An uncaught Exception was encountered</h1>
    <p>เกิดข้อผิดพลาดที่ไม่สามารถระบุได้ กรุณาตรวจสอบรายละเอียดด้านล่าง</p>

    <div style="text-align: left; margin: 0 auto; max-width: 600px;">
        <p><strong>Type:</strong> <?php echo get_class($exception); ?></p>
        <p><strong>Message:</strong> <?php echo $message; ?></p>
        <p><strong>Filename:</strong> <?php echo $exception->getFile(); ?></p>
        <p><strong>Line Number:</strong> <?php echo $exception->getLine(); ?></p>

        <?php if (defined('SHOW_DEBUG_BACKTRACE') && SHOW_DEBUG_BACKTRACE === TRUE): ?>
            <p><strong>Backtrace:</strong></p>
            <?php foreach ($exception->getTrace() as $error): ?>
                <?php if (isset($error['file']) && strpos($error['file'], realpath(BASEPATH)) !== 0): ?>
                    <p style="margin-left:10px">
                        File: <?php echo $error['file']; ?><br />
                        Line: <?php echo $error['line']; ?><br />
                        Function: <?php echo $error['function']; ?>
                    </p>
                <?php endif ?>
            <?php endforeach ?>
        <?php endif ?>
    </div><br><br>

    <a href="javascript:void(0);" class="button" onclick="window.history.back();">ย้อนกลับ</a><br>

    <img src="/docs/aslogo.png" alt="AS SYSTEM Co.,Ltd"><br>
    <p>Powered by AS SYSTEM Co.,Ltd</p>

</body>
</html>
