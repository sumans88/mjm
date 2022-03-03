<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Editors_Choice extends CI_Controller {
	function __construct(){
		parent::__construct();
		$this->load->model('EditorsChoiceModel');
	}
	function index(){
		$data['list_news_category'] = selectlist2(array('table'=>'news_category','title'=>'All Category','selected'=>$data['id_news_category']));
		$data['list_status_publish'] = selectlist2(array('table'=>'status_publish','title'=>'All Status','selected'=>$data['id_status_publish']));
		render('apps/editors_choice/index',$data,'apps');
	}
	
	function records(){
		$data = $this->EditorsChoiceModel->records();
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
		$this->EditorsChoiceModel->update($data,$this->input->post('iddel'));
	}
	
	function get_editor_choice(){
		$data = $this->EditorsChoiceModel->findby();
		echo json_encode($data);
	}

	function record_select_category($ids,$is_featured){
		$data = $this->EditorsChoiceModel->get_all_news();
		render('apps/editors_choice/record_select_category',$data,'blank');
	}
	function select_category(){
		render('apps/editors_choice/select_category',$data,'blank');
	}
	
	function process(){
		$value	= explode(',',$_POST['value']);
		$data_insert['create_date'] 	= date('Y-m-d H:i:s');
		$data_insert['user_id_create'] = id_user();
		$sort=0;
		$this->db->update('news',array('is_editor_choice' => 0, 'sort_is_editors_choice'=>NULL), array('id_status_publish' => 2));
		$data_now = date('Y-m-d H:i:s');
		$this->db->update('editors_choice_log',
			array(
				'is_delete' => 1,
				'modify_date' => $data_now,
				'user_id_modify' => id_user()
			), 
			array(
				'is_delete' => 0
			)
		);
		for ($x = 0; $x < count($value); $x++) {
			$data = explode('_',$value[$x]);
			$data_insert['sort_is_editors_choice'] = ++$sort;
			$data_insert['is_editor_choice'] = 1;
			
			$insert = $this->db->update('news',$data_insert,array('id' => $data[0]));	
			$data_insert_log['create_date'] 	= date('Y-m-d H:i:s');
			$data_insert_log['user_id_create'] = id_user();
			$data_insert_log['sort'] = $sort;
			$data_insert_log['id_news'] = $data[0];
			$this->db->insert('editors_choice_log',$data_insert_log);				
		}
		
		if($insert){
			$data['error_status'] = 'success';
			$data['msg'] = 'Success to save';
		}else{
			$data['msg'] = "there's somethings wrong";
		}
		echo json_encode($data);
	}
}

/* End of file slideshow.php */
/* Location: ./application/controllers/apps/editors_choice.php */