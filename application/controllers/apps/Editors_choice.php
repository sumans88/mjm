<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Editors_Choice extends CI_Controller {
	function __construct(){
		parent::__construct();
		$this->load->model('Editorschoicemodel');
		$this->load->model('languagemodel');
		$this->load->library('session');
	}
	function index(){
		// $data['list_news_category'] 	= selectlist2(array('table'=>'news_category','title'=>'All Category','selected'=>$data['id_news_category']));
		// $data['list_status_publish'] 	= selectlist2(array('table'=>'status_publish','title'=>'All Status','selected'=>$data['id_status_publish']));
		$data['list_languages'] 		= selectlist2(array('table'=>'language','title'=>'Select Language','where'=>'is_delete = 0','name'=>'name'));
		render('apps/editors_choice/index',$data,'apps');
	}
	
	function records(){
		$data = $this->Editorschoicemodel->records();
		foreach ($data['data'] as $key => $value) {
			$data['data'][$key]['news_title'] 		= quote_form($value['news_title']);
			$data['data'][$key]['publish_date'] 	= iso_date($value['publish_date']);
			$data['data'][$key]['approval_level'] 	= $approval;
			$data['data'][$key]['edit'] 		 	= is_edit_news($value['id'],$value['user_id_create'],$approval_level_news,'delete');
			$data['data'][$key]['delete'] 		 	= is_delete_news($value['id'],$value['user_id_create'],$approval_level_news);
		}
		render('apps/editors_choice/records',$data,'blank');
	}
	function del(){
		$data['is_editor_choice'] = 0;
		$this->Editorschoicemodel->update($data,$this->input->post('iddel'));
	}
	
	/*function get_editor_choice(){
		$post 				= $this->input->post();
		$id_lang_session	= array(
			'id_lang' => $post['id_lang'],
			);
		$this->session->set_userdata($id_lang_session);
		$ret 	= $this->Editorschoicemodel->findby(array('a.id_lang'=>$post['id_lang']));
		echo json_encode($ret);
	}*/

	function get_editor_choice(){
		$post 				= $this->input->post();

		$ret['error']		= 1;
		if ($post['id_lang']) {
			$id_lang_session	= array(
				'id_lang' => $post['id_lang'],
				);
			$this->session->set_userdata($id_lang_session);
			$ret 	= $this->Editorschoicemodel->findby(array('a.id_lang'=>$post['id_lang']));
			
			/*jika not available maka akan mengambl title dari news parent yang available*/
			foreach ($ret as $key => $value) {
				if ($value['is_not_available'] == 1 && $value['id_status_publish'] == 1) {

					if ($value['id_parent_lang'] != Null) {
						$whr_get['id'] = $value['id_parent_lang'];
					} else {
						$whr_get['id_parent_lang'] = $value['id'];
					}
					$get_news_available = $this->db->get_where('news',$whr_get)->row_array();

					$ret[$key]['news_title'] = $get_news_available['news_title'].' (Only '.ucwords(db_get_one('language','name',array('id'=>$get_news_available['id_lang']))).' Language News)';
				}
			}
			/*end jika not available maka akan mengambl title dari news parent yang available*/
			unset($ret['error']);
		}
		echo json_encode($ret);
	}

	function record_select_category(){
		$id_lang 	= $this->session->userdata('id_lang');
		$data 		= $this->Editorschoicemodel->get_all_news(0,$id_lang);
		render('apps/editors_choice/record_select_category',$data,'blank');
	}
	function select_category(){
		$id_lang = $this->input->post('id_lang');
		$this->session->set_userdata(array('id_lang'=>$id_lang));
		render('apps/editors_choice/select_category',$data,'blank');
	}

	function process(){
		$post 							= purify($this->input->post(Null,TRUE));
		$value							= ($post['value']) ? explode(',',$post['value']) : '';
		$id_lang						= $post['id_lang'];
		// $data_insert['id_lang'] 		= $id_lang;

		$sort = 0;
		$idin = array();
		if ($value) {
			for ($x = 0; $x < count($value); $x++) {
				$data 							= explode('_',$value[$x]);
				$id1 							= $data[0];
				$idin[]	= $id1;
				
				$id2    						= ($id_lang==1) ? db_get_one('news','id',array('id_parent_lang'=>$id1)) : db_get_one('news','id_parent_lang',array('id'=>$id1));
				$idin[]	= $id2;
				
				$idx = array($id1,$id2);
				
				$datins1['create_date']            = date('Y-m-d H:i:s');
				$datins1['user_id_create']         = id_user();
				$datins1['modify_date']            = date('Y-m-d H:i:s');
				$datins1['user_id_modify']         = id_user();
				$datins1['sort_is_editors_choice'] = ++$sort;
				$datins1['is_editor_choice']       = 1;
				// $data_insert['id_lang'] 				= $id_lang;
						  
						  // $this->db->where('id_status_publish',2);
						  // $this->db->where('is_not_available',0);
						  $this->db->where_in('id',$idx);
				$insert = $this->db->update('news',$datins1);
				// echo $this->db->last_query();exit();
				// popular_article_generate(1,$id_lang);
			}
		} else {
			$idin = '';
		}
		
		// print_r($idin);exit();
		// $this->db->where('id_lang',$id_lang);
		if ($idin) {
			$this->db->where_not_in('id',$idin);
		}
		$this->db->update('news',array('is_editor_choice' => 0, 'sort_is_editors_choice'=>NULL));

		if($insert){
			$data['error_status'] = 'success';
			$data['msg'] = 'Success to save';
		}else{
			$data['error_status'] = 'error';
			$data['msg'] = "there's somethings wrong";
		}
		echo json_encode($data);
	}
	
	/*function process(){
		$value							= explode(',',$_POST['value']);
		$id_lang						= $_POST['id_lang'];
		$data_insert['create_date'] 	= date('Y-m-d H:i:s');
		$data_insert['user_id_create'] 	= id_user();
		$data_insert['modify_date'] 	= date('Y-m-d H:i:s');
		$data_insert['user_id_modify'] 	= id_user();
		$data_insert['id_lang'] 		= $id_lang;
		$sort = 0;
		$this->db->update('news',array('is_editor_choice' => 0, 'sort_is_editors_choice'=>NULL), array('id_lang'=>$id_lang));
		for ($x = 0; $x < count($value); $x++) {
			$data 									= explode('_',$value[$x]);
			$data_insert['sort_is_editors_choice'] 	= ++$sort;
			$data_insert['is_editor_choice'] 		= 1;
			$data_insert['id_lang'] 				= $id_lang;
			$insert = $this->db->update('news',$data_insert,array('id' => $data[0]));
			// popular_article_generate(1,$id_lang);		
		}
		
		if($insert){
			$data['error_status'] = 'success';
			$data['msg'] = 'Success to save';
		}else{
			$data['error_status'] = 'error';
			$data['msg'] = "there's somethings wrong";
		}
		echo json_encode($data);
	}*/
}

/* End of file slideshow.php */
/* Location: ./application/controllers/apps/editors_choice.php */