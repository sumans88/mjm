<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');
class Model_menu_admin extends CI_Model {
	function __construct(){
      parent::__construct();
	}
	function GetMenuAdminByFile($file){
		 $this->db->where("file",$file);
		 $this->db->limit(1);
		 $this->db->order_by('id_ref_menu_admin','desc');
		 $query = $this->db->get("ref_menu_admin");
		 return $query;
	}
	function GetMenuAdminByGroup($group,$parent=0){
		 $this->db->where('id_parents_menu_admin',$parent);
		 $this->db->where('auth_pages.id_auth_user_grup',$group);
		 $this->db->where('r',1);
		 $this->db->order_by('ref_menu_admin.id_parents_menu_admin', 'asc');
		 $this->db->order_by('ref_menu_admin.urut', 'asc'); 
		 $this->db->join('auth_pages', 'auth_pages.id_ref_menu_admin = ref_menu_admin.id_ref_menu_admin','inner');
		 $query = $this->db->get("ref_menu_admin");
		 return $query;
	}
	
	function Cek_Has_Child($id_group,$id_admin_menu){
		$this->db->where('id_parents_menu_admin',$id_admin_menu);
		$this->db->where('auth_pages.id_auth_user_grup',$id_group);
		 $this->db->where('r',1);
		 $this->db->order_by('ref_menu_admin.id_parents_menu_admin', 'asc');
		 $this->db->order_by('ref_menu_admin.urut', 'asc'); 
		 $this->db->join('auth_pages', 'auth_pages.id_ref_menu_admin = ref_menu_admin.id_ref_menu_admin','inner');
		 $query = $this->db->get("ref_menu_admin");
		 
		 if($query->num_rows() > 0){
			return TRUE ;
		 }else{
			return FALSE ;
		 }
	}
}
?>