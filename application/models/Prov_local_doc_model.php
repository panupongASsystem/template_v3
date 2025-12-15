<?php
class Prov_local_doc_model extends CI_Model
{
    // private $remote_db;
    // private $connection_status = false;
    // private $table_name;
    
    // public function __construct()  // แก้ไข: เพิ่ม __ หน้า construct
    // {
    //     parent::__construct();
    //     $this->table_name = get_config_value('prov_local_doc_model');
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
    //             "db_prov_local_doc"
    //         );
            
    //         // ตรวจสอบการเชื่อมต่อ
    //         if ($this->remote_db->connect_error) {
    //             $this->connection_status = false;
    //             // บันทึก log แต่ไม่แสดง error
    //             log_message('error', 'Remote DB connection failed: ' . $this->remote_db->connect_error);
    //             return false;
    //         }
            
    //         // ตั้งค่า charset
    //         $this->remote_db->set_charset("utf8");
    //         $this->connection_status = true;
    //         return true;
            
    //     } catch (Exception $e) {
    //         $this->connection_status = false;
    //         log_message('error', 'Database connection exception: ' . $e->getMessage());
    //         return false;
    //     }
    // }
    
    // // ฟังก์ชันตรวจสอบสถานะการเชื่อมต่อ
    // public function is_connected()
    // {
    //     return $this->connection_status;
    // }
    
    // // ฟังก์ชันดึงข้อมูล ทั้งหมด
    // public function get_local_docs_all()
    // {
    //     // ตรวจสอบการเชื่อมต่อก่อน
    //     if (!$this->connection_status) {
    //         return array();
    //     }
        
    //     try {
    //         $data = array();
    //         $sql = "SELECT * FROM {$this->table_name} ORDER BY id DESC";
            
    //         // ใช้ @ เพื่อ suppress error
    //         $result = @$this->remote_db->query($sql);
            
    //         if ($result && $result->num_rows > 0) {
    //             while ($row = $result->fetch_assoc()) {
    //                 $data[] = $row;
    //             }
    //             $result->free();
    //         }
            
    //         return $data;
            
    //     } catch (Exception $e) {
    //         log_message('error', 'Error in get_local_docs_all: ' . $e->getMessage());
    //         return array();
    //     }
    // }
    
    // // ฟังก์ชันดึงข้อมูล หน้าแรก
    // public function get_local_docs()
    // {
    //     // ตรวจสอบการเชื่อมต่อก่อน
    //     if (!$this->connection_status) {
    //         return array();
    //     }
        
    //     try {
    //         $data = array();
    //         $sql = "SELECT * FROM {$this->table_name} ORDER BY id DESC LIMIT 13";
            
    //         // ใช้ @ เพื่อ suppress error
    //         $result = @$this->remote_db->query($sql);
            
    //         if ($result && $result->num_rows > 0) {
    //             while ($row = $result->fetch_assoc()) {
    //                 $data[] = $row;
    //             }
    //             $result->free();
    //         }
            
    //         return $data;
            
    //     } catch (Exception $e) {
    //         log_message('error', 'Error in get_local_docs: ' . $e->getMessage());
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