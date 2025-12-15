<?php
defined('BASEPATH') or exit('No direct script access allowed');

/**
 * API Data Catalog Controller
 * 
 * API สำหรับเข้าถึงข้อมูล Open Data ผ่าน Data Catalog
 * 
 * Endpoints:
 * - GET /api_data_catalog/datasets - รายการชุดข้อมูลทั้งหมด
 * - GET /api_data_catalog/dataset/{id} - ข้อมูลของชุดข้อมูล
 * - GET /api_data_catalog/categories - รายการหมวดหมู่
 * - GET /api_data_catalog/download/{id} - ดาวน์โหลดข้อมูล (CSV, JSON, Excel)
 */
class Api_data_catalog extends CI_Controller
{
    public function __construct()
    {
        parent::__construct();
        $this->load->model('Data_catalog_model');

        // Set JSON header
        header('Content-Type: application/json; charset=utf-8');
        header('Access-Control-Allow-Origin: *');
        header('Access-Control-Allow-Methods: GET, POST, OPTIONS');
        header('Access-Control-Allow-Headers: Content-Type');
    }

    /**
     * รายการชุดข้อมูลทั้งหมด
     * GET /api_data_catalog/datasets
     * 
     * Parameters:
     * - category_id (optional): กรองตามหมวดหมู่
     * - limit (optional): จำนวนรายการต่อหน้า (default: 20)
     * - offset (optional): เริ่มต้นที่รายการที่ (default: 0)
     * - search (optional): ค้นหาจากชื่อ
     */
    public function datasets()
    {
        try {
            $category_id = $this->input->get('category_id');
            $limit = $this->input->get('limit') ?: 20;
            $offset = $this->input->get('offset') ?: 0;
            $search = $this->input->get('search');

            // ตรวจสอบ limit ไม่เกิน 100
            if ($limit > 100) {
                $limit = 100;
            }

            // ดึงข้อมูล
            if (!empty($search)) {
                $datasets = $this->Data_catalog_model->search_datasets($search, $limit, $offset);
                $total = $this->Data_catalog_model->count_search_results($search);
            } elseif (!empty($category_id)) {
                $datasets = $this->Data_catalog_model->get_datasets_by_category($category_id, $limit, $offset);
                $total = $this->Data_catalog_model->count_datasets_by_category($category_id);
            } else {
                $datasets = $this->Data_catalog_model->get_all_datasets($limit, $offset);
                $total = $this->Data_catalog_model->count_all_datasets();
            }

            // Format output
            $result = [];
            foreach ($datasets as $dataset) {
                $result[] = [
                    'id' => $dataset->id,
                    'dataset_name' => $dataset->dataset_name,
                    'dataset_name_en' => $dataset->dataset_name_en,
                    'description' => $dataset->description,
                    'category' => $dataset->category_name,
                    'data_format' => $dataset->data_format,
                    'access_level' => $dataset->access_level,
                    'record_count' => $dataset->record_count,
                    'last_updated' => $dataset->last_updated,
                    'api_url' => base_url("api_data_catalog/dataset/{$dataset->id}"),
                    'download_url' => base_url("api_data_catalog/download/{$dataset->id}")
                ];
            }

            echo json_encode([
                'success' => true,
                'total' => $total,
                'count' => count($result),
                'limit' => (int) $limit,
                'offset' => (int) $offset,
                'data' => $result
            ], JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT);

        } catch (Exception $e) {
            $this->error_response($e->getMessage());
        }
    }

    /**
     * ข้อมูลของชุดข้อมูล
     * GET /api_data_catalog/dataset/{id}
     * 
     * Parameters:
     * - limit (optional): จำนวนแถวที่ต้องการ (default: ตาม record_count)
     * - offset (optional): เริ่มต้นที่แถวที่ (default: 0)
     */
    public function dataset($dataset_id)
    {
        try {
            if (empty($dataset_id)) {
                throw new Exception('กรุณาระบุ dataset_id');
            }

            // ดึงข้อมูล dataset
            $dataset = $this->Data_catalog_model->get_dataset_by_id($dataset_id);

            if (!$dataset) {
                throw new Exception('ไม่พบชุดข้อมูล');
            }

            // ตรวจสอบสิทธิ์การเข้าถึง
            if ($dataset->access_level == 'private') {
                // ต้อง login หรือมี API key
                if (!$this->check_api_access()) {
                    throw new Exception('ไม่มีสิทธิ์เข้าถึงข้อมูลนี้');
                }
            }

            // ดึง metadata
            $metadata = $this->Data_catalog_model->get_dataset_metadata($dataset_id);

            // ถ้าไม่มี metadata ให้แจ้งเตือน
            if (empty($metadata)) {
                throw new Exception('ชุดข้อมูลนี้ยังไม่ได้กำหนดโครงสร้างข้อมูล (Metadata)');
            }

            // เตรียมรายการฟิลด์ที่จะ SELECT
            $select_fields = [];
            $field_mapping = []; // map field_name => alias

            foreach ($metadata as $meta) {
                $field_name = $meta->field_name;
                $field_alias = !empty($meta->field_name_en) ? $meta->field_name_en : $field_name;

                $select_fields[] = $field_name;
                $field_mapping[$field_name] = $field_alias;
            }

            // ดึงข้อมูลจริงจากตาราง
            // ใช้ record_count จาก dataset เป็นตัวกำหนดจำนวนข้อมูลที่ส่งออก
            $dataset_limit = (int) $dataset->record_count;
            $offset = $this->input->get('offset') ?: 0;

            // กำหนด limit ตาม record_count
            if ($dataset_limit == -1) {
                // UNLIMIT - ใช้ parameter limit หรือไม่จำกัด
                $limit = $this->input->get('limit');
                if ($limit && $limit > 1000) {
                    $limit = 1000; // จำกัดสูงสุด 1000 เพื่อความปลอดภัย
                }
            } else {
                // จำกัดตาม record_count (100, 200, 500)
                $limit = $dataset_limit > 0 ? $dataset_limit : 100;
            }

            $table_name = $dataset->table_name;

            // ตรวจสอบว่าตารางมีอยู่จริง
            if (!$this->db->table_exists($table_name)) {
                throw new Exception('ไม่พบตารางข้อมูล');
            }

            // Query ข้อมูล - SELECT เฉพาะฟิลด์ที่มี metadata
            $this->db->select(implode(', ', $select_fields));

            // เรียงจากล่าสุดย้อนหลัง
            $fields = $this->db->field_data($table_name);
            $primary_key = !empty($fields) ? $fields[0]->name : 'id';
            $this->db->order_by($primary_key, 'DESC');

            // จำกัดจำนวนตาม limit
            if ($limit) {
                $this->db->limit($limit, $offset);
            }

            $query = $this->db->get($table_name);
            $data = $query->result_array();

            // แปลงชื่อฟิลด์เป็น alias
            $aliased_data = [];
            foreach ($data as $row) {
                $aliased_row = [];
                foreach ($row as $field => $value) {
                    $alias = $field_mapping[$field] ?? $field;
                    $aliased_row[$alias] = $value;
                }
                $aliased_data[] = $aliased_row;
            }

            // นับจำนวนทั้งหมด
            $total = $this->db->count_all($table_name);

            // Format metadata สำหรับ response
            $fields = [];
            foreach ($metadata as $meta) {
                $fields[] = [
                    'field_name' => !empty($meta->field_name_en) ? $meta->field_name_en : $meta->field_name,
                    'field_type' => $meta->field_type,
                    'field_description' => $meta->field_description
                ];
            }

            echo json_encode([
                'success' => true,
                'dataset' => [
                    'id' => $dataset->id,
                    'name' => $dataset->dataset_name,
                    'name_en' => $dataset->dataset_name_en,
                    'description' => $dataset->description,
                    'category' => $dataset->category_name ?? '',
                    'data_format' => $dataset->data_format,
                    'access_level' => $dataset->access_level,
                    'last_updated' => $dataset->last_updated,
                    'record_limit' => $dataset_limit == -1 ? 'UNLIMIT' : $dataset_limit
                ],
                'fields' => $fields,
                'total_records' => $total,
                'returned_records' => count($aliased_data),
                'limit' => $limit ? (int) $limit : 'UNLIMIT',
                'offset' => (int) $offset,
                'data' => $aliased_data
            ], JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT);

        } catch (Exception $e) {
            $this->error_response($e->getMessage());
        }
    }

    /**
     * รายการหมวดหมู่
     * GET /api_data_catalog/categories
     */
    public function categories()
    {
        try {
            $categories = $this->Data_catalog_model->get_all_categories();

            $result = [];
            foreach ($categories as $cat) {
                $result[] = [
                    'id' => $cat->id,
                    'name' => $cat->category_name,
                    'description' => $cat->description,
                    'icon' => $cat->icon,
                    'color' => $cat->color,
                    'dataset_count' => $cat->dataset_count
                ];
            }

            echo json_encode([
                'success' => true,
                'count' => count($result),
                'data' => $result
            ], JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT);

        } catch (Exception $e) {
            $this->error_response($e->getMessage());
        }
    }

    /**
     * ดาวน์โหลดข้อมูล
     * GET /api_data_catalog/download/{id}?format={csv|json|excel}
     * 
     * Parameters:
     * - format: รูปแบบไฟล์ (csv, json, excel) default: csv
     */
    public function download($dataset_id)
    {
        try {
            if (empty($dataset_id)) {
                throw new Exception('กรุณาระบุ dataset_id');
            }

            // ดึงข้อมูล dataset
            $dataset = $this->Data_catalog_model->get_dataset_by_id($dataset_id);

            if (!$dataset) {
                throw new Exception('ไม่พบชุดข้อมูล');
            }

            // ตรวจสอบสิทธิ์
            if ($dataset->access_level == 'private') {
                if (!$this->check_api_access()) {
                    throw new Exception('ไม่มีสิทธิ์เข้าถึงข้อมูลนี้');
                }
            }

            $table_name = $dataset->table_name;

            // ตรวจสอบว่าตารางมีอยู่จริง
            if (!$this->db->table_exists($table_name)) {
                throw new Exception('ไม่พบตารางข้อมูล');
            }

            // ดึง metadata
            $metadata = $this->Data_catalog_model->get_dataset_metadata($dataset_id);

            // ถ้าไม่มี metadata ให้แจ้งเตือน
            if (empty($metadata)) {
                throw new Exception('ชุดข้อมูลนี้ยังไม่ได้กำหนดโครงสร้างข้อมูล (Metadata)');
            }

            // เตรียมรายการฟิลด์ที่จะ SELECT
            $select_fields = [];
            $field_mapping = []; // map field_name => alias

            foreach ($metadata as $meta) {
                $field_name = $meta->field_name;
                $field_alias = !empty($meta->field_name_en) ? $meta->field_name_en : $field_name;

                $select_fields[] = $field_name;
                $field_mapping[$field_name] = $field_alias;
            }

            // Query ข้อมูล - SELECT เฉพาะฟิลด์ที่มี metadata
            $this->db->select(implode(', ', $select_fields));

            // เรียงจากล่าสุดย้อนหลัง
            $fields = $this->db->field_data($table_name);
            $primary_key = !empty($fields) ? $fields[0]->name : 'id';
            $this->db->order_by($primary_key, 'DESC');

            // จำกัดจำนวนตาม record_count
            $dataset_limit = (int) $dataset->record_count;
            if ($dataset_limit > 0) {
                // จำกัดตามที่กำหนด (100, 200, 500)
                $this->db->limit($dataset_limit);
            }
            // ถ้า -1 (UNLIMIT) จะไม่จำกัด

            $query = $this->db->get($table_name);
            $data = $query->result_array();

            if (empty($data)) {
                throw new Exception('ไม่มีข้อมูล');
            }

            // แปลงชื่อฟิลด์เป็น alias
            $aliased_data = [];
            foreach ($data as $row) {
                $aliased_row = [];
                foreach ($row as $field => $value) {
                    $alias = $field_mapping[$field] ?? $field;
                    $aliased_row[$alias] = $value;
                }
                $aliased_data[] = $aliased_row;
            }

            // รูปแบบการดาวน์โหลด
            $format = $this->input->get('format') ?: 'csv';

            switch ($format) {
                case 'json':
                    $this->download_json($aliased_data, $dataset->dataset_name);
                    break;

                case 'excel':
                    $this->download_excel($aliased_data, $dataset->dataset_name);
                    break;

                case 'csv':
                default:
                    $this->download_csv($aliased_data, $dataset->dataset_name);
                    break;
            }

        } catch (Exception $e) {
            header('Content-Type: application/json; charset=utf-8');
            $this->error_response($e->getMessage());
        }
    }
    
    /**
     * ดาวน์โหลดเป็น CSV
     */
    private function download_csv($data, $filename)
    {
        $filename = $this->sanitize_filename($filename) . '_' . date('Ymd_His') . '.csv';

        header('Content-Type: text/csv; charset=utf-8');
        header('Content-Disposition: attachment; filename="' . $filename . '"');
        header('Pragma: no-cache');
        header('Expires: 0');

        $output = fopen('php://output', 'w');

        // BOM สำหรับ UTF-8
        fprintf($output, chr(0xEF) . chr(0xBB) . chr(0xBF));

        // Header
        fputcsv($output, array_keys($data[0]));

        // Data
        foreach ($data as $row) {
            fputcsv($output, $row);
        }

        fclose($output);
    }

    /**
     * ดาวน์โหลดเป็น JSON
     */
    private function download_json($data, $filename)
    {
        $filename = $this->sanitize_filename($filename) . '_' . date('Ymd_His') . '.json';

        header('Content-Type: application/json; charset=utf-8');
        header('Content-Disposition: attachment; filename="' . $filename . '"');
        header('Pragma: no-cache');
        header('Expires: 0');

        echo json_encode([
            'success' => true,
            'count' => count($data),
            'exported_at' => date('Y-m-d H:i:s'),
            'data' => $data
        ], JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT);
    }

    /**
     * ดาวน์โหลดเป็น Excel
     */
    private function download_excel($data, $filename)
    {
        // Load PHPSpreadsheet (ถ้ามี) หรือใช้ simple HTML table
        $filename = $this->sanitize_filename($filename) . '_' . date('Ymd_His') . '.xls';

        header('Content-Type: application/vnd.ms-excel; charset=utf-8');
        header('Content-Disposition: attachment; filename="' . $filename . '"');
        header('Pragma: no-cache');
        header('Expires: 0');

        echo "\xEF\xBB\xBF"; // UTF-8 BOM
        echo '<html xmlns:x="urn:schemas-microsoft-com:office:excel">';
        echo '<head>';
        echo '<meta http-equiv="Content-Type" content="text/html; charset=UTF-8">';
        echo '</head>';
        echo '<body>';
        echo '<table border="1">';

        // Header
        echo '<tr>';
        foreach (array_keys($data[0]) as $header) {
            echo '<th>' . htmlspecialchars($header) . '</th>';
        }
        echo '</tr>';

        // Data
        foreach ($data as $row) {
            echo '<tr>';
            foreach ($row as $cell) {
                echo '<td>' . htmlspecialchars($cell) . '</td>';
            }
            echo '</tr>';
        }

        echo '</table>';
        echo '</body>';
        echo '</html>';
    }

    /**
     * ตรวจสอบสิทธิ์การเข้าถึง API
     */
    private function check_api_access()
    {
        // ตรวจสอบว่า login แล้วหรือไม่
        if ($this->session->userdata('m_id')) {
            return true;
        }

        // หรือตรวจสอบ API Key (ถ้ามี)
        $api_key = $this->input->get_request_header('X-API-Key');
        if ($api_key) {
            // ตรวจสอบ API Key กับ database
            // return $this->validate_api_key($api_key);
        }

        return false;
    }

    /**
     * ทำความสะอาดชื่อไฟล์
     */
    private function sanitize_filename($filename)
    {
        // แปลงภาษาไทยเป็นอังกฤษ (ถ้าต้องการ)
        $filename = preg_replace('/[^A-Za-z0-9_\-]/', '_', $filename);
        $filename = preg_replace('/_+/', '_', $filename);
        return trim($filename, '_');
    }

    /**
     * Response กรณี error
     */
    private function error_response($message, $code = 400)
    {
        http_response_code($code);
        echo json_encode([
            'success' => false,
            'error' => $message
        ], JSON_UNESCAPED_UNICODE);
    }
}