<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Login extends CI_Controller {

    private $login_attempt_limit = 5; // Number of allowed login attempts
    private $lockout_time = 60; // Lockout time in seconds

    public function __construct() {
        parent::__construct();
        $this->load->helper('url');
        $this->load->model('Login_model'); // Load the model
        $this->load->library('session');
    }

    public function index() {
        $this->load->view('login_view');
    }

    public function auth() {
        $username = $this->input->post('username');
        $password = $this->input->post('password');

        if (empty($username) || empty($password)) {
            $this->session->set_flashdata('error', 'Username and password are required.');
            redirect('login');
            return; // Important: Exit the function
        }

        // Check login attempts
        if ($this->is_locked_out($username)) {
            $this->session->set_flashdata('error', 'Too many failed login attempts. Please try again later.');
            redirect('login');
            return;
        }

        try {
            $user = $this->Login_model->get_user_by_username($username);

            if ($user) {
                // Verify password (using password_verify for security)
                if (password_verify($password, $user->password)) {
                    // Reset login attempts on successful login
                    $this->reset_login_attempts($username);

                    $session_data = array(
                        'id'       => $user->id,
                        'username' => $user->username,
                        'name'     => $user->name,
                        'level'    => $user->level,
                        'logged_in' => TRUE
                    );

                    $this->session->set_userdata($session_data);
                    redirect('home');
                } else {
                    // Increment login attempts on failed login
                    $this->increment_login_attempts($username);
                    $this->session->set_flashdata('error', 'Incorrect password.');
                    redirect('login');
                }
            } else {
                // Increment login attempts on failed login
                $this->increment_login_attempts($username);
                $this->session->set_flashdata('error', 'Username not found.');
                redirect('login');
            }
        } catch (Exception $e) {
            log_message('error', 'Authentication error: ' . $e->getMessage());
            $this->session->set_flashdata('error', 'Authentication failed. Please check the logs.');
            redirect('login');
        }
    }

    public function logout() {
        $this->session->sess_destroy();
        redirect('login');
    }

    // ------------------------------------------------------------------------
    // Login Attempt Limiting Functions
    // ------------------------------------------------------------------------

    private function increment_login_attempts(string $username): void {
        $attempts = $this->session->userdata('login_attempts') ?? [];
        if (isset($attempts[$username])) {
            $attempts[$username]['count']++;
            $attempts[$username]['last_attempt'] = time();
        } else {
            $attempts[$username] = ['count' => 1, 'last_attempt' => time()];
        }
        $this->session->set_userdata('login_attempts', $attempts);
    }

    private function reset_login_attempts(string $username): void {
        $attempts = $this->session->userdata('login_attempts') ?? [];
        if (isset($attempts[$username])) {
            unset($attempts[$username]);
            $this->session->set_userdata('login_attempts', $attempts);
        }
    }

    private function is_locked_out(string $username): bool {
        $attempts = $this->session->userdata('login_attempts') ?? [];
        if (isset($attempts[$username]) && $attempts[$username]['count'] >= $this->login_attempt_limit) {
            // Check if lockout time has expired
            $time_since_last_attempt = time() - $attempts[$username]['last_attempt'];
            if ($time_since_last_attempt < $this->lockout_time) {
                return true; // Locked out
            } else {
                // Lockout time expired, reset attempts
                $this->reset_login_attempts($username);
                return false; // Not locked out
            }
        }
        return false; // Not locked out
    }
}

/*
class Login extends CI_Controller {

    public function __construct() {
        parent::__construct();
    }

    public function index() {
//        if ($this->session->userdata('shop_id_pri') != NULL && $this->accesscontrol->checkLogin($this->session->userdata('user_id'), $this->session->userdata('regenerate_login')) == 1) {
//            redirect(base_url());
//        } else if ($this->session->userdata('shop_id_pri') != NULL && $this->accesscontrol->checkLogincustomer($this->session->userdata('user_id'), $this->session->userdata('regenerate_login')) == 1) {
//            redirect(base_url());
//            //echo $this->session->userdata('role_id');
//        }
//        $data = array(
//            'title' => 'เข้าสู่ระบบ',
//        );
//        $this->load->view('login_view', $data);
        if ($this->session->userdata('shop_id_pri') != NULL && $this->accesscontrol->checkLogin($this->session->userdata('user_id'), $this->session->userdata('regenerate_login')) == 1) {
            $package = $this->accesscontrol->getPackage($this->session->userdata('package_id'))->row();
            $package_shop = $this->accesscontrol->getUserPackage($this->session->userdata('user_id'))->row();
            $stop_usedate = date('Y-m-d', strtotime($package_shop->user_package_modify . "+ $package->package_usedate day"));
            if (date('Y-m-d') > $stop_usedate) { //หมดอายุ
                $data = array(
                    'title' => 'เข้าสู่ระบบ',
                );
                $this->load->view('login_view', $data);
            }
            else if ($this->accesscontrol->checkShop($package_shop->package_shop_id_pri)->status_shop_id != 1) {
                $data = array(
                    'title' => 'เข้าสู่ระบบ',
                );
                $this->load->view('login_view', $data);
            }
            else {
                redirect(base_url());
            }
        } else if ($this->session->userdata('shop_id_pri') != NULL && $this->accesscontrol->checkLogincustomer($this->session->userdata('user_id'), $this->session->userdata('regenerate_login')) == 1) {
            redirect(base_url());
//            //echo $this->session->userdata('role_id');
        } else {
            $data = array(
                'title' => 'เข้าสู่ระบบ',
            );
            $this->load->view('login_view', $data);
        }
    }

//    public function customer() {
//        if ($this->session->userdata('shop_id_pri') != NULL) {
//            redirect(base_url());
//        }
//        $data = array(
//            'title' => 'เข้าสู่ระบบสำหรับลูกค้า',
//        );
//        $this->load->view('login_customer_view', $data);
//    }

    public function doLogin() {
        $username = $this->input->post('username');
        $password = md5($username . $this->input->post('password'));

        $users = $this->db->select('*')->where('username', $username)->where('password', $password)->limit(1)->get('user');

        if ($users->num_rows() > 0) {
            $user = $users->row();
            if ($user->status_id == 1) {
                $shop = $this->accesscontrol->checkShop($user->shop_id_pri);
                if ($shop->status_shop_id == 1) {
                    $package_id = $this->accesscontrol->getUserPackage($user->user_id)->row()->package_id;

                    $package = $this->accesscontrol->getPackage($package_id)->row();
                    $package_shop = $this->accesscontrol->getUserPackage($user->user_id)->row();
                    $stop_usedate = date('Y-m-d', strtotime($package_shop->user_package_modify . "+ $package->package_usedate day"));
                    if (date('Y-m-d') > $stop_usedate) {
                        $this->session->set_flashdata('flash_message', '<div class="col-lg-12" style="padding: 7px; font-size: 14px; border: 2px solid orange;color: red;"><i class="fa fa-ban" style="color: #D33E3E;"></i>&nbsp;ร้านของท่านถูกระงับใช้งาน เนื่องจากแพ็กเกจหมดอายุ โปรดติดต่อ Admin</div>');
                        redirect(base_url('login'));
                    }
                    else if ($this->accesscontrol->checkShop($package_shop->package_shop_id_pri)->status_shop_id != 1) {
                        $this->session->set_flashdata('flash_message', '<div class="col-lg-12" style="padding: 7px; font-size: 14px; border: 2px solid orange;color: red;"><i class="fa fa-ban" style="color: #D33E3E;"></i>&nbsp;ร้านของท่านถูกระงับใช้งาน โปรดติดต่อ Admin</div>');
                        redirect(base_url('login'));
                    }
                    $sessiondata = array(
                        'shop_id_pri' => $user->shop_id_pri,
                        'package_id' => $package_id,
                        'user_id' => $user->user_id,
                        'role_id' => $user->role_id,
                        'regenerate_login' => rand(100000, 999999)
                    );
                    $this->session->set_userdata($sessiondata);
                    $this->systemlog->addUserLogin($user->user_id, 'Login');
                    if ($this->systemlog->checkAddLogin($user->user_id) == 1) {
                        $this->systemlog->updateLoginCheck($user->user_id, $this->session->userdata('regenerate_login'));
                    } else {
                        $this->systemlog->addLoginCheck($user->user_id, $this->session->userdata('regenerate_login'));
                    }
                    redirect(base_url());
                } else {
                    $this->session->set_flashdata('flash_message', '<div class="col-lg-12" style="padding: 7px; font-size: 14px; border: 2px solid orange;"><i class="fa fa-ban" style="color: #D33E3E;"></i>&nbsp;ร้านของท่านถูกระงับใช้งาน</div>');
                    redirect(base_url('login'));
                }
                    
            } else {
                $this->session->set_flashdata('flash_message', '<div class="col-lg-12" style="padding: 7px; font-size: 14px; border: 2px solid orange;"><i class="fa fa-ban" style="color: #D33E3E;"></i>&nbsp;Username ของท่านถูกระงับการใช้งาน</div>');
                redirect(base_url('login'));
            }
        } else {
            $users = $this->db->select('*')->where('username', $username)->where('password', $password)->limit(1)->get('customer');
            if ($users->num_rows() > 0) {
                $user = $users->row();
                if ($user->status_id == 1) {
                    $sessiondata = array(
                        'shop_id_pri' => $this->db->select('shop_id_pri')->where('customer_group_id', $user->customer_group_id)->limit(1)->get('customer_group')->row()->shop_id_pri,
                        'user_id' => $user->customer_id_pri,
                        'role_id' => $user->role_id,
                        'regenerate_login' => rand(100000, 999999)
                    );
                    $this->session->set_userdata($sessiondata);
                    if ($this->systemlog->checkAddLogincustomer($user->customer_id_pri) == 1) {
                        $this->systemlog->updateLoginCheckcustomer($user->customer_id_pri, $this->session->userdata('regenerate_login'));
                    } else {
                        $this->systemlog->addLoginCheckcustomer($user->customer_id_pri, $this->session->userdata('regenerate_login'));
                    }
                    redirect(base_url());
                } else {
                    $this->session->set_flashdata('flash_message', '<div class="col-lg-12" style="padding: 7px; font-size: 14px; border: 2px solid orange;"><i class="fa fa-ban" style="color: #D33E3E;"></i>&nbsp;Username ของท่านถูกระงับการใช้งาน</div>');
                    redirect(base_url('login'));
                }
            } else {
                $this->session->set_flashdata('flash_message', '<div class="col-lg-12" style="padding: 7px; font-size: 14px; border: 2px solid #f77474;"><i class="fa fa-times-circle" style="color: #D33E3E;"></i>&nbsp;Username or Password ไม่ถูกต้อง</div>');
                redirect(base_url('login'));
            }
        }
    }

//    public function dologin_customer() {
//        $username = $this->input->post('username');
//        $password = md5($username . $this->input->post('password'));
//
//        $users = $this->db->select('*')->where('username', $username)->where('password', $password)->limit(1)->get('customer');
//
//        if ($users->num_rows() > 0) {
//            $user = $users->row();
//            if ($user->status_id == 1) {
//                $sessiondata = array(
//                    'shop_id_pri' => $user->shop_id_pri,
//                    'user_id' => $user->customer_id_pri,
//                    'role_id' => $user->role_id
//                );
//                $this->session->set_userdata($sessiondata);
//                redirect(base_url());
//            } else {
//                $this->session->set_flashdata('flash_message', '<div class="col-lg-12" style="padding: 7px; font-size: 14px; border: 2px solid orange;"><i class="fa fa-ban" style="color: #D33E3E;"></i>&nbsp;Username ของท่านถูกระงับการใช้งาน</div>');
//                redirect(base_url('login'));
//            }
//        } else {
//            $this->session->set_flashdata('flash_message', '<div class="col-lg-12" style="padding: 7px; font-size: 14px; border: 2px solid #f77474;"><i class="fa fa-times-circle" style="color: #D33E3E;"></i>&nbsp;Username or Password ไม่ถูกต้อง</div>');
//            redirect(base_url('login'));
//        }
//    }

    public function logout() {
        $this->session->sess_destroy();
        redirect(base_url() . 'login');
    }

}
