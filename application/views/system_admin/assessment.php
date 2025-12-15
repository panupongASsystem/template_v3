<div class="d-flex justify-content-end mb-2">
    <!-- <a class="btn btn-light me-2" href="<?= site_url('assessment_backend'); ?>" role="button">
        <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-arrow-clockwise" viewBox="0 0 16 16">
            <path fill-rule="evenodd" d="M8 3a5 5 0 1 0 4.546 2.914.5.5 0 0 1 .908-.417A6 6 0 1 1 8 2v1z" />
            <path d="M8 4.466V.534a.25.25 0 0 1 .41-.192l2.36 1.966c.12.1.12.284 0 .384L8.41 4.658A.25.25 0 0 1 8 4.466z" />
        </svg> Refresh Data
    </a> -->
    <a class="btn btn-success" href="<?= site_url('assessment_backend/export_csv'); ?>" role="button">
        <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-file-earmark-spreadsheet" viewBox="0 0 16 16">
            <path d="M14 14V4.5L9.5 0H4a2 2 0 0 0-2 2v12a2 2 0 0 0 2 2h8a2 2 0 0 0 2-2M9.5 3A1.5 1.5 0 0 0 11 4.5h2V9H3V2a1 1 0 0 1 1-1h5.5zM3 12v-2h2v2zm0 1h2v2H4a1 1 0 0 1-1-1zm3 2v-2h3v2zm4 0v-2h3v1a1 1 0 0 1-1 1zm3-3h-3v-2h3zm-7 0v-2h3v2z" />
        </svg> Export Excel
    </a>
</div>

   <!-- <h5 class="border border-#f5f5f5 p-2 mb-2 font-black" style="background-color: #f5f5f5;">จัดการข้อมูลข่าวสารประจำเดือน</h5> -->
   <!-- DataTales Example -->
   <div class="card shadow mb-4">
       <div class="card-header py-3">
           <h6 class="m-0 font-weight-bold text-black">จัดการข้อมูลแบบประเมินความพึงพอใจการให้บริการ</h6>
       </div>
       <div class="card-body">
           <div class="table-responsive">

               <?php
                $Index = 1;
                ?>
               <table id="newdataTables" class="table">
                   <thead>
                       <tr>
                           <th style="width: 5%;">ลำดับ</th>
                           <th style="width: 22%;">ข้อมูลทั่วไปของผู้ตอบ</th>
                           <th style="width: 22%;">ด้านการให้บริการ</th>
                           <th style="width: 22%;">ด้านบุคลากรผู้ให้บริการ</th>
                           <th style="width: 22%;">ด้านสถานที่และสิ่งอำนวยความสะดวก</th>
                           <th class="text-center" style="width: 7%;">วันที่</th>
                       </tr>
                   </thead>
                   <tbody>
                       <?php
                        foreach ($query as $rs) { ?>
                           <tr role="row">
                               <td align="center"><?= $Index; ?></td>
                               <td>
                                   เพศ : <?= $rs->assessment_gender; ?><br>
                                   อายุ : <?= $rs->assessment_age; ?><br>
                                   ระดับการศึกษา : <?= $rs->assessment_study; ?><br>
                                   อาชีพ : <?= $rs->assessment_occupation; ?>
                                   <?php if ($rs->assessment_occupation == 'อื่นๆ') { ?>
                                       ( <?= $rs->assessment_occupation_etc; ?> )
                                   <?php  } ?><br>
                                   ข้อเสนอแนะ : <?= $rs->assessment_suggestion; ?><br>
                               </td>
                               <td>
                                   1.1 การให้บริการเป็นไปตามระยะเวลาที่กำหนด : <?= $rs->assessment_11; ?><br>
                                   1.2 ความรวดเร็วในการให้บริการ : <?= $rs->assessment_12; ?><br>
                                   1.3 ได้รับบริการตรงตามความต้องการ : <?= $rs->assessment_13; ?><br>
                                   1.4 ความพึงพอใจโดยภาพรวมของท่านที่ได้รับจากการบริการของหน่วยงาน : <?= $rs->assessment_14; ?><br>
                               </td>
                               <td>
                                   2.1 ความเหมาะสมในการแต่งกายของผู้ให้บริการ : <?= $rs->assessment_21; ?><br>
                                   2.2 ความเต็มใจและความพร้อมในการให้บริการอย่างสุภาพ : <?= $rs->assessment_22; ?><br>
                                   2.3 ความรู้ความสามารถในการให้บริการ เช่น สามารถตอบคำถาม ชี้แจงข้อสงสัยให้คำแนะนำได้เป็นต้น : <?= $rs->assessment_23; ?><br>
                                   2.4 การให้บริการเหมือนกันทุกรายโดยไม่เลือกปฏิบัติ : <?= $rs->assessment_24; ?><br>
                                   2.5 ความซื่อสัตย์สุจริตในการปฏิบัติหน้าที่ : <?= $rs->assessment_25; ?><br>
                                   2.6 ความสุภาพ กิริยามารยาทของเจ้าหน้าที่ผู้ให้บริการ (เป็นมิตร/มีรอยยิ้ม/อัธยาศัยดี) : <?= $rs->assessment_26; ?><br>
                               </td>
                               <td>
                                   3.1 สถานที่ตั้งของหน่วยงาน สะดวกในการเดินทางมารับบริการ : <?= $rs->assessment_31; ?><br>
                                   3.2 ความชัดเจนของป้ายสัญลักษณ์ ประชาสัมพันธ์บอกจุดบริการ : <?= $rs->assessment_32; ?><br>
                                   3.3 ความเพียงพอของสิ่งอำนวยความสะดวก เช่น ที่จอดรถ ห้องน้ำ เก้าอี้ที่นั่งคอยรับบริการ บริการน้ำดื่ม เป็นต้น : <?= $rs->assessment_33; ?><br>
                                   3.4 ความสะอาดของสถานที่โดยรวม : <?= $rs->assessment_34; ?><br>
                                   3.5 ความเป็นระเบียบของสถานที่และอุปกรณ์ในการติดต่อใช้บริการ : <?= $rs->assessment_35; ?><br>
                               </td>
                               <td class="text-center" >
                                   <?= date('d/m/Y H:i', strtotime($rs->assessment_datesave . '+543 years')) ?> น.<br>
                                   <?php if ($_SESSION['m_level'] == 1) : ?>
                                   <a class="red-add" href="#" role="button" onclick="confirmDelete('<?= $rs->assessment_id; ?>');"><i class="bi bi-trash fa-lg "></i></a>
                                   <?php endif; ?>
                                   <script>
                                       function confirmDelete(assessment_id) {
                                           Swal.fire({
                                               title: 'กดเพื่อยืนยัน?',
                                               text: "คุณจะไม่สามรถกู้คืนได้อีก!",
                                               icon: 'warning',
                                               showCancelButton: true,
                                               confirmButtonColor: '#3085d6',
                                               cancelButtonColor: '#d33',
                                               confirmButtonText: 'ใช่, ต้องการลบ!',
                                               cancelButtonText: 'ยกเลิก' // เปลี่ยนข้อความปุ่ม Cancel เป็นภาษาไทย
                                           }).then((result) => {
                                               if (result.isConfirmed) {
                                                   window.location.href = "<?= site_url('assessment_backend/del_assessment/'); ?>" + assessment_id;
                                               }
                                           });
                                       }
                                   </script>
                               </td>
                               <!-- <td>
                                   
                               </td> -->
                           </tr>
                       <?php
                            $Index++;
                        } ?>
                   </tbody>
               </table>
           </div>
       </div>
   </div>