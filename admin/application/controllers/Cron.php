<?php
 /**

 * Copyright (c) 2003-2021 BrightOutcome Inc.  All rights reserved.
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

class Cron extends CI_Controller {

    public function __construct() {
        parent::__construct();
        $this->load->model('User_model', 'user');
        $this->load->model('Classes_model', 'classes');        
        $this->config->load('auth', TRUE);
	}

	/**
 	 * Sending new-class notification email at the beginning of a new week even if the prior class was not completed 
 	 */
	public function class_reminder_email(){
		$msg="Error in sending reminder email";
		// get all users who does not get reminder email for a new class
		$user_classes = $this->db->select('id, users_id, start_at, end_at, classes_id, week_number')
				->where('DATE(start_at) ="'.date('Y-m-d').'" and reminder_email_sent_at IS NULL and end_at IS NOT NULL and status != "COMPLETED"')
				->get('users_has_classes')->result();
		// send email When the class of a new week is unlocked
		foreach($user_classes as $class){
			$user = $this->user->get_detail($class->users_id);
			if($user['mute_notification'] != "1" && strtolower($user['is_active']) == 'active' && $class->week_number >= 2){
				$email = $user['email'];
				$content['footer'] = TRUE;
				$content['class_schedule'] = $this->classes->get_class_list($class->users_id);
				$content['heading'] = $this->config->item('class_unlocked_heading');
				$content['message'] = $this->config->item('class_unlocked_message');
				$message = $this->load->view('email_template', $content, TRUE);
				$subject = $this->config->item('class_unlocked_subject');
				$msg = "Reminder email sent ";
				if (send_email($subject, $email, $message)) { // update if mail sent to participant
					$this->db->update('users_has_classes',array('reminder_email_sent_at'=>date('Y-m-d H:i:s')),array('id'=>$class->id));
				}
				generate_log($msg. " for a new Class[".$class->classes_id."] to User[".$class->users_id."]");
			} else {
				generate_log($msg. " for a new Class[".$class->classes_id."] to User[".$class->users_id."] because user is Inactive or Muted or It's new class is not started yet.");
			}
		}
	}

	public function send_notifications(){
        $this->not_accessed_site_mail();
		$this->class_reminder_email();// send email if a new weekly class is unlocked for a user
    }

	/**
	 * Called from CronTab.
	 * Function used to send email for who has not login for 48 hours and class is not completed in 7 days time frame.
	 */
	public function not_accessed_site_mail(){
		$hour = 48;
        $testing_cron = $this->config->item('testing_cron');
        if($testing_cron){
            $hour = 1;
		}

		/**
		*  HOUR(TIMEDIFF(NOW(),last_login)) > 48 :  Checking 48 hours inactivity from last login
		*  HOUR(TIMEDIFF(NOW(),`users_has_classes`.`start_at`)) > 48 : Checking 48 hours completion from current week start date
		*  (CURDATE() between `users_has_classes`.`start_at` and `users_has_classes`.`end_at`) : Get current week records only 
		*/

		$users = $this->db->query("SELECT `users`.`id`, `users`.`email`, `users_has_classes`.`start_at`, `users_has_classes`.`end_at`, `users_has_classes`.`status`, `users_has_classes`.`id` as `user_class_id`,`users_has_classes`.`week_number`, `users_has_classes`.`classes_id`
			FROM `users` JOIN `users_has_classes` ON `users_has_classes`.`users_id`=`users`.`id`
			WHERE HOUR(TIMEDIFF(NOW(),last_login)) > ".$hour.
			" AND HOUR(TIMEDIFF(NOW(),`users_has_classes`.`start_at`)) > ".$hour.
			" AND (CURDATE() between `users_has_classes`.`start_at` and `users_has_classes`.`end_at`) 
			  AND `users`.`is_active` = 1 AND `users`.`mute_notification` != '1' 
			  AND `users_has_classes`.`status` != 'COMPLETED' 
			  AND `users_has_classes`.`class_incompleted_in_timeframe_mail_sent_at` IS NULL 
			  AND `users_has_classes`.`class_incompleted_after_timeframe_mail_sent_at` IS NULL")->result_array();
		if(!empty($users)){
			foreach ($users as $key => $user) {
				$reminder_email_for = 2; // send reminder email #2 default
				// #3 reminder email should be sent after 1st week
				if($user['week_number'] > 1){
					$previous_week = $this->db->query("SELECT  `id`, `status` FROM `users_has_classes` WHERE `end_at` BETWEEN CURDATE()-INTERVAL 1 WEEK AND CURDATE() AND users_id=". $user['id'])->row();
					// Check for previous week class has started but not completed, then send #3
					if(isset($previous_week->status) && $previous_week->status == 'STARTED'){ 
						$reminder_email_for = 3;
					}
				}
				$email = aes_256_decrypt($user['email']);
				if($reminder_email_for == 2){
					// send email
					$end_at = date("m/d", strtotime($user['end_at']));
					$content['heading'] = $this->config->item('not_accessed_heading');
					$content['message'] = $this->config->item('not_accessed_message');
					$message = $this->load->view('email_template', $content, TRUE);
					$subject = sprintf($this->config->item('not_accessed_subject'), $end_at);					
					$data = array('class_incompleted_in_timeframe_mail_sent_at'=>date('Y-m-d H:i:s'));
					$log = "EMail sent to user[".$user['id']."] who has not login for 48 hours and class[".$user['classes_id']."] is completed in 7 days timeframe";
				} else {
					// send email
					$content['heading'] = $this->config->item('not_completed_class_heading');
					$content['message'] = $this->config->item('not_completed_class_message');
					$message = $this->load->view('email_template', $content, TRUE);
					$subject = $this->config->item('not_completed_class_subject');
					$data = array('class_incompleted_after_timeframe_mail_sent_at'=>date('Y-m-d H:i:s'));
					$log = "EMail sent to user[".$user['id']."] who has not login for 48 hours and class[".$user['classes_id']."] is not completed after 7 days timeframe";
				}
				
				if (send_email($subject, $email, $message)) {
					$this->db->update('users_has_classes', $data, array('id'=>$user['user_class_id']));
					generate_log($log);
				}
			}
		}
	}
}