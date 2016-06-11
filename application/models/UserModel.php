<?php if(!defined('BASEPATH')) exit('No direct script access allowed');

class UserModel extends CI_Model{

	// GET USER LIST
	public function getUserList(){

		$this->db->select()->from('users')->limit(1);
		$query = $this->db->get();

		if($query->num_rows() == 1){
			return $query->result();
		}else{
			return FALSE;
		}
	}

	// CHECK USER LOGIN
	public function checkUserLogin($username, $password){

		$condition = array(
					'username' => $username,
					'password' => $password
					);

		$this->db->select()->from('users');
		$this->db->where($condition);
		$query = $this->db->get();

		if($query->num_rows() == 1){
			return $query->result();
		}else{
			return FALSE;
		}
	}

	// UPDATE USER LOGIN DATE
	public function userLoginUpdate($id){

		$dataUpdate = array('LastLoginDate' => date('Y-m-d H:i:s'));
		$condition = array('user_id' => $id);
		$this->db->update('users', $dataUpdate, $condition);
	}

	// GET USER SINGLE DATA
	public function getUserData($id)
	{
		$this->db->select()->from('users')->where(['user_id' => $id])->limit(1);
		$query = $this->db->get();

		if($query->num_rows() == 1){
			return $query->row();
		}else{
			return FALSE;
		}
	}

}

/* End of file userModel.php */
/* Location : ./application/models/userModel.php */
?>
