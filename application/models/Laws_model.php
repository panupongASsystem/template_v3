<?php
class Laws_model extends CI_Model
{
    public function __construct()
    {
        parent::__construct();
        $this->load->model('space_model');
        $this->load->model('laws_model');
		// log เก็บข้อมูล
        $this->load->model('log_model');
    }

    public function add_topic()
    {
        // ดึงค่าจากฟอร์ม
        $laws_topic_topic = $this->input->post('laws_topic_topic');

        // ตรวจสอบว่ามีข้อมูลซ้ำหรือไม่
        $duplicate_check = $this->db->get_where('tbl_laws_topic', array('laws_topic_topic' => $laws_topic_topic));

        if ($duplicate_check->num_rows() > 0) {
            // ถ้ามีข้อมูลซ้ำ
            $this->session->set_flashdata('save_again', TRUE);
        } else {
            // ถ้าไม่มีข้อมูลซ้ำ, ทำการเพิ่มข้อมูล
            $data = array(
                'laws_topic_topic' => $laws_topic_topic,
                'laws_topic_by' => $this->session->userdata('m_fname'),
            );

            $query = $this->db->insert('tbl_laws_topic', $data);
			// บันทึก log การเพิ่มข้อมูล =================================================
       		 $laws_topic_id = $this->db->insert_id();
       		 // =======================================================================

            $this->space_model->update_server_current();
			
			// บันทึก log การเพิ่มข้อมูล =================================================
            $this->log_model->add_log(
                'เพิ่ม',
                'กฎหมาย',
                'เพิ่มหัวข้อ: ' . $data['laws_topic_topic'],
                $laws_topic_id
            );
            // =======================================================================


            if ($query) {
                $this->session->set_flashdata('save_success', TRUE);
            } else {
                echo "<script>";
                echo "alert('Error !');";
                echo "</script>";
            }
        }
    }

    public function list_all()
    {
        $this->db->order_by('laws_topic_id', 'DESC');
        $query = $this->db->get('tbl_laws_topic');
        return $query->result();
    }

    public function read($laws_topic_id)
    {
        $this->db->where('laws_topic_id', $laws_topic_id);
        $query = $this->db->get('tbl_laws_topic');
        if ($query->num_rows() > 0) {
            $data = $query->row();
            return $data;
        }
        return FALSE;
    }

    public function edit_topic($laws_topic_id)
    {
		// ดึงข้อมูลเก่าก่อนแก้ไข
        $old_data = $this->read($laws_topic_id);

        $data = array(
            'laws_topic_topic' => $this->input->post('laws_topic_topic'),
            'laws_topic_by' => $this->session->userdata('m_fname'),
        );

        $this->db->where('laws_topic_id', $laws_topic_id);
        $query = $this->db->update('tbl_laws_topic', $data);

        $this->space_model->update_server_current();

			// บันทึก log การเพิ่มข้อมูล =================================================
            $this->log_model->add_log(
                'แก้ไขหัวข้อ',
                'กฎหมาย',
                'แก้ไขหัวข้อ: ' . $data['laws_topic_topic'],
                $laws_topic_id
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

    public function list_all_laws($laws_topic_id)
    {
        $this->db->select('*');
        $this->db->from('tbl_laws');
        $this->db->join('tbl_laws_topic', 'tbl_laws.laws_ref_id = tbl_laws_topic.laws_topic_id');
        $this->db->where('tbl_laws.laws_ref_id', $laws_topic_id);
        $query = $this->db->get();
        $result = $query->result();
        return $result;
    }

    public function add_laws()
    {
        // Check used space
        $used_space_mb = $this->space_model->get_used_space();
        $upload_limit_mb = $this->space_model->get_limit_storage();

        // Calculate the total space required for all files
        $total_space_required = 0;
        if (!empty($_FILES['laws_pdf']['name'])) {
            $total_space_required += $_FILES['laws_pdf']['size'];
        }

        // Check if there's enough space
        if ($used_space_mb + ($total_space_required / (1024 * 1024 * 1024)) >= $upload_limit_mb) {
            $this->session->set_flashdata('save_error', TRUE);
            redirect('laws/adding_laws');
            return;
        }

        // Upload configuration
        $config['upload_path'] = './docs/file';
        $config['allowed_types'] = 'pdf';
        $this->load->library('upload', $config);

        // Upload main file
        if (!$this->upload->do_upload('laws_pdf')) {
            // If the file size exceeds the max_size, set flash data and redirect
            $this->session->set_flashdata('save_maxsize', TRUE);
            redirect('laws/adding_laws');
            return;
        }

        $data = $this->upload->data();
        $filename = $data['file_name'];

        $data = array(
            'laws_ref_id' => $this->input->post('laws_ref_id'),
            'laws_name' => $this->input->post('laws_name'),
            'laws_date' => $this->input->post('laws_date'),
            'laws_by' => $this->session->userdata('m_fname'),
            'laws_pdf' => $filename
        );

        $query = $this->db->insert('tbl_laws', $data);
		// บันทึก log การเพิ่มข้อมูล =================================================
        $laws_id  = $this->db->insert_id();
        // =======================================================================

        $this->space_model->update_server_current();
		
		    // บันทึก log การเพิ่มข้อมูล =================================================
            $this->log_model->add_log(
                'เพิ่ม',
                'กฏหมาย',
                $data['laws_name'],
                $laws_id,
                array(
                    'info' => array(
                        'laws_date' => $data['laws_date'],
                        'pdf_file' => $filename
                    )
                )
            );
            // =======================================================================


        if ($query) {
            $this->session->set_flashdata('save_success', TRUE);
        } else {
            echo "<script>";
            echo "alert('Error !');";
            echo "</script>";
        }
    }

    public function read_laws($laws_id)
    {
        $this->db->where('laws_id', $laws_id);
        $query = $this->db->get('tbl_laws');
        if ($query->num_rows() > 0) {
            $data = $query->row();
            return $data;
        }
        return FALSE;
    }

    public function edit_laws($laws_id)
    {
        $old_document = $this->db->get_where('tbl_laws', array('laws_id' => $laws_id))->row();

        $update_doc_file = !empty($_FILES['laws_pdf']['name']) && $old_document->laws_pdf != $_FILES['laws_pdf']['name'];

        // ตรวจสอบว่ามีการอัพโหลดรูปภาพใหม่หรือไม่
        if ($update_doc_file) {
            $old_file_path = './docs/file/' . $old_document->laws_pdf;
            if (file_exists($old_file_path)) {
                unlink($old_file_path);
            }

            // Check used space
            $used_space_mb = $this->space_model->get_used_space();
            $upload_limit_mb = $this->space_model->get_limit_storage();

            $total_space_required = 0;
            if (!empty($_FILES['laws_pdf']['name'])) {
                $total_space_required += $_FILES['laws_pdf']['size'];
            }

            if ($used_space_mb + ($total_space_required / (1024 * 1024 * 1024)) >= $upload_limit_mb) {
                $this->session->set_flashdata('save_error', TRUE);
                redirect('laws/editing_laws');
                return;
            }

            $config['upload_path'] = './docs/file';
            $config['allowed_types'] = 'pdf';
            $this->load->library('upload', $config);

            if (!$this->upload->do_upload('laws_pdf')) {
                echo $this->upload->display_errors();
                return;
            }

            $data = $this->upload->data();
            $filename = $data['file_name'];
        } else {
            // ใช้รูปภาพเดิม
            $filename = $old_document->laws_pdf;
        }

        $data = array(
            'laws_name' => $this->input->post('laws_name'),
            'laws_date' => $this->input->post('laws_date'),
            'laws_by' => $this->session->userdata('m_fname'), // เพิ่มชื่อคนที่เพิ่มข้อมูล
            'laws_pdf' => $filename
        );

        $this->db->where('laws_id', $laws_id);
        $query = $this->db->update('tbl_laws', $data);

        $this->space_model->update_server_current();

		// === เพิ่ม LOGGING ตรงนี้ ==============================================
        
        // เปรียบเทียบการเปลี่ยนแปลงข้อมูล
        $changes = array();
        
        if ($old_document->laws_name != $data['laws_name']) {
            $changes['laws_name'] = array(
                'old' => $old_document->laws_name,
                'new' => $data['laws_name']
            );
        }
        
        if ($old_document->laws_date != $data['laws_date']) {
            $changes['laws_date'] = array(
                'old' => $old_document->laws_date,
                'new' => $data['laws_date']
            );
        }
        
        // ตรวจสอบการเปลี่ยนแปลงไฟล์ PDF
        $file_info = array();
        if ($update_doc_file) {
            $changes['laws_pdf'] = array(
                'old' => $old_document->laws_pdf,
                'new' => $filename
            );
            $file_info['pdf_updated'] = true;
            $file_info['new_file_name'] = $filename;
            $file_info['old_file_name'] = $old_document->laws_pdf;
        }
        
        // บันทึก log การแก้ไขกฎหมาย
        $this->log_model->add_log(
            'แก้ไข',
            'กฎหมาย', // หรือชื่อเมนูที่เหมาะสม
            $data['laws_name'],
            $laws_id,
            array(
                'changes' => $changes,
                'file_info' => $file_info,
                'has_file_update' => $update_doc_file,
                'total_changes' => count($changes)
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

    public function del_laws($laws_id)
    {
        $old_document = $this->db->get_where('tbl_laws', array('laws_id' => $laws_id))->row();

        $old_file_path = './docs/file/' . $old_document->laws_pdf;
        if (file_exists($old_file_path)) {
            unlink($old_file_path);
        }

        $this->db->delete('tbl_laws', array('laws_id' => $laws_id));
		// บันทึก log การลบ =================================================
        if ($old_document) {
            $this->log_model->add_log(
                'ลบ',
                'กฎหมาย',
                $old_document->laws_name,
                $laws_id,
                array('deleted_date' => date('Y-m-d H:i:s'))
            );
        }
        // =======================================================================
    }

    public function del_laws_all($laws_topic_id)
{
    // ดึงข้อมูลหัวข้อกฎหมายก่อนลบ (สำหรับ logging)
    $topic_data = $this->db->get_where('tbl_laws_topic', array('laws_topic_id' => $laws_topic_id))->row();
    
    if (!$topic_data) {
        return false; // ถ้าไม่พบข้อมูลหัวข้อ
    }

    // ดึงข้อมูลกฎหมายทั้งหมดในหัวข้อนี้ก่อนลบ (สำหรับ logging)
    $laws = $this->db->get_where('tbl_laws', array('laws_ref_id' => $laws_topic_id))->result();
    
    // เก็บข้อมูลสำหรับ log
    $deleted_laws = array();
    $deleted_files = array();
    
    // ลบไฟล์และเก็บข้อมูลสำหรับ log
    foreach ($laws as $law) {
        // เก็บข้อมูลสำหรับ log
        $deleted_laws[] = array(
            'laws_id' => $law->laws_id,
            'laws_name' => $law->laws_name,
            'laws_pdf' => $law->laws_pdf
        );
        
        // ลบไฟล์ PDF
        $old_file_path = './docs/file/' . $law->laws_pdf;
        if (file_exists($old_file_path)) {
            unlink($old_file_path);
            $deleted_files[] = $law->laws_pdf;
        }
    }
    
    // ลบข้อมูลจากฐานข้อมูล
    // ลบกฎหมายทั้งหมดในหัวข้อนี้ก่อน
    $this->db->delete('tbl_laws', array('laws_ref_id' => $laws_topic_id));
    
    // ลบหัวข้อกฎหมาย
    $this->db->where('laws_topic_id', $laws_topic_id);
    $delete_result = $this->db->delete('tbl_laws_topic');
    
    // อัพเดตข้อมูลพื้นที่ใช้งาน
    $this->space_model->update_server_current();
    
    if ($delete_result) {
        // บันทึก log การลบหัวข้อและกฎหมายทั้งหมด
        $this->log_model->add_log(
            'ลบ',
            'กฎหมาย', // ปรับชื่อเมนูตามที่ใช้จริง
            'ลบหัวข้อ: ' . $topic_data->laws_topic_topic . ' (พร้อมกฎหมาย ' . count($deleted_laws) . ' รายการ)',
            $laws_topic_id,
            array(
                'topic_name' => $topic_data->laws_topic_topic,
                'deleted_laws_count' => count($deleted_laws),
                'deleted_files_count' => count($deleted_files),
                'deleted_laws' => $deleted_laws,
                'deleted_files' => $deleted_files,
                'deleted_date' => date('Y-m-d H:i:s')
            )
        );
        
        return true;
    }
    
    return false;
}
}
