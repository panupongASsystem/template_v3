<?php

use SebastianBergmann\Environment\Console;

class Complain_model extends CI_Model
{
    private $channelAccessToken;
    private $lineApiUrl;

    public function __construct()
    {
        parent::__construct();

        // ใช้ helper function get_config_value เพื่อดึงค่า token จากฐานข้อมูล
        $this->channelAccessToken = get_config_value('line_token');
        $this->lineApiUrl = 'https://api.line.me/v2/bot/message/multicast';
    }
    public function add_new_id_entry()
    {
        try {
            // ดึงปี พ.ศ. ปัจจุบัน (เพิ่ม 543 จาก ค.ศ.)
            $current_year_thai = date('Y') + 543;

            // ตรวจสอบ ID ล่าสุดในตาราง
            $this->db->select('MAX(complain_id) AS max_id');
            $this->db->from('tbl_complain');
            $query = $this->db->get();
            $result = $query->row();

            // กำหนดค่าเริ่มต้นของ ID เช่น 6700001 หรือ 6800001 ตามปี
            $default_id = (int) ($current_year_thai % 100) . '00001'; // เปลี่ยนจาก 00000 เป็น 00001

            if ($result && $result->max_id) {
                $last_id = (int) $result->max_id;
                $last_year_prefix = (int) substr($last_id, 0, 2);
                $current_year_prefix = (int) ($current_year_thai % 100);

                if ($last_year_prefix === $current_year_prefix) {
                    $new_id = $last_id + 1;
                } else {
                    $new_id = $default_id;
                }
            } else {
                $new_id = $default_id;
            }

            return $this->generate_complain_id();

        } catch (Exception $e) {
            log_message('error', 'Error in add_new_id_entry: ' . $e->getMessage());
            throw new Exception('ไม่สามารถสร้างหมายเลขร้องเรียนได้');
        }
    }



    public function generate_complain_id()
    {
        // ดึงปี พ.ศ. ปัจจุบัน (เพิ่ม 543 จาก ค.ศ.)
        $current_year_thai = date('Y') + 543;
        $year_prefix = str_pad($current_year_thai % 100, 2, '0', STR_PAD_LEFT); // เช่น 68, 69

        $max_attempts = 100; // จำกัดจำนวนครั้งในการลองสร้าง ID ใหม่
        $attempt = 0;

        do {
            // สร้างเลข random 6 หลัก
            $random_number = str_pad(mt_rand(100000, 999999), 6, '0', STR_PAD_LEFT);

            // รวมเป็น complain_id แบบใหม่ เช่น 68123456
            $new_id = $year_prefix . $random_number;

            // ตรวจสอบว่า ID นี้มีในฐานข้อมูลแล้วหรือไม่
            $this->db->select('complain_id');
            $this->db->from('tbl_complain');
            $this->db->where('complain_id', $new_id);
            $query = $this->db->get();

            // ถ้าไม่มี ID นี้ในฐานข้อมูล ให้ใช้ ID นี้
            if ($query->num_rows() == 0) {
                log_message('info', "Generated new complain_id: {$new_id} (Year: {$current_year_thai}, Prefix: {$year_prefix}, Random: {$random_number})");
                return (int) $new_id;
            }

            $attempt++;
            log_message('warning', "complain_id {$new_id} already exists, attempting again... (Attempt: {$attempt})");

        } while ($attempt < $max_attempts);

        // ถ้าลองแล้ว 100 ครั้งยังไม่ได้ ให้ใช้ timestamp แทน (fallback)
        $fallback_id = $year_prefix . substr(time(), -6);
        log_message('error', "Failed to generate unique complain_id after {$max_attempts} attempts. Using fallback: {$fallback_id}");

        return (int) $fallback_id;
    }





    public function add_complain()
    {
        log_message('info', '=== MODEL ADD_COMPLAIN START ===');

        try {
            // *** 1. ตรวจสอบความพร้อมของระบบ ***
            if (!$this->db->table_exists('tbl_complain')) {
                throw new Exception('ตาราง tbl_complain ไม่พบในฐานข้อมูล');
            }

            if (!$this->db->table_exists('tbl_complain_category')) {
                throw new Exception('ตาราง tbl_complain_category ไม่พบในฐานข้อมูล');
            }

            $columns = $this->db->list_fields('tbl_complain');
            if (!in_array('complain_category_id', $columns)) {
                throw new Exception('Column complain_category_id ไม่พบในตาราง tbl_complain');
            }

            // *** 2. ตรวจสอบข้อมูลจำเป็น ***
            $category_id = $this->input->post('complain_category_id');
            $topic = $this->input->post('complain_topic');
            $detail = $this->input->post('complain_detail');

            if (empty($category_id) || !is_numeric($category_id)) {
                throw new Exception('ไม่มีข้อมูลหมวดหมู่เรื่องร้องเรียน');
            }

            if (empty($topic) || strlen(trim($topic)) < 3) {
                throw new Exception('หัวข้อเรื่องร้องเรียนต้องมีอย่างน้อย 3 ตัวอักษร');
            }

            if (empty($detail) || strlen(trim($detail)) < 5) {
                throw new Exception('รายละเอียดเรื่องร้องเรียนต้องมีอย่างน้อย 5 ตัวอักษร');
            }

            // *** 3. สร้าง ID ใหม่ ***
            $new_id = $this->generate_complain_id();
            if (!$new_id) {
                throw new Exception('ไม่สามารถสร้างหมายเลขร้องเรียนได้');
            }

            log_message('info', "Generated complain_id: {$new_id}");

            // *** 4. ตรวจสอบโหมดไม่ระบุตัวตน ***
            $is_anonymous = $this->input->post('is_anonymous') == '1' || $this->input->post('anonymous_mode') == 'true';
            log_message('info', 'Anonymous mode: ' . ($is_anonymous ? 'YES' : 'NO'));

            // *** 5. ดึงข้อมูล user จาก session ชั่วคราว ***
            $temp_user_info = $this->session->tempdata('temp_user_info');
            $temp_user_address = $this->session->tempdata('temp_user_address');
            $temp_user_type = $this->session->tempdata('temp_user_type');
            $temp_is_logged_in = $this->session->tempdata('temp_is_logged_in');

            log_message('info', 'Temp session data: ' . json_encode([
                'temp_user_type' => $temp_user_type,
                'temp_is_logged_in' => $temp_is_logged_in,
                'has_temp_user_info' => !empty($temp_user_info),
                'has_temp_user_address' => !empty($temp_user_address)
            ]));

            // *** 6. ดึงชื่อหมวดหมู่จากฐานข้อมูล ***
            $category_name = $this->get_category_name($category_id);

            // *** 7. จัดการข้อมูล user ตามโหมด ***
            if ($is_anonymous) {
                $user_data = ['user_id' => null, 'user_type' => 'anonymous'];
                $complain_by = 'ไม่ระบุตัวตน';
                $complain_phone = '0000000000';
                $complain_email = 'ไม่ระบุตัวตน';
                $complain_address = 'ไม่ระบุตัวตน';
                log_message('info', 'ANONYMOUS MODE: Using anonymous data');

            } elseif ($temp_is_logged_in && $temp_user_info) {
                // *** ใช้ข้อมูลจาก logged-in user ***
                $user_data = $this->extract_user_data_from_temp_info($temp_user_info, $temp_user_type);

                if ($temp_user_type === 'public') {
                    // สำหรับ public user
                    $complain_by = $this->get_user_display_name($temp_user_info);
                    $complain_phone = $this->get_user_phone($temp_user_info, $temp_user_address);
                    $complain_email = $temp_user_info['mp_email'] ?? 'ไม่ระบุ';
                    $complain_address = $this->get_user_address_text($temp_user_info, $temp_user_address);

                } elseif ($temp_user_type === 'staff') {
                    // สำหรับ staff user
                    $complain_by = trim(($temp_user_info['m_fname'] ?? '') . ' ' . ($temp_user_info['m_lname'] ?? ''));
                    $complain_phone = $temp_user_info['m_phone'] ?? '0000000000';
                    $complain_email = $temp_user_info['m_email'] ?? 'ไม่ระบุ';
                    $complain_address = 'ข้อมูลจากบัญชี';

                } else {
                    // fallback
                    $complain_by = $temp_user_info['name'] ?? 'ผู้ใช้ที่ล็อกอิน';
                    $complain_phone = $temp_user_info['phone'] ?? '0000000000';
                    $complain_email = $temp_user_info['email'] ?? 'ไม่ระบุ';
                    $complain_address = 'ข้อมูลจากบัญชี';
                }

                log_message('info', 'LOGGED IN MODE: Using user account data (Type: ' . $temp_user_type . ')');

            } else {
                // *** Guest user - ใช้ข้อมูลจากฟอร์ม ***
                $login_info = $this->get_user_login_info();

                if ($login_info['is_logged_in']) {
                    // มี user ล็อกอินอยู่แต่ไม่มี temp session
                    $user_data = $this->extract_user_data_from_login_info($login_info);
                    $complain_by = $login_info['user_info']['name'] ?? 'ผู้ใช้ที่ล็อกอิน';
                    $complain_phone = $login_info['user_info']['phone'] ?? '0000000000';
                    $complain_email = $login_info['user_info']['email'] ?? 'ไม่ระบุ';
                    $complain_address = 'ข้อมูลจากบัญชี';

                    log_message('info', 'LOGGED IN WITHOUT TEMP: Using login info');

                } else {
                    // ไม่มี user ล็อกอิน = guest user
                    $user_data = ['user_id' => null, 'user_type' => 'guest'];

                    // ตรวจสอบข้อมูลจากฟอร์ม
                    $complain_by = $this->input->post('complain_by');
                    $complain_phone = $this->input->post('complain_phone');
                    $complain_email = $this->input->post('complain_email');
                    $complain_address = $this->input->post('complain_address');

                    if (empty($complain_by) || strlen(trim($complain_by)) < 2) {
                        throw new Exception('กรุณากรอกชื่อ-นามสกุล');
                    }
                    if (empty($complain_phone) || strlen(trim($complain_phone)) < 9) {
                        throw new Exception('กรุณากรอกเบอร์โทรศัพท์');
                    }
                    if (empty($complain_address) || strlen(trim($complain_address)) < 5) {
                        throw new Exception('กรุณากรอกที่อยู่');
                    }

                    $complain_by = trim($complain_by);
                    $complain_phone = trim($complain_phone);
                    $complain_email = trim($complain_email) ?: 'ไม่ระบุ';
                    $complain_address = trim($complain_address);

                    log_message('info', 'GUEST MODE: Using form data');
                }
            }

            // *** 8. เตรียมข้อมูลสำหรับบันทึก ***
            $complain_data = array(
                'complain_id' => $new_id,
                'complain_category_id' => (int) $category_id,
                'complain_type' => $category_name,
                'complain_topic' => trim($topic),
                'complain_detail' => trim($detail),
                'complain_by' => $complain_by ?: 'ไม่ระบุชื่อ',
                'complain_phone' => $complain_phone ?: '0000000000',
                'complain_email' => $complain_email ?: 'ไม่ระบุ',
                'complain_address' => $complain_address ?: 'ไม่ระบุที่อยู่',
                'complain_user_id' => $user_data['user_id'],
                'complain_user_type' => $user_data['user_type'],
                'complain_status' => 'รอรับเรื่อง',
                'complain_datesave' => date('Y-m-d H:i:s')
            );

            // *** 9. จัดการข้อมูลที่อยู่แยกย่อย ***
            $this->populate_address_fields($complain_data, $user_data, $temp_user_address, $temp_user_info);

            // *** 10. Debug ข้อมูลก่อนบันทึก ***
            log_message('info', '🔍 Final complain data before insert:');
            log_message('info', 'ID: ' . $new_id);
            log_message('info', 'Category: ' . $category_id . ' -> ' . $category_name);
            log_message('info', 'User: ' . $user_data['user_type'] . ' (ID: ' . $user_data['user_id'] . ')');
            log_message('info', 'By: ' . $complain_by);
            log_message('info', 'Phone: ' . $complain_phone);
            log_message('info', 'Address: ' . $complain_address);

            // *** 11. เริ่ม Transaction ***
            $this->db->trans_start();

            // *** 12. บันทึกข้อมูลหลัก ***
            $insert_result = $this->db->insert('tbl_complain', $complain_data);

            if (!$insert_result) {
                $db_error = $this->db->error();
                log_message('error', 'Failed to insert complain data');
                log_message('error', 'Database error: ' . print_r($db_error, true));
                log_message('error', 'SQL: ' . $this->db->last_query());
                throw new Exception('Failed to insert complain data: ' . $db_error['message']);
            }

            log_message('info', '✅ Complain data inserted successfully');
            log_message('info', 'Last query: ' . $this->db->last_query());

            // *** 13. จัดการอัพโหลดรูปภาพ ***
            $this->handle_file_uploads($new_id);

            // *** 14. จบ Transaction ***
            $this->db->trans_complete();

            if ($this->db->trans_status() === FALSE) {
                log_message('error', '❌ Transaction failed');
                throw new Exception('Transaction failed');
            }

            log_message('info', '✅ Transaction completed successfully');

            // *** 15. ส่ง notification หลังบันทึกสำเร็จ ***
            $this->send_complain_notifications($new_id, $complain_data);

            // ส่ง LINE notification (เดิม)
            $this->send_line_notification($new_id);

            log_message('info', '=== COMPLAIN SAVE COMPLETED ===');

            return $new_id;

        } catch (Exception $e) {
            $this->db->trans_rollback();
            log_message('error', 'Exception in add_complain: ' . $e->getMessage());
            log_message('error', 'Exception trace: ' . $e->getTraceAsString());
            throw $e;
        }
    }



    private function get_user_display_name($user_info)
    {
        if (!empty($user_info['name'])) {
            return $user_info['name'];
        }

        $name_parts = [];
        if (!empty($user_info['mp_prefix']))
            $name_parts[] = $user_info['mp_prefix'];
        if (!empty($user_info['mp_fname']))
            $name_parts[] = $user_info['mp_fname'];
        if (!empty($user_info['mp_lname']))
            $name_parts[] = $user_info['mp_lname'];

        return implode(' ', $name_parts) ?: 'ผู้ใช้ที่ล็อกอิน';
    }




    private function populate_address_fields(&$complain_data, $user_data, $temp_user_address, $temp_user_info)
    {
        if ($user_data['user_type'] === 'guest' || $user_data['user_type'] === 'anonymous') {
            // สำหรับ guest และ anonymous ใช้ข้อมูลจากฟอร์ม
            if ($user_data['user_type'] === 'anonymous') {
                $complain_data['guest_district'] = 'ไม่ระบุตัวตน';
                $complain_data['guest_amphoe'] = 'ไม่ระบุตัวตน';
                $complain_data['guest_province'] = 'ไม่ระบุตัวตน';
                $complain_data['guest_zipcode'] = '00000';
            } else {
                $complain_data['guest_district'] = $this->input->post('guest_district') ?: 'ไม่ระบุ';
                $complain_data['guest_amphoe'] = $this->input->post('guest_amphoe') ?: 'ไม่ระบุ';
                $complain_data['guest_province'] = $this->input->post('guest_province') ?: 'ไม่ระบุ';
                $complain_data['guest_zipcode'] = $this->input->post('guest_zipcode') ?: '00000';
            }

        } elseif ($user_data['user_type'] === 'public' && $temp_user_address && $temp_user_address['source'] === 'detailed_columns') {
            // สำหรับ public user ใช้ข้อมูลจาก temp_user_address
            $complain_data['guest_district'] = $temp_user_address['district'] ?? 'ไม่ระบุ';
            $complain_data['guest_amphoe'] = $temp_user_address['amphoe'] ?? 'ไม่ระบุ';
            $complain_data['guest_province'] = $temp_user_address['province'] ?? 'ไม่ระบุ';
            $complain_data['guest_zipcode'] = $temp_user_address['zipcode'] ?? '00000';

        } elseif ($user_data['user_type'] === 'public' && $temp_user_info) {
            // สำหรับ public user ใช้ข้อมูลจาก member_public ถ้ามี
            $complain_data['guest_district'] = $temp_user_info['mp_district'] ?? 'ไม่ระบุ';
            $complain_data['guest_amphoe'] = $temp_user_info['mp_amphoe'] ?? 'ไม่ระบุ';
            $complain_data['guest_province'] = $temp_user_info['mp_province'] ?? 'ไม่ระบุ';
            $complain_data['guest_zipcode'] = $temp_user_info['mp_zipcode'] ?? '00000';

        } else {
            // default values
            $complain_data['guest_district'] = 'ไม่ระบุ';
            $complain_data['guest_amphoe'] = 'ไม่ระบุ';
            $complain_data['guest_province'] = 'ไม่ระบุ';
            $complain_data['guest_zipcode'] = '00000';
        }
    }




    private function handle_file_uploads($new_id)
    {
        log_message('info', '=== STARTING FILE UPLOAD PROCESS ===');
        log_message('info', 'Raw $_FILES: ' . print_r($_FILES, true));

        // ตรวจสอบว่ามีไฟล์หรือไม่
        $files_found = false;
        $files_to_process = array();

        // จัดการ complain_imgs[] ที่ส่งมาจาก JavaScript
        if (isset($_FILES['complain_imgs']) && is_array($_FILES['complain_imgs'])) {
            log_message('info', '📁 Processing complain_imgs array');

            if (is_array($_FILES['complain_imgs']['name'])) {
                // หลายไฟล์
                $file_count = count($_FILES['complain_imgs']['name']);

                for ($i = 0; $i < $file_count; $i++) {
                    if (!empty($_FILES['complain_imgs']['name'][$i]) && $_FILES['complain_imgs']['error'][$i] == UPLOAD_ERR_OK) {
                        $files_to_process[] = array(
                            'name' => $_FILES['complain_imgs']['name'][$i],
                            'type' => $_FILES['complain_imgs']['type'][$i],
                            'tmp_name' => $_FILES['complain_imgs']['tmp_name'][$i],
                            'error' => $_FILES['complain_imgs']['error'][$i],
                            'size' => $_FILES['complain_imgs']['size'][$i]
                        );
                        $files_found = true;
                    }
                }
            } elseif (!empty($_FILES['complain_imgs']['name']) && $_FILES['complain_imgs']['error'] == UPLOAD_ERR_OK) {
                // ไฟล์เดียว
                $files_to_process[] = $_FILES['complain_imgs'];
                $files_found = true;
            }
        }

        log_message('info', "📊 Files found: " . ($files_found ? 'YES' : 'NO'));
        log_message('info', "📊 Files to process: " . count($files_to_process));

        if ($files_found && !empty($files_to_process)) {
            // ตั้งค่าการอัพโหลด
            $upload_path = './docs/complain/';

            if (!is_dir($upload_path)) {
                if (!mkdir($upload_path, 0755, true)) {
                    log_message('error', '❌ Cannot create upload directory: ' . $upload_path);
                    throw new Exception('Cannot create upload directory');
                }
            }

            $uploaded_files = array();
            $upload_errors = array();

            foreach ($files_to_process as $index => $file) {
                try {
                    if ($file['error'] !== UPLOAD_ERR_OK) {
                        $upload_errors[] = "ไฟล์ {$file['name']}: Upload error code {$file['error']}";
                        continue;
                    }

                    // ตรวจสอบประเภทไฟล์
                    $allowed_types = array('image/jpeg', 'image/jpg', 'image/png', 'image/gif', 'image/webp');
                    if (!in_array(strtolower($file['type']), $allowed_types)) {
                        $upload_errors[] = "ไฟล์ {$file['name']}: ประเภทไฟล์ไม่ถูกต้อง";
                        continue;
                    }

                    // ตรวจสอบขนาดไฟล์
                    if ($file['size'] > 5242880) { // 5MB
                        $upload_errors[] = "ไฟล์ {$file['name']}: ขนาดใหญ่เกินไป";
                        continue;
                    }

                    // สร้าง unique filename
                    $file_ext = pathinfo($file['name'], PATHINFO_EXTENSION);
                    $new_filename = md5(time() . $file['name'] . rand()) . '.' . $file_ext;
                    $full_path = $upload_path . $new_filename;

                    // ย้าย uploaded file
                    if (move_uploaded_file($file['tmp_name'], $full_path)) {
                        log_message('info', "✅ File uploaded successfully: {$new_filename}");

                        $uploaded_files[] = array(
                            'complain_img_ref_id' => $new_id,
                            'complain_img_img' => $new_filename,
                            'complain_img_line' => $new_filename
                        );

                        // Copy ไปโฟลเดอร์ LINE
                        $line_path = './docs/img/';
                        if (!is_dir($line_path)) {
                            mkdir($line_path, 0755, true);
                        }
                        copy($full_path, $line_path . $new_filename);

                    } else {
                        $upload_errors[] = "ไฟล์ {$file['name']}: ไม่สามารถย้ายไฟล์ได้";
                    }

                } catch (Exception $e) {
                    log_message('error', "Error processing file {$file['name']}: " . $e->getMessage());
                    $upload_errors[] = "ไฟล์ {$file['name']}: " . $e->getMessage();
                }
            }

            // บันทึกข้อมูลรูปภาพลงฐานข้อมูล
            if (!empty($uploaded_files)) {
                $batch_result = $this->db->insert_batch('tbl_complain_img', $uploaded_files);
                if ($batch_result) {
                    log_message('info', '✅ Images inserted into database successfully');
                } else {
                    log_message('error', '❌ Failed to insert images into database');
                }
            }

            if (!empty($upload_errors)) {
                log_message('warning', 'Upload errors: ' . implode(', ', $upload_errors));
            }

        } else {
            log_message('info', '📭 No files to upload');
        }
    }



    private function get_user_phone($user_info, $user_address)
    {
        if ($user_address && !empty($user_address['phone'])) {
            return $user_address['phone'];
        }

        return $user_info['mp_phone'] ?? '0000000000';
    }

    private function get_user_address_text($user_info, $user_address)
    {
        if ($user_address && $user_address['source'] === 'detailed_columns') {
            return $user_address['additional_address'] ?? 'ข้อมูลจากบัญชี';
        }

        return $user_info['mp_address'] ?? 'ข้อมูลจากบัญชีสมาชิก';
    }




    private function get_category_name($category_id)
    {
        if (empty($category_id)) {
            return 'อื่นๆ';
        }

        try {
            $this->db->select('cat_name');
            $this->db->from('tbl_complain_category');
            $this->db->where('cat_id', $category_id);
            $this->db->where('cat_status', 1);
            $category = $this->db->get()->row();

            if ($category && !empty($category->cat_name)) {
                log_message('info', "Found category name: {$category->cat_name} for ID: {$category_id}");
                return $category->cat_name;
            } else {
                log_message('warning', "Category not found for ID: {$category_id}, using default");
                return 'อื่นๆ';
            }

        } catch (Exception $e) {
            log_message('error', 'Error in get_category_name: ' . $e->getMessage());
            return 'อื่นๆ';
        }
    }





    private function send_complain_notifications($complain_id, $complain_data)
    {
        try {
            // โหลด Notification library
            $this->load->library('notification_lib');

            $complain_topic = $complain_data['complain_topic'];
            $complain_by = $complain_data['complain_by'];
            $complain_user_id = $complain_data['complain_user_id'];
            $complain_user_type = $complain_data['complain_user_type'];

            log_message('info', "Sending complain notifications for ID: {$complain_id}");
            log_message('info', "User data - ID: {$complain_user_id}, Type: {$complain_user_type}");

            // ส่ง notification แบบใหม่ที่รองรับ target_user_id
            $notification_result = $this->notification_lib->new_complain(
                $complain_id,
                $complain_topic,
                $complain_by,
                $complain_user_id,
                $complain_user_type
            );

            if ($notification_result) {
                log_message('info', "Complain notifications sent successfully for ID: {$complain_id}");
            } else {
                log_message('warning', "Failed to send complain notifications for ID: {$complain_id}");
            }

        } catch (Exception $e) {
            log_message('error', 'Failed to send complain notifications: ' . $e->getMessage());
            // ไม่ throw exception เพื่อไม่ให้ส่งผลต่อการบันทึกข้อมูลหลัก
        }
    }







    private function get_upload_error_message($error_code)
    {
        switch ($error_code) {
            case UPLOAD_ERR_INI_SIZE:
            case UPLOAD_ERR_FORM_SIZE:
                return 'ไฟล์ใหญ่เกินไป';
            case UPLOAD_ERR_PARTIAL:
                return 'อัปโหลดไม่สมบูรณ์';
            case UPLOAD_ERR_NO_FILE:
                return 'ไม่มีไฟล์';
            case UPLOAD_ERR_NO_TMP_DIR:
                return 'ไม่มีโฟลเดอร์ temp';
            case UPLOAD_ERR_CANT_WRITE:
                return 'ไม่สามารถเขียนไฟล์ได้';
            case UPLOAD_ERR_EXTENSION:
                return 'ส่วนขยายไฟล์ถูกบล็อก';
            default:
                return 'ข้อผิดพลาดไม่ทราบสาเหตุ (Code: ' . $error_code . ')';
        }
    }




    private function extract_user_data_from_temp_info($temp_user_info, $temp_user_type)
    {
        if (!$temp_user_info || !$temp_user_type) {
            return ['user_id' => null, 'user_type' => 'guest'];
        }

        if ($temp_user_type === 'public') {
            $user_id = $temp_user_info['id'] ?? null;
            return [
                'user_id' => $user_id,
                'user_type' => 'public'
            ];
        } elseif ($temp_user_type === 'staff') {
            return [
                'user_id' => $temp_user_info['m_id'] ?? null,
                'user_type' => 'staff'
            ];
        }

        return ['user_id' => null, 'user_type' => 'guest'];
    }






    public function get_complain_id_stats()
    {
        $this->db->select('
            SUBSTRING(complain_id, 1, 2) as year_prefix,
            COUNT(*) as count,
            MIN(complain_id) as min_id,
            MAX(complain_id) as max_id
        ');
        $this->db->from('tbl_complain');
        $this->db->where('LENGTH(complain_id)', 8); // เฉพาะ ID รูปแบบใหม่
        $this->db->group_by('SUBSTRING(complain_id, 1, 2)');
        $this->db->order_by('year_prefix', 'DESC');

        return $this->db->get()->result_array();
    }

    public function validate_complain_id($complain_id)
    {
        // ตรวจสอบว่าเป็นตัวเลข 8 หลัก
        if (!is_numeric($complain_id) || strlen($complain_id) != 8) {
            return false;
        }

        // ดึงปี พ.ศ. ปัจจุบัน
        $current_year_thai = date('Y') + 543;
        $current_year_prefix = str_pad($current_year_thai % 100, 2, '0', STR_PAD_LEFT);

        // ตรวจสอบว่า 2 หลักแรกเป็นปีไทยหรือไม่
        $id_year_prefix = substr($complain_id, 0, 2);

        // อนุญาตให้เป็นปีปัจจุบัน หรือปีที่ผ่านมา 5 ปี
        $allowed_years = array();
        for ($i = 0; $i < 6; $i++) {
            $year = ($current_year_thai - $i) % 100;
            $allowed_years[] = str_pad($year, 2, '0', STR_PAD_LEFT);
        }

        return in_array($id_year_prefix, $allowed_years);
    }




    private function get_user_login_info()
    {
        $login_info = [
            'is_logged_in' => false,
            'user_info' => null,
            'user_type' => 'guest'
        ];

        try {
            // ตรวจสอบ Public User (tbl_member_public) - ใช้ ID ที่แท้จริง
            $mp_email = $this->session->userdata('mp_email');
            $session_mp_id = $this->session->userdata('mp_id');

            if ($session_mp_id && $mp_email) {
                log_message('info', '🔍 Found public user session: ' . $session_mp_id . ', Email: ' . $mp_email);

                // *** เปลี่ยนแปลงสำคัญ: ดึงข้อมูลจาก tbl_member_public โดยใช้ email ก่อน ***
                $this->db->select('id, mp_id, mp_email, mp_prefix, mp_fname, mp_lname, mp_phone, mp_address');
                $this->db->from('tbl_member_public');
                $this->db->where('mp_email', $mp_email);
                $this->db->where('mp_status', 1);
                $public_user = $this->db->get()->row_array();

                // ถ้าไม่เจอด้วย email ลองค้นหาด้วย mp_id (กรณีไม่ overflow)
                if (!$public_user && $session_mp_id != 2147483647 && $session_mp_id != '2147483647') {
                    $this->db->select('id, mp_id, mp_email, mp_prefix, mp_fname, mp_lname, mp_phone, mp_address');
                    $this->db->from('tbl_member_public');
                    $this->db->where('mp_id', $session_mp_id);
                    $this->db->where('mp_status', 1);
                    $public_user = $this->db->get()->row_array();
                }

                if ($public_user) {
                    $login_info = [
                        'is_logged_in' => true,
                        'user_info' => [
                            'id' => $public_user['id'],           // *** ใช้ auto increment ID ***
                            'mp_id' => $public_user['mp_id'],     // เก็บไว้สำหรับ reference
                            'email' => $public_user['mp_email'],
                            'name' => trim($public_user['mp_prefix'] . ' ' . $public_user['mp_fname'] . ' ' . $public_user['mp_lname']),
                            'prefix' => $public_user['mp_prefix'],
                            'fname' => $public_user['mp_fname'],
                            'lname' => $public_user['mp_lname'],
                            'phone' => $public_user['mp_phone'],
                            'address' => $public_user['mp_address']
                        ],
                        'user_type' => 'public'
                    ];

                    log_message('info', '✅ Public user info loaded: ' . $public_user['mp_email'] . ' -> ID: ' . $public_user['id']);
                } else {
                    log_message('warning', '⚠️ Public user not found in database: Session MP_ID: ' . $session_mp_id . ', Email: ' . $mp_email);
                }
            }

            // ตรวจสอบ Staff User (tbl_member) หากไม่ใช่ public (ไม่เปลี่ยนแปลง)
            if (!$login_info['is_logged_in']) {
                $m_id = $this->session->userdata('m_id');

                if ($m_id) {
                    // ... รหัสเดิมสำหรับ staff user ...
                }
            }

        } catch (Exception $e) {
            log_message('error', '❌ Error in get_user_login_info: ' . $e->getMessage());
        }

        return $login_info;
    }


    private function extract_user_data_from_login_info($login_info)
    {
        if (!$login_info['is_logged_in']) {
            return ['user_id' => null, 'user_type' => 'guest'];
        }

        if ($login_info['user_type'] === 'public') {
            // *** ใช้ ID จาก tbl_member_public.id แทน mp_id ***
            $user_id = $login_info['user_info']['id'];  // เปลี่ยนจาก mp_id เป็น id

            return [
                'user_id' => $user_id,
                'user_type' => 'public'
            ];
        } elseif ($login_info['user_type'] === 'staff') {
            // Staff ใช้ m_id เหมือนเดิม
            return [
                'user_id' => $login_info['user_info']['m_id'],
                'user_type' => 'staff'
            ];
        }

        return ['user_id' => null, 'user_type' => 'guest'];
    }



    /**
     * ส่งแจ้งเตือน LINE สำหรับเรื่องร้องเรียนใหม่
     */
    private function send_line_notification($complain_id)
    {
        try {
            $complainData = $this->db->get_where('tbl_complain', array('complain_id' => $complain_id))->row();

            if ($complainData) {
                $message = "เรื่องร้องเรียน ใหม่ !\n";
                $message .= "หมายเลข: " . $complainData->complain_id . "\n";
                $message .= "หมวดหมู่: " . $complainData->complain_type . "\n";
                $message .= "สถานะ: " . ($complainData->complain_status ?: 'รอรับเรื่อง') . "\n";
                $message .= "เรื่อง: " . $complainData->complain_topic . "\n";
                $message .= "รายละเอียด: " . $complainData->complain_detail . "\n";
                $message .= "ผู้แจ้งเรื่อง: " . $complainData->complain_by . "\n";
                $message .= "เบอร์โทรศัพท์ผู้แจ้ง: " . $complainData->complain_phone . "\n";
            	$message .= "ที่อยู่: " . $complainData->complain_address . " " . $complainData->guest_district . $complainData->guest_amphoe . " " . $complainData->guest_province . " " . $complainData->guest_zipcode . "\n";

                // ปรับปรุง: แสดงข้อมูลที่อยู่แบบครบถ้วน
                $message .= $this->build_full_address($complainData);

                $message .= "อีเมล: " . ($complainData->complain_email ?: 'ไม่ระบุ') . "\n";

                // ปรับปรุง: แสดงประเภทผู้ใช้ให้เข้าใจง่าย
                $message .= $this->build_user_type_for_line_notification($complainData);

                $images = $this->db->get_where(
                    'tbl_complain_img',
                    array('complain_img_ref_id' => $complain_id)
                )->result();

                if ($images) {
                    $imagePaths = [];
                    foreach ($images as $image) {
                        if (!empty($image->complain_img_line)) {
                            $imagePaths[] = './docs/img/' . $image->complain_img_line;
                        }
                    }

                    if (!empty($imagePaths)) {
                        $this->broadcastLineOAMessage($message, $imagePaths);
                    } else {
                        $this->broadcastLineOAMessage($message);
                    }
                } else {
                    $this->broadcastLineOAMessage($message);
                }

                log_message('info', 'Line notification sent for complain_id: ' . $complain_id);
            }
        } catch (Exception $e) {
            log_message('error', 'Failed to send Line notification: ' . $e->getMessage());
        }
    }

    /**
     * ส่งแจ้งเตือน LINE สำหรับการอัปเดตเรื่องร้องเรียน
     */
    private function send_line_update_notification($complain_id)
    {
        try {
            // ดึงข้อมูลจาก tbl_complain หลังจากอัปเดต
            $complainData = $this->db->get_where('tbl_complain', array('complain_id' => $complain_id))->row();

            if ($complainData) {
                $message = "เรื่องร้องเรียน อัพเดต !\n";
                $message .= "หมายเลข: " . $complainData->complain_id . "\n";
                $message .= "สถานะ: " . $complainData->complain_status . "\n";
                $message .= "หมวดหมู่: " . $complainData->complain_type . "\n";
                $message .= "เรื่อง: " . $complainData->complain_topic . "\n";
                $message .= "รายละเอียด: " . $complainData->complain_detail . "\n";
                $message .= "ผู้แจ้งเรื่อง: " . $complainData->complain_by . "\n";
                $message .= "เบอร์โทรศัพท์ผู้แจ้ง: " . $complainData->complain_phone . "\n";

                // ปรับปรุง: แสดงข้อมูลที่อยู่แบบครบถ้วน
                $message .= $this->build_full_address($complainData);

                $message .= "อีเมล: " . ($complainData->complain_email ?: 'ไม่ระบุ') . "\n";

                // ปรับปรุง: แสดงประเภทผู้ใช้ให้เข้าใจง่าย
                $message .= $this->build_user_type_for_line_notification($complainData);
            }

            // ดึงข้อมูลจาก tbl_complain_detail หลังจากอัปเดต
            $this->db->order_by('complain_detail_id', 'DESC');
            $this->db->limit(1);
            $complainData2 = $this->db->get_where('tbl_complain_detail', array('complain_detail_case_id' => $complain_id))->row();

            if ($complainData2) {
                $message .= "ชื่อผู้อัพเดตข้อมูล: " . $complainData2->complain_detail_by . "\n";
                $message .= "ข้อความจากการอัพเดต: " . $complainData2->complain_detail_com . "\n";
                $message .= "วันที่อัพเดต: " . date('d/m/Y H:i', strtotime($complainData2->complain_detail_datesave)) . "\n";
            }

            // ส่งข้อความแบบ broadcast
            $this->broadcastLineOAMessage($message);

            log_message('info', 'LINE update notification sent for complain_id: ' . $complain_id);
        } catch (Exception $e) {
            log_message('error', 'Failed to send LINE update notification: ' . $e->getMessage());
        }
    }

    /**
     * สร้างข้อความที่อยู่สำหรับการแจ้งเตือน LINE
     */
    private function build_full_address($complainData)
    {
        $address_message = "";

        // ข้อมูลที่อยู่หลัก
        if (
            !empty($complainData->complain_address) &&
            $complainData->complain_address !== 'ข้อมูลจากบัญชี' &&
            $complainData->complain_address !== 'ไม่ระบุตัวตน'
        ) {
            $address_message .= "ที่อยู่: " . $complainData->complain_address . "\n";
        }

        // ข้อมูลที่อยู่แยกย่อย
        $detailed_address_parts = [];

        if (!empty($complainData->guest_district) && $complainData->guest_district !== 'ไม่ระบุ') {
            $detailed_address_parts[] = "ต." . $complainData->guest_district;
        }

        if (!empty($complainData->guest_amphoe) && $complainData->guest_amphoe !== 'ไม่ระบุ') {
            $detailed_address_parts[] = "อ." . $complainData->guest_amphoe;
        }

        if (!empty($complainData->guest_province) && $complainData->guest_province !== 'ไม่ระบุ') {
            $detailed_address_parts[] = "จ." . $complainData->guest_province;
        }

        if (!empty($complainData->guest_zipcode) && $complainData->guest_zipcode !== '00000') {
            $detailed_address_parts[] = $complainData->guest_zipcode;
        }

        // แสดงที่อยู่แยกย่อย
        if (!empty($detailed_address_parts)) {
            $address_message .= "พื้นที่: " . implode(' ', $detailed_address_parts) . "\n";
        }

        // กรณีไม่มีข้อมูลที่อยู่เลย
        if (empty($address_message)) {
            switch ($complainData->complain_user_type) {
                case 'staff':
                    $address_message .= "ที่อยู่: ข้อมูลจากระบบเจ้าหน้าที่\n";
                    break;
                case 'anonymous':
                    $address_message .= "ที่อยู่: ไม่ระบุตัวตน\n";
                    break;
                default:
                    $address_message .= "ที่อยู่: ไม่ได้ระบุ\n";
            }
        }

        return $address_message;
    }

    /**
     * สร้างข้อความประเภทผู้ใช้สำหรับการแจ้งเตือน LINE
     */
    private function build_user_type_for_line_notification($complainData)
    {
        $user_type_message = "";

        switch ($complainData->complain_user_type) {
            case 'public':
                $user_type_message = "ประเภทผู้ใช้: สมาชิกสาธารณะ\n";
                break;
            case 'staff':
                $user_type_message = "ประเภทผู้ใช้: เจ้าหน้าที่\n";
                break;
            case 'guest':
                $user_type_message = "ประเภทผู้ใช้: ผู้เยี่ยมชม\n";
                break;
            case 'anonymous':
                $user_type_message = "ประเภทผู้ใช้: ไม่ระบุตัวตน\n";
                $user_type_message .= "หมายเหตุ: แจ้งแบบไม่ระบุตัวตน\n";
                break;
            default:
                $user_type_message = "ประเภทผู้ใช้: ไม่ทราบประเภท\n";
        }

        return $user_type_message;
    }

    /**

     */
    public function get_anonymous_complains($limit = 10)
    {
        $this->db->select('*');
        $this->db->from('tbl_complain');
        $this->db->where('complain_user_type', 'anonymous');
        $this->db->order_by('complain_datesave', 'DESC');

        if ($limit > 0) {
            $this->db->limit($limit);
        }

        return $this->db->get()->result();
    }

    /**

     */
    public function get_complain_stats_by_user_type()
    {
        $this->db->select('complain_user_type, COUNT(*) as count');
        $this->db->from('tbl_complain');
        $this->db->group_by('complain_user_type');
        $this->db->order_by('count', 'DESC');

        return $this->db->get()->result_array();
    }

    /**

     */
    public function get_anonymous_complain_stats_by_location()
    {
        $this->db->select('guest_province, guest_amphoe, COUNT(*) as count');
        $this->db->from('tbl_complain');
        $this->db->where('complain_user_type', 'anonymous');
        $this->db->where('guest_province IS NOT NULL');
        $this->db->where('guest_province !=', 'ไม่ระบุตัวตน');
        $this->db->group_by(['guest_province', 'guest_amphoe']);
        $this->db->order_by('count', 'DESC');

        return $this->db->get()->result_array();
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
            return false;
        }

        $to = array_filter($to);
        if (empty($to)) {
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

        if (is_array($imagePaths)) {
            $imagePaths = array_slice($imagePaths, 0, 5);

            foreach ($imagePaths as $path) {
                $imageUrl = $this->uploadImageToLine($path);
                if ($imageUrl) {
                    $messages[] = [
                        'type' => 'image',
                        'originalContentUrl' => $imageUrl,
                        'previewImageUrl' => $imageUrl
                    ];
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
            }
        }

        $chunks = array_chunk($to, 500);
        $success = true;

        foreach ($chunks as $receivers) {
            $data = [
                'to' => $receivers,
                'messages' => $messages
            ];

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
                log_message('error', 'Line API Error: ' . $response);
            }

            curl_close($ch);
        }

        return $success;
    }



    private function uploadImageToLine($imagePath)
    {
        $fileName = basename($imagePath);
        return base_url('docs/img/' . $fileName);
    }

    public function add_complain_detail($complain_id)
    {
        $data = array(
            'complain_detail_case_id' => $complain_id,
            'complain_detail_by' => $this->input->post('complain_by'),
            // Add other fields as needed
        );

        $query = $this->db->insert('tbl_complain_detail', $data);

        $this->space_model->update_server_current();

        if ($query) {
            $this->session->set_flashdata('save_success', TRUE);
        } else {
            echo "<script>";
            echo "alert('เกิดข้อผิดพลาดในการเพิ่มข้อมูลใหม่ !');";
            echo "</script>";
        }
    }

    public function get_complains($complain_status = null)
    {
        $this->db->select('*');
        $this->db->from('tbl_complain');

        // ตรวจสอบและเพิ่มเงื่อนไขสถานะคำร้องเรียน ถ้ามี
        if ($complain_status) {
            $this->db->where('complain_status', $complain_status);
        }

        // เรียงลำดับตาม complain_datesave จากใหม่ไปเก่า (DESC)
        $this->db->order_by('complain_datesave', 'DESC');

        // ดึงข้อมูลและส่งกลับ
        return $this->db->get()->result();
    }

    public function get_images_for_complain($complain_id)
    {
        $this->db->select('complain_img_img');
        $this->db->from('tbl_complain_img');
        $this->db->where('complain_img_ref_id', $complain_id);
        return $this->db->get()->result();
    }

    //show form edit
    public function read($complain_id)
    {
        $this->db->where('complain_id', $complain_id);
        $query = $this->db->get('tbl_complain');
        if ($query->num_rows() > 0) {
            $data = $query->row();
            return $data;
        }
        return FALSE;
    }

    public function read_detail($complain_id)
    {
        $this->db->where('complain_detail_case_id', $complain_id);
        $this->db->order_by('complain_detail_id', 'asc');
        $query = $this->db->get('tbl_complain_detail');
        return $query->result();
    }

    public function updateComplainStatus($complainId, $complainStatus)
    {
        $data = array(
            'complain_status' => $complainStatus
        );

        $this->db->where('complain_id', $complainId);
        $result = $this->db->update('tbl_complain', $data);

        return $result;
    }

    public function dashboard_Complain()
    {
        $this->db->select('*');
        $this->db->from('tbl_complain as c');
        $this->db->limit(3);
        $this->db->order_by('c.complain_id', 'DESC');
        $query = $this->db->get();
        return $query->result();
    }

    public function intranet_complain()
    {
        $this->db->select('*');
        $this->db->from('tbl_complain as c');
        $this->db->limit(15);
        $this->db->order_by('c.complain_id', 'DESC');
        $query = $this->db->get();
        return $query->result();
    }








    private function send_update_notifications($complain_data, $new_status)
    {
        try {
            // โหลด Notification library
            $this->load->library('notification_lib');

            $staff_name = $this->session->userdata('m_fname') . ' ' . $this->session->userdata('m_lname');
            $complain_user_id = $complain_data->complain_user_id;
            $complain_user_type = $complain_data->complain_user_type;

            log_message('info', "Sending update notifications for complain {$complain_data->complain_id}");
            log_message('info', "User data - ID: {$complain_user_id}, Type: {$complain_user_type}");

            // ส่ง notification แบบใหม่ที่รองรับ target_user_id
            $notification_result = $this->notification_lib->complain_status_updated(
                $complain_data->complain_id,
                $new_status,
                $staff_name,
                $complain_user_id,
                $complain_user_type
            );

            if ($notification_result) {
                log_message('info', "Update notifications sent successfully for complain {$complain_data->complain_id}");
            } else {
                log_message('warning', "Failed to send update notifications for complain {$complain_data->complain_id}");
            }

        } catch (Exception $e) {
            log_message('error', 'Failed to send update notifications: ' . $e->getMessage());
        }
    }




    public function updateComplain($complain_detail_case_id, $complain_detail_status, $complain_detail_com)
    {
        // *** เพิ่ม: ดึงข้อมูล complain ก่อนอัปเดต ***
        $complain_data = $this->db->get_where('tbl_complain', array('complain_id' => $complain_detail_case_id))->row();

        if (!$complain_data) {
            log_message('error', 'Complain not found for update: ' . $complain_detail_case_id);
            return false;
        }

        // อัปเดต tbl_complain
        $this->db->set('complain_status', $complain_detail_status);
        $this->db->where('complain_id', $complain_detail_case_id);
        $this->db->update('tbl_complain');

        // เพิ่มข้อมูลใหม่ลงใน tbl_complain_detail
        $data = array(
            'complain_detail_case_id' => $complain_detail_case_id,
            'complain_detail_status' => $complain_detail_status,
            'complain_detail_by' => $this->session->userdata('m_fname'),
            'complain_detail_com' => $complain_detail_com
        );
        $this->db->insert('tbl_complain_detail', $data);

        // *** เพิ่ม: ส่ง notification แบบเจาะจง ***
        $this->send_update_notifications($complain_data, $complain_detail_status);

        // ส่ง LINE notification (เดิม)
        $this->send_line_update_notification($complain_detail_case_id);
    }





    public function statusCancel($complain_detail_case_id, $complain_detail_status, $complain_detail_com)
    {
        // อัปเดต tbl_complain
        $this->db->set('complain_status', 'ยกเลิก');
        $this->db->where('complain_id', $complain_detail_case_id);
        $this->db->update('tbl_complain');

        // ดึงข้อมูลจาก tbl_complain หลังจากอัปเดต
        $complainData = $this->db->get_where('tbl_complain', array('complain_id' => $complain_detail_case_id))->row();

        if ($complainData) {
            $message = "เรื่องร้องเรียน !" . "\n";
            $message .= "case: " . $complainData->complain_id . "\n";
            $message .= "สถานะ: " . $complainData->complain_status . "\n";
            $message .= "เรื่อง: " . $complainData->complain_topic . "\n";
            $message .= "รายละเอียด: " . $complainData->complain_detail . "\n";
            $message .= "ผู้แจ้งเรื่อง: " . $complainData->complain_by . "\n";
            $message .= "เบอร์โทรศัพท์ผู้แจ้ง: " . $complainData->complain_phone . "\n";
            $message .= "ที่อยู่: " . $complainData->complain_address . "\n";
            $message .= "อีเมล: " . $complainData->complain_email . "\n";
            // เพิ่มข้อมูลอื่น ๆ ตามที่คุณต้องการ
        }

        // ส่งข้อมูลไปที่ LINE Notify
        $this->broadcastLineOAMessage($message);

        // เพิ่มข้อมูลใหม่ลงใน tbl_complain_detail
        $data = array(
            'complain_detail_case_id' => $complain_detail_case_id,
            'complain_detail_status' => 'ยกเลิก',
            'complain_detail_com' => $complain_detail_com, // เพิ่มฟิลด์นี้
            'complain_detail_by' => $this->session->userdata('m_fname')
        );
        $this->db->insert('tbl_complain_detail', $data);
    }

    public function getLatestDetail($complain_id)
    {
        $this->db->select('*');
        $this->db->from('tbl_complain_detail');
        $this->db->where('complain_detail_case_id', $complain_id);
        $this->db->order_by('complain_detail_id', 'DESC');
        $this->db->limit(1); // จำกัดให้เรียกข้อมูลอันล่าสุดเท่านั้น

        $query = $this->db->get();

        if ($query->num_rows() > 0) {
            return $query->row();
        }

        return null;
    }

    //  *********************************************************************

    public function count_complain_year()
    {
        $this->db->select('COUNT(complain_id) AS total_complain_year');
        $this->db->from('tbl_complain');
        $this->db->where('YEAR(complain_datesave)', date('Y'));
        $query = $this->db->get();
        return $query->row()->total_complain_year;
    }
    public function count_complain_success()
    {
        $this->db->select('COUNT(complain_id) AS total_complain_success');
        $this->db->from('tbl_complain');
        $this->db->where('tbl_complain.complain_status', 'ดำเนินการเรียบร้อย');
        $query = $this->db->get();
        return $query->row()->total_complain_success;
    }
    public function count_complain_operation()
    {
        $this->db->select('COUNT(complain_id) AS total_complain_operation');
        $this->db->from('tbl_complain');
        $this->db->where('tbl_complain.complain_status', 'รอดำเนินการ');
        $query = $this->db->get();
        return $query->row()->total_complain_operation;
    }
    public function count_complain_accept()
    {
        $this->db->select('COUNT(complain_id) AS total_complain_accept');
        $this->db->from('tbl_complain');
        $this->db->where('tbl_complain.complain_status', 'รับเรื่องแล้ว');
        $query = $this->db->get();
        return $query->row()->total_complain_accept;
    }
    public function count_complain_doing()
    {
        $this->db->select('COUNT(complain_id) AS total_complain_doing');
        $this->db->from('tbl_complain');
        $this->db->where('tbl_complain.complain_status', 'กำลังดำเนินการ');
        $query = $this->db->get();
        return $query->row()->total_complain_doing;
    }

    public function count_complain_wait()
    {
        $this->db->select('COUNT(complain_id) AS total_complain_wait');
        $this->db->from('tbl_complain');
        $this->db->where('tbl_complain.complain_status', 'รอรับเรื่อง');
        $query = $this->db->get();
        return $query->row()->total_complain_wait;
    }

    public function count_complain_cancel()
    {
        $this->db->select('COUNT(complain_id) AS total_complain_cancel');
        $this->db->from('tbl_complain');
        $this->db->where('tbl_complain.complain_status', 'ยกเลิก');
        $query = $this->db->get();
        return $query->row()->total_complain_cancel;
    }

    public function get_complain_topic($complain_id)
    {
        $this->db->select('complain_topic');
        $this->db->from('tbl_complain');
        $this->db->where('complain_id', $complain_id);
        $query = $this->db->get();
        if ($query->num_rows() > 0) {
            return $query->row()->complain_topic;
        }
        return null;
    }

    public function get_complain_data($year = null)
    {
        if ($year) {
            $this->db->where('YEAR(complain_datesave)', $year);
        }
        $this->db->select('complain_id as id, complain_status as status, complain_type as type, complain_topic as topic, complain_detail as detail, complain_by as created_by, complain_phone as phone, complain_email as email, complain_address as address, complain_datesave as datesave, "complain" as table');
        $query = $this->db->get('tbl_complain');
        return $query->result_array();
    }

    public function get_corruption_data($year = null)
    {
        if ($year) {
            $this->db->where('YEAR(corruption_datesave)', $year);
        }
        $this->db->select('corruption_id as id, corruption_topic as topic, corruption_detail as detail, corruption_by as created_by, corruption_phone as phone, corruption_email as email, corruption_address as address, corruption_datesave as datesave, "corruption" as table');
        $query = $this->db->get('tbl_corruption');
        return $query->result_array();
    }

    public function get_kid_aw_ods_data($year = null)
    {
        if ($year) {
            $this->db->where('YEAR(kid_aw_ods_datesave)', $year);
        }
        $this->db->select('kid_aw_ods_id as id, kid_aw_ods_by as created_by, kid_aw_ods_phone as phone, kid_aw_ods_number as number, kid_aw_ods_address as address, kid_aw_ods_datesave as datesave, "kid_aw_ods" as table');
        $query = $this->db->get('tbl_kid_aw_ods');
        return $query->result_array();
    }

    public function get_suggestions_data($year = null)
    {
        if ($year) {
            $this->db->where('YEAR(suggestions_datesave)', $year);
        }
        $this->db->select('suggestions_id as id, suggestions_topic as topic, suggestions_detail as detail, suggestions_by as created_by, suggestions_phone as phone, suggestions_email as email, suggestions_address as address, suggestions_datesave as datesave, "suggestions" as table');
        $query = $this->db->get('tbl_suggestions');
        return $query->result_array();
    }

    public function get_elderly_aw_ods_data($year = null)
    {
        if ($year) {
            $this->db->where('YEAR(elderly_aw_ods_datesave)', $year);
        }
        $this->db->select('elderly_aw_ods_id as id, elderly_aw_ods_by as created_by, elderly_aw_ods_phone as phone, elderly_aw_ods_number as number, elderly_aw_ods_address as address, elderly_aw_ods_datesave as datesave, "elderly_aw_ods" as table');
        $query = $this->db->get('tbl_elderly_aw_ods');
        return $query->result_array();
    }

    public function get_esv_ods_data($year = null)
    {
        if ($year) {
            $this->db->where('YEAR(esv_ods_datesave)', $year);
        }
        $this->db->select('esv_ods_id as id, esv_ods_topic as topic, esv_ods_detail as detail, esv_ods_by as created_by, esv_ods_phone as phone, esv_ods_email as email, esv_ods_address as address, esv_ods_datesave as datesave, "esv_ods" as table');
        $query = $this->db->get('tbl_esv_ods');
        return $query->result_array();
    }
    public function get_queue_data($year = null)
    {
        if ($year) {
            $this->db->where('YEAR(queue_datesave)', $year);
        }
        $this->db->select('queue_id as id, queue_status as status, queue_topic as topic, queue_detail as detail, queue_by as created_by, queue_phone as phone, queue_number as number, queue_date as booking_date, queue_datesave as datesave, "queue" as table');
        $query = $this->db->get('tbl_queue');
        return $query->result_array();
    }
    public function get_q_a_data($year = null)
    {
        if ($year) {
            $this->db->where('YEAR(q_a_datesave)', $year);
        }
        $this->db->select('q_a_id as id, q_a_msg as topic, q_a_detail as detail, q_a_by as created_by, q_a_email as email, q_a_datesave as datesave, "q_a" as table');
        $query = $this->db->get('tbl_q_a');
        return $query->result_array();
    }
    public function get_assessment_data($year = null)
    {
        if ($year) {
            $this->db->where('YEAR(assessment_datesave)', $year);
        }
        $this->db->select('assessment_id as id, assessment_gender as gender, assessment_age as age, assessment_study as education, assessment_occupation as occupation, assessment_suggestion as suggestion, CONCAT("คะแนน: ", (assessment_11 + assessment_12 + assessment_13 + assessment_14 + assessment_21 + assessment_22 + assessment_23 + assessment_24 + assessment_25 + assessment_26 + assessment_31 + assessment_32 + assessment_33 + assessment_34 + assessment_35)) as detail, "" as created_by, "" as phone, assessment_ip as ip, assessment_datesave as datesave, "assessment" as table');
        $query = $this->db->get('tbl_assessment');
        return $query->result_array();
    }


    public function sum_complain_total()
    {
        $this->db->select('COUNT(complain_id) AS total_complain_sum');
        $this->db->from('tbl_complain');
        $query = $this->db->get();
        return $query->row()->total_complain_sum;
    }

    // ส่ง email ******************************
    public function list_email()
    {
        $this->db->from('tbl_email as a');
        $this->db->group_by('a.email_id');
        $this->db->order_by('a.email_id', 'DESC');
        $query = $this->db->get();
        return $query->result();
    }

    public function add_email()
    {
        $email_name = $this->input->post('email_name');
        $this->db->select('email_name');
        $this->db->where('email_name', $email_name);
        $query = $this->db->get('tbl_email');
        $num = $query->num_rows();
        if ($num > 0) {
            $this->session->set_flashdata('save_again', TRUE);
        } else {

            $data = array(
                'email_name' => $this->input->post('email_name'),
                'email_by' => $this->session->userdata('m_fname'), // เพิ่มชื่อคนที่เพิ่มข้อมูล
            );
            $query = $this->db->insert('tbl_email', $data);

            $this->space_model->update_server_current();
            $this->session->set_flashdata('save_success', TRUE);
        }
    }

    public function read_email($email_id)
    {
        $this->db->select('*');
        $this->db->from('tbl_email');
        $this->db->where('tbl_email.email_id', $email_id);
        $query = $this->db->get();
        if ($query->num_rows() > 0) {
            $data = $query->row();
            return $data;
        }
        return false;
    }

    public function edit_email($email_id)
    {
        $email_name = $this->input->post('email_name');

        // Check if the new email_name value is not already in the database for other records.
        $this->db->where('email_name', $email_name);
        $this->db->where_not_in('email_id', $email_id); // Exclude the current record being edited.
        $query = $this->db->get('tbl_email');
        $num = $query->num_rows();

        if ($num > 0) {
            // A record with the same email_name already exists in the database.
            $this->session->set_flashdata('save_again', TRUE);
        } else {
            // Update the record.
            $data = array(
                'email_name' => $email_name,
                'email_by' => $this->session->userdata('m_fname'), // Add the name of the person updating the record.
            );

            $this->db->where('email_id', $email_id);
            $this->db->update('tbl_email', $data);

            $this->space_model->update_server_current();
            $this->session->set_flashdata('save_success', TRUE);
        }
    }

    public function del_email($email_id)
    {
        $this->db->delete('tbl_email', array('email_id' => $email_id));
        $this->session->set_flashdata('del_success', TRUE);
    }

    public function updateEmailStatus()
    {
        // ตรวจสอบว่ามีการส่งข้อมูล POST มาหรือไม่
        if ($this->input->post()) {
            $emailId = $this->input->post('email_id'); // รับค่า email_id
            $newStatus = $this->input->post('new_status'); // รับค่าใหม่จาก switch checkbox

            // ทำการอัพเดตค่าในตาราง tbl_email ในฐานข้อมูลของคุณ
            $data = array(
                'email_status' => $newStatus
            );
            $this->db->where('email_id', $emailId); // ระบุ email_id ของแถวที่ต้องการอัพเดต
            $this->db->update('tbl_email', $data);

            // ส่งการตอบกลับ (response) กลับไปยังเว็บไซต์หรือแอพพลิเคชันของคุณ
            // โดยเช่นปกติคุณอาจส่ง JSON response กลับมาเพื่ออัพเดตหน้าเว็บ
            $response = array('status' => 'success', 'message' => 'อัพเดตสถานะเรียบร้อย');
            echo json_encode($response);
        } else {
            // ถ้าไม่มีข้อมูล POST ส่งมา ให้รีเดอร์เปรียบเสมอ
            show_404();
        }
    }

    public function updateEmailStatusAll($newStatus)
    {
        // อัปเดตค่า email_status ของทุกแถวในตาราง tbl_email
        $data = array(
            'email_status' => $newStatus
        );

        // อัปเดตทุกแถว
        $this->db->update('tbl_email', $data);

        // ส่งค่ากลับถ้าต้องการ
        if ($this->db->affected_rows() > 0) {
            return true; // สำเร็จ
        } else {
            return false; // ล้มเหลว หรือไม่มีแถวที่ถูกอัปเดต
        }
    }


    public function read_email_latest()
    {
        $this->db->select('*');
        $this->db->from('tbl_email');
        $this->db->order_by('email_id', 'DESC'); // เรียงลำดับจากมากไปน้อยเพื่อให้ได้ ID ล่าสุด
        $this->db->limit(1); // จำกัดผลลัพธ์ให้แค่ 1 แถว
        $query = $this->db->get();

        if ($query->num_rows() > 0) {
            return $query->row(); // ส่งข้อมูลแถวล่าสุดกลับ
        }

        return false; // หากไม่มีข้อมูลส่งกลับ false
    }
}