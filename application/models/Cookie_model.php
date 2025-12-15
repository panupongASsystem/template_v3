<?php
defined('BASEPATH') or exit('No direct script access allowed');

class Cookie_model extends CI_Model
{

    private $table = 'tbl_cookie';

    public function save_consent($data)
    {
        $result = $this->db->insert($this->table, $data);

        // Debug
        if (!$result) {
            log_message('error', 'Database Error: ' . print_r($this->db->error(), true));
        }

        return $result;
    }

    public function get_consent($session_id)
    {
        return $this->db->get_where($this->table, ['session_id' => $session_id])->row();
    }

    public function update_consent($session_id, $data)
    {
        return $this->db->update($this->table, $data, ['session_id' => $session_id]);
    }
}
