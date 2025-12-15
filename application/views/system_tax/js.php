<!-- sweetalert 2 -->
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11.7.31/dist/sweetalert2.all.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<!-- Include Bootstrap CSS and JavaScript -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js" integrity="sha384-C6RzsynM9kWDrMNeT87bh95OGNyZPhcTNXj1NW7RuBCsyN/o0jlpcV8Qyq46cDfL" crossorigin="anonymous"></script>
<script src="https://code.jquery.com/jquery-3.7.1.min.js" integrity="sha256-/JqT3SQfawRcv/BIHPThkBvs0OEvtFFmqPF/lYI/Cxo=" crossorigin="anonymous"></script>
<!-- เพิ่ม Script ApexCharts -->
<script src="https://cdn.jsdelivr.net/npm/apexcharts"></script>
<script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.10.2/dist/umd/popper.min.js"></script>
  <!-- พรีวิวรูปภาพ -->
  <script src="https://cdnjs.cloudflare.com/ajax/libs/fancybox/3.5.7/jquery.fancybox.min.js"></script>
<script>
  $(document).ready(function() {
    // จัดการการคลิก tab
    $('.nav-tabs button').click(function() {
      // ลบ active จากทุก tab
      $('.nav-tabs button').removeClass('active');
      // เพิ่ม active ให้ tab ที่คลิก
      $(this).addClass('active');

      // ซ่อนทุก content
      $('.tab-content').addClass('hidden');
      // แสดง content ที่เกี่ยวข้อง
      $('#' + $(this).data('tab')).removeClass('hidden');

      // จัดการปุ่มบันทึก/อัพโหลด
      if ($(this).data('tab') === 'file-upload') {
        $('#manualSubmitBtn').hide();
        $('#uploadSubmitBtn').show();
      } else {
        $('#manualSubmitBtn').show();
        $('#uploadSubmitBtn').hide();
      }
    });
  });

  function openArrearsModal() {
    $('#arrearsModal').modal('show');
  }

  function openPaymentSettings() {
    $('#paymentSettingsModal').modal('show');
    loadCurrentSettings();
  }

  function closeModal() {
    $('#paymentSettingsModal').modal('hide');
    $('#arrearsModal').modal('hide');
  }

  function loadCurrentSettings() {
    $.get('<?= site_url("system_tax/get_payment_settings") ?>', function(data) {
      if (data.status === 'success') {
        $('select[name="bank_name"]').val(data.settings.bank_name);
        $('input[name="account_name"]').val(data.settings.account_name);
        $('input[name="account_number"]').val(data.settings.account_number);
        if (data.settings.qr_code_image) {
          $('#qr_preview').html(`<img src="<?= base_url('docs/img/') ?>${data.settings.qr_code_image}" class="max-w-xs">`);
        }
      }
    });
  }

  $(document).ready(function() {
    // Preview QR Code image
    $('#qr_code_image').change(function(e) {
      if (this.files && this.files[0]) {
        var reader = new FileReader();
        reader.onload = function(e) {
          $('#qr_preview img').attr('src', e.target.result);
          $('#qr_preview img').addClass('border-4 border-blue-200');
        }
        reader.readAsDataURL(this.files[0]);
      }
    });

    // เพิ่ม event สำหรับปุ่มยกเลิก
    $('.cancel-btn').click(function() {
      $('#paymentSettingsModal').modal('hide');
    });

    // Form submission with animation
    // Form submission with animation
    $('#paymentSettingsForm').on('submit', function(e) {
      e.preventDefault();

      // เช็คข้อมูลก่อนส่ง
      if (!$('select[name="bank_name"]').val()) {
        Swal.fire({
          title: 'แจ้งเตือน!',
          text: 'กรุณาเลือกธนาคาร',
          icon: 'warning'
        });
        return;
      }
      if (!$('input[name="account_name"]').val()) {
        Swal.fire({
          title: 'แจ้งเตือน!',
          text: 'กรุณากรอกชื่อบัญชี',
          icon: 'warning'
        });
        return;
      }
      if (!$('input[name="account_number"]').val()) {
        Swal.fire({
          title: 'แจ้งเตือน!',
          text: 'กรุณากรอกเลขที่บัญชี',
          icon: 'warning'
        });
        return;
      }

      $('.save-btn')
        .prop('disabled', true)
        .html('<i class="fas fa-spinner fa-spin mr-2"></i>กำลังบันทึก...');

      var formData = new FormData(this);

      $.ajax({
        url: '<?= site_url("system_tax/update_payment_settings") ?>',
        type: 'POST',
        data: formData,
        processData: false,
        contentType: false,
        success: function(response) {
          try {
            response = typeof response === 'string' ? JSON.parse(response) : response;
            if (response.status === 'success') {
              Swal.fire({
                title: 'บันทึกสำเร็จ!',
                text: 'ข้อมูลการชำระเงินถูกบันทึกเรียบร้อยแล้ว',
                icon: 'success',
                showConfirmButton: false,
                timer: 1500
              }).then(() => {
                window.location.reload(); // รีเฟรชหน้าเว็บ
              });
            } else {
              Swal.fire({
                title: 'เกิดข้อผิดพลาด!',
                text: response.message || 'ไม่สามารถบันทึกข้อมูลได้',
                icon: 'error'
              });
            }
          } catch (e) {
            console.error(e);
            Swal.fire({
              title: 'เกิดข้อผิดพลาด!',
              text: 'ไม่สามารถประมวลผลข้อมูลได้',
              icon: 'error'
            });
          }
        },
        error: function(xhr, status, error) {
          console.error(xhr.responseText);
          Swal.fire({
            title: 'เกิดข้อผิดพลาด!',
            text: 'ไม่สามารถเชื่อมต่อกับเซิร์ฟเวอร์ได้',
            icon: 'error'
          });
        },
        complete: function() {
          $('.save-btn')
            .prop('disabled', false)
            .html('<i class="fas fa-save mr-2"></i>บันทึก');
        }
      });
    });
  });

  $(document).ready(function() {
    // รวม flashdata alerts เข้าด้วยกัน
    <?php
    $flashdata = array(
      'save_success' => array('success', 'บันทึกข้อมูลสำเร็จ', false),
      'save_again' => array('warning', 'มีข้อมูลอยู่แล้ว!', true),
      'save_error' => array('error', 'หน่วยความจำของท่าเต็ม!', true),
      'save_maxsize' => array('error', 'ขนาดรูปภาพต้องไม่เกิน 1.5MB!', true),
      'save_required' => array('warning', 'กรุณากรอกข้อมูลที่มี * ให้ครบทุกช่อง', true),
      'password_mismatch' => array('warning', 'รหัสผ่านไม่ตรงกัน!', true)
    );

    foreach ($flashdata as $key => $value) {
      if ($this->session->flashdata($key)) { ?>
        Swal.fire({
          icon: '<?php echo $value[0]; ?>',
          title: '<?php echo $value[1]; ?>',
          showConfirmButton: <?php echo $value[2] ? 'true' : 'false'; ?>,
          timer: <?php echo $value[2] ? 'undefined' : '1500'; ?>
          <?php echo $value[2] ? ", footer: '<a href=\"#\">ติดต่อผู้ดูแลระบบ?</a>'" : ''; ?>
        });
    <?php }
    } ?>
  });

  $(document).ready(function() {
    <?php if ($this->session->flashdata('del_success')) { ?>
      Swal.fire({
        // position: 'top-end',
        icon: 'success',
        title: 'ลบข้อมูลสำเร็จ',
        showConfirmButton: false,
        timer: 1500
      })
    <?php } ?>
  });

  $(document).ready(function() {
    <?php if ($this->session->flashdata('verify_success')) { ?>
      Swal.fire({
        // position: 'top-end',
        icon: 'success',
        title: 'อนุมัติการชำระภาษีเรียบร้อยแล้ว',
        showConfirmButton: false,
        timer: 1500
      })
    <?php } ?>
  });

  $(document).ready(function() {
    <?php if ($this->session->flashdata('reject_success')) { ?>
      Swal.fire({
        // position: 'top-end',
        icon: 'error',
        title: 'ปฏิเสธการชำระภาษีเรียบร้อยแล้ว',
        showConfirmButton: false,
        timer: 1500
      })
    <?php } ?>
  });

  $(document).ready(function() {
    <?php if ($this->session->flashdata('verify_reject_error')) { ?>
      Swal.fire({
        // position: 'top-end',
        icon: 'warning',
        title: 'ไม่สามารถอนุมัติการชำระภาษีได้',
        showConfirmButton: false,
        timer: 1500
      })
    <?php } ?>
  });
</script>