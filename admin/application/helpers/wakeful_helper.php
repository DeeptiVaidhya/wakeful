<?php

if (!defined('BASEPATH'))
    exit('No direct script access allowed');

if ( ! function_exists('p')) { // Temporary Function - Development Purpose
    function p($data){
        echo '<pre>'; print_r($data); echo '</pre>';
    }
}

if (!function_exists('get_file_unique_name')) {

    function get_file_unique_name($id = '') {
        if ($id != '') {
            $CI = & get_instance();
            $info_array = array('where' => array('id' => $id), 'table' => 'files');
            $info_array['fields'] = 'files' . '.*';
            $file_detail = $CI->db_model->get_data($info_array);
            if ($file_detail['result']) {
                $mime_type_or_return = $file_detail['result'][0]['type'];
                $type = explode('/', $mime_type_or_return)[0] . 's';
                $config = $CI->config->item('sftp_assets_' . $type);
                $file_path = $config['path'] . $file_detail['result'][0]['unique_name'];
                return $file_path;
            }
        }

        return false;
        // reads and outputs the file onto the output buffer
    }

}

if (!function_exists('generate_log')) {

    /**
     * @desc Used to add logs generated from APIs and user activity
     * @param string $msg
     * @return boolean
     */
    function generate_log($msg = '') {
        $CI = & get_instance();
        $message = '';
        $log_path = APPPATH . 'logs/';
        file_exists($log_path) OR mkdir($log_path, 0755, TRUE);
        if (!is_dir($log_path) OR ! is_really_writable($log_path)) {
            return FALSE;
        }
        $filepath = $log_path . 'system_log.php';

        if (!file_exists($filepath)) {
            $message .= "<" . "?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed'); ?" . ">\n\n";
        }

        if (!$fp = @fopen($filepath, FOPEN_WRITE_CREATE)) {
            return false;
        }

        $message .= date('Y-m-d H:i:s') . " [" . get_real_ip_addr() . "] " . $msg . "\n";
        flock($fp, LOCK_EX);
        fwrite($fp, $message);
        flock($fp, LOCK_UN);
        fclose($fp);

        @chmod($filepath, FILE_WRITE_MODE);
        return true;
    }

}


if (!function_exists('get_real_ip_addr')) {

    /**
     * @desc Used to get real ip for user activity
     * @return string
     */
    function get_real_ip_addr() {
        if (!empty($_SERVER['HTTP_CLIENT_IP'])) {   //check ip from share internet
            $ip = $_SERVER['HTTP_CLIENT_IP'];
        } elseif (!empty($_SERVER['HTTP_X_FORWARDED_FOR'])) {   //to check ip is pass from proxy
            $ip = $_SERVER['HTTP_X_FORWARDED_FOR'];
        } else {
            $ip = $_SERVER['REMOTE_ADDR'];
        }
        return $ip;
    }

}


if (!function_exists('aes_256_encrypt')) {

    /**
     * @desc Used to encrypt a string in AES 256, to store in Database
     * @param string $str
     */
    function aes_256_encrypt($str = '') {
        $CI = & get_instance();
        $config = $CI->config->item('encryption');
        $CI->encryption->initialize($config);
        if ($str != '') {
            return $CI->encryption->encrypt($str);
        }
        return '';
    }

}
if (!function_exists('aes_256_decrypt')) {

    /**
     * @desc Used to decrypt a encrypted AES 256 string, fetched from Database
     * @param string $str
     */
    function aes_256_decrypt($str = '') {
        $CI = & get_instance();
        $config = $CI->config->item('encryption');
        $CI->encryption->initialize($config);
        if ($str != '') {
            return $CI->encryption->decrypt($str);
        }
        return '';
    }

}

if (!function_exists('send_email')) {

    /**
     * @desc Send email function to use globally from Application
     * @param type $subject
     * @param type $to
     * @param type $msg
     * @param type $attachment
     * @return type
     */
    function send_email($subject = FALSE, $to = FALSE, $msg = FALSE, $is_welcome_email=FALSE, $study_id=FALSE, $attachment = FALSE) {
        $CI = & get_instance();
        $CI->load->library('email');
        $CI->email->clear();
        /* Add To Email */
        if ($to != FALSE) {
            $CI->email->to($to);
		}
		if ($is_welcome_email && $CI->config->item('cc_welcome_email')==true && $study_id) {
            $CI->email->cc(get_study_cc_email($study_id));
		}
        $from = $CI->config->item('email_from_info');
        $CI->email->from($from,$CI->config->item('site_name'));

        /* Add From subject */
        $CI->email->subject($subject);
        /* Add message content */
        $CI->email->message($msg);
        /* Add attachment */
        if ($attachment != FALSE) {
            if (is_array($attachment)) {
                foreach ($attachment as $val) {
                    $CI->email->attach($val, 'attachment');
                }
            } else {
                $CI->email->attach($attachment, 'attachment');
            }
        }
        $status = ($CI->email->send()) ? TRUE : FALSE;
        return $status;
    }
}

if (!function_exists('get_file')) {

    function get_file($id = '', $url = FALSE) {
//        var_dump($_SERVER['SERVER_ADDR']);
//        if ($_SERVER['SERVER_ADDR'] != $_SERVER['REMOTE_ADDR']) {
//            $this->output->set_status_header(400, 'No Remote Access Allowed');
//            exit; //just for good measure
//        }
        $CI = & get_instance();
        $info_array = array('where' => array('id' => $id), 'table' => 'files');
        $info_array['fields'] = 'files' . '.*';
        $file_detail = $CI->db_model->get_data($info_array);
        if ($file_detail['result']) {
            $mime_type_or_return = $file_detail['result'][0]['type'];
            $type = explode('/', $mime_type_or_return)[0] . 's';
            if ($type == 'audios' || $type == 'videos') {
                $config = $CI->config->item('sftp_assets_' . $type);
                $file_path = $config['url'] . $file_detail['result'][0]['unique_name'];
                $file_name = $file_detail['result'][0]['name'];
            } else {
            $config = $CI->config->item('assets_' . $type);
            $upload_path = check_directory_exists($config['path']);
            $file_path = base_url($upload_path . '/' . $file_detail['result'][0]['unique_name']);
            $file_name = $file_detail['result'][0]['name'];
            }
            // Return the image or output it?
            switch ($type) {
                case 'images':
                    $file = '<img class="img-responsive box-image" src="' . $file_path . '" alt="image" title="image"><p><small>' . $file_name . '</small></p>';
                    break;
                case 'audios':
                    $file = '<audio controls class="box-audio"><source src="' . $file_path . '" type="' . $mime_type_or_return . '"></audio><p><small>' . $file_name . '</small></p>';
                    break;
                case 'videos':
                    $file = '<video class="box-video" width="100%" height="200" controls><source src="' . $file_path . '" type="' . $mime_type_or_return . '"></video><p><small>' . $file_name . '</small></p>';
                    break;

                default:
                    $file = '';
                    break;
            }

            if ($url) {
                return array('type' => $type, 'url' => $file_path);
            }
            return $file;
        }
        return "";
        // reads and outputs the file onto the output buffer
    }

}
/**
 * Creating slug from title/name
 *
 * @access  public
 * @param   string
 * @return  string
 */
if (!function_exists('create_slug')) {

    function create_slug($slug) {

        $lettersNumbersSpacesHyphens = '/[^\-\s\pN\pL]+/u';
        $spacesDuplicateHypens = '/[\-\s]+/';

        $slug = preg_replace($lettersNumbersSpacesHyphens, '', $slug);
        $slug = preg_replace($spacesDuplicateHypens, '-', $slug);

        $slug = trim($slug, '-');

        return mb_strtolower($slug, 'UTF-8');
    }

}


if (!function_exists('assets_url')) {

    /**
     * @desc Function to get assets URL
     * @param type $uri
     * @return type
     */
    function assets_url($uri = '') {
        $CI = & get_instance();
        return $CI->config->item('base_url') . $CI->config->item('assets_url') . trim($uri, '/');
    }

}
if (!function_exists('re_arrange_files')) {

    function re_arrange_files($file_post = array(), $name = '') {
        $file_ary = array();
        $file_name = $file_post['name'];
        $file_keys = array_keys($file_post);

        foreach ($file_name as $i => $f_name) {
            
            foreach ($file_keys as $key) {
                $file_ary[$name . '_' . $i][$key] = $file_post[$key][$i];
            }
        }
        return $file_ary;
    }

}


if (!function_exists('check_directory_exists')) {

    /**
     * @desc Check the file or directory exists or not, if not then create it. Returns dir full path
     * @param type $file_name
     * @return type
     */
    function check_directory_exists($file_name = '') {
        $file_name = str_replace('\\', '/', $file_name);/** Replace for Linux server */
        if (!file_exists($file_name)) {
            @mkdir($file_name, 0777, true);
            $my_file = $file_name . '/index.html';
            $handle = @fopen($my_file, 'w');

            $data = '<!DOCTYPE html><html><head><title>403 Forbidden</title></head><body><p>Directory access is forbidden.</p></body></html>';
            @fwrite($handle, $data);
            @fclose($handle);
        }
        return $file_name;
    }

}

if (!function_exists('get_plugins_in_template')) {

    /**
     * @desc Function to load JS and/or CSS for a plugin
     * @param type $plugin
     */
    function get_plugins_in_template($plugin = '') {
        $CI = & get_instance();
        switch ($plugin) {
            case 'datatable':
                $CI->template->javascript->add('assets/js/jquery.dataTables.min.js');
                $CI->template->javascript->add('assets/js/dataTables.bootstrap.min.js');
                // $CI->template->javascript->add('assets/js/dataTables.responsive.min.js');
                // Dynamically add a css stylesheet
                $CI->template->stylesheet->add('assets/css/dataTables.bootstrap.min.css');
                $CI->template->stylesheet->add('assets/css/responsive.bootstrap.min.css');
                break;
            case 'color-picker':
                $CI->template->javascript->add('assets/js/bootstrap-colorpicker.min.js');
                // Dynamically add a css stylesheet
                $CI->template->stylesheet->add('assets/css/bootstrap-colorpicker.min.css');
                break;
            default:
                break;
        }
    }

}

if (!function_exists('is_user_has_course')) {

    /**
     * @desc Function to load JS and/or CSS for a plugin
     * @param type $plugin
     */
    function is_user_has_course($course_id) {
        $CI = & get_instance();
        $login_user_detail = $CI->session->userdata('logged_in');
        $user_type =$login_user_detail->user_type ;
        $user_id = $login_user_detail->id;
        $course_id_array = $CI->course->get_user_has_course($user_id);
        if (in_array($course_id,$course_id_array) || $user_type == 1 || $user_type == 3)
           return true;
           else     
           return false;
    }
}

if (!function_exists('is_user_has_organization')) {

    /**
     * @desc Function to load JS and/or CSS for a plugin
     * @param type $plugin
     */
    function is_user_has_organization($org_id) {
        $CI = & get_instance();
        $login_user_detail = $CI->session->userdata('logged_in');
        $user_type =$login_user_detail->user_type ;
        $user_id = $login_user_detail->id; 

        $org_id_array = $CI->course->get_user_has_organization($user_id);
        if (in_array($org_id,$org_id_array) || $user_type == 1 || $user_type == 3)
           return true;
           else     
           return false;
    }
}

if (!function_exists('get_course_id_by_class')) {

    /**
     * @desc Function to get course id by class id
     ** @param type $class_id
     */
    function get_course_id_by_class($class_id) {
        $CI = & get_instance();
        
        $res = $CI->db->select('courses_id')->where('id', $class_id)->get('classes')->row();
        
        if (!empty($res) && isset($res->courses_id)){
            return $res->courses_id;
        }
    }
}

if (!function_exists('decodeurl')) {
    function decodeurl($value)
    {
        return trim(urldecode($value));
    }
}

if (!function_exists('user_has_study')) {
    function user_has_study($user_id)
    {
        $CI = & get_instance();
        $res = $CI->db->select('study_id')->where('users_id', $user_id)->get('users_has_courses')->row();
        return isset($res->study_id) ? $res->study_id : null;
    }
}
if (!function_exists('get_study_cc_email')) {
    function get_study_cc_email($study_id)
    {
        $CI = & get_instance();
        $res = $CI->db->select('cc_email')->where('id',$study_id)->get('study')->row();
        return isset($res->cc_email) && !is_null($res->cc_email) ? $res->cc_email : null;
    }
}

if (!function_exists('last_page_position')) {
    function last_page_position($class_id)
    {
        $CI = & get_instance();
        $res = $CI->db->select('id')->where('classes_id',$class_id)->order_by('id','desc')->get('pages')->row();
        return isset($res->id) && !is_null($res->id) ? $res->id : null;
    }
}


if (!function_exists('last_page_id')) {
    function last_page_id($class_id)
    {
        $CI = & get_instance();
        $res = $CI->db->select('*')->where('classes_id',$class_id)->order_by('id','desc')->limit(1)->get('pages')->row();        
        return isset($res->id) && !is_null($res->id) ? $res->id : null;
    }
}




/* End of file wakeful_helper.php */
/* Location: ./application/helpers/wakeful_helper.php */
