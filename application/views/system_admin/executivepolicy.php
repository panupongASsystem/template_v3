   <!-- DataTales Example -->
   <div class="card shadow mb-4">
       <div class="card-header py-3">
           <h6 class="m-0 font-weight-bold text-black">จัดการข้อมูลนโยบายของผู้บริหาร</h6>
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
                           <th style="width: 35%;">รายละเอียด</th>
                           <th style="width: 13%;">รูปภาพ</th>
                           <th style="width: 15%;">ไฟล์เอกสาร</th>
                           <th style="width: 15%;">อัพโหลด</th>
                           <th style="width: 7%;">วันที่</th>
                           <th style="width: 10%;">จัดการ</th>
                       </tr>
                   </thead>
                   <tbody>
                       <?php
                        foreach ($executivepolicy as $rs) { ?>
                           <tr role="row">
                               <td align="center"><?= $Index; ?></td>
                               <td><?= $rs->executivepolicy_detail; ?></td>
                               <td>
                                   <?php foreach ($rs->img as $img) : ?>
                                       <a href="<?php echo base_url('docs/img/' . $img->executivepolicy_img_img); ?>" data-lightbox="image-1" data-title="image-1">
                                           <img src="<?php echo base_url('docs/img/' . $img->executivepolicy_img_img); ?>" width="120px" height="80px"><br>
                                       </a>
                                   <?php endforeach; ?>
                               </td>
                               <td>
                                   <?php foreach ($rs->pdf as $pdf) : ?>
                                       <a class="btn btn-primary btn-sm mt-1" href="<?php echo base_url('docs/file/' . $pdf->executivepolicy_pdf_pdf); ?>" target="_blank">ดูไฟล์เดิม!</a><br>
                                   <?php endforeach; ?>
                               </td>
                               <td><?= $rs->executivepolicy_by; ?></td>
                               <td><?= date('d/m/Y H:i', strtotime($rs->executivepolicy_datesave . '+543 years')) ?> น.</td>
                               <td>
                                   <a href="<?= site_url('executivepolicy_backend/editing/' . $rs->executivepolicy_id); ?>"><i class="bi bi-pencil-square fa-lg "></i></a>
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