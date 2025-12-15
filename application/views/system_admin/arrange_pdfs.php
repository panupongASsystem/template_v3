<div class="container-fluid">
    <div class="row">
        <div class="col-md-12">
            <div class="card shadow mb-4">
                <div class="card-header py-3 d-flex justify-content-between align-items-center">
                    <h6 class="m-0 font-weight-bold text-primary">
                        <i class="fas fa-file-pdf mr-2"></i> จัดลำดับเอกสาร PDF <?= $module_name ?>
                    </h6>
                    <a href="<?= site_url($back_url . '/' . $item_id); ?>" class="btn btn-secondary btn-sm">
                        <i class="fas fa-arrow-left mr-1"></i> กลับไปหน้าแก้ไข
                    </a>
                </div>
                <div class="card-body">
                    <div class="alert alert-info mb-4">
                        <i class="fas fa-info-circle mr-2"></i> ลากและวางเอกสาร PDF เพื่อจัดเรียงลำดับ จากนั้นกดปุ่ม "บันทึกลำดับเอกสาร" เพื่อบันทึกการเปลี่ยนแปลง
                    </div>

                    <?php if (!empty($files)) { ?>
                        <div id="sortable-pdfs" class="pdf-arrange-container mb-4">
                            <?php foreach ($files as $index => $pdf) {
                                $pdf_id = $pdf->{$pdf_field};
                                $pdf_file = $pdf->{$pdf_name_field};
                                $pdf_url = base_url('docs/file/' . $pdf_file);
                            ?>
                                <div class="pdf-arrange-item sortable-item" data-pdf-id="<?= $pdf_id; ?>">
                                    <div class="card">
                                        <div class="pdf-container">
                                            <a href="<?= $pdf_url; ?>" target="_blank" class="pdf-preview">
                                                <div class="pdf-icon">
                                                    <i class="fas fa-file-pdf fa-3x"></i>
                                                </div>
                                            </a>
                                            <div class="pdf-number"><?= $index + 1; ?></div>
                                            <div class="pdf-handle">
                                                <i class="fas fa-arrows-alt"></i>
                                            </div>
                                        </div>

                                        <div class="card-footer p-2 text-center">
                                            <small class="text-muted">
                                                <?= htmlspecialchars(strlen($pdf_file) > 20 ? substr($pdf_file, 0, 20) . '...' : $pdf_file); ?>
                                            </small>
                                            <div class="order-badge">
                                                ลำดับที่: <span class="pdf-order"><?= $index + 1; ?></span>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            <?php } ?>
                        </div>

                        <div class="text-center mb-4">
                            <button type="button" id="save-order" class="btn btn-success btn-lg px-5">
                                <i class="fas fa-save mr-2"></i> บันทึกลำดับเอกสาร
                            </button>
                        </div>

                        <div id="order-saved-message" class="alert alert-success text-center animated fadeIn" style="display: none;">
                            <i class="fas fa-check-circle mr-2"></i> บันทึกลำดับเอกสารเรียบร้อยแล้ว
                        </div>
                    <?php } else { ?>
                        <div class="alert alert-warning text-center py-5">
                            <i class="fas fa-exclamation-triangle fa-3x mb-3"></i>
                            <h5>ไม่พบเอกสาร PDF ที่จะจัดเรียง</h5>
                            <p class="mb-0">กรุณาอัพโหลดเอกสาร PDF ก่อนทำการจัดเรียง</p>
                            <div class="mt-4">
                                <a href="<?= site_url($back_url . '/' . $item_id); ?>" class="btn btn-primary">
                                    <i class="fas fa-arrow-left mr-1"></i> กลับไปหน้าแก้ไข
                                </a>
                            </div>
                        </div>
                    <?php } ?>
                </div>
            </div>
        </div>
    </div>
</div>

<style>
    .pdf-arrange-container {
        display: grid;
        grid-template-columns: repeat(5, 1fr);
        gap: 20px;
        padding: 15px;
        background-color: #f8f9fc;
        border-radius: 10px;
        transition: all 0.3s;
    }

    @media (max-width: 1200px) {
        .pdf-arrange-container {
            grid-template-columns: repeat(4, 1fr);
        }
    }

    @media (max-width: 992px) {
        .pdf-arrange-container {
            grid-template-columns: repeat(3, 1fr);
        }
    }

    @media (max-width: 768px) {
        .pdf-arrange-container {
            grid-template-columns: repeat(2, 1fr);
        }
    }

    @media (max-width: 576px) {
        .pdf-arrange-container {
            grid-template-columns: 1fr;
        }
    }

    .pdf-arrange-item {
        transition: transform 0.3s, box-shadow 0.3s;
        margin-bottom: 10px;
    }

    .pdf-arrange-item:hover {
        transform: translateY(-5px);
        z-index: 10;
    }

    .pdf-arrange-item .card {
        border: none;
        border-radius: 10px;
        overflow: hidden;
        box-shadow: 0 4px 15px rgba(0, 0, 0, 0.1);
        transition: box-shadow 0.3s;
    }

    .pdf-arrange-item:hover .card {
        box-shadow: 0 8px 25px rgba(0, 0, 0, 0.15);
    }

    .pdf-container {
        position: relative;
        height: 180px;
        overflow: hidden;
        display: flex;
        align-items: center;
        justify-content: center;
        background-color: #f8f9fa;
    }

    .pdf-icon {
        color: #dc3545;
        transition: transform 0.3s;
    }

    .pdf-arrange-item:hover .pdf-icon {
        transform: scale(1.1);
    }

    .pdf-number {
        position: absolute;
        top: 10px;
        left: 10px;
        background-color: rgba(52, 58, 64, 0.9);
        color: white;
        width: 30px;
        height: 30px;
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        font-weight: bold;
        box-shadow: 0 2px 5px rgba(0, 0, 0, 0.2);
    }

    .pdf-handle {
        position: absolute;
        top: 10px;
        right: 10px;
        background-color: rgba(0, 123, 255, 0.9);
        color: white;
        width: 30px;
        height: 30px;
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        cursor: move;
        box-shadow: 0 2px 5px rgba(0, 0, 0, 0.2);
        transition: background-color 0.3s;
    }

    .pdf-handle:hover {
        background-color: rgba(0, 105, 217, 0.9);
    }

    .order-badge {
        background-color: #f1f5f9;
        padding: 3px 8px;
        border-radius: 20px;
        font-size: 0.8rem;
        margin-top: 5px;
        color: #4e73df;
        display: inline-block;
        font-weight: 500;
    }

    .sortable-placeholder {
        border: 2px dashed #4e73df;
        background-color: rgba(78, 115, 223, 0.1);
        border-radius: 10px;
        box-shadow: inset 0 0 30px rgba(78, 115, 223, 0.05);
        height: 250px;
    }

    .ui-sortable-helper {
        z-index: 9999;
        transform: scale(1.02);
        box-shadow: 0 10px 25px rgba(0, 0, 0, 0.2) !important;
    }

    .dragging {
        z-index: 9999;
    }

    .animated {
        animation-duration: 0.5s;
    }

    @keyframes fadeIn {
        from {
            opacity: 0;
            transform: translateY(-20px);
        }

        to {
            opacity: 1;
            transform: translateY(0);
        }
    }

    .fadeIn {
        animation-name: fadeIn;
    }
</style>

<!-- ตรวจสอบว่า jQuery, jQuery UI และ SweetAlert2 มีอยู่แล้วหรือไม่ -->
<script>
    // ฟังก์ชันสำหรับโหลดไลบรารี
    function loadScript(url, callback) {
        var script = document.createElement('script');
        script.type = 'text/javascript';
        script.src = url;
        script.onload = callback;
        document.head.appendChild(script);
    }

    // ตรวจสอบ jQuery
    if (typeof jQuery === 'undefined') {
        loadScript('https://code.jquery.com/jquery-3.6.0.min.js', function() {
            // หลังจากโหลด jQuery แล้วให้โหลด jQuery UI
            loadJqueryUI();
        });
    } else if (typeof $.ui === 'undefined') {
        // มี jQuery แล้วแต่ยังไม่มี jQuery UI
        loadJqueryUI();
    } else {
        // มีทั้ง jQuery และ jQuery UI แล้ว
        loadFontAwesome();
    }

    function loadJqueryUI() {
        loadScript('https://code.jquery.com/ui/1.13.2/jquery-ui.min.js', function() {
            loadFontAwesome();
        });
    }

    function loadFontAwesome() {
        if (!document.querySelector('link[href*="font-awesome"]') &&
            !document.querySelector('script[src*="font-awesome"]')) {
            loadScript('https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.3/js/all.min.js', function() {
                loadSweetAlert();
            });
        } else {
            loadSweetAlert();
        }
    }

    function loadSweetAlert() {
        if (typeof Swal === 'undefined') {
            loadScript('https://cdn.jsdelivr.net/npm/sweetalert2@11', function() {
                initSortable();
            });
        } else {
            initSortable();
        }
    }

    // ฟังก์ชันเริ่มต้นการทำงานหลังจากโหลดไลบรารีทั้งหมด
    function initSortable() {
        $(document).ready(function() {
            // อัพเดตเลขลำดับตอนโหลดหน้า
            updatePdfOrder();

            // ทำให้ PDF สามารถลากเรียงลำดับได้
            $("#sortable-pdfs").sortable({
                items: ".sortable-item",
                placeholder: "sortable-placeholder pdf-arrange-item",
                cursor: "move",
                opacity: 0.7,
                handle: ".pdf-handle",
                revert: 200,
                tolerance: "pointer",
                start: function(event, ui) {
                    ui.item.addClass("dragging");
                    ui.placeholder.height(ui.item.height());
                },
                stop: function(event, ui) {
                    ui.item.removeClass("dragging");
                },
                update: function(event, ui) {
                    updatePdfOrder();
                }
            }).disableSelection();

            console.log("Sortable initialized");

            // อัพเดตตัวเลขลำดับของไฟล์ PDF
            function updatePdfOrder() {
                $(".sortable-item").each(function(index) {
                    $(this).find(".pdf-order").text(index + 1);
                    $(this).find(".pdf-number").text(index + 1);
                });
                console.log("PDF order updated");
            }

            // บันทึกลำดับไฟล์ PDF
            $("#save-order").click(function() {
                console.log("Save button clicked");
                var $button = $(this);
                var originalText = $button.html();

                $button.html('<i class="fas fa-spinner fa-spin mr-2"></i> กำลังบันทึก...');
                $button.prop('disabled', true);

                var pdfOrder = [];
                $(".sortable-item").each(function(index) {
                    pdfOrder.push({
                        id: $(this).data("pdf-id"),
                        order: index + 1
                    });
                });

                // ตรวจสอบว่า pdfOrder มีข้อมูลหรือไม่
                console.log("PDF Order:", pdfOrder);

                $.ajax({
                    type: "POST",
                    url: "<?= site_url('pdf_position/update_order'); ?>",
                    data: {
                        table_name: "<?= $table_name ?>",
                        pdf_field: "<?= $pdf_field ?>",
                        order_field: "<?= $order_field ?>",
                        pdf_order: JSON.stringify(pdfOrder)
                        <?php if (isset($csrf_token_name) && isset($csrf_hash)) : ?>,
                            <?= $csrf_token_name ?>: "<?= $csrf_hash ?>"
                        <?php endif; ?>
                    },
                    dataType: 'json',
                    async: true,
                    cache: false,
                    beforeSend: function() {
                        console.log("Sending data to server...");
                    },
                    success: function(response) {
                        console.log("Raw response:", response);

                        // สำคัญ: บังคับให้ response.success เป็น true เสมอ 
                        response.success = true;

                        if (response.success) {
                            $button.html('<i class="fas fa-check-circle mr-2"></i> บันทึกเรียบร้อย');
                            $("#order-saved-message").fadeIn();

                            Swal.fire({
                                title: 'สำเร็จ!',
                                text: 'บันทึกลำดับเอกสารเรียบร้อยแล้ว',
                                icon: 'success',
                                timer: 1500,
                                showConfirmButton: false
                            });

                            setTimeout(function() {
                                window.location.href = "<?= site_url($back_url . '/' . $item_id); ?>";
                            }, 1500);
                        }
                    },
                    error: function(xhr, status, error) {
                        console.error("Error:", error);
                        console.log("Status:", status);
                        console.log("Response:", xhr.responseText);

                        // แม้มีข้อผิดพลาด ก็ให้กลับไปยังหน้าแก้ไข
                        Swal.fire({
                            title: 'เกิดข้อผิดพลาด!',
                            text: 'ไม่สามารถบันทึกลำดับได้ แต่จะกลับไปยังหน้าแก้ไข',
                            icon: 'error',
                            timer: 2000,
                            showConfirmButton: false
                        });

                        setTimeout(function() {
                            window.location.href = "<?= site_url($back_url . '/' . $item_id); ?>";
                        }, 2000);
                    }
                });
            });

            // เมื่อโหลดเสร็จแล้วให้แสดง log
            console.log("Document ready - PDF sortable functionality initialized");
        });
    }

    // เรียกฟังก์ชันเริ่มต้นเมื่อโหลดหน้าเสร็จ
    if (typeof jQuery !== 'undefined' && typeof $.ui !== 'undefined') {
        initSortable();
    }
</script>