<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class News_category extends CI_Controller {
	function __construct(){
		parent::__construct();
		$this->load->model('NewsCategoryModel');
		$this->load->model('languageModel');
	}
	function index(){
		render('apps/news_category/index',$data,'apps');
	}
	public function add($id=''){
		if($id){
			// $data = $this->NewsCategoryModel->findById($id);
			$datas = $this->NewsCategoryModel->selectData($id);
            if(!$datas){
				die('404');
			}
			$data = quote_form($data);
			$data['is_hide_checked'] 	= ($data['is_hide'] == 1) ? 'checked':'';
			$data['judul']				= 'Edit';
			$data['proses']				= 'Update';
		}else{
			$data['judul']				= 'Add';
			$data['proses']				= 'Simpan';
			$data['page_content']		= '';
            $data['id'] 				= '';
            $data['name']				= '';
            $data['uri_path']			= '';
            $data['hide']			= '';
		}

		$data['list_lang']	= $this->languageModel->langName();
		foreach ($data['list_lang'] as $key => $value){
			$data['list_lang'][$key]['invis'] 		= ($key==0) ? '' : 'hide';
			$data['list_lang'][$key]['active'] 		= ($key==0) ? 'active in' : '';
			$data['list_lang'][$key]['validation'] 	= ($key==0) ? 'true' : 'false';
			$data['list_lang'][$key]['nomor'] 		= $key;

			$data['list_lang'][$key]['id']				= $datas[$key]['id'];
			$data['list_lang'][$key]['name']			= $datas[$key]['name'];
			$data['list_lang'][$key]['uri_path']		= $datas[$key]['uri_path'];
			$data['list_lang'][$key]['is_hide']			= $datas[$key]['is_hide'];
			$data['list_lang'][$key]['hide_checked'] 	= ($datas[$key]['is_hide'] == 1) ? 'checked' : '';
			
			$img_thumb											= image($datas[$key]['img'],'small');
			$imagemanager										= imagemanager('img',$img_thumb,750,186,$key);
			$data['list_lang'][$key]['img']						= $imagemanager['browse'];
			$data['list_lang'][$key]['imagemanager_config']		= $imagemanager['config'];
		}

		$data['list_lang2'] 	= $data['list_lang'];
		render('apps/news_category/add',$data,'apps');
	}
	function records(){
		$data = $this->NewsCategoryModel->records();
		foreach ($data['data'] as $key => $value) {
			$data['data'][$key]['create_date'] 	= iso_date($value['create_date']);
		}
		render('apps/news_category/records',$data,'blank');
	}
	function proses($idedit=''){
		$id_user 			=  id_user();
		$this->layout 		= 'none';
		$post 				= purify($this->input->post());
		$ret['error']		= 1;
		$id_parent_lang 	= NULL;
		$this->db->trans_start();
		foreach ($post['name'] as $key => $value) {
			if(!$idedit){
				$this->form_validation->set_rules('name', '"Name"', 'required');
				$this->form_validation->set_rules('uri_path', '"Uri Path"', 'required');
				$where['uri_path']		= $post['uri_path'][$key];
				$unik 					= $this->NewsCategoryModel->findBy($where);
				if ($this->form_validation->run() == FALSE){
					$ret['message']  = validation_errors(' ',' ');
				}else if($unik){
					$ret['message']	= "Page URL $post[uri_path] already taken";
				}
			}
			if($idedit){
				$where['id !=']	= $idedit;
			}
			if($key==0){
				if($idedit){
					$data_save['is_hide']	= ($post['is_hide'][$key] != 1) ? 0 : 1;
				}
				$idedit 	= $post['id'][$key];
				$is_hide	= ($post['is_hide'][$key] != 1) ? 0 : 1;
			}
			$data_save['name'] 				= $post['name'][$key];
			$data_save['uri_path'] 			= $post['uri_path'][$key];
			$data_save['is_hide']			= $is_hide;
			$data_save['id_lang'] 			= $post['id_lang'][$key];
			$data_save['id_parent_lang']	= $id_parent_lang;
			$data_save['id'] 				= $post['id'][$key];
			$post_image 					= $post['img'][$key];

			if($idedit && $post['img'][$key]){
				$data_save['img']	= $post['img'][$key];
			}elseif($idedit){
				$datas 				= $this->NewsCategoryModel->selectData($idedit);
				$data_save['img']	= $datas[$key]['img'];
			}else{
				$data_save['img']	= $post['img'][$key];
			}

			if($idedit){
				if($key==0){
                    auth_update();
					$ret['message']		= 'Update Success';
					$act				= "Update News Category";
					$iddata 			= $this->NewsCategoryModel->update($data_save,$idedit);
				}else{
					auth_update();
					$ret['message'] 	= 'Update Success';
					$act				= "Update News Category";
					$iddata 			= $this->NewsCategoryModel->updateKedua($data_save,$idedit);
				}					
			}else{
				auth_insert();
				$ret['message'] 	= 'Insert Success';
				$act				= "Insert News Category";
				$iddata 			= $this->NewsCategoryModel->insert($data_save);
			}

			if($key==0){
				$id_parent_lang = $iddata;
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
		$this->NewsCategoryModel->delete($id);
		$this->NewsCategoryModel->delete2($id);
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