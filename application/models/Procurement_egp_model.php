<?php
class Procurement_egp_model extends CI_Model
{
    // private $remote_db;
    // private $connection_status = false;
    // private $database_name;

    // public function __construct()
    // {
    //     parent::__construct();
    //     $this->database_name = get_config_value('procurement_egp_model');
    //     $this->connect_database();
    // }

    // private function connect_database()
    // {
    //     try {
    //         // ปิด mysqli error reporting ชั่วคราว
    //         mysqli_report(MYSQLI_REPORT_OFF);
            
    //         // ลองเชื่อมต่อฐานข้อมูล
    //         $this->remote_db = @new mysqli(
    //             "localthai.assystem.co.th",
    //             "asadmin",
    //             "ASsystem@458",
    //             $this->database_name
    //         );
            
    //         // ตรวจสอบการเชื่อมต่อ
    //         if ($this->remote_db->connect_error) {
    //             $this->connection_status = false;
    //             // บันทึก log แต่ไม่แสดง error
    //             log_message('error', 'EGP DB connection failed: ' . $this->remote_db->connect_error);
    //             return false;
    //         }
            
    //         // ตั้งค่า charset
    //         $this->remote_db->set_charset("utf8");
    //         $this->connection_status = true;
    //         return true;
            
    //     } catch (Exception $e) {
    //         $this->connection_status = false;
    //         log_message('error', 'EGP Database connection exception: ' . $e->getMessage());
    //         return false;
    //     }
    // }

    // // ฟังก์ชันตรวจสอบสถานะการเชื่อมต่อ
    // public function is_connected()
    // {
    //     return $this->connection_status;
    // }

    // // tbl_p0 = แผนการจัดซื้อจัดจ้าง
    // public function get_tbl_p0_all()
    // {
    //     if (!$this->connection_status) {
    //         return array();
    //     }

    //     try {
    //         $data = array();
    //         $sql = "SELECT * FROM tbl_p0 ORDER BY item_date DESC";
    //         $result = @$this->remote_db->query($sql);
            
    //         if ($result && $result->num_rows > 0) {
    //             while ($row = $result->fetch_assoc()) {
    //                 $data[] = $row;
    //             }
    //             $result->free();
    //         }
            
    //         return $data;
            
    //     } catch (Exception $e) {
    //         log_message('error', 'Error in get_tbl_p0_all: ' . $e->getMessage());
    //         return array();
    //     }
    // }

    // public function search_tbl_p0($search, $start_date = null, $end_date = null)
    // {
    //     if (!$this->connection_status) {
    //         return array();
    //     }

    //     try {
    //         $search = $this->remote_db->real_escape_string($search);
            
    //         $sql = "SELECT * FROM tbl_p0 WHERE 1";

    //         if (!empty($search)) {
    //             $sql .= " AND item_title LIKE '%$search%'";
    //         }

    //         if (!empty($start_date) && !empty($end_date)) {
    //             $sql .= " AND item_date BETWEEN '$start_date' AND '$end_date'";
    //         } elseif (!empty($start_date)) {
    //             $sql .= " AND item_date >= '$start_date'";
    //         } elseif (!empty($end_date)) {
    //             $sql .= " AND item_date <= '$end_date'";
    //         }

    //         $sql .= " ORDER BY item_date DESC";

    //         $result = @$this->remote_db->query($sql);
    //         $data = array();
            
    //         if ($result && $result->num_rows > 0) {
    //             while ($row = $result->fetch_assoc()) {
    //                 $data[] = $row;
    //             }
    //             $result->free();
    //         }

    //         return $data;
            
    //     } catch (Exception $e) {
    //         log_message('error', 'Error in search_tbl_p0: ' . $e->getMessage());
    //         return array();
    //     }
    // }

    // // tbl_15 = ประกาศราคากลาง
    // public function get_tbl_15_all()
    // {
    //     if (!$this->connection_status) {
    //         return array();
    //     }

    //     try {
    //         $data = array();
    //         $sql = "SELECT * FROM tbl_15 ORDER BY item_date DESC";
    //         $result = @$this->remote_db->query($sql);
            
    //         if ($result && $result->num_rows > 0) {
    //             while ($row = $result->fetch_assoc()) {
    //                 $data[] = $row;
    //             }
    //             $result->free();
    //         }
            
    //         return $data;
            
    //     } catch (Exception $e) {
    //         log_message('error', 'Error in get_tbl_15_all: ' . $e->getMessage());
    //         return array();
    //     }
    // }

    // public function search_tbl_15($search, $start_date = null, $end_date = null)
    // {
    //     if (!$this->connection_status) {
    //         return array();
    //     }

    //     try {
    //         $search = $this->remote_db->real_escape_string($search);
            
    //         $sql = "SELECT * FROM tbl_15 WHERE 1";

    //         if (!empty($search)) {
    //             $sql .= " AND item_title LIKE '%$search%'";
    //         }

    //         if (!empty($start_date) && !empty($end_date)) {
    //             $sql .= " AND item_date BETWEEN '$start_date' AND '$end_date'";
    //         } elseif (!empty($start_date)) {
    //             $sql .= " AND item_date >= '$start_date'";
    //         } elseif (!empty($end_date)) {
    //             $sql .= " AND item_date <= '$end_date'";
    //         }

    //         $sql .= " ORDER BY item_date DESC";

    //         $result = @$this->remote_db->query($sql);
    //         $data = array();
            
    //         if ($result && $result->num_rows > 0) {
    //             while ($row = $result->fetch_assoc()) {
    //                 $data[] = $row;
    //             }
    //             $result->free();
    //         }

    //         return $data;
            
    //     } catch (Exception $e) {
    //         log_message('error', 'Error in search_tbl_15: ' . $e->getMessage());
    //         return array();
    //     }
    // }

    // // tbl_b0 = ร่างเอกสารประกวดราคา
    // public function get_tbl_b0_all()
    // {
    //     if (!$this->connection_status) {
    //         return array();
    //     }

    //     try {
    //         $data = array();
    //         $sql = "SELECT * FROM tbl_b0 ORDER BY item_date DESC";
    //         $result = @$this->remote_db->query($sql);
            
    //         if ($result && $result->num_rows > 0) {
    //             while ($row = $result->fetch_assoc()) {
    //                 $data[] = $row;
    //             }
    //             $result->free();
    //         }
            
    //         return $data;
            
    //     } catch (Exception $e) {
    //         log_message('error', 'Error in get_tbl_b0_all: ' . $e->getMessage());
    //         return array();
    //     }
    // }

    // public function search_tbl_b0($search, $start_date = null, $end_date = null)
    // {
    //     if (!$this->connection_status) {
    //         return array();
    //     }

    //     try {
    //         $search = $this->remote_db->real_escape_string($search);
            
    //         $sql = "SELECT * FROM tbl_b0 WHERE 1";

    //         if (!empty($search)) {
    //             $sql .= " AND item_title LIKE '%$search%'";
    //         }

    //         if (!empty($start_date) && !empty($end_date)) {
    //             $sql .= " AND item_date BETWEEN '$start_date' AND '$end_date'";
    //         } elseif (!empty($start_date)) {
    //             $sql .= " AND item_date >= '$start_date'";
    //         } elseif (!empty($end_date)) {
    //             $sql .= " AND item_date <= '$end_date'";
    //         }

    //         $sql .= " ORDER BY item_date DESC";

    //         $result = @$this->remote_db->query($sql);
    //         $data = array();
            
    //         if ($result && $result->num_rows > 0) {
    //             while ($row = $result->fetch_assoc()) {
    //                 $data[] = $row;
    //             }
    //             $result->free();
    //         }

    //         return $data;
            
    //     } catch (Exception $e) {
    //         log_message('error', 'Error in search_tbl_b0: ' . $e->getMessage());
    //         return array();
    //     }
    // }

    // // tbl_d0 = ประกาศเชิญชวน
    // public function get_tbl_d0_all()
    // {
    //     if (!$this->connection_status) {
    //         return array();
    //     }

    //     try {
    //         $data = array();
    //         $sql = "SELECT * FROM tbl_d0 ORDER BY item_date DESC";
    //         $result = @$this->remote_db->query($sql);
            
    //         if ($result && $result->num_rows > 0) {
    //             while ($row = $result->fetch_assoc()) {
    //                 $data[] = $row;
    //             }
    //             $result->free();
    //         }
            
    //         return $data;
            
    //     } catch (Exception $e) {
    //         log_message('error', 'Error in get_tbl_d0_all: ' . $e->getMessage());
    //         return array();
    //     }
    // }

    // public function search_tbl_d0($search, $start_date = null, $end_date = null)
    // {
    //     if (!$this->connection_status) {
    //         return array();
    //     }

    //     try {
    //         $search = $this->remote_db->real_escape_string($search);
            
    //         $sql = "SELECT * FROM tbl_d0 WHERE 1";

    //         if (!empty($search)) {
    //             $sql .= " AND item_title LIKE '%$search%'";
    //         }

    //         if (!empty($start_date) && !empty($end_date)) {
    //             $sql .= " AND item_date BETWEEN '$start_date' AND '$end_date'";
    //         } elseif (!empty($start_date)) {
    //             $sql .= " AND item_date >= '$start_date'";
    //         } elseif (!empty($end_date)) {
    //             $sql .= " AND item_date <= '$end_date'";
    //         }

    //         $sql .= " ORDER BY item_date DESC";

    //         $result = @$this->remote_db->query($sql);
    //         $data = array();
            
    //         if ($result && $result->num_rows > 0) {
    //             while ($row = $result->fetch_assoc()) {
    //                 $data[] = $row;
    //             }
    //             $result->free();
    //         }

    //         return $data;
            
    //     } catch (Exception $e) {
    //         log_message('error', 'Error in search_tbl_d0: ' . $e->getMessage());
    //         return array();
    //     }
    // }

    // // tbl_w0 = ประกาศรายชื่อผู้ชนะการเสนอราคา
    // public function get_tbl_w0_all()
    // {
    //     if (!$this->connection_status) {
    //         return array();
    //     }

    //     try {
    //         $data = array();
    //         $sql = "SELECT * FROM tbl_w0 ORDER BY item_date DESC";
    //         $result = @$this->remote_db->query($sql);
            
    //         if ($result && $result->num_rows > 0) {
    //             while ($row = $result->fetch_assoc()) {
    //                 $data[] = $row;
    //             }
    //             $result->free();
    //         }
            
    //         return $data;
            
    //     } catch (Exception $e) {
    //         log_message('error', 'Error in get_tbl_w0_all: ' . $e->getMessage());
    //         return array();
    //     }
    // }

    // public function search_tbl_w0($search, $start_date = null, $end_date = null)
    // {
    //     if (!$this->connection_status) {
    //         return array();
    //     }

    //     try {
    //         $search = $this->remote_db->real_escape_string($search);
            
    //         $sql = "SELECT * FROM tbl_w0 WHERE 1";

    //         if (!empty($search)) {
    //             $sql .= " AND item_title LIKE '%$search%'";
    //         }

    //         if (!empty($start_date) && !empty($end_date)) {
    //             $sql .= " AND item_date BETWEEN '$start_date' AND '$end_date'";
    //         } elseif (!empty($start_date)) {
    //             $sql .= " AND item_date >= '$start_date'";
    //         } elseif (!empty($end_date)) {
    //             $sql .= " AND item_date <= '$end_date'";
    //         }

    //         $sql .= " ORDER BY item_date DESC";

    //         $result = @$this->remote_db->query($sql);
    //         $data = array();
            
    //         if ($result && $result->num_rows > 0) {
    //             while ($row = $result->fetch_assoc()) {
    //                 $data[] = $row;
    //             }
    //             $result->free();
    //         }

    //         return $data;
            
    //     } catch (Exception $e) {
    //         log_message('error', 'Error in search_tbl_w0: ' . $e->getMessage());
    //         return array();
    //     }
    // }

    // // tbl_d1 = ยกเลิกประกาศเชิญชวน
    // public function get_tbl_d1_all()
    // {
    //     if (!$this->connection_status) {
    //         return array();
    //     }

    //     try {
    //         $data = array();
    //         $sql = "SELECT * FROM tbl_d1 ORDER BY item_date DESC";
    //         $result = @$this->remote_db->query($sql);
            
    //         if ($result && $result->num_rows > 0) {
    //             while ($row = $result->fetch_assoc()) {
    //                 $data[] = $row;
    //             }
    //             $result->free();
    //         }
            
    //         return $data;
            
    //     } catch (Exception $e) {
    //         log_message('error', 'Error in get_tbl_d1_all: ' . $e->getMessage());
    //         return array();
    //     }
    // }

    // public function search_tbl_d1($search, $start_date = null, $end_date = null)
    // {
    //     if (!$this->connection_status) {
    //         return array();
    //     }

    //     try {
    //         $search = $this->remote_db->real_escape_string($search);
            
    //         $sql = "SELECT * FROM tbl_d1 WHERE 1";

    //         if (!empty($search)) {
    //             $sql .= " AND item_title LIKE '%$search%'";
    //         }

    //         if (!empty($start_date) && !empty($end_date)) {
    //             $sql .= " AND item_date BETWEEN '$start_date' AND '$end_date'";
    //         } elseif (!empty($start_date)) {
    //             $sql .= " AND item_date >= '$start_date'";
    //         } elseif (!empty($end_date)) {
    //             $sql .= " AND item_date <= '$end_date'";
    //         }

    //         $sql .= " ORDER BY item_date DESC";

    //         $result = @$this->remote_db->query($sql);
    //         $data = array();
            
    //         if ($result && $result->num_rows > 0) {
    //             while ($row = $result->fetch_assoc()) {
    //                 $data[] = $row;
    //             }
    //             $result->free();
    //         }

    //         return $data;
            
    //     } catch (Exception $e) {
    //         log_message('error', 'Error in search_tbl_d1: ' . $e->getMessage());
    //         return array();
    //     }
    // }

    // // tbl_w1 = ยกเลิกประกาศรายชื่อผู้ชนะการเสนอราคา
    // public function get_tbl_w1_all()
    // {
    //     if (!$this->connection_status) {
    //         return array();
    //     }

    //     try {
    //         $data = array();
    //         $sql = "SELECT * FROM tbl_w1 ORDER BY item_date DESC";
    //         $result = @$this->remote_db->query($sql);
            
    //         if ($result && $result->num_rows > 0) {
    //             while ($row = $result->fetch_assoc()) {
    //                 $data[] = $row;
    //             }
    //             $result->free();
    //         }
            
    //         return $data;
            
    //     } catch (Exception $e) {
    //         log_message('error', 'Error in get_tbl_w1_all: ' . $e->getMessage());
    //         return array();
    //     }
    // }

    // public function search_tbl_w1($search, $start_date = null, $end_date = null)
    // {
    //     if (!$this->connection_status) {
    //         return array();
    //     }

    //     try {
    //         $search = $this->remote_db->real_escape_string($search);
            
    //         $sql = "SELECT * FROM tbl_w1 WHERE 1";

    //         if (!empty($search)) {
    //             $sql .= " AND item_title LIKE '%$search%'";
    //         }

    //         if (!empty($start_date) && !empty($end_date)) {
    //             $sql .= " AND item_date BETWEEN '$start_date' AND '$end_date'";
    //         } elseif (!empty($start_date)) {
    //             $sql .= " AND item_date >= '$start_date'";
    //         } elseif (!empty($end_date)) {
    //             $sql .= " AND item_date <= '$end_date'";
    //         }

    //         $sql .= " ORDER BY item_date DESC";

    //         $result = @$this->remote_db->query($sql);
    //         $data = array();
            
    //         if ($result && $result->num_rows > 0) {
    //             while ($row = $result->fetch_assoc()) {
    //                 $data[] = $row;
    //             }
    //             $result->free();
    //         }

    //         return $data;
            
    //     } catch (Exception $e) {
    //         log_message('error', 'Error in search_tbl_w1: ' . $e->getMessage());
    //         return array();
    //     }
    // }

    // // tbl_d2 = เปลี่ยนแปลงประกาศเชิญชวน
    // public function get_tbl_d2_all()
    // {
    //     if (!$this->connection_status) {
    //         return array();
    //     }

    //     try {
    //         $data = array();
    //         $sql = "SELECT * FROM tbl_d2 ORDER BY item_date DESC";
    //         $result = @$this->remote_db->query($sql);
            
    //         if ($result && $result->num_rows > 0) {
    //             while ($row = $result->fetch_assoc()) {
    //                 $data[] = $row;
    //             }
    //             $result->free();
    //         }
            
    //         return $data;
            
    //     } catch (Exception $e) {
    //         log_message('error', 'Error in get_tbl_d2_all: ' . $e->getMessage());
    //         return array();
    //     }
    // }

    // public function search_tbl_d2($search, $start_date = null, $end_date = null)
    // {
    //     if (!$this->connection_status) {
    //         return array();
    //     }

    //     try {
    //         $search = $this->remote_db->real_escape_string($search);
            
    //         $sql = "SELECT * FROM tbl_d2 WHERE 1";

    //         if (!empty($search)) {
    //             $sql .= " AND item_title LIKE '%$search%'";
    //         }

    //         if (!empty($start_date) && !empty($end_date)) {
    //             $sql .= " AND item_date BETWEEN '$start_date' AND '$end_date'";
    //         } elseif (!empty($start_date)) {
    //             $sql .= " AND item_date >= '$start_date'";
    //         } elseif (!empty($end_date)) {
    //             $sql .= " AND item_date <= '$end_date'";
    //         }

    //         $sql .= " ORDER BY item_date DESC";

    //         $result = @$this->remote_db->query($sql);
    //         $data = array();
            
    //         if ($result && $result->num_rows > 0) {
    //             while ($row = $result->fetch_assoc()) {
    //                 $data[] = $row;
    //             }
    //             $result->free();
    //         }

    //         return $data;
            
    //     } catch (Exception $e) {
    //         log_message('error', 'Error in search_tbl_d2: ' . $e->getMessage());
    //         return array();
    //     }
    // }

    // // tbl_w2 = เปลี่ยนแปลงประกาศรายชื่อผู้ชนะการเสนอราคา
    // public function get_tbl_w2_all()
    // {
    //     if (!$this->connection_status) {
    //         return array();
    //     }

    //     try {
    //         $data = array();
    //         $sql = "SELECT * FROM tbl_w2 ORDER BY item_date DESC";
    //         $result = @$this->remote_db->query($sql);
            
    //         if ($result && $result->num_rows > 0) {
    //             while ($row = $result->fetch_assoc()) {
    //                 $data[] = $row;
    //             }
    //             $result->free();
    //         }
            
    //         return $data;
            
    //     } catch (Exception $e) {
    //         log_message('error', 'Error in get_tbl_w2_all: ' . $e->getMessage());
    //         return array();
    //     }
    // }

    // public function search_tbl_w2($search, $start_date = null, $end_date = null)
    // {
    //     if (!$this->connection_status) {
    //         return array();
    //     }

    //     try {
    //         $search = $this->remote_db->real_escape_string($search);
            
    //         $sql = "SELECT * FROM tbl_w2 WHERE 1";

    //         if (!empty($search)) {
    //             $sql .= " AND item_title LIKE '%$search%'";
    //         }

    //         if (!empty($start_date) && !empty($end_date)) {
    //             $sql .= " AND item_date BETWEEN '$start_date' AND '$end_date'";
    //         } elseif (!empty($start_date)) {
    //             $sql .= " AND item_date >= '$start_date'";
    //         } elseif (!empty($end_date)) {
    //             $sql .= " AND item_date <= '$end_date'";
    //         }

    //         $sql .= " ORDER BY item_date DESC";

    //         $result = @$this->remote_db->query($sql);
    //         $data = array();
            
    //         if ($result && $result->num_rows > 0) {
    //             while ($row = $result->fetch_assoc()) {
    //                 $data[] = $row;
    //             }
    //             $result->free();
    //         }

    //         return $data;
            
    //     } catch (Exception $e) {
    //         log_message('error', 'Error in search_tbl_w2: ' . $e->getMessage());
    //         return array();
    //     }
    // }

    // // Frontend functions
    // public function get_tbl_w0_frontend()
    // {
    //     if (!$this->connection_status) {
    //         return array();
    //     }

    //     try {
    //         $data = array();
    //         $sql = "SELECT * FROM tbl_w0 ORDER BY item_date DESC LIMIT 10";
    //         $result = @$this->remote_db->query($sql);
            
    //         if ($result && $result->num_rows > 0) {
    //             while ($row = $result->fetch_assoc()) {
    //                 $data[] = $row;
    //             }
    //             $result->free();
    //         }
            
    //         return $data;
            
    //     } catch (Exception $e) {
    //         log_message('error', 'Error in get_tbl_w0_frontend: ' . $e->getMessage());
    //         return array();
    //     }
    // }

    // public function get_tbl_p0_frontend()
    // {
    //     if (!$this->connection_status) {
    //         return array();
    //     }

    //     try {
    //         $data = array();
    //         $sql = "SELECT * FROM tbl_p0 ORDER BY item_date DESC LIMIT 10";
    //         $result = @$this->remote_db->query($sql);
            
    //         if ($result && $result->num_rows > 0) {
    //             while ($row = $result->fetch_assoc()) {
    //                 $data[] = $row;
    //             }
    //             $result->free();
    //         }
            
    //         return $data;
            
    //     } catch (Exception $e) {
    //         log_message('error', 'Error in get_tbl_p0_frontend: ' . $e->getMessage());
    //         return array();
    //     }
    // }

    // public function get_tbl_15_frontend()
    // {
    //     if (!$this->connection_status) {
    //         return array();
    //     }

    //     try {
    //         $data = array();
    //         $sql = "SELECT * FROM tbl_15 ORDER BY item_date DESC LIMIT 10";
    //         $result = @$this->remote_db->query($sql);
            
    //         if ($result && $result->num_rows > 0) {
    //             while ($row = $result->fetch_assoc()) {
    //                 $data[] = $row;
    //             }
    //             $result->free();
    //         }
            
    //         return $data;
            
    //     } catch (Exception $e) {
    //         log_message('error', 'Error in get_tbl_15_frontend: ' . $e->getMessage());
    //         return array();
    //     }
    // }

    // public function get_tbl_b0_frontend()
    // {
    //     if (!$this->connection_status) {
    //         return array();
    //     }

    //     try {
    //         $data = array();
    //         $sql = "SELECT * FROM tbl_b0 ORDER BY item_date DESC LIMIT 10";
    //         $result = @$this->remote_db->query($sql);
            
    //         if ($result && $result->num_rows > 0) {
    //             while ($row = $result->fetch_assoc()) {
    //                 $data[] = $row;
    //             }
    //             $result->free();
    //         }
            
    //         return $data;
            
    //     } catch (Exception $e) {
    //         log_message('error', 'Error in get_tbl_b0_frontend: ' . $e->getMessage());
    //         return array();
    //     }
    // }

    // // ฟังก์ชันลองเชื่อมต่อใหม่
    // public function reconnect()
    // {
    //     if ($this->connection_status && isset($this->remote_db)) {
    //         $this->remote_db->close();
    //     }
    //     return $this->connect_database();
    // }

    // public function __destruct()
    // {
    //     if ($this->connection_status && isset($this->remote_db)) {
    //         $this->remote_db->close();
    //     }
    // }
}