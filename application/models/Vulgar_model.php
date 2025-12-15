<?php
defined('BASEPATH') or exit('No direct script access allowed');

class Vulgar_model extends CI_Model
{
    public function __construct()
    {
        parent::__construct();
        $this->load->model('space_model');
    }

    // ==================== Vulgar Word Functions (ใช้โค้ดเดิม) ====================
    
    /**
     * เพิ่มคำหยาบ - เช็คข้อมูลซ้ำ
     */
    public function add()
    {
        $vulgar_com = $this->input->post('vulgar_com');
        $this->db->select('vulgar_com');
        $this->db->where('vulgar_com', $vulgar_com);
        $query = $this->db->get('tbl_vulgar');
        $num = $query->num_rows();
        
        if ($num > 0) {
            $this->session->set_flashdata('save_again', TRUE);
        } else {
            // Check used space
            $used_space_mb = $this->space_model->get_used_space();
            $upload_limit_mb = $this->space_model->get_limit_storage();
            
            // Calculate the total space required for all files
            $total_space_required = 0;
            if (!empty($_FILES['m_img']['name'])) {
                $total_space_required += $_FILES['m_img']['size'];
            }
            
            // Check if there's enough space
            if ($used_space_mb + ($total_space_required / (1024 * 1024 * 1024)) >= $upload_limit_mb) {
                $this->session->set_flashdata('save_error', TRUE);
                redirect('vulgar');
                return;
            }
            
            $data = array(
                'vulgar_com' => $this->input->post('vulgar_com'),
                'vulgar_by' => $this->session->userdata('m_fname'), // เพิ่มชื่อคนที่เพิ่มข้อมูล
            );
            
            $query = $this->db->insert('tbl_vulgar', $data);
            $this->space_model->update_server_current();
            $this->session->set_flashdata('save_success', TRUE);
        }
    }

    /**
     * ดึงรายการคำหยาบ
     */
    public function list()
    {
        $this->db->select('*');
        $this->db->from('tbl_vulgar');
        $this->db->order_by('tbl_vulgar.vulgar_id', 'DESC');
        $query = $this->db->get();
        return $query->result();
    }

    /**
     * อ่านข้อมูลคำหยาบ
     */
    public function read($vulgar_id)
    {
        $this->db->select('*');
        $this->db->from('tbl_vulgar');
        $this->db->where('tbl_vulgar.vulgar_id', $vulgar_id);
        $query = $this->db->get();
        
        if ($query->num_rows() > 0) {
            $data = $query->row();
            return $data;
        }
        return false;
    }

    /**
     * แก้ไขคำหยาบ
     */
    public function edit($vulgar_id)
    {
        $vulgar_com = $this->input->post('vulgar_com');
        
        // Check if the new vulgar_com value is not already in the database for other records.
        $this->db->where('vulgar_com', $vulgar_com);
        $this->db->where_not_in('vulgar_id', $vulgar_id); // Exclude the current record being edited.
        $query = $this->db->get('tbl_vulgar');
        $num = $query->num_rows();
        
        if ($num > 0) {
            // A record with the same vulgar_com already exists in the database.
            $this->session->set_flashdata('save_again', TRUE);
        } else {
            // Update the record.
            $data = array(
                'vulgar_com' => $vulgar_com,
                'vulgar_by' => $this->session->userdata('m_fname'), // Add the name of the person updating the record.
            );
            
            $this->db->where('vulgar_id', $vulgar_id);
            $this->db->update('tbl_vulgar', $data);
            $this->space_model->update_server_current();
            $this->session->set_flashdata('save_success', TRUE);
        }
    }

    /**
     * ลบคำหยาบ
     */
    public function del($vulgar_id)
    {
        $this->db->delete('tbl_vulgar', array('vulgar_id' => $vulgar_id));
        $this->session->set_flashdata('del_success', TRUE);
    }

    // ==================== Whitelist Functions (เพิ่มใหม่) ====================
    
    /**
     * ดึงรายการ whitelist ทั้งหมด
     */
    public function get_whitelist()
    {
        $this->db->select('*');
        $this->db->from('tbl_vulgar_whitelist');
        $this->db->order_by('whitelist_word', 'ASC');
        $query = $this->db->get();
        
        // แปลงให้เป็นรูปแบบที่ vulgar_check library ต้องการ
        $result = array();
        if ($query->num_rows() > 0) {
            foreach ($query->result() as $row) {
                $result[] = array(
                    'id' => $row->whitelist_id,
                    'word' => $row->whitelist_word,
                    'description' => $row->whitelist_desc,
                    'created_at' => $row->created_at
                );
            }
        }
        return $result;
    }

    /**
     * เพิ่มคำใน whitelist - เช็คข้อมูลซ้ำ
     */
    public function add_whitelist()
    {
        $whitelist_word = trim($this->input->post('whitelist_word'));
        
        // ตรวจสอบว่ามีคำนี้แล้วหรือไม่
        $this->db->select('whitelist_word');
        $this->db->where('whitelist_word', $whitelist_word);
        $query = $this->db->get('tbl_vulgar_whitelist');
        $num = $query->num_rows();
        
        if ($num > 0) {
            $this->session->set_flashdata('whitelist_duplicate', TRUE);
            return false;
        } else {
            $data = array(
                'whitelist_word' => $whitelist_word,
                'whitelist_desc' => trim($this->input->post('whitelist_desc')),
                'created_at' => date('Y-m-d H:i:s')
            );
            
            $result = $this->db->insert('tbl_vulgar_whitelist', $data);
            
            if ($result) {
                $this->session->set_flashdata('whitelist_success', TRUE);
                return true;
            } else {
                $this->session->set_flashdata('whitelist_error', TRUE);
                return false;
            }
        }
    }

    /**
     * อ่านข้อมูล whitelist ตาม ID
     */
    public function read_whitelist($whitelist_id)
    {
        $this->db->select('*');
        $this->db->from('tbl_vulgar_whitelist');
        $this->db->where('whitelist_id', $whitelist_id);
        $query = $this->db->get();
        
        if ($query->num_rows() > 0) {
            return $query->row();
        }
        return false;
    }

    /**
     * แก้ไขข้อมูล whitelist
     */
    public function edit_whitelist($whitelist_id)
    {
        $whitelist_word = trim($this->input->post('whitelist_word'));
        
        // ตรวจสอบว่ามีคำนี้แล้วหรือไม่ (ยกเว้นตัวเอง)
        $this->db->where('whitelist_word', $whitelist_word);
        $this->db->where_not_in('whitelist_id', $whitelist_id);
        $query = $this->db->get('tbl_vulgar_whitelist');
        $num = $query->num_rows();
        
        if ($num > 0) {
            $this->session->set_flashdata('whitelist_duplicate', TRUE);
            return false;
        } else {
            $data = array(
                'whitelist_word' => $whitelist_word,
                'whitelist_desc' => trim($this->input->post('whitelist_desc')),
                'updated_at' => date('Y-m-d H:i:s')
            );
            
            $this->db->where('whitelist_id', $whitelist_id);
            $result = $this->db->update('tbl_vulgar_whitelist', $data);
            
            if ($result) {
                $this->session->set_flashdata('whitelist_success', TRUE);
                return true;
            } else {
                $this->session->set_flashdata('whitelist_error', TRUE);
                return false;
            }
        }
    }

    /**
     * ลบข้อมูล whitelist
     */
    public function del_whitelist($whitelist_id)
    {
        $this->db->delete('tbl_vulgar_whitelist', array('whitelist_id' => $whitelist_id));
        $this->session->set_flashdata('whitelist_del_success', TRUE);
    }

    /**
     * ตรวจสอบว่าคำอยู่ใน whitelist หรือไม่
     */
    public function is_whitelisted($word)
    {
        $this->db->where('whitelist_word', $word);
        $query = $this->db->get('tbl_vulgar_whitelist');
        return $query->num_rows() > 0;
    }

    /**
     * ค้นหาใน whitelist
     */
    public function search_whitelist($keyword)
    {
        $this->db->like('whitelist_word', $keyword);
        $this->db->or_like('whitelist_desc', $keyword);
        $this->db->order_by('whitelist_word', 'ASC');
        $query = $this->db->get('tbl_vulgar_whitelist');
        return $query->result();
    }

    /**
     * นับจำนวน whitelist
     */
    public function count_whitelist()
    {
        return $this->db->count_all('tbl_vulgar_whitelist');
    }

    /**
     * นับจำนวนคำหยาบ
     */
    public function count_vulgar()
    {
        return $this->db->count_all('tbl_vulgar');
    }

    /**
     * ได้สถิติการใช้งาน
     */
    public function get_statistics()
    {
        $stats = array();
        
        // นับจำนวนคำหยาบ
        $stats['vulgar_count'] = $this->count_vulgar();
        
        // นับจำนวน whitelist
        $stats['whitelist_count'] = $this->count_whitelist();
        
        // อัตราส่วน
        $stats['ratio'] = $stats['vulgar_count'] > 0 ? round($stats['whitelist_count'] / $stats['vulgar_count'], 2) : 0;
        
        // คำ whitelist ล่าสุด
        $this->db->order_by('created_at', 'DESC');
        $this->db->limit(5);
        $stats['recent_whitelist'] = $this->db->get('tbl_vulgar_whitelist')->result();
        
        // คำหยาบล่าสุด
        $this->db->order_by('vulgar_datesave', 'DESC');
        $this->db->limit(5);
        $stats['recent_vulgar'] = $this->db->get('tbl_vulgar')->result();
        
        return $stats;
    }

    /**
     * เพิ่มคำหลายคำพร้อมกัน (bulk insert)
     */
    public function bulk_add_whitelist($words_array)
    {
        $success_count = 0;
        
        foreach ($words_array as $word_data) {
            // ตรวจสอบว่ามีคำนี้แล้วหรือไม่
            $existing = $this->db->where('whitelist_word', $word_data['word'])
                                 ->get('tbl_vulgar_whitelist');
            
            if ($existing->num_rows() == 0) {
                $data = array(
                    'whitelist_word' => trim($word_data['word']),
                    'whitelist_desc' => isset($word_data['description']) ? trim($word_data['description']) : '',
                    'created_at' => date('Y-m-d H:i:s')
                );
                
                if ($this->db->insert('tbl_vulgar_whitelist', $data)) {
                    $success_count++;
                }
            }
        }
        
        return $success_count;
    }

    /**
     * สำรองข้อมูล whitelist (export)
     */
    public function export_whitelist()
    {
        $this->db->order_by('whitelist_word', 'ASC');
        $query = $this->db->get('tbl_vulgar_whitelist');
        return $query->result_array();
    }

    /**
     * นำเข้าข้อมูล whitelist (import)
     */
    public function import_whitelist($data_array)
    {
        $success_count = 0;
        $error_count = 0;
        $duplicate_count = 0;
        
        foreach ($data_array as $row) {
            if (empty($row['whitelist_word'])) {
                $error_count++;
                continue;
            }
            
            // ตรวจสอบว่ามีคำนี้แล้วหรือไม่
            $existing = $this->db->where('whitelist_word', $row['whitelist_word'])
                                 ->get('tbl_vulgar_whitelist');
            
            if ($existing->num_rows() > 0) {
                $duplicate_count++;
                continue;
            }
            
            $data = array(
                'whitelist_word' => trim($row['whitelist_word']),
                'whitelist_desc' => isset($row['whitelist_desc']) ? trim($row['whitelist_desc']) : '',
                'created_at' => date('Y-m-d H:i:s')
            );
            
            if ($this->db->insert('tbl_vulgar_whitelist', $data)) {
                $success_count++;
            } else {
                $error_count++;
            }
        }
        
        return array(
            'success' => $success_count,
            'error' => $error_count,
            'duplicate' => $duplicate_count
        );
    }

    // ==================== Helper Functions ====================
    
    /**
     * ตรวจสอบว่าตาราง whitelist มีอยู่หรือไม่
     */
    public function check_whitelist_table()
    {
        return $this->db->table_exists('tbl_vulgar_whitelist');
    }

    /**
     * สร้างตาราง whitelist หากยังไม่มี
     */
    public function create_whitelist_table()
    {
        $sql = "CREATE TABLE IF NOT EXISTS `tbl_vulgar_whitelist` (
            `whitelist_id` int(11) NOT NULL AUTO_INCREMENT,
            `whitelist_word` varchar(255) NOT NULL,
            `whitelist_desc` text DEFAULT NULL,
            `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
            `updated_at` timestamp NULL DEFAULT NULL ON UPDATE CURRENT_TIMESTAMP,
            PRIMARY KEY (`whitelist_id`),
            UNIQUE KEY `whitelist_word` (`whitelist_word`),
            INDEX `idx_whitelist_word` (`whitelist_word`)
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;";
        
        return $this->db->query($sql);
    }

    /**
     * เพิ่มข้อมูล whitelist เริ่มต้น
     */
    public function add_default_whitelist()
    {
        $default_words = array(
            array('word' => 'กู้ชีพ', 'description' => 'การช่วยชีวิต'),
            array('word' => 'กู้ภัย', 'description' => 'การช่วยเหลือในภัยพิบัติ'),
            array('word' => 'กู้คืน', 'description' => 'การเอากลับคืนมา'),
            array('word' => 'กู้เงิน', 'description' => 'การขอยืมเงิน'),
            array('word' => 'กู้ยืม', 'description' => 'การขอยืม'),
            array('word' => 'ไอศกรีม', 'description' => 'ขนมหวานเย็น'),
            array('word' => 'ไอเดีย', 'description' => 'ความคิด'),
            array('word' => 'ไอโฟน', 'description' => 'โทรศัพท์ยี่ห้อ Apple'),
            array('word' => 'ไอแพด', 'description' => 'แท็บเล็ตยี่ห้อ Apple'),
            array('word' => 'ไอที', 'description' => 'เทคโนโลยีสารสนเทศ'),
            array('word' => 'แม่น้ำ', 'description' => 'สายน้ำธรรมชาติ'),
            array('word' => 'แม่บ้าน', 'description' => 'คนดูแลบ้าน'),
            array('word' => 'แม่ครัว', 'description' => 'คนทำอาหาร'),
            array('word' => 'พ่อค้า', 'description' => 'คนขายของ'),
            array('word' => 'พ่อบ้าน', 'description' => 'คนดูแลบ้าน'),
            array('word' => 'พ่อครัว', 'description' => 'คนทำอาหาร')
        );
        
        return $this->bulk_add_whitelist($default_words);
    }
}
?>