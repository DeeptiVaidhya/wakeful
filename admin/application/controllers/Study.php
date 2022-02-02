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

class Study extends CI_Controller {

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
        $this->load->model('Study_model', 'study');
        /** add error delimiters * */
        $this->form_validation->set_error_delimiters('<label class="error">', '</label>');
    }

    function index($id = false) {
        // Set the title
        get_plugins_in_template('datatable');
        $this->template->title = 'Studies';
        $this->breadcrumbs->push('Study', 'study');
        $data['breadcrumb'] = $this->breadcrumbs->show();
        $this->template->content->view('studies/studies', $data);
        
        // Publish the template
        $this->template->publish();
    }

    public function add_study() {
        $this->template->javascript->add(base_url() . 'assets/js/my_course.js');
        $this->template->title = 'Study';
        $data['courses'] = $this->course->get_courses();
        $data['subheading'] = 'Add Study';
        $this->breadcrumbs->push('Study', 'study');
        $this->breadcrumbs->push('Add', 'Study');
        $data['breadcrumb'] = $this->breadcrumbs->show();

        if ($this->input->post()) {
            $this->form_validation->set_rules($this->config->item("studyForm"));

            if ($this->form_validation->run() != FALSE) {
                $study_data = $this->input->post();
                $result = $this->study->save_study($study_data);
                $this->session->set_flashdata($result['status'], $result['msg']);
                redirect('study');
            }
        }

        $this->template->content->view('studies/add', $data);
        
        // Publish the template
        $this->template->publish();
    }

    public function edit_study($id) {
        $this->template->javascript->add(base_url() . 'assets/js/my_course.js');
        //if(is_user_has_course($id)){
            // Set the title
            get_plugins_in_template('datatable');
            $this->template->title = 'Studies';
            $data['courses'] = $this->course->get_courses();
            $data['subheading'] = 'Edit Study';
            $data['study_detail'] = array();
            $this->breadcrumbs->push('Study', 'study');
            if ($id) {
                $study_detail = $this->study->get_studies(array('where' => array('study.id' => $id), 'detail' => true));
                if (!empty($study_detail['result'])) {
                    $study_detail = $study_detail['result'];
                    $data['study_detail'] = $study_detail;
                    $data['class'] = $this->classes->get_classes(array('where' => array('course.id' => $study_detail['courses_id'],'classes.is_active' => 1)));
                }

                if ($this->input->post()) {

                    $this->form_validation->set_rules($this->config->item("studyForm"));
                    if ($this->form_validation->run() != FALSE) {
                        $study_data = $this->input->post();
                        $study_data['study_id'] = $id;
                        $result = $this->study->save_study($study_data);
                        $this->session->set_flashdata($result['status'], $result['msg']);
                        redirect('study');
                    }
                }
            }
            $data['breadcrumb'] = $this->breadcrumbs->show();
            $this->template->content->view('studies/edit', $data);
            // Publish the template
            $this->template->publish();
        // }
        // else{
        //     $this->session->set_flashdata('error', 'Not have access to this course');
        //     redirect(base_url() . 'course');
        // }
    }

    public function get_studies_data() {
		$login_user_detail = $this->session->userdata('logged_in');
        $params = $this->input->get();
        $data = $this->study->get_studies($params);
        $rowCount = $data['total'];
        $output = array(
            "sEcho" => intval($this->input->get('sEcho')),
            "iTotalRecords" => $rowCount,
            "iTotalDisplayRecords" => $rowCount,
            "aaData" => []
        );
        $i = $this->input->get('iDisplayStart') + 1;
        
        foreach ($data['result'] as $val) {
                $link = '<a href="' . base_url('study/edit-study/' . $val['id']) . '" title="Edit"><i class="fa fa-edit"></i></a>&nbsp<a href="' . base_url('study/setting/' . $val['id']) . '" title="Setting"><i class="fa fa-cog"></i></a>&nbsp<a class="comm-delete btn btn-xs btn-primary" href="' . base_url('dashboard/community-board/' . $val['courses_id'].'/'. $val['id']) . '" title="Community">Community</a>&nbsp<a class="comm-delete btn btn-xs btn-primary" href="' . base_url('user/list-users/' . $val['courses_id'].'/'. $val['id']) . '" title="Participants">Participants</a>&nbsp<a class="comm-delete btn btn-xs btn-primary" href="' . base_url('settings/site-settings/' . $val['id']) . '" title="Access Code">Access Code</a>';

                $output['aaData'][] = array(
                    "DT_RowId" => $val['id'],
                    $i++,
                    $val['name'],
                    $link
                );
        }

        echo json_encode($output);
        exit;
    }

    public function get_class() {
        if ($this->input->post('course_id')) {
            $course_id = $this->input->post('course_id');
            $class = $this->classes->get_classes(array('where' => array('course.id' => $course_id,'classes.is_active' => 1)));
            echo json_encode($class['result']);
            exit;
        }
    }

    function setting($id = false) {
        // Set the title
        //if(is_user_has_course($id)){
            get_plugins_in_template('datatable');
            $this->template->title = 'Setting';
            //$data['subheading'] = 'Add Course';
            $data['course_detail'] = array();
            $data['feedback_question'] = array();
            $this->breadcrumbs->push('Study', 'study');
            if ($id) {
                $data['subheading'] = 'Setting';

                $course_detail = $this->study->get_studies(array('where' => array('study.id' => $id)));

                if (!empty($course_detail['result'])) {
                    $data['course_detail'] = $course_detail['result'][0];
                }
                $settings = $this->course->get_setting(array('where' => array('study_id' => $id)));
                $db_setting = [];
                if (!empty($settings['result'])) {
                    $db_setting = $settings['result'];
                }
                $course_settings= $this->config->item('course_settings'); 
                $new_couse_settings = array();
                $flagNotFound=false;
                foreach($course_settings as $key => $setting){
                    if(count($db_setting) > 0 ){
                        foreach($db_setting as $dt => $v){
                            if($setting['key'] == $v['key']){
                                $new_couse_settings[$key] = $v;
                                $flagNotFound=false;
                                break;
                            } else {
                                $flagNotFound=true;
                            }
                            
                        }
                    } else {
                        $flagNotFound = true;
                    }
                    if($flagNotFound) {
                        $new_couse_settings[$key] = $setting;
                    }
                }
                $data['settings'] = $new_couse_settings;
            }
            // Used for toggle switch
            $this->template->javascript->add(base_url() . 'assets/js/bootstrap-switch.min.js');
            $this->template->stylesheet->add(base_url() . 'assets/css/bootstrap-switch.min.css');

            $this->breadcrumbs->push('Setting', 'setting');

            $data['breadcrumb'] = $this->breadcrumbs->show();
            $this->template->content->view('setting', $data);
            // Publish the template
            $this->template->publish();
        /*}
        else{
            $this->session->set_flashdata('error', 'Not have access to this course');
            redirect(base_url() . 'course');
        }*/
    }
   
}