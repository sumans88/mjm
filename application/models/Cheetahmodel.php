<?php
class cheetahModel extends  CI_Model{
	
	function __construct(){
		parent::__construct();
		$this->load->library('Cheetah_library'); 
	}
	function getuser($email='', $subscriber_list_id=array()) {
		$result = $this->cheetah_library->getuser($email, $subscriber_list_id);
		return $result;
	}
	function signup($email='', $first_name='', $last_name='', $subscriber_list_id=array(), $source_id='',$status) {
		$result = $this->cheetah_library->signup($email, $first_name, $last_name, $subscriber_list_id, $source_id,$status);
		return $result;
	}
	function change_email($email='', $new_email='', $subscriber_list_id=array()) {
		$result = $this->cheetah_library->change_email($email, $new_email, $subscriber_list_id);
		return $result;
	}
	function unsubscribe($email='', $first_name='', $last_name='', $subscriber_list_id=array(), $source_id='',$status='Unsubscribe') {
		$result = $this->cheetah_library->signup($email, $first_name, $last_name, $subscriber_list_id, $source_id,$status);
		return $result;
	}
 }
