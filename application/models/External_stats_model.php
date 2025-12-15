<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class External_stats_model extends CI_Model {
    
    private $external_db = null;
    private $db_config = null;
    private $tenant_db = null;
    private $tenant_code = null;
    private $table_prefix = null;
    private $current_domain = null;
    
    public function __construct() {
        parent::__construct();
        $this->initialize_external_connection();
    }
    
    /**
     * เริ่มต้นการเชื่อมต่อ database ภายนอก
     */
    private function initialize_external_connection() {
        try {
            // ดึง tenant code จาก domain ปัจจุบัน
            $this->detect_tenant_from_current_domain();
            
            // เชื่อมต่อ webanalytics database โดยตรง (ไม่ใช่ tenant database)
            $this->connect_webanalytics_db_direct();
            
            // ถ้าเชื่อมต่อได้ให้ค้นหา table prefix
            if ($this->external_db) {
                // ค้นหา table prefix สำหรับ tenant นี้
                $this->determine_table_prefix();
                
                log_message('info', 'Webanalytics database connected successfully with tenant: ' . $this->tenant_code . 
                                  ', prefix: ' . $this->table_prefix . 
                                  ', domain: ' . $this->current_domain);
                return;
            }
            
            // ถ้าเชื่อมต่อไม่ได้ ให้ log error
            log_message('error', 'Cannot connect to webanalytics database');
            
        } catch (Exception $e) {
            log_message('error', 'External DB Connection Error: ' . $e->getMessage());
            // ไม่ throw exception เพื่อให้ระบบทำงานต่อได้
        }
    }
    
    /**
     * 🆕 ตรวจสอบและดึง tenant code จาก domain ปัจจุบัน
     */
    private function detect_tenant_from_current_domain() {
        try {
            // วิธีที่ 1: ดึงจาก HTTP_HOST
            if (isset($_SERVER['HTTP_HOST'])) {
                $this->current_domain = $_SERVER['HTTP_HOST'];
            } 
            // วิธีที่ 2: ดึงจาก SERVER_NAME
            elseif (isset($_SERVER['SERVER_NAME'])) {
                $this->current_domain = $_SERVER['SERVER_NAME'];
            }
            // วิธีที่ 3: ใช้ base_url() ของ CodeIgniter
            else {
                $base_url = $this->config->item('base_url');
                if ($base_url) {
                    $parsed = parse_url($base_url);
                    $this->current_domain = $parsed['host'] ?? null;
                }
            }
            
            log_message('info', 'Current domain detected: ' . $this->current_domain);
            
            if ($this->current_domain) {
                $this->tenant_code = $this->extract_tenant_from_domain($this->current_domain);
                log_message('info', 'Tenant code extracted: ' . $this->tenant_code);
            } else {
                // fallback ใช้ webanalytics
                $this->tenant_code = 'webanalytics';
                log_message('warning', 'Cannot detect domain, using fallback tenant: ' . $this->tenant_code);
            }
            
        } catch (Exception $e) {
            log_message('error', 'Domain detection error: ' . $e->getMessage());
            $this->tenant_code = 'webanalytics'; // fallback
        }
    }
    
    /**
     * 🆕 แยก tenant code จาก domain name
     */
    private function extract_tenant_from_domain($domain) {
        // ลบ www. ออกก่อน
        $domain = preg_replace('/^www\./', '', $domain);
        
        // ตัวอย่าง patterns ต่างๆ:
        // tempc2.assystem.co.th → tempc2
        // nadee-ud.go.th → nadee-ud (เก็บ - ไว้ในขั้นตอนนี้)
        // sawang.go.th → sawang
        // subdomain.example.com → subdomain
        // analytics.company.co.th → analytics
        
        // Pattern 1: subdomain.domain.tld หรือ subdomain.domain.co.th
        if (preg_match('/^([^.]+)\./', $domain, $matches)) {
            $tenant = $matches[1];
            
            // กรองคำที่ไม่ควรเป็น tenant code
            $excluded_prefixes = ['www', 'mail', 'ftp', 'admin', 'api', 'cdn', 'static'];
            
            if (!in_array(strtolower($tenant), $excluded_prefixes)) {
                return strtolower($tenant); // เก็บ - ไว้ในขั้นตอนนี้
            }
        }
        
        // Pattern 2: domain.tld → ใช้ส่วนแรกของ domain หลัก
        $domain_parts = explode('.', $domain);
        if (count($domain_parts) >= 2) {
            return strtolower($domain_parts[0]); // เก็บ - ไว้ในขั้นตอนนี้
        }
        
        // fallback: ใช้ domain ทั้งหมดแต่แทนที่ . ด้วย _
        return strtolower(str_replace(['.'], '_', $domain));
    }
    
    /**
     * 🆕 เชื่อมต่อ webanalytics database โดยตรง
     */
    private function connect_webanalytics_db_direct() {
        try {
            // เชื่อมต่อ tenant_management เพื่อดึง webanalytics database config
            $this->connect_tenant_db();
            
            if (!$this->tenant_db) {
                throw new Exception('Cannot connect to tenant_management database');
            }
            
            // ดึง webanalytics database config จาก tenant_management
            $webanalytics_config = $this->get_webanalytics_db_config_from_tenant();
            
            if ($webanalytics_config) {
                $this->external_db = $this->load->database($webanalytics_config, TRUE);
                
                if ($this->external_db) {
                    log_message('info', 'Connected to webanalytics database successfully');
                    return;
                }
            }
            
            throw new Exception('Cannot get webanalytics database config');
            
        } catch (Exception $e) {
            log_message('error', 'Webanalytics DB connection failed: ' . $e->getMessage());
            $this->external_db = null;
        }
    }
    
    /**
     * 🆕 ดึง webanalytics database config จาก tenant_management
     */
    private function get_webanalytics_db_config_from_tenant() {
        try {
            // ค้นหา webanalytics database config โดยตรง
            $this->tenant_db->select('td.host, td.username, td.password, td.database_name, t.code as tenant_code');
            $this->tenant_db->from('tenant_databases td');
            $this->tenant_db->join('tenants t', 't.id = td.tenant_id');
            
            // ค้นหา tenant ที่เป็น webanalytics
            $this->tenant_db->where('t.code', 'webanalytics');
            $this->tenant_db->where('t.is_active', 1);
            $this->tenant_db->where('td.is_active', 1);
            $this->tenant_db->limit(1);
            
            $query = $this->tenant_db->get();
            
            log_message('debug', 'Webanalytics DB Query: ' . $this->tenant_db->last_query());
            
            if ($query->num_rows() > 0) {
                $row = $query->row();
                
                // ตรวจสอบและปรับ hostname สำหรับ external host
                $hostname = $row->host;
                
                // ถ้า host เป็น external และไม่มี port ให้เพิ่ม port 3306
                if (strpos($hostname, '.hostatom.com') !== false && strpos($hostname, ':') === false) {
                    $hostname = $hostname . ':3306';
                    log_message('info', 'Added default port to external host: ' . $hostname);
                }
                
                log_message('info', 'Found webanalytics DB config: ' . 
                                  'host: ' . $hostname . 
                                  ', database: ' . $row->database_name);
                
                return array(
                    'dsn'      => '',
                    'hostname' => $hostname,
                    'username' => $row->username,
                    'password' => $row->password,
                    'database' => $row->database_name,
                    'dbdriver' => 'mysqli',
                    'dbprefix' => '',
                    'pconnect' => FALSE,
                    'db_debug' => (ENVIRONMENT !== 'production'),
                    'cache_on' => FALSE,
                    'cachedir' => '',
                    'char_set' => 'utf8mb4',
                    'dbcollat' => 'utf8mb4_general_ci',
                    'swap_pre' => '',
                    'encrypt'  => FALSE,
                    'compress' => FALSE,
                    'stricton' => FALSE,
                    'failover' => array(),
                    'save_queries' => TRUE
                );
            }
            
            log_message('error', 'No webanalytics DB config found in tenant management');
            return null;
            
        } catch (Exception $e) {
            log_message('error', 'Get webanalytics DB config error: ' . $e->getMessage());
            return null;
        }
    }
    
    /**
     * เชื่อมต่อ tenant_management database
     */
    private function connect_tenant_db() {
        try {
            $this->tenant_db = $this->load->database('tenant_management', TRUE);
            
            if (!$this->tenant_db) {
                throw new Exception('ไม่สามารถเชื่อมต่อ tenant_management database ได้');
            }
        } catch (Exception $e) {
            log_message('error', 'Tenant DB connection failed: ' . $e->getMessage());
            throw $e;
        }
    }
    
    /**
     * ค้นหา table prefix สำหรับ tenant นี้
     */
    private function determine_table_prefix() {
        if (!$this->external_db || !$this->tenant_code) {
            log_message('error', 'Cannot determine table prefix: missing external_db or tenant_code');
            return;
        }
        
        try {
            // วิธีที่ 1: ค้นหาจาก domain_tables โดยใช้ tenant_code
            $domain_query = $this->external_db->query("
                SELECT dt.table_prefix, d.domain_name, d.id as domain_id
                FROM domain_tables dt 
                JOIN domains d ON dt.domain_id = d.id 
                WHERE (
                    d.domain_name LIKE ? OR 
                    d.domain_name LIKE ? OR
                    d.domain_name = ? OR
                    dt.table_prefix LIKE ? OR
                    dt.table_prefix LIKE ? OR
                    dt.table_prefix LIKE ?
                )
                AND dt.is_active = 1 
                LIMIT 1
            ", array(
                '%' . $this->tenant_code . '%',
                $this->tenant_code . '.%',
                $this->current_domain,
                '%' . $this->tenant_code . '%',
                'tbl_' . str_replace(['-', '.'], '_', $this->tenant_code) . '%',
                'tbl_%' . str_replace(['-', '.'], '_', $this->tenant_code) . '%'
            ));
            
            if ($domain_query && $domain_query->num_rows() > 0) {
                $result = $domain_query->row();
                $this->table_prefix = $result->table_prefix;
                log_message('info', 'Found table prefix via domain_tables: ' . $this->table_prefix . ' for domain: ' . $result->domain_name);
                return;
            }
            
            // วิธีที่ 2: ค้นหาจากตารางที่มีอยู่โดยใช้ tenant_code
            $tables = $this->external_db->list_tables();
            
            // สร้าง search patterns ที่หลากหลายจาก tenant_code ที่ detect ได้
            $search_patterns = [
                'tbl_' . strtolower(str_replace(['-', '.'], '_', $this->tenant_code)),
                'tbl_' . strtolower($this->tenant_code),
                'tbl_' . strtolower(str_replace(['-', '.', '_'], '', $this->tenant_code)),
                'tbl_' . strtolower(str_replace(['-', '.'], '_', $this->current_domain)) // เพิ่ม pattern จาก domain
            ];
            
            // ลบ pattern ที่ซ้ำกัน
            $search_patterns = array_unique($search_patterns);
            
            foreach ($search_patterns as $pattern) {
                foreach ($tables as $table) {
                    if (strpos($table, $pattern) === 0 && strpos($table, '_visitors') !== false) {
                        $this->table_prefix = str_replace('_visitors', '', $table);
                        log_message('info', 'Found table prefix via table scan: ' . $this->table_prefix . ' (pattern: ' . $pattern . ')');
                        return;
                    }
                }
            }
            
            // วิธีที่ 3: ใช้ tenant_code สร้าง prefix ตรงๆ
            $this->table_prefix = 'tbl_' . strtolower(str_replace(['-', '.'], '_', $this->tenant_code));
            log_message('info', 'Using generated table prefix: ' . $this->table_prefix);
            
            // ตรวจสอบว่าตารางที่สร้างขึ้นมีอยู่จริงหรือไม่
            if (!$this->table_exists($this->table_prefix . '_visitors')) {
                log_message('error', 'Generated table prefix does not exist: ' . $this->table_prefix);
                $this->table_prefix = null;
            }
            
        } catch (Exception $e) {
            log_message('error', 'Error determining table prefix: ' . $e->getMessage());
            $this->table_prefix = null;
        }
    }
    
    /**
     * ตรวจสอบว่าตารางมีอยู่จริงหรือไม่
     */
    private function table_exists($table_name) {
        if (!$this->external_db) {
            return false;
        }
        
        try {
            return $this->external_db->table_exists($table_name);
        } catch (Exception $e) {
            log_message('error', 'Error checking table existence: ' . $e->getMessage());
            return false;
        }
    }
    
    /**
     * ✅ แก้ไข - ดึงข้อมูลสถิติสรุป รองรับ custom date range
     */
   public function get_stats_summary($period = '7days') {
    if (!$this->external_db) {
        return $this->get_empty_stats_summary();
    }
    
    // ค้นหาตารางที่มีอยู่จริงสำหรับ tenant นี้
    $pageviews_table = $this->find_existing_pageviews_table();
    $visitors_table = $this->find_existing_visitors_table();
    
    if (!$pageviews_table) {
        log_message('error', 'No pageviews table found for tenant: ' . $this->tenant_code);
        return $this->get_empty_stats_summary();
    }
    
    // ✅ กำหนดช่วงเวลาแบบใหม่ที่รองรับ array parameter
    $date_condition = $this->build_date_condition_from_period($period);
    
    try {
        // ✅ ใช้ column 'created_at' ที่มีจริงในฐานข้อมูล
        $sql = "
            SELECT 
                COUNT(*) as total_pageviews,
                COUNT(DISTINCT p.visitor_id) as total_visitors,
                COUNT(DISTINCT p.domain_id) as total_domains,
                COUNT(DISTINCT DATE(p.created_at)) as active_days
            FROM {$pageviews_table} p
            WHERE {$date_condition}
        ";
        
        log_message('debug', 'Stats summary SQL: ' . $sql);
        log_message('debug', 'Period parameter: ' . json_encode($period));
        
        $query = $this->external_db->query($sql);
        
        if (!$query) {
            log_message('error', 'Failed to execute stats summary query');
            return $this->get_empty_stats_summary();
        }
        
        $summary = $query->row();
        
        // ✅ ดึงผู้ใช้ออนไลน์ (15 นาทีล่าสุด) - ใช้ column 'timestamp'
        $online_sql = "
            SELECT COUNT(DISTINCT visitor_id) as online_users
            FROM {$pageviews_table} 
            WHERE timestamp > DATE_SUB(NOW(), INTERVAL 15 MINUTE)
        ";
        
        $online_query = $this->external_db->query($online_sql);
        $online_data = $online_query ? $online_query->row() : (object)['online_users' => 0];
        
        $result = array(
            'total_pageviews' => (int)($summary->total_pageviews ?? 0),
            'total_visitors' => (int)($summary->total_visitors ?? 0),
            'total_domains' => (int)($summary->total_domains ?? 0),
            'active_days' => (int)($summary->active_days ?? 0),
            'online_users' => (int)($online_data->online_users ?? 0),
            'avg_pageviews_per_visitor' => ($summary->total_visitors ?? 0) > 0 ? 
                round(($summary->total_pageviews ?? 0) / ($summary->total_visitors ?? 1), 2) : 0
        );
        
        log_message('info', 'Stats summary retrieved for tenant: ' . $this->tenant_code . 
                          ', table: ' . $pageviews_table . 
                          ', pageviews: ' . $result['total_pageviews'] . 
                          ', period: ' . json_encode($period) . 
                          ', domain: ' . $this->current_domain);
        
        return $result;
        
    } catch (Exception $e) {
        log_message('error', 'Get stats summary error: ' . $e->getMessage());
        return $this->get_empty_stats_summary();
    }
}
    
    /**
     * ✅ แก้ไข - ดึงข้อมูลสถิติรายวัน รองรับ custom date range
     */
    public function get_daily_stats($period = '30days') {
    if (!$this->external_db) {
        return array();
    }
    
    $pageviews_table = $this->find_existing_pageviews_table();
    
    if (!$pageviews_table) {
        log_message('error', 'No pageviews table found for get_daily_stats');
        return array();
    }
    
    try {
        // ✅ กำหนดช่วงเวลาแบบใหม่ที่รองรับ array parameter
        $date_condition = $this->build_date_condition_from_period($period);
        
        // ✅ ใช้ column 'created_at' ที่มีจริงในฐานข้อมูล
        $sql = "
            SELECT 
                DATE(p.created_at) as date,
                COUNT(*) as pageviews,
                COUNT(DISTINCT p.visitor_id) as visitors
            FROM {$pageviews_table} p
            WHERE {$date_condition}
            GROUP BY DATE(p.created_at)
            ORDER BY date ASC
        ";
        
        log_message('debug', 'Daily stats SQL: ' . $sql);
        log_message('debug', 'Period parameter: ' . json_encode($period));
        
        $query = $this->external_db->query($sql);
        $result = $query ? $query->result() : array();
        
        log_message('info', 'Daily stats found: ' . count($result) . ' days for period: ' . json_encode($period));
        
        return $result;
        
    } catch (Exception $e) {
        log_message('error', 'Get daily stats error: ' . $e->getMessage());
        return array();
    }
}
    
    /**
     * ✅ แก้ไข - ดึงโดเมนที่มีผู้เข้าชมมากที่สุด รองรับ custom date range
     */
    public function get_top_domains($limit = 10, $period = '7days') {
    if (!$this->external_db) {
        return array();
    }
    
    $pageviews_table = $this->find_existing_pageviews_table();
    
    if (!$pageviews_table) {
        log_message('error', 'No pageviews table found for get_top_domains');
        return array();
    }
    
    // ✅ กำหนดช่วงเวลาแบบใหม่ที่รองรับ array parameter
    $date_condition = $this->build_date_condition_from_period($period);
    
    try {
        // ✅ เปลี่ยนให้ดึงข้อมูล page แทน domain
        $sql = "
            SELECT 
                COALESCE(p.page_url, '/') as page_url,
                '{$this->current_domain}' as domain_name,
                COUNT(*) as total_views,
                COUNT(DISTINCT p.visitor_id) as unique_visitors,
                COUNT(DISTINCT DATE(p.created_at)) as active_days,
                -- สร้างชื่อหน้าจาก URL
                CASE 
                    WHEN p.page_url = '/' OR p.page_url = '' OR p.page_url IS NULL THEN 'หน้าแรก'
                    WHEN p.page_url LIKE '%index%' THEN 'หน้าแรก'
                    WHEN p.page_url LIKE '%about%' THEN 'เกี่ยวกับเรา'
                    WHEN p.page_url LIKE '%contact%' THEN 'ติดต่อเรา'
                    WHEN p.page_url LIKE '%service%' THEN 'บริการ'
                    WHEN p.page_url LIKE '%product%' THEN 'สินค้า'
                    WHEN p.page_url LIKE '%news%' THEN 'ข่าวสาร'
                    WHEN p.page_url LIKE '%blog%' THEN 'บล็อก'
                    ELSE COALESCE(
                        NULLIF(
                            SUBSTRING_INDEX(SUBSTRING_INDEX(p.page_url, '/', -1), '?', 1), 
                            ''
                        ), 
                        'หน้าอื่นๆ'
                    )
                END as page_title
            FROM {$pageviews_table} p
            WHERE {$date_condition}
            GROUP BY p.page_url
            ORDER BY total_views DESC
            LIMIT {$limit}
        ";
        
        log_message('debug', 'Top pages SQL: ' . $sql);
        log_message('debug', 'Period parameter: ' . json_encode($period));
        
        $query = $this->external_db->query($sql);
        
        if (!$query) {
            log_message('error', 'Query failed in get_top_domains (modified for pages)');
            return array();
        }
        
        $results = $query->result();
        log_message('info', 'get_top_domains (pages) found ' . count($results) . ' results');
        
        return $results;
        
    } catch (Exception $e) {
        log_message('error', 'Get top domains (pages) error: ' . $e->getMessage());
        
        // Fallback: SQL แบบง่าย
        try {
            $sql_fallback = "
                SELECT 
                    COALESCE(p.page_url, '/') as page_url,
                    '{$this->current_domain}' as domain_name,
                    COUNT(*) as total_views,
                    COUNT(DISTINCT p.visitor_id) as unique_visitors,
                    1 as active_days,
                    'หน้าเว็บ' as page_title
                FROM {$pageviews_table} p
                WHERE {$date_condition}
                GROUP BY p.page_url
                ORDER BY total_views DESC
                LIMIT {$limit}
            ";
            
            $query = $this->external_db->query($sql_fallback);
            $results = $query ? $query->result() : array();
            
            log_message('info', 'get_top_domains (pages) fallback found ' . count($results) . ' results');
            return $results;
            
        } catch (Exception $e2) {
            log_message('error', 'Get top domains (pages) fallback error: ' . $e2->getMessage());
            return array();
        }
    }
}

    
    /**
     * ✅ แก้ไข - สร้าง date condition ที่รองรับ custom date range
     */
    private function get_date_condition_improved($period) {
        // ✅ แก้ไข: ตรวจสอบให้แน่ใจว่า period เป็น string ก่อนนำไปใช้ใน SQL
        if (is_array($period)) {
            // ถ้าเป็น array แสดงว่าเป็น custom date range
            if (isset($period['type']) && $period['type'] === 'custom') {
                $start_date = $this->db->escape_str($period['start']);
                $end_date = $this->db->escape_str($period['end']);
                
                // ตรวจสอบรูปแบบวันที่
                if (!$this->validate_date($start_date) || !$this->validate_date($end_date)) {
                    log_message('error', 'Invalid custom date range: ' . $start_date . ' to ' . $end_date);
                    return "p.created_at >= '" . date('Y-m-d', strtotime('-7 days')) . "'";
                }
                
                return "p.created_at >= '" . $start_date . " 00:00:00' AND p.created_at <= '" . $end_date . " 23:59:59'";
            }
            
            // ถ้าเป็น array แต่ไม่ใช่ custom ให้ fallback เป็น 7days
            log_message('warning', 'Invalid period array format, using 7days fallback');
            $period = '7days';
        }
        
        // ✅ แก้ไข: ตรวจสอบให้แน่ใจว่า period เป็น string
        if (!is_string($period)) {
            log_message('error', 'Period is not a string, converting to 7days. Period type: ' . gettype($period));
            $period = '7days';
        }
        
        // ถ้าเป็น string ปกติ
        switch ($period) {
            case 'today':
                return "DATE(p.created_at) = '" . date('Y-m-d') . "'";
            case '7days':
                return "p.created_at >= '" . date('Y-m-d', strtotime('-7 days')) . "'";
            case '30days':
                return "p.created_at >= '" . date('Y-m-d', strtotime('-30 days')) . "'";
            case '90days':
                return "p.created_at >= '" . date('Y-m-d', strtotime('-90 days')) . "'";
            case 'current_month':
                $start_of_month = date('Y-m-01');
                $end_of_month = date('Y-m-t');
                return "p.created_at >= '" . $start_of_month . " 00:00:00' AND p.created_at <= '" . $end_of_month . " 23:59:59'";
            default:
                // ถ้าไม่ตรงกับ pattern ใดๆ ให้ใช้ 7 วันล่าสุด
                log_message('warning', 'Unknown period format: ' . $period . ', using 7days fallback');
                return "p.created_at >= '" . date('Y-m-d', strtotime('-7 days')) . "'";
        }
    }
    
    /**
 * ✅ เก็บไว้ - ฟังก์ชัน validate_date เดิม
 */
private function validate_date($date) {
    if (empty($date)) {
        return false;
    }
    
    // ตรวจสอบรูปแบบ YYYY-MM-DD
    if (preg_match('/^\d{4}-\d{2}-\d{2}$/', $date)) {
        $timestamp = strtotime($date);
        return $timestamp !== false && date('Y-m-d', $timestamp) === $date;
    }
    
    return false;
}
    
    /**
     * ดึงข้อมูลสถิติตามอุปกรณ์ - เฉพาะของ tenant นี้
     */
    public function get_device_summary($period = null) {
    if (!$this->external_db) {
        return array();
    }
    
    $visitors_table = $this->find_existing_visitors_table();
    
    if (!$visitors_table) {
        log_message('error', 'No visitors table found for get_device_summary');
        return array();
    }
    
    try {
        // ✅ ถ้าไม่ได้ส่ง period มาให้ใช้ default 7 วัน
        if ($period === null) {
            $period = '7days';
        }
        
        // ✅ กำหนดช่วงเวลาแบบใหม่ที่รองรับ array parameter
        $date_condition = $this->build_date_condition_from_period($period, 'v');
        
        // ✅ ใช้ column 'device' ที่มีจริงในฐานข้อมูล
        $sql = "
            SELECT 
                device,
                COUNT(DISTINCT id) as count
            FROM {$visitors_table} v
            WHERE {$date_condition}
              AND device IS NOT NULL 
              AND device != ''
            GROUP BY device
            ORDER BY count DESC
            LIMIT 10
        ";
        
        log_message('debug', 'Device stats SQL: ' . $sql);
        log_message('debug', 'Period parameter: ' . json_encode($period));
        
        $query = $this->external_db->query($sql);
        $results = $query ? $query->result() : array();
        
        // ✅ ถ้าไม่มีข้อมูล ให้ส่ง empty array แทน mock data
        if (empty($results)) {
            log_message('info', 'No device data found for table: ' . $visitors_table);
        }
        
        return $results;
        
    } catch (Exception $e) {
        log_message('error', 'Get device summary error: ' . $e->getMessage());
        return array();
    }
}
    
    public function get_platform_summary($period = null) {
    if (!$this->external_db) {
        return array();
    }
    
    $visitors_table = $this->find_existing_visitors_table();
    
    if (!$visitors_table) {
        log_message('error', 'No visitors table found for get_platform_summary');
        return array();
    }
    
    try {
        // ✅ ถ้าไม่ได้ส่ง period มาให้ใช้ default 7 วัน
        if ($period === null) {
            $period = '7days';
        }
        
        // ✅ กำหนดช่วงเวลาแบบใหม่ที่รองรับ array parameter
        $date_condition = $this->build_date_condition_from_period($period, 'v');
        
        // ✅ ใช้ column 'platform' ที่มีจริงในฐานข้อมูล
        $sql = "
            SELECT 
                platform,
                COUNT(DISTINCT id) as count
            FROM {$visitors_table} v
            WHERE {$date_condition}
              AND platform IS NOT NULL 
              AND platform != ''
            GROUP BY platform
            ORDER BY count DESC
            LIMIT 10
        ";
        
        log_message('debug', 'Platform stats SQL: ' . $sql);
        log_message('debug', 'Period parameter: ' . json_encode($period));
        
        $query = $this->external_db->query($sql);
        return $query ? $query->result() : array();
        
    } catch (Exception $e) {
        log_message('error', 'Get platform summary error: ' . $e->getMessage());
        return array();
    }
}
	
	
	
    /**
     * ดึงข้อมูลสถิติรายชั่วโมง - เฉพาะของ tenant นี้
     */
    public function get_hourly_visits($period = null) {
    if (!$this->external_db) {
        return $this->get_empty_hourly_data();
    }
    
    $pageviews_table = $this->find_existing_pageviews_table();
    
    if (!$pageviews_table) {
        log_message('error', 'No pageviews table found for get_hourly_visits');
        return $this->get_empty_hourly_data();
    }
    
    try {
        // ✅ ถ้าไม่ได้ส่ง period มาให้ใช้ default 7 วัน
        if ($period === null) {
            $period = '7days';
        }
        
        // ✅ กำหนดช่วงเวลาแบบใหม่ที่รองรับ array parameter
        $date_condition = $this->build_date_condition_from_period($period, 'p', 'timestamp');
        
        // ✅ ใช้ column 'timestamp' ที่มีจริงในฐานข้อมูล
        $sql = "
            SELECT 
                HOUR(timestamp) as hour,
                COUNT(*) as count
            FROM {$pageviews_table} p
            WHERE {$date_condition}
            GROUP BY HOUR(timestamp)
            ORDER BY hour ASC
        ";
        
        log_message('debug', 'Hourly visits SQL: ' . $sql);
        log_message('debug', 'Period parameter: ' . json_encode($period));
        
        $query = $this->external_db->query($sql);
        
        if (!$query) {
            return $this->get_empty_hourly_data();
        }
        
        $results = $query->result();
        
        // สร้างอาร์เรย์สำหรับทุกชั่วโมง (0-23)
        $hours = array_fill(0, 24, 0);
        
        foreach ($results as $row) {
            $hours[(int)$row->hour] = (int)$row->count;
        }
        
        $formatted_results = [];
        for ($i = 0; $i < 24; $i++) {
            $formatted_results[] = (object)array(
                'hour' => $i,
                'count' => $hours[$i]
            );
        }
        
        return $formatted_results;
        
    } catch (Exception $e) {
        log_message('error', 'Get hourly visits error: ' . $e->getMessage());
        return $this->get_empty_hourly_data();
    }
}
    
    /**
     * ดึงข้อมูลสถิติเบราว์เซอร์ - เฉพาะของ tenant นี้
     */
    public function get_browser_stats($period = null) {
    if (!$this->external_db) {
        return array();
    }
    
    $visitors_table = $this->find_existing_visitors_table();
    
    if (!$visitors_table) {
        log_message('error', 'No visitors table found for get_browser_stats');
        return array();
    }
    
    try {
        // ✅ ถ้าไม่ได้ส่ง period มาให้ใช้ default 7 วัน
        if ($period === null) {
            $period = '7days';
        }
        
        // ✅ กำหนดช่วงเวลาแบบใหม่ที่รองรับ array parameter
        // แต่ต้องแก้ไข WHERE clause เพื่อใช้กับ visitors table
        $date_condition = $this->build_date_condition_from_period($period, 'v');
        
        // ✅ ใช้ column 'browser' ที่มีจริงในฐานข้อมูล
        $sql = "
            SELECT 
                browser,
                COUNT(DISTINCT id) as count
            FROM {$visitors_table} v
            WHERE {$date_condition}
              AND browser IS NOT NULL 
              AND browser != ''
            GROUP BY browser
            ORDER BY count DESC
            LIMIT 10
        ";
        
        log_message('debug', 'Browser stats SQL: ' . $sql);
        log_message('debug', 'Period parameter: ' . json_encode($period));
        
        $query = $this->external_db->query($sql);
        return $query ? $query->result() : array();
        
    } catch (Exception $e) {
        log_message('error', 'Get browser stats error: ' . $e->getMessage());
        return array();
    }
}
    
    /**
     * ดึงข้อมูลสถิติตามประเทศ - เฉพาะของ tenant นี้
     */
    public function get_country_stats($period = null) {
    if (!$this->external_db) {
        return array();
    }
    
    $visitors_table = $this->find_existing_visitors_table();
    
    if (!$visitors_table) {
        log_message('error', 'No visitors table found for get_country_stats');
        return array();
    }
    
    try {
        // ✅ ถ้าไม่ได้ส่ง period มาให้ใช้ default 7 วัน
        if ($period === null) {
            $period = '7days';
        }
        
        // ✅ กำหนดช่วงเวลาแบบใหม่ที่รองรับ array parameter
        $date_condition = $this->build_date_condition_from_period($period, 'v');
        
        // ✅ ใช้ column 'country' ที่มีจริงในฐานข้อมูล
        $sql = "
            SELECT 
                country,
                COUNT(DISTINCT id) as count
            FROM {$visitors_table} v
            WHERE {$date_condition}
              AND country IS NOT NULL
              AND country != ''
            GROUP BY country
            ORDER BY count DESC
            LIMIT 10
        ";
        
        log_message('debug', 'Country stats SQL: ' . $sql);
        log_message('debug', 'Period parameter: ' . json_encode($period));
        
        $query = $this->external_db->query($sql);
        return $query ? $query->result() : array();
        
    } catch (Exception $e) {
        log_message('error', 'Get country stats error: ' . $e->getMessage());
        return array();
    }
}
    
    /**
     * 🆕 ค้นหาตาราง pageviews ที่มีอยู่จริงสำหรับ tenant นี้
     */
    private function find_existing_pageviews_table() {
        if (!$this->external_db || !$this->tenant_code) {
            return null;
        }
        
        try {
            // วิธีที่ 1: ใช้ table prefix ที่หาได้แล้ว
            if ($this->table_prefix) {
                $table_name = $this->table_prefix . '_pageviews';
                if ($this->table_exists($table_name)) {
                    log_message('info', 'Found pageviews table with prefix: ' . $table_name);
                    return $table_name;
                }
            }
            
            // วิธีที่ 2: ค้นหาจากรายชื่อตารางทั้งหมด
            $tables = $this->external_db->list_tables();
            
            // สร้าง search patterns จาก tenant_code ที่ detect ได้
            $search_patterns = [
                'tbl_' . strtolower(str_replace(['-', '.'], '_', $this->tenant_code)) . '_pageviews',
                'tbl_' . strtolower($this->tenant_code) . '_pageviews',
                'tbl_' . strtolower(str_replace(['-', '.', '_'], '', $this->tenant_code)) . '_pageviews'
            ];
            
            foreach ($search_patterns as $pattern) {
                if (in_array($pattern, $tables)) {
                    log_message('info', 'Found pageviews table by pattern: ' . $pattern);
                    return $pattern;
                }
            }
            
            // วิธีที่ 3: ค้นหาโดยดูว่าตารางไหนมี pattern ที่คล้ายกัน
            foreach ($tables as $table) {
                if (strpos($table, 'pageviews') !== false && 
                    strpos($table, strtolower($this->tenant_code)) !== false) {
                    log_message('info', 'Found pageviews table by search: ' . $table);
                    return $table;
                }
            }
            
            log_message('error', 'No pageviews table found for tenant: ' . $this->tenant_code . ', domain: ' . $this->current_domain);
            return null;
            
        } catch (Exception $e) {
            log_message('error', 'Error finding pageviews table: ' . $e->getMessage());
            return null;
        }
    }
    
    /**
     * 🆕 ค้นหาตาราง visitors ที่มีอยู่จริงสำหรับ tenant นี้
     */
    private function find_existing_visitors_table() {
        if (!$this->external_db || !$this->tenant_code) {
            return null;
        }
        
        try {
            // วิธีที่ 1: ใช้ table prefix ที่หาได้แล้ว
            if ($this->table_prefix) {
                $table_name = $this->table_prefix . '_visitors';
                if ($this->table_exists($table_name)) {
                    log_message('info', 'Found visitors table with prefix: ' . $table_name);
                    return $table_name;
                }
            }
            
            // วิธีที่ 2: ค้นหาจากรายชื่อตารางทั้งหมด
            $tables = $this->external_db->list_tables();
            
            // สร้าง search patterns จาก tenant_code ที่ detect ได้
            $search_patterns = [
                'tbl_' . strtolower(str_replace(['-', '.'], '_', $this->tenant_code)) . '_visitors',
                'tbl_' . strtolower($this->tenant_code) . '_visitors', 
                'tbl_' . strtolower(str_replace(['-', '.', '_'], '', $this->tenant_code)) . '_visitors'
            ];
            
            foreach ($search_patterns as $pattern) {
                if (in_array($pattern, $tables)) {
                    log_message('info', 'Found visitors table by pattern: ' . $pattern);
                    return $pattern;
                }
            }
            
            // วิธีที่ 3: ค้นหาโดยดูว่าตารางไหนมี pattern ที่คล้ายกัน
            foreach ($tables as $table) {
                if (strpos($table, 'visitors') !== false && 
                    strpos($table, strtolower($this->tenant_code)) !== false) {
                    log_message('info', 'Found visitors table by search: ' . $table);
                    return $table;
                }
            }
            
            log_message('error', 'No visitors table found for tenant: ' . $this->tenant_code . ', domain: ' . $this->current_domain);
            return null;
            
        } catch (Exception $e) {
            log_message('error', 'Error finding visitors table: ' . $e->getMessage());
            return null;
        }
    }
    
    private function get_days_from_period($period) {
        switch ($period) {
            case '7days': return 7;
            case '30days': return 30;
            case '90days': return 90;
            default: return 30;
        }
    }
    
    private function get_empty_stats_summary() {
        return array(
            'total_pageviews' => 0,
            'total_visitors' => 0,
            'total_domains' => 0,
            'active_days' => 0,
            'online_users' => 0,
            'avg_pageviews_per_visitor' => 0
        );
    }
    
    private function get_empty_hourly_data() {
        $formatted_results = [];
        for ($i = 0; $i < 24; $i++) {
            $formatted_results[] = (object)array(
                'hour' => $i,
                'count' => 0
            );
        }
        return $formatted_results;
    }
    
    /**
     * 🆕 ฟังก์ชัน Debug สำหรับตรวจสอบปัญหา
     */
    public function debug_info() {
        $debug = array(
            'current_domain' => $this->current_domain,
            'tenant_code' => $this->tenant_code,
            'table_prefix' => $this->table_prefix,
            'external_db_connected' => ($this->external_db ? 'Yes' : 'No'),
            'pageviews_table' => null,
            'visitors_table' => null,
            'table_exists' => array(),
            'sample_data' => array(),
            'domain_detection' => array()
        );
        
        // ข้อมูลการ detect domain
        $debug['domain_detection'] = array(
            'http_host' => $_SERVER['HTTP_HOST'] ?? 'Not set',
            'server_name' => $_SERVER['SERVER_NAME'] ?? 'Not set',
            'base_url' => $this->config->item('base_url'),
            'extracted_tenant' => $this->tenant_code
        );
        
        if ($this->external_db) {
            // ตรวจสอบตารางที่หาได้
            $debug['pageviews_table'] = $this->find_existing_pageviews_table();
            $debug['visitors_table'] = $this->find_existing_visitors_table();
            
            // ตรวจสอบว่าตารางมีอยู่จริงไหม
            if ($debug['pageviews_table']) {
                $debug['table_exists']['pageviews'] = $this->table_exists($debug['pageviews_table']);
                
                // ลองดึงข้อมูลตัวอย่าง
                try {
                    $sample_query = $this->external_db->query("SELECT COUNT(*) as total FROM " . $debug['pageviews_table']);
                    if ($sample_query) {
                        $debug['sample_data']['pageviews_count'] = $sample_query->row()->total;
                    }
                } catch (Exception $e) {
                    $debug['sample_data']['pageviews_error'] = $e->getMessage();
                }
            }
            
            if ($debug['visitors_table']) {
                $debug['table_exists']['visitors'] = $this->table_exists($debug['visitors_table']);
                
                // ลองดึงข้อมูลตัวอย่าง
                try {
                    $sample_query = $this->external_db->query("SELECT COUNT(*) as total FROM " . $debug['visitors_table']);
                    if ($sample_query) {
                        $debug['sample_data']['visitors_count'] = $sample_query->row()->total;
                    }
                } catch (Exception $e) {
                    $debug['sample_data']['visitors_error'] = $e->getMessage();
                }
            }
            
            // แสดงตารางทั้งหมดที่มี
            try {
                $all_tables = $this->external_db->list_tables();
                $debug['all_tables'] = $all_tables;
                
                // กรองเฉพาะตารางที่เกี่ยวข้องกับ tenant นี้
                $debug['related_tables'] = array();
                foreach ($all_tables as $table) {
                    if (strpos($table, $this->tenant_code) !== false || 
                        strpos($table, 'pageviews') !== false || 
                        strpos($table, 'visitors') !== false) {
                        $debug['related_tables'][] = $table;
                    }
                }
                
            } catch (Exception $e) {
                $debug['tables_error'] = $e->getMessage();
            }
        }
        
        return $debug;
    }
    
    public function debug_table_structure() {
        if (!$this->external_db) {
            return ['error' => 'No external database connection'];
        }
        
        $debug_info = [];
        
        // Debug visitors table
        $visitors_table = $this->find_existing_visitors_table();
        if ($visitors_table) {
            try {
                // Columns
                $columns_query = $this->external_db->query("SHOW COLUMNS FROM {$visitors_table}");
                $debug_info['visitors_table'] = [
                    'table_name' => $visitors_table,
                    'columns' => $columns_query ? $columns_query->result() : [],
                    'sample_data' => []
                ];
                
                // Sample data - เลือกเฉพาะ columns ที่สำคัญ
                $sample_query = $this->external_db->query("
                    SELECT id, domain_id, device, platform, browser, country, created_at 
                    FROM {$visitors_table} 
                    ORDER BY created_at DESC 
                    LIMIT 3
                ");
                if ($sample_query) {
                    $debug_info['visitors_table']['sample_data'] = $sample_query->result();
                }
                
                // Row count
                $count_query = $this->external_db->query("SELECT COUNT(*) as total FROM {$visitors_table}");
                if ($count_query) {
                    $debug_info['visitors_table']['total_rows'] = $count_query->row()->total;
                }
                
                // Device summary test
                $device_test = $this->external_db->query("
                    SELECT device, COUNT(*) as count 
                    FROM {$visitors_table} 
                    WHERE device IS NOT NULL AND device != ''
                    GROUP BY device 
                    ORDER BY count DESC 
                    LIMIT 5
                ");
                if ($device_test) {
                    $debug_info['visitors_table']['device_data'] = $device_test->result();
                }
                
            } catch (Exception $e) {
                $debug_info['visitors_table']['error'] = $e->getMessage();
            }
        }
        
        // Debug pageviews table  
        $pageviews_table = $this->find_existing_pageviews_table();
        if ($pageviews_table) {
            try {
                $columns_query = $this->external_db->query("SHOW COLUMNS FROM {$pageviews_table}");
                $debug_info['pageviews_table'] = [
                    'table_name' => $pageviews_table,
                    'columns' => $columns_query ? $columns_query->result() : [],
                    'sample_data' => []
                ];
                
                // Sample data - เลือกเฉพาะ columns ที่สำคัญ
                $sample_query = $this->external_db->query("
                    SELECT id, visitor_id, domain_id, page_url, timestamp, created_at 
                    FROM {$pageviews_table} 
                    ORDER BY created_at DESC 
                    LIMIT 3
                ");
                if ($sample_query) {
                    $debug_info['pageviews_table']['sample_data'] = $sample_query->result();
                }
                
                $count_query = $this->external_db->query("SELECT COUNT(*) as total FROM {$pageviews_table}");
                if ($count_query) {
                    $debug_info['pageviews_table']['total_rows'] = $count_query->row()->total;
                }
                
            } catch (Exception $e) {
                $debug_info['pageviews_table']['error'] = $e->getMessage();
            }
        }
        
        return $debug_info;
    }
    
    /**
     * ดึงข้อมูล tenant code ปัจจุบัน
     */
    public function get_current_tenant_code() {
        return $this->tenant_code;
    }
    
    /**
     * ดึงข้อมูล table prefix ปัจจุบัน
     */
    public function get_current_table_prefix() {
        return $this->table_prefix;
    }
    
    /**
     * ดึงข้อมูล domain ปัจจุบัน
     */
    public function get_current_domain() {
        return $this->current_domain;
    }
    
    /**
     * 🆕 ดึงรายการ domain ที่เกี่ยวข้องกับ tenant นี้
     */
    public function get_tenant_domains() {
        if (!$this->external_db || !$this->tenant_code) {
            return array();
        }
        
        try {
            // ค้นหาจากตาราง domains ใน webanalytics database
            $query = $this->external_db->query("
                SELECT d.id, d.domain_name, d.is_active
                FROM domains d 
                WHERE (
                    d.domain_name LIKE ? OR 
                    d.domain_name LIKE ? OR
                    d.domain_name = ? OR
                    d.domain_name = ?
                )
                ORDER BY d.domain_name
            ", array(
                '%' . $this->tenant_code . '%',
                $this->tenant_code . '.%',
                $this->tenant_code,
                $this->current_domain
            ));
            
            if ($query && $query->num_rows() > 0) {
                return $query->result();
            }
            
            // Fallback: ถ้าไม่เจอให้ return ข้อมูล current domain
            log_message('debug', 'No domains found in webanalytics, returning current domain info');
            return array(
                (object) array(
                    'id' => 0,
                    'domain_name' => $this->current_domain,
                    'is_active' => 1
                )
            );
            
        } catch (Exception $e) {
            log_message('error', 'Get tenant domains error: ' . $e->getMessage());
            return array();
        }
    }
    
    /**
     * ปิดการเชื่อมต่อ database ภายนอก
     */
    public function __destruct() {
        if ($this->external_db) {
            $this->external_db->close();
        }
        if ($this->tenant_db) {
            $this->tenant_db->close();
        }
    }
	
	
	/**
 * ✅ ใหม่ - สร้าง date condition ที่รองรับ period parameter แบบใหม่จาก Controller
 */
private function build_date_condition_from_period($period, $table_alias = 'p', $date_column = 'created_at') {
    // ตรวจสอบว่า period เป็น array (จาก Controller ใหม่)
    if (is_array($period)) {
        // ถ้าเป็น custom period
        if (isset($period['type']) && $period['type'] === 'custom') {
            if (isset($period['start_date']) && isset($period['end_date'])) {
                $start_date = $this->external_db->escape_str($period['start_date']);
                $end_date = $this->external_db->escape_str($period['end_date']);
                
                if (!$this->validate_date($start_date) || !$this->validate_date($end_date)) {
                    log_message('error', 'Invalid custom date range: ' . $start_date . ' to ' . $end_date);
                    return "{$table_alias}.{$date_column} >= '" . date('Y-m-d', strtotime('-6 days')) . "'";
                }
                
                return "{$table_alias}.{$date_column} >= '" . $start_date . " 00:00:00' AND {$table_alias}.{$date_column} <= '" . $end_date . " 23:59:59'";
            }
        }
        
        // ถ้าเป็น predefined period ใน array format
        if (isset($period['period'])) {
            return $this->build_predefined_date_condition($period['period'], $table_alias, $date_column);
        }
        
        // ✅ แก้ไข: เปลี่ยนจาก 'warning' เป็น 'error'
        log_message('error', 'Unknown period array format: ' . json_encode($period) . ', using 7days fallback');
        return "{$table_alias}.{$date_column} >= '" . date('Y-m-d', strtotime('-6 days')) . "'";
    }
    
    // ถ้าเป็น string (period ปกติ)
    if (is_string($period)) {
        return $this->build_predefined_date_condition($period, $table_alias, $date_column);
    }
    
    // ✅ แก้ไข: เปลี่ยนจาก 'warning' เป็น 'error'
    log_message('error', 'Unknown period format: ' . json_encode($period) . ', type: ' . gettype($period) . ', using 7days fallback');
    return "{$table_alias}.{$date_column} >= '" . date('Y-m-d', strtotime('-6 days')) . "'";
}

/**
 * ✅ ใหม่ - สร้าง date condition สำหรับ predefined periods
 */
/**
 * ✅ แก้ไข - สร้าง date condition สำหรับ predefined periods
 */
private function build_predefined_date_condition($period, $table_alias = 'p', $date_column = 'created_at') {
    switch ($period) {
        case 'today':
            return "DATE({$table_alias}.{$date_column}) = '" . date('Y-m-d') . "'";
            
        case '7days':
            return "{$table_alias}.{$date_column} >= '" . date('Y-m-d', strtotime('-6 days')) . "'";
            
        case '30days':
            return "{$table_alias}.{$date_column} >= '" . date('Y-m-d', strtotime('-29 days')) . "'";
            
        case '90days':
            return "{$table_alias}.{$date_column} >= '" . date('Y-m-d', strtotime('-89 days')) . "'";
            
        case 'current_month':
            $start_of_month = date('Y-m-01');
            $end_of_month = date('Y-m-t');
            return "{$table_alias}.{$date_column} >= '" . $start_of_month . " 00:00:00' AND {$table_alias}.{$date_column} <= '" . $end_of_month . " 23:59:59'";
            
        default:
            // ✅ แก้ไข: เปลี่ยนจาก 'warning' เป็น 'error'
            log_message('error', 'Unknown predefined period: ' . $period . ', using 7days fallback');
            return "{$table_alias}.{$date_column} >= '" . date('Y-m-d', strtotime('-6 days')) . "'";
    }
}
	
	
}