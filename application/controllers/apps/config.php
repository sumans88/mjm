<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');
class Config extends CI_Controller {
	function index(){
		$data 						= $this->db->get('ref_email_config')->row_array();
		$data['ssl_checked'] 	= ($data['ssl']=='y') ? 'checked': '';
		$data['type_checked'] 	= ($data['type']=='SMTP') ? 'checked': '';
		$this->data					= array_merge($data,$this->data);
		$this->data['content']	= $this->parser->parse('apps/config.html', $this->data,true);
		$this->parser->parse('layout/apps.html', $this->data);
		//render("apps/guru_piket/siswa_terlambat",$data,'apps');
	}
	
	function proses(){
		$this->load->helper('htmlpurifier');
		$post 	= purify($this->input->post());
		$type		= $post['type'];
		$chpwd	= $post['chpwd'];
		unset($post['chpwd']);
		if($type =='SMTP'){
			if($chpwd == ''){
				unset($post['password']);
			}
			$this->db->update('ref_email_config',$post);
		}
		else{
			$this->db->update('ref_email_config',array('type'=>$type));
			//echo 'update typenya aja';
		}
		echo $this->db->last_query();
	}
}
