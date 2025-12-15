<?php
defined('BASEPATH') or exit('No direct script access allowed');

class Activity_backend extends MY_Controller
{

    public function __construct()
    {
        parent::__construct();
        // เช็ค steb 1 ระบบที่เลือกตรงมั้ย
        $this->check_access_permission(['1', '21']); // 1=ทั้งหมด

        $this->load->model('member_model');
        $this->load->model('space_model');
        $this->load->model('activity_model');
    }

    

    public function index()
    {
        $data['qadmin'] = $this->activity_model->list_admin();
        $data['qimg'] = $this->activity_model->list_admin();
        // $data['quser'] = $this->activity_model->list_user();
        // $data['query'] = $this->activity_model->list_all();
        $data['used_space_mb'] = $this->space_model->get_used_space();
        // $data['upload_limit_mb'] = 35;
        $data['upload_limit_mb'] = $this->session->userdata('upload_limit_mb') ?? 35; // ตั้งค่าเริ่มต้นเป็น 35 MB

        $this->load->view('templat/header');
        $this->load->view('asset/css');
        $this->load->view('templat/navbar_system_admin');
        $this->load->view('system_admin/activity', $data);
        $this->load->view('asset/js');
        $this->load->view('asset/option_js');
        $this->load->view('templat/footer');
    }

    public function adding_Activity()
    {
        $this->load->view('templat/header');
        $this->load->view('asset/css');
        $this->load->view('templat/navbar_system_admin');
        $this->load->view('system_admin/activity_form_add');
        $this->load->view('asset/js');
        $this->load->view('templat/footer');
    }

    public function add_Activity()
    {
        // echo '<pre>';
        // print_r($_POST);
        // echo '</pre>';
        // exit;
        $this->activity_model->add_Activity();
        redirect('Activity_backend', 'refresh');
    }

    public function editing_Activity($activity_id)
    {
        $data['rsedit'] = $this->activity_model->read_activity($activity_id);
        $data['qimg'] = $this->activity_model->read_img_activity($activity_id);

        // echo '<pre>';
        // print_r($data['rsedit']);
        // echo '</pre>';
        // exit();

        $this->load->view('templat/header');
        $this->load->view('asset/css');
        $this->load->view('templat/navbar_system_admin');
        $this->load->view('system_admin/activity_form_edit', $data);
        $this->load->view('asset/js');
        $this->load->view('templat/footer');
    }

    public function edit_Activity($activity_id)
    {
        $this->activity_model->edit_Activity($activity_id);
        redirect('Activity_backend', 'refresh');
    }

    public function del_activity($activity_id)
    {
        $this->activity_model->del_activity_img($activity_id);
        $this->activity_model->del_activity($activity_id);
        $this->session->set_flashdata('del_success', TRUE);
        redirect('Activity_backend', 'refresh');
    }

    public function updateActivityStatus()
    {
        $this->activity_model->updateActivityStatus();
    }

    public function editing_User_Activity($user_travel_id)
    {
        $data['rsedit'] = $this->activity_model->read_user_activity($user_travel_id);
        $data['qimg'] = $this->activity_model->read_user_img_activity($user_travel_id);

        // echo '<pre>';
        // print_r($data['rsedit']);
        // echo '</pre>';
        // exit();

        $this->load->view('templat/header');
        $this->load->view('asset/css');
        $this->load->view('templat/navbar_system_admin');
        $this->load->view('system_admin/activity_user_form_edit', $data);
        $this->load->view('asset/js');
        $this->load->view('templat/footer');
    }
    public function edit_User_Activity($user_activity_id)
    {
        $user_activity_name = $this->input->post('user_activity_name');
        $user_activity_detail = $this->input->post('user_activity_detail');
        $user_activity_phone = $this->input->post('user_activity_phone');

        $this->activity_model->edit_User_Activity($user_activity_id, $user_activity_name, $user_activity_detail, $user_activity_phone);
        redirect('Activity_backend', 'refresh');
    }

    public function del_User_Activity($user_activity_id)
    {
        $this->activity_model->del_user_activity_img($user_activity_id);
        $this->activity_model->del_user_activity($user_activity_id);
        $this->session->set_flashdata('del_success', TRUE);
        redirect('Activity_backend', 'refresh');
    }

    public function updateUserActivityStatus()
    {
        $this->activity_model->updateUserActivityStatus();
    }

    public function com($activity_id)
    {
        // อ่านข้อมูลเมนูอาหาร
        $data['activity'] = $this->activity_model->read_activity($activity_id);

        if (!empty($data['activity'])) {
            // อ่านข้อมูลความคิดเห็นที่เกี่ยวข้องกับอาหาร
            $data['rsCom'] = $this->activity_model->read_com_activity($activity_id);

            // อ่านข้อมูลความคิดเห็นตอบกลับที่เกี่ยวข้องกับความคิดเห็น
            foreach ($data['rsCom'] as $index => $com) {
                $activity_com_id = $com->activity_com_id;
                $com_reply_data = $this->activity_model->read_com_reply_activity($activity_com_id);

                // เก็บข้อมูลความคิดเห็นตอบกลับลงในอาร์เรย์ของความคิดเห็น
                $data['rsCom'][$index]->com_reply_data = $com_reply_data;
            }
            // echo '<pre>';
            // print_r($data['rsCom']);
            // echo '</pre>';
            // exit();
            // โหลดหน้า view และแสดงผล HTML
            $this->load->view('templat/header');
            $this->load->view('asset/css');
            $this->load->view('templat/navbar_system_admin');
            $this->load->view('system_admin/activity_com', $data);
            $this->load->view('asset/js');
            $this->load->view('templat/footer');
        } else {
            // หากไม่พบข้อมูลให้แสดงข้อความผิดพลาดหรือกระทำอื่นตามที่คุณต้องการ
            // ตัวอย่างเช่นแสดงข้อความผิดพลาด
            echo "ไม่พบข้อมูลอาหารที่เกี่ยวข้อง";
        }
    }

    public function del_com($activity_com_id)
    {
        $this->activity_model->del_reply($activity_com_id);
        $this->activity_model->del_com($activity_com_id);

        // ส่งคำตอบในรูปแบบ JSON เพื่อระบุว่าการลบสำเร็จ
        $response = array('success' => true);
        header('Content-Type: application/json');
        echo json_encode($response);
        $this->session->set_flashdata('del_success', TRUE);
    }

    public function del_com_reply($activity_com_reply_id)
    {
        $this->activity_model->del_com_reply($activity_com_reply_id);

        // ส่งคำตอบในรูปแบบ JSON เพื่อระบุว่าการลบสำเร็จ
        $response = array('success' => true);
        header('Content-Type: application/json');
        echo json_encode($response);
        $this->session->set_flashdata('del_success', TRUE);
    }

    public function com_user($user_activity_id)
    {
        // อ่านข้อมูลเมนูอาหาร
        $data['user_activity'] = $this->activity_model->read_user_activity($user_activity_id);

        if (!empty($data['user_activity'])) {
            // อ่านข้อมูลความคิดเห็นที่เกี่ยวข้องกับอาหาร
            $data['user_rsCom'] = $this->activity_model->read_user_com_activity($user_activity_id);

            // อ่านข้อมูลความคิดเห็นตอบกลับที่เกี่ยวข้องกับความคิดเห็น
            foreach ($data['user_rsCom'] as $index => $user_com) {
                $user_activity_com_id = $user_com->user_activity_com_id;
                $user_com_reply_data = $this->activity_model->read_user_com_reply_activity($user_activity_com_id);

                // เก็บข้อมูลความคิดเห็นตอบกลับลงในอาร์เรย์ของความคิดเห็น
                $data['user_rsCom'][$index]->user_com_reply_data = $user_com_reply_data;
            }

            // echo '<pre>';
            // print_r($data['user_rsCom']);
            // echo '</pre>';
            // exit();

            // โหลดหน้า view และแสดงผล HTML
            $this->load->view('templat/header');
            $this->load->view('asset/css');
            $this->load->view('templat/navbar_system_admin');
            $this->load->view('system_admin/activity_user_com', $data);
            $this->load->view('asset/js');
            $this->load->view('templat/footer');
        } else {
            // หากไม่พบข้อมูลให้แสดงข้อความผิดพลาดหรือกระทำอื่นตามที่คุณต้องการ
            // ตัวอย่างเช่นแสดงข้อความผิดพลาด
            echo "ไม่พบข้อมูลอาหารที่เกี่ยวข้อง";
        }
    }

    public function del_com_user($user_activity_com_id)
    {
        $this->activity_model->del_reply_user($user_activity_com_id);
        $this->activity_model->del_com_user($user_activity_com_id);

        // ส่งคำตอบในรูปแบบ JSON เพื่อระบุว่าการลบสำเร็จ
        $response = array('success' => true);
        header('Content-Type: application/json');
        echo json_encode($response);
        $this->session->set_flashdata('del_success', TRUE);
    }

    public function del_com_reply_user($user_activity_com_reply_id)
    {
        $this->activity_model->del_com_reply_user($user_activity_com_reply_id);

        // ส่งคำตอบในรูปแบบ JSON เพื่อระบุว่าการลบสำเร็จ
        $response = array('success' => true);
        header('Content-Type: application/json');
        echo json_encode($response);
        $this->session->set_flashdata('del_success', TRUE);
    }

    public function del_img($file_id)
    {
        // เรียกใช้ฟังก์ชันใน Model เพื่อลบไฟล์ PDF ด้วย $file_id
        $this->activity_model->del_img($file_id);

        // ใส่สคริปต์ JavaScript เพื่อรีเฟรชหน้าเดิม
        echo '<script>window.history.back();</script>';
    }

    public function delete_multiple_images()
    {
        // รับข้อมูล JSON จาก request
        $json_input = file_get_contents('php://input');
        $json_data = json_decode($json_input, true);

        // Debug: บันทึกข้อมูลที่ได้รับ
        log_message('debug', 'Received data for delete_multiple_images: ' . print_r($json_data, true));

        // ตรวจสอบว่ามีข้อมูลที่จำเป็นหรือไม่
        if (!isset($json_data['image_ids']) || empty($json_data['image_ids'])) {
            echo json_encode([
                'success' => false,
                'message' => 'ไม่พบข้อมูลรูปภาพที่ต้องการลบ'
            ]);
            return;
        }

        $image_ids = $json_data['image_ids'];
        $activity_id = isset($json_data['activity_id']) ? $json_data['activity_id'] : null;

        // Debug: แสดงข้อมูล IDs ที่จะลบ
        log_message('debug', 'Image IDs to delete: ' . implode(', ', $image_ids));
        log_message('debug', 'Activity ID: ' . $activity_id);

        // ดำเนินการลบรูปภาพ
        $deleted_count = 0;

        // เริ่ม Transaction
        $this->db->trans_start();

        foreach ($image_ids as $img_id) {
            // ดึงข้อมูลรูปภาพ
            $this->db->select('activity_img_img');
            $this->db->where('activity_img_id', $img_id);
            $query = $this->db->get('tbl_activity_img');
            $img_data = $query->row();

            if ($img_data) {
                // ลบไฟล์รูปภาพ
                $img_path = './docs/img/' . $img_data->activity_img_img;
                if (file_exists($img_path)) {
                    unlink($img_path);
                }

                // ลบข้อมูลจากฐานข้อมูล
                $this->db->where('activity_img_id', $img_id);
                $this->db->delete('tbl_activity_img');

                $deleted_count++;
            }
        }

        // อัพเดตข้อมูลพื้นที่เก็บข้อมูล
        $this->space_model->update_server_current();

        // สิ้นสุด Transaction
        $this->db->trans_complete();

        $response = [];

        if ($this->db->trans_status() === FALSE) {
            $response = [
                'success' => false,
                'message' => 'เกิดข้อผิดพลาดในการลบรูปภาพ'
            ];
        } else {
            $response = [
                'success' => true,
                'message' => 'ลบรูปภาพจำนวน ' . $deleted_count . ' รูปเรียบร้อยแล้ว',
                'count' => $deleted_count
            ];
        }

        echo json_encode($response);
    }

    public function upload_images_ajax($activity_id)
    {
        // ตรวจสอบว่ามีไฟล์ถูกอัพโหลดหรือไม่
        if (empty($_FILES['files']['name'][0])) {
            echo json_encode([
                'success' => false,
                'message' => 'กรุณาเลือกรูปภาพที่ต้องการอัพโหลด'
            ]);
            return;
        }

        // ตรวจสอบกิจกรรม
        $activity = $this->activity_model->read_activity($activity_id);
        if (!$activity) {
            echo json_encode([
                'success' => false,
                'message' => 'ไม่พบข้อมูลกิจกรรมที่ต้องการอัพโหลดรูปภาพ'
            ]);
            return;
        }

        // ตรวจสอบพื้นที่เก็บข้อมูล
        $used_space_mb = $this->space_model->get_used_space();
        $upload_limit_mb = $this->space_model->get_limit_storage();

        $total_space_required = 0;
        foreach ($_FILES['files']['size'] as $size) {
            $total_space_required += $size;
        }

        // ตรวจสอบพื้นที่ว่าเพียงพอหรือไม่
        if ($used_space_mb + ($total_space_required / (1024 * 1024 * 1024)) >= $upload_limit_mb) {
            echo json_encode([
                'success' => false,
                'message' => 'พื้นที่เก็บข้อมูลไม่เพียงพอ กรุณาติดต่อผู้ดูแลระบบ'
            ]);
            return;
        }

        // ตั้งค่าการอัพโหลด
        $config['upload_path'] = './docs/img';
        $config['allowed_types'] = 'gif|jpg|png|jpeg|jfif';
        $config['max_size'] = 10240; // 10MB
        $this->load->library('upload', $config);

        // เริ่ม Transaction
        $this->db->trans_start();

        $uploaded_images = [];
        $success_count = 0;
        $error_files = [];

        // วนลูปอัพโหลดทีละไฟล์
        foreach ($_FILES['files']['name'] as $index => $name) {
            // สร้างชื่อไฟล์ใหม่เพื่อป้องกันชื่อซ้ำ
            $img_name = pathinfo($name, PATHINFO_FILENAME);
            $img_ext = pathinfo($name, PATHINFO_EXTENSION);
            $new_img_name = $img_name . '_' . time() . rand(1000, 9999) . '.' . $img_ext;

            // ตั้งค่า $_FILES สำหรับไฟล์นี้
            $_FILES['file']['name'] = $new_img_name;
            $_FILES['file']['type'] = $_FILES['files']['type'][$index];
            $_FILES['file']['tmp_name'] = $_FILES['files']['tmp_name'][$index];
            $_FILES['file']['error'] = $_FILES['files']['error'][$index];
            $_FILES['file']['size'] = $_FILES['files']['size'][$index];

            // อัพโหลดไฟล์
            if ($this->upload->do_upload('file')) {
                $upload_data = $this->upload->data();

                // หาค่า order ล่าสุด
                $this->db->select_max('activity_img_order');
                $this->db->where('activity_img_ref_id', $activity_id);
                $max_order_query = $this->db->get('tbl_activity_img');
                $max_order = $max_order_query->row();

                $next_order = 1; // เริ่มต้นที่ 1
                if ($max_order && isset($max_order->activity_img_order)) {
                    $next_order = (int)$max_order->activity_img_order + 1;
                }

                // เพิ่มข้อมูลลงในฐานข้อมูล
                $image_data = [
                    'activity_img_ref_id' => $activity_id,
                    'activity_img_img' => $upload_data['file_name'],
                    'activity_img_order' => $next_order
                ];

                $this->db->insert('tbl_activity_img', $image_data);
                $img_id = $this->db->insert_id();

                // เพิ่มข้อมูลรูปที่อัพโหลดสำเร็จลงในอาเรย์
                $uploaded_images[] = [
                    'id' => $img_id,
                    'name' => $upload_data['file_name'],
                    'url' => base_url('docs/img/' . $upload_data['file_name']),
                    'order' => $next_order
                ];

                $success_count++;
            } else {
                $error_files[] = $name . ' (' . $this->upload->display_errors('', '') . ')';
            }
        }

        // อัพเดตข้อมูลพื้นที่เก็บข้อมูล
        $this->space_model->update_server_current();

        // สิ้นสุด Transaction
        $this->db->trans_complete();

        // ตรวจสอบผลลัพธ์และส่งค่ากลับ
        if ($this->db->trans_status() === FALSE) {
            echo json_encode([
                'success' => false,
                'message' => 'เกิดข้อผิดพลาดในการบันทึกข้อมูล กรุณาลองใหม่อีกครั้ง'
            ]);
        } else {
            if ($success_count > 0) {
                $message = 'อัพโหลดรูปภาพสำเร็จ ' . $success_count . ' รูป';
                if (!empty($error_files)) {
                    $message .= ' (มีบางรูปที่ไม่สำเร็จ: ' . implode(', ', $error_files) . ')';
                }

                echo json_encode([
                    'success' => true,
                    'message' => $message,
                    'images' => $uploaded_images
                ]);
            } else {
                echo json_encode([
                    'success' => false,
                    'message' => 'ไม่สามารถอัพโหลดรูปภาพได้: ' . implode(', ', $error_files)
                ]);
            }
        }
    }
}
