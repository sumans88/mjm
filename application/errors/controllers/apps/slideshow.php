<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Slideshow extends CI_Controller {
	function __construct(){
		parent::__construct();
		$this->load->model('slideshowModel');
		$this->load->model('languageModel');
	}
	function index(){
		$data['list_status_publish'] = selectlist2(array('table'=>'status_publish','title'=>'All Status','selected'=>$data['id_status_publish']));
		render('apps/slideshow/index',$data,'apps');
	}
	
	public function add($id=''){
		if($id){
			// $data = $this->slideshowModel->findById($id);
			$datas 	= $this->slideshowModel->selectData($id);

            if(!$datas){
				die('404');
			}
			$data 				= quote_form($datas);
			$data['judul']		= 'Edit';
			$data['proses']		= 'Update';
		}
		else{
			$data['judul']				= 'Add';
			$data['proses']				= 'Simpan';
			$data['slideshow_title']	= '';
			// $data['description2']		= '';
			$data['background']			= '';
			$data['color']				= '';
			$data['description']		= '';
			$data['publish_date']		= date('d-m-Y');
			$data['id'] 				= '';
            $data['url'] 				= '';
		}

		$data['list_lang']	= $this->languageModel->langName();

		foreach($data['list_lang'] as $key => $value){
			$data['list_lang'][$key]['invis']			= ($key==0) ? '' : 'hide';
			$data['list_lang'][$key]['active']			= ($key==0) ? 'active in' : '';
			$data['list_lang'][$key]['validation']		= ($key==0) ? 'true' : 'false';
			$data['list_lang'][$key]['nomor']			= $key;

			$data['list_lang'][$key]['is_background_checked'] 	= ($datas[$key]['is_background'] == 1) ? 'checked' : '';
			$data['list_lang'][$key]['position_left_checked'] 	= ($datas[$key]['is_box'] == 1) ? 'checked' : '';
			$data['list_lang'][$key]['position_right_checked'] 	= ($datas[$key]['is_box'] == 2) ? 'checked' : '';
			
			$data['list_lang'][$key]['slideshow_title'] 		= $datas[$key]['slideshow_title'];
			$data['list_lang'][$key]['url'] 					= $datas[$key]['url'];
			$data['list_lang'][$key]['publish_date'] 			= iso_date($datas[$key]['publish_date']);
			$data['list_lang'][$key]['description'] 			= $datas[$key]['description'];
			// $data['list_lang'][$key]['description2'] 			= $datas[$key]['description2'];
			$data['list_lang'][$key]['id_status_publish'] 		= $datas[$key]['id_status_publish'];
			$data['list_lang'][$key]['id_lang'] 				= $datas[$key]['id_lang'];
			$data['list_lang'][$key]['id'] 						= $datas[$key]['id'];

			$data['list_lang'][$key]['list_status_publish'] 	= selectlist2(array('table'=>'status_publish','title'=>'Select Status','selected'=>$datas[$key]['id_status_publish']));
			
			$img_thumb											= image($datas[$key]['img'],'small');
			$imagemanager										= imagemanager('img',$img_thumb,750,186,$key);
			$data['list_lang'][$key]['img']						= $imagemanager['browse'];
			$data['list_lang'][$key]['imagemanager_config']		= $imagemanager['config'];
		}

		$data['list_lang2']		= $data['list_lang'];
		render('apps/slideshow/add',$data,'apps');
	}
	public function view($id=''){
		if($id){
			$datas 	= $this->slideshowModel->selectData($id);
			
			if(!$datas){
				die('404');
			}

			$data['list_lang']	= $this->languageModel->langName();

			foreach($data['list_lang'] as $key => $value){
				
				$data['list_lang'][$key]['slideshow_title'] 		= $datas[$key]['slideshow_title'];
				$data['list_lang'][$key]['description'] 			= $datas[$key]['description'];
				
				$data['list_lang'][$key]['img_thumb'] 				= image($datas[$key]['img'],'small');
				$data['list_lang'][$key]['img_ori'] 				= image($datas[$key]['img'],'large');
				$data['list_lang'][$key]['style_position'] 			= '';
				$data['list_lang'][$key]['style_background']		= '';

				$data['list_lang'][$key]['list_status_publish']		= selectlist2(array('table'=>'status_publish','title'=>'All Status'));
				
				if($value['list_lang'][$key]['color']){
					$color = $value['list_lang'][$key]['color'];
				}else{
					$color = "#fff";
				}
				
				if($value['list_lang'][$key]['background']){
					$background = $value['list_lang'][$key]['background'];
				}else{
					$background = "rgba(169,172,174,0.7)";
				}

				if($data['position']==2){
					$data['style_position'] = '<style>.slider-caption{left:51% !important;}.carousel-caption{color: '.$color.' !important;}</style>';
				}
				if($data['is_background']==1){
					$data['style_background'] = '<style>.slider-caption{background-color:'.$background.' !important;}.carousel-caption{color: '.$color.' !important;}</style>';
				}
				$data['list_lang'][$key]['create_date'] = iso_date_time($datas[$key]['create_date']);
			}
		}
		render('apps/slideshow/view',$data,'blank');
	}
	function records(){
		$data = $this->slideshowModel->records();
		foreach ($data['data'] as $key => $value) {
			$data['data'][$key]['slideshow_title'] 	= quote_form($value['slideshow_title']);
			$data['data'][$key]['publish_date'] 	= iso_date($value['publish_date']);
			$data['data'][$key]['approval_level'] 	= $approval;
		}
		render('apps/slideshow/records',$data,'blank');
	}
	
	
	function proses($idedit=''){
		$id_user 				=  id_user();
		$this->layout 			= 'none';
		$post 					= purify($this->input->post());
		$ret['error']			= 1;
		$id_parent_lang 		= NULL;
		$this->db->trans_start();

		foreach ($post['slideshow_title'] as $key => $value){
			$this->form_validation->set_rules('id_status_publish', '"Status Publish"', 'required'); 
			if ($this->form_validation->run() == FALSE){
				$ret['message']  = validation_errors(' ',' ');
			}else{
				if($key==0){
					if($idedit){
						$data_save['is_background']	= ($post['is_background'][$key] != 1) ? 0 : 1;
						$data_save['is_box']		= ($post['position'][$key] != 1) ? 0 : 1;
					}
					$idedit 			= $post['id'][$key];
				 	$is_background 		= ($post['is_background'][$key] != 1) ? 0 : 1;
				 	$position			= $post['position'][$key];
				 	$publish_date		= iso_date($post['publish_date'][$key]);
				 	$id_status_publish	= $post['id_status_publish'][$key];
				}

				$data_save['slideshow_title'] 		= $post['slideshow_title'][$key];
				$data_save['url'] 					= $post['url'][$key];
				$data_save['publish_date']			= $publish_date;
				$data_save['description'] 			= $post['description'][$key];
				// $data_save['description2'] 			= $post['description2'][$key];
				$data_save['is_background'] 		= $is_background;
				$data_save['is_box'] 				= $position;
				$data_save['id_status_publish'] 	= $id_status_publish;
				$data_save['id_lang'] 				= $post['id_lang'][$key];
				$data_save['id_parent_lang']		= $id_parent_lang;
				$data_save['id'] 					= $post['id'][$key];
				
				if($idedit && $post['img'][$key]){
					$data_save['img']	= $post['img'][$key];
				}elseif($idedit){
					$datas 				= $this->slideshowModel->selectData($idedit);
					$data_save['img']	= $datas[$key]['img'];
				}else{
					$data_save['img']	= $post['img'][$key];
				}

				if($idedit){
					if($key==0){
						auth_update();
						$ret['message'] = 'Update Success';
						$act			= "Update News";
						$iddata 		= $this->slideshowModel->update($data_save,$idedit);
					}else{
						auth_update();
						$ret['message'] = 'Update Success';
						$act			= "Update News";
						$iddata 		= $this->slideshowModel->updateKedua($data_save,$idedit);
					}
				}else{
					auth_insert();
					$ret['message'] = 'Insert Success';
					$act			= "Insert News";
					$iddata 		= $this->slideshowModel->insert($data_save);
				}

				if($key==0){
					$id_parent_lang = $iddata;
				}

				$this->db->trans_complete();
				set_flash_session('message',$ret['message']);
				$ret['error'] = 0;
			}
		}
		echo json_encode($ret);
	}
	function del(){
		$this->db->trans_start();   
		$id = $this->input->post('iddel');
		$this->slideshowModel->delete($id);
		$this->slideshowModel->delete2($id);
		$this->db->trans_complete();
	}
	
}

/* End of file slideshow.php */
/* Location: ./application/controllers/apps/slideshow.php */