<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Gallery_video extends CI_Controller {
	function __construct(){
		parent::__construct();
		$this->load->model('galleryModel');
		$this->load->model('galleryImagesModel');
		$this->load->model('galleryDetailModel');
		$this->load->model('languagemodel');
		$this->load->model('tagsModel');
		$this->load->model('newsTagsModel');
		$this->load->model('eventTagsModel');
		$this->load->model('newsTagsVersionModel');
		$this->load->model('galleryTagsModel');
	}
	function index(){
		// $data['list_cat'] = selectlist2(array('table'=>'ref_kategori_logframe','title'=>'All Category'));
		render('apps/gallery_video/index',$data,'apps');
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
			$data['publish_date'] 	= iso_date($data['publish_date']);
			$data['id'] 			= $id;
		}else{
			$data['judul']            = 'Add';
			$data['proses']           = 'Save';
			$data['name_data']        = '';
			$data['tags']             = '';
			$data['uri_path']         = '';
			$data['description']      = '';
			$data['content']          = '';
			$data['start_date']       = '';
			$data['link_youtube_url'] = '';
			$data['end_date']         = '';
			$data['speaker']          = '';
			$data['id']               = '';
			$data['id_parent_lang']   = '';
			$data['youtube_url']      = '';
			$data['seo_title']        = '';
			$data['meta_description'] = '';
			$data['meta_keywords']    = '';
			$data['publish_date']     = '';
		}

		$data['list_lang']	= $this->languagemodel->langName();
		$tags_data = $this->newsTagsModel->records_tags_all();
		foreach ($tags_data as $key => $value_tags) {
		    $tags_data_val .=  ',"'.$value_tags['name'].'"';
		}
		$data['tags'] = substr($tags_data_val,1);
		$this->db->group_by('filename');
		$allImages = $this->db->select("id, filename")->get_where('gallery_images', array('is_delete'=>0, 'filename !='=>''))->result_array();
  
		foreach ($data['list_lang'] as $key => $value){
			$data['list_lang'][$key]['invis']                 = ($key==0) ? '' : 'hide';
			$data['list_lang'][$key]['active']                = ($key==0) ? 'active in' : '';
			$data['list_lang'][$key]['validation']            = ($key==0) ? 'true' : 'false';
			$data['list_lang'][$key]['nomor']                 = $key;
			
			$data['list_lang'][$key]['id_gallery']            = $datas[$key]['id'];
			$data['list_lang'][$key]['name_data']             = $datas[$key]['name'];
			$data['list_lang'][$key]['description']           = quote_form($datas[$key]['description']);
			$data['list_lang'][$key]['uri_path']              = $datas[$key]['uri_path'];
			$data['list_lang'][$key]['content']               = $datas[$key]['content'];
			$data['list_lang'][$key]['youtube_url']           = $datas[$key]['youtube_url'];
			$data['list_lang'][$key]['start_date']            = iso_date($datas[$key]['start_date']);
			$data['list_lang'][$key]['end_date']              = iso_date($datas[$key]['end_date']);
			$data['list_lang'][$key]['seo_title']             = $datas[$key]['seo_title'];
			$data['list_lang'][$key]['meta_description']      = $datas[$key]['meta_description'];
			$data['list_lang'][$key]['meta_keywords']         = $datas[$key]['meta_keywords'];
			$data['list_lang'][$key]['id_status_publish']        = $datas[$key]['id_status_publish'];
			$data['list_lang'][$key]['gallery_images']        = $this->show_images($datas[$key]['id'], $key);
			$data['list_lang'][$key]['publish_date2']         = iso_date($datas[$key]['publish_date']);
			$data['list_lang'][$key]['list_status_publish'] 	 = selectlist2(array('table'=>'status_publish','title'=>'Select Status','selected'=>$datas[$key]['id_status_publish']));

			$img_thumb                                        = image($datas[$key]['img'],'small');
			$imagemanager                                     = imagemanager('img',$img_thumb,320,180,$key,$datas[$key]['name'],'title'.$key);
			$data['list_lang'][$key]['img']                   = $imagemanager['browse'];
			$data['list_lang'][$key]['imagemanager_config']   = $imagemanager['config'];
			$data['list_lang'][$key]['list_gallery_category'] = selectlist2(array('table'=>'gallery_category','title'=>'Select Category','selected'=>$datas[$key]['id_gallery_category'],'where'=>array('is_delete'=>0)));

			/*untuk tag*/
					$this->db->select('a.*,b.name as tags,b.uri_path');
					$this->db->join('tags b','b.id = a.id_tags');
			$tags = $this->db->get_where('gallery_tags a',array('id_gallery'=>$datas[$key]['id']))->result_array();
			$tag = '';
			foreach ($tags as $k => $v){
				$tag .=  ','.$v['tags'];
			}
			$data['list_lang'][$key]['tags_data'] 				= substr($tag,1);
			/*end untuk tag*/

			/*Untuk File Upload*/
			$filemanager = filemanager($key+1,$datas[$key]['filename']);
			$data['list_lang'][$key]['file_upload']				= $filemanager['browse'];
		}
		$data['filemanager_config'] 	= $filemanager['config'];

		$data['id_gallery_category']  = $datas[0]['id_gallery_category'];

		$data['list_lang2'] 	= $data['list_lang'];
		foreach($data['list_lang2'] as $key => $value){
			$data['list_lang2'][$key]['lang_name'] = ucwords($value['lang_name']);
		}

		$data['gallery_images_modal']	= get_gallery_images("gallery/add_modal.html", $allImages, 0);
		$data['multiple_image_script']	= get_gallery_images("gallery/add_multiple_image.html", '', 0);

		render('apps/gallery_video/add',$data,'apps');
	}

	function show_images($idgallery, $key){
		$allImages = array();

		$wh['is_delete'] = 0;
		$this->db->where('id_gallery', $idgallery);
		$allImages['nomorGal'] 			= $key;
		$allImages['list_images']       = $this->galleryImagesModel->listImages($wh);

		$allImages['jmlImages']		= count($allImages['list_images']);
		$allImages['showUploadAll']	= (count($allImages['list_images']) != NULL ? 'block' : 'none');

		$tags_data = $this->newsTagsModel->records_tags_all();
		foreach ($tags_data as $key => $value_tags) {
		    $tags_data_val .=  ",'".$value_tags['name']."'";
		}
		$allImagestags = substr($tags_data_val,1);

			// print_r($allImages['list_images']);exit;
		/*untuk tag*/
		foreach ($allImages['list_images'] as $key => $value) {
			$this->db->where('is_delete', 0);
			$this->db->select('group_concat(id_tags) as hasil');
			$tags = $this->db->get_where('gallery_tags',array('id_images'=>$value['idImag']))->row_array()['hasil'];
			$arr_tags = explode(',', $tags);


			$this->db->select('group_concat(name) as hasil');
			$this->db->where_in('id',$arr_tags);
			$tagsname = $this->db->get_where('tags')->row_array()['hasil'];
			// print_r($this->db->last_query());exit;
			$allImages['list_images'][$key]['tag_images'] = $tagsname;
			$allImages['list_images'][$key]['tags'] = $allImagestags;
		}
			

		
		return get_gallery_images("gallery/list_images.html", $allImages, 1);
	}

	public function view($id=''){
		if($id){
			$data 	= $this->galleryModel->selectData($id,1);
			if(!$data){
				die('404');
			} 
			$data['id_gallery'] = $id;
			$this->session->set_userdata('id_view', $id);
			render('apps/gallery_video/view',$data,'apps');
		}
		else{
				die('404');
		}
	}
	function records(){
		$this->db->where('a.id_gallery_category', 4);
		$data = $this->galleryModel->records_video();
		foreach ($data['data'] as $key => $value) {
			$cat = $value['id_gallery_category'];
			$data['data'][$key]['name'] = ($value['name'] != 'NULL' ? quote_form($value['name']) : '');
			$data['data'][$key]['name_e'] = ($value['title_e'] != 'NULL' ? quote_form($value['title_e']) : '');
			$data['data'][$key]['start_date'] 	= iso_date($value['start_date']);
			$data['data'][$key]['end_date'] 	= iso_date($value['end_date']);
			$data['data'][$key]['dsp_video'] 	= $cat==2 || $cat == 5  ? 'hide' : '';
		}
		render('apps/gallery_video/records',$data,'blank');
	}
	function recordsgallery(){
		$id_gallery = $this->session->userdata('id_view');
		$data = $this->galleryDetailModel->records(array('id_gallery'=>$id_gallery));
		foreach ($data['data'] as $key => $value) {
			$data['data'][$key]['image'] 			= getImg($value['img']);
			$data['data'][$key]['title'] 		= $value['title'];
			$data['data'][$key]['description'] 	= $value['description'];
		}
		render('apps/gallery_video/recordsgallery',$data,'blank');
	}	
	
	function proses($idedit=''){
		$id_user 				= id_user();
		$this->layout 			= 'none';
		$post					= purify($this->input->post(NULL,TRUE));
		$ret['error']			= 1;
		$id_parent_lang 		= NULL;
		$this->db->trans_start(); 
		// $id_parent		= $this->languagemodel->langId();

		foreach ($post['name'] as $key => $value){
			if(!$idedit){
				$where['uri_path']			= $post['uri_path'][$key];
				$unik 	= $this->galleryModel->findBy($where);
				$this->form_validation->set_rules('name', '"page Name"', 'required'); 
				$this->form_validation->set_rules('youtube_url', '"Youtube Url"', 'required'); 
				// $this->form_validation->set_rules('description', '"Description"', 'required'); 
				// $this->form_validation->set_rules('seo_title', '"SEO Title"', 'required'); 
				// $this->form_validation->set_rules('meta_description', '"Meta Description"', 'required'); 
				// $this->form_validation->set_rules('meta_keyword', '"Meta Keyword"', 'required'); 
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
				
			$data_save['name']                = $post['name'][$key];
			$data_save['description']         = $post['description'][$key];
			$data_save['uri_path']            = $post['uri_path'][$key];
			$data_save['id_lang']             = $post['id_lang'][$key];
			$data_save['id_gallery_category'] = 4;
			$data_save['id_status_publish']   = $post['id_status_publish'][$key];
			$data_save['id_parent_lang']      = $id_parent_lang;
			$data_save['youtube_url']         = $post['youtube_url'][0];
			$data_save['publish_date']        = iso_date($post['publish_date'][0]);
			$data_save['seo_title']           = $post['seo_title'][$key];
			$data_save['meta_description']    = htmlspecialchars_decode(urldecode($post['meta_description'.$key.'']));
			$data_save['meta_keywords']       = $post['meta_keywords'][$key];
			if($post['imgDelete'][$key] == 0){
				if($post['img'][$key]){
					$data_save['img']				= $post['img'][$key];
				}
			} else{
				$data_save['img'] = NULL;
			}

			/*masukan data file*/
			if ($_FILES['file']['name'][$key]) {
					$data_save['filename']    = $_FILES['file']['name'][$key];
					/*echo pathinfo($file, PATHINFO_EXTENSION); exit();*/
					fileToUpload($_FILES['file'],$key);
			} else {
				unset($data_save['filename']);
			}
			/*masukan data file*/
			
			if($idedit){
				auth_update();
				$ret['message'] = 'Update Success';
				$act			= "Update Gallery Video";
				// if(!$post['img'][$key]){
				// 	unset($post['img'][$key]);
				// }
				$iddata 		= $this->galleryModel->update($data_save,$idedit);
			}else{
				auth_insert();
				$ret['message'] = 'Insert Success';
				$act			= "Insert Gallery Video";
				$iddata 		= $this->galleryModel->insert($data_save);
				$this->galleryImagesModel->updateByOther(array('id_gallery' => $iddata), array('id_gallery' => 0, 'id_lang' => $data_save['id_lang']));
				$idedit = $iddata;
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


			/*untuk tags*/
			$tags = $post['tags'][$post['id_lang'][$key]];
			$idGalleryTag = array();
			foreach ($tags as $k => $v) {
				$tag = strtolower($v);

				/*untuk mengecek apakah tag sudah ada apa belum*/
				$whr['name']      = $tag;
				$whr['is_delete'] = 0;
				$cek = $this->db->get_where('tags',$whr)->row_array();

				if(!$cek){
					$t['name']           = $tag;
					$t['uri_path']       = url_title($tag);
					$t['create_date']    = date('Y-m-d H:i:s');
					$t['user_id_create'] = id_user();
					$this->db->insert('tags',array_filter($t));
					$idTags = $this->db->insert_id();
				}
				else{
					$idTags = $cek['id'];
				}
				/*end untuk mengecek apakah tag sudah ada apa belum*/

				/*untuk mengecek apakah tag sudah ada di event ini */
				$whr2['a.id_gallery']   = $idedit;
				$whr2['a.id_tags']   = $idTags;
				$whr2['a.is_delete'] = 0;

									   $this->db->select('a.*,b.name as tags,b.uri_path');
									   $this->db->join('tags b','b.id = a.id_tags');
				$cek2 				 = $this->db->get_where('gallery_tags a',$whr2)->row_array();
				if(!$cek2){
					$newsTags['id_gallery']       = $idedit;
					$newsTags['id_tags']        = $idTags;
					$newsTags['create_date']    = date('Y-m-d H:i:s');
					$newsTags['user_id_create'] = id_user();
					$this->db->insert('gallery_tags',array_filter($newsTags));
				}
				/*untuk mengecek apakah tag sudah ada di event ini */

				/*mengambil id tags dalam event*/
				$idGalleryTag[]	 = $idTags;
			}

			if ($idGalleryTag && $idedit) {
								   $this->db->where('is_delete',0);
								   $this->db->where('id_gallery',$idedit);
								   $this->db->where_not_in('id_tags',$idGalleryTag);
				$deleteEventTags = $this->db->get('gallery_tags')->result_array();
				if ($deleteEventTags) {
					foreach ($deleteEventTags as $eventTag) {
						/*jika ingin langsung didelete*/
						$this->db->delete('gallery_tags',array('id'=>$eventTag['id']));
						// echo $this->db->last_query();exit();
						/*jika ingin memakai is_delete*/
						// $this->newsTagsModel->delete($newsTag['id']);
					}
				}
			}
			/*end untuk tags*/
		}
		echo json_encode($ret);
	}

	function uploadImages($idedit=''){
		$post = purify($this->input->post(NULL,TRUE));
		$data = array();

		$data_save['name']					= $post['nameImag'];
		$data_save['description']			= $post['descImag'];
		$data_save['id_lang']				= $post['id_lang'];
		$data_save['id_gallery']			= $post['id_gallery'];
		$tag_image         					= array_filter(explode(",", $post['tag_image']));
	
		if($post["statusImag"] == 0){
			foreach($_FILES as $index => $file){
				$uploadImag = multipleUpload($file, './images/gallery/', 20000000);
				
				if($uploadImag == true){
					
					$data_save['filename']				= $uploadImag['file_name'];

					auth_insert();
					$ret['message'] = 'Insert Success';
					$act			= "Insert Gallery Image";
					$iddata 		= $this->galleryImagesModel->insert($data_save);

					$data["idImag"] = $post["imag"];
					$data["statusImag"] = $post["statusImag"];

					
					$sort = 1;
					foreach ($tag_image as $key => $value) {
						$value = strtolower($tag_image[$key]);
						$cek = $this->tagsModel->fetchRow(array('name'=>$value));//liat tags name di tabel ref
						if(!$cek){//kalo belom ada
							$id_tags = $this->tagsModel->insert(array('name'=>$value,'uri_path'=>url_title($value)));//insert ke tabel ref
							detail_log();
						}
						else{
							$id_tags = $cek['id']; //kalo udah ada, tinggal ambil idnya
						}

						$cekTagsImage = $this->galleryTagsModel->fetchRow(array('id_tags'=>$id_tags,'id_images'=>$iddata)); //liat di tabel news tags, (utk edit)
					
						if(!$cekTagsNews){//kalo blm ada ya di insert
						$tag['id_gallery'] 	  = $data_save['id_gallery'];
						$tag['id_images']     = $iddata;
						$tag['id_tags']   	  = $id_tags;
						$id_news_tags = $this->galleryTagsModel->insert($tag);
						}
						else{//kalo udah ada, ambil id nya utk di simpen sbg array utk kebutuhan delete
						$tag['id_gallery'] 	  = $data_save['id_gallery'];
						$tag['id_images']     = $cekTagsImage['id_images'];
						$tag['id_tags']   	  = $id_tags;
						$id_news_tags = $this->galleryTagsModel->update($tag,$cekTagsNews['id']);
						}
						$temp_id[] 			=$id_tags;
					}

					$this->db->where_not_in('a.id_tags',$temp_id); 
					$delete = $this->galleryTagsModel->findBy(array('a.id_images'=> $iddata)); //dapetin id news tags yg diapus  (where id not in insert / select and id_news = $id_template)
					
					foreach ($delete as $key => $value) {
						$a['is_delete'] = 1;
						$b = $this->galleryTagsModel->update($a,$value['id']);
					}

				}
			}
		} else{
			$idedit = $post["idSavedImag"];
			
			auth_update();
			$ret['message'] = 'Update Success';
			$act			= "Update Gallery Image";
			$iddata 		= $this->galleryImagesModel->update($data_save,$idedit);
			$data["idImag"] = $post["imag"];
			$data["statusImag"] = $post["statusImag"];

					foreach ($tag_image as $key => $value) {
						$value = strtolower($tag_image[$key]);
						$cek = $this->tagsModel->fetchRow(array('name'=>$value));//liat tags name di tabel ref
						if(!$cek){//kalo belom ada
							$id_tags = $this->tagsModel->insert(array('name'=>$value,'uri_path'=>url_title($value)));//insert ke tabel ref
							detail_log();
						}
						else{
							$id_tags = $cek['id']; //kalo udah ada, tinggal ambil idnya
						}

						$cekTagsImage = $this->galleryTagsModel->fetchRow(array('id_tags'=>$id_tags,'id_images'=>$iddata)); //liat di tabel news tags, (utk edit)


					
						if(!$cekTagsImage){//kalo blm ada ya di insert
						$tag['id_gallery'] 	  = $data_save['id_gallery'];
						$tag['id_images']     = $iddata;
						$tag['id_tags']   	  = $id_tags;
						$id_news_tags = $this->galleryTagsModel->insert($tag);
						}
						else{//kalo udah ada, ambil id nya utk di simpen sbg array utk kebutuhan delete
						$tag['id_gallery'] 	  = $data_save['id_gallery'];
						$tag['id_images']     = $cekTagsImage['id_images'];
						$tag['id_tags']   	  = $id_tags;
						$id_news_tags = $this->galleryTagsModel->update($tag,$cekTagsNews['id']);
						}
						$temp_id[] 			=$id_tags;

					}

					$this->db->where_not_in('a.id_tags',$temp_id); 
					$delete = $this->galleryTagsModel->findBy(array('a.id_images'=> $iddata)); //dapetin id news tags yg diapus  (where id not in insert / select and id_news = $id_template)

					foreach ($delete as $key => $value) {
						$a['is_delete'] = 1;
						$b = $this->galleryTagsModel->update($a,$value['id']);
					}
		}

		// detail_log();
		// insert_log($act);
		$this->db->trans_complete();
		set_flash_session('message', $ret['message']);

		if($iddata != 0){
			$data["idSavedImag"] = $iddata;
			$data["status"] = true;
		} else{
			$data["idSavedImag"] = 0;
			$data["status"] = false;
		}

		$data["reloadImages"] = $this->show_images($post['id_gallery'], $post['key']);

		echo json_encode($data);
	}

	function addImages(){
		$post = purify($this->input->post(NULL,TRUE));

		$img_name = explode(',', $post['selectIdImg']);

		$data_save['id_lang']		= $post['id_lang'];
		$data_save['id_gallery']	= $post['id_gallery'];
		for($i=0; $i<count($img_name); $i++) {
			$data_save['filename']		= $img_name[$i];
			auth_insert();
			$ret['message'] = 'Insert Success';
			$act			= "Insert Gallery Image";
			$iddata 		= $this->galleryImagesModel->insert($data_save);
		}

		echo $this->show_images($post['id_gallery'], $post['key']);
	}

	function deleteImages(){
		$post = purify($this->input->post(NULL,TRUE));

		auth_delete();
		$this->galleryImagesModel->delete($post['idSavedImag']);
		// unlink('./images/gallery/'.$post['filename']);
		detail_log();
		insert_log("Delete Gallery Image");

		$data["idImag"] = $post["imag"];
		$data["status"] = true;
		$data["reloadImages"] = $this->show_images($post['id_gallery'], $post['key']);

		echo json_encode($data);
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
		render('apps/gallery_video/select_page',$data,'blank');

	}
	function record_select_page(){
		$data = $this->galleryModel->records();
		foreach ($data['data'] as $key => $value) {
			$data['data'][$key]['name'] = quote_form($value['name']);
		}
		render('apps/gallery_video/record_select_page',$data,'blank');
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
		render('apps/gallery_video/view_participant',$data,'apps');
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
			$data['proses']			= 'Save';
			$data['name_data']		= '';
			$data['uri_path']		= '';
			$data['description']	= '';
			$data['content']		= '';
			$data['start_date']		= '';
			$data['end_date']		= '';
			$data['speaker']		= '';
			$data['id'] 			= '';
			$data['youtube_url']	= '';
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
			$data['list_lang'][$key]['youtube_url']		= $datas[$key]['youtube_url'];
			$data['list_lang'][$key]['start_date']		= iso_date($datas[$key]['start_date']);
			$data['list_lang'][$key]['end_date']		= iso_date($datas[$key]['end_date']);

			$img_thumb											= image($datas[$key]['img'],'small');
			$imagemanager										= imagemanager('img',$img_thumb,320,180,$key);
			$data['list_lang'][$key]['img']						= $imagemanager['browse'];
			$data['list_lang'][$key]['imagemanager_config']		= $imagemanager['config'];
		}
		$gallery 			= $this->galleryModel->selectData($id_gallery,1);
		$data['name'] 		= $gallery['name'];
		$data['category_name'] 	= $gallery['category_name'];
		$data['id_gallery'] = $id_gallery;
		$data['idedit'] 	= $id;
		$data['list_lang2'] = $data['list_lang'];
		//$data['dsp_video']	= $gallery['id_gallery_category'] == 2 ? '' : 'hide';
		render('apps/gallery_video/addgallery',$data,'apps');
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
			// $data_save['youtube_url']	= $post['youtube_url'][0];
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