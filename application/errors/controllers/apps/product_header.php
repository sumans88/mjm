<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Product_Header extends CI_Controller {
	function __construct(){
		parent::__construct();
		$this->load->model('newsmodel');
		$this->load->model('productheaderModel');
		$this->load->model('newsversionmodel');
		$this->load->model('newscategorymodel');
		$this->load->model('newstagsmodel');
		$this->load->model('newstagsversionmodel');
		$this->load->model('tagsmodel');
		$this->load->model('newsapprovalcommentmodel');
		$this->load->model('authgroup_model','authGroupModel');
		$this->load->model('model_user','userModel');
	}
	function index(){
		$data['list_status_publish'] = selectlist2(array('table'=>'status_publish','title'=>'All Status','selected'=>$data['id_status_publish']));
		render('apps/product_header/index',$data,'apps');
	}
	public function add($id=''){
		if($id){
			$data = $this->productheaderModel->findById($id);
			if(!$data){
				die('404');
			}
			$data['judul']	= 'Edit';
			$data['proses']	= 'Update';
			$data = quote_form($data);
			$data['publish_date'] = iso_date($data['publish_date']);

		}
		else{
			$data['judul']			= 'Add';
			$data['proses']			= 'Simpan';
			$data['title']		= '';
			$data['link_youtube_video']		= '';
			$data['uri_path']		= '';
			$data['teaser']			= '';
			$data['page_content']	= '';
			$data['publish_date']	= date('d-m-Y');
			$data['tags'] 			= '';
			$data['id'] 			= '';
			$data['seo_title']			= '';
			$data['meta_description']	= '';
			$data['meta_keywords']		= '';
			$data['data_desc'] =  array();
		}
        
		$img_thumb					= image($data['img'],'small');
		$imagemanager				= imagemanager('img',$img_thumb);
		$data['img']				= $imagemanager['browse'];
		$data['imagemanager_config']= $imagemanager['config'];

		$data['list_status_publish'] = selectlist2(array('table'=>'status_publish','title'=>'Select Status','selected'=>$data['id_status_publish']));

		render('apps/product_header/add',$data,'apps');
	}
	
	function records(){
		$data = $this->productheaderModel->records();
		foreach ($data['data'] as $key => $value) {
			$approval_level_news = $value['approval_level'];
			$group = $this->authGroupModel->fetchRow(array('approval_level'=>$approval_level_news));
			// $approval = $approval_level_news == 0 ? 'Draft' : ('Sent to '.$group['grup']);
			

			$data['data'][$key]['title'] 		= quote_form($value['title']);
			$data['data'][$key]['publish_date'] 	= iso_date($value['publish_date']);
		}
		render('apps/product_header/records',$data,'blank');
	}
	
	
	function proses($idedit=''){
		$id_user =  id_user();
		$this->layout 			= 'none';
		$post 					= purify($this->input->post());
		$ret['error']			= 1;
		$where['a.uri_path']		= $post['uri_path'];
		$where['a.id !=']		= ($idedit) ? $idedit : '';
		$this->form_validation->set_rules('title', '"Title"', 'required'); 
		// $this->form_validation->set_rules('id_status_publish', '"Status"', 'required'); 
		if ($this->form_validation->run() == FALSE){
			$ret['message']  = validation_errors(' ',' ');
		}
		
		else{   
			$this->db->trans_start();   
			$post['publish_date'] = iso_date($post['publish_date']);
			
			if($idedit){
				auth_update();
				$ret['message'] = 'Update Success';
				$act			= "Update News";
				if(!$post['img']){
					unset($post['img']);
				}
				$this->productheaderModel->update($post,$idedit);
			}
			else{
				auth_insert();
				$ret['message'] = 'Insert Success';
				$act			= "Insert News";
				$idedit = $this->productheaderModel->insert($post);
			}
			
			$this->db->trans_complete();
			set_flash_session('message',$ret['message']);
			$ret['error'] = 0;
		}
		echo json_encode($ret);
	}
	function del(){
		$this->db->trans_start();   
		$id = $this->input->post('iddel');
		$this->productheaderModel->delete($id);
		$this->db->trans_complete();
	}
	
	

}

/* End of file news.php */
/* Location: ./application/controllers/apps/product.php */