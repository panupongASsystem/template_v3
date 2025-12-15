<?php
class Member_model extends CI_Model
{
    public function __construct()
    {
        parent::__construct();
        $this->load->model('space_model');
    }

    // public function list_member()
    // {
    //     $query = $this->db->get('tbl_member');
    //     return $query->result();
    // }
	
	
public function count_members_by_type($type) {
    return $this->db
        ->where('m_system', $type)
        ->count_all_results('tbl_member');
}
	
	
	

public function list_member() {
    $this->db->select('m.*, p.pname, GROUP_CONCAT(ms.name) as system_names');
    $this->db->from('tbl_member as m');
    $this->db->join('tbl_position as p', 'm.ref_pid = p.pid', 'left');
    $this->db->join('tbl_member_systems as ms', 'm.m_id = ms.m_id', 'left');
    $this->db->group_by('m.m_id');
    $this->db->order_by('m.m_id', 'DESC');
    return $this->db->get()->result();
}

    public function get_grant_systems()
    {
        $this->db->select('grant_system_id, grant_system_name');
        $this->db->from('tbl_grant_system');
        $this->db->where('grant_system_menu', 1);
        $this->db->order_by('grant_system_id', 'ASC');
        return $this->db->get()->result();
    }


    public function get_provinces()
    {
        $this->db->distinct();
        $this->db->select('tambol_pname');
        $query = $this->db->get('tbl_tambol');
        return $query->result();
    }

    public function get_unique_amphurs_by_province($province_name)
    {
        $this->db->distinct();
        $this->db->select('tambol_aname');
        $this->db->where('tambol_pname', $province_name);
        $query = $this->db->get('tbl_tambol');
        return $query->result();
    }


    public function get_tambols_by_amphur($province_name, $amphur_name)
    {
        $this->db->where('tambol_pname', $province_name);
        $this->db->where('tambol_aname', $amphur_name);
        $query = $this->db->get('tbl_tambol');
        return $query->result();
    }



    public function addmember()
    {

        $data = array(
            'ref_pid' => $this->input->post('ref_pid'),
            'm_username' => $this->input->post('m_username'),
            'm_password' => sha1($this->input->post('m_password')),
            'm_fname' => $this->input->post('m_fname'),
            'm_name' => $this->input->post('m_name'),
            'm_lname' => $this->input->post('m_lname')
        );
        $query = $this->db->insert('tbl_member', $data);

        if ($query) {
            echo 'add ok';
        } else {
            echo 'false';
        }
    }

    // เช็คข้อมูลซ้ำ
    public function add_Member()
    {
        $m_username = $this->input->post('m_username');

        $this->db->where('m_username', $m_username);

        $query = $this->db->get('tbl_member');
        $num = $query->num_rows();

        if ($num > 0) {
            $this->session->set_flashdata('save_again', TRUE);
        } else {
            // ตรวจสอบรหัสผ่าน
            $m_password1 = $this->input->post('m_password');
            $m_password2 = $this->input->post('confirm_password');

            if ($m_password1 != $m_password2) {
                // รหัสผ่านไม่ตรงกัน
                $this->session->set_flashdata('password_mismatch', TRUE);
                redirect('Member_backend/adding');
                return;
            }

            // Check used space
            $used_space_mb = $this->space_model->get_used_space();
            $upload_limit_mb = $this->space_model->get_limit_storage();

            // Calculate the total space required for all files
            $total_space_required = 0;
            if (!empty($_FILES['m_img']['name'])) {
                $total_space_required += $_FILES['m_img']['size'];
            }

            // Check if there's enough space
            if ($used_space_mb + ($total_space_required / (1024 * 1024 * 1024)) >= $upload_limit_mb) {
                $this->session->set_flashdata('save_error', TRUE);
                redirect('Member_backend/add_Member');
                return;
            }

            // Upload configuration
            $config['upload_path'] = './docs/img';
            $config['allowed_types'] = 'gif|jpg|png'; // gif|jpg|png
            $this->load->library('upload', $config);

            // Upload main file
            if (!empty($_FILES['m_img']['name']) && $this->upload->do_upload('m_img')) {
                $data = $this->upload->data();
                $filename = $data['file_name'];
            } else {
                $filename = null; // ไม่มีการอัปโหลดรูปภาพ
            }


            $data = array(
                'm_username' => $this->input->post('m_username'),
                'm_password' => sha1($m_password1),
                'ref_pid' => $this->input->post('ref_pid'),
                'm_fname' => $this->input->post('m_fname'),
                'm_lname' => $this->input->post('m_lname'),
                'm_email' => $this->input->post('m_email'),
                'm_phone' => $this->input->post('m_phone'),
                'm_system' => $this->input->post('m_system'),
                'grant_user_ref_id' => $this->input->post('grant_user_ref_id'),
                'm_img' => $filename
            );

            // print_r($data);

            $this->db->insert('tbl_member', $data);

            $this->space_model->update_server_current();
            $this->session->set_flashdata('save_success', TRUE);
        }
    }

    public function read($m_id)
    {
        // เลือกคอลัมน์ที่ต้องการจากทั้งสองตาราง
        $this->db->select('m.*, p.pname, g.grant_user_name'); // แก้ไข `grant_user_name` ให้เป็นคอลัมน์ที่ต้องการจาก `tbl_grant_user`

        // ตั้งค่าตารางหลักและทำ JOIN กับตารางอื่น
        $this->db->from('tbl_member as m');
        $this->db->join('tbl_position as p', 'm.ref_pid = p.pid', 'left'); // JOIN กับตารางตำแหน่ง
        $this->db->join('tbl_grant_user as g', 'FIND_IN_SET(g.grant_user_id, m.grant_user_ref_id)', 'left'); // JOIN กับตาราง grant_user โดยใช้ FIND_IN_SET

        // กำหนดเงื่อนไขการค้นหา
        $this->db->where('m.m_id', $m_id);

        // ดึงข้อมูล
        $query = $this->db->get();

        if ($query->num_rows() > 0) {
            $data = $query->row();

            // Decrypt the password using sha1() function (ถ้าจำเป็น)
            $data->m_password = sha1($data->m_password);

            return $data;
        }

        return false;
    }


    public function edit_Member($m_id)
    {
        $old_document = $this->db->get_where('tbl_member', array('m_id' => $m_id))->row();

        $update_doc_file = !empty($_FILES['m_img']['name']) && $old_document->m_img != $_FILES['m_img']['name'];

        // ตรวจสอบว่ามีการอัปโหลดรูปภาพใหม่หรือไม่
        if ($update_doc_file) {
            $old_file_path = './docs/img/' . $old_document->m_img;
            if (file_exists($old_file_path)) {
                unlink($old_file_path);
            }

            // Check used space
            $used_space_mb = $this->space_model->get_used_space();
            $upload_limit_mb = $this->space_model->get_limit_storage();

            $total_space_required = 0;
            if (!empty($_FILES['m_img']['name'])) {
                $total_space_required += $_FILES['m_img']['size'];
            }

            if ($used_space_mb + ($total_space_required / (1024 * 1024 * 1024)) >= $upload_limit_mb) {
                $this->session->set_flashdata('save_error', TRUE);
                redirect('Member_backend/edit/' . $m_id);
                return;
            }

            $config['upload_path'] = './docs/img';
            $config['allowed_types'] = 'gif|jpg|png';
            $this->load->library('upload', $config);

            if (!$this->upload->do_upload('m_img')) {
                echo $this->upload->display_errors();
                return;
            }

            $data = $this->upload->data();
            $filename = $data['file_name'];
        } else {
            // ใช้รูปภาพเดิม
            $filename = $old_document->m_img;
        }

        $data = array(
            'm_username' => $this->input->post('m_username'),
            'ref_pid' => $this->input->post('ref_pid'),
            'm_fname' => $this->input->post('m_fname'),
            'm_lname' => $this->input->post('m_lname'),
            'm_email' => $this->input->post('m_email'),
            'm_phone' => $this->input->post('m_phone'),
            'm_system' => $this->input->post('m_system'),
            'grant_user_ref_id' => $this->input->post('grant_user_ref_id'),
            'm_img' => $filename
        );

        $current_password = $this->input->post('current_password');
        $current_password2 = $this->input->post('current_password2');

        if (!empty($current_password) || !empty($current_password2)) {
            if ($current_password == $current_password2) {
                $data['m_password'] = sha1($current_password);
                $this->db->where('m_id', $m_id);
                $this->db->update('tbl_member', $data);
            } else {
                // รหัสผ่านไม่ตรงกัน
                $this->session->set_flashdata('password_mismatch', TRUE);
                redirect('Member_backend/edit/' . $m_id);
                return;
            }
        }
        $this->db->where('m_id', $m_id);
        $this->db->update('tbl_member', $data);

        // // ตรวจสอบ session สำหรับสมาชิกที่เข้าสู่ระบบ
        // $current_member_data = $this->db->get_where('tbl_member', array('m_id' => $m_id))->row();

        // if (
        //     $current_member_data->m_id == $this->session->userdata('m_id') &&
        //     $current_member_data->m_fname == $this->session->userdata('m_fname') &&
        //     $current_member_data->m_img == $this->session->userdata('m_img')
        // ) {
        //     // ไม่ต้องทำอะไรเพราะข้อมูล session ปัจจุบันตรงกับฐานข้อมูล
        // } else {
        //     // ทำการอัพเดต session หลังจากอัพเดตข้อมูลในฐานข้อมูล
        //     $updated_member_data = array(
        //         'm_id' => $current_member_data->m_id,
        //         'm_fname' => $current_member_data->m_fname,
        //         'm_img' => $current_member_data->m_img,
        //     );
        //     $this->session->set_userdata($updated_member_data);
        // }
        // // ลบถึงตรงนี้ 

        $this->space_model->update_server_current();
        $this->session->set_flashdata('save_success', TRUE);
    }

    public function edit_Profile($m_id)
    {
        $old_document = $this->db->get_where('tbl_member', array('m_id' => $m_id))->row();

        $update_doc_file = !empty($_FILES['m_img']['name']) && $old_document->m_img != $_FILES['m_img']['name'];

        // ตรวจสอบว่ามีการอัปโหลดรูปภาพใหม่หรือไม่
        if ($update_doc_file) {
            $old_file_path = './docs/img/' . $old_document->m_img;
            if (file_exists($old_file_path)) {
                unlink($old_file_path);
            }

            // Check used space
            $used_space_mb = $this->space_model->get_used_space();
            $upload_limit_mb = $this->space_model->get_limit_storage();

            $total_space_required = 0;
            if (!empty($_FILES['m_img']['name'])) {
                $total_space_required += $_FILES['m_img']['size'];
            }

            if ($used_space_mb + ($total_space_required / (1024 * 1024 * 1024)) >= $upload_limit_mb) {
                $this->session->set_flashdata('save_error', TRUE);
                redirect('Member_backend/edit/' . $m_id);
                return;
            }

            $config['upload_path'] = './docs/img';
            $config['allowed_types'] = 'gif|jpg|png';
            $this->load->library('upload', $config);

            if (!$this->upload->do_upload('m_img')) {
                echo $this->upload->display_errors();
                return;
            }

            $data = $this->upload->data();
            $filename = $data['file_name'];
        } else {
            // ใช้รูปภาพเดิม
            $filename = $old_document->m_img;
        }

        $data = array(
            'm_username' => $this->input->post('m_username'),
            'ref_pid' => $this->input->post('ref_pid'),
            'm_fname' => $this->input->post('m_fname'),
            'm_lname' => $this->input->post('m_lname'),
            'm_email' => $this->input->post('m_email'),
            'm_phone' => $this->input->post('m_phone'),
            // 'm_system' => $this->input->post('m_system'),
            // 'grant_user_ref_id' => $this->input->post('grant_user_ref_id'),
            'm_img' => $filename
        );

        $current_password = $this->input->post('current_password');
        $current_password2 = $this->input->post('current_password2');

        if (!empty($current_password) || !empty($current_password2)) {
            if ($current_password == $current_password2) {
                $data['m_password'] = sha1($current_password);
                $this->db->where('m_id', $m_id);
                $this->db->update('tbl_member', $data);
            } else {
                // รหัสผ่านไม่ตรงกัน
                $this->session->set_flashdata('password_mismatch', TRUE);
                redirect('Member_backend/edit/' . $m_id);
                return;
            }
        }
        $this->db->where('m_id', $m_id);
        $this->db->update('tbl_member', $data);

        // // ตรวจสอบ session สำหรับสมาชิกที่เข้าสู่ระบบ
        // $current_member_data = $this->db->get_where('tbl_member', array('m_id' => $m_id))->row();

        // if (
        //     $current_member_data->m_id == $this->session->userdata('m_id') &&
        //     $current_member_data->m_fname == $this->session->userdata('m_fname') &&
        //     $current_member_data->m_img == $this->session->userdata('m_img')
        // ) {
        //     // ไม่ต้องทำอะไรเพราะข้อมูล session ปัจจุบันตรงกับฐานข้อมูล
        // } else {
        //     // ทำการอัพเดต session หลังจากอัพเดตข้อมูลในฐานข้อมูล
        //     $updated_member_data = array(
        //         'm_id' => $current_member_data->m_id,
        //         'm_fname' => $current_member_data->m_fname,
        //         'm_img' => $current_member_data->m_img,
        //     );
        //     $this->session->set_userdata($updated_member_data);
        // }
        // // ลบถึงตรงนี้ 

        $this->space_model->update_server_current();
        $this->session->set_flashdata('save_success', TRUE);
    }

    public function edit_Profile_intranet($m_id)
    {
        $old_document = $this->db->get_where('tbl_member', array('m_id' => $m_id))->row();

        $update_doc_file = !empty($_FILES['m_img']['name']) && $old_document->m_img != $_FILES['m_img']['name'];

        // ตรวจสอบว่ามีการอัปโหลดรูปภาพใหม่หรือไม่
        if ($update_doc_file) {
            $old_file_path = './docs/img/' . $old_document->m_img;
            if (file_exists($old_file_path)) {
                unlink($old_file_path);
            }

            // Check used space
            $used_space_mb = $this->space_model->get_used_space();
            $upload_limit_mb = $this->space_model->get_limit_storage();

            $total_space_required = 0;
            if (!empty($_FILES['m_img']['name'])) {
                $total_space_required += $_FILES['m_img']['size'];
            }

            if ($used_space_mb + ($total_space_required / (1024 * 1024 * 1024)) >= $upload_limit_mb) {
                $this->session->set_flashdata('save_error', TRUE);
                redirect('System_intranet/profile' . $m_id);
                return;
            }

            $config['upload_path'] = './docs/img';
            $config['allowed_types'] = 'gif|jpg|png';
            $this->load->library('upload', $config);

            if (!$this->upload->do_upload('m_img')) {
                echo $this->upload->display_errors();
                return;
            }

            $data = $this->upload->data();
            $filename = $data['file_name'];
        } else {
            // ใช้รูปภาพเดิม
            $filename = $old_document->m_img;
        }

        $data = array(
            'm_username' => $this->input->post('m_username'),
            'ref_pid' => $this->input->post('ref_pid'),
            'm_fname' => $this->input->post('m_fname'),
            'm_lname' => $this->input->post('m_lname'),
            'm_email' => $this->input->post('m_email'),
            'm_phone' => $this->input->post('m_phone'),
            // 'm_system' => $this->input->post('m_system'),
            // 'grant_user_ref_id' => $this->input->post('grant_user_ref_id'),
            'm_img' => $filename
        );

        $current_password = $this->input->post('current_password');
        $current_password2 = $this->input->post('current_password2');

        if (!empty($current_password) || !empty($current_password2)) {
            if ($current_password == $current_password2) {
                $data['m_password'] = sha1($current_password);
                $this->db->where('m_id', $m_id);
                $this->db->update('tbl_member', $data);
            } else {
                // รหัสผ่านไม่ตรงกัน
                $this->session->set_flashdata('password_mismatch', TRUE);
                redirect('System_intranet/profile');
                return;
            }
        }
        $this->db->where('m_id', $m_id);
        $this->db->update('tbl_member', $data);

        // // ตรวจสอบ session สำหรับสมาชิกที่เข้าสู่ระบบ
        // $current_member_data = $this->db->get_where('tbl_member', array('m_id' => $m_id))->row();

        // if (
        //     $current_member_data->m_id == $this->session->userdata('m_id') &&
        //     $current_member_data->m_fname == $this->session->userdata('m_fname') &&
        //     $current_member_data->m_img == $this->session->userdata('m_img')
        // ) {
        //     // ไม่ต้องทำอะไรเพราะข้อมูล session ปัจจุบันตรงกับฐานข้อมูล
        // } else {
        //     // ทำการอัพเดต session หลังจากอัพเดตข้อมูลในฐานข้อมูล
        //     $updated_member_data = array(
        //         'm_id' => $current_member_data->m_id,
        //         'm_fname' => $current_member_data->m_fname,
        //         'm_img' => $current_member_data->m_img,
        //     );
        //     $this->session->set_userdata($updated_member_data);
        // }
        // // ลบถึงตรงนี้ 

        $this->space_model->update_server_current();
        $this->session->set_flashdata('save_success', TRUE);
    }

    // public function editmemberpwd()
    // {

    //     $data = array(
    //         'm_password' => sha1($this->input->post('m_password'))
    //     );

    //     $this->db->where('m_id', $this->input->post('m_id'));
    //     $query = $this->db->update('tbl_member', $data);

    //     if ($query) {
    //         echo "<script>";
    //         echo "alert('Update success !');";
    //         echo "</script>";
    //     } else {
    //         echo "<script>";
    //         echo "alert('Error !');";
    //         echo "</script>";
    //     }
    // }


    public function deldata($m_id)
    {
        $this->db->delete('tbl_member', array('m_id' => $m_id));
    }



    public function blockMember($m_id)
    {
        $data = array(
            'm_status' => 0,
        );

        $this->db->where('m_id', $m_id);
        $query = $this->db->update('tbl_member', $data);

        return $query;
    }
    public function unblockMember($m_id)
    {
        $data = array(
            'm_status' => 1,
        );

        $this->db->where('m_id', $m_id);
        $query = $this->db->update('tbl_member', $data);

        return $query;
    }

    public function count_member()
    {
        $this->db->select('COUNT(m_id) as member_total');
        $this->db->from('tbl_member');
        $query = $this->db->get();
        return $query->result();
    }

    public function count_member_ref_pid($pid)
    {
        // กำหนดเงื่อนไข WHERE
        $this->db->where('ref_pid', $pid);
        // ดึงข้อมูลจากตาราง tbl_member
        $query = $this->db->get('tbl_member');
        // คืนค่าจำนวนแถวที่ตรงกับเงื่อนไข
        return $query->num_rows();
    }

    public function count_members_ref_pid_3()
    {
        // กำหนดเงื่อนไข WHERE
        $this->db->where('ref_pid', 3);
        // ดึงข้อมูลจากตาราง tbl_member
        $query = $this->db->get('tbl_member');
        // คืนค่าจำนวนแถวที่ตรงกับเงื่อนไข
        return $query->num_rows();
    }
    public function count_members_ref_pid_4()
    {
        // กำหนดเงื่อนไข WHERE
        $this->db->where('ref_pid', 4);
        // ดึงข้อมูลจากตาราง tbl_member
        $query = $this->db->get('tbl_member');
        // คืนค่าจำนวนแถวที่ตรงกับเงื่อนไข
        return $query->num_rows();
    }
    public function count_members_ref_pid_5()
    {
        // กำหนดเงื่อนไข WHERE
        $this->db->where('ref_pid', 5);
        // ดึงข้อมูลจากตาราง tbl_member
        $query = $this->db->get('tbl_member');
        // คืนค่าจำนวนแถวที่ตรงกับเงื่อนไข
        return $query->num_rows();
    }
    public function count_members_ref_pid_6()
    {
        // กำหนดเงื่อนไข WHERE
        $this->db->where('ref_pid', 6);
        // ดึงข้อมูลจากตาราง tbl_member
        $query = $this->db->get('tbl_member');
        // คืนค่าจำนวนแถวที่ตรงกับเงื่อนไข
        return $query->num_rows();
    }
    public function count_members_ref_pid_7()
    {
        // กำหนดเงื่อนไข WHERE
        $this->db->where('ref_pid', 7);
        // ดึงข้อมูลจากตาราง tbl_member
        $query = $this->db->get('tbl_member');
        // คืนค่าจำนวนแถวที่ตรงกับเงื่อนไข
        return $query->num_rows();
    }
    public function count_members_ref_pid_8()
    {
        // กำหนดเงื่อนไข WHERE
        $this->db->where('ref_pid', 8);
        // ดึงข้อมูลจากตาราง tbl_member
        $query = $this->db->get('tbl_member');
        // คืนค่าจำนวนแถวที่ตรงกับเงื่อนไข
        return $query->num_rows();
    }
    public function count_members_ref_pid_9()
    {
        // กำหนดเงื่อนไข WHERE
        $this->db->where('ref_pid', 9);
        // ดึงข้อมูลจากตาราง tbl_member
        $query = $this->db->get('tbl_member');
        // คืนค่าจำนวนแถวที่ตรงกับเงื่อนไข
        return $query->num_rows();
    }
    public function count_members_ref_pid_10()
    {
        // กำหนดเงื่อนไข WHERE
        $this->db->where('ref_pid', 10);
        // ดึงข้อมูลจากตาราง tbl_member
        $query = $this->db->get('tbl_member');
        // คืนค่าจำนวนแถวที่ตรงกับเงื่อนไข
        return $query->num_rows();
    }
    public function count_members_ref_pid_11()
    {
        // กำหนดเงื่อนไข WHERE
        $this->db->where('ref_pid', 11);
        // ดึงข้อมูลจากตาราง tbl_member
        $query = $this->db->get('tbl_member');
        // คืนค่าจำนวนแถวที่ตรงกับเงื่อนไข
        return $query->num_rows();
    }










    public function get_filtered_members_2($search = '', $status = '', $limit = 20, $offset = 0, $user_system = '')
    {
        $this->db->select('m.*, p.pname');
        $this->db->from('tbl_member as m');
        $this->db->join('tbl_position as p', 'm.ref_pid = p.pid', 'left');

        // เงื่อนไขการกรองข้อมูล
        if (!empty($search)) {
            $this->db->group_start();
            $this->db->like('m.m_username', $search);
            $this->db->or_like('m.m_fname', $search);
            $this->db->or_like('m.m_lname', $search);
            $this->db->or_like('m.m_email', $search);
            $this->db->or_like('m.m_phone', $search);
            $this->db->group_end();
        }

        if (!empty($status)) {
            $this->db->where('m.m_status', $status);
        }

        // ถ้าเป็น super_admin จะไม่เห็น system_admin
        if ($user_system == 'super_admin') {
            $this->db->where('m.m_system !=', 'system_admin');
        }

        // Order by
        $this->db->order_by('m.m_id', 'DESC');

        // Limit & Offset
        $this->db->limit($limit, $offset);

        return $this->db->get()->result();
    }

    // แก้ไขฟังก์ชันสำหรับนับจำนวนทั้งหมดด้วย
    public function count_filtered_members_2($search = '', $status = '', $user_system = '')
    {
        $this->db->from('tbl_member as m');
        $this->db->join('tbl_position as p', 'm.ref_pid = p.pid', 'left');

        if (!empty($search)) {
            $this->db->group_start();
            $this->db->like('m.m_username', $search);
            $this->db->or_like('m.m_fname', $search);
            $this->db->or_like('m.m_lname', $search);
            $this->db->or_like('m.m_email', $search);
            $this->db->or_like('m.m_phone', $search);
            $this->db->group_end();
        }

        if (!empty($status)) {
            $this->db->where('m.m_status', $status);
        }

        if ($user_system == 'super_admin') {
            $this->db->where('m.m_system !=', 'system_admin');
        }

        return $this->db->count_all_results();
    }

    // ระบบจัดการสมาชิก Back office ---------------------
    public function get_filtered_members_3($search = '', $status = '', $limit = 10, $offset = 0)
    {
        $this->db->select('*');

        // เพิ่ม where condition สำหรับ grant_system_ref_id
        $this->db->where('FIND_IN_SET(3, grant_system_ref_id) >', 0);

        if (!empty($search)) {
            $this->db->group_start();
            $this->db->like('m_username', $search);
            $this->db->or_like('m_fname', $search);
            $this->db->or_like('m_lname', $search);
            $this->db->or_like('m_email', $search);
            $this->db->or_like('m_phone', $search);
            $this->db->group_end();
        }

        if (!empty($status)) {
            $this->db->where('status', $status);
        }

        $this->db->limit($limit, $offset);
        $this->db->order_by('m_datesave', 'DESC');

        return $this->db->get('tbl_member')->result();
    }

    public function count_filtered_members_3($search = '', $status = '')
    {
        // เพิ่ม where condition สำหรับ grant_system_ref_id
        $this->db->where('FIND_IN_SET(3, grant_system_ref_id) >', 0);

        if (!empty($search)) {
            $this->db->group_start();
            $this->db->like('m_username', $search);
            $this->db->or_like('m_fname', $search);
            $this->db->or_like('m_lname', $search);
            $this->db->or_like('m_email', $search);
            $this->db->or_like('m_phone', $search);
            $this->db->group_end();
        }

        if (!empty($status)) {
            $this->db->where('status', $status);
        }

        return $this->db->count_all_results('tbl_member');
    }

    // ระบบจัดการสมาชิก Saraban ---------------------
    public function get_filtered_members_4($search = '', $status = '', $limit = 10, $offset = 0)
    {
        $this->db->select('tbl_member.*, tbl_member_saraban.can_sign, tbl_position.pname')
            ->from('tbl_member')
            ->join('tbl_member_saraban', 'tbl_member.m_id = tbl_member_saraban.member_id', 'left')
            ->join('tbl_position', 'tbl_member.ref_pid = tbl_position.pid', 'left')
            ->where("FIND_IN_SET('4', tbl_member.grant_system_ref_id) >", 0);

        if (!empty($search)) {
            $this->db->group_start()
                ->like('tbl_member.m_username', $search)
                ->or_like('tbl_member.m_fname', $search)
                ->or_like('tbl_member.m_lname', $search)
                ->or_like('tbl_member.m_email', $search)
                ->group_end();
        }

        if (!empty($status)) {
            $this->db->where('tbl_member.status', $status);
        }

        $this->db->limit($limit, $offset);
        $this->db->order_by('tbl_member.m_datesave', 'DESC');

        return $this->db->get()->result();
    }

    public function count_filtered_members_4($search = '', $status = '')
    {
        // เพิ่ม where condition สำหรับ grant_system_ref_id
        $this->db->where('FIND_IN_SET(4, grant_system_ref_id) >', 0);

        if (!empty($search)) {
            $this->db->group_start();
            $this->db->like('m_username', $search);
            $this->db->or_like('m_fname', $search);
            $this->db->or_like('m_lname', $search);
            $this->db->or_like('m_email', $search);
            $this->db->or_like('m_phone', $search);
            $this->db->group_end();
        }

        if (!empty($status)) {
            $this->db->where('status', $status);
        }

        return $this->db->count_all_results('tbl_member');
    }

   

    public function toggle_status_saraban($member_id, $status)
    {
        // Check if record exists
        $exists = $this->db->where('member_id', $member_id)
            ->get('tbl_member_saraban')
            ->num_rows();

        if ($exists) {
            // Update existing record
            return $this->db->where('member_id', $member_id)
                ->update('tbl_member_saraban', [
                    'can_sign' => $status,
                    'ms_by' => $this->session->userdata('m_username'),
                    'ms_date' => date('Y-m-d H:i:s')
                ]);
        } else {
            // Insert new record
            return $this->db->insert('tbl_member_saraban', [
                'member_id' => $member_id,
                'can_sign' => $status,
                'ms_by' => $this->session->userdata('m_username'),
                'ms_date' => date('Y-m-d H:i:s')
            ]);
        }
    }



    // ระบบจัดการสมาชิก จองคิวรถ ---------------------
    public function get_filtered_members_5($search = '', $status = '', $limit = 10, $offset = 0)
    {
        $this->db->select('tbl_member.*, tbl_member_car.is_head, tbl_member_car.is_user');
        $this->db->from('tbl_member');
        $this->db->join('tbl_member_car', 'tbl_member.m_id = tbl_member_car.member_id', 'left');
        $this->db->where("FIND_IN_SET('5', tbl_member.grant_system_ref_id) >", 0);

        if (!empty($search)) {
            $this->db->group_start();
            $this->db->like('tbl_member.m_username', $search);
            $this->db->or_like('tbl_member.m_fname', $search);
            $this->db->or_like('tbl_member.m_lname', $search);
            $this->db->or_like('tbl_member.m_email', $search);
            $this->db->group_end();
        }

        if (!empty($status)) {
            $this->db->where('tbl_member.status', $status);
        }

        $this->db->limit($limit, $offset);
        $this->db->order_by('tbl_member.m_datesave', 'DESC');

        return $this->db->get()->result();
    }

    public function toggle_status_car($member_id, $status_type, $status)
    {
        // Check if record exists
        $exists = $this->db->where('member_id', $member_id)
            ->get('tbl_member_car')
            ->num_rows();

        $data = [
            'mc_by' => $this->session->userdata('m_username'),
            'mc_update_date' => date('Y-m-d H:i:s'),
            'mc_update_by' => $this->session->userdata('m_username')
        ];

        // Add the appropriate status field based on status_type
        if ($status_type === 'head') {
            $data['is_head'] = $status;
        } else {
            $data['is_user'] = $status;
        }

        if ($exists) {
            // Update existing record
            return $this->db->where('member_id', $member_id)
                ->update('tbl_member_car', $data);
        } else {
            // Insert new record
            $data['member_id'] = $member_id;
            $data['mc_create_date'] = date('Y-m-d H:i:s');
            $data['mc_create_by'] = $this->session->userdata('m_username');
            return $this->db->insert('tbl_member_car', $data);
        }
    }

    public function count_filtered_members_5($search = '', $status = '')
    {
        // เพิ่ม where condition สำหรับ grant_system_ref_id
        $this->db->where('FIND_IN_SET(5, grant_system_ref_id) >', 0);

        if (!empty($search)) {
            $this->db->group_start();
            $this->db->like('m_username', $search);
            $this->db->or_like('m_fname', $search);
            $this->db->or_like('m_lname', $search);
            $this->db->or_like('m_email', $search);
            $this->db->or_like('m_phone', $search);
            $this->db->group_end();
        }

        if (!empty($status)) {
            $this->db->where('status', $status);
        }

        return $this->db->count_all_results('tbl_member');
    }

    public function get_system_limit_car()
    {
        $this->db->select('grant_system_id, grant_system_name, grant_system_limit');
        $this->db->from('tbl_grant_system');
        $this->db->where('grant_system_id', 5);
        return $this->db->get()->row(); // เปลี่ยนจาก result() เป็น row()
    }


    // ระบบจัดการสมาชิก แก้ไข ---------------------
    public function get_member_by_id($m_id)
    {
        $this->db->where('m_id', $m_id);
        $query = $this->db->get('tbl_member');
        return $query->row();
    }

    // จัดการระบบหลัก ---------------------
    public function get_filtered_system($search = '', $status = '', $limit = 10, $offset = 0)
    {
        $this->db->select('*');

        if (!empty($search)) {
            $this->db->group_start();
            $this->db->like('grant_system_name', $search);
            $this->db->group_end();
        }

        if (!empty($status)) {
            $this->db->where('status', $status);
        }

        $this->db->limit($limit, $offset);
        $this->db->order_by('grant_system_id', 'asc');

        return $this->db->get('tbl_grant_system')->result();
    }

    public function count_filtered_system($search = '', $status = '')
    {

        if (!empty($search)) {
            $this->db->group_start();
            $this->db->like('grant_system_name', $search);
            $this->db->group_end();
        }

        if (!empty($status)) {
            $this->db->where('status', $status);
        }

        return $this->db->count_all_results('tbl_grant_system');
    }

    public function update_system_status($grant_system_id, $status)
    {
        return $this->db->update(
            'tbl_grant_system',
            ['grant_system_menu' => $status],
            ['grant_system_id' => $grant_system_id]
        );
    }

    public function count_filtered_logs($search_user = null, $date_from = null, $date_to = null)
    {
        if ($search_user) {
            $this->db->like('deleted_by', $search_user);
        }

        if ($date_from) {
            $this->db->where('DATE(delete_date) >=', $date_from);
        }

        if ($date_to) {
            $this->db->where('DATE(delete_date) <=', $date_to);
        }

        return $this->db->count_all_results('tbl_log_delete');
    }

    public function get_filtered_logs($search_user = null, $date_from = null, $date_to = null, $limit = null, $offset = null)
    {
        if ($search_user) {
            $this->db->like('deleted_by', $search_user);
        }

        if ($date_from) {
            $this->db->where('DATE(delete_date) >=', $date_from);
        }

        if ($date_to) {
            $this->db->where('DATE(delete_date) <=', $date_to);
        }

        $this->db->order_by('delete_date', 'DESC');

        if ($limit) {
            $this->db->limit($limit, $offset);
        }

        $query = $this->db->get('tbl_log_delete');
        $logs = $query->result();

        foreach ($logs as $log) {
            $log->deleted_data = json_decode($log->deleted_data);
        }

        return $logs;
    }

    public function get_log_by_id($log_id)
    {
        return $this->db->get_where('tbl_log_delete', ['log_id' => $log_id])->row();
    }

    // ระบบจัดการพัสดุ/ครุภัณฑ์ ---------------------
    public function get_filtered_members_10($search = null, $status = null, $limit = 10, $offset = 0)
    {
        $this->db->select('m.*, ma.can_add, ma.can_edit, ma.can_view, ma.can_delete, ma.created_at, ma.updated_at');
        $this->db->from('tbl_member m');
        $this->db->join('tbl_member_assets ma', 'm.m_id = ma.m_id', 'left');
        $this->db->where("FIND_IN_SET('10', m.grant_system_ref_id) >", 0);

        if ($search) {
            $this->db->group_start();
            $this->db->like('m.m_fname', $search);
            $this->db->or_like('m.m_lname', $search);
            $this->db->or_like('m.m_email', $search);
            $this->db->group_end();
        }

        if ($status) {
            $this->db->where('m.m_status', $status);
        }

        $this->db->limit($limit, $offset);
        return $this->db->get()->result();
    }






    public function count_filtered_members_10($search = '', $status = '')
    {
        // เพิ่ม where condition สำหรับ grant_system_ref_id
        $this->db->where('FIND_IN_SET(10, grant_system_ref_id) >', 0);

        if (!empty($search)) {
            $this->db->group_start();
            $this->db->like('m_username', $search);
            $this->db->or_like('m_fname', $search);
            $this->db->or_like('m_lname', $search);
            $this->db->or_like('m_email', $search);
            $this->db->or_like('m_phone', $search);
            $this->db->group_end();
        }

        if (!empty($status)) {
            $this->db->where('status', $status);
        }

        return $this->db->count_all_results('tbl_member');
    }

public function get_system_limit_assets()
{
    $this->db->select('id, name, status, description');
    $this->db->from('tbl_member_modules');
    $this->db->where('id', 10); // ระบบพัสดุ ID=10
    return $this->db->get()->row();
}


    // สำหรับพัสดุ
    public function get_members_with_assets_permissions($search = null)
    {
        $this->db->select('m.*, ma.can_add, ma.can_edit, ma.can_view, ma.can_delete');
        $this->db->from('tbl_member m');
        $this->db->join('tbl_member_assets ma', 'm.m_id = ma.m_id', 'left');
        $this->db->where("FIND_IN_SET('10', m.grant_system_ref_id) >", 0); // เลือกเฉพาะผู้ใช้ที่มีสิทธิ์ระบบพัสดุ

        if ($search) {
            $this->db->group_start();
            $this->db->like('m.m_fname', $search);
            $this->db->or_like('m.m_lname', $search);
            $this->db->or_like('m.m_email', $search);
            $this->db->group_end();
        }

        return $this->db->get()->result();
    }






    //พี่ตั้น


    public function list_position_back_office()
    {
        $this->db->where('pid !=', 1); // ไม่เอาตำแหน่งที่มี pid = 1
        $this->db->order_by('pid', 'ASC');
        $query = $this->db->get('tbl_position');
        return $query->result();
    }

    public function get_member_counts_by_position()
    {
        $this->db->select('ref_pid, COUNT(*) as count');
        $this->db->from('tbl_member');
        $this->db->where("FIND_IN_SET('2', grant_system_ref_id) >", 0);  // ดึงเฉพาะสมาชิกที่มีสิทธิ์ในระบบจัดการเว็บไซต์
        $this->db->where('ref_pid IS NOT NULL');  // เฉพาะที่มีตำแหน่ง
        $this->db->group_by('ref_pid');
        $result = $this->db->get()->result();

        $counts = array();
        foreach ($result as $row) {
            $counts[$row->ref_pid] = $row->count;
        }

        return $counts;
    }

    public function get_positions()
    {
        $this->db->select('*');
        $this->db->from('tbl_position');
        $this->db->order_by('pid', 'ASC');
        return $this->db->get()->result();
    }
    public function count_member_by_position($position_id, $user_system)
    {
        $this->db->where('ref_pid', $position_id);

        // ถ้าเป็น super_admin จะไม่นับ system_admin
        if ($user_system == 'super_admin') {
            $this->db->where('m_system !=', 'system_admin');
        }

        return $this->db->count_all_results('tbl_member');
    }

    // ใน member_model.php 
public function get_filtered_members($search, $status, $limit, $offset, $user_system) {
    $this->db->select('m.*, p.pname, 
                      GROUP_CONCAT(DISTINCT mm.name) as system_names, 
                      GROUP_CONCAT(DISTINCT mm.id) as system_ids');
    $this->db->from('tbl_member as m');
    $this->db->join('tbl_position as p', 'm.ref_pid = p.pid', 'left');
    $this->db->join('tbl_member_systems as ms', 'm.m_id = ms.m_id', 'left');
    $this->db->join('tbl_member_modules as mm', 'ms.module_id = mm.id', 'left');

    // ถ้าเป็น super_admin จะไม่เห็น system_admin
    if ($user_system == 'super_admin') {
        $this->db->where('m.m_system !=', 'system_admin');
    }

    if (!empty($search)) {
        $this->db->group_start();
        $this->db->like('m.m_username', $search);
        $this->db->or_like('m.m_fname', $search);
        $this->db->or_like('m.m_lname', $search);
        $this->db->or_like('m.m_email', $search);
        $this->db->group_end();
    }

    if (!empty($status)) {
        $this->db->where('m.m_status', $status);
    }

    $this->db->group_by('m.m_id');
    $this->db->order_by('m.m_id', 'DESC');
    $this->db->limit($limit, $offset);

    return $this->db->get()->result();
}

// สำหรับ count ก็ต้องปรับเช่นกัน
public function count_filtered_members($search, $status, $user_system) {
    $this->db->select('m.m_id');
    $this->db->from('tbl_member as m');
    $this->db->join('tbl_member_systems as ms', 'm.m_id = ms.m_id', 'left');
    $this->db->join('tbl_member_modules as mm', 'ms.module_id = mm.id', 'left');

    if ($user_system == 'super_admin') {
        $this->db->where('m.m_system !=', 'system_admin');
    }

    if (!empty($search)) {
        $this->db->group_start();
        $this->db->like('m.m_username', $search);
        $this->db->or_like('m.m_fname', $search);
        $this->db->or_like('m.m_lname', $search);
        $this->db->or_like('m.m_email', $search);
        $this->db->group_end();
    }

    if (!empty($status)) {
        $this->db->where('m.m_status', $status);
    }

    $this->db->group_by('m.m_id');
    return $this->db->get()->num_rows();
}

    public function count_total_members($user_system)
    {
        if ($user_system == 'super_admin') {
            $this->db->where('m_system !=', 'system_admin');
        }
        return $this->db->count_all_results('tbl_member');
    }


    public function get_website_permissions()
    {
        return $this->db->where('grant_user_id !=', 1)
            ->get('tbl_grant_user')
            ->result();
    }

    public function check_email_exists($email)
    {
        $this->db->group_start()
            ->where('m_username', $email)
            ->or_where('m_email', $email)
            ->group_end();
        $query = $this->db->get('tbl_member');
        return $query->num_rows() > 0;
    }


    
    public function count_filtered_members_web($search = '', $status = '', $user_system = '')
    {
        $this->db->from('tbl_member as m');
        $this->db->join('tbl_position as p', 'm.ref_pid = p.pid', 'left');

        if (!empty($search)) {
            $this->db->group_start();
            $this->db->like('m.m_username', $search);
            $this->db->or_like('m.m_fname', $search);
            $this->db->or_like('m.m_lname', $search);
            $this->db->or_like('m.m_email', $search);
            $this->db->or_like('m.m_phone', $search);
            $this->db->group_end();
        }

        if (!empty($status)) {
            $this->db->where('m.m_status', $status);
        }

        if ($user_system == 'super_admin') {
            $this->db->where('m.m_system !=', 'system_admin');
        }

        return $this->db->count_all_results();
    }

    public function get_members_status_count_web($user_system = '')
    {
        $this->db->select('m_system, COUNT(*) as count');
        $this->db->from('tbl_member');

        if ($user_system == 'super_admin') {
            $this->db->where('m_system !=', 'system_admin');
        }

        $this->db->group_by('m_system');
        $result = $this->db->get()->result();

        $counts = array();
        foreach ($result as $row) {
            $counts[$row->m_system] = $row->count;
        }

        return $counts;
    }

    public function get_members_position_count_web($user_system = '')
    {
        $this->db->select('p.pid, p.pname, COUNT(m.m_id) as count');
        $this->db->from('tbl_member m');
        $this->db->join('tbl_position p', 'm.ref_pid = p.pid', 'left');

        if ($user_system == 'super_admin') {
            $this->db->where('m.m_system !=', 'system_admin');
        }

        $this->db->where('p.pid IS NOT NULL');
        $this->db->where('p.pid NOT IN (1, 2)'); // ไม่นับ pid 1 และ 2
        $this->db->group_by('p.pid, p.pname');

        return $this->db->get()->result();
    }

    public function get_member_web_by_id($m_id)
    {
        $this->db->select('m.*, p.pname');
        $this->db->from('tbl_member m');
        $this->db->join('tbl_position p', 'm.ref_pid = p.pid', 'left');
        $this->db->where('m.m_id', $m_id);
        return $this->db->get()->row();
    }
	
	
	
public function get_available_modules() {
    return $this->db->select('*')
                    ->from('tbl_member_modules')
                    ->where('status', 1) // เฉพาะระบบที่เปิดใช้งาน
                    ->order_by('display_order', 'ASC')
                    ->get()
                    ->result();
}

public function get_member_systems($m_id) {
    return $this->db->select('ms.*, mm.name as module_name')
                    ->from('tbl_member_systems ms')
                    ->join('tbl_member_modules mm', 'mm.id = ms.system_id')
                    ->where('ms.m_id', $m_id)
                    ->get()
                    ->result();
}
	
	
	
	public function get_filtered_members_web($search = '', $status = '', $limit = null, $offset = null, $user_system = '', $sort_by = 'm_id', $sort_order = 'desc') {
    // กำหนดการแมประหว่างพารามิเตอร์การเรียงลำดับและชื่อคอลัมน์ในฐานข้อมูล
    $column_map = [
        'name' => 'm.m_fname',
        'position' => 'p.pname',
        'type' => 'm.m_system',
        'system' => 'GROUP_CONCAT(DISTINCT mm.name)',
        'date' => 'm.m_datesave',
        'm_id' => 'm.m_id',
        // เพิ่มคอลัมน์อื่นๆ ตามต้องการ
    ];
    
    // ใช้ชื่อคอลัมน์ที่ถูกต้องสำหรับการเรียงลำดับ
    $sort_column = isset($column_map[$sort_by]) ? $column_map[$sort_by] : 'm.m_id';
    
    $this->db->select('m.*, p.pname, 
                      GROUP_CONCAT(DISTINCT mm.name) as system_names, 
                      GROUP_CONCAT(DISTINCT mm.id) as system_ids');
    $this->db->from('tbl_member as m');
    $this->db->join('tbl_position as p', 'm.ref_pid = p.pid', 'left');
    $this->db->join('tbl_member_systems as ms', 'm.m_id = ms.m_id', 'left');
    $this->db->join('tbl_member_modules as mm', 'ms.module_id = mm.id', 'left');

    if ($user_system == 'super_admin') {
        $this->db->where('m.m_system !=', 'system_admin');
    }

    if (!empty($search)) {
        $this->db->group_start();
        $this->db->like('m.m_username', $search);
        $this->db->or_like('m.m_fname', $search);
        $this->db->or_like('m.m_lname', $search);
        $this->db->or_like('m.m_email', $search);
        $this->db->group_end();
    }

    if (!empty($status)) {
        $this->db->where('m.m_status', $status);
    }

    $this->db->group_by('m.m_id');
    $this->db->order_by($sort_column, $sort_order);
    
    if ($limit !== null) {
        $this->db->limit($limit, $offset);
    }

    return $this->db->get()->result();
}
	
	
	
	
	
	
	
	    public function get_members()
    {
        return $this->db->select('m_id, m_fname, m_lname, m_email')
            ->from('tbl_member')
            ->order_by('m_fname', 'ASC')
            ->get()
            ->result();
    }
	
	
	
	
	
	// ================== Google 2FA Functions ==================
    
    // ฟังก์ชันเข้าสู่ระบบ (แก้ไขให้ดึงข้อมูล 2FA ด้วย)
    public function fetch_user_login($username, $password)
    {
        $this->db->select('m.*, p.pname');
        $this->db->from('tbl_member m');
        $this->db->join('tbl_position p', 'm.ref_pid = p.pid', 'left');
        $this->db->where('m.m_username', $username);
        $this->db->where('m.m_password', $password);
        $this->db->where('m.m_status', 1);
        $query = $this->db->get();
        
        if ($query->num_rows() > 0) {
            return $query->row();
        }
        return false;
    }

    // ฟังก์ชันอัพเดท 2FA secret
    public function update_2fa_secret($user_id, $secret)
    {
        $data = [
            'google2fa_secret' => $secret,
            'google2fa_enabled' => 0  // ยังไม่เปิดใช้งาน
        ];
        
        $this->db->where('m_id', $user_id);
        return $this->db->update('tbl_member', $data);
    }

    // ฟังก์ชันเปิดใช้งาน 2FA
    public function enable_2fa($user_id)
    {
        $data = [
            'google2fa_enabled' => 1,
            'google2fa_setup_date' => date('Y-m-d H:i:s')
        ];
        
        $this->db->where('m_id', $user_id);
        return $this->db->update('tbl_member', $data);
    }

    // ฟังก์ชันปิดใช้งาน 2FA
    public function disable_2fa($user_id)
    {
        $data = [
            'google2fa_enabled' => 0,
            'google2fa_secret' => null,
            'google2fa_setup_date' => null
        ];
        
        $this->db->where('m_id', $user_id);
        $result = $this->db->update('tbl_member', $data);
        
        // ลบ backup codes
        if ($result) {
            $this->db->where('user_id', $user_id);
            $this->db->delete('tbl_2fa_backup_codes');
        }
        
        return $result;
    }

    // ฟังก์ชันดึงข้อมูล 2FA ของผู้ใช้
    public function get_2fa_info($user_id)
    {
        $this->db->select('google2fa_secret, google2fa_enabled, google2fa_setup_date');
        $this->db->where('m_id', $user_id);
        $query = $this->db->get('tbl_member');
        
        if ($query->num_rows() > 0) {
            return $query->row();
        }
        return false;
    }

    // ฟังก์ชันสร้าง backup codes
    public function create_backup_codes($user_id, $codes)
    {
        // ลบ backup codes เก่า
        $this->db->where('user_id', $user_id);
        $this->db->delete('tbl_2fa_backup_codes');
        
        // เพิ่ม backup codes ใหม่
        foreach ($codes as $code) {
            $data = [
                'user_id' => $user_id,
                'code' => $code,
                'created_at' => date('Y-m-d H:i:s'),
                'used' => 0
            ];
            $this->db->insert('tbl_2fa_backup_codes', $data);
        }
        
        return true;
    }

    // ฟังก์ชันดึง backup codes ที่ยังไม่ใช้
    public function get_backup_codes($user_id)
    {
        $this->db->select('code');
        $this->db->where('user_id', $user_id);
        $this->db->where('used', 0);
        $this->db->order_by('created_at', 'ASC');
        $query = $this->db->get('tbl_2fa_backup_codes');
        
        $codes = [];
        foreach ($query->result() as $row) {
            $codes[] = $row->code;
        }
        
        return $codes;
    }

    // ฟังก์ชันใช้ backup code
    public function use_backup_code($user_id, $code)
    {
        // ตรวจสอบว่า backup code ถูกต้องและยังไม่ใช้
        $this->db->where('user_id', $user_id);
        $this->db->where('code', $code);
        $this->db->where('used', 0);
        $query = $this->db->get('tbl_2fa_backup_codes');
        
        if ($query->num_rows() > 0) {
            // ทำเครื่องหมายว่าใช้แล้ว
            $this->db->where('user_id', $user_id);
            $this->db->where('code', $code);
            $this->db->update('tbl_2fa_backup_codes', [
                'used' => 1,
                'used_at' => date('Y-m-d H:i:s')
            ]);
            
            return true;
        }
        
        return false;
    }

    // ฟังก์ชันบันทึก log การใช้งาน 2FA
    public function log_2fa_activity($user_id, $action, $ip_address = null, $user_agent = null)
    {
        $data = [
            'user_id' => $user_id,
            'action' => $action,
            'ip_address' => $ip_address ?: $this->input->ip_address(),
            'user_agent' => $user_agent ?: $this->input->user_agent(),
            'created_at' => date('Y-m-d H:i:s')
        ];
        
        return $this->db->insert('tbl_2fa_logs', $data);
    }

    // ฟังก์ชันดึง log การใช้งาน 2FA
    public function get_2fa_logs($user_id, $limit = 50)
    {
        $this->db->select('action, ip_address, created_at');
        $this->db->where('user_id', $user_id);
        $this->db->order_by('created_at', 'DESC');
        $this->db->limit($limit);
        $query = $this->db->get('tbl_2fa_logs');
        
        return $query->result();
    }

    // ฟังก์ชันนับจำนวนผู้ใช้ที่เปิด 2FA
    public function count_2fa_enabled_users()
    {
        $this->db->where('google2fa_enabled', 1);
        $this->db->where('m_status', 1);
        return $this->db->count_all_results('tbl_member');
    }

    // ฟังก์ชันดึงรายชื่อผู้ใช้ที่เปิด 2FA
    public function get_2fa_enabled_users()
    {
        $this->db->select('m_id, m_username, m_fname, m_lname, google2fa_setup_date');
        $this->db->where('google2fa_enabled', 1);
        $this->db->where('m_status', 1);
        $this->db->order_by('google2fa_setup_date', 'DESC');
        $query = $this->db->get('tbl_member');
        
        return $query->result();
    }
	
	
	
}
