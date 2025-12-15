<style>
    /* Fancybox customization */
    .cursor-zoom-in {
        cursor: zoom-in !important;
    }
    
    .image-preview {
        position: relative;
        overflow: hidden;
        border-radius: 0.5rem;
    }
    
    .image-preview::after {
        content: '🔍';
        position: absolute;
        top: 50%;
        left: 50%;
        transform: translate(-50%, -50%);
        opacity: 0;
        transition: opacity 0.2s;
        font-size: 1.2rem;
        pointer-events: none;
    }
    
    .image-preview:hover::after {
        opacity: 1;
    }
    
    .image-preview img {
        transition: transform 0.3s ease;
    }
    
    .image-preview:hover img {
        transform: scale(1.1);
    }

    /* Fancybox Modal Customization */
    .fancybox-bg {
        background: rgba(0, 0, 0, 0.85);
    }

    .fancybox-caption {
        background: linear-gradient(0deg, rgba(0,0,0,0.85) 0%, rgba(0,0,0,0.3) 50%, rgba(0,0,0,0) 100%);
        padding: 1rem;
        font-size: 1rem;
    }

    .fancybox-button {
        background: rgba(0, 0, 0, 0.5);
        color: #fff;
    }

    .fancybox-button:hover {
        background: rgba(0, 0, 0, 0.7);
    }

    .fancybox-image {
        border-radius: 4px;
    }
    
  .tab-container {
    display: flex;
    justify-content: space-between;
    /* เปลี่ยนจาก center เป็น space-between */
    align-items: center;
    gap: 1rem;
    /* ลด gap ลง */
    margin-bottom: 4rem;
    position: relative;
    padding: 0 2rem;
    max-width: 1600px;
    /* เพิ่มความกว้างสูงสุด */
    margin-left: auto;
    margin-right: auto;
  }

  .tab {
    flex: 0 0 auto;
    /* ป้องกันการหด */
    width: 2.5rem;
    height: 2.5rem;
    display: flex;
    align-items: center;
    justify-content: center;
    border-radius: 50%;
    background: white;
    border: 2px solid #e5e7eb;
    cursor: pointer;
    position: relative;
    z-index: 2;
    transition: all 0.3s ease;
  }

  .tab-label {
    position: absolute;
    top: 100%;
    left: 50%;
    transform: translateX(-50%);
    margin-top: 0.75rem;
    white-space: normal;
    /* เปลี่ยนจาก nowrap เป็น normal */
    font-size: 0.75rem;
    color: #6b7280;
    width: 120px;
    /* กำหนดความกว้างคงที่ */
    text-align: center;
    min-height: 2.5rem;
    /* กำหนดความสูงขั้นต่ำ */
    overflow-wrap: break-word;
    /* ให้ข้อความขึ้นบรรทัดใหม่ได้ */
  }

  /* เส้นเชื่อม */
  .tab-container::after {
    content: '';
    position: absolute;
    width: 100%;
    height: 2px;
    background: #e5e7eb;
    top: 50%;
    left: 0;
    z-index: 1;
    max-width: calc(100% - 4rem);
    /* ลดความกว้างของเส้นเชื่อม */
    margin: 0 2rem;
    /* เพิ่ม margin ซ้ายขวา */
  }

  /* สีเมื่อ active */
  .tab.active {
    background: #3b82f6;
    border-color: #3b82f6;
    color: white;
  }

  /* สีเมื่อ completed */
  .tab.completed {
    background: #10b981;
    border-color: #10b981;
    color: white;
  }

  .tab-label {
    position: absolute;
    top: 100%;
    left: 50%;
    transform: translateX(-50%);
    margin-top: 0.5rem;
    white-space: nowrap;
    font-size: 0.875rem;
    color: #6b7280;
  }

  .form-section {
    display: none;
    background: white;
    padding: 2rem;
    border-radius: 0.5rem;
    box-shadow: 0 1px 3px 0 rgb(0 0 0 / 0.1);
  }

  .form-section.active {
    display: block;
  }

  /* Custom Toggle Switch */
  .system-toggle {
    margin: 1rem 0;
  }

  .system-toggle .toggle-container {
    background-color: white;
    padding: 1rem;
    border-radius: 1rem;
    box-shadow: 0 1px 3px rgba(0, 0, 0, 0.1);
    border: 1px solid #e5e7eb;
    transition: all 0.2s ease;
  }

  .system-toggle input[type="checkbox"] {
    display: none;
  }

  .system-toggle label {
    display: flex;
    align-items: center;
    gap: 1rem;
    cursor: pointer;
  }

  .system-toggle .icon {
    background-color: #f3f4f6;
    padding: 0.75rem;
    border-radius: 0.75rem;
    display: flex;
    align-items: center;
    justify-content: center;
  }

  .system-toggle .icon svg {
    width: 1.5rem;
    height: 1.5rem;
    color: #6b7280;
  }

  .system-toggle .text-content {
    flex: 1;
  }

  .system-toggle .title {
    font-weight: 500;
    color: #1f2937;
    margin-bottom: 0.25rem;
  }

  .system-toggle .description {
    font-size: 0.875rem;
    color: #6b7280;
  }

  .system-toggle .toggle-switch {
    width: 2.25rem;
    height: 1.25rem;
    background-color: #e5e7eb;
    border-radius: 999px;
    position: relative;
    transition: all 0.2s ease;
  }

  .system-toggle .toggle-switch::before {
    content: '';
    position: absolute;
    width: 1rem;
    height: 1rem;
    border-radius: 50%;
    background-color: white;
    left: 2px;
    top: 2px;
    transition: all 0.2s ease;
    box-shadow: 0 1px 2px rgba(0, 0, 0, 0.1);
  }

  .system-toggle input[type="checkbox"]:checked+label .toggle-switch {
    background-color: #3b82f6;
  }

  .system-toggle input[type="checkbox"]:checked+label .toggle-switch::before {
    transform: translateX(1rem);
  }

  .system-toggle input[type="checkbox"]:checked+label .icon {
    background-color: #ebf5ff;
  }

  .system-toggle input[type="checkbox"]:checked+label .icon svg {
    color: #3b82f6;
  }

  .system-form {
    display: none;
    padding: 1rem;
    background: #f9fafb;
    border-radius: 0.5rem;
    margin-top: 1rem;
  }

  .button-container {
    display: flex;
    justify-content: space-between;
    margin-top: 2rem;
  }

  .btn {
    padding: 0.5rem 1rem;
    border-radius: 0.375rem;
    font-weight: 500;
    transition: all 0.2s;
  }

  .btn-primary {
    background: #3b82f6;
    color: white;
  }

  .btn-primary:hover {
    background: #2563eb;
  }

  .btn-secondary {
    background: #9ca3af;
    color: white;
  }

  .btn-secondary:hover {
    background: #6b7280;
  }

  /* Toggle Switch Styles */
  input:checked~.dot {
    transform: translateX(100%);
  }

  input:checked~.block {
    background-color: #10B981;
  }

  .dot {
    transition: all 0.3s ease-in-out;
  }

  /* ตั้งค่าชำระเงิน ---------------------- */
  .modal-dialog {
    max-width: 450px;
    width: 100%;
    margin: 1.75rem auto;
  }

  .modal {
    position: fixed;
    top: 0;
    left: 0;
    z-index: 1055;
    width: 100%;
    height: 100%;
    background-color: rgba(0, 0, 0, 0.5);
    display: none;
    padding: 1rem;
    /* เพิ่มเพื่อให้มี space รอบๆ ใน mobile */
  }

  .modal.show {
    display: block;
  }

  .modal-dialog-centered {
    display: flex;
    align-items: center;
    min-height: calc(100% - 3.5rem);
  }

  /* ปรับขนาด preview QR Code ให้ใหญ่ขึ้น */
  #qr_preview img {
    height: 200px;
    /* เพิ่มจาก h-32 เป็น 200px */
    width: 300px;
    /* เพิ่มจาก w-32 เป็น 200px */
    transition: all 0.3s ease;
  }

  /* เพิ่ม padding ใน modal body ให้มากขึ้น */
  .modal-body {
    padding: 2rem;
    /* width: 500px; */
  }

  /* เพิ่มขนาดฟอนต์ */
  .form-group label {
    font-size: 1rem !important;
  }

  .form-input,
  .form-select {
    font-size: 1rem !important;
    padding: 0.75rem 1rem;
  }

  /* ปรับขนาดปุ่มให้ใหญ่ขึ้น */
  .save-btn,
  .cancel-btn {
    padding: 0.75rem 1.5rem;
    font-size: 1rem;
  }

  @media (max-width: 768px) {
    .modal-dialog {
      width: 95%;
      margin: 1rem auto;
    }
  }

  .save-btn:hover {
    transform: translateY(-1px);
    box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1), 0 2px 4px -1px rgba(0, 0, 0, 0.06);
  }

  /* -------------------------------- */

  .nav-tabs button.active {
    border-bottom: 2px solid #4F46E5;
    color: #4F46E5;
    background-color: #F3F4F6;
}

.tab-content {
    transition: all 0.3s ease;
}

/* กรณีที่เป็น link ทั่วไป */
a {
    text-decoration: none !important;
}
</style>