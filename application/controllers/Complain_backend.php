<?php
defined('BASEPATH') or exit('No direct script access allowed');

class Complain_backend extends MY_Controller
{

    public function __construct()
    {
        parent::__construct();
         // เช็ค steb 1 ระบบที่เลือกตรงมั้ย
         $this->check_access_permission(['1', '105']); // 1=ทั้งหมด

        $this->load->model('member_model');
        $this->load->model('space_model');
        $this->load->model('complain_model');
		$this->load->library('Notification_lib');
    }

    

public function index()
    {
        $complain_status = $this->input->get('complain_status');
    
        if (!$complain_status) {
            // ถ้าไม่มีการกรองด้วย complain_status ให้ดึงทั้งหมด
            $complains = $this->complain_model->get_complains();
        } else {
            // ถ้ามีการกรองด้วย complain_status ให้ดึงตามเงื่อนไข
            $complains = $this->complain_model->get_complains($complain_status);
        }
    
        foreach ($complains as $complain) {
            $complain->images = $this->complain_model->get_images_for_complain($complain->complain_id);
        }
    
        $this->load->view('templat/header');
        $this->load->view('asset/css');
        $this->load->view('templat/navbar_system_admin');
        $this->load->view('system_admin/complain', ['complains' => $complains]);
        $this->load->view('asset/js');
        $this->load->view('templat/footer');
    }
    

    // public function updateComplainStatus()
    // {
    //     // ตรวจสอบว่ามีการส่งข้อมูล POST มาหรือไม่
    //     if ($this->input->post()) {
    //         $complainId = $this->input->post('complain_id'); // รับค่า complain_id
    //         $newStatus = $this->input->post('new_status'); // รับค่าใหม่จาก dropdown

    //         // ทำการอัพเดตค่าในตาราง tbl_complain ในฐานข้อมูลของคุณ
    //         $data = array(
    //             'complain_status' => $newStatus
    //         );
    //         $this->db->where('complain_id', $complainId); // ระบุ complain_id ของแถวที่ต้องการอัพเดต
    //         $this->db->update('tbl_complain', $data);

    //         // ดึงข้อมูลของ complain_id จากฐานข้อมูล
    //         $complainData = $this->db->get_where('tbl_complain', array('complain_id' => $complainId))->row();
    //         if ($complainData) {
    //             $message = "เรื่องร้องเรียน !" . "\n";
    //             $message .= "case: " . $complainData->complain_id . "\n";
    //             $message .= "สถานะ: " . $newStatus . "\n";
    //             $message .= "เรื่อง: " . $complainData->complain_topic . "\n";
    //             $message .= "รายละเอียด: " . $complainData->complain_detail . "\n";
    //             $message .= "เบอร์โทรศัพท์ผู้แจ้ง: " . $complainData->complain_phone . "\n";
    //             $message .= "ชื่อผู้แจ้ง: " . $complainData->complain_by . "\n";
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

    public function detail($complain_id)
    {
        $data['query'] = $this->complain_model->read_detail($complain_id);
        $data['qcomplain'] = $this->complain_model->read($complain_id);
        $data['latest_query'] = $this->complain_model->getLatestDetail($complain_id);
    
        // เพิ่มบรรทัดนี้เพื่อส่งข้อมูลหัวข้อร้องเรียนไปยัง view
        $data['complain_topic'] = $this->complain_model->get_complain_topic($complain_id);
    
        $this->load->view('templat/header');
        $this->load->view('asset/css');
        $this->load->view('templat/navbar_system_admin');
        $this->load->view('system_admin/complain_detail', $data);
        $this->load->view('asset/js');
        $this->load->view('templat/footer');
    }


          public function updateStatus($complain_detail_case_id)
    {
        // รับข้อมูลจากฟอร์ม
        $complain_detail_case_id = $this->input->post('complain_detail_case_id');
        $complain_detail_status = $this->input->post('complain_detail_status');
        $complain_detail_by = $this->session->userdata('m_fname');
        $complain_detail_com = $this->input->post('complain_detail_com');

        // เรียกใช้ฟังก์ชัน updateComplain
        $this->complain_model->updateComplain($complain_detail_case_id, $complain_detail_status, $complain_detail_com);
			  
			  
	    // เพิ่มการแจ้งเตือน
    $this->Notification_lib->complain_status_updated(
        $complain_detail_case_id, 
        $complain_detail_status, 
        $complain_detail_by
    );

        // ดึงข้อมูลจาก tbl_complain หลังจากอัปเดต
        $complainData = $this->db->get_where('tbl_complain', array('complain_id' => $complain_detail_case_id))->row();

        // ดึงข้อมูลจาก tbl_email
        $emailData = $this->db->where('email_status', '1')->get('tbl_email')->result();

        // ตรวจสอบว่า $emailData มีข้อมูลหรือไม่
        if (!empty($emailData)) {
            // สร้าง Array ของอีเมล
            $email_list = [];
            foreach ($emailData as $email) {
                $email_list[] = $email->email_name; // ฟิลด์ชื่อ email_name
            }
 
            // เปลี่ยน Array เป็น Comma-Separated String
            $email_list_str = implode(', ', $email_list);

            // โหลด email library
            $this->load->library('email');

            $domain = get_config_value('domain');

            // ตั้งค่าการส่งอีเมล
            $this->email->set_mailtype('html');
            $this->email->set_newline("\r\n");
            $this->email->from('no-reply@'. $domain .'.go.th', 'Admin');
            $this->email->to($email_list_str); // ใช้ comma-separated string

            $this->email->subject('อัพเดตเรื่องร้องเรียน');

            // ส่งอีเมล
            $this->email->message(
                'เลขเคส : ' . $complain_detail_case_id . '<br>' .
                    'สถานะ : ' . $complainData->complain_status . '<br>' .
                    'เรื่อง : ' . $complainData->complain_topic . '<br>' .
                    'รายละเอียด : ' . $complainData->complain_detail . '<br>' .
                    'ผู้แจ้งเรื่อง : ' . $complainData->complain_by . '<br>' .
                    'เบอร์โทรศัพท์ผู้แจ้ง : ' . $complainData->complain_phone . '<br>' .
                    'ที่อยู่ : ' . $complainData->complain_address . '<br>' .
                    'อีเมล : ' . $complainData->complain_email . '<br>' .
                    'ชื่อผู้อัพเดต : ' . $complain_detail_by . '<br>' .
                    'ข้อความ : ' . $complain_detail_com
            );

            // ส่งอีเมล
            if ($this->email->send()) {
                echo 'อีเมลถูกส่งสำเร็จ';
            } else {
                echo 'เกิดข้อผิดพลาดในการส่งอีเมล';
                echo $this->email->print_debugger(); // แสดงข้อผิดพลาดในการส่งอีเมล
            }
        } else {
            // ข้ามการทำงานการส่งอีเมลหากไม่มีอีเมลในฐานข้อมูล
            echo 'ไม่มีอีเมลที่ต้องการส่ง';
        }
        // รีเดิร็คหน้าหลังจากทำการบันทึก
        redirect('Complain_backend/detail/' . $complain_detail_case_id);
    }

    public function statusCancel($complain_detail_case_id)
    {
        $complain_detail_case_id = $this->input->post('complain_detail_case_id');
        $complain_detail_status = 'ยกเลิก';
        $complain_detail_com = $this->input->post('complain_detail_com'); // รับข้อมูลจาก Swal

        // เรียกใช้ Model เพื่ออัปเดตข้อมูล
        $this->complain_model->statusCancel($complain_detail_case_id, $complain_detail_status, $complain_detail_com);

        // รีเดิร็คหน้าหลังจากทำการบันทึก
        redirect('complain_backend/detail/' . $complain_detail_case_id);
    }
}
