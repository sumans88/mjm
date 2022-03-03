<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');
class Directory extends CI_Controller {
	function __construct(){
		parent::__construct();
	
	}
    function index($uri_path){
    	print_r("Ssas");
    	render('pages/committee-detail',$data);
    }
}