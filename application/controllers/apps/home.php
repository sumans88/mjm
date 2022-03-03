<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');
class Home extends CI_Controller {
	function __construct(){
		parent::__construct();
		$this->layout = 'none';
		$this->load->model('HomeAdminModel');
		$this->load->model('newsModel');
		$this->load->model('newsVersionModel');
		$this->load->model('newsCategoryModel');
		$this->load->model('newsTagsModel');
		$this->load->model('newsTagsVersionModel');
		$this->load->model('tagsModel');
		$this->load->model('newsApprovalCommentModel');
		$this->load->model('authgroup_model','authGroupModel');
		$this->load->model('model_user','userModel');
	}
	function index(){
	    $_SESSION['adminLogin'] = true;
		render('apps/home/home',$data,'apps');
	}
	function pending(){
		if(group_id() == 2){
			$data = $this->newsModel->records_home(array('approval_level' => 0, 'is_revise'=>'1'));
		} else {
			$data = $this->newsModel->records_home(array('approval_level' => $this->newsModel->approvalLevelGroup, 'approval_level !='=>'0'));
		}
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
				$approval = '<a class="btn btn-primary" href="'.base_url().'apps/news/view/'.$value['id'].'" target="_BLANK">Review</>';
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
			$data['data'][$key]['edit'] 		 	= is_edit_news($value['id'],$value['user_id_create'],$approval_level_news,'delete','news');
			$data['data'][$key]['delete'] 		 	= is_delete_news($value['id'],$value['user_id_create'],$approval_level_news);
		}
		render('apps/news/records',$data,'blank');
	}
	function approve(){
		$data = $this->newsModel->records_home(array('approval_level' => 100));
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
				$approval = '<a class="btn btn-primary" href="'.base_url().'apps/news/view/'.$value['id'].'" target="_BLANK">Review</>';
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
			$data['data'][$key]['edit'] 		 	= is_edit_news($value['id'],$value['user_id_create'],$approval_level_news,'delete','news');
			$data['data'][$key]['delete'] 		 	= is_delete_news($value['id'],$value['user_id_create'],$approval_level_news);
		}
		render('apps/news/records',$data,'blank');
	}
	function schedule(){
		$data = $this->newsModel->records_home(array('approval_level' => 100, 'publish_date >' => date('Y-m-d'), 'id_status_publish'=> 2));
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
				$approval = '<a class="btn btn-primary" href="'.base_url().'apps/news/view/'.$value['id'].'" target="_BLANK">Review</>';
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
			$data['data'][$key]['edit'] 		 	= is_edit_news($value['id'],$value['user_id_create'],$approval_level_news,'delete','news');
			$data['data'][$key]['delete'] 		 	= is_delete_news($value['id'],$value['user_id_create'],$approval_level_news);
		}
		render('apps/news/records',$data,'blank');
	}
	
	function all_news(){
		$data = $this->newsModel->records_all(array('approval_level' => 100, 'id_status_publish'=> 2));
		foreach ($data as $key => $value) {
			$ret[$key][] = iso_date_custom_format($value['publish_date'],'d/n/Y');
			$ret[$key][] = 'News Will be Release';
			$ret[$key][] = '#';
			$ret[$key][] = '#2d353c';
			$ret[$key][] = "<a href='".base_url()."apps/news/view/$value[id]' target='_BLANK'>". $value['news_title'] .
			'</a><div class="text-right"><a href="'.base_url().'apps/news/" target="_BLANK">View More >></a></div>';
		}
		echo json_encode($ret);
	}

	function check_valid_file() {
		$file = purify($this->input->post());
		$file_size = $_FILES['userfile']['size'];
		$file_type = $_FILES['userfile']['type'];
		// print_r($_FILES);exit();
        if (intval($file_size/1000000) <= 50) {
        	if (
        		$file_type == 'application/pdf' || 
	        	$file_type == 'application/vnd.openxmlformats-officedocument.wordprocessingml.document' ||
	        	$file_type == 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet' ||
	        	$file_type == 'application/msword'||
	        	$file_type == 'application/vnd.ms-excel'
	        ) {
				$ret['error']   = 0;
				$ret['message'] = 'success';
        	} else {
        		$ret['error']   = 1;
				$ret['message'] = 'The filetype you are attempting to upload is not allowed.';
        	}

        }
        else {  
			$ret['error']   = 1;
			$ret['message'] = 'File Size Max 20Mb';
        }

        echo json_encode($ret);
        exit();
	}
	
	function imagemanager(){
		$post = purify($this->input->post());
		$tglsearch = $post['searchDate'];
		$file = $_FILES;
		if($file){
			$file 	= $_FILES['img'];
	        $fname 	= $file['name'];
	        // $ext	= explode('.',$fname);
	        // $ext	= '.'.$ext[count($ext)-1];
			$maxFileSize = MAX_UPLOAD_SIZE * 1024 * 1024;
	        if(!is_writable(UPLOAD_DIR)){//kalo ga bisa upload
	            $ret['error']   = 1;
	            $ret['message'] = "Directory is readonly";
	        } else if($file['size']>=$maxFileSize){
				$ret['error']   = 1;
	            $ret['message'] = "Max File size is ".MAX_UPLOAD_SIZE."MB";
			}
	        else if($fname){
	            // $folder=UPLOAD_DIR.'temp/';
	            // if(!file_exists($folder)){//kalo blm ada foldernya, bikin dulu
	            //     mkdir($folder);
	            // }
	            // $new_file = rand(1000,9999).$ext;
	            // move_uploaded_file($file['tmp_name'],$folder.$new_file);
	            $upload = upload_file('img','temp');
	            $ret['filename'] = $this->baseUrl."images/article/temp/".$upload['file_name'];
	            $ret['file'] = $upload['file_name'];
				$ret['size'] = $upload['file_size'];
	            $ret['width']= $upload['image_width'];
	            $ret['height']= $upload['image_height'];
	            $ret['message'] = 'success';
	        }
	        echo json_encode($ret);
	        exit;
		}
		$this->load->model('fileManagerModel');
		if($post['id']) { //delete image
			$this->fileManagerModel->delete($post['id'], array('user_id_modify'=>id_user(),'modify_date'=>date('Y-m-d H:i:s')));
		}
		$total_records = $this->fileManagerModel->getTotal("(user_id_create = ".id_user() ." or is_public = 1) and name LIKE '%".$post['searchPicture']."%' and create_date LIKE '%".$tglsearch."%'");
		$per_page = 12;
		$data['pages'] = ceil($total_records/$per_page);
		$data['load'] = base_url().'apps/home/imagemanager';
		// $data['search'] = base_url().'apps/home/search';

		//sanitize post value
		if(isset($post['page'])){
			$page_number = filter_var($post["page"], FILTER_SANITIZE_NUMBER_INT, FILTER_FLAG_STRIP_HIGH);
			if(!is_numeric($page_number)){die('Invalid page number!');} //incase of invalid page number
		}else{
			$page_number = 1;
		}

		//get current starting point of records
		$offset = (($page_number-1) * $per_page);


		$data['list_data'] = $this->fileManagerModel->getAll("(user_id_create = ".id_user() ." or is_public = 1) and name LIKE '%".$post['searchPicture']."%' and create_date LIKE '%".$tglsearch."%'", $per_page, $offset);
		render('apps/filemanager',$data,'blank');

	}
	function imagemanager_save(){
		$post    = $this->input->post();
		$tmp     = $_SERVER['DOCUMENT_ROOT'].$this->baseUrl.'external/'.$post['tmp'];
		$thumbs  =  UPLOAD_DIR.'small/'.$post['name'];
		$ori_tmp =  UPLOAD_DIR.'temp/'.$post['name'];
		$ori     =  UPLOAD_DIR.'large/'.$post['name'];
		rename($tmp,$thumbs);
		rename($ori_tmp,$ori);
		unset($post['tmp']);
		$post['user_id_create'] = id_user();
		$this->load->model('fileManagerModel');
		$this->fileManagerModel->insert($post);
	}

	function log_editors_choice(){
		$data = $this->HomeAdminModel->log_editor_choice(array('approval_level' => 100, 'publish_date <' => date('Y-m-d'), 'id_status_publish'=> 2));
		$date_group = '';
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
				$approval = '<a class="btn btn-primary" href="'.base_url().'apps/news/view/'.$value['id'].'" target="_BLANK">Review</>';
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
			if($value['is_active']==1){
				$is_active = "Not Active";
			} else {
				$is_active = "Active";
			}
			if($date_group != $value['date_group']){
				$date_group = NULL;
			}
			$data['data'][$key]['date_group'] = '';
			if(!$date_group){
				$date_group = $value['date_group'];
				$data['data'][$key]['date_group'] = '<tr><td colspan="9"><b> Date: '.$value['date_group'].' ; Status: ' . $is_active . '</td></b></tr>';
			}

			$data['data'][$key]['is_publisher']          = (group_id() == 4 or group_id() == 5 or group_id() == 1) ? '' : 'invis';
			$data['data'][$key]['news_title']            = quote_form($value['news_title']);
			$data['data'][$key]['publish_date']          = iso_date($value['publish_date']);
			$data['data'][$key]['expected_publish_date'] = iso_date($value['expected_publish_date']);
			$data['data'][$key]['modify_date']           = iso_date($value['modify_date']);
			$data['data'][$key]['approval_level']        = $approval;
			$data['data'][$key]['edit']                  = is_edit_news($value['id'],$value['user_id_create'],$approval_level_news,'delete','news');
			$data['data'][$key]['delete']                = is_delete_news($value['id'],$value['user_id_create'],$approval_level_news);
		}
		render('apps/home/log_editors_choice',$data,'blank');
	}

	function log_top_content(){
		$data = $this->HomeAdminModel->log_top_content(array('approval_level' => 100, 'publish_date <' => date('Y-m-d'), 'id_status_publish'=> 2));
		$date_group = '';
		foreach ($data['data'] as $key => $value) {
			$approval_level_news = $value['approval_level'];
			$group               = $this->authGroupModel->fetchRow(array('approval_level'=>$approval_level_news));
			// $approval = $approval_level_news == 0 ? 'Draft' : ('Sent to '.$group['grup']);
			if($approval_level_news == 0 && $value['is_revise']== 1){
				$approval = 'Revise (writter)';
			}
			else if($approval_level_news == 1 && $value['is_revise']== 1){
				$approval = 'Revise (editor)';
			}
			else if($this->newsModel->approvalLevelGroup == $approval_level_news && $approval_level_news != 0){
				$approval = '<a class="btn btn-primary" href="'.base_url().'apps/news/view/'.$value['id'].'" target="_BLANK">Review</>';
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
			if($value['is_active']==1){
				$is_active = "Not Active";
			} else {
				$is_active = "Active";
			}
			if($date_group != $value['date_group']){
				$date_group = NULL;
			}
			$data['data'][$key]['date_group'] = '';
			if($value['category_top']==0){
				$value['category_top'] = 'Home';
			} else {
				// $value['category_top'] = $this->db->get_where('news_category', array('id'=>$value['category_top']))->row_array()['name'];
			}
			if(!$date_group){
				$date_group = $value['date_group'];
				$data['data'][$key]['date_group'] = '<tr><td colspan="9"><b> Date: '. $value['date_group'] .' ; Category: ' . $value['category_top'] . ' ; Status: ' . $is_active . ' </td></b></tr>';
			}
			$data['data'][$key]['is_publisher']          = (group_id() == 4 or group_id() == 5 or group_id() == 1) ? '' : 'invis';
			$data['data'][$key]['news_title']            = quote_form($value['news_title']);
			$data['data'][$key]['publish_date']          = iso_date($value['publish_date']);
			$data['data'][$key]['expected_publish_date'] = iso_date($value['expected_publish_date']);
			$data['data'][$key]['modify_date']           = iso_date($value['modify_date']);
			$data['data'][$key]['approval_level']        = $approval;
			$data['data'][$key]['edit']                  = is_edit_news($value['id'],$value['user_id_create'],$approval_level_news,'delete','news');
			$data['data'][$key]['delete']                = is_delete_news($value['id'],$value['user_id_create'],$approval_level_news);
		}
		render('apps/home/log_top_content',$data,'blank');
	}
}