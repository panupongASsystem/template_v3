<?php
defined('BASEPATH') or exit('No direct script access allowed');

class Tax_penalty_model extends CI_Model
{
	    protected $tax_db;

    public function __construct()
    {
        parent::__construct();
        $this->tax_db = $this->load->database('db_tax', TRUE);
    }

    public function get_lan_tax_penalty_settings($tax_type = 'land')
    {
        return $this->tax_db->where('tax_type', $tax_type)->get('tbl_land_tax_penalty_settings')->row();
    }

    public function update_lan_tax_penalty_settings($tax_type, $data)
    {
        $this->tax_db->where('tax_type', $tax_type);
        return $this->tax_db->update('tbl_land_tax_penalty_settings', $data);
    }

     public function calculate_late_payment_penalty()
    {
        // ดึงข้อมูลวันครบกำหนดของภาษีที่ดิน
        $due_date = $this->tax_db->where('tax_type', 'land')
            ->get('tbl_tax_due_dates')
            ->row();
        if (!$due_date) {
            return false;
        }

        // ดึงการตั้งค่าค่าปรับ
        $settings = $this->get_lan_tax_penalty_settings('land');
        if (!$settings) {
            return false;
        }

        // ดึงรายการภาษีที่ดินที่ต้องจ่ายหรือค้างชำระ
        $payments = $this->tax_db->where('tax_type', 'land')
            ->where_not_in('payment_status', ['verified', 'pending'])
            ->get('tbl_tax_payments')
            ->result();

        if (empty($payments)) {
            return false;
        }

        foreach ($payments as $payment) {
            try {
                $penalty_amount = 0;
                $penalty_percent = 0;
                $months_late = 0;

                // กรณีไม่ยื่นแบบ
                if ($payment->filing_status == 'no_filing') {
                    $penalty_amount = $settings->no_filing_fine;
                }
                // กรณียื่นแบบไม่ถูกต้อง
                else if ($payment->filing_status == 'incorrect') {
                    $penalty_amount = $settings->incorrect_filing_fine;
                }

                // คำนวณเงินเพิ่มกรณีชำระเกินกำหนด
                $formatted_due_date = $this->get_due_date($payment->tax_year, $due_date->due_date);
                $due_date_full = date('Y-m-d', strtotime($formatted_due_date . ' +30 days'));
                $current_date = date('Y-m-d');

                if (strtotime($current_date) > strtotime($due_date_full)) {
                    $months_late = $this->calculate_months_between($due_date_full, $current_date);

                    if ($months_late > 4) {
                        // เกิน 4 เดือนขึ้นไป - ต้องยึดทรัพย์
                        $penalty_percent = 10;
                        $late_penalty = ($payment->amount * $penalty_percent) / 100;
                    } else {
                        // คำนวณตามช่วงเดือน
                        $penalty_percent = $this->get_penalty_percentage($months_late, $settings);
                        $late_penalty = ($payment->amount * $penalty_percent) / 100;
                    }
                    $penalty_amount += $late_penalty;

                    // อัพเดตข้อมูล
                    $update_data = [
                        'payment_status' => 'arrears',
                        'penalty_amount' => $penalty_amount,
                        'total_amount' => $payment->amount + $penalty_amount
                    ];

                    $this->tax_db->where('id', $payment->id)
                        ->update('tbl_tax_payments', $update_data);

                    // บันทึกประวัติค่าปรับ
                    $penalty_data = [
                        'payment_id' => $payment->id,
                        'tax_type' => 'land',
                        'penalty_type' => 'late_payment',
                        'base_amount' => $payment->amount,
                        'penalty_amount' => $penalty_amount,
                        'penalty_percent' => $penalty_percent,
                        'months_late' => $months_late,
                        'requires_seizure' => $months_late > 4,
                        'created_by' => $this->session->userdata('m_id')
                    ];

                    $this->tax_db->insert('tbl_tax_penalties', $penalty_data);
                } else {
                    // ถ้ายังไม่เกินกำหนด รีเซ็ตค่าปรับ
                    $update_data = [
                        'payment_status' => 'required',
                        'penalty_amount' => NULL,
                        'total_amount' => $payment->amount
                    ];

                    $this->tax_db->where('id', $payment->id)
                        ->update('tbl_tax_payments', $update_data);

                    // ลบประวัติค่าปรับ (ถ้ามี)
                    $this->tax_db->where('payment_id', $payment->id)
                        ->where('penalty_type', 'late_payment')
                        ->delete('tbl_tax_penalties');
                }
            } catch (Exception $e) {
                log_message('error', 'Error calculating land tax penalty: ' . $e->getMessage());
                continue;
            }
        }

        return true;
    }

    // เพิ่มฟังก์ชันบันทึกประวัติ
    private function save_penalty_history($payment_id, $penalty_data)
    {
        // ดึงข้อมูลการชำระเงิน
        $payment = $this->tax_db->get_where('tbl_tax_payments', ['id' => $payment_id])->row();

        // เช็คว่ามี penalty history อยู่แล้วหรือไม่
        $existing_penalty = $this->tax_db->get_where('tbl_tax_penalties', [
            'payment_id' => $payment_id,
            'penalty_type' => 'late_payment'
        ])->row();

        $data = [
            'payment_id' => $payment_id,
            'tax_type' => 'land',
            'penalty_type' => 'late_payment',
            'base_amount' => $payment->amount,
            'penalty_amount' => $penalty_data['penalty_amount'],
            'penalty_percent' => $penalty_data['penalty_percent'],
            'months_late' => $penalty_data['months_late'],
            'created_at' => date('Y-m-d H:i:s')
        ];

        if ($existing_penalty) {
            // ถ้ามีข้อมูลอยู่แล้ว ให้ update
            return $this->tax_db->where('id', $existing_penalty->id)
                ->update('tbl_tax_penalties', $data);
        } else {
            // ถ้ายังไม่มีข้อมูล ให้ insert
            return $this->tax_db->insert('tbl_tax_penalties', $data);
        }
    }
    public function can_collect_backdate($tax_type, $filing_status, $tax_year)
    {
        $settings = $this->get_lan_tax_penalty_settings($tax_type);
        if (!$settings) return false;

        $current_year = date('Y') + 543; // แปลงเป็น พ.ศ.
        $years_diff = $current_year - $tax_year;

        if ($filing_status == 'no_filing') {
            return $years_diff <= $settings->backdate_years_no_filing;
        } else if ($filing_status == 'incorrect') {
            return $years_diff <= $settings->backdate_years_incorrect;
        }

        return true;
    }

    private function calculate_months_between($start_date, $end_date)
    {
        $start = new DateTime($start_date);
        $end = new DateTime($end_date);
        $interval = $start->diff($end);
        return ($interval->y * 12) + $interval->m + ($interval->d > 0 ? 1 : 0);
    }

    private function get_penalty_percentage($months_late, $settings)
    {
        if ($months_late <= 1) return $settings->late_payment_1month;
        if ($months_late <= 2) return $settings->late_payment_2month;
        if ($months_late <= 3) return $settings->late_payment_3month;
        if ($months_late <= 4) return $settings->late_payment_4month;
        return $settings->late_payment_4month; // Maximum penalty
    }

    private function get_due_date($tax_year, $due_date)
    {
        list($day, $month) = explode('-', $due_date);
        // Convert Buddhist year to CE
        $year = $tax_year - 543;
        $formatted_date = sprintf('%d-%02d-%02d', $year, $month, $day);

        // Debug log
        log_message('debug', "Converting tax year from BE to CE");
        log_message('debug', "Original tax year (BE): {$tax_year}");
        log_message('debug', "Converted tax year (CE): {$year}");
        log_message('debug', "Formatted due date: {$formatted_date}");

        return $formatted_date;
    }

    public function calculate_no_filing_penalty($payment_id)
    {
        $payment = $this->tax_db->get_where('tbl_tax_payments', ['id' => $payment_id])->row();
        if (!$payment) return false;

        $settings = $this->get_lan_tax_penalty_settings($payment->tax_type);
        if (!$settings) return false;

        $penalty_data = [
            'payment_id' => $payment_id,
            'penalty_type' => 'no_filing',
            'base_amount' => $payment->amount,
            'penalty_amount' => $settings->no_filing_fine,
            'created_by' => $this->session->userdata('m_id')
        ];

        $this->tax_db->insert('tbl_tax_penalties', $penalty_data);

        return $settings->no_filing_fine;
    }

    public function calculate_incorrect_filing_penalty($payment_id)
    {
        $payment = $this->tax_db->get_where('tbl_tax_payments', ['id' => $payment_id])->row();
        if (!$payment) return false;

        $settings = $this->get_lan_tax_penalty_settings($payment->tax_type);
        if (!$settings) return false;

        $penalty_data = [
            'payment_id' => $payment_id,
            'penalty_type' => 'incorrect_filing',
            'base_amount' => $payment->amount,
            'penalty_amount' => $settings->incorrect_filing_fine,
            'created_by' => $this->session->userdata('m_id')
        ];

        $this->tax_db->insert('tbl_tax_penalties', $penalty_data);

        return $settings->incorrect_filing_fine;
    }

    public function get_penalties_by_payment($payment_id)
    {
        return $this->tax_db->where('payment_id', $payment_id)->get('tbl_tax_penalties')->result();
    }

    public function get_total_penalty_amount($payment_id)
    {
        $result = $this->tax_db->select_sum('penalty_amount')
            ->where('payment_id', $payment_id)
            ->get('tbl_tax_penalties')
            ->row();
        return $result ? $result->penalty_amount : 0;
    }

    // เพิ่มฟังก์ชันสำหรับอัพเดตยอดรวมทั้งหมด
    public function update_payment_total($payment_id)
    {
        // ดึงข้อมูลการชำระเงิน
        $payment = $this->tax_db->get_where('tbl_tax_payments', ['id' => $payment_id])->row();
        if (!$payment) return false;

        // คำนวณค่าปรับทั้งหมด
        $total_penalty = $this->get_total_penalty_amount($payment_id);

        // คำนวณยอดรวมทั้งหมด
        $total_amount = $payment->amount + $total_penalty;

        // อัพเดตตาราง payments
        return $this->tax_db->where('id', $payment_id)->update('tbl_tax_payments', [
            'total_amount' => $total_amount,
            'penalty_amount' => $total_penalty
        ]);
    }

    // ตั้งค่าการปรับภาษีท้องถิ่น ------------
    // ดึงการตั้งค่าที่ active
    public function get_local_tax_settings()
    {
        return $this->tax_db->where('is_active', 1)
            ->get('tbl_local_tax_penalty_settings')
            ->row();
    }

    // อัพเดตการตั้งค่า
    public function update_local_tax_settings($data)
    {
        $data['updated_by'] = $this->session->userdata('m_id');

        // หา setting ที่ active
        $current_setting = $this->get_local_tax_settings();

        if ($current_setting) {
            return $this->tax_db->where('id', $current_setting->id)
                ->update('tbl_local_tax_penalty_settings', $data);
        } else {
            $data['is_active'] = 1;
            return $this->tax_db->insert('tbl_local_tax_penalty_settings', $data);
        }
    }

    // ดึงประวัติการตั้งค่า
    public function get_local_tax_settings_history($limit = 10)
    {
        return $this->tax_db->select('tps.*, m.m_fname, m.m_lname')
            ->from('tbl_local_tax_penalty_settings tps')
            ->join('tbl_member m', 'm.m_id = tps.updated_by', 'left')
            ->order_by('tps.updated_at', 'DESC')
            ->limit($limit)
            ->get()
            ->result();
    }

    // public function calculate_local_tax_penalties($payment_id)
    // {
    //     // ดึงข้อมูลการชำระเงิน
    //     $payment = $this->tax_db->get_where('tbl_tax_payments', ['id' => $payment_id])->row();
    //     if (!$payment || $payment->tax_type !== 'local') {
    //         return false;
    //     }

    //     // ดึงการตั้งค่าค่าปรับ
    //     $settings = $this->get_local_tax_settings();
    //     if (!$settings) {
    //         return false;
    //     }

    //     $penalties = [];
    //     $total_penalty = 0;

    //     // 1. ไม่ยื่นแบบภายในกำหนด
    //     if ($payment->filing_status === 'no_filing') {
    //         $penalty_amount = ($payment->amount * $settings->no_filing_percent) / 100;
    //         $penalties['no_filing'] = [
    //             'type' => 'no_filing',
    //             'description' => 'ไม่ยื่นแบบภายในกำหนด',
    //             'base_amount' => $payment->amount,
    //             'rate' => $settings->no_filing_percent . '%',
    //             'amount' => $penalty_amount
    //         ];
    //         $total_penalty += $penalty_amount;
    //     }

    //     // 2. ยื่นรายการไม่ถูกต้อง
    //     if ($payment->filing_status === 'incorrect') {
    //         $additional_tax = $payment->additional_tax ?? 0;
    //         $penalty_amount = ($additional_tax * $settings->incorrect_filing_percent) / 100;
    //         $penalties['incorrect_filing'] = [
    //             'type' => 'incorrect_filing',
    //             'description' => 'ยื่นรายการไม่ถูกต้อง',
    //             'base_amount' => $additional_tax,
    //             'rate' => $settings->incorrect_filing_percent . '%',
    //             'amount' => $penalty_amount
    //         ];
    //         $total_penalty += $penalty_amount;
    //     }

    //     // 3. ชี้เขตแจ้งจำนวนเนื้อที่ดินไม่ถูกต้อง
    //     if ($payment->area_status === 'incorrect') {
    //         $additional_tax = $payment->additional_tax ?? 0;
    //         $penalty_amount = $additional_tax * $settings->incorrect_area_multiplier;
    //         $penalties['incorrect_area'] = [
    //             'type' => 'incorrect_area',
    //             'description' => 'ชี้เขตแจ้งจำนวนเนื้อที่ดินไม่ถูกต้อง',
    //             'base_amount' => $additional_tax,
    //             'rate' => $settings->incorrect_area_multiplier . ' เท่า',
    //             'amount' => $penalty_amount
    //         ];
    //         $total_penalty += $penalty_amount;
    //     }

    //     // 4. ชำระภาษีเกินกำหนด (30 เมษายน)
    //     if ($payment->payment_date) {
    //         $due_date = $payment->tax_year - 543 . '-04-30'; // แปลงปี พ.ศ. เป็น ค.ศ.
    //         if (strtotime($payment->payment_date) > strtotime($due_date)) {
    //             $years_late = $this->calculate_years_late($due_date, $payment->payment_date);
    //             $penalty_rate = $settings->late_payment_yearly_percent * $years_late;
    //             $penalty_amount = ($payment->amount * $penalty_rate) / 100;

    //             $penalties['late_payment'] = [
    //                 'type' => 'late_payment',
    //                 'description' => 'ชำระภาษีเกินกำหนด',
    //                 'base_amount' => $payment->amount,
    //                 'years_late' => number_format($years_late, 2),
    //                 'rate' => $settings->late_payment_yearly_percent . '% ต่อปี',
    //                 'total_rate' => $penalty_rate . '%',
    //                 'amount' => $penalty_amount
    //             ];
    //             $total_penalty += $penalty_amount;
    //         }
    //     }

    //     // บันทึกค่าปรับ
    //     $this->save_penalty_calculation($payment_id, $penalties, $total_penalty);

    //     return [
    //         'payment_id' => $payment_id,
    //         'base_amount' => $payment->amount,
    //         'penalties' => $penalties,
    //         'total_penalty' => $total_penalty,
    //         'total_amount' => $payment->amount + $total_penalty
    //     ];
    // }

    // private function save_penalty_calculation($payment_id, $penalties, $total_penalty)
    // {
    //     // บันทึกรายการค่าปรับแต่ละประเภท
    //     foreach ($penalties as $penalty) {
    //         $penalty_data = [
    //             'payment_id' => $payment_id,
    //             'tax_type' => 'local',
    //             'penalty_type' => $penalty['type'],
    //             'base_amount' => $penalty['base_amount'],
    //             'penalty_amount' => $penalty['amount'],
    //             'penalty_percent' => isset($penalty['rate']) ? floatval(str_replace('%', '', $penalty['rate'])) : null,
    //             'months_late' => isset($penalty['years_late']) ? ($penalty['years_late'] * 12) : null,
    //             'created_by' => $this->session->userdata('m_id')
    //         ];

    //         $this->tax_db->insert('tbl_tax_penalties', $penalty_data);
    //     }

    //     // อัพเดตข้อมูลในตารางการชำระเงิน
    //     return $this->tax_db->where('id', $payment_id)
    //         ->update('tbl_tax_payments', [
    //             'penalty_amount' => $total_penalty,
    //             'total_amount' => $this->tax_db->select('amount')
    //                 ->where('id', $payment_id)
    //                 ->get('tbl_tax_payments')
    //                 ->row()
    //                 ->amount + $total_penalty,
    //             'updated_at' => date('Y-m-d H:i:s'),
    //             'updated_by' => $this->session->userdata('m_id')
    //         ]);
    // }

    public function calculate_local_tax_penalties()
    {
        // ดึงข้อมูลกำหนดวันที่ชำระสำหรับภาษีท้องถิ่น
        $due_date = $this->tax_db->where('tax_type', 'local')
            ->get('tbl_tax_due_dates')
            ->row();
        if (!$due_date) {
            return false;
        }

        // ดึงการตั้งค่าค่าปรับ
        $settings = $this->tax_db->get('tbl_local_tax_penalty_settings')->row();
        if (!$settings) {
            return false;
        }

        // ดึงรายการภาษีท้องถิ่นที่ต้องจ่ายหรือค้างชำระ
        $payments = $this->tax_db->where('tax_type', 'local')
            ->where_not_in('payment_status', 'verified')
            ->get('tbl_tax_payments')
            ->result();

        if (empty($payments)) {
            return false;
        }

        // แยกวันที่ครบกำหนด
        $due_date_parts = explode('-', $due_date->due_date);
        if (count($due_date_parts) !== 2) {
            return false;
        }

        $due_day = $due_date_parts[0];
        $due_month = $due_date_parts[1];

        foreach ($payments as $payment) {
            try {
                // สร้างวันที่ครบกำหนด
                $tax_year = (int)$payment->tax_year - 543; // แปลง พ.ศ. เป็น ค.ศ.
                $full_due_date = sprintf('%d-%02d-%02d', $tax_year, $due_month, $due_day);
                $due_timestamp = strtotime($full_due_date);

                if ($due_timestamp === false) {
                    continue;
                }

                // ถ้าวันปัจจุบันเกินกำหนด
                if (time() > $due_timestamp) {
                    $years_late = $this->calculate_years_late($full_due_date, date('Y-m-d'));
                    $penalty_rate = $settings->late_payment_yearly_percent * $years_late;
                    $penalty_amount = ($payment->amount * $penalty_rate) / 100;

                    // อัพเดตข้อมูล
                    $update_data = [
                        'payment_status' => 'arrears',
                        'penalty_amount' => $penalty_amount,
                        'total_amount' => $payment->amount + $penalty_amount
                    ];

                    $this->tax_db->where('id', $payment->id)
                        ->update('tbl_tax_payments', $update_data);

                    // บันทึกประวัติค่าปรับ
                    $penalty_data = [
                        'payment_id' => $payment->id,
                        'tax_type' => 'local',
                        'penalty_type' => 'late_payment',
                        'base_amount' => $payment->amount,
                        'penalty_amount' => $penalty_amount,
                        'penalty_percent' => $penalty_rate,
                        'months_late' => $years_late * 12,
                        'created_by' => $this->session->userdata('m_id')
                    ];

                    $this->tax_db->insert('tbl_tax_penalties', $penalty_data);
                } else {
                    // ถ้ายังไม่เกินกำหนด รีเซ็ตค่าปรับเป็น 0 หรือ null
                    $update_data = [
                        'payment_status' => 'required',
                        'penalty_amount' => NULL,
                        'total_amount' => $payment->amount  // รีเซ็ตยอดรวมให้เท่ากับยอดภาษี
                    ];

                    $this->tax_db->where('id', $payment->id)
                        ->update('tbl_tax_payments', $update_data);

                    // ลบประวัติค่าปรับ (ถ้ามี)
                    $this->tax_db->where('payment_id', $payment->id)
                        ->where('penalty_type', 'late_payment')
                        ->delete('tbl_tax_penalties');
                }
            } catch (Exception $e) {
                log_message('error', 'Error calculating local tax penalty: ' . $e->getMessage());
                continue;
            }
        }

        return true;
    }

    public function cancel_local_tax_penalty($payment_id, $data)
    {
        // บันทึกประวัติการยกเลิก
        $cancel_data = [
            'payment_id' => $payment_id,
            'cancel_reason' => $data['cancel_reason'],
            'cancelled_by' => $data['cancelled_by'],
            'cancelled_at' => $data['cancelled_at']
        ];
        $this->tax_db->insert('tbl_tax_penalty_cancellations', $cancel_data);

        // รีเซ็ตค่าปรับในตารางการชำระเงิน
        return $this->tax_db->where('id', $payment_id)
            ->update('tbl_tax_payments', [
                'penalty_amount' => 0,
                'total_amount' => $this->tax_db->select('amount')->where('id', $payment_id)->get('tbl_tax_payments')->row()->amount,
                'updated_at' => date('Y-m-d H:i:s'),
                'updated_by' => $data['cancelled_by']
            ]);
    }

    private function calculate_years_late($due_date, $payment_date)
    {
        $due = new DateTime($due_date);
        $payment = new DateTime($payment_date);
        $diff = $payment->diff($due);
        return $diff->y + ($diff->m / 12) + ($diff->d / 365);
    }
    // --------------------------------

    // ตั้งค่าการปรับภาษีป้าย ------------
    public function get_signboard_tax_settings()
    {
        return $this->tax_db->where('is_active', 1)
            ->get('tbl_signboard_tax_penalty_settings')
            ->row();
    }

    public function get_signboard_tax_settings_history($limit = 10)
    {
        return $this->tax_db->select('tps.*, m.m_fname, m.m_lname')
            ->from('tbl_signboard_tax_penalty_settings tps')
            ->join('tbl_member m', 'm.m_id = tps.updated_by', 'left')
            ->order_by('tps.updated_at', 'DESC')
            ->limit($limit)
            ->get()
            ->result();
    }

    // อัพเดตการตั้งค่าภาษีป้าย
    public function update_signboard_tax_settings($data)
    {
        $data['updated_by'] = $this->session->userdata('m_id');

        // ตรวจสอบว่ามีข้อมูลอยู่แล้วหรือไม่
        $existing = $this->tax_db->where('is_active', 1)
            ->get('tbl_signboard_tax_penalty_settings')
            ->row();

        if ($existing) {
            return $this->tax_db->where('id', $existing->id)
                ->update('tbl_signboard_tax_penalty_settings', $data);
        } else {
            $data['is_active'] = 1;
            return $this->tax_db->insert('tbl_signboard_tax_penalty_settings', $data);
        }
    }

    // คำนวณค่าปรับกรณีไม่ยื่นแบบภายในกำหนด (เดือนมีนาคม)
    public function calculate_signboard_no_filing_penalty($payment_id)
    {
        $payment = $this->tax_db->where('id', $payment_id)->get('tbl_tax_payments')->row();
        $settings = $this->get_signboard_tax_settings();

        if (!$payment || !$settings) return false;

        $penalty_amount = ($payment->amount * $settings->no_filing_percent) / 100;

        // บันทึกประวัติค่าปรับ
        $penalty_data = [
            'payment_id' => $payment_id,
            'penalty_type' => 'no_filing',
            'base_amount' => $payment->amount,
            'penalty_amount' => $penalty_amount,
            'penalty_percent' => $settings->no_filing_percent,
            'created_by' => $this->session->userdata('m_id')
        ];

        $this->tax_db->insert('tbl_tax_penalties', $penalty_data);

        return $penalty_amount;
    }

    // คำนวณค่าปรับกรณียื่นแบบไม่ถูกต้อง
    public function calculate_signboard_incorrect_filing_penalty($payment_id)
    {
        $payment = $this->tax_db->where('id', $payment_id)->get('tbl_tax_payments')->row();
        $settings = $this->get_signboard_tax_settings();

        if (!$payment || !$settings) return false;

        // คำนวณจากส่วนต่างของภาษีที่ประเมินเพิ่ม
        $additional_tax = $payment->additional_tax ?? 0;
        $penalty_amount = ($additional_tax * $settings->incorrect_filing_percent) / 100;

        // บันทึกประวัติค่าปรับ
        $penalty_data = [
            'payment_id' => $payment_id,
            'penalty_type' => 'incorrect_filing',
            'base_amount' => $additional_tax,
            'penalty_amount' => $penalty_amount,
            'penalty_percent' => $settings->incorrect_filing_percent,
            'created_by' => $this->session->userdata('m_id')
        ];

        $this->tax_db->insert('tbl_tax_penalties', $penalty_data);

        return $penalty_amount;
    }

    // คำนวณค่าปรับกรณีชำระเกิน 15 วัน
    public function calculate_all_signboard_penalties()
    {
        // ดึงข้อมูลกำหนดวันที่ชำระสำหรับภาษีป้าย
        $due_date_info = $this->tax_db->where('tax_type', 'signboard')
            ->get('tbl_tax_due_dates')
            ->row();

        if (!$due_date_info) {
            return false;
        }

        // แยกวันที่ครบกำหนด
        list($due_day, $due_month) = explode('-', $due_date_info->due_date);

        // ดึงรายการภาษีป้ายที่ต้องจ่ายหรือค้างชำระ
        $payments = $this->tax_db->where('tax_type', 'signboard')
        ->where_not_in('payment_status', ['verified', 'pending'])
            ->get('tbl_tax_payments')
            ->result();

        if (empty($payments)) {
            return false;
        }

        foreach ($payments as $payment) {
            try {
                // สร้างวันที่ครบกำหนด
                $tax_year = (int)$payment->tax_year - 543; // แปลง พ.ศ. เป็น ค.ศ.
                $due_date = sprintf('%d-%02d-%02d', $tax_year, $due_month, $due_day);
                
                // กำหนดวันสุดท้ายที่ต้องชำระ (due_date + 15 วัน)
                $payment_deadline = date('Y-m-d', strtotime($due_date . ' + 15 days'));
                $current_date = date('Y-m-d');
    
                // Debug log
                log_message('debug', "Payment ID: {$payment->id}");
                log_message('debug', "Due Date: {$due_date}");
                log_message('debug', "Payment Deadline: {$payment_deadline}");
                log_message('debug', "Current Date: {$current_date}");
    
                // ตรวจสอบปีภาษีปัจจุบัน
                $current_tax_year = date('Y') + 543; // แปลงเป็น พ.ศ.
    
                // ตรวจสอบว่าเกินกำหนดหรือไม่
                if ($current_tax_year < $payment->tax_year || 
                    ($current_tax_year == $payment->tax_year && strtotime($current_date) <= strtotime($payment_deadline))) {
                    // ถ้ายังไม่เกินกำหนด รีเซ็ตค่าปรับเป็น 0 หรือ null
                    $update_data = [
                        'payment_status' => 'required',
                        'penalty_amount' => NULL,
                        'total_amount' => $payment->amount  // รีเซ็ตยอดรวมให้เท่ากับยอดภาษี
                    ];
    
                    $this->tax_db->where('id', $payment->id)
                        ->update('tbl_tax_payments', $update_data);
    
                    // ลบประวัติค่าปรับ (ถ้ามี)
                    $this->tax_db->where('payment_id', $payment->id)
                        ->where('penalty_type', 'late_payment')
                        ->delete('tbl_tax_penalties');
    
                    log_message('debug', "Reset penalty for payment ID: {$payment->id}");
                } else {
                    // คำนวณจำนวนเดือนที่เกินกำหนด
                    $date1 = new DateTime($payment_deadline);
                    $date2 = new DateTime($current_date);
                    $interval = $date1->diff($date2);
                    $months_late = ($interval->y * 12) + $interval->m;
                    if ($interval->d > 0) {
                        $months_late++; // นับเศษของเดือนเป็น 1 เดือน
                    }
    
                    // คิดค่าปรับ 2% ต่อเดือน
                    $monthly_rate = 2;
                    $penalty_amount = ($payment->amount * $monthly_rate * $months_late) / 100;
    
                    // อัพเดตสถานะและค่าปรับ
                    $update_data = [
                        'payment_status' => 'arrears',
                        'penalty_amount' => $penalty_amount,
                        'total_amount' => $payment->amount + $penalty_amount
                    ];
    
                    $this->tax_db->where('id', $payment->id)
                        ->update('tbl_tax_payments', $update_data);
    
                    // บันทึกประวัติการคำนวณค่าปรับ
                    $penalty_data = [
                        'payment_id' => $payment->id,
                        'tax_type' => 'signboard',
                        'penalty_type' => 'late_payment',
                        'base_amount' => $payment->amount,
                        'penalty_amount' => $penalty_amount,
                        'penalty_percent' => $monthly_rate * $months_late,
                        'months_late' => $months_late,
                        'created_by' => $this->session->userdata('m_id')
                    ];
    
                    $this->tax_db->insert('tbl_tax_penalties', $penalty_data);
                    log_message('debug', "Applied penalty for payment ID: {$payment->id}, Amount: {$penalty_amount}");
                }
            } catch (Exception $e) {
                log_message('error', 'Error calculating signboard penalty: ' . $e->getMessage());
                continue;
            }
        }
    
        return true;
    }
    // --------------------------------

    // เพิ่มฟังก์ชันสำหรับคำนวณภาษีทั้งหมดพร้อมกัน
    public function calculate_all_tax_penalties()
    {
        // คำนวณภาษีที่ดินและสิ่งปลูกสร้าง
        $this->calculate_late_payment_penalty();

        // คำนวณภาษีท้องถิ่น
        $this->calculate_local_tax_penalties();

        // คำนวณภาษีป้าย
        $this->calculate_all_signboard_penalties();

        return true;
    }
}
