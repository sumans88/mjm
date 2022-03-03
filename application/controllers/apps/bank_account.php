<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class bank_account extends CI_Controller{

	function __construct(){
		parent::__construct();
		$this->load->model('bank_account_model');
	}

	function index(){
		$data['list_status_publish']   = selectlist2(array('table'=>'status_publish','title'=>'All Status','selected'=>$data['id_status_publish']));
		
		render('apps/bank_account/index',$data,'apps');
	}

	public function add($id=''){
		if($id){
			$data = $this->bank_account_model->findById($id);
			if(!$data){
				die('404');
			}
			$data 					= quote_form($data);
			$data['judul']			= 'Edit';
			$data['proses']			= 'Update';
		}else{
			$data['judul']			= 'Add';
			$data['proses']			= 'Save';
			$data['name'] 			= '';
			$data['uri_path']		= '';
			$data['teaser']			= '';
			$data['page_content']	= '';
			$data['id'] 			= '';
			$data['address'] 		= '';
			$data['number'] 		= '';
			$data['email'] 			= '';
			$data['website'] 		= '';
			$data['bank_name']    	= '';
			$data['branch']       	= '';
			$data['city']         	= '';
			$data['account_number'] 	= '';
			$data['account_name'] 	= '';
			$data['bank_address'] 	= '';
			$data['bank_key'] 	= '';
			$data['swift_code'] 	= '';
			
		}
		$img_thumb						= image($data['img'],'small');
		$imagemanager					= imagemanager('img',$img_thumb,128,128);
		$data['img']					= $imagemanager['browse'];
		$data['imagemanager_config']	= $imagemanager['config'];
		$data['list_category'] = selectlist2(
			array(
				'table'=>'ref_privileges_category',
				'selected'=>$data['id_category'],
				'where'=> array('id_status_publish'=> 2 )
				)
			);
		$data['list_status_publish'] = selectlist2(
			array(
				'table'=>'status_publish',
				'selected'=>$data['id_status_publish'],

				)
			);

		render('apps/bank_account/add',$data,'apps');
	}

	public function view($id=''){
		if($id){
			$data = $this->bank_account_model->findById($id);

			$data['img_thumb'] 	= image($data['img'],'small');
			$data['img_ori'] 	= image($data['img'],'large');
			if(!$data){
				die('404');
			}
			$data['page_name'] 	= quote_form($data['page_name']);
			$data['teaser'] 	= quote_form($data['teaser']);
		}
		render('apps/bank_account/view',$data,'apps');
	}

	function records(){
		$data = $this->bank_account_model->records();		
		render('apps/bank_account/records',$data,'blank');
	}

	function proses($idedit=''){
		$this->layout         = 'none';
		$post                 = purify($this->input->post());
		$ret['error']         = 1;
		$post['img']          = $post['img'][0];
		$post['page_content'] = htmlspecialchars_decode(urldecode($post['page_content'.$key.'']));;

		$this->form_validation->set_rules('bank_name', '"Bank Name"', 'required');
		if ($this->form_validation->run() == FALSE){
			$ret['message']  = validation_errors(' ',' ');
		}else{
			$this->db->trans_start();

			if($idedit){
				if ($post['img'] == '') {
					unset($post['img']);
				}
				auth_update();
				$ret['message'] = 'Update Success';
				$act			= "Update Bank Account";
				$this->bank_account_model->update($post,$idedit);
			}else{
				auth_insert();
				$ret['message'] = 'Insert Success';
				$act			= "Insert Bank Account";
				$this->bank_account_model->insert($post);
			}

			detail_log();
			insert_log($act);
			$this->db->trans_complete();

			$this->session->set_flashdata('message',$ret['message']);
			$ret['error'] = 0;
		}
		echo json_encode($ret);
	}

	function del(){
		auth_delete();

		$id 	= $this->input->post('iddel');
		$data 	= $this->bank_account_model->delete($id);
		detail_log();
		insert_log("Delete Bank Account");
	}

	function select_page(){
		render('apps/bank_account/select_page',$data,'blank');
	}

	function record_select_page(){
		$data = $this->bank_account_model->records();
		foreach ($data['data'] as $key => $value) {
			$data['data'][$key]['page_name'] = quote_form($value['page_name']);
		}

		render('apps/bank_account/record_select_page',$data,'blank');
	}

}

/* End of file bank_account.php */
/* Location: ./application/controllers/apps/bank_account.php */