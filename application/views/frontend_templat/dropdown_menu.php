<?php
// รับพารามิเตอร์
$menu_type = isset($menu_type) ? $menu_type : 'general';
$dropdown_style = isset($dropdown_style) ? $dropdown_style : '';


switch ($menu_type) {
    case 'general': // ข้อมูลทั่วไป
?>
        <div class="dropdown-content" <?php echo $dropdown_style; ?>>
            <ul class="no-bullets mt-2" style="margin-left: 300px">
                <div class="dropdown-left">
                    <a href="<?php echo site_url('Pages/history'); ?>">
                        <img src="<?php echo base_url('docs/b.icon-nav.png'); ?>">
                        <span class="font-nav">&nbsp;&nbsp;ประวัติความเป็นมา</span>
                    </a>
                    <a href="<?php echo site_url('Pages/ci'); ?>">
                        <img src="<?php echo base_url('docs/b.icon-nav.png'); ?>">
                        <span class="font-nav">&nbsp;&nbsp;ข้อมูลชุมชน</span>
                    </a>
                    <a href="<?php echo site_url('Pages/authority'); ?>">
                        <img src="<?php echo base_url('docs/b.icon-nav.png'); ?>">
                        <span class="font-nav">&nbsp;&nbsp;อำนาจหน้าที่</span>
                    </a>
                    <a href="<?php echo site_url('Pages/gci'); ?>">
                        <img src="<?php echo base_url('docs/b.icon-nav.png'); ?>">
                        <span class="font-nav">&nbsp;&nbsp;ข้อมูลสภาพทั่วไป</span>
                    </a>
                    <a href="<?php echo site_url('Pages/msg_pres'); ?>">
                        <img src="<?php echo base_url('docs/b.icon-nav.png'); ?>">
                        <span class="font-nav">&nbsp;&nbsp;สารจากนายก (MES)</span>
                    </a>
                    <a href="<?php echo site_url('Pages/mission'); ?>">
                        <img src="<?php echo base_url('docs/b.icon-nav.png'); ?>">
                        <span class="font-nav">&nbsp;&nbsp;ภารกิจและความรับผิดชอบ</span>
                    </a>
                    <a href="<?php echo site_url('Pages/si'); ?>">
                        <img src="<?php echo base_url('docs/b.icon-nav.png'); ?>">
                        <span class="font-nav">&nbsp;&nbsp;ยุทธศาสตร์การพัฒนาด้านโครงสร้างพื้นฐาน</span>
                    </a>
                </div>
                <div class="dropdown-center" style="margin-left: 50px">
                    <a href="<?php echo site_url('Pages/vision'); ?>">
                        <img src="<?php echo base_url('docs/b.icon-nav.png'); ?>">
                        <span class="font-nav">&nbsp;&nbsp;วิสัยทัศน์และพันธกิจ</span>
                    </a>
                    <a href="<?php echo site_url('Pages/video'); ?>">
                        <img src="<?php echo base_url('docs/b.icon-nav.png'); ?>">
                        <span class="font-nav">&nbsp;&nbsp;วิดีทัศน์</span>
                    </a>
                    <a href="<?php echo site_url('Pages/motto'); ?>">
                        <img src="<?php echo base_url('docs/b.icon-nav.png'); ?>">
                        <span class="font-nav">&nbsp;&nbsp;คำขวัญ</span>
                    </a>
                    <a href="<?php echo site_url('Pages/executivepolicy'); ?>">
                        <img src="<?php echo base_url('docs/b.icon-nav.png'); ?>">
                        <span class="font-nav">&nbsp;&nbsp;นโยบายของผู้บริหาร</span>
                    </a>
                    <a href="<?php echo site_url('Pages/news_dla'); ?>">
                        <img src="<?php echo base_url('docs/b.icon-nav.png'); ?>">
                        <span class="font-nav">&nbsp;&nbsp;หนังสือราชการ สถ.</span>
                    </a>
                    <?php if (!empty($prov_base_url)): ?>
    <a href="<?php echo $prov_base_url; ?>" target="_blank" rel="noopener noreferrer">
<?php else: ?>
    <a href="<?php echo site_url('Pages/prov_local_doc'); ?>">
<?php endif; ?>
        <img src="<?php echo base_url('docs/b.icon-nav.png'); ?>">
        <span class="font-nav">&nbsp;&nbsp;หนังสือราชการ สถ.จ.</span>
    </a>
                    <a href="<?php echo site_url('Pages/egp'); ?>">
                        <img src="<?php echo base_url('docs/b.icon-nav.png'); ?>">
                        <span class="font-nav">&nbsp;&nbsp;ข่าวจัดซื้อจัดจ้าง e-GP</span>
                    </a>
                </div>
                <div class="dropdown-right">
                    <a href="<?php echo site_url('Pages/news'); ?>">
                        <img src="<?php echo base_url('docs/b.icon-nav.png'); ?>">
                        <span class="font-nav">&nbsp;&nbsp;ข่าวประชาสัมพันธ์</span>
                    </a>
                    <a href="<?php echo site_url('Pages/activity'); ?>">
                        <img src="<?php echo base_url('docs/b.icon-nav.png'); ?>">
                        <span class="font-nav">&nbsp;&nbsp;ข่าวสาร / กิจกรรม</span>
                    </a>
                    <a href="<?php echo site_url('Pages/procurement_tbl_w0_search'); ?>">
                        <img src="<?php echo base_url('docs/b.icon-nav.png'); ?>">
                        <span class="font-nav">&nbsp;&nbsp;ข่าวจัดซื้อจัดจ้าง</span>
                    </a>
                    <a href="<?php echo site_url('Pages/travel'); ?>">
                        <img src="<?php echo base_url('docs/b.icon-nav.png'); ?>">
                        <span class="font-nav">&nbsp;&nbsp;สถานที่สำคัญ-ท่องเที่ยว</span>
                    </a>
                    <a href="<?php echo site_url('Pages/otop'); ?>">
                        <img src="<?php echo base_url('docs/b.icon-nav.png'); ?>">
                        <span class="font-nav">&nbsp;&nbsp;ผลิตภัณฑ์ชุมชน (OTOP)</span>
                    </a>
                    <a href="<?php echo site_url('Pages/newsletter'); ?>">
                        <img src="<?php echo base_url('docs/b.icon-nav.png'); ?>">
                        <span class="font-nav">&nbsp;&nbsp;จดหมายข่าว</span>
                    </a>
                    <a href="<?php echo site_url('Pages/contact'); ?>">
                        <img src="<?php echo base_url('docs/b.icon-nav.png'); ?>">
                        <span class="font-nav">&nbsp;&nbsp;ติดต่อเรา</span>
                    </a>
                </div>
            </ul>
        </div>
    <?php
        break;

    case 'personnel': // โครงสร้างบุคลากร
    ?>
        <div class="dropdown-content" <?php echo $dropdown_style; ?> >
            <ul class="no-bullets mt-2" style="margin-left: 600px;">
                <div class="structure-dropdown-container">
                    <?php foreach ($structure_columns as $column_index => $column_items): ?>
                        <div class="structure-dropdown-column">
                            <?php foreach ($column_items as $item): ?>
                                <?php
                                // ตรวจสอบว่าเป็น sub item หรือไม่
                                $is_sub_item = isset($item->psub) && $item->psub == 1;
                                $item_class = $is_sub_item ? 'structure-sub-item' : 'structure-main-item';
                                ?>
                                <?php if (isset($item->type) && $item->type === 'main'): ?>
                                    <!-- แผนผังโครงสร้างรวม -->
                                    <a href="<?php echo site_url('Pages/site_map'); ?>" class="dropdown-link <?php echo $item_class; ?>">
                                        <?php if ($is_sub_item): ?>
                                            <span class="sub-indent"></span>
                                        <?php endif; ?>
                                        <img src="<?php echo base_url('docs/b.icon-nav.png'); ?>" class="link-icon">
                                        <span class="font-nav" style="flex-grow: 1; margin-left: 8px; display: inline-block; word-wrap: break-word; max-width: calc(100% - 30px); line-height: 1.2;"><?php echo $item->pname; ?></span>
                                    </a>
                                <?php else: ?>
                                    <!-- ประเภทตำแหน่งอื่นๆ -->
                                    <a href="<?php echo site_url('Pages/personnel/' . $item->peng); ?>" class="dropdown-link <?php echo $item_class; ?>">
                                        <?php if ($is_sub_item): ?>
                                            <span class="sub-indent"></span>
                                        <?php endif; ?>
                                        <img src="<?php echo base_url('docs/b.icon-nav.png'); ?>" class="link-icon">
                                        <span class="font-nav" style="flex-grow: 1; margin-left: 8px; display: inline-block; word-wrap: break-word; max-width: calc(100% - 30px); line-height: 1.2;"><?php echo $item->pname; ?></span>
                                    </a>
                                <?php endif; ?>
                            <?php endforeach; ?>
                        </div>
                    <?php endforeach; ?>
                </div>
            </ul>
        </div>
    <?php
        break;

    case 'service': // บริการประชาชน
    ?>
        <div class="dropdown-content" <?php echo $dropdown_style; ?>>
            <ul class="no-bullets mt-2" style="margin-left: 200px">
                <div class="dropdown-left">
                    <a href="<?php echo site_url('Pages/pbsv_utilities'); ?>">
                        <img src="<?php echo base_url('docs/b.icon-nav.png'); ?>">
                        <span class="font-nav">&nbsp;&nbsp;สาธารณูปโภค</span>
                    </a>
                    <a href="<?php echo site_url('Pages/pbsv_cjc'); ?>">
                        <img src="<?php echo base_url('docs/b.icon-nav.png'); ?>">
                        <span class="font-nav">&nbsp;&nbsp;ศูนย์ยุติธรรมชุมชน</span>
                    </a>
                    <a href="<?php echo site_url('Pages/pbsv_cac'); ?>">
                        <img src="<?php echo base_url('docs/b.icon-nav.png'); ?>">
                        <span class="font-nav">&nbsp;&nbsp;ศูนย์ช่วยเหลือประชาชน</span>
                    </a>
                    <a href="<?php echo site_url('Pages/pbsv_cig'); ?>">
                        <img src="<?php echo base_url('docs/b.icon-nav.png'); ?>">
                        <span class="font-nav">&nbsp;&nbsp;ศูนย์ข้อมูลข่าวสารทางราชการ</span>
                    </a>
                    <a href="<?php echo get_config_value('elec'); ?>" target="_blank">
                        <img src="<?php echo base_url('docs/b.icon-nav.png'); ?>">
                        <span class="font-nav">&nbsp;&nbsp;ศูนย์ข้อมูลข่าวสารอิเล็กทรอนิกส์</span>
                    </a>
                    <a href="<?php echo site_url('Pages/pbsv_ahs'); ?>">
                        <img src="<?php echo base_url('docs/b.icon-nav.png'); ?>">
                        <span class="font-nav">&nbsp;&nbsp;หลักประกันสุขภาพ</span>
                    </a>
                    <a href="<?php echo site_url('Pages/odata'); ?>">
                        <img src="<?php echo base_url('docs/b.icon-nav.png'); ?>">
                        <span class="font-nav">&nbsp;&nbsp;ฐานข้อมูลเปิดภาครัฐ (Open Data)</span>
                    </a>
                    <a href="https://www.nacc.go.th/NACCPPWFC?" target="_blank">
                        <img src="<?php echo base_url('docs/b.icon-nav.png'); ?>">
                        <span class="font-nav">&nbsp;&nbsp;ยกระดับเจตจำนงทางการเมืองในการต่อต้านการทุจริต</span>
                    </a>
                    <a href="<?php echo site_url('Pages/pbsv_statistics'); ?>">
                        <img src="<?php echo base_url('docs/b.icon-nav.png'); ?>">
                        <span class="font-nav">&nbsp;&nbsp;ข้อมูลสถิติการให้บริการ</span>
                    </a>
                </div>
                <div class="dropdown-center">
                    <a href="<?php echo site_url('Elderly_aw_ods/adding_elderly_aw_ods'); ?>">
                        <img src="<?php echo base_url('docs/b.icon-nav.png'); ?>">
                        <span class="font-nav">&nbsp;&nbsp;เบี้ยยังชีพผู้สูงอายุ / ผู้พิการ</span>
                    </a>
                    <a href="<?php echo site_url('Kid_aw_ods'); ?>">
                        <img src="<?php echo base_url('docs/b.icon-nav.png'); ?>">
                        <span class="font-nav">&nbsp;&nbsp;เงินอุดหนุนเด็กแรกเกิด</span>
                    </a>
                    <a href="<?php echo site_url('Pages/pbsv_gup'); ?>">
                        <img src="<?php echo base_url('docs/b.icon-nav.png'); ?>">
                        <span class="font-nav">&nbsp;&nbsp;คู่มือสำหรับประชาชน</span>
                    </a>
                    <a href="https://dbdregcom.dbd.go.th/mainsite/index.php?id=28" target="_blank">
                        <img src="<?php echo base_url('docs/b.icon-nav.png'); ?>">
                        <span class="font-nav">&nbsp;&nbsp;คู่มือจดทะเบียนพาณิชย์</span>
                    </a>
                    <a href="<?php echo site_url('Pages/manual_esv_detail/1'); ?>">
                        <img src="<?php echo base_url('docs/b.icon-nav.png'); ?>">
                        <span class="font-nav">&nbsp;&nbsp;คู่มือการใช้งาน e-Service</span>
                    </a>
                    <a href="<?php echo site_url('Pages/pbsv_sags'); ?>">
                        <img src="<?php echo base_url('docs/b.icon-nav.png'); ?>">
                        <span class="font-nav">&nbsp;&nbsp;คู่มือและมาตรฐานการให้บริการ</span>
                    </a>
                    <a href="<?php echo site_url('Pages/pbsv_ems'); ?>">
                        <img src="<?php echo base_url('docs/b.icon-nav.png'); ?>">
                        <span class="font-nav">&nbsp;&nbsp;งานกู้ชีพ / การบริการการแพทย์ฉุกเฉิน (EMS)</span>
                    </a>
                    <a href="<?php echo site_url('Pages/pbsv_oppr'); ?>">
                        <img src="<?php echo base_url('docs/b.icon-nav.png'); ?>">
                        <span class="font-nav">&nbsp;&nbsp;งานอาสาสมัครป้องกันภัยฝ่ายพลเรือน (อปพร.)</span>
                    </a>
                </div>
                <div class="dropdown-right">
                    <a href="<?php echo site_url('Pages/adding_complain'); ?>">
                        <img src="<?php echo base_url('docs/b.icon-nav.png'); ?>">
                        <span class="font-nav">&nbsp;&nbsp;ร้องเรียนร้องทุกข์</span>
                    </a>
                    <a href="<?php echo site_url('Esv_ods/submit_document'); ?>">
                        <img src="<?php echo base_url('docs/b.icon-nav.png'); ?>">
                        <span class="font-nav">&nbsp;&nbsp;ยื่นเอกสารออนไลน์</span>
                    </a>
                    <a href="<?php echo site_url('Suggestions/adding_suggestions'); ?>">
                        <img src="<?php echo base_url('docs/b.icon-nav.png'); ?>">
                        <span class="font-nav">&nbsp;&nbsp;ช่องทางรับฟังความคิดเห็น</span>
                    </a>
                    <a href="<?php echo site_url('Corruption/report_form'); ?>">
                        <img src="<?php echo base_url('docs/b.icon-nav.png'); ?>">
                        <span class="font-nav">&nbsp;&nbsp;แจ้งเรื่องทุจริตหน่วยงานภาครัฐ</span>
                    </a>
                    <a href="<?php echo site_url('Queue/adding_queue'); ?>">
                        <img src="<?php echo base_url('docs/b.icon-nav.png'); ?>">
                        <span class="font-nav">&nbsp;&nbsp;จองคิวติดต่อราชการออนไลน์</span>
                    </a>
                    <a href="<?php echo site_url('Pages/q_a'); ?>">
                        <img src="<?php echo base_url('docs/b.icon-nav.png'); ?>">
                        <span class="font-nav">&nbsp;&nbsp;กระทู้ ถาม-ตอบ (Q&A)</span>
                    </a>
                    <a href="<?php echo site_url('Pages/questions'); ?>">
                        <img src="<?php echo base_url('docs/b.icon-nav.png'); ?>">
                        <span class="font-nav">&nbsp;&nbsp;คำถามที่พบบ่อย (FAQ)</span>
                    </a>
                    <a href="<?php echo site_url('Pages/pbsv_e_book'); ?>">
                        <img src="<?php echo base_url('docs/b.icon-nav.png'); ?>">
                        <span class="font-nav">&nbsp;&nbsp; e-Book</span>
                    </a>
					<a href="<?php echo site_url('pages/e_mags_view'); ?>">
                        <img src="<?php echo base_url('docs/b.icon-nav.png'); ?>">
                        <span class="font-nav">&nbsp;&nbsp; วารสารออนไลน์</span>
                    </a>
                    <a href="<?php echo site_url('Assessment'); ?>">
                        <img src="<?php echo base_url('docs/b.icon-nav.png'); ?>">
                        <span class="font-nav">&nbsp;&nbsp;แบบประเมินความพึงพอใจการให้บริการ</span>
                    </a>
                </div>
            </ul>
        </div>
    <?php
        break;

    case 'plan': // แผนงาน
    ?>
        <div class="dropdown-content" <?php echo $dropdown_style; ?>>
            <ul class="no-bullets mt-2" style="margin-left: 250px">
                <div class="dropdown-left">
                    <a href="<?php echo site_url('Pages/plan_pdl'); ?>">
                        <img src="<?php echo base_url('docs/b.icon-nav.png'); ?>">
                        <span class="font-nav">&nbsp;&nbsp;แผนพัฒนาท้องถิ่น</span>
                    </a>
                    <a href="<?php echo site_url('Pages/plan_psi'); ?>">
                        <img src="<?php echo base_url('docs/b.icon-nav.png'); ?>">
                        <span class="font-nav">&nbsp;&nbsp;แผนแม่บทสารสนเทศ</span>
                    </a>
                    <a href="<?php echo site_url('Pages/plan_pop'); ?>">
                        <img src="<?php echo base_url('docs/b.icon-nav.png'); ?>">
                        <span class="font-nav">&nbsp;&nbsp;แผนปฏิบัติการจัดซื้อจัดจ้าง</span>
                    </a>
                    <a href="<?php echo site_url('Pages/plan_paca'); ?>">
                        <img src="<?php echo base_url('docs/b.icon-nav.png'); ?>">
                        <span class="font-nav">&nbsp;&nbsp;แผนปฏิบัติการป้องกันการทุจริต</span>
                    </a>
                    <a href="<?php echo site_url('Pages/plan_pmda'); ?>">
                        <img src="<?php echo base_url('docs/b.icon-nav.png'); ?>">
                        <span class="font-nav">&nbsp;&nbsp;แผนป้องกันและบรรเทาสาธารณภัยประจำปี</span>
                    </a>
                    <a href="<?php echo site_url('Pages/plan_dpy'); ?>">
                        <img src="<?php echo base_url('docs/b.icon-nav.png'); ?>">
                        <span class="font-nav">&nbsp;&nbsp;แผนการบริหารและพัฒนาทรัพยากรบุคคลประจำปี</span>
                    </a>
                </div>
                <div class="dropdown-center">
                    <a href="<?php echo site_url('Pages/plan_pc3y'); ?>">
                        <img src="<?php echo base_url('docs/b.icon-nav.png'); ?>">
                        <span class="font-nav">&nbsp;&nbsp;แผนอัตรากำลัง 3 ปี</span>
                    </a>
                    <a href="<?php echo site_url('Pages/plan_pds3y'); ?>">
                        <img src="<?php echo base_url('docs/b.icon-nav.png'); ?>">
                        <span class="font-nav">&nbsp;&nbsp;แผนพัฒนาบุคลากร 3 ปี</span>
                    </a>
                    <a href="<?php echo site_url('Pages/plan_poa'); ?>">
                        <img src="<?php echo base_url('docs/b.icon-nav.png'); ?>">
                        <span class="font-nav">&nbsp;&nbsp;แผนการดำเนินงานประจำปี</span>
                    </a>
                    <a href="<?php echo site_url('Pages/plan_pdpa'); ?>">
                        <img src="<?php echo base_url('docs/b.icon-nav.png'); ?>">
                        <span class="font-nav">&nbsp;&nbsp;แผนพัฒนาบุคลากรประจำปี</span>
                    </a>
                    <a href="<?php echo site_url('Pages/plan_pcra'); ?>">
                        <img src="<?php echo base_url('docs/b.icon-nav.png'); ?>">
                        <span class="font-nav">&nbsp;&nbsp;แผนการจัดเก็บรายได้ประจำปี</span>
                    </a>
                    <a href="<?php echo site_url('Pages/plan_progress'); ?>">
                        <img src="<?php echo base_url('docs/b.icon-nav.png'); ?>">
                        <span class="font-nav">&nbsp;&nbsp;แผนและความก้าวหน้าในการดำเนินงานและการใช้งบประมาณ</span>
                    </a>
                </div>
                <div class="dropdown-right">
                    <a href="https://itas.nacc.go.th/go/iit/<?php echo get_config_value('eit_iit'); ?>" target="_blank">
                        <img src="<?php echo base_url('docs/b.icon-nav.png'); ?>">
                        <span class="font-nav">&nbsp;&nbsp;IIT แบบวัดการรับรู้ภายใน</span>
                    </a>
                    <a href="https://itas.nacc.go.th/go/eit/<?php echo get_config_value('eit_iit'); ?>" target="_blank">
                        <img src="<?php echo base_url('docs/b.icon-nav.png'); ?>">
                        <span class="font-nav">&nbsp;&nbsp;EIT แบบวัดการรับรู้ภายนอก</span>
                    </a>
                </div>
            </ul>
        </div>
    <?php
        break;

    case 'operation': // การดำเนินงาน
    ?>
        <div class="dropdown-content" <?php echo $dropdown_style; ?>>
            <ul class="no-bullets mt-2" style="margin-left: 200px">
                <div class="dropdown-left">
                    <a href="<?php echo site_url('Pages/operation_aca'); ?>">
                        <img src="<?php echo base_url('docs/b.icon-nav.png'); ?>">
                        <span class="font-nav">&nbsp;&nbsp;การปฏิบัติการป้องกันการทุจริต</span>
                    </a>
                    <a href="<?php echo site_url('Pages/operation_mcc'); ?>">
                        <img src="<?php echo base_url('docs/b.icon-nav.png'); ?>">
                        <span class="font-nav">&nbsp;&nbsp;การจัดการเรื่องร้องเรียนการทุจริต</span>
                    </a>
                    <a href="<?php echo site_url('Pages/operation_sap'); ?>">
                        <img src="<?php echo base_url('docs/b.icon-nav.png'); ?>">
                        <span class="font-nav">&nbsp;&nbsp;การปฏิบัติงานและการให้บริการ</span>
                    </a>
                    <a href="<?php echo site_url('Pages/operation_pgn'); ?>">
                        <img src="<?php echo base_url('docs/b.icon-nav.png'); ?>">
                        <span class="font-nav">&nbsp;&nbsp;นโยบายไม่รับของขวัญ no gift policy</span>
                    </a>
                    <a href="<?php echo site_url('Pages/operation_po'); ?>">
                        <img src="<?php echo base_url('docs/b.icon-nav.png'); ?>">
                        <span class="font-nav">&nbsp;&nbsp;การเปิดโอกาสให้มีส่วนร่วม</span>
                    </a>
                    <a href="<?php echo site_url('Pages/operation_pm'); ?>">
                        <img src="<?php echo base_url('docs/b.icon-nav.png'); ?>">
                        <span class="font-nav">&nbsp;&nbsp;การมีส่วนร่วมของผู้บริหาร</span>
                    </a>
                    <a href="<?php echo site_url('Pages/operation_eco_topic'); ?>">
                        <img src="<?php echo base_url('docs/b.icon-nav.png'); ?>">
                        <span class="font-nav">&nbsp;&nbsp;การเสริมสร้างวัฒนธรรมองค์กร</span>
                    </a>
                    <a href="<?php echo site_url('Pages/operation_mr'); ?>">
                        <img src="<?php echo base_url('docs/b.icon-nav.png'); ?>">
                        <span class="font-nav">&nbsp;&nbsp;การบริหารจัดการความเสี่ยง</span>
                    </a>
                    <a href="<?php echo site_url('Pages/operation_aditn'); ?>">
                        <img src="<?php echo base_url('docs/b.icon-nav.png'); ?>">
                        <span class="font-nav">&nbsp;&nbsp;ตรวจสอบภายใน</span>
                    </a>
                </div>
                <div class="dropdown-center">
                    <a href="<?php echo site_url('Pages/ita_all'); ?>">
                        <img src="<?php echo base_url('docs/b.icon-nav.png'); ?>">
                        <span class="font-nav">&nbsp;&nbsp;การประเมินคุณธรรมและความโปร่งใส ITA</span>
                    </a>
                    <a href="<?php echo site_url('Pages/lpa'); ?>">
                        <img src="<?php echo base_url('docs/b.icon-nav.png'); ?>">
                        <span class="font-nav">&nbsp;&nbsp;การประเมินประสิทธิภาพขององค์กร LPA</span>
                    </a>
                    <a href="<?php echo site_url('Pages/operation_aa'); ?>">
                        <img src="<?php echo base_url('docs/b.icon-nav.png'); ?>">
                        <span class="font-nav">&nbsp;&nbsp;กิจการสภา</span>
                    </a>
                    <a href="<?php echo site_url('Pages/operation_meeting_topic'); ?>">
                        <img src="<?php echo base_url('docs/b.icon-nav.png'); ?>">
                        <span class="font-nav">&nbsp;&nbsp;รายงานการประชุมสภา</span>
                    </a>
                    <a>
                        <img src="<?php echo base_url('docs/b.icon-nav.png'); ?>">
                        <span class="font-nav">&nbsp;&nbsp;การบริหารและพัฒนาทรัพยากรบุคคล ></span>
                    </a>
                    <a href="<?php echo site_url('Pages/operation_policy_hr'); ?>">
                        <span class="font-nav mar-left-6">
                            <img src="<?php echo base_url('docs/b.icon-nav.png'); ?>">
                            &nbsp;&nbsp;นโยบายบริหารทรัพยากรบุคคล
                        </span>
                    </a>
                    <a href="<?php echo site_url('Pages/operation_am_hr'); ?>">
                        <span class="font-nav mar-left-6">
                            <img src="<?php echo base_url('docs/b.icon-nav.png'); ?>">
                            &nbsp;&nbsp;การดำเนินการบริหารทรัพยากรบุคคล
                        </span>
                    </a>
                    <a href="<?php echo site_url('Pages/operation_cdm_topic'); ?>">
                        <span class="font-nav mar-left-6">
                            <img src="<?php echo base_url('docs/b.icon-nav.png'); ?>">
                            &nbsp;&nbsp;หลักเกณฑ์การบริหารและพัฒนา
                        </span>
                    </a>
                    <a href="<?php echo site_url('Pages/operation_rdam_hr'); ?>">
                        <span class="font-nav mar-left-6">
                            <img src="<?php echo base_url('docs/b.icon-nav.png'); ?>">
                            &nbsp;&nbsp;รายงานผลการบริหารและพัฒนาทรัพยากรบุคคล
                        </span>
                    </a>
                    <a href="<?php echo site_url('Pages/operation_report'); ?>">
                        <img src="<?php echo base_url('docs/b.icon-nav.png'); ?>">
                        <span class="font-nav">&nbsp;&nbsp;รายงานผลการดำเนินงาน<?php echo get_config_value('abbreviation'); ?></span>
                    </a>
                </div>
                <div class="dropdown-right">
                    <a href="<?php echo site_url('Pages/p_reb'); ?>">
                        <img src="<?php echo base_url('docs/b.icon-nav.png'); ?>">
                        <span class="font-nav">&nbsp;&nbsp;รายงานใช้จ่ายงบประมาณจัดซื้อจัดจ้าง</span>
                    </a>
                    <a href="<?php echo site_url('Pages/p_rpo'); ?>">
                        <img src="<?php echo base_url('docs/b.icon-nav.png'); ?>">
                        <span class="font-nav">&nbsp;&nbsp;รายงานผลการดำเนินงานจัดซื้อจัดจ้าง</span>
                    </a>
                    <a href="<?php echo site_url('Pages/operation_reauf_topic'); ?>">
                        <img src="<?php echo base_url('docs/b.icon-nav.png'); ?>">
                        <span class="font-nav">&nbsp;&nbsp;รายงานติดตามและประเมินผลการดำเนินงาน</span>
                    </a>
                    <a href="<?php echo site_url('Pages/procurement'); ?>">
                        <img src="<?php echo base_url('docs/b.icon-nav.png'); ?>">
                        <span class="font-nav">&nbsp;&nbsp;ประกาศจัดซื้อจัดจ้าง</span>
                    </a>
                    <a href="<?php echo site_url('Pages/announce_oap'); ?>">
                        <img src="<?php echo base_url('docs/b.icon-nav.png'); ?>">
                        <span class="font-nav">&nbsp;&nbsp;ประกาศราคากลาง</span>
                    </a>
                    <a href="<?php echo site_url('Pages/announce_win'); ?>">
                        <img src="<?php echo base_url('docs/b.icon-nav.png'); ?>">
                        <span class="font-nav">&nbsp;&nbsp;ประกาศผู้ชนะราคา</span>
                    </a>
                    <a>
                        <img src="<?php echo base_url('docs/b.icon-nav.png'); ?>">
                        <span class="font-nav">&nbsp;&nbsp;การจัดซื้อจัดจ้างหรือการจัดหาพัสดุ ></span>
                    </a>
                    <a href="<?php echo site_url('Pages/p_rpobuy'); ?>">
                        <span class="font-nav mar-left-6">
                            <img src="<?php echo base_url('docs/b.icon-nav.png'); ?>">
                            &nbsp;&nbsp;รายการจัดซื้อจัดจ้างหรือการจัดหาพัสดุ
                        </span>
                    </a>
                    <a href="<?php echo site_url('Pages/p_sopopaortsr'); ?>">
                        <span class="font-nav mar-left-6">
                            <img src="<?php echo base_url('docs/b.icon-nav.png'); ?>">
                            &nbsp;&nbsp;รายงานสรุปผลการจัดซื้อจัดจ้างหรือการจัดหาพัสดุ<br>
                            &nbsp;&nbsp;&nbsp;&nbsp;ประจำปี
                        </span>
                    </a>
                    <a href="<?php echo site_url('Pages/p_sopopip'); ?>">
                        <span class="font-nav mar-left-6">
                            <img src="<?php echo base_url('docs/b.icon-nav.png'); ?>">
                            &nbsp;&nbsp;รายงานความก้าวหน้าการจัดซื้อจัดจ้างหรือ<br>
                            &nbsp;&nbsp;&nbsp;&nbsp;การจัดหาพัสดุ
                        </span>
                    </a>
                </div>
            </ul>
        </div>
    <?php
        break;

    case 'internal': // มาตรการภายใน
    ?>
        <div class="dropdown-content" <?php echo $dropdown_style; ?>>
            <ul class="no-bullets mt-2" style="margin-left: 200px">
                <div class="dropdown-left">
                    <a href="<?php echo site_url('Pages/order'); ?>">
                        <img src="<?php echo base_url('docs/b.icon-nav.png'); ?>">
                        <span class="font-nav">&nbsp;&nbsp;คำสั่ง</span>
                    </a>
                    <a href="<?php echo site_url('Pages/announce'); ?>">
                        <img src="<?php echo base_url('docs/b.icon-nav.png'); ?>">
                        <span class="font-nav">&nbsp;&nbsp;ประกาศ</span>
                    </a>
                    <a href="<?php echo site_url('Pages/mui'); ?>">
                        <img src="<?php echo base_url('docs/b.icon-nav.png'); ?>">
                        <span class="font-nav">&nbsp;&nbsp;มาตรการภายใน</span>
                    </a>
                    <a href="<?php echo site_url('Pages/guide_work'); ?>">
                        <img src="<?php echo base_url('docs/b.icon-nav.png'); ?>">
                        <span class="font-nav">&nbsp;&nbsp;คู่มือการปฏิบัติงาน</span>
                    </a>
                    <a href="<?php echo site_url('Pages/laws_topic'); ?>">
                        <img src="<?php echo base_url('docs/b.icon-nav.png'); ?>">
                        <span class="font-nav">&nbsp;&nbsp;กฎหมายที่เกี่ยวข้อง</span>
                    </a>
                    <a href="<?php echo site_url('Pages/loadform'); ?>">
                        <img src="<?php echo base_url('docs/b.icon-nav.png'); ?>">
                        <span class="font-nav">&nbsp;&nbsp;ดาวน์โหลดแบบฟอร์ม</span>
                    </a>
                    <a href="<?php echo site_url('Pages/pppw'); ?>">
                        <img src="<?php echo base_url('docs/b.icon-nav.png'); ?>">
                        <span class="font-nav">&nbsp;&nbsp;พรบ./พรก ที่ใช้การปฏิบัติงาน</span>
                    </a>
					<a href="<?php echo site_url('Pages/ethics_strategy'); ?>">
                        <img src="<?php echo base_url('docs/b.icon-nav.png'); ?>">
                        <span class="font-nav">&nbsp;&nbsp;ประมวลผลจริยธรรมและการขับเคลื่อนจริยธรรม</span>
                    </a>
                </div>
                <div class="dropdown-center">
                    <a href="<?php echo site_url('Pages/canon_bgps'); ?>">
                        <img src="<?php echo base_url('docs/b.icon-nav.png'); ?>">
                        <span class="font-nav">&nbsp;&nbsp;
                            <?php
                            $abbreviation = get_config_value('abbreviation');
                            $canon = ($abbreviation == 'อบต.') ? 'ข้อบัญญัติ' : 'เทศบัญญัติ';
                            echo $canon;
                            ?>งบประมาณ
                        </span>
                    </a>
                    <a href="<?php echo site_url('Pages/canon_chh'); ?>">
                        <img src="<?php echo base_url('docs/b.icon-nav.png'); ?>">
                        <span class="font-nav">&nbsp;&nbsp;
                            <?php
                            $abbreviation = get_config_value('abbreviation');
                            $canon = ($abbreviation == 'อบต.') ? 'ข้อบัญญัติ' : 'เทศบัญญัติ';
                            echo $canon;
                            ?>การควบคุมกิจการที่เป็นอันตรายต่อสุขภาพ
                        </span>
                    </a>
                    <a href="<?php echo site_url('Pages/canon_ritw'); ?>">
                        <img src="<?php echo base_url('docs/b.icon-nav.png'); ?>">
                        <span class="font-nav">&nbsp;&nbsp;
                            <?php
                            $abbreviation = get_config_value('abbreviation');
                            $canon = ($abbreviation == 'อบต.') ? 'ข้อบัญญัติ' : 'เทศบัญญัติ';
                            echo $canon;
                            ?>การติดตั้งระบบบำบัดน้ำเสียในอาคาร
                        </span>
                    </a>
                    <a href="<?php echo site_url('Pages/canon_market'); ?>">
                        <img src="<?php echo base_url('docs/b.icon-nav.png'); ?>">
                        <span class="font-nav">&nbsp;&nbsp;
                            <?php
                            $abbreviation = get_config_value('abbreviation');
                            $canon = ($abbreviation == 'อบต.') ? 'ข้อบัญญัติ' : 'เทศบัญญัติ';
                            echo $canon;
                            ?>ตลาด
                        </span>
                    </a>
                    <a href="<?php echo site_url('Pages/canon_rmwp'); ?>">
                        <img src="<?php echo base_url('docs/b.icon-nav.png'); ?>">
                        <span class="font-nav">&nbsp;&nbsp;
                            <?php
                            $abbreviation = get_config_value('abbreviation');
                            $canon = ($abbreviation == 'อบต.') ? 'ข้อบัญญัติ' : 'เทศบัญญัติ';
                            echo $canon;
                            ?>การจัดการสิ่งปฏิกูลและมูลฝอย
                        </span>
                    </a>
                    <a href="<?php echo site_url('Pages/canon_rcsp'); ?>">
                        <img src="<?php echo base_url('docs/b.icon-nav.png'); ?>">
                        <span class="font-nav">&nbsp;&nbsp;
                            <?php
                            $abbreviation = get_config_value('abbreviation');
                            $canon = ($abbreviation == 'อบต.') ? 'ข้อบัญญัติ' : 'เทศบัญญัติ';
                            echo $canon;
                            ?>หลักเกณฑ์การคัดมูลฝอย
                        </span>
                    </a>
                    <a href="<?php echo site_url('Pages/canon_rcp'); ?>">
                        <img src="<?php echo base_url('docs/b.icon-nav.png'); ?>">
                        <span class="font-nav">&nbsp;&nbsp;
                            <?php
                            $abbreviation = get_config_value('abbreviation');
                            $canon = ($abbreviation == 'อบต.') ? 'ข้อบัญญัติ' : 'เทศบัญญัติ';
                            echo $canon;
                            ?>การควบคุมการเลี้ยงหรือปล่อยสุนัขและแมว
                        </span>
                    </a>
                </div>
                <div class="dropdown-right">
                    <a href="<?php echo site_url('Pages/arevenuec'); ?>">
                        <img src="<?php echo base_url('docs/b.icon-nav.png'); ?>">
                        <span class="font-nav">&nbsp;&nbsp;การเร่งรัดจัดเก็บรายได้</span>
                    </a>
                    <a href="<?php echo site_url('Pages/finance_topic'); ?>">
                        <img src="<?php echo base_url('docs/b.icon-nav.png'); ?>">
                        <span class="font-nav">&nbsp;&nbsp;งานการเงินและการบัญชี</span>
                    </a>
                    <a href="<?php echo site_url('Pages/taepts_topic'); ?>">
                        <img src="<?php echo base_url('docs/b.icon-nav.png'); ?>">
                        <span class="font-nav">&nbsp;&nbsp;มาตรฐานการส่งเสริมคุณธรรมและความโปร่งใส</span>
                    </a>
                    <a href="<?php echo site_url('Pages/menu_eservice'); ?>">
                        <img src="<?php echo base_url('docs/b.icon-nav.png'); ?>">
                        <span class="font-nav">&nbsp;&nbsp;e-Service</span>
                    </a>
					<a href="<?php echo site_url('data_catalog'); ?>">
                        <img src="<?php echo base_url('docs/b.icon-nav.png'); ?>">
                        <span class="font-nav">&nbsp;&nbsp;ข้อมูลองค์กร (Data Catalog)</span>
                    </a>
					
                    <a href="<?php echo site_url('Pages/km'); ?>">
                        <img src="<?php echo base_url('docs/b.icon-nav.png'); ?>">
                        <span class="font-nav">&nbsp;&nbsp;knowledge Management: KM<br>การจัดการความรู้ของท้องถิ่น</span>
                    </a>
                    
                </div>
            </ul>
        </div>
<?php
        break;

    default:
        // กรณีไม่มี menu_type ที่ตรงกัน
        echo '<div class="dropdown-content">ไม่พบเมนู</div>';
        break;
}
?>