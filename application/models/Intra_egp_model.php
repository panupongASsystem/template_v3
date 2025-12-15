<?php
class Intra_egp_model extends CI_Model
{
    public function __construct()
    {
        parent::__construct();
        $this->load->model('space_model');
    }

    // ปี 2567 **************************************************
    // จำนวนโครงการทั้งหมด ####
    public function count_project_id_y2567()
    {
        return $this->db->count_all_results('tbl_bp_report_y2567');
    }
    // งบประมาณโครงการต่อปี ####
    // public function sum_project_money_y2567()
    // {
    //     // สร้างคำสั่ง SQL สำหรับ SUM() โดยไม่ใช้ REPLACE()
    //     $this->db->select_sum('project_money');

    //     // ดึงค่าผลรวมจากตาราง tbl_bp_report_y2567
    //     $query = $this->db->get('tbl_bp_report_y2567');

    //     // คืนค่าผลรวม
    //     return $query->row()->project_money;
    // }
    public function sum_project_money_without_comma_y2567()
    {
        // สร้างคำสั่ง SQL สำหรับ REPLACE() เพื่อลบเครื่องหมายคอมม่า
        $sql = "SELECT SUM(CAST(REPLACE(project_money, ',', '') AS DECIMAL(10,2))) AS sum_money FROM tbl_bp_report_y2567";

        // ส่งคำสั่ง SQL ไปยังฐานข้อมูล
        $query = $this->db->query($sql);

        // คืนค่าผลรวม
        return $query->row()->sum_money;
    }
    // ราคากลาง ####
    // public function sum_price_build_money_y2567()
    // {
    //     // สร้างคำสั่ง SQL สำหรับ SUM() โดยไม่ใช้ REPLACE()
    //     $this->db->select_sum('price_build');

    //     // ดึงค่าผลรวมจากตาราง tbl_bp_report_y2567
    //     $query = $this->db->get('tbl_bp_report_y2567');

    //     // คืนค่าผลรวม
    //     return $query->row()->sum_price_build;
    // }
    public function sum_price_build_money_without_comma_y2567()
    {
        // สร้างคำสั่ง SQL สำหรับ REPLACE() เพื่อลบเครื่องหมายคอมม่า
        $sql = "SELECT SUM(CAST(REPLACE(price_build, ',', '') AS DECIMAL(10,2))) AS sum_money FROM tbl_bp_report_y2567";

        // ส่งคำสั่ง SQL ไปยังฐานข้อมูล
        $query = $this->db->query($sql);

        // คืนค่าผลรวม
        return $query->row()->sum_money;
    }

    // สถานะโครงการ เงิน ระหว่างดำเนินการ ####
    public function sum_project_money_without_comma_by_status_process_y2567()
    {
        // สร้างคำสั่ง SQL สำหรับ REPLACE() เพื่อลบเครื่องหมายคอมม่า และมีเงื่อนไข WHERE project_status = 'ระหว่างดำเนินการ'
        $sql = "SELECT SUM(CAST(REPLACE(project_money, ',', '') AS DECIMAL(10,2))) AS sum_money FROM tbl_bp_report_y2567 WHERE project_status = 'ระหว่างดำเนินการ'";

        // ส่งคำสั่ง SQL ไปยังฐานข้อมูล
        $query = $this->db->query($sql);

        // คืนค่าผลรวม
        return $query->row()->sum_money;
    }
    // สถานะโครงการ เงิน สิ้นสุดสัญญา ####
    public function sum_project_money_without_comma_by_status_end_y2567()
    {
        // สร้างคำสั่ง SQL สำหรับ REPLACE() เพื่อลบเครื่องหมายคอมม่า และมีเงื่อนไข WHERE project_status = 'ระหว่างดำเนินการ'
        $sql = "SELECT SUM(CAST(REPLACE(project_money, ',', '') AS DECIMAL(10,2))) AS sum_money FROM tbl_bp_report_y2567 WHERE project_status = 'สิ้นสุดสัญญา'";

        // ส่งคำสั่ง SQL ไปยังฐานข้อมูล
        $query = $this->db->query($sql);

        // คืนค่าผลรวม
        return $query->row()->sum_money;
    }

    // สถานะโครงการ จำนวน ระหว่างดำเนินการ ####
    public function count_projects_status_by_process_y2567()
    {
        // สร้างคำสั่ง SQL สำหรับ COUNT() โดยไม่ใช้ REPLACE()
        $this->db->select('COUNT(project_id) as sum_project_status_process');
        $this->db->where('project_status', 'ระหว่างดำเนินการ');

        // ดึงจำนวน project_id ที่มี project_status เป็น 'ระหว่างดำเนินการ' จากตาราง tbl_bp_report_y2567
        $query = $this->db->get('tbl_bp_report_y2567');

        // คืนค่าผลรวม
        return $query->row()->sum_project_status_process;
    }

    // สถานะโครงการ จำนวน สิ้นสุดสัญญา ####
    public function count_projects_status_by_end_y2567()
    {
        // สร้างคำสั่ง SQL สำหรับ COUNT() โดยไม่ใช้ REPLACE()
        $this->db->select('COUNT(project_id) as sum_project_status_end');
        $this->db->where('project_status', 'สิ้นสุดสัญญา');

        // ดึงจำนวน project_id ที่มี project_status เป็น 'สิ้นสุดสัญญา' จากตาราง tbl_bp_report_y2567
        $query = $this->db->get('tbl_bp_report_y2567');

        // คืนค่าผลรวม
        return $query->row()->sum_project_status_end;
    }

    // สถานะโครงการ จำนวน ทั้งหมด ####
    public function count_projects_status_by_all_y2567()
    {
        // สร้างคำสั่ง SQL สำหรับ COUNT() โดยไม่ใช้ REPLACE()
        $this->db->select('COUNT(project_id) as sum_project_status_all');

        // ดึงจำนวน project_id ที่มี project_status เป็น 'สิ้นสุดสัญญา' จากตาราง tbl_bp_report_y2567
        $query = $this->db->get('tbl_bp_report_y2567');

        // คืนค่าผลรวม
        return $query->row()->sum_project_status_all;
    }

    // ประเภทโครงการ เงิน เช่า ####
    public function sum_project_money_without_comma_by_type_rent_y2567()
    {
        $sql = "SELECT SUM(CAST(REPLACE(project_money, ',', '') AS DECIMAL(10,2))) AS sum_money FROM tbl_bp_report_y2567 WHERE project_type_name = 'เช่า'";

        // ส่งคำสั่ง SQL ไปยังฐานข้อมูล
        $query = $this->db->query($sql);

        // คืนค่าผลรวม
        return $query->row()->sum_money;
    }
    // ประเภทโครงการ จ้างก่อสร้าง ####
    public function sum_project_money_without_comma_by_type_construction_y2567()
    {
        $sql = "SELECT SUM(CAST(REPLACE(project_money, ',', '') AS DECIMAL(10,2))) AS sum_money FROM tbl_bp_report_y2567 WHERE project_type_name = 'จ้างก่อสร้าง'";

        // ส่งคำสั่ง SQL ไปยังฐานข้อมูล
        $query = $this->db->query($sql);

        // คืนค่าผลรวม
        return $query->row()->sum_money;
    }

    // ประเภทโครงการ เงิน จ้างทำของ/จ้างเหมาบริการ ####
    public function sum_project_money_without_comma_by_type_s_contractor_y2567()
    {
        $sql = "SELECT SUM(CAST(REPLACE(project_money, ',', '') AS DECIMAL(10,2))) AS sum_money FROM tbl_bp_report_y2567 WHERE project_type_name = 'จ้างทำของ/จ้างเหมาบริการ'";

        // ส่งคำสั่ง SQL ไปยังฐานข้อมูล
        $query = $this->db->query($sql);

        // คืนค่าผลรวม
        return $query->row()->sum_money;
    }

    // ประเภทโครงการ เงิน ซื้อ ####
    public function sum_project_money_without_comma_by_type_buy_y2567()
    {
        $sql = "SELECT SUM(CAST(REPLACE(project_money, ',', '') AS DECIMAL(10,2))) AS sum_money FROM tbl_bp_report_y2567 WHERE project_type_name = 'ซื้อ'";

        // ส่งคำสั่ง SQL ไปยังฐานข้อมูล
        $query = $this->db->query($sql);

        // คืนค่าผลรวม
        return $query->row()->sum_money;
    }

    // ประเภทโครงการ จำนวน เช่า ####
    public function count_projects_type_by_rent_y2567()
    {
        $this->db->select('COUNT(project_id) as sum_project_type_rent');
        $this->db->where('project_type_name', 'เช่า');
        $query = $this->db->get('tbl_bp_report_y2567');
        return $query->row()->sum_project_type_rent;
    }

    // ประเภทโครงการ จำนวน จ้างก่อสร้าง ####
    public function count_projects_type_by_construction_y2567()
    {
        $this->db->select('COUNT(project_id) as sum_project_type_construction');
        $this->db->where('project_type_name', 'จ้างก่อสร้าง');
        $query = $this->db->get('tbl_bp_report_y2567');
        return $query->row()->sum_project_type_construction;
    }

    // ประเภทโครงการ จำนวน จ้างทำของ/จ้างเหมาบริการ ####
    public function count_projects_type_by_s_contractor_y2567()
    {
        $this->db->select('COUNT(project_id) as sum_project_type_s_contractor');
        $this->db->where('project_type_name', 'จ้างทำของ/จ้างเหมาบริการ');
        $query = $this->db->get('tbl_bp_report_y2567');
        return $query->row()->sum_project_type_s_contractor;
    }

    // ประเภทโครงการ จำนวน ซื้อ ####
    public function count_projects_type_by_buy_y2567()
    {
        $this->db->select('COUNT(project_id) as sum_project_type_buy');
        $this->db->where('project_type_name', 'ซื้อ');
        $query = $this->db->get('tbl_bp_report_y2567');
        return $query->row()->sum_project_type_buy;
    }

    // วิธีจัดซื้อจัดจ้าง เงิน อื่นๆ ####
    public function sum_project_money_without_comma_by_purchase_other_y2567()
    {
        $sql = "SELECT SUM(CAST(REPLACE(project_money, ',', '') AS DECIMAL(10,2))) AS sum_money 
                 FROM tbl_bp_report_y2567 
                 WHERE purchase_method_name NOT IN ('ประกวดราคาอิเล็กทรอนิกส์ (e-bidding)', 'เฉพาะเจาะจง')";
        $query = $this->db->query($sql);
        return $query->row()->sum_money;
    }

    // วิธีจัดซื้อจัดจ้าง เงิน ประกวดราคาอิเล็กทรอนิกส์ (e-bidding) ####
    public function sum_project_money_without_comma_by_purchase_e_bidding_y2567()
    {
        $sql = "SELECT SUM(CAST(REPLACE(project_money, ',', '') AS DECIMAL(10,2))) AS sum_money FROM tbl_bp_report_y2567 WHERE purchase_method_name = 'ประกวดราคาอิเล็กทรอนิกส์ (e-bidding)'";
        $query = $this->db->query($sql);
        return $query->row()->sum_money;
    }

    // วิธีจัดซื้อจัดจ้าง เงิน เฉพาะเจาะจง ####
    public function sum_project_money_without_comma_by_purchase_specific_y2567()
    {
        $sql = "SELECT SUM(CAST(REPLACE(project_money, ',', '') AS DECIMAL(10,2))) AS sum_money FROM tbl_bp_report_y2567 WHERE purchase_method_name = 'เฉพาะเจาะจง'";
        $query = $this->db->query($sql);
        return $query->row()->sum_money;
    }

    // วิธีจัดซื้อจัดจ้าง จำนวน อื่นๆ ####
    public function count_projects_purchase_by_other_y2567()
    {
        $this->db->select('COUNT(project_id) as sum_project_purchase_by_other');
        $this->db->where_not_in('purchase_method_name', array('ประกวดราคาอิเล็กทรอนิกส์ (e-bidding)', 'เฉพาะเจาะจง'));
        $query = $this->db->get('tbl_bp_report_y2567');
        return $query->row()->sum_project_purchase_by_other;
    }

    // วิธีจัดซื้อจัดจ้าง จำนวน E-Bidding ####
    public function count_projects_purchase_by_e_bidding_y2567()
    {
        $this->db->select('COUNT(project_id) as sum_project_purchase_by_e_bidding');
        $this->db->where('purchase_method_name', 'ประกวดราคาอิเล็กทรอนิกส์ (e-bidding)');
        $query = $this->db->get('tbl_bp_report_y2567');
        return $query->row()->sum_project_purchase_by_e_bidding;
    }
    // วิธีจัดซื้อจัดจ้าง จำนวน เฉพาะเจาะจง ####
    public function count_projects_purchase_by_specific_y2567()
    {
        $this->db->select('COUNT(project_id) as sum_project_purchase_by_specific');
        $this->db->where('purchase_method_name', 'เฉพาะเจาะจง');
        $query = $this->db->get('tbl_bp_report_y2567');
        return $query->row()->sum_project_purchase_by_specific;
    }


    // ใช้จ่ายงบประมาณรายเดือน สิ้นสุดสัญญา ####
    public function sum_project_money_by_project_status_end_y2567()
    {
        $query = "WITH months AS (
            SELECT 'ต.ค. 66' AS month
            UNION SELECT 'พ.ย. 66'
            UNION SELECT 'ธ.ค. 66'
            UNION SELECT 'ม.ค. 67'
            UNION SELECT 'ก.พ. 67'
            UNION SELECT 'มี.ค. 67'
            UNION SELECT 'เม.ย. 67'
            UNION SELECT 'พ.ค. 67'
            UNION SELECT 'มิ.ย. 67'
            UNION SELECT 'ก.ค. 67'
            UNION SELECT 'ส.ค. 67'
            UNION SELECT 'ก.ย. 67'
            UNION SELECT 'ต.ค. 67'
            UNION SELECT 'พ.ย. 67'
            UNION SELECT 'ธ.ค. 67'
        )
        SELECT 
            months.month,
            COALESCE(SUM(CAST(REPLACE(tbl_bp_report_y2567.project_money, ',', '') AS DECIMAL(10,2))), 0) AS sum_money
        FROM 
            months
        LEFT JOIN 
            tbl_bp_report_y2567 ON 
                (transaction_date LIKE CONCAT('%', SUBSTRING_INDEX(months.month, ' ', 1), '%', SUBSTRING_INDEX(months.month, ' ', -1), '%') 
                AND project_status = 'สิ้นสุดสัญญา')
        GROUP BY 
            months.month
        ORDER BY 
            FIELD(months.month, 'ต.ค. 66', 'พ.ย. 66', 'ธ.ค. 66', 'ม.ค. 67', 'ก.พ. 67', 'มี.ค. 67', 'เม.ย. 67', 'พ.ค. 67', 'มิ.ย. 67', 'ก.ค. 67', 'ส.ค. 67', 'ก.ย. 67', 'ต.ค. 67', 'พ.ย. 67', 'ธ.ค. 67');";

        $result = $this->db->query($query);

        if ($result->num_rows() > 0) {
            return $result->result_array();
        } else {
            return array();
        }
    }

    // ใช้จ่ายงบประมาณรายเดือน ระหว่างดำเนินการ ####
    public function sum_project_money_by_project_status_process_y2567()
    {
        $query = "WITH months AS (
            SELECT 'ต.ค. 66' AS month
            UNION SELECT 'พ.ย. 66'
            UNION SELECT 'ธ.ค. 66'
            UNION SELECT 'ม.ค. 67'
            UNION SELECT 'ก.พ. 67'
            UNION SELECT 'มี.ค. 67'
            UNION SELECT 'เม.ย. 67'
            UNION SELECT 'พ.ค. 67'
            UNION SELECT 'มิ.ย. 67'
            UNION SELECT 'ก.ค. 67'
            UNION SELECT 'ส.ค. 67'
            UNION SELECT 'ก.ย. 67'
            UNION SELECT 'ต.ค. 67'
            UNION SELECT 'พ.ย. 67'
            UNION SELECT 'ธ.ค. 67'
        )
        SELECT 
            months.month,
            COALESCE(SUM(CAST(REPLACE(tbl_bp_report_y2567.project_money, ',', '') AS DECIMAL(10,2))), 0) AS sum_money
        FROM 
            months
        LEFT JOIN 
            tbl_bp_report_y2567 ON 
                (transaction_date LIKE CONCAT('%', SUBSTRING_INDEX(months.month, ' ', 1), '%', SUBSTRING_INDEX(months.month, ' ', -1), '%') 
                AND project_status = 'ระหว่างดำเนินการ')
        GROUP BY 
            months.month
        ORDER BY 
            FIELD(months.month, 'ต.ค. 66', 'พ.ย. 66', 'ธ.ค. 66', 'ม.ค. 67', 'ก.พ. 67', 'มี.ค. 67', 'เม.ย. 67', 'พ.ค. 67', 'มิ.ย. 67', 'ก.ค. 67', 'ส.ค. 67', 'ก.ย. 67', 'ต.ค. 67', 'พ.ย. 67', 'ธ.ค. 67');";

        $result = $this->db->query($query);

        if ($result->num_rows() > 0) {
            return $result->result_array();
        } else {
            return array();
        }
    }


    // ปี 2566 **************************************************
    // จำนวนโครงการทั้งหมด ####
    public function count_project_id_y2566()
    {
        return $this->db->count_all_results('tbl_bp_report_y2566');
    }
    // งบประมาณโครงการต่อปี ####
    // public function sum_project_money_y2566()
    // {
    //     // สร้างคำสั่ง SQL สำหรับ SUM() โดยไม่ใช้ REPLACE()
    //     $this->db->select_sum('project_money');

    //     // ดึงค่าผลรวมจากตาราง tbl_bp_report_y2566
    //     $query = $this->db->get('tbl_bp_report_y2566');

    //     // คืนค่าผลรวม
    //     return $query->row()->project_money;
    // }

    public function sum_project_money_without_comma_y2566()
    {
        // สร้างคำสั่ง SQL สำหรับ REPLACE() เพื่อลบเครื่องหมายคอมม่า
        $sql = "SELECT SUM(CAST(REPLACE(project_money, ',', '') AS DECIMAL(10,2))) AS sum_money FROM tbl_bp_report_y2566";

        // ส่งคำสั่ง SQL ไปยังฐานข้อมูล
        $query = $this->db->query($sql);

        // คืนค่าผลรวม
        return $query->row()->sum_money;
    }
    // ราคากลาง ####
    // public function sum_price_build_money_y2566()
    // {
    //     // สร้างคำสั่ง SQL สำหรับ SUM() โดยไม่ใช้ REPLACE()
    //     $this->db->select_sum('price_build');

    //     // ดึงค่าผลรวมจากตาราง tbl_bp_report_y2566
    //     $query = $this->db->get('tbl_bp_report_y2566');

    //     // คืนค่าผลรวม
    //     return $query->row()->sum_price_build;
    // }
    public function sum_price_build_money_without_comma_y2566()
    {
        // สร้างคำสั่ง SQL สำหรับ REPLACE() เพื่อลบเครื่องหมายคอมม่า
        $sql = "SELECT SUM(CAST(REPLACE(price_build, ',', '') AS DECIMAL(10,2))) AS sum_money FROM tbl_bp_report_y2566";

        // ส่งคำสั่ง SQL ไปยังฐานข้อมูล
        $query = $this->db->query($sql);

        // คืนค่าผลรวม
        return $query->row()->sum_money;
    }

    // สถานะโครงการ เงิน ระหว่างดำเนินการ ####
    public function sum_project_money_without_comma_by_status_process_y2566()
    {
        // สร้างคำสั่ง SQL สำหรับ REPLACE() เพื่อลบเครื่องหมายคอมม่า และมีเงื่อนไข WHERE project_status = 'ระหว่างดำเนินการ'
        $sql = "SELECT SUM(CAST(REPLACE(project_money, ',', '') AS DECIMAL(10,2))) AS sum_money FROM tbl_bp_report_y2566 WHERE project_status = 'ระหว่างดำเนินการ'";

        // ส่งคำสั่ง SQL ไปยังฐานข้อมูล
        $query = $this->db->query($sql);

        // คืนค่าผลรวม
        return $query->row()->sum_money;
    }
    // สถานะโครงการ เงิน สิ้นสุดสัญญา ####
    public function sum_project_money_without_comma_by_status_end_y2566()
    {
        // สร้างคำสั่ง SQL สำหรับ REPLACE() เพื่อลบเครื่องหมายคอมม่า และมีเงื่อนไข WHERE project_status = 'ระหว่างดำเนินการ'
        $sql = "SELECT SUM(CAST(REPLACE(project_money, ',', '') AS DECIMAL(10,2))) AS sum_money FROM tbl_bp_report_y2566 WHERE project_status = 'สิ้นสุดสัญญา'";

        // ส่งคำสั่ง SQL ไปยังฐานข้อมูล
        $query = $this->db->query($sql);

        // คืนค่าผลรวม
        return $query->row()->sum_money;
    }

    // สถานะโครงการ จำนวน ระหว่างดำเนินการ ####
    public function count_projects_status_by_process_y2566()
    {
        // สร้างคำสั่ง SQL สำหรับ COUNT() โดยไม่ใช้ REPLACE()
        $this->db->select('COUNT(project_id) as sum_project_status_process');
        $this->db->where('project_status', 'ระหว่างดำเนินการ');

        // ดึงจำนวน project_id ที่มี project_status เป็น 'ระหว่างดำเนินการ' จากตาราง tbl_bp_report_y2566
        $query = $this->db->get('tbl_bp_report_y2566');

        // คืนค่าผลรวม
        return $query->row()->sum_project_status_process;
    }

    // สถานะโครงการ จำนวน สิ้นสุดสัญญา ####
    public function count_projects_status_by_end_y2566()
    {
        // สร้างคำสั่ง SQL สำหรับ COUNT() โดยไม่ใช้ REPLACE()
        $this->db->select('COUNT(project_id) as sum_project_status_end');
        $this->db->where('project_status', 'สิ้นสุดสัญญา');

        // ดึงจำนวน project_id ที่มี project_status เป็น 'สิ้นสุดสัญญา' จากตาราง tbl_bp_report_y2566
        $query = $this->db->get('tbl_bp_report_y2566');

        // คืนค่าผลรวม
        return $query->row()->sum_project_status_end;
    }

    // สถานะโครงการ จำนวน ทั้งหมด ####
    public function count_projects_status_by_all_y2566()
    {
        // สร้างคำสั่ง SQL สำหรับ COUNT() โดยไม่ใช้ REPLACE()
        $this->db->select('COUNT(project_id) as sum_project_status_all');

        // ดึงจำนวน project_id ที่มี project_status เป็น 'สิ้นสุดสัญญา' จากตาราง tbl_bp_report_y2566
        $query = $this->db->get('tbl_bp_report_y2566');

        // คืนค่าผลรวม
        return $query->row()->sum_project_status_all;
    }

    // ประเภทโครงการ เงิน เช่า ####
    public function sum_project_money_without_comma_by_type_rent_y2566()
    {
        $sql = "SELECT SUM(CAST(REPLACE(project_money, ',', '') AS DECIMAL(10,2))) AS sum_money FROM tbl_bp_report_y2566 WHERE project_type_name = 'เช่า'";

        // ส่งคำสั่ง SQL ไปยังฐานข้อมูล
        $query = $this->db->query($sql);

        // คืนค่าผลรวม
        return $query->row()->sum_money;
    }
    // ประเภทโครงการ จ้างก่อสร้าง ####
    public function sum_project_money_without_comma_by_type_construction_y2566()
    {
        $sql = "SELECT SUM(CAST(REPLACE(project_money, ',', '') AS DECIMAL(10,2))) AS sum_money FROM tbl_bp_report_y2566 WHERE project_type_name = 'จ้างก่อสร้าง'";

        // ส่งคำสั่ง SQL ไปยังฐานข้อมูล
        $query = $this->db->query($sql);

        // คืนค่าผลรวม
        return $query->row()->sum_money;
    }

    // ประเภทโครงการ เงิน จ้างทำของ/จ้างเหมาบริการ ####
    public function sum_project_money_without_comma_by_type_s_contractor_y2566()
    {
        $sql = "SELECT SUM(CAST(REPLACE(project_money, ',', '') AS DECIMAL(10,2))) AS sum_money FROM tbl_bp_report_y2566 WHERE project_type_name = 'จ้างทำของ/จ้างเหมาบริการ'";

        // ส่งคำสั่ง SQL ไปยังฐานข้อมูล
        $query = $this->db->query($sql);

        // คืนค่าผลรวม
        return $query->row()->sum_money;
    }

    // ประเภทโครงการ เงิน ซื้อ ####
    public function sum_project_money_without_comma_by_type_buy_y2566()
    {
        $sql = "SELECT SUM(CAST(REPLACE(project_money, ',', '') AS DECIMAL(10,2))) AS sum_money FROM tbl_bp_report_y2566 WHERE project_type_name = 'ซื้อ'";

        // ส่งคำสั่ง SQL ไปยังฐานข้อมูล
        $query = $this->db->query($sql);

        // คืนค่าผลรวม
        return $query->row()->sum_money;
    }

    // ประเภทโครงการ จำนวน เช่า ####
    public function count_projects_type_by_rent_y2566()
    {
        $this->db->select('COUNT(project_id) as sum_project_type_rent');
        $this->db->where('project_type_name', 'เช่า');
        $query = $this->db->get('tbl_bp_report_y2566');
        return $query->row()->sum_project_type_rent;
    }

    // ประเภทโครงการ จำนวน จ้างก่อสร้าง ####
    public function count_projects_type_by_construction_y2566()
    {
        $this->db->select('COUNT(project_id) as sum_project_type_construction');
        $this->db->where('project_type_name', 'จ้างก่อสร้าง');
        $query = $this->db->get('tbl_bp_report_y2566');
        return $query->row()->sum_project_type_construction;
    }

    // ประเภทโครงการ จำนวน จ้างทำของ/จ้างเหมาบริการ ####
    public function count_projects_type_by_s_contractor_y2566()
    {
        $this->db->select('COUNT(project_id) as sum_project_type_s_contractor');
        $this->db->where('project_type_name', 'จ้างทำของ/จ้างเหมาบริการ');
        $query = $this->db->get('tbl_bp_report_y2566');
        return $query->row()->sum_project_type_s_contractor;
    }

    // ประเภทโครงการ จำนวน ซื้อ ####
    public function count_projects_type_by_buy_y2566()
    {
        $this->db->select('COUNT(project_id) as sum_project_type_buy');
        $this->db->where('project_type_name', 'ซื้อ');
        $query = $this->db->get('tbl_bp_report_y2566');
        return $query->row()->sum_project_type_buy;
    }

    // วิธีจัดซื้อจัดจ้าง เงิน อื่นๆ ####
    public function sum_project_money_without_comma_by_purchase_other_y2566()
    {
        $sql = "SELECT SUM(CAST(REPLACE(project_money, ',', '') AS DECIMAL(10,2))) AS sum_money 
                   FROM tbl_bp_report_y2566 
                   WHERE purchase_method_name NOT IN ('ประกวดราคาอิเล็กทรอนิกส์ (e-bidding)', 'เฉพาะเจาะจง')";
        $query = $this->db->query($sql);
        return $query->row()->sum_money;
    }

    // วิธีจัดซื้อจัดจ้าง เงิน ประกวดราคาอิเล็กทรอนิกส์ (e-bidding) ####
    public function sum_project_money_without_comma_by_purchase_e_bidding_y2566()
    {
        $sql = "SELECT SUM(CAST(REPLACE(project_money, ',', '') AS DECIMAL(10,2))) AS sum_money FROM tbl_bp_report_y2566 WHERE purchase_method_name = 'ประกวดราคาอิเล็กทรอนิกส์ (e-bidding)'";
        $query = $this->db->query($sql);
        return $query->row()->sum_money;
    }

    // วิธีจัดซื้อจัดจ้าง เงิน เฉพาะเจาะจง ####
    public function sum_project_money_without_comma_by_purchase_specific_y2566()
    {
        $sql = "SELECT SUM(CAST(REPLACE(project_money, ',', '') AS DECIMAL(10,2))) AS sum_money FROM tbl_bp_report_y2566 WHERE purchase_method_name = 'เฉพาะเจาะจง'";
        $query = $this->db->query($sql);
        return $query->row()->sum_money;
    }

    // วิธีจัดซื้อจัดจ้าง จำนวน อื่นๆ ####
    public function count_projects_purchase_by_other_y2566()
    {
        $this->db->select('COUNT(project_id) as sum_project_purchase_by_other');
        $this->db->where_not_in('purchase_method_name', array('ประกวดราคาอิเล็กทรอนิกส์ (e-bidding)', 'เฉพาะเจาะจง'));
        $query = $this->db->get('tbl_bp_report_y2566');
        return $query->row()->sum_project_purchase_by_other;
    }

    // วิธีจัดซื้อจัดจ้าง จำนวน E-Bidding ####
    public function count_projects_purchase_by_e_bidding_y2566()
    {
        $this->db->select('COUNT(project_id) as sum_project_purchase_by_e_bidding');
        $this->db->where('purchase_method_name', 'ประกวดราคาอิเล็กทรอนิกส์ (e-bidding)');
        $query = $this->db->get('tbl_bp_report_y2566');
        return $query->row()->sum_project_purchase_by_e_bidding;
    }
    // วิธีจัดซื้อจัดจ้าง จำนวน เฉพาะเจาะจง ####
    public function count_projects_purchase_by_specific_y2566()
    {
        $this->db->select('COUNT(project_id) as sum_project_purchase_by_specific');
        $this->db->where('purchase_method_name', 'เฉพาะเจาะจง');
        $query = $this->db->get('tbl_bp_report_y2566');
        return $query->row()->sum_project_purchase_by_specific;
    }


   // ใช้จ่ายงบประมาณรายเดือน สิ้นสุดสัญญา ####
   public function sum_project_money_by_project_status_end_y2566()
   {
       $query = "WITH months AS (
           SELECT 'ต.ค. 65' AS month
           UNION SELECT 'พ.ย. 65'
           UNION SELECT 'ธ.ค. 65'
           UNION SELECT 'ม.ค. 66'
           UNION SELECT 'ก.พ. 66'
           UNION SELECT 'มี.ค. 66'
           UNION SELECT 'เม.ย. 66'
           UNION SELECT 'พ.ค. 66'
           UNION SELECT 'มิ.ย. 66'
           UNION SELECT 'ก.ค. 66'
           UNION SELECT 'ส.ค. 66'
           UNION SELECT 'ก.ย. 66'
           UNION SELECT 'ต.ค. 66'
           UNION SELECT 'พ.ย. 66'
           UNION SELECT 'ธ.ค. 66'
       )
       SELECT 
           months.month,
           COALESCE(SUM(CAST(REPLACE(tbl_bp_report_y2566.project_money, ',', '') AS DECIMAL(10,2))), 0) AS sum_money
       FROM 
           months
       LEFT JOIN 
           tbl_bp_report_y2566 ON 
               (transaction_date LIKE CONCAT('%', SUBSTRING_INDEX(months.month, ' ', 1), '%', SUBSTRING_INDEX(months.month, ' ', -1), '%') 
               AND project_status = 'สิ้นสุดสัญญา')
       GROUP BY 
           months.month
       ORDER BY 
           FIELD(months.month, 'ต.ค. 65', 'พ.ย. 65', 'ธ.ค. 65', 'ม.ค. 66', 'ก.พ. 66', 'มี.ค. 66', 'เม.ย. 66', 'พ.ค. 66', 'มิ.ย. 66', 'ก.ค. 66', 'ส.ค. 66', 'ก.ย. 66', 'ต.ค. 66', 'พ.ย. 66', 'ธ.ค. 66');";

       $result = $this->db->query($query);

       if ($result->num_rows() > 0) {
           return $result->result_array();
       } else {
           return array();
       }
   }

   // ใช้จ่ายงบประมาณรายเดือน ระหว่างดำเนินการ ####
   public function sum_project_money_by_project_status_process_y2566()
   {
       $query = "WITH months AS (
           SELECT 'ต.ค. 65' AS month
           UNION SELECT 'พ.ย. 65'
           UNION SELECT 'ธ.ค. 65'
           UNION SELECT 'ม.ค. 66'
           UNION SELECT 'ก.พ. 66'
           UNION SELECT 'มี.ค. 66'
           UNION SELECT 'เม.ย. 66'
           UNION SELECT 'พ.ค. 66'
           UNION SELECT 'มิ.ย. 66'
           UNION SELECT 'ก.ค. 66'
           UNION SELECT 'ส.ค. 66'
           UNION SELECT 'ก.ย. 66'
           UNION SELECT 'ต.ค. 66'
           UNION SELECT 'พ.ย. 66'
           UNION SELECT 'ธ.ค. 66'
       )
       SELECT 
           months.month,
           COALESCE(SUM(CAST(REPLACE(tbl_bp_report_y2566.project_money, ',', '') AS DECIMAL(10,2))), 0) AS sum_money
       FROM 
           months
       LEFT JOIN 
           tbl_bp_report_y2566 ON 
               (transaction_date LIKE CONCAT('%', SUBSTRING_INDEX(months.month, ' ', 1), '%', SUBSTRING_INDEX(months.month, ' ', -1), '%') 
               AND project_status = 'ระหว่างดำเนินการ')
       GROUP BY 
           months.month
       ORDER BY 
           FIELD(months.month, 'ต.ค. 65', 'พ.ย. 65', 'ธ.ค. 65', 'ม.ค. 66', 'ก.พ. 66', 'มี.ค. 66', 'เม.ย. 66', 'พ.ค. 66', 'มิ.ย. 66', 'ก.ค. 66', 'ส.ค. 66', 'ก.ย. 66', 'ต.ค. 66', 'พ.ย. 66', 'ธ.ค. 66');";

       $result = $this->db->query($query);

       if ($result->num_rows() > 0) {
           return $result->result_array();
       } else {
           return array();
       }
   }
   
    // ปี 2565 **************************************************
    // จำนวนโครงการทั้งหมด ####
    public function count_project_id_y2565()
    {
        return $this->db->count_all_results('tbl_bp_report_y2565');
    }
    // งบประมาณโครงการต่อปี ####
    // public function sum_project_money_y2565()
    // {
    //     // สร้างคำสั่ง SQL สำหรับ SUM() โดยไม่ใช้ REPLACE()
    //     $this->db->select_sum('project_money');

    //     // ดึงค่าผลรวมจากตาราง tbl_bp_report_y2565
    //     $query = $this->db->get('tbl_bp_report_y2565');

    //     // คืนค่าผลรวม
    //     return $query->row()->project_money;
    // }
    public function sum_project_money_without_comma_y2565()
    {
        // สร้างคำสั่ง SQL สำหรับ REPLACE() เพื่อลบเครื่องหมายคอมม่า
        $sql = "SELECT SUM(CAST(REPLACE(project_money, ',', '') AS DECIMAL(10,2))) AS sum_money FROM tbl_bp_report_y2565";

        // ส่งคำสั่ง SQL ไปยังฐานข้อมูล
        $query = $this->db->query($sql);

        // คืนค่าผลรวม
        return $query->row()->sum_money;
    }
    // ราคากลาง ####
    // public function sum_price_build_money_y2565()
    // {
    //     // สร้างคำสั่ง SQL สำหรับ SUM() โดยไม่ใช้ REPLACE()
    //     $this->db->select_sum('price_build');

    //     // ดึงค่าผลรวมจากตาราง tbl_bp_report_y2565
    //     $query = $this->db->get('tbl_bp_report_y2565');

    //     // คืนค่าผลรวม
    //     return $query->row()->sum_price_build;
    // }
    public function sum_price_build_money_without_comma_y2565()
    {
        // สร้างคำสั่ง SQL สำหรับ REPLACE() เพื่อลบเครื่องหมายคอมม่า
        $sql = "SELECT SUM(CAST(REPLACE(price_build, ',', '') AS DECIMAL(10,2))) AS sum_money FROM tbl_bp_report_y2565";

        // ส่งคำสั่ง SQL ไปยังฐานข้อมูล
        $query = $this->db->query($sql);

        // คืนค่าผลรวม
        return $query->row()->sum_money;
    }

    // สถานะโครงการ เงิน ระหว่างดำเนินการ ####
    public function sum_project_money_without_comma_by_status_process_y2565()
    {
        // สร้างคำสั่ง SQL สำหรับ REPLACE() เพื่อลบเครื่องหมายคอมม่า และมีเงื่อนไข WHERE project_status = 'ระหว่างดำเนินการ'
        $sql = "SELECT SUM(CAST(REPLACE(project_money, ',', '') AS DECIMAL(10,2))) AS sum_money FROM tbl_bp_report_y2565 WHERE project_status = 'ระหว่างดำเนินการ'";

        // ส่งคำสั่ง SQL ไปยังฐานข้อมูล
        $query = $this->db->query($sql);

        // คืนค่าผลรวม
        return $query->row()->sum_money;
    }
    // สถานะโครงการ เงิน สิ้นสุดสัญญา ####
    public function sum_project_money_without_comma_by_status_end_y2565()
    {
        // สร้างคำสั่ง SQL สำหรับ REPLACE() เพื่อลบเครื่องหมายคอมม่า และมีเงื่อนไข WHERE project_status = 'ระหว่างดำเนินการ'
        $sql = "SELECT SUM(CAST(REPLACE(project_money, ',', '') AS DECIMAL(10,2))) AS sum_money FROM tbl_bp_report_y2565 WHERE project_status = 'สิ้นสุดสัญญา'";

        // ส่งคำสั่ง SQL ไปยังฐานข้อมูล
        $query = $this->db->query($sql);

        // คืนค่าผลรวม
        return $query->row()->sum_money;
    }

    // สถานะโครงการ จำนวน ระหว่างดำเนินการ ####
    public function count_projects_status_by_process_y2565()
    {
        // สร้างคำสั่ง SQL สำหรับ COUNT() โดยไม่ใช้ REPLACE()
        $this->db->select('COUNT(project_id) as sum_project_status_process');
        $this->db->where('project_status', 'ระหว่างดำเนินการ');

        // ดึงจำนวน project_id ที่มี project_status เป็น 'ระหว่างดำเนินการ' จากตาราง tbl_bp_report_y2565
        $query = $this->db->get('tbl_bp_report_y2565');

        // คืนค่าผลรวม
        return $query->row()->sum_project_status_process;
    }

    // สถานะโครงการ จำนวน สิ้นสุดสัญญา ####
    public function count_projects_status_by_end_y2565()
    {
        // สร้างคำสั่ง SQL สำหรับ COUNT() โดยไม่ใช้ REPLACE()
        $this->db->select('COUNT(project_id) as sum_project_status_end');
        $this->db->where('project_status', 'สิ้นสุดสัญญา');

        // ดึงจำนวน project_id ที่มี project_status เป็น 'สิ้นสุดสัญญา' จากตาราง tbl_bp_report_y2565
        $query = $this->db->get('tbl_bp_report_y2565');

        // คืนค่าผลรวม
        return $query->row()->sum_project_status_end;
    }

    // สถานะโครงการ จำนวน ทั้งหมด ####
    public function count_projects_status_by_all_y2565()
    {
        // สร้างคำสั่ง SQL สำหรับ COUNT() โดยไม่ใช้ REPLACE()
        $this->db->select('COUNT(project_id) as sum_project_status_all');

        // ดึงจำนวน project_id ที่มี project_status เป็น 'สิ้นสุดสัญญา' จากตาราง tbl_bp_report_y2565
        $query = $this->db->get('tbl_bp_report_y2565');

        // คืนค่าผลรวม
        return $query->row()->sum_project_status_all;
    }

    // ประเภทโครงการ เงิน เช่า ####
    public function sum_project_money_without_comma_by_type_rent_y2565()
    {
        $sql = "SELECT SUM(CAST(REPLACE(project_money, ',', '') AS DECIMAL(10,2))) AS sum_money FROM tbl_bp_report_y2565 WHERE project_type_name = 'เช่า'";

        // ส่งคำสั่ง SQL ไปยังฐานข้อมูล
        $query = $this->db->query($sql);

        // คืนค่าผลรวม
        return $query->row()->sum_money;
    }
    // ประเภทโครงการ จ้างก่อสร้าง ####
    public function sum_project_money_without_comma_by_type_construction_y2565()
    {
        $sql = "SELECT SUM(CAST(REPLACE(project_money, ',', '') AS DECIMAL(10,2))) AS sum_money FROM tbl_bp_report_y2565 WHERE project_type_name = 'จ้างก่อสร้าง'";

        // ส่งคำสั่ง SQL ไปยังฐานข้อมูล
        $query = $this->db->query($sql);

        // คืนค่าผลรวม
        return $query->row()->sum_money;
    }

    // ประเภทโครงการ เงิน จ้างทำของ/จ้างเหมาบริการ ####
    public function sum_project_money_without_comma_by_type_s_contractor_y2565()
    {
        $sql = "SELECT SUM(CAST(REPLACE(project_money, ',', '') AS DECIMAL(10,2))) AS sum_money FROM tbl_bp_report_y2565 WHERE project_type_name = 'จ้างทำของ/จ้างเหมาบริการ'";

        // ส่งคำสั่ง SQL ไปยังฐานข้อมูล
        $query = $this->db->query($sql);

        // คืนค่าผลรวม
        return $query->row()->sum_money;
    }

    // ประเภทโครงการ เงิน ซื้อ ####
    public function sum_project_money_without_comma_by_type_buy_y2565()
    {
        $sql = "SELECT SUM(CAST(REPLACE(project_money, ',', '') AS DECIMAL(10,2))) AS sum_money FROM tbl_bp_report_y2565 WHERE project_type_name = 'ซื้อ'";

        // ส่งคำสั่ง SQL ไปยังฐานข้อมูล
        $query = $this->db->query($sql);

        // คืนค่าผลรวม
        return $query->row()->sum_money;
    }

    // ประเภทโครงการ จำนวน เช่า ####
    public function count_projects_type_by_rent_y2565()
    {
        $this->db->select('COUNT(project_id) as sum_project_type_rent');
        $this->db->where('project_type_name', 'เช่า');
        $query = $this->db->get('tbl_bp_report_y2565');
        return $query->row()->sum_project_type_rent;
    }

    // ประเภทโครงการ จำนวน จ้างก่อสร้าง ####
    public function count_projects_type_by_construction_y2565()
    {
        $this->db->select('COUNT(project_id) as sum_project_type_construction');
        $this->db->where('project_type_name', 'จ้างก่อสร้าง');
        $query = $this->db->get('tbl_bp_report_y2565');
        return $query->row()->sum_project_type_construction;
    }

    // ประเภทโครงการ จำนวน จ้างทำของ/จ้างเหมาบริการ ####
    public function count_projects_type_by_s_contractor_y2565()
    {
        $this->db->select('COUNT(project_id) as sum_project_type_s_contractor');
        $this->db->where('project_type_name', 'จ้างทำของ/จ้างเหมาบริการ');
        $query = $this->db->get('tbl_bp_report_y2565');
        return $query->row()->sum_project_type_s_contractor;
    }

    // ประเภทโครงการ จำนวน ซื้อ ####
    public function count_projects_type_by_buy_y2565()
    {
        $this->db->select('COUNT(project_id) as sum_project_type_buy');
        $this->db->where('project_type_name', 'ซื้อ');
        $query = $this->db->get('tbl_bp_report_y2565');
        return $query->row()->sum_project_type_buy;
    }

    // วิธีจัดซื้อจัดจ้าง เงิน อื่นๆ ####
    public function sum_project_money_without_comma_by_purchase_other_y2565()
    {
        $sql = "SELECT SUM(CAST(REPLACE(project_money, ',', '') AS DECIMAL(10,2))) AS sum_money 
                   FROM tbl_bp_report_y2565 
                   WHERE purchase_method_name NOT IN ('ประกวดราคาอิเล็กทรอนิกส์ (e-bidding)', 'เฉพาะเจาะจง')";
        $query = $this->db->query($sql);
        return $query->row()->sum_money;
    }

    // วิธีจัดซื้อจัดจ้าง เงิน ประกวดราคาอิเล็กทรอนิกส์ (e-bidding) ####
    public function sum_project_money_without_comma_by_purchase_e_bidding_y2565()
    {
        $sql = "SELECT SUM(CAST(REPLACE(project_money, ',', '') AS DECIMAL(10,2))) AS sum_money FROM tbl_bp_report_y2565 WHERE purchase_method_name = 'ประกวดราคาอิเล็กทรอนิกส์ (e-bidding)'";
        $query = $this->db->query($sql);
        return $query->row()->sum_money;
    }

    // วิธีจัดซื้อจัดจ้าง เงิน เฉพาะเจาะจง ####
    public function sum_project_money_without_comma_by_purchase_specific_y2565()
    {
        $sql = "SELECT SUM(CAST(REPLACE(project_money, ',', '') AS DECIMAL(10,2))) AS sum_money FROM tbl_bp_report_y2565 WHERE purchase_method_name = 'เฉพาะเจาะจง'";
        $query = $this->db->query($sql);
        return $query->row()->sum_money;
    }

    // วิธีจัดซื้อจัดจ้าง จำนวน อื่นๆ ####
    public function count_projects_purchase_by_other_y2565()
    {
        $this->db->select('COUNT(project_id) as sum_project_purchase_by_other');
        $this->db->where_not_in('purchase_method_name', array('ประกวดราคาอิเล็กทรอนิกส์ (e-bidding)', 'เฉพาะเจาะจง'));
        $query = $this->db->get('tbl_bp_report_y2565');
        return $query->row()->sum_project_purchase_by_other;
    }

    // วิธีจัดซื้อจัดจ้าง จำนวน E-Bidding ####
    public function count_projects_purchase_by_e_bidding_y2565()
    {
        $this->db->select('COUNT(project_id) as sum_project_purchase_by_e_bidding');
        $this->db->where('purchase_method_name', 'ประกวดราคาอิเล็กทรอนิกส์ (e-bidding)');
        $query = $this->db->get('tbl_bp_report_y2565');
        return $query->row()->sum_project_purchase_by_e_bidding;
    }
    // วิธีจัดซื้อจัดจ้าง จำนวน เฉพาะเจาะจง ####
    public function count_projects_purchase_by_specific_y2565()
    {
        $this->db->select('COUNT(project_id) as sum_project_purchase_by_specific');
        $this->db->where('purchase_method_name', 'เฉพาะเจาะจง');
        $query = $this->db->get('tbl_bp_report_y2565');
        return $query->row()->sum_project_purchase_by_specific;
    }

    // ใช้จ่ายงบประมาณรายเดือน สิ้นสุดสัญญา ####
    public function sum_project_money_by_project_status_end_y2565()
    {
        $query = "WITH months AS (
            SELECT 'ต.ค. 64' AS month
            UNION SELECT 'พ.ย. 64'
            UNION SELECT 'ธ.ค. 64'
            UNION SELECT 'ม.ค. 65'
            UNION SELECT 'ก.พ. 65'
            UNION SELECT 'มี.ค. 65'
            UNION SELECT 'เม.ย. 65'
            UNION SELECT 'พ.ค. 65'
            UNION SELECT 'มิ.ย. 65'
            UNION SELECT 'ก.ค. 65'
            UNION SELECT 'ส.ค. 65'
            UNION SELECT 'ก.ย. 65'
            UNION SELECT 'ต.ค. 65'
            UNION SELECT 'พ.ย. 65'
            UNION SELECT 'ธ.ค. 65'
        )
        SELECT 
            months.month,
            COALESCE(SUM(CAST(REPLACE(tbl_bp_report_y2565.project_money, ',', '') AS DECIMAL(10,2))), 0) AS sum_money
        FROM 
            months
        LEFT JOIN 
            tbl_bp_report_y2565 ON 
                (transaction_date LIKE CONCAT('%', SUBSTRING_INDEX(months.month, ' ', 1), '%', SUBSTRING_INDEX(months.month, ' ', -1), '%') 
                AND project_status = 'สิ้นสุดสัญญา')
        GROUP BY 
            months.month
        ORDER BY 
            FIELD(months.month, 'ต.ค. 64', 'พ.ย. 64', 'ธ.ค. 64', 'ม.ค. 65', 'ก.พ. 65', 'มี.ค. 65', 'เม.ย. 65', 'พ.ค. 65', 'มิ.ย. 65', 'ก.ค. 65', 'ส.ค. 65', 'ก.ย. 65', 'ต.ค. 65', 'พ.ย. 65', 'ธ.ค. 65');";
 
        $result = $this->db->query($query);
 
        if ($result->num_rows() > 0) {
            return $result->result_array();
        } else {
            return array();
        }
    }
 
    // ใช้จ่ายงบประมาณรายเดือน ระหว่างดำเนินการ ####
    public function sum_project_money_by_project_status_process_y2565()
    {
        $query = "WITH months AS (
            SELECT 'ต.ค. 64' AS month
            UNION SELECT 'พ.ย. 64'
            UNION SELECT 'ธ.ค. 64'
            UNION SELECT 'ม.ค. 65'
            UNION SELECT 'ก.พ. 65'
            UNION SELECT 'มี.ค. 65'
            UNION SELECT 'เม.ย. 65'
            UNION SELECT 'พ.ค. 65'
            UNION SELECT 'มิ.ย. 65'
            UNION SELECT 'ก.ค. 65'
            UNION SELECT 'ส.ค. 65'
            UNION SELECT 'ก.ย. 65'
            UNION SELECT 'ต.ค. 65'
            UNION SELECT 'พ.ย. 65'
            UNION SELECT 'ธ.ค. 65'
        )
        SELECT 
            months.month,
            COALESCE(SUM(CAST(REPLACE(tbl_bp_report_y2565.project_money, ',', '') AS DECIMAL(10,2))), 0) AS sum_money
        FROM 
            months
        LEFT JOIN 
            tbl_bp_report_y2565 ON 
                (transaction_date LIKE CONCAT('%', SUBSTRING_INDEX(months.month, ' ', 1), '%', SUBSTRING_INDEX(months.month, ' ', -1), '%') 
                AND project_status = 'ระหว่างดำเนินการ')
        GROUP BY 
            months.month
        ORDER BY 
            FIELD(months.month, 'ต.ค. 64', 'พ.ย. 64', 'ธ.ค. 64', 'ม.ค. 65', 'ก.พ. 65', 'มี.ค. 65', 'เม.ย. 65', 'พ.ค. 65', 'มิ.ย. 65', 'ก.ค. 65', 'ส.ค. 65', 'ก.ย. 65', 'ต.ค. 65', 'พ.ย. 65', 'ธ.ค. 65');";
 
        $result = $this->db->query($query);
 
        if ($result->num_rows() > 0) {
            return $result->result_array();
        } else {
            return array();
        }
    }

    public function egp_frontend()
    {
        $this->db->select('*');
        $this->db->from('tbl_bp_report_y2567');
        $this->db->limit(9);
        $this->db->order_by('tbl_bp_report_y2567.contract_contract_date', 'DESC');
        $query = $this->db->get();
        return $query->result();
    }
    public function egp_y2567()
    {
        $this->db->select('*');
        $this->db->from('tbl_bp_report_y2567');
        $this->db->order_by('tbl_bp_report_y2567.contract_contract_date', 'DESC');
        $query = $this->db->get();
        return $query->result();
    }
    public function egp_y2566()
    {
        $this->db->select('*');
        $this->db->from('tbl_bp_report_y2566');
        $this->db->order_by('tbl_bp_report_y2566.contract_contract_date', 'DESC');
        $query = $this->db->get();
        return $query->result();
    }
    public function egp_y2565()
    {
        $this->db->select('*');
        $this->db->from('tbl_bp_report_y2565');
        $this->db->order_by('tbl_bp_report_y2565.contract_contract_date', 'DESC');
        $query = $this->db->get();
        return $query->result();
    }
    public function egp_y2564()
    {
        $this->db->select('*');
        $this->db->from('tbl_bp_report_y2564');
        $this->db->order_by('tbl_bp_report_y2564.contract_contract_date', 'DESC');
        $query = $this->db->get();
        return $query->result();
    }
    public function egp_y2563()
    {
        $this->db->select('*');
        $this->db->from('tbl_bp_report_y2563');
        $this->db->order_by('tbl_bp_report_y2563.contract_contract_date', 'DESC');
        $query = $this->db->get();
        return $query->result();
    }
}
