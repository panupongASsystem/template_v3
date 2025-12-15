<!DOCTYPE html>
<html lang="th">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>การเข้าถึงสำหรับเจ้าหน้าที่</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
</head>
<body>

<script>
document.addEventListener('DOMContentLoaded', function() {
    // แสดง SweetAlert2 Modal
    Swal.fire({
        title: '<span style="color: #ff9800;">เข้าถึงสำหรับเจ้าหน้าที่</span>',
        html: `
            <div style="text-align: center; margin-bottom: 1.5rem;">
                <div style="width: 80px; height: 80px; margin: 0 auto 1rem; background: linear-gradient(135deg, rgba(255, 152, 0, 0.15) 0%, rgba(255, 152, 0, 0.25) 100%); border-radius: 50%; display: flex; align-items: center; justify-content: center; box-shadow: 0 8px 25px rgba(255, 152, 0, 0.3);">
                    <i class="fas fa-user-tie" style="font-size: 2.5rem; color: #ff9800;"></i>
                </div>
                <h4 style="color: #ff9800; font-weight: 600; margin-bottom: 1rem;">
                    คุณเข้าสู่ระบบในฐานะเจ้าหน้าที่
                </h4>
                <div style="background: linear-gradient(135deg, rgba(255, 152, 0, 0.1) 0%, rgba(255, 152, 0, 0.05) 100%); 
                           padding: 1.5rem; border-radius: 15px; margin: 1rem 0;
                           border: 1px solid rgba(255, 152, 0, 0.2);">
                    <p style="color: #e65100; font-size: 1.05rem; line-height: 1.6; margin: 0;">
                        <i class="fas fa-info-circle me-2" style="color: #ff9800;"></i>
                        หน้านี้สำหรับประชาชนทั่วไปเท่านั้น<br>
                        กรุณาใช้ระบบจัดการภายในสำหรับเจ้าหน้าที่
                    </p>
                </div>
                
                <div style="text-align: left; margin: 1.5rem 0;">
                    <h6 style="color: #ff9800; font-weight: 600; margin-bottom: 1rem;">
                        <i class="fas fa-arrow-right me-2"></i>เลือกปลายทางที่ต้องการ:
                    </h6>
                    <div class="d-grid gap-3">
                        <button type="button" 
                                onclick="goToSmartOffice()" 
                                class="btn btn-warning btn-lg" 
                                style="background: linear-gradient(135deg, #ff9800 0%, #f57c00 100%); 
                                       border: none; color: white; border-radius: 15px; 
                                       padding: 1rem 1.5rem; font-weight: 600; 
                                       box-shadow: 0 6px 20px rgba(255, 152, 0, 0.3);">
                            <i class="fas fa-building me-2"></i>ไปยังสมาร์ทออฟฟิศ
                        </button>
                        <button type="button" 
                                onclick="goToHomepage()" 
                                class="btn btn-outline-warning btn-lg" 
                                style="border: 2px solid #ff9800; color: #ff9800; 
                                       border-radius: 15px; padding: 1rem 1.5rem; 
                                       font-weight: 600;">
                            <i class="fas fa-home me-2"></i>กลับหน้าหลัก
                        </button>
                    </div>
                </div>
            </div>
        `,
        showConfirmButton: false,
        showCancelButton: false,
        allowOutsideClick: false,
        allowEscapeKey: false,
        customClass: {
            popup: 'staff-warning-modal',
            content: 'staff-warning-content'
        },
        width: '500px'
    });
});

// ฟังก์ชันไปยังสมาร์ทออฟฟิศ
function goToSmartOffice() {
    // แสดง loading
    Swal.fire({
        title: 'กำลังนำทางไปยังสมาร์ทออฟฟิศ...',
        html: '<div style="text-align: center;"><i class="fas fa-spinner fa-spin" style="font-size: 2rem; color: #ff9800;"></i></div>',
        allowOutsideClick: false,
        allowEscapeKey: false,
        showConfirmButton: false,
        timer: 1500
    }).then(() => {
        // Redirect ไปยังสมาร์ทออฟฟิศ - ปรับ URL ตามระบบของคุณ
        window.location.href = '<?= site_url('User/choice'); ?>'; // หรือ URL ของสมาร์ทออฟฟิศ
    });
}

// ฟังก์ชันกลับหน้าหลัก
function goToHomepage() {
    // แสดง loading
    Swal.fire({
        title: 'กำลังนำทางไปยังหน้าหลัก...',
        html: '<div style="text-align: center;"><i class="fas fa-spinner fa-spin" style="font-size: 2rem; color: #ff9800;"></i></div>',
        allowOutsideClick: false,
        allowEscapeKey: false,
        showConfirmButton: false,
        timer: 1500
    }).then(() => {
        // Redirect ไปยังหน้าหลัก
        window.location.href = '<?= site_url('Home'); ?>'; // หน้าหลักของเว็บไซต์
    });
}
</script>

<style>
.staff-warning-modal {
    border-radius: 20px !important;
    box-shadow: 0 20px 60px rgba(255, 152, 0, 0.2), 0 8px 25px rgba(0,0,0,0.08) !important;
    background: linear-gradient(135deg, #ffffff 0%, #fff8e1 100%) !important;
    overflow: hidden !important;
}

.staff-warning-content {
    border-radius: 20px !important;
}

.btn:hover {
    transform: translateY(-2px);
    transition: all 0.3s ease;
}

.btn-warning:hover {
    box-shadow: 0 8px 25px rgba(255, 152, 0, 0.4) !important;
    background: linear-gradient(135deg, #f57c00 0%, #ef6c00 100%) !important;
}

.btn-outline-warning:hover {
    background: #ff9800 !important;
    color: white !important;
    transform: translateY(-2px);
    box-shadow: 0 6px 20px rgba(255, 152, 0, 0.3) !important;
}
</style>

</body>
</html>