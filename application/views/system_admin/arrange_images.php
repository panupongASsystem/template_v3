<div class="container-fluid">
    <div class="row">
        <div class="col-md-12">
            <div class="card shadow mb-4">
                <div class="card-header py-3 d-flex justify-content-between align-items-center">
                    <h6 class="m-0 font-weight-bold text-primary">
                        <i class="fas fa-images mr-2"></i> จัดตำแหน่งรูปภาพ <?= $module_name ?>
                    </h6>
                    <a href="<?= site_url($back_url . '/' . $item_id); ?>" class="btn btn-secondary btn-sm">
                        <i class="fas fa-arrow-left mr-1"></i> กลับไปหน้าแก้ไข
                    </a>
                </div>
                <div class="card-body">
                    <div class="alert alert-info mb-4">
                        <i class="fas fa-info-circle mr-2"></i> ลากและวางรูปภาพเพื่อจัดเรียงตำแหน่ง จากนั้นกดปุ่ม "บันทึกลำดับรูปภาพ" เพื่อบันทึกการเปลี่ยนแปลง
                    </div>

                    <?php if (!empty($images)) { ?>
                        <div id="sortable-images" class="image-arrange-container mb-4">
                            <?php foreach ($images as $index => $img) {
                                $img_id = $img->{$img_field};
                                $img_file = $img->{$img_name_field};
                                $img_url = base_url('docs/img/' . $img_file);
                            ?>
                                <div class="image-arrange-item sortable-item" data-img-id="<?= $img_id; ?>">
                                    <div class="card">
                                        <div class="img-container">
                                            <a href="<?= $img_url; ?>" data-fancybox="gallery" data-caption="<?= htmlspecialchars($img_file); ?>">
                                                <img src="<?= $img_url; ?>" class="img-fluid" alt="รูปภาพ <?= $index + 1; ?>" onerror="this.onerror=null; this.src='data:image/svg+xml;base64,PHN2ZyB3aWR0aD0iMjAwIiBoZWlnaHQ9IjIwMCIgeG1sbnM9Imh0dHA6Ly93d3cudzMub3JnLzIwMDAvc3ZnIj48cmVjdCB3aWR0aD0iMjAwIiBoZWlnaHQ9IjIwMCIgZmlsbD0iI2VlZSIvPjx0ZXh0IHg9IjUwJSIgeT0iNTAlIiBkeT0iLjNlbSIgZmlsbD0iIzk5OSIgZm9udC1mYW1pbHk9InNhbnMtc2VyaWYiIGZvbnQtc2l6ZT0iMTRweCIgdGV4dC1hbmNob3I9Im1pZGRsZSI+SW1hZ2UgTm90IEZvdW5kPC90ZXh0Pjwvc3ZnPg==';">
                                            </a>
                                            <div class="img-number"><?= $index + 1; ?></div>
                                            <div class="img-handle">
                                                <i class="fas fa-arrows-alt"></i>
                                            </div>
                                        </div>

                                        <div class="card-footer p-2 text-center">
                                            <small class="text-muted">
                                                <?= htmlspecialchars(strlen($img_file) > 20 ? substr($img_file, 0, 20) . '...' : $img_file); ?>
                                            </small>
                                            <div class="order-badge">
                                                ลำดับที่: <span class="img-order"><?= $index + 1; ?></span>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            <?php } ?>
                        </div>

                        <div class="text-center mb-4">
                            <button type="button" id="save-order" class="btn btn-success btn-lg px-5">
                                <i class="fas fa-save mr-2"></i> บันทึกลำดับรูปภาพ
                            </button>
                        </div>

                        <div id="order-saved-message" class="alert alert-success text-center animated fadeIn" style="display: none;">
                            <i class="fas fa-check-circle mr-2"></i> บันทึกลำดับรูปภาพเรียบร้อยแล้ว
                        </div>
                    <?php } else { ?>
                        <div class="alert alert-warning text-center py-5">
                            <i class="fas fa-exclamation-triangle fa-3x mb-3"></i>
                            <h5>ไม่พบรูปภาพที่จะจัดเรียง</h5>
                            <p class="mb-0">กรุณาอัพโหลดรูปภาพก่อนทำการจัดเรียง</p>
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
    .image-arrange-container {
        display: grid;
        grid-template-columns: repeat(5, 1fr);
        gap: 20px;
        padding: 15px;
        background-color: #f8f9fc;
        border-radius: 10px;
        transition: all 0.3s;
    }

    @media (max-width: 1200px) {
        .image-arrange-container {
            grid-template-columns: repeat(4, 1fr);
        }
    }

    @media (max-width: 992px) {
        .image-arrange-container {
            grid-template-columns: repeat(3, 1fr);
        }
    }

    @media (max-width: 768px) {
        .image-arrange-container {
            grid-template-columns: repeat(2, 1fr);
        }
    }

    @media (max-width: 576px) {
        .image-arrange-container {
            grid-template-columns: 1fr;
        }
    }

    .image-arrange-item {
        transition: transform 0.3s, box-shadow 0.3s;
        margin-bottom: 10px;
    }

    .image-arrange-item:hover {
        transform: translateY(-5px);
        z-index: 10;
    }

    .image-arrange-item .card {
        border: none;
        border-radius: 10px;
        overflow: hidden;
        box-shadow: 0 4px 15px rgba(0, 0, 0, 0.1);
        transition: box-shadow 0.3s;
    }

    .image-arrange-item:hover .card {
        box-shadow: 0 8px 25px rgba(0, 0, 0, 0.15);
    }

    .img-container {
        position: relative;
        height: 180px;
        overflow: hidden;
    }

    .img-container img {
        width: 100%;
        height: 100%;
        object-fit: cover;
        transition: transform 0.3s;
    }

    .image-arrange-item:hover .img-container img {
        transform: scale(1.05);
    }

    .img-number {
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

    .img-handle {
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

    .img-handle:hover {
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

    /* Loading overlay */
    .loading-overlay {
        position: fixed;
        top: 0;
        left: 0;
        width: 100%;
        height: 100%;
        background-color: rgba(0, 0, 0, 0.5);
        z-index: 10000;
        display: none;
        justify-content: center;
        align-items: center;
    }

    .loading-spinner {
        color: white;
        font-size: 2rem;
    }
</style>

<!-- Loading overlay -->
<div class="loading-overlay" id="loadingOverlay">
    <div class="loading-spinner">
        <i class="fas fa-spinner fa-spin"></i>
        <div>กำลังโหลด...</div>
    </div>
</div>

<script>
    // รอให้ DOM โหลดเสร็จก่อน
    document.addEventListener('DOMContentLoaded', function() {
        console.log('DOM loaded, checking libraries...');

        // ตรวจสอบว่า jQuery โหลดแล้วหรือไม่
        if (typeof jQuery === 'undefined') {
            console.error('jQuery ไม่ได้โหลด');
            return;
        }

        console.log('jQuery version:', jQuery.fn.jquery);

        // ใช้ jQuery ready event
        jQuery(document).ready(function($) {
            console.log('jQuery ready');

            // ตรวจสอบ jQuery UI
            if (typeof $.ui === 'undefined') {
                console.error('jQuery UI ไม่ได้โหลด');
                return;
            }

            console.log('jQuery UI version:', $.ui.version);

            // ตรวจสอบ SweetAlert2
            if (typeof Swal === 'undefined') {
                console.error('SweetAlert2 ไม่ได้โหลด');
                return;
            }

            console.log('All libraries loaded successfully');

            // Initialize Fancybox
            if (typeof $.fancybox !== 'undefined') {
                $('[data-fancybox]').fancybox({
                    buttons: ['zoom', 'download', 'close']
                });
            }

            // อัพเดตเลขลำดับตอนโหลดหน้า
            updateImageOrder();

            // ทำให้รูปภาพสามารถลากเรียงลำดับได้
            $("#sortable-images").sortable({
                items: ".sortable-item",
                placeholder: "sortable-placeholder image-arrange-item",
                cursor: "move",
                opacity: 0.7,
                handle: ".img-handle",
                revert: 200,
                tolerance: "pointer",
                start: function(event, ui) {
                    console.log('Drag started');
                    ui.item.addClass("dragging");
                    ui.placeholder.height(ui.item.height());
                },
                stop: function(event, ui) {
                    console.log('Drag stopped');
                    ui.item.removeClass("dragging");
                },
                update: function(event, ui) {
                    console.log('Order updated');
                    updateImageOrder();
                }
            }).disableSelection();

            // อัพเดตตัวเลขลำดับของรูปภาพ
            function updateImageOrder() {
                $(".sortable-item").each(function(index) {
                    $(this).find(".img-order").text(index + 1);
                    $(this).find(".img-number").text(index + 1);
                });
            }

            // บันทึกลำดับรูปภาพ
            $("#save-order").click(function() {
                console.log('Save button clicked');

                var $button = $(this);
                var originalText = $button.html();

                // แสดง loading
                $button.html('<i class="fas fa-spinner fa-spin mr-2"></i> กำลังบันทึก...');
                $button.prop('disabled', true);

                // สร้างข้อมูลลำดับรูปภาพ
                var imageOrder = [];
                $(".sortable-item").each(function(index) {
                    var imgId = $(this).data("img-id");
                    if (imgId) {
                        imageOrder.push({
                            id: imgId,
                            order: index + 1
                        });
                    }
                });

                console.log("Image Order Data:", imageOrder);

                // ตรวจสอบว่ามีข้อมูลหรือไม่
                if (imageOrder.length === 0) {
                    console.error('No image data found');
                    $button.html(originalText);
                    $button.prop('disabled', false);

                    Swal.fire({
                        title: 'เกิดข้อผิดพลาด!',
                        text: 'ไม่พบข้อมูลรูปภาพที่จะบันทึก',
                        icon: 'error'
                    });
                    return;
                }

                // ส่งข้อมูลไป server
                var postData = {
                    table_name: "<?= $table_name ?>",
                    img_field: "<?= $img_field ?>",
                    order_field: "<?= $order_field ?>",
                    image_order: JSON.stringify(imageOrder)
                    <?php if (isset($csrf_token_name) && isset($csrf_hash)) : ?>,
                        <?= $csrf_token_name ?>: "<?= $csrf_hash ?>"
                    <?php endif; ?>
                };

                console.log("POST Data:", postData);

                $.ajax({
                    type: "POST",
                    url: "<?= site_url('images_position/update_order'); ?>",
                    data: postData,
                    dataType: 'json',
                    timeout: 30000,
                    beforeSend: function() {
                        console.log("Sending AJAX request...");
                    },
                    success: function(response) {
                        console.log("AJAX Success Response:", response);

                        $button.html('<i class="fas fa-check-circle mr-2"></i> บันทึกเรียบร้อย');
                        $("#order-saved-message").fadeIn();

                        Swal.fire({
                            title: 'สำเร็จ!',
                            text: 'บันทึกลำดับรูปภาพเรียบร้อยแล้ว',
                            icon: 'success',
                            timer: 1500,
                            showConfirmButton: false
                        }).then(function() {
                            window.location.href = "<?= site_url($back_url . '/' . $item_id); ?>";
                        });
                    },
                    error: function(xhr, status, error) {
                        console.error("AJAX Error:", {
                            status: status,
                            error: error,
                            responseText: xhr.responseText,
                            statusCode: xhr.status
                        });

                        $button.html('<i class="fas fa-times-circle mr-2"></i> เกิดข้อผิดพลาด');

                        setTimeout(function() {
                            $button.html(originalText);
                            $button.prop('disabled', false);
                        }, 2000);

                        var errorMessage = 'เกิดข้อผิดพลาดในการบันทึกข้อมูล';

                        if (xhr.status === 404) {
                            errorMessage = 'ไม่พบหน้าที่ต้องการ (404)';
                        } else if (xhr.status === 500) {
                            errorMessage = 'เกิดข้อผิดพลาดที่เซิร์ฟเวอร์ (500)';
                        } else if (status === 'timeout') {
                            errorMessage = 'การเชื่อมต่อหมดเวลา กรุณาลองใหม่อีกครั้ง';
                        }

                        Swal.fire({
                            title: 'เกิดข้อผิดพลาด!',
                            text: errorMessage,
                            icon: 'error',
                            footer: 'รายละเอียดข้อผิดพลาด: ' + error
                        });
                    }
                });
            });

            console.log('Script initialization completed');
        });
    });
</script>