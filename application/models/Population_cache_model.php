<?php
defined('BASEPATH') OR exit('No direct script access allowed');

/**
 * Population Cache Model
 * - สร้างตารางอัตโนมัติถ้ายังไม่มี
 * - เก็บข้อมูลประชากรจาก API
 * - Update ทับถ้าดึงเดือน-ปีเดิม
 * - Fallback เมื่อ API ภายนอกมีปัญหา
 */
class Population_cache_model extends CI_Model
{
    private $table = 'tbl_population_cache';
    
    public function __construct()
    {
        parent::__construct();
        
        // สร้างตารางอัตโนมัติถ้ายังไม่มี
        $this->create_table_if_not_exists();
    }

    /**
     * สร้างตารางอัตโนมัติถ้ายังไม่มี
     */
    private function create_table_if_not_exists()
    {
        // เก็บค่า db_debug เดิมไว้
        $db_debug = $this->db->db_debug;
        
        // ปิด db_debug ชั่วคราวเพื่อไม่ให้ error แสดงออกหน้าจอ
        $this->db->db_debug = FALSE;
        
        try {
            // วิธีที่ 1: ใช้ SHOW TABLES (ปลอดภัยที่สุด)
            $query = $this->db->query("SHOW TABLES LIKE '{$this->table}'");
            
            if ($query->num_rows() > 0) {
                log_message('info', 'Table ' . $this->table . ' already exists');
                $this->db->db_debug = $db_debug; // เปิด debug กลับ
                return true;
            }
            
            // ไม่มีตาราง → สร้างใหม่
            log_message('info', 'Table ' . $this->table . ' does not exist, creating...');
            
            $sql = "CREATE TABLE IF NOT EXISTS `{$this->table}` (
                `id` int(11) NOT NULL AUTO_INCREMENT,
                `yymm` varchar(4) NOT NULL COMMENT 'รูปแบบ YYMM เช่น 6810 = ตุลาคม 2568',
                `province_code` varchar(2) NOT NULL COMMENT 'รหัสจังหวัด เช่น 40',
                `district_code` varchar(4) NOT NULL COMMENT 'รหัสอำเภอ เช่น 4001',
                `subdistric_code` varchar(6) NOT NULL COMMENT 'รหัสตำบล เช่น 400101',
                `village_code` varchar(10) DEFAULT NULL COMMENT 'รหัสหมู่บ้าน/ชุมชน',
                `village_name` varchar(255) NOT NULL COMMENT 'ชื่อหมู่บ้าน/ชุมชน',
                `male_thai` int(11) DEFAULT 0 COMMENT 'ชายสัญชาติไทย',
                `female_thai` int(11) DEFAULT 0 COMMENT 'หญิงสัญชาติไทย',
                `total_thai` int(11) DEFAULT 0 COMMENT 'รวมสัญชาติไทย',
                `male_all` int(11) DEFAULT 0 COMMENT 'ชายทั้งหมด (ไทย+ต่างชาติ)',
                `female_all` int(11) DEFAULT 0 COMMENT 'หญิงทั้งหมด (ไทย+ต่างชาติ)',
                `total_all` int(11) DEFAULT 0 COMMENT 'รวมทั้งหมด',
                `male_foreign` int(11) DEFAULT 0 COMMENT 'ชายต่างชาติ (คำนวณ)',
                `female_foreign` int(11) DEFAULT 0 COMMENT 'หญิงต่างชาติ (คำนวณ)',
                `total_foreign` int(11) DEFAULT 0 COMMENT 'รวมต่างชาติ (คำนวณ)',
                `raw_data` text DEFAULT NULL COMMENT 'ข้อมูลดิบจาก API (JSON)',
                `data_source` varchar(20) DEFAULT 'api' COMMENT 'แหล่งข้อมูล: api, manual',
                `created_at` datetime DEFAULT CURRENT_TIMESTAMP,
                `updated_at` datetime DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
                PRIMARY KEY (`id`),
                UNIQUE KEY `unique_location_yymm` (`yymm`, `province_code`, `district_code`, `subdistric_code`, `village_name`),
                KEY `idx_yymm` (`yymm`),
                KEY `idx_location` (`province_code`, `district_code`, `subdistric_code`)
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='Cache ข้อมูลประชากรจาก DOPA API'";
            
            $result = $this->db->query($sql);
            
            // เปิด db_debug กลับ
            $this->db->db_debug = $db_debug;
            
            if ($result) {
                log_message('info', 'Table ' . $this->table . ' created successfully');
                return true;
            } else {
                log_message('error', 'Failed to create table ' . $this->table);
                return false;
            }
            
        } catch (Exception $e) {
            // เปิด db_debug กลับ
            $this->db->db_debug = $db_debug;
            
            log_message('error', 'Exception in create_table_if_not_exists: ' . $e->getMessage());
            return false;
        }
    }

    /**
     * บันทึกหรืออัปเดตข้อมูลจาก API
     * - ถ้ามีข้อมูลเดือน-ปีเดิม → UPDATE
     * - ถ้าไม่มี → INSERT
     * 
     * @param string $yymm รูปแบบ YYMM เช่น "6810"
     * @param array $api_data ข้อมูลจาก API (array of objects)
     * @param string $province_code รหัสจังหวัด
     * @param string $district_code รหัสอำเภอ
     * @param string $subdistric_code รหัสตำบล
     * @return bool
     */
    public function save_api_data($yymm, $api_data, $province_code, $district_code, $subdistric_code)
    {
        if (empty($api_data)) {
            log_message('warning', 'Population Cache - No data to save');
            return false;
        }

        log_message('info', "Population Cache - Saving " . count($api_data) . " records for YYMM: $yymm");

        // เก็บค่า db_debug เดิม
        $db_debug = $this->db->db_debug;
        $this->db->db_debug = FALSE;

        $success_count = 0;
        $update_count = 0;
        $insert_count = 0;

        foreach ($api_data as $item) {
            try {
                // เตรียมข้อมูล
                $data = [
                    'yymm' => $yymm,
                    'province_code' => $province_code,
                    'district_code' => $district_code,
                    'subdistric_code' => $subdistric_code,
                    'village_code' => isset($item->lsmmCode) ? $item->lsmmCode : null,
                    'village_name' => $item->ci_name ?? $item->lsmmDesc ?? '',
                    'male_thai' => (int)($item->male_thai ?? 0),
                    'female_thai' => (int)($item->female_thai ?? 0),
                    'total_thai' => (int)($item->total_thai ?? 0),
                    'male_all' => (int)($item->male_all ?? 0),
                    'female_all' => (int)($item->female_all ?? 0),
                    'total_all' => (int)($item->total_all ?? 0),
                    'male_foreign' => (int)($item->male_foreign ?? 0),
                    'female_foreign' => (int)($item->female_foreign ?? 0),
                    'total_foreign' => (int)($item->total_foreign ?? 0),
                    'raw_data' => json_encode($item, JSON_UNESCAPED_UNICODE),
                    'data_source' => 'api',
                    'updated_at' => date('Y-m-d H:i:s')  // ⭐ บังคับให้ update ทุกครั้ง
                ];

                // ตรวจสอบว่ามีข้อมูลเดิมหรือไม่
                $this->db->where('yymm', $yymm);
                $this->db->where('province_code', $province_code);
                $this->db->where('district_code', $district_code);
                $this->db->where('subdistric_code', $subdistric_code);
                $this->db->where('village_name', $data['village_name']);
                $existing = $this->db->get($this->table);

                if ($existing && $existing->num_rows() > 0) {
                    // มีข้อมูลเดิม → UPDATE (บังคับ updated_at)
                    $this->db->where('yymm', $yymm);
                    $this->db->where('province_code', $province_code);
                    $this->db->where('district_code', $district_code);
                    $this->db->where('subdistric_code', $subdistric_code);
                    $this->db->where('village_name', $data['village_name']);
                    
                    $result = $this->db->update($this->table, $data);
                    
                    // ⭐ แม้ affected_rows = 0 (ข้อมูลเหมือนเดิม) ก็นับเป็น success
                    if ($result !== false) {  // false = error, true/0 = success
                        $update_count++;
                        $success_count++;
                        log_message('debug', "Updated: {$data['village_name']} (affected: " . $this->db->affected_rows() . ")");
                    }
                } else {
                    // ไม่มีข้อมูลเดิม → INSERT
                    if ($this->db->insert($this->table, $data)) {
                        $insert_count++;
                        $success_count++;
                        log_message('debug', "Inserted: {$data['village_name']}");
                    }
                }
                
            } catch (Exception $e) {
                log_message('error', 'Error saving record: ' . $e->getMessage());
                continue;
            }
        }

        // เปิด db_debug กลับ
        $this->db->db_debug = $db_debug;

        log_message('info', "Population Cache - Saved: $success_count records (Insert: $insert_count, Update: $update_count)");

        return $success_count > 0;
    }

    /**
     * ดึงข้อมูลจาก Cache ตามเงื่อนไข
     * 
     * @param string $yymm
     * @param string $province_code
     * @param string $district_code
     * @param string $subdistric_code
     * @return array|false
     */
    public function get_cached_data($yymm, $province_code, $district_code, $subdistric_code)
    {
        // เก็บค่า db_debug เดิม
        $db_debug = $this->db->db_debug;
        $this->db->db_debug = FALSE;
        
        try {
            $this->db->select('*');
            $this->db->from($this->table);
            $this->db->where('yymm', $yymm);
            $this->db->where('province_code', $province_code);
            $this->db->where('district_code', $district_code);
            $this->db->where('subdistric_code', $subdistric_code);
            $this->db->order_by('village_name', 'asc');
            
            $query = $this->db->get();

            // เปิด db_debug กลับ
            $this->db->db_debug = $db_debug;

            if ($query && $query->num_rows() > 0) {
                log_message('info', "Population Cache - Found " . $query->num_rows() . " cached records for YYMM: $yymm");
                
                // แปลงเป็น format เดียวกับ API
                $result = [];
                foreach ($query->result() as $row) {
                    $obj = new stdClass();
                    $obj->ci_name = $row->village_name;
                    $obj->ci_home = '-';
                    $obj->male_thai = (int)$row->male_thai;
                    $obj->female_thai = (int)$row->female_thai;
                    $obj->total_thai = (int)$row->total_thai;
                    $obj->male_all = (int)$row->male_all;
                    $obj->female_all = (int)$row->female_all;
                    $obj->total_all = (int)$row->total_all;
                    $obj->male_foreign = (int)$row->male_foreign;
                    $obj->female_foreign = (int)$row->female_foreign;
                    $obj->total_foreign = (int)$row->total_foreign;
                    
                    // Backward compatibility
                    $obj->ci_man = $obj->male_thai;
                    $obj->ci_woman = $obj->female_thai;
                    $obj->ci_total = $obj->total_thai;
                    
                    $result[] = $obj;
                }
                
                return $result;
            }

            log_message('info', "Population Cache - No cached data found for YYMM: $yymm");
            return false;
            
        } catch (Exception $e) {
            // เปิด db_debug กลับ
            $this->db->db_debug = $db_debug;
            
            log_message('error', 'Error getting cached data: ' . $e->getMessage());
            return false;
        }
    }

    /**
     * ตรวจสอบว่ามีข้อมูลใน Cache หรือไม่
     */
    public function has_cached_data($yymm, $province_code, $district_code, $subdistric_code)
    {
        $this->db->where('yymm', $yymm);
        $this->db->where('province_code', $province_code);
        $this->db->where('district_code', $district_code);
        $this->db->where('subdistric_code', $subdistric_code);
        
        return $this->db->count_all_results($this->table) > 0;
    }

    /**
     * ลบข้อมูลเก่าที่เกินกำหนด (เช่น เก็บแค่ 12 เดือนล่าสุด)
     * 
     * @param int $keep_months จำนวนเดือนที่ต้องการเก็บ
     * @return int จำนวน record ที่ลบ
     */
    public function cleanup_old_data($keep_months = 12)
    {
        // คำนวณ YYMM ที่เก่าที่สุดที่ต้องเก็บ
        $current_year = (int)date('Y') + 543; // พ.ศ.
        $current_month = (int)date('m');
        
        // ย้อนกลับตามจำนวนเดือน
        $target_month = $current_month - $keep_months;
        $target_year = $current_year;
        
        while ($target_month < 1) {
            $target_month += 12;
            $target_year--;
        }
        
        $min_yymm = substr($target_year, -2) . str_pad($target_month, 2, '0', STR_PAD_LEFT);
        
        log_message('info', "Population Cache - Cleaning up data older than YYMM: $min_yymm");
        
        $this->db->where('yymm <', $min_yymm);
        $this->db->delete($this->table);
        
        $deleted = $this->db->affected_rows();
        log_message('info', "Population Cache - Deleted $deleted old records");
        
        return $deleted;
    }

    /**
     * ดึงรายการเดือน-ปีที่มีข้อมูลใน Cache
     */
    public function get_available_months()
    {
        $this->db->select('DISTINCT(yymm) as yymm');
        $this->db->from($this->table);
        $this->db->order_by('yymm', 'desc');
        
        $query = $this->db->get();
        return $query->result();
    }

    /**
     * ลบข้อมูลทั้งหมดของเดือน-ปีที่ระบุ
     */
    public function delete_by_yymm($yymm)
    {
        $this->db->where('yymm', $yymm);
        $result = $this->db->delete($this->table);
        
        if ($result) {
            log_message('info', "Population Cache - Deleted all data for YYMM: $yymm");
        }
        
        return $result;
    }

    /**
     * ดึงสถิติการใช้งาน Cache
     */
    public function get_cache_stats()
    {
        $stats = [];
        
        // จำนวน record ทั้งหมด
        $stats['total_records'] = $this->db->count_all($this->table);
        
        // จำนวนเดือน-ปีที่มีข้อมูล
        $this->db->select('COUNT(DISTINCT yymm) as month_count');
        $query = $this->db->get($this->table);
        $stats['month_count'] = $query->row()->month_count;
        
        // วันที่อัปเดตล่าสุด
        $this->db->select_max('updated_at');
        $query = $this->db->get($this->table);
        $stats['last_updated'] = $query->row()->updated_at;
        
        return $stats;
    }
}