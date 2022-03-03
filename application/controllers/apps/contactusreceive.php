<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class ContactUsReceive extends CI_Controller {
	function __construct(){
		parent::__construct();
		$this->load->model('contactusreceiveModel');
	}
	function index(){
		// $data['list_topic'] = selectlist2(array('table'=>'contact_us_topic','title'=>'All Topic','selected'=>$data['id_topic']));
		$data['list_status_publish']   = selectlist2(array('table'=>'status_publish','title'=>'All Status','selected'=>$data['id_status_publish']));

		render('apps/contact_us_receive/index',$data,'apps');
	}
	public function add($id=''){
		if($id){
			$data = $this->contactusreceiveModel->findById($id);
            if(!$data){
				die('404');
			}
			$data 				= quote_form($data);
			$data['judul']		= 'Edit';
			$data['proses']		= 'Update';
		}
		else{
			$data['judul'] 		= 'Add';
			$data['proses']		= 'Save';
            $data['id'] 		= '';
			$data['email'] 		= '';
		}

		$data['list_language']      = selectlist2(array('table'=>'language','title'=>'Select Language','selected'=>$data['id_lang']));
		
		$data['list_default_email'] = selectlist2(array('table'=>' ref_email_category','where'=>array('is_delete'=>0),'title'=>'Select Template Email','selected'=>$data['id_email_category']));
		$data['list_status_publish']   = selectlist2(array('table'=>'status_publish','title'=>'All Status','selected'=>$data['id_status_publish']));
		

		render('apps/contact_us_receive/add',$data,'apps');
	}
	function records(){
		$data = $this->contactusreceiveModel->records();
		render('apps/contact_us_receive/records',$data,'blank');
	}
	
	function del(){
		$this->db->trans_start();   
		$id = $this->input->post('iddel');
		$this->contactusreceiveModel->delete($id);
		detail_log(); 
		insert_log('Delete Email Received');
		$this->db->trans_complete();
	}
	function proses($idedit=''){
		$id_user 		=  id_user();
		$this->layout 	= 'none';
		$post 			= purify($this->input->post());
		$ret['error'] 	= 1;
		
        $this->form_validation->set_rules('email', '"Email"', 'required|valid_email'); 

		if ($this->form_validation->run() == FALSE){
			$ret['message']  = validation_errors(' ',' ');
		}
		else{
			$this->db->trans_start();   
			if($idedit){
                auth_update();
				$ret['message'] = 'Update Success';
				$act			= "Update Email Received";
				$iddata = $this->contactusreceiveModel->update($post,$idedit);
			}
			else{
				auth_insert();
				$ret['message'] = 'Insert Success';
				$act			= "Insert Email Received";
				$iddata = $this->contactusreceiveModel->insert($post);
			}
			detail_log();
			insert_log($act);
			$this->db->trans_complete();
			$this->session->set_flashdata('message',$ret['message']);
			$ret['error'] = 0;
		}
		echo json_encode($ret);
	}
}

/* End of file contactusreceive.php */
/* Location: ./application/controllers/apps/contactusreceive.php */