<?php
class System_config_model extends CI_Model
{
    public function __construct()
    {
        parent::__construct();
        $this->load->model('space_model');
    }

    public function list()
    {
        $this->db->select('*');
        $this->db->from('tbl_system_config');
        $this->db->order_by('tbl_system_config.id', 'asc');
        $query = $this->db->get();
        return $query->result();
    }

    public function list_by_type($type)
    {
        $this->db->select('*');
        $this->db->from('tbl_system_config');
        $this->db->where('type', $type);
        $this->db->order_by('tbl_system_config.id', 'asc');
        $query = $this->db->get();
        return $query->result();
    }

    public function read($id)
    {
        $this->db->select('*');
        $this->db->from('tbl_system_config');
        $this->db->where('tbl_system_config.id', $id);
        $query = $this->db->get();
        if ($query->num_rows() > 0) {
            $data = $query->row();
            return $data;
        }
        return false;
    }

    public function edit($id)
    {

        $data = array(
            'keyword' => $this->input->post('keyword'),
            'value' => $this->input->post('value'),
            'description' => $this->input->post('description'),
            'update_by' => $this->session->userdata('m_fname')
        );

        $this->db->where('id', $id);
        $this->db->update('tbl_system_config', $data);

        $this->space_model->update_server_current();
        $this->session->set_flashdata('save_success', TRUE);
    }

    public function update_domain($keyword, $value)
    {
        // ตรวจสอบว่ามี keyword นี้อยู่ในฐานข้อมูลหรือไม่
        $this->db->where('keyword', $keyword);
        $query = $this->db->get('tbl_system_config');

        $data = array(
            'value' => $value,
            'update_by' => $this->session->userdata('m_fname') ?? '',
            'update_date' => date('Y-m-d H:i:s')
        );

        if ($query->num_rows() > 0) {
            // ถ้ามี keyword อยู่แล้ว ให้อัพเดต
            $this->db->where('keyword', $keyword);
            $this->db->update('tbl_system_config', $data);
        } else {
            // ถ้ายังไม่มี keyword ให้เพิ่มใหม่
            $data['keyword'] = $keyword;
            $data['description'] = 'ชื่อโดเมน (อัพเดตอัตโนมัติ)';
            $this->db->insert('tbl_system_config', $data);
        }
        // เพิ่มข้อความแจ้งเตือน
        $this->session->set_flashdata('save_success', TRUE);
        return true;
    }

    public function get_all_config()
    {
        $query = $this->db->get('tbl_system_config');
        $result = array();

        foreach ($query->result() as $row) {
            $result[$row->keyword] = $row->value;
        }

        return $result;
    }



    public function add()
    {
        $data = array(
            'keyword' => $this->input->post('keyword'),
            'value' => $this->input->post('value'),
            'description' => $this->input->post('description'),
            'type' => $this->input->post('type'),
            'update_by' => $this->session->userdata('m_fname'),
            'update_date' => date('Y-m-d H:i:s')
        );

        $query = $this->db->insert('tbl_system_config', $data);

        // อัพเดตเซิร์ฟเวอร์หากมี
        $this->space_model->update_server_current();
        $this->session->set_flashdata('save_success', TRUE);

        return $query;
    }

    // เพิ่มเมธอดสำหรับดึงข้อมูลประเภทที่มีอยู่ในระบบ
    public function get_distinct_types()
    {
        $this->db->select('DISTINCT(type) as type');
        $this->db->from('tbl_system_config');
        $this->db->where('type IS NOT NULL');
        $this->db->where('type !=', '');
        $this->db->order_by('type', 'asc');
        $query = $this->db->get();
        return $query->result();
    }

    // เพิ่มเมธอดสำหรับลบข้อมูล
    public function delete($id)
    {
        $this->db->where('id', $id);
        $result = $this->db->delete('tbl_system_config');

        // อัพเดตเซิร์ฟเวอร์หากมี
        if ($result) {
            $this->space_model->update_server_current();
        }

        return $result;
    }




    /**
     * ดึงค่า config จาก keyword
     * @param string $keyword
     * @return string|null
     */
    public function get_config_by_key($keyword)
    {
        $this->db->select('value');
        $this->db->from('tbl_system_config');
        $this->db->where('keyword', $keyword);
        $query = $this->db->get();

        if ($query->num_rows() > 0) {
            return $query->row()->value;
        }

        return null;
    }

    /**
     * อัพเดตค่า config ตาม keyword (สำหรับ Dark Mode)
     * @param string $keyword
     * @param string $value
     * @return bool
     */
    public function update_by_keyword($keyword, $value)
    {
        $this->db->where('keyword', $keyword);
        $query = $this->db->get('tbl_system_config');

        $data = array(
            'value' => $value,
            'update_by' => $this->session->userdata('m_fname') ?? 'System',
            'update_date' => date('Y-m-d H:i:s')
        );

        if ($query->num_rows() > 0) {
            // อัพเดตถ้ามีอยู่แล้ว
            $this->db->where('keyword', $keyword);
            $result = $this->db->update('tbl_system_config', $data);
        } else {
            // เพิ่มใหม่ถ้ายังไม่มี
            $data['keyword'] = $keyword;
            $data['description'] = 'Dark Mode Setting';
            $data['type'] = 'setting';
            $result = $this->db->insert('tbl_system_config', $data);
        }

        // อัพเดตเซิร์ฟเวอร์
        if ($result) {
            $this->space_model->update_server_current();
            $this->session->set_flashdata('save_success', TRUE);
        }

        return $result;
    }

    /**
     * 🆕 ดึงข้อมูลที่อยู่ทั้งกลุ่ม (เฉพาะ 4 ฟิลด์หลัก)
     */
    public function get_address_group()
    {
        log_message('info', 'Getting address group data (4 main fields only)');

        // เฉพาะ 4 ฟิลด์หลัก
        $keywords = ['subdistric', 'district', 'province', 'zip_code'];

        $this->db->select('*');
        $this->db->from('tbl_system_config');
        $this->db->where_in('keyword', $keywords);
        $this->db->order_by('id', 'asc');
        $query = $this->db->get();

        $result = [];
        foreach ($query->result() as $row) {
            $result[$row->keyword] = $row->value;
        }

        log_message('debug', 'Address group data: ' . json_encode($result));
        return $result;
    }

    /**
     * 🆕 อัพเดตข้อมูลที่อยู่ (เฉพาะ 4 ฟิลด์หลัก - ไม่บันทึกรหัส)
     * ✅ ใช้ keyword แทน id
     */
    public function update_address_only($data)
    {
        log_message('info', '=== START update_address_only ===');
        log_message('debug', 'Input data: ' . json_encode($data));

        // ✅ ใช้ keyword แทน id
        $keywords = ['subdistric', 'district', 'province', 'zip_code'];

        $username = $this->session->userdata('m_fname') ?? 'System';
        $updated_count = 0;

        foreach ($keywords as $keyword) {
            // ตรวจสอบว่ามีข้อมูลส่งมาหรือไม่
            if (isset($data[$keyword]) && $data[$keyword] !== '' && $data[$keyword] !== null) {
                $update_data = [
                    'value' => $data[$keyword],
                    'update_by' => $username,
                    'update_date' => date('Y-m-d H:i:s')
                ];

                // ✅ อัพเดตโดยใช้ keyword
                $this->db->where('keyword', $keyword);
                $result = $this->db->update('tbl_system_config', $update_data);

                if ($result) {
                    log_message('info', "Updated keyword='$keyword' with value: " . $data[$keyword]);
                    $updated_count++;
                } else {
                    log_message('error', "Failed to update keyword='$keyword'");
                }
            }
        }

        // อัพเดตเซิร์ฟเวอร์
        $this->space_model->update_server_current();
        $this->session->set_flashdata('save_success', TRUE);

        log_message('info', "Total updated: $updated_count records (4 main fields only)");
        log_message('info', '=== END update_address_only ===');

        return $updated_count > 0;
    }

    /**
     * 🗑️ ฟังก์ชันเก่า (ไม่แนะนำให้ใช้แล้ว)
     * เก็บไว้เผื่อต้องการใช้ในอนาคต
     */
    public function update_address_with_codes($data)
    {
        log_message('info', '=== START update_address_with_codes (DEPRECATED) ===');
        log_message('warning', 'This function is deprecated. Use update_address_only() instead.');
        
        // ใช้ keyword แทน id
        $mapping = [
            'subdistric' => 'subdistric',
            'subdistric_id' => 'subdistric_id',
            'district' => 'district',
            'district_id' => 'district_id',
            'province' => 'province',
            'province_id' => 'province_id',
            'zip_code' => 'zip_code'
        ];

        $username = $this->session->userdata('m_fname') ?? 'System';
        $updated_count = 0;

        foreach ($mapping as $input_key => $keyword) {
            if (isset($data[$input_key]) && $data[$input_key] !== '' && $data[$input_key] !== null) {
                $update_data = [
                    'value' => $data[$input_key],
                    'update_by' => $username,
                    'update_date' => date('Y-m-d H:i:s')
                ];

                $this->db->where('keyword', $keyword);
                $result = $this->db->update('tbl_system_config', $update_data);

                if ($result) {
                    log_message('info', "Updated keyword='$keyword' with value: " . $data[$input_key]);
                    $updated_count++;
                } else {
                    log_message('error', "Failed to update keyword='$keyword'");
                }
            }
        }

        $this->space_model->update_server_current();
        $this->session->set_flashdata('save_success', TRUE);

        log_message('info', "Total updated: $updated_count records");
        log_message('info', '=== END update_address_with_codes ===');

        return $updated_count > 0;
    }
}