<?php
defined('BASEPATH') or exit('No direct script access allowed');

class Member_backend extends MY_Controller
{

	public function __construct()
	{
		parent::__construct();
		$this->load->model('space_model');
		$this->load->model('member_model');
		$this->load->model('position_model');

		// เช็ค steb 1 ระบบที่เลือกตรงมั้ย
		$this->check_access_permission(['1', '2']); // 1=ทั้งหมด
		
	}

	

	public function index()
	{
		// print_r($_SESSION);
		$data['query'] = $this->member_model->list_member();

		// echo '<pre>';
		// print_r($data);
		// echo '</pre>';
		// exit;

		$this->load->view('templat/header');
		$this->load->view('asset/css');
		$this->load->view('templat/navbar_system_admin');
		$this->load->view('system_admin/member', $data);
		$this->load->view('asset/js');
		$this->load->view('templat/footer');
	}

	public function adding()
	{
		$data['rspo_admin'] = $this->position_model->list_position_admin();
		$data['rspo_back_office'] = $this->position_model->list_position_back_office();
		$data['list_grant_user'] = $this->position_model->list_grant_user();
		// $data['provinces'] = $this->member_model->get_provinces();

		$this->load->view('templat/header');
		$this->load->view('asset/css');
		$this->load->view('templat/navbar_system_admin');
		$this->load->view('system_admin/member_form_add', $data);
		$this->load->view('asset/js');
		$this->load->view('templat/footer');
	}

	public function get_amphurs()
	{
		$province_name = $this->input->post('province_name');
		$amphurs = $this->member_model->get_unique_amphurs_by_province($province_name);
		echo json_encode($amphurs);
	}


	public function get_tambols()
	{
		$province_name = $this->input->post('province_name');
		$amphur_name = $this->input->post('amphur_name');
		$tambols = $this->member_model->get_tambols_by_amphur($province_name, $amphur_name);
		echo json_encode($tambols);
	}



	public function adddata()
	{
		echo '<pre>';
		print_r($_POST);
		echo '</pre>';
		exit;

		$this->member_model->addmember();
		redirect('member_backend', 'refresh');
	}
	public function add_Member()
	{
		// 		echo '<pre>';
		// print_r($_POST);
		// echo '</pre>';
		// exit;

		$this->member_model->add_Member();
		redirect('member_backend', 'refresh');
	}

	public function edit($m_id)
	{
		$data['rsedit'] = $this->member_model->read($m_id);
		$data['rspo_admin'] = $this->position_model->list_position_admin();
		$data['rspo_back_office'] = $this->position_model->list_position_back_office();
		$data['list_grant_user'] = $this->position_model->list_grant_user();
		// $data['provinces'] = $this->member_model->get_provinces();

		$this->load->view('templat/header');
		$this->load->view('asset/css');
		$this->load->view('templat/navbar_system_admin');
		$this->load->view('system_admin/member_form_edit', $data);
		$this->load->view('asset/js');
		$this->load->view('templat/footer');
	}

	// public function pwd($m_id)
	// {
	// 	$data['rsedit'] = $this->member_model->read($m_id);

	// 	$this->load->view('templat/header');
	// 	$this->load->view('asset/css');
	// 	$this->load->view('templat/navbar_system_admin');
	// 	$this->load->view('system_admin/member_form_pwd', $data);
	// 	$this->load->view('asset/js');
	// 	$this->load->view('templat/footer');
	// }

	public function edit_Member($m_id)
	{

		// echo '<pre>';
		// print_r($_POST);
		// echo '</pre>';
		// exit;
		$this->member_model->edit_Member($m_id);
		redirect('Member_backend', 'refresh');
	}

	public function edit_Profile($m_id)
	{

		// echo '<pre>';
		// print_r($_POST);
		// echo '</pre>';
		// exit;
		$this->member_model->edit_Profile($m_id);
		redirect('Member_backend', 'refresh');
	}


	// public function editpwd()
	// {
	// 	$this->member_model->editmemberpwd();
	// 	redirect('member', 'refresh');
	// }

	public function del($m_id)
	{
		// print_r($_POST);
		$this->member_model->deldata($m_id);
		$this->session->set_flashdata('del_success', TRUE);
		redirect('member_backend', 'refresh');
	}

	public function blockUser($m_id)
	{
		// เรียกใช้งานฟังก์ชัน blockMember และเก็บผลการบล็อคไว้ในตัวแปร $result
		$result = $this->member_model->blockMember($m_id);

		if ($result) {
			echo "<script>";
			echo "alert('แบนผู้ใช้งาน สำเร็จ !');";
			echo "</script>";
		} else {
			echo "<script>";
			echo "alert('Error !');";
			echo "</script>";
		}
		redirect('member_backend', 'refresh');
	}
	public function unblockUser($m_id)
	{
		// เรียกใช้งานฟังก์ชัน blockMember และเก็บผลการบล็อคไว้ในตัวแปร $result
		$result = $this->member_model->unblockMember($m_id);

		if ($result) {
			echo "<script>";
			echo "alert('ปลดแบนผู้ใช้งาน สำเร็จ !');";
			echo "</script>";
		} else {
			echo "<script>";
			echo "alert('Error !');";
			echo "</script>";
		}
		redirect('member_backend', 'refresh');
	}

	public function check_email()
	{
		$email = $this->input->post('email');
		$this->load->model('member_model');
		$exists = $this->member_model->check_email_exists($email);
		echo json_encode(['exists' => $exists]);
	}
}
