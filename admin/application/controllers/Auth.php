<?php
 /*

 * Copyright (c) 2003-2020 BrightOutcome Inc.  All rights reserved.
 * 
 * This software is the confidential and proprietary information of
 * BrightOutcome Inc. ("Confidential Information").  You shall not
 * disclose such Confidential Information and shall use it only
 * in accordance with the terms of the license agreement you
 * entered into with BrightOutcome.
 * 
 * BRIGHTOUTCOME MAKES NO REPRESENTATIONS OR WARRANTIES ABOUT THE
 * SUITABILITY OF THE SOFTWARE, EITHER EXPRESSED OR IMPLIED, INCLUDING BUT 
 * NOT LIMITED TO THE IMPLIED WARRANTIES OF MERCHANTABILITY, FITNESS FOR A 
 * PARTICULAR PURPOSE, OR NON-INFRINGEMENT. BRIGHTOUTCOME SHALL NOT BE LIABLE
 * FOR ANY DAMAGES SUFFERED BY LICENSEE AS A RESULT OF USING, MODIFYING OR
 * DISTRIBUTING THIS SOFTWARE OR ITS DERIVATIVES.
 */
defined('BASEPATH') OR exit('No direct script access allowed');
require APPPATH . '/libraries/PushNotifications.php';

class Auth extends CI_Controller {

    public function __construct() {
        parent::__construct();
        $this->load->model('Auth_model', 'auth');
        
        $this->config->load('auth', TRUE);
        //$this->config->load('wakeful', TRUE);

        /** add error delimiters * */
        $this->form_validation->set_error_delimiters('<label class="error">', '</label>');
	}
	

	/**
     * Login
     * Get username and password if user authenticate it redirect in 
     *  dashboard else it redirect to login page
     * @return Array
     * */
    public function login() {
        if ($this->session->userdata('logged_in') != FALSE) {
            redirect('course');
        }
        if ($this->input->post()) {
            $this->config->load("form_validation");
            $this->form_validation->set_rules($this->config->item("loginForm"));
            if ($this->form_validation->run() != FALSE) {
                $username = $this->input->post('username');
                $password = $this->input->post('password');
                $user_type = 1;
                $result = $this->auth->login($username, $password, $user_type);
                $this->session->set_flashdata($result['status'], $result['msg']);
                if (!empty($result) && $result['status'] == 'success') {
                    // Add user data in session
                    $this->session->set_userdata('logged_in', $result['userdetail']);
                    redirect('/');
                } else {
                    redirect('auth');
                }
            }
        }
        $this->load->view('login');
    }

    /**
     * Logout
     * Delete user session and redirect to login page
     * @return Bool
     * */
    public function logout() {
        $this->session->sess_destroy();
        $this->session->set_flashdata('success', 'User logout successfully');
        redirect('auth');
    }

    /**
     * Forgot Password
     * Take user email and emailed user password reset link in email address
     * @return Bool
     * */
    public function forgot_password() {
        if ($this->input->post()) {
            $this->form_validation->set_rules('email', 'Email', 'required|valid_email|callback_check_email|callback_check_user');
            if ($this->form_validation->run() != FALSE) {
                $result = $this->auth->forgotten_password($this->input->post('email'));
                $this->session->set_flashdata($result['status'], $result['msg']);
                redirect('auth');
            }
        }
        $this->load->view('forgot_password');
    }
    /**
     * Create password functionality for user type 2(Admin)
     */
    public function create_password($code) {
        $this->reset_password($code, true);
    }
    /**
     * Reset password functionality
     */
    public function reset_password($code, $create_pass = false) {
        $this->data['heading'] = 'RESET PASSWORD';
        if($create_pass){
            $this->data['heading'] = "CREATE PASSWORD";
        }
        if ($_SERVER['REQUEST_METHOD'] === 'GET') {
            if ($code != '') {
                $profile = $this->auth->forgotten_code_detail($code);

                if (!empty($profile)) {
                    if ($this->config->item('forgot_password_expiration', 'auth') > 0) {
                        $interval = abs(time() - $profile->forgotten_password_time);
                        $minutes = round($interval / 60);
                        $expiration = $this->config->item('forgot_password_expiration', 'auth');
                        if ($minutes > $expiration * 60) {
                            $this->session->set_flashdata('error', 'Forgot password link expired');
                            redirect('auth');
                        }
                    }
                    $this->data['code'] = $profile->forgotten_password_code;
                    $this->data['user_id'] = $profile->id;
                    $this->load->view('reset_password', $this->data);
                } else {
                    $this->session->set_flashdata('error', 'Forgot passowrd link is invalid');
                    redirect('auth');
                }
            } else {
                $this->session->set_flashdata('error', 'Forgot passowrd link is invalid');
                redirect('auth');
            }
        }

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $this->config->load("form_validation");
            $this->form_validation->set_rules($this->config->item("resetPasswordForm"));
            $code = $this->input->post('forgotten_code');
            $profile = $this->auth->forgotten_code_detail($code);
            $this->data['code'] = $profile->forgotten_password_code;
            $this->data['user_id'] = $profile->id;
            if ($this->form_validation->run() == FALSE) {
                $this->load->view('reset_password', $this->data);
            } else {
                $id = $this->input->post('user_id');
                $password = $this->input->post('password');
                $res = $this->auth->reset_password($id, $password);
                $this->session->set_flashdata($res['status'], $res['msg']);
                redirect('auth');
            }
        }
    }

    /**
     * Reset password functionality
     */
    public function verify_email($code) {
        if ($code != '') {
            $profile = $this->auth->authorization_code_detail($code);
            if (!empty($profile)) {
                if ($this->config->item('authorization_code_expiration', 'auth') > 0) {
                    $interval = abs(time() - $profile->authorization_time);
                    $minutes = round($interval / 60);
                    $expiration = $this->config->item('authorization_code_expiration', 'auth');
                    if ($minutes > $expiration * 60) {
                        $this->session->set_flashdata('error', 'Forgot password link expired');
                    }
                }
                $result = $this->auth->verify_email($profile->id);
                $this->session->set_flashdata($result['status'], $result['msg']);
            } else {
                $this->session->set_flashdata('error', 'Verify email link is invalid');
            }
        } else {
            $this->session->set_flashdata('error', 'Verify email link is invalid');
        }

        redirect($this->config->item('app_url').'/#/home');
	}
	
    public function index() {
        $this->login();
    }

    /**
     * Check Email
     * It is a callback function take user email to check if email exist in system or not
     * @return Bool
     * */
    function check_email($email) {
        if ($email != '') {
            $count = $this->auth->email_check($email);
            if (!$count) {
                $this->form_validation->set_message('check_email', 'Email does not exist in system.');
                return FALSE;
            } else {
                return TRUE;
            }
        }
    }
    /**
     * Check User
     * It is a callback function take user email and check if email is for participant or admin or super admin
     * @return Bool
     * */
    function check_user($email) {
        if ($email != '') {
            $result = $this->auth->user_check($email);
            if (!$result) {
                $this->form_validation->set_message('check_user', 'Email does not exist in system.');
                return FALSE;
            } else {
                return TRUE;
            }
        }
    }

    /**
     * Change Profile functionality
     */
    public function profile() {
        if ($this->session->userdata('logged_in') == FALSE) {
            redirect('auth');
        }
        $data['login_user_detail'] = $this->session->userdata('logged_in');
        $this->breadcrumbs->push('Profile', 'profile');
        $data['breadcrumb'] = $this->breadcrumbs->show();
        if ($this->input->post()) {
            if ($this->input->post('action') == 'basic_information') {
                $this->form_validation->set_rules($this->config->item("basicInfoForm"));
                if ($this->form_validation->run() == FALSE) {
                    $this->template->content->view('profile', $data);
                } else {
                    $user_data['first_name'] = $this->input->post('first_name');
                    $user_data['last_name'] = $this->input->post('last_name');
                    $user_data['gender'] = $this->input->post('gender');
                    $user_data['id'] = $data['login_user_detail']->id;
                    $user_data['profile_picture'] = $data['login_user_detail']->profile_picture;
                    if ($_FILES['image']['name'] != '') {
                        $image_data = isset($_FILES['image']) ? $this->db_model->upload_image($_FILES, $user_data['profile_picture']) : '';
                        $user_data['profile_picture'] = $image_data['file_name'];
                    }

                    $result = $this->auth->update_profile($user_data);
                    $this->session->set_flashdata($result['status'], $result['msg']);
                    if (!empty($result) && $result['status'] == 'success') {
                        $this->session->unset_userdata('logged_in');
                        $session_data = $result['userdetail'];
                        $this->session->set_userdata('logged_in', $session_data);
                        redirect('auth/profile');
                    } else {
                        redirect('auth/profile');
                    }
                }
            }

            if ($this->input->post('action') == 'login_detail') {
                // Check Username 
                if ($data['login_user_detail']->username != $this->input->post('username')) {
                    $this->form_validation->set_rules('username', 'Username', 'required|is_unique[users.username]');
                }

                // Check Email 
                if ($data['login_user_detail']->email != $this->input->post('email')) {
                    $this->form_validation->set_rules('email', 'Email', 'required|is_unique[users.email]|valid_email');
                }

                // Check Password 
                if ($this->input->post('password') != '') {
                    $this->form_validation->set_rules('password', 'Password', 'required|is_valid_password|matches[confirm_password]');
                    $this->form_validation->set_rules('confirm_password', 'Confirm password', 'required');
                }

                $this->form_validation->set_rules('action', 'action', 'required');

                if ($this->form_validation->run() == FALSE) {
                    $this->template->content->view('profile', $data);
                } else {
                    $user_data['email'] = $this->input->post('email');
                    $user_data['username'] = $this->input->post('username');
                    $user_data['password'] = $this->input->post('password');
                    $user_data['id'] = $data['login_user_detail']->id;
                    $result = $this->auth->update_login_detail($user_data);
                    $this->session->set_flashdata($result['status'], $result['msg']);
                    if (!empty($result) && $result['status'] == 'success') {
                        $this->session->unset_userdata('logged_in');
                        $session_data = $result['userdetail'];
                        $this->session->set_userdata('logged_in', $session_data);
                        redirect('auth/profile');
                    } else {
                        redirect('auth/profile');
                    }
                }
            }
        } else {
            $this->template->content->view('profile', $data);
        }
        $this->template->publish();
    }

    

    function send_notification() {
        $fcm = new PushNotifications();
        // Message payload
        $msg_payload = array(
            'mtitle' => 'Test push notification title',
            'mdesc' => 'Test push notification body',
        );
        $DeviceIdsArr[] = 'ebwqJyE8LO8:APA91bH6LUhyBX2jpnX7Nhx1beIr0_wBcDt6adQOj9DCjGeKDfQNKlhQqZ9khOtUjo4oaAqUqlbNb6pVSTgXfSugbSXd6lLcP6O9fPRmyr_i9gctMz1oaUMFH1NJqlZ8zR_XK4froWHo';
        $dataArr = array();
        $dataArr['device_id'] = $DeviceIdsArr; //fcm device ids array which is created previously
        $dataArr['message'] = 'This is test';
        //Message which you want to send
        //Send Notification
        $fcm->sendNotification($dataArr);
    }
    
    
    public function show_email(){
        $data['heading'] = "Password Reset Request";
        $data['subheading'] = "February 20, 2018";
        $data['message'] = "<h2>Hi Jane,</h2> We received a request to reset the password associated with this e-mail address. Please click the link below to start the password reset process.";
        $data['btntitle'] = "Reset password";
        $data['link'] = "#";
        $data['note'] = 'It is a long established fact that a reader will be distracted by the readable content of a page when looking at its layout.<br><a href="#">http://10.10.2.34:4200/#/home</a>';
        $data['footer']="Lorem Ipsum is simply dummy text of the printing and typesetting industry. Lorem Ipsum has been the industry's standard dummy text ever since the 1500s";
        $this->load->view('email_template',$data);
    }
    
    
//        public function encrypt_data() {
//        $this->load->model('user_model');
//        $query = $this->db->get('users');
//        $user_list = $query->result_array();
//        echo "<pre>";
//        foreach ($user_list as $key => $val) {
//            $data = array(
//                'username' => ($val['username'] != '' && $val['username'] != NULL) ? aes_256_encrypt($val['username']) : $val['username'],
//                'first_name' => ($val['first_name'] != '' && $val['first_name'] != NULL) ? aes_256_encrypt($val['first_name']) : NULL,
//                'last_name' => ($val['last_name'] != '' && $val['last_name'] != NULL) ? aes_256_encrypt($val['last_name']) : NULL,
//                'email' => ($val['email'] != '' && $val['email'] != NULL) ? aes_256_encrypt($val['email']) : NULL,
////                'id' => $val['id'],
//            );
////            print_r($data);
//            $this->db->update('users', $data, array('id' => $val['id']));
//        }
//    }
    
}
