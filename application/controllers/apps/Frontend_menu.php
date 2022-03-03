<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Frontend_menu extends CI_Controller{
	
	function __construct(){
		parent::__construct();
		$this->load->model('frontendMenuModel');
		$this->load->model('languagemodel');
	}

	function index(){
		$data['list_menu_position'] 		= selectlist2(array('table' => 'menu_position', 'title' => 'All Position'));
		$data['list_parent'] 				= selectlist2(array('table' => 'frontend_menu', 'title' => 'Root','where'=>'id_status_publish = 2 and is_delete = 0 and id_parent_lang is null and (id_parent = 0 or id_parent is null)'));
		// id_parent_lang is null buat bahasa indonesia
		render('apps/frontend_menu/index', $data, 'apps');
	}

	public function add($id=''){
		if($id){
			$datas 	= $this->frontendMenuModel->selectData($id);
			// $datas 	= $this->frontendMenuModel->findById($id);
			
			if(!$datas){
				die('404');
			}

			$data 				= quote_form($datas);
			$data['judul']		= 'Edit';
			$data['proses']		= 'Update';
		}else{
			$data['judul']				= 'Add';
			$data['proses']				= 'Save';
			$data['uri_path']			= '';
			$data['teaser']				= '';
			$data['page_content']		= '';
			$data['extra_param']		= '';
			$data['id'] 				= '';
			$data['name'] 				= '';
			$data['seo_title']			= '';
			$data['meta_description']	= '';
			$data['meta_keywords']		= '';
			$data['description']		= '';
			$data['publish_date'] 		= date('d-m-Y');
		}

		$data['list_lang']	= $this->languagemodel->langName();

		foreach ($data['list_lang'] as $key => $value){
			$data['list_lang'][$key]['invis'] 					= ($key==0) ? '' : 'hide';
			$data['list_lang'][$key]['active'] 					= ($key==0) ? 'active in' : '';
			$data['list_lang'][$key]['validation'] 				= ($key==0) ? 'true' : 'false';
			$data['list_lang'][$key]['nomor'] 					= $key;

			$data['list_lang'][$key]['id']						= $datas[$key]['id'];
			$data['list_lang'][$key]['name']					= $datas[$key]['name'];
			$data['list_lang'][$key]['extra_param']				= $datas[$key]['extra_param'];
			$data['list_lang'][$key]['publish_date']			= $datas[$key]['publish_date'];
			$data['list_lang'][$key]['description']				= $datas[$key]['description'];
			$data['list_lang'][$key]['seo_title']				= $datas[$key]['seo_title'];	
			$data['list_lang'][$key]['meta_description']		= $datas[$key]['meta_description'];
			$data['list_lang'][$key]['meta_keywords']			= $datas[$key]['meta_keywords'];
			$data['list_lang'][$key]['id_frontend_menu_type']	= $datas[$key]['id_frontend_menu_type'];
			$data['list_lang'][$key]['list_menu_position']		= selectlist2(array('table' => 'menu_position', 'selected' => $datas[$key]['id_menu_position']));
			$data['list_lang'][$key]['list_parent']				= selectlist2(array('table' => 'frontend_menu', 'selected' => $datas[$key]['id_parent'],'where'=>'id_status_publish = 2 and is_delete = 0 and id_parent_lang is null and (id_parent = 0 or id_parent is null)'));

			$data['list_lang'][$key]['list_frontend_menu_type']	= selectlist2(array('table' => 'frontend_menu_type', 'selected' => $datas[$key]['id_frontend_menu_type']));
			$data['list_lang'][$key]['list_module']			 	= selectlist2(array('table' => 'module', 'selected' => $datas[$key]['id_module']));
			$data['list_lang'][$key]['list_status_publish']		= selectlist2(array('table' => 'status_publish', 'selected' => $datas[$key]['id_status_publish']));
			$data['list_lang'][$key]['publish_date'] 			= iso_date($datas[$key]['publish_date']);
				
			//sintak yang diatas artinya sama dengan sintak dibawah ini
			// if ($key==0) {
			// 	$data['lis_lang'][$key]['invis']="";
			// }else{
			// 	$data['lis_lang'][$key]['invis']="hide";
			// }
		}

		$data['list_lang2'] 	= $data['list_lang'];
		render('apps/frontend_menu/add', $data, 'apps');
	}

	public function view($id=''){
		if($id){
			$datas 	= $this->frontendMenuModel->selectData($id);

			if(!$datas){
				die('404');
			}
			
			$data['list_lang']	= $this->languagemodel->langName();
			foreach ($data['list_lang'] as $key => $value){
				$data['list_lang'][$key]['invis'] 			= ($key==0) ? '' : 'hide';
				$data['list_lang'][$key]['active'] 			= ($key==0) ? 'active in' : '';
				$data['list_lang'][$key]['validation'] 		= ($key==0) ? 'true' : 'false';
				$data['list_lang'][$key]['nomor'] 			= $key;

				$data['list_lang'][$key]['publish_date'] 	= iso_date($datas[$key]['publish_date']);
				$data['list_lang'][$key]['description']		= $datas[$key]['description'];
				$data['list_lang'][$key]['name'] 			= $datas[$key]['name'];
			}
			$data['list_lang2'] = $data['list_lang'];
		}
		render('apps/frontend_menu/view', $data, 'apps');
	}

	function records(){
		$data = $this->frontendMenuModel->records();
		foreach ($data['data'] as $key => $value){
			// $data['data'][$key]['name'] = quote_form($value['name']);
			$data['data'][$key]['publish_date'] = iso_date($value['publish_date']);
		}
		render('apps/frontend_menu/records', $data, 'blank');
	}	
	
	function proses($idedit=''){
		$this->layout 		= 'none';
		$post				= purify(null_empty($this->input->post()));
		$ret['error']		= 1;
		$where['uri_path']	= $post['uri_path'];
		
		if($idedit){
			$where['id !=']	= $idedit;
		}

		$this->form_validation->set_rules('id_menu_position', '"Position"', 'required'); 
		$this->form_validation->set_rules('name', '"Menu Name"', 'required'); 
		$this->form_validation->set_rules('id_frontend_menu_type', '"Menu Type"', 'required'); 
		
		if($post['id_frontend_menu_type'] == 2){
			$this->form_validation->set_rules('extra_param', '"URL"', 'required'); 
		}else{
			// $this->form_validation->set_rules('id_module', '"Module"', 'required'); 
		}

		$this->form_validation->set_rules('id_status_publish', '"Status"', 'required'); 
		
		if($this->form_validation->run() == FALSE){
			$ret['message']  = validation_errors(' ', ' ');
		}else{

			$this->db->trans_start();
			$id_parent_lang = NULL;
			// print_r($post);exit;
			foreach ($post['name'] as $key => $value){
				if($key==0){
				 	$id_parent 		= $post['id_parent'][$key];
				 	$id_position 	= $post['id_menu_position'][$key];
				 	$id_menu_type 	= $post['id_frontend_menu_type'][$key];
				 	$id_module 		= $post['id_module'][$key];
				 	$extra_params	= $post['extra_param'][$key];
				 	$publish_date	= $post['publish_date'][$key];
				 	$id_status 		= $post['id_status_publish'][$key];
				}
				else{
					$parent = $this->frontendMenuModel->fetchRow(array('id_parent_lang'=>$post['id_parent'][0],'id_language'=>$post['id_lang'][$key]));
					$idParent = $parent ? $parent['id'] : -1 ; //-1 = kalo abis nambah bahasa. pasti ga punya parent
					$id_parent = !$id_parent ? 0 : $idParent; 

					// echo $this->db->last_query();
				}
				$idedit 		= $post['id'][$key];
				// print_r($post);exit;
				$data_save['id_menu_position'] 		= $id_position;
				$data_save['name']			 		= $post['name'][$key];
				$data_save['id_frontend_menu_type'] = $id_menu_type;
				$data_save['id_module'] 			= $id_module;
				$data_save['extra_param'] 			= $extra_params;
				$data_save['id_status_publish'] 	= $id_status;
				$data_save['publish_date'] 			= iso_date($post['publish_date'][$key]);
				$data_save['description'] 			= $post['description'][$key];
				$data_save['seo_title'] 			= $post['seo_title'][$key];
				$data_save['meta_description'] 		= $post['meta_description'][$key];
				$data_save['id_parent'] 			= (int)$id_parent;
				$data_save['meta_keywords'] 		= $post['meta_keywords'][$key];
				$data_save['id_language'] 			= $post['id_lang'][$key];
				$data_save['id_parent_lang'] 		= $id_parent_lang;				  
				// $data_save['id'] 					= $post['id'][$key];
				
				if($idedit){
				 	auth_update();
					$ret['message'] = 'Update Success';
					$act			= "Update Frontend menu";
					$iddata 		= $this->frontendMenuModel->update($data_save, $idedit);
				}else{
					auth_insert();
					$ret['message'] = 'Insert Success';
					$act			= "Insert Frontend menu";
					$iddata 		= $this->frontendMenuModel->insert($data_save);
				}

				if($key==0){
					$id_parent_lang = $iddata;
				}

				detail_log();
				insert_log($act);
				$this->db->trans_complete();
				set_flash_session('message', $ret['message']);
				$ret['error'] = 0;
			}
		}
		echo json_encode($ret);
	}

	function del(){
		auth_delete();
		$id = $this->input->post('iddel');
		$this->frontendMenuModel->delete($id);
		$this->frontendMenuModel->delete2($id);
		detail_log();
		insert_log("Delete Frontend menu");
	}

	function get_callback($id){
		echo db_get_one('module', 'callback', array('id' => $id));
	}
	
}

/* End of file frontend_menu.php */
/* Location: ./application/controllers/apps/frontend_menu.php */