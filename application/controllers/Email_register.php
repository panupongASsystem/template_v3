<?php
defined('BASEPATH') or exit('No direct script access allowed');

class Email_register extends MY_Controller
{

    public function __construct()
    {
        parent::__construct();
        // เช็ค steb 1 ระบบที่เลือกตรงมั้ย
        $this->check_access_permission(['1', '105']); // 1=ทั้งหมด

        $this->load->model('user_log_model');
    }

    

    public function index()
    {
        // ดึงข้อมูลอีเมลทั้งหมด
        $data['emails'] = $this->user_log_model->list_email();
        $data['line_notification_status'] = $this->user_log_model->get_setting('line_notification_status');


        // โหลดหน้า view
        $this->load->view('templat/header');
        $this->load->view('asset/css');
        $this->load->view('templat/navbar_system_admin');
        $this->load->view('system_admin/email_register', $data);
        $this->load->view('asset/js');
        $this->load->view('templat/footer');
    }

    public function add()
    {
        // ตรวจสอบการส่งข้อมูล
        if ($this->input->post('email_name')) {
            // เรียกใช้ฟังก์ชัน add_email จาก model
            $this->user_log_model->add_email();
            redirect('Email_register');
        } else {
            // แสดงแบบฟอร์ม
            $this->load->view('templat/header');
            $this->load->view('asset/css');
            $this->load->view('templat/navbar_system_admin');
            $this->load->view('system_admin/email_register_form');
            $this->load->view('asset/js');
            $this->load->view('templat/footer');
        }
    }

    public function edit($email_id)
    {
        // ตรวจสอบการส่งข้อมูล
        if ($this->input->post('email_name')) {
            // เรียกใช้ฟังก์ชัน edit_email จาก model
            $this->user_log_model->edit_email($email_id);
            redirect('Email_register');
        } else {
            // ดึงข้อมูลอีเมลที่ต้องการแก้ไข
            $data['email'] = $this->user_log_model->read_email($email_id);

            // แสดงแบบฟอร์ม
            $this->load->view('templat/header');
            $this->load->view('asset/css');
            $this->load->view('templat/navbar_system_admin');
            $this->load->view('system_admin/email_register_edit', $data);
            $this->load->view('asset/js');
            $this->load->view('templat/footer');
        }
    }

    public function delete($email_id)
    {
        // เรียกใช้ฟังก์ชัน del_email จาก model
        $this->user_log_model->del_email($email_id);
        redirect('Email_register');
    }

    public function update_status()
    {
        // ตรวจสอบการส่งข้อมูล
        if ($this->input->post()) {
            // เรียกใช้ฟังก์ชัน updateEmailStatus จาก model
            $this->user_log_model->updateEmailStatus();
        }
    }

    public function update_status_all()
    {
        // ตรวจสอบการส่งข้อมูล
        if ($this->input->post('new_status')) {
            $new_status = $this->input->post('new_status');
            // เรียกใช้ฟังก์ชัน updateEmailStatusAll จาก model
            $result = $this->user_log_model->updateEmailStatusAll($new_status);

            $response = array(
                'status' => $result ? 'success' : 'error',
                'message' => $result ? 'อัพเดตสถานะทั้งหมดเรียบร้อย' : 'เกิดข้อผิดพลาดในการอัพเดตสถานะ'
            );

            echo json_encode($response);
        }
    }

    public function test_email()
    {
        // ตรวจสอบว่ามีอีเมลที่เปิดใช้งานหรือไม่
        $this->db->where('email_status', '1');
        $active_emails = $this->db->count_all_results('tbl_email');

        if ($active_emails == 0) {
            $this->session->set_flashdata('error', 'ไม่มีอีเมลที่เปิดใช้งาน กรุณาเปิดใช้งานอีเมลอย่างน้อย 1 รายการ');
            redirect('Email_register');
            return;
        }

        // ส่งอีเมลทดสอบ
        $subject = 'ทดสอบระบบแจ้งเตือนทางอีเมล';
        $message = "นี่เป็นอีเมลทดสอบจากระบบแจ้งเตือนความปลอดภัย\n";
        $message .= "วันที่และเวลาทดสอบ: " . date('Y-m-d H:i:s') . "\n";
        $message .= "ผู้ทดสอบ: " . $this->session->userdata('m_fname') . ' ' . $this->session->userdata('m_lname');

        $result = $this->user_log_model->send_line_email($subject, $message);

        if ($result) {
            $this->session->set_flashdata('success', 'ส่งอีเมลทดสอบเรียบร้อยแล้ว');
        } else {
            $this->session->set_flashdata('error', 'เกิดข้อผิดพลาดในการส่งอีเมลทดสอบ');
        }

        redirect('Email_register');
    }

    /**
     * สำหรับอัพเดตสถานะการแจ้งเตือน Line OA
     */
    public function update_notification_status()
    {
        if ($this->input->post()) {
            $new_status = $this->input->post('status');
            $result = $this->user_log_model->update_setting(
                'line_notification_status',
                $new_status,
                $this->session->userdata('m_fname')
            );

            $response = array(
                'status' => $result ? 'success' : 'error',
                'message' => $result ? 'อัพเดตการตั้งค่าเรียบร้อย' : 'เกิดข้อผิดพลาดในการอัพเดตการตั้งค่า'
            );

            echo json_encode($response);
        }
    }

    /**
 * สำหรับทดสอบการส่งข้อความแจ้งเตือน Line OA (ส่งไปทั้งสองกลุ่ม)
 */
	public function test_line_notification()
    {
        // ตรวจสอบว่าการแจ้งเตือนถูกเปิดใช้งานหรือไม่
        $notification_status = $this->user_log_model->get_setting('line_notification_status');

        if ($notification_status != '1') {
            $response = array(
                'status' => 'error',
                'message' => 'การแจ้งเตือน Line OA ปิดใช้งานอยู่ กรุณาเปิดใช้งานก่อนทดสอบ'
            );

            echo json_encode($response);
            return;
        }

        // สร้างข้อความทดสอบ
        $message = "🔔 ทดสอบการแจ้งเตือน Line OA\n";
        $message .= "ระบบแจ้งเตือนความปลอดภัย\n";
        $message .= "-------------------------------\n";
        $message .= "👤 ผู้ทดสอบ: " . $this->session->userdata('m_fname') . ' ' . $this->session->userdata('m_lname') . "\n";
        $message .= "⏰ เวลา: " . date('Y-m-d H:i:s') . "\n";
        $message .= "📱 ส่งไปยัง: กลุ่มผู้ดูแลระบบ + กลุ่มลูกค้า\n";
        $message .= "✅ นี่เป็นข้อความทดสอบจากระบบ";

        // === ส่งไปกลุ่มผู้ดูแลระบบ (ฟังก์ชันที่ใช้เมื่อ login ผิด) ===
        $admin_result = $this->user_log_model->send_line_alert($message);

        // === ส่งไปกลุ่มลูกค้า ===
        $customer_result = $this->user_log_model->send_line_customer($message);

        // ตรวจสอบผลลัพธ์และสร้างข้อความตอบกลับ
        $success_groups = [];
        $failed_groups = [];

        if ($admin_result) {
            $success_groups[] = "กลุ่มผู้ดูแลระบบ";
        } else {
            $failed_groups[] = "กลุ่มผู้ดูแลระบบ";
        }

        if ($customer_result) {
            $success_groups[] = "กลุ่มลูกค้า";
        } else {
            $failed_groups[] = "กลุ่มลูกค้า";
        }

        // สร้างข้อความตอบกลับ
        if (count($success_groups) > 0) {
            $message_text = "ส่งข้อความทดสอบสำเร็จไปยัง: " . implode(", ", $success_groups);

            if (count($failed_groups) > 0) {
                $message_text .= " | ล้มเหลว: " . implode(", ", $failed_groups);
            }

            $response = array(
                'status' => 'success',
                'message' => $message_text
            );
        } else {
            $response = array(
                'status' => 'error',
                'message' => 'เกิดข้อผิดพลาดในการส่งข้อความทดสอบไปทั้งสองกลุ่ม'
            );
        }

        // บันทึก log สำหรับการดีบัก
        log_message('debug', 'LINE Test Results:');
        log_message('debug', '- Admin group (send_line_alert): ' . ($admin_result ? 'SUCCESS' : 'FAILED'));
        log_message('debug', '- Customer group (send_line_customer): ' . ($customer_result ? 'SUCCESS' : 'FAILED'));

        echo json_encode($response);
    }
}
