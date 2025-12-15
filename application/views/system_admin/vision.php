   <!-- DataTales Example -->
   <div class="card shadow mb-4">
       <div class="card-header py-3">
           <h6 class="m-0 font-weight-bold text-black">จัดการข้อมูลวิสัยทัศน์และพันธกิจ</h6>
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
                           <th style="width: 25%;">รายละเอียด</th>
                           <th style="width: 20%;">ข้อความหน้าหลัก</th> <!-- เพิ่มบรรทัดนี้ -->
                           <th style="width: 13%;">รูปภาพ</th>
                           <th style="width: 15%;">ไฟล์เอกสาร</th>
                           <th style="width: 15%;">อัพโหลด</th>
                           <th style="width: 7%;">วันที่</th>
                           <th style="width: 10%;">จัดการ</th>
                       </tr>
                   </thead>
                   <tbody>
                       <?php
                        foreach ($vision as $rs) { ?>
                           <tr role="row">
                               <td align="center"><?= $Index; ?></td>
                               <td><?= $rs->vision_detail; ?></td>
                               <td><?= $rs->vision_home_text; ?></td> <!-- เพิ่มบรรทัดนี้ -->
                               <td>
                                   <?php foreach ($rs->img as $img) : ?>
                                       <a href="<?php echo base_url('docs/img/' . $img->vision_img_img); ?>" data-lightbox="image-1" data-title="image-1">
                                           <img src="<?php echo base_url('docs/img/' . $img->vision_img_img); ?>" width="120px" height="80px"><br>
                                       </a>
                                   <?php endforeach; ?>
                               </td>
                               <td>
                                   <?php foreach ($rs->pdf as $pdf) : ?>
                                       <a class="btn btn-primary btn-sm mt-1" href="<?php echo base_url('docs/file/' . $pdf->vision_pdf_pdf); ?>" target="_blank">ดูไฟล์เดิม!</a><br>
                                   <?php endforeach; ?>
                               </td>
                               <td><?= $rs->vision_by; ?></td>
                               <td><?= date('d/m/Y H:i', strtotime($rs->vision_datesave . '+543 years')) ?> น.</td>
                               <td>
                                   <a href="<?= site_url('vision_backend/editing/' . $rs->vision_id); ?>"><i class="bi bi-pencil-square fa-lg "></i></a>
                               </td>
                           </tr>
                       <?php
                            $Index++;
                        } ?>
                   </tbody>
               </table>
           </div>
       </div>
   </div>