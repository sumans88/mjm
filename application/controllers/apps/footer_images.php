<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Footer_images extends CI_Controller {
	function __construct(){
		parent::__construct();
		$this->load->model('FooterImagesModel');
		$this->load->model('languageModel');
	}
	function index(){
		render('apps/footer_images/index',$data,'apps');
	}
	public function add($id=''){
		if($id){
			// $data = $this->FooterImagesModel->findById($id);
			$datas = $this->FooterImagesModel->selectData($id);
            if(!$datas){
				die('404');
			}
			$data = quote_form($data);
			$data['is_hide_checked'] 	= ($data['is_hide'] == 1) ? 'checked':'';
			$data['judul']				= 'Edit';
			$data['proses']				= 'Update';
		}else{
			$data['judul']				= 'Add';
			$data['proses']				= 'Save';
			$data['page_content']		= '';
            $data['id'] 				= '';
            $data['title']				= '';
            $data['url']			= '';
		}

		$data['list_lang']	= $this->languageModel->langName();
		foreach ($data['list_lang'] as $key => $value){
			$data['list_lang'][$key]['invis'] 		= ($key==0) ? '' : 'hide';
			$data['list_lang'][$key]['active'] 		= ($key==0) ? 'active in' : '';
			$data['list_lang'][$key]['validation'] 	= ($key==0) ? 'true' : 'false';
			$data['list_lang'][$key]['nomor'] 		= $key;

			$data['list_lang'][$key]['id']				= $datas[$key]['id'];
			$data['list_lang'][$key]['title']			= $datas[$key]['title'];
			$data['list_lang'][$key]['url']				= $datas[$key]['url'];
			
			$img_thumb											= image($datas[$key]['img'],'small');
			$imagemanager										= imagemanager('img',$img_thumb,320,180,$key,$datas[$key]['title'],'title'.$key);
			$data['list_lang'][$key]['img']						= $imagemanager['browse'];
			$data['list_lang'][$key]['imagemanager_config']		= $imagemanager['config'];
		}

		$data['list_lang2'] 	= $data['list_lang'];
		render('apps/footer_images/add',$data,'apps');
	}
	function records(){
		$data = $this->FooterImagesModel->records();
		foreach ($data['data'] as $key => $value) {
			$data['data'][$key]['create_date'] 	= iso_date($value['create_date']);
		}
		render('apps/footer_images/records',$data,'blank');
	}
	function proses($idedit=''){
		$id_user 			=  id_user();
		$this->layout 		= 'none';
		$post 				= purify($this->input->post());
		$ret['error']		= 1;
		$id_parent_lang 	= NULL;
		$this->db->trans_start();
		foreach ($post['title'] as $key => $value) {
			if(!$idedit){
				$this->form_validation->set_rules('title', '"Title"', 'required');
				$this->form_validation->set_rules('url', '"URL"', 'required');
			}
			if($idedit){
				$where['id !=']	= $idedit;
			}
			if($key==0){
				$idedit 	= $post['id'][$key];
			}

			$data_save['title'] 			= $post['title'][$key];
			$data_save['url'] 				= $post['url'][0];
			$data_save['id_lang'] 			= $post['id_lang'][$key];
			$data_save['id_parent_lang']	= $id_parent_lang;
			$data_save['id'] 				= $post['id'][$key];
			$post_image 					= $post['img'][0];

			if($idedit && $post['img'][0]){
				$data_save['img']	= $post['img'][0];
			}elseif($idedit){
				$datas 				= $this->FooterImagesModel->selectData($idedit);
				$data_save['img']	= $datas[0]['img'];
			}else{
				$data_save['img']	= $post['img'][0];
			}

			if($idedit){
				if($key==0){
                    auth_update();
					$ret['message']		= 'Update Success';
					$act				= "Update Footer Images";
					$iddata 			= $this->FooterImagesModel->update($data_save,$idedit);
				}else{
					auth_update();
					$ret['message'] 	= 'Update Success';
					$act				= "Update Footer Images";
					$iddata 			= $this->FooterImagesModel->updateKedua($data_save,$idedit);
				}					
			}else{
				auth_insert();
				$ret['message'] 	= 'Insert Success';
				$act				= "Insert Footer Images";
				$iddata 			= $this->FooterImagesModel->insert($data_save);
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
		$this->FooterImagesModel->delete($id);
		$this->FooterImagesModel->delete2($id);
		$this->db->trans_complete();
	}
	
}

/* End of file slideshow.php */
/* Location: ./application/controllers/apps/slideshow.php */