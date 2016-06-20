<?php if (!defined('BASEPATH')) {
    exit('No direct script access allowed');
}

class Dashboard extends MY_Controller
{

    public function __construct()
    {
        parent::__construct();

        // LOAD MODEL
        $this->load->model('userModel', '', true);
        $this->load->model('productModel', '', true);

        // LOAD LIBRARY
        $this->load->library('MyUpload');
        $this->load->library('pagination');
        $this->load->library('MyDateSystem');
        $this->load->library('MyExcel');
    }

    public function index()
    {
        if (!$this->session->userdata('userLogData')) {
            $this->data['title'] = 'ระบบฐานข้อมูลครุภัณฑ์และทรัพย์สินในสำนักงาน';
            $this->content = 'login';
            $this->layout('full-width-no-header');
        } else {
            $this->data['title'] = 'ระบบฐานข้อมูลครุภัณฑ์และทรัพย์สินในสำนักงาน';
            $this->data['product_count'] = $this->productModel->getDataCount('asset') ? : 0;
            $this->data['category_count'] = $this->productModel->getDataCount('category') ? : 0;
            $this->data['subcategory_count'] = $this->productModel->getDataCount('sub_category') ? : 0;
            $this->data['product_value'] = $this->productModel->getDataValue('asset') ? : 0;
            $this->content = 'dashboard';
            $this->layout();
        }
    }
}

/* End of file Dashboard.php */
/* Location : ./application/contollers/Dashboard.php */;
