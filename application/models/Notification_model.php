<?php
defined('BASEPATH') or exit('No direct script access allowed');

/**
* Notification Model - รองรับระบบ Multi-User + แก้ไขปัญหา User ID Overflow + เพิ่ม Corruption Filter สำหรับ Staff
* Version: 2.2 - รองรับหลาย User แบบ Individual Read Status + Corruption Notifications สำหรับ Staff (Fixed)
*/
class Notification_model extends CI_Model
{
   private $table = 'tbl_notifications';
   private $reads_table = 'tbl_notification_reads';
   
   public function __construct()
   {
       parent::__construct();
       $this->load->database();
       
       // *** สร้าง table tbl_notification_reads ถ้ายังไม่มี ***
       $this->create_reads_table_if_not_exists();
   }

   /**
    * สร้าง table สำหรับเก็บสถานะการอ่านของแต่ละ user
    */
   private function create_reads_table_if_not_exists()
   {
       if (!$this->db->table_exists($this->reads_table)) {
           $sql = "
               CREATE TABLE `{$this->reads_table}` (
                 `id` int(11) NOT NULL AUTO_INCREMENT,
                 `notification_id` int(11) NOT NULL,
                 `user_id` varchar(50) NOT NULL,
                 `user_type` enum('public','staff','admin','system_admin') NOT NULL,
                 `read_at` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
                 PRIMARY KEY (`id`),
                 UNIQUE KEY `unique_read` (`notification_id`, `user_id`, `user_type`),
                 KEY `idx_notification` (`notification_id`),
                 KEY `idx_user` (`user_id`, `user_type`),
                 KEY `idx_read_at` (`read_at`)
               ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci
           ";
           
           try {
               $this->db->query($sql);
               log_message('info', 'Created table: ' . $this->reads_table);
           } catch (Exception $e) {
               log_message('error', 'Failed to create reads table: ' . $e->getMessage());
           }
       }
   }

   /**
    * สร้างการแจ้งเตือนใหม่
    */
   public function create_notification($data)
   {
       try {
           // ตรวจสอบข้อมูลที่จำเป็น
           if (empty($data['type']) || empty($data['title']) || empty($data['message'])) {
               log_message('error', 'Missing required notification data');
               return false;
           }

           $notification = [
               'type' => $data['type'],
               'title' => $data['title'],
               'message' => $data['message'],
               'reference_id' => $data['reference_id'] ?? null,
               'reference_table' => $data['reference_table'] ?? null,
               'target_user_id' => $data['target_user_id'] ?? null,
               'target_role' => $data['target_role'] ?? null,
               'priority' => $data['priority'] ?? 'normal',
               'icon' => $data['icon'] ?? 'fas fa-bell',
               'url' => $data['url'] ?? null,
               'data' => $data['data'] ?? null,
               'is_read' => 0, // *** เก็บไว้เพื่อ backward compatibility ***
               'is_system' => $data['is_system'] ?? 1,
               'is_archived' => 0,
               'created_at' => $data['created_at'] ?? date('Y-m-d H:i:s'),
               'created_by' => $data['created_by'] ?? $this->get_current_user_id()
           ];

           log_message('debug', 'Inserting notification: ' . print_r($notification, true));

           $result = $this->db->insert($this->table, $notification);
           
           if ($result) {
               $insert_id = $this->db->insert_id();
               log_message('debug', 'Notification inserted with ID: ' . $insert_id);
               return $insert_id;
           } else {
               $error = $this->db->error();
               log_message('error', 'Database insert failed: ' . print_r($error, true));
               return false;
           }
           
       } catch (Exception $e) {
           log_message('error', 'Exception in create_notification: ' . $e->getMessage());
           return false;
       }
   }

/**
 * *** แก้ไข FINAL: ดึง notifications สำหรับ user คนนั้นๆ พร้อมสถานะการอ่าน + Staff Corruption Filter ***
 */
public function get_by_role_for_user($role, $user_id, $user_type, $limit = 20, $offset = 0)
{
    try {
        // *** แปลง user_type ให้ตรงกับ ENUM ใน database ***
        $db_user_type = $this->map_user_type_to_db($user_type);
        
        log_message('debug', "get_by_role_for_user called with: role={$role}, user_id={$user_id}, user_type={$user_type} -> db_user_type={$db_user_type}");
        
        // *** แก้ไข FINAL: เพิ่มการกรอง Corruption ตาม reference_table สำหรับ Staff ***
        if ($role === 'staff' && $db_user_type === 'staff') {
            // *** สำหรับ STAFF: ใช้ logic พิเศษ ***
            $sql = "
                SELECT n.*, 
                       nr.read_at as user_read_at, 
                       CASE WHEN nr.id IS NOT NULL THEN 1 ELSE 0 END as is_read_by_user
                FROM {$this->table} n
                LEFT JOIN {$this->reads_table} nr ON (
                    n.notification_id = nr.notification_id 
                    AND nr.user_id = ? 
                    AND nr.user_type = ?
                )
                WHERE n.target_role = ? 
                  AND n.is_archived = 0
                  AND (
                      -- *** STAFF: corruption reports ตาม reference_table = tbl_corruption_reports และ target_user_id = current_staff_id ***
                      (n.reference_table = 'tbl_corruption_reports' 
                       AND n.target_user_id = ? 
                       AND n.target_user_id IS NOT NULL)
                      OR
                      -- *** STAFF: complain assignments ต้องมี target_user_id = current_staff_id ***
                      (n.type IN ('complain_assigned', 'complain_status_update_staff', 'complain_response_required', 'complain_escalated') 
                       AND n.target_user_id = ? 
                       AND n.target_user_id IS NOT NULL)
                      OR
                      -- *** STAFF: ESV assignments ต้องมี target_user_id = current_staff_id ***
                      (n.type IN ('esv_document_assigned', 'esv_document_review_required', 'esv_document_approval_required') 
                       AND n.target_user_id = ? 
                       AND n.target_user_id IS NOT NULL)
                      OR 
                      -- *** STAFF: type อื่นๆ (เช่น qa, system) ใช้ logic เดิม (ไม่เช็ค target_user_id) ***
                      (n.reference_table IS NULL OR n.reference_table NOT IN ('tbl_corruption_reports'))
                      AND (n.type NOT IN (
                          'complain_assigned', 'complain_status_update_staff', 'complain_response_required', 'complain_escalated',
                          'esv_document_assigned', 'esv_document_review_required', 'esv_document_approval_required'
                      ))
                  )
                ORDER BY n.created_at DESC
                LIMIT ? OFFSET ?
            ";
            
            $params = [
                $user_id, $db_user_type, $role,  // Base parameters
                $user_id,                         // corruption (reference_table)
                $user_id,                         // complain
                $user_id,                         // esv
                (int)$limit, (int)$offset         // Pagination
            ];
            
        } elseif ($role === 'public' && $db_user_type === 'public') {
            // *** สำหรับ PUBLIC: ใช้ logic เดิม ***
            $sql = "
                SELECT n.*, 
                       nr.read_at as user_read_at, 
                       CASE WHEN nr.id IS NOT NULL THEN 1 ELSE 0 END as is_read_by_user
                FROM {$this->table} n
                LEFT JOIN {$this->reads_table} nr ON (
                    n.notification_id = nr.notification_id 
                    AND nr.user_id = ? 
                    AND nr.user_type = ?
                )
                WHERE n.target_role = ? 
                  AND n.is_archived = 0
                  AND (
                      -- *** PUBLIC: complain และ queue ต้องมี target_user_id = current_user_id ***
                      (n.type IN ('complain', 'queue', 'queue_cancelled', 'queue_status_update') 
                       AND n.target_user_id = ? 
                       AND n.target_user_id IS NOT NULL)
                      OR 
                      -- *** PUBLIC: corruption reports (เฉพาะของตัวเอง) ***
                      (n.type IN ('corruption_report_confirmation', 'new_corruption_report', 'corruption_status_update', 'corruption_assigned', 'corruption_response_added') 
                       AND n.target_user_id = ? 
                       AND n.target_user_id IS NOT NULL)
                      OR 
                      -- *** PUBLIC: ESV (เอกสารออนไลน์) ต้องมี target_user_id = current_user_id ***
                      (n.type IN ('esv_document', 'esv_document_update', 'esv_important_update', 'esv_document_created', 'esv_document_status_update', 'esv_document_note_added', 'esv_document_file_added', 'esv_document_completed', 'esv_document_rejected', 'esv_document_cancelled') 
                       AND n.target_user_id = ? 
                       AND n.target_user_id IS NOT NULL)
                      OR 
                      -- *** PUBLIC: เงินสนับสนุนเด็ก (kid_aw_ods) ต้องมี target_user_id = current_user_id ***
                      (n.type IN ('kid_aw_ods', 'kid_aw_ods_update', 'kid_aw_ods_data_update', 'kid_aw_ods_note', 'kid_aw_ods_deleted', 'kid_aw_ods_critical_update', 'kid_aw_ods_data_update_confirm') 
                       AND n.target_user_id = ? 
                       AND n.target_user_id IS NOT NULL)
                      OR 
                      -- *** PUBLIC: เบี้ยยังชีพผู้สูงอายุ (elderly_aw_ods) ต้องมี target_user_id = current_user_id ***
                      (n.type IN ('elderly_aw_ods', 'elderly_aw_ods_update', 'elderly_aw_ods_data_update', 'elderly_aw_ods_note', 'elderly_aw_ods_deleted', 'elderly_aw_ods_critical_update', 'elderly_aw_ods_data_update_confirm') 
                       AND n.target_user_id = ? 
                       AND n.target_user_id IS NOT NULL)
                      OR 
                      -- *** PUBLIC: type อื่นๆ (เช่น qa, system) ใช้ logic เดิม ***
                      (n.type NOT IN (
                          'complain', 'queue', 'queue_cancelled', 'queue_status_update', 
                          'corruption_report_confirmation', 'new_corruption_report', 'corruption_status_update', 'corruption_assigned', 'corruption_response_added',
                          'esv_document', 'esv_document_update', 'esv_important_update', 'esv_document_created', 'esv_document_status_update', 'esv_document_note_added', 'esv_document_file_added', 'esv_document_completed', 'esv_document_rejected', 'esv_document_cancelled', 
                          'kid_aw_ods', 'kid_aw_ods_update', 'kid_aw_ods_data_update', 'kid_aw_ods_note', 'kid_aw_ods_deleted', 'kid_aw_ods_critical_update', 'kid_aw_ods_data_update_confirm', 
                          'elderly_aw_ods', 'elderly_aw_ods_update', 'elderly_aw_ods_data_update', 'elderly_aw_ods_note', 'elderly_aw_ods_deleted', 'elderly_aw_ods_critical_update', 'elderly_aw_ods_data_update_confirm'
                      ))
                  )
                ORDER BY n.created_at DESC
                LIMIT ? OFFSET ?
            ";
            
            $params = [
                $user_id, $db_user_type, $role,  // Base parameters
                $user_id,                         // complain/queue
                $user_id,                         // corruption
                $user_id,                         // esv
                $user_id,                         // kid_aw_ods
                $user_id,                         // elderly_aw_ods
                (int)$limit, (int)$offset         // Pagination
            ];
            
        } else {
            // *** FALLBACK: สำหรับ role อื่นๆ ***
            $sql = "
                SELECT n.*, 
                       nr.read_at as user_read_at, 
                       CASE WHEN nr.id IS NOT NULL THEN 1 ELSE 0 END as is_read_by_user
                FROM {$this->table} n
                LEFT JOIN {$this->reads_table} nr ON (
                    n.notification_id = nr.notification_id 
                    AND nr.user_id = ? 
                    AND nr.user_type = ?
                )
                WHERE n.target_role = ? 
                  AND n.is_archived = 0
                ORDER BY n.created_at DESC
                LIMIT ? OFFSET ?
            ";
            
            $params = [
                $user_id, $db_user_type, $role,
                (int)$limit, (int)$offset
            ];
        }
        
        log_message('debug', 'Executing SQL with params: ' . print_r($params, true));
        
        $query = $this->db->query($sql, $params);
        
        if ($query === FALSE) {
            $error = $this->db->error();
            log_message('error', 'SQL Query failed: ' . print_r($error, true));
            log_message('error', 'Failed SQL: ' . $sql);
            return [];
        }
        
        log_message('debug', 'get_by_role_for_user with Staff Corruption filter executed successfully');
        log_message('debug', 'Last query: ' . $this->db->last_query());
        log_message('debug', "Found {$query->num_rows()} notifications for role: {$role}, user: {$user_id} ({$db_user_type})");
        
        if ($query->num_rows() > 0) {
            $results = $query->result();
            
            // แปลง JSON data กลับเป็น object
            foreach ($results as $notification) {
                if ($notification->data && is_string($notification->data)) {
                    $notification->data = json_decode($notification->data);
                }
                
                // Debug แต่ละ notification
                $read_status = isset($notification->is_read_by_user) ? 
                    ($notification->is_read_by_user ? 'READ' : 'UNREAD') : 'UNKNOWN';
                log_message('debug', "Notification {$notification->notification_id}: {$notification->title} (Type: {$notification->type}) - Status: {$read_status}");
            }
            
            return $results;
        }
        
        log_message('info', 'No notifications found for user');
        return [];
        
    } catch (Exception $e) {
        log_message('error', 'Exception in get_by_role_for_user: ' . $e->getMessage());
        log_message('error', 'Exception trace: ' . $e->getTraceAsString());
        
        if ($this->db->error()['code'] != 0) {
            log_message('error', 'Database error: ' . print_r($this->db->error(), true));
        }
        
        return [];
    }
}

/**
 * *** แก้ไข FINAL: นับ unread notifications สำหรับ user คนนั้นๆ + Staff Corruption Filter ***
 */
public function count_unread_for_user($role, $user_id, $user_type)
{
    try {
        // *** แปลง user_type ให้ตรงกับ ENUM ใน database ***
        $db_user_type = $this->map_user_type_to_db($user_type);
        
        log_message('debug', "count_unread_for_user called with: role={$role}, user_id={$user_id}, user_type={$user_type} -> db_user_type={$db_user_type}");
        
        // *** แก้ไข FINAL: เพิ่มการกรอง Corruption ตาม reference_table สำหรับ Staff ***
        if ($role === 'staff' && $db_user_type === 'staff') {
            // *** สำหรับ STAFF: ใช้ logic พิเศษ ***
            $sql = "
                SELECT COUNT(*) as unread_count
                FROM {$this->table} n
                LEFT JOIN {$this->reads_table} nr ON (
                    n.notification_id = nr.notification_id 
                    AND nr.user_id = ? 
                    AND nr.user_type = ?
                )
                WHERE n.target_role = ? 
                  AND n.is_archived = 0 
                  AND nr.id IS NULL
                  AND (
                      -- *** STAFF: corruption reports ตาม reference_table = tbl_corruption_reports และ target_user_id = current_staff_id ***
                      (n.reference_table = 'tbl_corruption_reports' 
                       AND n.target_user_id = ? 
                       AND n.target_user_id IS NOT NULL)
                      OR
                      -- *** STAFF: complain assignments ต้องมี target_user_id = current_staff_id ***
                      (n.type IN ('complain_assigned', 'complain_status_update_staff', 'complain_response_required', 'complain_escalated') 
                       AND n.target_user_id = ? 
                       AND n.target_user_id IS NOT NULL)
                      OR
                      -- *** STAFF: ESV assignments ต้องมี target_user_id = current_staff_id ***
                      (n.type IN ('esv_document_assigned', 'esv_document_review_required', 'esv_document_approval_required') 
                       AND n.target_user_id = ? 
                       AND n.target_user_id IS NOT NULL)
                      OR 
                      -- *** STAFF: type อื่นๆ (เช่น qa, system) ใช้ logic เดิม (ไม่เช็ค target_user_id) ***
                      (n.reference_table IS NULL OR n.reference_table NOT IN ('tbl_corruption_reports'))
                      AND (n.type NOT IN (
                          'complain_assigned', 'complain_status_update_staff', 'complain_response_required', 'complain_escalated',
                          'esv_document_assigned', 'esv_document_review_required', 'esv_document_approval_required'
                      ))
                  )
            ";
            
            $params = [
                $user_id, $db_user_type, $role,  // Base parameters
                $user_id,                         // corruption (reference_table)
                $user_id,                         // complain
                $user_id                          // esv
            ];
            
        } elseif ($role === 'public' && $db_user_type === 'public') {
            // *** สำหรับ PUBLIC: ใช้ logic เดิม ***
            $sql = "
                SELECT COUNT(*) as unread_count
                FROM {$this->table} n
                LEFT JOIN {$this->reads_table} nr ON (
                    n.notification_id = nr.notification_id 
                    AND nr.user_id = ? 
                    AND nr.user_type = ?
                )
                WHERE n.target_role = ? 
                  AND n.is_archived = 0 
                  AND nr.id IS NULL
                  AND (
                      -- *** PUBLIC: complain และ queue ต้องมี target_user_id = current_user_id ***
                      (n.type IN ('complain', 'queue', 'queue_cancelled', 'queue_status_update') 
                       AND n.target_user_id = ? 
                       AND n.target_user_id IS NOT NULL)
                      OR 
                      -- *** PUBLIC: corruption reports (เฉพาะของตัวเอง) ***
                      (n.type IN ('corruption_report_confirmation', 'new_corruption_report', 'corruption_status_update', 'corruption_assigned', 'corruption_response_added') 
                       AND n.target_user_id = ? 
                       AND n.target_user_id IS NOT NULL)
                      OR 
                      -- *** PUBLIC: ESV (เอกสารออนไลน์) ต้องมี target_user_id = current_user_id ***
                      (n.type IN ('esv_document', 'esv_document_update', 'esv_important_update', 'esv_document_created', 'esv_document_status_update', 'esv_document_note_added', 'esv_document_file_added', 'esv_document_completed', 'esv_document_rejected', 'esv_document_cancelled') 
                       AND n.target_user_id = ? 
                       AND n.target_user_id IS NOT NULL)
                      OR 
                      -- *** PUBLIC: เงินสนับสนุนเด็ก (kid_aw_ods) ต้องมี target_user_id = current_user_id ***
                      (n.type IN ('kid_aw_ods', 'kid_aw_ods_update', 'kid_aw_ods_data_update', 'kid_aw_ods_note', 'kid_aw_ods_deleted', 'kid_aw_ods_critical_update', 'kid_aw_ods_data_update_confirm') 
                       AND n.target_user_id = ? 
                       AND n.target_user_id IS NOT NULL)
                      OR 
                      -- *** PUBLIC: เบี้ยยังชีพผู้สูงอายุ (elderly_aw_ods) ต้องมี target_user_id = current_user_id ***
                      (n.type IN ('elderly_aw_ods', 'elderly_aw_ods_update', 'elderly_aw_ods_data_update', 'elderly_aw_ods_note', 'elderly_aw_ods_deleted', 'elderly_aw_ods_critical_update', 'elderly_aw_ods_data_update_confirm') 
                       AND n.target_user_id = ? 
                       AND n.target_user_id IS NOT NULL)
                      OR 
                      -- *** PUBLIC: type อื่นๆ (เช่น qa, system) ใช้ logic เดิม ***
                      (n.type NOT IN (
                          'complain', 'queue', 'queue_cancelled', 'queue_status_update', 
                          'corruption_report_confirmation', 'new_corruption_report', 'corruption_status_update', 'corruption_assigned', 'corruption_response_added',
                          'esv_document', 'esv_document_update', 'esv_important_update', 'esv_document_created', 'esv_document_status_update', 'esv_document_note_added', 'esv_document_file_added', 'esv_document_completed', 'esv_document_rejected', 'esv_document_cancelled', 
                          'kid_aw_ods', 'kid_aw_ods_update', 'kid_aw_ods_data_update', 'kid_aw_ods_note', 'kid_aw_ods_deleted', 'kid_aw_ods_critical_update', 'kid_aw_ods_data_update_confirm', 
                          'elderly_aw_ods', 'elderly_aw_ods_update', 'elderly_aw_ods_data_update', 'elderly_aw_ods_note', 'elderly_aw_ods_deleted', 'elderly_aw_ods_critical_update', 'elderly_aw_ods_data_update_confirm'
                      ))
                  )
            ";
            
            $params = [
                $user_id, $db_user_type, $role,  // Base parameters
                $user_id,                         // complain/queue
                $user_id,                         // corruption
                $user_id,                         // esv
                $user_id,                         // kid_aw_ods
                $user_id                          // elderly_aw_ods
            ];
            
        } else {
            // *** FALLBACK: สำหรับ role อื่นๆ ***
            $sql = "
                SELECT COUNT(*) as unread_count
                FROM {$this->table} n
                LEFT JOIN {$this->reads_table} nr ON (
                    n.notification_id = nr.notification_id 
                    AND nr.user_id = ? 
                    AND nr.user_type = ?
                )
                WHERE n.target_role = ? 
                  AND n.is_archived = 0 
                  AND nr.id IS NULL
            ";
            
            $params = [
                $user_id, $db_user_type, $role
            ];
        }
        
        log_message('debug', 'Executing count SQL with params: ' . print_r($params, true));
        
        $query = $this->db->query($sql, $params);
        
        if ($query === FALSE) {
            log_message('error', 'Query execution failed in count_unread_for_user');
            return 0;
        }
        
        $result = $query->row();
        $count = $result ? (int)$result->unread_count : 0;
        
        log_message('debug', 'count_unread_for_user with Staff Corruption filter executed successfully');
        log_message('debug', "Unread count - Role: {$role}, User: {$user_id} ({$db_user_type}), Count: {$count}");
        
        return $count;
        
    } catch (Exception $e) {
        log_message('error', 'Exception in count_unread_for_user: ' . $e->getMessage());
        
        if ($this->db->error()['code'] != 0) {
            log_message('error', 'Database error: ' . print_r($this->db->error(), true));
        }
        
        return 0;
    }
}

   /**
 * *** แก้ไข: ทำเครื่องหมายว่าอ่านแล้วสำหรับ user คนนั้นๆ ***
 */
public function mark_read_by_user($notification_id, $user_id, $user_type)
{
    try {
        // *** แก้ไข: แปลง user_type ให้ตรงกับ ENUM ใน database ***
        $db_user_type = $this->map_user_type_to_db($user_type);
        
        // ตรวจสอบว่าอ่านแล้วหรือยัง
        $check_sql = "SELECT id FROM {$this->reads_table} WHERE notification_id = ? AND user_id = ? AND user_type = ?";
        $check_query = $this->db->query($check_sql, [$notification_id, $user_id, $db_user_type]);
        
        if ($check_query->num_rows() > 0) {
            log_message('debug', "User {$user_id} already read notification {$notification_id}");
            return true; // อ่านแล้ว
        }
        
        // บันทึกสถานะการอ่าน
        $insert_sql = "INSERT INTO {$this->reads_table} (notification_id, user_id, user_type, read_at) VALUES (?, ?, ?, NOW())";
        $result = $this->db->query($insert_sql, [$notification_id, $user_id, $db_user_type]);
        
        if ($result) {
            log_message('info', "User {$user_id} ({$db_user_type}) marked notification {$notification_id} as read");
        } else {
            $error = $this->db->error();
            log_message('error', "Failed to mark notification {$notification_id} as read for user {$user_id}: " . print_r($error, true));
        }
        
        return $result;
        
    } catch (Exception $e) {
        log_message('error', 'Exception in mark_read_by_user: ' . $e->getMessage());
        log_message('error', 'SQL Error: ' . $this->db->error()['message']);
        return false;
    }
}

 public function mark_all_read_by_user($role, $user_id, $user_type)
{
    try {
        // *** แปลง user_type ให้ตรงกับ ENUM ใน database ***
        $db_user_type = $this->map_user_type_to_db($user_type);
        
        // *** แก้ไข FINAL: ดึง notification IDs ที่ยังไม่อ่าน + กรอง Corruption ตาม reference_table ***
        if ($role === 'staff' && $db_user_type === 'staff') {
            // *** สำหรับ STAFF: ใช้ logic พิเศษ ***
            $sql = "
                SELECT n.notification_id
                FROM {$this->table} n
                LEFT JOIN {$this->reads_table} nr ON (
                    n.notification_id = nr.notification_id 
                    AND nr.user_id = ? 
                    AND nr.user_type = ?
                )
                WHERE n.target_role = ? 
                  AND n.is_archived = 0 
                  AND nr.id IS NULL
                  AND (
                      -- *** STAFF: corruption reports ตาม reference_table = tbl_corruption_reports และ target_user_id = current_staff_id ***
                      (n.reference_table = 'tbl_corruption_reports' 
                       AND n.target_user_id = ? 
                       AND n.target_user_id IS NOT NULL)
                      OR
                      -- *** STAFF: complain assignments ต้องมี target_user_id = current_staff_id ***
                      (n.type IN ('complain_assigned', 'complain_status_update_staff', 'complain_response_required', 'complain_escalated') 
                       AND n.target_user_id = ? 
                       AND n.target_user_id IS NOT NULL)
                      OR
                      -- *** STAFF: ESV assignments ต้องมี target_user_id = current_staff_id ***
                      (n.type IN ('esv_document_assigned', 'esv_document_review_required', 'esv_document_approval_required') 
                       AND n.target_user_id = ? 
                       AND n.target_user_id IS NOT NULL)
                      OR 
                      -- *** STAFF: type อื่นๆ (เช่น qa, system) ใช้ logic เดิม (ไม่เช็ค target_user_id) ***
                      (n.reference_table IS NULL OR n.reference_table NOT IN ('tbl_corruption_reports'))
                      AND (n.type NOT IN (
                          'complain_assigned', 'complain_status_update_staff', 'complain_response_required', 'complain_escalated',
                          'esv_document_assigned', 'esv_document_review_required', 'esv_document_approval_required'
                      ))
                  )
            ";
            
            $params = [
                $user_id, $db_user_type, $role,  // Base parameters
                $user_id,                         // corruption (reference_table)
                $user_id,                         // complain
                $user_id                          // esv
            ];
            
        } elseif ($role === 'public' && $db_user_type === 'public') {
            // *** สำหรับ PUBLIC: ใช้ logic เดิม ***
            $sql = "
                SELECT n.notification_id
                FROM {$this->table} n
                LEFT JOIN {$this->reads_table} nr ON (
                    n.notification_id = nr.notification_id 
                    AND nr.user_id = ? 
                    AND nr.user_type = ?
                )
                WHERE n.target_role = ? 
                  AND n.is_archived = 0 
                  AND nr.id IS NULL
                  AND (
                      -- *** PUBLIC: complain และ queue ต้องมี target_user_id = current_user_id ***
                      (n.type IN ('complain', 'queue', 'queue_cancelled', 'queue_status_update') 
                       AND n.target_user_id = ? 
                       AND n.target_user_id IS NOT NULL)
                      OR 
                      -- *** PUBLIC: corruption reports (เฉพาะของตัวเอง) ***
                      (n.type IN ('corruption_report_confirmation', 'new_corruption_report', 'corruption_status_update', 'corruption_assigned', 'corruption_response_added') 
                       AND n.target_user_id = ? 
                       AND n.target_user_id IS NOT NULL)
                      OR 
                      -- *** PUBLIC: ESV (เอกสารออนไลน์) ต้องมี target_user_id = current_user_id ***
                      (n.type IN ('esv_document', 'esv_document_update', 'esv_important_update', 'esv_document_created', 'esv_document_status_update', 'esv_document_note_added', 'esv_document_file_added', 'esv_document_completed', 'esv_document_rejected', 'esv_document_cancelled') 
                       AND n.target_user_id = ? 
                       AND n.target_user_id IS NOT NULL)
                      OR 
                      -- *** PUBLIC: เงินสนับสนุนเด็ก (kid_aw_ods) ต้องมี target_user_id = current_user_id ***
                      (n.type IN ('kid_aw_ods', 'kid_aw_ods_update', 'kid_aw_ods_data_update', 'kid_aw_ods_note', 'kid_aw_ods_deleted', 'kid_aw_ods_critical_update', 'kid_aw_ods_data_update_confirm') 
                       AND n.target_user_id = ? 
                       AND n.target_user_id IS NOT NULL)
                      OR 
                      -- *** PUBLIC: เบี้ยยังชีพผู้สูงอายุ (elderly_aw_ods) ต้องมี target_user_id = current_user_id ***
                      (n.type IN ('elderly_aw_ods', 'elderly_aw_ods_update', 'elderly_aw_ods_data_update', 'elderly_aw_ods_note', 'elderly_aw_ods_deleted', 'elderly_aw_ods_critical_update', 'elderly_aw_ods_data_update_confirm') 
                       AND n.target_user_id = ? 
                       AND n.target_user_id IS NOT NULL)
                      OR 
                      -- *** PUBLIC: type อื่นๆ (เช่น qa, system) ใช้ logic เดิม ***
                      (n.type NOT IN (
                          'complain', 'queue', 'queue_cancelled', 'queue_status_update', 
                          'corruption_report_confirmation', 'new_corruption_report', 'corruption_status_update', 'corruption_assigned', 'corruption_response_added',
                          'esv_document', 'esv_document_update', 'esv_important_update', 'esv_document_created', 'esv_document_status_update', 'esv_document_note_added', 'esv_document_file_added', 'esv_document_completed', 'esv_document_rejected', 'esv_document_cancelled', 
                          'kid_aw_ods', 'kid_aw_ods_update', 'kid_aw_ods_data_update', 'kid_aw_ods_note', 'kid_aw_ods_deleted', 'kid_aw_ods_critical_update', 'kid_aw_ods_data_update_confirm', 
                          'elderly_aw_ods', 'elderly_aw_ods_update', 'elderly_aw_ods_data_update', 'elderly_aw_ods_note', 'elderly_aw_ods_deleted', 'elderly_aw_ods_critical_update', 'elderly_aw_ods_data_update_confirm'
                      ))
                  )
            ";
            
            $params = [
                $user_id, $db_user_type, $role,  // Base parameters
                $user_id,                         // complain/queue
                $user_id,                         // corruption
                $user_id,                         // esv
                $user_id,                         // kid_aw_ods
                $user_id                          // elderly_aw_ods
            ];
            
        } else {
            // *** FALLBACK: สำหรับ role อื่นๆ ***
            $sql = "
                SELECT n.notification_id
                FROM {$this->table} n
                LEFT JOIN {$this->reads_table} nr ON (
                    n.notification_id = nr.notification_id 
                    AND nr.user_id = ? 
                    AND nr.user_type = ?
                )
                WHERE n.target_role = ? 
                  AND n.is_archived = 0 
                  AND nr.id IS NULL
            ";
            
            $params = [
                $user_id, $db_user_type, $role
            ];
        }
        
        $query = $this->db->query($sql, $params);
        $unread_notifications = $query->result();
        
        if (empty($unread_notifications)) {
            log_message('debug', "No unread notifications for user {$user_id} ({$db_user_type}) in role {$role}");
            return true;
        }
        
        // เตรียมข้อมูลสำหรับ batch insert
        $batch_data = [];
        foreach ($unread_notifications as $notification) {
            $batch_data[] = [
                'notification_id' => $notification->notification_id,
                'user_id' => $user_id,
                'user_type' => $db_user_type,
                'read_at' => date('Y-m-d H:i:s')
            ];
        }
        
        $result = $this->db->insert_batch($this->reads_table, $batch_data);
        
        if ($result) {
            $count = count($batch_data);
            log_message('info', "Marked {$count} notifications as read for user {$user_id} ({$db_user_type}) with Staff Corruption filter");
        }
        
        return $result;
        
    } catch (Exception $e) {
        log_message('error', 'Exception in mark_all_read_by_user: ' . $e->getMessage());
        log_message('error', 'SQL Error: ' . $this->db->error()['message']);
        return false;
    }
}

	public function validate_corruption_notification_access($notification_id, $user_id, $user_type)
{
    try {
        // ดึงข้อมูล notification
        $this->db->select('type, target_user_id, target_role, data');
        $this->db->from($this->table);
        $this->db->where('notification_id', $notification_id);
        $notification = $this->db->get()->row();
        
        if (!$notification) {
            log_message('warning', "Corruption notification not found: {$notification_id}");
            return false;
        }
        
        // ตรวจสอบ type ที่เกี่ยวข้องกับการทุจริต
        $corruption_types_public = [
            'corruption_report_confirmation',
            'new_corruption_report', 
            'corruption_status_update', 
            'corruption_assigned', 
            'corruption_response_added'
        ];
        
        // *** เพิ่ม: ประเภท corruption notifications สำหรับ Staff ***
        $corruption_types_staff = [
            'corruption_report_assigned',
            'new_corruption_report_for_staff',
            'corruption_status_update_staff',
            'corruption_investigation_assigned',
            'corruption_response_required'
        ];
        
        $all_corruption_types = array_merge($corruption_types_public, $corruption_types_staff);
        
        if (!in_array($notification->type, $all_corruption_types)) {
            // ไม่ใช่การแจ้งเตือนการทุจริต ใช้ logic เดิม
            return true;
        }
        
        // สำหรับการแจ้งเตือนการทุจริต ต้องเป็นของตัวเอง
        if ($user_type === 'public') {
            // Public user ต้องเป็น corruption types ของ public และ target_user_id ตรงกัน
            if (in_array($notification->type, $corruption_types_public)) {
                $access_granted = ($notification->target_user_id == $user_id);
                
                if (!$access_granted) {
                    log_message('warning', "Access denied for public user {$user_id} to corruption notification {$notification_id} (target: {$notification->target_user_id})");
                }
                
                return $access_granted;
            }
            
            // Public user ไม่สามารถเข้าถึง staff corruption types
            return false;
        }
        
        // *** แก้ไข: Staff สามารถเข้าถึงเฉพาะการแจ้งเตือนที่มอบหมายให้ตัวเองเท่านั้น ***
        if ($user_type === 'staff') {
            // Staff ต้องเป็น corruption types ของ staff และ target_user_id ตรงกัน
            if (in_array($notification->type, $corruption_types_staff)) {
                $access_granted = ($notification->target_user_id == $user_id);
                
                if (!$access_granted) {
                    log_message('warning', "Access denied for staff user {$user_id} to corruption notification {$notification_id} (target: {$notification->target_user_id})");
                }
                
                return $access_granted;
            }
            
            // Staff ไม่สามารถเข้าถึง public corruption types
            return false;
        }
        
        log_message('warning', "Unknown user type for corruption notification access: {$user_type}");
        return false;
        
    } catch (Exception $e) {
        log_message('error', 'Error in validate_corruption_notification_access: ' . $e->getMessage());
        return false;
    }
}

/**
 * *** เพิ่ม: แปลง user_type ให้ตรงกับ ENUM ใน database ***
 */
private function map_user_type_to_db($user_type)
{
    switch ($user_type) {
        case 'public':
            return 'public';
        case 'staff':
        case 'admin':
        case 'system_admin':
        case 'super_admin':
        case 'user_admin':
            return 'staff'; // แมปทุก admin types เป็น 'staff'
        default:
            log_message('warning', "Unknown user_type: {$user_type}, defaulting to 'public'");
            return 'public';
    }
}

	public function validate_elderly_notification_access($notification_id, $user_id, $user_type)
{
    try {
        // ดึงข้อมูล notification
        $this->db->select('type, target_user_id, target_role, data');
        $this->db->from($this->table);
        $this->db->where('notification_id', $notification_id);
        $notification = $this->db->get()->row();
        
        if (!$notification) {
            log_message('warning', "Notification not found: {$notification_id}");
            return false;
        }
        
        // ตรวจสอบ type ที่เกี่ยวข้องกับเบี้ยยังชีพ
        $elderly_types = [
            'elderly_aw_ods', 
            'elderly_aw_ods_update', 
            'elderly_aw_ods_data_update', 
            'elderly_aw_ods_note', 
            'elderly_aw_ods_deleted', 
            'elderly_aw_ods_critical_update', 
            'elderly_aw_ods_data_update_confirm'
        ];
        
        if (!in_array($notification->type, $elderly_types)) {
            // ไม่ใช่การแจ้งเตือนเบี้ยยังชีพ ใช้ logic เดิม
            return true;
        }
        
        // สำหรับการแจ้งเตือนเบี้ยยังชีพ ต้องเป็นของตัวเอง
        if ($user_type === 'public') {
            $access_granted = ($notification->target_user_id == $user_id);
            
            if (!$access_granted) {
                log_message('warning', "Access denied for public user {$user_id} to elderly notification {$notification_id} (target: {$notification->target_user_id})");
            }
            
            return $access_granted;
        }
        
        // Staff สามารถเข้าถึงได้ทุกการแจ้งเตือน
        if ($user_type === 'staff') {
            return true;
        }
        
        log_message('warning', "Unknown user type for notification access: {$user_type}");
        return false;
        
    } catch (Exception $e) {
        log_message('error', 'Error in validate_elderly_notification_access: ' . $e->getMessage());
        return false;
    }
}

   /**
    * *** LEGACY: ดึงการแจ้งเตือนตาม role (เพื่อ backward compatibility) ***
    */
  public function get_notifications_by_role($target_role, $limit = 10, $offset = 0)
{
    try {
        $this->db->select('*');
        $this->db->from($this->table);
        $this->db->where('target_role', $target_role);
        $this->db->where('is_archived', 0);
        $this->db->order_by('created_at', 'DESC');
        $this->db->limit($limit, $offset);
        
        $query = $this->db->get();
        
        if ($query->num_rows() > 0) {
            $results = $query->result();
            
            foreach ($results as $notification) {
                if ($notification->data && is_string($notification->data)) {
                    $notification->data = json_decode($notification->data);
                }
            }
            
            return $results;
        }
        
        return [];
        
    } catch (Exception $e) {
        log_message('error', 'Exception in get_notifications_by_role: ' . $e->getMessage());
        return [];
    }
}

   /**
    * *** LEGACY: นับการแจ้งเตือนที่ยังไม่อ่าน ตาม role (เพื่อ backward compatibility) ***
    */
   public function count_unread_by_role($target_role)
   {
       try {
           $this->db->where('target_role', $target_role);
           $this->db->where('is_read', 0);
           $this->db->where('is_archived', 0);
           $count = $this->db->count_all_results($this->table);
           
           log_message('debug', "count_unread_by_role query: " . $this->db->last_query());
           log_message('debug', "Unread count for {$target_role}: {$count}");
           
           return $count;
           
       } catch (Exception $e) {
           log_message('error', 'Exception in count_unread_by_role: ' . $e->getMessage());
           return 0;
       }
   }

   /**
    * *** LEGACY: นับการแจ้งเตือนทั้งหมด ตาม role ***
    */
   public function count_notifications_by_role($target_role)
   {
       try {
           $this->db->where('target_role', $target_role);
           $this->db->where('is_archived', 0);
           $count = $this->db->count_all_results($this->table);
           
           log_message('debug', "count_notifications_by_role for {$target_role}: {$count}");
           
           return $count;
           
       } catch (Exception $e) {
           log_message('error', 'Exception in count_notifications_by_role: ' . $e->getMessage());
           return 0;
       }
   }

   /**
    * *** LEGACY: ทำเครื่องหมายทั้งหมดว่าอ่านแล้ว ตาม role (อันตราย - แนะนำไม่ให้ใช้) ***
    */
   public function mark_all_as_read_by_role($target_role, $user_id = null)
   {
       try {
           $update_data = [
               'is_read' => 1,
               'read_at' => date('Y-m-d H:i:s')
           ];
           
           if ($user_id) {
               $update_data['read_by'] = $user_id;
           }
           
           $this->db->where('target_role', $target_role);
           $this->db->where('is_read', 0);
           $this->db->where('is_archived', 0);
           $result = $this->db->update($this->table, $update_data);
           
           $affected_rows = $this->db->affected_rows();
           log_message('warning', "LEGACY: Marked {$affected_rows} notifications as read for ALL users in role: {$target_role}");
           
           return $result;
           
       } catch (Exception $e) {
           log_message('error', 'Exception in mark_all_as_read_by_role: ' . $e->getMessage());
           return false;
       }
   }

   /**
    * *** LEGACY: ทำเครื่องหมายว่าอ่านแล้ว (อันตราย - แนะนำไม่ให้ใช้) ***
    */
   public function mark_as_read($notification_id, $user_id = null)
   {
       try {
           $update_data = [
               'is_read' => 1,
               'read_at' => date('Y-m-d H:i:s')
           ];
           
           if ($user_id) {
               $update_data['read_by'] = $user_id;
           }
           
           $this->db->where('notification_id', $notification_id);
           $this->db->where('is_archived', 0);
           $result = $this->db->update($this->table, $update_data);
           
           log_message('warning', "LEGACY: mark_as_read - Notification: {$notification_id} marked as read for ALL users");
           
           return $result;
           
       } catch (Exception $e) {
           log_message('error', 'Exception in mark_as_read: ' . $e->getMessage());
           return false;
       }
   }

   /**
    * Archive การแจ้งเตือน
    */
   public function archive_notification($notification_id, $user_id = null)
   {
       try {
           $update_data = [
               'is_archived' => 1,
               'archived_at' => date('Y-m-d H:i:s')
           ];
           
           if ($user_id) {
               $update_data['archived_by'] = $user_id;
           }
           
           $this->db->where('notification_id', $notification_id);
           $result = $this->db->update($this->table, $update_data);
           
           log_message('info', "Archived notification ID: {$notification_id}");
           
           return $result;
           
       } catch (Exception $e) {
           log_message('error', 'Exception in archive_notification: ' . $e->getMessage());
           return false;
       }
   }

   /**
    * *** แก้ไข: ดึงการแจ้งเตือนตามเงื่อนไข (รองรับ user-specific read status) ***
    */
   public function get_notifications($user_id = null, $limit = 20, $offset = 0, $filters = [])
   {
       // ดึงข้อมูล user ปัจจุบัน
       $current_user = $this->get_current_user_info();
       
       if ($current_user['user_id'] && $current_user['user_type']) {
           // ใช้ method ใหม่ที่รองรับ individual read status
           return $this->get_by_role_for_user(
               $current_user['role'], 
               $current_user['user_id'], 
               $current_user['user_type'], 
               $limit, 
               $offset
           );
       }
       
       // Fallback สำหรับ guest หรือ legacy code
       $this->db->select('n.*, m.m_fname, m.m_lname');
       $this->db->from($this->table . ' n');
       $this->db->join('tbl_member m', 'n.created_by = m.m_id', 'left');
       
       if (!isset($filters['include_archived']) || !$filters['include_archived']) {
           $this->db->where('n.is_archived', 0);
       }
       
       if ($user_id) {
           $user_role = $this->get_user_role($user_id);
           $this->db->group_start();
           $this->db->where('n.target_user_id', $user_id);
           $this->db->or_where('n.target_role', $user_role);
           $this->db->or_where('n.target_user_id IS NULL AND n.target_role IS NULL');
           $this->db->group_end();
       }

       if (!empty($filters['type'])) {
           $this->db->where('n.type', $filters['type']);
       }
       if (!empty($filters['priority'])) {
           $this->db->where('n.priority', $filters['priority']);
       }
       if (isset($filters['is_read'])) {
           $this->db->where('n.is_read', $filters['is_read']);
       }

       $this->db->order_by('n.created_at', 'DESC');
       $this->db->limit($limit, $offset);
       
       $query = $this->db->get();
       $notifications = $query->result();

       foreach ($notifications as $notification) {
           if ($notification->data && is_string($notification->data)) {
               $notification->data = json_decode($notification->data, true);
           }
       }

       return $notifications;
   }

   /**
    * *** แก้ไข: นับจำนวนการแจ้งเตือนที่ยังไม่อ่าน (รองรับ user-specific) ***
    */
   public function count_unread($user_id = null)
   {
       $current_user = $this->get_current_user_info();
       
       if ($current_user['user_id'] && $current_user['user_type']) {
           return $this->count_unread_for_user(
               $current_user['role'], 
               $current_user['user_id'], 
               $current_user['user_type']
           );
       }
       
       // Fallback สำหรับ legacy code
       if (!$user_id) return 0;
       
       $user_role = $this->get_user_role($user_id);
       
       $this->db->from($this->table);
       $this->db->where('is_read', 0);
       $this->db->where('is_archived', 0);
       
       $this->db->group_start();
       $this->db->where('target_user_id', $user_id);
       $this->db->or_where('target_role', $user_role);
       $this->db->or_where('target_user_id IS NULL AND target_role IS NULL');
       $this->db->group_end();
       
       return $this->db->count_all_results();
   }

   /**
    * *** แก้ไข: ทำเครื่องหมายทั้งหมดว่าอ่านแล้ว (รองรับ user-specific) ***
    */
   public function mark_all_as_read($user_id = null)
   {
       $current_user = $this->get_current_user_info();
       
       if ($current_user['user_id'] && $current_user['user_type']) {
           return $this->mark_all_read_by_user(
               $current_user['role'], 
               $current_user['user_id'], 
               $current_user['user_type']
           );
       }
       
       // Fallback สำหรับ legacy code (อันตราย)
       if (!$user_id) return false;
       
       $user_role = $this->get_user_role($user_id);
       
       $this->db->where('is_read', 0);
       $this->db->where('is_archived', 0);
       $this->db->group_start();
       $this->db->where('target_user_id', $user_id);
       $this->db->or_where('target_role', $user_role);
       $this->db->or_where('target_user_id IS NULL AND target_role IS NULL');
       $this->db->group_end();
       
       return $this->db->update($this->table, [
           'is_read' => 1,
           'read_at' => date('Y-m-d H:i:s'),
           'read_by' => $user_id
       ]);
   }

   /**
    * *** แก้ไข: ดึง role ของ user (รองรับทั้งระบบ admin และ public + แก้ไข user_id overflow) ***
    */
   private function get_user_role($user_id)
   {
       $mp_id = $this->session->userdata('mp_id');
       $m_id = $this->session->userdata('m_id');
       
       if ($mp_id) {
           return 'public';
       } elseif ($m_id) {
           $this->db->select('m_system, ref_pid');
           $this->db->from('tbl_member');
           $this->db->where('m_id', $user_id);
           $user = $this->db->get()->row();
           
           if ($user) {
               return 'staff'; // *** แก้ไข: return 'staff' แทน 'admin' ***
           }
           return 'staff'; // *** แก้ไข: return 'staff' แทน 'admin' ***
       }
       
       return 'guest';
   }

   /**
    * *** แก้ไข: ดึงข้อมูล user ปัจจุบัน (แก้ไข user_id overflow) ***
    */
   private function get_current_user_info()
{
    $user_info = [
        'user_id' => null,
        'user_type' => 'guest',
        'role' => 'guest'
    ];
    
    $mp_id = $this->session->userdata('mp_id');
    $m_id = $this->session->userdata('m_id');
    $mp_email = $this->session->userdata('mp_email');
    $m_email = $this->session->userdata('m_email');
    
    if ($mp_id && $mp_email) {
        // *** แก้ไข: ดึง id จาก tbl_member_public แทน mp_id ***
        $public_user = $this->db->select('id')
                               ->where('mp_email', $mp_email)
                               ->get('tbl_member_public')
                               ->row();
        
        if ($public_user) {
            $user_info = [
                'user_id' => $public_user->id, // *** ใช้ id (เช่น 36) แทน mp_id ( เช่น 251749284089260) ***
                'user_type' => 'public',
                'role' => 'public'
            ];
            
            log_message('info', "Public user info: mp_id={$mp_id}, email={$mp_email}, database_id={$public_user->id}");
        }
    } elseif ($m_id && $m_email) {
        // *** แก้ไข: สำหรับ Staff ใช้ m_id โดยตรง ***
        $user_info = [
            'user_id' => $m_id,
            'user_type' => 'staff', 
            'role' => 'staff'  // *** แก้ไข: เปลี่ยนจาก 'admin' เป็น 'staff' ***
        ];
        
        log_message('info', "Staff user info: m_id={$m_id}, email={$m_email}");
    }
    
    return $user_info;
}

   /**
    * *** แก้ไข: ดึง ID ของ user ปัจจุบัน (แก้ไข user_id overflow) ***
    */
   private function get_current_user_id()
   {
       $user_info = $this->get_current_user_info();
       return $user_info['user_id'] ?: 0;
   }

   /**
    * *** เพิ่ม: ฟังก์ชันแก้ไข user_id overflow ***
    */
   private function fix_user_id_overflow($session_id, $email, $user_type = 'public')
   {
       // ตรวจสอบว่าเป็น INT overflow หรือไม่
       if ($session_id == 2147483647 || $session_id == '2147483647' || empty($session_id)) {
           log_message('info', "Notification Model detected user_id overflow: {$session_id} for email: {$email} (type: {$user_type})");
           
           if ($user_type === 'public') {
               // ใช้ auto increment id จาก tbl_member_public
               $public_user = $this->db->select('id, mp_id')
                                      ->where('mp_email', $email)
                                      ->get('tbl_member_public')
                                      ->row();
               
               if ($public_user) {
                   log_message('info', "Notification Model fixed public user_id: {$session_id} -> {$public_user->id} for {$email}");
                   return $public_user->id;
               }
           } else {
               // ดึง ID จาก tbl_member (staff)
               $staff_user = $this->db->select('m_id')
                                     ->where('m_email', $email)
                                     ->get('tbl_member')
                                     ->row();
               
               if ($staff_user) {
                   log_message('info', "Notification Model fixed staff user_id: {$session_id} -> {$staff_user->m_id} for {$email}");
                   return $staff_user->m_id;
               }
           }
           
           log_message('error', "Notification Model could not fix user_id for email: {$email}");
           return null;
       }
       
       return $session_id; // ถ้าไม่มีปัญหาให้ return ค่าเดิม
   }

   /**
    * ดึงสถิติการแจ้งเตือน
    */
   public function get_notification_statistics($user_id = null, $days = 30)
   {
       $stats = [];
       $date_from = date('Y-m-d', strtotime("-{$days} days"));
       
       $this->db->select('
           COUNT(*) as total,
           SUM(CASE WHEN is_read = 0 THEN 1 ELSE 0 END) as unread,
           SUM(CASE WHEN is_archived = 1 THEN 1 ELSE 0 END) as archived,
           SUM(CASE WHEN priority = "critical" THEN 1 ELSE 0 END) as critical,
           SUM(CASE WHEN priority = "high" THEN 1 ELSE 0 END) as high
       ');
       $this->db->from($this->table);
       $this->db->where('created_at >=', $date_from);
       
       if ($user_id) {
           $user_role = $this->get_user_role($user_id);
           $this->db->group_start();
           $this->db->where('target_user_id', $user_id);
           $this->db->or_where('target_role', $user_role);
           $this->db->group_end();
       }
       
       $stats['overview'] = $this->db->get()->row_array();
       
       return $stats;
   }

   /**
    * Archive การแจ้งเตือนเก่า
    */
   public function archive_old_read_notifications($days = 90)
   {
       $cutoff_date = date('Y-m-d H:i:s', strtotime("-{$days} days"));
       
       $this->db->where('is_read', 1);
       $this->db->where('is_archived', 0);
       $this->db->where('read_at <', $cutoff_date);
       
       return $this->db->update($this->table, [
           'is_archived' => 1,
           'archived_at' => date('Y-m-d H:i:s'),
           'archived_by' => 0 // system auto-archive
       ]);
   }

   /**
    * *** เพิ่ม: ทำความสะอาดข้อมูลการอ่านเก่า ***
    */
   public function cleanup_old_read_records($days = 365)
   {
       try {
           $cutoff_date = date('Y-m-d H:i:s', strtotime("-{$days} days"));
           
           // ลบ read records ที่เก่ามากๆ
           $this->db->where('read_at <', $cutoff_date);
           $result = $this->db->delete($this->reads_table);
           
           $affected_rows = $this->db->affected_rows();
           log_message('info', "Cleaned up {$affected_rows} old read records");
           
           return $result;
           
       } catch (Exception $e) {
           log_message('error', 'Exception in cleanup_old_read_records: ' . $e->getMessage());
           return false;
       }
   }

   /**
    * ทดสอบการเชื่อมต่อ
    */
   public function test_connection()
   {
       try {
           $query = $this->db->query("SELECT COUNT(*) as total FROM {$this->table}");
           $result = $query->row();
           
           $reads_exist = $this->db->table_exists($this->reads_table);
           $reads_count = 0;
           
           if ($reads_exist) {
               $reads_query = $this->db->query("SELECT COUNT(*) as total FROM {$this->reads_table}");
               $reads_result = $reads_query->row();
               $reads_count = $reads_result->total;
           }
           
           $current_user = $this->get_current_user_info();
           
           return [
               'status' => 'success',
               'message' => 'Database connection working',
               'total_notifications' => $result->total,
               'total_read_records' => $reads_count,
               'reads_table_exists' => $reads_exist,
               'database' => $this->db->database,
               'current_user' => $current_user,
               'session_mp_id' => $this->session->userdata('mp_id'),
               'session_m_id' => $this->session->userdata('m_id')
           ];
           
       } catch (Exception $e) {
           return [
               'status' => 'error',
               'message' => $e->getMessage(),
               'database' => $this->db->database ?? 'Unknown'
           ];
       }
   }

   /**
    * *** เพิ่ม: Method สำหรับ debugging staff notifications + reference_table ***
    */
   public function debug_staff_notifications($staff_id)
   {
       try {
           log_message('info', "=== DEBUG STAFF NOTIFICATIONS FOR USER: {$staff_id} (WITH REFERENCE_TABLE) ===");
           
           // ตรวจสอบ notifications ทั้งหมดของ staff
           $all_sql = "
               SELECT n.notification_id, n.type, n.title, n.target_user_id, n.target_role, n.reference_table, n.created_at
               FROM {$this->table} n
               WHERE n.target_role = 'staff' 
                 AND n.is_archived = 0
               ORDER BY n.created_at DESC
               LIMIT 20
           ";
           
           $all_query = $this->db->query($all_sql);
           $all_notifications = $all_query->result();
           
           log_message('info', "Total staff notifications: " . count($all_notifications));
           foreach ($all_notifications as $notif) {
               log_message('info', "ID: {$notif->notification_id}, Type: {$notif->type}, Target: {$notif->target_user_id}, Ref_Table: {$notif->reference_table}, Title: {$notif->title}");
           }
           
           // ตรวจสอบ corruption notifications ที่มี reference_table
           $corruption_sql = "
               SELECT n.notification_id, n.type, n.title, n.target_user_id, n.reference_table, n.created_at
               FROM {$this->table} n
               WHERE n.target_role = 'staff' 
                 AND n.is_archived = 0
                 AND n.reference_table = 'tbl_corruption_reports'
               ORDER BY n.created_at DESC
           ";
           
           $corruption_query = $this->db->query($corruption_sql);
           $corruption_notifications = $corruption_query->result();
           
           log_message('info', "Corruption notifications (reference_table): " . count($corruption_notifications));
           foreach ($corruption_notifications as $notif) {
               $assigned_to_staff = ($notif->target_user_id == $staff_id) ? 'YES' : 'NO';
               log_message('info', "Corruption ID: {$notif->notification_id}, Target: {$notif->target_user_id}, Assigned to this staff: {$assigned_to_staff}, Title: {$notif->title}");
           }
           
           // ตรวจสอบ notifications ที่ผ่าน filter
           $filtered_notifications = $this->get_by_role_for_user('staff', $staff_id, 'staff', 20);
           log_message('info', "Filtered notifications for staff {$staff_id}: " . count($filtered_notifications));
           
           foreach ($filtered_notifications as $notif) {
               log_message('info', "Filtered ID: {$notif->notification_id}, Type: {$notif->type}, Ref_Table: {$notif->reference_table}, Title: {$notif->title}");
           }
           
           // ตรวจสอบ unread count
           $unread_count = $this->count_unread_for_user('staff', $staff_id, 'staff');
           log_message('info', "Unread count for staff {$staff_id}: {$unread_count}");
           
           return [
               'total_staff_notifications' => count($all_notifications),
               'corruption_notifications' => count($corruption_notifications),
               'filtered_notifications' => count($filtered_notifications),
               'unread_count' => $unread_count,
               'all_notifications' => $all_notifications,
               'corruption_notifications' => $corruption_notifications,
               'filtered_notifications' => $filtered_notifications
           ];
           
       } catch (Exception $e) {
           log_message('error', 'Exception in debug_staff_notifications: ' . $e->getMessage());
           return [
               'error' => $e->getMessage()
           ];
       }
   }
}