<?php if (!defined('BASEPATH')) {
    exit('No direct script access allowed');
}

class UserModel extends CI_Model
{
    public function getUserList()
    {
        $this->db->select()->from('users');
        $query = $this->db->get();

        if ($query->num_rows() >= 1) {
            return $query->result();
        } else {
            return false;
        }
    }

    public function checkUserLogin($username, $password)
    {
        $condition = array('username' => $username, 'password' => $password);

        $this->db->select()->from('users');
        $this->db->where($condition);
        $query = $this->db->get();

        if ($query->num_rows() == 1) {
            return $query->result();
        } else {
            return false;
        }
    }

    // UPDATE USER LOGIN DATE
    public function userLoginUpdate($id)
    {
        $dataUpdate = array('LastLoginDate' => date('Y-m-d H:i:s'));
        $condition = array('user_id' => $id);
        $this->db->update('users', $dataUpdate, $condition);
    }

    // GET USER SINGLE DATA
    public function getUserData($id)
    {
        $this->db->select()->from('users')->where(['user_id' => $id])->limit(1);
        $query = $this->db->get();

        if ($query->num_rows() == 1) {
            return $query->row();
        } else {
            return false;
        }
    }

    public function create($inputs)
    {
        $data = [
            'username' => $inputs['txt_username'],
            'name' => $inputs['txt_name'],
            'password' => md5($inputs['txt_password']),
            'password_format' => $inputs['txt_password']
        ];

        $this->db->insert('users', $data);
    }

    public function update($inputs, $id)
    {
        $data = [
            'username' => $inputs['txt_username'],
            'name' => $inputs['txt_name']
        ];

        $this->db->update('users', $data, ['user_id' => $id]);
    }

    public function updateData($table, $data, $field, $value)
    {
        $cond = array($field => $value);
        $this->db->update($table, $data, $cond);
    }

    public function delete($id)
    {
        $this->db->delete('users', ['user_id' => $id]);
    }

    public function updatePassword($inputs, $id)
    {
        $user = $this->getUserData($id);

        if ($user->password == md5($inputs['txt_current_password'])) {
            if (!empty($inputs['txt_new_password']) && $inputs['txt_new_password'] == $inputs['txt_confirm_password']) {
                $data = [
                    'password' => md5($inputs['txt_new_password']),
                    'password_format' => $inputs['txt_new_password']
                ];

                $this->db->update('users', $data, ['user_id' => $id]);
            } else {
                $this->session->set_userdata('error', 'รหัสผ่านไม่ตรงกัน');

                return false;
            }
        } else {
            $this->session->set_userdata('error', 'รหัสผ่านไม่ถูกต้อง');

            return false;
        }
    }
}

/* End of file userModel.php */
/* Location : ./application/models/userModel.php */;
