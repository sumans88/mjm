<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Social_media extends CI_Controller {
	
	function __construct(){
		parent::__construct();
		$this->load->model('Social_media_model');
	}

	function index(){
		$data['list_status_publish'] 	= selectlist2(array('table'=>'status_publish','title'=>'All Status','selected'=>$data['id_status_publish']));
		$data['list_languages'] 		= selectlist2(array('table'=>'language','title'=>'All Languages','selected'=>$data['id_lang']));
		render('apps/social_media/index',$data,'apps');
	}
	
	function add($id=''){
		if($id){
			$data = $this->Social_media_model->findById($id);

            if(!$data){
				die('404');
			}
			$data 				= quote_form($data);
			$data['judul']		= 'Edit';
			$data['proses']		= 'Update';
		}
		else{
			$data['judul']			= 'Add';
			$data['proses']			= 'Simpan';
			$data['name'] 			= '';
            $data['url'] 			= '';
            $data['description']	= '';
			$data['id'] 			= '';
		}


		$img_thumb 		= image($data['img'],'small');
		$imagemanager	= imagemanager('img',$img_thumb,750,186);
		$data['img']	= $imagemanager['browse'];
		$data['imagemanager_config'] 	= $imagemanager['config'];
		$data['list_status_publish'] 	= selectlist2(array('table'=>'status_publish','title'=>'Select Status','selected'=>$data['id_status_publish']));
		$data['list_languages'] 		= selectlist2(array('table'=>'language','title'=>'All Languages','selected'=>$data['id_lang']));

		render('apps/social_media/add',$data,'apps');
	}

	function view($id=''){
		if($id){
			$data = $this->Social_media_model->findById($id);
			
			if(!$data){
				die('404');
			}
		}
		render('apps/social_media/view',$data,'blank');
	}

	function records(){
		$data = $this->Social_media_model->records();
		// foreach ($data['data'] as $key => $value) {
		// 	$data['data'][$key]['slideshow_title'] 	= quote_form($value['slideshow_title']);
		// 	$data['data'][$key]['publish_date'] 	= iso_date($value['publish_date']);
		// 	$data['data'][$key]['approval_level'] 	= $approval;
		// }
		render('apps/social_media/records',$data,'blank');
	}
	
	
	function proses($idedit=''){
		$id_user 		= id_user();
		$this->layout 	= 'none';
		$post 			= purify($this->input->post());
		$ret['error']	= 1;
		$this->db->trans_start();

		$this->form_validation->set_rules('id_lang', '"Status Publish"', 'required'); 
		$this->form_validation->set_rules('name', '"Name of Social Media"', 'required'); 
		$this->form_validation->set_rules('url', '"URL"', 'required'); 
		$this->form_validation->set_rules('id_status_publish', '"Status Publish"', 'required'); 
		if ($this->form_validation->run() == FALSE){
			$ret['message']  = validation_errors(' ',' ');
		}
		else{
			// print_r($post);
			if($idedit){
				auth_update();
				$ret['message'] = 'Update Success';
				$act			= "Update Social Media";
				$this->Social_media_model->update($post,$idedit);
			}
			else{
				auth_insert();
				$ret['message'] = 'Insert Success';
				$act			= "Insert Social Media";
				$iddata 		= $this->Social_media_model->insert($post);
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
		$this->Social_media_model->delete($id);
		$this->db->trans_complete();
	}
	
}

/* End of file Social_media.php */
/* Location: ./application/controllers/apps/Social_media.php */