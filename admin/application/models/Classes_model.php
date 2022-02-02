<?php
 /**
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

if (!defined('BASEPATH'))
    exit('No direct script access allowed');
/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of Classes_model
 *
 * @author Classes_model
 */
class Classes_model extends CI_Model {

    var $tables = array();
    var $page_containing_files = array('general', 'audio', 'video', 's_testimonial');

    public function __construct() {
        parent::__construct();
        $this->tables = array('courses' => 'courses', 'class' => 'classes', 'page' => 'pages', 'general' => 'general', 'audio' => 'practice_audio', 'question' => 'reflection_question', 'video' => 'educational_video','podcast' => 'practice_audio',
            'topic' => 'topics', 's_topic' => 'sub_topics', 'testimonial' => 'testimonials', 's_testimonial' => 'sub_testimonials', 'intention' => 'intention', 'files' => 'files', 'users_has_classes' => 'users_has_classes', 'page_activity' => 'users_page_activity', 'reflection_answer' => 'users_has_reflection_question', 'users' => 'users', 'users_has_intention' => 'users_has_intention', 'file_tracking' => 'users_audio_video', 'feedback_question_old' => 'feedback_question_old', 'feedback_answer' => 'users_has_feedback_question', 'reviews' => 'reviews', 'homework_exercises' => 'homework_exercises', 'reviews_has_files' => 'reviews_has_files', 'homework_has_files' => 'homework_exercises_has_files','numbered_general' => 'numbered_general','study_courses' => 'study_has_courses','avaccess'=>'audio_video_access');

        $this->load->model('user_model', 'user');
        $this->load->model('course_model', 'course');
    }

    /**
     * @desc Get percentage for class progress
     * @param type $params
     * @return array
     */
    function get_percentage($params = array()) {
        extract($params);
        $max = 0;
        $position = 0;
        $percentage = 0;
        $classes_id = (isset($classes_id)) ? $classes_id : 0;
        $users_id = (isset($users_id)) ? $users_id : 0;
        // Get max position
        $query = $this->db->select('max(position) as max')
                ->where(array('classes_id' => $classes_id))
                ->get($this->tables['page']);
        if ($query->num_rows() > 0) {
            $max = $query->row()->max;
        }
        // get user position
        $position_query = $this->db->select('page_activity.page_position as position,page_activity.status')
                ->join('users_page_activity as page_activity', 'page_activity.users_has_classes_id=users_has_classes.id')
                ->where(array('users_has_classes.classes_id' => $classes_id))
                ->where(array('users_has_classes.users_id' => $users_id))
                ->limit(1)
                ->order_by('page_activity.starts_at', 'DESC')
                ->get($this->tables['users_has_classes']);
        if ($position_query->num_rows() > 0) {
            $position_detail = $position_query->row();
            if ($position_detail->status == 'COMPLETED') {
                $position = ($position_detail->position) ? $position_detail->position : 1;
            } else {
                $position = $position_detail->position;
            }
        }
        $position = ($position < 0) ? 0 : $position;
        if($max == 0 && $position == 0){
            $percentage = 100;
        }
        else if($position) {
            $max = ($max==0) ? 1 :$max;
            $percentage = ($position * 100) / $max;
        }
        return ceil($percentage);
    }

    /**
     * @desc Add feedback given by user
     * @param type $params
     * @return array
     */
    function add_feedback($params = array()) {
        $status = "error";
        $msg = "Error while saving answer";
        $log = "";
        extract($params);
        $feedback_answers = isset($feedback_answers) ? $feedback_answers : FALSE;
        $question_id = isset($question_id) ? $question_id : FALSE;
        $users_id = isset($users_id) ? $users_id : FALSE;
        //$courses_id = isset($course_id) ? $course_id : FALSE;
        $created_at = date('Y-m-d H:i:s');
        $data = array();
        if (!empty($feedback_answers)) {
            foreach ($feedback_answers as $key => $value) {
                $data['answer'] = $value;
                $data['question_id'] = isset($question_id[$key]) ? $question_id[$key] : '';
                //$data['courses_id'] = isset($courses_id[$key]) ? $courses_id[$key] : '';
                $data['users_id'] = $users_id;
                //$query = $this->db->where('question_id', $data['question_id'])
                        // ->where('users_id', $users_id)
                        // ->get($this->tables['feedback_answer']);
                //if ($query->num_rows() > 0) {
                    //$feedback_detail = $query->row();
                //     $this->db->update($this->tables['feedback_answer'], array('answer' => $value), array('users_id' => $users_id, 'id' => $feedback_detail->id));
                // } else {
                    $data['created_at'] = $created_at;
                    $this->db->insert($this->tables['feedback_answer'], $data);
                //}
            }
        }
        $this->db->trans_complete();
        if ($this->db->trans_status() !== FALSE) {
            $log = "User [$users_id] submit feedback";
            generate_log($log);
            return array('status' => 'success', 'msg' => 'Answer submitted successfully');
        } else {
            return array('status' => $status, 'msg' => $msg);
        }
    }

    /**
     * @desc Getting feedback for course
     * @param type $params
     * @return array
     */
    function get_feedback($params = array()) {
        extract($params);
        $info_array = array('fields' => 'question,feedback_question_old.id as question_id');
        $info_array['table'] = $this->tables['feedback_question_old'];
        return $this->db_model->get_data($info_array);
    }

    /**
     * @desc Getting class status it is complete or start
     * @param type $params
     * @return array
     */
    function get_class_status($params = array()) {
        extract($params);
        $info_array = array('fields' => 'id,status,users_id, start_at, end_at');
        if (isset($where)) {
            $info_array['where'] = $where;
        }
        $info_array['table'] = $this->tables['users_has_classes'];
        $result = $this->db_model->get_data($info_array);
        if (!empty($result['result'])) {
            return $result['result'][0];
        } else {
            return array();
        }
    }

    /**
     * @desc Get current position of class
     * @param type $params
     * @return array
     */
    function get_position($params = array()) {
        $status = "error";
        $msg = "Error while getting position";
        $data = array();
        extract($params);
        $users_id = isset($users_id) ? $users_id : FALSE;
        $classes_id = isset($classes_id) ? $classes_id : FALSE;
        $query = $this->db->where('classes_id', $classes_id)
                ->where('users_id', $users_id)
                ->get($this->tables['users_has_classes']);
        if ($query->num_rows() > 0) {
            $status = 'success';
            $position_detail = $query->row();
            $data = $position_detail;
        } else {
            $status = 'error';
        }
        $this->db->trans_complete();
        return array('status' => $status, 'data' => $data);
    }

    /**
     * @desc Get file track detail for audio video
     * @param type $params
     * @return array
     */
    function get_file_tracking($params = array()) {
        $status = "error";
        $msg = "Error while getting track";
        $data = array();
        extract($params);
        $input['files_id'] = isset($files_id) ? $files_id : FALSE;
        $input['user_page_activity_id'] = isset($user_page_activity_id) ? $user_page_activity_id : 0;
        $query = $this->db->where('user_page_activity_id', $input['user_page_activity_id'])
                ->where('files_id', $files_id)
                ->get($this->tables['file_tracking']);
        if ($query->num_rows() > 0) {
            $status = 'success';
            $tracking_detail = $query->row();
            $data = $tracking_detail;
        } else {
            $status = 'error';
        }
        $this->db->trans_complete();
        return array('status' => $status, 'data' => $data);
    }

    /**
     * @desc Add file tracking data for audio video
     * @param type $params
     * @return array
     */
    function add_file_tracking($params = array()) {
        $status = "error";
        $msg = "Error while saving tracking";
        extract($params);
        $second_conversion = 1000;
        $input['current_time'] = isset($current_time) ? floor($current_time / $second_conversion) : 0;
        $input['left_time'] = isset($left_time) ? floor($left_time / $second_conversion) : 0;
        $input['total_time'] = isset($total_time) ? floor($total_time / $second_conversion) : 0;
        $input['files_id'] = isset($files_id) ? $files_id : FALSE;
        $last_avaccess_id = isset($last_avaccess_id) ? $last_avaccess_id : NULL;
        $input['user_page_activity_id'] = isset($user_page_activity_id) ? $user_page_activity_id : 0;
        $query = $this->db->where('user_page_activity_id', $input['user_page_activity_id'])
                ->get($this->tables['file_tracking']);
        if ($query->num_rows() > 0) {
            $status = 'success';
            $tracking_detail = $query->row();
            if ($tracking_detail->status != 'COMPLETED') {
                $input['status'] = isset($file_status) ? $file_status : 'STARTED';
                $input['total_elapsed_time'] = $input['current_time'];
            } else {
                $ct = $input['current_time'] - $tracking_detail->current_time;
                $ct = $ct >= 0 ? $ct : 0;
                $input['total_elapsed_time'] = $tracking_detail->total_elapsed_time + $ct;
            }
            $this->db->update($this->tables['file_tracking'], $input, array('id' => $tracking_detail->id));

            if ($last_avaccess_id != false) {
                 // update avaccess
                $data['time_spent'] = $input['current_time'];
                if (isset($file_status) && $file_status == 'COMPLETED') {                
                    $data['status'] = 1;   
                }            
                $data['users_audio_video_id'] = $tracking_detail->id;
                $data['completed_at'] = date('Y-m-d H:i:s');
                $this->db->update($this->tables['avaccess'], $data, array('id' => $last_avaccess_id));               
                $last_avaccess_id = $last_avaccess_id;
                //end                
            }else{
                $data['time_spent'] = $input['current_time'];
                $data['status'] = 0;                
                $data['users_audio_video_id'] = $tracking_detail->id;
                $data['started_at'] = date('Y-m-d H:i:s');
                $data['completed_at'] = date('Y-m-d H:i:s');
                $this->db->insert($this->tables['avaccess'], $data);                 
                $last_avaccess_id = $this->db->insert_id();
            }           
            
        } else {
            $input['status'] = 'STARTED';
            $insert_file = $this->db->insert($this->tables['file_tracking'], $input);
            if ($insert_file) {
                $data['time_spent'] = $input['current_time'];
                $data['users_audio_video_id'] = $this->db->insert_id();
                $data['status'] = 0; 
                $data['started_at'] = date('Y-m-d H:i:s');
                $this->db->insert($this->tables['avaccess'], $data);
                $last_avaccess_id = $this->db->insert_id();                                    
            } 

            $status = 'success';
        }
        $this->db->trans_complete();
        if ($this->db->trans_status() !== false) {
            return array('status' => 'success','data'=>array('last_avaccess_id'=>$last_avaccess_id));
        }
        return array('status' => 'failed','data'=>array());
    }

    /**
     * @desc Add intention answer and send email
     * @param type $params
     * @return array
     */
    function add_intention_answer($params = array()) {
        $status = "error";
        $msg = "Error while saving intention";
        extract($params);
        $intention_info_id = isset($intention_id) ? $intention_id : FALSE;
        $intention = isset($intention) ? $intention : FALSE;
        $users_id = isset($users_id) ? $users_id : FALSE;
        $current_date = date('Y-m-d H:i:s');
        $count = $this->db->where('intention_id', $intention_info_id)
                        ->where('users_id', $users_id)
                        ->count_all_results($this->tables['users_has_intention']) > 0;

        $query = $this->db->where('id', $intention_info_id)
                ->get($this->tables['intention']);
        $intention_detail = array();
        if ($query->num_rows() > 0) {
            $intention_detail = $query->row();
        }
        if ($count == 0) {
            $this->db->insert($this->tables['users_has_intention'], array('intention_id' => $intention_info_id, 'users_id' => $users_id, 'intention' => $intention, 'created_at' => $current_date));
            $status = 'success';
            $msg = 'Intention submitted successfully';
        } else {
            $this->db->update($this->tables['users_has_intention'], array('intention' => $intention), array('users_id' => $users_id, 'intention_id' => $intention_info_id));
            $status = 'success';
            $msg = 'Intention updated successfully';
        }

        $this->db->trans_complete();
        if ($this->db->trans_status() !== FALSE) {
            $userdata = $this->user->get_detail($users_id);
            if (!empty($userdata)) {
                $email = $userdata['email'];
                $first_name = $userdata['first_name'];
                 $content['message'] = sprintf($this->config->item('intention_message'), $intention);
                //$content['message'] = sprintf($this->config->item('intention_message'), ucfirst($first_name), $intention);
                $content['heading'] = sprintf($this->config->item('intention_subject'), ucfirst($intention_detail->title));
                $message = $this->load->view('email_template', $content, True);
                $subject = sprintf($this->config->item('intention_subject'), ucfirst($intention_detail->title));
                if (send_email($subject, $email, $message)) {
                    $status = "success";
                    $msg = 'Intention send in your email successfully.';
                } else {
                    $status = 'error';
                    $msg = 'Unable to make a request please try again later';
                }
            }
        } else {
            return FALSE;
        }
        return array('status' => $status, 'msg' => $msg);
    }

    /**
     * @desc Get answer of reflection question
     * @param type $type
     * @param type $id
     * @param type $users_id
     * @return array
     */
    function get_answer($type, $id, $users_id) {
        $status = "error";
        $msg = "Error while saving answer";
        $result = array();
        $id = isset($id) ? $id : FALSE;
        $users_id = isset($users_id) ? $users_id : FALSE;
        if ($id && $users_id) {
            if ($type == 'question') {
                $where = array('reflection_question_id' => $id);
                $table = $this->tables['reflection_answer'];
            } else {
                $where = array('intention_id' => $id);
                $table = $this->tables['users_has_intention'];
            }
            $where['users_id'] = $users_id;
            $query = $this->db->select('*')->where($where)
                    ->get($table);
            if ($query->num_rows() > 0) {
                $result = $query->row();
            }
        }
        return $result;
    }

    /**
     * @desc Add reflection answer for a question
     * @param type $params
     * @return array
     */
    function add_reflection_answer($params = array()) {
        $status = "error";
        $msg = "Error while saving answer";
        extract($params);
        $reflection_question_id = isset($question_id) ? $question_id : FALSE;
        $answer = isset($answer) ? $answer : FALSE;
        $users_id = isset($users_id) ? $users_id : FALSE;
        $current_date = date('Y-m-d H:i:s');
        $count = $this->db->where('reflection_question_id', $reflection_question_id)
                        ->where('users_id', $users_id)
                        ->count_all_results($this->tables['reflection_answer']) > 0;
        if ($count == 0) {
            $this->db->insert($this->tables['reflection_answer'], array('reflection_question_id' => $reflection_question_id, 'users_id' => $users_id, 'answer' => $answer, 'created_at' => $current_date));
            $status = 'success';
            $msg = 'Answer submit successfully';
        } else {
            $this->db->update($this->tables['reflection_answer'], array('answer' => $answer), array('users_id' => $users_id, 'reflection_question_id' => $reflection_question_id));
            $status = 'success';
            $msg = 'Answer updated successfully';
        }

        $this->db->trans_complete();
        return array('status' => $status, 'msg' => $msg);
    }

    /**
     * @desc Get all meditation minutes for class
     * @param type $users_id
     * @return array
     */
    function get_meditation_minutes($users_id) {
        $users_id = isset($users_id) ? $users_id : FALSE;
        $class_minutes = $this->get_class_meditation_minutes($users_id);
        $review_minutes = $this->get_review_meditation_minutes($users_id);
        $exercise_minutes = $this->get_exercise_meditation_minutes($users_id);
        return ceil($class_minutes + $review_minutes + $exercise_minutes);
    }

    /**
     * @desc Get all meditation minutes for audio and video of class
     * @param type $users_id
     * @return array
     */
    function get_class_meditation_minutes($users_id) {
        $users_id = isset($users_id) ? $users_id : FALSE;
        $query = $this->db->select('sum(ua.`total_elapsed_time`) as meditation_minutes')
                ->join('users_page_activity', 'users_page_activity.users_has_classes_id = users_has_classes.id', 'LEFT')
                ->join('users_audio_video as ua', 'ua.user_page_activity_id = users_page_activity.id', 'INNER')
                ->where('users_id', $users_id)
                ->get($this->tables['users_has_classes']);
        if ($query->num_rows() > 0) {
            $user_has_class = $query->row();
            return ($user_has_class->meditation_minutes != null) ? ($user_has_class->meditation_minutes / 60) : 0;
        }
    }

    /**
     * @desc Get all meditation minutes for audio and video of review
     * @param type $users_id
     * @return array
     */
    function get_review_meditation_minutes($users_id) {
        $users_id = isset($users_id) ? $users_id : FALSE;
        $query = $this->db->select('sum(`total_elapsed_time`) as meditation_minutes')
                ->where('users_id', $users_id)
                ->get('users_has_reviews_has_files');
        if ($query->num_rows() > 0) {
            $review_minutes = $query->row();
            return ($review_minutes->meditation_minutes != null) ? ($review_minutes->meditation_minutes / 60) : 0;
        }
    }

    /**
     * @desc Get all meditation minutes for audio and video of exercise
     * @param type $users_id
     * @return array
     */
    function get_exercise_meditation_minutes($users_id) {
        $users_id = isset($users_id) ? $users_id : FALSE;
        $query = $this->db->select('sum(`total_elapsed_time`) as meditation_minutes')
                ->where('users_id', $users_id)
                ->get('users_has_exercises_has_files');
        if ($query->num_rows() > 0) {
            $exercise_minutes = $query->row();
            return ($exercise_minutes->meditation_minutes != null) ? ($exercise_minutes->meditation_minutes / 60) : 0;
        }
    }

    /**
     * @desc Get all completed class of user
     * @param type $users_id
     * @return array
     */
    function get_completed_class($users_id) {
        $users_id = isset($users_id) ? $users_id : FALSE;
        return $this->db->where('users_id', $users_id)
                        ->where('status', 'COMPLETED')
                        ->count_all_results($this->tables['users_has_classes']);
    }
    
    public function get_started_completed_class($course_id, $users_id, $status=''){
        $todaysDate = date('Y-m-d H:i:s');

        $this->db->select('classes.id as class_id, title, end_at, status, users_has_classes.id as user_class_id, users_has_classes.week_number');
        $this->db->where(array('classes.courses_id' => $course_id,'classes.is_active' => '1','users_id'=> $users_id));
        if($status){
            $this->db->where(array('users_has_classes.status'=> $status));
        }

        $testingFlag=$this->config->item('CLASS_ENABLE_FOR_ALL_WEEKS');
        if(!$testingFlag){
            $this->db->where('users_has_classes.start_at <= "'.$todaysDate.'" ')
                    ->where('(users_has_classes.end_at '.($status=='COMPLETED' ? ' IS NOT NULL AND ' : 'IS NULL OR ').' users_has_classes.end_at >= "'.$todaysDate.'")');
        }        

        $class_detail = $this->db->join('users_has_classes', 'users_has_classes.classes_id=classes.id', 'LEFT')
                ->order_by('users_has_classes.week_number', 'asc')->limit(1,0)->get($this->tables['class'])->row();
        if(!empty($class_detail) && isset($class_detail->user_class_id) && $class_detail->status == 'STARTED'){
            $res = $this->db->select('starts_at')
                    ->where('users_has_classes_id', $class_detail->user_class_id)
                    ->get('users_page_activity'); 
            if($res->num_rows() > 0 ){
                $class_detail->status = 'INPROGRESS';
            }
        }
        return $class_detail;
    }

    /**
     * @desc Get current class of user
     * @param type $users_id
     * @return array
     */
    function get_current_class($users_id, $course_id) {
        $class_detail = array();
        $status = "error";
        $msg = "Error while getting class";
        $data = array('status'=>'error','msg'=>'');

        $users_id = isset($users_id) ? $users_id : FALSE;
        
        if(!$users_id){
            return array('status'=>'error','msg'=>'Invalid user');
        }

        $class_detail = $this->get_started_completed_class($course_id, $users_id, 'STARTED');
        if(!isset($class_detail->title)) { 
            // there might be possibility for class completion
            $class_detail = $this->get_started_completed_class($course_id, $users_id,'COMPLETED');
        }
        if (!empty($class_detail)) {
            $data['status'] = 'success';
        } else {
            $data['status'] = 'NO_MORE_CLASS';
            $data['msg'] = 'All classes are completed in this course.';
        }
        $data['data'] = $class_detail;
        return $data;
    }

    /**
     * @desc Get current page of user
     * @param type $users_id
     * @return array
     */
    function get_current_page($users_id, $class_id='') {
        $status = "error";
        $msg = "Error while getting class";
        $data = array();
        $page_detail = array();
        $users_id = isset($users_id) ? $users_id : FALSE;
        $query = $this->db->select('pages.title,pages.id,pages.position')
                ->join('users_page_activity as up', 'up.users_has_classes_id=users_has_classes.id', 'LEFT')
                ->join('pages', 'pages.id=up.pages_id', 'LEFT')
                ->where(array('users_id' => $users_id, 'users_has_classes.classes_id' => $class_id))
                ->where('up.status', 'STARTED')
                ->get($this->tables['users_has_classes']);
        if ($query->num_rows() > 0) {
            $page_detail = $query->row();
        } else {
            $query = $this->db->select('pages.title,pages.id')
                    ->where('classes.id', $class_id)
                    ->join('pages', 'pages.classes_id=classes.id', 'LEFT')
                    ->limit(1)
                    ->get($this->tables['class']);
            if ($query->num_rows() > 0) {
                $page_detail = $query->row();
            }
        }
        return $page_detail;
    }

    /**
     * @desc Add page activity of running class
     * @param type $params
     * @return array
     */
    function page_activity($params = array()) {
        extract($params);
        $classes_id = isset($classes_id) ? $classes_id : FALSE;
        $pages_id = isset($pages_id) ? $pages_id : FALSE;
        $users_has_classes_id = isset($users_has_classes_id) ? $users_has_classes_id : FALSE;
        $users_id = isset($users_id) ? $users_id : FALSE;
        $current_date = date('Y-m-d H:i:s');
        $query = $this->db->where('pages_id', $pages_id)
                ->where('users_has_classes_id', $users_has_classes_id)
                ->get($this->tables['page_activity']);
        if ($query->num_rows() > 0) {
            $activity_detail = $query->row();
            $activity_id = $activity_detail->id;
        } else {
            $this->db->trans_start();
            $this->db->insert($this->tables['page_activity'], array('pages_id' => $pages_id, 'starts_at' => $current_date, 'users_has_classes_id' => $users_has_classes_id, 'page_position' => $page_position));
            $activity_id = $this->db->insert_id();
            if ($page_position > 0) {
                $this->db->update($this->tables['page_activity'], array('status' => 'COMPLETED', 'ends_at' => $current_date), array('users_has_classes_id' => $users_has_classes_id, 'page_position' => $page_position - 1));
            }
        }

        $this->db->trans_complete();
        if ($this->db->trans_status() !== FALSE) {
            return $activity_id;
        } else {
            return FALSE;
        }
    }

    /**
     * @desc Add class activity of running class
     * @param type $params
     * @return array
     */
    function user_has_pages($params = array()) {
        $status = 'error';
        $msg = 'Class is already started';
        $data = array();
        extract($params);
        $classes_id = isset($class_id) ? $class_id : FALSE;
        $users_id = isset($users_id) ? $users_id : FALSE;
        $position = isset($position) ? $position : 0;
       
        $query = $this->db->where('classes_id', $classes_id)
                ->where('users_id', $users_id)
                ->get($this->tables['users_has_classes']);
        // load model to get user email using userid
        $users = $this->user->get_detail($users_id);
        $email = $users['email'];
        $count = $query->num_rows();
        $redirectTo = '';
        $startAt = '';
        $mail_send = false;
        if ($count > 0) {
            $user_has_class = $query->row();
            $users_has_classes_id = $user_has_class->id;
        }
        $this->db->trans_start();
        if ($classes_id) {
            $page_detail = $this->get_pages(array('where' => array('pages.classes_id' => $classes_id, 'position >=' => $position), 'users_id' => $users_id));
            if (!empty($page_detail['result'])) {
                if ($count != 0) {
                    $this->db->update($this->tables['users_has_classes'], array('current_page_position' => $position), array('id' => $users_has_classes_id, 'status!=' => 'COMPLETED'));
                    $status = 'success';
                }

                $next_page_details = $this->get_pages(array('where' => array('pages.classes_id' => $classes_id, 'position >=' => ($position+1)), 'users_id' => $users_id));
                $page_detail['result'][0]['next_page_details'] = isset($next_page_details['result']['0']['position']) ? true : false;
                $page_detail['result'][0]['next_page'] = $this->get_pages(array('where' => array('pages.classes_id' => $classes_id, 'position >=' => ($position+1)), 'users_id' => $users_id));


                $last_page_id = last_page_id($classes_id); 
                $activity['pages_id'] = $page_detail['result'][0]['id'];
                $activity['users_has_classes_id'] = $users_has_classes_id;
                $activity['page_position'] = $position;
                $activity_id = $this->page_activity($activity);
                $page_detail['result'][0]['users_has_classes_id'] = $users_has_classes_id;
                $page_detail['result'][0]['page_activity_id'] = $activity_id;
                $page_detail['result'][0]['percentage'] = $this->get_percentage(array('classes_id' => $classes_id, 'users_id' => $users_id));
                $page_detail['result'][0]['last_page'] = !is_null(last_page_position($classes_id)) && $page_detail['result'][0]['id']== last_page_position($classes_id) ? true : false;

                $page_detail['result'][0]['last_page_id'] = $last_page_id;
                $page_detail['result'][0]['welcome_class_position'] = $this->get_class_position($classes_id);

                $msg = 'Class started successfully';
                $log = "User [$users_id] accessing " . $page_detail['result'][0]['title'] . " page in class [$classes_id]";
                $data = $page_detail['result'][0];
            } else {
                $page_detail = $this->get_pages(array('where' => array('pages.classes_id' => $classes_id), 'users_id' => $users_id));
                if(!empty($page_detail['result'])){
                    if (isset($user_has_class) && $user_has_class->status != 'COMPLETED') {
                        $this->db->update($this->tables['users_has_classes'], array('status' => 'COMPLETED', 'current_page_position' => 0), array('id' => $users_has_classes_id));

                        $this->db->update($this->tables['page_activity'], array('status' => 'COMPLETED', 'ends_at' => date('Y-m-d H:i:s')), array('users_has_classes_id' => $users_has_classes_id, 'page_position' => $position - 1, 'status!=' => 'COMPLETED'));
                    }

                    // check if this class has homework in it ?
                    $hw_where = array('classes_id'=>$classes_id, 'title!=' => NULL);
                    if($this->db->where($hw_where)->get($this->tables['homework_exercises'])->num_rows() || $this->db->where($hw_where)->get('homework_podcasts')->num_rows() || $this->db->where($hw_where)->get('homework_readings')->num_rows())
                    {
                        $redirectTo = 'homework';
                    } else {
                        // check start at for current and next class is same 
                        if(isset($users_has_classes_id)){
                            $next_result = $this->db->query("select * from users_has_classes where id = (select min(id) from users_has_classes where id >".$users_has_classes_id.")")->row();
                            if(!empty($next_result)){
                                if(strtotime($user_has_class->start_at) == strtotime($next_result->start_at)){
                                    $redirectTo = 'next_class';

                                    // start week from today date and entered in users_has_classes table
                                    $course_id = get_course_id_by_class($user_has_class->classes_id);
                                    $study_id = user_has_study($users_id);
                                    $classes = $this->get_classes(array('where' => array('course.id' => $course_id,'is_active'=>1,'study_courses.study_id'=>$study_id), 'study_id'=> $study_id));
                                    $start_date = date('Y-m-d 00:00:00');
                                    $end_date = date('Y-m-d 23:59:59', strtotime("+6 day", strtotime($start_date)));

                                    $log = "New week entry for: Class[$classes_id], Position[$position], UserHasClass[$users_has_classes_id], for User[$users_id], NextClass[$next_result->classes_id], NextUserClass[$next_result->id]";
                                    generate_log($log);

                                    foreach($classes['result'] as $ckey => $cval){
                                        if($next_result->classes_id == $cval['id'] && $ckey == 1){
                                            $this->db->update('users_has_classes', array('start_at' => $start_date,'end_at' => $ckey ? $end_date : NULL, 'status' => 'STARTED', 'users_id' => $next_result->users_id, 'current_page_position' => 0,'week_number' => $ckey), array('classes_id' => $cval['id'], 'users_id' => $next_result->users_id));
                                        } else if($ckey > 1){
                                            if(!$this->db->where(array('users_id'=>$users_id,'classes_id'=>$cval['id']))
                                                ->get('users_has_classes')->num_rows()) {
                                                $this->db->insert('users_has_classes', array('classes_id' => $cval['id'], 'start_at' => $start_date,'end_at' => $ckey ? $end_date : NULL, 'status' => 'STARTED', 'users_id' => $next_result->users_id, 'current_page_position' => 0,'week_number' => $ckey));
                                            }
                                            
                                        }
                                        if($ckey){
                                            $start_date = date('Y-m-d', strtotime("+1 day", strtotime($end_date)));
                                            $end_date = date('Y-m-d 23:59:59', strtotime("+6 day", strtotime($start_date)));
                                        }
                                    }

                                }
                            }
                        }   
                        // initial and first week class are having same start dates
                    }
                    $status = 'complete';
                    $msg = 'Class completed successfully';
                    $log = "User [$users_id] completed class [$classes_id]";
                }else{
                    $status = 'error';
                    $msg = 'No page found in this class';
                    $log = "No page found in this class";
                }
            }
        }
        $this->db->trans_complete();

        if ($this->db->trans_status() !== FALSE) {
            generate_log($log);
            return array('status' => $status, 'msg' => $msg, 'data' => $data,'redirectTo'=>$redirectTo, 'mail_send' => $mail_send);
        }
        return array('status' => $status, 'msg' => $msg, 'data' => $data,'redirectTo'=>$redirectTo);
    }

    function get_class_position($classes_id) {   
        $this->db->select('position');
        $this->db->where('classes_id', $classes_id);
        $qres = $this->db->get('study_has_courses');
        if ($qres->num_rows() > 0) {
            $data = $qres->row();
            return $data->position;
        }

    }

    /**
     * @desc Check page status running class
     * @param type $params
     * @return array
     */
    public function check_page_status($user_has_class_id, $position) {
        if ($class_id != '') {
            $position = ($position != 0) ? ($position - 1) : 0;
            $info_array = array('where' => array('users_has_classes_id' => $user_has_class_id, 'position' => $position), 'table' => $this->tables['users_page_activity']);
            $info_array['fields'] = 'users_has_classes.id,users_page_activity.status,users_page_activity.pages_id,users_page_activity.users_has_classes_id,current_page_position';
            $info_array['join'] = array(
                array(
                    'table' => 'users_page_activity',
                    'on' => 'users_has_classes.id = users_page_activity.users_has_classes_id',
                    'type' => 'LEFT'
                )
            );
            // getting sub page details like topics, testimonials
            $result = $this->db_model->get_data($info_array);
        }

        return $result;
    }

    /**
     * @desc Save class data
     * @param type $params
     * @return array
     */
    function save_class($params = array()) {
        extract($params);
        $class_title = isset($class_title) ? $class_title : '';
        $class_id = isset($class_id) ? $class_id : '';
        $course_id = isset($course_id) ? $course_id : '';
        

        $created_at = date('Y-m-d H:i:s');

        $duration = $this->config->item('class_duration');
        $page_type = isset($page_type) ? $page_type : '';

        $previous_tile_file_id = isset($previous_tile_file_id) ? $previous_tile_file_id : NULL;       

        $course_id = isset($course_id) ? $course_id : '';
        $this->db->trans_start();
        // Save in Database
        if (isset($class_id) && $class_id) { 
            $this->db->update($this->tables['class'], array('tile_image' => $previous_tile_file_id), array('id' => $class_id));
        } else {
            // get max position of class
            $this->db->select_max('position');
            $result = $this->db->where(array('courses_id' => $course_id))->get($this->tables['class'])->row();
            $position = 0;
            if($result->position != ''){
                $position = $result->position+1;
            }

            if ($files['tile_image']['name'] != '') {
                $tile_image = isset($files['tile_image']) ? $this->db_model->upload_document($files, 'tile_image', 'images', $previous_tile_file_id) : '';
            }else{
                $tile_image = $previous_tile_file_id;
            }


            $this->db->insert($this->tables['class'], array('title' => $class_title, 'duration' => $duration, 'tile_image' => $tile_image, 'is_active' => 1, 'created_at' => $created_at, 'courses_id' => $course_id, 'position' => $position));

            $params['class_id'] = $this->db->insert_id();

            $this->db->insert($this->tables['reviews'], array('classes_id' => $params['class_id'], 'created_at' => $created_at));
            $this->db->insert($this->tables['homework_exercises'], array('classes_id' => $params['class_id'], 'created_at' => $created_at));
        }


        $this->db->trans_complete();
        $status = 'error';
        $msg = 'Error while saving class info';
        if ($this->db->trans_status() !== FALSE) {
            $result = $this->update_page($params);
            if ($result['status'] == 'success') {
                $status = 'success';
                $msg = 'Class info saved successfully.';
            }
        }
        return array('status' => $status, 'msg' => $msg, 'id' => $class_id);
    }


    function update_image($params = array()) {
        $status = 'error';
        $msg = 'Error while updating image';
        extract($params);
        $files = $_FILES;
        $img_id = isset($img_id) ? $img_id : NULL;
        $class_id = isset($class_id) ? $class_id : NULL;
        
        $files_id = isset($_FILES['tile_image']['name']) ? $_FILES['tile_image']['name'] : NULL;
        if ($files_id != '') {
            if ($img_id != '') {
                $tile_image = isset($files['tile_image']) ? $this->db_model->upload_document($files, 'tile_image', 'images', $img_id) : '';
                $status = "success";
                $msg = "Image updated successfully";
            }else{
                $tile_image = isset($files['tile_image']) ? $this->db_model->upload_document($files, 'tile_image', 'images', $img_id) : '';
                $this->db->update($this->tables['class'], array('tile_image' => $tile_image), array('id' => $class_id));
                $status = "success";
                $msg = "Image updated successfully";
            }
        }else{
            $tile_image = '';

        }
        return array('status' => $status, 'msg' => $msg);
    }

    /**
     * @desc Save and update page data
     * @param type $params
     * @return array
     */
     function update_page($params = array()) {
        extract($params);
        $class_id = isset($class_id) ? $class_id : NULL;
        $page_id = isset($page_id) ? $page_id : NULL;
        $files_id = isset($files_id) ? $files_id : NULL;
        $previous_file_id = isset($previous_file_id) ? $previous_file_id : NULL;
        $previous_audio_id = isset($previous_audio_id) ? $previous_audio_id : NULL;
        $previous_audio_id_2 = isset($previous_audio_id_2) ? $previous_audio_id_2 : NULL;
        $previous_poem_id = isset($previous_poem_id) ? $previous_poem_id : NULL;
        $previous_image = isset($previous_image) ? $previous_image : NULL;
        $created_at = date('Y-m-d H:i:s');

        $data = array('title' => isset($title) ? $title : '', 'button_text' => isset($button_text) ? $button_text : '', 'created_at' => $created_at);
        
        $transactional_data = array();

        $data['pages_classes_id'] = $class_id;

        if ($page_type) {
            switch ($page_type) {
                case 'general':
                    $data['header'] = isset($header) ? $header : '';
                    $data['content'] = isset($content) ? $content : '';
                    $data['remove_foreground_objects'] = isset($remove_foreground_objects) ? $remove_foreground_objects : '0';
                    if ($files['general_image']['name'] != '') {
                        $data['files_id'] = isset($files['general_image']) ? $this->db_model->upload_document($files, 'general_image', 'images', $previous_file_id) : '';
                    }
                    break;
                case 'audio':
                    $data['script'] = isset($script) ? $script : '';
                    $data['practice_type'] = isset($practice_type) ? $practice_type : 'review';
                    $data['practice_title'] = isset($practice_title) ? $practice_title : '';
                    $data['practice_text'] = isset($practice_text) ? $practice_text : '';
                    if(isset($practice_categories_id) && $practice_categories_id){
                        $data['practice_categories_id'] = $practice_categories_id;
                    }

                    $data['audio_text'] = isset($audio_text) ? $audio_text : '';
                    $data['closing_file_id'] = isset($closing_file) ? $closing_file : '';
                    if ($files['audio']['name'] != '') {
                        $data['practice_audio_file_id'] = isset($files['audio']) ? $this->db_model->upload_audio_videos($files, 'audio', 'audios', $previous_audio_id) : '';
                    } else {
                        $data['practice_audio_file_id'] = $previous_audio_id;
                    }
                    if ($files['poem']['name'] != '') {
                        $data['poem_file_id'] = isset($files['poem']) ? $this->db_model->upload_audio_videos($files, 'poem', 'audios', $previous_poem_id) : '';
                    } else {
                        $data['poem_file_id'] = $previous_poem_id;
                    }
                    if ($files['audio2']['name'] != '') {
                        $data['audio_file_id'] = isset($files['audio2']) ? $this->db_model->upload_audio_videos($files, 'audio2', 'audios', $previous_audio_id_2) : '';
                    } else {
                        $data['audio_file_id'] = $previous_audio_id_2;
                    }

                    $audio_files = array();

                    $course_detail = $this->course->get_courses(array('where' => array('courses.id' => $course_id)));
                    if ($course_detail['result'] && isset($course_detail['result'][0])) {
                        $config = $this->config->item('sftp_assets_audios');
                        $audio_files['start_bell_audio'] = (!empty($course_detail['result'][0]['bell_audio'])) ? $config['path'] . $course_detail['result'][0]['bell_audio']['bell_unique_name'] : FALSE;
                        $audio_files['end_bell_audio'] = $audio_files['start_bell_audio'];
                    }
                    $audio_files['practice_audio'] = get_file_unique_name($data['practice_audio_file_id']);
                    if ($data['audio_file_id']) {
                        $audio_files['audio_2'] = get_file_unique_name($data['audio_file_id']);
                    }
                    if ($data['poem_file_id']) {
                        $audio_files['poem_audio'] = get_file_unique_name($data['poem_file_id']);
                    }
                    $audio_files['closing_audio'] = get_file_unique_name($data['closing_file_id']);
                    $files_id = $this->generate_audio($audio_files, $previous_file_id = ($previous_file_id && $previous_audio_id != $previous_file_id ? $previous_file_id : ''));
                    if ($files_id) {
                        $data['files_id'] = $files_id;
                    } else {
                        $data['files_id'] = $data['practice_audio_file_id'];
                    }
                    if (isset($audio_files['poem_audio'])) {
                        unset($audio_files['poem_audio']);
                    }
                    break;
                case 'video':
                    $data['practice_type'] = isset($practice_type) ? $practice_type : 'review';
                    $data['practice_title'] = isset($practice_title) ? $practice_title : '';
                    $data['practice_text'] = isset($practice_text) ? $practice_text : '';
                    if(isset($practice_categories_id) && $practice_categories_id){
                        $data['practice_categories_id'] = $practice_categories_id;
                    }
                    $data['header'] = isset($header) ? $header : '';
                    $data['pretext'] = isset($pretext) ? $pretext : '';
                    $data['script'] = isset($script) ? $script : '';
                    $data['post_text'] = isset($post_text) ? $post_text : '';
                    if ($files['video']['name'] != '') {
                        $data['files_id'] = isset($files['video']) ? $this->db_model->upload_audio_videos($files, 'video', 'videos', $previous_file_id) : '';
                    }
                    break;
                case 'question':
                    $data['question_number'] = isset($question_number) ? $question_number : '';
                    $data['question_color'] = isset($question_color) ? $question_color : '';
                    $data['question_text'] = isset($question_text) ? $question_text : '';
                    break;
                case 'numbered_general':
                    $data['header'] = isset($header) ? $header : '';
                    $data['question_number'] = isset($question_number) ? $question_number : '';
                    $data['question_color'] = isset($question_color) ? $question_color : '';
                    $data['content'] = isset($content) ? $content : '';
                    break;
                case 'topic':
                    $data['intro_text'] = isset($intro_text) ? $intro_text : '';
                    $transactional_data = array(); //array('data'=>array(),'where'=>array());
                    if (!empty($topic_title)) {
                        foreach ($topic_title as $key => $value) {
                            $transactional_data[$key]['topic_title'] = $value;
                            $transactional_data[$key]['topic_color'] = isset($topic_color[$key]) ? $topic_color[$key] : '';
                            $transactional_data[$key]['topic_text'] = isset($topic_text[$key]) ? $topic_text[$key] : '';
                            $transactional_data[$key]['sub_id'] = isset($sub_id[$key]) ? $sub_id[$key] : '';
                        }
                    }
                    break;
                case 'testimonial':
                    $data['header'] = isset($header) ? $header : '';
                    $transactional_data = array();
                    if (!empty($name)) {
                        $_FILES = re_arrange_files($files['photo'], 'photo');
                        foreach ($name as $key => $value) {
                            $transactional_data[$key]['name'] = $value;
                            $transactional_data[$key]['quote'] = isset($quote[$key]) ? $quote[$key] : '';
                            if ($_FILES["photo_" . $key]['name']) {
                                $transactional_data[$key]['files_id'] = $this->db_model->upload_document($_FILES, "photo_" . $key, 'images', isset($previous_file_id[$key]) ? $previous_file_id[$key] : '');
                            } else if (isset($previous_file_id[$key]) && $previous_file_id[$key]) {
                                $transactional_data[$key]['files_id'] = $previous_file_id[$key];
                            }
                            $transactional_data[$key]['sub_id'] = isset($sub_id[$key]) ? $sub_id[$key] : '';
                        }
                    }
                    break;
                case 'intention':
                    $data['header'] = isset($header) ? $header : '';
                    $data['intro_text'] = isset($intro_text) ? $intro_text : '';
                    break;
                case 'podcast':
                    $data['script'] = isset($script) ? $script : '';
                    $data['practice_type'] = isset($practice_type) ? $practice_type : 'review';
                    $data['practice_title'] = isset($practice_title) ? $practice_title : '';
                    $data['practice_text'] = isset($practice_text) ? $practice_text : '';
                    if(isset($practice_categories_id) && $practice_categories_id){
                        $data['practice_categories_id'] = $practice_categories_id;
                    }
                    $data['audio_text'] = isset($audio_text) ? $audio_text : '';
                    $data['is_podcast_page'] = 1;

                    if ($files['audio']['name'] != '') {
                        $data['practice_audio_file_id'] = isset($files['audio']) ? $this->db_model->upload_audio_videos($files, 'audio', 'audios', $previous_audio_id) : '';
                    } else {
                        $data['practice_audio_file_id'] = $previous_audio_id;
                    }

                    if ($files['audio']['name'] != '') {
                        $data['files_id'] = isset($files['audio']) ? $this->db_model->upload_audio_videos($files, 'audio', 'audios', $previous_audio_id) : '';
                    } else {
                        $data['files_id'] = $previous_audio_id;
                    }
                    break;
            }
        }

        $this->db->trans_start();
        if ($class_id && $page_id) {
            $this->db->update($this->tables['page'], array('title' => $data['title']), array('id' => $page_id, 'classes_id' => $class_id));
            $this->db->update($this->tables[$page_type], $data, array('pages_id' => $page_id, 'pages_classes_id' => $class_id));
        } else {
            $this->db->set('title', $data['title']);
            $this->db->set('position', 'IFNULL((SELECT MAX(p.position) FROM pages p GROUP BY p.classes_id HAVING p.classes_id=' . $class_id . ') + 1,0)', FALSE);
            $this->db->set('page_type', ucwords($page_type));
            $this->db->set('created_at', $created_at);
            $this->db->set('classes_id', $class_id);
            $this->db->insert($this->tables['page']);
            $page_id = $this->db->insert_id();
            $data['pages_id'] = $page_id;
            $this->db->insert($this->tables[$page_type], $data);
            $current_id = $this->db->insert_id();
        }

        if ($page_type == 'audio' || $page_type == 'video' || $page_type == 'podcast') {
            $review_data['class_id'] = $class_id;
            $review_data['page_id'] = $page_id;
            $review_data['created_at'] = $created_at;
            $this->addReviewDetail($review_data);
        }

        if (!empty($transactional_data) && isset($current_id) && $current_id) {
            $sub_data = array();
            foreach ($transactional_data as $key => $value) {
                $transactional_data[$key][$page_type . 's_id '] = $current_id;
                $sub_id = $transactional_data[$key]['sub_id'];
                unset($transactional_data[$key]['sub_id']);
                if ($sub_id) {
                    $this->db->update($this->tables['s_' . $page_type], $transactional_data[$key], array('id' => $sub_id));
                } else {
                    $this->db->insert($this->tables['s_' . $page_type], $transactional_data[$key]);
                }
            }
        }

        $this->db->trans_complete();
        $status = 'error';
        $msg = 'Error while saving page details.';
        if ($this->db->trans_status() !== FALSE) {
            $status = 'success';
            $msg = 'Page details saved successfully.';
        }        
        return array('status' => $status, 'msg' => $msg);

    }

    /**
     * @desc delete page data
     * @param type $page_type
     * @param type $id
     * @return array
     */
    function delete_page($page_type, $id) {
        $status = 'error';
        $msg = 'Error while deleting ' . $page_type;
        $query = $this->db->where('pages_id', $id)->limit(1)->get($this->tables[$page_type]);

        if ($query->num_rows() > 0) {
            $this->db->trans_start();
            $page_detail = $query->row();

            if ($page_type == 'topic' || $page_type == 'testimonial') {
                $this->db->delete($this->tables['s_' . $page_type], array($page_type . 's_id' => $page_detail->id));
            }

            if($page_type == 'audio' || $page_type == 'video'){
                $user_page_activity_query = $this->db->where('pages_id', $id)->get($this->tables['page_activity']);

                if ($user_page_activity_query->num_rows() > 0) {
                    $page_activity_detail = $user_page_activity_query->result_array();
                    $page_activity_array = array();
                    for($i=0 ; $i< count($page_activity_detail) ;$i++){
                        $this->db->delete($this->tables['file_tracking'], array('user_page_activity_id' =>  $page_activity_detail[$i]['id']));
                    }
                }

                $config=$this->config->item('sftp_details');
                $connection = ssh2_connect($config['hostname'], 22);
                if($connection){
                    $assets_config = $this->config->item('sftp_assets_'.$page_type.'s');
                    ssh2_auth_password($connection, $config['username'], $config['password']);
                    $sftp = ssh2_sftp($connection);
                }
            }
            if($page_type == 'question'){
                $question_query = $this->db->where('pages_id', $id)->get($this->tables['question']);
                if ($question_query->num_rows() > 0) {
                    $question_detail = $question_query->row();
                }

                $user_has_reflection_question_query = $this->db->where('reflection_question_id', $question_detail->id)->get('users_has_reflection_question');
                if ($user_has_reflection_question_query->num_rows() > 0) {
                    $user_has_reflection_question_detail = $user_has_reflection_question_query->result_array();

                    $user_has_reflection_question_detail_array = array();
                    for($i=0 ; $i<count($user_has_reflection_question_detail) ;$i++){
                        $ans_id = $user_has_reflection_question_detail[$i]['id'];

                        $answer_comment_query = $this->db->where('answer_id', $ans_id)->get('answer_comments');
                        $answer_comment_detail = $answer_comment_query->result_array();

                        for($j=0; $j<count($answer_comment_detail) ; $j++ ){
                            $parent_id =  $answer_comment_detail[$j]['parent_comment_id'];
                            if($parent_id == NULL){
                                 $this->db->delete('users_answer_status', array('answer_id' => $answer_comment_detail[$j]['answer_id'] ));
                            }else{
                                 $this->db->delete('users_answer_comments_status', array('answer_comments_id' =>  $answer_comment_detail[$j]['parent_comment_id']));
                                 $this->db->delete('answer_comments', array('answer_id' =>  $ans_id , 'id' =>$answer_comment_detail[$j]['id']));
                            }
                        }
                        $this->db->delete('answer_comments', array('answer_id' =>$ans_id,));
                    }
                    $this->db->delete('users_has_reflection_question', array('id' =>  $ans_id));
                }
            }

            $this->db->delete($this->tables['page_activity'], array('pages_id' => $id));

            if ($this->db->delete($this->tables[$page_type], array('pages_id' => $id))) {
                if($page_type == 'general'){
                    $image_name = $this->db->where('id', $page_detail->files_id)->limit(1)->get($this->tables['files'])->row();

                    $this->db->delete($this->tables['files'], array('id' => $page_detail->files_id));
                    // delete image from folder
                    if($image_name){
                        $assets_config = $this->config->item('assets_images');
                        $image_path = $assets_config['path'].'/'.$image_name->unique_name;
                        unlink($image_path);
                    }
                    // delete image from folder

                }
                if($page_type == 'audio'){
                    // Fetch file name from table
                    $file_name = $this->db->where('id', $page_detail->files_id)->limit(1)->get($this->tables['files'])->row();
                    $practiceFile_name = $this->db->where('id', $page_detail->practice_audio_file_id)->limit(1)->get($this->tables['files'])->row();
                    $poemFile_name = $this->db->where('id', $page_detail->poem_file_id)->limit(1)->get($this->tables['files'])->row();
                    $audioFile_name = $this->db->where('id', $page_detail->audio_file_id)->limit(1)->get($this->tables['files'])->row();
                    // Fetch file name from table

                    $this->db->delete($this->tables['files'], array('id' => $page_detail->files_id));
                    $this->db->delete($this->tables['files'], array('id' => $page_detail->practice_audio_file_id));
                    $this->db->delete($this->tables['files'], array('id' => $page_detail->poem_file_id));
                    $this->db->delete($this->tables['files'], array('id' => $page_detail->audio_file_id));

                    $this->db->delete('users_has_reviews_has_files', array('files_id' => $page_detail->files_id));
                    $this->db->delete('users_has_reviews_has_files', array('files_id' => $page_detail->practice_audio_file_id));
                    $this->db->delete('users_has_reviews_has_files', array('files_id' => $page_detail->poem_file_id));
                    $this->db->delete('users_has_reviews_has_files', array('files_id' => $page_detail->audio_file_id));

                    // unlink file from folder
                    if($file_name){
                        $file_path = $assets_config['path'].$file_name->unique_name;
                        @ssh2_sftp_chmod ($sftp, $file_path , 0777 );
                        @ssh2_sftp_unlink($sftp, $file_path);
                    }
                    if($practiceFile_name){
                        $practiceFile_path = $assets_config['path'].$practiceFile_name->unique_name;
                        @ssh2_sftp_chmod ($sftp, $practiceFile_path , 0777 );
                        @ssh2_sftp_unlink($sftp, $practiceFile_path);
                    }
                    if($poemFile_name){
                        $poemFile_path = $assets_config['path'].$poemFile_name->unique_name;
                        @ssh2_sftp_chmod ($sftp, $poemFile_path , 0777 );
                        @ssh2_sftp_unlink($sftp, $poemFile_path);
                    }
                    if($audioFile_name){
                        $audioFile_path = $assets_config['path'].$audioFile_name->unique_name;
                        @ssh2_sftp_chmod ($sftp, $audioFile_path , 0777 );
                        @ssh2_sftp_unlink($sftp, $audioFile_path);
                    }
                    // unlink file from folder

                }
                if($page_type == 'video'){
                    // fetch the video name from table
                    $videoFile_name = $this->db->where('id', $page_detail->files_id)->limit(1)->get($this->tables['files'])->row();
                    // fetch the video name from table
                    $this->db->delete($this->tables['files'], array('id' => $page_detail->files_id));
                    $this->db->delete('users_has_reviews_has_files', array('files_id' => $page_detail->files_id));
                    // delete video file from folder

                    if($videoFile_name){
                        $videoFile_path = $assets_config['path'].$videoFile_name->unique_name;
                        @ssh2_sftp_chmod ($sftp, $videoFile_path , 0777 );
                        @ssh2_sftp_unlink($sftp, $videoFile_path);
                    }
                    // delete video file from folder
                }

                $this->db->delete($this->tables['reviews_has_files'], array('pages_id' => $page_detail->pages_id));
                $this->db->delete($this->tables['page'], array('id' => $page_detail->pages_id));
                // for update position
                $class_id    = $page_detail->pages_classes_id;
                $page_list = $this->get_pages(array('where' => array('pages.classes_id' => $class_id)));
                foreach($page_list['result'] as $key => $value){
                    $value['position'] =  $key;
                    $this->db->update($this->tables['page'], array('position' => $key), array('id' => $value['id'], 'classes_id' => $class_id));
                }
                // for update position
            }
            $status = 'success';
            $msg = $page_type . ' deleted successfully.';
            $this->db->trans_complete();
            $status = 'error';
            $msg = 'Error while saving page details.';
            if ($this->db->trans_status() !== FALSE) {
                $status = 'success';
                $msg = 'Page deleted successfully.';
            }
        }
        return array('status' => $status, 'msg' => $msg);
    }

    /**
     * @desc Update class status
     * @param type $params
     * @return array
     */
    function change_status($params = array()) {
        extract($params);
        $class_id = isset($cid) ? $cid : FALSE;
        $status = 'error';
        $msg = 'Error while change status';
        $this->db->trans_start();
        if ($class_id) {
            $this->db->update($this->tables['class'], array('is_active' => $is_active), array('id' => $class_id));
        }

        $this->db->trans_complete();
        if ($this->db->trans_status() !== FALSE) {
            $status = 'success';
            $msg = 'Class status changed successfully.';
        }
        return array('status' => $status, 'msg' => $msg);
    }

    /**
     * @desc Reordering of pages
     * @param type $data
     * @return array
     */
    function reorder_pages($data = array(), $classes_id = FALSE) {
        $status = 'error';
        $msg = 'Error while reordering';
        $this->db->trans_start();
        if ($classes_id) {
            $status = $this->db->where('classes_id', $classes_id)
                            ->limit(1)
                            ->count_all_results($this->tables['users_has_classes']) > 0;
            if (!empty($data)) {
                $this->db->update_batch($this->tables['page'], $data, 'id');
                $status = 'success';
                $msg = 'Reordered successfully.';
            } else {
                $status = 'success';
                $msg = 'Reordered successfully.';
            }
        }
        $this->db->trans_complete();
        if ($this->db->trans_status() !== FALSE) {
            return array('status' => $status, 'msg' => $msg);
        }
    }

    /**
     * @desc Reordering of classes
     * @param type $data
     * @return array
     */
    function reorder_classes($data = array(), $classes_id = FALSE) {
        $status = 'error';
        $msg = 'Error while reordering';
        $this->db->trans_start();
        if ($classes_id) {
            if (!empty($data)) {
                $this->db->update_batch($this->tables['class'], $data, 'id');
                $status = 'success';
                $msg = 'Reordered successfully.';
            } else {
                $status = 'success';
                $msg = 'Reordered successfully.';
            }

        }
        $this->db->trans_complete();
        if ($this->db->trans_status() !== FALSE) {
            return array('status' => $status, 'msg' => $msg);
        }
    }

    /**
     * @desc Delete subpages of topic and testimonial
     * @param type $page_type
     * @param type $id
     * @param type $file_id
     * @param type $file_type
     * @return array
     */
    function delete_sub_page($page_type, $id = '', $file_id = '', $file_type = '') {
        $status = 'error';
        $msg = 'Error while deleting ' . $page_type;
        $this->db->trans_start();
        $this->db->delete($this->tables['s_' . $page_type], array('id' => $id));
        if ($file_id) {
            $query = $this->db->where('id', $file_id)->limit(1)->get($this->tables['files']);
            if ($query->num_rows() > 0) {
                $file_detail = $query->row();
                $filename = $file_detail->unique_name;
                $assets = $this->config->item('assets_' . $file_type);
                $upload_path = check_directory_exists($assets['path']);
                $path = $upload_path . '/' . $filename;
                if ($filename != '' && file_exists($path)) {
                    unlink($path);
                }
                $this->db->delete($this->tables['files'], array('id' => $file_id));
            }
        }

        $this->db->trans_complete();
        if ($this->db->trans_status() !== FALSE) {
            $status = 'success';
            $msg = $page_type . ' deleted successfully.';
        }

        return array('status' => $status, 'msg' => $msg, [$file_id, $id, $page_type, $file_type]);
    }

    /**
     * @desc Delete subpages of topic and testimonial
     * @param type $page_type
     * @param type $id
     * @param type $file_id
     * @param type $file_type
     * @param type $field
     * @return array
     */
    function delete_file($params) {
        extract($params);
        $status = 'error';
        $msg = 'Error while deleting image';
        $id = isset($id) ? $id : FALSE;
        $file_id = isset($file_id) ? $file_id : NULL;
        $page_type = isset($page_type) ? $page_type : FALSE;
        $file_type = isset($file_type) ? $file_type : FALSE;
        $field = isset($field) ? $field : FALSE;
        $this->db->trans_start();
        $this->db->update($this->tables[$page_type], array($field => NULL), array('id' => $id));
        $return = $this->db->affected_rows() == 1;
        if ($return) {
            $query = $this->db->where('id', $file_id)->limit(1)->get($this->tables['files']);
            if ($query->num_rows() > 0) {
                $file_detail = $query->row();
                $filename = $file_detail->unique_name;
                $assets = $this->config->item('assets_' . $file_type);
                $upload_path = check_directory_exists($assets['path']);
                $path = $upload_path . '/' . $filename;
                if ($filename != '' && file_exists($path)) {
                    unlink($path);
                }
                $this->db->delete($this->tables['files'], array('id' => $file_id));
            }
        }

        $this->db->trans_complete();
        if ($this->db->trans_status() !== FALSE) {
            $status = 'success';
            $msg = 'image deleted successfully.';
        }
        return array('status' => $status, 'msg' => $msg, [$file_id, $id, $page_type, $file_type]);
    }

    /**
     * @desc Getting page detail
     * @param type $type
     * @param type $page_id
     * @param type $class_id
     * @return array
     */
    function get_page_details($type = '', $page_id = '', $class_id = '', $users_id = false) {
        if ($page_id != '') {
            $info_array = array('where' => array('pages_id' => $page_id, 'pages_classes_id' => $class_id), 'table' => $this->tables[$type]);
        } else {
            $info_array = array('where' => array('pages_classes_id' => $class_id), 'table' => $this->tables[$type]);
        }
        $info_array['fields'] = $this->tables[$type] . '.*';
        if (in_array($type, $this->page_containing_files)) {
            $info_array['fields'] .= ',files.name,files.unique_name,files.id as files_id,files.type';
            $info_array['join'] = array(
                array(
                    'table' => 'files',
                    'on' => $this->tables[$type] . '.files_id = files.id',
                    'type' => 'LEFT'
                )
            );
        }

        $dataNextPrevious = array('previous' => '', 'next' => '');
        $pageQuery = $this->db->select('id,page_type')->where("classes_id = ".$class_id." AND position > (select position from " . $this->tables['page'] . " WHERE id = " . $page_id ." )")->order_by('position','asc')->limit(1)->get($this->tables['page']);
        if ($pageQuery->num_rows() > 0) {
            $dataNextPrevious['next'] = $pageQuery->row_array();
        }
        $pageQuery = $this->db->select('id,page_type')->where("classes_id = ".$class_id." AND position < (select position from " . $this->tables['page'] . " WHERE id = " . $page_id . ")")->order_by('position','desc')->limit(1)->get($this->tables['page']);
        if ($pageQuery->num_rows() > 0) {
            $dataNextPrevious['previous'] = $pageQuery->row_array();
        }
        // getting sub page details like topics, testimonials
        $result = $this->db_model->get_data($info_array);
        if (isset($result['result'][0]['id']) && ($type == 'topic' || $type == 'testimonial')) {
            $result['result'][0]['sub_details'] = $this->get_sub_page_details($type, $result['result'][0]['id']);
        }
        if (isset($result['result'][0]['id']) && ($type == 'question' || $type == 'intention')) {
            if ($users_id) {
                $result['result'][0]['sub_details'] = $this->get_answer($type, $result['result'][0]['id'], $users_id);
            }
        }
        if (isset($result['result'][0]['id']) && ($type == 'audio')) {
            $result['result'][0]['poem_url'] = '';
            if (isset($result['result'][0]['poems_id'])) {
                $poems_id = $result['result'][0]['poems_id'];
                $poem_data = get_file($poems_id, TRUE);
                if (!empty($poem_data)) {
                    $result['result'][0]['poem_url'] = $poem_data['url'];
                }
            }
        }
        if (isset($result['result'][0]['files_id'])) {
            $file_id = $result['result'][0]['files_id'];
            $file_data = get_file($file_id, TRUE);
            if (!empty($file_data)) {
                $result['result'][0]['url'] = $file_data['url'];
            }
        }
        if (isset($result['result'][0]['backgound_image_unique_name'])) {
            $result['result'][0]['background_image_url'] = base_url() . "assets/uploads/images/" . $result['result'][0]['backgound_image_unique_name'];
        }
        return array_merge($result, $dataNextPrevious);
    }

    /**
     * @desc Getting subpage detail of topic and testimonial
     * @param type $type
     * @param type $page_id
     * @return array
     */
    function get_sub_page_details($type = '', $page_id = '') {
        $info_array = array('where' => array($type . 's_id ' => $page_id), 'table' => $this->tables['s_' . $type]);
        $info_array['fields'] = $this->tables['s_' . $type] . '.*';
        $result = $this->db_model->get_data($info_array);
        if ($result['result']) {
            foreach ($result['result'] as $key => $value) {
                if (isset($value['files_id'])) {
                    $file_data = get_file($value['files_id'], TRUE);
                    if (!empty($file_data)) {
                        $result['result'][$key]['url'] = $file_data['url'];
                    }
                }
            }
            return $result['result'];
        }
    }

    /**
     * @desc Getting class detail
     * @param type $params
     * @return array
     */
    function get_classes($params = array()) {
        extract($params);
        $study_id = isset($study_id) ? $study_id : null;
       
        if(!is_null($study_id)){
             $info_array = array('fields' => 'classes.id,classes.title,duration,is_active,tile_image,classes.created_at,classes.updated_at,study_courses.position');
            $join = array(
                array(
                    'table' => 'courses course',
                    'on' => 'course.id = classes.courses_id',
                    'type' => 'LEFT'
                ),
                array(
                    'table' => 'study_has_courses study_courses',
                    'on' => 'study_courses.classes_id = classes.id',
                    'type' => 'LEFT'
                )
            );
            $info_array['order_by'] = 'study_courses.position';
        } else {
            $info_array = array('fields' => 'classes.id,classes.title,duration,is_active,tile_image,classes.created_at,classes.updated_at,classes.position');
            $join = array(
                array(
                    'table' => 'courses course',
                    'on' => 'course.id = classes.courses_id',
                    'type' => 'LEFT'
                )
            );
            $info_array['order_by'] = 'classes.position';
        }

        if (isset($where)) {
            $info_array['where'] = $where;
        }
        $info_array['join'] = $join;
        
        $info_array['order'] = 'ASC';
        
        $info_array['table'] = $this->tables['class'];
        $class_data = $this->db_model->get_data($info_array);
        if ($class_data['result'] && !empty($class_data['result'])) {
            foreach ($class_data['result'] as $key => $val) {                
                if (isset($val['tile_image'])) {
                    $file_id = $val['tile_image'];
                    $file_data = get_file($file_id, TRUE);
                    if (!empty($file_data)) {
                        $class_data['result'][$key]['url'] = $file_data['url'];
                    }
                }
            }
        }
        return $class_data;
    }

    /**
     * @desc Getting pages detail
     * @param type $params
     * @return array
     */
    function get_pages($params = array()) {
        extract($params);
        $users_id = isset($users_id) ? $users_id : FALSE;
        $info_array = array('fields' => 'pages.*');
        if (isset($where)) {
            $info_array['where'] = $where;
        }
        $info_array['order_by'] = 'pages.position';
        $info_array['order'] = 'ASC';
        $info_array['table'] = $this->tables['page'];
        $result = $this->db_model->get_data($info_array);
        if (!empty($result['result'])) {
            foreach ($result['result'] as $key => $value) {
                $result['result'][$key]['page_data'] = array();
                $type = strtolower($value['page_type']);
                $page_id = $value['id'];
                $page_detail = $this->get_page_details(strtolower($value['page_type']), $value['id'], $value['classes_id'], $users_id);
                if ($page_detail['result']) {
                    $result['result'][$key]['page_data'] = $page_detail['result'][0];
                }
            }
        }
        return $result;
    }

    function update_class($params = array()) {
        $status = 'error';
        $msg = 'Error while updating title';
        extract($params);
        $class_title = isset($class_title) ? $class_title : FALSE;
        $class_id = isset($class_id) ? $class_id : FALSE;
        if ($class_id && $class_title) {
            $this->db->update($this->tables['class'], array('title' => $class_title), array('id' => $class_id));
            $return = $this->db->affected_rows() == 1;
            $status = "success";
            $msg = "Class title updated successfully";
        }
        return array('status' => $status, 'msg' => $msg);
    }

    function generate_audio($params = array(), $previous_file_id, $type = 'practice') {
        $previous_file_id = isset($previous_file_id) ? $previous_file_id : FALSE;
        $config = $this->config->item('sftp_assets_audios');
        $config_local = $this->config->item('assets_audios');
        $upload_path = $config['path'];
        $upload_url = $config['url'];
        $local_path = $config_local['path'];
        $files_id = FALSE;
        $files_arr = array('start_bell_audio', 'practice_audio', 'audio_2', 'poem_audio', 'end_bell_audio', 'closing_audio');

        $temp_files_arr = array();  // Store temporary file names, delete them after merge
        $temp_output_arr = array();  // Store temporary output names, delete them after merge

        $this->load->library('phpmp3.php');
        $file_name = $type . '_' . uniqid() . '.mp3';
        $newpath = $upload_path . $file_name;
        $mergedFile = new PHPMP3();
        foreach ($files_arr as $key => $val) {
            if (isset($params[$val])) {
                $temp_filename = $local_path.'/'.basename($params[$val]).'.temp.mp3';
                $output_path = $local_path.'/output_' . basename($params[$val]) . '.file';
                exec('ffprobe -v error -show_entries stream=bit_rate,sample_rate '.$upload_url.basename($params[$val]).' > '.$output_path);
                $output = file_get_contents($output_path);  // convert file into string

                // If audio file is encoded with 44.1KHz sampling rate + 128kn bitrate, don't do conversion to save time
                if (strpos($output, 'sample_rate=44100') && strpos($output, 'bit_rate=128000')) {
                // No need to convert
                    exec('wget -O '. $temp_filename .' '. $upload_url.basename($params[$val]));
                } else {
                    exec('ffmpeg -i '.$upload_url.basename($params[$val]).' -f mp3 -ab 128k -ar 44100 -ac 2 -y '.$temp_filename);
                }

                array_push($temp_output_arr, $output_path);
                array_push($temp_files_arr, $temp_filename);

                $mp3 = new PHPMP3($temp_filename);
                $mergedFile->mergeBehind($mp3);
                $mergedFile->striptags();
            }
        }

        $mergedFile->setIdv3_2('01','Track Title','Artist','Album','Year','Genre','Comments','Composer','OrigArtist','Copyright','url','encodedBy');
        if($mergedFile->save($newpath) && count($temp_output_arr)) {
            // Delete temporary files
            foreach ($temp_files_arr as $tmp_file) {
                @unlink($tmp_file);
            }
            foreach ($temp_output_arr as $tmp_file) {
                @unlink($tmp_file);
            }
        }

        $input_arr = array(
            'name' => 'practice_audio',
            'unique_name' => $file_name,
            'type' => 'audio/mp3',
            'size' => 'audio/mp3',
            'created_at' => date('Y-m-d H:i:s')
        );
        $this->db->trans_start();
        if ($previous_file_id) {
            $query = $this->db->where('id', $previous_file_id)->limit(1)->get($this->tables['files']);
            if ($query->num_rows() > 0) {
                $file_detail = $query->row();
                $filename = $file_detail->unique_name;
                $path = $upload_path . $filename;
                // file name not blank and file exists then delete it
                if ($filename != '' && file_exists($path)) {
                        unlink($path);
                }
                $this->db->update($this->tables['files'], $input_arr, array('id' => $previous_file_id));
                $files_id = $previous_file_id;
            }
        } else {
            $this->db->insert($this->tables['files'], $input_arr);
            $files_id = $this->db->insert_id();
        }
        $this->db->trans_complete();
        return $files_id;
    }

    /**
     * @desc Add review detail for class
     * @param type $params
     * @return array
     */
    public function addReviewDetail($param) {
        extract($param);
        $class_id = isset($class_id) ? $class_id : FALSE;
        $page_id = isset($page_id) ? $page_id : FALSE;
        $created_at = isset($created_at) ? $created_at : FALSE;
        $query = $this->db->where('classes_id', $class_id)->get($this->tables['reviews']);
        if ($query->num_rows() > 0) {
            $review_detail = $query->row();
            $review_id = $review_detail->id;
        } else {
            $this->db->insert($this->tables['reviews'], array('classes_id' => $class_id, 'created_at' => $created_at));
            $review_id = $this->db->insert_id();
        }

        if ($review_id) {
            $get_review_files = $this->db->where(array('reviews_id' => $review_id, 'pages_id' => $page_id))->count_all_results($this->tables['reviews_has_files']);
            if (!$get_review_files) {
                $review_arr = array(
                    'reviews_id' => $review_id,
                    'pages_id' => $page_id,
                    'created_at' => $created_at
                );
                $this->db->insert($this->tables['reviews_has_files'], $review_arr);
            }
        }
    }

    public function check_user_class_read_count($users_id, $class_array){
        $ids = implode(',', $class_array);
        return $result = $this->db->select('classes_id')->where("classes_id IN (".$ids.") AND users_id =".$users_id." AND status = 'COMPLETED'")->get('users_has_classes')->result_array();
    }
    
    public function get_avaccess_data($users_id){
        if($users_id){
            $res = $this->db->select('uav.id,uav.current_time,uav.left_time, uav.total_time, uav.total_elapsed_time, uav.status, uav.files_id, uav.user_page_activity_id, upa.users_has_classes_id, upa.starts_at, uhc.users_id, files.name as files_name')
                ->join('files','files.id = uav.files_id','LEFT')
                ->join('users_page_activity upa','upa.id = uav.user_page_activity_id','LEFT')
                ->join('users_has_classes uhc','uhc.id = upa.users_has_classes_id','LEFT')
                ->where('uhc.users_id', $users_id)
                ->get('users_audio_video uav')->result_array();
            if(!empty($res)){
                foreach($res as $key=> $val){
                    $file_res = $this->db->select('files.name as files_name, upa.practice_audio_file_id')
                    ->join('files','files.id = upa.practice_audio_file_id','LEFT')
                    ->where('upa.files_id', $val['files_id'])
                    ->get('practice_audio upa')->row();
                    if(!empty($file_res)){
                        $val['files_name'] = !is_null($file_res->files_name) ? $file_res->files_name : $val['files_name'];
                    }

                    $complete_av_count =  $this->db->where('users_audio_video_id', $val['id'])
                    ->where('status', 1)
                    ->count_all_results('audio_video_access');
                    $val['complete_av_count'] = $complete_av_count;

                    $started_av_count =  $this->db->where('users_audio_video_id', $val['id'])
                    ->where('status', 0)
                    ->count_all_results('audio_video_access');
                    $val['started_av_count'] = $started_av_count;
                    $res[$key] = $val;
                }
            }
           return $res;
        }
    }

    public function update_meditation_time($param) {
        extract($param);
        $users_id = isset($users_id) ? $users_id : FALSE;
        $prepare_time = isset($prepare_time) ? $prepare_time : 0;
        $interval_time = isset($interval_time) ? $interval_time : 0;
        $meditation_time = isset($meditation_time) ? $meditation_time : 0;
        $current_meditation_time = isset($current_meditation_time) ? $current_meditation_time : 0;
        $meditation_id = (isset($meditation_id) && $meditation_id) ? $meditation_id : NULL;
        
        $created_at = date('Y-m-d H:i:s');
        $meditation_date = date('Y-m-d H:i:s');
        $total_elapsed_time = 0;
        if($current_meditation_time && $meditation_time != $current_meditation_time){
            $total_elapsed_time = $meditation_time - $current_meditation_time;
        } else {
            $total_elapsed_time = $meditation_time;
        }

        $data = [
            'users_id' => $users_id,
            'meditation_date' =>  $meditation_date,
            'prepare_time' => $prepare_time,
            'interval_time' => $interval_time,
            'meditation_time' => $meditation_time,
            'total_elapsed_time' => $total_elapsed_time,
            'created_at' => $created_at
        ];
        if($meditation_id){
            $this->db->update('meditation', $data, array('id' => $meditation_id));  
            return array('status' => 'success', 'msg' => 'meditation time updated successfully','id' => $meditation_id);
        } else {
            $this->db->insert('meditation', $data);
            $insert_id = $this->db->insert_id('meditation');
            if($insert_id){
                return array('status' => 'success', 'msg' => 'meditation time inserted successfully', 'id' => $insert_id);
            }
        }
    }

    public function get_meditation_data($users_id){
        if($users_id){
            $res = $this->db->select('meditation_date, meditation_time, total_elapsed_time')
                ->where('users_id', $users_id)
                ->get('meditation')->result_array();
            return $res;
        }
    }

    public function get_userlogin_count($users_id){
        if($users_id){
            $res = $this->db->select('date(`login_time`) as `login_date` ')
                ->where('users_id', $users_id)
                ->group_by('login_date')
                ->get('user_tokens')->num_rows();
            return $res;
        }
    }

    public function get_excercise_complete_count($id){
        if($id){
            $res = $this->db->select('*')
                ->where('users_has_exercises_has_files_id', $id)
                ->get('audio_video_access')->num_rows();
            return $res;
        }
    }

    public function get_reading_data($users_id){
        if($users_id){
            $res = $this->db->select('uhra.id, uhra.start_time, uhra.end_time, SUM(TIMESTAMPDIFF(MINUTE, uhra.start_time, uhra.end_time)) AS TotalTimeSpentInMinutes,  hra.title, hra.title as article_title, class.title as class_title')
                ->join('homework_reading_articles hra','hra.id = uhra.homework_reading_articles_id','LEFT')
                ->join('classes class','class.id = hra.homework_readings_classes_id','LEFT')
                ->where('uhra.users_id', $users_id)
                ->group_by('uhra.homework_reading_articles_id, DATE(uhra.start_time)')
                ->order_by('uhra.start_time')
                ->get('users_has_homework_reading_articles uhra')->result_array();
            return $res;
        }
    }


    public function get_exercise_data($users_id, $type='exercise'){
        if($users_id && $type && $type == 'exercise'){
             $res = $this->db->select('uef.id as uef_id,uef.total_elapsed_time, uef.total_time, uef.created_at, files.name as files_name, category.label as category_title,files.id as exercise_file_id,uef.practice_categories_id as cat_id') 
                ->join('practice_categories category','category.id = uef.practice_categories_id','LEFT')
                ->join('files','files.id = uef.files_id','INNER')
                ->where(array('uef.users_id' => $users_id, 'uef.homework_podcast_recordings_id' => NULL))
                ->get('users_has_exercises_has_files uef')->result_array();
                if(!empty($res)){
                    foreach($res as $key=> $val){
                        $complete_excercise_count =  $this->db->where('users_has_exercises_has_files_id', $val['uef_id'])
                        ->where('status', 1)
                        ->count_all_results('audio_video_access');
                        $val['complete_excercise_count'] = $complete_excercise_count;

                        $started_excercise_count =  $this->db->where('users_has_exercises_has_files_id', $val['uef_id'])
                        ->where('status', 0)
                        ->count_all_results('audio_video_access');
                        $val['started_excercise_count'] = $started_excercise_count;
                        $val['practice_title'] = $this->get_practice_category_files($val['cat_id'],$val['exercise_file_id']);
                        $res[$key] = $val;
                    }                
                }             
                //->join('homework_exercises_has_files hehf','hehf.id = uef.homework_exercises_has_files_id','LEFT')
                // ->join('homework_exercises home_ex','home_ex.id = hehf.homework_exercises_id','LEFT')
                //->join('course_homework course_home','course_home.id = hehf.course_homework_id','LEFT')
           return $res;
        }elseif($users_id && $type && $type == 'podcast'){
            $res = $this->db->select('uef.id, uef.total_elapsed_time, uef.total_time, uef.created_at, files.name as files_name, class.title as class_title, home_pod.title as homework_title, hpr.title as home_pod_recording_title')
                ->join('files','files.id = uef.files_id','LEFT')
                ->join('homework_podcast_recordings hpr','hpr.id = uef.homework_podcast_recordings_id','LEFT')
                ->join('homework_podcasts home_pod','home_pod.id = hpr.homework_podcasts_id','LEFT')
                ->join('classes class','class.id = uef.classes_id','LEFT')
                ->where(array('uef.users_id' => $users_id, 'uef.homework_exercises_has_files_id' => NULL))
                ->get('users_has_exercises_has_files uef')->result_array();
            return $res;
        }
    }

    function get_practice_category_files($category_id = false,$files_id) {
        $this->db->select('practice_title');
        $this->db->where(array('practice_categories_id' => $category_id,'files_id' => $files_id,'practice_type' => 'practice','is_podcast_page' => 0));
        $audio_data = $this->db->get('practice_audio')->result();

        $this->db->select('practice_title');
        $this->db->where(array('practice_categories_id' => $category_id,'files_id' => $files_id,'practice_type' => 'practice'));
        $video_data = $this->db->get('educational_video')->result();

        $this->db->select('practice_title');
        $this->db->where(array('practice_categories_id' => $category_id,'files_id' => $files_id,'practice_type' => 'practice','is_podcast_page' => 1));
        $podcast_data = $this->db->get('practice_audio')->result();

        $this->db->select('title as practice_title');
        $this->db->where(array('practice_categories_id' => $category_id,'files_id' => $files_id));
        $audio_data = $this->db->get('course_homework')->result();

        if(!is_array($audio_data) || !is_array($video_data) || !is_array($podcast_data)){
           $audio_data = [];
           $video_data = [];
           $podcast_data = [];
        }

        $data = array_merge($audio_data,$video_data,$podcast_data);  
        return $data;
    }

    public function get_class_list($users_id){
        $users_id = isset($users_id) ? $users_id : FALSE;
        $today_date = date('Y-m-d H:i:s');
        $res = $this->db->select('users_has_classes.id,class.title, users_has_classes.start_at, users_has_classes.end_at, users_has_classes.status, , IF("'.$today_date.'" BETWEEN users_has_classes.start_at AND users_has_classes.end_at ,"current_week",IF("'.$today_date.'"<= users_has_classes.end_at,"next_week","previous_week")) as week_status, users_has_classes.week_number')
            ->where('users_id', $users_id)
            ->join('classes class','class.id = users_has_classes.classes_id')
            ->get($this->tables['users_has_classes'])->result_array();
        if(!empty($res)){
            if(isset($res[0]['status']) && $res[0]['status'] == 'STARTED'){
                $study_id = user_has_study($users_id);
                $classes = $this->get_classes(array('where' => array('is_active'=>1,'study_courses.study_id'=>$study_id), 'study_id'=> $study_id));
                if(!empty($classes['result'])){
                    $classes = $classes['result'];
                    foreach ($classes as $ckey => $cvalue) {
                        $classes[$ckey]['class_start_at'] = '';
                        $classes[$ckey]['completed_at'] = '';
                        $classes[$ckey]['end_at'] = null;
                        $classes[$ckey]['id'] = $cvalue['id'];
                        $classes[$ckey]['start_at'] = '';
                        $classes[$ckey]['status'] = 'STARTED';
                        $classes[$ckey]['title'] = $cvalue['title'];
                        $classes[$ckey]['week_number'] = $cvalue['position'];
                        $classes[$ckey]['week_status'] = 'next_week';
                        if($ckey == 0 || $ckey == 1){
                            $classes[$ckey]['start_at'] = $res[$ckey]['start_at'];
                            $classes[$ckey]['status'] = $res[$ckey]['status'];
                            $classes[$ckey]['week_number'] = $res[$ckey]['week_number'];
                            $classes[$ckey]['week_status'] = $res[$ckey]['week_status'];
                        }
                        unset($classes[$ckey]['created_at']);
                        unset($classes[$ckey]['duration']);
                        unset($classes[$ckey]['is_active']);
                        unset($classes[$ckey]['position']);
                        unset($classes[$ckey]['updated_at']);
                    }
                }
                $res = $classes;
            }  else {
                foreach($res as $key => $val){
                    $completed_at = '';
                    $startRes = $this->db->select('starts_at')
                            ->where('users_has_classes_id', $val['id'])
                            ->order_by('id','asc')
                            ->get('users_page_activity')->row(); 
                    $start_at = isset($startRes->starts_at) && $startRes->starts_at ? $startRes->starts_at : '';
                    if($val['status'] == 'COMPLETED'){
                        $result = $this->db->select('ends_at')
                                            ->where('users_has_classes_id', $val['id'])
                                            ->order_by('id','desc')
                                            ->get('users_page_activity')->row(); 
                        if ($result) {
                            $completed_at = $result->ends_at;
                        }
                    }
                    $res[$key]['class_start_at'] = $start_at;
                    $res[$key]['completed_at'] = $completed_at;
                }
            }
        }
        
        return $res;
    }

    public function next_class_day($users_id, $class_id, $week_number){
        $res = $this->db->select('id, week_number, start_at')
                        ->where('week_number = (select min(week_number) from `users_has_classes` where week_number > '.$week_number.' AND users_id = '.$users_id.')')
                        ->where('users_id', $users_id)
                        ->get('users_has_classes')->row();
        $days = 0;
        if(!empty($res)){
            // Declare two dates 
            $start_date = strtotime($res->start_at); 
            $today_date = strtotime(date('Y-m-d H:i:s')); 
            $diff = $today_date - $start_date; 
      
            // 1 day = 24 hours 
            // 24 * 60 * 60 = 86400 seconds 
            $days = abs(round($diff / 86400));
        }
        return $days;
    }

    public function practice_files($course_id)
    {
        $where = "is_meditation_practice='1' AND meditation_practice_title is  NOT NULL AND files_id is  NOT NULL AND courses_has_files_courses_id =".$course_id;
        $res = $this->db->select('meditation_practice_title as title, files_id')
                        ->where($where)
                        ->get('course_homework')->result_array();
        return $res;
    }


    function get_pages_communities($params = array()) {
        extract($params);
        $users_id = isset($users_id) ? $users_id : FALSE;
        $page = isset($page) ? $page : 0;
        $limit = 5;
        $offset = $page * $limit;
        $info_array = array('fields' => 'pages.*');
        $info_array['start'] = $offset;
        $info_array['limit'] = $limit;                  
        $info_array['count'] = true;                  
        if (isset($where)) {
            $info_array['where'] = $where;
        }
        $info_array['order_by'] = 'pages.position';
        $info_array['order'] = 'ASC';
        $info_array['table'] = $this->tables['page'];
        $result = $this->db_model->get_data($info_array);
        if (!empty($result['result'])) {
            foreach ($result['result'] as $key => $value) {
                $result['result'][$key]['page_data'] = array();
                $type = strtolower($value['page_type']);
                $page_id = $value['id'];
                $page_detail = $this->get_page_details(strtolower($value['page_type']), $value['id'], $value['classes_id'], $users_id);
                if ($page_detail['result']) {
                    $result['result'][$key]['page_data'] = $page_detail['result'][0];
                }
            }
        }
        return $result;
    }

}
