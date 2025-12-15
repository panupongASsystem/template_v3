<?php
defined('BASEPATH') or exit('No direct script access allowed');

class Q_a_backend extends MY_Controller
{
    public function __construct()
    {
        parent::__construct();
          // เช็ค steb 1 ระบบที่เลือกตรงมั้ย
          $this->check_access_permission(['1', '110']); // 1=ทั้งหมด

        $this->load->model('member_model');
        $this->load->model('q_a_model');
        $this->load->library('form_validation');
		$this->load->library('Notification_lib');
    }

   

public function index()
    {
        // อ่านข้อมูลความคิดเห็นทั้งหมดจากตาราง tbl_q_a
        $data['rsCom'] = $this->q_a_model->list_all();

        // อ่านข้อมูลความคิดเห็นตอบกลับทั้งหมดจากตาราง tbl_q_a_reply
        foreach ($data['rsCom'] as $index => $com) {
            $q_a_id = $com->q_a_id;
            $q_a_reply_data = $this->q_a_model->read_all_q_a_reply($q_a_id);

            // เก็บข้อมูลความคิดเห็นตอบกลับลงในอาร์เรย์ของความคิดเห็น
            $data['rsCom'][$index]->com_reply_data = $q_a_reply_data;
        }
        $this->load->view('templat/header');
        $this->load->view('asset/css');
        $this->load->view('templat/navbar_system_admin');
        $this->load->view('system_admin/q_a', $data);
        $this->load->view('asset/js');
        $this->load->view('templat/footer');
    }

    public function del_com($q_a_id)
    {
        $this->q_a_model->del_com($q_a_id);

        // ส่งคำตอบในรูปแบบ JSON เพื่อระบุว่าการลบสำเร็จ
        $response = array('success' => true);
        header('Content-Type: application/json');
        echo json_encode($response);
        $this->session->set_flashdata('del_success', TRUE);
    }

    public function del_com_reply($q_a_reply_id)
    {
        $this->q_a_model->del_com_reply($q_a_reply_id);

        // ส่งคำตอบในรูปแบบ JSON เพื่อระบุว่าการลบสำเร็จ
        $response = array('success' => true);
        header('Content-Type: application/json');
        echo json_encode($response);
        $this->session->set_flashdata('del_success', TRUE);
    }
	
	
	public function new_qa_notification($qa_id)
{
    $qa_data = $this->q_a_model->get_qa_by_id($qa_id); // สมมติว่ามี method นี้
    if ($qa_data) {
        $this->Notification_lib->new_qa(
            $qa_id,
            $qa_data->qa_topic ?? 'กระทู้ใหม่',
            $qa_data->qa_by ?? 'ไม่ระบุ'
        );
    }
}

// เพิ่ม method สำหรับการแจ้งเตือนเมื่อมีการตอบ
public function qa_reply_notification($qa_id, $reply_by)
{
    $this->Notification_lib->qa_reply($qa_id, $reply_by);
}
	
}
