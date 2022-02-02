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

class User extends CI_Controller {

    /**
     * @desc Class Constructor
     */
    function __construct() {
        parent::__construct();
        if ($this->session->userdata('logged_in') == FALSE) {
            redirect('auth');
        }
        $this->store_salt = $this->config->item('store_salt', 'auth');
        $this->load->model('User_model', 'user');
        $this->load->model('Auth_model', 'auth');
        $this->load->model('Course_model', 'course');
        $this->load->model('Classes_model', 'classes');
        $this->load->model('Community_model', 'community');
        $this->load->model('Organization_model', 'organization');
        $this->load->model('Homework_model', 'homework');
	}
	
	/**
     * @desc Showing list of all participant users
     *
     */
    function list_users($course_id='', $study_id='') {
	// Set the title
		if(!$course_id || !$study_id){
            $title = !$course_id ? 'course' : 'study';
			$this->session->set_flashdata('error', "No ".$title." selected.");
			redirect('study');
		}
        get_plugins_in_template('datatable');
        $this->breadcrumbs->push('Study', 'study');
        $this->breadcrumbs->push('Participant List', 'participant-list');
        $data['breadcrumb'] = $this->breadcrumbs->show();
        $this->template->title = 'List Users';
        $this->template->javascript->add(base_url() . 'assets/js/bootstrap-switch.min.js');
        $this->template->stylesheet->add(base_url() . 'assets/css/bootstrap-switch.min.css');
        $data['course_id'] = $course_id;
        $data['study_id'] = $study_id;
        $this->template->content->view('list_users', $data);

    // Publish the template
        $this->template->publish();
    }

    public function get_users_data($usertype, $course_id='', $study_id='') {
		$params = $this->input->get();
		if($course_id && $study_id && $usertype == 3){
			$params['where'] = array('users.user_type' => $usertype, 'uhc.courses_id' => $course_id,'uhc.study_id' => $study_id);
		} else if($usertype == 2){
            $org_id_array = $this->course->get_user_has_organization($this->session->userdata('logged_in')->id);
            $params['where'] = array('users.user_type' => $usertype, 'users.id !=' => $this->session->userdata('logged_in')->id);
			$params['where_in'] = $org_id_array;
		} else {
            $params['where'] = array('users.user_type' => $usertype);
        }
        $params['usertype'] = $usertype;
        $data = $this->user->get_users($params);
        $rowCount = $data['total'];
        $output = array(
            "sEcho" => intval($this->input->get('sEcho')),
            "iTotalRecords" => $rowCount,
            "iTotalDisplayRecords" => $rowCount,
            "aaData" => []
        );
        $i = $this->input->get('iDisplayStart') + 1;
        $is_authorized = array('No', 'Yes');
        $is_active = array('Deactive', 'Active');
        if ($data['result']) {
            foreach ($data['result'] as $val) {
                //if($val['id'] != $this->session->userdata('logged_in')->id){
                $li = '';
                if($usertype == 2){
                    //<a class="edit_course" title="Assign Course" href="#" data-params=' . json_encode(array("user_id" => $val['id'])) . ' data-toggle="modal" data-target="#edit_course" data-url="' . base_url() . 'course/get-detail"><i class="fa fa-edit"></i></a>
                   $link = '<a class="user_detail" title="User Detail" href="#" data-params=' . json_encode(array("user_id" => $val['id'])) . ' data-toggle="modal" data-target="#view_user" data-url="' . base_url() . 'user/get-detail"><i class="fa fa-eye"></i></a><a class="edit_course" title="Edit Admin" href="' . base_url() . 'user/edit-user/'.$val['id'].'" ><i class="fa fa-edit"></i></a>';
                   if(!is_null($val['is_active']) && $val['is_active'] == 1){
                        $link .= '<a class="change-status btn btn-xs btn-primary" href="javascript:void(0)" data-name="'.$val['first_name'].' '.$val['last_name'].'" data-type="status" data-msg="deactivate" data-url="' . base_url('user/edit-status/status/' . $val['id'] .'/0') . '">Deactivate</a>';
                    } else if (!is_null($val['is_active']) && $val['is_active'] == 0) {
                        $link .= '<a class="change-status btn btn-xs btn-primary" href="javascript:void(0)" data-name="'.$val['first_name'].' '.$val['last_name'].'" data-type="status" data-msg="re-activate" data-url="' . base_url('user/edit-status/status/' . $val['id'] .'/1') . '">Re-Activate</a>';
                    }
                  
                }else {
                    $link = '<a class="user_detail" title="User Detail" href="' . base_url() . 'user/participants-detail/'.$val['id'].'" ><i class="fa fa-eye"></i></a>';
                    
                    if(!is_null($val['is_active']) && $val['is_active'] == 1){
                        $link .= '<a class="change-status btn btn-xs btn-primary" href="javascript:void(0)" data-name="'.$val['first_name'].' '.$val['last_name'].'" data-type="status" data-msg="deactivate" data-url="' . base_url('user/edit-status/status/' . $val['id'] .'/0/'. $course_id.'/'. $study_id) . '">Deactivate</a>';
                    } else if (!is_null($val['is_active']) && $val['is_active'] == 0) {
                        $link .= '<a class="change-status btn btn-xs btn-primary" href="javascript:void(0)" data-name="'.$val['first_name'].' '.$val['last_name'].'" data-type="status" data-msg="re-activate" data-url="' . base_url('user/edit-status/status/' . $val['id'] .'/1/'. $course_id.'/'. $study_id) . '">Re-Activate</a>';
                    }
                    if($val['mute_notification'] == 1){
                        $link .= '<a class="change-status btn btn-xs btn-danger" href="javascript:void(0)" data-name="'.$val['username'].'" data-type="mute" data-msg="unmute" data-url="' . base_url('user/edit-status/mute/' . $val['id'] .'/0/'. $course_id.'/'. $study_id) . '">Unmute</a>';
                    } else if ($val['mute_notification'] == 0) {
                        $link .= '<a class="change-status btn btn-xs btn-primary" href="javascript:void(0)" data-name="'.$val['username'].'" data-type="mute" data-msg="mute" data-url="' . base_url('user/edit-status/mute/' . $val['id'] .'/1/'. $course_id.'/'. $study_id) . '">Mute</a>';
                    }
                }

                if($val['is_active'] == null && $val['is_authorized'] == null && $usertype == 3){
                    $link .= '<a class="reinvite_link" title="Reinvite Link" href="' . base_url() . 'user/reinvite-link?user_id='.$val['id'].'&course_id='.$course_id.'&study_id='.$study_id.'" data-params=' . json_encode(array("user_id" => $val['id'])) . ' data-toggle="modal" data-url="' . base_url() . 'user/reinvite-link"><i class="fa fa-link"></i></a>';
                } else if($val['is_authorized'] == null && $val['is_active'] == null && $usertype == 2){
                    $link .= '<a class="reinvite_link" title="Reinvite Link" href="' . base_url() . 'user/reinvite-link?user_id='.$val['id'].'" data-params=' . json_encode(array("user_id" => $val['id'])) . ' data-toggle="modal" data-url="' . base_url() . 'user/reinvite-link"><i class="fa fa-link"></i></a>';
                }
                if($val['user_type'] == 3) {
                    $output['aaData'][] = array(
                        "DT_RowId" => $val['id'],
                        $i++,
                        $val['unique_id'],
                        $val['username'],
                        $val['email'],
                        ($val['is_active']) ? 'Active' : 'Inactive',
                        (!is_null($val['registered_at']) && !is_null($val['is_authorized']) &&  !is_null($val['is_active'])) ? date("m/d/Y", strtotime($val['registered_at'])) : date("m/d/Y", strtotime($val['created_at'])),
                        $link
                    ); 
                } else {
                    $output['aaData'][] = array(
                        "DT_RowId" => $val['id'],
                        $i++,
                        $val['username'],
                        $val['first_name'].' '.$val['last_name'],
                        $val['email'],
                        isset($val['title']) ? $val['title'] : '',
                        ($val['is_active']) ? 'Active' : 'Inactive',
                        
                        $link
                    ); 
                }
                
                //}
            }
        }
        echo json_encode($output);
        die;
    }

     /**
     * @send reinvite link to user
     *
     */
     public function reinvite_link(){
        if ($this->input->get()) {
            $user_id = $this->input->get('user_id');
            $course_id = $this->input->get('course_id');
            $study_id = $this->input->get('study_id');
            $result = $this->user->get_detail($user_id);
            if (!empty($result)) {
				$invitation_email_status =  $this->auth->invitation_email_status($result['email']);
				if ($invitation_email_status['status'] == 'success') {                        
					$msg = 'A reinvite link to create password of admin has been sent to given email. Please check your email.';
				}
                $data = array('status' => 'success', 'msg' => $msg);
            } else {
                $data = array('status' => 'error', 'msg' => "User not found");
            }
            $this->session->set_flashdata($data['status'], $data['msg']);
            if($result['user_type'] == 2){
                redirect('user/list-admin');
            }else{
                redirect('user/list-users/'.$course_id.'/'.$study_id);
            }
        }
     }
     /**
     * @desc Showing list of all Admin user
     *
     */
    function list_admin() {
            get_plugins_in_template('datatable');
            $this->template->title = 'List Admins';
            $this->template->javascript->add(base_url() . 'assets/js/bootstrap-switch.min.js');
            $this->template->stylesheet->add(base_url() . 'assets/css/bootstrap-switch.min.css');
            $this->template->content->view('list_admins');
    
        // Publish the template
            $this->template->publish();
        }

    public function add_admin_user() {
        $this->template->title = 'AdminUser';
        $data['subheading'] = 'Add Customer Admin User';
        if ($this->input->post()) {
            $this->config->load("form_validation");
            $this->form_validation->set_rules($this->config->item("addAdminForm"));
            $params = $this->input->post();
            extract($params);
           if($this->session->userdata('logged_in')->user_type == 1){
                $this->form_validation->set_rules('course_has_users[]', 'organization', 'trim|required');
            }
            if($this->auth->email_check($email)){
                $this->form_validation->set_rules('email', 'Email', 'required|valid_email|callback_check_email');
                $this->form_validation->set_message('check_email','Email address already exist in system.');
            }
            if($this->auth->username_check($username)){
                $this->form_validation->set_rules('username', 'Username', 'required|valid_email|callback_check_username');
                $this->form_validation->set_message('check_username','Username already exist in system.');
            }
            if ($this->form_validation->run() != FALSE) {

                $first_name = (isset($first_name)) ? aes_256_encrypt(trim($first_name)) : FALSE;
                $last_name = (isset($last_name)) ? aes_256_encrypt(trim($last_name)) : FALSE;
                $username = (isset($username)) ? aes_256_encrypt(trim($username)) : FALSE;
                $salt = $this->store_salt ? $this->salt() : FALSE;
                
                // Users table.
                $user_data = array(
                    'first_name' => $first_name,
                    'last_name' => $last_name,
                    'username' => $username,
                    'gender'=> $gender,
                    'email' => aes_256_encrypt(trim($email)),
                    'salt' => $salt,
                    'user_type' => 2,
                    'created_at' => date('Y-m-d H:i:s'),
                );
                // // filter out any data passed that doesnt have a matching column in the users table
                // // and merge the set user data and the additional data
                $this->db->trans_start();
                $this->db->insert('users', $user_data);
                $id = $this->db->insert_id('users');
                $this->db->trans_complete();
                $status = 'error';
                $msg = 'Admin user cannot created successfully.';
                $log = "User[$id] account has not been created successfully.Try again";
                $this->session->set_flashdata('success', $msg);
                if ($this->db->trans_status() !== FALSE) {
                    $forgotten_password_status =  $this->auth->forgotten_password($email, FALSE, 2);
                    if ($forgotten_password_status['status'] == 'success') {
                        
                         if(!isset($course_has_users) && $this->session->userdata('logged_in')->user_type == 2){
                            $parent_user_id = $this->session->userdata('logged_in')->id;
                            $course_has_users = $this->course->get_user_has_organization($parent_user_id);
                            $this->course->insert_users_has_organization($course_has_users,$id,$parent_user_id);
                        } else {
                            $this->course->insert_users_has_organization($course_has_users,$id);
                        }
                        

                        $status = 'success';
                        $msg = 'A link to reset password of created admin has been sent to given email. Please check your email.';
                        $log = "User[$id] account has been created successfully, and a verification link has been sent to user's email address.";
                    }
                }
                generate_log($log);
                $this->session->set_flashdata($status, $msg);
                redirect('user/list-admin');
            }
        }
        
        $course_list = $this->organization->get_organizations();

        $data['course_list'] = $course_list['result'];

        $this->template->content->view('users/add', $data);
        // Publish the template
        $this->template->publish();
    }

    public function get_detail() {
        if ($this->input->post()) {
            $user_id = $this->input->post('user_id');
            $result = $this->user->get_detail($user_id);
            if (!empty($result)) {
                $data = array('status' => 'success', 'data' => $result);
            } else {
                $data = array('status' => 'error', 'msg' => "User not found");
            }
        } else {
            $data = array('status' => 'error', 'msg' => "User not found");
        }

        
        echo json_encode($data);
        exit;
	}
	
	/**
     * get participant details
     */
    public function participants_detail($user_id, $type = '', $sub_type = '')
    {
		if (isset($user_id) && $user_id) {
        $course_id = $this->auth->user_has_course($user_id);
		$study_id = user_has_study($user_id);

		$this->breadcrumbs->push('Study', 'study');
        $this->breadcrumbs->push('Participant List', 'user/list-users/'.$course_id.'/'.$study_id);
        $this->breadcrumbs->push('Participant Detail', 'participant');
        $data['breadcrumb'] = $this->breadcrumbs->show();
        $data['course_id'] = $course_id;
        $data['study_id'] = $study_id;
			// my code
			$user_info = $this->user->get_detail($user_id);
            if (empty($user_info) || $user_info['user_type'] != 3) {
                $this->session->set_flashdata('error', 'Invalid user');
                redirect('/dashboard');
            }
            if ($user_info['user_type'] == 3) {
                $data['details'] = $user_info;
                $data['type']['details'] = $user_info;
            }
			
			if(isset($type) && $type){
				switch ($type) {
					case "progress":
						$pdata['completed_class'] = $this->classes->get_completed_class($user_id);
						$pdata['meditation_minutes'] = $this->classes->get_meditation_minutes($user_id);
						$pdata['class_list'] = $this->classes->get_class_list($user_id);
						$current_class = $this->classes->get_current_class($user_id, $course_id);
						$pdata['current_class'] = $current_class['data'];
						$pdata['current_class_status'] = $current_class['msg'];
						$pdata['current_page']='';
						if(!empty($current_class['data'])){
							$pdata['current_page'] = $this->classes->get_current_page($user_id, $current_class['data']->class_id);
						}
						$data['type']['progress'] = $pdata;  
						break;
					case 'avaccess':
						$avdata = $this->classes->get_avaccess_data($user_id);
						$data['type']['avaccess'] = $avdata;
                        break;
                    case 'homework':
						if($sub_type == 'reading'){
							$data['type']['homework']['reading'] = $this->classes->get_reading_data($user_id);
						} elseif($sub_type == 'podcast'){
							$data['type']['homework']['podcast'] = $this->classes->get_exercise_data($user_id, 'podcast');
						}else {
							$data['type']['homework']['exercise'] = $this->classes->get_exercise_data($user_id, 'exercise');
						}
                        break;
					case 'meditation':
						$mddata = $this->classes->get_meditation_data($user_id);
						$data['type']['meditation'] = $mddata;
                        break;
                    
				}
			}

			if ($this->input->post()) {
				$params = $this->input->post();
				extract($params);
					$email = trim($email);
					$status = 'error';
					if(!$email){
						$msg = 'Email field is required.';
					} elseif($email == $user_info['email']){
						$msg = 'Email should be different from previous email.';
					} elseif($this->auth->email_check($email)){
						$status = 'error';
						$msg = 'Email address already exist in system.';
					} else {
						$this->db->trans_start();
						$this->db->update('users', array('email' => aes_256_encrypt(trim($email))),array('id' => $user_id));
						$this->db->trans_complete();
						if ($this->db->trans_status() !== FALSE) {
							$invitation_email_status =  $this->auth->invitation_email_status($email);
							if ($invitation_email_status['status'] == 'success') {                        
								$status = 'success';
								$msg = 'A link to invitation email using  new access code of created participant has been sent to given email. Please check your email.';
							}
						}
						$data['details']['email'] = $email;
					}
					$this->session->set_flashdata($status, $msg);
			}
        }
        $this->template->content->view('users/participant_details', $data);
        $this->template->publish();
    }

    public function edit_status($type='',$id='',$status='', $course_id='', $study_id='')
    {
        if ($this->session->userdata('logged_in') == FALSE) {
            redirect('auth');
        }
        if (isset($status) && isset($id)) {
            $info_array['user_id'] = $id;
            $info_array['status'] = $status;
            $info_array['type'] = $type;

            $result = $this->user->update_status($info_array);
            if($result){
                $log = "User ". $this->session->userdata['logged_in']->id ." updated status of participant ".$id;
                generate_log($log);
                $status = 'success'; $msg = 'Status updated';
            } else {
                $status = 'error'; $msg = 'Status not updated';
            }
            $this->session->set_flashdata($status, $msg);
            if($course_id && $study_id){
                redirect('/user/list-users/'. $course_id.'/'. $study_id);
            } else {
                redirect('/user/list-admin');
            }
        }
	}
	
	public function update_uniqueid() {
        echo json_encode($this->auth->update_profile($this->input->post()));
	}
	
	public function add_user($course_id='', $study_id='') {
		if(!$course_id && $study_id){
			$this->session->set_flashdata('error', "No study selected.");
			redirect('course');
		}
        $this->load->model('Setting_model', 'site_setting');
        $this->breadcrumbs->push('Study', 'study');
        $this->breadcrumbs->push('Participant List', 'user/list-users/'.$course_id);
        $this->breadcrumbs->push('Add Participant', 'add-user');
        $data['breadcrumb'] = $this->breadcrumbs->show();
        $this->template->title = 'Participant';
		$data['subheading'] = 'Add Participant';
        $data['course_id'] = $course_id;
		$data['study_id'] = $study_id;
		$access_code = $this->site_setting->get_settings(array('where' => array('study_id' => $study_id)));
		$data['access_code'] = (!empty($access_code['result'])) ? $access_code['result'][0]['value'] : '';
		if ($this->input->post()) {
            $this->config->load("form_validation");
            $this->form_validation->set_rules($this->config->item("addUserForm"));
            $params = $this->input->post();
            extract($params);
            if($this->auth->email_check($email)){
                $this->form_validation->set_rules('email', 'Email', 'required|valid_email|callback_check_email');
                $this->form_validation->set_message('check_email','Email address already exist in system.');
			}
			
			if($this->auth->register_token($user_access_code)){
                $this->form_validation->set_rules('user_access_code', 'Access Code', 'required|callback_check_register_token');
                $this->form_validation->set_message('check_register_token','User access code already exist in system.');
            }
            if ($this->form_validation->run() != FALSE) {
                
                // Users table.
                $user_data = array(
                    'unique_id' => trim($unique_id),
                    'email' => aes_256_encrypt(trim($email)),
                    'register_token' => $user_access_code,
                    'user_type' => 3,
                    'created_at' => date('Y-m-d H:i:s'),
                );
                // // filter out any data passed that doesnt have a matching column in the users table
                // // and merge the set user data and the additional data
                $this->db->trans_start();
                $this->db->insert('users', $user_data);
                $id = $this->db->insert_id('users');
                $this->course->insert_participant_has_course($course_id,$id, $study_id);
                $this->db->trans_complete();
                $status = 'error';
                $msg = 'Participant user cannot created successfully.';
                $log = "User[$id] account has not been created successfully.Try again";
                $this->session->set_flashdata('success', $msg);
                if ($this->db->trans_status() !== FALSE) {
					$invitation_email_status =  $this->auth->invitation_email_status($email);
                    if ($invitation_email_status['status'] == 'success') {                        
                        $status = 'success';
                        $msg = 'A link to invitation email using  new access code of created participant has been sent to given email. Please check your email.';
                        $log = "Participant[$id] account has been created successfully, and a invitation link has been sent to user's email address.";
                    }
                }
                generate_log($log);
                $this->session->set_flashdata($status, $msg);
                redirect('user/list-users/'.$course_id.'/'.$study_id);
            }
        }
      
        $this->template->content->view('users/add_participant', $data);
        // Publish the template
        $this->template->publish();
	}
	
	public function check_user_access_code(){
		echo json_encode($this->user->check_user_access_code($this->input->post()));
	}

	public function csv_file_participant_profile($course_id, $study_id){
        $this->load->library('zip');
		if($course_id){
			$params['where'] = array('users.user_type' => 3, 'uhc.courses_id' => $course_id, 'uhc.study_id' => $study_id);
		} 
        
        $result = $this->user->get_users($params, $is_csv=true);
		$separator = ',';
		$participant_csv = array(implode($separator, array('Unique ID', 'User Name','Email Address')));
		$progress_csv = array(implode($separator, array('Unique ID', 'Practice Minute','Consecutive days of practice','Completed Classes','Cumulative logins','Dashboard Entrances','Review Entrances', 'Practice Entrances', 'Community Entrances','Where you are in the course')));
		$avaccess_csv = array(implode($separator, array('Unique ID', 'Audio/Video Title','Access Time', 'Duration','# Started','# Completed')));
		$homework_exercise_csv = array(implode($separator, array('Unique ID', 'Category','Practice','Access Time','Duration','# Started','# Completed')));
		$homework_podcast_csv = array(implode($separator, array('Unique ID', 'Class','Title','Access Time','Duration')));
		$homework_reading_csv = array(implode($separator, array('Unique ID', 'Class','Title','Access Time','Duration')));
		$meditation_csv = array(implode($separator, array('Unique ID','Access Time','Duration')));
        $reflections = array(implode($separator, array('Unique ID','Post date and time','Question','Reflection','Reaction Button','Reply to reflection')));
		foreach ($result['result'] as $key => $user) {

			// participant detail csv
			$participant_csv[]= $user['unique_id'].','.$user['username'].','.$user['email'];

			// getting data for progress csv
			$user_info = $this->user->get_detail($user['id']);
            $dashboard_accessed_count = $this->user->get_dashboard_accessed_count($user['id']);
            $review_accessed_count = $this->user->get_review_accessed_count($user['id']);
            $practice_accessed_count = $this->user->get_practice_accessed_count($user['id']);
            $community_accessed_count = $this->user->get_community_accessed_count($user['id']);

            $userlogin_count = $this->classes->get_userlogin_count($user['id']);
			$consecutive_days = $user_info['consecutive_days'];
			$meditation_minutes = $this->classes->get_meditation_minutes($user['id']);
			$completed_class = $this->classes->get_completed_class($user['id']);
			$current_class = $this->classes->get_current_class($user['id'], $course_id);

			if(!empty($current_class['data'])){
				$current_page = $this->classes->get_current_page($user['id'], $current_class['data']->class_id);
			}

			$title = isset($current_class['data']->title) ? $current_class['data']->title.' :' : '';
			$class_title = isset($current_page->title) ? $current_page->title : 'N/A';


            //echo'<pre>';print($user['unique_id'].$class_title);
			$position = $title.$class_title;
            if ($user['unique_id'] > 0) {
			    $progress_csv[] = $user['unique_id'].','.$meditation_minutes.','.$consecutive_days.','.$completed_class.','.$userlogin_count.','.$dashboard_accessed_count.','.$review_accessed_count.','.$practice_accessed_count.','.$community_accessed_count.','.$position;
            }
			// getting data for progress csv

			// getting data for avaccess csv
			$avdata = $this->classes->get_avaccess_data($user['id']); 
			if(!empty($avdata)){
				foreach($avdata as $avkey => $avval){
					$avaccess_csv[] = $user["unique_id"].','.'"'.str_replace('"', "'", $avval["files_name"]).'"'.','.date("m/d/Y H:i", strtotime($avval["starts_at"])).','.gmdate("H:i:s", $avval["total_elapsed_time"]).','.$avval['started_av_count'].','.$avval['complete_av_count'];
				}
			}
			// getting data for avaccess csv
            
			// getting data for homework exercise csv
			$exercise = $this->classes->get_exercise_data($user['id'], 'exercise');
			if(!empty($exercise)){
				foreach($exercise as $exkey => $exval){
                    $practice_title = isset($exval['practice_title'][0]->practice_title) ? $exval['practice_title'][0]->practice_title : "";
                    if ($user['unique_id'] > 0) {
                    $homework_exercise_csv[] = $user["unique_id"].','.'"'.str_replace('"', "'", $exval["category_title"]).'"'.','.'"'.str_replace('"', "'", $practice_title).'"'.','.date('m/d/Y H:i:s', strtotime($exval['created_at'])).','.gmdate("H:i:s", $exval['total_elapsed_time']).','.$exval['started_excercise_count'].','.$exval['complete_excercise_count'];
                    }
                    
				}
			}
			// getting data for homework reading csv
			$reading = $this->classes->get_reading_data($user['id']);
			if(!empty($reading)){
				foreach($reading as $rdkey => $rdval){
                    if ($user['unique_id'] > 0) {
                        $homework_reading_csv[] = $user['unique_id'].','.'"'.str_replace('"', "'", $rdval['class_title']).'"'.','.'"'.str_replace('"', "'", $rdval['article_title']).'"'.','.(($rdval['start_time']) ? date('m/d/Y H:i:s', strtotime($rdval['start_time'])): '00:00:00').','.(($rdval['TotalTimeSpentInMinutes']) ? gmdate("H:i:s", $rdval['TotalTimeSpentInMinutes']*60) : '00:00');
                    }
				}
			}			
			// getting data for homework reading csv
			// getting data for meditation csv
			$mddata = $this->classes->get_meditation_data($user['id']);
			if(!empty($mddata)){
				foreach($mddata as $mdkey => $mdval){
                    if ($user['unique_id'] > 0) {
                        $meditation_csv[] = $user['unique_id'].','.date("m/d/Y H:i:s", strtotime($mdval['meditation_date'])).','.(($mdval['total_elapsed_time']) ? gmdate("H:i:s", $mdval['total_elapsed_time']) : '00:00');
                    }
				}
			}
            // getting data for meditation csv           
		}
        
        // getting data for reflection question csv           
        $reflection_question = $this->community->get_reflection_question_ans(array('course_id'=>$course_id,'study_id'=>$study_id ,'iDisplayLength' => -1, 'iDisplayStart'=> 0));
        if(isset($reflection_question['data'])){
            foreach ($reflection_question['data'] as $key => $reflections_details) {
            $res = $this->community->count_answer_status($reflections_details['answer_id']);
            $data['inspired'] = count($res['inspired']) ? "(Inspired)" : "";
            $data['understood'] = count($res['understood']) ? "(Understood)" : "";
            $data['grateful'] = count($res['grateful']) ? "(Grateful)" : "";

                $reply = $this->community->get_answer_reply($reflections_details['answer_id']);

                $reflections[] = implode($separator, array($reflections_details['unique_id'],date("m/d/Y H:i:s", strtotime($reflections_details['create_date'])),'"'.str_replace('"', "'", $reflections_details['question_text']).'"','"'.str_replace('"', "'", $reflections_details['answer']).'"'.','.$data['inspired'].''.$data['understood'].''.$data['grateful'].','.""));

                if (count($reply) > 0) {
                    foreach($reply as $rp){
                        $reflections[] = implode($separator, array($reflections_details['unique_id'],date("m/d/Y H:i:s", strtotime($reflections_details['create_date'])),"",'""'.','."".','.$rp['comment']));
                    }

                }
            }
        }

        $this->zip->add_data('participant-profile-' . date('Ymd') . '.csv', implode("\r\n", $participant_csv));
        $this->zip->add_data('progress-' . date('Ymd') . '.csv', implode("\r\n", $progress_csv));
        $this->zip->add_data('avaccess-' . date('Ymd') . '.csv', implode("\r\n", $avaccess_csv));
        $this->zip->add_data('homework-exercise-' . date('Ymd') . '.csv', implode("\r\n", $homework_exercise_csv));
        $this->zip->add_data('homework-reading-' . date('Ymd') . '.csv', implode("\r\n", $homework_reading_csv));
        $this->zip->add_data('meditation-' . date('Ymd') . '.csv', implode("\r\n", $meditation_csv));
        $this->zip->add_data('reflections-posts-' . date('Ymd') . '.csv', implode("\r\n", $reflections));
        $this->zip->download('data-export-'. date('Ymd'). '-' . uniqid() . '.zip');
	}

    public function edit_participant($course_id='', $study_id='', $user_id) {
        if(!$study_id){
            $this->session->set_flashdata('error', "No study selected.");
            redirect('course');
        }
        $this->breadcrumbs->push('Course', 'course');
        $this->breadcrumbs->push('Participant List', 'user/list-users/'.$study_id);
        $this->breadcrumbs->push('Add Participant', 'edit-user');
        $data['breadcrumb'] = $this->breadcrumbs->show();
        $this->template->title = 'Participant';
        $data['subheading'] = 'Edit Participant';
        $data['study_id'] = $study_id;
        $user_info = $this->user->get_detail($user_id);
        $data['user_info'] = $user_info;
        if ($this->input->post()) {
            $this->config->load("form_validation");
            $this->form_validation->set_rules($this->config->item("editUserForm"));
            $params = $this->input->post();
            extract($params);
            if($user_info['email'] != $email){
                if($this->auth->email_check($email)){
                    $this->form_validation->set_rules('email', 'Email', 'required|valid_email|callback_check_email');
                    $this->form_validation->set_message('check_email','Email address already exist in system.');
                }  
            }
            
            if($user_info['username'] != $username){
                if($this->auth->username_check($username)){
                    $this->form_validation->set_rules('username', 'Username', 'required|callback_check_username');
                    $this->form_validation->set_message('check_username','Username already exist in system.');
                }
            }

            if ($this->form_validation->run() != FALSE) {
                
                // Users table.
                $user_data = array(
                    'username' => aes_256_encrypt(trim($username)),
                    'email' => aes_256_encrypt(trim($email)),
                );
                // // filter out any data passed that doesnt have a matching column in the users table
                // // and merge the set user data and the additional data
                $this->db->trans_start();
                $this->db->update('users', $user_data, array('id' => $user_id));
                $this->db->trans_complete();
                $status = 'error';
                $msg = 'Participant user cannot updated successfully.';
                $log = "User[$id] account has not been updated successfully.Try again";
                $this->session->set_flashdata('success', $msg);
                if ($this->db->trans_status() !== FALSE) {
                        $status = 'success';
                        $msg = 'Participant user updated successfully.';
                        $log = "User[$user_id] account updated successfully.";
                }
                generate_log($log);
                $this->session->set_flashdata($status, $msg);
                redirect('user/list-users/'.$course_id.'/'.$study_id);
            }
        }
      
        $this->template->content->view('users/edit_participant', $data);
        // Publish the template
        $this->template->publish();
    }

    public function edit_user($user_id) {
        $this->breadcrumbs->push('Admin Users', 'user/list-admin');
        $this->breadcrumbs->push('Edit user', 'edit-user');
        $data['breadcrumb'] = $this->breadcrumbs->show();
        $this->template->title = 'Admin';
        $data['subheading'] = 'Edit User';
        $user_info = $this->user->get_detail($user_id);
        $data['user_info'] = $user_info;
        $course_has_users = $this->course->get_user_has_organization($user_id);
        if ($this->input->post()) {
            $this->config->load("form_validation");
            $this->form_validation->set_rules($this->config->item("addAdminForm"));
            $params = $this->input->post();
            extract($params);

            
            if ($this->form_validation->run() != FALSE) {
                if($this->session->userdata('logged_in')->user_type == 1){
                    $this->form_validation->set_rules('course_has_users[]', 'organization', 'trim|required');
                }
                if($user_info['email'] != $email){
                    if($this->auth->email_check($email)){
                        $this->form_validation->set_rules('email', 'Email', 'required|valid_email|callback_check_email');
                        $this->form_validation->set_message('check_email','Email address already exist in system.');
                    }
                }

                if($user_info['username'] != $username){
                    if($this->auth->username_check($username)){
                        $this->form_validation->set_rules('username', 'Username', 'required|callback_check_username');
                        $this->form_validation->set_message('check_username','Username already exist in system.');
                    }
                }
                $first_name = (isset($first_name)) ? aes_256_encrypt(trim($first_name)) : FALSE;
                $last_name = (isset($last_name)) ? aes_256_encrypt(trim($last_name)) : FALSE;
                
                // Users table.
                $user_data = array(
                    'first_name' => $first_name,
                    'last_name' => $last_name,
                    'email' => aes_256_encrypt(trim($email)),
                    'username' => aes_256_encrypt(trim($username)),
                );
                // // filter out any data passed that doesnt have a matching column in the users table
                // // and merge the set user data and the additional data
                $this->db->trans_start();
                $this->db->update('users', $user_data, array('id'=> $user_id));
                $this->db->trans_complete();
                $status = 'error';
                $msg = 'Admin user cannot updated successfully.';
                $log = "User[$user_id] account has not been updated successfully.Try again";
                $this->session->set_flashdata('success', $msg);
                if ($this->db->trans_status() !== FALSE) {
                    if($this->session->userdata('logged_in')->user_type == 1){
                        $this->db->delete('users_has_organizations', array('users_id' => $user_id));
                        $this->course->insert_users_has_organization($course_has_users,$user_id);    
                    }
                    
                    $status = 'success';
                    $msg = 'Admin user updated successfully';
                    $log = "User[$user_id] account has been updated successfully.";
                }
                generate_log($log);
                $this->session->set_flashdata($status, $msg);
                redirect('user/list-admin');
            }
        }
        $course_list = $this->organization->get_organizations();

        $data['course_list'] = $course_list['result'];
        $data['course_has_users'] = $course_has_users;
        $this->template->content->view('users/add', $data);
        // Publish the template
        $this->template->publish();
    }
}