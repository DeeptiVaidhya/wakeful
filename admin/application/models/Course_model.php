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
class Course_model extends CI_Model {

    var $tables = array();

    public function __construct() {
        parent::__construct();
        $this->tables = array('courses' => 'courses', 'orgs' => 'organizations', 'answer' => 'users_has_feedback_question', 'setting' => 'settings', 'courses_has_files' => 'courses_has_files', 'files' => 'files', 'course_homework' => 'course_homework','users_has_courses'=>'users_has_courses','users_organizations'=>'users_has_organizations','category' => 'practice_categories');
    }

    function generate_audio($params = array(), $previous_file_id, $type = 'practice') {
        $previous_file_id = isset($previous_file_id) ? $previous_file_id : FALSE;
        $config = $this->config->item('sftp_assets_audios');
        $config_local = $this->config->item('assets_audios');
        $upload_path = $config['path'];
        $upload_url = $config['url'];
        $local_path = $config_local['path'];
        $files_id = FALSE;
        $files_arr = array('start_bell_audio', 'practice_audio', 'poem_audio', 'end_bell_audio', 'closing_audio');

        $temp_files_arr = array();  // Store temporary file names, delete them after merge
        $temp_output_arr = array();  // Store temporary output names, delete them after merge

        $this->load->library('phpmp3.php');
        $file_name = $type . '_' . uniqid() . '.mp3';
        $newpath = $config['path'] . 'course_hw_'.$file_name;
        $mergedFile = new PHPMP3();

        foreach ($files_arr as $key => $val) {
            if (isset($params[$val])) {
                $temp_filename = $local_path.'/'.basename($params[$val]).'.temp.mp3';
                $output_path = $local_path.'/output_' . basename($params[$val]) . '.file';
                exec('ffprobe -v error -show_entries stream=bit_rate,sample_rate '.$upload_url.basename($params[$val]).' > '.$output_path);
                $output = file_get_contents($output_path);  // convert file into string

                // If audio file is encoded with 44.1KHz sampling rate + 128kn bitrate, don't do conversion to save time
                if (strpos($output, 'sample_rate=44100') && strpos($output, 'bit_rate=128000')) {
                // No need to convert
                    exec('wget -O '. $temp_filename .' '. $upload_url.basename($params[$val]));
                } else {
                    exec('ffmpeg -i '.$upload_url.basename($params[$val]).' -f mp3 -ab 128k -ar 44100 -ac 2 -y '.$temp_filename);
                }

                array_push($temp_output_arr, $output_path);
                array_push($temp_files_arr, $temp_filename);

                $mp3 = new PHPMP3($temp_filename);
                $mergedFile->mergeBehind($mp3);
                $mergedFile->striptags();
            }
        }

        $mergedFile->setIdv3_2('01','Track Title','Artist','Album','Year','Genre','Comments','Composer','OrigArtist','Copyright','url','encodedBy');
        if($mergedFile->save($newpath) && count($temp_output_arr)) {
            // Delete temporary files
            foreach ($temp_files_arr as $tmp_file) {
                unlink($tmp_file);
            }
            foreach ($temp_output_arr as $tmp_file) {
                unlink($tmp_file);
            }
        }


        $input_arr = array(
            'name' => 'Generated_Audio.mp3',
            'unique_name' => 'course_hw_' . $file_name,
            'type' => 'audio/mp3',
            'created_at' => date('Y-m-d H:i:s')
        );
        $this->db->trans_start();
        if ($previous_file_id) {
            $query = $this->db->where('id', $previous_file_id)->limit(1)->get($this->tables['files']);
            if ($query->num_rows() > 0) {
                $this->unlink_file($previous_file_id);
                $this->db->update($this->tables['files'], $input_arr, array('id' => $previous_file_id));
                $files_id = $previous_file_id;
                
            }
        } else {
            $this->db->insert($this->tables['files'], $input_arr);
            $files_id = $this->db->insert_id();
        }
        $this->db->trans_complete();
        return $files_id;
    }
    
    function unlink_file($file_id){
        $config=$this->config->item('sftp_details');
        $connection = ssh2_connect($config['hostname'], 22);
        ssh2_auth_password($connection, $config['username'], $config['password']);
        $sftp = ssh2_sftp($connection);
        $file_name=get_file_unique_name($file_id);
        return ssh2_sftp_unlink($sftp, $file_name);
    }


    function save_course_homework_exercise($params = array()) {
        extract($params);
        
        $previous_file_id = isset($previous_file_id) ? $previous_file_id : NULL;
        $course_id = isset($course_id) ? $course_id : NULL;
        $previous_audio_id = isset($previous_audio_id) ? $previous_audio_id : NULL;
        $previous_poem_id = isset($previous_poem_id) ? $previous_poem_id : NULL;
        $homework_id = isset($homework_id) ? $homework_id : NULL;

        $category_id = isset($category_id) ? $category_id : NULL;
        
        $created_at = date('Y-m-d H:i:s');

        $data = array('title' => isset($title) ? $title : '', 'tip' => isset($tip) ? $tip : '', 'script' => isset($script) ? $script : '', 'created_at' => $created_at, 'courses_has_files_courses_id' => $course_id);
        $transactional_data = array();

        
        $data['files_id']=NULL;
        $data['poem_file_id']=NULL;
        $bell_file=NULL;
        $data['practice_audio_file_id']=NULL;
        $data['closing_file_id'] = NULL;

        if($files['audio']['name'] || $previous_audio_id){
            $data['closing_file_id'] = isset($closing_file) ? $closing_file : NULL;
            if ($files['audio']['name'] != '') {
                $data['practice_audio_file_id'] = isset($files['audio']) ? $this->db_model->upload_audio_videos($files, 'audio', 'audios', $previous_audio_id) : '';
            } else {
                $data['practice_audio_file_id'] = $previous_audio_id;
            }
            if ($files['poem']['name'] != '') {
                $data['poem_file_id'] = isset($files['poem']) ? $this->db_model->upload_audio_videos($files, 'poem', 'audios', $previous_poem_id) : '';
            } else {
                $data['poem_file_id'] = $previous_poem_id;
            }

            $audio_files = array();

            $course_detail = $this->get_courses(array('where' => array('courses.id' => $course_id)));
            $bell_file='';
            if ($course_detail['result'] && isset($course_detail['result'][0]) && $course_detail = $course_detail['result'][0]) {
                $sftp_config = $this->config->item('sftp_assets_audios');
                $audio_files['start_bell_audio'] = $sftp_config['path'] . $course_detail['bell_audio']['bell_unique_name'];
                $audio_files['end_bell_audio'] = $audio_files['start_bell_audio'];
                $bell_file=$course_detail['bell_audio']['bell_file_id'];
            }


            $audio_files['practice_audio'] = get_file_unique_name($data['practice_audio_file_id']);
        

            if ($data['poem_file_id']) {
                $audio_files['poem_audio'] = get_file_unique_name($data['poem_file_id']);
            }
            if ($data['closing_file_id']) {
                $audio_files['closing_audio'] = get_file_unique_name($data['closing_file_id']);
            }
        
        
            $files_id = $this->generate_audio($audio_files, $previous_file_id = ($previous_file_id && $previous_audio_id != $previous_file_id ? 
            $previous_file_id : ''));
            if ($files_id) {
                $data['files_id'] = $files_id;
            } else {
                $data['files_id'] = $data['practice_audio_file_id'];
            }
            if (isset($audio_files['poem_audio'])) {
                unset($audio_files['poem_audio']);
            }
        
        }


        $this->db->trans_start();


        $data = array('title' => $title, 'tip' => $tip, 'script' => $script, 'created_at' => $created_at,'courses_has_files_courses_id' => $course_id, 'files_id' => $data['files_id'],'poem_file_id' => $data['poem_file_id'],
            'closing_file_id' => $data['closing_file_id'],'bell_file_id' => $bell_file,'practice_audio_file_id' => $data['practice_audio_file_id'],'practice_categories_id' => $category_id);

        if ($homework_id) {
            $this->db->update($this->tables['course_homework'], $data,array('id'=>$homework_id));
        } else {
            $this->db->insert($this->tables['course_homework'], $data);
        }


        $this->db->trans_complete();
        $status = 'error';
        $msg = 'Error while saving homework exercise.';
        if ($this->db->trans_status() !== FALSE) {
            $status = 'success';
            $msg = 'Homework exercise saved successfully.';
        }
        return array('status' => $status, 'msg' => $msg);
    }


    function save_practice_category($params = array()) {
        extract($params);
        
        $previous_cat_file_id = isset($previous_cat_file_id) ? $previous_cat_file_id : NULL;
        $category_id = isset($category_id) ? $category_id : NULL;
        
        $created_at = date('Y-m-d H:i:s');

        $label = isset($label) ? $label : NULL;

        $transactional_data = array();
        
        $data['files_id']=NULL;

        $this->db->trans_start();

        if (isset($category_id) && $category_id) {
            if ($files['category_image']['name'] != '') {
                $category_image = isset($files['category_image']) ? $this->db_model->upload_document($files, 'category_image', 'images', $previous_cat_file_id) : '';
            }else{
                $category_image = $previous_cat_file_id;
            }

            $this->db->update($this->tables['category'], array('label' => $label,'image_name' => $category_image), array('id' => $category_id));
        } else {

            if ($files['category_image']['name'] != '') {
                $category_image = isset($files['category_image']) ? $this->db_model->upload_document($files, 'category_image', 'images', $previous_cat_file_id) : '';
            }else{
                $category_image = $previous_cat_file_id;
            }

            $this->db->insert($this->tables['category'], array('label' => $label,'image_name' => $category_image,'courses_id' => $course_id));
        }

        $this->db->trans_complete();
        $status = 'error';
        $msg = 'Error while saving category.';
        if ($this->db->trans_status() !== FALSE) {
            $status = 'success';
            $msg = 'Category saved successfully.';
        }
        return array('status' => $status, 'msg' => $msg);
    }
    
         /**
     * Get homework excercise
     * @param  user_id
     * @return Array
     * */
    function get_course_homework_excercise($where,$params = array()) {
        extract($params);
        $col_sort = array("id", "title", "tip");
        $info_array = array('fields' => 'id,title,tip,script,files_id,is_meditation_practice,meditation_practice_title');
        $where = $where;
        $order_by = "id";
        $order = 'DESC';
        $search_array = FALSE;
        if (isset($params['iSortCol_0'])) {
            $index = $params['iSortCol_0'];
            $order = $params['sSortDir_0'] === 'asc' ? 'asc' : 'desc';
            $order_by = $col_sort[$index];
        }
        if (isset($where)) {
            $info_array['where'] = $where;
        }
        if (isset($params['sSearch']) && $params['sSearch'] != "") {
            $words = $params['sSearch'];
            $search_array = array();
            foreach ($col_sort as $key => $value) {
                $search_array[$value] = $words;
            }
            $info_array['like'] = $search_array;
        }
        if (isset($params['iDisplayStart']) && $params['iDisplayLength'] != '-1') {
            $start = intval($params['iDisplayStart']);
            $limit = intval($params['iDisplayLength']);
            $info_array['start'] = $start;
            $info_array['limit'] = $limit;
        }

        $info_array['order_by'] = $order_by;
        $info_array['order'] = $order;
        
        $info_array['count'] = true;
        //$info_array['debug'] = true;

        $info_array['table'] = $this->tables['course_homework'];
       
        $result = $this->db_model->get_data($info_array);
        return $result;
        
    }
    
    /**
     * @desc get single user detail
     * @param type $params
     * @return array
     */
    public function get_course_homework_detail($homework_id) {
        $info_array = array('fields'=>'id,title,tip,script,files_id,closing_file_id,practice_audio_file_id,poem_file_id,practice_categories_id','where' => array('id' => $homework_id), 'table' => $this->tables['course_homework']);
        // getting sub page details like topics, testimonials
        $result = $this->db_model->get_data($info_array);
        if ($result['result']) {
           $result = $result['result'][0];
           return $result;
        } else {
            return false;
        }
    }

    /**
     * @desc Function is used to add/update course data
     * @param type $params, having title and/or id if updating course data
     * @return array having message and status
     */
    function save_course($params = array()) {

        $login_user_detail = $this->session->userdata('logged_in');
        $user_id =$login_user_detail->id ;

        extract($params);
        $previous_bell_file_id = isset($previous_bell_file_id) ? $previous_bell_file_id : '';
        $previous_closing_audio_file = isset($previous_closing_audio_file) ? $previous_closing_audio_file : array();
        $data = array("title" => $title, "slug" => $slug, 'organizations_id' => $organizations_id, 'is_published' => 0);
        if (isset($is_published) && $is_published) {
            $data['is_published'] = 1;
        }
        if ($files['bell_audio_file']['name'] != '') {
            $file_data['bell_audio_file_id'] = isset($files['bell_audio_file']) ? $this->db_model->upload_audio_videos($files, 'bell_audio_file', 'audios', $previous_bell_file_id) : '';
        } else {
            $file_data['bell_audio_file_id'] = $previous_bell_file_id;
        }

        $_FILES = re_arrange_files($files['closing_audio_file'], 'file');
        $i = 1;
        foreach ($_FILES as $key => $value) {
            $previous_closing_audio_file_id = isset($previous_closing_audio_file[$key]) ? $previous_closing_audio_file[$key] : FALSE;
            if ($_FILES[$key]['name'] != '') {
                $file_data['closing_files_ids'][] = $this->db_model->upload_audio_videos($_FILES, $key, 'audios', $previous_closing_audio_file_id[$i]);
            } else {
                $file_data['closing_files_ids'][] = isset($previous_closing_audio_file[$i]) ? $previous_closing_audio_file[$i] : FALSE;
            }
            $i++;
        }

        $this->db->trans_start();
        // Save in Database
        if (isset($course_id) && $course_id) {
            $this->db->update($this->tables['courses'], $data, array('id' => $course_id));
        } else {
            $data["created_at"] = date('Y-m-d H:i:s');
            $this->db->insert($this->tables['courses'], $data);
            $course_id = $this->db->insert_id();
            // $this->db->where('id', $course_id);
            // $this->db->update($this->tables['courses'], array('slug' => create_slug($title) . '-' . $course_id));

            //$user_has_course_array = array ('users_id'=>$user_id ,'courses_id'=>$course_id);
            //$this->db->insert($this->tables['users_has_courses'], $user_has_course_array);
        }
        if ($course_id) {
            $this->db->delete($this->tables['courses_has_files'], array('courses_id' => $course_id));
            if (!empty($file_data)) {
                if (isset($file_data['bell_audio_file_id'])) {
                    $this->db->insert($this->tables['courses_has_files'], array('files_id' => $file_data['bell_audio_file_id'], 'courses_id' => $course_id, 'file_type' => 'bell_audio'));
                }
                if (isset($file_data['closing_files_ids'])) {
                    foreach ($file_data['closing_files_ids'] as $v) {
                        if ($v) {
                            $file_input['files_id'] = $v;
                            $file_input['file_type'] = 'closing_audio';
                            $file_input['courses_id'] = $course_id;
                            $this->db->insert($this->tables['courses_has_files'], $file_input);
                        }
                    }
                }
            }
        }
        $this->db->trans_complete();
        $status = 'error';
        $msg = 'Error in saving course.';
        if ($this->db->trans_status() !== FALSE) {
            $status = 'success';
            $msg = 'Course saved successfully.';
        }
        return array('status' => $status, 'msg' => $msg);
    }

    /**
     * @desc function to get all courses or course list by filter
     * @param type $params
     * @return list of courses
     */
    function get_courses($params = array()) {
        //print_r($params);die;    
        extract($params);
        $col_sort = array("courses.id", "courses.title", "courses.is_published", "courses.slug", "org.title", "courses.title");
        $info_array['fields'] = 'courses.id,courses.title,courses.is_published,courses.slug,org.title as org_title, courses.organizations_id';
        $order_by = "courses.id";
        $order = 'DESC';
        $search_array = FALSE;
        $join = array(
            array(
                'table' => 'organizations org',
                'on' => 'org.id = courses.organizations_id',
                'type' => 'INNER'
            )
        );
        //print_r($where);die;
        if(isset($admin_id) && $admin_id){
            $join[]=array('table' => 'users_has_organizations uorg',
                'on' => 'uorg.organizations_id = org.id',
                'type' => 'LEFT');
            
            $where = ' uorg.users_id = '.$admin_id;
        }

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
            $info_array['start'] = $start;
            $info_array['limit'] = $limit;
        }


        if (isset($where)) {
            $info_array['where'] = $where;
        }


        $info_array['order_by'] = $order_by;
        $info_array['order'] = $order;        
        $info_array['count'] = true;
        $info_array['join'] = $join;
        // $info_array['debug']=TRUE;
        $info_array['table'] = $this->tables['courses'];
        $result = $this->db_model->get_data($info_array);

        if ($result['result']) {
            foreach ($result['result'] as $key => $value) {
                $query = $this->db->select('files_id as bell_file_id,files.name as bell_file_name,unique_name as bell_unique_name')
                        ->join('courses_has_files', 'courses_has_files.files_id=files.id', 'left')
                        ->where(array('courses_id' => $value['id'], 'courses_has_files.file_type' => 'bell_audio'))
                        ->get($this->tables['files']);
                $result['result'][$key]['bell_audio'] = $query->row_array();

                $close_audio_arr = array();
                $query = $this->db->select('files_id as close_files_id,files.name as close_file_name,unique_name as close_unique_name')
                        ->join('courses_has_files', 'courses_has_files.files_id=files.id', 'left')
                        ->where(array('courses_id' => $value['id'], 'courses_has_files.file_type' => 'closing_audio'))
                        ->get($this->tables['files']);
                if ($query->num_rows() > 0) {
                    $close_audio_arr = $query->result_array();
                }
                $result['result'][$key]['close_audio'] = $close_audio_arr;
            }
        }
        return $result;
    }

    function get_courseid($params=array()){
        extract($params);
        $info_array['fields'] = 'id';
        $order_by = "id";
        $order = 'DESC';
        if (isset($where)) {
            $info_array['where'] = $where;
        }
        $info_array['table'] = $this->tables['courses'];
        $courseid = $this->db_model->get_data($info_array);
        if(count($courseid['result']))
        {
            return $courseid;
        }
        return $courseid = Array('result' => Array(Array('id' => 1)));
        
    }

    /**
     * @desc function to get all courses or course list by filter
     * @param type $params
     * @return list of courses
     */
    function get_user_has_course($user_id){
        $query = $this->db->select('courses_id, study_id')
        ->where('users_id = '.$user_id)
        ->get($this->tables['users_has_courses']);
        $user_has_course_array = $query->result_array();
        $course_id_array = array();
        foreach($user_has_course_array as $course_id){
            $course_id_array[] = $course_id['courses_id'];
        }
        return $course_id_array;
    }

    /**
     * @desc function to get all courses or course list by filter
     * @param type $params
     * @return list of courses
     */
    function get_user_has_organization($user_id){
        $query = $this->db->select('organizations_id')
        ->where('users_id = '.$user_id)
        ->get($this->tables['users_organizations']);
        $user_has_org_array = $query->result_array();
        $org_id_array = array();
        foreach($user_has_org_array as $organizations_id){
            $org_id_array[] = $organizations_id['organizations_id'];
        }
        return $org_id_array;
    }

    /**
     * @desc function to get all courses or course list by filter
     * @param type $params
     * @return list of feedback
     */
    function get_feedbacks($params = array()) {
        extract($params);
        //$course_id = (isset($course_id)) ? $course_id : FALSE;
        $col_sort = array("username", "question_id", "answer");
        $info_array['fields'] = 'users.id as user_id,username,question_id,answer,question,email,users_has_feedback_question.id as answer_id, users_has_feedback_question.created_at';
        $order_by = "users.id";
        $order = 'DESC';
        $start = FALSE;
        $search_array = FALSE;
        $limit = false;

        $join = array(
            array(
                'table' => 'feedback_question as question',
                'on' => 'question.id = users_has_feedback_question.question_id',
                'type' => 'LEFT'
            ),
            array(
                'table' => 'users',
                'on' => 'users.id = users_has_feedback_question.users_id',
                'type' => 'LEFT'
            ),
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

        $info_array['order_by'] = $order_by;
        $info_array['order'] = $order;
        $info_array['start'] = $start;
        $info_array['limit'] = $limit;
        $info_array['count'] = true;
        $info_array['join'] = $join;
        //$info_array['group_by']=('user_id');
        $info_array['table'] = $this->tables['answer'];
        return $this->db_model->get_data($info_array);
    }

    /**
     * @desc Function is used to check course is exist or not 
     * @param type $params, having title and organization id
     * @return count of cousre
     */
    function is_exist_course($title, $organization_id, $id = '') {
        $where = array('title' => $title, 'organizations_id' => $organization_id);
        if ($id) {
            $where['id !='] = $id;
        }
        $is_exist = $this->db->where($where)->count_all_results($this->tables['courses']);
        return $is_exist;
    }

    /**
     * @desc Function is used to check feature is exist or not 
     * @param type $params, having feature and course id
     * @return count of settings
     */
    function is_exist_setting($feature, $id = '') {
        $where = array('key' => $feature);
        if ($id) {
            $where['courses_id ='] = $id;
        }
        $is_exist = $this->db->where($where)->count_all_results($this->tables['setting']);
        return $is_exist;
    }

    /**
     * @desc function to get all setting of course
     * @param type $params
     * @return list of setting
     */
    function get_setting($params = array()) {
        extract($params);
        $info_array['fields'] = '*';
        $order_by = "id";
        $order = 'DESC';
        if (isset($where)) {
            $info_array['where'] = $where;
        }
        $info_array['debug'] = false;
        $info_array['table'] = $this->tables['setting'];
        return $this->db_model->get_data($info_array);
    }

    /**
     * @desc Update setting
     * @param type $params
     * @return array
     */
    function change_setting($params = array()) {
        extract($params);
        $id = isset($id) ? $id : FALSE;
        $status = 'error';
        $msg = 'Error while changing setting';
        $this->db->trans_start();
        if ($id) {
            $this->db->update($this->tables['setting'], array('value' => $is_active), array('id' => $id));
        }else{
            $data = array('value' => $is_active,'study_id' => $study_id, 'courses_id' => $course_id, 'key'=> $key, 'description' => $description );
            $this->db->insert($this->tables['setting'], $data);
        }

        $this->db->trans_complete();
        if ($this->db->trans_status() !== FALSE) {
            $status = 'success';
            $msg = 'Setting change successfully.';
        }
        return array('status' => $status, 'msg' => $msg);
    }

    /**
     * @desc Delete course files
     * @param type $params
     * @return array
     */
    function delete_file($params) {
        extract($params);
        $status = 'error';
        $msg = 'Error while deleting file';
        $this->db->trans_start();
        $file_id = isset($file_id) ? $file_id : FALSE;
        $id = isset($id) ? $id : FALSE;
        $query = $this->db->where('id', $file_id)->get($this->tables['files']);
        $is_file_deleted = false;
        if ($query->num_rows() > 0) {
            $is_file_deleted = $this->unlink_file($file_id);
            if($is_file_deleted && $id){
                $this->db->set('bell_file_id', null);
                $this->db->where('id', $id);
                $this->db->update($this->tables['course_homework']);
                $this->db->delete($this->tables['files'], array('id' => $file_id));
            }
        }


        $this->db->trans_complete();
        if ($this->db->trans_status() !== FALSE) {
            if(!$is_file_deleted){
                $msg='File not deleted from server';
                $status = 'error';
            } else {
                $status = 'success';
                $msg = 'File deleted successfully.';
            }
        }
        return array('status' => $status, 'msg' => $msg);
    }
    function delete_users_has_courses($user_id){
        $this->db->delete($this->tables['users_has_courses'], array('users_id' => $user_id));
    }

    function insert_users_has_courses($course_ids,$user_id){
        for ($i = 0; $i < count($course_ids); $i++)
        {
            $data = array(
                'courses_id' => $course_ids[$i],
                'users_id' => $user_id
            );
            $this->db->insert($this->tables['users_has_courses'],$data);
        }
    }

    function insert_users_has_organization($org_ids,$user_id, $parent_user_id=null){
        for ($i = 0; $i < count($org_ids); $i++)
        {
            $data = array(
                'organizations_id' => $org_ids[$i],
                'users_id' => $user_id,
                'parent_user_id' => $parent_user_id,
            );
            $this->db->insert($this->tables['users_organizations'],$data);
        }
    }

    function insert_participant_has_course($course_id,$user_id,$study_id){
        
            $data = array(
                'study_id' => $study_id,
                'courses_id' => $course_id,
                'users_id' => $user_id
            );
            $this->db->insert($this->tables['users_has_courses'],$data);
            return true;
        
    }

    /**
     * @desc function to get slug name to match from
     * @param type $params
     * @return list of courses
     */
    function get_slug($params = array()) {

        extract($params);
        $info_array['fields'] = 'slug';
        $order_by = "id";
        $order = 'DESC';
        if (isset($where)) {
            $info_array['where'] = $where;
        }
        $info_array['table'] = $this->tables['courses'];
        return $this->db_model->get_data($info_array);
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

    public function get_practice_data($id) {
        $info_array = array('fields'=>'id,label,image_name,courses_id','where' => array('courses_id' => $id), 'table' => $this->tables['category']);
        // getting sub page details like topics, testimonials
        $result = $this->db_model->get_data($info_array);
        if ($result['result']) {
           $result = $result['result'][0];
           return $result;
        } else {
            return false;
        }
    }

    public function get_category($course_id) {
        $info_array = array('fields'=>'id,label,image_name','where' => array('courses_id' => $course_id),'table' => $this->tables['category']);
        // getting sub page details like topics, testimonials
        $result = $this->db_model->get_data($info_array);
        if ($result['result']) {
           $result = $result['result'];
           return $result;
        } else {
            return false;
        }
    }

    function get_practice_category_data($where,$params = array()) {
        extract($params);
        $col_sort = array("id", "label", "image_name");
        $info_array = array('fields' => 'id,label,image_name');
        $order_by = "id";
        $order = 'DESC';
        $search_array = FALSE;

        if (isset($params['iSortCol_0'])) {
            $index = $params['iSortCol_0'];
            $order = $params['sSortDir_0'] === 'asc' ? 'asc' : 'desc';
            $order_by = $col_sort[$index];
        }
        if (isset($where)) {
            $info_array['where'] = $where;
        }
        if (isset($params['sSearch']) && $params['sSearch'] != "") {
            $words = $params['sSearch'];
            $search_array = array();
            foreach ($col_sort as $key => $value) {
                $search_array[$value] = $words;
            }
            $info_array['like'] = $search_array;
        }
        if (isset($params['iDisplayStart']) && $params['iDisplayLength'] != '-1') {
            $start = intval($params['iDisplayStart']);
            $limit = intval($params['iDisplayLength']);
            $info_array['start'] = $start;
            $info_array['limit'] = $limit;
        }

        $info_array['order_by'] = $order_by;
        $info_array['order'] = $order;        
        $info_array['count'] = true;
        //$info_array['debug'] = true;

        $info_array['table'] = $this->tables['category'];

        $result = $this->db_model->get_data($info_array);
        return $result;
        
    }

    

}
