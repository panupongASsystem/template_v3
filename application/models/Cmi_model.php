<?php
class Cmi_model extends CI_Model
{
    public function __construct()
    {
        parent::__construct();
        $this->load->model('space_model');
		// log เก็บข้อมูล
        $this->load->model('log_model');
    }

    public function add()
    {
        $ci_name = $this->input->post('ci_name');

        // ตรวจสอบว่ามีข้อมูลที่มีชื่อ ci_name นี้อยู่แล้วหรือไม่
        $existing_record = $this->db->get_where('tbl_ci', array('ci_name' => $ci_name))->row();

        if ($existing_record) {
            // ถ้ามีข้อมูลแล้วให้แสดงข้อความแจ้งเตือนหรือทำตามที่ต้องการ
            $this->session->set_flashdata('save_again', TRUE);
        } else {
            // ถ้าไม่มีข้อมูลในฐานข้อมูลให้ทำการเพิ่มข้อมูล
            $data = array(
                'ci_name' => $ci_name,
                'ci_total' => $this->input->post('ci_total'),
                'ci_man' => $this->input->post('ci_man'),
                'ci_woman' => $this->input->post('ci_woman'),
                'ci_home' => $this->input->post('ci_home'),
                'ci_by' => $this->session->userdata('m_fname'), // เพิ่มชื่อคนที่เพิ่มข้อมูล
            );

            $query = $this->db->insert('tbl_ci', $data);
			// บันทึก log การเพิ่มข้อมูล =================================================
      		  $ci_id = $this->db->insert_id();
       		// =======================================================================

            $this->space_model->update_server_current();
			
			 // บันทึก log การเพิ่มข้อมูล =================================================
            $this->log_model->add_log(
                'เพิ่ม',
                'ข้อมูลชุมชน',
                $data['ci_name'],
                $ci_id,
                array(
                    'ci_info' => array(
                        'ci_total' => $data['ci_total'],
						'ci_man' => $data['ci_man'],
						'ci_woman' => $data['ci_woman'],
						'ci_home' => $data['ci_home'],
						'ci_by' => $data['ci_by'],
                    )
                )
            );
            // =======================================================================


            if ($query) {
                $this->session->set_flashdata('save_success', TRUE);
            } else {
                echo "<script>";
                echo "alert('เกิดข้อผิดพลาดในการเพิ่มข้อมูลใหม่ !');";
                echo "</script>";
            }
        }
    }



    public function list_all()
    {
        $this->db->order_by('ci_id', 'DESC');
        $query = $this->db->get('tbl_ci');
        return $query->result();
    }

    //show form edit
    public function read($ci_id)
    {
        $this->db->where('ci_id', $ci_id);
        $query = $this->db->get('tbl_ci');
        if ($query->num_rows() > 0) {
            $data = $query->row();
            return $data;
        }
        return FALSE;
    }

    public function edit($ci_id)
    {
		// ดึงข้อมูลเก่าก่อนแก้ไข
        $old_data = $this->read($ci_id);

        $data = array(
            'ci_name' => $this->input->post('ci_name'),
            'ci_total' => $this->input->post('ci_total'),
            'ci_man' => $this->input->post('ci_man'),
            'ci_woman' => $this->input->post('ci_woman'),
            'ci_home' => $this->input->post('ci_home'),
            'ci_by' => $this->session->userdata('m_fname'), // เพิ่มชื่อคนที่เพิ่มข้อมูล
        );

        $this->db->where('ci_id', $ci_id);
        $query = $this->db->update('tbl_ci', $data);

        $this->space_model->update_server_current();

		// === เก็บ log แก้ไขข้อมูล ==============================================
        $this->log_model->add_log(
            'แก้ไข',
            'ข้อมูลชุมชน',
            $data['ci_name'],
            $ci_id,
            array(
                    'ci_info' => array(
                        'ci_total' => $data['ci_total'],
						'ci_man' => $data['ci_man'],
						'ci_woman' => $data['ci_woman'],
						'ci_home' => $data['ci_home'],
						'ci_by' => $data['ci_by'],
                    )
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

    public function del_ci($ci_id)
    {
		$old_document = $this->db->get_where('tbl_ci', array('ci_id' => $ci_id))->row();

        $this->db->delete('tbl_ci', array('ci_id' => $ci_id));
		
		// บันทึก log การลบ =================================================
        if ($old_document) {
            $this->log_model->add_log(
                'ลบ',
                'ข้อมูลชุมชน',
                $old_document->ci_name,
                $ci_id,
                array('deleted_date' => date('Y-m-d H:i:s'))
            );
        }
        // =======================================================================
    }

    public function ci_frontend()
    {
        $this->db->select('*');
        $this->db->from('tbl_ci');
        $query = $this->db->get();
        return $query->result();
    }
}
