<?php
defined('BASEPATH') or exit('No direct script access allowed');

class Data_catalog_model extends CI_Model
{

    private $table_catalog = 'tbl_data_catalog';
    private $table_category = 'tbl_data_catalog_category';
    private $table_metadata = 'tbl_data_catalog_metadata';
    private $table_views_log = 'tbl_data_catalog_views_log';
    private $table_downloads_log = 'tbl_data_catalog_downloads_log';

    public function __construct()
    {
        parent::__construct();
        $this->load->database();

        // เช็คและสร้างตารางอัตโนมัติ
        $this->check_and_create_tables();
    }

    /**
     * เช็คและสร้างตารางถ้ายังไม่มี
     */
    private function check_and_create_tables()
    {
        try {
            // เช็คตาราง Category
            if (!$this->db->table_exists($this->table_category)) {
                $this->create_category_table();
                $this->insert_default_categories();
            }

            // เช็คตาราง Catalog
            if (!$this->db->table_exists($this->table_catalog)) {
                $this->create_catalog_table();
                $this->insert_sample_datasets();
            } else {
                $this->add_file_size_column_if_not_exists();
            }

            // เช็คตาราง Metadata
            if (!$this->db->table_exists($this->table_metadata)) {
                $this->create_metadata_table();
                $this->insert_sample_metadata();
            } else {
                $this->add_metadata_columns_if_not_exists();
            }

            // เช็คตาราง Views Log
            $this->check_and_create_views_log_table();

            // เช็คตาราง Downloads Log
            $this->check_and_create_downloads_log_table();

        } catch (Exception $e) {
            log_message('error', 'Error creating tables: ' . $e->getMessage());
        }
    }

    /**
     * เช็คและสร้างตาราง views log
     */
    private function check_and_create_views_log_table()
    {
        if (!$this->db->table_exists($this->table_views_log)) {
            $sql = "CREATE TABLE `{$this->table_views_log}` (
                `id` int(11) NOT NULL AUTO_INCREMENT,
                `dataset_id` int(11) NOT NULL,
                `ip_address` varchar(45) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
                `user_agent` varchar(500) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
                `viewed_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
                PRIMARY KEY (`id`),
                KEY `dataset_id` (`dataset_id`),
                KEY `viewed_at` (`viewed_at`),
                KEY `date_index` (`viewed_at`, `dataset_id`)
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;";

            $this->db->query($sql);
            log_message('info', 'Created ' . $this->table_views_log . ' table');
        }
    }

    /**
     * เช็คและสร้างตาราง downloads log
     */
    private function check_and_create_downloads_log_table()
    {
        if (!$this->db->table_exists($this->table_downloads_log)) {
            $sql = "CREATE TABLE `{$this->table_downloads_log}` (
                `id` int(11) NOT NULL AUTO_INCREMENT,
                `dataset_id` int(11) NOT NULL,
                `ip_address` varchar(45) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
                `user_agent` varchar(500) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
                `downloaded_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
                PRIMARY KEY (`id`),
                KEY `dataset_id` (`dataset_id`),
                KEY `downloaded_at` (`downloaded_at`),
                KEY `date_index` (`downloaded_at`, `dataset_id`)
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;";

            $this->db->query($sql);
            log_message('info', 'Created ' . $this->table_downloads_log . ' table');
        }
    }

    /**
     * เพิ่มคอลัมน์ file_size ถ้ายังไม่มี
     */
    private function add_file_size_column_if_not_exists()
    {
        try {
            if (!$this->db->field_exists('file_size', $this->table_catalog)) {
                $sql = "ALTER TABLE `{$this->table_catalog}` 
                        ADD COLUMN `file_size` bigint(20) DEFAULT NULL 
                        AFTER `record_count`";
                $this->db->query($sql);
                log_message('info', 'Added file_size column to ' . $this->table_catalog);
            }

            if (!$this->db->field_exists('downloads', $this->table_catalog)) {
                $sql = "ALTER TABLE `{$this->table_catalog}` 
                        ADD COLUMN `downloads` int(11) DEFAULT '0' 
                        AFTER `views`,
                        ADD KEY `downloads` (`downloads`)";
                $this->db->query($sql);
                log_message('info', 'Added downloads column to ' . $this->table_catalog);
            }
        } catch (Exception $e) {
            log_message('error', 'Error adding columns: ' . $e->getMessage());
        }
    }

    /**
     * เพิ่มคอลัมน์ที่หายไปใน metadata table
     */
    private function add_metadata_columns_if_not_exists()
    {
        try {
            if (!$this->db->field_exists('is_unique', $this->table_metadata)) {
                $sql = "ALTER TABLE `{$this->table_metadata}` 
                        ADD COLUMN `is_unique` tinyint(1) DEFAULT '0' 
                        AFTER `is_primary_key`";
                $this->db->query($sql);
                log_message('info', 'Added is_unique column to ' . $this->table_metadata);
            }

            if (!$this->db->field_exists('sort_order', $this->table_metadata)) {
                if ($this->db->field_exists('display_order', $this->table_metadata)) {
                    $sql = "ALTER TABLE `{$this->table_metadata}` 
                            CHANGE `display_order` `sort_order` int(11) DEFAULT '0'";
                    $this->db->query($sql);
                    log_message('info', 'Renamed display_order to sort_order in ' . $this->table_metadata);
                } else {
                    $sql = "ALTER TABLE `{$this->table_metadata}` 
                            ADD COLUMN `sort_order` int(11) DEFAULT '0' 
                            AFTER `example_value`";
                    $this->db->query($sql);
                    log_message('info', 'Added sort_order column to ' . $this->table_metadata);
                }
            }
        } catch (Exception $e) {
            log_message('error', 'Error adding metadata columns: ' . $e->getMessage());
        }
    }

    /**
     * สร้างตาราง tbl_data_catalog_category
     */
    private function create_category_table()
    {
        $sql = "CREATE TABLE IF NOT EXISTS `{$this->table_category}` (
            `id` int(11) NOT NULL AUTO_INCREMENT,
            `category_name` varchar(200) COLLATE utf8mb4_unicode_ci NOT NULL,
            `category_name_en` varchar(200) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
            `description` text COLLATE utf8mb4_unicode_ci,
            `icon` varchar(100) COLLATE utf8mb4_unicode_ci DEFAULT 'fas fa-folder',
            `color` varchar(20) COLLATE utf8mb4_unicode_ci DEFAULT '#0066cc',
            `sort_order` int(11) DEFAULT '0',
            `status` tinyint(1) DEFAULT '1',
            `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
            `updated_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
            PRIMARY KEY (`id`),
            KEY `status` (`status`),
            KEY `sort_order` (`sort_order`)
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;";

        $this->db->query($sql);
    }

    /**
     * สร้างตาราง tbl_data_catalog
     */
    private function create_catalog_table()
    {
        $sql = "CREATE TABLE IF NOT EXISTS `{$this->table_catalog}` (
            `id` int(11) NOT NULL AUTO_INCREMENT,
            `category_id` int(11) NOT NULL,
            `dataset_name` varchar(300) COLLATE utf8mb4_unicode_ci NOT NULL,
            `dataset_name_en` varchar(300) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
            `description` text COLLATE utf8mb4_unicode_ci,
            `table_name` varchar(200) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
            `data_source` varchar(300) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
            `data_format` varchar(50) COLLATE utf8mb4_unicode_ci DEFAULT 'Database',
            `access_level` enum('public','restricted','private') COLLATE utf8mb4_unicode_ci DEFAULT 'public',
            `responsible_department` varchar(300) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
            `responsible_person` varchar(200) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
            `contact_email` varchar(200) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
            `contact_phone` varchar(50) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
            `keywords` text COLLATE utf8mb4_unicode_ci,
            `license` varchar(200) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
            `update_frequency` varchar(100) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
            `record_count` int(11) DEFAULT '0',
            `file_size` bigint(20) DEFAULT NULL,
            `last_updated` date DEFAULT NULL,
            `download_url` varchar(500) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
            `api_endpoint` varchar(500) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
            `views` int(11) DEFAULT '0',
            `downloads` int(11) DEFAULT '0',
            `status` tinyint(1) DEFAULT '1',
            `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
            `updated_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
            PRIMARY KEY (`id`),
            KEY `category_id` (`category_id`),
            KEY `status` (`status`),
            KEY `access_level` (`access_level`),
            KEY `views` (`views`),
            KEY `downloads` (`downloads`)
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;";

        $this->db->query($sql);
    }

    /**
     * สร้างตาราง tbl_data_catalog_metadata
     */
    private function create_metadata_table()
    {
        $sql = "CREATE TABLE IF NOT EXISTS `{$this->table_metadata}` (
            `id` int(11) NOT NULL AUTO_INCREMENT,
            `dataset_id` int(11) NOT NULL,
            `field_name` varchar(200) COLLATE utf8mb4_unicode_ci NOT NULL,
            `field_name_en` varchar(200) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
            `field_type` varchar(50) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
            `field_length` varchar(50) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
            `field_description` text COLLATE utf8mb4_unicode_ci,
            `is_required` tinyint(1) DEFAULT '0',
            `is_primary_key` tinyint(1) DEFAULT '0',
            `is_unique` tinyint(1) DEFAULT '0',
            `example_value` varchar(500) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
            `sort_order` int(11) DEFAULT '0',
            `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
            `updated_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
            PRIMARY KEY (`id`),
            KEY `dataset_id` (`dataset_id`),
            KEY `sort_order` (`sort_order`)
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;";

        $this->db->query($sql);
    }

    /**
     * เพิ่มหมวดหมู่เริ่มต้น
     */
    private function insert_default_categories()
    {
        $categories = [
            ['category_name' => '📰 ระบบข่าวสารและประชาสัมพันธ์', 'category_name_en' => 'News & Public Relations', 'description' => 'ข้อมูลข่าวสาร ประกาศ ประชาสัมพันธ์ต่างๆ', 'icon' => 'fas fa-newspaper', 'color' => '#1976d2', 'sort_order' => 1],
            ['category_name' => '💬 ระบบกิจกรรมและสวัสดิการ', 'category_name_en' => 'Activities & Welfare', 'description' => 'ข้อมูลกิจกรรม โครงการ สวัสดิการ', 'icon' => 'fas fa-calendar-alt', 'color' => '#7b1fa2', 'sort_order' => 2],
            ['category_name' => '🗺️ ระบบ OTOP และสถานที่ท่องเที่ยว', 'category_name_en' => 'Tourism & Places', 'description' => 'ข้อมูล OTOP และ สถานที่ท่องเที่ยว', 'icon' => 'fas fa-map-marked-alt', 'color' => '#00897b', 'sort_order' => 3],
            ['category_name' => '🏛️ ระบบบุคลากรและโครงสร้าง', 'category_name_en' => 'Personnel & Structure', 'description' => 'ข้อมูลบุคลากร โครงสร้างองค์กร', 'icon' => 'fas fa-users', 'color' => '#e53935', 'sort_order' => 4],
            ['category_name' => '💰 ระบบการเงินและงบประมาณ', 'category_name_en' => 'Finance & Budget', 'description' => 'ข้อมูลการเงิน งบประมาณ รายรับ-รายจ่าย', 'icon' => 'fas fa-coins', 'color' => '#f57c00', 'sort_order' => 5],
            ['category_name' => '📋 ระบบจัดซื้อจัดจ้าง', 'category_name_en' => 'Procurement', 'description' => 'ข้อมูลการจัดซื้อจัดจ้าง ประกาศราคากลาง', 'icon' => 'fas fa-shopping-cart', 'color' => '#5e35b1', 'sort_order' => 6],
            ['category_name' => '📊 ระบบแผนและยุทธศาสตร์', 'category_name_en' => 'Planning & Strategy', 'description' => 'ข้อมูลแผนพัฒนา แผนยุทธศาสตร์', 'icon' => 'fas fa-chart-line', 'color' => '#0097a7', 'sort_order' => 7],
            ['category_name' => '📜 ระบบกฎหมายและระเบียบ', 'category_name_en' => 'Laws & Regulations', 'description' => 'ข้อมูลกฎหมาย ระเบียบ ข้อบังคับต่างๆ', 'icon' => 'fas fa-gavel', 'color' => '#6d4c41', 'sort_order' => 8],
            ['category_name' => '📁 ระบบเอกสารและรายงาน', 'category_name_en' => 'Documents & Reports', 'description' => 'ข้อมูลเอกสาร รายงานต่างๆ', 'icon' => 'fas fa-file-alt', 'color' => '#455a64', 'sort_order' => 9],
            ['category_name' => '👥 ระบบสมาชิกและผู้ใช้งาน', 'category_name_en' => 'Members & Users', 'description' => 'ข้อมูลสมาชิก ผู้ใช้งานระบบ', 'icon' => 'fas fa-user-circle', 'color' => '#c62828', 'sort_order' => 10],
            ['category_name' => '🎓 ระบบการศึกษาและทุนการศึกษา', 'category_name_en' => 'Education & Scholarships', 'description' => 'ข้อมูลทุนการศึกษา โครงการการศึกษา', 'icon' => 'fas fa-graduation-cap', 'color' => '#1565c0', 'sort_order' => 11],
            ['category_name' => '🏥 ระบบสาธารณสุขและการแพทย์', 'category_name_en' => 'Healthcare & Medical', 'description' => 'ข้อมูลสาธารณสุข บริการการแพทย์', 'icon' => 'fas fa-hospital', 'color' => '#d32f2f', 'sort_order' => 12],
            ['category_name' => '🌱 ระบบสิ่งแวดล้อมและทรัพยากร', 'category_name_en' => 'Environment & Resources', 'description' => 'ข้อมูลสิ่งแวดล้อม ทรัพยากรธรรมชาติ', 'icon' => 'fas fa-leaf', 'color' => '#388e3c', 'sort_order' => 13],
            ['category_name' => '📞 ระบบร้องเรียน-ร้องทุกข์', 'category_name_en' => 'Complaints & Feedback', 'description' => 'ข้อมูลการร้องเรียน ร้องทุกข์', 'icon' => 'fas fa-bullhorn', 'color' => '#f57f17', 'sort_order' => 14],
            ['category_name' => '⚙️ ระบบอื่นๆ', 'category_name_en' => 'Others', 'description' => 'ข้อมูลอื่นๆ ที่ไม่อยู่ในหมวดหมู่ข้างต้น', 'icon' => 'fas fa-cogs', 'color' => '#616161', 'sort_order' => 15]
        ];

        foreach ($categories as $category) {
            $this->db->insert($this->table_category, $category);
        }
    }

    /**
     * เพิ่มข้อมูลชุดข้อมูลตัวอย่าง
     */
    private function insert_sample_datasets()
    {
        $datasets = [
            [
                'id' => 39,
                'category_id' => 2,
                'dataset_name' => 'กิจกรรม',
                'dataset_name_en' => '',
                'description' => 'กิจกรรม',
                'table_name' => 'tbl_activity',
                'data_format' => 'JSON',
                'data_source' => '',
                'responsible_person' => '',
                'responsible_department' => 'สำนักปลัด',
                'contact_email' => '',
                'contact_phone' => '',
                'update_frequency' => 'รายวัน',
                'last_updated' => '2025-10-26',
                'keywords' => 'กิจกรรม',
                'license' => '',
                'access_level' => 'public',
                'api_endpoint' => 'api_data_catalog/dataset/39',
                'download_url' => 'api_data_catalog/download/39',
                'record_count' => 0,
                'file_size' => NULL,
                'views' => 0,
                'status' => 1
            ],
            [
                'id' => 40,
                'category_id' => 1,
                'dataset_name' => 'ข่าวประชาสัมพันธ์',
                'dataset_name_en' => '',
                'description' => 'ข่าวประชาสัมพันธ์',
                'table_name' => 'tbl_activity',
                'data_format' => 'JSON',
                'data_source' => '',
                'responsible_person' => '',
                'responsible_department' => 'สำนักปลัด',
                'contact_email' => '',
                'contact_phone' => '',
                'update_frequency' => 'รายวัน',
                'last_updated' => '2025-01-01',
                'keywords' => 'ข่าวประชาสัมพันธ์ ข่าวสาร',
                'license' => '',
                'access_level' => 'public',
                'api_endpoint' => 'api_data_catalog/dataset/40',
                'download_url' => 'api_data_catalog/download/40',
                'record_count' => 0,
                'file_size' => NULL,
                'views' => 18,
                'status' => 1
            ],
            [
                'id' => 41,
                'category_id' => 5,
                'dataset_name' => 'การเงิน งบประมาณ',
                'dataset_name_en' => '',
                'description' => 'การเงิน งบประมาณ',
                'table_name' => 'tbl_finance',
                'data_format' => 'JSON',
                'data_source' => '',
                'responsible_person' => '',
                'responsible_department' => 'กองคลัง',
                'contact_email' => '',
                'contact_phone' => '',
                'update_frequency' => NULL,
                'last_updated' => '2025-10-01',
                'keywords' => 'การเงิน งบประมาณ',
                'license' => '',
                'access_level' => 'public',
                'api_endpoint' => 'api_data_catalog/dataset/41',
                'download_url' => 'api_data_catalog/download/41',
                'record_count' => 0,
                'file_size' => NULL,
                'views' => 12,
                'status' => 1
            ],
            [
                'id' => 42,
                'category_id' => 3,
                'dataset_name' => 'สินค้า OTOP',
                'dataset_name_en' => '',
                'description' => 'สินค้า OTOP',
                'table_name' => 'tbl_otop',
                'data_format' => 'JSON',
                'data_source' => '',
                'responsible_person' => '',
                'responsible_department' => 'สำนักปลัด',
                'contact_email' => '',
                'contact_phone' => '',
                'update_frequency' => NULL,
                'last_updated' => '2025-10-01',
                'keywords' => 'สินค้า OTOP',
                'license' => '',
                'access_level' => 'public',
                'api_endpoint' => 'api_data_catalog/dataset/42',
                'download_url' => 'api_data_catalog/download/42',
                'record_count' => 0,
                'file_size' => NULL,
                'views' => 5,
                'status' => 1
            ],
            [
                'id' => 43,
                'category_id' => 6,
                'dataset_name' => 'แผนงานและนโยบาย',
                'dataset_name_en' => '',
                'description' => 'แผนงานและนโยบาย',
                'table_name' => 'tbl_plan_pdl',
                'data_format' => 'JSON',
                'data_source' => '',
                'responsible_person' => '',
                'responsible_department' => 'สำนักปลัด',
                'contact_email' => '',
                'contact_phone' => '',
                'update_frequency' => NULL,
                'last_updated' => '2025-10-01',
                'keywords' => 'แผนงาน, นโยบาย',
                'license' => '',
                'access_level' => 'public',
                'api_endpoint' => 'api_data_catalog/dataset/43',
                'download_url' => 'api_data_catalog/download/43',
                'record_count' => 0,
                'file_size' => NULL,
                'views' => 0,
                'status' => 1
            ],
            [
                'id' => 44,
                'category_id' => 8,
                'dataset_name' => 'การดำเนินงาน',
                'dataset_name_en' => '',
                'description' => 'การดำเนินงาน',
                'table_name' => 'tbl_operation_report',
                'data_format' => 'JSON',
                'data_source' => '',
                'responsible_person' => '',
                'responsible_department' => 'สำนักปลัด',
                'contact_email' => '',
                'contact_phone' => '',
                'update_frequency' => NULL,
                'last_updated' => '2025-10-01',
                'keywords' => 'การดำเนินงาน',
                'license' => '',
                'access_level' => 'public',
                'api_endpoint' => 'api_data_catalog/dataset/44',
                'download_url' => 'api_data_catalog/download/44',
                'record_count' => 0,
                'file_size' => NULL,
                'views' => 0,
                'status' => 1
            ],
            [
                'id' => 45,
                'category_id' => 9,
                'dataset_name' => 'กฎหมาย และ ระเบียบ',
                'dataset_name_en' => '',
                'description' => 'กฎหมาย และ ระเบียบ',
                'table_name' => 'tbl_laws',
                'data_format' => 'JSON',
                'data_source' => '',
                'responsible_person' => '',
                'responsible_department' => 'สำนักปลัด',
                'contact_email' => '',
                'contact_phone' => '',
                'update_frequency' => 'รายเดือน',
                'last_updated' => '2025-10-28',
                'keywords' => 'กฎหมาย ระเบียบ',
                'license' => '',
                'access_level' => 'public',
                'api_endpoint' => 'api_data_catalog/dataset/45',
                'download_url' => 'api_data_catalog/download/45',
                'record_count' => 0,
                'file_size' => NULL,
                'views' => 0,
                'status' => 1
            ]
        ];

        foreach ($datasets as $dataset) {
            $this->db->insert($this->table_catalog, $dataset);
        }
    }

    /**
     * เพิ่มข้อมูล Metadata ตัวอย่าง
     */
    private function insert_sample_metadata()
    {
        $metadata = [
            // Dataset 39 - กิจกรรม
            ['id' => 14, 'dataset_id' => 39, 'field_name' => 'activity_name', 'field_name_en' => 'topic', 'field_type' => 'MEDIUMTEXT', 'field_description' => 'หัวข้อ', 'is_required' => 0, 'is_unique' => 0, 'example_value' => NULL, 'sort_order' => 0],
            ['id' => 15, 'dataset_id' => 39, 'field_name' => 'activity_detail', 'field_name_en' => 'detail', 'field_type' => 'MEDIUMTEXT', 'field_description' => 'รายละเอียด', 'is_required' => 0, 'is_unique' => 0, 'example_value' => NULL, 'sort_order' => 0],
            ['id' => 16, 'dataset_id' => 39, 'field_name' => 'activity_img', 'field_name_en' => 'picture', 'field_type' => 'VARCHAR(100)', 'field_description' => 'รูปภาพ', 'is_required' => 0, 'is_unique' => 0, 'example_value' => NULL, 'sort_order' => 0],
            ['id' => 17, 'dataset_id' => 39, 'field_name' => 'activity_date', 'field_name_en' => 'date', 'field_type' => 'DATETIME', 'field_description' => 'วันที่', 'is_required' => 0, 'is_unique' => 0, 'example_value' => NULL, 'sort_order' => 0],

            // Dataset 40 - ข่าวประชาสัมพันธ์
            ['id' => 30, 'dataset_id' => 40, 'field_name' => 'activity_name', 'field_name_en' => 'topic', 'field_type' => 'MEDIUMTEXT', 'field_description' => 'หัวข้อ', 'is_required' => 0, 'is_unique' => 0, 'example_value' => NULL, 'sort_order' => 0],
            ['id' => 31, 'dataset_id' => 40, 'field_name' => 'activity_detail', 'field_name_en' => 'detail', 'field_type' => 'MEDIUMTEXT', 'field_description' => 'รายละเอียด', 'is_required' => 0, 'is_unique' => 0, 'example_value' => NULL, 'sort_order' => 0],
            ['id' => 32, 'dataset_id' => 40, 'field_name' => 'activity_date', 'field_name_en' => 'date', 'field_type' => 'DATETIME', 'field_description' => 'วันที่', 'is_required' => 0, 'is_unique' => 0, 'example_value' => NULL, 'sort_order' => 0],
            ['id' => 33, 'dataset_id' => 40, 'field_name' => 'activity_img', 'field_name_en' => 'picture', 'field_type' => 'VARCHAR(100)', 'field_description' => 'ภาพ(ถ้ามี)', 'is_required' => 0, 'is_unique' => 0, 'example_value' => NULL, 'sort_order' => 0],

            // Dataset 41 - การเงิน
            ['id' => 18, 'dataset_id' => 41, 'field_name' => 'finance_name', 'field_name_en' => 'name', 'field_type' => 'VARCHAR(100)', 'field_description' => 'ชื่อ', 'is_required' => 0, 'is_unique' => 0, 'example_value' => NULL, 'sort_order' => 0],
            ['id' => 19, 'dataset_id' => 41, 'field_name' => 'finance_detail', 'field_name_en' => 'detail', 'field_type' => 'MEDIUMTEXT', 'field_description' => 'รายละเอียด', 'is_required' => 0, 'is_unique' => 0, 'example_value' => NULL, 'sort_order' => 0],
            ['id' => 20, 'dataset_id' => 41, 'field_name' => 'finance_link', 'field_name_en' => 'link', 'field_type' => 'VARCHAR(255)', 'field_description' => 'ลิงค์', 'is_required' => 0, 'is_unique' => 0, 'example_value' => NULL, 'sort_order' => 0],
            ['id' => 21, 'dataset_id' => 41, 'field_name' => 'finance_img', 'field_name_en' => 'picture', 'field_type' => 'VARCHAR(255)', 'field_description' => 'รูปภาพ', 'is_required' => 0, 'is_unique' => 0, 'example_value' => NULL, 'sort_order' => 0],
            ['id' => 22, 'dataset_id' => 41, 'field_name' => 'finance_date', 'field_name_en' => 'date', 'field_type' => 'DATETIME', 'field_description' => 'วันที่', 'is_required' => 0, 'is_unique' => 0, 'example_value' => NULL, 'sort_order' => 0],

            // Dataset 42 - OTOP
            ['id' => 34, 'dataset_id' => 42, 'field_name' => 'otop_name', 'field_name_en' => 'name', 'field_type' => 'VARCHAR(255)', 'field_description' => '', 'is_required' => 0, 'is_unique' => 0, 'example_value' => NULL, 'sort_order' => 0],
            ['id' => 35, 'dataset_id' => 42, 'field_name' => 'otop_detail', 'field_name_en' => 'detail', 'field_type' => 'MEDIUMTEXT', 'field_description' => '', 'is_required' => 0, 'is_unique' => 0, 'example_value' => NULL, 'sort_order' => 0],
            ['id' => 36, 'dataset_id' => 42, 'field_name' => 'otop_price', 'field_name_en' => 'price', 'field_type' => 'VARCHAR(10)', 'field_description' => '', 'is_required' => 0, 'is_unique' => 0, 'example_value' => NULL, 'sort_order' => 0],
            ['id' => 37, 'dataset_id' => 42, 'field_name' => 'otop_seller', 'field_name_en' => 'seller', 'field_type' => 'VARCHAR(100)', 'field_description' => '', 'is_required' => 0, 'is_unique' => 0, 'example_value' => NULL, 'sort_order' => 0],
            ['id' => 38, 'dataset_id' => 42, 'field_name' => 'otop_fb', 'field_name_en' => 'seller_fb', 'field_type' => 'VARCHAR(100)', 'field_description' => '', 'is_required' => 0, 'is_unique' => 0, 'example_value' => NULL, 'sort_order' => 0],
            ['id' => 39, 'dataset_id' => 42, 'field_name' => 'otop_phone', 'field_name_en' => 'seller_phone', 'field_type' => 'VARCHAR(10)', 'field_description' => '', 'is_required' => 0, 'is_unique' => 0, 'example_value' => NULL, 'sort_order' => 0],
            ['id' => 40, 'dataset_id' => 42, 'field_name' => 'otop_date', 'field_name_en' => 'date', 'field_type' => 'DATETIME', 'field_description' => '', 'is_required' => 0, 'is_unique' => 0, 'example_value' => NULL, 'sort_order' => 0],

            // Dataset 43 - แผนงาน
            ['id' => 41, 'dataset_id' => 43, 'field_name' => 'plan_pdl_name', 'field_name_en' => 'name', 'field_type' => 'VARCHAR(100)', 'field_description' => '', 'is_required' => 0, 'is_unique' => 0, 'example_value' => NULL, 'sort_order' => 0],
            ['id' => 42, 'dataset_id' => 43, 'field_name' => 'plan_pdl_detail', 'field_name_en' => 'รายละเอียด', 'field_type' => 'TEXT', 'field_description' => '', 'is_required' => 0, 'is_unique' => 0, 'example_value' => NULL, 'sort_order' => 0],
            ['id' => 43, 'dataset_id' => 43, 'field_name' => 'plan_pdl_link', 'field_name_en' => 'link', 'field_type' => 'VARCHAR(255)', 'field_description' => '', 'is_required' => 0, 'is_unique' => 0, 'example_value' => NULL, 'sort_order' => 0],
            ['id' => 44, 'dataset_id' => 43, 'field_name' => 'plan_pdl_img', 'field_name_en' => 'picture', 'field_type' => 'VARCHAR(255)', 'field_description' => '', 'is_required' => 0, 'is_unique' => 0, 'example_value' => NULL, 'sort_order' => 0],
            ['id' => 45, 'dataset_id' => 43, 'field_name' => 'plan_pdl_date', 'field_name_en' => 'date', 'field_type' => 'DATETIME', 'field_description' => '', 'is_required' => 0, 'is_unique' => 0, 'example_value' => NULL, 'sort_order' => 0],

            // Dataset 44 - การดำเนินงาน
            ['id' => 46, 'dataset_id' => 44, 'field_name' => 'operation_report_name', 'field_name_en' => 'name', 'field_type' => 'VARCHAR(100)', 'field_description' => '', 'is_required' => 0, 'is_unique' => 0, 'example_value' => NULL, 'sort_order' => 0],
            ['id' => 47, 'dataset_id' => 44, 'field_name' => 'operation_report_detail', 'field_name_en' => 'detail', 'field_type' => 'MEDIUMTEXT', 'field_description' => '', 'is_required' => 0, 'is_unique' => 0, 'example_value' => NULL, 'sort_order' => 0],
            ['id' => 48, 'dataset_id' => 44, 'field_name' => 'operation_report_link', 'field_name_en' => 'link', 'field_type' => 'VARCHAR(255)', 'field_description' => '', 'is_required' => 0, 'is_unique' => 0, 'example_value' => NULL, 'sort_order' => 0],
            ['id' => 49, 'dataset_id' => 44, 'field_name' => 'operation_report_img', 'field_name_en' => 'picture', 'field_type' => 'VARCHAR(255)', 'field_description' => '', 'is_required' => 0, 'is_unique' => 0, 'example_value' => NULL, 'sort_order' => 0],
            ['id' => 50, 'dataset_id' => 44, 'field_name' => 'operation_report_date', 'field_name_en' => 'date', 'field_type' => 'DATETIME', 'field_description' => '', 'is_required' => 0, 'is_unique' => 0, 'example_value' => NULL, 'sort_order' => 0],

            // Dataset 45 - กฎหมาย
            ['id' => 57, 'dataset_id' => 45, 'field_name' => 'laws_name', 'field_name_en' => 'name', 'field_type' => 'VARCHAR(255)', 'field_description' => '', 'is_required' => 0, 'is_unique' => 0, 'example_value' => NULL, 'sort_order' => 0],
            ['id' => 58, 'dataset_id' => 45, 'field_name' => 'laws_pdf', 'field_name_en' => 'file', 'field_type' => 'VARCHAR(255)', 'field_description' => '', 'is_required' => 0, 'is_unique' => 0, 'example_value' => NULL, 'sort_order' => 0],
            ['id' => 59, 'dataset_id' => 45, 'field_name' => 'laws_date', 'field_name_en' => 'date', 'field_type' => 'DATETIME', 'field_description' => '', 'is_required' => 0, 'is_unique' => 0, 'example_value' => NULL, 'sort_order' => 0],
        ];

        foreach ($metadata as $meta) {
            $this->db->insert($this->table_metadata, $meta);
        }
    }

    /**
     * บันทึก log การเข้าชม
     */
    public function log_view($dataset_id)
    {
        // ใช้ $_SERVER แทน input library
        $ip_address = isset($_SERVER['REMOTE_ADDR']) ? $_SERVER['REMOTE_ADDR'] : '0.0.0.0';
        $user_agent = isset($_SERVER['HTTP_USER_AGENT']) ? substr($_SERVER['HTTP_USER_AGENT'], 0, 500) : 'Unknown';

        $data = [
            'dataset_id' => $dataset_id,
            'ip_address' => $ip_address,
            'user_agent' => $user_agent,
            'viewed_at' => date('Y-m-d H:i:s')
        ];

        return $this->db->insert($this->table_views_log, $data);
    }

    /**
     * บันทึก log การดาวน์โหลด
     */
    public function log_download($dataset_id)
    {
        // ใช้ $_SERVER แทน input library
        $ip_address = isset($_SERVER['REMOTE_ADDR']) ? $_SERVER['REMOTE_ADDR'] : '0.0.0.0';
        $user_agent = isset($_SERVER['HTTP_USER_AGENT']) ? substr($_SERVER['HTTP_USER_AGENT'], 0, 500) : 'Unknown';

        $data = [
            'dataset_id' => $dataset_id,
            'ip_address' => $ip_address,
            'user_agent' => $user_agent,
            'downloaded_at' => date('Y-m-d H:i:s')
        ];

        return $this->db->insert($this->table_downloads_log, $data);
    }

    /**
     * ดึงหมวดหมู่ทั้งหมด พร้อมนับจำนวน dataset
     */
    public function get_all_categories()
    {
        $this->db->select('c.*, COUNT(CASE WHEN d.status = 1 THEN d.id END) as dataset_count');
        $this->db->from($this->table_category . ' c');
        $this->db->join($this->table_catalog . ' d', 'c.id = d.category_id', 'left');
        $this->db->where('c.status', 1);
        $this->db->group_by('c.id');
        $this->db->having('dataset_count >', 0);
        $this->db->order_by('c.sort_order', 'ASC');
        return $this->db->get()->result();
    }

    /**
     * ดึงข้อมูลหมวดหมู่เดียว
     */
    public function get_category($category_id)
    {
        $this->db->where('id', $category_id);
        $this->db->where('status', 1);
        return $this->db->get($this->table_category)->row();
    }

    /**
     * ดึง Dataset ทั้งหมด
     */
    public function get_all_datasets($limit = 50, $offset = 0)
    {
        $this->db->select('d.*, c.category_name, c.category_name_en, COUNT(v.id) as views');
        $this->db->from($this->table_catalog . ' d');
        $this->db->join($this->table_category . ' c', 'd.category_id = c.id', 'left');
        $this->db->join($this->table_views_log . ' v', 'd.id = v.dataset_id', 'left');
        $this->db->where('d.status', 1);
        $this->db->group_by('d.id');
        $this->db->order_by('d.updated_at', 'DESC');
        $this->db->limit($limit, $offset);
        return $this->db->get()->result();
    }

    /**
     * ดึง Dataset ที่อัปเดตล่าสุด (ดึง views จาก log จริง) ⭐
     */
    public function get_recent_datasets($limit = 6)
    {
        $this->db->select('d.*, c.category_name, c.icon, c.color, COUNT(v.id) as views');
        $this->db->from($this->table_catalog . ' d');
        $this->db->join($this->table_category . ' c', 'd.category_id = c.id', 'left');
        $this->db->join($this->table_views_log . ' v', 'd.id = v.dataset_id', 'left');
        $this->db->where('d.status', 1);
        $this->db->group_by('d.id');
        $this->db->order_by('d.updated_at', 'DESC');
        $this->db->limit($limit);
        return $this->db->get()->result();
    }

    /**
     * ดึง Dataset ยอดนิยม (เรียงตามจำนวนการเข้าชมจาก log จริง)
     */
    public function get_popular_datasets($limit = 6)
    {
        $this->db->select('d.*, c.category_name, c.icon, c.color, COUNT(v.id) as views');
        $this->db->from($this->table_catalog . ' d');
        $this->db->join($this->table_category . ' c', 'd.category_id = c.id', 'left');
        $this->db->join($this->table_views_log . ' v', 'd.id = v.dataset_id', 'left');
        $this->db->where('d.status', 1);
        $this->db->group_by('d.id');
        $this->db->order_by('views', 'DESC');
        $this->db->limit($limit);
        return $this->db->get()->result();
    }

    /**
     * ดึง Dataset ยอดนิยมสำหรับหน้า Statistics (Top 10 จาก log จริง)
     */
    public function get_popular_datasets_for_stats($limit = 10)
    {
        $this->db->select('d.id, d.dataset_name, c.category_name, c.icon, c.color, COUNT(v.id) as views');
        $this->db->from($this->table_catalog . ' d');
        $this->db->join($this->table_category . ' c', 'd.category_id = c.id', 'left');
        $this->db->join($this->table_views_log . ' v', 'd.id = v.dataset_id', 'inner');
        $this->db->where('d.status', 1);
        $this->db->group_by('d.id');
        $this->db->order_by('views', 'DESC');
        $this->db->limit($limit);
        return $this->db->get()->result();
    }

    /**
     * ดึง Dataset ยอดนิยมในช่วงเวลาที่กำหนด
     */
    public function get_popular_datasets_by_period($start_date = null, $end_date = null, $limit = 10)
    {
        $this->db->select('d.id, d.dataset_name, c.category_name, c.icon, c.color, COUNT(v.id) as views');
        $this->db->from($this->table_catalog . ' d');
        $this->db->join($this->table_category . ' c', 'd.category_id = c.id', 'left');
        $this->db->join($this->table_views_log . ' v', 'd.id = v.dataset_id', 'inner');
        $this->db->where('d.status', 1);

        if ($start_date) {
            $this->db->where('v.viewed_at >=', $start_date);
        }
        if ($end_date) {
            $this->db->where('v.viewed_at <=', $end_date);
        }

        $this->db->group_by('d.id');
        $this->db->order_by('views', 'DESC');
        $this->db->limit($limit);
        return $this->db->get()->result();
    }

    /**
     * ค้นหา Datasets (ดึง views จาก log จริง) ⭐
     */
    public function search_datasets($query, $category = null, $format = null, $access = null, $limit = 10, $offset = 0, $sort = 'relevance')
    {
        $this->db->select('d.*, c.category_name, c.icon, c.color, COUNT(v.id) as views');
        $this->db->from($this->table_catalog . ' d');
        $this->db->join($this->table_category . ' c', 'd.category_id = c.id', 'left');
        $this->db->join($this->table_views_log . ' v', 'd.id = v.dataset_id', 'left');
        $this->db->where('d.status', 1);

        $this->db->group_start();
        $this->db->like('d.dataset_name', $query);
        $this->db->or_like('d.description', $query);
        $this->db->or_like('d.keywords', $query);
        $this->db->group_end();

        if (!empty($category)) {
            $this->db->where('d.category_id', $category);
        }

        if (!empty($format)) {
            $this->db->where('d.data_format', $format);
        }

        if (!empty($access)) {
            $this->db->where('d.access_level', $access);
        }

        $this->db->group_by('d.id');

        switch ($sort) {
            case 'newest':
                $this->db->order_by('d.created_at', 'DESC');
                break;
            case 'oldest':
                $this->db->order_by('d.created_at', 'ASC');
                break;
            case 'name_asc':
                $this->db->order_by('d.dataset_name', 'ASC');
                break;
            case 'name_desc':
                $this->db->order_by('d.dataset_name', 'DESC');
                break;
            case 'views':
                $this->db->order_by('views', 'DESC');
                break;
            default:
                $this->db->order_by('d.updated_at', 'DESC');
        }

        $this->db->limit($limit, $offset);
        return $this->db->get()->result();
    }

    /**
     * นับจำนวนผลการค้นหา
     */
    public function count_search_results($query, $category = null, $format = null, $access = null)
    {
        $this->db->from($this->table_catalog . ' d');
        $this->db->where('d.status', 1);

        $this->db->group_start();
        $this->db->like('d.dataset_name', $query);
        $this->db->or_like('d.description', $query);
        $this->db->or_like('d.keywords', $query);
        $this->db->group_end();

        if (!empty($category)) {
            $this->db->where('d.category_id', $category);
        }

        if (!empty($format)) {
            $this->db->where('d.data_format', $format);
        }

        if (!empty($access)) {
            $this->db->where('d.access_level', $access);
        }

        return $this->db->count_all_results();
    }

    /**
     * ดึงจำนวนเข้าชมทั้งหมดจาก log
     */
    public function get_total_views()
    {
        $this->db->select('COUNT(*) as total');
        $result = $this->db->get($this->table_views_log)->row();
        return $result ? (int) $result->total : 0;
    }

    /**
     * ดึงจำนวนดาวน์โหลดทั้งหมดจาก log
     */
    public function get_total_downloads()
    {
        $this->db->select('COUNT(*) as total');
        $result = $this->db->get($this->table_downloads_log)->row();
        return $result ? (int) $result->total : 0;
    }

    /**
     * เพิ่มจำนวนดาวน์โหลดในตาราง catalog (สำหรับแสดงผล)
     */
    public function increment_download($dataset_id)
    {
        $this->db->where('id', $dataset_id);
        $this->db->set('downloads', 'downloads + 1', FALSE);
        return $this->db->update($this->table_catalog);
    }

    /**
     * ดึงสถิติการกระจายตัวตามหมวดหมู่
     */
    public function get_category_distribution()
    {
        $this->db->select('c.category_name, c.icon, c.color, COUNT(d.id) as count');
        $this->db->from($this->table_category . ' c');
        $this->db->join($this->table_catalog . ' d', 'c.id = d.category_id AND d.status = 1', 'left');
        $this->db->group_by('c.id');
        $this->db->order_by('count', 'DESC');
        return $this->db->get()->result();
    }

    /**
     * ดึงสถิติการเข้าชมรายเดือนจาก database จริง
     */
    public function get_monthly_views($months = 6)
    {
        $data = [];
        $month_names = ['ม.ค.', 'ก.พ.', 'มี.ค.', 'เม.ย.', 'พ.ค.', 'มิ.ย.', 'ก.ค.', 'ส.ค.', 'ก.ย.', 'ต.ค.', 'พ.ย.', 'ธ.ค.'];

        // Query ข้อมูลจาก log table
        for ($i = $months - 1; $i >= 0; $i--) {
            $date = strtotime("-$i months");
            $month_index = (int) date('n', $date) - 1;
            $year_month = date('Y-m', $date);

            // นับจำนวน views จาก log ในเดือนนั้นๆ
            $this->db->select('COUNT(*) as total_views');
            $this->db->from($this->table_views_log);
            $this->db->where('DATE_FORMAT(viewed_at, "%Y-%m") =', $year_month);
            $result = $this->db->get()->row();

            $month_views = $result ? (int) $result->total_views : 0;

            $data[] = (object) [
                'month' => $month_names[$month_index] . ' ' . date('y', $date),
                'views' => $month_views
            ];
        }

        return $data;
    }

    /**
     * ดึงสถิติระดับการเข้าถึง
     */
    public function get_access_level_stats()
    {
        $this->db->select('access_level, COUNT(*) as count');
        $this->db->from($this->table_catalog);
        $this->db->where('status', 1);
        $this->db->group_by('access_level');
        return $this->db->get()->result();
    }

    /**
     * ดึงสถิติรูปแบบข้อมูล
     */
    public function get_format_stats()
    {
        $this->db->select('data_format, COUNT(*) as count');
        $this->db->from($this->table_catalog);
        $this->db->where('status', 1);
        $this->db->where('data_format IS NOT NULL');
        $this->db->group_by('data_format');
        $this->db->order_by('count', 'DESC');
        $this->db->limit(5);
        return $this->db->get()->result();
    }

    /**
     * ดึงสถิติความถี่การอัปเดต
     */
    public function get_update_frequency_stats()
    {
        $this->db->select('update_frequency, COUNT(*) as count');
        $this->db->from($this->table_catalog);
        $this->db->where('status', 1);
        $this->db->where('update_frequency IS NOT NULL');
        $this->db->group_by('update_frequency');
        $this->db->order_by('count', 'DESC');
        return $this->db->get()->result();
    }

    /**
     * ดึง Datasets ทั้งหมดพร้อมกรอง (ดึง views จาก log จริง) ⭐
     */
    public function get_all_datasets_filtered($category = null, $sort = 'newest', $limit = 12, $offset = 0)
    {
        $this->db->select('d.*, c.category_name, c.icon, c.color, COUNT(v.id) as views');
        $this->db->from($this->table_catalog . ' d');
        $this->db->join($this->table_category . ' c', 'd.category_id = c.id', 'left');
        $this->db->join($this->table_views_log . ' v', 'd.id = v.dataset_id', 'left'); // ⭐ JOIN กับ log
        $this->db->where('d.status', 1);

        if (!empty($category)) {
            $this->db->where('d.category_id', $category);
        }

        $this->db->group_by('d.id'); // ⭐ GROUP BY เพื่อนับ views

        switch ($sort) {
            case 'newest':
                $this->db->order_by('d.created_at', 'DESC');
                break;
            case 'oldest':
                $this->db->order_by('d.created_at', 'ASC');
                break;
            case 'name_asc':
                $this->db->order_by('d.dataset_name', 'ASC');
                break;
            case 'name_desc':
                $this->db->order_by('d.dataset_name', 'DESC');
                break;
            case 'views':
                $this->db->order_by('views', 'DESC'); // ⭐ เรียงตาม views จาก log
                break;
            default:
                $this->db->order_by('d.updated_at', 'DESC');
        }

        $this->db->limit($limit, $offset);
        return $this->db->get()->result();
    }

    /**
     * นับจำนวน Datasets ทั้งหมด (พร้อมกรอง)
     */
    public function count_all_datasets($category = null)
    {
        $this->db->from($this->table_catalog);
        $this->db->where('status', 1);

        if (!empty($category)) {
            $this->db->where('category_id', $category);
        }

        return $this->db->count_all_results();
    }

    /**
     * ดึง Datasets ทั้งหมดสำหรับหน้า Management
     */
    public function get_all_datasets_for_management()
    {
        $this->db->select('d.*, c.category_name, c.icon, c.color');
        $this->db->from($this->table_catalog . ' d');
        $this->db->join($this->table_category . ' c', 'd.category_id = c.id', 'left');
        $this->db->order_by('d.id', 'DESC');
        return $this->db->get()->result();
    }

    /**
     * เพิ่ม Dataset ใหม่
     */
    public function insert_dataset($data)
    {
        $this->db->insert($this->table_catalog, $data);
        return $this->db->insert_id();
    }

    /**
     * แก้ไข Dataset
     */
    public function update_dataset($dataset_id, $data)
    {
        $this->db->where('id', $dataset_id);
        return $this->db->update($this->table_catalog, $data);
    }

    /**
     * ลบ Dataset
     */
    public function delete_dataset($dataset_id)
    {
        $this->db->where('dataset_id', $dataset_id);
        $this->db->delete($this->table_metadata);

        $this->db->where('id', $dataset_id);
        return $this->db->delete($this->table_catalog);
    }

    /**
     * เพิ่ม Metadata
     */
    public function insert_metadata($data)
    {
        $this->db->insert($this->table_metadata, $data);
        return $this->db->insert_id();
    }

    /**
     * แก้ไข Metadata
     */
    public function update_metadata($metadata_id, $data)
    {
        $this->db->where('id', $metadata_id);
        return $this->db->update($this->table_metadata, $data);
    }

    /**
     * ลบ Metadata
     */
    public function delete_metadata($metadata_id)
    {
        $this->db->where('id', $metadata_id);
        return $this->db->delete($this->table_metadata);
    }

    /**
     * ลบ Metadata ทั้งหมดของ Dataset
     */
    public function delete_metadata_by_dataset($dataset_id)
    {
        $this->db->where('dataset_id', $dataset_id);
        return $this->db->delete($this->table_metadata);
    }

    /**
     * บันทึก Metadata แบบ Batch
     */
    public function save_metadata_batch($dataset_id, $metadata_array)
    {
        // ลบ metadata เก่าทั้งหมดก่อนเสมอ (ย้ายออกจาก if)
        $this->delete_metadata_by_dataset($dataset_id);

        // ถ้าไม่มีข้อมูลใหม่ ให้จบทันที (หลังจากลบแล้ว)
        if (empty($metadata_array) || !is_array($metadata_array)) {
            log_message('info', "Deleted all metadata for dataset_id: {$dataset_id} (no new data)");
            return 0;
        }

        $insert_count = 0;
        foreach ($metadata_array as $meta) {
            // เช็คว่ามี field_name และไม่ว่างเปล่า
            if (!empty($meta['field_name']) && trim($meta['field_name']) !== '') {
                $data = [
                    'dataset_id' => $dataset_id,
                    'field_name' => trim($meta['field_name']),
                    'field_name_en' => isset($meta['field_name_en']) ? trim($meta['field_name_en']) : '',
                    'field_type' => isset($meta['field_type']) ? trim($meta['field_type']) : '',
                    'field_description' => isset($meta['field_description']) ? trim($meta['field_description']) : '',
                    'created_at' => date('Y-m-d H:i:s')
                ];

                $this->db->insert($this->table_metadata, $data);
                $insert_count++;
            }
        }

        log_message('info', "Inserted {$insert_count} metadata records for dataset_id: {$dataset_id}");
        return $insert_count;
    }

    /**
     * ดึง Datasets ทั้งหมดสำหรับ Admin
     */
    public function get_all_datasets_for_admin($limit = 20, $offset = 0)
    {
        $this->db->select('d.*, c.category_name, c.icon, c.color');
        $this->db->from($this->table_catalog . ' d');
        $this->db->join($this->table_category . ' c', 'd.category_id = c.id', 'left');
        $this->db->order_by('d.updated_at', 'DESC');
        $this->db->limit($limit, $offset);
        return $this->db->get()->result();
    }

    /**
     * ดึง Dataset ตามหมวดหมู่ (ดึง views จาก log จริง) ⭐
     */
    public function get_datasets_by_category($category_id, $limit = 20, $offset = 0)
    {
        $this->db->select('d.*, c.category_name, COUNT(v.id) as views');
        $this->db->from($this->table_catalog . ' d');
        $this->db->join($this->table_category . ' c', 'd.category_id = c.id', 'left');
        $this->db->join($this->table_views_log . ' v', 'd.id = v.dataset_id', 'left');
        $this->db->where('d.category_id', $category_id);
        $this->db->where('d.status', 1);
        $this->db->group_by('d.id');
        $this->db->order_by('d.updated_at', 'DESC');
        $this->db->limit($limit, $offset);
        return $this->db->get()->result();
    }

    /**
     * ดึงข้อมูล Dataset เดียว
     */
    public function get_dataset($dataset_id)
    {
        $this->db->select('d.*, c.category_name, c.category_name_en, c.icon, c.color, COUNT(v.id) as views');
        $this->db->from($this->table_catalog . ' d');
        $this->db->join($this->table_category . ' c', 'd.category_id = c.id', 'left');
        $this->db->join($this->table_views_log . ' v', 'd.id = v.dataset_id', 'left');
        $this->db->where('d.id', $dataset_id);
        $this->db->where('d.status', 1);
        $this->db->group_by('d.id');
        return $this->db->get()->row();
    }

    /**
     * ดึง Dataset ตาม ID สำหรับ Admin
     */
    public function get_dataset_by_id($dataset_id)
    {
        $this->db->select('d.*, c.category_name, c.category_name_en, c.icon, c.color, COUNT(v.id) as views');
        $this->db->from($this->table_catalog . ' d');
        $this->db->join($this->table_category . ' c', 'd.category_id = c.id', 'left');
        $this->db->join($this->table_views_log . ' v', 'd.id = v.dataset_id', 'left');
        $this->db->where('d.id', $dataset_id);
        $this->db->group_by('d.id');
        return $this->db->get()->row();
    }

    /**
     * ดึง Metadata ของ Dataset
     */
    public function get_dataset_metadata($dataset_id)
    {
        $this->db->where('dataset_id', $dataset_id);
        $this->db->order_by('sort_order', 'ASC');
        $this->db->order_by('id', 'ASC');
        return $this->db->get($this->table_metadata)->result();
    }

    /**
     * ดึง Dataset ที่เกี่ยวข้อง (ดึง views จาก log จริง) ⭐
     */
    public function get_related_datasets($category_id, $exclude_id, $limit = 4)
    {
        $this->db->select('d.*, c.category_name, COUNT(v.id) as views');
        $this->db->from($this->table_catalog . ' d');
        $this->db->join($this->table_category . ' c', 'd.category_id = c.id', 'left');
        $this->db->join($this->table_views_log . ' v', 'd.id = v.dataset_id', 'left');
        $this->db->where('d.category_id', $category_id);
        $this->db->where('d.id !=', $exclude_id);
        $this->db->where('d.status', 1);
        $this->db->group_by('d.id');
        $this->db->order_by('d.updated_at', 'DESC');
        $this->db->limit($limit);
        return $this->db->get()->result();
    }

    /**
     * นับจำนวน Dataset ทั้งหมด
     */
    public function count_total_datasets()
    {
        $this->db->where('status', 1);
        return $this->db->count_all_results($this->table_catalog);
    }

    /**
     * นับจำนวนหมวดหมู่ทั้งหมด (เฉพาะที่มีข้อมูล)
     */
    public function count_total_categories()
    {
        $this->db->select('COUNT(DISTINCT d.category_id) as total');
        $this->db->from($this->table_catalog . ' d');
        $this->db->join($this->table_category . ' c', 'd.category_id = c.id', 'inner');
        $this->db->where('d.status', 1);
        $this->db->where('c.status', 1);

        $query = $this->db->get();
        $result = $query->row();

        return $result ? (int) $result->total : 0;
    }

    /**
     * นับจำนวน Dataset ในหมวดหมู่
     */
    public function count_datasets_by_category($category_id)
    {
        $this->db->where('category_id', $category_id);
        $this->db->where('status', 1);
        return $this->db->count_all_results($this->table_catalog);
    }

    /**
     * ดึงวันที่อัปเดตล่าสุด
     */
    public function get_last_updated()
    {
        $this->db->select('updated_at');
        $this->db->from($this->table_catalog);
        $this->db->where('status', 1);
        $this->db->order_by('updated_at', 'DESC');
        $this->db->limit(1);
        $result = $this->db->get()->row();
        return $result ? $result->updated_at : null;
    }

    /**
     * เพิ่มจำนวนการเข้าชม (ในตาราง catalog สำหรับแสดงผล)
     */
    public function increment_view($dataset_id)
    {
        $this->db->set('views', 'views + 1', FALSE);
        $this->db->where('id', $dataset_id);
        return $this->db->update($this->table_catalog);
    }

    /**
     * ดึงข้อมูลทั้งหมดสำหรับ Export (ดึง views จาก log จริง) ⭐
     */
    public function get_all_datasets_for_export()
    {
        $this->db->select('
            d.id,
            d.dataset_name as "ชื่อชุดข้อมูล",
            d.dataset_name_en as "Dataset Name",
            c.category_name as "หมวดหมู่",
            d.description as "คำอธิบาย",
            d.table_name as "ชื่อตาราง",
            d.data_format as "รูปแบบข้อมูล",
            d.data_source as "แหล่งข้อมูล",
            d.responsible_person as "ผู้รับผิดชอบ",
            d.contact_email as "อีเมล",
            d.update_frequency as "ความถี่การอัปเดต",
            d.last_updated as "อัปเดตล่าสุด",
            d.keywords as "คำสำคัญ",
            d.license as "ลิขสิทธิ์",
            COUNT(v.id) as "จำนวนเข้าชม",
            d.created_at as "วันที่สร้าง"
        ');
        $this->db->from($this->table_catalog . ' d');
        $this->db->join($this->table_category . ' c', 'd.category_id = c.id', 'left');
        $this->db->join($this->table_views_log . ' v', 'd.id = v.dataset_id', 'left');
        $this->db->where('d.status', 1);
        $this->db->group_by('d.id');
        $this->db->order_by('c.sort_order', 'ASC');
        $this->db->order_by('d.dataset_name', 'ASC');
        return $this->db->get();
    }

    /**
     * ดึงข้อมูลตำแหน่ง/หน่วยงานจาก tbl_position
     */
    public function get_positions()
    {
        $fields = $this->db->list_fields('tbl_position');

        $name_field = '';
        $possible_names = ['position_name', 'name', 'pos_name', 'position', 'title'];

        foreach ($possible_names as $field) {
            if (in_array($field, $fields)) {
                $name_field = $field;
                break;
            }
        }

        if (empty($name_field)) {
            foreach ($fields as $field) {
                if (stripos($field, 'name') !== false) {
                    $name_field = $field;
                    break;
                }
            }
        }

        if (empty($name_field)) {
            return array();
        }

        $this->db->select("pid, {$name_field} as position_name");
        $this->db->from('tbl_position');
        $this->db->where('pid >=', 4);
        $this->db->order_by('pid', 'ASC');
        $query = $this->db->get();
        return $query->result();
    }

    /**
     * ดึงรายชื่อตารางที่มีในหมวดหมู่ที่เลือก
     */
    public function get_tables_by_category($category_id = null)
    {
        $this->db->distinct();
        $this->db->select('table_name');
        $this->db->from($this->table_catalog);

        if (!empty($category_id)) {
            $this->db->where('category_id', $category_id);
        }

        $this->db->where('table_name IS NOT NULL');
        $this->db->where('table_name !=', '');
        $this->db->order_by('table_name', 'ASC');

        $query = $this->db->get();
        $results = $query->result();

        foreach ($results as $row) {
            if (strpos($row->table_name, '.') !== false) {
                $parts = explode('.', $row->table_name);
                $row->table_name = end($parts);
            }
        }

        return $results;
    }

    /**
     * ดึงรายชื่อตารางทั้งหมดจากฐานข้อมูล
     */
    public function get_all_database_tables()
    {
        $tables = $this->db->list_tables();

        $result = array();
        foreach ($tables as $table) {
            $clean_table = $table;
            if (strpos($table, '.') !== false) {
                $parts = explode('.', $table);
                $clean_table = end($parts);
            }

            if (strpos($clean_table, 'tbl_') === 0) {
                $result[] = (object) ['table_name' => $clean_table];
            }
        }

        return $result;
    }

    /**
     * ดึงโครงสร้างคอลัมน์จากตาราง
     */
    public function get_table_columns($table_name)
    {
        if (empty($table_name)) {
            return array();
        }

        if (strpos($table_name, '.') !== false) {
            $parts = explode('.', $table_name);
            $table_name = end($parts);
        }

        if (!$this->db->table_exists($table_name)) {
            return array();
        }

        $fields = $this->db->field_data($table_name);

        $result = array();
        foreach ($fields as $field) {
            $result[] = (object) [
                'field_name' => $field->name,
                'field_type' => $this->format_field_type($field),
                'field_description' => ''
            ];
        }

        return $result;
    }

    /**
     * ดึงสถิติการกระจายตามหมวดหมู่ (สำหรับ Statistics)
     */
    public function get_category_statistics()
    {
        $this->db->select('
            c.id,
            c.category_name,
            c.icon,
            c.color,
            COUNT(CASE WHEN d.status = 1 THEN d.id END) as count
        ');
        $this->db->from($this->table_category . ' c');
        $this->db->join($this->table_catalog . ' d', 'c.id = d.category_id', 'left');
        $this->db->where('c.status', 1);
        $this->db->group_by('c.id');
        $this->db->having('count >', 0);
        $this->db->order_by('count', 'DESC');

        $query = $this->db->get();
        return $query->result();
    }

    /**
     * จัดรูปแบบชนิดข้อมูล
     */
    private function format_field_type($field)
    {
        $type = strtoupper($field->type);

        if (!empty($field->max_length) && $field->max_length > 0) {
            $type .= '(' . $field->max_length . ')';
        }

        return $type;
    }


    /**
     * ดึงข้อมูลจากตารางตาม dataset_id และจำกัดจำนวนตาม record_count
     * เรียงลำดับจากล่าสุดย้อนหลัง
     */
    public function get_table_data_with_limit($table_name, $record_count)
    {
        // ตรวจสอบว่าตารางมีอยู่จริง
        if (!$this->db->table_exists($table_name)) {
            return array();
        }

        // ดึงชื่อคอลัมน์ primary key หรือคอลัมน์แรก
        $fields = $this->db->field_data($table_name);
        $order_by = !empty($fields) ? $fields[0]->name : 'id';

        // สร้าง query
        $this->db->from($table_name);
        $this->db->order_by($order_by, 'DESC'); // เรียงจากล่าสุด

        // จำกัดจำนวนถ้าไม่ใช่ UNLIMIT (-1)
        if ($record_count > 0) {
            $this->db->limit($record_count);
        }

        return $this->db->get()->result_array();
    }

    /**
     * ดึงข้อมูลจากตารางพร้อมจำกัดจำนวนตาม record_count
     * เรียงลำดับจากล่าสุดย้อนหลัง
     */
    public function get_table_data_limited($table_name, $select_fields, $record_count, $offset = 0)
    {
        // ตรวจสอบว่าตารางมีอยู่จริง
        if (!$this->db->table_exists($table_name)) {
            return array();
        }

        // SELECT เฉพาะฟิลด์ที่ระบุ
        $this->db->select(implode(', ', $select_fields));

        // หาคอลัมน์แรก (มักเป็น primary key) สำหรับ ORDER BY
        $fields = $this->db->field_data($table_name);
        $primary_key = !empty($fields) ? $fields[0]->name : 'id';

        // เรียงจากล่าสุดย้อนหลัง
        $this->db->order_by($primary_key, 'DESC');

        // จำกัดจำนวน
        // -1 = UNLIMIT (ไม่จำกัด)
        // 0 = ใช้ค่า default (100)
        // > 0 = ใช้ค่าที่กำหนด
        if ($record_count == -1) {
            // ไม่จำกัด - ไม่ใส่ limit()
        } elseif ($record_count > 0) {
            $this->db->limit($record_count, $offset);
        } else {
            // default 100
            $this->db->limit(100, $offset);
        }

        $query = $this->db->get($table_name);
        return $query->result_array();
    }

}