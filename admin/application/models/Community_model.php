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
/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of Community_model
 *
 * @author Ideavate
 */
class Community_model extends CI_Model {

    var $tables = array();

    public function __construct() {
        parent::__construct();
        $this->tables = array('users' => 'users', 'class' => 'classes', 'answer_comments' => 'answer_comments', 'answer_status' => 'users_answer_status', 'reply_status' => 'users_answer_comments_status');
        $this->load->model('Classes_model');
    }
    
    
     /**
     * @desc Get all communities of course 
     * @param type $params
     * @return array
     */

    function get_communities($params = array()) {
        extract($params);
        $course_id = isset($course_id) ? $course_id : '';
        $users_id = isset($users_id) ? $users_id : '';
        $study_id = isset($study_id) ? $study_id : '';
        $page = isset($page) ? $page : 1;
        if ($course_id != '') {
            $info_array['where'] = array('classes.courses_id' => $course_id,'classes.is_active'=>1);
        }
        if($study_id){
            $join = array(
                array(
                    'table' => 'study_has_courses study_courses',
                    'on' => 'study_courses.classes_id = classes.id',
                    'type' => 'INNER'
                )
            );
        }
        $info_array['join'] = $join;
        $info_array['order_by'] = 'classes.position';
        $info_array['table'] = $this->tables['class'];
        $class_detail = $this->db_model->get_data($info_array);
        if (!empty($class_detail['result'])) {
            $community_detail = $class_detail['result'];
            foreach ($community_detail as $key => $val) {
                // Get current status of class
                $community_detail[$key]['status'] = 0;
                $status = $this->Classes_model->get_class_status(array('where' => array('classes_id' => $val['id'], 'users_id' => $users_id)));
                if (!empty($status)) {
                    $today_date = time();
					$end_date = strtotime($status['end_at']);
					if ($today_date > $end_date || ($today_date >= strtotime($status['start_at']) &&  $today_date <= $end_date)) {
						$community_detail[$key]['status'] = 1;
					}
                }

                $community_detail[$key]['list'] = array();

                // Get Page detail 
                $page_detail = $this->Classes_model->get_pages(array('where' => array('pages.classes_id' => $val['id'], 'page_type' => "'QUESTION'"), 'page' => $page));
                if (!empty($page_detail['result'])) {
                    $community_detail[$key]['list'] = $page_detail['result'];
                }
            }

            $data = array(
                'status' => 'success',
                'data' => $community_detail
            );
        } else {
            $data = array(
                'msg' => "Community_detail not found",
                'status' => 'error',
            );
        }

        return $data;
    }

    function get_community_new($params = array()){
        extract($params);
        $course_id = isset($course_id) ? $course_id : '';
        $users_id = isset($users_id) ? $users_id : '';
        $study_id = isset($study_id) ? $study_id : '';
        $page = isset($page) ? $page : 1;
        $number_of_pages = isset($number_of_pages) ? $number_of_pages : 0;
        $limit = isset($limit) ? $limit : 10;
        $mainRes = [];
        $res = $this->db->select('class.id as class_id, page.id as page_id, question.id, question.question_text,question.updated_at, 
            user_question.answer,
            user_question.updated_at as question_updated_at,
            user_question.id as answer_id,
            user.username, user.profile_picture, user.id as user_id, user.first_name, user.last_name, answer_user.id as answer_user_id,answer_user.username as answer_username,answer_user.first_name as answer_first_name,answer_user.participant_id as answer_participant_id,answer_user.unique_id as answer_unique_id, answer_user.last_name as answer_last_name,answer_user.profile_picture as answer_profile_picture,comments.id as comment_id,comment,comments.created_at as create_date,comments.updated_at as comment_update_date, comments.is_read, comments.parent_comment_id')
        ->join('pages page', 'page.classes_id = class.id', 'LEFT')
        ->join('reflection_question question', 'question.pages_id = page.id', 'LEFT')
        ->join('users_has_reflection_question user_question', 'user_question.reflection_question_id = question.id', 'LEFT')
        ->join('answer_comments comments', 'comments.answer_id = user_question.id', 'LEFT')
        ->join('users user', 'user.id = user_question.users_id', 'LEFT')
        ->join('users answer_user', 'answer_user.id = comments.users_id', 'LEFT')
        ->join('users_has_courses study_course', 'study_course.users_id = user.id', 'INNER')
        ->where(array('study_course.study_id' => $study_id, 'class.courses_id' => $course_id, 'class.is_active' => 1, 'page.page_type' => 'QUESTION'))
        ->order_by('comment_update_date DESC, question_updated_at DESC') //comment_update_date DESC, 
        ->get('classes class')
        ->result_array();

        $statusArray = $this->db->select('status,answer_id,COUNT(status) as total', FALSE)
        ->group_by('answer_id,status')
        ->get($this->tables['answer_status'])->result_array();

        $resQuest = array();
        foreach ($res as $key => $value) {
            $qid = $value['id'];
            $answerid = $value['answer_id'];
            
            if(!isset($resQuest[$qid]['discussion_list'])){
                $resQuest[$qid]['id']=$qid;
                $resQuest[$qid]['discussion_list']=array();
                $resQuest[$qid]['question_text']=$value['question_text'];
                $resQuest[$qid]['updated_at']=$value['updated_at'];
                $resQuest[$qid]['class_id']=$value['class_id'];
                $resQuest[$qid]['page_id']=$value['page_id'];
                $last_update_date = $value['question_updated_at'];    
            }
            
            if(isset($last_update_date) && strtotime($value['question_updated_at'])>strtotime($last_update_date)){
                $last_update_date = $value['question_updated_at'];
            }
            
            
            $disc_list=array();
            $disc_list['username']=isset($value['username']) && $value['username'] && ($users_id == $value['user_id']) ? 'Me' : ucfirst(aes_256_decrypt($value['username']));
            
            $disc_list['first_name']=isset($value['first_name']) && $value['first_name'] ? ucfirst(aes_256_decrypt($value['first_name'])) : "";
            $disc_list['last_name']=isset($value['last_name']) && $value['last_name']? ucfirst(aes_256_decrypt($value['last_name'])) : "";
            $disc_list['profile_picture']=isset($value['profile_picture']) && $value['profile_picture'] ? $value['profile_picture'] : "";
            $disc_list['answer']=$value['answer'];
            $disc_list['answer_id']=$value['answer_id'];
            $disc_list['updated_at']=$value['question_updated_at'];
            $disc_list['answer_id']=$value['answer_id'];
            $disc_list['user_id']=$value['user_id'];
            // get replies/comments by answer id.
            
            $post_reply=array();
            if($value['comment_id']){
                $post_reply['username']=isset($value['answer_username']) && $value['answer_username'] && ($users_id == $value['answer_user_id']) ? 'Me' : ucfirst(aes_256_decrypt($value['answer_username']));
                
                $post_reply['last_name'] = isset($value['answer_last_name']) && $value['answer_last_name'] ? ucfirst(aes_256_decrypt($value['answer_last_name'])) : "";

                $post_reply['first_name'] = isset($value['answer_first_name']) && $value['answer_first_name'] ? ucfirst(aes_256_decrypt($value['answer_first_name'])) : "";
                $post_reply['profile_picture'] = isset($value['answer_profile_picture']) && $value['answer_profile_picture'] ? $value['answer_profile_picture'] : "";
                $post_reply['comment_id']=$value['comment_id'];
                $post_reply['comment']=$value['comment'];
                $post_reply['updated_at']=$value['comment_update_date'];
                $post_reply['is_read']=$value['is_read'];
                $post_reply['parent_comment_id']=$value['parent_comment_id'];
                $post_reply['user_id']=$value['user_id'];
            }
           
            if(!isset($resQuest[$qid]['discussion_list'][$answerid])){
                $resQuest[$qid]['discussion_list'][$answerid] = $disc_list;
            }

            if(!isset($resQuest[$qid]['discussion_list'][$answerid]['post_rply']['data'])){
                $resQuest[$qid]['discussion_list'][$answerid]['post_rply']['data']=array();
            }

            foreach($statusArray as $ks=>$stat){
                if($stat['answer_id'] == $answerid){
                    $resQuest[$qid]['discussion_list'][$answerid]['post_rply'][strtolower($stat['status'])]=$stat['total'];
                    // unset by index so that next time the array will be decreased
                    unset($statusArray[$ks]);
                }           
            }
            
            if($value['comment_id']){
                if(is_null($value['parent_comment_id'])){
                    $prevReplies = array();
                    if(isset($resQuest[$qid]['discussion_list'][$answerid]['post_rply']['data'][$value['comment_id']]['replies'])){
                        $prevReplies = $resQuest[$qid]['discussion_list'][$answerid]['post_rply']['data'][$value['comment_id']]['replies'];
                    }
                    $resQuest[$qid]['discussion_list'][$answerid]['post_rply']['data'][$value['comment_id']] = $post_reply;
                    $resQuest[$qid]['discussion_list'][$answerid]['post_rply']['data'][$value['comment_id']]['replies']=$prevReplies;
                } else {
                    $resQuest[$qid]['discussion_list'][$answerid]['post_rply']['data'][$value['parent_comment_id']]['replies'][] = $post_reply;
                }

                if(isset($last_update_date) && strtotime($post_reply['updated_at'])>strtotime($last_update_date)){
                    $last_update_date = $post_reply['updated_at'];
                }
            }

            if((isset($resQuest[$qid]['last_update_date']) && strtotime($last_update_date) >strtotime($resQuest[$qid]['last_update_date'])) || !isset($resQuest[$qid]['last_update_date'])){
                $resQuest[$qid]['last_update_date']= $last_update_date;    
            }
            // get replies/comments by answer_id
        }
        $newRes = array();// array_values($resQuest);
        foreach($resQuest as $quest){
            $arrQuest=$quest;
            $disc_list=array_values($quest['discussion_list']);
            foreach($disc_list as $k=>$disc){
                $countStatus = array();
                $disc_list[$k]=$disc;
                $disc_list[$k]['post_rply']['data']=array_values($disc['post_rply']['data']);
                if(!isset($disc_list[$k]['post_rply']['inspired'])){
                    $disc_list[$k]['post_rply']['inspired']=0;
                }
                if(!isset($disc_list[$k]['post_rply']['understood'])){
                    $disc_list[$k]['post_rply']['understood']=0;
                }
                if(!isset($disc_list[$k]['post_rply']['grateful'])){
                    $disc_list[$k]['post_rply']['grateful']=0;
                }
            }
            $arrQuest['discussion_list']=$disc_list;
            $newRes[]=$arrQuest;
        }
        if(!empty($newRes)){
            usort($newRes, function ($item1, $item2) {
                if(isset($item1['last_update_date']) && isset($item2['last_update_date'])){
                    if (strtotime($item1['last_update_date']) == strtotime($item2['last_update_date'])) return 0;
                    return strtotime($item1['last_update_date']) < strtotime($item2['last_update_date']) ? 1 : -1;
                } else {
                    return -1;
                }
            });
            $number_of_pages = intval(count($newRes)/$limit)+1;
            $mainRes = array_values(array_slice($newRes, $page*$limit, $limit)); 
        }
        return array('res' => $mainRes, 'total_pages' => $number_of_pages);
    }
    
    
    /**
     * @desc Get discussion of community 
     * @param type $params
     * @return array
     */

    function get_reflection_question_ans($params = array()) {
        extract($params);
        $msg = 'Discussion not found';
        $status = 'error';
        $result = array();
        $question_id = isset($question_id) ? $question_id : '';
        $users_id = isset($users_id) ? $users_id : '';
        $course_id = isset($course_id) ? $course_id : '';
        $study_id = isset($study_id) ? $study_id : '';
        $start = 0;
        $search_array = FALSE;
		$limit = 20;
		$order_by = "answer.updated_at";
        $order = 'DESC';
        if($question_id){
			$info_array['where'] = "reflection_question_id='".$question_id."'";
        }
		$col_sort = array("answer.id", "answer.updated_at", "users.unique_id", "answer.answer");
        if (isset($params['sSearch']) && $params['sSearch'] != "") {
            $words = $params['sSearch'];
            $search_array = array();
            foreach ($col_sort as $key => $value) {
                $search_array[$value] = $words;
            }
            $info_array['like'] = $search_array;
		}
		if (isset($params['iSortCol_0'])) {
            $index = $params['iSortCol_0'];
            $order = $params['sSortDir_0'] === 'asc' ? 'asc' : 'desc';
            $order_by = $col_sort[$index];
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
        
		if($course_id && $study_id){
			$info_array['fields'] = 'users.id as user_id, users.unique_id,question.question_text,answer.id as answer_id,answer.answer,answer.reflection_question_id,answer.created_at as create_date';

			$info_array['join'] = array(
                array(
                    'table' => 'users_has_courses as study_course',
                    'on' => 'study_course.users_id = users.id',
                    'type' => 'INNER'
                ),                
				array(
					'table' => 'users_has_reflection_question as answer',
					'on' => 'answer.users_id = users.id',
					'type' => 'LEFT'
				),
				array(
					'table' => 'reflection_question as question',
					'on' => 'question.id = answer.reflection_question_id',
					'type' => 'INNER'
				),
                
			);

			$info_array['where'] = "study_course.study_id=".$study_id." AND study_course.courses_id=".$course_id;
		} else {
			$info_array['fields'] = 'users.id as user_id,username,first_name,participant_id,unique_id, last_name,profile_picture,reflection_question_id,answer.id as answer_id,answer,answer.created_at as create_date,answer.updated_at as update_date, answer.is_read, answer.is_removal';
			$info_array['join'] = array(
				array(
					'table' => 'users_has_reflection_question as answer',
					'on' => 'answer.users_id = users.id',
					'type' => 'LEFT'
				)
			);
		}
        $info_array['table'] = $this->tables['users'];
		$answer_detail = $this->db_model->get_data($info_array);
        $total = $answer_detail['total'];
        if (!empty($answer_detail['result'])) {
            $answer_detail = $answer_detail['result'];
            foreach ($answer_detail as $key => $val) {
                //$answer_detail[$key]['profile_picture'] = ($val['profile_picture'] != null) ? $val['profile_picture'] : "";
                // $date = date_create(($val['update_date'] != '') ? $val['update_date'] : $val['create_date']);
                // $answer_detail[$key]['username'] = ($users_id == $val['user_id']) ? 'Me' : ucfirst(aes_256_decrypt($val['username']));
                //  $answer_detail[$key]['first_name'] = ucfirst(aes_256_decrypt($val['first_name']));
                //  $answer_detail[$key]['last_name'] = ucfirst(aes_256_decrypt($val['last_name']));
                // $answer_detail[$key]['date'] = date_format($date, 'D M tS h:iA');
                ;
                $answer_detail[$key]['replies'] = $this->count_reply($val['answer_id']);
                $answer_detail[$key]['status'] = $this->get_comment_status($val['answer_id'],FALSE, $users_id);
            }
            $status = 'success';
            $result = $answer_detail;
            $msg = '';
        }
       
        return (array('msg' => $msg, 'status' => $status, 'data' => $result,'total' => $total));
    }


    function get_discussion($params = array()) {
        extract($params);
        $msg = 'Discussion not found';
        $status = 'error';
        $result = array();
        $question_id = isset($question_id) ? $question_id : '';
        $users_id = isset($users_id) ? $users_id : '';
        $course_id = isset($course_id) ? $course_id : '';
        $study_id = isset($study_id) ? $study_id : '';
        $start = 0;
        $search_array = FALSE;
        $limit = 20;
        $order_by = "answer.updated_at";
        $order = 'DESC';
        if($question_id){
            $info_array['where'] = "reflection_question_id='".$question_id."'";
        }
        $col_sort = array("answer.id", "answer.updated_at", "users.unique_id", "answer.answer");
        if (isset($params['sSearch']) && $params['sSearch'] != "") {
            $words = $params['sSearch'];
            $search_array = array();
            foreach ($col_sort as $key => $value) {
                $search_array[$value] = $words;
            }
            $info_array['like'] = $search_array;
        }
        if (isset($params['iSortCol_0'])) {
            $index = $params['iSortCol_0'];
            $order = $params['sSortDir_0'] === 'asc' ? 'asc' : 'desc';
            $order_by = $col_sort[$index];
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
        
        if($course_id && $study_id){
            $info_array['fields'] = 'class.id as class_id, users.id as user_id,username,first_name,participant_id,unique_id, last_name,profile_picture,question.question_text,reflection_question_id,answer.id as answer_id,answer,answer.created_at as create_date,answer.updated_at as update_date, answer.is_read, answer.is_removal, class.title as class_title';

            $info_array['join'] = array(
                array(
                    'table' => 'users_has_reflection_question as answer',
                    'on' => 'answer.users_id = users.id',
                    'type' => 'LEFT'
                ),
                array(
                    'table' => 'reflection_question as question',
                    'on' => 'question.id = answer.reflection_question_id',
                    'type' => 'LEFT'
                ),
                array(
                    'table' => 'classes as class',
                    'on' => 'class.id = question.pages_classes_id',
                    'type' => 'LEFT'
                ),
                array(
                    'table' => 'users_has_courses as study_course',
                    'on' => 'study_course.users_id = users.id',
                    'type' => 'INNER'
                ),
            );

            $info_array['where'] = "study_course.study_id=".$study_id." AND study_course.courses_id=".$course_id;
        } else {
            $info_array['fields'] = 'users.id as user_id,username,first_name,participant_id,unique_id, last_name,profile_picture,reflection_question_id,answer.id as answer_id,answer,answer.created_at as create_date,answer.updated_at as update_date, answer.is_read, answer.is_removal';
            $info_array['join'] = array(
                array(
                    'table' => 'users_has_reflection_question as answer',
                    'on' => 'answer.users_id = users.id',
                    'type' => 'LEFT'
                )
            );
        }
        $info_array['table'] = $this->tables['users'];
        $answer_detail = $this->db_model->get_data($info_array);
        $total = $answer_detail['total'];
        if (!empty($answer_detail['result'])) {
            $answer_detail = $answer_detail['result'];
            foreach ($answer_detail as $key => $val) {
                $answer_detail[$key]['profile_picture'] = ($val['profile_picture'] != null) ? $val['profile_picture'] : "";
                $date = date_create(($val['update_date'] != '') ? $val['update_date'] : $val['create_date']);
                $answer_detail[$key]['username'] = ($users_id == $val['user_id']) ? 'Me' : ucfirst(aes_256_decrypt($val['username']));
                 $answer_detail[$key]['first_name'] = ucfirst(aes_256_decrypt($val['first_name']));
                 $answer_detail[$key]['last_name'] = ucfirst(aes_256_decrypt($val['last_name']));
                $answer_detail[$key]['date'] = date_format($date, 'D M tS h:iA');
                ;
                $answer_detail[$key]['replies'] = $this->count_reply($val['answer_id']);
                $answer_detail[$key]['status'] = $this->get_comment_status($val['answer_id'],FALSE, $users_id);
            }
            $status = 'success';
            $result = $answer_detail;
            $msg = '';
        }
       
        return (array('msg' => $msg, 'status' => $status, 'data' => $result,'total' => $total));
    }

    /**
     * @desc Add reply for topic 
     * @param type $params
     * @return array
     */
    function add_reply($params = array()) {
        $status = "error";
        $msg = "Error while saving answer";
        $log = "";
        extract($params);
        $comment = isset($comment) ? $comment : NULL;
        $answer_id = isset($answer_id) ? $answer_id : NULL;
        $question_id = isset($question_id) ? $question_id : NULL;
        $users_id = isset($users_id) ? $users_id : FALSE;
        $parent_comment_id = isset($parent_comment_id) ? $parent_comment_id : NULL;
        $created_at = date('Y-m-d H:i:s');
        $updated_at = date('Y-m-d H:i:s');
        $this->db->insert($this->tables['answer_comments'], array('comment' => $comment, 'answer_id' => $answer_id, 'users_id' => $users_id, 'parent_comment_id' => $parent_comment_id, 'created_at' => $created_at));
        $status = 'success';
        $msg = 'Your comment has been successfully submitted.';
        $log = "User [$users_id] commented on answer[$answer_id] for question [$question_id]";
                generate_log($log);
        $this->db->trans_complete();
        return array('status' => $status, 'msg' => $msg);
    }
    
    
    /**
     * @desc Get all comment for topic 
     * @param type $params
     * @return array
    */

    function get_comment($params = array()) {
        extract($params);
        $msg = 'Discussion not found';
        $status = 'error';
        $result = array();
        $answer_id = isset($answer_id) ? $answer_id : '';
        $users_id = isset($users_id) ? $users_id : '';
        $info_array = array('where' => array('answer_id' => $answer_id, 'parent_comment_id' => NULL));
        $info_array['fields'] = 'users.id as user_id,username,first_name,participant_id,unique_id, last_name,profile_picture,comments.id as comment_id,comment,comments.created_at as create_date,comments.updated_at as update_date,answer_id, comments.is_read';
        $info_array['join'] = array(
            array(
                'table' => 'answer_comments as comments',
                'on' => 'comments.users_id = users.id',
                'type' => 'LEFT'
            )
        );
        $info_array['order_by'] = 'update_date';
        $info_array['order'] = 'DESC';
        $info_array['table'] = $this->tables['users'];
        $comments_detail = $this->db_model->get_data($info_array);
        if (!empty($comments_detail['result'])) {
            $comments_detail = $comments_detail['result'];
            foreach ($comments_detail as $key => $val) {
                $comments_detail[$key]['profile_picture'] = ($val['profile_picture'] != null) ? $val['profile_picture'] : '';
                $comments_detail[$key]['username'] = ($users_id == $val['user_id']) ? 'Me' : ucfirst(aes_256_decrypt($val['username']));
                $comments_detail[$key]['first_name'] = ucfirst(aes_256_decrypt($val['first_name']));
                $comments_detail[$key]['last_name'] =  ucfirst(aes_256_decrypt($val['last_name']));
                
                $date = date_create(($val['update_date'] != '') ? $val['update_date'] : $val['create_date']);
                $comments_detail[$key]['date'] = date_format($date, 'D M dS h:iA');
                $comments_detail[$key]['replies'] = $this->get_reply($val['comment_id'], $val['answer_id'], $users_id);
                $comments_detail[$key]['status'] = $this->get_comment_status($val['comment_id'], TRUE, $users_id);
            }
            $status = 'success';
            $result = $comments_detail;
            $msg = '';
        }
        return (array('msg' => $msg, 'status' => $status, 'data' => $result));
    }
    
    
    /**
     * @desc Get all reply on a comment 
     * @param type $params
     * @return array
    */

    public function get_answer_reply($answer_id){
        if($answer_id){
            $res = $this->db->select('*')
                ->where('answer_id', $answer_id)
                ->get('answer_comments')->result_array();
            return $res;
        }
    }

    public function get_reply($parent_id = FALSE, $answer_id = FALSE, $users_id = FALSE) {
        if ($parent_id && $answer_id) {
            $info_array = array('where' => array('answer_id' => $answer_id, 'parent_comment_id' => $parent_id));
            $info_array['fields'] = 'users.id as user_id,username,first_name,last_name,profile_picture,comments.id as reply_id,comment as reply,comments.created_at as reply_date,comments.updated_at as reply_update_date,answer_id as reply_on_answer, comments.is_read';
            $info_array['join'] = array(
                array(
                    'table' => 'answer_comments as comments',
                    'on' => 'comments.users_id = users.id',
                    'type' => 'LEFT'
                )
            );
            $info_array['table'] = $this->tables['users'];
            $info_array['order_by'] = 'reply_update_date';
            $info_array['order'] = 'DESC';
            $reply_detail = $this->db_model->get_data($info_array);
            if (!empty($reply_detail['result'])) {
                $reply_detail = $reply_detail['result'];
                foreach ($reply_detail as $key => $val) {
                    $reply_detail[$key]['profile_picture'] = ($val['profile_picture'] != null) ?  $val['profile_picture'] : '';
                    $reply_detail[$key]['username'] = ($users_id == $val['user_id']) ? 'Me' : ucfirst(aes_256_decrypt($val['username']));
                    $reply_detail[$key]['first_name'] = ucfirst(aes_256_decrypt($val['first_name']));
                    $reply_detail[$key]['last_name'] = ucfirst(aes_256_decrypt($val['last_name']));
                    $date = date_create(($val['reply_update_date'] != '') ? $val['reply_update_date'] : $val['reply_date']);
                    $reply_detail[$key]['date'] = date_format($date, 'D M dS h:iA');
                   
                }
                return $reply_detail;
            } else {
                return array();
            }
        }
    }

    /**
     * @desc Add status of a comment 
     * @param type $params
     * @return array
    */
    function add_comment_status($params = array()) {
        $status = "error";
        $msg = "Error while saving status";
        extract($params);
        $answer_status = isset($status) ? $status : NULL;
        $answer_id = isset($answer_id) ? $answer_id : NULL;
        $question_id = isset($question_id) ? $question_id : NULL;
        $users_id = isset($users_id) ? $users_id : FALSE;
        $created_at = date('Y-m-d H:i:s');
        $updated_at = date('Y-m-d H:i:s');
        $query = $this->db->where('answer_id', $answer_id)
                ->where('users_id', $users_id)
				->get($this->tables['answer_status']);
        if ($query->num_rows() < 1) {
            $this->db->insert($this->tables['answer_status'], array('status' => $answer_status, 'answer_id' => $answer_id, 'users_id' => $users_id, 'created_at' => $created_at));
            $status = 'success';
            $msg = 'Your status has been successfully submitted.';
            $log = "User [$users_id] update status $answer_status on answer [$answer_id] for question [$question_id]";
             
        } else {
            $status_detail = $query->row();
            $this->db->update($this->tables['answer_status'], array('status' => $answer_status), array('users_id' => $users_id, 'id' => $status_detail->id));
            $status = 'success';
            $msg = 'Status update successfully';
            $log = "User [$users_id] submit status $answer_status on answer [$answer_id] for question [$question_id]";
        }
        generate_log($log);
        $this->db->trans_complete();
        return array('status' => $status, 'msg' => $msg);
    }

    /**
     * @desc Add status of a reply 
     * @param type $params
     * @return array
    */
    function add_reply_status($params = array()) {
        $status = "error";
        $msg = "Error while saving status";
        extract($params);
        $status = isset($status) ? $status : NULL;
        $answer_comments_id = isset($answer_comments_id) ? $answer_comments_id : NULL;
        $users_id = isset($users_id) ? $users_id : FALSE;
        $created_at = date('Y-m-d H:i:s');
        $updated_at = date('Y-m-d H:i:s');
        $query = $this->db->where('answer_comments_id', $answer_comments_id)
                ->where('users_id', $users_id)
                ->get($this->tables['reply_status']);
        if ($query->num_rows() < 1) {
            $this->db->insert($this->tables['reply_status'], array('status' => $status, 'answer_comments_id' => $answer_comments_id, 'users_id' => $users_id, 'created_at' => $created_at));
            $status = 'success';
            $msg = 'Status submit successfully';
        } else {
            $status_detail = $query->row();
            $this->db->update($this->tables['reply_status'], array('status' => $status), array('users_id' => $users_id, 'id' => $status_detail->id));
            $status = 'success';
            $msg = 'Status update successfully';
        }
        $this->db->trans_complete();
        return array('status' => $status, 'msg' => $msg);
    }

    public function count_reply($answer_id) {
        return $this->db->where('answer_id', $answer_id)
                        ->count_all_results($this->tables['answer_comments']);
    }
    
    /**
     * @desc get status of a comment and reply
     * @param type $params
     * @return array
    */

    public function get_comment_status($answer_id, $is_reply = FALSE, $users_id) {
        $status = FALSE;
        if ($is_reply) {
            $query = $this->db->where(array('answer_comments_id' => $answer_id, 'users_id' => $users_id))
                    ->get($this->tables['reply_status']);
        } else {
            $query = $this->db->where(array('answer_id' => $answer_id, 'users_id' => $users_id))
                    ->get($this->tables['answer_status']);
		}
		if ($query->num_rows() > 0) {
			$status_detail = $query->row();
			$status = $status_detail->status;
		}
        return $status;
    }

    /**
     * @desc soft delete reflactive question
     * @param type $answer_id, $type(0/1)
     * @return array
    */
    public function delete_community($answer_id, $type){
        $status = 'error';
        $msg = 'Error while deleting community';
        $this->db->trans_start();
        $this->db->update('users_has_reflection_question', array('is_removal' => $type), array('id' => $answer_id));
        $this->db->trans_complete();
        if ($this->db->trans_status() !== FALSE) {
            $status = 'success';
            $msg = 'Community deleted successfully.';
        }
        return array('status' => $status, 'msg' => $msg);
    }

    /**
     * @desc read reflactive question
     * @param type $answer_id, $type(0/1)
     * @return array
    */
    public function read_community($answer_id, $type){
        $status = 'error';
        $msg = 'Error while read community';
        $this->db->trans_start();
        $this->db->update('users_has_reflection_question', array('is_read' => $type), array('id' => $answer_id));
        $this->db->trans_complete();
        if ($this->db->trans_status() !== FALSE) {
            $status = 'success';
            $msg = 'Community read successfully.';
        }
        return array('status' => $status, 'msg' => $msg);
	}
	
	/**
     * @desc get count of status on answer
     * @param type $answer_id
     * @return array
    */
	public function count_answer_status($answer_id){
		$res = $this->db->select('*')->where('answer_id', $answer_id)->get($this->tables['answer_status'])->result_array();
		$all_data=array();
		$all_data['inspired'] = array_values(array_filter($res,function($result){
			return $result['status']=='INSPIRED';
		}));
		$all_data['understood'] = array_values(array_filter($res,function($result){
			return $result['status']=='UNDERSTOOD';
		}));
		$all_data['grateful'] = array_values(array_filter($res,function($result){
			return $result['status']=='GRATEFUL';
		}));
		return $all_data;
	}

    //get discussion list for new community
    function get_discussion_list($params = array()) {
        extract($params);
        $msg = 'Discussion not found';
        $status = 'error';
        $result = array();
        $question_id = isset($question_id) ? $question_id : '';
        $users_id = isset($users_id) ? $users_id : '';
        $course_id = isset($course_id) ? $course_id : '';
        $app = isset($app) ? $app : false;
        $start = 0;
        $search_array = FALSE;
        $limit = 20;
        $order_by = "answer.updated_at";
        $order = 'DESC';
        if($question_id){
            $info_array['where'] = "reflection_question_id='".$question_id."'";
        }
        $col_sort = array("answer.id", "answer.updated_at", "users.unique_id", "answer.answer");
        if (isset($params['sSearch']) && $params['sSearch'] != "") {
            $words = $params['sSearch'];
            $search_array = array();
            foreach ($col_sort as $key => $value) {
                $search_array[$value] = $words;
            }
            $info_array['like'] = $search_array;
        }
        if (isset($params['iSortCol_0'])) {
            $index = $params['iSortCol_0'];
            $order = $params['sSortDir_0'] === 'asc' ? 'asc' : 'desc';
            $order_by = $col_sort[$index];
        }
        if (isset($params['iDisplayStart']) && $params['iDisplayLength'] != '-1') {
            $start = intval($params['iDisplayStart']);
            $limit = intval($params['iDisplayLength']);
        }
        $info_array['order_by'] = $order_by;
        $info_array['order'] = $order;
        $info_array['count'] = true;
        $info_array['debug'] = false;

        if (!(isset($params['iDisplayStart']) && $params['iDisplayLength'] == '-1') && !$app){
            
            $info_array['start'] = $start;
            $info_array['limit'] = $limit;
        }
        $info_array['fields'] = 'users.id as user_id,username,first_name,participant_id,unique_id, last_name,profile_picture,reflection_question_id,answer.id as answer_id,answer,answer.created_at as create_date,answer.updated_at as update_date, answer.is_read, answer.is_removal';
        $info_array['join'] = array(
            array(
                'table' => 'users_has_reflection_question as answer',
                'on' => 'answer.users_id = users.id',
                'type' => 'LEFT'
            )
        );
        $info_array['table'] = $this->tables['users'];
        $answer_detail = $this->db_model->get_data($info_array);
        $total = $answer_detail['total'];
        if (!empty($answer_detail['result'])) {
            $answer_detail = $answer_detail['result'];
            foreach ($answer_detail as $key => $val) {
                $answer_detail[$key]['profile_picture'] = ($val['profile_picture'] != null) ? $val['profile_picture'] : "";
                $date = date_create(($val['update_date'] != '') ? $val['update_date'] : $val['create_date']);
                $answer_detail[$key]['username'] = ($users_id == $val['user_id']) ? 'Me' : ucfirst(aes_256_decrypt($val['username']));
                 $answer_detail[$key]['first_name'] = ucfirst(aes_256_decrypt($val['first_name']));
                 $answer_detail[$key]['last_name'] = ucfirst(aes_256_decrypt($val['last_name']));
                $answer_detail[$key]['date'] = date_format($date, 'D M tS h:iA');
                ;
                $answer_detail[$key]['replies'] = $this->count_reply($val['answer_id']);
                $answer_detail[$key]['status'] = $this->get_comment_status($val['answer_id'],FALSE, $users_id);
            }
            $status = 'success';
            $result = $answer_detail;
            $msg = '';
        }
       
        return $result;
    }


    function get_notification($params = array()) {
        extract($params);
        $users_id = isset($users_id) ? $users_id : '';
        $result = $this->db->select('urq.id as post_id, urq.answer, urq.reflection_question_id as question_id, ac.id as comment_id, ac.users_id as commenter_id, ac.is_read, ac.updated_at')
                    ->join('answer_comments ac', 'ac.answer_id = urq.id')
                    ->join('users_has_courses as study_course', 'study_course.users_id = urq.users_id', 'INNER')
                    ->where(array('urq.users_id' => $users_id,'study_course.study_id' => $study_id))
                    ->order_by('urq.id', 'desc')
                    ->get('users_has_reflection_question urq')
                    ->result_array();
        $subArray = [];
        if(!empty($result)){
            $this->load->model('User_model', 'users');
            foreach ($result as $key => $value) {
                
                $users_detail = $this->users->get_detail($value['commenter_id'], true);
                if(!empty($users_detail)){
                    $result[$key]['commenter_name'] = $users_detail['username'];
                    $result[$key]['commenter_profile_picture'] = $users_detail['profile_picture'];
                    $result[$key]['parent_comment_id'] = '';
                    $result[$key]['sub_commenter_name'] = '';
                    $result[$key]['sub_comm_profilepicture'] = '';
                }

                $subRes = $this->db->select('id as comment_id, comment as answer, users_id as subcommenter_id, parent_comment_id, is_read, updated_at')
                        ->where('parent_comment_id', $value['comment_id'])
                        ->order_by('id', 'desc')
                        ->get('answer_comments')
                        ->result_array();
                if(!empty($subRes)){
                    foreach ($subRes as $skey => $svalue) {
                        $subRes[$skey]['question_id'] = $value['question_id'];
                        $subRes[$skey]['post_id'] = $value['post_id'];
                        $user = $this->users->get_detail($svalue['subcommenter_id'], true);
                        if(!empty($user)){
                            $subRes[$skey]['sub_commenter_name'] = $user['username'];
                            $subRes[$skey]['sub_comm_profilepicture'] = $user['profile_picture'];
                        }
                        $users_detail = $this->users->get_detail($value['commenter_id'], true);
                        if(!empty($users_detail)){
                            $subRes[$skey]['commenter_name'] = $users_detail['username'];
                            $subRes[$skey]['commenter_profile_picture'] = $users_detail['profile_picture'];
                        }
                         $subArray[] = $subRes[$skey];
                    }
                }
                $subArray[] = $result[$key];
            }
        }
        usort($subArray, function ($item1, $item2) {
            if (strtotime($item1['updated_at']) == strtotime($item2['updated_at'])) return 0;
            return strtotime($item1['updated_at']) < strtotime($item2['updated_at']) ? 1 : -1;
        });

        $countUnread = array_filter($subArray, function ($var) {
            return ($var['is_read'] == '0');
        }); 
        $data = array(
                'status' => 'success',
                'data' => $subArray,
                'unread_count' => !empty($countUnread) ? count($countUnread) : 0
        );

        return $data;
    }

    /**
     * @desc Add status of a reply 
     * @param type $params
     * @return array
    */
    function read_post($params = array()) {
        $status = "error";
        $msg = "Error while updating read status to post";
        extract($params);
        $comment_id = isset($comment_id) ? $comment_id : NULL;
        $users_id = isset($users_id) ? $users_id : FALSE;
        $created_at = date('Y-m-d H:i:s');

        $this->db->update('answer_comments', array('is_read' => '1', 'created_at' => $created_at), array('id' => $comment_id));
        $status = 'success';
        $msg = 'Status update successfully';
        $this->db->trans_complete();
        return array('status' => $status, 'msg' => $msg);
    }

    public function get_notification_count($users_id){
        $users_id = isset($users_id) ? $users_id : '';
        $data = array(
                    'status' => 'success',
                    'data' => array(),
                    'unread_count' => 0
            );
        if($users_id){
            $where = 'ac.notification_seen_at IS NULL AND urq.users_id='.$users_id;
            $result = $this->db->select('urq.id as answer_id, ac.id as comment_id, ac.users_id as commenter_id')
                    ->join('answer_comments ac', 'ac.answer_id = urq.id')
                    ->where($where)
                    ->order_by("urq.id desc, ac.id desc")
                    ->get('users_has_reflection_question urq')
                    ->result_array();
            $data = array(
                    'status' => 'success',
                    'data' => $result,
                    'unread_count' => !empty($result) ? count($result) : 0
            );

        }        
        return $data;
    }

    public function clear_notification_count($users_id){
        $status = 'success';
        $msg = "No notification found.";
        if($users_id){
            $notification = $this->get_notification_count($users_id);
            if(!empty($notification['data'])){
                foreach ($notification['data'] as $key => $value) {
                    $this->db->update('answer_comments', array('notification_seen_at' => date('Y-m-d H:i:s')), array('id' => $value['comment_id']));
                }
            $status = 'success';
            $msg = 'Notification clear successfully.';
            $log = "Notification clear for User[$users_id].";
            generate_log($log);
            }
            
            return array('status' => $status, 'msg' => $msg);
        }
    }

}