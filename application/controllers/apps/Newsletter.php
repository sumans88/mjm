<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Newsletter extends CI_Controller {
	function __construct(){
        parent::__construct();
        $this->load->model('newsletterModel');
        // $this->parentMenu = getParentMenu();
        // echo $this->parentMenu;exit;
    }

	public function index()
	{
		
	}

	function proses_newsletter($idedit=''){
		$post         = purify($this->input->post());
		$ret['error'] = 1;
		$ret['msg']   = "error";
		if ($this->newsletterModel->insert($post)) {
			$ret['error'] = 0;			
		};	
		echo json_encode($ret);
	}
}

/* End of file newsletter.php */
/* Location: ./application/controllers/apps/newsletter.php */