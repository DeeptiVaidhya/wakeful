<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');



if (!function_exists('send_email')) {
    /**
     * @desc Send email function to use globally from Application
     * @param type $subject
     * @param type $to
     * @param type $msg
     * @param type $attachment
     * @return type
     */
    function send_email($subject=FALSE,$to=FALSE,$msg=FALSE,$attachment=FALSE){
        return true;
        $CI =& get_instance();
        $CI->load->library('email');
        /* Add To Email */
        if($to!=FALSE){
            $CI->email->to($to);
        }
        /* Add From Email */
        $from = $CI->config->item('email_from_info');
        $CI->email->from($from,'ImHere');
        /* Add From subject */
        $CI->email->subject($subject);
        /* Add message content */
        $CI->email->message($msg);
        /* Add attachment */
        if($attachment!=FALSE){
            if(is_array($attachment)){
                foreach($attachment as $val){
                    $CI->email->attach($val,'attachment');
                }
            } else {
                $CI->email->attach($attachment,'attachment');
            }
        }
        /* Mail all data */
        $status = ($CI->email->send()) ? TRUE : FALSE ;
        return $status;
    }
}



