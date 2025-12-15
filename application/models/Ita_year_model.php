<?php
class Ita_year_model extends CI_Model
{
    public function __construct()
    {
        parent::__construct();
        $this->load->model('space_model');
        $this->load->model('ita_year_model');
		// log เก็บข้อมูล
        $this->load->model('log_model');
    }

public function add_year()
{
    // ดึงค่าจากฟอร์ม
    $ita_year_year = $this->input->post('ita_year_year');

    // ตรวจสอบว่ามีข้อมูลซ้ำหรือไม่
    $duplicate_check = $this->db->get_where('tbl_ita_year', array('ita_year_year' => $ita_year_year));

    if ($duplicate_check->num_rows() > 0) {
        // ถ้ามีข้อมูลซ้ำ
        $this->session->set_flashdata('save_again', TRUE);
    } else {
        // ถ้าไม่มีข้อมูลซ้ำ, ทำการเพิ่มข้อมูล
        $data = array(
            'ita_year_year' => $ita_year_year,
            'ita_year_by' => $this->session->userdata('m_fname'),
        );

        $query = $this->db->insert('tbl_ita_year', $data);
        $ita_year_id = $this->db->insert_id();

        $this->space_model->update_server_current();

        if ($query) {
            // บันทึก log การเพิ่มปี ITA =========================================
            $this->log_model->add_log(
                'เพิ่ม',
                'ITA ประจำปี',
                'ปี: ' . $ita_year_year,
                $ita_year_id,
                array(
                    'year' => $ita_year_year,
                    'created_by' => $this->session->userdata('m_fname')
                )
            );
            // ================================================================

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
        $this->db->order_by('ita_year_id', 'DESC');
        $query = $this->db->get('tbl_ita_year');
        return $query->result();
    }

    //show form edit
    public function read($ita_year_id)
    {
        $this->db->where('ita_year_id', $ita_year_id);
        $query = $this->db->get('tbl_ita_year');
        if ($query->num_rows() > 0) {
            $data = $query->row();
            return $data;
        }
        return FALSE;
    }

    public function edit_year($ita_year_id)
{
    // ดึงข้อมูลเก่าก่อนแก้ไข
    $old_data = $this->read($ita_year_id);

    $data = array(
        'ita_year_year' => $this->input->post('ita_year_year'),
        'ita_year_by' => $this->session->userdata('m_fname'),
    );

    $this->db->where('ita_year_id', $ita_year_id);
    $query = $this->db->update('tbl_ita_year', $data);

    $this->space_model->update_server_current();

    if ($query) {
        // บันทึก log การแก้ไขปี ITA =====================================
        $changes = array();
        if ($old_data) {
            if ($old_data->ita_year_year != $data['ita_year_year']) {
                $changes['ita_year_year'] = array(
                    'old' => $old_data->ita_year_year,
                    'new' => $data['ita_year_year']
                );
            }
        }

        $this->log_model->add_log(
            'แก้ไข',
            'ITA ประจำปี',
            'ปี: ' . $data['ita_year_year'],
            $ita_year_id,
            array(
                'changes' => $changes,
                'total_changes' => count($changes)
            )
        );
        // ===============================================================

        $this->session->set_flashdata('save_success', TRUE);
    } else {
        echo "<script>";
        echo "alert('เกิดข้อผิดพลาดในการอัปเดตข้อมูล !');";
        echo "</script>";
    }
}

    public function list_all_ita_topic($ita_year_id)
    {
        $this->db->select('*');
        $this->db->from('tbl_ita_year_topic');
        $this->db->join('tbl_ita_year', 'tbl_ita_year_topic.ita_year_topic_ref_id = tbl_ita_year.ita_year_id');
        $this->db->where('tbl_ita_year_topic.ita_year_topic_ref_id', $ita_year_id);
        $query = $this->db->get();
        $result = $query->result();
        return $result;
    }

    public function add_topic($ita_year_topic_ref_id, $ita_year_topic_name, $ita_year_topic_msg)
{
    $data = array(
        'ita_year_topic_ref_id' => $ita_year_topic_ref_id,
        'ita_year_topic_name' => $ita_year_topic_name,
        'ita_year_topic_msg' => $ita_year_topic_msg,
    );

    $this->db->insert('tbl_ita_year_topic', $data);
    $ita_year_topic_id = $this->db->insert_id();

    // ดึงข้อมูลปี ITA สำหรับ log
    $year_data = $this->read($ita_year_topic_ref_id);

    // บันทึก log การเพิ่มหัวข้อ ITA =====================================
    $this->log_model->add_log(
        'เพิ่ม',
        'ITA ประจำปี',
        'หัวข้อ: ' . $ita_year_topic_name . ' (ปี: ' . ($year_data ? $year_data->ita_year_year : 'ไม่ระบุ') . ')',
        $ita_year_topic_id,
        array(
            'topic_name' => $ita_year_topic_name,
            'topic_msg' => $ita_year_topic_msg,
            'year_ref_id' => $ita_year_topic_ref_id,
            'year' => $year_data ? $year_data->ita_year_year : null
        )
    );
    // ==================================================================

    $this->space_model->update_server_current();
    $this->session->set_flashdata('save_success', TRUE);
}
	
    public function read_topic($ita_year_topic_id)
    {
        $this->db->where('ita_year_topic_id', $ita_year_topic_id);
        $query = $this->db->get('tbl_ita_year_topic');
        if ($query->num_rows() > 0) {
            $data = $query->row();
            return $data;
        }
        return FALSE;
    }
    
	public function edit_topic($ita_year_topic_ref_id, $ita_year_topic_name, $ita_year_topic_msg, $ita_year_topic_id)
{
    // ดึงข้อมูลเก่าก่อนแก้ไข
    $old_data = $this->read_topic($ita_year_topic_id);

    $data = array(
        'ita_year_topic_name' => $ita_year_topic_name,
        'ita_year_topic_msg' => $ita_year_topic_msg,
    );

    $this->db->where('ita_year_topic_id', $ita_year_topic_id);
    $query = $this->db->update('tbl_ita_year_topic', $data);

    $this->space_model->update_server_current();

    if ($query) {
        // ดึงข้อมูลปี ITA สำหรับ log
        $year_data = $this->read($ita_year_topic_ref_id);

        // บันทึก log การแก้ไขหัวข้อ ITA =================================
        $changes = array();
        if ($old_data) {
            if ($old_data->ita_year_topic_name != $ita_year_topic_name) {
                $changes['ita_year_topic_name'] = array(
                    'old' => $old_data->ita_year_topic_name,
                    'new' => $ita_year_topic_name
                );
            }
            if ($old_data->ita_year_topic_msg != $ita_year_topic_msg) {
                $changes['ita_year_topic_msg'] = array(
                    'old' => 'มีการเปลี่ยนแปลง',
                    'new' => 'ข้อความใหม่'
                );
            }
        }

        $this->log_model->add_log(
            'แก้ไข',
            'ITA ประจำปี',
            'หัวข้อ: ' . $ita_year_topic_name . ' (ปี: ' . ($year_data ? $year_data->ita_year_year : 'ไม่ระบุ') . ')',
            $ita_year_topic_id,
            array(
                'changes' => $changes,
                'year_ref_id' => $ita_year_topic_ref_id,
                'year' => $year_data ? $year_data->ita_year_year : null,
                'total_changes' => count($changes)
            )
        );
        // ===============================================================

        $this->session->set_flashdata('save_success', TRUE);
    } else {
        echo "<script>";
        echo "alert('เกิดข้อผิดพลาดในการอัปเดตข้อมูล !');";
        echo "</script>";
    }
}

    public function read_link($ita_year_link_id)
    {
        $this->db->where('ita_year_link_id', $ita_year_link_id);
        $query = $this->db->get('tbl_ita_year_link');
        if ($query->num_rows() > 0) {
            $data = $query->row();
            return $data;
        }
        return FALSE;
    }

    public function list_all_ita_link($ita_year_topic_id)
    {
        $this->db->select('*');
        $this->db->from('tbl_ita_year_link');
        $this->db->join('tbl_ita_year_topic', 'tbl_ita_year_link.ita_year_link_ref_id = tbl_ita_year_topic.ita_year_topic_id');
        $this->db->where('tbl_ita_year_link.ita_year_link_ref_id', $ita_year_topic_id);
        $query = $this->db->get();
        $result = $query->result();
        return $result;
    }
    public function add_link($ita_year_link_ref_id, $ita_year_link_name, $ita_year_link_link1, $ita_year_link_link2, $ita_year_link_link3, $ita_year_link_link4, $ita_year_link_link5, $ita_year_link_title1, $ita_year_link_title2, $ita_year_link_title3, $ita_year_link_title4, $ita_year_link_title5)
{
    $data = array(
        'ita_year_link_ref_id' => $ita_year_link_ref_id,
        'ita_year_link_name' => $ita_year_link_name,
        'ita_year_link_link1' => $ita_year_link_link1,
        'ita_year_link_link2' => $ita_year_link_link2,
        'ita_year_link_link3' => $ita_year_link_link3,
        'ita_year_link_link4' => $ita_year_link_link4,
        'ita_year_link_link5' => $ita_year_link_link5,
        'ita_year_link_title1' => $ita_year_link_title1,
        'ita_year_link_title2' => $ita_year_link_title2,
        'ita_year_link_title3' => $ita_year_link_title3,
        'ita_year_link_title4' => $ita_year_link_title4,
        'ita_year_link_title5' => $ita_year_link_title5,
    );

    $this->db->insert('tbl_ita_year_link', $data);
    $ita_year_link_id = $this->db->insert_id();

    // ดึงข้อมูลหัวข้อและปี สำหรับ log
    $topic_data = $this->read_topic($ita_year_link_ref_id);
    $year_data = null;
    if ($topic_data) {
        $year_data = $this->read($topic_data->ita_year_topic_ref_id);
    }

    // นับจำนวน link ที่มีข้อมูล
    $link_count = 0;
    for ($i = 1; $i <= 5; $i++) {
        if (!empty($data['ita_year_link_link' . $i])) {
            $link_count++;
        }
    }

    // บันทึก log การเพิ่ม link ITA ======================================
    $this->log_model->add_log(
        'เพิ่ม',
        'ITA ประจำปี',
        'ลิงก์: ' . $ita_year_link_name . ' (หัวข้อ: ' . ($topic_data ? $topic_data->ita_year_topic_name : 'ไม่ระบุ') . ')',
        $ita_year_link_id,
        array(
            'link_name' => $ita_year_link_name,
            'topic_ref_id' => $ita_year_link_ref_id,
            'topic_name' => $topic_data ? $topic_data->ita_year_topic_name : null,
            'year' => $year_data ? $year_data->ita_year_year : null,
            'links_count' => $link_count,
            'links_data' => array(
                'link1' => $ita_year_link_link1,
                'link2' => $ita_year_link_link2,
                'link3' => $ita_year_link_link3,
                'link4' => $ita_year_link_link4,
                'link5' => $ita_year_link_link5
            )
        )
    );
    // ==================================================================

    $this->space_model->update_server_current();
    $this->session->set_flashdata('save_success', TRUE);
}

// แก้ไข edit_link function
public function edit_link($ita_year_link_id, $ita_year_link_ref_id, $ita_year_link_name, $ita_year_link_link1, $ita_year_link_link2, $ita_year_link_link3, $ita_year_link_link4, $ita_year_link_link5, $ita_year_link_title1, $ita_year_link_title2, $ita_year_link_title3, $ita_year_link_title4, $ita_year_link_title5)
{
    // ดึงข้อมูลเก่าก่อนแก้ไข
    $old_data = $this->read_link($ita_year_link_id);

    $data = array(
        'ita_year_link_name' => $ita_year_link_name,
        'ita_year_link_link1' => $ita_year_link_link1,
        'ita_year_link_link2' => $ita_year_link_link2,
        'ita_year_link_link3' => $ita_year_link_link3,
        'ita_year_link_link4' => $ita_year_link_link4,
        'ita_year_link_link5' => $ita_year_link_link5,
        'ita_year_link_title1' => $ita_year_link_title1,
        'ita_year_link_title2' => $ita_year_link_title2,
        'ita_year_link_title3' => $ita_year_link_title3,
        'ita_year_link_title4' => $ita_year_link_title4,
        'ita_year_link_title5' => $ita_year_link_title5,
    );

    $this->db->where('ita_year_link_id', $ita_year_link_id);
    $query = $this->db->update('tbl_ita_year_link', $data);

    $this->space_model->update_server_current();

    if ($query) {
        // ดึงข้อมูลหัวข้อและปี สำหรับ log
        $topic_data = $this->read_topic($ita_year_link_ref_id);
        $year_data = null;
        if ($topic_data) {
            $year_data = $this->read($topic_data->ita_year_topic_ref_id);
        }

        // เปรียบเทียบการเปลี่ยนแปลง
        $changes = array();
        if ($old_data) {
            if ($old_data->ita_year_link_name != $ita_year_link_name) {
                $changes['ita_year_link_name'] = array(
                    'old' => $old_data->ita_year_link_name,
                    'new' => $ita_year_link_name
                );
            }
            
            // ตรวจสอบการเปลี่ยนแปลงของ link แต่ละตัว
            for ($i = 1; $i <= 5; $i++) {
                $old_link = 'ita_year_link_link' . $i;
                $new_link = 'ita_year_link_link' . $i;
                if ($old_data->$old_link != $data[$new_link]) {
                    $changes[$new_link] = array(
                        'old' => $old_data->$old_link,
                        'new' => $data[$new_link]
                    );
                }
            }

            // ตรวจสอบการเปลี่ยนแปลงของ title แต่ละตัว
            for ($i = 1; $i <= 5; $i++) {
                $old_title = 'ita_year_link_title' . $i;
                $new_title = 'ita_year_link_title' . $i;
                if ($old_data->$old_title != $data[$new_title]) {
                    $changes[$new_title] = array(
                        'old' => $old_data->$old_title,
                        'new' => $data[$new_title]
                    );
                }
            }
        }

        // บันทึก log การแก้ไข link ITA ====================================
        $this->log_model->add_log(
            'แก้ไข',
            'ITA ประจำปี',
            'ลิงก์: ' . $ita_year_link_name . ' (หัวข้อ: ' . ($topic_data ? $topic_data->ita_year_topic_name : 'ไม่ระบุ') . ')',
            $ita_year_link_id,
            array(
                'changes' => $changes,
                'topic_ref_id' => $ita_year_link_ref_id,
                'topic_name' => $topic_data ? $topic_data->ita_year_topic_name : null,
                'year' => $year_data ? $year_data->ita_year_year : null,
                'total_changes' => count($changes)
            )
        );
        // ===============================================================

        $this->session->set_flashdata('save_success', TRUE);
    } else {
        echo "<script>";
        echo "alert('เกิดข้อผิดพลาดในการอัปเดตข้อมูล !');";
        echo "</script>";
    }
}

// แก้ไข del_ita_link function
public function del_ita_link($ita_year_link_id)
{
    // ดึงข้อมูลก่อนลบ
    $link_data = $this->read_link($ita_year_link_id);
    
    $topic_data = null;
    $year_data = null;
    if ($link_data) {
        $topic_data = $this->read_topic($link_data->ita_year_link_ref_id);
        if ($topic_data) {
            $year_data = $this->read($topic_data->ita_year_topic_ref_id);
        }
    }

    $this->db->delete('tbl_ita_year_link', array('ita_year_link_id' => $ita_year_link_id));

    // บันทึก log การลบ link ITA =======================================
    if ($link_data) {
        $this->log_model->add_log(
            'ลบ',
            'ITA ประจำปี',
            'ลิงก์: ' . $link_data->ita_year_link_name . ' (หัวข้อ: ' . ($topic_data ? $topic_data->ita_year_topic_name : 'ไม่ระบุ') . ')',
            $ita_year_link_id,
            array(
                'deleted_date' => date('Y-m-d H:i:s'),
                'link_name' => $link_data->ita_year_link_name,
                'topic_name' => $topic_data ? $topic_data->ita_year_topic_name : null,
                'year' => $year_data ? $year_data->ita_year_year : null
            )
        );
    }
    // ================================================================
}

// แก้ไข del_ita_topic function
public function del_ita_topic($ita_year_topic_id)
{
    // ดึงข้อมูลก่อนลบ
    $topic_data = $this->read_topic($ita_year_topic_id);
    $year_data = null;
    if ($topic_data) {
        $year_data = $this->read($topic_data->ita_year_topic_ref_id);
    }

    // ดึงจำนวน link ที่จะถูกลบ
    $link_count = $this->db->where('ita_year_link_ref_id', $ita_year_topic_id)->count_all_results('tbl_ita_year_link');

    // ลบข้อมูลใน tbl_ita_year_link ที่เชื่อมโยงไปยัง tbl_ita_year_topic
    $this->db->where('ita_year_link_ref_id', $ita_year_topic_id);
    $this->db->delete('tbl_ita_year_link');

    // ลบข้อมูลใน tbl_ita_year_topic
    $this->db->where('ita_year_topic_id', $ita_year_topic_id);
    $this->db->delete('tbl_ita_year_topic');

    // บันทึก log การลบหัวข้อ ITA =====================================
    if ($topic_data) {
        $this->log_model->add_log(
            'ลบ',
            'ITA ประจำปี',
            'หัวข้อ: ' . $topic_data->ita_year_topic_name . ' (ปี: ' . ($year_data ? $year_data->ita_year_year : 'ไม่ระบุ') . ') พร้อม ' . $link_count . ' ลิงก์',
            $ita_year_topic_id,
            array(
                'deleted_date' => date('Y-m-d H:i:s'),
                'topic_name' => $topic_data->ita_year_topic_name,
                'topic_msg' => $topic_data->ita_year_topic_msg,
                'year' => $year_data ? $year_data->ita_year_year : null,
                'deleted_links_count' => $link_count
            )
        );
    }
    // ================================================================
}

// แก้ไข del_ita_year function
public function del_ita_year($ita_year_id)
{
    // ดึงข้อมูลก่อนลบ
    $year_data = $this->read($ita_year_id);
    
    // นับจำนวน topic และ link ที่จะถูกลบ
    $topic_count = $this->db->where('ita_year_topic_ref_id', $ita_year_id)->count_all_results('tbl_ita_year_topic');
    
    // นับจำนวน link ทั้งหมด
    $this->db->select('COUNT(tbl_ita_year_link.ita_year_link_id) as link_count');
    $this->db->from('tbl_ita_year_topic');
    $this->db->join('tbl_ita_year_link', 'tbl_ita_year_topic.ita_year_topic_id = tbl_ita_year_link.ita_year_link_ref_id', 'left');
    $this->db->where('tbl_ita_year_topic.ita_year_topic_ref_id', $ita_year_id);
    $link_result = $this->db->get()->row();
    $link_count = $link_result ? $link_result->link_count : 0;

    // ดึง ita_year_topic_id จาก tbl_ita_year_topic
    $this->db->select('ita_year_topic_id');
    $this->db->where('ita_year_topic_ref_id', $ita_year_id);
    $this->db->order_by('ita_year_topic_id', 'asc');
    $this->db->limit(1);
    $query = $this->db->get('tbl_ita_year_topic');
    $result = $query->row();

    if ($result) {
        $ita_year_id_to_delete = $result->ita_year_topic_id;

        // ลบข้อมูลใน tbl_ita_year_link
        $this->db->where('ita_year_link_ref_id', $ita_year_id_to_delete);
        $this->db->delete('tbl_ita_year_link');

        // ลบข้อมูลใน tbl_ita_year_topic
        $this->db->where('ita_year_topic_ref_id', $ita_year_id);
        $this->db->delete('tbl_ita_year_topic');

        // ลบข้อมูลใน tbl_ita_year
        $this->db->where('ita_year_id', $ita_year_id);
        $this->db->delete('tbl_ita_year');
    }
    // ลบข้อมูลใน tbl_ita_year
    $this->db->where('ita_year_id', $ita_year_id);
    $this->db->delete('tbl_ita_year');

    // บันทึก log การลบปี ITA ============================================
    if ($year_data) {
        $this->log_model->add_log(
            'ลบ',
            'ITA ประจำปี',
            'ปี: ' . $year_data->ita_year_year . ' พร้อม ' . $topic_count . ' หัวข้อ และ ' . $link_count . ' ลิงก์',
            $ita_year_id,
            array(
                'deleted_date' => date('Y-m-d H:i:s'),
                'year' => $year_data->ita_year_year,
                'deleted_topics_count' => $topic_count,
                'deleted_links_count' => $link_count,
                'created_by' => $year_data->ita_year_by ?? null
            )
        );
    }
    // ==================================================================
}

    public function get_ita_year_data($ita_year_id)
    {
        $this->db->select('
        tbl_ita_year_topic.*,
        GROUP_CONCAT(
            JSON_OBJECT(
                "ita_year_link_id", tbl_ita_year_link.ita_year_link_id,
                "ita_year_link_name", tbl_ita_year_link.ita_year_link_name,
                "ita_year_link_title1", tbl_ita_year_link.ita_year_link_title1,
                "ita_year_link_title2", tbl_ita_year_link.ita_year_link_title2,
                "ita_year_link_title3", tbl_ita_year_link.ita_year_link_title3,
                "ita_year_link_title4", tbl_ita_year_link.ita_year_link_title4,
                "ita_year_link_title5", tbl_ita_year_link.ita_year_link_title5,
                "ita_year_link_link1", tbl_ita_year_link.ita_year_link_link1,
                "ita_year_link_link2", tbl_ita_year_link.ita_year_link_link2,
                "ita_year_link_link3", tbl_ita_year_link.ita_year_link_link3,
                "ita_year_link_link4", tbl_ita_year_link.ita_year_link_link4,
                "ita_year_link_link5", tbl_ita_year_link.ita_year_link_link5
            ) ORDER BY 
            CAST(SUBSTRING(tbl_ita_year_link.ita_year_link_name, 2) AS UNSIGNED) ASC
        ) AS link_data
    ');
        $this->db->from('tbl_ita_year_topic');
        $this->db->join('tbl_ita_year_link', 'tbl_ita_year_topic.ita_year_topic_id = tbl_ita_year_link.ita_year_link_ref_id', 'left');
        $this->db->where('tbl_ita_year_topic.ita_year_topic_ref_id', $ita_year_id);
        $this->db->group_by('tbl_ita_year_topic.ita_year_topic_id');

        $query = $this->db->get();
        // echo '<pre>';
        // print_r($query->result_array());
        // echo '</pre>';
        // exit;
        return $query->result();
    }

    // public function get_ita_year_link_data($ita_year_topic_id)
    // {
    //     $this->db->select('*');
    //     $this->db->from('tbl_ita_year_link');
    //     $this->db->where('tbl_ita_year_link.ita_year_link_ref_id', $ita_year_topic_id);
    //     $this->db->order_by('tbl_ita_year_link.ita_year_link_id', 'DESC');
    //     $query = $this->db->get();
    //     return $query->result();
    // }

    public function ita_year_frontend()
    {
        $this->db->select('*');
        $this->db->from('tbl_ita_year');
        $this->db->order_by('tbl_ita_year.ita_year_year', 'DESC');
        $query = $this->db->get();
        return $query->result();
    }
}
