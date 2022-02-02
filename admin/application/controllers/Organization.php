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

class Organization extends CI_Controller {

    /**
     * @desc Class Constructor
     */
    function __construct() {
        parent::__construct();
        if ($this->session->userdata('logged_in') == FALSE) {
            redirect('auth');
        }

        /** Load Model * */
        $this->load->model('Organization_model', 'organization');

        /** add error delimiters * */
        $this->form_validation->set_error_delimiters('<label class="error">', '</label>');
    }

    function index($id = false) {
        // Set the title
        get_plugins_in_template('datatable');
        $this->breadcrumbs->push('Organization', 'organization');
        $this->template->title = 'Organizations';
        $data['subheading'] = 'Add Organization';
        $data['organization_detail'] = array();
        if ($id) {
            $data['subheading'] = 'Edit Organization';
            $this->breadcrumbs->push('edit', 'Organization');
            $organization_detail = $this->organization->get_organizations(array('where' => array('organizations.id' => $id)));
            if (!empty($organization_detail['result'])) {
                $data['organization_detail'] = $organization_detail['result'][0];
            }
        }
        $data['breadcrumb'] = $this->breadcrumbs->show();

        if ($this->input->post()) {
            $this->form_validation->set_rules($this->config->item("organizationForm"));
            if ($this->form_validation->run() != FALSE) {
                if ($id) {
                    $organization_data['id'] = $id;
                }
                $organization_data['title'] = $this->input->post('title');
                $result = $this->organization->save_organization($organization_data);
                $this->session->set_flashdata($result['status'], $result['msg']);
                redirect('organization');
            }
        }
        $this->template->content->view('organizations', $data);


        $this->template->publish();
    }

    public function get_organizations_data() {
        $data = $this->organization->get_organizations($this->input->get());
        $rowCount = $data['total'];
        $output = array(
            "sEcho" => intval($this->input->get('sEcho')),
            "iTotalRecords" => $rowCount,
            "iTotalDisplayRecords" => $rowCount,
            "aaData" => []
        );
        $i = $this->input->get('iDisplayStart') + 1;
        foreach ($data['result'] as $val) {
            //$link = "";
            $link = '<a id="editOrganisation" href="' . base_url('organization/' . $val['id']) . '" title="Edit"><i class="fa fa-edit"></i></a>' . '&nbsp;&nbsp;';
            // $link .= '<button class="btn btn-danger btn-xs delete" data-href="' . base_url('admin/users/delete/' . $val['user_id']) . '" title="Delete"><i class="fa fa-trash-o"></i></button>';
            $output['aaData'][] = array(
                "DT_RowId" => $val['id'],
                $i++,
                $val['title'],
                $link
            );
        }
        echo json_encode($output);
        die;
    }

}
