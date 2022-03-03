<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class event_price extends CI_Controller{

	function __construct(){
		parent::__construct();
		$this->load->model('eventprice_model');
	}

	function index(){
		$data['list_status_publish'] = selectlist2(array('table'=>'status_publish','title'=>'All Status'));
		render('apps/event_price/index',$data,'apps');
		}

	public function add($id=''){
		
		if($id){
			$data = $this->eventprice_model->findById($id);
			

			if(!$data){
				die('404');
			}
			$data 					= quote_form($data);
			$data['judul']			= 'Edit';
			$data['proses']			= 'Update';
		}else{
			// $data['list_status_publish'] = selectlist(array('table'=>'status_publish','title'=>'All Status'));
			$data['judul']			= 'Add';
			$data['proses']			= 'Save';
			$data['name'] 			= '';
			$data['uri_path']		= '';
			$data['teaser']			= '';
			$data['page_content']	= '';
			$data['id'] 			= '';
			$data['alias'] 			= '';
			$data['amount'] 		= '0';
			
		}
			$data['list_status_publish']  = selectlist2(array(
				'table'=>'status_publish',
				'title'=>'All Status',
				'selected'=>$data['id_status_publish']
			));

			// print_r($data);exit;
		render('apps/event_price/add',$data,'apps');
	}

	public function view($id=''){
		if($id){
			$data = $this->eventprice_model->findById($id);

			$data['img_thumb'] 	= image($data['img'],'small');
			$data['img_ori'] 	= image($data['img'],'large');
			if(!$data){
				die('404');
			}
			$data['page_name'] 	= quote_form($data['page_name']);
			$data['teaser'] 	= quote_form($data['teaser']);
		}
		render('apps/event_price/view',$data,'apps');
	}

	function records(){
		
		$data = $this->eventprice_model->records();
		render('apps/event_price/records',$data,'blank');
	}

	function proses($idedit=''){
		$this->layout         = 'none';
		$post                 = purify($this->input->post());
		$ret['error']         = 1;

		$this->form_validation->set_rules('name', '"Category Name"', 'required');
		if ($this->form_validation->run() == FALSE){
			$ret['message']  = validation_errors(' ',' ');
		}else{
			$this->db->trans_start();

			if($idedit){
				auth_update();
				$ret['message'] = 'Update Success';
				$act			= "Update event_price";
				$this->eventprice_model->update($post,$idedit);
			}else{
				auth_insert();
				$ret['message'] = 'Insert Success';
				$act			= "Insert event_price";
				$this->eventprice_model->insert($post);
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
		$data 	= $this->eventprice_model->delete($id);
		detail_log();
		insert_log("Delete event_price");
	}

	function select_page(){
		render('apps/event_price/select_page',$data,'blank');
	}

	function record_select_page(){
		$data = $this->eventprice_model->records();
		foreach ($data['data'] as $key => $value) {
			$data['data'][$key]['page_name'] = quote_form($value['page_name']);
		}

		render('apps/event_price/record_select_page',$data,'blank');
	}

}

/* End of file event_price.php */
/* Location: ./application/controllers/apps/event_price.php */