<?php
class Prov2_local_doc_model extends CI_Model
{
    public function __construct()
    {
        parent::__construct();
        $this->load->model('space_model');
    }

    private function thai_month_to_english($thai_date)
    {
        $thai_months = array(
            'มกราคม' => 'January',
            'กุมภาพันธ์' => 'February',
            'มีนาคม' => 'March',
            'เมษายน' => 'April',
            'พฤษภาคม' => 'May',
            'มิถุนายน' => 'June',
            'กรกฎาคม' => 'July',
            'สิงหาคม' => 'August',
            'กันยายน' => 'September',
            'ตุลาคม' => 'October',
            'พฤศจิกายน' => 'November',
            'ธันวาคม' => 'December'
        );

        foreach ($thai_months as $thai => $eng) {
            $thai_date = str_replace($thai, $eng, $thai_date);
        }

        return $thai_date;
    }

    public function list_all()
    {
        $this->db->select('*');
        $this->db->from('tbl_prov_local_doc');
        $this->db->group_by('tbl_prov_local_doc.id');

        // Get the results
        $query = $this->db->get();
        $result = $query->result();

        // Convert and sort dates
        foreach ($result as $row) {
            $row->doc_date_converted = $this->thai_month_to_english($row->doc_date);
        }

        usort($result, function ($a, $b) {
            $dateA = DateTime::createFromFormat('d F Y', $a->doc_date_converted);
            $dateB = DateTime::createFromFormat('d F Y', $b->doc_date_converted);

            // Handle conversion errors
            if (!$dateA || !$dateB) {
                return 0;
            }

            return $dateB <=> $dateA;
        });

        return $result;
    }
    
    // public function list_all()
    // {
    //     $this->db->select('*');
    //     $this->db->from('tbl_prov_local_doc');
    //     $this->db->group_by('tbl_prov_local_doc.id');
    //     $this->db->order_by('STR_TO_DATE(tbl_prov_local_doc.doc_date, "%d/%m/%Y")', 'desc');
    //     $query = $this->db->get();
    //     return $query->result();
    // }



    public function prov_local_doc_frontend()
    {
        $this->db->select('*');
        $this->db->from('tbl_prov_local_doc');
        $this->db->where('tbl_prov_local_doc.prov_local_doc_status', 'show');
        $this->db->limit(9);
        $this->db->order_by('tbl_prov_local_doc.prov_local_doc_date', 'DESC');
        $query = $this->db->get();
        return $query->result();
    }

    public function prov_local_doc_frontend_list()
    {
        $this->db->select('*');
        $this->db->from('tbl_prov_local_doc');
        $this->db->where('tbl_prov_local_doc.prov_local_doc_status', 'show');
        $this->db->order_by('tbl_prov_local_doc.prov_local_doc_date', 'DESC');
        $query = $this->db->get();
        return $query->result();
    }
    public function increment_view($prov_local_doc_id)
    {
        $this->db->where('prov_local_doc_id', $prov_local_doc_id);
        $this->db->set('prov_local_doc_view', 'prov_local_doc_view + 1', false); // บวกค่า prov_local_doc_view ทีละ 1
        $this->db->update('tbl_prov_local_doc');
    }
    // ใน prov_local_doc_model
    public function increment_download_prov_local_doc($prov_local_doc_file_id)
    {
        $this->db->where('prov_local_doc_file_id', $prov_local_doc_file_id);
        $this->db->set('prov_local_doc_file_download', 'prov_local_doc_file_download + 1', false); // บวกค่า prov_local_doc_download ทีละ 1
        $this->db->update('tbl_prov_local_doc_file');
    }
}
