<!-- sweetalert 2 -->
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11.7.31/dist/sweetalert2.all.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<!-- Include Bootstrap CSS and JavaScript -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js" integrity="sha384-C6RzsynM9kWDrMNeT87bh95OGNyZPhcTNXj1NW7RuBCsyN/o0jlpcV8Qyq46cDfL" crossorigin="anonymous"></script>
<script src="https://code.jquery.com/jquery-3.7.1.min.js" integrity="sha256-/JqT3SQfawRcv/BIHPThkBvs0OEvtFFmqPF/lYI/Cxo=" crossorigin="anonymous"></script>

<script>
// ✅ Helper function สำหรับ safe element selection
function safeQuery(selector) {
  const element = document.querySelector(selector);
  if (!element) {
    console.log(`Element not found: ${selector}`);
  }
  return element;
}

function safeQueryAll(selector) {
  const elements = document.querySelectorAll(selector);
  if (elements.length === 0) {
    console.log(`No elements found: ${selector}`);
  }
  return elements;
}

function safeAddEventListener(element, event, callback) {
  if (element) {
    element.addEventListener(event, callback);
    return true;
  }
  return false;
}

function toggleAllUsers(checkbox) {
  const userCheckboxes = safeQueryAll('.user-checkbox');
  userCheckboxes.forEach(box => {
    box.checked = checkbox.checked;
  });
}

document.addEventListener('DOMContentLoaded', function() {
  console.log('🚀 Initializing page events...');
  
  // ✅ จัดการ checkbox ทั้งหมด (ปลอดภัย)
  const userCheckboxes = safeQueryAll('.user-checkbox');
  const selectAllCheckbox = safeQuery('#selectAll');

  if (selectAllCheckbox) {
    userCheckboxes.forEach(checkbox => {
      safeAddEventListener(checkbox, 'change', function() {
        if (!this.checked) {
          selectAllCheckbox.checked = false;
        }
        const allChecked = Array.from(userCheckboxes).every(box => box.checked);
        selectAllCheckbox.checked = allChecked;
      });
    });
  }

  // ✅ จัดการการเปิด/ปิดฟอร์มของแต่ละระบบ (ปลอดภัย)
  const systemToggles = safeQueryAll('.system-toggle input[type="checkbox"]');
  systemToggles.forEach(toggle => {
    safeAddEventListener(toggle, 'change', function() {
      const formId = this.getAttribute('data-form');
      const form = formId ? safeQuery('#' + formId) : null;
      
      if (form) {
        const grantSystemInput = form.querySelector('input[name="grant_system_ref_id[]"]');

        if (this.checked) {
          form.style.display = 'block';
          if (grantSystemInput) grantSystemInput.checked = true;
        } else {
          form.style.display = 'none';
          if (grantSystemInput) grantSystemInput.checked = false;
        }
      }
    });
  });

  // ✅ จัดการ System Role และ Position Selection (ปลอดภัย)
  const systemRadios = safeQueryAll('input[name="m_system"]');
  const positionSelection = safeQuery('#positionSelection');
  const websiteSection = safeQuery('#websiteManagementSection');

  function handleSystemChange(selectedValue) {
    // จัดการ Position Selection
    if (selectedValue === 'system_admin' || selectedValue === 'super_admin') {
      if (positionSelection) {
        positionSelection.style.display = 'none';

        let grantUserInput = safeQuery('input[name="grant_user_ref_id"]');
        if (!grantUserInput) {
          grantUserInput = document.createElement('input');
          grantUserInput.type = 'hidden';
          grantUserInput.name = 'grant_user_ref_id';
          positionSelection.parentNode.appendChild(grantUserInput);
        }

        if (selectedValue === 'system_admin') {
          grantUserInput.value = '1';
        } else if (selectedValue === 'super_admin') {
          grantUserInput.value = '2';
        }
      }

      // จัดการ Website Management Section
      if (websiteSection) {
        websiteSection.style.display = 'none';

        // ยกเลิกการเลือก checkbox ทั้งหมด
        const checkboxes = websiteSection.querySelectorAll('input[type="checkbox"]');
        checkboxes.forEach(checkbox => {
          checkbox.checked = false;
        });

        // สร้างหรืออัพเดท hidden input สำหรับ website management
        let websiteGrantUserInput = websiteSection.querySelector('input[name="grant_user_ref_id"]');
        if (!websiteGrantUserInput) {
          websiteGrantUserInput = document.createElement('input');
          websiteGrantUserInput.type = 'hidden';
          websiteGrantUserInput.name = 'grant_user_ref_id';
          websiteSection.appendChild(websiteGrantUserInput);
        }
        websiteGrantUserInput.value = '1';
      }
    } else {
      // กรณีเลือก user_admin
      if (positionSelection) {
        positionSelection.style.display = 'block';
        const grantUserInput = safeQuery('input[type="hidden"][name="grant_user_ref_id"]');
        if (grantUserInput) {
          grantUserInput.remove();
        }
      }

      if (websiteSection) {
        websiteSection.style.display = 'block';
        const websiteGrantUserInput = websiteSection.querySelector('input[name="grant_user_ref_id"][type="hidden"]');
        if (websiteGrantUserInput && websiteGrantUserInput.type === 'hidden') {
          websiteGrantUserInput.remove();
        }
      }
    }
  }

  systemRadios.forEach(radio => {
    safeAddEventListener(radio, 'change', function() {
      handleSystemChange(this.value);
    });
  });

  const selectedRadio = safeQuery('input[name="m_system"]:checked');
  if (selectedRadio) {
    handleSystemChange(selectedRadio.value);
  }

  // ✅ Multi-step form (ปลอดภัย)
  const tabs = safeQueryAll('.tab');
  const sections = safeQueryAll('.form-section');
  let currentStep = 0;

  // เพิ่มในส่วนของการตรวจสอบ Step 0
  function validateStep0() {
    const firstname = safeQuery('input[name="m_fname"]');
    const lastname = safeQuery('input[name="m_lname"]');
    const username = safeQuery('input[name="m_username"]');
    const password = safeQuery('input[name="m_password"]');
    const confirm_password = safeQuery('input[name="confirm_password"]');
    const selectedRole = safeQuery('input[name="m_system"]:checked');

    // ตรวจสอบข้อมูลพื้นฐาน
    if (!firstname?.value.trim() || !lastname?.value.trim() || !username?.value.trim() || !selectedRole) {
      Swal.fire({
        icon: 'warning',
        title: 'ตรวจพบปัญหา',
        text: 'กรุณากรอกข้อมูลที่มี * ให้ครบทุกช่อง'
      });
      return false;
    }

    // ตรวจสอบรหัสผ่านเฉพาะเมื่อมีการกรอก
    if (password?.value || confirm_password?.value) {
      if (password.value !== confirm_password.value) {
        Swal.fire({
          icon: 'warning',
          title: 'ตรวจพบปัญหา',
          text: 'รหัสผ่านไม่ตรงกัน'
        });
        return false;
      }
    }

    return true;
  }

  function updateFormStep(step) {
    tabs.forEach((tab, index) => {
      if (index <= step) {
        tab.classList.add('active');
      } else {
        tab.classList.remove('active');
      }
    });

    sections.forEach((section, index) => {
      section.classList.toggle('active', index === step);
    });

    const prevBtn = safeQuery('#prevBtn');
    const nextBtn = safeQuery('#nextBtn');

    if (prevBtn) prevBtn.style.display = step === 0 ? 'none' : 'block';
    if (nextBtn) nextBtn.textContent = step === sections.length - 1 ? 'บันทึก' : 'ถัดไป';
  }

  function validateSystemStep() {
    const currentRole = safeQuery('input[name="m_system"]:checked');
    if (!currentRole) return true;

    // ถ้าเป็น system_admin หรือ super_admin ไม่ต้องตรวจสอบการเลือกหัวข้อใช้งาน
    if (currentRole.value === 'system_admin' || currentRole.value === 'super_admin') {
      return true;
    }

    // เช็คระบบจัดการเว็บไซต์
    const websiteToggle = safeQuery('#system-toggle-2');
    if (websiteToggle && websiteToggle.checked) {
      const websiteCheckboxes = safeQueryAll('#websiteManagementSection input[name="grant_user_ref_id[]"]:checked');
      if (websiteCheckboxes.length === 0) {
        Swal.fire({
          icon: 'warning',
          title: 'ตรวจพบปัญหา',
          text: 'กรุณาเลือกหัวข้อใช้งานสำหรับระบบจัดการเว็บไซต์'
        });
        return false;
      }
    }

    // เช็คระบบ Back Office
    const backOfficeToggle = safeQuery('#system-toggle-3');
    if (backOfficeToggle && backOfficeToggle.checked) {
      const positionSelect = safeQuery('select[name="ref_pid"]');
      if (!positionSelect?.value) {
        Swal.fire({
          icon: 'warning',
          title: 'ตรวจพบปัญหา',
          text: 'กรุณาเลือกตำแหน่งสำหรับระบบ Back Office'
        });
        return false;
      }
    }

    return true;
  }

  // ✅ Event listeners สำหรับปุ่ม (ปลอดภัย)
  const nextBtn = safeQuery('#nextBtn');
  if (nextBtn) {
    safeAddEventListener(nextBtn, 'click', () => {
      if (currentStep === 0) {
        if (!validateStep0()) {
          return;
        }
      } else {
        if (!validateSystemStep()) {
          return;
        }
      }

      if (currentStep < sections.length - 1) {
        currentStep++;
        updateFormStep(currentStep);
      } else if (currentStep === sections.length - 1) {
        if (!validateSystemStep()) {
          return;
        }
        const form = safeQuery('#multiStepForm');
        if (form) form.submit();
      }
    });
  }

  const prevBtn = safeQuery('#prevBtn');
  if (prevBtn) {
    safeAddEventListener(prevBtn, 'click', () => {
      if (currentStep > 0) {
        currentStep--;
        updateFormStep(currentStep);
      }
    });
  }

  // ✅ Password validation (ปลอดภัย)
  const passwordInput = safeQuery('input[name="m_password"]');
  const confirmPasswordInput = safeQuery('input[name="confirm_password"]');

  function checkPasswordMatch() {
    if (!passwordInput || !confirmPasswordInput) return;
    
    const password = passwordInput.value;
    const confirmPassword = confirmPasswordInput.value;

    if (password && confirmPassword) {
      if (password === confirmPassword) {
        confirmPasswordInput.style.borderColor = '#10B981';
      } else {
        confirmPasswordInput.style.borderColor = '#EF4444';
      }
    } else {
      confirmPasswordInput.style.borderColor = '';
    }
  }

  if (passwordInput && confirmPasswordInput) {
    safeAddEventListener(passwordInput, 'input', checkPasswordMatch);
    safeAddEventListener(confirmPasswordInput, 'input', checkPasswordMatch);
  }

  // ✅ Global functions (ปลอดภัย)
  window.togglePassword = function(inputId) {
    const input = safeQuery('#' + inputId);
    if (!input) return;
    
    const icon = input.nextElementSibling?.querySelector('i');
    if (!icon) return;

    if (input.type === 'password') {
      input.type = 'text';
      icon.classList.remove('fa-eye');
      icon.classList.add('fa-eye-slash');
    } else {
      input.type = 'password';
      icon.classList.remove('fa-eye-slash');
      icon.classList.add('fa-eye');
    }
  }

  window.previewImage = function(input) {
    if (input.files && input.files[0]) {
      const reader = new FileReader();
      reader.onload = function(e) {
        const preview = safeQuery('#preview');
        if (preview) {
          preview.src = e.target.result;
        }
      }
      reader.readAsDataURL(input.files[0]);
    }
  }

  // ✅ Initialize form step (ปลอดภัย)
  if (sections.length > 0) {
    updateFormStep(currentStep);
  }
});

// ✅ jQuery functions (ปลอดภัย)
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
      icon: 'success',
      title: 'ลบข้อมูลสำเร็จ',
      showConfirmButton: false,
      timer: 1500
    })
  <?php } ?>
});
	
	
	
	
	
</script>