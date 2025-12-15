<?php
defined('BASEPATH') or exit('No direct script access allowed');

class Esv_ods_backend extends MY_Controller
{

    public function __construct()
    {
        parent::__construct();
        // เช็ค steb 1 ระบบที่เลือกตรงมั้ย
               $this->check_access_permission(['1', '109']); // 1=ทั้งหมด


        $this->load->model('member_model');
        $this->load->model('space_model');
        $this->load->model('esv_ods_model');
    }

   

public function index()
    {
        $data['query'] = $this->esv_ods_model->list_all();

        $this->load->view('templat/header');
        $this->load->view('asset/css');
        $this->load->view('templat/navbar_system_admin');
        $this->load->view('system_admin/esv_ods', $data);
        $this->load->view('asset/js');
        $this->load->view('templat/footer');
    }

    // public function updateesv_odsStatus()
    // {
    //     // ตรวจสอบว่ามีการส่งข้อมูล POST มาหรือไม่
    //     if ($this->input->post()) {
    //         $esv_odsId = $this->input->post('esv_ods_id'); // รับค่า esv_ods_id
    //         $newStatus = $this->input->post('new_status'); // รับค่าใหม่จาก dropdown

    //         // ทำการอัพเดตค่าในตาราง tbl_esv_ods ในฐานข้อมูลของคุณ
    //         $data = array(
    //             'esv_ods_status' => $newStatus
    //         );
    //         $this->db->where('esv_ods_id', $esv_odsId); // ระบุ esv_ods_id ของแถวที่ต้องการอัพเดต
    //         $this->db->update('tbl_esv_ods', $data);

    //         // ดึงข้อมูลของ esv_ods_id จากฐานข้อมูล
    //         $esv_odsData = $this->db->get_where('tbl_esv_ods', array('esv_ods_id' => $esv_odsId))->row();
    //         if ($esv_odsData) {
    //             $message = "เรื่องร้องเรียน !" . "\n";
    //             $message .= "case: " . $esv_odsData->esv_ods_id . "\n";
    //             $message .= "สถานะ: " . $newStatus . "\n";
    //             $message .= "เรื่อง: " . $esv_odsData->esv_ods_head . "\n";
    //             $message .= "ประเภท: " . $esv_odsData->esv_ods_type . "\n";
    //             $message .= "รายละเอียด: " . $esv_odsData->esv_ods_detail . "\n";
    //             $message .= "พิกัด: " . $esv_odsData->esv_ods_map . "\n";
    //             $message .= "เบอร์โทรศัพท์ผู้แจ้ง: " . $esv_odsData->esv_ods_phone . "\n";
    //             $message .= "ชื่อผู้แจ้ง: " . $esv_odsData->esv_ods_by . "\n";
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

    // public function detail($esv_ods_id)
    // {
    //     $data['query'] = $this->esv_ods_model->read_detail($esv_ods_id);
    //     $data['qesv_ods'] = $this->esv_ods_model->read($esv_ods_id);
    //     $data['latest_query'] = $this->esv_ods_model->getLatestDetail($esv_ods_id);

    //     $this->load->view('templat/header');
    //     $this->load->view('asset/css');
    //     $this->load->view('templat/navbar_system_admin');
    //     $this->load->view('system_admin/esv_ods_detail', $data);
    //     $this->load->view('asset/js');
    //     $this->load->view('templat/footer');
    // }


    // public function updateStatus($esv_ods_detail_case_id)
    // {
    //     // รับข้อมูลจากฟอร์ม
    //     $esv_ods_detail_case_id = $this->input->post('esv_ods_detail_case_id');
    //     $esv_ods_detail_status = $this->input->post('esv_ods_detail_status');
    //     $esv_ods_detail_com = $this->input->post('esv_ods_detail_com');

    //     // เรียกใช้ฟังก์ชัน updateesv_ods
    //     $this->esv_ods_model->updateesv_ods($esv_ods_detail_case_id, $esv_ods_detail_status, $esv_ods_detail_com);

    //     // รีเดิร็คหน้าหลังจากทำการบันทึก
    //     redirect('esv_ods_backend/detail/' . $esv_ods_detail_case_id);
    // }

    // public function statusCancel($esv_ods_detail_case_id)
    // {
    //     $esv_ods_detail_case_id = $this->input->post('esv_ods_detail_case_id');
    //     $esv_ods_detail_status = 'ยกเลิก';
    //     $esv_ods_detail_com = $this->input->post('esv_ods_detail_com'); // รับข้อมูลจาก Swal

    //     // เรียกใช้ Model เพื่ออัปเดตข้อมูล
    //     $this->esv_ods_model->statusCancel($esv_ods_detail_case_id, $esv_ods_detail_status, $esv_ods_detail_com);

    //     // รีเดิร็คหน้าหลังจากทำการบันทึก
    //     redirect('esv_ods_backend/detail/' . $esv_ods_detail_case_id);
    // }

    public function del($esv_ods_id)
    {
        // print_r($_POST);
        $this->esv_ods_model->del($esv_ods_id);
        redirect('esv_ods_backend', 'refresh');
    }
}
