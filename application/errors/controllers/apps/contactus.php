<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Contactus extends CI_Controller {
	function __construct(){
		parent::__construct();
		$this->load->model('contactUsModel');
		$this->load->model('contactUsReplyModel');
	}
	function index(){
		// $data['list_cat'] = selectlist2(array('table'=>'ref_kategori_logframe','title'=>'All Category'));
		render('apps/contactus/index',$data,'apps');
	}
	function proses($idedit=''){
		$this->layout 			= 'none';
		$post 					= purify($this->input->post());
		$ret['error']			= 1;
		$where['uri_path']		= $post['uri_path'];
		if($idedit){
			$where['id !=']		= $idedit;
		}
		$unik 					= $this->contactUsModel->findBy($where);
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
					$this->contactUsModel->update($post,$idedit);
				}
				else{
					auth_insert();
					$ret['message'] = 'Insert Success';
					$act			= "Insert Pages";
					$this->contactUsModel->insert($post);
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
		$data = $this->contactUsModel->records();
		foreach ($data['data'] as $key => $value) {
			// $data['data'][$key]['page_name'] = quote_form($value['page_name']);
			$message = substr($value['komentar'],0,150);
			if(strlen($value['komentar']) > 150){
				$message .= ' ...';
			}
			$data_reply = $this->contactUsReplyModel->findBy(array('id_contact_us'=>$value['id']));
			if($data_reply){
				$data['data'][$key]['status'] = 'Replied';
			} else {
				$data['data'][$key]['status'] = '-';
			}

			$data['data'][$key]['komentar'] = $message;
		}
		render('apps/contactus/records',$data,'blank');
	}
	function detail($id){
		$data = $this->contactUsModel->findById($id);
		$data['reply'] = $this->contactUsReplyModel->findBy(array('id_contact_us'=>$id));
		foreach ($data['reply'] as $key => $value) {
			$data['reply'][$key]['create_datex'] = iso_date_time($value['create_date']);
			$data['reply'][$key]['messagex'] = $value['message'];
		}
		render('apps/contactus/detail',$data,'blank');
	}
	function reply(){
		$post = $this->input->post();
		if($post){
			$contactus  = $this->contactUsModel->findById($post['id_contact_us']);

			$data['content'] = $post['message'];
			$data['name'] = $contactus['fullname'];
			$data['question'] = $contactus['message'];
			$data['date'] = iso_date_time($contactus['create_date']);
			$data['topic_name'] = $contactus['topic'];
			$data = array_merge($data, $contactus);
			$sent = sent_email_by_category(7,$data,$contactus['email']);
			if($sent['error']  === 0){
				$this->contactUsReplyModel->insert($post);
			}
			else{
				$ret['error'] =  1;
				$ret['message'] = '';
			}
			echo json_encode($sent);
		}
	}

	function export_to_excel(){
		$post = $this->input->post();

		$alias['search_title'] = 'a.name';
		$alias['search_topic'] = 'b.name';
		where_grid($post, $alias);
		$data['data'] = $this->contactUsModel->export_to_excel();
		$i=1;
		foreach ($data['data'] as $key => $value) {
			$message = substr($value['komentar'],0,150);
			if(strlen($value['komentar']) > 150){
				$message .= ' ...';
			}
			$data['data'][$key]['komentar'] = $message;
			$data['data'][$key]['nomor'] = $i++;
		}
		render('apps/contactus/export_to_excel',$data,'blank');
		export_to('Contact Us.xls');
	}
}

/* End of file contactus.php */
/* Location: ./application/controllers/apps/contactus.php */