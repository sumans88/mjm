<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');
class Lang extends CI_Controller {
	function __construct(){
		parent::__construct();
		
	}
    function index(){
        $this->lang->load("script",LANGUAGE);
        $lang = $this->lang->language;
        foreach ($lang as $key => $value) {
            echo "var ".$key." = '$value';";
        }
    }
}