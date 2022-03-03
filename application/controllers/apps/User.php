<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');
class User extends CI_Controller {
	function __construct(){
		parent::__construct();
		$this->layout = 'none';
		$this->load->model('model_user','model');
	}
	function index(){
		$this->load->model('Model_user');
		if($_GET['q']==1){
			$alias['mitra'] = 'c.name';
			grid($_GET,'Model_user','user',$alias);
			exit;
		}
		$data['grup_select']		= selectlist('auth_user_grup','id_auth_user_grup','grup');
		render('apps/system/user',$data,'apps');
	}
	function proses(){
		$post 					= $this->input->post();
		$idedit					= $this->uri->segment(4);

		$grp = $post['id_auth_user_grup'];
		unset($post['idedit']);
		if (empty($post['userpass'])) {
			unset($post['userpass']) ;
		}
		else {
			$post['userpass'] = md5($post['userpass']);
		}
		$this->form_validation->set_rules('userid', 'Userid', 'required');
		$this->form_validation->set_rules('username', 'User Name', 'required');
		$this->form_validation->set_rules('id_auth_user_grup', 'Group', 'required');
		$this->form_validation->set_rules('email', 'Email', 'required|valid_email');
		if ($this->form_validation->run() == FALSE){
			echo ' '.validation_errors(' ','<br>');
		}
		else{
			$this->db->trans_start(); 
			$where 				= ($idedit) ? "and id_auth_user not in ($idedit)" : '';
			$cek_code 			= db_get_one('auth_user',"userid","userid = '$post[userid]' and is_delete = 0 $where");
			if($cek_code){
				echo  " Account Name $post[account_name] already exsist";
				exit;
			}
			if($idedit){
				$act	= "Update User";
				$this->model->update($post,$idedit);
				$msg = '1Update Success';
			}
			else{
				$act	= "Insert User"; 
				$this->model->insert($post); 
				$msg = '1Insert Success';
			}
			detail_log();
			insert_log($act);
			$this->db->trans_complete();
			echo $msg;
		}
	}
	function delete(){
		auth_delete();
		$post		= $this->input->post();
		$iddel	= $post['iddel'];
		if ($iddel == 1){
			echo 'User Administrator Tidak Dapat Dihapus';
		}
		else{
			$id = $this->input->post('iddel');
			$this->model->delete($iddel);
			insert_log("Delete User");
			echo "Delete Success";
		}
	}

	function delete_selected(){
		auth_delete();
		$post		= $this->input->post();
		$iddel	= explode(',',$post['iddel']);
		foreach($iddel as $id){
			if($id == 1){
				echo "User Administrator Tidak Dapat Dihapus! \n";
			}
			else{
				$this->db->delete('auth_user',"id_auth_user = '$id'");
			}
		}
	}
}
