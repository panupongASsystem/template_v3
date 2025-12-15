   <!-- DataTales Example -->
   <div class="card shadow mb-4">
       <div class="card-header py-3">
           <h6 class="m-0 font-weight-bold text-black">จัดการข้อมูลเอกสารเงินอุดหนุนเด็กแรกเกิด</h6>
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
                           <th style="width: 55%;">เรื่อง</th>
                           <th style="width: 13%;">ไฟล์เอกสาร</th>
                           <th style="width: 13%;">อัพโหลด</th>
                           <th style="width: 7%;">วันที่</th>
                           <th style="width: 7%;">จัดการ</th>
                       </tr>
                   </thead>
                   <tbody>
                       <?php foreach ($query as $rs) { ?>
                           <tr role="row">
                               <td align="center"><?= $Index; ?></td>
                               <td><?= $rs->kid_aw_form_name; ?></td>
                               <td>
                                   <?php if (!empty($rs->kid_aw_form_file)) : ?>
                                       <a class="btn btn-info btn-sm mb-2" href="<?= base_url('docs/file/' . $rs->kid_aw_form_file); ?>" target="_blank">ดูไฟล์ <?= $rs->kid_aw_form_file; ?></a>
                                   <?php endif; ?>
                               </td>
                               <td><?= $rs->kid_aw_form_by; ?></td>
                               <td><?= date('d/m/Y H:i', strtotime($rs->kid_aw_form_datesave . '+543 years')) ?> น.</td>
                               <td><a href="<?= site_url('kid_aw_form_backend/editing/' . $rs->kid_aw_form_id); ?>"><i class="bi bi-pencil-square fa-lg "></i></a></td>
                           </tr>
                       <?php
                            $Index++;
                        } ?>
                   </tbody>
               </table>
           </div>
       </div>
   </div>