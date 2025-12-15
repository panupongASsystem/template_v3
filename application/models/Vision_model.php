<?php
class Vision_model extends CI_Model
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
        $this->db->from('tbl_vision');
        $this->db->group_by('tbl_vision.vision_id');
        $this->db->order_by('tbl_vision.vision_id', 'DESC');
        $query = $this->db->get();
        return $query->result();
    }

    public function list_all_pdf($vision_id)
    {
        $this->db->select('*');
        $this->db->from('tbl_vision_pdf');
        $this->db->where('vision_pdf_ref_id', $vision_id);
        return $this->db->get()->result();
    }
    public function list_all_img($vision_id)
    {
        $this->db->select('vision_img_img');
        $this->db->from('tbl_vision_img');
        $this->db->where('vision_img_ref_id', $vision_id);
        return $this->db->get()->result();
    }

    //show form edit
    public function read($vision_id)
    {
        $this->db->where('vision_id', $vision_id);
        $query = $this->db->get('tbl_vision');
        if ($query->num_rows() > 0) {
            $data = $query->row();
            return $data;
        }
        return FALSE;
    }

    public function read_pdf($vision_id)
    {
        $this->db->where('vision_pdf_ref_id', $vision_id);
        $this->db->order_by('vision_pdf_id', 'DESC');
        $query = $this->db->get('tbl_vision_pdf');
        return $query->result();
    }
    public function read_doc($vision_id)
    {
        $this->db->where('vision_file_ref_id', $vision_id);
        $this->db->order_by('vision_file_id', 'DESC');
        $query = $this->db->get('tbl_vision_file');
        return $query->result();
    }

    public function read_img($vision_id)
    {
        $this->db->where('vision_img_ref_id', $vision_id);
        $query = $this->db->get('tbl_vision_img');
        $results = $query->result();
        usort($results, function ($a, $b) {
            return strnatcmp($a->vision_img_img, $b->vision_img_img);
        });
        return $results;
    }


    public function del_pdf($pdf_id)
    {
        // ดึงชื่อไฟล์ PDF จากฐานข้อมูลโดยใช้ $pdf_id
        $this->db->select('vision_pdf_pdf, vision_pdf_ref_id');
        $this->db->where('vision_pdf_id', $pdf_id);
        $query = $this->db->get('tbl_vision_pdf');
        $file_data = $query->row();
		
		// บันทึก log การลบไฟล์ PDF =============================================
        $old_data = $this->read($file_data->vision_pdf_ref_id);
        // =======================================================================

        // ลบไฟล์จากแหล่งที่เก็บไฟล์ (อาจต้องใช้ unlink หรือวิธีอื่น)
        $file_path = './docs/file/' . $file_data->vision_pdf_pdf;
        if (file_exists($file_path)) {
            unlink($file_path);
        }

        // ลบข้อมูลของไฟล์จากฐานข้อมูล
        $this->db->where('vision_pdf_id', $pdf_id);
        $this->db->delete('tbl_vision_pdf');
        $this->space_model->update_server_current();
		// บันทึก log การลบไฟล์ PDF =============================================
        $this->log_model->add_log(
            'ลบ',
            'ข้อมูลวิสัยทัศน์และพันธกิจ',
            'ไฟล์ PDF: ' . $file_data->vision_pdf_pdf . ' จาก: ' . ($old_data ? $old_data->vision_detail : 'ไม่ระบุ'),
            $file_data->vision_pdf_ref_id,
            array('file_type' => 'PDF', 'file_name' => $file_data->vision_pdf_pdf)
        );
        // =======================================================================
        $this->session->set_flashdata('del_success', TRUE);
    }



    public function del_img($file_id)
    {
        // ดึงชื่อไฟล์ PDF จากฐานข้อมูลโดยใช้ $file_id
        $this->db->select('vision_img_img, vision_img_ref_id');
        $this->db->where('vision_img_id', $file_id);
        $query = $this->db->get('tbl_vision_img');
        $file_data = $query->row();
		
		// บันทึก log การลบรูปภาพ =================================================
        $old_data = $this->read($file_data->vision_img_ref_id);
        // =======================================================================

        // ลบไฟล์จากแหล่งที่เก็บไฟล์ (อาจต้องใช้ unlink หรือวิธีอื่น)
        $file_path = './docs/img/' . $file_data->vision_img_img;
        if (file_exists($file_path)) {
            unlink($file_path);
        }

        // ลบข้อมูลของไฟล์จากฐานข้อมูล
        $this->db->where('vision_img_id', $file_id);
        $this->db->delete('tbl_vision_img');
        $this->space_model->update_server_current();
		// บันทึก log การลบรูปภาพ =================================================
        $this->log_model->add_log(
            'ลบ',
            'ข้อมูลวิสัยทัศน์และพันธกิจ',
            'รูปภาพ: ' . $file_data->vision_img_img . ' จาก: ' . ($old_data ? $old_data->vision_detail : 'ไม่ระบุ'),
            $file_data->vision_img_ref_id,
            array('file_type' => 'IMAGE', 'file_name' => $file_data->vision_img_img)
        );
        // =======================================================================
        $this->session->set_flashdata('del_success', TRUE);
    }


    public function edit($vision_id)
    {
		// ดึงข้อมูลเก่าก่อนแก้ไข
        $old_data = $this->read($vision_id);
		
        // Update vision information
        $data = array(
            'vision_detail' => $this->input->post('vision_detail'),
        	'vision_home_text' => $this->input->post('vision_home_text'), // เพิ่มบรรทัดนี้
            'vision_by' => $this->session->userdata('m_fname'), // เพิ่มชื่อคนที่แก้ไขข้อมูล
        );

        $this->db->where('vision_id', $vision_id);
        $this->db->update('tbl_vision', $data);

        // หาพื้นที่ว่าง และอัพโหลดlimit จากฐานข้อมูล
        $used_space = $this->space_model->get_used_space();
        $upload_limit = $this->space_model->get_limit_storage();

        $total_space_required = 0;
        // ตรวจสอบว่ามีข้อมูลรูปภาพเพิ่มเติมหรือไม่
        if (isset($_FILES['vision_img_img'])) {
            foreach ($_FILES['vision_img_img']['name'] as $index => $name) {
                if (isset($_FILES['vision_img_img']['size'][$index])) {
                    $total_space_required += $_FILES['vision_img_img']['size'][$index];
                }
            }
        }

        // ตรวจสอบว่ามีข้อมูลไฟล์ PDF หรือไม่
        if (isset($_FILES['vision_pdf_pdf'])) {
            foreach ($_FILES['vision_pdf_pdf']['name'] as $index => $name) {
                if (isset($_FILES['vision_pdf_pdf']['size'][$index])) {
                    $total_space_required += $_FILES['vision_pdf_pdf']['size'][$index];
                }
            }
        }

        // เช็คค่าว่าง
        if ($used_space + ($total_space_required / (1024 * 1024 * 1024)) >= $upload_limit) {
            $this->session->set_flashdata('save_error', TRUE);
            redirect('vision_backend/adding');
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
        $img_main = $this->img_upload->do_upload('vision_img');

        // ตรวจสอบว่ามีการอัปโหลดรูปภาพหรือไม่
        if (!empty($img_main)) {
            $this->db->trans_start(); // เริ่ม Transaction

            // ดึงข้อมูลรูปเก่า
            $old_document = $this->db->get_where('tbl_vision', array('vision_id' => $vision_id))->row();

            // ตรวจสอบว่ามีไฟล์เก่าหรือไม่
            if ($old_document && $old_document->vision_img) {
                $old_file_path = './docs/img/' . $old_document->vision_img;

                if (file_exists($old_file_path)) {
                    unlink($old_file_path); // ลบไฟล์เก่า
                }
            }

            // ถ้ามีการอัปโหลดรูปภาพใหม่
            $img_data['vision_img'] = $this->img_upload->data('file_name');
            $this->db->where('vision_id', $vision_id);
            $this->db->update('tbl_vision', $img_data);

            $this->db->trans_complete(); // สิ้นสุด Transaction
        }

        $imgs_data = array();

        // ตรวจสอบว่ามีการอัปโหลดรูปภาพเพิ่มเติมหรือไม่
        if (!empty($_FILES['vision_img_img']['name'][0])) {

            foreach ($_FILES['vision_img_img']['name'] as $index => $name) {
                $_FILES['vision_img_img_multiple']['name'] = $name;
                $_FILES['vision_img_img_multiple']['type'] = $_FILES['vision_img_img']['type'][$index];
                $_FILES['vision_img_img_multiple']['tmp_name'] = $_FILES['vision_img_img']['tmp_name'][$index];
                $_FILES['vision_img_img_multiple']['error'] = $_FILES['vision_img_img']['error'][$index];
                $_FILES['vision_img_img_multiple']['size'] = $_FILES['vision_img_img']['size'][$index];

                if ($this->img_upload->do_upload('vision_img_img_multiple')) {
                    $upload_data = $this->img_upload->data();
                    $imgs_data[] = array(
                        'vision_img_ref_id' => $vision_id,
                        'vision_img_img' => $upload_data['file_name']
                    );
                }
            }
            $this->db->insert_batch('tbl_vision_img', $imgs_data);
        }

        $pdf_data = array();

        // ตรวจสอบว่ามีการอัปโหลด pdf เพิ่มเติมหรือไม่
        if (!empty($_FILES['vision_pdf_pdf']['name'][0])) {
            foreach ($_FILES['vision_pdf_pdf']['name'] as $index => $name) {
                $_FILES['vision_pdf_pdf_multiple']['name'] = $name;
                $_FILES['vision_pdf_pdf_multiple']['type'] = $_FILES['vision_pdf_pdf']['type'][$index];
                $_FILES['vision_pdf_pdf_multiple']['tmp_name'] = $_FILES['vision_pdf_pdf']['tmp_name'][$index];
                $_FILES['vision_pdf_pdf_multiple']['error'] = $_FILES['vision_pdf_pdf']['error'][$index];
                $_FILES['vision_pdf_pdf_multiple']['size'] = $_FILES['vision_pdf_pdf']['size'][$index];

                if ($this->pdf_upload->do_upload('vision_pdf_pdf_multiple')) {
                    $upload_data = $this->pdf_upload->data();
                    $pdf_data[] = array(
                        'vision_pdf_ref_id' => $vision_id,
                        'vision_pdf_pdf' => $upload_data['file_name']
                    );
                }
            }
            $this->db->insert_batch('tbl_vision_pdf', $pdf_data);
        }

        $this->space_model->update_server_current();
		// === เก็บ log แก้ไขข้อมูล ==============================================
        $changes = array();
        if ($old_data) {
            if ($old_data->vision_detail != $data['vision_detail']) {
                $changes['vision_detail'] = array(
                    'old' => $old_data->vision_detail,
                    'new' => $data['vision_detail']
                );
            }
        }

        // เพิ่มข้อมูลไฟล์ที่อัพโหลด
        $files_info = array();
        if (!empty($imgs_data)) {
            $files_info['images_added'] = count($imgs_data);
            $files_info['image_names'] = array_column($imgs_data, 'vision_img_img');
        }
        if (!empty($pdf_data)) {
            $files_info['pdfs_added'] = count($pdf_data);
            $files_info['pdf_names'] = array_column($pdf_data, 'vision_pdf_pdf');
        }


        // บันทึก log การแก้ไขข่าว
        $this->log_model->add_log(
            'แก้ไข',
            'ข้อมูลวิสัยทัศน์และพันธกิจ',
            $data['vision_detail'],
            $vision_id,
            array(
                'changes' => $changes,
                'files_uploaded' => $files_info,
                'total_files_added' => count($imgs_data) + count($pdf_data)
            )
        );
        // =======================================================================
        $this->session->set_flashdata('save_success', TRUE);
    }

    public function del_vision($vision_id)
    {
        $old_document = $this->db->get_where('tbl_vision', array('vision_id' => $vision_id))->row();

        $old_file_path = './docs/img/' . $old_document->vision_img;
        if (file_exists($old_file_path)) {
            unlink($old_file_path);
        }

        $this->db->delete('tbl_vision', array('vision_id' => $vision_id));
        $this->space_model->update_server_current();
    }

    public function del_vision_pdf($vision_id)
    {
        // ดึงข้อมูลรายการ pdf ก่อน
        $files = $this->db->get_where('tbl_vision_pdf', array('vision_pdf_ref_id' => $vision_id))->result();

        // ลบ pdf จากตาราง tbl_vision_pdf
        $this->db->where('vision_pdf_ref_id', $vision_id);
        $this->db->delete('tbl_vision_pdf');

        // ลบไฟล์ pdf ที่เกี่ยวข้อง
        foreach ($files as $files) {
            $file_path = './docs/file/' . $files->vision_pdf_pdf;
            if (file_exists($file_path)) {
                unlink($file_path);
            }
        }
    }

    public function del_vision_img($vision_id)
    {
        // ดึงข้อมูลรายการรูปภาพก่อน
        $files = $this->db->get_where('tbl_vision_img', array('vision_img_ref_id' => $vision_id))->result();

        // ลบรูปภาพจากตาราง tbl_vision_file
        $this->db->where('vision_img_ref_id', $vision_id);
        $this->db->delete('tbl_vision_img');

        // ลบไฟล์รูปภาพที่เกี่ยวข้อง
        foreach ($files as $files) {
            $file_path = './docs/img/' . $files->vision_img_img;
            if (file_exists($file_path)) {
                unlink($file_path);
            }
        }
    }

    public function vision_frontend()
    {
        $this->db->select('*');
        $this->db->from('tbl_vision');
        $query = $this->db->get();
        return $query->result();
    }

    public function increment_view()
    {
        $this->db->where('vision_id', 1);
        $this->db->set('vision_view', 'vision_view + 1', false); // บวกค่า vision_view ทีละ 1
        $this->db->update('tbl_vision');
    }
    public function increment_download_vision($vision_pdf_id)
    {
        $this->db->where('vision_pdf_id', $vision_pdf_id);
        $this->db->set('vision_pdf_download', 'vision_pdf_download + 1', false); // บวกค่า vision_download ทีละ 1
        $this->db->update('tbl_vision_pdf');
    }

   public function vision_frontend_home()
{
    $this->db->select('vision_home_text');  // เปลี่ยนจาก vision_detail เป็น vision_home_text
    $this->db->from('tbl_vision');
    $query = $this->db->get();
    return $query->result();
}
}
