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

class Review extends CI_Controller {

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
        $this->load->model('Review_model', 'review');
        $this->config->load('class_validation');
        $this->form_validation->set_error_delimiters('<label class="error">', '</label>');
    }

    /**
     * @desc Showing list of all reviews
     *
     */
    function review_detail($course_id = '', $class_id = '') {
        //if(is_user_has_course($course_id)){
        // Set the title
        if ($class_id && $course_id) {
            $review_id = NULL;
            $this->template->title = 'Review detail';
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
            $review_detail = $this->review->get_review(array('where' => array('reviews.classes_id' => $class_id)));
            if (!empty($review_detail['result'])) {
                $data['review_detail'] = $review_detail['result'][0];
                $review_id = $data['review_detail']['id'];
                $file_detail = $this->review->get_review_files(array('where' => "reviews_id='".$review_id."' AND (audio.practice_type='review' OR video.practice_type='review')" ));
                if (!empty($file_detail['result'])) {
                    $data['review_detail']['review_data'] = $file_detail['result'];
                }
                
            }

            $this->breadcrumbs->push('Course', 'course');
            $this->breadcrumbs->push('Classes', 'classes/list-classes/' . $course_id);
            $this->breadcrumbs->push($data['class_detail']['title'] . ' review', $data['class_detail']['title'] . ' class pages');
            $data['breadcrumb'] = $this->breadcrumbs->show();
            if ($this->input->post()) {
                $this->form_validation->set_rules($this->config->item('review'));
                if ($this->form_validation->run() != FALSE) {
                    $input_data = $this->input->post();
                    $input_data['id'] = $review_id;
                    $input_data['class_id'] = $class_id;
                    $result = $this->review->save_review($input_data);
                    $this->session->set_flashdata($result['status'], $result['msg']);
                    redirect('review/review-detail/' . $course_id . '/' . $class_id . '/' . $page_id);
                }
            }

            $this->template->content->view('review_detail', $data);
            // Publish the template
            $this->template->publish();
        } else {
            redirect('course');
        }
    // }
    //     else{
    //         $this->session->set_flashdata('error', 'Not have access to this course');
    //         redirect(base_url() . 'course');
    //     }
    }

    /**
     * @desc Showing list of all reviews
     *
     */
    function review_detail1() {
        // Set the title
        $this->template->title = 'Review detail';

        // Load a view in the content partial
        //$this->template->content->view('hero', array('title' => 'Hello, world!'));
        //$news = array(); // load from model (but using a dummy array here)
        $this->template->content->view('review_detail_1');

        // Publish the template
        $this->template->publish();
    }

}
