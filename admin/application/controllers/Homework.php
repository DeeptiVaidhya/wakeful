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

class Homework extends CI_Controller {

    /**
     * @desc Class Constructor
     */
    function __construct() {
        parent::__construct();
        if ($this->session->userdata('logged_in') == FALSE) {
            redirect('auth');
        }
        $this->load->model('Course_model', 'course');
        $this->load->model('Classes_model', 'classes');
        $this->load->model('Homework_model', 'homework');
        $this->config->load('class_validation');
        $this->form_validation->set_error_delimiters('<label class="error">', '</label>');
    }

    /**
     * @desc Showing list of all exercise
     *
     */
    function exercise($course_id = '', $class_id = '') {
        // Set the title
        if ($class_id && $course_id) {
            //if(is_user_has_course($course_id)){
            $exercise_id = NULL;
            $this->template->title = 'Practice';
            // Get course detail
            $course = $this->course->get_courses(array('where' => array('courses.id' => $course_id)));
            if (!empty($course['result'])) {
                $data['course'] = $course['result'][0];
            }

            $practice_category = $this->course->get_category($course_id);
            $data['practice_category'] = $practice_category;

            
            // Get class detail
            $class_detail = $this->classes->get_classes(array('where' => array('course.id' => $course_id, 'classes.id' => $class_id)));
            if (!empty($class_detail['result'])) {
                $data['class_detail'] = $class_detail['result'][0];
            }
            $course_homework_exercise = $this->course->get_course_homework_excercise(array('where' => array('courses_has_files_courses_id' => $course_id)));
            // Get exercise detail
            if (!empty($course_homework_exercise['result'])) {
                $data['course_homework_exercise'] = $course_homework_exercise['result'];
            }
            $exercise_detail = $this->homework->get_homework_data(array('where' => array('classes_id' => $class_id), 'table' => 'exercises'));
            if (!empty($exercise_detail['result'])) {
                $data['exercise_detail'] = $exercise_detail['result'][0];
                //print_r($data['exercise_detail']);
                $course_homework = $this->homework->get_homework_files(array('where' => array('homework_exercises_id' => $data['exercise_detail']['id'])));
                $course_homework_id = array();
                foreach ($course_homework as $homework){
                        array_push($course_homework_id,$homework['homework_id']);
                }
                $data['course_homework'] = $course_homework_id;
                $exercise_id = $data['exercise_detail']['id'];
            }
            $this->breadcrumbs->push('Course', 'course');
            $this->breadcrumbs->push('Classes', 'classes/list-classes/' . $course_id);
            $this->breadcrumbs->push($data['class_detail']['title'] . ' homework', $data['class_detail']['title'] . ' class pages');
            $data['breadcrumb'] = $this->breadcrumbs->show();

            if ($this->input->post()) {
                
                $this->form_validation->set_rules($this->config->item('homeworkExercise'));
                //var_dump($this->form_validation->run());
                if ($this->form_validation->run() != FALSE) {
					$input_data = $this->input->post();
                    $input_data['id'] = $exercise_id;
                    $input_data['class_id'] = $class_id;
                    $input_data['course_id'] = $course_id;
                    $result = $this->homework->save_exercise($input_data);
                    $this->session->set_flashdata($result['status'], $result['msg']);
                    redirect('homework/exercise/' . $course_id . '/' . $class_id . '/' . $page_id);
                }
            }
            $this->template->content->view('homework/exercise_detail', $data);
            // Publish the template
            $this->template->publish();
        // }else{
        //     $this->session->set_flashdata('error', 'Not have access to this course');
        //     redirect(base_url() . 'course');
        // }
        } else {
            redirect('course');
        }
    }

    function delete_data() {
        $id = $this->input->post('id');
        $type = $this->input->post('type');
        echo json_encode($this->homework->delete_data($id, $type));
    }

    /**
     * @desc Showing list of all podcast
     *
     */
    function podcast($course_id = '', $class_id = '') {
        // Set the title
        if ($class_id && $course_id) {
            //if(is_user_has_course($course_id)){    
                $podcast_id = NULL;
                $this->template->title = 'Homework Podcast';
                // Get course detail
                $course = $this->course->get_courses(array('where' => array('courses.id' => $course_id)));
                if (!empty($course['result'])) {
                    $data['course'] = $course['result'][0];
                }
                // Get class detail
                $class_detail = $this->classes->get_classes(array('where' => array('course.id' => $course_id, 'classes.id' => $class_id)));
                if (!empty($class_detail['result'])) {
                    $data['class_detail'] = $class_detail['result'][0];
                }
                // Get review detail
                $podcast_detail = $this->homework->get_homework_data(array('where' => array('classes_id' => $class_id), 'table' => 'podcasts'));


                if (!empty($podcast_detail['result'])) {
                    $data['podcast_detail'] = $podcast_detail['result'][0];
                    $select = 'homework_podcast_recordings.id as podcast_id,title,author,script,files_id,homework_podcasts_id,homework_podcasts_classes_id';
                    $file_detail = $this->homework->get_homework_data(array('where' => array('homework_podcasts_id' => $data['podcast_detail']['id']), 'table' => 'podcast_file', 'select' => $select));
                    if (!empty($file_detail['result'])) {
                        $data['podcast_detail']['podcast_data'] = $file_detail['result'];
                    }
                    $podcast_id = $data['podcast_detail']['id'];
                }
                $this->breadcrumbs->push('Course', 'course');
                $this->breadcrumbs->push('Classes', 'classes/list-classes/' . $course_id);
                $this->breadcrumbs->push($data['class_detail']['title'] . ' homework', $data['class_detail']['title'] . ' class pages');
                $data['breadcrumb'] = $this->breadcrumbs->show();

                if ($this->input->post()) {
                    $this->form_validation->set_rules($this->config->item('podcast'));
                    if ($this->form_validation->run() != FALSE) {
                        $input_data = $this->input->post();
                        $input_data['id'] = $podcast_id;
                        $input_data['class_id'] = $class_id;
                        $input_data['files'] = $_FILES;
                        $result = $this->homework->save_podcast($input_data);
                        $this->session->set_flashdata($result['status'], $result['msg']);
                        redirect('homework/podcast/' . $course_id . '/' . $class_id . '/' . $page_id);
                    }
                }

                $this->template->content->view('homework/podcast_detail', $data);
                // Publish the template
                $this->template->publish();
            // }else{
            //     $this->session->set_flashdata('error', 'Not have access to this course');
            //     redirect(base_url() . 'course');
            // }    
        }
         else {
            redirect('course');
        }
    }

    /**
     * @desc Showing list of all reading
     *
     */
    function reading($course_id = '', $class_id = '') {
        // Set the title
        $this->template->javascript->add(base_url() . 'assets/js/tinymce/tinymce.min.js');

        if ($class_id && $course_id) {
            //if(is_user_has_course($course_id)){    
                $reading_id = NULL;
                $this->template->title = 'Homework Reading';
                // Get course detail
                $course = $this->course->get_courses(array('where' => array('courses.id' => $course_id)));
                if (!empty($course['result'])) {
                    $data['course'] = $course['result'][0];
                }
                // Get class detail
                $class_detail = $this->classes->get_classes(array('where' => array('course.id' => $course_id, 'classes.id' => $class_id)));
                if (!empty($class_detail['result'])) {
                    $data['class_detail'] = $class_detail['result'][0];
                }
                // Get review detail
                $reading_detail = $this->homework->get_homework_data(array('where' => array('classes_id' => $class_id), 'table' => 'readings'));

                if (!empty($reading_detail['result'])) {
                    $data['reading_detail'] = $reading_detail['result'][0];
                    $select = 'homework_reading_articles.id as reading_id,title,author,reading_detail,homework_readings_id,homework_readings_classes_id';
                    $file_detail = $this->homework->get_homework_data(array('where' => array('homework_readings_id' => $data['reading_detail']['id']), 'table' => 'reading_file', 'select' => $select));
                    if (!empty($file_detail['result'])) {
                        $data['reading_detail']['reading_data'] = $file_detail['result'];
                    }
                    $reading_id = $data['reading_detail']['id'];
                }
                $this->breadcrumbs->push('Course', 'course');
                $this->breadcrumbs->push('Classes', 'classes/list-classes/' . $course_id);
                $this->breadcrumbs->push($data['class_detail']['title'] . ' homework', $data['class_detail']['title'] . ' class pages');
                $data['breadcrumb'] = $this->breadcrumbs->show();

                if ($this->input->post()) {
                    $this->form_validation->set_rules($this->config->item('reading'));
                    if ($this->form_validation->run() != FALSE) {
                        $input_data = $this->input->post();
                        $input_data['id'] = $reading_id;
                        $input_data['class_id'] = $class_id;
                        $result = $this->homework->save_reading($input_data);
                        $this->session->set_flashdata($result['status'], $result['msg']);
                        redirect('homework/reading/' . $course_id . '/' . $class_id . '/' . $page_id);
                    }
                }
                $this->template->content->view('homework/reading_detail', $data);
                // Publish the template
                $this->template->publish();
            /*}else{
                $this->session->set_flashdata('error', 'Not have access to this course');
                redirect(base_url() . 'course');
            } */   
        } else {
            redirect('course');
        }
    }

    function exercise1() {
        // Set the title
        $this->template->title = 'List Homework Exercises';

        // Load a view in the content partial
        //$this->template->content->view('hero', array('title' => 'Hello, world!'));
        //$news = array(); // load from model (but using a dummy array here)
        $this->template->content->view('homework/exercise_detail_1');

        // Publish the template
        $this->template->publish();
    }

    /**
     * @desc Showing list of all homework exercises
     *
     */
    function list_exercises() {
        // Set the title
        $this->template->title = 'List Homework Exercises';

        // Load a view in the content partial
        //$this->template->content->view('hero', array('title' => 'Hello, world!'));
        //$news = array(); // load from model (but using a dummy array here)
        $this->template->content->view('homework/list_homework_exercises');

        // Publish the template
        $this->template->publish();
    }

    /**
     * @desc Showing list of all reviews
     *
     */
    function add_exercise() {
        // Set the title
        $this->template->title = 'Add Homework Exercise';

        // Load a view in the content partial
        //$this->template->content->view('hero', array('title' => 'Hello, world!'));
        //$news = array(); // load from model (but using a dummy array here)
        $this->template->content->view('homework/add_homework_exercise');

        // Publish the template
        $this->template->publish();
    }

    /**
     * @desc Showing list of all homework exercises
     *
     */
    function list_readings() {
        // Set the title
        $this->template->title = 'List Homework Readings';

        // Load a view in the content partial
        //$this->template->content->view('hero', array('title' => 'Hello, world!'));
        //$news = array(); // load from model (but using a dummy array here)
        $this->template->content->view('homework/list_homework_readings');

        // Publish the template
        $this->template->publish();
    }

    /**
     * @desc Showing list of all reviews
     *
     */
    function add_reading() {
        // Set the title
        $this->template->title = 'Add Homework Reading';

        // Load a view in the content partial
        //$this->template->content->view('hero', array('title' => 'Hello, world!'));
        //$news = array(); // load from model (but using a dummy array here)
        $this->template->content->view('homework/add_homework_reading');

        // Publish the template
        $this->template->publish();
    }

    /**
     * @desc Showing list of all homework exercises
     *
     */
    function list_podcasts() {
        // Set the title
        $this->template->title = 'List Homework Podcasts';

        // Load a view in the content partial
        //$this->template->content->view('hero', array('title' => 'Hello, world!'));
        //$news = array(); // load from model (but using a dummy array here)
        $this->template->content->view('homework/list_homework_podcasts');

        // Publish the template
        $this->template->publish();
    }

    /**
     * @desc Showing list of all reviews
     *
     */
    function add_podcast() {
        // Set the title
        $this->template->title = 'Add Homework Podcast';

        // Load a view in the content partial
        //$this->template->content->view('hero', array('title' => 'Hello, world!'));
        //$news = array(); // load from model (but using a dummy array here)
        $this->template->content->view('homework/add_homework_podcast');

        // Publish the template
        $this->template->publish();
    }

    public function text_image(){
        reset($_FILES);
        $temp = current($_FILES);
        if (is_uploaded_file($temp['tmp_name'])) {
            if (preg_match("/([^\w\s\d\-_~,;:\[\]\(\).])|([\.]{2,})/", $temp['name'])) {
                header("HTTP/1.1 400 Invalid file name,Bad request");
                return;
            }
            
            // Validating File extensions
            if (! in_array(strtolower(pathinfo($temp['name'], PATHINFO_EXTENSION)), array(
                "gif",
                "jpg",
                "png"
            ))) {
                header("HTTP/1.1 400 Not an Image");
                return;
            }
            $file = "temp_".time().".".pathinfo($temp['name'], PATHINFO_EXTENSION);
            $fileName = "./assets/uploads/temp/" . $file ;
            move_uploaded_file($temp['tmp_name'], $fileName);
            // Return JSON response with the uploaded file path.
            echo json_encode(array(
                'file_path' => assets_url('uploads/temp').'/'.$file
            ));
    }
}

}
