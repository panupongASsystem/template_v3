<?php
defined('BASEPATH') or exit('No direct script access allowed');

class Tenant_access_model extends CI_Model
{

    private $db_tenant;

    public function __construct()
    {
        parent::__construct();
        // เชื่อมต่อกับฐานข้อมูล tenant_management
        $this->db_tenant = $this->load->database('tenant_management', TRUE);
    }

    /**
     * ดึงข้อมูล tenant จาก domain
     * สามารถเปรียบเทียบได้ทั้งแบบมี www. และไม่มี www.
     */
    public function get_tenant_by_domain($domain)
    {
        // ทำให้ domain อยู่ในรูปแบบมาตรฐาน (ตัด www. ออก)
        $domain = strtolower($domain);
        $clean_domain = str_replace('www.', '', $domain);

        // ค้นหา tenant ที่ตรงกับ domain
        $this->db_tenant->where('domain', $domain);
        $this->db_tenant->or_where('domain', 'www.' . $clean_domain);
        $this->db_tenant->or_where('domain', $clean_domain);

        $query = $this->db_tenant->get('tenants');

        if ($query->num_rows() > 0) {
            return $query->row();
        }
        return NULL;
    }

    /**
     * ดึงข้อมูล tenant ทั้งหมด
     */
    public function get_all_tenants()
    {
        $query = $this->db_tenant->get('tenants');
        return $query->result();
    }

    /**
     * ดึงข้อมูล tenant จาก ID
     */
    public function get_tenant_by_id($id)
    {
        $this->db_tenant->where('id', $id);
        $query = $this->db_tenant->get('tenants');

        if ($query->num_rows() > 0) {
            return $query->row();
        }
        return NULL;
    }

    /**
     * ดึงข้อมูล tenant จาก code
     */
    public function get_tenant_by_code($code)
    {
        $this->db_tenant->where('code', $code);
        $query = $this->db_tenant->get('tenants');

        if ($query->num_rows() > 0) {
            return $query->row();
        }
        return NULL;
    }
    /**
     * ตรวจสอบสิทธิ์การเข้าถึงบริการสาธารณะของ Tenant
     * 
     * @param int|string $tenant_identifier ID หรือ code ของ tenant
     * @param string $module_code รหัสของบริการที่ต้องการตรวจสอบ
     * @return bool|array คืนค่า false ถ้าไม่มีสิทธิ์ หรือข้อมูลสิทธิ์การเข้าถึงถ้ามี
     */
    public function check_module_access($tenant_identifier, $module_code)
    {
        // ตรวจสอบว่า $tenant_identifier เป็น ID หรือ code
        $tenant = null;
        if (is_numeric($tenant_identifier)) {
            $tenant = $this->get_tenant_by_id($tenant_identifier);
        } else {
            $tenant = $this->get_tenant_by_code($tenant_identifier);
        }

        // ถ้าไม่พบข้อมูล tenant
        if (!$tenant) {
            return false;
        }

        // ตรวจสอบว่า tenant มีสถานะใช้งานอยู่หรือไม่
        if (!$tenant->is_active) {
            return false;
        }

        // ค้นหาข้อมูลโมดูลจากรหัส
        $this->db_tenant->where('code', $module_code);
        $this->db_tenant->where('status', 1); // ต้องเป็นโมดูลที่เปิดใช้งาน
        $module = $this->db_tenant->get('tbl_public_modules')->row();

        // ถ้าไม่พบข้อมูลโมดูล หรือโมดูลไม่เปิดใช้งาน
        if (!$module) {
            return false;
        }

        // ค้นหาความสัมพันธ์ระหว่าง tenant และโมดูล
        $this->db_tenant->where('tenant_id', $tenant->id);
        $this->db_tenant->where('module_id', $module->id);
        $access = $this->db_tenant->get('tbl_public_modules_access')->row();

        // ถ้าไม่พบความสัมพันธ์
        if (!$access) {
            return false;
        }

        // ตรวจสอบสถานะการเข้าถึง
        if (!$access->is_active) {
            return false;
        }

        // คืนค่าข้อมูลการเข้าถึง
        return [
            'tenant' => $tenant,
            'module' => $module,
            'access' => $access
        ];
    }

    /**
     * ตรวจสอบสิทธิ์การเข้าถึงบริการสาธารณะของ Tenant จากโดเมนปัจจุบัน
     * 
     * @param string $domain โดเมนของ tenant
     * @param string $module_code รหัสของบริการที่ต้องการตรวจสอบ
     * @return bool|array คืนค่า false ถ้าไม่มีสิทธิ์ หรือข้อมูลสิทธิ์การเข้าถึงถ้ามี
     */
    public function check_module_access_by_domain($domain, $module_code)
    {
        // ดึงข้อมูล tenant จากโดเมน
        $tenant = $this->get_tenant_by_domain($domain);

        // ถ้าไม่พบข้อมูล tenant
        if (!$tenant) {
            return false;
        }

        // ตรวจสอบสิทธิ์การเข้าถึงโดยใช้ ID ของ tenant
        return $this->check_module_access($tenant->id, $module_code);
    }

    /**
     * ดึงข้อมูลบริการสาธารณะทั้งหมดที่ tenant มีสิทธิ์เข้าถึง
     * 
     * @param int|string $tenant_identifier ID หรือ code ของ tenant
     * @return array บริการสาธารณะทั้งหมดที่ tenant มีสิทธิ์เข้าถึง
     */
    public function get_accessible_modules($tenant_identifier)
    {
        // ตรวจสอบว่า $tenant_identifier เป็น ID หรือ code
        $tenant = null;
        if (is_numeric($tenant_identifier)) {
            $tenant = $this->get_tenant_by_id($tenant_identifier);
        } else {
            $tenant = $this->get_tenant_by_code($tenant_identifier);
        }

        // ถ้าไม่พบข้อมูล tenant
        if (!$tenant) {
            return [];
        }

        // ดึงข้อมูลบริการสาธารณะทั้งหมดที่ tenant มีสิทธิ์เข้าถึง
        $this->db_tenant->select('m.*, a.is_active as access_status');
        $this->db_tenant->from('tbl_public_modules_access a');
        $this->db_tenant->join('tbl_public_modules m', 'm.id = a.module_id');
        $this->db_tenant->where('a.tenant_id', $tenant->id);
        $this->db_tenant->where('a.is_active', 1);
        $this->db_tenant->where('m.status', 1);

        return $this->db_tenant->get()->result();
    }
	
	/**
 * ดึงข้อมูลโมดูลจากรหัส
 * 
 * @param string $code รหัสโมดูล
 * @return object|null ข้อมูลโมดูล หรือ null ถ้าไม่พบ
 */
public function get_module_by_code($code)
{
    $this->db_tenant->where('code', $code);
    $this->db_tenant->where('status', 1);
    $query = $this->db_tenant->get('tbl_public_modules');
    
    if ($query->num_rows() > 0) {
        return $query->row();
    }
    return NULL;
}

}