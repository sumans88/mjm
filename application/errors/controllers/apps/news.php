<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class News extends CI_Controller {
	function __construct(){
		parent::__construct();
		$this->load->model('newsModel');
		$this->load->model('newsVersionModel');
		$this->load->model('newsCategoryModel');
		$this->load->model('newsTagsModel');
		$this->load->model('newsTagsVersionModel');
		$this->load->model('tagsModel');
		$this->load->model('newsApprovalCommentModel');
		$this->load->model('languageModel');
		$this->load->model('authgroup_model','authGroupModel');
		$this->load->model('model_user','userModel');

		$this->sess = $this->session->userdata('ADM_SESS');
	}
	function index(){
		$data['list_news_category'] = selectlist2(array('table'=>'news_category','title'=>'All Category','where'=> array('id !='=>35, 'is_hide'=>0),'selected'=>$data['id_news_category'],'is_delete'=>0));
		$data['list_status_publish'] = selectlist2(array('table'=>'status_publish','title'=>'All Status','selected'=>$data['id_status_publish']));
		$data['calendar_days_number'] = calendar_days_number('cari form-control');
		$data['calendar_months_word'] = calendar_months_word('cari form-control');
		$data['calendar_years_number'] = calendar_years_number('cari form-control');
		$data['is_publisher'] = (group_id() == 4 or group_id() == 5 or group_id() == 1) ? '' : 'invis';

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
			// $data = $this->newsModel->findById($id);
			$datas 	= $this->newsModel->selectData($id);

			if(!$datas){
				die('404');
			}
			$data 							= quote_form($datas);
			$data['judul']					= 'Edit';
			$data['proses']					= 'Update';
			$data['publish_date'] 			= iso_date($data['publish_date']);
			$data['expected_publish_date'] 	= iso_date($data['expected_publish_date']);
			
			$data_version 				= $this->newsVersionModel->findByrecordversion(array('id_news'=>$id));
			$data['last_edited'] 		= "by <b>$data_version[username]</b>" . ' on ' . iso_date_time($data_version['create_date']);
			$data['edit_button'] 		=  (group_id() == 4 and $data['approval_level']==100) ? 'invis' : '';
			$data['last_edited_show'] 	= '';
		}else{
			$id=0;
			$data['last_edited'] 			= '';
			$data['last_edited_show']		= 'invis';
			$data['judul']					= 'Add';
			$data['proses']					= 'Simpan';
			$data['news_title']				= '';
			$data['link_youtube_video']		= '';
			$data['uri_path']				= '';
			$data['teaser']					= '';
			$data['page_content']			= '';
			$data['publish_date']			= '';
			$data['expected_publish_date']	= date('d-m-Y');
			$data['tags'] 					= '';
			$data['id'] 					= '';
			$data['is_featured']			= '';
			$data['is_experts']				= '';
			$data['sort']					= '';
			$data['id_lang']				= '';
			$data['is_qa']					= '';
			$data['seo_title']				= '';
			$data['meta_description']		= '';
			$data['meta_keywords']			= '';
			$data['footnote']				= '';
		}


		$tags_data = $this->newsTagsModel->records_tags_all();
		foreach ($tags_data as $key => $value_tags) {
		    $tags_data_val .=  ",'".$value_tags['name']."'";
		}
		$data['tags'] = substr($tags_data_val,1);
		$data['list_lang']	= $this->languageModel->langName();
		$id_lang_default	= $this->languageModel->langId();
		foreach ($data['list_lang'] as $key => $value){
			$data['list_lang'][$key]['invis'] 				= ($key==0) ? '' : 'hide';
			$data['list_lang'][$key]['active'] 				= ($key==0) ? 'active in' : '';
			$data['list_lang'][$key]['validation'] 			= ($key==0) ? 'true' : 'false';
			$data['list_lang'][$key]['nomor'] 				= $key;
			$data['list_lang'][$key]['dsp_publish_date'] 	= group_id() == 4 ? '' : 'invis';
			
			$data['list_lang'][$key]['show_thumb']			= $datas[$key]['thumb'] ? 1 : '';

			$data['list_lang'][$key]['is_experts_checked'] 			= ($datas[$key]['is_experts'] == 1) ? 'checked' : '';
			$data['list_lang'][$key]['sort_checked'] 				= ($datas[$key]['sort'] == 1) ? 'checked' : '';
			$data['list_lang'][$key]['is_qa_checked'] 				= ($datas[$key]['is_qa'] == 1) ? 'checked' : '';
			$data['list_lang'][$key]['is_featured_checked'] 		= ($datas[$key]['is_featured'] == 1) ? 'checked' : '';
			$data['list_lang'][$key]['is_editor_choice_checked'] 	= ($datas[$key]['is_editor_choice'] == 1) ? 'checked' : '';

			$data['list_lang'][$key]['id_news_category']		= $datas[$key]['id_news_category'];
			$data['list_lang'][$key]['news_title']				= $datas[$key]['news_title'];
			$data['list_lang'][$key]['uri_path']				= $datas[$key]['uri_path'];
			$data['list_lang'][$key]['link_youtube_video']		= $datas[$key]['link_youtube_video'];
			$data['list_lang'][$key]['publish_date']			= $datas[$key]['publish_date'];
			$data['list_lang'][$key]['expected_publish_date']	= $datas[$key]['expected_publish_date'];	
			$data['list_lang'][$key]['id_status_publish']		= $datas[$key]['id_status_publish'];
			$data['list_lang'][$key]['tags']					= $datas[$key]['tags'];
			$data['list_lang'][$key]['teaser']					= $datas[$key]['teaser'];
			$data['list_lang'][$key]['page_content']			= $datas[$key]['page_content'];
			$data['list_lang'][$key]['seo_title'] 				= $datas[$key]['seo_title'];
			$data['list_lang'][$key]['meta_description'] 		= $datas[$key]['meta_description'];
			$data['list_lang'][$key]['meta_keywords'] 			= $datas[$key]['meta_keywords'];
			$data['list_lang'][$key]['id'] 						= $datas[$key]['id'];

			$data['list_lang'][$key]['edit_status_publish'] 	= (group_id() == 4 and $datas[$key]['approval_level']==100) ? '' : 'invis';


			$data['list_lang'][$key]['list_news_category'] 		= selectlist2(array('table'=>'news_category','where'=> array('is_hide'=>0,'id_lang'=>$id_lang_default),'title'=>'Select Category','selected'=>$datas[$key]['id_news_category']));
			$data['list_lang'][$key]['list_status_publish'] 	= selectlist2(array('table'=>'status_publish','title'=>'Select Status','selected'=>$datas[$key]['id_status_publish']));
			$data['list_lang'][$key]['list_language'] 			= selectlist2(array('table'=>'language','title'=>'Select Language','selected'=>'1'));
			$data['list_lang'][$key]['list_tags'] 				= selectlist2(array('table'=>'tags','title'=>'','where'=> array('is_delete'=>'0')));
			
			
			$img_thumb											= image($datas[$key]['img'],'small');
			$imagemanager										= imagemanager('img',$img_thumb,200,150,$key);
			$data['list_lang'][$key]['img']						= $imagemanager['browse'];
			$data['list_lang'][$key]['imagemanager_config']		= $imagemanager['config'];

			$tags = $this->newsTagsModel->findBy(array('id_news'=>$datas[$key]['id']));
			$tag = '';
			foreach ($tags as $k => $v){
				$tag .=  ','.$v['tags'];
			}
			$data['list_lang'][$key]['tags_data'] 				= substr($tag,1);
			// echo '<br>';
		}
		// exit;
			// $data['tags_data']			= '';

		// $next_approval 	= $this->newsModel->approvalLevelGroup + 1;
		// $group 			= $this->authGroupModel->fetchRow(array('approval_level'=>$next_approval));
		// $data['next_approval'] = $group['grup'] ? "&amp; Sent to $group[grup]" : '&amp; Publish';
		// $data['enable_edit_status_publish'] = is_edit_publish_status();
		// $data['disable_edit'] = is_edit_news($id,$data['user_id_create'],$data['approval_level'],'return') ? '' : 'invis';
		// $data['enable_edit_editors_choice'] = enable_edit_editors_choice();
		$data['list_lang2'] = $data['list_lang'];
		render('apps/news/add',$data,'apps');
	}
	public function view($id=''){
		if($id){
			$datas 	= $this->newsModel->selectData($id);
			
			if(!$datas){
				die('404');
			}

			$data['list_lang']	= $this->languageModel->langName();
			foreach ($data['list_lang'] as $key => $value){
				$data['list_lang'][$key]['invis'] 				= ($key==0) ? '' : 'hide';
				$data['list_lang'][$key]['active'] 				= ($key==0) ? 'active in' : '';
				$data['list_lang'][$key]['validation'] 			= ($key==0) ? 'true' : 'false';
				$data['list_lang'][$key]['nomor'] 				= $key;
				$data['list_lang'][$key]['dsp_publish_date'] 	= group_id() == 4 ? '' : 'invis';
				
				$data['list_lang'][$key]['news_title']		= $datas[$key]['news_title'];
				$data['list_lang'][$key]['tags']			= $datas[$key]['tags'];
				$data['list_lang'][$key]['teaser']			= $datas[$key]['teaser'];
				$data['list_lang'][$key]['page_content']	= $datas[$key]['page_content'];

				$data['list_lang'][$key]['img_thumb'] 	= image($datas[$key]['img'],'small');
				$data['list_lang'][$key]['img_ori'] 	= image($datas[$key]['img'],'large');

				$data['list_lang'][$key]['img_news_class'] 				= '';
				$data['list_lang'][$key]['img_news_class_button'] 		= 'hide';
				
				if($data['list_lang'][$key]['link_youtube_video']){
					$data['list_lang'][$key]['link_youtube_video'] 		= str_replace("watch?v=","embed/",$datas[$key]['link_youtube_video']);
					$data['list_lang'][$key]['img_news_class'] 			= 'img-news-with-video';
					$data['list_lang'][$key]['img_news_class_button'] 	= 'img-news-class-button';
				}
				$data['list_lang'][$key]['teaser']		 				= remove_html_tag_news($datas[$key]['teaser']);
				$data['list_lang'][$key]['create_date'] 				= iso_date_time($datas[$key]['create_date']);

				// $tags = $this->newsTagsModel->findBy(array('id_news'=>$id));
				// foreach ($tags as $key => $value) {
				// 	$tag .=  ', '.$value['tags'];
				// }
				$data['list_lang'][$key]['footnote'] 					= urldecode(htmlspecialchars_decode(remove_html_tag_news($datas[$key]['footnote'])));
				$data['list_lang'][$key]['disable_edit'] 			= is_edit_news($id,$datas[$key]['user_id_create'],$datas[$key]['approval_level'],'return') ? '' : 'invis';

				$data['list_lang'][$key]['tags'] 						= substr($tag,1);
				$data['list_lang'][$key]['comment_article_not_login']	= comment_article_not_login();
				$data['list_lang'][$key]['comments_data'] 				= getcomments($id);
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

		// $data['approval'] 				= $this->newsModel->approvalLevelGroup == $data['approval_level'] && $data['approval_level'] != 0 ? '' : 'invis';
		// $data['list_news_category'] 	= selectlist2(array('table'=>'news_category','where'=> 'id!=35','title'=>'All Category'));
		// $data['list_status_publish'] 	= selectlist2(array('table'=>'status_publish','title'=>'All Status'));
  //       $data['share_widget']    		= '';//share_widget();
		// $data['top_content']     		= top_content();
  //       $data['ads']             		= ads_widget();
  //       $data['box_widget']      		= '';//box_widget();
  //       $data['create_date']     		= iso_date_custom_format($data['publish_date'],'d').' '.get_month(iso_date_custom_format($data['publish_date'],'F')).' '.iso_date_custom_format($data['publish_date'],'Y');
		
		// if(!$data['publish_date']){
		// 	$data['create_date'] 	= date('d').' '.get_month(date('F')).' '.date('Y');
		// }
		// $data['artikel_terkait'] 	= '';//artikel_terkait($data['id'],$id_tags);
        
		render('apps/news/view',$data,'apps');
	}
	function records(){
		$data = $this->newsModel->records();
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
			else if($this->newsModel->approvalLevelGroup == $approval_level_news && $approval_level_news != 0){
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
		}
		if ($this->sess['admin_id_auth_user_group'] == 2) {
			$data['hide_data'] 	= 'hide';
		}
		else{
			$data['hide_data']		= '';
		}
		
		render('apps/news/records_news',$data,'blank');
	}
	function get_comments_data($id_news){
		$data['comment_data'] = getcomments($id_news);
		$data['id_news'] = $id_news;
		echo json_encode($data);
	}
	function records_version($id_news){
		$data = $this->newsVersionModel->records(array('id_news'=>$id_news));
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
			if(!$idedit){
				$where['a.uri_path']	= $post['uri_path'][$key];
				$unik 					= $this->newsModel->findBy($where);
				$this->form_validation->set_rules('id_news_category', '"Category"', 'required'); 
				$this->form_validation->set_rules('news_title', '"Title"', 'required'); 
				$this->form_validation->set_rules('uri_path', '"Page URL"', 'required');
				if ($this->form_validation->run() == FALSE){
					$ret['message']  = validation_errors(' ',' ');
				}
				if($unik){
					$ret['message']	= "Page URL $value already taken";
				}
			}

			$data_save['teaser'] 				= remove_html_tag_news($post['teaser'][$key]);
			$data_save['footnote']				= remove_html_tag_news($post['footnote'][$key]);
			
			if($key==0){
				if($idedit){
					$data_save['sort']				= ($post['sort'][$key] != 1) ? 0 : 1;
				}
				$idedit				= $post['id'][$key];
				$tags 				= $post['tags'][$key];
				$id_news_category 	= $post['id_news_category'][$key];
			 	$sort 				= $post['sort'][$key];
			 	$publish_date		= $post['publish_date'][$key];
			 	$id_status 			= $post['id_status_publish'][$key];
			}
			else{
				$id_news_category 	= db_get_one('news_category','id',array('id_parent_lang'=>$post['id_news_category'][0],'id_lang'=>$post['id_lang'][$key])); 
			}
			$idedit								= $post['id'][$key];
			$data_save['id_news_category']		= $id_news_category;
			$data_save['news_title']			= $post['news_title'][$key];
			$data_save['link_youtube_video']	= $post['link_youtube_video'][$key];
			$data_save['publish_date']			= iso_date($post['publish_date'][$key]);
			$data_save['expected_publish_date']	= iso_date($post['expected_publish_date'][$key]);
			$data_save['id_status_publish'] 	= $id_status;
			$data_save['page_content']			= $post['page_content'][$key];
			$data_save['seo_title'] 			= $post['seo_title'][$key];
			$data_save['meta_description'] 		= $post['meta_description'][$key];
			$data_save['meta_keywords'] 		= $post['meta_keywords'][$key];
			$data_save['id_lang'] 				= $post['id_lang'][$key];
			$data_save['id_parent_lang'] 		= $id_parent_lang;
			$data_save['uri_path']				= $post['uri_path'][$key];
			$data_save['teaser']				= $post['teaser'][$key];

			$post_image 						= $post['img'][$key];

			// if($idedit && $post_image){ //kondisi saat edit tapi user tidak browse image
			// 	$data_save['img']	= $post['img'][$key];
			// }elseif($idedit){ //kondisi saat edit dan kalau user browse image
			// 	$datas 				= $this->pagesModel->selectData($idedit);
			// 	$data_save['img']	= $datas[$key]['img'];
			// }else{ //kondisi input image
			// 	$data_save['img']	= $post['img'][$key];
			// }
			
			if($idedit){
				if(!$post_image){
					//image gak di update
				}else{
					$data_save['img']	= $post['img'][$key];
				}

				// if($key==0){
					auth_update();
					$ret['message'] = 'Update Success';
					$act			= "Update News";
					if(!$post['thumb'][$key]){
						unset($post['thumb'][$key]);
					}elseif($post['thumb'][$key]=='x'){
						$post['thumb'][$key] = '';
					}
					$iddata 		= $this->newsModel->update($data_save,$idedit);
				/*}else{
					auth_update();
					$ret['message'] = 'Update Success';
					$act			= "Update News";
					if(!$post['thumb'][$key]){
						unset($post['thumb'][$key]);
					}elseif($post['thumb'][$key]=='x'){
						$post['thumb'][$key] = '';
					}
					$iddata 		= $this->newsModel->updateKedua($data_save,$idedit);
				}*/
			}else{
				$data_save['img']	= $post_image;
				auth_insert();
				$ret['message']		= 'Insert Success';
				$act				= "Insert News";
				$iddata 			= $this->newsModel->insert($data_save);
			}
			if($key==0){
				$id_parent_lang = $iddata;
			}

			detail_log();
			insert_log($act);
			$this->db->trans_complete();
			set_flash_session('message', $ret['message']);
			$ret['error'] = 0;
			$tags = $post['tags'][$post['id_lang'][$key]];

			$idNewsTags = array(0);
			foreach ($tags as $k => $v) {
				$tag = strtolower($v);
				$t['uri_path'] =  url_title($tag);
				$cek = $this->tagsModel->fetchRow($t);
				if(!$cek){
					$t['name'] =  $tag;
					$idTags = $this->tagsModel->insert($t);
				}
				else{
					$idTags = $cek['id'];
				}
				$newsTags['id_news'] = $idedit;
				$newsTags['id_tags'] = $idTags;
				$cek2 				 = $this->newsTagsModel->fetchRow($newsTags);
				if(!$cek2){
					$this->newsTagsModel->insert($newsTags);
				}
				$idNewsTags[]	 = $idTags;
			}
			$deleteNewsTags = $this->newsTagsModel->findByNotIn($idedit,$idNewsTags);
			foreach ($deleteNewsTags as $newsTag) {
				$this->newsTagsModel->delete($newsTag['id']);
			}
		}
		echo json_encode($ret);
	}
	function del(){
		auth_delete();
		$this->db->trans_start();   
		$id = $this->input->post('iddel');
		$this->newsModel->delete($id);
		$this->newsModel->delete2($id);
		detail_log();
		insert_log("Delete News");
		$this->db->trans_complete();
	}
	function proses_approval(){
		$this->db->trans_start();   
			$post			= purify($this->input->post());
			$news 			= $this->newsModel->findById($post['id_news']);
			$approval_level = $news['approval_level'];
			$next_approval 	= $post['proses'] == 'revise' ? ($approval_level - 1) : ($approval_level + 1);
			if(!$news['publish_date']){
				$news['publish_date'] = date('Y-m-d');
			}
			

			// if($post['comment']){
				$data['id_news'] = $post['id_news'];
				$data['approval_comment'] = $post['comment'];
				$data['approval_status'] = $post['proses'] == 'revise' ? '0' : 1;
				$this->newsApprovalCommentModel->insert($data);
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
				$this->newsModel->update($update,$post['id_news']);

			// }
		$this->db->trans_complete();
		$ret['error'] = 0;
		$ret['message'] = 'Update Success';
		echo json_encode($ret);
	}

	function records_approval($id_news){
		$data = $this->newsApprovalCommentModel->records(array('id_news'=>$id_news));
		foreach ($data['data'] as $key => $value) {
			$data['data'][$key]['create_date'] = iso_date_time($value['create_date']);
			$data['data'][$key]['status'] = $value['approval_status'] ==  1 ? 'Approve' : 'Revise';
		}
		render('apps/news/records_approval',$data,'blank');
	}

	function version_detail($id){
		$data = $this->newsVersionModel->findById($id);
		if($data){
			$news = $this->newsModel->findById($data['id_news']);
			$data['username'] = $news['username'];
			$data['img_thumb'] = image($data['img'],'small');
			$data['img_ori'] = image($data['img'],'large');
			if(!$data){
				die('404');
			}
			$data['news_title'] = quote_form($data['news_title']);
			$data['teaser'] = quote_form($data['teaser']);
			$data['create_date'] = iso_date_time($data['create_date']);

			$tags = $this->newsTagsVersionModel->findBy(array('id_news_version'=>$id));
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
		$data = $this->newsCategoryModel->records();
		foreach ($data['data'] as $key => $value) {
			// $data['data'][$key]['page_name'] = quote_form($value['page_name']);
		}
		render('apps/news/record_select_category',$data,'blank');
	}
    
	
	function tagsurl()
	{
		$this->load->model('tagsModel');
		$data = $this->tagsModel->findBy();
		foreach ($data as $key => $value) {
			echo $value['name'].' || '.$value['uri_path'].'<br>';
			$this->tagsModel->update(array('uri_path'=>url_title(strtolower($value['name']))),$value['id']);
		}
	}
	function preview(){
		$data	= purify($this->input->post());
		if($data){
			if($data['id']!=0 and $data['id']){
				$data_file = $this->newsModel->findById($data['id']);
				$data['img'] = $data_file['img']; 
			}
			$data['img_thumb'] = image($data['img'],'small');
			$data['img_ori'] = image($data['img'],'large');
			$data['img_news_class'] = '';
			$data['img_news_class_button'] = 'hide';
			if($data['link_youtube_video']){
				$data['link_youtube_video'] = str_replace("watch?v=","embed/",$data['link_youtube_video']);
				$data['img_news_class'] = 'img-news-with-video';
				$data['img_news_class_button'] = 'img-news-class-button';
			}
			$data['page_content'] = urldecode(htmlspecialchars_decode($data['page_content']));
			$data['teaser']		 = htmlspecialchars_decode($data['teaser']);
			$data['footnote']		 = urldecode(htmlspecialchars_decode($data['footnote']));
			$data['create_date'] = iso_date_time($data['create_date']);
			$category = $this->db->query("select name from news_category where id=$data[id_news_category]")->row_array();
			$data['category'] = $category['name'];
			$data['share_widget']    = '';//share_widget();
			$data['top_content']     = top_content();
			$data['ads']             = ads_widget();
			$data['box_widget']      = '';//box_widget();
			$data['create_date']     = iso_date_custom_format($data['publish_date'],'d').' '.get_month(iso_date_custom_format($data['publish_date'],'F')).' '.iso_date_custom_format($data['publish_date'],'Y');
			$data['artikel_terkait'] = '';//artikel_terkait($data['id'],$id_tags);
			render('apps/news/preview',$data,'futuready/main');
			
		} else {
			redirect(base_url().'apps/news');
		}
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

		$data['data'] = $this->newsModel->export_to_excel();
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
			else if($this->newsModel->approvalLevelGroup == $approval_level_news && $approval_level_news != 0){
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
		export_to('News.xls');
	}

}

/* End of file news.php */
/* Location: ./application/controllers/apps/news.php */