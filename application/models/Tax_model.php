<?php
class Tax_model extends CI_Model
{
	protected $tax_db;

    public function __construct()
    {
        parent::__construct();
        $this->tax_db = $this->load->database('db_tax', TRUE);
    }
	
    public function insert_payment_tax($data)
    {
        return $this->tax_db->insert('tbl_tax_payments', $data);
    }

    public function update_payment_tax($id, $data)
    {
        return $this->tax_db->where('id', $id)->update('tbl_tax_payments', $data);
    }

    public function get_payment_by_id($id)
    {
        return $this->tax_db->where('id', $id)->get('tbl_tax_payments')->row();
    }

    public function payment_status_pending()
    {
        $this->tax_db->where('payment_status', 'pending');
        $query = $this->tax_db->get('tbl_tax_payments');
        return $query->num_rows();
    }

    public function payment_status_verified()
    {
        $this->tax_db->where('payment_status', 'verified');
        $query = $this->tax_db->get('tbl_tax_payments');
        return $query->num_rows();
    }

    public function payment_status_rejected()
    {
        $this->tax_db->where('payment_status', 'rejected');
        $query = $this->tax_db->get('tbl_tax_payments');
        return $query->num_rows();
    }

    public function payment_status_all()
    {
        return $this->tax_db->count_all('tbl_tax_payments');
    }

    public function payment_status_arrears()
    {
        $this->tax_db->where('payment_status', 'arrears');
        $query = $this->tax_db->get('tbl_tax_payments');
        return $query->num_rows();
    }

    public function get_all_payments($limit, $start, $search = null)
    {
        $this->db->select('tp.*, m.m_fname, m.m_lname');
        $this->db->from('tbl_tax_payments tp');
        $this->db->join('tbl_member m', 'm.m_id = tp.verified_by', 'left');

        if ($search) {
            $this->db->group_start();
            $this->db->like('tp.citizen_id', $search);
            $this->db->or_like('tp.firstname', $search);
            $this->db->or_like('tp.lastname', $search);
            $this->db->or_like('tp.tax_type', $search);
            $this->db->or_like('tp.admin_comment', $search); // เพิ่ม admin_comment
            $this->db->or_like('m.m_fname', $search); // เพิ่มการค้นหาชื่อผู้ตรวจสอบ
            $this->db->or_like('m.m_lname', $search); // เพิ่มการค้นหานามสกุลผู้ตรวจสอบ
            $this->db->group_end();
        }

        $this->db->order_by('tp.created_at', 'DESC');
        $this->db->limit($limit, $start);

        return $this->db->get()->result();
    }

    public function count_all_payments($search = null)
    {
        $this->db->from('tbl_tax_payments');

        if ($search) {
            $this->db->group_start();
            $this->db->like('citizen_id', $search);
            $this->db->or_like('firstname', $search);
            $this->db->or_like('lastname', $search);
            $this->db->or_like('tax_type', $search);
            $this->db->group_end();
        }

        return $this->db->count_all_results();
    }

    // public function update_payment_status($id, $status, $comment)
    // {
    //     $data = array(
    //         'payment_status' => $status,
    //         'verification_date' => date('Y-m-d H:i:s'),
    //         'admin_comment' => $comment
    //     );

    //     $this->db->where('id', $id);
    //     return $this->db->update('tbl_tax_payments', $data);
    // }


    public function get_settings()
    {
        return $this->db->where('id', 1)->get('tbl_tax_payment_settings')->row();
    }

    public function update_settings($data)
    {
        // กำหนด id = 1 เสมอ
        $data['id'] = 1;

        // เช็คว่ามีข้อมูล id = 1 หรือไม่
        $exists = $this->db->where('id', 1)->get('tbl_tax_payment_settings')->num_rows();

        if ($exists > 0) {
            // ถ้ามีแล้วให้ update
            $this->db->where('id', 1);
            $result = $this->db->update('tbl_tax_payment_settings', $data);

            // Debug
            if (!$result) {
                log_message('error', 'Update failed: ' . $this->db->error()['message']);
            }
            return $result;
        } else {
            // ถ้ายังไม่มีให้ insert
            $result = $this->db->insert('tbl_tax_payment_settings', $data);

            // Debug
            if (!$result) {
                log_message('error', 'Insert failed: ' . $this->db->error()['message']);
            }
            return $result;
        }
    }

    public function get_tax_by_citizen_id($citizen_id)
    {
        return $this->db
            ->select('tbl_tax_payments.*, firstname, lastname')
            ->from('tbl_tax_payments')
            ->where('citizen_id', $citizen_id)
            ->order_by('created_at', 'DESC')
            ->get()
            ->result();
    }

    // dashboard ---------------------
    // สถิติการชำระภาษีรายเดือน
    public function get_monthly_payments($year)
    {
        // แปลง พ.ศ. เป็น ค.ศ.
        $year_ce = $year - 543;

        $this->db->select('
            MONTH(created_at) as month,
            COUNT(*) as total_transactions,
            SUM(amount) as total_amount
        ');
        $this->db->from('tbl_tax_payments');
        $this->db->where('YEAR(created_at)', $year_ce);
        $this->db->group_by('MONTH(created_at)');
        $this->db->order_by('MONTH(created_at)', 'ASC');

        return $this->db->get()->result();
    }

    // 2. กราฟวงกลมประเภทภาษี
    public function get_payment_by_type($year)
    {
        $year_ce = $year - 543;

        $this->db->select('
            tax_type,
            COUNT(*) as total_count,
            SUM(amount) as total_amount
        ');
        $this->db->from('tbl_tax_payments');
        $this->db->where('YEAR(created_at)', $year_ce);
        $this->db->group_by('tax_type');

        return $this->db->get()->result();
    }

    // 3. กราฟวงกลมสถานะการชำระ
    public function get_payment_by_status($year)
    {
        $year_ce = $year - 543;

        $this->db->select('
            payment_status,
            COUNT(*) as total_count,
            SUM(amount) as total_amount
        ');
        $this->db->from('tbl_tax_payments');
        $this->db->where('YEAR(created_at)', $year_ce);
        $this->db->group_by('payment_status');

        return $this->db->get()->result();
    }

    // เปรียบเทียบยอดชำระภาษีแต่ละประเภท
    public function get_payment_by_type_yearly($year)
    {
        $year_ce = $year - 543;

        $this->db->select('
            tax_type,
            MONTH(created_at) as month,
            COUNT(*) as total_count,
            SUM(amount) as total_amount
        ');
        $this->db->from('tbl_tax_payments');
        $this->db->where('YEAR(created_at)', $year_ce);
        $this->db->group_by('tax_type, MONTH(created_at)');
        $this->db->order_by('MONTH(created_at), tax_type');

        return $this->db->get()->result();
    }

    // จำนวนผู้ค้างชำระแยกตามประเภท
    public function get_arrears_by_type()
    {
        $this->db->select('
            tax_type,
            COUNT(*) as total_count,
            SUM(amount) as total_amount
        ');
        $this->db->from('tbl_tax_payments');
        $this->db->where('payment_status', 'arrears');
        $this->db->group_by('tax_type');

        return $this->db->get()->result();
    }

    // ฟังก์ชันคำนวณจำนวนเงินรวมที่ชำระทั้งหมด
    public function get_total_paid_amount()
    {
        $this->db->select_sum('amount');
        $this->db->where('payment_status', 'verified');
        $query = $this->db->get('tbl_tax_payments');
        return $query->row()->amount ?? 0;
    }

    // ฟังก์ชันคำนวณจำนวนเงินรวมที่ค้างชำระ
    public function get_total_arrears_amount()
    {
        $this->db->select_sum('amount');
        $this->db->where('payment_status', 'arrears');
        $query = $this->db->get('tbl_tax_payments');
        return $query->row()->amount ?? 0;
    }

    // ฟังก์ชันคำนวณอัตราการอนุมัติ
    public function get_approval_rate()
    {
        $total = $this->db->count_all('tbl_tax_payments');
        if ($total === 0) return 0;

        $this->db->where('payment_status', 'verified');
        $approved = $this->db->count_all_results('tbl_tax_payments');

        return ($approved / $total) * 100;
    }

    // ฟังก์ชันนับจำนวนผู้ชำระภาษีที่ไม่ซ้ำกัน
    public function get_unique_taxpayers()
    {
        $this->db->distinct();
        $this->db->select('citizen_id');
        $this->db->where('citizen_id IS NOT NULL');
        return $this->db->get('tbl_tax_payments')->num_rows();
    }

    // ฟังก์ชันคำนวณยอดชำระภายในเดือนนี้
    public function get_current_month_amount()
    {
        $this->db->select_sum('amount');
        $this->db->where('payment_status', 'verified');
        $this->db->where('MONTH(created_at)', date('m'));
        $this->db->where('YEAR(created_at)', date('Y'));
        $query = $this->db->get('tbl_tax_payments');
        return $query->row()->amount ?? 0;
    }

    // ฟังก์ชันคำนวณยอดชำระภายในปีนี้
    public function get_current_year_amount()
    {
        $this->db->select_sum('amount');
        $this->db->where('payment_status', 'verified');
        $this->db->where('YEAR(created_at)', date('Y'));
        $query = $this->db->get('tbl_tax_payments');
        return $query->row()->amount ?? 0;
    }
    // -------------------------------

    public function check_and_update_payment_status() {
        // ดึงข้อมูลกำหนดวันที่ชำระ
        $due_dates = $this->db->get('tbl_tax_due_dates')->result();
        $due_dates_map = array();
        
        // ตรวจสอบว่ามีข้อมูล due_dates หรือไม่
        if (!empty($due_dates)) {
            foreach ($due_dates as $due) {
                if (isset($due->tax_type) && isset($due->due_date)) {
                    $due_dates_map[$due->tax_type] = $due->due_date;
                }
            }
        }
    
        // ดึงรายการภาษีที่ต้องจ่ายหรือค้างชำระ
        $payments = $this->db->where_in('payment_status', ['required', 'arrears'])
                            ->get('tbl_tax_payments')
                            ->result();
    
        // ตรวจสอบว่ามีข้อมูล payments หรือไม่
        if (empty($payments)) {
            return false;
        }
    
        foreach ($payments as $payment) {
            // ตรวจสอบว่ามี tax_type ในแผนที่หรือไม่
            if (!isset($due_dates_map[$payment->tax_type])) {
                continue;
            }
    
            // ตรวจสอบว่ามีรูปแบบวันที่ถูกต้อง
            $due_date_parts = explode('-', $due_dates_map[$payment->tax_type]);
            if (count($due_date_parts) !== 2) {
                continue;
            }
    
            $due_day = $due_date_parts[0];
            $due_month = $due_date_parts[1];
    
            // ตรวจสอบความถูกต้องของวันที่
            if (!is_numeric($due_day) || !is_numeric($due_month) || 
                $due_day < 1 || $due_day > 31 || $due_month < 1 || $due_month > 12) {
                continue;
            }
    
            // สร้างวันที่ครบกำหนด
            try {
                $tax_year = (int)$payment->tax_year - 543; // แปลง พ.ศ. เป็น ค.ศ.
                $due_date = sprintf('%d-%02d-%02d', $tax_year, $due_month, $due_day);
                $due_timestamp = strtotime($due_date);
                
                if ($due_timestamp === false) {
                    continue;
                }
    
                // ถ้าวันปัจจุบันเกินกำหนด
                if (time() > $due_timestamp) {
                    $new_status = 'arrears';
    
                    // คำนวณค่าปรับ
                    $this->load->model('tax_penalty_model');
                    $penalty = $this->tax_penalty_model->calculate_late_payment_penalty($payment->id);
    
                    if ($penalty && isset($penalty['penalty_amount'])) {
                        // อัพเดตข้อมูล
                        $update_data = [
                            'payment_status' => $new_status,
                            'penalty_amount' => $penalty['penalty_amount'],
                            'total_amount' => $payment->amount + $penalty['penalty_amount']
                        ];
    
                        $this->db->where('id', $payment->id)
                                ->update('tbl_tax_payments', $update_data);
                    }
                }
            } catch (Exception $e) {
                log_message('error', 'Error in check_and_update_payment_status: ' . $e->getMessage());
                continue;
            }
        }
    
        return true;
    }

    public function count_payments_by_type($tax_type, $search = null)
    {
        $this->db->from('tbl_tax_payments');
        $this->db->where('tax_type', $tax_type);

        if ($search) {
            $this->db->group_start();
            $this->db->like('citizen_id', $search);
            $this->db->or_like('firstname', $search);
            $this->db->or_like('lastname', $search);
            $this->db->group_end();
        }

        return $this->db->count_all_results();
    }

    public function get_payments_by_type($tax_type, $limit, $start, $search = null)
    {
        $this->db->select('tp.*, m.m_fname, m.m_lname');
        $this->db->from('tbl_tax_payments tp');
        $this->db->join('tbl_member m', 'm.m_id = tp.verified_by', 'left');
        $this->db->where('tp.tax_type', $tax_type);

        if ($search) {
            $this->db->group_start();
            $this->db->like('tp.citizen_id', $search);
            $this->db->or_like('tp.firstname', $search);
            $this->db->or_like('tp.lastname', $search);
            $this->db->group_end();
        }

        $this->db->order_by('tp.created_at', 'DESC');
        $this->db->limit($limit, $start);

        return $this->db->get()->result();
    }

    public function insert_signboard_detail($data)
    {
        return $this->db->insert('tbl_tax_signboard_details', $data);
    }

    public function get_signboard_details($payment_id)
    {
        return $this->tax_db->where('payment_id', $payment_id)
            ->get('tbl_tax_signboard_details')
            ->result();
    }

    public function update_signboard_detail($id, $data)
    {
        return $this->db->where('id', $id)->update('tbl_tax_signboard_details', $data);
    }

    public function delete_signboard_detail($id)
{
    return $this->db->where('id', $id)->delete('tbl_tax_signboard_details');
}
}
