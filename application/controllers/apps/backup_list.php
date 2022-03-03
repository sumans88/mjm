<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');
class Backup_list extends CI_Controller {
	function __construct(){
		parent::__construct();
	}
	function index(){
		$texts 		= glob(BACKUP_DIR. "*.bz2");
		$data['file'] = array();
		foreach($texts as $key => $text){
			$data['file'][$key]['no'] 	= ++$n;
			$data['file'][$key]['name'] = str_replace(BACKUP_DIR,'',$text);
		}
		//echo '<pre>';
		//print_r($data);
		//exit;
        render('apps/backup/list',$data,'apps');
	}
	function download($file){
		$this->load->helper('download');
		$data = file_get_contents(BACKUP_DIR.$file);
		force_download($file, $data); 
	}
}
