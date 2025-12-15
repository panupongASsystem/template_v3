<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Data_catalog extends CI_Controller {

    public function __construct() {
        parent::__construct();
        $this->load->model('Data_catalog_model');
		$this->load->model('System_config_model');
        $this->load->helper(['url', 'text', 'date']);
        $this->load->library(['pagination', 'session']);
    }

    /**
     * หน้าแรก Data Catalog
     */
    public function index() {
        // ดึงข้อมูล config ทั้งหมดจากระบบ
        $config = $this->System_config_model->get_all_config();
        
        // ดึงข้อมูลองค์กรจาก config ที่มีอยู่ในฐานข้อมูล
        // fname = ชื่อเต็มองค์กร (เช่น องค์การบริหารส่วนตำบลสว่าง)
        // abbreviation = คำย่อ (เช่น อบต. / เทศบาล)
        // nname = ชื่อย่อ (เช่น สว่าง)
        
        $data['org'] = array(
            'fname' => isset($config['fname']) ? $config['fname'] : 'องค์กรปกครองส่วนท้องถิ่น',
            'abbreviation' => isset($config['abbreviation']) ? $config['abbreviation'] : '',
            'nname' => isset($config['nname']) ? $config['nname'] : '',
            'address' => isset($config['address']) ? $config['address'] : '',
            'subdistric' => isset($config['subdistric']) ? $config['subdistric'] : '',
            'district' => isset($config['district']) ? $config['district'] : '',
            'province' => isset($config['province']) ? $config['province'] : '',
            'phone_1' => isset($config['phone_1']) ? $config['phone_1'] : '',
            'email_1' => isset($config['email_1']) ? $config['email_1'] : '',
        );
        
        // หรือใช้วิธีดึงแบบ keyword เฉพาะ
        // $org_name = $this->System_config_model->get_config_by_key('fname');
        // $data['org'] = array('fname' => $org_name ?: 'องค์กรปกครองส่วนท้องถิ่น');
        
        $data['page_title'] = 'บัญชีรายการข้อมูล (Data Catalog)';
        
        // ดึงข้อมูลสถิติ
        $data['total_datasets'] = $this->Data_catalog_model->count_total_datasets();
        $data['total_categories'] = $this->Data_catalog_model->count_total_categories();
        $data['last_updated'] = $this->Data_catalog_model->get_last_updated();
        
        // ดึงหมวดหมู่ทั้งหมด
        $data['categories'] = $this->Data_catalog_model->get_all_categories();
        
        // ดึง dataset ที่อัปเดตล่าสุด
        $data['recent_datasets'] = $this->Data_catalog_model->get_recent_datasets(6);
        
        // ดึง dataset ยอดนิยม
        $data['popular_datasets'] = $this->Data_catalog_model->get_popular_datasets(6);
        
        $this->load->view('data_catalog/index', $data);
    }

    /**
     * หน้าค้นหา
     */
    public function search() {
        // ดึงข้อมูลองค์กร
        $config = $this->System_config_model->get_all_config();
        $data['org'] = array(
            'fname' => isset($config['fname']) ? $config['fname'] : 'องค์กรปกครองส่วนท้องถิ่น',
            'abbreviation' => isset($config['abbreviation']) ? $config['abbreviation'] : ''
        );
        
        // รับค่าจากการค้นหา
        $search_query = $this->input->get('q', TRUE);
        $category_id = $this->input->get('category', TRUE);
        $format = $this->input->get('format', TRUE);
        $access_level = $this->input->get('access', TRUE);
        $sort = $this->input->get('sort', TRUE) ?: 'relevance';
        
        // Pagination
        $config_pagination = array(
            'base_url' => base_url('data_catalog/search'),
            'total_rows' => $this->Data_catalog_model->count_search_results(
                $search_query, 
                $category_id, 
                $format, 
                $access_level
            ),
            'per_page' => 12,
            'use_page_numbers' => TRUE,
            'reuse_query_string' => TRUE,
            'full_tag_open' => '<nav><ul class="pagination">',
            'full_tag_close' => '</ul></nav>',
            'first_link' => 'แรก',
            'last_link' => 'สุดท้าย',
            'next_link' => 'ถัดไป &raquo;',
            'prev_link' => '&laquo; ก่อนหน้า',
            'cur_tag_open' => '<li class="page-item active"><span class="page-link">',
            'cur_tag_close' => '</span></li>',
            'num_tag_open' => '<li class="page-item">',
            'num_tag_close' => '</li>',
            'prev_tag_open' => '<li class="page-item">',
            'prev_tag_close' => '</li>',
            'next_tag_open' => '<li class="page-item">',
            'next_tag_close' => '</li>',
            'first_tag_open' => '<li class="page-item">',
            'first_tag_close' => '</li>',
            'last_tag_open' => '<li class="page-item">',
            'last_tag_close' => '</li>',
            'attributes' => array('class' => 'page-link'),
        );
        
        $this->pagination->initialize($config_pagination);
        
        $page = $this->input->get('page', TRUE) ?: 1;
        $offset = ($page - 1) * $config_pagination['per_page'];
        
        // ค้นหาข้อมูล
        $data['datasets'] = $this->Data_catalog_model->search_datasets(
            $search_query,
            $category_id,
            $format,
            $access_level,
            $sort,
            $config_pagination['per_page'],
            $offset
        );
        
        $data['page_title'] = 'ค้นหา: ' . $search_query;
        $data['search_query'] = $search_query;
        $data['total_results'] = $config_pagination['total_rows'];
        $data['pagination'] = $this->pagination->create_links();
        
        // ข้อมูลสำหรับ Filter
        $data['categories'] = $this->Data_catalog_model->get_all_categories();
        $data['formats'] = array('Database', 'JSON', 'CSV', 'XML', 'Excel');

        
        $this->load->view('data_catalog/search', $data);
    }


    /**
     * หน้าสถิติ
     */
    /**
     * หน้าสถิติ (แก้ไขให้ดึงจาก log จริง) ⭐
     */
    public function statistics() {
        // ดึงข้อมูลองค์กร
        $config = $this->System_config_model->get_all_config();
        $data['org'] = array(
            'fname' => isset($config['fname']) ? $config['fname'] : 'องค์กรปกครองส่วนท้องถิ่น',
            'abbreviation' => isset($config['abbreviation']) ? $config['abbreviation'] : ''
        );
        
        $data['page_title'] = 'สถิติข้อมูล';
        
        // สถิติพื้นฐาน (ดึงจาก log จริง) ⭐
        $total_datasets = $this->Data_catalog_model->count_total_datasets();
        $total_categories = $this->Data_catalog_model->count_total_categories();
        $total_views = $this->Data_catalog_model->get_total_views(); // จาก views_log
        $total_downloads = $this->Data_catalog_model->get_total_downloads(); // จาก downloads_log
        
        // คำนวณ total_records จากทุก dataset ที่ status = 1
        $this->db->select('SUM(COALESCE(record_count, 0)) as total');
        $this->db->where('status', 1);
        $query = $this->db->get('tbl_data_catalog');
        $total_records = $query->row()->total ?? 0;
        
        // เตรียม array stats
        $data['stats'] = [
            'total_datasets' => $total_datasets,
            'total_categories' => $total_categories,
            'total_records' => $total_records,
            'total_views' => $total_views,
            'total_downloads' => $total_downloads
        ];
        
        // ใช้ข้อมูลเดิมด้วย (backward compatible)
        $data['total_datasets'] = $total_datasets;
        $data['total_categories'] = $total_categories;
        $data['total_views'] = $total_views;
        $data['total_downloads'] = $total_downloads;
        
        // Trends
        $data['datasets_trend'] = 5; // ตัวอย่าง
        $data['views_trend'] = 12; // ตัวอย่าง
        
        // สถิติแยกตามหมวดหมู่ (สำหรับ view)
        $data['category_stats'] = $this->Data_catalog_model->get_category_statistics();
        
        // สถิติแยกตามหมวดหมู่ (เดิม - เก็บไว้ backward compatible)
        $data['category_distribution'] = $this->Data_catalog_model->get_category_distribution();
        
        // ⭐ Top datasets (ดึงจาก log จริง)
        $data['top_datasets'] = $this->Data_catalog_model->get_popular_datasets_for_stats(10);
        
        // ⭐ สถิติการเข้าชมรายเดือน (ดึงจาก log จริง)
        $data['monthly_views'] = $this->Data_catalog_model->get_monthly_views(6);
        
        // ⭐ ชุดข้อมูลยอดนิยม (ดึงจาก log จริง)
        $data['popular_datasets'] = $this->Data_catalog_model->get_popular_datasets_for_stats(10);
        
        // สถิติระดับการเข้าถึง
        $data['access_stats'] = $this->Data_catalog_model->get_access_level_stats();
        
        // สถิติรูปแบบข้อมูล
        $data['format_stats'] = $this->Data_catalog_model->get_format_stats();
        
        // สถิติความถี่การอัปเดต
        $data['update_frequency_stats'] = $this->Data_catalog_model->get_update_frequency_stats();
        
        $this->load->view('data_catalog/statistics', $data);
    }

    /**
     * หน้าแสดงชุดข้อมูลทั้งหมด
     */
    public function all_datasets() {
        $data['page_title'] = 'ชุดข้อมูลทั้งหมด';
        
        // รับค่าตัวกรอง
        $selected_category = $this->input->get('category');
        $sort = $this->input->get('sort', TRUE) ?: 'newest';
        
        $data['selected_category'] = $selected_category;
        $data['sort'] = $sort;
        $data['categories'] = $this->Data_catalog_model->get_all_categories();
        
        // Pagination
        $config['base_url'] = base_url('data_catalog/all_datasets');
        $config['total_rows'] = $this->Data_catalog_model->count_all_datasets($selected_category);
        $config['per_page'] = 12;
        $config['uri_segment'] = 3;
        
        // Pagination config
        $config['full_tag_open'] = '<ul class="pagination">';
        $config['full_tag_close'] = '</ul>';
        $config['first_link'] = 'หน้าแรก';
        $config['last_link'] = 'หน้าสุดท้าย';
        $config['first_tag_open'] = '<li class="page-item">';
        $config['first_tag_close'] = '</li>';
        $config['prev_link'] = '&laquo;';
        $config['prev_tag_open'] = '<li class="page-item">';
        $config['prev_tag_close'] = '</li>';
        $config['next_link'] = '&raquo;';
        $config['next_tag_open'] = '<li class="page-item">';
        $config['next_tag_close'] = '</li>';
        $config['last_tag_open'] = '<li class="page-item">';
        $config['last_tag_close'] = '</li>';
        $config['cur_tag_open'] = '<li class="page-item active"><a class="page-link">';
        $config['cur_tag_close'] = '</a></li>';
        $config['num_tag_open'] = '<li class="page-item">';
        $config['num_tag_close'] = '</li>';
        $config['attributes'] = array('class' => 'page-link');
        
        $this->pagination->initialize($config);
        
        $page = ($this->uri->segment(3)) ? $this->uri->segment(3) : 0;
        $data['datasets'] = $this->Data_catalog_model->get_all_datasets_filtered($selected_category, $sort, $config['per_page'], $page);
        $data['total_datasets'] = $config['total_rows'];
        $data['pagination'] = $this->pagination->create_links();
        
        $this->load->view('data_catalog/all_datasets', $data);
    }

    /**
     * รายการ Dataset ตามหมวดหมู่
     */
   /**
     * หน้าหมวดหมู่
     */
    /**
     * หน้าหมวดหมู่
     */
    public function category($category_id) {
        // ดึงข้อมูลองค์กร
        $config = $this->System_config_model->get_all_config();
        $data['org'] = array(
            'fname' => isset($config['fname']) ? $config['fname'] : 'องค์กรปกครองส่วนท้องถิ่น',
            'abbreviation' => isset($config['abbreviation']) ? $config['abbreviation'] : ''
        );
        
        // ✅ ดึงข้อมูลหมวดหมู่จาก get_all_categories แล้วหาตัวที่ตรงกับ category_id
        $all_categories = $this->Data_catalog_model->get_all_categories();
        $category = null;
        
        foreach ($all_categories as $cat) {
            if ($cat->id == $category_id) {
                $category = $cat;
                break;
            }
        }
        
        // ถ้าไม่เจอหมวดหมู่
        if (!$category) {
            show_404();
            return;
        }
        
        $data['category'] = $category;
        $data['page_title'] = $category->category_name;
        
        // Pagination
        $per_page = 12;
        $page = $this->input->get('page', TRUE) ?: 1;
        $offset = ($page - 1) * $per_page;
        
        // ดึงชุดข้อมูลในหมวดหมู่นี้
        $data['datasets'] = $this->Data_catalog_model->get_datasets_by_category(
            $category_id,
            $per_page,
            $offset
        );
        
        // นับทั้งหมด
        $total_rows = $this->Data_catalog_model->count_datasets_by_category($category_id);
        
        // Pagination
        if ($total_rows > $per_page) {
            $config_pagination = array(
                'base_url' => base_url('data_catalog/category/' . $category_id),
                'total_rows' => $total_rows,
                'per_page' => $per_page,
                'use_page_numbers' => TRUE,
            );
            $this->pagination->initialize($config_pagination);
            $data['pagination'] = $this->pagination->create_links();
        } else {
            $data['pagination'] = '';
        }
        
        $this->load->view('data_catalog/category', $data);
    }


    /**
     * หน้าชุดข้อมูล (เพิ่มการบันทึก log) ⭐
     */
    public function dataset($dataset_id) {
        $config = $this->System_config_model->get_all_config();
        $data['org'] = array(
            'fname' => isset($config['fname']) ? $config['fname'] : 'องค์กรปกครองส่วนท้องถิ่น',
            'abbreviation' => isset($config['abbreviation']) ? $config['abbreviation'] : ''
        );
        
        $dataset = $this->Data_catalog_model->get_dataset_by_id($dataset_id);
        
        if (!$dataset) {
            show_404();
            return;
        }
        
        // ⭐ บันทึก log การเข้าชม
        $this->Data_catalog_model->log_view($dataset_id);
        
        // ⭐ เพิ่มจำนวนการเข้าชมในตาราง catalog (สำหรับแสดงผล)
        $this->Data_catalog_model->increment_view($dataset_id);
        
        $data['dataset'] = $dataset;
        $data['page_title'] = $dataset->dataset_name;
        
        $data['metadata'] = $this->Data_catalog_model->get_dataset_metadata($dataset_id);
        
        $data['related_datasets'] = $this->Data_catalog_model->get_related_datasets(
            $dataset->category_id,
            $dataset_id,
            4
        );
        
        $this->load->view('data_catalog/dataset', $data);
    }


    /**
     * API JSON สำหรับ Dataset
     */
    public function api($dataset_id = null) {
        header('Content-Type: application/json; charset=utf-8');
        
        if ($dataset_id) {
            // ดึงข้อมูล Dataset เดียว
            $dataset = $this->Data_catalog_model->get_dataset($dataset_id);
            if (!$dataset) {
                echo json_encode([
                    'status' => 'error',
                    'message' => 'Dataset not found'
                ], JSON_UNESCAPED_UNICODE);
                return;
            }

            $metadata = $this->Data_catalog_model->get_dataset_metadata($dataset_id);
            
            // ซ่อน table_name และข้อมูลที่ไม่จำเป็น
            $dataset_info = [
                'id' => $dataset->id,
                'dataset_name' => $dataset->dataset_name,
                'dataset_name_en' => $dataset->dataset_name_en,
                'description' => $dataset->description,
                'category_id' => $dataset->category_id,
                'category_name' => $dataset->category_name ?? '',
                'data_format' => $dataset->data_format,
                'data_source' => $dataset->data_source,
                'access_level' => $dataset->access_level,
                'responsible_department' => $dataset->responsible_department,
                'responsible_person' => $dataset->responsible_person,
                'contact_email' => $dataset->contact_email,
                'contact_phone' => $dataset->contact_phone,
                'keywords' => $dataset->keywords,
                'license' => $dataset->license,
                'update_frequency' => $dataset->update_frequency,
                'record_count' => $dataset->record_count,
                'views' => $dataset->views,
                'last_updated' => $dataset->last_updated,
                'created_at' => $dataset->created_at,
                'download_url' => $dataset->download_url,
                'api_endpoint' => $dataset->api_endpoint
            ];
            
            // แปลง metadata ให้ใช้ชื่อแสดง (field_name_en)
            $metadata_info = [];
            foreach ($metadata as $field) {
                // ใช้ชื่อแสดง ถ้าไม่มีให้ใช้ชื่อจริง
                $display_name = (!empty($field->field_name_en)) 
                              ? $field->field_name_en 
                              : $field->field_name;
                
                $metadata_info[] = [
                    'field_name' => $display_name,
                    'field_type' => $field->field_type ?? '',
                    'field_length' => $field->field_length ?? null,
                    'field_description' => $field->field_description ?? '',
                    'is_required' => $field->is_required ?? 0,
                    'is_primary_key' => $field->is_primary_key ?? 0,
                    'example_value' => $field->example_value ?? null,
                    'display_order' => $field->display_order ?? 0
                ];
            }
            
            echo json_encode([
                'status' => 'success',
                'data' => [
                    'dataset' => $dataset_info,
                    'metadata' => $metadata_info
                ]
            ], JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT);
        } else {
            // ดึงข้อมูล Dataset ทั้งหมด
            $page = $this->input->get('page') ? (int)$this->input->get('page') : 1;
            $per_page = $this->input->get('per_page') ? (int)$this->input->get('per_page') : 50;
            $per_page = min($per_page, 100); // จำกัดไม่เกิน 100
            
            $offset = ($page - 1) * $per_page;
            
            $datasets = $this->Data_catalog_model->get_all_datasets($per_page, $offset);
            $total = $this->Data_catalog_model->count_total_datasets();
            
            // ซ่อน table_name ในทุก dataset
            $datasets_clean = [];
            foreach ($datasets as $ds) {
                $datasets_clean[] = [
                    'id' => $ds->id,
                    'dataset_name' => $ds->dataset_name,
                    'dataset_name_en' => $ds->dataset_name_en,
                    'description' => $ds->description,
                    'category_name' => $ds->category_name ?? '',
                    'data_format' => $ds->data_format,
                    'access_level' => $ds->access_level,
                    'record_count' => $ds->record_count,
                    'views' => $ds->views,
                    'last_updated' => $ds->last_updated,
                    'download_url' => $ds->download_url,
                    'api_endpoint' => $ds->api_endpoint
                ];
            }
            
            echo json_encode([
                'status' => 'success',
                'data' => $datasets_clean,
                'pagination' => [
                    'page' => $page,
                    'per_page' => $per_page,
                    'total' => $total,
                    'total_pages' => ceil($total / $per_page)
                ]
            ], JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT);
        }
    }

    /**
     * Export รายการ Dataset เป็น CSV
     */
    public function export_csv() {
        $this->load->dbutil();
        $this->load->helper('download');

        $datasets = $this->Data_catalog_model->get_all_datasets_for_export();

        $delimiter = ",";
        $newline = "\r\n";

        $csv_data = $this->dbutil->csv_from_result($datasets, $delimiter, $newline);

        $filename = 'data_catalog_' . date('Y-m-d_His') . '.csv';
        
        $csv_data = "\xEF\xBB\xBF" . $csv_data;
        
        force_download($filename, $csv_data);
    }
	
	
	/**
     * ดาวน์โหลดชุดข้อมูล (พร้อมบันทึก log) ⭐
     */
    public function download($dataset_id) {
        // ดึงข้อมูล dataset
        $dataset = $this->Data_catalog_model->get_dataset_by_id($dataset_id);
        
        if (!$dataset) {
            show_404();
            return;
        }
        
        // ⭐ บันทึก log การดาวน์โหลด
        $this->Data_catalog_model->log_download($dataset_id);
        
        // เพิ่มจำนวนดาวน์โหลดในตาราง catalog (ถ้ามีคอลัมน์)
        // $this->Data_catalog_model->increment_download($dataset_id);
        
        // Redirect ไปยัง download URL
        if (!empty($dataset->download_url)) {
            $download_url = (strpos($dataset->download_url, 'http') === 0) 
                          ? $dataset->download_url 
                          : base_url($dataset->download_url);
            redirect($download_url);
        } else {
            // ถ้าไม่มี download URL ให้กลับไปหน้ารายละเอียด
            $this->session->set_flashdata('error', 'ไม่พบ URL สำหรับดาวน์โหลด');
            redirect('data_catalog/dataset/' . $dataset_id);
        }
    }
	
}