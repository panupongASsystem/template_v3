<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Emp_backend extends CI_Controller {

	public function __construct(){
		parent::__construct();
		if($this->session->userdata('m_level') != 4){
			redirect('user','refresh');
		}

		// ตั้งค่าเวลาหมดอายุของเซสชัน
        $this->check_session_timeout();

		$this->load->model('doc_model');
		$this->load->model('doctype_model');
		$this->load->model('member_model');

	}
	private function check_session_timeout() {
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
        $data['query'] = $this->doc_model->list_doc_emp();

		// echo '<pre>';
		// print_r($data);
		// echo '</pre>';
		// exit;

		$this->load->view('templat/header');
		$this->load->view('asset/css');
		$this->load->view('templat/navbar_emp');
		$this->load->view('emp/list_doc',  $data);
		$this->load->view('asset/js');
		$this->load->view('templat/footer');
	}

	public function profile()
	{
		$m_id = $_SESSION['m_id'];

		// echo $m_id;
		// print_r($_SESSION);
		// exit;

		$data['rsedit'] = $this->member_model->read($m_id);
		
		// echo '<pre>';
		// print_r($data);
		// echo '</pre>';
		// exit;

		$this->load->view('templat/header');
		$this->load->view('asset/css');
		$this->load->view('templat/navbar_emp');
		$this->load->view('emp/member_form_edit',  $data);
		$this->load->view('asset/js');
		$this->load->view('templat/footer');
	}

	public function editdata()
	{

		// echo '<pre>';
		// print_r($_POST);
		// echo '</pre>';
		// exit;
		$this->member_model->editemp();
		redirect('emp_backend/profile', 'refresh');
	}

	public function pwd()
	{
		$m_id = $_SESSION['m_id'];

		// echo $m_id;
		// print_r($_SESSION);
		// exit;

		$data['rsedit'] = $this->member_model->read($m_id);
		
		// echo '<pre>';
		// print_r($data);
		// echo '</pre>';
		// exit;

		$this->load->view('templat/header');
		$this->load->view('asset/css');
		$this->load->view('templat/navbar_emp');
		$this->load->view('emp/member_form_pwd',  $data);
		$this->load->view('asset/js');
		$this->load->view('templat/footer');
	}

	public function editpwd()
	{

		// echo '<pre>';
		// print_r($_POST);
		// echo '</pre>';
		// exit;
		$this->member_model->editemppwd();
		redirect('emp_backend', 'refresh');
	}


}
