<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed'); 

/**
 * Extended Form_validation class to provide own check-methods.
 *
 * @author Konstantin Voth <konstantin.voth@fh-duesseldorf.de>
 */
class FHD_Form_validation extends CI_Form_validation 
{
	public function __construct($rules = array())
	{
		parent::__construct($rules);
		$this->CI->lang->load('FHD_form_validation');
	}

	/**
	 * This method checks the form value for alpha, dash, underscore and whitespaces.
	 *
	 * @see http://codeigniter.com/forums/viewthread/158696/
	 * 
	 * @param  String $str Which value should be checked.
	 * @return FALSE if there are other characters than described, TRUE otherwise
	 */
	public function alpha_dash_space($str)
	{
		// return ( ! preg_match("/^([-a-z0-9_ ])+$/i", $str)) ? FALSE : TRUE;
		return ( ! preg_match("/^([-a-z_ ])+$/i", $str)) ? FALSE : TRUE;
	}

}

// END Form Validation Class

/* End of file FHD_Form_validation.php */
/* Location: ./application/libraries/Form_validation.php */