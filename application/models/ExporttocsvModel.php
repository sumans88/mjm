<?php
class ExporttocsvModel extends  CI_Model{
	function __construct(){
	   parent::__construct();
	       
	}
	
	function all_member_newsletter_subscriber($where,$is_single_row=0){
		//$where['is_active'] = 1;
		$this->db->select('*');
		if($is_single_row==1){
			return 	$this->db->get_where('t_aegon_profile_member',$where)->row_array();
		}
		else{
			return 	$this->db->get_where('t_aegon_profile_member',$where)->result_array();
		}
	}
	
	function all_not_member_newsletter_subscriber($where,$is_single_row=0){
		//$where['is_active'] = 1;
		$this->db->select('*');
		if($is_single_row==1){
			return 	$this->db->get_where('newsletter',$where)->row_array();
		}
		else{
			return 	$this->db->get_where('newsletter',$where)->result_array();
		}
	}
}
