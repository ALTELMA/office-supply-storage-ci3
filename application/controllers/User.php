<?php if (!defined('BASEPATH')) {
    exit('No direct script access allowed');
}

class User extends MY_Controller
{

    public function __construct()
    {
        parent::__construct();

        // LOAD MODEL
        $this->load->model('userModel', '', true);

        // LOAD LIBRARY
        $this->load->library('MyUpload');
        $this->load->library('MyDateSystem');
    }

    public function index()
    {
    }

    // CHECK USER LOGIN
    public function verifylogin()
    {
        // SETUP VARIABLE
        $username = $this->input->post('username');
        $password = $this->input->post('password');
        $encodePassword = md5($password);

        $result = $this->userModel->checkUserLogin($username, $encodePassword);

        if ($result) {
            $sess_array = array();
            foreach ($result as $row) {
                $sess_array = [
                    'user_id' => $row->user_id,
                    'username' => $row->username,
                    'name' => $row->name
                ];
                $this->userModel->userLoginUpdate($row->user_id);
            }

            $this->session->set_userdata('userLogData', $sess_array);
            redirect('dashboard/index', 'refresh');
        } else {
            redirect('', 'refresh');
        }
    }

    // USER LOGOUT
    public function logout()
    {
        $this->session->sess_destroy();
        redirect('product', 'refresh');
    }

    public function view($id)
    {
        $user = $this->userModel->getUserData($this->session->userdata('userLogData')['user_id']);

        $this->data['title'] = 'ข้อมูลผู้ใช้งานระบบ';
        $this->data['user'] = $user;
        $this->content = 'user/detail';
        $this->layout();
    }

    public function edit($id)
    {
        $user = $this->userModel->getUserData($this->session->userdata('userLogData')['user_id']);

        $this->data['title'] = 'แก้ไขข้อมูลผู้ใช้งานระบบ';
        $this->data['user'] = $user;
        $this->content = 'user/edit';
        $this->layout();
    }

    public function update($id)
    {
        $this->userModel->update($this->input->post(), $id);

        redirect('user/view/' . $id, 'refresh');
    }

    public function password($id)
    {
        $user = $this->userModel->getUserData($this->session->userdata('userLogData')['user_id']);

        $this->data['title'] = 'เปลี่ยนรหัสผ่าน';
        $this->data['user'] = $user;
        $this->content = 'user/password';
        $this->layout();
    }

    public function updatePassword($id)
    {
        if ($this->userModel->updatePassword($this->input->post(), $id)) {
            redirect('user/view/' . $id, 'refresh');
        } else {
            redirect('user/password/' . $id, 'refresh');
        }
    }
}

/* End of file user.php */
/* Location : ./application/contollers/user.php */;
