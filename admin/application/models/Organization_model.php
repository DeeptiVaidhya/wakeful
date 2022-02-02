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
 * @desc Organization_model to store and view about organizations
 *
 * @author Ideavate
 */
class Organization_model extends CI_Model {

    var $tables = array();

    public function __construct() {
        parent::__construct();
        $this->tables = array('courses' => 'courses', 'orgs' => 'organizations');
    }

    /**
     * @desc Save organization detail
     * @param type $params
     * @return array
    */
    function save_organization($params = array()) {
        extract($params);
        $data = array("title" => $title);
        $this->db->trans_start();
        // Save in Database
        if (isset($id) && $id) {
            //$data['slug']=create_slug($title).'-'.$id;
            $this->db->update($this->tables['orgs'], $data, array('id' => $id));
        } else {
            $data["created_at"] = date('Y-m-d H:i:s');
            $this->db->insert($this->tables['orgs'], $data);
            //$insertid = $this->db->insert_id();
            // $this->db->where('id',$insertid);
            // $this->db->update($this->tables['orgs'],array('slug'=>create_slug($title).'-'.$insertid));
        }
        
        $this->db->trans_complete();
        $status = 'error';
        $msg = 'Error in saving organization.';
        if ($this->db->trans_status() !== FALSE) {
            $status = 'success';
            $msg = 'Organization saved successfully.';
        }
        return array('status' => $status, 'msg' => $msg);
    }
    
    
     /**
     * @desc Get all organization detail
     * @param type $params
     * @return array
    */

    function get_organizations($params = array()) {
        extract($params);
        $col_sort = array("id", "title");
        $info_array = array('fields' => 'id,title');
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
        $info_array['count'] = true;

        $info_array['table'] = $this->tables['orgs'];
        return $this->db_model->get_data($info_array);
    }

}
