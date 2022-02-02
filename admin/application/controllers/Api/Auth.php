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

// This can be removed if you use __autoload() in config.php OR use Modular Extensions
/** @noinspection PhpIncludeInspection */
require APPPATH . '/libraries/REST_Controller.php';


// use namespace

/**
 * This is an example of a few basic user interaction methods you could use
 * all done with a hardcoded array
 *
 * @package         CodeIgniter
 * @subpackage      Rest Server
 * @category        Controller
 * @author          Phil Sturgeon, Chris Kacerguis
 * @license         MIT
 * @link            https://github.com/chriskacerguis/codeigniter-restserver
 */
class Auth extends REST_Controller {

    function __construct() {
        header('Access-Control-Allow-Origin: *');
        header("Access-Control-Allow-Methods: GET, POST, OPTIONS, PUT, DELETE");
		header("Access-Control-Allow-Headers: Authorization, Token");
		
		parent::__construct();
		
        $this->load->model('Auth_model', 'auth');
        $this->load->model('User_model', 'users');
        $this->load->model('Course_model', 'course');
    }

    /**
     * Method: POST
     * Header Key: Authorization
     * Value: Auth token generated in GET call
     */
    public function login_post() {

        $login_data = json_decode(file_get_contents('php://input'), true);
        if (!empty($login_data)) {
            $this->config->load("form_validation");
            $this->form_validation->set_rules($this->config->item("loginForm"));
            $this->form_validation->set_data($login_data);
            if ($this->form_validation->run() == FALSE) {
                $data = array(
                    'status' => 'error',
                    'data' => $this->form_validation->error_array()
                );
                $this->response($data, REST_Controller::HTTP_BAD_REQUEST);
            } else {
                $username = trim($login_data['username']);
                $password = decodeurl($login_data['password']);
				$result = $this->auth->login($username, $password, 3, true);
					if(isset($result['user_id'])){
                        $course_id = $this->auth->user_has_course($result['user_id']);
						$study_id = user_has_study($result['user_id']);
                        $users_detail = $this->users->get_detail($result['user_id'],true);
						if($course_id){
                            $result['course_id'] = $course_id;
                        }
                        if($study_id){
							$result['study_id'] = $study_id;
						}
                        if(!empty($users_detail)){
                            $result['profile_picture'] = $users_detail['profile_picture'];
                            $result['username'] = $users_detail['username'];
                        }
					}
                $this->set_response($result);
            }
        } else {
            $data = array(
                'status' => 'error',
            );
            $this->response($data, REST_Controller::HTTP_METHOD_NOT_ALLOWED);
        }
    }

    /**
     * Checking user is logged in and token is valid or not
     */
    public function check_login_post() {
        $this->check_token(true);
    }

    /**  Create User */
    public function signup_post() {
        $register_data = json_decode(file_get_contents('php://input'), true);
        
        $register_data['confirm_password'] = decodeurl($register_data['confirm_password']);
        $register_data['password'] = decodeurl($register_data['password']);

        if (!empty($register_data)) {
            $this->config->load("form_validation");
            $this->form_validation->set_data($register_data);
            $this->form_validation->set_rules($this->config->item("registerForm"));
            if ($this->form_validation->run() == FALSE) {
                $data = array(
                    'status' => 'error',
                    'data' => $this->form_validation->error_array()
                );
                $this->response($data, REST_Controller::HTTP_BAD_REQUEST);
            } else {
				if(!$this->check_access_token($register_data['token'])){
					$data = array(
						'status' => 'error',
						'msg' => 'access token is not correct',
					);
					$this->response($data, REST_Controller::HTTP_OK);
				}else{
					$res = $this->auth->signup($register_data,3);
                	$this->response($res, REST_Controller::HTTP_CREATED);
				}               
            }
        } else {
            $data = array(
                'status' => 'error',
            );
            $this->response($data, REST_Controller::HTTP_METHOD_NOT_ALLOWED);
        }
    }

    /**  Create User When Login and signup using facebook and google */
    public function social_login_post() {
        $register_data = json_decode(file_get_contents('php://input'), true);
        if (!empty($register_data)) {
            $course = isset($register_data['course']) ? trim($register_data['course']) : '';
            $res = $this->auth->social_login($register_data);
            $res['course_id'] = '';
                if($course){
                    $course_id = $this->course->get_courseid(array('where' => array('courses.slug' => $course)));
                    $res['course_id'] = $course_id['result'][0]['id'];
                }
            $this->response($res, REST_Controller::HTTP_CREATED);
        } else {
            $data = array(
                'status' => 'error',
            );
            $this->response($data, REST_Controller::HTTP_METHOD_NOT_ALLOWED);
        }
    }

    /** Forgot Password */
    public function forgot_password_post() {
        $insert_data = json_decode(file_get_contents('php://input'), true);
        if (!empty($insert_data)) {
            $this->form_validation->set_data($insert_data);
            $this->form_validation->set_rules('email', 'Email', 'required|valid_email|callback_check_email|callback_check_user');
            if ($this->form_validation->run() == FALSE) {
                $data = array(
                    'status' => 'error',
                    'data' => $this->form_validation->error_array()
                );
                $this->response($data, REST_Controller::HTTP_OK);
            } else {
                $res = $this->auth->forgotten_password($insert_data['email'], TRUE);
                $this->response($res, REST_Controller::HTTP_OK);
            }
        }
    }

    /**
     * Method: Get
     * Header Key: Authorization
     */
    public function profile_get() {
        $users_id = $this->get_user();
        $data = array();
        $data = $this->users->get_detail($users_id,true);
        $this->response(array('status' => 'success', 'data' => $data), REST_Controller::HTTP_OK);
    }

    function check_email($email) {
        if ($email != '') {
            $count = $this->auth->email_check($email);
            if (!$count) {
                $this->form_validation->set_message('check_email', 'Email address does not exist in system.');
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
            if ($result) {
                $this->form_validation->set_message('check_user', 'Email does not exist in system.');
                return FALSE;
            } else {
                return TRUE;
            }
        }
    }

    public function check_email_post() {
        $insert_data = json_decode(file_get_contents('php://input'), true);
        $current_email = (isset($insert_data['current_email'])) ? $insert_data['current_email'] : FALSE;
        $previous_email = (isset($insert_data['previous_email'])) ? $insert_data['previous_email'] : FALSE;
        $result = false;
        if ($current_email != $previous_email) {
            $result = $this->auth->email_check($current_email);
        }

        if ($result) {
            $this->response(array('status' => 'success'), REST_Controller::HTTP_OK);
        } else {
            $this->response(array('status' => 'error'), REST_Controller::HTTP_OK);
        }
    }

    /** Callback function to check username exist or not in system */
    public function check_username_post() {
        $insert_data = json_decode(file_get_contents('php://input'), true);
        $current_username = (isset($insert_data['current_username'])) ? trim($insert_data['current_username']) : FALSE;
        $previous_username = (isset($insert_data['previous_username'])) ? trim($insert_data['previous_username']) : FALSE;
        $result = false;
        if ($current_username != $previous_username) {
            $result = $this->auth->username_check($current_username);
        }
        if ($result) {
            $this->response(array('status' => 'success'), REST_Controller::HTTP_OK);
        } else {
            $this->response(array('status' => 'error'), REST_Controller::HTTP_OK);
        }
    }

    /** User logout */
    public function logout_get() {
        $token = $this->input->server('HTTP_TOKEN');
        if ($token) {
            if ($this->auth->update_token($token)) {
                $this->response(array('status' => 'success', 'msg' => "Logged out successfully"), REST_Controller::HTTP_OK);
            } else {
                $this->response(array('status' => 'error'), REST_Controller::HTTP_OK);
            }
        }
    }

    public function profile_post() {
        $this->check_token();
        $insert_data = json_decode(file_get_contents('php://input'), true);
        if (!empty($insert_data)) {
            $this->form_validation->set_data($insert_data);
            //$this->form_validation->set_rules('first_name', 'first_name', 'required');
            $insert_data['password'] = decodeurl($insert_data['password']) == "null" ? '' : decodeurl($insert_data['password']);
            $insert_data['confirm_password'] = decodeurl($insert_data['confirm_password']);
            $insert_data['current_password'] = decodeurl($insert_data['current_password']);

            // $this->form_validation->set_rules('first_name', 'first_name', 'required');
			// $this->form_validation->set_rules('last_name', 'last name', 'required');
			

			$this->form_validation->set_rules('username', 'Username', 
						'required'.(trim($insert_data['previous_username']) != trim($insert_data['username'])?'|is_unique[users.username]':''));
            
			// Check Email 
            $this->form_validation->set_rules('email', 'Email', 'required|valid_email'.(trim($insert_data['previous_email']) != trim($insert_data['email'])?'|is_unique[users.email]':''));
            // if (trim($insert_data['previous_email']) != trim($insert_data['email'])) {
            //     $this->form_validation->set_rules('email', 'Email', 'required|is_unique[users.email]|valid_email');
            // }
            // Check Password 
            if ($insert_data['password'] != '') {
                $users_id = $this->get_user();
                $result = $this->auth->hash_password_db($users_id, $insert_data['current_password']);

                if (!$result) {
                    $this->response(array('status' => 'error', 'msg' => 'Password is invalid'), REST_Controller::HTTP_OK);
                }

                $this->form_validation->set_rules('current_password', 'Current Password', 'required');
                $this->form_validation->set_rules('password', 'Password', 'matches[confirm_password]');
                $this->form_validation->set_rules('confirm_password', 'Confirm password', 'required');
            }
            if ($this->form_validation->run() == FALSE) {
                $data = array(
                    'status' => 'error',
                    'data' => $this->form_validation->error_array()
                );
                $this->response($data, REST_Controller::HTTP_OK);
            } else {
				$user_data=array();
				if ($insert_data['profile_picture'] != '' && !empty($insert_data['profile_picture'])) {
				   $user_data['profile_picture'] = $insert_data['profile_picture'];
				} else {
                    $user_data['profile_picture'] = '';
                }
                $users_id = $this->get_user();
                // $user_data['first_name'] = $insert_data['first_name'];
				// $user_data['last_name'] = $insert_data['last_name'];
				//$user_data['unique_id'] = $insert_data['unique_id'];

                $user_data['id'] = $users_id;
                $this->auth->update_profile($user_data);
                $update_login_detail['email'] = $insert_data['email'];
                $update_login_detail['username'] = $insert_data['username'];
                $update_login_detail['password'] = $insert_data['password'];
                $update_login_detail['id'] = $users_id;
                $result = $this->auth->update_login_detail($update_login_detail);
                $this->response(array('status' => $result['status'], 'msg' => $result['msg']), REST_Controller::HTTP_OK);
            }
        } else {
            $this->response(array('status' => 'error'), REST_Controller::HTTP_OK);
        }
    }

    /** Callback function to check username exist or not in system */
    public function check_password_post() {
        $insert_data = json_decode(file_get_contents('php://input'), true);
        if (!empty($insert_data)) {
            $users_id = $this->get_user();
            $result = $this->auth->hash_password_db($users_id, decodeurl($insert_data['password']));
            if (!$result) {
                $this->response(array('status' => 'success'), REST_Controller::HTTP_OK);
            } else {
                $this->response(array('status' => 'error'), REST_Controller::HTTP_OK);
            }
        }
    }

    public function base64_to_image($profile_image = array()) {
        if (!empty($profile_image)) {
            $config = $this->config->item('assets_images');
            $upload_path = check_directory_exists($config['path']);
            $path = $profile_image['filename'];
            $file_name = pathinfo($path, PATHINFO_FILENAME) . '-' . uniqid() . '.png';
            $img = $profile_image['value'];
            $data = base64_decode($img);
            $file_path = $upload_path . '/' . $file_name;
            $success = file_put_contents($file_path, $data);
            return $success ? $file_name : FALSE;
        }
    }

    public function reset_password_post() {
        $res['status'] = 'error';
        $res['msg'] = 'Passowrd link is invalid';
        $input = json_decode(file_get_contents('php://input'), true);
        if (!empty($input)) {
            $code = $input['code'];
            if ($code != '') {
                $profile = $this->auth->forgotten_code_detail($code);
                $input['password'] = decodeurl($input['password']);
                $input['confirm_password'] = decodeurl($input['confirm_password']);
                if (!empty($profile)) {
                    $this->form_validation->set_data($input);
                    $this->form_validation->set_rules($this->config->item("resetPassUser"));
                    if ($this->form_validation->run() == FALSE) {
                        $data = array(
                            'status' => 'error',
                            'data' => $this->form_validation->error_array()
                        );
                        $this->response($data, REST_Controller::HTTP_BAD_REQUEST);
                    } else {
                        $id = $profile->id;
                        $password = $input['password'];
                        $res = $this->auth->reset_password($id, $password);
                    }
                }
            } else {
                $res['status'] = "error";
                $res['msg'] = 'Passowrd link is invalid';
            }
        }
        $this->response($res, REST_Controller::HTTP_OK);
    }

    public function reset_password_code_post() {
        $res['status'] = 'error';
        $res['msg'] = 'Unable to make a request please try again later';
        $input = json_decode(file_get_contents('php://input'), true);
        if (!empty($input)) {
            $code = $input['code'];
            if ($code != '') {
                $profile = $this->auth->forgotten_code_detail($code);
                if (!empty($profile)) {
                    if ($this->config->item('forgot_password_expiration', 'auth') > 0) {
                        $interval = abs(time() - $profile->forgotten_password_time);
                        $minutes = round($interval / 60);
                        $expiration = $this->config->item('forgot_password_expiration', 'auth');
                        if ($minutes > $expiration * 60) {
                            $res['status'] = "error";
                            $res['msg'] = 'Forgot passowrd link is invalid';
                        } else {
                            $res['status'] = "success";
                            $res['msg'] = 'Forgot passowrd link is invalid';
                        }
                    }
                } else {
                    $res['status'] = "error";
                    $res['msg'] = 'Forgot passowrd link is invalid';
                }
            }
        }
        $this->response($res, REST_Controller::HTTP_OK);
    }

    public function background_images_get() {
        $res['status'] = 'success';
        $res['msg'] = 'Background images are not found';
        $data['desktop'] = $this->config->item('desktop');
        $data['tablet'] = $this->config->item('tablet');
        $data['mobile'] = $this->config->item('mobile');
        if (!empty($data['desktop'])) {
            foreach ($data as $device => $images) {
                foreach ($images as $type => $image) {
                    $data[$device][$type] = base_url() . 'assets/images/app-background-images/' . $image;
                }
            }
            $res = $this->response(array('status' => 'success', 'data' => $data), REST_Controller::HTTP_OK);
        }
        $this->response($res, REST_Controller::HTTP_OK);
    }

    /** Callback function to check username exist or not in system */
    public function check_previous_password_post() {
        $insert_data = json_decode(file_get_contents('php://input'), true);
        if (!empty($insert_data)) {
            $users_id = $this->get_user();
            $result = $this->auth->check_password_history($users_id, decodeurl($insert_data['password']));
            if ($result) {
                $this->response(array('status' => 'success'), REST_Controller::HTTP_OK);
            } else {
                $this->response(array('status' => 'error'), REST_Controller::HTTP_OK);
            }
        }
    }

    /** function to send contact information */
    public function contact_us_post() {
        $res['status'] = 'error';
        $res['msg'] = 'Unable to send a request please try again later';
        $input = json_decode(file_get_contents('php://input'), true);
        if (!empty($input)) {
            $this->form_validation->set_data($input);
            $this->form_validation->set_rules($this->config->item("contactUsForm"));
            if ($this->form_validation->run() == FALSE) {
                $data = array(
                    'status' => 'error',
                    'data' => $this->form_validation->error_array()
                );
                $this->response($data, REST_Controller::HTTP_BAD_REQUEST);
            } else {
                $res = $this->auth->send_contact_us_email($input);
            }
        }
        $this->response($res, REST_Controller::HTTP_OK);
	}
	
	public function check_access_token($token){
		return isset($token) ? $this->auth->check_access_token($token) : FALSE;
	}

	/**
     * fetch user data using authorization_code
     */
    public function user_data_post() {
		$login_data = json_decode(file_get_contents('php://input'), true);
		$res['status'] = 'error';
        $res['msg'] = 'Access code not found';
        if (!empty($login_data)) {
			$profile = $this->auth->authorization_code_detail($login_data['code']);
			if (!empty($profile)) {
				$user_detail = $this->users->get_encrypted_user_detail(array('id'), $profile->id);
				$res = array(
                    'status' => 'success',
                    'result' => $user_detail,
                    'msg' => 'user detail found successfully',
                );
            } else {
                $res['status'] = 'error';
        		$res['msg'] = 'No user found with this access code';
            }
        } 
		$this->response($res, REST_Controller::HTTP_OK);
    }

    public function check_notification_count_get() {
        $user_id = $this->get_user();
        $this->load->model('Community_model', 'community');
        $notification_count = $this->community->get_notification_count($user_id)['unread_count'];
        $data = array(
            'status' => 'success',
            'msg' => 'Notification Count',
            'notification_count' => $notification_count
        );
        $this->response($data, REST_Controller::HTTP_OK);
    }
    public function clear_notification_count_get() {
        $user_id = $this->get_user();
        $this->load->model('Community_model', 'community');
        $notification = $this->community->clear_notification_count($user_id);
        $data = array(
            'status' => $notification['status'],
            'msg' => $notification['msg'],
        );
        $this->response($data, REST_Controller::HTTP_OK);
    }

    public function accessed_resources_post() {
        $input = json_decode(file_get_contents('php://input'), true);
        $users_id = $this->get_user();
        $data = array();
        $data = $this->users->get_detail($users_id,true);
        if (!empty($data)) {
            $input['users_id'] = $this->get_user();
            $res = $this->user->accessed_tabs($input);
            $this->response($res, REST_Controller::HTTP_CREATED);
        } else {
            $data = array(
                'status' => 'error',
            );
            $this->response($data, REST_Controller::HTTP_METHOD_NOT_ALLOWED);
        }
    }

}
