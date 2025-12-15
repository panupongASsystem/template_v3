<div class="ml-72 p-8">
    <!-- Header -->
    <div class="flex justify-between items-center mb-6">
        <div>
            <h2 class="text-2xl font-semibold text-gray-800">เพิ่มสมาชิกภายนอก</h2>
            <p class="text-gray-600">กรอกข้อมูลสมาชิกภายนอกใหม่ให้ครบถ้วน</p>
        </div>
        <a href="<?php echo site_url('System_member/member_web_external'); ?>" 
           class="px-4 py-2 bg-gray-600 text-white rounded-lg hover:bg-gray-700 flex items-center">
            <i class="fas fa-arrow-left mr-2"></i>กลับไปหน้ารายการ
        </a>
    </div>

    <!-- Alert Messages -->
    <?php if ($this->session->flashdata('save_error')) : ?>
    <div class="bg-red-100 border-l-4 border-red-500 text-red-700 p-4 mb-6" role="alert">
        <p class="font-bold">เกิดข้อผิดพลาด</p>
        <p><?php echo $this->session->flashdata('save_error'); ?></p>
    </div>
    <?php endif; ?>

    <?php if ($this->session->flashdata('password_mismatch')) : ?>
    <div class="bg-red-100 border-l-4 border-red-500 text-red-700 p-4 mb-6" role="alert">
        <p class="font-bold">เกิดข้อผิดพลาด</p>
        <p>รหัสผ่านไม่ตรงกัน กรุณาตรวจสอบอีกครั้ง</p>
    </div>
    <?php endif; ?>

    <!-- Main Card -->
    <div class="bg-white rounded-lg shadow-md">
        <!-- Form -->
        <?php echo form_open_multipart('System_member/add_member_external_db', ['id' => 'add-member-external-form']); ?>
        <div class="grid grid-cols-1 gap-6 p-6">
            
            <!-- Account Information Card -->
            <div class="bg-white border rounded-lg shadow-sm">
                <div class="px-4 py-3 border-b bg-gray-50">
                    <h3 class="text-lg font-medium text-gray-700">ข้อมูลบัญชีผู้ใช้</h3>
                </div>
                <div class="p-4">
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div>
                            <label for="mp_email" class="block text-lg font-medium text-gray-700 mb-1">อีเมล <span class="text-red-600">*</span></label>
                            <input type="email" id="mp_email" name="mp_email" required
                                class="w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                            <p class="text-sm text-gray-500 mt-1">ใช้เป็นชื่อผู้ใช้งานในการเข้าสู่ระบบ</p>
                        </div>
                        
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            <div>
                                <label for="mp_password" class="block text-lg font-medium text-gray-700 mb-1">รหัสผ่าน <span class="text-red-600">*</span></label>
                                <input type="password" id="mp_password" name="mp_password" required minlength="6"
                                    class="w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                            </div>
                            <div>
                                <label for="mp_password_confirm" class="block text-lg font-medium text-gray-700 mb-1">ยืนยันรหัสผ่าน <span class="text-red-600">*</span></label>
                                <input type="password" id="mp_password_confirm" name="mp_password_confirm" required minlength="6"
                                    class="w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            
            <!-- Personal Information Card -->
            <div class="bg-white border rounded-lg shadow-sm">
                <div class="px-4 py-3 border-b bg-gray-50">
                    <h3 class="text-lg font-medium text-gray-700">ข้อมูลส่วนตัว</h3>
                </div>
                <div class="p-4">
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div>
                            <label for="mp_fname" class="block text-lg font-medium text-gray-700 mb-1">ชื่อ <span class="text-red-600">*</span></label>
                            <input type="text" id="mp_fname" name="mp_fname" required
                                class="w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                        </div>
                        <div>
                            <label for="mp_lname" class="block text-lg font-medium text-gray-700 mb-1">นามสกุล <span class="text-red-600">*</span></label>
                            <input type="text" id="mp_lname" name="mp_lname" required
                                class="w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                        </div>
                        <div>
                            <label for="mp_number" class="block text-lg font-medium text-gray-700 mb-1">เลขบัตรประจำตัวประชาชน <span class="text-red-600">*</span></label>
                            <input type="text" id="mp_number" name="mp_number" required maxlength="13" pattern="[0-9]{13}"
                                class="w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                        </div>
                        <div>
                            <label for="mp_phone" class="block text-lg font-medium text-gray-700 mb-1">เบอร์โทรศัพท์ <span class="text-red-600">*</span></label>
                            <input type="text" id="mp_phone" name="mp_phone" required maxlength="10" pattern="[0-9]{10}"
                                class="w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                        </div>
                        <div class="md:col-span-2">
                            <label for="mp_address" class="block text-lg font-medium text-gray-700 mb-1">ที่อยู่</label>
                            <textarea id="mp_address" name="mp_address" rows="3"
                                class="w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500"></textarea>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Upload Profile Image -->
            <div class="bg-white border rounded-lg shadow-sm">
                <div class="px-4 py-3 border-b bg-gray-50">
                    <h3 class="text-lg font-medium text-gray-700">รูปโปรไฟล์</h3>
                </div>
                <div class="p-4">
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div>
                            <label for="mp_img" class="block text-lg font-medium text-gray-700 mb-1">อัปโหลดรูปโปรไฟล์</label>
                            <input type="file" id="mp_img" name="mp_img" accept="image/*"
                                class="w-full py-2 px-3 border border-gray-300 rounded-md text-sm">
                            <p class="text-sm text-gray-500 mt-1">รองรับไฟล์ภาพ JPG, PNG, GIF ขนาดไม่เกิน 2MB</p>
                        </div>
                        <div class="flex items-center justify-center">
                            <div class="w-32 h-32 bg-gray-100 rounded-full flex items-center justify-center border">
                                <img id="preview-image" src="<?php echo base_url('docs/default_user.png'); ?>" alt="Preview" class="h-full w-full object-cover rounded-full">
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Form Buttons -->
            <div class="flex justify-end space-x-3 border-t pt-6">
                <a href="<?php echo site_url('System_member/member_web_external'); ?>" 
                   class="px-6 py-2.5 bg-gray-300 text-gray-700 rounded-md hover:bg-gray-400 focus:outline-none focus:ring-2 focus:ring-gray-500 focus:ring-offset-2">
                    ยกเลิก
                </a>
                <button type="submit" 
                        class="px-6 py-2.5 bg-blue-600 text-white rounded-md hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2">
                    <i class="fas fa-save mr-2"></i>บันทึกข้อมูล
                </button>
            </div>
        </div>
        <?php echo form_close(); ?>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    // Preview uploaded image
    const fileInput = document.getElementById('mp_img');
    const previewImage = document.getElementById('preview-image');
    
    fileInput.addEventListener('change', function() {
        if (fileInput.files && fileInput.files[0]) {
            const reader = new FileReader();
            
            reader.onload = function(e) {
                previewImage.src = e.target.result;
            }
            
            reader.readAsDataURL(fileInput.files[0]);
        }
    });

    // Form validation
    const form = document.getElementById('add-member-external-form');
    
    form.addEventListener('submit', function(event) {
        const password = document.getElementById('mp_password').value;
        const confirmPassword = document.getElementById('mp_password_confirm').value;
        
        // Check if passwords match
        if (password !== confirmPassword) {
            event.preventDefault();
            Swal.fire({
                icon: 'error',
                title: 'รหัสผ่านไม่ตรงกัน',
                text: 'กรุณาตรวจสอบรหัสผ่านและยืนยันรหัสผ่านอีกครั้ง'
            });
            return;
        }
        
        // Check ID card format
        const idCard = document.getElementById('mp_number').value;
        if (idCard.length !== 13 || !/^\d+$/.test(idCard)) {
            event.preventDefault();
            Swal.fire({
                icon: 'error',
                title: 'เลขบัตรประชาชนไม่ถูกต้อง',
                text: 'กรุณากรอกเลขบัตรประชาชน 13 หลัก'
            });
            return;
        }
        
        // Check phone number format
        const phoneNumber = document.getElementById('mp_phone').value;
        if (phoneNumber.length !== 10 || !/^\d+$/.test(phoneNumber)) {
            event.preventDefault();
            Swal.fire({
                icon: 'error',
                title: 'เบอร์โทรศัพท์ไม่ถูกต้อง',
                text: 'กรุณากรอกเบอร์โทรศัพท์ 10 หลัก'
            });
            return;
        }
    });
    
    // Check for duplicate email
    const emailInput = document.getElementById('mp_email');
    emailInput.addEventListener('blur', function() {
        const email = this.value;
        if (email && /^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(email)) {
            fetch('<?php echo site_url("System_member/check_email_exists_external"); ?>', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/x-www-form-urlencoded',
                    'X-Requested-With': 'XMLHttpRequest'
                },
                body: 'email=' + encodeURIComponent(email)
            })
            .then(response => response.json())
            .then(data => {
                if (data.exists) {
                    Swal.fire({
                        icon: 'warning',
                        title: 'อีเมลซ้ำ',
                        text: 'อีเมลนี้มีในระบบแล้ว กรุณาใช้อีเมลอื่น'
                    });
                    emailInput.focus();
                }
            })
            .catch(error => console.error('Error:', error));
        }
    });
    
    // Check for duplicate ID card
    const idCardInput = document.getElementById('mp_number');
    idCardInput.addEventListener('blur', function() {
        const idCard = this.value;
        if (idCard && idCard.length === 13 && /^\d+$/.test(idCard)) {
            fetch('<?php echo site_url("System_member/check_id_card_exists_external"); ?>', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/x-www-form-urlencoded',
                    'X-Requested-With': 'XMLHttpRequest'
                },
                body: 'id_card=' + encodeURIComponent(idCard)
            })
            .then(response => response.json())
            .then(data => {
                if (data.exists) {
                    Swal.fire({
                        icon: 'warning',
                        title: 'เลขบัตรประชาชนซ้ำ',
                        text: 'เลขบัตรประชาชนนี้มีในระบบแล้ว'
                    });
                    idCardInput.focus();
                }
            })
            .catch(error => console.error('Error:', error));
        }
    });
});
</script>