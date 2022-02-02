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
class Review extends REST_Controller {

     /**
     * @desc Allow header peramater, load class model
     */
    function __construct() {
        header('Access-Control-Allow-Origin: *');
		header("Access-Control-Allow-Methods: GET, POST, OPTIONS, PUT, DELETE");
		header("Access-Control-Allow-Headers: Authorization , Token");
        parent::__construct();
        $this->load->model('Review_model', 'review');
        $this->load->model('Classes_model', 'class');
        $this->load->model('Homework_model', 'homework');
        $this->config->load('class_validation');
        $this->check_token();
    }

    /**
     * @desc Get all class with review detail accroding to course id
     * @param type $course_id
     * @return array
     */
    public function reviews_get() {
        $course_id = $this->input->get('course_id');
        $users_id = $this->get_user();
        $study_id = user_has_study($users_id);
        $review_detail = array();
        if ($course_id != NULL && !is_null($study_id)) {
            $review = $this->review->get_review(array('where' => array('classes.courses_id' => $course_id,'study_courses.study_id'=>$study_id),'study_id'=>$study_id));

            if (!empty($review['result'])) {
                $review_detail = $review['result'];
                foreach ($review_detail as $key => $val) {
                    $review_detail[$key]['status'] = 0;
					$class_detail = $this->class->get_class_status(array('where' => array('classes_id' => $val['classes_id'], 'users_id' => $users_id)));
                    if (!empty($class_detail)) {
						$today_date = time();
						$end_date = strtotime($class_detail['end_at']);
                        if ($today_date > $end_date || ($today_date >= strtotime($class_detail['start_at']) &&  $today_date <= $end_date)) {
                            $review_detail[$key]['status'] = 1;
                        }
                    }
                }
            }

            $data = array(
                'status' => 'success',
                'data' => $review_detail
            );
            $this->response($data, REST_Controller::HTTP_OK);
        } else {
            $data = array(
                'msg' => "Classes not found",
                'status' => 'success',
            );
            $this->response($data, REST_Controller::HTTP_BAD_REQUEST);
        }
    }

    /**
     * @desc Get review detail by review id
     * @param type $review_id
     * @return array
    */
    public function review_detail_get() {
        $msg = 'Review detail not found';
        $status = 'error';
        $result = array();
        $review_id = $this->input->get('id');
        $users_id = $this->get_user();
        $log = "";
        if ($review_id) {
            $review_detail = $this->review->get_review(array('where' => array('reviews.id' => $review_id)));
            if (!empty($review_detail['result'])) {
                $review_data['review_detail'] = $review_detail['result'][0];
                $file_detail = $this->review->get_review_files(array('where' => "reviews_id='".$review_id."' AND (audio.practice_type='review' OR video.practice_type='review')" ));
                if (!empty($file_detail['result'])) {
                    $file_detail = $file_detail['result'];
                    foreach ($file_detail as $key => $value) {
                        $file_id = ($value['page_type']=='AUDIO' || $value['page_type']=='PODCAST') ? $value['audio_id'] : $value['video_id']; 
                        $file_data = get_file($file_id, TRUE);
                        if (!empty($file_data)) {
                            $file_detail[$key]['url'] = $file_data['url'];
                            $file_detail[$key]['type'] = $file_data['type'];
                        }
                        $file_detail[$key]['file_status'] = $this->review->get_file_tracking($file_id, $users_id);
                        $file_detail[$key]['files_id'] = $file_id; 
                    }
                    $review_data['review_detail']['review_data'] = $file_detail;
                }

                $classes = $this->homework->get_homework_class(array('classes_id' => $review_data['review_detail']['classes_id'], 'users_id' => $users_id)); 

                if (!empty($classes['list'])) {
                    foreach ($classes['list'] as $key => $val) {
                        $class_id = (isset($val['classes_id'])) ? $val['classes_id'] : FALSE;
                        if ($val['type'] == 'reading') {
                            $where = array('homework_readings_id' => $val['id']);
                            $table = 'reading_file';
                            $select = 'homework_reading_articles.id as reading_id,title,author,reading_detail,homework_readings_id,homework_readings_classes_id';
                            $file_detail = $this->homework->get_homework_data(array('where' => $where, 'table' => $table, 'select' => $select));
                        }

                        if (!empty($file_detail['result'])) {
                            foreach ($file_detail['result'] as $key1 => $val1) {
                                if (isset($val1['files_id'])) {
                                    $file_id = $val1['files_id'];
                                    $file_data = get_file($file_id, TRUE);
                                    if (!empty($file_data)) {
                                        $file_detail['result'][$key1]['url'] = $file_data['url'];
                                    }
                                    $file_detail['result'][$key1]['file_status'] = $this->homework->get_file_tracking($val1['files_id'], $users_id);
                                }
                            }
                            $result = $file_detail['result'];
                        }
                        $review_data['review_detail']['homework_data'][] = array(                    
                            'classes_id' => $val['classes_id'],
                            'id'         => $val['id'],
                            'intro_text' => $val['intro_text'],
                            'title'      => $val['title'],
                            'type'       => $val['type'],
                            'updated_at' => $val['updated_at'], 
                            'created_at' => $val['created_at'],
                            'exercises_detail' => $result,
                        );
                    }
                }



            $msg = 'Review detail';
            $status = 'success';
            $result = $review_data['review_detail'];
            $log = "User [$users_id] accessing review ".$review_data['review_detail']['title']." for class [".$review_data['review_detail']['classes_id']."]";
            }
        }
        generate_log($log);
        $this->response(array('msg' => $msg, 'status' => $status, 'data' => $result), REST_Controller::HTTP_OK);
    }

    /**
     * @desc Add tracking time of audio and video file
     * @param type $tracking data
     * @return array
    */
    public function review_tracking_post() {
        $input = json_decode(file_get_contents('php://input'), true);

        if (!empty($input)) {
            $this->config->load("form_validation");
            $this->form_validation->set_data($input);

            $this->form_validation->set_rules($this->config->item("trackingReviewForm"));
            if ($input['file_status'] == 'STARTED') {
                $this->form_validation->set_rules('files_id', 'file', 'required');
                $this->form_validation->set_rules('reviews_id', 'reviews id', 'required');
                $this->form_validation->set_rules('classes_id', 'classes id', 'required');
            }

            if ($this->form_validation->run() == FALSE) {
                $data = array(
                    'status' => 'error',
                    'data' => $this->form_validation->error_array()
                );
                $this->response($data, REST_Controller::HTTP_BAD_REQUEST);
            } else {
                $input['users_id'] = $this->get_user();
                $res = $this->review->add_review_tracking($input);
                $this->response($res, REST_Controller::HTTP_CREATED);
            }
        } else {
            $data = array(
                'status' => 'error',
            );
            $this->response($data, REST_Controller::HTTP_METHOD_NOT_ALLOWED);
        }
    }

}
