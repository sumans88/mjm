<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Ask_expert extends CI_Controller {
	function __construct(){
		parent::__construct();
		$this->load->model('memberAskExpertModel');
	}
	function index(){
		// $data['list_cat'] = selectlist2(array('table'=>'ref_kategori_logframe','title'=>'All Category'));
		render('apps/ask_expert/index',$data,'apps');
	}
	function proses($idedit=''){
		$this->layout 			= 'none';
		$post 					= purify($this->input->post());
		$ret['error']			= 1;
		$where['uri_path']		= $post['uri_path'];
		if($idedit){
			$where['id !=']		= $idedit;
		}
		$unik 					= $this->memberAskExpertModel->findBy($where);
		$this->form_validation->set_rules('page_name', '"page Name"', 'required'); 
		$this->form_validation->set_rules('uri_path', '"Page URL"', 'required'); 
		$this->form_validation->set_rules('teaser', '"Teaser"', 'required'); 
		$this->form_validation->set_rules('page_content', '"Content"', 'required'); 
		if ($this->form_validation->run() == FALSE){
			$ret['message']  = validation_errors(' ',' ');
		}
		else if($unik){
			$ret['message']	= "Page URL $post[uri_path] already taken";
		}
		else{   
			$this->db->trans_start();   
				if($idedit){
					auth_update();
					$ret['message'] = 'Update Success';
					$act			= "Update Pages";
					if(!$post['img']){
						unset($post['img']);
					}
					$this->memberAskExpertModel->update($post,$idedit);
				}
				else{
					auth_insert();
					$ret['message'] = 'Insert Success';
					$act			= "Insert Pages";
					$this->memberAskExpertModel->insert($post);
				}
			detail_log();
			insert_log($act);
			$this->db->trans_complete();
			set_flash_session('message',$ret['message']);
			$ret['error'] = 0;
		}
		echo json_encode($ret);
	}
	function records(){
		$data = $this->memberAskExpertModel->records();
		foreach ($data['data'] as $key => $value) {
			$data['data'][$key]['create_date'] 	= iso_date_time($value['create_date']);

			// $data['data'][$key]['page_name'] = quote_form($value['page_name']);
		}
		render('apps/ask_expert/records',$data,'blank');
	}
	function detail($id){
		$data = $this->memberAskExpertModel->fetchRow(array('a.id'=>$id));
		// $data['reply'] = $this->ask_expertReplyModel->findBy(array('id_contact_us'=>$id));
		//foreach ($data['reply'] as $key => $value) {
		 	//$data['reply'][$key]['publish_date'] = iso_date($value['publish_date']);
		// 	$data['reply'][$key]['messagex'] = $value['message'];
		//}
		$data['publish_date'] = iso_date($data['publish_date']);
		$data['id_contact_us'] = $id;
		$data['list_status_publish'] = selectlist2(array('table'=>'status_publish','title'=>'Select Status','selected'=>$data['id_status_publish']));
		$data['list_news'] = selectlist2(array('table'=>'news','where'=> 'is_experts=1 and is_qa=1','title'=>'Article Ask the Expert', 'id'=>'id','name'=>'news_title','selected'=>$data['id_news'] ));
		render('apps/ask_expert/detail',$data,'blank');
	}
	function reply(){
		$post = $this->input->post();
		if($post){
			$id = $post['id_contact_us'];#id member ask expert;
			$ask_expert  = $this->memberAskExpertModel->findById($id);
			
			$data['content'] 		= $post['message'];
			$data['name'] 			= $ask_expert['fullname'];
			$data['question'] 		= $ask_expert['message'];
			$data['publish_date'] 			= iso_date($post['publish_date']);
			$data['date'] 			= iso_date_time($ask_expert['create_date']);
			$email 					= db_get_one('t_aegon_profile_member','email',array('id'=>$ask_expert['id_user']));
			//$sent 					= sent_email_by_category(7,$data,$email);
			//if($sent['error']  === 0){
				$update['answer_descr'] = $post['message'];
				$update['id_status_publish'] = $post['id_status_publish'];
				$update['publish_date'] = iso_date($post['publish_date']);
				$update['create_date_reply'] = date('Y-m-d H:i:s');
				$update['id_news'] = $post['id_news'];
				$this->memberAskExpertModel->update($update,$id);
			//}
			//else{
			//	$sent['error'] =  1;
			//	$sent['message'] = '';
			//}
				$sent['error'] =  0;
				//$sent['message'] = 'Success to Reply';
			echo json_encode($sent);
		}
	}

	function export_to_excel(){
		$post = $this->input->post();

		$alias['search_category'] = 'b.title';
		$alias['search_namadepan'] = 'd.namadepan';
		$alias['search_datestart'] = 'a.datestart';
		$alias['search_dateend'] = 'a.dateend';
		$alias['search_email'] = 'd.email';

		$post['search_datestart'] = $post['datestart'];
		$post['search_dateend'] = $post['dateend'];
		
		where_grid($post, $alias);
		$data['data'] = $this->memberAskExpertModel->export_to_excel();
		$i=1;
		foreach ($data['data'] as $key => $value) {
			$data['data'][$key]['create_date'] 	= iso_date_time($value['create_date']);
			$data['data'][$key]['nomor'] = $i++;
		}
		render('apps/ask_expert/export_to_excel',$data,'blank');
		export_to('Ask Expert.xls');
	}
}

/* End of file ask_expert.php */
/* Location: ./application/controllers/apps/ask_expert.php */