<?php
class Elderly_aw_model extends CI_Model
{
    public function __construct()
    {
        parent::__construct();
        $this->load->model('space_model');
    }

    public function add_elderly_aw()
    {

        $elderly_aw_data = array(
            'elderly_aw_id_num_eligible' => $this->input->post('elderly_aw_id_num_eligible'),
            'elderly_aw_name_eligible' => $this->input->post('elderly_aw_name_eligible'),
            'elderly_aw_id_num_owner' => $this->input->post('elderly_aw_id_num_owner'),
            'elderly_aw_name_owner' => $this->input->post('elderly_aw_name_owner'),
            'elderly_aw_agency' => $this->input->post('elderly_aw_agency'),
            'elderly_aw_bank' => $this->input->post('elderly_aw_bank'),
            'elderly_aw_type_payment' => $this->input->post('elderly_aw_type_payment'),
            'elderly_aw_bank_num' => $this->input->post('elderly_aw_bank_num'),
            'elderly_aw_period_payment' => $this->input->post('elderly_aw_period_payment'),
            'elderly_aw_money' => $this->input->post('elderly_aw_money'),
            'elderly_aw_note' => $this->input->post('elderly_aw_note'),
            'elderly_aw_by' => $this->session->userdata('m_fname') // เพิ่มชื่อคนที่แก้ไขข้อมูล
        );

        $this->db->trans_start();
        $this->db->insert('tbl_elderly_aw', $elderly_aw_data);

        $this->space_model->update_server_current();

        $this->db->trans_complete();

        $this->session->set_flashdata('save_success', TRUE);
    }

    public function list()
    {
        $this->db->select('*');
        $this->db->from('tbl_elderly_aw');
        $this->db->group_by('tbl_elderly_aw.elderly_aw_id');
        $this->db->order_by('tbl_elderly_aw.elderly_aw_datesave', 'DESC');
        $query = $this->db->get();
        return $query->result();
    }

    //show form edit
    public function read_elderly_aw($elderly_aw_id)
    {
        $this->db->where('elderly_aw_id', $elderly_aw_id);
        $query = $this->db->get('tbl_elderly_aw');
        if ($query->num_rows() > 0) {
            $data = $query->row();
            return $data;
        }
        return FALSE;
    }


    public function edit_elderly_aw($elderly_aw_id)
    {

        // Update elderly_aw information
        $data = array(
            'elderly_aw_id_num_eligible' => $this->input->post('elderly_aw_id_num_eligible'),
            'elderly_aw_name_eligible' => $this->input->post('elderly_aw_name_eligible'),
            'elderly_aw_id_num_owner' => $this->input->post('elderly_aw_id_num_owner'),
            'elderly_aw_name_owner' => $this->input->post('elderly_aw_name_owner'),
            'elderly_aw_agency' => $this->input->post('elderly_aw_agency'),
            'elderly_aw_bank' => $this->input->post('elderly_aw_bank'),
            'elderly_aw_type_payment' => $this->input->post('elderly_aw_type_payment'),
            'elderly_aw_bank_num' => $this->input->post('elderly_aw_bank_num'),
            'elderly_aw_period_payment' => $this->input->post('elderly_aw_period_payment'),
            'elderly_aw_money' => $this->input->post('elderly_aw_money'),
            'elderly_aw_note' => $this->input->post('elderly_aw_note'),
            'elderly_aw_by' => $this->session->userdata('m_fname') // เพิ่มชื่อคนที่แก้ไขข้อมูล
        );

        $this->db->where('elderly_aw_id', $elderly_aw_id);
        $this->db->update('tbl_elderly_aw', $data);
        $this->space_model->update_server_current();
        $this->session->set_flashdata('save_success', TRUE);
    }

    public function del_elderly_aw($elderly_aw_id)
    {
        $this->db->delete('tbl_elderly_aw', array('elderly_aw_id' => $elderly_aw_id));
        $this->space_model->update_server_current();
    }

    function get_addressbook()
    {
        $query = $this->db->get('tbl_elderly_aw');
        if ($query->num_rows() > 0) {
            return $query->result_array();
        } else {
            return FALSE;
        }
    }

    function insert_csv($data)
    {
        $this->db->insert('tbl_elderly_aw', $data);
    }

    public function elderly_aw_frontend()
    {
        $this->db->select('*');
        $this->db->from('tbl_elderly_aw');
        $this->db->where('tbl_elderly_aw.elderly_aw_status', 'show');
        $this->db->limit(10);
        $this->db->order_by('tbl_elderly_aw.elderly_aw_date', 'DESC');
        $query = $this->db->get();
        return $query->result();
    }
    public function elderly_aw_frontend_list()
    {
        $this->db->select('*');
        $this->db->from('tbl_elderly_aw');
        $this->db->where('tbl_elderly_aw.elderly_aw_status', 'show');
        $this->db->order_by('tbl_elderly_aw.elderly_aw_date', 'DESC');
        $query = $this->db->get();
        return $query->result();
    }
    public function read_img_elderly_aw_font($elderly_aw_id)
    {
        $this->db->where('elderly_aw_img_ref_id', $elderly_aw_id);
        $this->db->order_by('elderly_aw_img_id', 'DESC');
        // $this->db->limit(4); // จำกัดจำนวนรูปเป็น 4 รูป
        $query = $this->db->get('tbl_elderly_aw_img');
        return $query->result();
    }

    public function increment_elderly_aw_view($elderly_aw_id)
    {
        $this->db->where('elderly_aw_id', $elderly_aw_id);
        $this->db->set('elderly_aw_view', 'elderly_aw_view + 1', false); // บวกค่า elderly_aw_view ทีละ 1
        $this->db->update('tbl_elderly_aw');
    }
}
