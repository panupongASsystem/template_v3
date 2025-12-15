<?php
defined('BASEPATH') or exit('No direct script access allowed');

/**
* Notification Library - จัดการการแจ้งเตือนแบบ Multi-User
* Version: 2.1 - รองรับ Individual Read Status + แก้ไข User ID Overflow + Methods ใหม่
*/
class Notification_lib
{
   private $CI;
   
   // กำหนดประเภทการแจ้งเตือน
   const TYPE_STORAGE = 'storage';
   const TYPE_COMPLAIN = 'complain';
   const TYPE_QUEUE = 'queue';
   const TYPE_SUGGESTION = 'suggestion';
   const TYPE_QA = 'qa';
   const TYPE_QA_NEW = 'qa_new';
   const TYPE_QA_REPLY = 'qa_reply';
   const TYPE_QA_UPDATE = 'qa_update';
   const TYPE_SYSTEM = 'system';
   const TYPE_UPDATE = 'update';

   // กำหนดระดับความสำคัญ
   const PRIORITY_LOW = 'low';
   const PRIORITY_NORMAL = 'normal';
   const PRIORITY_HIGH = 'high';
   const PRIORITY_CRITICAL = 'critical';

   // กำหนด Role
   const ROLE_PUBLIC = 'public';
   const ROLE_ADMIN = 'admin';
   const ROLE_STAFF = 'staff';
   const ROLE_SYSTEM_ADMIN = 'system_admin';

   public function __construct()
   {
       $this->CI =& get_instance();
       $this->CI->load->model('Notification_model');
       $this->CI->load->database();
       $this->CI->load->helper('url');
   }

  
  

   /**
    * *** เพิ่ม: นับจำนวนทั้งหมดสำหรับ user ***
    */
   public function get_total_count($target_role = null)
   {
       try {
           // ดึงข้อมูล user ปัจจุบัน
           $current_user = $this->get_current_user_info();
           
           if (!$current_user['user_id'] || !$current_user['user_type']) {
               log_message('info', 'get_total_count: Cannot determine current user');
               return 0;
           }
           
           $final_role = $target_role ?: $current_user['role'];
           
           // Raw query นับทั้งหมด (ไม่แยก read/unread)
           $sql = "
               SELECT COUNT(*) as total_count
               FROM tbl_notifications n
               WHERE n.target_role = ? 
                 AND n.is_archived = 0
                 AND (
                     -- สำหรับ complain ต้องมี target_user_id = current_user_id
                     (n.type = 'complain' AND n.target_user_id = ? AND n.target_user_id IS NOT NULL)
                     OR 
                     -- สำหรับ type อื่นๆ (เช่น qa) ใช้ logic เดิม
                     (n.type != 'complain')
                 )
           ";
           
           $query = $this->CI->db->query($sql, [$final_role, $current_user['user_id']]);
           
           if ($query) {
               $result = $query->row();
               $count = $result ? (int)$result->total_count : 0;
               log_message('info', "get_total_count: Found {$count} total notifications for {$final_role}");
               return $count;
           }
           
           return 0;
           
       } catch (Exception $e) {
           log_message('error', 'Error in get_total_count: ' . $e->getMessage());
           return 0;
       }
   }

   /**
    * *** แก้ไข: รวม methods เดิมและใหม่ - ทำเครื่องหมายว่าอ่านแล้วสำหรับ user แบบ Individual Read Status ***
    */
   public function mark_as_read($notification_id)
   {
       try {
           // ดึงข้อมูล user ปัจจุบัน
           $current_user = $this->get_current_user_info();
           
           if (!$current_user['user_id'] || !$current_user['user_type']) {
               log_message('info', 'mark_as_read: Cannot determine current user');
               return false; // guest user ไม่สามารถ mark read ได้
           }
           
           log_message('info', "mark_as_read: Notification={$notification_id}, User={$current_user['user_id']}, Type={$current_user['user_type']}");
           
           // ใช้ Notification_model method ที่แก้ไขแล้ว
           return $this->CI->Notification_model->mark_read_by_user(
               $notification_id, 
               $current_user['user_id'], 
               $current_user['user_type']
           );
           
       } catch (Exception $e) {
           log_message('error', 'Error in mark_as_read: ' . $e->getMessage());
           return false;
       }
   }

   /**
    * *** แก้ไข: รวม methods เดิมและใหม่ - ทำเครื่องหมายทั้งหมดว่าอ่านแล้วสำหรับ user แบบ Individual Read Status ***
    */
   public function mark_all_as_read($target_role = null)
   {
       try {
           // ดึงข้อมูล user ปัจจุบัน
           $current_user = $this->get_current_user_info();
           
           if (!$current_user['user_id'] || !$current_user['user_type']) {
               log_message('info', 'mark_all_as_read: Cannot determine current user');
               return false;
           }
           
           $final_role = $target_role ?: $current_user['role'];
           
           log_message('info', "mark_all_as_read: Role={$final_role}, User={$current_user['user_id']}, Type={$current_user['user_type']}");
           
           // ใช้ Notification_model method ที่แก้ไขแล้ว
           return $this->CI->Notification_model->mark_all_read_by_user(
               $final_role, 
               $current_user['user_id'], 
               $current_user['user_type']
           );
           
       } catch (Exception $e) {
           log_message('error', 'Error in mark_all_as_read: ' . $e->getMessage());
           return false;
       }
   }

   /**
    * *** แก้ไข: ปรับปรุง get_current_user_info ให้รองรับ methods ใหม่และแก้ไข overflow ***
    */
   private function get_current_user_info()
   {
       $user_info = [
           'user_id' => null,
           'user_type' => 'guest',
           'role' => 'guest'
       ];
       
       $mp_id = $this->CI->session->userdata('mp_id');
       $m_id = $this->CI->session->userdata('m_id');
       $mp_email = $this->CI->session->userdata('mp_email');
       $m_email = $this->CI->session->userdata('m_email');
       
       // *** Public User (ใช้ id จาก tbl_member_public แทน mp_id) ***
       if ($mp_id && $mp_email) {
           try {
               $public_user = $this->CI->db->select('id, mp_id')
                                          ->where('mp_email', $mp_email)
                                          ->where('mp_status', 1)
                                          ->get('tbl_member_public')
                                          ->row();
               
               if ($public_user) {
                   $user_info = [
                       'user_id' => $public_user->id, // ใช้ auto increment id แทน mp_id
                       'user_type' => 'public',
                       'role' => self::ROLE_PUBLIC
                   ];
                   
                  // log_message('info', "Public user detected: mp_id={$mp_id}, db_id={$public_user->id}, email={$mp_email}");
               }
           } catch (Exception $e) {
               log_message('error', 'Error getting public user info: ' . $e->getMessage());
           }
       } 
       // *** Staff User ***
       elseif ($m_id && $m_email) {
           // *** แก้ไข: ตรวจสอบ user_id overflow สำหรับ staff ***
           if ($m_id == 2147483647 || $m_id == '2147483647') {
               log_message('warning', "Staff user_id overflow detected: {$m_id}, email: {$m_email}");
               
               // ดึง m_id จาก database โดยตรง
               try {
                   $staff_user = $this->CI->db->select('m_id')
                                              ->where('m_email', $m_email)
                                              ->where('m_status', '1')
                                              ->get('tbl_member')
                                              ->row();
                   
                   if ($staff_user && $staff_user->m_id != 2147483647) {
                       $fixed_user_id = $staff_user->m_id;
                       log_message('info', "Fixed staff user_id: {$m_id} -> {$fixed_user_id}");
                   } else {
                       // Fallback: ใช้ email hash
                       $fixed_user_id = abs(crc32($m_email));
                       log_message('info', "Using email hash as staff user_id: {$fixed_user_id}");
                   }
               } catch (Exception $e) {
                   log_message('error', 'Error fixing staff user_id: ' . $e->getMessage());
                   $fixed_user_id = abs(crc32($m_email)); // Fallback
               }
           } else {
               $fixed_user_id = $m_id;
           }
           
           $user_info = [
               'user_id' => $fixed_user_id,
               'user_type' => 'staff',
               'role' => self::ROLE_STAFF // *** แก้ไข: ใช้ 'staff' แทน 'admin' ***
           ];
           
          // log_message('info', "Staff user detected: original_id={$m_id}, fixed_id={$fixed_user_id}, email={$m_email}");
       }
       
       return $user_info;
   }

   /**
    * *** เพิ่ม: Test method สำหรับ debug ***
    */
   public function test_individual_read_status()
   {
       try {
           $current_user = $this->get_current_user_info();
           
           $test_results = [
               'current_user' => $current_user,
               'session_data' => [
                   'mp_id' => $this->CI->session->userdata('mp_id'),
                   'm_id' => $this->CI->session->userdata('m_id'),
                   'mp_email' => $this->CI->session->userdata('mp_email'),
                   'm_email' => $this->CI->session->userdata('m_email')
               ],
               'database_checks' => [
                   'tbl_notifications_exists' => $this->CI->db->table_exists('tbl_notifications'),
                   'tbl_notification_reads_exists' => $this->CI->db->table_exists('tbl_notification_reads')
               ],
               'methods_available' => [
                   'get_user_notifications' => method_exists($this, 'get_user_notifications'),
                   'get_unread_count' => method_exists($this, 'get_unread_count'),
                   'mark_as_read' => method_exists($this, 'mark_as_read'),
                   'get_total_count' => method_exists($this, 'get_total_count')
               ]
           ];
           
           if ($current_user['user_id'] && $current_user['user_type']) {
               // ทดสอบดึงข้อมูล
               $notifications = $this->get_user_notifications($current_user['role'], 5, 0);
               $unread_count = $this->get_unread_count($current_user['role']);
               $total_count = $this->get_total_count($current_user['role']);
               
               $test_results['test_data'] = [
                   'notifications_count' => count($notifications),
                   'unread_count' => $unread_count,
                   'total_count' => $total_count,
                   'sample_notification' => $notifications ? $notifications[0] : null
               ];
           }
           
           return $test_results;
           
       } catch (Exception $e) {
           return [
               'error' => $e->getMessage(),
               'trace' => $e->getTraceAsString()
           ];
       }
   }

   // *** รักษา methods เดิมไว้เพื่อ backward compatibility ***

   /**
    * แจ้งเตือน Q&A ใหม่ - บันทึก 2 ตัว (Admin + Public)
    */
   public function new_qa($qa_id, $qa_topic, $qa_by)
   {
       try {
           $qa_data = $this->CI->db->get_where('tbl_q_a', array('q_a_id' => $qa_id))->row();
           
           if (!$qa_data) {
               log_message('error', 'Q&A ID not found: ' . $qa_id);
               return false;
           }

           // ตรวจสอบการแจ้งเตือนซ้ำ
           $existing_admin = $this->CI->db->get_where('tbl_notifications', [
               'type' => 'qa',
               'reference_id' => $qa_id,
               'reference_table' => 'tbl_q_a',
               'target_role' => 'staff'
           ])->row();

           $existing_public = $this->CI->db->get_where('tbl_notifications', [
               'type' => self::TYPE_QA_NEW,
               'reference_id' => $qa_id,
               'reference_table' => 'tbl_q_a',
               'target_role' => self::ROLE_PUBLIC
           ])->row();

           if ($existing_admin && $existing_public) {
               log_message('info', 'Q&A notifications already exist for ID: ' . $qa_id);
               return true;
           }

           $result1 = $result2 = true;

           // === 1. สำหรับ Staff ===
           if (!$existing_admin) {
               $result1 = $this->create([
                   'type' => 'qa',
                   'title' => 'กระทู้ Q&A ใหม่',
                   'message' => "มีกระทู้ใหม่: {$qa_topic} โดย {$qa_by}",
                   'reference_id' => $qa_id,
                   'reference_table' => 'tbl_q_a',
                   'priority' => self::PRIORITY_NORMAL,
                   'icon' => 'fas fa-question-circle',
                   'target_role' => 'staff',
                   'url' => base_url('Pages/q_a#comment-' . $qa_id),
                   'data' => [
                       'qa_id' => (int)$qa_id,
                       'topic' => $qa_topic,
                       'detail' => isset($qa_data->q_a_detail) ? mb_substr($qa_data->q_a_detail, 0, 100) . (mb_strlen($qa_data->q_a_detail) > 100 ? '...' : '') : '',
                       'author' => $qa_by,
                       'email' => isset($qa_data->q_a_email) ? $qa_data->q_a_email : '',
                       'country' => isset($qa_data->q_a_country) ? $qa_data->q_a_country : '',
                       'created_at' => isset($qa_data->q_a_datesave) ? $qa_data->q_a_datesave : date('Y-m-d H:i:s'),
                       'view_url' => base_url('Pages/q_a#comment-' . $qa_id),
                       'type' => 'admin_notification'
                   ]
               ]);
           }

           // === 2. สำหรับ Public ===
           if (!$existing_public) {
               $result2 = $this->create([
                   'type' => self::TYPE_QA_NEW,
                   'title' => 'มีกระทู้ถาม-ตอบใหม่',
                   'message' => "มีการตั้งคำถามใหม่: {$qa_topic}",
                   'reference_id' => $qa_id,
                   'reference_table' => 'tbl_q_a',
                   'priority' => self::PRIORITY_NORMAL,
                   'icon' => 'fas fa-question-circle',
                   'target_role' => self::ROLE_PUBLIC,
                   'url' => base_url('Pages/q_a#comment-' . $qa_id),
                   'data' => [
                       'qa_id' => (int)$qa_id,
                       'topic' => $qa_topic,
                       'detail' => isset($qa_data->q_a_detail) ? mb_substr($qa_data->q_a_detail, 0, 100) . (mb_strlen($qa_data->q_a_detail) > 100 ? '...' : '') : '',
                       'author' => $qa_by,
                       'created_at' => isset($qa_data->q_a_datesave) ? $qa_data->q_a_datesave : date('Y-m-d H:i:s'),
                       'view_url' => base_url('Pages/q_a#comment-' . $qa_id),
                       'type' => 'public_notification'
                   ]
               ]);
           }

           log_message('info', "Q&A notifications processed - Admin: " . ($result1 ? 'SUCCESS' : 'FAILED') . ", Public: " . ($result2 ? 'SUCCESS' : 'FAILED'));
           
           return $result1 && $result2;

       } catch (Exception $e) {
           log_message('error', 'Error creating Q&A notification: ' . $e->getMessage());
           return false;
       }
   }

   /**
    * แจ้งเตือนการตอบ Q&A - บันทึก 2 ตัว (Admin + Public)
    */
   public function qa_reply($qa_id, $reply_by, $reply_detail = null)
   {
       try {
           $qa_data = $this->CI->db->get_where('tbl_q_a', array('q_a_id' => $qa_id))->row();
           
           if (!$qa_data) {
               log_message('error', 'Q&A ID not found for reply: ' . $qa_id);
               return false;
           }

           $result1 = $result2 = false;

           // === 1. สำหรับ Staff ===
           $result1 = $this->create([
               'type' => 'qa',
               'title' => 'มีการตอบกระทู้',
               'message' => "มีการตอบกระทู้: \"{$qa_data->q_a_msg}\" โดย {$reply_by}",
               'reference_id' => $qa_id,
               'reference_table' => 'tbl_q_a',
               'priority' => self::PRIORITY_NORMAL,
               'icon' => 'fas fa-reply',
               'target_role' => 'staff',
               'url' => base_url('Pages/q_a#comment-' . $qa_id),
               'data' => [
                   'qa_id' => (int)$qa_id,
                   'original_topic' => $qa_data->q_a_msg,
                   'replied_by' => $reply_by,
                   'reply_detail' => $reply_detail ? mb_substr($reply_detail, 0, 100) . (mb_strlen($reply_detail) > 100 ? '...' : '') : '',
                   'replied_at' => date('Y-m-d H:i:s'),
                   'view_url' => base_url('Pages/q_a#comment-' . $qa_id),
                   'type' => 'admin_reply_notification'
               ]
           ]);

           // === 2. สำหรับ Public ===
           $result2 = $this->create([
               'type' => self::TYPE_QA_REPLY,
               'title' => 'มีการตอบกระทู้',
               'message' => "มีการตอบกระทู้: \"{$qa_data->q_a_msg}\" โดย {$reply_by}",
               'reference_id' => $qa_id,
               'reference_table' => 'tbl_q_a',
               'priority' => self::PRIORITY_NORMAL,
               'icon' => 'fas fa-reply',
               'target_role' => self::ROLE_PUBLIC,
               'url' => base_url('Pages/q_a#comment-' . $qa_id),
               'data' => [
                   'qa_id' => (int)$qa_id,
                   'original_topic' => $qa_data->q_a_msg,
                   'replied_by' => $reply_by,
                   'reply_detail' => $reply_detail ? mb_substr($reply_detail, 0, 100) . (mb_strlen($reply_detail) > 100 ? '...' : '') : '',
                   'replied_at' => date('Y-m-d H:i:s'),
                   'view_url' => base_url('Pages/q_a#comment-' . $qa_id),
                   'type' => 'public_reply_notification'
               ]
           ]);

           log_message('info', "Q&A reply notifications created - Admin: " . ($result1 ? 'SUCCESS' : 'FAILED') . ", Public: " . ($result2 ? 'SUCCESS' : 'FAILED'));
           
           return $result1 && $result2;

       } catch (Exception $e) {
           log_message('error', 'Error creating Q&A reply notification: ' . $e->getMessage());
           return false;
       }
   }

   /**
    * แจ้งเตือนการแก้ไข Q&A
    */
   public function qa_update($qa_id, $updated_topic, $updated_by)
   {
       try {
           $qa_data = $this->CI->db->get_where('tbl_q_a', array('q_a_id' => $qa_id))->row();
           
           if (!$qa_data) {
               log_message('error', 'Q&A ID not found for update: ' . $qa_id);
               return false;
           }

           return $this->create([
               'type' => self::TYPE_QA_UPDATE,
               'title' => 'แก้ไขกระทู้ Q&A',
               'message' => "มีการแก้ไขกระทู้: \"{$updated_topic}\" โดย {$updated_by}",
               'reference_id' => $qa_id,
               'reference_table' => 'tbl_q_a',
               'priority' => self::PRIORITY_NORMAL,
               'icon' => 'fas fa-edit',
               'target_role' => self::ROLE_PUBLIC,
               'url' => base_url('Pages/q_a#comment-' . $qa_id),
               'data' => [
                   'qa_id' => (int)$qa_id,
                   'updated_topic' => $updated_topic,
                   'updated_by' => $updated_by,
                   'updated_at' => date('Y-m-d H:i:s'),
                   'view_url' => site_url('Pages/q_a#comment-' . $qa_id),
                   'type' => 'public_update_notification'
               ]
           ]);

       } catch (Exception $e) {
           log_message('error', 'Error creating Q&A update notification: ' . $e->getMessage());
           return false;
       }
   }

   /**
    * แจ้งเตือน Storage
    */
   public function storage_warning($percentage, $used_space, $total_space)
   {
       $priority = $this->get_storage_priority($percentage);
       $icon = $this->get_storage_icon($percentage);
       
       $message = "พื้นที่จัดเก็บใช้งาน {$percentage}% ({$used_space} GB จาก {$total_space} GB)";
       
       return $this->create([
           'type' => self::TYPE_STORAGE,
           'title' => 'แจ้งเตือนพื้นที่จัดเก็บ',
           'message' => $message,
           'priority' => $priority,
           'icon' => $icon,
           'target_role' => 'staff',
           'url' => site_url('System_reports/storage'),
           'data' => [
               'percentage' => $percentage,
               'used_space' => $used_space,
               'total_space' => $total_space
           ]
       ]);
   }

   /**
    * แจ้งเตือนเรื่องร้องเรียนใหม่
    */
   public function new_complain($complain_id, $complain_topic, $complain_by, $complain_user_id = null, $complain_user_type = null)
   {
       try {
           $complain_data = $this->CI->db->get_where('tbl_complain', array('complain_id' => $complain_id))->row();
           
           if (!$complain_data) {
               log_message('error', 'Complain ID not found: ' . $complain_id);
               return false;
           }

           // ตรวจสอบการแจ้งเตือนซ้ำ
           $existing_admin = $this->CI->db->get_where('tbl_notifications', [
               'type' => 'complain',
               'reference_id' => $complain_id,
               'reference_table' => 'tbl_complain',
               'target_role' => 'staff'
           ])->row();

           $result1 = true;

           // === 1. สำหรับ Staff (สร้างเสมอ) ===
           if (!$existing_admin) {
               $result1 = $this->create([
                   'type' => 'complain',
                   'title' => 'เรื่องร้องเรียนใหม่',
                   'message' => "มีเรื่องร้องเรียนใหม่: {$complain_topic} โดย {$complain_by}",
                   'reference_id' => $complain_id,
                   'reference_table' => 'tbl_complain',
                   'priority' => self::PRIORITY_HIGH,
                   'icon' => 'fas fa-exclamation-triangle',
                   'target_role' => 'staff',
                   'url' => site_url("System_reports/complain_detail/{$complain_id}"),
                   'data' => [
                       'complain_id' => (int)$complain_id,
                       'topic' => $complain_topic,
                       'detail' => isset($complain_data->complain_detail) ? mb_substr($complain_data->complain_detail, 0, 100) . (mb_strlen($complain_data->complain_detail) > 100 ? '...' : '') : '',
                       'complainant' => $complain_by,
                       'phone' => isset($complain_data->complain_phone) ? $complain_data->complain_phone : '',
                       'email' => isset($complain_data->complain_email) ? $complain_data->complain_email : '',
                       'address' => isset($complain_data->complain_address) ? $complain_data->complain_address : '',
                       'user_type' => $complain_user_type ?: 'guest',
                       'created_at' => isset($complain_data->complain_datesave) ? $complain_data->complain_datesave : date('Y-m-d H:i:s'),
                       'view_url' => site_url("System_reports/complain_detail/{$complain_id}"),
                       'type' => 'staff_notification'
                   ]
               ]);
           }

           // === 2. สำหรับ Public Users (เฉพาะ public user ที่ login เท่านั้น) ===
           $result2 = true;
           $individual_result = true;

           if ($complain_user_type === 'public' && $complain_user_id) {
               log_message('info', "Creating notifications for logged-in public user {$complain_user_id}");

               // Individual Notification สำหรับ Public User ที่แจ้งเรื่อง
               $individual_result = $this->create([
                   'type' => self::TYPE_COMPLAIN,
                   'title' => 'คุณได้แจ้งเรื่อง ร้องเรียนใหม่',
                   'message' => "เรื่องร้องเรียนของคุณ \"{$complain_topic}\" ได้รับการบันทึกในระบบเรียบร้อยแล้ว หมายเลขคำร้อง: {$complain_id}",
                   'reference_id' => $complain_id,
                   'reference_table' => 'tbl_complain',
                   'priority' => self::PRIORITY_HIGH,
                   'icon' => 'fas fa-check-circle',
                   'target_role' => self::ROLE_PUBLIC,
                   'target_user_id' => $complain_user_id,
                   'target_user_type' => $complain_user_type,
                   'url' => site_url("Pages/follow_complain?auto_search={$complain_id}"),
                   'data' => [
                       'complain_id' => (int)$complain_id,
                       'topic' => $complain_topic,
                       'detail' => isset($complain_data->complain_detail) ? mb_substr($complain_data->complain_detail, 0, 100) . (mb_strlen($complain_data->complain_detail) > 100 ? '...' : '') : '',
                       'status' => $complain_data->complain_status ?: 'รอรับเรื่อง',
                       'created_at' => isset($complain_data->complain_datesave) ? $complain_data->complain_datesave : date('Y-m-d H:i:s'),
                       'follow_url' => site_url("Pages/follow_complain?auto_search={$complain_id}"),
                       'type' => 'individual_confirmation'
                   ]
               ]);
               
               log_message('info', "Individual notification created for public user {$complain_user_id}: " . ($individual_result ? 'SUCCESS' : 'FAILED'));

               // General Public Notification
               $existing_public = $this->CI->db->get_where('tbl_notifications', [
                   'type' => self::TYPE_COMPLAIN,
                   'reference_id' => $complain_id,
                   'reference_table' => 'tbl_complain',
                   'target_role' => self::ROLE_PUBLIC,
                   'target_user_id IS NULL' => null
               ])->row();

               if (!$existing_public) {
                   $result2 = $this->create([
                       'type' => self::TYPE_COMPLAIN,
                       'title' => 'มีเรื่องร้องเรียนใหม่',
                       'message' => "มีการแจ้งเรื่องร้องเรียนใหม่: {$complain_topic}",
                       'reference_id' => $complain_id,
                       'reference_table' => 'tbl_complain',
                       'priority' => self::PRIORITY_NORMAL,
                       'icon' => 'fas fa-exclamation-circle',
                       'target_role' => self::ROLE_PUBLIC,
                       'url' => site_url("Pages/follow_complain?auto_search={$complain_id}"),
                       'data' => [
                           'complain_id' => (int)$complain_id,
                           'topic' => $complain_topic,
                           'detail' => isset($complain_data->complain_detail) ? mb_substr($complain_data->complain_detail, 0, 100) . (mb_strlen($complain_data->complain_detail) > 100 ? '...' : '') : '',
                           'complainant' => $complain_by,
                           'created_at' => isset($complain_data->complain_datesave) ? $complain_data->complain_datesave : date('Y-m-d H:i:s'),
                           'view_url' => site_url("Pages/follow_complain?auto_search={$complain_id}"),
                           'type' => 'public_general_notification'
                       ]
                   ]);
                   
                   log_message('info', "Public general notification created for complain {$complain_id}: " . ($result2 ? 'SUCCESS' : 'FAILED'));
               }

           } else {
               log_message('info', "Skipping public notifications for user type: {$complain_user_type} (not logged-in public user)");
               $result2 = true;
               $individual_result = true;
           }

           log_message('info', "Complain notifications processed - Staff: " . ($result1 ? 'SUCCESS' : 'FAILED') . 
                              ", Public: " . ($result2 ? 'SUCCESS' : 'FAILED') . 
                              ", Individual: " . ($individual_result ? 'SUCCESS' : 'FAILED'));
           
           return $result1 && $result2 && $individual_result;

       } catch (Exception $e) {
           log_message('error', 'Error creating complain notification: ' . $e->getMessage());
           return false;
       }
   }

   /**
    * อัปเดตสถานะเรื่องร้องเรียน
    */
   public function complain_status_updated($complain_id, $status, $updated_by, $complain_user_id = null, $complain_user_type = null)
   {
       try {
           $complain_data = $this->CI->db->get_where('tbl_complain', array('complain_id' => $complain_id))->row();
           
           if (!$complain_data) {
               log_message('error', 'Complain not found for status update: ' . $complain_id);
               return false;
           }

           $results = [];

           // === 1. สำหรับ Staff (สร้างเสมอ) ===
           $results['staff'] = $this->create([
               'type' => self::TYPE_COMPLAIN,
               'title' => 'อัปเดตสถานะเรื่องร้องเรียน',
               'message' => "เรื่องร้องเรียน #{$complain_id} อัปเดตสถานะเป็น: {$status} โดย {$updated_by}",
               'reference_id' => $complain_id,
               'reference_table' => 'tbl_complain',
               'priority' => self::PRIORITY_NORMAL,
               'icon' => 'fas fa-edit',
               'target_role' => 'staff',
               'url' => site_url("System_reports/complain_detail/{$complain_id}"),
               'data' => [
                   'complain_id' => $complain_id,
                   'topic' => $complain_data->complain_topic,
                   'new_status' => $status,
                   'updated_by' => $updated_by,
                   'updated_at' => date('Y-m-d H:i:s'),
                   'type' => 'staff_status_update'
               ]
           ]);

           // === 2. สำหรับ Public Users - ขยายเงื่อนไข ===
           $final_user_id = $complain_user_id ?: ($complain_data->complain_user_id ?? null);
           $final_user_type = $complain_user_type ?: ($complain_data->complain_user_type ?? null);
           
           log_message('info', "Final user info - ID: " . ($final_user_id ?: 'NULL') . ", Type: " . ($final_user_type ?: 'NULL'));
           
           $should_create_public = false;
           $public_user_id = null;
           $public_user_type = null;
           
           // กรณีที่ 1: เป็น public user ที่ login อยู่
           if ($final_user_type === 'public' && !empty($final_user_id) && $final_user_id > 0) {
               $should_create_public = true;
               $public_user_id = $final_user_id;
               $public_user_type = 'public';
               log_message('info', "Case 1: Direct public user - ID: {$public_user_id}");
           }
           
           // กรณีที่ 2: ตรวจสอบ complain_email ใน tbl_member_public
           else if (!empty($complain_data->complain_email)) {
               $public_member = $this->CI->db->select('id, mp_id, mp_email')
                                           ->where('mp_email', $complain_data->complain_email)
                                           ->where('mp_status', 1)
                                           ->get('tbl_member_public')
                                           ->row();
               
               if ($public_member) {
                   $should_create_public = true;
                   $public_user_id = $public_member->id;
                   $public_user_type = 'public';
                   log_message('info', "Case 2: Found public user by email - ID: {$public_user_id}, Email: {$complain_data->complain_email}");
               }
           }
           
           // กรณีที่ 3: ตรวจสอบ complain_phone ใน tbl_member_public
           else if (!empty($complain_data->complain_phone)) {
               $public_member = $this->CI->db->select('id, mp_id, mp_phone')
                                           ->where('mp_phone', $complain_data->complain_phone)
                                           ->where('mp_status', 1)
                                           ->get('tbl_member_public')
                                           ->row();
               
               if ($public_member) {
                   $should_create_public = true;
                   $public_user_id = $public_member->id;
                   $public_user_type = 'public';
                   log_message('info', "Case 3: Found public user by phone - ID: {$public_user_id}, Phone: {$complain_data->complain_phone}");
               }
           }

           if ($should_create_public) {
               // Individual Notification สำหรับเจ้าของเรื่อง
               $results['individual'] = $this->create([
                   'type' => self::TYPE_COMPLAIN,
                   'title' => 'อัปเดตสถานะเรื่องร้องเรียนของคุณ',
                   'message' => "เรื่องร้องเรียน \"{$complain_data->complain_topic}\" ได้รับการอัปเดตสถานะเป็น: {$status}",
                   'reference_id' => $complain_id,
                   'reference_table' => 'tbl_complain',
                   'priority' => self::PRIORITY_HIGH,
                   'icon' => 'fas fa-bell',
                   'target_role' => self::ROLE_PUBLIC,
                   'target_user_id' => $public_user_id,
                   'target_user_type' => $public_user_type,
                   'url' => site_url("Pages/follow_complain?auto_search={$complain_id}"),
                   'data' => [
                       'complain_id' => $complain_id,
                       'topic' => $complain_data->complain_topic,
                       'new_status' => $status,
                       'updated_by' => $updated_by,
                       'updated_at' => date('Y-m-d H:i:s'),
                       'follow_url' => site_url("Pages/follow_complain?auto_search={$complain_id}"),
                       'type' => 'individual_status_update'
                   ]
               ]);
               
               $results['public'] = true; // ไม่สร้าง general public notification
           } else {
               $results['individual'] = true;
               $results['public'] = true;
           }

           return $results['staff'] && $results['individual'] && $results['public'];

       } catch (Exception $e) {
           log_message('error', 'Error in complain_status_updated: ' . $e->getMessage());
           return false;
       }
   }

   /**
    * แจ้งเตือนการจองคิวใหม่
    */
   public function new_queue($queue_id, $queue_topic, $queue_by)
   {
       return $this->create([
           'type' => self::TYPE_QUEUE,
           'title' => 'การจองคิวใหม่',
           'message' => "มีการจองคิวใหม่: {$queue_topic} โดย {$queue_by}",
           'reference_id' => $queue_id,
           'reference_table' => 'tbl_queue',
           'priority' => self::PRIORITY_NORMAL,
           'icon' => 'fas fa-calendar-plus',
           'target_role' => 'staff',
           'url' => site_url("Queue/follow_queue?auto_search={$queue_id}"),
           'data' => [
               'queue_id' => $queue_id,
               'topic' => $queue_topic,
               'requester' => $queue_by
           ]
       ]);
   }

   /**
    * แจ้งเตือนข้อเสนอแนะใหม่
    */
   public function new_suggestion($suggestion_id, $suggestion_head, $suggestion_by)
   {
       return $this->create([
           'type' => self::TYPE_SUGGESTION,
           'title' => 'ข้อเสนอแนะใหม่',
           'message' => "มีข้อเสนอแนะใหม่: {$suggestion_head} โดย {$suggestion_by}",
           'reference_id' => $suggestion_id,
           'reference_table' => 'tbl_suggestions',
           'priority' => self::PRIORITY_NORMAL,
           'icon' => 'fas fa-lightbulb',
           'target_role' => 'staff',
           'url' => site_url("Suggestions_backend/index"),
           'data' => [
               'suggestion_id' => $suggestion_id,
               'title' => $suggestion_head,
               'suggester' => $suggestion_by
           ]
       ]);
   }

   /**
    * แจ้งเตือนระบบทั่วไป
    */
   public function system_alert($title, $message, $priority = self::PRIORITY_NORMAL, $url = null)
   {
       return $this->create([
           'type' => self::TYPE_SYSTEM,
           'title' => $title,
           'message' => $message,
           'priority' => $priority,
           'icon' => 'fas fa-cog',
           'target_role' => 'staff',
           'url' => $url
       ]);
   }

   /**
    * สร้าง notification แบบ Custom
    */
   public function create_custom_notification($type, $title, $message, $target_role, $options = [])
   {
       $default_options = [
           'priority' => self::PRIORITY_NORMAL,
           'icon' => 'fas fa-bell',
           'url' => null,
           'reference_id' => null,
           'reference_table' => null,
           'target_user_id' => null,
           'target_user_type' => null,
           'data' => []
       ];

       $notification_data = array_merge($default_options, [
           'type' => $type,
           'title' => $title,
           'message' => $message,
           'target_role' => $target_role
       ], $options);

       return $this->create($notification_data);
   }

   /**
    * *** LEGACY: รองรับ method เดิม (Backward Compatibility) ***
    */
   public function get_user_notifications_legacy($user_id, $limit = 10, $target_role = 'system_admin')
   {
       return $this->CI->Notification_model->get_notifications_by_role($target_role, $limit);
   }

   public function get_unread_count_legacy($target_role = 'system_admin')
   {
       return $this->CI->Notification_model->count_unread_by_role($target_role);
   }

   public function mark_as_read_legacy($notification_id, $user_id)
   {
       return $this->CI->Notification_model->mark_as_read($notification_id, $user_id);
   }

   /**
    * *** PRIVATE: สร้างการแจ้งเตือน (Core Method) ***
    */
   private function create($data)
   {
       try {
           if (!isset($this->CI->Notification_model)) {
               $this->CI->load->model('Notification_model');
           }

           if (!$this->CI->db->table_exists('tbl_notifications')) {
               return false;
           }

           // JSON encoding ให้อ่านภาษาไทยได้
           if (isset($data['data']) && is_array($data['data'])) {
               $json_data = json_encode($data['data'], JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
               
               if ($json_data === false) {
                   return false;
               }
               
               $data['data'] = $json_data;
           }

           // เพิ่มข้อมูลพื้นฐานที่จำเป็น โดยจัดการ overflow
           $data['created_at'] = $data['created_at'] ?? date('Y-m-d H:i:s');
           
           // แก้ไข created_by overflow
           $current_user_id = $this->get_current_user_id();
           if ($current_user_id === null || $current_user_id == 2147483647) {
               $data['created_by'] = 0; // หรือ NULL
           } else {
               $data['created_by'] = $current_user_id;
           }
           
           $data['is_read'] = 0;
           $data['is_system'] = 1;
           $data['is_archived'] = 0;

           // support target_user_id และ target_user_type
           if (isset($data['target_user_id'])) {
               if ($data['target_user_id'] == 2147483647 || $data['target_user_id'] == '2147483647') {
                   $data['target_user_id'] = null;
               } else {
                   $data['target_user_id'] = (int)$data['target_user_id'];
               }
           }
           
           if (isset($data['target_user_type'])) {
               $data['target_user_type'] = $data['target_user_type'];
           }

           $result = $this->CI->Notification_model->create_notification($data);
           
           if ($result) {
               $log_msg = 'Notification created successfully: ID=' . $result . ', Type=' . $data['type'] . ', Role=' . $data['target_role'];
               $log_msg .= ', Created By: ' . ($data['created_by'] ?: 'SYSTEM');
               if (isset($data['target_user_id'])) {
                   $log_msg .= ', Target User: ' . ($data['target_user_id'] ?: 'NULL') . ' (' . ($data['target_user_type'] ?? 'unknown') . ')';
               }
               return $result;
           } else {
               return false;
           }
           
       } catch (Exception $e) {
           log_message('error', 'Exception in create notification: ' . $e->getMessage());
           return false;
       }
   }

   /**
    * *** PRIVATE: ดึง user_id ปัจจุบัน ***
    */
   private function get_current_user_id()
   {
       $user_info = $this->get_current_user_info();
       $user_id = $user_info['user_id'] ?: 0;
       
       // แก้ไข overflow
       if ($user_id == 2147483647) {
           return null; // หรือ 0
       }
       
       return $user_id;
   }

   /**
    * *** PRIVATE: กำหนดระดับความสำคัญตาม Storage usage ***
    */
   private function get_storage_priority($percentage)
   {
       if ($percentage >= 95) return self::PRIORITY_CRITICAL;
       if ($percentage >= 85) return self::PRIORITY_HIGH;
       if ($percentage >= 75) return self::PRIORITY_NORMAL;
       return self::PRIORITY_LOW;
   }

   /**
    * *** PRIVATE: กำหนดไอคอนตาม Storage usage ***
    */
   private function get_storage_icon($percentage)
   {
       if ($percentage >= 95) return 'fas fa-exclamation-triangle text-danger';
       if ($percentage >= 85) return 'fas fa-exclamation-circle text-warning';
       return 'fas fa-info-circle text-info';
   }

   /**
    * *** UTILITY: ลบ notification ซ้ำ (สำหรับทำความสะอาด) ***
    */
   public function clean_old_qa_notifications()
   {
       try {
           $this->CI->db->trans_start();
           
           // ลบ notification เก่าที่มี Unicode escape sequences
           $this->CI->db->where('reference_table', 'tbl_q_a')
                       ->where('data LIKE', '%\\\\u%')
                       ->delete('tbl_notifications');
           
           $affected_rows = $this->CI->db->affected_rows();
           
           $this->CI->db->trans_complete();
           
           if ($this->CI->db->trans_status() === FALSE) {
               log_message('error', 'Failed to clean old notifications');
               return false;
           }
           
           return $affected_rows;
           
       } catch (Exception $e) {
           return false;
       }
   }

   /**
    * *** UTILITY: ดึงสถิติ notifications ***
    */
   public function get_notification_stats($role = null, $days = 30)
   {
       $user_info = $this->get_current_user_info();
       $target_role = $role ?: $user_info['role'];
       
       if (!$user_info['user_id']) {
           return [
               'total' => 0,
               'unread' => 0,
               'today' => 0,
               'this_week' => 0
           ];
       }

       try {
           $date_from = date('Y-m-d', strtotime("-{$days} days"));
           $today = date('Y-m-d');
           $week_start = date('Y-m-d', strtotime('monday this week'));

           // ใช้ subquery เพื่อตรวจสอบ read status ของ user
           $sql = "
               SELECT 
                   COUNT(*) as total,
                   SUM(CASE WHEN nr.id IS NULL THEN 1 ELSE 0 END) as unread,
                   SUM(CASE WHEN DATE(n.created_at) = ? THEN 1 ELSE 0 END) as today,
                   SUM(CASE WHEN DATE(n.created_at) >= ? THEN 1 ELSE 0 END) as this_week
               FROM tbl_notifications n
               LEFT JOIN tbl_notification_reads nr ON n.notification_id = nr.notification_id 
                   AND nr.user_id = ? AND nr.user_type = ?
               WHERE n.target_role = ? 
                   AND n.is_archived = 0 
                   AND n.created_at >= ?
           ";

           $query = $this->CI->db->query($sql, [
               $today,
               $week_start,
               $user_info['user_id'],
               $user_info['user_type'],
               $target_role,
               $date_from
           ]);

           $result = $query->row_array();

           return [
               'total' => (int)$result['total'],
               'unread' => (int)$result['unread'],
               'today' => (int)$result['today'],
               'this_week' => (int)$result['this_week']
           ];

       } catch (Exception $e) {
           return [
               'total' => 0,
               'unread' => 0,
               'today' => 0,
               'this_week' => 0
           ];
       }
   }

   /**
    * *** UTILITY: ทดสอบระบบ notification ***
    */
   public function test_notification_system()
   {
       try {
           $user_info = $this->get_current_user_info();
           
           // สร้าง test notification
           $test_id = $this->create_custom_notification(
               'test',
               'ทดสอบระบบแจ้งเตือน',
               'นี่คือการทดสอบระบบแจ้งเตือนแบบ Multi-User',
               $user_info['role'],
               [
                   'priority' => self::PRIORITY_NORMAL,
                   'data' => [
                       'test' => true,
                       'timestamp' => date('Y-m-d H:i:s'),
                       'user_info' => $user_info
                   ]
               ]
           );

           if ($test_id) {
               // ทดสอบการอ่าน
               $read_result = $this->mark_as_read($test_id);
               
               // ทดสอบนับ unread
               $unread_count = $this->get_unread_count();
               
               return [
                   'status' => 'success',
                   'message' => 'Notification system working properly',
                   'test_notification_id' => $test_id,
                   'read_test' => $read_result,
                   'current_unread_count' => $unread_count,
                   'user_info' => $user_info
               ];
           } else {
               return [
                   'status' => 'error',
                   'message' => 'Failed to create test notification',
                   'user_info' => $user_info
               ];
           }

       } catch (Exception $e) {
           return [
               'status' => 'error',
               'message' => 'Exception: ' . $e->getMessage()
           ];
       }
   }
	
	
	
	
	/**
 * *** เพิ่ม: ฟังก์ชันนับ unread สำหรับ Staff พร้อม Corruption Filter ***
 */
public function get_staff_unread_count_with_corruption_filter($user_id = null)
{
    try {
        // ใช้ user_id ที่ส่งมา หรือดึงจาก session
        $final_user_id = $user_id ?: $this->CI->session->userdata('m_id');
        
        if (!$final_user_id) {
            log_message('info', 'get_staff_unread_count_with_corruption_filter: No user ID provided');
            return 0;
        }
        
        // แก้ไข user_id overflow
        if ($final_user_id == 2147483647 || $final_user_id == '2147483647') {
            $m_email = $this->CI->session->userdata('m_email');
            if ($m_email) {
                $staff_user = $this->CI->db->select('m_id')
                                          ->where('m_email', $m_email)
                                          ->where('m_status', '1')
                                          ->get('tbl_member')
                                          ->row();
                
                if ($staff_user && $staff_user->m_id != 2147483647) {
                    $final_user_id = $staff_user->m_id;
                } else {
                    $final_user_id = abs(crc32($m_email));
                }
                
                log_message('info', "Fixed user_id overflow: {$user_id} -> {$final_user_id}");
            }
        }
        
        // ตรวจสอบสิทธิ์ Corruption
        $has_corruption_permission = $this->check_staff_corruption_permission($final_user_id);
        
        log_message('info', "get_staff_unread_count_with_corruption_filter: User={$final_user_id}, Corruption Permission=" . ($has_corruption_permission ? 'YES' : 'NO'));
        
        // สร้าง SQL Query ตามสิทธิ์
        if ($has_corruption_permission) {
            // มีสิทธิ์: รวม corruption notifications
            $sql = "
                SELECT COUNT(*) as unread_count
                FROM tbl_notifications n
                LEFT JOIN tbl_notification_reads nr ON (
                    n.notification_id = nr.notification_id 
                    AND nr.user_id = ? 
                    AND nr.user_type = 'staff'
                )
                WHERE n.target_role = 'staff' 
                  AND n.is_archived = 0 
                  AND nr.id IS NULL
                  AND (
                      (n.reference_table = 'tbl_corruption_reports')
                      OR
                      (n.type IN ('complain_assigned', 'complain_status_update_staff', 'complain_response_required', 'complain_escalated', 'esv_document_assigned', 'esv_document_review_required', 'esv_document_approval_required') 
                       AND n.target_user_id = ? 
                       AND n.target_user_id IS NOT NULL)
                      OR 
                      (n.reference_table IS NULL OR n.reference_table NOT IN ('tbl_corruption_reports'))
                      AND (n.type NOT IN (
                          'complain_assigned', 'complain_status_update_staff', 'complain_response_required', 'complain_escalated',
                          'esv_document_assigned', 'esv_document_review_required', 'esv_document_approval_required'
                      ))
                  )
            ";
            $params = [$final_user_id, $final_user_id];
        } else {
            // ไม่มีสิทธิ์: ซ่อน corruption notifications
            $sql = "
                SELECT COUNT(*) as unread_count
                FROM tbl_notifications n
                LEFT JOIN tbl_notification_reads nr ON (
                    n.notification_id = nr.notification_id 
                    AND nr.user_id = ? 
                    AND nr.user_type = 'staff'
                )
                WHERE n.target_role = 'staff' 
                  AND n.is_archived = 0 
                  AND nr.id IS NULL
                  AND (n.reference_table IS NULL OR n.reference_table != 'tbl_corruption_reports')
                  AND (
                      (n.type IN ('complain_assigned', 'complain_status_update_staff', 'complain_response_required', 'complain_escalated', 'esv_document_assigned', 'esv_document_review_required', 'esv_document_approval_required') 
                       AND n.target_user_id = ? 
                       AND n.target_user_id IS NOT NULL)
                      OR 
                      (n.type NOT IN (
                          'complain_assigned', 'complain_status_update_staff', 'complain_response_required', 'complain_escalated',
                          'esv_document_assigned', 'esv_document_review_required', 'esv_document_approval_required'
                      ))
                  )
            ";
            $params = [$final_user_id, $final_user_id];
        }
        
        // Execute Query
        $query = $this->CI->db->query($sql, $params);
        
        if ($query) {
            $result = $query->row();
            $count = $result ? (int)$result->unread_count : 0;
            
            log_message('info', "get_staff_unread_count_with_corruption_filter: Found {$count} unread notifications");
            return $count;
        }
        
        return 0;
        
    } catch (Exception $e) {
        log_message('error', 'Error in get_staff_unread_count_with_corruption_filter: ' . $e->getMessage());
        return 0;
    }
}

/**
 * *** เพิ่ม: ฟังก์ชันดึง notifications สำหรับ Staff พร้อม Corruption Filter ***
 */
public function get_staff_notifications_with_corruption_filter($user_id = null, $limit = 20, $offset = 0)
{
    try {
        // ใช้ user_id ที่ส่งมา หรือดึงจาก session
        $final_user_id = $user_id ?: $this->CI->session->userdata('m_id');
        
        if (!$final_user_id) {
            log_message('info', 'get_staff_notifications_with_corruption_filter: No user ID provided');
            return [];
        }
        
        // แก้ไข user_id overflow
        if ($final_user_id == 2147483647 || $final_user_id == '2147483647') {
            $m_email = $this->CI->session->userdata('m_email');
            if ($m_email) {
                $staff_user = $this->CI->db->select('m_id')
                                          ->where('m_email', $m_email)
                                          ->where('m_status', '1')
                                          ->get('tbl_member')
                                          ->row();
                
                if ($staff_user && $staff_user->m_id != 2147483647) {
                    $final_user_id = $staff_user->m_id;
                } else {
                    $final_user_id = abs(crc32($m_email));
                }
            }
        }
        
        // ตรวจสอบสิทธิ์ Corruption
        $has_corruption_permission = $this->check_staff_corruption_permission($final_user_id);
        
        log_message('info', "get_staff_notifications_with_corruption_filter: User={$final_user_id}, Limit={$limit}, Offset={$offset}, Corruption Permission=" . ($has_corruption_permission ? 'YES' : 'NO'));
        
        // สร้าง SQL Query ตามสิทธิ์
        if ($has_corruption_permission) {
            // มีสิทธิ์: รวม corruption notifications
            $sql = "
                SELECT n.*, 
                       nr.read_at as user_read_at, 
                       CASE WHEN nr.id IS NOT NULL THEN 1 ELSE 0 END as is_read_by_user
                FROM tbl_notifications n
                LEFT JOIN tbl_notification_reads nr ON (
                    n.notification_id = nr.notification_id 
                    AND nr.user_id = ? 
                    AND nr.user_type = 'staff'
                )
                WHERE n.target_role = 'staff' 
                  AND n.is_archived = 0
                  AND (
                      (n.reference_table = 'tbl_corruption_reports')
                      OR
                      (n.type IN ('complain_assigned', 'complain_status_update_staff', 'complain_response_required', 'complain_escalated', 'esv_document_assigned', 'esv_document_review_required', 'esv_document_approval_required') 
                       AND n.target_user_id = ? 
                       AND n.target_user_id IS NOT NULL)
                      OR 
                      (n.reference_table IS NULL OR n.reference_table NOT IN ('tbl_corruption_reports'))
                      AND (n.type NOT IN (
                          'complain_assigned', 'complain_status_update_staff', 'complain_response_required', 'complain_escalated',
                          'esv_document_assigned', 'esv_document_review_required', 'esv_document_approval_required'
                      ))
                  )
                ORDER BY n.created_at DESC
                LIMIT ? OFFSET ?
            ";
            $params = [$final_user_id, $final_user_id, (int)$limit, (int)$offset];
        } else {
            // ไม่มีสิทธิ์: ซ่อน corruption notifications
            $sql = "
                SELECT n.*, 
                       nr.read_at as user_read_at, 
                       CASE WHEN nr.id IS NOT NULL THEN 1 ELSE 0 END as is_read_by_user
                FROM tbl_notifications n
                LEFT JOIN tbl_notification_reads nr ON (
                    n.notification_id = nr.notification_id 
                    AND nr.user_id = ? 
                    AND nr.user_type = 'staff'
                )
                WHERE n.target_role = 'staff' 
                  AND n.is_archived = 0
                  AND (n.reference_table IS NULL OR n.reference_table != 'tbl_corruption_reports')
                  AND (
                      (n.type IN ('complain_assigned', 'complain_status_update_staff', 'complain_response_required', 'complain_escalated', 'esv_document_assigned', 'esv_document_review_required', 'esv_document_approval_required') 
                       AND n.target_user_id = ? 
                       AND n.target_user_id IS NOT NULL)
                      OR 
                      (n.type NOT IN (
                          'complain_assigned', 'complain_status_update_staff', 'complain_response_required', 'complain_escalated',
                          'esv_document_assigned', 'esv_document_review_required', 'esv_document_approval_required'
                      ))
                  )
                ORDER BY n.created_at DESC
                LIMIT ? OFFSET ?
            ";
            $params = [$final_user_id, $final_user_id, (int)$limit, (int)$offset];
        }
        
        // Execute Query
        $query = $this->CI->db->query($sql, $params);
        
        if ($query && $query->num_rows() > 0) {
            $results = $query->result();
            
            // แปลง JSON data กลับเป็น object
            foreach ($results as $notification) {
                if ($notification->data && is_string($notification->data)) {
                    $notification->data = json_decode($notification->data);
                }
            }
            
            log_message('info', "get_staff_notifications_with_corruption_filter: Found " . count($results) . " notifications");
            return $results;
        }
        
        return [];
        
    } catch (Exception $e) {
        log_message('error', 'Error in get_staff_notifications_with_corruption_filter: ' . $e->getMessage());
        return [];
    }
}

/**
 * *** เพิ่ม: ฟังก์ชันตรวจสอบสิทธิ์ Corruption (คัดลอกจาก Header) ***
 */
private function check_staff_corruption_permission($user_id)
{
    try {
        if (!$this->CI->db->table_exists('tbl_member')) {
            return false;
        }
        
        $this->CI->db->select('m.m_id, m.m_system, m.grant_user_ref_id');
        $this->CI->db->from('tbl_member m');
        $this->CI->db->where('m.m_id', intval($user_id));
        $this->CI->db->where('m.m_status', '1');
        $query = $this->CI->db->get();
        
        if (!$query || $query->num_rows() == 0) {
            log_message('info', 'check_staff_corruption_permission: Staff not found for ID ' . $user_id);
            return false;
        }
        
        $staff_data = $query->row();
        
        log_message('info', 'check_staff_corruption_permission: Staff ID ' . $staff_data->m_id . ', System: ' . $staff_data->m_system . ', Grant: ' . $staff_data->grant_user_ref_id);
        
        // system_admin และ super_admin
        if (in_array($staff_data->m_system, ['system_admin', 'super_admin'])) {
            log_message('info', 'check_staff_corruption_permission: GRANTED - system/super admin');
            return true;
        }
        
        // user_admin ที่มี grant 107
        if ($staff_data->m_system === 'user_admin') {
            if (empty($staff_data->grant_user_ref_id)) {
                log_message('info', 'check_staff_corruption_permission: DENIED - user_admin without grants');
                return false;
            }
            
            try {
                $grant_ids = explode(',', $staff_data->grant_user_ref_id);
                $grant_ids = array_map('trim', $grant_ids);
                
                log_message('info', 'check_staff_corruption_permission: Grant IDs: ' . json_encode($grant_ids));
                
                if (in_array('107', $grant_ids)) {
                    log_message('info', 'check_staff_corruption_permission: GRANTED - found 107 in grants');
                    return true;
                }
                
                // เช็คใน tbl_grant_user
                if ($this->CI->db->table_exists('tbl_grant_user')) {
                    foreach ($grant_ids as $grant_id) {
                        if (empty($grant_id) || !is_numeric($grant_id)) continue;
                        
                        $this->CI->db->select('grant_user_id, grant_user_name');
                        $this->CI->db->from('tbl_grant_user');
                        $this->CI->db->where('grant_user_id', intval($grant_id));
                        $grant_query = $this->CI->db->get();
                        
                        if ($grant_query && $grant_query->num_rows() > 0) {
                            $grant_data = $grant_query->row();
                            
                            if ($grant_data->grant_user_id == 107) {
                                log_message('info', 'check_staff_corruption_permission: GRANTED - grant_user_id = 107');
                                return true;
                            }
                            
                            $name_lower = mb_strtolower($grant_data->grant_user_name, 'UTF-8');
                            if (strpos($name_lower, 'ทุจริต') !== false) {
                                log_message('info', 'check_staff_corruption_permission: GRANTED - corruption-related grant');
                                return true;
                            }
                        }
                    }
                }
                
                log_message('info', 'check_staff_corruption_permission: DENIED - no valid corruption grants');
                return false;
                
            } catch (Exception $e) {
                log_message('error', 'check_staff_corruption_permission: Error checking grants: ' . $e->getMessage());
                // Fallback check
                $has_107 = (strpos($staff_data->grant_user_ref_id, '107') !== false);
                log_message('info', 'check_staff_corruption_permission: Fallback check result: ' . ($has_107 ? 'GRANTED' : 'DENIED'));
                return $has_107;
            }
        }
        
        log_message('info', 'check_staff_corruption_permission: DENIED - not authorized system: ' . $staff_data->m_system);
        return false;
        
    } catch (Exception $e) {
        log_message('error', 'check_staff_corruption_permission: Exception: ' . $e->getMessage());
        return false;
    }
}

/**
 * *** เพิ่ม: Override method เดิมให้ใช้ corruption filter เมื่อเป็น staff ***
 */
public function get_unread_count($target_role = null)
{
    try {
        // ถ้าเป็น staff role ให้ใช้ corruption filter
        if ($target_role === 'staff' || $target_role === null) {
            $current_user = $this->get_current_user_info();
            
            if ($current_user['role'] === 'staff' || $current_user['user_type'] === 'staff') {
                return $this->get_staff_unread_count_with_corruption_filter($current_user['user_id']);
            }
        }
        
        // สำหรับ role อื่นๆ ใช้ method เดิม
        $current_user = $this->get_current_user_info();
        
        if (!$current_user['user_id'] || !$current_user['user_type']) {
            log_message('info', 'get_unread_count: Cannot determine current user - returning 0');
            return 0;
        }
        
        $final_role = $target_role ?: $current_user['role'];
        
        return $this->CI->Notification_model->count_unread_for_user(
            $final_role, 
            $current_user['user_id'], 
            $current_user['user_type']
        );
        
    } catch (Exception $e) {
        log_message('error', 'Error in get_unread_count: ' . $e->getMessage());
        return 0;
    }
}

/**
 * *** เพิ่ม: Override method เดิมให้ใช้ corruption filter เมื่อเป็น staff ***
 */
public function get_user_notifications($target_role = null, $limit = 20, $offset = 0)
{
    try {
        // ถ้าเป็น staff role ให้ใช้ corruption filter
        if ($target_role === 'staff' || $target_role === null) {
            $current_user = $this->get_current_user_info();
            
            if ($current_user['role'] === 'staff' || $current_user['user_type'] === 'staff') {
                return $this->get_staff_notifications_with_corruption_filter($current_user['user_id'], $limit, $offset);
            }
        }
        
        // สำหรับ role อื่นๆ ใช้ method เดิม
        $current_user = $this->get_current_user_info();
        
        if (!$current_user['user_id'] || !$current_user['user_type']) {
            log_message('info', 'get_user_notifications: Cannot determine current user');
            return [];
        }
        
        $final_role = $target_role ?: $current_user['role'];
        
        return $this->CI->Notification_model->get_by_role_for_user(
            $final_role, 
            $current_user['user_id'], 
            $current_user['user_type'], 
            $limit, 
            $offset
        );
        
    } catch (Exception $e) {
        log_message('error', 'Error in get_user_notifications: ' . $e->getMessage());
        return [];
    }
}
	
	
	
}