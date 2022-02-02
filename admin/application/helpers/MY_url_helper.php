<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');

/**
 * Assets URL
 *
 * @access  public
 * @param   string
 * @return  string
 */
if ( ! function_exists('assets_url'))
{
    function assets_url($uri = '')
    {
        $CI =& get_instance();
        return $CI->config->item('base_url').$CI->config->item('assets_url') . trim($uri, '/');
    }
}

if ( ! function_exists('check_directory_exists')) {
    /** Check the file or directory exists or not, if not then create it. Returns dir full path */
	function check_directory_exists($file_name='') {
		$file_name= str_replace('\\','/',$file_name);/** Replace for Linux server */
		if (!file_exists($file_name)) {
    		@mkdir($file_name, 0777, true);
		}
		return $file_name;
	}   
}

/* End of file MY_url_helper.php */
/* Location: ./application/helpers/MY_url_helper.php */