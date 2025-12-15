<div class="text-center pages-head">
    <span class="font-pages-head">ช่องทางแจ้งเรื่องร้องเรียนการทุจริตและประพฤติมิชอบ</span>
</div>
</div>
<img src="<?php echo base_url('docs/welcome-btm-light-other.png'); ?>">

<div class="bg-pages ">
    <div class="container-pages-news" style="position: relative; z-index: 10;">
        <div class="d-flex justify-content-end">
            <div class="form-group">
                <div class="col-sm-12">
                    <select class="form-select custom-select" id="ChangPagesComplain">
                        <option value="" disabled selected>แจ้งเรื่องร้องเรียนการทุจริต</option>
                        <option value="esv_ods">ยื่นเอกสารออนไลน์</option>
                        <option value="suggestions">รับฟังความคิดเห็น</option>
                        <option value="complain">ร้องเรียน/ร้องทุกข์</option>
                        <option value="follow-complain">ติดตามสถานะเรื่องร้องเรียน</option>
                    </select>
                </div>
            </div>
        </div>
        <div class=" underline">
            <form id="reCAPTCHA3" action=" <?php echo site_url('Pages/add_corruption'); ?> " method="post" class="form-horizontal" enctype="multipart/form-data">
                <br>
                <div class="form-group">
                    <div class="col-sm-3 control-label font-e-service-complain">เรื่องเหตุการทุจริต <span class="red-font">*</span></div>
                    <div class="col-sm-12 mt-2">
                        <input type="text" name="corruption_topic" class="form-control font-label-e-service-complain" required placeholder="กรอกเรื่องเหตุการทุจริต..." value="<?php echo set_value('corruption_topic'); ?>">
                        <span class="red"><?= form_error('corruption_topic'); ?></span>
                    </div>
                </div>
                <br>
                <div class="row">
                    <div class="col-12 mb-3">
                        <div class="form-check">
                            <input class="form-check-input" type="checkbox" id="anonymousCheck">
                            <label class="form-check-label" for="anonymousCheck">
                                นโยบายการคุ้มครองข้อมูลผู้แจ้งเบาะแส โดยไม่ระบุตัวตน
                            </label>
                        </div>
                    </div>
                    <div class="col-5">
                        <div class="form-group">
                            <div class="col-sm-12 control-label  font-e-service-complain">ชื่อ-นามสกุล <span class="red-font">*</span></div>
                            <div class="col-sm-12 mt-2">
                                <input type="text" name="corruption_by" id="corruption_by" class="form-control font-label-e-service-complain" required placeholder="นางสาวน้ำใส ใจชื่นบาน" value="<?php echo set_value('corruption_by'); ?>">
                                <span class="red"><?= form_error('corruption_by'); ?></span>
                            </div>
                        </div>
                    </div>
                    <div class="col-3">
                        <div class="form-group">
                            <div class="col-sm-12 control-label font-e-service-complain">
                                เบอร์โทรศัพท์ <span class="red-font">*</span>
                            </div>
                            <div class="col-sm-12 mt-2">
                                <input type="tel" id="corruption_phone" name="corruption_phone" class="form-control font-label-e-service-complain" required placeholder="กรอกเบอร์โทรศัพท์" pattern="\d{10}" title="กรุณากรอกเบอร์มือถือเป็นตัวเลข 10 ตัว" value="<?php echo set_value('corruption_phone'); ?>">
                                <span class="red"><?= form_error('corruption_phone'); ?></span>
                            </div>
                        </div>

                        <script>
                            document.getElementById('corruption_phone').addEventListener('input', function(e) {
                                var value = e.target.value;
                                var cleanedValue = value.replace(/\D/g, ''); // Remove non-digit characters
                                if (cleanedValue.length > 10) {
                                    cleanedValue = cleanedValue.slice(0, 10); // Limit to 10 digits
                                }
                                e.target.value = cleanedValue;
                            });

                            // ส่วนที่ 2: การจัดการเมื่อติ๊กเลือก "ไม่ระบุตัวตน"
                            document.getElementById('anonymousCheck').addEventListener('change', function() {
                                // ดึงช่องกรอกชื่อและเบอร์โทร
                                var nameInput = document.getElementById('corruption_by');
                                var phoneInput = document.getElementById('corruption_phone');

                                // เมื่อติ๊กถูก
                                if (this.checked) {
                                    // กำหนดค่าเริ่มต้นสำหรับการไม่ระบุตัวตน
                                    nameInput.value = 'ไม่ระบุตัวตน';
                                    phoneInput.value = '0000000000';

                                    // ล็อคไม่ให้แก้ไขข้อมูล
                                    nameInput.readOnly = true;
                                    phoneInput.readOnly = true;

                                    // เปลี่ยนสีพื้นหลังให้เป็นสีเทาเพื่อแสดงว่าแก้ไขไม่ได้
                                    nameInput.style.backgroundColor = '#f0f0f0';
                                    phoneInput.style.backgroundColor = '#f0f0f0';
                                }
                                // เมื่อไม่ติ๊ก
                                else {
                                    // ล้างข้อมูลในช่องกรอก
                                    nameInput.value = '';
                                    phoneInput.value = '';

                                    // ปลดล็อคให้แก้ไขข้อมูลได้
                                    nameInput.readOnly = false;
                                    phoneInput.readOnly = false;

                                    // เปลี่ยนสีพื้นหลังกลับเป็นปกติ
                                    nameInput.style.backgroundColor = '';
                                    phoneInput.style.backgroundColor = '';
                                }
                            });
                        </script>
                        
                    </div>
                    <div class="col-4">
                        <div class="form-group">
                            <div class="col-sm-12 control-label  font-e-service-complain">อีเมล </div>
                            <div class="col-sm-12 mt-2">
                                <input type="email" name="corruption_email" class="form-control font-label-e-service-complain" placeholder="example@youremail.com" value="<?php echo set_value('corruption_email'); ?>">
                            </div>
                        </div>
                    </div>
                </div>
                <br>
                <div class="form-group">
                    <div class="col-sm-2 control-label font-e-service-complain">ที่อยู่ <span class="red-font">*</span></div>
                    <div class="col-sm-12 mt-2">
                        <input type="text" name="corruption_address" class="form-control font-label-e-service-complain" required placeholder="กรอกข้อมูลที่อยู่ของคุณ" value="<?php echo set_value('corruption_address'); ?>">
                        <span class="red"><?= form_error('corruption_address'); ?></span>
                    </div>
                </div>
                <br>
                <div class="form-group">
                    <label for="exampleFormControlTextarea1" class="form-label font-e-service-complain">รายละเอียด <span class="red-font">*</span></label>
                    <div class="col-sm-12">
                        <textarea name="corruption_detail" class="form-control font-label-e-service-complain" id="exampleFormControlTextarea1" rows="6" placeholder="กรอกรายละเอียดเพิ่มเติม..."><?php echo set_value('corruption_detail'); ?></textarea>
                        <span class="red"><?= form_error('corruption_detail'); ?></span>
                    </div>
                </div>
                <br>
                <div class="form-group">
                    <div class="col-sm-7 control-label font-e-service-complain">รูปภาพเพิ่มเติม(สามารถเพิ่มได้หลายรูป) </div>
                    <div class="col-sm-12 mt-2">
                        <input type="file" name="corruption_imgs[]" class="form-control" accept="image/*" multiple onchange="validateForm(this)">
                    </div>
                </div>
        </div>
        <div class="row mt-4">
			<div>
                <span style="color: red; font-size: 18px;">หมายเหตุ เพื่อเป็นการคุ้มครองสิทธิของผู้ร้องเรียน</span><br><br>
				<span style="font-size: 14px">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;1.  ชื่อ ที่อยู่ หรือข้อมูลใดๆ ที่สามารถระบุตัวผู้ร้องเรียนหรือผู้ให้ข้อมูลได้ จะถูกปกปิดไม่เผยแพร่สู่สาธารณะ </span><br>
				<span style="font-size: 14px;">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;2.  ข้อมูลของผู้ร้องเรียนหรือผู้ให้ข้อมูลจะถูกเก็บเป็นความลับ โดยจำกัดเฉพาะผู้ที่มีหน้าที่รับผิดชอบบในการดำเนินการตรวจสอบเรื่องร้องเรียนเท่านั้น ที่สามารถเข้าถึงข้อมูลดังกล่าวได </span><br>
				<span style="font-size: 14px;">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;3.  ในกรณีที่มีการร้องเรียนหน่วยงานโดยตรง จะกำหนดมาตรการคุ้มครองผู้แจ้งเบาะแสหรือผู้ร้องเรียน พยาน และบุคคลที่ให้ข้อมูลในการสืบสวนหาข้อเท็จจริงไม่ได้ได้รับความเดือดร้อน
					<br>อันตรายใด ๆ หรือความไม่ชอบธรรม อันเกิดมาจากการแจ้งเบาะแส การร้องเรียน การเป็นพยาน หรือการให้ข้อมูล</span><br>
				<span style="font-size: 14px;">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;ทั้งนี้ ผู้ได้รับข้อมูลจากการปฏิบัติหน้าที่ที่เกี่ยวข้อข้องกับเรื่องร้องเรียน มีหน้าที่เก็บรักษาข้อมูล ข้อร้องเรียนและเอกสารหลักฐานของผู้ร้องเรียนและผู้ให้ข้อมูลไว้เป็นความลับ
					<br>ห้ามเปิดเผยข้อมูลแก่บุคคลอื่นที่ไม่มีหน้าที่เกี่ยวข้อง เว้นแต่เป็นการเปิดเผยตามหน้าที่ที่กฎหมายกำหนด</span><br>
            </div>
            <div class="col-6 font-thx-curruption">
                <span>ขอขอบพระคุณที่แจ้งเหตุพบเห็นการทุจริตหน่วยงานภาครัฐ</span>
            </div>
            <div class="col-3">
                <div class="d-flex justify-content-end">
                    <!-- <div class="g-recaptcha" data-sitekey="6LcKoPcnAAAAAKGgUMRtkBs6chDKzC8XOoVnaZg_" data-callback="enableLoginButton"></div> -->
                </div>
            </div>
            <div class="col-3">
                <div class="d-flex justify-content-end">
                    <!-- <button type="submit" id="loginBtn" class="btn" disabled><img src="<?php echo base_url("docs/s.btn-add-q-a.png"); ?>"></button> -->

                    <!-- reCAPTCHA 3  หน้านี้มีเปลี่ยน 1 จุด นี่จุด 1 -->
                    <button data-action='submit' data-callback='onSubmit' data-sitekey="<?php echo get_config_value('recaptcha'); ?>" type="submit" id="loginBtn" class="btn g-recaptcha"><img src="<?php echo base_url("docs/s.btn-add-q-a.png"); ?>"></button>
                </div>
            </div>
        </div>
        </form>
    </div>
</div>

<script>
    function validateForm() {
        const imageTypes = ['image/jpeg', 'image/png', 'image/jpg', 'image/jfif'];
        const pdfType = 'application/pdf';
        const docTypes = [
            'application/msword',
            'application/vnd.openxmlformats-officedocument.wordprocessingml.document',
            'application/vnd.ms-powerpoint',
            'application/vnd.openxmlformats-officedocument.presentationml.presentation',
            'application/vnd.ms-excel',
            'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet'
        ];

        const fileInputs = document.querySelectorAll('input[type="file"]');

        for (const input of fileInputs) {
            for (const file of input.files) {
                if (input.accept.includes('image/') && !imageTypes.includes(file.type)) {
                    Swal.fire({
                        icon: 'error',
                        title: 'ตรวจพบปัญหา',
                        text: 'รูปภาพเพิ่มเติมจะต้องเป็นไฟล์ .JPG/.JPEG/.jfif/.PNG เท่านั้น!',
                        footer: '<a href="#">ติดต่อผู้ดูแลระบบ?</a>'
                    })
                    return false;
                }
                if (input.accept.includes('application/pdf') && file.type !== pdfType) {
                    Swal.fire({
                        icon: 'error',
                        title: 'ตรวจพบปัญหา',
                        text: 'ไฟล์เอกสารเพิ่มเติมจะต้องเป็นไฟล์ PDF เท่านั้น',
                        footer: '<a href="#">ติดต่อผู้ดูแลระบบ?</a>'
                    })
                    return false;
                }
                if (input.accept.includes('application/msword') || input.accept.includes('application/vnd.openxmlformats-officedocument')) {
                    if (!docTypes.includes(file.type)) {
                        Swal.fire({
                            icon: 'error',
                            title: 'ตรวจพบปัญหา',
                            text: 'ไฟล์เอกสารเพิ่มเติมจะต้องเป็นไฟล์ .doc .docx .ppt .pptx .xls .xlsx เท่านั้น',
                            footer: '<a href="#">ติดต่อผู้ดูแลระบบ?</a>'
                        })
                        return false;
                    }
                }
            }
        }

        return true;
    }
</script>