<?php
class Auto_save_egp_model extends CI_Model
{

    public function __construct()
    {
        parent::__construct();
        $this->load->database(); // เรียกใช้งานฐานข้อมูล
    }

    public function save_data_egp_y2573($json_url)
    {
        function convertThaiDateToDatetime2573($textDate)
        {
            $thaiMonths = array(
                'ม.ค' => 'Jan', 'ก.พ' => 'Feb', 'มี.ค' => 'Mar', 'เม.ย' => 'Apr',
                'พ.ค' => 'May', 'มิ.ย' => 'Jun', 'ก.ค' => 'Jul', 'ส.ค' => 'Aug',
                'ก.ย' => 'Sep', 'ต.ค' => 'Oct', 'พ.ย' => 'Nov', 'ธ.ค' => 'Dec'
            );
    
            foreach ($thaiMonths as $thaiMonth => $englishMonth) {
                $textDate = str_replace($thaiMonth, $englishMonth, $textDate);
            }
    
            if (preg_match('/(\d{1,2}) (\w{3}\.?) (\d{2})/', $textDate, $matches)) {
                $day = $matches[1];
                $month = rtrim($matches[2], ".");
                $year = $matches[3] + 2500 - 543;
    
                $textDate = "$day $month $year";
                $dateTime = DateTime::createFromFormat('d M Y', $textDate);
                return $dateTime ? $dateTime->format('Y-m-d') : null;
            }
            return null;
        }
    
        $json_data = file_get_contents($json_url);
        $data = json_decode($json_data, true);
    
        if (isset($data['result']) && is_array($data['result']) && !empty($data['result'])) {
            $results = $data['result'];
            $this->db->empty_table('tbl_bp_report_y2573');
    
            foreach ($results as $row) {
            $flattenedRow = $this->flattenArray($row);

            // Remove the '0_' from column names if present
            $flattenedRow = array_combine(
                array_map(function ($key) {
                    return str_replace('_0_', '_', $key);
                }, array_keys($flattenedRow)),
                $flattenedRow
            );

            if (!empty($flattenedRow['contract_contract_date'])) {
                    $contractDate = convertThaiDateToDatetime2573($flattenedRow['contract_contract_date']);
                    $flattenedRow['contract_contract_date'] = $contractDate;
                    
                    $contractMonth = (int)date('m', strtotime($contractDate));
    
                    // เงื่อนไขสำหรับเดือนมกราคมถึงพฤศจิกายน
                    if (($contractMonth >= 1 && $contractMonth <= 11) && strpos($json_url, 'year=2573') !== false) {
                        // แสดงข้อมูลสำหรับปี 2573
                    }
                    // เงื่อนไขสำหรับเดือนตุลาคมถึงธันวาคม
                    elseif (($contractMonth >= 10 && $contractMonth <= 12) && strpos($json_url, 'year=2572') !== false) {
                        // แสดงข้อมูลสำหรับปี 2572
                    } else {
                        // ข้ามการบันทึกถ้าไม่ตรงเงื่อนไข
                        continue;
                    }
                }
    
                if (!empty($flattenedRow['contract_contract_finish_date'])) {
                    $flattenedRow['contract_contract_finish_date'] = convertThaiDateToDatetime2573($flattenedRow['contract_contract_finish_date']);
                }
    
                $this->db->insert('tbl_bp_report_y2573', $flattenedRow);
            }
    
            return count($results);
        } else {
            return 0;
        }
    }

    public function save_data_egp_y2572($json_url)
    {
        function convertThaiDateToDatetime2572($textDate)
        {
            $thaiMonths = array(
                'ม.ค' => 'Jan', 'ก.พ' => 'Feb', 'มี.ค' => 'Mar', 'เม.ย' => 'Apr',
                'พ.ค' => 'May', 'มิ.ย' => 'Jun', 'ก.ค' => 'Jul', 'ส.ค' => 'Aug',
                'ก.ย' => 'Sep', 'ต.ค' => 'Oct', 'พ.ย' => 'Nov', 'ธ.ค' => 'Dec'
            );
    
            foreach ($thaiMonths as $thaiMonth => $englishMonth) {
                $textDate = str_replace($thaiMonth, $englishMonth, $textDate);
            }
    
            if (preg_match('/(\d{1,2}) (\w{3}\.?) (\d{2})/', $textDate, $matches)) {
                $day = $matches[1];
                $month = rtrim($matches[2], ".");
                $year = $matches[3] + 2500 - 543;
    
                $textDate = "$day $month $year";
                $dateTime = DateTime::createFromFormat('d M Y', $textDate);
                return $dateTime ? $dateTime->format('Y-m-d') : null;
            }
            return null;
        }
    
        $json_data = file_get_contents($json_url);
        $data = json_decode($json_data, true);
    
        if (isset($data['result']) && is_array($data['result']) && !empty($data['result'])) {
            $results = $data['result'];
            $this->db->empty_table('tbl_bp_report_y2572');
    
            foreach ($results as $row) {
            $flattenedRow = $this->flattenArray($row);

            // Remove the '0_' from column names if present
            $flattenedRow = array_combine(
                array_map(function ($key) {
                    return str_replace('_0_', '_', $key);
                }, array_keys($flattenedRow)),
                $flattenedRow
            );

            if (!empty($flattenedRow['contract_contract_date'])) {
                    $contractDate = convertThaiDateToDatetime2572($flattenedRow['contract_contract_date']);
                    $flattenedRow['contract_contract_date'] = $contractDate;
                    
                    $contractMonth = (int)date('m', strtotime($contractDate));
    
                    // เงื่อนไขสำหรับเดือนมกราคมถึงพฤศจิกายน
                    if (($contractMonth >= 1 && $contractMonth <= 11) && strpos($json_url, 'year=2572') !== false) {
                        // แสดงข้อมูลสำหรับปี 2572
                    }
                    // เงื่อนไขสำหรับเดือนตุลาคมถึงธันวาคม
                    elseif (($contractMonth >= 10 && $contractMonth <= 12) && strpos($json_url, 'year=2571') !== false) {
                        // แสดงข้อมูลสำหรับปี 2571
                    } else {
                        // ข้ามการบันทึกถ้าไม่ตรงเงื่อนไข
                        continue;
                    }
                }
    
                if (!empty($flattenedRow['contract_contract_finish_date'])) {
                    $flattenedRow['contract_contract_finish_date'] = convertThaiDateToDatetime2572($flattenedRow['contract_contract_finish_date']);
                }
    
                $this->db->insert('tbl_bp_report_y2572', $flattenedRow);
            }
    
            return count($results);
        } else {
            return 0;
        }
    }

    public function save_data_egp_y2571($json_url)
    {
        function convertThaiDateToDatetime2571($textDate)
        {
            $thaiMonths = array(
                'ม.ค' => 'Jan', 'ก.พ' => 'Feb', 'มี.ค' => 'Mar', 'เม.ย' => 'Apr',
                'พ.ค' => 'May', 'มิ.ย' => 'Jun', 'ก.ค' => 'Jul', 'ส.ค' => 'Aug',
                'ก.ย' => 'Sep', 'ต.ค' => 'Oct', 'พ.ย' => 'Nov', 'ธ.ค' => 'Dec'
            );
    
            foreach ($thaiMonths as $thaiMonth => $englishMonth) {
                $textDate = str_replace($thaiMonth, $englishMonth, $textDate);
            }
    
            if (preg_match('/(\d{1,2}) (\w{3}\.?) (\d{2})/', $textDate, $matches)) {
                $day = $matches[1];
                $month = rtrim($matches[2], ".");
                $year = $matches[3] + 2500 - 543;
    
                $textDate = "$day $month $year";
                $dateTime = DateTime::createFromFormat('d M Y', $textDate);
                return $dateTime ? $dateTime->format('Y-m-d') : null;
            }
            return null;
        }
    
        $json_data = file_get_contents($json_url);
        $data = json_decode($json_data, true);
    
        if (isset($data['result']) && is_array($data['result']) && !empty($data['result'])) {
            $results = $data['result'];
            $this->db->empty_table('tbl_bp_report_y2571');
    
            foreach ($results as $row) {
            $flattenedRow = $this->flattenArray($row);

            // Remove the '0_' from column names if present
            $flattenedRow = array_combine(
                array_map(function ($key) {
                    return str_replace('_0_', '_', $key);
                }, array_keys($flattenedRow)),
                $flattenedRow
            );

            if (!empty($flattenedRow['contract_contract_date'])) {
                    $contractDate = convertThaiDateToDatetime2571($flattenedRow['contract_contract_date']);
                    $flattenedRow['contract_contract_date'] = $contractDate;
                    
                    $contractMonth = (int)date('m', strtotime($contractDate));
    
                    // เงื่อนไขสำหรับเดือนมกราคมถึงพฤศจิกายน
                    if (($contractMonth >= 1 && $contractMonth <= 11) && strpos($json_url, 'year=2571') !== false) {
                        // แสดงข้อมูลสำหรับปี 2571
                    }
                    // เงื่อนไขสำหรับเดือนตุลาคมถึงธันวาคม
                    elseif (($contractMonth >= 10 && $contractMonth <= 12) && strpos($json_url, 'year=2570') !== false) {
                        // แสดงข้อมูลสำหรับปี 2570
                    } else {
                        // ข้ามการบันทึกถ้าไม่ตรงเงื่อนไข
                        continue;
                    }
                }
    
                if (!empty($flattenedRow['contract_contract_finish_date'])) {
                    $flattenedRow['contract_contract_finish_date'] = convertThaiDateToDatetime2571($flattenedRow['contract_contract_finish_date']);
                }
    
                $this->db->insert('tbl_bp_report_y2571', $flattenedRow);
            }
    
            return count($results);
        } else {
            return 0;
        }
    }

    public function save_data_egp_y2570($json_url)
    {
        function convertThaiDateToDatetime2570($textDate)
        {
            $thaiMonths = array(
                'ม.ค' => 'Jan', 'ก.พ' => 'Feb', 'มี.ค' => 'Mar', 'เม.ย' => 'Apr',
                'พ.ค' => 'May', 'มิ.ย' => 'Jun', 'ก.ค' => 'Jul', 'ส.ค' => 'Aug',
                'ก.ย' => 'Sep', 'ต.ค' => 'Oct', 'พ.ย' => 'Nov', 'ธ.ค' => 'Dec'
            );
    
            foreach ($thaiMonths as $thaiMonth => $englishMonth) {
                $textDate = str_replace($thaiMonth, $englishMonth, $textDate);
            }
    
            if (preg_match('/(\d{1,2}) (\w{3}\.?) (\d{2})/', $textDate, $matches)) {
                $day = $matches[1];
                $month = rtrim($matches[2], ".");
                $year = $matches[3] + 2500 - 543;
    
                $textDate = "$day $month $year";
                $dateTime = DateTime::createFromFormat('d M Y', $textDate);
                return $dateTime ? $dateTime->format('Y-m-d') : null;
            }
            return null;
        }
    
        $json_data = file_get_contents($json_url);
        $data = json_decode($json_data, true);
    
        if (isset($data['result']) && is_array($data['result']) && !empty($data['result'])) {
            $results = $data['result'];
            $this->db->empty_table('tbl_bp_report_y2570');
    
            foreach ($results as $row) {
            $flattenedRow = $this->flattenArray($row);

            // Remove the '0_' from column names if present
            $flattenedRow = array_combine(
                array_map(function ($key) {
                    return str_replace('_0_', '_', $key);
                }, array_keys($flattenedRow)),
                $flattenedRow
            );

            if (!empty($flattenedRow['contract_contract_date'])) {
                    $contractDate = convertThaiDateToDatetime2570($flattenedRow['contract_contract_date']);
                    $flattenedRow['contract_contract_date'] = $contractDate;
                    
                    $contractMonth = (int)date('m', strtotime($contractDate));
    
                    // เงื่อนไขสำหรับเดือนมกราคมถึงพฤศจิกายน
                    if (($contractMonth >= 1 && $contractMonth <= 11) && strpos($json_url, 'year=2570') !== false) {
                        // แสดงข้อมูลสำหรับปี 2570
                    }
                    // เงื่อนไขสำหรับเดือนตุลาคมถึงธันวาคม
                    elseif (($contractMonth >= 10 && $contractMonth <= 12) && strpos($json_url, 'year=2569') !== false) {
                        // แสดงข้อมูลสำหรับปี 2569
                    } else {
                        // ข้ามการบันทึกถ้าไม่ตรงเงื่อนไข
                        continue;
                    }
                }
    
                if (!empty($flattenedRow['contract_contract_finish_date'])) {
                    $flattenedRow['contract_contract_finish_date'] = convertThaiDateToDatetime2570($flattenedRow['contract_contract_finish_date']);
                }
    
                $this->db->insert('tbl_bp_report_y2570', $flattenedRow);
            }
    
            return count($results);
        } else {
            return 0;
        }
    }

    public function save_data_egp_y2569($json_url)
    {
        function convertThaiDateToDatetime2569($textDate)
        {
            $thaiMonths = array(
                'ม.ค' => 'Jan', 'ก.พ' => 'Feb', 'มี.ค' => 'Mar', 'เม.ย' => 'Apr',
                'พ.ค' => 'May', 'มิ.ย' => 'Jun', 'ก.ค' => 'Jul', 'ส.ค' => 'Aug',
                'ก.ย' => 'Sep', 'ต.ค' => 'Oct', 'พ.ย' => 'Nov', 'ธ.ค' => 'Dec'
            );
    
            foreach ($thaiMonths as $thaiMonth => $englishMonth) {
                $textDate = str_replace($thaiMonth, $englishMonth, $textDate);
            }
    
            if (preg_match('/(\d{1,2}) (\w{3}\.?) (\d{2})/', $textDate, $matches)) {
                $day = $matches[1];
                $month = rtrim($matches[2], ".");
                $year = $matches[3] + 2500 - 543;
    
                $textDate = "$day $month $year";
                $dateTime = DateTime::createFromFormat('d M Y', $textDate);
                return $dateTime ? $dateTime->format('Y-m-d') : null;
            }
            return null;
        }
    
        $json_data = file_get_contents($json_url);
        $data = json_decode($json_data, true);
    
        if (isset($data['result']) && is_array($data['result']) && !empty($data['result'])) {
            $results = $data['result'];
            $this->db->empty_table('tbl_bp_report_y2569');
    
            foreach ($results as $row) {
            $flattenedRow = $this->flattenArray($row);

            // Remove the '0_' from column names if present
            $flattenedRow = array_combine(
                array_map(function ($key) {
                    return str_replace('_0_', '_', $key);
                }, array_keys($flattenedRow)),
                $flattenedRow
            );

            if (!empty($flattenedRow['contract_contract_date'])) {
                    $contractDate = convertThaiDateToDatetime2569($flattenedRow['contract_contract_date']);
                    $flattenedRow['contract_contract_date'] = $contractDate;
                    
                    $contractMonth = (int)date('m', strtotime($contractDate));
    
                    // เงื่อนไขสำหรับเดือนมกราคมถึงพฤศจิกายน
                    if (($contractMonth >= 1 && $contractMonth <= 11) && strpos($json_url, 'year=2569') !== false) {
                        // แสดงข้อมูลสำหรับปี 2569
                    }
                    // เงื่อนไขสำหรับเดือนตุลาคมถึงธันวาคม
                    elseif (($contractMonth >= 10 && $contractMonth <= 12) && strpos($json_url, 'year=2568') !== false) {
                        // แสดงข้อมูลสำหรับปี 2568
                    } else {
                        // ข้ามการบันทึกถ้าไม่ตรงเงื่อนไข
                        continue;
                    }
                }
    
                if (!empty($flattenedRow['contract_contract_finish_date'])) {
                    $flattenedRow['contract_contract_finish_date'] = convertThaiDateToDatetime2569($flattenedRow['contract_contract_finish_date']);
                }
    
                $this->db->insert('tbl_bp_report_y2569', $flattenedRow);
            }
    
            return count($results);
        } else {
            return 0;
        }
    }

    public function save_data_egp_y2568($json_url)
    {
        function convertThaiDateToDatetime2568($textDate)
        {
            $thaiMonths = array(
                'ม.ค' => 'Jan', 'ก.พ' => 'Feb', 'มี.ค' => 'Mar', 'เม.ย' => 'Apr',
                'พ.ค' => 'May', 'มิ.ย' => 'Jun', 'ก.ค' => 'Jul', 'ส.ค' => 'Aug',
                'ก.ย' => 'Sep', 'ต.ค' => 'Oct', 'พ.ย' => 'Nov', 'ธ.ค' => 'Dec'
            );
    
            foreach ($thaiMonths as $thaiMonth => $englishMonth) {
                $textDate = str_replace($thaiMonth, $englishMonth, $textDate);
            }
    
            if (preg_match('/(\d{1,2}) (\w{3}\.?) (\d{2})/', $textDate, $matches)) {
                $day = $matches[1];
                $month = rtrim($matches[2], ".");
                $year = $matches[3] + 2500 - 543;
    
                $textDate = "$day $month $year";
                $dateTime = DateTime::createFromFormat('d M Y', $textDate);
                return $dateTime ? $dateTime->format('Y-m-d') : null;
            }
            return null;
        }
    
        $json_data = file_get_contents($json_url);
        $data = json_decode($json_data, true);
    
        if (isset($data['result']) && is_array($data['result']) && !empty($data['result'])) {
            $results = $data['result'];
            $this->db->empty_table('tbl_bp_report_y2568');
    
            foreach ($results as $row) {
            $flattenedRow = $this->flattenArray($row);

            // Remove the '0_' from column names if present
            $flattenedRow = array_combine(
                array_map(function ($key) {
                    return str_replace('_0_', '_', $key);
                }, array_keys($flattenedRow)),
                $flattenedRow
            );

            if (!empty($flattenedRow['contract_contract_date'])) {
                    $contractDate = convertThaiDateToDatetime2568($flattenedRow['contract_contract_date']);
                    $flattenedRow['contract_contract_date'] = $contractDate;
                    
                    $contractMonth = (int)date('m', strtotime($contractDate));
    
                    // เงื่อนไขสำหรับเดือนมกราคมถึงพฤศจิกายน
                    if (($contractMonth >= 1 && $contractMonth <= 11) && strpos($json_url, 'year=2568') !== false) {
                        // แสดงข้อมูลสำหรับปี 2568
                    }
                    // เงื่อนไขสำหรับเดือนตุลาคมถึงธันวาคม
                    elseif (($contractMonth >= 10 && $contractMonth <= 12) && strpos($json_url, 'year=2567') !== false) {
                        // แสดงข้อมูลสำหรับปี 2567
                    } else {
                        // ข้ามการบันทึกถ้าไม่ตรงเงื่อนไข
                        continue;
                    }
                }
    
                if (!empty($flattenedRow['contract_contract_finish_date'])) {
                    $flattenedRow['contract_contract_finish_date'] = convertThaiDateToDatetime2568($flattenedRow['contract_contract_finish_date']);
                }
    
                $this->db->insert('tbl_bp_report_y2568', $flattenedRow);
            }
    
            return count($results);
        } else {
            return 0;
        }
    }

    public function save_data_egp_y2567($json_url)
    {
        function convertThaiDateToDatetime2567($textDate)
        {
            $thaiMonths = array(
                'ม.ค' => 'Jan', 'ก.พ' => 'Feb', 'มี.ค' => 'Mar', 'เม.ย' => 'Apr',
                'พ.ค' => 'May', 'มิ.ย' => 'Jun', 'ก.ค' => 'Jul', 'ส.ค' => 'Aug',
                'ก.ย' => 'Sep', 'ต.ค' => 'Oct', 'พ.ย' => 'Nov', 'ธ.ค' => 'Dec'
            );
    
            foreach ($thaiMonths as $thaiMonth => $englishMonth) {
                $textDate = str_replace($thaiMonth, $englishMonth, $textDate);
            }
    
            if (preg_match('/(\d{1,2}) (\w{3}\.?) (\d{2})/', $textDate, $matches)) {
                $day = $matches[1];
                $month = rtrim($matches[2], ".");
                $year = $matches[3] + 2500 - 543;
    
                $textDate = "$day $month $year";
                $dateTime = DateTime::createFromFormat('d M Y', $textDate);
                return $dateTime ? $dateTime->format('Y-m-d') : null;
            }
            return null;
        }
    
        $json_data = file_get_contents($json_url);
        $data = json_decode($json_data, true);
    
        if (isset($data['result']) && is_array($data['result']) && !empty($data['result'])) {
            $results = $data['result'];
            $this->db->empty_table('tbl_bp_report_y2567');
    
            foreach ($results as $row) {
            $flattenedRow = $this->flattenArray($row);

            // Remove the '0_' from column names if present
            $flattenedRow = array_combine(
                array_map(function ($key) {
                    return str_replace('_0_', '_', $key);
                }, array_keys($flattenedRow)),
                $flattenedRow
            );

            if (!empty($flattenedRow['contract_contract_date'])) {
                    $contractDate = convertThaiDateToDatetime2567($flattenedRow['contract_contract_date']);
                    $flattenedRow['contract_contract_date'] = $contractDate;
                    
                    $contractMonth = (int)date('m', strtotime($contractDate));
    
                    // เงื่อนไขสำหรับเดือนมกราคมถึงพฤศจิกายน
                    if (($contractMonth >= 1 && $contractMonth <= 11) && strpos($json_url, 'year=2567') !== false) {
                        // แสดงข้อมูลสำหรับปี 2567
                    }
                    // เงื่อนไขสำหรับเดือนตุลาคมถึงธันวาคม
                    elseif (($contractMonth >= 10 && $contractMonth <= 12) && strpos($json_url, 'year=2566') !== false) {
                        // แสดงข้อมูลสำหรับปี 2566
                    } else {
                        // ข้ามการบันทึกถ้าไม่ตรงเงื่อนไข
                        continue;
                    }
                }
    
                if (!empty($flattenedRow['contract_contract_finish_date'])) {
                    $flattenedRow['contract_contract_finish_date'] = convertThaiDateToDatetime2567($flattenedRow['contract_contract_finish_date']);
                }
    
                $this->db->insert('tbl_bp_report_y2567', $flattenedRow);
            }
    
            return count($results);
        } else {
            return 0;
        }
    }

    public function save_data_egp_y2566($json_url)
    {
        function convertThaiDateToDatetime2566($textDate)
        {
            $thaiMonths = array(
                'ม.ค' => 'Jan', 'ก.พ' => 'Feb', 'มี.ค' => 'Mar', 'เม.ย' => 'Apr',
                'พ.ค' => 'May', 'มิ.ย' => 'Jun', 'ก.ค' => 'Jul', 'ส.ค' => 'Aug',
                'ก.ย' => 'Sep', 'ต.ค' => 'Oct', 'พ.ย' => 'Nov', 'ธ.ค' => 'Dec'
            );
    
            foreach ($thaiMonths as $thaiMonth => $englishMonth) {
                $textDate = str_replace($thaiMonth, $englishMonth, $textDate);
            }
    
            if (preg_match('/(\d{1,2}) (\w{3}\.?) (\d{2})/', $textDate, $matches)) {
                $day = $matches[1];
                $month = rtrim($matches[2], ".");
                $year = $matches[3] + 2500 - 543;
    
                $textDate = "$day $month $year";
                $dateTime = DateTime::createFromFormat('d M Y', $textDate);
                return $dateTime ? $dateTime->format('Y-m-d') : null;
            }
            return null;
        }
    
        $json_data = file_get_contents($json_url);
        $data = json_decode($json_data, true);
    
        if (isset($data['result']) && is_array($data['result']) && !empty($data['result'])) {
            $results = $data['result'];
            $this->db->empty_table('tbl_bp_report_y2566');
    
            foreach ($results as $row) {
            $flattenedRow = $this->flattenArray($row);

            // Remove the '0_' from column names if present
            $flattenedRow = array_combine(
                array_map(function ($key) {
                    return str_replace('_0_', '_', $key);
                }, array_keys($flattenedRow)),
                $flattenedRow
            );

            if (!empty($flattenedRow['contract_contract_date'])) {
                    $contractDate = convertThaiDateToDatetime2566($flattenedRow['contract_contract_date']);
                    $flattenedRow['contract_contract_date'] = $contractDate;
                    
                    $contractMonth = (int)date('m', strtotime($contractDate));
    
                    // เงื่อนไขสำหรับเดือนมกราคมถึงพฤศจิกายน
                    if (($contractMonth >= 1 && $contractMonth <= 11) && strpos($json_url, 'year=2566') !== false) {
                        // แสดงข้อมูลสำหรับปี 2566
                    }
                    // เงื่อนไขสำหรับเดือนตุลาคมถึงธันวาคม
                    elseif (($contractMonth >= 10 && $contractMonth <= 12) && strpos($json_url, 'year=2565') !== false) {
                        // แสดงข้อมูลสำหรับปี 2565
                    } else {
                        // ข้ามการบันทึกถ้าไม่ตรงเงื่อนไข
                        continue;
                    }
                }
    
                if (!empty($flattenedRow['contract_contract_finish_date'])) {
                    $flattenedRow['contract_contract_finish_date'] = convertThaiDateToDatetime2566($flattenedRow['contract_contract_finish_date']);
                }
    
                $this->db->insert('tbl_bp_report_y2566', $flattenedRow);
            }
    
            return count($results);
        } else {
            return 0;
        }
    }

    public function save_data_egp_y2565($json_url)
    {
        function convertThaiDateToDatetime2565($textDate)
        {
            $thaiMonths = array(
                'ม.ค' => 'Jan', 'ก.พ' => 'Feb', 'มี.ค' => 'Mar', 'เม.ย' => 'Apr',
                'พ.ค' => 'May', 'มิ.ย' => 'Jun', 'ก.ค' => 'Jul', 'ส.ค' => 'Aug',
                'ก.ย' => 'Sep', 'ต.ค' => 'Oct', 'พ.ย' => 'Nov', 'ธ.ค' => 'Dec'
            );
    
            foreach ($thaiMonths as $thaiMonth => $englishMonth) {
                $textDate = str_replace($thaiMonth, $englishMonth, $textDate);
            }
    
            if (preg_match('/(\d{1,2}) (\w{3}\.?) (\d{2})/', $textDate, $matches)) {
                $day = $matches[1];
                $month = rtrim($matches[2], ".");
                $year = $matches[3] + 2500 - 543;
    
                $textDate = "$day $month $year";
                $dateTime = DateTime::createFromFormat('d M Y', $textDate);
                return $dateTime ? $dateTime->format('Y-m-d') : null;
            }
            return null;
        }
    
        $json_data = file_get_contents($json_url);
        $data = json_decode($json_data, true);
    
        if (isset($data['result']) && is_array($data['result']) && !empty($data['result'])) {
            $results = $data['result'];
            $this->db->empty_table('tbl_bp_report_y2565');
    
            foreach ($results as $row) {
            $flattenedRow = $this->flattenArray($row);

            // Remove the '0_' from column names if present
            $flattenedRow = array_combine(
                array_map(function ($key) {
                    return str_replace('_0_', '_', $key);
                }, array_keys($flattenedRow)),
                $flattenedRow
            );

            if (!empty($flattenedRow['contract_contract_date'])) {
                    $contractDate = convertThaiDateToDatetime2565($flattenedRow['contract_contract_date']);
                    $flattenedRow['contract_contract_date'] = $contractDate;
                    
                    $contractMonth = (int)date('m', strtotime($contractDate));
    
                    // เงื่อนไขสำหรับเดือนมกราคมถึงพฤศจิกายน
                    if (($contractMonth >= 1 && $contractMonth <= 11) && strpos($json_url, 'year=2565') !== false) {
                        // แสดงข้อมูลสำหรับปี 2565
                    }
                    // เงื่อนไขสำหรับเดือนตุลาคมถึงธันวาคม
                    elseif (($contractMonth >= 10 && $contractMonth <= 12) && strpos($json_url, 'year=2564') !== false) {
                        // แสดงข้อมูลสำหรับปี 2564
                    } else {
                        // ข้ามการบันทึกถ้าไม่ตรงเงื่อนไข
                        continue;
                    }
                }
    
                if (!empty($flattenedRow['contract_contract_finish_date'])) {
                    $flattenedRow['contract_contract_finish_date'] = convertThaiDateToDatetime2565($flattenedRow['contract_contract_finish_date']);
                }
    
                $this->db->insert('tbl_bp_report_y2565', $flattenedRow);
            }
    
            return count($results);
        } else {
            return 0;
        }
    }

    public function save_data_egp_y2564($json_url)
    {
        function convertThaiDateToDatetime2564($textDate)
        {
            $thaiMonths = array(
                'ม.ค' => 'Jan', 'ก.พ' => 'Feb', 'มี.ค' => 'Mar', 'เม.ย' => 'Apr',
                'พ.ค' => 'May', 'มิ.ย' => 'Jun', 'ก.ค' => 'Jul', 'ส.ค' => 'Aug',
                'ก.ย' => 'Sep', 'ต.ค' => 'Oct', 'พ.ย' => 'Nov', 'ธ.ค' => 'Dec'
            );
    
            foreach ($thaiMonths as $thaiMonth => $englishMonth) {
                $textDate = str_replace($thaiMonth, $englishMonth, $textDate);
            }
    
            if (preg_match('/(\d{1,2}) (\w{3}\.?) (\d{2})/', $textDate, $matches)) {
                $day = $matches[1];
                $month = rtrim($matches[2], ".");
                $year = $matches[3] + 2500 - 543;
    
                $textDate = "$day $month $year";
                $dateTime = DateTime::createFromFormat('d M Y', $textDate);
                return $dateTime ? $dateTime->format('Y-m-d') : null;
            }
            return null;
        }
    
        $json_data = file_get_contents($json_url);
        $data = json_decode($json_data, true);
    
        if (isset($data['result']) && is_array($data['result']) && !empty($data['result'])) {
            $results = $data['result'];
            $this->db->empty_table('tbl_bp_report_y2564');
    
            foreach ($results as $row) {
            $flattenedRow = $this->flattenArray($row);

            // Remove the '0_' from column names if present
            $flattenedRow = array_combine(
                array_map(function ($key) {
                    return str_replace('_0_', '_', $key);
                }, array_keys($flattenedRow)),
                $flattenedRow
            );

            if (!empty($flattenedRow['contract_contract_date'])) {
                    $contractDate = convertThaiDateToDatetime2564($flattenedRow['contract_contract_date']);
                    $flattenedRow['contract_contract_date'] = $contractDate;
                    
                    $contractMonth = (int)date('m', strtotime($contractDate));
    
                    // เงื่อนไขสำหรับเดือนมกราคมถึงพฤศจิกายน
                    if (($contractMonth >= 1 && $contractMonth <= 11) && strpos($json_url, 'year=2564') !== false) {
                        // แสดงข้อมูลสำหรับปี 2564
                    }
                    // เงื่อนไขสำหรับเดือนตุลาคมถึงธันวาคม
                    elseif (($contractMonth >= 10 && $contractMonth <= 12) && strpos($json_url, 'year=2563') !== false) {
                        // แสดงข้อมูลสำหรับปี 2563
                    } else {
                        // ข้ามการบันทึกถ้าไม่ตรงเงื่อนไข
                        continue;
                    }
                }
    
                if (!empty($flattenedRow['contract_contract_finish_date'])) {
                    $flattenedRow['contract_contract_finish_date'] = convertThaiDateToDatetime2564($flattenedRow['contract_contract_finish_date']);
                }
    
                $this->db->insert('tbl_bp_report_y2564', $flattenedRow);
            }
    
            return count($results);
        } else {
            return 0;
        }
    }

    public function save_data_egp_y2563($json_url)
    {
        function convertThaiDateToDatetime2563($textDate)
        {
            $thaiMonths = array(
                'ม.ค' => 'Jan', 'ก.พ' => 'Feb', 'มี.ค' => 'Mar', 'เม.ย' => 'Apr',
                'พ.ค' => 'May', 'มิ.ย' => 'Jun', 'ก.ค' => 'Jul', 'ส.ค' => 'Aug',
                'ก.ย' => 'Sep', 'ต.ค' => 'Oct', 'พ.ย' => 'Nov', 'ธ.ค' => 'Dec'
            );
    
            foreach ($thaiMonths as $thaiMonth => $englishMonth) {
                $textDate = str_replace($thaiMonth, $englishMonth, $textDate);
            }
    
            if (preg_match('/(\d{1,2}) (\w{3}\.?) (\d{2})/', $textDate, $matches)) {
                $day = $matches[1];
                $month = rtrim($matches[2], ".");
                $year = $matches[3] + 2500 - 543;
    
                $textDate = "$day $month $year";
                $dateTime = DateTime::createFromFormat('d M Y', $textDate);
                return $dateTime ? $dateTime->format('Y-m-d') : null;
            }
            return null;
        }
    
        $json_data = file_get_contents($json_url);
        $data = json_decode($json_data, true);
    
        if (isset($data['result']) && is_array($data['result']) && !empty($data['result'])) {
            $results = $data['result'];
            $this->db->empty_table('tbl_bp_report_y2563');
    
            foreach ($results as $row) {
            $flattenedRow = $this->flattenArray($row);

            // Remove the '0_' from column names if present
            $flattenedRow = array_combine(
                array_map(function ($key) {
                    return str_replace('_0_', '_', $key);
                }, array_keys($flattenedRow)),
                $flattenedRow
            );

            if (!empty($flattenedRow['contract_contract_date'])) {
                    $contractDate = convertThaiDateToDatetime2563($flattenedRow['contract_contract_date']);
                    $flattenedRow['contract_contract_date'] = $contractDate;
                    
                    $contractMonth = (int)date('m', strtotime($contractDate));
    
                    // เงื่อนไขสำหรับเดือนมกราคมถึงพฤศจิกายน
                    if (($contractMonth >= 1 && $contractMonth <= 11) && strpos($json_url, 'year=2563') !== false) {
                        // แสดงข้อมูลสำหรับปี 2563
                    }
                    // เงื่อนไขสำหรับเดือนตุลาคมถึงธันวาคม
                    elseif (($contractMonth >= 10 && $contractMonth <= 12) && strpos($json_url, 'year=2562') !== false) {
                        // แสดงข้อมูลสำหรับปี 2562
                    } else {
                        // ข้ามการบันทึกถ้าไม่ตรงเงื่อนไข
                        continue;
                    }
                }
    
                if (!empty($flattenedRow['contract_contract_finish_date'])) {
                    $flattenedRow['contract_contract_finish_date'] = convertThaiDateToDatetime2563($flattenedRow['contract_contract_finish_date']);
                }
    
                $this->db->insert('tbl_bp_report_y2563', $flattenedRow);
            }
    
            return count($results);
        } else {
            return 0;
        }
    }

    
    


    // Function to flatten nested arrays and combine keys
    private function flattenArray($array, $prefix = '')
    {
        $result = [];
        foreach ($array as $key => $value) {
            if (is_array($value)) {
                // Recursively flatten nested arrays
                $result += $this->flattenArray($value, $prefix . $key . '_');
            } else {
                // Combine keys with underscores
                $result[$prefix . $key] = $value;
            }
        }
        return $result;
    }
}
