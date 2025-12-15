<?php
class Kid_aw_model extends CI_Model
{
    public function __construct()
    {
        parent::__construct();
        $this->load->model('space_model');
    }

    public function add_kid_aw()
    {

        $kid_aw_data = array(
            'kid_aw_id_num_eligible' => $this->input->post('kid_aw_id_num_eligible'),
            'kid_aw_name_eligible' => $this->input->post('kid_aw_name_eligible'),
            'kid_aw_id_num_owner' => $this->input->post('kid_aw_id_num_owner'),
            'kid_aw_name_owner' => $this->input->post('kid_aw_name_owner'),
            'kid_aw_agency' => $this->input->post('kid_aw_agency'),
            'kid_aw_bank' => $this->input->post('kid_aw_bank'),
            'kid_aw_type_payment' => $this->input->post('kid_aw_type_payment'),
            'kid_aw_bank_num' => $this->input->post('kid_aw_bank_num'),
            'kid_aw_period_payment' => $this->input->post('kid_aw_period_payment'),
            'kid_aw_money' => $this->input->post('kid_aw_money'),
            'kid_aw_note' => $this->input->post('kid_aw_note'),
            'kid_aw_by' => $this->session->userdata('m_fname') // เพิ่มชื่อคนที่แก้ไขข้อมูล
        );

        $this->db->trans_start();
        $this->db->insert('tbl_kid_aw', $kid_aw_data);

        $this->space_model->update_server_current();

        $this->db->trans_complete();

        $this->session->set_flashdata('save_success', TRUE);
    }

    public function list()
    {
        $this->db->select('*');
        $this->db->from('tbl_kid_aw');
        $this->db->group_by('tbl_kid_aw.kid_aw_id');
        $this->db->order_by('tbl_kid_aw.kid_aw_datesave', 'DESC');
        $query = $this->db->get();
        return $query->result();
    }

    //show form edit
    public function read_kid_aw($kid_aw_id)
    {
        $this->db->where('kid_aw_id', $kid_aw_id);
        $query = $this->db->get('tbl_kid_aw');
        if ($query->num_rows() > 0) {
            $data = $query->row();
            return $data;
        }
        return FALSE;
    }


    public function edit_kid_aw($kid_aw_id)
    {

        // Update kid_aw information
        $data = array(
            'kid_aw_id_num_eligible' => $this->input->post('kid_aw_id_num_eligible'),
            'kid_aw_name_eligible' => $this->input->post('kid_aw_name_eligible'),
            'kid_aw_id_num_owner' => $this->input->post('kid_aw_id_num_owner'),
            'kid_aw_name_owner' => $this->input->post('kid_aw_name_owner'),
            'kid_aw_agency' => $this->input->post('kid_aw_agency'),
            'kid_aw_bank' => $this->input->post('kid_aw_bank'),
            'kid_aw_type_payment' => $this->input->post('kid_aw_type_payment'),
            'kid_aw_bank_num' => $this->input->post('kid_aw_bank_num'),
            'kid_aw_period_payment' => $this->input->post('kid_aw_period_payment'),
            'kid_aw_money' => $this->input->post('kid_aw_money'),
            'kid_aw_note' => $this->input->post('kid_aw_note'),
            'kid_aw_by' => $this->session->userdata('m_fname') // เพิ่มชื่อคนที่แก้ไขข้อมูล
        );

        $this->db->where('kid_aw_id', $kid_aw_id);
        $this->db->update('tbl_kid_aw', $data);
        $this->space_model->update_server_current();
        $this->session->set_flashdata('save_success', TRUE);
    }

    public function del_kid_aw($kid_aw_id)
    {
        $this->db->delete('tbl_kid_aw', array('kid_aw_id' => $kid_aw_id));
        $this->space_model->update_server_current();
    }

    function get_addressbook()
    {
        $query = $this->db->get('tbl_kid_aw');
        if ($query->num_rows() > 0) {
            return $query->result_array();
        } else {
            return FALSE;
        }
    }

    function insert_csv($data)
    {
        $this->db->insert('tbl_kid_aw', $data);
    }

    public function kid_aw_frontend()
    {
        $this->db->select('*');
        $this->db->from('tbl_kid_aw');
        $this->db->where('tbl_kid_aw.kid_aw_status', 'show');
        $this->db->limit(10);
        $this->db->order_by('tbl_kid_aw.kid_aw_date', 'DESC');
        $query = $this->db->get();
        return $query->result();
    }
    public function kid_aw_frontend_list()
    {
        $this->db->select('*');
        $this->db->from('tbl_kid_aw');
        $this->db->where('tbl_kid_aw.kid_aw_status', 'show');
        $this->db->order_by('tbl_kid_aw.kid_aw_date', 'DESC');
        $query = $this->db->get();
        return $query->result();
    }
    public function read_img_kid_aw_font($kid_aw_id)
    {
        $this->db->where('kid_aw_img_ref_id', $kid_aw_id);
        $this->db->order_by('kid_aw_img_id', 'DESC');
        // $this->db->limit(4); // จำกัดจำนวนรูปเป็น 4 รูป
        $query = $this->db->get('tbl_kid_aw_img');
        return $query->result();
    }

    public function increment_kid_aw_view($kid_aw_id)
    {
        $this->db->where('kid_aw_id', $kid_aw_id);
        $this->db->set('kid_aw_view', 'kid_aw_view + 1', false); // บวกค่า kid_aw_view ทีละ 1
        $this->db->update('tbl_kid_aw');
    }
}
