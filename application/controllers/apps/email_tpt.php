<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class email_tpt extends CI_Controller {
	function __construct(){
		parent::__construct();
		$this->load->model('EmailTmpModel');
		$this->load->model('languageModel');
	}
	function index(){
		$data['list_status_publish'] = selectlist2(array('table'=>'status_publish','title'=>'All Status','selected'=>$data['id_status_publish']));
		$data['list_ref_email_category'] = selectlist2(array('table'=>'ref_email_category','title'=>'All Category','selected'=>$data['id_ref_email_category']));
		render('apps/email_tmp/index',$data,'apps');
	}
	public function add($id=''){
		if($id){
			// $data = $this->EmailTmpModel->findById($id);
			$datas 	= $this->EmailTmpModel->selectData($id);

            if(!$datas){
				die('404');
			}

			$data['judul']	= 'Edit';
			$data['proses']	= 'Update';
			$data 			= quote_form($data);
		}else{
			$data['judul']			= 'Add';
			$data['proses']			= 'Save';
			$data['template_name']  = '';
			$data['subject']		= '';
			$data['code']		= '';
			$data['page_content']	= '';
            $data['id'] 			= '';
		}
		$this->db->limit(1);
		$data['list_lang']	= $this->languageModel->langName();
		$data['id']	= $datas[0]['id'];

		foreach($data['list_lang'] as $key => $value){
			$data['list_lang'][$key]['invis']			= ($key==0) ? '' : 'hide';
			$data['list_lang'][$key]['active']			= ($key==0) ? 'active in' : '';
			$data['list_lang'][$key]['validation']		= ($key==0) ? 'true' : 'false';
			$data['list_lang'][$key]['nomor']			= $key;

			$data['list_lang'][$key]['template_name'] 			= $datas[$key]['template_name'];
			$data['list_lang'][$key]['subject'] 				= $datas[$key]['subject'];
			$data['list_lang'][$key]['page_content'] 			= $datas[$key]['page_content'];
			$data['list_lang'][$key]['id'] 						= $datas[$key]['id'];
			$data['list_lang'][$key]['code']					= $datas[$key]['code'];

			
			$data['list_lang'][$key]['list_status_publish'] 	= selectlist2(array('table'=>'status_publish','title'=>'Select Status','selected'=>$datas[$key]['id_status_publish']));
			$data['list_lang'][$key]['list_ref_email_category'] = selectlist2(array('table'=>'ref_email_category','title'=>'All Category','selected'=>$datas[$key]['id_ref_email_category']));
			
	        $img_thumb											= image($datas[$key]['img'],'small');//is_file_exsist(UPLOAD_DIR.'small/',$data['img']) ? ($this->baseUrl.'uploads/small/'.$data['img']) : '';
			$imagemanager										= imagemanager('img',$img_thumb,750,186,$key);
			$data['list_lang'][$key]['img']						= $imagemanager['browse'];
			$data['list_lang'][$key]['imagemanager_config'] 	= $imagemanager['config'];
		}

		$data['list_lang2']		= $data['list_lang'];
	render('apps/email_tmp/add',$data,'apps');
	}
	function records(){
		$data = $this->EmailTmpModel->records();
		// echo $this->db->last_query();
		foreach ($data['data'] as $key => $value) {
			$data['data'][$key]['slideshow_title'] 		= quote_form($value['slideshow_title']);
			$data['data'][$key]['publish_date'] 	= iso_date($value['publish_date']);
			$data['data'][$key]['approval_level'] 	= $approval;
			// $data['data'][$key]['code'] 	= $value['code'];
		}
		render('apps/email_tmp/records',$data,'blank');
	}
	function proses($idedit=''){
		$id_user =  id_user();
		$this->layout 			= 'none';
		$page_content 			= $this->input->post('page_content');
		$post 					= purify($this->input->post());
		// print_r($post);exit();
		$post['page_content']	= $page_content;
		$ret['error']			= 1;
		$id_parent_lang 		= NULL;
		$this->db->trans_start();

		foreach ($post['id_ref_email_category'] as $key => $value) {
	        
	        $this->form_validation->set_rules('id_ref_email_category', '"Email Category"', 'required'); 
			$this->form_validation->set_rules('template_name', '"Template Name"', 'required'); 
			$this->form_validation->set_rules('subject', '"Subject"', 'required'); 
			$this->form_validation->set_rules('page_content', '"Content"', 'required');
	        $this->form_validation->set_rules('id_status_publish', '"Status Publish"', 'required'); 

			if ($this->form_validation->run() == FALSE){
				$ret['message']  = validation_errors(' ',' ');
			}else{   
				if($key==0){
					$idedit 				= $post['id'][$key];
				 	$id_ref_email_category	= $post['id_ref_email_category'][$key];
				 	$id_status_publish		= $post['id_status_publish'][$key];
				}

				$data_save['id_ref_email_category'] 	= $id_ref_email_category;
				$data_save['template_name'] 			= $post['template_name'][$key];
				$data_save['subject'] 					= $post['subject'][$key];
				// $data_save['code'] 						= $post['code'][$key];
				/*$data_save['page_content'] 				= str_replace(
															array('%7B', '%7D'), 
															array('{', '}'), 
															$post['page_content'][$key]
													      );*/
				$data_save['page_content'] 				= htmlspecialchars_decode(urldecode($post['page_content'.$key.'']));													     
				$data_save['id_status_publish'] 		= $id_status_publish;
				$data_save['id_lang'] 					= $post['id_lang'][$key];
				$data_save['id_parent_lang']			= $id_parent_lang;
				$data_save['id'] 						= $post['id'][$key];				
				
				$idLang 								= $post['id_lang'][$key];

				if($idedit){
					if($key==0){
						auth_update();
						$ret['message'] = 'Update Success';
						$act			= "Update Email Template";
						$iddata 		= $this->EmailTmpModel->update($data_save,$idedit,$idLang);
					}else{
						auth_update();
						$ret['message'] = 'Update Success';
						$act			= "Update Email Template";
						$iddata			= $this->EmailTmpModel->updateKedua($data_save,$idedit,$idLang);
					}
				}else{
					auth_insert();
					$ret['message'] = 'Insert Success';
					$act			= "Insert News";
					$iddata 		= $this->EmailTmpModel->insert($data_save,$idLang);
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
		$this->EmailTmpModel->delete($id);
		$this->EmailTmpModel->delete2($id);
		$this->db->trans_complete();
	}
	
}

/* End of file slideshow.php */
/* Location: ./application/controllers/apps/slideshow.php */