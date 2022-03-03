<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');
class MY_Session extends CI_Controller
{
  public function __construct()
	{
		
		$this->CI =& get_instance();
		
		$this->CI->load->driver('session');
		
	}
	
	function session()
	{
	    return $_SESSION;
	}
}