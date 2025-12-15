<?php
class Member_public_model extends CI_Model
{
    public function __construct()
    {
        parent::__construct();
        $this->load->model('space_model');
    }

    // ==========================================
    // MEMBER REGISTRATION & MANAGEMENT
    // ==========================================

    /**
     * สร้างสมาชิกใหม่ (ปรับปรุงให้สอดคล้องกับ Controller)
     * @param array $data ข้อมูลสมาชิก
     * @return mixed ID ของสมาชิกที่สร้างใหม่ หรือ false หากล้มเหลว
     */
    public function create_member($data)
    {
        try {
            log_message('info', '🚀 create_member called for: ' . $data['mp_email']);
            log_message('info', '📊 Input data - mp_birthdate: ' . ($data['mp_birthdate'] ?? 'NULL'));

            // เช็คข้อมูลซ้ำ
            if ($this->check_email_exists($data['mp_email'])) {
                log_message('error', '❌ Email already exists: ' . $data['mp_email']);
                return false;
            }

            if (!empty($data['mp_number']) && $this->check_id_card_exists($data['mp_number'])) {
                log_message('error', '❌ ID card already exists: ' . $data['mp_number']);
                return false;
            }

            // ✅ ตรวจสอบวันเกิดและอายุ
            if (!empty($data['mp_birthdate'])) {
                log_message('info', '📅 Validating birthdate: ' . $data['mp_birthdate']);

                try {
                    $birthdate = new DateTime($data['mp_birthdate']);
                    $today = new DateTime();
                    $age = $today->diff($birthdate)->y;

                    log_message('info', '🎂 Calculated age: ' . $age . ' years');

                    if ($age < 13) {
                        log_message('error', '❌ Age is less than 13 years: ' . $age);
                        return false;
                    }

                    log_message('info', '✅ Age validation passed');
                } catch (Exception $e) {
                    log_message('error', '❌ Invalid birthdate format: ' . $e->getMessage());
                    $data['mp_birthdate'] = null;
                }
            } else {
                log_message('info', '⚠️ No birthdate provided, setting to NULL');
            }

            // Default data
            $default_data = [
                'mp_status' => 1,
                'mp_registered_date' => date('Y-m-d H:i:s'),
                'mp_by' => '',
                'google2fa_enabled' => 0,
                'mp_updated_at' => date('Y-m-d H:i:s')
            ];

            $insert_data = array_merge($default_data, $data);

            // ตรวจสอบและปรับปรุงค่า NULL
            if (isset($insert_data['mp_number']) && empty($insert_data['mp_number'])) {
                $insert_data['mp_number'] = null;
            }

            if (isset($insert_data['mp_birthdate']) && empty($insert_data['mp_birthdate'])) {
                $insert_data['mp_birthdate'] = null;
                log_message('info', '⚠️ Empty birthdate converted to NULL');
            }

            log_message('info', '📊 Final insert data - mp_birthdate: ' . ($insert_data['mp_birthdate'] ?? 'NULL'));
            log_message('info', '💾 Attempting database insert...');

            // บันทึกลงฐานข้อมูล
            $result = $this->db->insert('tbl_member_public', $insert_data);

            if ($result) {
                $member_id = $this->db->insert_id();

                if (method_exists($this->space_model, 'update_server_current')) {
                    $this->space_model->update_server_current();
                }

                $id_info = $insert_data['mp_number'] ? 'with ID: ' . $insert_data['mp_number'] : 'without ID card';
                $birth_info = $insert_data['mp_birthdate'] ? ', birthdate: ' . $insert_data['mp_birthdate'] : ', no birthdate';

                log_message('info', "✅ Member created successfully: {$data['mp_email']} (DB ID: {$member_id}, {$id_info}{$birth_info})");
                return $member_id;
            } else {
                $db_error = $this->db->error();
                log_message('error', '❌ Database insert failed: ' . $db_error['message']);
                log_message('error', '❌ Error code: ' . $db_error['code']);
                return false;
            }

        } catch (Exception $e) {
            log_message('error', '❌ Exception in create_member: ' . $e->getMessage());
            log_message('error', '❌ Stack trace: ' . $e->getTraceAsString());
            return false;
        }
    }

    /**
     * เมธอดเก่าสำหรับ backward compatibility
     */
    public function register()
    {
        $mp_email = $this->input->post('mp_email');
        $mp_number = $this->input->post('mp_number');

        // เช็คอีเมลซ้ำ
        if ($this->check_email_exists($mp_email)) {
            echo "<script>alert('อีเมลนี้ถูกใช้งานแล้ว'); window.history.go(-3);</script>";
            exit;
        }

        // เช็คเลขบัตรประชาชนซ้ำ
        if ($this->check_id_card_exists($mp_number)) {
            echo "<script>alert('เลขบัตรประชาชนนี้ถูกใช้งานแล้ว'); window.history.go(-3);</script>";
            exit;
        }

        // Upload configuration
        $config['upload_path'] = './docs/img';
        $config['allowed_types'] = 'gif|jpg|png';
        $this->load->library('upload', $config);

        // Upload main file
        $filename = null;
        if (!empty($_FILES['mp_img']['name'])) {
            if ($this->upload->do_upload('mp_img')) {
                $data = $this->upload->data();
                $filename = $data['file_name'];
            }
        }

        $generated_mp_id = $this->generateUniqueUserId();

        $data = array(
            'mp_id' => $generated_mp_id,
            'mp_email' => $mp_email,
            'mp_password' => sha1($this->input->post('mp_password')),
            'mp_prefix' => $this->input->post('mp_prefix'),
            'mp_fname' => $this->input->post('mp_fname'),
            'mp_lname' => $this->input->post('mp_lname'),
            'mp_phone' => $this->input->post('mp_phone'),
            'mp_number' => $mp_number,
            'mp_address' => $this->input->post('mp_address'),
            'mp_img' => $filename,
            'mp_status' => 1,
            'mp_registered_date' => date('Y-m-d H:i:s')
        );

        $this->db->insert('tbl_member_public', $data);
        $mp_id = $this->db->insert_id();

        if ($mp_id) {
            $this->session->set_flashdata('save_success', TRUE);
        }

        if (method_exists($this->space_model, 'update_server_current')) {
            $this->space_model->update_server_current();
        }

        return $mp_id;
    }

    /**
     * ดึงข้อมูลผู้ใช้สำหรับ login
     * @param string $mp_email
     * @param string $mp_password (SHA1 hashed)
     * @return object|null
     */
    public function fetch_user_login($mp_email, $mp_password)
    {
        try {
            $this->db->select('mp_id, mp_email, mp_fname, mp_lname, mp_prefix, mp_img, mp_phone, mp_number, mp_address, mp_status, google2fa_secret, google2fa_enabled, mp_district, mp_amphoe, mp_province, mp_zipcode');
            $this->db->where('mp_email', $mp_email);
            $this->db->where('mp_password', $mp_password);
            $this->db->where('mp_status', 1); // เฉพาะที่ใช้งานได้
            $query = $this->db->get('tbl_member_public');

            return $query->row();

        } catch (Exception $e) {
            log_message('error', 'Error in fetch_user_login: ' . $e->getMessage());
            return null;
        }
    }

    /**
     * ดึงข้อมูลสมาชิกตาม ID
     * @param string|int $user_id
     * @return object|null
     */
    public function get_member_by_id($user_id)
    {
        try {
            $this->db->where('mp_id', $user_id);
            $query = $this->db->get('tbl_member_public');

            if ($query->num_rows() > 0) {
                return $query->row();
            }

            return null;
        } catch (Exception $e) {
            log_message('error', 'Error in get_member_by_id: ' . $e->getMessage());
            return null;
        }
    }

    // ==========================================
    // PROFILE MANAGEMENT
    // ==========================================

    /**
     * อัพเดทข้อมูลโปรไฟล์แบบเต็ม (ปรับปรุงให้รองรับ address columns ใหม่)
     * @param string|int $user_id
     * @param array $data
     * @return bool
     */
    public function update_full_profile($user_id, $data)
    {
        try {
            // ตรวจสอบข้อมูลพื้นฐาน
            if (empty($user_id) || empty($data)) {
                return false;
            }

            // ⭐ เพิ่ม: ตรวจสอบ ID number ถ้ามีการอัพเดท
            if (isset($data['mp_number']) && !empty($data['mp_number'])) {
                if ($this->check_id_card_exists($data['mp_number'], $user_id)) {
                    log_message('error', 'ID card already exists when updating profile: ' . $data['mp_number']);
                    throw new Exception('เลขบัตรประชาชนนี้มีผู้ใช้งานแล้ว');
                }
            }

            // ⭐ เพิ่ม: จัดการกรณี ID number เป็นค่าว่าง
            if (isset($data['mp_number']) && empty($data['mp_number'])) {
                $data['mp_number'] = null; // ใช้ null แทน empty string
            }

            // เพิ่ม timestamp
            $data['mp_updated_at'] = date('Y-m-d H:i:s');

            // ⭐ เพิ่ม: Log การอัพเดท ID number
            if (isset($data['mp_number'])) {
                $id_info = $data['mp_number'] ? 'to ID: ' . $data['mp_number'] : 'removed ID number';
                log_message('info', "Updating ID number for user $user_id - $id_info");
            }

            $this->db->where('mp_id', $user_id);
            $result = $this->db->update('tbl_member_public', $data);

            if ($result) {
                log_message('info', 'Profile updated for user ID: ' . $user_id);
                return true;
            } else {
                log_message('error', 'Failed to update profile for user ID: ' . $user_id);
                log_message('error', 'Database error: ' . $this->db->error()['message']);
                return false;
            }

        } catch (Exception $e) {
            log_message('error', 'Error in update_full_profile: ' . $e->getMessage());
            return false;
        }
    }

    /**
     * อัพเดทข้อมูลพื้นฐาน (backward compatibility)
     * @param int $id
     * @return bool
     */
    public function update_profile($id)
    {
        $data = array(
            'mp_fname' => $this->input->post('mp_fname'),
            'mp_lname' => $this->input->post('mp_lname'),
            'mp_phone' => $this->input->post('mp_phone'),
            'mp_email' => $this->input->post('mp_email'),
            'mp_updated_at' => date('Y-m-d H:i:s')
        );

        return $this->db->where('mp_id', $id)->update('tbl_member_public', $data);
    }

    /**
     * อัพเดทข้อมูลที่อยู่แบบใหม่ (รองรับ address columns แยกย่อย)
     * @param string|int $user_id
     * @param array $address_data
     * @return bool
     */
    public function update_address($user_id, $address_data)
    {
        try {
            $data = [
                'mp_address' => $address_data['additional_address'] ?? '',
                'mp_district' => $address_data['district'] ?? '',
                'mp_amphoe' => $address_data['amphoe'] ?? '',
                'mp_province' => $address_data['province'] ?? '',
                'mp_zipcode' => $address_data['zipcode'] ?? '',
                'mp_updated_at' => date('Y-m-d H:i:s')
            ];

            $this->db->where('mp_id', $user_id);
            $result = $this->db->update('tbl_member_public', $data);

            if ($result) {
                log_message('info', 'Address updated for user ID: ' . $user_id);
                return true;
            }

            return false;

        } catch (Exception $e) {
            log_message('error', 'Error in update_address: ' . $e->getMessage());
            return false;
        }
    }

    // ==========================================
    // 2FA MANAGEMENT
    // ==========================================

    /**
     * ดึงข้อมูล 2FA ของผู้ใช้
     * @param string|int $user_id
     * @return object|null
     */
    public function get_2fa_info($user_id)
    {
        try {
            $this->db->select('mp_id, google2fa_secret, google2fa_enabled, google2fa_setup_date');
            $this->db->where('mp_id', $user_id);
            $query = $this->db->get('tbl_member_public');

            if ($query->num_rows() > 0) {
                return $query->row();
            }

            return null;
        } catch (Exception $e) {
            log_message('error', 'Error in get_2fa_info: ' . $e->getMessage());
            return null;
        }
    }

    /**
     * บันทึก Google 2FA Secret
     * @param string|int $user_id
     * @param string $secret
     * @return bool
     */
    public function save_2fa_secret($user_id, $secret)
    {
        try {
            $data = array(
                'google2fa_secret' => $secret,
                'google2fa_enabled' => 1,
                'google2fa_setup_date' => date('Y-m-d H:i:s'),
                'mp_updated_at' => date('Y-m-d H:i:s')
            );

            $this->db->where('mp_id', $user_id);
            $result = $this->db->update('tbl_member_public', $data);

            if ($result) {
                log_message('info', '2FA enabled for user ID: ' . $user_id);
                return true;
            } else {
                log_message('error', 'Failed to enable 2FA for user ID: ' . $user_id);
                log_message('error', 'Database error: ' . $this->db->error()['message']);
                return false;
            }

        } catch (Exception $e) {
            log_message('error', 'Error in save_2fa_secret: ' . $e->getMessage());
            return false;
        }
    }

    /**
     * เปิด/ปิดการใช้งาน 2FA
     * @param string|int $user_id
     * @param bool $enabled
     * @return bool
     */
    public function toggle_2fa($user_id, $enabled = true)
    {
        try {
            $data = array(
                'google2fa_enabled' => $enabled ? 1 : 0,
                'mp_updated_at' => date('Y-m-d H:i:s')
            );

            // ถ้าปิดใช้งาน ให้ลบ secret ด้วย
            if (!$enabled) {
                $data['google2fa_secret'] = null;
                $data['google2fa_setup_date'] = null;
            }

            $this->db->where('mp_id', $user_id);
            $result = $this->db->update('tbl_member_public', $data);

            if ($result) {
                $status = $enabled ? 'enabled' : 'disabled';
                log_message('info', '2FA ' . $status . ' for user ID: ' . $user_id);
                return true;
            } else {
                log_message('error', 'Failed to toggle 2FA for user ID: ' . $user_id);
                return false;
            }

        } catch (Exception $e) {
            log_message('error', 'Error in toggle_2fa: ' . $e->getMessage());
            return false;
        }
    }

    /**
     * ตรวจสอบว่าผู้ใช้เปิดใช้งาน 2FA หรือไม่
     * @param string|int $mp_id
     * @return bool
     */
    public function is_2fa_enabled($mp_id)
    {
        try {
            $this->db->select('google2fa_enabled');
            $this->db->where('mp_id', $mp_id);
            $this->db->where('google2fa_enabled', 1);
            $this->db->where('google2fa_secret IS NOT NULL');
            $query = $this->db->get('tbl_member_public');
            return $query->num_rows() > 0;
        } catch (Exception $e) {
            log_message('error', 'Error in is_2fa_enabled: ' . $e->getMessage());
            return false;
        }
    }

    /**
     * ตรวจสอบ 2FA columns ในฐานข้อมูล
     * @return bool
     */
    public function check_2fa_columns()
    {
        try {
            $fields = $this->db->list_fields('tbl_member_public');

            $required_fields = ['google2fa_secret', 'google2fa_enabled', 'google2fa_setup_date'];
            $missing_fields = [];

            foreach ($required_fields as $field) {
                if (!in_array($field, $fields)) {
                    $missing_fields[] = $field;
                }
            }

            if (count($missing_fields) > 0) {
                log_message('error', 'Missing 2FA columns in tbl_member_public: ' . implode(', ', $missing_fields));
                return false;
            }

            return true;

        } catch (Exception $e) {
            log_message('error', 'Error checking 2FA columns: ' . $e->getMessage());
            return false;
        }
    }

    // ==========================================
    // MEMBER LISTING & FILTERING
    // ==========================================

    /**
     * ดึงข้อมูลสมาชิกทั้งหมดแบบง่าย
     * @return array
     */
    public function get_all_members()
    {
        return $this->db->select('mp_id, mp_fname, mp_lname, mp_email')
            ->from('tbl_member_public')
            ->where('mp_status', 1)
            ->order_by('mp_fname', 'ASC')
            ->get()
            ->result();
    }

    /**
     * ดึงข้อมูลสมาชิกภายนอกทั้งหมดพร้อมการกรอง
     * @param string $search
     * @param int|null $limit
     * @param int $offset
     * @param string $sort_by
     * @param string $sort_order
     * @return array
     */
    public function get_filtered_members($search = '', $limit = null, $offset = 0, $sort_by = 'mp_id', $sort_order = 'desc')
    {
        $column_map = [
            'name' => 'mp_fname',
            'email' => 'mp_email',
            'id_card' => 'mp_number',
            'mp_id' => 'mp_id'
        ];

        $sort_column = isset($column_map[$sort_by]) ? $column_map[$sort_by] : 'mp_id';

        $this->db->select('*');
        $this->db->from('tbl_member_public');

        // เงื่อนไขการค้นหา
        if (!empty($search)) {
            $this->db->group_start();
            $this->db->like('mp_fname', $search);
            $this->db->or_like('mp_lname', $search);
            $this->db->or_like('mp_email', $search);
            $this->db->or_like('mp_phone', $search);

            // ⭐ ค้นหาเลขบัตรประชาชนโดยไม่รวม null values
            $this->db->or_group_start();
            $this->db->like('mp_number', $search);
            $this->db->where('mp_number IS NOT NULL');
            $this->db->group_end();

            $this->db->group_end();
        }

        $this->db->order_by($sort_column, $sort_order);

        if ($limit !== null) {
            $this->db->limit($limit, $offset);
        }

        return $this->db->get()->result();
    }

    /**
     * นับจำนวนสมาชิกที่กรองแล้ว
     * @param string $search
     * @return int
     */
    public function count_filtered_members($search = '')
    {
        if (!empty($search)) {
            $this->db->group_start();
            $this->db->like('mp_fname', $search);
            $this->db->or_like('mp_lname', $search);
            $this->db->like('mp_email', $search);
            $this->db->or_like('mp_phone', $search);

            // ⭐ ค้นหาเลขบัตรประชาชนโดยไม่รวม null values
            $this->db->or_group_start();
            $this->db->like('mp_number', $search);
            $this->db->where('mp_number IS NOT NULL');
            $this->db->group_end();

            $this->db->group_end();
        }

        return $this->db->count_all_results('tbl_member_public');
    }




    public function update_id_card($user_id, $id_number)
    {
        try {
            // ตรวจสอบรูปแบบเลขบัตรประชาชน
            if (!preg_match('/^\d{13}$/', $id_number)) {
                log_message('error', 'Invalid ID card format: ' . $id_number);
                return false;
            }

            // ตรวจสอบความซ้ำ
            if ($this->check_id_card_exists($id_number, $user_id)) {
                log_message('error', 'ID card already exists: ' . $id_number);
                return false;
            }

            $data = [
                'mp_number' => $id_number,
                'mp_updated_at' => date('Y-m-d H:i:s')
            ];

            $this->db->where('mp_id', $user_id);
            $result = $this->db->update('tbl_member_public', $data);

            if ($result) {
                log_message('info', 'ID card updated for user: ' . $user_id);
                return true;
            }

            return false;

        } catch (Exception $e) {
            log_message('error', 'Error in update_id_card: ' . $e->getMessage());
            return false;
        }
    }




    /**
     * ⭐ ใหม่: ดึงสถิติเลขบัตรประชาชน
     * @return array
     */
    public function get_id_card_stats()
    {
        $total_members = $this->count_all_members();

        // นับสมาชิกที่มีเลขบัตรประชาชน
        $with_id = $this->db->where('mp_number IS NOT NULL')
            ->where('mp_number !=', '')
            ->count_all_results('tbl_member_public');

        // นับสมาชิกที่ไม่มีเลขบัตรประชาชน
        $without_id = $this->db->group_start()
            ->where('mp_number IS NULL')
            ->or_where('mp_number', '')
            ->group_end()
            ->count_all_results('tbl_member_public');

        return [
            'total_members' => $total_members,
            'with_id_card' => $with_id,
            'without_id_card' => $without_id,
            'completion_rate' => $total_members > 0 ? round(($with_id / $total_members) * 100, 2) : 0
        ];
    }

    /**
     * นับจำนวนสมาชิกทั้งหมด
     * @return int
     */
    public function count_all_members()
    {
        return $this->db->count_all_results('tbl_member_public');
    }

    /**
     * นับจำนวนสมาชิกที่ยืนยันตัวตนแล้ว
     * @return int
     */
    public function count_verified_members()
    {
        // ใช้ฟิลด์ mp_status แทน (สมมติว่าสถานะ 1 คือใช้งานได้)
        return $this->db->where('mp_status', 1)->count_all_results('tbl_member_public');
    }

    /**
     * นับจำนวนสมาชิกที่ลงทะเบียนในเดือนนี้
     * @return int
     */
    public function count_new_members_this_month()
    {
        $first_day_of_month = date('Y-m-01 00:00:00');
        $last_day_of_month = date('Y-m-t 23:59:59');

        // ใช้ฟิลด์ mp_registered_date
        if ($this->db->field_exists('mp_registered_date', 'tbl_member_public')) {
            $this->db->where('mp_registered_date >=', $first_day_of_month);
            $this->db->where('mp_registered_date <=', $last_day_of_month);
            return $this->db->count_all_results('tbl_member_public');
        }

        // ถ้าไม่มีฟิลด์วันที่ลงทะเบียน ให้คืนค่า 0
        return 0;
    }

    // ==========================================
    // MEMBER MANAGEMENT (ADMIN)
    // ==========================================

    /**
     * อัพเดทสถานะของสมาชิก
     * @param string|int $mp_id
     * @param int $status
     * @return bool
     */
    public function update_status($mp_id, $status)
    {
        $data = [
            'mp_status' => $status,
            'mp_updated_by' => $this->session->userdata('m_fname') ?? 'System',
            'mp_updated_date' => date('Y-m-d H:i:s'),
            'mp_updated_at' => date('Y-m-d H:i:s')
        ];

        $this->db->where('mp_id', $mp_id);
        $this->db->update('tbl_member_public', $data);

        return ($this->db->affected_rows() > 0);
    }

    /**
     * ลบข้อมูลสมาชิก
     * @param string|int $mp_id
     * @return bool
     */
    public function delete_member($mp_id)
    {
        try {
            // ดึงข้อมูลรูปภาพก่อนลบ
            $member = $this->get_member_by_id($mp_id);

            $this->db->where('mp_id', $mp_id);
            $result = $this->db->delete('tbl_member_public');

            // ลบรูปภาพถ้ามี
            if ($result && $member && !empty($member->mp_img)) {
                $image_paths = [
                    './docs/img/' . $member->mp_img,
                    './uploads/' . $member->mp_img
                ];

                foreach ($image_paths as $path) {
                    if (file_exists($path)) {
                        @unlink($path);
                        break;
                    }
                }
            }

            return ($this->db->affected_rows() > 0);

        } catch (Exception $e) {
            log_message('error', 'Error in delete_member: ' . $e->getMessage());
            return false;
        }
    }

    // ==========================================
    // VALIDATION HELPERS
    // ==========================================

    /**
     * ตรวจสอบอีเมลซ้ำ
     * @param string $email
     * @param string|int|null $exclude_id
     * @return bool
     */
    public function check_email_exists($email, $exclude_id = null)
    {
        $this->db->where('mp_email', $email);

        if ($exclude_id !== null) {
            $this->db->where('mp_id !=', $exclude_id);
        }

        $count = $this->db->count_all_results('tbl_member_public');
        return ($count > 0);
    }

    /**
     * ตรวจสอบเลขบัตรประชาชนซ้ำ
     * @param string $id_card
     * @param string|int|null $exclude_id
     * @return bool
     */
    public function check_id_card_exists($id_card, $exclude_id = null)
    {
        // ถ้าไม่มีเลขบัตรประชาชน ถือว่าไม่ซ้ำ
        if (empty($id_card) || is_null($id_card)) {
            return false;
        }

        $this->db->where('mp_number', $id_card);

        // เพิ่มเงื่อนไขไม่ให้เช็คกับ null values
        $this->db->where('mp_number IS NOT NULL');

        if ($exclude_id !== null) {
            $this->db->where('mp_id !=', $exclude_id);
        }

        $count = $this->db->count_all_results('tbl_member_public');
        return ($count > 0);
    }

    /**
     * ดึงข้อมูลผู้ใช้ตามอีเมล (สำหรับ Controller)
     * @param string $email
     * @return object|null
     */
    public function get_user_by_email($email)
    {
        return $this->db->select('mp_id, mp_email, mp_fname, mp_lname')
            ->where('mp_email', $email)
            ->where('mp_status', 1)
            ->get('tbl_member_public')
            ->row();
    }

    // ==========================================
    // ID GENERATION
    // ==========================================

    /**
     * สร้างหมายเลข user_id ที่ไม่ซ้ำกัน ตามรูปแบบ: ปี (2 หลัก) + Timestamp
     * ตัวอย่าง: ปี 2025 + timestamp 1716360000 = 251716360000
     * @return string
     */
    private function generateUserId()
    {
        // ดึงปีปัจจุบันและแปลงเป็น 2 หลัก (เช่น 2025 -> 25)
        $year = substr(date('Y'), -2);

        // ดึง timestamp ปัจจุบัน
        $timestamp = time();

        // รวมปี + timestamp
        $userId = $year . $timestamp;

        return $userId;
    }

    /**
     * เวอร์ชั่นทางเลือกที่มี microseconds เพื่อความเฉพาะเจาะจงสูงขึ้น
     * รูปแบบ: ปี (2 หลัก) + Timestamp + Microseconds (3 หลักสุดท้าย)
     * @return string
     */
    private function generateUserIdWithMicroseconds()
    {
        // ดึงปีปัจจุบันและแปลงเป็น 2 หลัก
        $year = substr(date('Y'), -2);

        // ดึง timestamp พร้อม microseconds
        $microtime = microtime(true);
        $timestamp = floor($microtime);
        $microseconds = substr(sprintf('%06d', ($microtime - $timestamp) * 1000000), 0, 3);

        // รวมปี + timestamp + microseconds
        $userId = $year . $timestamp . $microseconds;

        return $userId;
    }

    /**
     * สร้าง mp_id และตรวจสอบให้แน่ใจว่าไม่ซ้ำกันในฐานข้อมูล
     * @return string mp_id ที่ไม่ซ้ำกัน
     */
    private function generateUniqueUserId()
    {
        do {
            $userId = $this->generateUserId();

            // ตรวจสอบว่า mp_id นี้มีอยู่ในฐานข้อมูลหรือไม่
            $this->db->where('mp_id', $userId);
            $query = $this->db->get('tbl_member_public');

        } while ($query->num_rows() > 0); // วนลูปจนกว่าจะได้ ID ที่ไม่ซ้ำ

        return $userId;
    }

    /**
     * ตรวจสอบว่า mp_id มีอยู่ในฐานข้อมูลหรือไม่
     * @param string $mp_id
     * @return bool true = มีอยู่แล้ว, false = ไม่มี
     */
    private function checkMpIdExists($mp_id)
    {
        $this->db->where('mp_id', $mp_id);
        $query = $this->db->get('tbl_member_public');
        return ($query->num_rows() > 0);
    }

    // ==========================================
    // ADDITIONAL HELPER METHODS
    // ==========================================

    /**
     * ดึงสถิติสมาชิกแบบง่าย
     * @return array
     */
    public function get_member_stats()
    {
        $basic_stats = [
            'total_members' => $this->count_all_members(),
            'active_members' => $this->db->where('mp_status', 1)->count_all_results('tbl_member_public'),
            'inactive_members' => $this->db->where('mp_status', 0)->count_all_results('tbl_member_public'),
            'new_this_month' => $this->count_new_members_this_month(),
            '2fa_enabled' => $this->db->where('google2fa_enabled', 1)->count_all_results('tbl_member_public')
        ];

        // รวมสถิติเลขบัตรประชาชน
        $id_stats = $this->get_id_card_stats();

        return array_merge($basic_stats, $id_stats);
    }

    /**
     * ค้นหาสมาชิกตามเลขบัตรประชาชน
     * @param string $id_number
     * @return object|null
     */
    public function find_by_id_number($id_number)
    {
        try {
            if (empty($id_number)) {
                return null;
            }

            $this->db->where('mp_number', $id_number);
            $this->db->where('mp_number IS NOT NULL'); // เพิ่มความปลอดภัย
            $query = $this->db->get('tbl_member_public');

            if ($query->num_rows() > 0) {
                return $query->row();
            }

            return null;
        } catch (Exception $e) {
            log_message('error', 'Error in find_by_id_number: ' . $e->getMessage());
            return null;
        }
    }

    /**
     * ดึงสมาชิกที่ลงทะเบียนล่าสุด
     * @param int $limit
     * @return array
     */
    public function get_recent_members($limit = 10)
    {
        return $this->db->select('mp_id, mp_fname, mp_lname, mp_email, mp_registered_date')
            ->from('tbl_member_public')
            ->order_by('mp_registered_date', 'DESC')
            ->limit($limit)
            ->get()
            ->result();
    }





    public function delete_account_with_log($mp_id, $deletion_data = [])
    {
        try {
            // ดึงข้อมูลผู้ใช้ก่อนลบ
            $user_data = $this->get_member_by_id($mp_id);
            if (!$user_data) {
                log_message('error', 'User not found for deletion: ' . $mp_id);
                return false;
            }

            // เริ่ม transaction
            $this->db->trans_start();

            // 1. บันทึก deletion log ก่อน
            $log_data = array_merge([
                'deleted_mp_id' => $user_data->mp_id,
                'deleted_mp_email' => $user_data->mp_email,
                'deleted_mp_fname' => $user_data->mp_fname,
                'deleted_mp_lname' => $user_data->mp_lname,
                'deleted_mp_phone' => $user_data->mp_phone ?? null,
                'deleted_mp_number' => $user_data->mp_number ?? null,
                'deleted_at' => date('Y-m-d H:i:s')
            ], $deletion_data);

            $log_inserted = $this->db->insert('tbl_member_public_deletion_log', $log_data);

            if (!$log_inserted) {
                $this->db->trans_rollback();
                log_message('error', 'Failed to insert deletion log for user: ' . $mp_id);
                return false;
            }

            // 2. ลบข้อมูลหลัก
            $this->db->where('mp_id', $mp_id);
            $deleted = $this->db->delete('tbl_member_public');

            if (!$deleted) {
                $this->db->trans_rollback();
                log_message('error', 'Failed to delete user: ' . $mp_id);
                return false;
            }

            // Complete transaction
            $this->db->trans_complete();

            if ($this->db->trans_status() === FALSE) {
                log_message('error', 'Transaction failed for user deletion: ' . $mp_id);
                return false;
            }

            log_message('info', "User account deleted successfully: {$user_data->mp_email} (ID: $mp_id)");
            return true;

        } catch (Exception $e) {
            $this->db->trans_rollback();
            log_message('error', 'Error in delete_account_with_log: ' . $e->getMessage());
            return false;
        }
    }

    /**
     * ตรวจสอบว่าสามารถลบบัญชีได้หรือไม่
     * @param string|int $mp_id
     * @return array
     */
    public function can_delete_account($mp_id)
    {
        try {
            $user_data = $this->get_member_by_id($mp_id);

            if (!$user_data) {
                return [
                    'can_delete' => false,
                    'reason' => 'ไม่พบข้อมูลผู้ใช้'
                ];
            }

            // เช็คเงื่อนไขต่างๆ ที่อาจป้องกันการลบ
            $blocking_conditions = [];

            // ตัวอย่าง: เช็คว่ามีข้อมูลสำคัญที่เชื่อมโยงหรือไม่
            // (สามารถเพิ่มเงื่อนไขอื่นๆ ตามต้องการ)

            if (count($blocking_conditions) > 0) {
                return [
                    'can_delete' => false,
                    'reason' => 'มีข้อมูลที่เชื่อมโยงกับบัญชีนี้',
                    'details' => $blocking_conditions
                ];
            }

            return [
                'can_delete' => true,
                'user_data' => $user_data
            ];

        } catch (Exception $e) {
            log_message('error', 'Error in can_delete_account: ' . $e->getMessage());
            return [
                'can_delete' => false,
                'reason' => 'เกิดข้อผิดพลาดในการตรวจสอบ'
            ];
        }
    }

    /**
     * ดึงประวัติการลบบัญชี
     * @param int $limit
     * @param int $offset
     * @return array
     */
    public function get_deletion_history($limit = 20, $offset = 0)
    {
        try {
            if (!$this->db->table_exists('tbl_member_public_deletion_log')) {
                return [];
            }

            return $this->db->select('*')
                ->from('tbl_member_public_deletion_log')
                ->order_by('deleted_at', 'DESC')
                ->limit($limit, $offset)
                ->get()
                ->result();

        } catch (Exception $e) {
            log_message('error', 'Error in get_deletion_history: ' . $e->getMessage());
            return [];
        }
    }

    /**
     * นับจำนวนการลบบัญชีทั้งหมด
     * @return int
     */
    public function count_deletion_history()
    {
        try {
            if (!$this->db->table_exists('tbl_member_public_deletion_log')) {
                return 0;
            }

            return $this->db->count_all_results('tbl_member_public_deletion_log');

        } catch (Exception $e) {
            log_message('error', 'Error in count_deletion_history: ' . $e->getMessage());
            return 0;
        }
    }

    // ==========================================
// ENHANCED VALIDATION METHODS
// ==========================================

    /**
     * ตรวจสอบเลขบัตรประชาชนแบบละเอียด
     * @param string $id_number
     * @param string|int|null $exclude_mp_id
     * @return array
     */
    public function validate_id_number($id_number, $exclude_mp_id = null)
    {
        try {
            if (empty($id_number) || is_null($id_number)) {
                return [
                    'valid' => true,
                    'available' => true,
                    'message' => 'ไม่ได้กรอกเลขบัตรประชาชน'
                ];
            }

            // ตรวจสอบรูปแบบพื้นฐาน
            if (!preg_match('/^\d{13}$/', $id_number)) {
                return [
                    'valid' => false,
                    'available' => false,
                    'message' => 'เลขบัตรประชาชนต้องเป็นตัวเลข 13 หลัก'
                ];
            }

            // 🆕 ตรวจสอบ pattern ไทย
            if (!$this->validate_thai_id_checksum($id_number)) {
                return [
                    'valid' => false,
                    'available' => false,
                    'message' => 'รูปแบบเลขประจำตัวประชาชนไม่ถูกต้อง'
                ];
            }

            // ตรวจสอบความซ้ำ (ใช้ function เดิม)
            $exists = $this->check_id_card_exists($id_number, $exclude_mp_id);

            if ($exists) {
                return [
                    'valid' => true,
                    'available' => false,
                    'message' => 'เลขบัตรประชาชนนี้มีผู้ใช้งานแล้ว'
                ];
            }

            return [
                'valid' => true,
                'available' => true,
                'message' => 'เลขบัตรประชาชนสามารถใช้งานได้'
            ];

        } catch (Exception $e) {
            log_message('error', 'Error in validate_id_number: ' . $e->getMessage());
            return [
                'valid' => false,
                'available' => false,
                'message' => 'เกิดข้อผิดพลาดในการตรวจสอบ'
            ];
        }
    }


    private function validate_thai_id_checksum($id_number)
    {
        if (strlen($id_number) !== 13 || !ctype_digit($id_number)) {
            return false;
        }

        $sum = 0;
        for ($i = 0; $i < 12; $i++) {
            $sum += (int) $id_number[$i] * (13 - $i);
        }

        $remainder = $sum % 11;
        $checkDigit = ($remainder < 2) ? $remainder : 11 - $remainder;

        return $checkDigit === (int) $id_number[12];
    }

    /**
     * อัพเดทข้อมูลเลขบัตรประชาชนเท่านั้น
     * @param string|int $mp_id
     * @param string|null $id_number
     * @return bool
     */
    public function update_id_number_only($mp_id, $id_number)
    {
        try {
            // Validate ID number
            $validation = $this->validate_id_number($id_number, $mp_id);

            if (!$validation['valid'] || !$validation['available']) {
                log_message('error', 'ID number validation failed: ' . $validation['message']);
                return false;
            }

            $data = [
                'mp_number' => !empty($id_number) ? $id_number : null,
                'mp_updated_at' => date('Y-m-d H:i:s')
            ];

            $this->db->where('mp_id', $mp_id);
            $result = $this->db->update('tbl_member_public', $data);

            if ($result) {
                $id_info = $data['mp_number'] ? 'updated to: ' . $data['mp_number'] : 'removed';
                log_message('info', "ID number $id_info for user: $mp_id");
                return true;
            }

            return false;

        } catch (Exception $e) {
            log_message('error', 'Error in update_id_number_only: ' . $e->getMessage());
            return false;
        }
    }

    public function get_all_members_for_export($search = null)
    {
        $this->db->select('
        id,
        mp_id,
        mp_email,
        mp_prefix,
        mp_fname,
        mp_lname,
        mp_phone,
        mp_number,
        mp_address,
        mp_district,
        mp_amphoe,
        mp_province,
        mp_zipcode,
        mp_img,
        mp_by,
        mp_status,
        mp_registered_date,
        mp_updated_by,
        mp_updated_date,
        profile_completion
    ');
        $this->db->from('tbl_member_public');

        if ($search) {
            $this->db->group_start();
            $this->db->like('mp_fname', $search);
            $this->db->or_like('mp_lname', $search);
            $this->db->or_like('mp_email', $search);
            $this->db->or_like('mp_phone', $search);
            $this->db->or_like('mp_id', $search);
            $this->db->group_end();
        }

        $this->db->order_by('mp_id', 'desc');

        $query = $this->db->get();
        return $query->result();
    }



}