<script>
        $(document).ready(function () {
    // เมื่อหน้าเว็บโหลดเสร็จ
    // กำหนดค่าเริ่มต้นให้ select option เป็น "ทั้งหมด"
    $("#displayOption").val("all");

    // เมื่อมีการเปลี่ยนค่าใน select option
    $("#displayOption").change(function () {
        var selectedOption = $(this).val();
        $("#newdataTables tbody tr").hide();

        if (selectedOption === "all") {
            $("#newdataTables tbody tr").show();
        } else if (selectedOption === "first") {
            $("#newdataTables tbody tr:lt(<?= count($qadmin) ?>)").show();
        } else if (selectedOption === "second") {
            $("#newdataTables tbody tr:gt(<?= count($qadmin) - 1 ?>)").show();
        }
    });
});
    </script>