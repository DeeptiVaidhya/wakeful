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

class Dashboard extends CI_Controller {
    
    /**
     * @desc Class Constructor
     */ 
    function __construct() {
        parent::__construct();
        if($this->session->userdata('logged_in')==FALSE){
          redirect('auth');
        }
        $this->load->model('Community_model', 'community');
    }
    
    public function index() {
        // Set the title
        $this->template->title = 'Admin Dashboard';
        $this->template->content->view('dashboard');
        $this->template->publish();
    }

    /**
     * @desc Get all answer of question
     * @param type $question_id
     * @return array
     */
    public function community_board($course_id='', $study_id='') {
        get_plugins_in_template('datatable');
        $this->breadcrumbs->push('Study', 'study');
        $this->breadcrumbs->push('Community Board', 'dashboard/community-board');
        $data['breadcrumb'] = $this->breadcrumbs->show();
        $data['course_id'] = $course_id;
        $data['study_id'] = $study_id;

        $this->template->title = "Community Board";
        $this->template->content->view('community-board/list_community', $data);

        // Publish the template
        $this->template->publish();
    }

    public function get_community_data($course_id='', $study_id=''){
		$params = $this->input->get();
		if($course_id && $study_id){
            $params['course_id'] = $course_id;
			$params['study_id'] = $study_id;
		}
        $data = $this->community->get_discussion($params);
        $rowCount = $data['total'];
        $output = array( 
            "sEcho" => intval($this->input->get('sEcho')),
            "iTotalRecords" => $rowCount,
            "iTotalDisplayRecords" => $rowCount,
            "aaData" => []
        );
        $i = $this->input->get('iDisplayStart') + 1;

        foreach ($data['data'] as $val) {
            $check_lnk = '<input type="checkbox" data-msg="read" data-id="'.$val['answer_id'].'" id="check'.$val['answer_id'].'" class="checkbox" data-url="'. base_url("dashboard/read-community/{$val['answer_id']}/{$course_id}/{$study_id}") .'" title="Read">';
            
            if($val['is_read'] == 1){
                $check_lnk = '<input type="checkbox" data-msg="unread" data-id="'.$val['answer_id'].'" id="check'.$val['answer_id'].'" class="checkbox" checked data-url="'. base_url("dashboard/read-community/{$val['answer_id']}/{$course_id}/{$study_id}") .'" title="Read">';
            }
            
            $link = $check_lnk . '&nbsp;<a href="javascript:void(0)" class="comm-delete btn btn-xs btn-primary" data-msg="Censor" data-url="'. base_url("dashboard/delete-community/{$val['answer_id']}/1/{$course_id}/{$study_id}") .'" title="Censor">Censor</a>';
            if($val['is_removal'] == 1) {
                $link = $check_lnk . '&nbsp;<a href="javascript:void(0)" class="comm-delete btn btn-xs btn-primary" data-msg="Un-censor" data-url="'. base_url("dashboard/delete-community/{$val['answer_id']}/0/{$course_id}/{$study_id}") .'" title="Un-censor">Censored</a>';
            }
            $output['aaData'][] = array(
                "DT_RowId" => $val['answer_id'],
				$i++,
				($val['update_date'] != '') ? date('m/d/Y h:i A', strtotime($val['update_date'])) :  date('m/d/Y', strtotime($val['create_date'])),
                $val['unique_id'],
                $val['answer'],
                $link
            );
       }
       echo json_encode($output);
       exit;
    }

    public function delete_community($answer_id, $type, $course_id='', $study_id=''){
        if (isset($answer_id)) {
            $result = $this->community->delete_community($answer_id, $type);
            $this->session->set_flashdata($result['status'], $result['msg']);
            redirect('dashboard/community-board/'.$course_id.'/'.$study_id);
        }
    }

    public function read_community($answer_id, $course_id='', $study_id='', $type=0 ){
        if (isset($answer_id)) {
            $result = $this->community->read_community($answer_id, $type);
            $this->session->set_flashdata($result['status'], $result['msg']);
            redirect('dashboard/community-board/'.$course_id.'/'.$study_id);
        }
    }
}
