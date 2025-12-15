<?php
defined('BASEPATH') or exit('No direct script access allowed');

class suggestions_backend extends MY_Controller
{

    public function __construct()
    {
        parent::__construct();
        // เช็ค steb 1 ระบบที่เลือกตรงมั้ย
        $this->check_access_permission(['1', '108']); // 1=ทั้งหมด

        $this->load->model('member_model');
        $this->load->model('space_model');
        $this->load->model('suggestions_model');
    }

    

public function index()
    {
        $suggestionss = $this->suggestions_model->get_suggestionss();

        foreach ($suggestionss as $suggestions) {
            $suggestions->images = $this->suggestions_model->get_images_for_suggestions($suggestions->suggestions_id);
        }

        $this->load->view('templat/header');
        $this->load->view('asset/css');
        $this->load->view('templat/navbar_system_admin');
        $this->load->view('system_admin/suggestions', ['suggestionss' => $suggestionss]);
        $this->load->view('asset/js');
        $this->load->view('templat/footer');
    }

    // public function updatesuggestionsStatus()
    // {
    //     // ตรวจสอบว่ามีการส่งข้อมูล POST มาหรือไม่
    //     if ($this->input->post()) {
    //         $suggestionsId = $this->input->post('suggestions_id'); // รับค่า suggestions_id
    //         $newStatus = $this->input->post('new_status'); // รับค่าใหม่จาก dropdown

    //         // ทำการอัพเดตค่าในตาราง tbl_suggestions ในฐานข้อมูลของคุณ
    //         $data = array(
    //             'suggestions_status' => $newStatus
    //         );
    //         $this->db->where('suggestions_id', $suggestionsId); // ระบุ suggestions_id ของแถวที่ต้องการอัพเดต
    //         $this->db->update('tbl_suggestions', $data);

    //         // ดึงข้อมูลของ suggestions_id จากฐานข้อมูล
    //         $suggestionsData = $this->db->get_where('tbl_suggestions', array('suggestions_id' => $suggestionsId))->row();
    //         if ($suggestionsData) {
    //             $message = "เรื่องร้องเรียน !" . "\n";
    //             $message .= "case: " . $suggestionsData->suggestions_id . "\n";
    //             $message .= "สถานะ: " . $newStatus . "\n";
    //             $message .= "เรื่อง: " . $suggestionsData->suggestions_head . "\n";
    //             $message .= "ประเภท: " . $suggestionsData->suggestions_type . "\n";
    //             $message .= "รายละเอียด: " . $suggestionsData->suggestions_detail . "\n";
    //             $message .= "พิกัด: " . $suggestionsData->suggestions_map . "\n";
    //             $message .= "เบอร์โทรศัพท์ผู้แจ้ง: " . $suggestionsData->suggestions_phone . "\n";
    //             $message .= "ชื่อผู้แจ้ง: " . $suggestionsData->suggestions_by . "\n";
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

    // public function detail($suggestions_id)
    // {
    //     $data['query'] = $this->suggestions_model->read_detail($suggestions_id);
    //     $data['qsuggestions'] = $this->suggestions_model->read($suggestions_id);
    //     $data['latest_query'] = $this->suggestions_model->getLatestDetail($suggestions_id);

    //     $this->load->view('templat/header');
    //     $this->load->view('asset/css');
    //     $this->load->view('templat/navbar_system_admin');
    //     $this->load->view('system_admin/suggestions_detail', $data);
    //     $this->load->view('asset/js');
    //     $this->load->view('templat/footer');
    // }


    // public function updateStatus($suggestions_detail_case_id)
    // {
    //     // รับข้อมูลจากฟอร์ม
    //     $suggestions_detail_case_id = $this->input->post('suggestions_detail_case_id');
    //     $suggestions_detail_status = $this->input->post('suggestions_detail_status');
    //     $suggestions_detail_com = $this->input->post('suggestions_detail_com');

    //     // เรียกใช้ฟังก์ชัน updatesuggestions
    //     $this->suggestions_model->updatesuggestions($suggestions_detail_case_id, $suggestions_detail_status, $suggestions_detail_com);

    //     // รีเดิร็คหน้าหลังจากทำการบันทึก
    //     redirect('suggestions_backend/detail/' . $suggestions_detail_case_id);
    // }

    // public function statusCancel($suggestions_detail_case_id)
    // {
    //     $suggestions_detail_case_id = $this->input->post('suggestions_detail_case_id');
    //     $suggestions_detail_status = 'ยกเลิก';
    //     $suggestions_detail_com = $this->input->post('suggestions_detail_com'); // รับข้อมูลจาก Swal

    //     // เรียกใช้ Model เพื่ออัปเดตข้อมูล
    //     $this->suggestions_model->statusCancel($suggestions_detail_case_id, $suggestions_detail_status, $suggestions_detail_com);

    //     // รีเดิร็คหน้าหลังจากทำการบันทึก
    //     redirect('suggestions_backend/detail/' . $suggestions_detail_case_id);
    // }

    public function del($suggestions_id)
    {
        // print_r($_POST);
        $this->suggestions_model->del($suggestions_id);
        redirect('suggestions_backend', 'refresh');
    }
}
