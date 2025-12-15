<div class="text-center pages-head">
    <span class="font-pages-head">ผังเว็บไซต์</span>
</div>
<div class="text-center" style="padding-top: 50px">
    <img src="<?php echo base_url('docs/logo.png'); ?>" width="174px" height="174px">
</div>
</div>
<img src="<?php echo base_url('docs/welcome-btm-light-other.png'); ?>">

<div class="bg-pages">
    <div class="container-pages-detail" style="position: relative; z-index: 10; margin-top: 150px;">
        <div class="row">
            <div class="col-6">
                <div class="content-all-web">
                    <span class="font-head-all-web">ข้อมูลพื้นฐาน</span><br>
                    <div class="underline pad-left-35 mt-1 mb-1">
                        <a href="<?php echo site_url('Pages/history'); ?>" class="font-content-all-web">ประวัติความเป็นมา</a><br>
                        <div class="mt-1"></div>
                        <a href="<?php echo site_url('Pages/ci'); ?>" class="font-content-all-web">ข้อมูลชุมชน</a><br>
                        <div class="mt-1"></div>
                        <a href="<?php echo site_url('Pages/gci'); ?>" class="font-content-all-web">ข้อมูลสภาพทั่วไป</a><br>
                        <div class="mt-1"></div>
                        <a href="<?php echo site_url('Pages/mission'); ?>" class="font-content-all-web">ภารกิจและความรับผิดชอบ</a><br>
                        <div class="mt-1"></div>
                        <a href="<?php echo site_url('Pages/si'); ?>" class="font-content-all-web">ยุทธศาสตร์การพัฒนาด้านโครงสร้างพื้นฐาน</a><br>
                        <div class="mt-1"></div>
                        <a href="<?php echo site_url('Pages/authority'); ?>" class="font-content-all-web">อำนาจหน้าที่</a><br>
                        <div class="mt-1"></div>
                        <a href="<?php echo site_url('Pages/vision'); ?>" class="font-content-all-web">วิสัยทัศน์และพันธกิจ</a><br>
                        <div class="mt-1"></div>
                        <a href="<?php echo site_url('Pages/video'); ?>" class="font-content-all-web">วิดีทัศน์</a><br>
                        <div class="mt-1"></div>
                        <a href="<?php echo site_url('Pages/executivepolicy'); ?>" class="font-content-all-web">นโยบายของผู้บริหาร</a><br>
                        <div class="mt-1"></div>
                        <a href="<?php echo site_url('Pages/newsletter'); ?>" class="font-content-all-web">จดหมายข่าว</a><br>
                        <div class="mt-1"></div>
                        <a href="<?php echo site_url('Pages/activity'); ?>" class="font-content-all-web">ข่าวสาร / กิจกรรม</a><br>
                        <div class="mt-1"></div>
                        <a href="<?php echo site_url('Pages/travel'); ?>" class="font-content-all-web">สถานที่ท่องเที่ยว</a><br>
                        <div class="mt-1"></div>
                        <a href="<?php echo site_url('Pages/otop'); ?>" class="font-content-all-web">ผลิตภัณฑ์ชุมชน</a><br>
                        <div class="mt-1"></div>
                        <a href="<?php echo site_url('Pages/contact'); ?>" class="font-content-all-web">ติดต่อเรา</a><br>
                    </div>
                </div>
                <div class="content-all-web mt-4">
                    <span class="font-head-all-web">โครงสร้างบุคลากร</span><br>
                    <div class="underline pad-left-35 mt-1 mb-1">
                        <a href="<?php echo site_url('Pages/site_map'); ?>" class="font-content-all-web">แผนผังโครงสร้างรวม</a><br>

                        <?php
                        $position_types = get_position_types();
                        ?>
                        <?php foreach ($position_types as $type): ?>
                            <?php if ($type->pstatus === 'show'): ?>
                                <?php
                                // ตรวจสอบว่าเป็น sub item หรือไม่
                                $is_sub_item = isset($type->psub) && $type->psub == 1;
                                $item_class = $is_sub_item ? 'sub-item-content' : '';
                                $item_style = $is_sub_item ? 'margin-left: 20px;' : '';
                                ?>
                                <div class="mt-1"></div>
                                <a href="<?php echo site_url('Pages/personnel/' . $type->peng); ?>"
                                    class="font-content-all-web <?= $item_class ?>">
                                    <?= $type->pname ?>
                                </a><br>
                            <?php endif; ?>
                        <?php endforeach; ?>
                    </div>
                </div>
                <div class="content-all-web mt-4">
                    <span class="font-head-all-web">บริการประชาชน</span><br>
                    <div class="underline pad-left-35 mt-2 mb-2">
                        <a href="<?php echo site_url('Pages/pbsv_cjc'); ?>" class="font-content-all-web">ศูนย์ยุติธรรมชุมชน</a><br>
                        <div class="mt-1"></div>
                        <a href="<?php echo site_url('Pages/pbsv_cac'); ?>" class="font-content-all-web">ศูนย์ช่วยเหลือประชาชน</a><br>
                        <div class="mt-1"></div>
                        <a href="<?php echo site_url('Pages/pbsv_cig'); ?>" class="font-content-all-web">ศูนย์ข้อมูลข่าวสารทางราชการ</a><br>
                        <div class="mt-1"></div>
                        <a href="<?php echo get_config_value('elec'); ?>" target="_blank" class="font-content-all-web">ศูนย์ข้อมูลข่าวสารอิเล็กทรอนิกส์</a><br>
                        <div class="mt-1"></div>
                        <a href="<?php echo site_url('Pages/pbsv_ahs'); ?>" class="font-content-all-web">หลักประกันสุขภาพตำบล</a><br>
                        <div class="mt-1"></div>
                        <a href="https://www.nacc.go.th/NACCPPWFC?" target="_blank" class="font-content-all-web">ยกระดับเจตจำนงทางการเมืองในการต่อต้านการทุจริต</a><br>
                        <div class="mt-1"></div>
                        <a href="<?php echo site_url('Pages/pbsv_gup'); ?>" class="font-content-all-web">คู่มือสำหรับประชาชน</a><br>
                        <div class="mt-1"></div>
                        <a href="<?php echo site_url('Pages/manual_esv_detail/1'); ?>" class="font-content-all-web">คู่มือใช้งาน e-Service</a><br>
                        <div class="mt-1"></div>
                        <a href="https://dbdregcom.dbd.go.th/mainsite/index.php?id=28" target="_blank" class="font-content-all-web">คู่มือจดทะเบียนพาณิชย์</a><br>
                        <div class="mt-1"></div>
                        <a href="<?php echo site_url('Pages/pbsv_sags'); ?>" class="font-content-all-web">คู่มือและมาตรฐานการให้บริการ</a><br>
                        <div class="mt-1"></div>
                        <a href="<?php echo site_url('Pages/pbsv_ems'); ?>" class="font-content-all-web">งานกู้ชีพ / การบริการการแพทย์ฉุกเฉิน (EMS)</a><br>
                        <div class="mt-1"></div>
                        <a href="<?php echo site_url('Pages/pbsv_oppr'); ?>" class="font-content-all-web">งานอาสาสมัครป้องกันภัยฝ่ายพลเรือน (อปพร.)</a><br>
                        <div class="mt-1"></div>
                        <a href="<?php echo site_url('Pages/pbsv_e_book'); ?>" class="font-content-all-web">ดาวน์โหลดแบบฟอร์ม e-Book</a><br>
                        <div class="mt-1"></div>
                        <a href="<?php echo site_url('Pages/odata'); ?>" class="font-content-all-web">ฐานข้อมูลเปิดภาครัฐ (Open Data)</a><br>
                        <div class="mt-1"></div>
                        <a href="<?php echo site_url('Pages/elderly_aw_ods'); ?>" class="font-content-all-web">เบี้ยยังชีพผู้สูงอายุ / ผู้พิการ</a><br>
                        <div class="mt-1"></div>
                        <a href="<?php echo site_url('Pages/adding_queue'); ?>" class="font-content-all-web">จองคิวติดต่อราชการออนไลน์</a><br>
                        <div class="mt-1"></div>
                        <a href="<?php echo site_url('Pages/pbsv_statistics'); ?>" class="font-content-all-web">ข้อมูลสถิติการให้บริการ</a><br>
						<div class="mt-1"></div>
						<a href="<?php echo site_url('Pages/e_mags_view'); ?>" class="font-content-all-web">วารสารออนไลน์</a><br>
                    </div>
                </div>
                <div class="content-all-web mt-4">
                    <span class="font-head-all-web">แผนงาน</span><br>
                    <div class="underline pad-left-35">
                        <a href="<?php echo site_url('Pages/plan_pdl'); ?>" class="font-content-all-web">แผนพัฒนาท้องถิ่น</a><br>
                        <div class="mt-1"></div>
                        <a href="<?php echo site_url('Pages/plan_psi'); ?>" class="font-content-all-web">แผนแม่บทสารสนเทศ</a><br>
                        <div class="mt-1"></div>
                        <a href="<?php echo site_url('Pages/plan_pop'); ?>" class="font-content-all-web">แผนปฏิบัติการจัดซื้อจัดจ้าง</a><br>
                        <div class="mt-1"></div>
                        <a href="<?php echo site_url('Pages/plan_paca'); ?>" class="font-content-all-web">แผนปฏิบัติการป้องกันการทุจริต</a><br>
                        <div class="mt-1"></div>
                        <a href="<?php echo site_url('Pages/plan_pmda'); ?>" class="font-content-all-web">แผนป้องกันและบรรเทาสาธารณภัยประจำปี</a><br>
                        <div class="mt-1"></div>
                        <a href="<?php echo site_url('Pages/plan_dpy'); ?>" class="font-content-all-web">แผนการบริหารและพัฒนาทรัพยากรบุคคลประจำปี</a><br>
                        <div class="mt-1"></div>
                        <a href="<?php echo site_url('Pages/plan_pc3y'); ?>" class="font-content-all-web">แผนอัตรากำลัง 3 ปี</a><br>
                        <div class="mt-1"></div>
                        <a href="<?php echo site_url('Pages/plan_pds3y'); ?>" class="font-content-all-web">แผนพัฒนาบุคลากร 3 ปี</a><br>
                        <div class="mt-1"></div>
                        <a href="<?php echo site_url('Pages/plan_poa'); ?>" class="font-content-all-web">แผนการดำเนินงานประจำปี</a><br>
                        <div class="mt-1"></div>
                        <a href="<?php echo site_url('Pages/plan_pdpa'); ?>" class="font-content-all-web">แผนพัฒนาบุคลากรประจำปี</a><br>
                        <div class="mt-1"></div>
                        <a href="<?php echo site_url('Pages/plan_pcra'); ?>" class="font-content-all-web">แผนการจัดเก็บรายได้ประจำปี</a><br>
                        <div class="mt-1"></div>
                        <a href="<?php echo site_url('Pages/plan_progress'); ?>" class="font-content-all-web">แผนและความก้าวหน้าในการดำเนินงานและการใช้งบประมาณ</a><br>
                    </div>
                </div>
                <div class="content-all-web mt-4">
                    <span class="font-head-all-web"><?php
                                                    $abbreviation = get_config_value('abbreviation');
                                                    $canon = ($abbreviation == 'อบต.') ? 'ข้อบัญญัติ' : 'เทศบัญญัติ';
                                                    echo $canon;
                                                    ?></span><br>
                    <div class="underline mt-1 mb-1 pad-left-35">
                        <a href="<?php echo site_url('Pages/canon_bgps'); ?>" class="font-content-all-web"><?php
                                                                                                            $abbreviation = get_config_value('abbreviation');
                                                                                                            $canon = ($abbreviation == 'อบต.') ? 'ข้อบัญญัติ' : 'เทศบัญญัติ';
                                                                                                            echo $canon;
                                                                                                            ?>งบประมาณ</a><br>
                        <div class="mt-1"></div>
                        <a href="<?php echo site_url('Pages/canon_chh'); ?>" class="font-content-all-web"><?php
                                                                                                            $abbreviation = get_config_value('abbreviation');
                                                                                                            $canon = ($abbreviation == 'อบต.') ? 'ข้อบัญญัติ' : 'เทศบัญญัติ';
                                                                                                            echo $canon;
                                                                                                            ?>การควบคุมกิจการที่เป็นอันตรายต่อสุขภาพ</a><br>
                        <div class="mt-1"></div>
                        <a href="<?php echo site_url('Pages/canon_ritw'); ?>" class="font-content-all-web"><?php
                                                                                                            $abbreviation = get_config_value('abbreviation');
                                                                                                            $canon = ($abbreviation == 'อบต.') ? 'ข้อบัญญัติ' : 'เทศบัญญัติ';
                                                                                                            echo $canon;
                                                                                                            ?>การติดตั้งระบบบำบัดน้ำเสียในอาคาร</a><br>
                        <div class="mt-1"></div>
                        <a href="<?php echo site_url('Pages/canon_market'); ?>" class="font-content-all-web"><?php
                                                                                                                $abbreviation = get_config_value('abbreviation');
                                                                                                                $canon = ($abbreviation == 'อบต.') ? 'ข้อบัญญัติ' : 'เทศบัญญัติ';
                                                                                                                echo $canon;
                                                                                                                ?>ตลาด</a><br>
                        <div class="mt-1"></div>
                        <a href="<?php echo site_url('Pages/canon_rmwp'); ?>" class="font-content-all-web"><?php
                                                                                                            $abbreviation = get_config_value('abbreviation');
                                                                                                            $canon = ($abbreviation == 'อบต.') ? 'ข้อบัญญัติ' : 'เทศบัญญัติ';
                                                                                                            echo $canon;
                                                                                                            ?>การจัดการสิ่งปฏิกูลและมูลฝอย</a><br>
                        <div class="mt-1"></div>
                        <a href="<?php echo site_url('Pages/canon_rcsp'); ?>" class="font-content-all-web"><?php
                                                                                                            $abbreviation = get_config_value('abbreviation');
                                                                                                            $canon = ($abbreviation == 'อบต.') ? 'ข้อบัญญัติ' : 'เทศบัญญัติ';
                                                                                                            echo $canon;
                                                                                                            ?>หลักเกณฑ์การคัดแยกมูลฝอย</a><br>
                        <div class="mt-1"></div>
                        <a href="<?php echo site_url('Pages/canon_rcp'); ?>" class="font-content-all-web"><?php
                                                                                                            $abbreviation = get_config_value('abbreviation');
                                                                                                            $canon = ($abbreviation == 'อบต.') ? 'ข้อบัญญัติ' : 'เทศบัญญัติ';
                                                                                                            echo $canon;
                                                                                                            ?>การควบคุมการเลี้ยงหรือปล่อยสุนัขและแมว</a><br>

                    </div>
                </div>
            </div>

            <div class="col-6">
                <div class="content-all-web">
                    <span class="font-head-all-web">การดำเนินงาน</span><br>
                    <div class="underline pad-left-35">
                        <!-- <span class="font-content-all-web">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;รายงานผลการดำเนินงาน</span><br><br> -->
                        <a href="<?php echo site_url('Pages/operation_aca'); ?>" class="font-content-all-web">การปฏิบัติการป้องกันการทุจริต</a><br>
                        <div class="mt-1"></div>
                        <a href="<?php echo site_url('Pages/operation_mcc'); ?>" class="font-content-all-web">การจัดการเรื่องร้องเรียนการทุจริตและประพฤติมิชอบ</a><br>
                        <div class="mt-1"></div>
                        <a href="<?php echo site_url('Pages/operation_sap'); ?>" class="font-content-all-web">การปฏิบัติงานและการให้บริการ</a><br>
                        <div class="mt-1"></div>
                        <a href="<?php echo site_url('Pages/operation_pgn'); ?>" class="font-content-all-web">นโยบายไม่รับของขวัญ no gift policy</a><br>
                        <div class="mt-1"></div>
                        <a href="<?php echo site_url('Pages/operation_po'); ?>" class="font-content-all-web">การเปิดโอกาสให้มีส่วนร่วม</a><br>
                        <div class="mt-1"></div>
                        <a href="<?php echo site_url('Pages/operation_pm'); ?>" class="font-content-all-web">การมีส่วนร่วมของผู้บริหาร</a><br>
                        <div class="mt-1"></div>
                        <a href="<?php echo site_url('Pages/operation_eco_topic'); ?>" class="font-content-all-web">การเสริมสร้างวัฒนธรรมองค์กร</a><br>
                        <div class="mt-1"></div>
                        <a href="<?php echo site_url('Pages/ita'); ?>" class="font-content-all-web">การประเมินคุณธรรมของหน่วยงานภาครัฐ ITA</a><br>
                        <div class="mt-1"></div>
                        <a href="<?php echo site_url('Pages/lpa'); ?>" class="font-content-all-web">การประเมินประสิทธิภาพขององค์กร LPA</a><br>
                        <div class="mt-1"></div>
                        <a href="#" class="font-content-all-web">การบริหารและพัฒนาทรัพยากรบุคคล ></a><br>
                        <div class="mt-1"></div>
                        &nbsp;&nbsp;<a href="<?php echo site_url('Pages/operation_policy_hr'); ?>" class="font-content-all-web dot-laws">นโยบายบริหารทรัพยากรบุคคล</a><br>
                        <div class="mt-1"></div>
                        &nbsp;&nbsp;<a href="<?php echo site_url('Pages/operation_am_hr'); ?>" class="font-content-all-web dot-laws">การดำเนินการบริหารทรัพยากรบุคคล</a><br>
                        <div class="mt-1"></div>
                        &nbsp;&nbsp;<a href="<?php echo site_url('Pages/operation_cdm_topic'); ?>" class="font-content-all-web dot-laws">หลักเกณฑ์การบริหารและพัฒนา</a><br>
                        <div class="mt-1"></div>
                        &nbsp;&nbsp;<a href="<?php echo site_url('Pages/operation_rdam_hr'); ?>" class="font-content-all-web dot-laws">รายงานผลการบริหารและพัฒนาทรัพยากรบุคคลประจำปี</a><br>
                        <div class="mt-1"></div>
                        <a href="<?php echo site_url('Pages/operation_aa'); ?>" class="font-content-all-web">กิจการสภา</a><br>
                        <div class="mt-1"></div>
                        <a href="<?php echo site_url('Pages/operation_meeting_topic'); ?>" class="font-content-all-web">รายงานการประชุมสภา</a><br>
                        <div class="mt-1"></div>
                        <a href="<?php echo site_url('Pages/operation_aditn'); ?>" class="font-content-all-web">ตรวจสอบภายใน</a><br>
                        <div class="mt-1"></div>
                        <a href="<?php echo site_url('Pages/operation_reauf_topic'); ?>" class="font-content-all-web">รายงานติดตามและประเมินผลแผน</a><br>
                        <div class="mt-1"></div>
                        <a href="<?php echo site_url('Pages/procurement'); ?>" class="font-content-all-web">ประกาศจัดซื้อจัดจ้าง</a><br>
                        <div class="mt-1"></div>
                        <a href="<?php echo site_url('Pages/announce_oap'); ?>" class="font-content-all-web">ประกาศราคากลาง</a><br>
                        <div class="mt-1"></div>
                        <a href="<?php echo site_url('Pages/announce_win'); ?>" class="font-content-all-web">ประกาศผู้ชนะราคา</a><br>
                        <div class="mt-1"></div>
                        <a href="#" class="font-content-all-web">การจัดซื้อจัดจ้างหรือการจัดหาพัสดุ ></a><br>
                        <div class="mt-1"></div>
                        &nbsp;&nbsp;<a href="<?php echo site_url('Pages/p_rpobuy'); ?>" class="font-content-all-web dot-laws">รายการผลจัดซื้อจัดจ้าง / จัดหาพัสดุประจำปี</a><br>
                        <div class="mt-1"></div>
                        &nbsp;&nbsp;<a href="<?php echo site_url('Pages/p_sopopaortsr'); ?>" class="font-content-all-web dot-laws">รายงานสรุปผลการจัดซื้อจัดจ้าง<br>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;หรือการจัดหาพัสดุประจำปี</a><br>
                        <div class="mt-1"></div>
                        &nbsp;&nbsp;<a href="<?php echo site_url('Pages/p_sopopip'); ?>" class="font-content-all-web dot-laws">รายงานความก้าวหน้าการจัดซื้อจัดจ้าง<br>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;หรือการจัดหาพัสดุ</a><br>
                        <div class="mt-1"></div>
                        <a href="<?php echo site_url('Pages/news'); ?>" class="font-content-all-web">ข่าวประชาสัมพันธ์</a><br>
                        <div class="mt-1"></div>
                        <a href="<?php echo site_url('Pages/procurement_tbl_w0_search'); ?>" class="font-content-all-web">ข่าวจัดซื้อจัดจ้าง</a><br>
                        <div class="mt-1"></div>
                        <a href="<?php echo site_url('Pages/egp'); ?>" class="font-content-all-web">ข่าวจัดซื้อจัดจ้าง e-GP</a><br>
                        <div class="mt-1"></div>
                        <a href="<?php echo site_url('Pages/p_rpo'); ?>" class="font-content-all-web">รายงานผลการดำเนินงานจัดซื้อจัดจ้าง</a><br>
                        <div class="mt-1"></div>
                        <a href="<?php echo site_url('Pages/p_reb'); ?>" class="font-content-all-web">รายงานการใช้จ่ายงบประมาณจัดซื้อจัดจ้าง</a><br>
                        <div class="mt-1"></div>
                        <a href="<?php echo site_url('Pages/operation_report'); ?>" class="font-content-all-web">รายงานผลการดำเนินงาน อบต.</a><br>
                        <div class="mt-1"></div>
                    </div>
                </div>

                <div class="content-all-web mt-4">
                    <span class="font-head-all-web">e-Service</span><br>
                    <div class="underline pad-left-35">
                        <a href="<?php echo site_url('Pages/e_service'); ?>" class="font-content-all-web">บริการยื่นเอกสารออนไลน์</a><br>
                        <div class="mt-1"></div>
                        <a href="<?php echo site_url('Pages/adding_complain'); ?>" class="font-content-all-web">ร้องเรียนร้องทุกข์</a><br>
                        <div class="mt-1"></div>
                        <a href="<?php echo site_url('Pages/follow_complain'); ?>" class="font-content-all-web">ติดตามเรื่องร้องเรียน</a><br>
                        <div class="mt-1"></div>
                        <a href="<?php echo site_url('Pages/adding_suggestions'); ?>" class="font-content-all-web">ช่องทางรับฟังความคิดเห็น</a><br>
                        <div class="mt-1"></div>
                        <a href="<?php echo site_url('Pages/adding_corruption'); ?>" class="font-content-all-web">แจ้งเรื่องทุจริตหน่วยงานภาครัฐ</a><br>
                        <div class="mt-1"></div>
                        <a href="<?php echo site_url('Pages/q_a'); ?>" class="font-content-all-web">กระทู้ ถาม-ตอบ (Q&A)</a><br>
                        <div class="mt-1"></div>
                        <a href="<?php echo site_url('Pages/questions'); ?>" class="font-content-all-web">คำถามที่พบบ่อย (FAQ)</a><br>
                    </div>
                </div>
                <div class="content-all-web mt-4">
                    <span class="font-head-all-web">มาตรการภายในหน่วยงาน</span><br>
                    <div class="underline pad-left-35">
                        <a href="<?php echo site_url('Pages/order'); ?>" class="font-content-all-web">คำสั่ง</a><br>
                        <div class="mt-1"></div>
                        <a href="<?php echo site_url('Pages/announce'); ?>" class="font-content-all-web">ประกาศ</a><br>
                        <div class="mt-1"></div>
                        <a href="<?php echo site_url('Pages/mui'); ?>" class="font-content-all-web">มาตรการภายใน</a><br>
                        <div class="mt-1"></div>
                        <a href="<?php echo site_url('Pages/guide_work'); ?>" class="font-content-all-web">คู่มือปฏิบัติงาน</a><br>
                        <div class="mt-1"></div>
                        <a href="<?php echo site_url('Pages/arevenuec'); ?>" class="font-content-all-web">การเร่งรัดจัดเก็บรายได้</a><br>
                        <div class="mt-1"></div>
                        <a href="<?php echo site_url('Pages/loadform'); ?>" class="font-content-all-web">ดาวน์โหลดแบบฟอร์ม</a><br>
                        <div class="mt-1"></div>
                        <a href="<?php echo site_url('Pages/laws_topic'); ?>" class="font-content-all-web">กฎหมายที่เกี่ยวข้อง</a><br>
                        <div class="mt-1"></div>
                        <a href="<?php echo site_url('Pages/km'); ?>" class="font-content-all-web">knowledge Management: KM<br>การจัดการความรู้ของท้องถิ่น</a><br>
                        <div class="mt-1"></div>
                        <a href="<?php echo site_url('Pages/finance_topic'); ?>" class="font-content-all-web">งานการเงินและการบัญชี</a><br>
                        <div class="mt-1"></div>
                        <a href="<?php echo site_url('Pages/taepts_topic'); ?>" class="font-content-all-web">มาตรฐานการส่งเสริมคุณธรรมและความโปร่งใส</a><br>
                        <div class="mt-1"></div>
                        <a href="<?php echo site_url('Pages/ethics_strategy'); ?>" class="font-content-all-web">ประมวลผลจริยธรรมและการขับเคลื่อนจริยธรรม</a><br>
						<div class="mt-1"></div>
                        <a href="<?php echo site_url('data_catalog'); ?>" class="font-content-all-web">ข้อมูลองค์กร (Data Catalog)</a><br>
                    </div>
                </div>
                <div class="content-all-web mt-4">
                    <span class="font-head-all-web">อื่นๆ</span><br>
                    <div class="underline pad-left-35">
                        <a href="https://itas.nacc.go.th/go/iit/<?php echo get_config_value('eit_iit'); ?>" target="_blank" class="font-content-all-web">แบบวัดการรับรู้ภายใน IIT</a><br>
                        <div class="mt-1"></div>
                        <a href="https://itas.nacc.go.th/go/eit/<?php echo get_config_value('eit_iit'); ?>" target="_blank" class="font-content-all-web">แบบวัดการรับรู้ภายนอก EIT</a><br>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div><br><br><br>