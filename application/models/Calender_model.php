<?php
class Calender_model extends CI_Model
{
    public function __construct()
    {
        parent::__construct();
        $this->load->model('space_model');
        // log เก็บข้อมูล
        $this->load->model('log_model');
    }

    public function add_calender()
    {
        $data = array(
            'calender_detail' => $this->input->post('calender_detail'),
            'calender_date' => $this->input->post('calender_date'),
            'calender_date_end' => $this->input->post('calender_date_end'),
            'calender_by' => $this->session->userdata('m_fname'),
        );
        $this->db->insert('tbl_calender', $data);
		// บันทึก log การเพิ่มข้อมูล =================================================
        $calender_id = $this->db->insert_id();
        // =======================================================================
        $this->space_model->update_server_current();
		
		// บันทึก log การเพิ่มข้อมูล =================================================
            $this->log_model->add_log(
                'เพิ่ม',
                'ปฏิทินกิจกรรม',
                $data['calender_detail'],
                $calender_id,
                array(
                    'info' => array(
                        'calender_date' => $data['calender_date'],
                        'calender_date_end' => $data['calender_date_end'],
						'calender_by' => $data['calender_by'],
                    )
                )
            );
            // =======================================================================

		
        $this->session->set_flashdata('save_success', TRUE);
    }

    public function list_admin()
    {
        $this->db->from('tbl_calender as a');
        $this->db->group_by('a.calender_id');
        $this->db->order_by('a.calender_id', 'DESC');
        $query = $this->db->get();
        return $query->result();
    }


    //show form edit
    public function read_calender($calender_id)
    {
        $this->db->where('calender_id', $calender_id);
        $query = $this->db->get('tbl_calender');
        if ($query->num_rows() > 0) {
            $data = $query->row();
            return $data;
        }
        return FALSE;
    }

    public function read_img_calender($calender_id)
    {
    }

    public function read_com_calender($calender_id)
    {
        $this->db->where('calender_com_ref_id', $calender_id);
        $this->db->order_by('calender_com_ref_id', 'DESC');
        $query = $this->db->get('tbl_calender_com');
        return $query->result();
    }

    public function read_com_reply_calender($calender_com_id)
    {
        $this->db->where('calender_com_reply_ref_id', $calender_com_id);
        $query = $this->db->get('tbl_calender_com_reply');
        return $query->result();
    }

    public function get_used_space()
    {
        $upload_folder = './docs'; // ตำแหน่งของโฟลเดอร์ที่คุณต้องการ

        $used_space = $this->calculateFolderSize($upload_folder);

        $used_space_mb = $used_space / (1024 * 1024 * 1024);
        return $used_space_mb;
    }
    private function calculateFolderSize($folder)
    {
        $used_space = 0;
        $files = scandir($folder);
        foreach ($files as $file) {
            if ($file !== '.' && $file !== '..') {
                $path = $folder . '/' . $file;
                if (is_file($path)) {
                    $used_space += filesize($path);
                } elseif (is_dir($path)) {
                    $used_space += $this->calculateFolderSize($path);
                }
            }
        }
        return $used_space;
    }

    public function edit_calender($calender_id)
    {
		        // ดึงข้อมูลเก่าก่อนแก้ไข
        $old_data = $this->read_calender($calender_id);
		
        // รับข้อมูลจากฟอร์ม
        $calender_detail = $this->input->post('calender_detail');
        $calender_date = $this->input->post('calender_date');
        $calender_date_end = $this->input->post('calender_date_end');
        $calender_by = $this->session->userdata('m_fname');

        // ตรวจสอบข้อมูล
        if (empty($calender_detail) || empty($calender_date) || empty($calender_date_end) || empty($calender_by)) {
            $this->session->set_flashdata('error', 'All fields are required.');
            return false;
        }

        // อัปเดตข้อมูล
        $data = array(
            'calender_detail' => $calender_detail,
            'calender_date' => $calender_date,
            'calender_date_end' => $calender_date_end,
            'calender_by' => $calender_by
        );

        $this->db->where('calender_id', $calender_id);
        $update = $this->db->update('tbl_calender', $data);

        if ($update) {
            // อัปเดต server current
            $this->space_model->update_server_current();
			
			     // === เก็บ log แก้ไขข้อมูล ==============================================
        $changes = array();
        if ($old_data) {
            if ($old_data->calender_detail != $data['calender_detail']) {
                $changes['calender_detail'] = array(
                    'old' => $old_data->calender_detail,
                    'new' => $data['calender_detail']
                );
            }
			if ($old_data->calender_date != $data['calender_date']) {
                $changes['calender_date'] = array(
                    'old' => $old_data->calender_date,
                    'new' => $data['calender_date']
                );
            }
			if ($old_data->calender_date_end != $data['calender_date_end']) {
                $changes['calender_date_end'] = array(
                    'old' => $old_data->calender_date_end,
                    'new' => $data['calender_date_end']
                );
            }
			if ($old_data->calender_by != $data['calender_by']) {
                $changes['calender_by'] = array(
                    'old' => $old_data->calender_by,
                    'new' => $data['calender_by']
                );
            }
        }

        // บันทึก log การแก้ไขข่าว
        $this->log_model->add_log(
            'แก้ไข',
            'ปฏิทินกิจกรรม',
            $data['calender_detail'],
            $calender_id,
            array(
                'changes' => $changes,
            )
        );
        // =======================================================================
			
            $this->session->set_flashdata('save_success', TRUE);
            return true;
        } else {
            // บันทึกข้อความข้อผิดพลาด
            $error = $this->db->error();
            log_message('error', 'Failed to update calendar: ' . print_r($error, true));
            $this->session->set_flashdata('error', 'Failed to update calendar.');
            return false;
        }
    }



    public function del_calender($calender_id)
    {
        $old_document = $this->db->get_where('tbl_calender', array('calender_id' => $calender_id))->row();

        $this->db->delete('tbl_calender', array('calender_id' => $calender_id));
		
		// บันทึก log การลบ =================================================
        if ($old_document) {
            $this->log_model->add_log(
                'ลบ',
                'ปฏิทินกิจกรรม',
                $old_document->calender_detail,
                $calender_id,
                array('deleted_date' => date('Y-m-d H:i:s'))
            );
        }
        // =======================================================================
		
        $this->space_model->update_server_current();
    }



    public function updatecalenderStatus()
    {
        // ตรวจสอบว่ามีการส่งข้อมูล POST มาหรือไม่
        if ($this->input->post()) {
            $calenderId = $this->input->post('calender_id'); // รับค่า calender_id
            $newStatus = $this->input->post('new_status'); // รับค่าใหม่จาก switch checkbox

            // ทำการอัพเดตค่าในตาราง tbl_calender ในฐานข้อมูลของคุณ
            $data = array(
                'calender_status' => $newStatus
            );
            $this->db->where('calender_id', $calenderId); // ระบุ calender_id ของแถวที่ต้องการอัพเดต
            $this->db->update('tbl_calender', $data);

            // ส่งการตอบกลับ (response) กลับไปยังเว็บไซต์หรือแอพพลิเคชันของคุณ
            // โดยเช่นปกติคุณอาจส่ง JSON response กลับมาเพื่ออัพเดตหน้าเว็บ
            $response = array('status' => 'success', 'message' => 'อัพเดตสถานะเรียบร้อย');
            echo json_encode($response);
        } else {
            // ถ้าไม่มีข้อมูล POST ส่งมา ให้รีเดอร์เปรียบเสมอ
            show_404();
        }
    }

    public function del_com($calender_com_id)
    {
        return $this->db->where('calender_com_id', $calender_com_id)->delete('tbl_calender_com');
    }

    public function del_reply($calender_com_id)
    {
        return $this->db->where('calender_com_reply_ref_id', $calender_com_id)->delete('tbl_calender_com_reply');
    }

    public function del_com_reply($calender_com_reply_id)
    {
        return $this->db->where('calender_com_reply_id', $calender_com_reply_id)->delete('tbl_calender_com_reply');
    }

    // ส่วนของ user ***************************************************************************************************************************************************************************************************************************************************************
    public function read_user_calender($user_calender_id)
    {
        $this->db->where('user_calender_id', $user_calender_id);
        $query = $this->db->get('tbl_user_calender');
        if ($query->num_rows() > 0) {
            $data = $query->row();
            return $data;
        }
        return FALSE;
    }



    public function read_user_com_calender($user_calender_id)
    {
        $this->db->where('user_calender_com_ref_id', $user_calender_id);
        $this->db->order_by('user_calender_com_ref_id', 'DESC');
        $query = $this->db->get('tbl_user_calender_com');
        return $query->result();
    }

    public function read_user_com_reply_calender($user_calender_com_id)
    {
        $this->db->where('user_calender_com_reply_ref_id', $user_calender_com_id);
        $query = $this->db->get('tbl_user_calender_com_reply');
        return $query->result();
    }

    public function edit_User_calender($user_calender_id, $user_calender_name, $user_calender_detail, $user_calender_phone)
    {
        $old_document = $this->db->get_where('tbl_user_calender', array('user_calender_id' => $user_calender_id))->row();


        // Update user_calender information
        $data = array(
            // 'user_calender_name' => $user_calender_name,
            'user_calender_detail' => $user_calender_detail,
            'user_calender_phone' => $user_calender_phone,
            'user_calender_by' => $this->session->userdata('m_fname') // เพิ่มชื่อคนที่แก้ไขข้อมูล
            // 'user_calender_img' => $filename
        );

        $this->db->where('user_calender_id', $user_calender_id);
        $this->db->update('tbl_user_calender', $data);


        $this->space_model->update_server_current();
        $this->session->set_flashdata('save_success', TRUE);
    }

    public function del_user_calender($user_calender_id)
    {
        $old_document = $this->db->get_where('tbl_user_calender', array('user_calender_id' => $user_calender_id))->row();



        $this->db->delete('tbl_user_calender', array('user_calender_id' => $user_calender_id));
        $this->space_model->update_server_current();
    }

    public function updateUsercalenderStatus()
    {
        // ตรวจสอบว่ามีการส่งข้อมูล POST มาหรือไม่
        if ($this->input->post()) {
            $usercalenderId = $this->input->post('user_calender_id'); // รับค่า calender_id
            $newStatus = $this->input->post('new_status'); // รับค่าใหม่จาก switch checkbox

            // ทำการอัพเดตค่าในตาราง tbl_calender ในฐานข้อมูลของคุณ
            $data = array(
                'user_calender_status' => $newStatus
            );
            $this->db->where('user_calender_id', $usercalenderId); // ระบุ calender_id ของแถวที่ต้องการอัพเดต
            $this->db->update('tbl_user_calender', $data);

            // ส่งการตอบกลับ (response) กลับไปยังเว็บไซต์หรือแอพพลิเคชันของคุณ
            // โดยเช่นปกติคุณอาจส่ง JSON response กลับมาเพื่ออัพเดตหน้าเว็บ
            $response = array('status' => 'success', 'message' => 'อัพเดตสถานะเรียบร้อย');
            echo json_encode($response);
        } else {
            // ถ้าไม่มีข้อมูล POST ส่งมา ให้รีเดอร์เปรียบเสมอ
            show_404();
        }
    }

    public function del_com_user($user_calender_com_id)
    {
        return $this->db->where('user_calender_com_id', $user_calender_com_id)->delete('tbl_user_calender_com');
    }

    public function del_reply_user($user_calender_com_id)
    {
        return $this->db->where('user_calender_com_reply_ref_id', $user_calender_com_id)->delete('tbl_user_calender_com_reply');
    }

    public function del_com_reply_user($user_calender_com_reply_id)
    {
        return $this->db->where('user_calender_com_reply_id', $user_calender_com_reply_id)->delete('tbl_user_calender_com_reply');
    }

    // ************************************************************************************************************

    public function sum_calender_views()
    {
        // คำนวณผลรวมของ tbl_calender
        $this->db->select('SUM(calender_view) as total_views');
        $this->db->from('tbl_calender');
        $query_calender = $this->db->get();

        $total_views = $query_calender->row()->total_views;

        return $total_views;
    }

    public function sum_calender_likes()
    {
        // คำนวณผลรวมของ tbl_calender
        $this->db->select('SUM(calender_like_like) as total_likes');
        $this->db->from('tbl_calender_like');
        $query_calender = $this->db->get();


        $total_likes = $query_calender->row()->total_likes;

        return $total_likes;
    }

    public function sum_calender_id()
    {
        // หาวันที่แรกของเดือนปัจจุบัน
        $start_of_current_month = date('Y-m-01');

        // หาวันที่แรกของเดือนถัดไป
        $start_of_next_month = date('Y-m-01', strtotime('+1 month'));

        // คำนวณผลรวมของ tbl_calender ที่มีวันที่อยู่ในเดือนปัจจุบันหรือเดือนถัดไป
        $this->db->select('SUM(calender_id) as total_id');
        $this->db->from('tbl_calender');
        $this->db->where('calender_datesave >=', $start_of_current_month);
        $this->db->where('calender_datesave <', $start_of_next_month);
        $query_calender = $this->db->get();

        $total_id = $query_calender->row()->total_id;

        return $total_id;
    }

    public function calender_frontend()
    {
        $this->db->select('*');
        $this->db->from('tbl_calender');
        $this->db->where('tbl_calender.calender_status', 'show');
        $this->db->limit(10);
        $this->db->order_by('tbl_calender.calender_date', 'DESC');
        $query = $this->db->get();
        return $query->result();
    }
    public function calender_frontend_list()
    {
        $this->db->select('*');
        $this->db->from('tbl_calender');
        $this->db->where('tbl_calender.calender_status', 'show');
        $this->db->order_by('tbl_calender.calender_date', 'DESC');
        $query = $this->db->get();
        return $query->result();
    }


    public function increment_calender_view($calender_id)
    {
        $this->db->where('calender_id', $calender_id);
        $this->db->set('calender_view', 'calender_view + 1', false); // บวกค่า calender_view ทีละ 1
        $this->db->update('tbl_calender');
    }



    public function get_events()
    {
        $this->db->where('calender_status', 'show');
        $query = $this->db->get('tbl_calender');
        return $query->result_array();
    }
}
