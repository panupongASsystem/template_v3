<?php
defined('BASEPATH') or exit('No direct script access allowed');

class Position_backend extends CI_Controller
{

	public function __construct()
	{
		parent::__construct();
		$this->load->model('position_model');
		if (
			$this->session->userdata('m_system') != 'system_admin'
		) {
			redirect('User/logout', 'refresh');
		}

		// ตั้งค่าเวลาหมดอายุของเซสชัน
		$this->check_session_timeout();
	}

	private function check_session_timeout()
	{
		$timeout = 900; // 15 นาที
		$last_activity = $this->session->userdata('last_activity');

		if ($last_activity && (time() - $last_activity > $timeout)) {
			$this->session->sess_destroy();
			redirect('User/logout', 'refresh');
		} else {
			$this->session->set_userdata('last_activity', time());
		}
	}

	public function index()
	{
		// print_r($_SESSION);
		$data['query'] = $this->position_model->list();
		// echo '<pre>';
		// print_r($data);
		// echo '</pre>';
		// exit;
		$this->load->view('templat/header');
		$this->load->view('asset/css');
		$this->load->view('templat/navbar_system_admin');
		$this->load->view('system_admin/structure_personnel', $data);
		$this->load->view('asset/js');
		$this->load->view('templat/footer');
	}

	public function editing($pid)
	{
		$data['rsedit'] = $this->position_model->read($pid);

		$this->load->view('templat/header');
		$this->load->view('asset/css');
		$this->load->view('templat/navbar_system_admin');
		$this->load->view('system_admin/structure_personnel_form_edit', $data);
		$this->load->view('asset/js');
		$this->load->view('templat/footer');
	}
	public function edit($pid)
	{
		// echo '<pre>';
		// print_r($_POST);
		// echo '</pre>';
		// exit;
		$this->position_model->edit($pid);
		redirect('position_backend', 'refresh');
	}

	public function updateStructure_personnelStatus()
	{
		$this->position_model->updateStructure_personnelStatus();
	}
}
