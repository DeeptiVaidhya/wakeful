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

class Course extends CI_Controller {

    /**
     * @desc Class Constructor
     */
    function __construct() {
        parent::__construct();
        if ($this->session->userdata('logged_in') == FALSE) {
            redirect('auth');
        }
       

        $this->load->model('Course_model', 'course');
        $this->load->model('Organization_model', 'organization');
        $this->load->model('Classes_model', 'classes');
        /** add error delimiters * */
        $this->form_validation->set_error_delimiters('<label class="error">', '</label>');
    }

    function index($id = false) {
        if($this->session->userdata('logged_in')->user_type == 1){
            redirect('user/list-admin');
        }

        // Set the title
        get_plugins_in_template('datatable');

        $data['login_user_detail'] = $this->session->userdata('logged_in');

        $this->template->title = 'Courses';
        $data['organizations'] = $this->organization->get_organizations();
        $data['subheading'] = 'Add Course';
        $data['course_detail'] = array();
        $this->breadcrumbs->push('Course', 'course');
        if ($id) {
            $data['subheading'] = 'Edit Course';
            $this->breadcrumbs->push('edit', 'Course');
            $course_detail = $this->course->get_courses(array('where' => array('courses.id' => $id)));
            if (!empty($course_detail['result'])) {
                $data['course_detail'] = $course_detail['result'][0];
            }
        }
        $data['breadcrumb'] = $this->breadcrumbs->show();

        if ($this->input->post()) {
            $this->form_validation->set_rules($this->config->item("courseForm"));
            $this->form_validation->set_rules('title', 'Title', 'required|callback_check_course');
            if ($this->form_validation->run() != FALSE) {
                $course_data = $this->input->post();
                $course_data['files'] = $_FILES;
                $result = $this->course->save_course($course_data);
                $this->session->set_flashdata($result['status'], $result['msg']);
                redirect('course');
            }
        }
        $this->template->content->view('courses/courses', $data);
        
        // Publish the template
        $this->template->publish();
    }

    public function add_course() {
        $this->template->javascript->add(base_url() . 'assets/js/my_course.js');
        $this->template->title = 'Courses';
        $data['organizations'] = $this->organization->get_organizations();
        $data['subheading'] = 'Add Course';
        $data['course_detail'] = array();
        $this->breadcrumbs->push('Course', 'course');
        $this->breadcrumbs->push('Add', 'Course');
        $data['breadcrumb'] = $this->breadcrumbs->show();

        if ($this->input->post()) {
            $this->form_validation->set_rules($this->config->item("courseForm"));
            $this->form_validation->set_rules('title', 'Title', 'required|callback_check_course');

            if ($this->form_validation->run() != FALSE) {
                $course_data = $this->input->post();
                $course_data['files'] = $_FILES;
                $result = $this->course->save_course($course_data);
                $this->session->set_flashdata($result['status'], $result['msg']);
                redirect('course');
            }
        }
        $this->template->content->view('courses/add', $data);
        
        // Publish the template
        $this->template->publish();
    }

    public function edit_course($id) {
        $this->template->javascript->add(base_url() . 'assets/js/my_course.js');
        
            // Set the title
            get_plugins_in_template('datatable');
            $this->template->title = 'Courses';
            $data['organizations'] = $this->organization->get_organizations();
            $data['subheading'] = 'Edit Course';
            $data['course_detail'] = array();
            $this->breadcrumbs->push('Course', 'course');
            if ($id) {
                $data['subheading'] = 'Edit Course';
                $this->breadcrumbs->push('edit', 'Course');
                $course_detail = $this->course->get_courses(array('where' => array('courses.id' => $id)));
                if (!empty($course_detail['result'])) {
                    $course_detail = $course_detail['result'][0];
                    $data['course_detail'] = $course_detail;
                }
                if(is_user_has_organization($course_detail['organizations_id'])){
                    if ($this->input->post()) {
                        $this->form_validation->set_rules($this->config->item("courseForm"));
                        if ($course_detail['title'] != $this->input->post('title')) {
                            $this->form_validation->set_rules('title', 'Title', 'required|callback_check_course');
                        }
                        if ($this->form_validation->run() != FALSE) {
                            $course_data = $this->input->post();
                            $course_data['files'] = $_FILES;
                            $course_data['course_id'] = $id;
                            $result = $this->course->save_course($course_data);
                            $this->session->set_flashdata($result['status'], $result['msg']);
                            redirect('course/edit-course/'.$id);
                        }
                    }
                } else{
                    $this->session->set_flashdata('error', 'Not have access to this course');
                    redirect(base_url() . 'course');
                }
            }
            $data['breadcrumb'] = $this->breadcrumbs->show();
            $this->template->content->view('courses/edit', $data);
            // Publish the template
            $this->template->publish();
        
    }

    public function get_courses_data() {
        $login_user_detail = $this->session->userdata('logged_in');
        $params = $this->input->get();
        if($this->session->userdata('logged_in')->user_type == 2){
            $params['admin_id'] = $this->session->userdata('logged_in')->id;
        }
        $data = $this->course->get_courses($params);
        $rowCount = $data['total'];
        $output = array(
            "sEcho" => intval($this->input->get('sEcho')),
            "iTotalRecords" => $rowCount,
            "iTotalDisplayRecords" => $rowCount,
            "aaData" => []
        );
        $i = $this->input->get('iDisplayStart') + 1;
        $is_published = array('No', 'Yes');
        foreach ($data['result'] as $val) {
             // if(is_user_has_organization($val['organizations_id']))
             // {
                $link = '<a href="' . base_url('course/edit-course/' . $val['id']) . '" title="Edit"><i class="fa fa-edit"></i></a>&nbsp<a href="' . base_url('course/add-homework-exercise/' . $val['id']) . '" title="Add Homework Exercise"><i class="fa fa-upload"></i></a>&nbsp<a href="' . base_url('course/add_practice_category/' . $val['id']) . '" title="Add Practice Category"><i class="fa fa-list"></i></a>';

                $title = '<a href="' . base_url('classes/list-classes/' . $val['id']) . '" title=' . $val['title'] . '>' . $val['title'] . '</a>';
                $output['aaData'][] = array(
                    "DT_RowId" => $val['id'],
                    $i++,
                    $title,
                    $val['org_title'],
                    $is_published[$val['is_published']],
                    // $val['slug'],
                    $link
                );
            //}
            
        }

        echo json_encode($output);
        exit;
    }

    /**
     * Check Course
     * It is a callback function take user course name and organization id  to check if curse exist in selected organization or not
     * @return Bool
     * */
    function check_course() {
        $title = $this->input->post('title');
        $organizations_id = $this->input->post('organizations_id');
        $course_id = $this->input->post('course_id');

        if ($title != '' && $organizations_id != '') {
            $count = $this->course->is_exist_course($title, $organizations_id, $course_id);
            if ($count > 0) {
                $this->form_validation->set_message('check_course', 'This course is already exist in selected organization');
                return FALSE;
            } else {
                return TRUE;
            }
        }
    }

    function check_setting() {
        $feature = $this->input->post('feature');
        $course_id = $this->input->post('course_id');
        if ($feature != '' && $course_id != '') {
            $count = $this->course->is_exist_setting($feature, $course_id);
            if ($count > 0) {
                $this->form_validation->set_message('check_setting', 'This feature is already exist in selected course');
                return FALSE;
            } else {
                return TRUE;
            }
        }
    }

    function feedback() {

        //if(is_user_has_course($id)){
                // Set the title
            get_plugins_in_template('datatable');
            $this->template->title = 'Feedback';
            $data['feedback_question'] = array();
            $data['subheading'] = 'Feedback';
            $feedback_question = $this->classes->get_feedback();
            if (!empty($feedback_question['result'])) {
                $data['feedback_question'] = $feedback_question['result'];
            }
            
            $this->breadcrumbs->push('Feedback', 'Fedback');

            $data['breadcrumb'] = $this->breadcrumbs->show();
            $this->template->content->view('feedbacks', $data);
            // Publish the template
            $this->template->publish();
        // }else{
        //     $this->session->set_flashdata('error', 'Not have access to this course');
        //     redirect(base_url() . 'course');
        // }
        
    }

    public function get_courses_feedback() {
        $data = $this->course->get_feedbacks();
        $data_arr = array();
        $questionArr = array();
        $feedback_question = $this->classes->get_feedback();
        
        $result = array();
        if (!empty($data['result'])) {
            
            foreach ($data['result'] as $key => $value) {
                $username = isset($value['username']) ? $value['username'] : (isset($value['email']) ? isset($value['email']) : '');
                if (!$username) {
                    continue;
                }
                $username = ucfirst(aes_256_decrypt($username));

                $uniq_key = $value['user_id'].'__'.$value['created_at'];
                if(!isset($result[$uniq_key])){
                    $result[$uniq_key]=array('user'=>$username,'cdate'=>$value['created_at'],'quest'=>array());
                }
                foreach($feedback_question['result'] as $qval) {
                    if($value['question_id']==$qval['question_id']){
                        $result[$uniq_key]['quest'][$qval['question_id']]=$value['answer'];
                    }
                }
                
            }
        }

       
        $sno=1;
        foreach($result as $key => $value) {
            $row = array($sno++,$value['user'],$value['cdate']);
            foreach($feedback_question['result'] as $qval) {
                $row[]=isset($value['quest'][$qval['question_id']])?$value['quest'][$qval['question_id']]:'';

            }
            $aadata[]=$row;
        }



        $rowCount = count($aadata);
        $output = array(
            "sEcho" => intval($this->input->get('sEcho')),
            "iTotalRecords" => $rowCount,
            "iTotalDisplayRecords" => $rowCount,
            "aaData" => []
        );
        $i = $this->input->get('iDisplayStart') + 1;



        $output['aaData'] = $aadata;

        echo json_encode($output);
        die;
    }

    public function change_setting() {
        echo json_encode($this->course->change_setting($this->input->post()));
    }

    public function delete_file() {
        echo json_encode($this->course->delete_file($this->input->post()));
    }
    
    public function add_homework_exercise($course_id = '', $homework_id = '') {
        // Set the title
        //if(is_user_has_course($course_id)){
            get_plugins_in_template('datatable');
            $this->template->title = 'Add Practice';
            $data['course_id']=$course_id?$course_id:($course_id=$this->input->post('course_id'));
            $data['homework_id']=$homework_id?$homework_id:($homework_id=$this->input->post('homework_id'));
            $data['category_id']= $this->input->post('practice_categories_id');
            
            $course_detail = $this->course->get_courses(array('where'=>array('courses.id'=>$course_id)));
            $data['course_detail'] = $course_detail['result'][0];

            $data['practice_category'] = $this->course->get_category($course_id); 

            $data['subheading'] = 'Add Practice';
            $this->breadcrumbs->push('Course','course');
            if ($course_id) {
                if($homework_id){
                    $data['subheading'] = 'Edit Practice';
                    $this->breadcrumbs->push('Edit Practice', 'Course');
                    $homework_detail = $this->course->get_course_homework_detail($homework_id);
                    if (!empty($homework_detail)) {
                        $data['homework_detail'] = $homework_detail;
                    }
                }
            }
            
            if ($this->input->post()){
                if ($this->form_validation->run('courseHomeworkExercise') != FALSE) {
                    $homework_data = $this->input->post();
                    $homework_data['files'] = $_FILES;
                    $homework_data['course_id'] = $course_id;
                    $homework_data['homework_id'] = $homework_id;
                    $category_id = $data['category_id'];
                    $homework_data['category_id'] = $category_id;
                    $result = $this->course->save_course_homework_exercise($homework_data);
                    $this->session->set_flashdata($result['status'], $result['msg']);
                    redirect('course/add-homework-exercise/'.$course_id.($homework_id?'/'.$homework_id:''));
                }
            }


            $data['breadcrumb'] = $this->breadcrumbs->show();
            $this->template->content->view('courses/add_homework_exercise', $data);
            // Publish the template
            $this->template->publish();
        // }    
        // else{
        //     $this->session->set_flashdata('error', 'Not have access to this course');
        //     redirect(base_url() . 'course');
        // }
    }



    public function add_practice_category($course_id = '',$category_id = '') {
            get_plugins_in_template('datatable');
            $course = $this->course->get_courses(array('where'=>array('courses.id'=>$course_id)));
            $data['course'] = $course['result'][0];

            $data['subheading'] = "Add Practice Category";
            $this->breadcrumbs->push('Course', 'course');
            $data['course_id']=$course_id?$course_id:($course_id=$this->input->post('course_id'));
            $data['category_id']=$category_id?$category_id:($category_id=$this->input->post('category_id'));

            $data['category_detail'] = $this->course->get_practice_data($course_id); 
            if ($category_id != '') {
                $data['subheading'] = 'Edit Practice Category';

                $category_detail = $this->course->get_practice_category($category_id);
                if (!empty($category_detail)) {
                    $data['edit_category'] = $category_detail;
                }else{
                    $data['edit_category'] = '';
                }                
            }

            $this->breadcrumbs->push('Category', 'Add Practice Category');
            $data['breadcrumb'] = $this->breadcrumbs->show();
            // Set the title
            $this->template->title = $this->lang->line("class_step_2_title");
            if ($this->input->post()) {
                if ($this->form_validation->run('practiceCategory') != FALSE) {
                    $post_data = $this->input->post();                    
                    $post_data['files'] = $_FILES;
                    $post_data['category_id'] = $category_id;
                    $post_data['course_id'] = $course_id;
                    $post_data['previous_cat_file_id'] = $this->input->post('previous_cat_file_id');;  
                    $result = $this->course->save_practice_category($post_data);
                    $this->session->set_flashdata($result['status'], $result['msg']);
                    redirect('course/add_practice_category/' . $course_id . (isset($category_id) ? '/' . $category_id : ''));

                }
            }
             $data['breadcrumb'] = $this->breadcrumbs->show();
            $this->template->content->view('courses/add_practice_category', $data);
            // Publish the template
            $this->template->publish();
    }
    
    public function get_course_homework_excercise_detail() {
        if ($this->input->post()) {
            $homework_id = $this->input->post('homework_id');
            $result = $this->course->get_course_homework_detail($homework_id);
            if (!empty($result)) {
                $data = array('status' => 'success', 'data' => $result);
                echo $this->template->content->view('courses/popup_page', $data, true);
            } else {
                $data = array('status' => 'error', 'msg' => "Excercise not found");
            }
        } else {
            $data = array('status' => 'error', 'msg' => "Excercise not found");
        }
        //echo json_encode($data);
        exit;
    }
    
    
    public function get_course_homework_excercise_data($course_id) {
        $params = $this->input->get();
        $data = $this->course->get_course_homework_excercise(array('courses_has_files_courses_id' => $course_id),$params);
        $rowCount = $data['total'];
        $output = array(
            "sEcho" => intval($this->input->get('sEcho')),
            "iTotalRecords" => $rowCount,
            "iTotalDisplayRecords" => $rowCount,
            "aaData" => []
        );
        $i = $this->input->get('iDisplayStart') + 1;
        if ($data['result']) {
            foreach ($data['result'] as $val) {
                $link = '<a class="homework-detail" href="#" data-params=' . json_encode(array("homework_id" => $val['id'])) . ' data-toggle="modal" data-target="#view_homework_excercise" data-url="' . base_url() . 'course/get-course-homework-excercise-detail"><i class="fa fa-eye"></i></a>&nbsp<a href="' . base_url('course/add-homework-exercise/'.$course_id.'/' . $val['id']) . '" title="Edit"><i class="fa fa-edit"></i></a>';
                $output['aaData'][] = array(
                    "DT_RowId" => $val['id'],
                    $i++,
                    $val['title'] ,
                    $val['tip'],
                    $link
                );
            }
        }
        echo json_encode($output);
        die;
    }

    public function get_course_practice_category_data($courses_id) {
        $params = $this->input->get();
        $data = $this->course->get_practice_category_data(array('courses_id' => $courses_id),$params);
        $rowCount = $data['total'];
        $output = array(
            "sEcho" => intval($this->input->get('sEcho')),
            "iTotalRecords" => $rowCount,
            "iTotalDisplayRecords" => $rowCount,
            "aaData" => []
        );
        $i = $this->input->get('iDisplayStart') + 1;
        foreach ($data['result'] as $val) {
            $link = '<a href="' . base_url('course/add-practice-category/'.$courses_id.'/' . $val['id']) . '" title="Edit"><i class="fa fa-edit"></i></a>';
            $output['aaData'][] = array(
                "DT_RowId" => $val['id'],
                $i++,
                $val['label'],
                $link
            );
        }
        echo json_encode($output);
        exit;
    }

    public function get_users_has_course_data(){
        $params = $this->input->post();
        $user_id = $params['user_id'];
        $course_list = $this->course->get_courses();
        $data['course_list'] = $course_list['result'];
        $course_id_array = $this->course->get_user_has_course($user_id);
        $data['user_has_course_list'] = $course_id_array;
        $data = array('status' => 'success', 'data' => $data);
        echo json_encode($data);
    }
    public function user_has_course(){
        $params = $this->input->post();
        $user_id = $params['user_id'];
        $this->course->delete_users_has_courses($user_id);
        if(isset($params['course_has_users'])){
            $this->course->insert_users_has_courses($params['course_has_users'],$user_id);
        }
        $data = array('status' => 'success');
        echo json_encode($data);
    }

    public function get_slug(){
        $params = $this->input->get();
        $slug_name = $this->course->get_slug(array('where' => array('courses.slug' => "'".$params['slug']."'")));
        $slug_name['result']['config'] = $this->config->item('app_url');
        echo json_encode($slug_name);
    }
}
