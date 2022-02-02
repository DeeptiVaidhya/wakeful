<?php

defined('BASEPATH') OR exit('No direct script access allowed');
$config['site_name'] = 'Wakeful';
$config['app_url'] = 'http://pub1.brightoutcome-dev.com/wakeful/member/';
//$config['app_url'] = 'http://10.10.2.34:8100/';
//$config['test_app_url'] = 'http://localhost:8100/';
$config['assets_url'] = 'assets/';


$config['assets_images'] = array('path' => 'assets/uploads/images', 'allowed_types' => 'gif|png|jpg|jpeg');
$config['assets_audios'] = array('path' => 'assets/uploads/audios', 'allowed_types' => 'mp3', 'max_size' => 512000);
$config['assets_videos'] = array('path' => 'assets/uploads/videos', 'allowed_types' => 'mp4|ogg|webm', 'max_size' => 512000);
$config['class_duration'] = 8; // in Days

$config['encryption'] = array('cipher' => 'aes-256', 'mode' => 'CBC', 'driver' => 'openssl', 'key' => 'mY3lEnkGvUorVv4E2pBo)#Cm8wPY-jh@');

/**
 * Records Per page in Pagination
 */
$config['pager_limit'] = 10;


$config['site_contact_us_email'] = $config['site_name'] . "@brightoutcome.com";

$config['store_number_of_password'] = 6; // How much previous password store for password history
$config['lockout_time'] = 10; // User account active after 10 min
/**
 * Email Config
 */
$config['email_from_info'] = $config['site_name'] . '@brightoutcome.com';
$config['contact_us_email'] = "priyanka.dateer@ideavate.com,rohan.verma@ideavate.com";


$config['expiration_note'] = 'Please note that the link will expire after %s hours. If you did not make this request, then you can safely ignore this email.';

$config['reset_password_subject'] = 'Your new Wakeful information awaits';
$config['reset_password_btn_titte'] = 'Reset Password';
$config['reset_password_heading'] = 'Password Reset Request';
$config['reset_password_message'] = "<h2>Hi %s,</h2>We received a request to reset the password associated with this e-mail address. Please click the link below to start the password reset process.";


$config['verify_email_subject'] = 'New user registration at Wakeful (pending approval)';
$config['verify_email_btn_titte'] = 'VERIFY EMAIL';
$config['verify_email_message'] = "<h2>Hi %s,</h2>You have successfully created a Wakeful Account. There’s just one more step before you get started. Please click the button below to verify your email address and to activate your account.";
$config['verify_email_note'] = "Please note that the link will expire after %s hours. If you did not make this request, then you can safely ignore this email";

$config['intention_subject'] = 'Intention of %s';
$config['intention_message'] = '<p>%s</p>';

$config['contact_us_subject'] = 'New Inquiry';
$config['contact_us_message'] = "<p><strong>You have received a new message from the contact us form.</strong></p><p><strong>Name:- </strong>  %s</p><p><strong>Email:- </strong>  %s</p><p><strong>Message:-</strong>  %s</p>";

$config['class_unlocked_heading'] = 'Your next Wakeful class is ready';
$config['class_unlocked_subject'] = 'Your next Wakeful class is ready';
$config['class_unlocked_message'] = "Hi there! A new Wakeful class is available. Please sign in here to begin: <a href='".$config['app_url']."' >".$config['app_url']."</a>. We encourage you to complete it within the next two days so that you have time to practice before the next class. Thank you!";

$config['not_accessed_heading'] = 'You’re so close! Let’s finish this class before the next one starts.';
$config['not_accessed_subject'] = 'Your current Wakeful class will close on %s. Let’s finish this class before the next one starts!';
$config['not_accessed_message'] = "Hey there! It looks like you didn’t finish the last class you started BUT you are still within the 1-week timeframe to get it done. Click here <a href='".$config['app_url']."' >".$config['app_url']."</a> to re-join the class so you can finish it before the next one is released. We’re rooting for you!";

$config['not_completed_class_heading'] = 'A huge part of the practice is re-starting the practice';
$config['not_completed_class_subject'] = 'A huge part of the practice is re-starting the practice';
$config['not_completed_class_message'] = "Hey there – are you still there? It looks like you haven’t signed in for a while. Whether you have missed one or more classes, no worries. Life happens. We get it. But something is definitely better than nothing when it comes to mindfulness training, and you can still get something by clicking here <a href='".$config['app_url']."' >".$config['app_url']."</a>. You can do it!";

// SFTP details for audio/video upload
$config['sftp_details'] = array('hostname' => 'macaw.brightoutcome.com', 'username' => 'mpguest', 'password' => '&wCU8$2QFx');

$config['sftp_assets_audios'] = array('url' => 'http://macaw.brightoutcome.com/wakeful/audio-ideavate/', 'path' => '/var/www/html/wakeful/audio/', 'allowed_types' => 'mp3', 'max_size' => 512000);
$config['sftp_assets_videos'] = array('url' => 'http://macaw.brightoutcome.com/wakeful/video-ideavate/', 'path' => '/var/www/html/wakeful/video/', 'allowed_types' => 'mp4|ogg|webm', 'max_size' => 512000);

// Background image
$config['desktop'] = array('main_page' => 'BG-Class-DT-Full-width-characters.jpg', 'inner_page' => 'BG-Class-DT-Full-width-plain.jpg');
$config['tablet'] = array('main_page' => 'Background-Class-TAB-REWORKED.jpg', 'inner_page' => 'Background-Class-TAB-plain.jpg');
$config['mobile'] = array('main_page' => 'Background-Class-MOB-REWORKED.jpg', 'inner_page' => 'BG-Class-MOB-plain.jpg');

$config['course_settings']=array(
	array('key'=>'CLASSES_RE-ENTERABLE','description'=>'Setting is used to enable classes which are already completed or not.'),
	array('key'=>'AUDIO/VIDEO_BUTTON_CLICKABLE_BEFORE_FINISHING','description'=>'When this setting is true then audio and/or video button will be always enabled, whether or not user listened/watched the audio/video.'),
	array('key'=>'JOURNEY_PAGE_VIEWABLE','description'=>'Enable a class/journey to show in application.'),
);

$config['testing_cron'] = false; // set it to true if required to get the notifications in a day and 1 hour


$config['create_password_btn_titte'] = 'Create Password';
$config['create_password_heading'] = 'Password Create Request';
$config['create_password_message'] = "<h2>Hi %s,</h2></p>We received a request to create the password associated with this e-mail address. Please click the link below to start the password create process.";

$config['invite_email_subject'] = 'New user invitation at Wakeful';
$config['invite_email_btn_titte'] = 'Create Password';
$config['invite_email_message'] = "<h2>Hi %s,</h2>You are invited for creating a Wakeful Account. There’s just one more step before you get started. Please click the button below to register yourself and create your account.";

 $config['session_logout_time'] = 1800; // Session logout time in seconds
 
//Testing Flag : If user wants to visit all Classes in a single week/day, and not week wise
$config['CLASS_ENABLE_FOR_ALL_WEEKS'] = FALSE;

$config['cc_welcome_email_info'] = 'bruriah.horowitz@northwestern.edu';
$config['cc_welcome_email'] = TRUE;

$config['community_pager_limit'] = 10;