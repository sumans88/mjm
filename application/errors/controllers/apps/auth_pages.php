<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');
class Auth_pages extends CI_Controller {
	function index(){
		if($_GET['q']==1){
			grid($_GET,'Auth_model','auth_pages',$alias);
			exit;
		}
		render('apps/auth_pages/auth_pages',$data,'apps');
	}
	function proses(){
		$this->load->helper('htmlpurifier');
		$post 	= purify($this->input->post());
		$img		= "<img src='".base_url()."assets/images/error.gif'>";
		$grup 	= $post['grup'];
		$idedit 	= $post['idedit'];
		unset($post['idedit']);
		if($grup==''){
			$err = "$img Group Name Is Required!<br>";
		}
		$cek = $this->db->get_where('auth_user_grup',"grup = '$grup' and  id_auth_user_grup not in ('$idedit')")->row_array();
		if($cek['grup']!=''){
			$err .= "$img Group Name Already Exist!";
			echo 'duplicate';
			exit;
		}
		if($err != ''){
			echo 'error'.$err;
		}
		else{
			if($idedit){
				auth_update();
				$this->db->update('auth_user_grup',$post,"id_auth_user_grup = '$idedit'");
				$message = 'Update Success';
			}
			else{
				auth_insert();
				$this->db->insert('auth_user_grup',$post);
				$message = 'Input Success';
			}
			echo $message;
		}
		
		//print_r($post);
	}
	function delete(){
		auth_delete();
		$post		= $this->input->post();
		$iddel	= $post['iddel'];
		$cek 		= count($this->db->get_where('auth_user',"id_auth_user_grup ='$iddel'")->result_array());
		if($cek > 0){
			$company = $this->db->get_where('auth_user_grup',"id_auth_user_grup = '$iddel'")->row()->grup;
			echo "Masih Ada $cek User di Grup $company";
		}
		else{
			$this->db->delete('auth_user_grup',"id_auth_user_grup = '$iddel'");
			echo "Delete Success";
		}
		
		
	}
	function delete_selected(){
		auth_delete();
		$post		= $this->input->post();
		$iddel	= explode(',',$post['iddel']);
		foreach($iddel as $id){
			$cek 		= count($this->db->get_where('customer_account',"id_ref_company ='$id'")->result_array());
			$company = $this->db->get_where('ref_company',"id_ref_company = '$id'")->row()->company;
			if($cek > 0){
				$msg .= "Company $company gagal Dihapus karena masih memiliki $cek Account \n";
			}
			else{
				$this->db->delete('ref_company',"id_ref_company = '$id'");
				$msg .= "Company $company Berhasil dihapus \n";
			}
		}
			echo $msg;
	}
	function auth_pages_edit(){
		$id 	= $this->input->post('id');
		$menu = $this->db->get_where('ref_menu_admin','id_parents_menu_admin = 0')->result_array();
		$n 	= 0;
		foreach($menu as $mn){
			$id_menu													= $mn['id_ref_menu_admin'];
			$this->data['list_menu'][$n]['id'] 				= $id_menu;
			$this->data['list_menu'][$n]['nama_menu'] 	= $mn['menu'];
			$this->checked($id_menu,$id,$n);
			$sub1 	= $this->db->get_where('ref_menu_admin',"id_parents_menu_admin = $id_menu")->result_array();
			foreach($sub1 as $s1){
				$n++;
				$id_sub1		= $s1['id_ref_menu_admin'];
				$this->data['list_menu'][$n]['id'] 				= $id_sub1;
				$this->data['list_menu'][$n]['nama_menu'] 	= ' &nbsp;&nbsp;&nbsp; &raquo; '.$s1['menu'];
				$this->checked($id_sub1,$id,$n);
				
				$sub2 = $this->db->get_where('ref_menu_admin',"id_parents_menu_admin = $id_sub1")->result_array();
				foreach($sub2 as $s2){
					$n++;
					$id_sub2													= $s2['id_ref_menu_admin'];
					$this->data['list_menu'][$n]['id'] 				= $id_sub2;
					$this->data['list_menu'][$n]['nama_menu'] 	= ' &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp; &raquo; '.$s2['menu'];
					$this->checked($id_sub2,$id,$n);
				}
			}
			$n++;
		}
		$this->data['id_grup'] = $id;
		$this->parser->parse('apps/auth_pages/auth_pages_edit.html', $this->data);
	}
	function update_auth(){
		$this->db->trans_start();   
		$this->load->helper('htmlpurifier');
		$post 				= purify($this->input->post());
		$id_grup 			= $post['id_grup'];
		// print_r($post['menu']);s
		foreach($post['menu'] as $menu){
			// echo $menu;
			$data ['c']		= ($post['insert'][$menu] != 1) ? 0 : 1;
			$data ['r']		= ($post['view']	[$menu] != 1) ? 0 : 1;
			$data ['u']		= ($post['update'][$menu] != 1) ? 0 : 1;
			$data ['d']		= ($post['delete'][$menu] != 1) ? 0 : 1;
			// if($this->db->affected_rows() == 0){
			$cek = $this->db->get_where('auth_pages',"id_ref_menu_admin = $menu and id_auth_user_grup = $id_grup");
			if($cek->num_rows() < 1){
			// 	//echo $this->db->affected_rows();
				$data['id_auth_user_grup'] = $id_grup;
				$data['id_ref_menu_admin'] = $menu;
				$this->db->insert('auth_pages',$data);
				unset($data['id_auth_user_grup']);
				unset($data['id_ref_menu_admin']);
			}
			else{
				$this->db->update('auth_pages',$data,"id_ref_menu_admin = $menu and id_auth_user_grup = $id_grup");
			}
		}
		$this->db->trans_complete();
		echo 'Update Success';
	}
	function checked($id_menu,$id_grup,$n){
			$status 	= $this->db->get_where('auth_pages',"id_ref_menu_admin = $id_menu and id_auth_user_grup = $id_grup")->row_array();
			$this->data['list_menu'][$n]['cchecked'] 		= ($status['c'] == 1) ? 'checked':'';
			$this->data['list_menu'][$n]['rchecked'] 		= ($status['r'] == 1) ? 'checked':'';
			$this->data['list_menu'][$n]['uchecked'] 		= ($status['u'] == 1) ? 'checked':'';
			$this->data['list_menu'][$n]['dchecked'] 		= ($status['d'] == 1) ? 'checked':'';

	}
}
