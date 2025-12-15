<?php
defined('BASEPATH') or exit('No direct script access allowed');

class Dashboard_model extends CI_Model
{
    public function __construct()
    {
        parent::__construct();
    }
 
	public function find_table_most_posts()
{
    $query = $this->db->query("
        SELECT 'ข่าวสาร / กิจกรรม' AS table_name, 
               COUNT(*) AS total_posts,
               'tbl_activity' AS table_id,
               'activity' AS category_id
        FROM tbl_activity
        UNION ALL
        SELECT 'ข่าวประชาสัมพันธ์' AS table_name, 
               COUNT(*) AS total_posts,
               'tbl_news' AS table_id,
               'news' AS category_id
        FROM tbl_news
        UNION ALL
        SELECT 'คำสั่ง' AS table_name, 
               COUNT(*) AS total_posts,
               'tbl_order' AS table_id,
               'order' AS category_id
        FROM tbl_order
        UNION ALL
        SELECT 'ประกาศ' AS table_name, 
               COUNT(*) AS total_posts,
               'tbl_announce' AS table_id,
               'announce' AS category_id
        FROM tbl_announce
        UNION ALL
        SELECT 'ประกาศจัดซื้อจัดจ้าง' AS table_name, 
               COUNT(*) AS total_posts,
               'tbl_procurement' AS table_id,
               'procurement' AS category_id
        FROM tbl_procurement
        UNION ALL
        SELECT 'รายงานผลการดำเนินงานจัดซื้อจัดจ้าง' AS table_name, 
               COUNT(*) AS total_posts,
               'tbl_p_rpo' AS table_id,
               'p_rpo' AS category_id
        FROM tbl_p_rpo
        UNION ALL
        SELECT 'รายงานใช้จ่ายงบประมาณ' AS table_name, 
               COUNT(*) AS total_posts,
               'tbl_p_reb' AS table_id,
               'p_reb' AS category_id
        FROM tbl_p_reb
		UNION ALL
        SELECT 'รายงานผลการดำเนินงาน' AS table_name, 
               COUNT(*) AS total_posts,
               'tbl_operation_report' AS table_id,
               'operation_report' AS category_id
        FROM tbl_operation_report
        ORDER BY total_posts DESC
        LIMIT 9
    ");
    
    $result = $query->result();
    return $result;
}
	
	 /**
     * Helper method: หาคอลัมน์แรกที่มีอยู่จริงในตาราง
     */
    private function first_existing_col($table, array $cands)
    {
        try {
            foreach ($cands as $c) {
                $q = $this->db->query("SHOW COLUMNS FROM `{$table}` LIKE ?", [$c]);
                if ($q && $q->num_rows() > 0) return $c;
            }
            return null;
        } catch (Exception $e) {
            log_message('error', "Error checking columns for table {$table}: " . $e->getMessage());
            return null;
        }
    }

    /**
     * ดึงข้อมูลยอดนิยมแบบแยกหมวด
     */
    public function get_popular_by_category($limit_per_category = 3)
    {
        try {
            // กำหนดหมวดหมู่และตาราง
            $categories = [
                'activity' => [
                    'title' => 'ข่าวสาร / กิจกรรม',
                    'icon' => 'fas fa-calendar-alt',
                    'color' => '#4F46E5', // Indigo
                    'tables' => ['tbl_activity']
                ],
                'news' => [
                    'title' => 'ข่าวประชาสัมพันธ์', 
                    'icon' => 'fas fa-bullhorn',
                    'color' => '#059669', // Emerald
                    'tables' => ['tbl_news']
                ],
                'travel' => [
                    'title' => 'สถานที่ท่องเที่ยว',
                    'icon' => 'fas fa-map-marker-alt',
                    'color' => '#DC2626', // Red
                    'tables' => ['tbl_travel']
                ]
            ];

            $result = [];

            foreach ($categories as $cat_key => $category) {
                $all_items = [];

                // รวบรวมข้อมูลจากทุกตารางในหมวดนี้
                foreach ($category['tables'] as $table) {
                    if (!$this->db->table_exists($table)) {
                        log_message('debug', "Table {$table} does not exist, skipping...");
                        continue;
                    }

                    $items = $this->get_table_popular_items($table, $limit_per_category * 2);
                    if (!empty($items)) {
                        $all_items = array_merge($all_items, $items);
                    }
                }

                // เรียงลำดับและเอาเท่าที่ต้องการ
                if (!empty($all_items)) {
                    usort($all_items, function($a, $b) {
                        $viewsA = (int)($a['views'] ?? 0);
                        $viewsB = (int)($b['views'] ?? 0);
                        
                        if ($viewsA == $viewsB) {
                            $dateA = strtotime($a['datesave'] ?? '1970-01-01');
                            $dateB = strtotime($b['datesave'] ?? '1970-01-01');
                            return $dateB - $dateA;
                        }
                        return $viewsB - $viewsA;
                    });

                    $category_data = array_slice($all_items, 0, $limit_per_category);
                } else {
                    $category_data = [];
                }

                $result[$cat_key] = [
                    'title' => $category['title'],
                    'icon' => $category['icon'],
                    'color' => $category['color'],
                    'items' => $category_data,
                    'total_items' => count($category_data)
                ];
            }

            return $result;

        } catch (Exception $e) {
            log_message('error', 'Error in get_popular_by_category: ' . $e->getMessage());
            return [];
        }
    }

    /**
     * ดึงข้อมูลยอดนิยมจากตารางเดียว
     */
    private function get_table_popular_items($table, $limit = 5)
    {
        try {
            // ตรวจสอบว่าตารางมีอยู่จริง
            if (!$this->db->table_exists($table)) {
                return [];
            }

            $p = preg_replace('/^tbl_/', '', $table);
            
            $id    = $this->first_existing_col($table, ["{$p}_id", 'id']);
            $name  = $this->first_existing_col($table, ["{$p}_name", "{$p}_title", 'name', 'title', 'topic']);
            $views = $this->first_existing_col($table, ["{$p}_view", "{$p}_views", 'views', 'view', 'count_view', 'hit']);
            $img   = $this->first_existing_col($table, ["{$p}_img", "{$p}_image", 'img', 'image', 'thumbnail', 'thumb', 'photo', 'picture']);
            $date  = $this->first_existing_col($table, ["{$p}_datesave", "{$p}_created", 'datesave', 'created_at', 'date_created', 'created', 'publish_date', 'date', 'updated_at']);
            $stat  = $this->first_existing_col($table, ["{$p}_status", 'status', 'active', 'is_active', 'is_show', 'visible', 'enable']);

            // ต้องมีอย่างน้อย id + name
            if (!$id || !$name) {
                log_message('debug', "Table {$table} missing required columns: id={$id}, name={$name}");
                return [];
            }

            // สร้าง SELECT clause
            $select_fields = [
                "'{$p}' AS src",
                "`{$id}` AS id", 
                "`{$name}` AS name"
            ];

            // เพิ่ม views
            if ($views) {
                $select_fields[] = "COALESCE(`{$views}`, 0) AS views";
            } else {
                $select_fields[] = "0 AS views";
            }

            // เพิ่ม img
            if ($img) {
                $select_fields[] = "`{$img}` AS img";
            } else {
                $select_fields[] = "NULL AS img";
            }

            // เพิ่ม date
            if ($date) {
                $select_fields[] = "`{$date}` AS datesave";
            } else {
                $select_fields[] = "NOW() AS datesave";
            }

            $select_fields[] = "'{$table}' AS table_name";

            $this->db->select(implode(', ', $select_fields));
            $this->db->from($table);

            // เงื่อนไข WHERE
            if ($stat) {
                $this->db->where_in($stat, ['show', '1', 'Y', 'y', 'yes', 'YES', 'true', 1, 'active', 'published']);
            }

            // เรียงลำดับ
            if ($views) {
                $this->db->order_by($views, 'DESC');
            }
            if ($date) {
                $this->db->order_by($date, 'DESC');
            }

            $this->db->limit((int)$limit);
            $query = $this->db->get();

            if (!$query) {
                log_message('error', "Query failed for table {$table}: " . $this->db->error()['message']);
                return [];
            }

            $result = $query->result_array();
            
            // Debug log
            log_message('debug', "Table {$table} returned " . count($result) . " items");
            
            return $result;

        } catch (Exception $e) {
            log_message('error', "Error getting items from table {$table}: " . $e->getMessage());
            return [];
        }
    }

    /**
     * ดึงข้อมูลยอดนิยมแบบเดิม (รวมทุกอย่าง)
     */
    public function get_popular($limit = 9)
    {
        try {
            $tables = ['tbl_activity', 'tbl_news', 'tbl_travel'];
            $parts = [];
            
            foreach ($tables as $table) {
                if (!$this->db->table_exists($table)) {
                    continue;
                }

                $p = preg_replace('/^tbl_/', '', $table);
                $id    = $this->first_existing_col($table, ["{$p}_id", 'id']);
                $name  = $this->first_existing_col($table, ["{$p}_name", "{$p}_title", 'name', 'title', 'topic']);
                $views = $this->first_existing_col($table, ["{$p}_view", "{$p}_views", 'views', 'view', 'count_view', 'hit']);
                $img   = $this->first_existing_col($table, ["{$p}_img", "{$p}_image", 'img', 'image', 'thumbnail', 'thumb', 'photo', 'picture']);
                $date  = $this->first_existing_col($table, ["{$p}_datesave", "{$p}_created", 'datesave', 'created_at', 'date_created', 'created', 'publish_date', 'date', 'updated_at']);
                $stat  = $this->first_existing_col($table, ["{$p}_status", 'status', 'active', 'is_active', 'is_show', 'visible', 'enable']);
                
                if (!$id || !$name) continue;
                
                $viewsExpr = $views ? "COALESCE(`{$views}`, 0)" : "0";
                $imgExpr   = $img   ? "`{$img}`" : "NULL";
                $dateExpr  = $date  ? "`{$date}`" : "NOW()";
                
                $where = [];
                if ($stat) {
                    $where[] = "`{$stat}` IN ('show','1','Y','y','yes','YES','true',1,'active','published')";
                }
                
                $whereSql = $where ? 'WHERE ' . implode(' AND ', $where) : '';
                $src = str_replace('tbl_', '', $table);
                
                $parts[] = "
                SELECT
                  '{$src}' AS src,
                  `{$id}` AS id,
                  `{$name}` AS name,
                  {$viewsExpr} AS views,
                  {$imgExpr} AS img,
                  {$dateExpr} AS datesave
                FROM `{$table}`
                {$whereSql}
                ";
            }
            
            if (empty($parts)) {
                return [];
            }
            
            $sql = "SELECT * FROM (" . implode(" UNION ALL ", $parts) . ") u
                ORDER BY views DESC, datesave DESC
                LIMIT ?";
            
            $query = $this->db->query($sql, [(int)$limit]);
            
            return $query ? $query->result_array() : [];

        } catch (Exception $e) {
            log_message('error', 'Error in get_popular: ' . $e->getMessage());
            return [];
        }
    }
}
