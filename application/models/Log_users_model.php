<?php
class Log_users_model extends CI_Model
{

    public function countOnlineUsers()
    {
        $this->db->where('timestamp >', time() - 300); // 300 วินาที (5 นาที) คือเวลาที่คิดว่าคนถือว่ายังคงออนไลน์
        return $this->db->count_all_results('tbl_sessions');
    }
    public function countAllUsers()
    {
        return $this->db->count_all('tbl_sessions');
    }
    public function countUsersThisYear()
    {
        $startOfYear = strtotime(date('Y-01-01 00:00:00'));
        $this->db->where('timestamp >=', $startOfYear);
        return $this->db->count_all_results('tbl_sessions');
    }
    public function countUsersThisMonth()
    {
        $startOfMonth = strtotime(date('Y-m-01 00:00:00'));
        $this->db->where('timestamp >=', $startOfMonth);
        return $this->db->count_all_results('tbl_sessions');
    }
    public function countUsersThisWeek()
    {
        $this->db->select('YEAR(FROM_UNIXTIME(timestamp)) AS year, WEEK(FROM_UNIXTIME(timestamp)) AS week_number, COUNT(*) AS user_count');
        $this->db->from('tbl_sessions');
        $this->db->group_by('year, week_number');
        $query = $this->db->get();
        return $query->result();
    }

    public function countUsersToday()
    {
        $startOfDay = strtotime(date('Y-m-d 00:00:00'));
        $this->db->where('timestamp >=', $startOfDay);
        return $this->db->count_all_results('tbl_sessions');
    }

    public function countUsersPerDay()
    {
        $result = array();

        for ($day = 1; $day <= 30; $day++) {
            $this->db->select('COUNT(*) as count');
            $this->db->from('tbl_sessions');
            $this->db->where('DAY(FROM_UNIXTIME(timestamp))', $day);
            $this->db->where('MONTH(FROM_UNIXTIME(timestamp))', date('m'));
            $query = $this->db->get();
            $row = $query->row();
            $result[$day] = ($row) ? $row->count : 0;
        }

        return $result;
    }

    // ในโมเดล
    public function countUsersEachDayInCurrentMonth()
    {
        // หาวันแรกของเดือนปัจจุบัน
        $firstDayOfMonth = date('Y-m-01 00:00:00');
        $startOfDay = strtotime($firstDayOfMonth);

        // หาวันสุดท้ายของเดือนปัจจุบัน
        $lastDayOfMonth = date('Y-m-t 23:59:59');
        $endOfDay = strtotime($lastDayOfMonth);

        $this->db->select('DATE(FROM_UNIXTIME(timestamp)) AS day, COUNT(DISTINCT id) AS user_count', FALSE);
        $this->db->from('tbl_sessions');
        $this->db->where('timestamp >=', $startOfDay);
        $this->db->where('timestamp <=', $endOfDay);
        $this->db->group_by('day');
        $query = $this->db->get();

        return $query->result_array();
    }
}
