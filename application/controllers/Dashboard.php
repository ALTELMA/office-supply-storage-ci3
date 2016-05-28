<?php if(!defined('BASEPATH'))exit('No direct script access allowed');

class Dashboard extends MY_Controller{

	public function __construct()
	{
		parent::__construct();
	}

	public function index()
	{
		if(!$this->session->userdata('userLogData')) {
			$this->data['title'] = 'ระบบฐานข้อมูลครุภัณฑ์และทรัพย์สินในสำนักงาน';
			$this->content = 'login';
			$this->layout('full-width-no-header');
		} else {
			$this->data['title'] = 'ระบบฐานข้อมูลครุภัณฑ์และทรัพย์สินในสำนักงาน';
			$this->content = 'dashboard';
			$this->layout();
		}
	}
}

/* End of file Dashboard.php */
/* Location : ./application/contollers/Dashboard.php */
?>
