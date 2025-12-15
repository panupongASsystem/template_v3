<style>
.delete-modal .swal2-popup {
    max-width: 500px;
}
.delete-modal .swal2-html-container {
    text-align: left;
}
</style>
<style>
	
	
	
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
      input:checked ~ .dot {
        transform: translateX(100%);
    }
    
    input:checked ~ .block {
        background-color: #10B981;
    }

    .dot {
        transition: all 0.3s ease-in-out;
    }
</style>

<style>
/* CSS สำหรับ Toggle Switch */
.toggle-switch {
    position: relative;
    display: inline-block;
    width: 52px;
    height: 26px;
}

.toggle-switch-checkbox {
    opacity: 0;
    width: 0;
    height: 0;
}

.toggle-switch-slider {
    position: absolute;
    cursor: pointer;
    top: 0;
    left: 0;
    right: 0;
    bottom: 0;
    background-color: #ccc;
    transition: .4s;
    border-radius: 34px;
}

.toggle-switch-slider:before {
    position: absolute;
    content: "";
    height: 18px;
    width: 18px;
    left: 4px;
    bottom: 4px;
    background-color: white;
    transition: .4s;
    border-radius: 50%;
}

.toggle-switch-checkbox:checked + .toggle-switch-slider {
    background-color: #3b82f6; /* สีฟ้า (blue-500) */
}

.toggle-switch-checkbox:focus + .toggle-switch-slider {
    box-shadow: 0 0 1px #3b82f6;
}

.toggle-switch-checkbox:checked + .toggle-switch-slider:before {
    transform: translateX(26px);
}

.toggle-switch-container {
    display: flex;
    align-items: center;
}
	
	
	
	
</style>

<style>
    #multiStepForm label {
        font-size: 1.125rem; /* text-lg */
    }
</style>

<style>
/* Apple-inspired styles for member details popup */
.apple-style-popup {
    max-width: 650px;
    width: 100%;
    border-radius: 16px !important;
    padding: 0 !important;
    box-shadow: 0 20px 60px rgba(0, 0, 0, 0.15) !important;
    background: linear-gradient(145deg, #ffffff, #f8f9fa) !important;
    overflow: hidden !important;
}

.apple-style-popup .swal2-title {
    padding: 24px 24px 0 !important;
    font-size: 22px !important;
    font-weight: 600 !important;
    color: #1d1d1f !important;
}

.apple-style-popup .swal2-html-container {
    margin: 10px 0 0 !important;
    padding: 0 24px 24px !important;
    overflow-x: hidden !important;
}

.apple-style-close {
    top: 16px !important;
    right: 16px !important;
    color: #86868b !important;
}

.text-gradient {
    background: linear-gradient(90deg, #06b6d4, #3b82f6);
    -webkit-background-clip: text;
    -webkit-text-fill-color: transparent;
}

/* Profile Section */
.profile-container {
    display: flex;
    justify-content: center;
    margin-bottom: 16px;
}

.profile-image-container {
    width: 100px;
    height: 100px;
    border-radius: 50%;
    overflow: hidden;
    background: white;
    box-shadow: 0 5px 15px rgba(0, 0, 0, 0.08);
    border: 3px solid white;
}

.profile-image {
    width: 100%;
    height: 100%;
    object-fit: cover;
}

.member-name {
    text-align: center;
    font-size: 20px;
    font-weight: 600;
    color: #1d1d1f;
    margin-bottom: 24px;
}

/* Info Container */
.info-container {
    display: flex;
    flex-direction: column;
    gap: 16px;
}

.info-section {
    display: grid;
    grid-template-columns: repeat(2, 1fr);
    gap: 16px;
}

.info-card {
    display: flex;
    align-items: flex-start;
    padding: 16px;
    background: rgba(255, 255, 255, 0.5);
    border-radius: 12px;
    backdrop-filter: blur(10px);
    -webkit-backdrop-filter: blur(10px);
    box-shadow: 0 4px 12px rgba(0, 0, 0, 0.03);
    border: 1px solid rgba(227, 232, 238, 0.7);
    transition: all 0.3s ease;
}

.info-card:hover {
    transform: translateY(-2px);
    box-shadow: 0 8px 16px rgba(0, 0, 0, 0.05);
}

.info-card.full-width {
    grid-column: span 2;
}

.info-icon {
    width: 36px;
    height: 36px;
    display: flex;
    align-items: center;
    justify-content: center;
    background: linear-gradient(135deg, #eef2ff, #e0e7ff);
    border-radius: 10px;
    color: #4f46e5;
    margin-right: 14px;
    font-size: 16px;
}

.info-content {
    flex: 1;
}

.info-label {
    font-size: 13px;
    color: #86868b;
    margin-bottom: 4px;
}

.info-value {
    font-size: 15px;
    color: #1d1d1f;
    font-weight: 500;
}

.info-subvalue {
    font-size: 12px;
    color: #86868b;
    margin-top: 2px;
}

.status-indicator {
    display: inline-block;
    padding: 4px 12px;
    border-radius: 50px;
    font-size: 13px;
    font-weight: 500;
}

.status-active {
    background-color: rgba(16, 185, 129, 0.1);
    color: #10b981;
}

.status-inactive {
    background-color: rgba(239, 68, 68, 0.1);
    color: #ef4444;
}

/* Footer Actions */
.footer-actions {
    margin-top: 24px;
    display: flex;
    justify-content: center;
}

.action-button {
    padding: 10px 20px;
    border-radius: 50px;
    font-size: 14px;
    font-weight: 500;
    border: none;
    cursor: pointer;
    transition: all 0.2s ease;
}

.edit-button {
    background: linear-gradient(135deg, #3b82f6, #2563eb);
    color: white;
    box-shadow: 0 4px 10px rgba(59, 130, 246, 0.3);
}

.edit-button:hover {
    transform: translateY(-2px);
    box-shadow: 0 6px 15px rgba(59, 130, 246, 0.4);
}
</style>

<style>
/* Apple-inspired styles for member details popup */
.apple-style-popup {
    max-width: 650px;
    width: 100%;
    border-radius: 16px !important;
    padding: 0 !important;
    box-shadow: 0 20px 60px rgba(0, 0, 0, 0.15) !important;
    background: linear-gradient(145deg, #ffffff, #f8f9fa) !important;
    overflow: hidden !important;
}

.apple-style-popup .swal2-title {
    padding: 24px 24px 0 !important;
    font-size: 22px !important;
    font-weight: 600 !important;
    color: #1d1d1f !important;
}

.apple-style-popup .swal2-html-container {
    margin: 10px 0 0 !important;
    padding: 0 24px 24px !important;
    overflow-x: hidden !important;
}

.apple-style-close {
    top: 16px !important;
    right: 16px !important;
    color: #86868b !important;
}

.text-gradient {
    background: linear-gradient(90deg, #06b6d4, #3b82f6);
    -webkit-background-clip: text;
    -webkit-text-fill-color: transparent;
}

/* Profile Section */
.profile-container {
    display: flex;
    justify-content: center;
    margin-bottom: 16px;
}

.profile-image-container {
    width: 100px;
    height: 100px;
    border-radius: 50%;
    overflow: hidden;
    background: white;
    box-shadow: 0 5px 15px rgba(0, 0, 0, 0.08);
    border: 3px solid white;
}

.profile-image {
    width: 100%;
    height: 100%;
    object-fit: cover;
}

.member-name {
    text-align: center;
    font-size: 20px;
    font-weight: 600;
    color: #1d1d1f;
    margin-bottom: 24px;
}

/* Info Container */
.info-container {
    display: flex;
    flex-direction: column;
    gap: 16px;
}

.info-section {
    display: grid;
    grid-template-columns: repeat(2, 1fr);
    gap: 16px;
}

.info-card {
    display: flex;
    align-items: flex-start;
    padding: 16px;
    background: rgba(255, 255, 255, 0.5);
    border-radius: 12px;
    backdrop-filter: blur(10px);
    -webkit-backdrop-filter: blur(10px);
    box-shadow: 0 4px 12px rgba(0, 0, 0, 0.03);
    border: 1px solid rgba(227, 232, 238, 0.7);
    transition: all 0.3s ease;
}

.info-card:hover {
    transform: translateY(-2px);
    box-shadow: 0 8px 16px rgba(0, 0, 0, 0.05);
}

.info-card.full-width {
    grid-column: span 2;
}

.info-icon {
    width: 36px;
    height: 36px;
    display: flex;
    align-items: center;
    justify-content: center;
    background: linear-gradient(135deg, #eef2ff, #e0e7ff);
    border-radius: 10px;
    color: #4f46e5;
    margin-right: 14px;
    font-size: 16px;
}

.info-content {
    flex: 1;
}

.info-label {
    font-size: 13px;
    color: #86868b;
    margin-bottom: 4px;
}

.info-value {
    font-size: 15px;
    color: #1d1d1f;
    font-weight: 500;
}

.info-subvalue {
    font-size: 12px;
    color: #86868b;
    margin-top: 2px;
}

.status-indicator {
    display: inline-block;
    padding: 4px 12px;
    border-radius: 50px;
    font-size: 13px;
    font-weight: 500;
}

.status-active {
    background-color: rgba(16, 185, 129, 0.1);
    color: #10b981;
}

.status-inactive {
    background-color: rgba(239, 68, 68, 0.1);
    color: #ef4444;
}

/* System Tags */
.system-tags {
    display: flex;
    flex-wrap: wrap;
    gap: 6px;
    margin-top: 4px;
}

.system-tag {
    display: inline-block;
    padding: 2px 8px;
    border-radius: 50px;
    font-size: 12px;
    background-color: rgba(79, 70, 229, 0.1);
    color: #4f46e5;
}

/* Footer Actions */
.footer-actions {
    margin-top: 24px;
    display: flex;
    justify-content: center;
}

.action-button {
    padding: 10px 20px;
    border-radius: 50px;
    font-size: 14px;
    font-weight: 500;
    border: none;
    cursor: pointer;
    transition: all 0.2s ease;
}

.edit-button {
    background: linear-gradient(135deg, #3b82f6, #2563eb);
    color: white;
    box-shadow: 0 4px 10px rgba(59, 130, 246, 0.3);
}

.edit-button:hover {
    transform: translateY(-2px);
    box-shadow: 0 6px 15px rgba(59, 130, 246, 0.4);
}
</style>


<style>
/* ลบพื้นหลังของตัวเลขใน tab */
.tab span.w-6.h-6.flex.items-center.justify-center.bg-gray-200 {
    background-color: transparent !important;
}

.tab span.w-6.h-6 {
    background-color: transparent !important;
}

/* กรณีที่ใช้คลาสอื่นสำหรับ active หรือ completed */
.tab.active span.w-6.h-6,
.tab.completed span.w-6.h-6 {
    background-color: transparent !important;
}
</style>

<style>
.permission-badge {
    position: relative;
    display: inline-flex;
    align-items: center;
    padding: 4px 8px;
    border-radius: 12px;
    font-size: 11px;
    font-weight: 500;
}

.permission-inherited {
    background: linear-gradient(135deg, #dbeafe 0%, #bfdbfe 100%);
    color: #1e40af;
    border: 1px solid #93c5fd;
}

.permission-direct {
    background: linear-gradient(135deg, #dcfce7 0%, #bbf7d0 100%);
    color: #166534;
    border: 1px solid #86efac;
}

.permission-override {
    background: linear-gradient(135deg, #fed7aa 0%, #fdba74 100%);
    color: #9a3412;
    border: 1px solid #fb923c;
}

.permission-mixed {
    background: linear-gradient(135deg, #f3e8ff 0%, #e9d5ff 100%);
    color: #7c2d12;
    border: 1px solid #c4b5fd;
}

.permission-indicator {
    position: absolute;
    top: -2px;
    right: -2px;
    width: 8px;
    height: 8px;
    border-radius: 50%;
    border: 1px solid white;
}

.inherited-indicator { background: #3b82f6; }
.direct-indicator { background: #10b981; }
.override-indicator { background: #f59e0b; }
.mixed-indicator { background: #8b5cf6; }
</style>
