<?php
class Arevenuec_model extends CI_Model
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

        // กำหนดค่าใน $arevenuec_data
        $arevenuec_data = array(
            'arevenuec_name' => $this->input->post('arevenuec_name'),
            'arevenuec_detail' => $this->input->post('arevenuec_detail'),
            'arevenuec_date' => $this->input->post('arevenuec_date'),
            'arevenuec_link' => $this->input->post('arevenuec_link'),
            'arevenuec_by' => $this->session->userdata('m_fname'), // เพิ่มชื่อคนที่แก้ไขข้อมูล
        );

        // ทำการอัปโหลดรูปภาพ
        $img_main = $this->img_upload->do_upload('arevenuec_img');
        // ตรวจสอบว่ามีการอัปโหลดรูปภาพหรือไม่
        if (!empty($img_main)) {
            // ถ้ามีการอัปโหลดรูปภาพ
            $arevenuec_data['arevenuec_img'] = $this->img_upload->data('file_name');
        }
        // เพิ่มข้อมูลลงในฐานข้อมูล
        $this->db->insert('tbl_arevenuec', $arevenuec_data);
        $arevenuec_id = $this->db->insert_id();

        // หาพื้นที่ว่าง และอัพโหลดlimit จากฐานข้อมูล
        $used_space = $this->space_model->get_used_space();
        $upload_limit = $this->space_model->get_limit_storage();

        $total_space_required = 0;
        // ตรวจสอบว่ามีข้อมูลรูปภาพเพิ่มเติมหรือไม่
        if (isset($_FILES['arevenuec_img_img'])) {
            foreach ($_FILES['arevenuec_img_img']['name'] as $index => $name) {
                if (isset($_FILES['arevenuec_img_img']['size'][$index])) {
                    $total_space_required += $_FILES['arevenuec_img_img']['size'][$index];
                }
            }
        }

        // ตรวจสอบว่ามีข้อมูลไฟล์ PDF หรือไม่
        if (isset($_FILES['arevenuec_pdf_pdf'])) {
            foreach ($_FILES['arevenuec_pdf_pdf']['name'] as $index => $name) {
                if (isset($_FILES['arevenuec_pdf_pdf']['size'][$index])) {
                    $total_space_required += $_FILES['arevenuec_pdf_pdf']['size'][$index];
                }
            }
        }

        // ตรวจสอบว่ามีข้อมูลไฟล์ doc หรือไม่
        if (isset($_FILES['arevenuec_file_doc'])) {
            foreach ($_FILES['arevenuec_file_doc']['name'] as $index => $name) {
                if (isset($_FILES['arevenuec_file_doc']['size'][$index])) {
                    $total_space_required += $_FILES['arevenuec_file_doc']['size'][$index];
                }
            }
        }

        // เช็คค่าว่าง
        if ($used_space + ($total_space_required / (1024 * 1024 * 1024)) >= $upload_limit) {
            $this->session->set_flashdata('save_error', TRUE);
            redirect('arevenuec_backend/adding');
            return;
        }

        $imgs_data = array();

        // ตรวจสอบว่ามีการอัปโหลดรูปภาพเพิ่มเติมหรือไม่
        if (!empty($_FILES['arevenuec_img_img']['name'][0])) {
            foreach ($_FILES['arevenuec_img_img']['name'] as $index => $name) {
                $_FILES['arevenuec_img_img_multiple']['name'] = $name;
                $_FILES['arevenuec_img_img_multiple']['type'] = $_FILES['arevenuec_img_img']['type'][$index];
                $_FILES['arevenuec_img_img_multiple']['tmp_name'] = $_FILES['arevenuec_img_img']['tmp_name'][$index];
                $_FILES['arevenuec_img_img_multiple']['error'] = $_FILES['arevenuec_img_img']['error'][$index];
                $_FILES['arevenuec_img_img_multiple']['size'] = $_FILES['arevenuec_img_img']['size'][$index];

                if ($this->img_upload->do_upload('arevenuec_img_img_multiple')) {
                    $upload_data = $this->img_upload->data();
                    $imgs_data[] = array(
                        'arevenuec_img_ref_id' => $arevenuec_id,
                        'arevenuec_img_img' => $upload_data['file_name']
                    );
                }
            }
            $this->db->insert_batch('tbl_arevenuec_img', $imgs_data);
        }

        $pdf_data = array();

        // ตรวจสอบว่ามีการอัปโหลดไฟล์PDFเพิ่มเติมหรือไม่
        if (!empty($_FILES['arevenuec_pdf_pdf']['name'][0])) {
            foreach ($_FILES['arevenuec_pdf_pdf']['name'] as $index => $name) {
                $_FILES['arevenuec_pdf_pdf_multiple']['name'] = $name;
                $_FILES['arevenuec_pdf_pdf_multiple']['type'] = $_FILES['arevenuec_pdf_pdf']['type'][$index];
                $_FILES['arevenuec_pdf_pdf_multiple']['tmp_name'] = $_FILES['arevenuec_pdf_pdf']['tmp_name'][$index];
                $_FILES['arevenuec_pdf_pdf_multiple']['error'] = $_FILES['arevenuec_pdf_pdf']['error'][$index];
                $_FILES['arevenuec_pdf_pdf_multiple']['size'] = $_FILES['arevenuec_pdf_pdf']['size'][$index];

                if ($this->pdf_upload->do_upload('arevenuec_pdf_pdf_multiple')) {
                    $upload_data = $this->pdf_upload->data();
                    $pdf_data[] = array(
                        'arevenuec_pdf_ref_id' => $arevenuec_id,
                        'arevenuec_pdf_pdf' => $upload_data['file_name']
                    );
                }
            }
            $this->db->insert_batch('tbl_arevenuec_pdf', $pdf_data);
        }

        $doc_data = array();

        // ตรวจสอบว่ามีการอัปโหลดไฟล์Docเพิ่มเติมหรือไม่
        if (!empty($_FILES['arevenuec_file_doc']['name'][0])) {
            foreach ($_FILES['arevenuec_file_doc']['name'] as $index => $name) {
                $_FILES['arevenuec_file_doc_multiple']['name'] = $name;
                $_FILES['arevenuec_file_doc_multiple']['type'] = $_FILES['arevenuec_file_doc']['type'][$index];
                $_FILES['arevenuec_file_doc_multiple']['tmp_name'] = $_FILES['arevenuec_file_doc']['tmp_name'][$index];
                $_FILES['arevenuec_file_doc_multiple']['error'] = $_FILES['arevenuec_file_doc']['error'][$index];
                $_FILES['arevenuec_file_doc_multiple']['size'] = $_FILES['arevenuec_file_doc']['size'][$index];

                if ($this->doc_upload->do_upload('arevenuec_file_doc_multiple')) {
                    $upload_data = $this->doc_upload->data();
                    $doc_data[] = array(
                        'arevenuec_file_ref_id' => $arevenuec_id,
                        'arevenuec_file_doc' => $upload_data['file_name']
                    );
                }
            }
            $this->db->insert_batch('tbl_arevenuec_file', $doc_data);
        }
        $this->space_model->update_server_current();
		// บันทึก log การเพิ่มข้อมูล =================================================
        $this->log_model->add_log(
            'เพิ่ม',
            'การเร่งรัดจัดเก็บรายได้',
            $arevenuec_data['arevenuec_name'],
            $arevenuec_id,
            array(
                'files_uploaded' => array(
                    'images' => count($imgs_data),
                    'pdfs' => count($pdf_data),
                    'docs' => count($doc_data)
                )
            )
        );
        // =======================================================================
        $this->session->set_flashdata('save_success', TRUE);
    }

    public function list_all()
    {
        $this->db->select('*');
        $this->db->from('tbl_arevenuec');
        $this->db->group_by('tbl_arevenuec.arevenuec_id');
        $this->db->order_by('tbl_arevenuec.arevenuec_date', 'DESC');
        $query = $this->db->get();
        return $query->result();
    }

    public function list_all_pdf($arevenuec_id)
    {
        $this->db->select('arevenuec_pdf_pdf');
        $this->db->from('tbl_arevenuec_pdf');
        $this->db->where('arevenuec_pdf_ref_id', $arevenuec_id);
        return $this->db->get()->result();
    }
    public function list_all_doc($arevenuec_id)
    {
        $this->db->select('arevenuec_file_doc');
        $this->db->from('tbl_arevenuec_file');
        $this->db->where('arevenuec_file_ref_id', $arevenuec_id);
        return $this->db->get()->result();
    }

    //show form edit
    public function read($arevenuec_id)
    {
        $this->db->where('arevenuec_id', $arevenuec_id);
        $query = $this->db->get('tbl_arevenuec');
        if ($query->num_rows() > 0) {
            $data = $query->row();
            return $data;
        }
        return FALSE;
    }

    public function read_pdf($arevenuec_id)
    {
        $this->db->where('arevenuec_pdf_ref_id', $arevenuec_id);
        $this->db->order_by('arevenuec_pdf_id', 'DESC');
        $query = $this->db->get('tbl_arevenuec_pdf');
        return $query->result();
    }
    public function read_doc($arevenuec_id)
    {
        $this->db->where('arevenuec_file_ref_id', $arevenuec_id);
        $this->db->order_by('arevenuec_file_id', 'DESC');
        $query = $this->db->get('tbl_arevenuec_file');
        return $query->result();
    }

    public function read_img($arevenuec_id)
    {
        $this->db->where('arevenuec_img_ref_id', $arevenuec_id);
        $this->db->order_by('arevenuec_img_id', 'DESC');
        $query = $this->db->get('tbl_arevenuec_img');
        return $query->result();
    }

// แก้ไข del_pdf function
public function del_pdf($pdf_id)
{
    // ดึงข้อมูลไฟล์และ ref_id สำหรับ logging
    $this->db->select('arevenuec_pdf_pdf, arevenuec_pdf_ref_id');
    $this->db->where('arevenuec_pdf_id', $pdf_id);
    $query = $this->db->get('tbl_arevenuec_pdf');
    $file_data = $query->row();

    // ดึงข้อมูลเอกสารหลักสำหรับ log (สมมติว่ามี function read หรือ get_by_id)
    $arevenuec_data = null;
    if ($file_data && $file_data->arevenuec_pdf_ref_id) {
        $arevenuec_data = $this->db->get_where('tbl_arevenuec', array('arevenuec_id' => $file_data->arevenuec_pdf_ref_id))->row();
    }

    // ลบไฟล์จากแหล่งที่เก็บไฟล์
    $file_path = './docs/file/' . $file_data->arevenuec_pdf_pdf;
    if (file_exists($file_path)) {
        unlink($file_path);
    }

    // ลบข้อมูลของไฟล์จากฐานข้อมูล
    $this->db->where('arevenuec_pdf_id', $pdf_id);
    $this->db->delete('tbl_arevenuec_pdf');

    // บันทึก log การลบไฟล์ PDF ============================================
    $this->log_model->add_log(
        'ลบ',
        'การเร่งรัดจัดเก็บรายได้', // ปรับชื่อเมนูตามที่ใช้จริง
        'ไฟล์ PDF: ' . $file_data->arevenuec_pdf_pdf . ' จากเอกสาร: ' . ($arevenuec_data ? $arevenuec_data->arevenuec_name : 'ไม่ระบุ'),
        $file_data->arevenuec_pdf_ref_id,
        array(
            'file_type' => 'PDF', 
            'file_name' => $file_data->arevenuec_pdf_pdf,
            'pdf_id' => $pdf_id
        )
    );
    // ==================================================================

    $this->space_model->update_server_current();
    $this->session->set_flashdata('del_success', TRUE);
}

// แก้ไข del_doc function
public function del_doc($doc_id)
{
    // ดึงข้อมูลไฟล์และ ref_id สำหรับ logging
    $this->db->select('arevenuec_file_doc, arevenuec_file_ref_id');
    $this->db->where('arevenuec_file_id', $doc_id);
    $query = $this->db->get('tbl_arevenuec_file');
    $file_data = $query->row();

    // ดึงข้อมูลเอกสารหลักสำหรับ log
    $arevenuec_data = null;
    if ($file_data && $file_data->arevenuec_file_ref_id) {
        $arevenuec_data = $this->db->get_where('tbl_arevenuec', array('arevenuec_id' => $file_data->arevenuec_file_ref_id))->row();
    }

    // ลบไฟล์จากแหล่งที่เก็บไฟล์
    $file_path = './docs/file/' . $file_data->arevenuec_file_doc;
    if (file_exists($file_path)) {
        unlink($file_path);
    }

    // ลบข้อมูลของไฟล์จากฐานข้อมูล
    $this->db->where('arevenuec_file_id', $doc_id);
    $this->db->delete('tbl_arevenuec_file');

    // บันทึก log การลบไฟล์ DOC ==========================================
    $this->log_model->add_log(
        'ลบ',
        'การเร่งรัดจัดเก็บรายได้', // ปรับชื่อเมนูตามที่ใช้จริง
        'ไฟล์ DOC: ' . $file_data->arevenuec_file_doc . ' จากเอกสาร: ' . ($arevenuec_data ? $arevenuec_data->arevenuec_name : 'ไม่ระบุ'),
        $file_data->arevenuec_file_ref_id,
        array(
            'file_type' => 'DOC', 
            'file_name' => $file_data->arevenuec_file_doc,
            'doc_id' => $doc_id
        )
    );
    // ==================================================================

    $this->space_model->update_server_current();
    $this->session->set_flashdata('del_success', TRUE);
}

// แก้ไข del_img function
public function del_img($file_id)
{
    // ดึงข้อมูลไฟล์และ ref_id สำหรับ logging
    $this->db->select('arevenuec_img_img, arevenuec_img_ref_id');
    $this->db->where('arevenuec_img_id', $file_id);
    $query = $this->db->get('tbl_arevenuec_img');
    $file_data = $query->row();

    // ดึงข้อมูลเอกสารหลักสำหรับ log
    $arevenuec_data = null;
    if ($file_data && $file_data->arevenuec_img_ref_id) {
        $arevenuec_data = $this->db->get_where('tbl_arevenuec', array('arevenuec_id' => $file_data->arevenuec_img_ref_id))->row();
    }

    // ลบไฟล์จากแหล่งที่เก็บไฟล์
    $file_path = './docs/img/' . $file_data->arevenuec_img_img;
    if (file_exists($file_path)) {
        unlink($file_path);
    }

    // บันทึก log การลบรูปภาพ ===============================================
    $this->log_model->add_log(
        'ลบ',
        'การเร่งรัดจัดเก็บรายได้', // ปรับชื่อเมนูตามที่ใช้จริง
        'รูปภาพ: ' . $file_data->arevenuec_img_img . ' จากเอกสาร: ' . ($arevenuec_data ? $arevenuec_data->arevenuec_name : 'ไม่ระบุ'),
        $file_data->arevenuec_img_ref_id,
        array(
            'file_type' => 'IMAGE', 
            'file_name' => $file_data->arevenuec_img_img,
            'img_id' => $file_id
        )
    );
    // ==================================================================

    // ลบข้อมูลของไฟล์จากฐานข้อมูล
    $this->db->where('arevenuec_img_id', $file_id);
    $this->db->delete('tbl_arevenuec_img');
    $this->space_model->update_server_current();
    $this->session->set_flashdata('del_success', TRUE);
}


    public function edit($arevenuec_id)
    {
		// ดึงข้อมูลเก่าก่อนแก้ไข
        $old_data = $this->read($arevenuec_id);
		
        // Update arevenuec information
        $data = array(
            'arevenuec_name' => $this->input->post('arevenuec_name'),
            'arevenuec_detail' => $this->input->post('arevenuec_detail'),
            'arevenuec_date' => $this->input->post('arevenuec_date'),
            'arevenuec_link' => $this->input->post('arevenuec_link'),
            'arevenuec_by' => $this->session->userdata('m_fname'), // เพิ่มชื่อคนที่แก้ไขข้อมูล
        );

        $this->db->where('arevenuec_id', $arevenuec_id);
        $this->db->update('tbl_arevenuec', $data);

        // หาพื้นที่ว่าง และอัพโหลดlimit จากฐานข้อมูล
        $used_space = $this->space_model->get_used_space();
        $upload_limit = $this->space_model->get_limit_storage();

        $total_space_required = 0;
        // ตรวจสอบว่ามีข้อมูลรูปภาพเพิ่มเติมหรือไม่
        if (isset($_FILES['arevenuec_img_img'])) {
            foreach ($_FILES['arevenuec_img_img']['name'] as $index => $name) {
                if (isset($_FILES['arevenuec_img_img']['size'][$index])) {
                    $total_space_required += $_FILES['arevenuec_img_img']['size'][$index];
                }
            }
        }

        // ตรวจสอบว่ามีข้อมูลไฟล์ PDF หรือไม่
        if (isset($_FILES['arevenuec_pdf_pdf'])) {
            foreach ($_FILES['arevenuec_pdf_pdf']['name'] as $index => $name) {
                if (isset($_FILES['arevenuec_pdf_pdf']['size'][$index])) {
                    $total_space_required += $_FILES['arevenuec_pdf_pdf']['size'][$index];
                }
            }
        }

        // ตรวจสอบว่ามีข้อมูลไฟล์ doc หรือไม่
        if (isset($_FILES['arevenuec_file_doc'])) {
            foreach ($_FILES['arevenuec_file_doc']['name'] as $index => $name) {
                if (isset($_FILES['arevenuec_file_doc']['size'][$index])) {
                    $total_space_required += $_FILES['arevenuec_file_doc']['size'][$index];
                }
            }
        }

        // เช็คค่าว่าง
        if ($used_space + ($total_space_required / (1024 * 1024 * 1024)) >= $upload_limit) {
            $this->session->set_flashdata('save_error', TRUE);
            redirect('arevenuec_backend/adding');
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
        $img_main = $this->img_upload->do_upload('arevenuec_img');

        // ตรวจสอบว่ามีการอัปโหลดรูปภาพหรือไม่
        if (!empty($img_main)) {
            $this->db->trans_start(); // เริ่ม Transaction

            // ดึงข้อมูลรูปเก่า
            $old_document = $this->db->get_where('tbl_arevenuec', array('arevenuec_id' => $arevenuec_id))->row();

            // ตรวจสอบว่ามีไฟล์เก่าหรือไม่
            if ($old_document && $old_document->arevenuec_img) {
                $old_file_path = './docs/img/' . $old_document->arevenuec_img;

                if (file_exists($old_file_path)) {
                    unlink($old_file_path); // ลบไฟล์เก่า
                }
            }

            // ถ้ามีการอัปโหลดรูปภาพใหม่
            $img_data['arevenuec_img'] = $this->img_upload->data('file_name');
            $this->db->where('arevenuec_id', $arevenuec_id);
            $this->db->update('tbl_arevenuec', $img_data);

            $this->db->trans_complete(); // สิ้นสุด Transaction
        }

        $imgs_data = array();

        // ตรวจสอบว่ามีการอัปโหลดรูปภาพเพิ่มเติมหรือไม่
        if (!empty($_FILES['arevenuec_img_img']['name'][0])) {

            foreach ($_FILES['arevenuec_img_img']['name'] as $index => $name) {
                $_FILES['arevenuec_img_img_multiple']['name'] = $name;
                $_FILES['arevenuec_img_img_multiple']['type'] = $_FILES['arevenuec_img_img']['type'][$index];
                $_FILES['arevenuec_img_img_multiple']['tmp_name'] = $_FILES['arevenuec_img_img']['tmp_name'][$index];
                $_FILES['arevenuec_img_img_multiple']['error'] = $_FILES['arevenuec_img_img']['error'][$index];
                $_FILES['arevenuec_img_img_multiple']['size'] = $_FILES['arevenuec_img_img']['size'][$index];

                if ($this->img_upload->do_upload('arevenuec_img_img_multiple')) {
                    $upload_data = $this->img_upload->data();
                    $imgs_data[] = array(
                        'arevenuec_img_ref_id' => $arevenuec_id,
                        'arevenuec_img_img' => $upload_data['file_name']
                    );
                }
            }
            $this->db->insert_batch('tbl_arevenuec_img', $imgs_data);
        }

        $pdf_data = array();

        // ตรวจสอบว่ามีการอัปโหลด pdf เพิ่มเติมหรือไม่
        if (!empty($_FILES['arevenuec_pdf_pdf']['name'][0])) {
            foreach ($_FILES['arevenuec_pdf_pdf']['name'] as $index => $name) {
                $_FILES['arevenuec_pdf_pdf_multiple']['name'] = $name;
                $_FILES['arevenuec_pdf_pdf_multiple']['type'] = $_FILES['arevenuec_pdf_pdf']['type'][$index];
                $_FILES['arevenuec_pdf_pdf_multiple']['tmp_name'] = $_FILES['arevenuec_pdf_pdf']['tmp_name'][$index];
                $_FILES['arevenuec_pdf_pdf_multiple']['error'] = $_FILES['arevenuec_pdf_pdf']['error'][$index];
                $_FILES['arevenuec_pdf_pdf_multiple']['size'] = $_FILES['arevenuec_pdf_pdf']['size'][$index];

                if ($this->pdf_upload->do_upload('arevenuec_pdf_pdf_multiple')) {
                    $upload_data = $this->pdf_upload->data();
                    $pdf_data[] = array(
                        'arevenuec_pdf_ref_id' => $arevenuec_id,
                        'arevenuec_pdf_pdf' => $upload_data['file_name']
                    );
                }
            }
            $this->db->insert_batch('tbl_arevenuec_pdf', $pdf_data);
        }

        $doc_data = array();

        // ตรวจสอบว่ามีการอัปโหลด doc เพิ่มเติมหรือไม่
        if (!empty($_FILES['arevenuec_file_doc']['name'][0])) {
            foreach ($_FILES['arevenuec_file_doc']['name'] as $index => $name) {
                $_FILES['arevenuec_file_doc_multiple']['name'] = $name;
                $_FILES['arevenuec_file_doc_multiple']['type'] = $_FILES['arevenuec_file_doc']['type'][$index];
                $_FILES['arevenuec_file_doc_multiple']['tmp_name'] = $_FILES['arevenuec_file_doc']['tmp_name'][$index];
                $_FILES['arevenuec_file_doc_multiple']['error'] = $_FILES['arevenuec_file_doc']['error'][$index];
                $_FILES['arevenuec_file_doc_multiple']['size'] = $_FILES['arevenuec_file_doc']['size'][$index];

                if ($this->doc_upload->do_upload('arevenuec_file_doc_multiple')) {
                    $upload_data = $this->doc_upload->data();
                    $doc_data[] = array(
                        'arevenuec_file_ref_id' => $arevenuec_id,
                        'arevenuec_file_doc' => $upload_data['file_name']
                    );
                }
            }
            $this->db->insert_batch('tbl_arevenuec_file', $doc_data);
        }
        $this->space_model->update_server_current();
				// === เก็บ log แก้ไขข้อมูล ==============================================
        $changes = array();
        if ($old_data) {
            if ($old_data->arevenuec_name != $data['arevenuec_name']) {
                $changes['arevenuec_name'] = array(
                    'old' => $old_data->arevenuec_name,
                    'new' => $data['arevenuec_name']
                );
            }
            if ($old_data->arevenuec_detail != $data['arevenuec_detail']) {
                $changes['arevenuec_detail'] = array(
                    'old' => 'มีการเปลี่ยนแปลง',
                    'new' => 'รายละเอียดใหม่'
                );
            }
            if ($old_data->arevenuec_date != $data['arevenuec_date']) {
                $changes['arevenuec_date'] = array(
                    'old' => $old_data->arevenuec_date,
                    'new' => $data['arevenuec_date']
                );
            }
            if ($old_data->arevenuec_link != $data['arevenuec_link']) {
                $changes['arevenuec_link'] = array(
                    'old' => $old_data->arevenuec_link,
                    'new' => $data['arevenuec_link']
                );
            }
        }

        // เพิ่มข้อมูลไฟล์ที่อัพโหลด
        $files_info = array();
        if (!empty($imgs_data)) {
            $files_info['images_added'] = count($imgs_data);
            $files_info['image_names'] = array_column($imgs_data, 'arevenuec_img_img');
        }
        if (!empty($pdf_data)) {
            $files_info['pdfs_added'] = count($pdf_data);
            $files_info['pdf_names'] = array_column($pdf_data, 'arevenuec_pdf_pdf');
        }
        if (!empty($doc_data)) {
            $files_info['docs_added'] = count($doc_data);
            $files_info['doc_names'] = array_column($doc_data, 'arevenuec_file_doc');
        }

        // บันทึก log การแก้ไข
        $this->log_model->add_log(
            'แก้ไข',
            'การเร่งรัดจัดเก็บรายได้',
            $data['arevenuec_name'],
            $arevenuec_id,
            array(
                'changes' => $changes,
                'files_uploaded' => $files_info,
                'total_files_added' => count($imgs_data) + count($pdf_data) + count($doc_data)
            )
        );
        // =======================================================================
        $this->session->set_flashdata('save_success', TRUE);
    }

    public function del_arevenuec($arevenuec_id)
{
    // ดึงข้อมูลก่อนลบสำหรับ logging
    $arevenuec_data = $this->db->get_where('tbl_arevenuec', array('arevenuec_id' => $arevenuec_id))->row();

    // ลบรูปภาพหลัก
    if ($arevenuec_data && $arevenuec_data->arevenuec_img) {
        $old_file_path = './docs/img/' . $arevenuec_data->arevenuec_img;
        if (file_exists($old_file_path)) {
            unlink($old_file_path);
        }
    }

    // ลบข้อมูลจากฐานข้อมูล
    $this->db->delete('tbl_arevenuec', array('arevenuec_id' => $arevenuec_id));

    // บันทึก log การลบเอกสารรายได้แผนดิน ===============================
    if ($arevenuec_data) {
        $this->log_model->add_log(
            'ลบ',
            'การเร่งรัดจัดเก็บรายได้', // ปรับชื่อเมนูตามที่ใช้จริง
            $arevenuec_data->arevenuec_name ?? 'เอกสารรายได้แผนดิน',
            $arevenuec_id,
            array(
                'deleted_date' => date('Y-m-d H:i:s'),
                'main_image' => $arevenuec_data->arevenuec_img ?? null,
                'document_type' => 'arevenuec_main'
            )
        );
    }
    // ====================================================================

    $this->space_model->update_server_current();
}

    public function del_arevenuec_pdf($arevenuec_id)
    {
        // ดึงข้อมูลรายการ pdf ก่อน
        $files = $this->db->get_where('tbl_arevenuec_pdf', array('arevenuec_pdf_ref_id' => $arevenuec_id))->result();

        // ลบ pdf จากตาราง tbl_arevenuec_pdf
        $this->db->where('arevenuec_pdf_ref_id', $arevenuec_id);
        $this->db->delete('tbl_arevenuec_pdf');

        // ลบไฟล์ pdf ที่เกี่ยวข้อง
        foreach ($files as $files) {
            $file_path = './docs/file/' . $files->arevenuec_pdf_pdf;
            if (file_exists($file_path)) {
                unlink($file_path);
            }
        }
    }

    public function del_arevenuec_doc($arevenuec_id)
    {
        // ดึงข้อมูลรายการ doc ก่อน
        $files = $this->db->get_where('tbl_arevenuec_file', array('arevenuec_file_ref_id' => $arevenuec_id))->result();

        // ลบ doc จากตาราง tbl_arevenuec_file
        $this->db->where('arevenuec_file_ref_id', $arevenuec_id);
        $this->db->delete('tbl_arevenuec_file');

        // ลบไฟล์ doc ที่เกี่ยวข้อง
        foreach ($files as $files) {
            $file_path = './docs/file/' . $files->arevenuec_file_doc;
            if (file_exists($file_path)) {
                unlink($file_path);
            }
        }
    }

    public function del_arevenuec_img($arevenuec_id)
    {
        // ดึงข้อมูลรายการรูปภาพก่อน
        $files = $this->db->get_where('tbl_arevenuec_img', array('arevenuec_img_ref_id' => $arevenuec_id))->result();

        // ลบรูปภาพจากตาราง tbl_arevenuec_file
        $this->db->where('arevenuec_img_ref_id', $arevenuec_id);
        $this->db->delete('tbl_arevenuec_img');

        // ลบไฟล์รูปภาพที่เกี่ยวข้อง
        foreach ($files as $files) {
            $file_path = './docs/img/' . $files->arevenuec_img_img;
            if (file_exists($file_path)) {
                unlink($file_path);
            }
        }
    }

    public function update_arevenuec_status()
    {
        // ตรวจสอบว่ามีการส่งข้อมูล POST มาหรือไม่
        if ($this->input->post()) {
            $arevenuecId = $this->input->post('arevenuec_id'); // รับค่า arevenuec_id
            $newStatus = $this->input->post('new_status'); // รับค่าใหม่จาก switch checkbox

            // ทำการอัพเดตค่าในตาราง tbl_arevenuec ในฐานข้อมูลของคุณ
            $data = array(
                'arevenuec_status' => $newStatus
            );
            $this->db->where('arevenuec_id', $arevenuecId); // ระบุ arevenuec_id ของแถวที่ต้องการอัพเดต
            $this->db->update('tbl_arevenuec', $data);

            // ส่งการตอบกลับ (response) กลับไปยังเว็บไซต์หรือแอพพลิเคชันของคุณ
            // โดยเช่นปกติคุณอาจส่ง JSON response กลับมาเพื่ออัพเดตหน้าเว็บ
            $response = array('status' => 'success', 'message' => 'อัพเดตสถานะเรียบร้อย');
            echo json_encode($response);
        } else {
            // ถ้าไม่มีข้อมูล POST ส่งมา ให้รีเดอร์เปรียบเสมอ
            show_404();
        }
    }

    public function arevenuec_frontend()
    {
        $this->db->select('*');
        $this->db->from('tbl_arevenuec');
        $this->db->where('tbl_arevenuec.arevenuec_status', 'show');
        $this->db->limit(8);
        $this->db->order_by('tbl_arevenuec.arevenuec_date', 'DESC');
        $query = $this->db->get();
        return $query->result();
    }
    public function arevenuec_frontend_list()
    {
        $this->db->select('*');
        $this->db->from('tbl_arevenuec');
        $this->db->where('tbl_arevenuec.arevenuec_status', 'show');
        $this->db->order_by('tbl_arevenuec.arevenuec_date', 'DESC');
        $query = $this->db->get();
        return $query->result();
    }
    public function increment_view($arevenuec_id)
    {
        $this->db->where('arevenuec_id', $arevenuec_id);
        $this->db->set('arevenuec_view', 'arevenuec_view + 1', false); // บวกค่า arevenuec_view ทีละ 1
        $this->db->update('tbl_arevenuec');
    }
    // ใน arevenuec_model
    public function increment_download_arevenuec($arevenuec_file_id)
    {
        $this->db->where('arevenuec_file_id', $arevenuec_file_id);
        $this->db->set('arevenuec_file_download', 'arevenuec_file_download + 1', false); // บวกค่า arevenuec_download ทีละ 1
        $this->db->update('tbl_arevenuec_file');
    }
}
