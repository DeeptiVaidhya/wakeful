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
if (!defined('BASEPATH'))
    exit('No direct script access allowed');
/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * @desc Course_model used to list courses
 *
 * @author Ideavate
 */
class Study_model extends CI_Model {

    var $tables = array();

    public function __construct() {
        parent::__construct();
        $this->tables = array('courses' => 'courses', 'setting' => 'settings','users_has_courses'=>'users_has_courses', 'study' => 'study', 'study_courses' => 'study_has_courses');
    }

    /**
     * @desc Function is used to add/update course data
     * @param type $params, having title and/or id if updating course data
     * @return array having message and status
     */
    function save_study($params = array()) {

        $login_user_detail = $this->session->userdata('logged_in');
        $user_id =$login_user_detail->id ;

        extract($params);
        $study_id = isset($study_id) ? $study_id : false;

        //array_map("myfunction",$class);
        $data = array("name" => $name,"cc_email" => $cc_email, 'created_at' => date('Y-m-d H:i:s'));
        if(!$study_id) {
           $this->db->insert($this->tables['study'], $data);
           $study_id = $this->db->insert_id(); 
           $statusMsg = 'saved';
        } else {
            $this->db->update($this->tables['study'], $data, array('id' => $study_id));
            $this->db->delete($this->tables['study_courses'], array('study_id' => $study_id, 'courses_id', $courses_id));
            $statusMsg = 'updated';
        }
        
        
        foreach ($class_id as $key => $value) {
            $subData = array("study_id" => $study_id, "courses_id" => $courses_id, 'classes_id' => $value, 'position' => $key, 'created_at' => date('Y-m-d H:i:s'));
            $this->db->insert($this->tables['study_courses'], $subData);
        }
        $status = 'success';
        $msg = 'Study '.$statusMsg.' successfully.';
        return array('status' => $status, 'msg' => $msg);
    }

    

    /**
     * @desc function to get all courses or course list by filter
     * @param type $params
     * @return list of courses
     */
    function get_studies($params = array()) {

        extract($params);
        $detail = isset($detail) ? $detail : false;
        $col_sort = array("study.id", "study.name");
        $info_array['fields'] = 'study.id,study.name,study.cc_email,study_courses.courses_id, study_courses.classes_id';
        $order_by = "study.id";
        $group_by = "study_courses.study_id";
        $order = 'DESC';
        $start = FALSE;
        $search_array = FALSE;
        $limit = false;
        $join = array(
            array(
                'table' => 'study_has_courses study_courses',
                'on' => 'study_courses.study_id = study.id',
                'type' => 'INNER'
            )
        );

        if (isset($params['iSortCol_0'])) {
            $index = $params['iSortCol_0'];
            $order = $params['sSortDir_0'] === 'asc' ? 'asc' : 'desc';
            $order_by = $col_sort[$index];
        }
        if (isset($params['sSearch']) && $params['sSearch'] != "") {
            $words = $params['sSearch'];
            $search_array = array();
            for ($i = 0; $i < count($col_sort); $i++) {
                $search_array[$col_sort[$i]] = $words;
                $info_array['like'] = $search_array;
            }
        }
        if (isset($params['iDisplayStart']) && $params['iDisplayLength'] != '-1') {
            $start = intval($params['iDisplayStart']);
            $limit = intval($params['iDisplayLength']);
        }


        if (isset($where)) {
            $info_array['where'] = $where;
        }

        if(!$detail){
            $info_array['group_by'] = $group_by;
        }
        $info_array['order_by'] = $order_by;
        $info_array['order'] = $order;
        $info_array['start'] = $start;
        $info_array['limit'] = $limit;
        $info_array['join'] = $join;
        $info_array['count'] = true;
        $info_array['debug'] = false;
        $info_array['table'] = $this->tables['study'];
        $result = $this->db_model->get_data($info_array);

        if (!empty($result['result']) && $detail) {
            $class = array();
            foreach ($result['result'] as $key => $value) {
                $class[] = $value['classes_id'];
                $courses_id = $value['courses_id'];
                $id = $value['id'];
                $name = $value['name'];
                $cc_email = $value['cc_email'];
            }
            unset($result['result']);
            $result['result']['id'] = $id;
            $result['result']['name'] = $name;
            $result['result']['courses_id'] = $courses_id;
            $result['result']['class'] = $class;
            $result['result']['cc_email'] = $cc_email;
        }
        return $result;
    }
}
