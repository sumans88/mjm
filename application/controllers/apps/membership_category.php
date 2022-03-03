<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class membership_category extends CI_Controller{

	function __construct(){
		parent::__construct();
		$this->load->model('membership_category_model');
	}

	function index(){
		render('apps/membership_category/index',$data,'apps');
	}

	public function add($id=''){
		if($id){
			$data = $this->membership_category_model->findById($id);
			if(!$data){
				die('404');
			}
			$data 					= quote_form($data);
			$data['judul']			= 'Edit';
			$data['proses']			= 'Update';
		}else{
			die('404');
			/*
				$data['judul']			= 'Add';
				$data['proses']			= 'Save';
				$data['name'] 			= '';
				$data['uri_path']		= '';
				$data['teaser']			= '';
				$data['page_content']	= '';
				$data['id'] 			= '';
			*/
		}

		$img_thumb						= image($data['img'],'small');
		$imagemanager					= imagemanager('img',$chair_img_thumb);
		$data['img']					= $imagemanager['browse'];
		$data['imagemanager_config']	= $imagemanager['config'];
		$data['list_status_publish'] = selectlist2(
			array(
				'table'=>'status_publish',
				'selected'=>$data['id_status_publish']
				)
			);

		render('apps/membership_category/add',$data,'apps');
	}

	public function view($id=''){
		if($id){
			$data = $this->membership_category_model->findById($id);

			$data['img_thumb'] 	= image($data['img'],'small');
			$data['img_ori'] 	= image($data['img'],'large');
			if(!$data){
				die('404');
			}
			$data['page_name'] 	= quote_form($data['page_name']);
			$data['teaser'] 	= quote_form($data['teaser']);
		}
		render('apps/membership_category/view',$data,'apps');
	}

	function records(){
		
		$data = $this->membership_category_model->records();		
		render('apps/membership_category/records',$data,'blank');
	}

	function proses($idedit=''){
		$this->layout         = 'none';
		$post                 = purify($this->input->post());
		$post['page_content'] = htmlspecialchars_decode(urldecode($post['page_content']));
		$ret['error']         = 1;

		$this->form_validation->set_rules('page_content', '"page Name"', 'required');
		if ($this->form_validation->run() == FALSE){
			$ret['message']  = validation_errors(' ',' ');
		}else{
			$this->db->trans_start();

			if($idedit){
				auth_update();
				$ret['message'] = 'Update Success';
				$act			= "Update membership category";
				$this->membership_category_model->update($post,$idedit);
			}else{
				auth_insert();
				$ret['message'] = 'Insert Success';
				$act			= "Insert membership_category";
				$this->membership_category_model->insert($post);
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
		$data 	= $this->membership_category_model->delete($id);
		detail_log();
		insert_log("Delete membership_category");
	}

	function select_page(){
		render('apps/membership_category/select_page',$data,'blank');
	}

	function record_select_page(){
		$data = $this->membership_category_model->records();
		foreach ($data['data'] as $key => $value) {
			$data['data'][$key]['page_name'] = quote_form($value['page_name']);
		}

		render('apps/membership_category/record_select_page',$data,'blank');
	}

}

/* End of file pages.php */
/* Location: ./application/controllers/apps/committee.php */