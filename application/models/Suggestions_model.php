<?php
class Suggestions_model extends CI_Model
{
    private $channelAccessToken;
    private $lineApiUrl;

    public function __construct()
    {
        parent::__construct();

        // ใช้ helper function get_config_value เพื่อดึงค่า token จากฐานข้อมูล
        $this->channelAccessToken = get_config_value('line_token');
        $this->lineApiUrl = 'https://api.line.me/v2/bot/message/multicast';

        // โหลด LINE Notification Library
        $this->load->library('line_notification');
    }

    public function add_new_id_entry()
    {
        // ดึงปี พ.ศ. ปัจจุบัน (เพิ่ม 543 จาก ค.ศ.)
        $current_year_thai = date('Y') + 543;

        // ตรวจสอบ ID ล่าสุดในตาราง
        $this->db->select('MAX(suggestions_id) AS max_id');
        $this->db->from('tbl_suggestions');
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

        return $new_id;
    }

    /**
     * เพิ่มข้อเสนอแนะใหม่ (รองรับ Address System & ID Card)
     */
    public function add_suggestion($data)
    {
        try {
            // *** รับ custom ID ที่ส่งมาจาก Controller ***
            $custom_id = $data['suggestions_id'] ?? null;

            if (!$custom_id) {
                log_message('error', 'Missing custom suggestions_id');
                return false;
            }

            // *** เตรียมข้อมูลสำหรับบันทึก ***
            $suggestion_data = [
                'suggestions_id' => $custom_id, // *** ใช้ custom ID ***
                'suggestion_type' => $data['suggestion_type'] ?? 'suggestion',
                'suggestions_topic' => $data['suggestions_topic'],
                'suggestions_detail' => $data['suggestions_detail'],
                'suggestions_by' => $data['suggestions_by'],
                'suggestions_phone' => $data['suggestions_phone'],
                'suggestions_email' => $data['suggestions_email'] ?? null,
                'suggestions_number' => $data['suggestions_number'] ?? null,
                'suggestions_address' => $data['suggestions_address'],
                'suggestions_is_anonymous' => $data['suggestions_is_anonymous'] ?? 0,
                'suggestions_user_id' => $data['suggestions_user_id'] ?? null,
                'suggestions_user_type' => $data['suggestions_user_type'] ?? 'guest',
                'suggestions_ip_address' => $data['suggestions_ip_address'] ?? null,
                'suggestions_user_agent' => $data['suggestions_user_agent'] ?? null,
                'suggestions_status' => 'received',
                'suggestions_priority' => $data['suggestions_priority'] ?? 'normal',
                'suggestions_category' => $data['suggestions_category'] ?? null,
                'suggestions_datesave' => date('Y-m-d H:i:s'),

                // *** ข้อมูลที่อยู่แยกสำหรับ Guest ***
                'guest_province' => $data['guest_province'] ?? null,
                'guest_amphoe' => $data['guest_amphoe'] ?? null,
                'guest_district' => $data['guest_district'] ?? null,
                'guest_zipcode' => $data['guest_zipcode'] ?? null
            ];

            // *** ตรวจสอบข้อมูลที่จำเป็น ***
            $required_fields = ['suggestions_topic', 'suggestions_detail', 'suggestions_by', 'suggestions_phone'];
            foreach ($required_fields as $field) {
                if (empty($suggestion_data[$field])) {
                    log_message('error', "Missing required field: {$field}");
                    return false;
                }
            }

            // *** ตรวจสอบเลขบัตรประชาชน (ถ้ามี) ***
            if (!empty($suggestion_data['suggestions_number'])) {
                if (!$this->validate_thai_id_card($suggestion_data['suggestions_number'])) {
                    log_message('error', 'Invalid Thai ID Card number: ' . $suggestion_data['suggestions_number']);
                    return false;
                }
            }

            // *** ตรวจสอบว่า ID ซ้ำหรือไม่ ***
            $this->db->where('suggestions_id', $custom_id);
            $existing = $this->db->get('tbl_suggestions')->num_rows();

            if ($existing > 0) {
                log_message('error', 'Duplicate suggestions_id: ' . $custom_id);
                return false;
            }

            // *** บันทึกข้อมูล ***
            $result = $this->db->insert('tbl_suggestions', $suggestion_data);
			
            log_message('info', "Line notification send by Suggestion ID : {$custom_id}");
            $this->line_notification->send_line_suggestions_notification($custom_id);


            if ($result) {
                // บันทึกประวัติ
                $this->add_suggestion_history(
                    $custom_id, // *** ใช้ custom ID ***
                    'created',
                    'สร้างข้อเสนอแนะใหม่',
                    $data['suggestions_by']
                );

                log_message('info', "New suggestion created with custom ID: {$custom_id}");
                return $custom_id; // *** คืน custom ID ***
            }

            return false;

        } catch (Exception $e) {
            log_message('error', 'Error adding suggestion: ' . $e->getMessage());
            return false;
        }
    }

    /**
     * *** เพิ่มใหม่: ตรวจสอบเลขบัตรประชาชนไทย ***
     */
    private function validate_thai_id_card($id_card)
    {
        // ตรวจสอบรูปแบบพื้นฐาน
        if (!$id_card || !preg_match('/^\d{13}$/', $id_card)) {
            return false;
        }

        // ตรวจสอบเลขซ้ำทั้งหมด
        if (preg_match('/^(\d)\1{12}$/', $id_card)) {
            return false;
        }

        // ตรวจสอบด้วยอัลกอริทึม MOD 11
        $digits = str_split($id_card);
        $weights = [13, 12, 11, 10, 9, 8, 7, 6, 5, 4, 3, 2];

        $sum = 0;
        for ($i = 0; $i < 12; $i++) {
            $sum += (int) $digits[$i] * $weights[$i];
        }

        $remainder = $sum % 11;
        $check_digit = $remainder < 2 ? (1 - $remainder) : (11 - $remainder);

        return $check_digit == (int) $digits[12];
    }

    /**
     * อัปเดตข้อเสนอแนะ (รองรับข้อมูลใหม่)
     */
    public function update_suggestion($suggestion_id, $data, $updated_by = 'System', $custom_action_description = null)
    {
        try {
            // ดึงข้อมูลเดิม
            $old_data = $this->get_suggestion_by_id($suggestion_id);
            if (!$old_data) {
                return false;
            }

            $update_data = [];

            // *** เพิ่มฟิลด์ใหม่ที่สามารถอัปเดตได้ ***
            $allowed_fields = [
                'suggestions_topic',
                'suggestions_detail',
                'suggestions_by',
                'suggestions_phone',
                'suggestions_email',
                'suggestions_number', // *** เพิ่มใหม่ ***
                'suggestions_address',
                'suggestions_status',
                'suggestions_priority',
                'suggestions_category',
                'suggestions_reply',
                'suggestions_replied_by',
                'suggestions_replied_at',
                'suggestions_updated_by',
                'suggestions_updated_at', // เพิ่ม fields ที่ controller ส่งมา
                'guest_province',
                'guest_amphoe',
                'guest_district',
                'guest_zipcode' // *** เพิ่มใหม่ ***
            ];

            foreach ($allowed_fields as $field) {
                if (isset($data[$field])) {
                    // *** ตรวจสอบเลขบัตรประชาชนก่อนอัปเดต ***
                    if ($field === 'suggestions_number' && !empty($data[$field])) {
                        if (!$this->validate_thai_id_card($data[$field])) {
                            log_message('error', 'Invalid Thai ID Card number in update: ' . $data[$field]);
                            return false;
                        }
                    }
                    $update_data[$field] = $data[$field];
                }
            }

            if (empty($update_data)) {
                return false;
            }

            // อัปเดตข้อมูลในฐานข้อมูล
            $this->db->where('suggestions_id', $suggestion_id);
            $result = $this->db->update('tbl_suggestions', $update_data);

            if ($result) {
                // บันทึกประวัติ - ใช้ custom_action_description ถ้ามี
                if (!empty($custom_action_description)) {
                    $action_desc = $custom_action_description;
                } else {
                    // สร้างคำอธิบายแบบเดิม (fallback)
                    $action_desc = $this->build_default_action_description($update_data, $updated_by);
                }

                $this->add_suggestion_history(
                    $suggestion_id,
                    'updated',
                    $action_desc,
                    $updated_by,
                    $old_data->suggestions_status,
                    $update_data['suggestions_status'] ?? null
                );

                log_message('info', "Suggestion updated: ID = {$suggestion_id} by {$updated_by}");
                return true;
            }

            return false;

        } catch (Exception $e) {
            log_message('error', 'Error updating suggestion: ' . $e->getMessage());
            return false;
        }
    }




    private function build_default_action_description($update_data, $updated_by)
    {
        $descriptions = [];

        if (isset($update_data['suggestions_status'])) {
            $descriptions[] = "เปลี่ยนสถานะ";
        }

        if (isset($update_data['suggestions_priority'])) {
            $descriptions[] = "เปลี่ยนความสำคัญ";
        }

        if (isset($update_data['suggestions_reply'])) {
            $descriptions[] = "ตอบกลับ";
        }

        // ไม่รวม updated_by, updated_at ในคำอธิบาย
        $excluded_fields = ['suggestions_updated_by', 'suggestions_updated_at', 'suggestions_replied_at'];
        $actual_changes = array_diff(array_keys($update_data), $excluded_fields);

        if (empty($descriptions) && !empty($actual_changes)) {
            $descriptions[] = "อัปเดตข้อมูล";
        }

        $action_text = !empty($descriptions) ? implode(', ', $descriptions) : 'อัปเดตข้อมูล';

        return "{$action_text} โดย {$updated_by}";
    }



    /**
     * อัปเดตสถานะ
     */
    public function update_status($suggestion_id, $new_status, $updated_by, $comment = null)
    {
        try {
            $old_data = $this->get_suggestion_by_id($suggestion_id);
            if (!$old_data) {
                return false;
            }

            $update_data = [
                'suggestions_status' => $new_status,
                'suggestions_updated_by' => $updated_by,
                'suggestions_updated_at' => date('Y-m-d H:i:s')
            ];

            $this->db->where('suggestions_id', $suggestion_id);
            $result = $this->db->update('tbl_suggestions', $update_data);

            if ($result) {
                // บันทึกประวัติ
                $action_desc = "เปลี่ยนสถานะจาก '{$old_data->suggestions_status}' เป็น '{$new_status}'";
                if ($comment) {
                    $action_desc .= " - {$comment}";
                }

                $this->add_suggestion_history($suggestion_id, 'status_changed', $action_desc, $updated_by, $old_data->suggestions_status, $new_status);

                return true;
            }

            return false;

        } catch (Exception $e) {
            log_message('error', 'Error updating suggestion status: ' . $e->getMessage());
            return false;
        }
    }

    // ===================================================================
    // *** SELECT Methods (รองรับข้อมูลใหม่) ***
    // ===================================================================




    /**
     * ดึงข้อเสนอแนะตาม ID (รองรับข้อมูลที่อยู่และเลขบัตรประชาชน) - ปรับปรุงใหม่
     */
    public function get_suggestion_by_id($suggestion_id)
    {
        try {
            // *** ตรวจสอบ input ***
            if (empty($suggestion_id)) {
                log_message('warning', 'Empty suggestion_id provided');
                return null;
            }

            // *** SELECT ข้อมูลพร้อมข้อมูลที่อยู่ครบถ้วน ***
            $this->db->select('s.*, 
                           s.suggestions_number,
                           s.guest_province, s.guest_amphoe, s.guest_district, s.guest_zipcode,
                           mp.mp_prefix, mp.mp_fname, mp.mp_lname, mp.mp_email as mp_email_db, 
                           mp.mp_number as mp_id_card, 
                           mp.mp_address as mp_address_profile,
                           mp.mp_district as mp_district_profile,
                           mp.mp_amphoe as mp_amphoe_profile,
                           mp.mp_province as mp_province_profile,
                           mp.mp_zipcode as mp_zipcode_profile,
                           m.m_fname as staff_fname, m.m_lname as staff_lname');
            $this->db->from('tbl_suggestions s');
            $this->db->join('tbl_member_public mp', 's.suggestions_user_id = mp.id AND s.suggestions_user_type = "public"', 'left');
            $this->db->join('tbl_member m', 's.suggestions_user_id = m.m_id AND s.suggestions_user_type = "staff"', 'left');
            $this->db->where('s.suggestions_id', $suggestion_id); // *** VARCHAR comparison ***

            $result = $this->db->get()->row();

            // *** ประมวลผลข้อมูลเพิ่มเติม ***
            if ($result) {
                // สร้างข้อมูลที่อยู่รวมแบบละเอียด
                $result->full_address_details = $this->build_complete_address($result);

                // ตรวจสอบเลขบัตรประชาชน
                $result->has_valid_id_card = !empty($result->suggestions_number) &&
                    $this->validate_thai_id_card($result->suggestions_number);

                // เพิ่มข้อมูลที่อยู่แยกแยะสำหรับแสดงผล
                $result->display_address = $this->get_display_address($result);

                log_message('info', "Suggestion retrieved with custom ID: {$suggestion_id}, User Type = {$result->suggestions_user_type}");
            } else {
                log_message('info', "No suggestion found with ID: {$suggestion_id}");
            }

            return $result;

        } catch (Exception $e) {
            log_message('error', 'Error getting suggestion by ID: ' . $e->getMessage());
            return null;
        }
    }

    /**
     * *** เพิ่มใหม่: สร้างข้อมูลที่อยู่รวมแบบครบถ้วน ***
     */
    private function build_complete_address($suggestion_data)
    {
        $address_info = [
            'has_address' => false,
            'source_type' => null, // 'guest', 'public_profile', 'suggestion_form'
            'additional_address' => '',
            'district' => '',
            'amphoe' => '',
            'province' => '',
            'zipcode' => '',
            'full_address' => '',
            'formatted_address' => ''
        ];

        try {
            // กำหนด source ของข้อมูลที่อยู่ตาม user type
            if ($suggestion_data->suggestions_user_type === 'public' && !empty($suggestion_data->suggestions_user_id)) {
                // Public User: ใช้ข้อมูลจาก profile ก่อน แล้วค่อย fallback ไปข้อมูลในฟอร์ม

                // ตรวจสอบว่ามีข้อมูลที่อยู่ใน profile หรือไม่
                if (!empty($suggestion_data->mp_province_profile) || !empty($suggestion_data->mp_address_profile)) {
                    $address_info['source_type'] = 'public_profile';
                    $address_info['additional_address'] = $suggestion_data->mp_address_profile ?: '';
                    $address_info['district'] = $suggestion_data->mp_district_profile ?: '';
                    $address_info['amphoe'] = $suggestion_data->mp_amphoe_profile ?: '';
                    $address_info['province'] = $suggestion_data->mp_province_profile ?: '';
                    $address_info['zipcode'] = $suggestion_data->mp_zipcode_profile ?: '';
                    $address_info['has_address'] = true;

                    log_message('debug', "Using public user profile address for suggestion: {$suggestion_data->suggestions_id}");
                }
                // ถ้าไม่มีข้อมูลใน profile ให้ใช้ข้อมูลที่กรอกในฟอร์ม
                elseif (!empty($suggestion_data->suggestions_address) || !empty($suggestion_data->guest_province)) {
                    $address_info['source_type'] = 'suggestion_form';
                    $address_info['additional_address'] = $suggestion_data->suggestions_address ?: '';
                    $address_info['district'] = $suggestion_data->guest_district ?: '';
                    $address_info['amphoe'] = $suggestion_data->guest_amphoe ?: '';
                    $address_info['province'] = $suggestion_data->guest_province ?: '';
                    $address_info['zipcode'] = $suggestion_data->guest_zipcode ?: '';
                    $address_info['has_address'] = true;

                    log_message('debug', "Using suggestion form address for public user: {$suggestion_data->suggestions_id}");
                }

            } elseif ($suggestion_data->suggestions_user_type === 'guest') {
                // Guest User: ใช้ข้อมูลที่กรอกในฟอร์มเท่านั้น
                if (!empty($suggestion_data->suggestions_address) || !empty($suggestion_data->guest_province)) {
                    $address_info['source_type'] = 'guest';
                    $address_info['additional_address'] = $suggestion_data->suggestions_address ?: '';
                    $address_info['district'] = $suggestion_data->guest_district ?: '';
                    $address_info['amphoe'] = $suggestion_data->guest_amphoe ?: '';
                    $address_info['province'] = $suggestion_data->guest_province ?: '';
                    $address_info['zipcode'] = $suggestion_data->guest_zipcode ?: '';
                    $address_info['has_address'] = true;

                    log_message('debug', "Using guest form address for suggestion: {$suggestion_data->suggestions_id}");
                }

            } elseif ($suggestion_data->suggestions_user_type === 'staff') {
                // Staff: ใช้ข้อมูลที่กรอกในฟอร์ม
                if (!empty($suggestion_data->suggestions_address)) {
                    $address_info['source_type'] = 'staff';
                    $address_info['additional_address'] = $suggestion_data->suggestions_address ?: '';
                    $address_info['has_address'] = true;

                    log_message('debug', "Using staff form address for suggestion: {$suggestion_data->suggestions_id}");
                }
            }

            // สร้างที่อยู่รวม
            if ($address_info['has_address']) {
                $address_parts = [];

                if (!empty($address_info['additional_address'])) {
                    $address_parts[] = $address_info['additional_address'];
                }

                if (!empty($address_info['district'])) {
                    $address_parts[] = 'ตำบล' . $address_info['district'];
                }

                if (!empty($address_info['amphoe'])) {
                    $address_parts[] = 'อำเภอ' . $address_info['amphoe'];
                }

                if (!empty($address_info['province'])) {
                    $address_parts[] = 'จังหวัด' . $address_info['province'];
                }

                if (!empty($address_info['zipcode'])) {
                    $address_parts[] = $address_info['zipcode'];
                }

                $address_info['full_address'] = implode(' ', $address_parts);

                // สร้างที่อยู่แบบ formatted สำหรับแสดงผล
                $formatted_parts = [];

                if (!empty($address_info['additional_address'])) {
                    $formatted_parts[] = $address_info['additional_address'];
                }

                $location_parts = [];
                if (!empty($address_info['district']))
                    $location_parts[] = 'ต.' . $address_info['district'];
                if (!empty($address_info['amphoe']))
                    $location_parts[] = 'อ.' . $address_info['amphoe'];
                if (!empty($address_info['province']))
                    $location_parts[] = 'จ.' . $address_info['province'];
                if (!empty($address_info['zipcode']))
                    $location_parts[] = $address_info['zipcode'];

                if (!empty($location_parts)) {
                    $formatted_parts[] = implode(' ', $location_parts);
                }

                $address_info['formatted_address'] = implode(' ', $formatted_parts);
            }

            log_message('debug', "Complete address built for suggestion {$suggestion_data->suggestions_id}: " .
                "Source={$address_info['source_type']}, Has_Address=" . ($address_info['has_address'] ? 'YES' : 'NO'));

            return $address_info;

        } catch (Exception $e) {
            log_message('error', 'Error building complete address: ' . $e->getMessage());
            return $address_info; // Return default empty structure
        }
    }





    private function get_display_address($suggestion_data)
    {
        $display = [
            'short' => 'ไม่ระบุที่อยู่',
            'full' => 'ไม่ระบุที่อยู่',
            'province_only' => 'ไม่ระบุ',
            'has_complete_address' => false
        ];

        try {
            $address_details = $suggestion_data->full_address_details ?? null;

            if ($address_details && $address_details['has_address']) {

                // ที่อยู่แบบสั้น (แสดงเฉพาะจังหวัด)
                if (!empty($address_details['province'])) {
                    $display['short'] = 'จังหวัด' . $address_details['province'];
                    $display['province_only'] = $address_details['province'];
                }

                // ที่อยู่แบบเต็ม
                if (!empty($address_details['formatted_address'])) {
                    $display['full'] = $address_details['formatted_address'];
                } elseif (!empty($address_details['full_address'])) {
                    $display['full'] = $address_details['full_address'];
                }

                // ตรวจสอบความสมบูรณ์ของที่อยู่
                $display['has_complete_address'] = (
                    !empty($address_details['province']) &&
                    !empty($address_details['amphoe']) &&
                    !empty($address_details['district'])
                );

                log_message('debug', "Display address created for suggestion {$suggestion_data->suggestions_id}: Short={$display['short']}");
            }

            return $display;

        } catch (Exception $e) {
            log_message('error', 'Error getting display address: ' . $e->getMessage());
            return $display;
        }
    }






    public function check_user_access_to_suggestion($suggestion_id, $user_id, $user_type)
    {
        try {
            $this->db->select('suggestions_user_type, suggestions_user_id');
            $this->db->from('tbl_suggestions');
            $this->db->where('suggestions_id', $suggestion_id);
            $suggestion = $this->db->get()->row();

            if (!$suggestion) {
                log_message('warning', "Suggestion not found for access check: {$suggestion_id}");
                return false;
            }

            // Staff สามารถเข้าถึงได้ทุกข้อเสนอแนะ
            if ($user_type === 'staff') {
                log_message('info', "Access granted for STAFF user to suggestion: {$suggestion_id}");
                return true;
            }

            // Public user สามารถเข้าถึงได้เฉพาะของตัวเองที่เป็น public user type
            if ($user_type === 'public') {
                $has_access = ($suggestion->suggestions_user_type === 'public' &&
                    $suggestion->suggestions_user_id == $user_id);

                log_message('info', "Access check for PUBLIC user {$user_id} to suggestion {$suggestion_id}: " . ($has_access ? 'GRANTED' : 'DENIED'));

                return $has_access;
            }

            // Guest user สามารถเข้าถึงได้เฉพาะที่เป็น guest user type
            if ($user_type === 'guest') {
                $has_access = ($suggestion->suggestions_user_type === 'guest');

                log_message('info', "Access check for GUEST user to suggestion {$suggestion_id}: " . ($has_access ? 'GRANTED' : 'DENIED'));

                return $has_access;
            }

            log_message('warning', "Unknown user type for access check: {$user_type}");
            return false;

        } catch (Exception $e) {
            log_message('error', 'Error checking user access to suggestion: ' . $e->getMessage());
            return false;
        }
    }



    public function get_suggestion_by_id_with_access_check($suggestion_id, $user_id, $user_type)
    {
        try {
            // ดึงข้อมูลข้อเสนอแนะก่อน
            $suggestion = $this->get_suggestion_by_id($suggestion_id);

            if (!$suggestion) {
                log_message('warning', "Suggestion not found: {$suggestion_id}");
                return null;
            }

            // ตรวจสอบสิทธิ์การเข้าถึง
            $has_access = $this->check_user_access_to_suggestion($suggestion_id, $user_id, $user_type);

            if (!$has_access) {
                log_message('warning', "Access denied for user {$user_id} (type: {$user_type}) to suggestion {$suggestion_id}");
                return null;
            }

            log_message('info', "Suggestion accessed successfully: {$suggestion_id} by user {$user_id} (type: {$user_type})");

            return $suggestion;

        } catch (Exception $e) {
            log_message('error', 'Error getting suggestion with access check: ' . $e->getMessage());
            return null;
        }
    }





    public function get_suggestions_with_filters_for_staff($filters = [], $limit = 20, $offset = 0)
    {
        try {
            // เฉพาะ Staff เท่านั้นที่ใช้ฟังก์ชันนี้ได้
            $this->db->select('s.*, s.suggestions_number, s.guest_province, s.guest_amphoe, s.guest_district, s.guest_zipcode,
                           mp.mp_prefix, mp.mp_fname, mp.mp_lname, mp.mp_province as mp_province_profile,
                           m.m_fname as staff_fname, m.m_lname as staff_lname');
            $this->db->from('tbl_suggestions s');
            $this->db->join('tbl_member_public mp', 's.suggestions_user_id = mp.id AND s.suggestions_user_type = "public"', 'left');
            $this->db->join('tbl_member m', 's.suggestions_user_id = m.m_id AND s.suggestions_user_type = "staff"', 'left');

            // Apply filters
            $this->apply_filters($filters);

            // นับจำนวนทั้งหมด
            $total_query = clone $this->db;
            $total_rows = $total_query->count_all_results();

            // Apply filters อีกครั้งและ pagination
            $this->apply_filters($filters);
            $this->db->order_by('s.suggestions_datesave', 'DESC');
            $this->db->limit($limit, $offset);

            $suggestions = $this->db->get()->result();

            // Log query for debugging
            log_message('debug', 'Staff suggestions query: ' . $this->db->last_query());

            // เพิ่มการประมวลผลข้อมูลที่อยู่สำหรับแต่ละรายการ
            foreach ($suggestions as $suggestion) {
                $suggestion->full_address_details = $this->build_full_address($suggestion);
                $suggestion->has_valid_id_card = !empty($suggestion->suggestions_number) &&
                    $this->validate_thai_id_card($suggestion->suggestions_number);
            }

            log_message('info', "Staff suggestions query results: {$total_rows} total, " . count($suggestions) . " returned");

            return [
                'data' => $suggestions,
                'total' => $total_rows
            ];

        } catch (Exception $e) {
            log_message('error', 'Error getting suggestions with filters for staff: ' . $e->getMessage());
            return ['data' => [], 'total' => 0];
        }
    }




    /**
     * *** เพิ่มใหม่: สร้างข้อมูลที่อยู่รวม ***
     */
    private function build_full_address($suggestion_data)
    {
        $address_parts = [];

        // ที่อยู่เพิ่มเติม
        if (!empty($suggestion_data->suggestions_address)) {
            $address_parts[] = $suggestion_data->suggestions_address;
        }

        // ข้อมูลที่อยู่แยก (สำหรับ guest)
        if (!empty($suggestion_data->guest_district)) {
            $address_parts[] = 'ตำบล' . $suggestion_data->guest_district;
        }

        if (!empty($suggestion_data->guest_amphoe)) {
            $address_parts[] = 'อำเภอ' . $suggestion_data->guest_amphoe;
        }

        if (!empty($suggestion_data->guest_province)) {
            $address_parts[] = 'จังหวัด' . $suggestion_data->guest_province;
        }

        if (!empty($suggestion_data->guest_zipcode)) {
            $address_parts[] = $suggestion_data->guest_zipcode;
        }

        return [
            'additional_address' => $suggestion_data->suggestions_address ?? '',
            'province' => $suggestion_data->guest_province ?? '',
            'amphoe' => $suggestion_data->guest_amphoe ?? '',
            'district' => $suggestion_data->guest_district ?? '',
            'zipcode' => $suggestion_data->guest_zipcode ?? '',
            'full_address' => implode(' ', $address_parts)
        ];
    }



    /**
     * Apply filters สำหรับ query (รองรับฟิลเตอร์ใหม่)
     */
    private function apply_filters($filters)
    {
        if (!empty($filters['status'])) {
            $this->db->where('s.suggestions_status', $filters['status']);
        }

        if (!empty($filters['type'])) {
            $this->db->where('s.suggestion_type', $filters['type']);
        }

        if (!empty($filters['priority'])) {
            $this->db->where('s.suggestions_priority', $filters['priority']);
        }

        if (!empty($filters['user_type'])) {
            $this->db->where('s.suggestions_user_type', $filters['user_type']);
        }

        if (!empty($filters['anonymous'])) {
            $this->db->where('s.suggestions_is_anonymous', $filters['anonymous'] === 'yes' ? 1 : 0);
        }

        // *** เพิ่มใหม่: ฟิลเตอร์ตามจังหวัด ***
        if (!empty($filters['province'])) {
            $this->db->group_start();
            $this->db->like('s.guest_province', $filters['province']);
            $this->db->or_like('mp.mp_province', $filters['province']);
            $this->db->group_end();
        }

        // *** เพิ่มใหม่: ฟิลเตอร์ตามรหัสไปรษณีย์ ***
        if (!empty($filters['zipcode'])) {
            $this->db->group_start();
            $this->db->like('s.guest_zipcode', $filters['zipcode']);
            $this->db->or_like('mp.mp_zipcode', $filters['zipcode']);
            $this->db->group_end();
        }

        // *** เพิ่มใหม่: ฟิลเตอร์ตามเลขบัตรประชาชน ***
        if (!empty($filters['id_card'])) {
            $this->db->group_start();
            $this->db->like('s.suggestions_number', $filters['id_card']);
            $this->db->or_like('mp.mp_number', $filters['id_card']);
            $this->db->group_end();
        }

        if (!empty($filters['date_from'])) {
            $this->db->where('DATE(s.suggestions_datesave) >=', $filters['date_from']);
        }

        if (!empty($filters['date_to'])) {
            $this->db->where('DATE(s.suggestions_datesave) <=', $filters['date_to']);
        }

        if (!empty($filters['search'])) {
            $search = $filters['search'];
            $this->db->group_start();
            $this->db->like('s.suggestions_topic', $search);
            $this->db->or_like('s.suggestions_detail', $search);
            $this->db->or_like('s.suggestions_by', $search);
            $this->db->or_like('s.suggestions_phone', $search);
            $this->db->or_like('s.suggestions_email', $search);
            $this->db->or_like('s.suggestions_number', $search); // *** เพิ่มใหม่ ***
            $this->db->or_like('s.guest_province', $search); // *** เพิ่มใหม่ ***
            $this->db->or_like('s.guest_district', $search); // *** เพิ่มใหม่ ***
            $this->db->group_end();
        }
    }

    public function get_suggestions_by_phone_and_user_type($phone, $user_type, $user_id = null)
    {
        try {
            $this->db->select('*');
            $this->db->from('tbl_suggestions');
            $this->db->where('suggestions_phone', $phone);
            $this->db->where('suggestions_user_type', $user_type);

            if ($user_type === 'public' && $user_id) {
                $this->db->where('suggestions_user_id', $user_id);
            }

            $this->db->order_by('suggestions_datesave', 'DESC');

            return $this->db->get()->result();

        } catch (Exception $e) {
            log_message('error', 'Error getting suggestions by phone: ' . $e->getMessage());
            return [];
        }
    }


    /**
     * *** เพิ่มใหม่: ค้นหาข้อเสนอแนะด้วยเลขบัตรประชาชนตาม user type ***
     */
    public function get_suggestions_by_id_card_and_user_type($id_card, $user_type, $user_id = null)
    {
        try {
            $this->db->select('*');
            $this->db->from('tbl_suggestions');
            $this->db->where('suggestions_number', $id_card);
            $this->db->where('suggestions_user_type', $user_type);

            if ($user_type === 'public' && $user_id) {
                $this->db->where('suggestions_user_id', $user_id);
            }

            $this->db->order_by('suggestions_datesave', 'DESC');

            return $this->db->get()->result();

        } catch (Exception $e) {
            log_message('error', 'Error getting suggestions by ID card: ' . $e->getMessage());
            return [];
        }
    }





    public function is_suggestion_id_exists($suggestion_id)
    {
        try {
            $this->db->where('suggestions_id', $suggestion_id);
            $count = $this->db->count_all_results('tbl_suggestions');

            return $count > 0;

        } catch (Exception $e) {
            log_message('error', 'Error checking suggestion ID existence: ' . $e->getMessage());
            return true; // ถ้าเกิด error ให้ถือว่ามีอยู่แล้ว (ปลอดภัย)
        }
    }




    /**
     * *** ปรับปรุง: ดึงข้อเสนอแนะตามผู้ใช้ (เพิ่มการตรวจสอบ user type) ***
     */
    public function get_suggestions_by_user($user_id, $user_type)
    {
        try {
            $this->db->select('s.*, s.suggestions_number, s.guest_province, s.guest_amphoe, s.guest_district, s.guest_zipcode');
            $this->db->from('tbl_suggestions s');

            if ($user_type === 'public') {
                // Public user: เฉพาะข้อเสนอแนะที่เป็น public user type และเป็นของตัวเอง
                $this->db->where('s.suggestions_user_type', 'public');
                $this->db->where('s.suggestions_user_id', $user_id);

                log_message('info', "Get suggestions for PUBLIC user - User ID: {$user_id}");

            } elseif ($user_type === 'staff') {
                // Staff: สามารถดูได้ทั้งหมด (ไม่จำกัด)
                log_message('info', "Get suggestions for STAFF user - User ID: {$user_id}");

            } else {
                // Guest หรือ user type ไม่ถูกต้อง
                log_message('warning', "Invalid user type for get_suggestions_by_user: {$user_type}");
                return [];
            }

            $this->db->order_by('s.suggestions_datesave', 'DESC');
            $suggestions = $this->db->get()->result();

            // Log query for debugging
            log_message('debug', 'Get suggestions by user query: ' . $this->db->last_query());

            // เพิ่มข้อมูลที่อยู่
            foreach ($suggestions as $suggestion) {
                $suggestion->full_address_details = $this->build_full_address($suggestion);
            }

            log_message('info', "User suggestions results - User ID: {$user_id}, User Type: {$user_type}, Results: " . count($suggestions));

            return $suggestions;

        } catch (Exception $e) {
            log_message('error', 'Error getting suggestions by user: ' . $e->getMessage());
            return [];
        }
    }






    /**
     * *** ปรับปรุง: ดึงรายการข้อเสนอแนะทั้งหมด (เพิ่มการกรองตาม user type สำหรับ staff) ***
     */
    public function get_suggestions_with_filters($filters = [], $limit = 20, $offset = 0)
    {
        try {
            // *** เพิ่มฟิลด์ที่อยู่ครบถ้วนในการ SELECT ***
            $this->db->select('s.*, s.suggestions_number, s.guest_province, s.guest_amphoe, s.guest_district, s.guest_zipcode,
                           mp.mp_prefix, mp.mp_fname, mp.mp_lname, 
                           mp.mp_address as mp_address_profile,
                           mp.mp_district as mp_district_profile,
                           mp.mp_amphoe as mp_amphoe_profile,
                           mp.mp_province as mp_province_profile,
                           mp.mp_zipcode as mp_zipcode_profile,
                           m.m_fname as staff_fname, m.m_lname as staff_lname');
            $this->db->from('tbl_suggestions s');
            $this->db->join('tbl_member_public mp', 's.suggestions_user_id = mp.id AND s.suggestions_user_type = "public"', 'left');
            $this->db->join('tbl_member m', 's.suggestions_user_id = m.m_id AND s.suggestions_user_type = "staff"', 'left');

            // Apply filters (รวมฟิลเตอร์ใหม่)
            $this->apply_filters($filters);

            // นับจำนวนทั้งหมด
            $total_query = clone $this->db;
            $total_rows = $total_query->count_all_results();

            // Apply filters อีกครั้งและ pagination
            $this->apply_filters($filters);
            $this->db->order_by('s.suggestions_datesave', 'DESC');
            $this->db->limit($limit, $offset);

            $suggestions = $this->db->get()->result();

            // *** เพิ่มการประมวลผลข้อมูลที่อยู่แบบครบถ้วนสำหรับแต่ละรายการ ***
            foreach ($suggestions as $suggestion) {
                $suggestion->full_address_details = $this->build_complete_address($suggestion);
                $suggestion->display_address = $this->get_display_address($suggestion);
                $suggestion->has_valid_id_card = !empty($suggestion->suggestions_number) &&
                    $this->validate_thai_id_card($suggestion->suggestions_number);
            }

            return [
                'data' => $suggestions,
                'total' => $total_rows
            ];

        } catch (Exception $e) {
            log_message('error', 'Error getting suggestions with filters: ' . $e->getMessage());
            return ['data' => [], 'total' => 0];
        }
    }




    /**
     * *** เพิ่มใหม่: ค้นหาข้อเสนอแนะด้วยเลขบัตรประชาชน ***
     */
    public function get_suggestions_by_id_card($id_card)
    {
        try {
            if (!$this->validate_thai_id_card($id_card)) {
                return [];
            }

            $this->db->select('s.*, s.guest_province, s.guest_amphoe, s.guest_district, s.guest_zipcode');
            $this->db->from('tbl_suggestions s');
            $this->db->where('s.suggestions_number', $id_card);
            $this->db->order_by('s.suggestions_datesave', 'DESC');

            $suggestions = $this->db->get()->result();

            // เพิ่มข้อมูลที่อยู่
            foreach ($suggestions as $suggestion) {
                $suggestion->full_address_details = $this->build_full_address($suggestion);
            }

            return $suggestions;

        } catch (Exception $e) {
            log_message('error', 'Error getting suggestions by ID card: ' . $e->getMessage());
            return [];
        }
    }

    /**
     * ดึงข้อเสนอแนะตามเบอร์โทร
     */
    public function get_suggestions_by_phone($phone)
    {
        try {
            $this->db->select('s.*, s.guest_province, s.guest_amphoe, s.guest_district, s.guest_zipcode');
            $this->db->from('tbl_suggestions s');
            $this->db->where('s.suggestions_phone', $phone);
            $this->db->order_by('s.suggestions_datesave', 'DESC');

            $suggestions = $this->db->get()->result();

            // เพิ่มข้อมูลที่อยู่
            foreach ($suggestions as $suggestion) {
                $suggestion->full_address_details = $this->build_full_address($suggestion);
            }

            return $suggestions;

        } catch (Exception $e) {
            log_message('error', 'Error getting suggestions by phone: ' . $e->getMessage());
            return [];
        }
    }

    // ===================================================================
    // *** FILE Methods (เหมือนเดิม) ***
    // ===================================================================

    /**
     * เพิ่มไฟล์แนบ
     */
    public function add_suggestion_file($file_data)
    {
        try {
            // *** ตรวจสอบว่า suggestions_id เป็น VARCHAR ***
            if (empty($file_data['suggestions_file_ref_id'])) {
                log_message('error', 'Missing suggestions_file_ref_id');
                return false;
            }

            $result = $this->db->insert('tbl_suggestions_files', $file_data);

            if ($result) {
                $file_id = $this->db->insert_id(); // File ID ยังใช้ AUTO_INCREMENT
                log_message('info', "File added for suggestion ID: {$file_data['suggestions_file_ref_id']}, File ID: {$file_id}");
                return $file_id;
            }

            return false;

        } catch (Exception $e) {
            log_message('error', 'Error adding suggestion file: ' . $e->getMessage());
            return false;
        }
    }

    /**
     * ดึงไฟล์แนบของข้อเสนอแนะ
     */
    public function get_suggestion_files($suggestion_id)
    {
        try {
            $this->db->select('*');
            $this->db->from('tbl_suggestions_files');
            $this->db->where('suggestions_file_ref_id', $suggestion_id);
            $this->db->where('suggestions_file_status', 'active');
            $this->db->order_by('suggestions_file_uploaded_at', 'ASC');

            return $this->db->get()->result();

        } catch (Exception $e) {
            log_message('error', 'Error getting suggestion files: ' . $e->getMessage());
            return [];
        }
    }

    /**
     * ดึงข้อมูลไฟล์ตามชื่อไฟล์
     */
    public function get_file_info($file_name)
    {
        try {
            $this->db->select('*');
            $this->db->from('tbl_suggestions_files');
            $this->db->where('suggestions_file_name', $file_name);
            $this->db->where('suggestions_file_status', 'active');

            return $this->db->get()->row();

        } catch (Exception $e) {
            log_message('error', 'Error getting file info: ' . $e->getMessage());
            return null;
        }
    }




    public function get_permission_usage_statistics($date_from = null, $date_to = null)
    {
        try {
            // สถิติการอัปเดตสถานะตามผู้ใช้
            $this->db->select('h.action_by, COUNT(*) as action_count, m.m_system');
            $this->db->from('tbl_suggestions_history h');
            $this->db->join('tbl_member m', 'CONCAT(m.m_fname, " ", m.m_lname) = h.action_by', 'left');
            $this->db->where('h.action_type', 'updated');

            if ($date_from) {
                $this->db->where('DATE(h.action_date) >=', $date_from);
            }

            if ($date_to) {
                $this->db->where('DATE(h.action_date) <=', $date_to);
            }

            $this->db->group_by(['h.action_by', 'm.m_system']);
            $this->db->order_by('action_count', 'DESC');

            $usage_stats = $this->db->get()->result();

            // นับตามระดับสิทธิ์
            $by_system = [
                'system_admin' => 0,
                'super_admin' => 0,
                'user_admin' => 0,
                'unknown' => 0
            ];

            $total_actions = 0;

            foreach ($usage_stats as $stat) {
                $total_actions += $stat->action_count;

                if (isset($by_system[$stat->m_system])) {
                    $by_system[$stat->m_system] += $stat->action_count;
                } else {
                    $by_system['unknown'] += $stat->action_count;
                }
            }

            return [
                'by_user' => $usage_stats,
                'by_system' => $by_system,
                'total_actions' => $total_actions,
                'date_range' => [
                    'from' => $date_from,
                    'to' => $date_to
                ]
            ];

        } catch (Exception $e) {
            log_message('error', 'Error getting permission usage statistics: ' . $e->getMessage());
            return [
                'by_user' => [],
                'by_system' => [],
                'total_actions' => 0,
                'date_range' => ['from' => $date_from, 'to' => $date_to]
            ];
        }
    }





    public function check_single_suggestion_access($suggestion_id, $user_system, $user_id, $grant_user_ref_id = null)
    {
        try {
            // ดึงข้อมูลข้อเสนอแนะ
            $suggestion = $this->get_suggestion_by_id($suggestion_id);

            if (!$suggestion) {
                return [
                    'can_access' => false,
                    'reason' => 'Suggestion not found'
                ];
            }

            // ตรวจสอบสิทธิ์ทั่วไป
            $permissions = $this->check_suggestion_permissions($user_system, $grant_user_ref_id);

            if (!$permissions['can_view']) {
                return [
                    'can_access' => false,
                    'reason' => 'No view permission'
                ];
            }

            // สำหรับ system_admin และ super_admin ดูได้ทั้งหมด
            if (in_array($user_system, ['system_admin', 'super_admin'])) {
                return [
                    'can_access' => true,
                    'reason' => 'Administrator access'
                ];
            }

            // สำหรับ user_admin และ end_user ดูได้ทั้งหมด (แต่จัดการได้เฉพาะที่มีสิทธิ์)
            return [
                'can_access' => true,
                'reason' => 'Standard user access',
                'can_modify' => $permissions['can_handle']
            ];

        } catch (Exception $e) {
            log_message('error', 'Error checking single suggestion access: ' . $e->getMessage());
            return [
                'can_access' => false,
                'reason' => 'Error occurred during access check'
            ];
        }
    }





    // ===================================================================
    // *** HISTORY Methods (เหมือนเดิม) ***
    // ===================================================================

    /**
     * เพิ่มประวัติการดำเนินการ
     */
    public function add_suggestion_history($suggestions_id, $action_type, $action_description, $action_by, $old_status = null, $new_status = null, $additional_data = null)
    {
        try {
            $history_data = [
                'suggestions_id' => $suggestions_id, // *** VARCHAR ID ***
                'action_type' => $action_type,
                'action_description' => $action_description,
                'action_by' => $action_by,
                'action_date' => date('Y-m-d H:i:s'),
                'old_status' => $old_status,
                'new_status' => $new_status,
                'additional_data' => $additional_data ? json_encode($additional_data, JSON_UNESCAPED_UNICODE) : null
            ];

            $result = $this->db->insert('tbl_suggestions_history', $history_data);

            if ($result) {
                log_message('info', "History added for suggestion ID: {$suggestions_id}, Action: {$action_type}");
                return true;
            }

            return false;

        } catch (Exception $e) {
            log_message('error', 'Error adding suggestion history: ' . $e->getMessage());
            return false;
        }
    }

    /**
     * ดึงประวัติการดำเนินการ
     */
    public function get_suggestion_history($suggestion_id)
    {
        try {
            $this->db->select('*');
            $this->db->from('tbl_suggestions_history');
            $this->db->where('suggestions_id', $suggestion_id);
            $this->db->order_by('action_date', 'ASC');

            return $this->db->get()->result();

        } catch (Exception $e) {
            log_message('error', 'Error getting suggestion history: ' . $e->getMessage());
            return [];
        }
    }

    // ===================================================================
    // *** STATISTICS Methods (ปรับปรุงให้รองรับข้อมูลใหม่) ***
    // ===================================================================

    /**
     * ดึงสถิติข้อเสนอแนะ (รองรับข้อมูลที่อยู่และเลขบัตรประชาชน)
     */
    public function get_suggestions_statistics_by_user_type($user_type, $user_id = null)
    {
        try {
            $stats = [];

            // สร้าง WHERE condition ตาม user type
            $where_conditions = [];

            if ($user_type === 'public' && $user_id !== null) {
                $where_conditions[] = "suggestions_user_type = 'public'";
                $where_conditions[] = "suggestions_user_id = {$user_id}";

            } elseif ($user_type === 'guest') {
                $where_conditions[] = "suggestions_user_type = 'guest'";

            } elseif ($user_type === 'staff') {
                // Staff ดูได้ทั้งหมด - ไม่ต้องมี WHERE condition เพิ่ม

            } else {
                log_message('warning', "Invalid user type for statistics: {$user_type}");
                return [];
            }

            $where_clause = !empty($where_conditions) ? 'WHERE ' . implode(' AND ', $where_conditions) : '';

            // สถิติตามสถานะ
            $status_query = "
            SELECT suggestions_status, COUNT(*) as count 
            FROM tbl_suggestions 
            {$where_clause}
            GROUP BY suggestions_status
        ";
            $status_stats = $this->db->query($status_query)->result();

            $stats['by_status'] = [];
            foreach ($status_stats as $stat) {
                $stats['by_status'][$stat->suggestions_status] = $stat->count;
            }

            // สถิติตามประเภท
            $type_query = "
            SELECT suggestion_type, COUNT(*) as count 
            FROM tbl_suggestions 
            {$where_clause}
            GROUP BY suggestion_type
        ";
            $type_stats = $this->db->query($type_query)->result();

            $stats['by_type'] = [];
            foreach ($type_stats as $stat) {
                $stats['by_type'][$stat->suggestion_type] = $stat->count;
            }

            // จำนวนรวม
            $total_query = "SELECT COUNT(*) as total FROM tbl_suggestions {$where_clause}";
            $total_result = $this->db->query($total_query)->row();
            $stats['total'] = $total_result ? $total_result->total : 0;

            log_message('info', "Statistics generated for user type: {$user_type}, total: {$stats['total']}");

            return $stats;

        } catch (Exception $e) {
            log_message('error', 'Error getting suggestions statistics by user type: ' . $e->getMessage());
            return [];
        }
    }

    /**
     * ดึงข้อเสนอแนะล่าสุด (รองรับข้อมูลใหม่)
     */
    public function get_recent_suggestions($limit = 10)
    {
        try {
            $this->db->select('s.*, s.suggestions_number, s.guest_province, s.guest_district,
                               mp.mp_prefix, mp.mp_fname, mp.mp_lname');
            $this->db->from('tbl_suggestions s');
            $this->db->join('tbl_member_public mp', 's.suggestions_user_id = mp.id AND s.suggestions_user_type = "public"', 'left');
            $this->db->order_by('s.suggestions_datesave', 'DESC');
            $this->db->limit($limit);

            $suggestions = $this->db->get()->result();

            // เพิ่มข้อมูลที่อยู่
            foreach ($suggestions as $suggestion) {
                $suggestion->full_address_details = $this->build_full_address($suggestion);
            }

            return $suggestions;

        } catch (Exception $e) {
            log_message('error', 'Error getting recent suggestions: ' . $e->getMessage());
            return [];
        }
    }

    // ===================================================================
    // *** UTILITY Methods (เหมือนเดิม + เพิ่มใหม่) ***
    // ===================================================================

    /**
     * ตรวจสอบว่ามีข้อเสนอแนะหรือไม่
     */
    public function suggestion_exists($suggestion_id)
    {
        try {
            $this->db->where('suggestions_id', $suggestion_id);
            return $this->db->count_all_results('tbl_suggestions') > 0;

        } catch (Exception $e) {
            log_message('error', 'Error checking suggestion exists: ' . $e->getMessage());
            return false;
        }
    }

    /**
     * *** เพิ่มใหม่: ตรวจสอบเลขบัตรประชาชนซ้ำ ***
     */
    public function check_id_card_duplicate($id_card, $exclude_suggestion_id = null)
    {
        try {
            if (empty($id_card)) {
                return false;
            }

            $this->db->where('suggestions_number', $id_card);
            if ($exclude_suggestion_id) {
                $this->db->where('suggestions_id !=', $exclude_suggestion_id);
            }

            return $this->db->count_all_results('tbl_suggestions') > 0;

        } catch (Exception $e) {
            log_message('error', 'Error checking ID card duplicate: ' . $e->getMessage());
            return false;
        }
    }



    /**
     * ดึงตัวเลือกสถานะทั้งหมด
     */
    public function get_all_status_options()
    {
        return [
            ['value' => 'received', 'label' => 'เรื่องเสนอแนะใหม่'],

            ['value' => 'replied', 'label' => 'รับเรื่องเสนอแนะแล้ว']

        ];
    }

    /**
     * ดึงตัวเลือกประเภททั้งหมด
     */
    public function get_all_type_options()
    {
        return [
            ['value' => 'suggestion', 'label' => 'ข้อเสนอแนะ'],
            ['value' => 'feedback', 'label' => 'ความคิดเห็น'],
            ['value' => 'improvement', 'label' => 'การปรับปรุง']
        ];
    }

    /**
     * ดึงตัวเลือกความสำคัญทั้งหมด
     */
    public function get_all_priority_options()
    {
        return [
            ['value' => 'low', 'label' => 'ต่ำ'],
            ['value' => 'normal', 'label' => 'ปกติ'],
            ['value' => 'high', 'label' => 'สูง'],
            ['value' => 'urgent', 'label' => 'เร่งด่วน']
        ];
    }

    // ===================================================================
    // *** LEGACY METHODS (คงไว้เพื่อ Backward Compatibility) ***
    // ===================================================================

    public function add_suggestions()
    {
        $new_id = $this->add_new_id_entry();

        // หาพื้นที่ว่าง และอัพโหลด limit
        $used_space_mb = $this->space_model->get_used_space();
        $upload_limit_mb = $this->space_model->get_limit_storage();
        $total_space_required = 0;

        // ตรวจสอบพื้นที่รูปภาพ
        if (isset($_FILES['suggestions_img_img'])) {
            foreach ($_FILES['suggestions_img_img']['size'] as $size) {
                $total_space_required += $size;
            }
        }

        // คำนวณพื้นที่เป็น MB
        if ($used_space_mb + ($total_space_required / (1024 * 1024)) >= $upload_limit_mb) {
            $this->session->set_flashdata('save_error', TRUE);
            redirect('suggestions_backend/adding');
            return;
        }

        // *** เพิ่มฟิลด์ใหม่ในการบันทึก ***
        $suggestions_data = array(
            'suggestions_id' => $new_id,
            'suggestions_topic' => $this->input->post('suggestions_topic'),
            'suggestions_detail' => $this->input->post('suggestions_detail'),
            'suggestions_by' => $this->input->post('suggestions_by'),
            'suggestions_phone' => $this->input->post('suggestions_phone'),
            'suggestions_email' => $this->input->post('suggestions_email'),
            'suggestions_number' => $this->input->post('suggestions_number'), // *** เพิ่มใหม่ ***
            'suggestions_address' => $this->input->post('suggestions_address'),
            'guest_province' => $this->input->post('guest_province'), // *** เพิ่มใหม่ ***
            'guest_amphoe' => $this->input->post('guest_amphoe'), // *** เพิ่มใหม่ ***
            'guest_district' => $this->input->post('guest_district'), // *** เพิ่มใหม่ ***
            'guest_zipcode' => $this->input->post('guest_zipcode') // *** เพิ่มใหม่ ***
        );

        // ตั้งค่าการอัพโหลด
        $config['upload_path'] = './docs/img';
        $config['allowed_types'] = 'gif|jpg|png|jpeg|jfif';
        $config['max_size'] = '2048';
        $this->load->library('upload', $config);

        // เริ่ม Transaction
        $this->db->trans_start();

        // บันทึกข้อมูลหลัก
        $this->db->insert('tbl_suggestions', $suggestions_data);
        $suggestions_id = $this->db->insert_id();

        // อัพโหลดรูปภาพ (ถ้ามี)
        if (!empty($_FILES['suggestions_img_img']['name'][0])) {
            $image_data = array();
            foreach ($_FILES['suggestions_img_img']['name'] as $index => $name) {
                $_FILES['suggestions_img']['name'] = $name;
                $_FILES['suggestions_img']['type'] = $_FILES['suggestions_img_img']['type'][$index];
                $_FILES['suggestions_img']['tmp_name'] = $_FILES['suggestions_img_img']['tmp_name'][$index];
                $_FILES['suggestions_img']['error'] = $_FILES['suggestions_img_img']['error'][$index];
                $_FILES['suggestions_img']['size'] = $_FILES['suggestions_img_img']['size'][$index];

                // อัพโหลดไฟล์
                if (!$this->upload->do_upload('suggestions_img')) {
                    $this->db->trans_rollback();
                    $this->session->set_flashdata('save_maxsize', TRUE);
                    redirect('suggestions_backend/adding');
                    return;
                }

                $upload_data = $this->upload->data();

                // สร้างชื่อไฟล์สำหรับ Line
                $file_ext = pathinfo($upload_data['file_name'], PATHINFO_EXTENSION);
                $line_filename = 'line_' . time() . '_' . uniqid() . '.' . $file_ext;

                // คัดลอกไฟล์สำหรับ Line
                copy(
                    $upload_data['full_path'],
                    $config['upload_path'] . '/' . $line_filename
                );

                $image_data[] = array(
                    'suggestions_img_ref_id' => $suggestions_id,
                    'suggestions_img_img' => $upload_data['file_name'],
                    'suggestions_img_line' => $line_filename
                );
            }

            // บันทึกข้อมูลรูปภาพ
            if (!empty($image_data)) {
                $this->db->insert_batch('tbl_suggestions_img', $image_data);
            }
        }

        // จบ Transaction
        $this->db->trans_complete();

        // ดึงข้อมูลสำหรับส่ง Line
        $suggestionsData = $this->db->get_where('tbl_suggestions', array('suggestions_id' => $suggestions_id))->row();

        if ($suggestionsData) {
            // *** ปรับปรุงข้อความ Line ให้รองรับข้อมูลใหม่ ***
            $message = "รับฟังความคิดเห็น ใหม่ !" . "\n";
            $message .= "case: " . $suggestionsData->suggestions_id . "\n";
            $message .= "เรื่อง: " . $suggestionsData->suggestions_topic . "\n";
            $message .= "รายละเอียด: " . $suggestionsData->suggestions_detail . "\n";
            $message .= "ชื่อผู้อัพเดตข้อมูล: " . $suggestionsData->suggestions_by . "\n";
            $message .= "เบอร์โทรศัพท์ผู้แจ้ง: " . $suggestionsData->suggestions_phone . "\n";

            // *** เพิ่มข้อมูลเลขบัตรประชาชน (ถ้ามี) ***
            if (!empty($suggestionsData->suggestions_number)) {
                $message .= "เลขบัตรประชาชน: " . $suggestionsData->suggestions_number . "\n";
            }

            $message .= "ที่อยู่: " . $suggestionsData->suggestions_address . "\n";

            // *** เพิ่มข้อมูลที่อยู่แยก (ถ้ามี) ***
            if (!empty($suggestionsData->guest_province)) {
                $message .= "จังหวัด: " . $suggestionsData->guest_province . "\n";
            }
            if (!empty($suggestionsData->guest_district)) {
                $message .= "ตำบล: " . $suggestionsData->guest_district . "\n";
            }

            $message .= "อีเมล: " . $suggestionsData->suggestions_email . "\n";

            // ดึงรูปภาพทั้งหมด
            $images = $this->db->get_where(
                'tbl_suggestions_img',
                array('suggestions_img_ref_id' => $suggestions_id)
            )->result();

            if ($images) {
                $imagePaths = [];
                foreach ($images as $image) {
                    if (!empty($image->suggestions_img_line)) {
                        $imagePaths[] = './docs/img/' . $image->suggestions_img_line;
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
        }

        // อัพเดทพื้นที่
        $this->space_model->update_server_current();
        $this->session->set_flashdata('save_success', TRUE);

        return $suggestions_id;
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

        // เริ่มด้วยข้อความ
        $messages = [
            [
                'type' => 'text',
                'text' => $message
            ]
        ];

        // เพิ่มรูปภาพ (สูงสุด 5 รูป)
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
        $baseUrl = 'https://www.phuengdad.go.th/docs/img/';
        $fileName = basename($imagePath);
        return $baseUrl . $fileName;
    }

    public function get_suggestionss()
    {
        $this->db->select('*');
        $this->db->from('tbl_suggestions');
        return $this->db->get()->result();
    }

    public function get_images_for_suggestions($suggestions_id)
    {
        $this->db->select('suggestions_img_img');
        $this->db->from('tbl_suggestions_img');
        $this->db->where('suggestions_img_ref_id', $suggestions_id);
        return $this->db->get()->result();
    }

    public function read($suggestions_id)
    {
        $this->db->where('suggestions_id', $suggestions_id);
        $query = $this->db->get('tbl_suggestions');
        if ($query->num_rows() > 0) {
            $data = $query->row();
            return $data;
        }
        return FALSE;
    }

    public function read_detail($suggestions_id)
    {
        $this->db->where('suggestions_detail_case_id', $suggestions_id);
        $this->db->order_by('suggestions_detail_id', 'DESC');
        $query = $this->db->get('tbl_suggestions_detail');
        return $query->result();
    }

    public function dashboard_suggestions()
    {
        $this->db->select('*');
        $this->db->from('tbl_suggestions as c');
        $this->db->limit(3);
        $this->db->order_by('c.suggestions_id', 'DESC');
        $query = $this->db->get();
        return $query->result();
    }

    public function getLatestDetail($suggestions_id)
    {
        $this->db->select('*');
        $this->db->from('tbl_suggestions_detail');
        $this->db->where('suggestions_detail_case_id', $suggestions_id);
        $this->db->order_by('suggestions_detail_id', 'DESC');
        $this->db->limit(1);

        $query = $this->db->get();

        if ($query->num_rows() > 0) {
            return $query->row();
        }

        return null;
    }

    public function del($suggestions_id)
    {
        $this->db->delete('tbl_suggestions', array('suggestions_id' => $suggestions_id));
    }



    /**
     * ดึงสถิติข้อเสนอแนะแบบรวม (สำหรับ Dashboard)
     */
    public function get_suggestions_statistics()
    {
        try {
            $stats = [
                'total' => 0,
                'by_status' => [],
                'by_type' => [],
                'by_user_type' => [],
                'by_priority' => [],
                'recent_count' => 0,
                'today_count' => 0,
                'this_month_count' => 0
            ];

            // ตรวจสอบว่าตารางมีอยู่หรือไม่
            if (!$this->db->table_exists('tbl_suggestions')) {
                log_message('warning', 'Table tbl_suggestions does not exist');
                return $stats;
            }

            // 1. จำนวนรวมทั้งหมด
            $this->db->select('COUNT(*) as total');
            $this->db->from('tbl_suggestions');
            $total_result = $this->db->get()->row();
            $stats['total'] = $total_result ? (int) $total_result->total : 0;

            // 2. สถิติตามสถานะ
            $this->db->select('suggestions_status, COUNT(*) as count');
            $this->db->from('tbl_suggestions');
            $this->db->group_by('suggestions_status');
            $status_results = $this->db->get()->result();

            foreach ($status_results as $result) {
                $stats['by_status'][$result->suggestions_status] = (int) $result->count;
            }

            // 3. สถิติตามประเภท
            $this->db->select('suggestion_type, COUNT(*) as count');
            $this->db->from('tbl_suggestions');
            $this->db->where('suggestion_type IS NOT NULL');
            $this->db->group_by('suggestion_type');
            $type_results = $this->db->get()->result();

            foreach ($type_results as $result) {
                $stats['by_type'][$result->suggestion_type] = (int) $result->count;
            }

            // 4. สถิติตามประเภทผู้ใช้
            $this->db->select('suggestions_user_type, COUNT(*) as count');
            $this->db->from('tbl_suggestions');
            $this->db->group_by('suggestions_user_type');
            $user_type_results = $this->db->get()->result();

            foreach ($user_type_results as $result) {
                $stats['by_user_type'][$result->suggestions_user_type] = (int) $result->count;
            }

            // 5. สถิติตามความสำคัญ
            $this->db->select('suggestions_priority, COUNT(*) as count');
            $this->db->from('tbl_suggestions');
            $this->db->where('suggestions_priority IS NOT NULL');
            $this->db->group_by('suggestions_priority');
            $priority_results = $this->db->get()->result();

            foreach ($priority_results as $result) {
                $stats['by_priority'][$result->suggestions_priority] = (int) $result->count;
            }

            // 6. จำนวนวันนี้
            $this->db->select('COUNT(*) as today_count');
            $this->db->from('tbl_suggestions');
            $this->db->where('DATE(suggestions_datesave)', date('Y-m-d'));
            $today_result = $this->db->get()->row();
            $stats['today_count'] = $today_result ? (int) $today_result->today_count : 0;

            // 7. จำนวนเดือนนี้
            $this->db->select('COUNT(*) as month_count');
            $this->db->from('tbl_suggestions');
            $this->db->where('YEAR(suggestions_datesave)', date('Y'));
            $this->db->where('MONTH(suggestions_datesave)', date('m'));
            $month_result = $this->db->get()->row();
            $stats['this_month_count'] = $month_result ? (int) $month_result->month_count : 0;

            // 8. จำนวน 7 วันล่าสุด
            $this->db->select('COUNT(*) as recent_count');
            $this->db->from('tbl_suggestions');
            $this->db->where('suggestions_datesave >=', date('Y-m-d H:i:s', strtotime('-7 days')));
            $recent_result = $this->db->get()->row();
            $stats['recent_count'] = $recent_result ? (int) $recent_result->recent_count : 0;

            // 9. เพิ่มข้อมูลสำหรับ Dashboard Card
            $stats['dashboard_summary'] = [
                'total' => $stats['total'],
                'new' => $stats['by_status']['received'] ?? 0,
                'reviewed' => ($stats['by_status']['reviewing'] ?? 0) + ($stats['by_status']['replied'] ?? 0),
                'implemented' => $stats['by_status']['closed'] ?? 0,
                'pending' => $stats['by_status']['received'] ?? 0,
                'in_progress' => $stats['by_status']['reviewing'] ?? 0,
                'completed' => $stats['by_status']['closed'] ?? 0
            ];

            log_message('info', 'Suggestions statistics retrieved successfully: Total = ' . $stats['total']);

            return $stats;

        } catch (Exception $e) {
            log_message('error', 'Error getting suggestions statistics: ' . $e->getMessage());
            return [
                'total' => 0,
                'by_status' => [],
                'by_type' => [],
                'by_user_type' => [],
                'by_priority' => [],
                'recent_count' => 0,
                'today_count' => 0,
                'this_month_count' => 0,
                'dashboard_summary' => [
                    'total' => 0,
                    'new' => 0,
                    'reviewed' => 0,
                    'implemented' => 0,
                    'pending' => 0,
                    'in_progress' => 0,
                    'completed' => 0
                ]
            ];
        }
    }

    /**
     * ดึงสถิติแบบย่อสำหรับ Dashboard
     */
    public function get_dashboard_summary()
    {
        try {
            $full_stats = $this->get_suggestions_statistics();
            return $full_stats['dashboard_summary'];

        } catch (Exception $e) {
            log_message('error', 'Error getting dashboard summary: ' . $e->getMessage());
            return [
                'total' => 0,
                'new' => 0,
                'reviewed' => 0,
                'implemented' => 0
            ];
        }
    }

    /**
     * ดึงสถิติตามช่วงเวลา
     */
    public function get_suggestions_statistics_by_period($period = 'month')
    {
        try {
            $stats = [];

            switch ($period) {
                case 'today':
                    $this->db->where('DATE(suggestions_datesave)', date('Y-m-d'));
                    break;

                case 'week':
                    $this->db->where('suggestions_datesave >=', date('Y-m-d H:i:s', strtotime('-7 days')));
                    break;

                case 'month':
                    $this->db->where('YEAR(suggestions_datesave)', date('Y'));
                    $this->db->where('MONTH(suggestions_datesave)', date('m'));
                    break;

                case 'year':
                    $this->db->where('YEAR(suggestions_datesave)', date('Y'));
                    break;

                default:
                    // ทั้งหมด - ไม่มี WHERE condition
                    break;
            }

            // สถิติตามสถานะ
            $this->db->select('suggestions_status, COUNT(*) as count');
            $this->db->from('tbl_suggestions');
            $this->db->group_by('suggestions_status');
            $results = $this->db->get()->result();

            $total = 0;
            foreach ($results as $result) {
                $count = (int) $result->count;
                $stats[$result->suggestions_status] = $count;
                $total += $count;
            }

            $stats['total'] = $total;
            $stats['period'] = $period;
            $stats['generated_at'] = date('Y-m-d H:i:s');

            return $stats;

        } catch (Exception $e) {
            log_message('error', 'Error getting suggestions statistics by period: ' . $e->getMessage());
            return ['total' => 0, 'period' => $period];
        }
    }


    /**
     * ลบข้อเสนอแนะ (hard delete) - ปรับปรุงสำหรับ system_admin และ super_admin
     */
    public function delete_suggestion($suggestion_id, $deleted_by = 'System')
    {
        try {
            // ตรวจสอบว่าข้อเสนอแนะมีอยู่จริง
            $suggestion_data = $this->get_suggestion_by_id($suggestion_id);
            if (!$suggestion_data) {
                log_message('error', "Cannot delete: Suggestion not found - ID: {$suggestion_id}");
                return false;
            }

            $this->db->trans_start();

            // 1. บันทึกข้อมูลสำคัญก่อนลบ (สำหรับ log)
            $backup_data = [
                'suggestions_id' => $suggestion_data->suggestions_id,
                'suggestions_topic' => $suggestion_data->suggestions_topic,
                'suggestions_detail' => $suggestion_data->suggestions_detail,
                'suggestions_by' => $suggestion_data->suggestions_by,
                'suggestions_phone' => $suggestion_data->suggestions_phone,
                'suggestions_email' => $suggestion_data->suggestions_email,
                'suggestions_number' => $suggestion_data->suggestions_number,
                'suggestions_address' => $suggestion_data->suggestions_address,
                'suggestions_datesave' => $suggestion_data->suggestions_datesave,
                'suggestions_status' => $suggestion_data->suggestions_status,
                'suggestions_user_type' => $suggestion_data->suggestions_user_type,
                'deleted_at' => date('Y-m-d H:i:s'),
                'deleted_by' => $deleted_by
            ];

            log_message('info', "Starting deletion process for suggestion: {$suggestion_id} by {$deleted_by}");

            // 2. ลบไฟล์แนบ (กำหนดสถานะเป็น deleted)
            $this->db->where('suggestions_file_ref_id', $suggestion_id);
            $this->db->update('tbl_suggestions_files', [
                'suggestions_file_status' => 'deleted',
                'suggestions_file_uploaded_by' => $deleted_by . ' (DELETED)'
            ]);
            $files_affected = $this->db->affected_rows();

            if ($files_affected > 0) {
                log_message('info', "Marked {$files_affected} files as deleted for suggestion: {$suggestion_id}");
            }

            // 3. เพิ่มบันทึกสุดท้ายในประวัติก่อนลบประวัติทั้งหมด
            $final_history = [
                'suggestions_id' => $suggestion_id,
                'action_type' => 'hard_deleted',
                'action_description' => "ข้อเสนอแนะถูกลบออกจากระบบโดย {$deleted_by} - หัวข้อ: {$suggestion_data->suggestions_topic}",
                'action_by' => $deleted_by,
                'action_date' => date('Y-m-d H:i:s'),
                'old_status' => $suggestion_data->suggestions_status,
                'new_status' => 'deleted',
                'additional_data' => json_encode($backup_data, JSON_UNESCAPED_UNICODE)
            ];

            $this->db->insert('tbl_suggestions_history', $final_history);

            // 4. ลบประวัติการดำเนินการ
            $this->db->where('suggestions_id', $suggestion_id);
            $history_delete_result = $this->db->delete('tbl_suggestions_history');
            $history_affected = $this->db->affected_rows();

            if ($history_affected > 0) {
                log_message('info', "Deleted {$history_affected} history records for suggestion: {$suggestion_id}");
            }

            // 5. ลบไฟล์แนบ (hard delete)
            $this->db->where('suggestions_file_ref_id', $suggestion_id);
            $files_delete_result = $this->db->delete('tbl_suggestions_files');
            $files_hard_deleted = $this->db->affected_rows();

            if ($files_hard_deleted > 0) {
                log_message('info', "Hard deleted {$files_hard_deleted} file records for suggestion: {$suggestion_id}");
            }

            // 6. ลบข้อเสนอแนะหลัก
            $this->db->where('suggestions_id', $suggestion_id);
            $main_delete_result = $this->db->delete('tbl_suggestions');

            if ($this->db->affected_rows() === 0) {
                log_message('error', "Failed to delete main suggestion record: {$suggestion_id}");
                $this->db->trans_rollback();
                return false;
            }

            $this->db->trans_complete();

            if ($this->db->trans_status() === FALSE) {
                log_message('error', 'Transaction failed while deleting suggestion: ' . $suggestion_id);
                return false;
            }

            // 7. บันทึก log สำคัญ
            log_message('info', "✅ Suggestion successfully deleted: ID={$suggestion_id}, Topic='{$suggestion_data->suggestions_topic}', By={$deleted_by}");
            log_message('info', "📋 Deletion summary - Files: {$files_hard_deleted}, History: {$history_affected}, Main: 1");

            return true;

        } catch (Exception $e) {
            log_message('error', 'Error deleting suggestion: ' . $e->getMessage());
            log_message('error', 'Stack trace: ' . $e->getTraceAsString());

            // Rollback transaction if still active
            if ($this->db->trans_status() !== FALSE) {
                $this->db->trans_rollback();
            }

            return false;
        }
    }

    /**
     * ตรวจสอบสิทธิ์การลบข้อเสนอแนะ
     */
    public function can_delete_suggestion($user_system_level)
    {
        $allowed_systems = ['system_admin', 'super_admin'];
        return in_array($user_system_level, $allowed_systems);
    }

    /**
     * ดึงสถิติการลบข้อเสนอแนะ (จากประวัติที่เหลือ)
     */
    public function get_deletion_statistics($date_from = null, $date_to = null)
    {
        try {
            $this->db->select('action_by, COUNT(*) as delete_count, MAX(action_date) as last_delete');
            $this->db->from('tbl_suggestions_history');
            $this->db->where('action_type', 'hard_deleted');

            if ($date_from) {
                $this->db->where('DATE(action_date) >=', $date_from);
            }

            if ($date_to) {
                $this->db->where('DATE(action_date) <=', $date_to);
            }

            $this->db->group_by('action_by');
            $this->db->order_by('delete_count', 'DESC');

            $results = $this->db->get()->result();

            $total_deletions = 0;
            foreach ($results as $result) {
                $total_deletions += $result->delete_count;
            }

            return [
                'by_user' => $results,
                'total_deletions' => $total_deletions,
                'date_range' => [
                    'from' => $date_from,
                    'to' => $date_to
                ]
            ];

        } catch (Exception $e) {
            log_message('error', 'Error getting deletion statistics: ' . $e->getMessage());
            return [
                'by_user' => [],
                'total_deletions' => 0,
                'date_range' => ['from' => $date_from, 'to' => $date_to]
            ];
        }
    }

    /**
     * ตรวจสอบข้อมูลก่อนลบ
     */
    public function validate_before_delete($suggestion_id)
    {
        try {
            $validation = [
                'can_delete' => false,
                'reason' => '',
                'suggestion_data' => null,
                'related_data' => []
            ];

            // ตรวจสอบว่าข้อเสนอแนะมีอยู่จริง
            $suggestion = $this->get_suggestion_by_id($suggestion_id);
            if (!$suggestion) {
                $validation['reason'] = 'ไม่พบข้อเสนอแนะที่ระบุ';
                return $validation;
            }

            $validation['suggestion_data'] = $suggestion;

            // ตรวจสอบไฟล์แนบ
            $files = $this->get_suggestion_files($suggestion_id);
            if (!empty($files)) {
                $validation['related_data']['files'] = count($files);
            }

            // ตรวจสอบประวัติ
            $history = $this->get_suggestion_history($suggestion_id);
            if (!empty($history)) {
                $validation['related_data']['history_records'] = count($history);
            }

            // ตรวจสอบสถานะ
            if ($suggestion->suggestions_status === 'replied' || $suggestion->suggestions_status === 'closed') {
                $validation['reason'] = 'ข้อเสนอแนะนี้ได้รับการตอบกลับหรือปิดเรื่องแล้ว ควรพิจารณาอย่างรอบคอบก่อนลบ';
            }

            // อนุญาตให้ลบได้
            $validation['can_delete'] = true;
            if (empty($validation['reason'])) {
                $validation['reason'] = 'สามารถลบได้';
            }

            return $validation;

        } catch (Exception $e) {
            log_message('error', 'Error validating before delete: ' . $e->getMessage());
            return [
                'can_delete' => false,
                'reason' => 'เกิดข้อผิดพลาดในการตรวจสอบข้อมูล',
                'suggestion_data' => null,
                'related_data' => []
            ];
        }
    }

    /**
     * สำรองข้อมูลก่อนลบ (เก็บไว้ในตาราง log หรือไฟล์)
     */
    public function backup_before_delete($suggestion_id)
    {
        try {
            $suggestion = $this->get_suggestion_by_id($suggestion_id);
            if (!$suggestion) {
                return false;
            }

            // ดึงข้อมูลที่เกี่ยวข้องทั้งหมด
            $files = $this->get_suggestion_files($suggestion_id);
            $history = $this->get_suggestion_history($suggestion_id);

            $backup_data = [
                'backup_date' => date('Y-m-d H:i:s'),
                'suggestion' => $suggestion,
                'files' => $files,
                'history' => $history,
                'total_files' => count($files),
                'total_history' => count($history)
            ];

            // ตัวอย่าง: บันทึกลง log file
            $backup_json = json_encode($backup_data, JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT);
            $backup_filename = 'suggestion_backup_' . $suggestion_id . '_' . date('Y-m-d_H-i-s') . '.json';
            $backup_path = './logs/deleted_suggestions/';

            // สร้างโฟลเดอร์ถ้าไม่มี
            if (!is_dir($backup_path)) {
                mkdir($backup_path, 0755, true);
            }

            $result = file_put_contents($backup_path . $backup_filename, $backup_json);

            if ($result !== false) {
                log_message('info', "Suggestion backup created: {$backup_filename}");
                return $backup_filename;
            } else {
                log_message('error', "Failed to create backup for suggestion: {$suggestion_id}");
                return false;
            }

        } catch (Exception $e) {
            log_message('error', 'Error creating backup before delete: ' . $e->getMessage());
            return false;
        }
    }

    /**
     * ลบไฟล์แนบ (soft delete) - ปรับปรุงใหม่
     */
    public function delete_suggestion_file($file_id, $deleted_by = 'System')
    {
        try {
            // ตรวจสอบว่าไฟล์มีอยู่จริง
            $file_data = $this->db->get_where('tbl_suggestions_files', [
                'suggestions_file_id' => $file_id,
                'suggestions_file_status' => 'active'
            ])->row();

            if (!$file_data) {
                log_message('warning', "File not found or already deleted: {$file_id}");
                return false;
            }

            $update_data = [
                'suggestions_file_status' => 'deleted',
                'suggestions_file_uploaded_by' => $deleted_by . ' (DELETED)',
                'suggestions_file_uploaded_at' => date('Y-m-d H:i:s') // อัปเดตเวลาสำหรับการลบ
            ];

            $this->db->where('suggestions_file_id', $file_id);
            $result = $this->db->update('tbl_suggestions_files', $update_data);

            if ($result) {
                log_message('info', "Suggestion file soft deleted: ID = {$file_id} by {$deleted_by}");

                // ลองลบไฟล์จริงๆ จากเซิร์ฟเวอร์ (ถ้าต้องการ)
                if (!empty($file_data->suggestions_file_path) && file_exists($file_data->suggestions_file_path)) {
                    if (unlink($file_data->suggestions_file_path)) {
                        log_message('info', "Physical file deleted: {$file_data->suggestions_file_path}");
                    } else {
                        log_message('warning', "Failed to delete physical file: {$file_data->suggestions_file_path}");
                    }
                }

                return true;
            }

            return false;

        } catch (Exception $e) {
            log_message('error', 'Error deleting suggestion file: ' . $e->getMessage());
            return false;
        }
    }

    /**
     * เพิ่มฟังก์ชันสำหรับดูข้อมูลที่ถูกลบ (จากไฟล์ backup)
     */
    public function get_deleted_suggestions_from_backup($limit = 50)
    {
        try {
            $backup_path = './logs/deleted_suggestions/';
            $deleted_suggestions = [];

            if (!is_dir($backup_path)) {
                return $deleted_suggestions;
            }

            $files = glob($backup_path . 'suggestion_backup_*.json');

            // เรียงตามวันที่ล่าสุดก่อน
            usort($files, function ($a, $b) {
                return filemtime($b) - filemtime($a);
            });

            $count = 0;
            foreach ($files as $file) {
                if ($count >= $limit)
                    break;

                $backup_data = json_decode(file_get_contents($file), true);
                if ($backup_data && isset($backup_data['suggestion'])) {
                    $suggestion = $backup_data['suggestion'];
                    $deleted_suggestions[] = [
                        'suggestions_id' => $suggestion['suggestions_id'] ?? 'N/A',
                        'suggestions_topic' => $suggestion['suggestions_topic'] ?? 'N/A',
                        'suggestions_by' => $suggestion['suggestions_by'] ?? 'N/A',
                        'suggestions_datesave' => $suggestion['suggestions_datesave'] ?? 'N/A',
                        'backup_date' => $backup_data['backup_date'] ?? 'N/A',
                        'backup_file' => basename($file),
                        'total_files' => $backup_data['total_files'] ?? 0,
                        'total_history' => $backup_data['total_history'] ?? 0
                    ];
                    $count++;
                }
            }

            return $deleted_suggestions;

        } catch (Exception $e) {
            log_message('error', 'Error getting deleted suggestions from backup: ' . $e->getMessage());
            return [];
        }
    }


}