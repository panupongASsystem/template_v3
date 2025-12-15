<?php
defined('BASEPATH') or exit('No direct script access allowed');

class Theme_model extends CI_Model
{

    private $table = 'system_theme';

    public function __construct()
    {
        parent::__construct();
        $this->load->database();
    }

    /**
     * ดึงธีมปัจจุบันของระบบ (record ล่าสุด)
     */
    public function get_current_theme()
    {
        $this->db->select('*');
        $this->db->from($this->table);
        $this->db->order_by('id', 'DESC');
        $this->db->limit(1);

        $query = $this->db->get();

        if ($query->num_rows() > 0) {
            return $query->row();
        } else {
            // ส่งค่าเริ่มต้นถ้าไม่มีข้อมูลในฐานข้อมูล
            return (object) array(
                'id' => 0,
                'primary_color' => '#179CB1',
                'gradient_start' => '#667eea',
                'gradient_end' => '#764ba2',
                'theme_name' => 'Default Theme',
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s'),
                'updated_by' => 1
            );
        }
    }

    /**
     * บันทึกธีมใหม่ (INSERT เสมอเพื่อเก็บประวัติ)
     */
    public function save_theme($data)
    {
        try {
            // เพิ่มข้อมูลเวลา
            $data['created_at'] = date('Y-m-d H:i:s');
            $data['updated_at'] = date('Y-m-d H:i:s');

            // INSERT ข้อมูลใหม่เสมอเพื่อเก็บประวัติ
            $result = $this->db->insert($this->table, $data);

            if ($result) {
                return $this->db->insert_id();
            }

            return false;
        } catch (Exception $e) {
            log_message('error', 'Theme Model Save Error: ' . $e->getMessage());
            return false;
        }
    }

    /**
     * อัปเดตธีมที่มีอยู่ (สำหรับกรณีพิเศษ)
     */
    public function update_theme($id, $data)
    {
        try {
            $data['updated_at'] = date('Y-m-d H:i:s');

            $this->db->where('id', $id);
            return $this->db->update($this->table, $data);
        } catch (Exception $e) {
            log_message('error', 'Theme Model Update Error: ' . $e->getMessage());
            return false;
        }
    }

    /**
     * ดึงประวัติการเปลี่ยนธีม
     */
    public function get_theme_history($limit = 10)
    {
        $this->db->select('*');
        $this->db->from($this->table);
        $this->db->order_by('id', 'DESC');

        if ($limit > 0) {
            $this->db->limit($limit);
        }

        $query = $this->db->get();

        if ($query->num_rows() > 0) {
            return $query->result();
        }

        return array();
    }

    /**
     * ดึงธีมตาม ID
     */
    public function get_theme_by_id($id)
    {
        $this->db->where('id', $id);
        $query = $this->db->get($this->table);

        if ($query->num_rows() == 1) {
            return $query->row();
        }

        return false;
    }

    /**
     * ลบธีม
     */
    public function delete_theme($id)
    {
        try {
            // ป้องกันไม่ให้ลบถ้าเหลือแค่ 1 record
            $total = $this->count_themes();
            if ($total <= 1) {
                return false;
            }

            $this->db->where('id', $id);
            return $this->db->delete($this->table);
        } catch (Exception $e) {
            log_message('error', 'Theme Model Delete Error: ' . $e->getMessage());
            return false;
        }
    }

    /**
     * นับจำนวนธีมทั้งหมด
     */
    public function count_themes()
    {
        return $this->db->count_all($this->table);
    }

    /**
     * ดึงธีมทั้งหมด
     */
    public function get_all_themes()
    {
        $this->db->select('*');
        $this->db->from($this->table);
        $this->db->order_by('id', 'DESC');

        $query = $this->db->get();

        if ($query->num_rows() > 0) {
            return $query->result();
        }

        return array();
    }

    /**
     * ค้นหาธีมตามชื่อ
     */
    public function search_themes($search_term)
    {
        $this->db->select('*');
        $this->db->from($this->table);
        $this->db->like('theme_name', $search_term);
        $this->db->order_by('id', 'DESC');

        $query = $this->db->get();

        if ($query->num_rows() > 0) {
            return $query->result();
        }

        return array();
    }

    /**
     * ตรวจสอบว่าธีมมีอยู่หรือไม่
     */
    public function theme_exists($id)
    {
        $this->db->where('id', $id);
        $query = $this->db->get($this->table);
        return $query->num_rows() > 0;
    }

    /**
     * สร้างธีมเริ่มต้นถ้ายังไม่มี
     */
    public function create_default_theme()
    {
        // ตรวจสอบว่ามีธีมอยู่แล้วหรือไม่
        if ($this->count_themes() == 0) {
            $default_theme = array(
                'primary_color' => '#179CB1',
                'gradient_start' => '#667eea',
                'gradient_end' => '#764ba2',
                'theme_name' => 'Default Theme',
                'updated_by' => 1, // Admin ID
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s')
            );

            return $this->save_theme($default_theme);
        }

        return true;
    }

    /**
     * ล้างธีมเก่าที่เกิน limit ที่กำหนด
     */
    public function cleanup_old_themes($keep_last = 10)
    {
        try {
            // นับจำนวน records ทั้งหมด
            $total_records = $this->db->count_all($this->table);

            // ถ้ามีมากกว่าที่ต้องการเก็บไว้
            if ($total_records > $keep_last) {
                // ดึง ID ของ records เก่าที่ต้องลบ
                $this->db->select('id');
                $this->db->from($this->table);
                $this->db->order_by('id', 'ASC');
                $this->db->limit($total_records - $keep_last);

                $query = $this->db->get();

                if ($query->num_rows() > 0) {
                    $old_ids = array();
                    foreach ($query->result() as $row) {
                        $old_ids[] = $row->id;
                    }

                    // ลบ records เก่า
                    $this->db->where_in('id', $old_ids);
                    return $this->db->delete($this->table);
                }
            }

            return true;
        } catch (Exception $e) {
            log_message('error', 'Theme Model Cleanup Error: ' . $e->getMessage());
            return false;
        }
    }

    /**
     * ดึงสถิติการใช้งานธีม
     */
    public function get_theme_statistics()
    {
        try {
            // จำนวนธีมทั้งหมด
            $total_themes = $this->count_themes();

            // ธีมที่ถูกใช้มากที่สุด
            $this->db->select('theme_name, COUNT(*) as usage_count');
            $this->db->from($this->table);
            $this->db->group_by('theme_name');
            $this->db->order_by('usage_count', 'DESC');
            $this->db->limit(5);
            $popular_themes = $this->db->get()->result();

            // การเปลี่ยนแปลงในเดือนนี้
            $this->db->select('COUNT(*) as monthly_changes');
            $this->db->from($this->table);
            $this->db->where('MONTH(created_at)', date('m'));
            $this->db->where('YEAR(created_at)', date('Y'));
            $monthly_changes = $this->db->get()->row()->monthly_changes;

            // ผู้ใช้ที่เปลี่ยนธีมมากที่สุด
            $this->db->select('updated_by, COUNT(*) as change_count');
            $this->db->from($this->table);
            $this->db->where('updated_by IS NOT NULL');
            $this->db->group_by('updated_by');
            $this->db->order_by('change_count', 'DESC');
            $this->db->limit(5);
            $top_users = $this->db->get()->result();

            return array(
                'total_themes' => $total_themes,
                'popular_themes' => $popular_themes,
                'monthly_changes' => $monthly_changes,
                'top_users' => $top_users
            );

        } catch (Exception $e) {
            log_message('error', 'Theme Statistics Error: ' . $e->getMessage());
            return array(
                'total_themes' => 0,
                'popular_themes' => array(),
                'monthly_changes' => 0,
                'top_users' => array()
            );
        }
    }

    /**
     * ดึงธีมตามช่วงเวลา
     */
    public function get_themes_by_date_range($start_date, $end_date)
    {
        $this->db->select('*');
        $this->db->from($this->table);
        $this->db->where('DATE(created_at) >=', $start_date);
        $this->db->where('DATE(created_at) <=', $end_date);
        $this->db->order_by('created_at', 'DESC');

        $query = $this->db->get();

        if ($query->num_rows() > 0) {
            return $query->result();
        }

        return array();
    }

    /**
     * ดึงธีมตามผู้ใช้
     */
    public function get_themes_by_user($user_id)
    {
        $this->db->select('*');
        $this->db->from($this->table);
        $this->db->where('updated_by', $user_id);
        $this->db->order_by('created_at', 'DESC');

        $query = $this->db->get();

        if ($query->num_rows() > 0) {
            return $query->result();
        }

        return array();
    }

    /**
     * ดึงธีมที่ได้รับความนิยม (ใช้บ่อย)
     */
    public function get_popular_themes($limit = 5)
    {
        $this->db->select('theme_name, primary_color, gradient_start, gradient_end, COUNT(*) as usage_count');
        $this->db->from($this->table);
        $this->db->group_by(array('theme_name', 'primary_color', 'gradient_start', 'gradient_end'));
        $this->db->order_by('usage_count', 'DESC');
        $this->db->limit($limit);

        $query = $this->db->get();

        if ($query->num_rows() > 0) {
            return $query->result();
        }

        return array();
    }

    /**
     * ดึงธีมล่าสุดของแต่ละชื่อ (unique theme names)
     */
    public function get_unique_themes()
    {
        $this->db->select('*');
        $this->db->from($this->table . ' t1');
        $this->db->join(
            '(SELECT theme_name, MAX(id) as max_id FROM ' . $this->table . ' GROUP BY theme_name) t2',
            't1.id = t2.max_id',
            'inner'
        );
        $this->db->order_by('t1.created_at', 'DESC');

        $query = $this->db->get();

        if ($query->num_rows() > 0) {
            return $query->result();
        }

        return array();
    }

    /**
     * สำรองข้อมูลธีม
     */
    public function backup_themes()
    {
        try {
            $themes = $this->get_all_themes();
            $backup_data = array(
                'backup_date' => date('Y-m-d H:i:s'),
                'total_records' => count($themes),
                'version' => '1.0',
                'database_info' => array(
                    'table_name' => $this->table,
                    'fields' => $this->db->list_fields($this->table)
                ),
                'themes' => $themes
            );

            $backup_json = json_encode($backup_data, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);
            
            // สร้างโฟลเดอร์ backup ถ้ายังไม่มี
            $backup_dir = FCPATH . 'assets/backups/';
            if (!is_dir($backup_dir)) {
                mkdir($backup_dir, 0755, true);
            }

            $backup_file = $backup_dir . 'themes_backup_' . date('Y-m-d_H-i-s') . '.json';

            if (file_put_contents($backup_file, $backup_json)) {
                return $backup_file;
            }

            return false;
        } catch (Exception $e) {
            log_message('error', 'Theme Backup Error: ' . $e->getMessage());
            return false;
        }
    }

    /**
     * กู้คืนข้อมูลธีมจากไฟล์สำรอง
     */
    public function restore_themes($backup_file)
    {
        try {
            if (!file_exists($backup_file)) {
                return false;
            }

            $backup_content = file_get_contents($backup_file);
            $backup_data = json_decode($backup_content, true);

            if (!$backup_data || !isset($backup_data['themes'])) {
                return false;
            }

            // เริ่ม transaction
            $this->db->trans_start();

            // ลบข้อมูลเก่าทั้งหมด (ระวัง!)
            $this->db->empty_table($this->table);

            // กู้คืนข้อมูล
            foreach ($backup_data['themes'] as $theme) {
                // แปลง object เป็น array ถ้าจำเป็น
                if (is_object($theme)) {
                    $theme = (array) $theme;
                }
                
                // ลบ ID เพื่อให้ auto increment ใหม่
                unset($theme['id']);
                $this->db->insert($this->table, $theme);
            }

            // สิ้นสุด transaction
            $this->db->trans_complete();

            return $this->db->trans_status();

        } catch (Exception $e) {
            log_message('error', 'Theme Restore Error: ' . $e->getMessage());
            return false;
        }
    }

    /**
     * ดึงรายการไฟล์สำรอง
     */
    public function get_backup_files()
    {
        try {
            $backup_dir = FCPATH . 'assets/backups/';
            
            if (!is_dir($backup_dir)) {
                return array();
            }

            $files = glob($backup_dir . 'themes_backup_*.json');
            $backup_files = array();

            foreach ($files as $file) {
                $filename = basename($file);
                $filesize = filesize($file);
                $filemtime = filemtime($file);
                
                $backup_files[] = array(
                    'filename' => $filename,
                    'filepath' => $file,
                    'size' => $this->format_bytes($filesize),
                    'date' => date('d/m/Y H:i:s', $filemtime),
                    'timestamp' => $filemtime
                );
            }

            // เรียงตามวันที่ล่าสุด
            usort($backup_files, function($a, $b) {
                return $b['timestamp'] - $a['timestamp'];
            });

            return $backup_files;

        } catch (Exception $e) {
            log_message('error', 'Get Backup Files Error: ' . $e->getMessage());
            return array();
        }
    }

    /**
     * ลบไฟล์สำรอง
     */
    public function delete_backup_file($filename)
    {
        try {
            $backup_dir = FCPATH . 'assets/backups/';
            $file_path = $backup_dir . $filename;

            if (file_exists($file_path) && is_file($file_path)) {
                return unlink($file_path);
            }

            return false;
        } catch (Exception $e) {
            log_message('error', 'Delete Backup File Error: ' . $e->getMessage());
            return false;
        }
    }

    /**
     * ทำความสะอาดไฟล์สำรองเก่า
     */
    public function cleanup_old_backups($keep_count = 5)
    {
        try {
            $backup_files = $this->get_backup_files();
            
            if (count($backup_files) > $keep_count) {
                $files_to_delete = array_slice($backup_files, $keep_count);
                
                foreach ($files_to_delete as $file) {
                    $this->delete_backup_file($file['filename']);
                }
            }

            return true;
        } catch (Exception $e) {
            log_message('error', 'Cleanup Old Backups Error: ' . $e->getMessage());
            return false;
        }
    }

    /**
     * ตรวจสอบความถูกต้องของธีม
     */
    public function validate_theme_data($data)
    {
        $errors = array();

        // ตรวจสอบสีหลัก
        if (empty($data['primary_color']) || !$this->is_valid_hex_color($data['primary_color'])) {
            $errors[] = 'สีหลักไม่ถูกต้อง';
        }

        // ตรวจสอบสี gradient
        if (empty($data['gradient_start']) || !$this->is_valid_hex_color($data['gradient_start'])) {
            $errors[] = 'สีเริ่มต้น gradient ไม่ถูกต้อง';
        }

        if (empty($data['gradient_end']) || !$this->is_valid_hex_color($data['gradient_end'])) {
            $errors[] = 'สีสิ้นสุด gradient ไม่ถูกต้อง';
        }

        // ตรวจสอบชื่อธีม
        if (empty($data['theme_name']) || strlen($data['theme_name']) > 100) {
            $errors[] = 'ชื่อธีมไม่ถูกต้องหรือยาวเกินไป';
        }

        return $errors;
    }

    /**
     * ตรวจสอบรูปแบบสี Hex
     */
    private function is_valid_hex_color($color)
    {
        return preg_match('/^#([A-Fa-f0-9]{6}|[A-Fa-f0-9]{3})$/', $color);
    }

    /**
     * แปลงขนาดไฟล์เป็นรูปแบบที่อ่านง่าย
     */
    private function format_bytes($size, $precision = 2)
    {
        $units = array('B', 'KB', 'MB', 'GB', 'TB');
        
        for ($i = 0; $size > 1024 && $i < count($units) - 1; $i++) {
            $size /= 1024;
        }
        
        return round($size, $precision) . ' ' . $units[$i];
    }

    /**
     * ดึงข้อมูลสำหรับ Dashboard
     */
    public function get_dashboard_data()
    {
        try {
            $data = array();
            
            // ธีมปัจจุบัน
            $data['current_theme'] = $this->get_current_theme();
            
            // จำนวนธีมทั้งหมด
            $data['total_themes'] = $this->count_themes();
            
            // ธีมยอดนิยม
            $data['popular_themes'] = $this->get_popular_themes(3);
            
            // การเปลี่ยนแปลงล่าสุด
            $data['recent_changes'] = $this->get_theme_history(5);
            
            // สถิติเดือนนี้
            $this->db->select('COUNT(*) as monthly_count');
            $this->db->from($this->table);
            $this->db->where('MONTH(created_at)', date('m'));
            $this->db->where('YEAR(created_at)', date('Y'));
            $data['monthly_changes'] = $this->db->get()->row()->monthly_count;
            
            return $data;
            
        } catch (Exception $e) {
            log_message('error', 'Get Dashboard Data Error: ' . $e->getMessage());
            return array();
        }
    }

    /**
     * ส่งออกธีมในรูปแบบ CSS
     */
    public function export_theme_css($theme_id = null)
    {
        try {
            if ($theme_id) {
                $theme = $this->get_theme_by_id($theme_id);
            } else {
                $theme = $this->get_current_theme();
            }

            if (!$theme) {
                return false;
            }

            $css_content = "/* Theme: {$theme->theme_name} */\n";
            $css_content .= "/* Generated: " . date('Y-m-d H:i:s') . " */\n\n";
            $css_content .= ":root {\n";
            $css_content .= "    --primary-color: {$theme->primary_color};\n";
            $css_content .= "    --gradient-start: {$theme->gradient_start};\n";
            $css_content .= "    --gradient-end: {$theme->gradient_end};\n";
            $css_content .= "}\n\n";
            $css_content .= "/* Add your custom CSS rules here */\n";

            return $css_content;

        } catch (Exception $e) {
            log_message('error', 'Export Theme CSS Error: ' . $e->getMessage());
            return false;
        }
    }
}
?>