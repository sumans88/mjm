<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Newsletter_Subscribers extends CI_Controller {
	function __construct(){
		parent::__construct();
		$this->load->model('mailchimpModel');
	}
	function index(){
		render('apps/newsletter_subscribers/index',$data,'apps');
	}
	function records(){
		$data = $this->mailchimpModel->lists_subscribe('3b6703b579');
		foreach ($data['data'] as $key => $value) {
			$data['data'][$key]['create_date'] 	= iso_date_time($value['timestamp']);
			$data['data'][$key]['fname'] 	= $value['merges']['FNAME'];
			$data['data'][$key]['lname'] 	= $value['merges']['LNAME'];
			$data['data'][$key]['nomor'] = ++$i;

		}
		render('apps/newsletter_subscribers/records',$data,'blank');
	}
	function migrate_data(){
		$data = $this->db->query('select email from newsletter')->result_array();
		foreach ($data as $key => $value) {
			$this->mailchimpModel->subscribe_mailchimp('3b6703b579',$value['email'],'','');
		}
	}
	function del(){
		$id = $this->input->post('iddel');
		$this->mailchimpModel->unsubscribe_mailchimp('3b6703b579',$id);
	}
	
	public function add($id=''){
		if($id){
			$cek_exist	= $this->mailchimpModel->check_subscribe_by('3b6703b579', array('euid'=> $id));
			if(!$cek_exist['success_count']==1){
				die('404');
			}
			$data['judul']	= 'Edit';
			$data['proses']	= 'Update';
			$data['email']	= $cek_exist['data'][0]['merges']['EMAIL'];
			$data['fname']	= $cek_exist['data'][0]['merges']['FNAME'];
			$data['lname']	= $cek_exist['data'][0]['merges']['LNAME'];				
		}else{
			$data['judul']	= 'Add';
			$data['proses']	= 'Simpan';
			$data['email']	= '';
			$data['fname']	= '';
			$data['lname']	= '';		
		}
		render('apps/newsletter_subscribers/add',$data,'apps');
	}
	function proses($idedit=''){
		$id_user =  id_user();
		$this->layout 			= 'none';
		$post 					= purify($this->input->post());
		$ret['error']			= 1;
		$this->form_validation->set_rules('email', '"Email"', 'required'); 
		if ($this->form_validation->run() == FALSE){
			$ret['message']  = validation_errors(' ',' ');
		}
		else{   
			$this->mailchimpModel->subscribe_mailchimp('3b6703b579',$post['email'],$post['fname'],$post['lname']);
			set_flash_session('message',$ret['message']);
			$ret['error'] = 0;
		}
		echo json_encode($ret);
	}
	
}

/* End of file newsletter.php */
/* Location: ./application/controllers/apps/newsletter.php */