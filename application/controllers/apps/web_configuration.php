<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class web_configuration extends CI_Controller{

	function __construct(){
		parent::__construct();
		$this->load->model('web_configuration_model');
	}

	function index(){
		$data['list_status_publish']   = selectlist2(array('table'=>'status_publish','title'=>'All Status','selected'=>$data['id_status_publish']));
		
		render('apps/web_configuration/index',$data,'apps');
	}

	public function add($id=''){
		if($id){
			$data = $this->web_configuration_model->findById($id);
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
			$data['value']	= '';
			$data['id'] 			= '';
			$data['address'] 		= '';
			$data['number'] 		= '';
			$data['email'] 			= '';
			$data['website'] 		= '';
			
		}

		render('apps/web_configuration/add',$data,'apps');
	}

	public function view($id=''){
		if($id){
			$data = $this->web_configuration_model->findById($id);

			$data['img_thumb'] 	= image($data['img'],'small');
			$data['img_ori'] 	= image($data['img'],'large');
			if(!$data){
				die('404');
			}
			$data['page_name'] 	= quote_form($data['page_name']);
			$data['teaser'] 	= quote_form($data['teaser']);
		}
		render('apps/web_configuration/view',$data,'apps');
	}

	function records(){
		$data = $this->web_configuration_model->records();		
		render('apps/web_configuration/records',$data,'blank');
	}

	function proses($idedit=''){
		$this->layout         = 'none';
		$post                 = purify($this->input->post());
		$ret['error']         = 1;
		$post['img']          = $post['img'][0];
		$post['value'] = htmlspecialchars_decode(urldecode($post['value'.$key.'']));;

		$this->form_validation->set_rules('value', '"Value  "', 'required');
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
				$act			= "Update web_configuration";
				$this->web_configuration_model->update($post,$idedit);
			}else{
				auth_insert();
				$ret['message'] = 'Insert Success';
				$act			= "Insert web_configuration";
				$this->web_configuration_model->insert($post);
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
		$data 	= $this->web_configuration_model->delete($id);
		detail_log();
		insert_log("Delete web_configuration");
	}

	function select_page(){
		render('apps/web_configuration/select_page',$data,'blank');
	}

	function record_select_page(){
		$data = $this->web_configuration_model->records();
		foreach ($data['data'] as $key => $value) {
			$data['data'][$key]['page_name'] = quote_form($value['page_name']);
		}

		render('apps/web_configuration/record_select_page',$data,'blank');
	}

}

/* End of file web_configuration.php */
/* Location: ./application/controllers/apps/web_configuration.php */