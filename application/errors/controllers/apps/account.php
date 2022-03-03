<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');
class Account extends CI_Controller { 
	function index(){
		session_start();
		$this->data['search'] = get('id');
		grid($_GET,'Model_data','account');
        render('apps/setting/account',$data,'apps');
	}
	function proses(){
		$post 					= $this->input->post();
		$idedit					= $this->uri->segment(4);
		unset($post['idedit']);
		$this->form_validation->set_rules('type', 'Type', 'required');
		$this->form_validation->set_rules('account_name', 'Account Name', 'required');
		$this->form_validation->set_rules('account_link', 'Account Link', 'required');
		
		if ($this->form_validation->run() == FALSE){
			echo ' '.validation_errors(' ','<br>');
		}
		else{
			$post['company_id'] = company_id();
			$where 				= ($idedit) ? "and id not in ($idedit)" : '';
			$cek_code 			= db_get_one('account','id',"account_name = '$post[account_name]' $where");
			if($cek_code){
				echo  " Account Name $post[account_name] already exsist";
				exit;
			}
			if($idedit){
				$this->db->update('account',$post,"id = '$idedit'");
				$msg = '1Update Success';
			}
			else{
				$this->db->insert('account',$post);
				$msg = '1Insert Success';
			}
			echo $msg;
		}
	}
	function delete(){
		auth_delete();
		$post		= $this->input->post();
		$iddel		= explode(',',$post['iddel']);
		$this->db->where_in('id', $iddel);
		$this->db->delete('account',$where);
		echo "Delete Success";
	}


	function tez(){
		$this->load->helper('connect');
		$data = fbpage('tezterpage');
        render('apps/setting/tez/tez',$data,'apps');
	}
	function post(){
		$this->load->helper('connect');
		$text = $this->input->post('text');
		post($text,'katapediapage');
	}
	function reply(){
		$this->load->helper('connect');
		$text = $this->input->post('text');
		$id = $this->input->post('id');
		reply($text,$id);
	}
	function message(){
        render('apps/setting/tez/message',$data,'apps');
	}
	function sent_message(){
		$this->load->helper('connect');
		$text 	= $this->input->post('text');
		$to 	= $this->input->post('to');
		sent_message($text,$to);
	}
	function pagelist($id){
		if($id){
			$data['data'] = $this->db->order_by('id','desc')->get_where('fb_page',array("account_id"=>$id))->result_array();
			render('apps/setting/pagelist',$data,'');
		}
	}
	function addpage(){
		$post = $this->input->post();
		if($post){
			$cek = db_get_one('fb_page','id',array('page_id'=>$post['page_id']));
			if($cek){
				echo 0;
			}
			else{
				$this->db->insert('fb_page',$post);
				echo 1;
			}
		}
	}
	function delete_page(){
		auth_delete();
		$iddel		= $this->input->post('iddel');
		$this->db->delete('fb_page',"id = '$iddel'");
		echo $this->db->last_query();
	}



}