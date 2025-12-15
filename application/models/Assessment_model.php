<?php
defined('BASEPATH') or exit('No direct script access allowed');

class Assessment_model extends CI_Model
{

    public function __construct()
    {
        parent::__construct();
        $this->load->database();
    }

    // ======== หมวดหมู่ ========
    public function get_categories($active_only = true)
    {
        if ($active_only) {
            $this->db->where('is_active', 1);
        }
        $this->db->order_by('category_order', 'ASC');
        return $this->db->get('tbl_assessment_categories')->result();
    }

    public function get_category($id)
    {
        return $this->db->get_where('tbl_assessment_categories', ['id' => $id])->row();
    }

    public function add_category($data)
    {
        return $this->db->insert('tbl_assessment_categories', $data);
    }

    public function update_category($id, $data)
    {
        $this->db->where('id', $id);
        return $this->db->update('tbl_assessment_categories', $data);
    }

    public function delete_category($id)
    {
        $this->db->where('id', $id);
        return $this->db->delete('tbl_assessment_categories');
    }

    // ======== คำถาม ========
    public function get_questions($category_id = null, $active_only = true)
    {
        if ($category_id) {
            $this->db->where('category_id', $category_id);
        }
        if ($active_only) {
            $this->db->where('is_active', 1);
        }
        $this->db->order_by('question_order', 'ASC');
        return $this->db->get('tbl_assessment_questions')->result();
    }

    public function get_question($id)
    {
        return $this->db->get_where('tbl_assessment_questions', ['id' => $id])->row();
    }

    public function add_question($data)
    {
        $this->db->insert('tbl_assessment_questions', $data);
        return $this->db->insert_id();
    }

    public function update_question($id, $data)
    {
        $this->db->where('id', $id);
        return $this->db->update('tbl_assessment_questions', $data);
    }

    public function delete_question($id)
    {
        $this->db->where('id', $id);
        return $this->db->delete('tbl_assessment_questions');
    }

    // ======== ตัวเลือก ========
    public function get_options($question_id, $active_only = true)
    {
        $this->db->where('question_id', $question_id);
        if ($active_only) {
            $this->db->where('is_active', 1);
        }
        $this->db->order_by('option_order', 'ASC');
        return $this->db->get('tbl_assessment_options')->result();
    }

    public function get_option($id)
    {
        return $this->db->get_where('tbl_assessment_options', ['id' => $id])->row();
    }

    public function add_option($data)
    {
        return $this->db->insert('tbl_assessment_options', $data);
    }

    public function update_option($id, $data)
    {
        $this->db->where('id', $id);
        return $this->db->update('tbl_assessment_options', $data);
    }

    public function delete_option($id)
    {
        $this->db->where('id', $id);
        return $this->db->delete('tbl_assessment_options');
    }

    // ======== แบบประเมินเต็ม - แก้ไขให้ไม่โหลด options สำหรับ textarea ========
    // ใน Assessment_model.php - แก้ไขฟังก์ชัน get_full_assessment()
    public function get_full_assessment()
    {
        $categories = $this->get_categories();

        foreach ($categories as &$category) {
            $questions = $this->get_questions($category->id);

            foreach ($questions as &$question) {
                // **แก้ไข: ตรวจสอบ question_type อย่างเข้มงวด**
                if ($question->question_type === 'textarea') {
                    // สำหรับ textarea ไม่ต้องมี options เลย
                    $question->options = [];
                } else {
                    // เฉพาะคำถามที่ไม่ใช่ textarea เท่านั้นที่จะโหลด options
                    $question->options = $this->get_options($question->id);
                }
            }

            $category->questions = $questions;
        }

        return $categories;
    }

    // ======== การตอบแบบประเมิน ========
    public function create_response($data)
    {
        $this->db->insert('tbl_assessment_responses', $data);
        return $this->db->insert_id();
    }

    public function add_answer($data)
    {
        return $this->db->insert('tbl_assessment_answers', $data);
    }

    public function complete_response($response_id)
    {
        $this->db->where('id', $response_id);
        return $this->db->update('tbl_assessment_responses', [
            'is_completed' => 1,
            'completed_at' => date('Y-m-d H:i:s')
        ]);
    }

    // ======== รายงาน ========
    public function get_responses($limit = null, $offset = null)
    {
        $this->db->select('r.*, COUNT(a.id) as answer_count');
        $this->db->from('tbl_assessment_responses r');
        $this->db->join('tbl_assessment_answers a', 'r.id = a.response_id', 'left');
        $this->db->where('r.is_completed', 1);
        $this->db->group_by('r.id');
        $this->db->order_by('r.completed_at', 'DESC');

        if ($limit) {
            $this->db->limit($limit, $offset);
        }

        return $this->db->get()->result();
    }

    public function get_response_detail($response_id)
    {
        $this->db->select('r.*, a.question_id, a.answer_text, a.answer_value, q.question_text, q.question_type, c.category_name');
        $this->db->from('tbl_assessment_responses r');
        $this->db->join('tbl_assessment_answers a', 'r.id = a.response_id');
        $this->db->join('tbl_assessment_questions q', 'a.question_id = q.id');
        $this->db->join('tbl_assessment_categories c', 'q.category_id = c.id');
        $this->db->where('r.id', $response_id);
        $this->db->order_by('c.category_order, q.question_order');

        return $this->db->get()->result();
    }

    public function get_statistics()
    {
        // คำนวณสถิติพื้นฐาน
        $stats = [];

        // จำนวนผู้ตอบทั้งหมด
        $stats['total_responses'] = $this->db->where('is_completed', 1)->count_all_results('tbl_assessment_responses');

        // คะแนนเฉลี่ยแต่ละหมวด
        $categories = $this->get_categories();
        foreach ($categories as $category) {
            $questions = $this->get_questions($category->id);

            $category_scores = [];
            foreach ($questions as $question) {
                // **แก้ไข: เฉพาะคำถาม radio เท่านั้นที่คำนวณคะแนน**
                if ($question->question_type === 'radio') {
                    $this->db->select('AVG(CAST(answer_value AS DECIMAL(3,2))) as avg_score');
                    $this->db->from('tbl_assessment_answers a');
                    $this->db->join('tbl_assessment_responses r', 'a.response_id = r.id');
                    $this->db->where('a.question_id', $question->id);
                    $this->db->where('r.is_completed', 1);
                    $this->db->where('a.answer_value REGEXP', '^[1-5]$');

                    $result = $this->db->get()->row();
                    if ($result && $result->avg_score) {
                        $category_scores[] = $result->avg_score;
                    }
                }
            }

            if (!empty($category_scores)) {
                $stats['categories'][$category->id] = [
                    'name' => $category->category_name,
                    'avg_score' => array_sum($category_scores) / count($category_scores)
                ];
            }
        }

        return $stats;
    }

    // ======== การตั้งค่า ========
    public function get_setting($key)
    {
        $result = $this->db->get_where('tbl_assessment_settings', ['setting_key' => $key])->row();
        return $result ? $result->setting_value : null;
    }

    public function update_setting($key, $value)
    {
        $existing = $this->db->get_where('tbl_assessment_settings', ['setting_key' => $key])->row();

        if ($existing) {
            $this->db->where('setting_key', $key);
            return $this->db->update('tbl_assessment_settings', ['setting_value' => $value]);
        } else {
            return $this->db->insert('tbl_assessment_settings', [
                'setting_key' => $key,
                'setting_value' => $value
            ]);
        }
    }

    public function get_all_settings()
    {
        $result = $this->db->get('tbl_assessment_settings')->result();
        $settings = [];
        foreach ($result as $row) {
            $settings[$row->setting_key] = $row->setting_value;
        }
        return $settings;
    }






    /**
     * นับจำนวนคำถามในหมวดหมู่
     */
    public function count_questions_in_category($category_id)
    {
        return $this->db->where('category_id', $category_id)
            ->where('is_active', 1)
            ->count_all_results('tbl_assessment_questions');
    }

    /**
     * ดึงผู้ตอบล่าสุด
     */
    public function get_recent_responses($limit = 10)
    {
        $this->db->select('r.*, COUNT(a.id) as answer_count');
        $this->db->from('tbl_assessment_responses r');
        $this->db->join('tbl_assessment_answers a', 'r.id = a.response_id', 'left');
        $this->db->where('r.is_completed', 1);
        $this->db->group_by('r.id');
        $this->db->order_by('r.completed_at', 'DESC');
        $this->db->limit($limit);

        return $this->db->get()->result();
    }

    /**
     * ดึงสถิติแดชบอร์ด
     */
    public function get_dashboard_statistics()
    {
        $stats = [];

        // จำนวนผู้ตอบทั้งหมด
        $stats['total_responses'] = $this->db->where('is_completed', 1)
            ->count_all_results('tbl_assessment_responses');

        // จำนวนผู้ตอบวันนี้
        $today = date('Y-m-d');
        $stats['today_responses'] = $this->db->where('is_completed', 1)
            ->where('DATE(completed_at)', $today)
            ->count_all_results('tbl_assessment_responses');

        // จำนวนคำถามทั้งหมด
        $stats['total_questions'] = $this->db->where('is_active', 1)
            ->count_all_results('tbl_assessment_questions');

        // คะแนนเฉลี่ยรวม
        $this->db->select('AVG(CAST(answer_value AS DECIMAL(3,2))) as avg_score');
        $this->db->from('tbl_assessment_answers a');
        $this->db->join('tbl_assessment_responses r', 'a.response_id = r.id');
        $this->db->join('tbl_assessment_questions q', 'a.question_id = q.id');
        $this->db->where('r.is_completed', 1);
        $this->db->where('q.question_type', 'radio');
        $this->db->where('a.answer_value REGEXP', '^[1-5]$');

        $result = $this->db->get()->row();
        $stats['average_score'] = $result ? $result->avg_score : 0;

        return $stats;
    }

    /**
     * ดึงสถิติคะแนนเฉลี่ยแต่ละหมวด
     */
    public function get_category_scores()
    {
        $categories = $this->get_categories();
        $category_scores = [];

        foreach ($categories as $category) {
            $questions = $this->get_questions($category->id);
            $scores = [];

            foreach ($questions as $question) {
                if ($question->question_type === 'radio') {
                    $this->db->select('AVG(CAST(answer_value AS DECIMAL(3,2))) as avg_score');
                    $this->db->from('tbl_assessment_answers a');
                    $this->db->join('tbl_assessment_responses r', 'a.response_id = r.id');
                    $this->db->where('a.question_id', $question->id);
                    $this->db->where('r.is_completed', 1);
                    $this->db->where('a.answer_value REGEXP', '^[1-5]$');

                    $q_result = $this->db->get()->row();
                    if ($q_result && $q_result->avg_score) {
                        $scores[] = $q_result->avg_score;
                    }
                }
            }

            if (!empty($scores)) {
                $category_scores[$category->id] = [
                    'name' => $category->category_name,
                    'avg_score' => array_sum($scores) / count($scores)
                ];
            }
        }

        return $category_scores;
    }

    /**
     * สำรองข้อมูลแบบประเมิน
     */
    public function backup_assessment_data()
    {
        $backup = [];

        // Categories
        $backup['categories'] = $this->db->get('tbl_assessment_categories')->result_array();

        // Questions
        $backup['questions'] = $this->db->get('tbl_assessment_questions')->result_array();

        // Options
        $backup['options'] = $this->db->get('tbl_assessment_options')->result_array();

        // Settings
        $backup['settings'] = $this->db->get('tbl_assessment_settings')->result_array();

        return $backup;
    }

    /**
     * กู้คืนข้อมูลแบบประเมิน
     */
    public function restore_assessment_data($backup_data)
    {
        $this->db->trans_start();

        try {
            // ลบข้อมูลเก่า
            $this->db->truncate('tbl_assessment_options');
            $this->db->truncate('tbl_assessment_questions');
            $this->db->truncate('tbl_assessment_categories');
            $this->db->truncate('tbl_assessment_settings');

            // กู้คืนข้อมูล
            if (!empty($backup_data['categories'])) {
                $this->db->insert_batch('tbl_assessment_categories', $backup_data['categories']);
            }

            if (!empty($backup_data['questions'])) {
                $this->db->insert_batch('tbl_assessment_questions', $backup_data['questions']);
            }

            if (!empty($backup_data['options'])) {
                $this->db->insert_batch('tbl_assessment_options', $backup_data['options']);
            }

            if (!empty($backup_data['settings'])) {
                $this->db->insert_batch('tbl_assessment_settings', $backup_data['settings']);
            }

            $this->db->trans_complete();

            return $this->db->trans_status();

        } catch (Exception $e) {
            $this->db->trans_rollback();
            return false;
        }
    }

    /**
     * ตรวจสอบความสมบูรณ์ของข้อมูล
     */
    public function validate_assessment_structure()
    {
        $issues = [];

        // ตรวจสอบหมวดหมู่ที่ไม่มีคำถาม
        $categories = $this->get_categories(false);
        foreach ($categories as $category) {
            $question_count = $this->count_questions_in_category($category->id);
            if ($question_count == 0) {
                $issues[] = "หมวดหมู่ '{$category->category_name}' ไม่มีคำถาม";
            }
        }

        // ตรวจสอบคำถาม radio ที่ไม่มีตัวเลือก
        $radio_questions = $this->db->where('question_type', 'radio')
            ->where('is_active', 1)
            ->get('tbl_assessment_questions')
            ->result();

        foreach ($radio_questions as $question) {
            $option_count = $this->db->where('question_id', $question->id)
                ->where('is_active', 1)
                ->count_all_results('tbl_assessment_options');
            if ($option_count == 0) {
                $issues[] = "คำถาม '{$question->question_text}' ไม่มีตัวเลือก";
            }
        }

        return $issues;
    }

    /**
     * ดึงข้อมูลคำตอบทั้งหมดพร้อม pagination และ filter
     */
    public function get_paginated_responses($limit, $offset, $filters = [])
    {
        try {
            $this->db->select('r.id, r.ip_address, r.completed_at, r.session_id');
            $this->db->from('tbl_assessment_responses r');
            $this->db->where('r.is_completed', 1);

            // Apply filters
            if (!empty($filters['date_from'])) {
                $this->db->where('DATE(r.completed_at) >=', $filters['date_from']);
            }

            if (!empty($filters['date_to'])) {
                $this->db->where('DATE(r.completed_at) <=', $filters['date_to']);
            }

            // Filter by score if specified
            if (!empty($filters['score'])) {
                $this->db->join('tbl_assessment_answers a', 'r.id = a.response_id');
                $this->db->join('tbl_assessment_questions q', 'a.question_id = q.id');
                $this->db->join('tbl_assessment_categories c', 'q.category_id = c.id');
                $this->db->where('c.is_scoring', 1);
                $this->db->where('q.question_type', 'radio');
                $this->db->where('a.answer_value', $filters['score']);
                $this->db->group_by('r.id');
            }

            $this->db->order_by('r.completed_at', 'DESC');
            $this->db->limit($limit, $offset);

            $responses = $this->db->get()->result();

            // ดึงรายละเอียดของแต่ละ response
            foreach ($responses as &$response) {
                $response->details = $this->get_response_details_extended($response->id);
                $response->average_score = $this->calculate_response_average_score($response->id);
            }

            return $responses;

        } catch (Exception $e) {
            log_message('error', 'Get Paginated Responses Error: ' . $e->getMessage());
            return [];
        }
    }

    /**
     * นับจำนวน response ทั้งหมดที่ตรงเงื่อนไข
     */
    public function count_total_responses($filters = [])
    {
        try {
            $this->db->from('tbl_assessment_responses r');
            $this->db->where('r.is_completed', 1);

            // Apply same filters as pagination
            if (!empty($filters['date_from'])) {
                $this->db->where('DATE(r.completed_at) >=', $filters['date_from']);
            }

            if (!empty($filters['date_to'])) {
                $this->db->where('DATE(r.completed_at) <=', $filters['date_to']);
            }

            if (!empty($filters['score'])) {
                $this->db->join('tbl_assessment_answers a', 'r.id = a.response_id');
                $this->db->join('tbl_assessment_questions q', 'a.question_id = q.id');
                $this->db->join('tbl_assessment_categories c', 'q.category_id = c.id');
                $this->db->where('c.is_scoring', 1);
                $this->db->where('q.question_type', 'radio');
                $this->db->where('a.answer_value', $filters['score']);
                $this->db->group_by('r.id');
            }

            return $this->db->count_all_results();

        } catch (Exception $e) {
            log_message('error', 'Count Total Responses Error: ' . $e->getMessage());
            return 0;
        }
    }

    /**
     * ดึงรายละเอียดคำตอบของ response นั้นๆ (เพิ่มเติม)
     */
    public function get_response_details_extended($response_id)
    {
        try {
            $this->db->select('
            a.answer_text, 
            a.answer_value, 
            q.question_text, 
            q.question_type, 
            q.question_order,
            c.category_name,
            c.category_order,
            c.is_scoring
        ');
            $this->db->from('tbl_assessment_answers a');
            $this->db->join('tbl_assessment_questions q', 'a.question_id = q.id');
            $this->db->join('tbl_assessment_categories c', 'q.category_id = c.id');
            $this->db->where('a.response_id', $response_id);
            $this->db->order_by('c.category_order, q.question_order');

            return $this->db->get()->result();

        } catch (Exception $e) {
            log_message('error', 'Get Response Details Extended Error: ' . $e->getMessage());
            return [];
        }
    }

    /**
     * คำนวณคะแนนเฉลี่ยของ response นั้นๆ
     */
    public function calculate_response_average_score($response_id)
    {
        try {
            $this->db->select('AVG(CAST(a.answer_value AS DECIMAL(3,2))) as avg_score');
            $this->db->from('tbl_assessment_answers a');
            $this->db->join('tbl_assessment_questions q', 'a.question_id = q.id');
            $this->db->join('tbl_assessment_categories c', 'q.category_id = c.id');
            $this->db->where('a.response_id', $response_id);
            $this->db->where('c.is_scoring', 1);
            $this->db->where('q.question_type', 'radio');
            $this->db->where('a.answer_value !=', '');
            $this->db->where('a.answer_value IS NOT NULL');

            $result = $this->db->get()->row();
            return $result ? round(floatval($result->avg_score), 2) : 0;

        } catch (Exception $e) {
            log_message('error', 'Calculate Response Average Score Error: ' . $e->getMessage());
            return 0;
        }
    }

    /**
     * ดึงข้อมูลคำตอบทั้งหมด (สำหรับ export หรือใช้งานอื่น)
     */
    public function get_all_assessment_responses_detailed()
    {
        try {
            $this->db->select('r.*, COUNT(a.id) as answer_count');
            $this->db->from('tbl_assessment_responses r');
            $this->db->join('tbl_assessment_answers a', 'r.id = a.response_id', 'left');
            $this->db->where('r.is_completed', 1);
            $this->db->group_by('r.id');
            $this->db->order_by('r.completed_at', 'DESC');

            return $this->db->get()->result();

        } catch (Exception $e) {
            log_message('error', 'Get All Assessment Responses Detailed Error: ' . $e->getMessage());
            return [];
        }
    }

    /**
     * ดึงสถิติการแจกแจงคะแนน
     */
    public function get_score_distribution()
    {
        try {
            $distribution = ['1' => 0, '2' => 0, '3' => 0, '4' => 0, '5' => 0];

            $this->db->select('a.answer_value, COUNT(*) as count');
            $this->db->from('tbl_assessment_answers a');
            $this->db->join('tbl_assessment_responses r', 'a.response_id = r.id');
            $this->db->join('tbl_assessment_questions q', 'a.question_id = q.id');
            $this->db->join('tbl_assessment_categories c', 'q.category_id = c.id');
            $this->db->where('r.is_completed', 1);
            $this->db->where('c.is_scoring', 1);
            $this->db->where('q.question_type', 'radio');
            $this->db->where_in('a.answer_value', ['1', '2', '3', '4', '5']);
            $this->db->group_by('a.answer_value');

            $results = $this->db->get()->result();

            foreach ($results as $result) {
                if (isset($distribution[$result->answer_value])) {
                    $distribution[$result->answer_value] = intval($result->count);
                }
            }

            return $distribution;

        } catch (Exception $e) {
            log_message('error', 'Get Score Distribution Error: ' . $e->getMessage());
            return ['1' => 0, '2' => 0, '3' => 0, '4' => 0, '5' => 0];
        }
    }

    /**
     * ดึงสถิติรายวัน (7 วันล่าสุด)
     */
    public function get_daily_statistics($days = 7)
    {
        try {
            $stats = [];

            for ($i = $days - 1; $i >= 0; $i--) {
                $date = date('Y-m-d', strtotime("-{$i} days"));

                $this->db->where('is_completed', 1);
                $this->db->where('DATE(completed_at)', $date);
                $count = $this->db->count_all_results('tbl_assessment_responses');

                $stats[] = [
                    'date' => $date,
                    'count' => $count,
                    'display_date' => date('d/m', strtotime($date))
                ];
            }

            return $stats;

        } catch (Exception $e) {
            log_message('error', 'Get Daily Statistics Error: ' . $e->getMessage());
            return [];
        }
    }

    /**
     * ดึงข้อเสนอแนะล่าสุด
     */
    public function get_recent_feedback($limit = 10)
    {
        try {
            $this->db->select('
            a.answer_text,
            r.completed_at,
            r.ip_address,
            q.question_text,
            c.category_name
        ');
            $this->db->from('tbl_assessment_answers a');
            $this->db->join('tbl_assessment_responses r', 'a.response_id = r.id');
            $this->db->join('tbl_assessment_questions q', 'a.question_id = q.id');
            $this->db->join('tbl_assessment_categories c', 'q.category_id = c.id');
            $this->db->where('r.is_completed', 1);
            $this->db->where('q.question_type', 'textarea');
            $this->db->where('a.answer_text !=', '');
            $this->db->where('a.answer_text IS NOT NULL');
            $this->db->order_by('r.completed_at', 'DESC');
            $this->db->limit($limit);

            return $this->db->get()->result();

        } catch (Exception $e) {
            log_message('error', 'Get Recent Feedback Error: ' . $e->getMessage());
            return [];
        }
    }

    /**
     * ดึงข้อมูลผู้ตอบล่าสุดแบบกรองตาม categories ที่ใช้ประเมิน
     */
    public function get_recent_responses_filtered($limit = 10)
    {
        try {
            $this->db->select('DISTINCT r.id, r.completed_at, r.ip_address, r.session_id');
            $this->db->from('tbl_assessment_responses r');
            $this->db->join('tbl_assessment_answers a', 'r.id = a.response_id');
            $this->db->join('tbl_assessment_questions q', 'a.question_id = q.id');
            $this->db->join('tbl_assessment_categories c', 'q.category_id = c.id');
            $this->db->where('r.is_completed', 1);
            $this->db->where('c.is_scoring', 1); // เฉพาะหมวดที่ใช้ประเมิน
            $this->db->order_by('r.completed_at', 'DESC');
            $this->db->limit($limit);

            $responses = $this->db->get()->result();

            // เพิ่มข้อมูลคะแนนเฉลี่ยสำหรับแต่ละ response
            foreach ($responses as &$response) {
                $response->average_score = $this->calculate_response_average_score($response->id);

                // เพิ่มข้อมูลจำนวนคำตอบ
                $this->db->where('response_id', $response->id);
                $response->answer_count = $this->db->count_all_results('tbl_assessment_answers');
            }

            return $responses;

        } catch (Exception $e) {
            log_message('error', 'Get Recent Responses Filtered Error: ' . $e->getMessage());
            return [];
        }
    }

    /**
     * นับจำนวนคำถามที่ใช้ประเมิน (scoring) ในหมวดหมู่
     */
    public function count_scoring_questions_in_category($category_id)
    {
        try {
            $this->db->from('tbl_assessment_questions q');
            $this->db->join('tbl_assessment_categories c', 'q.category_id = c.id');
            $this->db->where('q.category_id', $category_id);
            $this->db->where('q.is_active', 1);
            $this->db->where('q.question_type', 'radio');
            $this->db->where('c.is_scoring', 1);

            return $this->db->count_all_results();

        } catch (Exception $e) {
            log_message('error', 'Count Scoring Questions in Category Error: ' . $e->getMessage());
            return 0;
        }
    }

    /**
     * นับจำนวนผู้ตอบในแต่ละหมวด
     */
    public function get_category_response_count($category_id)
    {
        try {
            $this->db->select('COUNT(DISTINCT r.id) as count');
            $this->db->from('tbl_assessment_responses r');
            $this->db->join('tbl_assessment_answers a', 'r.id = a.response_id');
            $this->db->join('tbl_assessment_questions q', 'a.question_id = q.id');
            $this->db->where('r.is_completed', 1);
            $this->db->where('q.category_id', $category_id);

            $result = $this->db->get()->row();
            return $result ? intval($result->count) : 0;

        } catch (Exception $e) {
            log_message('error', 'Get Category Response Count Error: ' . $e->getMessage());
            return 0;
        }
    }

    /**
     * ดึงสถิติแบบขยาย (สำหรับ dashboard)
     */
    public function get_extended_statistics()
    {
        try {
            $stats = [];

            // จำนวนผู้ตอบทั้งหมด
            $stats['total_responses'] = $this->db->where('is_completed', 1)
                ->count_all_results('tbl_assessment_responses');

            // จำนวนผู้ตอบวันนี้
            $today = date('Y-m-d');
            $stats['today_responses'] = $this->db->where('is_completed', 1)
                ->where('DATE(completed_at)', $today)
                ->count_all_results('tbl_assessment_responses');

            // จำนวนคำถามทั้งหมด (เฉพาะที่ใช้ประเมิน)
            $this->db->select('COUNT(*) as total');
            $this->db->from('tbl_assessment_questions q');
            $this->db->join('tbl_assessment_categories c', 'q.category_id = c.id');
            $this->db->where('q.is_active', 1);
            $this->db->where('q.question_type', 'radio');
            $this->db->where('c.is_active', 1);
            $this->db->where('c.is_scoring', 1);

            $result = $this->db->get()->row();
            $stats['total_questions'] = $result ? intval($result->total) : 0;

            // คะแนนเฉลี่ยรวม
            $this->db->select('AVG(CAST(a.answer_value AS DECIMAL(3,2))) as avg_score');
            $this->db->from('tbl_assessment_answers a');
            $this->db->join('tbl_assessment_responses r', 'a.response_id = r.id');
            $this->db->join('tbl_assessment_questions q', 'a.question_id = q.id');
            $this->db->join('tbl_assessment_categories c', 'q.category_id = c.id');
            $this->db->where('r.is_completed', 1);
            $this->db->where('c.is_scoring', 1);
            $this->db->where('q.question_type', 'radio');
            $this->db->where('a.answer_value REGEXP', '^[1-5]');

            $result = $this->db->get()->row();
            $stats['average_score'] = $result ? round(floatval($result->avg_score), 2) : 0;

            // คะแนนเฉลี่ยแต่ละหมวด
            $stats['categories'] = [];
            $categories = $this->get_categories();

            foreach ($categories as $category) {
                if ($category->is_scoring == 1) { // เฉพาะหมวดที่ใช้ประเมิน
                    $this->db->select('AVG(CAST(a.answer_value AS DECIMAL(3,2))) as avg_score');
                    $this->db->from('tbl_assessment_answers a');
                    $this->db->join('tbl_assessment_responses r', 'a.response_id = r.id');
                    $this->db->join('tbl_assessment_questions q', 'a.question_id = q.id');
                    $this->db->where('r.is_completed', 1);
                    $this->db->where('q.category_id', $category->id);
                    $this->db->where('q.question_type', 'radio');
                    $this->db->where('a.answer_value REGEXP', '^[1-5]');

                    $cat_result = $this->db->get()->row();
                    $avg_score = $cat_result ? round(floatval($cat_result->avg_score), 2) : 0;

                    // นับจำนวนผู้ตอบในหมวดนี้
                    $response_count = $this->get_category_response_count($category->id);

                    $stats['categories'][$category->id] = [
                        'name' => $category->category_name,
                        'avg_score' => $avg_score,
                        'response_count' => $response_count
                    ];
                }
            }

            return $stats;

        } catch (Exception $e) {
            log_message('error', 'Get Extended Statistics Error: ' . $e->getMessage());
            return [
                'total_responses' => 0,
                'today_responses' => 0,
                'total_questions' => 0,
                'average_score' => 0,
                'categories' => []
            ];
        }
    }

    /**
     * ดึงรายการ Response IDs ที่ตรงกับ filter
     */
    public function get_filtered_response_ids($filters = [])
    {
        try {
            $this->db->select('DISTINCT r.id');
            $this->db->from('tbl_assessment_responses r');
            $this->db->where('r.is_completed', 1);

            // Apply filters
            if (!empty($filters['date_from'])) {
                $this->db->where('DATE(r.completed_at) >=', $filters['date_from']);
            }

            if (!empty($filters['date_to'])) {
                $this->db->where('DATE(r.completed_at) <=', $filters['date_to']);
            }

            if (!empty($filters['score'])) {
                $this->db->join('tbl_assessment_answers a', 'r.id = a.response_id');
                $this->db->join('tbl_assessment_questions q', 'a.question_id = q.id');
                $this->db->join('tbl_assessment_categories c', 'q.category_id = c.id');
                $this->db->where('c.is_scoring', 1);
                $this->db->where('q.question_type', 'radio');
                $this->db->where('a.answer_value', $filters['score']);
            }

            $results = $this->db->get()->result();
            return array_column($results, 'id');

        } catch (Exception $e) {
            log_message('error', 'Get Filtered Response IDs Error: ' . $e->getMessage());
            return [];
        }
    }




}

?>