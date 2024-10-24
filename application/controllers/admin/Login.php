<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Login extends CI_Controller {

    public function __construct() {
        parent::__construct();
        $this->load->model('admin/admin_model'); // โหลด Admin Model
        $this->load->library('auth'); // โหลด Auth Library
    }

    public function index() {
        $this->auth->checkLoginAdmin(); // ตรวจสอบการเข้าสู่ระบบโดยใช้ Auth library

        $data = array(
            'title' => 'เข้าสู่ระบบ',
        );
        $this->load->view('admin/login_view', $data);
    }

    public function doLogin() {
        $username = $this->input->post('username');
        $password = $this->input->post('password');

        // ใช้ Model เพื่อดึงข้อมูลแอดมิน
        $admin = $this->admin_model->getAdminByUsernamePassword($username, $password);

        if ($admin) {
            $sessiondata = array(
                'admin_id' => $admin->admin_id,
                'regenerate_login' => rand(100000, 999999)
            );
            $this->session->set_userdata($sessiondata);

            // ใช้ Auth library เพื่อจัดการการตรวจสอบการเข้าสู่ระบบ
            $this->auth->setLoginCheckAdmin($admin->admin_id, $this->session->userdata('regenerate_login'));

            redirect(base_url() . 'admin/home');
        } else {
            $this->session->set_flashdata('flash_message', '<div class="col-lg-12" style="padding: 7px; font-size: 14px; border: 2px solid #f77474;"><i class="fa fa-times-circle" style="color: #D33E3E;"></i>&nbsp;Username or Password ไม่ถูกต้อง</div>');
            redirect(base_url('admin'));
        }
    }

    public function logout() {
        $this->auth->deleteLoginCheckAdmin($this->session->userdata('admin_id'));
        $this->session->sess_destroy();
        redirect(base_url() . 'admin');
    }
}
