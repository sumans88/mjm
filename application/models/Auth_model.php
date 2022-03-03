<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

/*************************************
  * Created : Sept 27 2011
  * Creator : ivan lubis
  * Email : ihate.haters@yahoo.com
  * Content : Login
  * Project : 
  * CMS ver : CI ver.2
*************************************/	
class Auth_model extends CI_Model
{
	function __construct(){
		parent::__construct();
	}
	function check_login($userid,$password){
		//data untuk mendapatkan IP user
		$data['ip'] 		= $_SERVER['REMOTE_ADDR'];
		//controller apps/login
		$redir 				= 'apps/login';

		//cek username dan password yang di inputkan
		if ($userid !='' && $password!=''){
		 //query seleksi userid/username pada db auth_user
		 /*$query = $this->db->get_where('auth_user',"userid = '$userid'");*/
		 $query = $this->db->get_where('auth_user',array('userid'=>$userid));
			//$this->db->where("userid","$userid");
			//$query=$this->db->get("auth_user");
		 	//jika data lebih dari 0 (ada)
			if ($query->num_rows() > 0){
				//menampilkan hasil query dalam 1 baris
				$row = $query->row(); 
				//menampung data id_auth_user dari db yang terquery
				$data['id_auth_user'] 	= $row->id_auth_user;
				//variabel userpass dari db yang terquery 
				$userpass = $row->userpass;
				//menenkripsi inputan password
				$password = md5($password);
				//cek apakah password yang di enkripsi sama dgn $userpass
				if ($password == $userpass && $password != "") {
					//load library session
					$this->load->library("session");
										//menampung data array yang terquery pada db
					$user_sess = array();
					$user_sess = array(
					'admin_name'=>$row->username,
					'admin_id_auth_user_group'=>$row->id_auth_user_grup,
					'id'=>$row->id_auth_user,
					'admin_id_auth_user'=>$row->id_auth_user,
					'admin_id_ref'=>$row->id_ref,
					'admin_type'=>$row->tipe,
					'profil_mitra_id'=>$row->profil_mitra_id
					);
					$_SESSION['adminLogin'] = true;
                    //set data $user_sess
                    
					$this->session->set_userdata('ADM_SESS',$user_sess);
					//unset data $user_sess
					$this->session->unset_userdata('MEM_SESS');
					//set pesan ke field activity
					$data['activity'] 		= "Login";
					//redirect ke controller apps/home
					
					$redir ='apps/home';
				}
				//jika password yang di enkripsi tdk sama dgn $userpass
				else {
					//set notif password salah
          $this->session->set_flashdata('error_login','Incorrect password');
                    //set pesan ke field activity
					$data['activity'] = "Incorrect password";
				}
			}
			//jika data tdk ada 
			else {
			   //set notif username dan password yg diinputkan salah
			   $this->session->set_flashdata('error_login','Username atau password yang anda masukkan salah');
			   //set pesan ke field activity
			   $data['activity'] = "User not found : $userid";
			}
		}
		//cek kalau username dan password kosong
		else{
			//kalo userid or password or dua2nya kosong
			$this->session->set_flashdata('error_login','Username and Password is Required');
			redirect('apps/login');
			exit;
		}
		//menampung data date
		$data['log_date'] =  date('Y-m-d H:i:s');
		//insert ke tbl acces_log
		$this->db->insert('access_log',$data);
		//redirect ke controller apps/login karena gagal login
		redirect($redir);
	}

	
	function auth_pages($where,$total='',$sidx='',$sord='',$mulai='',$end=''){
	  $mulai = $mulai -1;
	  if ($total==1){
		  $sql	= "SELECT count(*) ttl from auth_user_grup ";//where 1 $where";
		  $data	= $this->db->query($sql)->row()->ttl;
	  }
	  else{
		  $sql	= "SELECT id_auth_user_grup id, grup from auth_user_grup ";//where 1
						 // $where order by $sidx $sord limit $mulai,$end ";
		  $dt	= $this->db->query($sql)->result_array();
		  $n 	= 0;
		  foreach($dt as $dtx){
			  $data[$n]['id'] 				= $dtx['id'];
			  $data[$n]['edit'] 			=  edit_grid($dtx['id']);
			  $data[$n]['del'] 				= ($dtx['id'] <= 10) ? '' : delete_grid($dtx['id']);
			  $data[$n]['grup'] 			= $dtx['grup'];
			  $data[$n]['total'] 			= $this->db->get_where('auth_user',array('id_auth_user_grup'=>$dtx['id']))->num_rows();
			  ++$n;
		  }
	  }
	  return $data;
	}


}

