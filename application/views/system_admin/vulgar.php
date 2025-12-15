<!-- sweetalert 2 -->
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11.7.31/dist/sweetalert2.all.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script src="https://code.jquery.com/jquery-3.7.1.min.js" integrity="sha256-/JqT3SQfawRcv/BIHPThkBvs0OEvtFFmqPF/lYI/Cxo=" crossorigin="anonymous"></script>

<style>
.popup {
    display: none;
    position: fixed;
    z-index: 1000;
    left: 0;
    top: 0;
    width: 100%;
    height: 100%;
    background-color: rgba(0,0,0,0.4);
}

.popup-content {
    background-color: #fefefe;
    margin: 5% auto;
    padding: 20px;
    border: 1px solid #888;
    width: 80%;
    max-width: 600px;
    border-radius: 8px;
}

.tabs {
    display: flex;
    border-bottom: 1px solid #ddd;
    margin-bottom: 20px;
}

.tab-button {
    background: none;
    border: none;
    padding: 10px 20px;
    cursor: pointer;
    border-bottom: 2px solid transparent;
    margin-right: 10px;
}

.tab-button.active {
    border-bottom-color: #007bff;
    background-color: #f8f9fa;
}

.tab-content {
    display: none;
}

.tab-content.active {
    display: block;
}

.test-section {
    background: #f8f9fa;
    padding: 15px;
    border-radius: 5px;
    margin-top: 20px;
}

.alert {
    padding: 10px;
    margin: 10px 0;
    border-radius: 4px;
}

.alert-success {
    background-color: #d4edda;
    border: 1px solid #c3e6cb;
    color: #155724;
}

.alert-danger {
    background-color: #f8d7da;
    border: 1px solid #f5c6cb;
    color: #721c24;
}
</style>

<script>
$(document).ready(function() {
    // แสดง Flash Messages สำหรับคำหยาบ
    <?php if ($this->session->flashdata('save_success')): ?>
        Swal.fire({
            icon: 'success',
            title: 'สำเร็จ',
            text: 'บันทึกข้อมูลคำหยาบสำเร็จ',
            timer: 2000,
            showConfirmButton: false
        });
    <?php endif; ?>

    <?php if ($this->session->flashdata('save_again')): ?>
        Swal.fire({
            icon: 'warning',
            title: 'คำเตือน',
            text: 'มีคำนี้ในระบบแล้ว',
            timer: 3000,
            showConfirmButton: false
        });
    <?php endif; ?>

    <?php if ($this->session->flashdata('save_error')): ?>
        Swal.fire({
            icon: 'error',
            title: 'เกิดข้อผิดพลาด',
            text: 'พื้นที่เก็บข้อมูลเต็ม ไม่สามารถบันทึกได้',
            timer: 3000,
            showConfirmButton: false
        });
    <?php endif; ?>

    <?php if ($this->session->flashdata('del_success')): ?>
        Swal.fire({
            icon: 'success',
            title: 'สำเร็จ',
            text: 'ลบข้อมูลคำหยาบสำเร็จ',
            timer: 2000,
            showConfirmButton: false
        });
    <?php endif; ?>

    // แสดง Flash Messages สำหรับ Whitelist
    <?php if ($this->session->flashdata('whitelist_success')): ?>
        Swal.fire({
            icon: 'success',
            title: 'สำเร็จ',
            text: 'จัดการ Whitelist สำเร็จ',
            timer: 2000,
            showConfirmButton: false
        });
    <?php endif; ?>

    <?php if ($this->session->flashdata('whitelist_duplicate')): ?>
        Swal.fire({
            icon: 'warning',
            title: 'คำเตือน',
            text: 'มีคำนี้ใน Whitelist แล้ว',
            timer: 3000,
            showConfirmButton: false
        });
    <?php endif; ?>

    <?php if ($this->session->flashdata('whitelist_error')): ?>
        Swal.fire({
            icon: 'error',
            title: 'เกิดข้อผิดพลาด',
            text: 'ไม่สามารถจัดการ Whitelist ได้',
            timer: 3000,
            showConfirmButton: false
        });
    <?php endif; ?>

    <?php if ($this->session->flashdata('whitelist_del_success')): ?>
        Swal.fire({
            icon: 'success',
            title: 'สำเร็จ',
            text: 'ลบคำจาก Whitelist สำเร็จ',
            timer: 2000,
            showConfirmButton: false
        });
    <?php endif; ?>

    // Flash Messages ทั่วไป
    <?php if ($this->session->flashdata('success')): ?>
        Swal.fire({
            icon: 'success',
            title: 'สำเร็จ',
            text: '<?= $this->session->flashdata('success') ?>',
            timer: 2000,
            showConfirmButton: false
        });
    <?php endif; ?>

    <?php if ($this->session->flashdata('error')): ?>
        Swal.fire({
            icon: 'error',
            title: 'เกิดข้อผิดพลาด',
            text: '<?= $this->session->flashdata('error') ?>',
            timer: 3000,
            showConfirmButton: false
        });
    <?php endif; ?>

    // เมื่อคลิกปุ่มเพิ่มข้อมูล
    $(".insert-vulgar-btn").click(function() {
        var target = $(this).data("target");
        $(target).show();
    });

    // เมื่อคลิกปุ่มปิด
    $(".close-button").click(function() {
        var target = $(this).data("target");
        $(target).hide();
    });

    // ปิด popup เมื่อคลิกนอกกรอบ
    $(window).click(function(event) {
        if ($(event.target).hasClass('popup')) {
            $('.popup').hide();
        }
    });

    // Tab switching
    $('.tab-button').click(function() {
        var tabId = $(this).data('tab');
        
        $('.tab-button').removeClass('active');
        $('.tab-content').removeClass('active');
        
        $(this).addClass('active');
        $('#' + tabId).addClass('active');
    });
});

function toggleCollapse(elementId) {
    var element = document.getElementById(elementId);
    if (element.style.display === "none" || element.style.display === "") {
        element.style.display = "block";
    } else {
        element.style.display = "none";
    }
}

function confirmDelete(vulgar_id) {
    Swal.fire({
        title: 'กดเพื่อยืนยัน?',
        text: "คุณจะไม่สามรถกู้คืนได้อีก!",
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#3085d6',
        cancelButtonColor: '#d33',
        confirmButtonText: 'ใช่, ต้องการลบ!',
        cancelButtonText: 'ยกเลิก'
    }).then((result) => {
        if (result.isConfirmed) {
            window.location.href = "<?= site_url('vulgar_backend/del/'); ?>" + vulgar_id;
        }
    });
}

function confirmDeleteWhitelist(whitelist_id) {
    Swal.fire({
        title: 'ยืนยันการลบ Whitelist?',
        text: "คำนี้จะถูกลบออกจาก Whitelist!",
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#3085d6',
        cancelButtonColor: '#d33',
        confirmButtonText: 'ใช่, ต้องการลบ!',
        cancelButtonText: 'ยกเลิก'
    }).then((result) => {
        if (result.isConfirmed) {
            window.location.href = "<?= site_url('vulgar_backend/del_whitelist/'); ?>" + whitelist_id;
        }
    });
}

function resetWhitelist() {
    Swal.fire({
        title: 'ยืนยันการรีเซ็ต Whitelist?',
        text: "การดำเนินการนี้จะลบ Whitelist ทั้งหมดและเพิ่มข้อมูลเริ่มต้นใหม่",
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#3085d6',
        cancelButtonColor: '#d33',
        confirmButtonText: 'ใช่, รีเซ็ต!',
        cancelButtonText: 'ยกเลิก'
    }).then((result) => {
        if (result.isConfirmed) {
            window.location.href = "<?= site_url('vulgar_backend/reset_whitelist'); ?>";
        }
    });
}

function testVulgar() {
    var testText = $('#test_text').val();
    if (!testText) {
        Swal.fire('ข้อผิดพลาด', 'กรุณาระบุข้อความที่ต้องการทดสอบ', 'error');
        return;
    }

    // แสดง loading
    $('#test_result').html('<div class="alert alert-info"><i class="bi bi-hourglass-split"></i> กำลังทดสอบ...</div>');

    $.ajax({
        url: '<?= site_url('vulgar_backend/test_vulgar') ?>',
        type: 'POST',
        data: { test_text: testText },
        dataType: 'json',
        success: function(response) {
            if (response.status === 'success') {
                var result = response.result;
                var resultHtml = '<div class="alert alert-primary"><h6>ผลการทดสอบ: "' + response.test_text + '"</h6></div>';
                
                if (result.whitelist_check) {
                    resultHtml += '<div class="alert alert-success">';
                    resultHtml += '<i class="bi bi-check-circle"></i> <strong>ผลลัพธ์: ผ่าน</strong><br>';
                    resultHtml += 'เหตุผล: พบคำใน Whitelist';
                    resultHtml += '</div>';
                } else if (Object.keys(result.vulgar_check).length > 0) {
                    resultHtml += '<div class="alert alert-danger">';
                    resultHtml += '<i class="bi bi-x-circle"></i> <strong>ผลลัพธ์: ติด</strong><br>';
                    resultHtml += 'เหตุผล: พบคำหยาบ - ' + Object.keys(result.vulgar_check).join(', ');
                    resultHtml += '</div>';
                } else {
                    resultHtml += '<div class="alert alert-success">';
                    resultHtml += '<i class="bi bi-check-circle"></i> <strong>ผลลัพธ์: ผ่าน</strong><br>';
                    resultHtml += 'เหตุผล: ไม่พบคำหยาบ';
                    resultHtml += '</div>';
                }
                
                // แสดงรายละเอียดเพิ่มเติม
                resultHtml += '<div class="alert alert-light">';
                resultHtml += '<small><strong>รายละเอียดการตรวจสอบ:</strong><br>';
                resultHtml += '• ตรวจสอบ Whitelist: ' + (result.whitelist_check ? 'พบ ✓' : 'ไม่พบ ✗') + '<br>';
                resultHtml += '• ตรวจสอบคำหยาบ: ' + (Object.keys(result.vulgar_check).length > 0 ? 'พบ (' + Object.keys(result.vulgar_check).length + ' คำ)' : 'ไม่พบ ✓') + '<br>';
                if (result.final_result && result.final_result.data) {
                    resultHtml += '• ผลลัพธ์สุดท้าย: ' + (result.final_result.data.has_vulgar_words ? 'ติดคำหยาบ ✗' : 'ผ่านการตรวจสอบ ✓');
                }
                resultHtml += '</small></div>';
                
                $('#test_result').html(resultHtml);
            } else {
                $('#test_result').html('<div class="alert alert-danger"><i class="bi bi-x-circle"></i> ' + response.message + '</div>');
            }
        },
        error: function() {
            $('#test_result').html('<div class="alert alert-danger"><i class="bi bi-x-circle"></i> ไม่สามารถเชื่อมต่อกับเซิร์ฟเวอร์ได้</div>');
        }
    });
}
</script>

<!-- ปุ่มเพิ่มข้อมูล -->
<div class="mb-3">
    <a class="btn btn-primary insert-vulgar-btn" data-target="#popupInsert">
        <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-plus-circle" viewBox="0 0 16 16">
            <path d="M8 15A7 7 0 1 1 8 1a7 7 0 0 1 0 14zm0 1A8 8 0 1 0 8 0a8 8 0 0 0 0 16z" />
            <path d="M8 4a.5.5 0 0 1 .5.5v3h3a.5.5 0 0 1 0 1h-3v3a.5.5 0 0 1-1 0v-3h-3a.5.5 0 0 1 0-1h3v-3A.5.5 0 0 1 8 4z" />
        </svg> เพิ่มข้อมูล
    </a>
    
    <a class="btn btn-light" href="<?= site_url('vulgar_backend'); ?>" role="button">
        <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-arrow-clockwise" viewBox="0 0 16 16">
            <path fill-rule="evenodd" d="M8 3a5 5 0 1 0 4.546 2.914.5.5 0 0 1 .908-.417A6 6 0 1 1 8 2v1z" />
            <path d="M8 4.466V.534a.25.25 0 0 1 .41-.192l2.36 1.966c.12.1.12.284 0 .384L8.41 4.658A.25.25 0 0 1 8 4.466z" />
        </svg> Refresh Data
    </a>

    <a class="btn btn-success" href="<?= site_url('vulgar_backend/export_whitelist'); ?>" role="button">
        <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-download" viewBox="0 0 16 16">
            <path d="M.5 9.9a.5.5 0 0 1 .5.5v2.5a1 1 0 0 0 1 1h12a1 1 0 0 0 1-1v-2.5a.5.5 0 0 1 1 0v2.5a2 2 0 0 1-2 2H2a2 2 0 0 1-2-2v-2.5a.5.5 0 0 1 .5-.5z"/>
            <path d="M7.646 11.854a.5.5 0 0 0 .708 0l3-3a.5.5 0 0 0-.708-.708L8.5 10.293V1.5a.5.5 0 0 0-1 0v8.793L5.354 8.146a.5.5 0 1 0-.708.708l3 3z"/>
        </svg> Export Whitelist
    </a>

    <a class="btn btn-warning" href="#" onclick="resetWhitelist()" role="button">
        <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-arrow-repeat" viewBox="0 0 16 16">
            <path d="M11.534 7h3.932a.25.25 0 0 1 .192.41l-1.966 2.36a.25.25 0 0 1-.384 0l-1.966-2.36a.25.25 0 0 1 .192-.41zm-11 2h3.932a.25.25 0 0 0 .192-.41L2.692 6.23a.25.25 0 0 0-.384 0L.342 8.59A.25.25 0 0 0 .534 9z"/>
            <path fill-rule="evenodd" d="M8 3c-1.552 0-2.94.707-3.857 1.818a.5.5 0 1 1-.771-.636A6.002 6.002 0 0 1 13.917 7H12.9A5.002 5.002 0 0 0 8 3zM3.1 9a5.002 5.002 0 0 0 8.757 2.182.5.5 0 1 1 .771.636A6.002 6.002 0 0 1 2.083 9H3.1z"/>
        </svg> Reset Whitelist
    </a>
</div>

<!-- สถิติการใช้งาน -->
<?php if (isset($statistics)): ?>
<div class="row mb-4">
    <div class="col-md-3">
        <div class="card border-left-danger shadow h-100 py-2">
            <div class="card-body">
                <div class="row no-gutters align-items-center">
                    <div class="col mr-2">
                        <div class="text-xs font-weight-bold text-danger text-uppercase mb-1">คำหยาบทั้งหมด</div>
                        <div class="h5 mb-0 font-weight-bold text-gray-800"><?= number_format($statistics['vulgar_count']) ?> คำ</div>
                    </div>
                    <div class="col-auto">
                        <i class="bi bi-exclamation-triangle fa-2x text-gray-300"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="col-md-3">
        <div class="card border-left-success shadow h-100 py-2">
            <div class="card-body">
                <div class="row no-gutters align-items-center">
                    <div class="col mr-2">
                        <div class="text-xs font-weight-bold text-success text-uppercase mb-1">Whitelist ทั้งหมด</div>
                        <div class="h5 mb-0 font-weight-bold text-gray-800"><?= number_format($statistics['whitelist_count']) ?> คำ</div>
                    </div>
                    <div class="col-auto">
                        <i class="bi bi-shield-check fa-2x text-gray-300"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="col-md-3">
        <div class="card border-left-info shadow h-100 py-2">
            <div class="card-body">
                <div class="row no-gutters align-items-center">
                    <div class="col mr-2">
                        <div class="text-xs font-weight-bold text-info text-uppercase mb-1">อัตราส่วน W:V</div>
                        <div class="h5 mb-0 font-weight-bold text-gray-800">1:<?= $statistics['ratio'] ?></div>
                    </div>
                    <div class="col-auto">
                        <i class="bi bi-bar-chart fa-2x text-gray-300"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="col-md-3">
        <div class="card border-left-warning shadow h-100 py-2">
            <div class="card-body">
                <div class="row no-gutters align-items-center">
                    <div class="col mr-2">
                        <div class="text-xs font-weight-bold text-warning text-uppercase mb-1">ประสิทธิภาพ</div>
                        <div class="h5 mb-0 font-weight-bold text-gray-800">
                            <?php 
                            $efficiency = $statistics['whitelist_count'] > 0 ? round(($statistics['whitelist_count'] / ($statistics['vulgar_count'] + $statistics['whitelist_count'])) * 100, 1) : 0;
                            echo $efficiency . '%';
                            ?>
                        </div>
                    </div>
                    <div class="col-auto">
                        <i class="bi bi-speedometer2 fa-2x text-gray-300"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<?php endif; ?>

<!-- Popup เพิ่มข้อมูล -->
<div id="popupInsert" class="popup">
    <div class="popup-content">
        <div class="tabs">
            <button class="tab-button active" data-tab="vulgar-tab">
                <i class="bi bi-exclamation-triangle"></i> คำหยาบ
            </button>
            <button class="tab-button" data-tab="whitelist-tab">
                <i class="bi bi-shield-check"></i> Whitelist
            </button>
            <button class="tab-button" data-tab="test-tab">
                <i class="bi bi-gear"></i> ทดสอบ
            </button>
        </div>

        <!-- Tab คำหยาบ -->
        <div id="vulgar-tab" class="tab-content active">
            <h4>เพิ่มคำหยาบ</h4>
            <form action="<?php echo site_url('vulgar_backend/add'); ?>" method="post" class="form-horizontal">
                <div class="form-group row">
                    <div class="col-sm-3 control-label">ข้อความ</div>
                    <div class="col-sm-9">
                        <input type="text" name="vulgar_com" required class="form-control" placeholder="ระบุคำหยาบที่ต้องการเพิ่ม">
                    </div>
                </div>
                <br>
                <div class="form-group row">
                    <div class="col-sm-3 control-label"></div>
                    <div class="col-sm-9">
                        <button type="submit" class="btn btn-success">บันทึกข้อมูล</button>
                        <button type="button" class="btn btn-secondary close-button" data-target="#popupInsert">ปิด</button>
                    </div>
                </div>
            </form>
        </div>

        <!-- Tab Whitelist -->
        <div id="whitelist-tab" class="tab-content">
            <h4>เพิ่มคำใน Whitelist</h4>
            <form action="<?php echo site_url('vulgar_backend/add_whitelist'); ?>" method="post" class="form-horizontal">
                <div class="form-group row">
                    <div class="col-sm-3 control-label">คำที่อนุญาต</div>
                    <div class="col-sm-9">
                        <input type="text" name="whitelist_word" required class="form-control" placeholder="เช่น กู้ชีพ, ไอศกรีม">
                    </div>
                </div>
                <br>
                <div class="form-group row">
                    <div class="col-sm-3 control-label">คำอธิบาย</div>
                    <div class="col-sm-9">
                        <textarea name="whitelist_desc" class="form-control" rows="3" placeholder="อธิบายความหมายของคำนี้"></textarea>
                    </div>
                </div>
                <br>
                <div class="form-group row">
                    <div class="col-sm-3 control-label"></div>
                    <div class="col-sm-9">
                        <button type="submit" class="btn btn-success">บันทึกข้อมูล</button>
                        <button type="button" class="btn btn-secondary close-button" data-target="#popupInsert">ปิด</button>
                    </div>
                </div>
            </form>
        </div>

        <!-- Tab ทดสอบ -->
        <div id="test-tab" class="tab-content">
            <h4>ทดสอบระบบตรวจสอบคำหยาบ</h4>
            <div class="test-section">
                <div class="form-group">
                    <label for="test_text">ข้อความทดสอบ:</label>
                    <input type="text" id="test_text" class="form-control" placeholder="พิมพ์ข้อความที่ต้องการทดสอบ">
                </div>
                <br>
                <button type="button" class="btn btn-primary" onclick="testVulgar()">ทดสอบ</button>
                <button type="button" class="btn btn-secondary close-button" data-target="#popupInsert">ปิด</button>
                
                <div id="test_result" class="mt-3"></div>
            </div>
        </div>
    </div>
</div>

<!-- ตารางคำหยาบ -->
<div class="card shadow mb-4">
    <div class="card-header py-3">
        <h6 class="m-0 font-weight-bold text-black">จัดการข้อมูลกรองคำหยาบ</h6>
    </div>
    <div class="card-body">
        <div class="table-responsive">
            <?php $Index = 1; ?>
            <table id="vulgarTable" class="table">
                <thead>
                    <tr>
                        <th style="width: 5%;">ลำดับ</th>
                        <th style="width: 20%;">คำหยาบ</th>
                        <th style="width: 25%;">ผู้เพิ่ม</th>
                        <th style="width: 15%;">วันที่</th>
                        <th style="width: 10%;">จัดการ</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($query as $rs) { ?>
                        <tr role="row">
                            <td align="center"><?= $Index; ?></td>
                            <td><?php echo htmlspecialchars($rs->vulgar_com, ENT_QUOTES, 'UTF-8'); ?></td>
                            <td><?php echo htmlspecialchars($rs->vulgar_by, ENT_QUOTES, 'UTF-8'); ?></td>
                            <td><?= date('d/m/Y H:i', strtotime($rs->vulgar_datesave . '+543 years')) ?> น.</td>
                            <td>
                                <a href="<?= site_url('vulgar_backend/editing/' . $rs->vulgar_id); ?>" title="แก้ไข">
                                    <i class="bi bi-pencil-square fa-lg"></i>
                                </a>
                                <a href="#" role="button" onclick="confirmDelete(<?= $rs->vulgar_id; ?>);" title="ลบ">
                                    <i class="bi bi-trash fa-lg"></i>
                                </a>
                            </td>
                        </tr>
                    <?php $Index++; } ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<!-- ตาราง Whitelist -->
<div class="card shadow mb-4">
    <div class="card-header py-3">
        <h6 class="m-0 font-weight-bold text-success">จัดการ Whitelist (คำที่อนุญาต)</h6>
    </div>
    <div class="card-body">
        <div class="table-responsive">
            <?php $WhiteIndex = 1; ?>
            <table id="whitelistTable" class="table">
                <thead>
                    <tr>
                        <th style="width: 5%;">ลำดับ</th>
                        <th style="width: 25%;">คำที่อนุญาต</th>
                        <th style="width: 35%;">คำอธิบาย</th>
                        <th style="width: 15%;">วันที่เพิ่ม</th>
                        <th style="width: 10%;">จัดการ</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($whitelist as $item) { ?>
                        <tr role="row">
                            <td align="center"><?= $WhiteIndex; ?></td>
                            <td><?php echo htmlspecialchars($item['word'], ENT_QUOTES, 'UTF-8'); ?></td>
                            <td><?php echo htmlspecialchars($item['description'], ENT_QUOTES, 'UTF-8'); ?></td>
                            <td><?= date('d/m/Y H:i', strtotime($item['created_at'] . '+543 years')) ?> น.</td>
                            <td>
                                <a href="<?= site_url('vulgar_backend/edit_whitelist/' . $item['id']); ?>" title="แก้ไข">
                                    <i class="bi bi-pencil-square fa-lg text-primary"></i>
                                </a>
                                <a href="#" role="button" onclick="confirmDeleteWhitelist(<?= $item['id']; ?>);" title="ลบ">
                                    <i class="bi bi-trash fa-lg text-danger"></i>
                                </a>
                            </td>
                        </tr>
                    <?php $WhiteIndex++; } ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<script>
// เพิ่ม DataTables ถ้ามี
$(document).ready(function() {
    if (typeof $.fn.DataTable !== 'undefined') {
        $('#vulgarTable').DataTable({
            "language": {
                "url": "//cdn.datatables.net/plug-ins/1.10.24/i18n/Thai.json"
            },
            "pageLength": 25,
            "order": [[ 0, "asc" ]]
        });
        
        $('#whitelistTable').DataTable({
            "language": {
                "url": "//cdn.datatables.net/plug-ins/1.10.24/i18n/Thai.json"
            },
            "pageLength": 25,
            "order": [[ 0, "asc" ]]
        });
    }
});
</script>