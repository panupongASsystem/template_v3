<?php
defined('BASEPATH') or exit('No direct script access allowed');

/**
 * LINE Notification Library
 * 
 * Library ที่ย้ายโค้ดจาก Complain_model มาใช้งาน
 * คงโครงสร้างและการทำงานเดิมไว้ทุกอย่าง
 * 
 * @package    CodeIgniter
 * @subpackage Libraries
 * @category   Communication
 */
class Line_notification
{
    private $CI;
    private $channelAccessToken;
    private $lineApiUrl;

    public function __construct()
    {
        $this->CI =& get_instance();

        // ใช้ helper function get_config_value เพื่อดึงค่า token จากฐานข้อมูล
        $this->channelAccessToken = get_config_value('line_token');
        $this->lineApiUrl = 'https://api.line.me/v2/bot/message/multicast';
    }

    /**
     * ส่งแจ้งเตือนเรื่องร้องเรียนใหม่ Complain
     * ย้ายมาจาก send_line_notification() ใน Complain_model
     */
    public function send_line_complain_notification($complain_id)
    {
        try {
            $complainData = $this->CI->db->get_where('tbl_complain', array('complain_id' => $complain_id))->row();

            if ($complainData) {
                $message = "เรื่องร้องเรียน ใหม่ !\n";
                $message .= "case: " . $complainData->complain_id . "\n";
                $message .= "สถานะ: " . ($complainData->complain_status ?: 'รอรับเรื่อง') . "\n";
                $message .= "เรื่อง: " . $complainData->complain_topic . "\n";
                $message .= "รายละเอียด: " . $complainData->complain_detail . "\n";
                $message .= "ผู้แจ้งเรื่อง: " . $complainData->complain_by . "\n";
                $message .= "เบอร์โทรศัพท์ผู้แจ้ง: " . $complainData->complain_phone . "\n";
                $message .= "ที่อยู่: " . $complainData->complain_address . ' ' . $complainData->guest_district . ' ' . $complainData->guest_amphoe . ' ' . $complainData->guest_province . ' ' . $complainData->guest_zipcode . "\n";
                $message .= "อีเมล: " . ($complainData->complain_email ?: 'ไม่ระบุ') . "\n";
                $message .= "ประเภทผู้ใช้: " . $complainData->complain_user_type . "\n";

                if ($complainData->complain_user_type === 'anonymous') {
                    $message .= "⚠️ แจ้งแบบไม่ระบุตัวตน\n";
                }

                $images = $this->CI->db->get_where(
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
     * ส่งแจ้งเตือนการอัปเดตสถานะ Complain
     * ปรับปรุงให้ตรงกับโครงสร้าง database และมี error handling ที่ดีขึ้น
     */
    public function send_line_complain_update_notification($complain_id, $detail_comment = null)
    {
        try {
            if (empty($complain_id)) {
                log_message('error', 'Complain ID is required for LINE notification');
                return false;
            }

            // *** 1. ดึงข้อมูลจาก tbl_complain ***
            $this->CI->db->select('
            complain_id, complain_status, complain_type, complain_topic, 
            complain_detail, complain_by, complain_phone, complain_email, 
            complain_address, complain_user_type, complain_datesave, 
            complain_dateupdate
        ');
            $complainData = $this->CI->db->get_where('tbl_complain', array('complain_id' => $complain_id))->row();

            if (!$complainData) {
                log_message('error', "Complain not found for LINE notification: {$complain_id}");
                return false;
            }

            // *** 2. สร้างข้อความหลัก ***
            $message = "เรื่องร้องเรียน อัพเดต!" . "\n";
            $message .= "หมายเลข: " . $complainData->complain_id . "\n";
            $message .= "สถานะ: " . $complainData->complain_status . "\n";
            $message .= "ประเภท: " . $complainData->complain_type . "\n";
            $message .= "เรื่อง: " . $complainData->complain_topic . "\n";
            $message .= "รายละเอียด: " . $this->truncate_text($complainData->complain_detail, 100) . "\n";
            $message .= "ผู้แจ้ง: " . $complainData->complain_by . "\n";
            $message .= "เบอร์: " . $complainData->complain_phone . "\n";
            $message .= "อีเมล: " . ($complainData->complain_email ?: 'ไม่ระบุ') . "\n";
            $message .= "ที่อยู่: " . $this->truncate_text($complainData->complain_address, 50) . "\n";

            // แสดงประเภทผู้ใช้
            $user_type_text = $this->get_user_type_text($complainData->complain_user_type);
            $message .= "ประเภทผู้ใช้: " . $user_type_text . "\n";

            // แสดงเวลาที่อัพเดต
            if ($complainData->complain_dateupdate) {
                $update_time = date('d/m/Y H:i', strtotime($complainData->complain_dateupdate));
                $message .= "อัพเดตเมื่อ: " . $update_time . "\n";
            }

            // *** 3. ดึงข้อมูลการอัพเดตล่าสุดจาก tbl_complain_detail ***
            if ($this->CI->db->table_exists('tbl_complain_detail')) {
                $this->CI->db->select('
                complain_detail_by, complain_detail_com, complain_detail_status,
                complain_detail_datesave
            ');
                $this->CI->db->where('complain_detail_case_id', $complain_id);
                $this->CI->db->order_by('complain_detail_id', 'DESC');
                $this->CI->db->limit(1);
                $latestDetail = $this->CI->db->get('tbl_complain_detail')->row();

                if ($latestDetail) {
                    $message .= "การอัพเดตล่าสุด:\n";
                    $message .= "โดย: " . ($latestDetail->complain_detail_by ?: 'ไม่ระบุ') . "\n";

                    if (!empty($latestDetail->complain_detail_com)) {
                        $message .= "หมายเหตุ: " . $this->truncate_text($latestDetail->complain_detail_com, 150) . "\n";
                    }

                    if ($latestDetail->complain_detail_datesave) {
                        $detail_time = date('d/m/Y H:i', strtotime($latestDetail->complain_detail_datesave));
                        $message .= "เวลา: " . $detail_time . "\n";
                    }
                }
            }

            // *** 4. เพิ่มข้อความเสริมถ้ามี ***
            if ($detail_comment) {
                $message .= "หมายเหตุเพิ่มเติม: " . $detail_comment . "\n";
            }


            // *** 5. ส่งข้อความแบบ broadcast ***
            $result = $this->broadcastLineOAMessage($message);

            if ($result) {
                log_message('info', "LINE update notification sent successfully for complain_id: {$complain_id}");
            } else {
                log_message('error', "Failed to send LINE update notification for complain_id: {$complain_id}");
            }

            return $result;

        } catch (Exception $e) {
            log_message('error', "Exception in send_line_complain_update_notification: " . $e->getMessage());
            return false;
        }
    }

    /**
     * แปลงประเภทผู้ใช้เป็นข้อความที่อ่านง่าย
     */
    private function get_user_type_text($user_type)
    {
        switch ($user_type) {
            case 'public':
                return 'สมาชิกสาธารณะ';
            case 'staff':
                return 'เจ้าหน้าที่';
            case 'guest':
                return 'ผู้เยี่ยมชม';
            case 'anonymous':
                return 'ไม่ระบุตัวตน';
            default:
                return 'ไม่ระบุ';
        }
    }

    /**
     * ตัดข้อความให้สั้นลงถ้ายาวเกินไป
     */
    private function truncate_text($text, $max_length = 100)
    {
        if (empty($text)) {
            return 'ไม่ระบุ';
        }

        if (mb_strlen($text, 'UTF-8') <= $max_length) {
            return $text;
        }

        return mb_substr($text, 0, $max_length, 'UTF-8') . '...';
    }

    /**
     * อัพเดตสถานะ Complain พร้อม timestamp
     * ใช้ร่วมกับการส่ง LINE notification
     */
    public function update_complain_status_with_notification($complain_id, $new_status, $comment = null, $updated_by = null)
    {
        try {
            if (!$this->CI->db->table_exists('tbl_complain')) {
                return false;
            }

            // *** 1. อัพเดต tbl_complain ***
            $update_data = [
                'complain_status' => $new_status,
                'complain_dateupdate' => date('Y-m-d H:i:s')
            ];

            $this->CI->db->where('complain_id', $complain_id);
            $update_result = $this->CI->db->update('tbl_complain', $update_data);

            if (!$update_result) {
                log_message('error', "Failed to update complain status: {$complain_id}");
                return false;
            }

            // *** 2. เพิ่มข้อมูลใน tbl_complain_detail ถ้ามี comment ***
            if ($comment && $this->CI->db->table_exists('tbl_complain_detail')) {
                $detail_data = [
                    'complain_detail_case_id' => $complain_id,
                    'complain_detail_status' => $new_status,
                    'complain_detail_com' => $comment,
                    'complain_detail_by' => $updated_by ?: 'ระบบ',
                    'complain_detail_datesave' => date('Y-m-d H:i:s')
                ];

                $this->CI->db->insert('tbl_complain_detail', $detail_data);
            }

            // *** 3. ส่ง LINE notification ***
            $this->send_line_complain_update_notification($complain_id, $comment);

            log_message('info', "Complain status updated successfully: {$complain_id} -> {$new_status}");
            return true;

        } catch (Exception $e) {
            log_message('error', "Error updating complain status: " . $e->getMessage());
            return false;
        }
    }

    /**
     * ส่งข้อความ broadcast ไปยังผู้ใช้ทุกคน
     * ย้ายมาจาก broadcastLineOAMessage() ใน Complain_model
     */
    public function broadcastLineOAMessage($message, $imagePaths = null)
    {
        $userIds = $this->CI->db->select('line_user_id')
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

    /**
     * แปลง path รูปภาพเป็น URL
     * ย้ายมาจาก uploadImageToLine() ใน Complain_model
     */
    private function uploadImageToLine($imagePath)
    {
        $fileName = basename($imagePath);
        return base_url('docs/img/' . $fileName);
    }


    /**
     * ส่งแจ้งเตือนเอกสาร ESV ODS ใหม่
     * อิงตามโครงสร้าง tbl_esv_ods (ไม่มี tbl_esv_files)
     */
    public function send_line_esv_ods_notification($esv_ods_id)
    {
        try {
            log_message('info', 'Starting ESV ODS line notification for ID: ' . $esv_ods_id);

            $esvData = $this->CI->db->get_where('tbl_esv_ods', array('esv_ods_id' => $esv_ods_id))->row();

            if ($esvData) {
                log_message('info', 'ESV ODS data found for notification');

                $message = "ระบบยื่นเอกสารออนไลน์\n";
                $message .= "รหัสอ้างอิง: " . ($esvData->esv_ods_reference_id ?: 'ไม่ระบุ') . "\n";
                $message .= "สถานะ: " . $this->getEsvStatusText($esvData->esv_ods_status) . "\n";
                $message .= "ระดับความเร่งด่วน: " . $this->getEsvPriorityText($esvData->esv_ods_priority) . "\n";
                $message .= "หัวข้อ: " . $esvData->esv_ods_topic . "\n";
                $message .= "รายละเอียด: " . $esvData->esv_ods_detail . "\n";
                $message .= "ผู้ส่ง: " . $esvData->esv_ods_by . "\n";
                $message .= "เบอร์โทรศัพท์: " . $esvData->esv_ods_phone . "\n";
                $message .= "อีเมล: " . ($esvData->esv_ods_email ?: 'ไม่ระบุ') . "\n";
                $message .= "ที่อยู่: " . $esvData->esv_ods_address . "\n";
                $message .= "ประเภทผู้ใช้: " . $esvData->esv_ods_user_type . "\n";

                // เพิ่มข้อมูลแผนกและหมวดหมู่ถ้ามี
                if (!empty($esvData->esv_ods_department_other)) {
                    $message .= "แผนก: " . $esvData->esv_ods_department_other . "\n";
                }

                if (!empty($esvData->esv_ods_category_other)) {
                    $message .= "หมวดหมู่: " . $esvData->esv_ods_category_other . "\n";
                }

                // แสดงข้อมูลเพิ่มเติมสำหรับความเร่งด่วน
                if ($esvData->esv_ods_priority === 'urgent') {
                    $message .= "🔥 เอกสารด่วน!\n";
                } elseif ($esvData->esv_ods_priority === 'very_urgent') {
                    $message .= "🚨 เอกสารด่วนมาก!\n";
                }

                // ตรวจสอบว่ามี method broadcastLineOAMessage หรือไม่
                if (method_exists($this, 'broadcastLineOAMessage')) {
                    log_message('info', 'Sending LINE notification message');
                    $this->broadcastLineOAMessage($message);
                    log_message('info', 'Line ESV ODS notification sent successfully for esv_ods_id: ' . $esv_ods_id);
                } else {
                    log_message('error', 'Method broadcastLineOAMessage not found');
                }

            } else {
                log_message('warning', 'ESV ODS data not found for ID: ' . $esv_ods_id);
            }
        } catch (Exception $e) {
            log_message('error', 'Failed to send Line ESV ODS notification: ' . $e->getMessage());
            log_message('error', 'Exception trace: ' . $e->getTraceAsString());
        }
    }

    /**
     * แปลงสถานะ ESV เป็นข้อความภาษาไทย
     */
    private function getEsvStatusText($status)
    {
        switch ($status) {
            case 'pending':
                return 'รอดำเนินการ';
            case 'processing':
                return 'กำลังดำเนินการ';
            case 'completed':
                return 'ดำเนินการเรียบร้อย';
            case 'rejected':
                return 'ปฏิเสธ';
            case 'cancelled':
                return 'ยกเลิก';
            default:
                return $status;
        }
    }

    /**
     * แปลงระดับความเร่งด่วนเป็นข้อความภาษาไทย
     */
    private function getEsvPriorityText($priority)
    {
        switch ($priority) {
            case 'normal':
                return 'ปกติ';
            case 'urgent':
                return 'ด่วน';
            case 'very_urgent':
                return 'ด่วนมาก';
            default:
                return $priority;
        }
    }

    /**
     * ส่งแจ้งเตือนการจองคิวใหม่
     * อิงตามโครงสร้าง tbl_queue
     */
    public function send_line_queue_notification($queue_id)
    {
        try {
            log_message('info', 'Starting Queue line notification for ID: ' . $queue_id);

            $queueData = $this->CI->db->get_where('tbl_queue', array('queue_id' => $queue_id))->row();

            if ($queueData) {
                log_message('info', 'Queue data found for notification');

                $message = $this->build_queue_message($queueData);

                // ตรวจสอบว่ามี method broadcastLineOAMessage หรือไม่
                if (method_exists($this, 'broadcastLineOAMessage')) {
                    log_message('info', 'Sending LINE queue notification message');
                    $result = $this->broadcastLineOAMessage($message);

                    if ($result) {
                        log_message('info', 'Line Queue notification sent successfully for queue_id: ' . $queue_id);
                    } else {
                        log_message('error', 'Failed to send LINE queue notification for queue_id: ' . $queue_id);
                    }

                    return $result;
                } else {
                    log_message('error', 'Method broadcastLineOAMessage not found');
                    return false;
                }

            } else {
                log_message('warning', 'Queue data not found for ID: ' . $queue_id);
                return false;
            }
        } catch (Exception $e) {
            log_message('error', 'Failed to send Line Queue notification: ' . $e->getMessage());
            log_message('error', 'Exception trace: ' . $e->getTraceAsString());
            return false;
        }
    }

    /**
     * สร้างข้อความ LINE notification สำหรับการจองคิว
     * @param object $queueData ข้อมูลจาก tbl_queue
     * @return string ข้อความที่จะส่ง
     */
    private function build_queue_message($queueData)
    {
        log_message('info', 'Building queue notification message for: ' . $queueData->queue_id);

        $message = "การจองคิวใหม่!\n";
        $message .= "หมายเลขคิว: " . $queueData->queue_id . "\n";
        $message .= "สถานะ: " . $this->getQueueStatusText($queueData->queue_status) . "\n";
        $message .= "หัวข้อ: " . $queueData->queue_topic . "\n";
        $message .= "รายละเอียด: " . $this->truncate_text($queueData->queue_detail, 100) . "\n";

        // ข้อมูลผู้จอง
        $message .= "ผู้จอง: " . $queueData->queue_by . "\n";
        $message .= "เบอร์โทร: " . $queueData->queue_phone . "\n";
        $message .= "เลขบัตรประชาชน: " . $this->format_citizen_id($queueData->queue_number) . "\n";

        // อีเมลสำหรับ guest
        if (!empty($queueData->queue_email)) {
            $message .= "อีเมล: " . $queueData->queue_email . "\n";
        }

        // ที่อยู่
        $address = $this->build_queue_address($queueData);
        if (!empty($address)) {
            $message .= "ที่อยู่: " . $address . "\n";
        }

        // ประเภทผู้ใช้
        $user_type_text = $this->get_queue_user_type_text($queueData->queue_user_type);
        $message .= "ประเภทผู้ใช้: " . $user_type_text . "\n";

        // วันที่และเวลานัดหมาย
        if (!empty($queueData->queue_date)) {
            $appointment_date = date('d/m/Y', strtotime($queueData->queue_date));
            $appointment_time = date('H:i', strtotime($queueData->queue_date));
            $message .= "วันที่นัดหมาย: " . $appointment_date . "\n";
            $message .= "เวลานัดหมาย: " . $appointment_time . " น.\n";

            // ช่วงเวลาที่เลือก
            if (!empty($queueData->queue_time_slot)) {
                $message .= "ช่วงเวลา: " . $queueData->queue_time_slot . " น.\n";
            }
        }

        // ข้อมูลเพิ่มเติม
        $created_time = date('d/m/Y H:i', strtotime($queueData->queue_create));
        $message .= "จองเมื่อ: " . $created_time . " น.\n";

        // แสดง IP Address สำหรับ security tracking
        if (!empty($queueData->queue_ip_address)) {
            $message .= "IP: " . $queueData->queue_ip_address . "\n";
        }

        // เพิ่ม status badge ตามสถานะ
        $status_badge = $this->getQueueStatusBadge($queueData->queue_status);
        if (!empty($status_badge)) {
            $message .= $status_badge . "\n";
        }

        $message .= "ติดตามสถานะได้ที่ระบบจองคิว\n";

        log_message('info', 'Queue message built successfully');
        return $message;
    }

    /**
     * สร้างข้อความที่อยู่จากข้อมูลคิว
     * @param object $queueData ข้อมูลจาก tbl_queue
     * @return string ที่อยู่ที่จัดรูปแบบแล้ว
     */
    private function build_queue_address($queueData)
    {
        $address_parts = array();

        // ที่อยู่หลัก
        if (!empty($queueData->queue_address)) {
            $address_parts[] = $queueData->queue_address;
        }

        // ตำบล อำเภอ จังหวัด (สำหรับ guest)
        if (!empty($queueData->guest_district)) {
            $address_parts[] = "ตำบล" . $queueData->guest_district;
        }

        if (!empty($queueData->guest_amphoe)) {
            $address_parts[] = "อำเภอ" . $queueData->guest_amphoe;
        }

        if (!empty($queueData->guest_province)) {
            $address_parts[] = "จังหวัด" . $queueData->guest_province;
        }

        if (!empty($queueData->guest_zipcode) && $queueData->guest_zipcode !== '00000') {
            $address_parts[] = $queueData->guest_zipcode;
        }

        $full_address = implode(' ', $address_parts);
        return $this->truncate_text($full_address, 80);
    }

    /**
     * จัดรูปแบบเลขบัตรประชาชนให้แสดงบางส่วน (เพื่อความปลอดภัย)
     * @param string $citizen_id เลขบัตรประชาชน
     * @return string เลขบัตรที่ถูก mask
     */
    private function format_citizen_id($citizen_id)
    {
        if (empty($citizen_id) || strlen($citizen_id) < 10) {
            return 'ไม่ระบุ';
        }

        // แสดงเฉพาะ 4 หลักแรกและ 2 หลักสุดท้าย เพื่อความปลอดภัย
        if (strlen($citizen_id) === 13) {
            return substr($citizen_id, 0, 4) . 'xxxxx' . substr($citizen_id, -2);
        }

        return substr($citizen_id, 0, 3) . 'xxx' . substr($citizen_id, -2);
    }

    /**
     * แปลงสถานะคิวเป็นข้อความภาษาไทย
     * @param string $status สถานะจาก database
     * @return string ข้อความสถานะภาษาไทย
     */
    private function getQueueStatusText($status)
    {
        switch ($status) {
            case 'รอยืนยันการจอง':
                return 'รอยืนยันการจอง';
            case 'รับเรื่องพิจารณา':
                return 'รับเรื่องพิจารณา';
            case 'อนุมัติการจอง':
                return 'อนุมัติการจอง';
            case 'ยกเลิกการจอง':
                return 'ยกเลิกการจอง';
            case 'เสร็จสิ้น':
                return 'เสร็จสิ้น';
            case 'ไม่มารับบริการ':
                return 'ไม่มารับบริการ';
            default:
                return $status;
        }
    }

    /**
     * แปลงประเภทผู้ใช้คิวเป็นข้อความที่อ่านง่าย
     * @param string $user_type ประเภทผู้ใช้
     * @return string ข้อความประเภทผู้ใช้
     */
    private function get_queue_user_type_text($user_type)
    {
        switch ($user_type) {
            case 'public':
                return 'สมาชิกสาธารณะ';
            case 'staff':
                return 'เจ้าหน้าที่';
            case 'guest':
                return 'ผู้เยี่ยมชม';
            default:
                return 'ไม่ระบุ';
        }
    }

    /**
     * สร้าง status badge ตามสถานะคิว
     * @param string $status สถานะคิว
     * @return string emoji badge
     */
    private function getQueueStatusBadge($status)
    {
        switch ($status) {
            case 'รอยืนยันการจอง':
                return 'รอการยืนยัน';
            case 'รับเรื่องพิจารณา':
                return 'อยู่ระหว่างพิจารณา';
            case 'อนุมัติการจอง':
                return 'อนุมัติแล้ว - เตรียมมารับบริการ';
            case 'ยกเลิกการจอง':
                return 'การจองถูกยกเลิก';
            case 'เสร็จสิ้น':
                return 'เสร็จสิ้นการให้บริการ';
            case 'ไม่มารับบริการ':
                return 'ไม่มารับบริการตามนัด';
            default:
                return '';
        }
    }


    /**
     * ส่งแจ้งเตือนความคิดเห็น/ข้อเสนอแนะใหม่
     * อิงตามโครงสร้าง tbl_suggestions
     */
    public function send_line_suggestions_notification($suggestions_id)
    {
        try {
            log_message('info', 'Starting Suggestions line notification for ID: ' . $suggestions_id);

            $suggestionsData = $this->CI->db->get_where('tbl_suggestions', array('suggestions_id' => $suggestions_id))->row();

            if ($suggestionsData) {
                log_message('info', 'Suggestions data found for notification');

                $message = $this->build_suggestions_message($suggestionsData);

                // ตรวจสอบว่ามี method broadcastLineOAMessage หรือไม่
                if (method_exists($this, 'broadcastLineOAMessage')) {
                    log_message('info', 'Sending LINE suggestions notification message');
                    $result = $this->broadcastLineOAMessage($message);

                    if ($result) {
                        log_message('info', 'Line Suggestions notification sent successfully for suggestions_id: ' . $suggestions_id);
                    } else {
                        log_message('error', 'Failed to send LINE suggestions notification for suggestions_id: ' . $suggestions_id);
                    }

                    return $result;
                } else {
                    log_message('error', 'Method broadcastLineOAMessage not found');
                    return false;
                }

            } else {
                log_message('warning', 'Suggestions data not found for ID: ' . $suggestions_id);
                return false;
            }
        } catch (Exception $e) {
            log_message('error', 'Failed to send Line Suggestions notification: ' . $e->getMessage());
            log_message('error', 'Exception trace: ' . $e->getTraceAsString());
            return false;
        }
    }

    /**
     * สร้างข้อความ LINE notification สำหรับความคิดเห็น/ข้อเสนอแนะ
     * @param object $suggestionsData ข้อมูลจาก tbl_suggestions
     * @return string ข้อความที่จะส่ง
     */
    private function build_suggestions_message($suggestionsData)
    {
        log_message('info', 'Building suggestions notification message for: ' . $suggestionsData->suggestions_id);

        // สร้างหัวข้อข้อความตามประเภท
        $type_text = $this->getSuggestionTypeText($suggestionsData->suggestion_type);
        $message = "รับฟังความคิดเห็นของ " . $type_text . " ใหม่!\n";

        // ข้อมูลพื้นฐาน
        $message .= "รหัส: " . $suggestionsData->suggestions_id . "\n";
        $message .= "ประเภท: " . $type_text . "\n";
        $message .= "สถานะ: " . $this->getSuggestionStatusText($suggestionsData->suggestions_status) . "\n";
        $message .= "ระดับความสำคัญ: " . $this->getSuggestionPriorityText($suggestionsData->suggestions_priority) . "\n";

        // หมวดหมู่ถ้ามี
        if (!empty($suggestionsData->suggestions_category)) {
            $message .= "หมวดหมู่: " . $suggestionsData->suggestions_category . "\n";
        }


        // เนื้อหา
        $message .= "หัวข้อ: " . $suggestionsData->suggestions_topic . "\n";
        $message .= "รายละเอียด: " . $this->truncate_text($suggestionsData->suggestions_detail, 150) . "\n";


        // ข้อมูลผู้ส่ง
        if ($suggestionsData->suggestions_is_anonymous == 1) {
            $message .= "ผู้ส่ง: ไม่ระบุตัวตน\n";
            $message .= "เบอร์: ไม่ระบุ\n";
            $message .= "อีเมล: ไม่ระบุ\n";
            $message .= "ที่อยู่: ไม่ระบุ\n";
            $message .= "แจ้งแบบไม่ระบุตัวตน\n";
        } else {
            $message .= "ผู้ส่ง: " . $suggestionsData->suggestions_by . "\n";
            $message .= "เบอร์: " . $suggestionsData->suggestions_phone . "\n";
            $message .= "อีเมล: " . ($suggestionsData->suggestions_email ?: 'ไม่ระบุ') . "\n";

            // ข้อมูลที่อยู่
            $address = $this->build_suggestions_address($suggestionsData);
            if (!empty($address)) {
                $message .= "ที่อยู่: " . $address . "\n";
            }

            // เลขบัตรประชาชนถ้ามี (แสดงแบบ mask)
            if (!empty($suggestionsData->suggestions_number)) {
                $masked_id = $this->format_citizen_id($suggestionsData->suggestions_number);
                $message .= "บัตรประชาชน: " . $masked_id . "\n";
            }
        }

        // ประเภทผู้ใช้
        $user_type_text = $this->get_suggestions_user_type_text($suggestionsData->suggestions_user_type);
        $message .= "ประเภทผู้ใช้: " . $user_type_text . "\n";

        // ข้อมูลเวลา
        $created_time = date('d/m/Y H:i', strtotime($suggestionsData->suggestions_datesave));
        $message .= "ส่งเมื่อ: " . $created_time . " น.\n";

        // ถ้ามีการตอบกลับแล้ว
        if (!empty($suggestionsData->suggestions_reply)) {
            $message .= "มีการตอบกลับแล้ว โดย: " . ($suggestionsData->suggestions_replied_by ?: 'เจ้าหน้าที่') . "\n";
            if ($suggestionsData->suggestions_replied_at) {
                $replied_time = date('d/m/Y H:i', strtotime($suggestionsData->suggestions_replied_at));
                $message .= "ตอบเมื่อ: " . $replied_time . " น.\n";
            }
        }

        // แสดง IP Address สำหรับ security tracking
        if (!empty($suggestionsData->suggestions_ip_address)) {
            $message .= "IP: " . $suggestionsData->suggestions_ip_address . "\n";
        }

        // เพิ่ม badge ตามสถานะและความสำคัญ
        $status_badge = $this->getSuggestionStatusBadge($suggestionsData->suggestions_status, $suggestionsData->suggestions_priority);
        if (!empty($status_badge)) {
            $message .= $status_badge . "\n";
        }

        log_message('info', 'Suggestions message built successfully');
        return $message;
    }

    /**
     * สร้างข้อความที่อยู่จากข้อมูลความคิดเห็น
     * @param object $suggestionsData ข้อมูลจาก tbl_suggestions
     * @return string ที่อยู่ที่จัดรูปแบบแล้ว
     */
    private function build_suggestions_address($suggestionsData)
    {
        $address_parts = array();

        // ที่อยู่หลัก
        if (!empty($suggestionsData->suggestions_address)) {
            $address_parts[] = $suggestionsData->suggestions_address;
        }

        // ตำบล อำเภอ จังหวัด (สำหรับ guest)
        if (!empty($suggestionsData->guest_district)) {
            $address_parts[] = "ตำบล" . $suggestionsData->guest_district;
        }

        if (!empty($suggestionsData->guest_amphoe)) {
            $address_parts[] = "อำเภอ" . $suggestionsData->guest_amphoe;
        }

        if (!empty($suggestionsData->guest_province)) {
            $address_parts[] = "จังหวัด" . $suggestionsData->guest_province;
        }

        if (!empty($suggestionsData->guest_zipcode) && $suggestionsData->guest_zipcode !== '00000') {
            $address_parts[] = $suggestionsData->guest_zipcode;
        }

        $full_address = implode(' ', $address_parts);
        return $this->truncate_text($full_address, 80);
    }

    /**
     * แปลงประเภทความคิดเห็นเป็นข้อความภาษาไทย
     * @param string $type ประเภทจาก database
     * @return string ข้อความประเภทภาษาไทย
     */
    private function getSuggestionTypeText($type)
    {
        switch ($type) {
            case 'suggestion':
                return 'ข้อเสนอแนะ';
            case 'feedback':
                return 'ความคิดเห็น';
            case 'improvement':
                return 'ข้อเสนอปรับปรุง';
            default:
                return $type;
        }
    }

    /**
     * แปลงสถานะความคิดเห็นเป็นข้อความภาษาไทย
     * @param string $status สถานะจาก database
     * @return string ข้อความสถานะภาษาไทย
     */
    private function getSuggestionStatusText($status)
    {
        switch ($status) {
            case 'received':
                return 'ได้รับแล้ว';
            case 'reviewing':
                return 'กำลังพิจารณา';
            case 'replied':
                return 'ตอบกลับแล้ว';
            case 'closed':
                return 'ปิดการติดตาม';
            default:
                return $status;
        }
    }

    /**
     * แปลงระดับความสำคัญเป็นข้อความภาษาไทย
     * @param string $priority ระดับความสำคัญจาก database
     * @return string ข้อความระดับความสำคัญภาษาไทย
     */
    private function getSuggestionPriorityText($priority)
    {
        switch ($priority) {
            case 'low':
                return 'ต่ำ';
            case 'normal':
                return 'ปกติ';
            case 'high':
                return 'สูง';
            case 'urgent':
                return 'เร่งด่วน';
            default:
                return $priority;
        }
    }

    /**
     * แปลงประเภทผู้ใช้ความคิดเห็นเป็นข้อความที่อ่านง่าย
     * @param string $user_type ประเภทผู้ใช้
     * @return string ข้อความประเภทผู้ใช้
     */
    private function get_suggestions_user_type_text($user_type)
    {
        switch ($user_type) {
            case 'public':
                return 'สมาชิกสาธารณะ';
            case 'staff':
                return 'เจ้าหน้าที่';
            case 'guest':
                return 'ผู้เยี่ยมชม';
            default:
                return 'ไม่ระบุ';
        }
    }

    /**
     * สร้าง status badge ตามสถานะและความสำคัญ
     * @param string $status สถานะ
     * @param string $priority ระดับความสำคัญ
     * @return string emoji badge
     */
    private function getSuggestionStatusBadge($status, $priority)
    {
        $badge = '';

        // Badge ตามสถานะ
        switch ($status) {
            case 'received':
                $badge .= 'ได้รับข้อมูลแล้ว - รอการพิจารณา';
                break;
            case 'reviewing':
                $badge .= 'อยู่ระหว่างการพิจารณา';
                break;
            case 'replied':
                $badge .= 'ตอบกลับแล้ว';
                break;
            case 'closed':
                $badge .= 'ปิดการติดตาม';
                break;
        }

        // เพิ่ม badge ตามความสำคัญ
        switch ($priority) {
            case 'urgent':
                $badge .= 'เร่งด่วน!';
                break;
            case 'high':
                $badge .= 'ความสำคัญสูง';
                break;
        }

        return $badge;
    }



    /**
     * LINE Notification Methods for Corruption Reports
     * เพิ่มใน Line_notification.php library
     */

    /**
     * ส่งแจ้งเตือนรายงานการทุจริตใหม่
     * อิงตามโครงสร้าง tbl_corruption_reports
     */
    public function send_line_corruption_notification($corruption_id)
    {
        try {
            log_message('info', 'Starting Corruption line notification for ID: ' . $corruption_id);

            $corruptionData = $this->CI->db->get_where('tbl_corruption_reports', array('corruption_id' => $corruption_id))->row();

            if ($corruptionData) {
                log_message('info', 'Corruption data found for notification');

                $message = $this->build_corruption_message($corruptionData);

                // ตรวจสอบว่ามี method broadcastLineOAMessage หรือไม่
                if (method_exists($this, 'broadcastLineOAMessage')) {
                    log_message('info', 'Sending LINE corruption notification message');
                    $result = $this->broadcastLineOAMessage($message);

                    if ($result) {
                        log_message('info', 'Line Corruption notification sent successfully for corruption_id: ' . $corruption_id);
                    } else {
                        log_message('error', 'Failed to send LINE corruption notification for corruption_id: ' . $corruption_id);
                    }

                    return $result;
                } else {
                    log_message('error', 'Method broadcastLineOAMessage not found');
                    return false;
                }

            } else {
                log_message('warning', 'Corruption data not found for ID: ' . $corruption_id);
                return false;
            }
        } catch (Exception $e) {
            log_message('error', 'Failed to send Line Corruption notification: ' . $e->getMessage());
            log_message('error', 'Exception trace: ' . $e->getTraceAsString());
            return false;
        }
    }

    /**
     * ส่งแจ้งเตือนการอัปเดตสถานะรายงานการทุจริต
     */
    public function send_line_corruption_update_notification($corruption_id, $update_comment = null)
    {
        try {
            if (empty($corruption_id)) {
                log_message('error', 'Corruption ID is required for LINE notification');
                return false;
            }

            // ดึงข้อมูลจาก tbl_corruption_reports
            $this->CI->db->select('*');
            $corruptionData = $this->CI->db->get_where('tbl_corruption_reports', array('corruption_id' => $corruption_id))->row();

            if (!$corruptionData) {
                log_message('error', "Corruption report not found for LINE notification: {$corruption_id}");
                return false;
            }

            // สร้างข้อความอัปเดต
            $message = $this->build_corruption_update_message($corruptionData, $update_comment);

            // ส่งข้อความแบบ broadcast
            $result = $this->broadcastLineOAMessage($message);

            if ($result) {
                log_message('info', "LINE corruption update notification sent successfully for corruption_id: {$corruption_id}");
            } else {
                log_message('error', "Failed to send LINE corruption update notification for corruption_id: {$corruption_id}");
            }

            return $result;

        } catch (Exception $e) {
            log_message('error', "Exception in send_line_corruption_update_notification: " . $e->getMessage());
            return false;
        }
    }

    /**
     * สร้างข้อความ LINE notification สำหรับรายงานการทุจริตใหม่
     * @param object $corruptionData ข้อมูลจาก tbl_corruption_reports
     * @return string ข้อความที่จะส่ง
     */
    private function build_corruption_message($corruptionData)
    {
        log_message('info', 'Building corruption notification message for: ' . $corruptionData->corruption_id);

        $message = "รายงานการทุจริต ใหม่!\n";

        // ข้อมูลพื้นฐาน
        $message .= "รหัสรายงาน: " . $corruptionData->corruption_report_id . "\n";
        $message .= "ประเภท: " . $this->getCorruptionTypeText($corruptionData->corruption_type) . "\n";

        // ประเภทอื่นๆ ถ้ามี
        if ($corruptionData->corruption_type === 'other' && !empty($corruptionData->corruption_type_other)) {
            $message .= "ประเภท (อื่นๆ): " . $corruptionData->corruption_type_other . "\n";
        }

        $message .= "สถานะ: " . $this->getCorruptionStatusText($corruptionData->report_status) . "\n";
        $message .= "ระดับความสำคัญ: " . $this->getCorruptionPriorityText($corruptionData->priority_level) . "\n";

        // เนื้อหา
        $message .= "หัวข้อ: " . $corruptionData->complaint_subject . "\n";
        $message .= "รายละเอียด: " . $this->truncate_text($corruptionData->complaint_details, 150) . "\n";

        // ข้อมูลเหตุการณ์
        if (!empty($corruptionData->incident_date)) {
            $incident_date = date('d/m/Y', strtotime($corruptionData->incident_date));
            $message .= "วันที่เกิดเหตุ: " . $incident_date;

            if (!empty($corruptionData->incident_time)) {
                $incident_time = date('H:i', strtotime($corruptionData->incident_time));
                $message .= " เวลา " . $incident_time . " น.";
            }
            $message .= "\n";
        }

        if (!empty($corruptionData->incident_location)) {
            $message .= "สถานที่เกิดเหตุ: " . $this->truncate_text($corruptionData->incident_location, 80) . "\n";
        }

        // ข้อมูลผู้กระทำผิด
        $message .= "ผู้กระทำผิด: " . $corruptionData->perpetrator_name . "\n";

        if (!empty($corruptionData->perpetrator_department)) {
            $message .= "หน่วยงาน: " . $corruptionData->perpetrator_department . "\n";
        }

        if (!empty($corruptionData->perpetrator_position)) {
            $message .= "ตำแหน่ง: " . $corruptionData->perpetrator_position . "\n";
        }

        // ข้อมูลผู้แจ้ง
        if ($corruptionData->is_anonymous == 1) {
            $message .= "ผู้แจ้ง: ไม่ระบุตัวตน\n";
            $message .= "เบอร์: ไม่ระบุ\n";
            $message .= "อีเมล: ไม่ระบุ\n";
            $message .= "แจ้งแบบไม่ระบุตัวตน\n";
        } else {
            $message .= "ผู้แจ้ง: " . ($corruptionData->reporter_name ?: 'ไม่ระบุ') . "\n";
            $message .= "เบอร์: " . ($corruptionData->reporter_phone ?: 'ไม่ระบุ') . "\n";
            $message .= "อีเมล: " . ($corruptionData->reporter_email ?: 'ไม่ระบุ') . "\n";

            if (!empty($corruptionData->reporter_position)) {
                $message .= "ตำแหน่ง/อาชีพ: " . $corruptionData->reporter_position . "\n";
            }

            if (!empty($corruptionData->reporter_relation)) {
                $message .= "ความสัมพันธ์: " . $this->getReporterRelationText($corruptionData->reporter_relation) . "\n";
            }
        }

        // ประเภทผู้ใช้
        $user_type_text = $this->get_corruption_user_type_text($corruptionData->reporter_user_type);
        $message .= "ประเภทผู้ใช้: " . $user_type_text . "\n";

        // ข้อมูลหลักฐาน
        if ($corruptionData->evidence_file_count > 0) {
            $message .= "จำนวนไฟล์หลักฐาน: " . $corruptionData->evidence_file_count . " ไฟล์\n";
        }

        if (!empty($corruptionData->evidence_description)) {
            $message .= "รายละเอียดหลักฐาน: " . $this->truncate_text($corruptionData->evidence_description, 100) . "\n";
        }

        // ข้อมูลเวลา
        $created_time = date('d/m/Y H:i', strtotime($corruptionData->created_at));
        $message .= "แจ้งเมื่อ: " . $created_time . " น.\n";

        // แสดง IP Address สำหรับ security tracking
        if (!empty($corruptionData->ip_address)) {
            $message .= "IP: " . $corruptionData->ip_address . "\n";
        }

        // เพิ่ม badge ตามระดับความสำคัญ
        $priority_badge = $this->getCorruptionPriorityBadge($corruptionData->priority_level);
        if (!empty($priority_badge)) {
            $message .= $priority_badge . "\n";
        }

        log_message('info', 'Corruption message built successfully');
        return $message;
    }

    /**
     * สร้างข้อความ LINE notification สำหรับการอัปเดตสถานะ
     * @param object $corruptionData ข้อมูลจาก tbl_corruption_reports
     * @param string $update_comment ข้อความอัปเดต
     * @return string ข้อความที่จะส่ง
     */
    private function build_corruption_update_message($corruptionData, $update_comment = null)
    {
        $message = "รายงานการทุจริต อัปเดต!\n";

        // ข้อมูลพื้นฐาน
        $message .= "รหัสรายงาน: " . $corruptionData->corruption_report_id . "\n";
        $message .= "สถานะ: " . $this->getCorruptionStatusText($corruptionData->report_status) . "\n";
        $message .= "ระดับความสำคัญ: " . $this->getCorruptionPriorityText($corruptionData->priority_level) . "\n";
        $message .= "ประเภท: " . $this->getCorruptionTypeText($corruptionData->corruption_type) . "\n";
        $message .= "หัวข้อ: " . $corruptionData->complaint_subject . "\n";
        $message .= "รายละเอียด: " . $this->truncate_text($corruptionData->complaint_details, 100) . "\n";
        $message .= "ผู้กระทำผิด: " . $corruptionData->perpetrator_name . "\n";

        // ข้อมูลผู้แจ้ง
        if ($corruptionData->is_anonymous == 1) {
            $message .= "ผู้แจ้ง: ไม่ระบุตัวตน\n";
        } else {
            $message .= "ผู้แจ้ง: " . ($corruptionData->reporter_name ?: 'ไม่ระบุ') . "\n";
            $message .= "เบอร์: " . ($corruptionData->reporter_phone ?: 'ไม่ระบุ') . "\n";
        }

        // แสดงเวลาที่อัปเดต
        if ($corruptionData->updated_at) {
            $update_time = date('d/m/Y H:i', strtotime($corruptionData->updated_at));
            $message .= "อัปเดตเมื่อ: " . $update_time . "\n";
        }

        // การตอบกลับล่าสุด
        if (!empty($corruptionData->response_message)) {
            $message .= "การตอบกลับล่าสุด:\n";
            $message .= "โดย: " . ($corruptionData->response_by ?: 'เจ้าหน้าที่') . "\n";
            $message .= "ข้อความ: " . $this->truncate_text($corruptionData->response_message, 150) . "\n";

            if ($corruptionData->response_date) {
                $response_time = date('d/m/Y H:i', strtotime($corruptionData->response_date));
                $message .= "เวลา: " . $response_time . "\n";
            }
        }

        // ข้อความเสริมถ้ามี
        if ($update_comment) {
            $message .= "หมายเหตุเพิ่มเติม: " . $update_comment . "\n";
        }

        // ผู้รับผิดชอบ
        if (!empty($corruptionData->assigned_department)) {
            $message .= "หน่วยงานรับผิดชอบ: " . $corruptionData->assigned_department . "\n";
        }

        return $message;
    }

    /**
     * แปลงประเภทการทุจริตเป็นข้อความภาษาไทย
     */
    private function getCorruptionTypeText($type)
    {
        switch ($type) {
            case 'embezzlement':
                return 'การยักยอก';
            case 'bribery':
                return 'การรับสินบน';
            case 'abuse_of_power':
                return 'การใช้อำนาจในทางมิชอบ';
            case 'conflict_of_interest':
                return 'ผลประโยชน์ทับซ้อน';
            case 'procurement_fraud':
                return 'การทุจริตในการจัดซื้อจัดจ้าง';
            case 'other':
                return 'อื่นๆ';
            default:
                return $type;
        }
    }

    /**
     * แปลงสถานะรายงานเป็นข้อความภาษาไทย
     */
    private function getCorruptionStatusText($status)
    {
        switch ($status) {
            case 'pending':
                return 'รอดำเนินการ';
            case 'under_review':
                return 'อยู่ระหว่างพิจารณา';
            case 'investigating':
                return 'กำลังสอบสวน';
            case 'resolved':
                return 'ดำเนินการเรียบร้อย';
            case 'dismissed':
                return 'ยกเลิก';
            case 'closed':
                return 'ปิดเรื่อง';
            default:
                return $status;
        }
    }

    /**
     * แปลงระดับความสำคัญเป็นข้อความภาษาไทย
     */
    private function getCorruptionPriorityText($priority)
    {
        switch ($priority) {
            case 'low':
                return 'ต่ำ';
            case 'normal':
                return 'ปกติ';
            case 'high':
                return 'สูง';
            case 'urgent':
                return 'เร่งด่วน';
            default:
                return $priority;
        }
    }

    /**
     * แปลงความสัมพันธ์ของผู้แจ้งเป็นข้อความภาษาไทย
     */
    private function getReporterRelationText($relation)
    {
        switch ($relation) {
            case 'witness':
                return 'พยาน';
            case 'victim':
                return 'ผู้เสียหาย';
            case 'colleague':
                return 'เพื่อนร่วมงาน';
            case 'whistleblower':
                return 'ผู้แจ้งเบาะแส';
            case 'other':
                return 'อื่นๆ';
            default:
                return $relation;
        }
    }

    /**
     * แปลงประเภทผู้ใช้เป็นข้อความที่อ่านง่าย
     */
    private function get_corruption_user_type_text($user_type)
    {
        switch ($user_type) {
            case 'public':
                return 'สมาชิกสาธารณะ';
            case 'staff':
                return 'เจ้าหน้าที่';
            case 'guest':
                return 'ผู้เยี่ยมชม';
            default:
                return 'ไม่ระบุ';
        }
    }

    /**
     * สร้าง priority badge ตามระดับความสำคัญ
     */
    private function getCorruptionPriorityBadge($priority)
    {
        switch ($priority) {
            case 'urgent':
                return 'เร่งด่วน!';
            case 'high':
                return 'ความสำคัญสูง';
            case 'low':
                return 'รายงานได้รับแล้ว';
            default:
                return 'อยู่ระหว่างดำเนินการ';
        }
    }

    /**
     * อัปเดตสถานะรายงานการทุจริตพร้อม LINE notification
     * ใช้ร่วมกับการส่ง LINE notification
     */
    public function update_corruption_status_with_notification($corruption_id, $new_status, $response_message = null, $updated_by = null, $assigned_department = null)
    {
        try {
            if (!$this->CI->db->table_exists('tbl_corruption_reports')) {
                return false;
            }

            // อัปเดต tbl_corruption_reports
            $update_data = [
                'report_status' => $new_status,
                'updated_at' => date('Y-m-d H:i:s'),
                'updated_by' => $updated_by ?: 'ระบบ'
            ];

            // เพิ่มข้อมูลตอบกลับถ้ามี
            if ($response_message) {
                $update_data['response_message'] = $response_message;
                $update_data['response_by'] = $updated_by ?: 'เจ้าหน้าที่';
                $update_data['response_date'] = date('Y-m-d H:i:s');
            }

            // เพิ่มหน่วยงานรับผิดชอบถ้ามี
            if ($assigned_department) {
                $update_data['assigned_department'] = $assigned_department;
            }

            $this->CI->db->where('corruption_id', $corruption_id);
            $update_result = $this->CI->db->update('tbl_corruption_reports', $update_data);

            if (!$update_result) {
                log_message('error', "Failed to update corruption report status: {$corruption_id}");
                return false;
            }

            // ส่ง LINE notification
            $this->send_line_corruption_update_notification($corruption_id, $response_message);

            log_message('info', "Corruption report status updated successfully: {$corruption_id} -> {$new_status}");
            return true;

        } catch (Exception $e) {
            log_message('error', "Error updating corruption report status: " . $e->getMessage());
            return false;
        }
    }



    /**
     * LINE Notification Methods for Elderly AW ODS System
     * เพิ่มใน Line_notification.php library
     */

    /**
     * ส่งแจ้งเตือนเรื่องเบี้ยยังชีพผู้สูงอายุ/คนพิการใหม่
     * อิงตามโครงสร้าง tbl_elderly_aw_ods
     */
    public function send_line_elderly_aw_ods_notification($elderly_aw_ods_id)
    {
        try {
            log_message('info', 'Starting Elderly AW ODS line notification for ID: ' . $elderly_aw_ods_id);

            $elderlyData = $this->CI->db->get_where('tbl_elderly_aw_ods', array('elderly_aw_ods_id' => $elderly_aw_ods_id))->row();

            if ($elderlyData) {
                log_message('info', 'Elderly AW ODS data found for notification');

                $message = $this->build_elderly_aw_ods_message($elderlyData);

                // ตรวจสอบว่ามี method broadcastLineOAMessage หรือไม่
                if (method_exists($this, 'broadcastLineOAMessage')) {
                    log_message('info', 'Sending LINE elderly AW ODS notification message');
                    $result = $this->broadcastLineOAMessage($message);

                    if ($result) {
                        log_message('info', 'Line Elderly AW ODS notification sent successfully for elderly_aw_ods_id: ' . $elderly_aw_ods_id);
                    } else {
                        log_message('error', 'Failed to send LINE elderly AW ODS notification for elderly_aw_ods_id: ' . $elderly_aw_ods_id);
                    }

                    return $result;
                } else {
                    log_message('error', 'Method broadcastLineOAMessage not found');
                    return false;
                }

            } else {
                log_message('warning', 'Elderly AW ODS data not found for ID: ' . $elderly_aw_ods_id);
                return false;
            }
        } catch (Exception $e) {
            log_message('error', 'Failed to send Line Elderly AW ODS notification: ' . $e->getMessage());
            log_message('error', 'Exception trace: ' . $e->getTraceAsString());
            return false;
        }
    }

    /**
     * ส่งแจ้งเตือนการอัปเดตสถานะเบี้ยยังชีพผู้สูงอายุ/คนพิการ
     */
    public function send_line_elderly_aw_ods_update_notification($elderly_aw_ods_id, $update_comment = null)
    {
        try {
            if (empty($elderly_aw_ods_id)) {
                log_message('error', 'Elderly AW ODS ID is required for LINE notification');
                return false;
            }

            // *** 1. ดึงข้อมูลจาก tbl_elderly_aw_ods ***
            $this->CI->db->select('*');
            $elderlyData = $this->CI->db->get_where('tbl_elderly_aw_ods', array('elderly_aw_ods_id' => $elderly_aw_ods_id))->row();

            if (!$elderlyData) {
                log_message('error', "Elderly AW ODS not found for LINE notification: {$elderly_aw_ods_id}");
                return false;
            }

            // *** 2. สร้างข้อความอัปเดต ***
            $message = $this->build_elderly_aw_ods_update_message($elderlyData, $update_comment);

            // *** 3. ส่งข้อความแบบ broadcast ***
            $result = $this->broadcastLineOAMessage($message);

            if ($result) {
                log_message('info', "LINE elderly AW ODS update notification sent successfully for elderly_aw_ods_id: {$elderly_aw_ods_id}");
            } else {
                log_message('error', "Failed to send LINE elderly AW ODS update notification for elderly_aw_ods_id: {$elderly_aw_ods_id}");
            }

            return $result;

        } catch (Exception $e) {
            log_message('error', "Exception in send_line_elderly_aw_ods_update_notification: " . $e->getMessage());
            return false;
        }
    }

    /**
     * สร้างข้อความ LINE notification สำหรับเรื่องเบี้ยยังชีพใหม่
     * @param object $elderlyData ข้อมูลจาก tbl_elderly_aw_ods
     * @return string ข้อความที่จะส่ง
     */
    private function build_elderly_aw_ods_message($elderlyData)
    {
        log_message('info', 'Building elderly AW ODS notification message for: ' . $elderlyData->elderly_aw_ods_id);

        // สร้างหัวข้อข้อความตามประเภท
        $type_text = $this->getElderlyAwOdsTypeText($elderlyData->elderly_aw_ods_type);
        $message = "เรื่องเบี้ยยังชีพผู้สูงอายุ / ผู้พิการ ใหม่!\n";

        // ข้อมูลพื้นฐาน
        $message .= "รหัสอ้างอิง: " . $elderlyData->elderly_aw_ods_id . "\n";
        $message .= "ประเภท: " . $type_text . "\n";
        $message .= "สถานะ: " . $this->getElderlyAwOdsStatusText($elderlyData->elderly_aw_ods_status) . "\n";
        $message .= "ระดับความสำคัญ: " . $this->getElderlyAwOdsPriorityText($elderlyData->elderly_aw_ods_priority) . "\n";

        // ข้อมูลผู้ยื่นเรื่อง
        $message .= "ผู้ยื่นเรื่อง: " . $elderlyData->elderly_aw_ods_by . "\n";
        $message .= "เบอร์โทรศัพท์: " . $elderlyData->elderly_aw_ods_phone . "\n";
        $message .= "อีเมล: " . ($elderlyData->elderly_aw_ods_email ?: 'ไม่ระบุ') . "\n";

        // เลขบัตรประชาชนถ้ามี (แสดงแบบ mask)
        if (!empty($elderlyData->elderly_aw_ods_number)) {
            $masked_id = $this->format_citizen_id($elderlyData->elderly_aw_ods_number);
            $message .= "บัตรประชาชน: " . $masked_id . "\n";
        }

        // ข้อมูลที่อยู่
        $address = $this->build_elderly_aw_ods_address($elderlyData);
        if (!empty($address)) {
            $message .= "ที่อยู่: " . $address . "\n";
        }

        // ประเภทผู้ใช้
        $user_type_text = $this->get_elderly_aw_ods_user_type_text($elderlyData->elderly_aw_ods_user_type);
        $message .= "ประเภทผู้ใช้: " . $user_type_text . "\n";

        // ข้อมูลไฟล์แนบ
        if (!empty($elderlyData->elderly_aw_ods_files)) {
            $files_data = json_decode($elderlyData->elderly_aw_ods_files, true);
            if (is_array($files_data) && count($files_data) > 0) {
                $message .= "จำนวนไฟล์แนบ: " . count($files_data) . " ไฟล์\n";
            }
        }

        // ข้อมูลเวลา
        $created_time = date('d/m/Y H:i', strtotime($elderlyData->elderly_aw_ods_datesave));
        $message .= "ยื่นเรื่องเมื่อ: " . $created_time . " น.\n";

        // หมายเหตุถ้ามี
        if (!empty($elderlyData->elderly_aw_ods_notes)) {
            $message .= "หมายเหตุ: " . $this->truncate_text($elderlyData->elderly_aw_ods_notes, 100) . "\n";
        }

        // แสดง IP Address สำหรับ security tracking
        if (!empty($elderlyData->elderly_aw_ods_ip_address)) {
            $message .= "IP: " . $elderlyData->elderly_aw_ods_ip_address . "\n";
        }

        // เพิ่ม badge ตามระดับความสำคัญ
        $priority_badge = $this->getElderlyAwOdsPriorityBadge($elderlyData->elderly_aw_ods_priority);
        if (!empty($priority_badge)) {
            $message .= $priority_badge . "\n";
        }

        log_message('info', 'Elderly AW ODS message built successfully');
        return $message;
    }

    /**
     * สร้างข้อความ LINE notification สำหรับการอัปเดตสถานะ
     * @param object $elderlyData ข้อมูลจาก tbl_elderly_aw_ods
     * @param string $update_comment ข้อความอัปเดต
     * @return string ข้อความที่จะส่ง
     */
    private function build_elderly_aw_ods_update_message($elderlyData, $update_comment = null)
    {
        $type_text = $this->getElderlyAwOdsTypeText($elderlyData->elderly_aw_ods_type);
        $message = "เรื่อง" . $type_text . " อัปเดต!\n";

        // ข้อมูลพื้นฐาน
        $message .= "รหัสอ้างอิง: " . $elderlyData->elderly_aw_ods_id . "\n";
        $message .= "ประเภท: " . $type_text . "\n";
        $message .= "สถานะ: " . $this->getElderlyAwOdsStatusText($elderlyData->elderly_aw_ods_status) . "\n";
        $message .= "ระดับความสำคัญ: " . $this->getElderlyAwOdsPriorityText($elderlyData->elderly_aw_ods_priority) . "\n";
        $message .= "ผู้ยื่นเรื่อง: " . $elderlyData->elderly_aw_ods_by . "\n";
        $message .= "เบอร์โทรศัพท์: " . $elderlyData->elderly_aw_ods_phone . "\n";

        // เลขบัตรประชาชนถ้ามี (แสดงแบบ mask)
        if (!empty($elderlyData->elderly_aw_ods_number)) {
            $masked_id = $this->format_citizen_id($elderlyData->elderly_aw_ods_number);
            $message .= "บัตรประชาชน: " . $masked_id . "\n";
        }

        // แสดงเวลาที่อัปเดต
        if ($elderlyData->elderly_aw_ods_updated_at) {
            $update_time = date('d/m/Y H:i', strtotime($elderlyData->elderly_aw_ods_updated_at));
            $message .= "อัปเดตเมื่อ: " . $update_time . "\n";
        }

        // ผู้อัปเดต
        if ($elderlyData->elderly_aw_ods_updated_by) {
            $message .= "อัปเดตโดย: " . $elderlyData->elderly_aw_ods_updated_by . "\n";
        }

        // หมายเหตุจากเจ้าหน้าที่
        if (!empty($elderlyData->elderly_aw_ods_notes)) {
            $message .= "หมายเหตุ: " . $this->truncate_text($elderlyData->elderly_aw_ods_notes, 150) . "\n";
        }

        // ข้อความเสริมถ้ามี
        if ($update_comment) {
            $message .= "หมายเหตุเพิ่มเติม: " . $update_comment . "\n";
        }

        // ผู้รับผิดชอบ
        if (!empty($elderlyData->elderly_aw_ods_assigned_to)) {
            $message .= "เจ้าหน้าที่ผู้รับผิดชอบ: ID " . $elderlyData->elderly_aw_ods_assigned_to . "\n";
        }

        // วันที่เสร็จสิ้น (ถ้ามี)
        if ($elderlyData->elderly_aw_ods_status === 'completed' && $elderlyData->elderly_aw_ods_completed_at) {
            $completed_time = date('d/m/Y H:i', strtotime($elderlyData->elderly_aw_ods_completed_at));
            $message .= "เสร็จสิ้นเมื่อ: " . $completed_time . " น.\n";
        }

        return $message;
    }

    /**
     * สร้างข้อความที่อยู่จากข้อมูลเบี้ยยังชีพ
     * @param object $elderlyData ข้อมูลจาก tbl_elderly_aw_ods
     * @return string ที่อยู่ที่จัดรูปแบบแล้ว
     */
    private function build_elderly_aw_ods_address($elderlyData)
    {
        $address_parts = array();

        // ที่อยู่หลัก
        if (!empty($elderlyData->elderly_aw_ods_address)) {
            $address_parts[] = $elderlyData->elderly_aw_ods_address;
        }

        // ตำบล อำเภอ จังหวัด (สำหรับ guest)
        if (!empty($elderlyData->guest_district)) {
            $address_parts[] = "ตำบล" . $elderlyData->guest_district;
        }

        if (!empty($elderlyData->guest_amphoe)) {
            $address_parts[] = "อำเภอ" . $elderlyData->guest_amphoe;
        }

        if (!empty($elderlyData->guest_province)) {
            $address_parts[] = "จังหวัด" . $elderlyData->guest_province;
        }

        if (!empty($elderlyData->guest_zipcode) && $elderlyData->guest_zipcode !== '00000') {
            $address_parts[] = $elderlyData->guest_zipcode;
        }

        $full_address = implode(' ', $address_parts);
        return $this->truncate_text($full_address, 80);
    }

    /**
     * แปลงประเภทเบี้ยยังชีพเป็นข้อความภาษาไทย
     * @param string $type ประเภทจาก database
     * @return string ข้อความประเภทภาษาไทย
     */
    private function getElderlyAwOdsTypeText($type)
    {
        switch ($type) {
            case 'elderly':
                return 'เบี้ยยังชีพผู้สูงอายุ';
            case 'disabled':
                return 'เบี้ยยังชีพคนพิการ';
            default:
                return $type;
        }
    }

    /**
     * แปลงสถานะเป็นข้อความภาษาไทย
     * @param string $status สถานะจาก database
     * @return string ข้อความสถานะภาษาไทย
     */
    private function getElderlyAwOdsStatusText($status)
    {
        switch ($status) {
            case 'submitted':
                return 'ยื่นเรื่องแล้ว';
            case 'reviewing':
                return 'อยู่ระหว่างพิจารณา';
            case 'approved':
                return 'อนุมัติแล้ว';
            case 'rejected':
                return 'ไม่อนุมัติ';
            case 'completed':
                return 'เสร็จสิ้น';
            default:
                return $status;
        }
    }

    /**
     * แปลงระดับความสำคัญเป็นข้อความภาษาไทย
     * @param string $priority ระดับความสำคัญจาก database
     * @return string ข้อความระดับความสำคัญภาษาไทย
     */
    private function getElderlyAwOdsPriorityText($priority)
    {
        switch ($priority) {
            case 'low':
                return 'ต่ำ';
            case 'normal':
                return 'ปกติ';
            case 'high':
                return 'สูง';
            case 'urgent':
                return 'เร่งด่วน';
            default:
                return $priority;
        }
    }

    /**
     * แปลงประเภทผู้ใช้เป็นข้อความที่อ่านง่าย
     * @param string $user_type ประเภทผู้ใช้
     * @return string ข้อความประเภทผู้ใช้
     */
    private function get_elderly_aw_ods_user_type_text($user_type)
    {
        switch ($user_type) {
            case 'public':
                return 'สมาชิกสาธารณะ';
            case 'staff':
                return 'เจ้าหน้าที่';
            case 'guest':
                return 'ผู้เยี่ยมชม';
            default:
                return 'ไม่ระบุ';
        }
    }

    /**
     * สร้าง priority badge ตามระดับความสำคัญ
     * @param string $priority ระดับความสำคัญ
     * @return string emoji badge
     */
    private function getElderlyAwOdsPriorityBadge($priority)
    {
        switch ($priority) {
            case 'urgent':
                return 'เร่งด่วน!';
            case 'high':
                return 'ความสำคัญสูง';
            case 'low':
                return 'รายการได้รับแล้ว';
            default:
                return 'อยู่ระหว่างดำเนินการ';
        }
    }

    /**
     * อัปเดตสถานะเบี้ยยังชีพพร้อม LINE notification
     * ใช้ร่วมกับการส่ง LINE notification
     */
    public function update_elderly_aw_ods_status_with_notification($elderly_aw_ods_id, $new_status, $notes = null, $updated_by = null, $assigned_to = null)
    {
        try {
            if (!$this->CI->db->table_exists('tbl_elderly_aw_ods')) {
                return false;
            }

            // *** 1. อัปเดต tbl_elderly_aw_ods ***
            $update_data = [
                'elderly_aw_ods_status' => $new_status,
                'elderly_aw_ods_updated_at' => date('Y-m-d H:i:s'),
                'elderly_aw_ods_updated_by' => $updated_by ?: 'ระบบ'
            ];

            // เพิ่มหมายเหตุถ้ามี
            if ($notes) {
                $update_data['elderly_aw_ods_notes'] = $notes;
            }

            // เพิ่มผู้รับผิดชอบถ้ามี
            if ($assigned_to) {
                $update_data['elderly_aw_ods_assigned_to'] = $assigned_to;
            }

            // เพิ่มวันที่เสร็จสิ้นถ้าสถานะเป็น completed
            if ($new_status === 'completed') {
                $update_data['elderly_aw_ods_completed_at'] = date('Y-m-d H:i:s');
            }

            $this->CI->db->where('elderly_aw_ods_id', $elderly_aw_ods_id);
            $update_result = $this->CI->db->update('tbl_elderly_aw_ods', $update_data);

            if (!$update_result) {
                log_message('error', "Failed to update elderly AW ODS status: {$elderly_aw_ods_id}");
                return false;
            }

            // *** 2. ส่ง LINE notification ***
            $this->send_line_elderly_aw_ods_update_notification($elderly_aw_ods_id, $notes);

            log_message('info', "Elderly AW ODS status updated successfully: {$elderly_aw_ods_id} -> {$new_status}");
            return true;

        } catch (Exception $e) {
            log_message('error', "Error updating elderly AW ODS status: " . $e->getMessage());
            return false;
        }
    }




    /**
     * LINE Notification Methods for Kid AW ODS (Children Allowance)
     * เพิ่มใน Line_notification.php library
     */

    /**
     * ส่งแจ้งเตือนการยื่นเรื่องเบี้ยเลี้ยงดูเด็กใหม่
     * อิงตามโครงสร้าง tbl_kid_aw_ods
     */
    public function send_line_kid_aw_ods_notification($kid_aw_ods_id)
    {
        try {
            log_message('info', 'Starting Kid AW ODS line notification for ID: ' . $kid_aw_ods_id);

            $kidAwOdsData = $this->CI->db->get_where('tbl_kid_aw_ods', array('kid_aw_ods_id' => $kid_aw_ods_id))->row();

            if ($kidAwOdsData) {
                log_message('info', 'Kid AW ODS data found for notification');

                $message = $this->build_kid_aw_ods_message($kidAwOdsData);

                // ตรวจสอบว่ามี method broadcastLineOAMessage หรือไม่
                if (method_exists($this, 'broadcastLineOAMessage')) {
                    log_message('info', 'Sending LINE kid aw ods notification message');
                    $result = $this->broadcastLineOAMessage($message);

                    if ($result) {
                        log_message('info', 'Line Kid AW ODS notification sent successfully for kid_aw_ods_id: ' . $kid_aw_ods_id);
                    } else {
                        log_message('error', 'Failed to send LINE kid aw ods notification for kid_aw_ods_id: ' . $kid_aw_ods_id);
                    }

                    return $result;
                } else {
                    log_message('error', 'Method broadcastLineOAMessage not found');
                    return false;
                }

            } else {
                log_message('warning', 'Kid AW ODS data not found for ID: ' . $kid_aw_ods_id);
                return false;
            }
        } catch (Exception $e) {
            log_message('error', 'Failed to send Line Kid AW ODS notification: ' . $e->getMessage());
            log_message('error', 'Exception trace: ' . $e->getTraceAsString());
            return false;
        }
    }

    /**
     * ส่งแจ้งเตือนการอัปเดตสถานะเบี้ยเลี้ยงดูเด็ก
     */
    public function send_line_kid_aw_ods_update_notification($kid_aw_ods_id, $update_comment = null)
    {
        try {
            if (empty($kid_aw_ods_id)) {
                log_message('error', 'Kid AW ODS ID is required for LINE notification');
                return false;
            }

            // ดึงข้อมูลจาก tbl_kid_aw_ods
            $this->CI->db->select('*');
            $kidAwOdsData = $this->CI->db->get_where('tbl_kid_aw_ods', array('kid_aw_ods_id' => $kid_aw_ods_id))->row();

            if (!$kidAwOdsData) {
                log_message('error', "Kid AW ODS not found for LINE notification: {$kid_aw_ods_id}");
                return false;
            }

            // สร้างข้อความอัปเดต
            $message = $this->build_kid_aw_ods_update_message($kidAwOdsData, $update_comment);

            // ส่งข้อความแบบ broadcast
            $result = $this->broadcastLineOAMessage($message);

            if ($result) {
                log_message('info', "LINE kid aw ods update notification sent successfully for kid_aw_ods_id: {$kid_aw_ods_id}");
            } else {
                log_message('error', "Failed to send LINE kid aw ods update notification for kid_aw_ods_id: {$kid_aw_ods_id}");
            }

            return $result;

        } catch (Exception $e) {
            log_message('error', "Exception in send_line_kid_aw_ods_update_notification: " . $e->getMessage());
            return false;
        }
    }

    /**
     * สร้างข้อความ LINE notification สำหรับการยื่นเรื่องเบี้ยเลี้ยงดูเด็กใหม่
     * @param object $kidAwOdsData ข้อมูลจาก tbl_kid_aw_ods
     * @return string ข้อความที่จะส่ง
     */
    private function build_kid_aw_ods_message($kidAwOdsData)
    {
        log_message('info', 'Building kid aw ods notification message for: ' . $kidAwOdsData->kid_aw_ods_id);

        $message = "ยื่นเรื่องเงินอุดหนุนเด็กแรกเกิด ใหม่!\n";

        // ข้อมูลพื้นฐาน
        $message .= "รหัสอ้างอิง: " . $kidAwOdsData->kid_aw_ods_id . "\n";
        $message .= "ประเภท: " . $this->getKidAwOdsTypeText($kidAwOdsData->kid_aw_ods_type) . "\n";
        $message .= "สถานะ: " . $this->getKidAwOdsStatusText($kidAwOdsData->kid_aw_ods_status) . "\n";
        $message .= "ระดับความสำคัญ: " . $this->getKidAwOdsPriorityText($kidAwOdsData->kid_aw_ods_priority) . "\n";

        // ข้อมูลผู้ยื่นเรื่อง
        $message .= "ผู้ยื่นเรื่อง: " . $kidAwOdsData->kid_aw_ods_by . "\n";
        $message .= "เบอร์โทรศัพท์: " . $kidAwOdsData->kid_aw_ods_phone . "\n";
        $message .= "อีเมล: " . ($kidAwOdsData->kid_aw_ods_email ?: 'ไม่ระบุ') . "\n";

        // เลขบัตรประชาชน (แสดงแบบ mask)
        if (!empty($kidAwOdsData->kid_aw_ods_number)) {
            $masked_id = $this->format_citizen_id($kidAwOdsData->kid_aw_ods_number);
            $message .= "บัตรประชาชน: " . $masked_id . "\n";
        }

        // ข้อมูลที่อยู่
        $message .= "ที่อยู่: " . $this->getKidAwOdsPriorityText($kidAwOdsData->kid_aw_ods_address) . "\n";
        //$address = $this->build_kid_aw_ods_address($kidAwOdsData);
        //if (!empty($address)) {
        //    $message .= "ที่อยู่: " . $address . "\n";
        //}

        // ประเภทผู้ใช้
        $user_type_text = $this->get_kid_aw_ods_user_type_text($kidAwOdsData->kid_aw_ods_user_type);
        $message .= "ประเภทผู้ใช้: " . $user_type_text . "\n";

        // ผู้รับผิดชอบ
        if (!empty($kidAwOdsData->kid_aw_ods_assigned_to)) {
            $message .= "ผู้รับผิดชอบ: ID " . $kidAwOdsData->kid_aw_ods_assigned_to . "\n";
        }

        // หมายเหตุ
        if (!empty($kidAwOdsData->kid_aw_ods_notes)) {
            $message .= "หมายเหตุ: " . $this->truncate_text($kidAwOdsData->kid_aw_ods_notes, 100) . "\n";
        }

        // ข้อมูลไฟล์แนบ
        if (!empty($kidAwOdsData->kid_aw_ods_files)) {
            $files_data = json_decode($kidAwOdsData->kid_aw_ods_files, true);
            if (is_array($files_data)) {
                $message .= "ไฟล์แนบ: " . count($files_data) . " ไฟล์\n";
            }
        }

        // ข้อมูลเวลา
        $created_time = date('d/m/Y H:i', strtotime($kidAwOdsData->kid_aw_ods_datesave));
        $message .= "ยื่นเรื่องเมื่อ: " . $created_time . " น.\n";

        // แสดง IP Address สำหรับ security tracking
        if (!empty($kidAwOdsData->kid_aw_ods_ip_address)) {
            $message .= "IP: " . $kidAwOdsData->kid_aw_ods_ip_address . "\n";
        }

        // เพิ่ม badge ตามระดับความสำคัญ
        $priority_badge = $this->getKidAwOdsPriorityBadge($kidAwOdsData->kid_aw_ods_priority);
        if (!empty($priority_badge)) {
            $message .= $priority_badge . "\n";
        }


        log_message('info', 'Kid AW ODS message built successfully');
        return $message;
    }

    /**
     * สร้างข้อความ LINE notification สำหรับการอัปเดตสถานะ
     * @param object $kidAwOdsData ข้อมูลจาก tbl_kid_aw_ods
     * @param string $update_comment ข้อความอัปเดต
     * @return string ข้อความที่จะส่ง
     */
    private function build_kid_aw_ods_update_message($kidAwOdsData, $update_comment = null)
    {
        $message = "เงินอุดหนุนเด็กแรกเกิด อัปเดต!\n";

        // ข้อมูลพื้นฐาน
        $message .= "รหัสอ้างอิง: " . $kidAwOdsData->kid_aw_ods_id . "\n";
        $message .= "สถานะ: " . $this->getKidAwOdsStatusText($kidAwOdsData->kid_aw_ods_status) . "\n";
        $message .= "ระดับความสำคัญ: " . $this->getKidAwOdsPriorityText($kidAwOdsData->kid_aw_ods_priority) . "\n";
        $message .= "ประเภท: " . $this->getKidAwOdsTypeText($kidAwOdsData->kid_aw_ods_type) . "\n";

        // ข้อมูลผู้ยื่นเรื่อง
        $message .= "ผู้ยื่นเรื่อง: " . $kidAwOdsData->kid_aw_ods_by . "\n";
        $message .= "เบอร์โทร: " . $kidAwOdsData->kid_aw_ods_phone . "\n";

        // เลขบัตรประชาชน (แสดงแบบ mask)
        if (!empty($kidAwOdsData->kid_aw_ods_number)) {
            $masked_id = $this->format_citizen_id($kidAwOdsData->kid_aw_ods_number);
            $message .= "บัตรประชาชน: " . $masked_id . "\n";
        }

        // แสดงเวลาที่อัปเดต
        if ($kidAwOdsData->kid_aw_ods_updated_at) {
            $update_time = date('d/m/Y H:i', strtotime($kidAwOdsData->kid_aw_ods_updated_at));
            $message .= "อัปเดตเมื่อ: " . $update_time . "\n";
        }

        // ผู้อัปเดตล่าสุด
        if (!empty($kidAwOdsData->kid_aw_ods_updated_by)) {
            $message .= "อัปเดตโดย: " . $kidAwOdsData->kid_aw_ods_updated_by . "\n";
        }

        // หมายเหตุจากเจ้าหน้าที่
        if (!empty($kidAwOdsData->kid_aw_ods_notes)) {
            $message .= "หมายเหตุ: " . $this->truncate_text($kidAwOdsData->kid_aw_ods_notes, 150) . "\n";
        }

        // ข้อความเสริมถ้ามี
        if ($update_comment) {
            $message .= "หมายเหตุเพิ่มเติม: " . $update_comment . "\n";
        }

        // ผู้รับผิดชอบ
        if (!empty($kidAwOdsData->kid_aw_ods_assigned_to)) {
            $message .= "ผู้รับผิดชอบ: ID " . $kidAwOdsData->kid_aw_ods_assigned_to . "\n";
        }

        // แสดงสถานะการเสร็จสิ้น
        if ($kidAwOdsData->kid_aw_ods_status === 'completed' && $kidAwOdsData->kid_aw_ods_completed_at) {
            $completed_time = date('d/m/Y H:i', strtotime($kidAwOdsData->kid_aw_ods_completed_at));
            $message .= "เสร็จสิ้นเมื่อ: " . $completed_time . "\n";
        }

        return $message;
    }

    /**
     * สร้างข้อความที่อยู่จากข้อมูลเบี้ยเลี้ยงดูเด็ก
     * @param object $kidAwOdsData ข้อมูลจาก tbl_kid_aw_ods
     * @return string ที่อยู่ที่จัดรูปแบบแล้ว
     */
    private function build_kid_aw_ods_address($kidAwOdsData)
    {
        $address_parts = array();

        // ที่อยู่หลัก
        if (!empty($kidAwOdsData->kid_aw_ods_address)) {
            $address_parts[] = $kidAwOdsData->kid_aw_ods_address;
        }

        // ตำบล อำเภอ จังหวัด (สำหรับ guest)
        // if (!empty($kidAwOdsData->guest_district)) {
        //     $address_parts[] = "ตำบล" . $kidAwOdsData->guest_district;
        // }

        // if (!empty($kidAwOdsData->guest_amphoe)) {
        //     $address_parts[] = "อำเภอ" . $kidAwOdsData->guest_amphoe;
        // }

        // if (!empty($kidAwOdsData->guest_province)) {
        //     $address_parts[] = "จังหวัด" . $kidAwOdsData->guest_province;
        // }

        // if (!empty($kidAwOdsData->guest_zipcode) && $kidAwOdsData->guest_zipcode !== '00000') {
        //     $address_parts[] = $kidAwOdsData->guest_zipcode;
        // }

        $full_address = implode(' ', $address_parts);
        return $this->truncate_text($full_address, 250);
    }

    /**
     * แปลงประเภทเบี้ยเลี้ยงดูเด็กเป็นข้อความภาษาไทย
     * @param string $type ประเภทจาก database
     * @return string ข้อความประเภทภาษาไทย
     */
    private function getKidAwOdsTypeText($type)
    {
        switch ($type) {
            case 'children':
                return 'เบี้ยเลี้ยงดูเด็ก';
            default:
                return $type;
        }
    }

    /**
     * แปลงสถานะเบี้ยเลี้ยงดูเด็กเป็นข้อความภาษาไทย
     * @param string $status สถานะจาก database
     * @return string ข้อความสถานะภาษาไทย
     */
    private function getKidAwOdsStatusText($status)
    {
        switch ($status) {
            case 'submitted':
                return 'ยื่นเรื่องแล้ว';
            case 'reviewing':
                return 'อยู่ระหว่างพิจารณา';
            case 'approved':
                return 'อนุมัติ';
            case 'rejected':
                return 'ปฏิเสธ';
            case 'completed':
                return 'เสร็จสิ้น';
            default:
                return $status;
        }
    }

    /**
     * แปลงระดับความสำคัญเป็นข้อความภาษาไทย
     * @param string $priority ระดับความสำคัญจาก database
     * @return string ข้อความระดับความสำคัญภาษาไทย
     */
    private function getKidAwOdsPriorityText($priority)
    {
        switch ($priority) {
            case 'low':
                return 'ต่ำ';
            case 'normal':
                return 'ปกติ';
            case 'high':
                return 'สูง';
            case 'urgent':
                return 'เร่งด่วน';
            default:
                return $priority;
        }
    }

    /**
     * แปลงประเภทผู้ใช้เป็นข้อความที่อ่านง่าย
     * @param string $user_type ประเภทผู้ใช้
     * @return string ข้อความประเภทผู้ใช้
     */
    private function get_kid_aw_ods_user_type_text($user_type)
    {
        switch ($user_type) {
            case 'public':
                return 'สมาชิกสาธารณะ';
            case 'staff':
                return 'เจ้าหน้าที่';
            case 'guest':
                return 'ผู้เยี่ยมชม';
            default:
                return 'ไม่ระบุ';
        }
    }

    /**
     * สร้าง priority badge ตามระดับความสำคัญ
     * @param string $priority ระดับความสำคัญ
     * @return string emoji badge
     */
    private function getKidAwOdsPriorityBadge($priority)
    {
        switch ($priority) {
            case 'urgent':
                return 'เร่งด่วน!';
            case 'high':
                return 'ความสำคัญสูง';
            case 'low':
                return 'รับเรื่องแล้ว';
            default:
                return 'อยู่ระหว่างดำเนินการ';
        }
    }

    /**
     * อัปเดตสถานะเบี้ยเลี้ยงดูเด็กพร้อม LINE notification
     * ใช้ร่วมกับการส่ง LINE notification
     */
    public function update_kid_aw_ods_status_with_notification($kid_aw_ods_id, $new_status, $notes = null, $updated_by = null, $assigned_to = null)
    {
        try {
            if (!$this->CI->db->table_exists('tbl_kid_aw_ods')) {
                return false;
            }

            // อัปเดต tbl_kid_aw_ods
            $update_data = [
                'kid_aw_ods_status' => $new_status,
                'kid_aw_ods_updated_at' => date('Y-m-d H:i:s'),
                'kid_aw_ods_updated_by' => $updated_by ?: 'ระบบ'
            ];

            // เพิ่มหมายเหตุถ้ามี
            if ($notes) {
                $update_data['kid_aw_ods_notes'] = $notes;
            }

            // เพิ่มผู้รับผิดชอบถ้ามี
            if ($assigned_to) {
                $update_data['kid_aw_ods_assigned_to'] = $assigned_to;
            }

            // ถ้าสถานะเป็น completed ให้เพิ่มเวลาเสร็จสิ้น
            if ($new_status === 'completed') {
                $update_data['kid_aw_ods_completed_at'] = date('Y-m-d H:i:s');
            }

            $this->CI->db->where('kid_aw_ods_id', $kid_aw_ods_id);
            $update_result = $this->CI->db->update('tbl_kid_aw_ods', $update_data);

            if (!$update_result) {
                log_message('error', "Failed to update kid aw ods status: {$kid_aw_ods_id}");
                return false;
            }

            // ส่ง LINE notification
            $this->send_line_kid_aw_ods_update_notification($kid_aw_ods_id, $notes);

            log_message('info', "Kid AW ODS status updated successfully: {$kid_aw_ods_id} -> {$new_status}");
            return true;

        } catch (Exception $e) {
            log_message('error', "Error updating kid aw ods status: " . $e->getMessage());
            return false;
        }
    }




}