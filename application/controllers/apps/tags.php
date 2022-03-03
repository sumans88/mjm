<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Tags extends CI_Controller {
	function __construct(){
		parent::__construct();
		$this->load->model('tagsModel');
		$this->load->model('languageModel');
	}
	function index(){
		render('apps/tags/index',$data,'apps');
	}
	public function add($id=''){
		if($id){
			$datas = $this->tagsModel->selectData($id);
            if(!$datas){
				die('404');
			}
			$data = quote_form($data);
			$data['is_hide_checked'] 	= ($data['is_hide'] == 1) ? 'checked':'';
			$data['judul']				= 'Edit';
			$data['proses']				= 'Update';
			$data['id']					= $id;
		}else{
			$data['judul']				= 'Add';
			$data['proses']				= 'Simpan';
            $data['id'] 				= '';
            $data['name']				= '';
            $data['uri_path']			= '';
		}

		$data['det_tags'] = $datas;
		render('apps/tags/add',$data,'apps');
	}
	function records(){
		$data = $this->tagsModel->records();
		foreach ($data['data'] as $key => $value) {
			$data['data'][$key]['create_date'] 	= iso_date($value['create_date']);
		}
		render('apps/tags/records',$data,'blank');
	}
	function proses($idedit=''){
		$id_user 			=  id_user();
		$this->layout 		= 'none';
		$post 				= purify($this->input->post());
		$ret['error']		= 1;
		$id_parent_lang 	= NULL;
		$this->db->trans_start();

		if(!$idedit){
			$this->form_validation->set_rules('name', '"Name"', 'required');
			$this->form_validation->set_rules('uri_path', '"Uri Path"', 'required');
			$where['uri_path']		= $post['uri_path'];
			$unik 					= $this->tagsModel->findBy($where);
			if ($this->form_validation->run() == FALSE){
				$ret['message']  = validation_errors(' ',' ');
			}else if($unik){
				$ret['message']	= "Page URL $post[uri_path] already taken";
			}
		}

		$data_save['name'] 				= $post['name'];
		$data_save['uri_path'] 			= $post['uri_path'];
		$data_save['id'] 				= $post['id'];

		if($idedit){
            auth_update();
			$ret['message']		= 'Update Success';
			$act				= "Update Tags";
			$iddata 			= $this->tagsModel->update($data_save,$idedit);
		}else{
			auth_insert();
			$ret['message'] 	= 'Insert Success';
			$act				= "Insert Tags";
			$iddata 			= $this->tagsModel->insert($data_save);
		}

		$this->db->trans_complete();
		set_flash_session('message',$ret['message']);
		$ret['error'] = 0;

		echo json_encode($ret);
	}
	function del(){
		$this->db->trans_start();   
		$id = $this->input->post('iddel');
		$this->tagsModel->delete($id);
		$this->db->trans_complete();
	}
	function record_select_page(){
		$data = $this->NewsCategoryModel->records();
		foreach ($data['data'] as $key => $value) {
			$data['data'][$key]['page_name'] = quote_form($value['page_name']);
		}
		render('apps/news_category/record_select_page',$data,'blank');
	}
	
}

/* End of file slideshow.php */
/* Location: ./application/controllers/apps/slideshow.php */