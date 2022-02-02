<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class MY_Form_validation extends CI_Form_validation {
    protected $CI;

    public function __construct($rules = array()) {
	    parent::__construct($rules);
        // reference to the CodeIgniter super object
        $this->CI =& get_instance();
    }
    
    /**
	 * Convert PHP tags to entities
	 *
	 * @param	string
	 * @return	string
	 */
	public function is_valid_password($str)
	{
	    $this->set_message('is_valid_password', 'The password must be contain minimum 8 characters, at least 1 uppercase letter, 1 lowercase letter, 1 number and 1 special character.');
		return ( ! preg_match('/^(?=.*[a-z])(?=.*[A-Z])(?=.*\d)(?=.*[$@$!%*?&])[A-Za-z\d$@$!%*?&]{8,}/', $str)) ? FALSE : TRUE;
	}

}
