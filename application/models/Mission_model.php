<?php
class Mission_model extends CI_Model
{
    public function __construct()
    {
        parent::__construct();
        $this->load->model('space_model');
		// log เก็บข้อมูล
        $this->load->model('log_model');
    }

    public function list_all()
    {
        $this->db->select('*');
        $this->db->from('tbl_mission');
        $this->db->group_by('tbl_mission.mission_id');
        $this->db->order_by('tbl_mission.mission_id', 'DESC');
        $query = $this->db->get();
        return $query->result();
    }

    public function list_all_pdf($mission_id)
    {
        $this->db->select('*');
        $this->db->from('tbl_mission_pdf');
        $this->db->where('mission_pdf_ref_id', $mission_id);
        return $this->db->get()->result();
    }
    public function list_all_img($mission_id)
    {
        $this->db->select('mission_img_img');
        $this->db->from('tbl_mission_img');
        $this->db->where('mission_img_ref_id', $mission_id);
        return $this->db->get()->result();
    }

    //show form edit
    public function read($mission_id)
    {
        $this->db->where('mission_id', $mission_id);
        $query = $this->db->get('tbl_mission');
        if ($query->num_rows() > 0) {
            $data = $query->row();
            return $data;
        }
        return FALSE;
    }

    public function read_pdf($mission_id)
    {
        $this->db->where('mission_pdf_ref_id', $mission_id);
        $this->db->order_by('mission_pdf_id', 'DESC');
        $query = $this->db->get('tbl_mission_pdf');
        return $query->result();
    }
    public function read_doc($mission_id)
    {
        $this->db->where('mission_file_ref_id', $mission_id);
        $this->db->order_by('mission_file_id', 'DESC');
        $query = $this->db->get('tbl_mission_file');
        return $query->result();
    }

    public function read_img($mission_id)
    {
        $this->db->where('mission_img_ref_id', $mission_id);
        $query = $this->db->get('tbl_mission_img');
        $results = $query->result();
        usort($results, function ($a, $b) {
            return strnatcmp($a->mission_img_img, $b->mission_img_img);
        });
        return $results;
    }


    public function del_pdf($pdf_id)
    {
        // ดึงชื่อไฟล์ PDF จากฐานข้อมูลโดยใช้ $pdf_id
        $this->db->select('mission_pdf_pdf, mission_pdf_ref_id');
        $this->db->where('mission_pdf_id', $pdf_id);
        $query = $this->db->get('tbl_mission_pdf');
        $file_data = $query->row();
		
		// บันทึก log การลบไฟล์ PDF =============================================
        $old_data = $this->read($file_data->mission_pdf_ref_id);
        // =======================================================================

        // ลบไฟล์จากแหล่งที่เก็บไฟล์ (อาจต้องใช้ unlink หรือวิธีอื่น)
        $file_path = './docs/file/' . $file_data->mission_pdf_pdf;
        if (file_exists($file_path)) {
            unlink($file_path);
        }

        // ลบข้อมูลของไฟล์จากฐานข้อมูล
        $this->db->where('mission_pdf_id', $pdf_id);
        $this->db->delete('tbl_mission_pdf');
        $this->space_model->update_server_current();
		// บันทึก log การลบไฟล์ PDF =============================================
        $this->log_model->add_log(
            'ลบ',
            'ภารกิจและความรับผิดชอบ',
            'ไฟล์ PDF: ' . $file_data->mission_pdf_pdf . ' จาก: ' . ($old_data ? $old_data->mission_detail : 'ไม่ระบุ'),
            $file_data->mission_pdf_ref_id,
            array('file_type' => 'PDF', 'file_name' => $file_data->mission_pdf_pdf)
        );
        // =======================================================================
        $this->session->set_flashdata('del_success', TRUE);
    }



    public function del_img($file_id)
    {
        // ดึงชื่อไฟล์ PDF จากฐานข้อมูลโดยใช้ $file_id
        $this->db->select('mission_img_img, mission_img_ref_id');
        $this->db->where('mission_img_id', $file_id);
        $query = $this->db->get('tbl_mission_img');
        $file_data = $query->row();
		
		// บันทึก log การลบรูปภาพ =================================================
        $old_data = $this->read($file_data->mission_img_ref_id);
        // =======================================================================

        // ลบไฟล์จากแหล่งที่เก็บไฟล์ (อาจต้องใช้ unlink หรือวิธีอื่น)
        $file_path = './docs/img/' . $file_data->mission_img_img;
        if (file_exists($file_path)) {
            unlink($file_path);
        }

        // ลบข้อมูลของไฟล์จากฐานข้อมูล
        $this->db->where('mission_img_id', $file_id);
        $this->db->delete('tbl_mission_img');
        $this->space_model->update_server_current();
		// บันทึก log การลบรูปภาพ =================================================
        $this->log_model->add_log(
            'ลบ',
            'ภารกิจและความรับผิดชอบ',
            'รูปภาพ: ' . $file_data->mission_img_img . ' จาก: ' . ($old_data ? $old_data->mission_detail : 'ไม่ระบุ'),
            $file_data->mission_img_ref_id,
            array('file_type' => 'IMAGE', 'file_name' => $file_data->mission_img_img)
        );
        // =======================================================================
        $this->session->set_flashdata('del_success', TRUE);
    }


    public function edit($mission_id)
    {
		// ดึงข้อมูลเก่าก่อนแก้ไข
        $old_data = $this->read($mission_id);
		
        // Update mission information
        $data = array(
            'mission_detail' => $this->input->post('mission_detail'),
            'mission_by' => $this->session->userdata('m_fname'), // เพิ่มชื่อคนที่แก้ไขข้อมูล
        );

        $this->db->where('mission_id', $mission_id);
        $this->db->update('tbl_mission', $data);

        // หาพื้นที่ว่าง และอัพโหลดlimit จากฐานข้อมูล
        $used_space = $this->space_model->get_used_space();
        $upload_limit = $this->space_model->get_limit_storage();

        $total_space_required = 0;
        // ตรวจสอบว่ามีข้อมูลรูปภาพเพิ่มเติมหรือไม่
        if (isset($_FILES['mission_img_img'])) {
            foreach ($_FILES['mission_img_img']['name'] as $index => $name) {
                if (isset($_FILES['mission_img_img']['size'][$index])) {
                    $total_space_required += $_FILES['mission_img_img']['size'][$index];
                }
            }
        }

        // ตรวจสอบว่ามีข้อมูลไฟล์ PDF หรือไม่
        if (isset($_FILES['mission_pdf_pdf'])) {
            foreach ($_FILES['mission_pdf_pdf']['name'] as $index => $name) {
                if (isset($_FILES['mission_pdf_pdf']['size'][$index])) {
                    $total_space_required += $_FILES['mission_pdf_pdf']['size'][$index];
                }
            }
        }

        // เช็คค่าว่าง
        if ($used_space + ($total_space_required / (1024 * 1024 * 1024)) >= $upload_limit) {
            $this->session->set_flashdata('save_error', TRUE);
            redirect('mission_backend/adding');
            return;
        }

        $pdf_config['upload_path'] = './docs/file';
        $pdf_config['allowed_types'] = 'pdf';
        $this->load->library('upload', $pdf_config, 'pdf_upload');

        $doc_config['upload_path'] = './docs/file';
        $doc_config['allowed_types'] = 'doc|docx|xls|xlsx|ppt|pptx';
        $this->load->library('upload', $doc_config, 'doc_upload');

        $img_config['upload_path'] = './docs/img';
        $img_config['allowed_types'] = 'gif|jpg|png|jpeg|jfif';
        $this->load->library('upload', $img_config, 'img_upload');

        // ทำการอัปโหลดรูปภาพ
        $img_main = $this->img_upload->do_upload('mission_img');

        // ตรวจสอบว่ามีการอัปโหลดรูปภาพหรือไม่
        if (!empty($img_main)) {
            $this->db->trans_start(); // เริ่ม Transaction

            // ดึงข้อมูลรูปเก่า
            $old_document = $this->db->get_where('tbl_mission', array('mission_id' => $mission_id))->row();

            // ตรวจสอบว่ามีไฟล์เก่าหรือไม่
            if ($old_document && $old_document->mission_img) {
                $old_file_path = './docs/img/' . $old_document->mission_img;

                if (file_exists($old_file_path)) {
                    unlink($old_file_path); // ลบไฟล์เก่า
                }
            }

            // ถ้ามีการอัปโหลดรูปภาพใหม่
            $img_data['mission_img'] = $this->img_upload->data('file_name');
            $this->db->where('mission_id', $mission_id);
            $this->db->update('tbl_mission', $img_data);

            $this->db->trans_complete(); // สิ้นสุด Transaction
        }

        $imgs_data = array();

        // ตรวจสอบว่ามีการอัปโหลดรูปภาพเพิ่มเติมหรือไม่
        if (!empty($_FILES['mission_img_img']['name'][0])) {

            foreach ($_FILES['mission_img_img']['name'] as $index => $name) {
                $_FILES['mission_img_img_multiple']['name'] = $name;
                $_FILES['mission_img_img_multiple']['type'] = $_FILES['mission_img_img']['type'][$index];
                $_FILES['mission_img_img_multiple']['tmp_name'] = $_FILES['mission_img_img']['tmp_name'][$index];
                $_FILES['mission_img_img_multiple']['error'] = $_FILES['mission_img_img']['error'][$index];
                $_FILES['mission_img_img_multiple']['size'] = $_FILES['mission_img_img']['size'][$index];

                if ($this->img_upload->do_upload('mission_img_img_multiple')) {
                    $upload_data = $this->img_upload->data();
                    $imgs_data[] = array(
                        'mission_img_ref_id' => $mission_id,
                        'mission_img_img' => $upload_data['file_name']
                    );
                }
            }
            $this->db->insert_batch('tbl_mission_img', $imgs_data);
        }

        $pdf_data = array();

        // ตรวจสอบว่ามีการอัปโหลด pdf เพิ่มเติมหรือไม่
        if (!empty($_FILES['mission_pdf_pdf']['name'][0])) {
            foreach ($_FILES['mission_pdf_pdf']['name'] as $index => $name) {
                $_FILES['mission_pdf_pdf_multiple']['name'] = $name;
                $_FILES['mission_pdf_pdf_multiple']['type'] = $_FILES['mission_pdf_pdf']['type'][$index];
                $_FILES['mission_pdf_pdf_multiple']['tmp_name'] = $_FILES['mission_pdf_pdf']['tmp_name'][$index];
                $_FILES['mission_pdf_pdf_multiple']['error'] = $_FILES['mission_pdf_pdf']['error'][$index];
                $_FILES['mission_pdf_pdf_multiple']['size'] = $_FILES['mission_pdf_pdf']['size'][$index];

                if ($this->pdf_upload->do_upload('mission_pdf_pdf_multiple')) {
                    $upload_data = $this->pdf_upload->data();
                    $pdf_data[] = array(
                        'mission_pdf_ref_id' => $mission_id,
                        'mission_pdf_pdf' => $upload_data['file_name']
                    );
                }
            }
            $this->db->insert_batch('tbl_mission_pdf', $pdf_data);
        }

        $this->space_model->update_server_current();
		
		        // === เก็บ log แก้ไขข้อมูล ==============================================
        $changes = array();
        if ($old_data) {
            if ($old_data->mission_detail != $data['mission_detail']) {
                $changes['mission_detail'] = array(
                    'old' => $old_data->mission_detail,
                    'new' => $data['mission_detail']
                );
            }
        }

        // เพิ่มข้อมูลไฟล์ที่อัพโหลด
        $files_info = array();
        if (!empty($imgs_data)) {
            $files_info['images_added'] = count($imgs_data);
            $files_info['image_names'] = array_column($imgs_data, 'mission_img_img');
        }
        if (!empty($pdf_data)) {
            $files_info['pdfs_added'] = count($pdf_data);
            $files_info['pdf_names'] = array_column($pdf_data, 'mission_pdf_pdf');
        }

        // บันทึก log การแก้ไขข่าว
        $this->log_model->add_log(
            'แก้ไข',
            'ภารกิจและความรับผิดชอบ',
            $data['mission_detail'],
            $mission_id,
            array(
                'changes' => $changes,
                'files_uploaded' => $files_info,
                'total_files_added' => count($imgs_data) + count($pdf_data)
            )
        );
        // =======================================================================
		
        $this->session->set_flashdata('save_success', TRUE);
    }

    public function del_mission($mission_id)
    {
        $old_document = $this->db->get_where('tbl_mission', array('mission_id' => $mission_id))->row();

        $old_file_path = './docs/img/' . $old_document->mission_img;
        if (file_exists($old_file_path)) {
            unlink($old_file_path);
        }

        $this->db->delete('tbl_mission', array('mission_id' => $mission_id));
        $this->space_model->update_server_current();
    }

    public function del_mission_pdf($mission_id)
    {
        // ดึงข้อมูลรายการ pdf ก่อน
        $files = $this->db->get_where('tbl_mission_pdf', array('mission_pdf_ref_id' => $mission_id))->result();

        // ลบ pdf จากตาราง tbl_mission_pdf
        $this->db->where('mission_pdf_ref_id', $mission_id);
        $this->db->delete('tbl_mission_pdf');

        // ลบไฟล์ pdf ที่เกี่ยวข้อง
        foreach ($files as $files) {
            $file_path = './docs/file/' . $files->mission_pdf_pdf;
            if (file_exists($file_path)) {
                unlink($file_path);
            }
        }
    }

    public function del_mission_img($mission_id)
    {
        // ดึงข้อมูลรายการรูปภาพก่อน
        $files = $this->db->get_where('tbl_mission_img', array('mission_img_ref_id' => $mission_id))->result();

        // ลบรูปภาพจากตาราง tbl_mission_file
        $this->db->where('mission_img_ref_id', $mission_id);
        $this->db->delete('tbl_mission_img');

        // ลบไฟล์รูปภาพที่เกี่ยวข้อง
        foreach ($files as $files) {
            $file_path = './docs/img/' . $files->mission_img_img;
            if (file_exists($file_path)) {
                unlink($file_path);
            }
        }
    }

    public function mission_frontend()
    {
        $this->db->select('*');
        $this->db->from('tbl_mission');
        $query = $this->db->get();
        return $query->result();
    }

    public function increment_view()
    {
        $this->db->where('mission_id', 1);
        $this->db->set('mission_view', 'mission_view + 1', false); // บวกค่า mission_view ทีละ 1
        $this->db->update('tbl_mission');
    }
    public function increment_download_mission($mission_pdf_id)
    {
        $this->db->where('mission_pdf_id', $mission_pdf_id);
        $this->db->set('mission_pdf_download', 'mission_pdf_download + 1', false); // บวกค่า mission_download ทีละ 1
        $this->db->update('tbl_mission_pdf');
    }
}
