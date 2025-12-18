<?php
class Cmi_model extends CI_Model
{
    public function __construct()
    {
        parent::__construct();
        $this->load->model('space_model');
        $this->load->model('log_model');
    }

    public function add()
    {
        $ci_name = $this->input->post('ci_name');

        $existing_record = $this->db->get_where('tbl_ci', array('ci_name' => $ci_name))->row();

        if ($existing_record) {
            $this->session->set_flashdata('save_again', TRUE);
        } else {
            $data = array(
                'ci_name' => $ci_name,
                'ci_total' => $this->input->post('ci_total'),
                'ci_man' => $this->input->post('ci_man'),
                'ci_woman' => $this->input->post('ci_woman'),
                'ci_home' => $this->input->post('ci_home'),
                'ci_by' => $this->session->userdata('m_fname'),
            );

            $query = $this->db->insert('tbl_ci', $data);
            $ci_id = $this->db->insert_id();

            $this->space_model->update_server_current();
            
            $this->log_model->add_log(
                'เพิ่ม',
                'ข้อมูลชุมชน',
                $data['ci_name'],
                $ci_id,
                array(
                    'ci_info' => array(
                        'ci_total' => $data['ci_total'],
                        'ci_man' => $data['ci_man'],
                        'ci_woman' => $data['ci_woman'],
                        'ci_home' => $data['ci_home'],
                        'ci_by' => $data['ci_by'],
                    )
                )
            );

            if ($query) {
                $this->session->set_flashdata('save_success', TRUE);
            } else {
                echo "<script>";
                echo "alert('เกิดข้อผิดพลาดในการเพิ่มข้อมูลใหม่ !');";
                echo "</script>";
            }
        }
    }

    public function list_all()
    {
        $this->db->order_by('ci_id', 'DESC');
        $query = $this->db->get('tbl_ci');
        return $query->result();
    }

    public function read($ci_id)
    {
        $this->db->where('ci_id', $ci_id);
        $query = $this->db->get('tbl_ci');
        if ($query->num_rows() > 0) {
            $data = $query->row();
            return $data;
        }
        return FALSE;
    }

    public function edit($ci_id)
    {
        $old_data = $this->read($ci_id);

        $data = array(
            'ci_name' => $this->input->post('ci_name'),
            'ci_total' => $this->input->post('ci_total'),
            'ci_man' => $this->input->post('ci_man'),
            'ci_woman' => $this->input->post('ci_woman'),
            'ci_home' => $this->input->post('ci_home'),
            'ci_by' => $this->session->userdata('m_fname'),
        );

        $this->db->where('ci_id', $ci_id);
        $query = $this->db->update('tbl_ci', $data);

        $this->space_model->update_server_current();

        $this->log_model->add_log(
            'แก้ไข',
            'ข้อมูลชุมชน',
            $data['ci_name'],
            $ci_id,
            array(
                'ci_info' => array(
                    'ci_total' => $data['ci_total'],
                    'ci_man' => $data['ci_man'],
                    'ci_woman' => $data['ci_woman'],
                    'ci_home' => $data['ci_home'],
                    'ci_by' => $data['ci_by'],
                )
            )
        );

        if ($query) {
            $this->session->set_flashdata('save_success', TRUE);
        } else {
            echo "<script>";
            echo "alert('เกิดข้อผิดพลาดในการอัปเดตข้อมูล !');";
            echo "</script>";
        }
    }

    public function del_ci($ci_id)
    {
        $old_document = $this->db->get_where('tbl_ci', array('ci_id' => $ci_id))->row();

        $this->db->delete('tbl_ci', array('ci_id' => $ci_id));
        
        if ($old_document) {
            $this->log_model->add_log(
                'ลบ',
                'ข้อมูลชุมชน',
                $old_document->ci_name,
                $ci_id,
                array('deleted_date' => date('Y-m-d H:i:s'))
            );
        }
    }

    public function ci_frontend()
    {
        $this->db->select('*');
        $this->db->from('tbl_ci');
        $query = $this->db->get();
        return $query->result();
    }

    /**
     * ดึงข้อมูลสรุปประชากรทั้งหมด
     * @return object
     */
    public function get_population_summary()
    {
        $this->db->select_sum('ci_man', 'total_man');
        $this->db->select_sum('ci_woman', 'total_woman');
        $this->db->select_sum('ci_total', 'total_population');
        $query = $this->db->get('tbl_ci');
        
        $result = $query->row();
        
        // ถ้าไม่มีข้อมูลให้ return ค่า 0
        if (!$result || $result->total_population === null) {
            return (object)[
                'total_man' => 0,
                'total_woman' => 0,
                'total_population' => 0
            ];
        }
        
        return $result;
    }
}