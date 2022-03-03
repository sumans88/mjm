<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class About_partners extends CI_Controller {
	function __construct(){
		parent::__construct();
		$this->load->model('AboutPartnersModel');
		$this->load->model('languageModel');
	}
	function index(){
		$lang = default_lang_id();
		$data['list_partners_category'] = selectlist2(array('table'=>'partners_category','title'=>'Select Category','where'=>'is_delete = 0 and id_lang = '.$lang));
		$data['list_status_publish'] = selectlist2(array('table'=>'status_publish','title'=>'All Status','selected'=>$data['id_status_publish']));
		render('apps/about_partners/index',$data,'apps');
	}
	public function add($id=''){
		$lang = default_lang_id();
		if($id){
			// $data = $this->AboutPartnersModel->findById($id);
			$datas = $this->AboutPartnersModel->selectData($id);
            if(!$datas){
				die('404');
			}
			$data = quote_form($data);
			$data['is_hide_checked'] = ($data['is_hide'] == 1) ? 'checked':'';
			$data['judul']           = 'Edit';
			$data['id']           = $id;
			$data['proses']          = 'Update';
		}else{
			$data['judul']           = 'Add';
			$data['proses']          = 'Save';
			$data['page_content']    = '';
			$data['id']              = '';
			$data['title']           = '';
			$data['url']             = '';
			$data['hide']            = '';
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
			$data['list_lang'][$key]['description']		= $datas[$key]['description'];
			$data['list_lang'][$key]['list_partners_category'] 	= selectlist2(array('table'=>'partners_category','title'=>'Select Category','selected'=>$datas[$key]['id_partners_category'],'where'=>'is_delete = 0 '));
			$data['list_lang'][$key]['list_status_publish'] = selectlist2(array('table'=>'status_publish','title'=>'All Status','selected'=>$datas[$key]['id_status_publish']));
			$img_thumb											= image($datas[$key]['img'],'small');
			$imagemanager										= imagemanager('img',$img_thumb,180,100,$key);
			$data['list_lang'][$key]['img']						= $imagemanager['browse'];
			$data['list_lang'][$key]['imagemanager_config']		= $imagemanager['config'];
		}
		$data['list_lang2'] 	= $data['list_lang'];

		render('apps/about_partners/add',$data,'apps');
	}
	function records(){
		$data = $this->AboutPartnersModel->records();
		foreach ($data['data'] as $key => $value) {
			$data['data'][$key]['create_date'] 	= iso_date($value['create_date']);
		}
		render('apps/about_partners/records',$data,'blank');
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
				$this->form_validation->set_rules('description', '"Description"', 'required');
			}

			$idedit = $post['id'][0];
			if($idedit){
				$where['id !=']	= $idedit;
			}

			if($key==0){
				$id_event_category 	= $post['id_partners_category'][$key];
			}
			else{
				$id_event_category 	= db_get_one('partners_category','id',array('id_parent_lang'=>$post['id_partners_category'][0],'id_lang'=>$post['id_lang'][$key]));
			}

			$data_save['title']                = $post['title'][$key];
			$data_save['url']                  = $post['url'][0];
			$data_save['description']          = htmlspecialchars_decode(urldecode($post['description'.$key.'']));
			$data_save['id_lang']              = $post['id_lang'][$key];
			$data_save['id_parent_lang']       = $id_parent_lang;
			$data_save['id_partners_category'] = $id_event_category;
			$data_save['id_status_publish']	   = $post['id_status_publish'][0];
			$data_save['id']                   = $post['id'][$key];
			$post_image                        = $post['img'][0];

			if($post['imgDelete'][$key] != 1){
				if($idedit && !empty($post['img'][$key])){
					$data_save['img']	= $post['img'][$key];
				}elseif($idedit){
						// $datas 				= $this->slideshowModel->selectData($idedit,1);
					unset($data_save['img']);
						// unset($data_save['img'])	= $datas['img'];
				}else{
					$data_save['img']	= $post['img'][$key];
				}
			} else{
				$data_save['img'] = NULL;
			}

			if($idedit){
				if($key==0){
                    auth_update();
					$ret['message']		= 'Update Success';
					$act				= "Update Amcham Partner";
					$iddata 			= $this->AboutPartnersModel->update($data_save,$idedit);
				}else{
					auth_update();
					$ret['message'] 	= 'Update Success';
					$act				= "Update Amcham Partner";
					$iddata 			= $this->AboutPartnersModel->updateKedua($data_save,$idedit);
				}					
			}else{
				auth_insert();
				$ret['message'] 	= 'Insert Success';
				$act				= "Insert Amcham Partner";
				$iddata 			= $this->AboutPartnersModel->insert($data_save);
			}

			if($key==0){
				$id_parent_lang = $iddata;
			}

			insert_log($act);
			$this->db->trans_complete();
			set_flash_session('message',$ret['message']);
			$ret['error'] = 0;
		}
		echo json_encode($ret);
	}
	function del(){
		$this->db->trans_start();   
		$id = $this->input->post('iddel');
		$this->AboutPartnersModel->delete($id);
		$this->AboutPartnersModel->delete2($id);
		$this->db->trans_complete();
	}
	function record_select_page(){
		$data = $this->AboutPartnersModel->records();
		foreach ($data['data'] as $key => $value) {
			$data['data'][$key]['page_name'] = quote_form($value['page_name']);
		}
		render('apps/about_partners/record_select_page',$data,'blank');
	}
	
}

/* End of file slideshow.php */
/* Location: ./application/controllers/apps/slideshow.php */