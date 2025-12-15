<?php
class Tax_user_log_model extends CI_Model
{
  // เก็บ log user ------------------
  public function insert_log($data)
  {
      return $this->db->insert('tbl_tax_user_logs', $data);
  }

  public function get_user_logs($user_id = null, $start_date = null, $end_date = null)
  {
      if ($user_id) {
          $this->db->where('user_id', $user_id);
      }

      if ($start_date && $end_date) {
          $this->db->where('action_time >=', $start_date . ' 00:00:00');
          $this->db->where('action_time <=', $end_date . ' 23:59:59');
      }

      $this->db->order_by('action_time', 'DESC');
      return $this->db->get('tbl_tax_user_logs')->result();
  }

  // อัพเดท method ให้รองรับทั้ง member และ member_public
  public function get_user_logs_with_details($limit, $start, $search = null, $user_id = null, $start_date = null, $end_date = null, $user_type = null)
  {
      $this->db->select('
          ul.*,
          CASE 
              WHEN mp.mp_fname IS NOT NULL THEN CONCAT(mp.mp_fname, " ", mp.mp_lname)
              WHEN m.m_fname IS NOT NULL THEN CONCAT(m.m_fname, " ", m.m_lname)
          END as user_name');
      $this->db->from('tbl_tax_user_logs ul');
      $this->db->join('tbl_member_public mp', 'ul.user_id = mp.mp_id AND ul.user_type = "member_public"', 'left');
      $this->db->join('tbl_member m', 'ul.user_id = m.m_id AND ul.user_type = "member"', 'left');

      // เงื่อนไขการค้นหา
      if ($search) {
          $this->db->group_start();
          $this->db->like('mp.mp_fname', $search);
          $this->db->or_like('mp.mp_lname', $search);
          $this->db->or_like('m.m_fname', $search);
          $this->db->or_like('m.m_lname', $search);
          $this->db->or_like('ul.ip_address', $search);
          $this->db->group_end();
      }

      if ($user_id) {
          $this->db->where('ul.user_id', $user_id);
      }

      if ($user_type) {
          $this->db->where('ul.user_type', $user_type);
      }

      if ($start_date && $end_date) {
          $this->db->where('ul.action_time >=', $start_date . ' 00:00:00');
          $this->db->where('ul.action_time <=', $end_date . ' 23:59:59');
      }

      $this->db->order_by('ul.action_time', 'DESC');
      $this->db->limit($limit, $start);

      return $this->db->get()->result();
  }

  public function count_all_logs($search = null, $user_id = null, $start_date = null, $end_date = null, $user_type = null)
  {
      $this->db->from('tbl_tax_user_logs ul');
      $this->db->join('tbl_member_public mp', 'ul.user_id = mp.mp_id AND ul.user_type = "member_public"', 'left');
      $this->db->join('tbl_member m', 'ul.user_id = m.m_id AND ul.user_type = "member"', 'left');

      if ($search) {
          $this->db->group_start();
          $this->db->like('mp.mp_fname', $search);
          $this->db->or_like('mp.mp_lname', $search);
          $this->db->or_like('m.m_fname', $search);
          $this->db->or_like('m.m_lname', $search);
          $this->db->or_like('ul.ip_address', $search);
          $this->db->group_end();
      }

      if ($user_id) {
          $this->db->where('ul.user_id', $user_id);
      }

      if ($user_type) {
          $this->db->where('ul.user_type', $user_type);
      }

      if ($start_date && $end_date) {
          $this->db->where('ul.action_time >=', $start_date . ' 00:00:00');
          $this->db->where('ul.action_time <=', $end_date . ' 23:59:59');
      }

      return $this->db->count_all_results();
  }

  public function get_last_login($user_id, $user_type = 'member_public')
  {
      $this->db->where('user_id', $user_id);
      $this->db->where('user_type', $user_type);
      $this->db->where('action', 'login');
      $this->db->order_by('action_time', 'DESC');
      $this->db->limit(1);
      return $this->db->get('tbl_tax_user_logs')->row();
  }
  //--------------------------------
}
