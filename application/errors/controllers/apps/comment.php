<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Comment extends CI_Controller {
	function __construct(){
		parent::__construct();
		$this->load->model('commentModel');
	}
	function index(){
		$data['list_status_publish'] = selectlist2(array('table'=>'status_publish','title'=>'All Status','selected'=>$data['is_delete']));
		$data['list_news'] = selectlist2(array('table'=>'news','title'=>'All News','selected'=>$data['id_news'], 'name'=>'news_title'));

		render('apps/comment/index',$data,'apps');
	}
	public function add($id=''){
		if($id){
			$data = $this->commentModel->findByEdit(array('a.id'=>$id),1);
		if(!$data){
				    die('404');
			    }
			    $data['judul']	= 'Edit';
			    $data['proses']	= 'Update';
			    $data = quote_form($data);
			    $data['publish_date'] = iso_date($data['publish_date']);
			    $data['is_background_checked'] 	= ($data['is_background'] == 1) ? 'checked':'';
			    $data['position_left_checked'] 	= ($data['position'] == 1) ? 'checked':'';
			    $data['position_right_checked'] = ($data['position'] == 2) ? 'checked':'';
		    }
		    else{
			    $data['judul']			= 'Add';
			    $data['proses']			= 'Simpan';
			    $data['slideshow_title']		= '';
			    $data['description']		= '';
			    $data['publish_date']	= date('d-m-Y');
			    $data['id'] 			= '';
		$data['url'] 			= '';
		}
		$img_thumb					= image($data['img'],'small');//is_file_exsist(UPLOAD_DIR.'small/',$data['img']) ? ($this->baseUrl.'uploads/small/'.$data['img']) : '';
		$imagemanager				= imagemanager('img',$img_thumb,750,186);
		$data['img']				= $imagemanager['browse'];
		$data['imagemanager_config']= $imagemanager['config'];

		$data['list_status_publish'] = selectlist2(array('table'=>'status_publish','title'=>'All Status','selected'=>$data['is_delete']));

		render('apps/comment/add',$data,'apps');
	}
	public function view($id=''){
		if($id){
			$data = $this->commentModel->findById($id);
			$data['img_thumb'] = image($data['img'],'small');
			$data['img_ori'] = image($data['img'],'large');
			$data['style_position'] ='';
			$data['style_background']='';
			if(!$data){
				die('404');
			}
			if($data['position']==2){
				$data['style_position'] = '<style>.slider-caption{left:51% !important;}</style>';
			}
			if($data['is_background']==1){
				$data['style_background'] = '<style>.slider-caption{background-color:rgba(169,172,174,0.7) !important;}</style>';
			}
			$data['create_date'] = iso_date_time($data['create_date']);

		}

		$data['list_status_publish'] = selectlist2(array('table'=>'status_publish','title'=>'All Status'));

		render('apps/comment/view',$data,'blank');
	}
	function records(){
		$data = $this->commentModel->records();
		foreach ($data['data'] as $key => $value) {
			$data['data'][$key]['comment'] 		= quote_form($value['comment']);
			$data['data'][$key]['create_date'] 	= iso_date_time($value['create_date']);
			if($value['is_admin']==1){
				$value_data = $this->commentModel->findbyadmin(array('id'=>$value['id']),1);
				$data['data'][$key]['author'] = '<span class="label-warning">'.$value_data['namadepan'].'</span>';
			}
		}
		render('apps/comment/records',$data,'blank');
	}
	
	
	function proses($idedit=''){
		$id_user =  id_user();
		$this->layout 			= 'none';
		$post 					= purify($this->input->post());
		$ret['error']			= 1;
		$this->form_validation->set_rules('commentar', '"Commentar"', 'required'); 
		$this->form_validation->set_rules('flag', '"Flag Count"', 'required|integer'); 
		$this->form_validation->set_rules('is_delete', '"Status"', 'required'); 
		if ($this->form_validation->run() == FALSE){
			$ret['message']  = validation_errors(' ',' ');
		}
		else{   
			$this->db->trans_start();   
				if($idedit){
					auth_update();
					$post['user_id_modify'] = id_user();
					$ret['message'] = 'Update Success';
					$act			= "Update News";
					$this->commentModel->update($post,$idedit);
				}
				else{
					auth_insert();
					$post['user_id_create'] = id_user();

					$ret['message'] = 'Insert Success';
					$act			= "Insert News";
					$idedit = $this->commentModel->insert($post);
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
		$this->commentModel->delete($id);
		$this->db->trans_complete();
	}
	function multiple_delete(){
		$post = purify($this->input->post());
		if($post){
			foreach($post['id_commentar'] as $idx => $val){
				$this->db->trans_start();   
				$this->commentModel->delete($post['id_commentar'][$idx]);
				$this->db->trans_complete();
			}
		}
		$ret['error'] = 0;
		echo json_encode($ret);
	}
	
}

/* End of file comment.php */
/* Location: ./application/controllers/apps/comment.php */