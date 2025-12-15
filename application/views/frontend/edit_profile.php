<div class="text-center pages-head">
    <span class="font-pages-head">แก้ไขข้อมูลส่วนตัว</span>
</div>
<img src="<?php echo base_url('docs/welcome-btm-light-other.png'); ?>">

<div class="bg-pages" style="margin-top:150px;" >
    <div class="container py-5">
        <div class="row justify-content-center">
            <div class="col-md-10">
                <div class="card premium-card shadow-lg">
                    <div class="profile-header bg-gradient-primary text-white">
                        <div class="header-content">
                            <div class="profile-avatar">
                                <?php if ($user->mp_img): ?>
                                    <img src="<?= base_url('docs/img/' . $user->mp_img); ?>" alt="Profile" class="avatar-img">
                                <?php else: ?>
                                    <i class="fas fa-user-circle avatar-icon"></i>
                                <?php endif; ?>
                            </div>
                            <h3 class="profile-name"><?= $user->mp_fname . ' ' . $user->mp_lname ?></h3>
                            <p class="profile-subtitle">สมาชิก</p>
                        </div>
                    </div>

                    <div class="card-body p-4">
                        <form action="<?= base_url('Pages/update_profile'); ?>" method="post" class="profile-form" enctype="multipart/form-data">
                            <div class="row g-4">
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label class="form-label">ชื่อ</label>
                                        <div class="input-group">
                                            <span class="input-group-text"><i class="fas fa-user"></i></span>
                                            <input type="text" name="mp_fname" class="form-control form-control-lg" value="<?= $user->mp_fname ?>">
                                        </div>
                                        <?= form_error('mp_fname', '<div class="text-danger">', '</div>'); ?>
                                    </div>
                                </div>

                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label class="form-label">นามสกุล</label>
                                        <div class="input-group">
                                            <span class="input-group-text"><i class="fas fa-user"></i></span>
                                            <input type="text" name="mp_lname" class="form-control form-control-lg" value="<?= $user->mp_lname ?>">
                                        </div>
                                        <?= form_error('mp_lname', '<div class="text-danger">', '</div>'); ?>
                                    </div>
                                </div>
                                
                                <div class="col-12">
                                    <div class="form-group ">
                                        <label class="form-label">เลขบัตรประชาชน</label>
                                        <div class="input-group">
                                            <span class="input-group-text"><i class="fas fa-id-card"></i></span>
                                            <input type="text" name="mp_number" class="form-control form-control-lg" value="<?= $user->mp_number ?>">
                                        </div>
                                        <?= form_error('mp_number', '<div class="text-danger">', '</div>'); ?>
                                    </div>
                                </div>

                              <!--   <div class="col-12">
                                    <div class="form-group readonly-group">
                                        <label class="form-label">เลขบัตรประชาชน</label>
                                        <div class="input-group">
                                            <span class="input-group-text"><i class="fas fa-id-card"></i></span>
                                            <input type="text" class="form-control form-control-lg" value="<?= $user->mp_number ?>" readonly>
                                        </div>
                                    </div>
                                </div> -->

                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label class="form-label">เบอร์โทรศัพท์</label>
                                        <div class="input-group">
                                            <span class="input-group-text"><i class="fas fa-phone"></i></span>
                                            <input type="tel" name="mp_phone" class="form-control form-control-lg" value="<?= $user->mp_phone ?>">
                                        </div>
                                        <?= form_error('mp_phone', '<div class="text-danger">', '</div>'); ?>
                                    </div>
                                </div>

                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label class="form-label">อีเมล</label>
                                        <div class="input-group">
                                            <span class="input-group-text"><i class="fas fa-envelope"></i></span>
                                            <input type="email" name="mp_email" class="form-control form-control-lg" value="<?= $user->mp_email ?>">
                                        </div>
                                        <?= form_error('mp_email', '<div class="text-danger">', '</div>'); ?>
                                    </div>
                                </div>

                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label class="form-label">รหัสผ่านใหม่ (เว้นว่างถ้าไม่ต้องการเปลี่ยน)</label>
                                        <div class="input-group">
                                            <span class="input-group-text"><i class="fas fa-lock"></i></span>
                                            <input type="password" name="mp_password" class="form-control form-control-lg">
                                        </div>
                                    </div>
                                </div>

                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label class="form-label">ยืนยันรหัสผ่านใหม่</label>
                                        <div class="input-group">
                                            <span class="input-group-text"><i class="fas fa-lock"></i></span>
                                            <input type="password" name="mp_password_confirm" class="form-control form-control-lg">
                                        </div>
                                    </div>
                                </div>

                                <div class="col-12">
                                    <div class="form-group">
                                        <label class="form-label">ที่อยู่</label>
                                        <div class="input-group">
                                            <span class="input-group-text"><i class="fas fa-home"></i></span>
                                            <textarea name="mp_address" class="form-control form-control-lg" rows="3"><?= $user->mp_address ?></textarea>
                                        </div>
                                    </div>
                                </div>

                                <div class="col-12">
                                    <div class="form-group">
                                        <label class="form-label">รูปโปรไฟล์</label>
                                        <div class="input-group">
                                            <span class="input-group-text"><i class="fas fa-image"></i></span>
                                            <input type="file" name="mp_img" class="form-control form-control-lg" accept="image/*">
                                        </div>
                                        <small class="text-muted">อัพโหลดรูปภาพขนาดไม่เกิน 2MB (jpg, jpeg, png)</small>
                                    </div>
                                </div>

                                <div class="col-12 text-center mt-4">
                                    <button type="submit" class="btn btn-primary btn-lg px-5 me-3">
                                        <i class="fas fa-save me-2"></i>บันทึกข้อมูล
                                    </button>
                                    <a href="<?= base_url('Pages/my_tax_payments'); ?>" class="btn btn-outline-secondary btn-lg px-5">
                                        <i class="fas fa-arrow-left me-2"></i>ย้อนกลับ
                                    </a>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<?php if ($this->session->flashdata('save_success')) : ?>
<script>
    $(document).ready(function() {
        <?php if ($this->session->flashdata('save_success')) { ?>
            Swal.fire({
                // position: 'top-end',
                icon: 'success',
                title: 'บันทึกข้อมูลสำเร็จ',
                showConfirmButton: false,
                timer: 1500
            })
        <?php } ?>
    });
</script>
<?php endif; ?>

<style>
    .premium-card {
        border: none;
        border-radius: 20px;
        overflow: hidden;
        background: linear-gradient(145deg, #ffffff 0%, #f8f9fa 100%);
    }

    .profile-header {
        background: linear-gradient(135deg, #1976d2 0%, #1565c0 100%);
        padding: 3rem 2rem;
        text-align: center;
        position: relative;
    }

    .profile-header::before {
        content: '';
        position: absolute;
        top: 0;
        left: 0;
        right: 0;
        bottom: 0;
        background: url('data:image/svg+xml,<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 1440 320"><path fill="rgba(255,255,255,0.1)" fill-opacity="1" d="M0,96L48,112C96,128,192,160,288,186.7C384,213,480,235,576,213.3C672,192,768,128,864,128C960,128,1056,192,1152,208C1248,224,1344,192,1392,176L1440,160L1440,320L1392,320C1344,320,1248,320,1152,320C1056,320,960,320,864,320C768,320,672,320,576,320C480,320,384,320,288,320C192,320,96,320,48,320L0,320Z"></path></svg>');
        background-position: bottom;
        background-repeat: no-repeat;
        opacity: 0.3;
    }

    .profile-avatar {
        width: 150px;
        height: 150px;
        margin: 0 auto 1.5rem;
        border-radius: 50%;
        overflow: hidden;
        border: 5px solid rgba(255, 255, 255, 0.2);
        box-shadow: 0 0 20px rgba(0, 0, 0, 0.2);
    }

    .avatar-img {
        width: 100%;
        height: 100%;
        object-fit: cover;
    }

    .avatar-icon {
        font-size: 9.5rem;
        color: rgba(255, 255, 255, 0.8);
    }

    .profile-name {
        font-size: 2rem;
        font-weight: 600;
        margin-bottom: 0.5rem;
        text-shadow: 2px 2px 4px rgba(0, 0, 0, 0.2);
    }

    .profile-subtitle {
        font-size: 1.1rem;
        opacity: 0.9;
    }

    .form-group {
        margin-bottom: 1.5rem;
    }

    .form-label {
        font-weight: 600;
        color: #2c3e50;
        margin-bottom: 0.5rem;
    }

    .input-group {
        box-shadow: 0 2px 6px rgba(0, 0, 0, 0.05);
        border-radius: 12px;
        overflow: hidden;
    }

    .input-group-text {
        background: #f8f9fa;
        border: none;
        color: #1976d2;
        padding: 0.75rem 1.25rem;
    }

    .form-control {
        border: none;
        padding: 0.75rem 1.25rem;
        font-size: 1rem;
        transition: all 0.3s ease;
    }

    .form-control:focus {
        box-shadow: none;
        background: #fff;
    }

    .readonly-group .form-control {
        background: #f8f9fa;
        color: #6c757d;
    }

    .btn {
        border-radius: 12px;
        padding: 0.75rem 2rem;
        font-weight: 600;
        text-transform: uppercase;
        letter-spacing: 0.5px;
        transition: all 0.3s ease;
    }

    .btn-primary {
        background: linear-gradient(135deg, #1976d2 0%, #1565c0 100%);
        border: none;
        box-shadow: 0 4px 15px rgba(25, 118, 210, 0.3);
    }

    .btn-primary:hover {
        transform: translateY(-2px);
        box-shadow: 0 6px 20px rgba(25, 118, 210, 0.4);
    }

    .btn-outline-secondary {
        border: 2px solid #6c757d;
    }

    .btn-outline-secondary:hover {
        background: #6c757d;
        color: white;
        transform: translateY(-2px);
    }

    /* Animations */
    @keyframes fadeInUp {
        from {
            opacity: 0;
            transform: translateY(20px);
        }

        to {
            opacity: 1;
            transform: translateY(0);
        }
    }

    .form-group {
        animation: fadeInUp 0.5s ease forwards;
        opacity: 0;
    }

    .form-group:nth-child(1) {
        animation-delay: 0.1s;
    }

    .form-group:nth-child(2) {
        animation-delay: 0.2s;
    }

    .form-group:nth-child(3) {
        animation-delay: 0.3s;
    }

    .form-group:nth-child(4) {
        animation-delay: 0.4s;
    }

    .form-group:nth-child(5) {
        animation-delay: 0.5s;
    }
	
	.footer-other,
	.bg-link-other {
		margin-top: 1250px;
	}
</style>