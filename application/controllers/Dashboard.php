<?php if (!defined('BASEPATH')) {
    exit('No direct script access allowed');
}

class Dashboard extends MY_Controller
{

    public function __construct()
    {
        parent::__construct();

        // LOAD MODEL
        $this->load->model('product_model', '', true);
    }

    public function index()
    {
        $this->data['title'] = 'ระบบฐานข้อมูลครุภัณฑ์และทรัพย์สินในสำนักงาน';
        $this->data['product_count'] = $this->product_model->getDataCount('asset') ? : 0;
        $this->data['category_count'] = $this->product_model->getDataCount('category') ? : 0;
        $this->data['subcategory_count'] = $this->product_model->getDataCount('sub_category') ? : 0;
        $this->data['product_value'] = $this->product_model->getDataValue('asset') ? : 0;
        $this->content = 'dashboard';
        $this->layout();
    }
}

/* End of file Dashboard.php */
/* Location : ./application/contollers/Dashboard.php */;
