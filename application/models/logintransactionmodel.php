<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class loginTransactionModel extends CI_Model
{
	var $table = 'login_transaction';
	function __construct(){
		parent::__construct();
	}

	function check_user($user_sess){
		$where =  array(
			'userid'=>$user_sess['admin_id_auth_user'],
			'ip_address'=>$_SERVER['REMOTE_ADDR'],
			'user_agent'=>$_SERVER['HTTP_USER_AGENT'],
			'is_active'=>1);
		$query = $this->db->get_where($this->table,$where);
		if($query->num_rows()!=0) {
			$row = $query->row_array();
			$long_session = date('Y-m-d H:i:s',strtotime('+'.LONG_SESSION.' minutes',strtotime($row['last_activity'])));
			$now = date('Y-m-d H:i:s');
			if($now < $long_session) {
                return set_flash_session('error_login','Anda telah login di Komputer/Device lain.');
			} else if($now > $long_session) {
				$data = array(
					'is_active'=>2,
					'lock_date'=>date('Y-m-d H:i:s'));
				$this->update($user_sess['admin_id_auth_user'],$data,array('ip_address' => $_SERVER['REMOTE_ADDR']));
				$this->insert($where);
			}
		} else {
			$this->insert($where);
		}
	}
	function insert($insert){
		$insert['create_date'] = date('Y-m-d H:i:s');
		$insert['last_activity'] = date('Y-m-d H:i:s');
		$this->db->insert($this->table,$insert);
		return $this->db->insert_id();
	}

	function update($userid,$data,$where){
		$where['userid'] = $userid;
		$where['is_active'] = 1;
		$data['last_activity'] = date('Y-m-d H:i:s');
		$this->db->update($this->table,$data,$where);
	}

	function singleLogin($user_sess){
		$where =  array(
			'userid'=>$user_sess['admin_id_auth_user'],
			'is_active'=>1);
		$query = $this->db->get_where($this->table,$where);
		if($query->num_rows()>1){
			$where =  array(
			'userid'=>$user_sess['admin_id_auth_user'],
			'user_agent !='=>$_SERVER['HTTP_USER_AGENT'],
			'is_active'=>1);
			$this->update($user_sess['admin_id_auth_user'],array('is_active'=>2,'lock_date'=>date('Y-m-d H:i:s')),$where);
		} else {
			$this->update($user_sess['admin_id_auth_user']);
		}
	}

	function check_is_single($user_sess){
		$where =  array(
			'userid'=>$user_sess['admin_id_auth_user'],
			'ip_address'=>$_SERVER['REMOTE_ADDR'],
			'user_agent'=>$_SERVER['HTTP_USER_AGENT'],
			'is_active'=>1);
		$query = $this->db->get_where($this->table,$where)->num_rows();
		if(!$query){
			$this->logout();
		}
	}

	function logout(){
		$data['ip'] 		    = $_SERVER['REMOTE_ADDR'];
    	$data['activity']       = "Logout";
    	$data['id_auth_user']   = $this->data['id_auth_user'];
    	$data['log_date'] =  date('Y-m-d H:i:s');
        $this->db->insert('access_log',$data);
        $this->session->sess_destroy();
        $this->load->model('LoginTransactionModel');
        $this->LoginTransactionModel->update($data['id_auth_user'],array('lock_date'=>$data['log_date'],'is_active'=>2),array('ip_address'=>$data['ip']));
        redirect('apps/login');
	}
}