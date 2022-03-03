<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');
class Profile extends CI_Controller {
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
		$this->data['username'] = $dt_user['username'];
		$this->data['userid'] = $dt_user['userid'];
		$this->data['grup_select']= selectlist2(array('table'=>'auth_user_grup','id'=>'id_auth_user_grup','name'=>'grup','selected'=>$dt_user['id_auth_user_grup']));
		$this->data['email'] = $dt_user['email'];
		$this->data['phone'] = $dt_user['phone'];
		render('apps/system/profile',$data,'apps');
	}
	
	function proses(){
		$this->load->helper('htmlpurifier');
		$post 	= purify($this->input->post());
		$id	= $post['id_auth_user'];
                $email = $post['email'];
                $this->form_validation->set_rules('username', 'User Name', 'required');
		$this->form_validation->set_rules('email', 'Email', 'valid_email');
		unset ($post['id_auth_user']);
		$cek_email = $this->db->get_where('auth_user', "id_auth_user != '$id' and email = '$email'")->num_rows();
		if (trim($email)!='' && $cek_email){
			echo 'err_email';
		} else if ($this->form_validation->run() == FALSE){
			echo ' '.validation_errors(' ','<br>');
		} else {
			$this->db->update('auth_user',$post, "id_auth_user = '$id'");
			detail_log();
			insert_log('Update Profil Pengguna');
			echo 'Update Success';
		}
	}

}

