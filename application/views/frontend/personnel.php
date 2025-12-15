<div class="text-center pages-head">
    <span class="font-pages-head"><?= $type->pname ?></span>
</div>
</div>
<img src="<?php echo base_url('docs/welcome-btm-light-other.png'); ?>">

<div class="bg-pages ">
    <div class="container-pages-detail">
        <div class="page-center">
            <?php 
            // หาจำนวน slots สูงสุดจากข้อมูลจริง
            $max_slot_number = 120; // ค่าเริ่มต้นขั้นต่ำ
            
            foreach ($all_positions as $position) {
                if ($position->position_order > $max_slot_number) {
                    $max_slot_number = $position->position_order;
                }
            }
            
            // สร้าง array ของ slots ทั้งหมด (dynamic ตามจำนวนจริง)
            $all_slots = [];
            for ($i = 1; $i <= $max_slot_number; $i++) {
                $all_slots[$i] = null; // เริ่มต้นเป็น null (ว่าง)
            }
            
            // เติมข้อมูลจริงลงใน slots ที่มีข้อมูล
            foreach ($all_positions as $position) {
                $slot_number = $position->position_order;
                
                // ตรวจสอบว่ามีข้อมูลจริงหรือไม่
                $has_name = !empty(trim($position->data['name'] ?? ''));
                $has_position = !empty(trim($position->data['position'] ?? ''));
                
                if ($has_name || $has_position) {
                    $all_slots[$slot_number] = $position;
                }
            }
            
            // แยกตำแหน่งหลัก (slot 1) 
            $main_position = $all_slots[1];
            ?>
            
            <!-- แสดงตำแหน่งหลัก (slot 1) -->
            <?php if ($main_position): ?>
                <div class="bg-personnel-s">
                    <div class="rounded-image-s">
                        <?php if (!empty($main_position->data['image'])): ?>
                            <img src="<?php echo base_url('docs/img/' . $main_position->data['image']); ?>" width="100%" height="100%">
                        <?php else: ?>
                            <img src="<?php echo base_url('docs/ex_personnel.png'); ?>" width="100%" height="100%">
                        <?php endif; ?>
                    </div>
                </div>

                <div class="bg-personnel-n">
                    <div class="mt-3 center-center">
                        <span class="font-p-name">
                            <?php echo !empty($main_position->data['name']) ? $main_position->data['name'] : 'ว่าง'; ?>
                        </span>
                        <?php if (!empty($main_position->data['position'])): ?>
                            <span class="font-p-detail "><?= $main_position->data['position']; ?></span>
                        <?php endif; ?>
                        <?php if (!empty($main_position->data['phone'])): ?>
                            <span class="font-p-detail">เบอร์ <?= $main_position->data['phone']; ?></span>
                        <?php endif; ?>
                        <?php if (!empty($main_position->data['email'])): ?>
                            <span class="font-p-detail"><?= $main_position->data['email']; ?></span>
                        <?php endif; ?>
                    </div><br>
                </div>
            <?php else: ?>
                <!-- แสดงตำแหน่งหลักว่าง -->
                <div class="bg-personnel-s">
                    <div class="rounded-image-s">
                        <img src="<?php echo base_url('docs/ex_personnel.png'); ?>" width="100%" height="100%">
                    </div>
                </div>
                <div class="bg-personnel-n">
                    <div class="mt-3 center-center">
                        <span class="font-p-name">ว่าง</span>
                        <span class="font-p-detail">-</span>
                    </div><br>
                </div>
            <?php endif; ?>
        </div>
        
        <!-- แสดงตำแหน่งอื่นๆ (slot 2-61) แถวละ 3 คน -->
        <?php 
        // คำนวณจำนวนแถวที่ต้องแสดง (dynamic)
        // ถ้ามีข้อมูลใน slot สูงสุดที่ไหน ให้แสดงถึงแถวนั้น
        $max_filled_slot = 0;
        for ($i = 2; $i <= $max_slot_number; $i++) {
            if ($all_slots[$i] !== null) {
                $max_filled_slot = $i;
            }
        }
        
        // ถ้าไม่มีข้อมูลเลย ให้แสดงอย่างน้อย 1 แถว (แถวที่ 2)
        if ($max_filled_slot < 2) {
            $max_filled_slot = 4; // แสดงแถวที่ 2 (slots 2,3,4)
        }
        
        // คำนวณจำนวนแถวที่ต้องแสดง
        $last_row_to_show = ceil(($max_filled_slot - 1) / 3) + 1;
        ?>
        
        <div style="margin-top: 25px;">
            <?php 
            // แสดงแถวละ 3 คน (slots 2-61)
            for ($row = 2; $row <= $last_row_to_show; $row++): 
            ?>
                <div class="row mb-3">
                    <?php 
                    // แต่ละแถวมี 3 columns (slots)
                    for ($col = 0; $col < 3; $col++):
                        // คำนวณ slot number: แถว 2 = slots 2,3,4; แถว 3 = slots 5,6,7; ฯลฯ
                        $slot_number = (($row - 2) * 3) + $col + 2;
                        
                        // ไม่เกินจำนวน slots สูงสุดที่มี
                        if ($slot_number > $max_slot_number) break;
                        
                        $position = $all_slots[$slot_number];
                    ?>
                        <div class="col-4 col-md-4 mb-3 center-center">
                            <?php if ($position): ?>
                                <!-- แสดงข้อมูลจริง -->
                                <div class="bg-personnel-s">
                                    <div class="rounded-image-s">
                                        <?php if (!empty($position->data['image'])): ?>
                                            <img src="<?php echo base_url('docs/img/' . $position->data['image']); ?>" width="100%" height="100%">
                                        <?php else: ?>
                                            <img src="<?php echo base_url('docs/ex_personnel.png'); ?>" width="100%" height="100%">
                                        <?php endif; ?>
                                    </div>
                                </div>
                                <div class="bg-personnel-n">
                                    <div class="mt-3 center-center">
                                        <span class="font-p-name">
                                            <?php echo !empty($position->data['name']) ? $position->data['name'] : 'ว่าง'; ?>
                                        </span>
                                        <?php if (!empty($position->data['position'])): ?>
                                            <span class="font-p-detail "><?= $position->data['position']; ?></span>
                                        <?php endif; ?>
                                        <?php if (!empty($position->data['phone'])): ?>
                                            <span class="font-p-detail">เบอร์ <?= $position->data['phone']; ?></span>
                                        <?php endif; ?>
                                        <?php if (!empty($position->data['email'])): ?>
                                            <span class="font-p-detail"><?= $position->data['email']; ?></span>
                                        <?php endif; ?>
                                    </div><br>
                                </div>
                            <?php else: ?>
                                <!-- แสดงช่องว่าง -->
                                <!-- <div class="bg-personnel-s" style="opacity: 0.3;">
                                    <div class="rounded-image-s">
                                        <img src="<?php echo base_url('docs/ex_personnel.png'); ?>" width="100%" height="100%">
                                    </div>
                                </div>
                                <div class="bg-personnel-n" style="opacity: 0.3;">
                                    <div class="mt-3 center-center">
                                        <span class="font-p-name" style="color: #ccc;">ว่าง</span>
                                        <span class="font-p-detail" style="color: #ccc;">ตำแหน่งที่ <?= $slot_number ?></span>
                                    </div><br>
                                </div> -->
                            <?php endif; ?>
                        </div>
                    <?php endfor; ?>
                </div>
            <?php endfor; ?>
        </div>
        
        <!-- แสดงข้อความเมื่อไม่มีข้อมูลเลย (ไม่มีแม้แต่ตำแหน่งหลัก) -->
        <?php if (!$main_position && $max_filled_slot == 0): ?>
            <div class="text-center py-5">
                <div class="bg-personnel-s">
                    <div class="rounded-image-s">
                        <img src="<?php echo base_url('docs/ex_personnel.png'); ?>" width="100%" height="100%">
                    </div>
                </div>
                <div class="bg-personnel-n">
                    <div class="mt-3 center-center">
                        <span class="font-p-name">ยังไม่มีข้อมูลบุคลากร</span>
                        <span class="font-p-detail">ข้อมูล<?= $type->pname ?>จะแสดงที่นี่เมื่อมีการเพิ่มข้อมูล</span>
                    </div><br>
                </div>
            </div>
        <?php endif; ?>
        
    </div>
</div><br><br><br>