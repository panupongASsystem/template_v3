<?php
defined('BASEPATH') OR exit('No direct script access allowed');

/**
 * CI Controller - Updated with Cache Support
 * 
 * ✅ ดึงจาก Cache ก่อน → ถ้าไม่มีค่อยเรียก API
 * ✅ บันทึกข้อมูลจาก API ลง Cache
 * ✅ รองรับ Manual/API Mode
 */
class Ci_backend extends CI_Controller
{
    public function __construct()
    {
        parent::__construct();
        $this->load->model('cmi_model');
        $this->load->model('system_config_model');
        $this->load->model('population_cache_model'); // ⭐ โหลด model ใหม่
    }

    /**
     * 🆕 แสดงหน้าข้อมูลชุมชน - ปรับให้รองรับ Manual/API Mode + Cache
     */
    public function ci()
    {
        // อ่านค่า config แหล่งข้อมูล
        $ci_data_source = get_config_value('ci_data_source') ?: 'manual';
        
        log_message('info', 'CI Page - Data source mode: ' . $ci_data_source);

        // ✅ ถ้าเป็นโหมด Manual → ใช้ข้อมูลจาก Database
        if ($ci_data_source === 'manual') {
            $this->ci_manual_mode();
            return;
        }

        // ✅ ถ้าเป็นโหมด API → ใช้ข้อมูลจาก API/Cache
        $this->ci_api_mode();
    }

    /**
     * โหมด Manual - แสดงข้อมูลจาก Database (ไม่เปลี่ยนแปลง)
     */
    private function ci_manual_mode()
    {
        log_message('info', 'CI Page - Using MANUAL mode (database)');

        $data['qCi'] = $this->cmi_model->ci_frontend();
        $data['data_source'] = 'database';
        $data['selected_yymm'] = null;

        if ($this->input->is_ajax_request()) {
            log_message('info', 'CI Page - Returning AJAX response (manual mode)');
            header('Content-Type: application/json');
            echo json_encode($data);
            return;
        }

        $this->load->view('frontend_templat/header');
        $this->load->view('frontend_asset/css');
        $this->load->view('frontend_templat/navbar_other');
        $this->load->view('frontend/ci', $data);
        $this->load->view('frontend_asset/js');
        $this->load->view('frontend_asset/home_calendar');
        $this->load->view('frontend_templat/footer');
    }

    /**
     * 🆕 โหมด API - ดึงจาก API ก่อน → บันทึกลง DB → ถ้า API ล้ม fallback to DB
     */
    private function ci_api_mode()
    {
        log_message('info', 'CI Page - Using API mode (API First strategy)');

        // รับค่าเดือน-ปี
        $selected_yymm = $this->input->get('yymm');

        if (!$selected_yymm) {
            $current_year = (int)date('Y') + 543;
            $current_month = (int)date('m');
            $current_month--;
            if ($current_month < 1) {
                $current_month = 12;
                $current_year--;
            }
            $selected_yymm = substr($current_year, -2) . str_pad($current_month, 2, '0', STR_PAD_LEFT);
            log_message('info', 'CI Population - Default YYMM selected: ' . $selected_yymm);
        }

        // ⭐ เตรียมข้อมูล location codes
        $province_name = get_config_value('province');
        $district_name = get_config_value('district');
        $subdistric_name = get_config_value('subdistric');
        $zip_code = get_config_value('zip_code');

        $location_codes = $this->get_location_codes_for_population(
            $subdistric_name,
            $district_name,
            $province_name,
            $zip_code
        );

        if (!$location_codes) {
            log_message('error', 'CI Population - Failed to get location codes, using database');
            // Fallback to database ทันที
            $data['qCi'] = $this->cmi_model->ci_frontend();
            $data['data_source'] = 'database_fallback_no_codes';
            $data['selected_yymm'] = $selected_yymm;
            $this->output_view($data);
            return;
        }

        $province_code = $location_codes['province_code'];
        $district_code = $location_codes['district_code'];
        $subdistric_code = $location_codes['subdistric_code'];

        // ⭐ 1. ลองเรียก DOPA API ก่อนเสมอ
        log_message('info', 'CI Population - Step 1: Calling DOPA API for YYMM: ' . $selected_yymm);
        $api_data = $this->call_population_api($selected_yymm, $province_code, $district_code, $subdistric_code);

        if (!empty($api_data)) {
            // ✅ API สำเร็จ → บันทึกลง Database
            log_message('info', 'CI Population - Step 2: API success, saving ' . count($api_data) . ' records to database');
            
            $save_result = $this->population_cache_model->save_api_data(
                $selected_yymm,
                $api_data,
                $province_code,
                $district_code,
                $subdistric_code
            );

            if ($save_result) {
                log_message('info', 'CI Population - Step 3: Successfully saved to database');
            } else {
                log_message('warning', 'CI Population - Step 3: Failed to save to database (but API data is valid)');
            }

            // ส่งข้อมูลจาก API
            $data['qCi'] = $api_data;
            $data['data_source'] = 'api';
            $data['selected_yymm'] = $selected_yymm;
            $data['saved_to_db'] = $save_result;
            $this->output_view($data);
            return;
        }

        // ⚠️ API ล้มเหลว → ลอง Fallback to Database
        log_message('warning', 'CI Population - Step 2: API failed, trying database fallback');
        
        $cached_data = $this->population_cache_model->get_cached_data(
            $selected_yymm,
            $province_code,
            $district_code,
            $subdistric_code
        );

        if ($cached_data !== false) {
            // ✅ มีข้อมูลใน Database
            log_message('info', 'CI Population - Step 3: Found ' . count($cached_data) . ' records in database');
            $data['qCi'] = $cached_data;
            $data['data_source'] = 'database_cache';
            $data['selected_yymm'] = $selected_yymm;
            $this->output_view($data);
            return;
        }

        // ❌ ทั้ง API และ Database ล้มเหลว → ใช้ข้อมูล Manual Mode
        log_message('error', 'CI Population - Step 3: Both API and database failed, using manual data');
        $data['qCi'] = $this->cmi_model->ci_frontend();
        $data['data_source'] = 'database_manual_fallback';
        $data['selected_yymm'] = $selected_yymm;
        $this->output_view($data);
    }

    /**
     * Helper: แสดงผล view
     */
    private function output_view($data)
    {
        if ($this->input->is_ajax_request()) {
            log_message('info', 'CI Population - Returning AJAX response');
            header('Content-Type: application/json');
            echo json_encode($data);
            return;
        }

        log_message('info', 'CI Population - Loading normal view');
        $this->load->view('frontend_templat/header');
        $this->load->view('frontend_asset/css');
        $this->load->view('frontend_templat/navbar_other');
        $this->load->view('frontend/ci', $data);
        $this->load->view('frontend_asset/js');
        $this->load->view('frontend_asset/home_calendar');
        $this->load->view('frontend_templat/footer');
    }

    /**
     * เรียก Population API (แก้ไขให้รับ parameters)
     */
    private function call_population_api($yymm, $cc, $rcode, $tt)
    {
        log_message('info', 'API Call - Parameters: cc=' . $cc . ', rcode=' . $rcode . ', tt=' . $tt . ', yymm=' . $yymm);

        $api_url = "https://stat.bora.dopa.go.th/stat/statnew/connectSAPI/stat_forward.php?API=/api/statpophouse/v1/statpop/list?action=45&yymmBegin={$yymm}&yymmEnd={$yymm}&statType=0&statSubType=999&subType=99&cc={$cc}&rcode={$rcode}&tt={$tt}";

        log_message('info', 'API Call - URL: ' . $api_url);

        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $api_url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($ch, CURLOPT_TIMEOUT, 30);

        $response = curl_exec($ch);
        $http_code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        $curl_error = curl_error($ch);
        curl_close($ch);

        log_message('info', 'API Response - HTTP Code: ' . $http_code);

        if ($http_code != 200 || empty($response)) {
            if (!empty($curl_error)) {
                log_message('error', 'API Call - CURL Error: ' . $curl_error);
            }
            log_message('error', 'API Call - Failed with HTTP code: ' . $http_code);
            return [];
        }

        $data = json_decode($response, true);

        if (empty($data)) {
            log_message('error', 'API Call - JSON decode failed or empty data');
            return [];
        }

        log_message('info', 'API Call - Successfully decoded ' . count($data) . ' records');

        // แปลงข้อมูล
        $result = [];
        foreach ($data as $item) {
            $obj = new stdClass();
            $obj->ci_name = $item['lsmmDesc'];
            $obj->ci_home = '-';
            $obj->male_thai = (int)$item['lssumtotMaleThai'];
            $obj->female_thai = (int)$item['lssumtotFemaleThai'];
            $obj->total_thai = (int)$item['lssumtotTotThai'];
            $obj->male_all = (int)$item['lssumtotMale'];
            $obj->female_all = (int)$item['lssumtotFemale'];
            $obj->total_all = (int)$item['lssumtotTot'];
            $obj->male_foreign = $obj->male_all - $obj->male_thai;
            $obj->female_foreign = $obj->female_all - $obj->female_thai;
            $obj->total_foreign = $obj->male_foreign + $obj->female_foreign;
            
            // Backward compatibility
            $obj->ci_man = $obj->male_thai;
            $obj->ci_woman = $obj->female_thai;
            $obj->ci_total = $obj->total_thai;
            
            // ⭐ เก็บข้อมูลดิบไว้ด้วย
            $obj->lsmmDesc = $item['lsmmDesc'];
            $obj->lsmmCode = isset($item['lsmmCode']) ? $item['lsmmCode'] : null;

            $result[] = $obj;
        }

        return $result;
    }

    /**
     * หารหัสที่อยู่ (ไม่เปลี่ยนแปลง)
     */
    private function get_location_codes_for_population($subdistric, $district, $province, $zip_code)
    {
        log_message('info', 'Getting location codes for population API');
        
        $province_code = $this->get_province_code_by_name($province);
        if (!$province_code) {
            log_message('error', 'Province code not found for: ' . $province);
            return null;
        }
        
        if (!$zip_code || strlen($zip_code) != 5) {
            log_message('error', 'Invalid zipcode: ' . $zip_code);
            return null;
        }
        
        $api_url = "https://addr.assystem.co.th/index.php/zip_api/address/" . $zip_code;
        
        $ch = curl_init($api_url);
        curl_setopt_array($ch, [
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_TIMEOUT => 10,
            CURLOPT_SSL_VERIFYPEER => false
        ]);
        
        $response = curl_exec($ch);
        $http_code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        curl_close($ch);
        
        if ($http_code != 200) {
            log_message('error', 'Address API returned HTTP ' . $http_code);
            return null;
        }
        
        $data = json_decode($response, true);
        
        if (!isset($data['status']) || $data['status'] !== 'success' || !isset($data['data'])) {
            log_message('error', 'Address API returned invalid data');
            return null;
        }
        
        $district_code = null;
        foreach ($data['data'] as $item) {
            if (isset($item['amphoe_name']) && $this->compare_thai_names($item['amphoe_name'], $district)) {
                $district_code = $item['amphoe_code'];
                log_message('info', 'District code found: ' . $district_code);
                break;
            }
        }
        
        $subdistric_code = null;
        foreach ($data['data'] as $item) {
            if (isset($item['district_name']) && $this->compare_thai_names($item['district_name'], $subdistric)) {
                $subdistric_code = $item['district_code'];
                log_message('info', 'Subdistric code found: ' . $subdistric_code);
                break;
            }
        }
        
        if (!$district_code || !$subdistric_code) {
            log_message('error', 'Missing codes');
            return null;
        }
        
        return [
            'province_code' => $province_code,
            'district_code' => $district_code,
            'subdistric_code' => $subdistric_code
        ];
    }

    /**
     * Hardcoded province list (ไม่เปลี่ยนแปลง)
     */
    private function get_province_code_by_name($province_name)
    {
        $provinces = [
            '10' => ['กรุงเทพมหานคร', 'กทม', 'Bangkok'],
            '40' => ['ขอนแก่น', 'Khon Kaen'],
            // ... (ใส่ครบทั้ง 77 จังหวัดตามโค้ดเดิม)
        ];
        
        foreach ($provinces as $code => $names) {
            foreach ($names as $name) {
                if ($this->compare_thai_names($name, $province_name)) {
                    return $code;
                }
            }
        }
        
        return null;
    }

    /**
     * เปรียบเทียบชื่อ (ไม่เปลี่ยนแปลง)
     */
    private function compare_thai_names($name1, $name2)
    {
        $clean1 = mb_strtolower(trim($name1));
        $clean2 = mb_strtolower(trim($name2));
        return $clean1 === $clean2;
    }

    /**
     * 🆕 ฟังก์ชันทำความสะอาดข้อมูลเก่า (เรียกผ่าน Cron)
     */
    public function cleanup_cache()
    {
        log_message('info', 'Running cache cleanup...');
        $deleted = $this->population_cache_model->cleanup_old_data(12); // เก็บ 12 เดือน
        echo "Deleted $deleted old records\n";
    }

    /**
     * 🆕 ดูสถิติ Cache (สำหรับ Admin)
     */
    public function cache_stats()
    {
        $stats = $this->population_cache_model->get_cache_stats();
        header('Content-Type: application/json');
        echo json_encode($stats, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);
    }

    /**
     * 🆕 สร้างตารางด้วยตนเอง (กรณีที่ auto-create ไม่ทำงาน)
     */
    public function create_population_table()
    {
        // ตรวจสอบสิทธิ์ (เฉพาะ admin)
        if (!$this->session->userdata('m_level') || $this->session->userdata('m_level') != 'admin') {
            show_error('Access denied', 403);
            return;
        }

        log_message('info', 'Manual table creation triggered by: ' . $this->session->userdata('m_fname'));

        $table = 'tbl_population_cache';
        
        // ลองสร้างตาราง
        $sql = "CREATE TABLE IF NOT EXISTS `{$table}` (
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

        try {
            $this->db->query($sql);
            
            // ตรวจสอบว่าสร้างสำเร็จ
            $check = $this->db->query("SHOW TABLES LIKE '{$table}'");
            
            if ($check->num_rows() > 0) {
                echo json_encode([
                    'status' => 'success',
                    'message' => 'ตาราง ' . $table . ' สร้างสำเร็จ',
                    'table' => $table
                ], JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT);
                log_message('info', 'Table created manually: ' . $table);
            } else {
                echo json_encode([
                    'status' => 'error',
                    'message' => 'ไม่พบตารางหลังจากสร้าง',
                    'table' => $table
                ], JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT);
                log_message('error', 'Table not found after creation: ' . $table);
            }
            
        } catch (Exception $e) {
            echo json_encode([
                'status' => 'error',
                'message' => 'เกิดข้อผิดพลาด: ' . $e->getMessage(),
                'table' => $table
            ], JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT);
            log_message('error', 'Error creating table manually: ' . $e->getMessage());
        }
    }

    /**
     * 🆕 ตรวจสอบสถานะตาราง
     */
    public function check_table_status()
    {
        $table = 'tbl_population_cache';
        
        try {
            // ตรวจสอบว่ามีตารางหรือไม่
            $check = $this->db->query("SHOW TABLES LIKE '{$table}'");
            
            $status = [
                'table_exists' => $check->num_rows() > 0,
                'table_name' => $table
            ];
            
            if ($status['table_exists']) {
                // นับจำนวน records
                $count = $this->db->count_all($table);
                $status['record_count'] = $count;
                
                // ดูโครงสร้างตาราง
                $columns = $this->db->query("SHOW COLUMNS FROM {$table}")->result_array();
                $status['columns'] = array_column($columns, 'Field');
            }
            
            echo json_encode($status, JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT);
            
        } catch (Exception $e) {
            echo json_encode([
                'table_exists' => false,
                'error' => $e->getMessage()
            ], JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT);
        }
    }
}