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

require APPPATH . '/libraries/REST_Controller.php';

class Homework extends REST_Controller {

    /**
     * @desc Class Constructor
     */
    function __construct() {
        header('Access-Control-Allow-Origin: *');
        header("Access-Control-Allow-Methods: GET, POST, OPTIONS, PUT, DELETE");
        header("Access-Control-Allow-Headers: Authorization , Token");
        parent::__construct();
        $this->load->model('Homework_model', 'homework');
        $this->load->model('Classes_model', 'class');
        $this->config->load('class_validation');
        $this->check_token();
    }

   

    public function category_get() {
        $course_id = $this->input->get('course_id');
        if ($course_id) {
            $category_data = $this->homework->get_category(array('courses_id' => $course_id, 'users_id' => $this->get_user()));
            $data = array(
                'status' => 'success',
                'data' => $category_data
            );
            $this->response($data, REST_Controller::HTTP_OK);
        } else {
            $data = array(
                'msg' => "Category not found",
                'status' => 'error',
            );
            $this->response($data, REST_Controller::HTTP_OK);
        }
          
    }

    function category_homework_post() {

        $input = json_decode(file_get_contents('php://input'), true);
        $category_id = (isset($input['category_id'])) ? $input['category_id'] : FALSE;
        $users_id = $this->get_user();
        $exercise_detail=array();        
        $practice_detail=array();
        if ($category_id) {
            $exercise_detail = $this->homework->get_homework_data(array('where' => array('practice_categories_id' => $category_id), 'table' => 'homework'));   
            $exercise_detail = isset($exercise_detail['result'])?$exercise_detail['result']:array(); 

            if (!empty($exercise_detail)) {
                foreach ($exercise_detail as $key=>$value ) {
                    $file_id = $value['files_id']; 
                    $file_data = get_file($file_id, TRUE);
                    if (!empty($file_data)) {
                        $exercise_detail[$key]['url'] = $file_data['url'];                        
                        $exercise_detail[$key]['type'] = $file_data['type'];
                    } 
                    $exercise_detail[$key]['file_status'] = $this->homework->get_file_tracking($file_id, $users_id);
                }
            } 
            
            $practice_detail = $this->homework->get_practice_category_files($category_id);
            if (!empty($practice_detail)) {
                foreach ($practice_detail as $key=>$value ) {
                    $file_id = $value['files_id']; 
                    $file_data = get_file($file_id, TRUE);
                    if (!empty($file_data)) {
                        $practice_detail[$key]['url'] = $file_data['url'];                        
                        $practice_detail[$key]['type'] = $file_data['type'];
                    } 
                    $practice_detail[$key]['file_status'] = $this->homework->get_file_tracking($file_id, $users_id);
                }
                $data['practice_audio_vedio']= $practice_detail;
            }            

            $status = 'success';

            $log = "User [$users_id] accessing homework " . ucfirst($category_id) . " for class[$category_id]";
            generate_log($log); 
        } 

        $new_arr = array(                    
            'exercise_detail' => $exercise_detail,
            'practice_detail' => $practice_detail
        );

        $data = array(
            'status' => 'success',
            'data'   => $new_arr,
            //'practice_detail' => $practice_detail
        );
        $this->response($data, REST_Controller::HTTP_OK);        
    }

     function homeworks_get() {
        $course_id = $this->input->get('course_id');
        if ($course_id != NULL) {
            $classes = $this->homework->get_homework(array('course_id' => $course_id, 'users_id' => $this->get_user()));
            $data = array(
                'status' => 'success',
                'data' => $classes
            );
            $this->response($data, REST_Controller::HTTP_OK);
        } else {
            $data = array(
                'msg' => "Classes not found",
                'status' => 'error',
            );
            $this->response($data, REST_Controller::HTTP_OK);
        }
    }

    function class_list_post() {
        $input = json_decode(file_get_contents('php://input'), true);
        $classes_id = (isset($input['class_id'])) ? $input['class_id'] : FALSE;
        $users_id = $this->get_user();
        if ($classes_id != NULL) {
            $classes = $this->homework->get_homework_class(array('classes_id' => $classes_id, 'users_id' => $this->get_user()));

            if (!empty($classes['list'])) {
                foreach ($classes['list'] as $key => $val) {

                    $class_id = (isset($val['classes_id'])) ? $val['classes_id'] : FALSE;

                    if ($val['type'] == 'exercises') {
                        $class_id = $val['classes_id'];

                        $where = array('homework_exercises_id' => $val['id']);
                        $table = 'homework_has_files';

                        $exercise_detail = $this->homework->get_homework_data(array('where' => array('classes_id' => $class_id), 'table' => 'exercises'));
                        if (!empty($exercise_detail['result'])) {
                            $data['exercise_detail'] = $exercise_detail['result'][0];
                            $course_homework = $this->homework->get_homework_files(array('where' => array('homework_exercises_id' => $data['exercise_detail']['id'])));
                            $course_homework_exercise = array();
                            foreach ($course_homework as $homework) {
                                $course_homework = $this->course->get_course_homework_excercise(array('where' => array('id' => $homework['homework_id'])));
                                $course_homework['result'][0]['homework_exercises_has_files_id'] = $homework['homework_exercises_has_files_id'];
                                $course_homework_exercise[] = $course_homework['result'][0];
                            }
                            $file_detail = array('result'=>$course_homework_exercise);
                        }
                    // } elseif ( $val['type'] == 'podcast') {
                    //     $where = array('homework_podcasts_id' => $val['id']);
                    //     $table = 'podcast_file';
                    //     $select = 'homework_podcast_recordings.id as podcast_id,title,author,script,files_id,homework_podcasts_id,homework_podcasts_classes_id';
                    //     $file_detail = $this->homework->get_homework_data(array('where' => $where, 'table' => $table, 'select' => $select));
                    } elseif ( $val['type'] == 'reading') {
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
                    $status = 'success';

                    $log = "User [$users_id] accessing homework " . ucfirst($val['type']) . " for class[$class_id]";
                    generate_log($log);

                    $new_arr[] = array(                    
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
                
                $data = array(
                    'status' => 'success',
                    'data'   => $new_arr
                );
                $this->response($data, REST_Controller::HTTP_OK);
            } else {
                $data = array(
                    'msg' => "Error in fetching next page",
                    'status' => 'error',
                );
                $this->response($data, REST_Controller::HTTP_BAD_REQUEST);
            }
        } else {
            $data = array(
                'msg' => "Classes not found",
                'status' => 'error',
            );
            $this->response($data, REST_Controller::HTTP_BAD_REQUEST);
        }
    }    

    function homework_detail_post() {
        $status = 'error';
        $input = json_decode(file_get_contents('php://input'), true);
        $result = array();
        $log = "";
        $users_id = $this->get_user();
        if (!empty($input)) {
            $class_id = (isset($input['class_id'])) ? $input['class_id'] : FALSE;
            if ($input['type'] == 'exercises') {
                $class_id = $input['class_id'];
                $where = array('homework_exercises_id' => $input['exercise_id']);
                $table = 'homework_has_files';
                $exercise_detail = $this->homework->get_homework_data(array('where' => array('classes_id' => $class_id), 'table' => 'exercises'));
                if (!empty($exercise_detail['result'])) {
                    $data['exercise_detail'] = $exercise_detail['result'][0];
                    $course_homework = $this->homework->get_homework_files(array('where' => array('homework_exercises_id' => $data['exercise_detail']['id'])));
                    $course_homework_exercise = array();
                    foreach ($course_homework as $homework) {
                        $course_homework = $this->course->get_course_homework_excercise(array('where' => array('id' => $homework['homework_id'])));
                        $course_homework['result'][0]['homework_exercises_has_files_id'] = $homework['homework_exercises_has_files_id'];
                        $course_homework_exercise[] = $course_homework['result'][0];
                    }
					$file_detail = array('result'=>$course_homework_exercise);
                }
            } elseif ($input['type'] == 'podcast') {
                $where = array('homework_podcasts_id' => $input['exercise_id']);
                $table = 'podcast_file';
                $select = 'homework_podcast_recordings.id as podcast_id,title,author,script,files_id,homework_podcasts_id,homework_podcasts_classes_id';
                $file_detail = $this->homework->get_homework_data(array('where' => $where, 'table' => $table, 'select' => $select));
            } elseif ($input['type'] == 'reading') {
                $where = array('homework_readings_id' => $input['exercise_id']);
                $table = 'reading_file';
                $select = 'homework_reading_articles.id as reading_id,title,author,reading_detail,homework_readings_id,homework_readings_classes_id';
                $file_detail = $this->homework->get_homework_data(array('where' => $where, 'table' => $table, 'select' => $select));
            }

            if (!empty($file_detail['result'])) {
                foreach ($file_detail['result'] as $key => $val) {
                    if (isset($val['files_id'])) {
                        $file_id = $val['files_id'];
                        $file_data = get_file($file_id, TRUE);
                        if (!empty($file_data)) {
                            $file_detail['result'][$key]['url'] = $file_data['url'];
                        }
                        $file_detail['result'][$key]['file_status'] = $this->homework->get_file_tracking($val['files_id'], $users_id);
                    }
                }
                $result = $file_detail['result'];
            }
            $status = 'success';

            $log = "User [$users_id] accessing homework " . ucfirst($input['type']) . " for class[$class_id]";
            generate_log($log);
            $this->response(array('status' => $status, 'data' => $result), REST_Controller::HTTP_OK);
        } else {
            $data = array(
                'msg' => "Detail not found",
                'status' => 'error',
            );
            $this->response($data, REST_Controller::HTTP_BAD_REQUEST);
        }
    }

    /**
     * Method: POST
     * Header Key: Authorization
     */
    public function exercise_tracking_post() {
        $input = json_decode(file_get_contents('php://input'), true);

        if (!empty($input)) {
            $this->config->load("form_validation");
            $this->form_validation->set_data($input);
            $this->form_validation->set_rules($this->config->item("trackingExerciseForm"));
            if ($input['file_status'] == 'STARTED') {
                $this->form_validation->set_rules('files_id', 'file', 'required');
                $this->form_validation->set_rules('practice_categories_id', 'Category id', 'required');
            }

            if ($this->form_validation->run() == FALSE) {
                $data = array(
                    'status' => 'error',
                    'data' => $this->form_validation->error_array()
                );
                $this->response($data, REST_Controller::HTTP_BAD_REQUEST);
            } else {
                $input['users_id'] = $this->get_user();
                $res = $this->homework->add_exercise_tracking($input);
                $this->response($res, REST_Controller::HTTP_CREATED);
            }
        } else {
            $data = array(
                'status' => 'error',
            );
            $this->response($data, REST_Controller::HTTP_METHOD_NOT_ALLOWED);
        }
    }
    
    public function update_reading_time_post() {
        $status = 'error';
        $msg = 'Reading time not updated';
        $input = json_decode(file_get_contents('php://input'), true);
        $input['user_id'] = $this->get_user();
        if($input['user_id']){
           $result = $this->homework->update_reading_time($input);
        }
        $this->response($result, REST_Controller::HTTP_OK);
    }

}
