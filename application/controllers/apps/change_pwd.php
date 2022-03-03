<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');
class Change_pwd extends CI_Controller {
	function __construct(){
		parent::__construct();
		$this->layout = 'none';
		
	}
	function index(){
		$this->load->model('Model_user');
		$this->data['disabled'] = 'disabled';
		$user = $this->data['id_auth_user'];
		$dt_user = $this->db->get_where('auth_user', "id_auth_user = '$user'")->row_array();
		$this->data['userid'] = $dt_user['userid'];
		render('apps/change_pwd/change_pwd',$data,'apps');
	}
	
	function proses(){
		$this->load->helper('htmlpurifier');
		$post 	= purify($this->input->post());
		$userid		= $post['userid'];
		$pass1	= $post['new_userpass'];
		$pass2  = $post['re_userpass'];
		$old_pass1 = md5($post['old_userpass']);
		unset ($post['new_userpass']);
		unset ($post['re_userpass']);
		unset ($post['old_userpass']);
		unset ($post['idedit']);
		unset ($post['userid']);
		unset ($post['pass']);
		$cek = $this->db->get_where('auth_user', "userid = '$userid'")->row_array();
		$cek_pass = $cek['userpass'];
		if ($cek_pass != $old_pass1){
			echo 'notsame';
		}
		elseif ($pass1 != $pass2) {
			echo "beda";
		} 
		else {
			$post['userpass'] = md5($pass1);
			//$post['first_login'] = 1;
			$this->db->update('auth_user',$post, "userid = '$userid'");
			echo 'Update Success';
			detail_log();
			insert_log('Update Password');
		}
	}

}

