<?php
class Manual_admin_model extends CI_Model
{
    public function get_all()
    {
        return $this->db->get('tbl_manual_admin')->result();
    }
    
    public function get_by_id($id)
    {
        return $this->db->get_where('tbl_manual_admin', ['manual_admin_id' => $id])->row();
    }
    
    public function insert_manual_admin($data)
    {
        // ✅ แก้ไข: ตรวจสอบและกำหนดค่า default ถ้า manual_admin_by เป็น NULL
        if (empty($data['manual_admin_by'])) {
            $data['manual_admin_by'] = 'System';
        }
        
        return $this->db->insert('tbl_manual_admin', $data);
    }
    
    public function update_manual_admin($id, $data)
    {
        // ✅ แก้ไข: ตรวจสอบและกำหนดค่า default ถ้า manual_admin_by เป็น NULL
        if (empty($data['manual_admin_by'])) {
            $data['manual_admin_by'] = 'System';
        }
        
        $this->db->where('manual_admin_id', $id);
        return $this->db->update('tbl_manual_admin', $data);
    }
    
    public function delete_manual_admin($id)
    {
        $this->db->where('manual_admin_id', $id);
        return $this->db->delete('tbl_manual_admin');
    }
    
    public function increment_download_manual_admin($manual_admin_id)
    {
        $this->db->where('manual_admin_id', $manual_admin_id);
        $this->db->set('manual_admin_download', 'manual_admin_download + 1', false);
        $this->db->update('tbl_manual_admin');
    }
}