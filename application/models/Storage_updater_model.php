<?php
defined('BASEPATH') or exit('No direct script access allowed');

/**
 * ระบบอัปเดตข้อมูลพื้นที่จัดเก็บอัตโนมัติ (แก้ไขให้เข้ากับโครงสร้างฐานข้อมูลที่มีอยู่)
 * สร้างไฟล์นี้ที่ models/Storage_updater_model.php
 */
class Storage_updater_model extends CI_Model
{
    public function __construct()
    {
        parent::__construct();
        
        // ตรวจสอบและปรับปรุงโครงสร้างตารางถ้าจำเป็น
        $this->ensure_table_structure();
    }

    /**
     * 🔧 ตรวจสอบและปรับปรุงโครงสร้างตาราง
     */
    private function ensure_table_structure()
    {
        try {
            // ตรวจสอบว่าคอลัมน์ server_updated มีอยู่หรือไม่
            $columns = $this->db->list_fields('tbl_server');
            
            if (!in_array('server_updated', $columns)) {
                // เพิ่มคอลัมน์ server_updated
                $sql = "ALTER TABLE `tbl_server` ADD COLUMN `server_updated` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()";
                $this->db->query($sql);
                
                // อัปเดตข้อมูลที่มีอยู่
                $this->db->query("UPDATE `tbl_server` SET `server_updated` = NOW()");
                
                error_log('Added server_updated column to tbl_server');
            }
            
            // สร้างตาราง settings history ถ้าไม่มี
            $this->create_storage_settings_history_table();
            
        } catch (Exception $e) {
            error_log('Table structure check error: ' . $e->getMessage());
        }
    }

    /**
     * 🤖 อัปเดตข้อมูลพื้นที่จัดเก็บอัตโนมัติ
     */
    public function update_storage_usage()
    {
        try {
            // 1. คำนวณพื้นที่ใช้งานจริง
            $used_space = $this->calculate_actual_usage();
            
            // 2. ดึงข้อมูลพื้นที่ทั้งหมดจากแอดมิน
            $total_space = $this->get_admin_defined_total_space();
            
            // 3. อัปเดตข้อมูลในฐานข้อมูล
            $this->update_server_data($total_space, $used_space);
            
            // 4. บันทึก log
            $this->log_storage_update($total_space, $used_space);
            
            return [
                'success' => true,
                'total_space' => $total_space,
                'used_space' => $used_space,
                'updated_at' => date('Y-m-d H:i:s')
            ];
            
        } catch (Exception $e) {
            error_log('Storage Update Error: ' . $e->getMessage());
            return [
                'success' => false,
                'error' => $e->getMessage()
            ];
        }
    }

    /**
     * 📊 คำนวณพื้นที่ใช้งานจริงจากไฟล์ต่างๆ (ทั้ง httpdocs)
     */
    private function calculate_actual_usage()
    {
        $total_size = 0;
        
        try {
            // คำนวณขนาดทั้ง httpdocs directory
            $httpdocs_path = FCPATH; // ได้ path ของ httpdocs
            
            if (is_dir($httpdocs_path)) {
                $total_size += $this->get_directory_size($httpdocs_path);
                
                // Log สำหรับ debug
                error_log('Calculating storage from: ' . $httpdocs_path);
            } else {
                error_log('httpdocs path not found: ' . $httpdocs_path);
            }
            
            // เพิ่มขนาดฐานข้อมูล
            $database_size = $this->get_database_size();
            $total_size += $database_size;
            
            // Log รายละเอียด
            $size_gb = round($total_size / (1024 * 1024 * 1024), 2);
            $db_size_gb = round($database_size / (1024 * 1024 * 1024), 2);
            
            error_log("Storage calculation: Files={$size_gb}GB (includes DB={$db_size_gb}GB), Total={$size_gb}GB");
            
        } catch (Exception $e) {
            error_log('Storage calculation error: ' . $e->getMessage());
            // ถ้าเกิดข้อผิดพลาด ให้ใช้ค่าเดิมที่มีในฐานข้อมูล
            $current = $this->get_current_server_data();
            return $current ? floatval($current->server_current) : 0;
        }
        
        // แปลงจาก bytes เป็น GB
        return round($total_size / (1024 * 1024 * 1024), 2);
    }

    /**
     * 📁 คำนวณขนาดโฟลเดอร์ (ปรับปรุงให้เร็วขึ้นและแสดง progress)
     */
    private function get_directory_size($directory)
    {
        $size = 0;
        $file_count = 0;
        
        try {
            if (!is_dir($directory)) {
                return 0;
            }
            
            // แสดง path ที่กำลังคำนวณ
            error_log("Calculating size for: {$directory}");
            
            // ใช้ RecursiveIterator สำหรับ performance ที่ดี
            $iterator = new RecursiveIteratorIterator(
                new RecursiveDirectoryIterator($directory, RecursiveDirectoryIterator::SKIP_DOTS),
                RecursiveIteratorIterator::LEAVES_ONLY
            );
            
            foreach ($iterator as $file) {
                if ($file->isFile() && $file->isReadable()) {
                    $file_size = $file->getSize();
                    if ($file_size !== false) {
                        $size += $file_size;
                        $file_count++;
                        
                        // แสดง progress ทุก 1000 ไฟล์
                        if ($file_count % 1000 == 0) {
                            $current_gb = round($size / (1024 * 1024 * 1024), 2);
                            error_log("Progress: {$file_count} files, {$current_gb} GB");
                        }
                    }
                }
            }
            
            $size_gb = round($size / (1024 * 1024 * 1024), 2);
            error_log("Directory '{$directory}': {$file_count} files, {$size_gb} GB");
            
        } catch (Exception $e) {
            error_log("Directory size calculation error for '{$directory}': " . $e->getMessage());
        }
        
        return $size;
    }

    /**
     * 🗄️ คำนวณขนาดฐานข้อมูล
     */
    private function get_database_size()
    {
        try {
            $db_name = $this->db->database;
            
            $query = $this->db->query("
                SELECT 
                    SUM(data_length + index_length) as size 
                FROM information_schema.TABLES 
                WHERE table_schema = ?
            ", [$db_name]);
            
            $result = $query->row();
            return $result ? $result->size : 0;
            
        } catch (Exception $e) {
            error_log('Database size calculation error: ' . $e->getMessage());
            return 0;
        }
    }

    /**
     * 💾 ดึงพื้นที่ทั้งหมดจากค่าที่แอดมินกำหนดเท่านั้น
     */
    private function get_admin_defined_total_space()
    {
        try {
            $current = $this->get_current_server_data();
            
            if ($current && $current->server_storage > 0) {
                return floatval($current->server_storage);
            }
            
            // ถ้าไม่มีข้อมูล ให้ใช้ค่า default
            $default_size = 100;
            $this->initialize_server_storage($default_size);
            
            return $default_size;
            
        } catch (Exception $e) {
            error_log('Get admin defined storage error: ' . $e->getMessage());
            return 100;
        }
    }

    /**
     * 🆕 สร้างข้อมูลเริ่มต้น
     */
    private function initialize_server_storage($default_size)
    {
        $data = [
            'server_storage' => $default_size,
            'server_current' => 0
        ];
        
        // ตรวจสอบว่าคอลัมน์ server_updated มีหรือไม่
        $columns = $this->db->list_fields('tbl_server');
        if (in_array('server_updated', $columns)) {
            $data['server_updated'] = date('Y-m-d H:i:s');
        }
        
        $this->db->insert('tbl_server', $data);
    }

    /**
     * 🔄 อัปเดตข้อมูลในตาราง tbl_server
     */
    private function update_server_data($total_space, $used_space)
    {
        $existing = $this->get_current_server_data();
        
        $data = [
            'server_current' => $used_space
        ];
        
        // เพิ่ม server_updated ถ้ามีคอลัมน์นี้
        $columns = $this->db->list_fields('tbl_server');
        if (in_array('server_updated', $columns)) {
            $data['server_updated'] = date('Y-m-d H:i:s');
        }
        
        if ($existing) {
            $this->db->where('server_id', $existing->server_id)
                     ->update('tbl_server', $data);
        } else {
            $data['server_storage'] = $total_space;
            $this->db->insert('tbl_server', $data);
        }
    }

    /**
     * 📋 ดึงข้อมูลเซิร์ฟเวอร์ปัจจุบัน
     */
    private function get_current_server_data()
    {
        return $this->db->get('tbl_server')->row();
    }

    /**
     * 🔧 อัปเดตขนาดพื้นที่ทั้งหมด (สำหรับ System Admin เท่านั้น)
     */
    public function update_total_storage_size($new_size, $updated_by)
    {
        try {
            $current = $this->get_current_server_data();
            $old_size = $current ? floatval($current->server_storage) : 0;
            
            if (!$current) {
                // สร้างข้อมูลใหม่
                $data = [
                    'server_storage' => $new_size,
                    'server_current' => 0
                ];
                
                $columns = $this->db->list_fields('tbl_server');
                if (in_array('server_updated', $columns)) {
                    $data['server_updated'] = date('Y-m-d H:i:s');
                }
                
                $this->db->insert('tbl_server', $data);
            } else {
                // อัปเดตข้อมูลเดิม
                $update_data = [
                    'server_storage' => $new_size
                ];
                
                $columns = $this->db->list_fields('tbl_server');
                if (in_array('server_updated', $columns)) {
                    $update_data['server_updated'] = date('Y-m-d H:i:s');
                }
                
                $this->db->where('server_id', $current->server_id)
                         ->update('tbl_server', $update_data);
            }
            
            // บันทึกประวัติการเปลี่ยนแปลง
            $this->log_storage_size_change($old_size, $new_size, $updated_by);
            
            return [
                'success' => true,
                'old_size' => $old_size,
                'new_size' => $new_size,
                'message' => 'อัปเดตขนาดพื้นที่จัดเก็บสำเร็จ'
            ];
            
        } catch (Exception $e) {
            error_log('Update total storage size error: ' . $e->getMessage());
            return [
                'success' => false,
                'error' => $e->getMessage()
            ];
        }
    }

    /**
     * 📝 บันทึกประวัติการเปลี่ยนแปลงขนาดพื้นที่
     */
    private function log_storage_size_change($old_size, $new_size, $updated_by)
    {
        try {
            $this->create_storage_settings_history_table();
            
            $data = [
                'old_size' => $old_size,
                'new_size' => $new_size,
                'updated_by' => $updated_by,
                'updated_at' => date('Y-m-d H:i:s')
            ];
            
            $this->db->insert('tbl_storage_settings_history', $data);
        } catch (Exception $e) {
            error_log('Log storage size change error: ' . $e->getMessage());
        }
    }

    /**
     * 🗂️ สร้างตารางประวัติการตั้งค่า
     */
    private function create_storage_settings_history_table()
    {
        if (!$this->db->table_exists('tbl_storage_settings_history')) {
            $sql = "CREATE TABLE `tbl_storage_settings_history` (
                `history_id` int(11) NOT NULL AUTO_INCREMENT,
                `old_size` decimal(10,2) NOT NULL COMMENT 'ขนาดเดิม (GB)',
                `new_size` decimal(10,2) NOT NULL COMMENT 'ขนาดใหม่ (GB)',
                `updated_by` varchar(255) NOT NULL COMMENT 'ผู้แก้ไข',
                `updated_at` timestamp NOT NULL DEFAULT current_timestamp() COMMENT 'วันที่แก้ไข',
                PRIMARY KEY (`history_id`),
                KEY `updated_at` (`updated_at`)
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci COMMENT='ประวัติการเปลี่ยนแปลงขนาดพื้นที่จัดเก็บ'";
            
            $this->db->query($sql);
        }
    }

    /**
     * 📈 ดึงประวัติการตั้งค่า
     */
    public function get_storage_settings_history($limit = 10)
    {
        try {
            if ($this->db->table_exists('tbl_storage_settings_history')) {
                return $this->db->select('*')
                               ->from('tbl_storage_settings_history')
                               ->order_by('updated_at', 'DESC')
                               ->limit($limit)
                               ->get()
                               ->result();
            }
        } catch (Exception $e) {
            error_log('Get storage settings history error: ' . $e->getMessage());
        }
        
        return [];
    }

    /**
     * ✅ ดึงข้อมูลการตั้งค่าปัจจุบัน
     */
    public function get_current_storage_settings()
    {
        try {
            $current = $this->get_current_server_data();
            
            $total_space = $current ? floatval($current->server_storage) : 100;
            $current_usage = $current ? floatval($current->server_current) : 0;
            $last_updated = null;
            
            // ตรวจสอบว่ามีคอลัมน์ server_updated หรือไม่
            $columns = $this->db->list_fields('tbl_server');
            if (in_array('server_updated', $columns) && $current && isset($current->server_updated)) {
                $last_updated = $current->server_updated;
            }
            
            return [
                'total_space' => $total_space,
                'current_usage' => $current_usage,
                'last_updated' => $last_updated
            ];
            
        } catch (Exception $e) {
            error_log('Get current storage settings error: ' . $e->getMessage());
            return [
                'total_space' => 100,
                'current_usage' => 0,
                'last_updated' => null
            ];
        }
    }

    /**
     * 📝 บันทึก log การอัปเดต
     */
    private function log_storage_update($total_space, $used_space)
    {
        // บันทึกลง file log
        $log_message = sprintf(
            "[%s] Storage Updated - Total: %.3f GB, Used: %.3f GB (%.2f%%)\n",
            date('Y-m-d H:i:s'),
            $total_space,
            $used_space,
            ($used_space / $total_space) * 100
        );
        
        $log_file = APPPATH . 'logs/storage_updates.log';
        file_put_contents($log_file, $log_message, FILE_APPEND | LOCK_EX);
        
        // บันทึกลงฐานข้อมูล
        $this->save_storage_history($total_space, $used_space);
    }

    /**
     * 📊 บันทึกประวัติการใช้งาน
     */
    private function save_storage_history($total_space, $used_space)
    {
        try {
            $this->create_storage_history_table();
            
            $percentage = ($used_space / $total_space) * 100;
            
            $data = [
                'used_space' => $used_space,
                'total_space' => $total_space,
                'percentage_used' => round($percentage, 2),
                'created_at' => date('Y-m-d H:i:s')
            ];
            
            $this->db->insert('tbl_storage_history', $data);
            
            // ลบข้อมูลเก่าที่เกิน 90 วัน
            $this->cleanup_old_history();
            
        } catch (Exception $e) {
            error_log('Save storage history error: ' . $e->getMessage());
        }
    }

    /**
     * 🗂️ สร้างตาราง storage history (ถ้าไม่มี)
     */
    private function create_storage_history_table()
    {
        if (!$this->db->table_exists('tbl_storage_history')) {
            $sql = "CREATE TABLE `tbl_storage_history` (
                `history_id` int(11) NOT NULL AUTO_INCREMENT,
                `used_space` decimal(10,2) NOT NULL COMMENT 'พื้นที่ที่ใช้งาน (GB)',
                `total_space` decimal(10,2) NOT NULL COMMENT 'พื้นที่ทั้งหมด (GB)',
                `percentage_used` decimal(5,2) NOT NULL COMMENT 'เปอร์เซ็นต์การใช้งาน',
                `created_at` timestamp NOT NULL DEFAULT current_timestamp() COMMENT 'วันที่บันทึก',
                PRIMARY KEY (`history_id`),
                KEY `created_at` (`created_at`)
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci";
            
            $this->db->query($sql);
        }
    }

    /**
     * 🧹 ลบข้อมูลประวัติเก่า
     */
    private function cleanup_old_history()
    {
        try {
            $this->db->where('created_at <', date('Y-m-d H:i:s', strtotime('-90 days')))
                     ->delete('tbl_storage_history');
        } catch (Exception $e) {
            error_log('Cleanup old history error: ' . $e->getMessage());
        }
    }

    /**
     * ⚡ อัปเดตแบบ manual
     */
    public function manual_update()
    {
        return $this->update_storage_usage();
    }

    /**
     * 📈 ดึงสถิติการใช้งานล่าสุด
     */
    public function get_usage_statistics()
    {
        try {
            $current = $this->get_current_server_data();
            
            if (!$current) {
                return null;
            }
            
            $total_gb = floatval($current->server_storage);
            $used_gb = floatval($current->server_current);
            $percentage = ($used_gb / $total_gb) * 100;
            
            // ตรวจสอบคอลัมน์ server_updated
            $last_updated = date('Y-m-d H:i:s');
            $columns = $this->db->list_fields('tbl_server');
            if (in_array('server_updated', $columns) && isset($current->server_updated)) {
                $last_updated = $current->server_updated;
            }
            
            return [
                'total_gb' => $total_gb,
                'used_gb' => $used_gb,
                'free_gb' => $total_gb - $used_gb,
                'percentage_used' => round($percentage, 2),
                'last_updated' => $last_updated,
                'status' => $this->get_usage_status($percentage)
            ];
            
        } catch (Exception $e) {
            error_log('Get usage statistics error: ' . $e->getMessage());
            return null;
        }
    }

    /**
     * 🚦 กำหนดสถานะการใช้งาน
     */
    private function get_usage_status($percentage)
    {
        if ($percentage >= 90) {
            return 'critical';
        } elseif ($percentage >= 70) {
            return 'warning';
        } else {
            return 'normal';
        }
    }

    /**
     * 🔄 Hooks
     */
    public function on_file_upload()
    {
        $this->update_storage_usage();
    }

    public function on_file_delete()
    {
        $this->update_storage_usage();
    }
}