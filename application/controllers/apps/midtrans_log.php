<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class midtrans_log extends CI_Controller{

	function __construct(){
		parent::__construct();
		$this->load->model('midtrans_log_model');
	}

	function index(){
		render('apps/midtrans_log/index',$data,'apps');
	}

	public function add($id=''){
		if($id){
			$data = $this->midtrans_log_model->findById($id);
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
			
		}

		render('apps/midtrans_log/add',$data,'apps');
	}

	public function view($id=''){
		if($id){
			$data = $this->midtrans_log_model->findById($id);

			$data['img_thumb'] 	= image($data['img'],'small');
			$data['img_ori'] 	= image($data['img'],'large');
			if(!$data){
				die('404');
			}
			$data['page_name'] 	= quote_form($data['page_name']);
			$data['teaser'] 	= quote_form($data['teaser']);
		}
		render('apps/midtrans_log/view',$data,'apps');
	}

	function records(){
		$data = $this->midtrans_log_model->records();		
		render('apps/midtrans_log/records',$data,'blank');
	}

	function proses($idedit=''){
		$this->layout         = 'none';
		$post                 = purify($this->input->post());
		$ret['error']         = 1;
		$post['img']          = $post['img'][0];
		$post['page_content'] = htmlspecialchars_decode(urldecode($post['page_content'.$key.'']));;

		$this->form_validation->set_rules('name', '"Company Name"', 'required');
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
				$act			= "Update midtrans_log";
				$this->midtrans_log_model->update($post,$idedit);
			}else{
				auth_insert();
				$ret['message'] = 'Insert Success';
				$act			= "Insert midtrans_log";
				$this->midtrans_log_model->insert($post);
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
		$data 	= $this->midtrans_log_model->delete($id);
		detail_log();
		insert_log("Delete midtrans_log");
	}

	function select_page(){
		render('apps/midtrans_log/select_page',$data,'blank');
	}

	function record_select_page(){
		$data = $this->midtrans_log_model->records();
		foreach ($data['data'] as $key => $value) {
			$data['data'][$key]['page_name'] = quote_form($value['page_name']);
		}

		render('apps/midtrans_log/record_select_page',$data,'blank');
	}

}

/* End of file midtrans_log.php */
/* Location: ./application/controllers/apps/midtrans_log.php */