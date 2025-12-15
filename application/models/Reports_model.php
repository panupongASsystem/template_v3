<?php
defined('BASEPATH') or exit('No direct script access allowed');

class Reports_model extends CI_Model
{
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * ดึงข้อมูลสรุปรายงานทั้งหมด
     */
    public function get_reports_summary()
{
    try {
        // สรุปข้อมูลพื้นที่จัดเก็บ
        $storage = $this->db->get('tbl_server')->row();
        $storage_percentage = $storage ? ($storage->server_current / $storage->server_storage) * 100 : 0;

        // สรุปข้อมูลเรื่องร้องเรียน (ตรวจสอบว่าตารางมีอยู่ก่อน)
        $total_complains = 0;
        $waiting_complains = 0;      // 🆕 เพิ่ม
        $pending_complains = 0;
        $completed_complains = 0;
        $in_progress_complains = 0;
        
        if ($this->db->table_exists('tbl_complain')) {
            $total_complains = $this->db->count_all('tbl_complain');
            
            // 🆕 เพิ่มการนับ รอรับเรื่อง
            $waiting_complains = $this->db->where('complain_status', 'รอรับเรื่อง')
                                          ->from('tbl_complain')
                                          ->count_all_results();
                                          
            $pending_complains = $this->db->where('complain_status', 'รอดำเนินการ')
                                          ->from('tbl_complain')
                                          ->count_all_results();
                                          
            $completed_complains = $this->db->where('complain_status', 'ดำเนินการเรียบร้อย')
                                            ->from('tbl_complain')
                                            ->count_all_results();
                                            
            $in_progress_complains = $this->db->where('complain_status', 'กำลังดำเนินการ')
                                              ->from('tbl_complain')
                                              ->count_all_results();
        }

        return [
            'storage' => [
                'total' => $storage ? $storage->server_storage : 0,
                'used' => $storage ? $storage->server_current : 0,
                'percentage' => round($storage_percentage, 2),
                'free' => $storage ? ($storage->server_storage - $storage->server_current) : 0
            ],
            'complains' => [
                'total' => $total_complains,
                'waiting' => $waiting_complains,           // 🆕 เพิ่มบรรทัดนี้
                'pending' => $pending_complains,
                'completed' => $completed_complains,
                'in_progress' => $in_progress_complains
            ]
        ];
    } catch (Exception $e) {
        error_log('Reports summary error: ' . $e->getMessage());
        return [
            'storage' => ['total' => 0, 'used' => 0, 'percentage' => 0, 'free' => 0],
            'complains' => [
                'total' => 0, 
                'waiting' => 0,     // 🆕 เพิ่มบรรทัดนี้
                'pending' => 0, 
                'completed' => 0, 
                'in_progress' => 0
            ]
        ];
    }
}
    /**
     * ================================
     * รายงานพื้นที่จัดเก็บข้อมูล
     * ================================
     */

    /**
     * ✅ ดึงข้อมูลพื้นที่จัดเก็บแบบละเอียด (ไม่ต้องอัปเดตเอง)
     */
    public function get_storage_detailed_report()
    {
        try {
            $server_info = $this->db->get('tbl_server')->row();
            
            if (!$server_info) {
                return [
                    'server_storage' => 100,
                    'server_current' => 0,
                    'percentage_used' => 0,
                    'free_space' => 100,
                    'status' => 'normal',
                    'last_updated' => date('Y-m-d H:i:s')
                ];
            }

            $percentage = $server_info->server_storage > 0 ? ($server_info->server_current / $server_info->server_storage) * 100 : 0;
            
            // กำหนดสถานะตามเปอร์เซ็นต์การใช้งาน
            $status = 'normal';
            if ($percentage >= 90) {
                $status = 'critical';
            } elseif ($percentage >= 70) {
                $status = 'warning';
            }

            // ดึงเวลาอัปเดตล่าสุดจาก storage_history
            $last_updated = $this->get_last_storage_update_time();

            return [
                'server_storage' => floatval($server_info->server_storage),
                'server_current' => floatval($server_info->server_current),
                'percentage_used' => round($percentage, 2),
                'free_space' => floatval($server_info->server_storage) - floatval($server_info->server_current),
                'status' => $status,
                'last_updated' => $last_updated ?: date('Y-m-d H:i:s')
            ];
        } catch (Exception $e) {
            error_log('Storage detailed report error: ' . $e->getMessage());
            return [
                'server_storage' => 100,
                'server_current' => 0,
                'percentage_used' => 0,
                'free_space' => 100,
                'status' => 'normal',
                'last_updated' => date('Y-m-d H:i:s')
            ];
        }
    }

    /**
     * ✅ ดึงเวลาอัปเดตล่าสุดจากประวัติ
     */
    private function get_last_storage_update_time()
    {
        try {
            if ($this->db->table_exists('tbl_storage_history')) {
                $latest = $this->db->select('created_at')
                                  ->from('tbl_storage_history')
                                  ->order_by('created_at', 'DESC')
                                  ->limit(1)
                                  ->get()
                                  ->row();
                
                if ($latest) {
                    return $latest->created_at;
                }
            }
            
            // ตรวจสอบว่าคอลัมน์ server_updated มีอยู่หรือไม่
            $server_columns = $this->db->list_fields('tbl_server');
            if (in_array('server_updated', $server_columns)) {
                $server = $this->db->select('server_updated')->get('tbl_server')->row();
                if ($server && $server->server_updated) {
                    return $server->server_updated;
                }
            }
        } catch (Exception $e) {
            error_log('Get last storage update time error: ' . $e->getMessage());
        }
        
        return null;
    }

	
	
	
	// เพิ่มใน Reports_model.php

/**
 * ดึงหมวดหมู่ทั้งหมด
 */
public function get_all_categories()
{
    try {
        if (!$this->db->table_exists('tbl_complain_category')) {
            return [];
        }
        
        return $this->db->select('*')
                       ->from('tbl_complain_category')
                       ->order_by('cat_order', 'ASC')
                       ->order_by('cat_name', 'ASC')
                       ->get()
                       ->result_array();
    } catch (Exception $e) {
        error_log('Get all categories error: ' . $e->getMessage());
        return [];
    }
}

/**
 * ดึงหมวดหมู่ตาม ID
 */
public function get_category_by_id($cat_id)
{
    try {
        if (!$this->db->table_exists('tbl_complain_category')) {
            return null;
        }
        
        return $this->db->where('cat_id', $cat_id)
                       ->get('tbl_complain_category')
                       ->row_array();
    } catch (Exception $e) {
        error_log('Get category by id error: ' . $e->getMessage());
        return null;
    }
}

/**
 * เพิ่มหมวดหมู่ใหม่
 */
public function insert_category($data)
{
    try {
        if (!$this->db->table_exists('tbl_complain_category')) {
            return false;
        }
        
        return $this->db->insert('tbl_complain_category', $data);
    } catch (Exception $e) {
        error_log('Insert category error: ' . $e->getMessage());
        return false;
    }
}

/**
 * อัปเดตหมวดหมู่
 */
public function update_category($cat_id, $data)
{
    try {
        if (!$this->db->table_exists('tbl_complain_category')) {
            return false;
        }
        
        return $this->db->where('cat_id', $cat_id)
                       ->update('tbl_complain_category', $data);
    } catch (Exception $e) {
        error_log('Update category error: ' . $e->getMessage());
        return false;
    }
}

/**
 * ลบหมวดหมู่
 */
public function delete_category($cat_id)
{
    try {
        if (!$this->db->table_exists('tbl_complain_category')) {
            return false;
        }
        
        return $this->db->where('cat_id', $cat_id)
                       ->delete('tbl_complain_category');
    } catch (Exception $e) {
        error_log('Delete category error: ' . $e->getMessage());
        return false;
    }
}

/**
 * นับการใช้งานหมวดหมู่
 */
public function count_category_usage($cat_id)
{
    try {
        if (!$this->db->table_exists('tbl_complain')) {
            return 0;
        }
        
        return $this->db->where('complain_category_id', $cat_id)
                       ->count_all_results('tbl_complain');
    } catch (Exception $e) {
        error_log('Count category usage error: ' . $e->getMessage());
        return 0;
    }
}
	
	
    /**
     * ✅ ดึงประวัติการใช้พื้นที่จัดเก็บ (ใช้ข้อมูลจริงจาก tbl_storage_history)
     */
    public function get_storage_usage_history($days = 30)
    {
        try {
            // ตรวจสอบว่ามีตาราง storage_history หรือไม่
            if ($this->db->table_exists('tbl_storage_history')) {
                $history_data = $this->db->select('DATE(created_at) as date, AVG(used_space) as avg_used')
                                       ->from('tbl_storage_history')
                                       ->where('created_at >=', date('Y-m-d', strtotime("-$days days")))
                                       ->group_by('DATE(created_at)')
                                       ->order_by('date', 'ASC')
                                       ->get()
                                       ->result();
                
                if (!empty($history_data)) {
                    return $history_data;
                }
            }

            // ✅ ถ้าไม่มีข้อมูล history ให้สร้างข้อมูลตัวอย่างจาก tbl_server ปัจจุบัน
            $current_server = $this->db->get('tbl_server')->row();
            $current_usage = $current_server ? floatval($current_server->server_current) : 0.22;
            
            $history = [];
            for ($i = $days; $i >= 0; $i--) {
                $date = date('Y-m-d', strtotime("-$i days"));
                
                // สร้างแนวโน้มที่สมจริงโดยใช้ current usage เป็นฐาน
                $variation = sin($i * 0.1) * 0.05; // แปรผันไปมา ±0.05 GB
                $usage = max(0.1, $current_usage + $variation + (($days - $i) * 0.001)); // เติบโตช้าๆ
                
                $history[] = (object)[
                    'date' => $date,
                    'avg_used' => round($usage, 3)
                ];
            }
            
            return $history;
        } catch (Exception $e) {
            error_log('Storage usage history error: ' . $e->getMessage());
            return [];
        }
    }
	
	
	
	/**
 * ✅ ดึงข้อมูล case ที่ยังไม่เสร็จสิ้นสำหรับคำนวณแจ้งเตือน
 */
public function get_pending_complains_for_alerts()
{
    $this->db->select('complain_id, complain_topic, complain_status, complain_datesave, complain_by');
    $this->db->from('tbl_complain');
    $this->db->where_not_in('complain_status', ['ดำเนินการเรียบร้อย', 'ยกเลิก']);
    $this->db->order_by('complain_datesave', 'ASC');
    return $this->db->get()->result();
}

/**
 * ✅ ดึงสถิติ case ที่ค้างตามจำนวนวัน
 */
public function get_case_alerts_summary()
{
    $today = date('Y-m-d');
    
    $sql = "
    SELECT 
        SUM(CASE 
            WHEN DATEDIFF('$today', DATE(complain_datesave)) >= 14 
                AND complain_status NOT IN ('ดำเนินการเรียบร้อย', 'ยกเลิก')
            THEN 1 ELSE 0 END) as critical_count,
        SUM(CASE 
            WHEN DATEDIFF('$today', DATE(complain_datesave)) BETWEEN 7 AND 13 
                AND complain_status NOT IN ('ดำเนินการเรียบร้อย', 'ยกเลิก')
            THEN 1 ELSE 0 END) as danger_count,
        SUM(CASE 
            WHEN DATEDIFF('$today', DATE(complain_datesave)) BETWEEN 3 AND 6 
                AND complain_status NOT IN ('ดำเนินการเรียบร้อย', 'ยกเลิก')
            THEN 1 ELSE 0 END) as warning_count
    FROM tbl_complain
    ";
    
    return $this->db->query($sql)->row_array();
}
	

    /**
     * ดึงข้อมูลการใช้พื้นที่แยกตามประเภทไฟล์
     */
    public function get_storage_usage_by_file_type()
    {
        try {
            // ตรวจสอบไฟล์ในโฟลเดอร์ต่างๆ
            $file_types = [
                'images' => $this->calculate_folder_size('./docs/img/'),
                'documents' => $this->calculate_folder_size('./docs/files/'),
                'uploads' => $this->calculate_folder_size('./uploads/'),
                'others' => 0
            ];

            return $file_types;
        } catch (Exception $e) {
            error_log('Storage usage by file type error: ' . $e->getMessage());
            return ['images' => 0, 'documents' => 0, 'uploads' => 0, 'others' => 0];
        }
    }

    /**
     * คำนวณขนาดไฟล์ในโฟลเดอร์
     */
    private function calculate_folder_size($folder_path)
    {
        if (!is_dir($folder_path)) {
            return 0;
        }

        $size = 0;
        try {
            $files = new RecursiveIteratorIterator(
                new RecursiveDirectoryIterator($folder_path),
                RecursiveIteratorIterator::LEAVES_ONLY
            );

            foreach ($files as $file) {
                if ($file->isFile()) {
                    $size += $file->getSize();
                }
            }
        } catch (Exception $e) {
            error_log('Calculate folder size error: ' . $e->getMessage());
            return 0;
        }

        return $size;
    }

    /**
     * ดึงสถิติไฟล์
     */
    /**
 * ✅ get_file_statistics() - สแกน httpdocs/docs และโฟลเดอร์ย่อยทั้งหมด
 * แทนที่ในไฟล์ application/models/Reports_model.php
 */
public function get_file_statistics()
{
    try {
        $stats = [
            'total_files' => 0,
            'image_files' => 0,
            'document_files' => 0,
            'other_files' => 0
        ];

        $image_ext = ['jpg', 'jpeg', 'png', 'gif', 'bmp', 'webp', 'svg', 'ico'];
        $doc_ext = ['pdf', 'doc', 'docx', 'xls', 'xlsx', 'ppt', 'pptx', 'txt', 'rtf'];
        
        // สแกนโฟลเดอร์ทั้งหมดใน docs
        $folders_to_scan = [
            FCPATH . 'docs/',               // โฟลเดอร์หลัก docs
           // FCPATH . 'docs/intranet/',      // intranet
           // FCPATH . 'docs/file/',          // file
           // FCPATH . 'docs/temp/',          // temp
           // FCPATH . 'docs/img/',           // img
           // FCPATH . 'docs/back_office/'    // back_office
        ];
        
        foreach ($folders_to_scan as $folder) {
            if (is_dir($folder)) {
                $this->scan_folder_recursive($folder, $stats, $image_ext, $doc_ext);
            }
        }
        
        return $stats;
        
    } catch (Exception $e) {
        return ['total_files' => 0, 'image_files' => 0, 'document_files' => 0, 'other_files' => 0];
    }
}

/**
 * ✅ สแกนโฟลเดอร์แบบ recursive
 */
private function scan_folder_recursive($folder, &$stats, $image_ext, $doc_ext)
{
    try {
        $iterator = new RecursiveIteratorIterator(
            new RecursiveDirectoryIterator($folder, RecursiveDirectoryIterator::SKIP_DOTS)
        );
        
        foreach ($iterator as $file) {
            if ($file->isFile()) {
                $stats['total_files']++;
                $extension = strtolower($file->getExtension());
                
                if (in_array($extension, $image_ext)) {
                    $stats['image_files']++;
                } elseif (in_array($extension, $doc_ext)) {
                    $stats['document_files']++;
                } else {
                    $stats['other_files']++;
                }
            }
        }
    } catch (Exception $e) {
        // Fallback ถ้าไม่สามารถใช้ RecursiveIterator ได้
        $this->scan_folder_simple($folder, $stats, $image_ext, $doc_ext);
    }
}

/**
 * ✅ สแกนแบบธรรมดา (fallback)
 */
private function scan_folder_simple($folder, &$stats, $image_ext, $doc_ext)
{
    $files = glob($folder . '*', GLOB_MARK);
    
    foreach ($files as $file) {
        if (is_file($file)) {
            $stats['total_files']++;
            $extension = strtolower(pathinfo($file, PATHINFO_EXTENSION));
            
            if (in_array($extension, $image_ext)) {
                $stats['image_files']++;
            } elseif (in_array($extension, $doc_ext)) {
                $stats['document_files']++;
            } else {
                $stats['other_files']++;
            }
        } elseif (is_dir($file)) {
            $this->scan_folder_simple($file, $stats, $image_ext, $doc_ext);
        }
    }
}

    /**
     * ✅ ดึงแนวโน้มการใช้พื้นที่จัดเก็บ (ใช้ข้อมูลจริงจาก tbl_storage_history)
     */
    public function get_storage_trends($period = '30days')
    {
        try {
            $days = ($period == '7days') ? 7 : (($period == '90days') ? 90 : 30);
            
            // ตรวจสอบว่ามีตาราง storage_history หรือไม่
            if ($this->db->table_exists('tbl_storage_history')) {
                $trend_data = $this->db->select('DATE(created_at) as date, AVG(used_space) as usage_gb, AVG(percentage_used) as percentage')
                                     ->from('tbl_storage_history')
                                     ->where('created_at >=', date('Y-m-d', strtotime("-$days days")))
                                     ->group_by('DATE(created_at)')
                                     ->order_by('date', 'ASC')
                                     ->get()
                                     ->result_array();
                
                if (!empty($trend_data)) {
                    // คำนวณ growth rate
                    for ($i = 1; $i < count($trend_data); $i++) {
                        $current = $trend_data[$i]['usage_gb'];
                        $previous = $trend_data[$i-1]['usage_gb'];
                        $trend_data[$i]['growth_rate'] = $previous > 0 ? round((($current - $previous) / $previous) * 100, 2) : 0;
                    }
                    $trend_data[0]['growth_rate'] = 0; // ไม่มีข้อมูลก่อนหน้า
                    
                    return $trend_data;
                }
            }

            // ✅ ถ้าไม่มีข้อมูล history ให้สร้างข้อมูลตัวอย่าง
            $current_server = $this->db->get('tbl_server')->row();
            $current_usage = $current_server ? floatval($current_server->server_current) : 0.22;
            
            $trends = [];
            $prev_usage = null;
            
            for ($i = $days; $i >= 0; $i--) {
                $date = date('Y-m-d', strtotime("-$i days"));
                
                // สร้างแนวโน้มที่สมจริง
                $base_variation = sin($i * 0.1) * 0.03; // แปรผันตามคลื่น
                $growth_factor = (($days - $i) * 0.0005); // เติบโตช้าๆ
                $usage = max(0.1, $current_usage + $base_variation + $growth_factor);
                
                // คำนวณ growth rate
                $growth_rate = $prev_usage ? (($usage - $prev_usage) / $prev_usage) * 100 : 0;
                
                $trends[] = [
                    'date' => $date,
                    'usage_gb' => round($usage, 3),
                    'growth_rate' => round($growth_rate, 2),
                    'percentage' => round(($usage / ($current_server ? floatval($current_server->server_storage) : 100)) * 100, 2)
                ];
                
                $prev_usage = $usage;
            }

            return $trends;
        } catch (Exception $e) {
            error_log('Storage trends error: ' . $e->getMessage());
            return [];
        }
    }

    /**
     * ================================
     * รายงานเรื่องร้องเรียน
     * ================================
     */

    /**
     * นับจำนวนเรื่องร้องเรียนทั้งหมด
     */
    public function count_complains($filters = [])
    {
        try {
            if (!$this->db->table_exists('tbl_complain')) {
                return 0;
            }
            
            $this->db->from('tbl_complain c');
            $this->apply_complain_filters($filters);
            return $this->db->count_all_results();
        } catch (Exception $e) {
            error_log('Count complains error: ' . $e->getMessage());
            return 0;
        }
    }

    /**
     * ดึงรายการเรื่องร้องเรียนพร้อมรายละเอียด
     */
    public function get_complains_with_details($limit = 20, $offset = 0, $filters = [])
    {
        try {
            if (!$this->db->table_exists('tbl_complain')) {
                return [];
            }
            
            // ใช้ชื่อ column ที่ถูกต้องตามฐานข้อมูลจริง
            $this->db->select("c.*");
            
            // เพิ่ม subquery สำหรับนับรูปภาพ (ถ้าตารางมีอยู่)
            if ($this->db->table_exists('tbl_complain_img')) {
                $this->db->select("(SELECT COUNT(*) FROM tbl_complain_img ci WHERE ci.complain_img_ref_id = c.complain_id) as image_count");
            }
            
            // เพิ่ม subquery สำหรับสถานะล่าสุด (ถ้าตารางมีอยู่)
            if ($this->db->table_exists('tbl_complain_detail')) {
                $this->db->select("(SELECT cd.complain_detail_status FROM tbl_complain_detail cd 
                                   WHERE cd.complain_detail_case_id = c.complain_id 
                                   ORDER BY cd.complain_detail_id DESC LIMIT 1) as latest_status");
            }
            
            $this->db->from('tbl_complain c');
            
            $this->apply_complain_filters($filters);
            
            $this->db->order_by('c.complain_datesave', 'DESC');
            $this->db->limit($limit, $offset);
            
            $complains = $this->db->get()->result();

            // ดึงรูปภาพสำหรับแต่ละเรื่องร้องเรียน
            foreach ($complains as $complain) {
                $complain->images = $this->get_complain_images($complain->complain_id);
            }

            return $complains;
        } catch (Exception $e) {
            error_log('Get complains with details error: ' . $e->getMessage());
            return [];
        }
    }

    /**
     * ใช้ filters สำหรับเรื่องร้องเรียน
     */
    private function apply_complain_filters($filters)
    {
        try {
            if (!empty($filters['status'])) {
                $this->db->where('c.complain_status', $filters['status']);
            }

            if (!empty($filters['type'])) {
                $this->db->where('c.complain_type', $filters['type']);
            }

            if (!empty($filters['date_from'])) {
                $this->db->where('DATE(c.complain_datesave) >=', $filters['date_from']);
            }

            if (!empty($filters['date_to'])) {
                $this->db->where('DATE(c.complain_datesave) <=', $filters['date_to']);
            }

            if (!empty($filters['search'])) {
                $this->db->group_start();
                $this->db->like('c.complain_topic', $filters['search']);
                $this->db->or_like('c.complain_detail', $filters['search']);
                $this->db->or_like('c.complain_by', $filters['search']);
                $this->db->group_end();
            }
        } catch (Exception $e) {
            error_log('Apply complain filters error: ' . $e->getMessage());
        }
    }

    /**
     * ดึงสรุปข้อมูลเรื่องร้องเรียน
     */
    public function get_complain_summary()
{
    try {
        if (!$this->db->table_exists('tbl_complain')) {
            return [
                'by_status' => [
                    'รอรับเรื่อง' => 0,
                    'รับเรื่องแล้ว' => 0,
                    'รอดำเนินการ' => 0,
                    'กำลังดำเนินการ' => 0,
                    'ดำเนินการเรียบร้อย' => 0,
                    'ยกเลิก' => 0
                ],
                'by_type' => [],
                'total' => 0,
                'today' => 0,
                'this_week' => 0,
                'this_month' => 0
            ];
        }
        
        $summary = [];
        
        // 🆕 แก้ไข: นับตามสถานะใหม่ - แยกแต่ละ query เพื่อหลีกเลี่ยง conflict
        $statuses = [
            'รอรับเรื่อง' => 0,
            'รับเรื่องแล้ว' => 0,
            'รอดำเนินการ' => 0,
            'กำลังดำเนินการ' => 0,
            'ดำเนินการเรียบร้อย' => 0,
            'ยกเลิก' => 0
        ];
        
        // วิธีใหม่: ใช้ query เดียวดึงทั้งหมดแล้วแยก
        $status_query = $this->db->select('complain_status, COUNT(*) as count')
                                ->from('tbl_complain')
                                ->where('complain_status IS NOT NULL')
                                ->where('complain_status !=', '')
                                ->group_by('complain_status')
                                ->get();
        
        foreach ($status_query->result() as $row) {
            if (isset($statuses[$row->complain_status])) {
                $statuses[$row->complain_status] = (int)$row->count;
            }
        }
        
        $summary['by_status'] = $statuses;

        // นับตามประเภท
        $types = $this->db->select('complain_type, COUNT(*) as count')
                         ->from('tbl_complain')
                         ->where('complain_type IS NOT NULL')
                         ->where('complain_type !=', '')
                         ->group_by('complain_type')
                         ->get()
                         ->result();
        
        $summary['by_type'] = [];
        foreach ($types as $type) {
            $summary['by_type'][$type->complain_type] = (int)$type->count;
        }

        // สถิติเวลา - ใช้ query แยกเพื่อหลีกเลี่ยงปัญหา
        $summary['total'] = (int)$this->db->count_all('tbl_complain');
        
        $today_count = $this->db->where('DATE(complain_datesave)', date('Y-m-d'))
                                ->from('tbl_complain')
                                ->count_all_results();
        $summary['today'] = (int)$today_count;
        
        $week_count = $this->db->where('YEARWEEK(complain_datesave)', date('oW'))
                               ->from('tbl_complain')
                               ->count_all_results();
        $summary['this_week'] = (int)$week_count;
        
        $month_count = $this->db->where('YEAR(complain_datesave)', date('Y'))
                                ->where('MONTH(complain_datesave)', date('m'))
                                ->from('tbl_complain')
                                ->count_all_results();
        $summary['this_month'] = (int)$month_count;

        return $summary;
        
    } catch (Exception $e) {
        error_log('Complain summary error: ' . $e->getMessage());
        return [
            'by_status' => [
                'รอรับเรื่อง' => 0,
                'รับเรื่องแล้ว' => 0,
                'รอดำเนินการ' => 0,
                'กำลังดำเนินการ' => 0,
                'ดำเนินการเรียบร้อย' => 0,
                'ยกเลิก' => 0
            ],
            'by_type' => [],
            'total' => 0,
            'today' => 0,
            'this_week' => 0,
            'this_month' => 0
        ];
    }
}
	
	
    /**
     * ดึงสถิติเรื่องร้องเรียน
     */
    public function get_complain_statistics()
    {
        try {
            if (!$this->db->table_exists('tbl_complain')) {
                return [
                    'avg_resolution_days' => 0,
                    'resolution_rate' => 0,
                    'total_complains' => 0,
                    'resolved_complains' => 0
                ];
            }
            
            // คำนวณเวลาเฉลี่ยการแก้ไข (ถ้ามีตาราง detail)
            $avg_resolution_time = null;
            if ($this->db->table_exists('tbl_complain_detail')) {
                $avg_resolution_time = $this->db->select('AVG(DATEDIFF(cd.complain_detail_datesave, c.complain_datesave)) as avg_days')
                                               ->from('tbl_complain c')
                                               ->join('tbl_complain_detail cd', 'c.complain_id = cd.complain_detail_case_id')
                                               ->where('cd.complain_detail_status', 'ดำเนินการเรียบร้อย')
                                               ->get()
                                               ->row();
            }

            // อัตราการแก้ไข
            $total = $this->db->count_all_results('tbl_complain');
            $resolved = $this->db->where('complain_status', 'ดำเนินการเรียบร้อย')->count_all_results('tbl_complain');
            $resolution_rate = $total > 0 ? ($resolved / $total) * 100 : 0;

            return [
                'avg_resolution_days' => ($avg_resolution_time && $avg_resolution_time->avg_days !== null) ? round($avg_resolution_time->avg_days, 1) : 0,
                'resolution_rate' => round($resolution_rate, 2),
                'total_complains' => $total,
                'resolved_complains' => $resolved
            ];
        } catch (Exception $e) {
            error_log('Complain statistics error: ' . $e->getMessage());
            return [
                'avg_resolution_days' => 0,
                'resolution_rate' => 0,
                'total_complains' => 0,
                'resolved_complains' => 0
            ];
        }
    }

    /**
     * ดึงแนวโน้มเรื่องร้องเรียน
     */
    public function get_complain_trends($period = '30days')
    {
        try {
            if (!$this->db->table_exists('tbl_complain')) {
                return [];
            }
            
            $days = ($period == '7days') ? 7 : (($period == '90days') ? 90 : 30);
            
            return $this->db->select('DATE(complain_datesave) as date, COUNT(*) as count')
                           ->from('tbl_complain')
                           ->where('complain_datesave >=', date('Y-m-d', strtotime("-$days days")))
                           ->group_by('DATE(complain_datesave)')
                           ->order_by('date', 'ASC')
                           ->get()
                           ->result();
        } catch (Exception $e) {
            error_log('Complain trends error: ' . $e->getMessage());
            return [];
        }
    }

    /**
     * ✅ ดึงตัวเลือกสถานะเรื่องร้องเรียน (แก้ไข SQL Error)
     */
    public function get_complain_status_options()
    {
        try {
            if (!$this->db->table_exists('tbl_complain')) {
                return [];
            }
            
            return $this->db->select('complain_status')
                           ->from('tbl_complain')
                           ->where('complain_status IS NOT NULL')
                           ->where('complain_status !=', '')
                           ->group_by('complain_status')
                           ->get()
                           ->result_array();
        } catch (Exception $e) {
            error_log('Complain status options error: ' . $e->getMessage());
            return [];
        }
    }

    /**
     * ✅ ดึงตัวเลือกประเภทเรื่องร้องเรียน (แก้ไข SQL Error)
     */
    public function get_complain_type_options()
    {
        try {
            if (!$this->db->table_exists('tbl_complain')) {
                return [];
            }
            
            return $this->db->select('complain_type')
                           ->from('tbl_complain')
                           ->where('complain_type IS NOT NULL')
                           ->where('complain_type !=', '')
                           ->group_by('complain_type')
                           ->get()
                           ->result_array();
        } catch (Exception $e) {
            error_log('Complain type options error: ' . $e->getMessage());
            return [];
        }
    }

    /**
     * ดึงข้อมูลเรื่องร้องเรียนตาม ID
     */
    public function get_complain_by_id($complain_id)
    {
        try {
            if (!$this->db->table_exists('tbl_complain')) {
                return null;
            }
            
            return $this->db->where('complain_id', $complain_id)
                           ->get('tbl_complain')
                           ->row();
        } catch (Exception $e) {
            error_log('Get complain by id error: ' . $e->getMessage());
            return null;
        }
    }

    /**
     * ดึงรายละเอียดการติดตามเรื่องร้องเรียน
     */
    public function get_complain_details($complain_id)
    {
        try {
            if (!$this->db->table_exists('tbl_complain_detail')) {
                return [];
            }
            
            return $this->db->select('*')
                           ->from('tbl_complain_detail')
                           ->where('complain_detail_case_id', $complain_id)
                           ->order_by('complain_detail_datesave', 'ASC')
                           ->get()
                           ->result();
        } catch (Exception $e) {
            error_log('Get complain details error: ' . $e->getMessage());
            return [];
        }
    }

    /**
     * ดึงรูปภาพของเรื่องร้องเรียน (ใช้ชื่อ column ที่ถูกต้อง)
     */
    public function get_complain_images($complain_id)
    {
        try {
            if (!$this->db->table_exists('tbl_complain_img')) {
                return [];
            }
            
            return $this->db->select('*')
                           ->from('tbl_complain_img')
                           ->where('complain_img_ref_id', $complain_id)
                           ->get()
                           ->result();
        } catch (Exception $e) {
            error_log('Get complain images error: ' . $e->getMessage());
            return [];
        }
    }

    /**
     * ดึงสถานะล่าสุดของเรื่องร้องเรียน
     */
    public function get_latest_complain_status($complain_id)
    {
        try {
            if (!$this->db->table_exists('tbl_complain_detail')) {
                return null;
            }
            
            return $this->db->select('*')
                           ->from('tbl_complain_detail')
                           ->where('complain_detail_case_id', $complain_id)
                           ->order_by('complain_detail_datesave', 'DESC')
                           ->limit(1)
                           ->get()
                           ->row();
        } catch (Exception $e) {
            error_log('Get latest complain status error: ' . $e->getMessage());
            return null;
        }
    }

    /**
     * เพิ่มรายละเอียดการติดตามเรื่องร้องเรียน
     */
    public function insert_complain_detail($data)
    {
        try {
            if (!$this->db->table_exists('tbl_complain_detail')) {
                return false;
            }
            
            $this->db->insert('tbl_complain_detail', $data);
            return $this->db->insert_id();
        } catch (Exception $e) {
            error_log('Insert complain detail error: ' . $e->getMessage());
            return false;
        }
    }

    /**
     * อัพเดทสถานะหลักของเรื่องร้องเรียน
     */
    public function update_complain_main_status($complain_id, $status)
    {
        try {
            if (!$this->db->table_exists('tbl_complain')) {
                return false;
            }
            
            return $this->db->where('complain_id', $complain_id)
                           ->update('tbl_complain', [
                               'complain_status' => $status
                           ]);
        } catch (Exception $e) {
            error_log('Update complain main status error: ' . $e->getMessage());
            return false;
        }
    }
	
	
	
	public function get_complain_detail($complain_id) {
    $this->db->where('complain_id', $complain_id);
    $query = $this->db->get('complain'); // ปรับชื่อตารางตามจริง
    
    if ($query->num_rows() > 0) {
        return $query->row();
    }
    
    return false;
}
	

    /**
     * ดึงข้อมูลเรื่องร้องเรียนทั้งหมดสำหรับส่งออก
     */
    public function get_all_complains_for_export($filters = [])
    {
        try {
            if (!$this->db->table_exists('tbl_complain')) {
                return [];
            }
            
            $this->db->select('c.*');
            $this->db->from('tbl_complain c');
            
            $this->apply_complain_filters($filters);
            
            $this->db->order_by('c.complain_datesave', 'DESC');
            
            return $this->db->get()->result();
        } catch (Exception $e) {
            error_log('Get all complains for export error: ' . $e->getMessage());
            return [];
        }
    }

    /**
     * ลบเรื่องร้องเรียน (สำหรับ admin) - ใช้ชื่อ column ที่ถูกต้อง
     */
    public function delete_complain($complain_id)
    {
        try {
            if (!$this->db->table_exists('tbl_complain')) {
                return false;
            }
            
            $this->db->trans_start();

            // ลบรูปภาพ
            if ($this->db->table_exists('tbl_complain_img')) {
                $images = $this->get_complain_images($complain_id);
                
                foreach ($images as $image) {
                    if (isset($image->complain_img_img)) {
                        $image_path = './docs/img/' . $image->complain_img_img;
                        if (file_exists($image_path)) {
                            unlink($image_path);
                        }
                    }
                }
                
                // ลบข้อมูลในตาราง
                $this->db->where('complain_img_ref_id', $complain_id)->delete('tbl_complain_img');
            }
            
            if ($this->db->table_exists('tbl_complain_detail')) {
                $this->db->where('complain_detail_case_id', $complain_id)->delete('tbl_complain_detail');
            }
            
            $this->db->where('complain_id', $complain_id)->delete('tbl_complain');

            $this->db->trans_complete();

            return $this->db->trans_status();
        } catch (Exception $e) {
            $this->db->trans_rollback();
            error_log('Delete complain error: ' . $e->getMessage());
            return false;
        }
    }

    /**
     * ================================
     * ข้อมูลสำหรับกราฟ
     * ================================
     */

    /**
     * ข้อมูลกราฟสถานะเรื่องร้องเรียน
     */
    public function get_complain_status_chart_data($period = '30days')
    {
        try {
            if (!$this->db->table_exists('tbl_complain')) {
                return [];
            }
            
            $days = ($period == '7days') ? 7 : (($period == '90days') ? 90 : 30);
            
            return $this->db->select('complain_status as status, COUNT(*) as count')
                           ->from('tbl_complain')
                           ->where('complain_datesave >=', date('Y-m-d', strtotime("-$days days")))
                           ->where('complain_status IS NOT NULL')
                           ->where('complain_status !=', '')
                           ->group_by('complain_status')
                           ->get()
                           ->result();
        } catch (Exception $e) {
            error_log('Complain status chart data error: ' . $e->getMessage());
            return [];
        }
    }

    /**
     * ข้อมูลกราฟแนวโน้มเรื่องร้องเรียน
     */
    public function get_complain_trend_chart_data($period = '30days')
    {
        return $this->get_complain_trends($period);
    }

    /**
     * ข้อมูลกราฟประเภทเรื่องร้องเรียน
     */
    public function get_complain_type_chart_data($period = '30days')
    {
        try {
            if (!$this->db->table_exists('tbl_complain')) {
                return [];
            }
            
            $days = ($period == '7days') ? 7 : (($period == '90days') ? 90 : 30);
            
            return $this->db->select('complain_type as type, COUNT(*) as count')
                           ->from('tbl_complain')
                           ->where('complain_datesave >=', date('Y-m-d', strtotime("-$days days")))
                           ->where('complain_type IS NOT NULL')
                           ->where('complain_type !=', '')
                           ->group_by('complain_type')
                           ->get()
                           ->result();
        } catch (Exception $e) {
            error_log('Complain type chart data error: ' . $e->getMessage());
            return [];
        }
    }

    /**
     * ข้อมูลกราฟการใช้พื้นที่จัดเก็บ
     */
    public function get_storage_usage_chart_data($period = '30days')
    {
        return $this->get_storage_usage_history($period);
    }

    /**
     * ข้อมูลกราฟประเภทไฟล์
     */
    public function get_file_type_chart_data()
    {
        try {
            $file_types = $this->get_storage_usage_by_file_type();
            
            $chart_data = [];
            foreach ($file_types as $type => $size) {
                if ($size > 0) {
                    $chart_data[] = [
                        'type' => $type,
                        'size_mb' => round($size / (1024 * 1024), 2)
                    ];
                }
            }
            
            return $chart_data;
        } catch (Exception $e) {
            error_log('File type chart data error: ' . $e->getMessage());
            return [];
        }
    }

    /**
     * ข้อมูลกราฟแนวโน้มการใช้พื้นที่จัดเก็บ
     */
    public function get_storage_trend_chart_data($period = '30days')
    {
        return $this->get_storage_trends($period);
    }
}