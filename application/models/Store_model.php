<?php
class Store_model extends CI_Model
{
    public function __construct()
    {
        parent::__construct();
        $this->load->model('space_model');
    }

    public function add_store()
    {
        // Check used space
        $used_space_mb = $this->space_model->get_used_space();
        $upload_limit_mb = $this->space_model->get_limit_storage();

        $total_space_required = 0;
        $store_imgs = $_FILES['store_imgs'];

        foreach ($store_imgs['size'] as $size) {
            $total_space_required += $size;
        }

        // Check if there's enough space
        if ($used_space_mb + ($total_space_required / (1024 * 1024 * 1024)) >= $upload_limit_mb) {
            $this->session->set_flashdata('save_error', TRUE);
            redirect('store/adding_store');
            return;
        }

        $store_data = array(
            'store_type' => $this->input->post('store_type'),
            'store_name' => $this->input->post('store_name'),
            'store_date' => $this->input->post('store_date'),
            'store_timeopen' => $this->input->post('store_timeopen'),
            'store_timeclose' => $this->input->post('store_timeclose'),
            'store_phone' => $this->input->post('store_phone'),
            'store_detail' => $this->input->post('store_detail'),
            'store_location' => $this->input->post('store_location'),
            'store_lat' => $this->input->post('store_lat'),
            'store_long' => $this->input->post('store_long'),
            'store_by' => $this->session->userdata('m_fname') // เพิ่มชื่อคนที่แก้ไขข้อมูล
        );

        $config['upload_path'] = './docs/img';
        $config['allowed_types'] = 'gif|jpg|png|jpeg|jfif';
        $this->load->library('upload', $config);

        $store_img = $_FILES['store_img'];
        $store_imgs = $_FILES['store_imgs'];

        $this->db->trans_start();
        $this->db->insert('tbl_store', $store_data);
        $store_id = $this->db->insert_id();

        // Upload and update store_img
        $_FILES['store_img']['name'] = $store_img['name'];
        $_FILES['store_img']['type'] = $store_img['type'];
        $_FILES['store_img']['tmp_name'] = $store_img['tmp_name'];
        $_FILES['store_img']['error'] = $store_img['error'];
        $_FILES['store_img']['size'] = $store_img['size'];

        if (!$this->upload->do_upload('store_img')) {
            $this->session->set_flashdata('save_maxsize', TRUE);
            redirect('store/adding_store'); // กลับไปหน้าเดิม
            return;
        }

        $upload_data = $this->upload->data();
        $store_img_file = $upload_data['file_name'];

        // Update store_img column with the uploaded image
        $store_img_data = array('store_img' => $store_img_file);
        $this->db->where('store_id', $store_id);
        $this->db->update('tbl_store', $store_img_data);

        // Upload and insert data into tbl_store_img
        $image_data = array(); // Initialize the array
        foreach ($store_imgs['name'] as $index => $name) {
            $_FILES['store_img']['name'] = $name;
            $_FILES['store_img']['type'] = $store_imgs['type'][$index];
            $_FILES['store_img']['tmp_name'] = $store_imgs['tmp_name'][$index];
            $_FILES['store_img']['error'] = $store_imgs['error'][$index];
            $_FILES['store_img']['size'] = $store_imgs['size'][$index];

            if (!$this->upload->do_upload('store_img')) {
                $this->session->set_flashdata('save_maxsize', TRUE);
                redirect('store/adding_store'); // กลับไปหน้าเดิม
                return;
            }

            $upload_data = $this->upload->data();
            $image_data[] = array(
                'store_img_ref_id' => $store_id,
                'store_img_img' => $upload_data['file_name']
            );
        }

        $this->db->insert_batch('tbl_store_img', $image_data);

        $this->db->trans_complete();

        $latitude = $store_data['store_lat'];
        $longitude = $store_data['store_long'];

        $map_link = "https://www.google.com/maps?q=$latitude,$longitude";

        // สร้างข้อมูลที่คุณต้องการส่งใน $message
        $message = "ร้านค้าและบริการใหม่: แอดมิน" . "\n";
        $message .= "ประเภท: " . $store_data['store_type'] . "\n";
        $message .= "ชือร้าน: " . $store_data['store_name'] . "\n";
        $message .= "เบอร์โทร: " . $store_data['store_phone'] . "\n";
        $message .= "สถานที่: " . $store_data['store_location'] . "\n";
        $message .= "พิกัด: ($map_link)\n";
        // เรียกใช้ฟังก์ชันส่งข้อมูล LINE Notify โดยใส่ $message
        $this->sendLineNotify($message);

        $this->space_model->update_server_current();
        $this->session->set_flashdata('save_success', TRUE);
    }

    private function sendLineNotify($message)
    {
        define('LINE_API', "https://notify-api.line.me/api/notify");
        $token = "BvFE3KkynASdlXVVLQaArCBz71iyxDbE6fqvDgY6iVT"; // ใส่ Token ที่คุณได้รับ

        $queryData = array('message' => $message);
        $queryData = http_build_query($queryData, '', '&');
        $headerOptions = array(
            'http' => array(
                'method' => 'POST',
                'header' => "Content-Type: application/x-www-form-urlencoded\r\n" .
                    "Authorization: Bearer " . $token . "\r\n" .
                    "Content-Length: " . strlen($queryData) . "\r\n",
                'content' => $queryData
            ),
        );

        $context = stream_context_create($headerOptions);
        $result = file_get_contents(LINE_API, FALSE, $context);
        $res = json_decode($result);
    }


    public function list_admin()
    {
        $this->db->select('s.*, GROUP_CONCAT(si.store_img_img) as additional_images');
        $this->db->from('tbl_store as s');
        $this->db->join('tbl_store_img as si', 's.store_id = si.store_img_ref_id', 'left');
        $this->db->group_by('s.store_id');
        $this->db->order_by('s.store_id', 'DESC');
        $query = $this->db->get();
        return $query->result();
    }

    public function list_user()
    {
        $this->db->select('s.*, GROUP_CONCAT(si.user_store_img_img) as additional_images');
        $this->db->from('tbl_user_store as s');
        $this->db->join('tbl_user_store_img as si', 's.user_store_id = si.user_store_img_ref_id', 'left');
        $this->db->group_by('s.user_store_id');
        $this->db->order_by('s.user_store_id', 'DESC');
        $query = $this->db->get();
        return $query->result();
    }

    //show form edit
    public function read_store($store_id)
    {
        $this->db->where('store_id', $store_id);
        $query = $this->db->get('tbl_store');
        if ($query->num_rows() > 0) {
            $data = $query->row();
            return $data;
        }
        return FALSE;
    }

    public function read_img_store($store_id)
    {
        $this->db->where('store_img_ref_id', $store_id);
        $this->db->order_by('store_img_id', 'DESC');
        $query = $this->db->get('tbl_store_img');
        return $query->result();
    }

    public function read_com_store($store_id)
    {
        $this->db->where('store_com_ref_id', $store_id);
        $this->db->order_by('store_com_ref_id', 'DESC');
        $query = $this->db->get('tbl_store_com');
        return $query->result();
    }

    public function read_com_reply_store($store_com_id)
    {
        $this->db->where('store_com_reply_ref_id', $store_com_id);
        $query = $this->db->get('tbl_store_com_reply');
        return $query->result();
    }

    public function edit_store($store_id)
    {
        $old_document = $this->db->get_where('tbl_store', array('store_id' => $store_id))->row();

        $update_doc_file = !empty($_FILES['store_img']['name']) && $old_document->store_img != $_FILES['store_img']['name'];

        // ตรวจสอบว่ามีการอัพโหลดรูปภาพใหม่หรือไม่
        if ($update_doc_file) {
            $old_file_path = './docs/img/' . $old_document->store_img;
            if (file_exists($old_file_path)) {
                unlink($old_file_path);
            }

            // Check used space
            $used_space_mb = $this->space_model->get_used_space();
            $upload_limit_mb = $this->space_model->get_limit_storage();

            $total_space_required = 0;
            if (!empty($_FILES['store_img']['name'])) {
                $total_space_required += $_FILES['store_img']['size'];
            }

            if ($used_space_mb + ($total_space_required / (1024 * 1024 * 1024)) >= $upload_limit_mb) {
                $this->session->set_flashdata('save_error', TRUE);
                redirect('store/editing_store');
                return;
            }

            $config['upload_path'] = './docs/img';
            $config['allowed_types'] = 'gif|jpg|png|jpeg|jfif';
            $this->load->library('upload', $config);

            if (!$this->upload->do_upload('store_img')) {
                echo $this->upload->display_errors();
                return;
            }

            $data = $this->upload->data();
            $filename = $data['file_name'];
        } else {
            // ใช้รูปภาพเดิม
            $filename = $old_document->store_img;
        }

        // Update store information
        $data = array(
            'store_type' => $this->input->post('store_type'),
            'store_name' => $this->input->post('store_name'),
            'store_date' => $this->input->post('store_date'),
            'store_timeopen' => $this->input->post('store_timeopen'),
            'store_timeclose' => $this->input->post('store_timeclose'),
            'store_phone' => $this->input->post('store_phone'),
            'store_detail' => $this->input->post('store_detail'),
            'store_location' => $this->input->post('store_location'),
            'store_lat' => $this->input->post('store_lat'),
            'store_long' => $this->input->post('store_long'),
            'store_by' => $this->session->userdata('m_fname'), // เพิ่มชื่อคนที่แก้ไขข้อมูล
            'store_img' => $filename
        );

        $this->db->where('store_id', $store_id);
        $this->db->update('tbl_store', $data);

        // อัปโหลดและบันทึกไฟล์ใหม่ลงโฟลเดอร์
        if (!empty($_FILES['store_img_img']['name'])) {
            $upload_config['upload_path'] = './docs/img';
            $upload_config['allowed_types'] = 'gif|jpg|png|jpeg|jfif';
            $this->load->library('upload', $upload_config);

            $upload_success = true; // ตั้งค่าเริ่มต้นเป็น true

            foreach ($_FILES['store_img_img']['name'] as $index => $name) {
                $_FILES['store_img']['name'] = $name;
                $_FILES['store_img']['type'] = $_FILES['store_img_img']['type'][$index];
                $_FILES['store_img']['tmp_name'] = $_FILES['store_img_img']['tmp_name'][$index];
                $_FILES['store_img']['error'] = $_FILES['store_img_img']['error'][$index];
                $_FILES['store_img']['size'] = $_FILES['store_img_img']['size'][$index];

                if (!$this->upload->do_upload('store_img')) {
                    // echo $this->upload->display_errors();
                    $upload_success = false; // หากเกิดข้อผิดพลาดในการอัปโหลด ตั้งค่าเป็น false
                    break; // หยุดการทำงานลูป
                }

                $upload_data = $this->upload->data();
                $image_path = $upload_data['file_name'];

                // สร้างข้อมูลสำหรับบันทึกลงฐานข้อมูล tbl_store_img
                $image_data = array(
                    'store_img_ref_id' => $store_id,
                    'store_img_img' => $image_path
                );

                $this->db->insert('tbl_store_img', $image_data);
            }

            if ($upload_success) {
                // ลบรูปภาพเก่าที่เกี่ยวข้องกับกิจกรรม
                $this->db->where('store_img_ref_id', $store_id);
                $existing_images = $this->db->get('tbl_store_img')->result();

                foreach ($existing_images as $existing_image) {
                    $old_file_path = './docs/img/' . $existing_image->store_img_img;
                    if (file_exists($old_file_path)) {
                        unlink($old_file_path);
                    }
                }

                $this->db->where('store_img_ref_id', $store_id);
                $this->db->delete('tbl_store_img');

                // เพิ่มรูปภาพใหม่ลงไป
                foreach ($_FILES['store_img_img']['name'] as $index => $name) {
                    $_FILES['store_img']['name'] = $name;
                    $_FILES['store_img']['type'] = $_FILES['store_img_img']['type'][$index];
                    $_FILES['store_img']['tmp_name'] = $_FILES['store_img_img']['tmp_name'][$index];
                    $_FILES['store_img']['error'] = $_FILES['store_img_img']['error'][$index];
                    $_FILES['store_img']['size'] = $_FILES['store_img_img']['size'][$index];

                    if (!$this->upload->do_upload('storeimg')) {
                        // echo $this->upload->display_errors();
                        break; // หยุดการทำงานลูปหากรูปภาพมีปัญหา
                    }

                    $upload_data = $this->upload->data();
                    $image_path = $upload_data['file_name'];

                    // สร้างข้อมูลสำหรับบันทึกลงฐานข้อมูล tbl_store_img
                    $image_data = array(
                        'store_img_ref_id' => $store_id,
                        'store_img_img' => $image_path
                    );

                    $this->db->insert('tbl_store_img', $image_data);
                }
            }
        }
        $latitude = $data['store_lat'];
        $longitude = $data['store_long'];

        $map_link = "https://www.google.com/maps?q=$latitude,$longitude";

        // สร้างข้อมูลที่คุณต้องการส่งใน $message
        $message = "แก้ไขร้านค้าและบริการ: แอดมิน" . "\n";
        $message .= "ประเภท: " . $data['store_type'] . "\n";
        $message .= "ชือร้าน: " . $data['store_name'] . "\n";
        $message .= "เบอร์โทร: " . $data['store_phone'] . "\n";
        $message .= "สถานที่: " . $data['store_location'] . "\n";
        $message .= "พิกัด: ($map_link)\n";
        // เรียกใช้ฟังก์ชันส่งข้อมูล LINE Notify โดยใส่ $message
        $this->sendLineNotify($message);

        $this->space_model->update_server_current();
        $this->session->set_flashdata('save_success', TRUE);
    }


    public function del_store($store_id)
    {
        $old_document = $this->db->get_where('tbl_store', array('store_id' => $store_id))->row();

        $old_file_path = './docs/img/' . $old_document->store_img;
        if (file_exists($old_file_path)) {
            unlink($old_file_path);
        }

        $this->db->delete('tbl_store', array('store_id' => $store_id));
        $this->space_model->update_server_current();
    }

    public function del_store_img($store_id)
    {
        // ดึงข้อมูลรายการรูปภาพก่อน
        $images = $this->db->get_where('tbl_store_img', array('store_img_ref_id' => $store_id))->result();

        // ลบรูปภาพจากตาราง tbl_store_img
        $this->db->where('store_img_ref_id', $store_id);
        $this->db->delete('tbl_store_img');

        // ลบไฟล์รูปภาพที่เกี่ยวข้อง
        foreach ($images as $image) {
            $image_path = './docs/img/' . $image->store_img_img;
            if (file_exists($image_path)) {
                unlink($image_path);
            }
        }
    }

    public function updateStoreStatus()
    {
        // ตรวจสอบว่ามีการส่งข้อมูล POST มาหรือไม่
        if ($this->input->post()) {
            $storeId = $this->input->post('store_id'); // รับค่า store_id
            $newStatus = $this->input->post('new_status'); // รับค่าใหม่จาก switch checkbox

            // ทำการอัพเดตค่าในตาราง tbl_store ในฐานข้อมูลของคุณ
            $data = array(
                'store_status' => $newStatus
            );
            $this->db->where('store_id', $storeId); // ระบุ store_id ของแถวที่ต้องการอัพเดต
            $this->db->update('tbl_store', $data);

            // ส่งการตอบกลับ (response) กลับไปยังเว็บไซต์หรือแอพพลิเคชันของคุณ
            // โดยเช่นปกติคุณอาจส่ง JSON response กลับมาเพื่ออัพเดตหน้าเว็บ
            $response = array('status' => 'success', 'message' => 'อัพเดตสถานะเรียบร้อย');
            echo json_encode($response);
        } else {
            // ถ้าไม่มีข้อมูล POST ส่งมา ให้รีเดอร์เปรียบเสมอ
            show_404();
        }
    }

    public function del_com($store_com_id)
    {
        return $this->db->where('store_com_id', $store_com_id)->delete('tbl_store_com');
    }

    public function del_reply($store_com_id)
    {
        return $this->db->where('store_com_reply_ref_id', $store_com_id)->delete('tbl_store_com_reply');
    }

    public function del_com_reply($store_com_reply_id)
    {
        return $this->db->where('store_com_reply_id', $store_com_reply_id)->delete('tbl_store_com_reply');
    }

    // ส่วนของ user ***************************************************************************************************************************************************************************************************************************************************************
    public function read_user_store($user_store_id)
    {
        $this->db->where('user_store_id', $user_store_id);
        $query = $this->db->get('tbl_user_store');
        if ($query->num_rows() > 0) {
            $data = $query->row();
            return $data;
        }
        return FALSE;
    }

    public function read_img_user_store($user_store_id)
    {
        $this->db->where('user_store_img_ref_id', $user_store_id);
        $this->db->order_by('user_store_img_id', 'DESC');
        $query = $this->db->get('tbl_user_store_img');
        return $query->result();
    }

    public function read_user_com_store($user_store_id)
    {
        $this->db->where('user_store_com_ref_id', $user_store_id);
        $this->db->order_by('user_store_com_ref_id', 'DESC');
        $query = $this->db->get('tbl_user_store_com');
        return $query->result();
    }

    public function read_user_com_reply_store($user_store_com_id)
    {
        $this->db->where('user_store_com_reply_ref_id', $user_store_com_id);
        $query = $this->db->get('tbl_user_store_com_reply');
        return $query->result();
    }


    public function edit_user_store($user_store_id)
    {
        $old_document = $this->db->get_where('tbl_user_store', array('user_store_id' => $user_store_id))->row();

        $update_doc_file = !empty($_FILES['user_store_img']['name']) && $old_document->user_store_img != $_FILES['user_store_img']['name'];

        // ตรวจสอบว่ามีการอัพโหลดรูปภาพใหม่หรือไม่
        if ($update_doc_file) {
            $old_file_path = './docs/img/' . $old_document->user_store_img;
            if (file_exists($old_file_path)) {
                unlink($old_file_path);
            }

            // Check used space
            $used_space_mb = $this->space_model->get_used_space();
            $upload_limit_mb = $this->space_model->get_limit_storage();

            $total_space_required = 0;
            if (!empty($_FILES['user_store_img']['name'])) {
                $total_space_required += $_FILES['user_store_img']['size'];
            }

            if ($used_space_mb + ($total_space_required / (1024 * 1024 * 1024)) >= $upload_limit_mb) {
                $this->session->set_flashdata('save_error', TRUE);
                redirect('store/editing_user_store');
                return;
            }

            $config['upload_path'] = './docs/img';
            $config['allowed_types'] = 'gif|jpg|png';
            $this->load->library('upload', $config);

            if (!$this->upload->do_upload('user_store_img')) {
                echo $this->upload->display_errors();
                return;
            }

            $data = $this->upload->data();
            $filename = $data['file_name'];
        } else {
            // ใช้รูปภาพเดิม
            $filename = $old_document->user_store_img;
        }

        // Update user_store information
        $data = array(
            'user_store_type' => $this->input->post('user_store_type'),
            'user_store_name' => $this->input->post('user_store_name'),
            'user_store_date' => $this->input->post('user_store_date'),
            'user_store_timeopen' => $this->input->post('user_store_timeopen'),
            'user_store_timeclose' => $this->input->post('user_store_timeclose'),
            'user_store_phone' => $this->input->post('user_store_phone'),
            'user_store_detail' => $this->input->post('user_store_detail'),
            'user_store_location' => $this->input->post('user_store_location'),
            'user_store_lat' => $this->input->post('user_store_lat'),
            'user_store_long' => $this->input->post('user_store_long'),
            'user_store_by' => $this->session->userdata('m_fname'), // เพิ่มชื่อคนที่แก้ไขข้อมูล
            'user_store_img' => $filename
        );

        $this->db->where('user_store_id', $user_store_id);
        $this->db->update('tbl_user_store', $data);

        // อัปโหลดและบันทึกไฟล์ใหม่ลงโฟลเดอร์
        if (!empty($_FILES['user_store_img_img']['name'])) {
            $upload_config['upload_path'] = './docs/img';
            $upload_config['allowed_types'] = 'gif|jpg|png';
            $this->load->library('upload', $upload_config);

            $upload_success = true; // ตั้งค่าเริ่มต้นเป็น true

            foreach ($_FILES['user_store_img_img']['name'] as $index => $name) {
                $_FILES['user_store_img']['name'] = $name;
                $_FILES['user_store_img']['type'] = $_FILES['user_store_img_img']['type'][$index];
                $_FILES['user_store_img']['tmp_name'] = $_FILES['user_store_img_img']['tmp_name'][$index];
                $_FILES['user_store_img']['error'] = $_FILES['user_store_img_img']['error'][$index];
                $_FILES['user_store_img']['size'] = $_FILES['user_store_img_img']['size'][$index];

                if (!$this->upload->do_upload('user_store_img')) {
                    // echo $this->upload->display_errors();
                    $upload_success = false; // หากเกิดข้อผิดพลาดในการอัปโหลด ตั้งค่าเป็น false
                    break; // หยุดการทำงานลูป
                }

                $upload_data = $this->upload->data();
                $image_path = $upload_data['file_name'];

                // สร้างข้อมูลสำหรับบันทึกลงฐานข้อมูล tbl_user_store_img
                $image_data = array(
                    'user_store_img_ref_id' => $user_store_id,
                    'user_store_img_img' => $image_path
                );

                $this->db->insert('tbl_user_store_img', $image_data);
            }

            if ($upload_success) {
                // ลบรูปภาพเก่าที่เกี่ยวข้องกับกิจกรรม
                $this->db->where('user_store_img_ref_id', $user_store_id);
                $existing_images = $this->db->get('tbl_user_store_img')->result();

                foreach ($existing_images as $existing_image) {
                    $old_file_path = './docs/img/' . $existing_image->user_store_img_img;
                    if (file_exists($old_file_path)) {
                        unlink($old_file_path);
                    }
                }

                $this->db->where('user_store_img_ref_id', $user_store_id);
                $this->db->delete('tbl_user_store_img');

                // เพิ่มรูปภาพใหม่ลงไป
                foreach ($_FILES['user_store_img_img']['name'] as $index => $name) {
                    $_FILES['user_store_img']['name'] = $name;
                    $_FILES['user_store_img']['type'] = $_FILES['user_store_img_img']['type'][$index];
                    $_FILES['user_store_img']['tmp_name'] = $_FILES['user_store_img_img']['tmp_name'][$index];
                    $_FILES['user_store_img']['error'] = $_FILES['user_store_img_img']['error'][$index];
                    $_FILES['user_store_img']['size'] = $_FILES['user_store_img_img']['size'][$index];

                    if (!$this->upload->do_upload('user_store_img')) {
                        // echo $this->upload->display_errors();
                        break; // หยุดการทำงานลูปหากรูปภาพมีปัญหา
                    }

                    $upload_data = $this->upload->data();
                    $image_path = $upload_data['file_name'];

                    // สร้างข้อมูลสำหรับบันทึกลงฐานข้อมูล tbl_user_store_img
                    $image_data = array(
                        'user_store_img_ref_id' => $user_store_id,
                        'user_store_img_img' => $image_path
                    );

                    $this->db->insert('tbl_user_store_img', $image_data);
                }
            }
        }
        $latitude = $data['store_lat'];
        $longitude = $data['store_long'];

        $map_link = "https://www.google.com/maps?q=$latitude,$longitude";

        // สร้างข้อมูลที่คุณต้องการส่งใน $message
        $message = "แก้ไขร้านค้าและบริการ: แอดมิน" . "\n";
        $message .= "ประเภท: " . $data['store_type'] . "\n";
        $message .= "ชือร้าน: " . $data['store_name'] . "\n";
        $message .= "เบอร์โทร: " . $data['store_phone'] . "\n";
        $message .= "สถานที่: " . $data['store_location'] . "\n";
        $message .= "พิกัด: ($map_link)\n";
        // เรียกใช้ฟังก์ชันส่งข้อมูล LINE Notify โดยใส่ $message
        $this->sendLineNotify($message);
        
        $this->space_model->update_server_current();
        $this->session->set_flashdata('save_success', TRUE);
    }

    public function del_user_store($user_store_id)
    {
        $old_document = $this->db->get_where('tbl_user_store', array('user_store_id' => $user_store_id))->row();

        $old_file_path = './docs/img/' . $old_document->user_store_img;
        if (file_exists($old_file_path)) {
            unlink($old_file_path);
        }

        $this->db->delete('tbl_user_store', array('user_store_id' => $user_store_id));
        $this->space_model->update_server_current();
    }

    public function del_user_store_img($user_store_id)
    {
        // ดึงข้อมูลรายการรูปภาพก่อน
        $images = $this->db->get_where('tbl_user_store_img', array('user_store_img_ref_id' => $user_store_id))->result();

        // ลบรูปภาพจากตาราง tbl_store_img
        $this->db->where('user_store_img_ref_id', $user_store_id);
        $this->db->delete('tbl_user_store_img');

        // ลบไฟล์รูปภาพที่เกี่ยวข้อง
        foreach ($images as $image) {
            $image_path = './docs/img/' . $image->user_store_img_img;
            if (file_exists($image_path)) {
                unlink($image_path);
            }
        }
    }

    public function updateUserStoreStatus()
    {
        // ตรวจสอบว่ามีการส่งข้อมูล POST มาหรือไม่
        if ($this->input->post()) {
            $userstoreId = $this->input->post('user_store_id'); // รับค่า store_id
            $newStatus = $this->input->post('new_status'); // รับค่าใหม่จาก switch checkbox

            // ทำการอัพเดตค่าในตาราง tbl_store ในฐานข้อมูลของคุณ
            $data = array(
                'user_store_status' => $newStatus
            );
            $this->db->where('user_store_id', $userstoreId); // ระบุ store_id ของแถวที่ต้องการอัพเดต
            $this->db->update('tbl_user_store', $data);

            // ส่งการตอบกลับ (response) กลับไปยังเว็บไซต์หรือแอพพลิเคชันของคุณ
            // โดยเช่นปกติคุณอาจส่ง JSON response กลับมาเพื่ออัพเดตหน้าเว็บ
            $response = array('status' => 'success', 'message' => 'อัพเดตสถานะเรียบร้อย');
            echo json_encode($response);
        } else {
            // ถ้าไม่มีข้อมูล POST ส่งมา ให้รีเดอร์เปรียบเสมอ
            show_404();
        }
    }

    public function del_com_user($user_store_com_id)
    {
        return $this->db->where('user_store_com_id', $user_store_com_id)->delete('tbl_user_store_com');
    }

    public function del_reply_user($user_store_com_id)
    {
        return $this->db->where('user_store_com_reply_ref_id', $user_store_com_id)->delete('tbl_user_store_com_reply');
    }

    public function del_com_reply_user($user_store_com_reply_id)
    {
        return $this->db->where('user_store_com_reply_id', $user_store_com_reply_id)->delete('tbl_user_store_com_reply');
    }

    // ************************************************************************************************************

    public function sum_store_views()
    {
        // คำนวณผลรวมของ tbl_store
        $this->db->select('SUM(store_view) as total_views');
        $this->db->from('tbl_store');
        $query_store = $this->db->get();

        // คำนวณผลรวมของ tbl_user_store
        $this->db->select('SUM(user_store_view) as total_user_views');
        $this->db->from('tbl_user_store');
        $query_user_store = $this->db->get();

        // นำผลรวมของทั้งสองตารางมาบวกกัน
        $total_views = $query_store->row()->total_views + $query_user_store->row()->total_user_views;

        return $total_views;
    }

    public function sum_store_likes()
    {
        // คำนวณผลรวมของ tbl_store
        $this->db->select('SUM(store_like_like) as total_likes');
        $this->db->from('tbl_store_like');
        $query_store = $this->db->get();

        // คำนวณผลรวมของ tbl_user_store
        $this->db->select('SUM(user_store_like_like) as total_user_likes');
        $this->db->from('tbl_user_store_like');
        $query_user_store = $this->db->get();

        // นำผลรวมของทั้งสองตารางมาบวกกัน
        $total_likes = $query_store->row()->total_likes + $query_user_store->row()->total_user_likes;

        return $total_likes;
    }
}
