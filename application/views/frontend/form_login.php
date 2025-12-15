<div class="text-center pages-head">
    <span class="font-pages-head">เข้าสู่ระบบ</span>
</div>
</div>
<img src="<?php echo base_url('docs/welcome-btm-light-other.png'); ?>">
<div class="bg-pages">
    <div class="container-pages-news" style="position: relative; z-index: 10;">
        <div class="underline">
            <form id="reCAPTCHA3" action="<?php echo site_url('Auth_public_mem/login'); ?>" method="post" class="form-horizontal" enctype="multipart/form-data">
                <br>
                <div class="login-container">
                    <div class="form-group">
                        <div class="col-sm-12 control-label font-e-service-complain">อีเมล <span class="red-font">*</span></div>
                        <div class="col-sm-12 mt-2">
                            <div class="input-icon">
                                <i class="bi bi-envelope"></i>
                                <input type="email" name="mp_email" class="form-control font-label-e-service-complain" required placeholder="example@youremail.com" value="<?php echo set_value('mp_email'); ?>">
                            </div>
                        </div>
                    </div>
                    <br>
                    <div class="form-group">
                        <div class="col-sm-12 control-label font-e-service-complain">รหัสผ่าน <span class="red-font">*</span></div>
                        <div class="col-sm-12 mt-2">
                            <div class="input-icon">
                                <i class="bi bi-lock"></i>
                                <input type="password" name="mp_password" class="form-control font-label-e-service-complain" required value="<?php echo set_value('mp_password'); ?>">
                            </div>
                        </div>
                    </div>
                </div>
                <div class="row mt-4">
                    <div class="col-6 font-e-service-primary">
                        <a href="<?php echo site_url('Auth_public_mem/register_form'); ?>" class="register-link">สมัครสมาชิก ที่นี่ !</a>
                    </div>
                    <div class="col-3">
                        <div class="d-flex justify-content-end">
                        </div>
                    </div>
                    <div class="col-3">
                        <div class="d-flex justify-content-end">
                            <!-- reCAPTCHA 3  หน้านี้มีเปลี่ยน 1 จุด นี่จุด 1 -->
                            <button
                                data-action='submit'
                                data-callback='onSubmit'
                                data-sitekey="<?php echo get_config_value('recaptcha'); ?>"
                                type="submit"
                                class="btn btn-primary login-btn">
                                <span class="text-xl">➜</span>
                                <span class="font-medium text-lg tracking-wide">เข้าสู่ระบบ</span>
                            </button>
                        </div>
                    </div>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- เพิ่ม Bootstrap Icons -->
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.8.0/font/bootstrap-icons.css">

<style>
:root {
    /* สีจากภาพที่ส่งมา */
    --white: #FFFFFF;
    --snow: #FFFAFA;
    --honeydew: #F0FFF0;
    --mintcream: #F5FFFA;
    --azure: #F0FFFF;
    --aliceblue: #F0F8FF;
    --ghostwhite: #F8F8FF;
    --whitesmoke: #F5F5F5;
    --seashell: #FFF5EE;
    --beige: #F5F5DC;
    --oldlace: #FDF5E6;
    --floralwhite: #FFFAF0;
    --ivory: #FFFFF0;
    --antiquewhite: #FAEBD7;
    --linen: #FAF0E6;
    --lavenderblush: #FFF0F5;
    --mistyrose: #FFE4E1;
    
    /* สีเพิ่มเติมสำหรับองค์ประกอบต่างๆ */
    --primary: #F0F8FF; /* AliceBlue */
    --primary-dark: #90A4AE;
    --secondary: #FFF0F5; /* LavenderBlush */
    --accent: #F5F5DC; /* Beige */
    --text-color: #455A64;
    --form-bg: #FFFFFF;
    --input-bg: #F5F5F5; /* WhiteSmoke */
    --border-color: #E0E0E0;
    --form-shadow: rgba(0, 0, 0, 0.05);
    --error: #FF5252;
}

body {
    font-family: 'Prompt', sans-serif;
    background-color: var(--whitesmoke);
    color: var(--text-color);
}

.bg-pages {
    background-color: var(--form-bg);
    border-radius: 10px;
    box-shadow: 0 5px 20px var(--form-shadow);
    padding: 2rem;
    margin-top: 1.5rem;
    margin-bottom: 2rem;
    border-top: 5px solid var(--primary);
}

.container-pages-news {
    max-width: 1200px;
    margin: 0 auto;
}

.font-pages-head {
    font-size: 2rem;
    font-weight: 500;
    color: var(--text-color);
    padding-bottom: 5px;
    border-bottom: 3px solid var(--secondary);
    display: inline-block;
}

.font-e-service-complain {
    font-weight: 500;
    color: var(--text-color);
}

.red-font {
    color: var(--error);
}

.login-container {
    max-width: 500px;
    margin: 0 auto;
    padding: 20px;
    background-color: var(--ghostwhite);
    border-radius: 8px;
    box-shadow: 0 2px 10px rgba(0, 0, 0, 0.03);
}

.input-icon {
    position: relative;
}

.input-icon i {
    position: absolute;
    left: 12px;
    top: 50%;
    transform: translateY(-50%);
    color: var(--primary-dark);
}

.input-icon input {
    padding-left: 35px;
}

.font-label-e-service-complain {
    border-radius: 6px;
    border: 1px solid var(--border-color);
    background-color: var(--input-bg);
    transition: all 0.3s;
}

.font-label-e-service-complain:focus, .font-label-e-service-complain:hover {
    border-color: var(--primary-dark);
    box-shadow: 0 0 0 3px rgba(144, 164, 174, 0.2);
    background-color: white;
}

.form-group {
    margin-bottom: 15px;
}

.btn-primary, .login-btn {
    background-color: var(--primary-dark);
    border-color: var(--primary-dark);
    color: white;
    font-weight: 500;
    padding: 10px 20px;
    border-radius: 6px;
    transition: all 0.3s;
    box-shadow: 0 2px 5px rgba(0, 0, 0, 0.1);
}

.btn-primary:hover, .login-btn:hover {
    background-color: #607D8B;
    border-color: #607D8B;
    transform: translateY(-2px);
    box-shadow: 0 4px 8px rgba(0, 0, 0, 0.15);
}

.underline {
    padding-bottom: 2rem;
    border-bottom: 1px dashed var(--border-color);
    margin-bottom: 1rem;
}

.register-link {
    color: var(--primary-dark);
    text-decoration: none;
    font-weight: 500;
    display: inline-block;
    padding: 6px 12px;
    background-color: var(--primary);
    border-radius: 5px;
    transition: all 0.3s;
}

.register-link:hover {
    color: white;
    background-color: var(--primary-dark);
    text-decoration: none;
}

.red {
    color: var(--error);
    font-size: 0.85rem;
    margin-top: 5px;
    display: block;
}

.mt-2 {
    margin-top: 0.5rem;
}

.mt-4 {
    margin-top: 1.5rem;
}

@media (max-width: 768px) {
    .col-6, .col-3 {
        width: 100%;
        margin-bottom: 1rem;
    }
    
    .row {
        display: block;
    }
    
    .login-container {
        padding: 15px;
    }
}
</style>