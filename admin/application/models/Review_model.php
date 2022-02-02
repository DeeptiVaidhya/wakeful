<?php
 /*

 * Copyright (c) 2003-2017 BrightOutcome Inc.  All rights reserved.
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
 * Description of Review_model
 *
 * @author Ideavate
 */
class Review_model extends CI_Model {

    var $tables = array();
    public function __construct() {
        parent::__construct();
        $this->tables = array('class' => 'classes', 'files' => 'files', 'reviews' => 'reviews', 'reviews_has_files' => 'reviews_has_files', 'users_has_classes' => 'users_has_classes', 'file_tracking' => 'users_has_reviews_has_files');
    }
    /**
     * @desc Get all file tracking detail of review
     * @param type $file_id
     * @param type $user_id
     * @return array
     */
    function get_file_tracking($file_id = false, $user_id = false) {
        if ($file_id && $user_id) {
            $info_array = array('fields' => '*');
            $info_array['where'] = array('files_id' => $file_id, 'users_id' => $user_id);
            $info_array['table'] = $this->tables['file_tracking'];
            $result = $this->db_model->get_data($info_array);
            if (!empty($result['result'])) {
                return $result['result'][0];
            } else {
                return array();
            }
        }
    }
    /**
     * @desc Add all file tracking detail of review
     * @param type $params
     * @return array
     */
    function add_review_tracking($params = array()){
        $status = "error";
        $msg = "Error while saving tracking";
        extract($params);
        $second_conversion = 1000;
        $input['current_time'] = isset($current_time) ? floor($current_time / $second_conversion) : 0;
        $input['left_time'] = isset($left_time) ? floor($left_time / $second_conversion) : 0;
        $input['total_time'] = isset($total_time) ? floor($total_time / $second_conversion) : 0;
        $file_status = isset($file_status) ? $file_status : 'STARTED';
        $reviews_files_id = isset($reviews_files_id) ? $reviews_files_id : FALSE;
        $users_id = isset($users_id) ? $users_id : NULL;
        $query = $this->db->where(array('reviews_has_files_id' => $reviews_files_id, 'users_id' => $users_id))
                ->get($this->tables['file_tracking']);
        $this->db->trans_start();
        if ($query->num_rows() > 0) {
            $status = 'success';
            $tracking_detail = $query->row();
            if ($tracking_detail->status != 'COMPLETED') {
                $input['status'] = isset($file_status) ? $file_status : 'STARTED';
                $input['total_elapsed_time'] = $input['current_time'];
            } else {
                $ct = $input['current_time'] - $tracking_detail->current_time;
                $ct = $ct >= 0 ? $ct : 0;
                $input['total_elapsed_time'] = $tracking_detail->total_elapsed_time + $ct;
            }
            if (isset($file_status) && $file_status == 'COMPLETED') {
                $input['current_time'] = 0;
            }
            $this->db->update($this->tables['file_tracking'], $input, array('id' => $tracking_detail->id));
        } else {
            $input['reviews_has_files_id'] = $reviews_files_id;
            $input['users_id'] = $users_id;
            $input['status'] = 'STARTED';
            $input['reviews_id'] = isset($reviews_id) ? $reviews_id : FALSE;
            $input['classes_id'] = isset($classes_id) ? $classes_id : FALSE;
            $input['files_id'] = isset($files_id) ? $files_id : FALSE;
            $this->db->insert($this->tables['file_tracking'], $input);
            $status = 'success';
        }
        $this->db->trans_complete();
        return array('status' => $status);
    }
    /**
     * @desc Get review detail of a review
     * @param type $params
     * @return array
     */
    function get_review($params = array()){
        extract($params);
        $study_id = isset($study_id) ? $study_id : null;

        $info_array = array('fields' => 'reviews.id,reviews.title,reviews.intro_text,reviews.classes_id,classes.title as class_title,classes.tile_image as tile_image,classes.is_active as is_active');

        if (isset($where)) {
            $info_array['where'] = $where;
        }

        if(!is_null($study_id)){
            $info_array['join'] = array(
                array(
                    'table' => 'reviews',
                    'on' => 'reviews.classes_id = classes.id',
                    'type' => 'LEFT'
                ),
                array(
                    'table' => 'study_has_courses study_courses',
                    'on' => 'study_courses.classes_id = classes.id',
                    'type' => 'LEFT'
                )
            );
            
            $info_array['order_by'] = 'study_courses.position';
        } else {
            $info_array['join'] = array(
                array(
                    'table' => 'reviews',
                    'on' => 'reviews.classes_id = classes.id',
                    'type' => 'LEFT'
                )
            );
            $info_array['order_by'] = 'classes.position';
        }

        $info_array['table'] = $this->tables['class'];
        //return $this->db_model->get_data($info_array);

        $review_data = $this->db_model->get_data($info_array);
        if ($review_data['result'] && !empty($review_data['result'])) {
            foreach ($review_data['result'] as $key => $val) {                
                if (isset($val['tile_image'])) {
                    $file_id = $val['tile_image'];
                    $file_data = get_file($file_id, TRUE);
                    if (!empty($file_data)) {
                        $review_data['result'][$key]['url'] = $file_data['url'];
                    }
                }
            }
        }
        return $review_data;
    }
    /**
     * @desc Get review file detail of a review
     * @param type $params
     * @return array
     */
    function get_review_files($params=array()) {
        extract($params);
        $review_detail = array();
        $info_array = array(
        'fields' => 'reviews_has_files.id,reviews_has_files.pretext,reviews_id,reviews_has_files.pages_id,audio.files_id as audio_id,video.files_id as video_id,p.position,p.page_type,audio.script as audio_script,video.script as video_script,audio.title as audio_title,video.title as video_title,audio.practice_title as audio_practice_title,audio.practice_text as audio_practice_text ,video.practice_text as video_practice_text,video.practice_title as video_practice_title,audio.is_podcast_page as is_podcast');

        if (isset($where)) {  
            $info_array['where'] = $where;
        } 

        $info_array['join'] = array(
            array(
                'table' => 'pages as p',
                'on' => 'p.id = reviews_has_files.pages_id',
                'type' => 'LEFT'
            ),
            array(
                'table' => 'practice_audio as audio',
                'on' => 'audio.pages_id = p.id',
                'type' => 'LEFT'
            ),
            array(
                'table' => 'educational_video as video',
                'on' => 'video.pages_id = p.id',
                'type' => 'LEFT'
            )
        );

        $info_array['table'] = $this->tables['reviews_has_files'];
        $info_array['order_by'] = 'p.position';

        $review_detail = $this->db_model->get_data($info_array);
        return $review_detail;
    }


    function get_homework_data($params = array()) {
        extract($params);
        $homework_data = array();
        $select = isset($select) ? $select : FALSE;

        if ($select) {
            $info_array = array('fields' => $select . ",");
        } else {
            $info_array = array('fields' => "*,");
        }

        if (isset($where)) {
            $info_array['where'] = $where;
        }

        $info_array['table'] = $this->tables[$table];

        $homework_data = $this->db_model->get_data($info_array);
        if ($homework_data['result'] && !empty($homework_data['result'])) {
            foreach ($homework_data['result'] as $key => $val) {
                if (isset($val['files_id'])) {
                    $file_id = $val['files_id'];
                    $file_data = get_file($file_id, TRUE);
                    if (!empty($file_data)) {
                        $homework_data['result'][$key]['url'] = $file_data['url'];
                    }
                }
            }
        }
        return $homework_data;
    }



    /**
    * @desc Save review detail a class
    * @param type $params
    * @return array
    */
    function save_review($params = array()) {
        extract($params);
        $class_id = isset($class_id) ? $class_id : '';
        $created_at = date('Y-m-d H:i:s');
        $data = array('title' => isset($title) ? $title : '','intro_text' => isset($intro_text) ? $intro_text : '');
        $transactional_data = array();
        $data['classes_id'] = $class_id;
        $transactional_data = array();
        if (!empty($sub_id)) {
            foreach ($sub_id as $key => $value) {
                $transactional_data[$key]['sub_id'] = $value;
                $transactional_data[$key]['pretext'] = isset($pretext[$key]) ? $pretext[$key] : '';
            }
        }
        $this->db->trans_start();
        if ($class_id) {
            if ($id != NULL) {
                $data['updated_at'] = $created_at;
                $this->db->update($this->tables['reviews'], $data, array('id' => $id));
                $current_id = $id;
            } else {
                $data['created_at'] = $created_at;
                $this->db->insert($this->tables['reviews'], $data);
                $current_id = $this->db->insert_id();
            }
        }

        $flag = 0;
        if (!empty($transactional_data) && isset($current_id) && $current_id) {
            $sub_data = array();
            foreach ($transactional_data as $key => $value) {
                $sub_id = $transactional_data[$key]['sub_id'];
                unset($transactional_data[$key]['sub_id']);
                if ($sub_id) {
                    $count = 0;
                    $count = $this->db->where('reviews_has_files_id', $current_id)
                                    ->count_all_results($this->tables['file_tracking']) > 0;
                    $this->db->update($this->tables['reviews_has_files'], $transactional_data[$key], array('id' => $sub_id));
                } else {
                    $transactional_data[$key]['reviews_id'] = $current_id;
                    $transactional_data[$key]['reviews_classes_id'] = $class_id;
                    $transactional_data[$key]['created_at'] = $created_at;
                    $this->db->insert($this->tables['reviews_has_files'], $transactional_data[$key]);
                }
            }
        }

        $this->db->trans_complete();
        $status = 'error';
        $msg = 'Error while saving review details.';
        if ($this->db->trans_status() !== FALSE) {
            if ($flag > 0) {
                $msg = 'Review details updated successfully. But file can not be modified as user already watched this file.';
                $status = 'success';
            } else {
                $status = 'success';
                $msg = 'Review details saved successfully.';
            }
        }
        return array('status' => $status, 'msg' => $msg);
    }
}
