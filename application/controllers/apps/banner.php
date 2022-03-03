<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Banner extends CI_Controller {
	function __construct(){
		parent::__construct();
		$this->load->model('Banner_Model');
		$this->load->model('frontendmenumodel');
		$this->load->model('languageModel');
	}
	function index(){
		$data['list_status_publish'] = selectlist2(array('table'=>'status_publish','title'=>'All Status','selected'=>$data['id_status_publish']));
		$data['list_banner_position'] = selectlist2(array('table'=>'banner_position','title'=>'All Position'));
		render('apps/banner/index',$data,'apps');
	}
	
	public function add($id=''){
		$id_lang_default = $this->languageModel->langId();
		if($id){
			// $data = $this->Banner_Model->findById($id);
			$datas 	= $this->Banner_Model->selectData($id);
            if(!$datas){
				die('404');
			}
			$data 				= quote_form($datas);
			$data['judul']		= 'Edit';
			$data['proses']		= 'Update';
			// $data['id'] 				= $id;
		}
		else{
			$data['judul']				= 'Add';
			$data['proses']				= 'Save';
			$data['banner_title']	= '';
			// $data['description2']		= '';
			$data['background']			= '';
			$data['color']				= '';
			$data['description']		= '';
			$data['publish_date']		= date('d-m-Y');
			$data['id'] 				= '';
            $data['url'] 				= '';
            $data['iframe'] 		= '';

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
			
			$data['list_lang'][$key]['banner_title'] 		= htmlentities($datas[$key]['banner_title']);
			$data['list_lang'][$key]['url'] 					= $datas[$key]['url'];
			$data['list_lang'][$key]['publish_date'] 			= iso_date($datas[$key]['publish_date']);
			$data['list_lang'][$key]['description'] 			= $datas[$key]['description'];
			$data['list_lang'][$key]['iframe'] 			= $datas[$key]['iframe'];
			// $data['list_lang'][$key]['description2'] 			= $datas[$key]['description2'];
			$data['list_lang'][$key]['id_status_publish'] 		= $datas[$key]['id_status_publish'];
			$data['list_lang'][$key]['id_lang'] 				= $datas[$key]['id_lang'];
			$data['list_lang'][$key]['id'] 						= $datas[$key]['id'];

			$data['list_lang'][$key]['list_status_publish'] 	= selectlist2(array('table'=>'status_publish','title'=>'Select Status','selected'=>$datas[$key]['id_status_publish']));
			$data['list_lang'][$key]['list_banner_position'] = selectlist2(array('table'=>'banner_position','title'=>'All Position','selected'=>$datas[$key]['id_banner_position']));
			
			$img_thumb											= image($datas[$key]['img'],'small');
			$imagemanager										= imagemanager('img',$img_thumb,750,320,$key,$datas[$key]['name'],'banner_title'.$key);
			$data['list_lang'][$key]['img']						= $imagemanager['browse'];
			$data['list_lang'][$key]['imagemanager_config']		= $imagemanager['config'];
			$data['list_lang'][$key]['menu_cat']				= $datas[$key]['menu'];

		}

		$this->db->where_in('id_module',array(2,3,4)); //news, event // home
		$list_cat = $this->frontendmenumodel->findBy(array('id_language'=>$id_lang_default,'id_parent'=>0));
		foreach ($list_cat as $key => $value) {
			$sub_cat = $this->frontendmenumodel->findBy(array('id_parent'=>$value['id']));
			foreach ($sub_cat as $k => $v) {
				$sub_cat[$k]['sub_name'] = $v['name'];
				$sub_cat[$k]['id_sub_menu'] = $v['id'];
			}
			$list_cat[$key]['id_menu'] = $value['id'];
			$list_cat[$key]['list_sub_cat'] = $sub_cat;
		}
		$data['list_cat'] = $list_cat;
		$data['list_lang2']		= $data['list_lang'];
		foreach($data['list_lang2'] as $key => $value){
			$data['list_lang2'][$key]['lang_name'] = ucwords($value['lang_name']);
		}
		render('apps/banner/add',$data,'apps');
	}
	public function view($id=''){
		if($id){
			$datas 	= $this->Banner_Model->selectData($id);
			
			if(!$datas){
				die('404');
			}

			$data['list_lang']	= $this->languageModel->langName();

			foreach($data['list_lang'] as $key => $value){
				
				$data['list_lang'][$key]['banner_title'] 		= $datas[$key]['banner_title'];
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
		render('apps/banner/view',$data,'blank');
	}
	function records(){
		$data = $this->Banner_Model->records();
		foreach ($data['data'] as $key => $value) {
			$data['data'][$key]['banner_title'] 	= quote_form($value['banner_title']);
			$data['data'][$key]['publish_date'] 	= iso_date($value['publish_date']);
			$data['data'][$key]['approval_level'] 	= $approval;
		}
		render('apps/banner/records',$data,'blank');
	}
	
	
	function proses($idedit=''){
		$id_user 				=  id_user();
		$this->layout 			= 'none';
		$post 					= $this->input->post();
		$ret['error']			= 1;
		$id_parent_lang 		= NULL;
		$this->db->trans_start();

		foreach ($post['banner_title'] as $key => $value){
			$this->form_validation->set_rules('id_status_publish', '"Status Publish"', 'required'); 
			if ($this->form_validation->run() == FALSE){
				$ret['message']  = validation_errors(' ',' ');
			}else{
				$idedit 			= $post['id'][$key];
				if($key==0){
					if($idedit){
						$data_save['is_background']	= ($post['is_background'][$key] != 1) ? 0 : 1;
						$data_save['is_box']		= ($post['position'][$key] != 1) ? 0 : 1;
					}
				 	$is_background 		= ($post['is_background'][$key] != 1) ? 0 : 1;
				 	$position			= $post['position'][$key];
				 	$publish_date		= iso_date($post['publish_date'][$key]);
				 	$id_status_publish	= $post['id_status_publish'][$key];
				 	$id_banner_position	= $post['id_banner_position'][$key];
				 	$menu  				= ','.implode(',', $post['menu']).',';
				}
				else{
					$menu = '';
					foreach($post['menu'] as $mn){
						$menu 	.= ','.db_get_one('frontend_menu','id',array('id_parent_lang'=>$mn,'id_language'=>$post['id_lang'][$key])); 
					}
					$menu .= ',';
				}

				$data_save['banner_title'] 		= $post['banner_title'][$key];
				$data_save['url'] 					= $post['url'][$key];
				$data_save['publish_date']			= $publish_date;
				$data_save['description'] 			= $post['description'][$key];
				$data_save['iframe'] 			= $post['iframe'][$key];
				// $data_save['description2'] 			= $post['description2'][$key];
				$data_save['is_background'] 		= $is_background;
				$data_save['is_box'] 				= $position;
				$data_save['id_status_publish'] 	= $id_status_publish;
				$data_save['id_banner_position']	= $id_banner_position;
				$data_save['id_lang'] 				= $post['id_lang'][$key];
				$data_save['id_parent_lang']		= $id_parent_lang;
				$data_save['id'] 					= $post['id'][$key];
				$data_save['menu'] 					= $menu;


				if($post['imgDelete'][$key] != 1){
					if($idedit && !empty($post['img'][$key])){
						$data_save['img']	= $post['img'][$key];
					}elseif($idedit){
						// $datas 				= $this->Banner_Model->selectData($idedit,1);
						unset($data_save['img']);
						// unset($data_save['img'])	= $datas['img'];
					}else{
						$data_save['img']	= $post['img'][$key];
					}
				} else{
					$data_save['img'] = NULL;
				}

				// if($post['imgDelete'][$key] != 0){
				// 	if($idedit && $post['img'][$key]){
				// 		$data_save['img']	= $post['img'][$key];
				// 	}elseif($idedit){
				// 		$datas 				= $this->Banner_Model->selectData($idedit,1);
				// 		$data_save['img']	= $datas['img'];
				// 	}else{
				// 		$data_save['img']	= $post['img'][$key];
				// 	}
				// } else{
				// 	$data_save['img'] = NULL;
				// }
				if($idedit){
					auth_update();
					$ret['message'] = 'Update Success';
					$act			= "Update News";
					$iddata 		= $this->Banner_Model->update($data_save,$idedit);
				}else{
					auth_insert();
					$ret['message'] = 'Insert Success';
					$act			= "Insert News";
					$iddata 		= $this->Banner_Model->insert($data_save);
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
		$this->Banner_Model->delete($id);
		$this->Banner_Model->delete2($id);
		$this->db->trans_complete();
	}
	
}

/* End of file banner.php */
/* Location: ./application/controllers/apps/banner.php */