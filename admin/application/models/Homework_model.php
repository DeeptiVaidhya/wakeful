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
if (!defined('BASEPATH'))
    exit('No direct script access allowed');
/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of Homework_model
 *
 * @author Homework_model
 */
class Homework_model extends CI_Model {

    var $tables = array();

    public function __construct() {
        parent::__construct();
        $this->tables = array('class' => 'classes', 'exercises' => 'homework_exercises', 'homework_exercises_has_files' => 'homework_exercises_has_files', 'podcasts' => 'homework_podcasts', 'podcast_file' => 'homework_podcast_recordings', 'readings' => 'homework_readings', 'reading_file' => 'homework_reading_articles', 'file_tracking' => 'users_has_exercises_has_files','category' => 'practice_categories','audio' => 'practice_audio','homework' => 'course_homework','avaccess'=>'audio_video_access');
        $this->load->model('Classes_model');
    }

    /**
     * @desc get file track detail of exercise 
     * @param type $params
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

    public function get_meditation_data($users_id){
        if($users_id){
            $res = $this->db->select('meditation_date, meditation_time, total_elapsed_time')
                ->where('users_id', $users_id)
                ->get('meditation')->result_array();
            return $res;
        }
    }

    /**
     * @desc add file track detail of exercise 
     * @param type $params
     * @return array
     */
    function add_exercise_tracking($params = array()) {
        $status = "error";
        $msg = "Error while saving tracking";
        extract($params);
        $second_conversion = 1000;
        $input['current_time'] = isset($current_time) ? floor($current_time / $second_conversion) : 0;
        $input['left_time'] = isset($left_time) ? floor($left_time / $second_conversion) : 0;
        $input['total_time'] = isset($total_time) ? floor($total_time / $second_conversion) : 0;
        $file_status = isset($file_status) ? $file_status : 'STARTED';
        $practice_categories_id = isset($practice_categories_id) ? $practice_categories_id : NULL;
        $users_id = isset($users_id) ? $users_id : NULL;
        $files_id = isset($files_id) ? $files_id : NULL;
        $last_avaccess_id = isset($last_avaccess_id) ? $last_avaccess_id : NULL;

        $where = array('practice_categories_id' => $practice_categories_id, 'users_id' => $users_id,'files_id'=> $files_id);
        $query = $this->db->where($where)->get($this->tables['file_tracking']);
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

            $this->db->update($this->tables['file_tracking'], $input, array('id' => $tracking_detail->id));
            
            if ($last_avaccess_id != false) {
                 // update avaccess
                $data['time_spent'] = $input['current_time'];
                if (isset($file_status) && $file_status == 'COMPLETED') {                
                    $data['status'] = 1;   
                }            
                $data['users_has_exercises_has_files_id'] = $tracking_detail->id;
                $data['completed_at'] = date('Y-m-d H:i:s');
                $this->db->update($this->tables['avaccess'], $data, array('id' => $last_avaccess_id));               
                $last_avaccess_id = $last_avaccess_id;
                //end                
            }else{
                $data['time_spent'] = $input['current_time'];
                $data['status'] = 0;                
                $data['users_has_exercises_has_files_id'] = $tracking_detail->id;
                $data['started_at'] = date('Y-m-d H:i:s');
                $data['completed_at'] = date('Y-m-d H:i:s');
                $this->db->insert($this->tables['avaccess'], $data);                 
                $last_avaccess_id = $this->db->insert_id();
            }
        } else {
            $input['status'] = 'STARTED';
            $input['files_id'] = isset($files_id) ? $files_id : FALSE;
            $input['homework_exercises_has_files_id'] = (isset($homework_exercises_has_files_id) && $homework_exercises_has_files_id) ? $homework_exercises_has_files_id : NULL;
            $input['classes_id'] = isset($classes_id) ? $classes_id : FALSE;
            $input['practice_categories_id'] = $practice_categories_id;
            $input['users_id'] = $users_id;
            if ($practice_categories_id) {
                $insert_file = $this->db->insert($this->tables['file_tracking'], $input);
                if ($insert_file) {
                    $data['time_spent'] = $input['current_time'];
                    $data['users_has_exercises_has_files_id'] = $this->db->insert_id();
                    $data['started_at'] = date('Y-m-d H:i:s');
                    $this->db->insert($this->tables['avaccess'], $data);
                    $last_avaccess_id = $this->db->insert_id();                                    
                }                
            }
            $status = 'success';
        }

        $this->db->trans_complete();
        if ($this->db->trans_status() !== false) {
            return array('status' => 'success','data'=>array('last_avaccess_id'=>$last_avaccess_id));
        }
        return array('status' => 'failed','data'=>array());
    }

    /**
     * @desc get homework list of class 
     * @param type $params
     * @return array
     */
    function get_homework($params = array()) {
        extract($params);
        $info_array = array('fields' => 'classes.id as classes_id,classes.title as class_title');

        $course_id = isset($course_id) ? $course_id : '';
        $users_id = isset($users_id) ? $users_id : '';

        $study_id = user_has_study($users_id);

        if ($course_id != '') {
            $info_array['where'] = array('classes.courses_id' => $course_id, 'classes.is_active' => 1);
        }
        if($study_id){
            $info_array['join'] = array(
                array(
                    'table' => 'study_has_courses study_courses',
                    'on' => 'study_courses.classes_id = classes.id',
                    'type' => 'LEFT'
                )
            );
            if ($course_id != '') {
                $info_array['where'] = array('classes.courses_id' => $course_id, 'classes.is_active' => 1, 'study_courses.study_id' => $study_id);
            }
            $info_array['order_by'] = 'study_courses.position';
        } else {
            $info_array['order_by'] = 'classes.position';

        }

        $info_array['table'] = $this->tables['class'];
        $result = $this->db_model->get_data($info_array);
        $result = $result['result'];
        if (!empty($result)) {
            foreach ($result as $key => $val) {
                $detail_info_arr = array();
                $detail_info_arr['where'] = array('classes_id' => $val['classes_id'], 'title!=' => NULL);
                $class_detail = $this->Classes_model->get_class_status(array('where' => array('classes_id' => $val['classes_id'], 'users_id' => $users_id)));
                
                $result[$key]['status'] = 0;
                if (!empty($class_detail)) {
                    $today_date = time();
                    $end_date = strtotime($class_detail['end_at']);
                    if ($today_date > $end_date || ($today_date >= strtotime($class_detail['start_at']) &&  $today_date <= $end_date)) {
                        $result[$key]['status'] = 1;
                    }
                }

                $result[$key]['list'] = array();
                $detail_info_arr['table'] = 'exercises';
                $exercise_detail = $this->get_homework_data($detail_info_arr);
                if (!empty($exercise_detail['result'])) {
                    $exercise_detail['result'][0]['type'] = 'exercises';
                    array_push($result[$key]['list'], $exercise_detail['result'][0]);
                }

                $detail_info_arr['table'] = 'podcasts';
                $podcast_detail = $this->get_homework_data($detail_info_arr);

                if (!empty($podcast_detail['result'])) {
                    $podcast_detail['result'][0]['type'] = 'podcast';
                    array_push($result[$key]['list'], $podcast_detail['result'][0]);
                }
                $detail_info_arr['table'] = 'readings';
                $reading_detail = $this->get_homework_data($detail_info_arr);

                if (!empty($reading_detail['result'])) {
                    $reading_detail['result'][0]['type'] = 'reading';
                    array_push($result[$key]['list'], $reading_detail['result'][0]);
                }
            }
        }
        return $result;
    }


    function get_category($params = array()) {
        extract($params);
        $info_array = array('fields' => 'practice_categories.id as category_id, practice_categories.label as title, practice_categories.image_name');

        $course_id = isset($course_id) ? $course_id : '';
        $users_id = isset($users_id) ? $users_id : '';

        $study_id = user_has_study($users_id);

        if ($course_id != '') {
            $info_array['where'] = array('practice_categories.courses_id' => $course_id);
        }
       if ($course_id != '') {
            $info_array['where'] = array('practice_categories.courses_id' => $course_id);
        }

        $info_array['table'] = $this->tables['category'];
        $result = $this->db_model->get_data($info_array);
        $result = $result['result'];

        if (!empty($result)) {
            foreach ($result as $key => $val) {
                // $detail_info_arr = array();
                // $detail_info_arr['where'] = array('practice_categories_id' => $val['category_id'], 'title!=' => NULL);

                // $result[$key]['list'] = array();
                // $detail_info_arr['table'] = 'homework';
                // $exercise_detail = $this->get_homework_data($detail_info_arr);
                // if (!empty($exercise_detail['result'])) {
                //     $exercise_detail['result'][0]['type'] = 'exercises';
                //     array_push($result[$key]['list'], $exercise_detail['result'][0]);
                // }
                if (isset($val['image_name'])) {
                    $file_id = $val['image_name'];
                    $file_data = get_file($file_id, TRUE);
                    if (!empty($file_data)) {
                        $result[$key]['url'] = $file_data['url'];
                    }
                }
            }
        }
        return $result;
    }


    function get_practice_category_files($category_id = false) {
        //extract($params);
        $this->db->select('files_id,id as audio_id, script ,title,practice_title,practice_text,pages_classes_id as classes_id, practice_categories_id as category_id, "audios" as "type"');

        $this->db->where(array('practice_categories_id' => $category_id,'practice_type' => 'practice','is_podcast_page' => 0));
        $audio_data = $this->db->get('practice_audio')->result_array();

        $this->db->select('files_id,id as video_id,script,title,practice_text,practice_title,pages_classes_id as classes_id,practice_categories_id as category_id, "videos" as "type"');

         $this->db->where(array('practice_categories_id' => $category_id,'practice_type' => 'practice'));

        $video_data = $this->db->get('educational_video')->result_array();

        $this->db->select('files_id,id as audio_id, script ,title,practice_title,practice_text,pages_classes_id as classes_id, practice_categories_id as category_id, "podcast" as "type"');

        $this->db->where(array('practice_categories_id' => $category_id,'practice_type' => 'practice','is_podcast_page' => 1));

        $podcast_data = $this->db->get('practice_audio')->result_array();

        $data = array_merge($audio_data,$video_data,$podcast_data);  
        return $data;
    }


    function get_homework_class($params = array()) {
        extract($params);
        $info_array = array('fields' => 'classes.id as classes_id,classes.title as class_title');

        $classes_id = isset($classes_id) ? $classes_id : '';
        $detail_info_arr = array();
        $detail_info_arr['where'] = array('classes_id' => $classes_id, 'title!=' => NULL);
        //$class_detail = $this->Classes_model->get_class_status(array('where' => array('classes_id' => $classes_id, 'users_id' => $users_id)));
        
        
        $result['list'] = array();
        $detail_info_arr['table'] = 'exercises';
        $exercise_detail = $this->get_homework_data($detail_info_arr);
        if (!empty($exercise_detail['result'])) {
            $exercise_detail['result'][0]['type'] = 'exercises';
            array_push($result['list'], $exercise_detail['result'][0]);
        }

        $detail_info_arr['table'] = 'podcasts';
        $podcast_detail = $this->get_homework_data($detail_info_arr);

        if (!empty($podcast_detail['result'])) {
            $podcast_detail['result'][0]['type'] = 'podcast';
            array_push($result['list'], $podcast_detail['result'][0]);
        }
        
        $detail_info_arr['table'] = 'readings';
        $reading_detail = $this->get_homework_data($detail_info_arr);

        if (!empty($reading_detail['result'])) {
            $reading_detail['result'][0]['type'] = 'reading';
            array_push($result['list'], $reading_detail['result'][0]);
        }
        return $result;
    }

    /**
     * @desc get homework detail of class 
     * @param type $params
     * @return array
     */
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
     * @desc Get review file detail of a review
     * @param type $params
     * @return array
     */
    function get_homework_files($params = array()) {
        extract($params);
        $course_homework = array();
        $info_array = array('fields' => 'homework_exercises_has_files.id as homework_exercises_has_files_id,homework_exercises_has_files.course_homework_id as homework_id,homework_exercises_has_files.id as homework_exercises_has_files_id, h.classes_id');
        if (isset($where)) {
            $info_array['where'] = $where;
        }
        $info_array['join'] = array(
            array(
                'table' => 'homework_exercises as h',
                'on' => 'h.id = homework_exercises_has_files.course_homework_id',
                'type' => 'LEFT'
            )
        );

        $info_array['table'] = $this->tables['homework_exercises_has_files'];
        //$info_array['debug'] = true;

        $course_homework = $this->db_model->get_data($info_array);
        return $course_homework['result'];
    }

    /**
     * @desc Save exercise detail of class 
     * @param type $params
     * @return array
     */
  //   function save_exercise($params = array()) {
        // //print_r($params);die;
  //       extract($params);
        
  //       $status = 'error';
  //       $msg = 'Error while saving exercise details.';
  //       $class_id = isset($class_id) ? $class_id : '';
  //       $practice_categories_id = isset($practice_categories_id) ? $practice_categories_id : '';
  //       $created_at = date('Y-m-d H:i:s');
  //       $data = array('title' => isset($title) ? $title : '', 'intro_text' => isset($intro_text) ? $intro_text : '', 'classes_id' => isset($class_id) ? $class_id : '','practice_categories_id' => isset($practice_categories_id) ? $practice_categories_id : '');
  //       $transactional_data = array();
  //       $data['classes_id'] = $class_id;

  //       $this->db->trans_start();
  //       if ($class_id) {
  //           if ($id != NULL) {
  //               $data['updated_at'] = $created_at;
  //               $this->db->update($this->tables['exercises'], $data, array('id' => $id));
  //               $current_id = $id;
  //           } else {
  //               $data['created_at'] = $created_at;
  //               $this->db->insert($this->tables['exercises'], $data);
  //               $current_id = $this->db->insert_id();
  //           }
  //       }

  //       $flag = 0;
  //       if (isset($current_id) && $current_id) {
  //           $info_array = array('fields' => 'id');
  //           $info_array['where'] = array('homework_exercises_id' => $current_id);
  //           $info_array['table'] = $this->tables['homework_exercises_has_files'];

  //           $result = $this->db_model->get_data($info_array);
  //           if (!empty($result['result'])) {
  //               $homework_exercise_id = $result['result'];
                
  //               $id_array = array();
  //               foreach ($homework_exercise_id as $value) {
  //                   $id_array[] = $value['id'];
  //               }
  //               $this->db->where_in('homework_exercises_has_files_id', $id_array);
  //               $this->db->delete('users_has_exercises_has_files');
  //           }
            
  //           $this->db->delete($this->tables['homework_exercises_has_files'], array('homework_exercises_id' => $current_id));

  //           foreach ($homework_id as $key => $value) {
  //               $homework_data = array('homework_exercises_id' => isset($current_id) ? $current_id : '', 'course_homework_id' => $value, 'created_at' => date('Y-m-d H:i:s'));
  //               $this->db->insert($this->tables['homework_exercises_has_files'], $homework_data);
        //  }

        //  $practice_data = array('is_meditation_practice' => '0', 'meditation_practice_title' => null);
        //              // update data into course_homework table
        //  $this->db->update('course_homework', $practice_data, array('courses_has_files_courses_id' => $course_id));
        //  foreach ($practice_title as $pKey => $pValue) {
        //          if($pValue && isset($practice_file_id[$pKey]) && $practice_file_id[$pKey]){
        //              $practice_data = array('is_meditation_practice' => '1', 'meditation_practice_title' => $pValue);
        //              // update data into course_homework table
        //              $this->db->update('course_homework', $practice_data, array('id' => $pKey));
        //          }   
        //  }
  //       }

  //       $this->db->trans_complete();

  //       if ($this->db->trans_status() !== FALSE) {
  //           if ($flag > 0) {
  //               $msg = 'Exercise details updated successfully. But file can not be modified as user already used this file.';
  //               $status = 'success';
  //           } else {
  //               $status = 'success';
  //               $msg = 'Exercise details saved successfully.';
  //           }
  //       }

  //       return array('status' => $status, 'msg' => $msg);
  //   }

    /**
     * @desc Save podcast detail of class 
     * @param type $params
     * @return array
     */
    function save_podcast($params = array()) {
        extract($params);

        $class_id = isset($class_id) ? $class_id : '';
        $created_at = date('Y-m-d H:i:s');
        $data = array('title' => isset($title) ? $title : '', 'intro_text' => isset($intro_text) ? $intro_text : '');
        $transactional_data = array();
        $data['classes_id'] = $class_id;
        $transactional_data = array();
        $_FILES = re_arrange_files($files['podcast_link'], 'file');
        if (!empty($podcast_title)) {
            foreach ($podcast_title as $key => $value) {
                $transactional_data[$key]['title'] = $value;
                $transactional_data[$key]['author'] = isset($podcast_author[$key]) ? $podcast_author[$key] : '';
                $transactional_data[$key]['script'] = isset($podcast_script[$key]) ? $podcast_script[$key] : '';
                $transactional_data[$key]['sub_id'] = isset($sub_id[$key]) ? $sub_id[$key] : '';
                if ($_FILES["file_" . $key]['name']) {
                    $transactional_data[$key]['files_id'] = $this->db_model->upload_audio_videos($_FILES, "file_" . $key, 'audios');
                } else {
                    if (isset($previous_file_id[$key]) && $previous_file_id[$key])
                        $transactional_data[$key]['files_id'] = $previous_file_id[$key];
                }
            }
        }


        $this->db->trans_start();
        if ($class_id) {
            if ($id != NULL) {
                $data['updated_at'] = $created_at;
                $this->db->update($this->tables['podcasts'], $data, array('id' => $id));
                $current_id = $id;
            } else {
                $data['created_at'] = $created_at;
                $this->db->insert($this->tables['podcasts'], $data);
                $current_id = $this->db->insert_id();
            }
        }



        if (!empty($transactional_data) && isset($current_id) && $current_id) {

            $sub_data = array();
            foreach ($transactional_data as $key => $value) {
                $sub_id = $transactional_data[$key]['sub_id'];
                unset($transactional_data[$key]['sub_id']);
                if ($sub_id != '') {
                    $this->db->update($this->tables['podcast_file'], $transactional_data[$key], array('id' => $sub_id));
                } else {
                    $transactional_data[$key]['homework_podcasts_id'] = $current_id;
                    $transactional_data[$key]['homework_podcasts_classes_id'] = $class_id;
                    $transactional_data[$key]['created_at'] = $created_at;
                    $this->db->insert($this->tables['podcast_file'], $transactional_data[$key]);
                }
            }
        }

        $this->db->trans_complete();
        $status = 'error';
        $msg = 'Error while saving podcast details.';
        if ($this->db->trans_status() !== FALSE) {
            $status = 'success';
            $msg = 'Podcast details saved successfully.';
        }
        return array('status' => $status, 'msg' => $msg);
    }

    /**
     * @desc Save reading detail of class 
     * @param type $params
     * @return array
     */
    function save_reading($params = array()) {
        extract($params);

        $class_id = isset($class_id) ? $class_id : '';
        $created_at = date('Y-m-d H:i:s');
        $data = array('title' => isset($title) ? $title : '', 'intro_text' => isset($intro_text) ? $intro_text : '');
        $transactional_data = array();
        $data['classes_id'] = $class_id;
        $transactional_data = array();
        if (!empty($reading_title)) {
            foreach ($reading_title as $key => $value) {
                $transactional_data[$key]['title'] = $value;
                $transactional_data[$key]['author'] = isset($reading_author[$key]) ? $reading_author[$key] : '';
                $transactional_data[$key]['reading_detail'] = isset($reading_detail[$key]) ? $reading_detail[$key] : '';
                $transactional_data[$key]['sub_id'] = isset($sub_id[$key]) ? $sub_id[$key] : '';
            }
        }


        $this->db->trans_start();
        if ($class_id) {
            if ($id != NULL) {
                $data['updated_at'] = $created_at;
                $this->db->update($this->tables['readings'], $data, array('id' => $id));
                $current_id = $id;
            } else {
                $data['created_at'] = $created_at;
                $this->db->insert($this->tables['readings'], $data);
                $current_id = $this->db->insert_id();
            }
        }



        if (!empty($transactional_data) && isset($current_id) && $current_id) {

            $sub_data = array();
            foreach ($transactional_data as $key => $value) {
                $sub_id = $transactional_data[$key]['sub_id'];
                unset($transactional_data[$key]['sub_id']);
                if ($sub_id != '') {
                    $this->db->update($this->tables['reading_file'], $transactional_data[$key], array('id' => $sub_id));
                } else {
                    $transactional_data[$key]['homework_readings_id'] = $current_id;
                    $transactional_data[$key]['homework_readings_classes_id'] = $class_id;
                    $transactional_data[$key]['created_at'] = $created_at;
                    $this->db->insert($this->tables['reading_file'], $transactional_data[$key]);
                }
            }
        }

        $this->db->trans_complete();
        $status = 'error';
        $msg = 'Error while saving reading details.';
        if ($this->db->trans_status() !== FALSE) {
            $status = 'success';
            $msg = 'Reading details saved successfully.';
        }
        return array('status' => $status, 'msg' => $msg);
    }

    /**
     * @desc Save delete homework detail of class 
     * @param type $params
     * @return array
     */
    function delete_data($id = '', $type = '') {
        $status = 'error';
        $msg = 'Error while deleting ' . $type;
        $this->db->trans_start();
        $count = $this->db->where('homework_exercises_has_files_id', $id)
                        ->count_all_results($this->tables['file_tracking']) > 0;
        if ($count > 0) {
            $status = 'error';
            $msg = $type . ' can not be deleted. User already used this file.';
        } else {
            $this->db->delete($this->tables[$type . '_file'], array('id' => $id));
            $status = 'success';
            $msg = $type . ' deleted successfully.';
        }
        $this->db->trans_complete();
        return array('status' => $status, 'msg' => $msg);
    }

    public function update_reading_time($params = array()){
        extract($params);
        $diff = 0;
        $request['users_id'] = isset($user_id) ? $user_id : NULL;
        $request['homework_reading_articles_id']= isset($id) ? $id : NULL;
        $today_date = new DateTime(date('Y-m-d H:i:s'));
        $request['start_time'] = date('Y-m-d H:i:s');
        $request['end_time'] = date('Y-m-d H:i:s');
        $previouse_details  = $this->db->select('id,end_time')
                                        ->where(array('homework_reading_articles_id' => $request['homework_reading_articles_id'], 'users_id' => $request['users_id']))
                                        ->limit(1)
                                        ->order_by('id','desc')
                                        ->get('users_has_homework_reading_articles')
                                        ->row();
                                        //print_r($previouse_details);
        if(isset($previouse_details->id) && $previouse_details->id && $previouse_details->end_time){
            $update_end_time= $today_date->diff(new DateTime($previouse_details->end_time));
            $diff = $update_end_time->days*24*60*60;
            $diff += $update_end_time->h*60*60;
            $diff += $update_end_time->i*60;
            $diff += $update_end_time->s;
            if($diff <= 15) { // if user doesn't idle for 15 seconds then update it // if($diff > 0 && $diff < 15) {
                $this->db->trans_start();
                    $this->db->update('users_has_homework_reading_articles', array('end_time' => date('Y-m-d H:i:s')), array('id'=> $previouse_details->id));
                $this->db->trans_complete();
                if($this->db->trans_status() != FALSE){
                    return array('status' => 'success', 'msg' => 'Reading time updated');
                }
            } else {
                $this->db->insert('users_has_homework_reading_articles', $request);
            }
        } else {
            $this->db->insert('users_has_homework_reading_articles', $request);
            $ret = 2;
        }
        if($this->db->insert_id()){
            return array('status' => 'success', 'msg' => 'Reading time updated');
        }
        return array('status' => 'success', 'msg' => "Reading time didn't update");
    }
    

    /**
     * @desc get single category data
     * @param type $params
     * @return array
     */
    public function get_practice_category($category_id) {
        $info_array = array('fields'=>'id,label,image_name','where' => array('id' => $category_id), 'table' => $this->tables['category']);
        // getting sub page details like topics, testimonials
        $result = $this->db_model->get_data($info_array);
        if ($result['result']) {
           $result = $result['result'][0];
           return $result;
        } else {
            return false;
        }
    }

}
