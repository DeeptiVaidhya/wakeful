<?php
 /**
 * Copyright (c) 2003-2019 BrightOutcome Inc.  All rights reserved.
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
if (!defined('BASEPATH'))
    exit('No direct script access allowed');

/**
 * Name:    Auth Model
 *
 * Requirements: PHP5 or above
 *
 */
class Auth_model extends CI_Model {

    var $expire_time;

    public function __construct() {
        parent::__construct();
        $this->expire_time = $this->config->item('lockout_time');
        $this->config->load('auth', TRUE);
        $this->load->helper('date');
        $this->tables = array('users' => 'users', 'users_secure' => 'users_secure');
        $this->load->model('user_model', 'user');
       
        // initialize db tables data
        //initialize data
        $this->store_salt = $this->config->item('store_salt', 'auth');
        $this->salt_length = $this->config->item('salt_length', 'auth');
        // initialize hash method options (Bcrypt)
        $this->hash_method = $this->config->item('hash_method', 'auth');
        $this->default_rounds = $this->config->item('default_rounds', 'auth');
        $this->random_rounds = $this->config->item('random_rounds', 'auth');
        $this->min_rounds = $this->config->item('min_rounds', 'auth');
        $this->max_rounds = $this->config->item('max_rounds', 'auth');
        // load the bcrypt class if needed
        if ($this->hash_method == 'bcrypt') {
            if ($this->random_rounds) {
                $rand = rand($this->min_rounds, $this->max_rounds);
                $params = array('rounds' => $rand);
            } else {
                $params = array('rounds' => $this->default_rounds);
            }

            $params['salt_prefix'] = $this->config->item('salt_prefix', 'auth');
            $this->load->library('bcrypt', $params);
        }
    }

    /**
     * @desc Login to the Web Application
     * @param type $username
     * @param type $password
     * @param type $usertype
     * @return type
     */
    public function login($username, $password, $usertype, $is_app = false) {
		$user = $this->user->get_encrypted_user_detail(array('email', 'username'), $username, $is_case_insensitive = true);
		//print_r($user);die;
		
        $status = 'error';
        $msg = 'Username or password is not valid.';
        if (!empty($user)) {
             
            if ($user['user_type'] != $usertype && $usertype == 3 && $is_app ) {
                $status = 'error';
                $msg = 'You are not authorized to access the application';
            } else {
                if ($user['user_type'] > 2) {
                    $user = $this->activate_account($user);
                }
                $user = (object) $user;
                if ($this->is_max_login_attempts_exceeded($user->id)) {
                    generate_log("User [$user->id] account is deactivated due to many failed login attempts");
                    return array('status' => 'error', 'msg' => 'This account is inactive due to many failed login attempts. Please try again after ' . $this->expire_time . ' minute(s)');
                }
                $password = $this->hash_password_db($user->id, $password);

                if ($password === TRUE) {

                    if (!$user->is_authorized) {
                        generate_log("User [$user->id] account is not verified yet. Please check your Inbox to verify your account.");
                        return array('status' => 'error', 'msg' => 'Your account is not verified yet. Please check your Inbox to verify your account.', 'user_id' => $user->id);
                    }
                    if (!$user->is_active) {
                        generate_log("User [$user->id] account account is not active.");
                        return array('status' => 'error', 'msg' => 'This account has been deactivated.', 'user_id' => $user->id);
                    }
                    $this->update_login($user->id);
                    $status = 'success';
                    $msg = 'Logged in successfully.';
                    if ($user->user_type > 2) {
						$diff=date_diff(new DateTime(),new DateTime($user->last_login));
                        generate_log("User [$user->id] logged in successfully");
                        return array('status' => $status, 'msg' => $msg, 'token' => $this->create_token($user->id), 'login_days' => $diff->days, 'user_id' => $user->id);
                    } else {
                        return array('status' => $status, 'msg' => $msg, 'userdetail' => $user);
                    }
                } else {
                    $status = 'error';
                    $msg = 'Incorrect password';
                    if ($user->user_type > 1) {
                        $max_attempts = $this->config->item('maximum_login_attempts', 'auth');
                        $remaining_attemps = ($max_attempts - (int) ($user->login_attempts + 1));
                        if ($remaining_attemps < 1) {
                            $msg = 'This account is inactive due to many failed login attempts. Please try again after ' . $this->expire_time . ' minute(s).';
                        } else {
                            $msg .= '. ' . $remaining_attemps . ' attempts remaining';
                        }
                    }
                    generate_log("User [$user->id] $msg");
                    $this->increase_login_attempts($user->encrypt_username);
                    return array('status' => $status, 'msg' => $msg);
                }
                
            }
        }
        return array('status' => $status, 'msg' => $msg);
    }
    /**
     * @desc Registration for users
     * @param type $data
     * @return type
     */
    public function signup($params = array(),$usertype) {
        $status = 'error';
        $msg = 'Error while signup';
        $log = 'Error while signup';
        extract($params);

		$salt = $this->store_salt ? $this->salt() : FALSE;
        $password = $this->hash_password($password, $salt);
        $username = isset($username) ? aes_256_encrypt(trim($username)) : FALSE;
        $id = isset($id) ? $id : FALSE;

        // Users table.
        $user_data = array(
            'username' => $username,
            'password' => $password,
            'salt' => $salt,
			'is_authorized' => 1,
			'is_active' => 1,
			'authorization_code' => NULL,
            'created_at' => date('Y-m-d H:i:s'),
            'registered_at' => date('Y-m-d H:i:s'),
		);
		
		if(isset($first_name) && $first_name){
			$user_data['first_name']=aes_256_encrypt(trim($first_name));
		}
		if(isset($last_name) && $last_name){
			$user_data['last_name']=aes_256_encrypt(trim($last_name));
		}

		// filter out any data passed that doesnt have a matching column in the users table
        // and merge the set user data and the additional data
        $this->db->trans_start();
		$this->db->update('users', $user_data, array('id' => $id));
        $course_id = $this->user_has_course($id);
		$study_id = user_has_study($id);
		$this->load->model('classes_model', 'classes');
		$classes = $this->classes->get_classes(array('where' => array('course.id' => $course_id,'is_active'=>1,'study_courses.study_id'=>$study_id),'study_id'=>$study_id));
		if(!empty($classes['result'])){
			$start_date = date('Y-m-d 00:00:00');
			$end_date = date('Y-m-d 23:59:59', strtotime("+6 day", strtotime($start_date)));
			foreach($classes['result'] as $ckey => $cval){
				if($ckey == 0 || $ckey == 1){
					$this->db->insert('users_has_classes', array('classes_id' => $cval['id'], 'start_at' => $start_date, 'end_at' => $ckey ? $end_date : NULL, 'status' => 'STARTED', 'users_id' => $id,'current_page_position' => 0, 'week_number' => $ckey));	
				}
				if ($ckey == 1) {
				        $start_date = date('Y-m-d', strtotime("+1 day", strtotime($end_date)));
				        $end_date = date('Y-m-d 23:59:59', strtotime("+6 day", strtotime($start_date)));
				}
			}
		}
        $this->db->trans_complete();
        if ($this->db->trans_status() !== FALSE) {
			$status = 'success';
			$msg = 'Your account has been created successfully.';
			$log = "User[$id] account has been created successfully.";
        }
        generate_log($log);
        return array('status' => $status, 'msg' => $msg);
    }

    /**
     * @desc Registration for users via facebbok and google
     * @param type $data
     * @return type
     */

    /**
     * @desc Registration for users via facebbok and google
     * @param type $data
     * @return type
     */
    public function social_login($params = array()) {
        $status = 'error';
        $msg = 'Error while conneting social networking site';
        extract($params);
        $id = isset($id) ? $id : NULL;
        $first_name = isset($first_name) ? $first_name : '';
        $last_name = isset($last_name) ? $last_name : '';
        $email = isset($email) ? $email : '';
        $name = isset($name) ? $name : '';
        $profile_image = '';
        if (isset($photoUrl)) {
            $content = file_get_contents($photoUrl);
            $config = $this->config->item('assets_images');
            $upload_path = check_directory_exists($config['path']);
            $profile_image = 'profile_image' . uniqid() . '.png';
            copy($photoUrl, $upload_path . '/' . $profile_image);
        }

        if ($name != '') {
            $fullname = explode(' ', $name);
            $first_name = ($first_name == '') ? $fullname[0] : '';
            $last_name = ($last_name == '') ? $fullname[1] : '';
        }

        $provider = isset($provider) ? $provider : '';
        // Users table.
        $user_data = array(
            'first_name' => aes_256_encrypt($first_name),
            'last_name' => aes_256_encrypt($last_name),
            'profile_picture' => $profile_image,
            'last_login' => date('Y-m-d H:i:s'),
            'username' => aes_256_encrypt($email)
        );
        // filter out any data passed that doesnt have a matching column in the users table
        // and merge the set user data and the additional data
        $this->db->trans_start();
        // Check User exist or not 
        $user = $this->user->get_encrypted_user_detail(array('email'), $email);

        if (!empty($user)) {
            $user_id = $user['id'];
            $this->db->update('users', $user_data, array('id' => $user_id));
        } else {
            $user_data['email'] = aes_256_encrypt($email);
            $user_data['username'] = aes_256_encrypt($email);
            $user_data['user_type'] = 2;
            $user_data['is_active'] = 1;
            $user_data['is_authorized'] = 1;
            $user_data['created_at'] = date('Y-m-d H:i:s');
            $this->db->insert('users', $user_data);
            $user_id = $this->db->insert_id('users');
        }


        $this->db->trans_complete();
        if ($this->db->trans_status() !== FALSE) {
            $status = 'success';
            $msg = 'Logged In Successfully.';
            return array('status' => $status, 'msg' => $msg, 'token' => $this->create_token($user_id));
        }
        return array('status' => $status, 'msg' => $msg);
    }

    /**
     * @desc Insert a forgotten password key.
     * @param type $email
     * @return type
     */
    public function send_authorization_code($email) {
        // All some more randomness
        $activation_code_part = "";
        if (function_exists("openssl_random_pseudo_bytes")) {
            $activation_code_part = openssl_random_pseudo_bytes(128);
        }

        for ($i = 0; $i < 1024; $i++) {
            $activation_code_part = sha1($activation_code_part . mt_rand() . microtime());
        }

        $key = $this->hash_code($activation_code_part . $email);

        // If enable query strings is set, then we need to replace any unsafe characters so that the code can still work
        if ($key != '' && $this->config->item('permitted_uri_chars') != '' && $this->config->item('enable_query_strings') == FALSE) {
            // preg_quote() in PHP 5.3 escapes -, so the str_replace() and addition of - to preg_quote() is to maintain backwards
            // compatibility as many are unaware of how characters in the permitted_uri_chars will be parsed as a regex pattern
            if (!preg_match("|^[" . str_replace(array('\\-', '\-'), '-', preg_quote($this->config->item('permitted_uri_chars'), '-')) . "]+$|i", $key)) {
                $key = preg_replace("/[^" . $this->config->item('permitted_uri_chars') . "]+/i", "-", $key);
            }
        }

        // Limit to 40 characters since that's how our DB field is setup
		$expire_time = $this->config->item('authorization_code_expiration', 'auth');
		$link_expires_at = time()+$expire_time*60*60;
        $update = array(
            'authorization_code' => $key,
            'authorization_time' => $link_expires_at
        );
        $user_detail = $this->user->get_encrypted_user_detail(array('email'), $email);
        $status = 'error';
        $msg = 'Unable to make a request please try again later';
        if (!empty($user_detail)) {
            $this->db->update('users', $update, array('id' => $user_detail['id']));
            $return = $this->db->affected_rows() == 1;
            if ($return) {
                $expire_time = $this->config->item('authorization_code_expiration', 'auth');
                $content['link'] = base_url() . 'auth/verify-email/' . $key;
                $content['btntitle'] = $this->config->item('verify_email_btn_titte');
                $content['message'] = sprintf($this->config->item('verify_email_message'), ucfirst($user_detail['username']));
                $content['note'] = sprintf($this->config->item('verify_email_note'), $expire_time);
                $message = $this->load->view('email_template', $content, TRUE);
                $subject = $this->config->item('verify_email_subject');
                if (send_email($subject, $email, $message)) {
                    return TRUE;
                }
            }
            return FALSE;
        } else {
            return FALSE;
        }
    }

    /**
     * @desc Insert a forgotten password key.
     * @param type $email
     * @return type
     */
    public function forgotten_password($email, $is_app = FALSE, $type= '') {
        $email = trim($email);
        // All some more randomness
        $activation_code_part = "";
        $log = '';
        if (function_exists("openssl_random_pseudo_bytes")) {
            $activation_code_part = openssl_random_pseudo_bytes(128);
        }

        for ($i = 0; $i < 1024; $i++) {
            $activation_code_part = sha1($activation_code_part . mt_rand() . microtime());
        }

        $key = $this->hash_code($activation_code_part . $email);

        // If enable query strings is set, then we need to replace any unsafe characters so that the code can still work
        if ($key != '' && $this->config->item('permitted_uri_chars') != '' && $this->config->item('enable_query_strings') == FALSE) {
            // preg_quote() in PHP 5.3 escapes -, so the str_replace() and addition of - to preg_quote() is to maintain backwards
            // compatibility as many are unaware of how characters in the permitted_uri_chars will be parsed as a regex pattern
            if (!preg_match("|^[" . str_replace(array('\\-', '\-'), '-', preg_quote($this->config->item('permitted_uri_chars'), '-')) . "]+$|i", $key)) {
                $key = preg_replace("/[^" . $this->config->item('permitted_uri_chars') . "]+/i", "-", $key);
            }
        }

        // Limit to 40 characters since that's how our DB field is setup
		$expire_time = $this->config->item('forgot_password_expiration', 'auth');
		$link_expires_at = time()+$expire_time*60*60;
        $update = array(
            'forgotten_password_code' => $key,
            'forgotten_password_time' => $link_expires_at
        );

        $user_detail = $this->user->get_encrypted_user_detail(array('email'), $email);
        $status = 'error';
        $msg = 'Unable to send mail, please try again later';

            $url=  base_url() . 'auth/reset-password/' . $key;
            if(isset($type) && $type == 2){
                $url=  base_url() . 'auth/create-password/' . $key;
            }
        if (!empty($user_detail)) {
            $this->db->update('users', $update, array('id' => $user_detail['id']));
            if ($this->db->affected_rows() == 1) {
				$content['note'] = sprintf($this->config->item('expiration_note'), $expire_time);
                $content['link'] = $url;
                if ($is_app) {
                    $content['link'] = $this->config->item('app_url') . '?code=' . $key;
                }
                $content['btntitle'] = ($type == 2) ? $this->config->item('create_password_btn_titte') : $this->config->item('reset_password_btn_titte');
                $content['heading'] = ($type == 2) ? $this->config->item('create_password_heading') : $this->config->item('reset_password_heading');
                $content['message'] = sprintf(($type == 2) ? $this->config->item('create_password_message') : $this->config->item('reset_password_message'), ucfirst($user_detail['first_name']));
                $content['footer'] = TRUE;
                $message = $this->load->view('email_template', $content, True);

                $subject = $this->config->item('reset_password_subject');

                if (send_email($subject, $email, $message)) {
                    $log = "User [$user_detail[id]] request for forgot password";
                    $status = "success";
                    $msg = 'A link to reset your password has been sent. Please check your email.';
                } else {
                    $log = "User [$user_detail[id]] unable to send mail, please try again later";
                    $status = 'error';
                    $msg = 'Unable to send mail, please try again later';
                }
            }
        }
        generate_log($log);
        return array('status' => $status, 'msg' => $msg);
    }

    /**
     * Misc functions
     *
     * Hash password : Hashes the password to be stored in the database.
     * Hash password db : This function takes a password and validates it
     * against an entry in the users table.
     * Salt : Generates a random salt value.
     *
     */

    /**
     * @desc Hashes the password to be stored in the database. 
     * @param type $password
     * @param type $salt
     * @param type $use_sha1_override
     * @return boolean
     */
    public function hash_password($password, $salt = false, $use_sha1_override = FALSE) {
        if (empty($password)) {
            return FALSE;
        }
        // bcrypt
        if ($use_sha1_override === FALSE && $this->hash_method == 'bcrypt') {
            return $this->bcrypt->hash($password);
        }

        if ($this->store_salt && $salt) {
            return sha1($password . $salt);
        } else {
            $salt = $this->salt();
            return $salt . substr(sha1($salt . $password), 0, -$this->salt_length);
        }
    }

    /**
     * This function takes a password and validates it
     * against an entry in the users table.
     *
     * @return void
     * ''
     * */
    public function hash_password_db($id, $password, $use_sha1_override = FALSE) {
        if (empty($id) || empty($password)) {
            return FALSE;
        }

        $query = $this->db->select('password, salt')
                ->where('id', $id)
                ->limit(1)
                ->order_by('id', 'desc')
                ->get('users');

        $hash_password_db = $query->row();

        if ($query->num_rows() !== 1) {
            return FALSE;
        }

        // bcrypt
        if ($use_sha1_override === FALSE && $this->hash_method == 'bcrypt') {
            if ($hash_password_db->password != NULL && $this->bcrypt->verify($password, $hash_password_db->password)) {
                return TRUE;
            }

            return FALSE;
        }

        // sha1
        if ($this->store_salt) {
            $db_password = sha1($password . $hash_password_db->salt);
        } else {
            $salt = substr($hash_password_db->password, 0, $this->salt_length);

            $db_password = $salt . substr(sha1($salt . $password), 0, -$this->salt_length);
        }

        if ($db_password == $hash_password_db->password) {
            return TRUE;
        } else {
            return FALSE;
        }
    }

    /**
     * Generates a random salt value for forgotten passwords or any other keys. Uses SHA1.
     *
     * @return void
     * ''
     * */
    public function hash_code($password) {
        return $this->hash_password($password, FALSE, TRUE);
    }

    /**
     * Generates a random salt value.
     *
     * Salt generation code taken from https://github.com/ircmaxell/password_compat/blob/master/lib/password.php
     *
     * @return void

     * */
    public function salt() {

        $raw_salt_len = 16;

        $buffer = '';
        $buffer_valid = false;

        if (function_exists('random_bytes')) {
            $buffer = random_bytes($raw_salt_len);
            if ($buffer) {
                $buffer_valid = true;
            }
        }

        if (!$buffer_valid && function_exists('mcrypt_create_iv') && !defined('PHALANGER')) {
            $buffer = mcrypt_create_iv($raw_salt_len, MCRYPT_DEV_URANDOM);
            if ($buffer) {
                $buffer_valid = true;
            }
        }

        if (!$buffer_valid && function_exists('openssl_random_pseudo_bytes')) {
            $buffer = openssl_random_pseudo_bytes($raw_salt_len);
            if ($buffer) {
                $buffer_valid = true;
            }
        }

        if (!$buffer_valid && @is_readable('/dev/urandom')) {
            $f = fopen('/dev/urandom', 'r');
            $read = strlen($buffer);
            while ($read < $raw_salt_len) {
                $buffer .= fread($f, $raw_salt_len - $read);
                $read = strlen($buffer);
            }
            fclose($f);
            if ($read >= $raw_salt_len) {
                $buffer_valid = true;
            }
        }

        if (!$buffer_valid || strlen($buffer) < $raw_salt_len) {
            $bl = strlen($buffer);
            for ($i = 0; $i < $raw_salt_len; $i++) {
                if ($i < $bl) {
                    $buffer[$i] = $buffer[$i] ^ chr(mt_rand(0, 255));
                } else {
                    $buffer .= chr(mt_rand(0, 255));
                }
            }
        }

        $salt = $buffer;

        // encode string with the Base64 variant used by crypt
        $base64_digits = 'ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz0123456789+/';
        $bcrypt64_digits = './ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz0123456789';
        $base64_string = base64_encode($salt);
        $salt = strtr(rtrim($base64_string, '='), $base64_digits, $bcrypt64_digits);

        $salt = substr($salt, 0, $this->salt_length);


        return $salt;
    }

    /**
     * Checks username
     *
     * @return bool
     * ''
     * */
    public function username_check($username = '') {
        if ($username != '') {
            $user_detail = $this->user->get_encrypted_user_detail(array('username'), trim($username));
            return count($user_detail) > 0;
        }
        return FALSE;
    }

    /**
     * Checks email
     *
     * @return bool
     * ''
     * */
    public function email_check($email = '') {
        if ($email != '') {
            $user_detail = $this->user->get_encrypted_user_detail(array('email'), trim($email));
           return count($user_detail) > 0;
        }
        return FALSE;
	}
	
	public function register_token($user_access_code = '') {
        if ($user_access_code != '') {
            $user_detail = $this->db->where('register_token', $user_access_code)->count_all_results('users');
           return $user_detail > 0;
        }
        return FALSE;
    }

    /**
     * Checks user
     *
     * @return bool
     * ''
     * */
    public function user_check($email = '') {
        if ($email != '') {
            $user_detail = $this->user->get_encrypted_user_detail(array('email'), trim($email));
            return ($user_detail['user_type'] == 3) ? FALSE : TRUE ;
        }
        return FALSE;
    }

    /**
     * @param string $identity: user's identity
     * */
    public function increase_login_attempts($username) {
        if ($this->config->item('track_login_attempts', 'auth')) {

            $this->db->select('login_attempts,id');
            $this->db->where('username', $username);
            $this->db->where('user_type !=', 1);
            $this->db->or_where('email', $username);
            $qres = $this->db->get('users');
            if ($qres->num_rows() > 0) {
                $user = $qres->row();
                if ($user->login_attempts == ($this->config->item('maximum_login_attempts', 'auth')-1)) {
                    $data = array('login_attempts' => $user->login_attempts + 1, 'is_active' => 0);
                } else {
                    $data = array('login_attempts' => $user->login_attempts + 1);
                }
                $data['updated_at'] = date('Y-m-d H:i:s');
                return $this->db->update('users', $data, array('id' => $user->id));
            }
        }
        return FALSE;
    }

    /**
     * @param string $identity: user's identity
     * @return boolean
     * */
    public function is_max_login_attempts_exceeded($id) {
        if ($this->config->item('track_login_attempts', 'auth')) {
            $max_attempts = $this->config->item('maximum_login_attempts', 'auth');
            if ($max_attempts > 0) {
                $attempts = $this->get_attempts_num($id);
                return $attempts >= $max_attempts;
            }
        }
        return FALSE;
    }

    /**
     * @param string $identity: user's identity
     * @return int
     */
    public function get_attempts_num($id) {
        if ($this->config->item('track_login_attempts', 'auth')) {
            $this->db->select('login_attempts');
            $this->db->where('id', $id);
            $qres = $this->db->get('users');
            if ($qres->num_rows() > 0) {
                $user = $qres->row();
                return $user->login_attempts;
            }
        }
        return 0;
    }

    /**
     * clear_login_attempts
     * Based on code from Tank Auth, by Ilya Konyukhov (https://github.com/ilkon/Tank-Auth)
     *
     * @param string $identity: user's identity
     * @param int $old_attempts_expire_period: in seconds, any attempts older than this value will be removed.
     *                                         It is used for regularly purging the attempts table.
     *                                         (for security reason, minimum value is lockout_time config value)

     * */
    public function update_login($user_id) {
        if ($this->config->item('track_login_attempts', 'auth')) {
            $this->db->where('id', $user_id);

            return $this->db->update('users', array('login_attempts' => 0, 'last_login' => date('Y-m-d H:i:s'), 'last_access_date' => date('Y-m-d H:i:s')));
		}
    }

    public function create_token($user_id) {
        $this->db->select('token');
        $this->db->where('users_id', $user_id);
        $qres = $this->db->get('user_tokens');
        if ($qres->num_rows() > 0) {
            $user = $qres->row();
            $data = array(
                'users_id' => $user_id,
                'logout_time' => null
            );
            $this->db->update('user_tokens', array('logout_time' => date("Y-m-d H:i:s")), $data);
        }
        $token = $this->hash_password(uniqid());
        $this->db->insert('user_tokens', array('users_id' => $user_id, 'token' => $token, 'login_time' => date("Y-m-d H:i:s")));
        $id = $this->db->insert_id('user_tokens');
        return (isset($id)) ? $token : FALSE;
    }

    /**
     * Get forgotten code detail
     *
     * @return array
     * ''
     * */
    public function forgotten_code_detail($code = '') {
        if (empty($code)) {
            return FALSE;
        }

        $query = $this->db->select('id,forgotten_password_code,forgotten_password_time')
                ->where('forgotten_password_code', $code)
                ->limit(1)
                ->order_by('id', 'desc')
                ->get('users');

        $user_detail = $query->row();

        if ($query->num_rows() > 0) {
            return $user_detail;
        } else {
            return false;
        }
    }

    /**
     * reset password
     *
     * @return bool
     * 
     * */
    public function reset_password($id = false, $password = false) {
        $is_exist = $this->db->where('id', $id)->limit(1)->count_all_results('users') > 0;
        $status = 'error';
        $msg = 'Passowrd link is invalid';
        $log = "User [$id ] 'passowrd link is invalid.";
        if ($is_exist) {
            $query = $this->db->select('id, password, salt')
                    ->where('id', $id)
                    ->limit(1)
                    ->order_by('id', 'desc')
                    ->get('users');

            if ($query->num_rows() == 1) {
                $is_password_exist = $this->check_password_history($id, $password);
                if (!$is_password_exist) {
                    $result = $query->row();
                    $salt = $this->store_salt ? $this->salt() : FALSE;
                    $new = $this->hash_password($password, $salt);

                    $data = array(
                        'password' => $new,
                        'forgotten_password_code' => NULL,
                        'forgotten_password_time' => NULL,
                        'is_authorized' => 1,
                        'is_active' => 1,
                        'login_attempts' => 0,
                        'salt' => $salt
                    );

                    $this->db->update('users', $data, array('id' => $id));
                    $this->add_previous_password_detail($id);
                    $return = $this->db->affected_rows() == 1;
                    if ($return) {
                        $status = 'success';
                        $msg = 'Password reset successfully.';
                        $log = "User [$id ] password reset successfully.";
                    }
                } else {
                    $status = 'error';
                    $msg = 'Your password must be different from the previous ' . $this->config->item('store_number_of_password') . ' passwords.';
                    $log = "User [$id ] your password must be different from the previous 6 passwords.";
                }
            }
        }
        generate_log($log);
        return array('status' => $status, 'msg' => $msg);
    }

    /**
     * Update Profile
     *
     * @return array
     * 
     * */
    public function update_profile($params = array()) {
        extract($params);
        $status = 'error';
        $msg = 'User not found.';
        $log = '';
        $user = array();

        $gender = isset($gender) ? $gender : FALSE;
        $id = isset($id) ? $id : FALSE;
        if ($id) {
			$data = array(); //'participant_id' => $participant_id
			if(isset($first_name) && $first_name){
				$data['first_name']=aes_256_encrypt(trim($first_name));
			}
			if(isset($gender) && $gender){
				$data['gender']=aes_256_encrypt(trim($gender));
			}
			if(isset($profile_picture)){
				$data['profile_picture']=$profile_picture;
			}
			if(isset($last_name) && $last_name){
				$data['last_name']=aes_256_encrypt(trim($last_name));
			}
			if(isset($unique_id) && $unique_id){
				$data['unique_id']=(trim($unique_id));
			}

			
            $is_exist = $this->db->where('id', $id)->limit(1)->count_all_results('users') > 0;
            if ($is_exist) {
                $this->db->update($this->tables['users'], $data, array('id' => $id));
                $query = $this->db->select('*')
                                ->where(array('id' => $id))
                                ->limit(1)->get('users');
                if ($query->num_rows() > 0) {
                    $user = $query->row();
                    $user->username = aes_256_decrypt($user->username);
                    $user->first_name = aes_256_decrypt($user->first_name);
                    $user->last_name = aes_256_decrypt($user->last_name);
                    $user->email = aes_256_decrypt($user->email);
                }
                $status = 'success';
                $msg = 'Profile updated successfully.';
                $log = "User [$id] profile detail updated successfully";
            }
        }
        generate_log($log);
        return array('status' => $status, 'msg' => $msg, 'userdetail' => $user);
    }

    /**
     * Update login detail
     *
     * @return array
     * 
     * */
    public function update_login_detail($params = array()) {
        extract($params);
        $log = "";
        $is_password = FALSE;
        if ($password != '') {
            $is_password = TRUE;
        }
        $user = array();

        $data = array("username" => aes_256_encrypt(trim($username)), 'email' => aes_256_encrypt(trim($email)));
        $query = $this->db->where('id', $id)->limit(1)->get('users');
        if ($query->num_rows() > 0) {
            $user_data = $query->row();
            if ($is_password) {
                $salt = $this->store_salt ? $this->salt() : FALSE;
                $data['password'] = $this->hash_password($password, $salt);
                $data['salt'] = $salt;
            }
            $this->db->update($this->tables['users'], $data, array('id' => $id));
            ($is_password) ? $this->add_previous_password_detail($id) : FALSE;
            $query = $this->db->select('*')
                            ->where(array('id' => $id))
                            ->limit(1)->get('users');
            if ($query->num_rows() > 0) {
                $user = $query->row();
                //$user = $query->row();

                $user->username = aes_256_decrypt($user->username);
                $user->first_name = aes_256_decrypt($user->first_name);
                $user->last_name = aes_256_decrypt($user->last_name);
                $user->email = aes_256_decrypt($user->email);
            }
            $status = 'success';
            $msg = 'User detail updated successfully.';
            $log = "User [$id] login detail updated successfully";
        } else {
            $status = 'error';
            $msg = 'User not found.';
        }
        generate_log($log);
        return array('status' => $status, 'msg' => $msg, 'userdetail' => $user);
    }

    /**
     * Store previous password detail
     *
     * @return boolean
     * 
     * */
    public function add_previous_password_detail($id) {
        $query = $this->db->where('id', $id)->limit(1)->get('users');
        if ($query->num_rows() > 0) {
            $user_data = $query->row();
            $history = $this->db->where('users_id', $id)->order_by("id", "asc")->get($this->tables['users_secure']);
            if ($history->num_rows() > ($this->config->item('store_number_of_password') - 1)) {
                $history = $history->result();
                $this->db->delete($this->tables['users_secure'], array('id' => $history[0]->id));
            }
            $data['password_history_1'] = $user_data->password;
            $data['salt_history_1'] = $user_data->salt;
            $data['users_id'] = $id;
            $data["last_update"] = date('Y-m-d H:i:s');
            $this->db->insert($this->tables['users_secure'], $data);
            $insert_id = $this->db->insert_id('user_tokens');
            return (isset($insert_id)) ? TRUE : FALSE;
        }
    }

    /**
     * Get forgotten code detail
     *
     * @return array
     * ''
     * */
    public function authorization_code_detail($code = '') {
        if (empty($code)) {
            return FALSE;
        }

        $query = $this->db->select('id,authorization_code,authorization_time')
                ->where('authorization_code', $code)
                ->limit(1)
                ->order_by('id', 'desc')
                ->get('users');
        $user_detail = $query->row();
        if ($query->num_rows() > 0) {
            return $user_detail;
        } else {
            return false;
        }
    }

    /**
     * Store previous password detail
     *
     * @return boolean
     * 
     * */
    public function verify_email($id) {
        $status = 'error';
        $msg = 'Error while activate account';
        $log = '';
        if ($id > 0) {
            $query = $this->db->where('id', $id)->limit(1)->get('users');
            if ($query->num_rows() > 0) {
                $user_data = $query->row();
                $data["updated_at"] = date('Y-m-d H:i:s');
                $data["is_active"] = 1;
                $data["is_authorized"] = 1;
                $data["authorization_code"] = NULL;
                $data["authorization_time"] = NULL;
                $this->db->update($this->tables['users'], $data, array('id' => $id));
                $return = $this->db->affected_rows() == 1;
                if ($return) {
                    $status = 'success';
                    $msg = 'Your account has been verified successfully.';
                    $log = "User [$id] account has been verified successfully";
                }
            }
        }
        generate_log($log);
        return array('status' => $status, 'msg' => $msg, 'id' => $class_id);
    }

    /**
     * Check Token
     * It is a callback function take user token to check if token exist in system or not
     * @return Bool
     * */
    function check_token($token = FALSE) {
        // if ($token) {
        //     $query = $this->db->where(array('token' => $token, 'logout_time' => NULL))->limit(1)->get('user_tokens');
        //     if ($query->num_rows() > 0) {
        //         return TRUE;
        //     } else {
        //         return FALSE;
        //     }
        // }
		// return FALSE;
		
		if ($token) {
			$query = $this->db->where(array('token' => $token, 'logout_time' => NULL))->limit(1)->get('user_tokens');
			if ($query->num_rows() > 0) {
				// checks for last login and last access time,
				// if it exceeds with default session time then make a user log out forcefully.
				$tokenRow = $query->row();
				// get user details
				$user = $this->db->select('id,last_login,last_access_date')
								->where('id',$tokenRow->users_id)->get($this->tables['users'])->row();
				if(isset($user->last_access_date) && !is_null($user->last_access_date) && $user->last_access_date){
					// default session time
					$session_out_time = $this->config->item('session_logout_time');
			
					$diff = strtotime("now")-strtotime($user->last_access_date); // difference in secondszz
					if($diff > $session_out_time){ // check for the difference
						$this->db->update('user_tokens', array('logout_time'=>$user->last_access_date), array('token' => $tokenRow->token));
						generate_log("User[$user->id] logged out forcefully because of session time out.");
						return FALSE;
					}
				}
				$this->db->update($this->tables['users'], array('last_access_date' => date('Y-m-d H:i:s')), array('id' => $user->id));
				return TRUE;
            }
		}
		//echo"hi"; die;
		
        return FALSE;
    }

    /**
     * Delete Token
     * @return Bool
     * */
    function update_token($token = FALSE) {
        $flag = FALSE;
        if ($token) {
            $id = $this->get_user($token);
            $log = "User [$id] could not be logout.";
			$this->db->update('user_tokens', array('logout_time' => date('Y-m-d H:i:s')), array('token' => $token));
			
            $return = $this->db->affected_rows() == 1;
            if ($return) {
                $flag = TRUE;
                $log = "User [$id] logged out successfully.";
            } else {
                $flag = FALSE;
                $log = "User [$id] could not be logged out.";
            }
        }
        generate_log($log);
        return $flag;
    }

    /**
     * Check Token
     * It is a callback function take user token to check if token exist in system or not
     * @return Bool
     * */
    function get_user($token = FALSE) {
        if ($token) {
            $query = $this->db->where('token', $token)->limit(1)->get('user_tokens');
            if ($query->num_rows() > 0) {
                $userdata = $query->row();
                return $userdata->users_id;
            } else {
                return FALSE;
            }
        }
        return FALSE;
    }

    // SELECT COUNT(*) as login_count FROM `user_tokens` WHERE `users_id` = 164 AND `login_time` BETWEEN '2021-10-26 00:00:00' AND '2021-10-28 03:22:52'

    function count_consecutive_day($user_id) {
        $user_id = $user_id ? $user_id : FALSE;
        $created_at = date('Y-m-d H:i:s');
        $query = $this->db->where('users_id', $user_id)->where('login_time BETWEEN "'. date('Y-m-d 00:00:00', strtotime('-1 day', strtotime($created_at))). '" and "'. date('Y-m-d H:i:s ', strtotime($created_at)).'"')->get('user_tokens');
        $detail = $query->result(); 
        if (count($detail) > 0){
            $count = count($detail);
        }else{
            $count = 0;
        }
        $this->db->update('users', array('consecutive_days' => $count, 'last_access_date' => date('Y-m-d H:i:s')), array('id' => $user_id));
    }

    

    function count_consecutive_day_old($user_id) {
        $user_id = $user_id ? $user_id : FALSE;
        $created_at = date('Y-m-d');
        $query = $this->db->where('id', $user_id)->get('users');
        if ($query->num_rows() > 0) {
            $detail = $query->row();            
            $last_access = date_create($created_at);
            if ($detail->last_access_date != NULL) {
                $last_access = date_create(date('Y-m-d', strtotime($detail->last_access_date)));
            }
            $current_date = date_create($created_at);
            $diff = date_diff($current_date, $last_access);
            $days_interval = $diff->format("%d");
            if ($days_interval == 1) {
                $count = $detail->consecutive_days + 1;
            } elseif ($days_interval == 0) {
                $count = ($detail->consecutive_days != null) ? $detail->consecutive_days : 0;
            } else {
                $count = 0;
            }
            $this->db->update('users', array('consecutive_days' => $count, 'last_access_date' => date('Y-m-d H:i:s')), array('id' => $user_id));
        }
    }

    public function get_setting($courses_id) {
        $this->load->model('course_model');
        $setting = $this->course_model->get_setting($courses_id);
        $result = array();
        if (!empty($setting['result'])) {
            foreach ($setting['result'] as $key => $val) {
                $result[$val['courses_id']][$val['key']] = $val['value'];
            }
        }
        return $result;
    }

    public function visited_user($user_id) {
        if ($user_id) {
            $query = $this->db->where(array('users_id' => $user_id))->count_all_results('user_tokens');
            if ($query <= 1) {
                return TRUE;
            } else {
                return FALSE;
            }
        }
        return FALSE;
    }

    /**
     * Check password history if password exits or not
     * @param $id
     * @param $password
     * @return Bool
     * */
    public function check_password_history($id, $password) {
        if ($id) {
            $password_history = array();
            $is_password_exist = false;
            $history = $this->db->where('users_id', $id)->order_by("id", "asc")->get($this->tables['users_secure']);
            if ($history->num_rows() > 0) {
                $password_history = $history->result();
                foreach ($password_history as $val) {
                    if ($this->bcrypt->verify($password, $val->password_history_1)) {
                        $is_password_exist = TRUE;
                    }
                }
            }
            return $is_password_exist;
        }
    }

    public function activate_account($user) {
        if (!empty($user)) {
            $last_update_time = $user['updated_at'];
            $current_time = date('Y-m-d H:i:s');
            $datetime1 = date_create($current_time);
            $datetime2 = date_create($last_update_time);
            $interval = date_diff($datetime1, $datetime2);
            $is_update = FALSE;
            $data = array(
                'is_active' => 1,
                'login_attempts' => 0,
                'updated_at' => $current_time
            );

            $is_update = ($interval->i >= $this->config->item('lockout_time') || $interval->h);
            if (!$interval->h && $interval->i < $this->config->item('lockout_time')) {
                $this->expire_time = $this->config->item('lockout_time') - $interval->i;
            }


            if ($is_update) {
                $this->db->update($this->tables['users'], $data, array('id' => $user['id']));
                $user['is_active'] = 1;
                $user['login_attempts'] = 0;
            }

            return $user;
        }
    }

    /**
     * @desc Insert a forgotten password key.
     * @param type $email
     * @return type
     */
    public function send_contact_us_email($params) {
        $status = 'error';
        $msg = 'Unable to send a request please try again later';
        extract($params);
        $name = ucfirst(trim($first_name)) . ' ' . ucfirst(trim($last_name));
        $content['message'] = sprintf($this->config->item('contact_us_message'), ucfirst($name), trim($email), trim($message));
        $email_content = $this->load->view('email_template', $content, true);
        $subject = $this->config->item('contact_us_subject');
        $to = $this->config->item('contact_us_email');
        if (send_email($subject, $to, $email_content)) {
            $status = "success";
            $msg = "Thanks for your message! We will get back to you ASAP!";
        }

        return array('status' => $status, 'msg' => $msg);
	}
	
	public function check_access_token($token){
		if($token){
			$query = $this->db->where(array('register_token' => $token))->select('id')->get('users');
			if($query->num_rows() > 0){
				return TRUE;
			}
				return FALSE;
		}
		return FALSE;
    }
    
    public function participant_id($firstname, $lastname){
        $ff = strtolower(substr($firstname, 0, 2));
		$ll = strtolower(substr($lastname, 0, 3));
		$participant_id = $ff.$ll;
		$info_array = array('table'=>'users');
		$info_array['fields'] = 'id';
		$info_array['count'] = true;
		$info_array['where'] = "participant_id LIKE '".$participant_id."%'";
		$response = $this->db_model->get_data($info_array);

        if($response['total'] > 0){
            $i = 96 + $response['total'];
            $char = chr($i);
            $participant_id = $ff.$ll.$char;
        }
		return $participant_id;
	}
	
	/**
     * @desc Insert a forgotten password key.
     * @param type $email
     * @return type
     */
	public function invitation_email_status($email) {
		$log = '';
		// All some more randomness		
        $activation_code_part = "";
        if (function_exists("openssl_random_pseudo_bytes")) {
            $activation_code_part = openssl_random_pseudo_bytes(128);
        }

        for ($i = 0; $i < 1024; $i++) {
            $activation_code_part = sha1($activation_code_part . mt_rand() . microtime());
        }

        $key = $this->hash_code($activation_code_part . $email);

        // If enable query strings is set, then we need to replace any unsafe characters so that the code can still work
        if ($key != '' && $this->config->item('permitted_uri_chars') != '' && $this->config->item('enable_query_strings') == FALSE) {
            // preg_quote() in PHP 5.3 escapes -, so the str_replace() and addition of - to preg_quote() is to maintain backwards
            // compatibility as many are unaware of how characters in the permitted_uri_chars will be parsed as a regex pattern
            if (!preg_match("|^[" . str_replace(array('\\-', '\-'), '-', preg_quote($this->config->item('permitted_uri_chars'), '-')) . "]+$|i", $key)) {
                $key = preg_replace("/[^" . $this->config->item('permitted_uri_chars') . "]+/i", "-", $key);
            }
        }

        // Limit to 40 characters since that's how our DB field is setup
		// $expire_time = $this->config->item('authorization_code_expiration', 'auth');
		// $link_expires_at = time()+$expire_time*60*60;
        $update = array(
            'authorization_code' => $key,
        );
        $user_detail = $this->user->get_encrypted_user_detail(array('email'), $email);
        $status = 'error';
        $msg = 'Unable to make a request please try again later';
        if (!empty($user_detail)) {
            $this->db->update('users', $update, array('id' => $user_detail['id']));
			$return = $this->db->affected_rows() == 1;
			$status = 'error';
			$msg = 'Unable to send mail, please try again later';
            if ($return) {
                $expire_time = $this->config->item('authorization_code_expiration', 'auth');
                $content['link'] = $this->config->item('app_url') . '#/sign-up/' . $key;
                $content['btntitle'] = $this->config->item('invite_email_btn_titte');
                $content['message'] = sprintf($this->config->item('invite_email_message'), ucfirst('Participant'));
                $message = $this->load->view('email_template', $content, TRUE);
                $subject = $this->config->item('invite_email_subject');
                $study_id = user_has_study($user_detail['id']);
				if (send_email($subject, $email, $message, true, $study_id)) {
                    $log = "Participant [$user_detail[id]] sign up invitation";
                    $status = "success";
                    $msg = 'A link to participant sign up invitation. Please check your email.';
                }
            }
		} 
		generate_log($log);
		return array('status' => $status, 'msg' => $msg);
    }
    
    public function user_has_course($user_id){
        $res = $this->db->select('courses_id')->where('users_id', $user_id)->get('users_has_courses')->row();
        return isset($res->courses_id) ? $res->courses_id : 1;
    }
}
