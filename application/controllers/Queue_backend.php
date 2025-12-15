<?php
defined('BASEPATH') or exit('No direct script access allowed');

class Queue_backend extends MY_Controller
{

    public function __construct()
    {
        parent::__construct();
         // เช็ค steb 1 ระบบที่เลือกตรงมั้ย
         $this->check_access_permission(['1', '106']); // 1=ทั้งหมด

        $this->load->model('member_model');
        $this->load->model('space_model');
        $this->load->model('queue_model');
		$this->load->library('Notification_lib');
    }


public function index()
    {
        $queue_status = $this->input->get('queue_status');
    
        if (!$queue_status) {
            // ถ้าไม่มีการกรองด้วย queue_status ให้ดึงทั้งหมด
            $queues = $this->queue_model->get_queues();
        } else {
            // ถ้ามีการกรองด้วย queue_status ให้ดึงตามเงื่อนไข
            $queues = $this->queue_model->get_queues($queue_status);
        }
    

    
        $this->load->view('templat/header');
        $this->load->view('asset/css');
        $this->load->view('templat/navbar_system_admin');
        $this->load->view('system_admin/queue', ['queues' => $queues]);
        $this->load->view('asset/js');
        $this->load->view('templat/footer');
    }
    

    // public function updatequeueStatus()
    // {
    //     // ตรวจสอบว่ามีการส่งข้อมูล POST มาหรือไม่
    //     if ($this->input->post()) {
    //         $queueId = $this->input->post('queue_id'); // รับค่า queue_id
    //         $newStatus = $this->input->post('new_status'); // รับค่าใหม่จาก dropdown

    //         // ทำการอัพเดตค่าในตาราง tbl_queue ในฐานข้อมูลของคุณ
    //         $data = array(
    //             'queue_status' => $newStatus
    //         );
    //         $this->db->where('queue_id', $queueId); // ระบุ queue_id ของแถวที่ต้องการอัพเดต
    //         $this->db->update('tbl_queue', $data);

    //         // ดึงข้อมูลของ queue_id จากฐานข้อมูล
    //         $queueData = $this->db->get_where('tbl_queue', array('queue_id' => $queueId))->row();
    //         if ($queueData) {
    //             $message = "เรื่องร้องเรียน !" . "\n";
    //             $message .= "case: " . $queueData->queue_id . "\n";
    //             $message .= "สถานะ: " . $newStatus . "\n";
    //             $message .= "เรื่อง: " . $queueData->queue_topic . "\n";
    //             $message .= "รายละเอียด: " . $queueData->queue_detail . "\n";
    //             $message .= "เบอร์โทรศัพท์ผู้แจ้ง: " . $queueData->queue_phone . "\n";
    //             $message .= "ชื่อผู้แจ้ง: " . $queueData->queue_by . "\n";
    //         } else {
    //             $message = "สถานะใหม่: " . $newStatus;
    //         }


    //         // โค้ดสำหรับส่งข้อความ LINE Notify
    //         define('LINE_API', "https://notify-api.line.me/api/notify");
    //         $token = "Bo56prLIvzzXoSIvQhFImstsl3PusUl2iKEvEsVMobV"; // ใส่ Token ที่คุณได้รับ

    //         $queryData = array('message' => $message);
    //         $queryData = http_build_query($queryData, '', '&');
    //         $headerOptions = array(
    //             'http' => array(
    //                 'method' => 'POST',
    //                 'header' => "Content-Type: application/x-www-form-urlencoded\r\n" .
    //                     "Authorization: Bearer " . $token . "\r\n" .
    //                     "Content-Length: " . strlen($queryData) . "\r\n",
    //                 'content' => $queryData
    //             ),
    //         );

    //         $context = stream_context_create($headerOptions);
    //         $result = file_get_contents(LINE_API, FALSE, $context);
    //         $res = json_decode($result);
    //     } else {
    //         // ถ้าไม่มีข้อมูล POST ส่งมา ให้รีเดอร์เปรียบเสมอ
    //         show_404();
    //     }
    // }

    public function detail($queue_id)
    {
        $data['query'] = $this->queue_model->read_detail($queue_id);
        $data['qqueue'] = $this->queue_model->read($queue_id);
        $data['latest_query'] = $this->queue_model->getLatestDetail($queue_id);
    
        // เพิ่มบรรทัดนี้เพื่อส่งข้อมูลหัวข้อร้องเรียนไปยัง view
        $data['queue_topic'] = $this->queue_model->get_queue_topic($queue_id);
    
        $this->load->view('templat/header');
        $this->load->view('asset/css');
        $this->load->view('templat/navbar_system_admin');
        $this->load->view('system_admin/queue_detail', $data);
        $this->load->view('asset/js');
        $this->load->view('templat/footer');
    }


    public function updateStatus($queue_detail_case_id)
    {
        // รับข้อมูลจากฟอร์ม
        $queue_detail_case_id = $this->input->post('queue_detail_case_id');
        $queue_detail_status = $this->input->post('queue_detail_status');
        $queue_detail_com = $this->input->post('queue_detail_com');

        // เรียกใช้ฟังก์ชัน updatequeue
        $this->queue_model->updatequeue($queue_detail_case_id, $queue_detail_status, $queue_detail_com);
		
		$queue_data = $this->queue_model->read($queue_detail_case_id);
    if ($queue_data) {
        $this->Notification_lib->new_queue(
            $queue_detail_case_id,
            $queue_data->queue_topic ?? 'อัปเดตสถานะคิว',
            $this->session->userdata('m_fname')
        );
    }


        // รีเดิร็คหน้าหลังจากทำการบันทึก
        redirect('queue_backend/detail/' . $queue_detail_case_id);
    }

    public function statusCancel($queue_detail_case_id)
    {
        $queue_detail_case_id = $this->input->post('queue_detail_case_id');
        $queue_detail_status = 'ยกเลิก';
        $queue_detail_com = $this->input->post('queue_detail_com'); // รับข้อมูลจาก Swal

        // เรียกใช้ Model เพื่ออัปเดตข้อมูล
        $this->queue_model->statusCancel($queue_detail_case_id, $queue_detail_status, $queue_detail_com);

        // รีเดิร็คหน้าหลังจากทำการบันทึก
        redirect('queue_backend/detail/' . $queue_detail_case_id);
    }
}
