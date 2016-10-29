<?php
defined('BASEPATH') or exit('No direct script access allowed');

class Auth extends My_Controller
{
    public function __construct()
    {
        parent::__construct();
    }

    public function index()
    {
        $this->lang->load('form', $this->session->userdata('language'));
        $this->data['title'] = 'ระบบฐานข้อมูลครุภัณฑ์และทรัพย์สินในสำนักงาน :: Login';
        $this->data['text_please_sign_in'] = $this->lang->line('text_please_sign_in');
        $this->data['text_login'] = $this->lang->line('text_login');
        $this->data['text_username'] = $this->lang->line('text_username');
        $this->data['text_password'] = $this->lang->line('text_password');
        $this->content = 'auth/login';
        $this->layout('full-width-no-header');
    }

    public function postLogin()
    {
        $user = $this->user_model->loginByName($this->input->post('username'), $this->input->post('password'));

        if ($user) {
            $data = [
                'user_id' => $user->id,
                'username' => $user->username,
                'email' => $user->email,
                'type' => $user->type,
                'is_active' => $user->is_active,
                'language' => $user->language != '' ? $user->language : $this->config->item('language'),
                'logged_in' => true,
            ];

            $this->session->set_userdata($data);

            redirect('dashboard');
        } else {

            $this->lang->load('validation', $this->config->item('language'));
            $this->session->set_flashdata('error', $this->lang->line('text_invalid_login'));

            redirect('login');
        }
    }

    public function logout()
    {
        $data = ['user_id','username','email','type','is_active','logged_in',];
        $this->session->unset_userdata($data);
        redirect('login');
    }
}
