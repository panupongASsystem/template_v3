   <!-- DataTales Example -->
   <div class="card shadow mb-4">
       <div class="card-header py-3">
           <h6 class="m-0 font-weight-bold text-black">จัดการข้อมูลข่าวด่วน</h6>
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
                           <th style="width: 65%;">เรื่อง</th>
                           <th style="width: 13%;">อัพโหลด</th>
                           <th style="width: 7%;">วันที่</th>
                           <th style="width: 5%;">สถานะ</th>
                           <th style="width: 7%;">จัดการ</th>
                       </tr>
                   </thead>
                   <tbody>
                       <?php
                        foreach ($query as $rs) { ?>
                           <tr role="row">
                               <td align="center"><?= $Index; ?></td>
                               <td class="limited-text"><?= $rs->hotNews_text; ?></td>
                               <td><?= $rs->hotNews_by; ?></td>
                               <td><?= date('d/m/Y H:i', strtotime($rs->hotNews_datesave . '+543 years')) ?> น.</td>
                               <td>
                                   <label class="switch">
                                       <input class="form-check-input" type="checkbox" role="switch" id="flexSwitchCheck<?= $rs->hotNews_id; ?>" data-hotNews-id="<?= $rs->hotNews_id; ?>" <?= $rs->hotNews_status === 'show' ? 'checked' : ''; ?> onchange="updateHotNewsStatus<?= $rs->hotNews_id; ?>()">
                                       <span class="slider"></span>
                                   </label>
                                   <script>
                                       function updateHotNewsStatus<?= $rs->hotNews_id; ?>() {
                                           const hotNewsId = <?= $rs->hotNews_id; ?>;
                                           const newStatus = document.getElementById('flexSwitchCheck<?= $rs->hotNews_id; ?>').checked ? 'show' : 'hide';

                                           // ส่งข้อมูลไปยังเซิร์ฟเวอร์ด้วย AJAX
                                           $.ajax({
                                               type: 'POST',
                                               url: 'hotNews_backend/updateHotNewsStatus',
                                               data: {
                                                hotNews_id: hotNewsId,
                                                   new_status: newStatus
                                               },
                                               success: function(response) {
                                                   console.log(response);
                                                   // ทำอื่นๆตามต้องการ เช่น อัพเดตหน้าเว็บ
                                               },
                                               error: function(error) {
                                                   console.error(error);
                                               }
                                           });
                                       }
                                   </script>
                               </td>
                               <td>
                                   <a href="<?= site_url('hotNews_backend/editing_hotNews/' . $rs->hotNews_id); ?>"><i class="bi bi-pencil-square fa-lg "></i></a>
                                   <!-- <a href="#" role="button" onclick="confirmDelete('<?= $rs->hotNews_id; ?>');"><i class="bi bi-trash fa-lg "></i></a> -->
                                   <script>
                                       function confirmDelete(hotNews_id) {
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
                                                   window.location.href = "<?= site_url('hotNews_backend/del_hotNews/'); ?>" + hotNews_id;
                                               }
                                           });
                                       }
                                   </script>
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