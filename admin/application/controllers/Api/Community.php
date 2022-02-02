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
 * @author          BrightOutcome
 */
class Community extends REST_Controller {

    var $tables = array();

    /**
     * @desc Allow header peramater, load class model
     */
    function __construct() {
        header('Access-Control-Allow-Origin: *');
        header("Access-Control-Allow-Methods: GET, POST, OPTIONS, PUT, DELETE");
        header("Access-Control-Allow-Headers: Authorization , Token");
        parent::__construct();
        $this->load->model('Community_model', 'community');
        $this->config->load('class_validation');
        $this->check_token();
        $this->tables = array('classes' => 'classes', 'users' => 'users');
    }

    /**
     * @desc Get all class with community detail accroding to course id
     * @param type $course_id
     * @return array
     */
    public function communities_get_old() {
        $course_id = $this->input->get('course_id');
        $page = $this->input->get('page') ? $this->input->get('page') : 0;
        $limit = 5;
        $users_id = $this->get_user();
        $number_of_pages = 0;
        if ($course_id != NULL) {
            $data = $this->community->get_communities(array('course_id' => $course_id, 'users_id' => $users_id, 'page' => $page));
            $res = [];
            $mainRes = [];
            $last_update_date = '';
            if(!empty($data['data'])){
                foreach ($data['data'] as $key => $value) {
                    if(isset($value['list']) && !empty($value['list'])){
                        foreach ($value['list'] as $lKey => $lValue) {
                            if(isset($lValue['page_data']) && !empty($lValue['page_data'])){
                                $discussion_list = $this->discussion($lValue['page_data']['id']);
                                $lValue['page_data']['discussion_list'] = $discussion_list;
                                $last_update_date = '';
                                if(!empty($lValue['page_data']['discussion_list'])){
                                    foreach ($discussion_list as $dKey => $dValue) {
                                        
                                        if($last_update_date == ''){
                                            $last_update_date = $dValue['update_date'];
                                        } else {
                                            if(strtotime($dValue['update_date']) > strtotime($last_update_date)){
                                                $last_update_date = $dValue['update_date'];
                                            }
                                        }  
                                        $reply = $this->reply($dValue['answer_id']);
                                        if(!empty($reply['data'])){
                                            foreach ($reply['data'] as $postkey => $postvalue) {
                                                $update_date = $postvalue['update_date'];
                                                if(strtotime($update_date) > strtotime($last_update_date)){
                                                    $last_update_date = $update_date;
                                                }
                                                if(!empty($postvalue['replies'])){
                                                    foreach ($postvalue['replies'] as $repkey => $repvalue) {
                                                        if(strtotime($repvalue['reply_update_date']) > strtotime($last_update_date)){
                                                            $last_update_date = $repvalue['reply_update_date'];
                                                        }
                                                    }
                                                }
                                            }
                                        }                                        
                                        $lValue['page_data']['discussion_list'][$dKey]['post_rply'] = $reply;
                                       
                                    }
                                    $lValue['page_data']['last_update_date'] = $last_update_date;
                                    array_push($res, $lValue['page_data']);
                                }
                                
                            }
                        }
                    }   
                }

                if(!empty($res)){
                    usort($res, function ($item1, $item2) {
                    	if(isset($item1['last_update_date']) && isset($item2['last_update_date'])){
                    		if (strtotime($item1['last_update_date']) == strtotime($item2['last_update_date'])) return 0;
                        	return strtotime($item1['last_update_date']) < strtotime($item2['last_update_date']) ? 1 : -1;
                    	} else {
                    		return -1;
                    	}
                    }); 
                    $number_of_pages = intval(count($res)/$limit)+1;
                    foreach (array_slice($res, $page*$limit, $limit) as $mainKey => $mainValue) {
                        array_push($mainRes, $mainValue);
                    }    
                }
                
                $data['data'] = $mainRes;
                $data['total_pages'] = $number_of_pages;

            }
        } else {
            $data = array('msg' => "Community_detail not found", 'status' => 'error');
        }
        $this->response($data, REST_Controller::HTTP_OK);
    }

    public function communities_get(){
        $course_id = $this->input->get('course_id');
        $page = $this->input->get('page') ? $this->input->get('page') : 0;
        //community_pager_limit
        $limit = $this->config->item("community_pager_limit");
        $users_id = $this->get_user();
        $study_id = user_has_study($users_id);
        $number_of_pages = 0;
        if ($course_id != NULL) {
            $res = $this->community->get_community_new(array('study_id' => $study_id,'course_id' => $course_id, 'users_id' => $users_id, 'page' => $page, 'number_of_pages' => $number_of_pages, 'limit' => $limit));
            $data['status'] = 'success';
            $data['data'] = $res['res'];
            $data['total_pages'] = $res['total_pages'];
        } else {
            $data = array('msg' => "Community_detail not found", 'status' => 'error');
        }

        $this->response($data, REST_Controller::HTTP_OK);
    }
    /**
     * @desc Get all answer of question
     * @param type $question_id
     * @return array
     */
    public function discussion($question_id) {
            $users_id = $this->get_user();
            $data = $this->community->get_discussion_list(array('question_id' => $question_id, 'users_id' => $users_id, 'app' => true));
            return $data;
    }
    
    /**
     * @desc Add reply on answer
     * @param type $array
     * @return array
     */
    public function reply_post() {
        $input = json_decode(file_get_contents('php://input'), true);
        if (!empty($input)) {
            $this->form_validation->set_data($input);
            $this->form_validation->set_rules($this->config->item("replyForm"));
            if ($this->form_validation->run() == FALSE) {
                $data = array(
                    'status' => 'error',
                    'data' => $this->form_validation->error_array()
                );
                $this->response($data, REST_Controller::HTTP_OK);
            } else {
                $input['users_id'] = $this->get_user();
                $res = $this->community->add_reply($input);
                $this->response($res, REST_Controller::HTTP_CREATED);
            }
        } else {
            $this->response(array('status' => 'error'), REST_Controller::HTTP_METHOD_NOT_ALLOWED);
        }
    }

    /**
     * @desc Add reply on answer
     * @param type $array
     * @return array
     */
    public function reply_get() {
        $answer_id = $this->input->get('answer_id');
        $users_id = $this->get_user();
        if ($answer_id != NULL) {
			$data = $this->community->get_comment(array('answer_id' => $answer_id, 'users_id' => $users_id));
			$res = $this->community->count_answer_status($answer_id);
			$data['inspired'] = count($res['inspired']);
			$data['understood'] = count($res['understood']);
			$data['grateful'] = count($res['grateful']);
        } else {
            $data = array('msg' => "Reply not found", 'status' => 'error');
        }
        $this->response($data, REST_Controller::HTTP_OK);
    }

    public function reply($answer_id) {
        $users_id = $this->get_user();
        if ($answer_id != NULL) {
            $data = $this->community->get_comment(array('answer_id' => $answer_id, 'users_id' => $users_id));
            $res = $this->community->count_answer_status($answer_id);
            $data['inspired'] = count($res['inspired']);
            $data['understood'] = count($res['understood']);
            $data['grateful'] = count($res['grateful']);
            return $data;
        }
        return false;
        
    }

    /**
     * @desc Add reply on answer
     * @param type $array
     * @return array
     */
    public function comment_status_post() {
        $input = json_decode(file_get_contents('php://input'), true);
        if (!empty($input)) {
            $this->form_validation->set_data($input);
            $this->form_validation->set_rules($this->config->item("commentStatusForm"));
            if ($this->form_validation->run() == FALSE) {
                $data = array(
                    'status' => 'error',
                    'data' => $this->form_validation->error_array()
                );
                $this->response($data, REST_Controller::HTTP_BAD_REQUEST);
            } else {
                $input['users_id'] = $this->get_user();
				$data = $this->community->add_comment_status($input);
				$res = $this->community->count_answer_status($input['answer_id']);
				$data['inspired'] = count($res['inspired']);
				$data['understood'] = count($res['understood']);
				$data['grateful'] = count($res['grateful']);
                $this->response($data, REST_Controller::HTTP_CREATED);
            }
        } else {
            $this->response(array('status' => 'error'), REST_Controller::HTTP_METHOD_NOT_ALLOWED);
        }
    }

    public function reply_status_post() {
        $input = json_decode(file_get_contents('php://input'), true);
        if (!empty($input)) {
            $this->form_validation->set_data($input);
            $this->form_validation->set_rules($this->config->item("replyStatusForm"));
            if ($this->form_validation->run() == FALSE) {
                $data = array(
                    'status' => 'error',
                    'data' => $this->form_validation->error_array()
                );
                $this->response($data, REST_Controller::HTTP_BAD_REQUEST);
            } else {
                $input['users_id'] = $this->get_user();
                $res = $this->community->add_reply_status($input);
                $this->response($res, REST_Controller::HTTP_CREATED);
            }
        } else {
            $this->response(array('status' => 'error'), REST_Controller::HTTP_METHOD_NOT_ALLOWED);
        }
    }

    /**
     * @desc Get all notification community detail accroding to course id
     * @param type $course_id
     * @return array
     */
    public function notification_get() {
        $page = $this->input->get('page') ? $this->input->get('page') : 0;
        $limit = 10;
        $users_id = $this->get_user();
        $study_id = user_has_study($users_id);
        $number_of_pages = 0;
        $mainRes = [];
        $data = $this->community->get_notification(array('users_id' => $users_id,'study_id' => $study_id));
        //print_r($data);
        if(!empty($data['data'])){
            $number_of_pages = intval(count($data['data'])/$limit)+1;
            foreach (array_slice($data['data'], $page*$limit, $limit) as $mainKey => $mainValue) {
                array_push($mainRes, $mainValue);
            }    
        }
        $data['data'] = $mainRes;
        $data['total_pages'] = $number_of_pages;
        $this->response($data, REST_Controller::HTTP_OK);
    }

    /**
     * @desc Get  community detail accroding to notifaiction question id
     * @param type $course_id
     * @return array
     */
    public function notification_community_get() {
        $course_id = $this->input->get('course_id');
        $question_id = $this->input->get('question_id');
        $post_id = $this->input->get('post_id');
        if (!empty($course_id)) {
            $users_id = $this->get_user();
            $study_id = user_has_study($users_id);
            $data = $this->community->get_communities(array('study_id' => $study_id,'course_id' => $course_id, 'users_id' => $users_id));
            $res = [];
            $mainRes = [];
            $last_update_date = '';
            if(!empty($data['data'])){
                foreach ($data['data'] as $key => $value) {
                    if(isset($value['list']) && !empty($value['list'])){
                        foreach ($value['list'] as $lKey => $lValue) {
                            if($lValue['page_data']['id'] == $question_id &&isset($lValue['page_data']) && !empty($lValue['page_data'])){
                                $discussion_list = $this->discussion($lValue['page_data']['id']);
                                $last_update_date = '';
                                if(!empty($discussion_list)){
                                    foreach ($discussion_list as $dKey => $dValue) {
                                         if((int)$dValue['answer_id'] == $post_id){
                                            if($last_update_date == ''){
                                                $last_update_date = $dValue['update_date'];
                                            } else {
                                                if(strtotime($dValue['update_date']) > strtotime($last_update_date)){
                                                    $last_update_date = $dValue['update_date'];
                                                }
                                            }  
                                            $reply = $this->reply($dValue['answer_id']);
                                            if(!empty($reply['data'])){
                                                foreach ($reply['data'] as $postkey => $postvalue) {
                                                    $update_date = $postvalue['update_date'];
                                                    if(strtotime($update_date) > strtotime($last_update_date)){
                                                        $last_update_date = $update_date;
                                                    }
                                                    if(!empty($postvalue['replies'])){
                                                        foreach ($postvalue['replies'] as $repkey => $repvalue) {
                                                            if(strtotime($repvalue['reply_update_date']) > strtotime($last_update_date)){
                                                                $last_update_date = $repvalue['reply_update_date'];
                                                            }
                                                        }
                                                    }
                                                }
                                            }                                        
                                            $lValue['page_data']['discussion_list'][$dKey] = $dValue;
                                            $lValue['page_data']['discussion_list'][$dKey]['post_rply'] = $reply;
                                        }
                                       
                                    }
                                    $lValue['page_data']['last_update_date'] = $last_update_date;
                                }
                                array_push($res, $lValue['page_data']);
                            }
                        }
                    }   
                }

                if(!empty($res)){
                    usort($res, function ($item1, $item2) {
                        if(isset($item1['last_update_date']) && isset($item2['last_update_date'])){
                    		if (strtotime($item1['last_update_date']) == strtotime($item2['last_update_date'])) return 0;
                        	return strtotime($item1['last_update_date']) < strtotime($item2['last_update_date']) ? 1 : -1;
                    	} else {
                    		return -1;
                    	}
                    }); 
 
                }
                
                $data['data'] = $res;

            }
        } else {
            $data = array('msg' => "Community_detail not found", 'status' => 'error');
        }
        $this->response($data, REST_Controller::HTTP_OK);
    }

    public function update_notification_post() {
        $input = json_decode(file_get_contents('php://input'), true);
        if (!empty($input)) {
                $input['users_id'] = $this->get_user();
                $res = $this->community->read_post($input);
                $this->response($res, REST_Controller::HTTP_CREATED);
        } else {
            $this->response(array('status' => 'error'), REST_Controller::HTTP_METHOD_NOT_ALLOWED);
        }
    }

}
