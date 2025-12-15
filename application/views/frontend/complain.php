<div class="text-center pages-head">
    <span class="font-pages-head" id="dynamic-page-title">
        <?php echo isset($page_title) ? $page_title : 'แจ้งเรื่อง ร้องเรียน'; ?>
    </span>
</div>
</div>
<img src="<?php echo base_url('docs/welcome-btm-light-other.png'); ?>">

<!-- Modal สำหรับการยืนยันการแจ้งร้องเรียนโดยไม่เข้าสู่ระบบ -->
<div class="modal fade" id="guestConfirmModal" tabindex="-1" aria-labelledby="guestConfirmModalLabel"
    aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content"
            style="border: none; border-radius: 20px; box-shadow: 0 20px 60px rgba(255, 120, 73, 0.2), 0 8px 25px rgba(0,0,0,0.08); background: linear-gradient(135deg, #ffffff 0%, #fff7f0 100%); overflow: hidden;">
            <div class="modal-header"
                style="background: linear-gradient(135deg, rgba(255, 120, 73, 0.1) 0%, rgba(255, 107, 53, 0.1) 100%); color: #2c3e50; border-radius: 20px 20px 0 0; border-bottom: 1px solid rgba(255, 120, 73, 0.2); backdrop-filter: blur(10px);">
                <h5 class="modal-title" id="guestConfirmModalLabel"
                    style="font-weight: 600; color: #e55a2b; width: 100%; text-align: center;">
                    <i class="fas fa-exclamation-triangle me-2"
                        style="color: #ff7849;"></i>ยินดีต้อนรับสู่ระบบแจ้งเรื่อง ร้องเรียน
                </h5>
            </div>
            <div class="modal-body text-center"
                style="padding: 2.5rem; background: linear-gradient(135deg, #ffffff 0%, #fff7f0 100%);">
                <div class="mb-4">
                    <div
                        style="width: 80px; height: 80px; margin: 0 auto; background: linear-gradient(135deg, rgba(255, 120, 73, 0.15) 0%, rgba(255, 107, 53, 0.15) 100%); border-radius: 50%; display: flex; align-items: center; justify-content: center; box-shadow: 0 8px 25px rgba(255, 120, 73, 0.3);">
                        <i class="fas fa-user-shield"
                            style="font-size: 2.5rem; color: #e55a2b; text-shadow: 0 2px 8px rgba(255, 120, 73, 0.4);"></i>
                    </div>
                </div>
                <h5 class="mb-3" style="color: #2c3e50; font-weight: 600; text-shadow: 0 1px 3px rgba(0,0,0,0.1);">
                    เริ่มต้นการใช้งาน</h5>
                <p class="text-muted mb-4" style="font-size: 1.05rem; line-height: 1.6; color: #6c757d;">
                    เข้าสู่ระบบเพื่อติดตามสถานะการแจ้งเรื่อง ร้องเรียนและได้รับการแจ้งเตือน สะดวกรวดเร็ว
                    หรือดำเนินการต่อโดยไม่ต้องเข้าสู่ระบบ</p>

                <div class="d-grid gap-3">
                    <button type="button" class="btn btn-lg" onclick="redirectToLogin()"
                        style="background: linear-gradient(135deg, #ff7849 0%, #e55a2b 100%); border: none; color: white; border-radius: 15px; padding: 1rem 1.5rem; font-weight: 600; box-shadow: 0 6px 20px rgba(255, 120, 73, 0.4); transition: all 0.3s ease; font-size: 1.1rem;">
                        <i class="fas fa-sign-in-alt me-2"></i>เข้าสู่ระบบ
                    </button>
                    <button type="button" class="btn btn-lg" onclick="proceedAsGuest()"
                        style="background: rgba(255, 120, 73, 0.08); border: 2px solid rgba(255, 120, 73, 0.3); color: #e55a2b; border-radius: 15px; padding: 1rem 1.5rem; font-weight: 600; transition: all 0.3s ease; font-size: 1.1rem; backdrop-filter: blur(10px);">
                        <i class="fas fa-file-alt me-2"></i>ดำเนินการต่อโดยไม่เข้าสู่ระบบ
                    </button>
                </div>

                <!-- เพิ่ม decorative elements -->
                <div
                    style="position: absolute; top: -50px; right: -50px; width: 100px; height: 100px; background: linear-gradient(135deg, rgba(255, 120, 73, 0.08) 0%, rgba(255, 107, 53, 0.08) 100%); border-radius: 50%; z-index: -1;">
                </div>
                <div
                    style="position: absolute; bottom: -30px; left: -30px; width: 60px; height: 60px; background: linear-gradient(135deg, rgba(255, 107, 53, 0.08) 0%, rgba(255, 120, 73, 0.08) 100%); border-radius: 50%; z-index: -1;">
                </div>
            </div>
        </div>
    </div>
</div>

<div class="text-center pages-head">
    <span class="font-pages-head"
        style="font-size: 2.8rem; font-weight: 700; text-shadow: 1px 1px 3px rgba(108, 117, 125, 0.2);">แจ้งเรื่อง
        ร้องเรียน</span>
</div>

<div class="bg-pages" style="background: #ffffff; min-height: 100vh; padding: 2rem 0;">
    <div class="container-pages-news" style="position: relative; z-index: 10;">
        <div class="container-pages-news mb-5 mt-5"
            style="position: relative; z-index: 10; background: white; border-radius: 25px; box-shadow: 0 15px 35px rgba(0, 0, 0, 0.1); padding: 2rem; margin: 0 auto; max-width: 1000px; overflow: hidden;"
            id="complain_form">

            <!-- เพิ่ม decorative element -->
            <div
                style="position: absolute; top: 0; left: 0; right: 0; height: 5px; background: linear-gradient(90deg, #ff7849, #e55a2b, #ff7849); background-size: 200% 100%; animation: gradientShift 3s ease-in-out infinite;">
            </div>

            <!-- ปุ่มติดตามสถานะ - เพิ่มใหม่ -->
            <div class="d-flex justify-content-end mb-3">
                <a href="<?php echo site_url('Pages/follow_complain'); ?>" class="btn track-status-btn" style="background: linear-gradient(135deg, #e55a2b 0%, #ff7849 100%); 
                          border: none; 
                          color: white; 
                          padding: 0.7rem 1.5rem; 
                          border-radius: 12px; 
                          font-size: 0.95rem; 
                          font-weight: 600; 
                          transition: all 0.3s ease; 
                          box-shadow: 0 4px 15px rgba(255, 120, 73, 0.3); 
                          text-decoration: none;
                          position: relative;
                          overflow: hidden;">
                    <span style="position: relative; z-index: 2;">
                        <i class="fas fa-search me-2"></i>ติดตามสถานะแจ้งเรื่อง ร้องเรียน
                    </span>
                    <div style="position: absolute; top: 0; left: -100%; width: 100%; height: 100%; background: linear-gradient(90deg, transparent, rgba(255,255,255,0.2), transparent); transition: left 0.5s; z-index: 1;"
                        class="btn-shine-track"></div>
                </a>
            </div>

            <div class="underline">
                <form id="complainForm" action="<?php echo site_url('Pages/add_complain'); ?>" method="post"
                    class="form-horizontal" enctype="multipart/form-data" onsubmit="return false;">
                    <input type="hidden" name="form_token" id="formToken" value="">
                    <br>

                    <!-- หมวดหมู่เรื่องร้องเรียน - แบบ Category Cards -->
                    <div class="form-group mb-4">
                        <div class="form-label-wrapper">
                            <label class="form-label">
                                <i class="fas fa-list me-2" style="color: #e55a2b;"></i>
                                หมวดหมู่เรื่องร้องเรียน<span style="color: #e55a2b;">*</span>
                            </label>
                        </div>
                        <div class="col-sm-12">
                            <?php if (isset($category_error)): ?>
                                <div class="alert alert-warning">
                                    <i class="fas fa-exclamation-triangle me-2"></i>
                                    <?= $category_error ?>
                                </div>
                                <div class="category-card-grid disabled-grid">
                                    <div class="category-card disabled">
                                        <div class="category-card-icon">
                                            <i class="fas fa-exclamation-triangle" style="color: #6c757d;"></i>
                                        </div>
                                        <div class="category-card-content">
                                            <h4 class="category-title">ไม่สามารถโหลดหมวดหมู่ได้</h4>
                                            <p class="category-description">กรุณาลองใหม่อีกครั้ง</p>
                                        </div>
                                    </div>
                                </div>
                            <?php else: ?>
                                <!-- Hidden input สำหรับเก็บค่าที่เลือก (แทนที่ select) -->
                                <input type="hidden" name="complain_category_id" id="complain_category_id" required>

                                <!-- Grid สำหรับแสดง category buttons -->
                                <div class="category-card-grid">
                                    <?php if (!empty($complain_categories)): ?>
    <?php foreach ($complain_categories as $cat): ?>
        <div class="category-card" 
             data-category-id="<?= $cat->cat_id ?>"
             data-icon="<?= $cat->cat_icon ?>" 
             data-color="<?= $cat->cat_color ?>"
             data-category-name="<?= htmlspecialchars($cat->cat_name) ?>"
             data-category-description="<?= htmlspecialchars($cat->cat_description ?? 'หมวดหมู่ร้องเรียน') ?>">
            <div class="category-card-icon">
                <i class="<?= $cat->cat_icon ?>" style="color: <?= $cat->cat_color ?>"></i>
            </div>
            <div class="category-card-content">
                <h4 class="category-title"><?= htmlspecialchars($cat->cat_name) ?></h4>
                <p class="category-description">
                    <?= htmlspecialchars($cat->cat_description ?? 'หมวดหมู่ร้องเรียน') ?>
                </p>
            </div>
        </div>
    <?php endforeach; ?>
                                    <?php else: ?>
                                        <div class="category-card disabled">
                                            <div class="category-card-icon">
                                                <i class="fas fa-exclamation-circle" style="color: #6c757d;"></i>
                                            </div>
                                            <div class="category-card-content">
                                                <h4 class="category-title">ไม่มีหมวดหมู่</h4>
                                                <p class="category-description">ไม่สามารถโหลดหมวดหมู่ได้</p>
                                            </div>
                                        </div>
                                    <?php endif; ?>
                                </div>
                            <?php endif; ?>
                            <div class="invalid-feedback" id="complain_category_feedback"></div>
                        </div>
                    </div>


                    <!-- หัวข้อเรื่องร้องเรียน -->
                    <div class="form-group mb-4">
                        <div class="form-label-wrapper"
                            style="background: linear-gradient(135deg, rgba(255, 120, 73, 0.08) 0%, rgba(255, 107, 53, 0.08) 100%); border-radius: 12px; padding: 0.8rem 1.2rem; margin-bottom: 0.8rem; box-shadow: 0 4px 12px rgba(255, 120, 73, 0.15); backdrop-filter: blur(10px); transition: all 0.3s ease;">
                            <label class="form-label"
                                style="margin: 0; font-size: 1.1rem; font-weight: 600; color: #495057;">
                                <i class="fas fa-exclamation-circle me-2" style="color: #e55a2b;"></i>หัวข้อแจ้งเรื่อง
                                ร้องเรียน<span style="color: #e55a2b; margin-left: 0.2rem;">*</span>
                            </label>
                        </div>
                        <div class="col-sm-12">
                            <input type="text" name="complain_topic" class="form-control" required
                                placeholder="กรอกหัวข้อเรื่องร้องเรียน..."
                                style="border: none; border-radius: 15px; padding: 1rem; font-size: 1.1rem; transition: all 0.3s ease; box-shadow: 0 6px 20px rgba(255, 120, 73, 0.15); background: linear-gradient(135deg, #ffffff 0%, #f8f9fa 100%); backdrop-filter: blur(10px);">
                            <div class="invalid-feedback" id="complain_topic_feedback"></div>
                        </div>
                    </div>

                    <br>

                    <!-- ข้อมูลผู้แจ้ง -->
                    <div class="form-group mb-4">
                        <div class="form-check mb-3">
                            <input class="form-check-input" type="checkbox" id="anonymousCheck"
                                style="transform: scale(1.2); margin-right: 0.5rem;">
                            <label class="form-check-label" for="anonymousCheck"
                                style="font-size: 1rem; color: #495057;">
                                <i class="fas fa-user-secret me-2 text-muted"></i>นโยบายการคุ้มครองข้อมูลผู้แจ้งเบาะแส
                                โดยไม่ระบุตัวตน
                            </label>
                        </div>
                    </div>

                    <div class="row">
                        <!-- ชื่อ-นามสกุล -->
                        <div class="col-md-6">
                            <div class="form-group mb-4">
                                <div class="form-label-wrapper"
                                    style="background: linear-gradient(135deg, rgba(255, 120, 73, 0.08) 0%, rgba(255, 107, 53, 0.08) 100%); border-radius: 12px; padding: 0.8rem 1.2rem; margin-bottom: 0.8rem; box-shadow: 0 4px 12px rgba(255, 120, 73, 0.15); backdrop-filter: blur(10px); transition: all 0.3s ease;">
                                    <label class="form-label"
                                        style="margin: 0; font-size: 1.1rem; font-weight: 600; color: #495057;">
                                        <i class="fas fa-user me-2" style="color: #e55a2b;"></i>ชื่อ-นามสกุล<span
                                            style="color: #e55a2b; margin-left: 0.2rem;">*</span>
                                    </label>
                                </div>
                                <div class="col-sm-12">
                                    <input type="text" name="complain_by" id="complain_by" class="form-control" required
                                        placeholder="เช่น นาย สมชาย ใจดี"
                                        style="border: none; border-radius: 15px; padding: 1rem; font-size: 1.1rem; transition: all 0.3s ease; box-shadow: 0 6px 20px rgba(255, 120, 73, 0.15); background: linear-gradient(135deg, #ffffff 0%, #f8f9fa 100%); backdrop-filter: blur(10px);">
                                    <div class="invalid-feedback" id="complain_by_feedback"></div>
                                </div>
                            </div>
                        </div>

                        <!-- เบอร์โทรศัพท์ -->
                        <div class="col-md-6">
                            <div class="form-group mb-4">
                                <div class="form-label-wrapper"
                                    style="background: linear-gradient(135deg, rgba(255, 120, 73, 0.08) 0%, rgba(255, 107, 53, 0.08) 100%); border-radius: 12px; padding: 0.8rem 1.2rem; margin-bottom: 0.8rem; box-shadow: 0 4px 12px rgba(255, 120, 73, 0.15); backdrop-filter: blur(10px); transition: all 0.3s ease;">
                                    <label class="form-label"
                                        style="margin: 0; font-size: 1.1rem; font-weight: 600; color: #495057;">
                                        <i class="fas fa-phone me-2" style="color: #e55a2b;"></i>เบอร์โทรศัพท์<span
                                            style="color: #e55a2b; margin-left: 0.2rem;">*</span>
                                    </label>
                                </div>
                                <div class="col-sm-12">
                                    <input type="tel" name="complain_phone" id="complain_phone" class="form-control"
                                        required placeholder="เช่น 0812345678" pattern="\d{10}"
                                        title="กรุณากรอกเบอร์มือถือเป็นตัวเลข 10 ตัว"
                                        style="border: none; border-radius: 15px; padding: 1rem; font-size: 1.1rem; transition: all 0.3s ease; box-shadow: 0 6px 20px rgba(255, 120, 73, 0.15); background: linear-gradient(135deg, #ffffff 0%, #f8f9fa 100%); backdrop-filter: blur(10px);">
                                    <div class="invalid-feedback" id="complain_phone_feedback"></div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- อีเมล -->
                    <div class="form-group mb-4">
                        <div class="form-label-wrapper"
                            style="background: linear-gradient(135deg, rgba(255, 120, 73, 0.08) 0%, rgba(255, 107, 53, 0.08) 100%); border-radius: 12px; padding: 0.8rem 1.2rem; margin-bottom: 0.8rem; box-shadow: 0 4px 12px rgba(255, 120, 73, 0.15); backdrop-filter: blur(10px); transition: all 0.3s ease;">
                            <label class="form-label"
                                style="margin: 0; font-size: 1.1rem; font-weight: 600; color: #495057;">
                                <i class="fas fa-envelope me-2" style="color: #e55a2b;"></i>อีเมล<small
                                    style="color: #6c757d; font-weight: 400; margin-left: 0.5rem;">(Optional)</small>
                            </label>
                        </div>
                        <div class="col-sm-12">
                            <input type="email" name="complain_email" id="complain_email" class="form-control"
                                placeholder="เช่น somchai@gmail.com"
                                style="border: none; border-radius: 15px; padding: 1rem; font-size: 1.1rem; transition: all 0.3s ease; box-shadow: 0 6px 20px rgba(255, 120, 73, 0.15); background: linear-gradient(135deg, #ffffff 0%, #f8f9fa 100%); backdrop-filter: blur(10px);">
                            <div class="invalid-feedback" id="complain_email_feedback"></div>
                        </div>
                    </div>






                    <!-- *** เพิ่มใหม่: ระบบที่อยู่แบบละเอียด *** -->
                    <div class="form-group mb-4">
                        <div class="form-label-wrapper"
                            style="background: linear-gradient(135deg, rgba(255, 120, 73, 0.08) 0%, rgba(255, 107, 53, 0.08) 100%); border-radius: 12px; padding: 0.8rem 1.2rem; margin-bottom: 0.8rem; box-shadow: 0 4px 12px rgba(255, 120, 73, 0.15); backdrop-filter: blur(10px); transition: all 0.3s ease;">
                            <label class="form-label"
                                style="margin: 0; font-size: 1.1rem; font-weight: 600; color: #495057;">
                                <i class="fas fa-map-marker-alt me-2" style="color: #e55a2b;"></i>ที่อยู่<span
                                    style="color: #e55a2b; margin-left: 0.2rem;">*</span>
                            </label>
                        </div>



                        <!-- ที่อยู่เพิ่มเติม (บังคับกรอก) -->
                        <div class="row mb-3">
                            <div class="col-md-12">
                                <div class="input-wrapper">
                                    <input type="text" id="additional_address_field" class="form-control" required
                                        placeholder="กรอกที่อยู่เพิ่มเติม (บ้านเลขที่ ซอย ถนน หมู่บ้าน) *"
                                        style="border: none; border-radius: 15px; padding: 1rem; font-size: 1.1rem; transition: all 0.3s ease; box-shadow: 0 6px 20px rgba(255, 120, 73, 0.15); background: linear-gradient(135deg, #ffffff 0%, #f8f9fa 100%); backdrop-filter: blur(10px);">
                                    <i class="fas fa-map-marker-alt input-icon"
                                        style="position: absolute; right: 15px; top: 50%; transform: translateY(-50%); color: #e55a2b;"></i>
                                </div>
                                <small class="form-text text-muted">บ้านเลขที่ ซอย ถนน หรือรายละเอียดเพิ่มเติม</small>
                            </div>
                        </div>




                        <!-- รหัสไปรษณีย์ -->
                        <div class="row mb-3">
                            <div class="col-md-6">
                                <div class="input-wrapper">
                                    <input type="text" id="zipcode_field" class="form-control"
                                        placeholder="กรอกรหัสไปรษณีย์ 5 หลัก" maxlength="5" pattern="[0-9]{5}"
                                        style="border: none; border-radius: 15px; padding: 1rem; font-size: 1.1rem; transition: all 0.3s ease; box-shadow: 0 6px 20px rgba(255, 120, 73, 0.15); background: linear-gradient(135deg, #ffffff 0%, #f8f9fa 100%); backdrop-filter: blur(10px);">
                                    <i class="fas fa-mail-bulk input-icon"
                                        style="position: absolute; right: 15px; top: 50%; transform: translateY(-50%); color: #e55a2b;"></i>
                                </div>
                                <small class="form-text text-muted">กรอกรหัสไปรษณีย์เพื่อเติมข้อมูลอัตโนมัติ</small>

                                <!-- Loading & Error indicators -->
                                <div id="zipcode_loading" class="text-center mt-1" style="display: none;">
                                    <small class="text-primary">
                                        <i class="fas fa-spinner fa-spin"></i> กำลังค้นหา...
                                    </small>
                                </div>
                                <div id="zipcode_error" class="mt-1" style="display: none;">
                                    <small class="text-danger"></small>
                                </div>
                            </div>
                        </div>

                        <!-- จังหวัด และ อำเภอ -->
                        <div class="row mb-3">
                            <div class="col-md-6">
                                <div class="input-wrapper">
                                    <input type="text" id="province_field" class="form-control" placeholder="จังหวัด"
                                        readonly
                                        style="border: none; border-radius: 15px; padding: 1rem; font-size: 1.1rem; transition: all 0.3s ease; box-shadow: 0 6px 20px rgba(255, 120, 73, 0.15); background: linear-gradient(135deg, #ffffff 0%, #f8f9fa 100%); backdrop-filter: blur(10px);">
                                    <i class="fas fa-map input-icon"
                                        style="position: absolute; right: 15px; top: 50%; transform: translateY(-50%); color: #e55a2b;"></i>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="input-wrapper">
                                    <select id="amphoe_field" class="form-control" disabled
                                        style="border: none; border-radius: 15px; padding: 1rem; font-size: 1.1rem; transition: all 0.3s ease; box-shadow: 0 6px 20px rgba(255, 120, 73, 0.15); background: linear-gradient(135deg, #ffffff 0%, #f8f9fa 100%); backdrop-filter: blur(10px);">
                                        <option value="">เลือกอำเภอ</option>
                                    </select>
                                    <i class="fas fa-city input-icon"
                                        style="position: absolute; right: 15px; top: 50%; transform: translateY(-50%); color: #e55a2b;"></i>
                                </div>
                            </div>
                        </div>

                        <!-- ตำบล -->
                        <div class="row mb-3">
                            <div class="col-md-6">
                                <div class="form-label-wrapper"
                                    style="background: linear-gradient(135deg, rgba(255, 120, 73, 0.08) 0%, rgba(255, 107, 53, 0.08) 100%); border-radius: 12px; padding: 0.8rem 1.2rem; margin-bottom: 0.8rem; box-shadow: 0 4px 12px rgba(255, 120, 73, 0.15); backdrop-filter: blur(10px); transition: all 0.3s ease;">
                                    <label class="form-label"
                                        style="margin: 0; font-size: 1.1rem; font-weight: 600; color: #495057;">
                                        <i class="fas fa-home me-2" style="color: #e55a2b;"></i>ตำบล<span
                                            style="color: #e55a2b; margin-left: 0.2rem;">*</span>
                                    </label>
                                </div>
                                <div class="input-wrapper">
                                    <select id="district_field" name="district_field" class="form-control" disabled
                                        required
                                        style="border: none; border-radius: 15px; padding: 1rem; font-size: 1.1rem; transition: all 0.3s ease; box-shadow: 0 6px 20px rgba(255, 120, 73, 0.15); background: linear-gradient(135deg, #ffffff 0%, #f8f9fa 100%); backdrop-filter: blur(10px);">
                                        <option value="">เลือกตำบล *</option>
                                    </select>
                                    <i class="fas fa-home input-icon"
                                        style="position: absolute; right: 15px; top: 50%; transform: translateY(-50%); color: #e55a2b;"></i>
                                </div>
                                <div class="invalid-feedback" id="district_field_feedback"></div>
                            </div>
                        </div>





                        <!-- ที่อยู่รวม (ซ่อน - ส่งไปยัง complain_address) -->
                        <input type="hidden" name="complain_address" id="full_address_field" value="">

                        <!-- *** เพิ่มใหม่: ซ่อน hidden fields สำหรับข้อมูลที่อยู่แยก *** -->
                        <input type="hidden" name="guest_province" id="guest_province_field" value="">
                        <input type="hidden" name="guest_amphoe" id="guest_amphoe_field" value="">
                        <input type="hidden" name="guest_district" id="guest_district_field" value="">
                        <input type="hidden" name="guest_zipcode" id="guest_zipcode_field" value="">

                        <!-- แสดงที่อยู่ที่รวมแล้ว -->
                        <div class="row">
                            <div class="col-md-12">
                                <div class="alert alert-info" id="address_preview"
                                    style="display: none; border-radius: 15px; background: linear-gradient(135deg, rgba(255, 140, 66, 0.1) 0%, rgba(255, 107, 53, 0.1) 100%); border: 1px solid rgba(255, 140, 66, 0.3);">
                                    <strong><i class="fas fa-eye"></i> ที่อยู่ที่จะบันทึก:</strong>
                                    <div id="address_preview_text"></div>
                                </div>
                            </div>
                        </div>




                        <div class="invalid-feedback" id="complain_address_feedback"></div>
                    </div>

                    <br>

                    <!-- รายละเอียด -->
                    <div class="form-group mb-4">
                        <div class="form-label-wrapper"
                            style="background: linear-gradient(135deg, rgba(255, 120, 73, 0.08) 0%, rgba(255, 107, 53, 0.08) 100%); border-radius: 12px; padding: 0.8rem 1.2rem; margin-bottom: 0.8rem; box-shadow: 0 4px 12px rgba(255, 120, 73, 0.15); backdrop-filter: blur(10px); transition: all 0.3s ease;">
                            <label for="complainDetailTextarea" class="form-label"
                                style="margin: 0; font-size: 1.1rem; font-weight: 600; color: #495057;">
                                <i class="fas fa-align-left me-2"
                                    style="color: #e55a2b;"></i>รายละเอียดเรื่องร้องเรียน<span
                                    style="color: #e55a2b; margin-left: 0.2rem;">*</span>
                            </label>
                        </div>
                        <div class="col-sm-12">
                            <textarea name="complain_detail" class="form-control" id="complainDetailTextarea" rows="6"
                                required placeholder="กรอกรายละเอียดเพิ่มเติม..."
                                style="border: none; border-radius: 15px; padding: 1rem; font-size: 1.1rem; transition: all 0.3s ease; box-shadow: 0 6px 20px rgba(255, 120, 73, 0.15); background: linear-gradient(135deg, #ffffff 0%, #f8f9fa 100%); backdrop-filter: blur(10px); resize: vertical;"></textarea>
                            <div class="invalid-feedback" id="complain_detail_feedback"></div>
                        </div>
                    </div>

                    <br>

                    <div class="row" style="padding-bottom: 20px;">
                        <!-- รูปภาพ -->
                        <div class="col-9">
                            <div class="form-group">
                                <div class="form-label-wrapper"
                                    style="background: linear-gradient(135deg, rgba(255, 120, 73, 0.08) 0%, rgba(255, 107, 53, 0.08) 100%); border-radius: 12px; padding: 0.8rem 1.2rem; margin-bottom: 0.8rem; box-shadow: 0 4px 12px rgba(255, 120, 73, 0.15); backdrop-filter: blur(10px); transition: all 0.3s ease;">
                                    <label class="form-label"
                                        style="margin: 0; font-size: 1.1rem; font-weight: 600; color: #495057;">
                                        <i class="fas fa-images me-2" style="color: #e55a2b;"></i>รูปภาพประกอบ<small
                                            style="color: #6c757d; font-weight: 400; margin-left: 0.5rem;">(สามารถเพิ่มได้หลายรูป)</small>
                                    </label>
                                </div>
                                <div class="col-sm-12">
                                    <!-- File Upload Zone -->
                                    <div class="file-upload-wrapper"
                                        style="border: 2px dashed #dee2e6; border-radius: 15px; padding: 1.5rem; text-align: center; background: linear-gradient(135deg, #ffffff 0%, #f8f9fa 100%); transition: all 0.3s ease; cursor: pointer; box-shadow: 0 6px 20px rgba(255, 120, 73, 0.15); backdrop-filter: blur(10px);"
                                        ondrop="handleDrop(event)" ondragover="handleDragOver(event)"
                                        ondragenter="handleDragEnter(event)" ondragleave="handleDragLeave(event)">
                                        <div id="upload-placeholder" class="upload-placeholder">
                                            <i class="fas fa-cloud-upload-alt"
                                                style="font-size: 2rem; color: #e55a2b; margin-bottom: 0.5rem;"></i>
                                            <p style="margin: 0; color: #6c757d; font-size: 1rem;">คลิกเพื่อเลือกรูปภาพ
                                                หรือลากไฟล์มาวางที่นี่</p>
                                            <small class="text-muted mt-2 d-block">รองรับไฟล์: JPG, JPEG, PNG (สูงสุด 5
                                                รูป)(ไม่เกิน 5 MB)</small>
                                        </div>
                                    </div>
                                    <input type="file" id="complain_imgs" name="complain_imgs[]" class="form-control"
                                        accept="image/*" multiple onchange="handleFileSelect(this)"
                                        style="display: none;">

                                    <!-- File Preview Area -->
                                    <div id="file-preview-area" class="file-preview-area mt-3" style="display: none;">
                                        <div class="d-flex justify-content-between align-items-center mb-2">
                                            <span class="text-muted" style="font-size: 0.9rem;">
                                                <i class="fas fa-images me-1"></i>ไฟล์ที่เลือก (<span
                                                    id="file-count">0</span>/5)
                                            </span>
                                            <button type="button" class="btn btn-outline-danger btn-sm"
                                                onclick="clearAllFiles()"
                                                style="border-radius: 8px; font-size: 0.8rem;">
                                                <i class="fas fa-times me-1"></i>ลบทั้งหมด
                                            </button>
                                        </div>
                                        <div id="preview-container" class="preview-container"
                                            style="display: grid; grid-template-columns: repeat(auto-fill, minmax(120px, 1fr)); gap: 1rem; max-height: 300px; overflow-y: auto; padding: 1rem; background: #f8f9fa; border-radius: 10px; border: 1px solid #e9ecef;">
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- ปุ่มส่ง -->
                        <div class="col-3">
                            <div class="d-flex justify-content-end mt-4">
                                <button type="button" id="submitComplainBtn" class="btn modern-submit-btn"
                                    onclick="handleComplainSubmit(event)"
                                    style="background: linear-gradient(135deg, #ff7849 0%, #e55a2b 100%); border: none; color: white; padding: 1rem 2rem; border-radius: 15px; font-size: 1.1rem; font-weight: 600; transition: all 0.3s ease; box-shadow: 0 6px 20px rgba(255, 120, 73, 0.3); position: relative; overflow: hidden; min-width: 150px;">
                                    <span style="position: relative; z-index: 2;">
                                        <i class="fas fa-paper-plane me-2"></i>ส่งเรื่องร้องเรียน
                                    </span>
                                    <div style="position: absolute; top: 0; left: -100%; width: 100%; height: 100%; background: linear-gradient(90deg, transparent, rgba(255,255,255,0.2), transparent); transition: left 0.5s; z-index: 1;"
                                        class="btn-shine"></div>
                                </button>
                            </div>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>




<style>
    /* เพิ่ม CSS สำหรับ sortable */
    #sortable .ui-sortable-helper {
        background: #f8f9fa;
        box-shadow: 0 5px 15px rgba(0, 0, 0, 0.3);
    }

    .fa-grip-vertical {
        cursor: move;
    }

    .permission-info {
        margin-bottom: 1rem;
        padding: 1rem;
        border-radius: 0.5rem;
    }

    .permission-info.system-admin {
        background: linear-gradient(135deg, #fff3cd 0%, #ffeaa7 100%);
        border: 1px solid #ffc107;
    }

    .permission-info.super-admin {
        background: linear-gradient(135deg, #e8f5e8 0%, #c3e6cb 100%);
        border: 1px solid #28a745;
    }
</style>


<!-- *** CSS รวมการปรับปรุงใหม่ *** -->
<style>
    /* Input wrapper สำหรับ icon positioning */
    .input-wrapper {
        position: relative;
        display: inline-block;
        width: 100%;
    }

    .input-icon {
        position: absolute;
        right: 15px;
        top: 50%;
        transform: translateY(-50%);
        color: #e55a2b;
        pointer-events: none;
        z-index: 2;
    }

    /* CSS สำหรับปุ่มติดตามสถานะ */
    .track-status-btn:hover {
        transform: translateY(-2px);
        box-shadow: 0 6px 20px rgba(255, 120, 73, 0.4) !important;
        background: linear-gradient(135deg, #d4491c 0%, #e55a2b 100%) !important;
        color: white !important;
        text-decoration: none !important;
    }

    .track-status-btn:hover .btn-shine-track {
        left: 100%;
    }

    .track-status-btn:active {
        transform: translateY(-1px);
    }

    .track-status-btn:focus {
        outline: none;
        box-shadow: 0 6px 20px rgba(255, 120, 73, 0.4) !important;
    }

    @keyframes gradientShift {

        0%,
        100% {
            background-position: 0% 50%;
        }

        50% {
            background-position: 100% 50%;
        }
    }

    /* เพิ่ม hover effect สำหรับ form labels */
    .form-label-wrapper:hover {
        background: linear-gradient(135deg, rgba(255, 120, 73, 0.12) 0%, rgba(255, 107, 53, 0.12) 100%) !important;
        box-shadow: 0 6px 16px rgba(255, 120, 73, 0.2) !important;
        transform: translateY(-2px);
    }

    .form-control:focus,
    .form-select:focus {
        border-color: transparent !important;
        box-shadow: 0 8px 25px rgba(255, 120, 73, 0.25) !important;
        transform: translateY(-1px);
        background: linear-gradient(135deg, #ffffff 0%, #fff7f0 100%) !important;
    }

    .modern-submit-btn:hover {
        transform: translateY(-3px);
        box-shadow: 0 10px 30px rgba(255, 120, 73, 0.4) !important;
        background: linear-gradient(135deg, #e55a2b 0%, #d4491c 100%) !important;
    }

    .modern-submit-btn:hover .btn-shine {
        left: 100%;
    }

    .modern-submit-btn:active {
        transform: translateY(-1px);
    }

    /* File Upload Styles */
    .file-upload-wrapper {
        transition: all 0.3s ease;
    }

    .file-upload-wrapper:hover {
        background: linear-gradient(135deg, #fff7f0 0%, #ffeee6 100%) !important;
        box-shadow: 0 8px 25px rgba(255, 120, 73, 0.2) !important;
        transform: translateY(-2px);
        border-color: #e55a2b !important;
    }

    .file-upload-wrapper.drag-over {
        background: linear-gradient(135deg, #ffeee6 0%, #fde6d9 100%) !important;
        border-color: #ff7849 !important;
        box-shadow: 0 8px 25px rgba(255, 120, 73, 0.3) !important;
        transform: scale(1.02);
    }

    /* File Preview Styles */
    .preview-item {
        position: relative;
        background: white;
        border-radius: 8px;
        overflow: hidden;
        box-shadow: 0 2px 8px rgba(0, 0, 0, 0.1);
        transition: all 0.3s ease;
    }

    .preview-item:hover {
        transform: translateY(-2px);
        box-shadow: 0 4px 15px rgba(0, 0, 0, 0.15);
    }

    .preview-image {
        width: 100%;
        height: 80px;
        object-fit: cover;
        border-radius: 6px 6px 0 0;
    }

    .preview-info {
        padding: 0.5rem;
        background: #f8f9fa;
    }

    .preview-name {
        font-size: 0.7rem;
        color: #495057;
        margin: 0;
        white-space: nowrap;
        overflow: hidden;
        text-overflow: ellipsis;
        font-weight: 500;
    }

    .preview-size {
        font-size: 0.6rem;
        color: #6c757d;
        margin: 0;
    }

    .remove-file {
        position: absolute;
        top: 4px;
        right: 4px;
        background: rgba(229, 90, 43, 0.9);
        color: white;
        border: none;
        border-radius: 50%;
        width: 20px;
        height: 20px;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 0.7rem;
        cursor: pointer;
        transition: all 0.2s ease;
        backdrop-filter: blur(4px);
    }

    .remove-file:hover {
        background: rgba(229, 90, 43, 1);
        transform: scale(1.1);
    }

    /* Progress animation */
    @keyframes uploadProgress {
        0% {
            width: 0%;
        }

        100% {
            width: 100%;
        }
    }

    .upload-progress {
        position: absolute;
        bottom: 0;
        left: 0;
        height: 3px;
        background: linear-gradient(90deg, #ff7849, #e55a2b);
        border-radius: 0 0 6px 6px;
        animation: uploadProgress 1.5s ease-in-out;
    }

    /* Anonymous checkbox styling */
    .form-check-input:checked {
        background-color: #e55a2b;
        border-color: #e55a2b;
    }

    .form-check-input:focus {
        border-color: #e55a2b;
        outline: 0;
        box-shadow: 0 0 0 0.25rem rgba(255, 120, 73, 0.25);
    }

    /* เพิ่ม hover effects สำหรับปุ่มใน modal */
    .modal-body button:hover {
        transform: translateY(-2px);
        transition: all 0.3s ease;
    }

    .modal-body button:first-of-type:hover {
        background: linear-gradient(135deg, #e55a2b 0%, #d4491c 100%) !important;
        box-shadow: 0 8px 25px rgba(255, 120, 73, 0.5) !important;
    }

    .modal-body button:last-of-type:hover {
        background: linear-gradient(135deg, rgba(255, 120, 73, 0.15) 0%, rgba(229, 90, 43, 0.15) 100%) !important;
        border-color: rgba(255, 120, 73, 0.5) !important;
        box-shadow: 0 6px 20px rgba(255, 120, 73, 0.3) !important;
    }

    .modal-body button:active {
        transform: translateY(0);
    }

    /* เพิ่ม animation สำหรับ modal */
    .modal.fade .modal-dialog {
        transform: scale(0.8) translateY(-50px);
        transition: all 0.3s ease;
    }

    .modal.show .modal-dialog {
        transform: scale(1) translateY(0);
    }

    /* เพิ่ม glassmorphism effect */
    .modal-header {
        backdrop-filter: blur(10px) !important;
    }

    /* เพิ่ม loading animation */
    .loading-spinner {
        animation: spin 1s linear infinite;
    }

    @keyframes spin {
        0% {
            transform: rotate(0deg);
        }

        100% {
            transform: rotate(360deg);
        }
    }

    /* Responsive design */
    @media (max-width: 768px) {
        .font-pages-head {
            font-size: 2rem !important;
        }

        .container-pages-news {
            margin: 0 1rem !important;
            padding: 1.5rem !important;
        }

        .row .col-md-6 {
            width: 100% !important;
            margin-bottom: 1rem;
        }

        .col-9,
        .col-3 {
            width: 100% !important;
        }

        .preview-container {
            grid-template-columns: repeat(auto-fill, minmax(100px, 1fr)) !important;
        }

        .track-status-btn {
            font-size: 0.85rem !important;
            padding: 0.6rem 1.2rem !important;
        }
    }
</style>

<style>
    /* *** เพิ่ม CSS สำหรับ Category Card Grid *** */
    /* Category Card Grid */
    .category-card-grid {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(300px, 1fr));
        gap: 1.2rem;
        margin-top: 1rem;
    }

    .category-card-grid.disabled-grid {
        opacity: 0.6;
        pointer-events: none;
    }

    /* Category Card Style */
    .category-card {
        display: flex;
        align-items: flex-start;
        padding: 1.5rem;
        border: 2px solid #e9ecef;
        border-radius: 12px;
        background: #ffffff;
        cursor: pointer;
        transition: all 0.3s ease;
        box-shadow: 0 2px 8px rgba(0, 0, 0, 0.04);
        position: relative;
        overflow: hidden;
    }

    .category-card:hover {
        border-color: #e55a2b;
        background: #fff7f0;
        transform: translateY(-2px);
        box-shadow: 0 6px 20px rgba(229, 90, 43, 0.12);
    }

    .category-card.selected {
        border-color: #e55a2b;
        background: linear-gradient(135deg, #fff7f0 0%, #ffeee6 100%);
        box-shadow: 0 8px 25px rgba(229, 90, 43, 0.2);
        transform: translateY(-3px);
    }

    .category-card.selected::before {
        content: '';
        position: absolute;
        top: 0;
        left: 0;
        right: 0;
        height: 4px;
        background: linear-gradient(90deg, #e55a2b, #ff7849);
    }

    .category-card.disabled {
        opacity: 0.6;
        cursor: not-allowed;
        pointer-events: none;
    }

    /* Category Card Icon */
    .category-card-icon {
        flex-shrink: 0;
        width: 60px;
        height: 60px;
        display: flex;
        align-items: center;
        justify-content: center;
        border-radius: 12px;
        background: rgba(229, 90, 43, 0.1);
        margin-right: 1.2rem;
        transition: all 0.3s ease;
    }

    .category-card-icon i {
        font-size: 1.8rem;
        transition: all 0.3s ease;
    }

    .category-card.selected .category-card-icon {
        background: rgba(229, 90, 43, 0.15);
        transform: scale(1.05);
    }

    /* Category Card Content */
    .category-card-content {
        flex: 1;
    }

    .category-title {
        margin: 0 0 0.5rem 0;
        font-size: 1.1rem;
        font-weight: 600;
        color: #2c3e50;
        line-height: 1.3;
    }

    .category-description {
        margin: 0;
        font-size: 0.9rem;
        color: #6c757d;
        line-height: 1.4;
    }

    .category-card.selected .category-title {
        color: #e55a2b;
    }

    .category-card.selected .category-description {
        color: #8b4513;
    }

    /* Selected Animation */
    @keyframes selectedGlow {
        0% {
            box-shadow: 0 8px 25px rgba(229, 90, 43, 0.2);
        }

        50% {
            box-shadow: 0 10px 30px rgba(229, 90, 43, 0.25);
        }

        100% {
            box-shadow: 0 8px 25px rgba(229, 90, 43, 0.2);
        }
    }

    .category-card.selected {
        animation: selectedGlow 3s ease-in-out infinite;
    }

    /* Validation Error State */
    .category-card-grid.error {
        border: 2px dashed #dc3545;
        border-radius: 10px;
        padding: 1rem;
        background: rgba(220, 53, 69, 0.05);
    }

    .category-card-grid.error::before {
        content: 'กรุณาเลือกหมวดหมู่เรื่องร้องเรียน';
        display: block;
        color: #dc3545;
        font-size: 0.875rem;
        font-weight: 600;
        text-align: center;
        margin-bottom: 1rem;
    }

    /* Ripple Effect */
    .category-card::after {
        content: '';
        position: absolute;
        top: 50%;
        left: 50%;
        width: 0;
        height: 0;
        background: rgba(229, 90, 43, 0.1);
        border-radius: 50%;
        transform: translate(-50%, -50%);
        transition: all 0.6s ease;
        pointer-events: none;
    }

    .category-card:active::after {
        width: 200px;
        height: 200px;
    }

    /* Responsive */
    @media (max-width: 768px) {
        .category-card-grid {
            grid-template-columns: 1fr;
            gap: 1rem;
        }

        .category-card {
            padding: 1.2rem;
        }

        .category-card-icon {
            width: 50px;
            height: 50px;
            margin-right: 1rem;
        }

        .category-card-icon i {
            font-size: 1.5rem;
        }

        .category-title {
            font-size: 1rem;
        }

        .category-description {
            font-size: 0.85rem;
        }
    }

    @media (max-width: 576px) {
        .category-card {
            padding: 1rem;
            flex-direction: column;
            text-align: center;
        }

        .category-card-icon {
            margin: 0 auto 1rem auto;
        }
    }
</style>

<!-- Font Awesome สำหรับ icons -->
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">

<!-- Bootstrap CSS และ JS -->
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>

<!-- SweetAlert2 -->
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

<script>
	/**
 * *** ฟังก์ชันอัปเดตชื่อหัวข้อตามหมวดหมู่ที่เลือก ***
 */
function updatePageTitle(categoryName, animate = true) {
    try {
        const titleElement = document.getElementById('dynamic-page-title');
        
        if (!titleElement) {
            console.warn('⚠️ Page title element not found');
            return;
        }
        
        // สร้างชื่อหัวข้อใหม่
        const newTitle = categoryName ? 
            `แจ้งเรื่อง ร้องเรียน ${categoryName}` : 
            'แจ้งเรื่อง ร้องเรียน';
        
        // ถ้าต้องการ animation
        if (animate) {
            // Fade out
            titleElement.style.transition = 'opacity 0.3s ease, transform 0.3s ease';
            titleElement.style.opacity = '0';
            titleElement.style.transform = 'translateY(-10px)';
            
            // เปลี่ยนข้อความและ fade in
            setTimeout(() => {
                titleElement.textContent = newTitle;
                titleElement.style.opacity = '1';
                titleElement.style.transform = 'translateY(0)';
            }, 300);
        } else {
            // เปลี่ยนทันที (ไม่มี animation)
            titleElement.textContent = newTitle;
        }
        
        console.log('✅ Page title updated:', newTitle);
        
    } catch (error) {
        console.error('❌ Update page title error:', error);
    }
}

/**
 * *** ฟังก์ชันรีเซ็ตชื่อหัวข้อกลับเป็นค่า default ***
 */
function resetPageTitle() {
    updatePageTitle('', true);
}

/**
 * *** ฟังก์ชันอัปเดตชื่อหัวข้อพร้อม icon ***
 */
function updatePageTitleWithIcon(categoryName, categoryColor) {
    try {
        const titleElement = document.getElementById('dynamic-page-title');
        
        if (!titleElement) {
            console.warn('⚠️ Page title element not found');
            return;
        }
        
        // สร้างชื่อหัวข้อพร้อม icon
        const newTitle = categoryName ? 
            `แจ้งเรื่อง ร้องเรียน` : 
            'แจ้งเรื่อง ร้องเรียน';
        
        const iconHTML = categoryIcon && categoryName ? 
            `<i class="${categoryIcon}" style="color: ${categoryColor}; margin-right: 0.5rem;"></i>` : 
            '';
        
        const categoryHTML = categoryName ? 
            `<span style="color: ${categoryColor}; font-weight: 700;">${categoryName}</span>` : 
            '';
        
        // Animation
        titleElement.style.transition = 'opacity 0.3s ease, transform 0.3s ease';
        titleElement.style.opacity = '0';
        titleElement.style.transform = 'translateY(-10px)';
        
        setTimeout(() => {
            titleElement.innerHTML = categoryName ? 
                `${newTitle}: ${iconHTML}${categoryHTML}` : 
                newTitle;
            titleElement.style.opacity = '1';
            titleElement.style.transform = 'translateY(0)';
        }, 300);
        
        console.log('✅ Page title with icon updated');
        
    } catch (error) {
        console.error('❌ Update page title with icon error:', error);
    }
}
	
	/**
 * *** ฟังก์ชันเลือก Category โดยอัตโนมัติจาก URL ***
 */
function autoSelectCategoryFromURL() {
    try {
        const selectedCategoryId = <?= json_encode($selected_category_id ?? null); ?>;
        
        console.log('🎯 Auto-selecting category ID:', selectedCategoryId);
        
        if (!selectedCategoryId) {
            console.log('ℹ️ No category ID to auto-select');
            return;
        }
        
        const targetCard = document.querySelector(`.category-card[data-category-id="${selectedCategoryId}"]`);
        
        if (targetCard) {
            // ลบ selected class จากการ์ดอื่นๆ
            document.querySelectorAll('.category-card').forEach(card => {
                card.classList.remove('selected');
            });
            
            // เพิ่ม selected class ให้การ์ดที่เลือก
            targetCard.classList.add('selected');
            
            // ตั้งค่าใน hidden input
            const hiddenInput = document.getElementById('complain_category_id');
            if (hiddenInput) {
                hiddenInput.value = selectedCategoryId;
                hiddenInput.dispatchEvent(new Event('change'));
            }
            
            // *** เพิ่มใหม่: อัปเดตชื่อหัวข้อ ***
            const categoryName = targetCard.dataset.categoryName;
            const categoryIcon = targetCard.dataset.icon;
            const categoryColor = targetCard.dataset.color;
            
            // อัปเดตชื่อหัวข้อ (ใช้ animate = false เพราะเป็นการโหลดครั้งแรก)
            setTimeout(() => {
                updatePageTitleWithIcon(categoryName, categoryIcon, categoryColor);
            }, 500);
            
            // ลบ error state
            const cardGrid = document.querySelector('.category-card-grid');
            if (cardGrid) {
                cardGrid.classList.remove('error');
            }
            
            const feedbackElement = document.getElementById('complain_category_feedback');
            if (feedbackElement) {
                feedbackElement.textContent = '';
            }
            
            // Scroll ไปที่การ์ด
            targetCard.scrollIntoView({ 
                behavior: 'smooth', 
                block: 'center' 
            });
            
            showAlert('success', 'เลือกหมวดหมู่แล้ว', 
                `เลือกหมวดหมู่: ${categoryName}`, 
                2000
            );
            
            console.log('✅ Category auto-selected and title updated');
        } else {
            console.warn('⚠️ Category card not found for ID:', selectedCategoryId);
            showAlert('warning', 'ไม่พบหมวดหมู่', 
                'ไม่พบหมวดหมู่ที่เลือก กรุณาเลือกหมวดหมู่ใหม่'
            );
        }
        
    } catch (error) {
        console.error('❌ Auto-select category error:', error);
    }
}
    // ==============================================
    // แก้ไข Error และเพิ่ม Error Handling
    // ==============================================

    // แก้ไข 1: เพิ่ม error handling และป้องกัน console error
    window.addEventListener('error', function (e) {
        // ป้องกัน error จาก extension หรือ external script
        if (e.message && (e.message.includes('message channel') || e.message.includes('check_login_status'))) {
            e.preventDefault();
            return true;
        }
    });

    // แก้ไข 2: เพิ่ม check_login_status function ที่หายไป
    function check_login_status() {
        try {
            // ตรวจสอบสถานะ login จากตัวแปรที่มีอยู่
            return typeof window.isUserLoggedIn !== 'undefined' ? window.isUserLoggedIn : false;
        } catch (error) {
            console.log('Check login status error (ignored):', error);
            return false;
        }
    }

    // แก้ไข 3: ป้องกัน error จาก undefined variables
    const safeGetVariable = (varName, defaultValue = null) => {
        try {
            return typeof window[varName] !== 'undefined' ? window[varName] : defaultValue;
        } catch (e) {
            return defaultValue;
        }
    };

    // ==============================================
    // ตัวแปร Global ที่ปลอดภัย
    // ==============================================

    // รับข้อมูลการ login จาก PHP (คืนค่าเป็นรูปแบบเดิม)
    const isUserLoggedIn = <?= json_encode($is_logged_in ?? false); ?>;
    const userInfo = <?= json_encode($user_info ?? null); ?>;
    const userAddress = <?= json_encode($user_address ?? null); ?>; // *** เพิ่มข้อมูลที่อยู่ ***

    // ตัวแปรสำหรับเก็บสถานะ guest
    let hasConfirmedAsGuest = isUserLoggedIn; // ถ้า login แล้วถือว่ายืนยันแล้ว

    // ตัวแปรสำหรับเก็บ modal instance
    let guestModalInstance = null;

    // ตัวแปรสำหรับเก็บไฟล์ที่เลือก
    let selectedFiles = [];
    const maxFiles = 5;
    const maxFileSize = 5 * 1024 * 1024; // 5MB
    const allowedTypes = ['image/jpeg', 'image/jpg', 'image/png', 'image/gif', 'image/webp'];


    const compressionSettings = {
        quality: 0.9,
        maxWidth: 1920,
        maxHeight: 1080
    };




    // ตัวแปรป้องกันการส่งซ้ำ
    let formSubmitting = false;

    // *** เพิ่มตัวแปรสำหรับ Address System ***
    const API_BASE_URL = 'https://addr.assystem.co.th/index.php/zip_api';
    let zipcodeField, provinceField, amphoeField, districtField, fullAddressField, additionalAddressField;
    let currentAddressData = [];

    // Debug ข้อมูล login status
    console.log('Login Status:', isUserLoggedIn);
    console.log('User Info:', userInfo);
    console.log('User Address:', userAddress); // *** Debug ข้อมูลที่อยู่ ***

    // ==============================================
    // ฟังก์ชันหลัก
    // ==============================================

    // ฟังก์ชันสร้าง form token
    function generateFormToken() {
        return Date.now().toString(36) + Math.random().toString(36).substr(2);
    }

    // ==============================================
    // *** ฟังก์ชันตั้งค่า Category Card Selection ***
    // ==============================================

    function setupCategoryCardSelection() {
        try {
            const categoryCards = document.querySelectorAll('.category-card:not(.disabled)');
            const hiddenInput = document.getElementById('complain_category_id');
            const feedbackElement = document.getElementById('complain_category_feedback');
            const cardGrid = document.querySelector('.category-card-grid');

            if (!hiddenInput || !cardGrid) {
                console.log('Category card elements not found');
                return;
            }

            categoryCards.forEach(card => {
                card.addEventListener('click', function () {
                    // ลบ selected class จากการ์ดอื่นๆ
                    categoryCards.forEach(c => c.classList.remove('selected'));

                    // เพิ่ม selected class ให้การ์ดที่คลิก
                    this.classList.add('selected');

                    // ตั้งค่าใน hidden input
                    const categoryId = this.dataset.categoryId;
                    hiddenInput.value = categoryId;
					
					// *** เพิ่มใหม่: อัปเดตชื่อหัวข้อ ***
                const categoryName = this.dataset.categoryName;
					
					updatePageTitle(categoryName, true);

                    // ลบ error state ถ้ามี
                    cardGrid.classList.remove('error');
                    if (feedbackElement) {
                        feedbackElement.textContent = '';
                    }

                    // เรียก event สำหรับ form validation
                    hiddenInput.dispatchEvent(new Event('change'));

                    console.log('Selected category:', categoryId);
                });

                // เพิ่ม keyboard support
                card.addEventListener('keydown', function (e) {
                    if (e.key === 'Enter' || e.key === ' ') {
                        e.preventDefault();
                        this.click();
                    }
                });

                // เพิ่ม tabindex สำหรับ accessibility
                card.setAttribute('tabindex', '0');
                card.setAttribute('role', 'button');

                const titleElement = card.querySelector('.category-title');
                if (titleElement) {
                    card.setAttribute('aria-label', 'เลือกหมวดหมู่: ' + titleElement.textContent);
                }
            });

            // Custom validation เมื่อ submit form
            const form = document.getElementById('complainForm');
            if (form) {
                form.addEventListener('submit', function (e) {
                    if (!hiddenInput.value) {
                        e.preventDefault();

                        // แสดง error state
                        cardGrid.classList.add('error');
                        if (feedbackElement) {
                            feedbackElement.textContent = 'กรุณาเลือกหมวดหมู่เรื่องร้องเรียน';
                            feedbackElement.style.display = 'block';
                        }

                        // scroll ไปที่ category section
                        cardGrid.scrollIntoView({
                            behavior: 'smooth',
                            block: 'center'
                        });

                        return false;
                    }
                });
            }

            console.log('✅ Category card system initialized');

        } catch (error) {
            console.error('❌ Setup category card selection error:', error);
        }
    }

    function getComplainCategoryId() {
        // ลอง hidden input ก่อน (Category Cards)
        const hiddenInput = document.querySelector('input[name="complain_category_id"]');
        if (hiddenInput && hiddenInput.value) {
            return hiddenInput.value;
        }

        // fallback: select element (ระบบเก่า - ถ้ายังมีอยู่)
        const selectElement = document.querySelector('select[name="complain_category_id"]');
        if (selectElement && selectElement.value) {
            return selectElement.value;
        }

        return '';
    }

    // *** เพิ่มฟังก์ชันจัดการ field ตามสถานะ login พร้อมที่อยู่ ***
    function updateFormFieldsBasedOnLoginStatus() {
        try {
            //console.log('🔧 Updating form fields based on login status');
            //console.log('👤 isUserLoggedIn:', isUserLoggedIn);
            //console.log('📋 userInfo:', userInfo);
            // console.log('🏠 userAddress:', userAddress);

            // หาคอนเทนเนอร์และฟิลด์ที่ต้องจัดการ
            const nameField = document.querySelector('input[name="complain_by"]');
            const phoneField = document.querySelector('input[name="complain_phone"]');
            const emailField = document.querySelector('input[name="complain_email"]');
            const addressField = document.querySelector('input[name="complain_address"]');
            const additionalAddressField = document.querySelector('#additional_address_field');

            const nameContainer = nameField?.closest('.form-group');
            const phoneContainer = phoneField?.closest('.col-md-6');
            const emailContainer = emailField?.closest('.form-group');
            const addressContainer = document.querySelector('#zipcode_field')?.closest('.form-group');

            if (isUserLoggedIn && userInfo) {
                //  console.log('✅ User is logged in - preparing fields for logged-in user');

                // *** สำคัญ: ใส่ข้อมูลจาก user account และลบ required attribute ***
                if (nameField) {
                    // ใส่ชื่อจาก user account
                    let userName = '';
                    if (userInfo.user_info) {
                        userName = userInfo.user_info.name ||
                            (userInfo.user_info.fname + ' ' + userInfo.user_info.lname) ||
                            'ผู้ใช้ที่ล็อกอิน';
                    } else {
                        userName = userInfo.name ||
                            (userInfo.fname + ' ' + userInfo.lname) ||
                            'ผู้ใช้ที่ล็อกอิน';
                    }

                    nameField.value = userName;
                    nameField.removeAttribute('required'); // *** ลบ required attribute ***
                    nameField.style.backgroundColor = '#f8f9fa';
                    nameField.readOnly = true;
                    console.log('✅ Set name field:', userName);
                }

                if (phoneField) {
                    // ใส่เบอร์โทรจาก user account
                    let userPhone = '';
                    if (userAddress && userAddress.phone) {
                        userPhone = userAddress.phone;
                    } else if (userInfo.user_info && userInfo.user_info.phone) {
                        userPhone = userInfo.user_info.phone;
                    } else if (userInfo.phone) {
                        userPhone = userInfo.phone;
                    } else {
                        userPhone = '0000000000'; // fallback
                    }

                    phoneField.value = userPhone;
                    phoneField.removeAttribute('required'); // *** ลบ required attribute ***
                    phoneField.style.backgroundColor = '#f8f9fa';
                    phoneField.readOnly = true;
                    //  console.log('✅ Set phone field:', userPhone);
                }

                if (emailField) {
                    // ใส่อีเมลจาก user account
                    let userEmail = '';
                    if (userInfo.user_info) {
                        userEmail = userInfo.user_info.email || userInfo.user_info.username || 'ไม่ระบุ';
                    } else {
                        userEmail = userInfo.email || userInfo.username || 'ไม่ระบุ';
                    }

                    emailField.value = userEmail;
                    emailField.removeAttribute('required'); // ลบ required ถ้ามี
                    emailField.style.backgroundColor = '#f8f9fa';
                    emailField.readOnly = true;
                    // console.log('✅ Set email field:', userEmail);
                }

                if (addressField) {
                    // ใส่ที่อยู่จาก user account
                    let userAddressText = '';
                    if (userAddress && userAddress.full_address) {
                        userAddressText = userAddress.full_address;
                    } else if (userAddress && userAddress.additional_address) {
                        userAddressText = userAddress.additional_address;
                    } else {
                        userAddressText = 'ที่อยู่จากบัญชีผู้ใช้';
                    }

                    addressField.value = userAddressText;
                    addressField.removeAttribute('required'); // *** ลบ required attribute ***
                    addressField.style.backgroundColor = '#f8f9fa';
                    addressField.readOnly = true;
                    // console.log('✅ Set address field:', userAddressText);
                }

                if (additionalAddressField) {
                    // ใส่ที่อยู่เพิ่มเติมจาก user account
                    let additionalAddress = '';
                    if (userAddress && userAddress.additional_address) {
                        additionalAddress = userAddress.additional_address;
                    } else {
                        additionalAddress = 'ข้อมูลจากบัญชี';
                    }

                    additionalAddressField.value = additionalAddress;
                    additionalAddressField.removeAttribute('required'); // ลบ required ถ้ามี
                    additionalAddressField.style.backgroundColor = '#f8f9fa';
                    additionalAddressField.readOnly = true;
                    // console.log('✅ Set additional address field:', additionalAddress);
                }

                // ซ่อนคอนเทนเนอร์ (แต่ฟิลด์ยังมีค่า)
                if (nameContainer) nameContainer.style.display = 'none';
                if (phoneContainer) phoneContainer.style.display = 'none';
                if (emailContainer) emailContainer.style.display = 'none';
                if (addressContainer) addressContainer.style.display = 'none';

                // แสดงข้อความแจ้งให้ทราบว่าใช้ข้อมูลจากบัญชี
                showLoggedInUserInfo();

            } else {
                // console.log('👤 User is not logged in - showing all fields');

                // *** คืนค่า required attribute และแสดงฟิลด์ ***
                if (nameField) {
                    nameField.value = '';
                    nameField.setAttribute('required', 'required'); // *** คืน required attribute ***
                    nameField.style.backgroundColor = '';
                    nameField.readOnly = false;
                }

                if (phoneField) {
                    phoneField.value = '';
                    phoneField.setAttribute('required', 'required'); // *** คืน required attribute ***
                    phoneField.style.backgroundColor = '';
                    phoneField.readOnly = false;
                }

                if (emailField) {
                    emailField.value = '';
                    emailField.removeAttribute('required'); // email ไม่บังคับ
                    emailField.style.backgroundColor = '';
                    emailField.readOnly = false;
                }

                if (addressField) {
                    addressField.value = '';
                    addressField.setAttribute('required', 'required'); // *** คืน required attribute ***
                    addressField.style.backgroundColor = '';
                    addressField.readOnly = false;
                }

                if (additionalAddressField) {
                    additionalAddressField.value = '';
                    additionalAddressField.setAttribute('required', 'required'); // คืน required ถ้าเป็นฟิลด์บังคับ
                    additionalAddressField.style.backgroundColor = '';
                    additionalAddressField.readOnly = false;
                }

                // แสดงฟิลด์ข้อมูลส่วนตัว
                if (nameContainer) nameContainer.style.display = 'block';
                if (phoneContainer) phoneContainer.style.display = 'block';
                if (emailContainer) emailContainer.style.display = 'block';
                if (addressContainer) addressContainer.style.display = 'block';

                // ลบข้อความแจ้งเตือน (ถ้ามี)
                const userInfoDisplay = document.getElementById('logged-in-user-info');
                if (userInfoDisplay) {
                    userInfoDisplay.remove();
                }
            }

        } catch (error) {
            console.error('❌ Form update error:', error);
        }
    }

    function showLoggedInUserInfo() {
        try {
            // ลบข้อความเดิม (ถ้ามี)
            const existingInfo = document.getElementById('logged-in-user-info');
            if (existingInfo) {
                existingInfo.remove();
            }

            // สร้างข้อมูล user
            let userName = '';
            let userEmail = '';
            let userPhone = '';
            let userAddressText = '';

            if (userInfo.user_info) {
                userName = userInfo.user_info.name || '';
                userEmail = userInfo.user_info.email || userInfo.user_info.username || '';
                userPhone = userInfo.user_info.phone || '';
            } else {
                userName = userInfo.name || '';
                userEmail = userInfo.email || userInfo.username || '';
                userPhone = userInfo.phone || '';
            }

            // จัดการข้อมูลที่อยู่
            if (userAddress && userAddress.parsed) {
                userAddressText = userAddress.parsed.full_address || '';
            } else if (userAddress && userAddress.full_address) {
                userAddressText = userAddress.full_address;
            }

            // สร้าง HTML แสดงข้อมูล user แบบละเอียด
            const userInfoHTML = `
            <div id="logged-in-user-info" class="alert alert-success" style="
                border-radius: 15px; 
                background: linear-gradient(135deg, #d4edda 0%, #c3e6cb 100%); 
                border: 1px solid rgba(40, 167, 69, 0.3);
                margin-bottom: 2rem;
                box-shadow: 0 4px 15px rgba(40, 167, 69, 0.2);
                backdrop-filter: blur(10px);
            ">
                <div class="row align-items-center">
                    <div class="col-auto">
                        <div style="
                            width: 60px; 
                            height: 60px; 
                            background: linear-gradient(135deg, rgba(40, 167, 69, 0.15) 0%, rgba(25, 135, 84, 0.15) 100%); 
                            border-radius: 50%; 
                            display: flex; 
                            align-items: center; 
                            justify-content: center;
                            box-shadow: 0 4px 15px rgba(40, 167, 69, 0.3);
                        ">
                            <i class="fas fa-user-check" style="font-size: 1.8rem; color: #198754;"></i>
                        </div>
                    </div>
                    <div class="col">
                        <h5 class="mb-2" style="color: #155724; font-weight: 600;">
                            <i class="fas fa-check-circle me-2"></i>ใช้ข้อมูลจากบัญชีของคุณ
                        </h5>
                        <div class="row">
                            <div class="col-md-6">
                                ${userName ? `<div class="mb-1" style="color: #155724; font-size: 0.9rem;"><i class="fas fa-user me-2"></i><strong>ชื่อ:</strong> ${userName}</div>` : ''}
                                ${userEmail ? `<div class="mb-1" style="color: #155724; font-size: 0.9rem;"><i class="fas fa-envelope me-2"></i><strong>อีเมล:</strong> ${userEmail}</div>` : ''}
                            </div>
                            <div class="col-md-6">
                                ${userPhone ? `<div class="mb-1" style="color: #155724; font-size: 0.9rem;"><i class="fas fa-phone me-2"></i><strong>เบอร์โทร:</strong> ${userPhone}</div>` : ''}
                                ${userAddressText ? `<div class="mb-1" style="color: #155724; font-size: 0.9rem;"><i class="fas fa-map-marker-alt me-2"></i><strong>ที่อยู่:</strong> ${userAddressText}</div>` : '<div class="mb-1" style="color: #155724; font-size: 0.9rem;"><i class="fas fa-map-marker-alt me-2"></i><strong>ที่อยู่:</strong> ใช้ข้อมูลจากบัญชี</div>'}
                            </div>
                        </div>
                        <div class="mt-2">
                            <small style="color: #155724; display: flex; align-items: center;">
                                <i class="fas fa-info-circle me-1"></i> 
                                ระบบจะใช้ข้อมูลจากบัญชีของคุณโดยอัตโนมัติ หากต้องการแจ้งแบบไม่ระบุตัวตน กรุณาเลือกช่อง "นโยบายการคุ้มครองข้อมูลผู้แจ้งเบาะแส โดยไม่ระบุตัวตน" ข้างล่าง
                            </small>
                        </div>
                    </div>
                </div>
            </div>
        `;

            // แทรกข้อความหลังหัวข้อฟอร์ม
            const formContainer = document.getElementById('complain_form');
            const firstFormGroup = formContainer.querySelector('.form-group');
            if (firstFormGroup) {
                firstFormGroup.insertAdjacentHTML('beforebegin', userInfoHTML);
            }

            // console.log('✅ User info display created with detailed information');

        } catch (error) {
            console.error('❌ Error showing user info:', error);
        }
    }

    // *** เพิ่มใหม่: ฟังก์ชันอัพเดทฟิลด์ที่อยู่จากข้อมูล user ***
    function updateAddressFieldsFromUserData(addressData) {
        try {
            // console.log('🏠 Updating address fields from user data:', addressData);

            if (!addressData || !addressData.parsed) {
                console.log('⚠️ No parsed address data available');
                return;
            }

            const parsed = addressData.parsed;

            // อัพเดท zipcode
            if (parsed.zipcode && zipcodeField) {
                zipcodeField.value = parsed.zipcode;
                console.log('📮 Set zipcode:', parsed.zipcode);

                // เรียก API เพื่อค้นหาข้อมูลจาก zipcode
                setTimeout(() => {
                    searchByZipcode(parsed.zipcode);
                }, 500);
            }

            // อัพเดทที่อยู่เพิ่มเติม
            if (parsed.additional_address && additionalAddressField) {
                additionalAddressField.value = parsed.additional_address;
                console.log('🏡 Set additional address:', parsed.additional_address);
            }

            // ล็อคฟิลด์ที่อยู่เพื่อแสดงว่าใช้ข้อมูลจากบัญชี
            [zipcodeField, additionalAddressField].forEach(field => {
                if (field && field.value) {
                    field.style.backgroundColor = '#f8f9fa';
                    field.style.cursor = 'not-allowed';
                    field.readOnly = true;
                }
            });

            // เพิ่มข้อความแจ้งเตือน
            if (zipcodeField && zipcodeField.value) {
                addLoginInfoMessage(zipcodeField, 'ใช้ข้อมูลจากบัญชีของคุณ');
            }
            if (additionalAddressField && additionalAddressField.value) {
                addLoginInfoMessage(additionalAddressField, 'ใช้ข้อมูลจากบัญชีของคุณ');
            }

        } catch (error) {
            console.error('❌ Error updating address fields:', error);
        }
    }

    // ฟังก์ชันเพิ่มข้อความแจ้งเตือน
    function addLoginInfoMessage(field, message) {
        if (!field) return;

        // ลบข้อความเดิม
        const existingInfo = field.parentNode.querySelector('.login-info');
        if (existingInfo) {
            existingInfo.remove();
        }

        // เพิ่มข้อความใหม่
        const infoDiv = document.createElement('div');
        infoDiv.className = 'login-info';
        infoDiv.style.cssText = 'font-size: 0.8rem; color: #28a745; margin-top: 0.25rem; font-style: italic;';
        infoDiv.innerHTML = `<i class="fas fa-check-circle me-1"></i>${message}`;
        field.parentNode.appendChild(infoDiv);
    }

    // ==============================================
    // *** ฟังก์ชัน Address System (เหมือนหน้าสมัครสมาชิก) ***
    // ==============================================

    // ฟังก์ชันเริ่มต้น Address System
    function initializeAddressSystem() {
        try {
            // เริ่มต้น elements
            zipcodeField = document.getElementById('zipcode_field');
            provinceField = document.getElementById('province_field');
            amphoeField = document.getElementById('amphoe_field');
            districtField = document.getElementById('district_field');
            fullAddressField = document.getElementById('full_address_field');
            additionalAddressField = document.getElementById('additional_address_field');

            console.log('🔧 Address elements initialized:', {
                zipcode: !!zipcodeField,
                province: !!provinceField,
                amphoe: !!amphoeField,
                district: !!districtField,
                fullAddress: !!fullAddressField,
                additionalAddress: !!additionalAddressField
            });

            if (!zipcodeField) {
                console.error('❌ Address elements not found');
                return;
            }

            // โหลดรายการจังหวัดทั้งหมด
            loadAllProvinces();

            // จำกัดให้กรอกเฉพาะตัวเลขในช่องรหัสไปรษณีย์
            zipcodeField.addEventListener('keypress', function (e) {
                if (!/[0-9]/.test(e.key) && e.key !== 'Backspace' && e.key !== 'Delete' && e.key !== 'Tab') {
                    e.preventDefault();
                }
            });

            // เมื่อกรอกรหัสไปรษณีย์
            zipcodeField.addEventListener('input', function () {
                const zipcode = this.value.trim();
                // console.log('📮 Zipcode input changed:', zipcode);

                if (zipcode.length === 0) {
                    resetToProvinceSelection();
                } else if (zipcode.length === 5 && /^\d{5}$/.test(zipcode)) {
                    searchByZipcode(zipcode);
                } else {
                    clearDependentAddressFields();
                }
            });

            // เมื่อเลือกจังหวัด
            provinceField.addEventListener('change', function () {
                const selectedProvinceCode = this.value;
                // console.log('🗾 Province changed to:', selectedProvinceCode);

                clearDependentFields('province');

                if (selectedProvinceCode) {
                    loadAmphoesByProvince(selectedProvinceCode);
                }

                updateFullAddress();
            });

            // เมื่อเลือกอำเภอ
            amphoeField.addEventListener('change', function () {
                const selectedAmphoeCode = this.value;
                //  console.log('🏙️ Amphoe changed to:', selectedAmphoeCode);

                if (selectedAmphoeCode) {
                    const currentZipcode = zipcodeField.value.trim();

                    if (currentZipcode.length === 5) {
                        filterDistrictsByAmphoe(selectedAmphoeCode);
                    } else {
                        loadDistrictsByAmphoe(selectedAmphoeCode);
                    }
                } else {
                    districtField.innerHTML = '<option value="">เลือกตำบล</option>';
                    districtField.disabled = true;
                }

                updateFullAddress();
            });

            // เมื่อเลือกตำบล
            districtField.addEventListener('change', function () {
                const selectedDistrictCode = this.value;
                //  console.log('🏘️ District changed to:', selectedDistrictCode);

                if (selectedDistrictCode) {
                    // หา zipcode จากข้อมูลที่มีแล้ว
                    const selectedDistrict = currentAddressData.find(item =>
                        (item.district_code || item.code) === selectedDistrictCode
                    );

                    if (selectedDistrict && selectedDistrict.zipcode) {
                        zipcodeField.value = selectedDistrict.zipcode;
                    } else {
                        loadZipcodeByDistrict(selectedDistrictCode);
                    }
                }

                updateFullAddress();
            });

            // Event Listener สำหรับช่องที่อยู่เพิ่มเติม
            if (additionalAddressField) {
                additionalAddressField.addEventListener('input', function () {
                    clearTimeout(this.updateTimeout);
                    this.updateTimeout = setTimeout(() => {
                        updateFullAddress();
                    }, 300);
                });
            }

            //  console.log('✅ Address system initialized successfully');

        } catch (error) {
            console.error('❌ Error initializing address system:', error);
        }
    }

    // ฟังก์ชันดึงรายการจังหวัดทั้งหมด
    async function loadAllProvinces() {
        console.log('Loading all provinces...');

        const provinces = [
            { code: '10', name: 'กรุงเทพมหานคร' },
            { code: '11', name: 'สมุทรปราการ' },
            { code: '12', name: 'นนทบุรี' },
            { code: '13', name: 'ปทุมธานี' },
            { code: '14', name: 'พระนครศรีอยุธยา' },
            { code: '15', name: 'อ่างทอง' },
            { code: '16', name: 'ลพบุรี' },
            { code: '17', name: 'สิงห์บุรี' },
            { code: '18', name: 'ชัยนาท' },
            { code: '19', name: 'สระบุรี' },
            { code: '20', name: 'ชลบุรี' },
            { code: '21', name: 'ระยอง' },
            { code: '22', name: 'จันทบุรี' },
            { code: '23', name: 'ตราด' },
            { code: '24', name: 'ฉะเชิงเทรา' },
            { code: '25', name: 'ปราจีนบุรี' },
            { code: '26', name: 'นครนายก' },
            { code: '27', name: 'สระแก้ว' },
            { code: '30', name: 'นครราชสีมา' },
            { code: '31', name: 'บุรีรัมย์' },
            { code: '32', name: 'สุรินทร์' },
            { code: '33', name: 'ศีสะเกษ' },
            { code: '34', name: 'อุบลราชธานี' },
            { code: '35', name: 'ยโสธร' },
            { code: '36', name: 'ชัยภูมิ' },
            { code: '37', name: 'อำนาจเจริญ' },
            { code: '38', name: 'บึงกาฬ' },
            { code: '39', name: 'หนองบัวลำภู' },
            { code: '40', name: 'ขอนแก่น' },
            { code: '41', name: 'อุดรธานี' },
            { code: '42', name: 'เลย' },
            { code: '43', name: 'หนองคาย' },
            { code: '44', name: 'มหาสารคาม' },
            { code: '45', name: 'ร้อยเอ็ด' },
            { code: '46', name: 'กาฬสินธุ์' },
            { code: '47', name: 'สกลนคร' },
            { code: '48', name: 'นครพนม' },
            { code: '49', name: 'มุกดาหาร' },
            { code: '50', name: 'เชียงใหม่' },
            { code: '51', name: 'ลำพูน' },
            { code: '52', name: 'ลำปาง' },
            { code: '53', name: 'อุตรดิตถ์' },
            { code: '54', name: 'แพร่' },
            { code: '55', name: 'น่าน' },
            { code: '56', name: 'พะเยา' },
            { code: '57', name: 'เชียงราย' },
            { code: '58', name: 'แม่ฮ่องสอน' },
            { code: '60', name: 'นครสวรรค์' },
            { code: '61', name: 'อุทัยธานี' },
            { code: '62', name: 'กำแพงเพชร' },
            { code: '63', name: 'ตาก' },
            { code: '64', name: 'สุโขทัย' },
            { code: '65', name: 'พิษณุโลก' },
            { code: '66', name: 'พิจิตร' },
            { code: '67', name: 'เพชรบูรณ์' },
            { code: '70', name: 'ราชบุรี' },
            { code: '71', name: 'กาญจนบุรี' },
            { code: '72', name: 'สุพรรณบุรี' },
            { code: '73', name: 'นครปฐม' },
            { code: '74', name: 'สมุทรสาคร' },
            { code: '75', name: 'สมุทรสงคราม' },
            { code: '76', name: 'เพชรบุรี' },
            { code: '77', name: 'ประจวบคีรีขันธ์' },
            { code: '80', name: 'นครศรีธรรมราช' },
            { code: '81', name: 'กระบี่' },
            { code: '82', name: 'พังงา' },
            { code: '83', name: 'ภูเก็ต' },
            { code: '84', name: 'สุราษฎร์ธานี' },
            { code: '85', name: 'ระนอง' },
            { code: '86', name: 'ชุมพร' },
            { code: '90', name: 'สงขลา' },
            { code: '91', name: 'สตูล' },
            { code: '92', name: 'ตรัง' },
            { code: '93', name: 'พัทลุง' },
            { code: '94', name: 'ปัตตานี' },
            { code: '95', name: 'ยะลา' },
            { code: '96', name: 'นราธิวาส' }
        ];

        populateProvinceDropdown(provinces);
    }

    // ฟังก์ชันค้นหาข้อมูลจากรหัสไปรษณีย์
    async function searchByZipcode(zipcode) {
        //console.log('🔍 Searching by zipcode:', zipcode);
        showAddressLoading(true);

        try {
            const response = await fetch(`${API_BASE_URL}/address/${zipcode}`);
            const data = await response.json();

            console.log('📦 API Response for zipcode:', data);

            if (data.status === 'success' && data.data.length > 0) {
                const dataWithZipcode = data.data.map(item => ({
                    ...item,
                    zipcode: zipcode,
                    searched_zipcode: zipcode
                }));

                // console.log('✅ Enhanced data with zipcode:', dataWithZipcode);

                currentAddressData = dataWithZipcode;
                populateFieldsFromZipcode(dataWithZipcode);
                updateFullAddress();
            } else {
                showAddressError('ไม่พบข้อมูลสำหรับรหัสไปรษณีย์นี้');
                resetToProvinceSelection();
            }
        } catch (error) {
            console.error('❌ Address API Error:', error);
            showAddressError('เกิดข้อผิดพลาดในการค้นหาข้อมูล');
            resetToProvinceSelection();
        } finally {
            showAddressLoading(false);
        }
    }

    // ฟังก์ชันดึงอำเภอตามจังหวัด
    async function loadAmphoesByProvince(provinceCode) {
        console.log('Loading amphoes for province:', provinceCode);
        showAddressLoading(true, 'province');

        try {
            const response = await fetch(`${API_BASE_URL}/amphoes/${provinceCode}`);
            const data = await response.json();

            if (data.status === 'success' && data.data && data.data.length > 0) {
                const processedAmphoes = data.data.map(item => ({
                    code: item.amphoe_code || item.code || item.id,
                    name: item.amphoe_name || item.name || item.name_th || 'ไม่ระบุชื่อ',
                    name_en: item.amphoe_name_en || item.name_en || ''
                }));

                populateAmphoeDropdown(processedAmphoes);
                amphoeField.disabled = false;
            } else {
                amphoeField.innerHTML = '<option value="">ไม่พบข้อมูลอำเภอ</option>';
                amphoeField.disabled = true;
            }
        } catch (error) {
            console.error('Amphoe API Error:', error);
            amphoeField.innerHTML = '<option value="">ไม่สามารถโหลดข้อมูลอำเภอได้</option>';
            amphoeField.disabled = true;
        } finally {
            showAddressLoading(false);
        }
    }

    // ฟังก์ชันดึงตำบลตามอำเภอ
    async function loadDistrictsByAmphoe(amphoeCode) {
        // console.log('Loading districts for amphoe:', amphoeCode);
        showAddressLoading(true, 'amphoe');

        try {
            const response = await fetch(`${API_BASE_URL}/districts/${amphoeCode}`);
            const data = await response.json();

            if (data.status === 'success' && data.data && data.data.length > 0) {
                const processedDistricts = data.data.map(item => ({
                    code: item.district_code || item.code || item.id,
                    name: item.district_name || item.name || item.name_th || 'ไม่ระบุชื่อ',
                    name_en: item.district_name_en || item.name_en || '',
                    amphoe_code: item.amphoe_code || amphoeCode
                }));

                populateDistrictDropdown(processedDistricts);
                districtField.disabled = false;
            } else {
                districtField.innerHTML = '<option value="">ไม่พบข้อมูลตำบล</option>';
                districtField.disabled = true;
            }
        } catch (error) {
            console.error('District API Error:', error);
            districtField.innerHTML = '<option value="">ไม่สามารถโหลดข้อมูลตำบลได้</option>';
            districtField.disabled = true;
        } finally {
            showAddressLoading(false);
        }
    }

    // ฟังก์ชันดึงรหัสไปรษณีย์จากตำบล
    async function loadZipcodeByDistrict(districtCode) {
        // console.log('📡 Loading zipcode for district:', districtCode);

        try {
            const response = await fetch(`${API_BASE_URL}/district/${districtCode}`);

            if (!response.ok) {
                throw new Error(`HTTP error! status: ${response.status}`);
            }

            const data = await response.json();
            console.log('📦 District API Response:', data);

            if (data.status === 'success' && data.data && data.data.length > 0) {
                const districtData = data.data[0];
                const zipcode = districtData.zipcode;

                if (zipcode) {
                    zipcodeField.value = zipcode;
                    //  console.log('✅ Zipcode field updated:', zipcode);
                }

                updateFullAddress();
            }
        } catch (error) {
            console.error('❌ Zipcode API Error:', error);
        }
    }





    // แสดงข้อมูลจากการค้นหา zipcode
    function populateFieldsFromZipcode(data) {
        if (data.length === 0) return;

        //  console.log('📝 Populating fields from zipcode data:', data);

        const searchedZipcode = zipcodeField.value.trim();
        const relevantData = data.filter(item =>
            (item.zipcode || item.searched_zipcode) === searchedZipcode
        );

        if (relevantData.length === 0) {
            console.warn('⚠️ No data matches the searched zipcode');
            return;
        }

        const firstItem = relevantData[0];
        convertToProvinceInput(firstItem.province_name);

        const amphoes = getUniqueAmphoes(relevantData);
        populateAmphoeDropdown(amphoes);

        const districts = relevantData.map(item => ({
            code: item.district_code,
            name: item.district_name,
            name_en: item.district_name_en,
            amphoe_code: item.amphoe_code,
            zipcode: item.zipcode || item.searched_zipcode || searchedZipcode
        }));
        populateDistrictDropdown(districts);

        amphoeField.disabled = false;
        districtField.disabled = false;

        // Auto-select อำเภอถ้ามีเพียงอำเภอเดียว
        if (amphoes.length === 1) {
            amphoeField.value = amphoes[0].code;
            setTimeout(() => {
                filterDistrictsByAmphoe(amphoes[0].code);
            }, 100);
        }

        currentAddressData = relevantData;
    }

    // สร้าง dropdown จังหวัด
    function populateProvinceDropdown(provinces) {
        if (!provinceField.tagName || provinceField.tagName.toLowerCase() !== 'select') {
            convertToProvinceSelect();
        }

        provinceField.innerHTML = '<option value="">เลือกจังหวัด</option>';

        provinces.forEach(province => {
            if (province.code && province.name) {
                provinceField.innerHTML += `<option value="${province.code}">${province.name}</option>`;
            }
        });
    }

    // สร้าง dropdown อำเภอ
    function populateAmphoeDropdown(amphoes) {
        amphoeField.innerHTML = '<option value="">เลือกอำเภอ</option>';

        amphoes.forEach(amphoe => {
            if (amphoe && amphoe.code && amphoe.name) {
                amphoeField.innerHTML += `<option value="${amphoe.code}">${amphoe.name}</option>`;
            }
        });
    }

    // สร้าง dropdown ตำบล
    function populateDistrictDropdown(districts) {
        districtField.innerHTML = '<option value="">เลือกตำบล</option>';

        districts.forEach(district => {
            if (district && district.code && district.name) {
                districtField.innerHTML += `
                <option value="${district.code}" 
                        data-amphoe-code="${district.amphoe_code}"
                        data-zipcode="${district.zipcode || ''}">
                    ${district.name}
                </option>
            `;
            }
        });
    }

    // สร้างรายการอำเภอที่ไม่ซ้ำกัน
    function getUniqueAmphoes(data) {
        const uniqueAmphoes = [];
        const seenCodes = new Set();

        data.forEach(item => {
            if (!seenCodes.has(item.amphoe_code)) {
                seenCodes.add(item.amphoe_code);
                uniqueAmphoes.push({
                    code: item.amphoe_code,
                    name: item.amphoe_name,
                    name_en: item.amphoe_name_en
                });
            }
        });

        return uniqueAmphoes;
    }

    // กรองตำบลตามอำเภอที่เลือก
    function filterDistrictsByAmphoe(amphoeCode) {
        // console.log('🔍 Filtering districts for amphoe:', amphoeCode);

        const options = districtField.querySelectorAll('option');
        let visibleCount = 0;

        options.forEach(option => {
            if (option.value === '') {
                option.style.display = 'block';
                return;
            }

            const optionAmphoeCode = option.getAttribute('data-amphoe-code');

            if (String(optionAmphoeCode) === String(amphoeCode)) {
                option.style.display = 'block';
                visibleCount++;
            } else {
                option.style.display = 'none';
            }
        });

        // console.log(`📊 Filtering result: ${visibleCount} districts visible`);
        updateFullAddress();
    }

    // แปลงจังหวัดเป็น select
    function convertToProvinceSelect() {
        if (provinceField && provinceField.tagName.toLowerCase() === 'select') return;

        const provinceWrapper = provinceField.parentNode;
        provinceField.remove();

        const selectHtml = `
        <select id="province_field" class="form-control" style="border: none; border-radius: 15px; padding: 1rem; font-size: 1.1rem; transition: all 0.3s ease; box-shadow: 0 6px 20px rgba(220, 38, 38, 0.15); background: linear-gradient(135deg, #ffffff 0%, #f8f9fa 100%); backdrop-filter: blur(10px);">
            <option value="">เลือกจังหวัด</option>
        </select>
    `;

        provinceWrapper.insertAdjacentHTML('beforeend', selectHtml);
        provinceField = document.getElementById('province_field');
    }

    // แปลงจังหวัดเป็น input (สำหรับโหมด zipcode)
    function convertToProvinceInput(value = '') {
        if (provinceField && provinceField.tagName.toLowerCase() === 'input') {
            provinceField.value = value;
            return;
        }

        const provinceWrapper = provinceField.parentNode;
        provinceField.remove();

        const inputHtml = `
        <input type="text" id="province_field" class="form-control" 
               placeholder="จังหวัด" readonly value="${value}"
               style="border: none; border-radius: 15px; padding: 1rem; font-size: 1.1rem; transition: all 0.3s ease; box-shadow: 0 6px 20px rgba(220, 38, 38, 0.15); background: linear-gradient(135deg, #ffffff 0%, #f8f9fa 100%); backdrop-filter: blur(10px);">
    `;

        provinceWrapper.insertAdjacentHTML('beforeend', inputHtml);
        provinceField = document.getElementById('province_field');
    }

    // รีเซ็ตกลับไปสู่การเลือกจังหวัด
    function resetToProvinceSelection() {
        convertToProvinceSelect();
        loadAllProvinces();

        amphoeField.innerHTML = '<option value="">เลือกอำเภอ</option>';
        amphoeField.disabled = true;
        districtField.innerHTML = '<option value="">เลือกตำบล</option>';
        districtField.disabled = true;

        document.querySelectorAll('.address-error').forEach(el => el.remove());
        updateFullAddress();
    }

    // ล้างข้อมูลที่ขึ้นต่อกันเมื่อ zipcode เปลี่ยน
    function clearDependentAddressFields() {
        if (provinceField.tagName.toLowerCase() === 'input') {
            provinceField.value = '';
        }

        amphoeField.innerHTML = '<option value="">เลือกอำเภอ</option>';
        amphoeField.disabled = true;
        districtField.innerHTML = '<option value="">เลือกตำบล</option>';
        districtField.disabled = true;

        document.querySelectorAll('.address-error').forEach(el => el.remove());
        updateFullAddress();
    }

    // ล้างข้อมูลตามระดับ
    function clearDependentFields(fromLevel) {
        switch (fromLevel) {
            case 'zipcode':
                if (provinceField.tagName.toLowerCase() === 'select') {
                    provinceField.value = '';
                } else {
                    provinceField.value = '';
                }
                amphoeField.innerHTML = '<option value="">เลือกอำเภอ</option>';
                amphoeField.disabled = true;
                districtField.innerHTML = '<option value="">เลือกตำบล</option>';
                districtField.disabled = true;
                break;
            case 'province':
                amphoeField.innerHTML = '<option value="">เลือกอำเภอ</option>';
                amphoeField.disabled = true;
                districtField.innerHTML = '<option value="">เลือกตำบล</option>';
                districtField.disabled = true;
                zipcodeField.value = '';
                break;
            case 'amphoe':
                districtField.innerHTML = '<option value="">เลือกตำบล</option>';
                districtField.disabled = true;
                zipcodeField.value = '';
                break;
        }

        document.querySelectorAll('.address-error').forEach(el => el.remove());
        updateFullAddress();
    }


    function updateFullAddress() {
        if (!fullAddressField) return;

        const additionalAddress = additionalAddressField ? additionalAddressField.value.trim() : '';

        // *** เปลี่ยนใหม่: ใส่เฉพาะที่อยู่เพิ่มเติมใน complain_address ***
        fullAddressField.value = additionalAddress; // เฉพาะ mp_address

        // *** เพิ่ม: จัดเก็บข้อมูลแยกสำหรับ guest ***
        const currentZipcode = zipcodeField ? zipcodeField.value : '';
        let currentProvince = '';
        let currentAmphoe = '';
        let currentDistrict = '';

        if (provinceField) {
            if (provinceField.tagName.toLowerCase() === 'select') {
                const selectedOption = provinceField.options[provinceField.selectedIndex];
                currentProvince = selectedOption && selectedOption.value ? selectedOption.text : '';
            } else {
                currentProvince = provinceField.value;
            }
        }

        if (amphoeField && amphoeField.selectedIndex > 0) {
            currentAmphoe = amphoeField.options[amphoeField.selectedIndex].text;
        }

        if (districtField && districtField.selectedIndex > 0) {
            currentDistrict = districtField.options[districtField.selectedIndex].text;
        }

        // *** เพิ่ม hidden fields สำหรับส่งข้อมูลแยก ***
        updateHiddenAddressFields(currentProvince, currentAmphoe, currentDistrict, currentZipcode);

        // แสดงผลในช่องแสดงที่อยู่รวม (สำหรับให้ผู้ใช้เห็น - แสดง full address)
        const addressPreview = document.getElementById('address_preview');
        const addressPreviewText = document.getElementById('address_preview_text');

        if (addressPreview && addressPreviewText) {
            let displayAddress = '';
            if (additionalAddress) displayAddress = additionalAddress;
            if (currentDistrict) displayAddress += (displayAddress ? ' ' : '') + 'ตำบล' + currentDistrict;
            if (currentAmphoe) displayAddress += (displayAddress ? ' ' : '') + 'อำเภอ' + currentAmphoe;
            if (currentProvince) displayAddress += (displayAddress ? ' ' : '') + 'จังหวัด' + currentProvince;
            if (currentZipcode && currentZipcode.length === 5) displayAddress += (displayAddress ? ' ' : '') + currentZipcode;

            if (displayAddress) {
                addressPreviewText.textContent = displayAddress;
                addressPreview.style.display = 'block';
            } else {
                addressPreview.style.display = 'none';
            }
        }
    }

    // *** เพิ่มฟังก์ชันใหม่ ***
    function updateHiddenAddressFields(province, amphoe, district, zipcode) {
        // สร้าง hidden fields ถ้ายังไม่มี
        const hiddenFields = [
            { name: 'guest_province', value: province },
            { name: 'guest_amphoe', value: amphoe },
            { name: 'guest_district', value: district },
            { name: 'guest_zipcode', value: zipcode }
        ];

        hiddenFields.forEach(field => {
            let hiddenField = document.querySelector(`input[name="${field.name}"]`);
            if (!hiddenField) {
                hiddenField = document.createElement('input');
                hiddenField.type = 'hidden';
                hiddenField.name = field.name;
                document.getElementById('complainForm').appendChild(hiddenField);
            }
            hiddenField.value = field.value || '';
        });
    }





    // แสดง Loading indicator
    function showAddressLoading(show, context = 'zipcode') {
        if (show) {
            document.querySelectorAll('.address-loading-icon').forEach(el => el.remove());

            let targetField;
            switch (context) {
                case 'province':
                    targetField = provinceField;
                    break;
                case 'amphoe':
                    targetField = amphoeField;
                    break;
                case 'district':
                    targetField = districtField;
                    break;
                default:
                    targetField = zipcodeField;
            }

            if (targetField) {
                const icon = document.createElement('i');
                icon.className = 'fas fa-spinner fa-spin address-loading-icon';
                icon.style.cssText = 'position: absolute; right: 45px; top: 50%; transform: translateY(-50%); color: #e55a2b; z-index: 3;';
                targetField.parentNode.appendChild(icon);
            }
        } else {
            document.querySelectorAll('.address-loading-icon').forEach(el => el.remove());
        }
    }

    // แสดงข้อความ Error
    function showAddressError(message) {
        document.querySelectorAll('.address-error').forEach(el => el.remove());

        if (zipcodeField) {
            const errorDiv = document.createElement('small');
            errorDiv.className = 'address-error text-danger form-text';
            errorDiv.textContent = message;
            zipcodeField.parentNode.appendChild(errorDiv);

            setTimeout(() => {
                errorDiv.style.opacity = '0';
                setTimeout(() => errorDiv.remove(), 300);
            }, 5000);
        }
    }

    // ==============================================
    // ฟังก์ชันจัดการไฟล์
    // ==============================================

    // ฟังก์ชันจัดการการเลือกไฟล์
    // แก้ไขฟังก์ชันเดิม - เพิ่ม async และ compression logic
    async function handleFileSelect(input) {
        if (input._processing) return;

        input._processing = true;
        const files = Array.from(input.files);

        if (files.length === 0) {
            input._processing = false;
            return;
        }

        if (selectedFiles.length + files.length > maxFiles) {
            showAlert('warning', 'เกินจำนวนที่กำหนด', `คุณสามารถอัพโหลดได้สูงสุด ${maxFiles} รูปภาพเท่านั้น`);
            input._processing = false;
            input.value = '';
            return;
        }

        let validFiles = [];

        for (let file of files) {
            if (!validateFile(file)) {
                input._processing = false;
                input.value = '';
                return;
            }

            file.id = Date.now() + Math.random();

            // เพิ่มส่วนนี้ใหม่ - การบีบอัด
            if (file.size > 1024 * 1024) {
                try {
                    const compressedFile = await compressImage(file);
                    validFiles.push(compressedFile);

                    if (compressedFile.compressed) {
                        showAlert('success', 'บีบอัดสำเร็จ',
                            `${file.name} ลดขนาดจาก ${formatFileSize(file.size)} เป็น ${formatFileSize(compressedFile.size)}`, 3000);
                    }
                } catch (error) {
                    validFiles.push(file);
                }
            } else {
                validFiles.push(file);
            }
        }

        selectedFiles = [...selectedFiles, ...validFiles];
        updateFileDisplay();

        setTimeout(() => {
            input.value = '';
            input._processing = false;
        }, 100);
    }

    // ฟังก์ชันตรวจสอบไฟล์
    function validateFile(file) {
        if (!allowedTypes.includes(file.type.toLowerCase())) {
            showAlert('error', 'ประเภทไฟล์ไม่ถูกต้อง', 'รองรับเฉพาะไฟล์ JPG, JPEG, PNG, GIF และ WebP เท่านั้น');
            return false;
        }

        if (file.size > maxFileSize) {
            showAlert('error', 'ไฟล์ใหญ่เกินไป', `ขนาดไฟล์ต้องไม่เกิน ${maxFileSize / (1024 * 1024)} MB`);
            return false;
        }

        return true;
    }

    // ฟังก์ชันแสดงไฟล์ที่เลือก
    function updateFileDisplay() {
        const uploadPlaceholder = document.getElementById('upload-placeholder');
        const previewArea = document.getElementById('file-preview-area');
        const previewContainer = document.getElementById('preview-container');
        const fileCount = document.getElementById('file-count');

        if (selectedFiles.length === 0) {
            uploadPlaceholder.style.display = 'block';
            previewArea.style.display = 'none';
            return;
        }

        uploadPlaceholder.style.display = 'none';
        previewArea.style.display = 'block';
        fileCount.textContent = selectedFiles.length;

        previewContainer.innerHTML = '';

        selectedFiles.forEach((file, index) => {
            const previewItem = createPreviewItem(file, index);
            previewContainer.appendChild(previewItem);
        });
    }

    // ฟังก์ชันสร้าง preview item
    function createPreviewItem(file, index) {
        const div = document.createElement('div');
        div.className = 'preview-item';
        div.setAttribute('data-file-id', file.id);

        const img = document.createElement('img');
        img.className = 'preview-image';
        img.alt = file.name;

        const reader = new FileReader();
        reader.onload = function (e) {
            img.src = e.target.result;
        };
        reader.readAsDataURL(file);

        const info = document.createElement('div');
        info.className = 'preview-info';

        const name = document.createElement('p');
        name.className = 'preview-name';
        name.textContent = file.name;
        name.title = file.name;

        const size = document.createElement('p');
        size.className = 'preview-size';

        // แทนที่ส่วนเดิม size.textContent = formatFileSize(file.size);
        if (file.compressed && file.originalSize) {
            const savedPercent = Math.round(((file.originalSize - file.size) / file.originalSize) * 100);
            size.innerHTML = `
        <span style="color: #28a745; font-weight: 600;">
            <i class="fas fa-compress-arrows-alt"></i> ${formatFileSize(file.size)}
        </span>
        <br>
        <small style="color: #6c757d;">บีบอัดจาก ${formatFileSize(file.originalSize)} (-${savedPercent}%)</small>
    `;

            // เพิ่ม badge การบีบอัด
            const badge = document.createElement('div');
            badge.innerHTML = `<i class="fas fa-compress-arrows-alt"></i> ${savedPercent}%`;
            badge.style.cssText = `
        position: absolute;
        top: 4px;
        left: 4px;
        background: #28a745;
        color: white;
        border-radius: 10px;
        padding: 2px 6px;
        font-size: 0.6rem;
        font-weight: 600;
    `;
            div.appendChild(badge);
        } else {
            size.textContent = formatFileSize(file.size);
        }

        const removeBtn = document.createElement('button');
        removeBtn.className = 'remove-file';
        removeBtn.innerHTML = '<i class="fas fa-times"></i>';
        removeBtn.title = 'ลบไฟล์นี้';
        removeBtn.onclick = () => removeFile(file.id);

        info.appendChild(name);
        info.appendChild(size);

        div.appendChild(img);
        div.appendChild(info);
        div.appendChild(removeBtn);

        const progress = document.createElement('div');
        progress.className = 'upload-progress';
        div.appendChild(progress);

        return div;
    }

    // ฟังก์ชันลบไฟล์
    function removeFile(fileId) {
        selectedFiles = selectedFiles.filter(file => file.id !== fileId);
        updateFileDisplay();
    }

    // ฟังก์ชันลบไฟล์ทั้งหมด
    function clearAllFiles() {
        selectedFiles = [];
        updateFileDisplay();
        showAlert('info', 'ลบไฟล์แล้ว', 'ลบไฟล์ทั้งหมดเรียบร้อยแล้ว', 2000);
    }

    // ฟังก์ชันแปลงขนาดไฟล์
    function formatFileSize(bytes) {
        if (bytes === 0) return '0 Bytes';
        const k = 1024;
        const sizes = ['Bytes', 'KB', 'MB', 'GB'];
        const i = Math.floor(Math.log(bytes) / Math.log(k));
        return parseFloat((bytes / Math.pow(k, i)).toFixed(2)) + ' ' + sizes[i];
    }

    // ==============================================
    // ฟังก์ชันจัดการ Drag & Drop
    // ==============================================

    function handleDragOver(e) {
        e.preventDefault();
        e.dataTransfer.dropEffect = 'copy';
    }

    function handleDragEnter(e) {
        e.preventDefault();
        e.currentTarget.classList.add('drag-over');
    }

    function handleDragLeave(e) {
        e.preventDefault();
        if (!e.currentTarget.contains(e.relatedTarget)) {
            e.currentTarget.classList.remove('drag-over');
        }
    }

    // แก้ไขฟังก์ชันเดิม - เพิ่ม async และ compression logic
    async function handleDrop(e) {
        e.preventDefault();
        e.currentTarget.classList.remove('drag-over');

        const files = Array.from(e.dataTransfer.files).filter(file => file.type.startsWith('image/'));

        if (files.length === 0) {
            showAlert('error', 'ไม่พบไฟล์รูปภาพ', 'กรุณาเลือกไฟล์รูปภาพเท่านั้น');
            return;
        }

        if (selectedFiles.length + files.length > maxFiles) {
            showAlert('warning', 'เกินจำนวนที่กำหนด', `คุณสามารถอัพโหลดได้สูงสุด ${maxFiles} รูปภาพเท่านั้น`);
            return;
        }

        let validFiles = [];

        for (let file of files) {
            if (validateFile(file)) {
                file.id = Date.now() + Math.random();

                // เพิ่มส่วนนี้ใหม่ - การบีบอัด
                if (file.size > 500 * 1024) {
                    try {
                        const compressedFile = await compressImage(file);
                        validFiles.push(compressedFile);
                    } catch (error) {
                        validFiles.push(file);
                    }
                } else {
                    validFiles.push(file);
                }
            }
        }

        selectedFiles = [...selectedFiles, ...validFiles];
        updateFileDisplay();

        if (validFiles.length > 0) {
            showAlert('success', 'เพิ่มไฟล์สำเร็จ', `เพิ่มไฟล์ ${validFiles.length} ไฟล์เรียบร้อยแล้ว`, 2000);
        }
    }

    // ==============================================
    // ฟังก์ชันแสดงข้อความแจ้งเตือน
    // ==============================================

    function showAlert(icon, title, text, timer = null) {
        const config = {
            icon: icon,
            title: title,
            text: text,
            confirmButtonColor: '#e55a2b',
            confirmButtonText: 'ตกลง'
        };

        if (timer) {
            config.timer = timer;
            config.showConfirmButton = false;
            config.toast = true;
            config.position = 'top-end';
        }

        Swal.fire(config);
    }

    // ==============================================
    // ฟังก์ชันจัดการการส่งฟอร์ม - แก้ไขให้ปลอดภัย
    // ==============================================

    // *** เพิ่ม mapping ชื่อฟิลด์ภาษาไทย ***
    const fieldNameMapping = {
        'complain_type': 'หมวดหมู่เรื่องร้องเรียน',
        'complain_topic': 'หัวข้อเรื่องร้องเรียน',
        'complain_detail': 'รายละเอียดเรื่องร้องเรียน',
        'complain_by': 'ชื่อ-นามสกุล',
        'complain_phone': 'เบอร์โทรศัพท์',
        'complain_email': 'อีเมล',
        'complain_address': 'ที่อยู่',
        'district_field': 'ตำบล',
        'amphoe_field': 'อำเภอ',
        'province_field': 'จังหวัด',
        'zipcode_field': 'รหัสไปรษณีย์',
        'additional_address_field': 'ที่อยู่เพิ่มเติม',
        'guest_district': 'ตำบล',
        'guest_amphoe': 'อำเภอ',
        'guest_province': 'จังหวัด',
        'guest_zipcode': 'รหัสไปรษณีย์'
    };
    // *** ฟังก์ชันสำหรับสร้าง reCAPTCHA Token ***
    async function generateRecaptchaToken(action = 'complain_submit') {
        if (!window.recaptchaReady || !window.RECAPTCHA_SITE_KEY) {
            console.warn('⚠️ reCAPTCHA not ready or site key missing');
            return null;
        }

        try {
            const token = await grecaptcha.execute(window.RECAPTCHA_SITE_KEY, {
                action: action
            });

            console.log('✅ reCAPTCHA token generated for action:', action);
            return token;
        } catch (error) {
            console.error('❌ Error generating reCAPTCHA token:', error);
            return null;
        }
    }

    // *** ฟังก์ชันตรวจสอบสถานะ reCAPTCHA ***
    function checkRecaptchaStatus() {
        return {
            ready: window.recaptchaReady || false,
            siteKey: window.RECAPTCHA_SITE_KEY || null,
            skipForDev: window.SKIP_RECAPTCHA_FOR_DEV || false
        };
    }

    // *** ฟังก์ชัน submitForm() แบบสมบูรณ์ - รองรับทุกสถานการณ์ + reCAPTCHA ***
    async function submitForm() {
        try {
            const form = document.getElementById('complainForm');

            if (!form) {
                throw new Error('Form not found');
            }

            // *** Frontend Validation ก่อนส่งข้อมูล ***
            const validationErrors = validateFormFields();
            if (validationErrors.length > 0) {
                Swal.fire({
                    icon: 'warning',
                    title: 'กรุณาตรวจสอบข้อมูล',
                    html: `
                    <div style="text-align: left;">
                        <p style="margin-bottom: 1rem; color: #856404;">พบข้อผิดพลาดในการกรอกข้อมูล:</p>
                        <div style="background: #fff3cd; padding: 1rem; border-radius: 8px; border: 1px solid #ffeaa7;">
                            <ul style="color: #e55a2b; margin: 0; padding-left: 1.5rem;">
                                ${validationErrors.map(error => `<li>${error}</li>`).join('')}
                            </ul>
                        </div>
                        <div style="margin-top: 1rem; padding: 0.8rem; background: #e8f4f8; border-radius: 8px; border-left: 4px solid #17a2b8;">
                            <small style="color: #0c5460;">
                                <i class="fas fa-lightbulb"></i> 
                                <strong>คำแนะนำ:</strong> กรุณากรอกข้อมูลให้ครบถ้วนก่อนส่ง
                            </small>
                        </div>
                    </div>
                `,
                    confirmButtonColor: '#e55a2b',
                    confirmButtonText: 'ตกลง'
                });
                return false;
            }

            if (form.checkValidity()) {
                if (formSubmitting) {
                    console.log('Form submission already in progress');
                    return;
                }

                formSubmitting = true;

                const submitBtn = document.getElementById('submitComplainBtn');
                const originalContent = submitBtn.innerHTML;

                submitBtn.disabled = true;
                submitBtn.innerHTML = '<i class="fas fa-spinner fa-spin me-2"></i>กำลังส่ง...';

                // *** เพิ่ม: สร้าง reCAPTCHA Token ***
                const recaptchaStatus = checkRecaptchaStatus();
                let recaptchaToken = null;

                if (recaptchaStatus.ready && recaptchaStatus.siteKey) {
                    try {
                        recaptchaToken = await generateRecaptchaToken('complain_submit');
                        console.log('🔐 Generated reCAPTCHA token for complain submission');
                    } catch (error) {
                        console.error('❌ reCAPTCHA token generation failed:', error);

                        // แสดงข้อผิดพลาด
                        Swal.fire({
                            icon: 'error',
                            title: 'ไม่สามารถยืนยันตัวตนได้',
                            text: 'กรุณาลองใหม่อีกครั้ง หรือติดต่อเจ้าหน้าที่',
                            confirmButtonColor: '#e55a2b'
                        });

                        // รีเซ็ตปุ่ม
                        submitBtn.disabled = false;
                        submitBtn.innerHTML = originalContent;
                        formSubmitting = false;
                        return false;
                    }
                } else if (!recaptchaStatus.skipForDev) {
                    console.warn('⚠️ reCAPTCHA not ready - proceeding without verification');
                }

                const formToken = generateFormToken();
                const tokenField = document.getElementById('formToken');
                if (tokenField) {
                    tokenField.value = formToken;
                }

                const formData = new FormData();

                // *** เพิ่ม reCAPTCHA Token ***
                if (recaptchaToken) {
                    formData.append('g-recaptcha-response', recaptchaToken);
                    formData.append('recaptcha_action', 'complain_submit');
                    formData.append('recaptcha_source', 'complain_form');
                }

                // *** เพิ่มข้อมูลเพิ่มเติมสำหรับ reCAPTCHA ***
                formData.append('client_timestamp', Date.now());
                formData.append('user_agent_info', navigator.userAgent);

                // ตรวจสอบ user type
                let userTypeDetected = 'guest';
                if (isUserLoggedIn && userInfo) {
                    userTypeDetected = userInfo.user_type || 'citizen';
                }
                formData.append('user_type_detected', userTypeDetected);

                // ตรวจสอบโหมด development
                const isDevelopment = window.SKIP_RECAPTCHA_FOR_DEV || false;
                if (isDevelopment) {
                    formData.append('dev_mode', '1');
                }

                // *** ตรวจสอบโหมด Anonymous ***
                const anonymousCheck = document.getElementById('anonymousCheck');
                const isAnonymousMode = anonymousCheck && anonymousCheck.checked;
                if (isAnonymousMode) {
                    formData.append('is_anonymous', '1');
                    formData.append('anonymous_mode', 'true');
                }

                // *** จัดการข้อมูลตามสถานะ login ***
                if (isUserLoggedIn && !isAnonymousMode && userInfo) {
                    // *** User ที่ login แล้ว และไม่ใช่โหมด anonymous ***
                    console.log('✅ Using logged-in user data');

                    // ข้อมูลหลักที่ต้องส่งเสมอ
                    const categoryId = getComplainCategoryId(); // ใช้ฟังก์ชันเดิมที่แก้ไขแล้ว
                    if (categoryId) {
                        formData.append('complain_category_id', categoryId);
                        console.log('📋 Added category ID:', categoryId);
                    } else {
                        throw new Error('ไม่พบหมวดหมู่เรื่องร้องเรียนที่เลือก');
                    }
                    formData.append('complain_topic', document.querySelector('input[name="complain_topic"]').value);
                    formData.append('complain_detail', document.querySelector('textarea[name="complain_detail"]').value);

                    console.log('📝 Sending minimal data for logged-in user');

                } else {
                    // *** Guest หรือ Anonymous mode - ส่งข้อมูลจากฟอร์ม ***
                    console.log('👤 Using form data for guest/anonymous user');
                    // *** Guest หรือ Anonymous mode ***
                    const guestCategoryId = getComplainCategoryId(); // ใช้ฟังก์ชันเดิมที่แก้ไขแล้ว
                    if (guestCategoryId) {
                        formData.append('complain_category_id', guestCategoryId);
                        console.log('📋 Added guest category ID:', guestCategoryId);
                    } else {
                        throw new Error('ไม่พบหมวดหมู่เรื่องร้องเรียนที่เลือก');
                    }


                    // เพิ่มข้อมูลฟอร์มทั้งหมด
                    const formElements = form.elements;
                    for (let i = 0; i < formElements.length; i++) {
                        const element = formElements[i];

                        if (element.type === 'file' || element.type === 'button' || element.type === 'submit') {
                            continue;
                        }

                        if (element.name && element.value !== '') {
                            formData.append(element.name, element.value);
                        }
                    }
                }

                // *** เพิ่มไฟล์ (ทุกโหมดเหมือนกัน) - เวอร์ชันแก้ไข ***
                console.log('📁 Adding files to FormData...');

                if (selectedFiles && selectedFiles.length > 0) {
                    // *** วิธีใหม่: ส่งไฟล์แบบ array ที่ถูกต้อง ***
                    selectedFiles.forEach((file, index) => {
                        // ใช้ชื่อ field เป็น complain_imgs[] สำหรับหลายไฟล์
                        formData.append('complain_imgs[]', file, file.name);
                    });

                    // *** Debug: ตรวจสอบ FormData ***
                    console.log('📋 FormData files check:');
                    let fileCount = 0;
                    for (let pair of formData.entries()) {
                        if (pair[0].includes('complain_imgs')) {
                            fileCount++;
                        }
                    }

                    if (fileCount === 0) {
                        console.warn('⚠️ No files found in FormData!');
                    } else {
                        console.log(`✅ Successfully added ${fileCount} files to FormData`);
                    }
                } else {
                    console.log('📭 No files selected');
                }

                // *** Debug FormData ทั้งหมด ***
                console.log('🔍 Debugging FormData before send...');
                debugFormData(formData);

                // Debug FormData
                console.log('=== FormData Contents ===');
                for (let pair of formData.entries()) {
                    if (pair[1] instanceof File) {
                        console.log(`📎 ${pair[0]}: ${pair[1].name} (${formatFileSize(pair[1].size)})`);
                    } else {
                        console.log(`📝 ${pair[0]}: ${pair[1]}`);
                    }
                }

                // *** Fetch with better error handling ***
                const fetchPromise = fetch(form.action, {
                    method: 'POST',
                    body: formData,
                    signal: AbortSignal.timeout(30000) // 30 seconds timeout
                });

                // *** จัดการ Promise properly ***
                fetchPromise
                    .then(response => {
                        console.log('Response status:', response.status);
                        console.log('Response headers:', response.headers);

                        return response.text().then(text => {
                            console.log('📦 Server response:', text.substring(0, 200));

                            // กรณี response ว่าง (PHP redirect หรือไม่มี output)
                            if (text.length === 0) {
                                Swal.fire({
                                    icon: 'success',
                                    title: 'ส่งเรื่องร้องเรียนสำเร็จ!',
                                    text: 'เรื่องร้องเรียนของคุณถูกส่งเรียบร้อยแล้ว',
                                    confirmButtonColor: '#28a745',
                                    confirmButtonText: 'ตกลง'
                                }).then(() => {
                                    resetFormAfterSubmit();
                                });
                                return;
                            }

                            // *** แก้ไข: แยก JSON ออกจาก response ที่อาจมี HTML/Error message ***
                            let jsonResponse;
                            try {
                                // หาจุดเริ่มต้นของ JSON
                                const jsonStart = text.indexOf('{');
                                const jsonEnd = text.lastIndexOf('}') + 1;

                                if (jsonStart !== -1 && jsonEnd > jsonStart) {
                                    const jsonString = text.substring(jsonStart, jsonEnd);
                                    jsonResponse = JSON.parse(jsonString);
                                    console.log('✅ JSON parsed successfully:', jsonResponse);
                                } else {
                                    throw new Error('No valid JSON found in response');
                                }
                            } catch (jsonError) {
                                console.error('❌ JSON parse error:', jsonError.message);
                                console.log('📄 Full response text:', text);

                                // ถ้าไม่สามารถ parse JSON ได้ แต่ response status OK
                                if (text.includes('success') || text.includes('เรียบร้อย')) {
                                    Swal.fire({
                                        icon: 'success',
                                        title: 'ส่งเรื่องร้องเรียนสำเร็จ!',
                                        text: 'เรื่องร้องเรียนของคุณถูกส่งเรียบร้อยแล้ว',
                                        confirmButtonColor: '#28a745',
                                        confirmButtonText: 'ตกลง'
                                    }).then(() => {
                                        resetFormAfterSubmit();
                                    });
                                } else {
                                    Swal.fire({
                                        icon: 'error',
                                        title: 'เกิดข้อผิดพลาดในการประมวลผล',
                                        text: 'กรุณาลองใหม่อีกครั้ง',
                                        confirmButtonColor: '#e55a2b'
                                    });
                                }
                                return;
                            }

                            // *** จัดการ JSON response ***
                            if (jsonResponse.success === false) {
                                // *** แยก validation error กับ server error ***
                                if (jsonResponse.errors) {
                                    // *** Validation Error - แสดงข้อความ validation ***
                                    console.log('❌ Validation Error:', jsonResponse.errors);

                                    Swal.fire({
                                        icon: 'warning',
                                        title: 'ข้อมูลไม่ถูกต้อง',
                                        html: `
                                    <div style="text-align: left;">
                                        <p style="margin-bottom: 1rem; color: #856404;">กรุณาตรวจสอบข้อมูลที่กรอก:</p>
                                        <div style="background: #fff3cd; padding: 1rem; border-radius: 8px; border: 1px solid #ffeaa7;">
                                            <pre style="margin: 0; color: #856404; font-size: 0.9rem; white-space: pre-wrap; font-family: inherit;">${jsonResponse.errors}</pre>
                                        </div>
                                        <div style="margin-top: 1rem; padding: 0.8rem; background: #e8f4f8; border-radius: 8px; border-left: 4px solid #17a2b8;">
                                            <small style="color: #0c5460;">
                                                <i class="fas fa-lightbulb"></i> 
                                                <strong>คำแนะนำ:</strong> กรุณาตรวจสอบความยาวของข้อความที่กรอก
                                            </small>
                                        </div>
                                    </div>
                                `,
                                        confirmButtonColor: '#e55a2b',
                                        confirmButtonText: 'แก้ไขข้อมูล',
                                        allowOutsideClick: true
                                    }).then(() => {
                                        // *** เน้นไปที่ field ที่มีปัญหา ***
                                        try {
                                            if (jsonResponse.errors.includes('หมวดหมู่') || jsonResponse.errors.includes('complain_category_id')) {
                                                focusAndHighlightField('select[name="complain_category_id"]', 'กรุณาเลือกหมวดหมู่เรื่องร้องเรียน');
                                            } else if (jsonResponse.errors.includes('รายละเอียด')) {
                                                focusAndHighlightField('complainDetailTextarea', 'โปรดใส่รายละเอียด');
                                            } else if (jsonResponse.errors.includes('หัวข้อ')) {
                                                focusAndHighlightField('input[name="complain_topic"]', 'โปรดใส่ชื่อหัวข้อ');
                                            } else if (jsonResponse.errors.includes('ชื่อ')) {
                                                focusAndHighlightField('input[name="complain_by"]', 'โปรดใส่ ชื่อ นามสกุล');
                                            } else if (jsonResponse.errors.includes('เบอร์')) {
                                                focusAndHighlightField('input[name="complain_phone"]', 'โปรดใส่เบอร์โทรศัพท์');
                                            } else if (jsonResponse.errors.includes('ที่อยู่')) {
                                                focusAndHighlightField('input[name="complain_address"]', 'โปรดใส่ที่อยู่ เช่น บ้านเลขที่');
                                            } else if (jsonResponse.errors.includes('ตำบล') || jsonResponse.errors.includes('district')) {
                                                focusAndHighlightField('#district_field', 'กรุณาเลือกตำบล');
                                            } else if (jsonResponse.errors.includes('รหัสไปรษณีย์') || jsonResponse.errors.includes('zipcode')) {
                                                focusAndHighlightField('#zipcode_field', 'โปรดใส่รหัสไปรษณีย์');
                                            }
                                        } catch (focusError) {
                                            console.log('Focus error (ignored):', focusError);
                                        }
                                    });

                                    return; // *** สำคัญ: return เพื่อไม่ให้ไปต่อ ***

                                } else {
                                    // *** Server Error อื่นๆ รวมถึง reCAPTCHA errors ***
                                    let errorMessage = jsonResponse.message || 'เกิดข้อผิดพลาดในระบบ';
                                    let errorType = jsonResponse.error_type || 'unknown';

                                    // จัดการข้อผิดพลาด reCAPTCHA
                                    if (errorType === 'recaptcha_missing') {
                                        errorMessage = 'ไม่พบข้อมูลการยืนยันตัวตน กรุณารีเฟรชหน้าและลองใหม่อีกครั้ง';
                                    } else if (errorType === 'recaptcha_failed') {
                                        errorMessage = 'การยืนยันตัวตนไม่ผ่าน กรุณาลองใหม่อีกครั้ง';
                                    } else if (errorType === 'vulgar_content') {
                                        errorMessage = 'พบคำไม่เหมาะสมในข้อความ กรุณาแก้ไขข้อความ';
                                    } else if (errorType === 'url_content') {
                                        errorMessage = 'ไม่อนุญาตให้มี URL หรือลิงก์ในข้อความ';
                                    }

                                    Swal.fire({
                                        icon: 'error',
                                        title: 'ไม่สามารถส่งเรื่องร้องเรียนได้',
                                        text: errorMessage,
                                        confirmButtonColor: '#e55a2b',
                                        confirmButtonText: 'ลองใหม่',
                                        footer: errorType !== 'unknown' ?
                                            `<small>รหัสข้อผิดพลาด: ${errorType}</small>` : null
                                    });
                                }
                            }
                            else if (jsonResponse.success === true) {
                                // *** แสดงข้อความสำเร็จ ***
                                const isAnonymous = jsonResponse.is_anonymous || false;
                                const complainId = jsonResponse.complain_id || 'ไม่ระบุ';

                                Swal.fire({
                                    icon: 'success',
                                    title: 'ส่งแจ้งเรื่อง หรือร้องเรียน สำเร็จ!',
                                    html: `
                                <div style="text-align: center;">
                                    <div style="margin-bottom: 1.5rem;">
                                        <div style="width: 80px; height: 80px; margin: 0 auto 1rem; background: linear-gradient(135deg, #28a745 0%, #20c997 100%); border-radius: 50%; display: flex; align-items: center; justify-content: center; box-shadow: 0 8px 25px rgba(40, 167, 69, 0.3);">
                                            <i class="fas fa-check" style="font-size: 2.5rem; color: white;"></i>
                                        </div>
                                        <p style="font-size: 1.2rem; margin: 0; color: #2c3e50; font-weight: 600;">
                                            ${jsonResponse.message}
                                        </p>
                                    </div>
                                    
                                    <div style="background: linear-gradient(135deg, #e8f5e8 0%, #f0f9f0 100%); 
                                                padding: 1.5rem; 
                                                border-radius: 15px; 
                                                border: 2px solid #28a745;
                                                margin: 1rem 0;
                                                box-shadow: 0 4px 15px rgba(40, 167, 69, 0.2);">
                                        <div style="font-size: 1.1rem; color: #155724; margin-bottom: 0.5rem;">
                                            <i class="fas fa-clipboard-check" style="color: #28a745; margin-right: 0.5rem;"></i>
                                            <strong>หมายเลขแจ้งเรื่อง ร้องเรียน</strong>
                                        </div>
                                        <div style="font-size: 2rem; font-weight: bold; color: #155724; margin: 0.5rem 0; font-family: 'Courier New', monospace; letter-spacing: 2px; text-shadow: 1px 1px 2px rgba(0,0,0,0.1);">
                                            ${complainId}
                                        </div>
                                        <small style="color: #155724; display: block; margin-bottom: 1rem;">
                                            <i class="fas fa-bookmark"></i> กรุณาเก็บหมายเลขนี้ไว้สำหรับติดตามสถานะ
                                        </small>
                                        
                                        <div style="display: flex; gap: 0.5rem; justify-content: center; flex-wrap: wrap;">
                                            <button onclick="copyComplainId('${complainId}')" 
                                                    class="btn btn-sm btn-success" 
                                                    style="border-radius: 8px; font-size: 0.9rem; padding: 0.5rem 1rem; transition: all 0.3s ease;">
                                                <i class="fas fa-copy"></i> คัดลอกหมายเลข
                                            </button>
                                            <button onclick="goToFollowComplainWithId('${complainId}')" 
                                                    class="btn btn-sm btn-outline-primary"
                                                    style="border-radius: 8px; font-size: 0.9rem; padding: 0.5rem 1rem;">
                                                <i class="fas fa-search"></i> ติดตามสถานะ
                                            </button>
                                        </div>
                                    </div>
                                    
                                    ${isAnonymous ?
                                            '<div style="background: linear-gradient(135deg, #f8f9fa 0%, #e9ecef 100%); padding: 0.8rem; border-radius: 8px; margin: 1rem 0; border-left: 4px solid #6c757d;"><p style="color: #6c757d; margin: 0; font-size: 0.9rem;"><i class="fas fa-user-secret"></i> แจ้งแบบไม่ระบุตัวตน</p></div>' :
                                            ''
                                        }
                                    
                                    <div style="background: linear-gradient(135deg, #fff3cd 0%, #ffeaa7 100%); 
                                                padding: 1rem; 
                                                border-radius: 10px; 
                                                border: 1px solid #ffc107; 
                                                margin-top: 1rem;">
                                        <p style="color: #856404; margin: 0; font-size: 0.95rem;">
                                            <i class="fas fa-info-circle" style="margin-right: 0.5rem;"></i>
                                            <strong>ฟอร์มพร้อมสำหรับการแจ้งเรื่อง หรือ ร้องเรียน ใหม่</strong>
                                            <br>
                                            <small>คุณสามารถแจ้งเรื่อง ร้องเรียนเพิ่มเติมได้ทันที</small>
                                        </p>
                                    </div>
                                </div>
                            `,
                                    showConfirmButton: true,
                                    confirmButtonText: 'เรียบร้อย',
                                    confirmButtonColor: '#28a745',
                                    allowOutsideClick: true,
                                    allowEscapeKey: true,
                                    customClass: {
                                        popup: 'animated fadeInDown faster',
                                        confirmButton: 'pulse-button'
                                    },
                                    showClass: {
                                        popup: 'animate__animated animate__fadeInDown animate__faster'
                                    }
                                }).then(() => {
                                    // *** ไปหน้าที่เหมาะสมตามสถานะการ login ***
                                    if (isUserLoggedIn) {
                                        // User ที่ login แล้ว - ไปหน้า complaints_public/status
                                        window.location.href = '<?= site_url("complaints_public/status") ?>';
                                    } else {
                                        // Guest user - ไปหน้า home
                                        window.location.href = '<?= site_url("home") ?>';
                                    }
                                });

                                return;
                            }

                            // *** Fallback สำหรับ non-JSON response ***
                            if (response.ok) {
                                Swal.fire({
                                    icon: 'success',
                                    title: 'ส่งแจ้งเรื่อง หรือร้องเรียนสำเร็จ!',
                                    text: 'ฟอร์มพร้อมสำหรับการแจ้งเรื่อง หรือร้องเรียนใหม่',
                                    confirmButtonColor: '#28a745',
                                    confirmButtonText: 'ตกลง'
                                }).then(() => {
                                    resetFormAfterSubmit();
                                });
                            } else {
                                throw new Error(`HTTP ${response.status}: ${text.substring(0, 200)}...`);
                            }
                        });
                    })
                    .catch(error => {
                        console.error('❌ Fetch error:', error);

                        // *** ป้องกัน extension errors ***
                        if (error.message && (
                            error.message.includes('message channel') ||
                            error.message.includes('listener indicated an asynchronous response') ||
                            error.message.includes('Extension context invalidated')
                        )) {
                            console.log('🔇 Suppressed browser extension error in fetch:', error.message);
                            return; // ไม่แสดง error alert
                        }

                        let errorMessage = 'ไม่สามารถส่งแจ้งเรื่อง หรือร้องเรียนได้ กรุณาลองใหม่อีกครั้ง';
                        let errorDetail = '';
                        let errorIcon = 'error';

                        if (error.name === 'AbortError') {
                            errorMessage = 'การส่งข้อมูลใช้เวลานานเกินไป';
                            errorDetail = 'กรุณาลองใหม่อีกครั้ง หรือตรวจสอบการเชื่อมต่ออินเทอร์เน็ต';
                            errorIcon = 'warning';
                        } else if (error.message.includes('Failed to fetch')) {
                            errorMessage = 'ไม่สามารถเชื่อมต่อกับเซิร์ฟเวอร์ได้';
                            errorDetail = 'กรุณาตรวจสอบการเชื่อมต่ออินเทอร์เน็ต';
                            errorIcon = 'warning';
                        } else if (error.message.includes('HTTP 500')) {
                            errorMessage = 'เกิดข้อผิดพลาดในเซิร์ฟเวอร์';
                            errorDetail = 'กรุณาติดต่อผู้ดูแลระบบ';
                        } else if (error.message.includes('HTTP 404')) {
                            errorMessage = 'ไม่พบหน้าที่ต้องการ';
                            errorDetail = 'กรุณาตรวจสอบการตั้งค่าระบบ';
                        }

                        Swal.fire({
                            icon: errorIcon,
                            title: 'เกิดข้อผิดพลาด',
                            html: `
                        <div style="text-align: center;">
                            <div style="margin-bottom: 1rem;">
                                <p style="font-size: 1.1rem; margin-bottom: 0.5rem; color: #721c24;">${errorMessage}</p>
                                ${errorDetail ? `<small style="color: #6c757d; display: block; margin-bottom: 1rem;">${errorDetail}</small>` : ''}
                            </div>
                            
                            <div style="background: #f8f9fa; padding: 1rem; border-radius: 8px; margin: 1rem 0; border-left: 4px solid #dc3545;">
                                <p style="margin: 0; color: #721c24; font-size: 0.9rem;">
                                    <i class="fas fa-exclamation-triangle"></i> 
                                    <strong>ข้อมูลของคุณยังไม่ถูกส่ง</strong>
                                    <br>
                                    <small>กรุณาลองใหม่อีกครั้งหรือติดต่อเจ้าหน้าที่</small>
                                </p>
                            </div>
                            
                            <div style="background: #f8f9fa; padding: 1rem; border-radius: 8px; margin-top: 1rem; font-family: monospace; font-size: 0.8rem; color: #6c757d; display: none;" id="error-details">
                                <strong>รายละเอียดข้อผิดพลาด:</strong><br>
                                ${error.message}
                            </div>
                            <button onclick="document.getElementById('error-details').style.display='block'; this.style.display='none';" 
                                    class="btn btn-sm btn-outline-secondary mt-2" id="show-details-btn">
                                <i class="fas fa-info-circle"></i> แสดงรายละเอียด
                            </button>
                        </div>
                    `,
                            confirmButtonColor: '#e55a2b',
                            confirmButtonText: 'ลองใหม่',
                            showCancelButton: true,
                            cancelButtonText: 'ยกเลิก',
                            cancelButtonColor: '#6c757d'
                        }).then((result) => {
                            if (result.isConfirmed) {
                                // ลองส่งใหม่
                                setTimeout(() => {
                                    if (!formSubmitting) {
                                        submitForm();
                                    }
                                }, 1000);
                            }
                        });
                    })
                    .finally(() => {
                        // *** รีเซ็ตปุ่มในทุกกรณี ***
                        setTimeout(() => {
                            submitBtn.disabled = false;
                            submitBtn.innerHTML = originalContent;
                            formSubmitting = false;
                            console.log('✅ Submit process completed');
                        }, 100);
                    });

            } else {
                // *** Form validation failed - แสดงชื่อฟิลด์เป็นภาษาไทย ***
                const invalidFields = form.querySelectorAll(':invalid');
                const fieldNames = Array.from(invalidFields).map(field => {
                    // ใช้ mapping ภาษาไทยก่อน
                    if (fieldNameMapping[field.name]) {
                        return fieldNameMapping[field.name];
                    }
                    if (fieldNameMapping[field.id]) {
                        return fieldNameMapping[field.id];
                    }

                    // ถ้าไม่มีใน mapping ให้หา label
                    const label = document.querySelector(`label[for="${field.id}"]`);
                    if (label) {
                        return label.textContent.replace('*', '').trim();
                    }

                    // fallback ใช้ name หรือ id
                    return field.name || field.id || 'ฟิลด์ที่ไม่ทราบชื่อ';
                });

                Swal.fire({
                    icon: 'warning',
                    title: 'กรุณากรอกข้อมูลให้ครบถ้วน',
                    html: `
                    <div style="text-align: left;">
                        <p style="margin-bottom: 1rem; color: #856404;">มีข้อมูลที่จำเป็นยังไม่ได้กรอก:</p>
                        <div style="background: #fff3cd; padding: 1rem; border-radius: 8px; border: 1px solid #ffeaa7;">
                            <ul style="color: #e55a2b; margin: 0; padding-left: 1.5rem;">
                                ${fieldNames.map(name => `<li>${name}</li>`).join('')}
                            </ul>
                        </div>
                        <div style="margin-top: 1rem; padding: 0.8rem; background: #e8f4f8; border-radius: 8px; border-left: 4px solid #17a2b8;">
                            <small style="color: #0c5460;">
                                <i class="fas fa-lightbulb"></i> 
                                <strong>คำแนะนำ:</strong> กรุณากรอกข้อมูลให้ครบถ้วนก่อนส่ง
                            </small>
                        </div>
                    </div>
                `,
                    confirmButtonColor: '#e55a2b',
                    confirmButtonText: 'ตกลง'
                }).then(() => {
                    // Focus ไปที่ field แรกที่ invalid
                    if (invalidFields.length > 0) {
                        invalidFields[0].focus();
                        invalidFields[0].scrollIntoView({ behavior: 'smooth', block: 'center' });
                    }
                });
            }

        } catch (error) {
            console.error('❌ Submit form error:', error);

            // *** ป้องกัน extension errors ***
            if (error.message && (
                error.message.includes('message channel') ||
                error.message.includes('listener indicated an asynchronous response') ||
                error.message.includes('Extension context invalidated')
            )) {
                console.log('🔇 Suppressed browser extension error in submit:', error.message);
                return false; // ไม่แสดง error alert
            }

            Swal.fire({
                icon: 'error',
                title: 'เกิดข้อผิดพลาดในระบบ',
                html: `
                <div style="text-align: center;">
                    <p style="margin-bottom: 1rem;">ไม่สามารถส่งฟอร์มได้ กรุณาลองใหม่</p>
                    <div style="background: #f8f9fa; padding: 1rem; border-radius: 8px; margin: 1rem 0; border-left: 4px solid #dc3545;">
                        <small style="color: #721c24;">
                            <i class="fas fa-bug"></i> ข้อผิดพลาดในการประมวลผลฟอร์ม
                        </small>
                    </div>
                </div>
            `,
                confirmButtonColor: '#e55a2b',
                confirmButtonText: 'ตกลง'
            });

            formSubmitting = false;

            // รีเซ็ตปุ่มในกรณีเกิดข้อผิดพลาด
            const submitBtn = document.getElementById('submitComplainBtn');
            if (submitBtn) {
                submitBtn.disabled = false;
                submitBtn.innerHTML = '<span style="position: relative; z-index: 2;"><i class="fas fa-paper-plane me-2"></i>ส่งเรื่องร้องเรียน</span><div style="position: absolute; top: 0; left: -100%; width: 100%; height: 100%; background: linear-gradient(90deg, transparent, rgba(255,255,255,0.2), transparent); transition: left 0.5s; z-index: 1;" class="btn-shine"></div>';
            }
        }

        return false;
    }

    // ฟังก์ชันใหม่ - วางก่อน handleFileSelect
    function compressImage(file) {
        return new Promise((resolve) => {
            const canvas = document.createElement('canvas');
            const ctx = canvas.getContext('2d');
            const img = new Image();

            img.onload = function () {
                let { width, height } = img;

                if (width > compressionSettings.maxWidth || height > compressionSettings.maxHeight) {
                    const ratio = Math.min(
                        compressionSettings.maxWidth / width,
                        compressionSettings.maxHeight / height
                    );
                    width *= ratio;
                    height *= ratio;
                }

                canvas.width = width;
                canvas.height = height;
                ctx.drawImage(img, 0, 0, width, height);

                canvas.toBlob((blob) => {
                    if (blob && blob.size < file.size) {
                        const compressedFile = new File([blob], file.name.replace(/\.[^/.]+$/, '.jpg'), {
                            type: 'image/jpeg',
                            lastModified: Date.now()
                        });
                        compressedFile.id = file.id;
                        compressedFile.originalSize = file.size;
                        compressedFile.compressed = true;
                        resolve(compressedFile);
                    } else {
                        resolve(file);
                    }
                }, 'image/jpeg', compressionSettings.quality);
            };

            img.onerror = () => resolve(file);
            img.src = URL.createObjectURL(file);
        });
    }

    /**
     * *** ฟังก์ชันเสริมสำหรับ submitForm() ***
     */

    // *** ฟังก์ชันสำหรับ debug FormData ***
    function debugFormData(formData) {
        console.log('🔍 === FormData Debug ===');

        let totalSize = 0;
        let fileCount = 0;
        let textFields = 0;

        for (let pair of formData.entries()) {
            if (pair[1] instanceof File) {
                fileCount++;
                totalSize += pair[1].size;
                console.log(`📎 File: ${pair[0]} = ${pair[1].name} (${formatFileSize(pair[1].size)}, ${pair[1].type})`);
            } else {
                textFields++;
                console.log(`📝 Field: ${pair[0]} = ${pair[1]}`);
            }
        }

        console.log(`📊 Summary: ${fileCount} files (${formatFileSize(totalSize)}), ${textFields} text fields`);

        // *** ตรวจสอบขนาดรวม ***
        const maxPostSize = 10 * 1024 * 1024; // 10MB (ประมาณ)
        if (totalSize > maxPostSize) {
            console.warn('⚠️ Total file size may exceed server limits:', formatFileSize(totalSize));
            showAlert('warning', 'ไฟล์รวมใหญ่', `ขนาดไฟล์รวม ${formatFileSize(totalSize)} อาจเกินขีดจำกัดของเซิร์ฟเวอร์`);
        }

        console.log('🔍 === End FormData Debug ===');
    }

    // *** ฟังก์ชันแปลงขนาดไฟล์ ***
    function formatFileSize(bytes) {
        if (bytes === 0) return '0 Bytes';

        const k = 1024;
        const sizes = ['Bytes', 'KB', 'MB', 'GB'];
        const i = Math.floor(Math.log(bytes) / Math.log(k));

        return parseFloat((bytes / Math.pow(k, i)).toFixed(2)) + ' ' + sizes[i];
    }

    // *** Helper Functions ที่ใช้ร่วมกับ submitForm() ***
    // *** แก้ไขฟังก์ชัน validateFormFields ให้ทำงานกับ Category Cards ***

    function validateFormFields() {
        const errors = [];

        try {
            // *** แก้ไข: ตรวจสอบ Category ให้รองรับ Category Cards ***
            let complainCategoryId = '';

            // ลอง hidden input ก่อน (สำหรับ Category Cards)
            const hiddenCategoryField = document.querySelector('input[name="complain_category_id"]');
            if (hiddenCategoryField && hiddenCategoryField.value) {
                complainCategoryId = hiddenCategoryField.value.trim();
            }

            // ถ้าไม่มี ลอง select field (fallback สำหรับระบบเก่า)
            if (!complainCategoryId) {
                const selectCategoryField = document.querySelector('select[name="complain_category_id"]');
                if (selectCategoryField && selectCategoryField.value) {
                    complainCategoryId = selectCategoryField.value.trim();
                }
            }

            // ตรวจสอบว่ามีการเลือก category หรือไม่
            if (!complainCategoryId) {
                errors.push('กรุณาเลือกหมวดหมู่เรื่องร้องเรียน');
            }

            // *** ส่วนอื่นๆ ไม่เปลี่ยน - ตรวจสอบหัวข้อ ***
            const topicField = document.querySelector('input[name="complain_topic"]');
            if (topicField) {
                const topic = topicField.value.trim();
                if (topic.length < 4) {
                    errors.push('โปรดใส่หัวข้อร้องเรียน');
                }
            } else {
                errors.push('ไม่พบฟิลด์หัวข้อเรื่องร้องเรียน');
            }

            // *** ส่วนอื่นๆ ไม่เปลี่ยน - ตรวจสอบรายละเอียด ***
            const detailField = document.querySelector('textarea[name="complain_detail"]');
            if (detailField) {
                const detail = detailField.value.trim();
                if (detail.length < 4) {
                    errors.push('โปรดใส่รายละเอียด');
                }
            } else {
                errors.push('ไม่พบฟิลด์รายละเอียดเรื่องร้องเรียน');
            }

            // *** ส่วนที่เหลือไม่เปลี่ยน (ตรวจสอบข้อมูลส่วนตัว) ***
            const anonymousCheck = document.getElementById('anonymousCheck');
            const isAnonymousMode = anonymousCheck && anonymousCheck.checked;

            if (!isUserLoggedIn || isAnonymousMode) {
                // ตรวจสอบชื่อ
                const nameField = document.querySelector('input[name="complain_by"]');
                if (nameField && nameField.style.display !== 'none') {
                    const name = nameField.value.trim();
                    if (name.length < 4) {
                        errors.push('โปรดใส่ ชื่อ-นามสกุล');
                    }
                }

                // ตรวจสอบเบอร์โทร
                const phoneField = document.querySelector('input[name="complain_phone"]');
                if (phoneField && phoneField.style.display !== 'none') {
                    const phone = phoneField.value.trim();
                    if (phone.length < 9 || phone.length > 10) {
                        errors.push('โปรดใส่เบอร์โทรศัพท์');
                    }
                }

                // ตรวจสอบที่อยู่เพิ่มเติม
                const additionalAddressField = document.querySelector('#additional_address_field');
                if (additionalAddressField && additionalAddressField.style.display !== 'none') {
                    const additionalAddress = additionalAddressField.value.trim();
                    if (additionalAddress.length < 1) {
                        errors.push('โปรดใส่ที่อยู่ เช่น บ้านเลขที่');
                    }
                }

                // ตรวจสอบตำบล
                const districtField = document.querySelector('#district_field');
                if (districtField && !districtField.disabled) {
                    const district = districtField.value.trim();
                    if (!district) {
                        errors.push('กรุณาเลือกตำบล');
                    }
                }

                // ตรวจสอบรหัสไปรษณีย์
                const zipcodeField = document.querySelector('#zipcode_field');
                if (zipcodeField && zipcodeField.style.display !== 'none') {
                    const zipcode = zipcodeField.value.trim();
                    if (zipcode.length !== 5 || !/^\d{5}$/.test(zipcode)) {
                        errors.push('รหัสไปรษณีย์ต้องเป็นตัวเลข 5 หลัก');
                    }
                }
            } else {
                console.log('✅ Skipping personal info validation for logged-in user');
            }

        } catch (error) {
            console.error('❌ Validation error:', error);
            errors.push('เกิดข้อผิดพลาดในการตรวจสอบข้อมูล');
        }

        return errors;
    }

    function focusAndHighlightField(selector, message) {
        try {
            let field;

            // หาฟิลด์จาก selector หรือจาก mapping
            if (typeof selector === 'string') {
                field = document.querySelector(selector);

                // ถ้าไม่เจอ ลองหาจาก name
                if (!field) {
                    field = document.querySelector(`[name="${selector}"]`);
                }

                // ถ้าไม่เจอ ลองหาจาก id
                if (!field) {
                    field = document.getElementById(selector);
                }

                // ถ้าไม่เจอ ลองหาจาก mapping
                if (!field) {
                    const reverseMapping = Object.keys(fieldNameMapping).find(key =>
                        fieldNameMapping[key] === selector
                    );
                    if (reverseMapping) {
                        field = document.querySelector(`[name="${reverseMapping}"]`) ||
                            document.getElementById(reverseMapping);
                    }
                }
            } else {
                field = selector;
            }

            if (field) {
                field.focus();
                field.scrollIntoView({ behavior: 'smooth', block: 'center' });

                // เพิ่ม highlight
                field.style.borderColor = '#e55a2b';
                field.style.boxShadow = '0 0 8px rgba(229, 90, 43, 0.5)';
                field.style.backgroundColor = '#ffeee8';

                // แสดงข้อความ tooltip
                if (message) {
                    const tooltip = document.createElement('div');
                    tooltip.className = 'validation-tooltip';
                    tooltip.innerHTML = `<i class="fas fa-exclamation-triangle"></i> ${message}`;
                    tooltip.style.cssText = `
                    position: absolute;
                    background: #e55a2b;
                    color: white;
                    padding: 0.5rem 0.8rem;
                    border-radius: 6px;
                    font-size: 0.8rem;
                    margin-top: -2.5rem;
                    margin-left: 1rem;
                    z-index: 1000;
                    box-shadow: 0 2px 8px rgba(0,0,0,0.2);
                    animation: fadeInDown 0.3s ease;
                `;

                    field.parentNode.appendChild(tooltip);

                    setTimeout(() => {
                        if (tooltip.parentNode) {
                            tooltip.remove();
                        }
                    }, 4000);
                }

                // ลบ highlight หลังจาก 4 วินาที
                setTimeout(() => {
                    field.style.borderColor = '';
                    field.style.boxShadow = '';
                    field.style.backgroundColor = '';
                }, 4000);
            }
        } catch (error) {
            console.log('Focus error (ignored):', error);
        }
    }



    function goToFollowComplainWithId(complainId) {
        console.log('🔍 Going to follow complain with ID:', complainId);

        // ตรวจสอบว่ามี complainId หรือไม่
        if (!complainId || complainId === 'undefined' || complainId === 'null') {
            // แสดง error และเปิดหน้า follow_complain แบบไม่มี ID
            Swal.fire({
                icon: 'error',
                title: 'เกิดข้อผิดพลาด',
                text: 'ไม่สามารถดึงหมายเลขเรื่องร้องเรียนได้'
            }).then(() => {
                window.open('<?= site_url("Pages/follow_complain") ?>', '_blank');
            });
            return;
        }

        // สร้าง URL ตามประเภทผู้ใช้
        let followUrl = '';
        if (isUserLoggedIn) {
            followUrl = `<?= site_url("complaints_public/status") ?>`;
        } else {
            followUrl = `<?= site_url("Pages/follow_complain") ?>?auto_search=${encodeURIComponent(complainId)}`;
        }

        // เปิดในหน้าต่างใหม่
        window.open(followUrl, '_blank');
    }




    function handleComplainSubmit(event) {
        event.preventDefault();

        try {
            if (formSubmitting) {
                // console.log('Form submission already in progress');
                return false;
            }

            // *** ตรวจสอบโหมด anonymous ***
            const anonymousCheck = document.getElementById('anonymousCheck');
            const isAnonymousMode = anonymousCheck && anonymousCheck.checked;

            // console.log('🔍 Anonymous mode check:', isAnonymousMode);

            if (isAnonymousMode) {
                // โหมดไม่ระบุตัวตน - ส่งเลย ไม่ต้องมี modal
                // console.log('🕶️ Anonymous mode - proceeding with submission');
                submitForm();
            } else if (!isUserLoggedIn && !hasConfirmedAsGuest) {
                // โหมดปกติ + ยังไม่ได้ยืนยันเป็น guest
                // console.log('👤 Guest user needs confirmation');
                showModal();
            } else {
                // โหมดปกติ + user authorized แล้ว
                // console.log('✅ User authorized, submitting form');
                submitForm();
            }
        } catch (error) {
            console.error('Handle submit error:', error);
            showAlert('error', 'เกิดข้อผิดพลาด', 'ไม่สามารถส่งฟอร์มได้ กรุณาลองใหม่');
        }

        return false;
    }


    function copyComplainId(complainId) {
        if (navigator.clipboard) {
            navigator.clipboard.writeText(complainId).then(() => {
                showAlert('success', 'คัดลอกแล้ว', `คัดลอกหมายเลขแจ้งเรื่อง ร้องเรียน ${complainId} แล้ว`, 2000);
            }).catch(() => {
                fallbackCopyText(complainId);
            });
        } else {
            fallbackCopyText(complainId);
        }
    }

    function fallbackCopyText(text) {
        const textArea = document.createElement('textarea');
        textArea.value = text;
        document.body.appendChild(textArea);
        textArea.focus();
        textArea.select();
        try {
            document.execCommand('copy');
            showAlert('success', 'คัดลอกแล้ว', `คัดลอกหมายเลขแจ้งเรื่อง ร้องเรียน ${text} แล้ว`, 2000);
        } catch (err) {
            showAlert('error', 'ไม่สามารถคัดลอกได้', 'กรุณาคัดลอกหมายเลขด้วยตนเอง');
        }
        document.body.removeChild(textArea);
    }

    // *** เพิ่มฟังก์ชันสำหรับปุ่มติดตามสถานะในหน้า success ***
    function goToFollowComplain(complainId) {
        if (isUserLoggedIn) {
            // สมาชิก: ไป complaints/status  
            window.location.href = `<?= site_url("complaints/status") ?>`;
        } else {
            // Guest: มาที่ follow_complain พร้อม auto search
            window.location.href = `<?= site_url("Pages/follow_complain") ?>?auto_search=${complainId}`;
        }
    }

    // *** เพิ่มใหม่: ตรวจสอบที่อยู่ ***
    function validateAddress() {
        const additionalAddress = additionalAddressField ? additionalAddressField.value.trim() : '';
        const fullAddress = fullAddressField ? fullAddressField.value.trim() : '';

        // console.log('Validating address:', { additionalAddress, fullAddress });

        if (!additionalAddress) {
            showAlert('warning', 'กรุณากรอกที่อยู่เพิ่มเติม', 'กรุณากรอกบ้านเลขที่ ซอย ถนน หรือรายละเอียดเพิ่มเติม');
            if (additionalAddressField) {
                additionalAddressField.focus();
            }
            return false;
        }

        if (!fullAddress || fullAddress.length < 10) {
            showAlert('warning', 'ที่อยู่ไม่ครบถ้วน', 'กรุณากรอกข้อมูลที่อยู่ให้ครบถ้วน (รหัสไปรษณีย์, อำเภอ, ตำบล)');
            return false;
        }

        return true;
    }

    // ฟังก์ชัน reset form หลังส่งสำเร็จ
    function resetFormAfterSubmit() {
        try {
            const form = document.getElementById('complainForm');
            if (form) {
                form.reset();
            }

            selectedFiles = [];
            updateFileDisplay();

            const fileInput = document.getElementById('complain_imgs');
            if (fileInput) {
                fileInput.value = '';
                fileInput._processing = false;
            }

            const tokenField = document.getElementById('formToken');
            if (tokenField) {
                tokenField.value = '';
            }

            // *** รีเซ็ต Address fields ***
            if (typeof zipcodeField !== 'undefined' && zipcodeField) zipcodeField.value = '';
            if (typeof additionalAddressField !== 'undefined' && additionalAddressField) additionalAddressField.value = '';
            if (typeof fullAddressField !== 'undefined' && fullAddressField) fullAddressField.value = '';
            if (typeof resetToProvinceSelection === 'function') {
                resetToProvinceSelection();
            }

            // console.log('✅ Form reset completed');

        } catch (error) {
            console.error('Reset form error:', error);
        }
    }

    // ==============================================
    // ฟังก์ชันจัดการ Modal
    // ==============================================

    function showModal() {
        try {
            const modalElement = document.getElementById('guestConfirmModal');

            if (!modalElement) {
                console.error('Modal element not found');
                return;
            }

            if (typeof bootstrap !== 'undefined' && bootstrap.Modal) {
                try {
                    guestModalInstance = new bootstrap.Modal(modalElement);
                    guestModalInstance.show();
                    return;
                } catch (e) {
                    console.log('Bootstrap 5 method failed, using fallback:', e);
                }
            }

            // Fallback method
            modalElement.style.display = 'block';
            modalElement.classList.add('show');
            modalElement.style.paddingRight = '15px';
            document.body.classList.add('modal-open');
            document.body.style.paddingRight = '15px';

            if (!document.querySelector('.modal-backdrop')) {
                const backdrop = document.createElement('div');
                backdrop.className = 'modal-backdrop fade show';
                backdrop.setAttribute('data-custom-backdrop', 'true');
                document.body.appendChild(backdrop);
            }
        } catch (error) {
            console.error('Show modal error:', error);
        }
    }

    function hideModal() {
        try {
            const modalElement = document.getElementById('guestConfirmModal');

            if (guestModalInstance && typeof bootstrap !== 'undefined') {
                try {
                    guestModalInstance.hide();
                    guestModalInstance = null;
                    return;
                } catch (e) {
                    console.log('Bootstrap 5 hide failed, using fallback:', e);
                }
            }

            // Fallback method
            if (modalElement) {
                modalElement.style.display = 'none';
                modalElement.classList.remove('show');
                modalElement.style.paddingRight = '';
            }

            document.body.classList.remove('modal-open');
            document.body.style.paddingRight = '';

            const backdrop = document.querySelector('.modal-backdrop[data-custom-backdrop="true"]');
            if (backdrop) {
                backdrop.remove();
            }
        } catch (error) {
            console.error('Hide modal error:', error);
        }
    }

    // ฟังก์ชันดำเนินการต่อโดยไม่ login
    function proceedAsGuest() {
        try {
            hasConfirmedAsGuest = true;
            hideModal();
            showAlert('info', 'ดำเนินการต่อโดยไม่เข้าสู่ระบบ', 'คุณสามารถกรอกข้อมูลและแจ้งเรื่อง ร้องเรียนได้แล้ว', 2000);
        } catch (error) {
            console.error('Proceed as guest error:', error);
        }
    }

    // ฟังก์ชันเปลี่ยนเส้นทางไปหน้า login
    function redirectToLogin() {
        try {
            hideModal();
            const currentUrl = window.location.href;
            sessionStorage.setItem('redirect_after_login', currentUrl);
            window.open('<?= site_url("User") ?>', '_blank');
        } catch (error) {
            console.error('Redirect to login error:', error);
            window.open('<?= site_url("User") ?>', '_blank');
        }
    }

    // ฟังก์ชันแสดง welcome message สำหรับ user ที่ login แล้ว
    function showWelcomeMessageIfLoggedIn() {
        try {
            if (isUserLoggedIn && userInfo) {
                // ตรวจสอบโครงสร้างข้อมูล user
                let userName = 'ผู้ใช้';

                if (userInfo.user_info && userInfo.user_info.name) {
                    userName = userInfo.user_info.name;
                } else if (userInfo.name) {
                    userName = userInfo.name;
                }

                Swal.fire({
                    icon: 'success',
                    title: `ยินดีต้อนรับ ${userName}`,
                    text: 'คุณสามารถแจ้งเรื่องร้องเรียนได้ทันที ข้อมูลของคุณจะถูกใช้โดยอัตโนมัติ',
                    timer: 3000,
                    showConfirmButton: false,
                    position: 'top-end',
                    toast: true,
                    background: 'linear-gradient(135deg, #fde6d9 0%, #ffe5d9 100%)',
                    color: '#8b4513'
                });
            }
        } catch (error) {
            console.error('Show welcome message error:', error);
        }
    }

    // ==============================================
    // ฟังก์ชันจัดการ Anonymous checkbox
    // ==============================================

    function setupAnonymousCheckbox() {
        try {
            const anonymousCheck = document.getElementById('anonymousCheck');

            if (!anonymousCheck) {
                console.log('Anonymous checkbox not found');
                return;
            }

            anonymousCheck.addEventListener('change', function () {
                console.log('📝 Anonymous checkbox changed:', this.checked);

                if (this.checked) {
                    handleAnonymousMode(true);
                } else {
                    handleAnonymousMode(false);
                }
            });

            // console.log('✅ Anonymous checkbox setup completed');

        } catch (error) {
            console.error('❌ Setup anonymous checkbox error:', error);
        }
    }

    function handleAnonymousMode(isAnonymous) {
        try {
            console.log('🔄 Switching to anonymous mode:', isAnonymous);

            // หาฟิลด์ข้อมูลส่วนตัวทั้งหมด
            const nameField = document.querySelector('input[name="complain_by"]');
            const phoneField = document.querySelector('input[name="complain_phone"]');
            const emailField = document.querySelector('input[name="complain_email"]');
            const addressField = document.querySelector('input[name="complain_address"]');
            const additionalAddressField = document.querySelector('#additional_address_field');

            // *** เพิ่มใหม่: หาฟิลด์ที่อยู่ทั้งหมด ***
            const zipcodeField = document.querySelector('#zipcode_field');
            const provinceField = document.querySelector('#province_field');
            const amphoeField = document.querySelector('#amphoe_field');
            const districtField = document.querySelector('#district_field');

            // หาคอนเทนเนอร์
            const nameContainer = nameField?.closest('.form-group');
            const phoneContainer = phoneField?.closest('.col-md-6');
            const emailContainer = emailField?.closest('.form-group');
            const addressContainer = document.querySelector('#zipcode_field')?.closest('.form-group');

            if (isAnonymous) {
                console.log('🕶️ Setting anonymous data...');

                // ลบข้อความแจ้งเตือนของ logged-in user
                const loggedInInfo = document.getElementById('logged-in-user-info');
                if (loggedInInfo) {
                    loggedInInfo.style.display = 'none';
                }

                // แสดงฟิลด์ข้อมูลส่วนตัวกลับมา
                if (nameContainer) nameContainer.style.display = 'block';
                if (phoneContainer) phoneContainer.style.display = 'block';
                if (emailContainer) emailContainer.style.display = 'block';
                if (addressContainer) addressContainer.style.display = 'block';

                // *** เก็บข้อมูลเดิมก่อนเปลี่ยน ***
                [nameField, phoneField, emailField, addressField, additionalAddressField,
                    zipcodeField, provinceField, amphoeField, districtField].forEach(field => {
                        if (field && !field.dataset.originalValue) {
                            field.dataset.originalValue = field.value || '';
                            field.dataset.originalRequired = field.hasAttribute('required') ? 'true' : 'false';
                            field.dataset.originalReadonly = field.readOnly ? 'true' : 'false';
                            field.dataset.originalDisabled = field.disabled ? 'true' : 'false';
                            field.dataset.originalBackground = field.style.backgroundColor;
                        }
                    });

                // *** ตั้งค่าข้อมูลไม่ระบุตัวตนและลบ required ***
                if (nameField) {
                    nameField.value = 'ไม่ระบุตัวตน';
                    nameField.removeAttribute('required');
                    nameField.readOnly = true;
                    nameField.style.backgroundColor = '#f0f0f0';
                    nameField.style.cursor = 'not-allowed';
                }

                if (phoneField) {
                    phoneField.value = '0000000000';
                    phoneField.removeAttribute('required');
                    phoneField.readOnly = true;
                    phoneField.style.backgroundColor = '#f0f0f0';
                    phoneField.style.cursor = 'not-allowed';
                }

                if (emailField) {
                    emailField.value = 'ไม่ระบุตัวตน';
                    emailField.removeAttribute('required');
                    emailField.readOnly = true;
                    emailField.style.backgroundColor = '#f0f0f0';
                    emailField.style.cursor = 'not-allowed';
                }

                if (addressField) {
                    addressField.value = 'ไม่ระบุตัวตน';
                    addressField.removeAttribute('required');
                    addressField.readOnly = true;
                    addressField.style.backgroundColor = '#f0f0f0';
                    addressField.style.cursor = 'not-allowed';
                }

                if (additionalAddressField) {
                    additionalAddressField.value = 'ไม่ระบุตัวตน';
                    additionalAddressField.removeAttribute('required');
                    additionalAddressField.readOnly = true;
                    additionalAddressField.style.backgroundColor = '#f0f0f0';
                    additionalAddressField.style.cursor = 'not-allowed';
                }

                // *** เพิ่มใหม่: ตั้งค่าฟิลด์ที่อยู่ให้เป็น anonymous ***
                if (zipcodeField) {
                    zipcodeField.value = '00000';
                    zipcodeField.removeAttribute('required');
                    zipcodeField.readOnly = true;
                    zipcodeField.style.backgroundColor = '#f0f0f0';
                    zipcodeField.style.cursor = 'not-allowed';
                }

                if (provinceField) {
                    if (provinceField.tagName.toLowerCase() === 'select') {
                        // ถ้าเป็น dropdown ให้แปลงเป็น input
                        const provinceWrapper = provinceField.parentNode;
                        const inputHtml = `
                        <input type="text" id="province_field" class="form-control" 
                               placeholder="จังหวัด" readonly value="ไม่ระบุตัวตน"
                               style="border: none; border-radius: 15px; padding: 1rem; font-size: 1.1rem; transition: all 0.3s ease; box-shadow: 0 6px 20px rgba(255, 120, 73, 0.15); background: #f0f0f0; cursor: not-allowed;">
                    `;
                        provinceField.remove();
                        provinceWrapper.insertAdjacentHTML('beforeend', inputHtml);

                        // อัปเดตตัวแปร
                        window.provinceField = document.getElementById('province_field');
                    } else {
                        provinceField.value = 'ไม่ระบุตัวตน';
                        provinceField.readOnly = true;
                        provinceField.style.backgroundColor = '#f0f0f0';
                        provinceField.style.cursor = 'not-allowed';
                    }
                }

                if (amphoeField) {
                    amphoeField.innerHTML = '<option value="ไม่ระบุตัวตน">ไม่ระบุตัวตน</option>';
                    amphoeField.value = 'ไม่ระบุตัวตน';
                    amphoeField.disabled = true;
                    amphoeField.style.backgroundColor = '#f0f0f0';
                    amphoeField.style.cursor = 'not-allowed';
                }

                if (districtField) {
                    districtField.innerHTML = '<option value="ไม่ระบุตัวตน">ไม่ระบุตัวตน</option>';
                    districtField.value = 'ไม่ระบุตัวตน';
                    districtField.removeAttribute('required');
                    districtField.disabled = true;
                    districtField.style.backgroundColor = '#f0f0f0';
                    districtField.style.cursor = 'not-allowed';
                }

                // *** อัปเดต hidden fields สำหรับ guest address ***
                updateHiddenAddressFields('ไม่ระบุตัวตน', 'ไม่ระบุตัวตน', 'ไม่ระบุตัวตน', '00000');

                // *** อัปเดต address preview ***
                const addressPreview = document.getElementById('address_preview');
                const addressPreviewText = document.getElementById('address_preview_text');
                if (addressPreview && addressPreviewText) {
                    addressPreviewText.textContent = 'ไม่ระบุตัวตน';
                    addressPreview.style.display = 'block';
                }

                // แสดงข้อความแจ้งเตือน
                showAnonymousAlert();

            } else {
                console.log('👤 Restoring normal mode...');

                // แสดงข้อความ logged-in user กลับมา (ถ้าเป็น user ที่ login)
                const loggedInInfo = document.getElementById('logged-in-user-info');
                if (loggedInInfo && isUserLoggedIn) {
                    loggedInInfo.style.display = 'block';
                }

                // *** คืนค่าฟิลด์ทั้งหมด ***
                [nameField, phoneField, emailField, addressField, additionalAddressField,
                    zipcodeField, provinceField, amphoeField, districtField].forEach(field => {
                        if (field && field.dataset.originalValue !== undefined) {
                            // คืนค่าข้อมูลเดิม
                            field.value = field.dataset.originalValue;

                            // คืนค่า required attribute
                            if (field.dataset.originalRequired === 'true') {
                                field.setAttribute('required', 'required');
                            } else {
                                field.removeAttribute('required');
                            }

                            // คืนค่า readonly และ disabled
                            field.readOnly = field.dataset.originalReadonly === 'true';
                            field.disabled = field.dataset.originalDisabled === 'true';

                            // คืนค่า style
                            field.style.backgroundColor = field.dataset.originalBackground || '';
                            field.style.cursor = '';

                            // ลบ dataset
                            delete field.dataset.originalValue;
                            delete field.dataset.originalRequired;
                            delete field.dataset.originalReadonly;
                            delete field.dataset.originalDisabled;
                            delete field.dataset.originalBackground;
                        }
                    });

                // *** คืนค่าตามสถานะ login ***
                if (isUserLoggedIn) {
                    updateFormFieldsBasedOnLoginStatus();
                } else {
                    // *** รีเซ็ต address system สำหรับ guest ***
                    if (typeof resetToProvinceSelection === 'function') {
                        setTimeout(() => {
                            resetToProvinceSelection();
                        }, 100);
                    }
                }

                // ลบข้อความแจ้งเตือน anonymous
                const anonymousAlert = document.getElementById('anonymous-alert');
                if (anonymousAlert) {
                    anonymousAlert.remove();
                }
            }

        } catch (error) {
            console.error('❌ Handle anonymous mode error:', error);
        }
    }

    function showAnonymousAlert() {
        try {
            // ลบข้อความเดิม (ถ้ามี)
            const existingAlert = document.getElementById('anonymous-alert');
            if (existingAlert) {
                existingAlert.remove();
            }

            // สร้างข้อความแจ้งเตือน
            const alertHTML = `
            <div id="anonymous-alert" class="alert alert-warning" style="
                border-radius: 15px; 
                background: linear-gradient(135deg, #fff3cd 0%, #ffeaa7 100%); 
                border: 1px solid rgba(255, 193, 7, 0.5);
                margin-bottom: 2rem;
                box-shadow: 0 4px 15px rgba(255, 193, 7, 0.3);
                backdrop-filter: blur(10px);
            ">
                <div class="d-flex align-items-center">
                    <div style="
                        width: 50px; 
                        height: 50px; 
                        background: linear-gradient(135deg, rgba(255, 193, 7, 0.2) 0%, rgba(255, 193, 7, 0.1) 100%); 
                        border-radius: 50%; 
                        display: flex; 
                        align-items: center; 
                        justify-content: center; 
                        margin-right: 1rem;
                        box-shadow: 0 4px 15px rgba(255, 193, 7, 0.4);
                    ">
                        <i class="fas fa-user-secret" style="font-size: 1.5rem; color: #856404;"></i>
                    </div>
                    <div>
                        <h6 class="mb-1" style="color: #856404; font-weight: 600;">
                            <i class="fas fa-shield-alt me-2"></i>โหมดไม่ระบุตัวตน
                        </h6>
                        <p class="mb-0" style="color: #856404; font-size: 0.9rem;">
                            ข้อมูลของคุณถูกตั้งเป็น "ไม่ระบุตัวตน" เพื่อปกป้องความเป็นส่วนตัว
                            <br><small><i class="fas fa-info-circle me-1"></i>คุณยังสามารถยกเลิกได้โดยคลิกที่ checkbox อีกครั้ง</small>
                        </p>
                    </div>
                </div>
            </div>
        `;

            // แทรกข้อความหลังหัวข้อฟอร์ม
            const formContainer = document.getElementById('complain_form');
            const firstFormGroup = formContainer.querySelector('.form-group');
            if (firstFormGroup) {
                firstFormGroup.insertAdjacentHTML('beforebegin', alertHTML);
            }

            // console.log('✅ Anonymous alert shown');

        } catch (error) {
            console.error('❌ Error showing anonymous alert:', error);
        }
    }

    // ==============================================
    // ฟังก์ชันจัดการ Phone number validation
    // ==============================================

    function setupPhoneValidation() {
        try {
            const phoneInput = document.getElementById('complain_phone');

            if (phoneInput) {
                phoneInput.addEventListener('input', function (e) {
                    // ดึงค่าที่ผู้ใช้กรอก
                    var value = e.target.value;
                    // ลบตัวอักษรที่ไม่ใช่ตัวเลขออก
                    var cleanedValue = value.replace(/\D/g, '');
                    // ถ้าเกิน 10 ตัว ให้ตัดเหลือ 10 ตัว
                    if (cleanedValue.length > 10) {
                        cleanedValue = cleanedValue.slice(0, 10);
                    }
                    // นำค่าที่ได้ไปแสดงในช่องกรอก
                    e.target.value = cleanedValue;
                });
            }
        } catch (error) {
            console.error('Setup phone validation error:', error);
        }
    }

    // ==============================================
    // Event Listeners - แก้ไขให้ปลอดภัย
    // ==============================================

    document.addEventListener('DOMContentLoaded', function () {
		
		// *** เพิ่มใหม่: ตั้งค่า Category Card Selection ***
        setupCategoryCardSelection();
        
        // *** เพิ่มใหม่: Auto-select category จาก URL ***
        setTimeout(() => {
            autoSelectCategoryFromURL();
        }, 300); // รอให้ category cards โหลดเสร็จก่อน
		
        try {
            // ป้องกัน default form submission
            const form = document.getElementById('complainForm');
            if (form) {
                form.addEventListener('submit', function (e) {
                    e.preventDefault();
                    return false;
                });
            }

            // *** เริ่มต้น Address System ***
            initializeAddressSystem();

            // อัพเดท form fields ตามสถานะ login
            updateFormFieldsBasedOnLoginStatus();

            // *** เพิ่มใหม่: ตั้งค่า Anonymous checkbox ***
            setupAnonymousCheckbox();

            // ตั้งค่า Phone validation
            setupPhoneValidation();

            // *** เพิ่มใหม่: ตั้งค่า Category Card Selection ***
            setupCategoryCardSelection();

            // ตั้งค่า upload wrapper
            let uploadWrapperInitialized = false;

            document.addEventListener('dragover', function (e) {
                e.preventDefault();
            });

            document.addEventListener('drop', function (e) {
                e.preventDefault();
            });

            const uploadWrapper = document.querySelector('.file-upload-wrapper');
            if (uploadWrapper && !uploadWrapperInitialized) {
                uploadWrapper.addEventListener('click', function (e) {
                    e.preventDefault();
                    e.stopPropagation();
                    const fileInput = document.getElementById('complain_imgs');
                    if (fileInput) {
                        fileInput.click();
                    }
                });
                uploadWrapperInitialized = true;
            }

            // แสดง modal เฉพาะกรณีที่ไม่ได้ login
            if (!isUserLoggedIn) {
                setTimeout(function () {
                    if (!hasConfirmedAsGuest) {
                        showModal();
                    }
                }, 1000);
            } else {
                // แสดงข้อความต้อนรับสำหรับ user ที่ login แล้ว
                setTimeout(function () {
                    showWelcomeMessageIfLoggedIn();
                }, 500);
            }

            // *** เพิ่มในส่วน DOMContentLoaded เพื่อจัดการ success message ***
            const urlParams = new URLSearchParams(window.location.search);
            const complainId = urlParams.get('complain_id');
            const success = urlParams.get('success');

            if (success === '1' && complainId) {
                Swal.fire({
                    icon: 'success',
                    title: 'ส่งเรื่องร้องเรียนสำเร็จ!',
                    html: `
                    <div style="text-align: center;">
                        <div style="background: linear-gradient(135deg, #e8f5e8 0%, #f0f9f0 100%); 
                                    padding: 1.5rem; 
                                    border-radius: 15px; 
                                    border: 2px solid #28a745;
                                    margin: 1rem 0;">
                            <h3 style="color: #155724; margin-bottom: 0.5rem;">
                                📋 หมายเลขแจ้งเรื่อง ร้องเรียน
                            </h3>
                            <div style="font-size: 1.5rem; font-weight: bold; color: #155724; margin: 1rem 0;">
                                ${complainId}
                            </div>
                            <button onclick="copyComplainId('${complainId}')" 
                                    class="btn btn-sm btn-outline-success" 
                                    style="margin-top: 0.5rem;">
                                <i class="fas fa-copy"></i> คัดลอกหมายเลข
                            </button>
                        </div>
                        <p style="color: #666; margin-top: 1rem;">
                            กรุณาเก็บหมายเลขนี้ไว้สำหรับติดตามสถานะแจ้งเรื่อง ร้องเรียน
                        </p>
                    </div>
                `,
                    confirmButtonText: 'ติดตามสถานะ',
                    confirmButtonColor: '#28a745',
                    showCancelButton: true,
                    cancelButtonText: 'ปิด',
                    cancelButtonColor: '#6c757d'
                }).then((result) => {
                    if (result.isConfirmed) {
                        goToFollowComplain(complainId);
                    }
                });
            }

            // เพิ่ม animation เมื่อหน้าโหลดเสร็จ
            const formContainer = document.querySelector('.container-pages-news');
            if (formContainer) {
                formContainer.style.opacity = '0';
                formContainer.style.transform = 'translateY(20px)';

                setTimeout(() => {
                    formContainer.style.transition = 'all 0.6s ease';
                    formContainer.style.opacity = '1';
                    formContainer.style.transform = 'translateY(0)';
                }, 100);
            }

            //console.log('✅ Complain page initialized successfully with anonymous support');

        } catch (error) {
            console.error('DOMContentLoaded error:', error);
        }
    });

    // ==============================================
    // ป้องกัน Error เพิ่มเติม
    // ==============================================

    // ป้องกัน error จาก browser extension
    if (typeof chrome !== 'undefined' && chrome.runtime) {
        try {
            chrome.runtime.onMessage = chrome.runtime.onMessage || function () { };
        } catch (extensionError) {
            console.log('Chrome extension error (ignored):', extensionError);
        }
    }

    // ป้องกัน undefined function errors
    window.onerror = function (msg, url, lineNo, columnNo, error) {
        if (msg.includes('check_login_status') || msg.includes('message channel')) {
            console.log('Ignored error:', msg);
            return true; // ป้องกันไม่ให้แสดง error ใน console
        }
        return false;
    };

    // ป้องกัน Promise rejection errors
    window.addEventListener('unhandledrejection', function (event) {
        if (event.reason && event.reason.message &&
            (event.reason.message.includes('message channel') ||
                event.reason.message.includes('check_login_status'))) {
            console.log('Ignored promise rejection:', event.reason);
            event.preventDefault();
        }
    });

    // สร้าง global variables ที่ปลอดภัย
    window.isUserLoggedIn = isUserLoggedIn;
    window.userInfo = userInfo;
    window.userAddress = userAddress; // *** เพิ่ม global variable สำหรับที่อยู่ ***
    window.hasConfirmedAsGuest = hasConfirmedAsGuest;

    const base_url = '<?= base_url() ?>';

    console.log('🛡️ Error protection initialized');
    //console.log('✅ All systems ready for complain form with address system and anonymous support');

</script>

<!-- JavaScript สำหรับฟอร์มร้องเรียน - ใช้ข้อมูลที่อยู่ของ user ที่ login -->
<script>
    $(document).ready(function () {
        // console.log('🚀 Complain form initialized');

        // ตรวจสอบสถานะ login และโหลดข้อมูลที่อยู่
        checkLoginAndLoadAddress();

        /**
         * ตรวจสอบ login และโหลดข้อมูลที่อยู่
         */
        function checkLoginAndLoadAddress() {
            // ตรวจสอบจากข้อมูลที่ส่งมาจาก PHP
            if (typeof window.user_login_data !== 'undefined' && window.user_login_data.is_logged_in) {
                console.log('👤 User is logged in:', window.user_login_data.user_type);

                if (window.user_login_data.user_address) {
                    populateAddressFields(window.user_login_data.user_address);
                }
            } else {
                // ถ้าไม่มีข้อมูล global ให้เรียก API
                loadUserAddressFromAPI();
            }
        }

        /**
         * โหลดข้อมูลที่อยู่จาก API
         */
        function loadUserAddressFromAPI() {
            // console.log('📡 Loading user address from API...');

            $.ajax({
                url: '<?= site_url("Pages/get_user_detailed_address") ?>', // ใช้ site_url แทน base_url
                type: 'POST',
                dataType: 'json',
                beforeSend: function () {
                    showAddressLoading(true);
                },
                success: function (response) {
                    console.log('📦 Address API Response:', response);

                    if (response.success && response.is_logged_in && response.address_info) {
                        populateAddressFields(response.address_info);
                        showAddressSuccess('โหลดข้อมูลที่อยู่สำเร็จ');
                    } else if (response.success && response.is_logged_in) {
                        console.log('ℹ️ User logged in but no address data');
                        showAddressInfo('กรุณากรอกข้อมูลที่อยู่');
                    } else {
                        console.log('ℹ️ User not logged in');
                        showAddressInfo('กรุณาเข้าสู่ระบบเพื่อใช้ข้อมูลที่อยู่อัตโนมัติ');
                    }
                },
                error: function (xhr, status, error) {
                    console.error('❌ Address API Error:', error);
                    showAddressError('ไม่สามารถโหลดข้อมูลที่อยู่ได้');
                },
                complete: function () {
                    showAddressLoading(false);
                }
            });
        }

        /**
         * เติมข้อมูลที่อยู่ในฟอร์ม
         */
        function populateAddressFields(addressData) {
            //console.log('📝 Populating address fields:', addressData);

            try {
                // เติมข้อมูลพื้นฐาน
                if (addressData.phone) {
                    $('#complain_phone').val(addressData.phone);
                    console.log('📞 Phone populated:', addressData.phone);
                }

                // ⭐ เติมที่อยู่แบบใหม่ (ใช้ข้อมูลแยกย่อย)
                if (addressData.source === 'detailed_columns') {
                    populateDetailedAddress(addressData);
                } else if (addressData.source === 'parsed_from_full_address') {
                    populateParsedAddress(addressData);
                } else if (addressData.full_address) {
                    // fallback: ใช้ที่อยู่เต็ม
                    $('#complain_address').val(addressData.full_address);
                    console.log('🏠 Full address populated (fallback)');
                }

            } catch (error) {
                console.error('❌ Error populating address fields:', error);
            }
        }

        /**
         * เติมข้อมูลที่อยู่แบบแยกย่อย (จาก detailed columns)
         */
        function populateDetailedAddress(addressData) {
            // console.log('🎯 Populating detailed address');

            // เติมรหัสไปรษณีย์ก่อน (ถ้ามี)
            if (addressData.zipcode) {
                $('#zipcode_field').val(addressData.zipcode);
                console.log('📮 Zipcode populated:', addressData.zipcode);

                // รอให้ระบบประมวลผล zipcode
                setTimeout(() => {
                    // เติมจังหวัด (ไม่มี code)
                    if (addressData.province) {
                        if ($('#province_field').is('select')) {
                            $('#province_field').val(addressData.province);
                        } else {
                            $('#province_field').val(addressData.province);
                        }
                        console.log('🗺️ Province populated:', addressData.province);
                    }

                    // เติมอำเภอ (ไม่มี code)
                    setTimeout(() => {
                        if (addressData.amphoe) {
                            $('#amphoe_field').val(addressData.amphoe);
                            console.log('🏘️ Amphoe populated:', addressData.amphoe);
                        }

                        // เติมตำบล (ไม่มี code)
                        setTimeout(() => {
                            if (addressData.district) {
                                $('#district_field').val(addressData.district);
                                console.log('🏡 District populated:', addressData.district);
                            }

                            // เติมที่อยู่เพิ่มเติม
                            if (addressData.additional_address) {
                                $('#additional_address_field').val(addressData.additional_address);
                                console.log('🏠 Additional address populated:', addressData.additional_address);
                            }

                            // อัปเดทที่อยู่เต็ม
                            setTimeout(() => {
                                if (typeof updateFullAddress === 'function') {
                                    updateFullAddress();
                                }
                            }, 200);

                        }, 300);
                    }, 300);
                }, 500);
            } else {
                // ถ้าไม่มี zipcode ให้เติมข้อมูลโดยตรง
                populateAddressWithoutZipcode(addressData);
            }
        }

        /**
         * เติมข้อมูลที่อยู่โดยไม่ใช้ zipcode
         */
        function populateAddressWithoutZipcode(addressData) {
            // console.log('📍 Populating address without zipcode');

            // สร้างที่อยู่เต็มจากข้อมูลที่มี
            let fullAddress = '';

            if (addressData.additional_address) {
                fullAddress = addressData.additional_address;
            }

            if (addressData.district) {
                fullAddress += (fullAddress ? ' ' : '') + 'ตำบล' + addressData.district;
            }

            if (addressData.amphoe) {
                fullAddress += (fullAddress ? ' ' : '') + 'อำเภอ' + addressData.amphoe;
            }

            if (addressData.province) {
                fullAddress += (fullAddress ? ' ' : '') + 'จังหวัด' + addressData.province;
            }

            if (addressData.zipcode) {
                fullAddress += (fullAddress ? ' ' : '') + addressData.zipcode;
            }

            $('#complain_address').val(fullAddress);
            console.log('🏠 Complete address populated:', fullAddress);
        }

        /**
         * เติมข้อมูลที่อยู่ที่แยกมาจาก full address
         */
        function populateParsedAddress(addressData) {
            console.log('🔧 Populating parsed address');

            // ใช้วิธีเดียวกับ populateAddressWithoutZipcode
            populateAddressWithoutZipcode(addressData);
        }

        /**
         * แสดง loading indicator
         */
        function showAddressLoading(show) {
            if (show) {
                $('.address-status').remove();
                $('.form-group').first().append(`
                <div class="address-status">
                    <small class="text-primary">
                        <i class="fas fa-spinner fa-spin"></i> กำลังโหลดข้อมูลที่อยู่...
                    </small>
                </div>
            `);
            } else {
                $('.address-status').remove();
            }
        }

        /**
         * แสดงข้อความสำเร็จ
         */
        function showAddressSuccess(message) {
            $('.address-status').remove();
            $('.form-group').first().append(`
            <div class="address-status">
                <small class="text-success">
                    <i class="fas fa-check-circle"></i> ${message}
                </small>
            </div>
        `);

            setTimeout(() => {
                $('.address-status').fadeOut();
            }, 3000);
        }

        /**
         * แสดงข้อความแจ้งเตือน
         */
        function showAddressInfo(message) {
            $('.address-status').remove();
            $('.form-group').first().append(`
            <div class="address-status">
                <small class="text-info">
                    <i class="fas fa-info-circle"></i> ${message}
                </small>
            </div>
        `);

            setTimeout(() => {
                $('.address-status').fadeOut();
            }, 5000);
        }

        /**
         * แสดงข้อความผิดพลาด
         */
        function showAddressError(message) {
            $('.address-status').remove();
            $('.form-group').first().append(`
            <div class="address-status">
                <small class="text-danger">
                    <i class="fas fa-exclamation-circle"></i> ${message}
                </small>
            </div>
        `);

            setTimeout(() => {
                $('.address-status').fadeOut();
            }, 5000);
        }

        /**
         * ปุ่มโหลดข้อมูลที่อยู่ใหม่
         */
        $(document).on('click', '.btn-reload-address', function (e) {
            e.preventDefault();
            loadUserAddressFromAPI();
        });

        // console.log('✅ Complain form address loading completed');
    });
</script>