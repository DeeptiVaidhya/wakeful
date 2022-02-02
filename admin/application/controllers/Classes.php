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
defined('BASEPATH') or exit('No direct script access allowed');

class Classes extends CI_Controller {

    /**
     * @desc Class Constructor
     */
    public function __construct() {
        parent::__construct();
        if ($this->session->userdata('logged_in') == false) {
            redirect('auth');
        }
        $this->load->model('Course_model', 'course');
        $this->load->model('Classes_model', 'classes');
  
        // Load class validation config
        $this->config->load('class_validation');
        $this->form_validation->set_error_delimiters('<label class="error">','</label>');
    }

    public function delete_page($type, $course_id = '', $class_id = '', $page_id = '') {
        if (isset($type) && isset($page_id)) {
            $result = $this->classes->delete_page($type, $page_id);
            $this->session->set_flashdata($result['status'], $result['msg']);
            redirect('classes/list-pages/' . $course_id . '/' . $class_id);
        }
    }

    public function change_status() {
        echo json_encode($this->classes->change_status($this->input->post()));
    }

    public function delete_sub_page() {
        $id = $this->input->post('id');
        $file_id = $this->input->post('file_id');
        $page_type = $this->input->post('page_type');
        $file_type = $this->input->post('file_type');
        echo json_encode($this->classes->delete_sub_page($page_type, $id, $file_id, $file_type));
    }

    public function delete_file() {
        echo "string";die;
        echo json_encode($this->classes->delete_file($this->input->post()));
    }

    public function reorder_pages() {
        echo json_encode($this->classes->reorder_pages($this->input->post('data'), $this->input->post('classes_id')));
    }

    public function reorder_classes() {
        echo json_encode($this->classes->reorder_classes($this->input->post('data'), $this->input->post('classes_id')));
    }

    public function view_page() {
        $type = $this->input->post('type');
        $page_id = $this->input->post('page_id');
        $class_id = $this->input->post('class_id');
        $response = $this->classes->get_page_details($type, $page_id, $class_id);
        $result = isset($response['result'][0]) ? $response['result'][0] : array();
        $master = array('title' => 'Title', 'header' => 'Header', 'files_id' => 'File', 'content' => 'Content', 'audio_text' => 'Audio Text', 'script' => 'Script', 'button_text' => 'Button Text',
            'pretext' => 'Pretext', 'post_text' => 'Post Text', 'question_number' => 'Question Number', 'question_text' => 'Question Text', 'question_color' => 'Question Color');
        $sub_details = array('name' => 'Name', 'quote' => 'Quote', 'files_id' => 'File', 'topic_title' => 'Topic title', 'topic_color' => 'Topic color', 'topic_text' => 'Topic text');
        if (!empty($result)) {
            $data = array('master' => $master, 'sub_details' => $sub_details, 'result' => $result);
            echo $this->template->content->view('classes/popup_page', $data, true);
        } else {
            echo 'No Data Found';
        }
        exit;
    }

    public function page($type, $course_id = '', $class_id = '', $page_id = '') {
        get_plugins_in_template('color-picker');
        $course = $this->get_course($course_id);
        $pagedata['page_data'] = false;
        $pagedata['course'] = $course['result'][0];
        $data['class_detail'] = $this->get_class($class_id, $course_id);
       
        $practice_category = $this->course->get_category($course_id);
        $pagedata['practice_category'] = $practice_category;

        $this->breadcrumbs->push('Course', 'course');
        $this->breadcrumbs->push('Classes', 'classes/list-classes/' . $course_id);
        $this->breadcrumbs->push('Pages', 'classes/list-pages/' . $course_id . '/' . $class_id);
        $this->breadcrumbs->push('Edit Page', 'Edit Page');
        $data['breadcrumb'] = $this->breadcrumbs->show();
        $page_data = $this->classes->get_page_details($type, $page_id, $class_id);
        $data['previous']=$page_data['previous'];
        $data['next']=$page_data['next'];
        $data['class_id']=$class_id;
        if (!empty($page_data['result'])) {
            $pagedata['page_data'] = $page_data['result'][0];            
        }
        if ($this->input->post()) {
            $this->form_validation->set_rules($this->config->item($type));

            if ($this->form_validation->run() != false) {
                $update_data = $this->input->post();
                $update_data['files'] = $_FILES;
                $update_data['class_id'] = $class_id;
                $update_data['page_id'] = $page_id;
                $update_data['page_type'] = $type;
                $update_data['course_id'] = $course_id;

                $result = $this->classes->update_page($update_data);
                $this->session->set_flashdata($result['status'], $result['msg']);
                redirect('classes/page/' . $type . '/' . $course_id . '/' . $class_id . '/' . $page_id);
            }
        }

        $data['page'] = $this->load->view('classes/pagelets/' . $type, $pagedata, true);
        $this->template->content->view('classes/edit_page', $data);
        $this->template->publish();
    }

    public function list_pages($course_id = '', $class_id = '') {
        //if(is_user_has_course($course_id)){
            get_plugins_in_template('datatable');
            $this->template->javascript->add('assets/js/dataTables.rowReorder.min.js');
            $course = $this->get_course($course_id);
            $data['class_detail'] = $this->get_class($class_id, $course_id);             
            $data['course'] = $course['result'][0];
            $this->breadcrumbs->push('Course', 'course');
            $this->breadcrumbs->push('Classes', 'classes/list-classes/' . $course_id);
            $this->breadcrumbs->push($data['class_detail']['title'] . ' pages', $data['class_detail']['title'] . ' class pages');
            $data['breadcrumb'] = $this->breadcrumbs->show();           

            if ($class_id != '') {
                $pages = $this->classes->get_pages(array('where' => array('pages.classes_id' => $class_id)));

                if (!empty($pages['result'])) {
                    $data['pages'] = $pages['result'];
                }
            }
            $this->template->content->view('classes/pages', $data);
            $this->template->publish();
        //  }
        // else{
        //     $this->session->set_flashdata('error', 'Not have access to this course');
        //     redirect(base_url() . 'course');
        // }
    }

    public function get_course($id) {
        if ($id) {
            $course = $this->course->get_courses(array('where' => array('courses.id' => $id)));
            if (!empty($course['result'])) {
                return $course;
            }
        }
        $this->session->set_flashdata('error', 'Please check your course details.');
        redirect(base_url() . 'course');
    }

    public function get_class($class_id, $course_id) {
        if ($class_id && $course_id) {
            $class_detail = $this->classes->get_classes(array('where' => array('course.id' => $course_id, 'classes.id' => $class_id)));
            if (!empty($class_detail['result'])) {
                return $class_detail['result'][0];
            }
        }
        $this->session->set_flashdata('warning', 'No class specified');
        redirect(base_url() . 'course');
    }

    public function add_class($course_id = '', $id = '') {
        //if(is_user_has_course($course_id)){
            get_plugins_in_template('color-picker');
            $course = $this->get_course($course_id);
            $data = array('course' => $course['result'][0]);
            $data['class'] = false;
            $data['type'] = "general";
            $data['heading'] = "Add Class";
            $data['is_edit'] = false;
            $data['is_edit_img'] = false; 

            
            $data['page'] = $this->load->view('classes/pagelets/' . $data['type'], array(),  true);
           
            if ($id != '') {
                $data['class'] = $this->get_class($id, $course_id);
                $data['heading'] = "Edit Class";
                $data['is_edit'] = true;
                $data['is_edit_img'] = true;                 
            }

            $this->breadcrumbs->push('Course', 'course');
            $this->breadcrumbs->push('Classes', 'classes/list-classes/' . $course_id);
            $this->breadcrumbs->push('Class', 'Add Class');
            $data['breadcrumb'] = $this->breadcrumbs->show();
            // Set the title
            $this->template->title = $this->lang->line("class_step_2_title");

            // $practice_category = $this->course->get_category($course_id);
            // $data['practice_category'] = $practice_category;
            // echo'<pre>';print_r($practice_category);die;


            if ($this->input->post()) {
                $data['type'] = $this->input->post('action');
                $this->form_validation->set_rules($this->config->item($data['type']));
                $this->form_validation->set_rules('class_title', 'class title', 'required');
                if ($this->form_validation->run() == false) {

                    $data['errors'] = $this->form_validation->error_array();
                    $data['page'] = $this->load->view('classes/pagelets/' . $data['type'], array(), true);
                } else {
                    $post_data = $this->input->post();
                    $post_data['files'] = $_FILES; 
                    $post_data['class_id'] = $id;
                    $post_data['course_id'] = $course_id;
                    $post_data['previous_tile_file_id'] = $this->input->post('previous_tile_file_id');
                   
                    $post_data['previous_tile_image']  = $_FILES['tile_image'];                
                    $post_data['page_type'] = $this->input->post('action');
                    $result = $this->classes->save_class($post_data);
                    $this->session->set_flashdata($result['status'], $result['msg']);
                    redirect('classes/add-class/' . $course_id . (isset($result['id']) ? '/' . $result['id'] : ''));
                }
            }
            $this->template->content->view('classes/add_class', $data);
            // Publish the template
            $this->template->publish();
        // }else{
        //     $this->session->set_flashdata('error', 'Not have access to this course');
        //     redirect(base_url() . 'course');
        // }
    }

    public function list_classes($course_id = '') {
        get_plugins_in_template('datatable');
        $this->template->javascript->add('assets/js/dataTables.rowReorder.min.js');
        // check if course detail exists or not, and make sure it comes in URL
        $data = array('course_detail' => $this->get_course($course_id));
        $data['cid'] = $course_id;
        $this->breadcrumbs->push('Course', 'course');
        $this->breadcrumbs->push('Classes', 'Classes');
        $data['breadcrumb'] = $this->breadcrumbs->show();

        //if(is_user_has_course($course_id)){
            $data['classes'] = $this->classes->get_classes(array('where' => array('course.id' => $course_id)));
            $this->template->title = $this->lang->line("classes_list_title");
            // Used for toggle switch
            $this->template->javascript->add(base_url() . 'assets/js/bootstrap-switch.min.js');
            $this->template->stylesheet->add(base_url() . 'assets/css/bootstrap-switch.min.css');
    
    
            $this->template->content->view('classes/list_classes', $data);
            // Publish the template
            $this->template->publish();
        // }
        // else{
        //     $this->session->set_flashdata('error', 'Not have access to this course');
        //     redirect(base_url() . 'course');
        // }
        // Set the title
    }

    public function get_page() {
        if ($this->input->post('type')) {
            $course_id = $this->input->post('course_id');
            $course = $this->get_course($course_id);
            $data = array('course' => $course['result'][0]);
            if ($this->input->post('type') == 'audio' || $this->input->post('type') == 'video' || $this->input->post('type') == 'podcast') {
                $practice_category = $this->course->get_category($course_id);
                $data['practice_category'] = $practice_category;
            }
            $page = $this->load->view('classes/pagelets/' . $this->input->post('type'), $data, true);
            echo $page;
            exit;
        }
    }

    function update_class() {
        echo json_encode($this->classes->update_class($this->input->post()));
        exit;
    }


    function update_image() {
        $img_id = isset($_GET['img_id']) ? $_GET['img_id'] : FALSE;
        $class_id = isset($_GET['class_id']) ? $_GET['class_id'] : NULL;

        $data = array('img_id' => $img_id,'class_id' => $class_id);
        echo json_encode($this->classes->update_image($data));
        exit;
    }

}
