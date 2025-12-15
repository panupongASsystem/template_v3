<?php
defined('BASEPATH') or exit('No direct script access allowed');

class Report_backend extends CI_Controller
{

	// ตั้งค่าสิทธิ์ที่ไม่ใช่ให้ออกไปหน้า login
	public function __construct()
	{
		parent::__construct();
		$allowedLevels = [1, 2, 3];
		$m_level = $this->session->userdata('m_level');
		$logged_in = $this->session->userdata('logged_in');

		// ตรวจสอบสิทธิ์และการล็อกอิน
		if ((!$logged_in && !$m_level) || !in_array($m_level, $allowedLevels)) {
			redirect('user', 'refresh');
		}

		

		$this->load->model('report_model');
	}

	

public function index()
	{
		// $data['date'] = $this->report_model->count_doc_date();
		// $data['month'] = $this->report_model->count_doc_month();
		// $data['year'] = $this->report_model->count_doc_year();
		// $data['status'] = $this->report_model->count_doc_status();
		// $data['type'] = $this->report_model->count_doc_type();
		$data['sum_news'] = $this->report_model->count_news();
		$data['sum_activity'] = $this->report_model->count_activity();
		$data['sum_travel'] = $this->report_model->count_travel();
		$data['sum_food'] = $this->report_model->count_food();
		$data['sum_health'] = $this->report_model->count_health();
		$data['sum_otop'] = $this->report_model->count_otop();
		$data['sum_store'] = $this->report_model->count_store();

		$data['sum_user_store'] = $this->report_model->count_user_store();
		// $data['sum_user_activity'] = $this->report_model->count_user_activity();
		// $data['sum_user_travel'] = $this->report_model->count_user_travel();
		// $data['sum_user_food'] = $this->report_model->count_user_food();

		// print_r($data);
		// exit();
		// print_r($_SESSION);
		$this->load->view('templat/header');
		$this->load->view('asset/css');
		$this->load->view('templat/navbar_system_admin');
		$this->load->view('system_admin/report', $data);
		$this->load->view('asset/js');
		$this->load->view('templat/footer');
	}

	// รายงานตามผู้ใช้งาน
	public function report_user()
	{
		$data['rsemp'] = $this->report_model->list_member();

		// ตรวจสอบการส่งคำค้นหาจากฟอร์ม
		$searched_user_id = $this->input->post('m_id');

		if (!empty($searched_user_id)) {
			$user_name = $this->report_model->get_user_name($searched_user_id);
			if ($user_name) {
				// แสดงชื่อการค้นหา
				$data['searched_user_name'] = $user_name->m_name;
				// แสดงชื่อหัวข้อ นับจำนวน
				$data['rsnews'] = $this->report_model->get_news_count($user_name->m_name);
				$data['rsactivity'] = $this->report_model->get_activity_count($user_name->m_name);
				$data['rsfood'] = $this->report_model->get_food_count($user_name->m_name);
				$data['rstravel'] = $this->report_model->get_travel_count($user_name->m_name);
				$data['rshealth'] = $this->report_model->get_health_count($user_name->m_name);
				$data['rsotop'] = $this->report_model->get_otop_count($user_name->m_name);
				$data['rsstore'] = $this->report_model->get_store_count($user_name->m_name);
				$data['rs_user_store'] = $this->report_model->get_user_store_count($user_name->m_name);
				// $data['rs_user_food'] = $this->report_model->get_user_food_count($user_name->m_name);
				// $data['rs_user_travel'] = $this->report_model->get_user_travel_count($user_name->m_name);

				// แสดงรายละเอียด
				$data['news_data'] = $this->report_model->get_news_data($user_name->m_name);
				$data['activity_data'] = $this->report_model->get_activity_data($user_name->m_name);
				$data['travel_data'] = $this->report_model->get_travel_data($user_name->m_name);
				$data['food_data'] = $this->report_model->get_food_data($user_name->m_name);
				$data['health_data'] = $this->report_model->get_health_data($user_name->m_name);
				$data['otop_data'] = $this->report_model->get_otop_data($user_name->m_name);
				$data['store_data'] = $this->report_model->get_store_data($user_name->m_name);
				$data['user_store_data'] = $this->report_model->get_user_store_data($user_name->m_name);
				// $data['user_travel_data'] = $this->report_model->get_user_travel_data($user_name->m_name);
				// $data['user_food_data'] = $this->report_model->get_user_food_data($user_name->m_name);
			}
		}

		$this->load->view('templat/header');
		$this->load->view('asset/css');
		$this->load->view('templat/navbar_system_admin');
		$this->load->view('system_admin/report_user', $data);
		$this->load->view('asset/js');
		$this->load->view('templat/footer');
	}

	// รายงานตามวันที่
	public function report_date()
	{
		$start_date = $this->input->post('start_date');
		$end_date = $this->input->post('end_date');

		if (!empty($start_date) && !empty($end_date)) {
			// เรียกใช้งานโมเดลเพื่อค้นหาและนับจำนวนข้อมูล
			$data['news_count'] = $this->report_model->get_news_count_date($start_date, $end_date);
			$data['activity_count'] = $this->report_model->get_activity_count_date($start_date, $end_date);
			$data['travel_count'] = $this->report_model->get_travel_count_date($start_date, $end_date);
			$data['food_count'] = $this->report_model->get_food_count_date($start_date, $end_date);
			$data['health_count'] = $this->report_model->get_health_count_date($start_date, $end_date);
			$data['otop_count'] = $this->report_model->get_otop_count_date($start_date, $end_date);
			$data['store_count'] = $this->report_model->get_store_count_date($start_date, $end_date);
			$data['user_store_count'] = $this->report_model->get_user_store_count_date($start_date, $end_date);

			// แสดงรายละเอียด
			$data['news_detail'] = $this->report_model->get_news_date_detail($start_date, $end_date);
			$data['activity_detail'] = $this->report_model->get_activity_date_detail($start_date, $end_date);
			$data['travel_detail'] = $this->report_model->get_travel_date_detail($start_date, $end_date);
			$data['food_detail'] = $this->report_model->get_food_date_detail($start_date, $end_date);
			$data['health_detail'] = $this->report_model->get_health_date_detail($start_date, $end_date);
			$data['otop_detail'] = $this->report_model->get_otop_date_detail($start_date, $end_date);
			$data['store_detail'] = $this->report_model->get_store_date_detail($start_date, $end_date);
			$data['user_store_detail'] = $this->report_model->get_user_store_date_detail($start_date, $end_date);

			// ส่งข้อมูลไปยัง view สำหรับแสดงผล
			$this->load->view('templat/header');
			$this->load->view('asset/css');
			$this->load->view('templat/navbar_system_admin');
			$this->load->view('system_admin/report_date', $data);
			$this->load->view('asset/js');
			$this->load->view('templat/footer');
		} else {
			// ถ้าไม่ได้รับวันที่เริ่มต้นและสิ้นสุด ให้แสดงฟอร์มสำหรับกรอกข้อมูล
			$this->load->view('templat/header');
			$this->load->view('asset/css');
			$this->load->view('templat/navbar_system_admin');
			$this->load->view('system_admin/report_date');
			$this->load->view('asset/js');
			$this->load->view('templat/footer');
		}
	}

	public function doc_status()
	{
		$data['query'] = $this->report_model->count_doc_status();

		// print_r($data);
		// exit();
		// print_r($_SESSION);
		$this->load->view('templat/header');
		$this->load->view('asset/css');
		$this->load->view('templat/navbar_report');
		$this->load->view('report/r_docstatus', $data);
		$this->load->view('asset/js');
		$this->load->view('templat/footer');
	}

	public function bytype()
	{
		$data['query'] = $this->report_model->count_doc_type();

		// print_r($data);
		// exit();
		// print_r($_SESSION);
		$this->load->view('templat/header');
		$this->load->view('asset/css');
		$this->load->view('templat/navbar_report');
		$this->load->view('report/r_doctype', $data);
		$this->load->view('asset/js');
		$this->load->view('templat/footer');
	}

	// แยกตามวันที่
	public function bydate()
	{
		$data['query'] = $this->report_model->count_doc_date();

		// print_r($data);
		// exit();
		$this->load->view('templat/header');
		$this->load->view('asset/css');
		$this->load->view('templat/navbar_report');
		$this->load->view('report/r_docdate', $data);
		$this->load->view('asset/js');
		$this->load->view('templat/footer');
	}

	public function bymonth()
	{
		$data['query'] = $this->report_model->count_doc_month();

		// print_r($data);
		// exit();
		$this->load->view('templat/header');
		$this->load->view('asset/css');
		$this->load->view('templat/navbar_report');
		$this->load->view('report/r_docmonth', $data);
		$this->load->view('asset/js');
		$this->load->view('templat/footer');
	}

	public function byyear()
	{
		$data['query'] = $this->report_model->count_doc_year();

		// print_r($data);
		// exit();
		$this->load->view('templat/header');
		$this->load->view('asset/css');
		$this->load->view('templat/navbar_report');
		$this->load->view('report/r_docyear', $data);
		$this->load->view('asset/js');
		$this->load->view('templat/footer');
	}

	public function form()
	{
		$this->load->view('templat/header');
		$this->load->view('asset/css');
		$this->load->view('templat/navbar_report');
		$this->load->view('report/r_form');
		$this->load->view('asset/js');
		$this->load->view('templat/footer');
	}

	// ค้นหาเอกสาร
	public function getform()
	{
		$data['query'] = $this->report_model->count_doc_form();

		// echo '<pre>';
		// print_r($data);
		// echo '</pre>';
		// exit();
		$this->load->view('templat/header');
		$this->load->view('asset/css');
		$this->load->view('templat/navbar_report');
		$this->load->view('report/list_doc', $data);
		$this->load->view('asset/js');
		$this->load->view('templat/footer');
	}

	public function mychart_d()
	{

		$data['query'] = $this->report_model->count_doc_date();

		$this->load->view('templat/header');
		$this->load->view('asset/css');
		$this->load->view('templat/navbar_report');
		$this->load->view('report/mychart_d', $data);
		$this->load->view('asset/js');
		$this->load->view('templat/footer');
	}

	public function mychart_m()
	{

		$data['query'] = $this->report_model->count_doc_month();

		$this->load->view('templat/header');
		$this->load->view('asset/css');
		$this->load->view('templat/navbar_report');
		$this->load->view('report/mychart_m', $data);
		$this->load->view('asset/js');
		$this->load->view('templat/footer');
	}

	public function mychart_y()
	{

		$data['query'] = $this->report_model->count_doc_year();

		$this->load->view('templat/header');
		$this->load->view('asset/css');
		$this->load->view('templat/navbar_report');
		$this->load->view('report/mychart_y', $data);
		$this->load->view('asset/js');
		$this->load->view('templat/footer');
	}

	public function mychart_l()
	{

		$data['query'] = $this->report_model->count_doc_status();

		$this->load->view('templat/header');
		$this->load->view('asset/css');
		$this->load->view('templat/navbar_report');
		$this->load->view('report/mychart_l', $data);
		$this->load->view('asset/js');
		$this->load->view('templat/footer');
	}

	public function mychart_t()
	{

		$data['query'] = $this->report_model->count_doc_type();

		$this->load->view('templat/header');
		$this->load->view('asset/css');
		$this->load->view('templat/navbar_report');
		$this->load->view('report/mychart_t', $data);
		$this->load->view('asset/js');
		$this->load->view('templat/footer');
	}
}
