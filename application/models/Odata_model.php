<?php
class Odata_model extends CI_Model
{
    public function __construct()
    {
        parent::__construct();
        $this->load->model('space_model');
        $this->load->model('odata_model');
		// log เก็บข้อมูล
        $this->load->model('log_model');
    }

public function add_odata()
{
    // ดึงค่าจากฟอร์ม
    $odata_name = $this->input->post('odata_name');

    // ตรวจสอบว่ามีข้อมูลซ้ำหรือไม่
    $duplicate_check = $this->db->get_where('tbl_odata', array('odata_name' => $odata_name));

    if ($duplicate_check->num_rows() > 0) {
        // ถ้ามีข้อมูลซ้ำ
        $this->session->set_flashdata('save_again', TRUE);
    } else {
        // ถ้าไม่มีข้อมูลซ้ำ, ทำการเพิ่มข้อมูล
        $data = array(
            'odata_name' => $odata_name,
            'odata_by' => $this->session->userdata('m_fname'),
        );

        $query = $this->db->insert('tbl_odata', $data);
        $odata_id = $this->db->insert_id();

        $this->space_model->update_server_current();

        if ($query) {
            // บันทึก log การเพิ่มข้อมูล Odata ===============================
            $this->log_model->add_log(
                'เพิ่ม',
                'ฐานข้อมูลเปิดภาครัฐ (OPEN DATA)', // ปรับชื่อเมนูตามที่ใช้จริง
                'หมวดหมู่: ' . $odata_name,
                $odata_id,
                array(
                    'odata_name' => $odata_name,
                    'created_by' => $this->session->userdata('m_fname')
                )
            );
            // ==============================================================

            $this->session->set_flashdata('save_success', TRUE);
        } else {
            echo "<script>";
            echo "alert('Error !');";
            echo "</script>";
        }
    }
}

    public function list_odata()
    {
        $this->db->order_by('odata_id', 'DESC');
        $query = $this->db->get('tbl_odata');
        return $query->result();
    }

    //show form edit
    public function read($odata_id)
    {
        $this->db->where('odata_id', $odata_id);
        $query = $this->db->get('tbl_odata');
        if ($query->num_rows() > 0) {
            $data = $query->row();
            return $data;
        }
        return FALSE;
    }

    public function edit_odata($odata_id)
{
    // ดึงข้อมูลเก่าก่อนแก้ไข
    $old_data = $this->read($odata_id);

    $data = array(
        'odata_name' => $this->input->post('odata_name'),
        'odata_by' => $this->session->userdata('m_fname'),
    );

    $this->db->where('odata_id', $odata_id);
    $query = $this->db->update('tbl_odata', $data);

    $this->space_model->update_server_current();

    if ($query) {
        // บันทึก log การแก้ไขข้อมูล Odata =============================
        $changes = array();
        if ($old_data) {
            if ($old_data->odata_name != $data['odata_name']) {
                $changes['odata_name'] = array(
                    'old' => $old_data->odata_name,
                    'new' => $data['odata_name']
                );
            }
        }

        $this->log_model->add_log(
            'แก้ไข',
            'ฐานข้อมูลเปิดภาครัฐ (OPEN DATA)',
            'หมวดหมู่: ' . $data['odata_name'],
            $odata_id,
            array(
                'changes' => $changes,
                'total_changes' => count($changes)
            )
        );
        // ==============================================================

        $this->session->set_flashdata('save_success', TRUE);
    } else {
        echo "<script>";
        echo "alert('เกิดข้อผิดพลาดในการอัปเดตข้อมูล !');";
        echo "</script>";
    }
}

    public function list_all_odata_sub($odata_id)
    {
        $this->db->select('*');
        $this->db->from('tbl_odata_sub');
        $this->db->join('tbl_odata', 'tbl_odata_sub.odata_sub_ref_id = tbl_odata.odata_id');
        $this->db->where('tbl_odata_sub.odata_sub_ref_id', $odata_id);
        $query = $this->db->get();
        $result = $query->result();
        return $result;
    }

    public function add_odata_sub($odata_sub_ref_id, $odata_sub_name)
{
    $data = array(
        'odata_sub_ref_id' => $odata_sub_ref_id,
        'odata_sub_name' => $odata_sub_name,
    );

    $this->db->insert('tbl_odata_sub', $data);
    $odata_sub_id = $this->db->insert_id();

    // ดึงข้อมูลหมวดหมู่หลักสำหรับ log
    $odata_main = $this->read($odata_sub_ref_id);

    // บันทึก log การเพิ่มหมวดหมู่ย่อย ================================
    $this->log_model->add_log(
        'เพิ่ม',
        'ฐานข้อมูลเปิดภาครัฐ (OPEN DATA)',
        'หมวดหมู่ย่อย: ' . $odata_sub_name . ' (หมวดหมู่หลัก: ' . ($odata_main ? $odata_main->odata_name : 'ไม่ระบุ') . ')',
        $odata_sub_id,
        array(
            'sub_name' => $odata_sub_name,
            'main_ref_id' => $odata_sub_ref_id,
            'main_name' => $odata_main ? $odata_main->odata_name : null
        )
    );
    // ==============================================================

    $this->space_model->update_server_current();
    $this->session->set_flashdata('save_success', TRUE);
}
	
    public function read_odata_sub($odata_sub_id)
    {
        $this->db->where('odata_sub_id', $odata_sub_id);
        $query = $this->db->get('tbl_odata_sub');
        if ($query->num_rows() > 0) {
            $data = $query->row();
            return $data;
        }
        return FALSE;
    }
	
    public function edit_odata_sub($odata_sub_ref_id, $odata_sub_name, $odata_sub_id)
{
    // ดึงข้อมูลเก่าก่อนแก้ไข
    $old_data = $this->read_odata_sub($odata_sub_id);

    $data = array(
        'odata_sub_name' => $odata_sub_name,
        'odata_sub_ref_id' => $odata_sub_ref_id,
    );

    $this->db->where('odata_sub_id', $odata_sub_id);
    $query = $this->db->update('tbl_odata_sub', $data);

    $this->space_model->update_server_current();

    if ($query) {
        // ดึงข้อมูลหมวดหมู่หลักสำหรับ log
        $odata_main = $this->read($odata_sub_ref_id);

        // บันทึก log การแก้ไขหมวดหมู่ย่อย =============================
        $changes = array();
        if ($old_data) {
            if ($old_data->odata_sub_name != $odata_sub_name) {
                $changes['odata_sub_name'] = array(
                    'old' => $old_data->odata_sub_name,
                    'new' => $odata_sub_name
                );
            }
            if ($old_data->odata_sub_ref_id != $odata_sub_ref_id) {
                $changes['odata_sub_ref_id'] = array(
                    'old' => $old_data->odata_sub_ref_id,
                    'new' => $odata_sub_ref_id
                );
            }
        }

        $this->log_model->add_log(
            'แก้ไข',
            'ฐานข้อมูลเปิดภาครัฐ (OPEN DATA)',
            'หมวดหมู่ย่อย: ' . $odata_sub_name . ' (หมวดหมู่หลัก: ' . ($odata_main ? $odata_main->odata_name : 'ไม่ระบุ') . ')',
            $odata_sub_id,
            array(
                'changes' => $changes,
                'main_ref_id' => $odata_sub_ref_id,
                'main_name' => $odata_main ? $odata_main->odata_name : null,
                'total_changes' => count($changes)
            )
        );
        // ==============================================================

        $this->session->set_flashdata('save_success', TRUE);
    } else {
        echo "<script>";
        echo "alert('เกิดข้อผิดพลาดในการอัปเดตข้อมูล !');";
        echo "</script>";
    }
}

    public function read_odata_sub_file($odata_sub_file_id)
    {
        $this->db->where('odata_sub_file_id', $odata_sub_file_id);
        $query = $this->db->get('tbl_odata_sub_file');
        if ($query->num_rows() > 0) {
            $data = $query->row();
            return $data;
        }
        return FALSE;
    }

    public function list_all_odata_sub_file($odata_sub_id)
    {
        $this->db->select('*');
        $this->db->from('tbl_odata_sub_file');
        $this->db->join('tbl_odata_sub', 'tbl_odata_sub_file.odata_sub_file_ref_id = tbl_odata_sub.odata_sub_id');
        $this->db->where('tbl_odata_sub_file.odata_sub_file_ref_id', $odata_sub_id);
        $query = $this->db->get();
        $result = $query->result();
        return $result;
    }
    
	public function add_odata_sub_file($odata_sub_file_ref_id, $odata_sub_file_name, $uploaded_filename)
{
    // เตรียมข้อมูลที่จะบันทึกลงฐานข้อมูล
    $data = array(
        'odata_sub_file_ref_id' => $odata_sub_file_ref_id,
        'odata_sub_file_name' => $odata_sub_file_name,
        'odata_sub_file_doc' => $uploaded_filename
    );

    // บันทึกข้อมูลลงฐานข้อมูล
    $this->db->insert('tbl_odata_sub_file', $data);
    $odata_sub_file_id = $this->db->insert_id();

    // ดึงข้อมูลหมวดหมู่ย่อยและหลักสำหรับ log
    $odata_sub = $this->read_odata_sub($odata_sub_file_ref_id);
    $odata_main = null;
    if ($odata_sub) {
        $odata_main = $this->read($odata_sub->odata_sub_ref_id);
    }

    // บันทึก log การเพิ่มไฟล์ =======================================
    $this->log_model->add_log(
        'เพิ่ม',
        'ฐานข้อมูลเปิดภาครัฐ (OPEN DATA)',
        'ไฟล์: ' . $odata_sub_file_name . ' (หมวดหมู่ย่อย: ' . ($odata_sub ? $odata_sub->odata_sub_name : 'ไม่ระบุ') . ')',
        $odata_sub_file_id,
        array(
            'file_name' => $odata_sub_file_name,
            'file_doc' => $uploaded_filename,
            'sub_ref_id' => $odata_sub_file_ref_id,
            'sub_name' => $odata_sub ? $odata_sub->odata_sub_name : null,
            'main_name' => $odata_main ? $odata_main->odata_name : null
        )
    );
    // ============================================================

    // อัปเดตพื้นที่ที่ใช้งานปัจจุบันบนเซิร์ฟเวอร์
    $this->space_model->update_server_current();
}

    public function edit_odata_sub_file($odata_sub_file_id, $odata_sub_file_ref_id, $odata_sub_file_name, $odata_sub_file_doc = null)
{
    // ดึงข้อมูลเก่าก่อนแก้ไข
    $old_data = $this->read_odata_sub_file($odata_sub_file_id);

    // รับข้อมูลไฟล์เดิมจากฐานข้อมูล
    $this->db->select('odata_sub_file_doc');
    $this->db->from('tbl_odata_sub_file');
    $this->db->where('odata_sub_file_id', $odata_sub_file_id);
    $old_file = $this->db->get()->row()->odata_sub_file_doc;

    // ถ้ามีไฟล์ใหม่ที่แตกต่างจากไฟล์เดิม ให้ลบไฟล์เก่าออกจากโฟลเดอร์
    if ($old_file && $odata_sub_file_doc && $old_file != $odata_sub_file_doc) {
        $this->delete_file_from_server($old_file);
    }

    // เตรียมข้อมูลสำหรับอัปเดตในฐานข้อมูล
    $data = array(
        'odata_sub_file_ref_id' => $odata_sub_file_ref_id,
        'odata_sub_file_name' => $odata_sub_file_name,
    );

    // ถ้ามีการอัพโหลดไฟล์ใหม่ ให้เพิ่มข้อมูลไฟล์ใหม่ในฐานข้อมูล
    if ($odata_sub_file_doc) {
        $data['odata_sub_file_doc'] = $odata_sub_file_doc;
    }

    // อัปเดตข้อมูลในฐานข้อมูล
    $this->db->where('odata_sub_file_id', $odata_sub_file_id);
    $query = $this->db->update('tbl_odata_sub_file', $data);

    // อัปเดตพื้นที่ที่ใช้งานปัจจุบันบนเซิร์ฟเวอร์
    $this->space_model->update_server_current();

    // ตรวจสอบสถานะการอัปเดตและตั้งค่า session flashdata
    if ($query) {
        // ดึงข้อมูลหมวดหมู่ย่อยและหลักสำหรับ log
        $odata_sub = $this->read_odata_sub($odata_sub_file_ref_id);
        $odata_main = null;
        if ($odata_sub) {
            $odata_main = $this->read($odata_sub->odata_sub_ref_id);
        }

        // บันทึก log การแก้ไขไฟล์ ===================================
        $changes = array();
        if ($old_data) {
            if ($old_data->odata_sub_file_name != $odata_sub_file_name) {
                $changes['odata_sub_file_name'] = array(
                    'old' => $old_data->odata_sub_file_name,
                    'new' => $odata_sub_file_name
                );
            }
            if ($old_data->odata_sub_file_ref_id != $odata_sub_file_ref_id) {
                $changes['odata_sub_file_ref_id'] = array(
                    'old' => $old_data->odata_sub_file_ref_id,
                    'new' => $odata_sub_file_ref_id
                );
            }
            if ($odata_sub_file_doc && $old_data->odata_sub_file_doc != $odata_sub_file_doc) {
                $changes['odata_sub_file_doc'] = array(
                    'old' => $old_data->odata_sub_file_doc,
                    'new' => $odata_sub_file_doc
                );
            }
        }

        $this->log_model->add_log(
            'แก้ไข',
            'ฐานข้อมูลเปิดภาครัฐ (OPEN DATA)',
            'ไฟล์: ' . $odata_sub_file_name . ' (หมวดหมู่ย่อย: ' . ($odata_sub ? $odata_sub->odata_sub_name : 'ไม่ระบุ') . ')',
            $odata_sub_file_id,
            array(
                'changes' => $changes,
                'sub_ref_id' => $odata_sub_file_ref_id,
                'sub_name' => $odata_sub ? $odata_sub->odata_sub_name : null,
                'main_name' => $odata_main ? $odata_main->odata_name : null,
                'total_changes' => count($changes),
                'file_updated' => !empty($odata_sub_file_doc)
            )
        );
        // =========================================================

        $this->session->set_flashdata('save_success', TRUE);
    } else {
        $this->session->set_flashdata('save_error', 'เกิดข้อผิดพลาดในการอัปเดตข้อมูล');
    }
}

    // ฟังก์ชันสำหรับลบไฟล์จากเซิร์ฟเวอร์
    private function delete_file_from_server($filename)
    {
        $file_path = './docs/file/' . $filename;
        if (file_exists($file_path)) {
            unlink($file_path);
        }
    }

    public function del_odata_sub_file($odata_sub_file_id)
{
    // ดึงข้อมูลก่อนลบ
    $file_data = $this->read_odata_sub_file($odata_sub_file_id);
    
    $odata_sub = null;
    $odata_main = null;
    if ($file_data) {
        $odata_sub = $this->read_odata_sub($file_data->odata_sub_file_ref_id);
        if ($odata_sub) {
            $odata_main = $this->read($odata_sub->odata_sub_ref_id);
        }
    }

    $old_document = $this->db->get_where('tbl_odata_sub_file', array('odata_sub_file_id' => $odata_sub_file_id))->row();

    $old_file_path = './docs/file/' . $old_document->odata_sub_file_doc;
    if (file_exists($old_file_path)) {
        unlink($old_file_path);
    }

    $this->db->delete('tbl_odata_sub_file', array('odata_sub_file_id' => $odata_sub_file_id));

    // บันทึก log การลบไฟล์ =======================================
    if ($file_data) {
        $this->log_model->add_log(
            'ลบ',
            'ฐานข้อมูลเปิดภาครัฐ (OPEN DATA)',
            'ไฟล์: ' . $file_data->odata_sub_file_name . ' (หมวดหมู่ย่อย: ' . ($odata_sub ? $odata_sub->odata_sub_name : 'ไม่ระบุ') . ')',
            $odata_sub_file_id,
            array(
                'deleted_date' => date('Y-m-d H:i:s'),
                'file_name' => $file_data->odata_sub_file_name,
                'file_doc' => $file_data->odata_sub_file_doc,
                'sub_name' => $odata_sub ? $odata_sub->odata_sub_name : null,
                'main_name' => $odata_main ? $odata_main->odata_name : null
            )
        );
    }
    // =========================================================

    $this->space_model->update_server_current();
}


    public function del_odata_sub($odata_sub_id)
{
    // ดึงข้อมูลก่อนลบ
    $odata_sub = $this->read_odata_sub($odata_sub_id);
    $odata_main = null;
    if ($odata_sub) {
        $odata_main = $this->read($odata_sub->odata_sub_ref_id);
    }

    // นับจำนวนไฟล์ที่จะถูกลบ
    $file_count = $this->db->where('odata_sub_file_ref_id', $odata_sub_id)->count_all_results('tbl_odata_sub_file');

    // ดึงข้อมูลทั้งหมดจาก tbl_odata_sub_file ที่เชื่อมโยงไปยัง tbl_odata_sub
    $query = $this->db->get_where('tbl_odata_sub_file', array('odata_sub_file_ref_id' => $odata_sub_id));
    $files = $query->result();

    // วนลูปผ่านข้อมูลเพื่อลบไฟล์แต่ละไฟล์
    foreach ($files as $file) {
        $file_path = './docs/file/' . $file->odata_sub_file_doc;
        if (file_exists($file_path)) {
            unlink($file_path); // ลบไฟล์จากระบบ
        }
    }

    // ลบข้อมูลใน tbl_odata_sub_file ที่เชื่อมโยงไปยัง tbl_odata_sub
    $this->db->where('odata_sub_file_ref_id', $odata_sub_id);
    $this->db->delete('tbl_odata_sub_file');

    // ลบข้อมูลใน tbl_odata_sub
    $this->db->where('odata_sub_id', $odata_sub_id);
    $this->db->delete('tbl_odata_sub');

    // บันทึก log การลบหมวดหมู่ย่อย =================================
    if ($odata_sub) {
        $this->log_model->add_log(
            'ลบ',
            'ฐานข้อมูลเปิดภาครัฐ (OPEN DATA)',
            'หมวดหมู่ย่อย: ' . $odata_sub->odata_sub_name . ' (หมวดหมู่หลัก: ' . ($odata_main ? $odata_main->odata_name : 'ไม่ระบุ') . ') พร้อม ' . $file_count . ' ไฟล์',
            $odata_sub_id,
            array(
                'deleted_date' => date('Y-m-d H:i:s'),
                'sub_name' => $odata_sub->odata_sub_name,
                'main_name' => $odata_main ? $odata_main->odata_name : null,
                'deleted_files_count' => $file_count
            )
        );
    }
    // ==========================================================

    // อัปเดตสถานะของเซิร์ฟเวอร์ปัจจุบัน (ถ้าจำเป็น)
    $this->space_model->update_server_current();
}


    public function del_odata($odata_id)
{
    // ดึงข้อมูลก่อนลบ
    $odata_main = $this->read($odata_id);

    // นับจำนวน sub และ file ที่จะถูกลบ
    $sub_count = $this->db->where('odata_sub_ref_id', $odata_id)->count_all_results('tbl_odata_sub');
    
    // นับจำนวนไฟล์ทั้งหมด
    $this->db->select('COUNT(tbl_odata_sub_file.odata_sub_file_id) as file_count');
    $this->db->from('tbl_odata_sub');
    $this->db->join('tbl_odata_sub_file', 'tbl_odata_sub.odata_sub_id = tbl_odata_sub_file.odata_sub_file_ref_id', 'left');
    $this->db->where('tbl_odata_sub.odata_sub_ref_id', $odata_id);
    $file_result = $this->db->get()->row();
    $file_count = $file_result ? $file_result->file_count : 0;

    // ดึง odata_sub_id จาก tbl_odata_sub
    $this->db->select('odata_sub_id');
    $this->db->where('odata_sub_ref_id', $odata_id);
    $query = $this->db->get('tbl_odata_sub');
    $results = $query->result();

    // วนลูปผ่านแต่ละผลลัพธ์
    foreach ($results as $result) {
        $odata_sub_id = $result->odata_sub_id;

        // ดึงข้อมูลไฟล์จาก tbl_odata_sub_file ที่เชื่อมโยงกับ odata_sub_id
        $file_query = $this->db->get_where('tbl_odata_sub_file', array('odata_sub_file_ref_id' => $odata_sub_id));
        $files = $file_query->result();

        // วนลูปผ่านข้อมูลเพื่อลบไฟล์แต่ละไฟล์
        foreach ($files as $file) {
            $file_path = './docs/file/' . $file->odata_sub_file_doc;
            if (file_exists($file_path)) {
                unlink($file_path); // ลบไฟล์จากระบบ
            }
        }

        // ลบข้อมูลใน tbl_odata_sub_file ที่เชื่อมโยงกับ odata_sub_id
        $this->db->where('odata_sub_file_ref_id', $odata_sub_id);
        $this->db->delete('tbl_odata_sub_file');
    }

    // ลบข้อมูลใน tbl_odata_sub ที่เชื่อมโยงกับ odata_id
    $this->db->where('odata_sub_ref_id', $odata_id);
    $this->db->delete('tbl_odata_sub');

    // ลบข้อมูลใน tbl_odata
    $this->db->where('odata_id', $odata_id);
    $this->db->delete('tbl_odata');

    // บันทึก log การลบหมวดหมู่หลัก =================================
    if ($odata_main) {
        $this->log_model->add_log(
            'ลบ',
            'ฐานข้อมูลเปิดภาครัฐ (OPEN DATA)',
            'หมวดหมู่: ' . $odata_main->odata_name . ' พร้อม ' . $sub_count . ' หมวดหมู่ย่อย และ ' . $file_count . ' ไฟล์',
            $odata_id,
            array(
                'deleted_date' => date('Y-m-d H:i:s'),
                'odata_name' => $odata_main->odata_name,
                'deleted_subs_count' => $sub_count,
                'deleted_files_count' => $file_count,
                'created_by' => $odata_main->odata_by ?? null
            )
        );
    }
    // ==========================================================

    // อัปเดตสถานะของเซิร์ฟเวอร์ปัจจุบัน (ถ้าจำเป็น)
    $this->space_model->update_server_current();
}
    
    public function odata_frontend()
    {
        $this->db->select('*');
        $this->db->from('tbl_odata');
        $this->db->order_by('tbl_odata.odata_id', 'DESC');
        $query = $this->db->get();
        return $query->result();
    }

    public function increment_download_odata_sub_file($odata_sub_file_id)
    {
        $this->db->where('odata_sub_file_id', $odata_sub_file_id);
        $this->db->set('odata_sub_file_download', 'odata_sub_file_download + 1', false); // บวกค่า mui_download ทีละ 1
        $this->db->update('tbl_odata_sub_file');
    }
}
