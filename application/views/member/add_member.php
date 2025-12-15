<!-- add_member_single_page.php -->
<div class="ml-72 p-8">
    <!-- ฟอร์มหลัก -->
    <form id="singlePageForm" method="post" action="<?php echo site_url('System_member/add_member_db'); ?>" 
          enctype="multipart/form-data" class="bg-white rounded-2xl shadow-lg border border-gray-100">
        
        <!-- ส่วนหัว -->
        <div class="p-8 border-b border-gray-100">
            <h2 class="text-2xl font-medium text-gray-800">เพิ่มข้อมูลผู้ใช้</h2>
        </div>

        <div class="p-8 space-y-12">
            <!-- ส่วนที่ 1: ข้อมูลผู้ใช้ -->
            <div class="bg-gray-50 p-6 rounded-xl">
                <h3 class="text-lg font-semibold text-gray-800 mb-6 flex items-center">
                    <span class="w-8 h-8 bg-blue-600 text-white rounded-full flex items-center justify-center mr-3">1</span>
                    ข้อมูลผู้ใช้
                </h3>

                <div class="space-y-6">
                    <!-- ชื่อ-นามสกุล -->
                    <div class="grid grid-cols-2 gap-6">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">
                                ชื่อ <span class="text-rose-400">*</span>
                            </label>
                            <input type="text" name="m_fname" required
                                   class="w-full px-4 py-2 rounded-xl border border-gray-200 focus:ring-2 focus:ring-blue-100 focus:border-blue-300 transition-colors">
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">
                                นามสกุล <span class="text-rose-400">*</span>
                            </label>
                            <input type="text" name="m_lname" required
                                   class="w-full px-4 py-2 rounded-xl border border-gray-200 focus:ring-2 focus:ring-blue-100 focus:border-blue-300 transition-colors">
                        </div>
                    </div>

                    <!-- อีเมล -->
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">
                            อีเมล <span class="text-rose-400">*</span>
                            <span class="text-sm text-gray-500">(ใช้เป็นชื่อผู้ใช้งานสำหรับเข้าสู่ระบบ)</span>
                        </label>
                        <input type="email" name="m_username" id="m_username" required
                               class="w-full px-4 py-2 rounded-xl border border-gray-200 focus:ring-2 focus:ring-blue-100 focus:border-blue-300 transition-colors"
                               onblur="validateAndCheckEmail(this.value)" 
                               oninput="validateAndCheckEmail(this.value)"
                               pattern="[A-Za-z0-9._%+\-]+@[A-Za-z0-9.\-]+\.[A-Za-z]{2,}$">
                        <div id="email-validation-status" class="mt-1 text-sm"></div>
                        <div id="email-availability-status" class="mt-1 text-sm"></div>
                    </div>

                    <!-- รหัสผ่าน -->
                    <div class="grid grid-cols-2 gap-6">
                        <div class="relative">
                            <label class="block text-sm font-medium text-gray-700 mb-2">
                                รหัสผ่าน <span class="text-rose-400">*</span>
                            </label>
                            <div class="relative">
                                <input type="password" id="password" name="m_password" required
                                       autocomplete="new-password"
                                       readonly
                                       onfocus="this.removeAttribute('readonly');"
                                       class="w-full px-4 py-2 rounded-xl border border-gray-200 focus:ring-2 focus:ring-blue-100 focus:border-blue-300 transition-colors pr-10">
                                <button type="button" onclick="togglePassword('password')"
                                        class="absolute inset-y-0 right-0 px-3 flex items-center">
                                    <i class="fas fa-eye text-gray-400"></i>
                                </button>
                            </div>
                        </div>
                        <div class="relative">
                            <label class="block text-sm font-medium text-gray-700 mb-2">
                                ยืนยันรหัสผ่าน <span class="text-rose-400">*</span>
                            </label>
                            <div class="relative">
                                <input type="password" id="confirm_password" name="confirm_password" required
                                       autocomplete="new-password"
                                       readonly
                                       onfocus="this.removeAttribute('readonly');"
                                       class="w-full px-4 py-2 rounded-xl border border-gray-200 focus:ring-2 focus:ring-blue-100 focus:border-blue-300 transition-colors pr-10">
                                <button type="button" onclick="togglePassword('confirm_password')"
                                        class="absolute inset-y-0 right-0 px-3 flex items-center">
                                    <i class="fas fa-eye text-gray-400"></i>
                                </button>
                            </div>
                        </div>
                    </div>

                    <!-- เบอร์โทร -->
                    <div class="grid grid-cols-2 gap-6">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">เบอร์โทร</label>
                            <input type="tel" name="m_phone"
                                   class="w-full px-4 py-2 rounded-xl border border-gray-200 focus:ring-2 focus:ring-blue-100 focus:border-blue-300 transition-colors">
                        </div>
                    </div>

                    <!-- สิทธิ์การใช้งาน -->
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-4">
                            สิทธิ์การเข้าใช้งาน <span class="text-rose-400">*</span>
                        </label>
                        
                        <!-- ข้อความอธิบายสิทธิ์ -->
                        <div class="mb-4 p-3 bg-blue-50 border border-blue-200 rounded-lg">
                            <div class="flex items-start space-x-2">
                                <i class="fas fa-info-circle text-blue-500 mt-0.5"></i>
                                <div class="text-sm text-blue-700">
                                    <strong>หมายเหตุ:</strong> บุคลากรภายใน (End User) จะถูกจำกัดการเข้าถึงระบบจัดการสมาชิกและระบบจัดการเว็บไซต์เพื่อความปลอดภัย
                                </div>
                            </div>
                        </div>
                        
                        <div class="grid grid-cols-2 gap-4 p-4 bg-gray-50 rounded-lg">
                            <?php if ($is_system_admin): ?>
                                <div class="p-4 rounded-xl border border-gray-200 hover:border-blue-300 cursor-pointer transition-colors">
                                    <label class="flex items-start cursor-pointer">
                                        <input type="radio" name="m_system" value="system_admin" class="mt-1" onchange="togglePositionSelect(this.value)">
                                        <div class="ml-3">
                                            <span class="block font-medium text-gray-900">ผู้ดูแลระบบสูงสุด</span>
                                            <span class="text-sm text-gray-500">System Admin</span>
                                        </div>
                                    </label>
                                </div>
                            <?php endif; ?>

                            <div class="p-4 rounded-xl border border-gray-200 hover:border-blue-300 cursor-pointer transition-colors">
                                <label class="flex items-start cursor-pointer">
                                    <input type="radio" name="m_system" value="super_admin" class="mt-1" onchange="togglePositionSelect(this.value)">
                                    <div class="ml-3">
                                        <span class="block font-medium text-gray-900">ผู้ดูแลระบบ</span>
                                        <span class="text-sm text-gray-500">Super Admin</span>
                                    </div>
                                </label>
                            </div>

                            <div class="p-4 rounded-xl border border-gray-200 hover:border-blue-300 cursor-pointer transition-colors">
                                <label class="flex items-start cursor-pointer">
                                    <input type="radio" name="m_system" value="user_admin" class="mt-1" onchange="togglePositionSelect(this.value)">
                                    <div class="ml-3">
                                        <span class="block font-medium text-gray-900">เจ้าหน้าที่</span>
                                        <span class="text-sm text-gray-500">User Admin ให้จัดการแค่บางส่วน</span>
                                    </div>
                                </label>
                            </div>

                            <div class="p-4 rounded-xl border border-gray-200 hover:border-blue-300 cursor-pointer transition-colors">
                                <label class="flex items-start cursor-pointer">
                                    <input type="radio" name="m_system" value="end_user" class="mt-1" onchange="togglePositionSelect(this.value)">
                                    <div class="ml-3">
                                        <span class="block font-medium text-gray-900">บุคลากรภายใน</span>
                                        <span class="text-sm text-gray-500">End User บุคลากรภายในองค์กร</span>
                                        <span class="text-xs text-orange-600 block mt-1">
                                            <i class="fas fa-info-circle mr-1"></i>
                                            จำกัดการเข้าถึงระบบจัดการสมาชิกและเว็บไซต์
                                        </span>
                                    </div>
                                </label>
                            </div>
                        </div>

                        <!-- ส่วนเลือกตำแหน่ง -->
                        <div id="position-select-container" class="mt-4 hidden">
                            <label class="block text-sm font-medium text-gray-700 mb-2">
                                ตำแหน่ง <span class="text-rose-400">*</span>
                            </label>
                            <select name="ref_pid" id="ref_pid" class="w-full px-4 py-2 rounded-xl border border-gray-200 focus:ring-2 focus:ring-blue-100 focus:border-blue-300 transition-colors">
                                <option value="">-- เลือกตำแหน่ง --</option>
                                <?php foreach ($positions as $position): ?>
                                    <?php if ($position->pid > 3 && $position->pstatus == 'show'): ?>
                                        <option value="<?php echo $position->pid; ?>"><?php echo $position->pname; ?></option>
                                    <?php endif; ?>
                                <?php endforeach; ?>
                            </select>
                        </div>
                    </div>

                    <!-- รูปโปรไฟล์ -->
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-4">รูปโปรไฟล์</label>
                        
                        <!-- ส่วนแสดงตัวอย่างรูปโปรไฟล์ -->
                        <div class="flex items-center space-x-6 mb-6">
                            <div class="w-24 h-24 rounded-full overflow-hidden bg-gray-100 border-4 border-white shadow-lg">
                                <img id="preview" 
                                     src="<?php echo base_url('docs/img/avatar/default_user.png'); ?>"
                                     class="w-full h-full object-cover">
                            </div>
                            <div>
                                <label class="cursor-pointer mb-2 block">
                                    <span class="px-4 py-2 bg-blue-50 text-blue-600 rounded-lg hover:bg-blue-100 border border-blue-200 transition-colors inline-block">
                                        <i class="fas fa-upload mr-2"></i>อัปโหลดรูปภาพ
                                    </span>
                                    <input type="file" name="m_img" id="imageUpload" class="hidden" onchange="previewImage(this)"
                                           accept="image/png, image/jpeg, image/gif, image/jpg, image/webp">
                                </label>
                                <p class="text-sm text-gray-500">หรือเลือกจาก Avatar ด้านล่าง</p>
                                <p class="text-xs text-gray-400 mt-1">รองรับไฟล์: JPG, PNG, GIF, WEBP (ขนาดไม่เกิน 5MB)</p>
                            </div>
                        </div>
                        
                        <!-- ส่วนเลือก Avatar -->
                        <div class="mt-4">
                            <h3 class="text-sm font-medium text-gray-700 mb-3">เลือก Avatar อักษรย่อ</h3>
                            <div class="grid grid-cols-5 gap-4 mb-6">
                                <!-- Avatar รูปแบบเป็นทางการที่ใช้อักษรจาก email (โทนสีทางการ) -->
                                <div class="avatar-item cursor-pointer rounded-lg p-2 border border-transparent hover:border-blue-300 transition-colors" 
                                     onclick="selectAvatar('https://ui-avatars.com/api/?name=AA&background=17202A&color=fff&size=200&format=svg&bold=true&font-size=0.33')">
                                    <img src="https://ui-avatars.com/api/?name=AA&background=17202A&color=fff&size=200&format=svg&bold=true&font-size=0.33" 
                                         class="w-16 h-16 mx-auto rounded-full object-cover email-avatar">
                                </div>
                                
                                <div class="avatar-item cursor-pointer rounded-lg p-2 border border-transparent hover:border-blue-300 transition-colors" 
                                     onclick="selectAvatar('https://ui-avatars.com/api/?name=AA&background=1A5276&color=fff&size=200&format=svg&bold=true&font-size=0.33')">
                                    <img src="https://ui-avatars.com/api/?name=AA&background=1A5276&color=fff&size=200&format=svg&bold=true&font-size=0.33" 
                                         class="w-16 h-16 mx-auto rounded-full object-cover email-avatar">
                                </div>
                                
                                <div class="avatar-item cursor-pointer rounded-lg p-2 border border-transparent hover:border-blue-300 transition-colors" 
                                     onclick="selectAvatar('https://ui-avatars.com/api/?name=AA&background=145A32&color=fff&size=200&format=svg&bold=true&font-size=0.33')">
                                    <img src="https://ui-avatars.com/api/?name=AA&background=145A32&color=fff&size=200&format=svg&bold=true&font-size=0.33" 
                                         class="w-16 h-16 mx-auto rounded-full object-cover email-avatar">
                                </div>
                                
                                <div class="avatar-item cursor-pointer rounded-lg p-2 border border-transparent hover:border-blue-300 transition-colors" 
                                     onclick="selectAvatar('https://ui-avatars.com/api/?name=AA&background=7D6608&color=fff&size=200&format=svg&bold=true&font-size=0.33')">
                                    <img src="https://ui-avatars.com/api/?name=AA&background=7D6608&color=fff&size=200&format=svg&bold=true&font-size=0.33" 
                                         class="w-16 h-16 mx-auto rounded-full object-cover email-avatar">
                                </div>
                                
                                <div class="avatar-item cursor-pointer rounded-lg p-2 border border-transparent hover:border-blue-300 transition-colors" 
                                     onclick="selectAvatar('https://ui-avatars.com/api/?name=AA&background=7B7D7D&color=fff&size=200&format=svg&bold=true&font-size=0.33')">
                                    <img src="https://ui-avatars.com/api/?name=AA&background=7B7D7D&color=fff&size=200&format=svg&bold=true&font-size=0.33" 
                                         class="w-16 h-16 mx-auto rounded-full object-cover email-avatar">
                                </div>
                            </div>
                            
                            <h3 class="text-sm font-medium text-gray-700 mb-3">Avatar อื่นๆ</h3>
                            
                            <div class="grid grid-cols-5 gap-4">
                                <!-- แถวที่ 1: สัญลักษณ์ราชการแบบเป็นทางการ -->
                                <div class="avatar-item cursor-pointer rounded-lg p-2 border border-transparent hover:border-blue-300 transition-colors" 
                                     onclick="selectAvatar('https://cdn-icons-png.flaticon.com/512/4140/4140047.png')">
                                    <img src="https://cdn-icons-png.flaticon.com/512/4140/4140047.png" 
                                         class="w-16 h-16 mx-auto rounded-full object-cover bg-white">
                                </div>

                                <div class="avatar-item cursor-pointer rounded-lg p-2 border border-transparent hover:border-blue-300 transition-colors" 
                                     onclick="selectAvatar('https://cdn-icons-png.flaticon.com/512/4140/4140051.png')">
                                    <img src="https://cdn-icons-png.flaticon.com/512/4140/4140051.png" 
                                         class="w-16 h-16 mx-auto rounded-full object-cover bg-white">
                                </div>

                                <div class="avatar-item cursor-pointer rounded-lg p-2 border border-transparent hover:border-blue-300 transition-colors" 
                                     onclick="selectAvatar('https://cdn-icons-png.flaticon.com/512/4202/4202840.png')">
                                    <img src="https://cdn-icons-png.flaticon.com/512/4202/4202840.png" 
                                         class="w-16 h-16 mx-auto rounded-full object-cover bg-white">
                                </div>

                                <div class="avatar-item cursor-pointer rounded-lg p-2 border border-transparent hover:border-blue-300 transition-colors" 
                                     onclick="selectAvatar('https://cdn-icons-png.flaticon.com/512/3048/3048122.png')">
                                    <img src="https://cdn-icons-png.flaticon.com/512/3048/3048122.png" 
                                         class="w-16 h-16 mx-auto rounded-full object-cover bg-white">
                                </div>

                                <div class="avatar-item cursor-pointer rounded-lg p-2 border border-transparent hover:border-blue-300 transition-colors" 
                                     onclick="selectAvatar('https://cdn-icons-png.flaticon.com/512/2922/2922510.png')">
                                    <img src="https://cdn-icons-png.flaticon.com/512/2922/2922510.png" 
                                         class="w-16 h-16 mx-auto rounded-full object-cover bg-white">
                                </div>
                                
                                <div class="avatar-item cursor-pointer rounded-lg p-2 border border-transparent hover:border-blue-300 transition-colors" 
                                     onclick="selectAvatar('https://cdn-icons-png.flaticon.com/512/3135/3135715.png')">
                                    <img src="https://cdn-icons-png.flaticon.com/512/3135/3135715.png" 
                                         class="w-16 h-16 mx-auto rounded-full object-cover bg-white">
                                </div>
                                
                                <div class="avatar-item cursor-pointer rounded-lg p-2 border border-transparent hover:border-blue-300 transition-colors" 
                                     onclick="selectAvatar('https://cdn-icons-png.flaticon.com/512/2830/2830175.png')">
                                    <img src="https://cdn-icons-png.flaticon.com/512/2830/2830175.png" 
                                         class="w-16 h-16 mx-auto rounded-full object-cover bg-white">
                                </div>
                                
                                <div class="avatar-item cursor-pointer rounded-lg p-2 border border-transparent hover:border-blue-300 transition-colors" 
                                     onclick="selectAvatar('https://cdn-icons-png.flaticon.com/512/5599/5599510.png')">
                                    <img src="https://cdn-icons-png.flaticon.com/512/5599/5599510.png" 
                                         class="w-16 h-16 mx-auto rounded-full object-cover bg-white">
                                </div>
                                
                                <div class="avatar-item cursor-pointer rounded-lg p-2 border border-transparent hover:border-blue-300 transition-colors" 
                                     onclick="selectAvatar('https://cdn-icons-png.flaticon.com/512/2232/2232688.png')">
                                    <img src="https://cdn-icons-png.flaticon.com/512/2232/2232688.png" 
                                         class="w-16 h-16 mx-auto rounded-full object-cover bg-white">
                                </div>
                                
                                <div class="avatar-item cursor-pointer rounded-lg p-2 border border-transparent hover:border-blue-300 transition-colors" 
                                     onclick="selectAvatar('https://cdn-icons-png.flaticon.com/512/2966/2966486.png')">
                                    <img src="https://cdn-icons-png.flaticon.com/512/2966/2966486.png" 
                                         class="w-16 h-16 mx-auto rounded-full object-cover bg-white">
                                </div>
                                
                                <div class="avatar-item cursor-pointer rounded-lg p-2 border border-transparent hover:border-blue-300 transition-colors" 
                                     onclick="selectAvatar('https://cdn-icons-png.flaticon.com/512/4207/4207247.png')">
                                    <img src="https://cdn-icons-png.flaticon.com/512/4207/4207247.png" 
                                         class="w-16 h-16 mx-auto rounded-full object-cover bg-white">
                                </div>
                                
                                <div class="avatar-item cursor-pointer rounded-lg p-2 border border-transparent hover:border-blue-300 transition-colors" 
                                     onclick="selectAvatar('https://cdn-icons-png.flaticon.com/512/1584/1584961.png')">
                                    <img src="https://cdn-icons-png.flaticon.com/512/1584/1584961.png" 
                                         class="w-16 h-16 mx-auto rounded-full object-cover bg-white">
                                </div>
                          
                                <div class="avatar-item cursor-pointer rounded-lg p-2 border border-transparent hover:border-blue-300 transition-colors" 
                                     onclick="selectAvatar('https://cdn-icons-png.flaticon.com/512/4333/4333609.png')">
                                    <img src="https://cdn-icons-png.flaticon.com/512/4333/4333609.png" 
                                         class="w-16 h-16 mx-auto rounded-full object-cover bg-white">
                                </div>
                                
                                <div class="avatar-item cursor-pointer rounded-lg p-2 border border-transparent hover:border-blue-300 transition-colors" 
                                     onclick="selectAvatar('https://cdn-icons-png.flaticon.com/512/4333/4333640.png')">
                                    <img src="https://cdn-icons-png.flaticon.com/512/4333/4333640.png" 
                                         class="w-16 h-16 mx-auto rounded-full object-cover bg-white">
                                </div>
                                
                                <div class="avatar-item cursor-pointer rounded-lg p-2 border border-transparent hover:border-blue-300 transition-colors" 
                                     onclick="selectAvatar('https://cdn-icons-png.flaticon.com/512/2202/2202112.png')">
                                    <img src="https://cdn-icons-png.flaticon.com/512/2202/2202112.png" 
                                         class="w-16 h-16 mx-auto rounded-full object-cover bg-white">
                                </div>
                                
                                <div class="avatar-item cursor-pointer rounded-lg p-2 border border-transparent hover:border-blue-300 transition-colors" 
                                     onclick="selectAvatar('https://cdn-icons-png.flaticon.com/512/4086/4086679.png')">
                                    <img src="https://cdn-icons-png.flaticon.com/512/4086/4086679.png" 
                                         class="w-16 h-16 mx-auto rounded-full object-cover bg-white">
                                </div>
                                
                                <div class="avatar-item cursor-pointer rounded-lg p-2 border border-transparent hover:border-blue-300 transition-colors" 
                                     onclick="selectAvatar('https://cdn-icons-png.flaticon.com/512/560/560216.png')">
                                    <img src="https://cdn-icons-png.flaticon.com/512/560/560216.png" 
                                         class="w-16 h-16 mx-auto rounded-full object-cover bg-white">
                                </div>
                                
                                <div class="avatar-item cursor-pointer rounded-lg p-2 border border-transparent hover:border-blue-300 transition-colors" 
                                     onclick="selectAvatar('https://cdn-icons-png.flaticon.com/512/560/560277.png')">
                                    <img src="https://cdn-icons-png.flaticon.com/512/560/560277.png" 
                                         class="w-16 h-16 mx-auto rounded-full object-cover bg-white">
                                </div>
                                
                                <div class="avatar-item cursor-pointer rounded-lg p-2 border border-transparent hover:border-blue-300 transition-colors" 
                                     onclick="selectAvatar('https://cdn-icons-png.flaticon.com/512/2138/2138918.png')">
                                    <img src="https://cdn-icons-png.flaticon.com/512/2138/2138918.png" 
                                         class="w-16 h-16 mx-auto rounded-full object-cover bg-white">
                                </div>
                                
                                <div class="avatar-item cursor-pointer rounded-lg p-2 border border-transparent hover:border-blue-300 transition-colors" 
                                     onclick="selectAvatar('https://cdn-icons-png.flaticon.com/512/2138/2138926.png')">
                                    <img src="https://cdn-icons-png.flaticon.com/512/2138/2138926.png" 
                                         class="w-16 h-16 mx-auto rounded-full object-cover bg-white">
                                </div>
                                
                                <div class="avatar-item cursor-pointer rounded-lg p-2 border border-transparent hover:border-blue-300 transition-colors" 
                                     onclick="selectAvatar('https://cdn-icons-png.flaticon.com/512/4748/4748894.png')">
                                    <img src="https://cdn-icons-png.flaticon.com/512/4748/4748894.png" 
                                         class="w-16 h-16 mx-auto rounded-full object-cover bg-white">
                                </div>
                            </div>
                        </div>

                        <!-- Hidden input to store avatar URL -->
                        <input type="hidden" name="avatar_url" id="avatarUrl">
                    </div>
                </div>
            </div>

            <!-- ส่วนที่ 2+: ระบบต่างๆ -->
            <?php 
            $stepCount = 2;
            foreach ($modules as $module): 
                if ($module->status == 1 && $module->is_trial == 0 && $module->id != 11):
                // ตรวจสอบว่าไม่ใช่ระบบที่ถูกจำกัดสำหรับ end_user หรือถ้าเป็นต้องไม่ใช่ end_user
                $moduleClass = '';
                if ($module->id == 1) $moduleClass = 'member-management-section';
                if ($module->id == 2) $moduleClass = 'website-management-section';
            ?>
                <div class="bg-gray-50 p-6 rounded-xl <?php echo $moduleClass; ?>" data-module-id="<?php echo $module->id; ?>">
                    <h3 class="text-lg font-semibold text-gray-800 mb-6 flex items-center">
                        <span class="w-8 h-8 bg-blue-600 text-white rounded-full flex items-center justify-center mr-3"><?php echo $stepCount; ?></span>
                        <?php echo $module->name; ?>
                    </h3>
                    
                    <div class="space-y-6">
                        <!-- Checkbox หลักควบคุมการเปิดใช้งาน -->
                        <div class="flex items-center space-x-2 mb-6">
                            <input type="checkbox" 
                                   id="module_<?php echo $module->id; ?>"
                                   name="grant_system_ref_id[]" 
                                   value="<?php echo $module->id; ?>"
                                   onchange="toggleMenuCheckboxes(<?php echo $module->id; ?>)"
                                   <?php echo ($module->id == 3 || $module->id == 17) ? 'checked' : ''; ?>
                                   class="form-checkbox h-5 w-5 text-blue-600">
                            <label class="font-medium text-gray-700">
                                เปิดใช้งาน <?php echo $module->name; ?>
                                <?php if ($module->id == 3 || $module->id == 17): ?>
                                    <span class="text-xs text-green-600 ml-2">
                                        <i class="fas fa-check-circle mr-1"></i>
                                        (เลือกโดยอัตโนมัติ)
                                    </span>
                                <?php endif; ?>
                            </label>
                        </div>

                        <?php if ($module->id == 2): // โมดูลจัดการเว็บไซต์ ?>
                            <div class="web-permissions p-4 bg-white rounded-lg mt-4 border" style="display: none;">
                                <h4 class="text-md font-medium mb-4">กำหนดสิทธิ์การจัดการเว็บไซต์</h4>
                                <div class="grid grid-cols-3 gap-4">
                                    <?php 
                                    // กำหนดรายการที่ต้องการให้เป็นตัวหนา
                                    $bold_grant_ids = [50, 53, 105, 106, 108, 107, 109, 125];
                                    
                                    foreach ($grant_users as $grant): 
                                        $is_bold = in_array($grant->grant_user_id, $bold_grant_ids);
                                        $text_class = $is_bold ? 'font-bold text-blue-800' : 'text-gray-700';
                                        
                                        // เช็คว่าเป็น checkbox "ทั้งหมด" หรือไม่
                                        $is_all_access = ($grant->grant_user_id == 1);
                                    ?>
                                        <label class="flex items-center p-3 bg-gray-50 rounded-lg border hover:border-blue-300 transition-colors">
                                            <input type="checkbox" 
                                                   name="grant_user_ids[]" 
                                                   value="<?php echo $grant->grant_user_id; ?>"
                                                   <?php echo $is_all_access ? 'data-all="true"' : ''; ?>
                                                   class="grant-user-checkbox form-checkbox h-4 w-4 text-blue-500 rounded"
                                                   onchange="handleGrantUserChange(this)"
                                                   disabled>
                                            <span class="ml-2 <?php echo $text_class; ?>">
                                                <?php echo $grant->grant_user_name; ?>
                                                <?php if ($is_all_access): ?>
                                                    <span class="text-xs text-blue-600 block">(รวมสิทธิ์สำคัญทั้งหมด)</span>
                                                <?php endif; ?>
                                            </span>
                                        </label>
                                    <?php endforeach; ?>
                                </div>
                                
                                <!-- เพิ่มข้อความช่วยเหลือ -->
                                <div class="mt-4 p-3 bg-blue-50 rounded-lg border border-blue-200">
                                    <div class="flex items-start space-x-2">
                                        <i class="fas fa-info-circle text-blue-500 mt-0.5"></i>
                                        <div class="text-sm text-blue-700">
                                            <p><strong>หมายเหตุ:</strong></p>
                                            <ul class="mt-1 list-disc list-inside space-y-1">
                                                <li>เมื่อเลือก <strong>"ทั้งหมด"</strong> จะได้รับสิทธิ์หลักทั้งหมด (ID: 1,50,53,105,106,107,108,109,125)</li>
                                                <li>สิทธิ์ที่เป็น<strong class="text-blue-800">ตัวหนา</strong>จะถูกเลือกโดยอัตโนมัติ</li>
                                                <li>สิทธิ์อื่นๆ จะถูกปิดการใช้งานเมื่อเลือก "ทั้งหมด"</li>
                                            </ul>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        <?php endif; ?>

                        <!-- Grid แสดงเมนูย่อย -->
                        <div class="menu-grid-<?php echo $module->id; ?> mt-4" <?php echo ($module->id == 3 || $module->id == 17) ? 'style="display: block;"' : 'style="display: none;"'; ?>>
                            <h4 class="text-md font-medium mb-3">สิทธิ์การใช้งานเมนู</h4>
                            <div class="grid grid-cols-3 gap-3 p-4 bg-white rounded-lg border">
                                <?php 
                                // ดึงรายการเมนูย่อยของโมดูล
                                $module_menus = $this->db->select('*')
                                                       ->from('tbl_member_module_menus')
                                                       ->where('module_id', $module->id)
                                                       ->where('status', 1)
                                                       ->order_by('display_order', 'ASC')
                                                       ->get()
                                                       ->result();

                                foreach($module_menus as $menu): 
                                ?>
                                    <div class="flex items-center space-x-2 p-2 rounded hover:bg-gray-50">
                                        <input type="checkbox" 
                                               id="menu_<?php echo $menu->id; ?>"
                                               name="module_menu_ids[]" 
                                               value="<?php echo $menu->id; ?>"
                                               class="menu-checkbox-<?php echo $module->id; ?> form-checkbox h-4 w-4 text-blue-500"
                                               <?php echo ($module->id == 3 || $module->id == 17) ? 'checked' : 'disabled'; ?>>
                                        <label for="menu_<?php echo $menu->id; ?>" class="text-sm text-gray-600 cursor-pointer">
                                            <?php echo $menu->name; ?>
                                            <?php if ($module->id == 3 || $module->id == 17): ?>
                                                <span class="text-xs text-green-600 ml-1">
                                                    <i class="fas fa-check text-xs"></i>
                                                </span>
                                            <?php endif; ?>
                                        </label>
                                    </div>
                                <?php endforeach; ?>
                            </div>
                            
                            <?php if ($module->id == 3): ?>
                                <!-- ข้อความแจ้งเตือนสำหรับ Back Office -->
                                <div class="mt-4 p-3 bg-green-50 rounded-lg border border-green-200">
                                    <div class="flex items-start space-x-2">
                                        <i class="fas fa-info-circle text-green-500 mt-0.5"></i>
                                        <div class="text-sm text-green-700">
                                            <p><strong>ระบบ Back Office:</strong></p>
                                            <ul class="mt-1 list-disc list-inside space-y-1">
                                                <li>ระบบนี้จำเป็นสำหรับการทำงานพื้นฐานของผู้ใช้งานทุกคน</li>
                                                <li>ได้รับการเลือกและเปิดใช้งานโดยอัตโนมัติ</li>
                                                <li>คุณสามารถยกเลิกการเลือกได้หากไม่ต้องการใช้งาน</li>
                                            </ul>
                                        </div>
                                    </div>
                                </div>
                            <?php endif; ?>
                            
                            <?php if ($module->id == 17): ?>
                                <!-- ข้อความแจ้งเตือนสำหรับกระดานสนทนา -->
                                <div class="mt-4 p-3 bg-purple-50 rounded-lg border border-purple-200">
                                    <div class="flex items-start space-x-2">
                                        <i class="fas fa-comments text-purple-500 mt-0.5"></i>
                                        <div class="text-sm text-purple-700">
                                            <p><strong>กระดานสนทนาภายในเครือข่าย:</strong></p>
                                            <ul class="mt-1 list-disc list-inside space-y-1">
                                                <li>ใช้สำหรับการสื่อสารและพูดคุยภายในเครือข่าย องค์กร อบต. เทศบาล</li>
                                                <li>ได้รับการเลือกและเปิดใช้งานโดยอัตโนมัติเพื่อให้ทุกคนสื่อสารได้</li>
                                                <li>ส่งเสริมการทำงานและสื่อสารร่วมกันภายในเครือข่าย</li>
                                                <li>คุณสามารถยกเลิกการเลือกได้หากไม่ต้องการใช้งาน</li>
                                            </ul>
                                        </div>
                                    </div>
                                </div>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>
            <?php 
                    $stepCount++;
                endif;
            endforeach; 
            ?>
            
            <!-- Hidden input สำหรับ Module 11 (ให้สิทธิ์อัตโนมัติ) -->
            <input type="hidden" name="grant_system_ref_id[]" value="11">
            <!-- Hidden inputs สำหรับเมนูย่อยของ Module 11 (ถ้าต้องการ) -->
            <?php
            // เพิ่มสิทธิ์เมนูย่อยของ Module 11 โดยอัตโนมัติ
            $module_11_menus = $this->db->select('id')
                                      ->from('tbl_member_module_menus')
                                      ->where('module_id', 11)
                                      ->where('status', 1)
                                      ->get()
                                      ->result();
            
            foreach($module_11_menus as $menu_11): 
            ?>
                <input type="hidden" name="module_menu_ids[]" value="<?php echo $menu_11->id; ?>">
            <?php endforeach; ?>
        </div>

        <!-- ปุ่มด้านล่าง -->
        <div class="p-8 bg-gray-50 border-t border-gray-100 flex justify-end space-x-4">
            <button type="button" onclick="window.location.href='<?php echo site_url('System_member/member_web'); ?>'"
                    class="px-6 py-2 bg-gray-100 text-gray-700 rounded-lg hover:bg-gray-200 transition-colors">
                ยกเลิก
            </button>
            <button type="submit" id="saveBtn"
                    class="px-6 py-2 bg-blue-500 text-white rounded-lg hover:bg-blue-600 transition-colors disabled:opacity-50 disabled:cursor-not-allowed">
                <i class="fas fa-save mr-2"></i>บันทึกข้อมูล
            </button>
        </div>
    </form>
</div>

<!-- JavaScript ที่ปรับปรุงแล้ว -->
<script>
// Global Variables
const site_url = '<?php echo site_url(); ?>';
var systemModules = <?php echo json_encode($modules); ?>;
var emailCheckTimeout;
let isSubmitting = false;

// Document Ready
document.addEventListener('DOMContentLoaded', function() {
    // Initialize
    initializeUserTypeSelection();
    initializeModuleCheckboxes();
    initializeAutoEnabledModules(); // เปลี่ยนชื่อฟังก์ชันเพื่อรองรับทั้ง module 3 และ 17
    
    // Form Submit Handler
    const form = document.getElementById('singlePageForm');
    form.addEventListener('submit', function(e) {
        e.preventDefault();
        
        if (validateForm()) {
            submitForm(this);
        }
    });
    
    // Initialize email avatar
    const emailInput = document.querySelector('input[name="m_username"]');
    if (emailInput) {
        emailInput.addEventListener('input', updateAvatarsFromEmail);
        emailInput.addEventListener('blur', updateAvatarsFromEmail);
    }
});

// Validation Functions
function validateForm() {
    clearValidationHighlights();
    
    // Validate required fields
    const requiredFields = [
        { name: 'm_fname', label: 'ชื่อ' },
        { name: 'm_lname', label: 'นามสกุล' },
        { name: 'm_username', label: 'อีเมล' },
        { name: 'm_password', label: 'รหัสผ่าน' },
        { name: 'confirm_password', label: 'ยืนยันรหัสผ่าน' }
    ];
    
    for (const field of requiredFields) {
        const input = document.querySelector(`[name="${field.name}"]`);
        if (!input || !input.value.trim()) {
            highlightErrorField(input);
            Swal.fire({
                icon: 'warning',
                title: 'กรุณากรอกข้อมูลให้ครบถ้วน',
                text: `กรุณากรอก${field.label}`,
                didClose: () => {
                    if (input) {
                        input.focus();
                        input.scrollIntoView({ behavior: 'smooth', block: 'center' });
                    }
                }
            });
            return false;
        }
    }

    // Validate email format
    const emailInput = document.querySelector('input[name="m_username"]');
    if (emailInput) {
        const emailRegex = /^[a-zA-Z0-9._\%+\-]+@[a-zA-Z0-9.\-]+\.[a-zA-Z]{2,}$/;
        if (!emailRegex.test(emailInput.value)) {
            highlightErrorField(emailInput);
            Swal.fire({
                icon: 'warning',
                title: 'รูปแบบอีเมลไม่ถูกต้อง',
                text: 'กรุณาใส่อีเมลให้ถูกรูปแบบ เช่น example@domain.com'
            });
            return false;
        }
    }

    // Validate password match
    const password = document.getElementById('password').value;
    const confirmPassword = document.getElementById('confirm_password').value;
    
    if (password !== confirmPassword) {
        highlightErrorField(document.getElementById('confirm_password'));
        Swal.fire({
            icon: 'warning',
            title: 'รหัสผ่านไม่ตรงกัน',
            text: 'กรุณากรอกรหัสผ่านให้ตรงกันทั้งสองช่อง'
        });
        return false;
    }
    
    if (password.length < 6) {
        highlightErrorField(document.getElementById('password'));
        Swal.fire({
            icon: 'warning',
            title: 'รหัสผ่านสั้นเกินไป',
            text: 'รหัสผ่านต้องมีความยาวอย่างน้อย 6 ตัวอักษร'
        });
        return false;
    }

    // Validate system type
    const selectedUserType = document.querySelector('input[name="m_system"]:checked');
    if (!selectedUserType) {
        Swal.fire({
            icon: 'warning',
            title: 'กรุณาเลือกสิทธิ์การใช้งาน',
            text: 'กรุณาเลือกสิทธิ์การเข้าใช้งานระบบ'
        });
        return false;
    }

    // Check position for non-system_admin
    if (selectedUserType.value !== 'system_admin') {
        const positionSelect = document.getElementById('ref_pid');
        if (!positionSelect.value) {
            highlightErrorField(positionSelect);
            Swal.fire({
                icon: 'warning',
                title: 'กรุณาเลือกตำแหน่ง',
                text: 'กรุณาเลือกตำแหน่งสำหรับผู้ใช้งาน'
            });
            return false;
        }
    }

    // Validate website module permissions
    const websiteModuleCheckbox = document.getElementById('module_2');
    if (websiteModuleCheckbox && websiteModuleCheckbox.checked) {
        const checkedGrantUsers = document.querySelectorAll('.grant-user-checkbox:checked');
        if (checkedGrantUsers.length === 0) {
            Swal.fire({
                icon: 'warning',
                title: 'กรุณาเลือกสิทธิ์การจัดการเว็บไซต์',
                text: 'โปรดเลือกอย่างน้อย 1 รายการ'
            });
            return false;
        }
    }

    return true;
}

// Submit Form Function
async function submitForm(form) {
    if (isSubmitting) return;
    isSubmitting = true;
    
    const saveBtn = document.getElementById('saveBtn');
    const originalText = saveBtn.innerHTML;
    
    // แสดง loading ที่ปุ่ม
    saveBtn.disabled = true;
    saveBtn.innerHTML = '<i class="fas fa-spinner fa-spin mr-2"></i>กำลังบันทึก...';
    
    const formData = new FormData(form);
    
    try {
        // ตั้งเวลา timeout
        const controller = new AbortController();
        const timeoutId = setTimeout(() => controller.abort(), 30000); // 30 วินาที
        
        const response = await fetch(form.action, {
            method: 'POST',
            body: formData,
            signal: controller.signal
        });
        
        clearTimeout(timeoutId);

        // ตรวจสอบ Content-Type ของการตอบกลับ
        const contentType = response.headers.get('content-type');
        
        console.log('Response status:', response.status);
        console.log('Content-Type:', contentType);
        
        // กรณีได้รับ HTML กลับมา (redirect หรือ error page)
        if (contentType && contentType.includes('text/html')) {
            if (response.ok) {
                await Swal.fire({
                    icon: 'success',
                    title: 'สำเร็จ!',
                    text: 'เพิ่มข้อมูลสมาชิกเรียบร้อยแล้ว',
                    timer: 1500,
                    showConfirmButton: false
                });
                
                window.location.href = `${site_url}/System_member/member_web`;
                return;
            } else {
                throw new Error(`เกิดข้อผิดพลาด: HTTP status ${response.status}`);
            }
        }

        // กรณีได้รับ JSON
        let result;
        try {
            result = await response.json();
            console.log('JSON Response:', result);
        } catch (jsonError) {
            console.log('JSON Parse Error:', jsonError);
            
            // ถ้า HTTP OK แต่ parse JSON ไม่ได้ ให้ถือว่าสำเร็จ
            if (response.ok) {
                await Swal.fire({
                    icon: 'success',
                    title: 'สำเร็จ!',
                    text: 'เพิ่มข้อมูลสมาชิกเรียบร้อยแล้ว',
                    timer: 1500,
                    showConfirmButton: false
                });
                
                window.location.href = `${site_url}/System_member/member_web`;
                return;
            } else {
                throw new Error('ไม่สามารถประมวลผลข้อมูลได้');
            }
        }
        
        // ประมวลผล JSON response
        if (result && result.success) {
            await Swal.fire({
                icon: 'success',
                title: 'สำเร็จ!',
                text: result.message || 'เพิ่มข้อมูลสมาชิกเรียบร้อยแล้ว',
                timer: 1500,
                showConfirmButton: false
            });
            
            // redirect ไปหน้ารายการ
            if (result.member_id) {
                window.location.href = `${site_url}/System_member/member_web?highlight=${result.member_id}`;
            } else {
                window.location.href = `${site_url}/System_member/member_web`;
            }
        } else {
            throw new Error(result ? result.message : 'ไม่สามารถบันทึกข้อมูลได้');
        }

    } catch (error) {
        console.error('Submit Error:', error);
        
        // จัดการ error ต่างๆ
        let errorMessage = 'ไม่สามารถบันทึกข้อมูลได้';
        
        if (error.name === 'AbortError') {
            errorMessage = 'การส่งข้อมูลใช้เวลานานเกินไป กรุณาลองใหม่อีกครั้ง';
        } else if (error.message) {
            errorMessage = error.message;
        }
        
        await Swal.fire({
            icon: 'error',
            title: 'เกิดข้อผิดพลาด!',
            text: errorMessage,
            confirmButtonText: 'ตกลง'
        });
        
    } finally {
        // คืนค่าปุ่มเดิม
        isSubmitting = false;
        saveBtn.disabled = false;
        saveBtn.innerHTML = originalText;
    }
}

// Helper Functions
function highlightErrorField(element) {
    if (!element) return;
    
    element.classList.add('border-red-400', 'bg-red-50');
    element.classList.remove('border-gray-200');
    
    setTimeout(() => {
        element.classList.remove('border-red-400', 'bg-red-50');
        element.classList.add('border-gray-200');
    }, 3000);
}

function clearValidationHighlights() {
    const errorElements = document.querySelectorAll('.border-red-400');
    errorElements.forEach(element => {
        element.classList.remove('border-red-400', 'bg-red-50');
        element.classList.add('border-gray-200');
    });
}

// Email Validation Functions
function validateAndCheckEmail(email) {
    const validationStatus = document.getElementById('email-validation-status');
    const availabilityStatus = document.getElementById('email-availability-status');
    
    if (!validationStatus || !availabilityStatus) return;
    
    validationStatus.innerHTML = '';
    availabilityStatus.innerHTML = '';
    
    if (!email) {
        validationStatus.innerHTML = '<span class="text-red-500">✗ กรุณากรอกอีเมล</span>';
        return;
    }

    const emailRegex = /^[a-zA-Z0-9._\%+\-]+@[a-zA-Z0-9.\-]+\.[a-zA-Z]{2,}$/;
    if (!emailRegex.test(email)) {
        validationStatus.innerHTML = '<span class="text-red-500">✗ รูปแบบอีเมลไม่ถูกต้อง</span>';
        return;
    }

    validationStatus.innerHTML = '<span class="text-green-500">✓ รูปแบบอีเมลถูกต้อง</span>';
    
    if (emailCheckTimeout) {
        clearTimeout(emailCheckTimeout);
    }

    emailCheckTimeout = setTimeout(() => {
        availabilityStatus.innerHTML = '<span class="text-blue-500">กำลังตรวจสอบ...</span>';
        checkEmailAvailability(email);
    }, 500);
}

function checkEmailAvailability(email) {
    $.ajax({
        url: `${site_url}/System_member/check_email_available`,
        type: 'POST',
        data: { email },
        success: function(response) {
            try {
                const result = typeof response === 'string' ? JSON.parse(response) : response;
                handleEmailCheckResponse(result);
            } catch (e) {
                console.error('Error parsing email check response:', e);
                handleEmailCheckError();
            }
        },
        error: function(xhr, status, error) {
            console.error('Email check AJAX error:', status, error);
            handleEmailCheckError();
        }
    });
}

function handleEmailCheckResponse(result) {
    const availabilityStatus = document.getElementById('email-availability-status');
    
    if (!availabilityStatus) return;
    
    if (result && result.available) {
        availabilityStatus.innerHTML = '<span class="text-green-500">✓ อีเมลนี้สามารถใช้งานได้</span>';
    } else {
        availabilityStatus.innerHTML = '<span class="text-red-500">✗ อีเมลนี้มีผู้ใช้งานแล้ว</span>';
    }
}

function handleEmailCheckError() {
    const availabilityStatus = document.getElementById('email-availability-status');
    
    if (!availabilityStatus) return;
    
    availabilityStatus.innerHTML = '<span class="text-red-500">✗ ไม่สามารถตรวจสอบอีเมลได้ กรุณาลองใหม่</span>';
}

// Module Management Functions
function toggleMenuCheckboxes(moduleId) {
    const mainCheckbox = document.getElementById(`module_${moduleId}`);
    const menuCheckboxes = document.querySelectorAll(`.menu-checkbox-${moduleId}`);
    const menuGrid = document.querySelector(`.menu-grid-${moduleId}`);
    
    if (!mainCheckbox) return;
    
    // ตรวจสอบว่า Module ID 11 ถูกซ่อนไปแล้ว ไม่ต้องจัดการ
    if (moduleId === 11 || moduleId === '11') {
        console.log('Module 11 is hidden and handled automatically');
        return;
    }
    
    if (mainCheckbox.checked) {
        // แสดงส่วนเมนูย่อยเมื่อเปิดใช้งานโมดูล
        if (menuGrid) {
            menuGrid.style.display = 'block';
            // เพิ่ม slide down animation
            menuGrid.style.opacity = '0';
            menuGrid.style.transform = 'translateY(-10px)';
            setTimeout(() => {
                menuGrid.style.transition = 'all 0.3s ease-out';
                menuGrid.style.opacity = '1';
                menuGrid.style.transform = 'translateY(0)';
            }, 50);
        }
        
        // เปิดใช้งาน checkbox เมนูย่อย
        menuCheckboxes.forEach(checkbox => {
            checkbox.disabled = false;
        });
        
        // สำหรับ Back Office Module (ID = 3) และ กระดานสนทนา (ID = 17) ให้เลือกเมนูย่อยทั้งหมดโดยอัตโนมัติ
        if (moduleId === 3 || moduleId === '3' || moduleId === 17 || moduleId === '17') {
            setTimeout(() => {
                menuCheckboxes.forEach(checkbox => {
                    if (!checkbox.checked) {
                        checkbox.checked = true;
                    }
                });
                
                // แสดงข้อความแจ้งเตือนตามประเภท module
                const moduleNames = {
                    3: 'Back Office',
                    17: 'กระดานสนทนาภายในเครือข่าย'
                };
                
                const moduleName = moduleNames[moduleId] || 'ระบบ';
                const moduleColor = moduleId === 17 ? 'purple' : 'success';
                
                Swal.fire({
                    icon: moduleColor === 'purple' ? 'info' : 'success',
                    title: `เลือกเมนู${moduleName}ทั้งหมดแล้ว`,
                    text: `ระบบได้เลือกเมนูย่อยทั้งหมดให้อัตโนมัติ เพื่อการทำงานที่สมบูรณ์`,
                    timer: 2500,
                    showConfirmButton: false,
                    toast: true,
                    position: 'top-end'
                });
            }, 400);
        }
        
        // จัดการเฉพาะโมดูลจัดการเว็บไซต์ (ID = 2)
        if (moduleId === 2 || moduleId === '2') {
            toggleWebPermissions(mainCheckbox);
        }
    } else {
        // ซ่อนส่วนเมนูย่อยเมื่อปิดใช้งานโมดูล
        if (menuGrid) {
            menuGrid.style.transition = 'all 0.3s ease-in';
            menuGrid.style.opacity = '0';
            menuGrid.style.transform = 'translateY(-10px)';
            setTimeout(() => {
                menuGrid.style.display = 'none';
            }, 300);
        }
        
        // ปิดใช้งานและยกเลิกการเลือก checkbox เมนูย่อย
        menuCheckboxes.forEach(checkbox => {
            checkbox.disabled = true;
            checkbox.checked = false;
        });
        
        // สำหรับ Back Office Module และ กระดานสนทนา แสดงการแจ้งเตือน
        if (moduleId === 3 || moduleId === '3') {
            Swal.fire({
                icon: 'warning',
                title: 'ปิดใช้งาน Back Office',
                html: `
                    <div class="text-sm">
                        <p class="mb-2">คุณได้ปิดใช้งานระบบ Back Office</p>
                        <p class="text-orange-600">
                            <i class="fas fa-exclamation-triangle mr-1"></i>
                            ระบบนี้อาจจำเป็นสำหรับการทำงานพื้นฐาน
                        </p>
                    </div>
                `,
                timer: 3000,
                showConfirmButton: false,
                toast: true,
                position: 'top-end'
            });
        } else if (moduleId === 17 || moduleId === '17') {
            Swal.fire({
                icon: 'warning',
                title: 'ปิดใช้งานกระดานสนทนา',
                html: `
                    <div class="text-sm">
                        <p class="mb-2">คุณได้ปิดใช้งานกระดานสนทนาภายในเครือข่าย</p>
                        <p class="text-orange-600">
                            <i class="fas fa-exclamation-triangle mr-1"></i>
                            ระบบนี้ช่วยในการสื่อสารภายในองค์กร
                        </p>
                    </div>
                `,
                timer: 3000,
                showConfirmButton: false,
                toast: true,
                position: 'top-end'
            });
        }
        
        // จัดการเฉพาะโมดูลจัดการเว็บไซต์ (ID = 2)
        if (moduleId === 2 || moduleId === '2') {
            toggleWebPermissions(mainCheckbox);
        }
    }
}

function handleGrantUserChange(checkbox) {
    const isAllAccess = checkbox.hasAttribute('data-all') && checkbox.getAttribute('data-all') === 'true';
    const grantUserCheckboxes = document.querySelectorAll('.grant-user-checkbox');
    
    // รายการ grant_user_ids ที่สำคัญที่ต้องเลือกเมื่อเลือก "ทั้งหมด"
    const importantGrantIds = ['1', '50', '53', '105', '106', '107', '108', '109', '125'];

    if (isAllAccess) {
        if (checkbox.checked) {
            // ถ้าเลือก "ทั้งหมด" ให้เลือกเฉพาะ grant_user_ids ที่สำคัญและ disable อันอื่น
            grantUserCheckboxes.forEach(cb => {
                if (cb !== checkbox) {
                    const cbValue = cb.value;
                    
                    if (importantGrantIds.includes(cbValue)) {
                        // เลือกเฉพาะ grant_user_ids ที่สำคัญ
                        cb.checked = true;
                        cb.disabled = true; // ปิดการใช้งาน
                        
                        // เพิ่ม visual effect สำหรับตัวที่ถูกเลือกแบบบังคับ
                        cb.closest('label').style.opacity = '0.8';
                        cb.closest('label').style.backgroundColor = '#e8f4f8';
                        cb.closest('label').style.borderColor = '#3b82f6';
                    } else {
                        // ไม่เลือกสิทธิ์ที่ไม่สำคัญ
                        cb.checked = false;
                        cb.disabled = true;
                        
                        // เพิ่ม visual effect สำหรับตัวที่ถูก disable
                        cb.closest('label').style.opacity = '0.5';
                        cb.closest('label').style.backgroundColor = '#f9f9f9';
                    }
                }
            });
            
            // เพิ่ม visual effect สำหรับ checkbox "ทั้งหมด" เอง
            checkbox.closest('label').style.opacity = '1';
            checkbox.closest('label').style.backgroundColor = '#dbeafe';
            checkbox.closest('label').style.borderColor = '#3b82f6';
            
            // แสดงข้อความแจ้งเตือน
            Swal.fire({
                icon: 'info',
                title: 'เลือกสิทธิ์ทั้งหมดแล้ว',
                html: `
                    <div class="text-sm text-left">
                        <p class="mb-2">จะได้รับสิทธิ์หลักทั้งหมด ประกอบด้วย:</p>
                        <ul class="list-disc list-inside space-y-1 text-gray-600">
                            <li>สิทธิ์การจัดการทั้งหมด</li>
                            <li>สิทธิ์การจัดการเนื้อหาสำคัญ</li>
                            <li>สิทธิ์การตั้งค่าระบบ</li>
                        </ul>
                    </div>
                `,
                timer: 3000,
                showConfirmButton: false,
                toast: true,
                position: 'top-end'
            });
        } else {
            // ถ้ายกเลิกการเลือก "ทั้งหมด" ให้ uncheck และเปิดการใช้งานทุกอันคืน
            grantUserCheckboxes.forEach(cb => {
                if (cb !== checkbox) {
                    cb.checked = false; // ยกเลิกการเลือกทั้งหมด
                    cb.disabled = false; // เปิดการใช้งานคืน
                    
                    // รีเซ็ต visual effects
                    cb.closest('label').style.opacity = '1';
                    cb.closest('label').style.backgroundColor = '';
                    cb.closest('label').style.borderColor = '';
                }
            });
            
            // รีเซ็ต visual effect สำหรับ checkbox "ทั้งหมด" เอง
            checkbox.closest('label').style.opacity = '1';
            checkbox.closest('label').style.backgroundColor = '';
            checkbox.closest('label').style.borderColor = '';
            
            // แสดงข้อความแจ้งเตือน
            Swal.fire({
                icon: 'warning',
                title: 'ยกเลิกสิทธิ์ทั้งหมดแล้ว',
                text: 'ยกเลิกการเลือกสิทธิ์ทั้งหมดแล้ว กรุณาเลือกสิทธิ์ที่ต้องการใช้งาน',
                timer: 2000,
                showConfirmButton: false,
                toast: true,
                position: 'top-end'
            });
        }
    } else {
        // ถ้าเลือกตัวเลือกอื่น ให้ตรวจสอบว่า "ทั้งหมด" ถูกเลือกอยู่หรือไม่
        if (checkbox.checked) {
            const allAccessCheckbox = document.querySelector('.grant-user-checkbox[data-all="true"]');
            if (allAccessCheckbox && allAccessCheckbox.checked) {
                // ถ้า "ทั้งหมด" ยังถูกเลือกอยู่ ให้ยกเลิกการเลือก "ทั้งหมด"
                allAccessCheckbox.checked = false;
                
                // เปิดการใช้งานคืนและรีเซ็ต visual effects สำหรับ checkbox อื่นๆ
                grantUserCheckboxes.forEach(cb => {
                    if (cb !== allAccessCheckbox && cb !== checkbox) {
                        cb.disabled = false; // เปิดการใช้งานคืน
                        
                        // รีเซ็ต visual effects
                        cb.closest('label').style.opacity = '1';
                        cb.closest('label').style.backgroundColor = '';
                        cb.closest('label').style.borderColor = '';
                    }
                });
                
                // รีเซ็ต visual effect สำหรับ checkbox "ทั้งหมด"
                allAccessCheckbox.closest('label').style.opacity = '1';
                allAccessCheckbox.closest('label').style.backgroundColor = '';
                allAccessCheckbox.closest('label').style.borderColor = '';
                
                // แสดงข้อความแจ้งเตือน
                Swal.fire({
                    icon: 'info',
                    title: 'ยกเลิกโหมดทั้งหมดแล้ว',
                    text: 'เปลี่ยนเป็นโหมดเลือกแต่ละรายการ คุณสามารถเลือกสิทธิ์ได้อย่างอิสระ',
                    timer: 2000,
                    showConfirmButton: false,
                    toast: true,
                    position: 'top-end'
                });
            }
        }
    }
    
    // ตรวจสอบสิทธิ์แบบ Real-time หลังจากเปลี่ยนแปลง
    setTimeout(() => checkWebPermissionRequirement(), 100);
}

function toggleWebPermissions(moduleCheckbox) {
    const grantUserCheckboxes = document.querySelectorAll('.grant-user-checkbox');
    const webPermissionsSection = document.querySelector('.web-permissions');
    
    if (webPermissionsSection) {
        if (moduleCheckbox.checked) {
            // แสดงส่วนกำหนดสิทธิ์เว็บไซต์
            webPermissionsSection.style.display = 'block';
            // เพิ่ม slide down animation
            webPermissionsSection.style.opacity = '0';
            webPermissionsSection.style.transform = 'translateY(-10px)';
            setTimeout(() => {
                webPermissionsSection.style.transition = 'all 0.3s ease-out';
                webPermissionsSection.style.opacity = '1';
                webPermissionsSection.style.transform = 'translateY(0)';
            }, 50);
            
            // เปิดใช้งานส่วนกำหนดสิทธิ์
            grantUserCheckboxes.forEach(checkbox => {
                checkbox.disabled = false;
            });
            
            // แสดงข้อความแนะนำ
            showWebPermissionReminder();
        } else {
            // ซ่อนส่วนกำหนดสิทธิ์เว็บไซต์
            webPermissionsSection.style.transition = 'all 0.3s ease-in';
            webPermissionsSection.style.opacity = '0';
            webPermissionsSection.style.transform = 'translateY(-10px)';
            setTimeout(() => {
                webPermissionsSection.style.display = 'none';
            }, 300);
            
            // ปิดการใช้งานและรีเซ็ทค่าทั้งหมด
            grantUserCheckboxes.forEach(checkbox => {
                checkbox.disabled = true;
                checkbox.checked = false;
                
                // รีเซ็ต visual effects
                checkbox.closest('label').style.opacity = '1';
                checkbox.closest('label').style.backgroundColor = '';
                checkbox.closest('label').style.borderColor = '';
            });
            
            // ลบ visual effects ของ section
            webPermissionsSection.style.border = '';
            webPermissionsSection.style.backgroundColor = '';
            
            // ลบข้อความ warning ถ้ามี
            const warningElement = webPermissionsSection.querySelector('.permission-warning');
            if (warningElement) {
                warningElement.remove();
            }
        }
    }
}

function checkWebPermissionRequirement() {
    const websiteModuleCheckbox = document.getElementById('module_2');
    const webPermissionsSection = document.querySelector('.web-permissions');
    const saveBtn = document.getElementById('saveBtn');
    
    if (!websiteModuleCheckbox || !webPermissionsSection || !saveBtn) return;
    
    if (websiteModuleCheckbox.checked) {
        const checkedGrantUsers = document.querySelectorAll('.grant-user-checkbox:checked');
        
        if (checkedGrantUsers.length === 0) {
            // แสดง visual warning
            webPermissionsSection.style.border = '1px solid #f59e0b';
            webPermissionsSection.style.backgroundColor = '#fffbeb';
            
            // เพิ่มข้อความแจ้งเตือนด้านล่าง
            let warningElement = webPermissionsSection.querySelector('.permission-warning');
            if (!warningElement) {
                warningElement = document.createElement('div');
                warningElement.className = 'permission-warning mt-3 p-3 bg-orange-50 border border-orange-200 rounded-lg';
                warningElement.innerHTML = `
                    <div class="flex items-center space-x-2">
                        <i class="fas fa-exclamation-triangle text-orange-500"></i>
                        <span class="text-sm text-orange-700 font-medium">กรุณาเลือกสิทธิ์การจัดการอย่างน้อย 1 รายการ</span>
                    </div>
                `;
                webPermissionsSection.appendChild(warningElement);
            }
            
            // ปิดการใช้งานปุ่มบันทึก
            saveBtn.disabled = true;
            saveBtn.classList.add('opacity-50', 'cursor-not-allowed');
            saveBtn.title = 'กรุณาเลือกสิทธิ์การจัดการเว็บไซต์อย่างน้อย 1 รายการ';
        } else {
            // ลบ visual warning
            webPermissionsSection.style.border = '';
            webPermissionsSection.style.backgroundColor = '';
            
            // ลบข้อความแจ้งเตือน
            const warningElement = webPermissionsSection.querySelector('.permission-warning');
            if (warningElement) {
                warningElement.remove();
            }
            
            // เปิดการใช้งานปุ่มบันทึก
            saveBtn.disabled = false;
            saveBtn.classList.remove('opacity-50', 'cursor-not-allowed');
            saveBtn.title = '';
        }
    } else {
        // ถ้าไม่เปิดใช้งานโมดูล ให้ลบ warning ทั้งหมด
        webPermissionsSection.style.border = '';
        webPermissionsSection.style.backgroundColor = '';
        
        const warningElement = webPermissionsSection.querySelector('.permission-warning');
        if (warningElement) {
            warningElement.remove();
        }
        
        saveBtn.disabled = false;
        saveBtn.classList.remove('opacity-50', 'cursor-not-allowed');
        saveBtn.title = '';
    }
}

function showWebPermissionReminder() {
    Swal.fire({
        icon: 'info',
        title: 'เปิดใช้งานโมดูลจัดการเว็บไซต์',
        html: `
            <div class="text-sm text-left">
                <p class="mb-2"><strong>หมายเหตุสำคัญ:</strong></p>
                <p class="text-orange-600">กรุณาเลือกสิทธิ์การจัดการเว็บไซต์อย่างน้อย 1 รายการ</p>
                <ul class="list-disc list-inside mt-2 space-y-1 text-gray-600">
                    <li>เลือก "ทั้งหมด" เพื่อได้สิทธิ์หลักทั้งหมด</li>
                    <li>หรือเลือกสิทธิ์เฉพาะที่ต้องการ</li>
                </ul>
            </div>
        `,
        timer: 4000,
        showConfirmButton: false,
        toast: true,
        position: 'top-end',
        width: '400px'
    });
}

// Initialization Functions
function initializeUserTypeSelection() {
    const userTypeRadios = document.querySelectorAll('input[name="m_system"]');
    if (!userTypeRadios.length) return;
    
    userTypeRadios.forEach(radio => {
        radio.addEventListener('change', () => {
            togglePositionSelect(radio.value);
            toggleStepsByUserType(radio.value);
        });
    });
}

function initializeModuleCheckboxes() {
    const moduleCheckboxes = document.querySelectorAll('input[name="grant_system_ref_id[]"]');
    
    moduleCheckboxes.forEach(checkbox => {
        checkbox.addEventListener('change', function() {
            toggleMenuCheckboxes(this.value);
        });
        
        if (checkbox.checked) {
            toggleMenuCheckboxes(checkbox.value);
        }
    });
}

// เปลี่ยนชื่อฟังก์ชันจาก initializeBackOfficeModule เป็น initializeAutoEnabledModules
function initializeAutoEnabledModules() {
    // ตรวจสอบ Back Office Module (ID = 3)
    const backOfficeCheckbox = document.getElementById('module_3');
    
    if (backOfficeCheckbox && backOfficeCheckbox.checked) {
        setTimeout(() => {
            Swal.fire({
                icon: 'info',
                title: 'ระบบ Back Office',
                html: `
                    <div class="text-sm text-left">
                        <p class="mb-3"><strong>ระบบนี้ได้รับการเลือกโดยอัตโนมัติ</strong></p>
                        <ul class="list-disc list-inside space-y-1 text-gray-600">
                            <li>จำเป็นสำหรับการทำงานพื้นฐานของผู้ใช้งาน</li>
                            <li>รวมเมนูสำคัญที่ต้องใช้ในการทำงานประจำ</li>
                            <li>คุณสามารถปรับแต่งสิทธิ์ได้ตามต้องการ</li>
                        </ul>
                    </div>
                `,
                timer: 4000,
                showConfirmButton: false,
                toast: true,
                position: 'top-end',
                width: '400px'
            });
        }, 1000);
        
        const menuGrid3 = document.querySelector('.menu-grid-3');
        if (menuGrid3) {
            menuGrid3.style.border = '1px solid #10b981';
            menuGrid3.querySelector('.grid').style.backgroundColor = '#f0fdf4';
            
            setTimeout(() => {
                menuGrid3.style.border = '';
                menuGrid3.querySelector('.grid').style.backgroundColor = '';
            }, 3000);
        }
    }
    
    // ตรวจสอบ กระดานสนทนา Module (ID = 17)
    const discussionBoardCheckbox = document.getElementById('module_17');
    
    if (discussionBoardCheckbox && discussionBoardCheckbox.checked) {
        setTimeout(() => {
            Swal.fire({
                icon: 'info',
                title: 'กระดานสนทนาภายในเครือข่าย',
                html: `
                    <div class="text-sm text-left">
                        <p class="mb-3"><strong>ระบบนี้ได้รับการเลือกโดยอัตโนมัติ</strong></p>
                        <ul class="list-disc list-inside space-y-1 text-gray-600">
                            <li>ช่วยในการสื่อสารภายในองค์กร อบต. เทศบาล</li>
                            <li>ส่งเสริมการทำงานร่วมกันภายในเครือข่าย</li>
                            <li>เปิดใช้งานโดยอัตโนมัติเพื่อให้ทุกคนสื่อสารได้</li>
                            <li>คุณสามารถปรับแต่งสิทธิ์ได้ตามต้องการ</li>
                        </ul>
                    </div>
                `,
                timer: 4500,
                showConfirmButton: false,
                toast: true,
                position: 'top-end',
                width: '420px'
            });
        }, 1500); // รอหลัง Back Office 0.5 วินาที
        
        const menuGrid17 = document.querySelector('.menu-grid-17');
        if (menuGrid17) {
            menuGrid17.style.border = '1px solid #9333ea';
            menuGrid17.querySelector('.grid').style.backgroundColor = '#faf5ff';
            
            setTimeout(() => {
                menuGrid17.style.border = '';
                menuGrid17.querySelector('.grid').style.backgroundColor = '';
            }, 3000);
        }
    }
}

// Utility Functions
function togglePassword(inputId) {
    const input = document.getElementById(inputId);
    if (!input) return;
    
    const buttonElement = input.nextElementSibling;
    if (!buttonElement) return;
    
    const icon = buttonElement.querySelector('i');
    if (!icon) return;
    
    input.type = input.type === 'password' ? 'text' : 'password';
    icon.classList.toggle('fa-eye');
    icon.classList.toggle('fa-eye-slash');
}

function togglePositionSelect(userType) {
    const positionContainer = document.getElementById('position-select-container');
    const positionSelect = document.getElementById('ref_pid');
    
    if (userType === 'system_admin') {
        positionContainer.classList.add('hidden');
        positionSelect.removeAttribute('required');
        positionSelect.value = '';
    } else {
        positionContainer.classList.remove('hidden');
        positionSelect.setAttribute('required', 'required');
    }
}

function toggleStepsByUserType(userType) {
    const memberManagementSection = document.querySelector('.member-management-section');
    const websiteManagementSection = document.querySelector('.website-management-section');
    
    const isEndUser = userType === 'end_user';
    
    // จัดการแท็บและส่วนของระบบจัดการสมาชิก (Module ID = 1)
    if (memberManagementSection) {
        memberManagementSection.style.display = isEndUser ? 'none' : 'block';
    }
    
    // จัดการแท็บและส่วนของระบบจัดการเว็บไซต์ (Module ID = 2)
    if (websiteManagementSection) {
        websiteManagementSection.style.display = isEndUser ? 'none' : 'block';
    }
    
    // จัดการกับ checkbox ของโมดูลที่ถูกจำกัด
    const memberManagementCheckbox = document.querySelector('input[type="checkbox"][value="1"]');
    const websiteManagementCheckbox = document.querySelector('input[type="checkbox"][value="2"]');
    
    if (memberManagementCheckbox) {
        memberManagementCheckbox.disabled = isEndUser;
        if (isEndUser) {
            memberManagementCheckbox.checked = false;
            toggleMenuCheckboxes('1');
        }
    }
    
    if (websiteManagementCheckbox) {
        websiteManagementCheckbox.disabled = isEndUser;
        if (isEndUser) {
            websiteManagementCheckbox.checked = false;
            toggleMenuCheckboxes('2');
        }
    }
    
    // แสดง notification เมื่อเลือก end user
    if (isEndUser) {
        Swal.fire({
            icon: 'info',
            title: 'ข้อจำกัดสำหรับบุคลากรภายใน',
            html: `
                <div class="text-sm text-left">
                    <p class="mb-3">บุคลากรภายในจะไม่สามารถเข้าถึงระบบต่อไปนี้:</p>
                    <ul class="list-disc list-inside space-y-1 text-gray-600">
                        <li>ระบบจัดการสมาชิก</li>
                        <li>ระบบจัดการเว็บไซต์</li>
                    </ul>
                    <p class="mt-3 text-orange-600">
                        <i class="fas fa-info-circle mr-1"></i>
                        ระบบเหล่านี้จะถูกซ่อนจากการตั้งค่าสิทธิ์
                    </p>
                </div>
            `,
            confirmButtonText: 'เข้าใจแล้ว',
            timer: 6000,
            timerProgressBar: true
        });
    }
}

// Avatar Functions
function selectAvatar(url) {
    const preview = document.getElementById('preview');
    if (preview) {
        preview.src = url;
    }
    
    const avatarUrlInput = document.getElementById('avatarUrl');
    if (avatarUrlInput) {
        avatarUrlInput.value = url;
    }
    
    const fileInput = document.getElementById('imageUpload');
    if (fileInput) {
        fileInput.value = '';
    }
    
    const avatarItems = document.querySelectorAll('.avatar-item');
    avatarItems.forEach(item => {
        const itemImg = item.querySelector('img');
        if (itemImg && itemImg.src === url) {
            item.classList.remove('border-transparent');
            item.classList.add('border-blue-500');
        } else {
            item.classList.remove('border-blue-500');
            item.classList.add('border-transparent');
        }
    });
    
    Swal.fire({
        icon: 'success',
        title: 'เลือก Avatar สำเร็จ',
        text: 'เลือก Avatar เรียบร้อยแล้ว',
        timer: 1500,
        showConfirmButton: false,
        toast: true,
        position: 'top-end'
    });
}

function previewImage(input) {
    if (input.files && input.files[0]) {
        const file = input.files[0];
        
        const allowedTypes = ['image/jpeg', 'image/jpg', 'image/png', 'image/gif', 'image/webp'];
        if (!allowedTypes.includes(file.type)) {
            Swal.fire({
                icon: 'warning',
                title: 'ประเภทไฟล์ไม่ถูกต้อง',
                text: 'กรุณาเลือกไฟล์รูปภาพ (JPG, PNG, GIF, WEBP) เท่านั้น'
            });
            return;
        }
        
        const maxSize = 5 * 1024 * 1024;
        if (file.size > maxSize) {
            Swal.fire({
                icon: 'warning',
                title: 'ไฟล์ใหญ่เกินไป',
                text: 'ขนาดไฟล์ต้องไม่เกิน 5MB'
            });
            return;
        }
        
        const reader = new FileReader();
        const preview = document.querySelector('#preview');
        
        if (!preview) return;
        
        reader.onload = e => {
            preview.src = e.target.result;
            
            const avatarUrlInput = document.getElementById('avatarUrl');
            if (avatarUrlInput) {
                avatarUrlInput.value = '';
            }
            
            const avatarItems = document.querySelectorAll('.avatar-item');
            avatarItems.forEach(item => {
                item.classList.remove('border-blue-500');
                item.classList.add('border-transparent');
            });
            
            Swal.fire({
                icon: 'success',
                title: 'อัปโหลดสำเร็จ',
                text: 'เลือกรูปภาพเรียบร้อยแล้ว',
                timer: 1500,
                showConfirmButton: false,
                toast: true,
                position: 'top-end'
            });
        }
        
        reader.readAsDataURL(file);
    }
}

function updateAvatarsFromEmail() {
    const emailInput = document.querySelector('input[name="m_username"]');
    if (!emailInput) return;
    
    const email = emailInput.value.trim();
    if (!email) return;
    
    let initials = '';
    if (email.length >= 2) {
        initials = email.substring(0, 2).toUpperCase();
    } else if (email.length === 1) {
        initials = email.toUpperCase();
    }
    
    const emailAvatars = document.querySelectorAll('.email-avatar');
    emailAvatars.forEach(img => {
        if (img && img.src.includes('ui-avatars.com')) {
            const currentUrl = new URL(img.src);
            currentUrl.searchParams.set('name', initials);
            img.src = currentUrl.toString();
            
            const parentDiv = img.closest('.avatar-item');
            if (parentDiv) {
                parentDiv.setAttribute('onclick', `selectAvatar('${currentUrl.toString()}')`);
            }
        }
    });
}
</script>

<!-- SweetAlert2 CDN -->
<script src="https://cdnjs.cloudflare.com/ajax/libs/sweetalert2/11.0.19/sweetalert2.min.js"></script>
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/sweetalert2/11.0.19/sweetalert2.min.css">

<!-- FontAwesome for icons -->
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">

<!-- jQuery CDN -->
<script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.6.0/jquery.min.js"></script>

<style>
/* Custom CSS */
.border-red-400 {
    border-color: #f87171 !important;
}

.bg-red-50 {
    background-color: #fef2f2 !important;
}

.avatar-item.selected {
    border-color: #3b82f6 !important;
}

input[type="password"]::-webkit-credentials-auto-fill-button {
    visibility: hidden;
    display: none !important;
}

input[type="password"]:-webkit-autofill,
input[type="password"]:-webkit-autofill:hover, 
input[type="password"]:-webkit-autofill:focus {
    -webkit-box-shadow: 0 0 0px 1000px white inset !important;
    -webkit-text-fill-color: #000 !important;
    transition: background-color 5000s ease-in-out 0s;
}

input, select, textarea {
    transition: all 0.2s ease-in-out;
}

input:focus, select:focus, textarea:focus {
    outline: none;
    box-shadow: 0 0 0 2px rgba(59, 130, 246, 0.1);
}

button:hover {
    transform: translateY(-1px);
    transition: all 0.2s ease-in-out;
}

button:active {
    transform: translateY(0);
}

/* Permission sections that can be hidden/shown */
.web-permissions,
.menu-grid-1, .menu-grid-2, .menu-grid-3, .menu-grid-4, .menu-grid-5,
.menu-grid-6, .menu-grid-7, .menu-grid-8, .menu-grid-9, .menu-grid-10,
.menu-grid-17,
.module-11-info {
    transition: all 0.3s ease-in-out;
}

.web-permissions[style*="display: none"],
.menu-grid-1[style*="display: none"], .menu-grid-2[style*="display: none"],
.menu-grid-3[style*="display: none"], .menu-grid-4[style*="display: none"],
.menu-grid-5[style*="display: none"], .menu-grid-6[style*="display: none"],
.menu-grid-7[style*="display: none"], .menu-grid-8[style*="display: none"],
.menu-grid-9[style*="display: none"], .menu-grid-10[style*="display: none"],
.menu-grid-17[style*="display: none"],
.module-11-info[style*="display: none"] {
    opacity: 0;
    transform: translateY(-10px);
}

/* Smooth show animation */
.web-permissions[style*="opacity: 1"],
[class*="menu-grid-"][style*="opacity: 1"],
.module-11-info[style*="opacity: 1"] {
    animation: slideInDown 0.3s ease-out;
}

/* Special styling for Module 11 info box */
.module-11-info {
    border-left: 4px solid #3b82f6;
}

.module-11-info .bg-blue-50 {
    background: linear-gradient(135deg, #eff6ff 0%, #dbeafe 100%);
}

@keyframes slideInDown {
    from {
        opacity: 0;
        transform: translateY(-10px);
    }
    to {
        opacity: 1;
        transform: translateY(0);
    }
}

/* Module sections that can be hidden */
.member-management-section,
.website-management-section {
    transition: all 0.3s ease-in-out;
}

.member-management-section[style*="display: none"],
.website-management-section[style*="display: none"] {
    opacity: 0;
    transform: translateY(-20px);
}

/* Responsive design improvements */
@media (max-width: 768px) {
    .grid-cols-2 {
        grid-template-columns: 1fr;
    }
    
    .grid-cols-3 {
        grid-template-columns: 1fr;
    }
    
    .grid-cols-5 {
        grid-template-columns: repeat(3, 1fr);
    }
}

/* Loading states */
.web-permissions[style*="opacity: 0.5"] {
    pointer-events: none;
}

/* Better visual feedback for disabled elements */
input:disabled,
select:disabled,
textarea:disabled {
    background-color: #f9fafb;
    color: #6b7280;
    cursor: not-allowed;
}

/* Checkbox styling improvements */
.form-checkbox {
    border-radius: 0.375rem;
    border-width: 2px;
    transition: all 0.15s ease-in-out;
}

.form-checkbox:checked {
    background-color: #3b82f6;
    border-color: #3b82f6;
}

.form-checkbox:focus {
    box-shadow: 0 0 0 3px rgba(59, 130, 246, 0.1);
    border-color: #3b82f6;
}

/* Better spacing for module sections */
.bg-gray-50.p-6.rounded-xl[data-module-id] {
    margin-bottom: 1.5rem;
}

/* Improved avatar grid on mobile */
@media (max-width: 640px) {
    .avatar-item {
        padding: 0.5rem;
    }
    
    .avatar-item img {
        width: 3rem;
        height: 3rem;
    }
}
</style>