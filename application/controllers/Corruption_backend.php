<?php
defined('BASEPATH') or exit('No direct script access allowed');

class Corruption_backend extends MY_Controller
{

    public function __construct()
    {
        parent::__construct();
         // เช็ค steb 1 ระบบที่เลือกตรงมั้ย
                $this->check_access_permission(['1', '107']); // 1=ทั้งหมด

        $this->load->model('member_model');
        $this->load->model('space_model');
        $this->load->model('corruption_model');
    }

   

public function index()
    {
        $corruptions = $this->corruption_model->get_corruptions();

        foreach ($corruptions as $corruption) {
            $corruption->images = $this->corruption_model->get_images_for_corruption($corruption->corruption_id);
        }

        $this->load->view('templat/header');
        $this->load->view('asset/css');
        $this->load->view('templat/navbar_system_admin');
        $this->load->view('system_admin/corruption', ['corruptions' => $corruptions]);
        $this->load->view('asset/js');
        $this->load->view('templat/footer');
    }

    // public function updatecorruptionStatus()
    // {
    //     // ตรวจสอบว่ามีการส่งข้อมูล POST มาหรือไม่
    //     if ($this->input->post()) {
    //         $corruptionId = $this->input->post('corruption_id'); // รับค่า corruption_id
    //         $newStatus = $this->input->post('new_status'); // รับค่าใหม่จาก dropdown

    //         // ทำการอัพเดตค่าในตาราง tbl_corruption ในฐานข้อมูลของคุณ
    //         $data = array(
    //             'corruption_status' => $newStatus
    //         );
    //         $this->db->where('corruption_id', $corruptionId); // ระบุ corruption_id ของแถวที่ต้องการอัพเดต
    //         $this->db->update('tbl_corruption', $data);

    //         // ดึงข้อมูลของ corruption_id จากฐานข้อมูล
    //         $corruptionData = $this->db->get_where('tbl_corruption', array('corruption_id' => $corruptionId))->row();
    //         if ($corruptionData) {
    //             $message = "เรื่องร้องเรียน !" . "\n";
    //             $message .= "case: " . $corruptionData->corruption_id . "\n";
    //             $message .= "สถานะ: " . $newStatus . "\n";
    //             $message .= "เรื่อง: " . $corruptionData->corruption_head . "\n";
    //             $message .= "ประเภท: " . $corruptionData->corruption_type . "\n";
    //             $message .= "รายละเอียด: " . $corruptionData->corruption_detail . "\n";
    //             $message .= "พิกัด: " . $corruptionData->corruption_map . "\n";
    //             $message .= "เบอร์โทรศัพท์ผู้แจ้ง: " . $corruptionData->corruption_phone . "\n";
    //             $message .= "ชื่อผู้แจ้ง: " . $corruptionData->corruption_by . "\n";
    //         } else {
    //             $message = "สถานะใหม่: " . $newStatus;
    //         }


    //         // โค้ดสำหรับส่งข้อความ LINE Notify
    //         define('LINE_API', "https://notify-api.line.me/api/notify");
    //         $token = "ziHhjoKhdgWBAOSV8LiwhKm7LZxqfqP52esG3pYkNlK"; // ใส่ Token ที่คุณได้รับ

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

    // public function detail($corruption_id)
    // {
    //     $data['query'] = $this->corruption_model->read_detail($corruption_id);
    //     $data['qcorruption'] = $this->corruption_model->read($corruption_id);
    //     $data['latest_query'] = $this->corruption_model->getLatestDetail($corruption_id);

    //     $this->load->view('templat/header');
    //     $this->load->view('asset/css');
    //     $this->load->view('templat/navbar_system_admin');
    //     $this->load->view('system_admin/corruption_detail', $data);
    //     $this->load->view('asset/js');
    //     $this->load->view('templat/footer');
    // }


    // public function updateStatus($corruption_detail_case_id)
    // {
    //     // รับข้อมูลจากฟอร์ม
    //     $corruption_detail_case_id = $this->input->post('corruption_detail_case_id');
    //     $corruption_detail_status = $this->input->post('corruption_detail_status');
    //     $corruption_detail_com = $this->input->post('corruption_detail_com');

    //     // เรียกใช้ฟังก์ชัน updatecorruption
    //     $this->corruption_model->updatecorruption($corruption_detail_case_id, $corruption_detail_status, $corruption_detail_com);

    //     // รีเดิร็คหน้าหลังจากทำการบันทึก
    //     redirect('corruption_backend/detail/' . $corruption_detail_case_id);
    // }

    // public function statusCancel($corruption_detail_case_id)
    // {
    //     $corruption_detail_case_id = $this->input->post('corruption_detail_case_id');
    //     $corruption_detail_status = 'ยกเลิก';
    //     $corruption_detail_com = $this->input->post('corruption_detail_com'); // รับข้อมูลจาก Swal

    //     // เรียกใช้ Model เพื่ออัปเดตข้อมูล
    //     $this->corruption_model->statusCancel($corruption_detail_case_id, $corruption_detail_status, $corruption_detail_com);

    //     // รีเดิร็คหน้าหลังจากทำการบันทึก
    //     redirect('corruption_backend/detail/' . $corruption_detail_case_id);
    // }

    public function del($corruption_id)
    {
        // print_r($_POST);
        $this->corruption_model->del($corruption_id);
        redirect('Corruption_backend', 'refresh');
    }
}
