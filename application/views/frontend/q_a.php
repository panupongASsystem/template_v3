<?php
// *** แก้ไข: ตรวจสอบสถานะการเข้าสู่ระบบและแสดงข้อมูลผู้ใช้ (CONSISTENT VERSION) ***
$is_logged_in = false;
$user_info = [];
$user_type = '';

// *** ฟังก์ชันแก้ไข user_id overflow (CONSISTENT VERSION) ***
function fixUserIdOverflow($session_id, $email)
{
    // แก้ไขปัญหา INT overflow (2147483647) และใช้ auto increment id เสมอ
    $CI =& get_instance();

    // *** เปลี่ยน: ใช้ auto increment id เสมอสำหรับความ consistent ***

    // ตรวจสอบใน tbl_member_public ก่อน
    $public_user = $CI->db->select('id, mp_id')
        ->where('mp_email', $email)
        ->get('tbl_member_public')
        ->row();

    if ($public_user) {
        // *** ใช้ auto increment id เสมอ ***
        log_message('info', "Using consistent auto increment ID: {$public_user->id} for email: {$email} (original mp_id: {$session_id})");
        return $public_user->id; // ใช้ auto increment id แทน mp_id เสมอ
    }

    // ถ้าไม่เจอ ตรวจสอบใน tbl_member
    $staff_user = $CI->db->select('m_id')
        ->where('m_email', $email)
        ->get('tbl_member')
        ->row();

    if ($staff_user) {
        log_message('info', "Using staff m_id: {$staff_user->m_id} for email: {$email}");
        return $staff_user->m_id;
    }

    // ถ้าไม่เจออะไรเลย return null
    log_message('error', "Could not find user for email: {$email}");
    return null;
}

// ตรวจสอบผู้ใช้ประชาชน (Public User)
if ($this->session->userdata('mp_id')) {
    $is_logged_in = true;
    $user_type = 'public';

    // *** แก้ไข: ใช้ auto increment id เสมอ ***
    $session_mp_id = $this->session->userdata('mp_id');
    $user_email = $this->session->userdata('mp_email');
    $fixed_user_id = fixUserIdOverflow($session_mp_id, $user_email);

    $user_info = [
        'id' => $this->session->userdata('mp_id'),
        'user_id' => $fixed_user_id, // *** ใช้ auto increment ID เสมอ ***
        'name' => trim($this->session->userdata('mp_fname') . ' ' . $this->session->userdata('mp_lname')),
        'email' => $user_email,
        'img' => $this->session->userdata('mp_img'),
        'login_type' => 'ประชาชน',
        'user_type' => 'public',
        'type' => 'ประชาชน'
    ];

    // *** Log การแก้ไข user_id สำหรับ debug ***
    log_message('info', "Public user login: mp_id={$session_mp_id}, fixed_user_id={$fixed_user_id}, email={$user_email}");
}
// ตรวจสอบเจ้าหน้าที่ (Staff User)
elseif ($this->session->userdata('m_id')) {
    $is_logged_in = true;
    $user_type = 'staff';

    // *** ตรวจสอบระดับผู้ใช้ ***
    $m_system = $this->session->userdata('m_system');
    $actual_user_type = 'staff';

    if ($m_system) {
        switch ($m_system) {
            case 'system_admin':
                $actual_user_type = 'system_admin';
                break;
            case 'super_admin':
                $actual_user_type = 'super_admin';
                break;
            case 'user_admin':
                $actual_user_type = 'user_admin';
                break;
            default:
                $actual_user_type = 'staff';
        }
    }

    // *** แก้ไข: ดึง user_id สำหรับ staff ***
    $session_m_id = $this->session->userdata('m_id');
    $user_email = $this->session->userdata('m_email'); // *** เปลี่ยนจาก m_email เป็น staff email field ที่ถูกต้อง ***

    // *** แก้ไข: ตรวจสอบว่า email มีค่าหรือไม่ ถ้าไม่มีให้ดึงจากฐานข้อมูล ***
    if (empty($user_email)) {
        $CI =& get_instance();
        $staff_data = $CI->db->select('m_email')
            ->where('m_id', $session_m_id)
            ->get('tbl_member')
            ->row();

        if ($staff_data && !empty($staff_data->m_email)) {
            $user_email = $staff_data->m_email;
            log_message('info', "Retrieved staff email from database: {$user_email}");
        } else {
            log_message('warning', "No email found for staff ID: {$session_m_id}");
        }
    }

    $fixed_user_id = fixUserIdOverflow($session_m_id, $user_email);

    $user_info = [
        'id' => $session_m_id,
        'user_id' => $fixed_user_id, // *** ใช้ ID ที่แก้ไขแล้ว ***
        'name' => trim($this->session->userdata('m_fname') . ' ' . $this->session->userdata('m_lname')),
        'username' => $this->session->userdata('m_username'),
        'email' => $user_email, // *** แก้ไข: ใช้ email ที่ดึงมาแล้ว ***
        'img' => $this->session->userdata('m_img'),
        'level' => $this->session->userdata('m_level'),
        'login_type' => 'เจ้าหน้าที่',
        'user_type' => $actual_user_type,
        'type' => 'เจ้าหน้าที่'
    ];

    // *** Log การแก้ไข user_id สำหรับ debug ***
    log_message('info', "Staff user login: m_id={$session_m_id}, fixed_user_id={$fixed_user_id}, email={$user_email}");
}

// ฟังก์ชันแสดง Badge สถานะผู้ใช้ (แก้ไขแล้ว)
function getUserTypeBadge($userType)
{
    if ($userType == 'public') {
        return '<span class="badge bg-success ms-2"><i class="fas fa-user me-1"></i>สมาชิก</span>';
    } elseif (in_array($userType, ['staff', 'system_admin', 'super_admin', 'user_admin'])) {
        // *** แยก badge ตามระดับ staff ***
        switch ($userType) {
            case 'system_admin':
                return '<span class="badge bg-danger ms-2"><i class="fas fa-user-cog me-1"></i>ผู้ดูแลระบบ</span>';
            case 'super_admin':
                return '<span class="badge bg-warning ms-2"><i class="fas fa-user-crown me-1"></i>ผู้ดูแล</span>';
            case 'user_admin':
                return '<span class="badge bg-info ms-2"><i class="fas fa-user-tie me-1"></i>เจ้าหน้าที่</span>';
            default:
                return '<span class="badge bg-primary ms-2"><i class="fas fa-user-shield me-1"></i>เจ้าหน้าที่</span>';
        }
    } else {
        // กรณีเป็น 'guest', null, หรือค่าอื่นๆ
        return '<span class="badge bg-secondary ms-2"><i class="fas fa-user-alt me-1"></i>ผู้เยี่ยมชม</span>';
    }
}
?>

<!-- Modal ตั้งกระทู้ -->
<div class="modal fade" id="guestConfirmModal" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content"
            style="border: none; border-radius: 20px; box-shadow: 0 20px 60px rgba(173, 216, 230, 0.2); background: linear-gradient(135deg, #ffffff 0%, #f0f8ff 100%);">
            <div class="modal-header"
                style="background: linear-gradient(135deg, rgba(173, 216, 230, 0.1) 0%, rgba(135, 206, 250, 0.1) 100%); border-radius: 20px 20px 0 0; border-bottom: 1px solid rgba(173, 216, 230, 0.2);">
                <h5 class="modal-title w-100 text-center" style="color: #4682b4; font-weight: 600;">
                    <i class="fas fa-sparkles me-2" style="color: #87ceeb;"></i>ยินดีต้อนรับสู่กระทู้ถาม-ตอบ
                </h5>
            </div>
            <div class="modal-body text-center"
                style="padding: 2.5rem; background: linear-gradient(135deg, #ffffff 0%, #f0f8ff 100%);">
                <div class="mb-4">
                    <div
                        style="width: 80px; height: 80px; margin: 0 auto; background: linear-gradient(135deg, rgba(173, 216, 230, 0.15) 0%, rgba(135, 206, 250, 0.15) 100%); border-radius: 50%; display: flex; align-items: center; justify-content: center; box-shadow: 0 8px 25px rgba(173, 216, 230, 0.3);">
                        <i class="fas fa-user-circle" style="font-size: 2.5rem; color: #4682b4;"></i>
                    </div>
                </div>
                <h5 class="mb-3" style="color: #2c3e50; font-weight: 600;">เริ่มต้นการใช้งาน</h5>
                <p class="text-muted mb-4">ตอบกระทู้ได้ทันทีโดยไม่ต้องลงทะเบียน หรือเข้าสู่ระบบสำหรับความสะดวกมากขึ้น
                </p>

                <div class="d-grid gap-3">
                    <button type="button" class="btn btn-lg" onclick="redirectToLogin()"
                        style="background: linear-gradient(135deg, #87ceeb 0%, #4682b4 100%); border: none; color: white; border-radius: 15px; padding: 1rem 1.5rem; font-weight: 600; box-shadow: 0 6px 20px rgba(135, 206, 250, 0.4);">
                        <i class="fas fa-sign-in-alt me-2"></i>เข้าสู่ระบบ
                    </button>
                    <button type="button" class="btn btn-lg" onclick="proceedAsGuest()"
                        style="background: rgba(173, 216, 230, 0.08); border: 2px solid rgba(173, 216, 230, 0.3); color: #4682b4; border-radius: 15px; padding: 1rem 1.5rem; font-weight: 600;">
                        <i class="fas fa-edit me-2"></i>ดำเนินการต่อโดยไม่เข้าสู่ระบบ
                    </button>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Modal ตอบกระทู้ -->
<div class="modal fade" id="guestReplyModal" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content"
            style="border: none; border-radius: 20px; box-shadow: 0 20px 60px rgba(40, 167, 69, 0.2); background: linear-gradient(135deg, #ffffff 0%, #f0fff4 100%);">
            <div class="modal-header"
                style="background: linear-gradient(135deg, rgba(40, 167, 69, 0.1) 0%, rgba(32, 201, 151, 0.1) 100%); border-radius: 20px 20px 0 0; border-bottom: 1px solid rgba(40, 167, 69, 0.2);">
                <h5 class="modal-title w-100 text-center" style="color: #28a745; font-weight: 600;">
                    <i class="fas fa-reply me-2" style="color: #20c997;"></i>ตอบกระทู้
                </h5>
            </div>
            <div class="modal-body text-center" style="padding: 2.5rem;">
                <div class="mb-4">
                    <div
                        style="width: 80px; height: 80px; margin: 0 auto; background: linear-gradient(135deg, rgba(40, 167, 69, 0.15) 0%, rgba(32, 201, 151, 0.15) 100%); border-radius: 50%; display: flex; align-items: center; justify-content: center; box-shadow: 0 8px 25px rgba(40, 167, 69, 0.3);">
                        <i class="fas fa-comments" style="font-size: 2.5rem; color: #28a745;"></i>
                    </div>
                </div>
                <h5 class="mb-3" style="color: #2c3e50; font-weight: 600;">ท่านต้องการตอบกระทู้อย่างไร?</h5>
                <p class="text-muted mb-4">ท่านสามารถตอบกระทู้โดยไม่ต้องเข้าสู่ระบบ
                    หรือเข้าสู่ระบบเพื่อจัดการโพสต์และติดตามการตอบกลับ</p>

                <div class="d-grid gap-3">
                    <button type="button" class="btn btn-lg" onclick="redirectToLogin()"
                        style="background: linear-gradient(135deg, #28a745 0%, #20c997 100%); border: none; color: white; border-radius: 15px; padding: 1rem 1.5rem; font-weight: 600; box-shadow: 0 6px 20px rgba(40, 167, 69, 0.4);">
                        <i class="fas fa-sign-in-alt me-2"></i>เข้าสู่ระบบก่อน
                    </button>
                    <button type="button" class="btn btn-lg" onclick="proceedReplyAsGuest()"
                        style="background: rgba(40, 167, 69, 0.08); border: 2px solid rgba(40, 167, 69, 0.3); color: #28a745; border-radius: 15px; padding: 1rem 1.5rem; font-weight: 600;">
                        <i class="fas fa-reply me-2"></i>ตอบกระทู้โดยไม่เข้าสู่ระบบ
                    </button>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="text-center pages-head">
    <span class="font-pages-head">กระทู้ถาม - ตอบ</span>
</div>
</div>
<img src="<?php echo base_url('docs/welcome-btm-light-other.png'); ?>">

<div class="bg-pages" style="background: #ffffff; min-height: 100vh; padding: 2rem 0;">
    <div class="container-pages-news" style="position: relative; z-index: 10;">

        <?php
        $count = count($query);
        $itemsPerPage = 5;
        $totalPages = ceil($count / $itemsPerPage);
        $currentPage = isset($_GET['page']) ? $_GET['page'] : 1;
        $startIndex = ($currentPage - 1) * $itemsPerPage;
        $endIndex = min($startIndex + $itemsPerPage - 1, $count - 1);
        $Index = $count - $startIndex;
        ?>

        <!-- Header กระทู้ + ปุ่มเพิ่มกระทู้ -->
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h3 style="color: #495057; font-weight: 600; margin: 0;">
                <i class="fas fa-list me-2" style="color: #6c757d;"></i>รายการกระทู้ทั้งหมด
            </h3>
            <button type="button" class="btn" onclick="handleAddNewTopic()"
                style="background: linear-gradient(135deg, #4682b4 0%, #87ceeb 100%); border: none; color: white; border-radius: 15px; padding: 0.8rem 1.5rem; font-weight: 600; box-shadow: 0 4px 15px rgba(70, 130, 180, 0.3); transition: all 0.3s ease;">
                <i class="fas fa-plus me-2"></i>เพิ่มกระทู้ใหม่
            </button>
        </div>

        <?php
        for ($i = $startIndex; $i <= $endIndex; $i++) {
            $rs = $query[$i];
            if (isset($query) && !empty($query)): ?>
                <div class="card-q-a" id="comment-<?= $rs->q_a_id; ?>"
                    style="margin-bottom: 2rem; border-radius: 20px; box-shadow: 0 8px 25px rgba(108, 117, 125, 0.1); border: none; overflow: hidden; transition: all 0.3s ease;">

                    <!-- แสดงเนื้อหากระทู้ปกติ -->
                    <div class="topic-content-<?= $rs->q_a_id; ?>">
                        <div class="card-header"
                            style="background: linear-gradient(135deg, rgba(108, 117, 125, 0.08) 0%, rgba(134, 142, 150, 0.08) 100%); padding: 1.5rem; border-bottom: 1px solid rgba(108, 117, 125, 0.1);">
                            <span style="font-size: 1.2rem; font-weight: 600; color: #495057;">
                                <i class="fas fa-comments me-2" style="color: #6c757d;"></i>
                                ความคิดเห็นที่ <?= $Index; ?> - <?= $rs->q_a_msg; ?>
                            </span>
                        </div>
                        <div class="card-body"
                            style="padding: 2rem; background: linear-gradient(135deg, #ffffff 0%, #f8f9fa 100%);">
                            <div class="mb-3">
                                <span style="color: #2c3e50; line-height: 1.6;"><?= $rs->q_a_detail; ?></span>
                                <?php
                                $images = !empty($rs->additional_images) ? explode(',', $rs->additional_images) : [];
                                $images = array_filter($images);
                                if (!empty($images)) {
                                    echo '<br><br>';
                                    foreach ($images as $img) { ?>
                                        <a href="<?= base_url('docs/img/' . $img); ?>"
                                            data-lightbox="image-<?= $rs->additional_images; ?>">
                                            <img src="<?= base_url('docs/img/' . $img); ?>" width="120px" height="100px"
                                                style="border-radius: 10px; margin: 5px; box-shadow: 0 4px 10px rgba(108, 117, 125, 0.2);">
                                        </a>
                                    <?php }
                                }
                                ?>
                            </div>
                            <hr style="border-color: rgba(108, 117, 125, 0.2);">
                            <div class="row">
                                <div class="col-sm-8">
                                    <small style="color: #6c757d;">
    <i class="fas fa-user me-1"></i>ผู้ตั้งกระทู้: <?= $rs->q_a_by; ?>
    <?= getUserTypeBadge($rs->q_a_user_type); ?>
    <br>
    <i class="fas fa-globe me-1"></i>
    <?= ($rs->q_a_ip) ? $rs->q_a_ip . ' (' . $rs->q_a_country . ')' : 'ไม่ระบุตัวตน'; ?>
    <br>
    <?php if(isset($rs->q_a_os) && isset($rs->q_a_browser)): ?>
    <i class="fas fa-desktop me-1"></i>
    <?= $rs->q_a_os; ?> - <?= $rs->q_a_browser; ?>
    <br>
    <?php endif; ?>
    <i class="fas fa-calendar me-1"></i><?= thai_date($rs->q_a_datesave); ?>
	<i class="fas fa-clock ms-2 me-1"></i><?= date('H:i', strtotime($rs->q_a_datesave)); ?> น.

    
</small>
                                </div>
                                <div class="col-sm-4">
                                    <div class="d-flex gap-2 justify-content-end flex-wrap">
                                        <!-- ปุ่มตอบกลับ -->
                                        <button class="btn" onclick="handleReplyClick(<?= $rs->q_a_id; ?>)"
                                            style="background: linear-gradient(135deg, #e8f5e8 0%, #d4edda 100%); border: 1px solid rgba(40, 167, 69, 0.3); color: #28a745; border-radius: 12px; padding: 0.5rem 1rem; font-weight: 500; box-shadow: 0 2px 8px rgba(40, 167, 69, 0.15); transition: all 0.3s ease; font-size: 0.9rem;">
                                            <i class="fa fa-reply me-1" style="font-size: 0.8rem;"></i>ตอบกลับ
                                        </button>

                                        <!-- ปุ่มแก้ไข/ลบ (แสดงตามเงื่อนไข) -->
                                        <div id="edit-delete-buttons-<?= $rs->q_a_id; ?>" class="edit-delete-buttons"
                                            data-topic-id="<?= $rs->q_a_id; ?>" data-user-type="<?= $rs->q_a_user_type; ?>"
                                            data-user-id="<?= isset($rs->q_a_user_id) ? $rs->q_a_user_id : ''; ?>"
                                            style="display: none;">
                                            <button class="btn btn-sm" onclick="editTopic(<?= $rs->q_a_id; ?>)"
                                                style="background: linear-gradient(135deg, #fff3cd 0%, #ffeaa7 100%); border: 1px solid rgba(255, 193, 7, 0.3); color: #856404; border-radius: 10px; padding: 0.4rem 0.8rem; font-weight: 500; transition: all 0.3s ease;">
                                                <i class="fas fa-edit me-1" style="font-size: 0.7rem;"></i>แก้ไข
                                            </button>
                                            <button class="btn btn-sm" onclick="deleteTopic(<?= $rs->q_a_id; ?>)"
                                                style="background: linear-gradient(135deg, #f8d7da 0%, #f5c6cb 100%); border: 1px solid rgba(220, 53, 69, 0.3); color: #721c24; border-radius: 10px; padding: 0.4rem 0.8rem; font-weight: 500; transition: all 0.3s ease;">
                                                <i class="fas fa-trash me-1" style="font-size: 0.7rem;"></i>ลบ
                                            </button>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- ฟอร์มแก้ไขกระทู้ (ซ่อนไว้) -->
                    <div class="edit-form-container-<?= $rs->q_a_id; ?>" style="display: none;">
                        <!-- ฟอร์มแก้ไขจะถูกใส่ที่นี่ด้วย JavaScript -->
                    </div>

                    <!-- แสดง Reply -->
                    <!-- ในส่วนแสดง Reply - ตรวจสอบว่ามีการ loop ซ้ำหรือไม่ -->
                    <div class="replies-section-<?= $rs->q_a_id; ?>">
                        <?php if (isset($rsReply[$rs->q_a_id]) && !empty($rsReply[$rs->q_a_id])): ?>
                            <?php foreach ($rsReply[$rs->q_a_id] as $reply): ?>
                                <!-- *** ลบส่วนที่ซ้ำออกและเก็บเฉพาะส่วนนี้ *** -->
                                <div class="mt-4 p-3 reply-item" id="reply-<?= $reply->q_a_reply_id; ?>"
                                    style="background: linear-gradient(135deg, rgba(108, 117, 125, 0.03) 0%, rgba(134, 142, 150, 0.03) 100%); border-radius: 15px; border-left: 4px solid #6c757d; margin: 0 2rem 0 2rem;">

                                    <!-- แสดงเนื้อหา Reply ปกติ -->
                                    <div class="reply-content-<?= $reply->q_a_reply_id; ?>">
                                        <div class="d-flex justify-content-between align-items-start mb-2">
                                            <div>
                                                <strong style="color: #495057;">
                                                    <i class="fas fa-user-check me-1"></i>ผู้ตอบ: <?= $reply->q_a_reply_by; ?>
                                                    <?= getUserTypeBadge($reply->q_a_reply_user_type); ?>
                                                </strong>
                                            </div>
                                            <!-- ปุ่มแก้ไข/ลบ Reply -->
                                            <div id="reply-edit-delete-buttons-<?= $reply->q_a_reply_id; ?>"
                                                class="reply-edit-delete-buttons" data-reply-id="<?= $reply->q_a_reply_id; ?>"
                                                data-user-type="<?= $reply->q_a_reply_user_type; ?>"
                                                data-user-id="<?= isset($reply->q_a_reply_user_id) ? $reply->q_a_reply_user_id : ''; ?>"
                                                style="display: none;">
                                                <button class="btn btn-sm me-1" onclick="editReply(<?= $reply->q_a_reply_id; ?>)"
                                                    style="background: linear-gradient(135deg, #fff3cd 0%, #ffeaa7 100%); border: 1px solid rgba(255, 193, 7, 0.3); color: #856404; border-radius: 8px; padding: 0.3rem 0.6rem; font-weight: 500; font-size: 0.75rem;">
                                                    <i class="fas fa-edit me-1" style="font-size: 0.6rem;"></i>แก้ไข
                                                </button>
                                                <button class="btn btn-sm" onclick="deleteReply(<?= $reply->q_a_reply_id; ?>)"
                                                    style="background: linear-gradient(135deg, #f8d7da 0%, #f5c6cb 100%); border: 1px solid rgba(220, 53, 69, 0.3); color: #721c24; border-radius: 8px; padding: 0.3rem 0.6rem; font-weight: 500; font-size: 0.75rem;">
                                                    <i class="fas fa-trash me-1" style="font-size: 0.6rem;"></i>ลบ
                                                </button>
                                            </div>
                                        </div>
                                        <div class="mb-3">
                                            <span style="color: #2c3e50;"><?= $reply->q_a_reply_detail; ?></span>
                                            <?php
                                            $images = !empty($reply->additional_images) ? explode(',', $reply->additional_images) : [];
                                            if (!empty($images)) {
                                                echo '<br><br>';
                                                foreach ($images as $img) { ?>
                                                    <a href="<?= base_url('docs/img/' . $img); ?>"
                                                        data-lightbox="reply-<?= $reply->q_a_reply_id; ?>">
                                                        <img src="<?= base_url('docs/img/' . $img); ?>" width="120px" height="100px"
                                                            style="border-radius: 10px; margin: 5px;">
                                                    </a>
                                                <?php }
                                            }
                                            ?>
                                        </div>
                                        
                                        <br>
                                        <small style="color: #6c757d;">
    <i class="fas fa-user me-1"></i>ผู้ตอบ: <?= $reply->q_a_reply_by; ?>
    <?= getUserTypeBadge($reply->q_a_reply_user_type); ?>
    <br>
    <i class="fas fa-globe me-1"></i>
    <?= ($reply->q_a_reply_ip) ? $reply->q_a_reply_ip . ' (' . $reply->q_a_reply_country . ')' : 'ไม่ระบุตัวตน'; ?>
    <br>
    <?php if(isset($reply->q_a_reply_os) && isset($reply->q_a_reply_browser)): ?>
        <i class="fas fa-desktop me-1"></i>
        <?= $reply->q_a_reply_os; ?> - <?= $reply->q_a_reply_browser; ?>
        <br>
    <?php endif; ?>
    <?php if (isset($reply->q_a_reply_datesave)): ?>
        <i class="fas fa-calendar me-1"></i><?= thai_date($reply->q_a_reply_datesave); ?>
        <i class="fas fa-clock ms-2 me-1"></i><?= date('H:i', strtotime($reply->q_a_reply_datesave)); ?> น.
    <?php endif; ?>
</small>
                                    </div>

                                    <!-- ฟอร์มแก้ไข Reply (ซ่อนไว้) -->
                                    <div class="reply-edit-form-container-<?= $reply->q_a_reply_id; ?>" style="display: none;">
                                        <!-- ฟอร์มแก้ไขจะถูกใส่ที่นี่ด้วย JavaScript -->
                                    </div>
                                </div>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </div>

                </div>

                <!-- Container สำหรับฟอร์ม Reply -->
                <div id="reply-container-<?= $rs->q_a_id; ?>" class="reply-container" style="display: none;"></div>
            <?php endif; ?>
            <?php $Index--; ?>
        <?php } ?>

        <!-- Pagination -->
        <div class="d-flex justify-content-between align-items-center my-4">
            <div>
                <?php if ($currentPage > 1): ?>
                    <a href="?page=<?= $currentPage - 1; ?>" class="btn btn-outline-secondary me-2">« ก่อนหน้า</a>
                <?php endif; ?>

                <span class="mx-3">หน้า <?= $currentPage; ?> จาก <?= $totalPages; ?></span>

                <?php if ($currentPage < $totalPages): ?>
                    <a href="?page=<?= $currentPage + 1; ?>" class="btn btn-outline-secondary ms-2">ถัดไป »</a>
                <?php endif; ?>
            </div>

            <form method="GET" class="d-flex">
                <input type="number" name="page" min="1" max="<?= $totalPages; ?>" value="<?= $currentPage; ?>"
                    class="form-control me-2" style="width: 80px;">
                <button type="submit" class="btn btn-secondary">ไป</button>
            </form>
        </div>
    </div>
</div>


<!-- เพิ่ม CSS สำหรับ Reply Edit Buttons -->
<style>
    .reply-edit-delete-buttons {
        opacity: 0;
        transition: opacity 0.3s ease;
    }

    .reply-item:hover .reply-edit-delete-buttons {
        opacity: 1;
    }

    .reply-edit-form-container {
        animation: slideDown 0.3s ease-out;
    }

    .form-label-sm {
        font-size: 0.8rem;
        margin-bottom: 0.25rem;
    }

    .form-control-sm {
        font-size: 0.875rem;
        padding: 0.375rem 0.75rem;
    }
</style>


<style>
    .form-label-wrapper {
        background: linear-gradient(135deg, rgba(108, 117, 125, 0.08) 0%, rgba(134, 142, 150, 0.08) 100%);
        border-radius: 12px;
        padding: 0.8rem 1.2rem;
        margin-bottom: 0.8rem;
        box-shadow: 0 4px 12px rgba(108, 117, 125, 0.15);
        transition: all 0.3s ease;
    }

    .form-label-wrapper:hover {
        background: linear-gradient(135deg, rgba(108, 117, 125, 0.12) 0%, rgba(134, 142, 150, 0.12) 100%);
        box-shadow: 0 6px 16px rgba(108, 117, 125, 0.2);
        transform: translateY(-2px);
    }

    .form-label {
        margin: 0;
        font-size: 1.1rem;
        font-weight: 600;
        color: #495057;
    }

    .form-control {
        border: none;
        border-radius: 15px;
        padding: 1rem;
        font-size: 1.1rem;
        box-shadow: 0 6px 20px rgba(108, 117, 125, 0.15);
        background: linear-gradient(135deg, #ffffff 0%, #f8f9fa 100%);
        transition: all 0.3s ease;
    }

    .form-control:focus {
        box-shadow: 0 8px 25px rgba(108, 117, 125, 0.25);
        transform: translateY(-1px);
        background: linear-gradient(135deg, #ffffff 0%, #f1f3f4 100%);
    }

    .modern-submit-btn {
        background: linear-gradient(135deg, #e9ecef 0%, #dee2e6 100%);
        border: none;
        color: #495057;
        padding: 1rem 2rem;
        border-radius: 15px;
        font-size: 1.1rem;
        font-weight: 600;
        box-shadow: 0 6px 20px rgba(108, 117, 125, 0.2);
        transition: all 0.3s ease;
    }

    .modern-submit-btn:hover {
        transform: translateY(-3px);
        box-shadow: 0 10px 30px rgba(108, 117, 125, 0.3);
        background: linear-gradient(135deg, #dee2e6 0%, #ced4da 100%);
    }

    .card-q-a:hover {
        transform: translateY(-5px);
        box-shadow: 0 15px 35px rgba(108, 117, 125, 0.15);
    }

    /* Reply button hover */
    button[onclick^="handleReplyClick"]:hover {
        transform: translateY(-1px);
        background: linear-gradient(135deg, #d1ecf1 0%, #bee5eb 100%) !important;
        color: #0c5460 !important;
        box-shadow: 0 4px 12px rgba(40, 167, 69, 0.2) !important;
    }

    /* ปุ่มแก้ไข/ลบ */
    button[onclick^="editTopic"]:hover {
        background: linear-gradient(135deg, #ffeaa7 0%, #fdcb6e 100%) !important;
        color: #6c5ce7 !important;
        transform: translateY(-1px);
        box-shadow: 0 4px 12px rgba(255, 193, 7, 0.3) !important;
    }

    button[onclick^="deleteTopic"]:hover {
        background: linear-gradient(135deg, #ff7675 0%, #fd79a8 100%) !important;
        color: white !important;
        transform: translateY(-1px);
        box-shadow: 0 4px 12px rgba(220, 53, 69, 0.3) !important;
    }

    .btn:hover {
        transform: translateY(-2px);
    }

    /* ปุ่มเพิ่มกระทู้ */
    button[onclick="handleAddNewTopic()"]:hover {
        background: linear-gradient(135deg, #1e90ff 0%, #4682b4 100%) !important;
        box-shadow: 0 6px 20px rgba(70, 130, 180, 0.4) !important;
    }

    /* Badge Styles */
    .badge {
        font-size: 0.75em;
        padding: 0.4em 0.7em;
        border-radius: 0.6rem;
        font-weight: 500;
        display: inline-flex;
        align-items: center;
        gap: 0.25rem;
    }

    .badge.bg-success {
        background: linear-gradient(135deg, #28a745, #20c997) !important;
        color: white;
    }

    .badge.bg-primary {
        background: linear-gradient(135deg, #007bff, #0056b3) !important;
        color: white;
    }

    .badge.bg-danger {
        background: linear-gradient(135deg, #dc3545, #c82333) !important;
        color: white;
    }

    .badge.bg-warning {
        background: linear-gradient(135deg, #ffc107, #e0a800) !important;
        color: #212529;
    }

    .badge.bg-info {
        background: linear-gradient(135deg, #17a2b8, #138496) !important;
        color: white;
    }

    .badge.bg-secondary {
        background: linear-gradient(135deg, #6c757d, #495057) !important;
        color: white;
    }

    .badge:hover {
        transform: scale(1.05);
        transition: transform 0.2s ease;
    }

    /* Edit Form Styles */
    .edit-form-container {
        background: linear-gradient(135deg, #fff9e6 0%, #ffeaa7 20%, #fff9e6 100%);
        border: 2px solid rgba(255, 193, 7, 0.3);
        border-radius: 20px;
        margin: 0;
        padding: 0;
        box-shadow: 0 8px 25px rgba(255, 193, 7, 0.2);
        animation: slideDown 0.3s ease-out;
    }

    @keyframes slideDown {
        from {
            opacity: 0;
            transform: translateY(-20px);
        }

        to {
            opacity: 1;
            transform: translateY(0);
        }
    }

    .edit-form-container .card-header {
        background: linear-gradient(135deg, #ffc107 0%, #e0a800 100%) !important;
        color: #212529 !important;
        border-radius: 18px 18px 0 0 !important;
    }

    .edit-form-container .form-control {
        background: linear-gradient(135deg, #ffffff 0%, #fffbf0 100%);
        border: 2px solid rgba(255, 193, 7, 0.2);
    }

    .edit-form-container .form-control:focus {
        border-color: rgba(255, 193, 7, 0.5);
        box-shadow: 0 0 0 0.2rem rgba(255, 193, 7, 0.25);
    }

    @media (max-width: 768px) {

        .col-6,
        .col-9,
        .col-3 {
            width: 100%;
            margin-bottom: 1rem;
        }

        .font-pages-head {
            font-size: 2rem !important;
        }

        .d-flex.justify-content-between {
            flex-direction: column;
            gap: 1rem;
        }

        .d-flex.justify-content-between h3 {
            order: 2;
        }

        .d-flex.justify-content-between button {
            order: 1;
            width: 100%;
        }

        .edit-delete-buttons {
            justify-content: center !important;
        }
    }
</style>





<script>
    // *** ส่งข้อมูลจาก PHP ไป JavaScript (FIXED VERSION WITH OVERFLOW HANDLING) ***
    const isUserLoggedIn = <?= json_encode($is_logged_in); ?>;
    const userInfo = <?= json_encode($user_info); ?>;
    let currentReplyId = null;
    let currentEditingTopicId = null;
    let currentEditingReplyId = null;

    // รอให้ SweetAlert2 โหลดเสร็จก่อน
    document.addEventListener('DOMContentLoaded', function () {
        // รอให้ Swal พร้อมใช้งาน
        const waitForSwal = setInterval(() => {
            if (typeof Swal !== 'undefined') {
                clearInterval(waitForSwal);
                //console.log('✅ SweetAlert2 loaded successfully');
                initializeQAPage();
            }
        }, 100);
    });

    // *** ฟังก์ชันหลักสำหรับ initialize ***
    function initializeQAPage() {
        //console.log('🚀 DOM Content Loaded - Running OVERFLOW FIXED permission check');

        // เรียกตรวจสอบสิทธิ์
        setTimeout(() => {
            checkTopicEditPermission();
            checkReplyEditPermission();
        }, 500);

        // เรียกฟังก์ชันเลื่อนไปที่การตอบกลับหลัง reload
        scrollToNewReplyAfterReload();

        // Flash messages เดิม
        const redirectUrl = sessionStorage.getItem('redirect_after_login');
        if (redirectUrl && isUserLoggedIn) {
            sessionStorage.removeItem('redirect_after_login');
            Swal.fire({
                icon: 'success',
                title: 'เข้าสู่ระบบสำเร็จ!',
                text: 'ยินดีต้อนรับกลับ',
                timer: 3000,
                showConfirmButton: false,
                position: 'top-end',
                toast: true
            });
        }

        // *** Keyboard shortcuts สำหรับ Edit ***
        document.addEventListener('keydown', function (e) {
            if (e.key === 'Escape' && currentEditingTopicId) {
                e.preventDefault();
                cancelEdit(currentEditingTopicId);
            }
        });

        // console.log('✅ Q&A Page with Overflow Fix initialized successfully');
    }

    // *** Debug ข้อมูลที่ได้รับ ***
    //console.log('=== JAVASCRIPT USER INFO DEBUG (OVERFLOW FIXED VERSION) ===');
    //console.log('isUserLoggedIn:', isUserLoggedIn);
    //console.log('userInfo:', userInfo);
    //if (userInfo) {
    //    console.log('userInfo.user_id (FIXED):', userInfo.user_id, typeof userInfo.user_id);
    //    console.log('userInfo.user_type:', userInfo.user_type);
    //    console.log('userInfo.email:', userInfo.email);
    //   console.log('userInfo.id (original session):', userInfo.id);
    //}
    //console.log('========================================');

    // *** ฟังก์ชันตรวจสอบสิทธิ์การแก้ไข/ลบกระทู้ ***
    // *** ฟังก์ชันตรวจสอบสิทธิ์การแก้ไข/ลบกระทู้ (แก้ไขแล้ว) ***
    function checkTopicEditPermission() {
        console.log('=== CHECKING TOPIC EDIT PERMISSIONS (FIXED SUPER_ADMIN VERSION) ===');
        console.log('User logged in:', isUserLoggedIn);
        console.log('User info:', userInfo);

        let currentUserId = null;
        let currentUserType = 'guest';
        let currentSystemType = 'guest';
        let isStaffOrAdmin = false;

        if (isUserLoggedIn && userInfo) {
            currentUserId = String(userInfo.user_id);
            currentUserType = userInfo.user_type || 'public';

            // *** แก้ไข: ตรวจสอบระดับ system ที่แท้จริงจากหลายแหล่ง ***
            currentSystemType = userInfo.m_system || userInfo.level || userInfo.user_type || 'guest';

            // *** ตรวจสอบว่าเป็น Staff/Admin หรือไม่ ***
            const adminTypes = ['system_admin', 'super_admin', 'user_admin', 'staff'];
            isStaffOrAdmin = adminTypes.includes(currentSystemType) ||
                adminTypes.includes(currentUserType) ||
                (userInfo.id && userInfo.id.toString().startsWith('m_')); // staff มักมี m_ prefix

            console.log('✅ User permission details:');
            console.log('  - User ID (FIXED):', currentUserId);
            console.log('  - User Type:', currentUserType);
            console.log('  - System Type:', currentSystemType);
            console.log('  - M System:', userInfo.m_system);
            console.log('  - Level:', userInfo.level);
            console.log('  - Email:', userInfo.email);
            console.log('  - Original Session ID:', userInfo.id);
            console.log('  - Is Staff/Admin:', isStaffOrAdmin);
        }

        const editDeleteButtons = document.querySelectorAll('.edit-delete-buttons');
        console.log('Found edit/delete button containers:', editDeleteButtons.length);

        editDeleteButtons.forEach(buttonContainer => {
            const topicId = buttonContainer.getAttribute('data-topic-id');
            const topicUserType = buttonContainer.getAttribute('data-user-type');
            const topicUserId = buttonContainer.getAttribute('data-user-id');

            console.log(`\n--- Topic ${topicId} Permission Check ---`);
            console.log('Topic User Type:', topicUserType);
            console.log('Topic User ID:', topicUserId);

            let canEdit = false;
            let reason = '';

            // *** กรณีที่ 1: ไม่ได้ login ***
            if (!isUserLoggedIn || !currentUserId) {
                reason = 'User not logged in or missing user_id';
                buttonContainer.style.display = 'none';
                console.log('❌ Hiding buttons - ' + reason);
                return;
            }

            // *** กรณีที่ 2: เป็น Staff/Admin - ให้สิทธิ์เต็ม ***
            if (isStaffOrAdmin) {
                canEdit = true;
                reason = `Staff/Admin (type: ${currentUserType}, system: ${currentSystemType}) can edit all topics`;

                buttonContainer.style.display = 'inline-flex';
                buttonContainer.style.gap = '0.5rem';
                console.log('✅ Showing edit buttons - ' + reason);
                return;
            }

            // *** กรณีที่ 3: ตรวจสอบ overflow user_id ***
            if (topicUserId === '2147483647' || topicUserId === 2147483647 || topicUserId == '2147483647') {
                console.log('⚠️ DETECTED OVERFLOW USER_ID, checking via API...');
                checkEditPermissionViaAPI(topicId, currentUserId, currentUserType, buttonContainer);
                return;
            }

            // *** กรณีที่ 4: Public User - ตรวจสอบเจ้าของ ***
            if (topicUserId && currentUserId == topicUserId) {
                canEdit = true;
                reason = `User owns this topic (FIXED: ${currentUserId} == ${topicUserId})`;
            } else if ((topicUserType === 'public' || topicUserType === 'staff') && !topicUserId) {
                canEdit = true;
                reason = `Legacy topic without user_id, allowing edit for logged-in user`;
            } else {
                reason = `No permission: FIXED user ${currentUserId} (${currentUserType}) cannot edit topic owned by ${topicUserId} (${topicUserType})`;
            }

            console.log('Can edit:', canEdit);
            console.log('Reason:', reason);

            if (canEdit) {
                buttonContainer.style.display = 'inline-flex';
                buttonContainer.style.gap = '0.5rem';
                console.log('✅ Showing edit buttons');
            } else {
                buttonContainer.style.display = 'none';
                console.log('❌ Hiding edit buttons');
            }
        });

        console.log('========================================');
    }

    // *** ฟังก์ชันตรวจสอบสิทธิ์ผ่าน API ***
    function checkEditPermissionViaAPI(topicId, currentUserId, currentUserType, buttonContainer) {
        console.log(`🔍 Checking permission for OVERFLOW topic ${topicId} via API...`);
        console.log(`📡 API Params: topicId=${topicId}, userId=${currentUserId}, userType=${currentUserType}`);

        const formData = new FormData();
        formData.append('action', 'check_edit_permission');
        formData.append('topic_id', topicId);
        formData.append('user_id', currentUserId);
        formData.append('user_type', currentUserType);

        fetch('<?= site_url("Pages/check_edit_permission"); ?>', {
            method: 'POST',
            body: formData
        })
            .then(response => {
                console.log(`📥 API Response status for topic ${topicId}:`, response.status);

                if (!response.ok) {
                    throw new Error(`HTTP ${response.status}: ${response.statusText}`);
                }

                return response.json();
            })
            .then(data => {
                //console.log(`📊 API Response data for topic ${topicId}:`, data);

                if (data.success && data.can_edit) {
                    // console.log(`✅ API: User CAN edit overflow topic ${topicId}`);
                    buttonContainer.style.display = 'inline-flex';
                    buttonContainer.style.gap = '0.5rem';

                    if (data.auto_fixed) {
                        showOverflowFixedNotification(topicId);
                    }

                } else {
                    console.log(`❌ API: User CANNOT edit overflow topic ${topicId} - ${data.message || 'Unknown reason'}`);
                    buttonContainer.style.display = 'none';
                }

                if (data.debug_info) {
                    // console.log(`🐛 Debug info for topic ${topicId}:`, data.debug_info);

                    if (data.debug_info.current_topic_user_id && data.debug_info.current_topic_user_id != '2147483647') {
                        buttonContainer.setAttribute('data-user-id', data.debug_info.current_topic_user_id);
                        console.log(`🔄 Updated DOM user_id for topic ${topicId}: ${data.debug_info.current_topic_user_id}`);
                    }
                }
            })
            .catch(error => {
                console.error(`🚨 Error checking permission for topic ${topicId}:`, error);
                console.error('Error details:', {
                    message: error.message,
                    stack: error.stack
                });

                buttonContainer.style.display = 'none';

                if (typeof Swal !== 'undefined') {
                    Swal.fire({
                        icon: 'error',
                        title: 'ไม่สามารถตรวจสอบสิทธิ์ได้',
                        text: `เกิดข้อผิดพลาดในการตรวจสอบสิทธิ์สำหรับกระทู้ ${topicId}`,
                        timer: 4000,
                        showConfirmButton: false,
                        position: 'top-end',
                        toast: true
                    });
                }
            });
    }

    // *** แสดง notification เมื่อระบบแก้ไข overflow แล้ว ***
    function showOverflowFixedNotification(topicId) {
        if (typeof Swal !== 'undefined' && !sessionStorage.getItem(`overflow_fixed_${topicId}`)) {
            Swal.fire({
                icon: 'success',
                title: 'ระบบแก้ไขข้อมูลแล้ว',
                text: `กระทู้ ${topicId} ได้รับการแก้ไขปัญหาการระบุตัวตนแล้ว`,
                timer: 3000,
                showConfirmButton: false,
                position: 'top-end',
                toast: true,
                background: 'linear-gradient(135deg, #e8f5e8 0%, #d4edda 100%)',
                color: '#155724'
            });

            sessionStorage.setItem(`overflow_fixed_${topicId}`, 'true');
        }
    }

    // *** ฟังก์ชันตรวจสอบสิทธิ์การแก้ไข/ลบ Reply ***
    function checkReplyEditPermission() {
        //console.log('=== CHECKING REPLY EDIT PERMISSIONS ===');

        let currentUserId = null;
        let currentUserType = 'guest';

        if (isUserLoggedIn && userInfo) {
            currentUserId = String(userInfo.user_id);
            currentUserType = userInfo.user_type || 'public';
        }

        const replyEditButtons = document.querySelectorAll('.reply-edit-delete-buttons');
        // console.log('Found reply edit buttons:', replyEditButtons.length);

        replyEditButtons.forEach(buttonContainer => {
            const replyId = buttonContainer.getAttribute('data-reply-id');
            const replyUserType = buttonContainer.getAttribute('data-user-type');
            const replyUserId = buttonContainer.getAttribute('data-user-id');

            // console.log(`\n--- Reply ${replyId} ---`);
            // console.log('Reply User Type:', replyUserType);
            // console.log('Reply User ID:', replyUserId);

            let canEdit = false;

            if (!isUserLoggedIn || !currentUserId) {
                buttonContainer.style.display = 'none';
                return;
            }

            if (['system_admin', 'super_admin'].includes(currentUserType)) {
                canEdit = true;
            } else if (replyUserId === '2147483647' || replyUserId === 2147483647 || replyUserId == '2147483647') {
                checkReplyEditPermissionViaAPI(replyId, currentUserId, currentUserType, buttonContainer);
                return;
            } else if (replyUserId && currentUserId == replyUserId) {
                canEdit = true;
            } else if ((replyUserType === 'public' || replyUserType === 'staff') && !replyUserId) {
                canEdit = true;
            }

            if (canEdit) {
                buttonContainer.style.display = 'inline-flex';
                buttonContainer.style.gap = '0.25rem';
            } else {
                buttonContainer.style.display = 'none';
            }
        });
    }

    // *** ฟังก์ชันตรวจสอบสิทธิ์ Reply ผ่าน API ***
    function checkReplyEditPermissionViaAPI(replyId, currentUserId, currentUserType, buttonContainer) {
        const formData = new FormData();
        formData.append('action', 'check_reply_edit_permission');
        formData.append('reply_id', replyId);
        formData.append('user_id', currentUserId);
        formData.append('user_type', currentUserType);

        fetch('<?= site_url("Pages/check_reply_edit_permission"); ?>', {
            method: 'POST',
            body: formData
        })
            .then(response => response.json())
            .then(data => {
                if (data.success && data.can_edit) {
                    buttonContainer.style.display = 'inline-flex';
                    buttonContainer.style.gap = '0.25rem';
                } else {
                    buttonContainer.style.display = 'none';
                }
            })
            .catch(error => {
                console.error('Error checking reply permission:', error);
                buttonContainer.style.display = 'none';
            });
    }

    // *** ฟังก์ชันหลักสำหรับการตอบกระทู้ ***
    function handleReplyClick(replyId) {
        currentReplyId = replyId;
        // console.log('🗨️ Reply clicked for topic:', replyId);

        if (!isUserLoggedIn) {
            //  console.log('👤 User not logged in, showing guest modal');
            showModal('guestReplyModal');
        } else {
            //  console.log('✅ User logged in, showing reply form directly');
            showReplyForm(replyId);
        }
    }

    // *** ฟังก์ชันแสดง Modal ***
    function showModal(modalId) {
        const modal = document.getElementById(modalId);
        if (!modal) {
            console.error('❌ Modal not found:', modalId);
            return;
        }

        console.log('📱 Showing modal:', modalId);

        if (typeof bootstrap !== 'undefined') {
            new bootstrap.Modal(modal).show();
        } else if (typeof $ !== 'undefined') {
            $(modal).modal('show');
        } else {
            modal.style.display = 'block';
            modal.classList.add('show');
        }
    }

    // *** ฟังก์ชันซ่อน Modal ทั้งหมด ***
    function hideAllModals() {
        document.querySelectorAll('.modal').forEach(modal => {
            if (typeof bootstrap !== 'undefined') {
                const modalInstance = bootstrap.Modal.getInstance(modal);
                if (modalInstance) modalInstance.hide();
            } else if (typeof $ !== 'undefined') {
                $(modal).modal('hide');
            } else {
                modal.style.display = 'none';
                modal.classList.remove('show');
            }
        });
    }

    // *** ฟังก์ชันสำหรับ Guest ตอบโดยไม่ login ***
    function proceedReplyAsGuest() {
        // console.log('👤 Guest proceeding to reply without login');
        hideAllModals();

        setTimeout(() => {
            showReplyForm(currentReplyId);

            setTimeout(() => {
                const replyContainer = document.getElementById('reply-container-' + currentReplyId);
                if (replyContainer) {
                    replyContainer.scrollIntoView({
                        behavior: 'smooth',
                        block: 'center',
                        inline: 'nearest'
                    });

                    const detailTextarea = replyContainer.querySelector('textarea[name="q_a_reply_detail"]');
                    if (detailTextarea) {
                        setTimeout(() => {
                            detailTextarea.focus();
                        }, 500);
                    }
                }
            }, 100);
        }, 300);
    }

    // *** ฟังก์ชันเปลี่ยนเส้นทางไปหน้า login ***
    function redirectToLogin() {
        hideAllModals();
        sessionStorage.setItem('redirect_after_login', window.location.href);
        window.open('<?= site_url("User"); ?>', '_blank');
    }

    // *** ฟังก์ชันสำหรับ guest ตั้งกระทู้ ***
    function proceedAsGuest() {
        hideAllModals();
        window.location.href = '<?= site_url("Pages/adding_q_a"); ?>';
    }

    // *** ฟังก์ชันไปหน้าตั้งกระทู้ใหม่ ***
    function handleAddNewTopic() {
        window.location.href = '<?= site_url("Pages/adding_q_a"); ?>';
    }

    // *** ฟังก์ชันแสดงฟอร์มตอบกระทู้ ***
    function showReplyForm(replyId) {
        document.querySelectorAll('.reply-container').forEach(c => {
            c.style.display = 'none';
            c.innerHTML = '';
        });

        const container = document.getElementById('reply-container-' + replyId);
        const nameField = isUserLoggedIn ?
            `<input type="text" name="q_a_reply_by" class="form-control" value="${userInfo.name}" readonly>` :
            `<input type="text" name="q_a_reply_by" class="form-control" placeholder="กรอกชื่อผู้ตอบกลับ" required>`;

        const emailField = isUserLoggedIn && userInfo.email ?
            `<input type="email" name="q_a_reply_email" class="form-control" value="${userInfo.email}" readonly>` :
            `<input type="email" name="q_a_reply_email" class="form-control" required placeholder="example@youremail.com">`;

        container.innerHTML = `
        <div class="card mb-4" style="border-radius: 20px; box-shadow: 0 8px 25px rgba(40, 167, 69, 0.1); border: none; background: linear-gradient(135deg, #f8fff8 0%, #e8f5e8 100%);">
            <div class="card-header text-center" style="background: linear-gradient(135deg, #28a745 0%, #20c997 100%); color: white; border-radius: 20px 20px 0 0; padding: 1rem;">
                <h5 class="mb-0">
                    <i class="fas fa-reply me-2"></i>ตอบกระทู้
                    <small class="d-block mt-1" style="font-size: 0.8rem; opacity: 0.9;">กรุณากรอกข้อมูลด้านล่างเพื่อตอบกระทู้</small>
                </h5>
            </div>
            
            <form action="<?= site_url('Pages/add_reply_q_a'); ?>" method="post" enctype="multipart/form-data" onsubmit="return handleReplySubmit(this, event)">
                <input type="hidden" name="q_a_reply_ref_id" value="${replyId}">
                <div class="card-body p-4">
                    <div class="row mb-3">
                        <div class="col-6">
                            <div class="form-label-wrapper">
                                <label class="form-label text-success fw-bold">
                                    <i class="fas fa-user me-2"></i>ชื่อ <span class="text-danger">*</span>
                                </label>
                            </div>
                            ${nameField}
                        </div>
                        <div class="col-6">
                            <div class="form-label-wrapper">
                                <label class="form-label text-success fw-bold">
                                    <i class="fas fa-envelope me-2"></i>อีเมล<span class="text-danger">*</span>
                                </label>
                            </div>
                            ${emailField}
                        </div>
                    </div>
                    <div class="mb-3">
                        <div class="form-label-wrapper">
                            <label class="form-label text-success fw-bold">
                                <i class="fas fa-align-left me-2"></i>รายละเอียด <span class="text-danger">*</span>
                            </label>
                        </div>
                        <textarea name="q_a_reply_detail" 
                                  class="form-control" 
                                  rows="6" 
                                  placeholder="กรอกรายละเอียดการตอบกระทู้..." 
                                  required
                                  style="background: linear-gradient(135deg, #ffffff 0%, #f0fff0 100%); border: 2px solid rgba(40, 167, 69, 0.2);"></textarea>
                    </div>
                    <div class="row">
                        <div class="col-9">
                            <div class="form-label-wrapper">
                                <label class="form-label text-success fw-bold">
                                    <i class="fas fa-images me-2"></i>รูปภาพเพิ่มเติม
                                </label>
                            </div>
                            <input type="file" 
                                   name="q_a_reply_imgs[]" 
                                   class="form-control" 
                                   accept="image/*" 
                                   multiple 
                                   onchange="validateReplyFileInput(this)"
                                   style="background: linear-gradient(135deg, #ffffff 0%, #f0fff0 100%); border: 2px solid rgba(40, 167, 69, 0.2);">
                            <small class="text-muted mt-1 d-block">
                                <i class="fas fa-info-circle me-1"></i>รองรับไฟล์ JPG, PNG, GIF, WebP (สูงสุด 5 ไฟล์)(ไม่เกิน 5 MB)
                            </small>
                        </div>
                        <div class="col-3 d-flex gap-2 align-items-end">
                            <button type="submit" 
                                    class="btn btn-success" 
                                    id="replySubmitBtn-${replyId}"
                                    style="background: linear-gradient(135deg, #28a745 0%, #20c997 100%); border: none; border-radius: 12px; padding: 0.8rem 1.2rem; font-weight: 600; box-shadow: 0 4px 15px rgba(40, 167, 69, 0.3); transition: all 0.3s ease;">
                                <i class="fas fa-paper-plane me-2"></i>ตอบกระทู้
                            </button>
                            <button type="button" 
                                    class="btn btn-secondary" 
                                    onclick="hideReplyForm(${replyId})"
                                    style="border-radius: 12px; padding: 0.8rem 1.2rem; font-weight: 600; transition: all 0.3s ease;">
                                <i class="fas fa-times me-2"></i>ยกเลิก
                            </button>
                        </div>
                    </div>
                </div>
            </form>
        </div>
    `;

        container.style.display = 'block';

        setTimeout(() => {
            container.scrollIntoView({
                behavior: 'smooth',
                block: 'center',
                inline: 'nearest'
            });

            const detailTextarea = container.querySelector('textarea[name="q_a_reply_detail"]');
            if (detailTextarea) {
                setTimeout(() => {
                    detailTextarea.focus();
                    detailTextarea.setSelectionRange(0, 0);
                }, 600);
            }
        }, 100);
    }

    // *** ฟังก์ชันซ่อนฟอร์มตอบกระทู้ ***
    function hideReplyForm(replyId) {
        const container = document.getElementById('reply-container-' + replyId);
        container.style.display = 'none';
        container.innerHTML = '';
    }

    // ==============================================
    // ระบบ Modal แสดงข้อผิดพลาดคำหยาบสำหรับหน้า Q&A
    // ==============================================

    // *** ฟังก์ชันเพื่อ decode Unicode escape sequences และ HTML entities ***
    function decodeWord(word) {
        try {
            console.log('🔍 Decoding word:', word);

            let decodedWord = word;

            // 1. Decode Unicode escape sequences (\u0e2a\u0e38\u0e20\u0e32\u0e1e)
            if (decodedWord.includes('\\u')) {
                try {
                    decodedWord = JSON.parse('"' + decodedWord + '"');
                    console.log('📝 Unicode decoded:', decodedWord);
                } catch (e) {
                    console.log('⚠️ Unicode decode failed:', e.message);
                }
            }

            // 2. Decode HTML entities (&amp;, &lt;, &gt;, etc.)
            if (decodedWord.includes('&')) {
                const tempDiv = document.createElement('div');
                tempDiv.innerHTML = decodedWord;
                decodedWord = tempDiv.textContent || tempDiv.innerText || decodedWord;
                console.log('📝 HTML entity decoded:', decodedWord);
            }

            // 3. Decode URL encoding (%20, %E0%B8%AA, etc.)
            if (decodedWord.includes('%')) {
                try {
                    decodedWord = decodeURIComponent(decodedWord);
                    console.log('📝 URL decoded:', decodedWord);
                } catch (e) {
                    console.log('⚠️ URL decode failed:', e.message);
                }
            }

            // 4. Trim whitespace
            decodedWord = decodedWord.trim();

            console.log('✅ Final decoded word:', decodedWord);
            return decodedWord;

        } catch (error) {
            console.error('❌ Error decoding word:', word, error);
            return word; // Return original if decoding fails
        }
    }


    // *** ฟังก์ชันแสดง Modal คำหยาบแบบพื้นฐาน ***
    function showVulgarErrorModal() {
        if (typeof Swal === 'undefined') {
            alert('พบคำไม่เหมาะสม กรุณาแก้ไขข้อความและลองใหม่');
            return;
        }

        Swal.fire({
            icon: 'error',
            title: '⚠️ พบเนื้อหาไม่เหมาะสม',
            html: `
            <div style="text-align: left; padding: 1rem;">
                <p style="margin-bottom: 1rem; color: #721c24;">
                    <i class="fas fa-exclamation-triangle me-2"></i>
                    <strong>ไม่สามารถบันทึกได้</strong>
                </p>
                <div style="background: #f8d7da; border: 1px solid #f5c6cb; border-radius: 8px; padding: 1rem; margin-bottom: 1rem;">
                    <p style="margin: 0; color: #721c24; font-size: 0.95rem;">
                        📝 <strong>สาเหตุ:</strong> พบคำหรือข้อความที่ไม่เหมาะสมในการตอบกลับ
                    </p>
                </div>
                <div style="background: #d1ecf1; border: 1px solid #bee5eb; border-radius: 8px; padding: 1rem;">
                    <p style="margin: 0; color: #0c5460; font-size: 0.9rem;">
                        💡 <strong>แนะนำ:</strong> กรุณาตรวจสอบและแก้ไขข้อความให้เหมาะสม แล้วลองส่งใหม่อีกครั้ง
                    </p>
                </div>
            </div>
        `,
            confirmButtonColor: '#dc3545',
            confirmButtonText: '<i class="fas fa-edit me-2"></i>แก้ไขข้อความ',
            allowOutsideClick: false,
            customClass: {
                popup: 'vulgar-error-modal',
                title: 'vulgar-error-title',
                htmlContainer: 'vulgar-error-content'
            }
        });
    }

    // *** ฟังก์ชันแสดง Modal คำหยาบพร้อมรายการคำที่พบ (พร้อม decoding) ***
    function showVulgarErrorModalWithWords(vulgarWords = []) {
        console.log('🚨 showVulgarErrorModalWithWords called with:', vulgarWords);

        if (typeof Swal === 'undefined') {
            // Decode words สำหรับ fallback alert
            const decodedWords = vulgarWords.map(word => decodeWord(word));
            let wordsText = decodedWords.length > 0 ? ` คำที่พบ: ${decodedWords.join(', ')}` : '';
            alert('พบคำไม่เหมาะสม' + wordsText + ' กรุณาแก้ไขข้อความและลองใหม่');
            return;
        }

        let wordsHtml = '';
        let processedWords = [];

        if (vulgarWords && vulgarWords.length > 0) {
            // Process และ decode แต่ละคำ
            processedWords = vulgarWords.map(word => {
                const originalWord = word;
                const decodedWord = decodeWord(word);

                // เก็บข้อมูลทั้ง original และ decoded เพื่อการ debug
                return {
                    original: originalWord,
                    decoded: decodedWord,
                    display: decodedWord || originalWord // ใช้ decoded ถ้ามี ไม่งั้นใช้ original
                };
            });

            console.log('📋 Processed words:', processedWords);

            wordsHtml = `
            <div style="background: #fff3cd; border: 1px solid #ffeaa7; border-radius: 8px; padding: 1rem; margin: 1rem 0;">
                <p style="margin: 0 0 0.5rem 0; color: #856404; font-weight: bold;">
                    🚫 <strong>คำที่ไม่เหมาะสม:</strong>
                </p>
                <div style="display: flex; flex-wrap: wrap; gap: 0.5rem;">
                    ${processedWords.map(wordObj => `
                        <span style="
                            background: #f8d7da; 
                            color: #721c24; 
                            padding: 0.3rem 0.6rem; 
                            border-radius: 15px; 
                            font-size: 0.85rem;
                            border: 1px solid #f5c6cb;
                        " title="Original: ${wordObj.original}">${wordObj.display}</span>
                    `).join('')}
                </div>
            </div>
        `;
        }

        Swal.fire({
            icon: 'error',
            title: '⚠️ พบเนื้อหาไม่เหมาะสม',
            html: `
            <div style="text-align: left; padding: 1rem;">
                <p style="margin-bottom: 1rem; color: #721c24;">
                    <i class="fas fa-exclamation-triangle me-2"></i>
                    <strong>ไม่สามารถบันทึกการตอบกลับได้</strong>
                </p>
                <div style="background: #f8d7da; border: 1px solid #f5c6cb; border-radius: 8px; padding: 1rem; margin-bottom: 1rem;">
                    <p style="margin: 0; color: #721c24; font-size: 0.95rem;">
                        📝 <strong>สาเหตุ:</strong> พบคำหรือข้อความที่ไม่เหมาะสมในการตอบกลับของคุณ
                    </p>
                </div>
                ${wordsHtml}
                <div style="background: #d1ecf1; border: 1px solid #bee5eb; border-radius: 8px; padding: 1rem;">
                    <p style="margin: 0; color: #0c5460; font-size: 0.9rem;">
                        💡 <strong>แนะนำ:</strong> กรุณาลบหรือแก้ไขคำดังกล่าวออกจากข้อความ แล้วลองส่งใหม่อีกครั้ง
                    </p>
                </div>
            </div>
        `,
            confirmButtonColor: '#dc3545',
            confirmButtonText: '<i class="fas fa-edit me-2"></i>แก้ไขข้อความ',
            allowOutsideClick: false,
            customClass: {
                popup: 'vulgar-error-modal',
                title: 'vulgar-error-title',
                htmlContainer: 'vulgar-error-content'
            },
            width: '600px'
        });
    }

    // *** ฟังก์ชันแสดง Modal สำหรับ URL Detection ***
    function showUrlDetectedModal() {
        if (typeof Swal === 'undefined') {
            alert('ไม่อนุญาตให้มี URL หรือลิงก์ในข้อความ');
            return;
        }

        Swal.fire({
            icon: 'warning',
            title: '🔗 พบลิงก์ในข้อความ',
            html: `
            <div style="text-align: left; padding: 1rem;">
                <p style="margin-bottom: 1rem; color: #856404;">
                    <i class="fas fa-link me-2"></i>
                    <strong>ไม่สามารถบันทึกการตอบกลับได้</strong>
                </p>
                <div style="background: #fff3cd; border: 1px solid #ffeaa7; border-radius: 8px; padding: 1rem; margin-bottom: 1rem;">
                    <p style="margin: 0; color: #856404; font-size: 0.95rem;">
                        🔗 <strong>สาเหตุ:</strong> ไม่อนุญาตให้มี URL หรือลิงก์ในการตอบกลับ
                    </p>
                </div>
                <div style="background: #d1ecf1; border: 1px solid #bee5eb; border-radius: 8px; padding: 1rem;">
                    <p style="margin: 0; color: #0c5460; font-size: 0.9rem;">
                        💡 <strong>แนะนำ:</strong> กรุณาลบ URL หรือลิงก์ออกจากข้อความ แล้วลองส่งใหม่อีกครั้ง
                    </p>
                </div>
            </div>
        `,
            confirmButtonColor: '#ffc107',
            confirmButtonText: '<i class="fas fa-edit me-2"></i>แก้ไขข้อความ',
            allowOutsideClick: false,
            customClass: {
                popup: 'url-error-modal',
                title: 'url-error-title'
            }
        });
    }

    // ==============================================
    // แก้ไขฟังก์ชัน handleReplySubmit ให้ใช้ Modal
    // ==============================================

    // *** แก้ไขฟังก์ชัน handleReplySubmit ที่มีอยู่แล้ว ***
    // *** แก้ไขฟังก์ชัน handleReplySubmit ให้รองรับ reCAPTCHA ***
    function handleReplySubmit(form, event) {
        event.preventDefault();

        const submitBtn = form.querySelector('button[type="submit"]');
        const originalContent = submitBtn.innerHTML;
        const replyId = form.querySelector('input[name="q_a_reply_ref_id"]').value;

        if (submitBtn.disabled) return false;
        if (!form.checkValidity()) {
            form.reportValidity();
            return false;
        }

        submitBtn.disabled = true;
        submitBtn.innerHTML = '<i class="fas fa-spinner fa-spin me-2"></i>กำลังส่ง...';

        // *** เพิ่ม: ตรวจสอบ reCAPTCHA ก่อนส่งข้อมูล ***
        handleRecaptchaForReply(form, replyId, submitBtn, originalContent);

        return false;
    }

    // *** ฟังก์ชันใหม่: จัดการ reCAPTCHA สำหรับ Reply ***
    function handleRecaptchaForReply(form, replyId, submitBtn, originalContent) {
        console.log('🔐 Starting reCAPTCHA verification for reply...');

        // *** ตรวจสอบว่า reCAPTCHA พร้อมใช้งานหรือไม่ ***
        if (typeof window.SKIP_RECAPTCHA_FOR_DEV !== 'undefined' && window.SKIP_RECAPTCHA_FOR_DEV) {
            console.log('⚠️ DEV MODE: Skipping reCAPTCHA verification');
            submitReplyWithoutRecaptcha(form, replyId, submitBtn, originalContent);
            return;
        }

        if (!window.RECAPTCHA_SITE_KEY || window.RECAPTCHA_SITE_KEY === '') {
            console.error('❌ reCAPTCHA Site Key not available');
            showRecaptchaError('ระบบยืนยันตัวตนไม่พร้อมใช้งาน', submitBtn, originalContent);
            return;
        }

        if (!window.recaptchaReady) {
            console.log('⏳ reCAPTCHA not ready yet, waiting...');

            let retryCount = 0;
            const maxRetries = 10;

            const waitForRecaptcha = setInterval(() => {
                retryCount++;

                if (window.recaptchaReady) {
                    console.log('✅ reCAPTCHA ready after waiting');
                    clearInterval(waitForRecaptcha);
                    executeRecaptchaForReply(form, replyId, submitBtn, originalContent);
                } else if (retryCount >= maxRetries) {
                    console.error('❌ reCAPTCHA timeout after waiting');
                    clearInterval(waitForRecaptcha);
                    showRecaptchaError('ระบบยืนยันตัวตนไม่ตอบสนอง กรุณาลองใหม่', submitBtn, originalContent);
                }
            }, 500);

            return;
        }

        // *** reCAPTCHA พร้อม - ดำเนินการทันที ***
        executeRecaptchaForReply(form, replyId, submitBtn, originalContent);
    }

    // *** ฟังก์ชันดำเนินการ reCAPTCHA สำหรับ Reply (ปรับปรุงการตรวจสอบ Staff) ***
    // *** ฟังก์ชันดำเนินการ reCAPTCHA สำหรับ Reply (ปรับปรุงแล้ว) ***
    function executeRecaptchaForReply(form, replyId, submitBtn, originalContent) {
        console.log('🚀 Executing reCAPTCHA for reply...');

        // *** อัปเดต UI ให้แสดงการ verify ***
        submitBtn.innerHTML = '<i class="fas fa-shield-alt fa-spin me-2"></i>กำลังยืนยันตัวตน...';

        try {
            // *** กำหนดค่าเริ่มต้นสำหรับ reCAPTCHA ***
            let recaptchaAction = 'qa_guest_submit'; // default สำหรับ guest
            let userTypeDetected = 'guest';
            let sourceType = 'guest_portal';
            let isStaffUser = false;

            // *** ตรวจสอบสถานะการเข้าสู่ระบบและประเภทผู้ใช้ ***
            if (window.isUserLoggedIn && window.userInfo) {
                console.log('🔍 Reply User logged in, checking user type...');
                console.log('🔍 Reply UserInfo available:', Object.keys(window.userInfo));

                // *** ตรวจสอบประเภทผู้ใช้จากหลายแหล่งข้อมูล ***
                const userType = window.userInfo.user_type || window.userInfo.type || 'public';
                const userSystem = window.userInfo.m_system || window.userInfo.system || '';
                const userLevel = window.userInfo.m_level || window.userInfo.level || '';
                const userId = window.userInfo.user_id || window.userInfo.id || '';
                const userEmail = window.userInfo.email || '';

                console.log('🔍 Reply User data:', {
                    userType: userType,
                    userSystem: userSystem,
                    userLevel: userLevel,
                    userId: userId,
                    email: userEmail,
                    hasM_id: !!window.userInfo.m_id
                });

                // *** ตรวจสอบว่าเป็น Staff/Admin หรือไม่ ***
                const staffTypes = ['system_admin', 'super_admin', 'user_admin', 'staff', 'admin'];
                const staffLevels = ['1', '2', '3', '4', '5', '6', '7', '8', '9', '10'];
                const staffEmailPatterns = [/@assystem\.co\.th$/, /@admin\./, /@staff\./];

                // ตรวจสอบจาก user_type
                isStaffUser = staffTypes.includes(userType) || staffTypes.includes(userSystem);

                // ตรวจสอบจาก level (ถ้ามี)
                if (!isStaffUser && userLevel) {
                    isStaffUser = staffLevels.includes(String(userLevel));
                }

                // ตรวจสอบจาก m_id (ถ้ามี m_id แสดงว่าเป็น staff)
                if (!isStaffUser && window.userInfo.m_id) {
                    isStaffUser = true;
                }

                // ตรวจสอบจาก email pattern
                if (!isStaffUser && userEmail) {
                    isStaffUser = staffEmailPatterns.some(pattern => pattern.test(userEmail));
                }

                // *** กำหนด reCAPTCHA action ตามประเภทผู้ใช้ ***
                if (isStaffUser) {
                    recaptchaAction = 'qa_admin_submit';
                    userTypeDetected = 'staff';
                    sourceType = 'staff_portal';
                    console.log('👤 Reply Staff/Admin user detected:', {
                        userType: userType,
                        userSystem: userSystem,
                        userLevel: userLevel,
                        hasM_id: !!window.userInfo.m_id,
                        action: recaptchaAction
                    });
                } else {
                    recaptchaAction = 'qa_guest_submit';
                    userTypeDetected = 'citizen';
                    sourceType = 'member_portal';
                    console.log('👥 Reply Public/Citizen user detected:', {
                        userType: userType,
                        action: recaptchaAction
                    });
                }

            } else {
                console.log('👤 Reply Guest user (not logged in)');
                recaptchaAction = 'qa_guest_submit';
                userTypeDetected = 'guest';
                sourceType = 'guest_portal';
            }

            // *** เพิ่มข้อมูล source เพิ่มเติม ***
            const additionalSource = {
                page: 'qa_reply',
                feature: 'reply_submission',
                timestamp: new Date().toISOString(),
                userAgent: navigator.userAgent.substring(0, 50),
                sessionInfo: {
                    hasUserInfo: !!window.userInfo,
                    isLoggedIn: window.isUserLoggedIn,
                    userInfoKeys: window.userInfo ? Object.keys(window.userInfo) : []
                }
            };

            console.log('🔐 Reply Final reCAPTCHA configuration:', {
                action: recaptchaAction,
                userType: userTypeDetected,
                source: sourceType,
                isStaffUser: isStaffUser
            });

            // *** Execute reCAPTCHA ***
            grecaptcha.execute(window.RECAPTCHA_SITE_KEY, {
                action: recaptchaAction
            })
                .then(function (token) {
                    console.log('✅ Reply reCAPTCHA token received for action:', recaptchaAction);
                    console.log('📝 Reply Token preview:', token.substring(0, 20) + '...');

                    if (!token || token.length === 0) {
                        throw new Error('reCAPTCHA token is empty');
                    }

                    // *** ส่งข้อมูลพร้อม token ***
                    submitReplyWithRecaptcha(form, replyId, token, submitBtn, originalContent, {
                        action: recaptchaAction,
                        source: sourceType,
                        userTypeDetected: userTypeDetected,
                        additionalSource: additionalSource
                    });
                })
                .catch(function (error) {
                    console.error('❌ Reply reCAPTCHA execution failed:', error);
                    showRecaptchaError('การยืนยันตัวตนล้มเหลว กรุณาลองใหม่', submitBtn, originalContent);
                });

        } catch (error) {
            console.error('❌ Reply reCAPTCHA execute error:', error);
            showRecaptchaError('ไม่สามารถเรียกใช้ระบบยืนยันตัวตนได้', submitBtn, originalContent);
        }
    }

    // *** ฟังก์ชันเสริมสำหรับตรวจสอบประเภทผู้ใช้ (ใช้ร่วมกันได้) ***
    function getUserTypeForRecaptcha() {
        // *** ถ้าไม่ได้เข้าสู่ระบบ ***
        if (!window.isUserLoggedIn || !window.userInfo) {
            return {
                action: 'qa_guest_submit',
                userType: 'guest',
                source: 'guest_portal',
                isStaff: false
            };
        }

        // *** ดึงข้อมูลผู้ใช้ ***
        const userType = window.userInfo.user_type || window.userInfo.type || 'public';
        const userSystem = window.userInfo.m_system || window.userInfo.system || '';
        const userLevel = window.userInfo.m_level || window.userInfo.level || '';
        const userEmail = window.userInfo.email || '';

        // *** ตรวจสอบว่าเป็น Staff หรือไม่ ***
        const staffTypes = ['system_admin', 'super_admin', 'user_admin', 'staff', 'admin'];
        const staffLevels = ['1', '2', '3', '4', '5', '6', '7', '8', '9', '10'];
        const staffEmailPatterns = [/@assystem\.co\.th$/, /@admin\./, /@staff\./];

        let isStaff = false;

        // ตรวจสอบจาก type
        isStaff = staffTypes.includes(userType) || staffTypes.includes(userSystem);

        // ตรวจสอบจาก level
        if (!isStaff && userLevel) {
            isStaff = staffLevels.includes(String(userLevel));
        }

        // ตรวจสอบจาก m_id
        if (!isStaff && window.userInfo.m_id) {
            isStaff = true;
        }

        // ตรวจสอบจาก email
        if (!isStaff && userEmail) {
            isStaff = staffEmailPatterns.some(pattern => pattern.test(userEmail));
        }

        // *** กำหนดค่าตามผลลัพธ์ ***
        if (isStaff) {
            return {
                action: 'qa_admin_submit',
                userType: 'staff',
                source: 'staff_portal',
                isStaff: true
            };
        } else {
            return {
                action: 'qa_guest_submit',
                userType: 'citizen',
                source: 'member_portal',
                isStaff: false
            };
        }
    }


    // *** ฟังก์ชันส่งข้อมูลพร้อม reCAPTCHA token (ปรับปรุงแล้ว) ***
    function submitReplyWithRecaptcha(form, replyId, recaptchaToken, submitBtn, originalContent, recaptchaData) {
        console.log('📤 Submitting reply with reCAPTCHA token...');

        // *** อัปเดต UI ***
        submitBtn.innerHTML = '<i class="fas fa-paper-plane fa-spin me-2"></i>กำลังส่งข้อมูล...';

        const formData = new FormData(form);

        // *** เพิ่ม reCAPTCHA token ***
        formData.append('g-recaptcha-response', recaptchaToken);

        // ✅ เพิ่ม reCAPTCHA action, source และ user type data
        if (recaptchaData) {
            formData.append('recaptcha_action', recaptchaData.action);
            formData.append('recaptcha_source', recaptchaData.source);
            formData.append('user_type_detected', recaptchaData.userTypeDetected);

            // ✅ Debug fields
            formData.append('debug_recaptcha_action', recaptchaData.action);
            formData.append('debug_user_type_detected', recaptchaData.userTypeDetected);
            formData.append('debug_source_type', recaptchaData.source);

            // ✅ Additional context
            formData.append('form_source', 'qa_reply_submission');
            formData.append('client_timestamp', recaptchaData.additionalSource.timestamp);
            formData.append('user_agent_info', recaptchaData.additionalSource.userAgent);

            console.log('📋 Reply form reCAPTCHA fields added:', {
                'recaptcha_action': recaptchaData.action,
                'recaptcha_source': recaptchaData.source,
                'user_type_detected': recaptchaData.userTypeDetected
            });
        }

        // เพิ่มข้อมูล user (ไม่เปลี่ยน)
        if (isUserLoggedIn && userInfo) {
            formData.append('fixed_user_id', userInfo.user_id);
            formData.append('user_type', userInfo.user_type);
            formData.append('original_session_id', userInfo.id);
            formData.append('user_email', userInfo.email);
        }

        // *** สำคัญ: บอกให้ Controller ส่ง JSON (ไม่เปลี่ยน) ***
        formData.append('ajax_request', '1');

        // *** เพิ่ม debug info (ไม่เปลี่ยน) ***
        formData.append('action_type', 'reply_submission');
        formData.append('reply_ref_id', replyId);

        const replyTimestamp = Date.now();
        const replyContent = form.querySelector('textarea[name="q_a_reply_detail"]').value.trim();

        fetch(form.action, {
            method: 'POST',
            headers: {
                'X-Requested-With': 'XMLHttpRequest'
            },
            body: formData
        })
            .then(response => {
                console.log('📨 Reply response status:', response.status);

                const contentType = response.headers.get('content-type');
                console.log('📨 Reply Content-Type:', contentType);

                if (!response.ok) {
                    throw new Error(`HTTP ${response.status}: ${response.statusText}`);
                }

                // *** แยกการจัดการตาม Content-Type (ไม่เปลี่ยน) ***
                if (contentType && contentType.includes('application/json')) {
                    return response.json();
                } else {
                    console.warn('⚠️ Controller sent HTML instead of JSON, parsing...');
                    return response.text().then(html => {
                        return parseHtmlResponse(html);
                    });
                }
            })
            .then(data => {
                console.log('📨 Reply response data:', data);

                // *** ตรวจสอบ reCAPTCHA errors (ไม่เปลี่ยน) ***
                if (data.error_type === 'recaptcha_missing' || data.error_type === 'recaptcha_failed') {
                    console.log('🚫 reCAPTCHA verification failed:', data.message);
                    showRecaptchaError(data.message || 'การยืนยันตัวตนไม่ผ่าน', submitBtn, originalContent);
                    return;
                }

                // *** ตรวจสอบคำหยาบ (ไม่เปลี่ยน) ***
                if (data.vulgar_detected === true) {
                    console.log('🚫 Reply vulgar detected:', data.vulgar_words);
                    showVulgarErrorModalWithWords(data.vulgar_words || []);
                    submitBtn.disabled = false;
                    submitBtn.innerHTML = originalContent;
                    return;
                }

                // *** ตรวจสอบ URL detection (ไม่เปลี่ยน) ***
                if (data.url_detected === true) {
                    console.log('🚫 Reply URL detected');
                    showUrlDetectedModal();
                    submitBtn.disabled = false;
                    submitBtn.innerHTML = originalContent;
                    return;
                }

                // *** ตรวจสอบ error อื่นๆ (ไม่เปลี่ยน) ***
                if (data.success === false) {
                    throw new Error(data.message || 'เกิดข้อผิดพลาด');
                }

                // *** สำเร็จ - เก็บข้อมูลและ reload (ไม่เปลี่ยน) ***
                console.log('✅ Reply submitted successfully!');

                sessionStorage.setItem('scrollToNewReply', replyId);
                sessionStorage.setItem('newReplyTimestamp', replyTimestamp.toString());
                sessionStorage.setItem('newReplyContent', replyContent.substring(0, 100));
                sessionStorage.setItem('showReplySuccess', 'true');

                Swal.fire({
                    icon: 'success',
                    title: 'ส่งการตอบกลับสำเร็จ!',
                    text: 'กำลังโหลดการตอบกลับใหม่...',
                    timer: 1500,
                    showConfirmButton: false,
                    didClose: () => {
                        window.location.reload();
                    }
                });
            })
            .catch(error => {
                console.error('❌ Reply submission error:', error);

                // *** ตรวจสอบประเภท error (ไม่เปลี่ยน) ***
                if (error.message.includes('recaptcha') || error.message.includes('ยืนยันตัวตน')) {
                    showRecaptchaError(error.message, submitBtn, originalContent);
                } else if (error.message.includes('vulgar') || error.message.includes('คำไม่เหมาะสม')) {
                    showVulgarErrorModalWithWords([]);
                    submitBtn.disabled = false;
                    submitBtn.innerHTML = originalContent;
                } else if (error.message.includes('URL') || error.message.includes('ลิงก์')) {
                    showUrlDetectedModal();
                    submitBtn.disabled = false;
                    submitBtn.innerHTML = originalContent;
                } else {
                    Swal.fire({
                        icon: 'error',
                        title: 'เกิดข้อผิดพลาด',
                        text: error.message || 'ไม่สามารถส่งการตอบกลับได้'
                    });
                    submitBtn.disabled = false;
                    submitBtn.innerHTML = originalContent;
                }
            });
    }

    // *** ฟังก์ชันส่งข้อมูลโดยไม่มี reCAPTCHA (สำหรับ development) ***
    function submitReplyWithoutRecaptcha(form, replyId, submitBtn, originalContent) {
        console.log('⚠️ Submitting reply WITHOUT reCAPTCHA (Development mode)');

        const formData = new FormData(form);

        // เพิ่มข้อมูล user
        if (isUserLoggedIn && userInfo) {
            formData.append('fixed_user_id', userInfo.user_id);
            formData.append('user_type', userInfo.user_type);
            formData.append('original_session_id', userInfo.id);
            formData.append('user_email', userInfo.email);
        }

        formData.append('ajax_request', '1');
        formData.append('dev_mode', '1'); // แจ้งให้ Controller ทราบว่าเป็น dev mode

        const replyTimestamp = Date.now();
        const replyContent = form.querySelector('textarea[name="q_a_reply_detail"]').value.trim();

        fetch(form.action, {
            method: 'POST',
            headers: {
                'X-Requested-With': 'XMLHttpRequest'
            },
            body: formData
        })
            .then(response => {
                // ใช้การจัดการเดียวกับฟังก์ชันปกติ
                console.log('📨 Reply response status (no reCAPTCHA):', response.status);

                const contentType = response.headers.get('content-type');

                if (!response.ok) {
                    throw new Error(`HTTP ${response.status}: ${response.statusText}`);
                }

                if (contentType && contentType.includes('application/json')) {
                    return response.json();
                } else {
                    return response.text().then(html => {
                        return parseHtmlResponse(html);
                    });
                }
            })
            .then(data => {
                console.log('📨 Reply response data (no reCAPTCHA):', data);

                // จัดการ response เหมือนฟังก์ชันปกติ
                if (data.vulgar_detected === true) {
                    showVulgarErrorModalWithWords(data.vulgar_words || []);
                    submitBtn.disabled = false;
                    submitBtn.innerHTML = originalContent;
                    return;
                }

                if (data.url_detected === true) {
                    showUrlDetectedModal();
                    submitBtn.disabled = false;
                    submitBtn.innerHTML = originalContent;
                    return;
                }

                if (data.success === false) {
                    throw new Error(data.message || 'เกิดข้อผิดพลาด');
                }

                // สำเร็จ
                sessionStorage.setItem('scrollToNewReply', replyId);
                sessionStorage.setItem('newReplyTimestamp', replyTimestamp.toString());
                sessionStorage.setItem('newReplyContent', replyContent.substring(0, 100));
                sessionStorage.setItem('showReplySuccess', 'true');

                Swal.fire({
                    icon: 'success',
                    title: 'ส่งการตอบกลับสำเร็จ!',
                    text: 'กำลังโหลดการตอบกลับใหม่...',
                    timer: 1500,
                    showConfirmButton: false,
                    didClose: () => {
                        window.location.reload();
                    }
                });
            })
            .catch(error => {
                console.error('❌ Reply submission error (no reCAPTCHA):', error);

                Swal.fire({
                    icon: 'error',
                    title: 'เกิดข้อผิดพลาด',
                    text: error.message || 'ไม่สามารถส่งการตอบกลับได้'
                });

                submitBtn.disabled = false;
                submitBtn.innerHTML = originalContent;
            });
    }

    // *** ฟังก์ชันแสดง error สำหรับ reCAPTCHA ***
    function showRecaptchaError(message, submitBtn, originalContent) {
        console.error('🚫 reCAPTCHA Error:', message);

        if (typeof Swal !== 'undefined') {
            Swal.fire({
                icon: 'error',
                title: '🔐 ปัญหาการยืนยันตัวตน',
                html: `
                <div style="text-align: left; padding: 1rem;">
                    <p style="margin-bottom: 1rem; color: #721c24;">
                        <i class="fas fa-shield-alt me-2"></i>
                        <strong>การยืนยันตัวตนล้มเหลว</strong>
                    </p>
                    <div style="background: #f8d7da; border: 1px solid #f5c6cb; border-radius: 8px; padding: 1rem; margin-bottom: 1rem;">
                        <p style="margin: 0; color: #721c24; font-size: 0.95rem;">
                            🛡️ <strong>สาเหตุ:</strong> ${message}
                        </p>
                    </div>
                    <div style="background: #d1ecf1; border: 1px solid #bee5eb; border-radius: 8px; padding: 1rem;">
                        <p style="margin: 0; color: #0c5460; font-size: 0.9rem;">
                            💡 <strong>แนะนำ:</strong> โปรดรีเฟรชหน้าเว็บและลองส่งการตอบกลับใหม่อีกครั้ง
                        </p>
                    </div>
                </div>
            `,
                confirmButtonColor: '#dc3545',
                confirmButtonText: '<i class="fas fa-redo me-2"></i>ลองใหม่',
                allowOutsideClick: false,
                customClass: {
                    popup: 'recaptcha-error-modal',
                    title: 'recaptcha-error-title'
                },
                width: '500px'
            });
        } else {
            alert('การยืนยันตัวตนล้มเหลว: ' + message);
        }

        // *** Reset button ***
        if (submitBtn && originalContent) {
            submitBtn.disabled = false;
            submitBtn.innerHTML = originalContent;
        }
    }

    // *** ฟังก์ชันแปลง HTML response เป็น JSON (ใช้ร่วมกัน) ***
    function parseHtmlResponse(html) {
        try {
            // ตรวจสอบข้อความใน HTML
            if (html.includes('พบคำไม่เหมาะสม') || html.includes('vulgar')) {
                return {
                    success: false,
                    vulgar_detected: true,
                    vulgar_words: extractVulgarWordsFromHTML(html),
                    message: 'พบคำไม่เหมาะสม',
                    error_type: 'vulgar_content'
                };
            }

            if (html.includes('ไม่อนุญาตให้มี URL') || html.includes('check_no_urls')) {
                return {
                    success: false,
                    url_detected: true,
                    message: 'ไม่อนุญาตให้มี URL หรือลิงก์ในข้อความ',
                    error_type: 'url_content'
                };
            }

            if (html.includes('reCAPTCHA') || html.includes('การยืนยันตัวตน')) {
                return {
                    success: false,
                    message: 'การยืนยันตัวตนไม่ผ่าน',
                    error_type: 'recaptcha_failed'
                };
            }

            // ถ้าไม่พบปัญหา ถือว่าสำเร็จ
            return {
                success: true,
                message: 'บันทึกการตอบกลับสำเร็จ'
            };

        } catch (error) {
            console.error('❌ Error parsing HTML response:', error);
            return {
                success: false,
                message: 'ไม่สามารถแปลงข้อมูลจากเซิร์ฟเวอร์ได้',
                error_type: 'parse_error'
            };
        }
    }

    // *** เพิ่ม CSS สำหรับ reCAPTCHA Modal ***
    const recaptchaModalStyles = `
        <style>
        .recaptcha-error-modal {
            border-radius: 20px !important;
            box-shadow: 0 20px 60px rgba(220, 53, 69, 0.3) !important;
        }

        .recaptcha-error-title {
            color: #721c24 !important;
            font-size: 1.4rem !important;
            font-weight: 700 !important;
        }

        .swal2-confirm.swal2-styled {
            border-radius: 12px !important;
            font-weight: 600 !important;
            padding: 0.7rem 1.5rem !important;
            transition: all 0.3s ease !important;
        }

        @keyframes shieldPulse {
            0% { transform: scale(1); }
            50% { transform: scale(1.1); }
            100% { transform: scale(1); }
        }

        .recaptcha-error-modal .swal2-icon {
            animation: shieldPulse 2s infinite;
        }
        </style>
        `;

    // *** เพิ่ม CSS เข้าไปใน head ***
    if (!document.getElementById('recaptcha-modal-styles')) {
        const styleElement = document.createElement('style');
        styleElement.id = 'recaptcha-modal-styles';
        styleElement.innerHTML = recaptchaModalStyles.replace(/<\/?style>/g, '');
        document.head.appendChild(styleElement);
    }

    console.log('✅ Enhanced handleReplySubmit with reCAPTCHA support loaded');
    console.log('🔧 Functions available:');
    console.log('- handleReplySubmit() - Main function (now with reCAPTCHA)');
    console.log('- showRecaptchaError() - Show reCAPTCHA error modal');
    console.log('- submitReplyWithRecaptcha() - Submit with reCAPTCHA verification');
    console.log('- submitReplyWithoutRecaptcha() - Submit without reCAPTCHA (dev mode)');

    // *** ฟังก์ชันเลื่อนไปที่การตอบกลับใหม่หลัง reload (แก้ไขแล้ว) ***
    function scrollToNewReplyAfterReload() {
        const replyId = sessionStorage.getItem('scrollToNewReply');
        const showSuccess = sessionStorage.getItem('showReplySuccess');
        const replyTimestamp = sessionStorage.getItem('newReplyTimestamp');
        const replyContent = sessionStorage.getItem('newReplyContent');
        const editSuccess = sessionStorage.getItem('editSuccess');
        const showEditSuccess = sessionStorage.getItem('showEditSuccess');

        // *** แก้ไข: ป้องกันการ scroll และ highlight ทับกัน ***
        if (replyId && showSuccess) {
            sessionStorage.removeItem('scrollToNewReply');
            sessionStorage.removeItem('showReplySuccess');
            sessionStorage.removeItem('newReplyTimestamp');
            sessionStorage.removeItem('newReplyContent');

            console.log('🎯 Scrolling to new reply for topic ID:', replyId);
            console.log('Reply timestamp:', replyTimestamp);
            console.log('Reply content preview:', replyContent);

            // *** แก้ไข: ใช้ delay ที่ยาวขึ้นและป้องกันการทำงานซ้ำ ***
            setTimeout(() => {
                scrollToNewReplyWithHighlight(replyId, replyTimestamp, replyContent);
            }, 2500); // เพิ่ม delay เป็น 2.5 วินาที

            return; // *** สำคัญ: return เพื่อป้องกันการรัน edit success ทับ ***
        }

        // *** แก้ไข: แยกการ handle edit success ออกมา ***
        if (editSuccess && showEditSuccess) {
            sessionStorage.removeItem('editSuccess');
            sessionStorage.removeItem('showEditSuccess');

            console.log('📝 Scrolling to edited topic ID:', editSuccess);

            setTimeout(() => {
                scrollToEditedTopicWithHighlight(editSuccess);
            }, 1000); // ใช้ delay ที่แตกต่างกัน
        }
    }

    // *** 2. สร้างฟังก์ชันแยกสำหรับ highlight reply ใหม่ ***
    function scrollToNewReplyWithHighlight(replyId, replyTimestamp, replyContent) {
        const topicCard = document.getElementById('comment-' + replyId);
        if (!topicCard) {
            console.error('❌ Topic card not found for ID:', replyId);
            return;
        }

        // console.log('✅ Found topic card:', topicCard.id);

        const repliesSection = topicCard.querySelector('.replies-section-' + replyId);
        if (!repliesSection) {
            console.error('❌ Replies section not found for topic:', replyId);
            return;
        }

        // console.log('✅ Found replies section');

        // *** แก้ไข: ป้องกันการ scroll กลับด้านบน ***
        let scrollProtectionActive = true;
        const preventScrollBack = function (e) {
            if (scrollProtectionActive && window.scrollY < 200) {
                e.preventDefault();
                return false;
            }
        };

        window.addEventListener('scroll', preventScrollBack, { passive: false });

        // หา reply ใหม่
        let newReply = findNewestReplyAdvanced(repliesSection, replyTimestamp, replyContent);

        if (newReply) {
            //console.log('✅ Found new reply:', newReply.id);

            // *** แก้ไข: เคลียร์ highlight อื่นๆ ก่อน ***
            clearAllHighlights();

            // เพิ่ม highlight effect สำหรับ reply ใหม่ (สีเขียว)
            newReply.style.transition = 'all 0.6s ease';
            newReply.style.background = 'linear-gradient(135deg, rgba(40, 167, 69, 0.25) 0%, rgba(32, 201, 151, 0.15) 100%)';
            newReply.style.border = '3px solid rgba(40, 167, 69, 0.7)';
            newReply.style.transform = 'scale(1.03)';
            newReply.style.boxShadow = '0 12px 30px rgba(40, 167, 69, 0.4)';

            // เพิ่ม data attribute เพื่อระบุว่าเป็น new reply
            newReply.setAttribute('data-new-reply', 'true');

            // คำนวณตำแหน่งที่จะ scroll
            const replyRect = newReply.getBoundingClientRect();
            const targetScrollY = window.scrollY + replyRect.top - (window.innerHeight / 2) + (replyRect.height / 2);

            // Smooth scroll ไปที่ reply
            window.scrollTo({
                top: Math.max(0, targetScrollY),
                behavior: 'smooth'
            });

            // แสดง success notification
            setTimeout(() => {
                if (typeof Swal !== 'undefined') {
                    Swal.fire({
                        icon: 'success',
                        title: '🎉 ตอบกระทู้สำเร็จ!',
                        text: 'การตอบกลับของคุณถูกเพิ่มแล้ว',
                        timer: 3000,
                        showConfirmButton: false,
                        position: 'top-end',
                        toast: true,
                        background: 'linear-gradient(135deg, #e8f5e8 0%, #d4edda 100%)',
                        color: '#155724'
                    });
                }
            }, 1000);

            // *** แก้ไข: ลบ highlight หลัง 5 วินาที และปลดล็อก scroll ***
            setTimeout(() => {
                if (newReply.getAttribute('data-new-reply') === 'true') {
                    newReply.style.background = '';
                    newReply.style.border = '';
                    newReply.style.transform = '';
                    newReply.style.boxShadow = '';
                    newReply.removeAttribute('data-new-reply');
                }

                // ปลดล็อกการป้องกัน scroll
                scrollProtectionActive = false;
                window.removeEventListener('scroll', preventScrollBack);

                // console.log('✅ Reply highlight cleared and scroll protection removed');
            }, 5000);

            // console.log('✅ Successfully scrolled to new reply with highlight');

        } else {
            console.log('❌ Could not identify new reply, scrolling to replies section');

            // Fallback: scroll ไปที่ replies section
            repliesSection.scrollIntoView({
                behavior: 'smooth',
                block: 'end',
                inline: 'nearest'
            });

            // ปลดล็อกการป้องกัน scroll
            setTimeout(() => {
                scrollProtectionActive = false;
                window.removeEventListener('scroll', preventScrollBack);
            }, 3000);
        }
    }

    // *** 3. สร้างฟังก์ชันแยกสำหรับ highlight topic ที่แก้ไข ***
    function scrollToEditedTopicWithHighlight(editId) {
        const qaCard = document.getElementById('comment-' + editId);
        if (!qaCard) {
            console.error('❌ Edited topic card not found for ID:', editId);
            return;
        }

        //console.log('✅ Found edited topic card:', qaCard.id);

        // *** เคลียร์ highlight อื่นๆ ก่อน ***
        clearAllHighlights();

        // ตรวจสอบว่าต้อง scroll หรือไม่
        const cardRect = qaCard.getBoundingClientRect();
        const windowHeight = window.innerHeight;

        if (cardRect.top < 0 || cardRect.bottom > windowHeight) {
            // เพิ่ม highlight effect สำหรับ topic ที่แก้ไข (สีเหลือง)
            qaCard.style.transition = 'all 0.5s ease';
            qaCard.style.background = 'linear-gradient(135deg, rgba(255, 193, 7, 0.2) 0%, rgba(255, 235, 59, 0.1) 100%)';
            qaCard.style.border = '2px solid rgba(255, 193, 7, 0.5)';
            qaCard.style.transform = 'scale(1.02)';
            qaCard.style.boxShadow = '0 8px 25px rgba(255, 193, 7, 0.3)';

            // เพิ่ม data attribute เพื่อระบุว่าเป็น edited topic
            qaCard.setAttribute('data-edited-topic', 'true');

            qaCard.scrollIntoView({
                behavior: 'smooth',
                block: 'center',
                inline: 'nearest'
            });

            // แสดง success notification
            setTimeout(() => {
                if (typeof Swal !== 'undefined') {
                    Swal.fire({
                        icon: 'success',
                        title: '📝 แก้ไขสำเร็จ!',
                        text: 'กระทู้ของคุณถูกแก้ไขแล้ว',
                        timer: 2500,
                        showConfirmButton: false,
                        position: 'top-end',
                        toast: true,
                        background: 'linear-gradient(135deg, #fff9e6 0%, #ffeaa7 100%)',
                        color: '#856404'
                    });
                }
            }, 500);

            // ลบ highlight หลัง 4 วินาที
            setTimeout(() => {
                if (qaCard.getAttribute('data-edited-topic') === 'true') {
                    qaCard.style.background = '';
                    qaCard.style.border = '';
                    qaCard.style.transform = '';
                    qaCard.style.boxShadow = '';
                    qaCard.removeAttribute('data-edited-topic');
                }
            }, 4000);

            // console.log('✅ Successfully scrolled to edited topic with highlight');
        }
    }

    // *** 4. ฟังก์ชันเคลียร์ highlight ทั้งหมด ***
    function clearAllHighlights() {
        console.log('🧹 Clearing all existing highlights');

        // เคลียร์ highlight ของ reply ใหม่
        document.querySelectorAll('[data-new-reply="true"]').forEach(element => {
            element.style.background = '';
            element.style.border = '';
            element.style.transform = '';
            element.style.boxShadow = '';
            element.removeAttribute('data-new-reply');
        });

        // เคลียร์ highlight ของ topic ที่แก้ไข
        document.querySelectorAll('[data-edited-topic="true"]').forEach(element => {
            element.style.background = '';
            element.style.border = '';
            element.style.transform = '';
            element.style.boxShadow = '';
            element.removeAttribute('data-edited-topic');
        });

        // เคลียร์ highlight ที่อาจมาจาก notification
        document.querySelectorAll('[style*="rgba(255, 215, 0"]').forEach(element => {
            element.style.background = '';
            element.style.border = '';
            element.style.transform = '';
            element.style.boxShadow = '';
        });

        // console.log('✅ All highlights cleared');
    }




    // *** ฟังก์ชันใหม่: ค้นหา reply ใหม่ที่แม่นยำขึ้น ***
    function findNewestReplyAdvanced(repliesSection, submissionTimestamp, expectedContent) {
        // console.log('🔍 Finding newest reply with advanced matching...');

        const allReplies = repliesSection.querySelectorAll('.reply-item');
        // console.log('Total replies found:', allReplies.length);

        if (allReplies.length === 0) {
            return null;
        }

        // *** แก้ไข: ถ้ามี timestamp ให้หา reply ที่เพิ่มหลัง timestamp ***
        if (submissionTimestamp) {
            const submissionTime = new Date(parseInt(submissionTimestamp));
            //console.log('Submission time:', submissionTime);

            // หา reply ที่มีเวลาใกล้เคียงกับเวลาที่ส่ง
            let bestMatch = null;
            let bestScore = 0;

            allReplies.forEach((reply, index) => {
                let score = 0;

                // คะแนนจากตำแหน่ง (reply ใหม่อยู่ท้าย)
                const positionScore = (index + 1) / allReplies.length * 40;
                score += positionScore;

                // คะแนนจากเนื้อหา
                if (expectedContent) {
                    const detailElement = reply.querySelector('.mb-3 span');
                    if (detailElement) {
                        const replyText = detailElement.textContent.trim();
                        const similarity = calculateTextSimilarity(replyText, expectedContent);
                        score += similarity * 60; // เพิ่มน้ำหนักของเนื้อหา
                    }
                }

                console.log(`Reply ${index + 1} score: ${score.toFixed(1)}`);

                if (score > bestScore) {
                    bestScore = score;
                    bestMatch = reply;
                }
            });

            console.log(`Best match score: ${bestScore.toFixed(1)}`);

            // *** แก้ไข: ยกเกณฑ์คะแนนขั้นต่ำ ***
            if (bestScore >= 60) {
                //  console.log('✅ Found reply with good confidence score');
                return bestMatch;
            }
        }

        // Fallback: ใช้ reply ล่าสุด
        //console.log('📍 Using fallback: last reply');
        return allReplies[allReplies.length - 1];
    }

    // *** ฟังก์ชันคำนวณความคล้ายคลึงของข้อความ ***
    function calculateTextSimilarity(text1, text2) {
        if (!text1 || !text2) return 0;

        // ทำความสะอาดข้อความ
        const clean1 = text1.toLowerCase().replace(/[^\u0E00-\u0E7Fa-z0-9\s]/g, '').trim();
        const clean2 = text2.toLowerCase().replace(/[^\u0E00-\u0E7Fa-z0-9\s]/g, '').trim();

        if (clean1 === clean2) return 1.0;

        // ตรวจสอบว่ามีส่วนที่เหมือนกันหรือไม่
        const words1 = clean1.split(/\s+/);
        const words2 = clean2.split(/\s+/);

        let matchCount = 0;
        const maxWords = Math.max(words1.length, words2.length);

        words1.forEach(word1 => {
            if (words2.some(word2 => word2.includes(word1) || word1.includes(word2))) {
                matchCount++;
            }
        });

        return maxWords > 0 ? matchCount / maxWords : 0;
    }

    // *** ฟังก์ชันแก้ไขกระทู้ ***
    function editTopic(topicId) {
        //console.log('📝 Starting inline edit for topic:', topicId);

        if (currentEditingTopicId && currentEditingTopicId !== topicId) {
            cancelEdit(currentEditingTopicId);
        }

        currentEditingTopicId = topicId;

        const topicContent = document.querySelector(`.topic-content-${topicId}`);
        const editContainer = document.querySelector(`.edit-form-container-${topicId}`);
        const repliesSection = document.querySelector(`.replies-section-${topicId}`);

        if (!topicContent || !editContainer) {
            console.error('❌ Cannot find topic content or edit container for ID:', topicId);
            return;
        }

        const originalTitle = topicContent.querySelector('.card-header span').textContent.split(' - ')[1];
        const originalDetail = topicContent.querySelector('.card-body span').textContent;

        // console.log('📋 Original data:', { title: originalTitle, detail: originalDetail });

        const editForm = createEditForm(topicId, originalTitle, originalDetail);
        editContainer.innerHTML = editForm;

        topicContent.style.display = 'none';
        editContainer.style.display = 'block';
        if (repliesSection) {
            repliesSection.style.display = 'none';
        }

        setTimeout(() => {
            editContainer.scrollIntoView({
                behavior: 'smooth',
                block: 'center',
                inline: 'nearest'
            });

            const firstInput = editContainer.querySelector('input[name="q_a_msg"]');
            if (firstInput) {
                setTimeout(() => {
                    firstInput.focus();
                    firstInput.select();
                }, 500);
            }
        }, 100);

        //console.log('✅ Edit form displayed for topic:', topicId);
    }

    // *** ฟังก์ชันสร้างฟอร์มแก้ไข ***
    function createEditForm(topicId, originalTitle, originalDetail) {
        const nameField = isUserLoggedIn ?
            `<input type="text" name="q_a_by" class="form-control" value="${userInfo.name}" readonly>` :
            `<input type="text" name="q_a_by" class="form-control" placeholder="กรอกชื่อผู้แก้ไข" required>`;

        const emailField = isUserLoggedIn && userInfo.email ?
            `<input type="email" name="q_a_email" class="form-control" value="${userInfo.email}" readonly>` :
            `<input type="email" name="q_a_email" class="form-control" required placeholder="example@youremail.com">`;

        return `
        <div class="card edit-form-container" style="border-radius: 20px; box-shadow: 0 8px 25px rgba(255, 193, 7, 0.2); border: 2px solid rgba(255, 193, 7, 0.3); background: linear-gradient(135deg, #fff9e6 0%, #ffeaa7 20%, #fff9e6 100%);">
            <div class="card-header text-center" style="background: linear-gradient(135deg, #ffc107 0%, #e0a800 100%); color: #212529; border-radius: 18px 18px 0 0; padding: 1rem;">
                <h5 class="mb-0">
                    <i class="fas fa-edit me-2"></i>แก้ไขกระทู้
                    <small class="d-block mt-1" style="font-size: 0.8rem; opacity: 0.9;">กรุณาแก้ไขข้อมูลด้านล่างแล้วคลิกบันทึก</small>
                </h5>
            </div>
            
            <form action="<?= site_url('Pages/update_topic'); ?>" method="post" enctype="multipart/form-data" onsubmit="return handleEditSubmit(this, event, ${topicId})">
                <input type="hidden" name="topic_id" value="${topicId}">
                <div class="card-body p-4">
                    <div class="row mb-3">
                        <div class="col-6">
                            <div class="form-label-wrapper">
                                <label class="form-label text-warning fw-bold">
                                    <i class="fas fa-user me-2"></i>ชื่อ <span class="text-danger">*</span>
                                </label>
                            </div>
                            ${nameField}
                        </div>
                        <div class="col-6">
                            <div class="form-label-wrapper">
                                <label class="form-label text-warning fw-bold">
                                    <i class="fas fa-envelope me-2"></i>อีเมล<span class="text-danger">*</span>
                                </label>
                            </div>
                            ${emailField}
                        </div>
                    </div>
                    <div class="mb-3">
                        <div class="form-label-wrapper">
                            <label class="form-label text-warning fw-bold">
                                <i class="fas fa-heading me-2"></i>หัวข้อ <span class="text-danger">*</span>
                            </label>
                        </div>
                        <input type="text" name="q_a_msg" class="form-control" value="${originalTitle}" required placeholder="กรอกหัวข้อกระทู้">
                    </div>
                    <div class="mb-3">
                        <div class="form-label-wrapper">
                            <label class="form-label text-warning fw-bold">
                                <i class="fas fa-align-left me-2"></i>รายละเอียด <span class="text-danger">*</span>
                            </label>
                        </div>
                        <textarea name="q_a_detail" 
                                  class="form-control" 
                                  rows="8" 
                                  placeholder="กรอกรายละเอียดกระทู้..." 
                                  required>${originalDetail}</textarea>
                    </div>
                    <div class="row">
                        <div class="col-9">
                            <div class="form-label-wrapper">
                                <label class="form-label text-warning fw-bold">
                                    <i class="fas fa-images me-2"></i>รูปภาพเพิ่มเติม
                                </label>
                            </div>
                            <input type="file" 
                                   name="q_a_imgs[]" 
                                   class="form-control" 
                                   accept="image/*" 
                                   multiple 
                                   onchange="validateEditFileInput(this)"
                                   style="background: linear-gradient(135deg, #ffffff 0%, #fffbf0 100%); border: 2px solid rgba(255, 193, 7, 0.2);">
                            <small class="text-muted mt-1 d-block">
                                <i class="fas fa-info-circle me-1"></i>รองรับไฟล์ JPG, PNG, GIF, WebP (สูงสุด 5 ไฟล์)(ไม่เกิน 5 MB)
                            </small>
                        </div>
                        <div class="col-3 d-flex gap-2 align-items-end flex-column">
                            <button type="submit" 
                                    class="btn btn-warning w-100" 
                                    id="editSubmitBtn-${topicId}"
                                    style="background: linear-gradient(135deg, #ffc107 0%, #e0a800 100%); border: none; border-radius: 12px; padding: 0.8rem 1.2rem; font-weight: 600; box-shadow: 0 4px 15px rgba(255, 193, 7, 0.3); transition: all 0.3s ease; color: #212529;">
                                <i class="fas fa-save me-2"></i>บันทึก
                            </button>
                            <button type="button" 
                                    class="btn btn-secondary w-100" 
                                    onclick="cancelEdit(${topicId})"
                                    style="border-radius: 12px; padding: 0.8rem 1.2rem; font-weight: 600; transition: all 0.3s ease;">
                                <i class="fas fa-times me-2"></i>ยกเลิก
                            </button>
                        </div>
                    </div>
                </div>
            </form>
        </div>
    `;
    }

    // *** ฟังก์ชันยกเลิกการแก้ไข ***
    function cancelEdit(topicId) {
        console.log('❌ Cancelling edit for topic:', topicId);

        const topicContent = document.querySelector(`.topic-content-${topicId}`);
        const editContainer = document.querySelector(`.edit-form-container-${topicId}`);
        const repliesSection = document.querySelector(`.replies-section-${topicId}`);

        if (topicContent && editContainer) {
            topicContent.style.display = 'block';
            editContainer.style.display = 'none';
            editContainer.innerHTML = '';

            if (repliesSection) {
                repliesSection.style.display = 'block';
            }

            setTimeout(() => {
                topicContent.scrollIntoView({
                    behavior: 'smooth',
                    block: 'center',
                    inline: 'nearest'
                });
            }, 100);
        }

        currentEditingTopicId = null;
        // console.log('✅ Edit cancelled successfully');
    }

    // แก้ไขฟังก์ชัน validateEditFileInput ใน JavaScript
    function validateEditFileInput(input) {
        const files = input.files;
        const maxFiles = 5;
        const maxSize = 5 * 1024 * 1024; // 5MB
        const allowedTypes = ['image/jpeg', 'image/jpg', 'image/png', 'image/gif', 'image/webp'];

        // *** เพิ่ม: ตรวจสอบจำนวนรูปเก่าที่มีอยู่ ***
        // ดึงข้อมูลจำนวนรูปเก่าจาก DOM หรือ AJAX call
        const topicId = input.closest('form').querySelector('input[name="topic_id"]').value;

        // วิธีที่ 1: ส่ง AJAX ตรวจสอบจำนวนรูปเก่า (แนะนำ)
        checkExistingImagesCount(topicId, files.length, input);

        // ตรวจสอบจำนวนไฟล์ใหม่
        if (files.length > maxFiles) {
            alert('เลือกได้สูงสุด ' + maxFiles + ' ไฟล์');
            input.value = '';
            return false;
        }

        for (let i = 0; i < files.length; i++) {
            const file = files[i];

            // ตรวจสอบประเภทไฟล์
            if (!allowedTypes.includes(file.type)) {
                alert('ไฟล์ ' + file.name + ' ไม่ใช่ไฟล์รูปภาพที่รองรับ');
                input.value = '';
                return false;
            }

            // ตรวจสอบขนาดไฟล์
            if (file.size > maxSize) {
                alert('ไฟล์ ' + file.name + ' มีขนาดใหญ่เกินไป (สูงสุด 5MB)');
                input.value = '';
                return false;
            }
        }

        return true;
    }

    // *** เพิ่มฟังก์ชันตรวจสอบจำนวนรูปเก่า ***
    function checkExistingImagesCount(topicId, newFilesCount, inputElement) {
        // ส่ง AJAX request ไปตรวจสอบจำนวนรูปเก่า
        fetch(baseUrl + 'Pages/get_existing_images_count', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/x-www-form-urlencoded',
            },
            body: 'topic_id=' + encodeURIComponent(topicId)
        })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    const existingCount = data.existing_count;
                    const maxImages = 5;
                    const totalImages = existingCount + newFilesCount;

                    if (totalImages > maxImages) {
                        const remainingSlots = maxImages - existingCount;
                        const message = `สามารถเพิ่มรูปภาพได้อีกเพียง ${remainingSlots} รูป\n(ปัจจุบันมี ${existingCount} รูป จากทั้งหมด ${maxImages} รูป)`;

                        alert(message);
                        inputElement.value = '';
                        return false;
                    }
                }
            })
            .catch(error => {
                console.error('Error checking existing images:', error);
                // ถ้า AJAX ล้มเหลว ให้ผ่านไป (อาจตรวจสอบที่ backend แทน)
            });
    }


    // ===================================================================
    // ค้นหาฟังก์ชัน handleEditSubmit() และแทนที่ด้วยโค้ดนี้
    // ===================================================================
    /**
     * ฟังก์ชันจัดการการส่งฟอร์มแก้ไข - FINAL VERSION
     * @param {HTMLFormElement} form - Form element
     * @param {Event} event - Submit event
     * @param {string|number} topicId - Topic ID
     */
    function handleEditSubmit(form, event, topicId) {
        event.preventDefault();

        const submitBtn = form.querySelector('button[type="submit"]');
        const originalContent = submitBtn.innerHTML;

        // ตรวจสอบว่าไม่ได้ส่งซ้ำ
        if (submitBtn.disabled) {
            console.log('Edit form submission already in progress');
            return false;
        }

        // ตรวจสอบความถูกต้องของฟอร์ม
        if (!form.checkValidity()) {
            form.reportValidity();
            return false;
        }

        // ปิดการใช้งานปุ่มและแสดง Loading
        submitBtn.disabled = true;
        submitBtn.innerHTML = '<i class="fas fa-spinner fa-spin me-2"></i>กำลังบันทึก...';

        // เตรียมข้อมูลฟอร์ม
        const formData = new FormData(form);

        // เพิ่มข้อมูล User ถ้า Login อยู่
        if (isUserLoggedIn && userInfo) {
            formData.append('fixed_user_id', userInfo.user_id);
            formData.append('user_type', userInfo.user_type);
            formData.append('original_session_id', userInfo.id);
            formData.append('user_email', userInfo.email);
        }

        // บอกให้ส่ง JSON response
        formData.append('ajax_request', '1');

        // Log ข้อมูลที่ส่ง
        console.log('🔄 Submitting edit form for topic:', topicId);
        console.log('📤 Form data prepared');

        // ส่ง Request
        fetch(form.action, {
            method: 'POST',
            headers: {
                'X-Requested-With': 'XMLHttpRequest'
            },
            body: formData
        })
            .then(response => {
                console.log('📨 Edit response status:', response.status);
                console.log('📨 Edit response headers:', response.headers.get('content-type'));

                const contentType = response.headers.get('content-type');

                // *** 🔥 CRITICAL FIX: จัดการ Response ให้ถูกต้อง ***

                // ตรวจสอบ Content Type และแปลง Response
                if (contentType && contentType.includes('application/json')) {
                    console.log('✅ JSON response detected');
                    return response.json();
                }

                // ถ้าไม่ใช่ JSON และ status OK
                if (response.ok) {
                    console.log('📄 HTML response received for success');
                    return response.text().then(html => {
                        // ตรวจสอบ Success
                        if (html.includes('แก้ไขกระทู้สำเร็จ') || html.includes('save_success')) {
                            return {
                                success: true,
                                message: 'แก้ไขกระทู้สำเร็จ',
                                topic_id: topicId
                            };
                        }

                        // ถือว่าสำเร็จถ้าไม่มีปัญหา
                        return { success: true };
                    });
                }

                // *** 🔥 CRITICAL FIX: จัดการ HTTP Error Status ***
                // ถ้า status ไม่ OK แต่อาจจะมี JSON Error Response

                console.log('⚠️ HTTP Error Status:', response.status);

                // ลองอ่าน JSON ก่อน (สำคัญมาก!)
                if (contentType && contentType.includes('application/json')) {
                    console.log('📋 Reading JSON error response...');
                    return response.json().then(data => {
                        console.log('📄 JSON error data received:', data);
                        return data;
                    });
                }

                // ถ้าไม่มี JSON ให้ลองอ่าน text
                console.log('📄 Reading text error response...');
                return response.text().then(html => {
                    console.log('📄 HTML error response:', html.substring(0, 300) + '...');

                    // ตรวจสอบ URL detection จาก HTML
                    if (html.includes('URL') || html.includes('ลิงก์') ||
                        html.includes('url_content') || html.includes('url_detected')) {
                        return {
                            success: false,
                            url_detected: true,
                            message: 'ไม่อนุญาตให้มี URL หรือลิงก์ในข้อความ',
                            error_type: 'url_content',
                            debug_info: {
                                source: 'HTML_fallback',
                                status: response.status
                            }
                        };
                    }

                    // ตรวจสอบ Vulgar detection
                    if (html.includes('vulgar') || html.includes('คำไม่เหมาะสม') ||
                        html.includes('vulgar_content')) {
                        return {
                            success: false,
                            vulgar_detected: true,
                            vulgar_words: extractVulgarWordsFromHTML(html),
                            message: 'พบคำไม่เหมาะสม',
                            error_type: 'vulgar_content',
                            debug_info: {
                                source: 'HTML_fallback',
                                status: response.status
                            }
                        };
                    }

                    // Generic error
                    return {
                        success: false,
                        message: `HTTP ${response.status}: ${response.statusText}`,
                        error_type: 'http_error',
                        debug_info: {
                            source: 'HTML_fallback',
                            status: response.status,
                            html_preview: html.substring(0, 200)
                        }
                    };
                });
            })
            .then(data => {
                console.log('✅ Edit response data processed:', data);

                // *** 🔥 Enhanced: เพิ่ม debug info ***
                if (data.debug_info) {
                    console.log('🔍 Debug info:', data.debug_info);
                }

                // ========================================================================
                // 🚨 ตรวจสอบคำหยาบ (Vulgar Detection)
                // ========================================================================
                if (data.vulgar_detected === true) {
                    console.log('🚨 Vulgar content detected:', data.vulgar_words);
                    console.log('📱 Calling showVulgarErrorModalWithWords()...');

                    // เรียกใช้ Modal ที่มีอยู่แล้ว
                    if (typeof showVulgarErrorModalWithWords === 'function') {
                        showVulgarErrorModalWithWords(data.vulgar_words || []);
                    } else {
                        console.error('❌ showVulgarErrorModalWithWords function not found');
                        // Fallback
                        Swal.fire({
                            icon: 'warning',
                            title: 'พบคำไม่เหมาะสม',
                            text: 'กรุณาตรวจสอบและแก้ไขข้อความของคุณ',
                            confirmButtonText: 'ตกลง'
                        });
                    }

                    // เปิดการใช้งานปุ่มกลับคืน
                    submitBtn.disabled = false;
                    submitBtn.innerHTML = originalContent;
                    return;
                }

                // ========================================================================
                // 🔗 ตรวจสอบ URL (URL Detection) - ENHANCED
                // ========================================================================
                if (data.url_detected === true) {
                    console.log('🔗 URL content detected');
                    console.log('📱 Calling showUrlDetectedModal()...');

                    // เรียกใช้ Modal ที่มีอยู่แล้ว
                    if (typeof showUrlDetectedModal === 'function') {
                        showUrlDetectedModal();
                    } else {
                        console.error('❌ showUrlDetectedModal function not found');
                        // Fallback
                        Swal.fire({
                            icon: 'warning',
                            title: 'พบ URL ในข้อความ',
                            text: 'ไม่อนุญาตให้มี URL หรือลิงก์ในข้อความ',
                            confirmButtonText: 'ตกลง'
                        });
                    }

                    // เปิดการใช้งานปุ่มกลับคืน
                    submitBtn.disabled = false;
                    submitBtn.innerHTML = originalContent;

                    console.log('🎭 URL detection modal displayed');
                    return;
                }

                // ========================================================================
                // ❌ ตรวจสอบ Error อื่นๆ
                // ========================================================================
                if (data.success === false) {
                    console.error('❌ Edit failed:', data.message);

                    // แสดง error message ตาม error type
                    let errorMessage = data.message || 'เกิดข้อผิดพลาดในการแก้ไข';

                    // ปรับ error message ตาม error type
                    switch (data.error_type) {
                        case 'validation_error':
                            errorMessage = 'ข้อมูลไม่ครบถ้วน กรุณาตรวจสอบและลองใหม่';
                            break;
                        case 'permission_denied':
                            errorMessage = 'คุณไม่มีสิทธิ์แก้ไขกระทู้นี้';
                            break;
                        case 'topic_not_found':
                            errorMessage = 'ไม่พบกระทู้ที่ต้องการแก้ไข';
                            break;
                        case 'image_upload_error':
                            errorMessage = 'เกิดข้อผิดพลาดในการอัพโหลดรูปภาพ';
                            break;
                        case 'database_error':
                            errorMessage = 'เกิดข้อผิดพลาดในการบันทึกข้อมูล';
                            break;
                        case 'http_error':
                            errorMessage = data.message || 'เกิดข้อผิดพลาดในการส่งข้อมูล';
                            break;
                        default:
                            errorMessage = data.message || 'เกิดข้อผิดพลาดในการแก้ไข';
                    }

                    // แสดง Error Modal
                    Swal.fire({
                        icon: 'error',
                        title: 'เกิดข้อผิดพลาด',
                        text: errorMessage,
                        confirmButtonText: 'ตกลง',
                        confirmButtonColor: '#dc3545'
                    });

                    // เปิดการใช้งานปุ่มกลับคืน
                    submitBtn.disabled = false;
                    submitBtn.innerHTML = originalContent;
                    return;
                }

                // ========================================================================
                // ✅ สำเร็จ - แสดงผลลัพธ์
                // ========================================================================
                console.log('✅ Edit successful for topic:', topicId);

                // เก็บข้อมูลสำเร็จใน Session Storage
                sessionStorage.setItem('editSuccess', topicId);
                sessionStorage.setItem('showEditSuccess', 'true');
                sessionStorage.setItem('editTimestamp', new Date().toISOString());

                // แสดง Success Modal
                Swal.fire({
                    icon: 'success',
                    title: 'แก้ไขกระทู้สำเร็จ!',
                    html: `
                <div class="text-center">
                    <i class="fas fa-check-circle text-success mb-3" style="font-size: 3rem;"></i>
                    <p class="mb-2">กระทู้ของคุณได้รับการแก้ไขเรียบร้อยแล้ว</p>
                    <small class="text-muted">กำลังโหลดข้อมูลใหม่...</small>
                </div>
            `,
                    timer: 1500,
                    showConfirmButton: false,
                    allowOutsideClick: false,
                    allowEscapeKey: false,
                    didOpen: () => {
                        Swal.getPopup().classList.add('animate__animated', 'animate__fadeInUp');
                    },
                    didClose: () => {
                        console.log('🔄 Reloading page to show updated content');
                        window.location.reload();
                    }
                });
            })
            .catch(error => {
                console.error('❌ Edit submission error:', error);

                // ========================================================================
                // 🚨 จัดการ Error ตามประเภท
                // ========================================================================

                // ตรวจสอบ Error Type จาก Error Message
                if (error.message.includes('vulgar') || error.message.includes('คำไม่เหมาะสม')) {
                    console.log('🚨 Vulgar error detected in catch block');
                    if (typeof showVulgarErrorModalWithWords === 'function') {
                        showVulgarErrorModalWithWords([]);
                    } else {
                        Swal.fire({
                            icon: 'warning',
                            title: 'พบคำไม่เหมาะสม',
                            text: 'กรุณาตรวจสอบและแก้ไขข้อความของคุณ'
                        });
                    }

                } else if (error.message.includes('URL') || error.message.includes('ลิงก์')) {
                    console.log('🔗 URL error detected in catch block');
                    if (typeof showUrlDetectedModal === 'function') {
                        showUrlDetectedModal();
                    } else {
                        Swal.fire({
                            icon: 'warning',
                            title: 'พบ URL ในข้อความ',
                            text: 'ไม่อนุญาตให้มี URL หรือลิงก์ในข้อความ'
                        });
                    }

                } else {
                    // Error ทั่วไป
                    let errorTitle = 'เกิดข้อผิดพลาด';
                    let errorMessage = error.message || 'ไม่สามารถบันทึกการแก้ไขได้ กรุณาลองใหม่อีกครั้ง';

                    // ปรับ Title ตาม Error Type
                    if (error.message.includes('HTTP 400')) {
                        errorTitle = 'ข้อมูลไม่ถูกต้อง';
                        errorMessage = 'กรุณาตรวจสอบข้อมูลและลองใหม่อีกครั้ง';
                    } else if (error.message.includes('HTTP 403')) {
                        errorTitle = 'ไม่มีสิทธิ์เข้าถึง';
                        errorMessage = 'คุณไม่มีสิทธิ์แก้ไขกระทู้นี้';
                    } else if (error.message.includes('HTTP 404')) {
                        errorTitle = 'ไม่พบข้อมูล';
                        errorMessage = 'ไม่พบกระทู้ที่ต้องการแก้ไข';
                    } else if (error.message.includes('HTTP 500')) {
                        errorTitle = 'ข้อผิดพลาดเซิร์ฟเวอร์';
                        errorMessage = 'เกิดข้อผิดพลาดในระบบ กรุณาลองใหม่ภายหลัง';
                    }

                    Swal.fire({
                        icon: 'error',
                        title: errorTitle,
                        text: errorMessage,
                        confirmButtonText: 'ตกลง',
                        confirmButtonColor: '#dc3545'
                    });
                }

                // เปิดการใช้งานปุ่มกลับคืน
                submitBtn.disabled = false;
                submitBtn.innerHTML = originalContent;
            });

        return false;
    }


    // *** เพิ่ม function ตรวจสอบ Modal functions ***
    function checkModalFunctions() {
        const modalFunctions = [
            'showVulgarErrorModalWithWords',
            'showUrlDetectedModal'
        ];

        modalFunctions.forEach(funcName => {
            if (typeof window[funcName] === 'function') {
                console.log(`✅ ${funcName} is available`);
            } else {
                console.warn(`⚠️ ${funcName} is not available`);
            }
        });
    }

    // เรียกใช้ตรวจสอบเมื่อเริ่มต้น
    checkModalFunctions();

    // ===================================================================
    // 🎯 เพิ่มฟังก์ชัน Modal สำหรับ Image Limit Error
    // ===================================================================

    /**
     * 🔧 ฟังก์ชันแสดง Modal Image Limit แบบง่าย
     */
    function showImageLimitErrorModal(data) {
        console.log('🖼️ showImageLimitErrorModal called with data:', data);

        if (typeof Swal === 'undefined') {
            alert('จำกัดการเพิ่มรูปสูงสุดแค่ 5 รูปเท่านั้น');
            return;
        }

        Swal.fire({
            icon: 'warning',
            title: '🖼️ รูปภาพเกินขั้นสูง',
            text: 'จำกัดการเพิ่มรูปสูงสุดแค่ 5 รูปเท่านั้น',
            confirmButtonColor: '#ffc107',
            confirmButtonText: '<i class="fas fa-edit me-2"></i>ตกลง',
            allowOutsideClick: false,
            customClass: {
                popup: 'image-limit-error-modal',
                title: 'image-limit-error-title'
            }
        });
    }

    /**
     * 🔧 ฟังก์ชันแสดง Modal Image Limit แบบง่าย (สำรอง)
     */
    function showSimpleImageLimitModal(message = 'จำกัดการเพิ่มรูปสูงสุดแค่ 5 รูปเท่านั้น') {
        console.log('🖼️ showSimpleImageLimitModal called with:', message);

        if (typeof Swal === 'undefined') {
            alert('จำกัดการเพิ่มรูปสูงสุดแค่ 5 รูปเท่านั้น');
            return;
        }

        Swal.fire({
            icon: 'warning',
            title: '🖼️ รูปภาพเกินขั้นสูง',
            text: 'จำกัดการเพิ่มรูปสูงสุดแค่ 5 รูปเท่านั้น',
            confirmButtonColor: '#ffc107',
            confirmButtonText: '<i class="fas fa-edit me-2"></i>ตกลง',
            allowOutsideClick: false,
            customClass: {
                popup: 'image-limit-error-modal',
                title: 'image-limit-error-title'
            }
        });
    }

    // ===================================================================
    // 🎯 เพิ่ม CSS สำหรับ Image Limit Modal
    // ===================================================================

    const imageLimitModalStyles = `
<style>
.image-limit-error-modal {
    border-radius: 20px !important;
    box-shadow: 0 20px 60px rgba(255, 193, 7, 0.3) !important;
}

.image-limit-error-title {
    color: #856404 !important;
    font-size: 1.4rem !important;
    font-weight: 700 !important;
}

.image-limit-error-content {
    font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif !important;
}

.image-limit-error-modal .swal2-confirm {
    background: linear-gradient(135deg, #ffc107 0%, #e0a800 100%) !important;
    border: none !important;
    border-radius: 12px !important;
    font-weight: 600 !important;
    padding: 0.7rem 1.5rem !important;
    transition: all 0.3s ease !important;
    color: #212529 !important;
}

.image-limit-error-modal .swal2-confirm:hover {
    transform: translateY(-2px) !important;
    box-shadow: 0 6px 20px rgba(255, 193, 7, 0.4) !important;
    background: linear-gradient(135deg, #e0a800 0%, #d39e00 100%) !important;
}

/* Animation สำหรับ Image Modal */
@keyframes imageWarning {
    0% { transform: scale(1); }
    50% { transform: scale(1.1); }
    100% { transform: scale(1); }
}

.image-limit-error-modal .swal2-icon {
    animation: imageWarning 2s infinite;
}

/* Responsive */
@media (max-width: 768px) {
    .image-limit-error-modal {
        width: 95vw !important;
        margin: 0 auto !important;
    }
    
    .image-limit-error-title {
        font-size: 1.2rem !important;
    }
    
    .image-limit-error-content {
        font-size: 0.9rem !important;
    }
}
</style>
`;

    // เพิ่ม CSS เข้าไปใน head (ถ้ายังไม่มี)
    if (!document.getElementById('image-limit-modal-styles')) {
        const styleElement = document.createElement('style');
        styleElement.id = 'image-limit-modal-styles';
        styleElement.innerHTML = imageLimitModalStyles.replace(/<\/?style>/g, '');
        document.head.appendChild(styleElement);
    }

    console.log('✅ Enhanced handleEditSubmit() with Image Limit Modal loaded');
    console.log('🔧 Functions available:');
    console.log('- showImageLimitErrorModal(data) - แสดง Modal แบบละเอียด');
    console.log('- showSimpleImageLimitModal(message) - แสดง Modal แบบง่าย');




    // ฟังก์ชันแสดง alert
    function showAlert(type, message) {
        // ใช้ SweetAlert หรือ alert ธรรมดา
        if (typeof Swal !== 'undefined') {
            Swal.fire({
                icon: type === 'success' ? 'success' : 'error',
                title: type === 'success' ? 'สำเร็จ' : 'เกิดข้อผิดพลาด',
                text: message,
                timer: type === 'success' ? 2000 : 5000,
                showConfirmButton: type !== 'success'
            });
        } else {
            alert(message);
        }
    }

    // *** ฟังก์ชันลบกระทู้ ***
    function deleteTopic(topicId) {
        Swal.fire({
            title: 'ยืนยันการลบกระทู้',
            text: 'คุณแน่ใจหรือไม่ที่จะลบกระทู้นี้? การดำเนินการนี้ไม่สามารถยกเลิกได้!',
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#dc3545',
            cancelButtonColor: '#6c757d',
            confirmButtonText: '<i class="fas fa-trash me-2"></i>ลบกระทู้',
            cancelButtonText: '<i class="fas fa-times me-2"></i>ยกเลิก',
            background: 'linear-gradient(135deg, #ffffff 0%, #ffe6e6 100%)',
            customClass: {
                popup: 'border-0 shadow-lg',
                title: 'text-danger fw-bold fs-4',
                confirmButton: 'btn-danger',
                cancelButton: 'btn-secondary'
            },
            footer: '<small class="text-muted"><i class="fas fa-exclamation-triangle me-1"></i>การลบกระทู้จะลบการตอบกลับทั้งหมดด้วย</small>'
        }).then((result) => {
            if (result.isConfirmed) {
                Swal.fire({
                    title: 'กำลังลบกระทู้...',
                    text: 'กรุณารอสักครู่',
                    icon: 'info',
                    allowOutsideClick: false,
                    showConfirmButton: false,
                    didOpen: () => {
                        Swal.showLoading();
                    }
                });

                const formData = new FormData();
                formData.append('topic_id', topicId);

                if (isUserLoggedIn && userInfo) {
                    formData.append('fixed_user_id', userInfo.user_id);
                    formData.append('user_type', userInfo.user_type);
                    formData.append('original_session_id', userInfo.id);
                    formData.append('user_email', userInfo.email);
                }

                if (!isUserLoggedIn) {
                    const guestSessions = JSON.parse(localStorage.getItem('guest_topic_sessions') || '{}');
                    const sessionToken = guestSessions[topicId];
                    if (sessionToken) {
                        formData.append('guest_session_token', JSON.stringify(sessionToken));
                    }
                }

                fetch('<?= site_url("Pages/delete_topic"); ?>', {
                    method: 'POST',
                    body: formData
                })
                    .then(response => response.json())
                    .then(data => {
                        if (data.success) {
                            if (!isUserLoggedIn) {
                                const guestSessions = JSON.parse(localStorage.getItem('guest_topic_sessions') || '{}');
                                delete guestSessions[topicId];
                                localStorage.setItem('guest_topic_sessions', JSON.stringify(guestSessions));
                            }

                            Swal.fire({
                                icon: 'success',
                                title: 'ลบกระทู้สำเร็จ!',
                                text: 'กระทู้ถูกลบเรียบร้อยแล้ว',
                                timer: 2000,
                                showConfirmButton: false,
                                willClose: () => {
                                    window.location.reload();
                                }
                            });
                        } else {
                            throw new Error(data.message || 'เกิดข้อผิดพลาดในการลบกระทู้');
                        }
                    })
                    .catch(error => {
                        console.error('Delete topic error:', error);
                        Swal.fire({
                            icon: 'error',
                            title: 'เกิดข้อผิดพลาด!',
                            text: error.message || 'ไม่สามารถลบกระทู้ได้ กรุณาลองใหม่อีกครั้ง',
                            confirmButtonColor: '#dc3545'
                        });
                    });
            }
        });
    }

    // *** ฟังก์ชันแก้ไข Reply ***
    function editReply(replyId) {
        console.log('📝 Starting edit for reply:', replyId);

        if (currentEditingReplyId && currentEditingReplyId !== replyId) {
            cancelReplyEdit(currentEditingReplyId);
        }

        currentEditingReplyId = replyId;

        const replyContent = document.querySelector(`.reply-content-${replyId}`);
        const editContainer = document.querySelector(`.reply-edit-form-container-${replyId}`);

        if (!replyContent || !editContainer) {
            console.error('❌ Cannot find reply content or edit container for ID:', replyId);
            return;
        }

        const detailDiv = replyContent.querySelector('.mb-3');
        let originalDetail = '';

        if (detailDiv) {
            const spanElement = detailDiv.querySelector('span');
            if (spanElement) {
                originalDetail = spanElement.textContent.trim();
            } else {
                for (let node of detailDiv.childNodes) {
                    if (node.nodeType === Node.TEXT_NODE && node.textContent.trim()) {
                        originalDetail = node.textContent.trim();
                        break;
                    }
                }
            }
        }

        console.log('📋 Original reply detail (text only):', originalDetail);

        const editForm = createReplyEditForm(replyId, originalDetail);
        editContainer.innerHTML = editForm;

        replyContent.style.display = 'none';
        editContainer.style.display = 'block';

        setTimeout(() => {
            const textarea = editContainer.querySelector('textarea');
            if (textarea) {
                textarea.focus();
                textarea.select();
            }
        }, 100);
    }

    // *** ฟังก์ชันสร้างฟอร์มแก้ไข Reply ***
    function createReplyEditForm(replyId, originalDetail) {
        const nameField = isUserLoggedIn ?
            `<input type="text" name="q_a_reply_by" class="form-control form-control-sm" value="${userInfo.name}" readonly>` :
            `<input type="text" name="q_a_reply_by" class="form-control form-control-sm" placeholder="ชื่อ" required>`;

        const emailField = isUserLoggedIn && userInfo.email ?
            `<input type="email" name="q_a_reply_email" class="form-control form-control-sm" value="${userInfo.email}" readonly>` :
            `<input type="email" name="q_a_reply_email" class="form-control form-control-sm" required placeholder="อีเมล">`;

        return `
        <div class="p-3" style="background: linear-gradient(135deg, #fff9e6 0%, #ffeaa7 20%, #fff9e6 100%); border: 2px solid rgba(255, 193, 7, 0.3); border-radius: 15px;">
            <h6 class="text-warning fw-bold mb-3">
                <i class="fas fa-edit me-2"></i>แก้ไขการตอบกลับ
            </h6>
            
            <form onsubmit="return handleReplyEditSubmit(this, event, ${replyId})">
                <input type="hidden" name="reply_id" value="${replyId}">
                <input type="hidden" name="remove_old_images" value="1">
                
                <div class="row mb-2">
                    <div class="col-6">
                        <label class="form-label form-label-sm text-warning fw-bold">ชื่อ</label>
                        ${nameField}
                    </div>
                    <div class="col-6">
                        <label class="form-label form-label-sm text-warning fw-bold">อีเมล</label>
                        ${emailField}
                    </div>
                </div>
                
                <div class="mb-3">
                    <label class="form-label form-label-sm text-warning fw-bold">รายละเอียด</label>
                    <textarea name="q_a_reply_detail" class="form-control" rows="4" required>${originalDetail}</textarea>
                </div>
                
                <div class="row">
                    <div class="col-8">
                        <label class="form-label form-label-sm text-warning fw-bold">รูปภาพใหม่ (จะแทนที่รูปเก่าทั้งหมด)</label>
                        <input type="file" name="q_a_reply_imgs[]" class="form-control form-control-sm" accept="image/*" multiple>
                        <small class="text-muted">
                            <i class="fas fa-info-circle me-1"></i>รองรับ JPG, PNG, GIF, WebP (สูงสุด 5 ไฟล์)<br>
                            <i class="fas fa-exclamation-triangle me-1 text-warning"></i><strong>หมายเหตุ:</strong> รูปภาพเก่าจะถูกลบออกทั้งหมด
                        </small>
                    </div>
                    <div class="col-4 d-flex gap-1 align-items-end">
                        <button type="submit" class="btn btn-warning btn-sm">
                            <i class="fas fa-save me-1"></i>บันทึก
                        </button>
                        <button type="button" class="btn btn-secondary btn-sm" onclick="cancelReplyEdit(${replyId})">
                            <i class="fas fa-times me-1"></i>ยกเลิก
                        </button>
                    </div>
                </div>
            </form>
        </div>
    `;
    }

    // *** ฟังก์ชันยกเลิกการแก้ไข Reply ***
    function cancelReplyEdit(replyId) {
        const replyContent = document.querySelector(`.reply-content-${replyId}`);
        const editContainer = document.querySelector(`.reply-edit-form-container-${replyId}`);

        if (replyContent && editContainer) {
            replyContent.style.display = 'block';
            editContainer.style.display = 'none';
            editContainer.innerHTML = '';
        }

        currentEditingReplyId = null;
    }


    /**
 * ฟังก์ชันจัดการการส่งฟอร์มแก้ไข Reply - Enhanced Version
 * @param {HTMLFormElement} form - Form element
 * @param {Event} event - Submit event
 * @param {string|number} replyId - Reply ID
 */
    function handleReplyEditSubmit(form, event, replyId) {
        event.preventDefault();

        const submitBtn = form.querySelector('button[type="submit"]');
        const originalContent = submitBtn.innerHTML;

        // ตรวจสอบว่าไม่ได้ส่งซ้ำ
        if (submitBtn.disabled) {
            console.log('Reply edit form submission already in progress');
            return false;
        }

        // ตรวจสอบความถูกต้องของฟอร์ม
        if (!form.checkValidity()) {
            form.reportValidity();
            return false;
        }

        // ปิดการใช้งานปุ่มและแสดง Loading
        submitBtn.disabled = true;
        submitBtn.innerHTML = '<i class="fas fa-spinner fa-spin me-1"></i>กำลังบันทึก...';

        // เตรียมข้อมูลฟอร์ม
        const formData = new FormData(form);

        // เพิ่มข้อมูล User ถ้า Login อยู่
        if (isUserLoggedIn && userInfo) {
            formData.append('fixed_user_id', userInfo.user_id);
            formData.append('user_type', userInfo.user_type);
            formData.append('original_session_id', userInfo.id);
            formData.append('user_email', userInfo.email);
        }

        // บอกให้ส่ง JSON response
        formData.append('ajax_request', '1');

        // Log ข้อมูลที่ส่ง
        console.log('🔄 Submitting reply edit form for reply:', replyId);

        // ส่ง Request
        fetch('<?= site_url("Pages/update_reply"); ?>', {
            method: 'POST',
            headers: {
                'X-Requested-With': 'XMLHttpRequest'
            },
            body: formData
        })
            .then(response => {
                console.log('📨 Reply edit response status:', response.status);
                const contentType = response.headers.get('content-type');

                // ตรวจสอบ Content Type และแปลง Response
                if (contentType && contentType.includes('application/json')) {
                    console.log('✅ JSON response detected');
                    return response.json();
                }

                // ถ้าไม่ใช่ JSON และ status OK
                if (response.ok) {
                    console.log('📄 HTML response received for success');
                    return response.text().then(html => {
                        if (html.includes('แก้ไขการตอบกลับสำเร็จ') || html.includes('save_success')) {
                            return {
                                success: true,
                                message: 'แก้ไขการตอบกลับสำเร็จ',
                                reply_id: replyId
                            };
                        }
                        return { success: true };
                    });
                }

                // จัดการ HTTP Error Status
                console.log('⚠️ HTTP Error Status:', response.status);

                // ลองอ่าน JSON ก่อน
                if (contentType && contentType.includes('application/json')) {
                    console.log('📋 Reading JSON error response...');
                    return response.json().then(data => {
                        console.log('📄 JSON error data received:', data);
                        return data;
                    });
                }

                // ถ้าไม่มี JSON ให้ลองอ่าน text
                console.log('📄 Reading text error response...');
                return response.text().then(html => {
                    // ตรวจสอบ URL detection จาก HTML
                    if (html.includes('URL') || html.includes('ลิงก์') ||
                        html.includes('url_content') || html.includes('url_detected')) {
                        return {
                            success: false,
                            url_detected: true,
                            message: 'ไม่อนุญาตให้มี URL หรือลิงก์ในข้อความ',
                            error_type: 'url_content'
                        };
                    }

                    // ตรวจสอบ Vulgar detection
                    if (html.includes('vulgar') || html.includes('คำไม่เหมาะสม') ||
                        html.includes('vulgar_content')) {
                        return {
                            success: false,
                            vulgar_detected: true,
                            vulgar_words: [],
                            message: 'พบคำไม่เหมาะสม',
                            error_type: 'vulgar_content'
                        };
                    }

                    // Generic error
                    return {
                        success: false,
                        message: `HTTP ${response.status}: ${response.statusText}`,
                        error_type: 'http_error'
                    };
                });
            })
            .then(data => {
                console.log('✅ Reply edit response data processed:', data);

                // ตรวจสอบคำหยาบ (Vulgar Detection)
                if (data.vulgar_detected === true) {
                    console.log('🚨 Vulgar content detected:', data.vulgar_words);

                    if (typeof showVulgarErrorModalWithWords === 'function') {
                        showVulgarErrorModalWithWords(data.vulgar_words || []);
                    } else {
                        Swal.fire({
                            icon: 'warning',
                            title: 'พบคำไม่เหมาะสม',
                            text: 'กรุณาตรวจสอบและแก้ไขข้อความของคุณ',
                            confirmButtonText: 'ตกลง'
                        });
                    }

                    submitBtn.disabled = false;
                    submitBtn.innerHTML = originalContent;
                    return;
                }

                // ตรวจสอบ URL (URL Detection)
                if (data.url_detected === true) {
                    console.log('🔗 URL content detected');

                    if (typeof showUrlDetectedModal === 'function') {
                        showUrlDetectedModal();
                    } else {
                        Swal.fire({
                            icon: 'warning',
                            title: 'พบ URL ในข้อความ',
                            text: 'ไม่อนุญาตให้มี URL หรือลิงก์ในข้อความ',
                            confirmButtonText: 'ตกลง'
                        });
                    }

                    submitBtn.disabled = false;
                    submitBtn.innerHTML = originalContent;
                    return;
                }

                // ตรวจสอบ Error อื่นๆ
                if (data.success === false) {
                    console.error('❌ Reply edit failed:', data.message);

                    let errorMessage = data.message || 'เกิดข้อผิดพลาดในการแก้ไขการตอบกลับ';

                    switch (data.error_type) {
                        case 'validation_error':
                            errorMessage = 'ข้อมูลไม่ครบถ้วน กรุณาตรวจสอบและลองใหม่';
                            break;
                        case 'permission_denied':
                            errorMessage = 'คุณไม่มีสิทธิ์แก้ไขการตอบกลับนี้';
                            break;
                        case 'reply_not_found':
                            errorMessage = 'ไม่พบการตอบกลับที่ต้องการแก้ไข';
                            break;
                        case 'database_error':
                            errorMessage = 'เกิดข้อผิดพลาดในการบันทึกข้อมูล';
                            break;
                        default:
                            errorMessage = data.message || 'เกิดข้อผิดพลาดในการแก้ไขการตอบกลับ';
                    }

                    Swal.fire({
                        icon: 'error',
                        title: 'เกิดข้อผิดพลาด',
                        text: errorMessage,
                        confirmButtonText: 'ตกลง',
                        confirmButtonColor: '#dc3545'
                    });

                    submitBtn.disabled = false;
                    submitBtn.innerHTML = originalContent;
                    return;
                }

                // สำเร็จ - แสดงผลลัพธ์
                console.log('✅ Reply edit successful for reply:', replyId);

                Swal.fire({
                    icon: 'success',
                    title: 'แก้ไขการตอบกลับสำเร็จ!',
                    html: `
            <div class="text-center">
                <i class="fas fa-check-circle text-success mb-3" style="font-size: 3rem;"></i>
                <p class="mb-2">การตอบกลับของคุณได้รับการแก้ไขเรียบร้อยแล้ว</p>
                <small class="text-muted">กำลังโหลดข้อมูลใหม่...</small>
            </div>
        `,
                    timer: 1500,
                    showConfirmButton: false,
                    allowOutsideClick: false,
                    allowEscapeKey: false,
                    didClose: () => {
                        console.log('🔄 Reloading page to show updated reply content');
                        window.location.reload();
                    }
                });
            })
            .catch(error => {
                console.error('❌ Reply edit submission error:', error);

                // จัดการ Error ตามประเภท
                if (error.message.includes('vulgar') || error.message.includes('คำไม่เหมาะสม')) {
                    if (typeof showVulgarErrorModalWithWords === 'function') {
                        showVulgarErrorModalWithWords([]);
                    } else {
                        Swal.fire({
                            icon: 'warning',
                            title: 'พบคำไม่เหมาะสม',
                            text: 'กรุณาตรวจสอบและแก้ไขข้อความของคุณ'
                        });
                    }
                } else if (error.message.includes('URL') || error.message.includes('ลิงก์')) {
                    if (typeof showUrlDetectedModal === 'function') {
                        showUrlDetectedModal();
                    } else {
                        Swal.fire({
                            icon: 'warning',
                            title: 'พบ URL ในข้อความ',
                            text: 'ไม่อนุญาตให้มี URL หรือลิงก์ในข้อความ'
                        });
                    }
                } else {
                    let errorTitle = 'เกิดข้อผิดพลาด';
                    let errorMessage = error.message || 'ไม่สามารถแก้ไขการตอบกลับได้ กรุณาลองใหม่อีกครั้ง';

                    if (error.message.includes('HTTP 400')) {
                        errorTitle = 'ข้อมูลไม่ถูกต้อง';
                        errorMessage = 'กรุณาตรวจสอบข้อมูลและลองใหม่อีกครั้ง';
                    } else if (error.message.includes('HTTP 403')) {
                        errorTitle = 'ไม่มีสิทธิ์เข้าถึง';
                        errorMessage = 'คุณไม่มีสิทธิ์แก้ไขการตอบกลับนี้';
                    } else if (error.message.includes('HTTP 404')) {
                        errorTitle = 'ไม่พบข้อมูล';
                        errorMessage = 'ไม่พบการตอบกลับที่ต้องการแก้ไข';
                    } else if (error.message.includes('HTTP 500')) {
                        errorTitle = 'ข้อผิดพลาดเซิร์ฟเวอร์';
                        errorMessage = 'เกิดข้อผิดพลาดในระบบ กรุณาลองใหม่ภายหลัง';
                    }

                    Swal.fire({
                        icon: 'error',
                        title: errorTitle,
                        text: errorMessage,
                        confirmButtonText: 'ตกลง',
                        confirmButtonColor: '#dc3545'
                    });
                }

                submitBtn.disabled = false;
                submitBtn.innerHTML = originalContent;
            });

        return false;
    }

    // ==============================================
    // ฟังก์ชันช่วยเหลือ
    // ==============================================

    // *** ฟังก์ชันดึงคำหยาบจาก HTML response ***
    function extractVulgarWordsFromHTML(html) {
        try {
            const vulgarMatch = html.match(/vulgar_words['"]\s*:\s*\[(.*?)\]/);
            if (vulgarMatch && vulgarMatch[1]) {
                return vulgarMatch[1]
                    .split(',')
                    .map(word => word.replace(/['"]/g, '').trim())
                    .filter(word => word.length > 0);
            }
        } catch (e) {
            console.log('Could not extract vulgar words from HTML:', e);
        }
        return [];
    }

    // ==============================================
    // เพิ่ม CSS สำหรับ Modal (ถ้ายังไม่มี)
    // ==============================================

    // *** เพิ่ม CSS สำหรับ Modal ***
    const vulgarModalStyles = `
<style>
.vulgar-error-modal {
    border-radius: 20px !important;
    box-shadow: 0 20px 60px rgba(220, 53, 69, 0.3) !important;
}

.vulgar-error-title {
    color: #721c24 !important;
    font-size: 1.4rem !important;
    font-weight: 700 !important;
}

.vulgar-error-content {
    font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif !important;
}

.url-error-modal {
    border-radius: 20px !important;
    box-shadow: 0 20px 60px rgba(255, 193, 7, 0.3) !important;
}

.url-error-title {
    color: #856404 !important;
    font-size: 1.4rem !important;
    font-weight: 700 !important;
}

.swal2-confirm.swal2-styled {
    border-radius: 12px !important;
    font-weight: 600 !important;
    padding: 0.7rem 1.5rem !important;
    transition: all 0.3s ease !important;
}

.swal2-confirm.swal2-styled:hover {
    transform: translateY(-2px) !important;
    box-shadow: 0 6px 20px rgba(220, 53, 69, 0.4) !important;
}

/* Animation สำหรับ Modal */
@keyframes modalPulse {
    0% { transform: scale(1); }
    50% { transform: scale(1.05); }
    100% { transform: scale(1); }
}

.vulgar-error-modal .swal2-icon {
    animation: modalPulse 2s infinite;
}

/* Responsive */
@media (max-width: 768px) {
    .vulgar-error-modal,
    .url-error-modal {
        width: 95vw !important;
        margin: 0 auto !important;
    }
    
    .swal2-title {
        font-size: 1.2rem !important;
    }
    
    .swal2-html-container {
        font-size: 0.9rem !important;
    }
}
</style>
`;

    // *** เพิ่ม CSS เข้าไปใน head เมื่อโหลดหน้า ***
    if (!document.getElementById('vulgar-modal-styles')) {
        const styleElement = document.createElement('style');
        styleElement.id = 'vulgar-modal-styles';
        styleElement.innerHTML = vulgarModalStyles.replace(/<\/?style>/g, '');
        document.head.appendChild(styleElement);
    }

// ==============================================
// Flash Messages สำหรับคำหยาบ (ถ้ามีจาก PHP)
// ==============================================

// *** เพิ่ม Flash Messages สำหรับคำหยาบในหน้า Q&A ***
<?php if ($this->session->flashdata('save_vulgar')): ?>
        document.addEventListener('DOMContentLoaded', function () {
            const vulgarWords = <?= json_encode($this->session->flashdata('vulgar_words') ?: []); ?>;
            setTimeout(() => {
                if (typeof showVulgarErrorModalWithWords === 'function') {
                    showVulgarErrorModalWithWords(vulgarWords);
                } else {
                    showVulgarErrorModal();
                }
            }, 1000);
        });
<?php endif; ?>

<?php if ($this->session->flashdata('save_url_detected')): ?>
        document.addEventListener('DOMContentLoaded', function () {
            setTimeout(() => {
                showUrlDetectedModal();
            }, 1000);
        });
<?php endif; ?>




        // *** ฟังก์ชันลบ Reply ***
        function deleteReply(replyId) {
            Swal.fire({
                title: 'ยืนยันการลบการตอบกลับ',
                text: 'คุณแน่ใจหรือไม่ที่จะลบการตอบกลับนี้?',
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#dc3545',
                cancelButtonColor: '#6c757d',
                confirmButtonText: '<i class="fas fa-trash me-2"></i>ลบการตอบกลับ',
                cancelButtonText: '<i class="fas fa-times me-2"></i>ยกเลิก'
            }).then((result) => {
                if (result.isConfirmed) {
                    const formData = new FormData();
                    formData.append('reply_id', replyId);

                    if (isUserLoggedIn && userInfo) {
                        formData.append('fixed_user_id', userInfo.user_id);
                        formData.append('user_type', userInfo.user_type);
                        formData.append('original_session_id', userInfo.id);
                        formData.append('user_email', userInfo.email);
                    }

                    fetch('<?= site_url("Pages/delete_reply"); ?>', {
                        method: 'POST',
                        body: formData
                    })
                        .then(response => response.json())
                        .then(data => {
                            if (data.success) {
                                Swal.fire({
                                    icon: 'success',
                                    title: 'ลบการตอบกลับสำเร็จ!',
                                    timer: 2000,
                                    showConfirmButton: false,
                                    willClose: () => {
                                        window.location.reload();
                                    }
                                });
                            } else {
                                throw new Error(data.message || 'เกิดข้อผิดพลาดในการลบการตอบกลับ');
                            }
                        })
                        .catch(error => {
                            Swal.fire({
                                icon: 'error',
                                title: 'เกิดข้อผิดพลาด!',
                                text: error.message || 'ไม่สามารถลบการตอบกลับได้'
                            });
                        });
                }
            });
        }

    // *** ฟังก์ชันตรวจสอบไฟล์ ***
    async function validateReplyFileInput(input) {
        const allowedTypes = ['image/jpeg', 'image/png', 'image/gif', 'image/webp'];
        const files = Array.from(input.files);
        const maxFiles = 5;
        const maxFileSize = 5 * 1024 * 1024; // 5MB

        console.log('Validating reply files:', files.length);

        if (files.length > maxFiles) {
            Swal.fire({
                icon: 'error',
                title: 'ตรวจพบปัญหา',
                text: `คุณสามารถอัพโหลดได้ไม่เกิน ${maxFiles} รูปภาพ`
            });
            input.value = '';
            return;
        }

        for (let file of files) {
            if (!allowedTypes.includes(file.type.toLowerCase())) {
                Swal.fire({
                    icon: 'error',
                    title: 'ตรวจพบปัญหา',
                    text: `ไฟล์ "${file.name}" ไม่ใช่รูปภาพที่รองรับ\nรองรับเฉพาะ JPG, PNG, GIF, WebP เท่านั้น`
                });
                input.value = '';
                return;
            }

            if (file.size > maxFileSize) {
                Swal.fire({
                    icon: 'error',
                    title: 'ตรวจพบปัญหา',
                    text: `ไฟล์ "${file.name}" มีขนาดใหญ่เกินไป\nขนาดไฟล์ต้องไม่เกิน 5 MB`
                });
                input.value = '';
                return;
            }
        }
    }

    async function validateEditFileInput(input) {
        const allowedTypes = ['image/jpeg', 'image/png', 'image/gif', 'image/webp'];
        const files = Array.from(input.files);
        const maxFiles = 5;
        const maxFileSize = 5 * 1024 * 1024; // 5MB

        console.log('Validating edit files:', files.length);

        if (files.length > maxFiles) {
            Swal.fire({
                icon: 'error',
                title: 'ตรวจพบปัญหา',
                text: `คุณสามารถอัพโหลดได้ไม่เกิน ${maxFiles} รูปภาพ`
            });
            input.value = '';
            return;
        }

        for (let file of files) {
            if (!allowedTypes.includes(file.type.toLowerCase())) {
                Swal.fire({
                    icon: 'error',
                    title: 'ตรวจพบปัญหา',
                    text: `ไฟล์ "${file.name}" ไม่ใช่รูปภาพที่รองรับ\nรองรับเฉพาะ JPG, PNG, GIF, WebP เท่านั้น`
                });
                input.value = '';
                return;
            }

            if (file.size > maxFileSize) {
                Swal.fire({
                    icon: 'error',
                    title: 'ตรวจพบปัญหา',
                    text: `ไฟล์ "${file.name}" มีขนาดใหญ่เกินไป\nขนาดไฟล์ต้องไม่เกิน 5 MB`
                });
                input.value = '';
                return;
            }
        }
    }

// *** Flash messages (รอให้ Swal โหลดเสร็จ) ***
<?php if ($this->session->flashdata('save_success')): ?>
        document.addEventListener('DOMContentLoaded', function () {
            const waitForSwal = setInterval(() => {
                if (typeof Swal !== 'undefined') {
                    clearInterval(waitForSwal);
                    Swal.fire({ icon: 'success', title: 'สำเร็จ!', text: 'บันทึกข้อมูลเรียบร้อยแล้ว', timer: 3000, showConfirmButton: false });
                }
            }, 100);
        });
<?php endif; ?>

<?php if ($this->session->flashdata('save_error')): ?>
        document.addEventListener('DOMContentLoaded', function () {
            const waitForSwal = setInterval(() => {
                if (typeof Swal !== 'undefined') {
                    clearInterval(waitForSwal);
                    Swal.fire({ icon: 'error', title: 'เกิดข้อผิดพลาด!', text: 'ไม่สามารถบันทึกข้อมูลได้' });
                }
            }, 100);
        });
<?php endif; ?>

<?php if ($this->session->flashdata('save_vulgar')): ?>
        document.addEventListener('DOMContentLoaded', function () {
            const waitForSwal = setInterval(() => {
                if (typeof Swal !== 'undefined') {
                    clearInterval(waitForSwal);
                    Swal.fire({ icon: 'warning', title: 'ตรวจพบคำไม่เหมาะสม!', text: 'กรุณาแก้ไขข้อความและลองใหม่อีกครั้ง' });
                }
            }, 100);
        });
<?php endif; ?>

        // *** ป้องกัน Extension Errors ***
        (function () {
            'use strict';

            const originalError = console.error;
            console.error = function (...args) {
                const message = args.join(' ');
                if (message.includes('message channel closed') ||
                    message.includes('Extension context invalidated') ||
                    message.includes('listener indicated an asynchronous response')) {
                    return;
                }
                originalError.apply(console, args);
            };

            window.addEventListener('unhandledrejection', function (event) {
                const error = event.reason;
                if (error && error.message &&
                    (error.message.includes('message channel closed') ||
                        error.message.includes('Extension context') ||
                        error.message.includes('listener indicated an asynchronous response'))) {
                    event.preventDefault();
                    console.log('🛡️ Blocked extension error:', error.message);
                }
            });

            window.addEventListener('error', function (event) {
                if (event.message &&
                    (event.message.includes('message channel closed') ||
                        event.message.includes('Extension context') ||
                        event.message.includes('listener indicated an asynchronous response'))) {
                    event.preventDefault();
                    console.log('🛡️ Blocked extension error:', event.message);
                    return false;
                }
            });
        })();

    // *** ฟังก์ชันสำหรับ Debug ***
    function debugReplyStructure(replyId) {
        console.log('=== DEBUG REPLY STRUCTURE ===');
        const topicCard = document.getElementById('comment-' + replyId);
        if (topicCard) {
            console.log('Topic card found:', topicCard);

            const repliesSection = topicCard.querySelector('.replies-section-' + replyId);
            console.log('Replies section:', repliesSection);

            if (repliesSection) {
                const allReplies = repliesSection.querySelectorAll('.reply-item');
                console.log('Total replies found:', allReplies.length);

                allReplies.forEach((reply, index) => {
                    console.log(`Reply ${index + 1}:`, reply.id, reply);
                });
            }
        }
        console.log('==============================');
    }

    function debugReplyTimestamps(topicId) {
        console.log('=== DEBUG REPLY TIMESTAMPS ===');
        const topicCard = document.getElementById('comment-' + topicId);
        if (topicCard) {
            const repliesSection = topicCard.querySelector('.replies-section-' + topicId);
            if (repliesSection) {
                const allReplies = repliesSection.querySelectorAll('.reply-item');
                console.log('Total replies:', allReplies.length);

                allReplies.forEach((reply, index) => {
                    const timeElement = reply.querySelector('small');
                    const detailElement = reply.querySelector('.mb-3 span');

                    console.log(`Reply ${index + 1}:`, {
                        id: reply.id,
                        timeText: timeElement ? timeElement.textContent.trim() : 'No time',
                        contentPreview: detailElement ? detailElement.textContent.trim().substring(0, 30) + '...' : 'No content'
                    });
                });
            }
        }
        console.log('===============================');
    }

    // *** เพิ่มฟังก์ชันป้องกัน Auto-refresh และ Scroll กลับด้านบน ***
    function preventAutoRefresh() {
        // *** ปิดการแสดง reload confirmation popup ***
        window.addEventListener('beforeunload', function (e) {
            // ลบการแสดง popup reload confirmation ทั้งหมด
            // ไม่ต้องมีการ preventDefault หรือ returnValue
            return undefined;
        });

        // ป้องกันการ scroll restoration ของ browser
        if ('scrollRestoration' in history) {
            history.scrollRestoration = 'manual';
        }

        // ป้องกันการ scroll กลับด้านบนหลัง page load
        let pageLoadScrollPrevented = false;
        window.addEventListener('scroll', function (e) {
            // ถ้าเพิ่งโหลดหน้าและมี scroll action รอดำเนินการ
            if (!pageLoadScrollPrevented && (sessionStorage.getItem('scrollToNewReply') || sessionStorage.getItem('editSuccess'))) {
                const scrollY = window.scrollY;

                // ถ้า scroll ขึ้นไปด้านบน (น้อยกว่า 200px) ให้ป้องกัน
                if (scrollY < 200) {
                    e.preventDefault();

                    // รอแล้วค่อย scroll ไปที่ตำแหน่งที่ถูกต้อง
                    setTimeout(() => {
                        if (sessionStorage.getItem('scrollToNewReply')) {
                            scrollToNewReplyAfterReload();
                        }
                    }, 500);

                    pageLoadScrollPrevented = true;
                    return false;
                }
            }
        }, { passive: false });

        // ป้องกันการ navigation ที่ไม่จำเป็น
        let isScrolling = false;
        window.addEventListener('scroll', function () {
            if (!isScrolling) {
                isScrolling = true;
                setTimeout(() => {
                    isScrolling = false;
                    // อัปเดต history เพื่อป้องกันการกลับไปด้านบน
                    if (window.scrollY > 100) {
                        history.replaceState(null, null, window.location.href);
                    }
                }, 100);
            }
        });

        // ป้องกันการ automatic scroll เมื่อ focus ไปที่ element
        document.addEventListener('focusin', function (e) {
            if (sessionStorage.getItem('scrollToNewReply')) {
                e.preventDefault();
            }
        });

        //console.log('✅ Enhanced scroll protection initialized (reload popup disabled)');
    }

    // เรียกใช้ฟังก์ชันป้องกัน auto-refresh
    preventAutoRefresh();


</script>


<script>

    // *** เพิ่ม Script นี้ในหน้า กระทู้ถาม-ตอบ (q_a) ***
    // *** ใส่ไว้ใน section <script> ที่มีอยู่แล้ว หรือเพิ่มเป็น script tag ใหม่ ***


    // *** ฟังก์ชัน handle hash จาก notification ***
    function handleNotificationHashNavigation(hash) {
        //console.log('🎯 Q&A: Handling notification hash navigation:', hash);

        // ลองหา element ในหน้าปัจจุบัน
        const targetElement = document.getElementById(hash);

        if (targetElement) {
            // console.log('✅ Q&A: Found target element immediately:', targetElement);

            // เพิ่ม highlight effect แบบ notification (สีทอง)
            targetElement.style.transition = 'all 0.6s ease';
            targetElement.style.background = 'linear-gradient(135deg, rgba(255, 215, 0, 0.4) 0%, rgba(255, 215, 0, 0.1) 100%)';
            targetElement.style.border = '3px solid rgba(255, 215, 0, 0.8)';
            targetElement.style.transform = 'scale(1.03)';
            targetElement.style.boxShadow = '0 15px 35px rgba(255, 215, 0, 0.5)';

            // เลื่อนไปที่ element
            targetElement.scrollIntoView({
                behavior: 'smooth',
                block: 'center',
                inline: 'nearest'
            });

            // แสดง success message
            setTimeout(() => {
                if (typeof Swal !== 'undefined') {
                    Swal.fire({
                        icon: 'success',
                        title: '🎯 พบกระทู้แล้ว!',
                        text: 'พบกระทู้จากการแจ้งเตือนเรียบร้อย',
                        timer: 2500,
                        showConfirmButton: false,
                        position: 'top-end',
                        toast: true,
                        background: 'linear-gradient(135deg, #fff9e6 0%, #ffeaa7 100%)',
                        color: '#856404'
                    });
                }
            }, 800);

            // ลบ highlight หลัง 6 วินาที
            setTimeout(() => {
                targetElement.style.background = '';
                targetElement.style.border = '';
                targetElement.style.transform = '';
                targetElement.style.boxShadow = '';
            }, 6000);

            //console.log('✅ Q&A: Successfully scrolled to notification target');

        } else {
            console.warn('❌ Q&A: Target element not found, trying alternatives...');

            // ลองหา element ที่เกี่ยวข้อง
            const relatedElement = findQARelatedElement(hash);
            if (relatedElement) {
                console.log('🔍 Q&A: Found related element:', relatedElement.id);
                handleNotificationHashNavigation(relatedElement.id);
                return;
            }

            // ถ้าไม่พบ element ให้แสดง warning และลองค้นหาหน้าอื่น
            if (typeof Swal !== 'undefined') {
                Swal.fire({
                    icon: 'warning',
                    title: '🔍 กำลังค้นหากระทู้...',
                    text: 'ไม่พบกระทู้ในหน้านี้ กำลังค้นหาในหน้าอื่น',
                    timer: 3000,
                    showConfirmButton: false,
                    position: 'top-end',
                    toast: true
                });
            }

            // ลองค้นหาผ่าน API
            const topicId = extractQATopicId(hash);
            if (topicId) {
                findTopicPageFromQA(topicId, hash);
            }
        }
    }

    // *** ฟังก์ชัน handle hash แบบ direct navigation ***
    function handleDirectHashNavigation(hash) {
        //console.log('🎯 Q&A: Handling direct hash navigation:', hash);

        const targetElement = document.getElementById(hash);
        if (targetElement) {
            // console.log('✅ Q&A: Found direct target element:', targetElement);

            // เพิ่ม highlight effect แบบปกติ (สีเขียว)
            targetElement.style.transition = 'all 0.5s ease';
            targetElement.style.background = 'linear-gradient(135deg, rgba(40, 167, 69, 0.2) 0%, rgba(40, 167, 69, 0.1) 100%)';
            targetElement.style.border = '2px solid rgba(40, 167, 69, 0.5)';
            targetElement.style.transform = 'scale(1.02)';
            targetElement.style.boxShadow = '0 8px 25px rgba(40, 167, 69, 0.3)';

            // เลื่อนไปที่ element
            targetElement.scrollIntoView({
                behavior: 'smooth',
                block: 'center',
                inline: 'nearest'
            });

            // ลบ highlight หลัง 4 วินาที
            setTimeout(() => {
                targetElement.style.background = '';
                targetElement.style.border = '';
                targetElement.style.transform = '';
                targetElement.style.boxShadow = '';
            }, 4000);

            //  console.log('✅ Q&A: Successfully scrolled to direct target');
        } else {
            console.warn('❌ Q&A: Direct target not found:', hash);
        }
    }

    // *** ฟังก์ชันค้นหา element ที่เกี่ยวข้องใน Q&A ***
    function findQARelatedElement(hash) {
        //console.log('🔍 Q&A: Searching for related element:', hash);

        // แสดงรายการ elements ที่มีอยู่ทั้งหมด
        // console.log('📋 Available comment elements:');
        const commentElements = document.querySelectorAll('[id^="comment-"]');
        commentElements.forEach((el, index) => {
            //   console.log(`  ${index + 1}. ${el.id}`);
        });

        console.log('📋 Available reply elements:');
        const replyElements = document.querySelectorAll('[id^="reply-"]');
        replyElements.forEach((el, index) => {
            //  console.log(`  ${index + 1}. ${el.id}`);
        });

        // รูปแบบที่ต้องลอง
        const patterns = [
            hash,                    // hash เดิม
            `comment-${hash}`,       // comment-XX
            `reply-${hash}`,         // reply-XX
            `topic-${hash}`,         // topic-XX
            `post-${hash}`           // post-XX
        ];

        // ถ้า hash เป็น comment-XX ให้ลองหา XX
        const commentMatch = hash.match(/comment-(\d+)/);
        if (commentMatch) {
            patterns.push(commentMatch[1]);
        }

        // ถ้า hash เป็น reply-XX ให้ลองหา parent comment
        const replyMatch = hash.match(/reply-(\d+)/);
        if (replyMatch) {
            // ค้นหา reply element แล้วหา parent comment
            const allReplies = document.querySelectorAll('[id^="reply-"]');
            for (let reply of allReplies) {
                const parentComment = reply.closest('[id^="comment-"]');
                if (parentComment) {
                    patterns.push(parentComment.id);
                    //  console.log('🔗 Q&A: Found parent comment for reply:', parentComment.id);
                }
            }
        }

        console.log('🔍 Trying patterns:', patterns);

        // ลอง patterns ทีละตัว
        for (let pattern of patterns) {
            const element = document.getElementById(pattern);
            if (element) {
                //  console.log('✅ Q&A: Found related element with pattern:', pattern);
                return element;
            } else {
                console.log(`❌ Pattern "${pattern}" not found`);
            }
        }

        console.log('❌ Q&A: No related element found');
        return null;
    }


    function isCorrectQAPage(topicId) {
        // ตรวจสอบว่ามี element ของกระทู้ที่ต้องการหรือไม่
        const commentElement = document.getElementById(`comment-${topicId}`);
        if (commentElement) {
            //  console.log(`✅ Found comment-${topicId} on current page`);
            return true;
        }

        // ตรวจสอบ reply elements
        const replyElements = document.querySelectorAll(`[id^="reply-"]`);
        for (let reply of replyElements) {
            // ตรวจสอบว่า reply นี้เป็นของกระทู้ที่ต้องการหรือไม่
            const parentComment = reply.closest(`[id="comment-${topicId}"]`);
            if (parentComment) {
                //  console.log(`✅ Found reply for comment-${topicId} on current page`);
                return true;
            }
        }

        console.log(`❌ comment-${topicId} not found on current page`);
        return false;
    }


    // *** ฟังก์ชันดึง Topic ID จาก hash สำหรับ Q&A ***
    function extractQATopicId(hash) {
        if (!hash) return null;

        const patterns = [
            /comment-(\d+)/,  // comment-77
            /reply-(\d+)/,    // reply-123
            /topic-(\d+)/,    // topic-456
            /post-(\d+)/,     // post-789
            /^(\d+)$/         // 77 (ตัวเลขเปล่า)
        ];

        for (const pattern of patterns) {
            const match = hash.match(pattern);
            if (match && match[1]) {
                const id = parseInt(match[1]);
                console.log('🔢 Q&A: Extracted topic ID:', id, 'from hash:', hash);
                return id;
            }
        }

        console.log('❌ Q&A: Could not extract topic ID from hash:', hash);
        return null;
    }

    // *** ฟังก์ชันค้นหาหน้าที่มีกระทู้ (จากหน้า Q&A) ***
    function findTopicPageFromQA(topicId, originalHash) {
        console.log('🔍 Q&A: Finding page for topic ID:', topicId);

        // แสดง loading message
        if (typeof Swal !== 'undefined') {
            Swal.fire({
                title: 'กำลังค้นหากระทู้...',
                text: 'กรุณารอสักครู่',
                icon: 'info',
                allowOutsideClick: false,
                showConfirmButton: false,
                didOpen: () => {
                    Swal.showLoading();
                }
            });
        }

        // *** แก้ไข: ใช้ Pages/find_topic_page ***
        const apiUrl = '<?= site_url("Pages/find_topic_page"); ?>';
        console.log('📡 API URL:', apiUrl);

        // เรียก API
        fetch(apiUrl, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/x-www-form-urlencoded',
                'X-Requested-With': 'XMLHttpRequest'
            },
            body: `topic_id=${topicId}`
        })
            .then(response => {
                // console.log('📊 Q&A API Response Status:', response.status);
                // console.log('📊 Q&A API Response URL:', response.url);

                // *** แก้ไข: ตรวจสอบ response.ok ก่อน ***
                if (!response.ok) {
                    throw new Error(`HTTP ${response.status}: ${response.statusText}`);
                }

                // ตรวจสอบ Content-Type
                const contentType = response.headers.get('content-type');
                console.log('📊 Content-Type:', contentType);

                // *** แก้ไข: ตรวจสอบ Content-Type ที่ถูกต้อง ***
                if (!contentType || (!contentType.includes('application/json') && !contentType.includes('text/json'))) {
                    console.warn('⚠️ Response is not JSON, reading as text for debugging...');
                    return response.text().then(text => {
                        console.error('❌ API returned non-JSON response:', text.substring(0, 500));
                        throw new Error('API ส่งกลับข้อมูลที่ไม่ใช่ JSON');
                    });
                }

                return response.json();
            })
            .then(data => {
                console.log('📊 Q&A API Response Data:', data);

                // *** แก้ไข: ปิด Swal ก่อนการตรวจสอบ data ***
                if (typeof Swal !== 'undefined') {
                    Swal.close();
                }

                // *** แก้ไข: ตรวจสอบ data.success อย่างถูกต้อง ***
                if (data && data.success === true && data.page) {
                    // console.log(`✅ Q&A: Topic found on page ${data.page}, navigating...`);

                    // สร้าง URL ใหม่
                    const currentUrl = window.location.pathname;
                    const newUrl = `${currentUrl}?page=${data.page}&from_notification=1#${originalHash}`;

                    console.log('🚀 Generated URL:', newUrl);

                    // แสดง success message ก่อน navigate
                    if (typeof Swal !== 'undefined') {
                        Swal.fire({
                            icon: 'success',
                            title: '🎯 พบกระทู้แล้ว!',
                            text: `${data.message || 'พบกระทู้ในหน้า ' + data.page} กำลังพาไป...`,
                            timer: 2000,
                            showConfirmButton: false,
                            didClose: () => {
                                console.log('🚀 Navigating to:', newUrl);
                                window.location.href = newUrl;
                            }
                        });
                    } else {
                        console.log('🚀 Navigating to:', newUrl);
                        window.location.href = newUrl;
                    }

                } else {
                    // *** แก้ไข: แสดง error ที่ถูกต้อง ***
                    const errorMessage = data ?
                        (data.message || 'ไม่สามารถค้นหากระทู้ที่ระบุได้') :
                        'ไม่ได้รับข้อมูลจาก API';

                    console.error('❌ Q&A: Topic search failed:', {
                        success: data ? data.success : 'undefined',
                        page: data ? data.page : 'undefined',
                        message: errorMessage,
                        fullData: data
                    });

                    if (typeof Swal !== 'undefined') {
                        Swal.fire({
                            icon: 'error',
                            title: '❌ ไม่พบกระทู้',
                            text: errorMessage,
                            confirmButtonText: 'ตกลง',
                            footer: `<small>กระทู้ ID: ${topicId}<br>API Response: ${JSON.stringify(data)}</small>`
                        });
                    }
                }
            })
            .catch(error => {
                console.error('🚨 Q&A: Error finding topic page:', error);
                console.error('🚨 Error details:', {
                    message: error.message,
                    stack: error.stack,
                    name: error.name
                });

                // *** แก้ไข: ตรวจสอบ Swal ก่อนปิด ***
                if (typeof Swal !== 'undefined') {
                    // ปิด loading modal ถ้ายังเปิดอยู่
                    try {
                        Swal.close();
                    } catch (e) {
                        // Ignore close errors
                    }

                    let errorMessage = 'ไม่สามารถค้นหากระทู้ได้';
                    let errorDetails = error.message;

                    // ปรับข้อความตามประเภทของ error
                    if (error.message.includes('404')) {
                        errorMessage = 'ระบบค้นหาไม่พร้อมใช้งาน';
                        errorDetails = 'API endpoint ไม่พบ (404)';
                    } else if (error.message.includes('JSON') || error.message.includes('Unexpected token')) {
                        errorMessage = 'ข้อมูลจากเซิร์ฟเวอร์ไม่ถูกต้อง';
                        errorDetails = 'การตอบกลับจากเซิร์ฟเวอร์ไม่ใช่ JSON';
                    } else if (error.message.includes('Failed to fetch') || error.message.includes('NetworkError')) {
                        errorMessage = 'ไม่สามารถเชื่อมต่อเซิร์ฟเวอร์ได้';
                        errorDetails = 'การเชื่อมต่อขาดหาย';
                    } else if (error.message.includes('fetch')) {
                        errorMessage = 'ปัญหาการเชื่อมต่อเครือข่าย';
                        errorDetails = 'ตรวจสอบการเชื่อมต่ออินเทอร์เน็ต';
                    }

                    Swal.fire({
                        icon: 'error',
                        title: '🚨 เกิดข้อผิดพลาด',
                        text: errorMessage,
                        confirmButtonText: 'ตกลง',
                        footer: `<small>รายละเอียด: ${errorDetails}<br>กระทู้ ID: ${topicId}</small>`
                    });
                }

                // *** แก้ไข: Fallback ที่ปลอดภัยกว่า ***
                console.log('🔄 Setting fallback timer to Q&A main page...');
                setTimeout(() => {
                    console.log('🔄 Fallback: Redirecting to Q&A main page');
                    if (window.location.pathname.includes('/q_a')) {
                        window.location.href = '<?= site_url("Pages/q_a"); ?>';
                    }
                }, 3000);
            });
    }


    function findTopicPageFromPublic(topicId, originalHash) {
        // console.log('🔍 Public: Finding page for topic ID:', topicId);

        // แสดง loading message
        Swal.fire({
            title: 'กำลังค้นหากระทู้...',
            text: 'กรุณารอสักครู่',
            icon: 'info',
            allowOutsideClick: false,
            showConfirmButton: false,
            didOpen: () => {
                Swal.showLoading();
            }
        });

        // *** แก้ไข: ใช้ API endpoint ที่ถูกต้อง ***
        const apiUrl = '<?= site_url("Pages/find_topic_page"); ?>';
        // console.log('📡 Public API URL:', apiUrl);

        // เรียก API
        fetch(apiUrl, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/x-www-form-urlencoded',
                'X-Requested-With': 'XMLHttpRequest'
            },
            body: `topic_id=${topicId}`
        })
            .then(response => {
                console.log('📊 Public API Response Status:', response.status);

                if (!response.ok) {
                    throw new Error(`HTTP ${response.status}: ${response.statusText}`);
                }

                // ตรวจสอบ Content-Type
                const contentType = response.headers.get('content-type');
                if (!contentType || !contentType.includes('application/json')) {
                    return response.text().then(text => {
                        console.error('❌ Public API returned non-JSON:', text.substring(0, 500));
                        throw new Error('API ส่งกลับข้อมูลที่ไม่ใช่ JSON');
                    });
                }

                return response.json();
            })
            .then(data => {
                console.log('📊 Public API Response Data:', data);

                Swal.close();

                if (data && data.success === true && data.page) {
                    // console.log(`✅ Public: Topic found on page ${data.page}, navigating...`);

                    // สร้าง URL ใหม่สำหรับ Q&A page
                    const qaUrl = '<?= site_url("Pages/q_a"); ?>';
                    const newUrl = `${qaUrl}?page=${data.page}&from_notification=1#${originalHash}`;

                    console.log('🚀 Public navigating to:', newUrl);

                    // แสดง success message ก่อน navigate
                    Swal.fire({
                        icon: 'success',
                        title: '🎯 พบกระทู้แล้ว!',
                        text: `${data.message || 'พบกระทู้ในหน้า ' + data.page} กำลังพาไป...`,
                        timer: 2000,
                        showConfirmButton: false,
                        didClose: () => {
                            console.log('🚀 Public navigating to Q&A page');
                            window.location.href = newUrl;
                        }
                    });

                } else {
                    const errorMessage = data ?
                        (data.message || 'ไม่สามารถค้นหากระทู้ที่ระบุได้') :
                        'ไม่ได้รับข้อมูลจาก API';

                    console.error('❌ Public: Topic search failed:', errorMessage);

                    Swal.fire({
                        icon: 'error',
                        title: '❌ ไม่พบกระทู้',
                        text: errorMessage,
                        confirmButtonText: 'ตกลง'
                    });
                }
            })
            .catch(error => {
                console.error('🚨 Public: Error finding topic page:', error);

                Swal.close();

                let errorMessage = 'ไม่สามารถค้นหากระทู้ได้';
                if (error.message.includes('404')) {
                    errorMessage = 'ระบบค้นหาไม่พร้อมใช้งาน';
                } else if (error.message.includes('JSON')) {
                    errorMessage = 'ข้อมูลจากเซิร์ฟเวอร์ไม่ถูกต้อง';
                } else if (error.message.includes('fetch')) {
                    errorMessage = 'ปัญหาการเชื่อมต่อเครือข่าย';
                }

                Swal.fire({
                    icon: 'error',
                    title: '🚨 เกิดข้อผิดพลาด',
                    text: errorMessage,
                    confirmButtonText: 'ตกลง'
                });
            });
    }

    // *** 2. เพิ่มฟังก์ชันดึง Topic ID จาก hash ***
    function extractTopicIdFromHash(hash) {
        if (!hash) return null;

        const patterns = [
            /comment-(\d+)/,  // comment-77
            /reply-(\d+)/,    // reply-123
            /topic-(\d+)/,    // topic-456
            /post-(\d+)/,     // post-789
            /^(\d+)$/         // 77 (ตัวเลขเปล่า)
        ];

        for (const pattern of patterns) {
            const match = hash.match(pattern);
            if (match && match[1]) {
                const id = parseInt(match[1]);
                console.log('🔢 Public: Extracted topic ID:', id, 'from hash:', hash);
                return id;
            }
        }

        console.log('❌ Public: Could not extract topic ID from hash:', hash);
        return null;
    }

    // *** 3. แก้ไขฟังก์ชัน scrollToElement ให้ค้นหาข้ามหน้า ***
    function scrollToElement(hash) {
        console.log('🎯 Public: Scrolling to hash:', hash);

        const targetElement = document.getElementById(hash);
        if (targetElement) {
            // console.log('✅ Public: Found target element:', targetElement);

            // เพิ่ม highlight effect
            targetElement.style.transition = 'all 0.5s ease';
            targetElement.style.background = 'linear-gradient(135deg, rgba(255, 215, 0, 0.2) 0%, rgba(255, 215, 0, 0.1) 100%)';
            targetElement.style.border = '2px solid rgba(255, 215, 0, 0.5)';
            targetElement.style.transform = 'scale(1.02)';
            targetElement.style.boxShadow = '0 8px 25px rgba(255, 215, 0, 0.3)';

            // เลื่อนไปที่ element
            targetElement.scrollIntoView({
                behavior: 'smooth',
                block: 'center',
                inline: 'nearest'
            });

            // ลบ highlight หลัง 3 วินาที
            setTimeout(() => {
                targetElement.style.background = '';
                targetElement.style.border = '';
                targetElement.style.transform = '';
                targetElement.style.boxShadow = '';
            }, 3000);

            // console.log('✅ Public: Successfully scrolled to element:', hash);
            return true;

        } else {
            console.warn('❌ Public: Target element not found, trying cross-page search...');

            // *** เพิ่ม: ค้นหาข้ามหน้าเหมือน staff ***
            Swal.fire({
                icon: 'warning',
                title: '🔍 กำลังค้นหากระทู้...',
                text: 'ไม่พบกระทู้ในหน้านี้ กำลังค้นหาในหน้าอื่น',
                timer: 3000,
                showConfirmButton: false,
                position: 'top-end',
                toast: true
            });

            // ลองค้นหาผ่าน API
            const topicId = extractTopicIdFromHash(hash);
            if (topicId) {
                findTopicPageFromPublic(topicId, hash);
            } else {
                console.log('❌ Public: Cannot extract topic ID from hash');

                Swal.fire({
                    icon: 'error',
                    title: '❌ ไม่สามารถค้นหาได้',
                    text: 'ลิงก์ไม่ถูกต้องหรือกระทู้ไม่มีอยู่',
                    timer: 4000,
                    showConfirmButton: false,
                    position: 'top-end',
                    toast: true
                });
            }

            return false;
        }
    }




    // *** เพิ่มฟังก์ชันตรวจสอบ Data Structure ***
    function validateAPIResponse(data) {
        console.log('🔍 Validating API Response:', data);

        const checks = {
            'data exists': data !== null && data !== undefined,
            'data is object': typeof data === 'object',
            'has success property': data && 'success' in data,
            'success is boolean': data && typeof data.success === 'boolean',
            'success is true': data && data.success === true,
            'has page property': data && 'page' in data,
            'page is valid': data && (typeof data.page === 'number' || typeof data.page === 'string') && data.page > 0,
            'has message': data && 'message' in data
        };

        console.log('=== API RESPONSE VALIDATION ===');
        Object.entries(checks).forEach(([check, result]) => {
            // console.log(`${result ? '✅' : '❌'} ${check}:`, result);
        });
        console.log('===============================');

        const isValid = checks['data exists'] &&
            checks['data is object'] &&
            checks['has success property'] &&
            checks['success is boolean'] &&
            checks['success is true'] &&
            checks['has page property'] &&
            checks['page is valid'];

        //console.log(`Overall validation: ${isValid ? '✅ PASS' : '❌ FAIL'}`);

        return isValid;
    }



    // *** ฟังก์ชันทดสอบสำหรับ Debug ***
    function testQAHashNavigation(hash) {
        console.log('🧪 Testing Q&A hash navigation with:', hash || 'comment-90');
        const testHash = hash || 'comment-90';
        handleNotificationHashNavigation(testHash);
    }

    function debugQAElements() {
        console.log('=== Q&A DEBUG: Available Elements ===');
        console.log('Current URL:', window.location.href);
        console.log('Current Hash:', window.location.hash);
        console.log('URL Parameters:', Object.fromEntries(new URLSearchParams(window.location.search)));

        console.log('\nQ&A Comment Elements:');
        document.querySelectorAll('[id^="comment-"]').forEach((el, index) => {
            console.log(`${index + 1}. ${el.id}`, el);
        });

        console.log('\nReply Elements:');
        document.querySelectorAll('[id^="reply-"]').forEach((el, index) => {
            console.log(`${index + 1}. ${el.id}`, el);
        });
        console.log('=====================================');
    }

    // *** เพิ่มใน global scope สำหรับ debug ***
    window.testQAHashNavigation = testQAHashNavigation;
    window.debugQAElements = debugQAElements;
    window.handleNotificationHashNavigation = handleNotificationHashNavigation;

    //console.log('🔧 Q&A Hash Navigation Functions Available:');
    //console.log('- testQAHashNavigation("comment-90") - ทดสอบการ scroll');
    //console.log('- debugQAElements() - แสดงรายการ elements ทั้งหมด');
    //console.log('- handleNotificationHashNavigation("comment-90") - จำลองการมาจาก notification');

</script>


<script>
    // *** Full Script สำหรับหน้า กระทู้ถาม-ตอบ (แก้ไข Hash Cleaning แล้ว) ***

    // *** ฟังก์ชันทำความสะอาด hash (เพิ่มฟังก์ชันใหม่) ***
    function cleanHashFromUrlParams(hash) {
        if (!hash) return hash;

        console.log('🧹 Starting hash cleaning for:', hash);

        let cleaned = hash;

        // *** แก้ไข: ตรวจสอบและลบ Google Search Console tab parameter ***
        if (cleaned === 'gsc.tab=0' || cleaned.startsWith('gsc.tab=')) {
            console.log('🗑️ Removing Google Search Console tab parameter entirely');
            return ''; // ถ้าเป็น gsc.tab เปล่าๆ ให้เอาออกทั้งหมด
        }

        // *** แก้ไข: จัดการ hash ที่มี gsc.tab อยู่ต่างๆ ***
        // ลบ &gsc.tab=0 ที่ท้าย
        cleaned = cleaned.replace(/[&?]gsc\.tab=\d+$/, '');
        // ลบ gsc.tab=0 ที่หน้า
        cleaned = cleaned.replace(/^gsc\.tab=\d+[&?]?/, '');
        // ลบ &gsc.tab=0 ที่กลาง
        cleaned = cleaned.replace(/[&?]gsc\.tab=\d+[&?]/, '&');

        // ลบ URL parameters อื่นๆ ที่ไม่ต้องการ
        const unwantedParams = [
            /[&?]utm_[^&#]*/g,
            /[&?]_ga=[^&#]*/g,
            /[&?]_gl=[^&#]*/g,
            /[&?]fbclid=[^&#]*/g,
            /[&?]gclid=[^&#]*/g,
            /[&?]PHPSESSID=[^&#]*/g,
            /[&?]msclkid=[^&#]*/g,
            /[&?]mc_cid=[^&#]*/g,
            /[&?]mc_eid=[^&#]*/g
        ];

        unwantedParams.forEach(pattern => {
            cleaned = cleaned.replace(pattern, '');
        });

        // ลบ & และ ? ที่เหลือ
        cleaned = cleaned.replace(/[&?]+$/, '');
        cleaned = cleaned.replace(/^[&?]+/, '');

        // แก้ไข & หรือ ? ที่ซ้ำกัน
        cleaned = cleaned.replace(/[&?]{2,}/g, '&');

        console.log('🧹 Hash cleaning result:', {
            original: hash,
            cleaned: cleaned,
            isEmpty: cleaned === '',
            isValidFormat: cleaned.match(/^(comment-|reply-|topic-|post-)\d+$/) !== null
        });

        return cleaned;
    }




    function cleanCurrentURL() {
        const currentHash = window.location.hash.substring(1);

        if (currentHash) {
            const cleanedHash = cleanHashFromUrlParams(currentHash);

            if (cleanedHash !== currentHash) {
                console.log('🔧 Updating URL hash from:', currentHash, 'to:', cleanedHash);

                // อัปเดต URL โดยไม่ reload หน้า
                let newUrl = window.location.pathname + window.location.search;

                if (cleanedHash && cleanedHash.length > 0) {
                    newUrl += '#' + cleanedHash;
                }

                window.history.replaceState({}, document.title, newUrl);

                return cleanedHash;
            }
        }

        return currentHash;
    }




    // *** แก้ไข DOMContentLoaded event สำหรับจัดการ hash ที่ว่างเปล่า ***
    document.addEventListener('DOMContentLoaded', function () {
        console.log('🚀 Q&A Page - Checking for notification navigation (IMPROVED HASH CLEANING)');

        // ตรวจสอบว่ามาจาก notification หรือไม่
        const urlParams = new URLSearchParams(window.location.search);
        const fromNotification = urlParams.get('from_notification');

        // ทำความสะอาด URL ก่อนเสมอ
        const cleanedHash = cleanCurrentURL();

        if (fromNotification) {
            console.log('📥 Q&A: Came from staff notification');

            if (cleanedHash && cleanedHash.length > 0) {
                console.log('📍 Q&A: Using cleaned hash for notification:', cleanedHash);

                // รอให้หน้าโหลดเสร็จแล้วค่อย scroll
                setTimeout(() => {
                    handleNotificationHashNavigation(cleanedHash);
                }, 2000);
            } else {
                console.log('⚠️ Q&A: No valid hash after cleaning, showing general notification');

                setTimeout(() => {
                    if (typeof Swal !== 'undefined') {
                        Swal.fire({
                            icon: 'info',
                            title: '📢 มาจากการแจ้งเตือน',
                            text: 'คุณได้เข้าสู่หน้ากระทู้ถาม-ตอบแล้ว',
                            timer: 3000,
                            showConfirmButton: false,
                            position: 'top-end',
                            toast: true
                        });
                    }
                }, 1000);
            }

            // ลบ from_notification parameter ออกจาก URL
            const cleanUrl = window.location.pathname + window.location.search.replace(/[?&]from_notification=1/, '');
            const finalUrl = cleanUrl + (cleanedHash ? '#' + cleanedHash : '');
            window.history.replaceState({}, document.title, finalUrl);

        } else if (cleanedHash && cleanedHash.length > 0) {
            // กรณี direct link ที่มี hash
            console.log('📍 Q&A: Direct hash navigation with cleaned hash:', cleanedHash);

            setTimeout(() => {
                handleDirectHashNavigation(cleanedHash);
            }, 1500);
        }

        // console.log('✅ Q&A: Improved hash navigation handler initialized');
    });


    // *** ฟังก์ชัน handle hash จาก notification ***
    function handleNotificationHashNavigation(hash) {
        // console.log('🎯 Q&A: Handling notification hash navigation:', hash);

        // *** ทำความสะอาด hash อีกครั้งเพื่อความแน่ใจ ***
        const cleanedHash = cleanHashFromUrlParams(hash);
        //console.log('🧹 Q&A: Using cleaned hash for notification:', cleanedHash);

        // ลองหา element ในหน้าปัจจุบัน
        const targetElement = document.getElementById(cleanedHash);

        if (targetElement) {
            // console.log('✅ Q&A: Found target element immediately:', targetElement);

            // เพิ่ม highlight effect แบบ notification (สีทอง)
            targetElement.style.transition = 'all 0.6s ease';
            targetElement.style.background = 'linear-gradient(135deg, rgba(255, 215, 0, 0.4) 0%, rgba(255, 215, 0, 0.1) 100%)';
            targetElement.style.border = '3px solid rgba(255, 215, 0, 0.8)';
            targetElement.style.transform = 'scale(1.03)';
            targetElement.style.boxShadow = '0 15px 35px rgba(255, 215, 0, 0.5)';

            // เลื่อนไปที่ element
            targetElement.scrollIntoView({
                behavior: 'smooth',
                block: 'center',
                inline: 'nearest'
            });

            // แสดง success message
            setTimeout(() => {
                if (typeof Swal !== 'undefined') {
                    Swal.fire({
                        icon: 'success',
                        title: '🎯 พบกระทู้แล้ว!',
                        text: 'พบกระทู้จากการแจ้งเตือนเรียบร้อย',
                        timer: 2500,
                        showConfirmButton: false,
                        position: 'top-end',
                        toast: true,
                        background: 'linear-gradient(135deg, #fff9e6 0%, #ffeaa7 100%)',
                        color: '#856404'
                    });
                }
            }, 800);

            // ลบ highlight หลัง 6 วินาที
            setTimeout(() => {
                targetElement.style.background = '';
                targetElement.style.border = '';
                targetElement.style.transform = '';
                targetElement.style.boxShadow = '';
            }, 6000);

            //console.log('✅ Q&A: Successfully scrolled to notification target');

        } else {
            console.warn('❌ Q&A: Target element not found, trying alternatives...');

            // ลองหา element ที่เกี่ยวข้อง
            const relatedElement = findQARelatedElement(cleanedHash);
            if (relatedElement) {
                // console.log('🔍 Q&A: Found related element:', relatedElement.id);
                handleNotificationHashNavigation(relatedElement.id);
                return;
            }

            // ถ้าไม่พบ element ให้แสดง warning และลองค้นหาหน้าอื่น
            if (typeof Swal !== 'undefined') {
                Swal.fire({
                    icon: 'warning',
                    title: '🔍 กำลังค้นหากระทู้...',
                    text: 'ไม่พบกระทู้ในหน้านี้ กำลังค้นหาในหน้าอื่น',
                    timer: 3000,
                    showConfirmButton: false,
                    position: 'top-end',
                    toast: true
                });
            }

            // ลองค้นหาผ่าน API
            const topicId = extractQATopicId(cleanedHash);
            if (topicId) {
                findTopicPageFromQA(topicId, cleanedHash);
            }
        }
    }

    // *** แก้ไขฟังก์ชัน handleDirectHashNavigation ***
    function handleDirectHashNavigation(hash) {
        console.log('🎯 Q&A: Handling direct hash navigation:', hash);

        // *** ตรวจสอบว่า hash ไม่ว่างเปล่า ***
        if (!hash || hash.length === 0) {
            // console.log('⚠️ Q&A: Empty hash provided to handleDirectHashNavigation');
            return;
        }

        const cleanedHash = cleanHashFromUrlParams(hash);
        console.log('🧹 Q&A: Using cleaned hash for direct navigation:', cleanedHash);

        // *** ตรวจสอบว่า cleaned hash ไม่ว่างเปล่า ***
        if (!cleanedHash || cleanedHash.length === 0) {
            //  console.log('⚠️ Q&A: Hash cleaned to empty in handleDirectHashNavigation');

            if (typeof Swal !== 'undefined') {
                Swal.fire({
                    icon: 'warning',
                    title: '❌ ลิงก์ไม่ถูกต้อง',
                    text: 'ลิงก์ที่คุณเข้ามาไม่ถูกต้อง',
                    timer: 3000,
                    showConfirmButton: false,
                    position: 'top-end',
                    toast: true
                });
            }
            return;
        }

        const targetElement = document.getElementById(cleanedHash);
        if (targetElement) {
            // console.log('✅ Q&A: Found direct target element:', targetElement);

            // เพิ่ม highlight effect แบบปกติ (สีเขียว)
            targetElement.style.transition = 'all 0.5s ease';
            targetElement.style.background = 'linear-gradient(135deg, rgba(40, 167, 69, 0.2) 0%, rgba(40, 167, 69, 0.1) 100%)';
            targetElement.style.border = '2px solid rgba(40, 167, 69, 0.5)';
            targetElement.style.transform = 'scale(1.02)';
            targetElement.style.boxShadow = '0 8px 25px rgba(40, 167, 69, 0.3)';

            // เลื่อนไปที่ element
            targetElement.scrollIntoView({
                behavior: 'smooth',
                block: 'center',
                inline: 'nearest'
            });

            // แสดง success message
            setTimeout(() => {
                if (typeof Swal !== 'undefined') {
                    Swal.fire({
                        icon: 'success',
                        title: '🎯 พบกระทู้แล้ว!',
                        text: 'เลื่อนไปที่กระทู้เรียบร้อย',
                        timer: 2000,
                        showConfirmButton: false,
                        position: 'top-end',
                        toast: true,
                        background: 'linear-gradient(135deg, #e8f5e8 0%, #d4edda 100%)',
                        color: '#155724'
                    });
                }
            }, 500);

            // ลบ highlight หลัง 4 วินาที
            setTimeout(() => {
                targetElement.style.background = '';
                targetElement.style.border = '';
                targetElement.style.transform = '';
                targetElement.style.boxShadow = '';
            }, 4000);

            // console.log('✅ Q&A: Successfully scrolled to direct target');
        } else {
            console.warn('❌ Q&A: Direct target not found after cleaning:', cleanedHash);

            // ลองหา element ที่เกี่ยวข้อง
            const relatedElement = findQARelatedElement(cleanedHash);
            if (relatedElement) {
                console.log('🔍 Q&A: Found related element for direct navigation:', relatedElement.id);
                handleDirectHashNavigation(relatedElement.id);
            } else {
                console.log('❌ Q&A: No related element found for direct navigation');


            }
        }
    }

    // *** ฟังก์ชันค้นหา element ที่เกี่ยวข้องใน Q&A ***
    function findQARelatedElement(hash) {
        console.log('🔍 Q&A: Searching for related element:', hash);

        // รูปแบบที่ต้องลอง
        const patterns = [
            hash,                    // hash เดิม
            `comment-${hash}`,       // comment-XX
            `reply-${hash}`,         // reply-XX
            `topic-${hash}`,         // topic-XX
            `post-${hash}`           // post-XX
        ];

        // ถ้า hash เป็น comment-XX ให้ลองหา XX
        const commentMatch = hash.match(/comment-(\d+)/);
        if (commentMatch) {
            patterns.push(commentMatch[1]);
        }

        // ถ้า hash เป็น reply-XX ให้ลองหา parent comment
        const replyMatch = hash.match(/reply-(\d+)/);
        if (replyMatch) {
            // ค้นหา reply element แล้วหา parent comment
            const allReplies = document.querySelectorAll('[id^="reply-"]');
            for (let reply of allReplies) {
                const parentComment = reply.closest('[id^="comment-"]');
                if (parentComment) {
                    patterns.push(parentComment.id);
                    console.log('🔗 Q&A: Found parent comment for reply:', parentComment.id);
                }
            }
        }

        // ลอง patterns ทีละตัว
        for (let pattern of patterns) {
            const element = document.getElementById(pattern);
            if (element) {
                // console.log('✅ Q&A: Found related element with pattern:', pattern);
                return element;
            }
        }

        console.log('❌ Q&A: No related element found');
        return null;
    }

    // *** ฟังก์ชันดึง Topic ID จาก hash สำหรับ Q&A ***
    function extractQATopicId(hash) {
        if (!hash) return null;

        const patterns = [
            /comment-(\d+)/,  // comment-77
            /reply-(\d+)/,    // reply-123
            /topic-(\d+)/,    // topic-456
            /post-(\d+)/,     // post-789
            /^(\d+)$/         // 77 (ตัวเลขเปล่า)
        ];

        for (const pattern of patterns) {
            const match = hash.match(pattern);
            if (match && match[1]) {
                const id = parseInt(match[1]);
                console.log('🔢 Q&A: Extracted topic ID:', id, 'from hash:', hash);
                return id;
            }
        }

        console.log('❌ Q&A: Could not extract topic ID from hash:', hash);
        return null;
    }

    // *** ฟังก์ชันค้นหาหน้าที่มีกระทู้ (จากหน้า Q&A) ***
    // *** แก้ไขฟังก์ชันค้นหาหน้าที่มีกระทู้ (แก้ปัญหา Error Handling) ***
    function findTopicPageFromQA(topicId, originalHash) {
        console.log('🔍 Q&A: Finding page for topic ID:', topicId);

        // แสดง loading message
        if (typeof Swal !== 'undefined') {
            Swal.fire({
                title: 'กำลังค้นหากระทู้...',
                text: 'กรุณารอสักครู่',
                icon: 'info',
                allowOutsideClick: false,
                showConfirmButton: false,
                didOpen: () => {
                    Swal.showLoading();
                }
            });
        }

        // *** แก้ไข: ใช้ Pages/find_topic_page ***
        const apiUrl = '<?= site_url("Pages/find_topic_page"); ?>';
        console.log('📡 API URL:', apiUrl);

        // เรียก API
        fetch(apiUrl, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/x-www-form-urlencoded',
                'X-Requested-With': 'XMLHttpRequest'
            },
            body: `topic_id=${topicId}`
        })
            .then(response => {
                console.log('📊 Q&A API Response Status:', response.status);
                console.log('📊 Q&A API Response URL:', response.url);

                // *** แก้ไข: ตรวจสอบ response.ok ก่อน ***
                if (!response.ok) {
                    throw new Error(`HTTP ${response.status}: ${response.statusText}`);
                }

                // ตรวจสอบ Content-Type
                const contentType = response.headers.get('content-type');
                console.log('📊 Content-Type:', contentType);

                // *** แก้ไข: ตรวจสอบ Content-Type ที่ถูกต้อง ***
                if (!contentType || (!contentType.includes('application/json') && !contentType.includes('text/json'))) {
                    console.warn('⚠️ Response is not JSON, reading as text for debugging...');
                    return response.text().then(text => {
                        console.error('❌ API returned non-JSON response:', text.substring(0, 500));
                        throw new Error('API ส่งกลับข้อมูลที่ไม่ใช่ JSON');
                    });
                }

                return response.json();
            })
            .then(data => {
                console.log('📊 Q&A API Response Data:', data);

                // *** แก้ไข: ปิด Swal ก่อนการตรวจสอบ data ***
                if (typeof Swal !== 'undefined') {
                    Swal.close();
                }

                // *** แก้ไข: ตรวจสอบ data.success อย่างถูกต้อง ***
                if (data && data.success === true && data.page) {
                    // console.log(`✅ Q&A: Topic found on page ${data.page}, navigating...`);

                    // สร้าง URL ใหม่
                    const currentUrl = window.location.pathname;
                    const newUrl = `${currentUrl}?page=${data.page}&from_notification=1#${originalHash}`;

                    console.log('🚀 Generated URL:', newUrl);

                    // แสดง success message ก่อน navigate
                    if (typeof Swal !== 'undefined') {
                        Swal.fire({
                            icon: 'success',
                            title: '🎯 พบกระทู้แล้ว!',
                            text: `${data.message || 'พบกระทู้ในหน้า ' + data.page} กำลังพาไป...`,
                            timer: 2000,
                            showConfirmButton: false,
                            didClose: () => {
                                console.log('🚀 Navigating to:', newUrl);
                                window.location.href = newUrl;
                            }
                        });
                    } else {
                        console.log('🚀 Navigating to:', newUrl);
                        window.location.href = newUrl;
                    }

                } else {
                    // *** แก้ไข: แสดง error ที่ถูกต้อง ***
                    const errorMessage = data ?
                        (data.message || 'ไม่สามารถค้นหากระทู้ที่ระบุได้') :
                        'ไม่ได้รับข้อมูลจาก API';

                    console.error('❌ Q&A: Topic search failed:', {
                        success: data ? data.success : 'undefined',
                        page: data ? data.page : 'undefined',
                        message: errorMessage,
                        fullData: data
                    });

                    if (typeof Swal !== 'undefined') {
                        Swal.fire({
                            icon: 'error',
                            title: '❌ ไม่พบกระทู้',
                            text: errorMessage,
                            confirmButtonText: 'ตกลง',
                            footer: `<small>กระทู้ ID: ${topicId}<br>API Response: ${JSON.stringify(data)}</small>`
                        });
                    }
                }
            })
            .catch(error => {
                console.error('🚨 Q&A: Error finding topic page:', error);
                console.error('🚨 Error details:', {
                    message: error.message,
                    stack: error.stack,
                    name: error.name
                });

                // *** แก้ไข: ตรวจสอบ Swal ก่อนปิด ***
                if (typeof Swal !== 'undefined') {
                    // ปิด loading modal ถ้ายังเปิดอยู่
                    try {
                        Swal.close();
                    } catch (e) {
                        // Ignore close errors
                    }

                    let errorMessage = 'ไม่สามารถค้นหากระทู้ได้';
                    let errorDetails = error.message;

                    // ปรับข้อความตามประเภทของ error
                    if (error.message.includes('404')) {
                        errorMessage = 'ระบบค้นหาไม่พร้อมใช้งาน';
                        errorDetails = 'API endpoint ไม่พบ (404)';
                    } else if (error.message.includes('JSON') || error.message.includes('Unexpected token')) {
                        errorMessage = 'ข้อมูลจากเซิร์ฟเวอร์ไม่ถูกต้อง';
                        errorDetails = 'การตอบกลับจากเซิร์ฟเวอร์ไม่ใช่ JSON';
                    } else if (error.message.includes('Failed to fetch') || error.message.includes('NetworkError')) {
                        errorMessage = 'ไม่สามารถเชื่อมต่อเซิร์ฟเวอร์ได้';
                        errorDetails = 'การเชื่อมต่อขาดหาย';
                    } else if (error.message.includes('fetch')) {
                        errorMessage = 'ปัญหาการเชื่อมต่อเครือข่าย';
                        errorDetails = 'ตรวจสอบการเชื่อมต่ออินเทอร์เน็ต';
                    }

                    Swal.fire({
                        icon: 'error',
                        title: '🚨 เกิดข้อผิดพลาด',
                        text: errorMessage,
                        confirmButtonText: 'ตกลง',
                        footer: `<small>รายละเอียด: ${errorDetails}<br>กระทู้ ID: ${topicId}</small>`
                    });
                }

                // *** แก้ไข: Fallback ที่ปลอดภัยกว่า ***
                console.log('🔄 Setting fallback timer to Q&A main page...');
                setTimeout(() => {
                    console.log('🔄 Fallback: Redirecting to Q&A main page');
                    if (window.location.pathname.includes('/q_a')) {
                        window.location.href = '<?= site_url("Pages/q_a"); ?>';
                    }
                }, 3000);
            });
    }

    // *** ฟังก์ชันทดสอบสำหรับ Debug ***
    function testQAHashNavigation(hash) {
        console.log('🧪 Testing Q&A hash navigation with:', hash || 'comment-90');
        const testHash = hash || 'comment-90';
        const cleanedHash = cleanHashFromUrlParams(testHash);
        console.log('🧹 Test using cleaned hash:', cleanedHash);
        handleNotificationHashNavigation(cleanedHash);
    }

    function debugQAElements() {
        console.log('=== Q&A DEBUG: Available Elements ===');
        console.log('Current URL:', window.location.href);
        console.log('Current Hash:', window.location.hash);
        console.log('URL Parameters:', Object.fromEntries(new URLSearchParams(window.location.search)));

        console.log('\nQ&A Comment Elements:');
        document.querySelectorAll('[id^="comment-"]').forEach((el, index) => {
            console.log(`${index + 1}. ${el.id}`, el);
        });

        console.log('\nReply Elements:');
        document.querySelectorAll('[id^="reply-"]').forEach((el, index) => {
            console.log(`${index + 1}. ${el.id}`, el);
        });
        console.log('=====================================');
    }

    function testHashCleaning(testHash) {
        console.log('=== TESTING HASH CLEANING ===');
        const testCases = [
            testHash || 'comment-88&gsc.tab=0',
            'reply-123&utm_source=test',
            'comment-77&gsc.tab=0&utm_campaign=test',
            'comment-99?fbclid=test123',
            'reply-456&_ga=test&_gl=test2'
        ];

        testCases.forEach(hash => {
            const cleaned = cleanHashFromUrlParams(hash);
            console.log(`Original: "${hash}" -> Cleaned: "${cleaned}"`);
        });
        console.log('==============================');
    }

    // *** เพิ่มใน global scope สำหรับ debug ***
    window.testQAHashNavigation = testQAHashNavigation;
    window.debugQAElements = debugQAElements;
    window.handleNotificationHashNavigation = handleNotificationHashNavigation;
    window.cleanHashFromUrlParams = cleanHashFromUrlParams;
    window.testHashCleaning = testHashCleaning;

    //console.log('🔧 Q&A Hash Navigation Functions Available (FIXED VERSION):');
    //console.log('- testQAHashNavigation("comment-90") - ทดสอบการ scroll');
    //console.log('- debugQAElements() - แสดงรายการ elements ทั้งหมด');
    //console.log('- testHashCleaning("comment-88&gsc.tab=0") - ทดสอบการทำความสะอาด hash');
    //console.log('- cleanHashFromUrlParams("comment-88&gsc.tab=0") - ทำความสะอาด hash');
    //console.log('- handleNotificationHashNavigation("comment-90") - จำลองการมาจาก notification');



    // *** ฟังก์ชันทดสอบ API (สำหรับ debug) ***
    function testAPIConnection() {
        //console.log('🧪 Testing find_topic_page API...');

        const testUrl = '<?= site_url("Pages/find_topic_page"); ?>';
        console.log('Testing URL:', testUrl);

        // ทดสอบด้วยกระทู้ ID 80 ที่เรารู้ว่ามีอยู่
        fetch(testUrl, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/x-www-form-urlencoded',
                'X-Requested-With': 'XMLHttpRequest'
            },
            body: 'topic_id=80'
        })
            .then(response => {
                // console.log('Test API Status:', response.status);

                // ตรวจสอบ Content-Type
                const contentType = response.headers.get('content-type');
                console.log('Test Content-Type:', contentType);

                if (!response.ok) {
                    throw new Error(`HTTP ${response.status}: ${response.statusText}`);
                }

                // ตรวจสอบว่าเป็น JSON หรือไม่
                if (!contentType || !contentType.includes('application/json')) {
                    return response.text().then(text => {
                        console.error('❌ Test API returned non-JSON:', text.substring(0, 200));
                        throw new Error('API ไม่ส่งกลับ JSON');
                    });
                }

                return response.json();
            })
            .then(data => {
                // console.log('✅ Test API Response:', data);

                if (typeof Swal !== 'undefined') {
                    Swal.fire({
                        icon: 'success',
                        title: 'API ทำงานปกติ',
                        text: `พบกระทู้ ID ${data.topic_id} ในหน้า ${data.page}`,
                        timer: 3000,
                        showConfirmButton: false
                    });
                }
            })
            .catch(error => {
                console.error('❌ Test API Error:', error);

                if (typeof Swal !== 'undefined') {
                    Swal.fire({
                        icon: 'error',
                        title: 'API ไม่ทำงาน',
                        text: error.message,
                        footer: '<small>ตรวจสอบ API endpoint และ Controller</small>'
                    });
                }
            });
    }
    // *** ฟังก์ชันทดสอบการค้นหากระทู้ที่ระบุ ***
    function testFindTopic(topicId) {
        if (!topicId) {
            topicId = prompt('กรุณาใส่ Topic ID ที่ต้องการทดสอบ:', '80');
        }

        if (topicId) {
            // console.log(`🧪 Testing findTopicPageFromQA with ID: ${topicId}`);
            findTopicPageFromQA(topicId, `comment-${topicId}`);
        }
    }

    // *** เพิ่มฟังก์ชัน debug ใน console ***
    window.testAPIConnection = testAPIConnection;
    window.findTopicPageFromQA = findTopicPageFromQA;
    window.testFindTopic = testFindTopic;

    //console.log('🔧 Q&A API Functions Available (FIXED VERSION):');
    //console.log('- testAPIConnection() - ทดสอบการเชื่อมต่อ API');
    //console.log('- testFindTopic(80) - ทดสอบการค้นหากระทู้ ID 80');
    //console.log('- findTopicPageFromQA(80, "comment-80") - ทดสอบการค้นหากระทู้โดยตรง');
</script>







<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
<link href="https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.min.css" rel="stylesheet">
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.all.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>