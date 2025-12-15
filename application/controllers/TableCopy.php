<?php
defined('BASEPATH') or exit('No direct script access allowed');

class TableCopy extends CI_Controller
{
    public function __construct()
    {
        parent::__construct();
    }

    public function copy_and_rename_columns()
    {
        try {
            $old_table = "tbl_operation_aca";   // ตารางต้นฉบับ
            $new_table = "tbl_award"; // ตารางใหม่

            // คัดลอกโครงสร้างตาราง
            $this->db->query("CREATE TABLE $new_table LIKE $old_table");

            // ดึงข้อมูลคอลัมน์จากตารางใหม่
            $columns = $this->db->query("SHOW COLUMNS FROM $new_table")->result();

            // คำสั่ง ALTER TABLE สำหรับเปลี่ยนชื่อคอลัมน์
            $alter_sql = "ALTER TABLE $new_table ";
            $changes = [];

            foreach ($columns as $col) {
                $old_col = $col->Field;

                // ถ้าคอลัมน์ขึ้นต้นด้วย 'operation_aca_' ให้เปลี่ยนเป็น 'award_'
                if (strpos($old_col, 'operation_aca_') === 0) {
                    $new_col = str_replace('operation_aca_', 'award_', $old_col);
                    // ถ้าเป็นคอลัมน์ ID ให้เพิ่ม AUTO_INCREMENT
                    if ($old_col === 'operation_aca_id') {
                        $changes[] = "CHANGE COLUMN `$old_col` `$new_col` INT NOT NULL AUTO_INCREMENT";
                    }
                    // ถ้าเป็นคอลัมน์ status
                    else if ($old_col === 'operation_aca_status') {
                        $changes[] = "CHANGE COLUMN `$old_col` `$new_col` $col->Type DEFAULT 'show'";
                    }
                    // คอลัมน์อื่นๆ
                    else {
                        $changes[] = "CHANGE COLUMN `$old_col` `$new_col` $col->Type";
                    }
                }
            }

            // ถ้ามีคอลัมน์ที่ต้องเปลี่ยนชื่อให้ทำ ALTER TABLE
            if (!empty($changes)) {
                $alter_sql .= implode(", ", $changes) . ";";
                $this->db->query($alter_sql);
            }

            // ส่งผลลัพธ์กลับไป
            $response = [
                'success' => true,
                'message' => 'Table copied and columns renamed successfully!',
                'new_table' => $new_table
            ];
        } catch (Exception $e) {
            $response = [
                'success' => false,
                'message' => $e->getMessage()
            ];
        }

        // ส่ง JSON กลับไป
        $this->output
            ->set_content_type('application/json')
            ->set_output(json_encode($response));
    }

    public function copy_and_rename_columns_file()
    {
        try {
            $old_table = "tbl_operation_aca_file";   // ตารางต้นฉบับ
            $new_table = "tbl_award_file"; // ตารางใหม่

            // คัดลอกโครงสร้างตาราง
            $this->db->query("CREATE TABLE $new_table LIKE $old_table");

            // ดึงข้อมูลคอลัมน์จากตารางใหม่
            $columns = $this->db->query("SHOW COLUMNS FROM $new_table")->result();

            // คำสั่ง ALTER TABLE สำหรับเปลี่ยนชื่อคอลัมน์
            $alter_sql = "ALTER TABLE $new_table ";
            $changes = [];

            foreach ($columns as $col) {
                $old_col = $col->Field;

                // ถ้าคอลัมน์ขึ้นต้นด้วย 'operation_aca_' ให้เปลี่ยนเป็น 'award_'
                if (strpos($old_col, 'operation_aca_') === 0) {
                    $new_col = str_replace('operation_aca_', 'award_', $old_col);
                    // ถ้าเป็นคอลัมน์ ID ให้เพิ่ม AUTO_INCREMENT
                    if ($old_col === 'operation_aca_file_id') {
                        $changes[] = "CHANGE COLUMN `$old_col` `$new_col` INT NOT NULL AUTO_INCREMENT";
                    } else {
                        $changes[] = "CHANGE COLUMN `$old_col` `$new_col` $col->Type";
                    }
                }
            }

            // ถ้ามีคอลัมน์ที่ต้องเปลี่ยนชื่อให้ทำ ALTER TABLE
            if (!empty($changes)) {
                $alter_sql .= implode(", ", $changes) . ";";
                $this->db->query($alter_sql);
            }

            // ส่งผลลัพธ์กลับไป
            $response = [
                'success' => true,
                'message' => 'Table copied and columns renamed successfully!',
                'new_table' => $new_table
            ];
        } catch (Exception $e) {
            $response = [
                'success' => false,
                'message' => $e->getMessage()
            ];
        }

        // ส่ง JSON กลับไป
        $this->output
            ->set_content_type('application/json')
            ->set_output(json_encode($response));
    }

    public function copy_and_rename_columns_img()
    {
        try {
            $old_table = "tbl_operation_aca_img";   // ตารางต้นฉบับ
            $new_table = "tbl_award_img"; // ตารางใหม่

            // คัดลอกโครงสร้างตาราง
            $this->db->query("CREATE TABLE $new_table LIKE $old_table");

            // ดึงข้อมูลคอลัมน์จากตารางใหม่
            $columns = $this->db->query("SHOW COLUMNS FROM $new_table")->result();

            // คำสั่ง ALTER TABLE สำหรับเปลี่ยนชื่อคอลัมน์
            $alter_sql = "ALTER TABLE $new_table ";
            $changes = [];

            foreach ($columns as $col) {
                $old_col = $col->Field;

                // ถ้าคอลัมน์ขึ้นต้นด้วย 'operation_aca_' ให้เปลี่ยนเป็น 'award_'
                if (strpos($old_col, 'operation_aca_') === 0) {
                    $new_col = str_replace('operation_aca_', 'award_', $old_col);
                    // ถ้าเป็นคอลัมน์ ID ให้เพิ่ม AUTO_INCREMENT
                    if ($old_col === 'operation_aca_img_id') {
                        $changes[] = "CHANGE COLUMN `$old_col` `$new_col` INT NOT NULL AUTO_INCREMENT";
                    } else {
                        $changes[] = "CHANGE COLUMN `$old_col` `$new_col` $col->Type";
                    }
                }
            }

            // ถ้ามีคอลัมน์ที่ต้องเปลี่ยนชื่อให้ทำ ALTER TABLE
            if (!empty($changes)) {
                $alter_sql .= implode(", ", $changes) . ";";
                $this->db->query($alter_sql);
            }

            // ส่งผลลัพธ์กลับไป
            $response = [
                'success' => true,
                'message' => 'Table copied and columns renamed successfully!',
                'new_table' => $new_table
            ];
        } catch (Exception $e) {
            $response = [
                'success' => false,
                'message' => $e->getMessage()
            ];
        }

        // ส่ง JSON กลับไป
        $this->output
            ->set_content_type('application/json')
            ->set_output(json_encode($response));
    }

    public function copy_and_rename_columns_pdf()
    {
        try {
            $old_table = "tbl_operation_aca_pdf";   // ตารางต้นฉบับ
            $new_table = "tbl_award_pdf"; // ตารางใหม่

            // คัดลอกโครงสร้างตาราง
            $this->db->query("CREATE TABLE $new_table LIKE $old_table");

            // ดึงข้อมูลคอลัมน์จากตารางใหม่
            $columns = $this->db->query("SHOW COLUMNS FROM $new_table")->result();

            // คำสั่ง ALTER TABLE สำหรับเปลี่ยนชื่อคอลัมน์
            $alter_sql = "ALTER TABLE $new_table ";
            $changes = [];

            foreach ($columns as $col) {
                $old_col = $col->Field;

                // ถ้าคอลัมน์ขึ้นต้นด้วย 'operation_aca_' ให้เปลี่ยนเป็น 'award_'
                if (strpos($old_col, 'operation_aca_') === 0) {
                    $new_col = str_replace('operation_aca_', 'award_', $old_col);
                    // ถ้าเป็นคอลัมน์ ID ให้เพิ่ม AUTO_INCREMENT
                    if ($old_col === 'operation_aca_pdf_id') {
                        $changes[] = "CHANGE COLUMN `$old_col` `$new_col` INT NOT NULL AUTO_INCREMENT";
                    } else {
                        $changes[] = "CHANGE COLUMN `$old_col` `$new_col` $col->Type";
                    }
                }
            }

            // ถ้ามีคอลัมน์ที่ต้องเปลี่ยนชื่อให้ทำ ALTER TABLE
            if (!empty($changes)) {
                $alter_sql .= implode(", ", $changes) . ";";
                $this->db->query($alter_sql);
            }

            // ส่งผลลัพธ์กลับไป
            $response = [
                'success' => true,
                'message' => 'Table copied and columns renamed successfully!',
                'new_table' => $new_table
            ];
        } catch (Exception $e) {
            $response = [
                'success' => false,
                'message' => $e->getMessage()
            ];
        }

        // ส่ง JSON กลับไป
        $this->output
            ->set_content_type('application/json')
            ->set_output(json_encode($response));
    }

    public function copy_and_rename_columns_topic()
    {
        try {
            $old_table = "tbl_operation_eco_type";   // ตารางต้นฉบับ
            $new_table = "tbl_award_type"; // ตารางใหม่

            // คัดลอกโครงสร้างตาราง
            $this->db->query("CREATE TABLE $new_table LIKE $old_table");

            // ดึงข้อมูลคอลัมน์จากตารางใหม่
            $columns = $this->db->query("SHOW COLUMNS FROM $new_table")->result();

            // คำสั่ง ALTER TABLE สำหรับเปลี่ยนชื่อคอลัมน์
            $alter_sql = "ALTER TABLE $new_table ";
            $changes = [];

            foreach ($columns as $col) {
                $old_col = $col->Field;

                // ถ้าคอลัมน์ขึ้นต้นด้วย 'operation_eco_' ให้เปลี่ยนเป็น 'award_'
                if (strpos($old_col, 'operation_eco_') === 0) {
                    $new_col = str_replace('operation_eco_', 'award_', $old_col);
                    // ถ้าเป็นคอลัมน์ ID ให้เพิ่ม AUTO_INCREMENT
                    if ($old_col === 'operation_eco_id') {
                        $changes[] = "CHANGE COLUMN `$old_col` `$new_col` INT NOT NULL AUTO_INCREMENT";
                    } else {
                        $changes[] = "CHANGE COLUMN `$old_col` `$new_col` $col->Type";
                    }
                }
            }

            // ถ้ามีคอลัมน์ที่ต้องเปลี่ยนชื่อให้ทำ ALTER TABLE
            if (!empty($changes)) {
                $alter_sql .= implode(", ", $changes) . ";";
                $this->db->query($alter_sql);
            }

            // ส่งผลลัพธ์กลับไป
            $response = [
                'success' => true,
                'message' => 'Table copied and columns renamed successfully!',
                'new_table' => $new_table
            ];
        } catch (Exception $e) {
            $response = [
                'success' => false,
                'message' => $e->getMessage()
            ];
        }

        // ส่ง JSON กลับไป
        $this->output
            ->set_content_type('application/json')
            ->set_output(json_encode($response));
    }

    public function copy_table()
    {
        $this->copy_and_rename_columns();
        $this->copy_and_rename_columns_file();
        $this->copy_and_rename_columns_img();
        $this->copy_and_rename_columns_pdf();
    }



    public function copy_table_topic()
    {
        $this->copy_and_rename_columns();
        $this->copy_and_rename_columns_file();
        $this->copy_and_rename_columns_img();
        $this->copy_and_rename_columns_pdf();
        $this->copy_and_rename_columns_topic();
    }
}
