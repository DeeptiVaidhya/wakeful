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

/**
 * @desc Setting_model to store and view about Settings
 *
 * @author Ideavate
 */
class Setting_model extends CI_Model {

    var $tables = array();

    public function __construct() {
        parent::__construct();
        $this->tables = array('site_settings' => 'site_settings');
    }
    
     /**
     * @desc Get all setting list
     * @param type $params
     * @return array
    */

    function get_settings($params = array()) {
        extract($params);
        $col_sort = array("id", "title","value");
        $info_array = array('fields' => '`id`,`title`,`value`,`study_id`');
        $order_by = "id";
        $order = 'DESC';
        $start = 0;
        $search_array = FALSE;
        $limit = $this->config->item('pager_limit');
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

        $info_array['order_by'] = $order_by;
        $info_array['order'] = $order;
        $info_array['start'] = $start;
        $info_array['limit'] = $limit;
        $info_array['count'] = false;


        $info_array['table'] = $this->tables['site_settings'];
        return $this->db_model->get_data($info_array);
	}
	
	/**
     * @desc Save setting detail
     * @param type $params
     * @return array
    */
    function save_setting($params = array()) {
        extract($params);
        $data = array("value" => $value, "key" => 'ACCESS_CODE', 'title' => 'Access Code', 'description' => 'Access Code used to Sign Up', 'study_id' => $study_id);
        $this->db->trans_start();
        // Save in Database
        $status = 'error';
        $msg = 'Error in updating setting.';
        if (isset($id) && $id) {
            $this->db->update($this->tables['site_settings'], $data, array('id' => $id));
            $msg = 'Site setting updated successfully.';
        } else {
            $this->db->insert($this->tables['site_settings'], $data);
            $msg = 'Site setting added successfully.';
        } 
            
        $this->db->trans_complete();
        
        if ($this->db->trans_status() !== FALSE) {
            $status = 'success';
            
        }
        return array('status' => $status, 'msg' => $msg);
    }

}
