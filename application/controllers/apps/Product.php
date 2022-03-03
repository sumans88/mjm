<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Product extends CI_Controller {
	function __construct(){
		parent::__construct();
		$this->load->model('newsModel');
		$this->load->model('productModel');
		$this->load->model('newsVersionModel');
		$this->load->model('newsCategoryModel');
		$this->load->model('newsTagsModel');
		$this->load->model('newsTagsVersionModel');
		$this->load->model('tagsModel');
		$this->load->model('newsApprovalCommentModel');
		$this->load->model('authgroup_model','authGroupModel');
		$this->load->model('model_user','userModel');
	}
	function index(){
		$data['list_status_publish'] = selectlist2(array('table'=>'status_publish','title'=>'All Status','selected'=>$data['id_status_publish']));
		render('apps/product/index',$data,'apps');
	}
	public function add($id=''){
		if($id){
			$data = $this->productModel->findById($id);
			if(!$data){
				die('404');
			}
			$data['judul']	= 'Edit';
			$data['proses']	= 'Update';
			$data = quote_form($data);
			$data['publish_date'] = iso_date($data['publish_date']);
			$data_desc = $this->productModel->get_desc($id);
			foreach($data_desc as $n =>  $data_child){
				++$i;
				$data_desc[$n]['nomor'] 	= ++$nomor;
			}
			$data['data_desc'] = $data_desc;
		}else{
			$data['judul']			= 'Add';
			$data['proses']			= 'Simpan';
			$data['news_title']		= '';
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

		render('apps/product/add',$data,'apps');
	}
	public function view($id=''){
		if($id){
			$data = $this->productModel->findById($id);
			$data['img_thumb'] = image($data['img'],'small');
			$data['img_ori'] = image($data['img'],'large');
			$data['img_news_class'] = '';
			$data['img_news_class_button'] = 'hide';
			if($data['link_youtube_video']){
				$data['link_youtube_video'] = str_replace("watch?v=","embed/",$data['link_youtube_video']);
				$data['img_news_class'] = 'img-news-with-video';
				$data['img_news_class_button'] = 'img-news-class-button';
			}
			if(!$data){
				die('404');
			}
			// $data['news_title']  = quote_form($data['news_title']);
			$data['teaser']		 = quote_form($data['teaser']);
			$data['create_date'] = iso_date_time($data['create_date']);
			$data_desc = $this->productModel->get_desc($id);
			foreach($data_desc as $n =>  $data_child){
				++$i;
				$data_desc[$n]['in'] = '';
				if($i==1){
					$data_desc[$n]['in'] = 'in';
				}
				$data_desc[$n]['nomor'] 	= ++$nomor;
			}
			$data['data_desc'] = $data_desc;
		}



        $data['share_widget']    = '';//share_widget();
		$data['top_content']     = top_content();
        $data['ads']             = ads_widget();
        $data['box_widget']      = '';//box_widget();
        $data['create_date']     = iso_date_custom_format($data['publish_date'],'d').' '.get_month(iso_date_custom_format($data['publish_date'],'F')).' '.iso_date_custom_format($data['publish_date'],'Y');
        $data['artikel_terkait'] = '';//artikel_terkait($data['id'],$id_tags);
        
		render('apps/product/view2',$data,'futuready/main');
	}
	function records(){
		$data = $this->productModel->records();
		foreach ($data['data'] as $key => $value) {
			$approval_level_news = $value['approval_level'];
			$group = $this->authGroupModel->fetchRow(array('approval_level'=>$approval_level_news));
			// $approval = $approval_level_news == 0 ? 'Draft' : ('Sent to '.$group['grup']);
			

			$data['data'][$key]['news_title'] 		= quote_form($value['news_title']);
			$data['data'][$key]['publish_date'] 	= iso_date($value['publish_date']);
			$data['data'][$key]['approval_level'] 	= $approval;
			$data['data'][$key]['edit'] 		 	= is_edit_news($value['id'],$value['user_id_create'],$approval_level_news,'delete');
			$data['data'][$key]['delete'] 		 	= is_delete_news($value['id'],$value['user_id_create'],$approval_level_news);
		}
		render('apps/product/records',$data,'blank');
	}
	
	
	function proses($idedit=''){
		$id_user =  id_user();
		$this->layout 			= 'none';
		$post 					= purify($this->input->post());
		$ret['error']			= 1;
		$where['a.uri_path']		= $post['uri_path'];
		$where['a.id !=']		= ($idedit) ? $idedit : '';
		$unik 				= $this->newsModel->findBy($where);
		$ProductModel_unik 		= $this->productModel->findBy($where);
		$this->form_validation->set_rules('news_title', '"Title"', 'required'); 
		$this->form_validation->set_rules('uri_path', '"Page URL"', 'required'); 
		// $this->form_validation->set_rules('id_status_publish', '"Status"', 'required'); 
		if ($this->form_validation->run() == FALSE){
			$ret['message']  = validation_errors(' ',' ');
		}
		else if($unik or $ProductModel_unik){
			$ret['message']	= "Page URL $post[uri_path] already taken";
		}
		else{   
			$this->db->trans_start();   
			$post['publish_date'] = iso_date($post['publish_date']);
			
			$title_data = $post['title'];
			$description_data = $post['description_data'];
			unset($post['title'],$post['description_data']);
			if($idedit){
				auth_update();
				$ret['message'] = 'Update Success';
				$act			= "Update News";
				if(!$post['img']){
					unset($post['img']);
				}
				$this->productModel->update($post,$idedit);
			}
			else{
				auth_insert();
				$ret['message'] = 'Insert Success';
				$act			= "Insert News";
				$idedit = $this->productModel->insert($post);
			}
			if($title_data and $description_data){
				$this->productModel->delete_all_desc($idedit);
				foreach($title_data as $idx => $val){
					if($title_data[$idx]){
						$sort = $idx;
						$this->productModel->insert_desc($title_data[$idx],$description_data[$idx],$idedit,++$sort);
					}
				}
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
		$this->productModel->delete($id);
		$this->db->trans_complete();
	}
	
	function tagsurl()
	{
		$this->load->model('tagsModel');
		$data = $this->tagsModel->findBy();
		foreach ($data as $key => $value) {
			echo $value['name'].' || '.$value['uri_path'].'<br>';
			$this->tagsModel->update(array('uri_path'=>url_title(strtolower($value['name']))),$value['id']);
		}
	}

}

/* End of file news.php */
/* Location: ./application/controllers/apps/product.php */