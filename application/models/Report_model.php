<?php
class Report_model extends CI_Model
{
    public function __construct()
    {
        parent::__construct();
    }

    // ภาพรวมเอกสาร *************************************************************************************
    public function count_news()
    {
        $this->db->select('COUNT(news_id) as news_total');
        $this->db->from('tbl_news');
        $query = $this->db->get();
        return $query->result();
    }
    public function count_activity()
    {
        $this->db->select('COUNT(activity_id) as activity_total');
        $this->db->from('tbl_activity');
        $query = $this->db->get();
        return $query->result();
    }
    public function count_travel()
    {
        $this->db->select('COUNT(travel_id) as travel_total');
        $this->db->from('tbl_travel');
        $query = $this->db->get();
        return $query->result();
    }
    public function count_food()
    {
        $this->db->select('COUNT(food_id) as food_total');
        $this->db->from('tbl_food');
        $query = $this->db->get();
        return $query->result();
    }
    public function count_health()
    {
        $this->db->select('COUNT(health_id) as health_total');
        $this->db->from('tbl_health');
        $query = $this->db->get();
        return $query->result();
    }
    public function count_otop()
    {
        $this->db->select('COUNT(otop_id) as otop_total');
        $this->db->from('tbl_otop');
        $query = $this->db->get();
        return $query->result();
    }
    public function count_store()
    {
        $this->db->select('COUNT(store_id) as store_total');
        $this->db->from('tbl_store');
        $query = $this->db->get();
        return $query->result();
    }

    public function count_user_store()
    {
        $this->db->select('COUNT(user_store_id) as user_store_total');
        $this->db->from('tbl_user_store');
        $query = $this->db->get();
        return $query->result();
    }
    // public function count_user_activity()
    // {
    //     $this->db->select('COUNT(user_activity_id) as user_activity_total');
    //     $this->db->from('tbl_user_activity');
    //     $query = $this->db->get();
    //     return $query->result();
    // }
    // public function count_user_travel()
    // {
    //     $this->db->select('COUNT(user_travel_id) as user_travel_total');
    //     $this->db->from('tbl_user_travel');
    //     $query = $this->db->get();
    //     return $query->result();
    // }
    // public function count_user_food()
    // {
    //     $this->db->select('COUNT(user_food_id) as user_food_total');
    //     $this->db->from('tbl_user_food');
    //     $query = $this->db->get();
    //     return $query->result();
    // }

    // *****************************************************************************************************
    // ค้นหาตามชื่อผู้ใช้งาน ************************************************************************************
    public function list_member()
    {
        $query = $this->db->get('tbl_member');
        return $query->result();
    }

    public function get_user_name($user_id)
    {
        $this->db->select('m_name');
        $this->db->from('tbl_member');
        $this->db->where('m_id', $user_id);
        $query = $this->db->get();
        return $query->row();
    }

    public function get_news_count($m_name)
    {
        $this->db->select('news_by, COUNT(*) as news_count');
        $this->db->from('tbl_news');
        $this->db->where('news_by', $m_name);
        $this->db->group_by('news_by');
        $query = $this->db->get();
        return $query->result();
    }
    public function get_activity_count($m_name)
    {
        $this->db->select('activity_by, COUNT(*) as activity_count');
        $this->db->from('tbl_activity');
        $this->db->where('activity_by', $m_name);
        $this->db->group_by('activity_by');
        $query = $this->db->get();
        return $query->result();
    }

    public function get_food_count($m_name)
    {
        $this->db->select('food_by, COUNT(*) as food_count');
        $this->db->from('tbl_food');
        $this->db->where('food_by', $m_name);
        $this->db->group_by('food_by');
        $query = $this->db->get();
        return $query->result();
    }
    public function get_travel_count($m_name)
    {
        $this->db->select('travel_by, COUNT(*) as travel_count');
        $this->db->from('tbl_travel');
        $this->db->where('travel_by', $m_name);
        $this->db->group_by('travel_by');
        $query = $this->db->get();
        return $query->result();
    }
    public function get_health_count($m_name)
    {
        $this->db->select('health_by, COUNT(*) as health_count');
        $this->db->from('tbl_health');
        $this->db->where('health_by', $m_name);
        $this->db->group_by('health_by');
        $query = $this->db->get();
        return $query->result();
    }
    public function get_otop_count($m_name)
    {
        $this->db->select('otop_by, COUNT(*) as otop_count');
        $this->db->from('tbl_otop');
        $this->db->where('otop_by', $m_name);
        $this->db->group_by('otop_by');
        $query = $this->db->get();
        return $query->result();
    }
    public function get_store_count($m_name)
    {
        $this->db->select('store_by, COUNT(*) as store_count');
        $this->db->from('tbl_store');
        $this->db->where('store_by', $m_name);
        $this->db->group_by('store_by');
        $query = $this->db->get();
        return $query->result();
    }
    public function get_user_store_count($m_name)
    {
        $this->db->select('user_store_by, COUNT(*) as user_store_count');
        $this->db->from('tbl_user_store');
        $this->db->where('user_store_by', $m_name);
        $this->db->group_by('user_store_by');
        $query = $this->db->get();
        return $query->result();
    }

    // *****************************************************************************************************

    // ค้นหาตามชื่อผู้ใช้งาน รายละเอียด *******************************************************************************
    public function get_news_data($m_name)
    {
        $this->db->select('news_name, news_datesave');
        $this->db->from('tbl_news');
        $this->db->where('news_by', $m_name);
        $query = $this->db->get();
        return $query->result();
    }
    public function get_activity_data($m_name)
    {
        $this->db->select('activity_name, activity_datesave');
        $this->db->from('tbl_activity');
        $this->db->where('activity_by', $m_name);
        $query = $this->db->get();
        return $query->result();
    }
    public function get_travel_data($m_name)
    {
        $this->db->select('travel_name, travel_datesave');
        $this->db->from('tbl_travel');
        $this->db->where('travel_by', $m_name);
        $query = $this->db->get();
        return $query->result();
    }
    public function get_food_data($m_name)
    {
        $this->db->select('food_name, food_datesave');
        $this->db->from('tbl_food');
        $this->db->where('food_by', $m_name);
        $query = $this->db->get();
        return $query->result();
    }
    public function get_health_data($m_name)
    {
        $this->db->select('health_name, health_datesave');
        $this->db->from('tbl_health');
        $this->db->where('health_by', $m_name);
        $query = $this->db->get();
        return $query->result();
    }
    public function get_otop_data($m_name)
    {
        $this->db->select('otop_name, otop_datesave');
        $this->db->from('tbl_otop');
        $this->db->where('otop_by', $m_name);
        $query = $this->db->get();
        return $query->result();
    }
    public function get_store_data($m_name)
    {
        $this->db->select('store_name, store_datesave');
        $this->db->from('tbl_store');
        $this->db->where('store_by', $m_name);
        $query = $this->db->get();
        return $query->result();
    }
    public function get_user_store_data($m_name)
    {
        $this->db->select('user_store_name, user_store_datesave');
        $this->db->from('tbl_user_store');
        $this->db->where('user_store_by', $m_name);
        $query = $this->db->get();
        return $query->result();
    }
    // *****************************************************************************************************

    // ค้นหาตามวัน/เดือน/ปี ************************************************************************************
    public function get_news_count_date($start_date, $end_date)
    {
        $this->db->select('COUNT(*) as news_count');
        $this->db->from('tbl_news');
        $this->db->where('news_datesave >=', $start_date);
        $this->db->where('news_datesave <=', $end_date);
        $query = $this->db->get();
        return $query->row()->news_count;
    }
    public function get_activity_count_date($start_date, $end_date)
    {
        $this->db->select('COUNT(*) as activity_count');
        $this->db->from('tbl_activity');
        $this->db->where('activity_datesave >=', $start_date);
        $this->db->where('activity_datesave <=', $end_date);
        $query = $this->db->get();
        return $query->row()->activity_count;
    }
    public function get_travel_count_date($start_date, $end_date)
    {
        $this->db->select('COUNT(*) as travel_count');
        $this->db->from('tbl_travel');
        $this->db->where('travel_datesave >=', $start_date);
        $this->db->where('travel_datesave <=', $end_date);
        $query = $this->db->get();
        return $query->row()->travel_count;
    }
    public function get_food_count_date($start_date, $end_date)
    {
        $this->db->select('COUNT(*) as food_count');
        $this->db->from('tbl_food');
        $this->db->where('food_datesave >=', $start_date);
        $this->db->where('food_datesave <=', $end_date);
        $query = $this->db->get();
        return $query->row()->food_count;
    }
    public function get_health_count_date($start_date, $end_date)
    {
        $this->db->select('COUNT(*) as health_count');
        $this->db->from('tbl_health');
        $this->db->where('health_datesave >=', $start_date);
        $this->db->where('health_datesave <=', $end_date);
        $query = $this->db->get();
        return $query->row()->health_count;
    }
    public function get_otop_count_date($start_date, $end_date)
    {
        $this->db->select('COUNT(*) as otop_count');
        $this->db->from('tbl_otop');
        $this->db->where('otop_datesave >=', $start_date);
        $this->db->where('otop_datesave <=', $end_date);
        $query = $this->db->get();
        return $query->row()->otop_count;
    }
    public function get_store_count_date($start_date, $end_date)
    {
        $this->db->select('COUNT(*) as store_count');
        $this->db->from('tbl_store');
        $this->db->where('store_datesave >=', $start_date);
        $this->db->where('store_datesave <=', $end_date);
        $query = $this->db->get();
        return $query->row()->store_count;
    }
    public function get_user_store_count_date($start_date, $end_date)
    {
        $this->db->select('COUNT(*) as user_store_count');
        $this->db->from('tbl_user_store');
        $this->db->where('user_store_datesave >=', $start_date);
        $this->db->where('user_store_datesave <=', $end_date);
        $query = $this->db->get();
        return $query->row()->user_store_count;
    }
    // *****************************************************************************************************
    // ค้นหาตามวัน/เดือน/ปี รายละเอียด *************************************************************************
    public function get_news_date_detail($start_date, $end_date)
    {
        $this->db->select('*');
        $this->db->from('tbl_news');
        $this->db->where('news_datesave >=', $start_date);
        $this->db->where('news_datesave <=', $end_date);
        $query = $this->db->get();
        return $query->result();
    }
    public function get_activity_date_detail($start_date, $end_date)
    {
        $this->db->select('*');
        $this->db->from('tbl_activity');
        $this->db->where('activity_datesave >=', $start_date);
        $this->db->where('activity_datesave <=', $end_date);
        $query = $this->db->get();
        return $query->result();
    }
    public function get_travel_date_detail($start_date, $end_date)
    {
        $this->db->select('*');
        $this->db->from('tbl_travel');
        $this->db->where('travel_datesave >=', $start_date);
        $this->db->where('travel_datesave <=', $end_date);
        $query = $this->db->get();
        return $query->result();
    }
    public function get_food_date_detail($start_date, $end_date)
    {
        $this->db->select('*');
        $this->db->from('tbl_food');
        $this->db->where('food_datesave >=', $start_date);
        $this->db->where('food_datesave <=', $end_date);
        $query = $this->db->get();
        return $query->result();
    }
    public function get_health_date_detail($start_date, $end_date)
    {
        $this->db->select('*');
        $this->db->from('tbl_health');
        $this->db->where('health_datesave >=', $start_date);
        $this->db->where('health_datesave <=', $end_date);
        $query = $this->db->get();
        return $query->result();
    }
    public function get_otop_date_detail($start_date, $end_date)
    {
        $this->db->select('*');
        $this->db->from('tbl_otop');
        $this->db->where('otop_datesave >=', $start_date);
        $this->db->where('otop_datesave <=', $end_date);
        $query = $this->db->get();
        return $query->result();
    }
    public function get_store_date_detail($start_date, $end_date)
    {
        $this->db->select('*');
        $this->db->from('tbl_store');
        $this->db->where('store_datesave >=', $start_date);
        $this->db->where('store_datesave <=', $end_date);
        $query = $this->db->get();
        return $query->result();
    }
    public function get_user_store_date_detail($start_date, $end_date)
    {
        $this->db->select('*');
        $this->db->from('tbl_user_store');
        $this->db->where('user_store_datesave >=', $start_date);
        $this->db->where('user_store_datesave <=', $end_date);
        $query = $this->db->get();
        return $query->result();
    }
    // *****************************************************************************************************


    // ดูสเตตัสเอกสาร
    public function count_doc_status()
    {
        $this->db->select('doc_status, COUNT(doc_id) as dtotal');
        $this->db->from('tbl_doc');
        $this->db->group_by('doc_status');
        $query = $this->db->get();
        return $query->result();
    }

    // จอยดูประเภทเอกสาร
    public function count_doc_type()
    {
        $this->db->select('t.dname, COUNT(d.doc_id) as dtotal');
        $this->db->from('tbl_doc as d');
        $this->db->join('tbl_doc_type as t', 'd.ref_did=t.did');
        $this->db->group_by('d.ref_did');
        $query = $this->db->get();
        return $query->result();
    }

    // ตามวัน
    public function count_doc_date()
    {
        $this->db->select("CONCAT(DATE_FORMAT(d.doc_save, '%d-%m-'), (DATE_FORMAT(d.doc_save, '%Y')+543)) as docsave, COUNT(d.doc_id) as dtotal", false);
        $this->db->from('tbl_doc as d');
        $this->db->group_by('DATE_FORMAT(d.doc_save,"%d%")');
        $this->db->order_by('DATE_FORMAT(d.doc_save,"%m%")');
        $query = $this->db->get();
        return $query->result();
    }

    // ตามเดือน
    public function count_doc_month()
    {
        // ถ้าwindow เป็นภาษาไหนก็จะเป็นภาษานั้น                 M ใหญ่
        $this->db->select("CONCAT(DATE_FORMAT(d.doc_save, '%M-'), (DATE_FORMAT(d.doc_save, '%Y')+543)) as docsave, COUNT(d.doc_id) as dtotal", false);
        $this->db->from('tbl_doc as d');
        $this->db->group_by('DATE_FORMAT(d.doc_save,"%m%")');
        $query = $this->db->get();
        return $query->result();
    }

    // ตามปี
    public function count_doc_year()
    {
        // $this->db->select("DATE_FORMAT(d.doc_save, '%Y') as docsave, COUNT(d.doc_id) as dtotal");
        $this->db->select("DATE_FORMAT(d.doc_save, '%Y')+543 as docsave, COUNT(d.doc_id) as dtotal", false);
        $this->db->from('tbl_doc as d');
        $this->db->group_by('DATE_FORMAT(d.doc_save,"%Y%")');
        $query = $this->db->get();
        return $query->result();
    }

    // เลือกดูตามช่วงเวลา
    public function count_doc_form()
    {
        $ds = $this->input->post('ds');
        $de = $this->input->post('de');
        // echo $ds .' x '.$de;
        // exit;
        $de = $de . ' 23:59:59';

        $this->db->select('*');
        $this->db->from('tbl_doc');
        $this->db->where('doc_save >=', $ds);
        $this->db->where('doc_save <=', $de);
        $query = $this->db->get();
        return $query->result();
    }


    // dashboard ****************************************************************************
    public function find_most_viewed_table()
    {
        $query = $this->db->query("
        SELECT 'ภาพกิจกรรม' AS table_name, SUM(activity_view) AS total_views FROM tbl_activity
        UNION ALL
        SELECT 'ข่าว/ประกาศจัดซื้อจัดจ้าง' AS table_name, SUM(procurement_view) AS total_views FROM tbl_procurement
        UNION ALL
        SELECT 'คำสั่งประกาศ' AS table_name, SUM(announce_view) AS total_views FROM tbl_announce
        UNION ALL
        SELECT 'ข่าวประชาสัมพันธ์' AS table_name, SUM(news_view) AS total_views FROM tbl_news
        UNION ALL
        SELECT 'มาตราการภายในหน่วยงาน' AS table_name, SUM(mui_view) AS total_views FROM tbl_mui
        UNION ALL
        SELECT 'ดาวน์โหลดแบบฟอร์ม' AS table_name, SUM(loadform_view) AS total_views FROM tbl_loadform
        UNION ALL
        SELECT 'คู่มือการปฏิบัติงาน' AS table_name, SUM(guide_work_view) AS total_views FROM tbl_guide_work
        UNION ALL
        SELECT 'สถานที่ท่องเที่ยว' AS table_name, SUM(travel_view) AS total_views FROM tbl_travel
        UNION ALL
        SELECT 'นโยบายของผู้บริหาร' AS table_name, SUM(executivepolicy_view) AS total_views FROM tbl_executivepolicy
        UNION ALL
        SELECT 'เทศบัญญัติงบประมาณ' AS table_name, SUM(canon_bgps_view) AS total_views FROM tbl_canon_bgps
        UNION ALL
        SELECT 'การควบคุมกิจการที่เป็นอันตรายต่อสุขภาพ' AS table_name, SUM(canon_chh_view) AS total_views FROM tbl_canon_chh
        UNION ALL
        SELECT 'เทศบัญญัติการติดตั้งระบบบำบัดน้ำเสียในอาคาร' AS table_name, SUM(canon_ritw_view) AS total_views FROM tbl_canon_ritw
        UNION ALL
        SELECT 'เทศบัญญัติตลาด' AS table_name, SUM(canon_market_view) AS total_views FROM tbl_canon_market
        UNION ALL
        SELECT 'เทศบัญญัติการจัดการสิ่งปฏิกูลและมูลฝอย' AS table_name, SUM(canon_rmwp_view) AS total_views FROM tbl_canon_rmwp
        UNION ALL
        SELECT 'เทศบัญญัติหลักเกณฑ์การคัดมูลฝอย' AS table_name, SUM(canon_rcsp_view) AS total_views FROM tbl_canon_rcsp
        UNION ALL
        SELECT 'เทศบัญญัติการควบคุมการเลี้ยงหรือปล่อยสุนัขและแมว' AS table_name, SUM(canon_rcp_view) AS total_views FROM tbl_canon_rcp
        UNION ALL
        SELECT 'ศูนย์ช่วยเหลือประชาชน' AS table_name, SUM(pbsv_cac_view) AS total_views FROM tbl_pbsv_cac
        UNION ALL
        SELECT 'ศูนย์ข้อมูลข่าวสารทางราชการ' AS table_name, SUM(pbsv_cig_view) AS total_views FROM tbl_pbsv_cig
        UNION ALL
        SELECT 'ศูนย์ยุติธรรมชุมชน' AS table_name, SUM(pbsv_cjc_view) AS total_views FROM tbl_pbsv_cjc
        UNION ALL
        SELECT 'คู่มือและมาตรฐานการให้บริการ' AS table_name, SUM(pbsv_sags_view) AS total_views FROM tbl_pbsv_sags
        UNION ALL
        SELECT 'งานอาสาสมัครป้องกันภัยฝ่ายพลเรือน (อปพร.)' AS table_name, SUM(pbsv_oppr_view) AS total_views FROM tbl_pbsv_oppr
        UNION ALL
        SELECT 'งานกู้ชีพ/การบริการการแพทย์ฉุกเฉิน(EMS)' AS table_name, SUM(pbsv_ems_view) AS total_views FROM tbl_pbsv_ems
        UNION ALL
        SELECT 'หลักประกันสุขภาพตำบลสว่าง' AS table_name, SUM(pbsv_ahs_view) AS total_views FROM tbl_pbsv_ahs
        UNION ALL
        SELECT 'ดาวน์โหลดแบบฟอร์ม E-book' AS table_name, SUM(pbsv_e_book_view) AS total_views FROM tbl_pbsv_e_book
        UNION ALL
        SELECT 'แผนงานพัฒนาท้องถิ่น' AS table_name, SUM(plan_pdl_view) AS total_views FROM tbl_plan_pdl
        UNION ALL
        SELECT 'แผนอัตรากำลัง 3 ปี' AS table_name, SUM(plan_pc3y_view) AS total_views FROM tbl_plan_pc3y
        UNION ALL
        SELECT 'แผนพัฒนาบุคลากร 3 ปี' AS table_name, SUM(plan_pds3y_view) AS total_views FROM tbl_plan_pds3y
        UNION ALL
        SELECT 'แผนพัฒนาบุคลากรประจำปี' AS table_name, SUM(plan_pdpa_view) AS total_views FROM tbl_plan_pdpa
        UNION ALL
        SELECT 'แผนการดำเนินงานประจำปี' AS table_name, SUM(plan_poa_view) AS total_views FROM tbl_plan_poa
        UNION ALL
        SELECT 'แผนจัดเก็บรายได้ประจำปี' AS table_name, SUM(plan_pcra_view) AS total_views FROM tbl_plan_pcra
        UNION ALL
        SELECT 'แผนปฏิบัติการจัดซื้อจัดจ้าง' AS table_name, SUM(plan_pop_view) AS total_views FROM tbl_plan_pop
        UNION ALL
        SELECT 'แผนปฏิบัติการป้องกันการทุจริต' AS table_name, SUM(plan_paca_view) AS total_views FROM tbl_plan_paca
        UNION ALL
        SELECT 'แผนแม่บทสารสนเทศ' AS table_name, SUM(plan_psi_view) AS total_views FROM tbl_plan_psi
        UNION ALL
        SELECT 'แผนป้องกันและบรรเทาสาธารณภัยประจำปี' AS table_name, SUM(plan_pmda_view) AS total_views FROM tbl_plan_pmda
        UNION ALL
        SELECT 'รายงานติดตามและประเมินผลแผนฯ' AS table_name, SUM(operation_reauf_view) AS total_views FROM tbl_operation_reauf
        UNION ALL
        SELECT 'การปฏิบัติการป้องกันการทุจริต' AS table_name, SUM(operation_aca_view) AS total_views FROM tbl_operation_aca
        UNION ALL
        SELECT 'การจัดการเรื่องร้องเรียนการทุจริต' AS table_name, SUM(operation_mcc_view) AS total_views FROM tbl_operation_mcc
        UNION ALL
        SELECT 'การปฏิบัติงานและการให้บริการ' AS table_name, SUM(operation_sap_view) AS total_views FROM tbl_operation_sap
        UNION ALL
        SELECT 'นโยบายไม่รับของขวัญ no gift policy' AS table_name, SUM(operation_pgn_view) AS total_views FROM tbl_operation_pgn
        UNION ALL
        SELECT 'การเปิดโอกาสให้มีส่วนร่วม' AS table_name, SUM(operation_po_view) AS total_views FROM tbl_operation_po
        UNION ALL
        SELECT 'การเสริมสร้างวัฒนธรรมองค์กร' AS table_name, SUM(operation_eco_view) AS total_views FROM tbl_operation_eco
        UNION ALL
        SELECT 'ITA การประเมินคุณธรรมของหน่วยงานภาครัฐ' AS table_name, SUM(ita_view) AS total_views FROM tbl_ita
        UNION ALL
        SELECT 'LPA การประเมินประสิทธิภาพขององค์กร' AS table_name, SUM(lpa_view) AS total_views FROM tbl_lpa
        UNION ALL
        SELECT 'นโยบายบริหารทรัพยากรบุคคล' AS table_name, SUM(operation_policy_hr_view) AS total_views FROM tbl_operation_policy_hr
        UNION ALL
        SELECT 'การดำเนินการบริหารทรัพยากรบุคคล' AS table_name, SUM(operation_am_hr_view) AS total_views FROM tbl_operation_am_hr
        UNION ALL
        SELECT 'รายงานผลการบริหารและพัฒนาทรัพยากรบุคคล' AS table_name, SUM(operation_rdam_hr_view) AS total_views FROM tbl_operation_rdam_hr
        UNION ALL
        SELECT 'หลักเกณฑ์การบริหารและพัฒนา' AS table_name, SUM(operation_cdm_view) AS total_views FROM tbl_operation_cdm
        UNION ALL
        SELECT 'การจัดซื้อจัดจ้าง' AS table_name, SUM(operation_procurement_view) AS total_views FROM tbl_operation_procurement
        UNION ALL
        SELECT 'กิจการสภา' AS table_name, SUM(operation_aa_view) AS total_views FROM tbl_operation_aa
        UNION ALL
        SELECT 'การมีส่วนร่วมของผู้บริหาร' AS table_name, SUM(operation_pm_view) AS total_views FROM tbl_operation_pm
        UNION ALL
        SELECT 'ตรวจสอบภายใน' AS table_name, SUM(operation_aditn_view) AS total_views FROM tbl_operation_aditn
        ORDER BY total_views DESC
        LIMIT 5
        
    ");

        $result = $query->result();

        return $result;
    }
    // *****************************************************************************************
}
