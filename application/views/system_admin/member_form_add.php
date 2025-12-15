<div class="container">
    <div class="row">
        <div class="col-md-2"></div>
        <div class="col-md-7">
            <h4>เพิ่มข้อมูลสมาชิก</h4>
            <form id="memberForm" action="<?php echo site_url('member_backend/add_Member'); ?>" method="post" class="form-horizontal" enctype="multipart/form-data">
                <div class="form-group row">
                    <div class="col-sm-3 control-label">ชื่อผู้ใช้งาน</div>
                    <div class="col-sm-5">
                        <input type="text" name="m_username" class="form-control" required>
                    </div>
                </div>
                <br>
                <div class="form-group row">
                    <div class="col-sm-3 control-label">รหัสผ่าน</div>
                    <div class="col-sm-5">
                        <div class="input-group">
                            <input type="password" id="m_password" name="m_password" class="form-control">
                            <button type="button" class="btn btn-outline-secondary" onclick="swapPasswordType()">
                                <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-eye" viewBox="0 0 16 16">
                                    <path d="M16 8s-3-5.5-8-5.5S0 8 0 8s3 5.5 8 5.5S16 8 16 8M1.173 8a13.133 13.133 0 0 1 1.66-2.043C4.12 4.668 5.88 3.5 8 3.5c2.12 0 3.879 1.168 5.168 2.457A13.133 13.133 0 0 1 14.828 8c-.058.087-.122.183-.195.288-.335.48-.83 1.12-1.465 1.755C11.879 11.332 10.119 12.5 8 12.5c-2.12 0-3.879-1.168-5.168-2.457A13.134 13.134 0 0 1 1.172 8z" />
                                    <path d="M8 5.5a2.5 2.5 0 1 0 0 5 2.5 2.5 0 0 0 0-5M4.5 8a3.5 3.5 0 1 1 7 0 3.5 3.5 0 0 1-7 0" />
                                </svg>
                            </button>
                        </div>
                    </div>
                </div>
                <br>
                <div class="form-group row">
                    <div class="col-sm-3 control-label">ยืนยัน รหัสผ่าน</div>
                    <div class="col-sm-5">
                        <div class="input-group">
                            <input type="password" id="confirm_password" name="confirm_password" class="form-control">
                            <button type="button" class="btn btn-outline-secondary" onclick="swapPasswordTypeConfirm()">
                                <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-eye" viewBox="0 0 16 16">
                                    <path d="M16 8s-3-5.5-8-5.5S0 8 0 8s3 5.5 8 5.5S16 8 16 8M1.173 8a13.133 13.133 0 0 1 1.66-2.043C4.12 4.668 5.88 3.5 8 3.5c2.12 0 3.879 1.168 5.168 2.457A13.133 13.133 0 0 1 14.828 8c-.058.087-.122.183-.195.288-.335.48-.83 1.12-1.465 1.755C11.879 11.332 10.119 12.5 8 12.5c-2.12 0-3.879-1.168-5.168-2.457A13.134 13.134 0 0 1 1.172 8z" />
                                    <path d="M8 5.5a2.5 2.5 0 1 0 0 5 2.5 2.5 0 0 0 0-5M4.5 8a3.5 3.5 0 1 1 7 0 3.5 3.5 0 0 1-7 0" />
                                </svg>
                            </button>
                        </div>
                    </div>
                </div>
                <br>
                <!-- <div class="form-group row">
                    <div class="col-sm-3 control-label">คำนำหน้าชื่อ</div>
                    <div class="col-sm-3">
                        <select class="form-control" name="m_fname" required>
                            <option value="">เลือกข้อมูล</option>
                            <option value="นาย">นาย</option>
                            <option value="นางสาว">นางสาว</option>
                            <option value="นาง">นาง</option>
                        </select>
                    </div>
                </div>
                <br> -->
                <div class="form-group row">
                    <div class="col-sm-3 control-label">ชื่อ</div>
                    <div class="col-sm-5">
                        <input type="text" name="m_fname" class="form-control" required>
                        <span class="red">กรุณากรอกคำนำหน้าชื่อ</span>
                    </div>
                </div>
                <br>
                <div class="form-group row">
                    <div class="col-sm-3 control-label">นามสกุล</div>
                    <div class="col-sm-5">
                        <input type="text" name="m_lname" class="form-control" required>
                    </div>
                </div>
                <br>
                <!-- <div class="form-group row">
                    <div class="col-sm-3 control-label">เลขบัตรประจำตัวประชาชน</div>
                    <div class="col-sm-4">
                        <input type="text" name="m_number" class="form-control" pattern="\d{13}" title="กรุณากรอกเลขบัตรประจำตัวประชาชนตัวเลข 14 ตัว">
                    </div>
                </div>
                <br> -->
                <div class="form-group row">
                    <div class="col-sm-3 control-label">เบอร์มือถือ</div>
                    <div class="col-sm-5">
                        <input type="text" name="m_phone" class="form-control" required pattern="\d{9,10}" title="กรุณากรอกเบอร์มือถือเป็นตัวเลข 9 หรือ 10 ตัว">
                    </div>
                </div>
                <br>
                <div class="form-group row">
                    <div class="col-sm-3 control-label">E-mail</div>
                    <div class="col-sm-5">
                        <input type="email" name="m_email" id="m_email" class="form-control" required>
                        <div id="email-error" class="text-danger"></div>
                    </div>
                </div>
                <br>
                <!-- <div class="form-group row">
                    <div class="col-sm-3 control-label">จังหวัด</div>
                    <div class="col-sm-5">
                        <select class="form-control" id="province" name="m_pname">
                            <option value="">เลือกจังหวัด</option>
                            <?php foreach ($provinces as $province) : ?>
                                <option value="<?php echo $province->tambol_pname; ?>"><?php echo $province->tambol_pname; ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                </div>
                <br>
                <div class="form-group row">
                    <div class="col-sm-3 control-label">อำเภอ</div>
                    <div class="col-sm-5">
                        <select class="form-control" id="amphur" name="m_aname">
                            <option value="">เลือกอำเภอ</option>
                        </select>
                    </div>
                </div>
                <br>
                <div class="form-group row">
                    <div class="col-sm-3 control-label">ตำบล</div>
                    <div class="col-sm-5">
                        <select class="form-control" id="tambol" name="m_tname">
                            <option value="">เลือกตำบล</option>
                        </select>
                    </div>
                </div>
                <br>
                <div class="form-group row">
                    <div class="col-sm-3 control-label">ที่อยู่</div>
                    <div class="col-sm-5">
                        <input type="text" name="m_address" class="form-control">
                    </div>
                </div>
                <br> -->
                <div class="form-group row">
                    <div class="col-sm-3 control-label">รูปโปรไฟล์</div>
                    <div class="col-sm-5">
                        <input type="file" name="m_img" class="form-control" accept="image/*">
                    </div>
                </div>
                <br>
                <!-- เลือกระบบ -->
                <div class="form-group row">
                    <div class="col-sm-3 control-label">เลือกระบบที่ต้องการ</div>
                    <div class="col-sm-5">
                        <select class="form-control" name="m_system" id="system-select" required>
                            <option value="">เลือกข้อมูล</option>
                            <option value="system_admin">ระบบ Admin</option>
                            <option value="system_back_office">ระบบ Back Office</option>
                        </select>
                    </div>
                </div>
                <br>
                <!-- ระบบ Admin -->
                <div id="admin-form" class="form-group row hidden">
                    <div class="col-sm-3 control-label">ชื่อตำแหน่งงาน</div>
                    <div class="col-sm-5">
                        <select class="form-control" name="ref_pid" id="admin-select" disabled required>
                            <option value="">เลือกข้อมูล</option>
                            <?php foreach ($rspo_admin as $rs) : ?>
                                <?php if ($rs->pid == 1) : ?>
                                    <?php if ($this->session->userdata('m_level') == 1) : ?>
                                        <option value="<?php echo $rs->pid; ?>"><?php echo $rs->pname; ?></option>
                                    <?php endif; ?>
                                <?php else : ?>
                                    <option value="<?php echo $rs->pid; ?>"><?php echo $rs->pname; ?></option>
                                <?php endif; ?>
                            <?php endforeach; ?>
                        </select>
                    </div>
                </div>
                <!-- ระบบ Back Office -->
                <div id="back_office-form" class="form-group row hidden">
                    <div class="col-sm-3 control-label">ชื่อตำแหน่งงาน</div>
                    <div class="col-sm-5">
                        <select class="form-control" name="ref_pid" id="back_office-select" disabled required>
                            <option value="">เลือกข้อมูล</option>
                            <?php foreach ($rspo_back_office as $rs) : ?>
                                <option value="<?php echo $rs->pid; ?>"><?php echo $rs->pname; ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                </div>
                <br>
                <div class="form-group row hidden" id="grant-user-form">
                    <div class="col-sm-4 row">
                        <div class="col-sm-2">
                            <input type="checkbox" id="check-all" name="1" checked />
                        </div>
                        <div class="col-sm-10 control-label">ทั้งหมด</div>
                    </div>
                    <?php foreach ($list_grant_user as $rs) : ?>
                        <div class="col-sm-4 row">
                            <div class="col-sm-2">
                                <input type="checkbox" class="check-item" name="<?php echo $rs->grant_user_id; ?>" checked />
                            </div>
                            <div class="col-sm-10 control-label"><?php echo $rs->grant_user_name; ?></div>
                        </div>
                    <?php endforeach; ?>
                </div>
                <br>
                <input type="hidden" id="grant_user_ref_id" name="grant_user_ref_id" value="">
                <div class="form-group row">
                    <div class="col-sm-3 control-label"></div>
                    <div class="col-sm-6">
                        <button type="submit" class="btn btn-success">บันทึกข้อมูล</button>
                        <a class="btn btn-danger" href="<?php echo site_url('member_backend/index'); ?>">ยกเลิก</a>
                    </div>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        var emailInput = document.getElementById('m_email');
        var emailError = document.getElementById('email-error');
        var form = document.getElementById('memberForm');
        var isEmailValid = true;

        emailInput.addEventListener('blur', function() {
            var email = this.value;
            var xhr = new XMLHttpRequest();
            xhr.open('POST', '<?php echo site_url("member_backend/check_email"); ?>', true);
            xhr.setRequestHeader('Content-type', 'application/x-www-form-urlencoded');
            xhr.onload = function() {
                if (xhr.status === 200) {
                    var response = JSON.parse(xhr.responseText);
                    if (response.exists) {
                        emailError.textContent = 'อีเมลนี้มีอยู่ในระบบแล้ว';
                        isEmailValid = false;
                    } else {
                        emailError.textContent = '';
                        isEmailValid = true;
                    }
                }
            };
            xhr.send('email=' + encodeURIComponent(email));
        });

        form.addEventListener('submit', function(e) {
            if (!isEmailValid) {
                e.preventDefault();
                Swal.fire({
                    title: 'ข้อผิดพลาด!',
                    text: 'อีเมลนี้มีอยู่ในระบบแล้ว กรุณาใช้อีเมลอื่น',
                    icon: 'error',
                    confirmButtonText: 'ตกลง'
                });
            }
        });
    });
</script>

<script>
    document.getElementById('system-select').addEventListener('change', function() {
        var value = this.value;
        var adminForm = document.getElementById('admin-form');
        var backOfficeForm = document.getElementById('back_office-form');
        var grantUserForm = document.getElementById('grant-user-form');
        var adminSelect = document.getElementById('admin-select');
        var backOfficeSelect = document.getElementById('back_office-select');

        // ซ่อนฟอร์มทั้งหมดและปิดการใช้งาน
        adminForm.classList.add('hidden');
        backOfficeForm.classList.add('hidden');
        grantUserForm.classList.add('hidden');
        adminSelect.disabled = true;
        backOfficeSelect.disabled = true;

        // แสดงฟอร์มและเปิดการใช้งานตามการเลือก
        if (value === 'system_admin') {
            adminForm.classList.remove('hidden');
            adminSelect.disabled = false;
        } else if (value === 'system_back_office') {
            backOfficeForm.classList.remove('hidden');
            backOfficeSelect.disabled = false;
        }
    });

    document.getElementById('admin-select').addEventListener('change', function() {
        var value = this.value;
        var grantUserForm = document.getElementById('grant-user-form');

        // ซ่อนฟอร์ม
        grantUserForm.classList.add('hidden');

        // แสดงฟอร์มตามการเลือก
        if (value === '3') { // เปรียบเทียบกับค่าที่ต้องการ
            grantUserForm.classList.remove('hidden');
        }
    });

    document.addEventListener('DOMContentLoaded', function() {
        var checkAll = document.getElementById('check-all');
        var checkItems = document.querySelectorAll('.check-item');
        var form = document.getElementById('checkbox-form');
        var combinedValuesInput = document.getElementById('grant_user_ref_id');

        // เมื่อคลิกที่ checkbox "ทั้งหมด"
        checkAll.addEventListener('change', function() {
            checkItems.forEach(function(checkbox) {
                checkbox.checked = checkAll.checked;
            });
            updateCombinedValues();
        });

        // เมื่อคลิกที่ checkbox รายการอื่นๆ
        checkItems.forEach(function(checkbox) {
            checkbox.addEventListener('change', function() {
                if (!checkbox.checked) {
                    checkAll.checked = false;
                } else {
                    var allChecked = true;
                    checkItems.forEach(function(item) {
                        if (!item.checked) {
                            allChecked = false;
                        }
                    });
                    checkAll.checked = allChecked;
                }
                updateCombinedValues();
            });
        });

        // อัปเดตค่า combined values
        function updateCombinedValues() {
            var values = [];
            if (checkAll.checked) {
                values.push(checkAll.name);
            }
            checkItems.forEach(function(checkbox) {
                if (checkbox.checked) {
                    values.push(checkbox.name);
                }
            });
            combinedValuesInput.value = values.join(',');
        }

        // อัปเดตค่า combined values เมื่อโหลดหน้า
        updateCombinedValues();

        // ก่อนส่งฟอร์ม
        form.addEventListener('submit', function() {
            updateCombinedValues();
        });
    });
</script>