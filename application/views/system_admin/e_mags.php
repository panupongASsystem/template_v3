<!-- Fancybox CSS -->
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/@fancyapps/ui@5.0/dist/fancybox/fancybox.css">

<!-- PDF.js for auto cover generation -->
<script src="https://cdnjs.cloudflare.com/ajax/libs/pdf.js/2.16.105/pdf.min.js"></script>

<a class="btn add-btn" href="<?= site_url('E_mags_backend/adding'); ?>" role="button">
    <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-plus-circle" viewBox="0 0 16 16">
        <path d="M8 15A7 7 0 1 1 8 1a7 7 0 0 1 0 14zm0 1A8 8 0 1 0 8 0a8 8 0 0 0 0 16z" />
        <path d="M8 4a.5.5 0 0 1 .5.5v3h3a.5.5 0 0 1 0 1h-3v3a.5.5 0 0 1-1 0v-3h-3a.5.5 0 0 1 0-1h3v-3A.5.5 0 0 1 8 4z" />
    </svg> เพิ่มข้อมูล</a>
<a class="btn btn-light" href="<?= site_url('E_mags_backend'); ?>" role="button">
    <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-arrow-clockwise" viewBox="0 0 16 16">
        <path fill-rule="evenodd" d="M8 3a5 5 0 1 0 4.546 2.914.5.5 0 0 1 .908-.417A6 6 0 1 1 8 2v1z" />
        <path d="M8 4.466V.534a.25.25 0 0 1 .41-.192l2.36 1.966c.12.1.12.284 0 .384L8.41 4.658A.25.25 0 0 1 8 4.466z" />
    </svg> Refresh Data</a>

<!-- DataTales Example -->
<div class="card shadow mb-4">
    <div class="card-header py-3">
        <h6 class="m-0 font-weight-bold text-black">จัดการข้อมูล E-Magazine</h6>
    </div>
    <div class="card-body">
        <div class="table-responsive">
            <?php $Index = 1; ?>
            <table id="newdataTables" class="table">
                <thead>
                    <tr>
                        <th style="width: 5%;">ลำดับ</th>
                        <th style="width: 15%;">รูปหน้าปก</th>
                        <th style="width: 20%;">ชื่อ E-Magazine</th>
                        <th style="width: 15%;">ไฟล์ PDF</th>
                        <th style="width: 15%;">ขนาดไฟล์</th>
                        <th style="width: 15%;">วันที่อัปโหลด</th>
                        <th style="width: 15%;">จัดการ</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($e_mags as $rs) { ?>
                        <tr role="row">
                            <td align="center"><?= $Index; ?></td>
                            <td>
                                <!-- รูปหน้าปก -->
                                <div class="cover-image">
                                    <?php 
                                    $cover_path = './docs/img/' . $rs['cover_image'];
                                    $cover_exists = !empty($rs['cover_image']) && file_exists($cover_path);
                                    ?>
                                    
                                    <?php if ($cover_exists) : ?>
                                        <a href="<?= base_url('docs/img/' . $rs['cover_image']); ?>" 
                                           data-fancybox="cover-<?= $rs['id']; ?>" 
                                           data-caption="<?= htmlspecialchars($rs['original_name']); ?>">
                                            <img src="<?= base_url('docs/img/' . $rs['cover_image']); ?>" 
                                                 class="img-thumbnail" 
                                                 width="120px" 
                                                 height="100px"
                                                 style="object-fit: cover; cursor: pointer;"
                                                 alt="หน้าปก">
                                        </a>
                                    <?php else : ?>
                                        <!-- ยังไม่มีรูปหน้าปก - สร้างอัตโนมัติ -->
                                        <div class="auto-cover-placeholder" data-pdf-file="<?= $rs['file_name']; ?>" data-cover-file="<?= $rs['cover_image']; ?>" data-emag-id="<?= $rs['id']; ?>">
                                            <div class="placeholder-content">
                                                <i class="bi bi-file-earmark-pdf" style="font-size: 2rem; color: #dc3545;"></i>
                                                <br>
                                                <small class="text-muted">กำลังสร้างหน้าปก...</small>
                                                <br>
                                                <button class="btn btn-sm btn-primary mt-1" onclick="generateCoverForItem(this)">
                                                    <i class="bi bi-image"></i> สร้างหน้าปก
                                                </button>
                                            </div>
                                        </div>
                                    <?php endif; ?>
                                </div>
                            </td>
                            <td>
                                <div class="magazine-info">
                                    <h6 class="mb-1"><?= htmlspecialchars($rs['original_name']); ?></h6>
                                </div>
                            </td>
                            <td>
                                <!-- ไฟล์ PDF -->
                                <?php if (!empty($rs['file_name'])) : ?>
                                    <a class="btn btn-outline-danger btn-sm" 
                                       href="<?= base_url('docs/file/' . $rs['file_name']); ?>" 
                                       target="_blank"
                                       title="ดูไฟล์ PDF">
                                        <i class="bi bi-file-earmark-pdf"></i>
                                        PDF
                                    </a>
                                    <br>
                                    <small class="text-muted mt-1">
                                        <?= substr($rs['file_name'], 0, 20) . '...'; ?>
                                    </small>
                                <?php else : ?>
                                    <small class="text-muted">ไม่มีไฟล์</small>
                                <?php endif; ?>
                            </td>
                            <td>
                                <div class="file-sizes">
                                    <?php 
                                    // คำนวณขนาดไฟล์
                                    $pdf_path = './docs/file/' . $rs['file_name'];
                                    $cover_path = './docs/img/' . $rs['cover_image'];
                                    
                                    $pdf_size = file_exists($pdf_path) ? filesize($pdf_path) : 0;
                                    $cover_size = file_exists($cover_path) ? filesize($cover_path) : 0;
                                    
                                    // ฟังก์ชันแปลงขนาดไฟล์ (ใช้ชื่อไม่ซ้ำกัน)
                                    if (!function_exists('formatEMagFileSize')) {
                                        function formatEMagFileSize($size, $precision = 2) {
                                            if ($size == 0) return '0 B';
                                            $units = array('B', 'KB', 'MB', 'GB');
                                            $i = 0;
                                            while ($size >= 1024 && $i < count($units) - 1) {
                                                $size /= 1024;
                                                $i++;
                                            }
                                            return round($size, $precision) . ' ' . $units[$i];
                                        }
                                    }
                                    ?>
                                    
                                    <div class="mb-1">
                                        <i class="bi bi-file-earmark-pdf text-danger"></i>
                                        <small>PDF: <?= formatEMagFileSize($pdf_size); ?></small>
                                    </div>
                                    <div>
                                        <i class="bi bi-image text-success"></i>
                                        <small>Cover: <?= formatEMagFileSize($cover_size); ?></small>
                                    </div>
                                </div>
                            </td>
                            <td>
                                <?php
                                $upload_date = new DateTime($rs['uploaded_at']);
                                $thai_date = $upload_date->format('d/m/') . ($upload_date->format('Y') + 543);
                                $time = $upload_date->format('H:i');
                                ?>
                                <div class="upload-date">
                                    <div><?= $thai_date; ?></div>
                                    <small class="text-muted"><?= $time; ?> น.</small>
                                </div>
                            </td>
                            <td>
                                <div class="btn-group" role="group">
                                    <!-- ปุ่มดูตัวอย่าง -->
                                    <a href="<?= base_url('docs/file/' . $rs['file_name']); ?>" 
                                       class="btn btn-sm btn-outline-info" 
                                       target="_blank"
                                       title="ดูตัวอย่าง">
                                        <i class="bi bi-eye"></i>
                                    </a>
                                    
                                    <!-- ปุ่มแก้ไข -->
                                    <a href="<?= site_url('E_mags_backend/editing/' . $rs['id']); ?>" 
                                       class="btn btn-sm btn-outline-primary" 
                                       title="แก้ไข">
                                        <i class="bi bi-pencil-square"></i>
                                    </a>
                                    
                                    <!-- ปุ่มลบ -->
                                    <button type="button" 
                                            class="btn btn-sm btn-outline-danger" 
                                            onclick="confirmDelete(<?= $rs['id']; ?>, '<?= htmlspecialchars($rs['original_name']); ?>');" 
                                            title="ลบ">
                                        <i class="bi bi-trash"></i>
                                    </button>
                                </div>
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

<!-- JavaScript สำหรับ DataTables และ SweetAlert -->
<script>
    // PDF.js worker
    pdfjsLib.GlobalWorkerOptions.workerSrc = 'https://cdnjs.cloudflare.com/ajax/libs/pdf.js/2.16.105/pdf.worker.min.js';

    // DataTables
    $(document).ready(function() {
        $('#eMagDataTables').DataTable({
            "language": {
                "url": "//cdn.datatables.net/plug-ins/1.10.24/i18n/Thai.json"
            },
            "pageLength": 10,
            "order": [[ 0, "desc" ]],
            "columnDefs": [
                { "orderable": false, "targets": [1, 6] }, // ไม่ให้เรียงรูปภาพและปุ่มจัดการ
                { "searchable": false, "targets": [1, 6] } // ไม่ค้นหารูปภาพและปุ่มจัดการ
            ]
        });

        // ตรวจสอบว่าต้องสร้างรูปหน้าปกหรือไม่
        checkCoverGeneration();
    });

    // ฟังก์ชันตรวจสอบและสร้างรูปหน้าปก
    function checkCoverGeneration() {
        <?php if ($this->session->flashdata('need_cover_generation')): ?>
            const coverData = <?= $this->session->flashdata('need_cover_generation'); ?>;
            generateCoverFromPDF(coverData.pdf_file, coverData.cover_file);
        <?php endif; ?>
    }

    // ฟังก์ชันสร้างรูปหน้าปกสำหรับ item เฉพาะ
    function generateCoverForItem(button) {
        const container = button.closest('.auto-cover-placeholder');
        const pdfFile = container.dataset.pdfFile;
        const coverFile = container.dataset.coverFile;
        const emagId = container.dataset.emagId;
        
        // Disable button
        button.disabled = true;
        button.innerHTML = '<i class="bi bi-hourglass-split"></i> กำลังสร้าง...';
        
        generateCoverFromPDF(pdfFile, coverFile).then(() => {
            // สร้างสำเร็จ - รีเฟรชแค่ส่วนนี้
            location.reload();
        }).catch(() => {
            // เกิดข้อผิดพลาด - รีเซ็ตปุ่ม
            button.disabled = false;
            button.innerHTML = '<i class="bi bi-image"></i> สร้างหน้าปก';
        });
    }

    // ฟังก์ชันสร้างรูปหน้าปกจาก PDF (แก้ไขให้ return Promise)
    function generateCoverFromPDF(pdfFilename, coverFilename) {
        return new Promise((resolve, reject) => {
            const pdfUrl = '<?= base_url("docs/file/"); ?>' + pdfFilename;
            
            // โหลด PDF ด้วย PDF.js
            pdfjsLib.getDocument(pdfUrl).promise.then(function(pdf) {
                // ดึงหน้าแรก
                return pdf.getPage(1);
            }).then(function(page) {
                // สร้าง canvas
                const canvas = document.createElement('canvas');
                const context = canvas.getContext('2d');
                
                // กำหนดขนาด (scale 1.5 เพื่อความคมชัด)
                const viewport = page.getViewport({ scale: 1.5 });
                canvas.height = viewport.height;
                canvas.width = viewport.width;
                
                // Render หน้า PDF ลงใน canvas
                const renderContext = {
                    canvasContext: context,
                    viewport: viewport
                };
                
                return page.render(renderContext).promise.then(() => {
                    return canvas.toDataURL('image/png');
                });
            }).then(function(imageData) {
                // ส่งข้อมูลรูปไปบันทึกที่เซิร์ฟเวอร์
                return $.ajax({
                    url: '<?= site_url("E_mags_backend/save_generated_cover"); ?>',
                    type: 'POST',
                    data: {
                        cover_filename: coverFilename,
                        cover_data: imageData
                    }
                });
            }).then(function(response) {
                if (response.status === 'success') {
                    resolve(response);
                } else {
                    reject(response);
                }
            }).catch(function(error) {
                console.error('Error generating cover:', error);
                reject(error);
            });
        });
    }

    // ฟังก์ชันยืนยันการลบ
    function confirmDelete(id, name) {
        Swal.fire({
            title: 'คุณแน่ใจหรือไม่?',
            text: 'คุณต้องการลบ E-Magazine "' + name + '" ใช่หรือไม่?',
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#3085d6',
            cancelButtonColor: '#d33',
            confirmButtonText: 'ใช่, ต้องการลบ!',
            cancelButtonText: 'ยกเลิก'
        }).then((result) => {
            if (result.isConfirmed) {
                window.location.href = "<?= site_url('E_mags_backend/del/'); ?>" + id;
            }
        });
    }
</script>

<!-- Fancybox JavaScript -->
<script src="https://cdn.jsdelivr.net/npm/@fancyapps/ui@5.0/dist/fancybox/fancybox.umd.js"></script>
<script>
    // Initialize Fancybox
    Fancybox.bind('[data-fancybox]', {
        Thumbs: {
            autoStart: false,
        },
        Toolbar: {
            display: {
                left: ["infobar"],
                middle: [
                    "zoomIn",
                    "zoomOut",
                    "toggle1to1",
                    "rotateCCW",
                    "rotateCW",
                    "flipX",
                    "flipY",
                ],
                right: ["slideshow", "download", "thumbs", "close"],
            },
        },
    });
</script>

<style>
    .cover-image img {
        transition: transform 0.3s ease;
    }
    
    .cover-image img:hover {
        transform: scale(1.05);
    }
    
    .magazine-info h6 {
        font-size: 14px;
        font-weight: 600;
        line-height: 1.2;
        margin-bottom: 0.25rem;
    }
    
    .file-sizes {
        font-size: 12px;
    }
    
    .upload-date {
        font-size: 13px;
    }
    
    .btn-group .btn {
        margin-right: 2px;
    }
    
    .btn-group .btn:last-child {
        margin-right: 0;
    }
    
    /* Custom DataTables styling */
    .dataTables_wrapper .dataTables_length,
    .dataTables_wrapper .dataTables_filter,
    .dataTables_wrapper .dataTables_info,
    .dataTables_wrapper .dataTables_processing,
    .dataTables_wrapper .dataTables_paginate {
        color: #333;
    }
    
    .table td {
        vertical-align: middle;
    }
    
    /* Auto cover placeholder styling */
    .auto-cover-placeholder {
        width: 120px;
        height: 160px;
        border: 2px dashed #dee2e6;
        border-radius: 8px;
        display: flex;
        align-items: center;
        justify-content: center;
        background-color: #f8f9fa;
        transition: all 0.3s ease;
    }
    
    .auto-cover-placeholder:hover {
        border-color: #007bff;
        background-color: #e7f3ff;
    }
    
    .placeholder-content {
        text-align: center;
        padding: 10px;
    }
    
    .placeholder-content small {
        font-size: 11px;
        line-height: 1.2;
    }
</style>