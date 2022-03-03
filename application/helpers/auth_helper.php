<?php if (!defined('BASEPATH')) exit('No direct script access allowed');
/**
 * @file
 * Fungsi-fungsi yg meng-handle auth
 * @author Agung Iskandar <agung.iskandar@gmail.com>
 */


/**
 * Cek Auth menu (controller) per group
 * untuk mengecek user login apakah boleh mengakses menu/controller tertentu
 *
 */
function auth_menu(){
	 $CI = & get_instance();
	 $CI->load->model('Model_menu_admin');
	 $CI->load->model('Authgroup_model');
	 $CI->load->library('session');
	 $CI->data['header'] = '';
	 $apps 						= $CI->uri->segment(1);
	 $ref_menu 							= $CI->uri->segment(2);
	 $logout	 							= $CI->uri->segment(3);
	 //utk export pdf
	 $exp		 							= $CI->uri->segment(3);
	 $token								=get('token');
	 if ( $token == md5(date('dmy') . '1qazxsw2') ) return true;
	 //end utk pdf//
	 if($apps=='apps'){
		  $user_sess 						= $CI->session->userdata('ADM_SESS');
		  $group 							= $user_sess['admin_id_auth_user_group'];
		  $id_user 							= $user_sess['admin_id_auth_user'];
		  if ($user_sess){ //kalo ada session nya..
				$id_ref_menu			 	= NULL;
				//$query 						= $CI->Model_menu_admin->GetMenuAdminByFile($ref_menu);
	 				$query					= $CI->db->get_where('ref_menu_admin',"controller like '$ref_menu/%'");
				if($query->num_rows()== 0){
	 				$query					= $CI->db->get_where('ref_menu_admin',"controller = '$ref_menu'");
				}
				if($query->num_rows() > 0){
					 //echo 'aaa';
					 $row 					= $query->row_array();
					 $id_ref_menu_admin 	= $row['id_ref_menu_admin'];
					 $cek		 				= $CI->Authgroup_model->GetMenuByRef($id_ref_menu_admin,$group)->row_array();
					 //	print_r($cek);exit;
					 if($cek['r'] != 1 && $ref_menu !='home'){ // Akses Read Harus 1
						  #redirect("apps/home/forbiden");
						  die('Forbiden');
					 }
					 //button
					 #print_r($cek);
					 $CI->data['auth_insert'] = $cek['c'];
					 $CI->data['auth_update'] = $cek['u'];
					 $CI->data['auth_delete'] = $cek['d'];
					 // $is_mitra = db_get_one('auth_user','is_mitra',array('id_auth_user'=>id_user()));
					 // $CI->data['is_mitra']		= $is_mitra;
				}
				else if($ref_menu == 'login' || $ref_menu == ''){ //kalo ada session,tapi dihalaman login (controller login), redirect ke halaman home
					 if( $logout !='logout'){ //asal fungsinya bukan logout...
						  redirect("apps/home");
					 }
				}
				else{
					 if ($ref_menu != 'home') {//kalo kontrolernya blm di daftarin di db..
						  #redirect("apps/home/forbiden");
  						  die('Forbiden');
					 }
				}
		  }
		  else if($ref_menu !='login'){//kalo ga ada sessionya...
				redirect("apps/login");
		  }
	 }
}
/**
 * Dapetin auth akses utk crud
 * @param $type c=create/insert,r=read/view,u=update,d=delete
 * @return boolean
 */
function auth_access($type){
	$CI 			= & get_instance();
	$file 		= $CI->uri->segment(2);
	$user_sess 	= $CI->session->userdata('ADM_SESS');
	$id_group 	= $user_sess['admin_id_auth_user_group'];
	$auth 		= $CI->db->select('c,r,u,d')->get_where('auth_pages a, ref_menu_admin b',"a.id_ref_menu_admin = b.id_ref_menu_admin and controller like '$file/%' and id_auth_user_grup = '$id_group'")->row_array();
	if(count($auth) == 0){
	 	$auth 		= $CI->db->select('c,r,u,d')->get_where('auth_pages a, ref_menu_admin b',"a.id_ref_menu_admin = b.id_ref_menu_admin and controller = '$file' and id_auth_user_grup = '$id_group'")->row_array();
	}
	return $auth[$type];
}
/**
 * Cek proses Insert
 * @return exit script if not authorize
 */
function auth_insert(){
	if(auth_access('c') != 1) die('Forbiden');
}
/**
 * Cek proses Update
 * @return exit script if not authorize
 */
function auth_update(){
	if(auth_access('u') != 1) die('Forbiden');
}
/**
 * Cek proses Delete
 * @return exit script if not authorize
 */
function auth_delete(){
	if(auth_access('d') != 1) die('Forbiden');
}

