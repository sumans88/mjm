<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class News extends CI_Controller {
	function __construct(){
		parent::__construct();
		$this->load->model('newsmodel');
		$this->load->model('newsversionmodel');
		$this->load->model('newscategorymodel');
		$this->load->model('newstagsmodel');
		$this->load->model('newsImagesModel');
		$this->load->model('newstagsversionmodel');
		$this->load->model('newsFilesModel');
		$this->load->model('tagsmodel');
		$this->load->model('newsapprovalcommentmodel');
		$this->load->model('languagemodel');
		$this->load->model('authgroup_model','authGroupModel');
		$this->load->model('model_user','userModel');
		$this->load->model('galleryImagesModel');
		$this->load->model('galleryModel');
		$this->load->model('gallerytagsmodel');

		$this->sess = $this->session->userdata('ADM_SESS');
	}
	function index(){
		$id_lang = default_lang_id();
		$data['list_news_category']    = selectNewsCat(array('table'=>'news_category','where'=> array('is_hide'=>0,'id_lang'=>$id_lang,'is_delete'=>0),'title'=>'Select Category'));
		$data['list_status_publish']   = selectlist2(array('table'=>'status_publish','title'=>'All Status','selected'=>$data['id_status_publish']));
		$data['calendar_days_number']  = calendar_days_number('cari form-control');
		$data['calendar_months_word']  = calendar_months_word('cari form-control');
		$data['calendar_years_number'] = calendar_years_number('cari form-control');
		$data['is_publisher']          = (group_id() == 4 or group_id() == 5 or group_id() == 1) ? '' : 'invis';

		if ($this->sess['admin_id_auth_user_group'] == 2) {
			$data['hide_data'] 	= 'hide';
		}
		else{
			$data['hide_data']		= '';
		}
		render('apps/news/index',$data,'apps');
	}
	public function add($id=''){
		if($id){
			// $data = $this->newsmodel->findById($id);
			$datas 		= $this->newsmodel->selectData($id);
			$id_gallery = get_news_gallery_id($id)?get_news_gallery_id($id):0; 
			if (empty($id_gallery)) {
				$ins_data                         = $this->newsmodel->selectData($id,1);
				$ins_datas['name']                = $ins_data['news_title'] ;
				$ins_datas['description']         = $ins_data['teaser'] ;
				$ins_datas['uri_path']            = $ins_data['uri_path'] ;
				$ins_datas['id_lang']             = $ins_data['id_lang'] ;
				$ins_datas['img']                 = $ins_data['img'] ;
				// $ins_datas['filename']            = $ins_data['filename'] ;
				$ins_datas['id_gallery_category'] = 3 ;
				$ins_datas['id_category_item']    = $id ;
				$ins_datas['youtube_url']         = $ins_data['youtube_url'] ;
				$ins_datas['seo_title']           = $ins_data['seo_title'] ;
				$ins_datas['meta_description']    = $ins_data['meta_description'] ;
				$ins_datas['meta_keywords']       = $ins_data['meta_keywords']    ;
				$id_gallery                       = $this->galleryModel->insert($ins_datas);
				
				$data_g                           = $this->galleryModel->selectData($id_gallery,1);
				$id_gallery			  = $data_g['id'];
				$datas[0]['gallery_images']		  = $this->show_images($data_g['id'], $key);
			}else{
				$data_g                           = $this->galleryModel->selectData($id_gallery,1);
				$id_gallery			  =  $data_g['id'];
				$datas[0]['gallery_images']		  = $this->show_images($data_g['id'], $key);
			}


			if(!$datas){
				die('404');
			}
			$data                             = quote_form($datas);
			$data['judul']                    = 'Edit';
			$data['proses']                   = 'Update';
			$data['publish_date']             =  iso_date_custom_format($post['publish_date'][$key],'d-m-Y H:i');
			$data['year']             		  = $datas[0]['year'] == '0000' ? "" : $datas[0]['year'];
			$data['expected_publish_date']    = iso_date($data['expected_publish_date']);
			
			$data_version                     = $this->newsversionmodel->findByrecordversion(array('id_news'=>$id));
			$data['last_edited']              = "by <b>$data_version[username]</b>" . ' on ' . iso_date_time($data_version['create_date']);
			$data['edit_button']              =  ($datas[0]['approval_level'] == 100) ? 'invis' : '';
			$data['last_edited_show']         = '';
			$data['id']                       = $id;			
			$data['invis_file_tampil']        = $datas[0]['filename'] ? '' : 'hide';

		}else{
            $this->newsFilesModel->updateByOther(array('is_delete' => 1), array('id_news' => 0));
			$id                               = 0;
			$data['last_edited']              = '';
			$data['last_edited_show']         = 'invis';
			$data['judul']                    = 'Add';
			$data['proses']                   = 'Save';
			$data['news_title']               = '';
			$data['link_youtube_video']       = '';
			$data['uri_path']                 = '';
			$data['teaser']                   = '';
			$data['mailchimp']                = '';
			$data['page_content']             = '';
			$data['publish_date']    		  = date('d-m-Y');
			$data['year']                     = '';
			$data['tags']                     = '';
			$data['id']                       = '';
			$data['id_news']                  = '';
			$data['is_featured']              = '';
			$data['is_experts']               = '';
			$data['sort']                     = '';
			$data['id_lang']                  = '';
			$data['is_qa']                    = '';
			$data['seo_title']                = '';
			$data['meta_description']         = '';
			$data['meta_keywords']            = '';
			$data['is_not_available']         = '';
			$data['is_rssfeed']               = 'checked';
			$data['footnote']                 = '';
			$ins_datas['id_gallery_category'] = 3 ;
			$ins_datas['id_category_item']    = 0 ;
			$datas[0]['writer']         	  = '';
			$datas[0]['id_gallery']			  = 0 ;
			$data['invis_file_tampil']        = 'hide';
		}


		$tags_data = $this->newstagsmodel->records_tags_all();
		$data['tags'] = generate_tags($tags_data,'name');

		/*untuk menu tags*/
		$this->db->where_in('id',array("119","137","141","145","153","159","179","180"));
		// $this->db->group_by('extra_param');
		$this->db->order_by('name','asc');
		// $this->db->where("extra_param != '' AND extra_param != '-'");
		$tags_menu = $this->db->get_where('frontend_menu',array('is_delete'=>0, 'id_status_publish'=>2, 'name !='=>'', 'id_language'=>1))->result_array();
		$data['menu_tags'] = generate_tags($tags_menu,'name');
		/*end untuk menu tags*/

		$data['list_lang']	= $this->languagemodel->langName();
		$n = 0;
		$detail_images = $this->newsImagesModel->findBy(array('id_news'=>$datas[0]['id'],'is_delete'=>0));
		if(!empty($detail_images)){

			// $img_detail = array_filter(explode(",", $datas[0]['detail_img']));
			$is_img_share = 1 ;
			foreach ($detail_images as $key_detail => $value_detail){
				$img_thumb 			= ($value_detail['filename']=='') ? image('no_image.png','small') : image($value_detail['filename'],'small');
				$imagemanager 		= imagemanager('detail_img',$img_thumb,450,260,'_'.$key_detail,'[]',$img_detail[$key_detail]);

				$is_checked   = ($value_detail['is_share'] == 1) ? 'checked' : '';
				$is_check     = ($value_detail['is_share'] == 1) ? 1 : 0;
				
				$is_img_share = ($is_check == 1) ? 0 :$is_img_share;
				
				$data['list_lang'][0]['images_detail'][$key_detail]['no_img_detail']            = $n++;
				$data['list_lang'][0]['images_detail'][$key_detail]['id_disclaim']              = $n;

				$data['list_lang'][0]['images_detail'][$key_detail]['id_images_detail']         = $value_detail['id'];
				$data['list_lang'][0]['images_detail'][$key_detail]['is_share_checked']         = $is_checked;
				$data['list_lang'][0]['images_detail'][$key_detail]['is_share']                 = $is_check;
				$data['list_lang'][0]['images_detail'][$key_detail]['is_share']                 = $is_check;
				$data['list_lang'][0]['images_detail'][$key_detail]['form_images_detail']       = $imagemanager['browse'];
				$data['list_lang'][0]['images_detail'][$key_detail]['description_images_detail']    = $value_detail['description'];
				
				$data['list_lang'][0]['images_detail'][$key_detail]['form_images_detail_label'] = $key_detail == 0?'Image Detail':'';
				$data['list_lang'][0]['images_detail'][$key_detail]['invis_del_img']            = $key_detail == 0 ?'del-img-detail-first':'';
			}
			// $img_thumb               = image($data['img'],'small');
			$imagemanager               = imagemanager('detail_img',image('','small'),450,260,'','[]','');
			$data['form_images_detail'] = $imagemanager['browse'];
		}else{
			$data['list_lang'][0]['invis_del_img']                                 = 'del-img-detail-first';
			$data['list_lang'][0]['images_detail'][0]['form_images_detail_label']  = 'Image Detail';
			$data['list_lang'][0]['images_detail'][0]['id_images_detail']          = '';
			$data['list_lang'][0]['images_detail'][0]['no_img_detail']             = $n++;
			$data['list_lang'][0]['images_detail'][0]['description_images_detail'] = '';
			$data['list_lang'][0]['images_detail'][0]['is_share_checked']          = '';
			$data['list_lang'][0]['images_detail'][0]['is_share']                  = 0;
			$data['list_lang'][0]['images_detail'][0]['id_disclaim']               = '';

			$img_thumb						= image('','small');
			$imagemanager					= imagemanager('detail_img',$img_thumb,450,260,$key_detail,'[]','');
			$data['form_images_detail'] 	= $imagemanager['browse'];
		}

		$id_lang_default	= $this->languagemodel->langId();
		foreach ($data['list_lang'] as $key => $value){
			$data['list_lang'][$key]['publish_date']             = date('d-m-Y');
			$data['list_lang'][$key]['invis']                    = ($key==0) ? '' : 'hide';
			$data['list_lang'][$key]['active']                   = ($key==0) ? 'active in' : '';
			$data['list_lang'][$key]['validation']               = ($key==0) ? 'true' : 'false';
			$data['list_lang'][$key]['nomor']                    = $key;
			$data['list_lang'][$key]['show_thumb']               = $datas[$key]['thumb'] ? 1 : '';
			
			$data['list_lang'][$key]['is_experts_checked']       = ($datas[$key]['is_experts'] == 1) ? 'checked' : '';
			$data['list_lang'][$key]['sort_checked']             = ($datas[$key]['sort'] == 1) ? 'checked' : '';
			$data['list_lang'][$key]['is_qa_checked']            = ($datas[$key]['is_qa'] == 1) ? 'checked' : '';
			$data['list_lang'][$key]['is_featured_checked']      = ($datas[$key]['is_featured'] == 1) ? 'checked' : '';
			$data['list_lang'][$key]['is_editor_choice_checked'] = ($datas[$key]['is_editor_choice'] == 1) ? 'checked' : '';
			
			$data['list_lang'][$key]['id_news_category']         = $datas[$key]['id_news_category'];
			$data['list_lang'][$key]['news_title']               = quote_form($datas[$key]['news_title']);
			$data['list_lang'][$key]['mailchimp']                = $datas[$key]['mailchimp'];
			$data['list_lang'][$key]['uri_path']                 = $datas[$key]['uri_path'];
			$data['list_lang'][$key]['link_youtube_video']       = $datas[$key]['link_youtube_video'];
			$data['list_lang'][$key]['publish_date2']            = iso_date_custom_format($datas[$key]['publish_date'],'d-m-Y H:i');
			$data['list_lang'][$key]['expected_publish_date']    = $datas[$key]['expected_publish_date'];	
			$data['list_lang'][$key]['id_status_publish']        = $datas[$key]['id_status_publish'];
			$data['list_lang'][$key]['tags']                     = $datas[$key]['tags'];
			$data['list_lang'][$key]['teaser']                   = $datas[$key]['teaser'];
			$data['list_lang'][$key]['page_content']             = $datas[$key]['page_content'];
			$data['list_lang'][$key]['writer']             	 	 = $datas[$key]['writer'];
			$data['list_lang'][$key]['filename']                 = $datas[$key]['filename'];
			$data['list_lang'][$key]['seo_title']                = $datas[$key]['seo_title'];
			$data['list_lang'][$key]['meta_description']         = $datas[$key]['meta_description'];
			$data['list_lang'][$key]['meta_keywords']            = $datas[$key]['meta_keywords'];
			$data['list_lang'][$key]['id_news']                  = $datas[$key]['id'];
			$data['list_lang'][$key]['is_file_member']           = $datas[$key]['is_file_member'];
			$data['list_lang'][$key]['is_not_available']         = $datas[$key]['is_not_available'] ? 'checked' : '';
			$data['list_lang'][$key]['is_rssfeed']               = $datas[$key]['is_rssfeed'] ? 'checked' : '';
			$data['list_lang'][$key]['id_gallery']	             = $id_gallery;
			$data['list_lang'][$key]['gallery_images']      	 = $this->show_images($id_gallery, $key);
			$data['list_lang'][$key]['list_gallery_images'] 	 = $datas[0]['gallery_images'];

			$data['list_lang'][$key]['gallery_album']  	  	  	 = $datas[$key]['id_gallery'] ?$datas[$key]['id_gallery'] : "";
			
			$data['list_lang'][$key]['edit_status_publish']      = (group_id() == 4 and $datas[$key]['approval_level']==100) ? '' : 'invis';
		
			$data['list_lang'][$key]['list_news_category'] 	   	 = selectNewsCat(array('table'=>'news_category','where'=> array('is_hide'=>0,'id_lang'=>$value['lang_id'],'is_delete'=>0),'title'=>'Select Category','selected'=>$datas[$key]['id_news_category']));
			
			$data['list_lang'][$key]['list_status_publish'] 	 = selectlist2(array('table'=>'status_publish','title'=>'Select Status','selected'=>$datas[$key]['id_status_publish']));
			$data['list_lang'][$key]['list_language'] 			 = selectlist2(array('table'=>'language','title'=>'Select Language','selected'=>'1'));
			$data['list_lang'][$key]['list_tags'] 				 = selectlist2(array('table'=>'tags','title'=>'','where'=> array('is_delete'=>'0')));
			
			
			$img_thumb											 = image($datas[$key]['img'],'small');
			$imagemanager										 = imagemanager('img',$img_thumb,450,260,$key,$datas[$key]['news_title'],'');
			$data['list_lang'][$key]['img']						 = $imagemanager['browse'];
			$data['list_lang'][$key]['imagemanager_config']		 = $imagemanager['config'];

			$tags = $this->newstagsmodel->findBy(array('id_news'=>$datas[$key]['id']));
			$data['list_lang'][$key]['tags_data'] 				= generate_tags($tags,'tags');

			/*untuk menu tag*/
					$this->db->select('a.*,b.name as tags,b.extra_param as extra_param');
					$this->db->join('frontend_menu b','b.id = a.id_menu');
			$menutags = $this->db->get_where('news_menu_tags a',array('id_news'=>$datas[$key]['id']))->result_array();
			$data['list_lang'][$key]['menutags_data'] 				= generate_tags($menutags,'tags');

			/*end untuk menu tag*/

			// $filemanager = filemanager($key+1,$datas[$key]['filename']);
			// $data['list_lang'][$key]['file_upload']				= $filemanager['browse'];
			$data['list_lang'][$key]['event_files'] = $this->show_files($datas[$key]['id'], $key);
			$data['list_lang'][$key]['id'] = $datas[$key]['id'];
			// echo '<br>';
		}

		$data['filemanager_config'] 	= $filemanager['config'];

		$next_approval                      = $this->newsmodel->approvalLevelGroup + 1;
		$group                              = $this->authGroupModel->fetchRow(array('approval_level'=>$next_approval));
		$data['next_approval']              = $group['grup'] ? "&amp; Sent to $group[grup]" : '&amp; Publish';
		$data['enable_edit_status_publish'] = is_edit_publish_status();
		$data['disable_edit']               = is_edit_news($id,$datas[0]['user_id_create'],$datas[0]['approval_level'],'return') ? '' : 'invis';
		$data['enable_edit_editors_choice'] = enable_edit_editors_choice();
		$data['list_lang2'] = $data['list_lang'];

		$data['grup_enable'] = ($data['enable_edit_status_publish'] == 'hide') ? 'not' : 'open';
		foreach ($data['list_lang'] as $key => $value) {
			$data['list_lang'][$key]['bahasa']     = ucwords($value['lang_name']);
			
			$data['list_lang2'][$key]['lang_name'] = ucwords($value['lang_name']);
		}
		$this->db->group_by('filename');
		$this->db->limit(10);
		$allImages = $this->db->select("id, filename")->get_where('gallery_images', array('is_delete'=>0, 'filename !='=>''))->result_array();

		$data['gallery_images_modal']	= get_gallery_images("gallery/add_modal.html", $allImages, 0);
		$data['multiple_image_script']	= get_gallery_images("gallery/add_multiple_image.html", '', 0);
		$data['multiple_file_script']   = get_event_files("news/add_multiple_file.html", '', 0);
		// print_r($data['id_gallery']);exit;
		render('apps/news/add',$data,'apps');
	}
	function show_files($idnews, $key)
	{
		$allFiles = array();

		if ($key == 0) {
			$id_lang = 1;
		} else {
			$id_lang = 2;
		}
		$wh['is_delete']    = 0;
		$wh['id_news']     = $idnews;
		$wh['id_lang']      = $id_lang;
		$allFiles['nomorFile']          = $key;
		$allFiles['list_files'] = array();
		$listFiles = $this->newsFilesModel->listFiles($wh);
		foreach ($listFiles as $key => $value) {
			$ext = pathinfo($value['filename'], PATHINFO_EXTENSION);
			if ($ext == "pdf") {
				$listFiles[$key]['imageFile'] = "file-pdf.jpg";
			}
			if ($ext == "xls" | $ext == "xlsx") {
				$listFiles[$key]['imageFile'] = "file-excel.jpg";
			}
			if ($ext == "doc" | $ext == "docx") {
				$listFiles[$key]['imageFile'] = "file-word.jpg";
			}
			if ($ext == "ppt" | $ext == "pptx") {
				$listFiles[$key]['imageFile'] = "file-ppt.jpg";
			}
		}
		$allFiles['list_files'] = $listFiles;

		$allFiles['jmlFiles']       = count($allFiles['list_files']);
		$allFiles['showUploadAll']  = (count($allFiles['list_files']) != NULL ? 'block' : 'none');

		return get_event_files("news/list_files.html", $allFiles, 1);
	}
	function uploadFiles($idedit = '')
	{
		$post = purify($this->input->post(NULL, TRUE));
		$data = array();

		$data_save['name']          = $post['nameFile'];
		$id_news                   = $post['id_news'];
		$id_lang                    = $post['id_lang'];
		if ($post['id_lang'] == 1) {
			$getID = $this->db->select('id')->get_where('news', array('id_parent_lang' => $post['id_news'], 'is_delete' => 0))->row_array();
			$id_news_new = $getID['id'];
			$id_lang_new = 2;
		} else {
			$getID = $this->db->select('id_parent_lang')->get_where('news', array('id' => $post['id_news'], 'is_delete' => 0))->row_array();
			$id_news_new = $getID['id_parent_lang'];
			$id_lang_new = 1;
		}
		if ($post["statusFile"] == 0) {
			foreach ($_FILES as $index => $file) {
				$uploadFile = multipleUpload($file, './document/material/', 20000000);

				if ($uploadFile == true) {

					$data_save['filename']  = $uploadFile['file_name'];

					if ($id_lang == 1) {
						auth_insert();
						$data_save['id_news']  = $id_news;
						$data_save['id_lang']   = $id_lang;
						$ret['message'] = 'Insert Success';
						$act            = "Insert news File";
						$iddata         = $this->newsFilesModel->insert($data_save);
					} else {
						auth_insert();
						$data_save['id_news']  = $id_news_new;
						$data_save['id_lang']   = $id_lang_new;
						$ret['message'] = 'Insert Success';
						$act            = "Insert news File";
						$iddata         = $this->newsFilesModel->insert($data_save);
					}

					$data_save['id_parent_lang']    = $iddata;

					if ($id_lang == 1) {
						auth_insert();
						$data_save['id_news']  = $id_news_new;
						$data_save['id_lang']   = $id_lang_new;
						$ret['message'] = 'Insert Success';
						$act            = "Insert news File";
						$iddata         = $this->newsFilesModel->insert($data_save);
					} else {
						auth_insert();
						$data_save['id_news']  = $id_news;
						$data_save['id_lang']   = $id_lang;
						$ret['message'] = 'Insert Success';
						$act            = "Insert news File";
						$iddata         = $this->newsFilesModel->insert($data_save);
					}

					$data["idFile"] = $post["imag"];
					$data["statusFile"] = $post["statusFile"];
				}
			}
		} else {
			$idedit = $post["idSavedFile"];

			auth_update();
			$ret['message'] = 'Update Success';
			$act            = "Update news File";
			$iddata         = $this->newsFilesModel->updateAll($data_save, $idedit);
			$data["idFile"] = $post["imag"];
			$data["statusFile"] = $post["statusFile"];
		}

		detail_log();
		insert_log($act);
		$this->db->trans_complete();
		set_flash_session('message', $ret['message']);

		if ($iddata != 0) {
			$data["idSavedFile"] = $iddata;
			$data["status"] = true;
		} else {
			$data["idSavedFile"] = 0;
			$data["status"] = false;
		}

		$data["reloadFiles"] = $this->show_files($post['id_news'], $post['key']);

		echo json_encode($data);
	}

	function deleteFile()
	{
		$post = purify($this->input->post(NULL, TRUE));

		if ($key == 0) {
			$getID = $this->db->select('id')->get_where('news_files', array('id_parent_lang' => $post['idSavedFile'], 'is_delete' => 0))->row_array();
			$id_news_new = $getID['id'];
		} else {
			$getID = $this->db->select('id_parent_lang')->get_where('news_files', array('id' => $post['idSavedFile'], 'is_delete' => 0))->row_array();
			$id_news_new = $getID['id_parent_lang'];
		}

		auth_delete();
		$this->newsFilesModel->delete($post['idSavedFile']);
		// unlink('./document/material/'.$post['filename']);
		detail_log();
		insert_log("Delete news File");

		auth_delete();
		$this->newsFilesModel->delete($id_news_new);
		// unlink('./document/material/'.$post['filename']);
		detail_log();
		insert_log("Delete news File");

		$data["idFile"] = $post["imag"];
		$data["status"] = true;
		$data["reloadFiles"] = $this->show_files($post['id_news'], $post['key']);

		echo json_encode($data);
	}

	public function view($id=''){
		if($id){
			$datas 	= $this->newsmodel->selectData($id);
			
			if(!$datas){
				die('404');
			}

			$data['list_lang']	= $this->languagemodel->langName();
			foreach ($data['list_lang'] as $key => $value){
				$data['list_lang'][$key]['invis']            = ($key==0) ? '' : 'hide';
				$data['list_lang'][$key]['active']           = ($key==0) ? 'active in' : '';
				$data['list_lang'][$key]['validation']       = ($key==0) ? 'true' : 'false';
				$data['list_lang'][$key]['nomor']            = $key;
				$data['list_lang'][$key]['dsp_publish_date'] = group_id() == 4 ? '' : 'invis';
				
				$data['list_lang'][$key]['news_title']       = $datas[$key]['news_title'];
				$data['list_lang'][$key]['tags']             = $datas[$key]['tags'];
				$data['list_lang'][$key]['teaser']           = $datas[$key]['teaser'];
				$data['list_lang'][$key]['page_content']     = $datas[$key]['page_content'];
				
				$data['list_lang'][$key]['img_thumb']        = image($datas[$key]['img'],'small');
				$data['list_lang'][$key]['img_ori']          = image($datas[$key]['img'],'large');

				$data['list_lang'][$key]['img_news_class'] 				= '';
				$data['list_lang'][$key]['img_news_class_button'] 		= 'hide';
				
				if($data['list_lang'][$key]['link_youtube_video']){
					$data['list_lang'][$key]['link_youtube_video'] 		= str_replace("watch?v=","embed/",$datas[$key]['link_youtube_video']);
					$data['list_lang'][$key]['img_news_class'] 			= 'img-news-with-video';
					$data['list_lang'][$key]['img_news_class_button'] 	= 'img-news-class-button';
				}
				$data['list_lang'][$key]['teaser']		 				= remove_html_tag_news($datas[$key]['teaser']);
				$data['list_lang'][$key]['create_date'] 				= iso_date_time($datas[$key]['create_date']);
			
				$data['list_lang'][$key]['footnote'] 					= urldecode(htmlspecialchars_decode(remove_html_tag_news($datas[$key]['footnote'])));
				$data['list_lang'][$key]['disable_edit'] 			= is_edit_news($id,$datas[$key]['user_id_create'],$datas[$key]['approval_level'],'return') ? '' : 'invis';

				$data['list_lang'][$key]['tags'] 						= substr($tag,1);
				$data['list_lang'][$key]['disabled_form'] 				= '';
				$data['list_lang'][$key]['hidden_form'] 				= '';
				$user_sess_data = $this->session->userdata('ADM_SESS');
				if(!$user_sess_data){
					$data['list_lang'][$key]['disabled_form'] 			= 'disabled';
				} else {
					$data['list_lang'][$key]['hidden_form'] 			= 'hidden';	
				}
				$data['list_lang'][$key]['publish_date'] 				= iso_date($datas[$key]['publish_date']);
			}
		}
		$data['list_lang2'] 			= $data['list_lang'];

        
		render('apps/news/view',$data,'apps');
	}
	function records(){
		$data = $this->newsmodel->records();
		// echo $this->db->last_query();
		foreach ($data['data'] as $key => $value) {
			$approval_level_news = $value['approval_level'];
			$approval_level_news_e = $value['approval_level_e'];
			$group = $this->authGroupModel->fetchRow(array('approval_level'=>$approval_level_news));
			$group_e = $this->authGroupModel->fetchRow(array('approval_level'=>$approval_level_news_e));
			// $approval = $approval_level_news == 0 ? 'Draft' : ('Sent to '.$group['grup']);
			if(($approval_level_news == 0 && $value['is_revise']== 1) || ($approval_level_news_e == 0 && $value['is_revise_e']== 1)){
				$approval = 'Revise (writter)';
			}
			else if(($approval_level_news == 1 && $value['is_revise']== 1) || ($approval_level_news_e == 1 && $value['is_revise_e']== 1)){
				$approval = 'Revise (editor)';
			}
			else if(($this->newsmodel->approvalLevelGroup == $approval_level_news && $approval_level_news != 0) || ($this->newsmodel->approvalLevelGroup == $approval_level_news_e && $approval_level_news_e != 0)){
				$approval = '<a class="btn btn-primary" href="'.$this->currentController.'view/'.$value['id'].'">Review</>';
			}
			else if($approval_level_news == 100 || $approval_level_news_e == 100){
				$approval = 'Approved';
			}
			else if($approval_level_news == 0 || $approval_level_news_e == 0){
				$approval = 'Draft';
			}
			else{
				$approval = 'Sent to '.$group['grup'];
			}
			if($value['id_status_publish'] == 2 || $value['id_status_publish_e'] == 2){
				$data['data'][$key]['status'] = "Publish";
			} else{
				$data['data'][$key]['status'] = "Unpublish";
			}
			$data['data'][$key]['is_publisher'] = (group_id() == 4 or group_id() == 5 or group_id() == 1) ? '' : 'invis';
			$data['data'][$key]['news_title'] 		= quote_form($value['news_title']);
			$data['data'][$key]['news_title_e'] 		= quote_form($value['title_e']);
			$data['data'][$key]['publish_date'] 	= iso_date($value['publish_date']);
			$data['data'][$key]['expected_publish_date'] 	= iso_date($value['expected_publish_date']);
			$data['data'][$key]['modify_date'] 	= iso_date($value['modify_date']);
			$data['data'][$key]['approval_level'] 	= $approval;
			//$data['data'][$key]['edit'] 		 	= is_edit_news($value['id'],$value['user_id_create'],$approval_level_news,'delete');
			//$data['data'][$key]['delete'] 		 	= is_delete_news($value['id'],$value['user_id_create'],$approval_level_news);

			/*get Hits and download counter*/
			$data['data'][$key]['hits_id'] = $value['hits'];
			$hits_en = db_get_one('news','hits',array('id_parent_lang'=>$value['id']));
			$data['data'][$key]['hits_en'] = ($hits_en) ? $hits_en : 0 ;

			$data['data'][$key]['down_id'] = $value['hits_download'];
			$down_en = db_get_one('news','hits_download',array('id_parent_lang'=>$value['id']));
			$data['data'][$key]['down_en'] = ($down_en) ? $down_en : 0 ;
			/*endget Hits and download counter*/
		}
		if ($this->sess['admin_id_auth_user_group'] == 2) {
			$data['hide_data'] 	= 'hide';
		}
		else{
			$data['hide_data']		= '';
		}
		render('apps/news/records_news',$data,'blank');
	}
	public function download(){
		$post       = purify($this->input->post(NULL,TRUE));
		$start_date = iso_date($post['start_date']);
		$end_date   = iso_date($post['end_date']);
		$where      = '';

		unset($post['start_date'],$post['end_date']);
		
		if ($start_date || $end_date) {
			if ($start_date && $end_date) {
				// $where = "a.publish_date BETWEEN '$start_date' AND '$end_date'";
				$where = "a.publish_date >= '$start_date' AND a.publish_date <= '$end_date'";
			} elseif ($start_date) {
				$where = "a.publish_date = '$start_date'";
			} elseif ($end_date) {
				$where = "a.publish_date = '$end_date'";
			}
		}

		$data['data'] = $this->newsmodel->download($where);
		// print_r($data['data']);
		// exit;
		$nomor = 1;

		foreach ($data['data'] as $key => $value) {
			$approval_level_news = $value['approval_level'];
			$group = $this->authGroupModel->fetchRow(array('approval_level'=>$approval_level_news));
			// $approval = $approval_level_news == 0 ? 'Draft' : ('Sent to '.$group['grup']);
			if($approval_level_news == 0 && $value['is_revise']== 1){
				$approval = 'Revise (writter)';
			}
			else if($approval_level_news == 1 && $value['is_revise']== 1){
				$approval = 'Revise (editor)';
			}
			else if($this->newsmodel->approvalLevelGroup == $approval_level_news && $approval_level_news != 0){
				$approval = '<a class="btn btn-primary" href="'.$this->currentController.'view/'.$value['id'].'">Review</>';
			}
			else if($approval_level_news == 100){
				$approval = 'Approved';
			}
			else if($approval_level_news == 0){
				$approval = 'Draft';
			}
			else{
				$approval = 'Sent to '.$group['grup'];
			}
			$data['data'][$key]['is_publisher'] = (group_id() == 4 or group_id() == 5 or group_id() == 1) ? '' : 'invis';
			$data['data'][$key]['news_title'] 		= quote_form($value['news_title']);
			$data['data'][$key]['news_title_e'] 		= quote_form($value['title_e']);
			$data['data'][$key]['publish_date'] 	= iso_date($value['publish_date']);
			$data['data'][$key]['expected_publish_date'] 	= iso_date($value['expected_publish_date']);
			$data['data'][$key]['modify_date'] 	= iso_date($value['modify_date']);
			$data['data'][$key]['approval_level'] 	= $approval;
			$data['data'][$key]['edit'] 		 	= is_edit_news($value['id'],$value['user_id_create'],$approval_level_news,'delete');
			$data['data'][$key]['delete'] 		 	= is_delete_news($value['id'],$value['user_id_create'],$approval_level_news);

			/*get Hits and download counter*/
			$data['data'][$key]['hits_id'] = $value['hits'];
			$hits_en = db_get_one('news','hits',array('id_parent_lang'=>$value['id']));
			$data['data'][$key]['hits_en'] = ($hits_en) ? $hits_en : 0 ;
			
			$data['data'][$key]['down_id'] = $value['hits_download'];
			$down_en = db_get_one('news','hits_download',array('id_parent_lang'=>$value['id']));
			$data['data'][$key]['down_en'] = ($down_en) ? $down_en : 0 ;
			/*end get Hits download counter*/
			$data['data'][$key]['nomor'] = $nomor;
			$nomor++;
		}
		if ($this->sess['admin_id_auth_user_group'] == 2) {
			$data['hide_data'] 	= 'hide';
		}
		else{
			$data['hide_data']		= '';
		}

		
		html_to_excel($this->parser->parse('apps/news/export.html',$data,true),'list-news');
		// render('apps/news/export',$data,'blank');
		// $filename = 'list-news.csv';
		// header("Content-Type: text/csv; charset=utf-8");
		// header('Content-Disposition: attachment; filename='.$filename);
	}
	function get_comments_data($id_news){
		$data['comment_data'] = getcomments($id_news);
		$data['id_news'] = $id_news;
		echo json_encode($data);
	}
	function records_version($id_news){
		$data = $this->newsversionmodel->records(array('id_news'=>$id_news));
		foreach ($data['data'] as $key => $value) {
			$approval_level_news = $value['approval_level'];
			$group = $this->authGroupModel->fetchRow(array('approval_level'=>$approval_level_news));

			$user_creator = $this->userModel->findById($value['user_id_create']);
			$level_user_creator = $this->authGroupModel->findById($user_creator['id_auth_user_grup']);



			if($approval_level_news == 100){ ## approved
				$approval = "approved and published by $value[username]";
			}
			else if($approval_level_news == $level_user_creator['approval_level']){
				$approval = "$value[username] update content";
			}
			else{
				$approval = "$value[username] update content and Sent to $group[grup]";
			}

			$data['data'][$key]['news_title'] 	= quote_form($value['news_title']);
			$data['data'][$key]['note'] 		= $approval;
			$data['data'][$key]['create_date']	= iso_date_time($value['create_date']);
		}
		render('apps/news/records_version',$data,'blank');
	}	
	
	function proses($idedit=''){
		$id_user 				= id_user();
		$this->layout 			= 'none';
		$post					= purify($this->input->post());
		$ret['error']			= 1;
		$id_parent_lang 		= NULL;
		$this->db->trans_start();
		
		foreach ($post['news_title'] as $key => $value){
			$id_newsx = $post['id_news'][$key];
			if(!$idedit){
				$where['a.uri_path']	= $post['uri_path'][$key];
				$unik 					= $this->newsmodel->findBy($where);
				$this->form_validation->set_rules('id_news_category', '"Category"', 'required'); 
				$this->form_validation->set_rules('news_title', '"Title"', 'required'); 
				$this->form_validation->set_rules('uri_path', '"Page URL"', 'required');
				if ($this->form_validation->run() == FALSE){
					$ret['message']  = validation_errors(' ',' ');
				}else if($unik){
					$ret['message']	= "Page URL $value already taken";
					$ret['error']	= 1;
					echo json_encode($ret);
					return;
				}
			}
			// $where['a.uri_path']	= $post['uri_path'][$key];
			// $unik 					= $this->newsmodel->findBy($where);
			// if($unik){
			// 	$ret['message']	= "Page URL $value already taken";
			// 	$ret['error']	= 1;
			// 	echo json_encode($ret);
			// 	return;
			// }
			// unset($where['a.uri_path']);

			$data_save['teaser'] 				= remove_html_tag_news($post['teaser'][$key]);
			$data_save['footnote']				= remove_html_tag_news($post['footnote'][$key]);
			
			if($key==0){
				if($idedit){
					$data_save['sort']				= ($post['sort'][$key] != 1) ? 0 : 1;
				}

				// $idedit				= $post['id'][$key];
			 	$sort 				= $post['sort'][$key];
				
				$tags 				= $post['tags'][$key];

				if ($post['is_not_available'][$post['id_lang'][$key]]) {
					$whId['id']       = $post['id_news_category'][1];
					$whId['id_lang']  = $post['id_lang'][1];
					$id_news_category = db_get_one('news_category','id_parent_lang',$whId);

					$publish_date		= $post['publish_date'][1];
				 	$id_status 			= 1;

				} else {
					$id_news_category 	= $post['id_news_category'][$key];
				 	$publish_date		= $post['publish_date'][$key];
				 	$id_status 			= $post['id_status_publish'][$key];
				}
				/*$id_news_category 	= $post['id_news_category'][$key];
				 	
			 	$publish_date		= $post['publish_date'][$key];
			 	
			 	$id_status 			= ($post['id_status_publish'][1]!=1) ? $post['id_status_publish'][1] : $post['id_status_publish'][$key];*/
				
			}
			else{
				$tags 				= $post['tags'][$key];

				if ($post['is_not_available'][$post['id_lang'][0]]) {
					$id_news_category 	= $post['id_news_category'][$key];
					$publish_date		= $post['publish_date'][$key];
			 		$id_status 			= $post['id_status_publish'][$key];

				} else {
					if ($post['is_not_available'][$post['id_lang'][$key]]) {
			 			$id_status 			= 1;
					}
					$id_news_category 	= db_get_one('news_category','id',array('id_parent_lang'=>$post['id_news_category'][0],'id_lang'=>$post['id_lang'][$key]));

				}
				// $id_news_category 	= db_get_one('news_category','id',array('id_parent_lang'=>$post['id_news_category'][0],'id_lang'=>$post['id_lang'][$key])); 
			}
			// $idedit								= $post['id'][$key];

			$send_approval = $post['send_approval'];
			if($send_approval){
				$data_save['is_revise'] = NULL;

				$approval_level = $this->newsmodel->approvalLevelGroup + 1;
				$data_save['approval_level'] = $approval_level;
				if($approval_level == 2){ #current level = 1 = editor
					$data_save['user_id_editor'] = $id_user;
				}
				else if($approval_level == 3){ #current level = 2 = publisher
					$data_save['user_id_publisher'] = $id_user;
					$data_save['approval_level'] = 100 ; # 100 = lulus all approval = publish
					$data_save['id_status_publish'] = 2; #set to publish
				}

				if(!$post['publish_date'][0]){
				$post['publish_date'][0] = date('d-m-Y');
			}
			}


			$data_save['id_news_category']		= $id_news_category;
			$data_save['news_title']			= $post['news_title'][$key];
			$data_save['year'] 					= $post['year'][$key];
			$data_save['link_youtube_video']	= $post['link_youtube_video'][$key];
			$data_save['publish_date']			= iso_date_custom_format($post['publish_date'][$key],'Y-m-d H:i:s');
			$data_save['expected_publish_date']	= iso_date($post['expected_publish_date'][$key]);
			$data_save['id_status_publish'] 	= $id_status;
			$data_save['page_content']			= htmlspecialchars_decode(urldecode($post['page_content'.$key.'']));
			$data_save['writer']				= $post['writer'][$key];
			$data_save['meta_description'] 		= htmlspecialchars_decode(urldecode($post['meta_description'.$key.'']));
			$data_save['seo_title'] 			= $post['seo_title'][$key];
			$data_save['meta_keywords'] 		= $post['meta_keywords'][$key];
			$data_save['id_lang'] 				= $post['id_lang'][$key];
			$data_save['id_parent_lang'] 		= $id_parent_lang;
			$data_save['uri_path']				= $post['uri_path'][$key];
			$data_save['teaser']				= $post['teaser'][$key];
			$data_save['mailchimp']				= $post['mailchimp'][$key];
			$data_save['is_file_member']	    = $post['is_file_member'][$key];
			$data_save['is_not_available']		= $post['is_not_available'][$post['id_lang'][$key]] ? 1 : 0;
			$data_save['is_rssfeed']			= $post['is_rssfeed'][$post['id_lang'][$key]] ? 1 : 0;
			$post_image 						= $post['img'][$key];

			/*masukan data file*/
			if ($_FILES['file']['name'][$key]) {
					$ext = pathinfo($_FILES['file']['name'][$key], PATHINFO_EXTENSION);
					$fileRename = preg_replace('/[^A-Za-z0-9\-]/', '', (str_replace(" ", "-", substr($post['news_title'][$key], 0, 50))))."-".date("dMYHis").".".$ext;
					$data_save['filename']    = $fileRename;
					/*echo pathinfo($file, PATHINFO_EXTENSION); exit();*/
					fileToUpload($_FILES['file'],$key,$fileRename);
			} else {
				if($post['fileDelete'][$key] == 0){
					unset($data_save['filename']);
				} else{
					$data_save['filename'] = '';
					$data_save['is_file_member'] = 0;
				}
			}
			/*masukan data file*/
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

			foreach ($post['detail_img'] as $key => $value) {
				$temp['filename']     = $post['detail_img'][$key];
				$temp['description']  = $post['description_images_detail'][$key];
				$temp['is_share']     = $post['is_share'][$key];
				$temp['is_delete']    = $post['id_images_delete'][$key];
				$data_save_img_detail[] = $temp;
			}
			if($idedit){
				$this->db->where('id_news', $idedit);
				$this->newsImagesModel->update(array('is_delete'=>1,'is_share'=>0));

				foreach ($data_save_img_detail as $key => $value) {
					
					// $data_save_img_detail[$key]['id_news'] = $idedit;
					$check_img = $this->newsImagesModel->findBydelete(array('id'=>$post['id_images_detail'][$key]));

					if ($check_img) {

						if ($value['filename'] == '') {
							$data_save_img_detail[$key]['filename']  = $check_img[0]['filename'];
						}
						$this->db->where('id', $post['id_images_detail'][$key]);
						$this->newsImagesModel->update($data_save_img_detail[$key]);
					}else{
						$data_save_img_detail[$key]['id_news'] = $idedit;
						$this->newsImagesModel->insert($data_save_img_detail[$key]);
					}

				}
				// $img_detail_db = db_get_one('news','detail_img','id = '.$idedit);
				// $img_detail_db_arr = array_filter(explode(",", $img_detail_db));

				// if(empty(array_filter($post['detail_img'])) && $post['id_delete_img_detail']){
				// 	/* IF USER ONLY DELETE IMAGE*/
				// 	$img_delete = explode(',', $post['id_delete_img_detail']);

				// 	foreach ($img_delete as $key => $value) {						
				// 		$key_delete = array_search($value,$img_detail_db_arr);
				// 		if ($key_delete) {
				// 			unset($img_detail_db_arr[$key_delete]);
				// 		}
				// 	}

				// 	foreach($img_detail_db_arr as $key_detail => $value_detail){
				// 		$img_detail .= $img_detail_db_arr[$key_detail].',';
				// 	}
					
				// }else if (!empty($post['detail_img']) || $post['id_delete_img_detail']) {
				// 	/* IF USER INSERT OR DELETE IMAGE*/
				// 	foreach ($post['detail_img'] as $key_detail => $detail_img) {						
				// 		$img_detail .= ($post['detail_img'][$key_detail]=='') ? $img_detail_db_arr[$key_detail].',' : $post['detail_img'][$key_detail].',';
				// 	}
				// }else{
				// 	/* IF USER Do nothing with IMAGE*/
				// 	$img_detail = $img_detail_db;
				// 	/* insert share image*/
				// }
				// $img_detail_arr = array_filter(explode(",", $img_detail));

				// /* insert share image*/
				// foreach ($post['is_share'] as $key_share => $value_share) {
				// 	if ($post['is_share'][$key_share] == 1) {
				// 		$img_share = ($img_detail_arr[$key_share] == '') ? '' : $img_detail_arr[$key_share];
				// 	}	
				// }			
				// $data_save['share_img']  = $img_share;

				// $data_save['detail_img'] = implode(',',array_filter(explode(',',$img_detail)));


				// if($post['imgDelete'][$key] == 0){
				// 	if(!$post_image){
				// 		//image gak di update
				// 	}else{
				// 		$data_save['img']	= $post['img'][$key];
				// 	}
				// } else{
				// 	$data_save['img'] = NULL;
				// }

				// print_r($data_save);exit;
				// if($key==0){
					auth_update();
					$ret['message'] = 'Update Success';
					$act			= "Update News";
					if(!$post['thumb'][$key]){
						unset($post['thumb'][$key]);
					}elseif($post['thumb'][$key]=='x'){
						$post['thumb'][$key] = '';
					}
					$iddata 		= $this->newsmodel->update($data_save,$idedit);
				// }else{
				// 	auth_update();
				// 	$ret['message'] = 'Update Success';
				// 	$act			= "Update News";
				// 	if(!$post['thumb'][$key]){
				// 		unset($post['thumb'][$key]);
				// 	}elseif($post['thumb'][$key]=='x'){
				// 		$post['thumb'][$key] = '';
				// 	}
				// 	$iddata 		= $this->newsmodel->updateKedua($data_save,$idedit);
				// }
			}else{				
				$data_save['img']	= $post_image;
				auth_insert();
				$ret['message']		= 'Insert Success';
				$act				= "Insert News";
				$iddata 			= $this->newsmodel->insert($data_save);
				$id_newsx			= $iddata;

				$datas 		= $this->newsmodel->selectData($iddata);
				$id_gallery = get_news_gallery_id($iddata)?get_news_gallery_id($iddata):0; 
				if (empty($id_gallery)) {
					$ins_data                         = $this->newsmodel->selectData($iddata,1);
					$ins_datas['name']                = $ins_data['news_title'] ;
					$ins_datas['description']         = $ins_data['teaser'] ;
					$ins_datas['uri_path']            = $ins_data['uri_path'] ;
					$ins_datas['id_lang']             = $ins_data['id_lang'] ;
					$ins_datas['img']                 = $ins_data['img'] ;
					$ins_datas['filename']            = $ins_data['filename'] ;
					$ins_datas['id_gallery_category'] = 3 ;
					$ins_datas['id_category_item']    = $id ;
					$ins_datas['youtube_url']         = $ins_data['youtube_url'] ;
					$ins_datas['seo_title']           = $ins_data['seo_title'] ;
					$ins_datas['meta_description']    = $ins_data['meta_description'] ;
					$ins_datas['meta_keywords']       = $ins_data['meta_keywords']    ;
					$id_gallery                       = $this->galleryModel->insert($ins_datas);
					update_0_images_gallery($id_gallery);
				}

				if(!empty($post['detail_img'])){
					foreach ($data_save_img_detail as $key => $value) {
						$data_save_img_detail[$key]['id_news'] = $iddata;
						$this->newsImagesModel->insert($data_save_img_detail[$key]);
					}
				}
				// 		// print_r();exit;
				// }

			}
			if($key==0){
				$id_parent_lang = $iddata;
			}

			detail_log();
			insert_log($act);
			set_flash_session('message', $ret['message']);
			$ret['error'] = 0;
			// print_r($post['tags'][$post['id_lang'][$key]]);exit;
			$tags = $post['tags'][$post['id_lang'][0]];
			if (!$tags){
				$this->db->delete('news_tags',array('id_news'=>$idedit));
			}

			$idNewsTags = array();
			foreach ($tags as $k => $v) {
				$tag = strtolower($v);
				$t['name'] =  $tag;
				$cek = $this->tagsmodel->fetchRow($t);
				if(!$cek){
					$t['uri_path'] =  url_title($tag);
					$idTags = $this->tagsmodel->insert($t);
				}
				else{
					$idTags = $cek['id'];
				}
				$newsTags['id_news'] = $id_newsx;
				$newsTags['id_tags'] = $idTags;	
				$cek2 				 = $this->newstagsmodel->fetchRow($newsTags);
				if(!$cek2){
					$this->newstagsmodel->insert($newsTags);
				}
				$idNewsTags[]	 = $idTags;
			}
			if ($idNewsTags && $id_newsx) {
				$deleteNewsTags = $this->newstagsmodel->findByNotIn($id_newsx,$idNewsTags);
				foreach ($deleteNewsTags as $newsTag) {
					/*jika ingin langsung delete*/
					$this->db->delete('news_tags',array('id'=>$newsTag['id']));
					/*jika ingin memakai is_delete*/
					// $this->newstagsmodel->delete($newsTag['id']);
				}
			}

			/*untuk menu tags*/
			// if($post['is_not_available'][$post['id_lang'][0]]){
			// 	$menutags = $post['menu_tags'][$post['id_lang'][1]];
			// } else{
				$menutags = $post['menu_tags'][$post['id_lang'][0]];
			// }
			if (!$menutags){
				$this->db->delete('news_menu_tags',array('id_news'=>$idedit));
			}

			$idEventMenuTags = array();
			foreach ($menutags as $k => $v) {
				$tag = strtolower($v);

				/*untuk mengecek apakah tag sudah ada apa belum*/
				$whrMn['name']      = $tag;
				$whrMn['is_delete'] = 0;
				$cek = $this->db->get_where('frontend_menu',$whrMn)->row_array();
				if(!$cek){
					$idMenuTags = 0;
				}
				else{
					$idMenuTags = $cek['id'];
				}
				/*end untuk mengecek apakah tag sudah ada apa belum*/

				/*untuk mengecek apakah tag sudah ada di event ini */
				$whrMn2['a.id_news']   = $id_newsx;
				$whrMn2['a.id_menu']   = $idMenuTags;
				$whrMn2['a.is_delete'] = 0;

									   $this->db->select('a.*,b.name as tags,b.extra_param');
									   $this->db->join('frontend_menu b','b.id = a.id_menu');
				$cek2 				 = $this->db->get_where('news_menu_tags a',$whrMn2)->row_array();
				if(!$cek2 && $idMenuTags != 0){
					$newsTags['id_news']       = $id_newsx;
					$newsTags['id_menu']        = $idMenuTags;
					$newsTags['create_date']    = date('Y-m-d H:i:s');
					$newsTags['user_id_create'] = id_user();
					$this->db->insert('news_menu_tags',array_filter($newsTags));
				}
				/*untuk mengecek apakah tag sudah ada di event ini */

				/*mengambil id tags dalam event*/
				$idEventMenuTags[]	 = $idMenuTags;
			}

			


			if ($idEventMenuTags && $id_newsx) {
								   $this->db->where('is_delete',0);
								   $this->db->where('id_news',$id_newsx);
								   $this->db->where_not_in('id_menu',$idEventMenuTags);
				$deleteEventMenuTags = $this->db->get('news_menu_tags')->result_array();
				if ($deleteEventMenuTags) {
					foreach ($deleteEventMenuTags as $eventTag) {
						/*jika ingin langsung didelete*/
						$this->db->delete('news_menu_tags',array('id'=>$eventTag['id']));
						/*jika ingin memakai is_delete*/
						// $this->newstagsmodel->delete($newsTag['id']);
					}
				}
			}
			/*end untuk menu tags*/
			$this->db->trans_complete();
		}
		echo json_encode($ret);
	}
	function del(){
		auth_delete();
		$this->db->trans_start();   
		$id = $this->input->post('iddel');
		$this->newsmodel->delete($id);
		$this->newsmodel->delete2($id);
		detail_log();
		insert_log("Delete News");
		$this->db->trans_complete();
	}
	function proses_approval(){
		die('?');
		$this->db->trans_start();   
			$post			= purify($this->input->post());
			$news 			= $this->newsmodel->findById($post['id_news']);
			$approval_level = $news['approval_level'];
			$next_approval 	= $post['proses'] == 'revise' ? ($approval_level - 1) : ($approval_level + 1);
			if(!$news['publish_date']){
				$news['publish_date'] = date('Y-m-d');
			}
			

			// if($post['comment']){
				$data['id_news'] = $post['id_news'];
				$data['approval_comment'] = $post['comment'];
				$data['approval_status'] = $post['proses'] == 'revise' ? '0' : 1;
				$this->newsapprovalcommentmodel->insert($data);
				if($post['proses'] == 'revise'){
					$update['is_revise'] = 1;
					$sent_mail = array(
						'news_title' => $news['news_title'],
						'date_create' => iso_date($news['create_date']),
						'expected_publish_date' => iso_date($news['expected_publish_date']),
						'date_modify' => iso_date($news['modify_date']),
						'revision_date' => date('d-m-Y'),
						'revision_comment' => $post['comment'],
						'view' => '<a href="'.base_url().'apps/news/view/'.$post['id_news'].'">View</a>',
						'edit' =>  '<a href="'.base_url().'apps/news/add/'.$post['id_news'].'">Edit</a>'
					);
				
					$id_auth_user_grup = (group_id() == 4) ? 3 : 2;
					$revisioner = $this->userModel->findBy(array('id_auth_user_grup'=>$id_auth_user_grup));
					foreach($revisioner as $key => $value) {
						$sent_mail['to'] = $value['username'];
						sent_email_by_category(35,$sent_mail, $value['email']);
					}
				}


				if($approval_level == 1){
					$update['user_id_editor'] = id_user();
				}
				else if($approval_level == 2){
					$update['user_id_publisher'] = id_user();
					$update['id_status_publish'] = 2;
					$update['publish_date'] = $news['publish_date'];
					$next_approval = $next_approval == 3 ? 100 : $next_approval;
				}

				$update['approval_level'] = $next_approval;
				$this->newsmodel->update($update,$post['id_news']);

			// }
		$this->db->trans_complete();
		$ret['error'] = 0;
		$ret['message'] = 'Update Success';
		echo json_encode($ret);
	}

	function records_approval($id_news){
		$data = $this->newsapprovalcommentmodel->records(array('id_news'=>$id_news));
		foreach ($data['data'] as $key => $value) {
			$data['data'][$key]['create_date'] = iso_date_time($value['create_date']);
			$data['data'][$key]['status'] = $value['approval_status'] ==  1 ? 'Approve' : 'Revise';
		}
		render('apps/news/records_approval',$data,'blank');
	}

	function version_detail($id){
		$data = $this->newsversionmodel->findById($id);
		if($data){
			$news = $this->newsmodel->findById($data['id_news']);
			$data['username'] = $news['username'];
			$data['img_thumb'] = image($data['img'],'small');
			$data['img_ori'] = image($data['img'],'large');
			if(!$data){
				die('404');
			}
			$data['news_title'] = quote_form($data['news_title']);
			$data['teaser'] = quote_form($data['teaser']);
			$data['create_date'] = iso_date_time($data['create_date']);

			$tags = $this->newstagsversionmodel->findBy(array('id_news_version'=>$id));
			foreach ($tags as $key => $value) {
				$tag .=  ', '.$value['tags'];
			}
			$data['tags'] 			= substr($tag,1);
			render('apps/news/view_version',$data,'blank');
		}
		else{
			echo 'Data Not Found';
		}

	}

	function select_category(){
		render('apps/news/select_category',$data,'blank');
	}
	function record_select_category(){
		$data = $this->newscategorymodel->records();
		foreach ($data['data'] as $key => $value) {
			// $data['data'][$key]['page_name'] = quote_form($value['page_name']);
		}
		render('apps/news/record_select_category',$data,'blank');
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
	function preview($uri_path){
		$url = base_url().'en/news/detail/'.$uri_path.'/1';
		$ch = curl_init($url);
		
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, TRUE);
		curl_setopt($ch, CURLOPT_FOLLOWLOCATION, TRUE);
		curl_setopt($ch, CURLOPT_AUTOREFERER, TRUE);
		
		$content = curl_exec($ch);
		curl_close($ch);

		print_r($content);exit;
	}
	

	function export_to_excel(){
		$post = $this->input->post();

		$alias['search_uri_path'] = 'a.uri_path';
		$alias['search_status_publish'] = 'c.id';
		$alias['search_id'] = 'a.id';
		$alias['search_news_category'] = 'b.id';

		$tgl = $post['tgl'];
		$bln = $post['bln'];
		$thn = $post['thn'];
		// echo $tgl.'-'.$bln.'-'.$thn;
		$nama_bln = $bulan[(int)$bln];
		
		if($thn && $bln && $tgl){
			$this->db->like('publish_date',"$thn-$bln-$tgl");
		}
		else if($thn && $bln && !$tgl){
			$this->db->like('publish_date',"$thn-$bln%");
		}
		else if($thn && !$bln && !$tgl){
			$this->db->like('publish_date',"$thn-%");
		}
		else if(!$thn && $bln && $tgl){
			$this->db->like('publish_date',"%-$bln-$tgl");
		}
		else if(!$thn && !$bln && $tgl){
			$this->db->where("publish_date like '%-$tgl'");
		}
		else if(!$thn && $bln && !$tgl){
			$this->db->like('publish_date',"%-$bln-%");
		}
		else if($thn && !$bln && $tgl){
			$this->db->like('publish_date',"$thn-%%-$tgl");
		}

		where_grid($post, $alias);

		$data['data'] = $this->newsmodel->export_to_excel();
		$i=1;
		foreach ($data['data'] as $key => $value) {
			$approval_level_news = $value['approval_level'];
			$group = $this->authGroupModel->fetchRow(array('approval_level'=>$approval_level_news));
			// $approval = $approval_level_news == 0 ? 'Draft' : ('Sent to '.$group['grup']);
			if($approval_level_news == 0 && $value['is_revise']== 1){
				$approval = 'Revise (writter)';
			}
			else if($approval_level_news == 1 && $value['is_revise']== 1){
				$approval = 'Revise (editor)';
			}
			else if($this->newsmodel->approvalLevelGroup == $approval_level_news && $approval_level_news != 0){
				$approval = '<a class="btn btn-primary" href="'.$this->currentController.'view/'.$value['id'].'">Review</>';
			}
			else if($approval_level_news == 100){
				$approval = 'Approved';
			}
			else if($approval_level_news == 0){
				$approval = 'Draft';
			}
			else{
				$approval = 'Sent to '.$group['grup'];
			}
			$data['data'][$key]['is_publisher'] = (group_id() == 4 or group_id() == 5 or group_id() == 1) ? '' : 'invis';
			$data['data'][$key]['news_title'] 		= quote_form($value['news_title']);
			$data['data'][$key]['publish_date'] 	= iso_date($value['publish_date']);
			$data['data'][$key]['expected_publish_date'] 	= iso_date($value['expected_publish_date']);
			$data['data'][$key]['modify_date'] 	= iso_date($value['modify_date']);
			$data['data'][$key]['approval_level'] 	= $approval;
			$data['data'][$key]['edit'] 		 	= is_edit_news($value['id'],$value['user_id_create'],$approval_level_news,'delete');
			$data['data'][$key]['delete'] 		 	= is_delete_news($value['id'],$value['user_id_create'],$approval_level_news);
			$data['data'][$key]['nomor'] = $i++;
			$data['data'][$key]['base_url_article'] = base_url();
		}
		render('apps/news/export_to_excel',$data,'blank');
		// export_to('News.xls');
	}

	function compare_id(){
		$id              = $this->input->post('id');

		$id_publication  = id_news_publication(1); // amcham report
		$id_us_invesment = id_child_news(56,1);
		$id_newsletter   = id_child_news(54,1);
		$id_networking	 = id_child_news(array(60,52),1);

		$data['is_publication']  = '0';
		$data['is_us_invesment'] = '0';
		$data['is_newsletter']   = '0';
		$data['is_networking']	 = '0';

		
		if (in_array($id, $id_us_invesment)){
			$data['is_us_invesment'] = '1';
		}else if (in_array($id, $id_newsletter)){
			$data['is_newsletter'] = '1';
		}else if ( in_array($id, $id_publication) ) {
			// check category report
			$data['is_publication'] = '1';
		}else if (in_array($id, $id_networking)) {
			$data['is_networking'] = '1';
		}
		
		echo json_encode($data);
	}

	function show_images($idgallery, $key){
		$allImages = array();

		$wh['is_delete'] = 0;
		$this->db->where('id_gallery', $idgallery);
		$allImages['nomorGal'] 			= $key;
		$allImages['list_images']       = $this->galleryImagesModel->listImages($wh);

		$allImages['jmlImages']		= count($allImages['list_images']);
		// print_r(count($allImages['list_images']));exit;
		$allImages['showUploadAll']	= (count($allImages['list_images']) == 0 ? 'none' : 'block');

		$tags_data = $this->newstagsmodel->records_tags_all();
		$allImagestags = generate_tags($tags_data,'name');

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
						$cek = $this->tagsmodel->fetchRow(array('name'=>$value));//liat tags name di tabel ref
						if(!$cek){//kalo belom ada
							$id_tags = $this->tagsmodel->insert(array('name'=>$value,'uri_path'=>url_title($value)));//insert ke tabel ref
							detail_log();
						}
						else{
							$id_tags = $cek['id']; //kalo udah ada, tinggal ambil idnya
						}

						$cekTagsImage = $this->gallerytagsmodel->fetchRow(array('id_tags'=>$id_tags,'id_images'=>$iddata)); //liat di tabel news tags, (utk edit)
					
						if(!$cekTagsNews){//kalo blm ada ya di insert
						$tag['id_gallery'] 	  = $data_save['id_gallery'];
						$tag['id_images']     = $iddata;
						$tag['id_tags']   	  = $id_tags;
						$id_news_tags = $this->gallerytagsmodel->insert($tag);
						}
						else{//kalo udah ada, ambil id nya utk di simpen sbg array utk kebutuhan delete
						$tag['id_gallery'] 	  = $data_save['id_gallery'];
						$tag['id_images']     = $cekTagsImage['id_images'];
						$tag['id_tags']   	  = $id_tags;
						$id_news_tags = $this->gallerytagsmodel->update($tag,$cekTagsNews['id']);
						}
						$temp_id[] 			=$id_tags;
					}

					$this->db->where_not_in('a.id_tags',$temp_id); 
					$delete = $this->gallerytagsmodel->findBy(array('a.id_images'=> $iddata)); //dapetin id news tags yg diapus  (where id not in insert / select and id_news = $id_template)
					
					foreach ($delete as $key => $value) {
						$a['is_delete'] = 1;
						$b = $this->gallerytagsmodel->update($a,$value['id']);
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
						$cek = $this->tagsmodel->fetchRow(array('name'=>$value));//liat tags name di tabel ref
						if(!$cek){//kalo belom ada
							$id_tags = $this->tagsmodel->insert(array('name'=>$value,'uri_path'=>url_title($value)));//insert ke tabel ref
							detail_log();
						}
						else{
							$id_tags = $cek['id']; //kalo udah ada, tinggal ambil idnya
						}

						$cekTagsImage = $this->gallerytagsmodel->fetchRow(array('id_tags'=>$id_tags,'id_images'=>$iddata)); //liat di tabel news tags, (utk edit)


					
						if(!$cekTagsImage){//kalo blm ada ya di insert
						$tag['id_gallery'] 	  = $data_save['id_gallery'];
						$tag['id_images']     = $iddata;
						$tag['id_tags']   	  = $id_tags;
						$id_news_tags = $this->gallerytagsmodel->insert($tag);
						}
						else{//kalo udah ada, ambil id nya utk di simpen sbg array utk kebutuhan delete
						$tag['id_gallery'] 	  = $data_save['id_gallery'];
						$tag['id_images']     = $cekTagsImage['id_images'];
						$tag['id_tags']   	  = $id_tags;
						$id_news_tags = $this->gallerytagsmodel->update($tag,$cekTagsNews['id']);
						}
						$temp_id[] 			=$id_tags;

					}

					$this->db->where_not_in('a.id_tags',$temp_id); 
					$delete = $this->gallerytagsmodel->findBy(array('a.id_images'=> $iddata)); //dapetin id news tags yg diapus  (where id not in insert / select and id_news = $id_template)

					foreach ($delete as $key => $value) {
						$a['is_delete'] = 1;
						$b = $this->gallerytagsmodel->update($a,$value['id']);
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
		$post['id_gallery'] = get_news_gallery_id($post['id_gallery']);


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
		$post['id_gallery'] = get_news_gallery_id($post['id_gallery']);


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

	
    function show_images_AlbumGallery_add(){
        $post                   = purify($this->input->post(NULL,TRUE));
        $idalbum                = $post['id_album'];
        $array = array_filter(explode(",", $idalbum));

        $this->db->where_in('id_gallery',$array);
        $allImages['nomorGal']      = $key;

        $allImages['list_images']   = $this->galleryImagesModel->findBy();
    
        $i=1;
        foreach ($allImages['list_images'] as $key => $value) {

            $this->db->where('is_delete', 0);
            $this->db->select('group_concat(id_tags) as hasil');
            $tags = $this->db->get_where('gallery_tags',array('id_images'=>$value['id']))->row_array()['hasil'];
            $arr_tags = explode(',', $tags);


            $this->db->select('group_concat(name) as hasil');
            $this->db->where_in('id',$arr_tags);
            $tagsname = $this->db->get_where('tags')->row_array()['hasil'];
            $allImages['list_images'][$key]['tags']            = $allImagestags;
            $allImages['list_images'][$key]['tag_images']      = $tagsname;
            $allImages['list_images'][$key]['rowNum']          = $i++;
            $allImages['list_images'][$key]['name_img']        = $value['name'];
            $allImages['list_images'][$key]['description_img'] = $value['description'];
            $allImages['list_images'][$key]['idGallery']       = $value['id_gallery'];
        }
        render('apps/news/gallery_list_images_album_gallery',$allImages,'blank');
    }

    function record_select_category_album(){
        $arr_id = $this->uri->segment_array();
        unset($arr_id[1]);
        unset($arr_id[2]);
        unset($arr_id[3]);
        $real_id = array_filter($arr_id);
        if (!empty(array_filter($arr_id))) {
            $this->db->where_not_in('a.id',$arr_id);
        }
        $this->db->where('a.id_gallery_category',1);
        $data['data']       = $this->galleryModel->findBy();
        
        $i=1;
        foreach ($data['data'] as $key => $value) {
            $data['data'][$key]['name'] = $value['name'];
            $data['data'][$key]['nomor'] = $i++;
        }
        render('apps/news/record_select_category_album',$data,'blank');
    }

    function getAlbumGallery(){
        $post = purify($this->input->post(NULL,TRUE));

        if ($post['id_gallery']) {
            $arr_id = explode(",", $post['id_gallery']);
            if ($arr_id) {
                $data['idgallery'] = implode("/", $arr_id);
            }else{
                $data['idgallery'] = $post['id_gallery'];
            }
        }else{
            $data['idgallery'] = "";    
        }

        render('apps/news/select_category_album',$data,'blank');
    }

    function show_images_AlbumGallery(){

        $post = purify($this->input->post(NULL,TRUE));

        $idgallery =$post['id_gallery'];
        $idevent   =$post['id_event'];
        $arrayid   =$post['array_id'];

        $tags_data = $this->newstagsmodel->records_tags_all();
        foreach ($tags_data as $key => $value_tags) {
        $tags_data_val .=  ",'".$value_tags['name']."'";
        }
        $allImagestags = substr($tags_data_val,1);
        
    
        $this->db->where_in('id_gallery', $idgallery);
        $allImages['nomorGal']      = $key;
        $allImages['list_images']   = $this->galleryImagesModel->findBy();
        $i=1;
        foreach ($allImages['list_images'] as $key => $value) {

            $this->db->where('is_delete', 0);
            $this->db->select('group_concat(id_tags) as hasil');
            $tags = $this->db->get_where('gallery_tags',array('id_images'=>$value['id']))->row_array()['hasil'];
            $arr_tags = explode(',', $tags);


            $this->db->select('group_concat(name) as hasil');
            $this->db->where_in('id',$arr_tags);
            $tagsname = $this->db->get_where('tags')->row_array()['hasil'];
            $allImages['list_images'][$key]['tags']            = $allImagestags;
            $allImages['list_images'][$key]['tag_images']      = $tagsname;
            $allImages['list_images'][$key]['rowNum']          = $i++;
            $allImages['list_images'][$key]['name_img']        = $value['name'];
            $allImages['list_images'][$key]['description_img'] = $value['description'];
            $allImages['list_images'][$key]['idGallery']       = $value['id_gallery'];
        }

        $arrayid = implode(",", array_filter(explode(",", $arrayid)));
        if (empty($arrayid)) {
        	$update_gallery['id_gallery'] = '';
        }else{
        	$update_gallery['id_gallery'] = ','.$arrayid.',';
        }
        $this->newsmodel->update($update_gallery,$idevent);
        
        render('apps/news/gallery_list_images_album_gallery',$allImages,'blank');
    }

    function record_select_category_album_to_remove(){
        $arr_id = $this->uri->segment_array();
        unset($arr_id[1]);
        unset($arr_id[2]);
        unset($arr_id[3]);
        $real_id = array_filter($arr_id);
        // if (!empty(array_filter($arr_id))) {
            $this->db->where_in('a.id',$arr_id);
        // }
        $this->db->where('a.id_gallery_category',1);
        $data['data']       = $this->galleryModel->findBy();
        
        $i=1;
        foreach ($data['data'] as $key => $value) {
            $data['data'][$key]['name'] = $value['name'];
            $data['data'][$key]['nomor'] = $i++;
        }
        render('apps/news/record_select_category_album_to_remove',$data,'blank');
    }

    function getAlbumGalleryToRemove(){
        $post = purify($this->input->post(NULL,TRUE));
        if ($post['id_gallery']) {
            $arr_id = explode(",", $post['id_gallery']);
            if ($arr_id) {
                $data['idgallery'] = implode("/", $arr_id);
            }else{
                $data['idgallery'] = $post['id_gallery'];
            }
        }else{
            $data['idgallery'] = "";    
        }

        render('apps/news/select_category_album_to_remove',$data,'blank');
    }

    function show_images_AlbumGallery_to_remove(){

        $post = purify($this->input->post(NULL,TRUE));
        $idgallery = $post['id_gallery'];

        $idevent   = $post['id_event'];

        $tags_data = $this->newstagsmodel->records_tags_all();
        foreach ($tags_data as $key => $value_tags) {
        $tags_data_val .=  ",'".$value_tags['name']."'";
        }
        $allImagestags = substr($tags_data_val,1);
    
        $this->db->where('id_gallery', $idgallery);
        $allImages['nomorGal']      = $key;
        $allImages['list_images']   = $this->galleryImagesModel->findBy();
        $i=1;
        foreach ($allImages['list_images'] as $key => $value) {

            $this->db->where('is_delete', 0);
            $this->db->select('group_concat(id_tags) as hasil');
            $tags = $this->db->get_where('gallery_tags',array('id_images'=>$value['id']))->row_array()['hasil'];
            $arr_tags = explode(',', $tags);


            $this->db->select('group_concat(name) as hasil');
            $this->db->where_in('id',$arr_tags);
            $tagsname = $this->db->get_where('tags')->row_array()['hasil'];
            $allImages['list_images'][$key]['tags']            = $allImagestags;
            $allImages['list_images'][$key]['tag_images']      = $tagsname;
            $allImages['list_images'][$key]['rowNum']          = $i++;
            $allImages['list_images'][$key]['name_img']        = $value['name'];
            $allImages['list_images'][$key]['description_img'] = $value['description'];
            $allImages['list_images'][$key]['idGallery']       = $value['id_gallery'];
        }

        $idgallery = implode(",", array_filter(explode(",", $idgallery)));
		if (empty($idgallery)) {
			$update_gallery['id_gallery'] = '';
		}else{
			$update_gallery['id_gallery'] = ','.$idgallery.',';
		}       

        $this->newsmodel->update($update_gallery,$idevent);

        render('apps/news/gallery_list_images_album_gallery',$allImages,'blank');
    }

}

/* End of file news.php */
/* Location: ./application/controllers/apps/news.php */