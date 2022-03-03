<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Gallery extends CI_Controller {
	function __construct(){
		parent::__construct();
		$this->load->model('galleryModel');
		$this->load->model('galleryDetailModel');
		$this->load->model('languagemodel');
	}
	function index(){
		// $data['list_cat'] = selectlist2(array('table'=>'ref_kategori_logframe','title'=>'All Category'));
		render('apps/gallery/index',$data,'apps');
	}
	public function add($id=''){
		if($id){
			// echo $id;exit;
			// $data = $this->galleryModel->findById($id);
			$datas 	= $this->galleryModel->selectData($id);

			if(!$datas){
				die('404');
			}
			$data 					= quote_form($datas);
			$data['judul']			= 'Edit';
			$data['proses']			= 'Update';
		}else{
			$data['judul']			= 'Add';
			$data['proses']			= 'Simpan';
			$data['name_data']		= '';
			$data['uri_path']		= '';
			$data['description']			= '';
			$data['content']		= '';
			$data['start_date']		= '';
			$data['end_date']		= '';
			$data['speaker']		= '';
			$data['id'] 			= '';
			$data['id_parent_lang']	= '';
		}

		$data['list_lang']	= $this->languagemodel->langName();

		foreach ($data['list_lang'] as $key => $value){
			$data['list_lang'][$key]['invis'] 			= ($key==0) ? '' : 'hide';
			$data['list_lang'][$key]['active'] 			= ($key==0) ? 'active in' : '';
			$data['list_lang'][$key]['validation'] 		= ($key==0) ? 'true' : 'false';
			$data['list_lang'][$key]['nomor'] 			= $key;

			$data['list_lang'][$key]['id']				= $datas[$key]['id'];
			$data['list_lang'][$key]['name_data']		= $datas[$key]['name'];
			$data['list_lang'][$key]['description'] 			= quote_form($datas[$key]['description']);
			$data['list_lang'][$key]['uri_path']		= $datas[$key]['uri_path'];
			$data['list_lang'][$key]['is_open']			= ($datas[$key]['is_open']==1) ? 'checked' : '';
			$data['list_lang'][$key]['content']			= $datas[$key]['content'];
			$data['list_lang'][$key]['speaker']			= $datas[$key]['speaker'];
			$data['list_lang'][$key]['start_date']		= iso_date($datas[$key]['start_date']);
			$data['list_lang'][$key]['end_date']		= iso_date($datas[$key]['end_date']);

			$img_thumb											= image($datas[$key]['img'],'small');
			$imagemanager										= imagemanager('img',$img_thumb,200,150,$key);
			$data['list_lang'][$key]['img']						= $imagemanager['browse'];
			$data['list_lang'][$key]['imagemanager_config']		= $imagemanager['config'];
		}

		$data['list_lang2'] 	= $data['list_lang'];
		render('apps/gallery/add',$data,'apps');
	}
	public function view($id=''){
		if($id){
			$data 	= $this->galleryModel->selectData($id,1);
			if(!$data){
				die('404');
			} 
			$data['id_gallery'] = $id;
			render('apps/gallery/view',$data,'apps');
		}
		else{
				die('404');
		}
	}
	function records(){
		$data = $this->galleryModel->records();
		foreach ($data['data'] as $key => $value) {
			$data['data'][$key]['name'] = quote_form($value['name']);
			$data['data'][$key]['start_date'] = iso_date($value['start_date']);
			$data['data'][$key]['end_date'] = iso_date($value['end_date']);
		}
		render('apps/gallery/records',$data,'blank');
	}
	function recordsgallery($id_gallery){
		$data = $this->galleryDetailModel->records(array('id_gallery'=>$id_gallery));
		foreach ($data['data'] as $key => $value) {
			$data['data'][$key]['image'] 			= getImg($value['img']);
			$data['data'][$key]['title'] 		= $value['title'];
			$data['data'][$key]['description'] 	= $value['description'];
		}
		render('apps/gallery/recordsgallery',$data,'blank');
	}	
	
	function proses($idedit=''){
		$id_user 				= id_user();
		$this->layout 			= 'none';
		$post					= purify($this->input->post());
		$ret['error']			= 1;
		$id_parent_lang 		= NULL;
		$this->db->trans_start(); 
		// $id_parent		= $this->languagemodel->langId();

		foreach ($post['name'] as $key => $value){
			if(!$idedit){
				$where['uri_path']			= $post['uri_path'][$key];
				$unik 	= $this->galleryModel->findBy($where);
				$this->form_validation->set_rules('name', '"page Name"', 'required'); 
				$this->form_validation->set_rules('description', '"Description"', 'required'); 
				if ($this->form_validation->run() == FALSE){
					$ret['message']  	= validation_errors(' ',' ');
				}
				if($unik){
					$ret['message']		= "Page URL $value already taken";
				}
			}
			
			if($idedit){
				$where['id !=']		= $idedit;
			}
			$idedit		= $post['id'][$key];
				
			$data_save['name']			= $post['name'][$key];
			$data_save['description']	= $post['description'][$key];
			$data_save['uri_path'] 		= $post['uri_path'][$key];
			$data_save['id_lang']		= $post['id_lang'][$key];
			$data_save['id_parent_lang']= $id_parent_lang;
			if($post['img'][$key]){
				$data_save['img']		= $post['img'][$key];
			}
			
			if($idedit){
				auth_update();
				$ret['message'] = 'Update Success';
				$act			= "Update Gallery";
				// if(!$post['img'][$key]){
				// 	unset($post['img'][$key]);
				// }
				$iddata 		= $this->galleryModel->update($data_save,$idedit);
			}else{
				auth_insert();
				$ret['message'] = 'Insert Success';
				$act			= "Insert Gallery";
				$iddata 		= $this->galleryModel->insert($data_save);
				// print_r($unik);
			}
			if($key==0){
				$id_parent_lang	= $iddata;
			}
			detail_log();
			insert_log($act);
			$this->db->trans_complete();
			set_flash_session('message', $ret['message']);
			$ret['error'] = 0;
		}
		echo json_encode($ret);
	}
	function del(){
		auth_delete();
		$id = $this->input->post('iddel');
		$this->galleryModel->delete($id);
		$this->galleryModel->delete2($id);
		detail_log();
		insert_log("Delete Gallery");
	}
	function delgallery(){
		auth_delete();
		$id = $this->input->post('iddel');
		$this->galleryDetailModel->delete($id);
		$this->galleryDetailModel->delete2($id);
		detail_log();
		insert_log("Delete Gallery");
	}

	function select_page(){
		render('apps/gallery/select_page',$data,'blank');

	}
	function record_select_page(){
		$data = $this->galleryModel->records();
		foreach ($data['data'] as $key => $value) {
			$data['data'][$key]['name'] = quote_form($value['name']);
		}
		render('apps/gallery/record_select_page',$data,'blank');
	}
	public function approve($gallery_id='',$id=''){
		if($id){
			$datas 	= $this->galleryModel->selectDataParticipant($id);
			if(!$datas){
				die('404');
			} 
			else {
				foreach ($datas as $key => $value) {
					$data['name'] 		=  $value['name'];
					$data['id'] 		=  $id;
					$data['email'] 		=  $value['email'];
					$data['phone'] 		=  $value['phone'];
					$data['address'] 	=  $value['address'];
					$data['dob'] 		=  iso_date($value['dob']);
					$data['gender'] 	=  ($value['gender'] = 1 ? 'Male' : 'Female');
				}
			}
		}
		$data['gallery_id'] = $gallery_id;
		render('apps/gallery/view_participant',$data,'apps');
	}

	function proses_approve($gallery_id='',$id=''){

		if (!empty($id)){
			$data_save['is_approve'] = 1;
			$update_status = $this->galleryModel->updateApprovaalParticipant($data_save,$id);
			if ($update_status){
				//redirect ke halaman participant
				redirect(base_url("apps/gallery/view/$gallery_id"));
			}
		}
	}



	public function addgallery($id_gallery='',$id){
		if($id){
			// echo $id;exit;
			// $data = $this->galleryModel->findById($id);
			$datas 	= $this->galleryDetailModel->selectData($id);

			if(!$datas){
				die('404');
			}
			$data 					= quote_form($datas);
			$data['judul']			= 'Edit';
			$data['proses']			= 'Update';
		}else{
			$data['judul']			= 'Add';
			$data['proses']			= 'Simpan';
			$data['name_data']		= '';
			$data['uri_path']		= '';
			$data['description']			= '';
			$data['content']		= '';
			$data['start_date']		= '';
			$data['end_date']		= '';
			$data['speaker']		= '';
			$data['id'] 			= '';
			$data['id_parent_lang']	= '';
		}

		$data['list_lang']	= $this->languagemodel->langName();

		foreach ($data['list_lang'] as $key => $value){
			$data['list_lang'][$key]['invis'] 			= ($key==0) ? '' : 'hide';
			$data['list_lang'][$key]['active'] 			= ($key==0) ? 'active in' : '';
			$data['list_lang'][$key]['validation'] 		= ($key==0) ? 'true' : 'false';
			$data['list_lang'][$key]['nomor'] 			= $key;

			$data['list_lang'][$key]['id']				= $datas[$key]['id'];
			$data['list_lang'][$key]['name_data']		= $datas[$key]['title'];
			$data['list_lang'][$key]['description'] 			= quote_form($datas[$key]['description']);
			$data['list_lang'][$key]['uri_path']		= $datas[$key]['uri_path'];
			$data['list_lang'][$key]['is_open']			= ($datas[$key]['is_open']==1) ? 'checked' : '';
			$data['list_lang'][$key]['content']			= $datas[$key]['content'];
			$data['list_lang'][$key]['speaker']			= $datas[$key]['speaker'];
			$data['list_lang'][$key]['start_date']		= iso_date($datas[$key]['start_date']);
			$data['list_lang'][$key]['end_date']		= iso_date($datas[$key]['end_date']);

			$img_thumb											= image($datas[$key]['img'],'small');
			$imagemanager										= imagemanager('img',$img_thumb,200,150,$key);
			$data['list_lang'][$key]['img']						= $imagemanager['browse'];
			$data['list_lang'][$key]['imagemanager_config']		= $imagemanager['config'];
		}
		$gallery 			= $this->galleryModel->selectData($id_gallery,1);
		$data['name'] 		= $gallery['name'];
		$data['id_gallery'] = $id_gallery;
		$data['idedit'] 	= $id;
		$data['list_lang2'] = $data['list_lang'];
		render('apps/gallery/addgallery',$data,'apps');
	}

	function prosesgallery($id_gallery,$idedit=''){
		$id_user 				= id_user();
		$this->layout 			= 'none';
		$post					= purify($this->input->post());
		$ret['error']			= 1;
		$id_parent_lang 		= NULL;
		$this->db->trans_start(); 
		// $id_parent		= $this->languagemodel->langId();

		foreach ($post['title'] as $key => $value){
			if(!$idedit){
				$where['uri_path']			= $post['uri_path'][$key];
				$unik 	= $this->galleryModel->findBy($where);
				$this->form_validation->set_rules('img', '"Image"', 'required'); 
				if ($this->form_validation->run() == FALSE){
					$ret['message']  	= validation_errors(' ',' ');
				}
				if($unik){
					$ret['message']		= "Page URL $value already taken";
				}
			}
			
			if($idedit){
				$where['id !=']		= $idedit;
			}
			$idedit		= $post['id'][$key];
			if($key!=0){
				$id_gallery = db_get_one('gallery','id',array('id_parent_lang'=>$id_gallery,'id_lang'=>$post['id_lang'][$key])); 
			}
				
			$data_save['id_gallery']	= $id_gallery;
			$data_save['title']			= $post['title'][$key];
			$data_save['description']	= $post['description'][$key];
			$data_save['uri_path'] 		= $post['uri_path'][$key];
			$data_save['id_lang']		= $post['id_lang'][$key];
			$data_save['id_parent_lang']= $id_parent_lang;
			
			if($post['img'][0]){
				$data_save['img']= $post['img'][0];
			}

			if($idedit){
				auth_update();
				$ret['message'] = 'Update Success';
				$act			= "Update Gallery";
				// if(!$post['img'][$key]){
				// 	unset($post['img'][$key]);
				// }
				$iddata 		= $this->galleryDetailModel->update($data_save,$idedit);
			}else{
				auth_insert();
				$ret['message'] = 'Insert Success';
				$act			= "Insert Gallery";
				$iddata 		= $this->galleryDetailModel->insert($data_save);
				// print_r($unik);
			}
			if($key==0){
				$id_parent_lang	= $iddata;
			}
			detail_log();
			insert_log($act);
			$this->db->trans_complete();
			set_flash_session('message', $ret['message']);
			$ret['error'] = 0;
		}
		echo json_encode($ret);
	}
}

/* End of file gallery.php */
/* Location: ./application/controllers/apps/gallery.php */