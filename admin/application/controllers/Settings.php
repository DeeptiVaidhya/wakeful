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

class Settings extends CI_Controller {
    
    /**
     * @desc Class Constructor
     */    
    function __construct() {
		parent::__construct();
        $this->load->model('Setting_model', 'site_setting');
		
    }
	
	public function site_settings($study_id){
		get_plugins_in_template('datatable');
		$this->breadcrumbs->push('Study', 'study');

        $this->breadcrumbs->push('Site Settings', 'setting');
        $this->template->title = 'Site Settings';
        $data['site_setting_detail'] = array();
        $data['breadcrumb'] = $this->breadcrumbs->show();

		$data['settings'] = $this->site_setting->get_settings(array('where' => array('study_id' => $study_id)));
		$data['study_id'] = $study_id;
        $this->template->content->view('site-setting/site_settings', $data);
        $this->template->publish();
	}

	public function edit_setting($id){
		
        $data['setting_detail'] = array();
        if ($id) {
			$setting_detail = $this->site_setting->get_settings(array('where' => array('id' => $id)));
            if (!empty($setting_detail['result'])) {
				$this->breadcrumbs->push('Site Settings', 'settings/site-settings/'.$setting_detail['result'][0]['study_id']);
				$this->template->title = 'Site Settings';
				$data['subheading'] = 'Edit Site Setting';
				$this->breadcrumbs->push('edit', 'setting');
                $data['setting_detail'] = $setting_detail['result'][0];
            }
        }
        $data['breadcrumb'] = $this->breadcrumbs->show();

        if ($this->input->post()) {
            $this->form_validation->set_rules($this->config->item("siteSettingForm"));
            if ($this->form_validation->run() != FALSE) {
                if ($id) {
                    $setting_data['id'] = $id;
                }
                $setting_data['value'] = $this->input->post('value');
                $setting_data['study_id'] = $setting_detail['result'][0]['study_id'];
                $result = $this->site_setting->save_setting($setting_data);
                $this->session->set_flashdata($result['status'], $result['msg']);
                redirect('settings/site-settings/'.$setting_detail['result'][0]['study_id']);
            }
        }
        $this->template->content->view('site-setting/edit', $data);


        $this->template->publish();
    }
    

    public function add_setting($study_id){
		
        $data['setting_detail'] = array();
        if ($study_id) {
				$this->breadcrumbs->push('Site Settings', 'settings/site-settings/'.$study_id);
				$this->template->title = 'Site Settings';
				$data['subheading'] = 'Add Site Setting';
				$this->breadcrumbs->push('add', 'setting');

        }
        $data['breadcrumb'] = $this->breadcrumbs->show();

        if ($this->input->post()) {
            $this->form_validation->set_rules($this->config->item("siteSettingForm"));
            if ($this->form_validation->run() != FALSE) {
                if ($study_id) {
                    $setting_data['study_id'] = $study_id;
                }
                $setting_data['value'] = $this->input->post('value');
                $result = $this->site_setting->save_setting($setting_data);
                $this->session->set_flashdata($result['status'], $result['msg']);
                redirect('settings/site-settings/'.$study_id);
            }
        }
        $this->template->content->view('site-setting/edit', $data);


        $this->template->publish();
	}
    
    
}
