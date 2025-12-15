<div class="text-center pages-head">
    <span class="font-pages-head">แบบฟอร์มออนไลน์</span>
</div>
</div>
<img src="<?php echo base_url('docs/welcome-btm-light-other.png'); ?>">

<div class="bg-pages" style="background: #ffffff; min-height: 100vh; padding: 2rem 0;">
    <div class="container-pages-news mb-5 mt-4" style="position: relative; z-index: 10; background: white; border-radius: 25px; box-shadow: 0 15px 35px rgba(0, 0, 0, 0.1); padding: 2rem; margin: 0 auto; max-width: 1400px; overflow: hidden;">
        
        <!-- เพิ่ม decorative element -->
        <div style="position: absolute; top: 0; left: 0; right: 0; height: 5px; background: linear-gradient(90deg, #667eea, #764ba2, #667eea); background-size: 200% 100%; animation: gradientShift 3s ease-in-out infinite;"></div>
        
        <!-- Alert Messages -->
        <?php if (!empty($success_message)): ?>
            <div class="alert alert-success alert-dismissible fade show" role="alert" style="border-radius: 15px;">
                <i class="fas fa-check-circle me-2"></i><?php echo $success_message; ?>
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        <?php endif; ?>
        
        <?php if (!empty($error_message)): ?>
            <div class="alert alert-danger alert-dismissible fade show" role="alert" style="border-radius: 15px;">
                <i class="fas fa-exclamation-circle me-2"></i><?php echo $error_message; ?>
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        <?php endif; ?>
        
        <!-- Header Section -->
        <div class="text-center mb-5">
            <div style="width: 100px; height: 100px; margin: 0 auto 1.5rem; background: linear-gradient(135deg, rgba(102, 126, 234, 0.15) 0%, rgba(118, 75, 162, 0.15) 100%); border-radius: 50%; display: flex; align-items: center; justify-content: center; box-shadow: 0 8px 25px rgba(102, 126, 234, 0.3);">
                <i class="fas fa-download" style="font-size: 3rem; color: #667eea; text-shadow: 0 2px 8px rgba(102, 126, 234, 0.4);"></i>
            </div>
            
            <h2 class="mb-3" style="color: #2d3748; font-weight: 600;">ดาวน์โหลดแบบฟอร์มและยื่นคำร้อง</h2>
            <p class="text-muted mb-4" style="font-size: 1.1rem; max-width: 800px; margin: 0 auto;">
                ท่านสามารถใช้งานระบบ E-Services ในรูปแบบ One Stop Service โดยคลิกเลือกแบบฟอร์มที่ท่านต้องการ ดังนี้
            </p>
            
            <!-- สถิติ -->
            <div class="row text-center mb-4">
                <div class="col-lg-3 col-md-6 mb-3">
                    <div style="background: linear-gradient(135deg, rgba(102, 126, 234, 0.1) 0%, rgba(118, 75, 162, 0.1) 100%); padding: 1.5rem; border-radius: 15px; height: 100%;">
                        <i class="fas fa-layer-group" style="font-size: 2rem; color: #667eea; margin-bottom: 0.5rem;"></i>
                        <h4 style="color: #2d3748; margin-bottom: 0.25rem;"><?php echo number_format($statistics['total_types']); ?></h4>
                        <small class="text-muted">ประเภทเอกสาร</small>
                    </div>
                </div>
                <div class="col-lg-3 col-md-6 mb-3">
                    <div style="background: linear-gradient(135deg, rgba(23, 162, 184, 0.1) 0%, rgba(20, 164, 77, 0.1) 100%); padding: 1.5rem; border-radius: 15px; height: 100%;">
                        <i class="fas fa-folder-open" style="font-size: 2rem; color: #17a2b8; margin-bottom: 0.5rem;"></i>
                        <h4 style="color: #2d3748; margin-bottom: 0.25rem;"><?php echo number_format($statistics['total_categories']); ?></h4>
                        <small class="text-muted">หมวดหมู่เอกสาร</small>
                    </div>
                </div>
                <div class="col-lg-3 col-md-6 mb-3">
                    <div style="background: linear-gradient(135deg, rgba(40, 167, 69, 0.1) 0%, rgba(25, 135, 84, 0.1) 100%); padding: 1.5rem; border-radius: 15px; height: 100%;">
                        <i class="fas fa-wpforms" style="font-size: 2rem; color: #28a745; margin-bottom: 0.5rem;"></i>
                        <h4 style="color: #2d3748; margin-bottom: 0.25rem;"><?php echo number_format($statistics['total_forms']); ?></h4>
                        <small class="text-muted">แบบฟอร์มทั้งหมด</small>
                    </div>
                </div>
                <div class="col-lg-3 col-md-6 mb-3">
                    <div style="background: linear-gradient(135deg, rgba(255, 193, 7, 0.1) 0%, rgba(255, 193, 7, 0.2) 100%); padding: 1.5rem; border-radius: 15px; height: 100%;">
                        <i class="fas fa-download" style="font-size: 2rem; color: #ffc107; margin-bottom: 0.5rem;"></i>
                        <h4 style="color: #2d3748; margin-bottom: 0.25rem;"><?php echo number_format($statistics['total_downloads']); ?></h4>
                        <small class="text-muted">ครั้งการดาวน์โหลด</small>
                    </div>
                </div>
            </div>
        </div>

        <!-- ขั้นตอนที่ 1 -->
        <div class="bg-how-e-service mt-4 mb-4" style="background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); color: white; padding: 1rem 1.5rem; border-radius: 15px; box-shadow: 0 6px 20px rgba(102, 126, 234, 0.3);">
            <i class="fas fa-download me-2"></i>
            <span class="font-e-service-how" style="font-size: 1.2rem; font-weight: 600;">ขั้นตอนที่ 1 ดาวน์โหลดเอกสารออนไลน์</span>
        </div>

        <!-- แบบฟอร์มยอดนิยม -->
        <?php if (!empty($popular_forms)): ?>
        <div class="mb-5">
            <div style="background: linear-gradient(135deg, rgba(255, 193, 7, 0.1) 0%, rgba(255, 193, 7, 0.2) 100%); padding: 2rem; border-radius: 15px; margin-bottom: 2rem;">
                <div class="d-flex align-items-center justify-content-between mb-3">
                    <h4 style="color: #2d3748; margin: 0;">
                        <i class="fas fa-star text-warning me-2"></i>แบบฟอร์มยอดนิยม
                    </h4>
                    <small class="text-muted">อิงจากจำนวนการดาวน์โหลด</small>
                </div>
                <div class="row">
                    <?php foreach ($popular_forms as $form): ?>
                    <div class="col-lg-4 col-md-6 mb-3">
                        <div class="d-flex align-items-center p-3 h-100" style="background: white; border-radius: 12px; box-shadow: 0 4px 12px rgba(0,0,0,0.1); transition: all 0.3s ease;" onmouseover="this.style.transform='translateY(-3px)'; this.style.boxShadow='0 8px 20px rgba(0,0,0,0.15)'" onmouseout="this.style.transform='translateY(0)'; this.style.boxShadow='0 4px 12px rgba(0,0,0,0.1)'">
                            <div style="width: 50px; height: 50px; background: <?php echo $form->esv_type_color ?: '#667eea'; ?>20; border-radius: 12px; display: flex; align-items: center; justify-content: center; margin-right: 1rem; flex-shrink: 0;">
                                <i class="<?php echo $form->esv_type_icon ?: 'fas fa-file-alt'; ?>" style="color: <?php echo $form->esv_type_color ?: '#667eea'; ?>; font-size: 1.3rem;"></i>
                            </div>
                            <div class="flex-grow-1 min-width-0">
                                <h6 class="mb-1" style="color: #2d3748; font-size: 0.95rem; line-height: 1.3;" title="<?php echo htmlspecialchars($form->form_name); ?>">
                                    <?php echo mb_strlen($form->form_name) > 25 ? mb_substr(htmlspecialchars($form->form_name), 0, 25) . '...' : htmlspecialchars($form->form_name); ?>
                                </h6>
                                <small class="text-muted d-block mb-1"><?php echo htmlspecialchars($form->category_display); ?></small>
                                <small class="text-info">
                                    <i class="fas fa-download me-1"></i><?php echo $form->download_count_text; ?>
                                </small>
                            </div>
                            <div class="ms-2">
                                <div class="btn-group-vertical">
                                    <a href="<?php echo $form->view_url; ?>" 
                                       class="btn btn-outline-primary btn-sm mb-1" 
                                       style="border-radius: 6px; font-size: 0.8rem; padding: 0.25rem 0.5rem;" 
                                       target="_blank" title="ดูตัวอย่าง">
                                        <i class="fas fa-eye"></i>
                                    </a>
                                    <a href="<?php echo $form->download_url; ?>" 
                                       class="btn btn-sm" 
                                       style="background: <?php echo $form->esv_type_color ?: '#667eea'; ?>; color: white; border: none; border-radius: 6px; font-size: 0.8rem; padding: 0.25rem 0.5rem;" 
                                       target="_blank" 
                                       onclick="trackDownload(<?php echo $form->form_id; ?>)"
                                       title="ดาวน์โหลด">
                                        <i class="fas fa-download"></i>
                                    </a>
                                </div>
                            </div>
                        </div>
                    </div>
                    <?php endforeach; ?>
                </div>
            </div>
        </div>
        <?php endif; ?>

        <!-- โครงสร้างแบบฟอร์ม: ประเภท → หมวดหมู่ → แบบฟอร์ม -->
        <?php if (!empty($document_structure)): ?>
            <?php foreach ($document_structure as $type_data): ?>
                <div class="mb-5">
                    <!-- Header ประเภทเอกสาร -->
                    <div class="document-type-header mb-4" style="background: linear-gradient(135deg, <?php echo $type_data['type_color']; ?>15 0%, <?php echo $type_data['type_color']; ?>25 100%); padding: 2rem 1.5rem; border-radius: 20px; border-left: 6px solid <?php echo $type_data['type_color']; ?>; position: relative; overflow: hidden;">
                        <!-- Background Pattern -->
                        <div style="position: absolute; top: 0; right: 0; width: 200px; height: 200px; background: url('data:image/svg+xml,<svg xmlns=\"http://www.w3.org/2000/svg\" viewBox=\"0 0 100 100\"><circle cx=\"50\" cy=\"50\" r=\"40\" fill=\"none\" stroke=\"<?php echo urlencode($type_data['type_color']); ?>\" stroke-width=\"0.5\" opacity=\"0.1\"/></svg></div>
                        
                        <div class="d-flex align-items-center position-relative">
                            <div style="width: 80px; height: 80px; background: <?php echo $type_data['type_color']; ?>20; border-radius: 20px; display: flex; align-items: center; justify-content: center; margin-right: 1.5rem; border: 2px solid <?php echo $type_data['type_color']; ?>40;">
                                <i class="<?php echo $type_data['type_icon']; ?>" style="color: <?php echo $type_data['type_color']; ?>; font-size: 2.5rem;"></i>
                            </div>
                            <div class="flex-grow-1">
                                <h3 class="mb-2" style="color: #2d3748; font-weight: 700; font-size: 1.8rem;">
                                    <?php echo htmlspecialchars($type_data['type_name']); ?>
                                </h3>
                                <?php if (!empty($type_data['type_description'])): ?>
                                    <p class="mb-2 text-muted" style="font-size: 1rem; line-height: 1.5;">
                                        <?php echo htmlspecialchars($type_data['type_description']); ?>
                                    </p>
                                <?php endif; ?>
                                <div class="d-flex flex-wrap gap-3">
                                    <span class="badge" style="background: <?php echo $type_data['type_color']; ?>; color: white; padding: 0.5rem 1rem; border-radius: 12px; font-size: 0.85rem;">
                                        <i class="fas fa-folder-open me-1"></i><?php echo $type_data['total_categories']; ?> หมวดหมู่
                                    </span>
                                    <span class="badge" style="background: <?php echo $type_data['type_color']; ?>80; color: white; padding: 0.5rem 1rem; border-radius: 12px; font-size: 0.85rem;">
                                        <i class="fas fa-file-alt me-1"></i><?php echo $type_data['total_forms']; ?> แบบฟอร์ม
                                    </span>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- หมวดหมู่และแบบฟอร์ม -->
                    <?php if (!empty($type_data['categories'])): ?>
                        <div class="categories-container">
                            <?php foreach ($type_data['categories'] as $category_data): ?>
                                <?php if (!empty($category_data['forms'])): ?>
                                    <div class="category-section mb-4">
                                        <!-- Header หมวดหมู่ -->
                                        <div class="category-header mb-3" style="background: linear-gradient(135deg, rgba(248, 249, 250, 0.8) 0%, rgba(233, 236, 239, 0.8) 100%); padding: 1.5rem; border-radius: 15px; border-left: 4px solid <?php echo $category_data['category_color']; ?>;">
                                            <div class="d-flex align-items-center justify-content-between">
                                                <div class="d-flex align-items-center">
                                                    <div style="width: 50px; height: 50px; background: <?php echo $category_data['category_color']; ?>20; border-radius: 12px; display: flex; align-items: center; justify-content: center; margin-right: 1rem;">
                                                        <i class="<?php echo $category_data['category_icon']; ?>" style="color: <?php echo $category_data['category_color']; ?>; font-size: 1.5rem;"></i>
                                                    </div>
                                                    <div>
                                                        <h5 style="color: #2d3748; font-weight: 600; margin-bottom: 0.25rem;">
                                                            <?php echo htmlspecialchars($category_data['category_name']); ?>
                                                        </h5>
                                                        <?php if (!empty($category_data['category_description'])): ?>
                                                            <small class="text-muted d-block"><?php echo htmlspecialchars($category_data['category_description']); ?></small>
                                                        <?php endif; ?>
                                                        <small class="text-info">
                                                            <i class="fas fa-file-alt me-1"></i><?php echo count($category_data['forms']); ?> แบบฟอร์ม
                                                        </small>
                                                    </div>
                                                </div>
                                                <div class="text-end">
                                                    <?php if ($category_data['category_fee'] > 0): ?>
                                                        <span class="badge bg-info text-dark mb-1 d-block" style="border-radius: 8px;">
                                                            <i class="fas fa-money-bill-wave me-1"></i>ค่าธรรมเนียม <?php echo number_format($category_data['category_fee']); ?> บาท
                                                        </span>
                                                    <?php endif; ?>
                                                    <?php if ($category_data['category_process_days'] > 0): ?>
                                                        <span class="badge bg-warning text-dark" style="border-radius: 8px;">
                                                            <i class="fas fa-clock me-1"></i><?php echo $category_data['category_process_days']; ?> วันทำการ
                                                        </span>
                                                    <?php endif; ?>
                                                </div>
                                            </div>
                                        </div>

                                        <!-- แบบฟอร์มในหมวดหมู่ -->
                                        <div class="forms-grid">
                                            <?php foreach ($category_data['forms'] as $form): ?>
                                                <div class="form-card mb-3" style="background: white; border-radius: 15px; box-shadow: 0 3px 12px rgba(0,0,0,0.08); transition: all 0.3s ease; overflow: hidden; border: 1px solid #f0f0f0;" onmouseover="this.style.transform='translateY(-3px)'; this.style.boxShadow='0 8px 25px rgba(0,0,0,0.12)'" onmouseout="this.style.transform='translateY(0)'; this.style.boxShadow='0 3px 12px rgba(0,0,0,0.08)'">
                                                    <div class="row g-0 align-items-center">
                                                        <div class="col-md-8">
                                                            <div class="p-4">
                                                                <div class="d-flex align-items-start">
                                                                    <div style="width: 45px; height: 45px; background: <?php echo $category_data['category_color']; ?>15; border-radius: 10px; display: flex; align-items: center; justify-content: center; margin-right: 1rem; flex-shrink: 0;">
                                                                        <i class="fas fa-file-alt" style="color: <?php echo $category_data['category_color']; ?>; font-size: 1.3rem;"></i>
                                                                    </div>
                                                                    <div class="flex-grow-1">
                                                                        <h6 class="mb-2" style="color: #2d3748; font-weight: 600; line-height: 1.4;">
                                                                            <?php echo htmlspecialchars($form['form_name']); ?>
                                                                        </h6>
                                                                        <?php if (!empty($form['form_description'])): ?>
                                                                            <p class="text-muted mb-2" style="font-size: 0.9rem; line-height: 1.4;">
                                                                                <?php echo mb_strlen($form['form_description']) > 80 ? mb_substr(htmlspecialchars($form['form_description']), 0, 80) . '...' : htmlspecialchars($form['form_description']); ?>
                                                                            </p>
                                                                        <?php endif; ?>
                                                                        <div class="d-flex flex-wrap gap-2 mt-2">
                                                                            <small class="badge bg-light text-dark" style="border-radius: 6px;">
                                                                                <i class="fas fa-download me-1"></i><?php echo $form['download_count_text']; ?>
                                                                            </small>
                                                                            <?php if (!empty($form['file_size'])): ?>
                                                                                <small class="badge bg-light text-dark" style="border-radius: 6px;">
                                                                                    <i class="fas fa-file me-1"></i><?php echo $form['file_size']; ?>
                                                                                </small>
                                                                            <?php endif; ?>
                                                                        </div>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                        </div>
                                                        <div class="col-md-4">
                                                            <div class="p-4 text-center">
                                                                <div class="d-grid gap-2">
                                                                    <!-- ปุ่มดูตัวอย่าง -->
                                                                    <a href="<?php echo $form['view_url']; ?>" 
                                                                       class="btn btn-outline-primary" 
                                                                       style="border-radius: 12px; border-color: <?php echo $category_data['category_color']; ?>; color: <?php echo $category_data['category_color']; ?>;" 
                                                                       target="_blank">
                                                                        <i class="fas fa-eye me-2"></i>ดูตัวอย่าง
                                                                    </a>
                                                                    <!-- ปุ่มดาวน์โหลด -->
                                                                    <a href="<?php echo $form['download_url']; ?>" 
                                                                       class="btn" 
                                                                       style="background: linear-gradient(135deg, <?php echo $category_data['category_color']; ?> 0%, <?php echo $category_data['category_color']; ?>dd 100%); color: white; border: none; border-radius: 12px; font-weight: 600; box-shadow: 0 4px 12px <?php echo $category_data['category_color']; ?>30;" 
                                                                       target="_blank"
                                                                       onclick="trackDownload(<?php echo $form['form_id']; ?>)">
                                                                        <i class="fas fa-download me-2"></i>ดาวน์โหลด
                                                                    </a>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                            <?php endforeach; ?>
                                        </div>
                                    </div>
                                <?php endif; ?>
                            <?php endforeach; ?>
                        </div>
                    <?php endif; ?>
                </div>
            <?php endforeach; ?>
        <?php else: ?>
            <!-- ไม่มีแบบฟอร์ม -->
            <div class="text-center py-5">
                <i class="fas fa-file-excel" style="font-size: 4rem; color: #dee2e6; margin-bottom: 1rem;"></i>
                <h5 style="color: #6c757d;">ยังไม่มีแบบฟอร์มในระบบ</h5>
                <p class="text-muted">กรุณาติดต่อเจ้าหน้าที่เพื่อข้อมูลเพิ่มเติม</p>
            </div>
        <?php endif; ?>

        <!-- หมายเหตุ -->
        <div class="mt-5 p-4" style="background: linear-gradient(135deg, rgba(220, 53, 69, 0.1) 0%, rgba(220, 53, 69, 0.15) 100%); border-radius: 15px; border-left: 5px solid #dc3545;">
            <h6 style="color: #dc3545; font-weight: 600; margin-bottom: 1rem;">
                <i class="fas fa-exclamation-triangle me-2"></i>หมายเหตุ
            </h6>
            <p class="font-e-service-danger mb-0" style="color: #721c24; line-height: 1.6;">
                โปรดเตรียมไฟล์เอกสารแนบประกอบคำขอให้ครบถ้วน เช่น สำเนาบัตรประชาชน สำเนาทะเบียนบ้าน สำเนาหน้าบัญชีสมุดธนาคาร หนังสือมอบอำนาจพร้อมติดอากร เป็นต้น
            </p>
        </div>

        <!-- ขั้นตอนที่ 2 -->
        <div class="bg-how-e-service mt-5 mb-3" style="background: linear-gradient(135deg, #28a745 0%, #20c997 100%); color: white; padding: 1rem 1.5rem; border-radius: 15px; box-shadow: 0 6px 20px rgba(40, 167, 69, 0.3);">
            <i class="fas fa-upload me-2"></i>
            <span class="font-e-service-how" style="font-size: 1.2rem; font-weight: 600;">ขั้นตอนที่ 2 ยื่นเอกสารออนไลน์</span>
        </div>

        <!-- ปุ่มยื่นเอกสาร -->
        <div class="bg-content-e-service text-center" style="background: linear-gradient(135deg, rgba(40, 167, 69, 0.1) 0%, rgba(32, 201, 151, 0.1) 100%); padding: 3rem 2rem; border-radius: 15px; margin-bottom: 3rem;">
            <div style="width: 80px; height: 80px; margin: 0 auto 1.5rem; background: linear-gradient(135deg, rgba(40, 167, 69, 0.15) 0%, rgba(32, 201, 151, 0.15) 100%); border-radius: 50%; display: flex; align-items: center; justify-content: center; box-shadow: 0 8px 25px rgba(40, 167, 69, 0.3);">
                <i class="fas fa-paper-plane" style="font-size: 2.5rem; color: #28a745; text-shadow: 0 2px 8px rgba(40, 167, 69, 0.4);"></i>
            </div>
            
            <h4 class="mb-3" style="color: #2d3748; font-weight: 600;">พร้อมยื่นเอกสารแล้วใช่ไหม?</h4>
            <p class="text-muted mb-4">กรอกข้อมูลและแนบเอกสารที่ดาวน์โหลดไปแล้วเพื่อยื่นคำขอออนไลน์</p>
            
            <a class="btn btn-success btn-lg" 
               href="<?php echo site_url('Esv_ods/submit_document'); ?>" 
               style="background: linear-gradient(135deg, #28a745 0%, #20c997 100%); border: none; border-radius: 15px; padding: 1rem 2.5rem; font-size: 1.1rem; font-weight: 600; box-shadow: 0 6px 20px rgba(40, 167, 69, 0.3); transition: all 0.3s ease;">
                <i class="fas fa-paper-plane me-2"></i>คลิกเพื่อยื่นเอกสาร
            </a>
        </div>
    </div>
</div>

<!-- CSS เพิ่มเติม -->
<style>
@keyframes gradientShift {
    0%, 100% { background-position: 0% 50%; }
    50% { background-position: 100% 50%; }
}

.document-type-header:hover {
    transform: translateY(-2px);
    box-shadow: 0 12px 35px rgba(0,0,0,0.1);
}

.category-header:hover {
    background: linear-gradient(135deg, rgba(248, 249, 250, 0.9) 0%, rgba(233, 236, 239, 0.9) 100%) !important;
}

.form-card:hover .btn {
    transform: translateY(-1px);
}

.btn-success:hover {
    transform: translateY(-3px);
    box-shadow: 0 10px 30px rgba(40, 167, 69, 0.4) !important;
}

@media (max-width: 768px) {
    .font-pages-head {
        font-size: 2rem !important;
    }
    
    .container-pages-news {
        margin: 0 1rem !important;
        padding: 1.5rem !important;
    }
    
    .document-type-header {
        padding: 1.5rem 1rem !important;
    }
    
    .document-type-header h3 {
        font-size: 1.4rem !important;
    }
    
    .category-header {
        padding: 1rem !important;
    }
    
    .form-card .col-md-8,
    .form-card .col-md-4 {
        flex: 0 0 100%;
        max-width: 100%;
    }
    
    .form-card .row {
        text-align: center;
    }
}

.min-width-0 {
    min-width: 0;
}

.categories-container {
    animation: fadeInUp 0.6s ease-out;
}

@keyframes fadeInUp {
    from {
        opacity: 0;
        transform: translateY(20px);
    }
    to {
        opacity: 1;
        transform: translateY(0);
    }
}
</style>

<!-- JavaScript -->
<script>
function trackDownload(formId) {
    // อัปเดตสถิติการดาวน์โหลด
    fetch('<?php echo site_url("Esv_ods/track_form_download"); ?>', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/x-www-form-urlencoded',
        },
        body: 'form_id=' + formId
    }).catch(function(error) {
        console.log('Download tracking failed:', error);
    });
}

// เพิ่ม smooth scrolling และ animations
document.addEventListener('DOMContentLoaded', function() {
    // Animation สำหรับ category sections
    const observerOptions = {
        threshold: 0.1,
        rootMargin: '0px 0px -50px 0px'
    };
    
    const observer = new IntersectionObserver((entries) => {
        entries.forEach(entry => {
            if (entry.isIntersecting) {
                entry.target.style.animation = 'fadeInUp 0.6s ease-out';
            }
        });
    }, observerOptions);
    
    // สังเกตการแสดงผลของ category sections
    document.querySelectorAll('.category-section').forEach(section => {
        observer.observe(section);
    });
    
    // เพิ่ม hover effects
    document.querySelectorAll('.form-card').forEach(card => {
        card.addEventListener('mouseenter', function() {
            this.style.transform = 'translateY(-3px)';
            this.style.boxShadow = '0 8px 25px rgba(0,0,0,0.12)';
        });
        
        card.addEventListener('mouseleave', function() {
            this.style.transform = 'translateY(0)';
            this.style.boxShadow = '0 3px 12px rgba(0,0,0,0.08)';
        });
    });
});
</script>