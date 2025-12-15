<?php
class msg_pres_model extends CI_Model
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
        $this->db->from('tbl_msg_pres');
        $this->db->group_by('tbl_msg_pres.msg_pres_id');
        $this->db->order_by('tbl_msg_pres.msg_pres_id', 'DESC');
        $query = $this->db->get();
        return $query->result();
    }

    public function list_all_pdf($msg_pres_id)
    {
        $this->db->select('*');
        $this->db->from('tbl_msg_pres_pdf');
        $this->db->where('msg_pres_pdf_ref_id', $msg_pres_id);
        return $this->db->get()->result();
    }
    public function list_all_img($msg_pres_id)
    {
        $this->db->select('msg_pres_img_img');
        $this->db->from('tbl_msg_pres_img');
        $this->db->where('msg_pres_img_ref_id', $msg_pres_id);
        return $this->db->get()->result();
    }

    //show form edit
    public function read($msg_pres_id)
    {
        $this->db->where('msg_pres_id', $msg_pres_id);
        $query = $this->db->get('tbl_msg_pres');
        if ($query->num_rows() > 0) {
            $data = $query->row();
            return $data;
        }
        return FALSE;
    }

    public function read_pdf($msg_pres_id)
    {
        $this->db->where('msg_pres_pdf_ref_id', $msg_pres_id);
        $this->db->order_by('msg_pres_pdf_id', 'DESC');
        $query = $this->db->get('tbl_msg_pres_pdf');
        return $query->result();
    }
    public function read_doc($msg_pres_id)
    {
        $this->db->where('msg_pres_file_ref_id', $msg_pres_id);
        $this->db->order_by('msg_pres_file_id', 'DESC');
        $query = $this->db->get('tbl_msg_pres_file');
        return $query->result();
    }

    public function read_img($msg_pres_id)
    {
        $this->db->where('msg_pres_img_ref_id', $msg_pres_id);
        $query = $this->db->get('tbl_msg_pres_img');
        $results = $query->result();
        usort($results, function ($a, $b) {
            return strnatcmp($a->msg_pres_img_img, $b->msg_pres_img_img);
        });
        return $results;
    }

public function del_pdf($pdf_id)
{
    // ตรวจสอบความถูกต้องของ input
    if (empty($pdf_id) || !is_numeric($pdf_id)) {
        $this->session->set_flashdata('del_error', 'รหัสไฟล์ไม่ถูกต้อง');
        return false;
    }

    // ดึงข้อมูลไฟล์ PDF จากฐานข้อมูลโดยใช้ $pdf_id
    $this->db->select('msg_pres_pdf_pdf, msg_pres_pdf_ref_id');
    $this->db->where('msg_pres_pdf_id', $pdf_id);
    $query = $this->db->get('tbl_msg_pres_pdf');
    $file_data = $query->row();

    // ตรวจสอบว่าพบข้อมูลไฟล์หรือไม่
    if (!$file_data) {
        $this->session->set_flashdata('del_error', 'ไม่พบไฟล์ PDF ที่ต้องการลบ');
        return false;
    }

    // ดึงข้อมูลสารจากนายกสำหรับ log
    $msg_pres_data = $this->read($file_data->msg_pres_pdf_ref_id);

    // ลบไฟล์จากแหล่งที่เก็บไฟล์
    $file_path = './docs/file/' . $file_data->msg_pres_pdf_pdf;
    $file_deleted = false;
    if (file_exists($file_path)) {
        $file_deleted = unlink($file_path);
    }

    // ลบข้อมูลของไฟล์จากฐานข้อมูล
    $this->db->where('msg_pres_pdf_id', $pdf_id);
    $db_deleted = $this->db->delete('tbl_msg_pres_pdf');

    // ตรวจสอบผลการลบ
    if ($db_deleted) {
        // บันทึก log การลบไฟล์ PDF =============================================
        $this->log_model->add_log(
            'ลบ',
            'สารจากนายก',
            'ไฟล์ PDF: ' . $file_data->msg_pres_pdf_pdf . ' จากสาร: ' . ($msg_pres_data ? substr($msg_pres_data->msg_pres_detail, 0, 100) . '...' : 'ไม่ระบุ'),
            $file_data->msg_pres_pdf_ref_id,
            array(
                'file_type' => 'PDF', 
                'file_name' => $file_data->msg_pres_pdf_pdf,
                'file_id' => $pdf_id,
                'file_deleted_from_disk' => $file_deleted,
                'file_path' => $file_path
            )
        );
        // =======================================================================

        // อัพเดท space usage
        $this->space_model->update_server_current();
        $this->session->set_flashdata('del_success', 'ลบไฟล์ PDF เรียบร้อยแล้ว');
        return true;
    } else {
        // ถ้าลบจากฐานข้อมูลไม่สำเร็จ
        $this->session->set_flashdata('del_error', 'เกิดข้อผิดพลาดในการลบข้อมูล');
        return false;
    }
}

public function del_img($file_id)
{
    // ตรวจสอบความถูกต้องของ input
    if (empty($file_id) || !is_numeric($file_id)) {
        $this->session->set_flashdata('del_error', 'รหัสไฟล์ไม่ถูกต้อง');
        return false;
    }

    // ดึงข้อมูลรูปภาพจากฐานข้อมูลโดยใช้ $file_id
    $this->db->select('msg_pres_img_img, msg_pres_img_ref_id');
    $this->db->where('msg_pres_img_id', $file_id);
    $query = $this->db->get('tbl_msg_pres_img');
    $file_data = $query->row();

    // ตรวจสอบว่าพบข้อมูลไฟล์หรือไม่
    if (!$file_data) {
        $this->session->set_flashdata('del_error', 'ไม่พบไฟล์รูปภาพที่ต้องการลบ');
        return false;
    }

    // ดึงข้อมูลสารจากนายกสำหรับ log
    $msg_pres_data = $this->read($file_data->msg_pres_img_ref_id);

    // ลบไฟล์จากแหล่งที่เก็บไฟล์
    $file_path = './docs/img/' . $file_data->msg_pres_img_img;
    $file_deleted = false;
    if (file_exists($file_path)) {
        $file_deleted = unlink($file_path);
    }

    // ลบข้อมูลของไฟล์จากฐานข้อมูล
    $this->db->where('msg_pres_img_id', $file_id);
    $db_deleted = $this->db->delete('tbl_msg_pres_img');

    // ตรวจสอบผลการลบ
    if ($db_deleted) {
        // บันทึก log การลบรูปภาพ =================================================
        $this->log_model->add_log(
            'ลบ',
            'สารจากนายก',
            'รูปภาพ: ' . $file_data->msg_pres_img_img . ' จากสาร: ' . ($msg_pres_data ? substr($msg_pres_data->msg_pres_detail, 0, 100) . '...' : 'ไม่ระบุ'),
            $file_data->msg_pres_img_ref_id, // แก้ไขจาก news_img_ref_id
            array(
                'file_type' => 'IMAGE', 
                'file_name' => $file_data->msg_pres_img_img,
                'file_id' => $file_id,
                'file_deleted_from_disk' => $file_deleted,
                'file_path' => $file_path
            )
        );
        // =======================================================================

        // อัพเดท space usage
        $this->space_model->update_server_current();
        $this->session->set_flashdata('del_success', 'ลบรูปภาพเรียบร้อยแล้ว');
        return true;
    } else {
        // ถ้าลบจากฐานข้อมูลไม่สำเร็จ
        $this->session->set_flashdata('del_error', 'เกิดข้อผิดพลาดในการลบข้อมูล');
        return false;
    }
}


    public function edit($msg_pres_id)
    {
		 // ดึงข้อมูลเก่าก่อนแก้ไข
        $old_data = $this->read($msg_pres_id);
		
        // Update msg_pres information
        $data = array(
            'msg_pres_detail' => $this->input->post('msg_pres_detail'),
            'msg_pres_by' => $this->session->userdata('m_fname'), // เพิ่มชื่อคนที่แก้ไขข้อมูล
        );

        $this->db->where('msg_pres_id', $msg_pres_id);
        $this->db->update('tbl_msg_pres', $data);

        // หาพื้นที่ว่าง และอัพโหลดlimit จากฐานข้อมูล
        $used_space = $this->space_model->get_used_space();
        $upload_limit = $this->space_model->get_limit_storage();

        $total_space_required = 0;
        // ตรวจสอบว่ามีข้อมูลรูปภาพเพิ่มเติมหรือไม่
        if (isset($_FILES['msg_pres_img_img'])) {
            foreach ($_FILES['msg_pres_img_img']['name'] as $index => $name) {
                if (isset($_FILES['msg_pres_img_img']['size'][$index])) {
                    $total_space_required += $_FILES['msg_pres_img_img']['size'][$index];
                }
            }
        }

        // ตรวจสอบว่ามีข้อมูลไฟล์ PDF หรือไม่
        if (isset($_FILES['msg_pres_pdf_pdf'])) {
            foreach ($_FILES['msg_pres_pdf_pdf']['name'] as $index => $name) {
                if (isset($_FILES['msg_pres_pdf_pdf']['size'][$index])) {
                    $total_space_required += $_FILES['msg_pres_pdf_pdf']['size'][$index];
                }
            }
        }

        // เช็คค่าว่าง
        if ($used_space + ($total_space_required / (1024 * 1024 * 1024)) >= $upload_limit) {
            $this->session->set_flashdata('save_error', TRUE);
            redirect('msg_pres_backend/adding');
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
        $img_main = $this->img_upload->do_upload('msg_pres_img');

        // ตรวจสอบว่ามีการอัปโหลดรูปภาพหรือไม่
        if (!empty($img_main)) {
            $this->db->trans_start(); // เริ่ม Transaction

            // ดึงข้อมูลรูปเก่า
            $old_document = $this->db->get_where('tbl_msg_pres', array('msg_pres_id' => $msg_pres_id))->row();

            // ตรวจสอบว่ามีไฟล์เก่าหรือไม่
            if ($old_document && $old_document->msg_pres_img) {
                $old_file_path = './docs/img/' . $old_document->msg_pres_img;

                if (file_exists($old_file_path)) {
                    unlink($old_file_path); // ลบไฟล์เก่า
                }
            }

            // ถ้ามีการอัปโหลดรูปภาพใหม่
            $img_data['msg_pres_img'] = $this->img_upload->data('file_name');
            $this->db->where('msg_pres_id', $msg_pres_id);
            $this->db->update('tbl_msg_pres', $img_data);

            $this->db->trans_complete(); // สิ้นสุด Transaction
        }

        $imgs_data = array();

        // ตรวจสอบว่ามีการอัปโหลดรูปภาพเพิ่มเติมหรือไม่
        if (!empty($_FILES['msg_pres_img_img']['name'][0])) {

            foreach ($_FILES['msg_pres_img_img']['name'] as $index => $name) {
                $_FILES['msg_pres_img_img_multiple']['name'] = $name;
                $_FILES['msg_pres_img_img_multiple']['type'] = $_FILES['msg_pres_img_img']['type'][$index];
                $_FILES['msg_pres_img_img_multiple']['tmp_name'] = $_FILES['msg_pres_img_img']['tmp_name'][$index];
                $_FILES['msg_pres_img_img_multiple']['error'] = $_FILES['msg_pres_img_img']['error'][$index];
                $_FILES['msg_pres_img_img_multiple']['size'] = $_FILES['msg_pres_img_img']['size'][$index];

                if ($this->img_upload->do_upload('msg_pres_img_img_multiple')) {
                    $upload_data = $this->img_upload->data();
                    $imgs_data[] = array(
                        'msg_pres_img_ref_id' => $msg_pres_id,
                        'msg_pres_img_img' => $upload_data['file_name']
                    );
                }
            }
            $this->db->insert_batch('tbl_msg_pres_img', $imgs_data);
        }

        $pdf_data = array();

        // ตรวจสอบว่ามีการอัปโหลด pdf เพิ่มเติมหรือไม่
        if (!empty($_FILES['msg_pres_pdf_pdf']['name'][0])) {
            foreach ($_FILES['msg_pres_pdf_pdf']['name'] as $index => $name) {
                $_FILES['msg_pres_pdf_pdf_multiple']['name'] = $name;
                $_FILES['msg_pres_pdf_pdf_multiple']['type'] = $_FILES['msg_pres_pdf_pdf']['type'][$index];
                $_FILES['msg_pres_pdf_pdf_multiple']['tmp_name'] = $_FILES['msg_pres_pdf_pdf']['tmp_name'][$index];
                $_FILES['msg_pres_pdf_pdf_multiple']['error'] = $_FILES['msg_pres_pdf_pdf']['error'][$index];
                $_FILES['msg_pres_pdf_pdf_multiple']['size'] = $_FILES['msg_pres_pdf_pdf']['size'][$index];

                if ($this->pdf_upload->do_upload('msg_pres_pdf_pdf_multiple')) {
                    $upload_data = $this->pdf_upload->data();
                    $pdf_data[] = array(
                        'msg_pres_pdf_ref_id' => $msg_pres_id,
                        'msg_pres_pdf_pdf' => $upload_data['file_name']
                    );
                }
            }
            $this->db->insert_batch('tbl_msg_pres_pdf', $pdf_data);
        }

        $this->space_model->update_server_current();
		
		        // === เก็บ log แก้ไขข้อมูล ==============================================
        $changes = array();
        if ($old_data) {
            if ($old_data->msg_pres_detail != $data['msg_pres_detail']) {
                $changes['msg_pres_detail'] = array(
                    'old' => $old_data->msg_pres_detail,
                    'new' => $data['msg_pres_detail']
                );
            }
            if ($old_data->msg_pres_by != $data['msg_pres_by']) {
                $changes['msg_pres_by'] = array(
                    'old' => $old_data->msg_pres_by,
                    'new' => $data['msg_pres_by']
                );
            }
        }

        // เพิ่มข้อมูลไฟล์ที่อัพโหลด
        $files_info = array();
        if (!empty($imgs_data)) {
            $files_info['images_added'] = count($imgs_data);
            $files_info['image_names'] = array_column($imgs_data, 'msg_pres_img_img');
        }
        if (!empty($pdf_data)) {
            $files_info['pdfs_added'] = count($pdf_data);
            $files_info['pdf_names'] = array_column($pdf_data, 'msg_pres_pdf_pdf');
        }

        // บันทึก log การแก้ไขข่าว
        $this->log_model->add_log(
            'แก้ไข',
            'สารจากนายก',
            $data['msg_pres_detail'],
            $msg_pres_detail_id,
            array(
                'changes' => $changes,
                'files_uploaded' => $files_info,
                'total_files_added' => count($imgs_data) + count($pdf_data)
            )
        );
        // =======================================================================
		
        $this->session->set_flashdata('save_success', TRUE);
    }

    public function del_msg_pres($msg_pres_id)
    {
        $old_document = $this->db->get_where('tbl_msg_pres', array('msg_pres_id' => $msg_pres_id))->row();

        $old_file_path = './docs/img/' . $old_document->msg_pres_img;
        if (file_exists($old_file_path)) {
            unlink($old_file_path);
        }

        $this->db->delete('tbl_msg_pres', array('msg_pres_id' => $msg_pres_id));
        $this->space_model->update_server_current();
    }

    public function del_msg_pres_pdf($msg_pres_id)
    {
        // ดึงข้อมูลรายการ pdf ก่อน
        $files = $this->db->get_where('tbl_msg_pres_pdf', array('msg_pres_pdf_ref_id' => $msg_pres_id))->result();

        // ลบ pdf จากตาราง tbl_msg_pres_pdf
        $this->db->where('msg_pres_pdf_ref_id', $msg_pres_id);
        $this->db->delete('tbl_msg_pres_pdf');

        // ลบไฟล์ pdf ที่เกี่ยวข้อง
        foreach ($files as $files) {
            $file_path = './docs/file/' . $files->msg_pres_pdf_pdf;
            if (file_exists($file_path)) {
                unlink($file_path);
            }
        }
    }

    public function del_msg_pres_img($msg_pres_id)
    {
        // ดึงข้อมูลรายการรูปภาพก่อน
        $files = $this->db->get_where('tbl_msg_pres_img', array('msg_pres_img_ref_id' => $msg_pres_id))->result();

        // ลบรูปภาพจากตาราง tbl_msg_pres_file
        $this->db->where('msg_pres_img_ref_id', $msg_pres_id);
        $this->db->delete('tbl_msg_pres_img');

        // ลบไฟล์รูปภาพที่เกี่ยวข้อง
        foreach ($files as $files) {
            $file_path = './docs/img/' . $files->msg_pres_img_img;
            if (file_exists($file_path)) {
                unlink($file_path);
            }
        }
    }

    public function msg_pres_frontend()
    {
        $this->db->select('*');
        $this->db->from('tbl_msg_pres');
        $query = $this->db->get();
        return $query->result();
    }

    public function increment_view()
    {
        $this->db->where('msg_pres_id', 1);
        $this->db->set('msg_pres_view', 'msg_pres_view + 1', false); // บวกค่า msg_pres_view ทีละ 1
        $this->db->update('tbl_msg_pres');
    }
    public function increment_download_msg_pres($msg_pres_pdf_id)
    {
        $this->db->where('msg_pres_pdf_id', $msg_pres_pdf_id);
        $this->db->set('msg_pres_pdf_download', 'msg_pres_pdf_download + 1', false); // บวกค่า msg_pres_download ทีละ 1
        $this->db->update('tbl_msg_pres_pdf');
    }
}
