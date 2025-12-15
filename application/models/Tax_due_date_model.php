<?php
class Tax_due_date_model extends CI_Model
{

    public function get_all_due_dates()
    {
        return $this->db->order_by('tax_type')->get('tbl_tax_due_dates')->result();
    }

    public function get_due_date($tax_type, $tax_year)
    {
        return $this->db->where('tax_type', $tax_type)
            ->where('tax_year', $tax_year)
            ->get('tbl_tax_due_dates')
            ->row();
    }

    public function add_due_date($data)
    {
        // ตรวจสอบว่ามีข้อมูลของประเภทภาษีนี้แล้วหรือไม่
        $existing = $this->db->where('tax_type', $data['tax_type'])->get('tbl_tax_due_dates')->row();
        if ($existing) {
            // ถ้ามีแล้วให้อัพเดต
            return $this->db->where('id', $existing->id)->update('tbl_tax_due_dates', $data);
        }
        // ถ้ายังไม่มีให้เพิ่มใหม่
        return $this->db->insert('tbl_tax_due_dates', $data);
    }

    public function update_due_date($id, $data)
    {
        return $this->db->where('id', $id)->update('tbl_tax_due_dates', $data);
    }

    public function delete_due_date($id)
    {
        return $this->db->where('id', $id)->delete('tbl_tax_due_dates');
    }

    public function get_by_id($id)
    {
        return $this->db->where('id', $id)->get('tbl_tax_due_dates')->row();
    }
}
