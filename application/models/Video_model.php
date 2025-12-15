<?php
class Video_model extends CI_Model
{
    public function __construct()
    {
        parent::__construct();
        $this->load->model('space_model');
        // log เก็บข้อมูล
        $this->load->model('log_model');
    }

    // public function update_all_video_status($status)
    // {
    //     $this->db->update('tbl_video', array('video_status' => $status));
    // }

    public function add()
    {
        // Configure video upload
        $video_config['upload_path'] = './docs/video';
        $video_config['allowed_types'] = 'mp4|webm|ogg|avi|m4v|mov|mpg|mpeg|wmv';
        $this->load->library('upload', $video_config, 'video_upload');

        // เช็คพื้นที่ว่าง
        $used_space = $this->space_model->get_used_space();
        $upload_limit = $this->space_model->get_limit_storage();

        if (!empty($_FILES['video_video']['name'])) {
            $file_size = $_FILES['video_video']['size'];
            $total_space_required = $file_size / (1024 * 1024 * 1024); // แปลงเป็น GB

            if ($used_space + $total_space_required >= $upload_limit) {
                $this->session->set_flashdata('save_error', TRUE);
                redirect('video_backend/adding');
                return;
            }
        }

        $file_name = null;

        // ตรวจสอบว่ามีไฟล์อัปโหลดหรือไม่
        if (!empty($_FILES['video_video']['name'])) {
            if ($this->video_upload->do_upload('video_video')) {
                $upload_data = $this->video_upload->data();
                $file_name = $upload_data['file_name'];
            } else {
                // ถ้ามี error ตอนอัปโหลด
                $this->session->set_flashdata('save_error', $this->video_upload->display_errors());
                redirect('video_backend/adding');
                return;
            }
        }

        // กำหนดค่าใน $video_data
        $video_data = array(
            'video_name'   => $this->input->post('video_name'),
            'video_link'   => $this->input->post('video_link'),
            'video_video'  => $file_name,  // เก็บไฟล์วิดีโอเดียว
            'video_date'   => $this->input->post('video_date'),
            'video_by'     => $this->session->userdata('m_fname'),
        );

        // เพิ่มข้อมูลลงในฐานข้อมูล
        $this->db->insert('tbl_video', $video_data);
        // บันทึก log การเพิ่มข้อมูล =================================================
        $video_id = $this->db->insert_id();
        // =======================================================================

        $this->space_model->update_server_current();
        // บันทึก log การเพิ่มข้อมูล =================================================
        $this->log_model->add_log(
            'เพิ่ม',
            'ข้อมูลวิดีทัศน์',
            $video_data['video_name'],
            $video_id,
            array(
                'info' => array(
                    'video_link' => $video_data['video_link'],
                    'video_date' => $video_data['video_date'],
                )
            )
        );
        // =======================================================================
        $this->session->set_flashdata('save_success', TRUE);
    }

    // public function add()
    // {
    //     $video_name = $this->input->post('video_name');

    //     // ตรวจสอบว่ามีข้อมูลที่มีชื่อ video_name นี้อยู่แล้วหรือไม่
    //     $existing_record = $this->db->get_where('tbl_video', array('video_name' => $video_name))->row();

    //     if ($existing_record) {
    //         // ถ้ามีข้อมูลแล้วให้แสดงข้อความแจ้งเตือนหรือทำตามที่ต้องการ
    //         $this->session->set_flashdata('save_again', TRUE);
    //     } else {
    //         // ถ้าไม่มีข้อมูลในฐานข้อมูลให้ทำการเพิ่มข้อมูล
    //         $data = array(
    //             'video_name' => $video_name,
    //             'video_link' => $this->input->post('video_link'),
    //             'video_date' => $this->input->post('video_date'),
    //             'video_by' => $this->session->userdata('m_fname'), // เพิ่มชื่อคนที่เพิ่มข้อมูล
    //         );

    //         $query = $this->db->insert('tbl_video', $data);
    // 		// บันทึก log การเพิ่มข้อมูล =================================================
    //   		$video_id = $this->db->insert_id();
    //    		 // =======================================================================

    //         $this->space_model->update_server_current();

    // 		// บันทึก log การเพิ่มข้อมูล =================================================
    //         $this->log_model->add_log(
    //             'เพิ่ม',
    //             'ข้อมูลวิดีทัศน์',
    //             $data['video_name'],
    //             $video_id,
    //             array(
    //                 'info' => array(
    //                     'video_link' => $data['video_link'],
    // 					'video_date' => $data['video_date'],
    //                 )
    //             )
    //         );
    //         // =======================================================================


    //         if ($query) {
    //             $this->session->set_flashdata('save_success', TRUE);
    //         } else {
    //             echo "<script>";
    //             echo "alert('เกิดข้อผิดพลาดในการเพิ่มข้อมูลใหม่ !');";
    //             echo "</script>";
    //         }
    //     }
    // }



    public function list_all()
    {
        $this->db->order_by('video_id', 'DESC');
        $query = $this->db->get('tbl_video');
        return $query->result();
    }

    //show form edit
    public function read($video_id)
    {
        $this->db->where('video_id', $video_id);
        $query = $this->db->get('tbl_video');
        if ($query->num_rows() > 0) {
            $data = $query->row();
            return $data;
        }
        return FALSE;
    }

    public function edit($video_id)
    {
        // ดึงข้อมูลเก่า
        $old_data = $this->read($video_id);

        // Configure video upload
        $video_config['upload_path']   = './docs/video';
        $video_config['allowed_types'] = 'mp4|webm|ogg|avi|m4v|mov|mpg|mpeg|wmv';
        $this->load->library('upload', $video_config, 'video_upload');

        $file_name = $old_data->video_video; // ค่าเดิม (ถ้าไม่มีการอัปโหลดใหม่)

        // ตรวจสอบว่ามีการอัปโหลดไฟล์ใหม่หรือไม่
        if (!empty($_FILES['video_video']['name'])) {

            // เช็คพื้นที่ว่าง
            $used_space   = $this->space_model->get_used_space();
            $upload_limit = $this->space_model->get_limit_storage();
            $file_size    = $_FILES['video_video']['size'];
            $total_space_required = $file_size / (1024 * 1024 * 1024); // GB

            if ($used_space + $total_space_required >= $upload_limit) {
                $this->session->set_flashdata('save_error', TRUE);
                redirect('video_backend/editing/' . $video_id);
                return;
            }

            // ถ้ามีการอัปโหลดใหม่
            if ($this->video_upload->do_upload('video_video')) {
                $upload_data = $this->video_upload->data();
                $file_name   = $upload_data['file_name'];

                // ลบไฟล์เก่า (ถ้ามี)
                if (!empty($old_data->video_video) && file_exists('./docs/video/' . $old_data->video_video)) {
                    unlink('./docs/video/' . $old_data->video_video);
                }
            } else {
                // ถ้า upload error
                $this->session->set_flashdata('save_error', $this->video_upload->display_errors());
                redirect('video_backend/editing/' . $video_id);
                return;
            }
        }

        // เตรียมข้อมูลใหม่
        $data = array(
            'video_name'  => $this->input->post('video_name'),
            'video_link'  => $this->input->post('video_link'),
            'video_date'  => $this->input->post('video_date'),
            'video_by'    => $this->session->userdata('m_fname'),
            'video_video' => $file_name, // ใช้ไฟล์ใหม่หรือไฟล์เก่า
        );

        // อัปเดตข้อมูล
        $this->db->where('video_id', $video_id);
        $query = $this->db->update('tbl_video', $data);

        $this->space_model->update_server_current();

        // Log การแก้ไข
        $this->log_model->add_log(
            'แก้ไข',
            'ข้อมูลวิดีทัศน์',
            $data['video_name'],
            $video_id,
            array(
                'info' => array(
                    'video_link' => $data['video_link'],
                    'video_date' => $data['video_date'],
                )
            )
        );

        if ($query) {
            $this->session->set_flashdata('save_success', TRUE);
        } else {
            echo "<script>alert('เกิดข้อผิดพลาดในการอัปเดตข้อมูล !');</script>";
        }
    }


    public function del_video($video_id)
    {
        // ดึงข้อมูลเก่ามาเพื่อจะรู้ชื่อไฟล์
        $old_document = $this->db->get_where('tbl_video', array('video_id' => $video_id))->row();

        if ($old_document) {
            // ถ้ามีไฟล์ video ใน column และไฟล์ยังอยู่ใน docs ให้ลบออก
            if (!empty($old_document->video_video)) {
                $file_path = FCPATH . 'docs/video/' . $old_document->video_video;
                if (file_exists($file_path)) {
                    unlink($file_path);
                }
            }

            // ลบ record ใน DB
            $this->db->delete('tbl_video', array('video_id' => $video_id));

            // บันทึก log การลบ
            $this->log_model->add_log(
                'ลบ',
                'ข้อมูลวิดีทัศน์',
                $old_document->video_name,
                $video_id,
                array('deleted_date' => date('Y-m-d H:i:s'))
            );
        }
    }


    public function updateVideoStatus()
    {
        // ตรวจสอบว่ามีการส่งข้อมูล POST มาหรือไม่
        if ($this->input->post()) {
            $videoId = $this->input->post('video_id'); // รับค่า video_id
            $newStatus = $this->input->post('new_status'); // รับค่าใหม่จาก switch checkbox

            // ทำการอัพเดตค่าในตาราง tbl_video ในฐานข้อมูลของคุณ
            $data = array(
                'video_status' => $newStatus
            );
            $this->db->where('video_id', $videoId); // ระบุ video_id ของแถวที่ต้องการอัพเดต
            $this->db->update('tbl_video', $data);

            // ส่งการตอบกลับ (response) กลับไปยังเว็บไซต์หรือแอพพลิเคชันของคุณ
            // โดยเช่นปกติคุณอาจส่ง JSON response กลับมาเพื่ออัพเดตหน้าเว็บ
            $response = array('status' => 'success', 'message' => 'อัพเดตสถานะเรียบร้อย');
            echo json_encode($response);
        } else {
            // ถ้าไม่มีข้อมูล POST ส่งมา ให้รีเดอร์เปรียบเสมอ
            show_404();
        }
    }

    public function video_frontend()
    {
        $this->db->select('*');
        $this->db->from('tbl_video');
        $this->db->order_by('video_id', 'asc');
        $query = $this->db->get();
        return $query->result();
    }

    public function increment_view($video_id)
    {
        $this->db->where('video_id', $video_id);
        $this->db->set('video_view', 'video_view + 1', false); // บวกค่า video_view ทีละ 1
        $this->db->update('tbl_video');
    }

public function get_latest_video()
{
    $this->db->where('tbl_video.video_status', 'show');
    $this->db->order_by('video_date', 'DESC'); // ✅ เรียงตามวันที่ใหม่ล่าสุด
    $this->db->limit(4);
    $query = $this->db->get('tbl_video');
    return $query->result();
}

}
