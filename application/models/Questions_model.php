<?php
class Questions_model extends CI_Model
{
    public function __construct()
    {
        parent::__construct();
        $this->load->model('space_model');
    }

    // เช็คข้อมูลซ้ำ
    public function add()
    {
        $questions_ask = $this->input->post('questions_ask');
        $this->db->select('questions_ask');
        $this->db->where('questions_ask', $questions_ask);
        $query = $this->db->get('tbl_questions');
        $num = $query->num_rows();
        if ($num > 0) {
            $this->session->set_flashdata('save_again', TRUE);
        } else {
            $data = array(
                'questions_ask' => $this->input->post('questions_ask'),
                'questions_reply' => $this->input->post('questions_reply'),
                'questions_by' => $this->session->userdata('m_fname'), // เพิ่มชื่อคนที่เพิ่มข้อมูล
            );
            $query = $this->db->insert('tbl_questions', $data);

            $this->session->set_flashdata('save_success', TRUE);
        }
    }


    public function list()
    {
        $this->db->select('*');
        $this->db->from('tbl_questions');
        $this->db->order_by('tbl_questions.questions_id', 'DESC');
        $query = $this->db->get();
        return $query->result();
    }

    public function read($questions_id)
    {
        $this->db->select('*');
        $this->db->from('tbl_questions');
        $this->db->where('tbl_questions.questions_id', $questions_id);
        $query = $this->db->get();
        if ($query->num_rows() > 0) {
            $data = $query->row();
            return $data;
        }
        return false;
    }

    public function edit($questions_id)
    {
        $questions_ask = $this->input->post('questions_ask');

        // Check if the new questions_com value is not already in the database for other records.
        $this->db->where('questions_ask', $questions_ask);
        $this->db->where_not_in('questions_id', $questions_id); // Exclude the current record being edited.
        $query = $this->db->get('tbl_questions');
        $num = $query->num_rows();

        if ($num > 0) {
            // A record with the same questions_com already exists in the database.
            $this->session->set_flashdata('save_again', TRUE);
        } else {
            // Update the record.
            $data = array(
                'questions_ask' => $this->input->post('questions_ask'),
                'questions_reply' => $this->input->post('questions_reply'),
                'questions_by' => $this->session->userdata('m_fname'),
            );

            $this->db->where('questions_id', $questions_id);
            $this->db->update('tbl_questions', $data);

            $this->space_model->update_server_current();
            $this->session->set_flashdata('save_success', TRUE);
        }
    }


    public function del($questions_id)
    {
        $this->db->delete('tbl_questions', array('questions_id' => $questions_id));
    }
}
