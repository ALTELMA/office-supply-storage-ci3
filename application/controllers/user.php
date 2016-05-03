<?php if(!defined('BASEPATH'))exit('No direct script access allowed');

class User extends CI_Controller{

	public function __construct() {

		parent::__construct();

		// LOAD MODEL
		$this->load->model('userModel', '', TRUE);
	}

	public function index(){

	}

	// CHECK USER LOGIN
	public function verifylogin() {

		// SETUP VARIABLE
		$username = mysql_real_escape_string($this->input->post('txt_username'));
		$password = mysql_real_escape_string($this->input->post('txt_password'));
		$encodePassword = md5($password);

		$result = $this->userModel->checkUserLogin($username, $encodePassword);

		if($result){
			$sess_array = array();
			foreach($result as $row){
				$sess_array = array(
							'user_id' => $row->user_id,
							'username' => $row->username,
							'name' => $row->name
							);
				$this->userModel->userLoginUpdate($row->user_id);
			}

			// SETUP SESSION
			$this->session->set_userdata('userLogData', $sess_array);
			redirect('asset/page', 'refresh');
		}else{
			redirect('asset/page', 'refresh');
		}
	}

	// USER LOGOUT
	public function logout(){
		$this->session->sess_destroy();
		redirect('asset', 'refresh');
	}

	// ==================================================================
	// PAGE
	// ==================================================================

	public function view($id){

		$data['title'] = 'ข้อมูลผู้ใช้งานระบบ';

		$this->load->view('templates/header',$data);
		$this->load->view('users/user_detail',$data);
		$this->load->view('templates/footer',$data);
	}
}

/* End of file user.php */
/* Location : ./application/contollers/user.php */
?>