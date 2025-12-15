   <!-- DataTales Example -->
   <div class="card shadow mb-4">
       <div class="card-header py-3">
           <h6 class="m-0 font-weight-bold text-black">จัดการข้อมูลอำเภอ</h6>
       </div>
       <div class="card-body">
           <div class="table-responsive">

               <?php
                $Index = 1;
                ?>
               <table id="newdataTables" class="table">
                   <thead>
                       <tr>
                           <th style="width: 3%;">ลำดับ</th>
                           <th style="width: 15%;">อำเภอ</th>
                           <th style="width: 20%;">รหัสสายทาง</th>
                           <th style="width: 22%;">เขตรับผิดชอบ</th>
                           <th style="width: 17%;">เบอร์โทร สจ.</th>
                           <th style="width: 13%;">อัพโหลด</th>
                           <th style="width: 7%;">วันที่</th>
                           <th style="width: 3%;">จัดการ</th>
                       </tr>
                   </thead>
                   <tbody>
                       <?php
                        foreach ($query as $rs) { ?>
                           <tr role="row">
                               <td align="center"><?= $Index; ?></td>
                               <td><?= $rs->road_district; ?></td>
                               <td><?= $rs->road_code; ?></td>
                               <td><?= $rs->road_responsibility; ?></td>
                               <td><?= $rs->road_phone; ?></td>
                               <td><?= $rs->road_by; ?></td>
                               <td><?= date('d/m/Y H:i', strtotime($rs->road_datesave . '+543 years')) ?> น.</td>
                               <td align="center"><a href="<?= site_url('road_backend/editing/' . $rs->road_id); ?>"><i class="bi bi-pencil-square fa-lg "></i></a></td>
                           </tr>
                       <?php
                            $Index++;
                        } ?>
                   </tbody>
               </table>
           </div>
       </div>
   </div>