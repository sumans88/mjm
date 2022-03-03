<?php
class mailchimpModel extends  CI_Model{
	
	function __construct(){
		parent::__construct();
		$this->load->library('Mailchimp_library'); 
	}
	
	function subscribe_mailchimp($id_list,$email,$namadepan='',$namabelakang=''){
		$result = $this->mailchimp_library->call('lists/subscribe', array(
			'id'                => $id_list,
			'email'             => array('email'=>$email),
			'merge_vars'        => array('FNAME'=>$namadepan, 'LNAME'=>$namabelakang),
			'double_optin'      => false,
			'update_existing'   => true,
			'replace_interests' => false,
			'send_welcome'      => false,
		));
		return $result;
	}
	function unsubscribe_mailchimp($id_list,$email){
		$result = $this->mailchimp_library->call('lists/unsubscribe', array(
			'id'                	=> $id_list,
			'email'         	=> array('email'=>$email),
			'send_goodbye'   	=> false,
			'send_notify' 		=> false,
		));
		return $result;	
	}
	function lists_subscribe($id_list){
		$result = $this->mailchimp_library->call('lists/members', array(
			'id'    => $id_list,
		));
		return $result;	
	}
	function check_subscribe($id_list,$email){
		$result = $this->mailchimp_library->call('lists/member-info', array(
			'id'    => $id_list,
			'emails'         	=> array(array('email'=>$email)),
		));
		return $result;	
	}
	function check_subscribe_by($id_list,$data){
		$result = $this->mailchimp_library->call('lists/member-info', array(
			'id'    => $id_list,
			'emails'         	=> array($data),
		));
		return $result;	
	}
 }
