<!DOCTYPE html>
<html lang="th">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>ระบบจัดการสมาชิก</title>
	<link rel="icon" href="<?php echo base_url("docs/logo.png"); ?>" type="image/x-icon">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/tailwindcss/2.2.19/tailwind.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
 <!-- sweetalert 2 -->
 <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/sweetalert2@11.7.31/dist/sweetalert2.min.css">
	
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>

	
<script>
// === DISABLE ALL DEBUG/CONSOLE LOGS ===
(function() {
    // ปิด console ทั้งหมด
    console.log = function() {};
    console.warn = function() {};
    console.info = function() {};
    console.debug = function() {};
    // เก็บ console.error ไว้เฉพาะ error ที่สำคัญ (optional)
    // console.error = function() {};
})();
</script>	
	

	
	
	
	<style>
/* เพิ่ม transition เพื่อความสวยงาม */
.menu-grid {
    transition: opacity 0.3s ease;
}

/* ปรับแต่ง checkbox ให้สวยงาม */
.form-checkbox {
    cursor: pointer;
}

/* ปรับขนาดและสีของ label เมื่อ disabled */
.form-checkbox:disabled + label {
    color: #9CA3AF;
    cursor: not-allowed;
}
</style>
	

</head>
<body class="bg-gray-50">
	<div>