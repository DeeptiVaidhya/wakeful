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
 * Name:    User Model
 *
 * Requirements: PHP5 or above
 *
 */
class User_model extends CI_Model {

    var $tables = array();

    public function __construct() {
        parent::__construct();
        $this->tables = array('users' => 'users');
    }

    /**
     * @desc Get user detail by encrypted field.
     */
    public function get_encrypted_user_detail($field_name, $str,$is_case_insensitive=false) {
		//print_r($str) ;die;
        $info_array = array('table' => $this->tables['users']);
        $info_array['fields'] = 'id,unique_id,username as encrypt_username,username as username,first_name,last_name,email,is_active,is_authorized,user_type,profile_picture,gender,login_attempts,updated_at,last_login, participant_id, register_token';
		$userdata = $this->db_model->get_data($info_array);
        $user_detail = array();
        if (!empty($userdata['result'])) {
            $flagFound = false;
            foreach ($userdata['result'] as $key => $val) {
                if ($flagFound) {
                    break;
                }
                foreach ($field_name as $field) {
					if($field == 'id' && $val[$field] == $str){
						$val['email'] = aes_256_decrypt($val['email']);
                        $user_detail = $val;
                        $flagFound = true;
                        break;
					} else if (aes_256_decrypt($val[$field]) == $str || ($is_case_insensitive==true && strtolower(aes_256_decrypt($val[$field])) == strtolower($str))) {
                        $val['first_name'] = aes_256_decrypt($val['first_name']);
                        $val['last_name'] = aes_256_decrypt($val['last_name']);
                        $val['email'] = aes_256_decrypt($val['email']);
                        $val['username'] = aes_256_decrypt($val['username']);
                        $user_detail = $val;
                        $flagFound = true;
                        break;
                    }
                }
            }
        }
        return $user_detail;
    }

    /**
     * Get User Detail
     * @param  user_id
     * @return Array
     * */
    function get_users($params = array(), $is_csv=false) {
        $info_array = array('fields' => 'users.id,users.first_name,users.last_name,users.unique_id,users.email,users.is_active,users.is_authorized,users.username,users.forgotten_password_time, users.participant_id, users.mute_notification,users.password,users.salt, registered_at, created_at,users.user_type', 'where' => array());
        $where = $params['where'];
        $col_sort = array("id","unique_id", "username", "email", "is_active", "mute_notification");
		if(isset($params['where']['uhc.courses_id']) && $params['where']['uhc.courses_id'] && isset($params['where']['uhc.study_id']) && $params['where']['uhc.study_id']){
			$info_array['join'] = array(
				array(
					'table' => 'users_has_courses uhc',
					'on' => 'uhc.users_id = users.id',
					'type' => 'LEFT'
				)
			);
		} elseif($params['usertype'] == 2){
            $col_sort = array("id", "username", "first_name", "email", "title", "is_active");
            $info_array['fields'] = 'users.id,users.first_name,users.last_name,users.unique_id,users.email,users.is_active,users.is_authorized,users.username,users.forgotten_password_time, users.participant_id, users.mute_notification, users.password,users.salt, users.registered_at, users.created_at, users.user_type, organizations.title';
            $info_array['join'] = array(
                array(
                    'table' => 'users_has_organizations users_organizations',
                    'on' => 'users_organizations.users_id = users.id',
                    'type' => 'INNER'
                ),
                array(
                    'table' => 'organizations',
                    'on' => 'organizations.id = users_organizations.organizations_id',
                    'type' => 'LEFT'
                )
            );
            
            if(isset($params['where_in']) && !empty($params['where_in'])){
                $info_array['where_in'] = array('key' => 'users_organizations.organizations_id', 'val' => $params['where_in']);
            }
            $info_array['group_by'] = "id";
        }

        
        
        $order_by = "unique_id, username";
        $order = 'ASC';
        $start = 0;
        $search_array = FALSE;
		$data = array('result'=>FALSE, 'total'=>0);
		$limit = '';
		if(!$is_csv){
			$limit = $this->config->item('pager_limit');
		}
       
        if (isset($where)) {
            $info_array['where'] = $where;
        }
        if (isset($params['sSearch']) && $params['sSearch'] != "") {
            $words = $params['sSearch'];
            $search_array = array();
            foreach ($col_sort as $key => $value) {
                $search_array[$value] = $words;
            }
            $info_array['like'] = $search_array;
        }
        if (isset($params['iDisplayStart']) && $params['iDisplayLength'] != '-1') {
            $start = intval($params['iDisplayStart']);
            $limit = intval($params['iDisplayLength']);
        }

        $info_array['order_by'] = $order_by;
		$info_array['order'] = $order;
		$info_array['count'] = true;
		$info_array['debug'] = false;
        if (!(isset($params['iDisplayStart']) && $params['iDisplayLength'] == '-1')){
            $info_array['start'] = $start;
            $info_array['limit'] = $limit;
        }
        $info_array['table'] = $this->tables['users'];
        $result = $this->db_model->get_data($info_array);
        if (!empty($result['result'])) {
            foreach ($result['result'] as $key => $val) {
                $result['result'][$key]['email'] = aes_256_decrypt($val['email']);
                $result['result'][$key]['username'] = aes_256_decrypt($val['username']);
                $result['result'][$key]['first_name'] = ucwords(aes_256_decrypt($val['first_name']));
                $result['result'][$key]['last_name'] = ucwords(aes_256_decrypt($val['last_name']));
            }
		}
		
		$data['result']  = $result['result'];
		$data['total'] = $result['total'];
        if (isset($params['iSortCol_0'])) {
            if($params['iSortCol_0'] != 0 && $params['iSortCol_0'] != 7){
				$index = $params['iSortCol_0'];
				if($index < 4){
					$order = $params['sSortDir_0'] == 'asc'  ? 'asc' : 'desc';
				} else {
					$order = $params['sSortDir_0'] == 'asc'  ? 'desc' : 'asc';
				}
                $order_by = $col_sort[$index];
                array_multisort(array_column($data['result'], $order_by), $order == 'asc' ? SORT_ASC : SORT_DESC, $data['result']);
            }
        }else{
            $data['result'] = $this->array_orderby($data['result'], 'username', SORT_ASC, 'email', SORT_ASC);
        }
        
        return $data;
	}
	
	public function array_orderby()
    {
        $args = func_get_args();
        $data = array_shift($args);
        foreach ($args as $n => $field) {
            if (is_string($field)) {
                $tmp = array();
                foreach ($data as $key => $row) {
                    $tmp[$key] = $row[$field];
                }
                $args[$n] = $tmp;
            }
        }
        $args[] = &$data;
        call_user_func_array('array_multisort', $args);
        return array_pop($args);
    }

    /**
     * @desc get single user detail
     * @param type $params
     * @return array
     */
    public function get_detail($user_id, $is_app = false) {
        $info_array = array('where' => array('id' => $user_id), 'table' => $this->tables['users']);
        $info_array['fields'] = 'id,username,unique_id,first_name,last_name,email,is_active,is_authorized,profile_picture,consecutive_days,user_type,gender, participant_id, mute_notification, password, salt, registered_at, created_at';
        // getting sub page details like topics, testimonials
        $result = $this->db_model->get_data($info_array);
        if ($result['result']) {
            $is_authorized = array('No', 'Yes');
            $is_active = array('Deactive', 'Active');
            $userdetails = $result['result'][0];
            $userdetails['username'] = aes_256_decrypt($userdetails['username']);
            $userdetails['first_name'] = aes_256_decrypt($userdetails['first_name']);
            $userdetails['last_name'] = aes_256_decrypt($userdetails['last_name']);
            $userdetails['fullname'] = ucwords($userdetails['first_name'] . ' ' . $userdetails['last_name']);
            $userdetails['email'] = aes_256_decrypt($userdetails['email']);
            if($userdetails['is_active']){
                 $userdetails['is_authorized'] = $is_authorized[$userdetails['is_active']];
                 $userdetails['is_active'] = $is_active[$userdetails['is_active']];
            }
			$userdetails['consecutive_days'] = ($userdetails['consecutive_days'] != null) ? $userdetails['consecutive_days'] : 0;
			if(!$is_app){
				$userdetails['profile_picture'] = ($userdetails['profile_picture'] != null) ? base_url() . "assets/uploads/images/$userdetails[profile_picture]" : '';
			}else{
				$userdetails['profile_picture'] = ($userdetails['profile_picture'] != null) ? $userdetails['profile_picture'] : '';
			}
            
            return $userdetails;
        } else {
            return false;
        } 
	}
	
    public function update_status($params) {
        $update_data = array('is_active' => $params['status']);
        if($params['type'] == 'mute'){
            $update_data = array('mute_notification' => $params['status']);
        }
        
        $this->db->update($this->tables['users'], $update_data, array('id' => $params['user_id']));
        return $this->db->affected_rows();
	}
	
	public function check_user_access_code($params = array()) {
        extract($params);
        $status = 'error';
        $msg = 'This access code used already.';
        $log = 'This access code used already.';
        $user = array();

        $register_token = isset($register_token) ? $register_token : FALSE;
        if ($register_token) {
			
			$is_exist = $this->db->where('register_token', $register_token)->count_all_results('users') > 0;
            if (!$is_exist) {
                $status = 'success';
                $msg = 'Access code is new.';
            }
        }
        return array('status' => $status, 'msg' => $msg, 'userdetail' => $user);
    }

    function accessed_tabs($params = array()) {
        $resource_id=null;
        $status = "error";
        $msg = "Error while saving tracking";
        extract($params);
        $second_conversion = 1000;
        $input['spent_time'] = isset($spent_time) ? floor($spent_time / $second_conversion) : 0;
        $users_id = isset($users_id) ? $users_id : NULL;
        $resource_id = isset($resource_id) ? $resource_id : NULL;
        if ($resource_id > 0)  {
            $update_data['spent_time'] = $spent_time;
            $update_data['ends_at'] = date('Y-m-d H:i:s');
            $this->db->update('accessed_tabs', $update_data, array('id' => $resource_id)); 
            $resource_id = $resource_id;
        }else{
            $input['users_id'] = $users_id;
            $input['spent_time'] = '0';
            $input['status'] = $status;
            $input['starts_at'] = date('Y-m-d H:i:s');
            $input['ends_at'] = date('Y-m-d H:i:s');
            $input['created_at'] = date('Y-m-d H:i:s');
            $this->db->insert('accessed_tabs', $input);
            $resource_id = $this->db->insert_id();            
        }

        
        $this->db->trans_complete();
        if ($this->db->trans_status() !== false) {
            return array('status' => 'success','data'=>array('resource_id'=>$resource_id));
        }
        return array('status' => 'failed','data'=>array());
    }

    public function get_dashboard_accessed_count($users_id = false) {
        if ($users_id != '') {
            return $this->db->where('users_id', $users_id)
            ->where('status', 'DASHBOARD')
            ->count_all_results('accessed_tabs');
        }
        return 0;
    }

    public function get_review_accessed_count($users_id = false) {
        if ($users_id != '') {
            return $this->db->where('users_id', $users_id)
            ->where('status', 'REVIEW')
            ->count_all_results('accessed_tabs');
        }
        return 0;
    }


    public function get_community_accessed_count($users_id = false) {
        if ($users_id != '') {
            return $this->db->where('users_id', $users_id)
            ->where('status', 'COMMUNITY')
            ->count_all_results('accessed_tabs');
        }
        return 0;
    }

    public function get_practice_accessed_count($users_id = false) {
        if ($users_id != '') {
            return $this->db->where('users_id', $users_id)
            ->where('status', 'PRACTICE')
            ->count_all_results('accessed_tabs');
        }
        return 0;
    }



}
