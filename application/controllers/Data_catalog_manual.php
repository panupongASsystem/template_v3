<?php
defined('BASEPATH') or exit('No direct script access allowed');

class Data_catalog_manual extends CI_Controller
{
    public function __construct()
    {
        parent::__construct();

        // เช็ค login
        if (!$this->session->userdata('m_id')) {
            redirect('User/logout', 'refresh');
        }

        // ✅ เช็คว่า user มีอยู่ในตาราง tbl_member หรือไม่
        $user_exists = $this->db->where('m_id', $this->session->userdata('m_id'))
            ->where('m_status', '1')
            ->count_all_results('tbl_member');

        if ($user_exists == 0) {
            redirect('User/logout', 'refresh');
        }

        // ✅ เช็คสิทธิ์การเข้าถึง Data Catalog Manual
        if (!$this->check_catalog_manual_access()) {
            show_404();
        }

        // โหลด models และ libraries ที่จำเป็น
        $this->load->model('Data_catalog_model');
        $this->load->library('form_validation');
    }

    /**
     * เช็คสิทธิ์การเข้าถึง Data Catalog Manual
     * เฉพาะ grant_system_ref_id มี 1 หรือ 2
     */
    private function check_catalog_manual_access()
    {
        $user_id = $this->session->userdata('m_id');

        $user = $this->db->select('grant_system_ref_id')
            ->where('m_id', $user_id)
            ->where('m_status', '1')
            ->get('tbl_member')
            ->row();

        if (!$user) {
            return false;
        }

        // แปลง grant_system_ref_id เป็น array
        $grants = explode(',', $user->grant_system_ref_id);

        // เช็คว่ามี 1 หรือ 2 หรือไม่
        return (in_array('1', $grants) || in_array('2', $grants));
    }

    /**
     * หน้าแรก - รายการ Dataset ทั้งหมด
     */
    public function index()
    {
        $data['page_title'] = 'จัดการข้อมูล Data Catalog';
        $data['datasets'] = $this->Data_catalog_model->get_all_datasets_for_management();
        $data['categories'] = $this->Data_catalog_model->get_all_categories();
        $data['user_info'] = $this->get_user_info();

        $this->load->view('data_catalog/index_manual', $data);
    }

    /**
     * หน้าเพิ่มข้อมูลใหม่
     */
    public function add()
    {
        $data['page_title'] = 'เพิ่มชุดข้อมูลใหม่';
        $data['categories'] = $this->Data_catalog_model->get_all_categories();
        $data['positions'] = $this->Data_catalog_model->get_positions();
        $data['tables'] = $this->Data_catalog_model->get_all_database_tables();
        $data['mode'] = 'add';
        $data['metadata'] = array();

        $this->load->view('data_catalog/form_manual', $data);
    }

    /**
     * หน้าแก้ไขข้อมูล
     */
    public function edit($dataset_id)
    {
        $dataset = $this->Data_catalog_model->get_dataset_by_id($dataset_id);

        if (!$dataset) {
            show_404();
        }

        $data['page_title'] = 'แก้ไขชุดข้อมูล';
        $data['dataset'] = $dataset;
        $data['categories'] = $this->Data_catalog_model->get_all_categories();
        $data['positions'] = $this->Data_catalog_model->get_positions();

        // ✅ เปลี่ยนจาก get_tables_by_category เป็น get_all_database_tables
        $data['tables'] = $this->Data_catalog_model->get_all_database_tables();

        $data['metadata'] = $this->Data_catalog_model->get_dataset_metadata($dataset_id);
        $data['mode'] = 'edit';

        $this->load->view('data_catalog/form_manual', $data);
    }

    /**
     * บันทึกข้อมูล (เพิ่ม/แก้ไข)
     */
    public function save()
    {
        // Validation rules
        $this->form_validation->set_rules('category_id', 'หมวดหมู่', 'required|integer');
        $this->form_validation->set_rules('dataset_name', 'ชื่อชุดข้อมูล', 'required|max_length[300]');
        $this->form_validation->set_rules('description', 'คำอธิบาย', 'max_length[1000]');
        $this->form_validation->set_rules('access_level', 'ระดับการเข้าถึง', 'required|in_list[public,restricted,private]');

        if ($this->form_validation->run() == FALSE) {
            echo json_encode([
                'success' => false,
                'message' => validation_errors()
            ]);
            return;
        }

        $dataset_id = $this->input->post('dataset_id');
        $table_changed = $this->input->post('table_changed') === 'true';

        // เตรียมข้อมูล dataset
        $data = [
            'category_id' => $this->input->post('category_id'),
            'dataset_name' => $this->input->post('dataset_name'),
            'dataset_name_en' => $this->input->post('dataset_name_en'),
            'description' => $this->input->post('description'),
            'table_name' => $this->input->post('table_name'),
            'data_source' => $this->input->post('data_source'),
            'data_format' => $this->input->post('data_format') ?: 'Database',
            'access_level' => $this->input->post('access_level'),
            'responsible_department' => $this->input->post('responsible_department'),
            'responsible_person' => $this->input->post('responsible_person'),
            'contact_email' => $this->input->post('contact_email'),
            'contact_phone' => $this->input->post('contact_phone'),
            'keywords' => $this->input->post('keywords'),
            'license' => $this->input->post('license'),
            'update_frequency' => $this->input->post('update_frequency'),
            'record_count' => $this->input->post('record_count') ?: 100,
            'last_updated' => $this->input->post('last_updated') ?: date('Y-m-d'),
            'download_url' => $this->input->post('download_url'),
            'api_endpoint' => $this->input->post('api_endpoint'),
            'status' => 1
        ];

        if ($dataset_id) {
            // ========== โหมดแก้ไข ==========

            // อัปเดตข้อมูล dataset
            $result = $this->Data_catalog_model->update_dataset($dataset_id, $data);

            // Auto-generate URLs ถ้ายังไม่มี
            if (empty($data['download_url']) || empty($data['api_endpoint'])) {
                $auto_urls = [
                    'download_url' => "api_data_catalog/download/{$dataset_id}",
                    'api_endpoint' => "api_data_catalog/dataset/{$dataset_id}"
                ];
                $this->Data_catalog_model->update_dataset($dataset_id, $auto_urls);
            }

            // ดึงข้อมูล metadata จาก POST
            $field_names = $this->input->post('field_name');
            $field_names_en = $this->input->post('field_name_en');
            $field_types = $this->input->post('field_type');
            $field_descriptions = $this->input->post('field_description');

            // Log เพื่อ debug
            log_message('debug', 'POST field_name: ' . json_encode($field_names));
            log_message('debug', 'POST field_name_en: ' . json_encode($field_names_en));
            log_message('debug', 'POST field_type: ' . json_encode($field_types));

            // เตรียม metadata array
            $metadata_array = [];

            // ตรวจสอบว่ามีข้อมูลส่งมาหรือไม่
            if (is_array($field_names)) {
                foreach ($field_names as $index => $field_name) {
                    // กรองเฉพาะที่มีค่าและไม่ว่างเปล่า
                    if (!empty($field_name) && trim($field_name) !== '') {
                        $metadata_array[] = [
                            'field_name' => trim($field_name),
                            'field_name_en' => isset($field_names_en[$index]) ? trim($field_names_en[$index]) : '',
                            'field_type' => isset($field_types[$index]) ? trim($field_types[$index]) : '',
                            'field_description' => isset($field_descriptions[$index]) ? trim($field_descriptions[$index]) : ''
                        ];
                    }
                }
            }

            // Log metadata ที่จะบันทึก
            log_message('debug', 'Metadata array prepared: ' . json_encode($metadata_array));
            log_message('debug', 'Metadata count: ' . count($metadata_array));

            // บันทึก metadata (เรียกเสมอ แม้ array ว่าง เพื่อลบของเก่า)
            $saved_count = $this->Data_catalog_model->save_metadata_batch($dataset_id, $metadata_array);

            log_message('info', "Dataset {$dataset_id}: Saved {$saved_count} metadata records");

            echo json_encode([
                'success' => $result,
                'message' => $result ? 'แก้ไขข้อมูลสำเร็จ' : 'เกิดข้อผิดพลาด',
                'dataset_id' => $dataset_id,
                'metadata_count' => $saved_count
            ]);

        } else {
            // ========== โหมดเพิ่มใหม่ ==========

            $dataset_id = $this->Data_catalog_model->insert_dataset($data);

            if ($dataset_id > 0) {
                // Auto-generate URLs
                $auto_urls = [
                    'download_url' => "api_data_catalog/download/{$dataset_id}",
                    'api_endpoint' => "api_data_catalog/dataset/{$dataset_id}"
                ];
                $this->Data_catalog_model->update_dataset($dataset_id, $auto_urls);

                // ดึงข้อมูล metadata
                $field_names = $this->input->post('field_name');
                $field_names_en = $this->input->post('field_name_en');
                $field_types = $this->input->post('field_type');
                $field_descriptions = $this->input->post('field_description');

                // เตรียม metadata array
                $metadata_array = [];

                if (is_array($field_names)) {
                    foreach ($field_names as $index => $field_name) {
                        if (!empty($field_name) && trim($field_name) !== '') {
                            $metadata_array[] = [
                                'field_name' => trim($field_name),
                                'field_name_en' => isset($field_names_en[$index]) ? trim($field_names_en[$index]) : '',
                                'field_type' => isset($field_types[$index]) ? trim($field_types[$index]) : '',
                                'field_description' => isset($field_descriptions[$index]) ? trim($field_descriptions[$index]) : ''
                            ];
                        }
                    }
                }

                // บันทึก metadata
                $saved_count = 0;
                if (count($metadata_array) > 0) {
                    $saved_count = $this->Data_catalog_model->save_metadata_batch($dataset_id, $metadata_array);
                }

                log_message('info', "New dataset {$dataset_id}: Saved {$saved_count} metadata records");
            }

            echo json_encode([
                'success' => ($dataset_id > 0),
                'message' => ($dataset_id > 0) ? 'เพิ่มข้อมูลสำเร็จ' : 'เกิดข้อผิดพลาด',
                'dataset_id' => $dataset_id,
                'metadata_count' => isset($saved_count) ? $saved_count : 0
            ]);
        }
    }

    /**
     * ลบข้อมูล
     */
    public function delete($dataset_id)
    {
        $result = $this->Data_catalog_model->delete_dataset($dataset_id);

        echo json_encode([
            'success' => $result,
            'message' => $result ? 'ลบข้อมูลสำเร็จ' : 'เกิดข้อผิดพลาด'
        ]);
    }

    /**
     * เปลี่ยนสถานะ (เปิด/ปิด)
     */
    public function toggle_status($dataset_id)
    {
        $dataset = $this->Data_catalog_model->get_dataset_by_id($dataset_id);

        if (!$dataset) {
            echo json_encode([
                'success' => false,
                'message' => 'ไม่พบข้อมูล'
            ]);
            return;
        }

        $new_status = ($dataset->status == 1) ? 0 : 1;
        $result = $this->Data_catalog_model->update_dataset($dataset_id, ['status' => $new_status]);

        echo json_encode([
            'success' => $result,
            'message' => $result ? 'เปลี่ยนสถานะสำเร็จ' : 'เกิดข้อผิดพลาด',
            'new_status' => $new_status
        ]);
    }

    /**
     * AJAX: ดึงรายชื่อตารางตามหมวดหมู่
     */
    public function get_tables_ajax()
    {
        $category_id = $this->input->post('category_id');

        if (empty($category_id)) {
            // ถ้าไม่มีหมวดหมู่ ให้ดึงตารางทั้งหมดจาก database
            $tables = $this->Data_catalog_model->get_all_database_tables();
        } else {
            // ถ้ามีหมวดหมู่ ให้ดึงตารางในหมวดนั้น
            $tables = $this->Data_catalog_model->get_tables_by_category($category_id);

            // ถ้าไม่มีข้อมูลในหมวดนี้ ให้ดึงตารางทั้งหมดจาก database
            if (empty($tables)) {
                $tables = $this->Data_catalog_model->get_all_database_tables();
            }
        }

        echo json_encode([
            'success' => true,
            'tables' => $tables
        ]);
    }

    /**
     * AJAX: ดึงโครงสร้างคอลัมน์จากตาราง
     */
    public function get_table_columns_ajax()
    {
        $table_name = $this->input->post('table_name');

        if (empty($table_name)) {
            echo json_encode([
                'success' => false,
                'message' => 'กรุณาระบุชื่อตาราง'
            ]);
            return;
        }

        $columns = $this->Data_catalog_model->get_table_columns($table_name);

        echo json_encode([
            'success' => true,
            'columns' => $columns
        ]);
    }

    /**
     * ดึงข้อมูลผู้ใช้
     */
    private function get_user_info()
    {
        $user_id = $this->session->userdata('m_id');

        return $this->db->select('m_id, m_fname, m_lname, m_email, grant_system_ref_id')
            ->where('m_id', $user_id)
            ->get('tbl_member')
            ->row();
    }
}