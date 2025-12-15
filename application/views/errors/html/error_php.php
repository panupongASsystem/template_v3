<?php
defined('BASEPATH') OR exit('No direct script access allowed');
?><!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>A PHP Error was encountered</title>
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
        .error-box {
            border: 1px solid #990000;
            padding: 20px;
            margin: 20px 0;
            text-align: left;
        }
        .error-box h4 {
            color: #990000;
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

<h1>A PHP Error was encountered</h1>

<div class="error-box">
    <h4>รายละเอียดข้อผิดพลาด PHP:</h4>
    <p><strong>Severity:</strong> <?php echo $severity; ?></p>
    <p><strong>Message:</strong> <?php echo $message; ?></p>
    <p><strong>Filename:</strong> <?php echo $filepath; ?></p>
    <p><strong>Line Number:</strong> <?php echo $line; ?></p>

    <?php if (defined('SHOW_DEBUG_BACKTRACE') && SHOW_DEBUG_BACKTRACE === TRUE): ?>
        <h4>Backtrace:</h4>
        <?php foreach (debug_backtrace() as $error): ?>
            <?php if (isset($error['file']) && strpos($error['file'], realpath(BASEPATH)) !== 0): ?>
                <p style="margin-left:10px">
                <strong>File:</strong> <?php echo $error['file'] ?><br />
                <strong>Line:</strong> <?php echo $error['line'] ?><br />
                <strong>Function:</strong> <?php echo $error['function'] ?>
                </p>
            <?php endif ?>
        <?php endforeach ?>
    <?php endif ?>
</div><br><br>

   <a href="javascript:void(0);" class="button" onclick="window.history.back();">ย้อนกลับ</a><br><br>

<img src="/docs/aslogo.png" alt="AS SYSTEM Co.,Ltd">
<p>Powered by AS SYSTEM Co.,Ltd</p>

</body>
</html>
