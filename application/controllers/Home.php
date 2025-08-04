<?php

<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Home extends CI_Controller {

    public function __construct() {
        parent::__construct();
        $this->load->helper('url');
        $this->load->model('Home_model');  // Load the model
        $this->load->library('session');
    }

    public function index() {
        $data['title'] = 'POS System';
        try {
            $data['total_products'] = $this->Home_model->count_total_products();
            $data['total_orders'] = $this->Home_model->count_total_orders();
            $data['total_customers'] = $this->Home_model->count_total_customers();
            $data['total_sales'] = $this->Home_model->calculate_total_sales(); // Assuming this function exists
        } catch (Exception $e) {
            log_message('error', 'Error fetching data: ' . $e->getMessage());
            $data['error_message'] = 'Failed to load data. Please check the logs.'; // User-friendly message
            $data['total_products'] = 0;
            $data['total_orders'] = 0;
            $data['total_customers'] = 0;
            $data['total_sales'] = 0;
        }
        $this->load->view('home_view', $data); // Load the view
    }

    public function login() {
        $this->load->view('login_view');
    }

    public function auth() {
        $username = $this->input->post('username');
        $password = $this->input->post('password');

        if (empty($username) || empty($password)) {
            $this->session->set_flashdata('error', 'Username and password are required.');
            redirect('home/login');
            return; // Important: Exit the function
        }

        try {
            $user = $this->Home_model->get_user_by_username($username);

            if ($user) {
                // Verify password (using password_verify for security)
                if (password_verify($password, $user->password)) {
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
                    $this->session->set_flashdata('error', 'Incorrect password.');
                    redirect('home/login');
                }
            } else {
                $this->session->set_flashdata('error', 'Username not found.');
                redirect('home/login');
            }
        } catch (Exception $e) {
            log_message('error', 'Authentication error: ' . $e->getMessage());
            $this->session->set_flashdata('error', 'Authentication failed. Please check the logs.');
            redirect('home/login');
        }
    }

    public function logout() {
        $this->session->sess_destroy();
        redirect('home/login');
    }
}




/*-----------


class Home extends CI_Controller {

    public $group_id = '';
    public $menu_id = '';

    public function __construct() {
        parent::__construct();
        $this->auth->isLoginNull();
        $this->load->model('home_model');
    }

    public function index() {
        if ($this->session->userdata('role_id') != 8) {
            $data = array(
                'group_id' => $this->group_id,
                'menu_id' => $this->menu_id,
                'title' => 'ภาพรวม',
                'css' => array(),
                'js' => array('build/charts/highcharts.js', 'build/charts.js')
            );
            $this->renderView('home_view', $data);
        }else{
            redirect(base_url().'transportcustomer');
        }
    }

    public function charts() {
        $data = array();

        for ($i = 1; $i <= 12; $i++) {
            $price_pay = $this->home_model->charts_year_month((date("Y") - 1), $i)->row()->price_sum_pay;
            if ($price_pay == null) {
                $price_pay = 0;
            }
            $data['oldyear'][] = round($price_pay, 2);
        }
        for ($i = 1; $i <= 12; $i++) {
            $price_pay = $this->home_model->charts_year_month((date("Y")), $i)->row()->price_sum_pay;
            if ($price_pay == null) {
                $price_pay = 0;
            }
            $data['newyear'][] = round($price_pay, 2);
        }

        echo json_encode($data);
    }

}
*/