<?php
class Q_a_model extends CI_Model
{
    private $channelAccessToken;
    private $lineApiUrl;

    public function __construct()
    {
        parent::__construct();
        $this->channelAccessToken = get_config_value('line_token');
        $this->lineApiUrl = 'https://api.line.me/v2/bot/message/multicast';
    }

    /**
     * ตรวจสอบสถานะการเข้าสู่ระบบ (IMPROVED VERSION - แก้ไขอีเมล)
     */
    public function check_user_login()
    {
        $is_logged_in = false;
        $user_type = '';
        $user_info = [];

        // ตรวจสอบประชาชน
        if ($this->session->userdata('mp_id')) {
            $is_logged_in = true;
            $user_type = 'public';

            // *** แก้ไข: ดึง user_id และอีเมลที่ถูกต้อง ***
            $session_mp_id = $this->session->userdata('mp_id');
            $session_email = $this->session->userdata('mp_email'); // อาจจะเป็น null
            $fixed_user_id = $this->fix_user_id_overflow($session_mp_id, $session_email, 'public');

            // *** เพิ่ม: ดึงข้อมูลจากฐานข้อมูลเพื่อความแน่ใจ ***
            $db_user = $this->db->select('mp_email, mp_fname, mp_lname')
                ->where('id', $fixed_user_id)
                ->get('tbl_member_public')
                ->row();

            // ใช้ข้อมูลจาก session เป็นหลัก แต่ fallback ไปฐานข้อมูล
            $final_email = !empty($session_email) ? $session_email : ($db_user ? $db_user->mp_email : '');
            $fname = $this->session->userdata('mp_fname') ?: ($db_user ? $db_user->mp_fname : '');
            $lname = $this->session->userdata('mp_lname') ?: ($db_user ? $db_user->mp_lname : '');

            $user_info = [
                'id' => $fixed_user_id,
                'user_id' => $fixed_user_id,
                'name' => trim($fname . ' ' . $lname),
                'email' => $final_email,
                'type' => 'ประชาชน'
            ];

            // *** Log การดึงข้อมูล ***
            if (empty($session_email) && !empty($final_email)) {
                log_message('info', "Retrieved public user email from DB: {$final_email} for user_id: {$fixed_user_id}");
            }

            if ($session_mp_id != $fixed_user_id) {
                log_message('info', "Model fixed public user_id: {$session_mp_id} -> {$fixed_user_id} for {$final_email}");
            }

            log_message('debug', "Public user login - ID: {$fixed_user_id}, Email: {$final_email}, Name: {$user_info['name']}");
        }
        // ตรวจสอบเจ้าหน้าที่
        elseif ($this->session->userdata('m_id')) {
            $is_logged_in = true;

            // *** เพิ่ม: ตรวจสอบประเภทผู้ใช้แบบละเอียด ***
            $m_system = $this->session->userdata('m_system');
            $m_level = $this->session->userdata('m_level');

            if ($m_system == 1) {
                $user_type = 'system_admin';
            } elseif ($m_level == 0) {
                $user_type = 'super_admin';
            } elseif ($m_level == 1) {
                $user_type = 'user_admin';
            } else {
                $user_type = 'staff';
            }

            // *** แก้ไข: ดึง user_id และอีเมลที่ถูกต้อง ***
            $session_m_id = $this->session->userdata('m_id');
            $session_email = $this->session->userdata('m_email'); // อาจจะเป็น null
            $fixed_user_id = $this->fix_user_id_overflow($session_m_id, $session_email, 'staff');

            // *** เพิ่ม: ดึงข้อมูลจากฐานข้อมูลเพื่อความแน่ใจ ***
            $db_user = $this->db->select('m_email, m_fname, m_lname, m_username')
                ->where('m_id', $fixed_user_id)
                ->get('tbl_member')
                ->row();

            // ใช้ข้อมูลจาก session เป็นหลัก แต่ fallback ไปฐานข้อมูล
            $final_email = !empty($session_email) ? $session_email : ($db_user ? $db_user->m_email : '');
            $fname = $this->session->userdata('m_fname') ?: ($db_user ? $db_user->m_fname : '');
            $lname = $this->session->userdata('m_lname') ?: ($db_user ? $db_user->m_lname : '');
            $username = $this->session->userdata('m_username') ?: ($db_user ? $db_user->m_username : '');

            $user_info = [
                'id' => $fixed_user_id,
                'user_id' => $fixed_user_id,
                'name' => trim($fname . ' ' . $lname),
                'email' => $final_email,
                'username' => $username,
                'type' => 'เจ้าหน้าที่',
                'system' => $m_system,
                'level' => $m_level
            ];

            // *** Log การดึงข้อมูล ***
            if (empty($session_email) && !empty($final_email)) {
                log_message('info', "Retrieved staff user email from DB: {$final_email} for user_id: {$fixed_user_id}");
            }

            if ($session_m_id != $fixed_user_id) {
                log_message('info', "Model fixed staff user_id: {$session_m_id} -> {$fixed_user_id} for {$final_email}");
            }

            log_message('debug', "Staff user login - ID: {$fixed_user_id}, Email: {$final_email}, Name: {$user_info['name']}, Type: {$user_type}");
        }

        // *** Log สรุปผลลัพธ์ ***
        log_message('debug', 'check_user_login result: ' . json_encode([
            'is_logged_in' => $is_logged_in,
            'user_type' => $user_type,
            'user_id' => $user_info['user_id'] ?? null,
            'email' => $user_info['email'] ?? null,
            'name' => $user_info['name'] ?? null
        ]));

        return [
            'is_logged_in' => $is_logged_in,
            'user_type' => $user_type,
            'user_info' => $user_info
        ];
    }

    /**
     * ฟังก์ชันแก้ไขปัญหา user_id overflow สำหรับ Model
     */
    public function can_user_edit_topic($topic_id, $current_user_id, $current_user_type)
    {
        // ดึงข้อมูลกระทู้
        $topic = $this->db->select('q_a_user_id, q_a_user_type, q_a_email')
            ->where('q_a_id', $topic_id)
            ->get('tbl_q_a')
            ->row();

        if (!$topic) {
            return false;
        }

        // ถ้าเป็น staff สามารถแก้ไขได้ทุกอัน
        if (in_array($current_user_type, ['staff', 'system_admin', 'super_admin', 'user_admin'])) {
            return true;
        }

        // ถ้าเป็น public user ต้องเป็นเจ้าของกระทู้
        if ($current_user_type === 'public') {
            $topic_user_id = $topic->q_a_user_id;

            // *** แก้ไข: ตรวจสอบ overflow user_id ***
            if ($topic_user_id == 2147483647 || $topic_user_id == '2147483647') {
                if (!empty($topic->q_a_email)) {
                    // หา user_id ที่ถูกต้องจากอีเมล
                    $correct_user_id = $this->get_correct_user_id_by_email($topic->q_a_email, $topic->q_a_user_type);
                    if ($correct_user_id) {
                        // อัพเดทในฐานข้อมูล
                        $this->db->where('q_a_id', $topic_id)
                            ->update('tbl_q_a', ['q_a_user_id' => $correct_user_id]);
                        $topic_user_id = $correct_user_id;
                        log_message('info', "Auto-fixed topic {$topic_id} user_id: 2147483647 -> {$correct_user_id}");
                    }
                }
            }

            return ($topic_user_id == $current_user_id);
        }

        return false;
    }

    /**
     * ฟังก์ชันใหม่: ดึง user_id ที่ถูกต้องจากอีเมล
     */
    public function get_correct_user_id_by_email($email, $user_type = 'public')
    {
        if (empty($email))
            return null;

        if ($user_type === 'public') {
            // *** ใช้ id (auto increment) แทน mp_id สำหรับ public user ***
            $user = $this->db->select('id, mp_id')
                ->where('mp_email', $email)
                ->get('tbl_member_public')
                ->row();

            return $user ? $user->id : null; // *** ใช้ id แทน mp_id ***
        } else {
            // สำหรับ staff ใช้ m_id
            $user = $this->db->select('m_id')
                ->where('m_email', $email)
                ->get('tbl_member')
                ->row();

            return $user ? $user->m_id : null;
        }
    }

    /**
     * แก้ไขฟังก์ชัน fix_user_id_overflow ให้ consistent (IMPROVED)
     */
    private function fix_user_id_overflow($session_id, $email, $user_type = 'public')
    {
        try {
            // ตรวจสอบว่าเป็น INT overflow หรือไม่
            if ($session_id == 2147483647 || $session_id == '2147483647' || empty($session_id)) {
                log_message('info', "Model detected user_id overflow: {$session_id} for email: {$email} (type: {$user_type})");

                // ตรวจสอบว่ามีอีเมลหรือไม่
                if (empty($email)) {
                    log_message('error', "Model cannot fix user_id - email is empty");
                    return null;
                }

                if ($user_type === 'public') {
                    // *** ใช้ auto increment id เสมอ ***
                    $public_user = $this->db->select('id, mp_id')
                        ->where('mp_email', $email)
                        ->get('tbl_member_public')
                        ->row();

                    if ($public_user) {
                        log_message('info', "Model fixed public user_id: {$session_id} -> {$public_user->id} for {$email}");
                        return $public_user->id; // *** ใช้ auto increment id แทน mp_id ***
                    } else {
                        log_message('debug', "Model could not find public user with email: {$email}");
                    }
                } else {
                    // ดึง ID จาก tbl_member (staff)
                    $staff_user = $this->db->select('m_id')
                        ->where('m_email', $email)
                        ->get('tbl_member')
                        ->row();

                    if ($staff_user) {
                        log_message('info', "Model fixed staff user_id: {$session_id} -> {$staff_user->m_id} for {$email}");
                        return $staff_user->m_id;
                    } else {
                        log_message('debug', "Model could not find staff user with email: {$email}");
                    }
                }

                log_message('error', "Model could not fix user_id for email: {$email} (type: {$user_type})");
                return null;
            }

            // ถ้าไม่มีปัญหาให้ return ค่าเดิม
            return $session_id;

        } catch (Exception $e) {
            log_message('error', "fix_user_id_overflow exception: " . $e->getMessage());
            log_message('error', "Exception details: session_id={$session_id}, email={$email}, type={$user_type}");
            return $session_id; // Return original value on error
        }
    }


    public function add_q_a()
    {
        try {
            // *** เพิ่ม: Debug เริ่มต้น ***
            log_message('debug', '=== MODEL ADD_Q_A START (ENHANCED VULGAR CHECK VERSION) ===');

            // *** แก้ไข: ตรวจสอบสถานะการเข้าสู่ระบบที่แก้ไขแล้ว ***
            $login_status = $this->check_user_login();
            $is_logged_in = $login_status['is_logged_in'];
            $user_info = $login_status['user_info'];

            // รับข้อมูลจากฟอร์ม
            $q_a_msg = $this->input->post('q_a_msg');
            $q_a_detail = $this->input->post('q_a_detail');

            // จัดการชื่อและอีเมลตามสถานะการเข้าสู่ระบบ
            if ($is_logged_in) {
                $q_a_by = $user_info['name'];

                // *** แก้ไข: ตรวจสอบอีเมลให้ครบถ้วน ***
                $q_a_email = '';
                if (isset($user_info['email']) && !empty(trim($user_info['email']))) {
                    $q_a_email = trim($user_info['email']);
                } else {
                    // หากไม่มีในข้อมูล user_info ให้ดึงจากฐานข้อมูล
                    $email_from_db = $this->getUserEmailById($user_info['user_id'], $login_status['user_type']);
                    if ($email_from_db) {
                        $q_a_email = $email_from_db;
                        log_message('info', 'Retrieved email from database: ' . $email_from_db);
                    }
                }

                log_message('debug', 'User logged in - using email: ' . $q_a_email);
            } else {
                $q_a_by = $this->input->post('q_a_by');
                $q_a_email = $this->input->post('q_a_email');
                log_message('debug', 'Guest user - using posted email: ' . $q_a_email);
            }

            log_message('debug', 'Q&A data: msg=' . $q_a_msg . ', by=' . $q_a_by);
            log_message('debug', 'Fixed user_id to save: ' . (isset($user_info['user_id']) ? $user_info['user_id'] : 'null'));

            // *** เพิ่ม: ตรวจสอบคำหยาบด้วย Vulgar_check Library ***
            $this->load->library('vulgar_check');

            $fields_to_check = array(
                'q_a_msg' => $q_a_msg,
                'q_a_detail' => $q_a_detail,
                'q_a_by' => $q_a_by,
                'q_a_email' => $q_a_email
            );

            log_message('debug', 'Starting vulgar check for fields: ' . print_r($fields_to_check, true));

            // ตรวจสอบคำหยาบด้วย library
            $vulgar_result = $this->vulgar_check->check_form($fields_to_check);

            log_message('debug', 'Vulgar check result: ' . print_r($vulgar_result, true));

            if ($vulgar_result['has_vulgar']) {
                log_message('debug', 'Vulgar words detected, blocking submission');

                // *** เพิ่ม: เก็บข้อมูลคำหยาบเพื่อส่งกลับ ***
                $vulgar_words = array();
                $vulgar_fields = array();

                foreach ($vulgar_result['results'] as $field => $result) {
                    if ($result['has_vulgar']) {
                        $vulgar_words = array_merge($vulgar_words, $result['vulgar_words']);
                        $vulgar_fields[] = $field;
                    }
                }

                $unique_vulgar_words = array_unique($vulgar_words);

                log_message('debug', 'Vulgar words found: ' . implode(', ', $unique_vulgar_words));
                log_message('debug', 'Vulgar fields: ' . implode(', ', $vulgar_fields));

                // *** เพิ่ม: ส่งข้อมูล error พร้อมคำหยาบกลับไป ***
                $this->session->set_flashdata('save_vulgar', TRUE);
                $this->session->set_flashdata('vulgar_words', $unique_vulgar_words);
                $this->session->set_flashdata('vulgar_fields', $vulgar_fields);
                $this->session->set_flashdata('vulgar_message', 'พบคำไม่เหมาะสม: ' . implode(', ', $unique_vulgar_words));

                return false; // ไม่บันทึกข้อมูล
            }

            // *** เพิ่ม: ตรวจสอบ URL หลังจากตรวจสอบคำหยาบแล้ว ***
            if (isset($vulgar_result['has_url']) && $vulgar_result['has_url']) {
                log_message('debug', 'URLs detected in form data, blocking submission');

                $url_fields = isset($vulgar_result['url_detected_fields']) ? $vulgar_result['url_detected_fields'] : array();
                $error_message = 'ไม่อนุญาตให้มี URL หรือลิงก์ในข้อความ (พบใน: ' . implode(', ', $url_fields) . ')';

                log_message('debug', 'Model add_q_a: ' . $error_message);

                $this->session->set_flashdata('save_url_detected', TRUE);
                $this->session->set_flashdata('url_message', $error_message);
                $this->session->set_flashdata('url_fields', $url_fields);

                return false; // ไม่บันทึกข้อมูล
            }

            // *** เพิ่ม: ตรวจสอบ URL ด้วยวิธีแยกต่างหาก (สำรอง) ***
            log_message('debug', 'Model add_q_a: Starting additional URL check...');

            $url_check_fields = ['q_a_msg', 'q_a_detail'];
            if (!$is_logged_in) {
                $url_check_fields[] = 'q_a_by';
            }

            $url_detected = false;
            $url_in_fields = [];

            foreach ($url_check_fields as $field) {
                $field_value = isset($fields_to_check[$field]) ? $fields_to_check[$field] : '';
                if (!empty($field_value)) {
                    log_message('debug', 'Model add_q_a: Checking URL in field "' . $field . '": ' . $field_value);

                    // ใช้ vulgar_check library เพื่อตรวจสอบ URL
                    if (!$this->vulgar_check->check_no_urls($field_value)) {
                        $url_detected = true;
                        $url_in_fields[] = $field;
                        log_message('debug', 'Model add_q_a: URL detected in field "' . $field . '": ' . $field_value);
                    }
                }
            }

            // หากพบ URL ให้หยุดการทำงานและ set flash data
            if ($url_detected) {
                $error_message = 'ไม่อนุญาตให้มี URL หรือลิงก์ในข้อความ (พบใน: ' . implode(', ', $url_in_fields) . ')';

                log_message('debug', 'Model add_q_a: ' . $error_message);

                $this->session->set_flashdata('save_url_detected', TRUE);
                $this->session->set_flashdata('url_message', $error_message);
                $this->session->set_flashdata('url_fields', $url_in_fields);

                return false; // ไม่บันทึกข้อมูล
            }

            log_message('debug', 'No vulgar content or URLs detected, proceeding with save');

            // ตรวจสอบพื้นที่
            if (!$this->load->model('space_model')) {
                log_message('error', 'Cannot load space_model');
            }

            $used_space_mb = 0;
            $upload_limit_mb = 100;

            try {
                $used_space_mb = $this->space_model->get_used_space();
                $upload_limit_mb = $this->space_model->get_limit_storage();
            } catch (Exception $e) {
                log_message('error', 'Space model error: ' . $e->getMessage());
            }

            $total_space_required = 0;
            if (!empty($_FILES['q_a_imgs']['name'][0])) {
                foreach ($_FILES['q_a_imgs']['size'] as $size) {
                    $total_space_required += $size;
                }
            }

            if ($used_space_mb + ($total_space_required / (1024 * 1024)) >= $upload_limit_mb) {
                log_message('error', 'Storage limit exceeded');
                $this->session->set_flashdata('save_error', TRUE);
                $this->session->set_flashdata('error_message', 'พื้นที่จัดเก็บเต็ม ไม่สามารถอัพโหลดไฟล์ได้');
                return false;
            }

            $user_agent = $this->input->user_agent();
            $device_info = $this->parseUserAgent($user_agent);

            log_message('debug', 'User Agent: ' . $user_agent);
            log_message('debug', 'Device Info: ' . print_r($device_info, true));


            $ip = $this->input->ip_address();
            $country = $this->get_country_from_ip($ip);

            // *** แก้ไข: เตรียมข้อมูลพร้อม user_id ที่ถูกต้อง ***
            $q_a_data = array(
                'q_a_msg' => $q_a_msg,
                'q_a_detail' => $q_a_detail,
                'q_a_by' => $q_a_by,
                'q_a_email' => $q_a_email,
                'q_a_ip' => $ip,
                'q_a_country' => $country,
                'q_a_os' => $device_info['os'],              // เพิ่ม OS
                'q_a_browser' => $device_info['browser'],    // เพิ่ม Browser
                'q_a_user_agent' => substr($user_agent, 0, 255), // เก็บ user agent ดิบ (ถ้าต้องการ)

                'q_a_datesave' => date('Y-m-d H:i:s'),
                'q_a_user_type' => $is_logged_in ? $login_status['user_type'] : 'guest',
                'q_a_user_id' => $is_logged_in ? $user_info['user_id'] : null // *** ใช้ user_id ที่แก้ไขแล้ว ***
            );

            log_message('debug', 'Q&A data prepared: ' . print_r($q_a_data, true));

            // ตั้งค่าการอัพโหลดไฟล์
            $upload_path = './docs/img';
            if (!is_dir($upload_path)) {
                if (!mkdir($upload_path, 0755, true)) {
                    log_message('error', 'Cannot create upload directory: ' . $upload_path);
                    $this->session->set_flashdata('save_error', TRUE);
                    $this->session->set_flashdata('error_message', 'ไม่สามารถสร้างโฟลเดอร์อัพโหลดได้');
                    return false;
                }
            }

            $config['upload_path'] = $upload_path;
            $config['allowed_types'] = 'gif|jpg|png|jpeg|webp';
            $config['max_size'] = '5120'; // 5MB
            $config['encrypt_name'] = TRUE;

            $this->load->library('upload', $config);

            // เริ่ม transaction
            $this->db->trans_start();

            // บันทึกข้อมูลหลัก
            $this->db->insert('tbl_q_a', $q_a_data);
            $q_a_id = $this->db->insert_id();

            if (!$q_a_id) {
                $this->db->trans_rollback();
                log_message('error', 'Failed to insert Q&A data');
                log_message('error', 'Database error: ' . print_r($this->db->error(), true));
                $this->session->set_flashdata('save_error', TRUE);
                $this->session->set_flashdata('error_message', 'ไม่สามารถบันทึกข้อมูลลงฐานข้อมูลได้');
                return false;
            }

            log_message('debug', 'Q&A inserted successfully with ID: ' . $q_a_id . ' and fixed user_id: ' . (isset($user_info['user_id']) ? $user_info['user_id'] : 'null'));

            // *** เพิ่ม: อัพโหลดรูปภาพสำหรับกระทู้ ***
            if (!empty($_FILES['q_a_imgs']['name'][0])) {
                log_message('debug', '=== PROCESSING Q&A IMAGES ===');

                $q_a_imgs = $_FILES['q_a_imgs'];
                $image_data = array();
                $successful_uploads = 0;
                $failed_uploads = 0;

                // *** เพิ่ม: Debug ข้อมูลไฟล์ ***
                log_message('debug', 'Topic images to process: ' . count($q_a_imgs['name']));
                log_message('debug', 'Topic images array: ' . print_r($q_a_imgs, true));

                // *** แก้ไข: ตรวจสอบความซ้ำของไฟล์ ***
                $processed_files = array();

                foreach ($q_a_imgs['name'] as $index => $name) {
                    if (empty($name)) {
                        log_message('debug', "Skipping empty file at index {$index}");
                        continue;
                    }

                    // *** เพิ่ม: ตรวจสอบความซ้ำตาม name และ size ***
                    $file_key = $name . '_' . $q_a_imgs['size'][$index];
                    if (in_array($file_key, $processed_files)) {
                        log_message('debug', "Duplicate file detected: {$name} (size: {$q_a_imgs['size'][$index]})");
                        continue;
                    }
                    $processed_files[] = $file_key;

                    // ตรวจสอบ error code
                    if ($q_a_imgs['error'][$index] !== UPLOAD_ERR_OK) {
                        $failed_uploads++;
                        log_message('error', 'Topic upload error for file: ' . $name . ', Error code: ' . $q_a_imgs['error'][$index]);
                        continue;
                    }

                    log_message('debug', "Processing file {$index}: {$name} (size: {$q_a_imgs['size'][$index]})");

                    // สร้าง $_FILES entry สำหรับไฟล์เดียว
                    $_FILES['q_a_img']['name'] = $name;
                    $_FILES['q_a_img']['type'] = $q_a_imgs['type'][$index];
                    $_FILES['q_a_img']['tmp_name'] = $q_a_imgs['tmp_name'][$index];
                    $_FILES['q_a_img']['error'] = $q_a_imgs['error'][$index];
                    $_FILES['q_a_img']['size'] = $q_a_imgs['size'][$index];

                    // รีเซ็ต upload config
                    $this->upload->initialize($config);

                    if ($this->upload->do_upload('q_a_img')) {
                        $upload_data = $this->upload->data();

                        // *** เพิ่ม: ตรวจสอบความซ้ำในฐานข้อมูล ***
                        $existing_file = $this->db->get_where('tbl_q_a_img', array(
                            'q_a_img_ref_id' => $q_a_id,
                            'q_a_img_img' => $upload_data['file_name']
                        ))->row();

                        if ($existing_file) {
                            log_message('debug', 'File already exists in database: ' . $upload_data['file_name']);
                            // ลบไฟล์ที่อัพโหลดซ้ำ
                            if (file_exists($upload_data['full_path'])) {
                                unlink($upload_data['full_path']);
                            }
                            continue;
                        }

                        // สร้างไฟล์สำหรับ LINE (ถ้าต้องการ)
                        $file_ext = pathinfo($upload_data['file_name'], PATHINFO_EXTENSION);
                        $line_filename = 'line_topic_' . time() . '_' . uniqid() . '.' . $file_ext;
                        $final_line_filename = $upload_data['file_name']; // default

                        if (copy($upload_data['full_path'], $upload_path . '/' . $line_filename)) {
                            $final_line_filename = $line_filename;
                        }

                        $img_data = array(
                            'q_a_img_ref_id' => $q_a_id,
                            'q_a_img_img' => $upload_data['file_name']
                        );

                        // เพิ่ม line filename ถ้ามี field นี้
                        if ($this->db->field_exists('q_a_img_line', 'tbl_q_a_img')) {
                            $img_data['q_a_img_line'] = $final_line_filename;
                        }

                        $image_data[] = $img_data;

                        $successful_uploads++;
                        log_message('info', 'Successfully uploaded topic image: ' . $upload_data['file_name'] . ' for Q&A ID: ' . $q_a_id);
                    } else {
                        $failed_uploads++;
                        $error = $this->upload->display_errors('', '');
                        log_message('error', 'Topic upload failed for file ' . $name . ': ' . $error);
                    }
                }

                // บันทึกข้อมูลรูปภาพ
                if (!empty($image_data)) {
                    log_message('debug', 'Inserting ' . count($image_data) . ' image records');
                    log_message('debug', 'Image data: ' . print_r($image_data, true));

                    $insert_result = $this->db->insert_batch('tbl_q_a_img', $image_data);
                    if (!$insert_result) {
                        log_message('error', 'Failed to insert topic image data batch for Q&A ID: ' . $q_a_id);
                    } else {
                        log_message('info', 'Successfully inserted ' . count($image_data) . ' topic images for Q&A ID: ' . $q_a_id);
                    }
                }

                log_message('debug', "Topic upload summary: {$successful_uploads} successful, {$failed_uploads} failed");
                log_message('debug', '=== Q&A IMAGES PROCESSING COMPLETE ===');
            } else {
                log_message('debug', 'No topic images to process');
            }

            // *** เพิ่ม: สร้างการแจ้งเตือนสำหรับกระทู้ใหม่โดยตรง ***
            $this->createTopicNotificationDirect($q_a_id, $q_a_msg, $q_a_by, $user_info);

            // จบ transaction
            $this->db->trans_complete();

            if ($this->db->trans_status() === FALSE) {
                log_message('error', 'Transaction failed for Q&A ID: ' . $q_a_id);
                $this->session->set_flashdata('save_error', TRUE);
                $this->session->set_flashdata('error_message', 'เกิดข้อผิดพลาดในการทำ Transaction');
                return false;
            }

            log_message('info', 'Q&A transaction completed successfully for ID: ' . $q_a_id . ' with fixed user_id: ' . (isset($user_info['user_id']) ? $user_info['user_id'] : 'null'));

            // สร้าง notification (เดิม - notification_lib)
            try {
                $this->load->library('notification_lib');
                $this->notification_lib->new_qa($q_a_id, $q_a_data['q_a_msg'], $q_a_data['q_a_by']);
                log_message('debug', 'Notification_lib created for Q&A ID: ' . $q_a_id);
            } catch (Exception $e) {
                log_message('error', 'Notification_lib creation error: ' . $e->getMessage());
            }

            // ส่ง Line notification (เดิม)
            try {
                $this->sendLineNotification($q_a_id);
                log_message('debug', 'LINE notification sent for Q&A ID: ' . $q_a_id);
            } catch (Exception $e) {
                log_message('error', 'Line notification error: ' . $e->getMessage());
            }

            // อัพเดทพื้นที่ (เดิม)
            try {
                $this->space_model->update_server_current();
            } catch (Exception $e) {
                log_message('error', 'Space update error: ' . $e->getMessage());
            }

            $this->session->set_flashdata('save_success', TRUE);
            $this->session->set_flashdata('success_message', 'บันทึกกระทู้สำเร็จ');
            log_message('debug', '=== MODEL ADD_Q_A END ===');
            return $q_a_id;

        } catch (Exception $e) {
            // *** เพิ่ม: จัดการ Exception ที่ละเอียดขึ้น ***
            log_message('error', 'Q&A add error: ' . $e->getMessage());
            log_message('error', 'Q&A add stack trace: ' . $e->getTraceAsString());

            $this->session->set_flashdata('save_error', TRUE);
            $this->session->set_flashdata('error_message', 'เกิดข้อผิดพลาดระบบ: ' . $e->getMessage());
            return false;
        }
    }


    public function add_reply_q_a()
    {
        try {
            log_message('debug', '=== MODEL ADD_REPLY_Q_A START (ENHANCED VULGAR CHECK VERSION) ===');

            // *** แก้ไข 1: เช็ค POST data จาก Frontend ก่อน ***
            $frontend_user_id = $this->input->post('fixed_user_id');
            $frontend_user_type = $this->input->post('user_type');
            $frontend_user_email = $this->input->post('user_email');

            log_message('info', 'Reply Frontend data: user_id=' . $frontend_user_id . ', type=' . $frontend_user_type . ', email=' . $frontend_user_email);

            // *** แก้ไข 2: ใช้ข้อมูลจาก Frontend ถ้ามี ***
            if (!empty($frontend_user_id) && !empty($frontend_user_email)) {
                // ใช้ข้อมูลจาก Frontend (JavaScript ส่งมา)
                $is_logged_in = true;
                $user_info = [
                    'user_id' => $frontend_user_id,
                    'name' => $this->input->post('q_a_reply_by'),
                    'email' => $frontend_user_email
                ];
                $user_type = $frontend_user_type;

                log_message('info', '✅ Reply using Frontend user data: user_id=' . $frontend_user_id);
            } else {
                // Fallback: ใช้วิธีเดิม
                $login_status = $this->check_user_login();
                $is_logged_in = $login_status['is_logged_in'];
                $user_info = $login_status['user_info'];
                $user_type = $login_status['user_type'];

                log_message('info', '⚠️ Reply using Session user data: user_id=' . ($user_info['user_id'] ?? 'null'));
            }

            // *** รับข้อมูลจากฟอร์ม ***
            $q_a_reply_ref_id = $this->input->post('q_a_reply_ref_id');
            $q_a_reply_detail = $this->input->post('q_a_reply_detail');

            // จัดการชื่อและอีเมลตามสถานะการเข้าสู่ระบบ
            if ($is_logged_in) {
                $q_a_reply_by = $user_info['name'];

                // *** แก้ไข: ตรวจสอบอีเมลให้ครบถ้วน ***
                $q_a_reply_email = '';
                if (isset($user_info['email']) && !empty(trim($user_info['email']))) {
                    $q_a_reply_email = trim($user_info['email']);
                } else {
                    // หากไม่มีในข้อมูล user_info ให้ดึงจากฐานข้อมูล
                    $email_from_db = $this->getUserEmailById($user_info['user_id'], $user_type);
                    if ($email_from_db) {
                        $q_a_reply_email = $email_from_db;
                        log_message('info', 'Reply - Retrieved email from database: ' . $email_from_db);
                    }
                }

                log_message('debug', 'Reply - User logged in - using email: ' . $q_a_reply_email);
            } else {
                $q_a_reply_by = $this->input->post('q_a_reply_by');
                $q_a_reply_email = $this->input->post('q_a_reply_email');
                log_message('debug', 'Reply - Guest user - using posted email: ' . $q_a_reply_email);
            }

            // *** ตรวจสอบข้อมูลที่จำเป็น ***
            if (!$q_a_reply_ref_id) {
                log_message('error', 'Reply: Missing q_a_reply_ref_id');
                $this->session->set_flashdata('save_error', 'ไม่พบรหัสกระทู้ที่ต้องการตอบ');
                return false;
            }

            if (empty($q_a_reply_detail) || empty($q_a_reply_by)) {
                log_message('error', 'Reply: Missing required fields');
                $this->session->set_flashdata('save_error', 'ข้อมูลไม่ครบถ้วน');
                return false;
            }

            log_message('debug', 'Reply data: ref_id=' . $q_a_reply_ref_id . ', by=' . $q_a_reply_by);
            log_message('debug', '🎯 FINAL user_id to save: ' . ($user_info['user_id'] ?? 'null'));

            // *** เพิ่ม: ดึงข้อมูลกระทู้เดิมสำหรับ notification ***
            $original_topic = $this->db->select('q_a_msg, q_a_by, q_a_user_id, q_a_email')
                ->where('q_a_id', $q_a_reply_ref_id)
                ->get('tbl_q_a')
                ->row();

            if (!$original_topic) {
                log_message('error', 'Reply: Original topic not found for reply: ' . $q_a_reply_ref_id);
                $this->session->set_flashdata('save_error', 'ไม่พบกระทู้ที่ต้องการตอบ');
                return false;
            }

            // *** แก้ไข: ตรวจสอบ URL ก่อน (ย้ายมาก่อน vulgar check) ***
            $this->load->library('vulgar_check');

            log_message('debug', 'Reply: Checking URLs in content: "' . substr($q_a_reply_detail, 0, 100) . '"');

            try {
                $url_check_result = $this->vulgar_check->check_no_urls($q_a_reply_detail);
                log_message('debug', 'Reply: URL check result: ' . ($url_check_result ? 'PASS' : 'FAIL'));

                if (!$url_check_result) {
                    log_message('debug', 'Reply: URL detected in content: "' . $q_a_reply_detail . '"');

                    // ตรวจหา URL patterns
                    $detected_urls = array();
                    $url_patterns = [
                        'http(s)://' => '/https?:\/\/[^\s]+/i',
                        'www.' => '/\bwww\.[a-z0-9-]+(\.[a-z0-9-]+)*/i',
                        'domain.tld' => '/\b[a-z0-9-]{2,}\.(com|net|org|info|io|co|th|biz|xyz|app|dev|me|asia)\b/i'
                    ];

                    foreach ($url_patterns as $name => $pattern) {
                        if (preg_match_all($pattern, $q_a_reply_detail, $matches)) {
                            $detected_urls = array_merge($detected_urls, $matches[0]);
                            log_message('debug', 'Reply: Found ' . $name . ' pattern: ' . implode(', ', $matches[0]));
                        }
                    }

                    $this->session->set_flashdata('save_url_detected', TRUE);
                    $this->session->set_flashdata('url_message', 'ไม่อนุญาตให้มี URL หรือลิงก์ในการตอบกลับ');
                    $this->session->set_flashdata('detected_urls', array_unique($detected_urls));

                    log_message('debug', 'Reply: All detected URLs: ' . implode(', ', array_unique($detected_urls)));
                    return false;
                }
            } catch (Exception $e) {
                log_message('error', 'Reply: URL check exception: ' . $e->getMessage());
                log_message('error', 'Reply: URL check trace: ' . $e->getTraceAsString());

                // ในกรณี error ให้ผ่านไปเพื่อไม่ให้เกิด 500 error
                log_message('debug', 'Reply: URL check failed, allowing submission due to error');
            }

            log_message('debug', 'Reply: URL check completed successfully - no URLs found');

            // *** แก้ไข: ตรวจสอบคำหยาบหลัง (เฉพาะเมื่อไม่มี URL) ***
            $fields_to_check = array(
                'q_a_reply_by' => $q_a_reply_by,
                'q_a_reply_detail' => $q_a_reply_detail,
                'q_a_reply_email' => $q_a_reply_email
            );

            log_message('debug', 'Reply: Starting vulgar check for fields: ' . print_r($fields_to_check, true));

            // ตรวจสอบคำหยาบด้วย library
            $vulgar_result = $this->vulgar_check->check_form($fields_to_check);

            log_message('debug', 'Reply: Vulgar check result: ' . print_r($vulgar_result, true));

            if ($vulgar_result['has_vulgar']) {
                log_message('debug', 'Reply: Vulgar words detected, blocking submission');

                // *** เพิ่ม: เก็บข้อมูลคำหยาบเพื่อส่งกลับ ***
                $vulgar_words = array();
                $vulgar_fields = array();

                foreach ($vulgar_result['results'] as $field => $result) {
                    if ($result['has_vulgar']) {
                        $vulgar_words = array_merge($vulgar_words, $result['vulgar_words']);
                        $vulgar_fields[] = $field;
                    }
                }

                $unique_vulgar_words = array_unique($vulgar_words);

                log_message('debug', 'Reply: Vulgar words found: ' . implode(', ', $unique_vulgar_words));
                log_message('debug', 'Reply: Vulgar fields: ' . implode(', ', $vulgar_fields));

                // *** เพิ่ม: ส่งข้อมูล error พร้อมคำหยาบกลับไป ***
                $this->session->set_flashdata('save_vulgar', TRUE);
                $this->session->set_flashdata('vulgar_words', $unique_vulgar_words);
                $this->session->set_flashdata('vulgar_fields', $vulgar_fields);
                $this->session->set_flashdata('vulgar_message', 'พบคำไม่เหมาะสม: ' . implode(', ', $unique_vulgar_words));

                return false; // ไม่บันทึกข้อมูล
            }

            log_message('debug', 'Reply: No vulgar content detected, proceeding with save');

            // *** ตรวจสอบพื้นที่จัดเก็บ ***
            if (!$this->load->model('space_model')) {
                log_message('error', 'Reply: Cannot load space_model');
            }

            $used_space_mb = 0;
            $upload_limit_mb = 100;

            try {
                $used_space_mb = $this->space_model->get_used_space();
                $upload_limit_mb = $this->space_model->get_limit_storage();
            } catch (Exception $e) {
                log_message('error', 'Reply: Space model error: ' . $e->getMessage());
            }

            $total_space_required = 0;
            if (!empty($_FILES['q_a_reply_imgs']['name'][0])) {
                foreach ($_FILES['q_a_reply_imgs']['size'] as $size) {
                    $total_space_required += $size;
                }
            }

            if ($used_space_mb + ($total_space_required / (1024 * 1024)) >= $upload_limit_mb) {
                log_message('error', 'Reply: Storage limit exceeded');
                $this->session->set_flashdata('save_error', 'พื้นที่จัดเก็บเต็ม ไม่สามารถอัพโหลดไฟล์ได้');
                return false;
            }


            $user_agent = $this->input->user_agent();
            $device_info = $this->parseUserAgent($user_agent);

            log_message('debug', 'Reply User Agent: ' . $user_agent);
            log_message('debug', 'Reply Device Info: ' . print_r($device_info, true));


            // *** เตรียมข้อมูลสำหรับบันทึก ***
            $ip = $this->input->ip_address();
            $country = $this->get_country_from_ip($ip);

            $final_user_id = $is_logged_in ? $user_info['user_id'] : null;
            $final_user_type = $is_logged_in ? $user_type : 'guest';

            $q_a_reply_data = array(
                'q_a_reply_ref_id' => $q_a_reply_ref_id,
                'q_a_reply_by' => $q_a_reply_by,
                'q_a_reply_email' => $q_a_reply_email,
                'q_a_reply_detail' => $q_a_reply_detail,
                'q_a_reply_ip' => $ip,
                'q_a_reply_country' => $country,
                'q_a_reply_os' => $device_info['os'],           // เพิ่ม OS
                'q_a_reply_browser' => $device_info['browser'],  // เพิ่ม Browser
                'q_a_reply_user_agent' => substr($user_agent, 0, 255), // เก็บ user agent ดิบ

                'q_a_reply_datesave' => date('Y-m-d H:i:s'),
                'q_a_reply_user_type' => $final_user_type,
                'q_a_reply_user_id' => $final_user_id
            );

            log_message('debug', '📋 Reply data prepared: ' . print_r($q_a_reply_data, true));
            log_message('info', '🎯 CONFIRMED user_id to save: ' . ($final_user_id ?? 'NULL'));

            // *** ตั้งค่าการอัพโหลดไฟล์ ***
            $upload_path = './docs/img';
            if (!is_dir($upload_path)) {
                if (!mkdir($upload_path, 0755, true)) {
                    log_message('error', 'Reply: Cannot create upload directory: ' . $upload_path);
                    $this->session->set_flashdata('save_error', 'ไม่สามารถสร้างโฟลเดอร์อัพโหลดได้');
                    return false;
                }
            }

            $config['upload_path'] = $upload_path;
            $config['allowed_types'] = 'gif|jpg|png|jpeg|webp';
            $config['max_size'] = '5120'; // 5MB
            $config['encrypt_name'] = TRUE;

            $this->load->library('upload', $config);

            // *** เริ่ม transaction ***
            $this->db->trans_start();

            // *** บันทึกข้อมูล reply ***
            log_message('info', '💾 Inserting reply data with user_id: ' . ($final_user_id ?? 'NULL'));
            $this->db->insert('tbl_q_a_reply', $q_a_reply_data);
            $q_a_reply_id = $this->db->insert_id();

            if (!$q_a_reply_id) {
                $this->db->trans_rollback();
                log_message('error', '❌ Failed to insert reply data');
                log_message('error', '🔍 Last query: ' . $this->db->last_query());
                log_message('error', '🔍 DB Error: ' . print_r($this->db->error(), true));
                $this->session->set_flashdata('save_error', 'ไม่สามารถบันทึกการตอบกลับได้');
                return false;
            }

            log_message('info', '✅ Reply inserted successfully with ID: ' . $q_a_reply_id);

            // *** ตรวจสอบข้อมูลที่บันทึกจริง ***
            $saved_reply = $this->db->select('q_a_reply_id, q_a_reply_user_id, q_a_reply_user_type, q_a_reply_by')
                ->where('q_a_reply_id', $q_a_reply_id)
                ->get('tbl_q_a_reply')
                ->row();

            if ($saved_reply) {
                log_message('info', '🔍 ACTUAL saved data: ID=' . $saved_reply->q_a_reply_id .
                    ', user_id=' . $saved_reply->q_a_reply_user_id .
                    ', user_type=' . $saved_reply->q_a_reply_user_type .
                    ', by=' . $saved_reply->q_a_reply_by);

                // ตรวจสอบว่าบันทึกถูกต้องหรือไม่
                if ($saved_reply->q_a_reply_user_id != $final_user_id) {
                    log_message('error', '🚨 DATA MISMATCH! Expected: ' . $final_user_id . ', Got: ' . $saved_reply->q_a_reply_user_id);
                } else {
                    log_message('info', '✅ Data saved correctly! user_id: ' . $saved_reply->q_a_reply_user_id);
                }
            }

            // *** การอัพโหลดรูปภาพ Reply ***
            if (!empty($_FILES['q_a_reply_imgs']['name'][0])) {
                log_message('debug', '=== PROCESSING REPLY IMAGES ===');
                $this->process_reply_images($q_a_reply_id);
            } else {
                log_message('debug', 'No reply images to process');
            }

            // *** สร้างการแจ้งเตือนโดยตรงใน tbl_notification ***
            $this->createReplyNotificationDirect($q_a_reply_ref_id, $q_a_reply_by, $q_a_reply_detail, $q_a_reply_id, $original_topic, $user_info);

            // *** จบ transaction ***
            $this->db->trans_complete();

            if ($this->db->trans_status() === FALSE) {
                log_message('error', 'Reply transaction failed for Reply ID: ' . $q_a_reply_id);
                $this->session->set_flashdata('save_error', 'เกิดข้อผิดพลาดในการทำ Transaction');
                return false;
            }

            // *** ส่วนที่เหลือเหมือนเดิม (notification lib และ LINE) ***
            try {
                $this->load->library('notification_lib');
                $this->notification_lib->qa_reply($q_a_reply_ref_id, $q_a_reply_by, $q_a_reply_detail);
                log_message('debug', 'Notification_lib->qa_reply called successfully');
            } catch (Exception $e) {
                log_message('error', 'Reply notification creation error: ' . $e->getMessage());
            }

            try {
                $this->sendReplyLineNotification($q_a_reply_ref_id, $q_a_reply_data);
                log_message('debug', 'LINE notification sent for reply');
            } catch (Exception $e) {
                log_message('error', 'Reply Line notification error: ' . $e->getMessage());
            }

            // *** อัพเดทพื้นที่ ***
            try {
                $this->space_model->update_server_current();
            } catch (Exception $e) {
                log_message('error', 'Space update error: ' . $e->getMessage());
            }

            $this->session->set_flashdata('save_success', TRUE);
            log_message('debug', '=== MODEL ADD_REPLY_Q_A END ===');
            return $q_a_reply_id;

        } catch (Exception $e) {
            log_message('error', 'Reply add error: ' . $e->getMessage());
            log_message('error', 'Reply add stack trace: ' . $e->getTraceAsString());

            $this->session->set_flashdata('save_error', 'เกิดข้อผิดพลาดระบบ: ' . $e->getMessage());
            return false;
        }
    }




    private function parseUserAgent($userAgent)
    {
        if (empty($userAgent)) {
            return [
                'os' => 'Unknown',
                'browser' => 'Unknown'
            ];
        }

        // ตรวจสอบ OS พร้อมเวอร์ชัน
        $os = 'Unknown OS';

        // Windows versions
        if (preg_match('/windows nt 11/i', $userAgent)) {
            $os = 'Windows 11';
        } elseif (preg_match('/windows nt 10/i', $userAgent)) {
            $os = 'Windows 10';
        } elseif (preg_match('/windows nt 6\.3/i', $userAgent)) {
            $os = 'Windows 8.1';
        } elseif (preg_match('/windows nt 6\.2/i', $userAgent)) {
            $os = 'Windows 8';
        } elseif (preg_match('/windows nt 6\.1/i', $userAgent)) {
            $os = 'Windows 7';
        } elseif (preg_match('/windows/i', $userAgent)) {
            $os = 'Windows';
        }
        // macOS
        elseif (preg_match('/mac os x ([\d_\.]+)/i', $userAgent, $matches)) {
            $version = str_replace('_', '.', $matches[1]);
            $os = 'macOS ' . $version;
        } elseif (preg_match('/mac os x/i', $userAgent)) {
            $os = 'macOS';
        }
        // Mobile OS
        elseif (preg_match('/android[\s\/]([\d\.]+)/i', $userAgent, $matches)) {
            $os = 'Android ' . $matches[1];
        } elseif (preg_match('/android/i', $userAgent)) {
            $os = 'Android';
        } elseif (preg_match('/iphone.*os[\s_]([\d_]+)/i', $userAgent, $matches)) {
            $version = str_replace('_', '.', $matches[1]);
            $os = 'iOS ' . $version . ' (iPhone)';
        } elseif (preg_match('/iphone/i', $userAgent)) {
            $os = 'iOS (iPhone)';
        } elseif (preg_match('/ipad.*os[\s_]([\d_]+)/i', $userAgent, $matches)) {
            $version = str_replace('_', '.', $matches[1]);
            $os = 'iOS ' . $version . ' (iPad)';
        } elseif (preg_match('/ipad/i', $userAgent)) {
            $os = 'iOS (iPad)';
        }
        // Linux
        elseif (preg_match('/ubuntu/i', $userAgent)) {
            $os = 'Ubuntu Linux';
        } elseif (preg_match('/linux/i', $userAgent)) {
            $os = 'Linux';
        }

        // ตรวจสอบ Browser พร้อมเวอร์ชัน
        $browser = 'Unknown Browser';

        // Edge (ต้องตรวจก่อน Chrome)
        if (preg_match('/edg\/([\d\.]+)/i', $userAgent, $matches)) {
            $browser = 'Edge ' . $matches[1];
        } elseif (preg_match('/edg/i', $userAgent)) {
            $browser = 'Edge';
        }
        // Opera
        elseif (preg_match('/opr\/([\d\.]+)/i', $userAgent, $matches)) {
            $browser = 'Opera ' . $matches[1];
        } elseif (preg_match('/opera/i', $userAgent)) {
            $browser = 'Opera';
        }
        // Chrome
        elseif (preg_match('/chrome\/([\d\.]+)/i', $userAgent, $matches) && !preg_match('/edg/i', $userAgent)) {
            $browser = 'Chrome ' . $matches[1];
        } elseif (preg_match('/chrome/i', $userAgent) && !preg_match('/edg/i', $userAgent)) {
            $browser = 'Chrome';
        }
        // Safari
        elseif (preg_match('/version\/([\d\.]+).*safari/i', $userAgent, $matches)) {
            $browser = 'Safari ' . $matches[1];
        } elseif (preg_match('/safari/i', $userAgent) && !preg_match('/chrome/i', $userAgent)) {
            $browser = 'Safari';
        }
        // Firefox
        elseif (preg_match('/firefox\/([\d\.]+)/i', $userAgent, $matches)) {
            $browser = 'Firefox ' . $matches[1];
        } elseif (preg_match('/firefox/i', $userAgent)) {
            $browser = 'Firefox';
        }
        // Internet Explorer
        elseif (preg_match('/msie ([\d\.]+)/i', $userAgent, $matches)) {
            $browser = 'Internet Explorer ' . $matches[1];
        } elseif (preg_match('/trident.*rv:*([\d\.]+)/i', $userAgent, $matches)) {
            $browser = 'Internet Explorer ' . $matches[1];
        } elseif (preg_match('/msie|trident/i', $userAgent)) {
            $browser = 'Internet Explorer';
        }

        log_message('debug', 'ParseUserAgent - OS: ' . $os . ', Browser: ' . $browser);

        return [
            'os' => $os,
            'browser' => $browser
        ];
    }


    /**
     * เพิ่ม method นี้ใน Q_a_model.php
     * ประมวลผลรูปภาพสำหรับ Reply
     */
    public function process_reply_images($reply_id)
    {
        try {
            log_message('debug', 'process_reply_images: เริ่มประมวลผลรูปภาพสำหรับ reply_id: ' . $reply_id);

            if (empty($reply_id) || !is_numeric($reply_id)) {
                log_message('error', 'process_reply_images: Invalid reply_id: ' . $reply_id);
                return false;
            }

            // ตรวจสอบว่ามีการอัพโหลดไฟล์หรือไม่
            if (empty($_FILES['q_a_reply_imgs']['name'][0])) {
                log_message('debug', 'process_reply_images: ไม่มีไฟล์รูปภาพ');
                return true; // ไม่มีรูปภาพก็ถือว่าสำเร็จ
            }

            $this->load->library('upload');
            $upload_path = './docs/img/';

            // สร้างโฟลเดอร์หากไม่มี
            if (!is_dir($upload_path)) {
                if (!mkdir($upload_path, 0755, true)) {
                    log_message('error', 'process_reply_images: ไม่สามารถสร้างโฟลเดอร์ upload ได้');
                    return false;
                }
            }

            $config = array(
                'upload_path' => $upload_path,
                'allowed_types' => 'gif|jpg|png|jpeg|webp',
                'max_size' => 5120, // 5MB
                'encrypt_name' => TRUE,
                'remove_spaces' => TRUE
            );

            $this->upload->initialize($config);
            $files = $_FILES['q_a_reply_imgs'];
            $file_count = count($files['name']);
            $uploaded_count = 0;
            $total_files = 0;

            log_message('debug', 'process_reply_images: พบไฟล์รูปภาพ ' . $file_count . ' ไฟล์');

            for ($i = 0; $i < $file_count; $i++) {
                if (!empty($files['name'][$i]) && $files['error'][$i] === UPLOAD_ERR_OK) {
                    $total_files++;

                    // สร้าง $_FILES array สำหรับไฟล์เดียว
                    $_FILES['single_file'] = array(
                        'name' => $files['name'][$i],
                        'type' => $files['type'][$i],
                        'tmp_name' => $files['tmp_name'][$i],
                        'error' => $files['error'][$i],
                        'size' => $files['size'][$i]
                    );

                    if ($this->upload->do_upload('single_file')) {
                        $upload_data = $this->upload->data();

                        // *** แก้ไข: บันทึกข้อมูลตามโครงสร้าง Table จริง ***
                        $image_data = array(
                            'q_a_reply_img_ref_id' => $reply_id,
                            'q_a_reply_img_img' => $upload_data['file_name']
                            // *** เอา q_a_reply_img_create_date ออกเพราะไม่มีในตาราง ***
                        );

                        if ($this->db->insert('tbl_q_a_reply_img', $image_data)) {
                            $uploaded_count++;
                            log_message('info', 'process_reply_images: อัพโหลดสำเร็จ: ' . $upload_data['file_name'] . ' (Reply ID: ' . $reply_id . ')');
                        } else {
                            log_message('error', 'process_reply_images: ไม่สามารถบันทึกข้อมูลรูปภาพลงฐานข้อมูล: ' . $upload_data['file_name']);
                            log_message('error', 'Database error: ' . $this->db->error()['message']);
                        }
                    } else {
                        $error = $this->upload->display_errors('', '');
                        log_message('error', 'process_reply_images: อัพโหลดล้มเหลว ไฟล์ ' . $files['name'][$i] . ': ' . $error);
                    }

                    // ลบ $_FILES['single_file'] เพื่อเตรียมสำหรับไฟล์ถัดไป
                    unset($_FILES['single_file']);
                }
            }

            log_message('info', 'process_reply_images: อัพโหลดสำเร็จ ' . $uploaded_count . ' จาก ' . $total_files . ' ไฟล์');

            // ถือว่าสำเร็จถ้าอัพโหลดได้อย่างน้อย 1 ไฟล์ หรือไม่มีไฟล์เลย
            return ($uploaded_count > 0 || $total_files === 0);

        } catch (Exception $e) {
            log_message('error', 'process_reply_images: Exception: ' . $e->getMessage());
            log_message('error', 'process_reply_images: Stack trace: ' . $e->getTraceAsString());
            return false;
        }
    }


    /**
     * *** เพิ่ม: ฟังก์ชันดึงอีเมลจาก user_id (สำหรับใช้ใน LINE notification) ***
     */
    private function getUserEmailById($user_id, $user_type)
    {
        try {
            if (empty($user_id)) {
                log_message('debug', 'getUserEmailById: user_id is empty');
                return null;
            }

            log_message('debug', "getUserEmailById: Looking for user_id={$user_id}, type={$user_type}");

            if ($user_type === 'public') {
                $user = $this->db->select('mp_email')
                    ->where('id', $user_id) // ใช้ id สำหรับ public
                    ->get('tbl_member_public')
                    ->row();

                $email = $user ? $user->mp_email : null;
                log_message('debug', "getUserEmailById: Public user email = " . ($email ?: 'not found'));
                return $email;
            } else {
                // staff, system_admin, etc.
                $user = $this->db->select('m_email')
                    ->where('m_id', $user_id)
                    ->get('tbl_member')
                    ->row();

                $email = $user ? $user->m_email : null;
                log_message('debug', "getUserEmailById: Staff user email = " . ($email ?: 'not found'));
                return $email;
            }
        } catch (Exception $e) {
            log_message('error', 'getUserEmailById error: ' . $e->getMessage());
            return null;
        }
    }



    /**
     * สร้างการแจ้งเตือนสำหรับการตอบกระทู้โดยตรง (ใหม่) - *** แก้ไข JSON encoding ***
     */
    private function createReplyNotificationDirect($q_a_id, $reply_by, $reply_detail, $reply_id, $original_topic, $user_info)
    {
        try {
            log_message('info', "Creating reply notification directly for Q&A {$q_a_id} by {$reply_by}...");

            // ตรวจสอบว่ามี tbl_notifications หรือไม่
            if (!$this->db->table_exists('tbl_notifications')) {
                log_message('debug', 'tbl_notifications table does not exist');
                return false;
            }

            // ตัดข้อความให้สั้นลง
            $short_detail = mb_strlen($reply_detail) > 100 ?
                mb_substr($reply_detail, 0, 100) . '...' :
                $reply_detail;

            // *** สร้างข้อมูลตาม schema ที่มีจริง ***
            $notification_data = [
                'type' => 'qa_reply', // มี column นี้
                'title' => 'มีการตอบกระทู้: ' . $original_topic->q_a_msg, // มี column นี้
                'message' => $reply_by . ' ได้ตอบกระทู้ "' . $original_topic->q_a_msg . '": ' . $short_detail, // มี column นี้
                'reference_id' => $q_a_id, // มี column นี้
                'reference_table' => 'tbl_q_a', // มี column นี้
                'target_role' => 'public', // มี column นี้
                'priority' => 'normal', // มี column นี้
                'icon' => 'fas fa-reply', // มี column นี้
                'url' => 'Pages/q_a#comment-' . $q_a_id, // มี column นี้
                'created_at' => date('Y-m-d H:i:s'), // มี column นี้
                'is_read' => 0, // มี column นี้
                'is_system' => 1, // มี column นี้
                'is_archived' => 0 // มี column นี้
            ];

            // เพิ่ม user_id ของผู้ตอบ
            if (isset($user_info['user_id']) && $this->db->field_exists('created_by', 'tbl_notifications')) {
                $notification_data['created_by'] = $user_info['user_id'];
            }

            // เพิ่ม target_user_id (ผู้ที่จะได้รับการแจ้งเตือน = เจ้าของกระทู้)
            if ($this->db->field_exists('target_user_id', 'tbl_notifications')) {
                $notification_data['target_user_id'] = $original_topic->q_a_user_id;
            }

            // *** สำคัญ: เพิ่ม data field พร้อม JSON encoding ที่ถูกต้อง ***
            if ($this->db->field_exists('data', 'tbl_notifications')) {
                $data_array = [
                    'qa_id' => (int) $q_a_id,
                    'original_topic' => $original_topic->q_a_msg,
                    'replied_by' => $reply_by,
                    'reply_detail' => $short_detail,
                    'replied_at' => date('Y-m-d H:i:s'),
                    'url' => base_url('qa/view/' . $q_a_id),
                    'type' => 'public_reply_notification'
                ];

                // *** ใช้ JSON_UNESCAPED_UNICODE เพื่อแสดงภาษาไทยได้ถูกต้อง ***
                $notification_data['data'] = json_encode($data_array, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
            }

            log_message('info', 'Reply notification data: ' . print_r($notification_data, true));

            $insert_result = $this->db->insert('tbl_notifications', $notification_data);

            if ($insert_result) {
                $notification_id = $this->db->insert_id();
                log_message('info', 'SUCCESS: Direct reply notification created with ID: ' . $notification_id);
                return true;
            } else {
                log_message('error', 'FAILED: Direct reply notification insert failed');
                log_message('error', 'DB Error: ' . print_r($this->db->error(), true));
            }

            return false;

        } catch (Exception $e) {
            log_message('error', 'Exception in createReplyNotificationDirect: ' . $e->getMessage());
            return false;
        }
    }


    /**
     * สร้างการแจ้งเตือนสำหรับกระทู้ใหม่โดยตรง (เพิ่มใหม่) - *** แก้ไข JSON encoding ***
     */
    private function createTopicNotificationDirect($q_a_id, $topic_title, $topic_by, $user_info)
    {
        try {
            log_message('info', "Creating topic notification directly for Q&A {$q_a_id} by {$topic_by}...");

            // ตรวจสอบว่ามี tbl_notifications หรือไม่
            if (!$this->db->table_exists('tbl_notifications')) {
                log_message('debug', 'tbl_notifications table does not exist');
                return false;
            }

            // *** สร้างข้อมูลตาม schema ที่มีจริง ***
            $notification_data = [
                'type' => 'qa_new', // มี column นี้
                'title' => 'กระทู้ใหม่: ' . $topic_title, // มี column นี้
                'message' => $topic_by . ' ได้ตั้งกระทู้ใหม่ "' . $topic_title . '"', // มี column นี้
                'reference_id' => $q_a_id, // มี column นี้
                'reference_table' => 'tbl_q_a', // มี column นี้
                'target_role' => 'public', // มี column นี้
                'priority' => 'normal', // มี column นี้
                'icon' => 'fas fa-question-circle', // มี column นี้
                'url' => 'Pages/q_a#comment-' . $q_a_id, // มี column นี้
                'created_at' => date('Y-m-d H:i:s'), // มี column นี้
                'is_read' => 0, // มี column นี้
                'is_system' => 1, // มี column นี้
                'is_archived' => 0 // มี column นี้
            ];

            // เพิ่ม user_id ของผู้ตั้งกระทู้
            if (isset($user_info['user_id']) && $this->db->field_exists('created_by', 'tbl_notifications')) {
                $notification_data['created_by'] = $user_info['user_id'];
            }

            // *** สำคัญ: เพิ่ม data field พร้อม JSON encoding ที่ถูกต้อง ***
            if ($this->db->field_exists('data', 'tbl_notifications')) {
                // ดึงข้อมูลเพิ่มเติมจากกระทู้
                $qa_data = $this->db->get_where('tbl_q_a', array('q_a_id' => $q_a_id))->row();

                $data_array = [
                    'qa_id' => (int) $q_a_id,
                    'topic' => $topic_title,
                    'detail' => isset($qa_data->q_a_detail) ? mb_substr($qa_data->q_a_detail, 0, 100) . (mb_strlen($qa_data->q_a_detail) > 100 ? '...' : '') : '',
                    'author' => $topic_by,
                    'created_at' => date('Y-m-d H:i:s'),
                    'url' => base_url('qa/view/' . $q_a_id),
                    'type' => 'public_notification'
                ];

                // *** ใช้ JSON_UNESCAPED_UNICODE เพื่อแสดงภาษาไทยได้ถูกต้อง ***
                $notification_data['data'] = json_encode($data_array, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
            }

            log_message('info', 'Topic notification data: ' . print_r($notification_data, true));

            $insert_result = $this->db->insert('tbl_notifications', $notification_data);

            if ($insert_result) {
                $notification_id = $this->db->insert_id();
                log_message('info', 'SUCCESS: Direct topic notification created with ID: ' . $notification_id);
                return true;
            } else {
                log_message('error', 'FAILED: Direct topic notification insert failed');
                log_message('error', 'DB Error: ' . print_r($this->db->error(), true));
            }

            return false;

        } catch (Exception $e) {
            log_message('error', 'Exception in createTopicNotificationDirect: ' . $e->getMessage());
            return false;
        }
    }

    // *** ฟังก์ชันอื่นๆ ยังคงเดิม ***
    public function debug_file_upload()
    {
        if (!empty($_FILES)) {
            log_message('debug', '=== FILE UPLOAD DEBUG ===');
            log_message('debug', 'FILES array: ' . print_r($_FILES, true));

            if (isset($_FILES['q_a_imgs'])) {
                $files = $_FILES['q_a_imgs'];
                log_message('debug', 'Number of files: ' . count($files['name']));

                foreach ($files['name'] as $index => $name) {
                    if (!empty($name)) {
                        log_message('debug', "File {$index}: {$name}, Size: {$files['size'][$index]}, Type: {$files['type'][$index]}, Error: {$files['error'][$index]}");
                    }
                }
            }

            // ตรวจสอบ PHP settings
            log_message('debug', 'PHP upload_max_filesize: ' . ini_get('upload_max_filesize'));
            log_message('debug', 'PHP post_max_size: ' . ini_get('post_max_size'));
            log_message('debug', 'PHP max_file_uploads: ' . ini_get('max_file_uploads'));
            log_message('debug', '========================');
        }
    }

    // *** เพิ่มฟังก์ชันตรวจสอบ permission ***
    public function check_upload_permissions()
    {
        $upload_path = './docs/img';
        $permissions = array();

        $permissions['folder_exists'] = is_dir($upload_path);
        $permissions['folder_writable'] = is_writable($upload_path);
        $permissions['folder_readable'] = is_readable($upload_path);

        if (!$permissions['folder_exists']) {
            if (mkdir($upload_path, 0755, true)) {
                $permissions['folder_created'] = true;
                $permissions['folder_writable'] = is_writable($upload_path);
            } else {
                $permissions['folder_created'] = false;
            }
        }

        log_message('debug', 'Upload permissions check: ' . print_r($permissions, true));
        return $permissions;
    }

    /**
     * แก้ไข LINE notification ให้แสดงอีเมลและรูปภาพได้ถูกต้อง
     */
    private function sendLineNotification($q_a_id)
    {
        $QaData = $this->db->get_where('tbl_q_a', array('q_a_id' => $q_a_id))->row();

        if (!$QaData) {
            return false;
        }

        $message = "กระทู้ถาม-ตอบ ใหม่ !" . "\n";
        $message .= "หัวข้อคำถาม: " . $QaData->q_a_msg . "\n";
        $message .= "รายละเอียด: " . $QaData->q_a_detail . "\n";
        $message .= "ชื่อผู้ถาม: " . $QaData->q_a_by . "\n";
        $message .= "จาก IP: " . $QaData->q_a_ip . "\n";

        // *** แก้ไข 1: ตรวจสอบอีเมลให้ครบถ้วน ***
        if (!empty($QaData->q_a_email) && trim($QaData->q_a_email) !== '') {
            $message .= "อีเมล: " . $QaData->q_a_email . "\n";
        } else {
            // ถ้าไม่มีอีเมลในกระทู้ ให้ดึงจาก user_info ตาม user_id
            if (!empty($QaData->q_a_user_id)) {
                $user_email = $this->getUserEmailById($QaData->q_a_user_id, $QaData->q_a_user_type);
                if ($user_email) {
                    $message .= "อีเมล: " . $user_email . "\n";
                } else {
                    $message .= "อีเมล: ไม่ระบุ\n";
                }
            } else {
                $message .= "อีเมล: ไม่ระบุ\n";
            }
        }

        // *** แก้ไข 2: ปรับปรุงการจัดการรูปภาพ ***
        $images = $this->db->get_where(
            'tbl_q_a_img',
            array('q_a_img_ref_id' => $q_a_id)
        )->result();

        if ($images) {
            $imagePaths = [];
            foreach ($images as $image) {
                // *** ลำดับความสำคัญ: q_a_img_line > q_a_img_img ***
                if (!empty($image->q_a_img_line)) {
                    $imagePaths[] = $image->q_a_img_line;
                } elseif (!empty($image->q_a_img_img)) {
                    $imagePaths[] = $image->q_a_img_img;
                }
            }

            log_message('debug', 'LINE Notification Images: ' . implode(', ', $imagePaths));

            if (!empty($imagePaths)) {
                return $this->broadcastLineOAMessage($message, $imagePaths);
            } else {
                return $this->broadcastLineOAMessage($message);
            }
        } else {
            return $this->broadcastLineOAMessage($message);
        }
    }

    private function sendReplyLineNotification($q_a_id, $reply_data)
    {
        $QaData = $this->db->get_where('tbl_q_a', array('q_a_id' => $q_a_id))->row();

        if (!$QaData) {
            return false;
        }

        $message = "มีการตอบกระทู้ !" . "\n";
        $message .= "หัวข้อคำถาม: " . $QaData->q_a_msg . "\n";
        $message .= "ชื่อผู้ตอบ: " . $reply_data['q_a_reply_by'] . "\n";
        $message .= "รายละเอียดการตอบ: " . $reply_data['q_a_reply_detail'] . "\n";

        // *** แก้ไข: เพิ่มอีเมลผู้ตอบ ***
        if (!empty($reply_data['q_a_reply_email']) && trim($reply_data['q_a_reply_email']) !== '') {
            $message .= "อีเมลผู้ตอบ: " . $reply_data['q_a_reply_email'] . "\n";
        }

        // *** แก้ไข: เพิ่มรูปภาพใน reply ***
        $reply_images = $this->db->get_where(
            'tbl_q_a_reply_img',
            array('q_a_reply_img_ref_id' => $reply_data['q_a_reply_id'] ?? null)
        )->result();

        if ($reply_images) {
            $imagePaths = [];
            foreach ($reply_images as $image) {
                if (!empty($image->q_a_reply_img_img)) {
                    $imagePaths[] = $image->q_a_reply_img_img;
                }
            }

            if (!empty($imagePaths)) {
                return $this->broadcastLineOAMessage($message, $imagePaths);
            }
        }

        return $this->broadcastLineOAMessage($message);
    }

    private function broadcastLineOAMessage($message, $imagePaths = null)
    {
        $userIds = $this->db->select('line_user_id')
            ->from('tbl_line')
            ->where('line_status', 'show')
            ->get()
            ->result_array();

        $to = array_column($userIds, 'line_user_id');
        if (empty($to)) {
            log_message('debug', 'No LINE users found for notification');
            return false;
        }

        $to = array_filter($to);
        if (empty($to)) {
            log_message('debug', 'No active LINE users found');
            return false;
        }

        $headers = [
            'Content-Type: application/json',
            'Authorization: Bearer ' . $this->channelAccessToken
        ];

        $messages = [
            [
                'type' => 'text',
                'text' => $message
            ]
        ];

        // *** แก้ไข: ปรับปรุงการจัดการรูปภาพ ***
        if (is_array($imagePaths)) {
            $imagePaths = array_slice($imagePaths, 0, 5); // จำกัดไม่เกิน 5 รูป

            foreach ($imagePaths as $filename) {
                $imageUrl = $this->uploadImageToLine($filename);
                if ($imageUrl) {
                    $messages[] = [
                        'type' => 'image',
                        'originalContentUrl' => $imageUrl,
                        'previewImageUrl' => $imageUrl
                    ];
                    log_message('debug', 'Added image to LINE message: ' . $imageUrl);
                } else {
                    log_message('debug', 'Failed to create image URL for: ' . $filename);
                }
            }
        } elseif ($imagePaths) {
            $imageUrl = $this->uploadImageToLine($imagePaths);
            if ($imageUrl) {
                $messages[] = [
                    'type' => 'image',
                    'originalContentUrl' => $imageUrl,
                    'previewImageUrl' => $imageUrl
                ];
                log_message('debug', 'Added single image to LINE message: ' . $imageUrl);
            }
        }

        $chunks = array_chunk($to, 500);
        $success = true;

        foreach ($chunks as $receivers) {
            $data = [
                'to' => $receivers,
                'messages' => $messages
            ];

            log_message('debug', 'Sending LINE message to ' . count($receivers) . ' users');
            log_message('debug', 'LINE API Data: ' . json_encode($data, JSON_UNESCAPED_UNICODE));

            $ch = curl_init();
            curl_setopt($ch, CURLOPT_URL, $this->lineApiUrl);
            curl_setopt($ch, CURLOPT_POST, 1);
            curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($data));
            curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
            curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);

            $response = curl_exec($ch);
            $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);

            if ($httpCode !== 200) {
                $success = false;
                log_message('error', 'Line API Error (HTTP ' . $httpCode . '): ' . $response);
            } else {
                log_message('info', 'LINE notification sent successfully to ' . count($receivers) . ' users');
            }

            curl_close($ch);
        }

        return $success;
    }

    /**
     * *** แก้ไข: อัพเดต URL ให้ถูกต้อง ***
     */
    private function uploadImageToLine($filename)
    {
        if (empty($filename)) {
            return false;
        }

        // *** แก้ไข: เปลี่ยน URL ให้ถูกต้อง ***
        $baseUrl = base_url('docs/img/'); // จะสร้าง URL ตาม domain ปัจจุบัน
        $cleanFilename = basename($filename); // ป้องกัน path traversal

        $fullUrl = $baseUrl . $cleanFilename;

        // *** เพิ่ม: ตรวจสอบว่าไฟล์มีอยู่จริง ***
        $localPath = './docs/img/' . $cleanFilename;
        if (!file_exists($localPath)) {
            log_message('debug', 'Image file not found: ' . $localPath);
            return false;
        }

        log_message('debug', 'Created LINE image URL: ' . $fullUrl);
        return $fullUrl;
    }


    // ฟังก์ชันอื่นๆ ยังคงเหมือนเดิม...
    public function list_all()
    {
        $this->db->select('a.*, GROUP_CONCAT(ai.q_a_img_img) as additional_images');
        $this->db->from('tbl_q_a as a');
        $this->db->join('tbl_q_a_img as ai', 'a.q_a_id = ai.q_a_img_ref_id', 'left');
        $this->db->group_by('a.q_a_id');
        $this->db->order_by('a.q_a_datesave', 'DESC');
        $query = $this->db->get();
        return $query->result();
    }

    public function read_all_q_a_reply($q_a_id)
    {
        $this->db->where('q_a_reply_ref_id', $q_a_id);
        $query = $this->db->get('tbl_q_a_reply');
        return $query->result();
    }

    public function read($q_a_id)
    {
        $this->db->where('q_a_id', $q_a_id);
        $query = $this->db->get('tbl_q_a');
        if ($query->num_rows() > 0) {
            $data = $query->row();
            return $data;
        }
        return FALSE;
    }

    public function read_reply($q_a_id)
    {
        $this->db->select('tbl_q_a_reply.*, GROUP_CONCAT(tbl_q_a_reply_img.q_a_reply_img_img) as additional_images');
        $this->db->from('tbl_q_a_reply');
        $this->db->join('tbl_q_a_reply_img', 'tbl_q_a_reply.q_a_reply_id = tbl_q_a_reply_img.q_a_reply_img_ref_id', 'left');
        $this->db->where('tbl_q_a_reply.q_a_reply_ref_id', $q_a_id);
        $this->db->group_by('tbl_q_a_reply.q_a_reply_id');
        $this->db->order_by('tbl_q_a_reply.q_a_reply_id', 'DESC');
        $query = $this->db->get();
        $result = $query->result();
        return $result;
    }

    public function del_com($q_a_id)
    {
        $q_a_reply_ids = $this->db->select('q_a_reply_id')
            ->get_where('tbl_q_a_reply', array('q_a_reply_ref_id' => $q_a_id))
            ->result_array();

        $q_a_reply_ids = array_column($q_a_reply_ids, 'q_a_reply_id');

        if (!empty($q_a_reply_ids)) {
            $this->db->where_in('q_a_reply_id', $q_a_reply_ids)->delete('tbl_q_a_reply');

            $images = $this->db->where_in('q_a_reply_img_ref_id', $q_a_reply_ids)
                ->get('tbl_q_a_reply_img')
                ->result();

            $this->db->where_in('q_a_reply_img_ref_id', $q_a_reply_ids)->delete('tbl_q_a_reply_img');

            foreach ($images as $image) {
                $image_path = './docs/img/' . $image->q_a_reply_img_img;
                if (file_exists($image_path)) {
                    unlink($image_path);
                }
            }
        }

        $this->db->where('q_a_id', $q_a_id)->delete('tbl_q_a');

        $images = $this->db->get_where('tbl_q_a_img', array('q_a_img_ref_id' => $q_a_id))->result();
        $this->db->where('q_a_img_ref_id', $q_a_id)->delete('tbl_q_a_img');

        foreach ($images as $image) {
            $image_path = './docs/img/' . $image->q_a_img_img;
            if (file_exists($image_path)) {
                unlink($image_path);
            }
        }
    }

    public function del_com_reply($q_a_reply_id)
    {
        $this->db->where('q_a_reply_id', $q_a_reply_id)->delete('tbl_q_a_reply');

        $images = $this->db->get_where('tbl_q_a_reply_img', array('q_a_reply_img_ref_id' => $q_a_reply_id))->result();
        $this->db->where('q_a_reply_img_ref_id', $q_a_reply_id);
        $this->db->delete('tbl_q_a_reply_img');

        foreach ($images as $image) {
            $image_path = './docs/img/' . $image->q_a_reply_img_img;
            if (file_exists($image_path)) {
                unlink($image_path);
            }
        }
    }

    public function list_one()
    {
        $this->db->order_by('q_a_id', 'DESC');
        $this->db->limit(1);
        $query = $this->db->get('tbl_q_a');
        return $query->result();
    }

    public function q_a_frontend()
    {
        $this->db->order_by('q_a_id', 'DESC');
        $this->db->limit(5);
        $query = $this->db->get('tbl_q_a');
        return $query->result();
    }

    public function get_country_from_ip($ip)
    {
        if (empty($ip)) {
            return 'ไม่ระบุตัวตน';
        }

        if ($ip == '127.0.0.1' || $ip == '::1') {
            return 'localhost';
        }

        $url = "https://ipinfo.io/{$ip}/json";

        $curl = curl_init();
        curl_setopt($curl, CURLOPT_URL, $url);
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($curl, CURLOPT_CONNECTTIMEOUT, 5);
        curl_setopt($curl, CURLOPT_TIMEOUT, 5);
        $response = curl_exec($curl);
        curl_close($curl);

        $data = json_decode($response, true);

        if (isset($data['country'])) {
            return $data['country'];
        } else {
            return 'ไม่ทราบประเทศ';
        }
    }

    /**
     * ฟังก์ชันใหม่: แสดงสถิติ overflow data
     */
    public function get_overflow_statistics()
    {
        try {
            // นับกระทู้ที่มี overflow
            $overflow_topics = $this->db->where('q_a_user_id', '2147483647')
                ->or_where('q_a_user_id', 2147483647)
                ->count_all_results('tbl_q_a');

            // นับ reply ที่มี overflow
            $overflow_replies = $this->db->where('q_a_reply_user_id', '2147483647')
                ->or_where('q_a_reply_user_id', 2147483647)
                ->count_all_results('tbl_q_a_reply');

            // นับกระทู้ทั้งหมด
            $total_topics = $this->db->count_all('tbl_q_a');

            // นับ reply ทั้งหมด
            $total_replies = $this->db->count_all('tbl_q_a_reply');

            return [
                'overflow_topics' => $overflow_topics,
                'overflow_replies' => $overflow_replies,
                'total_topics' => $total_topics,
                'total_replies' => $total_replies,
                'topics_percentage' => $total_topics > 0 ? round(($overflow_topics / $total_topics) * 100, 2) : 0,
                'replies_percentage' => $total_replies > 0 ? round(($overflow_replies / $total_replies) * 100, 2) : 0
            ];

        } catch (Exception $e) {
            log_message('error', 'Get overflow statistics error: ' . $e->getMessage());
            return false;
        }
    }

    /**
     * ฟังก์ชันใหม่: ตรวจสอบ user_id ปัจจุบันใน session
     */
    public function get_current_user_info_fixed()
    {
        // ตรวจสอบสถานะ login แบบใหม่
        $login_status = $this->check_user_login();

        if (!$login_status['is_logged_in']) {
            return [
                'is_logged_in' => false,
                'user_id' => null,
                'user_type' => 'guest',
                'user_info' => null
            ];
        }

        return [
            'is_logged_in' => true,
            'user_id' => $login_status['user_info']['user_id'],
            'user_type' => $login_status['user_type'],
            'user_info' => $login_status['user_info']
        ];
    }

    /**
     * ฟังก์ชันใหม่: ตรวจสอบและแก้ไข overflow อัตโนมัติเมื่อมีการเข้าถึงข้อมูล
     */
    public function auto_fix_topic_on_access($topic_id)
    {
        $topic = $this->db->select('q_a_id, q_a_user_id, q_a_user_type, q_a_email')
            ->where('q_a_id', $topic_id)
            ->get('tbl_q_a')
            ->row();

        if (!$topic) {
            return false;
        }

        // ถ้าพบ overflow ให้แก้ไขทันที
        if ($topic->q_a_user_id == 2147483647 || $topic->q_a_user_id == '2147483647') {
            if (!empty($topic->q_a_email)) {
                $correct_user_id = $this->get_correct_user_id_by_email($topic->q_a_email, $topic->q_a_user_type);

                if ($correct_user_id) {
                    $this->db->where('q_a_id', $topic_id)
                        ->update('tbl_q_a', ['q_a_user_id' => $correct_user_id]);

                    log_message('info', "Auto-fixed topic {$topic_id} on access: {$topic->q_a_user_id} -> {$correct_user_id}");

                    // อัพเดทข้อมูลใน object
                    $topic->q_a_user_id = $correct_user_id;
                    return $topic;
                }
            }
        }

        return $topic;
    }

    /**
     * ฟังก์ชันใหม่: แก้ไขข้อมูล overflow ทั้งหมด
     */
    public function fix_all_overflow_data()
    {
        try {
            log_message('info', '=== STARTING COMPREHENSIVE OVERFLOW FIX ===');

            $fixed_topics = 0;
            $fixed_replies = 0;

            // 1. แก้ไขข้อมูลในตาราง tbl_q_a
            $overflow_topics = $this->db->select('q_a_id, q_a_email, q_a_user_id, q_a_user_type')
                ->where('q_a_user_id', '2147483647')
                ->or_where('q_a_user_id', 2147483647)
                ->get('tbl_q_a')
                ->result();

            foreach ($overflow_topics as $topic) {
                if (!empty($topic->q_a_email)) {
                    $correct_user_id = $this->get_correct_user_id_by_email($topic->q_a_email, $topic->q_a_user_type);

                    if ($correct_user_id && $correct_user_id != $topic->q_a_user_id) {
                        $this->db->where('q_a_id', $topic->q_a_id)
                            ->update('tbl_q_a', ['q_a_user_id' => $correct_user_id]);

                        log_message('info', "Fixed topic {$topic->q_a_id}: {$topic->q_a_user_id} -> {$correct_user_id}");
                        $fixed_topics++;
                    }
                }
            }

            // 2. แก้ไขข้อมูลในตาราง tbl_q_a_reply
            $overflow_replies = $this->db->select('q_a_reply_id, q_a_reply_email, q_a_reply_user_id, q_a_reply_user_type')
                ->where('q_a_reply_user_id', '2147483647')
                ->or_where('q_a_reply_user_id', 2147483647)
                ->get('tbl_q_a_reply')
                ->result();

            foreach ($overflow_replies as $reply) {
                if (!empty($reply->q_a_reply_email)) {
                    $correct_user_id = $this->get_correct_user_id_by_email($reply->q_a_reply_email, $reply->q_a_reply_user_type);

                    if ($correct_user_id && $correct_user_id != $reply->q_a_reply_user_id) {
                        $this->db->where('q_a_reply_id', $reply->q_a_reply_id)
                            ->update('tbl_q_a_reply', ['q_a_reply_user_id' => $correct_user_id]);

                        log_message('info', "Fixed reply {$reply->q_a_reply_id}: {$reply->q_a_reply_user_id} -> {$correct_user_id}");
                        $fixed_replies++;
                    }
                }
            }

            log_message('info', "=== OVERFLOW FIX COMPLETED: {$fixed_topics} topics, {$fixed_replies} replies ===");
            return ['topics' => $fixed_topics, 'replies' => $fixed_replies];

        } catch (Exception $e) {
            log_message('error', 'Fix all overflow data error: ' . $e->getMessage());
            return false;
        }
    }
}
?>