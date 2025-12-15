<?php
class Taepts_model extends CI_Model
{
    public function __construct()
    {
        parent::__construct();
        $this->load->model('space_model');
        // log เก็บข้อมูล
        $this->load->model('log_model');
    }

    public function add_type()
    {

        $data = array(
            'taepts_type_name' => $this->input->post('taepts_type_name'),
            'taepts_type_date' => $this->input->post('taepts_type_date'),
            'taepts_type_by' => $this->session->userdata('m_fname'),
        );

        $query = $this->db->insert('tbl_taepts_type', $data);
        // บันทึก log การเพิ่มข้อมูล =================================================
        $taepts_type_id = $this->db->insert_id();
        // =======================================================================

        $this->space_model->update_server_current();

        // บันทึก log การเพิ่มข้อมูล =================================================
        $this->log_model->add_log(
            'เพิ่ม',
            'มาตรฐานการส่งเสริมคุณธรรมและความโปร่งใส',
            'หัวข้อ : ' . $data['taepts_type_name'],
            $taepts_type_id,
            array(
                'info' => array(
                    'taepts_type_date' => $data['taepts_type_date'],
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

    public function edit_type($taepts_type_id)
    {
        // ดึงข้อมูลเก่าก่อนแก้ไข
        $old_data = $this->read_type($taepts_type_id);

        $data = array(
            'taepts_type_name' => $this->input->post('taepts_type_name'),
            'taepts_type_date' => $this->input->post('taepts_type_date'),
            'taepts_type_by' => $this->session->userdata('m_fname'),
        );

        $this->db->where('taepts_type_id', $taepts_type_id);
        $this->db->update('tbl_taepts_type', $data);

        $this->space_model->update_server_current();

        if ($data) {
            // บันทึก log การแก้ไขหัวข้อ ========================================
            $changes = array();
            if ($old_data) {
                if ($old_data->taepts_type_name != $data['taepts_type_name']) {
                    $changes['taepts_type_name'] = array(
                        'old' => $old_data->taepts_type_name,
                        'new' => $data['taepts_type_name']
                    );
                }
                if ($old_data->taepts_type_date != $data['taepts_type_date']) {
                    $changes['taepts_type_date'] = array(
                        'old' => $old_data->taepts_type_date,
                        'new' => $data['taepts_type_date']
                    );
                }
            }

            $this->log_model->add_log(
                'แก้ไข',
                'มาตรฐานการส่งเสริมคุณธรรมและความโปร่งใส',
                'หัวข้อ: ' . $data['taepts_type_name'],
                $taepts_type_id,
                array(
                    'changes' => $changes,
                    'total_changes' => count($changes)
                )
            );
            // =================================================================

            $this->session->set_flashdata('save_success', TRUE);
        } else {
            echo "<script>";
            echo "alert('Error !');";
            echo "</script>";
        }
    }

    public function list_type()
    {
        $this->db->select('*');
        $this->db->from('tbl_taepts_type');
        $this->db->group_by('tbl_taepts_type.taepts_type_id');
        $this->db->order_by('tbl_taepts_type.taepts_type_date', 'DESC');
        $query = $this->db->get();
        return $query->result();
    }

    public function list_taepts_type()
    {
        $this->db->order_by('taepts_type_id', 'DESC');
        $query = $this->db->get('tbl_taepts_type');
        return $query->result();
    }


    public function add()
    {
        // Configure PDF upload
        $pdf_config['upload_path'] = './docs/file';
        $pdf_config['allowed_types'] = 'pdf';
        $this->load->library('upload', $pdf_config, 'pdf_upload');

        // Configure image upload
        $img_config['upload_path'] = './docs/img';
        $img_config['allowed_types'] = 'gif|jpg|png|jpeg|jfif';
        $this->load->library('upload', $img_config, 'img_upload');

        // Configure Doc upload
        $doc_config['upload_path'] = './docs/file';
        $doc_config['allowed_types'] = 'doc|docx|xls|xlsx|ppt|pptx';
        $this->load->library('upload', $doc_config, 'doc_upload');

        // กำหนดค่าใน $taepts_data
        $taepts_data = array(
            'taepts_ref_id' => $this->input->post('taepts_ref_id'),
            'taepts_name' => $this->input->post('taepts_name'),
            'taepts_detail' => $this->input->post('taepts_detail'),
            'taepts_date' => $this->input->post('taepts_date'),
            'taepts_by' => $this->session->userdata('m_fname'), // เพิ่มชื่อคนที่แก้ไขข้อมูล
        );

        // ทำการอัปโหลดรูปภาพ
        $img_main = $this->img_upload->do_upload('taepts_img');
        // ตรวจสอบว่ามีการอัปโหลดรูปภาพหรือไม่
        if (!empty($img_main)) {
            // ถ้ามีการอัปโหลดรูปภาพ
            $taepts_data['taepts_img'] = $this->img_upload->data('file_name');
        }
        // เพิ่มข้อมูลลงในฐานข้อมูล
        $this->db->insert('tbl_taepts', $taepts_data);
        $taepts_id = $this->db->insert_id();

        // หาพื้นที่ว่าง และอัพโหลดlimit จากฐานข้อมูล
        $used_space = $this->space_model->get_used_space();
        $upload_limit = $this->space_model->get_limit_storage();

        $total_space_required = 0;
        // ตรวจสอบว่ามีข้อมูลรูปภาพเพิ่มเติมหรือไม่
        if (isset($_FILES['taepts_img_img'])) {
            foreach ($_FILES['taepts_img_img']['name'] as $index => $name) {
                if (isset($_FILES['taepts_img_img']['size'][$index])) {
                    $total_space_required += $_FILES['taepts_img_img']['size'][$index];
                }
            }
        }

        // ตรวจสอบว่ามีข้อมูลไฟล์ PDF หรือไม่
        if (isset($_FILES['taepts_pdf_pdf'])) {
            foreach ($_FILES['taepts_pdf_pdf']['name'] as $index => $name) {
                if (isset($_FILES['taepts_pdf_pdf']['size'][$index])) {
                    $total_space_required += $_FILES['taepts_pdf_pdf']['size'][$index];
                }
            }
        }

        // ตรวจสอบว่ามีข้อมูลไฟล์ doc หรือไม่
        if (isset($_FILES['taepts_file_doc'])) {
            foreach ($_FILES['taepts_file_doc']['name'] as $index => $name) {
                if (isset($_FILES['taepts_file_doc']['size'][$index])) {
                    $total_space_required += $_FILES['taepts_file_doc']['size'][$index];
                }
            }
        }

        // เช็คค่าว่าง
        if ($used_space + ($total_space_required / (1024 * 1024 * 1024)) >= $upload_limit) {
            $this->session->set_flashdata('save_error', TRUE);
            redirect('taepts_backend/adding');
            return;
        }

        $imgs_data = array();

        // ตรวจสอบว่ามีการอัปโหลดรูปภาพเพิ่มเติมหรือไม่
        if (!empty($_FILES['taepts_img_img']['name'][0])) {
            foreach ($_FILES['taepts_img_img']['name'] as $index => $name) {
                $_FILES['taepts_img_img_multiple']['name'] = $name;
                $_FILES['taepts_img_img_multiple']['type'] = $_FILES['taepts_img_img']['type'][$index];
                $_FILES['taepts_img_img_multiple']['tmp_name'] = $_FILES['taepts_img_img']['tmp_name'][$index];
                $_FILES['taepts_img_img_multiple']['error'] = $_FILES['taepts_img_img']['error'][$index];
                $_FILES['taepts_img_img_multiple']['size'] = $_FILES['taepts_img_img']['size'][$index];

                if ($this->img_upload->do_upload('taepts_img_img_multiple')) {
                    $upload_data = $this->img_upload->data();
                    $imgs_data[] = array(
                        'taepts_img_ref_id' => $taepts_id,
                        'taepts_img_img' => $upload_data['file_name']
                    );
                }
            }
            $this->db->insert_batch('tbl_taepts_img', $imgs_data);
        }

        $pdf_data = array();

        // ตรวจสอบว่ามีการอัปโหลดไฟล์PDFเพิ่มเติมหรือไม่
        if (!empty($_FILES['taepts_pdf_pdf']['name'][0])) {
            foreach ($_FILES['taepts_pdf_pdf']['name'] as $index => $name) {
                $_FILES['taepts_pdf_pdf_multiple']['name'] = $name;
                $_FILES['taepts_pdf_pdf_multiple']['type'] = $_FILES['taepts_pdf_pdf']['type'][$index];
                $_FILES['taepts_pdf_pdf_multiple']['tmp_name'] = $_FILES['taepts_pdf_pdf']['tmp_name'][$index];
                $_FILES['taepts_pdf_pdf_multiple']['error'] = $_FILES['taepts_pdf_pdf']['error'][$index];
                $_FILES['taepts_pdf_pdf_multiple']['size'] = $_FILES['taepts_pdf_pdf']['size'][$index];

                if ($this->pdf_upload->do_upload('taepts_pdf_pdf_multiple')) {
                    $upload_data = $this->pdf_upload->data();
                    $pdf_data[] = array(
                        'taepts_pdf_ref_id' => $taepts_id,
                        'taepts_pdf_pdf' => $upload_data['file_name']
                    );
                }
            }
            $this->db->insert_batch('tbl_taepts_pdf', $pdf_data);
        }

        $doc_data = array();

        // ตรวจสอบว่ามีการอัปโหลดไฟล์Docเพิ่มเติมหรือไม่
        if (!empty($_FILES['taepts_file_doc']['name'][0])) {
            foreach ($_FILES['taepts_file_doc']['name'] as $index => $name) {
                $_FILES['taepts_file_doc_multiple']['name'] = $name;
                $_FILES['taepts_file_doc_multiple']['type'] = $_FILES['taepts_file_doc']['type'][$index];
                $_FILES['taepts_file_doc_multiple']['tmp_name'] = $_FILES['taepts_file_doc']['tmp_name'][$index];
                $_FILES['taepts_file_doc_multiple']['error'] = $_FILES['taepts_file_doc']['error'][$index];
                $_FILES['taepts_file_doc_multiple']['size'] = $_FILES['taepts_file_doc']['size'][$index];

                if ($this->doc_upload->do_upload('taepts_file_doc_multiple')) {
                    $upload_data = $this->doc_upload->data();
                    $doc_data[] = array(
                        'taepts_file_ref_id' => $taepts_id,
                        'taepts_file_doc' => $upload_data['file_name']
                    );
                }
            }
            $this->db->insert_batch('tbl_taepts_file', $doc_data);
        }
        $this->space_model->update_server_current();

        // บันทึก log การเพิ่มข้อมูล การเงิน ======================================
        $this->log_model->add_log(
            'เพิ่ม',
            'มาตรฐานการส่งเสริมคุณธรรมและความโปร่งใส',
            $taepts_data['taepts_name'],
            $taepts_id,
            array(
                'taepts_ref_id' => $taepts_data['taepts_ref_id'],
                'files_uploaded' => array(
                    'images' => count($imgs_data),
                    'pdfs' => count($pdf_data),
                    'docs' => count($doc_data)
                )
            )
        );
        // ====================================================================

        $this->session->set_flashdata('save_success', TRUE);
    }

    public function list_all($taepts_type_id)
    {
        $this->db->select('*');
        $this->db->from('tbl_taepts');
        $this->db->join('tbl_taepts_type', 'tbl_taepts.taepts_ref_id = tbl_taepts_type.taepts_type_id', 'inner'); // เปลี่ยนเป็น INNER JOIN
        $this->db->where('tbl_taepts_type.taepts_type_id', $taepts_type_id);
        $query = $this->db->get();
        return $query->result();
    }


    public function list_all_doc($taepts_id)
    {
        $this->db->select('taepts_file_doc');
        $this->db->from('tbl_taepts_file');
        $this->db->where('taepts_file_ref_id', $taepts_id);
        return $this->db->get()->result();
    }
    public function list_all_pdf($taepts_id)
    {
        $this->db->select('taepts_pdf_pdf');
        $this->db->from('tbl_taepts_pdf');
        $this->db->where('taepts_pdf_ref_id', $taepts_id);
        return $this->db->get()->result();
    }

    //show form edit
    public function read_type($taepts_id)
    {
        $this->db->where('taepts_type_id', $taepts_id);
        $query = $this->db->get('tbl_taepts_type');
        if ($query->num_rows() > 0) {
            $data = $query->row();
            return $data;
        }
        return FALSE;
    }

    public function read($taepts_id)
    {
        $this->db->select('m.*,t.taepts_type_name');
        $this->db->from('tbl_taepts as m');
        $this->db->join('tbl_taepts_type as t', 'm.taepts_ref_id=t.taepts_type_id');
        $this->db->where('m.taepts_id', $taepts_id);
        $query = $this->db->get('tbl_taepts');
        if ($query->num_rows() > 0) {
            $data = $query->row();
            return $data;
        }
        return FALSE;
    }

    public function read_pdf($taepts_id)
    {
        $this->db->where('taepts_pdf_ref_id', $taepts_id);
        $this->db->order_by('taepts_pdf_id', 'DESC');
        $query = $this->db->get('tbl_taepts_pdf');
        return $query->result();
    }
    public function read_doc($taepts_id)
    {
        $this->db->where('taepts_file_ref_id', $taepts_id);
        $this->db->order_by('taepts_file_id', 'DESC');
        $query = $this->db->get('tbl_taepts_file');
        return $query->result();
    }

    public function read_img($taepts_id)
    {
        $this->db->where('taepts_img_ref_id', $taepts_id);
        $this->db->order_by('taepts_img_id', 'DESC');
        $query = $this->db->get('tbl_taepts_img');
        return $query->result();
    }

    public function del_pdf($pdf_id)
    {
        // ดึงชื่อไฟล์ PDF จากฐานข้อมูลโดยใช้ $pdf_id
        $this->db->select('taepts_pdf_pdf, taepts_pdf_ref_id');
        $this->db->where('taepts_pdf_id', $pdf_id);
        $query = $this->db->get('tbl_taepts_pdf');
        $file_data = $query->row();

        // ดึงชื่อข้อมูลการเงินสำหรับ log
        $taepts_data = $this->read($file_data->taepts_pdf_ref_id);

        // ลบไฟล์จากแหล่งที่เก็บไฟล์ (อาจต้องใช้ unlink หรือวิธีอื่น)
        $file_path = './docs/file/' . $file_data->taepts_pdf_pdf;
        if (file_exists($file_path)) {
            unlink($file_path);
        }

        // ลบข้อมูลของไฟล์จากฐานข้อมูล
        $this->db->where('taepts_pdf_id', $pdf_id);
        $this->db->delete('tbl_taepts_pdf');

        // บันทึก log การลบไฟล์ PDF ============================================
        $this->log_model->add_log(
            'ลบ',
            'มาตรฐานการส่งเสริมคุณธรรมและความโปร่งใส',
            'ไฟล์ PDF: ' . $file_data->taepts_pdf_pdf . ' จากเอกสาร: ' . ($taepts_data ? $taepts_data->taepts_name : 'ไม่ระบุ'),
            $file_data->taepts_pdf_ref_id,
            array('file_type' => 'PDF', 'file_name' => $file_data->taepts_pdf_pdf)
        );
        // ==================================================================

        $this->space_model->update_server_current();
        $this->session->set_flashdata('del_success', TRUE);
    }

    public function del_doc($doc_id)
    {
        // ดึงชื่อไฟล์ DOC จากฐานข้อมูลโดยใช้ $doc_id
        $this->db->select('taepts_file_doc, taepts_file_ref_id');
        $this->db->where('taepts_file_id', $doc_id);
        $query = $this->db->get('tbl_taepts_file');
        $file_data = $query->row();

        // ดึงชื่อข้อมูลการเงินสำหรับ log
        $taepts_data = $this->read($file_data->taepts_file_ref_id);

        // ลบไฟล์จากแหล่งที่เก็บไฟล์ (อาจต้องใช้ unlink หรือวิธีอื่น)
        $file_path = './docs/file/' . $file_data->taepts_file_doc;
        if (file_exists($file_path)) {
            unlink($file_path);
        }

        // ลบข้อมูลของไฟล์จากฐานข้อมูล
        $this->db->where('taepts_file_id', $doc_id);
        $this->db->delete('tbl_taepts_file');

        // บันทึก log การลบไฟล์ DOC ==========================================
        $this->log_model->add_log(
            'ลบ',
            'มาตรฐานการส่งเสริมคุณธรรมและความโปร่งใส',
            'ไฟล์ DOC: ' . $file_data->taepts_file_doc . ' จากเอกสาร: ' . ($taepts_data ? $taepts_data->taepts_name : 'ไม่ระบุ'),
            $file_data->taepts_file_ref_id,
            array('file_type' => 'DOC', 'file_name' => $file_data->taepts_file_doc)
        );
        // ==================================================================

        $this->space_model->update_server_current();
        $this->session->set_flashdata('del_success', TRUE);
    }

    public function del_img($file_id)
    {
        // ดึงชื่อไฟล์ IMG จากฐานข้อมูลโดยใช้ $file_id
        $this->db->select('taepts_img_img, taepts_img_ref_id');
        $this->db->where('taepts_img_id', $file_id);
        $query = $this->db->get('tbl_taepts_img');
        $file_data = $query->row();

        // ดึงชื่อข้อมูลการเงินสำหรับ log
        $taepts_data = $this->read($file_data->taepts_img_ref_id);

        // ลบไฟล์จากแหล่งที่เก็บไฟล์ (อาจต้องใช้ unlink หรือวิธีอื่น)
        $file_path = './docs/img/' . $file_data->taepts_img_img;
        if (file_exists($file_path)) {
            unlink($file_path);
        }

        // บันทึก log การลบรูปภาพ ===============================================
        $this->log_model->add_log(
            'ลบ',
            'มาตรฐานการส่งเสริมคุณธรรมและความโปร่งใส',
            'รูปภาพ: ' . $file_data->taepts_img_img . ' จากเอกสาร: ' . ($taepts_data ? $taepts_data->taepts_name : 'ไม่ระบุ'),
            $file_data->taepts_img_ref_id,
            array('file_type' => 'IMAGE', 'file_name' => $file_data->taepts_img_img)
        );
        // ==================================================================

        // ลบข้อมูลของไฟล์จากฐานข้อมูล
        $this->db->where('taepts_img_id', $file_id);
        $this->db->delete('tbl_taepts_img');
        $this->space_model->update_server_current();
        $this->session->set_flashdata('del_success', TRUE);
    }

    public function edit($taepts_id)
    {
        // ดึงข้อมูลเก่าก่อนแก้ไข
        $old_data = $this->read($taepts_id);

        // Update taepts information
        $data = array(
            'taepts_ref_id' => $this->input->post('taepts_ref_id'),
            'taepts_name' => $this->input->post('taepts_name'),
            'taepts_detail' => $this->input->post('taepts_detail'),
            'taepts_date' => $this->input->post('taepts_date'),
            'taepts_link' => $this->input->post('taepts_link'),
            'taepts_by' => $this->session->userdata('m_fname'), // เพิ่มชื่อคนที่แก้ไขข้อมูล
        );

        $this->db->where('taepts_id', $taepts_id);
        $this->db->update('tbl_taepts', $data);

        // หาพื้นที่ว่าง และอัพโหลดlimit จากฐานข้อมูล
        $used_space = $this->space_model->get_used_space();
        $upload_limit = $this->space_model->get_limit_storage();

        $total_space_required = 0;
        // ตรวจสอบว่ามีข้อมูลรูปภาพเพิ่มเติมหรือไม่
        if (isset($_FILES['taepts_img_img'])) {
            foreach ($_FILES['taepts_img_img']['name'] as $index => $name) {
                if (isset($_FILES['taepts_img_img']['size'][$index])) {
                    $total_space_required += $_FILES['taepts_img_img']['size'][$index];
                }
            }
        }

        // ตรวจสอบว่ามีข้อมูลไฟล์ PDF หรือไม่
        if (isset($_FILES['taepts_pdf_pdf'])) {
            foreach ($_FILES['taepts_pdf_pdf']['name'] as $index => $name) {
                if (isset($_FILES['taepts_pdf_pdf']['size'][$index])) {
                    $total_space_required += $_FILES['taepts_pdf_pdf']['size'][$index];
                }
            }
        }

        // ตรวจสอบว่ามีข้อมูลไฟล์ doc หรือไม่
        if (isset($_FILES['taepts_file_doc'])) {
            foreach ($_FILES['taepts_file_doc']['name'] as $index => $name) {
                if (isset($_FILES['taepts_file_doc']['size'][$index])) {
                    $total_space_required += $_FILES['taepts_file_doc']['size'][$index];
                }
            }
        }

        // เช็คค่าว่าง
        if ($used_space + ($total_space_required / (1024 * 1024 * 1024)) >= $upload_limit) {
            $this->session->set_flashdata('save_error', TRUE);
            redirect('taepts_backend/adding');
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
        $img_main = $this->img_upload->do_upload('taepts_img');

        // ตรวจสอบว่ามีการอัปโหลดรูปภาพหรือไม่
        if (!empty($img_main)) {
            $this->db->trans_start(); // เริ่ม Transaction

            // ดึงข้อมูลรูปเก่า
            $old_document = $this->db->get_where('tbl_taepts', array('taepts_id' => $taepts_id))->row();

            // ตรวจสอบว่ามีไฟล์เก่าหรือไม่
            if ($old_document && $old_document->taepts_img) {
                $old_file_path = './docs/img/' . $old_document->taepts_img;

                if (file_exists($old_file_path)) {
                    unlink($old_file_path); // ลบไฟล์เก่า
                }
            }

            // ถ้ามีการอัปโหลดรูปภาพใหม่
            $img_data['taepts_img'] = $this->img_upload->data('file_name');
            $this->db->where('taepts_id', $taepts_id);
            $this->db->update('tbl_taepts', $img_data);

            $this->db->trans_complete(); // สิ้นสุด Transaction
        }

        $imgs_data = array();

        // ตรวจสอบว่ามีการอัปโหลดรูปภาพเพิ่มเติมหรือไม่
        if (!empty($_FILES['taepts_img_img']['name'][0])) {

            foreach ($_FILES['taepts_img_img']['name'] as $index => $name) {
                $_FILES['taepts_img_img_multiple']['name'] = $name;
                $_FILES['taepts_img_img_multiple']['type'] = $_FILES['taepts_img_img']['type'][$index];
                $_FILES['taepts_img_img_multiple']['tmp_name'] = $_FILES['taepts_img_img']['tmp_name'][$index];
                $_FILES['taepts_img_img_multiple']['error'] = $_FILES['taepts_img_img']['error'][$index];
                $_FILES['taepts_img_img_multiple']['size'] = $_FILES['taepts_img_img']['size'][$index];

                if ($this->img_upload->do_upload('taepts_img_img_multiple')) {
                    $upload_data = $this->img_upload->data();
                    $imgs_data[] = array(
                        'taepts_img_ref_id' => $taepts_id,
                        'taepts_img_img' => $upload_data['file_name']
                    );
                }
            }
            $this->db->insert_batch('tbl_taepts_img', $imgs_data);
        }

        $pdf_data = array();

        // ตรวจสอบว่ามีการอัปโหลด pdf เพิ่มเติมหรือไม่
        if (!empty($_FILES['taepts_pdf_pdf']['name'][0])) {
            foreach ($_FILES['taepts_pdf_pdf']['name'] as $index => $name) {
                $_FILES['taepts_pdf_pdf_multiple']['name'] = $name;
                $_FILES['taepts_pdf_pdf_multiple']['type'] = $_FILES['taepts_pdf_pdf']['type'][$index];
                $_FILES['taepts_pdf_pdf_multiple']['tmp_name'] = $_FILES['taepts_pdf_pdf']['tmp_name'][$index];
                $_FILES['taepts_pdf_pdf_multiple']['error'] = $_FILES['taepts_pdf_pdf']['error'][$index];
                $_FILES['taepts_pdf_pdf_multiple']['size'] = $_FILES['taepts_pdf_pdf']['size'][$index];

                if ($this->pdf_upload->do_upload('taepts_pdf_pdf_multiple')) {
                    $upload_data = $this->pdf_upload->data();
                    $pdf_data[] = array(
                        'taepts_pdf_ref_id' => $taepts_id,
                        'taepts_pdf_pdf' => $upload_data['file_name']
                    );
                }
            }
            $this->db->insert_batch('tbl_taepts_pdf', $pdf_data);
        }

        $doc_data = array();

        // ตรวจสอบว่ามีการอัปโหลด doc เพิ่มเติมหรือไม่
        if (!empty($_FILES['taepts_file_doc']['name'][0])) {
            foreach ($_FILES['taepts_file_doc']['name'] as $index => $name) {
                $_FILES['taepts_file_doc_multiple']['name'] = $name;
                $_FILES['taepts_file_doc_multiple']['type'] = $_FILES['taepts_file_doc']['type'][$index];
                $_FILES['taepts_file_doc_multiple']['tmp_name'] = $_FILES['taepts_file_doc']['tmp_name'][$index];
                $_FILES['taepts_file_doc_multiple']['error'] = $_FILES['taepts_file_doc']['error'][$index];
                $_FILES['taepts_file_doc_multiple']['size'] = $_FILES['taepts_file_doc']['size'][$index];

                if ($this->doc_upload->do_upload('taepts_file_doc_multiple')) {
                    $upload_data = $this->doc_upload->data();
                    $doc_data[] = array(
                        'taepts_file_ref_id' => $taepts_id,
                        'taepts_file_doc' => $upload_data['file_name']
                    );
                }
            }
            $this->db->insert_batch('tbl_taepts_file', $doc_data);
        }
        $this->space_model->update_server_current();

        // บันทึก log การแก้ไขข้อมูลการเงิน =====================================
        // เปรียบเทียบการเปลี่ยนแปลงข้อมูล
        $changes = array();
        if ($old_data) {
            if ($old_data->taepts_ref_id != $data['taepts_ref_id']) {
                $changes['taepts_ref_id'] = array(
                    'old' => $old_data->taepts_ref_id,
                    'new' => $data['taepts_ref_id']
                );
            }
            if ($old_data->taepts_name != $data['taepts_name']) {
                $changes['taepts_name'] = array(
                    'old' => $old_data->taepts_name,
                    'new' => $data['taepts_name']
                );
            }
            if ($old_data->taepts_detail != $data['taepts_detail']) {
                $changes['taepts_detail'] = array(
                    'old' => 'มีการเปลี่ยนแปลง',
                    'new' => 'รายละเอียดใหม่'
                );
            }
            if ($old_data->taepts_date != $data['taepts_date']) {
                $changes['taepts_date'] = array(
                    'old' => $old_data->taepts_date,
                    'new' => $data['taepts_date']
                );
            }
            if (isset($data['taepts_link']) && $old_data->taepts_link != $data['taepts_link']) {
                $changes['taepts_link'] = array(
                    'old' => $old_data->taepts_link,
                    'new' => $data['taepts_link']
                );
            }
        }

        // เพิ่มข้อมูลไฟล์ที่อัพโหลด
        $files_info = array();
        if (!empty($imgs_data)) {
            $files_info['images_added'] = count($imgs_data);
            $files_info['image_names'] = array_column($imgs_data, 'taepts_img_img');
        }
        if (!empty($pdf_data)) {
            $files_info['pdfs_added'] = count($pdf_data);
            $files_info['pdf_names'] = array_column($pdf_data, 'taepts_pdf_pdf');
        }
        if (!empty($doc_data)) {
            $files_info['docs_added'] = count($doc_data);
            $files_info['doc_names'] = array_column($doc_data, 'taepts_file_doc');
        }

        // บันทึก log การแก้ไขข้อมูลการเงิน
        $this->log_model->add_log(
            'แก้ไข',
            'มาตรฐานการส่งเสริมคุณธรรมและความโปร่งใส',
            $data['taepts_name'],
            $taepts_id,
            array(
                'changes' => $changes,
                'files_uploaded' => $files_info,
                'total_files_added' => count($imgs_data) + count($pdf_data) + count($doc_data)
            )
        );
        // ====================================================================

        $this->session->set_flashdata('save_success', TRUE);
    }

    public function del_taepts($taepts_id)
    {
        // ดึงข้อมูลก่อนลบ
        $taepts_data = $this->read($taepts_id);

        $old_document = $this->db->get_where('tbl_taepts', array('taepts_id' => $taepts_id))->row();

        $old_file_path = './docs/img/' . $old_document->taepts_img;
        if (file_exists($old_file_path)) {
            unlink($old_file_path);
        }

        $this->db->delete('tbl_taepts', array('taepts_id' => $taepts_id));

        // บันทึก log การลบข้อมูลการเงิน =====================================
        if ($taepts_data) {
            $this->log_model->add_log(
                'ลบ',
                'มาตรฐานการส่งเสริมคุณธรรมและความโปร่งใส',
                $taepts_data->taepts_name,
                $taepts_id,
                array('deleted_date' => date('Y-m-d H:i:s'))
            );
        }
        // ==================================================================

        $this->space_model->update_server_current();
    }

    public function del_taepts_pdf($taepts_id)
    {
        // ดึงข้อมูลรายการ pdf ก่อน
        $files = $this->db->get_where('tbl_taepts_pdf', array('taepts_pdf_ref_id' => $taepts_id))->result();

        // ลบ pdf จากตาราง tbl_taepts_pdf
        $this->db->where('taepts_pdf_ref_id', $taepts_id);
        $this->db->delete('tbl_taepts_pdf');

        // ลบไฟล์ pdf ที่เกี่ยวข้อง
        foreach ($files as $files) {
            $file_path = './docs/file/' . $files->taepts_pdf_pdf;
            if (file_exists($file_path)) {
                unlink($file_path);
            }
        }
    }

    public function del_taepts_doc($taepts_id)
    {
        // ดึงข้อมูลรายการ doc ก่อน
        $files = $this->db->get_where('tbl_taepts_file', array('taepts_file_ref_id' => $taepts_id))->result();

        // ลบ doc จากตาราง tbl_taepts_file
        $this->db->where('taepts_file_ref_id', $taepts_id);
        $this->db->delete('tbl_taepts_file');

        // ลบไฟล์ doc ที่เกี่ยวข้อง
        foreach ($files as $files) {
            $file_path = './docs/file/' . $files->taepts_file_doc;
            if (file_exists($file_path)) {
                unlink($file_path);
            }
        }
    }

    public function del_taepts_img($taepts_id)
    {
        // ดึงข้อมูลรายการรูปภาพก่อน
        $files = $this->db->get_where('tbl_taepts_img', array('taepts_img_ref_id' => $taepts_id))->result();

        // ลบรูปภาพจากตาราง tbl_taepts_file
        $this->db->where('taepts_img_ref_id', $taepts_id);
        $this->db->delete('tbl_taepts_img');

        // ลบไฟล์รูปภาพที่เกี่ยวข้อง
        foreach ($files as $files) {
            $file_path = './docs/img/' . $files->taepts_img_img;
            if (file_exists($file_path)) {
                unlink($file_path);
            }
        }
    }


    public function del_type_taepts_type($taepts_type_id)
    {
        // ดึงข้อมูลหัวข้อก่อนลบ
        $type_data = $this->read_type($taepts_type_id);

        $this->db->delete('tbl_taepts_type', array('taepts_type_id' => $taepts_type_id));

        // บันทึก log การลบหัวข้อการเงิน ===================================
        if ($type_data) {
            $this->log_model->add_log(
                'ลบ',
                'มาตรฐานการส่งเสริมคุณธรรมและความโปร่งใส',
                'ลบหัวข้อ: ' . $type_data->taepts_type_name,
                $taepts_type_id,
                array('deleted_date' => date('Y-m-d H:i:s'))
            );
        }
        // ================================================================

        $this->space_model->update_server_current();
    }
    public function del_type_taepts($taepts_type_id)
    {
        // ดึงข้อมูลทั้งหมดที่ต้องการลบ
        $documents = $this->db->get_where('tbl_taepts', array('taepts_ref_id' => $taepts_type_id))->result();

        // ถ้ามีข้อมูล
        if ($documents) {
            foreach ($documents as $doc) {
                // ลบไฟล์รูปภาพ ถ้ามี
                if (!empty($doc->taepts_img)) {
                    $old_file_path = './docs/img/' . $doc->taepts_img;
                    if (file_exists($old_file_path)) {
                        unlink($old_file_path);
                    }
                }
            }
        }

        // ลบข้อมูลจากฐานข้อมูล
        $this->db->delete('tbl_taepts', array('taepts_ref_id' => $taepts_type_id));
        $this->space_model->update_server_current();
    }

    public function del_type_taepts_pdf($taepts_type_id)
    {
        $taepts_ids = $this->get_taepts_id($taepts_type_id);

        if ($taepts_ids) {
            // แปลง array เป็น string โดยคั่นด้วย ','
            $taepts_ids_string = implode(',', $taepts_ids);

            // สร้าง SQL query โดยใส่ ' รอบแต่ละค่า
            $sql = "SELECT * FROM `tbl_taepts_pdf` WHERE taepts_pdf_ref_id IN ($taepts_ids_string)";

            // ดึงข้อมูลรายการรูปภาพที่ต้องการลบ
            $files = $this->db->query($sql)->result();

            // ลบรูปภาพจากตาราง tbl_taepts_pdf
            $this->db->where_in('taepts_pdf_ref_id', $taepts_ids);
            $this->db->delete('tbl_taepts_pdf');

            // ลบไฟล์รูปภาพที่เกี่ยวข้อง
            foreach ($files as $file) {
                $file_path = './docs/file/' . $file->taepts_pdf_pdf;
                if (file_exists($file_path)) {
                    unlink($file_path);
                }
            }
        }
    }

    public function del_type_taepts_doc($taepts_type_id)
    {
        $taepts_ids = $this->get_taepts_id($taepts_type_id);

        if ($taepts_ids) {
            // แปลง array เป็น string โดยคั่นด้วย ','
            $taepts_ids_string = implode(',', $taepts_ids);

            // สร้าง SQL query โดยใส่ ' รอบแต่ละค่า
            $sql = "SELECT * FROM `tbl_taepts_file` WHERE taepts_file_ref_id IN ($taepts_ids_string)";

            // ดึงข้อมูลรายการรูปภาพที่ต้องการลบ
            $files = $this->db->query($sql)->result();

            // ลบรูปภาพจากตาราง tbl_taepts_file
            $this->db->where_in('taepts_file_ref_id', $taepts_ids);
            $this->db->delete('tbl_taepts_file');

            // ลบไฟล์รูปภาพที่เกี่ยวข้อง
            foreach ($files as $file) {
                $file_path = './docs/file/' . $file->taepts_file_doc;
                if (file_exists($file_path)) {
                    unlink($file_path);
                }
            }
        }
    }

    public function del_type_taepts_img($taepts_type_id)
    {
        $taepts_ids = $this->get_taepts_id($taepts_type_id);

        if ($taepts_ids) {
            // แปลง array เป็น string โดยคั่นด้วย ','
            $taepts_ids_string = implode(',', $taepts_ids);

            // สร้าง SQL query โดยใส่ ' รอบแต่ละค่า
            $sql = "SELECT * FROM `tbl_taepts_img` WHERE taepts_img_ref_id IN ($taepts_ids_string)";

            // ดึงข้อมูลรายการรูปภาพที่ต้องการลบ
            $files = $this->db->query($sql)->result();

            // ลบรูปภาพจากตาราง tbl_taepts_img
            $this->db->where_in('taepts_img_ref_id', $taepts_ids);
            $this->db->delete('tbl_taepts_img');

            // ลบไฟล์รูปภาพที่เกี่ยวข้อง
            foreach ($files as $file) {
                $file_path = './docs/img/' . $file->taepts_img_img;
                if (file_exists($file_path)) {
                    unlink($file_path);
                }
            }
        }
    }

    public function get_taepts_id($taepts_type_id)
    {
        $query = $this->db->select('taepts_id')
            ->from('tbl_taepts')
            ->where('taepts_ref_id', $taepts_type_id)
            ->get();

        if ($query->num_rows() > 0) {
            $result = array();
            foreach ($query->result() as $row) {
                $result[] = $row->taepts_id;
            }
            return $result; // คืน Array ของ taepts_id ทั้งหมด
        } else {
            return null; // หรือค่าที่เหมาะสมเมื่อไม่พบข้อมูล
        }
    }

    public function taepts_topic_frontend()
    {
        $this->db->select('*');
        $this->db->from('tbl_taepts_type');
        $this->db->limit(8);
        $this->db->order_by('tbl_taepts_type.taepts_type_date', 'DESC');
        $query = $this->db->get();
        return $query->result();
    }
    public function taepts_frontend_list_topic()
    {
        $this->db->select('*');
        $this->db->from('tbl_taepts_type');
        $this->db->order_by('tbl_taepts_type.taepts_type_date', 'DESC');
        $query = $this->db->get();
        return $query->result();
    }
    public function taepts_frontend_list($taepts_type_id)
    {
        $this->db->select('*');
        $this->db->from('tbl_taepts');
        $this->db->join('tbl_taepts_type', 'tbl_taepts.taepts_ref_id = tbl_taepts_type.taepts_type_id', 'inner'); // เปลี่ยนเป็น INNER JOIN
        $this->db->where('tbl_taepts_type.taepts_type_id', $taepts_type_id);
        $this->db->where('tbl_taepts.taepts_status', 'show');
        $this->db->order_by('tbl_taepts.taepts_date', 'DESC'); // เพิ่มบรรทัดนี้
        $query = $this->db->get();
        return $query->result();
    }
    public function increment_view($taepts_id)
    {
        $this->db->where('taepts_id', $taepts_id);
        $this->db->set('taepts_view', 'taepts_view + 1', false); // บวกค่า taepts_view ทีละ 1
        $this->db->update('tbl_taepts');
    }
    // ใน taepts_model
    public function increment_download_taepts($taepts_file_id)
    {
        $this->db->where('taepts_file_id', $taepts_file_id);
        $this->db->set('taepts_file_download', 'taepts_file_download + 1', false); // บวกค่า taepts_download ทีละ 1
        $this->db->update('tbl_taepts_file');
    }
}
