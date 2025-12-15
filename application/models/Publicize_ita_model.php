<?php
class Publicize_ita_model extends CI_Model
{
    public function __construct()
    {
        parent::__construct();
        $this->load->model('space_model');
        // log เก็บข้อมูล
        $this->load->model('log_model');
    }

    public function add_publicize_ita()
    {
        // Check used space
        $used_space_mb = $this->space_model->get_used_space();
        $upload_limit_mb = $this->space_model->get_limit_storage();

        // Calculate the total space required for all files
        $total_space_required = 0;
        if (!empty($_FILES['publicize_ita_img']['name'])) {
            $total_space_required += $_FILES['publicize_ita_img']['size'];
        }

        // Check if there's enough space
        if ($used_space_mb + ($total_space_required / (1024 * 1024 * 1024)) >= $upload_limit_mb) {
            $this->session->set_flashdata('save_error', TRUE);
            redirect('publicize_ita/adding_publicize_ita');
            return;
        }

        // Upload configuration
        $config['upload_path'] = './docs/img';
        $config['allowed_types'] = 'gif|jpg|png|jpeg|jfif';
        $this->load->library('upload', $config);

        // Upload main file
        if (!$this->upload->do_upload('publicize_ita_img')) {
            // If the file size exceeds the max_size, set flash data and redirect
            $this->session->set_flashdata('save_maxsize', TRUE);
            redirect('publicize_ita/adding_publicize_ita');
            return;
        }

        $data = $this->upload->data();
        $filename = $data['file_name'];

        // เตรียมข้อมูลสำหรับบันทึก
        $data = array(
            'publicize_ita_name' => $this->input->post('publicize_ita_name'),
            'publicize_ita_link' => $this->input->post('publicize_ita_link'),
            'publicize_ita_by' => $this->session->userdata('m_fname'),
            'publicize_ita_img' => $filename,
            'publicize_ita_display_type' => $this->input->post('publicize_ita_display_type')
        );

        // เพิ่มวันที่หากเลือกแสดงแบบช่วงเวลา
        if ($this->input->post('publicize_ita_display_type') == 'period') {
            $data['publicize_ita_start_day'] = $this->input->post('publicize_ita_start_day');
            $data['publicize_ita_start_month'] = $this->input->post('publicize_ita_start_month');
            $data['publicize_ita_end_day'] = $this->input->post('publicize_ita_end_day');
            $data['publicize_ita_end_month'] = $this->input->post('publicize_ita_end_month');
        }

        $query = $this->db->insert('tbl_publicize_ita', $data);

        $this->space_model->update_server_current();

        if ($query) {
            $this->session->set_flashdata('save_success', TRUE);
        } else {
            echo "<script>";
            echo "alert('Error !');";
            echo "</script>";
        }
    }

    public function list_all()
    {
        $this->db->order_by('publicize_ita_id', 'DESC');
        $query = $this->db->get('tbl_publicize_ita');
        return $query->result();
    }

    //show form edit
    public function read($publicize_ita_id)
    {
        $this->db->where('publicize_ita_id', $publicize_ita_id);
        $query = $this->db->get('tbl_publicize_ita');
        if ($query->num_rows() > 0) {
            $data = $query->row();
            return $data;
        }
        return FALSE;
    }

    public function edit_publicize_ita($publicize_ita_id)
    {
        // ดึงข้อมูลเก่าก่อนแก้ไข
        $old_data = $this->read($publicize_ita_id);
        
        $old_document = $this->db->get_where('tbl_publicize_ita', array('publicize_ita_id' => $publicize_ita_id))->row();

        $update_doc_file = !empty($_FILES['publicize_ita_img']['name']) && $old_document->publicize_ita_img != $_FILES['publicize_ita_img']['name'];

        // ตรวจสอบว่ามีการอัพโหลดรูปภาพใหม่หรือไม่
        if ($update_doc_file) {
            $old_file_path = './docs/img/' . $old_document->publicize_ita_img;
            if (file_exists($old_file_path)) {
                unlink($old_file_path);
            }

            // Check used space
            $used_space_mb = $this->space_model->get_used_space();
            $upload_limit_mb = $this->space_model->get_limit_storage();

            $total_space_required = 0;
            if (!empty($_FILES['publicize_ita_img']['name'])) {
                $total_space_required += $_FILES['publicize_ita_img']['size'];
            }

            if ($used_space_mb + ($total_space_required / (1024 * 1024 * 1024)) >= $upload_limit_mb) {
                $this->session->set_flashdata('save_error', TRUE);
                redirect('publicize_ita/editing_publicize_ita');
                return;
            }

            $config['upload_path'] = './docs/img';
            $config['allowed_types'] = 'gif|jpg|png|jpeg|jfif';
            $this->load->library('upload', $config);

            if (!$this->upload->do_upload('publicize_ita_img')) {
                echo $this->upload->display_errors();
                return;
            }

            $data = $this->upload->data();
            $filename = $data['file_name'];
        } else {
            // ใช้รูปภาพเดิม
            $filename = $old_document->publicize_ita_img;
        }

        // เตรียมข้อมูลสำหรับอัพเดต
        $data = array(
            'publicize_ita_name' => $this->input->post('publicize_ita_name'),
            'publicize_ita_link' => $this->input->post('publicize_ita_link'),
            'publicize_ita_by' => $this->session->userdata('m_fname'),
            'publicize_ita_img' => $filename,
            'publicize_ita_display_type' => $this->input->post('publicize_ita_display_type')
        );

        // จัดการวันที่ตามประเภทการแสดง
        if ($this->input->post('publicize_ita_display_type') == 'period') {
            $data['publicize_ita_start_day'] = $this->input->post('publicize_ita_start_day');
            $data['publicize_ita_start_month'] = $this->input->post('publicize_ita_start_month');
            $data['publicize_ita_end_day'] = $this->input->post('publicize_ita_end_day');
            $data['publicize_ita_end_month'] = $this->input->post('publicize_ita_end_month');
        } else {
            // ถ้าเปลี่ยนเป็นแสดงตลอด ให้ล้างวันที่
            $data['publicize_ita_start_day'] = NULL;
            $data['publicize_ita_start_month'] = NULL;
            $data['publicize_ita_end_day'] = NULL;
            $data['publicize_ita_end_month'] = NULL;
        }

        $this->db->where('publicize_ita_id', $publicize_ita_id);
        $query = $this->db->update('tbl_publicize_ita', $data);

        $this->space_model->update_server_current();

        // === เก็บ log แก้ไขข้อมูล ==============================================
        $changes = array();
        if ($old_data) {
            if ($old_data->publicize_ita_name != $data['publicize_ita_name']) {
                $changes['publicize_ita_name'] = array(
                    'old' => $old_data->publicize_ita_name,
                    'new' => $data['publicize_ita_name']
                );
            }
            if ($old_data->publicize_ita_link != $data['publicize_ita_link']) {
                $changes['publicize_ita_link'] = array(
                    'old' => $old_data->publicize_ita_link,
                    'new' => $data['publicize_ita_link']
                );
            }
            if ($old_data->publicize_ita_img != $data['publicize_ita_img']) {
                $changes['publicize_ita_img'] = array(
                    'old' => $old_data->publicize_ita_img,
                    'new' => $data['publicize_ita_img']
                );
            }
            // เพิ่มการเช็คการเปลี่ยนแปลงของฟิลด์ใหม่
            if (isset($old_data->publicize_ita_display_type) && $old_data->publicize_ita_display_type != $data['publicize_ita_display_type']) {
                $changes['publicize_ita_display_type'] = array(
                    'old' => $old_data->publicize_ita_display_type,
                    'new' => $data['publicize_ita_display_type']
                );
            }
        }

        // บันทึก log การแก้ไขข่าว
        $this->log_model->add_log(
            'แก้ไข',
            'ประชาสัมพันธ์ EIT/IIT',
            $data['publicize_ita_name'],
            $publicize_ita_id,
            array(
                'changes' => $changes,
            )
        );
        // =======================================================================

        if ($query) {
            $this->session->set_flashdata('save_success', TRUE);
        } else {
            echo "<script>";
            echo "alert('เกิดข้อผิดพลาดในการอัปเดตข้อมูล !');";
            echo "</script>";
        }
    }

    public function del_publicize_ita($publicize_ita_id)
    {
        $old_document = $this->db->get_where('tbl_publicize_ita', array('publicize_ita_id' => $publicize_ita_id))->row();

        $old_file_path = './docs/img/' . $old_document->publicize_ita_img;
        if (file_exists($old_file_path)) {
            unlink($old_file_path);
        }

        $this->db->delete('tbl_publicize_ita', array('publicize_ita_id' => $publicize_ita_id));
    }

    public function updatepublicize_itaStatus()
    {
        // ตรวจสอบว่ามีการส่งข้อมูล POST มาหรือไม่
        if ($this->input->post()) {
            $publicize_itaId = $this->input->post('publicize_ita_id'); // รับค่า publicize_ita_id
            $newStatus = $this->input->post('new_status'); // รับค่าใหม่จาก switch checkbox

            // ทำการอัพเดตค่าในตาราง tbl_publicize_ita ในฐานข้อมูลของคุณ
            $data = array(
                'publicize_ita_status' => $newStatus
            );
            $this->db->where('publicize_ita_id', $publicize_itaId); // ระบุ publicize_ita_id ของแถวที่ต้องการอัพเดต
            $this->db->update('tbl_publicize_ita', $data);

            // ส่งการตอบกลับ (response) กลับไปยังเซิร์ฟเวอร์ด้วย AJAX
            // โดยเช่นปกติคุณอาจส่ง JSON response กลับมาเพื่ออัพเดตหน้าเว็บ
            $response = array('status' => 'success', 'message' => 'อัพเดตสถานะเรียบร้อย');
            echo json_encode($response);
        } else {
            // ถ้าไม่มีข้อมูล POST ส่งมา ให้รีเดอร์เปรียบเสมอ
            show_404();
        }
    }

    public function publicize_ita_frontend()
    {
        $this->db->select('*');
        $this->db->from('tbl_publicize_ita');
        $this->db->where('tbl_publicize_ita.publicize_ita_status', 'show');
        
        // เพิ่มเงื่อนไขการแสดงตามช่วงเวลา (ไม่รวมปี)
        $this->db->group_start(); // เริ่ม group condition
        $this->db->where('publicize_ita_display_type', 'always'); // แสดงตลอด
        $this->db->or_group_start(); // หรือ
        $this->db->where('publicize_ita_display_type', 'period');
        
        // ตรวจสอบช่วงเวลาแสดงโดยไม่สนใจปี
        $current_day = (int)date('j'); // วันที่ปัจจุบัน (1-31)
        $current_month = (int)date('n'); // เดือนปัจจุบัน (1-12)
        
        // สร้าง subquery สำหรับตรวจสอบช่วงเวลา
        $this->db->where("(
            (publicize_ita_start_month < publicize_ita_end_month AND 
             (($current_month > publicize_ita_start_month) OR 
              ($current_month = publicize_ita_start_month AND $current_day >= publicize_ita_start_day)) AND 
             (($current_month < publicize_ita_end_month) OR 
              ($current_month = publicize_ita_end_month AND $current_day <= publicize_ita_end_day)))
            OR
            (publicize_ita_start_month > publicize_ita_end_month AND 
             (($current_month > publicize_ita_start_month) OR 
              ($current_month = publicize_ita_start_month AND $current_day >= publicize_ita_start_day) OR
              ($current_month < publicize_ita_end_month) OR 
              ($current_month = publicize_ita_end_month AND $current_day <= publicize_ita_end_day)))
            OR
            (publicize_ita_start_month = publicize_ita_end_month AND 
             $current_month = publicize_ita_start_month AND 
             $current_day >= publicize_ita_start_day AND 
             $current_day <= publicize_ita_end_day)
        )", NULL, FALSE);
        
        $this->db->group_end(); // จบ or group
        $this->db->group_end(); // จบ main group
        
        $this->db->order_by('tbl_publicize_ita.publicize_ita_id', 'DESC');
        $query = $this->db->get();
        return $query->result();
    }

    // ฟังก์ชันใหม่: ตรวจสอบสถานะการแสดงตามวันที่ (ไม่รวมปี)
    public function check_display_status($item)
    {
        if ($item->publicize_ita_display_type == 'always') {
            return true;
        }
        
        if ($item->publicize_ita_display_type == 'period') {
            $current_day = (int)date('j'); // วันที่ปัจจุบัน (1-31)
            $current_month = (int)date('n'); // เดือนปัจจุบัน (1-12)
            
            $start_day = (int)$item->publicize_ita_start_day;
            $start_month = (int)$item->publicize_ita_start_month;
            $end_day = (int)$item->publicize_ita_end_day;
            $end_month = (int)$item->publicize_ita_end_month;
            
            // กรณีไม่ข้ามปี (เช่น มกราคม ถึง มีนาคม)
            if ($start_month < $end_month) {
                return ($current_month > $start_month || 
                       ($current_month == $start_month && $current_day >= $start_day)) &&
                       ($current_month < $end_month || 
                       ($current_month == $end_month && $current_day <= $end_day));
            }
            // กรณีข้ามปี (เช่น ธันวาคม ถึง กุมภาพันธ์)
            else if ($start_month > $end_month) {
                return ($current_month > $start_month || 
                       ($current_month == $start_month && $current_day >= $start_day)) ||
                       ($current_month < $end_month || 
                       ($current_month == $end_month && $current_day <= $end_day));
            }
            // กรณีเดือนเดียวกัน
            else {
                return ($current_month == $start_month && 
                       $current_day >= $start_day && 
                       $current_day <= $end_day);
            }
        }
        
        return false;
    }

    // ฟังก์ชันสำหรับแสดงข้อความสถานะ
    public function get_display_status_text($item)
    {
        if ($item->publicize_ita_display_type == 'always') {
            return 'แสดงตลอด';
        }
        
        if ($item->publicize_ita_display_type == 'period') {
            $current_day = (int)date('j');
            $current_month = (int)date('n');
            
            $start_day = (int)$item->publicize_ita_start_day;
            $start_month = (int)$item->publicize_ita_start_month;
            $end_day = (int)$item->publicize_ita_end_day;
            $end_month = (int)$item->publicize_ita_end_month;
            
            // ตรวจสอบว่าอยู่ในช่วงแสดงหรือไม่
            $is_in_range = $this->check_display_status($item);
            
            if ($is_in_range) {
                return 'กำลังแสดง';
            } else {
                // ตรวจสอบว่าผ่านช่วงแสดงแล้วหรือยังไม่ถึง
                if ($start_month < $end_month) {
                    // ไม่ข้ามปี
                    if ($current_month < $start_month || 
                        ($current_month == $start_month && $current_day < $start_day)) {
                        return 'ยังไม่ถึงเวลาแสดง';
                    } else {
                        return 'หมดเวลาแสดงแล้ว';
                    }
                } else if ($start_month > $end_month) {
                    // ข้ามปี
                    if ($current_month > $end_month && $current_month < $start_month) {
                        return 'ยังไม่ถึงเวลาแสดง';
                    } else {
                        return 'หมดเวลาแสดงแล้ว';
                    }
                } else {
                    // เดือนเดียวกัน
                    if ($current_day < $start_day) {
                        return 'ยังไม่ถึงเวลาแสดง';
                    } else {
                        return 'หมดเวลาแสดงแล้ว';
                    }
                }
            }
        }
        
        return 'ไม่ทราบสถานะ';
    }

    // ฟังก์ชันสำหรับแสดงช่วงเวลาเป็นข้อความ
    public function get_period_text($item)
    {
        if ($item->publicize_ita_display_type != 'period') {
            return '';
        }
        
        $months = array(
            1 => 'ม.ค.', 2 => 'ก.พ.', 3 => 'มี.ค.', 4 => 'เม.ย.',
            5 => 'พ.ค.', 6 => 'มิ.ย.', 7 => 'ก.ค.', 8 => 'ส.ค.',
            9 => 'ก.ย.', 10 => 'ต.ค.', 11 => 'พ.ย.', 12 => 'ธ.ค.'
        );
        
        $start_text = $item->publicize_ita_start_day . ' ' . $months[$item->publicize_ita_start_month];
        $end_text = $item->publicize_ita_end_day . ' ' . $months[$item->publicize_ita_end_month];
        
        return $start_text . ' ถึง ' . $end_text;
    }
}