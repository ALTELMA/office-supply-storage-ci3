<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class MY_Controller extends CI_Controller
{
    // set the class variable.
    var $template = [];
    var $data = [];

    public function __construct()
    {
        parent::__construct();

        // Authentication
        if (!$this->session->has_userdata('logged_in') && (strtolower($this->router->class) != 'auth')) {
            redirect(base_url('login'));
        }

        // Redirect already logged user access to login
        if ($this->session->has_userdata('logged_in') && (strtolower($this->router->class) == 'auth' && strtolower($this->router->method) == 'index')) {
            redirect(base_url('dashboard'));
        }

    }

    // Load layout
    public function layout($type = null) {

        switch ($type) {
            case 'full-width-no-header':
                $this->template['middle'] = $this->load->view($this->content, $this->data, true);
                break;

            default:
                $this->template['header'] = $this->load->view('layout/header', $this->data, true);
                $this->template['left']   = $this->load->view('layout/left', $this->data, true);
                $this->template['middle'] = $this->load->view($this->content, $this->data, true);
                $this->template['footer'] = $this->load->view('layout/footer', $this->data, true);
                break;
        }

        $this->load->view('layout/index', $this->template);
    }
}
