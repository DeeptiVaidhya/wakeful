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
class Classes extends REST_Controller {

    function __construct() {
        header('Access-Control-Allow-Origin: *');
        header("Access-Control-Allow-Methods: GET, POST, OPTIONS, PUT, DELETE");
        header("Access-Control-Allow-Headers: Authorization, Token");
        parent::__construct();
        $this->load->model('classes_model', 'classes');
        $this->load->model('Course_model', 'course');
        $this->load->model('User_model', 'users');
        $this->load->model('Auth_model', 'auth');
        $this->config->load('class_validation');
        $this->check_token();
	}
	
	public function course_get(){
		$course_id = $this->input->get('course_id');
		$data = array(
			'msg' => "Invalid request.",
			'status' => 'error',
		);
        if ($course_id != NULL) {
            $courses = $this->course->get_courses(array('where' => array('courses.id' => $course_id)));
			$audio_config = $this->config->item('sftp_assets_audios');
			$result = isset($courses['result'][0]) ? $courses['result'][0] : array();
			$result['audio_url']=$audio_config['url'];
            $data = array(
                'status' => 'success',
				'data' => $result,
            );
            $this->response($data, REST_Controller::HTTP_OK);
        } else {
            $this->response($data, REST_Controller::HTTP_BAD_REQUEST);
        }
	}

    /**
     * Method: GET
     * Header Key: Authorization
     * Value: Course Id
     */
    public function classes_get() {
        $course_id = $this->input->get('course_id');
        $users_id = $this->get_user();
        $study_id = user_has_study($users_id);
        if ($course_id != NULL && !is_null($study_id)) {
            $classes = $this->classes->get_classes(array('where' => array('course.id' => $course_id,'study_courses.study_id'=>$study_id),'study_id'=>$study_id));
            if (!empty($classes['result'])) {
                $data = $classes['result'];
                foreach ($data as $key => $val) {
                    $data[$key]['status'] = 0;
                    $class_detail = $this->classes->get_class_status(array('where' => array('classes_id' => $val['id'], 'users_id' => $users_id)));
                    if (!empty($class_detail)) {
                        $data[$key]['start_at']     = $class_detail['start_at'];
                        $data[$key]['end_at'] = $class_detail['end_at'];
                        $data[$key]['class_status'] = $class_detail['status'];
                        $today_date = time();
                        $end_date = strtotime($class_detail['end_at']);
                        if ($today_date > $end_date || ($today_date >= strtotime($class_detail['start_at']) &&  $today_date <= $end_date)) {
                            $data[$key]['status'] = 1;                            
                        }
                    }
                }
                $classes['result']=$data;
            }


            $data = array(
                'status' => 'success',
                'data' => $classes['result']
            );
            $this->response($data, REST_Controller::HTTP_OK);
        } else {
            $data = array(
                'msg' => "No class is available.",
                'status' => 'error',
            );
            $this->response($data, REST_Controller::HTTP_BAD_REQUEST);
        }
    }

    /**
     * Method: POST
     * Header Key: Authorization
     * Value: Class Id
     * Page Position: Position
     */
    public function pages_post() {
        $pagedata = array();
        $input = json_decode(file_get_contents('php://input'), true);
        $date = date('Y-m-d H:i:s');
        if (!empty($input)) {
            $input['users_id'] = $this->get_user();
            $result = $this->classes->user_has_pages($input);
            $this->response($result, REST_Controller::HTTP_OK);
        } else {
            $data = array(
                'msg' => "No class is available.",
                'status' => 'error',
            );
            $this->response(array('msg' => 'Classes not found', 'status' => 'error'), REST_Controller::HTTP_BAD_REQUEST);
        }
    }

    /**
     * Method: Get
     * Header Key: Authorization
     */
    public function dashboard_get($course_id = false) {
        $users_id = $this->get_user();
        $data['user_profile'] = $this->users->get_detail($users_id);
        $data['completed_class'] = $this->classes->get_completed_class($users_id);
        $data['meditation_minutes'] = $this->classes->get_meditation_minutes($users_id);
        $current_class = $this->classes->get_current_class($users_id, $course_id);
        $data['current_class'] = $current_class['data'];
        $data['current_class_status'] = $current_class['msg'];
        $data['current_page']='';
        if(!empty($current_class['data'])){
            $data['current_page'] = $this->classes->get_current_page($users_id, $current_class['data']->class_id);
            $data['next_class_day'] = $this->classes->next_class_day($users_id, $current_class['data']->class_id, $current_class['data']->week_number);
        }
        $data['is_new_user'] = $this->auth->visited_user($users_id);
        $this->response(array('status' => 'success', 'data' => $data), REST_Controller::HTTP_OK);
    }

    /**
     * Method: POST
     * Header Key: Authorization
     */
    public function reflection_answer_post() {
        $input = json_decode(file_get_contents('php://input'), true);
        if (!empty($input)) {
            $this->config->load("form_validation");
            $this->form_validation->set_data($input);
            $this->form_validation->set_rules($this->config->item("reflectionAnswerForm"));
            if ($this->form_validation->run() == FALSE) {
                $data = array(
                    'status' => 'error',
                    'data' => $this->form_validation->error_array()
                );
                $this->response($data, REST_Controller::HTTP_BAD_REQUEST);
            } else {
                $input['users_id'] = $this->get_user();
                $res = $this->classes->add_reflection_answer($input);
                $this->response($res, REST_Controller::HTTP_OK);
            }
        } else {
            $this->response(array('status' => 'error'), REST_Controller::HTTP_OK);
        }
    }

    /**
     * Method: POST
     * Header Key: Authorization
     */
    public function intention_answer_post() {
        $input = json_decode(file_get_contents('php://input'), true);
        if (!empty($input)) {
            $this->config->load("form_validation");
            $this->form_validation->set_data($input);
            $this->form_validation->set_rules($this->config->item("intentionAnswerForm"));
            if ($this->form_validation->run() == FALSE) {
                $data = array(
                    'status' => 'error',
                    'data' => $this->form_validation->error_array()
                );
                $this->response($data, REST_Controller::HTTP_BAD_REQUEST);
            } else {
                $input['users_id'] = $this->get_user();
                $res = $this->classes->add_intention_answer($input);
                $this->response($res, REST_Controller::HTTP_OK);
            }
        } else {
            $data = array(
                'status' => 'error',
            );
            $this->response($data, REST_Controller::HTTP_OK);
        }
    }

    /**
     * Method: POST
     * Header Key: Authorization
     */
    public function file_tracking_post() {
        $input = json_decode(file_get_contents('php://input'), true);
        if (!empty($input)) {
            $this->config->load("form_validation");
            $this->form_validation->set_data($input);
            $this->form_validation->set_rules($this->config->item("trackingForm"));
            if ($this->form_validation->run() == FALSE) {
                $data = array(
                    'status' => 'error',
                    'data' => $this->form_validation->error_array()
                );
                $this->response($data, REST_Controller::HTTP_BAD_REQUEST);
            } else {
                $res = $this->classes->add_file_tracking($input);
                $this->response($res, REST_Controller::HTTP_OK);
            }
        } else {
            $data = array(
                'status' => 'error',
            );
            $this->response($data, REST_Controller::HTTP_OK);
        }
    }

    /**
     * Method: POST
     * Header Key: Authorization
     */
    public function get_file_tracking_post() {
        $input = json_decode(file_get_contents('php://input'), true);
        if (isset($input['user_page_activity_id']) && isset($input['files_id'])) {
            $res = $this->classes->get_file_tracking($input);
            $this->response($res, REST_Controller::HTTP_OK);
        } else {
            $data = array(
                'status' => 'error',
            );
            $this->response($data, REST_Controller::HTTP_OK);
        }
    }

    /**
     * Method: POST
     * Header Key: Authorization
     */
    public function position_get() {
        $classes_id = $this->input->get('classes_id');
        if ($classes_id != '') {
            $input['classes_id'] = $classes_id;
            $input['users_id'] = $this->get_user();
            $res = $this->classes->get_position($input);
            $this->response($res, REST_Controller::HTTP_OK);
        } else {
            $data = array(
                'status' => 'error',
            );
            $this->response($data, REST_Controller::HTTP_OK);
        }
    }

    /**
     * Method: GET
     * Header Key: Authorization
     * Value: Course Id
     */
    public function feedback_get() {
        $feedback = $this->classes->get_feedback();
        $data = array(
            'status' => 'success',
            'data' => $feedback['result']
        );
        $this->response($data, REST_Controller::HTTP_OK);
        
    }

    /**
     * Method: GET
     * Header Key: Authorization
     * Value: Course Id
     */
    public function feedback_post() {
        $input = json_decode(file_get_contents('php://input'), true);

        if (!empty($input)) {
            $this->config->load("form_validation");
            $this->form_validation->set_data($input);
            for ($i = 0; $i < count($input['feedback_answers']); $i++) {
                $this->form_validation->set_rules('feedback_answers[' . $i . ']', "Answer $i", 'required', array('required' => 'This field is required.'));
                $this->form_validation->set_rules('question_id[' . $i . ']', "Question $i", 'required');
                //$this->form_validation->set_rules('course_id[' . $i . ']', "Course $i", 'required');
            }
            if ($this->form_validation->run() == FALSE) {
                $data = array(
                    'status' => 'error',
                    'data' => $this->form_validation->error_array()
                );
                $this->response($data, REST_Controller::HTTP_OK);
            } else {
                $input['users_id'] = $this->get_user();
                $res = $this->classes->add_feedback($input);
                $this->response($res, REST_Controller::HTTP_OK);
            }
        } else {
            $data = array(
                'status' => 'error',
            );
            $this->response($data, REST_Controller::HTTP_OK);
        }
    }

    public function current_class_get($course_id=false) {
        $users_id = $this->get_user();
        $result = $this->classes->get_current_class($users_id,$course_id);
        $this->response($result, REST_Controller::HTTP_OK);
    }

    public function percentage_post() {
        $users_id = $this->get_user();
        $input = json_decode(file_get_contents('php://input'), true);
        $data['data'] = array();
        $data['status'] = 'error';
        $input['users_id'] = $users_id;
        $result = $this->classes->get_percentage($input);
        if (!empty($data)) {
            $data['status'] = 'success';
            $data['data'] = $result;
        };
        $this->response($data, REST_Controller::HTTP_OK);
    }

    public function setting_get() {
        $result = array();
        // $course_id = 1;
        $study_id = $this->input->get('study_id');
        $setting = $this->course->get_setting(array('where' => array('study_id' => $study_id)));
        if (!empty($setting['result'])) {
            $result = $setting['result'];
        }
        $data['status'] = 'success';
        $data['data'] = $result;
        $this->response($data, REST_Controller::HTTP_OK);
    }

    public function check_user_class_read_count_get($course_id) {
        $users_id = $this->get_user();
        $study_id = user_has_study($users_id);
        $data = array(
            'status' => 'error',
            'msg' => 'Some class are still remaining.'
        );
        if($study_id){
            $class_count = $this->classes->get_classes(array('where' => array('course.id' => $course_id,'is_active' => 1,'study_courses.study_id'=>$study_id), 'study_id'=>$study_id));
        
            $class_id = array_column($class_count['result'], 'id');
            $users_count = $this->classes->check_user_class_read_count($users_id, $class_id);
            $user_class_id = array_column($users_count, 'classes_id');
            $result = array_diff($class_id, $user_class_id);
            if(empty($result)){
                $data = array(
                    'status' => 'success',
                    'msg' => 'user has read all the classes of this course.'
                );
            }
        }
        $this->response($data, REST_Controller::HTTP_OK);
	}
	
	/**
     * Method: POST
     * Header Key: Authorization
     * Value: Preparation time, interval_time, meditation_time, total_elapsed_time, created_at
     * Page Position: Position
     */
    public function update_meditation_time_post() {
        $input = json_decode(file_get_contents('php://input'), true);

        if (!empty($input)) {
			$input['users_id'] = $this->get_user();
            $result = $this->classes->update_meditation_time($input);
            $this->response($result, REST_Controller::HTTP_OK);
        } else {
            $this->response(array('msg' => 'No time found', 'status' => 'success'), REST_Controller::HTTP_OK);
        }
	}
	
	/**
     * Method: Get
     * Header Key: Authorization
     */
	public function classes_list_get(){
		$users_id = $this->get_user();
		if($users_id){
			$result = $this->classes->get_class_list($users_id);
			$this->response(array('msg' => 'Class List', 'status' => 'success', 'data' => $result), REST_Controller::HTTP_OK);
		} else {
            $this->response(array('msg' => 'No user found', 'status' => 'error'), REST_Controller::HTTP_OK);
        }
	}

	public function practice_files_get($course_id=false){
		if($course_id){
			$res = $this->classes->practice_files($course_id);
			if (!empty($res)) {
				foreach ($res as $key => $val) {
					if (isset($res)) {
						$file_id = $val['files_id'];
						$file_data = get_file($file_id, TRUE);
						if (!empty($file_data)) {
							$res[$key]['url'] = $file_data['url'];
						}
					}
				}
				$result = $res;
				$status = 'success';
				$log = "Homework added to practice for course[$course_id]";
				generate_log($log);
				$this->response(array('status' => $status, 'data' => $result), REST_Controller::HTTP_OK);
			} else {
				$data = array(
					'msg' => "Practice files not found",
					'status' => 'error',
				);
				$this->response($data, REST_Controller::HTTP_OK);
			}
			
		}
	}

}
