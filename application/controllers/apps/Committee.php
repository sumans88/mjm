<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Committee extends CI_Controller{
	function __construct(){
		parent::__construct();
		$this->load->model('committee_model');
		$this->load->model('committetagsmodel');
		$this->load->model('newstagsmodel');
		$this->load->model('tagsmodel');
		$this->load->model('committeeFilesModel');
	}



	function index()

	{


		render('apps/committee/index',$data,'apps');

	}



	public function add($id=''){

		if($id){

			$data = $this->committee_model->findById($id);

			if(!$data){

				die('404');

			}

			$data 					= quote_form($data);

			$data['judul']			= 'Edit';

			$data['proses']			= 'Update';

			$tags = $this->committetagsmodel->findBy(array('id_committee'=>$id));
			
            foreach ($tags as $key => $value) 
            {
                $tag .=  ','.$value['tags'];
            }

            $data['tags']               = substr($tag,1);


		}

		else{

			$data['judul']			= 'Add';

			$data['proses']			= 'Save';

			$data['tags'] 			= '';

			$data['name'] 			= '';

			$data['chair'] 			= '';
			
			$data['chair_img'] 		= '';
			
			
			$data['co_chair'] 		= '';
			$data['co_chair_img'] 	= '';
			$data['co_chair2'] 		= '';
			$data['co_chair_img2'] 	= '';

			$data['uri_path']		= '';

			$data['teaser']			= '';

			$data['contact']		= '';

			$data['page_content']	= '';

			$data['id'] 			= '';

		}

		$chair_img_thumb = image($data['chair_img'],'small');

		$imagemanager = imagemanager('chair_img',$chair_img_thumb);


		$data['chair_img'] = $imagemanager['browse'];

		$data['imagemanager_config'] = $imagemanager['config'];


		$co_chair_img_thumb = image($data['co_chair_img'],'small');

		$imagemanager = imagemanager('co_chair_img',$co_chair_img_thumb);


		$data['co_chair_img'] = $imagemanager['browse'];

		$co_chair_img_thumb2 = image($data['co_chair_img2'],'small');

		$imagemanager = imagemanager('co_chair_img2',$co_chair_img_thumb2);


		$data['co_chair_img2'] = $imagemanager['browse'];

		$data['imagemanager_config']	= $imagemanager['config'];

		$tags_data = $this->newstagsmodel->records_tags_all();
		foreach ($tags_data as $key => $value_tags) {
		    $tags_data_val .=  ",'".$value_tags['name']."'";
		}
		$data['tags_data'] 			= substr($tags_data_val,1);
		
		$data['list_tags'] = selectlist2(array('table'=>'tags','title'=>'','where'=>array('is_delete'=>0)));



		
		$data['list_status_publish'] = selectlist2(
			array(
				'table'=>'status_publish',
				'selected'=>$data['id_status_publish']
				)
			);

		$data['nomor']      		= 0;

		$data['committee_files']      	= $this->show_files($id,0);

		$data['multiple_file_script']	= get_event_files("committee/add_multiple_file.html", '', 0);

		render('apps/committee/add',$data,'apps');

	}



	public function view($id=''){

		if($id){

			$data = $this->committee_model->findById($id);

			$data['img_thumb'] 	= image($data['img'],'small');

			$data['img_ori'] 	= image($data['img'],'large');

			if(!$data){

				die('404');

			}

			$data['page_name'] 	= quote_form($data['page_name']);

			$data['teaser'] 	= quote_form($data['teaser']);

		}

		render('apps/committee/view',$data,'apps');

	}



	function records(){

		$data = $this->committee_model->records();

		render('apps/committee/records',$data,'blank');

	}



	function proses($idedit=''){

		$this->layout 			= 'none';

		$post 					= purify($this->input->post());
		$post['chair_description'] 	= htmlspecialchars_decode(urldecode($post['chair_description']));
		$post['co_chair_description'] 	= htmlspecialchars_decode(urldecode($post['co_chair_description']));
		$post['co_chair_description2'] 	= htmlspecialchars_decode(urldecode($post['co_chair_description2']));
		$post['page_content'] 	= htmlspecialchars_decode(urldecode($post['page_content']));
		$post['contact'] 		= htmlspecialchars_decode(urldecode($post['contact']));
	
		$ret['error']		= 1;

		$where['uri_path'] 	= $post['uri_path'];

		if($idedit){

			$where['id !='] = $idedit;

		}


		$unik 					= $this->committee_model->findBy($where);

		$this->form_validation->set_rules('name', '"page Name"', 'required');

		$this->form_validation->set_rules('uri_path', '"Page URL"', 'required');

		/*$this->form_validation->set_rules('teaser', '"Teaser"', 'required');*/

		if ($this->form_validation->run() == FALSE){

			$ret['message']  = validation_errors(' ',' ');

		}

		else if($unik){

			$ret['message']	= "Page URL $post[uri_path] already taken";

		}

		else{
			$this->db->trans_start();

			$tags = $post['tags'];
			unset($post['tags']);
			
			$post['chair_img'] = $post['chair_img'][0];
			$post['co_chair_img'] = $post['co_chair_img'][0];
			$post['co_chair_img2'] = $post['co_chair_img2'][0];
		
			if($idedit){

				auth_update();

				$ret['message'] = 'Update Success';

				$act			= "Update Committee";

				if(!$post['chair_img']){

					unset($post['chair_img']);

				}

				if(!$post['co_chair_img']){

					unset($post['co_chair_img']);

				}
				if(!$post['co_chair_img2']){

					unset($post['co_chair_img2']);

				}

				if($post['id_lang']){

					unset($post['id_lang']);

				}

				unset($post['id_committee']);

				$this->committee_model->update($post,$idedit);

			}

			else{

				auth_insert();

				$ret['message'] = 'Insert Success';

				$act			= "Insert Committee";

				if($post['id_lang']){

					unset($post['id_lang']);

				}

				$this->committee_model->insert($post);

			}

			foreach ($tags as $key => $value) {
					$value = strtolower($tags[$key]);
					if($value){
						$cek = $this->tagsmodel->fetchRow(array('name'=>$value));//liat tags name di tabel ref
						if(!$cek){//kalo belom ada
							$id_tags = $this->tagsmodel->insert(array('name'=>$value,'uri_path'=>url_title($value)));//insert ke tabel ref
							detail_log();
						}
						else{
							$id_tags = $cek['id']; //kalo udah ada, tinggal ambil idnya
						}
						$cekTagsNews = $this->committetagsmodel->fetchRow(array('id_committee'=>$idedit,'id_tags'=>$id_tags)); //liat di tabel news tags, (utk edit)
					
						if(!$cekTagsNews){//kalo blm ada ya di insert
							$tag['id_committee'] = $idedit;
							$tag['id_tags'] = $id_tags;
							$id_news_tags = $this->committetagsmodel->insert($tag);
						}
						else{//kalo udah ada, ambil id nya utk di simpen sbg array utk kebutuhan delete
							$id_news_tags = $cekTagsNews['id'];
						}
						$del_tags_news[] = $id_news_tags;

					}
				}
				$this->db->where_not_in('a.id',$del_tags_news); 
				$delete = $this->committetagsmodel->findBy(array('a.id_committee'=>$idedit)); //dapetin id news tags yg diapus  (where id not in insert / select and id_news = $idedit)
				foreach ($delete as $key => $value) {
					$this->committetagsmodel->delete($value['id']);
				}

			detail_log();

			insert_log($act);

			$this->db->trans_complete();

			$this->session->set_flashdata('message',$ret['message']);

			$ret['error'] = 0;

		}

		echo json_encode($ret);

	}



	function del(){

		auth_delete();

		$id 	= $this->input->post('iddel');

		$data 	= $this->committee_model->delete($id);

		detail_log();

		insert_log("Delete Committee");

	}



	function select_page(){

		render('apps/committee/select_page',$data,'blank');



	}



	function record_select_page(){

		$data = $this->committee_model->records();

		foreach ($data['data'] as $key => $value) {

			$data['data'][$key]['page_name'] = quote_form($value['page_name']);

		}

		render('apps/committee/record_select_page',$data,'blank');

	}

	function tagsurl()
	{
		$this->load->model('tagsmodel');
		$data = $this->tagsmodel->findBy();
		foreach ($data as $key => $value) {
			echo $value['name'].' || '.$value['uri_path'].'<br>';
			$this->tagsmodel->update(array('uri_path'=>url_title(strtolower($value['name']))),$value['id']);
		}
	}

	function show_files($idcommittee, $key){
		$allFiles = array();
		
		$wh['is_delete']	= 0;
		$wh['id_committee']		= $idcommittee;
		$allFiles['nomorFile'] 			= $key;
		$allFiles['list_files'] = array();
		$listFiles = $this->committeeFilesModel->listFiles($wh);
		foreach ($listFiles as $key => $value) {
			$ext = pathinfo($value['filename'], PATHINFO_EXTENSION);
			if($ext == "pdf"){
				$listFiles[$key]['imageFile'] = "file-pdf.jpg";
			}
			if($ext == "xls" | $ext == "xlsx"){
				$listFiles[$key]['imageFile'] = "file-excel.jpg";
			}
			if($ext == "doc" | $ext == "docx"){
				$listFiles[$key]['imageFile'] = "file-word.jpg";
			}
			if($ext == "ppt" | $ext == "pptx"){
				$listFiles[$key]['imageFile'] = "file-ppt.jpg";
			}
		}
		$allFiles['list_files'] = $listFiles;

		$allFiles['jmlFiles']		= count($allFiles['list_files']);
		$allFiles['showUploadAll']	= (count($allFiles['list_files']) != NULL ? 'block' : 'none');

		return get_event_files("committee/list_files.html", $allFiles, 1);
	}

	// function uploadFiles($idedit=''){
	// 	$post = purify($this->input->post(NULL,TRUE));
	// 	$data = array();




	// 	$data_save['name']			= $post['nameFile'];
	// 	$id_event 					= $post['id_event'];
	// 	$id_lang 					= $post['id_lang'];
		
	// 	if($post["statusFile"] == 0){
	// 		foreach($_FILES as $index => $file){
	// 			$uploadFile = multipleUpload($file, './document/material/', 20000000);
				
	// 			if($uploadFile == true){
					
	// 				$data_save['filename']	= $uploadFile['file_name'];
	// 				auth_insert();
	// 				$data_save['id_committee']	= $post['id_committee'];
	// 				$ret['message'] 			= 'Insert Success';
	// 				$act						= "Insert Committee Files";
	// 				$iddata 					= $this->committeeFilesModel->insert($data_save);
					
	// 				$data["idFile"] = $post["imag"];
	// 				$data["statusFile"] = $post["statusFile"];
	// 			}
	// 		}
	// 	} else{
	// 		$idedit = $post["idSavedFile"];

	// 		auth_update();
	// 		$ret['message'] = 'Update Success';
	// 		$act			= "Update Committee Files ";
	// 		$iddata 		= $this->committeeFilesModel->updateAll($data_save,$idedit);
	// 		$data["idFile"] = $post["imag"];
	// 		$data["statusFile"] = $post["statusFile"];
	// 	}

	// 	detail_log();
	// 	insert_log($act);
	// 	$this->db->trans_complete();
	// 	set_flash_session('message', $ret['message']);

	// 	if($iddata != 0){
	// 		$data["idSavedFile"] = $iddata;
	// 		$data["status"] = true;
	// 	} else{
	// 		$data["idSavedFile"] = 0;
	// 		$data["status"] = false;
	// 	}

	// 	$data["reloadFiles"] = $this->show_files($post['id_event'], $post['key']);

	// 	echo json_encode($data);
	// }

	// function prosesdocument($field){
	// 	$file = $_FILES[$field];
	// 	// echo json_encode($_FILES);
	// 	 // print_r($file);
	// 	$fname = rand(100,999).'-'.$file['name'];
	// 	move_uploaded_file($file['tmp_name'],"document/temp/$fname");
	// 	$ret['fname'] = "$fname";
	// 	echo json_encode($ret);
	// }

	// function show_images($idgallery, $key){
	// 	$allImages = array();

	// 	$wh['is_delete'] = 0;
	// 	$this->db->where('id_gallery', $idgallery);
	// 	$allImages['nomorGal']      = $key;
	// 	$allImages['list_images']   = $this->galleryImagesModel->listImages($wh);
		
	// 	$allImages['jmlImages']     = count($allImages['list_images']);
	// 	$allImages['showUploadAll'] = (count($allImages['list_images']) != NULL ? 'block' : 'none');

	// 	return get_gallery_images("event/gallery_list_images.html", $allImages, 1);
	// }

	function uploadFiles($idedit=''){
		$post = purify($this->input->post(NULL,TRUE));
		$data = array();

		$data_save['name']			= $post['nameFile'];
		$id_committee 				= $post['id_committee'];
		$id_lang 					= $post['id_lang'];
		
		if($post["statusFile"] == 0){
			foreach($_FILES as $index => $file){
				$uploadFile = multipleUpload($file, './document/material/', 20000000);
				
				if($uploadFile == true){
					
					$data_save['filename']	= $uploadFile['file_name'];

					if($id_lang == 1){
						auth_insert();
						$data_save['id_committee']	= $id_committee;
						$ret['message'] = 'Insert Success';
						$act			= "Insert Committee File";
						$iddata 		= $this->committeeFilesModel->insert($data_save);
					} else{
						auth_insert();
						$data_save['id_committee']	= $id_committee_new;
						$ret['message'] = 'Insert Success';
						$act			= "Insert Committee File";
						$iddata 		= $this->committeeFilesModel->insert($data_save);
					}

					$data_save['id_parent_lang']	= $iddata;

					if($id_lang == 1){
						auth_insert();
						$data_save['id_committee']	= $id_committee_new;
						$ret['message'] = 'Insert Success';
						$act			= "Insert Committee File";
						$iddata 		= $this->committeeFilesModel->insert($data_save);
					} else{
						auth_insert();
						$data_save['id_committee']	= $id_committee;
						$data_save['id_lang']	= $id_lang;
						$ret['message'] = 'Insert Success';
						$act			= "Insert Committee File";
						$iddata 		= $this->committeeFilesModel->insert($data_save);
					}

					$data["idFile"] = $post["imag"];
					$data["statusFile"] = $post["statusFile"];
				}
			}
		} else{
			$idedit = $post["idSavedFile"];

			auth_update();
			$ret['message'] = 'Update Success';
			$act			= "Update Committee File";
			$iddata 		= $this->committeeFilesModel->updateAll($data_save,$idedit);
			$data["idFile"] = $post["imag"];
			$data["statusFile"] = $post["statusFile"];
		}

		detail_log();
		insert_log($act);
		$this->db->trans_complete();
		set_flash_session('message', $ret['message']);

		if($iddata != 0){
			$data["idSavedFile"] = $iddata;
			$data["status"] = true;
		} else{
			$data["idSavedFile"] = 0;
			$data["status"] = false;
		}

		$data["reloadFiles"] = $this->show_files($post['id_committee'], $post['key']);

		echo json_encode($data);
	}

	function deleteFile(){
		$post = purify($this->input->post(NULL,TRUE));

		auth_delete();
		$this->committeeFilesModel->delete($post['idSavedFile']);
		unlink('./document/material/'.$post['filename']);
		detail_log();
		insert_log("Delete Event File");


		$data["idFile"] = $post["imag"];
		$data["status"] = true;
		$data["reloadFiles"] = $this->show_files($post['id_committee'], 0);

		echo json_encode($data);
	}


}



/* End of file committee.php */

/* Location: ./application/controllers/apps/committee.php */

